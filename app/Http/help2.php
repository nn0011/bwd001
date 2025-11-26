<?php
//date_default_timezone_set('Asia/Manila');
//echo date('Y-m-d H:i:s');
//die();
use Illuminate\Support\Facades\Auth;

use App\AccountMetas;
use App\Accounts;
use App\Zones;
use App\HwdRequests;
use App\Reading;
use App\HwdOfficials;
use App\BillingMdl;
use App\BillingMeta;
use App\BillingRateVersion;
use App\HwdJob;
use App\User;
use App\Role;
use App\Collection;
use App\Reports;
use App\OtherPayable;
use App\BillingDue;
use App\Arrear;
use App\LedgerData;
use App\Invoice;
use App\Bank;
use App\ReadingPeriod;
use App\BillingAdjMdl;
use App\Exp4;


function get_coll_info1_step3($collected, $r1)
{
}

function get_coll_info1_step2($collected, $r1)
{
	
	//~ echo '<pre>';
	//~ print_r($r1);
	
	$CURR_BILL    = (float) @$r1[2];
	$CURR_ARREAR  = (float) @$r1[0];
	$CURR_PENALTY = (float) @$r1[1];
	$ADJUSTMENT   = (float) @$r1[3];
	$ADVANCE_PAY  = (float) @$r1[4];
	
	$arr_ret = array();
	$arr_ret['adj'] = $ADJUSTMENT;
	

	if($ADJUSTMENT > 0 )
	{
		if($ADJUSTMENT > $CURR_ARREAR){
			$ADJUSTMENT -= $CURR_ARREAR;
			$CURR_ARREAR = 0;
		}else{
			$CURR_ARREAR -= $ADJUSTMENT;
			$ADJUSTMENT = 0;
		}

		if($ADJUSTMENT > $CURR_PENALTY){
			$ADJUSTMENT -= $CURR_PENALTY;
			$CURR_ARREAR = 0;
		}else{
			$CURR_PENALTY -= $ADJUSTMENT;
			$ADJUSTMENT = 0;
		}

		if($ADJUSTMENT > $CURR_BILL){
			$ADJUSTMENT -= $CURR_BILL;
			$CURR_ARREAR = 0;
		}else{
			$CURR_BILL -= $ADJUSTMENT;
			$ADJUSTMENT = 0;
		}
		
	}//
	
	
	$cur_col = ($collected);
	
	if($cur_col > 0){
		if($CURR_ARREAR > 0){
			$arr_ret['ar'] = min($cur_col, $CURR_ARREAR);
			$cur_col = $cur_col - $CURR_ARREAR;
		}
	}
	
	if($cur_col > 0){
		if($CURR_PENALTY > 0){
			$arr_ret['pe'] = min($cur_col, $CURR_PENALTY);
			$cur_col = $cur_col - $CURR_PENALTY;
		}
	}
	
	if($cur_col > 0){
		if($CURR_BILL > 0){
			$arr_ret['bi'] = min($cur_col, $CURR_BILL);
			$cur_col -= $CURR_BILL;
		}
	}
	
	$arr_ret['ap'] = $ADVANCE_PAY;


	//~ echo '<pre>';
	//~ print_r($arr_ret);
	//~ die();
	
	return 	$arr_ret;
}//



function get_coll_info1($coll1)
{

	if(
		$coll1->status == 'active' || 
		$coll1->status == 'collector_receipt' || 
		$coll1->status == 'cancel_receipt' || 
		$coll1->status == 'cancel_cr'
	){
		//Do nothing
	}else{
		return false;
	}

	
	$cust_id  = $coll1->cust_id;
	$pay_date = $coll1->payment_date;
	
	
	$bill1 = BillingMdl::where('account_id', $cust_id)
				->where('period', date('Y-m-01',strtotime($pay_date)))
					->where('period', date('Y-m-01',strtotime($pay_date)))
						->where('bill_date', '<', $pay_date)
							->where('created_at', '<', $pay_date)
								->orderBy('period','desc')
									->first();//created_at


	$pay_arr_type = array(
			'payment', 
			'payment_cancel',
			'payment_cr',
			'cancel_cr',
			'wtax'
		);		
	
	$beg101_11 = LedgerData::where('status', 'active')
					->whereIn('led_type', $pay_arr_type)
						->where('acct_id', $cust_id)
							->where('coll_id', $coll1->id)
								->where('reff_no', $coll1->invoice_num)
									->orderBy('zort1','desc')
										->orderBy('id','desc')
											->first();
	
	$advance_payment = 0;
	
	if($beg101_11){
		if($beg101_11->ttl_bal < 0){
			$advance_payment = $beg101_11->ttl_bal;
			$neg_payment     = ($beg101_11->payment) * -1;
			//~ ap= -200 vs neg_p = -100
			if($neg_payment  > $advance_payment){
				$advance_payment = $neg_payment;
			}
		}
	}
	
	/**
	if($coll1->invoice_num == 3982){
		echo '<pre>';
		//~ print_r($advance_payment);
		//~ print_r($neg_payment);
		echo $beg101_11->payment;
		print_r($beg101_11->toArray());
		die();
	}
	/**/
	
    if(!$bill1)
    { // Beginning Balance
		
				
		$my_payment = $beg101_11->payment;
		
		$beg101_22 = LedgerData::where('status', 'active')
						->where('acct_id', $cust_id)
							->where('id', '<', $beg101_11->id)
							->whereIn('led_type', ['billing', 'penalty', 'beginning'])
								->orderBy('zort1','desc')
									->orderBy('id','desc')
										//~ ->first();
										->get();
		
		$my_arrear1 = 0;
		$my_penalty = 0;
		
		foreach($beg101_22 as $bb1)
		{
			$pre_pay2 =  $my_payment;
			
			if($bb1->led_type == 'billing'){
				$my_bil = ($bb1->billing - $bb1->discount);
				$my_payment -= $my_bil; 
				$my_arrear1 += $my_payment > 0?$my_bil:$pre_pay2; 
			}
			
			if($bb1->led_type == 'penalty'){
				$pp1  = $bb1->penalty;
				$my_payment -= ($pp1);  
				$my_penalty += ($my_payment > 0)?$pp1:$pre_pay2; 
			}
			
			if($bb1->led_type == 'beginning'){
				$beg1  = $bb1->ttl_bal;
				$my_payment -= ($beg1);  
				$my_arrear1 += ($my_payment > 0)?$beg1:$pre_pay2; 
			}
			
			if($my_payment <= 0)
			{
				break;
			}
		}
		
		//~ echo $my_arrear1;
		//~ echo '<br />';
		//~ echo $my_penalty;
		//~ echo '<pre>';
		//~ print_r($beg101_22->toArray());
		//~ die();
		
		//~ echo '<pre>';
		//~ print_r($coll1->toArray());
		//~ print_r($beg101_22->toArray());
		//~ die();
		
		$prio = array();
		//~ $prio[] = @$beg101_22->ttl_bal; //0 Arrear
		$prio[] = $my_arrear1; //0 Arrear
		$prio[] = $my_penalty; //1 Penalty
		$prio[] = 0; //2 Current Bill
		$prio[] = 0; //3 Adjustment
		$prio[] = $advance_payment;//4 Advance Payment
		
		return $prio;
	}//
	
	//~ $my_arrear  = null;
	//~ $my_due 	= null;
	//~ $my_adju    = null;
	
	$my_arrear =  Arrear::where('acct_id', $bill1->account_id)->where('period', $bill1->period)->first();
	$my_due    =  BillingDue::where('period', $bill1->period)->where('acct_id', $bill1->account_id)->where('due_stat', 'active')->first();
	$my_adju   =  BillingAdjMdl::selectRaw('SUM(amount) as ttl')->where('acct_id', $bill1->account_id)->where('date1', '>', $bill1->bill_date)->where('adj_typ','billing')->where('status', 'active')->first();
	
	

	$CURR_BILL = $bill1->curr_bill - $bill1->discount;
	$CURR_ARREAR = 0;
	$CURR_PENALTY = 0;
	
	if(@$my_arrear){
		$CURR_ARREAR = @$my_arrear->amount;
	}

	if(@$my_due){
		$CURR_PENALTY = @$my_due->due1;
	}

	$ttl1 = $CURR_BILL + $CURR_ARREAR + $CURR_PENALTY;
	
	
	$prio = array();
	$prio[] = round($CURR_ARREAR,2);//0
	$prio[] = round($CURR_PENALTY,2);//1
	$prio[] = round($CURR_BILL,2);//2
	$prio[] = @$my_adju->ttl;//3
	$prio[] = $advance_payment;//4 Advance Payment

	
	
	$coll_credit = Collection::where('cust_id', $cust_id)
						->where(function($q1){
								$q1->where('status', 'active');
								$q1->orWhere('status', 'collector_receipt');
							})
							->where('id', '<', $coll1->id)
							->where('payment_date', '>', $bill1->created_at)
							->sum('payment');

	$coll_debit = Collection::where('cust_id', $cust_id)
						->where(function($q1){
								$q1->where('status', 'cancel_receipt');
								$q1->orWhere('status', 'cancel_cr');
							})
							->where('id', '<', $coll1->id)
							->where('payment_date', '>', $bill1->created_at)
							->sum('payment');	
							
	$TTL_COL = ($coll_credit - $coll_debit);

	
	/*
	if($coll1->invoice_num == 3982){
		echo '<pre>';
		print_r($prio);
		//~ echo $bill1->bill_date;
		echo $TTL_COL;
		//~ echo $CURR_PENALTY;
		die();
	}*/	
	
	
	if($TTL_COL <= 0){
		return $prio;
	}
	
	
	if($CURR_ARREAR != 0)
	{
		$TTL_COL -= $CURR_ARREAR;
		if($TTL_COL < 0){
			$mmm = $TTL_COL + $CURR_ARREAR;
			$nnn = $CURR_ARREAR - $mmm;
			$prio[0] = $nnn;
			return $prio;
		}else{
			unset($prio[0]);
		}
	}else{
		unset($prio[0]);
	}
	

	
	if($CURR_PENALTY > 0)
	{
		$TTL_COL -= $CURR_PENALTY;
		if($TTL_COL < 0){
			$mmm = $TTL_COL + $CURR_PENALTY;
			$nnn = $CURR_PENALTY - $mmm;
			$prio[1] = $nnn;
			return $prio;
		}else{
			unset($prio[1]);
		}
	}else{
		unset($prio[1]);
	}



	
	/*
	if($CURR_BILL > 0)
	{
		$TTL_COL -= $CURR_BILL;
		
		if($TTL_COL <= 0){
			$mmm = $TTL_COL + $CURR_BILL;
			$nnn = $CURR_BILL - $mmm;
			$prio[2] = $nnn;
			return $prio;
		}else{
			unset($prio[2]);
		}

		
		
	}else{
			unset($prio[2]);
	}
	*/
	
	
	
	
	return $prio;	
	
	echo '<pre>';
	print_r($prio);
	print_r($bill1->toArray());
	//~ print_r($coll1->toArray());
	//~ print_r($my_due->toArray());
	die();

	
}//


function cons_average($period, $acct_id)
{
	$sql1 = "
		
		SELECT AVG(C1) as c_ave FROM (
			SELECT current_consump as C1 FROM `readings`
			WHERE account_id=? AND period < ?
			order by period desc
			limit 3
		) AS MMM
	
	";
	
	return DB::select($sql1, [$acct_id, $period]);
}//


function CONS_111()
{	
	
	
	$sql1 = "
		SELECT 
				HL.led_key1,
				HL.led_date2, 
				AA.acct_no, 
				AA.fname, 
				AA.lname, 
				AA.acct_status_key 
		FROM `hwd_ledgers` as HL 
		LEFT JOIN accounts as AA on AA.id=HL.led_key1
		WHERE 
			HL.led_date2 like '2019-09%' AND 
			HL.ctyp1='account_modify' AND 
			HL.led_desc1 like '%to Disconnected%'
	";
}


function tview_dis_act_history()
{
	$sql1 = "
	
			SELECT * FROM (
				SELECT * FROM (SELECT id,led_key1,led_date2,'D' as stat FROM `hwd_ledgers` where  led_desc1 like '%to Disconnect%'
				UNION
				SELECT id,led_key1,led_date2,'A' as stat FROM `hwd_ledgers` where  led_desc1 like '%to ACTIVE%') 
				AS TAB1
			)
			AS dis_act_history 
	
	";
	
	return $sql1;
}//


function tview_ledger_latest_by_zone($zone_id=1, $date1=null)
{
	$date1 = strtotime($date1);
	
	if(!$date1){
		$date1 = date('Y-m-d');
	}else{
		$date1 = date('Y-m-d', $date1);
	}
	
	
	$sql1 = "
	
			SELECT 	
				LD2.acct_id, 
				LD2.ttl_bal, 
				LD2.date01, 
				LD2.led_type, 
				LD2.period,
				AA.zone_id,
				AA.acct_no,
				AA.route_id
			FROM ledger_datas as LD2 
			LEFT JOIN accounts as AA ON AA.id=LD2.acct_id

			WHERE EXISTS(
				SELECT * FROM (
					SELECT max(id) mx1   FROM `ledger_datas` 
					WHERE date01 <= '$date1'
					GROUP BY acct_id
				) LD1 WHERE  LD1.mx1 = LD2.id
			 )
			 
			 AND LD2.ttl_bal > 0 AND AA.zone_id=$zone_id
			 ORDER BY AA.route_id ASC	
	";
	
	return $sql1;
	
	
}//



//tview_ledger_billing_ttl(339);
//~ function tview_ledger_billing_ttl($acct_id, $date1=null)
function tview_ledger_billing_ttl($acct_id, $led_id=0)
{
	
	//~ $date1 = strtotime($date1);
	//~ if(!$date1){
		//~ $date1 = date('Y-m-d');
	//~ }else{
		//~ $date1 = date('Y-m-d', $date1);
	//~ }
	
	//~ if($led_id == 0){
	//~ }
	
	$cur_led = LedgerData::find($led_id);
	
	if(!$cur_led){
		return false;
	}
	
	$sql1 = "
			SELECT *, ((A2+A4) - A5) AS AR1  
			FROM (
				SELECT 
					period,	
					UNIX_TIMESTAMP(period) as PPP,
					SUM(arrear)   AS A1, 
					IFNULL(SUM(billing),0)  AS A2, 
					IFNULL(SUM(payment),0)  AS A3, 
					IFNULL(SUM(penalty),0)  AS A4, 
					IFNULL(SUM(bill_adj),0) AS A5,
					IFNULL(SUM(discount),0) AS A6 
					
				FROM (
					SELECT acct_id, arrear, billing, payment, discount, penalty, bill_adj, period FROM `ledger_datas` 
						WHERE acct_id=$acct_id AND 
							 id < $led_id  AND 
							 led_type != 'beginning'
				) AS TAB1
					GROUP BY period
				) AS TAB2	
				WHERE A2 != 0
				ORDER BY period DESC	
	";
	
	return $sql1;
	
	
}//

function no_bill_apply_tax($wtax, $wtax_value, $ttl_bal, $acct_id, $method, $new_col, $trans_date33_d)
{
	
		$acct11 = Accounts::find($acct_id);
	
		$ww1 = 0;
		$per_c1 = 0;

		if($wtax == 'true')
		{
			$ww1 = $wtax_value;
		}
		
		
		if($wtax == 'true')
		{
			$led_type = 'wtax';

			$ttl_bal -= $ww1;

			$new_ledger_data = new LedgerData;
			$new_ledger_data->led_type = $led_type;//
			$new_ledger_data->acct_id  = $acct_id;
			$new_ledger_data->ttl_bal  = $ttl_bal;
			$new_ledger_data->payment  = $ww1;
			$new_ledger_data->ledger_info = 'Withholding Tax '.strtoupper($method);
			$new_ledger_data->status = 'active';
			$new_ledger_data->acct_num = $acct11->acct_no;

			//$new_ledger_data->date01 = date('Y-m-d');
			$new_ledger_data->date01 = $trans_date33_d;

			$new_ledger_data->period = date('Y-m-01');
			$new_ledger_data->reff_no = $new_col->invoice_num;
			$new_ledger_data->coll_id = $new_col->id;
			$new_ledger_data->save();
		}
		

		
}//END



function ee($mm, $xx, $yy)
{
	echo '<pre>';
	echo $xx.' '.$yy.'<br />';
	print_r($mm);
	echo '</pre>';
	die();

}

function ee1($mm, $xx, $yy)
{
	echo '<pre>';
	echo $xx.' '.$yy.'<br />';
	print_r($mm);
	echo '</pre>';
}
