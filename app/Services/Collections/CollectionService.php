<?php 


namespace App\Services\Collections;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// use App\Collection;
use App\LedgerData;
use App\Arrear;
use App\Collection;
use App\OtherPayable;
use App\HwdLedger;
use App\NwbBalance;
use App\PayPenLed;
use App\Accounts;
use App\Invoice;
use App\BillingMdl;
use App\BillingAdjMdl;
use App\User;

use Excel;


class CollectionService
{

	static
	function get_py_cy($payed_arr, $payment_date, $due_date=null)
	{
		$previous_year_time = strtotime(date('Y-01-01',strtotime($payment_date)).' -1 day');
		$current_year_time  = strtotime(date('Y-m-01',strtotime($payment_date)).' -1 day');
		$current_date_time  = strtotime($payment_date); 

		// echo $previous_year_time;
		// echo '<br />';
		// echo $current_year_time;
		// echo '<br />';
		// echo strtotime($payed_arr[13]['date01']);
		// die();

		$pycy_arr = [
			'py' => 0,
			'cy' => 0,
			'cur' => 0,
			'pen' => 0,
			'amt' => 0
		];

		foreach($payed_arr as $kk => $vv)
		{

			// $penalty_date_time  = strtotime(trim($vv['penalty_date']).' -1 day');

			if( $previous_year_time >= strtotime($vv['date01']) )
			{
				$pycy_arr['py'] += $vv['amount'];
				continue;
			}

			if( $current_year_time >= strtotime($vv['date01']) )
			{
				$pycy_arr['cy'] += $vv['amount'];
				continue;
			}

			if( $vv['led_type'] == 'penalty') 
			{
				$pycy_arr['pen'] += $vv['amount'];
			}else{
				$pycy_arr['cur'] += $vv['amount'];
			}



			/*


			//IF ALREADY A DUEDATE
			if($current_date_time >= $penalty_date_time) 
			{
				// PY PY PY - including penalty
				if( $previous_year_time >= strtotime($vv['date01']) )
				{
					$pycy_arr['py'] += $vv['amount'];
				}

				//CURRENT YEAR ARREAR
				elseif( $previous_year_time < strtotime($vv['date01']) ) 
				{
					if( $current_year_time >= strtotime($vv['date01']) )
					{
						$pycy_arr['cy'] += $vv['amount'];
					}
					else
					{
						if( $vv['led_type'] == 'penalty') 
						{
							$pycy_arr['pen'] += $vv['amount'];
						}else{
							$pycy_arr['cy'] += $vv['amount'];
						}
					}

				}

			}else{
				//DUE DATE IS NOT YET
				$pycy_arr['cur'] += $vv['amount'];
			}
			*/
		}

		$pycy_arr['bil'] = $pycy_arr['cur'];
		// $pycy_arr['bil'] = $pycy_arr['cur'];

		return $pycy_arr;

	}

	static
	function get_py_cy_v2($payed_arr, $payment_date, $due_date=null)
	{
		// echo '<pre>';
		// print_r($payed_arr);

		// ee($payed_arr, __FILE__, __LINE__);



		$previous_year_time = strtotime(date('Y-01-01',strtotime($payment_date)).' -1 day');
		$current_year_time  = strtotime(date('Y-m-01',strtotime($payment_date)).' -1 day');
		$current_date_time  = strtotime($payment_date); 

		$pycy_arr = [
			'py' => 0,
			'cy' => 0,
			'cur' => 0,
			'pen' => 0,
			'amt' => 0,
			'bil' => 0,
			'nwb' => 0,
			'nwb_desc' => [],
		];

		foreach($payed_arr as $kk => $vv)
		{

			// $penalty_date_time  = strtotime(trim($vv['penalty_date']).' -1 day');

			if( in_array($vv['led_type'], ['nw_billing']) )
			{
				$pycy_arr['nwb'] += $vv['val'];
				$pycy_arr['nwb_desc'][] = $vv['desc'];
				continue;
			}


			if( $previous_year_time >= strtotime($vv['date01']) )
			{
				$pycy_arr['py'] += $vv['val'];
				continue;
			}

			if( $current_year_time >= strtotime($vv['date01']) )
			{
				$pycy_arr['cy'] += $vv['val'];
				continue;
			}

			if( $vv['typ'] == 'penalty') 
			{
				$pycy_arr['pen'] += $vv['val'];
			}else{
				$pycy_arr['cur'] += $vv['val'];
				$pycy_arr['bil'] += $vv['val'];
			}
			
		}

		// $pycy_arr['bil'] = $pycy_arr['cur'];

		return $pycy_arr;

	}//



	static 
	function get_payable_raw($collectables, $penlty_date='')
	{

		echo '<pre>';
		print_r($collectables);
		die();

		$payable_raw = []; 
				
		foreach( $collectables as $kk => $vv )
		{

			// All Payment
			if( in_array( $vv['led_type'] , ['payment','payment_cancel','payment_cr','cancel_cr']) ) 
			{
				continue;
			}

			if( in_array($vv['led_type'] , ['cr_nw','cr_nw_debit','cancel_cr_nw','or_nw', 'or_nw_debit', 'nw_cancel']) )
			{
				continue;
			}

			if( in_array($vv['led_type'] , ['wtax']) )
			{
				continue;
			}

			// ADJUSTMENT WILL BE REWORK
			if( in_array($vv['led_type'] , ['adjustment']) )
			{
				continue;
			}


			if( in_array($vv['led_type'] , ['billing','beginning','penalty']) )
			{

				$my_amt = 0;

				if( $vv['led_type'] == 'beginning')
				{
					$my_amt = $vv['arrear'];
				}

				if( $vv['led_type'] == 'penalty')
				{
					$my_amt = $vv['penalty'];

				}
				
				if( $vv['led_type'] == 'billing')
				{
					$my_amt = $vv['billing'];
				}

				$payable_raw[] = [
									$vv['id'],
									$vv['led_type'],
									round($my_amt - $vv['discount'], 2),
									$vv['discount'],
									$vv['date01'],
									$vv['period'],
									$vv['reff_no'],
									$penlty_date
								];
			}

		}// FOR END		

		// echo '<pre>';
		// print_r($payable_raw);
		// die();

		return $payable_raw;

	}// END

	static 
	function get_existing_payment($my_collection)
	{
		//Payment Only
		$additional_payment = 0;
		foreach( $my_collection as $kk => $vv )
		{

			// All Payment
			if( in_array( $vv['led_type'] , ['payment','payment_cancel','payment_cr','cancel_cr']) ) 
			{
				$additional_payment += $vv['payment'];
				continue;
			}

			// All Non-Water
			if( in_array($vv['led_type'] , ['cr_nw','cr_nw_debit','cancel_cr_nw','or_nw', 'or_nw_debit', 'nw_cancel']) )
			{
				// unset($vv);
				// $cc->my_collectables[$kk] = null;
			}

		}//	

		if($additional_payment <=0 ){
			$additional_payment = 0;
		}
		
		return $additional_payment;
	}//END

	static 
	function remove_payed_acct($payable_raw, $existing_payment)
	{

		// $payable_raw[] = [
		// 	'id', 0
		// 	'led_type', 1
		// 	'amount', 2 
		// 	'discount', 3
		// 	'date01', 4 
		// 	'period', 5
		// 	'reff_no', 6
		// 	'penalty_date', 7
		// 	'bill_id', 8
		// ];		

				/**/
				/**/
				/**/

				$payed_arr = [];
				$existing_payment = $existing_payment;
				$charge1  = 0;

				foreach($payable_raw as $kk=>$pay1)
				{

					// Break if Empty
					if($existing_payment <= 0 )
					{
						break;
					}
					
					if( $existing_payment <  $payable_raw[$kk]['amount'] )
					{
						$payable_orig = $payable_raw[$kk];

						$fix_zero = round($existing_payment,2);
						$payable_raw[$kk]['amount'] -= $fix_zero;

						if($payable_raw[$kk]['amount'] <= 0){
							$payed_arr[] = $payable_orig;
							unset($payable_raw[$kk]);
							break;
						}

						$payable_orig['amount'] -= $payable_raw[$kk]['amount']; 
						$payed_arr[] = $payable_orig;
						break;

					}elseif($existing_payment <  $payable_raw[$kk]['amount']) {
						unset($payable_raw[$kk]);
						break;

					} 

					$existing_payment -= $payable_raw[$kk]['amount'];

					$payed_arr[] = $payable_raw[$kk];
					unset($payable_raw[$kk]);

				} 

				return [$payable_raw, $payed_arr];
				/**/
				/**/
				/**/		

	}

	static 
	function get_year_end_balance($pdate, $cid)
	{
		// $pdate Payment date
		// this method only return the Last year balance or previous year end balance

		$date_now = date('Y-01-01', strtotime($pdate));

		return LedgerData::where('status', 'active')
								->where('acct_id', $cid)
								->where('date01','<', $date_now)
								->orderBy('date01', 'desc')
								->orderBy('zort1', 'desc')
								->orderBy('id', 'desc')
								->first();

	}//

	static 
	function get_remaining_collectable($any_last_led_id, $acct_id, $date_ar)
	{
		$cc = new \stdClass();
		$cc->payment_date = $date_ar[0];
		$cc->billing = (object) ['penalty_date'=> $date_ar[1]];

		$collectables = self::get_collectables($any_last_led_id, $acct_id, $cc);
		$payable_list = self::remove_payed_acct($collectables['payable'], $collectables['ttl_payment']);

		return $payable_list;
	}//

	static 
	function   get_collectables($max_led_id, $cid, $cc) 
	{
		// $max_led_id = MAXIMUM LEDGER ID 
		// $cid = Customer id
		// $cc = collection object		
		
		$year_end_date = date('Y-m-d', strtotime(date('Y-01-01', strtotime($cc->payment_date)).' -1 Day '));

		$payable_raw = [];

		// GET YEAR END BALANCE	
		// GET YEAR END BALANCE	
		$year_end_balance_raw  = self::get_year_end_balance($cc->payment_date, $cid);
		$year_end_balance = 0;
		$led_id = '';

		if($year_end_balance_raw)
		{
			$year_end_balance = $year_end_balance_raw->ttl_bal;
			$led_id = $year_end_balance_raw->id;
		}

		$payable_raw[] = [
			'id' => $led_id,
			'led_type' =>  '',
			'amount' => $year_end_balance,
			'discount' => 'year_end_beginning',
			'date01' => $year_end_date,
			'period' => '',
			'reff_no'  => $led_id,
			'penalty_date'  => @$cc->billing->penalty_date,
			'bill_id'  => '',
		];
		// GET YEAR END BALANCE	
		// GET YEAR END BALANCE	
		
		// echo $year_end_date;
		// die();

		$ledger_payables =  LedgerData::where('status', 'active')
								->where('acct_id', $cid)
								->where('date01','>', $year_end_date)
								// ->where('id', '<', '767169')
								->whereIn('led_type', ['billing', 'penalty'])
								->orderBy('date01', 'asc')
								->orderBy('zort1', 'asc')
								->orderBy('id', 'asc')
								->get();
		
		foreach($ledger_payables as $lp1)
		{
			$payable_raw[] = [
				'id' => $lp1->id,
				'led_type' =>  $lp1->led_type,
				'amount' => $lp1->led_type == 'billing'?($lp1->billing - $lp1->discount):$lp1->penalty,
				'discount' => $lp1->discount,
				'date01' => $lp1->date01,
				'period' => $lp1->period,
				'reff_no'  => $lp1->reff_no,
				'penalty_date'  => @$cc->billing->penalty_date,
				'bill_id'  => $lp1->bill_id,
			];
		}


		$total_payment =  LedgerData::where('status', 'active')
								->where('acct_id', $cid)
								->where('date01','>', $year_end_date)
								->where('id','<', $max_led_id)
								->whereIn('led_type', ['payment', 'payment_cr'])
								->sum('payment');

		$total_cancel =  LedgerData::where('status', 'active')
								->where('acct_id', $cid)
								->where('date01','>', $year_end_date)
								->where('id','<', $max_led_id)
								->whereIn('led_type', ['payment_cancel', 'cancel_cr'])
								->sum('payment');								

		return [
				'payable' => $payable_raw, 
				'ttl_payment' => ($total_payment + $total_cancel) , 
				'payment' => $total_payment, 
				'cancels' => $total_cancel
			];
			
	}//

    static 
    function get_breakdown_collection_data($acct_id, $payme=0)
    {

		$acct_id  = (int) $acct_id;

		$payment_info = CollectionService::payables_and_payed($acct_id);
		$brk1         = CollectionService::breakdown($payment_info['payables'], 0);
		$myPay        = CollectionService::process_payment($brk1, ($payment_info['payment_made']));

		// ee($brk1, __FILE__, __LINE__);

		// echo $payment_info['payment_made'];
		// echo '<pre>';
		// print_r($payment_info['payables']->toArray());
		// print_r($payment_info['payment_made']);
		// die();

		// echo '<Pre>';
		// print_r($payment_info['payables']);
		// print_r($payment_info['payment_made']);
		// die();

		$ret1 = [];

		foreach($myPay['remaining'] as $bb)
		{
			$ret1[] = [
				'amt' => $bb['val'],
				'typ' => $bb['typ'],
				'period' => $bb['period'],
				'reff_no' => $bb['reff_no'],
				'date01' => $bb['date01'],
				'led_id'  => $bb['led_id'],
				'other_payable'  => $bb['other_payable'],
			];

		}
		
		return $ret1;

		echo '<pre>';
		print_r($brk1);
		die();
    }//

	static 
	function get_ledger_balance($acct_id) 
	{
		$ledger2 = LedgerData::where('acct_id', @$acct_id)
						->where('status','active')
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						->orderBy('id', 'desc')
						->first();			

		$ttl_bal = 0;

		if($ledger2){
			$ttl_bal = $ledger2->ttl_bal;
		}				

		return $ttl_bal;

	}// END


	static 
	function  daily_collection_excel()
	{
		CollectionReportService::daily_collection_excel();
	}// END


	static 
	function test001($acct_id)
	{

		$payment_info = self::payables_and_payed($acct_id, 1);
		$brk1 = self::breakdown($payment_info['payables'], 0);
		$b1   = self::process_payment($brk1, $payment_info['payment_made']);


		echo '<pre>';
		echo $payment_info['payment_made'];
		print_r($brk1);

		// print_r($payables->toArray());
		// print_r($payment_made->toArray());

		// $brkdwn = self::breakdown($payables, 0);
		// print_r($brkdwn);

		die();		

	}//


	static
	function payables_and_payed_no_db($led_datas)
	{

		$ret_data = []; 
		$payed_ret = 0; 

		$led_data = [];

		foreach($led_datas as $ld)
		{
			if( $ld->led_type == 'billing' && $ld->arrear_amt <= 0 )
			{
				$led_data[] = $ld;
				break;
			 }

			// $led_data[] = $ld->toArray();
			$led_data[] = $ld;

		}

		// echo '<pre>';
		// print_r($led_datas->toArray());
		// die();

		foreach($led_data as $ld)
		{
			// $ld = (object) $ld;

			if( in_array($ld->led_type, ['payment', 'payment_cancel', 'payment_cr', 'cancel_cr']) )
			{
				$payed_ret = round($payed_ret + $ld->payment, 2);
			}

			if( in_array($ld->led_type, ['wtax']) )
			{
				$payed_ret = round($payed_ret + $ld->payment, 2);
			}

			if( in_array($ld->led_type, ['adjustment']) )
			{
				$payed_ret = round($payed_ret + $ld->bill_adj, 2);
			}


			if( in_array($ld->led_type, ['billing','beginning','penalty','other_payable', 'nw_billing']) )
			{
				// $ret_data[] = $ld->toArray();
				$ret_data[] = $ld;
			}

		}//

		return ['payment_made' => $payed_ret, 'payables' => $ret_data];

	}

	static 
	function payables_and_payed($acct_id, $all=0, $coll_id=0)
	{
		$acct_id = (int) $acct_id;


		$last_ledger = LedgerData::where('status', 'active')
			->whereIn('led_type', ['payment', 'payment_cancel', 'payment_cr', 'cancel_cr'])		
			->where('acct_id', $acct_id)
			// ->where('coll_id', $coll_id)
			->orderBy('date01', 'desc')
			->orderBy('zort1', 'desc')
			->orderBy('id', 'desc')
			->first();
			// echo $last_ledger->id;
			// die();
		
		$has_full_payed = LedgerData::where('acct_id', $acct_id)
			->where('status', 'active')
			->where('ttl_bal', '<=', '0')
			->whereRaw("
				id IN ( 
					SELECT MAX(id) mmid FROM `ledger_datas`
					WHERE acct_id=$acct_id 
					AND led_type IN ('payment', 'payment_cancel', 'payment_cr', 'cancel_cr')
					GROUP BY reff_no
				)									
			")
			->where('id', '!=', $last_ledger?$last_ledger->id:0)
			->orderBy('date01', 'desc')
			->orderBy('zort1', 'desc')
			->orderBy('id', 'desc')
			->first();


		#########
		#########
		// $last_full_payment = LedgerData::where('acct_id', $acct_id)
		// 							->where('ttl_bal', '<=', 0)
		// 							->orderBy('id', 'desc')
		// 							->first();
		

$sqlxxx = "
SELECT * FROM 	ledger_datas 
WHERE 
	 ttl_bal<=0
AND  acct_id=$acct_id
AND 
(
	id NOT IN (

			SELECT id FROM 
				ledger_datas 
			WHERE 
			acct_id=$acct_id
			AND 
			reff_no IN(
				SELECT reff_no FROM ledger_datas
				WHERE acct_id=$acct_id AND led_type='payment_cancel'
			)
			AND
			led_type IN ('payment', 'payment_cancel' )
	)
	AND 
	id NOT IN (

			SELECT id FROM 
				ledger_datas 
			WHERE 
			acct_id=$acct_id
			AND 
			reff_no IN(
				SELECT reff_no FROM ledger_datas
				WHERE acct_id=$acct_id AND led_type='cancel_cr'
			)
			AND
			led_type IN ('payment_cr', 'cancel_cr' )
	)
)
ORDER BY id desc 
LIMIT 1
		";
// ee($sqlxxx, __FILE__, __LINE__);
$last_full_payment = DB::select( $sqlxxx );
// ee($last_full_payment, __FILE__, __LINE__);

		$last_full_payment_id = 0; 
		if( !empty ($last_full_payment) ) 
		{
			$last_full_payment = $last_full_payment[0];
			$last_full_payment_id = $last_full_payment->id;
		}
		// ee($last_full_payment->toArray(), __FILE__, __LINE__);
		#########
		#########

		$payables = LedgerData::whereIn('ledger_datas.led_type', ['billing','beginning','penalty','other_payable', 'nw_billing'])
			->where('ledger_datas.status', 'active')
			->where('ledger_datas.acct_id', $acct_id)
			->where('ledger_datas.id', '>', $last_full_payment_id)
			->with('adjust_me')
			// ->join('billing_adj_mdls as bam', function($join){
			// 	$join->on('ledger_datas.acct_id','=', 'bam.acct_id');
			// 	$join->on('ledger_datas.bill_id','=', 'bam.bill_id');
			// })
			// ->selectRaw('ledger_datas.*, count(bam.id) as MM')
			->orderBy('ledger_datas.zort1', 'asc')
			->orderBy('ledger_datas.id', 'asc');

		########
		$r1 = $payables->get()->toArray();
		// ee($r1, __FILE__, __LINE__);
		########

			
		$payment_made = LedgerData::whereIn('led_type', ['payment', 'payment_cancel', 'payment_cr', 'cancel_cr'])
			->where('status', 'active')
			->where('ledger_datas.id', '>', $last_full_payment_id)
			->where('acct_id', $acct_id)
			->with('wtax')
			->orderBy('date01', 'asc')
			->orderBy('zort1', 'asc')
			->orderBy('id', 'asc');

		if( $has_full_payed && $all == 0) 
		{
			$payables->where('ledger_datas.id','>', $has_full_payed->id);
			$payment_made->where('id','>', $has_full_payed->id);
		}
		
		$payables = $payables->get();			
		$payment_made = $payment_made->get();

		$payed_ret = 0;
		foreach($payment_made as $pp)
		{
			// if($pp->led_type == 'adjustment'){
			// 	$payed_ret = round($payed_ret - $pp->bill_adj, 2);
			// 	continue;
			// }

			$payed_ret = round($payed_ret + $pp->payment, 2);

			if($pp->wtax)
			{
				$payed_ret = round($payed_ret + $pp->wtax->payment, 2);
			}
		}

		$adjsts = billing_adjustment_reff_bill($acct_id);
		// ff1($adjsts);

		foreach($payables as $pp)
		{

			if( !empty($adjsts[$pp->id]) ) {
				$pp->billing = round( $pp->billing - $adjsts[$pp->id]->bill_adj, 2 );
			}

			// FOR NEGATIVE ADJUSTMENT ONLY 
			// FOR NEGATIVE ADJUSTMENT ONLY    
			// FOR NEGATIVE ADJUSTMENT ONLY 
			if( $pp->adjust_me->sum('amount') < 0 ) {
				$pp->billing = round( $pp->billing + abs($pp->adjust_me->sum('amount')), 2 );
				continue;
			}			

			continue;
			continue;
			continue;
			// OLD
			// OLD
			// OLD
			// OLD
			if( $pp->adjust_me->sum('amount') > 0 ) {
				$pp->billing = round( $pp->billing - $pp->adjust_me->sum('amount'), 2 );
			}

			// FOR NEGATIVE ADJUSTMENT ONLY 
			// FOR NEGATIVE ADJUSTMENT ONLY    
			// FOR NEGATIVE ADJUSTMENT ONLY 
			if( $pp->adjust_me->sum('amount') < 0 ) {
				$pp->billing = round( $pp->billing + abs($pp->adjust_me->sum('amount')), 2 );
				continue;
			}
		}//

		// ee($payables->toArray(), __FILE__, __LINE__);

		foreach($payables as $kk => $pp)
		{
			if( $pp->led_type == 'beginning' )
			{
				if( $pp->arrear <= 0)
				{
					unset($payables[$kk]);
				}
			}
		}//


		// NON WATER BILL
		// NON WATER BILL
		// NON WATER BILL
		$nw_ledger = LedgerData::where('status', 'active')
			->whereIn('led_type', ['or_nw', 'or_nw_debit'])		
			->where('acct_id', $acct_id)
			->where('ledger_datas.id', '>', $last_full_payment_id)
			// ->where('period','>', '2023-09-01')
			->selectRaw('*, SUM(payment) sum_pay, SUM(bill_adj) sum_adj')
			->groupBy('coll_id')
			->orderBy('date01', 'desc')
			->orderBy('zort1', 'desc')
			->orderBy('id', 'desc');
			// ->get();		

		$nw_ledger->with('collection');

		if( $has_full_payed )
		{
			$nw_ledger->where('id','>', $has_full_payed->id);
		}

		$nw_ledger = $nw_ledger->get();

		foreach($nw_ledger as $nw_l)
		{
			$nw_bal = abs($nw_l->sum_pay + $nw_l->sum_adj); 

			if( !in_array($nw_l->collection->status, ['nw_cancel', 'cancel_cr_nw'])) 
			{
				if($nw_bal > 0)
				{
					// other_payable
					$nw_l->led_type = 'other_payable'; 
					$nw_l->billing = $nw_bal; 
					$payables[] = $nw_l;
				}
			}

		}

		// ee($payables);
		// NON WATER BILL
		// NON WATER BILL
		// NON WATER BILL



		// NEGATIVE ADJUSTMENT
		// NEGATIVE ADJUSTMENT
		// NEGATIVE ADJUSTMENT
		/*
		$nw_ledger = LedgerData::where('status', 'active')
			->whereIn('led_type', ['adjustment'])		
			->where('acct_id', $acct_id)
			->where('bill_adj','<', 0)
			->where('period','>', '2023-09-01')
			->selectRaw('*, SUM(payment) sum_pay, SUM(bill_adj) sum_adj')
			->groupBy('coll_id')
			->orderBy('zort1', 'desc')
			->orderBy('id', 'desc');

		if( $has_full_payed )
		{
			$nw_ledger->where('id','>', $has_full_payed->id);
		}

		$nw_ledger = $nw_ledger->get();

		foreach($nw_ledger as $nw_l)
		{
			$nw_bal = abs($nw_l->sum_pay + $nw_l->sum_adj); 
			if($nw_bal > 0)
			{
				// other_payable
				$nw_l->led_type = 'other_payable'; 
				$nw_l->billing = $nw_bal; 
				$payables[] = $nw_l;
			}
		}*/

		// CONVERT TO PAYABLES
		// CONVERT TO PAYABLES
		// CONVERT TO PAYABLES

		$new_ands = "  ";
		if( $has_full_payed && $all == 0) 
		{
			// $payables->where('ledger_datas.id','>', $has_full_payed->id);
			// $payment_made->where('id','>', $has_full_payed->id);
			$new_ands =  " AND id > ".$has_full_payed->id.' '; 
		}

			// ->where('ledger_datas.id', '>', $last_full_payment_id)

		$neg_adj_sql1 = "
							SELECT * FROM `ledger_datas`
							WHERE acct_id=? AND led_type='adjustment' and bill_adj < 0 
							AND status='active' 
							AND id > ".$last_full_payment_id."
							$new_ands
						";

        $adj_neg01 = DB::select($neg_adj_sql1, [$acct_id]);

		foreach($adj_neg01 as $kk => $vv)
		{
			$vv->billing  = abs($vv->bill_adj); 
			$vv->led_type = 'other_payable'; 
			$payables[] = $vv;
		}//

		$new_payables = $payables->toArray();

		$new_ar1 = [];
		foreach($new_payables as $kk => $vv)
		{
			$vv = (array) $vv;
			$new_ar1[$vv['id']] = $vv;
		}

		ksort($new_ar1);
		$new_ar2 = [];
		foreach($new_ar1 as $kk => $vv)
		{
			$new_ar2[] = (object) $vv;
		}

		$payables = $new_ar2;
		// CONVERT TO PAYABLES END
		// CONVERT TO PAYABLES END
		// CONVERT TO PAYABLES END

		// echo '<pre>';
		// print_r($new_ar2);
		// die();


		// NEGATIVE ADJUSTMENT
		// NEGATIVE ADJUSTMENT
		// NEGATIVE ADJUSTMENT	


		// echo '<pre>';
		// print_r($has_full_payed->toArray());
		// die(); 



		// echo 'AAAA';
		// die();



		return [
				'payables' => $payables,
				'payment_made' => $payed_ret
		];
		
		echo '<pre>';
		print_r($payables->toArray());
		// print_r($payment_made->toArray());
		die();
		
	}//



    static
    function breakdown($ledgers, $total_payed)
    {

   

       if($total_payed > 0)
       {
			$cost = 0;

            foreach($ledgers as $kk => $remain) 
            {

                if($total_payed <= 0){
                    break;
                }

                if( $remain->led_type == 'billing'){
                    // $cost = round($remain->billing, 2);
                    $cost = round($remain->billing - $remain->discount, 2);
                    $remain->discount = 0;

                    if($total_payed < $cost) {
                        $remain->billing = round($cost - $total_payed, 2);
                        break;
                    }

                }
                if( $remain->led_type == 'penalty'){
                    $cost = $remain->penalty;
                    if($total_payed < $cost) {
                        $remain->penalty = round($cost - $total_payed, 2);
                        break;
                    }
                }
                if( $remain->led_type == 'beginning'){
                    $cost =  $remain->arrear;
                    if($total_payed < $cost) {
                        $remain->arrear = round($cost - $total_payed, 2);
                        break;
                    }
                }
                if( $remain->led_type == 'other_payable'){
                    $cost =  $remain->billing;
                    if($total_payed < $cost) {
                        $remain->billing = round($cost - $total_payed, 2);
                        break;
                    }
                }

                if( $remain->led_type == 'nw_billing'){
                    $cost =  $remain->billing;
                    if($total_payed < $cost) {
                        $remain->billing = round($cost - $total_payed, 2);
                        break;
                    }
                }


                if($cost != 0)
                {
                    unset($ledgers[$kk]);
                    $total_payed = round($total_payed - $cost, 2); 
                }

                if($total_payed <= 0){
                    break;
                }                

            }


            // echo '<pre>';
            // print_r($account->toArray());
            // print_r($ledgers->toArray());
        }



        $return1 = [];
        foreach($ledgers as $ll)
        {
            $cost = 0;

            if( $ll->led_type == 'billing'){$cost = $ll->billing - $ll->discount;}
            if( $ll->led_type == 'penalty'){$cost = $ll->penalty;}
            if( $ll->led_type == 'beginning'){$cost = $ll->arrear;}
            if( $ll->led_type == 'other_payable'){$cost = $ll->billing;}
            if( $ll->led_type == 'nw_billing'){$cost = $ll->billing;}

            $return1[] = [
                'desc' => $ll->ledger_info,
                'pre_val' => $cost,
                'val' => $cost,
                'typ' => $ll->led_type,
                'led_type' => $ll->led_type,
                'led_id' => $ll->id,
                'date01' => $ll->date01,
                'reff_no' =>$ll->reff_no,
                'period' => $ll->period,
                'amount' => $cost,
				'other_payable' => $ll->nw_desc,
				'beg_data1' => @$ll->beg_data1,
				
            ];
            
        }

        return $return1;

        echo '<pre>';
        print_r($return1);
        print_r($ledgers->toArray());
        die();

    }//END 


    static 
    function process_payment($breakdown, $amount)
    {
        $total_payed = $amount;
        $cost = 0;

        $payed_arr = [];

        foreach($breakdown as $kk => $vv) 
        {
            if($total_payed <= 0){break;}

            $cost =  $vv['val'];

            if($total_payed < $cost) {
                $payed_arr[$kk] =  $vv;
                $payed_arr[$kk]['val'] = $total_payed;
                $breakdown[$kk]['val'] = round($cost - $total_payed, 2);
                break;
            }
            elseif($total_payed > $cost) {
                $payed_arr[$kk] =  $vv;
                $total_payed = round($total_payed - $cost,2);
                unset($breakdown[$kk]);
            }
            else{
                $payed_arr[$kk] =  $vv;
                $total_payed = round($total_payed - $cost, 2);
                unset($breakdown[$kk]);
                break;
            }

            if($total_payed <= 0){break;}
        }

		foreach($breakdown as $kk=>$vv){ $vv['amount'] = $vv['val']; $breakdown[$kk] = $vv;}
		foreach($payed_arr as $kk=>$vv){ $vv['amount'] = $vv['val']; $payed_arr[$kk] = $vv;}

        return [
                'remaining' => $breakdown, 
                'payed' => $payed_arr
            ];


    }// END


	static 
	function request_data($acct_id)
	{

		$userId = Auth::id();

		$bill_id = (int) @$_GET['bill_id'];
		$inv_num  = '';
		$amt = (float) @$_GET['amt'];

		$invoice_num = (int) @$_GET['inv'];

		$bcash  = (float) @$_GET['bcash'];
		$bcheck = (float) @$_GET['bcheck'];
		$bchecknum = @$_GET['bchecknum'];
		$bbankname = (int) @$_GET['bbankname'];
		$bbankbranch = @$_GET['bbankbranch'];
		$method = @$_GET['method'];
		$wtax = @$_GET['wtax'];
		$wtax_value = @$_GET['wtax_value'];
		$ort1 = @$_GET['ort1'];
		$ada_amount = @$_GET['ada_amount'];
		$chk_full = @$_GET['chk_full'];
		$chk_date = @$_GET['chk_date'];
		$amt_due_x = @$_GET['amt_due_x'];// FOR OVER PAYMENT ALLOWED ONLY


		$banks = get_bank_list();
		$banks = $banks->toArray();


		$trx_date    = @$_GET['trx_date'];
		$min_month   = strtotime(date('Y-m-d').' -1 Month');
		$cur_date33  = strtotime(date('Y-m-d'));

		$trans_date33  = strtotime($trx_date);


		$inv_set = Invoice::where('seq_start','<=', $invoice_num)
			->where('seq_end','>=', $invoice_num)
			->first();


		$check_invoice = Collection::where('invoice_num',$invoice_num)
			->where('payment_date', '>=', '2025-06-08' )### SALES INVOICE STARTED
			->orderBy('id', 'desc')
			->first();


		// ee($check_invoice->toArray(), __FILE__, __LINE__);


		$amount_recieve = $amt;

		if( in_array($method, ['check','both', 'ada'] ))
		{
			if($method == 'check'){
				$amt = $bcheck;
			}
	
			if($method == 'both'){
				$amt = $bcash + $bcheck;
			}
	
			if($method == 'ada'){
				$amt = $ada_amount;
			}
	
			$amount_recieve = $amt;

		}//
	
		$acct111 = Accounts::find($acct_id);


		$ww1 = 0;
		$per_c1 = 0;

		if($wtax == 'true')
		{
			$ww1 = $wtax_value;
		}


		$LATEST_LEDGER = LedgerData::where('status','active')
			->where('acct_id', $acct_id)
			->orderBy('date01', 'desc')
			->orderBy('zort1', 'desc')
			->orderBy('id', 'desc')
			->first();

		if($LATEST_LEDGER)
		{
			$amount_due = round($LATEST_LEDGER->ttl_bal,2);
		}
	
		// FOR OVER PAYMENT ALLOWED ONLY
		if($amt_due_x > $amount_due){
			$amount_due = $amt_due_x;
		}

		$payment_made = $amt;
		$change1 = $amt - $amount_due;


		if($change1 > 0){
			$payment_made = $amount_due;
		}else{
			$payment_made = $amt;
		}

		$col_stat11 = 'active';

		if($ort1 == 'CR'){
			$col_stat11 = 'collector_receipt';
		}		

		$trans_date33_d = date('Y-m-d', $trans_date33);

		//PREPARE
        $is_good = true;
		$all_vars = get_defined_vars(); // 

        return $all_vars;
	}// 


	static 
	function collections_conditions($vars)
	{
		extract($vars);

		if($trans_date33 > $cur_date33){
			//return array('status'=>0, 'msg'=>'Invalid transaction date '.date('F d, Y', $trans_date33));
		}

		if($trans_date33 <= $min_month){
			return array('status'=>0, 'msg'=>'Invalid transaction date '.date('F d, Y', $trans_date33));
		}

		if($invoice_num == 0){
			return array('status'=>0, 'msg'=>'Invalid Invoice');
		}

		if(!$inv_set){
			return array('status'=>0, 'msg'=>'Invalid Invoice');
		}

		// 
		// 
		// 
		if($check_invoice)
		{
			if($ort1 == 'OR')
			{
				return array('status'=>0, 'msg'=>'Invoice #'.$invoice_num.' is already used. ERROR 1 - 1228');
			}else{ //CR

				if(
					$check_invoice->status == 'cancel_receipt' ||
					$check_invoice->status == 'cancel_cr' ||
					$check_invoice->status == 'nw_cancel'

				)
				{
					if($check_invoice->invoice_num != $invoice_num){
						return array('status'=>0, 'msg'=>'Invoice #'.$invoice_num.' is not equal to previous invoice #');
					}

				}else{
					return array('status'=>0, 'msg'=>'Invoice #'.$invoice_num.' is already used. ERROR 2');
				}
			}

		}else{

			if($ort1 == 'CR')
			{
				return array('status'=>0, 'msg'=>'Invalid collector receipt #');
			}

		}
		// 
		// 
		// 

		if($amt <=0 && $method=='cash'){
			return array('status'=>0, 'msg'=>'Payment amount is invalid');
		}

		if($bcheck <= 0 && ($method=='check' || $method=='both')){
			return array('status'=>0, 'msg'=>'Invalid check amount');
		}

		if(trim($bchecknum) == '' && ($method=='check' || $method=='both')){
			return array('status'=>0, 'msg'=>'Invalid check number');
		}

		if(empty(@$banks[$bbankname]) && ($method=='check' || $method=='both')){
			return array('status'=>0, 'msg'=>'Invalid Bank');
		}


		if($amount_due <= 0){
			return array('status'=>0, 'msg'=>'Full payment is alreay done.');
		}


		return 'good';
	}//


	static 
	function coll_report_breakdown($coll)
	{


		$coll_led = LedgerData::where('status', 'active')
			->where('coll_id', $coll->id)
			// ->where('coll_id', '164534')
			->first();

		if( !$coll_led )
		{
			echo '<h1>FAILED TO CREATE LEDGER ON THIS COLLECTION </h1>';
			echo '<pre>';
			print_r($coll);
			exit;
		}
		
		$ledger_all = LedgerData::where('ledger_datas.status', 'active')
			->where('ledger_datas.acct_id', @$coll_led->acct_id)
			->where('ledger_datas.id', '<', @$coll_led->id)
			->leftJoin('arrears', function($join){
				$join->on('ledger_datas.acct_id', '=', 'arrears.acct_id');
				$join->on('ledger_datas.period', '=', 'arrears.period');
			})
			->with('adjust_me')
			->selectRaw('ledger_datas.*, arrears.amount as arrear_amt')
			->orderBy('ledger_datas.zort1', 'desc')
			->orderBy('ledger_datas.id', 'desc')
			->get();
		



		$data = self::payables_and_payed_no_db($ledger_all);
		// ee($data, __FILE__, __LINE__);

		$brk_dwn = CollectionService::breakdown($data['payables'], 0);
		$brk_dwn = array_reverse($brk_dwn);
		// ee($brk_dwn, __FILE__, __LINE__);

		$payment_info = CollectionService::process_payment($brk_dwn, $data['payment_made']);
		$payment_info = CollectionService::process_payment($payment_info['remaining'], $coll->tax_val);
		$payment_info = CollectionService::process_payment($payment_info['remaining'], $coll->payment);
		// ee($payment_info, __FILE__, __LINE__);

		$pycy = CollectionService::get_py_cy_v2($payment_info['payed'], $coll->payment_date);

		###########
		###########
		foreach($payment_info['payed'] as $k => $v) {
			if( $v['typ'] == 'beginning' ) 
			{
				$jdata = json_decode($v['beg_data1'], true);
				$curr = $pycy['cur'];

				$pycy['cur'] = 0;
				$pycy['bil'] = 0;

				if( @$jdata['py_arrear'] > 0 ) { @$pycy['py'] = $curr; }
				elseif( @$jdata['cy_arrear'] > 0 ) { @$pycy['cy'] = $curr; }
				else{ @$pycy['cur'] = $curr; }
			}
		}
		##########
		// ee1($jdata, __FILE__, __LINE__);
		// ee1($pycy, __FILE__, __LINE__);
		// ee($payment_info, __FILE__, __LINE__);

		// $coll->id
		
		
		// $coll_info = json_decode($coll->coll_info, true);
		// echo '<pre>';
		// echo '--------------';
		// print_r($payment_info);
		// echo '<br />';
		// echo '--------------';
		// echo '<br />';


		// if( empty($coll->coll_info) )
		// {

		// }else{
		// 	$coll_info = json_decode($coll->coll_info);
		// 	$all_amt = 0;
		// 	foreach($coll_info->payed as $pp)
		// 	{
		// 		$all_amt+= $pp->amount;
		// 	}

		// 	$pycy['amt'] = $all_amt;

		// }
		// die();


		return $pycy;
		
		// echo $data['payables'];
		// echo $data['payment_made'];
		// echo '<br/>';
		// echo $coll->payment;
		// echo '<pre>';
		// print_r($pycy);
		// print_r($coll_led->toArray());
		// print_r($ledger_all->toArray());
		// die();
		// print_r($data);
		// print_r($pycy);
		// die();

		
	}
    
}    



