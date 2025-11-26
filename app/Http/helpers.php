<?php
//date_default_timezone_set('Asia/Manila');
//echo date('Y-m-d H:i:s');
//die();
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

use App\Services\Collections\CollectionService;


use Illuminate\Support\Facades\Auth;


session_start();
ob_start();


define('PENALTY_PERCENT', 0.05);
define('PENALTY_PERCENT_GOV', 0.02);
define('PENALTY_GL_CODE', '111-111-02');
define('COLOR_01', '#12474e');



define('GOV_TYPE', [10,11,12,13]);
define('SENIOR_ID', 49);
define('RES_ID',14);
define('RECONNECT_ID',2);
// define('LOGO_SRC','/hinatuan.jpg');
define('LOGO_SRC','');
define('INIT_INSTALL_period','2020-10-01'); // FOR BILLING USED in ReadingCtrl on uploadReadingData_ReadNBill  Search for #1110000



// define('WD_NAME','LAS NIEVES WATER DISTRICT');
// define('WD_ADDRESS','LAS NIEVES, AGUSAN DEL NORTE');
// define('WD_MANAGER','JERWIN YEE');
// define('WD_MANAGER_RA','General Manager D');

define('WD_NAME','BAYUGAN CITY WATER DISTRICT');
define('WD_ADDRESS','BAYUGAN CITY, AGUSAN DEL NORTE');
define('WD_MANAGER','---');
define('WD_MANAGER_RA','General Manager D');

define('REP_SIGN1','-----');
define('REP_SIGN1_TITLE','Cashier');

define('REP_SIGN2','----');
define('REP_SIGN2_TITLE','Accounting');

define('REP_SIGN3','-----');
define('REP_SIGN3_TITLE','Billing Officer');

define('REP_SIGN4','-----');
define('REP_SIGN4_TITLE','TELLER 1');

// Dominic Trent D. Villaluz
// DOMINIC TRENT D. VILLALUZ


global $extra_msg;
global $EXEMP_ZONES;

$EXEMP_ZONES = [];


///LEDGER ZORT01 START
///LEDGER ZORT01 START
///LEDGER ZORT01 START
function ledger_zort01_desc(&$my_ledger)
{
	$my_ledger->orderBy('zort1', 'desc');
	$my_ledger->orderBy('id', 'desc');
}

function ledger_zort01_asc(&$my_ledger)
{
	$my_ledger->orderBy('zort1', 'asc');
	$my_ledger->orderBy('id', 'asc');
}

function get_ledgers($acct_id, $s='desc')
{
	$led1 = LedgerData::where('acct_id', $acct_id)->where('status', 'active');
	if($s=='desc'){ledger_zort01_desc($led1);}
	else{ledger_zort01_asc($led1);}
	return $led1->get();
}

function get_last_ledger($acct_id)
{
	$led1 = LedgerData::where('acct_id', $acct_id)->where('status', 'active');
	ledger_zort01_desc($led1);	
	return $led1->first();
}
///LEDGER ZORT01 END
///LEDGER ZORT01 END
///LEDGER ZORT01 END



function AAA_PyArrearCollection($coll1, $prev_only=0){
	
	//~ $py1   = date('Y', strtotime($date1.' - 1 Year'));
	
	$prevBalance = 0;
	$allCollCy = 0;
	
	$py1   = date('Y-01-01', strtotime($coll1->payment_date));
	$cy1   = date('Y', strtotime($coll1->payment_date));
	
	$led01 = LedgerData::where('date01', '<', $py1)
					->where('acct_id', $coll1->cust_id)
					  ->where('status', 'active')
						->orderBy('id', 'desc')
							->first();
	
	if($led01){
		$prevBalance = @$led01->ttl_bal;
	}
	
	
	$total_collected = DB::select("
					SELECT 
						SUM(payment) TTL 
					FROM 
						collections
					WHERE id IN 
					(
						SELECT MAX(id) my_id FROM collections 
						WHERE collection_type='bill_payment' AND cust_id=? AND payment_date like ?
						GROUP BY invoice_num
					)
					AND 
						status IN ('active', 'collector_receipt')
					AND
						id < ?
			", [$coll1->cust_id, $cy1.'%', $coll1->id]);
	
	$allCollCy = @$total_collected[0]->TTL;//1000
	$CColPYA = ($prevBalance - $allCollCy);
	
	if($prev_only ==1){
		return $CColPYA;
	}
	
	
	//~ echo '<pre>';
	//~ print_r($CColPYA);
	//~ die();
	
	if($CColPYA <= 0){
		return 0;
	}
	
	$xx11 = $CColPYA;
	
	$CColPYA -= $coll1->payment;//221.2
	//~ $CColPYA -= 2000;
	
	if($CColPYA > 0){
		return $coll1->payment;
	}
	
	return $xx11;
}//



function AAA_GetPeriodDates($period1)
{
	
	$period1   =  date('Y-m-01', strtotime($period1));
	$prev_date =  date('Y-m-01', strtotime($period1.' - 1 Month'));
	
	$curr_period01 = ReadingPeriod::where('period', $period1)->first();
	$prev_period01 = ReadingPeriod::where('period', $prev_date)->first();
	
	$A_read_dates = json_decode(@$curr_period01->read_dates, true);
	$B_read_dates = json_decode(@$prev_period01->read_dates, true);
	
	$A_due_dates = json_decode(@$curr_period01->due_dates, true);
	$B_due_dates = json_decode(@$prev_period01->due_dates, true);

	$A_fine_dates = json_decode(@$curr_period01->fine_dates, true);
	$B_fine_dates = json_decode(@$prev_period01->fine_dates, true);
	
	return array(
				'prev' => array(
						'due' =>  $B_due_dates,
						'fine' => $B_fine_dates,
						'read' => $B_read_dates
					),
				'curr' => array(
						'due' =>  $A_due_dates,
						'fine' => $A_fine_dates,
						'read' => $A_read_dates
					)
			
			);
	
}//


function AAA_DailyCollectionPerTeller($tell_id, $date1)
{
	$date_start = date('Y-m-d 00:00:01', strtotime($date1));
	$date_end   = date('Y-m-d 23:59:59', strtotime($date1));

	$collection1 = Collection::where('payment_date', '>=', $date_start)
						->where('payment_date', '<=', $date_end)
							->where('collector_id', $tell_id)
								->whereIn('status', ['active', 'collector_receipt', 'cr_nw', 'or_nw'])
									->selectRaw('SUM(payment) as TTL, MIN(invoice_num) inv_min, MAX(invoice_num) inv_max, COUNT(id) INV_TTL ')
										->first();
	return $collection1;
}//


function HELPER_get_PAST_collection_info($vv){
	
			$inv1 = $vv->invoice_num;
			$col1 = $vv->id;
			$typ1 = $vv->collection_type;
			$act1 = $vv->cust_id;
			
			$led1 = LedgerData::where('acct_id', $act1)
						->where('status', 'active')
							->orderBy('zort1', 'desc')
							->orderBy('id', 'desc')
								->get();
			
			$led_id_start = 0;
			$led_id_end   = 0;
			
			
			//~ BILLING START
			$total_bill = 0;//BILLING
			$ttl_bal 	= 0;
			
			$last_billing_ledger = null;
			foreach($led1 as $l1){
				
				$last_billing_ledger = $l1;
				
				$ttl_bal = $l1->ttl_bal;
				
				if($l1->led_type == 'billing'){
					$total_bill = ($l1->billing - $l1->discount);
					$led_id_start = $l1->id;					
					break;
				}
			}
			//~ BILLING END
			
			//~ ARREAR START
			$arrear = $ttl_bal - $total_bill; // ARREAR
			//~ ARREAR END
			
			
			//PY ARREAR START
			$py_arrear = 0;
			
			if($arrear > 0){
				
				$d1 = date('Y-01-01', strtotime($vv->payment_date));
				$d1 = date('Y-m-d', strtotime($d1.' - 1 sec'));
				
				if(!empty($last_billing_ledger)){
					
					$py_led01 = LedgerData::where('acct_id', $act1)
									->where('date01','<=', $d1)
										->where('status', 'active')
											->orderBy('zort1', 'desc')
											->orderBy('id', 'desc')
											//~ ->orderBy('date01', 'desc')
												->first();
					
					if($py_led01){
						$py_arrear = $py_led01->ttl_bal;
					}
					
				}
				
			}
			//PY ARREAR END

			
			$led2 = LedgerData::where('acct_id', $act1)
						->where('reff_no', $inv1)
							->where('led_type', 'payment')
								->where('status', 'active')
									->orderBy('zort1', 'desc')
									->orderBy('id', 'desc')
										->first();
			
			
			$led_id_end = $led2->id;


			//~ PENALTY START
			
			$pen_ttl1  = 0;
			$penalty01 = LedgerData::where('acct_id', $act1)
								->where('id','>', $led_id_start)
									->where('id','<', $led_id_end)
										->where('led_type', 'penalty')
											->where('status', 'active')
											->orderBy('zort1', 'desc')
												->orderBy('id', 'desc')
													->limit(1)	
														->get();
			
			foreach($penalty01 as $pp){
				$pen_ttl1 = $pp->penalty; // PENALTY
			}
			
			//~ PENALTY END
			
			/*
			 *
			 * 
			 *  
			 * 
			 */
			 
			
			//~ PAYMENT START
			$pay_led_type = array('cancel_cr','payment','payment_cancel','payment_cr', 'wtax');
			
			$led_payment = LedgerData::where('acct_id', $act1)
								->where('id','>', $led_id_start)
									->where('id','<', $led_id_end)
										->whereIn('led_type', $pay_led_type)
											->where('status', 'active')
											->orderBy('zort1', 'desc')
												->orderBy('id', 'desc')
													->get();			
			
			$total_payed = 0;
			foreach($led_payment as $lp1){
				
				if($lp1->led_type == 'wtax'){
					$total_payed += $lp1->bill_adj;
					continue;
				}
				
				$total_payed += $lp1->payment ;
			}
			//~ PAYMENT END
			
			
		$ret11 = array(
					'arr' => $arrear, 
					'bil' => $total_bill, 
					'pen' => $pen_ttl1,
					'pya' => $py_arrear  
				);
			
			
		//////////////////////
		//////////////////////

		$origpay = $total_payed;
		$total_payed -= $arrear;
		
		if($total_payed >= 0){
			$arrear = 0;
			//NEXT
		}
		elseif($total_payed < 0){
			$ret11['arr'] = $arrear - $origpay;
			return $ret11;
			//RETURN
		}
		
		//////////////////////
		//////////////////////
		
		$origpay = $total_payed;
		$total_payed -= $pen_ttl1;
		
		if($total_payed >= 0){
			$pen_ttl1= 0;
			//NEXT
		}
		elseif($total_payed < 0){
			$ret11['pen'] = $pen_ttl1 - $origpay;
			return $ret11;
			//RETURN
		}

		
		//////////////////////
		//////////////////////
		
		$origpay = $total_payed;
		$total_payed -= $total_bill;
		
		if($total_payed >= 0){
			$total_bill= 0;
			//NEXT
		}
		elseif($total_payed < 0){
			$ret11['bil'] = $total_bill - $origpay;
			return $ret11;			
			//RETURN
		}

		return $ret11;			
			
}//endfunc





function get_dd1($raw1, $z_id)
{
	$ret_date = '';
	$j1 = json_decode($raw1);

	if($j1)
	{
		$j2 = (array) $j1;

		if(!empty($j2[$z_id]))
		{
			$j3 = $j2[$z_id];
			$j4 = array_merge([], array_filter(explode('|',$j3)));
			$j5 = array_filter(explode('@',$j4[0]));
			$ret_date = $j5[1];
		}
	}

	return $ret_date;
}//


function get_adj_desc($ref1)
{
	$bam = BillingAdjMdl::find($ref1);
	if(!$bam){return '';}
	return @$bam->adj_typ_desc;
}

function get_other_payable_by_id($id)
{
	return OtherPayable::find($id);
}

function get_billing_info_total($new_cur, $cls_id, $zone_id)
{
	
	if(@$cls_id == 'senior')
	{
		
		$bill_all_info  =  BillingMdl::where('period','like', $new_cur.'%')
							->whereNotNull('consumption')
							->where('status','active')
							->whereHas('account', function($query)use($cls_id, $zone_id){
								$query->where('zone_id', $zone_id);
								$query->where('acct_discount', SENIOR_ID);
								//~ $query->where('status','active');
							})
							->select(DB::raw('
									SUM(billing_total) as  ttl_curr_bill,
									SUM(consumption)  ttl_consum,
									SUM(arrears)  ttl_arrear,
									SUM(discount) ttl_discount,
									SUM(penalty) ttl_penalty,
									COUNT(id) ttl_cons
							'))
							->get();
							
		//Fix the accuracy of the Penalty
		//START HERE 
		$bill_due = BillingDue::where('period','like', $new_cur.'%')
						->whereHas('account', function($q1)use($cls_id, $zone_id){
								$q1->where('zone_id', $zone_id);
								$q1->where('acct_discount', SENIOR_ID);
								//~ $q1->where('status','active');
							})
							->where('due_stat', 'active')
							->selectRaw('SUM(due1) as PP1')
								->first();
												
		$bill_all_info[0]->ttl_penalty = $bill_due->PP1;
		//END HERE								
							
							
		return $bill_all_info;
		
	}//
	
	$bill_all_info = BillingMdl::where('period','like', $new_cur.'%')
						->whereNotNull('consumption')
						->where('status','active')
						->whereHas('account', function($query)use($cls_id, $zone_id){
							$query->where('acct_type_key',$cls_id);
							$query->where('zone_id', $zone_id);
							$query->where(function($q2){
									$q2->where('acct_discount','!=', SENIOR_ID);
									$q2->orWhereNull('acct_discount');
									$q2->orWhere('acct_discount', 0);
								});
							//~ $query->where('status','active');
						})
						->select(DB::raw('
								SUM(billing_total) as  ttl_curr_bill,
								SUM(consumption)  ttl_consum,
								SUM(arrears)  ttl_arrear,
								SUM(discount) ttl_discount,
								SUM(penalty) ttl_penalty,
								COUNT(id) ttl_cons
						'))
						->get();
		
		
		//Fix the accuracy of the Penalty
		//START HERE 
		$bill_due = BillingDue::where('period','like', $new_cur.'%')
						->whereHas('account', function($q1)use($cls_id, $zone_id){
								$q1->where('zone_id', $zone_id);
								$q1->where('acct_type_key',$cls_id);
								$q1->where(function($q2){
										$q2->where('acct_discount','!=', SENIOR_ID);
										$q2->orWhereNull('acct_discount');
										$q2->orWhere('acct_discount', 0);
										
									});
								//~ $q1->where('status','active');
							})
							->where('due_stat', 'active')
							->selectRaw('SUM(due1) as PP1')
								->first();
												
		$bill_all_info[0]->ttl_penalty = $bill_due->PP1;
		//END HERE						
	
	
	return $bill_all_info;
	
}//


function get_due_dates_V3($period1)
{
	$read_period = ReadingPeriod::where('period','like', $period1.'%')
				->first();

	$due_dates = array();

	if(!empty($read_period->due_dates2)){
		$due_dates = json_decode($read_period->due_dates2, true);
	}

	$due_date_by_zone = array();

	$zones = Zones::where('status', '!=', 'deleted')->get();
	foreach($zones as $zz)
	{
		$dd1 = @$due_dates[$zz->id];
		$due_date_by_zone[$zz->id] =  $dd1;
	}//

	return $due_date_by_zone;
}//



function get_due_dates($period1)
{
	$read_period = ReadingPeriod::where('period','like', $period1.'%')
				->first();

	$due_dates = array();

	if(!empty($read_period->fine_dates)){
		$due_dates = json_decode($read_period->fine_dates, true);
	}

	$due_date_by_zone = array();

	$zones = Zones::where('status', '!=', 'deleted')->get();
	foreach($zones as $zz)
	{
		$dd1 = @$due_dates[$zz->id];
		$due_date_by_zone[$zz->id] =  $dd1;
	}//

	return $due_date_by_zone;
}//

function get_billing_number($bill_id){
	$bill1 = BillingMdl::find($bill_id);
	if(!$bill1){
		return '';
	}
	return @$bill1->bill_num_01;
}//



function balance_breakdown_receipt_334($collect1)
{
	
	$valid_coll_type = array(
							'active',
							'collector_receipt',
							'cr_nw',
							'or_nw'
						);
	
	if(!in_array($collect1->status, $valid_coll_type)){
		return array();
	}
	
	if(in_array($collect1->status, array('cr_nw', 'or_nw'))){
		return array();
	}	
	
	$coll_date = $collect1->payment_date;
	$period    = date('Y-m', strtotime($coll_date));
	
	$led_data1 = LedgerData::where('acct_id', $collect1->cust_id)
							->where('reff_no', $collect1->invoice_num)
								->first();
							
	if(!$led_data1){
		return array();
	}
	
							
	$led_data33 = LedgerData::where('acct_id', $collect1->cust_id)
						->where('id', '<', $led_data1->id)
							->where(function($q1){
									$q1->where('led_type',   'beginning');
									$q1->orWhere('led_type', 'billing');
									$q1->orWhere('led_type', 'penalty');
									$q1->orWhere('led_type', 'adjustment');
								})
								->where('status','active')
									->orderBy('zort1', 'asc')
										->orderBy('id', 'asc')
											->get();
	
	$water_bill_coll1 = array(
							'active',
							'collector_receipt'
							//~ 'cancel_cr',
							//~ 'cancel_receipt'
						);
							
	$prev_coll_made = Collection::where('id', '<', $collect1->id)
							->where('cust_id', $collect1->cust_id)
								->whereIn('status', $water_bill_coll1)
									->sum('payment');
									
	$curr_coll_made = $collect1->payment;
	
	$conti     = array();
	$xx        = 0;
	$cur_conti = null;
	
	foreach($led_data33 as $led1)
	{
		
		if($led1->led_type == 'adjustment'){
			$prev_coll_made += $led1->bill_adj;
			$xx ++;
			continue;
		}
		
		$conti[$xx]['typ']    	  = $led1->led_type;
		$conti[$xx]['date']   	  = $led1->date01;
		$conti[$xx]['date1'] 	  = $led1->date01;
		$conti[$xx]['val']   	  = 0;
		$conti[$xx]['ttl_bal']    = $led1->ttl_bal;
		
		if($led1->led_type == 'beginning'){
			$conti[$xx]['amt'] = $led1->arrear;
		}
		
		if($led1->led_type == 'billing'){
			$conti[$xx]['amt'] = ($led1->billing -  $led1->discount);
		}
		
		if($led1->led_type == 'penalty'){
			$conti[$xx]['amt'] = $led1->penalty;
		}

		$cur_conti = $conti[$xx];
		
		$xx++;
	}
	
	if(!empty($cur_conti)){
		
		if($curr_coll_made >= $cur_conti['ttl_bal']){
			$curr_coll_made = $cur_conti['ttl_bal'];
		}
	}
	
	foreach($conti as  $kk => $c1)
	{
		$prev_coll_made -= $c1['amt'];
		
		if($prev_coll_made < 0){
			$conti[$kk]['amt'] = round($c1['amt'] - ($c1['amt'] + $prev_coll_made),2);
			break;
		}else{
			unset($conti[$kk]);
		}
	}
	
	$rec1 = array();
	$yy = 0;
	foreach($conti as  $kk => $c1)
	{
		$rec1[$yy] = $c1;
		
		$curr_coll_made -= $c1['amt'];
		if($curr_coll_made <= 0){
			$rec1[$yy]['amt'] = round($curr_coll_made+$c1['amt'],2);
			break;
		}else{
			$rec1[$yy]['amt'] = $conti[$kk]['amt'];
		}
		$yy++;
	}
	
	foreach($rec1 as  $kk => $c1)
	{
		if($rec1[$kk]['amt'] <= 0){
			unset($rec1[$kk]);
		}
	}
	
	foreach($rec1 as  $kk => $c1)
	{
		$rec1[$kk]['val'] = $rec1[$kk]['amt']; 
	}

	
	return $rec1;
}//





function balance_breakdown_receipt_22($collect1)
{


	$led_data1 = LedgerData::where('acct_id', $collect1->cust_id)
					->where('reff_no', $collect1->invoice_num)
						->first();



	$led_data33 = LedgerData::where('acct_id', $collect1->cust_id)
				->where(function($query){
					$query->where('led_type', '=', 'billing');
					$query->orWhere('led_type', '=', 'beginning');
					$query->orWhere('led_type', '=', 'penalty');
					$query->orWhere('led_type', '=', 'adjustment');
					$query->orWhere('led_type', '=', 'payment');
					$query->orWhere('led_type', '=', 'payment_cancel');
					$query->orWhere('led_type', '=', 'payment_cr');
					$query->orWhere('led_type', '=', 'cancel_cr');
				})
				->where('id', '<', $led_data1->id)
				->where('status','active')
				->orderBy('zort1', 'desc')
				->orderBy('id', 'desc')
					->get();

	$ttl1 = $led_data33[0]->ttl_bal;



	$bal1 = $ttl1;

	$mm1 = array();
	$xx = 0;

	foreach($led_data33 as $ll1)
	{

		if($bal1 <= 0){break;}

		$val1 = 0;
		$pre_val1 = 0;

		$descr = $ll1->ledger_info;

		if($ll1->led_type == 'payment'){continue;}
		if($ll1->led_type == 'payment_cancel'){continue;}
		if($ll1->led_type == 'payment_cr'){continue;}
		if($ll1->led_type == 'cancel_cr'){continue;}

		if($ll1->led_type == 'billing'){$pre_val1 = $val1 = $ll1->billing;}
		if($ll1->led_type == 'beginning'){$pre_val1 = $val1 = $ll1->arrear;
				$descr = "Beginning Balance \n      ".date('F Y', strtotime($ll1->date01))."";
			}
		if($ll1->led_type == 'penalty'){$pre_val1 = $val1 = $ll1->penalty;}
		if($ll1->led_type == 'adjustment')
		{

			$ll1->adjustments;

			$descr = @$ll1->adjustments->adj_typ_desc;

			if(empty($descr)){
				$descr = $ll1->ledger_info;
			}

			$pre_val1 =  $ll1->bill_adj;

			if($ll1->bill_adj <= 0){
				$val1 = abs($ll1->bill_adj);
			}
		}

		$bal1 -= $val1;
		
		if($ll1->discount <= 0)
		{
			$descr = str_replace('SENIOR CITIZEN', '', $descr);
		}

		$mm1[$xx]['desc'] =  $descr;
		$mm1[$xx]['pre_val']  =  $pre_val1;
		$mm1[$xx]['val']  =  $val1;
		$mm1[$xx]['typ']  =  $ll1->led_type;
		$mm1[$xx]['date1']  =  $ll1->date01;
		
		//~ echo '<pre>';
		//~ print_r($ll1);
		//~ die();
		
		$xx++;
	}


	$ttl_payment = $collect1->payment;
	$mm1 = array_reverse($mm1);

	$new_arr = array();

	foreach($mm1 as $aa){
		if($ttl_payment <= 0){break;}
		$ttl_payment -= $aa['val'];
		$new_arr[] = $aa;
	}

	return $new_arr;
	echo '<pre>';
	//~ print_r($mm1);
	print_r($new_arr);
	//~ print_r($led_data1->toArray());
	//~ print_r($led_data33->toArray());
	die();

}//

function balance_breakdown_receipt($acct_id, $balance)
{
		$led_data33 = LedgerData::where('acct_id', $acct_id)
					->where(function($query){
						$query->where('led_type', '=', 'billing');
						$query->orWhere('led_type', '=', 'beginning');
						$query->orWhere('led_type', '=', 'penalty');
						$query->orWhere('led_type', '=', 'adjustment');
					})
					->where('status','active')
					->orderBy('zort1', 'desc')
					->orderBy('id', 'desc')
						->get();



		//~ echo '<pre>';
		//~ print_r($led_data33->toArray());
		//~ die();

		//~ echo $html1;
		//~ echo '<pre>';
		//~ print_r($led_data->toArray());
		//~ print_r($balance->ttl_bal);

		//~ $bal1 = ($balance->ttl_bal);

		$bal1 = $balance;

		if($bal1 <= 0){

			if(!empty($led_data33)){
				$bal1 = $led_data33[0]->ttl_bal;
			}
		}

		//~ echo $bal1;
		//~ die();

		$mm1 = array();
		$xx = 0;
		foreach($led_data33 as $ll1)
		{

			if($bal1 <= 0){break;}

			$val1 = 0;
			$pre_val1 = 0;


			$descr = $ll1->ledger_info;


			if($ll1->led_type == 'billing'){$pre_val1 = $val1 = $ll1->billing;}
			if($ll1->led_type == 'beginning'){$pre_val1 = $val1 = $ll1->arrear;}
			if($ll1->led_type == 'penalty'){$pre_val1 = $val1 = $ll1->penalty;}
			if($ll1->led_type == 'adjustment'){

				$ll1->adjustments;

				$descr = @$ll1->adjustments->adj_typ_desc;

				if(empty($descr)){
					$descr = $ll1->ledger_info;
				}

				$pre_val1 =  $ll1->bill_adj;

				if($ll1->bill_adj <= 0){
					$val1 = abs($ll1->bill_adj);
				}
			}

			$bal1 -= $val1;
			
			
			if($ll1->discount <= 0)
			{
				$descr = str_replace('SENIOR CITIZEN', '', $descr);
			}			

			$mm1[$xx]['desc'] =  $descr;
			$mm1[$xx]['pre_val']  =  $pre_val1;
			$mm1[$xx]['val']  =  $val1;
			$mm1[$xx]['typ']  =  $ll1->led_type;
			$xx++;
		}

		//~ $mm1 = array_reverse($mm1);

		//~ echo '<pre>';
		//~ print_r($mm1);
		//~ die();

	return $mm1;
}//




function balance_breakdown($acct_id, $balance)
{
		$led_data33 = LedgerData::where('acct_id', $acct_id)
					->where(function($query){
						$query->where('led_type', '=', 'billing');
						$query->orWhere('led_type', '=', 'beginning');
						$query->orWhere('led_type', '=', 'penalty');
						$query->orWhere('led_type', '=', 'adjustment');
					})
					->where('status','active')
					->orderBy('zort1', 'desc')
					->orderBy('id', 'desc')
						->get();


		//~ echo $html1;
		//~ echo '<pre>';
		//~ print_r($led_data->toArray());
		//~ print_r($balance->ttl_bal);

		$bal1 = 0;
		if($balance){
			$bal1 = ($balance->ttl_bal);
		}

		//~ $bal1 = 1;

		$mm1 = array();
		$xx = 0;
		foreach($led_data33 as $ll1)
		{

			if($bal1 <= 0){break;}

			$val1 = 0;
			$pre_val1 = 0;


			$descr = $ll1->ledger_info;


			if($ll1->led_type == 'billing'){$pre_val1 = $val1 = $ll1->billing;}
			if($ll1->led_type == 'beginning'){
					$pre_val1 = $val1 = $ll1->arrear;
					$descr = "Beginning Balance <br/>".date('F Y', strtotime($ll1->date01))."";
			}
			if($ll1->led_type == 'penalty'){$pre_val1 = $val1 = $ll1->penalty;}
			if($ll1->led_type == 'adjustment'){

				$ll1->adjustments;

				$descr = @$ll1->adjustments->adj_typ_desc;

				if(empty($descr)){
					$descr = $ll1->ledger_info;
				}

				$pre_val1 =  $ll1->bill_adj;

				if($ll1->bill_adj <= 0){
					$val1 = abs($ll1->bill_adj);
				}
			}

			$bal1 -= $val1;
			
			
			if($ll1->discount <= 0)
			{
				$descr = str_replace('SENIOR CITIZEN', '', $descr);
			}			

			$mm1[$xx]['desc'] =  $descr;
			$mm1[$xx]['pre_val']  =  $pre_val1;
			$mm1[$xx]['val']  =  $val1;
			$mm1[$xx]['typ']  =  $ll1->led_type;
			$xx++;
		}

		//~ echo '<pre>';
		//~ print_r($led_data33->toArray());
		//~ die();

	return $mm1;
}//




function Q_Search_label($qsearh)
{
	$arr1= array();
	$arr1[1] = 'List of accounts with penalty exeption';
	$arr1[2] = 'List of senior citizen';

	$arr1[7] = 'List of account type COM 1';
	$arr1[8] = 'List of account type COM B';
	$arr1[9] = 'List of account type COMM A';
	$arr1[10] = 'List of account type GOVT./COMMERCIAL 1';
	$arr1[11] = 'List of account type GOVT./COMMERCIAL A';
	$arr1[12] = 'List of account type GOVT';
	$arr1[13] = 'List of account type GOVT. B';
	$arr1[14] = 'List of account type RES';

	$arr1[100] = 'Account Master List';


	return @$arr1[$qsearh];
}

function Exemp111()
{
	$res1 = Exp4::where('O','1')
		->whereHas('acct1')
		->with('acct1')
		->orderBy('C','ASC')
		->orderBy('F','ASC')
		->get();

	foreach($res1 as $r1)
	{
		$r1->acct1bpen_exempt=1;
		$r1->acct1->save();
	}

	echo '<pre>';
	print_r($res1->toArray());
}

function account_type_old_system($tid)
{
	$ret = array();
	$ret[1] = 14;
	$ret[2] = 12;
	$ret[3] = 9;
	$ret[4] = 8;
	$ret[5] = 7;
	$ret[6] = 11;
	$ret[7] = 10;
	$ret[8] = 13;
	$ret[0] = 0;
	return @$ret[$tid];
}

function account_status_old_system($tid)
{
	$ret = array();
	$ret[0] = 1;
	$ret[1] = 2;
	$ret[2] = 3;
	$ret[3] = 4;
	$ret[4] = 5;
	return @$ret[$tid];
}


function FIX_Account_type_allAccounts()
{
	$res1 = Exp4::whereHas('acct1')
		->with('acct1')
		->orderBy('C','ASC')
		->orderBy('F','ASC')
		//~ ->limit(100)
		->get();

	//~ echo '<pre>';

	foreach($res1 as $r1)
	{
		$ctype1 = account_type_old_system($r1->D);

		if($r1->acct1->acct_type_key != $ctype1)
		{
			$r1->acct1->acct_type_key = $ctype1;
			$r1->acct1->save();
			echo ' --- ';
		}
		echo ctype_str(account_type_old_system($r1->D)).' - '.$r1->acct1->lname.'  ----  '.$r1->acct1->acct_type_key.'('.$r1->acct1->acct_no.')';
		echo '<br />';
		//~ print_r($r1->acct1->toArray());
	}


}//


function FIX_NUMBER_OF_BILLS()
{
	$res1 = Exp4::whereHas('acct1')
		->with('acct1')
		->orderBy('C','ASC')
		->orderBy('F','ASC')
		->get();

	foreach($res1 as $r1)
	{
		$r1->acct1->num_of_bill	 = $r1->V;
		$r1->acct1->save();
	}


}//

function FIX_ACCOUNT_STATUS()
{
	$res1 = Exp4::whereHas('acct1')
		->with('acct1')
		->orderBy('C','ASC')
		->orderBy('F','ASC')
		->get();

	foreach($res1 as $r1)
	{
		$acct_stat = account_status_old_system($r1->E);

		if($r1->acct1->acct_status_key != $acct_stat)
		{
			echo con_led_type_v3($acct_stat).' - '.$r1->acct1->lname.'  ----  '.$r1->acct1->acct_status_key.'('.$r1->acct1->acct_no.')';
			echo '<br />';
			//~ $r1->acct1->acct_status_key =  $acct_stat;
			//~ $r1->acct1->save();
		}

		//~ $vvv = con_led_type_v3(account_status_old_system($r1->E));
	}

}


function isGov($acct_type)
{
	//~ if($acct_type == 10){return true;}
	//~ if($acct_type == 11){return true;}
	//~ if($acct_type == 12){return true;}
	//~ if($acct_type == 13){return true;}
	return false;
}


/*
<br />
___________________<br />
System Administrator
*/
function html_signature($name1 = ''){
	echo '
<table class="tab22">
	<tr>
		<td>
			Prepared by:
			'.$name1.'
		</td>
		<td>
			Checked / Verified by:
			<br /><br />
			'.REP_SIGN1.'
			<br /><br />
			'.REP_SIGN1_TITLE.'
		</td>
		<td>
			Noted:
			<br /><br />
			'.WD_MANAGER.'
			<br /><br />
			'.WD_MANAGER_RA.'
		</td>
	</tr>
</table>

<style>
.tab22 td{
 border:0;
}
.tab22{
 width:100%;
 border:0;
 margin-top:15px;
}
</style>


	';
}




function curr_bill_v3($acct_type, $date01, $zone_id, $beg1)
{
	$new_arr2 = array(
				 'm1' => '- 1 Month',
				 'm2' => '- 2 Month',
				 'm3' => '- 3 Month',
				 'm4' => '- 4 Month',
				 'm5' => '- 5 Month',
				 'm6' => '- 6 Month',
				 'y1' => '- 1 Year'
				);


	$d1 = date('Y-m-28', strtotime($date01));
	$d2 = date('Y-m-01', strtotime($d1.' '.$new_arr2[$beg1]));

	$month1  = date('Y-m', strtotime($date01));
	$period  = date('Y-m-01', strtotime($date01));

	$bill_start = date('Y-m-d',strtotime($month1.'-'.$zone_id));
	$bill_end   = date('Y-m-d',strtotime($date01));
	$bill_date_start = date('Y-m-d',strtotime($d2));


	$func11 = function($qq1)use($bill_date_start, $bill_end){
						$qq1->where('date01', '>=', $bill_date_start);
						$qq1->where('date01', '<=', $bill_end);
						$qq1->where(function($qq2){
								$qq2->where('led_type','beginning');
								$qq2->orWhere('led_type','adjustment');
								$qq2->orWhere('led_type','billing');
								$qq2->orWhere('led_type','penalty');
								$qq2->orWhere('led_type','payment');
								$qq2->orWhere('led_type','payment_cancel');
								$qq2->orWhere('led_type','payment_cr');
								$qq2->orWhere('led_type','cancel_cr');
								$qq2->orWhere('led_type','witholding');
							});

						$qq1->where(function($q2){
									$q2->where('status','active');
							  });
					};

	$acct1 = Accounts::where('acct_type_key', $acct_type)
				->whereHas('ledger_data6', $func11)
					->where('zone_id', $zone_id)
						->where(function($query){
							$query->where('acct_status_key', '2');
							$query->orWhere('acct_status_key', '3');
						})
						->with(['ledger_data6' => $func11])
							->get();

	$acct_discon = Accounts::where('acct_type_key', $acct_type)
				->whereHas('ledger_data6', $func11)
					->where('zone_id', $zone_id)
						->where(function($query){
							$query->where('acct_status_key', '4');
							$query->orWhere('acct_status_key', '5');
						})
						->with(['ledger_data6' => $func11])
							->get();



	//~ echo $zone_id;
	//~ die();

	//~ echo '<pre>';
	//~ echo $bill_date_start;
	//~ echo '<br />';
	//~ echo $bill_end;
	//~ echo '<br />';
	//~ print_r($acct1->toArray());
	//~ die();



	$ttl_ALL = 0;
	$res11 = array();

	foreach($acct1 as $aa)
	{

		//~ $ttl_ALL += $bill1 = $aa->ledger_data6->sum('billing');
		//~ $ttl_ALL += $pen11 = $aa->ledger_data6->sum('penalty');
		//~ $ttl_ALL -= $pay11 = $aa->ledger_data6->sum('payment');
		//~ $ttl_ALL -= $dis11 = $aa->ledger_data6->sum('discount');
		//~ $ttl_ALL -= $adj11 = $aa->ledger_data6->sum('bill_adj');


		foreach($aa->ledger_data6 as $ll){
			//~ $res11[$ll->period] =  0;
		}

		foreach($aa->ledger_data6 as $ll)
		{

			@$res11[$ll->period] +=  $ll->billing;
			@$res11[$ll->period] +=  $ll->penalty;
			@$res11[$ll->period] -=  $ll->payment;
			@$res11[$ll->period] -=  $ll->discount;
			@$res11[$ll->period] -=  $ll->bill_adj;

			if($ll->led_type == 'beginning')
			{
				$res11[$ll->period] +=  $ll->arrear;
			}

			$ttl_ALL +=  $ll->billing;
			$ttl_ALL +=  $ll->penalty;
			$ttl_ALL -=  $ll->payment;
			$ttl_ALL -=  $ll->discount;
			$ttl_ALL -=  $ll->bill_adj;

			if($ll->led_type == 'beginning')
			{
				$ttl_ALL +=  $ll->arrear;
			}
		}


	}//

	//~ echo '<pre>';
	//~ echo $ttl_ALL;
	//~ echo '<br />';
	//~ print_r($res11);
	//~ die();

	return array( 'ttl'=> $ttl_ALL, 'brdown' =>$res11);
	die();


}//

function curr_bill_v2($acct_id, $date01, $zone_id, $beg1)
{
	//~ $acct_id = 58;

	$acct_id = (int) $acct_id;
	$month1  = date('Y-m', strtotime($date01));
	$period  = date('Y-m-01', strtotime($date01));

	$bill_start = date('Y-m-d',strtotime($month1.'-'.$zone_id));
	$bill_end   = date('Y-m-d',strtotime($date01));
	$bill_date_start = date('Y-m-d',strtotime($beg1));

	$acct_data = Accounts::find($acct_id);

	$billigs = LedgerData::where('acct_id', $acct_id)
					->where('status','active')

					->where('date01', '>=', $bill_date_start)
					 ->where('date01', '<=', $bill_end)
						->where(function($query){
								$query->where('led_type','beginning');
								$query->orWhere('led_type','adjustment');
								//~ $query->where('led_type','adjustment');
								$query->orWhere('led_type','billing');
								$query->orWhere('led_type','penalty');
								$query->orWhere('led_type','payment');
								$query->orWhere('led_type','payment_cancel');
								$query->orWhere('led_type','payment_cr');
								$query->orWhere('led_type','cancel_cr');
								$query->orWhere('led_type','witholding');
							})
							->where(function($q2){
									$q2->where('status','active');
								})

								//~ ->with('acct')
								->orderBy('zort1', 'asc')
								->orderBy('id','asc')
									->get();

	$bill_last = LedgerData::where('acct_id', $acct_id)
					 ->where('date01', '<', $bill_date_start)
						->where(function($q2){
								$q2->where('status','active');
							})
							->where('status','active')
							->orderBy('zort1', 'desc')
							->orderBy('id','desc')
								->first();



	$pre_bill = array();
	foreach($billigs as $bb)
	{
		$pre_bill[$bb->period] = 0;
	}

	$ttl_coll = 0;
	foreach($billigs as $bb)
	{
		//~ echo $bb->billing;
		//~ echo '<br />';
		//~ echo '<br />';
		//~ echo '<br />';

		$pre_bill[$bb->period] += (float) $bb->billing;
		$pre_bill[$bb->period] += (float)  $bb->penalty;
		$pre_bill[$bb->period] -= (float)  $bb->bill_adj;
		$pre_bill[$bb->period] -= (float)  $bb->discount;

		if($bb->led_type == 'billing'){

		}

		if($bb->led_type == 'beginning'){
			$pre_bill[$bb->period] += (float)  $bb->arrear;
		}

		if($bb->led_type == 'witholding'){
			$pre_bill[$bb->period] -= (float)  $bb->discount;
		}

		$ttl_coll += (float)  $bb->payment;
	}


	if($bill_last){
		$pre_bill = (array('last1'=> $bill_last->ttl_bal) + $pre_bill);
		//~ echo 'test';
		//~ echo '<pre>';
		//~ array_unshift($pre_bill);
		//~ print_r($bill_last->toArray());
		//~ print_r($pre_bill);
		//~ echo $bill_date_start;
		//~ die();
	}



	foreach($pre_bill as $kk=>$vv)
	{
		//~ break;
		if($ttl_coll <= 0){
			continue;
		}

		$rr = $vv - $ttl_coll;

		if($rr<=0){
			$ttl_coll -= $vv;
			$pre_bill[$kk] = 0;

			if($vv < 0){
				$pre_bill[$kk] = $vv;
			}

		}else{
			$pre_bill[$kk] = $rr;
			break;
		}
	}

	//~ if($acct_data->acct_no == '113200190'){
		//~ echo $ttl_coll;
		//~ echo '<pre>';
		//~ print_r($pre_bill);
		//~ print_r($billigs->toArray());
		//~ die();
	//~ }

	return $pre_bill;

	echo $ttl_coll;
	echo '<pre>';
	echo $bill_date_start;
	echo '<br />';
	echo $bill_end;
	echo '<br />';

	print_r($pre_bill);
	print_r($billigs->toArray());
	die();


}//

function curr_bill_v1($acct_id, $date01, $zone_id, $beg1)
{

	//~ echo $beg1;
	//~ die();

	$acct_id = (int) $acct_id;
	$month1  = date('Y-m', strtotime($date01));
	$period  = date('Y-m-01', strtotime($date01));

	$bill_start = date('Y-m-d',strtotime($month1.'-'.$zone_id));
	$bill_end   = date('Y-m-d',strtotime($date01));


	$bill_date_start = date('Y-m-d',strtotime($beg1));

	$sql1 = "
		SELECT
			BM.id,
			BM.period,
			BM.curr_bill,
			BAM.amount as ttl_adj,
			BAM.date1,
			BD.due1 as penalty
		FROM
			billing_mdls AS BM
		LEFT JOIN
			billing_adj_mdls AS BAM ON (BAM.acct_id = BM.account_id)
		LEFT JOIN
			billing_dues as BD ON (BD.bill_id = BM.id AND due_date <= '$bill_end' AND  due_date >= '$beg1')
		WHERE
			BM.account_id ='$acct_id'
			AND
			BM.bill_date >= '$bill_date_start'
			AND
			BM.bill_date <= '$bill_end'
		ORDER BY BM.period ASC
	";


	$resu1 = DB::select($sql1);


	return $resu1;
	//~ echo '<pre>';
	//~ echo $sql1;
	//~ print_r($resu1);
	//~ die();

}//


function ageing_get_current_led_by_type($dat1, $zone, $typ1, $stat=2, $ttl_allx=false)
{

	$dat1 = date('Y-m-d', strtotime($dat1));
	$period1 = date('Y-m', strtotime($dat1));
	$date_start = date('Y-m-d', strtotime($period1.'-'.$zone));
	$period1 = $period1.'-01';


	$zone = (int) @$zone;


	$act1_active = Accounts::where('accounts.zone_id', $zone)
						->where('accounts.status', '!=', 'deleted')

						->where(function($qq){
							$qq->where('accounts.acct_status_key', 2);
							$qq->orWhere('accounts.acct_status_key', 3);
						})

						->where('accounts.acct_type_key', $typ1)

						->leftJoin('billing_adj_mdls as BAM', function($join)use($dat1){
							$join->on('BAM.acct_id','=', 'accounts.id');
							$dat2 = date('Y-m-01',strtotime($dat1));
							$join->where('BAM.date1', '<=', $dat1);
							$join->where('BAM.date1', '>=', $dat2);
						})
						->leftJoin('ledger_datas', function($join)use($dat1){
							$dat2 = date('Y-m-01',strtotime($dat1));
							$join->on('ledger_datas.acct_id','=', 'accounts.id');
							$join->where('ledger_datas.date01', '<=', $dat1);
							$join->where('ledger_datas.date01', '>=', $dat2);
							$join->where('ledger_datas.led_type','billing');
						})
						->select(DB::raw('accounts.*, SUM(BAM.amount) as DD, SUM(ledger_datas.billing) as EE'))
						->get();

	$DD = @$act1_active->sum('DD');
	$ttl_bal = @$act1_active->sum('EE');
	$ttl_bal -= $DD;

	return $ttl_bal;
}//



function ageing_date_diff()
{
		return array(
					 'm1' => '- 1 Month',
					 'm2' => '- 2 Month',
					 'm3' => '- 3 Month',
					 'm4' => '- 4 Month',
					 'm5' => '- 5 Month',
					 'm6' => '- 6 Month',
					 'y1' => '- 1 Year',
				    );

}//


function ageing_date_breakdown($month)
{
		$new_arr = array(
					'm1' => '30 Days',
					'm2' => '60 Days',
					'm3' => '90 Days',
					'm4' => '120 Days',
					'm5' => '150 Days',
					'm6' => '180 Days',
					'y1' => '360 Days',
				   );

		$new_xx = array();
		$stop1 = 0;

		foreach($new_arr as $kk=>$vv)
		{
			if($kk == $month){$stop1 = 1;}
			$new_xx[$kk]=$vv;
			if($stop1 == 1)break;
		}

		//~ echo '<pre>';
		//~ print_r($new_xx);
		//~ die(0);

		$new_arr = $new_xx;
		return $new_arr;
}//


function ageing_body1($dd)
{
	extract($dd);
	$dd33 = array_keys($dd);
	//~ $extra1  = 5;
	

	$A1 = trim($v1->acct_no).' '.trim($v1->fname).' '.trim($v1->lname);
	$A1 = substr($A1,0,27);

	$A2 = ' ';
	$A3 = ' ';
	$A4 = ' ';
	$A5 = ' ';

	$TTL_1 = 0;

	$age1 = ageing_breakdown1($dat1, $v1);

	//~ echo '<pre>';
	//~ print_r($age1);
	//~ die();

	//~ $A3 = $age1[0]==0?'':number_format($age1[0],2);
	//~ $A3_ttl += $age1[0];
	//~ $TTL_1  += $age1[0];

	$beg1 = date('Y-m-'.$zone, strtotime($dat1.' '.@$new_arr2[$month]));
	$last_dat = date('Y-m-'.$zone, strtotime($dat1));
	$last_dat = date('Y-m-01', strtotime($last_dat));

	//~ echo $last_dat;
	//~ die();

	//~ echo '<pre>';
	//~ print_r($v1);
	//~ print_r($dat1);
	//~ echo '<br />';
	//~ print_r($beg1);
	//~ die();


	$period1_dd = date('Y-m-01', strtotime($dat1));
	$read_period_data = ReadingPeriod::where('period', $period1_dd)->first();
	$due_dates_raw = @$read_period_data->due_dates;
	$due_dates = json_decode($due_dates_raw, true);

	$m1 = $due_dates[$zone];
	$m1 = str_replace('|','',$m1);
	$m1 = explode('@',$m1);
	$m1 = @$m1[1];

	$has_current = false;


	//~ echo '<pre>';
	//~ print_r($dat1);
	//~ die();



	$my_curr_bill = curr_bill_v2($v1->id, $dat1, $zone, $beg1);

	//~ die();

	$A3 = (float) @$my_curr_bill[$last_dat];
	unset($my_curr_bill[$last_dat]);


	$t1 = strtotime($m1);//DUE DATE
	$t2 = strtotime($dat1);//CURRENT DATE

	if($t1 >= $t2){
		//~ $dat2 = date('Y-m-01', strtotime($dat1));
		//~ $dat2 = date('Y-m-d', strtotime($dat1.' - 1 day'));
		$A3 = 0;
	}



	//~ if($has_current == false){
		//~ $A3 = 0;
	//~ }

	//~ echo '<pre>';
	//~ echo ($A3);
	//~ echo print_r($my_curr_bill);
	//~ die();

	Fpdf::Cell(50+$extra1,$cell_height, htmlspecialchars($A1),'',0,'L', false);
	Fpdf::Cell(10+$extra1,$cell_height, $A2,'',0,'R', false);

	$A3_ttl += $A3;
	$TTL_1  += $A3;



	Fpdf::Cell(20+$extra1,$cell_height, $A3==0?'':number_format($A3,2).'','',0,'R', false);

	$dat1_22 = date('Y-m-28',strtotime($dat1));

	$ind2 = 0;
	foreach($new_arr as $kk => $vv):
		$per_xx1 = date('Y-m-01', strtotime($dat1_22.' '.@$new_arr2[$kk]));
		$A4 = (float) @$my_curr_bill[$per_xx1];
		Fpdf::Cell(20+$extra1,$cell_height, $A4==0?'':number_format($A4,2),'',0,'R', false);
		//~ Fpdf::Cell(20,$cell_height, $per_xx1,'',0,'R', false);
		$A4_ttl[$ind2] += $A4;
		$TTL_1  += $A4;
		$ind2++;
	endforeach;

	$A4 = 0;
	@$A4_ttl[$ind2] += $A4;
	if(!empty(@$my_curr_bill['last1'])){
		$A4 = $my_curr_bill['last1'];
		$A4_ttl[$ind2] += $A4;
		$TTL_1  += $A4;
	}

	Fpdf::Cell(20+$extra1,$cell_height, $A4==0?'':number_format($A4,2),'',0,'R', false);

	if($TTL_1 != 0){
		$TTL_WITH_AGEING++;
		$SUM1_ttl += $TTL_1;
		$GRAND_TTL += $TTL_1;
		//~ $A5 = $TTL_1==0?'':number_format($TTL_1,2);
	}

	$A5 = $v1->ttl_bal==0?'':number_format($v1->ttl_bal,2);

	Fpdf::Cell(30+$extra1,$cell_height, $A5,'',0,'R', false);
	Fpdf::Ln();

	if($ind1  >= $ind_max){
		$ind1 = 0;
		//~ $ind_max = 70;
		$ind_max = 54;

		ageing_foo1($new_arr, compact($dd33));

		Fpdf::Ln();

		Fpdf::AddPage('P', 'Letter');
		Fpdf::SetMargins(6, 5, 5);

		ageing_head11($new_arr, $con_stat, compact('curr_date_label'));
	}

	return compact($dd33);

}//


function ageing_sub_total($dd)
{
	extract($dd);
	Fpdf::Ln();
	Fpdf::Ln();
	
	//~ $extra1 = 5;

	Fpdf::Cell(50+$extra1,5, ' ','',0,'L', false);
	Fpdf::Cell(10+$extra1,5, ' ','',0,'R', false);
	Fpdf::Cell(20+$extra1,5, ' ','',0,'R', false);
	$ind = 0;
	foreach($new_arr as $kk => $vv):
		Fpdf::Cell(20,5, '','',0,'R', false);
		$ind ++;
	endforeach;
	Fpdf::Cell(20+$extra1,5, $acct_stat11.'  ','TB',0,'R', false);
	Fpdf::Cell(30+$extra1,5,''.number_format(@$SUM1_ttl,2),'TB',0,'R', false);
	Fpdf::Ln();
	Fpdf::Ln();
}


function ageing_foo1($new_arr, $dd)
{
	extract($dd);
	//~ print_r($dd);
	//~ die();
	
	//~ $extra1 = 5;

	$A3_ttl  = number_format($A3_ttl,2);

	Fpdf::Ln();
	//~ Fpdf::SetFont('Courier',null, 7);

	Fpdf::Cell(50+$extra1,5, 'Page '.Fpdf::PageNo().' of {nb}','T',0,'L', false);
	Fpdf::Cell(10+$extra1,5, ' ','T',0,'R', false);
	Fpdf::Cell(20+$extra1,5, $A3_ttl,'T',0,'R', false);
	$ind = 0;
	foreach($new_arr as $kk => $vv):
		$gg1 = number_format($A4_ttl[$ind],2);
		Fpdf::Cell(20+$extra1,5, $gg1,'T',0,'R', false);
		$ind ++;
	endforeach;
	$gg1 = number_format($A4_ttl[$ind],2);
	Fpdf::Cell(20+$extra1,5,$gg1,'T',0,'R', false);
	Fpdf::Cell(30+$extra1,5,number_format((float)@$GRAND_TTL,2),'T',0,'R', false);
	Fpdf::Ln();
}


function ageing_foo1_final($new_arr, $dd)
{
	extract($dd);

	$A3_ttl  = number_format($A3_ttl,2);
	//~ $extra1 = 5;
	

	Fpdf::Ln();
	//~ Fpdf::SetFont('Courier',null, 7);

	Fpdf::Cell(50+$extra1,5, @$zone_name.' ','T',0,'L', false);
	Fpdf::Cell(10+$extra1,5, $TTL_WITH_AGEING.'  '.' ','T',0,'R', false);
	Fpdf::Cell(20+$extra1,5, $A3_ttl,'TB',0,'R', false);
	$ind = 0;
	foreach($new_arr as $kk => $vv):
		$gg1 = number_format($A4_ttl[$ind],2);
		Fpdf::Cell(20+$extra1,5, $gg1,'TB',0,'R', false);
		$ind ++;
	endforeach;
	$gg1 = number_format($A4_ttl[$ind],2);
	Fpdf::Cell(20+$extra1,5,$gg1,'BT',0,'R', false);
	Fpdf::Cell(30+$extra1,5,number_format((float)@$GRAND_TTL,2),'BT',0,'R', false);
	Fpdf::Ln();
	/*---------------------*/
	/*---------------------*/
	/*---------------------*/
	/*---------------------*/
	Fpdf::Ln();
	Fpdf::Cell(50+$extra1,5, 'Grand Total: '.$TTL_WITH_AGEING.' / '.$TTL_ALL_AGEING,'B',0,'L', false);
	Fpdf::Cell(10+$extra1,5, ''.'  '.' ','B',0,'R', false);
	Fpdf::Cell(20+$extra1,5, $A3_ttl,'B',0,'R', false);
	$ind = 0;
	foreach($new_arr as $kk => $vv):
		$gg1 = number_format($A4_ttl[$ind],2);
		Fpdf::Cell(20+$extra1,5, $gg1,'B',0,'R', false);
		$ind ++;
	endforeach;
	$gg1 = number_format($A4_ttl[$ind],2);
	Fpdf::Cell(20+$extra1,5,$gg1,'B',0,'R', false);
	Fpdf::Cell(30+$extra1,5,number_format((float)@$GRAND_TTL,2),'B',0,'R', false);
	Fpdf::Ln();
	/*---------------------*/
	/*---------------------*/
	/*---------------------*/
	/*---------------------*/
	Fpdf::Ln();
	Fpdf::Cell(60,5, 'Prepared By: ','',0,'L', false);
	Fpdf::Cell(60,5, 'Checked/Verified by:'.'  '.' ','',0,'L', false);
	Fpdf::Cell(60,5, 'Noted'.'  '.' ','',0,'L', false);

	Fpdf::Ln();
	Fpdf::Cell(60,4, REP_SIGN3,'',0,'L', false);
	Fpdf::Cell(60,4, REP_SIGN2.'  '.' ','',0,'L', false);
	Fpdf::Cell(60,4, WD_MANAGER.'  '.' ','',0,'L', false);
	Fpdf::Ln();
	Fpdf::Cell(60,3, REP_SIGN3_TITLE,'',0,'L', false);
	Fpdf::Cell(60,3, REP_SIGN2_TITLE.'  '.' ','',0,'L', false);
	Fpdf::Cell(60,3, WD_MANAGER_RA.'  '.' ','',0,'L', false);
	Fpdf::Ln();


}

function ageing_foo1_signature()
{
}




function ageing_head11($new_arr, $stat='Active', $dd=array())
{

	//~ var_dump($dd);
	//~ die();
	extract($dd);
	$extra1  = 5;

	if(!@$is_first)
	{
		Fpdf::write(4, 'Ageing of Receivables');
		Fpdf::Ln();
		Fpdf::write(4, 'As of '.@$curr_date_label);
		Fpdf::Ln();
	}

	Fpdf::write(6, 'STATUS : '.$stat);
	Fpdf::Ln();

	Fpdf::SetFont('Courier',null, 10);
	Fpdf::Cell(50+$extra1,5, '','B',0,'L', false);
	Fpdf::Cell(10+$extra1,5, '','B',0,'R', false);
	Fpdf::Cell(20+$extra1,5, 'Current','B',0,'R', false);

	foreach($new_arr as $kk => $vv):
	Fpdf::Cell(20+$extra1,5, $vv,'B',0,'R', false);
	endforeach;

	Fpdf::Cell(20+$extra1,5, '>'.@$vv,'B',0,'R', false);
	Fpdf::Cell(30+$extra1,5, 'WB Total','B',0,'R', false);
	Fpdf::Ln();
}//


function ageing_breakdown1($d0, $acct)
{
	$d1 = date('Y-m-d', strtotime($d0));
	$d2 = date('Y-m-'.$acct->zone_id, strtotime($d0));
	//~ $d2 = date('Y-m-d', strtotime($d2.'-1 day'));
	$d2 = date('Y-m-01', strtotime($d1.''));

	//~ echo $d2;
	//~ die();

	$led1 = LedgerData::
				where('date01','<=',$d1)->
				where('date01', '>=', $d2)->
				where('acct_id', $acct->id)
				->where(function($q){
						$q->where('led_type', 'billing');
						$q->orWhere('led_type', 'beginning');
						//~ $q->orWhere('led_type', 'payment');
					})
				->where('status','active')
				->orderBy('zort1', 'desc')
				->orderBy('id', 'desc')
					->first();

	//~ echo '<pre>';
	//~ print_r($led1->toArray());
	//~ die();

	$led2 = LedgerData::where('date01','<=',$d2)
				->where('acct_id', $acct->id)
				->where(function($q){
						$q->where('led_type', 'billing');
						$q->orWhere('led_type', 'beginning');
						//$q->orWhere('led_type', 'payment');
					})
				->where('status','active')
				->orderBy('zort1', 'desc')
				->orderBy('id', 'desc')
					->first();

	$ret1 = array(0,0,0);

	$d1_t = date('Y-m-d',strtotime(substr($d0,0,10).' + 1 day'));

	$ttl_col_raw = Collection::
				where(function($query){
					$query->where('status', 'active');
					$query->orWhere('status', 'collector_receipt');
				})
				->where('payment_date','<', $d1_t);

	//~ echo $d1_t;
	//~ die();

	//~ $ttl_col = 0;
	//~ echo '<pre>';
	//~ print_r($ttl_col_raw->get()->toArray());
	//~ echo $d1_t;
	//~ die();


	if($led1)
	{
		//~ $ttl_col_raw->where('collection_type', 'bill_payment');
		$ttl_col2 = $ttl_col_raw->where('cust_id', $led1->acct_id)->sum('arrear1');

		//~ $ttl_col = $ttl_col_raw->where('billing_id', $led1->reff_no)->sum('bill1');
		$ttl_col = $ttl_col_raw->where('cust_id', $led1->acct_id)->sum('bill1');


		$ret1[0] = $led1->billing - $ttl_col;

		if($led1->led_type == 'beginning'){
			$ret1[0] = $led1->arrear - $ttl_col2;
		}else{
			$ttl_adjustment = BillingAdjMdl::where('adj_period', $led1->period)
							->where('acct_id', $led1->acct_id)
								->sum('amount');

			$ret1[0] -= $ttl_adjustment;
		}
		//~ $ret1[0] -= $ttl_col;
	}

	if($led2)
	{
		$ttl_col2 = $ttl_col_raw->where('cust_id', $led2->acct_id)->sum('arrear1');
		$ttl_col = $ttl_col_raw->where('cust_id', $led2->acct_id)->sum('bill1');

		$ret1[2] = $led2->ttl_bal - ($ttl_col + $ttl_col2);

		if($led2->led_type == 'beginning'){
			$ret1[2] = $led2->arrear - $ttl_col2;
		}else{
			$ttl_adjustment = BillingAdjMdl::where('adj_period', $led2->period)
							->where('acct_id', $led2->acct_id)
								->sum('amount');
			$ret1[2] -= $ttl_adjustment;
		}

	}//

	return $ret1;

}//


function bill_payment_breakdown1($bill, $curr_pay)
{

	if(!$bill){return false;}

	$bill->arrear2;
	$bill->due3;
	$bill->collection_total;
	$bill->senior1;
	$bill->account2;


	$ttl_payed  = (float) @$bill->collection_total->total_payed;
	//~ $ttl_payed  = 500;
	$current_payment = $curr_pay;


	$break1 = array();
	$break1[0] = 0;//Arrear
	$break1[1] = $bill->curr_bill;//Billing
	$break1[2] = 0;//Penalty
	//~ $break1[3] = 0;//Senior


	if($bill->arrear2)
	{
		$break1[0] = $bill->arrear2->amount;
	}

	if($bill->due3)
	{
		$break1[2] = $bill->due3->due1;
	}

	//~ if($bill->senior1)
	//~ {
		//~ $break1[3] = $bill->senior1->amount;
	//~ }

	if($bill->account2->acct_discount == SENIOR_ID)
	{
		$break1[1] -= round($bill->curr_bill * 0.05, 2);
	}

	//~ echo '<pre>';
	//~ print_r($bill->account2->acct_discount);
	//~ echo 'AAA';


	$break2 = array();

	foreach($break1 as  $kk=> $vv)
	{
		$def1 = $vv - $ttl_payed;
		if($def1 > 0){
			$break2[$kk] = $ttl_payed;
			break;}
		$ttl_payed -= $vv;
		$break2[$kk] = $vv;
	}

	$break3 = $break1;
	$break3[0] -= @$break2[0];
	$break3[1] -= @$break2[1];
	$break3[2] -= @$break2[2];


	$ttl_payed = $current_payment;

	$break4 = array();
	foreach($break3 as  $kk=> $vv)
	{
		$def1 = $vv - $ttl_payed;
		if($def1 > 0){
			$break4[$kk] = $ttl_payed;
			break;}
		$ttl_payed -= $vv;
		$break4[$kk] = $vv;
	}


	return array(
		'arr11' => @$break4[0],
		'bill11' => @$break4[1],
		'pena11' => @$break4[2],
		'seni11' => @$break4[3],
	);

	return $break4;

	echo '<pre>';
	echo $def1;
	echo '<br />';
	print_r($break2);
	print_r($break3);
	print_r($break4);

}//


function billing_info_by_bill_id($bill_id, $acct_id, $col_id)
{
	$billing1 = BillingMdl::where('id',$bill_id)
					->with('arrear2')
						->first();

	$cbill = 0;
	$arr1  = 0;

	if(!$billing1)
	{
		$arrear3 = getArrearV3($acct_id);
		if($arrear3)
		{
			$arr1 = (float) $arrear3->amount;
		}
	}else{
		$cbill = (float) $billing1->curr_bill;
		$arr1  = (float) $billing1->arrear2->amount;
	}

	$ttl_bill_pay_raw = Collection::where('billing_id',$bill_id)
						->where(function($query){
							$query->where('status', 'active');
							$query->orWhere('status', 'collector_receipt');
						})
						->where('collection_type', 'bill_payment');

	$ttl_bill_pay = $ttl_bill_pay_raw->where('id','<=', $col_id)->sum('payment');
	$ttl_bill_pay_prev = $ttl_bill_pay_raw->where('id','<', $col_id)->sum('payment');

	return compact('cbill', 'arr1', 'ttl_bill_pay', 'ttl_bill_pay_prev');
}//


function inv_stat1($inv)
{
	$tax_val = Collection::where('invoice_num', $inv)
				->orderBy('id', 'desc')
					->first();
	return $tax_val;
}

function wtax_val1($bill_id)
{
	$tax_val = Collection::where('billing_id', $bill_id)
				->where(function($query){
					$query->where('status', 'active');
					$query->orWhere('status', 'collector_receipt');
				})
				->sum('tax_val');
	return $tax_val;
}//

function get_account_by_id($acct_id)
{
	return Accounts::find($acct_id);

}

function get_latest_billing_V1($acct_id, $coll_date=null)
{
	return LedgerData::where('acct_id', $acct_id)
				->where(function($query){
					$query->where('led_type', '=', 'billing');
					$query->orWhere('led_type', '=', 'beginning');
					$query->orWhere('led_type', '=', 'adjustment');
				})
				->where('status','active')
				->orderBy('zort1', 'desc')
				->orderBy('id', 'desc')
					->first();
}//

function get_ttl_payment_V1($acct_id, $id1, $id2)
{
	 return LedgerData::where('acct_id', $acct_id)
				->where(function($query){
					$query->where('led_type','payment');
					$query->orWhere('led_type','payment_cancel');
				})
				->where('status','active')
				->whereBetween('id',[$id1, $id2])
				->sum('payment');
}//


function get_penalty_counts($period1, $zone_id, $pen_date)
{
	//~ echo $pen_date;

	$per1 = date('Y-m-01', strtotime($period1));

	$results = DB::select("

			SELECT

				COUNT(tab2.id) as ttl1

			 FROM (
				SELECT LD1.id,LD1.acct_id,LD1.led_type,LD1.acct_num, MAX(LD1.id) as last_id
				  FROM ledger_datas as LD1
					LEFT JOIN accounts
					ON accounts.id = LD1.acct_id
			       WHERE LD1.led_type!='beginning' AND LD1.date01 < ? AND LD1.date01 >= ?
						AND accounts.zone_id = ?

			        GROUP BY acct_id
			 ) as tab1

			 LEFT JOIN ledger_datas as tab2
			  ON tab2.id=tab1.last_id



			 WHERE
					tab2.ttl_bal > 0
				AND
					tab2.ttl_bal IS NOT NULL


	", [$pen_date, $per1, $zone_id]);


	$total_penelized = BillingMdl::where('period', $period1)
					->whereHas('account', function($query)use($zone_id){
							$query->where('zone_id', $zone_id);
						})
						->where('due_stat', 'has-due')
						->count();




	return array('tobe_penalize' => $results[0]->ttl1, 'penalized' =>$total_penelized);

	echo '<pre>';
	print_r($results);
	die();

}//

function get_penalized_count(){
}


function wala_111()
{
	//due2
	$bill1 = BillingMdl::where('period', $period1)
				->whereNull('due_stat')
				->with(['ledger12' => function($query){
					$query->where('led_type','!=', 'beginning');
					$query->orderBy('id', 'desc');
				 }])
				 ->limit(10)
				  ->get();
	echo '<pre>';
	print_r($bill1->toArray());
	die();

}


function get_reading_period()
{
	$read_period =
			ReadingPeriod::orderBy('period', 'desc')
				->limit(20)
				->get();

	return $read_period;
}//

function get_penalty_date_V1($period1, $zone_id)
{
	$bill = BillingMdl::whereHas('account2', function($query)use($zone_id){
					$query->where('zone_id', $zone_id);
				})
				->where('period', $period1)
				->whereNotNull('penalty_date')
				->orderBy('id','desc')
				->first();

	$ret_date = '';

	if($bill){
		$ret_date = $bill->penalty_date;
	}

	return 	$ret_date;
}//

function get_billable_info($period1, $zone_id)
{
		$billable_raw = Accounts::where('zone_id', $zone_id)
						->where(function($query){
							$query->where('acct_status_key', 2);
							$query->orWhere('acct_status_key', 3);
							//~ $query->orWhere('acct_status_key', 4);
						})
						->whereHas('reading1', function($query)use($period1){
							$per1 = date('Y-m', strtotime($period1));
							$query->where('period', 'like', $per1.'%');
							$query->where('curr_reading', '!=', 0);
							$query->whereNotNull('curr_reading');
						})
						->where('status', '!=', 'deleted');

		$unbilled_raw = Accounts::where('zone_id', $zone_id)
						->where(function($query){
							$query->where('acct_status_key', 2);
							$query->orWhere('acct_status_key', 3);
							//~ $query->orWhere('acct_status_key', 4);
						})
						->whereHas('reading1', function($query)use($period1){
							$per1 = date('Y-m', strtotime($period1));

							//~ echo $per1;
							//~ die();

							$query->where('period', 'like', $per1.'%');
							$query->where('curr_reading', '!=', 0);
							$query->whereNotNull('curr_reading');
							$query->where('bill_stat', 'unbilled');
						})
						->where('status', '!=', 'deleted');

		$billed_raw = Accounts::where('zone_id', $zone_id)
						->where(function($query){
							$query->where('acct_status_key', 2);
							$query->orWhere('acct_status_key', 3);
							//~ $query->orWhere('acct_status_key', 4);
						})
						->whereHas('reading1', function($query)use($period1){
							$per1 = date('Y-m', strtotime($period1));
							$query->where('period', 'like', $per1.'%');
							$query->where('curr_reading', '!=', 0);
							$query->whereNotNull('curr_reading');
							$query->where('bill_stat', 'billed');
						})
						->where('status', '!=', 'deleted');

	return compact('billable_raw', 'unbilled_raw', 'billed_raw');
}//


function get_total_billable_by_zone($period1, $zone_id)
{
	return Accounts::where('zone_id', $zone_id)
			->where(function($query){
				$query->where('acct_status_key', 2);
				$query->orWhere('acct_status_key', 3);
				//~ $query->orWhere('acct_status_key', 4);
			})
			->whereHas('reading1', function($query)use($period1){
				$per1 = date('Y-m', strtotime($period1));
				$query->where('period', 'like', $per1.'%');
				$query->where('curr_reading', '!=', 0);
				$query->whereNotNull('curr_reading');
			})
			->where('status', '!=', 'deleted')
			//~ ->limit(10)
			->count();
}//


function get_zone_total($period1, $zone_id)
{
	return Accounts::where('zone_id', $zone_id)
			->where(function($query){
				$query->where('acct_status_key', 2);
				$query->orWhere('acct_status_key', 3);
				$query->orWhere('acct_status_key', 4);
				$query->orWhere('acct_status_key', 5);
			})
			->whereHas('reading1', function($query)use($period1){
				$per1 = date('Y-m', strtotime($period1));
				$query->where('period', 'like', $per1.'%');
			})
			->where('status', '!=', 'deleted')
			//~ ->limit(10)
			->count();
}//


function get_reading_val($acct_id, $period)
{
	$read1 = Reading::where('period', $period)
				//~ ->where('account_number', $acct_num)
				->where('account_id', $acct_id)
				->first();

	if(!$read1){return 0;}
	return (int) $read1->curr_reading;
}

function acct_type_for_data_loading($kk)
{
	$kk = (int) @$kk;
	$arr1 = array();
	$arr1[0] = 1;//New Concession
	return @$arr1[$kk];
}

function acct_stat_for_data_loading($kk)
{
	$kk = (int) @$kk;

	$arr1 = array();
	$arr1[0] = 1;//New Concession
	$arr1[1] = 2;//Active
	$arr1[2] = 3;//For Disconnection
	$arr1[3] = 4;//For Disconnection
	$arr1[4] = 5;//For Disconnection

	return @$arr1[$kk];
}//


function get_coll_header_info($dd=null){

	$date1 = date('Y-m-d');

	if(!empty(@$_GET['trd'])){
		$date1 = date('Y-m-d', strtotime(@$_GET['trd']));
	}

	if(!empty($dd))
	{
		$date1 = date('Y-m-d', strtotime($dd));
	}

	$user = Auth::user();


	return Collection:://where('payment_date', '>=', date('Y-m-d'))
				where('payment_date', 'like', $date1.'%')
				//~ ->where('status', 'active')
				->where(function($query){
					$query->where('status', 'active');
					$query->orWhere('status', 'collector_receipt');
					$query->orWhere('status', 'or_nw');
					$query->orWhere('status', 'cr_nw');
				})
				->where('collector_id', $user->id)
				->selectRaw('
					SUM(payment) as ttl_col, COUNT(id) as ttl_trx
				')
				->first();
}//

function get_bank_list()
{
	$bank_list = Bank::where('status', 'active')
					->orderBy('bank_name', 'asc')
					->get();

	return $bank_list;
}

function get_nw_inv()
{
	$last_coll = Collection::where('collection_type', 'non_water_bill_payment')
		->orderBy('id', 'desc')->first();
	$inv_num = (int) @$last_coll->invoice_num;

	$inv_set = Invoice::where('seq_start','<=', $inv_num)
				 ->where('seq_end','>=', $inv_num)
				 ->first();

	return @$inv_set->seq_c;

}//


function get_invoice_current()
{
	$userId = Auth::id();
	//~ echo $userId;
	//~ die();

	$last_coll = Collection::
					// where('collection_type', 'bill_payment')
						where('collector_id',$userId)
						->orderBy('id', 'desc')
						->first();

	if(!$last_coll){
		return null;
	}

	$inv_num = (int) $last_coll->invoice_num;

	//~ $inv_num = '2001';

	$inv_set = Invoice::where('seq_start','<=', $inv_num)
				 ->where('seq_end','>=', $inv_num)
				 ->where('uid', $userId)
				 ->first();


	//~ echo '<pre>';
	//~ echo $inv_num;
	//~ print_r(@$inv_set->toArray());
	//~ return @$inv_set->seq_c;
	
	$inv_num++;
	return @$inv_num;
}

function getPenalty($acct_id, $period1){
	$period = date('Y-m-', strtotime($period1));
	$due1 = BillingDue::where('acct_id', $acct_id)
				->where('period','like', $period.'%')
					->first();
	return $due1;
}


function getPenaltyV2($acct_id, $date1){
	$due1 = BillingDue::where('acct_id', $acct_id)
				->where('created_at','>=', $date1)
				->orderBy('created_at', 'asc')
					->first();

	//~ echo '<pre>';
	//~ var_dump($due1);
	//~ die();
	return $due1;
}


function getArrearV1($acct_id, $period1){
	$period = date('Y-m-', strtotime($period1));
	$due1 = Arrear::where('acct_id', $acct_id)
			->where('period','like', $period.'%')
				->first();
	return $due1;
}

function getArrearV2($acct_id)
{
	$due1 = Arrear::where('acct_id', $acct_id)
				->orderBy('period', 'desc')
				->first();
	return $due1;
}

function getArrearV3($acct_id)
{
	$due1 = Arrear::where('acct_id', $acct_id)
				->orderBy('id', 'desc')
				->first();
	return $due1;
}


function getLatestBillingV1($acct_id){
	$bill1 = BillingMdl::where('account_id', $acct_id)
				->orderBy('period', 'desc')
					->first();
	return $bill1;
}
function getLatestBillingV2($acct_id){

	return LedgerData::where('acct_id', $acct_id)
			->where('led_type', 'billing')
			->where('status', 'active')
			->orderBy('period', 'desc')
			->orderBy('zort1', 'desc')
			  ->orderBy('id', 'desc')
				->first();
}



function getReadingByPeriod($acct_id, $period1){
	$period = date('Y-m-', strtotime($period1));
	$read1 = Reading::where('period', 'like', $period.'%')
				->where('account_id', $acct_id)
				->first();
	return $read1;
}

function getLatestLeger($acct_id)
{
	return LedgerData::where('acct_id', $acct_id)
			->where('status', 'active')
			->orderBy('period', 'desc')
				->orderBy('zort1', 'desc')
				  ->orderBy('id', 'desc')
				->first();

}

function getLatestLegerV2($acct_id)
{
	return LedgerData::where('acct_id', $acct_id)
			->where('status', 'active')
			//~ ->orderBy('created_at', 'desc')
			->orderBy('zort1', 'desc')
			  ->orderBy('id', 'desc')
				->first();
}//

//No Beginning
function getLatestLegerV3($acct_id)
{
	return LedgerData::where('acct_id', $acct_id)
			->where('status', 'active')
			 ->where('led_type','!=', 'beginning')
			->orderBy('zort1', 'desc')
			  ->orderBy('id', 'desc')
				->first();
}//


function getLatestAdjustment($acct_id){
	$bill1 = getLatestBillingV2($acct_id);
	if(!$bill1){return false;}
	$LD1 = LedgerData::where('acct_id', $acct_id)
			->where('led_type', 'adjustment')
			->where('status', 'active')
			->where('date01', '>', $bill1->created_at)
			->orderBy('period', 'desc')
			->sum('bill_adj');
	return $LD1;
}

//~ echo uniqid();
//~ die();

function common_data($par1 = array()){

		$status_key_active_raw  =
					AccountMetas::where('meta_type', 'account_status')
						->where('meta_code', '1')
						->first();

		 if($status_key_active_raw){
			$status_key_active = $status_key_active_raw->id;
		 }else{
			$status_key_active = 0;
		 }


		if(in_array('acct_types_lab',$par1)){
				$acct_types =
								AccountMetas::where('meta_type', 'account_type')
									->where('status', 'active')
									->orderBy('meta_name', 'asc')
									->get()
									->toArray();

					$acct_types_lab = array();
					foreach($acct_types as $att){
						$acct_types_lab[$att['id']] = $att['meta_name'];
					}
		}


		if(in_array('bill_rates',$par1)){

			$bill_rates =
						BillingMeta::where('meta_type', 'billing_rates')
								->where('status', 'active')
								->get()
								->toArray();
		}


		if(in_array('zones_lab',$par1)){

				$zones  = Zones::where('status', '!=', 'deleted')
									->orderBy('zone_name', 'asc')
									->get()
									->toArray();

					$zones_lab = array();
					foreach($zones as $att){
						$zones_lab[$att['id']] = $att['zone_name'];
					}

		}


		if(in_array('bill_dis_lab',$par1)){

				$bill_discount = BillingMeta::where('meta_type', 'billing_discount')
										->where('status', 'active')
										->get()
										->toArray();
					$bill_dis_lab = array();
					foreach($bill_discount as $att){
						$bill_dis_lab[$att['id']] = $att['meta_name'];
					}

			}

		if(in_array('hw1_requests',$par1)){

				$hw1_requests =
								HwdRequests::where('req_type','generate_billing_period_request')
										->orderBy('id', 'desc')
										->limit(30)
										->get()->toArray();

		}


		if(in_array('acct_statuses_lab',$par1)){

				$acct_statuses = AccountMetas::where('status','!=', 'deleted')
										->where('meta_type', 'account_status')
										->orderBy('meta_name', 'asc')
										->get()
										->toArray();

					$acct_statuses_lab = array();
					foreach($acct_statuses as $att){
						$acct_statuses_lab[$att['id']] = $att['meta_name'];
					}

		}


		if(in_array('reading_off',$par1)){

					$reading_off = Role::with('users')
												->with('users.hwdOfficer')
												->where('name', 'reading_officer')
												->get()
												->toArray();

						$reading_off = $reading_off[0]['users'];

						foreach($reading_off as $kk => $vv)
						{
								$curr_zones = array_filter(explode('|', $vv['hwd_officer']['zones']));
								$zones_str = array();
								foreach($curr_zones as $cc1){
										$zones_str[]  = $zones_lab[$cc1];
								}
								$vv['zones_txt'] = implode(', ', $zones_str);
								$reading_off[$kk] =  $vv;
						}

			}

		if(in_array('active_acct_count',$par1)){

				$active_acct_count = Accounts::where('acct_status_key', $status_key_active)
									->count();

		}

		if(in_array('read_accout_count',$par1)){

				$read_accout_count =
						Accounts::whereHas('reading_billed', function($query){
								$date1 = date('Y-m');
								$get_date = @$_GET['date1'];
								if(!empty($get_date)){
									$date1 = date('Y-m', strtotime($get_date));
								}
								$query->where('period', 'like', $date1.'%');
							})
							->count();
		}



		return compact($par1);


}//End Func


function	ageing_common($varx = array())
{
	$mm_arr = array(
		'm1' => '-1 Month',
		'm2' => '-2 Month',
		'm3' => '-3 Month',
		'm4' => '-4 Month',
		'm5' => '-5 Month',
		'm6' => '-6 Month',
		'y1' => '-12 Month'
	);

	$lab_days = array(
		'm1' => '30 Days',
		'm2' => '60 Days',
		'm3' => '90 Days',
		'm4' => '120 Days',
		'm5' => '150 Days',
		'm6' => '180 Days',
		'y1' => '1 Year',
	);

	$zones1  = Zones::where('status', '!=', 'deleted')->get();
	$zone_arr = array();
	foreach($zones1 as $zz)
	{
		$zone_arr[$zz->id] = $zz->zone_name;
	}

	//$zone_lbl = $zone_arr[$zone];

	return compact($varx);
}//End Subs



function pagi1($request, $per_page = 20){
	$get_data1 = @$_GET;
	$url1 = $request->url();

	$pg1 = (int) @$get_data1['pg'];
	$pg1_next = $pg1 + 1;

	if($pg1 <= 0){$get_data1['pg'] = 1;}

	$current_page = (int) $get_data1['pg'];

	$offset1 = ($current_page * $per_page) - $per_page;

	$get_data1['pg']++;
	$next_page = $url1.'?'.http_build_query($get_data1);
	$get_data1['pg']--;
	$get_data1['pg']--;
	$prev_page = $url1.'?'.http_build_query($get_data1);

	return array(
			'next_page' => $next_page,
			'prev_page' => $prev_page,
			'offset' => $offset1
		);

}//End Func



function getPeriodBill($cur_per, $period_data)
{
	foreach($period_data as $vv1)
	{
		if($vv1['period'] == $cur_per){
			$payment = 0;
			foreach($vv1['collection1'] as $vv2)
			{
				$payment+=$vv2['payment'];
			}
			return $vv1['billing_total'] - $payment;
		}
	}

	return 0;
}

function getCollection($bill_id)
{
	$collect1 = Collection::where('billing_id', $bill_id)
		->where('collection_type','bill_payment')
		->sum('payment');
	return $collect1;
}//

function getPeriodBalance($period, $acct_id)
{

	//~ echo '-----';
	//~ echo $period;
	//~ echo '-----';

	$dat11 = date('Y-m', strtotime($period));
	$bills1 = BillingMdl::where('period', 'like',$dat11.'%')
			->where('account_id', $acct_id)
			//->where('due_stat', 'has-due')
			->first();

	if(!$bills1){return 0;}

	$collect1 = Collection::where('billing_id', $bills1->id)
		->sum('payment');

	 $current_bill = $bills1->billing_total;
	 $arrears = $bills1->arrears;
	 $penalty = $bills1->penalty;
	 $discount = $bills1->discount;
	 $total_billing = (($current_bill+$penalty) - $discount);

	return  $total_billing - $collect1;
	//return $bills1->billing_total - $collect1;
}//End

function getAfterPeriodBalance($period, $acct_id)
{
	$bills1 = BillingMdl::where('period','<', $period)
			->where('account_id', $acct_id)
			//->where('due_stat', 'has-due')
			->get();

	if(empty($bills1->toArray())){return 0;}

	$total_bill_balance = 0;

	foreach($bills1 as $bb)
	{
		$collect1 = Collection::where('billing_id', $bb->id)
			->sum('payment');

		 $current_bill = $bb->billing_total;
		 $arrears = $bb->arrears;
		 $penalty = $bb->penalty;
		 $discount = $bb->discount;
		 $total_billing = (($current_bill+$penalty) - $discount);
		$total_bill_balance+= $total_billing - $collect1;
		//$total_bill_balance+= $bb->billing_total - $collect1;
	}

	return $total_bill_balance;

}//End


function date_info1()
{

	$T1 = strtotime('TODAY');

	$curr_period = date('Y-m-28');

	$mm_arr = array(
		'm1' => '-1 Month',
		'm2' => '-2 Month',
		'm3' => '-3 Month',
		'm4' => '-4 Month',
		'm5' => '-5 Month',
		'm6' => '-6 Month',
		'y1' => '-12 Month',
	);

	$lab_days = array(
		'm1' => '30 Days',
		'm2' => '60 Days',
		'm3' => '90 Days',
		'm4' => '120 Days',
		'm5' => '150 Days',
		'm6' => '180 Days',
		'y1' => '1 Year',
	);

	//$i = array_search($month, array_keys($mm_arr));
	//$new_arr = array_slice($mm_arr, 0, ($i+1));
	//$ageing_prev = array_slice($mm_arr,($i+1));

	$period_set = array();
	foreach($mm_arr as  $mm){
		$T1 = strtotime(date('Y-m-28').' '.$mm);
		$period_set[] = date('Y-m-28', $T1);
	}

	return compact('curr_period','mm_arr', 'lab_days', 'period_set');

}//End func


/********/
//FPDF  ReportGetBalances = RGB
/********/
function RGB_report_head1($cel_sp,$cel_wd)
{
	// $cel_sp = 3;
	// $cel_wd = 30;

	Fpdf::Cell($cel_wd,5,'Account Number','B',0,'L', false);
	Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
	Fpdf::Cell($cel_wd,5,'Account Name','B',0,'L', false);
	Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
	Fpdf::Cell($cel_wd,5,'Meter Number','B',0,'L', false);
	Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
	Fpdf::Cell($cel_wd,5,'A/R Others','B',0,'L', false);
	Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
	Fpdf::Cell($cel_wd  - 10,5,'Current Bill','B',0,'R', false);
	Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
	Fpdf::Cell($cel_wd - 5,5,'Balance','B',0,'R', false);
	Fpdf::Cell($cel_sp ,5,'',0,0,'L', false);
	Fpdf::Cell($cel_wd - 15,5,'Status','B',0,'L', false);
	Fpdf::Ln();

}//end Sub

function RGB_report_sub_total($dd)
{
	extract($dd);

	Fpdf::Cell($cel_wd,$cel_height,'Sub Total',0,0,'L', false);
	Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
	Fpdf::Cell($cel_wd,$cel_height,'---',0,0,'L', false);
	Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
	Fpdf::Cell($cel_wd,$cel_height,'---',0,0,'L', false);
	Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
	Fpdf::Cell($cel_wd,$cel_height,'----',0,0,'L', false);
	Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
	Fpdf::Cell($cel_wd-10,$cel_height, number_format($sub_total1[0],2),0,0,'R', false);
	Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
	Fpdf::Cell($cel_wd-5,$cel_height, number_format($sub_total1[1], 2),0,0,'R', false);
	Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
	Fpdf::Cell($cel_wd-15,$cel_height,'-',0,0,'C', false);
	Fpdf::Ln();

}



/*ReportGetByZonePDF = RGBZPDF*/

function RGBZPDF_Headers($ddd)
{
	extract($ddd);
	$h = $h+1.5;

	// Fpdf::SetXY($xy_arr[0], $y);
	// Fpdf::MultiCell($w+$w_sub[0],$h,'Acct No.','B', 'L');
	//
	// Fpdf::SetXY($xy_arr[1], $y);
	// Fpdf::MultiCell($w+$w_sub[1],$h,'Name','B', 'L');
	//
	// Fpdf::SetXY($xy_arr[2], $y-$h);
	// Fpdf::MultiCell($w+$w_sub[2],$h,'A/R Others.','B', 'C');
	//
	// Fpdf::SetXY($xy_arr[3], $y);
	// Fpdf::MultiCell($w+$w_sub[3],$h,'Current','B', 'C');
	//
	// Fpdf::SetXY($xy_arr[4], $y-$h);
	// Fpdf::MultiCell($w+$w_sub[4],$h,"30\nDays",'B', 'R');
	//
	// Fpdf::SetXY($xy_arr[5], $y-$h);
	// Fpdf::MultiCell($w+$w_sub[5],$h,"60\nDays",'B', 'R');
	//
	// Fpdf::SetXY($xy_arr[6], $y-$h);
	// Fpdf::MultiCell($w+$w_sub[6],$h,"90\nDays",'B', 'R');
	//
	// Fpdf::SetXY($xy_arr[7], $y-($h*2));
	// Fpdf::MultiCell($w+$w_sub[7],$h,"Over\n90\nDays",'B', 'R');
	//
	// Fpdf::SetXY($xy_arr[8], $y-$h);
	// Fpdf::MultiCell($w+$w_sub[8],$h,"WB\nTotal",'B', 'R');
	// Fpdf::Ln();

	$h = $h+1.5;
	Fpdf::Cell($w+$w_sub[0]+10,$h, 'Name & Acct No.','B',0,'L', false);
	//Fpdf::Cell($w+$w_sub[1],$h,'Name','B',0,'L', false);
	Fpdf::Cell($w+$w_sub[2]+5,$h,'A/R Others.','B',0,'R', false);
	Fpdf::Cell($w+$w_sub[3],$h,'Current','B',0,'R', false);

	// Fpdf::Cell($w+$w_sub[4],$h,'30 Days','B',0,'R', false);
	// Fpdf::Cell($w+$w_sub[5],$h,'60 Days','B',0,'R', false);
	// Fpdf::Cell($w+$w_sub[6],$h,'90 Days','B',0,'R', false);
	foreach($lab_days as $kk => $vv)
	{
		Fpdf::Cell($w,$h, $vv ,'B',0,'R', false);

		if($kk == $month)
		{
			break;
		}
	}

	Fpdf::Cell($w+$w_sub[7]+10,$h,'Over '.$vv,'B',0,'R', false);
	Fpdf::Cell($w+$w_sub[8],$h,'WB Total','B',0,'R', false);
	Fpdf::Ln();

}//End Sub


function RGBZPDF_Content1($ddd)
{
	extract($ddd);

	// Fpdf::SetXY($xy_arr[0], $y);
	// Fpdf::MultiCell($w+$w_sub[0],$h,$rra1->account_num,0, 'L');
	//
	// Fpdf::SetXY($xy_arr[1], $y);
	// Fpdf::MultiCell($w+$w_sub[1],$h,$rra1->full_name,0, 'L');
	//
	// Fpdf::SetXY($xy_arr[2], $y);
	// Fpdf::MultiCell($w+$w_sub[2],$h,'---',0, 'C');
	//
	// Fpdf::SetXY($xy_arr[3], $y);
	// Fpdf::MultiCell($w+$w_sub[3],$h,'---',0, 'R');
	//
	// //$rra1->ageing_data;
	// $age1 = json_decode($rra1->ageing_data);
	//
	// Fpdf::SetXY($xy_arr[4], $y);
	// Fpdf::MultiCell($w+$w_sub[4],$h,number_format($age1->m1,2),0, 'R');
	//
	// Fpdf::SetXY($xy_arr[5], $y);
	// Fpdf::MultiCell($w+$w_sub[5],$h,number_format($age1->m2,2),0, 'R');
	//
	// Fpdf::SetXY($xy_arr[6], $y);
	// Fpdf::MultiCell($w+$w_sub[6],$h, number_format($age1->m3,2),0, 'R');
	//
	// Fpdf::SetXY($xy_arr[7], $y);
	// Fpdf::MultiCell($w+$w_sub[7],$h, '-' ,0, 'R');
	//
	// Fpdf::SetXY($xy_arr[8], $y);
	// Fpdf::MultiCell($w+$w_sub[8],$h,'---',0, 'R');
	// Fpdf::Ln();

	//~ echo '<pre>';
	//~ print_r($rra1->toArray());
	//~ die();

	$data1 = (array) json_decode($rra1->data1);
	$coll  = getCollection(@$data1['billing_id']);
	//$current_remaining_bill  = $rra1->billing_total - $coll;
	//$current_remaining_bill  = $rra1->billing_total - $coll;
	$current_remaining_bill  = $rra1->billing_total;

	//~ echo '<pre>';
	//~ print_r($coll);
	//~ die();

	$adj = 2;
	$total_x  = 0;
	Fpdf::SetLeftMargin(10);
	Fpdf::SetXY(5, $y+1.7);
	Fpdf::MultiCell($w+30,$h+0.5,$rra1->full_name."\n".$rra1->account_num,0, 'L');
	//Fpdf::MultiCell($w+20,$h,$rra1->account_num."\n".$rra1->full_name,0, 'L');
	Fpdf::SetXY(10, $y+1.7);
	Fpdf::SetLeftMargin(40);
	//Fpdf::Cell($w+$w_sub[0],$h+$adj,$rra1->account_num,0,0,'L', false);
	//Fpdf::Cell($w+$w_sub[1],$h+$adj,$rra1->full_name,0,0,'L', false);
	Fpdf::Cell($w+$w_sub[2],$h+$adj,'-',0,0,'R', false);
	Fpdf::Cell($w+$w_sub[3],$h+$adj,number_format($current_remaining_bill, 2),0,0,'R', false);

	$rra1->ageing_data;
	$age1 = (array) json_decode($rra1->ageing_data);

	// Fpdf::Cell($w+$w_sub[4],$h+$adj,number_format($age1->m1,2),0,0,'R', false);
	// Fpdf::Cell($w+$w_sub[5],$h+$adj,number_format($age1->m2,2),0,0,'R', false);
	// Fpdf::Cell($w+$w_sub[6],$h+$adj,number_format($age1->m3,2),0,0,'R', false);

	foreach($lab_days as $kk => $vv)
	{

		$total_x+= @$age1[$kk];

		Fpdf::Cell($w,$h+$adj,number_format(@$age1[$kk],2),0,0,'R', false);

		if($kk == $month)
		{
			break;
		}
	}

	$more_than_total = 0;
	foreach($ageing_prev as $kk=>$vv)
	{
		$more_than_total+=@$age1[$kk];
	}
	$total_x+=$more_than_total;

	$total_x+=$current_remaining_bill;


	Fpdf::Cell($w+$w_sub[7],$h+$adj,number_format($more_than_total,2),0,0,'R', false);
	Fpdf::Cell($w+$w_sub[8]+10,$h+$adj,number_format($total_x,2),0,0,'R', false);

	Fpdf::Ln();


}// End Sub

function RGBZPDF_Sub_total($ddd)
{
	extract($ddd);

	Fpdf::SetLeftMargin(5);
	Fpdf::Ln();
	Fpdf::Cell(0,1,'','B',1,'C', false);
	Fpdf::Cell(30,5,'Sub Total',0,0,'L', false);
	Fpdf::Cell(25,5,'-',0,0,'R', false);
	Fpdf::Cell(20,5,number_format(@$current_sub, 2),0,0,'R', false);

	$total_subx1 = 0;
	foreach($new_arr as $kk => $vv){
		$total_subx1 += $sub_total[$kk];
		Fpdf::Cell(20,5,number_format($sub_total[$kk], 2),0,0,'R', false);
	}
	$over_total_x1 =0;
	foreach($ageing_prev as $kk => $vv){
		$total_subx1 += $sub_total[$kk];
		$over_total_x1+=$sub_total[$kk];
	}

	Fpdf::Cell(20,5, number_format($over_total_x1, 2),0,0,'R', false);
	Fpdf::Cell(30,5,number_format($total_subx1+@$current_sub, 2),0,0,'R', false);

}//

function RGBZPDF_Grand_total($ddd)
{
	extract($ddd);

	Fpdf::SetLeftMargin(5);
	Fpdf::Ln();
	Fpdf::Cell(0,1,'','B',1,'C', false);
	Fpdf::Ln();
	Fpdf::Cell(30,5,'Grand Total',0,0,'L', false);
	Fpdf::Cell(25,5,'-',0,0,'R', false);
	Fpdf::Cell(20,5,number_format($current_grand_total, 2),0,0,'R', false);

	$total_subx1 = 0;
	foreach($new_arr as $kk => $vv){
		$total_subx1 += $total_grand_1[$kk];
		Fpdf::Cell(20,5,number_format($total_grand_1[$kk], 2),0,0,'R', false);
	}
	$over_total_x1 =0;
	foreach($ageing_prev as $kk => $vv){
		$total_subx1 += $total_grand_1[$kk];
		$over_total_x1+=$total_grand_1[$kk];
	}

	Fpdf::Cell(20,5, number_format($over_total_x1, 2),0,0,'R', false);
	Fpdf::Cell(30,5,number_format($total_subx1+@$current_grand_total, 2),0,0,'R', false);

}//


//ReportsAccountRecievableSummary
//Reports
function  RARSummary_get_data1_new($curr_period,$zone,$acct_stat=2)
{
		//~ echo $curr_period;
		//~ echo '<br />';

		$reports = Reports::where('reports.period', $curr_period)
							->where('reports.zone_id', $zone)
							->where('reports.acct_status_id', $acct_stat)
							->leftJoin('account_metas', 'account_metas.id', '=', 'reports.acct_type_id')
							//->with('meta_name')
							->select(DB::raw('
								account_metas.meta_name,
								account_metas.id as acct_type_id,
								reports.period,
								SUM(reports.billing_total) as total_by_type_bill,
								SUM(reports.collected) as total_by_type_coll
							'))
							->groupBy('account_metas.meta_name')
							->get();

		//~ echo '<pre>';
		//~ print_r($reports->toArray());
		//~ echo '</pre>';
		//~ die();
		return $reports->toArray();


}
function  RARSummary_get_data1($curr_period,$zone,$acct_stat=2)
{

	return  RARSummary_get_data1_new($curr_period,$zone,$acct_stat);

	$acct001 = Accounts::leftJoin('account_metas', 'account_metas.id', '=', 'accounts.acct_type_key')
		->leftJoin('billing_mdls', 'billing_mdls.account_id', '=','accounts.id')
		->leftJoin(DB::raw('
			(
				SELECT
					SUM(collections.payment)as total_collected,
					collections.billing_id as coll_bill_id
				FROM collections
				GROUP BY collections.billing_id

			) as coll_tbl
		'), 'coll_tbl.coll_bill_id','=','billing_mdls.id')
		->select(DB::raw('

			account_metas.meta_name,
			account_metas.id as acct_type_id,
			billing_mdls.period,
			SUM(billing_mdls.billing_total) as total_by_type_bill,
			SUM(coll_tbl.total_collected) as total_by_type_coll

		'))
		->where('billing_mdls.period',$curr_period);

		$acct001->where('accounts.acct_status_key', $acct_stat);

		$acct001->where('accounts.zone_id', $zone);

		$xxx = $acct001->orderBy('account_metas.meta_name')
		->groupBy('account_metas.meta_name')
		->get();

	// if($acct_stat == 4){
	// 	echo '<pre>';
     //    	print_r($xxx->toArray());
	// }

	//~ echo '<pre>';
	//~ print_r($xxx->toArray());
	//~ die();

	return $xxx->toArray();
}//

function  RARSummary_get_more_thann_data1($curr_period, $acct_stat=2)
{

	$acct001 = Accounts::leftJoin('account_metas', 'account_metas.id', '=', 'accounts.acct_type_key')
		->leftJoin('billing_mdls', 'billing_mdls.account_id', '=','accounts.id')
		->leftJoin(DB::raw('
			(
				SELECT
					SUM(collections.payment)as total_collected,
					collections.billing_id as coll_bill_id
				FROM collections
				GROUP BY collections.billing_id

			) as coll_tbl
		'), 'coll_tbl.coll_bill_id','=','billing_mdls.id')
		->select(DB::raw('

			account_metas.meta_name,
			account_metas.id as acct_type_id,
			billing_mdls.period,
			SUM(billing_mdls.billing_total) as total_by_type_bill,
			SUM(coll_tbl.total_collected) as total_by_type_coll

		'))
		->where('billing_mdls.period','<',$curr_period);

		$acct001->where('accounts.acct_status_key', $acct_stat);

		$xxx = $acct001->orderBy('account_metas.meta_name')
			->groupBy('account_metas.meta_name')
			->get();

	 return $xxx->toArray();
	 echo '<pre>';
	 print_r($acct001->toArray());
	 die();
}//



function num2word($num = false)
{
    $num = str_replace(array(',', ' '), '' , trim($num));
    if(! $num) {
        return false;
    }
    $num = (int) $num;
    $words = array();
    $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
        'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
    );
    $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
    $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
        'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
        'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
    );
    $num_length = strlen($num);
    $levels = (int) (($num_length + 2) / 3);
    $max_length = $levels * 3;
    $num = substr('00' . $num, -$max_length);
    $num_levels = str_split($num, 3);
    for ($i = 0; $i < count($num_levels); $i++) {
        $levels--;
        $hundreds = (int) ($num_levels[$i] / 100);
        $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
        $tens = (int) ($num_levels[$i] % 100);
        $singles = '';
        if ( $tens < 20 ) {
            $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
        } else {
            $tens = (int)($tens / 10);
            $tens = ' ' . $list2[$tens] . ' ';
            $singles = (int) ($num_levels[$i] % 10);
            $singles = ' ' . $list1[$singles] . ' ';
        }
        $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
    } //end for loop
    $commas = count($words);
    if ($commas > 1) {
        $commas = $commas - 1;
    }
    return implode(' ', $words);
}//


/**************/
/**************/


function add_print_Hspace($cc)
{
	$spc = '';
	for($x=1;$x<=$cc;$x++)
	{
		$spc.=' ';
	}
	return $spc;
}//

function add_print_Vspace($cc)
{
	$spc = "";
	for($x=1;$x<=$cc;$x++)
	{
		$spc.="\n";
	}
	return $spc;
}//


function fwrite_stream($fp, $string) {
    for ($written = 0; $written < strlen($string); $written += $fwrite) {
        $fwrite = fwrite($fp, substr($string, $written));
        if ($fwrite === false) {
            return $written;
        }
    }
    return $written;
}



function 	pdf_heading1($rep_title = 'Daily Collection Report', $date1=null)
{
		if(empty($date1))
		{
			$date1 = date('l, F d, Y');
		}

		Fpdf::AddPage('P', 'Letter');
		Fpdf::SetMargins(5, 5, 5);
		Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Ln();
		Fpdf::Cell(100, 4, WD_NAME, 0, 0, 'L', false);
		Fpdf::Ln();
		Fpdf::write(4, WD_ADDRESS);
		Fpdf::Ln();
		Fpdf::write(4, $rep_title);
		Fpdf::Ln();
		Fpdf::write(4, $date1);
		Fpdf::Ln();
		Fpdf::Ln();

}//END pdf_heading1


function pdf_footer_signature()
{
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(20,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'Prepared by:',0,0,'L', false);
		Fpdf::Cell(90,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'Approved by:',0,0,'L', false);

		Fpdf::Ln();
		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(60,6, REP_SIGN3,'B',0,'C', false);
		Fpdf::Cell(70,6, '',0,0,'C', false);
		Fpdf::Cell(40,6, WD_MANAGER,'B',0,'C', false);

		Fpdf::SetFont('Courier','UB'); //Where "U" means underline.

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(60,6,REP_SIGN3_TITLE ,0,0,'C', false);
		Fpdf::Cell(70,6, '',0,0,'C', false);
		Fpdf::Cell(40,6, WD_MANAGER_RA,'',0,'C', false);
}//pdf_footer_signature


function get_other_payable()
{
	$ap1 = OtherPayable::where('paya_stat', 'active')->get();
	return $ap1;
}

function	   report_date_filter()
{
/*****************************/
/*****************************/
?>
 <small>Date:</small>
     <br />
     <select  class="form-control  mm_x_month">
		 <?php
		 $curr_mo  =  (int) date('m');
		 for($x=1;$x<=12;$x++){
			 $sele = '';
			 if($curr_mo == $x){
				 $sele = '  selected  ';
			 }
			 ?>
			<option value="<?php echo date('m', strtotime('2018-'.$x)); ?>"   <?php echo $sele ; ?>><?php echo date('F', strtotime('2018-'.$x)); ?></option>
		 <?php } ?>
     </select>

		<select class="form-control  mm_x_day">
			<?php for($x=1;$x<=31;$x++): ?>
			<option value="<?php echo $x; ?>"><?php echo $x; ?></option>
			<?php endfor; ?>
		</select>

      <select  class="form-control  mm_x_year">
		 <?php
		 $from1 = date('Y');
		 $to1  = $from1 - 10;
		 for($x=$from1;$x>=$to1;$x--){ ?>
			<option value="<?php echo $x; ?>"><?php echo $x; ?></option>
		 <?php } ?>
     </select>

<?php
/*****************************/
/*****************************/
}

function	   get_ledger_type()
{
	$ledger_type_list = array(
		'account1' => 'Account  application',
		'service1' => 'Service',
		'reading1' => 'Reading',
		'billing1' => 'Billing',
		'collection1' => 'Collection',
		'due1' => 'Due',
	);
}


function clean_str($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string =  preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
   $string = str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
   return $string;
}


function  zone_arr()
{
	$zz = array();

	$zz[11] = 1;//ZONE 01 BOOK 1
	$zz[12] = 2;//ZONE 01 BOOK 2
	$zz[13] = 3;//ZONE 01 BOOK 3

	$zz[21] = 4;//ZONE 02 BOOK 1
	$zz[22] = 5;//ZONE 02 BOOK 1

	$zz[31] = 6;//ZONE 03 BOOK 1
	$zz[32] = 7;//ZONE 03 BOOK 2
	$zz[33] = 9;//ZONE 03 BOOK 3

	$zz[41] = 10;//ZONE 04 BOOK 1
	$zz[42] = 11;//ZONE 04 BOOK 2
	$zz[43] = 12;//ZONE 04 BOOK 3
	$zz[44] = 13;//ZONE 04 BOOK 4
	$zz[45] = 14;//ZONE 04 BOOK 5

	$zz[51] = 15;//ZONE 05 BOOK 1
	$zz[52] = 16;//ZONE 05 BOOK 2

	$zz[61] = 17;//ZONE 06 BOOK 1
	$zz[62] = 18;//ZONE 06 BOOK 2
	$zz[63] = 19;//ZONE 06 BOOK 3

	$zz[71] = 20;//ZONE 07 BOOK 1
	$zz[81] = 21;//ZONE 08 BOOK 1

	$zz[91] = 22;//ZONE 09 BOOK 1
	$zz[92] = 23;//ZONE 09 BOOK 2

	$zz[111] = 24;//ZONE TEMP ZONE BOOK 1
	$zz[112] = 25;//ZONE TEMP ZONE BOOK 2

	$zz[121] = 26;//DISCONNECTED BOOK 1
	$zz[122] = 27;//DISCONNECTED BOOK 2



	return $zz;
}

function  status_arr()
{
	$stat = array();
	$stat[0] = 1;
	$stat[1] = 2;
	$stat[2] = 3;
	$stat[3] = 4;
	$stat[4] = 5;
	return $stat;
}

function  ctype_arr()
{
	$stat = array();
	$stat[1] = 14;//RES
	$stat[2] = 12;//GOVT
	$stat[3] = 15;//COM/IND
	$stat[4] = 9;//Comm A
	$stat[5] = 8;//Com B
	$stat[6] = 7;//Com C
	$stat[8] = 14;//RES
	return $stat;
}

function  ctype_str($tp1)
{
	$meta1 = AccountMetas::where('meta_type', 'account_type')->get();
	
	$stat = array();
	foreach($meta1 as $m1)
	{
		$stat[$m1->id] =  $m1->meta_name;
	}
	//~ $stat = array();
	//~ $stat[7] = 'Com C';
	//~ $stat[8] = 'Com B';
	//~ $stat[9] = 'Comm A';
	//~ $stat[12] = 'GOVT';
	//~ $stat[14] = 'RES';
	//~ $stat[15] = 'COM/IND';
	
	
	return @$stat[$tp1];
}




function acct_status($v)
{
	$arr1 = array();
	//$arr1[1] = 'New Concessionaire';
	$arr1[1] = 'N / C';
	$arr1[2] = 'Active';
	$arr1[3] = 'For Disconnection';
	$arr1[4] = 'Disconnected';
	$arr1[5] = 'For Reconnection';
	$arr1[6] = 'Pending';
	$arr1[15] = 'Voluntary Disconnection';
	return @$arr1[$v];
}


function request_stat101($v)
{
	$arr1 = array();
	$arr1['approved'] = '<span style="color:blue;">Approved</span>';
	$arr1['canceled'] = '<span class="rd">Denied</span>';
	$arr1['pending'] = '<span class="rd">Pending for approval</span>';
	if(empty($arr1[$v])){
		return $arr1['approved'];
		return $arr1['pending'];
	}
	return  $arr1[$v];
}

function zones_billing101($bill_req_id)
{
	$zonebilling =
		Zones::where('status', 'active')
			->with(['billzone' => function($query)use($bill_req_id){
					$query->where('bill_period_id', $bill_req_id);
			}])
			->get();

	return $zonebilling;

}//

function bill_request_list()
{
	$current_bill_request =
		HwdRequests::where('req_type','generate_billing_period_request')
			->where(function($query){
					$query->orWhere('status', 'ongoing');
					$query->orWhere('status', 'completed');
				})
			->with('bill_zone')
			->orderBy('dkey1', 'desc')
			->limit(5)
			->get();
	return $current_bill_request;

}

function cc_date($d, $mm)
{
	return date($d, strtotime($mm));
}

function bill_request_HTML()
{
	$req_list = bill_request_list();

	//~ ob_clean();
	//~ echo '<pre>';
	//~ print_r($req_list->toArray());
	//~ die();

	$arr22 = array();

	foreach($req_list as $kk => $rll)
	{
		$period_str = date('F Y',strtotime($rll->dkey1));
		$zone11 = zones_billing101($rll->id);
		$str_zz = '

		<table class="table10 table-bordered  table-hover"><tbody>

			<tr class="headings">
				<td>Period : </td>
				<td>Zone : </td>
				<td>Billing Status</td>
				<td>Due Date</td>
				<td>Due Status</td>
				<td>Actions</td>
			</tr>

		';

		//~ ob_clean();
		//~ echo '<pre>';
		foreach($zone11 as $zz)
		{

			//~ print_r($zz->toArray());

			$fine_stat = '---';
			$fine_date = '---';


			if($zz->billzone){
				if(!empty($zz->billzone->pen_stat)){
					$fine_stat = $zz->billzone->pen_stat;
					$fine_date = $zz->billzone->pen_date;
				}
			}

			$stat = empty($zz->billzone)?'UNBILLED':$zz->billzone->status;
			$act1 = empty($zz->billzone)?'Start Proccess':'Re-Proccess';

			$str_zz.='
				<tr>
					<td>'.$period_str.'</td>
					<td>'.$zz->zone_name.'</td>
					<td>'.$stat.'</td>
					<td>'.$fine_date.'</td>
					<td>'.$fine_stat.'</td>
					<td><button onclick="start_process_bill('.$zz->id.', \''.$fine_date.'\')">'.$act1.'</button></td>
				</tr>
			';
		}
		//~ die();

		$str_zz.='
		</tbody></table>

		';

		$rll->html1 = $str_zz;
		$req_list[$kk] = $rll;
	}

	return $req_list;

}//


function con_led_type($tp1)
{
	$arrr = array();
	$arrr['for_disconnection'] = '';
	$arrr['disconnected'] = '';
	$arrr['for_reconnection'] = '';
	$arrr['connected'] = '';//active
	$arrr['new_concessionaire'] = '';
	$arrr['pending'] = '';
	return  @$arrr[$tp1];
}

function con_led_type_v2($tp1)
{
	$arrr = array();
	$arrr[1] = 'new_concessionaire';
	$arrr[2] = 'connected';
	$arrr[3] = 'for_disconnection';
	$arrr[4] = 'connected';
	$arrr[5] = 'for_reconnection';
	$arrr[6] = 'pending';
	$arrr[15] = 'voluntary_disconnection';
	return  @$arrr[$tp1];
}

function con_led_type_v3($tp1)
{
	$arr1 = array();
	$arr1[1] = 'New Concessionaire';
	$arr1[2] = 'Active';
	$arr1[3] = 'For Disconnection';
	$arr1[4] = 'Disconnected';
	$arr1[5] = 'For Reconnection';
	$arr1[6] = 'Pending Approval';
	$arrr[15] = 'Voluntary Disconnection';
	return @$arr1[$tp1];
}


function get_dis_lab($disc)
{
	$disc = (int) $disc;
	$arrr = array();
	$arrr[0] = 'NONE';
	$arrr[41] = 'SENIOR CITIZEN';
	return  @$arrr[$disc];

}

function get_zone102()
{
	$zones1 = Zones::where('status', '!=', 'deleted')->orderBy('id', 'asc')->get();
	return  $zones1;
	//~ echo '<pre>';
	//~ print_r($zones1->toArray());
	//~ die();
}

function get_zone101($zone_id)
{
	$my_sone = Zones::find($zone_id);

	if(!$my_sone)
	{
		return 'NONE';
	}

	return $my_sone->zone_name;

	//~ $arrr = array();
	//~ $arrr[1] = 'Zone 1';
	//~ $arrr[2] = 'Zone 2';
	//~ $arrr[3] = 'Zone 3';
	//~ $arrr[4] = 'Zone 4';
	//~ $arrr[5] = 'Zone 5';
	//~ $arrr[6] = 'Zone 6';
	//~ $arrr[7] = 'Zone 7';
	//~ $arrr[9] = 'Temp Zone';
	//~ $arrr[8] = 'Zone 8';
	//~ return  @$arrr[$zone_id];
}//

function  overdue_startus101($stat)
{
	$arrr = array();
	$arrr[0] = '<span style="color:#FF0000;">Pending</span>';
	$arrr[1] = '<span style="color:#FFA500;">Proccessing</span>';
	$arrr[2] = '<span style="color:#008000;">Done Applied</span>';
	return  @$arrr[$stat];
}


function  part_name1($type_n)
{
	$arrr = array();
	$arrr['beginning'] = 'Beg. Balance';
	$arrr['billing'] = 'Water Bill';
	$arrr['adjustment'] = 'Adjustment';
	$arrr['penalty'] = 'Penalty';
	$arrr['payment'] = 'WB - Official Receipt';
	$arrr['payment_none_water'] = 'OR - Non-Water';
	$arrr['non_water_bill'] = 'Non-Water Bill';
	$arrr['payment_cancel'] = 'WB - Payment Cancel';
	$arrr['nw_cancel'] = 'Payment Cancel - Non-Water';
	$arrr['payment_cr'] = 'WB - Collector Receipt';
	$arrr['cancel_cr'] = 'WB - CR - Cancel';
	$arrr['cancel_cr_nw'] = 'NWB - CR - Cancel';
	$arrr['cr_nw'] = 'CR - Non-Water';
	$arrr['or_nw'] = 'OR - Non-Water';
	$arrr['wtax'] = 'W/Tax';
	$arrr['witholding'] = 'W/Tax';
	return  @$arrr[$type_n];
}//

function  receipt_name1($type_n)
{
	$arrr = array();
	$arrr['active'] = 'Official Receipt';
	$arrr['or_nw'] = 'Official Receipt - NW';
	$arrr['cr_nw'] = 'Collector Receipt - NW';
	$arrr['collector_receipt'] = 'Collector Receipt';
	$arrr['cancel_receipt'] = 'OR - Cancel';
	$arrr['cancel_cr'] = 'CR - Cancel';
	$arrr['nw_cancel'] = 'NW - Cancel';
	$arrr['cancel_cr_nw'] = 'NW - CR - Cancel';


	return  @$arrr[$type_n];
}//




function ave_reading($acct_id, $period)
{
	$limits = 2;
	$multiply = 2;

	$arr1 = array();
	for($x=1;$x<=$limits;$x++){
		$arr1[] = date('Y-m-d', strtotime($period.' - '.$x.' month'));
	}

	$get_reading1_raw  =
		Reading::where('account_id', $acct_id)
		->whereIn('period', $arr1)
		->orderBy('period', 'desc')
		->limit($limits);

	$get_reading1 = $get_reading1_raw->get();
	$total_consump = $get_reading1_raw->sum('current_consump');

	if($get_reading1->count() != $limits){
		return 0;
	}

	$total_ave = ($total_consump / $limits)  * $multiply;

	return $total_ave;
}//End


function reading_label1()
{
	$stat = (int) @$_GET['status'];
	$arr1 = array();
	$arr1[0] = 'All Result';
	$arr1[1] = 'Negative or Zero Consumption';
	$arr1[2] = 'New Accounts';
	$arr1[3] = 'Disconnected Accounts';
	$arr1[4] = 'For Reconnection Accounts';
	$arr1[5] = 'Disconnected Accounts w/ consumption';
	$arr1[6] = 'Active Accounts w/ zero consumption';
	$arr1[7] = 'Read Accounts Actice and Disconnected';
	$arr1[8] = 'Read Accounts Active Only';
	$arr1[9] = 'Read Accounts Disconnected Only';
	$arr1[10] = 'Unread Active Account Accounts';
	$arr1[11] = 'Active Accounts';
	$arr1[12] = 'Abnormal Readings';
	$arr1[13] = 'Billable Account';


	return @$arr1[$stat];
}


function all_account()
{
	$all_acc = Accounts::where('status', '!=', 'deleted')
				->orderBy('route_id', 'asc')
				//~ ->orderBy('fname', 'asc')
				->get()
				->toArray();
	return $all_acc;
}


function  meter_size_label($n1)
{
	$n1 = (int) $n1;
	if($n1<=0){$n1 = 0;}

	$mtrs = BillingMeta::where('meta_type', 'meter_size')
					->orderBy('nsort')
						->get();

	$arr1 = array();
	$arr1[0] = 'NONE';

	foreach($mtrs as $mms)
	{
		$arr1[$mms->id] = $mms->meta_name;
	}

	return $arr1[$n1];
}//




function help_collector_daily_report_heading1($label1)
{
	Fpdf::MultiCell(35,6,$label1,0 , 'L');
	Fpdf::SetLeftMargin(0);

	$x = Fpdf::GetX();
	$y = Fpdf::GetY();

	$x = Fpdf::GetX();
	$y = Fpdf::GetY();
	$x = 0;


	Fpdf::MultiCell(35,6,'Reciept Number','LTB' , 'L');
	$x += 35;
	Fpdf::SetXY($x, $y);
	Fpdf::MultiCell(50,6,'Payor','LTB' , 'C');
	$x += 50;
	Fpdf::SetXY($x, $y);
	Fpdf::MultiCell(20,3,'Amount Collected','LTB' , 'C');
	$x += 20;
	Fpdf::SetXY($x, $y);
	Fpdf::MultiCell(20,6,'Current','LTB' , 'C');
	$x += 20;
	Fpdf::SetXY($x, $y);
	Fpdf::MultiCell(20,3,'Arrears (CY)','LTB' , 'C');
	$x += 20;
	Fpdf::SetXY($x, $y);
	Fpdf::MultiCell(20,3,'Arrears (PY)','LTB' , 'C');
	$x += 20;
	Fpdf::SetXY($x, $y);
	Fpdf::MultiCell(20,3,'Meter Maint. Fee','LTB' , 'C');
	$x += 20;
	Fpdf::SetXY($x, $y);
	Fpdf::MultiCell(25,6,'Amount','LTBR' , 'C');
}//

function help_collector_daily_report_func555($cc, $CR1=false)
{
	$CR1=false;

	$OR1 = 'OR-'.$cc->invoice_num;
	$NAME1 = $cc->full_name;
	$AMOUNT_COL = number_format($cc->payment, 2);
	$AMOUNT_COL_VAL = ($cc->payment);

	$A1 = '';
	$A2 = '';
	$A3 = '';
	$A4 = '';
	$A5 = '';


	$curr_bill = $cc->bill1 + $cc->penalty1;

	$A1 = $curr_bill <= 0 ?'':number_format($curr_bill, 2);
	$A2 = $cc->arrear1 <= 0 ?'':number_format($cc->arrear1, 2);;

	$inv1 = inv_stat1($cc->invoice_num);

	if($inv1->status == 'cancel_receipt' && $CR1==false)
	{
		$NAME1 = 'CANCELED';
		$OR1 = 'OR-'.$cc->invoice_num;
		$AMOUNT_COL = '';

		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}



	if($inv1->status == 'cancel_cr'){
		$NAME1 = 'CANCELED';
		$OR1 = 'CR-'.$cc->invoice_num;
		$AMOUNT_COL = '';

		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';

		if($CR1==true)
		{
			$NAME1 = 'CANCELED - COLLECTOR\'S RECEIPT';
		}

	}

	if($inv1->status == 'collector_receipt' && $CR1==false){
		//~ $NAME1 = 'COLLECTOR RECEIPT';
		$OR1 = 'CR-'.$cc->invoice_num;
		//~ $AMOUNT_COL = '';
		//~ $A1 = '';
		//~ $A2 = '';
		//~ $A3 = '';
		//~ $A4 = '';
		//~ $A5 = '';
	}

	if($CR1 == true){
		$OR1 = 'CR-'.$cc->invoice_num;
	}

	//$NAME1
	if($cc->pay_type == 'check'){
		//~ $NAME1.=' (CHECK)';
	}

	if($inv1->status == 'or_nw' && $CR1==false){
		$OR1 .= ' (NW) ';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'cancel_cr_nw' && $CR1==false){
		$OR1 = 'CR-'.$cc->invoice_num;
		$NAME1 = 'COLLECTOR RECEIPT';
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'cancel_cr_nw' && $CR1==true){
		$OR1 = 'CR-'.$cc->invoice_num;
		$NAME1 = 'NW-CR - CANCELED';
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'nw_cancel' && $CR1==false){
		$OR1 = 'OR-'.$cc->invoice_num;
		$NAME1 = 'NW- CANCELED';
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'cr_nw' && $CR1==false){
		$OR1 = 'CR-'.$cc->invoice_num;
		$NAME1 = 'NW- COLLECTOR RECEIPT';
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'cr_nw' && $CR1==true){
		$OR1 = 'CR-'.$cc->invoice_num.' (NW) ';
	}

	$NAME1 = substr($NAME1,0,35);

	return compact(
		'NAME1',
		'OR1',
		'AMOUNT_COL',
		'A1',
		'A2',
		'A3',
		'A4',
		'A5',
		'inv1'
	);

}//



function help_collector_daily_report_func111($cc, $CR1=false)
{
	//~ extract($data1);
	$OR1 = 'OR-'.$cc->invoice_num;
	$NAME1 = $cc->accounts->acct_no.' '.$cc->accounts->lname.' '.$cc->accounts->fname;
	$AMOUNT_COL = number_format($cc->last_col->payment, 2);
	$AMOUNT_COL_VAL = ($cc->last_col->payment);


	//~ echo '<pre>';
	//~ print_r($cc);
	//~ die();

	$bill_id = (int) @$cc->billing_id;
	$acct_id = (int) @$cc->accounts->id;
	$coll_id = (int) @$cc->id;


	//~ $A1 = number_format(0, 2);
	//~ $A2 = number_format(0, 2);
	//~ $A3 = number_format(0, 2);
	//~ $A4 = number_format(0, 2);
	//~ $A5 = number_format(0, 2);

	$A1 = '';
	$A2 = '';
	$A3 = '';
	$A4 = '';
	$A5 = '';


	/*


	$bill_info  =  billing_info_by_bill_id($bill_id, $acct_id, $coll_id);
	$bal11 =  ($bill_info['arr1'] + $bill_info['cbill']) - $bill_info['ttl_bill_pay'];


	if(@$bill_info['arr1'] >= @$bill_info['ttl_bill_pay'])
	{
		if($cc->last_col->payment){
			$A2 = $cc->last_col->payment==0?'': number_format($cc->last_col->payment, 2);
		}
	}else{

		$nn1 = $bill_info['arr1'] - $bill_info['ttl_bill_pay_prev'];
		if($nn1 <= 0){$nn1 = 0;}

		$nn2 = $cc->last_col->payment - $nn1;
		$nn3 = $cc->last_col->payment - $nn2;

		$A1 = $nn2==0?'':number_format($nn2, 2);
		$A2 = $nn1==0?'':number_format($nn1, 2);

	}
	*/


	$A1 = @$cc->xxx_bill==0?'':number_format(@$cc->xxx_bill,2);
	$A2 = @$cc->xxx_arre==0?'':number_format(@$cc->xxx_arre,2);
	
	//~ echo '<pre>';
	//~ print_r($cc->penalty101);
	//~ die();


	$inv1 = inv_stat1($cc->invoice_num);

	if($inv1->status == 'cancel_receipt' && $CR1==false){
		$NAME1 = 'CANCELED';
		$OR1 = 'OR-'.$cc->invoice_num;
		$AMOUNT_COL = '';

		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}



	if($inv1->status == 'cancel_cr'){
		$NAME1 = 'CANCELED';
		$OR1 = 'CR-'.$cc->invoice_num;
		$AMOUNT_COL = '';

		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';

		if($CR1==true){
			$NAME1 = 'CANCELED - COLLECTOR\'S RECEIPT';
		}

	}

	if($inv1->status == 'collector_receipt' && $CR1==false){
		$NAME1 = 'COLLECTOR RECEIPT';
		$OR1 = 'CR-'.$cc->invoice_num;
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($CR1 == true){
		$OR1 = 'CR-'.$cc->invoice_num;
	}

	//$NAME1
	if($cc->pay_type == 'check'){
		//~ $NAME1.=' (CHECK)';
	}

	if($inv1->status == 'or_nw' && $CR1==false){
		$OR1 .= ' (NW) ';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'cancel_cr_nw' && $CR1==false){
		$OR1 = 'CR-'.$cc->invoice_num;
		$NAME1 = 'COLLECTOR RECEIPT';
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'cancel_cr_nw' && $CR1==true){
		$OR1 = 'CR-'.$cc->invoice_num;
		$NAME1 = 'NW-CR - CANCELED';
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'nw_cancel' && $CR1==false){
		$OR1 = 'OR-'.$cc->invoice_num;
		$NAME1 = 'NW- CANCELED';
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'cr_nw' && $CR1==false){
		$OR1 = 'CR-'.$cc->invoice_num;
		$NAME1 = 'NW- COLLECTOR RECEIPT';
		$AMOUNT_COL = '';
		$A1 = '';
		$A2 = '';
		$A3 = '';
		$A4 = '';
		$A5 = '';
	}

	if($inv1->status == 'cr_nw' && $CR1==true){
		$OR1 = 'CR-'.$cc->invoice_num.' (NW) ';
	}

	$NAME1 = substr($NAME1,0,35);
	
	if($cc->penalty101 > 0){
		$A4 = number_format(@$cc->penalty101,2);
		$A1 = '';
		$A2 = @$cc->xxx_arre==0?'':number_format((@$cc->xxx_arre - @$cc->penalty101),2);
	}
	

	return compact(
		'NAME1',
		'OR1',
		'AMOUNT_COL',
		'A1',
		'A2',
		'A3',
		'A4',
		'A5',
		'inv1'
	);

}//

function help_collector_daily_report_func222($dd)
{
	extract($dd);

	Fpdf::SetLeftMargin(5);

	Fpdf::Cell(30,4, $OR1, 0,0,'L', false);
	Fpdf::Cell(50,4, substr($NAME1, 0,28), 0 ,0,'L', false);
	Fpdf::Cell(20,4, $AMOUNT_COL, 0,0,'R', false);

	Fpdf::Cell(20,4, $A1,0,0,'R', false);
	Fpdf::Cell(20,4, $A2 ,0,0,'R', false);

	Fpdf::Cell(20,4,$A3,0,0,'R', false);
	Fpdf::Cell(20,4,$A4,0,0,'R', false);
	Fpdf::Cell(20,4,$A5,0,0,'R', false);

	Fpdf::Ln();
}//

function help_collector_daily_report_func333($dd)
{
	extract($dd);

	if($inv1->tax_val > 0  && ($inv1->status == 'active'))
	{
		Fpdf::SetLeftMargin(5);

		Fpdf::Cell(30,4, '', 0,0,'L', false);
		Fpdf::Cell(50,4, 'W\TAX', 0 ,0,'L', false);
		Fpdf::Cell(20,4, '', 0,0,'R', false);

		Fpdf::Cell(20,4, '',0,0,'R', false);
		Fpdf::Cell(20,4, '' ,0,0,'R', false);

		Fpdf::Cell(20,4,'',0,0,'R', false);
		Fpdf::Cell(20,4,'',0,0,'R', false);
		Fpdf::Cell(20,4, number_format($inv1->tax_val,2) ,0,0,'R', false);

		Fpdf::Ln();
	}

	if(($inv1->pay_type == 'check') &&  ($inv1->status == 'active'))
	{
		Fpdf::SetLeftMargin(5);

		Fpdf::Cell(30,4, '', 0,0,'L', false);
		Fpdf::Cell(50,4, 'CHECK #'.$inv1->check_no, 0 ,0,'L', false);
		Fpdf::Cell(20,4, '', 0,0,'R', false);

		Fpdf::Cell(20,4, '',0,0,'R', false);
		Fpdf::Cell(20,4, '' ,0,0,'R', false);

		Fpdf::Cell(20,4,'',0,0,'R', false);
		Fpdf::Cell(20,4,'',0,0,'R', false);
		Fpdf::Cell(20,4,'' ,0,0,'R', false);

		Fpdf::Ln();
	}


	if(($inv1->pay_type == 'ada') &&  ($inv1->status == 'active'))
	{
		Fpdf::SetLeftMargin(5);

		Fpdf::Cell(30,4, '', 0,0,'L', false);
		Fpdf::Cell(50,4, 'ADA - REF #'.$inv1->check_no, 0 ,0,'L', false);
		Fpdf::Cell(20,4, '', 0,0,'R', false);

		Fpdf::Cell(20,4, '',0,0,'R', false);
		Fpdf::Cell(20,4, '' ,0,0,'R', false);

		Fpdf::Cell(20,4,'',0,0,'R', false);
		Fpdf::Cell(20,4,'',0,0,'R', false);
		Fpdf::Cell(20,4,'' ,0,0,'R', false);

		Fpdf::Ln();
	}


}//

function help_collector_daily_report_subttl111($dd)
{
	extract($dd);

	Fpdf::SetFont('Courier',"B", 8);

	Fpdf::SetLeftMargin(5);
	//~ Fpdf::Cell(40,6, 'Official Reciept','T',0,'L', false);
	//~ Fpdf::Cell(30,6, 'SUB TOTALS','T',0,'L', false);
	Fpdf::Cell(70,6, 'SUB TOTALS','T',0,'L', false);
	Fpdf::Cell(10,6, '', 'T' ,0,'L', false);
	Fpdf::Cell(20,6, number_format($total_collected, 2),'T',0,'R', false);
	Fpdf::Cell(20,6, number_format($total_bill, 2),'T',0,'R', false);
	Fpdf::Cell(20,6, number_format($total_arrear, 2),'T',0,'R', false);
	Fpdf::Cell(20,6, number_format($total_arrear22, 2),'T',0,'R', false);
	Fpdf::Cell(20,6, number_format(0, 2),'T',0,'R', false);
	Fpdf::Cell(20,6, number_format($ttl_discount, 2),'T',0,'R', false);

	Fpdf::SetFont('Courier',"", 8);

	Fpdf::Ln();
	Fpdf::SetLeftMargin(5);
	Fpdf::Cell(50,6, '',0,0,'R', false);
	Fpdf::Cell(20,6, 'CASH',0,0,'L', false);
	Fpdf::Cell(30,6, number_format($ttl_cash, 2),0,0,'R', false);

	Fpdf::Ln();
	Fpdf::SetLeftMargin(5);
	Fpdf::Cell(50,6, '',0,0,'R', false);
	Fpdf::Cell(20,6, 'CHECK',0,0,'L', false);
	Fpdf::Cell(30,6, number_format($ttl_check, 2),0,0,'R', false);

	Fpdf::Ln();
	Fpdf::SetLeftMargin(5);
	Fpdf::Cell(50,6, '',0,0,'R', false);
	Fpdf::Cell(20,6, 'ADA',0,0,'L', false);
	Fpdf::Cell(30,6, number_format($ttl_ada, 2),0,0,'R', false);


	Fpdf::Ln();
	Fpdf::SetLeftMargin(5);
	Fpdf::Cell(50,6, '',0,0,'R', false);
	Fpdf::Cell(20,6, 'WATER BILL',0,0,'L', false);
	Fpdf::Cell(30,6, number_format(@$ttl_water, 2),0,0,'R', false);

	Fpdf::Ln();
	Fpdf::SetLeftMargin(5);
	Fpdf::Cell(50,6, '',0,0,'R', false);
	Fpdf::Cell(20,6, 'NON-WATER BILL',0,0,'L', false);
	Fpdf::Cell(30,6, number_format(@$ttl_non_water, 2),0,0,'R', false);

	Fpdf::SetFont('Courier',"B", 8);
}//


function help_collector_monthly($date1_x, $zone1=0)
{
	//~ echo $zone1;
	//~ die();

		$user = Auth::user();

		$zone_sql1 = '';
		if($zone1 != 0)
		{
			$zone_sql1 = '

					AND zone_id='.$zone1.'

			';

		}

		$raw_db = DB::raw( $SSS = '
							SUM(payment) as ttl,
							SUM(tax_val) as ttl_tax,
							SUM(bill1) as bill1,
							SUM(arrear1) as arrear1,
							SUM(penalty1) as penalty1,

							(
								SELECT
									SUM(payment) as ttl_nw
								FROM collections

								WHERE
										collection_type = \'non_water_bill_payment\'
									AND
										(status=\'or_nw\' || status=\'cr_nw\')
									AND
										payment_date like \''.$date1_x.'%\'



									'.$zone_sql1.'

							) as ttl_nw
						');


		$coll_all_raw = Collection::where('payment_date', 'like', $date1_x.'%')
					->where(function($query){
							$query->where('status', 'active');
							$query->orWhere('status', 'or_nw');
							$query->orWhere('status', 'collector_receipt');
						})
					->where(function($qq){
						$qq->where('collection_type', 'bill_payment');
						$qq->orWhere('collection_type', 'non_water_bill_payment');
					});
					//->where('collector_id',$user->id);


		if($zone1 != 0)
		{
			$coll_all_raw->where('zone_id', $zone1);
		}

		$coll_all =  $coll_all_raw->select($raw_db)->first();

	//~ echo '<pre>';
	//~ print_r($coll_all->toArray());
	//~ die();

	return $coll_all;

}//



Class NWBill {

	// get_other_payable()
	static
	function get_other_payable()
	{
		return get_other_payable();
	}


	static 
	function get_payables1($acct_id, $led_id)
	{
		$ledger_all = LedgerData::where('ledger_datas.status', 'active')
						->where('ledger_datas.acct_id', @$acct_id)
						->where('ledger_datas.id', '<', @$led_id)
						->leftJoin('arrears', function($join){
							$join->on('ledger_datas.acct_id', '=', 'arrears.acct_id');
							$join->on('ledger_datas.period', '=', 'arrears.period');
						})
						->with('adjust_me')
						->selectRaw('ledger_datas.*, arrears.amount as arrear_amt')
						->orderBy('ledger_datas.zort1', 'desc')
						->orderBy('ledger_datas.id', 'desc')
						->get();

			$data = CollectionService::payables_and_payed_no_db($ledger_all);
			$brk_dwn = CollectionService::breakdown($data['payables'], 0);
			$brk_dwn = array_reverse($brk_dwn);

			ee($brk_dwn, __FILE__, __LINE__);

	}//

}//





ob_start();
