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
use App\BillingAdjMdl;
use App\BillingMeta;
use App\BillingRateVersion;
use App\HwdJob;
use App\User;
use App\Role;
use App\Invoice;
use App\OtherPayable;

use App\Http\Controllers\BillingCtrl;
use App\Http\Controllers\HwdLedgerCtrl;
use App\Http\Controllers\LedgerCtrl;

//BillingCtrl::__accountExtraData()
/*'acct_types', 'acct_statuses', 'zones', 'accounts', 'hwd_request_new_acct',
'bill_discount', 'acct_active', 'acct_new_con', 'acct_discon',
'acct_types_lab',  'acct_statuses_lab', 'zones_lab', 'bill_dis_lab'
*/


class AdminCtrl extends Controller
{

	function index()
	{
		// Redirect GM
		// Redirect GM
		$gm = Auth::user()->hasRole('general_manager');
		if( $gm ) { return redirect('/admin/gm/dashboard'); }
		// Redirect GM
		// Redirect GM

		return view('admin.index');
	}//
	
	function update_remarks_status(Request $req)
	{
		
		$adj_id         = @$_POST['adj_id'];
		$acct_id  	    = @$_POST['acct_id'];
		$admin_rem_001  = @$_POST['admin_rem_001'];
		$stat_001	    = @$_POST['stat_001'];
		$amt_002	    = @$_POST['amt_002'];
		
		$bill1 = BillingAdjMdl::where('id', $adj_id)->where('acct_id', $acct_id)->first();
		
		if(!$bill1){return back()->with('success', 'Adjustment not found.');}
		
		$bill1->admin_remarks = trim($admin_rem_001);
		$bill1->status        = trim($stat_001);
		$bill1->amount        = ($amt_002);
		$bill1->save();
		
		
		$ledg  = LedgerData::where('status', 'active')
					->where('led_type', 'adjustment')
						->where('ledger_info', 'like', '%Billing Adjustment%')
							->where('reff_no', $adj_id)
								->first();
		
		if(!$ledg){
			return back()->with('success', 'Done Updaed');
		}
		
		$ledg->bill_adj = $amt_002;
		$ledg->save();
		
		
		$led_data = new LedgerCtrl;
		$led_data->refresh_ledger_101($acct_id);
		
		return back()->with('success', 'Done Updaed');
	}
	
	
	function AdjusmentIndex()
	{
		$bill_adj = BillingAdjMdl::where(function($q1){
								$q1->where('adj_typ', 'billing');
								$q1->orWhereNull('adj_typ');
							})
							->with('acct')
								->orderBy('date1_stamp', 'desc')
									->paginate(10);
		//~ echo '<pre>';
		//~ print_r($bill_adj->toArray());
		return view('admin.adjustment', compact('bill_adj'));
		
	}//
	
	function AdjusmentAjax01($key1)
	{
		
		
		$key1 = trim($key1);
		
		$bill_adj = BillingAdjMdl::where('status', 'active')
							->where(function($q1){
									$q1->where('adj_typ', 'billing');
									$q1->orWhereNull('adj_typ');
								})
								->whereHas('acct', function($q1)use($key1){
										$q1->where('lname','like', $key1.'%');
									})
									->with('acct')
										->orderBy('date1_stamp', 'desc')
											->paginate(10);

		return view('admin.adjustment', compact('bill_adj', 'key1'));

									
		//~ $html1 = view('admin.incs.adjustments.adjust1', compact('bill_adj'))->render();
		//~ return array('html1'=> $html1);
	}
	
	
	//~ function dailyCollectionStartEnd001()
	function get_collection_info_by_date1001($date1)
	{
		
		$role1 = Role::where('name','collection_officer')->first();
								
		$date1 = date('Y-m-d', strtotime($date1));
		
		foreach($role1->users as $us1)
		{
			$daily_1 = AAA_DailyCollectionPerTeller($us1->id, $date1);
			$us1->daily_col01 = $daily_1->toArray();
		}
		
		$html1 = view('admin.incs.dashboard.coll_info0011', compact('role1', 'date1'))->render();
		return array('status'=>1, 'html1'=> $html1);
		
		echo '<pre>';
		print_r($role1->toArray());
	}//
	
	function get_collection_info_by_date1001_monthly_summary($date1, $col_id)
	{
		$date_stat = date('l, F d, Y', strtotime($date1));
		$date_end  = date('l, F d, Y', strtotime($date1));
		
		$date_inc  = date('Y-m-', strtotime($date1));
		
		$nth_start = 1;
		$nth_end   = (int) date('d', strtotime($date1));
		
		$collector = User::find($col_id);
		
		if(!$collector){
			echo 'Collector Not Found!';
			die();
		}
		
		
		$new_arr = array();
		for($x=$nth_start;$x<=$nth_end;$x++)
		{
			$new_date = date('Y-m-d', strtotime($date_inc.$x));
			$new_arr[$x] = (object) array();
			$new_arr[$x]->coll1 = AAA_DailyCollectionPerTeller($col_id, $new_date)->toArray();
			$new_arr[$x]->date1 = $new_date;
		}
		
		$my_coll = (object) $collector->toArray();
		return view('admin.incs.dashboard.coll_info0011_summary', 
				compact('date_stat', 'date_end', 'new_arr', 'date1', 'my_coll'));
		
	}//
	

	function allRequests()
	{

		$acct_req =
			$this->__allRequests_Data1(['new_account_approval', 'new_account_approval']);

		$billing_req =
			$this->__allRequests_Data1(['generate_billing_period_request']);

		$invoice_req =
			$this->__allRequests_Data1(['invoice_request'], 'invoice_request');



		return view('admin.all_requests', compact('acct_req', 'billing_req', 'invoice_req'));
		echo '<pre>';
		print_r($invoice_req);
	}//End

	private function  __allRequests_Data1($req_types = array(), $w_type = 'account')
	{

		$req1  =
			HwdRequests::where(function($query) use($req_types) {
				foreach($req_types as $rrt){
					$query->orWhere('req_type',$rrt);
				}
			})
			->orderBy('id', 'desc');

		switch(@$w_type)
		{
			case "account":
				$req1->with('account');
			break;
		}

		$hqRequest_raw = $req1->paginate(100);
		$hqRequest  =  $hqRequest_raw->toArray();
		return $hqRequest;
	}


		function requestApproveAccount($req_id, Request $request)
		{


				 $req1 = HwdRequests::where('id',$req_id)
								->where('req_type','new_account_approval')
								->with('account')
								->first();
								
				//~ $req1->reff_id
				//~ echo '<pre>';
				//~ print_r($req1->toArray());			
				//~ die();

				 if($req1)
				 {
					 $req1->status = "approved";
					 $req1->date_stat = date('Y-m-d H:i:s');
					 $req1->save();
					 
					 $req1->account->acct_status_key = 1;
					 $req1->account->save();

					 HwdLedgerCtrl::newAcctAcknowledgement($req1->reff_id);

					 $request->session()->flash('success', 'Request approved');
				 }else{
					$request->session()->flash('success', 'Request cant found');
				 }
				return Redirect::to(URL::previous() . "#account_list");

		}//End Func

		function requestCancelAccount($req_id, Request $request){
			$req1 = HwdRequests::where('id',$req_id)
						    ->where('req_type','new_account_approval')
						    ->with('account')
						    ->first();

				 if($req1){
					 $req1->status = "canceled";
					 $req1->date_stat = date('Y-m-d H:i:s');
					 $req1->save();

					 HwdLedgerCtrl::newAcctAcknowledgement($req1->reff_id, 'canceled');

					$request->session()->flash('success', 'Request canceled');
				 }else{
					$request->session()->flash('success', 'Request cant found');
				 }
				return Redirect::to(URL::previous() . "#account_list");
		}//End Func


		function requestBillingApprove($req_id, Request $request){
				 $req1 = HwdRequests::where('id',$req_id)
								->where('req_type','generate_billing_period_request')
								->with('account')
								->first();

				 if($req1){
					 $req1->status = "approved";
					 $req1->date_stat = date('Y-m-d H:i:s');
					 $req1->save();
					$request->session()->flash('success', 'Request approved');
				 }else{
					$request->session()->flash('success', 'Request cant found');
				 }

				return Redirect::to(URL::previous() . "#billing");

		}//End Func

		function requestBillingCancel($req_id, Request $request){
				 $req1 = HwdRequests::where('id',$req_id)
								->where('req_type','generate_billing_period_request')
								->with('account')
								->first();

				 if($req1){
					 $req1->status = "canceled";
					 $req1->date_stat = date('Y-m-d H:i:s');
					 $req1->save();
					$request->session()->flash('success', 'Request canceled');
				 }else{
					$request->session()->flash('success', 'Request cant found');
				 }

				return Redirect::to(URL::previous() . "#billing");

		}//End Func


		function requestInvoiceApprove($req_id, Request $request)
		{

			$req1 = HwdRequests::where('id',$req_id)
				    ->where('req_type','invoice_request')
				    ->first();

			if($req1){
				$req1->status = "approved";
				$req1->date_stat = date('Y-m-d H:i:s');
				$req1->save();
			     $request->session()->flash('success', 'Invoice approved');

				$invoice =  Invoice::find($req1->reff_id);
				if($invoice){
					$invoice->stat = 'active';
					$invoice->save();
				}

			}else{
			    $request->session()->flash('success', 'Invoice cant found');
			}

			return Redirect::to(URL::previous() . "#invoice");

		}//End Func invoice


		function requestInvoiceCancel($req_id, Request $request)
		{
			$req1 = HwdRequests::where('id',$req_id)
				    ->where('req_type','invoice_request')
				    ->first();

			if($req1){
				$req1->status = "canceled";
				$req1->date_stat = date('Y-m-d H:i:s');
				$req1->save();
			    	$request->session()->flash('success', 'Invoice Canceled');

				$invoice =  Invoice::find($req1->reff_id);
				if($invoice){
					$invoice->stat = 'inactive';
					$invoice->save();
				}


			}else{
			    $request->session()->flash('success', 'Invoice cant found');
			}
			return Redirect::to(URL::previous() . "#invoice");

		}//End Func invoice
		
		
		function otherPayableIndex()
		{
			$other_pay = OtherPayable::where('paya_stat', '!=','deleted')->orderBy('paya_title', 'asc')->get();
			
			return view('admin.other_payable', compact('other_pay'));			
		}
		
		function otherPayableAddNew(Request $request)
		{
			extract($_POST);
			//~ echo '<pre>';
			//~ print_r($_POST);
			$new_op = new OtherPayable;
			$new_op->paya_title = trim($paya_title);
			$new_op->paya_desc = trim($paya_desc);
			$new_op->paya_amount = trim($paya_amount);
			$new_op->paya_stat = trim($paya_stat);
			$new_op->glsl_code = trim($glsl_type);
			$new_op->save();
			
			$request->session()->flash('success', 'Done Added.');
			return Redirect::to(URL::previous() );
		}
		
		function otherPayableUpdate(Request $request)
		{
			extract($_POST);
			//~ echo '<pre>';
			//~ print_r($_POST);
			$new_op = OtherPayable::find($paya_id);
			$new_op->paya_title = trim($paya_title);
			$new_op->paya_desc = trim($paya_desc);
			$new_op->paya_amount = trim($paya_amount);
			$new_op->paya_stat = trim($paya_stat);
			$new_op->glsl_code = trim($glsl_type);
			$new_op->save();

			$request->session()->flash('success', 'Done Updated.');
			return Redirect::to(URL::previous() );
		}
		

		function accountIndex(){

				$acct11 = Accounts::orderBy('lname', 'asc')
										->paginate(20)
										->toArray();

				$data1 = BillingCtrl::__accountExtraData();
				extract($data1);


				foreach($acct11['data'] as $kk => $vv){
					$vv['birth_date'] = date('M d, Y', strtotime($vv['birth_date']));
					$vv['residence_date'] = date('M d, Y', strtotime(@$vv['residence_date']));
					$vv['acct_stat_lab'] =  @$acct_statuses_lab[$vv['acct_status_key']];
					$vv['acct_type_lab'] =  @$acct_types_lab[$vv['acct_type_key']];
					$vv['zone_lab'] =  @$zones_lab[$vv['zone_id']];
					$vv['bill_dis_lab'] =  @$bill_dis_lab[$vv['acct_discount']] ?  $bill_dis_lab[$vv['acct_discount']] : 'None';
					$vv['acct_created']  =  date('F d, Y', strtotime($vv['created_at']));
					$vv['num_of_bill']  = (int) $vv['num_of_bill'];
					$acct11['data'][$kk] = $vv;
				}

				$number_of_acct = $accounts['total'];
				$res = $acct11;
				/**/
				return view('admin.accounts', compact(
						'accounts',
						'acct_statuses',
						'number_of_acct',
						'res'
				));
				/**/
				echo '<pre>';
				print_r($res);
		}//End func

		function accountGetSearch1_ajax($acct, $fname, $zone){
				$Acct1 = Accounts::orderBy('id', 'desc');
				if($acct != 'none'){
					$Acct1->where('acct_no', 'like', $acct.'%');
				}
				if($fname != 'none'){
					$Acct1->where('lname', 'like', $fname.'%');
				}
				if($zone != 'none'){
					$Acct1->where('zone_id', 'like', $zone.'%');
				}
				$res = $Acct1->paginate(20)->toArray();
				$acct_statuses = AccountMetas::where('status','!=', 'deleted')->where('meta_type', 'account_status')->orderBy('meta_name', 'asc')->get()->toArray();

				$html  =  view('admin.incs.acccount.ajax.ajax_temp1', compact('res', 'acct_statuses'));
				return  array('html' => $html.'', 'data1' => $res['data']);
				echo '<pre>';
				print_r($res);
		}//

		function readingIndex(){

				$par1 = array(
								'reading_off',
								'zones_lab',
								'active_acct_count',
								 'bill_rates',
								 'read_accout_count',
								 'status_key_active'
							);

				$data1  = common_data($par1);
				extract($data1);

				return view('admin.readings', compact('reading_off', 'active_acct_count', 'read_accout_count'));
				//$reading_1 = Reading::get()->toArray();

				$zone_acct_count =
							Accounts::where('acct_status_key', $status_key_active)
									->select(DB::raw('
												accounts.*,
												COUNT(id) as  acct_per_zone
									'))
									->whereHas('reading_billed', function($query){
											$date1 = date('Y-m');
											$get_date = @$_GET['date1'];

											if(!empty($get_date)){
												$date1 = date('Y-m', strtotime($get_date));
											}
											$query->where('period','like',  $date1.'%');
									})
									->groupBy('accounts.zone_id')
									->get()
									->toArray();

				$zone_acct_count_lab  = array();
				if(!empty($zone_acct_count)){
					foreach($zone_acct_count  as $zac){
						$zone_acct_count_lab[$zac['zone_id']]['acct_count'] =  $zac['acct_per_zone'];
						$zone_acct_count_lab[$zac['zone_id']]['zone_name'] =  $zones_lab[$zac['zone_id']];
					}
				}


				echo '<pre>';
				print_r($zone_acct_count_lab);
		}


		function collectionIndex(){
				return view('admin.collection');
		}

		function billingIndex(){
				return view('admin.billing');
		}

		function systemAccountIndex(){
			$role1 =  Role::orderBy('name', 'asc')->get()->toArray();
			$users1 =  User::with('roles')->orderBy('name', 'asc')->get()->toArray();
			$role1 = Role::get()->toArray();
			return view('admin.system_acct', compact('role1', 'users1', 'role1'));
			echo '<pre>';
			print_r($role1->toArray());
		}

		function systemAccountCreateNewAccount(Request $request){
			extract($_POST);

			if($user_pass1 != $user_pass2){
					$request->session()->flash('success', 'Failed to register');
					return Redirect::to(URL::previous() );
			}

			$role_admin  = Role::where('name', $acct_type)->first();
			$user1 = new User();
			$user1->name = trim($fname);
			$user1->username = trim($user_name);
			$user1->password = bcrypt(trim($user_pass1));
			$user1->save();
			$user1->roles()->attach($role_admin);

			$request->session()->flash('success', 'Done Added.');
			return Redirect::to(URL::previous() );

		}//End func


		function systemAccountCreateNewAccountType(Request $request){
			extract($_POST);
			$vname = str_replace(' ','_',strtolower(trim($name)));
			$vdesc   = trim($name);

			$new_role = new Role;
			$new_role->name = $vname;
			$new_role->description = $vdesc;
			$new_role->save();

			if($new_role){
				$request->session()->flash('success', 'Done Added New');
			}else{
				$request->session()->flash('success', 'Failed to add');
			}

			return Redirect::to(URL::previous().'#acct_type');
		}


		function systemAccountEditAccount(Request $request){
			extract($_POST);

			if($user_pass1 != $user_pass2){
				$request->session()->flash('success', 'Failed to change, password not match');
				return Redirect::to(URL::previous() );
			}

			$role_admin  = Role::where('name', $acct_type)->first();

			$user1 = User::with('roles')->find($id);

			if(!$user1){
				$request->session()->flash('success', 'Failed to change. User not found');
				return Redirect::to(URL::previous() );
			}

			$user1->name = trim($fname);
			$user1->username = trim($user_name);
			if(!empty($user_pass1)){
				$user1->password = bcrypt(trim($user_pass1));
			}
			$user1->save();

			if($acct_type != $user1->roles[0]->name){
				$user1->roles()->detach();
				$user1->roles()->attach($role_admin);
			}

			$request->session()->flash('success', 'Updated');
			return Redirect::to(URL::previous() );

		}//

		function systemAccountLogout(Request $request){
				Auth::logout();
				return redirect('/');
		}


		function ajaxRequest001(){
				$req_count  =
						HwdRequests::where(function($query){
							$query->orWhere('req_type', 'new_account_approval');
						})
						->where('status', 'pending')
						->count();

				$biil_count  =
						HwdRequests::where(function($query){
							$query->orWhere('req_type', 'generate_billing_period_request');
						})
						->where('status', 'pending')
						->count();

				return array(
							'req_count' => (int) $req_count,
							'bill_req1' => (int) $biil_count
						);
		}



}
