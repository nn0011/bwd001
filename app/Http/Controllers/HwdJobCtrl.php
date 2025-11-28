<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

use App\HwdJob;
use App\AccountMetas;
use App\Accounts;
use App\Zones;
use App\BillingMeta;
use App\Reading;
use App\ExportBilling;
use App\BillingMdl;
use App\HwdLedger;
use App\PreReading;
use App\Arrear;
use App\LedgerData;
use App\Collection;
use App\OverdueStat;
use App\BillingDue;
use App\Exp1;
use App\Exp3;
use App\Exp4;
use App\BillingAdjMdl;
use App\PayPenLed;




use App\Http\Controllers\ServiceCtrl;


class HwdJobCtrl extends Controller
{

	function fix_z7_z8_penalty0002()
	{
		$sql1 = "
		SELECT * FROM `ledger_datas`
		where acct_id IN (
		 SELECT cust_id FROM `collections`
		 where payment_date like '2021-02-26%' and created_at like '2021-03%' and status='active'
		)
		AND led_type='penalty' AND period like '2021-02%'	
		AND status='active'	
		";

		$resu1 = DB::select($sql1,[]);
		if(count($resu1) == 0){echo "DONE";die();}

		$led001 = new LedgerCtrl;

		foreach($resu1 as $m)
		{
			$up_sql1 = "UPDATE ledger_datas SET status='deleted' WHERE id=$m->id LIMIT 1 ";
			DB::select($up_sql1, []);
			
			$led001->view_ledger_account_info_fix_led($m->acct_id);
		}

		echo 'Continue..';

		// echo '<pre>';
		// print_r($resu1);


	}

	function fix_z7_z8_penalty0001()
	{
		$sql1 = "
		SELECT * FROM 
		billing_dues
		WHERE id IN 
		(SELECT id from billing_dues 
		where acct_id IN (
		 SELECT cust_id FROM `collections`
		where payment_date like '2021-02-26%' and created_at like '2021-03%' and status='active'
		)
		AND period like '2021-02%')
		AND due_stat='active'	
		limit 1000	
		";

		$resu1 = DB::select($sql1,[]);

		if(count($resu1) == 0){echo "DONE";die();}
		foreach($resu1 as $m)
		{
			$up_sql1 = "UPDATE billing_dues SET due_stat='deleted' WHERE id=$m->id LIMIT 1 ";
			DB::select($up_sql1, []);
		}

		echo 'CONTINUE...';
		// echo '<pre>';
		// print_r($resu1);
	}



	function test00020()
	{
		$sql1 = "
SELECT * FROM   ledger_datas 
WHERE id IN (
	SELECT MAX(id) iid FROM `ledger_datas` 
	where led_type IN ('payment', 'payment_cancel', 'payment_cr', 'cancel_cr')
	AND acct_id=1375
	GROUP BY reff_no
)
OR id IN(
	SELECT id FROM `ledger_datas` 
	where led_type IN ('beginning', 'billing', 'penalty')
	AND acct_id=1375
)
OR id IN (
	SELECT id FROM `ledger_datas` 
	where led_type IN ('adjustment')
	AND acct_id=1375 AND bill_adj > 0
)

order by zort1 asc, id asc

		";
		
		$resu1 = DB::select($sql1,[]);
		
        $arr1 = [];
        $x=0;
        foreach($resu1 as $mm)
        {
            $arr1[$x] = ['credit'=>0, 'debit'=>0, 'parti'=> $mm->led_type, 'date'=>$mm->date01];

            if($mm->led_type == 'beginning'){$arr1[$x]['debit'] = $mm->arrear;}
            if($mm->led_type == 'penalty'){$arr1[$x]['debit']   = $mm->penalty;}
            if($mm->led_type == 'billing'){$arr1[$x]['debit']   = $mm->billing - $mm->discount;}
			
            if($mm->led_type == 'payment'){$arr1[$x]['credit'] 		 = $mm->payment;}
            if($mm->led_type == 'payment_cr'){$arr1[$x]['credit']    = $mm->payment;}
            if($mm->led_type == 'adjustment'){$arr1[$x]['credit']    = $mm->bill_adj;}
			
			$x++;
        }
		
		
		$ttl_bal = 0;
        foreach($arr1 as $a1){
			$ttl_bal += $a1['debit'];
			$ttl_bal -= $a1['credit'];
            echo $a1['parti'];
            echo  ' | ';
            echo $a1['debit'];
            echo  ' | ';
            echo $a1['credit'];
            echo  ' | ';
			echo $ttl_bal;
            echo '<br />';
        }		

        echo '<pre>';
        print_r($arr1);		
		
		//echo '<pre>';
		//print_r($resu1);
		
	}//
	
	function fix_penalty_z6789_feb_2021()
	{
		$zone  = 6;
		$duedd = '2021-02-23';

		$sql1 = "
			SELECT id, acct_id,acct_num, led_type,period, ttl_bal, (SELECT (billing-discount) D1 FROM ledger_datas WHERE led_type='billing' AND period='2021-02-01' and acct_id=LD1.acct_id and status='active') my_bill,
			(SELECT id from billing_mdls B1 WHERE B1.account_id=acct_id and period='2021-02-01' limit 1) bill_id
			FROM ledger_datas LD1 WHERE id IN (SELECT MAX(id) FROM `ledger_datas` 
			WHERE acct_id IN (select id from accounts where zone_id=$zone AND acct_type_key IN (7,8,9,14) AND pen_exempt = 0) 
			AND date01 <= '$duedd' 
			GROUP BY acct_id)
			AND date01 like '2021-02%' AND ttl_bal > 0
			ORDER BY ttl_bal desc	
		";
		$resu1 = DB::select($sql1);

		//LedgerData
		//BillingDue
		//BillingMdl

		$due_date = $duedd;

		foreach($resu1 as $mm)
		{
			extract((array) $mm);

			if($ttl_bal < $my_bill){
				$pen = $ttl_bal * 0.1;
			}else{
				$pen = $my_bill * 0.1;
			}

			$pen = round($pen,2);

			$bill_001 = BillingMdl::where('id', $bill_id)->whereNull('due_stat')->first();
			if(!$bill_001){continue;}

			$due1 = new BillingDue;
			$due1->bill_id = $bill_id;
			$due1->due_amount = $pen;
			$due1->due_date = $due_date;
			$due1->due_stat = 'active';
			$due1->bill_balance = $my_bill;
			$due1->due1 = $pen;
			$due1->acct_id = $acct_id;
			$due1->acct_no = $acct_num;
			$due1->period = '2021-02-01';
			$due1->save();


			$led002 	= LedgerData::where('acct_id', $acct_id)->where('status','active')
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						->orderBy('id', 'desc')
						->first();

			$new_ttl = $led002->ttl_bal + $pen;

			$led_data = new LedgerData;
			$led_data->acct_id = $acct_id;
			$led_data->penalty = $pen;
			$led_data->ttl_bal = $new_ttl;
			$led_data->ledger_info = 'Penalty - Feb 2021';
			$led_data->date01 = '2021-03-01';
			$led_data->period = '2021-02-01';
			$led_data->status = 'active';
			$led_data->led_type = 'penalty';
			$led_data->acct_num = $acct_num;
			$led_data->reff_no = $due1->id;
			$led_data->save();

			$bill_001->due_stat = 'has-due';
			$bill_001->save();
		}


		// echo '<pre>';
		// print_r($resu1);


	}

	function tidman_mawis_fix1002()
	{
		$sql2 = "
			SELECT * FROM (SELECT id, acct_num, acct_id,payment, ttl_bal, (SELECT id FROM ledger_datas where reff_no= LD1.reff_no AND led_type ='or_nw_debit') DDD FROM ledger_datas LD1 WHERE acct_id IN (SELECT id FROM accounts ) 
			AND status='active' AND payment > 1  and bill_adj is  null  AND ttl_bal > 0 AND (led_type = 'or_nw' OR led_type = 'cr_nw')
			ORDER BY `acct_num` ASC) XXX where DDD is null
			LIMIT 10
		";
		
		$resu1 = DB::select($sql2);
		
		// echo '<pre>';
		// print_r($resu1);
		// die();
		
		$led001 = new LedgerCtrl;
		if(empty($resu1)){echo 'DONE';die();}
		foreach($resu1 as $r1)
		{
			$led_data = LedgerData::find($r1->id);
			$led_data->bill_adj = ($r1->payment * -1);
			$led_data->save();	
			$led001->view_ledger_account_info_fix_led($r1->acct_id);

		}

		echo 'continue;';

	}

	function tidman_mawis_fix1001()
	{
		 die();
		
		$sql1 = "
			SELECT id,acct_num,acct_id, ttl_bal FROM ledger_datas where id IN (SELECT MIN(id) FROM ledger_datas WHERE acct_id IN (SELECT id FROM accounts WHERE zone_id=7) AND status='active'
			AND ttl_bal < 0 GROUP BY acct_id)  
			ORDER BY `ledger_datas`.`ttl_bal` ASC
		";

		$resu1 = DB::select($sql1);

		// echo '<pre>';
		// print_r($resu1);
		// die();

		$led001 = new LedgerCtrl;

		foreach($resu1 as $r1)
		{
			$led_data = LedgerData::where('id', '<', $r1->id)
						->where('acct_id', $r1->acct_id)
						->where('led_type','billing')
							->where('status', 'active')
							->orderBy('id', 'desc')
								->first();

								
			// echo '<pre>';
			// echo $r1->acct_num; 
			// print_r($led_data->toArray());

			$led_data->bill_adj = $r1->ttl_bal;
			$led_data->save();
			$led001->view_ledger_account_info_fix_led($r1->acct_id);

		}//



	}//

	function execute_refresh_ledger_by_zone()
	{
		$zone_id = 10;
		// die();
	
		$files001 = 'temp002/ids005_'.$zone_id.'.json';
		$ids001   = json_decode(@file_get_contents($files001));
	
		if(empty($ids001)){
			$ids001 = array();
		}
	
		$acct_all = Accounts::where('zone_id', $zone_id)
							->whereNotIn('id', $ids001)
								->limit(10)
									->get();
	
		$led001 = new LedgerCtrl;
	
		foreach($acct_all as $aa){
			$ids001[] = $aa->id;
			$led001->view_ledger_account_info_fix_led($aa->id);
			$this->remove_pen_ledger_by_acct_id($aa->id);
			$this->collection_ledger_update_dec272020($aa->id);				
		}//
	
		$fp = fopen($files001, 'w');
		fwrite($fp, json_encode($ids001));
		fclose($fp);
	
		echo count($ids001).' ---- '.$files001;
	}



	function remove_pen_ledger_by_acct_id($acct_id)
	{
		PayPenLed::where('uid', $acct_id)->delete();
	}
		

	function mmmm001()
	{

		$acct_id = 2974;
		$acct_id = 478;
		$acct_id = 4266;
		$acct_id = 2415;
		$acct_id = 60;
		$acct_id = 594;
		
		$curr_or = 4612;

		$mm = feb_05_2021_daily_col_break($acct_id, 191, '2020-12-21');
		echo '<pre>';
		print_r($mm);
		die();

	}

	function collection_ledger_update_dec272020($cid)// CID = customer id
	{
		// $this->mmmm001();

		$payable_ledgers  = LedgerData::where('status', 'active')
								->where('acct_id', $cid)
									->orderBy('date01', 'asc')
									->orderBy('zort1', 'asc')
										->orderBy('id', 'asc')
											->get();

		$debit_arr  = ['adjustment', 'beginning', 'billing', 'penalty'];
		$credit_arr = ['payment', 'wtax'];		
		$non_wat    = ['cancel_cr_nw','cr_nw','cr_nw_debit', 'nw_cancel','or_nw', 'or_nw_debit'];	

		$debt_info = [];//amt, typ, reff

		foreach($payable_ledgers as $mm)
		{
			if(in_array($mm->led_type, $non_wat)){continue;}
			if($mm->led_type == 'beginning'){$debt_info[] = [$mm->arrear,$mm->led_type, $mm->id];}
			if($mm->led_type == 'billing'){$debt_info[] = [($mm->billing - $mm->discount),$mm->led_type, $mm->reff_no];}
			if($mm->led_type == 'penalty'){$debt_info[] = [($mm->penalty),$mm->led_type, $mm->reff_no];}

			if($mm->led_type == 'payment'){

				$payment = $mm->payment;	

				foreach($debt_info as $kk => $dd1){
					$ttl_pa1 = 0;
					$dd1['p'] = (array) @$dd1['p'];
					
					foreach(@$dd1['p'] as $oo){
						$ttl_pa1 += $oo[0];
					}

					$payment+=$ttl_pa1;

					$diff1 = $dd1[0] - $payment;
					$ttl_cr = 0;
					if($diff1 <= 0){$ttl_cr = $dd1[0];}
					if($diff1 >  0){$ttl_cr = $payment;}
					$payment -= $ttl_cr;
					$dd1['p'][] = array($ttl_cr-$ttl_pa1, $mm->reff_no);
					$debt_info[$kk] = $dd1;
					if($payment <= 0){break;}
				}

			}

		}

		// echo '<pre>';
		// print_r($debt_info);
		// print_r($payable_ledgers->toArray());

	}

	function collection_ledger_update_dec272020__qqq($cid)// CID = customer id
	{
		$sql1 = "
			SELECT id, cust_id, payment,tax_val, payment_date, created_at FROM collections 
			WHERE 
				id IN (SELECT max(id) mx_id FROM `collections` group by invoice_num)
				AND status  IN('active', 'collector_receipt')  
				AND  NOT EXISTS(SELECT id FROM `pay_pen_leds` WHERE pay_pen_leds.cid=collections.id)
				AND cust_id=?
				ORDER BY id ASC
		";

		// LIMIT 100
		$rs1 = DB::select($sql1,[$cid]);
		
		// echo '<pre>';
		// print_r($rs1);

		$led_types = array('beginning', 'billing', 'penalty', 'adjustment');
		
		foreach($rs1 as $r1)
		{
			$acct_id      = $r1->cust_id;
			$dd1          = $r1->created_at;
			$payment_made = $r1->payment + $r1->tax_val;
			$cid		  = $r1->id;
			
			$payable_ledgers  = LedgerData::whereIn('led_type', $led_types)
									->where('status', 'active')
										->where('acct_id', $acct_id)
										->where('created_at', '<', $dd1)
										// ->where('date01', '<', $dd1)
										->orderBy('zort1', 'asc')
											->orderBy('id', 'asc')
												->get();

			// echo '<pre>';
			// print_r($payable_ledgers->toArray());
			// break;

			$gg_vars   = compact('acct_id', 'payable_ledgers', 'payment_made');

			feb022021__payable_ledger101($payable_ledgers, $acct_id, $payment_made, $r1);

			// $all_payed = dec082020__execute_payled_breakdown($gg_vars);
			// echo '<pre>';
			// echo print_r($all_payed);
			// nov252020__insert_payment_breakdown($acct_id, $cid, $all_payed, $r1);

		}//

	}


	function collection_ledger_update_dec252020()
	{

		$sql1 = "
			SELECT id, cust_id, payment, tax_val, payment_date, created_at, status FROM collections WHERE 
			id IN (SELECT max(id) mx_id FROM `collections` group by invoice_num)
			AND status  IN('active', 'collector_receipt')  
			AND  NOT EXISTS(SELECT id FROM `pay_pen_leds` WHERE pay_pen_leds.cid=collections.id)
			ORDER BY id ASC
		";
		
		$rs1 = DB::select($sql1);
		
		if(empty($rs1)){
			echo 'Nothing ';
			die();
		}
		
		// echo '<pre>';
		// echo count($rs1);
		// print_r($rs1);
		// die();

		
		$led_types = array('beginning', 'billing', 'penalty', 'adjustment');
		
		foreach($rs1 as $r1)
		{
			$acct_id      = $r1->cust_id;
			$dd1          = $r1->created_at;
			$payment_made = $r1->payment + $r1->tax_val;
			$cid		  = $r1->id;
			$coll_info    = $r1;
			
			$payable_ledgers  = LedgerData::whereIn('led_type', $led_types)
									->where('status', 'active')
										->where('acct_id', $acct_id)
											// ->where('created_at', '<', $dd1)
											->where('date01', '<', $dd1)
												->orderBy('date01', 'asc')
												->orderBy('zort1', 'asc')
													->orderBy('id', 'asc')
														->get();

			$gg_vars   = compact('acct_id', 'payable_ledgers', 'payment_made', 'coll_info');
			$all_payed = dec082020__execute_payled_breakdown($gg_vars);
			
			// echo '<pre>';
			// print_r($all_payed);
			// die();

			nov252020__insert_payment_breakdown($acct_id, $cid, $all_payed, $r1);		
		   
		}//
		
		
		echo 'Continue;';

	}//	

		function update_zorting101($acct_id)
		{
			$led1 = LedgerData::where('acct_id',$acct_id)
							->orderBy('date01', 'asc')
							->orderBy('zort1', 'ASC')
								->orderBy('id', 'ASC')
									->get();
			
			$led = (int) @$led1->count();
			$led = ($led * 10);
			
			foreach($led1 as $l)
			{
				$l->zort1 =  ($led * -1);
				$l->save();
				$led -= 10;
			}
			
			echo 'Done';
			//~ echo '<pre>';
			//~ print_r($led1->toArray());
									
		
		}//
	
	
		function add_penalty_to_user_manual()
		{
			$user_id = 1613;
			$period  = '2020-06-01';

			
			 $my_bill = BillingMdl::where('account_id', $user_id)->where('period', $period)
							->where('status', 'active')
								->with('account')
								->first();
			 
			 $bill_balance = $my_bill->curr_bill - $my_bill->discount;
			 
			 $penal = round($bill_balance * 0.1, 2);
			 
			
			$my_due = BillingDue::where('acct_id',$user_id)
						->where('bill_id', $my_bill->id)
							->where('period', $period)
								->first();
			
			if($my_due){
				//Do Nothing
			}else{
				$my_due = new BillingDue;
				$my_due->bill_id = $my_bill->id;
				$my_due->due_date = $my_bill->penalty_date;
				$my_due->due1 = $penal;
				$my_due->acct_id = $my_bill->account->id;
				$my_due->acct_no = $my_bill->account->acct_no;
				$my_due->period = $period;
				$my_due->bill_balance = $bill_balance;
				$my_due->due_stat = 'active';
				$my_due->save();
				
				$my_bill->due_stat= 'has-due';
				$my_bill->save();
			}
			
			$my_led	  = LedgerData::where('led_type', 'penalty')
							->where('acct_num', $my_bill->account->acct_no)
							->where('acct_id', $my_bill->account->id)
							->where('period', $period)
							->where('reff_no', $my_due->id)
							->first();
			
			if($my_led){
				//
			}else{
				$my_led =  new LedgerData;
				$my_led->acct_id = $my_bill->account->id;
				$my_led->penalty  = $penal;
				$my_led->ledger_info = 'Penalty';
				$my_led->date01 = $my_bill->penalty_date;
				$my_led->period = $period;
				$my_led->status = 'active';
				$my_led->led_type = 'penalty';
				$my_led->acct_num = $my_bill->account->acct_no;
				$my_led->reff_no = $my_due->id;
				$my_led->glsl = PENALTY_GL_CODE;
				$my_led->save();
			}
			
			
		}//
		
		function fix_date_bill_read_ledger101($zone_id, $perxx, $read_date)
		{
			$per1 = $perxx;
			$reading_date = $read_date;
			
			$reading1 = ['reading1'=>function($q1)use($per1){
							$q1->where('period', $per1);
						}];

			$billing_41 = ['billing_41'=>function($q1)use($per1){
							$q1->where('period', $per1);
						}];
						

			$ledger_data4 = ['ledger_data4'=>function($q1)use($per1){
							$q1->where('period', $per1);
							$q1->where('led_type', 'billing');
							$q1->where('status', 'active');
						}];
						

			
			$acct11 = Accounts::where('zone_id', $zone_id)->where('status', 'active')
						->whereHas('reading1', $reading1['reading1'])
						->whereHas('billing_41', $billing_41['billing_41'])
						->whereHas('ledger_data4', $ledger_data4['ledger_data4'])
						->with($reading1)
						->with($billing_41)
						->with($ledger_data4)
						->get();
			
			$xx = 0;
			foreach($acct11  as $a1)
			{
				//Reading
				$a1->reading1->date_read = $read_date;
				$a1->reading1->curr_read_date = $read_date;
				$a1->reading1->save();
				
				//Billing
				$a1->billing_41->bill_date = $read_date;
				$a1->billing_41->save();
				
				//Ledger
				$a1->ledger_data4->date01 = $read_date;
				$a1->ledger_data4->save();
				
				$xx++;
			}
			
			echo $xx;
			
			
		}
		
		function fix_senior_feb_billing()
		{
				$peri1 = '2020-03-01';
				
				$bil1 = BillingMdl::whereHas('account', function($q1){
								$q1->where('acct_discount', '45');
							})
							->whereHas('ledger12', function($q2)use($peri1){
								$q2->where('period', $peri1);
								$q2->where('led_type', 'billing');
								$q2->where('status', 'active');
							})
							->where('period', $peri1)
							->where('status', 'active')
							->with(['ledger12'=>function($q3)use($peri1){
									$q3->where('period', $peri1);
									$q3->where('led_type', 'billing');
									$q3->where('status', 'active');
								}])
							->get();
				
				
				$bbxx = 0;
				
				foreach($bil1 as $bb)
				{
					if($bb->discount != 0)
					{
						if($bb->discount != $bb->ledger12->discount)
						{
							if($bb->ledger12->discount == 0)
							{
								$bb->ledger12->discount = $bb->discount;
								$bb->ledger12->save();
								$bbxx++;
							}
						}
					}
				}
				
				//~ echo '<pre>';
				//~ echo print_r($bil1->toArray());
				
				echo $bbxx.'<br />';
				echo 'DONE';
				
		}//
	
		// Begginning In Active Only
		function update_beginning_balance101()
		{
			$res1 = DB::select("
					SELECT *, ROUND(((E-F)+G),2) TTL FROM (
						SELECT A, SUM(E) E, SUM(F) F, SUM(G) G FROM `exp4s`
						WHERE Z is NULL
						GROUP BY A
					) TAB1
					LIMIT 50
				");
				
			foreach($res1 as $AA)
			{
				//~ Exp4::where('A', $AA->A)
					//~ ->update(['Z'=>1]);
				
				$acct1 = Accounts::where('acct_no', trim($AA->A))
							->first();
				
				if(!$acct1)
				{
					Exp4::where('A', $AA->A)
						->update(['Z'=>'acct_not_found']);
					continue;
				}
				
				
				$nld = new LedgerData;
				$nld->acct_id=$acct1->id;
				$nld->acct_num=$acct1->acct_no;
				$nld->period = '2020-01-01';
				$nld->date01 = '2020-01-22';
				$nld->status='active';
				$nld->led_type='beginning';
				$nld->arrear=$AA->TTL;
				$nld->ttl_bal=$AA->TTL;
				$nld->save();				
				

				Exp4::where('A', $AA->A)
					->update(['Z'=>'good']);
			
				
			}
			
			echo count($res1);	
			//~ echo '<pre>';
			//~ print_r($res1);
			
		}
			
	

		function update_new_customer_101()
		{
			return;
			return;
			
			$acct_11 = Exp4::doesntHave('acct1')
						->limit(50)
							->get();
						
			if(count($acct_11) == 0){
				echo 'No more';
				return;
			}
						
			foreach($acct_11 as $bb)
			{
				$this->Add_new_account($bb);
			}
			
			echo 'Processing';
			echo '<pre>';
			
			//~ print_r($acct_11->toArray());
		}
		
		
		function Add_new_account($bb)
		{
			$acct_num = trim($bb->A);
			$last_name = trim($bb->H);
			$first_name = trim($bb->I);
			$mi = trim($bb->J);
			$address = trim(@$bb->K);
			
			$mtr_num = trim($bb->B);
			$route = trim($bb->F);	
			
			$stat1 = status_arr();
			$type1 = ctype_arr();
			$zz    = zone_arr();
			
			$con_stat = @$stat1[$bb->E];		
			$con_type  = @$type1[$bb->D];
			$zone = @$zz[$bb->C];
			
		
			$acct = new Accounts;
			$acct->acct_no = $acct_num;
			$acct->fname = $first_name;
			$acct->lname = $last_name;
			$acct->mi = $mi;
			$acct->address1 = $address;
			
			$acct->zone_id = $zone;
			$acct->acct_type_key = $con_type;
			$acct->acct_status_key = $con_stat;
			
			$acct->status = 'active';
			$acct->meter_number1 = $mtr_num;
			$acct->route_id = $route;
			$acct->old_route = $route;
			
			$acct->meter_size_id = 41;//Meter Size
			
			if(@$bb->D == 8)//Senior
			{$acct->acct_discount=45;}
			
			$acct->save();
						
		}
		
		function update_senior_001()
		{
			
			return;
			return;
			return;
			return;
			$acct_11 = Exp4::whereHas('acct1', function($q1){
							$q1->whereNull('acct_discount');
						 })
						->where('D','8')
						->limit(10)
						->get();
			
			if(count($acct_11) == 0)
			{
				echo 'No more';
				return;
			}
			
			foreach($acct_11 as $uu)
			{
				$uu->acct1;
				$uu->acct1->acct_discount = 45;
				$uu->acct1->save();
			}
			
			echo 'Processing';
			echo '<pre>';			
					
		}
		
		
		function load_prev_reading()
		{
			$acct_11 = Exp4::whereHas('acct2', function($q1){
							//~ $q1->whereNull('acct_discount');
						 })
						//~ ->with('acct2')
						->whereNull('Z')
						->limit(50)
						->get();
						
			
			if(count($acct_11) == 0)
			{
				echo 'No more';
				return;
			}
			
			foreach($acct_11 as $uu)
			{
				$uu->acct2;

				if(!$uu->acct2)
				{
					$uu->Z = 1;
					$uu->save();					
					continue;
				}
				
				$prev_date =  date('Y-m-d',strtotime($uu->D));
				$cur_date  =  date('Y-m-d',strtotime($uu->E));
				
				$new_read1 = new Reading;
				$new_read1->zone_id=$uu->acct2->zone_id;
				$new_read1->account_id = $uu->acct2->id;
				$new_read1->account_number =$uu->acct2->acct_no;
				$new_read1->meter_number = $uu->acct2->meter_number1;
				$new_read1->period = '2019-12-01';
				$new_read1->curr_reading = $uu->G;
				$new_read1->prev_reading = $uu->F;
				$new_read1->current_consump = $uu->H;
				$new_read1->init_reading = $uu->F;
				$new_read1->status = 'active';
				$new_read1->bill_stat = 'billed';
				$new_read1->date_read=$cur_date;
				$new_read1->prev_read_date=$prev_date;
				$new_read1->curr_read_date=$cur_date;
				$new_read1->save();
				
				$uu->Z = 1;
				$uu->save();
			}
			
			echo 'Processing';
			echo '<pre>';			
		}
		

}
