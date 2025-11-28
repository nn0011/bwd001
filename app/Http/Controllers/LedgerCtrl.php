<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
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
use App\Role;
use App\Collection;
use App\AgingRecievable;
use App\Reports;
use App\HwdLedger;
use App\Bank;
use App\ReadingPeriod;
use App\CustomerRoute;
use App\LedgerData;
use App\BillingAdjMdl;
use App\BillingDue;
use App\Arrear;



use PDF;
use Fpdf;

use App\Http\Controllers\HwdLedgerCtrl;
use NWBill;

class LedgerCtrl extends Controller
{
	
		function fix_ledger_zorting($acct_id)
		{
			$res1 = LedgerData::where('status','active')
						->where('acct_id', $acct_id)
						->orderBy('date01', 'desc')
							->orderBy('zort1', 'desc')
								->orderBy('id', 'desc')
									->get();
			
			$xx = 0;
			foreach($res1 as $r1)
			{
				$r1->zort1 = $xx;
				$r1->save();
				$xx-= 10;
			}
			
			echo 'Done';
			
			//~ echo '<pre>';
			//~ print_r($res1->toArray());
			
		}//
	
	
	
		function cancel_this_penalty($leds_id)
		{
			$is_ok = LedgerData::find($leds_id);
			$last1 = LedgerData::where('acct_id', $is_ok->acct_id)
						->where('status', 'active')
						->orderBy('date01', 'desc')
							->orderBy('zort1', 'desc')
							->orderBy('id', 'desc')
								->first();
			
			if($is_ok->id != $last1->id){
				//~ return array('status'=>'0', 'msg'=>'Cancel Previous not allowed');
			}
			
			$is_ok->status = 'disabled';
			$is_ok->save();

			$billDue = BillingDue::where('acct_id', $is_ok->acct_id)
							->where('period', $is_ok->period)
								->where('id', $is_ok->reff_no)
									->where('due_stat', 'active')
										->first();
			if($billDue){
				$billDue->due_stat = 'disabled';
				$billDue->save();
			}
			
			return array('status'=>'0', 'msg'=>'Cancel Previous not allowed');			
		}
	
		function cancel_this_adjustment($leds_id)
		{
			$is_ok = LedgerData::find($leds_id);
			$last1 = LedgerData::where('acct_id', $is_ok->acct_id)
						->where('status', 'active')
						->orderBy('date01', 'desc')
							->orderBy('zort1', 'desc')
							->orderBy('id', 'desc')
								->first();
			
			if($is_ok->id != $last1->id){
				return array('status'=>'0', 'msg'=>'Cancel Previous not allowed');
			}
			
			$is_ok->status = 'disabled';
			$is_ok->save();

			$bill_adjust = BillingAdjMdl::where('acct_id', $is_ok->acct_id)
							->where('id', $is_ok->reff_no)
								->where('status', 'active')
									->first();
			if($bill_adjust){
				$bill_adjust->status = 'disabled';
				$bill_adjust->save();
			}
			
			return array('status'=>'0', 'msg'=>'Cancel Previous not allowed');
		}//


		
		function cancel_last_billing($leds_id)
		{
			$is_ok = LedgerData::find($leds_id);
			$last1 = LedgerData::where('acct_id', $is_ok->acct_id)
						->where('status', 'active')
						->orderBy('date01', 'desc')
							->orderBy('zort1', 'desc')
							->orderBy('id', 'desc')
								->first();
			
			if($is_ok->id != $last1->id){
				return array('status'=>'0', 'msg'=>'Cancel Previous not allowed');
			}
			
			$is_ok->status = 'disabled';
			$is_ok->save();

			$bill1 = BillingMdl::where('period', $is_ok->period)
						->where('account_id', $is_ok->acct_id)
							->where('status', 'active')
								->first();
			
			if($bill1){
				$bill1->status = 'disabled';
				$bill1->save();
			}
			
			return array('status'=>'0', 'msg'=>'Cancel Previous not allowed');
		}//
		
		
		
		function disable_ledger_item($led_id)
		{
			$is_ok = LedgerData::find($led_id);
			$is_ok->status = 'disabled';
			$is_ok->save();
		}
		
		function get_led_item($led_id)
		{
			return LedgerData::find($led_id);
		}
		
		function recalculate_v2()
		{
			
			return;
			return;
			return;
			
			$acct_id = @$_GET['acct_id'];

			$led001 = LedgerData::where('acct_id', $acct_id)
									->where('status', 'active')
									->orderBy('date01', 'asc')
									->orderBy('zort1', 'asc')
									->orderBy('id', 'asc')
									//~ ->orderBy('date01', 'asc')
									->get();

			$acct1 = Accounts::find($_GET['acct_id']);


			$ttl_bal = 0;
			foreach($led001 as $ll)
			{

				if($ll->led_type == 'beginning'){
					$ttl_bal = $ll->ttl_bal;
					continue;
				}

				if($ll->led_type == 'billing'){
					$ttl_bal+=$ll->billing-$ll->discount;
				}

				if($ll->led_type == 'payment'){
					$ttl_bal-=$ll->payment;
				}

				if($ll->led_type == 'penalty'){
					$ttl_bal+=$ll->penalty;
				}

				if($ll->led_type == 'adjustment'){
					$ttl_bal-=$ll->bill_adj;
				}

				$ll->ttl_bal = round($ttl_bal, 2);
				$ll->save();
			}//

			return array('status'=>1);

		}
		
		
		
		function refresh_ledger_101($acct_id)
		{
			$this->view_ledger_account_info_fix_led($acct_id);
		}
		
		function view_ledger_account_info_fix_led($acct_id)
		{
			//~ $acct_id = @$_GET['acct_id'];
			
			$led001 = LedgerData::where('acct_id', $acct_id)
							->where('status', 'active')
							->orderBy('date01', 'asc')
							->orderBy('zort1', 'asc')
							->orderBy('id', 'asc')
							->get();
								
			
			$ttl_bal = 0;
			
			foreach($led001 as $l1)
			{
				if($l1->led_type == 'beginning'){
					$ttl_bal += $l1->arrear + $l1->bill_adj;
					break;
				}
			}//


			$non_water_arr = [
				'cancel_cr_nw','cr_nw','cr_nw_debit','nw_cancel','or_nw', 'or_nw_debit'
			];
			
				
			foreach($led001 as $l1)
			{
				
				$prev_ttl = $ttl_bal;
				
				
				$my_adjust = @$l1->bill_adj;
				
				
				if($l1->led_type == 'beginning'){
					continue;
					//~ $ttl_bal -= $l1->discount;
				}				
				
				if($l1->led_type == 'witholding'){
					$ttl_bal -= $l1->discount;
				}
				
				if($l1->led_type == 'wtax'){
					$ttl_bal -= $l1->payment;
				}
				
				if($l1->led_type == 'billing'){
					$ttl_bal += ($l1->billing - $l1->discount) - $l1->bill_adj;
					$l1->arrear = $prev_ttl;
				}
				
				if($l1->led_type == 'adjustment')
				{
					$ttl_bal -= $my_adjust;
				}
				
				if($l1->led_type == 'penalty'){
					$ttl_bal += $l1->penalty;
				}
				
				if($l1->led_type == 'nw_billing'){
					$ttl_bal += $l1->billing;
				}
				

				if(in_array($l1->led_type,$non_water_arr)){
					
				}

				if($l1->led_type == 'or_nw'){
					$ttl_bal -= ($l1->payment + $my_adjust);
				}
				if($l1->led_type == 'nw_cancel'){
					$ttl_bal -= ($l1->payment + $my_adjust);
				}
				if($l1->led_type == 'cr_nw'){
					$ttl_bal -= $l1->payment;
				}
				if($l1->led_type == 'cancel_cr_nw'){
					$ttl_bal -= $l1->payment;
				}
				if($l1->led_type == 'or_nw_debit' || $l1->led_type == 'cr_nw_debit'){
					$ttl_bal -= $my_adjust;
				}

								
				if($l1->led_type == 'payment'){
					$ttl_bal -= $l1->payment;
				}
				if($l1->led_type == 'payment_cancel'){
					$ttl_bal -= ($l1->payment  + $my_adjust);
				}
				
				if($l1->led_type == 'payment_cr'){
					$ttl_bal -= ($l1->payment  + $my_adjust);
				}
				
				if($l1->led_type == 'cancel_cr'){
					$ttl_bal -= ($l1->payment  + $my_adjust);
				}
				
				
				if($l1->led_type == 'billing')
				{
					$l1->arrear = $prev_ttl;
					
					$arr1 = Arrear::where('period', $l1->period)
								->where('acct_id', $l1->acct_id)
									->first();
					if($arr1){
						$arr1->amount = $prev_ttl;
						$arr1->save();
					}

				}
				
				$l1->ttl_bal = $ttl_bal;
				$l1->save();
			}//
			
			// echo '<pre>';
			// print_r($led001->toArray());
			
		}//

		function view_ledger_account_info()
		{
			//~ $this->view_ledger_account_info_fix_led();
			//~ return;

			// NWBill::get_payables1(2650,14);

			$acct_id = @$_GET['acct_id'];

			$account_info = Accounts::find($acct_id);
			if(!$account_info){
				echo '<h1>Account Not Found</h1>';
				return;
			}

			$ledger_list1 = HwdLedgerCtrl::top20_ledger($acct_id);


			$led001 = LedgerData::where('acct_id', $acct_id)
									->where('status', 'active')
									->orderBy('date01', 'asc')
									->orderBy('zort1', 'asc')
									->orderBy('id', 'asc')								
									//~ ->orderBy('id', 'asc')
									//~ ->orderBy('date01', 'asc')
									//~ ->orderBy('created_at', 'asc')
									->get();
			
			
			
			foreach($led001 as $l1)
			{
				if($l1->led_type == 'billing')
				{
					$l1->billing01; 
					// ee($l1->toArray(), __FILE__, __LINE__);
				}//

				if($l1->led_type == 'billing' && $l1->discount <= 0)
				{
					$l1->ledger_info = str_replace('SENIOR CITIZEN','',$l1->ledger_info);
				}
			}
			
			/*
			foreach($led001 as $ll1s){
				
				if($ll1s->led_type == 'penalty'){
					$due1 = BillingDue::where('acct_id', $ll1s->acct_id)
							->where('period', $ll1s->period)
							->where('due_stat', 'active')
							->first();
					
					$ll1s->due_info = null;
					if($due1){
						$ll1s->due_info = (object) $due1->toArray();
					}
					
				}
				
			}
			echo '<pre>';
			print_r($led001->toArray());
			die();						
			*/

			// ee($led001->toArray(), __FILE__, __LINE__);

			$reading12 = Reading::where('account_id', $acct_id)
										->where('status', 'active')
											->orderBy('curr_read_date', 'asc')
											->get();


			$beginning = LedgerData::where('acct_id', $acct_id)
									->where('led_type', 'beginning')
									->where('status', 'active')
									->first();
									
									
			return view('billings.inc.billing_ledger.view_ledger_account',
						compact(
							'ledger_list1',
							'led001',
							'acct_id',
							'reading12',
							'account_info',
							'beginning'));

		}//


		function view_ledger_account_info_pdf()
		{
			$acct_id = @$_GET['acct_id'];

			$account_info = Accounts::find($acct_id);
			if(!$account_info){
				echo '<h1>Account Not Found</h1>';
				return;
			}

			$ledger_list1 = HwdLedgerCtrl::top20_ledger($acct_id);


			$led001 = LedgerData::where('acct_id', $acct_id)
									->where('status', 'active')
									->orderBy('date01', 'asc')
									->orderBy('zort1', 'asc')
									->orderBy('id', 'asc')
									//~ ->orderBy('date01', 'asc')
									//~ ->orderBy('created_at', 'asc')
									->get();

			$reading12 = Reading::where('account_id', $acct_id)
										->where('status', 'active')
											->orderBy('curr_read_date', 'asc')
											->get();


			$beginning = LedgerData::where('acct_id', $acct_id)
									->where('led_type', 'beginning')
									->where('status', 'active')
									->first();

			//~ $pdf = PDF::loadView('billings.inc.billing_ledger.acct_ledger_list_ajax22_pdf_reading', compact('acct1', 'reading12'));
			//~ return $pdf->stream($_GET['acct_id'].'.pdf');

			/*
			$pdf = PDF::loadView('billings.inc.billing_ledger.view_ledger_account_PDF',
						compact(
							'ledger_list1',
							'led001',
							'acct_id',
							'reading12',
							'account_info',
							'beginning'));



			return $pdf->stream($_GET['acct_id'].'-account-ledger.pdf');
			*/
			return view('billings.inc.billing_ledger.view_ledger_account_PDF',
						compact(
							'ledger_list1',
							'led001',
							'acct_id',
							'reading12',
							'account_info',
							'beginning'));

		}//



		function  Ledger001()
		{
			
			die();
				//~ $led1 = new LedgerData;
				//~ $led1->acct_id = '';
				//~ $led1->bill_id = '';
				//~ $led1->read_id = '';
				//~ $led1->arrear = '';
				//~ $led1->billing = '';
				//~ $led1->payment = '';
				//~ $led1->discount = '';
				//~ $led1->ledger_info = '';
				//~ $led1->period = '';
				//~ $led1->status = '';
				//~ $led1->arrear = '';

				//~ $this->add_begining(3809, array(
						//~ 'amount' => 1000
					//~ ));

				//~ LedgerCtrl::add_billing(3809, array(
						//~ 'period' => '2018-11-01',
						//~ 'amount' => 100,
						//~ 'bill_id' => 1,
						//~ 'read_id' => 1,
						//~ 'less' => 10
					//~ ));

				//~ LedgerCtrl::add_penalty(3809, array(
						//~ 'period' => '2018-11-01',
						//~ 'amount' => 10,
						//~ 'bill_id' => 1,
						//~ 'read_id' => 1
					//~ ));


				$this->add_payment(3809, array(
						//~ 'period' => '2018-11-01',
						'amount' => 1000,
					));

					//add_payment


		}//


		static function add_begining($acct_id,  $arr_data= array())
		{
				extract($arr_data);

				//~ if(empty($amount)){
					//~ return false;
				//~ }

				$led2 = LedgerData::where('acct_id', $acct_id)
				->orderBy('date01', 'desc')
							->orderBy('zort1', 'desc')
							->orderBy('id', 'desc')
							->first();

				$prev_bal = 0;
				if($led2)
				{$prev_bal = (float) $led2->ttl_bal;}

				$prev_bal += (float) @$amount;

				$led1 = new LedgerData;
				$led1->acct_id = $acct_id;
				$led1->date01 = date('Y-m-d');
				$led1->status = 'active';
				$led1->led_type = 'beginning';
				$led1->ttl_bal = $prev_bal;
				$led1->save();

				return true;
		}//


		static function add_billing($acct_id,  $arr_data= array())
		{

				$is_update = LedgerCtrl::update_billing($acct_id,$arr_data);

				if($is_update){
					return true;
				}

				extract($arr_data);

				if(empty($period)  || empty($amount)){
					return false;
				}

				$led2 = LedgerData::where('acct_id', $acct_id)
							->where('status', 'active')
							->orderBy('date01', 'desc')
							->orderBy('zort1', 'desc')
							->orderBy('id', 'desc')
							->first();

				$prev_bal = 0;
				if($led2)
				{$prev_bal = (float) $led2->ttl_bal;}

				$prev_bal += (float) @$amount;
				$prev_bal -= (float) @$less; // Discount

				$bill_date = date('Y-m-d');
				if(!empty(@$new_date11)){
					$bill_date = $new_date11;
				}


				$led1 = new LedgerData;
				$led1->acct_id = $acct_id;
				$led1->date01 = $bill_date;
				$led1->status = 'active';
				$led1->led_type = 'billing';
				$led1->billing = (float) @$amount;
				$led1->discount = (float) @$less;
				$led1->ttl_bal = $prev_bal;
				$led1->period = $period;
				$led1->bill_id = $bill_id;
				$led1->read_id = $read_id;
				$led1->save();

				return true;
		}//

		static function update_billing($acct_id,  $arr_data= array())
		{
			extract($arr_data);

			$led2 = LedgerData::where('acct_id', $acct_id)
						->where('bill_id', $bill_id)
						->where(function($query){
							$query->where('led_type', 'beginning');
							$query->orWhere('led_type', 'billing');
							$query->orWhere('led_type', 'penalty');

						})
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						->orderBy('id', 'desc')
						->first();

			if(!$led2){return false;}


			if(empty($period)  || empty($amount)){
				return false;
			}

			$led2_raw = LedgerData::where('acct_id', $acct_id)
						->where(function($query){
							$query->where('led_type', 'beginning');
							$query->orWhere('led_type', 'billing');
							$query->orWhere('led_type', 'penalty');

						})
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						->orderBy('id', 'desc')
						->limit(2)
						->get();

			$led2 = $led2_raw[1];

			$prev_bal = 0;

			if($led2)
			{$prev_bal = (float) $led2->ttl_bal;}


			//~ echo $less.'<br />';
			//~ echo $amount.'<br />';
			//~ echo $prev_bal.'<br />';

			$prev_bal += (float) @$amount;
			$prev_bal -= (float) @$less; // Discount

			$led1 = $led2_raw[0];

			$led1->date01 = date('Y-m-d');
			$led1->billing = (float) @$amount;
			$led1->discount = (float) @$less;
			$led1->ttl_bal = $prev_bal;
			$led1->save();

			$led33 =
				LedgerData::where('acct_id', $acct_id)
					->where('id', '>', $led1->id)
					->where(function($query){
						$query->where('led_type', 'payment');
						//~ $query->orWhere('led_type', 'payment');
					})
					->orderBy('date01', 'asc')
					->orderBy('zort1', 'asc')
					->orderBy('id', 'asc')
					->get();



			$ttl3 = $led1->ttl_bal;
			foreach($led33 as $l3){

				if($l3->led_type == 'payment'){
					$ttl3 -= $l3->payment;
				}elseif($l3->led_type == 'penalty'){
					$ttl3 += $l3->penalty;
				}

				$l3->ttl_bal = $ttl3;
				$l3->save();
			}


			return true;
		}



		static function add_penalty($acct_id,  $arr_data= array())
		{

			//~ return;
			//~ return;
			//~ return;

				extract($arr_data);
				//period_cur

				if(empty($period)  || empty($amount)){
					return false;
				}

				$period_short = date('Y-m', strtotime($period));
				$pen_date = @$penalty_date;

				$led2 = LedgerData::where('acct_id', $acct_id)
							->where('date01', '>=',$pen_date)
							//~ ->orderBy('zort1', 'desc')							
							->orderBy('date01', 'desc')
							->first();


				$prev_bal = 0;
				if($led2)
				{$prev_bal = (float) $led2->ttl_bal;}

				$prev_bal += (float) @$amount;
				$prev_bal -= (float) @$less; // Discount

				$led1 = new LedgerData;
				$led1->acct_id = $acct_id;
				//$led1->date01 = date('Y-m-d');
				$led1->date01 = $pen_date;
				$led1->status = 'active';
				$led1->led_type = 'penalty';
				$led1->penalty = (float) @$amount;
				$led1->ttl_bal = $prev_bal;
				$led1->period = $period;
				$led1->bill_id = $bill_id;
				$led1->read_id = $read_id;
				$led1->save();

				return true;
		}//


		static function add_payment($acct_id,  $arr_data= array())
		{
				extract($arr_data);

				if(empty($amount)){
					return false;
				}

				$led2 = LedgerData::where('acct_id', $acct_id)
							->where(function($query){
								$query->where('led_type', 'beginning');
								$query->orWhere('led_type', 'billing');
								$query->orWhere('led_type', 'penalty');
								$query->orWhere('led_type', 'payment');
							})
							->orderBy('date01', 'desc')
							->orderBy('zort1', 'desc')
							->orderBy('id', 'desc')
							->first();

				$prev_bal = 0;
				if($led2)
				{$prev_bal = (float) $led2->ttl_bal;}

				$prev_bal -= (float) @$amount;

				$new_date_pre = date('Y-m-d');
				if(!empty(@$new_date)){
					$new_date_pre = $new_date;
				}


				$led1 = new LedgerData;
				$led1->acct_id = $acct_id;
				//$led1->date01 = date('Y-m-d');
				$led1->date01 = $new_date_pre;
				$led1->status = 'active';
				$led1->led_type = 'payment';
				$led1->payment = (float) @$amount;
				$led1->ttl_bal = $prev_bal;
				$led1->ledger_info = '
					Invoice # : '.$invoice.'
					<br />
				';

				$led1->save();

				return true;
		}//

		static function add_payment_none_water($acct_id,  $arr_data= array())
		{
				extract($arr_data);

				if(empty($amount)){
					return false;
				}

				$ld1 = getLatestLeger($acct_id);
				$ttl = @$ld1->ttl_bal;

				if($ld1){
					$ttl -=  @$amount;
				}


				$led1 = new LedgerData;
				$led1->acct_id = $acct_id;
				$led1->date01 = date('Y-m-d');
				$led1->status = 'active';
				$led1->led_type = 'payment_none_water';
				$led1->payment = (float) @$amount;
				$led1->ttl_bal = $ttl;
				$led1->ledger_info = 'Invoice # : '.$invoice.'';
				$led1->reff_no  = @$reff_no;
				$led1->acct_num = @$acct_num;

				$led1->save();

				return true;
		}//

		static function add_non_water_bill($acct_id,  $arr_data= array())
		{
				if(empty($arr_data)){
					return false;
				}

				extract($arr_data);

				$ld1 = getLatestLeger($acct_id);
				$ttl = @$paya_amount;


				//~ echo '<pre>';
				//~ print_r($ld1->toArray());
				//~ die();
				//~ die();

				if($ld1){
					$ttl +=  $ld1->ttl_bal;
				}


				$led1 = new LedgerData;
				$led1->acct_id = $acct_id;
				$led1->date01 = date('Y-m-d');
				$led1->status = 'active';
				$led1->led_type = 'non_water_bill';
				$led1->billing = (float) @$paya_amount;
				$led1->ttl_bal = $ttl;
				$led1->ledger_info = @$paya_title;
				$led1->reff_no  = @$reff_no;
				$led1->acct_num = @$acct_num;
				$led1->save();

			return true;
		}



		function AccountLedgerMain()
		{



				//~ $this->Ledger001();
				//~ die();
				//~ die();
				//~ die();

				$acct_list_raw = Accounts::limit(20)
						->with(['my_zone', 'my_stat'])
						->where('status', '!=', 'deleted')
						->orderBy('id', 'desc');
						//~ ->get();

				$acct_list = $acct_list_raw->paginate(20);

				// ->map(function ($query) {
				//    $query->setRelation('ledger_last_20', $query->ledger_last_20->take(3));
				//    return $query;
				//  });
				//->toArray();

				//~ $led001 = LedgerData::where('acct_id', $acct_id)->where('status', 'active')->get();
				//~ echo '<pre>';
				//~ echo print_r($led001->toArray());
				//~ die();

				$zones = Zones::where('status', '!=', 'deleted')->get();

				return view('billings.account_ledger', compact('acct_list', 'zones'));

				echo '<pre>';
				print_r($acct_list->toArray());

		}//


		function AccountLedgerMain22($acct_num, $meter_num, $lname, $zone,$stype)
		{



				$acct_list_raw = Accounts::limit(20)
						->with(['my_zone', 'my_stat', 'ledger_data4'])
						->orderBy('old_route', 'asc');
						//->get();

				if($acct_num != 'none'){
					$acct_list_raw->where('acct_no', 'like', '%'.$acct_num);
				}

				if($meter_num != 'none'){
					$acct_list_raw->where('meter_number1', 'like', '%'.$acct_num);
				}

				if($lname != 'none'){
					$new_last = explode(',', $lname);
					if(count($new_last) == 1){
						$acct_list_raw->where('lname', 'like', $lname.'%');
					}else{
						$acct_list_raw->where('lname', 'like', trim($new_last[0]).'%');
						$acct_list_raw->where('fname', 'like', trim($new_last[1]).'%');
					}
				}

				if($zone != 'none'){
						$acct_list_raw->where('zone_id',  $zone);
				}

				if(@$stype == 3){
					//ledger_data4
					$acct_list_raw->whereDoesntHave('ledger_data4');

				}
				if(@$stype == 2){
					//ledger_data4
					$acct_list_raw->where('acct_status_key', 4);

				}

				if(@$stype == 1){
					//ledger_data4
					$acct_list_raw->where('acct_status_key', 2);

				}


				//~ $acct_list_raw->orderBy('old_route', 'asc');


				$acct_list = $acct_list_raw->paginate(200);

				// ->map(function ($query) {
				 //        $query->setRelation('ledger_last_20', $query->ledger_last_20->take(3));
				 //        return $query;
				 //  });
				//->toArray();

				$zones = Zones::where('status', '!=', 'deleted')->get();



				return view('billings.account_ledger', compact('acct_list', 'acct_num', 'meter_num', 'lname', 'zone', 'zones', 'stype'));
				echo '<pre>';
				print_r($acct_list->toArray());
		}


		function AccountLedgerGetAccountPrintPdf1History()
		{
			$acct_id = @$_GET['acct_id'];
			$acct1 = Accounts::find($_GET['acct_id']);
			$ledger_list1 = HwdLedgerCtrl::top20_ledger($acct_id);

			//~ die();

			$pdf = PDF::loadView('billings.inc.billing_ledger.acct_ledger_list_ajax22_pdf_history', compact('ledger_list1', 'acct1'));
			return $pdf->stream($_GET['acct_id'].'.pdf');

			//~ return view('billings.inc.billing_ledger.acct_ledger_list_ajax22_pdf_history', compact('ledger_list1', 'acct1'));
		}

		function AccountLedgerGetAccountPrintPdf1Reading()
		{
			$acct_id = @$_GET['acct_id'];
			$acct1 = Accounts::find($_GET['acct_id']);

			$reading12 = Reading::where('account_id', $acct_id)
										->where('status', 'active')
											->orderBy('curr_read_date', 'asc')
											->get();

			$pdf = PDF::loadView('billings.inc.billing_ledger.acct_ledger_list_ajax22_pdf_reading', compact('acct1', 'reading12'));
			return $pdf->stream($_GET['acct_id'].'.pdf');
			//~ return view('billings.inc.billing_ledger.acct_ledger_list_ajax22_pdf_reading', compact('acct1', 'reading12'));
		}


		function AccountLedgerGetAccountPrintPdf1()
		{
			$acct_id = @$_GET['acct_id'];

			$led001 = LedgerData::where('acct_id', $acct_id)
									->where('status', 'active')
									->orderBy('date01', 'asc')
									->orderBy('zort1', 'asc')
									->orderBy('id', 'asc')
									->get();

			$acct1 = Accounts::find($_GET['acct_id']);

			//~ echo '<pre>';
			//~ echo print_r($acct1->toArray());
			//~ die();

			return view('billings.inc.billing_ledger.acct_ledger_list_ajax22_pdf', compact('led001', 'acct1'));
		}


		function AccountLedgerRecalculate(Request $request)
		{
			$acct_id = @$_GET['acct_id'];

			$led001 = LedgerData::where('acct_id', $acct_id)
									->where('status', 'active')
									->orderBy('date01', 'asc')
									->orderBy('zort1', 'asc')
									->orderBy('id', 'asc')
									//~ ->orderBy('date01', 'asc')
									->get();

			$acct1 = Accounts::find($_GET['acct_id']);


			$ttl_bal = 0;
			foreach($led001 as $ll){


				if($ll->led_type == 'beginning'){
					$ttl_bal = $ll->ttl_bal;
					continue;
				}

				if($ll->led_type == 'billing'){
					$ttl_bal+=$ll->billing-$ll->discount;
				}

				if($ll->led_type == 'payment'){
					$ttl_bal-=$ll->payment;
				}

				if($ll->led_type == 'penalty'){
					$ttl_bal+=$ll->penalty;
				}

				if($ll->led_type == 'adjustment'){
					$ttl_bal-=$ll->bill_adj;
				}

				$ll->ttl_bal = round($ttl_bal, 2);
				$ll->save();
			}

			$request->session()->flash('success', 'Ledger re-process done');
			return Redirect::to(URL::previous() . "#period_request");

			//~ echo '<pre>';
			//~ print_r($led001->toArray());

		}




		static function AccountLedgerGetAccount()
		{

			$acct_id = @$_GET['acct_id'];
			$ledger_list1 = HwdLedgerCtrl::top20_ledger($acct_id);


			$led001 = LedgerData::where('acct_id', $acct_id)
									->where('status', 'active')
									->orderBy('date01', 'asc')
									->orderBy('zort1', 'asc')
									->orderBy('id', 'asc')
									//~ ->orderBy('date01', 'asc')
									//~ ->orderBy('created_at', 'asc')
									->get();

			$reading12 = Reading::where('account_id', $acct_id)
										->where('status', 'active')
											->orderBy('curr_read_date', 'asc')
											->get();


			$beginning = LedgerData::where('acct_id', $acct_id)
									->where('led_type', 'beginning')
									->where('status', 'active')
									->first();

			foreach($led001 as $ll)
			{
				$ll->bill_ref = '';
				if($ll->led_type == 'billing')
				{
						$bill1 = BillingMdl::find($ll->reff_no);
						if($bill1){
							$ll->bill_ref = $bill1->bill_num_01;
						}
						
						if($ll->discount <= 0){$ll->ledger_info = str_replace('SENIOR CITIZEN', '', $ll->ledger_info);}

				}
			}
			

			//~ echo '<pre>';
			//~ echo print_r($led001->toArray());
			//~ die();

			//return view('billings.inc.billing_ledger.acct_ledger_list_ajax', compact('ledger_list1'));
			return view('billings.inc.billing_ledger.acct_ledger_list_ajax22', compact('ledger_list1', 'led001', 'acct_id', 'reading12', 'beginning'));

			echo '<pre>';
			print_r($ledger_list1->toArray());
		}



		function AccountLedgerGetAccountById($acct_id)
		{
			$ledger_list1 = HwdLedgerCtrl::top20_ledger($acct_id);
			return view('billings.inc.billing_ledger.acct_ledger_list_ajax22', compact('ledger_list1'));
		}


		function update_beginning_v2()
		{
			$acct_id = @$_GET['acct_id'];
			$amt = @$_GET['amt'];
			$prd = @$_GET['prd'];

			//~ $prd_db = date('Y-m-01',strtotime($prd));
			$prd_db = date('Y-m-d',strtotime($prd));


			$acct1 = Accounts::find($acct_id);

			if(!$acct1){
				$request->session()->flash('success', 'Account not found');
				return Redirect::to(URL::previous() . "#period_request");
			}


			$beginning = LedgerData::where('acct_id', $acct1->id)
									->where('led_type', 'beginning')
									->where('status', 'active')
									->first();

			if($beginning){
				$beginning->ttl_bal = $amt;
				$beginning->arrear = $amt;
				$beginning->date01 = $prd_db;
				$beginning->period = $prd_db;
				$beginning->save();
			}else{
				$new_beg = new LedgerData;
				$new_beg->ttl_bal = (float) $amt;
				$new_beg->arrear = (float) $amt;
				$new_beg->acct_id = $acct1->id;
				$new_beg->date01 = $prd_db;
				$new_beg->period = $prd_db;
				$new_beg->status = 'active';
				$new_beg->led_type = 'beginning';
				$new_beg->acct_num = $acct1->acct_no;
				$new_beg->save();
			}

		}///


		function AccountLedgerUpdateBeginning(Request $request)
		{
			$acct_id = @$_GET['acct_id'];
			$amt = @$_GET['amt'];
			$prd = @$_GET['prd'];

			$prd_db = date('Y-m-01',strtotime($prd));


			$acct1 = Accounts::find($acct_id);

			if(!$acct1){
				$request->session()->flash('success', 'Account not found');
				return Redirect::to(URL::previous() . "#period_request");
			}


			$beginning = LedgerData::where('acct_id', $acct1->id)
									->where('led_type', 'beginning')
									->where('status', 'active')
									->first();


			if($beginning){
				$beginning->ttl_bal = $amt;
				$beginning->arrear = $amt;
				$beginning->date01 = $prd_db;
				$beginning->period = $prd_db;
				$beginning->save();
			}else{
				$new_beg = new LedgerData;
				$new_beg->ttl_bal = (float) $amt;
				$new_beg->arrear = (float) $amt;
				$new_beg->acct_id = $acct1->id;
				$new_beg->date01 = $prd_db;
				$new_beg->period = $prd_db;
				$new_beg->status = 'active';
				$new_beg->led_type = 'beginning';
				$new_beg->acct_num = $acct1->acct_no;
				$new_beg->save();
			}


			$request->session()->flash('success', 'Beginning balance updated');
			return Redirect::to(URL::previous() . "#period_request");

		}//

		function add_bill_ajustment_v2()
		{
			// ff($_GET);
			
			$my_csrf = @$_GET["my_csrf"];
			$se_csrf = @$_SESSION["my_csrf"];

			$amt =  round(@$_GET["amt"],2);
			$desc1 =  trim(@$_GET["desc"]);
			$adj_type =  trim(@$_GET["adj_type"]);
			$acct_id = @$_GET["acct_id"];
			$acct_no = @$_GET["acct_no"];
			$adj_reff = @$_GET["adj_reff"];
			
			
			$amt = abs($amt);
			
			if($adj_type == 'debit'){
				$amt = $amt * -1;
			}			

			//~ if(empty($my_csrf)){
				//~ return Redirect::to(URL::previous() . "");
			//~ }
			//~ if($se_csrf !=$my_csrf){
				//~ return Redirect::to(URL::previous() . "");
			//~ }

			$acct1 = Accounts::where('id', $acct_id)
						->where('acct_no', $acct_no)
							->first();

			if(!$acct1){
				return 	array('status' => 0);
			}
			
			//Add bill id
			$my_last_bill = BillingMdl::where('account_id', $acct_id)
								->where('status', 'active')
									->orderBy('id', 'desc')
										->first();
										
			$last_bill_id = 0;
			if($my_last_bill)
			{
				$last_bill_id = $my_last_bill->id;
			}
			//Add bill id end


			$new_bill = new BillingAdjMdl;
			$new_bill->acct_id = $acct_id;
			$new_bill->acct_no = $acct_no;
			$new_bill->date1 = date('Y-m-d');
			$new_bill->date1_stamp = date('Y-m-d H:i:s');
			$new_bill->amount = $amt;
			$new_bill->adj_typ_desc = $desc1.' - '.date('F d, Y');
			$new_bill->adj_typ = 'billing';
			//Add bill id
			$new_bill->bill_id = $last_bill_id;
			//Add bill id End
			$new_bill->save();

			$ledger11 = getLatestLeger($acct_id);
			$ttl = 0;

			if($ledger11){
				$ttl = $ledger11->ttl_bal -  $amt;
			}

			$new_data = new LedgerData;
			$new_data->acct_id = $acct_id;
			$new_data->ttl_bal = $ttl;
			$new_data->ledger_info = 'Billing Adjustment';
			$new_data->date01 = $new_bill->date1;
			$new_data->period = date('Y-m-01');
			$new_data->status = 'active';
			$new_data->led_type = 'adjustment';
			$new_data->acct_num = $acct_no;
			$new_data->reff_no = $new_bill->id;
			$new_data->bill_adj = $amt;
			$new_data->adj_reff = @$adj_reff;
			$new_data->save();

			unset($_SESSION["my_csrf"]);

			return 	array('status' => 1);
		}//


		function add_bill_ajustment(Request $requests)
		{

			$my_csrf = @$_GET["my_csrf"];
			$se_csrf = @$_SESSION["my_csrf"];

			$amt =  round(@$_GET["amt"],2);
			$desc1 =  trim(@$_GET["desc"]);
			$acct_id = @$_GET["acct_id"];
			$acct_no = @$_GET["acct_no"];

			if(empty($my_csrf)){
				return Redirect::to(URL::previous() . "");
			}
			if($se_csrf !=$my_csrf){
				return Redirect::to(URL::previous() . "");
			}


			$acct1 = Accounts::where('id', $acct_id)
						->where('acct_no', $acct_no)
							->first();

			if(!$acct1){
				$requests->session()->flash('success', 'Failed to find account');
				return Redirect::to(URL::previous() . "");
			}
			
			
			//Add bill id
			$my_last_bill = BillingMdl::where('account_id', $acct_id)
								->where('status', 'active')
									->orderBy('id', 'desc')
										->first();
										
			$last_bill_id = 0;
			if($my_last_bill)
			{
				$last_bill_id = $my_last_bill->id;
			}
			//Add bill id end
			


			$new_bill = new BillingAdjMdl;
			$new_bill->acct_id = $acct_id;
			$new_bill->acct_no = $acct_no;
			$new_bill->date1 = date('Y-m-d');
			$new_bill->date1_stamp = date('Y-m-d H:i:s');
			//$new_bill->ref_no = '';
			$new_bill->amount = $amt;
			$new_bill->adj_typ = 'billing';
			//Add bill id
			$new_bill->bill_id = $last_bill_id;
			//Add bill id end
			$new_bill->save();

			$ledger11 = getLatestLeger($acct_id);
			$ttl = 0;

			if($ledger11){
				$ttl = $ledger11->ttl_bal -  $amt;
			}



			$new_data = new LedgerData;
			$new_data->acct_id = $acct_id;
			$new_data->ttl_bal = $ttl;
			$new_data->ledger_info = 'Billing Adjustment';
			$new_data->date01 = $new_bill->date1;
			$new_data->period = date('Y-m-01');
			$new_data->status = 'active';
			$new_data->led_type = 'adjustment';
			$new_data->acct_num = $acct_no;
			$new_data->reff_no = $new_bill->id;
			$new_data->bill_adj = $amt;
			$new_data->save();

			unset($_SESSION["my_csrf"]);

			return Redirect::to(URL::previous() . "");


		}





}
