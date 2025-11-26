<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Zones;
use App\LedgerData;
use App\BillingMdl;
use App\BillingDue;
use App\ReadingPeriod;



class PenaltyCtrl extends Controller
{

	
	function get_apply_penalty_for_billing_zone_list($period1)
	{
		$zones1 = Zones::where('status', 'active')
						->orderBy('id', 'asc')
						->get();

		$due_dates = get_due_dates($period1);
		
		$str11 = '';
		$str11.='<h3>'.date('F Y', strtotime($period1)).' - For Penalty</h3>';
		
		$str11 .= '
			<h4> Current Date : '.date('F d, Y').'</h4>
			<table class="table10 hh">
				<tr class="headings">
					<td>Zone</td>
					<td>Count</td>
					<td>Penalty Date</td>
					<td>Action</td>
				</tr>		
		';
		
		foreach(@$zones1 as $zz):
		

			$per1 = date('Y-m-01', strtotime($period1));
			
			//~ $pen_date = get_penalty_date_V1($period1, $zz->id);		
			
			$dd1 = $due_dates[$zz->id];
			$pen_date = $dd1;
			
			$pen_count = get_penalty_counts($per1,$zz->id, $pen_date);
			//' / '.$pen_count['tobe_penalize'].
			$str11.='
				<tr>	
					<td>'.strtoupper($zz->zone_name).'</td>
					<td> '.$pen_count['penalized'].'</td>
					<td>'.@$dd1.'</td>
					<td><button  onclick="execute_penalty_now(\''.$pen_date.'\', '.$zz->id.', \''.$period1.'\')">Execute Penalty</button></td>
				</tr>
			';
		endforeach;
		
		$str11.= '</table>';
		
		return array('stat'=>1, 'html' => $str11);		
		
	}//
	
	
	//$pen_date+'/'+$zone_id+'/'+$period;
	function execute_penalty_by_zone_id_pen_date($pen_date, $zone_id, $period)
	{
		
		$CC_due_dates_XX = get_due_dates_V3($period);
		
		
		$curr_date   = date('Y-m-d');
		$curr_period = date('Y-m-01');
		
		// if($period == $curr_period){
		// 	return array('status' => 0, 'msg' => 'Period is not valid.'); // Sep 2, 2021 /-->>lagria
		// }

		$tt1 = strtotime($pen_date);
		$tt2 = strtotime($curr_date);
		
		if($tt1 > $tt2){
			return array('status' => 0, 'msg' => 'Penalty is '.date('F d, Y',$tt1).' and today is '.date('F d, Y',$tt2));
		}
		
		$cur_time = strtotime($current_date = date('Y-m-d'));
			
		
		$billings_raw = BillingMdl::where('period', $period)
						 ->with(['ledger12' => function($query){
							$query->where('led_type','!=', 'beginning');
							$query->orderBy('zort1', 'desc')
										->orderBy('id', 'desc');
						 }])
						 ->with(['account'])
						 ->whereHas('account', function($query)use($zone_id){
								$query->where('zone_id', $zone_id);
								$query->where('acct_status_key', '2');
							})
						 ->whereNotNull('penalty_date')
						 ->where('penalty_date','<=', $current_date);
			
		$total_tobe_procced = $billings_raw->count('id');
		$total_due_stat = $billings_raw->whereNull('due_stat')->count('id');
		
		
		$remaining  = $total_tobe_procced - $total_due_stat;
		
		$billings = $billings_raw
							->whereNull('due_stat')
							->limit(1)
								->get();

		if($billings->count() == 0)
		{
			return array('status' => 0, 'msg' => 'No penalty list to process');
		}
		
		foreach($billings as $bill)
		{
			$remaining ++;
			
			if($bill->ledger12)
			{
				
				if(isGov($bill->account->acct_type_key))
				{
					$bill->due_stat = 'no-due';
					$bill->save();
					continue;
				}
				
				if($bill->account->pen_exempt == 1)
				{
					$bill->due_stat = 'no-due';
					$bill->save();
					continue;					
				}
				
				
				if($bill->ledger12->ttl_bal <= 0){
					// No penalty
					$bill->due_stat = 'no-due';
					$bill->save();
					continue;
				}
				//Has Penalty
				//has-due
				
				
				$current_bill = (float) $bill->billing_total  -  (float) $bill->discount;
				$penalty_amount = ($current_bill * PENALTY_PERCENT);
				
				//If Has Balance on Payment
				if($bill->ledger12->ttl_bal < $current_bill)
				{
					$penalty_amount = ($bill->ledger12->ttl_bal * PENALTY_PERCENT);
				}
				
				
				$ttl_penalty001 = 	round(abs($penalty_amount),2);

				$curr_due  = new BillingDue;
				$curr_due->bill_id = $bill->id;
				$curr_due->due_date = $bill->penalty_date;
				$curr_due->due_stat = 'active';
				$curr_due->bill_balance = $current_bill;
				$curr_due->due1 = $ttl_penalty001;
				$curr_due->period = $bill->period;
				$curr_due->acct_id = $bill->account->id;
				$curr_due->acct_no = $bill->account->acct_no;
				$curr_due->save();
				
				
				$ttl  = $bill->ledger12->ttl_bal;
				$ttl += $ttl_penalty001;
				
				$new_ledger = new LedgerData;
				$new_ledger->ledger_info='Penalty';
				$new_ledger->led_type='penalty';
				$new_ledger->status='active';
				$new_ledger->period = $period;
				$new_ledger->acct_id = $bill->account->id;
				$new_ledger->acct_num = $bill->account->acct_no;
				$new_ledger->date01 = $bill->penalty_date;
				$new_ledger->reff_no = $curr_due->id;
				$new_ledger->penalty = $ttl_penalty001;
				$new_ledger->ttl_bal = round($ttl,2);
				$new_ledger->glsl = PENALTY_GL_CODE;
				$new_ledger->save();				
				
				$bill->penalty  = $ttl_penalty001;
				$bill->due_stat = 'has-due';
				$bill->save();
				
				
				continue;
				 
			}else{
				//if Doesnthave leger
				$bill->due_stat = 'no-due';
				$bill->save();
				continue;
			}//
			
		}	
		
		
		return array('status' => 1, 'ttp' => $total_tobe_procced, 'rr' =>  $remaining);
				  
		
		//~ echo '<pre>';
		//~ print_r($bill1->toArray());
		//~ die();		
			
	}
	
	
	
}//











