<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\ConnectionLedger;
use App\Accounts;
use App\LedgerData;






class ConnectionLedgerCtrl extends Controller
{
	
	
	
	function for_disconnection_status_disconnect()
	{
		$acct_ids = @$_GET['mm'];
		
		if(count($acct_ids) <= 0){
			return array(
					'stat'=>0,
					'msg' => 'Account id is missing.'
				);
		}
		
		foreach($acct_ids as $ai)
		{
			$qq = json_decode(base64_decode($ai));
			$acct_1 = Accounts::where('id', $qq->acct_id)->first();
			if(!$acct_1){continue;}
			
			$new_cled = new ConnectionLedger;
			$new_cled->acct_id = $acct_1->id;
			$new_cled->bill_id = $qq->bill_id;
			$new_cled->acct_no = $qq->acct_no;
			$new_cled->status	 = 'active';
			$new_cled->typ1 = 'disconnected';
			$new_cled->remaks = 'Disconnected Line';
			$new_cled->date1 = date('Y-m-d',strtotime($qq->date1));
			
			
			$new_cled->save();
			
			$acct_1->acct_status_key = 4;
			$acct_1->save();
			
		}
		
		return array(
				'stat'=>1,
				'msg' => 'Done updated'
			);		

		
		
		
	}//END
	
	
	function make_for_disconnection_status()
	{
		$acct_ids = @$_GET['mm'];
		
		if(count($acct_ids) <= 0){
			return array(
					'stat'=>0,
					'msg' => 'Account id is missing.'
				);
		}
		
		foreach($acct_ids as $ai)
		{
			$qq = json_decode(base64_decode($ai));
			
			//~ print_r();
			//~ die();
			
			$acct_1 = Accounts::where('id', $qq->acct_id)->first();
			if(!$acct_1){continue;}
			
			$new_cled = new ConnectionLedger;
			$new_cled->acct_id = $acct_1->id;
			$new_cled->bill_id = $qq->bill_id;
			$new_cled->acct_no = $qq->acct_no;
			$new_cled->status	 = 'active';
			$new_cled->typ1 = 'for_disconnection';
			$new_cled->remaks = 'For Disconnection';
			$new_cled->date1 = date('Y-m-d',strtotime($qq->date1));
			
			$new_cled->save();
			
			$acct_1->acct_status_key = 3;
			$acct_1->save();
			
		}
		
		return array(
				'stat'=>1,
				'msg' => 'Done updated'
			);
	}//
	
	
	function for_disconnection_list($date1, $zone_id, $acct_id)
	{
		$date_start = date('Y-m-01', strtotime($date1));
		$period1 = date('Y-m', strtotime($date1));
		
		$discon_list_raw = Accounts::where('zone_id', $zone_id)
						->whereHas('bill1',  $bill1 = function($query)use($date1, $date_start, $period1){
							$query->where('period', 'like', $period1.'%');
						})
						->whereHas('ledger_data3', $ledger_data3 = function($query)use($date1, $date_start){
							$query->where('date01','<=', $date1);
							//~ $query->where('date01','>=', $date_start);
							$query->where('led_type', '!=', 'beginning');
							$query->where('status', 'active');
						})
						->where('acct_status_key', '3')
						->with(['ledger_data3' => $ledger_data3])
						->with(['bill1' => $bill1])
						->orderBy('route_id', 'asc')
						->get();		
						
						
			$discon_list = [];
			
			$led_where1 = function($q1){
							$q1->where('led_type', 'billing');
							$q1->orWhere('led_type', 'beginning');
						};
						
			$led_where2 = function($q1){								
							$q1->where('led_type', 'billing');
							$q1->orWhere('led_type', 'penalty');
							$q1->orWhere('led_type', 'adjustment');
							$q1->orWhere('led_type', 'witholding');
						};
			
			foreach($discon_list_raw as $dl)
			{
				if($dl->ledger_data3->ttl_bal <= 0){continue;}
				//~ $discon_list[] = (object) $dl->toArray();
				$discon_list[] = $dl;
				//~ $dl->ledger_data3->date01
				
				$current_ttl = $dl->ttl_bal;
				
				$led2 = LedgerData::where('acct_id', $dl->id)
						->where($led_where1)
						->where('status','active')
						->orderBy('period','desc')
							->limit(10)
								->get();
							
				//~ echo '<pre>';
				//~ print_r($led2->toArray());
				//~ die();
				
				$date_stat1 = '';
				
				foreach($led2 as $l2)
				{
					$cur_per = date('Y-m', strtotime($l2->period));
					
					$date_stat1 = $l2->date01;
					
					$ttl1 = 0;
					if($l2->led_type == 'beginning'){
						$ttl1 = $l2->arrear;
						break;
					}
					
					$sum1 = LedgerData::where('acct_id', $dl->id)
								->where($led_where2)
									->where('status','active')

									->where('date01','like', $cur_per.'%')
										->get();
										
					$ttl1 += $sum1->sum('billing');
					$ttl1 += $sum1->sum('penalty');
					$ttl1 -= $sum1->sum('discount');
					$ttl1 -= $sum1->sum('bill_adj');
					
					$current_ttl -= $ttl1;
					
					if($current_ttl<=0){
						break;
					}
					
				}//
				
				echo '<br />';
				echo $date_stat1;
				
			}//
			
			echo '<pre>';
			print_r($discon_list);
			die();
						
			if(@$_GET['dd'] == 1){
				return $discon_list;
			}
		
			return view('billings.inc.billing_billing.for_disconnection_list', compact('discon_list', 'date1'));						
						
		
		echo '<pre>';
		print_r($discon_list_raw->toArray());
		
	}//
	
	
	
	
}//
