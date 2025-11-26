<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;


use App\AccountMetas;
use App\Accounts;
use App\Zones;
use App\Reading;
use App\HwdOfficials;
use App\ReadingPeriod;
use App\CustomerRoute;
use App\BillingRateVersion;
use App\BillingMdl;
use App\BillingMeta;
use App\Arrear;
use App\BillingAdjMdl;
use App\LedgerData;
use App\GpsAcct;


use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HwdLedgerCtrl;
use App\Http\Controllers\BillingCtrl;


class ReadingCtrl extends Controller
{

	function execute_billing_by_20($period1, $zone_id, $pen_date)
	{

		$ky1 = '|'.$zone_id.'@'.$pen_date.'|';
		
		$ar1 = array();
		$ar1[$zone_id] = $ky1;
		
		
		$read_per1 = ReadingPeriod::where('period', $period1)
						->where('due_dates','like', '%'.$ky1.'%')
							->first();
							
		
		if(!$read_per1)
		{
			$read_per2 = ReadingPeriod::where('period', $period1)
								->first();
								
			if($read_per2->due_dates == '')
			{
				$read_per2->due_dates = json_encode($ar1);
				$read_per2->save();
			}else{
				
				$r33 =  json_decode($read_per2->due_dates, true);
				$r33[$zone_id] = $ky1;
				$read_per2->due_dates = json_encode($r33);
				$read_per2->save();
			}
		}

		//billable_raw, unbilled_raw, billed_raw
		$dd = get_billable_info($period1, $zone_id);
		extract($dd);

		$billable = $billable_raw->count();
		$unbilled = $unbilled_raw->count();
		$billed = $billed_raw->count();

		$unbilled_data =
				$unbilled_raw
					->with(['reading1'=>function($query)use($period1){

							$per1 = date('Y-m', strtotime($period1));
							$query->where('period', 'like', $per1.'%');
							$query->where('curr_reading', '!=', 0);
							$query->whereNotNull('curr_reading');

						}])
					->limit(10)
					->get();

		//~ echo '<pre>';
		//~ print_r($unbilled_data->toArray());
		//~ die();

		if($unbilled == 0)
		{
			return array(
					'stat' =>1,
					'billable' => $billable,
					'unbilled' => $unbilled,
					'billed' => $billed
				);
		}

		$data11 = ServiceCtrl::___rates_and_discounts();


		//~ echo '<pre>';
		//~ print_r($data11);
		//~ die();
		//~ die();
		//~ die();


		$acct_type = array();
		$acctype1 = AccountMetas::where('meta_type', 'account_type')->get();

		foreach($acctype1 as $att)
		{
			@$acct_type[$att->id] = $att->toArray();
		}

        $data11['acct_type'] = $acct_type;

		$rate_version = BillingRateVersion::orderBy('id', 'desc')
					->first();


		extract($data11);


		//~ $json_1 = json_decode($rate_version->meta_data);
		//~ echo '<pre>';
		//~ print_r($unbilled_data->toArray());
		//~ print_r($json_1);
		//~ die();



		foreach($unbilled_data as  $ubb)
		{

			//~ $ubb->reading1;

			//~ echo '<pre>';
			//~ print_r($unbilled_data->toArray());
			//~ die();
			//~ die();
			//~ die();


			$billed++;
			$unbilled --;

			$arrear =  getArrearV1($ubb->id, $period1);

			if($arrear)
			{
				$ubb->reading1->bill_stat = 'billed';
				$ubb->reading1->save();
				continue;
			}



			$total_arrear = 0;
			$latest_ledger = getLatestLegerV2($ubb->id);

			if($latest_ledger)
			{
				$total_arrear = $latest_ledger->ttl_bal;
			}


			$billing1 = BillingMdl::where('period', $period1)
							->where('account_id', $ubb->id)
								->first();
			if($billing1)
			{
				$ubb->reading1->bill_stat = 'billed';
				$ubb->reading1->save();
				continue;
			}


			 $consumption =  (int) $ubb->reading1->current_consump;

			if(empty($ubb->acct_type_key)){
				 $ubb->acct_type_key = 14;
			 }

             //~ echo '<pre>';
             //~ print_r($ubb->toArray());
             //~ die();

             $total_billing = BillingCtrl::__calculate_billing_rates($consumption, $rate_info[$ubb->acct_type_key], $ubb->meter_size_id);

             //~ echo '<pre>';
             //~ print_r($total_billing);
             //~ die();

			 $gov_type = array(10,11,12,13);
			 if(in_array($ubb->acct_type_key, $gov_type)){
				  $ubb->acct_discount = null;
			 }

			$acct_discount_id = (int) @$ubb->acct_discount;
			$dis001 = @$discount_type[$acct_discount_id];

			$total_discount = 0;
			$total_all = 0;

			if(!empty($dis001))
			{
			   $total_discount =  round($total_billing * ($dis001['meta_value'] / 100), 2);
			}

			if($consumption>30)
			{
			  $total_discount = 0;
			}

			//~ echo "\n\n";
			//~ echo $total_discount;
			//~ print_r($dis001);

			$bill_date = date('Y-m-',strtotime($period1));
			$bill_date.= $zone_id;

			$prev_read = $ubb->reading1->prev_reading;
			$curr_read = $ubb->reading1->curr_reading;



			$new_billing = new BillingMdl;
			$new_billing->period = $period1;
			$new_billing->reading_id = $ubb->reading1->id;
			$new_billing->rate_id	 = $rate_version->id;
			$new_billing->status	 = 'active';
			$new_billing->bill_date	 = $bill_date;
			$new_billing->prep_by	 = 1;
			$new_billing->billing_total = $total_billing;
			$new_billing->curr_bill = $total_billing;
			$new_billing->account_id = $ubb->id;
			$new_billing->consumption = (int) @$consumption;
			$new_billing->read_PC = $prev_read.'||'.$curr_read;
			$new_billing->arrears = $total_arrear;
			$new_billing->discount = $total_discount;
			$new_billing->penalty_date = $pen_date;
			$new_billing->save();

			$new_arrear = new Arrear;
			$new_arrear->acct_id = $ubb->id;
			$new_arrear->acct_id_str = $ubb->acct_no;
			$new_arrear->amount = $total_arrear;
			$new_arrear->period = $period1;
			$new_arrear->arr_type = 'billing';
			$new_arrear->save();


			if(!empty($dis001))
			{

				$new_bill = new BillingAdjMdl;
				$new_bill->acct_id = $ubb->id;
				$new_bill->acct_no = $ubb->acct_no;
				$new_bill->date1 = $bill_date;
				$new_bill->date1_stamp = date('Y-m-d H:i:s');
				$new_bill->ref_no = $new_billing->id;
				$new_bill->amount = $total_discount;
				$new_bill->adj_typ = $dis001['id'].'|'.$dis001['meta_name'];
				$new_bill->adj_period = $period1;
				$new_bill->save();

			}

			$str_date = date('F Y', strtotime($period1));


			$ttl = $total_arrear + $total_billing;

			$new_ledger = new LedgerData;
			$new_ledger->ledger_info='Billing '.$str_date;
			$new_ledger->led_type='billing';
			$new_ledger->status='active';
			$new_ledger->period = $period1;
			$new_ledger->acct_id = $ubb->id;
			$new_ledger->acct_num = $ubb->acct_no;
			$new_ledger->date01  = $bill_date;
			$new_ledger->reff_no = $new_billing->id;
			$new_ledger->reading = $curr_read;
			$new_ledger->consump = $consumption;
			$new_ledger->billing = $total_billing;
			$new_ledger->arrear  = (float) @$total_arrear;
			$new_ledger->ttl_bal = $ttl;
			$new_ledger->save();

			if(!empty($dis001))
			{
				$ttl -= abs($total_discount);
				$new_ledger = new LedgerData;
				$new_ledger->ledger_info = @$dis001['meta_name'];
				$new_ledger->led_type='adjustment';
				$new_ledger->status='active';
				$new_ledger->period = $period1;
				$new_ledger->acct_id = $ubb->id;
				$new_ledger->acct_num = $ubb->acct_no;
				$new_ledger->date01 = $bill_date;
				$new_ledger->reff_no = $new_billing->id;
				$new_ledger->bill_adj = abs($total_discount);
				$new_ledger->ttl_bal = $ttl;
				$new_ledger->save();
			}//


			$ubb->reading1->bill_stat = 'billed';
			$ubb->reading1->save();



		}//

		//~ echo '<pre>';
		//~ print_r($unbilled_data->toArray());

		return array(
			'stat' =>0,
			'billable' => $billable,
			'unbilled' => $unbilled,
			'billed' => $billed
		);


	}//




	function start_for_billing_execute($period1)
	{
		
		$rea22 = ReadingPeriod::where('period', $period1)
					->first();
		
		$due_date11 = array();
		
		//~ echo $rea22->due_dates;
		if(!empty($rea22->due_dates)){
			$due_date11 = json_decode($rea22->due_dates, true);
		}
		
		//~ echo '<pre>';
		//~ print_r($due_date11); 
		//~ die();
		

		$zones1 = Zones::where('status', 'active')
						->orderBy('id', 'asc')
						->get();

		$str11 = '';
		$str11.='<h3>'.date('F Y', strtotime($period1)).'</h3>';
		$str11 .= '
			<table class="tab_read_period1 table10">
				<tr class="headings">
					<td>Zone</td>
					<td>&nbsp;</td>
					<td>Due Date</td>
					<td>Action</td>
				</tr>
		';

		foreach(@$zones1 as $zz):

			$per1 = date('Y-m-01', strtotime($period1));

			//billable_raw, unbilled_raw, billed_raw
			$dd = get_billable_info($per1, $zz->id);
			extract($dd);

			$billable = $billable_raw->count();
			$unbilled = $unbilled_raw->count();
			$billed = $billed_raw->count();

			$penalty_date = get_penalty_date_V1($per1, $zz->id);


			
			$dd1 = @$due_date11[$zz->id];
			
			if($dd1 != '')
			{
				$dd1 = str_replace('|','',$dd1);
				$dd1 = explode('@', $dd1);
				$dd1 = $dd1[1];
			}//
			
			

			$str11.='
				<tr>
					<td>'.strtoupper($zz->zone_name).'</td>
					<td>'.$billed.'/'.$billable.'</td>
					<td><input type="text"  class="datepick_77  dd_'.$zz->id.'"  
					placeholder="yyyy-mm-dd" value="'.$dd1.'" /></td>
					<td><button  onclick="start_billing_101('.$zz->id.')">Execute Billing</button></td>
				</tr>
			';
		endforeach;

		$str11.= '</table>';

		return array('stat'=>1, 'html' => $str11);




	}//

	function initilize_get_zones_counts($period1)
	{
		
		$rea22 = ReadingPeriod::where('period', $period1)
						->first();
						
		$sched_d11  = json_decode($rea22->read_dates);
		$sched_d11  = (array) $sched_d11;						
		
		$due_d11  = json_decode($rea22->due_dates2);
		$due_d11  = (array) $due_d11;

		$fine_d11  = json_decode($rea22->fine_dates);
		$fine_d11  = (array) $fine_d11;
		
		//~ echo '<pre>';
		//~ print_r($rea22->due_dates2);
		//~ die();
		
		$zones1 = Zones::where('status', 'active')
						->orderBy('id', 'asc')
							->get();

		$str11 = '
			<input type="hidden"  class="read_period11"  value="'.$rea22->id.'" />		
			<table class="tab_read_period1 table10">
				<tr class="headings">
					<td>Zone</td>
					<td>Cons.</td>
					<td>Reading Sched.</td>
					<td>Due Date</td>
					<td>Penalty Date</td>
					<td>Action</td>
				</tr>
		';

		foreach(@$zones1 as $zz):
		
			$per1 = date('Y-m-01', strtotime($period1));
			//~ $count1 = get_zone_total($period1, $zz->id);
			$count1 = '';

			$str11.='
				<tr>
					<td>'.strtoupper($zz->zone_name).'</td>
					<td>'.$count1.'</td>
					<td><input type="text"  class="dd1_date  read_z_'.$zz->id.'"  placeholder="'.' Reading Schedule"  		value="'.@$sched_d11[$zz->id].'" /></td>
					<td><input type="text"  class="dd1_date  due_z_'.$zz->id.'"   placeholder="'.' Due Date"  		value="'.@$due_d11[$zz->id].'" /></td>
					<td><input type="text"  class="dd1_date  pen_z_'.$zz->id.'"   placeholder="'.' Penalty Date"     value="'.@$fine_d11[$zz->id].'" /></td>
					<td><button  onclick="initialize_reading_period111('.$zz->id.')">Initialize</button></td>
				</tr>
			';
		endforeach;

		$str11.= '</table>';

		return array('stat'=>1, 'html' => $str11);

	}//


	function initilize_start_v2($period1, $zone_id, $due_date, $read_per_id, $fine_date, $read_date)
	{
		
		//~ echo $fine_date;
		//~ return;
		//~ return;
		//~ return;

		$res1 = Accounts::where('zone_id', $zone_id)
				->where(function($query){
					$query->where('acct_status_key', 1);
					$query->orWhere('acct_status_key', 2);
					$query->orWhere('acct_status_key', 3);
					$query->orWhere('acct_status_key', 4);
				})
				->whereDoesntHave('reading1', function($query)use($period1){
					$per1 = date('Y-m-01', strtotime($period1));
					$query->where('period', 'like', $per1);
				})
				->where('status', '!=', 'deleted')
				->limit(10)
				->get();

		//~ echo '<pre>';
		//~ print_r($res1->toArray());
		//~ die();

		$res2 = Accounts::where('zone_id', $zone_id)
				->where(function($query){
					$query->where('acct_status_key', 1);
					$query->orWhere('acct_status_key', 2);
					$query->orWhere('acct_status_key', 3);
					$query->orWhere('acct_status_key', 4);
				})
				->whereHas('reading1', function($query)use($period1){
					$per1 = date('Y-m-01', strtotime($period1));
					$query->where('period', 'like', $per1);
				})
				->where('status', '!=', 'deleted')
				//~ ->limit(10)
				->count();
				//~ echo $res2;
				//~ die();


		$users11 = Accounts::where('zone_id', $zone_id)
				->where(function($query){
					$query->where('acct_status_key', 1);
					$query->orWhere('acct_status_key', 2);
					$query->orWhere('acct_status_key', 3);
					$query->orWhere('acct_status_key', 4);
				})
				->where('status', '!=', 'deleted')
				//~ ->limit(10)
				->count();

		//~ echo  $users11;
		//~ die();


		$per1 = date('Y-m-01', strtotime($period1));
		$prev_period = date('Y-m-01', strtotime($period1.' -1 Month'));
		
		$read_period22 = ReadingPeriod::find($read_per_id);
		
		
		////////////
		////////////
		
		//~ echo $fine_date;
		//~ die();
		
		$bills = BillingMdl::whereHas('account', function($q1)use($zone_id){
								$q1->where('zone_id', $zone_id);
								
					})
					->where('period', $per1)
						->update(['penalty_date'=>$fine_date]);
						
		//~ foreach($bills as $b){
			//~ $b->penalty_date = $fine_date;
		//~ }
		
		//~ echo $fine_date;
		
		//~ die();
		
		////////////
		////////////
		
		
		
		if(!$read_period22){
			return array('stat' => 4, 'count1' => ($res2), 'total_count' =>$users11);
		}else{
			
			$zns11 = Zones::where('status', 'active')->get();
			
			
			
			//DUE DATE
			$due_dates1 = json_decode($read_period22->due_dates2);
			
			if($due_dates1)
			{
				$due_dates1 = (array) $due_dates1;
				$due_dates1[$zone_id] =  $due_date;
				$read_period22->due_dates2 = json_encode($due_dates1);
				$read_period22->save();
			}
			else
			{
				$due_dates1 = array ();
				$due_dates1[$zone_id] =  $due_date;
				$read_period22->due_dates2 = json_encode($due_dates1);
				$read_period22->save();
			}
			
			//FINE DATE
			$fine_date1 = json_decode($read_period22->fine_dates);

			if($fine_date1)
			{
				$fine_date1 = (array) $fine_date1;
				$fine_date1[$zone_id] =  $fine_date;
				$read_period22->fine_dates = json_encode($fine_date1);
				$read_period22->save();
			}
			else
			{
				$fine_date1 = array ();
				$fine_date1[$zone_id] =  $fine_date;
				$read_period22->fine_dates = json_encode($fine_date1);
				$read_period22->save();
			}

			
			//READING DATE
			$read_date1 = json_decode($read_period22->read_dates);

			if($read_date1)
			{
				$read_date1 = (array) $read_date1;
				$read_date1[$zone_id] =  $read_date;
				$read_period22->read_dates = json_encode($read_date1);
				$read_period22->save();
			}
			else
			{
				$read_date1 = array ();
				$read_date1[$zone_id] =  $read_date;
				$read_period22->read_dates = json_encode($read_date1);
				$read_period22->save();
			}
			

			
			
		}


		if(count($res1->toArray()) == 0)
		{
			return array('stat' => 1, 'count1' => ($res2), 'total_count' =>$users11);
		}


		$xx = 0;
		foreach($res1 as $rr)
		{
			$xx++;

			$prev_read_date =  date('Y-m', strtotime($period1.' -1 Month'));
			$prev_read_date.= '-'.$rr->zone_id;

			//~ $current_read_date = date('Y-m', strtotime($period1));
			//~ $current_read_date.= '-'.$rr->zone_id;
			
			$current_read_date = date('Y-m-d', strtotime($read_date));

			$prev_reading =  get_reading_val($rr->id, $prev_period);

			$reading = new Reading;
			$reading->zone_id = $rr->zone_id;
			$reading->account_id = $rr->id;
			$reading->account_number = $rr->acct_no;
			$reading->meter_number = $rr->meter_number1;
			$reading->period = $per1;
			$reading->curr_reading = 0;
			$reading->prev_reading = $prev_reading;
			$reading->status = 'active';
			$reading->date_read = $current_read_date;
			$reading->init_reading = $prev_reading;
			$reading->bill_stat = 'unbilled';
			$reading->current_consump = 0;
			$reading->prev_read_date = $prev_read_date;
			$reading->curr_read_date = $current_read_date;
			$reading->save();

		}//


		//~ echo '<pre>';
		//~ print_r($res1->toArray());

		return array('stat'=>0, 'count1' => ($res2+$xx), 'total_count' =>$users11);

	}


	function getReadingInformaion1($period1, $cid)
	{

		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');

		$my_reading_period = ReadingPeriod::where('period', 'like', $period1.'%')->first();
		$due_dates2 = json_decode($my_reading_period->due_dates2,1);
		$fine_dates = json_decode($my_reading_period->fine_dates,1);
		$read_dates = json_decode($my_reading_period->read_dates,1);

		$discon_date = [];
		foreach($due_dates2 as $kk => $vv)
		{
			$discon_date[$kk] = date('Y-m-d', strtotime($vv.' + 5 days '));
		}



		/*
		 
		$credentials = array(
			'username' => 'reading1',
			'password' => '123456'
		);

		try{if (! $token = JWTAuth::attempt($credentials)) {return response()->json(['error' => 'invalid_credentials'], 401);}}
		catch (JWTException $e) {return response()->json(['error' => 'could_not_create_token'], 500);}

		$user = Auth::User();
		$user_id = $user->id;
		echo $token;
		die();

		*/
		//$officer= HwdOfficials::where('uid', '5')->first();

		$officer= HwdOfficials::where('uid', (int) $cid)->first();

		if(!$officer){
			return array('status' => 'error', 'msg' => 'login failed');
		}

		if(empty($officer->zones)){
			return array('status' => 'error', 'msg' => 'No Zone');
		}

		$zones = array_filter(explode('|', $officer->zones));

		$reading_period1 = ReadingPeriod::where('period', 'like', $period1.'%')
											//->where('status', 'completed')
											->orderBy('period', 'desc')
											->first();
											//~ ->limit(1)
											//~ ->get();

		 //~ $reading_period1 = @$reading_period1[0];

		 if(!$reading_period1)
		 {
			return array('status' => 'error', 'msg' => 'No data for period '.date('F Y',  strtotime($period1) ).'.');
		 }
		 
		 $due_date2 = json_decode($reading_period1->due_dates2);
		 $fine_dates = json_decode($reading_period1->fine_dates);
		 
		 //~ echo '<pre>';
		 //~ print_r($reading_period1->toArray());
		 //~ die();
		 //~ echo $reading_period1->due_dates2;
		 
		 if(!$due_date2)
		 {
			return array('status' => 'error', 'msg' => 'No Due Date is assigned.');
		 }
		 
		 $due_date2  = (array) $due_date2;
		 $fine_dates = (array) $fine_dates;
		 
		 //Reading Schedule
		 $read_scheds = json_decode($reading_period1->read_dates);
		 
		 if(!$read_scheds)
		 {
			return array('status' => 'error', 'msg' => 'No Schedule is assigned.');
		 }
		 
		 $read_scheds = (array) $read_scheds;		 
		 
		 
		 

		 //~ echo '<pre>';
		 //~ echo $period1;
		 //~ echo '<br>';
		 //~ print_r($reading_period1->toArray());
		 //~ die();

		$period1 = date('Y-m', strtotime($period1));
		
		

		$acct1 = Accounts::
				 whereIn('zone_id',  $zones)
				 ->with([
				 'read_officer_curr' => function($query) use($period1){
						$query->where('period', 'like', $period1.'%');
					}, 
				 'billing_41'=> function($q1)use($period1){
						$q1->where('period', 'like', $period1.'%');
					},
				 'arrear_last'=> function($q1)use($period1){
						$q1->where('period', 'like', $period1.'%');
					},
				 'last_gps_data',
				 'nwb_billing' => function($q1)use($period1) {
						$q1->where('date1', 'like', $period1.'%');
					}
				 ])
				 ->where('status','!=','deleted')
				 ->orderBy('route_id', 'asc')
				 ->paginate(10000)
				 ->toArray();
		 
		 //~ echo '<pre>';
		 //~ print_r($period1);
		 //~ print_r($acct1);
		 //~ die();

		// ee($acct1, __FILE__, __LINE__);
		 
		 
		$my_per 		= date('Y-m-01', strtotime($period1.' - 1 month'));
		$read_period1 	= ReadingPeriod::where('period', $my_per)->first();
		$read_per_data  = (array) json_decode(@$read_period1->read_dates);
		
		
		

		$res1 = array();
		$x=0;
		foreach($acct1['data'] as $kk => $vv)
		{
			
				if(empty($due_date2[$vv['zone_id']]))
				{
					return array('status' => 'error', 'msg' => 'No Due Date is assigned.');
				}//
			
				$con_ave = cons_average($period1, $vv['id']);

	  		    $my_arrear = 0;
	  		    $my_bill   = 0;
	  		    $my_dis    = 0;
				
			
				if(!empty($vv['arrear_last'])){
					//Meron arrear sa current period
					$my_arrear = $vv['arrear_last']['amount'];
				}else{
					//Walang arrear sa current period
					$led_data1 = LedgerData::where('status', 'active')
									->where('acct_id', $vv['id'])
										->orderBy('id', 'desc')
											->first();
											
					$my_arrear = @$led_data1->ttl_bal;
				}//

				if(!empty($vv['billing_41'])){
					//Meron Billing sa current period
					$my_bill = $vv['billing_41']['curr_bill'];
					$my_dis  = $vv['billing_41']['discount'];
				}else{
					//Walang Billing sa current period
				}
				
				
				$curr_read = @$vv['read_officer_curr']['curr_reading'];
				$prev_read = @$vv['read_officer_curr']['prev_reading'];

				if(!empty(@$vv['read_officer_curr']['init_reading']))
				{
					$prev_read = $vv['read_officer_curr']['init_reading'];
				}

				$kk_v = $x;

				$route_iid = $vv['route_id'];
				if(empty($vv['route_id']))
				{
					$route_iid = 9999;
				}
				
				
				$dis111 = 0;//percentage
				$dis_desc = '';
				$max_dis_cons = 30;
				
				if(!empty($vv['acct_discount']))
				{
					$discount001 = BillingMeta::find($vv['acct_discount']);
					if($discount001)
					{
						$dis111 = $discount001->meta_value;
						$dis_desc = strtoupper($discount001->meta_name);
					}
				}
				
				$prev_read_date = @$read_per_data[$vv['zone_id']];
				
				if(empty(@$read_per_data[$vv['zone_id']])){
					$prev_read_date  = @$vv['read_officer_curr']['prev_read_date'];
				}


				// NOW-WATER
				$non_water_bill = 0;
				if(!empty(@$vv['nwb_billing'])) {
					foreach(@$vv['nwb_billing'] as $k2 => $v2) {
						$non_water_bill += $v2['amt_1'];
					}
				}

				
				
				$res1[$kk_v]['acct_n'] = $vv['id'];
				$res1[$kk_v]['acct_n_txt'] = $vv['acct_no'];
				$res1[$kk_v]['mtr_n'] = $vv['meter_number1'];
				$res1[$kk_v]['prv'] = $prev_read;
				$res1[$kk_v]['zn'] = $vv['zone_id'];
				$res1[$kk_v]['prd'] = $period1;
				$res1[$kk_v]['fname'] = $vv['lname'].', '.$vv['fname'].' '.$vv['mi'];
				$res1[$kk_v]['addr'] = $vv['address1'];
				$res1[$kk_v]['route_id'] = $route_iid;
				$res1[$kk_v]['curr_read'] = (int)$curr_read;
				$res1[$kk_v]['acct_stat'] = $vv['acct_status_key'];
				$res1[$kk_v]['arrear'] = $my_arrear;
				$res1[$kk_v]['discount'] = $vv['acct_discount'];
				$res1[$kk_v]['mtr_size'] = $vv['meter_size_id'];
				$res1[$kk_v]['pen_exemp'] = $vv['pen_exempt'];
				$res1[$kk_v]['cons_ave'] = $con_ave[0]->c_ave;
				$res1[$kk_v]['acct_typ'] = $vv['acct_type_key'];
				$res1[$kk_v]['prv_date'] = $prev_read_date;
				$res1[$kk_v]['cur_date'] = @$read_scheds[$vv['zone_id']];
				$res1[$kk_v]['due_date'] = @$due_date2[$vv['zone_id']];
				$res1[$kk_v]['fine_date'] = @$fine_dates[$vv['zone_id']];
				// $res1[$kk_v]['disc_date'] = date('Y-m-d', strtotime(@$due_date2[$vv['zone_id']].' + 5 WEEKDAYS'));
				$res1[$kk_v]['disc_date'] = date('Y-m-d', strtotime(@$due_date2[$vv['zone_id']].' + 5 DAYS'));
				$res1[$kk_v]['max_dis_cons'] = $max_dis_cons;
				$res1[$kk_v]['dis_per']  = $dis111;
				$res1[$kk_v]['dis_desc'] = $dis_desc;
				$res1[$kk_v]['my_bill']  = $my_bill;
				$res1[$kk_v]['my_cons']  = @$vv['read_officer_curr']['current_consump'];
				$res1[$kk_v]['my_dis']   = $my_dis;
				$res1[$kk_v]['lat1']     = empty(@$vv['last_gps_data']['lat1'])?'':@$vv['last_gps_data']['lat1'];
				$res1[$kk_v]['lon1']     = empty(@$vv['last_gps_data']['lng1'])?'':@$vv['last_gps_data']['lng1'];
				$res1[$kk_v]['cluster']  = empty($vv['cluster'])?'':$vv['cluster'];

				$res1[$kk_v]['new_discon_date']   = $discon_date[$vv['zone_id']];
				$res1[$kk_v]['new_penalty_date']   = $fine_dates[$vv['zone_id']];
				$res1[$kk_v]['nwb_arrear']   = @$vv['arrear_last']['nwb'];
				$res1[$kk_v]['arrear2']   = @$vv['arrear_last']['amount'] - @$vv['arrear_last']['nwb'];
				$res1[$kk_v]['nwb_bill']   = @$non_water_bill + @$vv['arrear_last']['nwb'];

				
				$x++;
		}//

		$acct1['data'] = $res1;

		// ee($res1, __FILE__, __LINE__);

		//echo '<pre>';
		//print_r($acct1);

		//~ echo '<pre>';
		//~ print_r($acct1);
		//~ die();

		//~ foreach($acct1['data'] as $kk => $vv)
		//~ {
			//~ echo @$vv['fname'].' '.@$vv['lname'];
			//~ echo '<br />';
		//~ }
		//~ die();


		return array(
			'status' => 'sucess',
			'msg' => 'Success',
			'data' => $acct1,
		);

	}//



	function getZones001($uid)
	{

		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
		

		$res1  = HwdOfficials::where('uid', $uid)->first()->toArray();
		$zones_arr = array_filter(explode('|', $res1['zones']));
		$my_zones = Zones::whereIn('id', $zones_arr)->get()->toArray();
		$my_type  = AccountMetas::where('meta_type', 'account_type')->get()->toArray();
		$my_msize = BillingMeta::where('meta_type', 'meter_size')->get()->toArray();
		$bb1 = BillingRateVersion::orderBy('id','desc')->first();
		
		###PERIOD
		$period1 = ReadingPeriod::orderBy('id','desc')->first()->toArray();
		$period1['fine_dates'] = json_decode($period1['fine_dates'], true);
		$period1['due_dates2'] = json_decode($period1['due_dates2'], true);
		$period1['read_dates'] = json_decode($period1['read_dates'], true);
		$period1['period_name'] = date('F Y', strtotime($period1['period']));
		###PERIOD
		
		// ee($period1, __FILE__, __LINE__);
		
		$eeee =  array(
					'zones'=>$my_zones, 
					'vrates'=>$bb1->toArray(), 
					'atypes' => $my_type,
					'msizes' => $my_msize,
					'period' => $period1
				);
		return $eeee;
		//~ return $my_zones;
		
		echo '<pre>';
		print_r($eeee);
		
		//~ print_r($zones_arr);
		//~ print_r($my_zones);
		//~ print_r($bb1->toArray());
		
		$dd1 = json_decode($bb1->meta_data);
		foreach($dd1 as $d){
			$d->dd1 = json_decode($d->meta_data);
		}
		print_r($dd1);
		
		//whereIn
	}


	//upload_reading_data
	function uploadReadingData()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');

		$data = json_decode(file_get_contents('php://input'), true);


		$rr1 = $data['dd1'][0];

		$rdd1 = ReadingPeriod::where('period','like', $rr1['prd'].'%')
					//~ ->where('status', 'completed')
					->first();


		if(!$rdd1)
		{
			return  array('status' => 'failed', 
					'msg' => 'Reading period '.date('F Y', strtotime($rr1['prd'].'-1')).' is not initialize, onprogress or not yet created. Please contact billing admin');
		}


		foreach($data['dd1'] as  $dd1)
		{

			$acct1_exist = Accounts::where('id', $dd1['acct_n'])->first();

			if(!$acct1_exist)
			{
				continue;
			}


			$read1 =
					Reading::where('account_id', $dd1['acct_n'])
						->where('period', 'like', $dd1['prd'].'%')
								->first();

			if($read1)
			{

				$cur = (int) $dd1['cur'];


				$prv = (int) $dd1['prv'];
				$c_prv =  (int) $read1->prev_reading;


				$mtr_num = trim($dd1['mtr_n']);
				$C_mtr_num = trim($acct1_exist->meter_number1);

				if($mtr_num != $C_mtr_num){
					$read1->meter_number = trim($dd1['mtr_n']);
					$acct1_exist->meter_number1 = trim($dd1['mtr_n']);
					$acct1_exist->save();
					//ADD TO THE LEDGER PLEASE
					HwdLedgerCtrl::MeterNumberChange($acct1_exist->id, array(trim($dd1['mtr_n']), ' change '));

				}


				if($prv != $c_prv){
					$read1->prev_reading = $prv;
					//ADD TO THE LEDGER PLEASE
				}


				$pre = (int) $read1->prev_reading;
				$cum = 0;

				if($pre > $cur){
					$cum = null;
				}else{
					$cum = $cur - $pre;
				}



				$read1->curr_reading = $dd1['cur'];
				$read1->current_consump = $cum;
				$read1->bill_stat = 'unbilled';
				$read1->curr_read_date = date('Y-m-d');
				$read1->save();


			}else{

				$prev_per = date('Y-m-01',strtotime($dd1['prd'].'-01 -1 Month'));

				$prev_reading = get_reading_val($dd1['acct_n'], $prev_per);

				$current_reading = (int)  trim($dd1['cur']);

				$consump = $current_reading - $prev_reading;

				$reading1 = new Reading;
				$reading1->zone_id = $dd1['zn'];
				$reading1->account_id = $dd1['acct_n'];
				$reading1->account_number = $dd1['acct_n_txt'];
				$reading1->meter_number = $dd1['mtr_n'];
				$reading1->period = $dd1['prd'].'-01';
				$reading1->curr_reading = trim($dd1['cur']);
				$reading1->status = 'active';
				$reading1->curr_read_date = date('Y-m-d');
				$reading1->bill_stat = 'unbilled';
				$reading1->prev_reading = $prev_reading;
				$reading1->init_reading = $prev_reading;
				$reading1->current_consump = $consump;
				$reading1->save();
			}

			$route_id = @$dd1['route_id'];

			if($route_id != 0)
			{
				$acct1_exist->route_id = $route_id;
				$acct1_exist->save();

				$has_route =
						CustomerRoute::where('route_num', $route_id)
							->first();

				if(!$has_route){
					$new_route = new CustomerRoute;
					$new_route->route_num = $route_id;
					$new_route->save();
				}

			}


		}//endforeach

		//echo '<pre>';
		return  array('status' => 'done');
		//$aa = json_decode($_POST['dd1']);
		//return array('aaa'=>key($_POST)));
		//echo json_encode($_POST['dd1']);
		//return array('data1' => json_decode($_POST['dd1']));
	}


	
	//upload_reading_data
	function update_gps_data_to_server()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		
		$data = json_decode(file_get_contents('php://input'), true);
		/*
		[
		{"id":7,"mtr_n":"18NB053205","lng":"3","lat":"3"},
		{"id":6,"mtr_n":"18NB052471","lng":"3","lat":"3"},
		{"id":21,"mtr_n":"18463411","lng":"3","lat":"3"}
		]
		*/
		//GpsAcct
		//~ $data_save = array();
		foreach($data['dd1'] as  $dd1)
		{
			$is_exists = GpsAcct::where('acct_id', (int) $dd1['id'])
							->where('mtr_n', trim($dd1['mtr_n']))
								->where('stat', 'active')
									->first();
			
			if($is_exists){
				$is_exists->lng1 = trim($dd1['lng']);
				$is_exists->lat1 = trim($dd1['lat']);
				$is_exists->save();
			}else{
				$new_gps_acct = new GpsAcct;
				$new_gps_acct->acct_id 	= (int) $dd1['id'];
				$new_gps_acct->mtr_n 	= trim($dd1['mtr_n']);
				$new_gps_acct->lng1 	= trim($dd1['lng']);
				$new_gps_acct->lat1 	= trim($dd1['lat']);
				$new_gps_acct->stat 	= 'active';
				$new_gps_acct->save();
			}
			
		}
				
		//~ GpsAcct::upsert($data_save,['acct_id','mtr_n','stat'],['lng1','lat1']);
		
		return array('stat'=>100, 'msg' => 'Upload GPS data complete');
		
	}//
	
	//upload_reading_data
	function uploadReadingData_ReadNBill()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		
		$bill_ctrl = new BillingCtrl;


		$data = json_decode(file_get_contents('php://input'), true);


		$rr1 = $data['dd1'][0];

		$rdd1 = ReadingPeriod::where('period','like', $rr1['prd'].'%')->first();
		
		$last_bill_rates = BillingRateVersion::orderBy('id','desc')->first();


		if(!$rdd1)
		{
			return  array('status' => 'failed', 
							'msg' => 'Reading period '.date('F Y', strtotime($rr1['prd'].'-1')).' is not initialize, onprogress or not yet created. Please contact billing admin');
		}


		$un_upload = [];
		foreach($data['dd1'] as  $dd1)
		{

			$acct1_exist = Accounts::where('id', $dd1['acct_n'])->first();

			if(!$acct1_exist)
			{
				continue;
			}
			
			/**/
			//SAVE CLUSSTER
			$cluster = strtoupper(@$dd1['cluster']);
			if($acct1_exist->cluster != $cluster)
			{
				$acct1_exist->cluster = $cluster;
				$acct1_exist->save();
			}//
			/**/
			

			$read1 =
					Reading::where('account_id', $dd1['acct_n'])
						->where('period', 'like', $dd1['prd'].'%')
							->first();
								
			
			$read_data1 = null;
			
			
			$reading_id = 0;	
			
			$consump = 0;				
			$prev_reading = 0;				


			//******************** */
			//******************** */
			//******************** */
			$route_id = @$dd1['route_id'];

			if($route_id != 0)
			{
				$acct1_exist->route_id = $route_id;
				$acct1_exist->save();

				$has_route =
						CustomerRoute::where('route_num', $route_id)
							->first();

				if(!$has_route){
					$new_route = new CustomerRoute;
					$new_route->route_num = $route_id;
					$new_route->save();
				}
			}
			//******************** */
			//******************** */
			//******************** */


			

			if($read1)
			{

				$cur = (int) $dd1['cur'];


				// NOT ALLOW TO UPLOAD EXISTING READING
				// NOT ALLOW TO UPLOAD EXISTING READING
				$db_curr_reading = (int) @$read1->curr_reading;
				if( $db_curr_reading > 0 )
				{
					$un_upload[] = $dd1;
					continue;
				}
				// NOT ALLOW TO UPLOAD EXISTING READING - END
				// NOT ALLOW TO UPLOAD EXISTING READING - END


				$prv = (int) $dd1['prv'];
				$c_prv =  (int) $read1->prev_reading;


				$mtr_num = trim($dd1['mtr_n']);
				//$C_mtr_num = trim($read1->meter_number);
				$C_mtr_num = trim($acct1_exist->meter_number1);

				if($mtr_num != $C_mtr_num)
				{
					$read1->meter_number = trim($dd1['mtr_n']);
					$acct1_exist->meter_number1 = trim($dd1['mtr_n']);
					$acct1_exist->save();
					//ADD TO THE LEDGER PLEASE
					HwdLedgerCtrl::MeterNumberChange($acct1_exist->id, array(trim($dd1['mtr_n']), ' change '));
				}
				
				


				if($prv != $c_prv)
				{
					$read1->prev_reading = $prv;
					//ADD TO THE LEDGER PLEASE
				}


				$pre = (int) $read1->prev_reading;
				$cum = 0;

				if($pre > $cur){
					$cum = null;
				}else{
					$cum = $cur - $pre;
				}
				
				

				$prev_reading    = (int) @$dd1['prv'];
				$current_reading = (int) @$dd1['cur'];				
				$consump         = (int) @$dd1['cum'];
				
				$read1->prev_reading = $prev_reading;
				$read1->curr_reading = $current_reading;
				$read1->current_consump = $consump;
				$read1->bill_stat = 'billed';
				//~ $read1->curr_read_date = date('Y-m-d');
				$read1->curr_read_date = @$dd1['cur_date'];
				$read1->officer_id = @$dd1['off_id'];
				$read1->save();
				
				$reading_id = $read1->id;
				
				$read_data1 = $read1;
				
			}else{

				$prev_per = date('Y-m-01',strtotime($dd1['prd'].'-01 -1 Month'));

				$prev_reading = get_reading_val($dd1['acct_n'], $prev_per);
				$current_reading = (int)  trim($dd1['cur']);
				$consump = $current_reading - $prev_reading;

				$prev_reading    = (int) $dd1['prv'];;
				$current_reading = (int) $dd1['cur'];				
				$consump         = (int) $dd1['cum'];
				
				$reading1 = new Reading;
				$reading1->zone_id = $dd1['zn'];
				$reading1->account_id = $dd1['acct_n'];
				$reading1->account_number = $dd1['acct_n_txt'];
				$reading1->meter_number = $dd1['mtr_n'];
				$reading1->period = $dd1['prd'].'-01';
				$reading1->curr_reading = trim($dd1['cur']);
				$reading1->status = 'active';
				//~ $reading1->curr_read_date = date('Y-m-d');
				$reading1->curr_read_date = @$dd1['cur_date'];
				$reading1->bill_stat = 'billed';
				$reading1->prev_reading = $prev_reading;
				$reading1->init_reading = $prev_reading;
				$reading1->current_consump = $consump;
				$reading1->officer_id = @$dd1['off_id'];
				$reading1->save();

				$reading_id = $reading1->id;
				
				$read_data1 = $reading1;

			}






			
			///INIT CLUSTER // THIS WILL NOT EXECUTE BILL for INITIALIZATION PURPOSE ONLY
			//#1110000
			$period101   =  strtotime($dd1['prd'].'-01');
			$min1        =  strtotime(INIT_INSTALL_period);
			
			if($period101 <= $min1){
				continue;
			}
			///INIT END
			
			
			if($reading_id == 0)
			{
				//LOG
				continue;
			}else{
				
				
				$cur_cons = (int) $dd1['cur'];
				
				if($cur_cons <= 0)
				{
					continue;
				}

				try {
					@$bill_ctrl->rebill_from_reading(
						$read_data1->id, 
						$read_data1->account_id, 
						$read_data1->account_number
					);
				} catch (Exception $e) {
					return  array('status' => 'failed', 'msg'=> $read_data1->account_number);
				}				
				

			}//ENDIF


		}//endforeach

		//echo '<pre>';
		// return  array('status' => 'failed', 'msg'=>'333333');
		return  array('status' => 'done', 'un_upload' => $un_upload);
		//$aa = json_decode($_POST['dd1']);
		//return array('aaa'=>key($_POST)));
		//echo json_encode($_POST['dd1']);
		//return array('data1' => json_decode($_POST['dd1']));
	}




	function OfficerLogin()
	{
		header('Access-Control-Allow-Origin: *');
		//return array('stat'=>'Done');
		/**/
		$data = json_decode(file_get_contents('php://input'), true);

		$credentials = array(
			'username' => $data['username1'],
			'password' => $data['password1']
		);

		/**/
		try{if (! $token = JWTAuth::attempt($credentials)) {return response()->json(['error' => 'invalid_credentials'], 401);}}
		catch (JWTException $e) {return response()->json(['error' => 'could_not_create_token'], 500);}

		$user = Auth::User();
		$user_id = $user->id;
		$fname = ucwords(strtolower($user->name));

		return array('token1' => $token, 'uid' => $user_id, 'ok' =>true, 'fname' => $fname);

		echo $token;
		die();

		return  $data;

		echo '<pre>';
		print_r($data);
		return array('stat'=>'Done');
		/**/
		//[app-scripts] [11:32:21]  console.log: {"username1":"reading1","password1":"123456"}
	}


	function addReadingPeriod(Request $request)
	{

		$customMessages = [
			'period_year.required' => 'Period year is required',
			'period_month.required' => 'Period month is required',
		 ];

		$request->validate([
			'period_year' => 'required',
			'period_month' => 'required',
			//'period_status' => 'required',
			//'address' => 'required',
			//'phone' => 'required',
		], $customMessages);

		$period_id = $_POST['period_id'];
		$period_year = $_POST['period_year'];
		$period_month = $_POST['period_month'];
		//$period_status = $_POST['period_status'];


		$date1 = date('Y-m', strtotime($period_year.'-'.$period_month.'-1'));

		$reading1 = ReadingPeriod::where('period', 'like', $date1.'%')->first();


		if(!$reading1)
		{
				$new_reading  = new ReadingPeriod;
				$new_reading->status = 'pending';
				$new_reading->period = $date1.'-1';
				$new_reading->save();
		}else{
				$reading1->status = 'pending';
				$reading1->save();
		}


		$request->session()->flash('success', 'Reading Period Updated');
		return Redirect::to(URL::previous() . "#reading_period");

	}



	function ReadingPeriodInitializeStart($date1, Request $request)
	{
		$rdd1 = ReadingPeriod::where('period', $date1)->first();

		if($rdd1){
			$rdd1->status = 'ongoing';
			$rdd1->save();
		}

		$request->session()->flash('success', 'Reading period updated');
		return Redirect::to(URL::previous() . "#reading_period");

	}

	function addRoute01(Request $request)
	{
			//CustomerRoute
			$route_id = (int) $_POST['route_id'];
			$route_num = (int) $_POST['route_num'];
			$route_addr = $_POST['route_addr'];

			if($route_id == 0){}

			$customRoute =
					CustomerRoute::where('id', $route_id)->first();

			if(!$customRoute){
				$newR = new CustomerRoute;
				$newR->route_num = $route_num;
				$newR->route_addr = trim($route_addr);
				$newR->save();
			}else{
				$customRoute->route_num = $route_num;
				$customRoute->route_addr = trim($route_addr);
				$customRoute->save();
			}

			$request->session()->flash('success', 'Reading Route updated');
			return Redirect::to(URL::previous() . "#route");

	}//


	function getTop3ReadingHtml1($account_id)
	{
			//~ $readings1 = Reading::where('status', 'active')->orderBy('period')->limit(3)->get();
			//~ echo '<pre>';
			//~ print_r($readings1->toArray());

			$arr_dates    = array();
			$arr_dates[] = date('Y-m');

			for($x=1;$x<=3;$x++)
			{
				$arr_dates[] = date('Y-m', strtotime('- '.$x.' month'));
			}

			$readings1 = Reading::where('status', 'active')
								->where(function($query)use($arr_dates){
										$query->where('period', 'like', $arr_dates[0].'%');
										foreach($arr_dates as $vv1)
										{
											$query->orWhere('period', 'like', $vv1.'%');
										}
								})
								->where('account_id',  $account_id)
								->orderBy('period', 'desc')
								->get();

			$new_cont = array();

			foreach($arr_dates as $arr_d)
			{
				$new_cont[$arr_d] = array();
			}

			foreach($readings1 as $rr1)
			{
				$per1 = date('Y-m', strtotime($rr1->period));
				$new_cont[$per1] = $rr1->toArray();
			}

			echo '<ul class="item_list1">';
			foreach($new_cont as $kk=>$rr1)
			{

				if(!empty($rr1))
				{
					$dd1 =  date('Y/m', strtotime($rr1['period']));
					echo '
						<li>'.date('F Y', strtotime($rr1['period'])).'   <span> <a href="/billing/reading/'.$dd1.'/filter/'.$rr1['account_number'].'/none/none/none/#accounts">'.$rr1['prev_reading'].' / '.$rr1['curr_reading'].' / '.$rr1['current_consump'].'</a></span></li>
					';
				}else{
					echo '<li>'.date('F Y', strtotime($kk)).' <span>---</span></li>';
				}
			}
			echo '</ul>';

	}//

	function getTop3BillingHtml1($account_id)
	{

			$acct1 = Accounts::where('id', $account_id)->first();



			$arr_dates    = array();
			$arr_dates[] = date('Y-m');


			echo '<ul class="item_list1">';

			$dd1 =  date('Y/m');
			echo '
				<li>'.date('F Y').'   <span><a href="/billing/billing/'.$dd1.'/filter/'.$acct1->acct_no.'/none/none/all/#accounts">View</a></span></li>
			';

			for($x=1;$x<=3;$x++)
			{
				$dd1 =  date('Y/m', strtotime('- '.$x.' month'));
				$arr_dates[]  = $vv= date('Y-m', strtotime('- '.$x.' month'));
				echo '
					<li>'.date('F Y', strtotime($vv)).'   <span><a href="/billing/billing/'.$dd1.'/filter/'.$acct1->acct_no.'/none/none/all/#accounts">View</a></span></li>
				';
			}
			echo '</ul>';

			//~ echo  '<pre>';
			//~ print_r($arr_dates);
	}





}
