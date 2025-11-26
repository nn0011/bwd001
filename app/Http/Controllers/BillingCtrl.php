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
use App\PrintServ;
use App\ServiceBillZone;
use App\Arrear;
use App\OverdueStat;
use App\ConnectionLedger;
use App\GpsAcct;
use App\BillingNw;


use PDF;
use Fpdf;
use Excel;


use App\Http\Controllers\HwdLedgerCtrl;
use App\Http\Controllers\LedgerCtrl;
use App\Http\Controllers\ServiceCtrl;
use App\User;

class BillingCtrl extends Controller
{
	
	
	function rebill_from_reading($read_id, $acct_id, $acct_no)
	{
		global $extra_msg;
		
		$reading1 = Reading::where('id',$read_id)
						->where('account_id',$acct_id)
							->where('account_number',$acct_no)
								->first();

		if(!$reading1){
			return array('status' => 0, 'msg' => 'Reading failed to retrieve');
		}

		$curr_read_009 = (int) @$reading1->curr_reading;
		$prev_read_009 = (int) @$reading1->prev_reading;
		$cons_read_009 = (int) @$reading1->current_consump;


		if($curr_read_009 <= 0){
			return array('status' => 0, 'msg' => 'Current reading failed');
		}

		if($cons_read_009 < 0){
			return array('status' => 0, 'msg' => 'Negative consumption');
		}
		
		$extra_msg = '';
		
		if($cons_read_009 <= 30)
		{
			$my_acct = Accounts::find($acct_id);
			if($my_acct->acct_discount == SENIOR_ID)
			{
				$extra_msg = ' SENIOR CITIZEN ';
			}
		}		


		/*
		 *
		 *
		 *
		 * 
		 * 
		 *
		 *
		 * */


		// $rate_version = BillingRateVersion::orderBy('id', 'desc')->first();

		$data11 = ServiceCtrl::___rates_and_discounts();
		extract($data11);

		$acct_type = array();
		$acctype1 = AccountMetas::where('meta_type', 'account_type')->get();
		foreach($acctype1 as $att){$acct_type[$att->id] = $att->toArray();}
		$data11['acct_type'] = $acct_type;


		// $arrear = ServiceCtrl::proccess_arrears($reading1, $reading1->period,  true);
		// $bill_info = ServiceCtrl::proccess_billing($reading1, $reading1->period, $data11, true);


		// $led1777 = LedgerData::where('acct_id', $reading1->account_id)
		// 				->where('status','active')
		// 					->orderBy('id', 'desc')
		// 						->first();
		

		// $str_date = date('F Y',strtotime($reading1->period));
		
		/*
		if($reading1->billing)
		{

				$ledBill01 = LedgerData::where('led_type','billing')
								->where('acct_id', $reading1->account_id)
								->where('reff_no', $reading1->billing->id)
								->where('status','active')
									->get();

				if(count($ledBill01) != 0)
				{
					foreach($ledBill01 as $lb1)
					{
						$lb1->status = 'rebill';
						$lb1->save();
					}
				}
		}//
		*/

		// ee($reading1->toArray(), __FILE__, __LINE__);

		ServiceCtrl::proccess_arrears($reading1, $reading1->period,  false);
		
		ServiceCtrl::proccess_billing($reading1, $reading1->period, $data11, false);
		
		return array('status' => 1, 'msg' => 'Billing success');

	}//

	function save_penalty_date($bill_id)
	{
		$bill1 = BillingMdl::find($bill_id);
		if(!$bill1){return array('status'=>0, 'msg'=>'Billing not found');}

		$penalty_date = @$_GET['pen_date'];

		$dd0 = date('Y-m-d');
		$dd1 = strtotime($penalty_date);
		$dd2 = date('Y-m-01');
		$dd3 = date('Y-m-01', $dd1);
		$dd4 = strtotime($dd0);
		$dd5 = date('Y-m-d', $dd1);

		if($dd2 != $dd3){
			return array('status'=>0, 'msg'=>'Invalid penalty date. V1');
		}
		
		
		if($dd4 >= $dd1){
			//~ return array('status'=>0, 'msg'=>'Invalid penalty date. V2 '.date('Y-m-d', $dd4).' > '.date('Y-m-d', $dd1));
		}

		if($bill1->penalty_date == $dd5){
			return array('status'=>0, 'msg'=>'No change is made');
		}

		$bill1->penalty_date = $dd5;
		$bill1->save();

		return array('status'=>1, 'msg'=>'Penalty date saved.');

		//~ echo $dd1;
		//~ echo '<br />';
		//~ echo $dd4;
	}//

	function save_billing_number($bill_id)
	{
		$bill1 = BillingMdl::find($bill_id);
		if(!$bill1){return array('status'=>0, 'msg'=>'Billing not found');}

		$bill_no = (int) @$_GET['bill_no'];

		if($bill_no <= 0){
			return array('status'=>0, 'msg'=>'Invalid bill number');
		}

		$is_belong_to_other = BillingMdl::where('bill_num_01', $bill_no)
					->where('id', '!=', $bill_id)
						->first();

		if($is_belong_to_other){
			return array('status'=>0, 'msg'=>'Bill number is already used.');
		}

		if($bill1->bill_num_01 == $bill_no){
			return array('status'=>0, 'msg'=>'No change is made.');
		}

		$bill1->bill_num_01 = $bill_no;
		$bill1->save();

		return array('status'=>1, 'msg'=>'Bill number save.');
	}


	function index()
	{
		// $sql1 = "
		// 	SELECT * FROM `billing_dues`  
		// 	WHERE period like '2024-07-01' AND due_stat != 'active' AND created_at like '2024-08-20%';		
		// ";
		// $mmm = DB::select($sql1);
		// foreach($mmm as $vv){
		// 	echo '<br />';
		// 	echo '<a href="http://192.168.1.254:8585/billing/account_ledger/refresh_ledger_101/'.$vv->acct_id.'">'.$vv->acct_id.'</a>';
		// }
		// die();
		
		return view('billings.index');
		echo '<pre>';
		print_r($acct_types);
	}

	function AAAAA()
	{
	}


	function AccountMain(Request $request)
	{

		// echo 'AAAA';
		// die();

		$extra_data = $this->__accountExtraData();
		extract($extra_data);

		$stat_label  = array();
		foreach($acct_statuses as $ass)
		{
			$stat_label[$ass['id']] = $ass['meta_name'];
		}

		foreach($accounts['data'] as $kk => $vv)
		{
			//~ $vv['birth_date'] = date('M d, Y', strtotime($vv['birth_date']));
			//$vv['residence_date'] = date('M d, Y', strtotime(@$vv['residence_date']));
			$vv['acct_stat_lab'] =  @$acct_statuses_lab[$vv['acct_status_key']];
			$vv['acct_type_lab'] =  @$acct_types_lab[$vv['acct_type_key']];
			$vv['zone_lab'] =  @$zones_lab[$vv['zone_id']];
			$vv['bill_dis_lab'] =  @$bill_dis_lab[$vv['acct_discount']] ?  $bill_dis_lab[$vv['acct_discount']] : 'None';
			$vv['acct_created']  =  date('F d, Y', strtotime($vv['created_at']));
			$accounts['data'][$kk] = $vv;
		}

		foreach($hwd_request_new_acct as $kk => $vv)
		{
			//~ $vv['account']['birth_date'] = date('M d, Y', strtotime($vv['account']['birth_date']));
			//$vv['account']['residence_date'] = date('M d, Y', strtotime(@$vv['account']['residence_date']));
			$vv['account']['acct_stat_lab'] =  @$acct_statuses_lab[@$vv['account']['acct_status_key']];
			$vv['account']['acct_type_lab'] =  @$acct_types_lab[@$vv['account']['acct_type_key']];
			$vv['account']['zone_lab'] =  @$zones_lab[@$vv['account']['zone_id']];
			$vv['account']['bill_dis_lab'] =  @$bill_dis_lab[@$vv['account']['acct_discount']] ?  $bill_dis_lab[$vv['account']['acct_discount']] : 'None';
			$vv['account']['acct_created']  =  date('F d, Y', strtotime(@$vv['account']['created_at']));
			$hwd_request_new_acct[$kk] = $vv;
		}

		$customer_routes = CustomerRoute::orderBy('route_num')->get();

		$meter_sizes = BillingMeta::where('meta_type','meter_size')->orderBy('nsort')->get();

		// echo '<pre>';
		// print_r($hwd_request_new_acct);
		// die();

		/**/
		return view('billings.accounts', compact(
				'acct_types',
				'acct_statuses',
				'zones',
				'accounts',
				'hwd_request_new_acct',
				'bill_discount',
				'acct_active',
				'acct_new_con',
				'acct_discon',
				'stat_label',
				'customer_routes',
				'meter_sizes'
			));

		/**/
		echo '<pre>';
		//print_r($accounts);
		print_r($accounts);

	}//TEST


	function AccountFilterPDF($acct_num, $meter_num, $lname, $zone, $acct_status,$beg_stat,Request $request)
	{
		$extra_data = $this->__accountExtraData();
		extract($extra_data);
		

		$ress1 = $this->AccountFilter($acct_num, $meter_num, $lname, $zone, $acct_status,$beg_stat, true, $request);

		ee($ress1->toArray(), __FILE__, __LINE__);

		// $data1 = [];
		// foreach() {
		// }

		$pdf = PDF::loadView('billings.inc.billing_accounts.acct_list_pdf',
					compact('ress1','zone', 'zones'));
		return $pdf->stream('account_list_result.pdf');

		echo '<pre>';
		print_r($ress1->toArray());
	}

	function AccountFilter($acct_num, $meter_num, $lname, $zone, $acct_status,$beg_stat, $data_only=false, Request $request)
	{

		$extra_data = $this->__accountExtraData();

		//~ echo '<pre>';
		//~ print_r($extra_data['acct_types']);
		//~ die();

		extract($extra_data);

		$acct_raw =  Accounts::where('status', '!=', 'deleted');

		if($acct_status != 'none'){
			$acct_raw->where('acct_status_key', $acct_status);
		}

		if($zone != 'none'){
			$acct_raw->where('zone_id', $zone);
		}
		if($lname != 'none')
		{
			$new_n = explode(',',$lname);
			if(count($new_n) == 1){
				$acct_raw->where('lname', 'like', trim($new_n[0]).'%');
			}else{
				$acct_raw->where('lname', 'like', trim($new_n[0]).'%');
				$acct_raw->where('fname', 'like', trim($new_n[1]).'%');
			}

		}

		if($acct_num != 'none'){$acct_raw->where('acct_no', 'like', '%'.trim($acct_num).'');}
		if($meter_num != 'none'){$acct_raw->where('meter_number1', 'like', '%'.trim($meter_num).'');}


		$quick_search = (int) @$_GET['quick_search'];

		if($quick_search == 0){
			//~ $acct_raw->where('meter_number1', 'like', '%'.trim($meter_num).'');
		}else{

			if(
				$quick_search == 7  ||
				$quick_search == 8  ||
				$quick_search == 9  ||
				$quick_search == 12 ||
				$quick_search == 14 ||
				$quick_search == 15
			)
			{
				$acct_raw->where('acct_type_key', $quick_search);
			}

			//Penalty Exempted
			if($quick_search == 1)
			{
				$acct_raw->where('pen_exempt', '1');
			}

			//senior
			if($quick_search == 2)
			{
				$acct_raw->where('acct_discount', SENIOR_ID);
			}



		}



		//~ $acct_raw->with(['request001' => function($query){
				//~ $query->where('req_type', 'new_account_approval');
		//~ }]);

		if($beg_stat != 'none'){
			$acct_raw->whereDoesntHave('ledger_data4');
		}



		$acct_raw->with(['ledger_data' => function($query){
			$query->where('led_type', 'beginning');
		}]);



		if($data_only == true)
		{
			//~ $accounts22 =  $acct_raw->orderBy('old_route', 'asc')
											//~ ->get();

			//~ $accounts22 =  $acct_raw->orderBy('route_id', 'asc')
			$accounts22 =  $acct_raw->orderBy('acct_no', 'asc')
											->get();
			return $accounts22;
			echo '<pre>';
			print_r($accounts22->toArray());
			die();
		}


		
		//~ $accounts =  $acct_raw->orderBy('old_route', 'asc')
		$accounts =  
									$acct_raw
										->orderBy(DB::Raw(' ISNULL(route_id), route_id '), 'asc')
										//->orderBy('acct_no', 'asc')
										->paginate(100)
										->toArray();



		$stat_label  = array();
		foreach($acct_statuses as $ass){
			$stat_label[$ass['id']] = $ass['meta_name'];
		}

		foreach($accounts['data'] as $kk => $vv){
			$vv['birth_date'] = date('M d, Y', strtotime($vv['birth_date']));
			//$vv['residence_date'] = date('M d, Y', strtotime(@$vv['residence_date']));
			$vv['acct_stat_lab'] =  @$acct_statuses_lab[$vv['acct_status_key']];
			$vv['acct_type_lab'] =  @$acct_types_lab[$vv['acct_type_key']];
			$vv['zone_lab'] =  @$zones_lab[$vv['zone_id']];
			$vv['bill_dis_lab'] =  @$bill_dis_lab[$vv['acct_discount']] ?  $bill_dis_lab[$vv['acct_discount']] : 'None';
			$vv['acct_created']  =  date('F d, Y', strtotime($vv['created_at']));
			$accounts['data'][$kk] = $vv;
		}

		//~ echo '<pre>';
		//~ print_r($accounts);
		//~ die();

		$customer_routes =
					CustomerRoute::orderBy('route_num')
					->get();

		$meter_sizes = BillingMeta::where('meta_type','meter_size')
						->orderBy('nsort')
						->get();



		return view('billings.accounts', compact(
								'acct_types',
								'acct_statuses',
								'zones',
								'accounts',
								'hwd_request_new_acct',
								'bill_discount',
								'acct_active',
								'acct_new_con',
								'acct_discon',
								'stat_label',
								'meter_num',
								'zone',
								'lname',
								'acct_num',
								'customer_routes',
								'acct_status',
								'beg_stat',
								'meter_sizes'
							));

		echo '<pre>';
		print_r($accounts);

	}

	function accounts_update_route()
	{
		$acct_id = (int) @$_POST['acct_id'];
		$route_num = (float) @$_POST['route_num'];

		$curr_acct = Accounts::find($acct_id);

		if(!$curr_acct) {
			echo 'ERROR : Account not Found.';
			die();
		}

		$curr_acct->route_id = round($route_num, 2);
		$curr_acct->save();

		echo 'SUCCESS : Route updated.';
		die();
		ee($_POST, __FILE__, __LINE__);

	}

	public static	function  __accountExtraData(){

		$acct_types = AccountMetas::where('status','!=', 'deleted')
								->where('meta_type', 'account_type')
								->orderBy('meta_name', 'asc')
								->get()
								->toArray();

		$acct_statuses = AccountMetas::where('status','!=', 'deleted')
								->where('meta_type', 'account_status')
								->orderBy('meta_name', 'asc')
								->get()
								->toArray();

		$zones  = Zones::where('status', '!=', 'deleted')
							->orderBy('zone_name', 'asc')
							->get()
							->toArray();

		$bill_discount = BillingMeta::where('meta_type', 'billing_discount')
								->where('status', 'active')
								->get()
								->toArray();


		$acct_types_lab = array();
		foreach($acct_types as $att){
			$acct_types_lab[$att['id']] = $att['meta_name'];
		}

		$acct_statuses_lab = array();
		foreach($acct_statuses as $att){
			$acct_statuses_lab[$att['id']] = $att['meta_name'];
		}

		$zones_lab = array();
		foreach($zones as $att){
			$zones_lab[$att['id']] = $att['zone_name'];
		}

		$bill_dis_lab = array();
		foreach($bill_discount as $att){
			$bill_dis_lab[$att['id']] = $att['meta_name'];
		}

		$accounts = Accounts::where('status', '!=', 'deleted')
									// ->where('acct_status_key', '!=', '20') // STATUS DELETED
									->with(['request001' => function($query){
												$query->where('req_type', 'new_account_approval');
										}])
									->with(['ledger_data' => function($query){
										$query->where('led_type', 'beginning');
									}])
									->with(['last_gps_data'])
									->orderBy('id', 'desc')
									// ->orderBy(DB::raw('ISNULL(route_id), route_id'), 'asc')
									->paginate(20);
									

		foreach($accounts as $kk => $vv){
			$mmm = $vv->reading_billed()->count();
			$accounts[$kk]  = $vv;
			$accounts[$kk]['bill_count']  = $mmm;
		}

		$accounts = $accounts->toArray();

		$hwd_request_new_acct = HwdRequests::where('status', '!=', 'deleted')
									->where('req_type', 'new_account_approval')
									->with('account')
									->orderBy('id', 'desc')
									->limit(10)
									->get()
									->toArray();

		//Active
		$acct_active = Accounts::where('status', 'active')
								->whereHas('acct_status' ,  function($query){
											$query->where('old_id', '1');
											$query->where('meta_type', 'account_status');
									})
								->count();

		//Disconnected
		$acct_discon = Accounts::where('status', 'active')
								->whereHas('acct_status' ,  function($query){
											$query->where('old_id', '3');
											$query->where('meta_type', 'account_status');
									})
								->count();

		//New Concessionaire
		$acct_new_con = Accounts::where('status', 'active')
									->whereHas('acct_status' ,  function($query){
											$query->where('meta_type', 'account_status');
											$query->where('old_id', '0');
									})
								->count();

		/*
		echo '<pre>';
		print_r($accounts->reading_billed()->count());
		print_r($acct_types_lab);
		print_r($acct_statuses_lab);
		print_r($zones_lab);
		print_r($bill_dis_lab);
		die();
		/**/

		// echo '<pre>';
		// print_r($accounts);
		// die();

		// ee($accounts, __FILE__, __LINE__);



		return compact(
							'acct_types',
							'acct_statuses',
							'zones',
							'accounts',
							'hwd_request_new_acct',
							'bill_discount',
							'acct_active',
							'acct_new_con',
							'acct_discon',
							'acct_types_lab',
							'acct_statuses_lab',
							'zones_lab',
							'bill_dis_lab'
						);


	}//


	function AccountNew(Request $request)
	{
		extract($_POST);
		

		$residence_date = date('Y-m-d', strtotime($residence_date));
		$birth = date('Y-m-d', strtotime($birth));

		$acct_stat = AccountMetas::where('meta_code', '-1')
		//$acct_stat = AccountMetas::where('meta_code', '1')
								->where('meta_type', 'account_status')
								->first();

		//~ echo '<pre>';
		//~ echo $residence_date;
		//~ die();


		$acct = new Accounts;
		$acct->fname = $fname;
		$acct->lname = $lname;
		$acct->mi = $mi;
		$acct->address1 = $address1;
		$acct->address2 = @$address2;
		$acct->tel1 = $phone;
		$acct->install_date = $residence_date;
		$acct->zone_id = $zone;
		$acct->acct_type_key = $acct_type;
		$acct->acct_status_key = $acct_stat->id;
		//~ $acct->acct_status_key = 1;
		//$acct->status = 'active';
		$acct->status = 'pending';

		$acct->acct_discount = $discount_type;
		$acct->birth_date = $birth;

		$acct->save();

		//GENERATE ACOUNT NUMBER
		//$acct->acct_no = date('Ymd').'-111-'.$acct->id;
		$acct->acct_no = '001-'.$acct->id;
		$acct->save();

		$new_request = new HwdRequests;
		$new_request->reff_id = $acct->id;
		$new_request->req_type = 'new_account_approval';
		$new_request->remarks = ' New account approval reqeust for  account # : '.$acct->acct_no;
		$new_request->status = 'pending';
		$new_request->save();


		HwdLedgerCtrl::newAcctApply($acct->id);

		$rrs11 = LedgerCtrl::add_begining($acct->id, array(
				'amount' => 0
			));

		//~ var_dump($rrs11);
		//~ die();

		$request->session()->flash('success', 'New Account Added');
		return Redirect::to(URL::previous() . "#account_list");

		//echo '<pre>';
		//print_r($_POST);
	}

	function AccountUpdate(Request $request)
	{
		// ee($_POST, __FILE__, __LINE__);

		extract($_POST);
		$curr_period = date('Y-m');
		
		$residence_date = ($residence_date);
		
		if(!empty($residence_date)){
			$residence_date = date('Y-m-d', strtotime($residence_date));
		}else{
			$residence_date = null;
		}
		
		
		$birth = ($birth);
		
		if($birth){
			$birth = date('Y-m-d', strtotime($birth));
			$new_birth = date('Y-m-d', strtotime($birth));
		}else{
			$birth = null;
			$new_birth = null;
		}
		

		$acct = Accounts::find($id);

		// echo '<pre>';
		// print_r($residence_date);
		// print_r($_POST);
		// die();

		$name_change = 0;
		$birth_change = 0;
		$address_change = 0;
		$zone_change = 0;
		$acct_type_change = 0;
		$acct_status_change = 0;
		$phone_change = 0;
		$discount_change = 0;
		$penalty_exempt_change = 0;
		$residence_date_change = 0;
		$statusWord = 'active';
		$employee_change = 0;

		$tin_id_change = 0;
		$other_id_change = 0;

		$change_labels = array();
		
		if(@$lname != $acct->lname){$name_change=1;}
		if(@$fname != $acct->fname){$name_change=1;}
		if(@$mi != $acct->mi){$name_change=1;}

		if(@$new_birth != $acct->birth_date){$birth_change=1;}
		if(@$address1 != $acct->address1){$address_change=1;}
		if(@$zone != $acct->zone_id){$zone_change=1;}
		if(@$acct_type != $acct->acct_type_key){$acct_type_change=1;}
		if(@$acct_status != $acct->acct_status_key){$acct_status_change=1;}
		if(@$phone != $acct->tel1){$phone_change=1;}
		if(@$discount_type != @$acct->acct_discount){$discount_change=1;}
		if(@$penalty_exempt_id != @$acct->pen_exempt){$penalty_exempt_change=1;}
		if(@$residence_date != @$acct->install_date){$residence_date_change=1;}
		if(@$employee != @$acct->is_employee){$employee_change=1;}

		if(@$tin_id != @$acct->tin_id){$tin_id_change=1;}
		if(@$other_id != @$acct->other_id){$other_id_change=1;}
		
		//~ echo '<pre>';
		//~ echo $new_birth;
		//~ die();

		if($employee_change == 1)
		{
			$from_01 = 'NO';
			$to_01 = 'NO';
			
			if( @$acct->is_employee == 1) {
				$from_01 = 'YES';
			}

			if(@$employee == 1 ){
				$to_01 = 'YES';
			}

			$change_labels[] = '
				IS_EMPLOYEE Change from '.$from_01.'
				to '.$to_01.'
			';

		}

		if($name_change == 1)
		{
			$from_ful_name = $acct->lname.', '.$acct->fname.' '.$acct->mi;
			$to_ful_name = $lname.', '.$fname.' '.$mi;

			$change_labels[] = '
				Name Change from '.$from_ful_name.'
				to '.$to_ful_name.'
			';
		}//

		if($birth_change == 1)
		{
			$date_change_from = date('F d, Y', strtotime($acct->birth_date));
			
			if(!empty($new_birth)){
				$date_change_to   = date('F d, Y', strtotime($new_birth));
			}else{
				$date_change_to   = 'NONE';
			}

			$change_labels[] = '
				Date of birth change from '.$date_change_from.'
				to '.$date_change_to.'
			';
		}

		if($address_change == 1)
		{
			$address1_from = $acct->address1;
			$address1_to   = $address1;

			$change_labels[] = '
				Address change from '.$address1_from.'
				to '.$address1_to.'
			';
		}

		if($zone_change == 1)
		{
			$from1 = get_zone101($acct->zone_id);
			$to1   = get_zone101($zone);

			$change_labels[] = '
				Zone change from '.$from1.'
				to '.$to1.'
			';
		}

		if($acct_type_change == 1)
		{
			$from1 = ctype_str($acct->acct_type_key);
			$to1   = ctype_str($acct_type);

			$change_labels[] = '
				Account type change from '.$from1.'
				to '.$to1.'
			';
		}

		if($acct_status_change == 1)
		{
			$from1 = acct_status($acct->acct_status_key);
			$to1   = acct_status($acct_status);

			$change_labels[] = '
				Account status change from '.$from1.'
				to '.$to1.'
			';
		}

		if($phone_change == 1)
		{
			$from1 = ($acct->tel1);
			$to1   = ($phone);

			$change_labels[] = '
				Contact info change from '.$from1.'
				to '.$to1.'
			';
		}

		if($discount_change == 1)
		{
			$from1 = get_dis_lab($acct->acct_discount);
			$to1   = get_dis_lab($discount_type);

			$change_labels[] = '
				Discount type change from '.$from1.'
				to '.$to1.'
			';
		}

		if($penalty_exempt_change  == 1){
			//~ if(@$penalty_exempt_id != @$acct->pen_exempt){$penalty_exempt_change=1;}
			$from1 = ($acct->pen_exempt)==1?'Yes':'No';
			$to1   = ($penalty_exempt_id)==1?'Yes':'No';
			$change_labels[] = 'Penalty Exemption '.$from1.' to '.$to1.'';
		}

		if($residence_date_change == 1){
			$from1 = date('F d, Y',strtotime($acct->install_date));
			$to1   = date('F d, Y',strtotime($residence_date));
			$change_labels[] = 'Installation Date Change '.$from1.' to '.$to1.'';
		}


		if(@$meter_size_id != (int) $acct->meter_size_id){
			$from1 = meter_size_label($acct->meter_size_id);
			$to1   = meter_size_label($meter_size_id);
			$change_labels[] = 'Meter size change from '.$from1.' to '.$to1.'';
		}//

		if($tin_id_change == 1){
			$from1 = ($acct->tin_id);
			$to1   = ($tin_id);
			$change_labels[] = 'TIN ID from '.$from1.' to '.$to1.'';
		}//

		if($other_id_change == 1){
			$from1 = ($acct->other_id);
			$to1   = ($other_id);
			$change_labels[] = 'OTHER ID from '.$from1.' to '.$to1.'';
		}//


		if(count($change_labels) <= 0)
		{
			$request->session()->flash('success', 'No update is implemented');
			return Redirect::to(URL::previous() . "#account_list");
		}


		//~ print_r($change_labels);
		//~ die();

		$acct->is_employee = @$employee;
		$acct->fname = $fname;
		$acct->lname = $lname;
		$acct->mi = $mi;
		$acct->address1 = $address1;
		$acct->tel1 = $phone;
		$acct->zone_id = @$zone;
		$acct->acct_type_key = @$acct_type;
		$acct->acct_status_key = @$acct_status;
		$acct->acct_discount = @$discount_type;
		$acct->birth_date = @$birth;
		$acct->meter_size_id = @$meter_size_id;
		$acct->pen_exempt = (int) @$penalty_exempt_id;

		$acct->tin_id = @$tin_id;
		$acct->other_id = @$other_id;

		//~ $acct->install_date = date('Y-m-d',strtotime(@$residence_date));
		if(!empty(@$residence_date)){
			$acct->install_date = date('Y-m-d',strtotime(@$residence_date));
		}else{
			$acct->install_date = null;
		}
		//~ die();
		$acct->save();

		if ($acct->acct_status_key == 1) {
			$acct->status = 'new concess.';
			// $acct->save();
		}

		if ($acct->acct_status_key == 2) {
			$acct->status = 'active';
			// $acct->save();
		}

		if ($acct->acct_status_key == 3) {
			$acct->status = 'For Disconnection';
			// $acct->save();
		}

		if ($acct->acct_status_key == 4) {
			$acct->status = 'disconnected';
			// $acct->save();
		}

		if ($acct->acct_status_key == 5) {
			$acct->status = 'for reconnection';
			// $acct->save();
		}

		if ($acct->acct_status_key == 6) {
			$acct->status = 'pending';
			// $acct->save();
		}

		if ($acct->acct_status_key == 15) {
			$acct->status = 'voluntary dis.';
			// $acct->save();
		}

		$acct->save();
		

		$info1 = '<ul>';
		foreach($change_labels as $cl1){$info1.='<li>'.$cl1.'</li>';}
		$info1 .= '</ul>';

		$new_led = new HwdLedger;
		$new_led->led_type = 'account';
		$new_led->led_title = 'Account modificaton';
		$new_led->status1 = 'active';
		$new_led->led_key1 = $acct->id;
		$new_led->led_date2 = date('Y-m-d H:i:s');
		$new_led->led_desc1 = $info1;
		$new_led->ctyp1 = 'account_modify';
		$new_led->save();

		if($acct_status_change == 1)
		{
			$stat1  = con_led_type_v2($acct_status);
			$stat2  = con_led_type_v3($acct_status);

			$new_cled = new ConnectionLedger;
			$new_cled->acct_id = $acct->id;
			$new_cled->acct_no = $acct->acct_no;
			$new_cled->status	 = 'active';
			$new_cled->typ1 = $stat1;
			$new_cled->remaks = 'Account modificaton - '.$stat2;
			$new_cled->date1 = date('Y-m-d');
			$new_cled->save();
		}//




		$request->session()->flash('success', 'New Account Updated');
		return Redirect::to(URL::previous() . "#account_list");
		echo '<pre>';
		print_r($_POST);
	}

	function AccountDelete(Request $request){
	}

	function DeleteAccount($acct_id, $acct_no, Request $request){
		$acct1 = Accounts::where('id', $acct_id)->where('acct_no', $acct_no)->first();
		if(!$acct1){
			$request->session()->flash('success', 'Account not found');
			return Redirect::to(URL::previous() . "#account_list");
		}

		$acct1->status = 'deleted';
		$acct1->save();

		$request->session()->flash('success', 'Account #'.$acct_no.' Deleted');
		return Redirect::to(URL::previous() . "#account_list");

	}

	function DisconnectAccount($acct_id, $acct_no, Request $request){
		$acct1 = Accounts::where('id', $acct_id)->where('acct_no', $acct_no)->first();
		if(!$acct1){
			$request->session()->flash('success', 'Account not found');
			return Redirect::to(URL::previous() . "#account_list");
		}

		$acct1->acct_status_key = 4;
		$acct1->save();

		$request->session()->flash('success', 'Account #'.$acct_no.' is now disconnected.');
		return Redirect::to(URL::previous() . "#account_list");

	}

	function ReconnectAccount($acct_id, $acct_no, Request $request)
	{
		$acct1 = Accounts::where('id', $acct_id)->where('acct_no', $acct_no)->first();
		if(!$acct1){
			$request->session()->flash('success', 'Account not found');
			return Redirect::to(URL::previous() . "#account_list");
		}

		$acct1->acct_status_key = 2;
		$acct1->save();

		$request->session()->flash('success', 'Account #'.$acct_no.' is now reconnected.');
		return Redirect::to(URL::previous() . "#account_list");

	}


	/**************/
	/**************/
	function AccountTypeNew(Request $request){
		extract($_POST);
		//echo '<pre>';
		//print_r($_POST);
		/**/
		$new_meta = new AccountMetas;
		$new_meta->meta_name	 = $name;
		$new_meta->meta_code	 = $code;
		$new_meta->meta_desc	 = $descr;
		$new_meta->meta_type	 = 'account_type';
		$new_meta->status		 	= $status;
		$new_meta->save();
		/**/
		$request->session()->flash('success', 'New Account Type Added');
		return Redirect::to(URL::previous() . "#account_type");
		//return redirect()->back();

	}

	function AccountTypeUpdate(Request $request){
		extract($_POST);

		$new_meta = AccountMetas::where('id', $id)->where('meta_type', 'account_type')->first();
		$new_meta->meta_name	 = $name;
		$new_meta->meta_code	 = $code;
		$new_meta->meta_desc	 = $descr;
		$new_meta->status		 	= $status;
		$new_meta->save();

		$request->session()->flash('success', 'Account Type Updated');
		return Redirect::to(URL::previous() . "#account_type");
	}

	function AccountTypeDelete(Request $request){
	}

	function AddBeginningBalAmt($amnt, $acct_id, $acct_no, Request $request)
	{

			$first1 = Accounts::where('id', $acct_id)
							->where('acct_no', $acct_no)
								->first();

			if(!$first1){
				$request->session()->flash('success', 'Account not found.');
				return Redirect::to(URL::previous() . "#account_list");
			}

		$beg01 = LedgerData::where('acct_id', $first1->id)
						->where('led_type','beginning')
						->first();

		if(!$beg01)
		{
			$ldd = new LedgerData;
			$ldd->acct_id = $first1->id;
			$ldd->bill_id = 0;
			$ldd->read_id = 0;
			$ldd->arrear = (float)$amnt;
			$ldd->billing = 0;
			$ldd->payment = 0;
			$ldd->status = 'active';
			$ldd->ttl_bal = (float)$amnt;
			$ldd->led_type = 'beginning';
			$ldd->ledger_info = trim($_GET['remaks']);
			$ldd->date01 = date('Y-m-d');
			$ldd->acct_num = trim($acct_no);
			$ldd->save();

			$new_arrer = new Arrear;
			$new_arrer->acct_id = $first1->id;
			$new_arrer->acct_id_str = trim($acct_no);
			$new_arrer->amount = (float)$amnt;
			$new_arrer->arr_type = 'beginning';
			$new_arrer->save();

				$request->session()->flash('success', 'Beginning balance is added');
				return Redirect::to(URL::previous() . "#account_list");

		}else{
				$request->session()->flash('success', 'Beginning balance is already exist');
				return Redirect::to(URL::previous() . "#account_list");
		}



		//~ echo $_GET['remaks'];
		//~ echo $amnt;
		//~ echo $amnt;
		//~ echo $amnt;
	}

	/**************/
	/**************/

	function AccountStatusNew(Request $request){
		extract($_POST);

		$new_meta = new AccountMetas;
		$new_meta->meta_name	 = $name;
		$new_meta->meta_code	 = $code;
		$new_meta->meta_desc	 = $descr;
		$new_meta->meta_type	 = 'account_status';
		$new_meta->status		 	= $status;
		$new_meta->save();
		/**/
		$request->session()->flash('success', 'Account status added');
		return Redirect::to(URL::previous() . "#account_status");
		/**/
		echo '<pre>';
		print_r($_POST);
	}

	function AccountStatusUpdate(Request $request){
		extract($_POST);


		$new_meta = AccountMetas::where('id', $id)->where('meta_type', 'account_status')->first();
		$new_meta->meta_name	 = $name;
		$new_meta->meta_code	 = $code;
		$new_meta->meta_desc	 = $descr;
		$new_meta->status		= $status;
		$new_meta->save();

		$request->session()->flash('success', 'Account status updated');
		return Redirect::to(URL::previous() . "#account_status");

	}

	function AccountStatusDelete(){
	}


	/*BBBBBBBBBB*/
	/*BBBBBBBBBB*/
	/*BBBBBBBBBB*/
	/*BBBBBBBBBB*/


	/*BBBBBBBBBB*/
	/*BBBBBBBBBB*/
	/*BBBBBBBBBB*/
	/*BBBBBBBBBB*/

	function BBBBB(){}
	function bbbbbb(){}

	function billing_billing_get_price_rates_list()
	{
		$meta_id = (int) @$_GET['rr_id'];
		$my_meta = BillingMeta::where('meta_type', 'billing_rates')
					->where('status', 'active')
						->where('id', $meta_id)
							->first();

		$data1 = json_decode($my_meta->meta_data);

		$new_arr = array();

		if(!empty($data1->meta_id))
		{
			foreach(@$data1->meta_id as $kk=>$vv){
				$new_arr[$vv]['mc'] = @$data1->min_charge[$kk];
				$new_arr[$vv]['pr'] = @$data1->price_rate[$kk];
			}
		}

		//~ echo '<pre>';
		//~ print_r($data1);
		//~ print_r($new_arr);
		//~ die();


		if(!$my_meta){
			return array('status'=>'failed', 'msg'=>'Failed to find meta');
		}

		$price_rates1 = json_decode($my_meta->prince_rates);

		$meter_sizes = BillingMeta::where('meta_type', 'meter_size')
							->where('status', 'active')
								->orderBy('nsort','asc')
									->get();

		//~ var_dump($price_rates1);
		//~ die();


		$html1 = '';
		@$html1.= ' '.$data1->min_charge.' / '.$data1->price_rate;

		$html1.=  '<table class="mm11">
						<tr>
							<td>Size</td>
							<td>Min Charge</td>
							<td>Rate per c.u.m.</td>
						</tr>';
		
		$ttt = 0;
		
		foreach($meter_sizes as $mss):
		
			$rat1 = @$new_arr[$mss->id];

			$html1.= '
				<tr>
					<td>'.strtoupper($mss->meta_name).' <input type="hidden" name="meta_id[]"  value="'.$mss->id.'"   /></td>
					<td>
						<input type="number"  min="0" class="form-control nn'.$mss->id.'  gg1_'.$ttt.' " autocomplete="off"  placeholder="Php."  name="min_charge[]"   step="any"  value="'.@$rat1['mc'].'"   >
					</td>
					<td>
						<input type="number"  min="0"  class="form-control mm'.$mss->id.' gg2_'.$ttt.' " autocomplete="off"  placeholder="Php."  name="price_rate[]"  step="any"  value="'.@$rat1['pr'].'">
					</td>
				</tr>
			';
			
			$ttt++;
		endforeach;
		$html1 .= '</table>';


		return array('status'=>'success', 'msg'=> 'Data recieved', 'html1' => $html1);



	}//


	function billing_billing_get_meter_sizes1001()
	{
		$bil1 = BillingMeta::where('meta_type', 'meter_size')
						->where('status', 'active')
							->orderBy('nsort','asc')
							->get();

		if($bil1->count() == 0)
		{return array('status' => 'failed', 'msg' => 'No List found');}

		$html1 = '<table class="meter_size1">';
		$html1 .= '<tr class="heading11">';
		$html1 .= '<td>Seq</td>';
		$html1 .= '<td>Name</td>';
		$html1 .= '<td>&nbsp;</td>';
		$html1 .= '</tr>';

		$x=1;
		foreach($bil1 as $bb):
		$html1 .= '<tr>';
		$html1 .= '<td>'.$bb->nsort.'</td>';
		$html1 .= '<td>'.$bb->meta_name.'</td>';
		$html1 .= '<td><button onclick="edit_meter_size_223('.($x-1).')">Edit</button></td>';
		$html1 .= '</tr>';
		$x++;
		endforeach;
		$html1 .= '</table>';



		return array(
			'status' => 'success',
			'msg' => 'Data Retieved',
			'html1' => $html1,
			'data1' => $bil1
		);


	}//

	function billing_billing_add_rates_meter_size(Request $req)
	{
		$val = trim(@$_GET['val']);
		$tx = trim(@$_GET['tx']);
		$meta_id = (int) trim(@$_GET['meta_id']);
		$nsort = (int) trim(@$_GET['nsort']);


		if($meta_id <= 0)
		{

			$bil1 = BillingMeta::where('meta_type', 'meter_size')
						->where('meta_name', 'like', $val)
							->where('status', 'active')
								->first();

			if($bil1){
				return array('status' => 'failed', 'msg' => $val.' is already exist');
			}

			$new_met = new BillingMeta;
			$new_met->meta_type = 'meter_size';
			$new_met->meta_name = $val;
			$new_met->status = 'active';
			$new_met->nsort = 9999;
			$new_met->save();
		}else{
			$bil1 = BillingMeta::where('meta_type', 'meter_size')
						//->where('meta_name', 'like', $val)
						->where('id', $meta_id)
							->where('status', 'active')
								->first();

			if(!$bil1){
				return array('status' => 'failed', 'msg' => 'Account not found');
			}

			$bil1->meta_name = $val;
			$bil1->nsort = $nsort;
			$bil1->save();
		}


		return Redirect::to('/billing_billing_add_rates_meter_size_redirect/v1', 301);
	}


	function billing_billing_add_rates_meter_size_redirect()
	{
		return array('status' => 'success', 'msg' => 'Meter updated');
	}









	function BillingBilling_START()
	{
	}


	function BillingMain__BillingProccess1()
	{

		$current_bill_request = HwdRequests::where('req_type','generate_billing_period_request')
								->where(function($query){
										$query->orWhere('status', 'ongoing');
										$query->orWhere('status', 'completed');
									})
								->orderBy('dkey1', 'desc')
								->first();

		if($current_bill_request){
			$current_bill_request = $current_bill_request->toArray();
		}else{
			$current_bill_request = [];
		}




		$current_period = date('Y-m', strtotime(@$current_bill_request['dkey1']));

		$zonebilling = Zones::where('status', 'active')
									->with(['billzone' => function($query)use($current_bill_request){
											$query->where('bill_period_id', @$current_bill_request['id']);
									}, 'billzone.bill_request'])
									->orderBy('zone_name', 'ASC')
										->get();

		//~ echo $current_period;
		//~ echo '<pre>';
		//~ print_r($zonebilling->toArray());
		//~ die();

		return compact('zonebilling', 'current_bill_request');
	}


	function print_bill()
	{
		$bill_id = (int) @$_GET['bid'];
		$typ1    = (int) @$_GET['typ'];
		if( $bill_id <= 0 ) { echo 'Failed..';die();}

		$curr_bill = BillingMdl::find($bill_id);
		$curr_bill->account;
		$curr_bill->reading1;
		$curr_bill = $curr_bill->toArray();

		$meter_sizes = BillingMeta::meter_size();
		$acct_types  = AccountMetas::acct_type();
		$zones       = Zones::list();
		$officer     = User::list_all();

		$reading_info = ReadingPeriod::reading_info($curr_bill['period']);
		
		// ee($officer);
		// ee($reading_info);
		// ee(GOV_TYPE);
		// ee($curr_bill);

		return view('billings.billing_print', compact(
			'curr_bill',
			'meter_sizes',
			'acct_types',
			'zones',
			'reading_info',
			'officer',
			'typ1'
		));		

		// ee($curr_bill->toArray());

	}

	function BillingMain($r_year=null, $r_month=null, $acct_num=null, $meter_num=null, $lname=null, $zone=null)
	{

		$VV11 = $this->BillingMain__BillingProccess1();
		extract($VV11);

		$is_r_year = $r_year;
		$is_r_month = $r_month;
		$is_acct_num = $acct_num;

		if(empty($is_r_year))
		{
			$r_year = date('Y');
			$r_month = date('m');
			$curr_period = date('Y-m');
		}else{
			$curr_period = date('Y-m', strtotime($r_year.'-'.$r_month.'-28'));
		}

		//~ echo $curr_period;
		//~ die();

		$status_key_active_raw  =  AccountMetas::where('meta_type', 'account_status')
								->where('old_id', '1')
								->first();

		$active_id = $status_key_active_raw->id;

		$billing_res = Accounts::
					where('acct_status_key', $active_id)
					->where('status', '!=','deleted')
					->whereHas('bill1', function($query)use($curr_period){
						$query->where('period','like' ,$curr_period.'%');
						$query->where('status','active');
					})
					->with(['bill1' => function($query)use($curr_period){
						$query->where('period','like', $curr_period.'%');
						$query->where('status','active');
						$query->with(['nw_bill'=> function($q1) use($curr_period)  {
							$q1->where('date1', date('Y-m-01', strtotime($curr_period)));
							$q1->where('typ', 'nw_child');
							$q1->where('status', 'billed');
						}]);
						// ->leftJoin('mysql_db.orders as o', 'u.id', '=', 'o.user_id')

					}])
					->with(['ledger_data3'=>function($query)use($curr_period){
						$prev_per = date('Y-m', strtotime($curr_period.' -1 month'));
						//~ echo $prev_per;
						//~ die();
						//~ die();
						$query->where('period', 'like', $prev_per.'%');

					}])

					->with(['ledger_data4'=>function($query)use($curr_period){
						$prev_per = date('Y-m', strtotime($curr_period));
						//~ echo $prev_per;
						//~ die();
						//~ die();
						$query->where('period', 'like', $prev_per.'%');
						$query->where('led_type', 'billing');

					}])
					->with(['arrears3' => function($query)use($curr_period){
						$prev_per = date('Y-m', strtotime($curr_period.' -1 month'));
						$query->where('period', 'like', $curr_period.'%');
					}])
					->where(function($query)use( $acct_num, $meter_num, $lname, $zone){

						if($acct_num != 'none'  &&  !empty($acct_num))
						{
							$query->where('acct_no', 'like', '%'.$acct_num);
						}

						if($meter_num != 'none'  && !empty($meter_num))
						{
							$query->where('meter_number1', 'like', '%'.$meter_num);
						}

						if($lname != 'none'  && !empty($lname))
						{
							$query->where('lname', 'like', $lname.'%');
						}

						if($zone != 'none'  &&  !empty($zone)  &&  $zone != 'all')
						{
							$query->where('zone_id', 'like', $zone);
						}

					})
					->orderBy('route_id', 'asc')
					->paginate(200)
					->toArray();
					//->get();

				// foreach($billing_res['data'] as $k => $v) {
				// 	// $v['bill1']['period'];
				// }	

				// ee($billing_res['data'], __FILE__, __LINE__);					

				//~ echo '<pre>';
				//~ foreach($billing_res['data'] as $kk => $vv)
				//~ {
					//~ echo '<br />';
					//~ print_r($vv['bill1']);
					//~ $curr_bill = $vv['bill1']['curr_bill'];
				//~ }

				//~ die();



		$data1 = $this->__billingAccountExtraData();
		extract($data1);


		$print_serve = PrintServ::orderBy('id', 'desc')
									->with('zone_info')
									->limit(1)
									->get();

		$overdue  = OverdueStat::orderBy('date1', 'asc')
					//~ ->where('zone_id', 1)
					->get();

		//~ echo $curr_period;
		//~ echo '<pre>';
		//~ print_r($overdue->toArray());
		//~ die();
		
		

		$collectors = Role::with('users')
									->where('name', 'collection_officer')
									->first();
									
		// ee($billing_res, __FILE__, __LINE__);

		if(!empty($is_r_year))
		{

			if(!empty($is_acct_num)){
				return compact(
								'overdue',
								'billing_res',
								'zones',
								'acct_type',
								'bill_rates',
								'bill_discount',
								'hw1_requests',
								'r_year',
								'r_month',
								'acct_num',
								'meter_num',
								'lname',
								'zone',
								'active_acct_count',
								'billed_acct',
								'print_serve',
								'zonebilling',
								'current_bill_request',
								'curr_period',
								'collectors'
						);
			}

			return  compact(
				'overdue',
				'billing_res',
				'zones',
				'acct_type',
				'bill_rates',
				'bill_discount',
				'hw1_requests',
				'r_year',
				'r_month',
				'active_acct_count',
				'billed_acct',
				'print_serve',
				'zonebilling',
				'current_bill_request',
				'curr_period',
				'collectors'

			);
		}

		return view('billings.billing', compact(
			'overdue',
			'billing_res',
			'zones',
			'acct_type',
			'bill_rates',
			'bill_discount',
			'hw1_requests',
			'r_year',
			'r_month',
			'active_acct_count',
			'billed_acct',
			'print_serve',
			'zonebilling',
			'current_bill_request',
			'curr_period',
			'collectors'
		));

		//~ echo '<pre>';
		// ~ print_r($billing_res);

	}//BillingMain

	function BillingMainDate($r_year, $r_month)
	{
		$arr_1 = $this->BillingMain($r_year, $r_month);
		extract($arr_1);
		return view('billings.billing', compact('billing_res', 'zones', 'acct_type', 'bill_rates', 'bill_discount', 'hw1_requests', 'r_year', 'r_month', 'active_acct_count', 'billed_acct', 'print_serve', 'curr_period'));
	}//BillingMain

	function BillingMainDateFilter($r_year, $r_month, $acct_num, $meter_num, $lname, $zone)
	{
		$VV11 = $this->BillingMain__BillingProccess1();
		extract($VV11);

			$arr_1 = $this->BillingMain($r_year, $r_month, $acct_num, $meter_num, $lname, $zone);
			extract($arr_1);
			return view('billings.billing', compact(
					'overdue',
					'billing_res',
					'zones',
					'acct_type',
					'bill_rates',
					'bill_discount',
					'hw1_requests',
					'r_year',
					'r_month',
					'acct_num',
					'meter_num',
					'lname',
					'zone',
					'active_acct_count',
					'billed_acct',
					'print_serve',
					'zonebilling',
					'current_bill_request',
					'curr_period'
			));
	}//End

	/**
	 *
	 *
	 *
	 * */

	//BillingMainDateFilter222($r_year, $r_month, $acct_num, $meter_num, $lname, $zone)
	function BillingMain222()
	{
			$r_year = date('Y');
			$r_month = date('m');

			$billing_res = $this->___billingAccountGetData2($r_year, $r_month);
			$data1 = $this->__billingAccountExtraData();
			extract($data1);

			return view('billings.billing', compact('billing_res', 'zones', 'acct_type', 'bill_rates', 'bill_discount', 'hw1_requests', 'r_year', 'r_month', 'active_acct_count', 'billed_acct'));
			echo '<pre>';
			print_r($billing_res);
	}//BillingMain


	function BillingMainDate222($r_year, $r_month)
	{
			$billing_res = $this->___billingAccountGetData2($r_year, $r_month);
			$data1 = $this->__billingAccountExtraData();
			extract($data1);
			return view('billings.billing', compact('billing_res', 'zones', 'acct_type', 'bill_rates', 'bill_discount', 'hw1_requests',  'r_year', 'r_month', 'active_acct_count', 'billed_acct'));
			echo '<pre>';
			print_r($billing_res);
	}


	function BillingMainDateFilter222($r_year, $r_month, $acct_num, $meter_num, $lname, $zone)
	{

		$billing_res = $this->___billingAccountGetData2($r_year,
			$r_month, $acct_num, $meter_num, $lname, $zone);
		$data1 = $this->__billingAccountExtraData();
		extract($data1);
		return view('billings.billing', compact(
				'billing_res',
				'zones',
				'acct_type',
				'bill_rates',
				'bill_discount',
				'hw1_requests',
				'r_year',
				'r_month',
				'acct_num',
				'meter_num',
				'lname',
				'zone',
				'active_acct_count',
				'billed_acct'
		));
	}

	function BillingBilingHwdJobAdd1($request_id, $period, Request $request){

			$hwd1 = HwdRequests::find($request_id);

			if(!$hwd1){
					$request->session()->flash('success', 'Failed to generate');
					return Redirect::to(URL::previous() . "#period_request");
			}

			$hwd1->status = 'ongoing';
			$hwd1->save();

			$jj = new HwdJob;
			$jj->jtype = 'generate_billing1';
			$jj->jcmd = '/service/generate_billing1/'.$period;
			$jj->jstatus = 'ongoing';
			$jj->jdata = json_encode($hwd1->toArray());
			$jj->save();


			$request->session()->flash('success', 'Done Generated');
			return Redirect::to(URL::previous() . "#period_request");
	}


	function  BillingBilingRequestPeriod(Request $request){
		extract($_POST);

		// echo '<pre>';
		// print_r($_POST);
		// die();

		$hw1 = HwdRequests::where(function($query) use($period_month, $period_year){
							$query->where('other_datas','like', '%'.'"period_month":"'.$period_month.'"'.'%');
							$query->where('other_datas','like', '%'.'"period_year":"'.$period_year.'"'.'%');
						})
						->where(function($query){
								$query->orWhere('status','completed');
								$query->orWhere('status','ongoing');
								$query->orWhere('status','pending');
								$query->orWhere('status','approved');
						})
						->where('req_type','generate_billing_period_request')
						->first();

		if($hw1){
			$request->session()->flash('success', 'Billing requested is already exit');
			return Redirect::to(URL::previous() . "#period_request");
		}

		$rate_version = BillingRateVersion::orderBy('id', 'desc')->first()->toArray();

		$post11 = $_POST;
		$post11['rate_version'] = $rate_version['id'];

		// echo '<pre>';
		// print_r($_POST);
		// die();

		$req1  =  new  HwdRequests;
		$req1->req_type = 'generate_billing_period_request';
		$req1->remarks = $dis_desc;
		$req1->other_datas = json_encode($post11);

		$post_1 = json_encode($_POST);
		$ot_dta = json_decode($post_1);
		$period_xx = date('Y-m-28', strtotime($ot_dta->period_year.'-'.$ot_dta->period_month));
		$req1->dkey1 = $period_xx;

		$req1->status = 'pending';
		$req1->save();

		$request->session()->flash('success', 'Billing Period Requested');
		return Redirect::to(URL::previous() . "#period_request");

	}




	function UpdateAcctTypeProcess($acct_type001, $acct_no, $acct_id, $billing_id, Request $request)
	{

		$acct1 = Accounts::where('acct_no', trim($acct_no))
					->where('id', trim($acct_id))
						->first();

		if(!$acct1){
			$request->session()->flash('success', 'Failed to find accounts!');
			return Redirect::to(URL::previous() . "#accounts");
		}


		$billing1 = BillingMdl::find((int)$billing_id);


		if(!$billing1){
			$request->session()->flash('success', 'Failed to find billing!');
			return Redirect::to(URL::previous() . "#accounts");
		}



		$billing1->reading1;
		$rate_version = BillingRateVersion::orderBy('id', 'desc')
							->first();

		$data11 = ServiceCtrl::___rates_and_discounts();
		extract($data11);

		$acct_type = array();
		$acctype1 = AccountMetas::where('meta_type', 'account_type')->get();



		foreach($acctype1 as $att)
		{
			$acct_type[$att->id] = $att->toArray();
		}

		$data11['acct_type'] = $acct_type;


		$old_ctype = ctype_str($acct1->acct_type_key);
		$new_ctype = ctype_str($acct_type001);


		$acct1->acct_type_key= $acct_type001;
		$acct1->save();



		//~ echo ctype_str($acct1->toArray);
		//~ echo '<pre>';
		//~ print_r($acct1->toArray());
		//~ echo $acct1->acct_type_key;
		//~ die();



		ServiceCtrl::proccess_arrears($billing1->reading1, $billing1->reading1->period);
		ServiceCtrl::proccess_billing($billing1->reading1, $billing1->reading1->period, $data11);

		$new_led = new HwdLedger;
		$new_led->led_type = 'account';
		$new_led->led_title = 'Account type changed from '.$old_ctype.' to '.$new_ctype;
		$new_led->status1 = 'active';
		$new_led->led_key1 = $acct1->id;
		$new_led->led_date2 = date('Y-m-d H:i:s');

		$info001 = '
		   <ul  class="item_list1">
			<li>Previous Account Type : <span>'.$old_ctype.'</span></li>
			<li>New Account Type : <span>'.$new_ctype.'</span></li>
		   </ul>
		';

		$new_led->led_desc1 = $info001;
		$new_led->ctyp1 = 'account_type';
		$new_led->save();




		$request->session()->flash('success', 'Account type changed');
		return Redirect::to(URL::previous() . "#accounts");

	}//


	//function fix_via_old_ledger($acct_no, $acct_id, $billing_id, Request $request)
	function FixViaOldLedger($acct_no, $acct_id, $billing_id, Request $request)
	{
		$acct1 = Accounts::where('acct_no', trim($acct_no))
					->where('id', trim($acct_id))
						->first();

		if(!$acct1){
			$request->session()->flash('success', 'Failed to find accounts!');
			return Redirect::to(URL::previous() . "#accounts");
		}


		$billing1 = BillingMdl::find((int)$billing_id);


		if(!$billing1){
			$request->session()->flash('success', 'Failed to find billing!');
			return Redirect::to(URL::previous() . "#accounts");
		}


		$period = date('Y-m', strtotime($billing1->period));
		$bill_date = $period.'-'.$acct1->zone_id;


		$acct_ledger = LedgerData::where('date01', $bill_date)->
				where('acct_id', $acct_id)->
				where('period', $period.'-01')
				->where('status','active')
				->first();

		if(!$acct_ledger){
			$request->session()->flash('success', 'Ledger Not found');
			return Redirect::to(URL::previous() . "#accounts");
		}

		$bill_total = $acct_ledger->ttl_bal;


		$arrear1 = Arrear::where('acct_id', $acct_id)->where('period',$period.'-01')->first();

		$bill1 = BillingMdl::where('account_id', $acct_id)->where('period', $period.'-01')->first();

		$bill1->curr_bill = $acct_ledger->billing;
		$bill1->billing_total = $acct_ledger->ttl_bal;
		$bill1->arrears = $arrear1->amount;
		$bill1->save();

		$request->session()->flash('success', 'Done Fixed');
		return Redirect::to(URL::previous() . "#accounts");


		echo '<pre>';
		//~ print_r($acct1->toArray());
		//~ print_r($billing1->toArray());
		print_r($acct_ledger->toArray());
		print_r($arrear1->toArray());
		print_r($bill1->toArray());



	}


	function BillingBilling_END()
	{
	}


	/*************/
	/*************/
	function BillingBilingRatesAdd(Request $request){
		extract($_POST);

		$new_rates = new BillingMeta;
		$new_rates->meta_name = $rname;
		$new_rates->meta_desc = $rate_desc;
		$new_rates->meta_type = 'billing_rates';
		$new_rates->status = 'active';
		$new_rates->meta_data = json_encode($_POST);
		$new_rates->save();

		$rates1 = BillingMeta::where('meta_type', 'billing_rates')
					->select(DB::raw('id,meta_data,updated_at'))
					->get()->toArray();

		$ratesV1 = new BillingRateVersion;
		$ratesV1->rates_description	 = "Billing Rates as of ".date('Y-m-d  H:i:s');
		$ratesV1->meta_data = json_encode($rates1);
		$ratesV1->save();

		$request->session()->flash('success', 'Billing Rates Added');
		return Redirect::to(URL::previous() . "#rates");
	}


	function BillingBilingRatesUpdate(Request $request)
	{
		extract($_POST);

		//~ echo '<pre>';
		//~ echo print_r($_POST);
		//~ die();

		$new_rates = BillingMeta::where('id', $rate_id)
						//~ ->where('status', strtolower($rate_sta1))
							->first();
		
		if(strtolower($rate_sta1) == 'active')
		{
			$new_rates->meta_name = $rname;
			$new_rates->meta_desc = $rate_desc;
			$new_rates->meta_data = json_encode($_POST);
		}else{
			$new_rates->status = 'deleted';
		}
		$new_rates->save();

		$rates1 = BillingMeta::where('meta_type', 'billing_rates')
					->where('status', 'active')
					->select(DB::raw('id,meta_data,updated_at'))
					->get()
					->toArray();

		$ratesV1 = new BillingRateVersion;
		$ratesV1->rates_description	 = "Billing Rates as of ".date('Y-m-d  H:i:s');
		$ratesV1->meta_data = json_encode($rates1);
		$ratesV1->save();

		$request->session()->flash('success', 'Billing Rates Updated');
		return Redirect::to(URL::previous() . "#rates");
		
		echo '<pre>';
		print_r($_POST);
	}

	/*************/
	/*************/

	function BillingBilingDiscountAdd(Request $request){
		extract($_POST);

		$new_discount = new BillingMeta;
		$new_discount->meta_name = $dis_name;
		$new_discount->meta_desc = $dis_desc;
		$new_discount->meta_value = $dis_value;
		$new_discount->meta_data = json_encode($_POST);
		$new_discount->meta_type = 'billing_discount';
		$new_discount->status = 'active';
		$new_discount->save();

		$request->session()->flash('success', 'Billing Discount Added');
		return Redirect::to(URL::previous() . "#discounts");

		echo '<pre>';
		print_r($_POST);
	}

	function BillingBilingDiscountUpdate(Request $request){
		extract($_POST);
		$new_discount = BillingMeta::find($dis_id);
		$new_discount->meta_name = $dis_name;
		$new_discount->meta_desc = $dis_desc;
		$new_discount->meta_value = $dis_value;
		$new_discount->meta_data = json_encode($_POST);
		$new_discount->save();

		$request->session()->flash('success', 'Billing Discount Updated');
		return Redirect::to(URL::previous() . "#discounts");
	}


	function BillingBilingZoneBillingStart($zone_id, $bill_period_id, Request $request){

			$sbz1 = ServiceBillZone::where('zone_id', $zone_id)
							->where('bill_period_id', $bill_period_id)
								->with('bill_request')
									->first();
			//~ echo '<pre>';
			//~ print_r($sbz1);
			//~ die();

			$fine_date = @$_GET['fine_date'];
			$period = @$_GET['period'];

			if(!$sbz1){
					$new_sbz = new ServiceBillZone;
					$new_sbz->zone_id = $zone_id;
					$new_sbz->bill_period_id = $bill_period_id;
					$new_sbz->status = 'ONPROGRESS';
					$new_sbz->pen_stat = 'pending';
					$new_sbz->pen_date = $fine_date;
					$new_sbz->period = $period;
					$new_sbz->save();
			}else{
				$sbz1->status = 'ONPROGRESS';
				$sbz1->pen_date = $fine_date;
				$sbz1->period = $period;
				$sbz1->pen_stat = 'pending';
				$sbz1->save();
			}

			//~ $request->session()->flash('success', 'Billing Discount Updated');
			return Redirect::to(URL::previous() . "#bill_procces_period");
	}

	function BillingBilingReprocessBilling($billing_id, Request $request)
	{
			$bill1 = BillingMdl::find($billing_id);

			if(!$bill1){
				$request->session()->flash('success', 'Billing Not Found');
				return Redirect::to(URL::previous() . "#accounts");
				exit();
			}

			$bill1->reading1;

			$rate_version = BillingRateVersion::orderBy('id', 'desc')
					->first();

			$data11 = ServiceCtrl::___rates_and_discounts();
			extract($data11);

			$acct_type = array();
			$acctype1 = AccountMetas::where('meta_type', 'account_type')->get();

			foreach($acctype1 as $att)
			{
				 $acct_type[$att->id] = $att->toArray();
			}

			$data11['acct_type'] = $acct_type;




			$bill_info = ServiceCtrl::proccess_billing($bill1->reading1, $bill1->reading1->period, $data11, true);

			$new_bill = (@$bill_info[8] +  @$bill_info[10]) - @$bill_info[7];
			$prev_bill = $bill1->curr_bill;

			if(@$bill_info[8] == $prev_bill){
				//~ $request->session()->flash('success', 'Billing Reprocess Failed.');
				//~ return Redirect::to(URL::previous() . "#accounts");
				//~ exit();
			}

			$ttl_bal = $new_bill;


			$led1777 = LedgerData::where('acct_id', $bill1->reading1->account_id)
							->where('status','active')
								->orderBy('id', 'desc')
									->first();


			// $ledBill01 = LedgerData::where(function($query){
			// 									$query->where('led_type','billing');
			// 									$query->orWhere(function($q2){
			// 										$q2->where('led_type','adjustment');
			// 										$q2->where('ledger_info','like','%'.'senior'.'%');
			// 									});
			// 						  })
			// 							->where('acct_id', $bill1->reading1->account_id)
			// 							->where('reff_no', $bill1->id)
			// 							->where('status','active')
			// 								->get();
			//
			// echo '<pre>';
			// print_r($ledBill01->toArray());
			// die();

			if($led1777->led_type != 'billing'){
				$request->session()->flash('success', 'Billing Reprocess Failed ! there is already Payment, Adjustment or Penalty after billing.');
				return Redirect::to(URL::previous() . "#accounts");
			}




			ServiceCtrl::proccess_arrears($bill1->reading1, $bill1->reading1->period);
			ServiceCtrl::proccess_billing($bill1->reading1, $bill1->reading1->period, $data11);


			$str_date = date('F Y',strtotime($bill1->reading1->period));


			// $ledBill01 = LedgerData::where('led_type','billing')
			// 				->where('acct_id', $bill1->reading1->account_id)
			// 				->where('reff_no', $bill1->id)
			// 				->where('status','active')
			// 					->get();

			$ledBill01 = LedgerData::where(function($query){
												$query->where('led_type','billing');
												$query->orWhere(function($q2){
													$q2->where('led_type','adjustment');
													$q2->where('ledger_info','like','%'.'senior'.'%');
												});
									  })
										->where('acct_id', $bill1->reading1->account_id)
										->where('reff_no', $bill1->id)
										->where('status','active')
											->get();


			if(count($ledBill01) != 0)
			{
				foreach($ledBill01 as $lb1)
				{
					$lb1->status = 'rebill';
					$lb1->save();

					$new_ledger = new LedgerData;
					$new_ledger->ledger_info='Re Billing '.$str_date.' '.$lb1;


				}
			}


			$new_ledger = new LedgerData;
			$new_ledger->ledger_info='Re Billing '.$str_date;
			$new_ledger->led_type='billing';
			$new_ledger->status='active';
			$new_ledger->period = $bill1->reading1->period;

			$new_ledger->acct_id = $bill1->reading1->account_id;
			$new_ledger->acct_num = $bill1->reading1->account_number;
			$new_ledger->date01  = date('Y-m-d');
			$new_ledger->reff_no = $bill1->id;
			$new_ledger->reading = $bill_info[2];

			$new_ledger->consump = @$bill_info[3];
			$new_ledger->billing = @$bill_info[8];
			$new_ledger->arrear  = (float) @$bill_info[10];
			$new_ledger->ttl_bal = $ttl_bal;
			$new_ledger->discount =(float) @$bill_info[7];
			$new_ledger->save();



			$request->session()->flash('success', 'Billing Reprocess Done');
			return Redirect::to(URL::previous() . "#accounts");
			exit();

	}



	function BillingLogout()
	{
		Auth::logout();
		return redirect('/');
	}

	function StartPrintingService($req_id, Request $request)
	{

		$bill_request  =
				HwdRequests::where('req_type', 'generate_billing_period_request')
				->where('id', $req_id)
				->first();

		if(!$bill_request){
			$request->session()->flash('success', 'Failed to find billing request');
			return Redirect::to(URL::previous() . "#period_request");
		}

		$print_request =
				HwdRequests::where('req_type', 'billing_print_request')
				->where('reff_id', $bill_request->id)
				->where('status', 'active')
				->first();

		$new_dd = date('F Y',  strtotime($bill_request->dkey1));

		if($print_request){
			$request->session()->flash('success', $new_dd.' Printing service already exist.');
			return Redirect::to(URL::previous() . "#period_request");
		}

		$new_print_service = new HwdRequests;
		$new_print_service->reff_id = $bill_request->id;
		$new_print_service->remarks = 'Billing printing service '.$new_dd;
		$new_print_service->dkey1  = $bill_request->dkey1;
		$new_print_service->status  = 'started';
		$new_print_service->req_type  = 'billing_print_request';
		$new_print_service->save();

		$request->session()->flash('success', $new_dd.' Printing service is stated');
		return Redirect::to(URL::previous() . "#period_request");

	}//



	/******************/
	/******************/
	/******************/

	function CollectionMain(){

		//$billing_res = $this->__billingAccountsGetData(date('Y'), date('m'));
		$data1 = $this->__billingAccountExtraData();
		extract($data1);

		$r_year = date('Y');
		$r_month = date('m');

		$req_date  = date('Y-m', strtotime($r_year.'-'.($r_month)));
		$date_now = date('Y-m-d');

		$current_period= BillingMdl::where('period', 'like',  $req_date.'%')
					->leftJoin('collections', 'collections.billing_id', 'billing_mdls.id')
					->sum('collections.payment');

		$current_day = BillingMdl::where('collections.payment_date', 'like',  $date_now.'%')
					->leftJoin('collections', 'collections.billing_id', 'billing_mdls.id')
					->sum('collections.payment');

		$coll_total = BillingMdl::where('period', 'like', $req_date.'%')
								->where('status', 'active')
								->sum('billing_total');


		$dash_info = array();
		$dash_info['total_period_collection'] =  $current_period;
		$dash_info['today_collection'] =  $current_day;
		$dash_info['total_collectable'] =  $coll_total;

		$accts = $this->___collection_resu001(compact('r_year', 'r_month'));

		$banks = Bank::orderBY('bank_name')->get();


		return view('billings.collection', compact('zones', 'acct_type', 'dash_info', 'accts', 'r_year', 'r_month', 'banks'));

		echo '<pre>';
		print_r($accts);
	}// END  CollectionMain


	function CollectionSearchAccount($r_year, $r_month, $acct_num, $lname){

			//$r_year = date('Y');
			//$r_month = date('m');

			$accts = $this->___collection_resu001(compact('r_year', 'r_month', 'acct_num', 'lname'));
			return view('billings.inc.billing_collection.collection_inc.table1', compact('r_year', 'r_month', 'accts'));

			echo '<pre>';
			print_r($accts);
	}//END METHOD

	private function ___collection_resu001($var1){
			extract($var1);

			$request_period  = date('Y-m', strtotime($r_year.'-'.($r_month)));

			$status_key_active_raw  =  AccountMetas::where('meta_type', 'account_status')
									->where('old_id', '1')
									->first();

			if($status_key_active_raw){
				$status_key_active =
							$status_key_active_raw->id;
			}else{
				$status_key_active = 0;
			}

			$accts_x  =  Accounts::where('acct_status_key', $status_key_active);
			$accts_x->with(['reading1' => function($query) use($request_period){
										$query->where('readings.period', 'like',  $request_period.'%');
								}, 'reading1.billing.collection']);

			if(@$acct_num != null  && @$acct_num!='none'){
				$accts_x->where('acct_no','like', '%'.trim($acct_num).'');
			}

			if(@$lname != null   && @$lname!='none'){
				$accts_x->where('lname','like', trim($lname).'%');
			}

			$accts_raw	 =  $accts_x->paginate(20);
			$accts  =  $accts_raw->toArray();
			return  $accts;
	}//End funct






	/*RRRRRRRRRRRRRRR*/
	/*RRRRRRRRRRRRRRR*/
	/*RRRRRRRRRRRRRRR*/
	/*RRRRRRRRRRRRRRR*/

	function ReadingMain(){
		 $mm =  (int) date('m');
		 $yy =  (int) date('Y');
		return redirect('/billing/reading/'.$yy.'/'.$mm);
	}

	function ReadingWithDate($r_year, $r_month)
	{
		//~ ave_reading(2, '2019-05-01');
		//~ die();

		$acct_reading  = array();
		//~ $acct_reading = $this->__sql_accounts2($r_year, $r_month);

		$data1 = $this->__readingExtraData($r_year.'-'.$r_month);
		$read_period = ReadingPeriod::orderBy('period', 'desc')
						->with(['read1' => function($query){
							//~ $query->where('period');
						}])
						->limit(20)
						->get();

		extract($data1);


		$acct_all =  0;
		$acct_active = 0;
		$acct_new = 0;
		$acct_4dis = 0;
		$acct_dis = 0;
		$acct_4rec = 0;

		//~ $acct_all = Accounts::where('status', '!=', 'deleted');
		//~ $acct_new = Accounts::where('acct_status_key','1' )->orWhere('acct_status_key','6' )->where('status', '!=', 'deleted');
		//~ $acct_active = Accounts::where('acct_status_key','2' )->where('status', '!=', 'deleted');
		//~ $acct_4dis = Accounts::where('acct_status_key','3' )->where('status', '!=', 'deleted');
		//~ $acct_dis = Accounts::where('acct_status_key','4' )->where('status', '!=', 'deleted');
		//~ $acct_4rec = Accounts::where('acct_status_key','5' )->where('status', '!=', 'deleted');

		//~ $acct_all =  $acct_all->get()->count();
		//~ $acct_active = $acct_active->get()->count();
		//~ $acct_new = $acct_new->get()->count();
		//~ $acct_4dis = $acct_4dis->get()->count();
		//~ $acct_dis = $acct_dis->get()->count();
		//~ $acct_4rec = $acct_4rec->get()->count();



		$acct_stat11 = compact('acct_all', 'acct_new', 'acct_active', 'acct_4dis', 'acct_dis', 'acct_4rec');

		//~ $acct_init = Accounts::where('meter_number1')->get()->count();
		//~ echo '<pre>';
		//~ print_r($read_period->toArray());
		//~ die();

		$curr_period = date('Y-m', strtotime($r_year.'-'.$r_month.'-1'));
		$reading11 = Reading::where('period', 'like', $curr_period.'%')
							->whereHas('account1', function($query){
								$query->where('status', '!=', 'deleted');
							})
							->with('account1')
							->select('*')
							//~ ->orderBy('account1.route_id', 'asc')
							->paginate(200)
							->toArray();

		
		

		return view('billings.reading', compact(
			'acct_reading',
			'reading11',
			'r_year',
			'r_month',
			'zones',
			'officials',
			'read_count',
			'no_read_count',
			'reading_off',
			'read_period',
			'acct_all',
			'acct_stat11'
		));

		echo '<pre>';
		print_r($reading1->toArray());

		//~ print_r($read_period->toArray());
		//print_r($zones_lab);
	}

	function ReadingWithDateWithFilter($r_year, $r_month, $acct_num, $meter_num, $lname, $zone)
	{
		//~ die();
		//~ $acct_reading = $this->__sql_accounts2($r_year, $r_month,
		//~ compact('acct_num','meter_num','lname', 'zone'));

		$acct_reading = array();
		$data1 = $this->__readingExtraData($r_year.'-'.$r_month);
		$read_period = ReadingPeriod::orderBy('period', 'desc')->limit(20)->get();

		$curr_period = date('Y-m', strtotime($r_year.'-'.$r_month.'-1'));

		$reading11_raw =
			Reading::where('period', 'like', $curr_period.'%')
				->whereHas('account1', function($query)use($acct_num, $meter_num, $lname, $zone){
						if(!empty($acct_num)  && $acct_num!='none'){
							$query->where('acct_no', 'like',  '%'.$acct_num);
						}

						if(!empty($meter_num)  && $meter_num!='none'){
							$query->where('meter_number1', 'like',  '%'.$meter_num);
						}

						if(!empty($lname)  && $lname!='none'){
							$query->where('lname', 'like',  $lname.'%');
						}

						if(!empty($zone)  && $zone!='none'){
							$query->where('zone_id', $zone);
						}

						$query->where('status', '!=', 'deleted');
				})
				->with('account1')
				->join('accounts as acc1', 'acc1.id', '=', 'readings.account_id');


		if(@$_GET['status'] == 1){
			$reading11_raw->where(function($query){
				$query->whereNull('current_consump');
				$query->orWhere('current_consump', '');
				$query->orWhere('current_consump', 0);
				$query->orWhere('current_consump','<', 0);
			});
			$reading11_raw->where('curr_reading','>', 0);
		}


		if(@$_GET['status'] == 14){

			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_discount', SENIOR_ID);
			});
		}


		if(@$_GET['status'] == 2){

			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', '1');
				$query->orWhere('acct_status_key', '6');
			});
		}


		if(@$_GET['status'] == 3){
			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', 4);
			});
		}



		if(@$_GET['status'] == 4){
			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', 5);
			});

		}

		if(@$_GET['status'] == 5){
			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', 4);
			});

			$reading11_raw->where(function($query){
				$query->whereNotNull('curr_reading');
				$query->where('curr_reading','>', 0);
			});

		}

		//Active Accounts w/ zero consumption
		//Active Accounts w/ zero consumption
		if(@$_GET['status'] == 6){

			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', 2);
				$query->orWhere('acct_status_key', 3);

			});

			$reading11_raw->where(function($query){
				$query->whereNull('current_consump');
				$query->orWhere('current_consump','<=', 0);
			});

			$reading11_raw->where(function($query){
				$query->whereNotNull('curr_reading');
				$query->where('curr_reading','>', 0);
			});

		}

		//Read Accounts Active and Disconnected
		//Read Accounts Active and Disconnected
		if(@$_GET['status'] == 7){

			//~ $reading11_raw->whereHas('account1', function($query){
				//~ $query->where('acct_status_key', 2);
				//~ $query->orWhere('acct_status_key', 3);
			//~ });

			$reading11_raw->where(function($query){
				$query->whereNotNull('curr_reading');
				$query->where('curr_reading','>', 0);
			});

		}

		//~ Read Accounts Active Only( 23 )
		//~ Read Accounts Active Only( 23 )
		//~ Read Accounts Active Only( 23 )

		if(@$_GET['status'] == 8){

			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', 2);
				$query->orWhere('acct_status_key', 3);
			});


			$reading11_raw->where(function($query){
				$query->whereNotNull('curr_reading');
				$query->where('curr_reading','>', 0);
			});

		}//

		//~ Read Accounts Disconnected Only
		//~ Read Accounts Disconnected Only
		//~ Read Accounts Disconnected Only
		if(@$_GET['status'] == 9){

			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', 4);
			});


			$reading11_raw->where(function($query){
				$query->whereNotNull('curr_reading');
				$query->where('curr_reading','>', 0);
			});

		}//


		//~ Unread Active Accounts
		//~ Unread Active Accounts
		//~ Unread Active Accounts
		if(@$_GET['status'] == 10){

			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', 2);
				$query->orWhere('acct_status_key', 3);
			});

			$reading11_raw->where(function($query){
				$query->whereNull('curr_reading');
				$query->orWhere('curr_reading','<=', 0);
			});
		}//


		//~ Active Accounsts
		//~ Active Accounsts
		//~ Active Accounsts
		if(@$_GET['status'] == 11){
			$reading11_raw->whereHas('account1', function($query){
				$query->where('acct_status_key', 2);
				$query->orWhere('acct_status_key', 3);
			});
		}//


		//~ Abnormal Readings
		//~ Abnormal Readings
		//~ Abnormal Readings
		$select11 = '';

		if(@$_GET['status'] == 12){

			//~ die();

			//~ $reading11_raw->whereHas('account1', function($query){
				//~ $query->where('acct_status_key', 2);
				//~ $query->orWhere('acct_status_key', 3);
			//~ });

			//$select11.=" , (SELECT SUM(current_consump) FROM readings WHERE period IN ('2019-05-01') AND account_id=1800  ) as ee";

		}//


		//Billable Account
		//Billable Account
		//Billable Account
		if(@$_GET['status'] == 13){

			$reading11_raw->where(function($q1){
							$q1->where('current_consump', '!=', '');
							$q1->whereNotNull('curr_reading');
							//$q1->orWhere('curr_reading','!=', '0');
						});

			$reading11_raw->where('curr_reading', '!=', '0');
			$reading11_raw->where('current_consump', '>', '0');

			$reading11_raw->where('bill_stat', 'unbilled');

		}//



		if(@$_GET['status'] == 12)
		{
			$P1 = $curr_period.'-01';
			$P2 = date('Y-m-d', strtotime($P1.' - 1 Month'));
			$P3 = date('Y-m-d', strtotime($P1.' - 2 Month'));

			//~ echo $P1;
			//~ echo '<br />';
			//~ echo $P2;
			//~ echo '<br />';
			//~ echo $P3;
			//~ echo '<br />';
			//~ die();

			$reading11_raw->leftJoin(DB::raw("
					(
						SELECT * FROM (SELECT TAB2.*, (CONVERT(RR.current_consump, UNSIGNED) * 1) as YY FROM
						(SELECT account_id,account_number, AVG(MM) AS TT,  (AVG(MM) * 2) AS TT2 FROM
							(SELECT account_id, account_number,period, CONVERT(current_consump, UNSIGNED) AS MM FROM `readings`
						WHERE  period='$P2' OR   period='$P3') AS TAB1
						GROUP BY account_id) AS TAB2
						LEFT JOIN readings as RR ON (TAB2.account_id = RR.account_id  AND RR.period='$P1')
						WHERE RR.current_consump != 0) AS UU
						WHERE UU.YY > UU.TT2
					) AS TABXX
			"), 'TABXX.account_id','=','acc1.id');


			$reading11_raw->where('TABXX.TT2', '!=', null);
			$select11.= ',TABXX.TT,TABXX.TT2,TABXX.YY';
		}//



		//~ var_dump($select11);

		$reading11_raw->selectRaw(DB::raw('readings.* '.$select11))
				->orderBy('acc1.route_id', 'asc');
				//~ ->orderBy('acc1.id', 'asc');


		//~ $total_accounts = $reading11_raw->count();
		//~ echo $total_accounts;
		//~ die();


		if(@$_GET['pdf'] == 1){

			$reading11 = $reading11_raw
							->paginate(10000)
								->toArray();

			$pdf = PDF::loadView('billings.inc.billing_reading.reading_accounts_pdf',
					compact('reading11', 'r_year', 'r_month', 'zone', 'zone_label'));
			return $pdf->stream('account_list_result.pdf');
		}

		$reading11 = $reading11_raw
						->orderBy(DB::raw('ISNULL(route_id), route_id'), 'DESC')
						
						->paginate(200)
							->toArray();


		//~ $RES22	= DB::select("

				//~ SELECT * FROM (SELECT TAB2.*, (CONVERT(RR.current_consump, UNSIGNED) * 1) as YY FROM (SELECT account_id,account_number, AVG(MM) AS TT,  (AVG(MM) * 1.5) AS TT2 FROM (SELECT account_id, account_number,period, CONVERT(current_consump, UNSIGNED) AS MM FROM `readings`
				//~ WHERE  period='2019-06-01' OR   period='2019-05-01') AS TAB1
				//~ GROUP BY account_id) AS TAB2
				//~ LEFT JOIN readings as RR ON (TAB2.account_id = RR.account_id  AND RR.period='2019-07-01')
				//~ WHERE RR.current_consump != 0) AS UU
				//~ WHERE UU.YY > UU.TT2
				//~ LIMIT 10
			//~ ");




		//~ echo '<pre>';
		//~ print_r($reading11);
		//~ die();


		extract($data1);
		//~ $acct_all = Accounts::whereNotNull('meter_number1')->get()->count();



		$acct_all = Accounts::where('status', '!=', 'deleted');
		$acct_new = Accounts::
							where(function($query){
								$query->where('acct_status_key','1' );
								$query->orWhere('acct_status_key','6' );
							})
							->where('status', '!=', 'deleted');

		$acct_active = Accounts::where('acct_status_key','2' )->where('status', '!=', 'deleted');
		$acct_4dis = Accounts::where('acct_status_key','3' )->where('status', '!=', 'deleted');
		$acct_dis = Accounts::where('acct_status_key','4' )->where('status', '!=', 'deleted');
		$acct_4rec = Accounts::where('acct_status_key','5' )->where('status', '!=', 'deleted');

		$acct_dis_read = Accounts::where('acct_status_key','4' )
							->where('status', '!=', 'deleted')
							->whereHas('reading1', function($query)use($curr_period){
								$query->where('period', 'like', $curr_period.'%');
								$query->whereNotNull('curr_reading');
								$query->where('curr_reading','>=', 0);
							});


		//Active Accounts w/ zero consumption
		//Active Accounts w/ zero consumption
		$acct_active_zero =
				Accounts::
					where(function($query){
						$query->where('acct_status_key','2' );
						$query->orWhere('acct_status_key','3' );
					})
					->whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
						$query->whereNotNull('curr_reading');
						$query->where('curr_reading','>=', 0);
						$query->where('current_consump','<=', 0);

					})
					->where('status', '!=', 'deleted');


		//Read Accounts
		$acct_read =
				Accounts::
					whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
						$query->whereNotNull('curr_reading');
						$query->where('curr_reading','>', 0);
					})
					->where('status', '!=', 'deleted');

		//~ Read Accounts Active Only( 23 )
		$acct_read_active =
				Accounts::
					whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
						$query->whereNotNull('curr_reading');
						$query->where('curr_reading','>', 0);
					})
					->where(function($query){
						$query->where('acct_status_key','2' );
						$query->orWhere('acct_status_key','3' );
					})
					->where('status', '!=', 'deleted');

		//~ Read Accounts Disconnected Only
		$acct_read_disconnected =
				Accounts::
					whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
						$query->whereNotNull('curr_reading');
						$query->where('curr_reading','>', 0);
					})
					->where(function($query){
						$query->where('acct_status_key',4);
					})
					->where('status', '!=', 'deleted');


		//~ Unread Active Account Accounts
		$acct_unread_active =
				Accounts::
					whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
						$query->where(function($q2){
							$q2->whereNull('curr_reading');
							$q2->orWhere('curr_reading','<=', 0);
						});
					})
					->where(function($query){
						$query->where('acct_status_key','2' );
						$query->orWhere('acct_status_key','3' );
					})
					->where('status', '!=', 'deleted');


		//~ Unread Active Account Accounts
		$acct_active22 =
				Accounts::
					whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
					})
					->where(function($query){
						$query->where('acct_status_key','2' );
						$query->orWhere('acct_status_key','3' );
					})
					->where('status', '!=', 'deleted');



		//Billable Readings
		$billable_reading =
				Accounts::
					whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
						$query->where(function($q1){
							$q1->where('current_consump', '!=', '');
							$q1->whereNotNull('curr_reading');
							$q1->orWhere('curr_reading', 0);
						});
						$query->where('bill_stat', 'unbilled');
					})
					->where('status', '!=', 'deleted');

		$billed_accounts =
				Accounts::
					whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
						$query->where(function($q1){
							$q1->where('current_consump', '!=', '');
							$q1->where('curr_reading','!=', 0);
						});
						$query->where('bill_stat', 'billed');
					})
					->where('status', '!=', 'deleted');

		$unbilled_accounts =
				Accounts::
					whereHas('reading1', function($query)use($curr_period){
						$query->where('period', 'like', $curr_period.'%');
						$query->where('bill_stat', 'unbilled');
						$query->where(function($q1){
							//$q1->where('current_consump', '!=', '');
							//$q1->where('curr_reading','!=', 0);
						});
					})
					->where(function($query){
						$query->where('acct_status_key','2' );
						$query->orWhere('acct_status_key','3' );
					})
					->where('status', '!=', 'deleted');


		//~ $billable_reading->orderBy('id', 'asc');
		//~ $ddd = $billable_reading->get()->toArray();
		//~ echo '<pre>';
		//~ print_r($ddd);
		//~ die();


		if(!empty($zone)  && $zone!='none')
		{
			$acct_all->where('zone_id', $zone);
			$acct_active->where('zone_id', $zone);
			$acct_new->where('zone_id', $zone);

			$acct_4dis->where('zone_id', $zone);
			$acct_dis->where('zone_id', $zone);

			$acct_4rec->where('zone_id', $zone);
			$acct_dis_read->where('zone_id', $zone);
			$acct_read->where('zone_id', $zone);

			$acct_read_active->where('zone_id', $zone);
			$acct_read_disconnected->where('zone_id', $zone);

			$acct_unread_active->where('zone_id', $zone);
			$acct_active22->where('zone_id', $zone);

			$billable_reading->where('zone_id', $zone);
			$billed_accounts->where('zone_id', $zone);
			$unbilled_accounts->where('zone_id', $zone);

		}
		
		
		//~ if(@$_GET['status'] == 14)
		//~ {
		//~ }//		

		//~ $nn = $acct_new->get()->toArray();
		//~ echo '<pre>';
		//~ print_r($nn);
		//~ die();

		//~ $acct_all = $acct_all->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_active = $acct_active->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_new = $acct_new->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_4dis = $acct_4dis->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_dis = $acct_dis->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_4rec = $acct_4rec->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_dis_read = $acct_dis_read->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_active_zero = $acct_active_zero->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_read = $acct_read->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_read_active = $acct_read_active->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_read_disconnected = $acct_read_disconnected->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_unread_active = $acct_unread_active->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $acct_active22 = $acct_active22->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $billable_reading = $billable_reading->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $billed_accounts = $billed_accounts->selectRaw('COUNT(id) as mm')->first()->mm;
		//~ $unbilled_accounts = $unbilled_accounts->selectRaw('COUNT(id) as mm')->first()->mm;


		//~ echo '<pre>';
		//~ print_r($acct_all);
		//~ die();



		$acct_all =  0;
		$acct_active = 0;
		$acct_new = 0;
		$acct_4dis = 0;
		$acct_dis =  0;
		$acct_4rec =  0;
		$acct_dis_read =  0;
		$acct_active_zero =  0;
		$acct_read =  0;
		$acct_read_active =  0;
		$acct_read_disconnected =  0;
		$acct_unread_active =  0;
		$acct_active22 =  0;
		$billable_reading =  0;
		$billed_accounts =  0;
		$unbilled_accounts =  0;


		//~ $acct_all =  $acct_all->get()->count();
		//~ $acct_active = $acct_active->get()->count();
		//~ $acct_new = $acct_new->get()->count();
		//~ $acct_4dis= $acct_4dis->get()->count();
		//~ $acct_dis = $acct_dis->get()->count();
		//~ $acct_4rec = $acct_4rec->get()->count();
		//~ $acct_dis_read = $acct_dis_read->get()->count();
		//~ $acct_active_zero = $acct_active_zero->get()->count();
		//~ $acct_read = $acct_read->get()->count();

		//~ $acct_read_active = $acct_read_active->get()->count();
		//~ $acct_read_disconnected = $acct_read_disconnected->get()->count();

		//~ $acct_unread_active = $acct_unread_active->get()->count();

		//~ $acct_active22 = $acct_active22->get()->count();

		//~ $billable_reading = $billable_reading->get()->count();
		//~ $billed_accounts = $billed_accounts->get()->count();
		//~ $unbilled_accounts = $unbilled_accounts->get()->count();


		//~ echo $acct_unread_active;
		//~ die();


		$acct_stat11 = compact('acct_all', 'acct_new', 'acct_active', 'acct_4dis',
								'acct_dis', 'acct_4rec', 'acct_dis_read',
								'acct_read','acct_read_active','acct_read_disconnected',
								'acct_unread_active','acct_active22',
								'billable_reading','billed_accounts','unbilled_accounts',
								'acct_active_zero');

		//~ echo '<pre>';
		//~ print_r($reading11);
		//~ die();

		// ee($reading11, __FILE__, __LINE__);

		return view('billings.reading', compact(
			'acct_reading',
			'reading11',
			'r_year',
			'r_month',
			'acct_num',
			'meter_num',
			'lname',
			'zone',
			'zones',
			'officials',
			'read_count',
			'no_read_count',
			'reading_off',
			'read_period',
			'acct_all',
			'acct_stat11'

		));
	}



	function OverDueAddJob($zone_id, $date1, Request $request)
	{
			$dd1 = date('Y-m', strtotime($date1));
			$date1 = date('Y-m-d', strtotime($date1));

			$get_overstat = OverdueStat::where('zone_id', $zone_id)
			//~ ->where('date1',  $date1)
			->where('period',  $dd1.'-01')
			->first();

			if($get_overstat){
				$request->session()->flash('success', 'Overdue schedule is already exist');
				return Redirect::to(URL::previous() . '#overdue');
			}

			$new_over_stat = new OverdueStat;
			$new_over_stat->zone_id = $zone_id;
			$new_over_stat->date1 = $date1;
			$new_over_stat->period = $dd1.'-01';
			$new_over_stat->save();

			$request->session()->flash('success', 'Successfully added the overdue schedule');
			return Redirect::to(URL::previous() . '#overdue');

			echo '<pre>';
			print_r($get_overstat);

	}

	function OverDueProccessJob($job_id, Request $request)
	{
		$get_overstat = OverdueStat::find($job_id);

		if(!$get_overstat){
				$request->session()->flash('success', 'Overdue schedule not found');
				return Redirect::to(URL::previous() . '#overdue');
		}
		$get_overstat->status = 1;
		$get_overstat->save();

		$request->session()->flash('success', 'Overdue schedule is started');
		return Redirect::to(URL::previous() . '#overdue');

	}

	function OverDueProccessJobRestart($job_id, Request $request)
	{
		$get_overstat = OverdueStat::find($job_id);
		if(!$get_overstat){
				$request->session()->flash('success', 'Overdue schedule not found');
				return Redirect::to(URL::previous() . '#overdue');
		}

		$duedate = $get_overstat->date1;
		$period = $get_overstat->period;
		$zone_id = $get_overstat->zone_id;


		$all_billing = BillingMdl::whereHas('account2', function($query)use($zone_id){
							$query->where('zone_id', $zone_id);
						})
						->where('period', $period)
						->update(['due_stat'=> null]);

		$get_overstat->status = 1;
		$get_overstat->save();

		$request->session()->flash('success', 'Overdue schedule is restarted');
		return Redirect::to(URL::previous() . '#overdue');
	}






	function ReadingAddMeterNumber(Request $request)
	{
		extract($_POST);//meter_number  acct_id
		
		// echo '<pre>';
		// print_r($_POST);
		// die();

		$tab1 = '#accounts';
		if(@$_GET['vr'] == 2)
		{
			$tab1='#account_list';
		}

		$mtr_num_stat = ' change ';
		$acct1  =  Accounts::where('id',$acct_id)
							->with('last_gps_data')
								->first();
			

		$mtr_num_stat.= '<br />From '.@$acct1->meter_number1.' to '.@$meter_number;

		if(empty($acct1->meter_number1)){
			$mtr_num_stat = ' added ';
		}

		//HwdLedgerCtrl::ReadingProcess($acct_id, 'meter_number_updated');
		$info001 = array(
			$meter_number, //0
			$mtr_num_stat, //0
		);
		
		if(!empty(@$meter_number))
		{
			if(@$meter_number != @$acct1->meter_number1)
			{
				$acct1->meter_number1 = $meter_number;
				$acct1->save();
				HwdLedgerCtrl::MeterNumberChange($acct_id, $info001);
			}
		}

		
		if(@$acct_number != @$acct1->acct_no)
		{
			$acct_num_stat = ' change ';
			$acct_num_stat.= '<br />From '.@$acct1->acct_no.' to '.@$acct_number;
			
			$info001 = array(
				$acct_number, //0
				$acct_num_stat, //0
			);
			HwdLedgerCtrl::AccountNumberChange($acct_id, $info001);

			$acct1->acct_no = $acct_number;
			$acct1->save();
		}
		
		if(@$acct1->last_gps_data){

			if(
				@$long1 != @$acct1->last_gps_data->lng1
				||
				@$lat1  != @$acct1->last_gps_data->lat1
			)
			{
				$acct1->last_gps_data->lng1 = @$long1;
				$acct1->last_gps_data->lat1 = @$lat1;
				$acct1->last_gps_data->save();
			}//
		}else{
			
			$gps1 = new GpsAcct;
			$gps1->acct_id = @$acct1->id;
			$gps1->mtr_n   = @$meter_number;
			$gps1->lat1    = @$lat1;
			$gps1->lng1    = @$long1;
			$gps1->stat    = 'active';
			$gps1->save();
		}

		//REMARKS
		$acct1->mtr_rem = trim($meter_remarks);
		$acct1->save();


		$request->session()->flash('success', 'Meter number updated.');
		return Redirect::to(URL::previous() . $tab1);
	}


	private function __sql_accounts2($r_year, $r_month,  $data1=array())
	{

			extract($data1);

			$curr_date =  date('Y-m', strtotime($r_year.'-'.$r_month));
			$prev_date =  date('Y-m', strtotime($curr_date.'  -1 month '));

			$status_key_active_raw  =  AccountMetas::where('meta_type', 'account_status')
										->where('meta_code', '1')
										->first();

			if($status_key_active_raw){
				$status_key_active = $status_key_active_raw->id;
			}else{
				$status_key_active = 0;
			}

			$min_period = strtotime('2018-10-1');
			$curr_period = strtotime($r_year.'-'.$r_month.'-1');

			if($min_period > $curr_period){
				return array();
			}

			//~ echo $min_period;
			//~ echo '<br />';
			//~ echo $curr_period;
			//~ die();

			$acct1 = Accounts::where('accounts.acct_status_key', $status_key_active);
					//~ ->whereHas('reading1', function($query) use ($curr_date){
						//~ $query->where('period', 'like', $curr_date.'%');
					//~ })
					//~ ->with(['reading1' => function($query) use ($curr_date){
								//~ $query->where('period', 'like', $curr_date.'%');
						//~ }]);


			$acct1_arr = array();
			$acct1_arr['total'] = $acct1->count();
			$acct1_arr = $acct1->paginate(20)->toArray();

			//~ $acct1_arr = $acct1->limit(20)->get()->toArray();
			//~ echo '<pre>';
			//~ print_r($acct1_arr);
			//~ die();


			return  $acct1_arr;


		return array();
	}

	private function __sql_accounts2_xxx($r_year, $r_month,  $data1=array())
	{
			extract($data1);

			$curr_date =  date('Y-m', strtotime($r_year.'-'.$r_month));
			$prev_date =  date('Y-m', strtotime($curr_date.'  -1 month '));

			$status_key_active_raw  =  AccountMetas::where('meta_type', 'account_status')
										->where('meta_code', '1')
										->first();

			if($status_key_active_raw){
				$status_key_active = $status_key_active_raw->id;
			}else{
				$status_key_active = 0;
			}

			//~ echo $status_key_active;
			//~ die();

			$min_period = strtotime('2018-10-1');
			$curr_period = strtotime($r_year.'-'.$r_month.'-1');

			if($min_period > $curr_period){
				return array();
			}

			//~ echo $min_period;
			//~ echo '<br />';
			//~ echo $curr_period;
			//~ die();


			$acct1 = Accounts::where('accounts.acct_status_key', $status_key_active)
					->whereHas('reading1', function($query) use ($curr_date){
						$query->where('period', 'like', $curr_date.'%');
					})
					->with(['reading1' => function($query) use ($curr_date){
								$query->where('period', 'like', $curr_date.'%');
						}]);
					//~ ->with(['reading_prev' => function($query) use($prev_date){
								//~ $query->where('period', 'like', $prev_date.'%');
						//~ }]);

			if(!empty($acct_num)  && $acct_num!='none'){
				$acct1->where('acct_no', 'like',  '%'.$acct_num);
			}

			if(!empty($meter_num)  && $meter_num!='none'){
				$acct1->where('meter_number1', 'like',  '%'.$meter_num);
			}

			if(!empty($lname)  && $lname!='none'){
				$acct1->where('lname', 'like',  $lname.'%');
			}

			if(!empty($zone)  && $zone!='none'){
				$acct1->where('zone_id', $zone);
			}

			//~ echo 'AAA';
			//~ echo $acct1->count();
			//~ die();

			$acct1_arr = array();
			$acct1_arr['total'] = $acct1->count();

			$acct1_arr = $acct1->paginate(20)->toArray();

			// $acct1->limit(10)->offset(9);
			// $dd1  = $acct1->get();
			// $acct1_arr['data'] = $dd1->toArray();

			//~ echo $curr_date;
			 //~ echo '<pre>';
			//~ print_r($acct1_arr);
			//~ die();


			return  $acct1_arr;
	}//End Private

	private function  __readingExtraData($date1=''){

			$status_key_active_raw  =
					AccountMetas::where('meta_type', 'account_status')
						->where('old_id', '1')
						->first();

		$zones  = Zones::where('status', '!=', 'deleted')
						->orderBy('zone_name', 'asc')
						->get()
						->toArray();

		$officials =
						HwdOfficials::where('stat', '!=', 'deleted')
							->where('typ1','meter_officer')
							->orderBy('lname', 'asc')
							->get()
							->toArray();

		$date1 = date('Y-m', strtotime($date1));

		$read_count = Reading::where('period','like', $date1.'%')
								->where('curr_reading', '!=', '')
								->where('status', 'active')
								->count();

		$no_read_count = Accounts::whereDoesntHave('reading1')
									->where('acct_status_key', $status_key_active_raw->id)
									->count();

		$reading_off = Role::with('users')
									->with('users.hwdOfficer')
									->where('name', 'reading_officer')
									->get()
									->toArray();

		$reading_off = $reading_off[0]['users'];

		$zones_lab = array();
		foreach($zones as $att){
			$zones_lab[$att['id']] = $att['zone_name'];
		}

		foreach($reading_off as $kk => $vv){
			$curr_zones = array_filter(explode('|', @$vv['hwd_officer']['zones']));
			$zones_str = array();
			foreach($curr_zones as $cc1){
					$zones_str[]  = @$zones_lab[$cc1];
			}
			$vv['zones_txt'] = implode(', ', $zones_str);
			$reading_off[$kk] =  $vv;
		}

		return compact('zones', 'officials', 'read_count', 'no_read_count', 'reading_off', 'zones_lab');

		echo '<pre>';
		print_r($reading_off);
		print_r($zones_lab);
		die();
	}//End Private


	function ReadingUpdateCurrentReading(Request $request){
		extract($_POST);

		//~ echo '<pre>';
		//~ print_r($_POST);
		//~ return;
		//~ return;
		//~ return;

			$period =  date('Y-m',strtotime($reading_year.'-'.$reading_month));
			$period_now = date('Y-m');
			$prev_period = date('Y-m', strtotime($period.' -1 month'));

			//TEST PLEASE UNCOMMENT BELLOW
			//~ if($period_now != $period){return array('stat'=>'failed');}


			$reading1 = Reading::where('period', 'like', $period.'%')
							->where('id', (int) $acct_info['id'])
							->where('account_number', $acct_info['account_number'])
							->first();

			 $prev_read  = (int) $reading1->prev_reading;
			 if($prev_read == 0){
				 return array('stat'=>'failed');
			}

		if($reading1)
		{

				//$part_name = 'Updated/Changed <br />';
				$part_name = 'Reading correction <br />';

				if(empty( $curr_reading_str = trim($current_read))){
					$curr_reading_str = 'Empty';
				}
				$part_name.= 'From '.@$reading1->curr_reading.' To '.trim($curr_reading_str).'<br />';
				if(empty($reading1->curr_reading))
				{
					$part_name = 'Added '.trim($curr_reading_str).' <br />';
				}

				$reading1->curr_reading = trim($current_read);
				$reading1->meter_number = trim($acct_info['meter_number']);
				$reading1->bill_stat = 'unbilled';
				$reading1->curr_read_date = date('Y-m-d');
				$reading1->current_consump = (int) $reading1->curr_reading -  (int)$reading1->prev_reading;
				$reading1->save();

				if(!empty($reading1->init_reading))
				{
					$reading1->prev_reading  = $reading1->init_reading;
				}

				/*
				$new_billing = BillingMdl::where('period', 'like', $period.'%')
								->where('reading_id', $reading1->id)->first();
				if(!$new_billing){
					$new_billing = new BillingMdl;
					$new_billing->period = $period.'-28';
					$new_billing->reading_id = $reading1->id;
					$new_billing->rate_id	 = 1;
					$new_billing->status	 = 'active';
					$new_billing->bill_date	 = date('Y-m-d');
					$new_billing->prep_by	 = 1;
					$new_billing->save();
				}
				*/

				HwdLedgerCtrl::ReadingProcess($reading1->account_id, array(
					date('F Y', strtotime($period)),
					$reading1->meter_number,
					$reading1->prev_reading,
					$reading1->curr_reading,
					((int)$reading1->curr_reading - (int)$reading1->prev_reading),
					$part_name
				));

				return array('stat'=>'updated');

		}
		else
		{
			$reading1 = new Reading;
			$reading1->zone_id = $acct_info['zone_id'];
			$reading1->account_id = $acct_info['id'];
			$reading1->account_number = $acct_info['acct_no'];
			$reading1->meter_number = $acct_info['meter_number1'];
			$reading1->period = $period.'-28';
			$reading1->curr_reading = trim($current_read);
			$reading1->status = 'active';
			$reading1->save();

			$prev_reading = Reading::where('period', 'like', $prev_period.'%')
							->where('account_id', $acct_info['id'])
							->first();

			if($prev_reading){
				$reading1->prev_reading = $prev_reading->curr_reading;
				$reading1->save();
			}


			// HwdLedgerCtrl::ReadingProcess($reading1->account_id, 'reading_updated');
			HwdLedgerCtrl::ReadingProcess($reading1->account_id, array(
				date('F Y', strtotime($period)),
				$reading1->meter_number,
				$reading1->prev_reading,
				$reading1->curr_reading,
				((int)$reading1->curr_reading - (int)$reading1->prev_reading),
			));

			/*
			$new_billing = new BillingMdl;
			$new_billing->period = $period.'-28';
			$new_billing->reading_id = $reading1->id;
			$new_billing->rate_id	 = 1;
			$new_billing->status	 = 'active';
			$new_billing->bill_date	 = date('Y-m-d');
			$new_billing->prep_by	 = 1;
			$new_billing->save();
			*/

			return array('stat'=>'added');
		}

		echo '<pre>';
		print_r($_POST);
	}


	function update_previous_reading()
	{

		extract($_POST);

		//~ echo '<pre>';
		//~ print_r($_POST);
		//~ die();


		$period =  date('Y-m',strtotime($reading_year.'-'.$reading_month));
		$period_now = date('Y-m');
		$prev_period = date('Y-m', strtotime($period.' -1 month'));

		//~ TEST PLEASE UNCOMMENT BELOW
		//~ if($period_now != $period){return array('stat'=>'failed');}


		$reading1 = Reading::where('period', 'like', $period.'%')
						->where('id', (int) $acct_info['id'])
						->where('account_number', $acct_info['account_number'])
						->first();



		if($reading1)
		{

				$part_name = 'Previous reading correction <br />';

				if(empty( $curr_reading_str = trim($current_read))){
					$curr_reading_str = 'Empty';
				}
				$part_name.= 'From '.@$reading1->curr_reading.' To '.trim($curr_reading_str).'<br />';
				if(empty($reading1->curr_reading))
				{
					$part_name = 'Added '.trim($curr_reading_str).' <br />';
				}

				$reading1->prev_reading = trim($previous_reading);
				$reading1->init_reading = trim($previous_reading);

				$reading1->meter_number = trim($acct_info['meter_number']);
				$reading1->bill_stat = 'unbilled';
				$reading1->curr_read_date = date('Y-m-d');
				$reading1->current_consump = (int) $reading1->curr_reading -  (int)$reading1->prev_reading;
				$reading1->save();


				//~ HwdLedgerCtrl::ReadingProcess($reading1->account_id, array(
					//~ date('F Y', strtotime($period)),
					//~ $reading1->meter_number,
					//~ $reading1->prev_reading,
					//~ $reading1->curr_reading,
					//~ ((int)$reading1->curr_reading - (int)$reading1->prev_reading),
					//~ $part_name
				//~ ));

				return array('stat'=>'updated');

		}
		else
		{
			$reading1 = new Reading;
			$reading1->zone_id = $acct_info['zone_id'];
			$reading1->account_id = $acct_info['id'];
			$reading1->account_number = $acct_info['acct_no'];
			$reading1->meter_number = $acct_info['meter_number1'];
			$reading1->period = $period.'-01';

			$reading1->prev_reading = trim($previous_reading);
			$reading1->init_reading = trim($previous_reading);

			$reading1->status = 'active';
			$reading1->save();

			$prev_reading = Reading::where('period', 'like', $prev_period.'%')
							->where('account_id', $acct_info['id'])
							->first();

			if($prev_reading){
				$reading1->prev_reading = $prev_reading->curr_reading;
				$reading1->save();
			}


			//~ HwdLedgerCtrl::ReadingProcess($reading1->account_id, array(
				//~ date('F Y', strtotime($period)),
				//~ $reading1->meter_number,
				//~ $reading1->prev_reading,
				//~ $reading1->curr_reading,
				//~ ((int)$reading1->curr_reading - (int)$reading1->prev_reading),
			//~ ));


			return array('stat'=>'added');
		}

		echo '<pre>';
		print_r($_POST);

	}







	//HwdOfficials
	function ReadingNewMeterOfficer(Request $request){
		die();
		die();
		die();
		die();
		extract($_POST);

		$new_off = new HwdOfficials;
		$new_off->fname = $fname;
		$new_off->lname = $lname;
		$new_off->mi = $mi;
		$new_off->address1 = $address1;
		$new_off->zones = '';
		if(!empty($zone_ass)){$new_off->zones = '|'.implode('|',$zone_ass).'|';}
		$new_off->stat = $status;
		$new_off->typ1 = 'meter_officer';
		$new_off->save();

		$request->session()->flash('success', 'Meter Officer Added');
		return Redirect::to(URL::previous() . "#meterofficer");
	}

	function ReadingUpdateMeterOfficer(Request $request)
	{
		extract($_POST);

		$new_off = HwdOfficials::where('uid', $uid)->first();

		if($new_off){
			$new_off->address1 = $address1;
			$new_off->zones = '';
			if(!empty($zone_ass)){$new_off->zones = '|'.implode('|',$zone_ass).'|';}
			$new_off->stat = $status;
			$new_off->save();
		}else{
			$new_off = new HwdOfficials;
			$new_off->address1 = $address1;
			$new_off->typ1 = 'reading';
			$new_off->zones = '';
			$new_off->uid = $uid;
			if(!empty($zone_ass)){$new_off->zones = '|'.implode('|',$zone_ass).'|';}
			$new_off->stat = $status;
			$new_off->save();
		}

		$request->session()->flash('success', 'Meter Officer Updated');
		return Redirect::to(URL::previous() . "#meterofficer");

	}

	function ReadingUpdateInitReading(Request $request){

		//~ return;
		//~ return;
		//~ return;


		extract($_POST);

		if(@$_GET['vr'] == 2)
		{
			$data1 = (array)json_decode($data1);

			$init_reading = $init_reading_txt;
			// echo '<pre>';
			// print_r($_POST);
			// print_r($reading_year);
			// print_r($reading_month);
			// print_r($data1);
			// die();

		}

		$period =  date('Y-m',strtotime($reading_year.'-'.$reading_month));
		$period_now = date('Y-m');
		$prev_period = date('Y-m', strtotime($period.' -1 month'));

		if($period_now != $period){
			return array('stat'=>'failed', 'msg' => 'Period problem');
		}


		$reading1 = Reading::where('period', 'like', $period.'%')
					->where('account_id', $data1['id'])
					->where('account_number', $data1['acct_no'])
					->first();

		// echo '<pre>';
		// print_r($reading1->toArray);
		// die();

		if($reading1){

			if(empty(@$data1['meter_number1'])){
				return array('stat'=>'failed', 'msg' => 'Meter number is required');
			}

			$reading1->prev_reading = trim($init_reading);
			$reading1->init_reading = trim($init_reading);
			$reading1->bill_stat = 'unbilled';
			$reading1->save();

		}else{

			if(empty($data1['meter_number1'])){
				return array('stat'=>'failed', 'msg' => 'Meter number is required');
			}


			$reading1 = new Reading;
			$reading1->zone_id = $data1['zone_id'];
			$reading1->account_id = $data1['id'];
			$reading1->account_number = $data1['acct_no'];
			$reading1->meter_number = $data1['meter_number1'];
			$reading1->period = $period.'-28';
			//$reading1->curr_reading = trim($current_read);
			//$reading1->prev_reading = $acct_info['prev_reading'];
			$reading1->prev_reading = trim($init_reading);
			$reading1->init_reading = trim($init_reading);
			$reading1->curr_read_date = date('Y-m-d');
			$reading1->status = 'active';
			$reading1->save();
		}

		//HwdLedgerCtrl::initialReading($reading1->account_id);
		HwdLedgerCtrl::initialReading($reading1->account_id, array('init'=>trim($init_reading), 'mtr'=>$data1['meter_number1']));

		if(@$_GET['vr'] == 2)
		{
			$tab1 = '#accounts';
			$request->session()->flash('success', 'Initial Reading Updated.');
			return Redirect::to(URL::previous() . $tab1);
		}


		return array('stat'=>'added');

	}//End



	function ReportsMain(Request $request)
	{

		$T1 = strtotime('TODAY');
		$T2 = strtotime(date('Y-m-28').' -1 Month ');//30days
		$T3 = strtotime(date('Y-m-28').' -2 Month ');//60Days
		$T4 = strtotime(date('Y-m-28').' -3 Month ');//90Days
		$T5 = strtotime(date('Y-m-28').' -6 Month ');//180Days

		//echo date('Y-m-d', $T5);
		//die();

		$zone1 =  Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')->get();

		$period_set = array();
		$period_set[] = $DD1 = date('Y-m-28', $T1);
		$period_set[] = $DD2 = date('Y-m-28', $T2);
		$period_set[] = $DD3 = date('Y-m-28', $T3);
		//$period_set[] = $DD3 = date('Y-m-28', $T4);
		//$period_set[] = $DD3 = date('Y-m-28', $T5);

		$ppage = 3000;
		$page_info1 = pagi1($request, $ppage);
		$acct_result = array();

		$report_list = HwdRequests::where('req_type', 'report_generate_request')
				->paginate(20);

		// echo '<pre>';
		// print_r($report_list->toArray());
		// die();

		 return view('billings.reports', compact(
			'acct_result',
		 	'page_info1',
			'period_set',
			'zone1',
			'report_list'
		));

		//
		// die();
		// die();
	}//



	function ReportsAccountRecievableSummary($zone, $display_month, $full_date, $display_type='')
	{
		$T1 = strtotime($full_date);
		$curr_period = date('Y-m-28', $T1);
		$curr_date_label  =  date('l, F d, Y', $T1);

		$data1 = ageing_common(array('mm_arr', 'lab_days', 'zone_arr'));
		extract($data1);
		$zone_lbl = $zone_arr[$zone];

		$i = array_search($display_month, array_keys($mm_arr));
		$new_arr = array_slice($mm_arr, 0, ($i+1));
		$ageing_prev = array_slice($mm_arr,($i+1));

		if(empty($new_arr))
		{
			return;
		}

		$period_set = array();
		foreach($new_arr as  $mm)
		{
			$T2 = strtotime($curr_period.' '.$mm);
			$period_set[] = date('Y-m-28', $T2);
		}

		/**/
		/*Start*/
		/**/

		$arr_rars = array();
		$arr_rars_discon = array();

		$arr_rars[] = RARSummary_get_data1($curr_period, $zone);

		$arr_rars_discon[] = RARSummary_get_data1($curr_period, $zone, 4);


		foreach ($period_set as $kk => $vv)
		{
			$arr_rars[] = RARSummary_get_data1($vv,$zone);
			$arr_rars_discon[] = RARSummary_get_data1($vv,$zone,4);
		}


		 //~ echo '<pre>';
		 //~ print_r($arr_rars);
		 //~ die();


		$ff_data = array();

		foreach($arr_rars as $dd1)
		{
			foreach($dd1 as $dd2)
			{
				$mm = $dd2['acct_type_id'];
				$mm = 'type'.str_replace(' ', '', $mm);
				$arr_re = $mm.date('Ymd',strtotime($dd2['period']));
				$ff_data[$arr_re]['bill'] =  $dd2['total_by_type_bill'];
				$ff_data[$arr_re]['coll'] =  $dd2['total_by_type_coll'];
			}
		}

		$more_than1 = RARSummary_get_more_thann_data1($vv);

		foreach($more_than1 as $mt1)
		{
			$mm = $mt1['acct_type_id'];
			$mm = 'type_m_'.str_replace(' ', '', $mm);
			$arr_re = $mm;
			$ff_data[$arr_re]['bill'] =  $mt1['total_by_type_bill'];
			$ff_data[$arr_re]['coll'] =  $mt1['total_by_type_coll'];
		}

		///
		///

		$ff_data_discon = array();

		foreach($arr_rars_discon as $dd1)
		{
			foreach($dd1 as $dd2)
			{
				$mm = $dd2['acct_type_id'];
				$mm = 'type'.str_replace(' ', '', $mm);
				$arr_re = $mm.date('Ymd',strtotime($dd2['period']));
				$ff_data_discon[$arr_re]['bill'] =  $dd2['total_by_type_bill'];
				$ff_data_discon[$arr_re]['coll'] =  $dd2['total_by_type_coll'];
			}
		}

		$more_than_discon = RARSummary_get_more_thann_data1($vv, 4);

		foreach($more_than_discon as $mt1)
		{
			$mm = $mt1['acct_type_id'];
			$mm = 'type_m_'.str_replace(' ', '', $mm);
			$arr_re = $mm;
			$ff_data_discon[$arr_re]['bill'] =  $mt1['total_by_type_bill'];
			$ff_data_discon[$arr_re]['coll'] =  $mt1['total_by_type_coll'];
		}


		 //~ echo '<pre>';
		 //~ print_r($arr_rars);
		 //~ die();

		/**/
		/*End*/
		/**/
		$acct_type1 = AccountMetas::where('meta_type','account_type')->orderBy('meta_name')->get();


		if($display_type == 'PDF')
		{
			return compact(
				'acct_type1',
				'ff_data',
				'ff_data_discon',
				'period_set',
				'lab_days',
				'curr_period',
				'new_arr',
				'more_than1',
				'curr_date_label',
				'zone_lbl'
			);
		}//

		//$zone, $display_month, $full_date
		$pdf_link = '/billing/report_account_recievable_summary_pdf/'.$zone.'/'.$display_month.'/'.$full_date;

		return view('billings.inc.billing_reports.ajax_aging_summary', compact(
			'acct_type1',
			'ff_data',
			'ff_data_discon',
			'period_set',
			'lab_days',
			'curr_period',
			'new_arr',
			'more_than1',
			'curr_date_label',
			'zone_lbl',
			'pdf_link'
	    ));


	}//End Sub




	function ReportsAccountRecievableSummaryPDF__AGESUMMARY($zone, $display_month, $full_date, $acct_type=14, $stat=2)
	{

		////////
		$date33 = date("Y-m-d", strtotime($full_date));
		$zone = (int) $zone;
		///////////

		$age1_mm = ageing_date_diff();

		$date11 = date('Y-m-d', strtotime(date('Y-m-01').' '.$age1_mm[$display_month]));

		$datetime1 = date_create($date11);
		$datetime2 = date_create(date('Y-m-01'));

		$interval = date_diff($datetime1, $datetime2);
		$def111 = $interval->format('%m');


		$ARR_PER = array();
		for($x=0;$x<$def111;$x++){
			$mmm = date('Y-m-01', strtotime(date('Y-m-01', strtotime($date33)).' - '.($x).'Month'));
			$ARR_PER[$mmm] = 0;
		}
		
		


		$result11 = DB::select("
			SELECT TAB1.*,TAB2.ttl_bal,TAB2.date01,TAB2.led_type,TAB2.period,AA.zone_id,AA.acct_no , AA.acct_type_key, AA.acct_status_key , AA.lname, AA.route_id

				FROM (
					SELECT acct_id, MAX(id) as last_led FROM `ledger_datas`
						WHERE date01 <= '".$date33."'
					GROUP BY acct_id
				)

			 AS TAB1

			LEFT JOIN ledger_datas as TAB2
			 ON TAB2.id=TAB1.last_led

			LEFT JOIN accounts as AA
				ON AA.id = TAB1.acct_id

			  WHERE (TAB2.ttl_bal != 0 )
			  AND AA.zone_id='$zone'
			  AND AA.acct_type_key='$acct_type'
			  AND AA.status != 'deleted'

			  ORDER BY AA.route_id ASC

		");

//			  AND (AA.acct_status_key='' OR AA.acct_status_key='')

//~ echo '<pre>';
//~ print_r($result11);
//~ die();


		$ttltotal = 0;
		$ttl_beg  = 0;

		foreach($result11 as $rr1)
		{
			$ttltotal+= $rr1->ttl_bal;

			$ttl20 = $rr1->ttl_bal;
			$acct_id = $rr1->acct_id;


			$rr112 = DB::select("
				SELECT *, ((TAB1.BB+TAB1.PP+TAB1.BEG) - TAB1.BA) as RRR FROM (

				SELECT  period, SUM(billing) as BB, SUM(penalty) as PP, SUM(bill_adj) as BA, sum(BEG) as BEG  FROM (


SELECT * FROM (SELECT LED11.period, (LED11.billing), (LED11.penalty),IFNULL(bill_adj,0) as bill_adj, (0) as BEG  FROM `ledger_datas` AS LED11
	WHERE LED11.acct_id='".$acct_id."' AND (LED11.led_type='billing' OR LED11.led_type='penalty' OR LED11.led_type='adjustment')
	AND id <= '".$rr1->last_led."'
UNION ALL
SELECT LED11.period, (LED11.billing) , (LED11.penalty),IFNULL(bill_adj,0) as bill_adj, (LED11.arrear) as BEG  FROM `ledger_datas` AS LED11
	WHERE LED11.acct_id='".$acct_id."' AND (LED11.led_type='beginning')
)as TABB_99  ORDER BY period ASC



				 ) AS TABXX
				 GROUP BY TABXX.period
				 ORDER BY TABXX.period DESC

				) as TAB1
			");



			$ggg = $ttl20;


			foreach($rr112 as $mm_2)
			{

				if($ttl20 == 0){break;}

				$def1 = $ttl20  - $mm_2->RRR;


				if($def1 < 0){
					@$ARR_PER[$mm_2->period]+= round($ttl20,2);
					break;
				}

				@$ARR_PER[$mm_2->period]+= $mm_2->RRR;
				$ttl20  = round($ttl20,2) - round($mm_2->RRR,2);
			}

		}

		return $ARR_PER;

	}//END
	
	function ReportsAccountRecievableSummaryPDF_v22($zone, $display_month, $full_date){
		$sql1 = "
		
			SELECT *, ((A2+A4) - A5) AS AR1  
			FROM (
				SELECT 
					period,
					SUM(arrear)   AS A1, 
					IFNULL(SUM(billing),0)  AS A2, 
					IFNULL(SUM(payment),0)  AS A3, 
					IFNULL(SUM(penalty),0)  AS A4, 
					IFNULL(SUM(bill_adj),0) AS A5 
				FROM (
					SELECT acct_id, arrear, billing, payment, discount, penalty, bill_adj, period FROM `ledger_datas` 
						WHERE acct_id=339 AND date01 AND led_type != 'beginning'
				) AS TAB1
					GROUP BY period
				) AS TAB2		
		";
		
		$sql2 = "
						
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
						GROUP BY acct_id
					) LD1 WHERE  LD1.mx1 = LD2.id
				 )
				 
				 AND LD2.ttl_bal > 0 AND AA.zone_id=1
				 ORDER BY AA.route_id ASC

		";
		
		
		echo 'AAAA';
		
		
	}//
	
	//report_account_recievable_summary_excel
	function report_account_recievable_summary_excel($zone, $display_month, $full_date)
	{
		/**/
    	Excel::load(public_path('excel02/reading_logs.xls'), function($excel){
			
				$excel->sheet('Sheet1', function($sheet) {
					
					
						$sheet->setCellValue('A1', 'RRRR');
						$sheet->setCellValue('A2', 'GGGG');
						$sheet->setCellValue('A3', 'YYYY');
						

					
				});
				
				$my_writer = $excel->get_writer();
				$my_writer->loadView('excel.ex01');

				
				
				$sheet2 = $excel->getActiveSheet();
				$sheet2->setCellValue('A1', 'GOLDEN');				
				
				//~ $excel->sheet('Sheet2', function($sheet) {
						//~ $sheet->setCellValue('A1', 'AAAA');
						//~ $sheet->setCellValue('A2', 'BBBB');
						//~ $sheet->setCellValue('A3', 'CCCCC');

				//~ });

			
				//~ $sheet = $excel->getActiveSheet();
				//~ $sheet->loadView('excel.ex01');
				//~ $sheet->parser->setView('excel.ex01');
				
				//~ echo '<pre>';
				//~ print_r($sheet);
				//~ die();


				
    	})->download('xls');		
    	
		/**/
		/*
			Excel::create('excel11', function($excel) {

				$excel->sheet('Sheet1', function($sheet) {

					$sheet->loadView('excel.ex01');
					
					$sheet->getColumnDimension('A')->setAutoSize(true);
					$sheet->getColumnDimension('B')->setAutoSize(true);
					$sheet->getColumnDimension('C')->setAutoSize(true);
					$sheet->getColumnDimension('D')->setAutoSize(true);
					$sheet->getColumnDimension('E')->setAutoSize(true);
					$sheet->getColumnDimension('F')->setAutoSize(true);
					$sheet->getColumnDimension('G')->setAutoSize(true);
					$sheet->getColumnDimension('H')->setAutoSize(true);
					$sheet->getColumnDimension('I')->setAutoSize(true);

			
				});
				
				$sheet2 = $excel->getActiveSheet();
				$sheet2->setCellValue('A1', 'GOLDEN');

				

			})->download('xls');
			/**/ 
	}//
	
	//report_account_recievable_summary_pdf
	function ReportsAccountRecievableSummaryPDF($zone, $display_month, $full_date)
	{
		
		//~ die();
		//~ return $this->ReportsAccountRecievableSummaryPDF_v22($zone, $display_month, $full_date);

		$acct_type1 = AccountMetas::where('meta_type','account_type')->orderBy('meta_name')->get();

		$contain1 = array();
		$xx = 0;
		
		foreach($acct_type1 as $typ1)
		{
			$ARR_PER = $this->ReportsAccountRecievableSummaryPDF__AGESUMMARY($zone, $display_month, $full_date, $typ1->id);
			$contain1[$xx]['data'] = $ARR_PER;
			$contain1[$xx]['name'] = strtoupper($typ1->meta_name);
			$xx++;
		}

	$new_arr22 = ageing_date_breakdown($display_month);
	//~ echo '<pre>';
	//~ print_r($new_arr22);
	//~ die();


	$pdf = PDF::loadView('billings.inc.billing_billing.report_pdf.ageing_summary',
				compact(
						'contain1',
						'new_arr22',
						'full_date'
					));

		return $pdf->stream('ageing_summary_'.strtotime('NOW').'.pdf');

		print_r($ARR_PER);
		die();
		die();
		die();
		die();
		die();


		$date1 = $this->ReportsAccountRecievableSummary($zone, $display_month, $full_date, 'PDF');

		//~ echo $display_month;
		//~ die();

		extract($date1);

		$new_arr22 = ageing_date_breakdown($display_month);


		$new_arr_diff  = ageing_date_diff();

		//~ echo '<pre>';
		//~ print_r($new_arr_diff);
		//~ die();


		$dat1 = $full_date;

		//~ ageing_get_current_bill_adj_pen($dat1, $zone);


		//~ $act1_active = Accounts::where('accounts.zone_id', $zone)
							//~ ->where('accounts.status', '!=', 'deleted')
							//~ ->where('accounts.acct_status_key', '2')
							//~ ->join('ledger_datas', function($join){
								//~ $join->on('accounts.id', '=', 'ledger_datas.acct_id');
							//~ })
							//~ ->where('ledger_datas.date01', '<=', $dat1)
							//~ ->select(DB::raw('
									//~ accounts.acct_type_key,
									//~ SUM(ledger_datas.ttl_bal) as ttl_typ,
									//~ accounts.zone_id'))
							//~ ->groupBy('accounts.acct_type_key')
							//~ ->get();


		//~ $act1_disconnected = Accounts::where('accounts.zone_id', $zone)
								//~ ->where('accounts.status', '!=', 'deleted')
								//~ ->where('accounts.acct_status_key', '4')
								//~ ->join('ledger_datas', function($join){
									//~ $join->on('accounts.id', '=', 'ledger_datas.acct_id');
								//~ })
								//~ ->where('ledger_datas.date01', '<=', $dat1)
								//~ ->select(DB::raw('accounts.acct_type_key, SUM(0) as ttl_typ, accounts.zone_id'))
								//~ ->groupBy('accounts.acct_type_key')
								//~ ->get();


		//~ $active_type = array();

		//~ foreach($act1_active as $rr)
		//~ {
			//~ $active_type[$rr->acct_type_key] = $rr->ttl_typ;
		//~ }

		//~ $discon_type = array();
		//~ foreach($act1_disconnected as $rr)
		//~ {
			//~ $discon_type[$rr->acct_type_key] = $rr->ttl_typ;
		//~ }

		//~ echo '<pre>';
		//~ print_r($active_type);
		//~ print_r($discon_type);
		//~ print_r($act1_active->toArray());
		//~ die();

		Fpdf::AddPage('P', 'Letter');
		Fpdf::SetMargins(6, 5, 5);
		Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Ln();
		Fpdf::write(5, WD_NAME);
		Fpdf::Ln();
		Fpdf::write(4, WD_ADDRESS);
		Fpdf::Ln();
		Fpdf::write(4, 'Ageing of Account Recievable - Summary');
		Fpdf::Ln();
		Fpdf::write(4, 'As of '.$curr_date_label);
		Fpdf::Ln();
		Fpdf::write(4, $zone_lbl);
		Fpdf::Ln();
		Fpdf::Ln();

		Fpdf::write(6, 'Active');

		Fpdf::Ln();
		Fpdf::SetFont('Courier',null, 7);
		Fpdf::Cell(30,5, '','B',0,'L', false);
		Fpdf::Cell(10,5, 'A/R Others','B',0,'R', false);
		Fpdf::Cell(20,5, 'Current','B',0,'R', false);
		foreach($new_arr22 as $kk => $vv):
		Fpdf::Cell(20,5, $vv,'B',0,'R', false);
		endforeach;
		Fpdf::Cell(20,5, '>'.$vv,'B',0,'R', false);
		Fpdf::Cell(30,5, 'WB Total','B',0,'R', false);
		Fpdf::Ln();


		$ttl_sub1 = 0;
		$grand_ttl = 0;

		$new_arr2 = array(
					 'm1' => '- 1 Month',
					 'm2' => '- 2 Month',
					 'm3' => '- 3 Month',
					 'm4' => '- 4 Month',
					 'm5' => '- 5 Month',
					 'm5' => '- 6 Month',
					 'y1' => '- 1 Year'
					);

		$d1 = date('Y-m-28', strtotime($dat1));
		$d2 = date('Y-m-01', strtotime($d1.' '.$new_arr2[$display_month]));
		$d3 = date('Y-m-01', strtotime($d1));

		//~ echo '<pre>';
		//~ echo $d3;
		//~ die();

		foreach($acct_type1 as $at1):

			$ttl_44 = curr_bill_v3($at1->id, $dat1, $zone, $display_month);

			$rr11 = ageing_get_current_led_by_type($dat1, $zone, $at1->id);
			//~ $ttl_sub1 +=$rr11;
			$ttl_sub1 +=$ttl_44['ttl'];

			Fpdf::Cell(30,5, $at1['meta_name'],0,0,'L', false);
			Fpdf::Cell(10,5, '-',0,0,'R', false);

			$A3 = number_format(@$ttl_44['brdown'][$d3],2);
			Fpdf::Cell(20,5, $A3,0,0,'R', false);
			foreach($new_arr22 as $kk => $vv):
				$d4 = date('Y-m-01', strtotime($d1.' '.$new_arr2[$kk]));
				$A4 = number_format(@$ttl_44['brdown'][$d4],2);
				Fpdf::Cell(20,5, $A4,0,0,'R', false);
			endforeach;
			Fpdf::Cell(20,5, '',0,0,'R', false);
			Fpdf::Cell(30,5, number_format($ttl_44['ttl'],2),0,0,'R', false);
			Fpdf::Ln();

		endforeach;
		//~ die();
		$grand_ttl+=$ttl_sub1;

		//~ die();
		//~ die();
		//~ die();
		//~ die();
		//~ die();

		/*---------------------*/
		/*---------------------*/
		Fpdf::Cell(30,5, 'Sub Total','T',0,'L', false);
		Fpdf::Cell(10,5, '-','T',0,'R', false);
		Fpdf::Cell(20,5, '','T',0,'R', false);
		foreach($period_set as $kk => $vv):
			Fpdf::Cell(20,5, '','T',0,'R', false);
		endforeach;
		Fpdf::Cell(20,5, '','T',0,'R', false);
		Fpdf::Cell(30,5, number_format($ttl_sub1,2),'T',0,'R', false);
		Fpdf::Ln();
		/*---------------------*/
		/*---------------------*/

		Fpdf::Ln();
		Fpdf::Ln();

		Fpdf::write(6, 'Disconnected');
		Fpdf::Ln();
		Fpdf::SetFont('Courier',null, 7);
		Fpdf::Cell(30,5, '','B',0,'L', false);
		Fpdf::Cell(10,5, 'A/R Others','B',0,'R', false);
		Fpdf::Cell(20,5, 'Current','B',0,'R', false);
		foreach($new_arr22 as $kk => $vv):
		Fpdf::Cell(20,5, $vv,'B',0,'R', false);
		endforeach;
		Fpdf::Cell(20,5, '>'.$lab_days[$kk],'B',0,'R', false);
		Fpdf::Cell(30,5, 'WB Total','B',0,'R', false);
		Fpdf::Ln();

		$ttl_sub1 = 0;
		foreach($acct_type1 as $at1):
			$ttl_1 = ageing_get_current_led_by_type($dat1, $zone, $at1->id, 4);
			$ttl_sub1 += $ttl_1;
			Fpdf::Cell(30,5, $at1['meta_name'],0,0,'L', false);
			Fpdf::Cell(10,5, '-',0,0,'R', false);
			Fpdf::Cell(20,5, '',0,0,'R', false);
			foreach($new_arr22 as $kk => $vv):
				Fpdf::Cell(20,5, '',0,0,'R', false);
			endforeach;
			Fpdf::Cell(20,5, '',0,0,'R', false);
			Fpdf::Cell(30,5, number_format($ttl_1,2),0,0,'R', false);
			Fpdf::Ln();
		endforeach;
		$grand_ttl+=$ttl_sub1;

		/*---------------------*/
		/*---------------------*/
		Fpdf::Cell(30,5, 'Sub Total','T',0,'L', false);
		Fpdf::Cell(10,5, '-','T',0,'R', false);
		Fpdf::Cell(20,5, '','T',0,'R', false);
		foreach($new_arr22 as $kk => $vv):
			Fpdf::Cell(20,5, '','T',0,'R', false);
		endforeach;
		Fpdf::Cell(20,5, '','T',0,'R', false);
		Fpdf::Cell(30,5, number_format($ttl_sub1,2),'T',0,'R', false);
		Fpdf::Ln();
		/*---------------------*/
		/*---------------------*/

		Fpdf::Ln();
		Fpdf::Ln();

		/*---------------------*/
		/*---------------------*/
		Fpdf::Cell(30,5, 'Grand Total','BT',0,'L', false);
		Fpdf::Cell(10,5, '-','TB',0,'R', false);
		Fpdf::Cell(20,5, '','TB',0,'R', false);
		foreach($period_set as $kk => $vv):
			Fpdf::Cell(20,5, '','TB',0,'R', false);
		endforeach;
		Fpdf::Cell(20,5, '','TB',0,'R', false);
		Fpdf::Cell(30,5, number_format($grand_ttl,2),'BT',0,'R', false);
		Fpdf::Ln();
		/*---------------------*/
		/*---------------------*/
		Fpdf::Ln();
		Fpdf::Ln();
		Fpdf::Ln();
		Fpdf::Cell(60,5, 'Prepared By: ','',0,'L', false);
		Fpdf::Cell(60,5, 'Checked/Verified by:'.'  '.' ','',0,'L', false);
		Fpdf::Cell(60,5, 'Noted'.'  '.' ','',0,'L', false);

		Fpdf::Ln();
		//~ Fpdf::Cell(60,4, '___________________','',0,'L', false);
		//~ Fpdf::Cell(60,4, '___________________'.'  '.' ','',0,'L', false);
		//~ Fpdf::Cell(60,4, '___________________.'.'  '.' ','',0,'L', false);

		Fpdf::Cell(60,4, '___________________','',0,'L', false);
		Fpdf::Cell(60,4, '___________________'.'  '.' ','',0,'L', false);
		Fpdf::Cell(60,4, WD_MANAGER.'  '.' ','',0,'L', false);
		
		Fpdf::Ln();
		Fpdf::Cell(60,3, 'System Administrator','',0,'L', false);
		Fpdf::Cell(60,3, 'Admin/Gen. Services Officer'.'  '.' ','',0,'L', false);
		Fpdf::Cell(60,3, WD_MANAGER_RA.'  '.' ','',0,'L', false);
		Fpdf::Ln();




		Fpdf::Output();
		exit;


	}//END FUNC

	function ReportsAccountRecievableSummaryPDF_JUL_29_OLD($zone, $display_month, $full_date)
	{
		$date1 = $this->ReportsAccountRecievableSummary($zone, $display_month, $full_date, 'PDF');

		//~ echo '<pre>';
		//~ print_r($date1['acct_type1']->toArray());
		//~ print_r($date1);
		//~ die();

		extract($date1);

		Fpdf::AddPage('P', 'Letter');
		Fpdf::SetMargins(6, 5, 5);
		Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Ln();
		Fpdf::write(5, WD_NAME);
		Fpdf::Ln();
		Fpdf::write(4, WD_ADDRESS);
		Fpdf::Ln();
		Fpdf::write(4, 'Ageing of Account Recievable - Summary');
		Fpdf::Ln();
		Fpdf::write(4, 'As of '.$curr_date_label);
		Fpdf::Ln();
		Fpdf::write(4, $zone_lbl);
		Fpdf::Ln();
		Fpdf::Ln();

		Fpdf::write(6, 'Active');
		Fpdf::Ln();

		Fpdf::SetFont('Courier',null, 7);
		Fpdf::Cell(30,5, '','B',0,'L', false);
		Fpdf::Cell(10,5, 'A/R Others','B',0,'R', false);
		Fpdf::Cell(20,5, 'Current','B',0,'R', false);
		foreach($new_arr as $kk => $vv):
		Fpdf::Cell(20,5, $lab_days[$kk],'B',0,'R', false);
		endforeach;
		Fpdf::Cell(20,5, '>'.$lab_days[$kk],'B',0,'R', false);
		Fpdf::Cell(30,5, 'WB Total','B',0,'R', false);
		Fpdf::Ln();

		$total_1 = array();
		foreach($acct_type1 as $at1):
			$kk001 = 'type'.$at1['id'].date('Ymd',strtotime($curr_period));
	          $current_bill = (float) @$ff_data[$kk001]['bill'];
	          $sub_total_bill =  $current_bill;
	          $total_x = 0;
	          @$total_1[$total_x] += $current_bill;
	          $total_x++;

			Fpdf::Cell(30,5, $at1['meta_name'],0,0,'L', false);
			Fpdf::Cell(10,5, '-',0,0,'R', false);
			Fpdf::Cell(20,5,  number_format($current_bill, 2),0,0,'R', false);
			foreach($period_set as $kk => $vv):
				$kk11  = 'type'.$at1['id'].date('Ymd',strtotime($vv));
	               $billx =  (float) @$ff_data[$kk11]['bill'];
	               $sub_total_bill+=$billx;
	               @$total_1[$total_x] += $billx;
	               $total_x++;

				Fpdf::Cell(20,5, number_format($billx,2),0,0,'R', false);

			endforeach;
			$kk11 = 'type_m_'.$at1['id'];
               $billx =  (float) @$ff_data[$kk11]['bill'];
               $sub_total_bill+=$billx;

               @$total_1[$total_x] += $billx;
               $total_x++;

               @$total_1[$total_x] += $sub_total_bill;
               $total_x++;

			Fpdf::Cell(20,5, number_format($billx,2),0,0,'R', false);
			Fpdf::Cell(30,5, number_format($sub_total_bill,2),0,0,'R', false);

			Fpdf::Ln();
		endforeach;

		$total_x = 0;

		//Fpdf::SetFont('Courier','bold', 7);
		Fpdf::Cell(30,5, 'Sub Total	','T',0,'L', false);
		Fpdf::Cell(10,5, '-','T',0,'R', false);
		Fpdf::Cell(20,5, number_format($total_1[$total_x],2),'T',0,'R', false);$total_x++;
		foreach($new_arr as $kk => $vv):
		Fpdf::Cell(20,5, number_format($total_1[$total_x], 2),'T',0,'R', false);$total_x++;
		endforeach;
		Fpdf::Cell(20,5, number_format($total_1[$total_x], 2),'T',0,'R', false);$total_x++;
		Fpdf::Cell(30,5,  number_format($total_1[$total_x], 2),'T',0,'R', false);
		Fpdf::Ln();
		Fpdf::Ln();

		Fpdf::SetFont('Courier','B', 7);
		Fpdf::write(6, 'Disconnected');
		Fpdf::Ln();

		Fpdf::SetFont('Courier',null, 7);
		Fpdf::Cell(30,5, '','B',0,'L', false);
		Fpdf::Cell(10,5, 'A/R Others','B',0,'R', false);
		Fpdf::Cell(20,5, 'Current','B',0,'R', false);
		foreach($new_arr as $kk => $vv):
		Fpdf::Cell(20,5, $lab_days[$kk],'B',0,'R', false);
		endforeach;
		Fpdf::Cell(20,5, '>'.$lab_days[$kk],'B',0,'R', false);
		Fpdf::Cell(30,5, 'WB Total','B',0,'R', false);
		Fpdf::Ln();

		$total_1 = array();
		foreach($acct_type1 as $at1):
			$kk001 = 'type'.$at1['id'].date('Ymd',strtotime($curr_period));
	          $current_bill = (float) @$ff_data_discon[$kk001]['bill'];
	          $sub_total_bill =  $current_bill;
	          $total_x = 0;
	          @$total_1[$total_x] += $current_bill;
	          $total_x++;

			Fpdf::Cell(30,5, $at1['meta_name'],0,0,'L', false);
			Fpdf::Cell(10,5, '-',0,0,'R', false);
			Fpdf::Cell(20,5,  number_format($current_bill, 2),0,0,'R', false);
			foreach($period_set as $kk => $vv):
				$kk11  = 'type'.$at1['id'].date('Ymd',strtotime($vv));
	               $billx =  (float) @$ff_data_discon[$kk11]['bill'];
	               $sub_total_bill+=$billx;
	               @$total_1[$total_x] += $billx;
	               $total_x++;

				Fpdf::Cell(20,5, number_format($billx,2),0,0,'R', false);

			endforeach;
			$kk11 = 'type_m_'.$at1['id'];
               $billx =  (float) @$ff_data_discon[$kk11]['bill'];
               $sub_total_bill+=$billx;

               @$total_1[$total_x] += $billx;
               $total_x++;

               @$total_1[$total_x] += $sub_total_bill;
               $total_x++;

			Fpdf::Cell(20,5, number_format($billx,2),0,0,'R', false);
			Fpdf::Cell(30,5, number_format($sub_total_bill,2),0,0,'R', false);

			Fpdf::Ln();
		endforeach;
		$total_x = 0;

		//Fpdf::SetFont('Courier','bold', 7);
		Fpdf::Cell(30,5, 'Sub Total	','T',0,'L', false);
		Fpdf::Cell(10,5, '-','T',0,'R', false);
		Fpdf::Cell(20,5, number_format($total_1[$total_x],2),'T',0,'R', false);$total_x++;
		foreach($new_arr as $kk => $vv):
		Fpdf::Cell(20,5, number_format($total_1[$total_x], 2),'T',0,'R', false);$total_x++;
		endforeach;
		Fpdf::Cell(20,5, number_format($total_1[$total_x], 2),'T',0,'R', false);$total_x++;
		Fpdf::Cell(30,5,  number_format($total_1[$total_x], 2),'T',0,'R', false);
		Fpdf::Ln();
		Fpdf::Ln();

		//Fpdf::SetFont('Courier',null, 7);
		// Fpdf::SetFont('Courier',null, 7);




		Fpdf::Output();
		exit;

	}


	function ReportGetByZone($zone, $month,$full_date, Request $request)
	{

		// echo 'AAAA';
		// die();

		//extract($_POST);
		// echo  $pdf_link = '/billing/report_get_by_zone/pdf/'.$zone.'/'.$month;
		// die();

		//$T1 = strtotime('TODAY');
		//$T1 = strtotime($period_year.'-'.$period_month);
		$T1 = strtotime($full_date);

		//$curr_period = date('Y-m-28');
		$curr_period = date('Y-m-01', $T1);
		$curr_date_label  =  date('l, F d, Y', $T1);

		$data1 = ageing_common(array('mm_arr', 'lab_days', 'zone_arr'));
		extract($data1);

		$i = array_search($month, array_keys($mm_arr));
		$new_arr = array_slice($mm_arr, 0, ($i+1));
		$ageing_prev = array_slice($mm_arr,($i+1));

		if(empty($new_arr)){
			return;
		}

		$period_set = array();
		foreach($new_arr as  $mm){
			$T2 = strtotime($curr_period.' '.$mm);
			$period_set[] = date('Y-m-01', $T2);
		}

		$zone_lbl = $zone_arr[$zone];

		  // echo '<pre>';
		  // print_r($zone_lbl);
		  // print_r($period_set);
		  // print_r($new_arr);
		  // print_r($mm_arr);
		  // die();

		$acct_result = array();
		$acct_counts = 0;

		//~ echo $curr_period;
		//~ die();

		$result1 = Reports::where('reports.rtype', 'ageing_of_accounts')
			->where('reports.period', $curr_period)
			->where('accounts.zone_id', $zone)
			->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
			->orderBy('reports.full_name', 'asc')
			->groupBy('reports.id')
			->addSelect(DB::raw('reports.*,
				accounts.zone_id as zone_orig,
				accounts.meter_number1
			'))
			->chunk(100, function($res1) use(&$acct_result, &$acct_counts){
				$acct_result[] =  $res1->toArray();
				$acct_counts+=$res1->count();
			});

		if(!$result1)
		{

			$result1 = Reports::where('reports.rtype', 'ageing_of_accounts')
				->where('reports.period', $period_set[0])
				->where('accounts.zone_id', $zone)
				->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
				->orderBy('reports.full_name', 'asc')
				->groupBy('reports.id')
				->addSelect(DB::raw('reports.*,
					accounts.zone_id as zone_orig,
					accounts.meter_number1
				'))
				->chunk(100, function($res1) use(&$acct_result, &$acct_counts){
					$acct_result[] =  $res1->toArray();
					$acct_counts+=$res1->count();
				});
		}


		$pdf_link = '/billing/report_get_by_zone/pdf/'.$zone.'/'.$month.'/'.$full_date;
		return view('billings.inc.billing_reports.ajax_aging1',
		compact('acct_result', 'period_set', 'new_arr',
		'lab_days', 'pdf_link', 'ageing_prev', 'curr_date_label',
		'zone_lbl', 'acct_counts'
		));

		 echo '<pre>';
		 print_r($acct_result);

	}//END

	//~ function ReportGetByZonePDF($zone, $month, $full_date)
	function ReportGetByZonePDF_XXXXXXX($zone, $month, $full_date)
	{

		$T1 = strtotime($full_date);
		$curr_period = date('Y-m-28', $T1);
		$curr_date_label  =  date('l, F d, Y', $T1);

		$data1 = ageing_common(array('mm_arr', 'lab_days', 'zone_arr'));
		extract($data1);

		//~ echo '<pre>';
		//~ print_r($mm_arr);
		//~ print_r($zone_arr);
		//~ print_r($lab_days);

		$report_aging = Reports::where('rtype', 'ageing_of_accounts')
						->where('reports.period', $curr_period)
						->get();

		echo '<pre>';
		print_r($report_aging->toArray());


	}//

	//~ function ReportGetByZonePDF_OOOOOOOOOOO($zone, $month, $full_date)

	function ReportGetByZonePDF_Get_Ageing_Jan_04_2021_01($zone, $full_date, $acct_stat=2, $acct_typ=0, $is_seniour=0)
	{

		$acct_type_sql = "";
		if(!empty($acct_typ)){
			$acct_type_sql = " AND acct_type_key=$acct_typ   ";
		}

		$senior_sql = "";
		if(!empty($is_seniour)){
			if($is_seniour == 1){
				$senior_sql = "  AND acct_discount='".SENIOR_ID."'  ";
			}elseif($is_seniour == 2){
				$senior_sql = "  AND (acct_discount=''  OR  acct_discount=0  OR  acct_discount is null)  ";
			}
		}

		$acct_stat_sql = "";
		if(!empty($acct_stat)){
			if($acct_stat == 2){// Active
				$acct_stat_sql = "  AND acct_status_key=$acct_stat  ";
			}elseif($acct_stat == 4){// Disconnected
				$acct_stat_sql = "  AND acct_status_key=$acct_stat  ";
			}
		}

		$zone = (int) $zone;
		$zone_sql = "";
		if($zone > 0){
			$zone_sql = " AND accounts.zone_id=$zone   ";
		}


		$sql1_active  = "
				SELECT 
					id, acct_id, date01, ttl_bal 
				FROM 
					ledger_datas
				WHERE id IN(
					SELECT max(id) id1 FROM ledger_datas 
					WHERE 
					acct_id IN(
							SELECT id from accounts 
								WHERE 
									accounts.status='active'
									$zone_sql
									$acct_stat_sql
									$senior_sql
									$acct_type_sql
						)
					AND status  = 'active'
					AND date01 <= '$full_date'
					GROUP BY acct_id
				)
				AND ttl_bal !=0
		";
		// limit 500

		$rs1     = DB::select($sql1_active,[$zone]);
		$aar1    = array();
		$bal_arr = array();

		foreach($rs1 as $r1)
		{
			$aar1[] = $r1->acct_id;
			$bal_arr[$r1->acct_id] = $r1->ttl_bal;

			// Dec292020____get_payment_breakdown($r1->acct_id);			
		}


		if(empty($aar1)){
			return array('stat'=>0, 'result' => [], 'msg'=>'No result');
		}

		$str1 = implode(', ', $aar1);

		// echo '<pre>';
		// print_r($str1);
		// die();
		
		$sql22 = "
			SELECT *, IF((amt1-payled_ttl) is not null, (amt1-payled_ttl), amt1) re1 FROM (
				SELECT 
					id,
					acct_num,
					acct_id,
					IF(led_type='beginning', id, reff_no) reff1,
					led_type,
					status,
					date01,
					IF(led_type='beginning',ttl_bal, IF(led_type='penalty',penalty, billing-discount)) amt1,
					IF(led_type='billing', (
							SELECT SUM(amt) payle1 
								FROM pay_pen_leds 
							WHERE 
									pay_pen_leds.uid=ledger_datas.acct_id 
									AND (pay_pen_leds.typ=ledger_datas.led_type OR pay_pen_leds.typ='over_pay')
									AND pay_pen_leds.pen_id=reff1
								AND pay_pen_leds.date11 <= '$full_date'
					), (
						SELECT SUM(amt) payle1 
							FROM pay_pen_leds 
						WHERE 
								pay_pen_leds.uid=ledger_datas.acct_id 
							AND pay_pen_leds.typ=ledger_datas.led_type 
							AND pay_pen_leds.pen_id=reff1
							AND pay_pen_leds.date11 <= '$full_date'
					))  payled_ttl
				
						FROM `ledger_datas`
				WHERE ledger_datas.acct_id IN($str1) 
					AND ledger_datas.led_type IN ('beginning', 'billing', 'penalty') 
					AND ledger_datas.status='active'
					AND ledger_datas.date01 <= '$full_date'
			) TAB1 
				WHERE (amt1 != payled_ttl OR payled_ttl is null)	
		";

		$result002  = DB::select($sql22);

		// echo '<pre>';
		// print_r($result002);
		// die();

		$no_zero = [];
		foreach($result002 as $r1)
		{
			if($r1->amt1 < 0.01){continue;}
			if($bal_arr[$r1->acct_id] < 0.01){continue;}
			$no_zero[] = $r1;
		}

		$d_today 	 = date('Y-m-d',strtotime($full_date));
		// $d_90_days   = date('Y-m-d',strtotime($full_date.' - 3 MONTHS'));	
		// $d_1_year    = date('Y-m-d',strtotime($full_date.' - 1 YEAR'));		
		// $d_2_year    = date('Y-m-d',strtotime($full_date.' - 2 YEAR'));		
		// $d_3_year    = date('Y-m-d',strtotime($full_date.' - 3 YEAR'));		

		$d_90_days   = date('Y-m-d',strtotime($full_date.' - 30 Days'));	
		$d_1_year    = date('Y-m-d',strtotime($full_date.' - 60 Days'));		
		$d_2_year    = date('Y-m-d',strtotime($full_date.' - 90 Days'));		
		$d_3_year    = date('Y-m-d',strtotime($full_date.' - 120 Days'));		

		$acct_data1  = array();
		foreach($no_zero as $vv)
		{
			$acct_data1[$vv->acct_id]['A']     = 0;
			$acct_data1[$vv->acct_id]['B']     = 0;
			$acct_data1[$vv->acct_id]['C']     = 0;
			$acct_data1[$vv->acct_id]['D']     = 0;
			$acct_data1[$vv->acct_id]['E']     = 0;	
			$acct_data1[$vv->acct_id]['bal']   = $bal_arr[$vv->acct_id];	
		}

		foreach($no_zero as $vv)
		{
			$my_date  = strtotime($vv->date01);

			if( $my_date <= strtotime($d_today) &&  $my_date >= strtotime($d_90_days) ){
				$acct_data1[$vv->acct_id]['A'] += round($vv->re1,2);
			}elseif( $my_date < strtotime($d_90_days) &&  $my_date >= strtotime($d_1_year) ){
				$acct_data1[$vv->acct_id]['B'] += $vv->re1;
			}elseif( $my_date < strtotime($d_1_year) &&  $my_date >= strtotime($d_2_year) ){
				$acct_data1[$vv->acct_id]['C'] += $vv->re1;
			}
			// elseif( $my_date < strtotime($d_2_year) &&  $my_date >= strtotime($d_3_year) ){
			// 	$acct_data1[$vv->acct_id]['D'] += $vv->re1;
			// }
			elseif( $my_date < strtotime($d_2_year) ){
				$acct_data1[$vv->acct_id]['D'] += $vv->re1;
			}
		}
		
		// echo '<pre>';
		// print_r($no_zero);
		// die();

		// echo '<pre>';
		// print_r($acct_data1);
		// die();		

		$acct_ids = implode(', ', array_keys($acct_data1)).'';

		$sql_acct = "
			SELECT id, CONCAT(acct_no,' - ', fname,' ', lname) DD FROM `accounts`	
			WHERE id IN ($acct_ids)	
		";

		$result003  = DB::select($sql_acct);
		$acct_name1 = array();

		foreach($result003 as $vv)
		{
			$acct_name1[$vv->id] = strtoupper($vv->DD);
		}

		return array('stat'=>1, 'result' => $acct_data1, 'acct_data' => $acct_name1);
	}




	function ReportGetByZonePDF($zone, $month, $full_date)
	{
		$zone 	     = (int) $zone;
		$full_date   = date('Y-m-d',strtotime($full_date));

		// AND led_type NOT IN('cancel_cr_nw','cr_nw', 'cr_nw_debit', 'nw_cancel', 'or_nw', 'or_nw_debit')
		// AND led_type NOT IN('adjustment','beginning', 'billing', 'cancel_cr', 'payment', 'payment_cancel', 'payment_cr', 'penalty', 'wtax')
		// AND led_type IN('adjustment','beginning', 'billing', 'cancel_cr', 'payment', 'payment_cancel', 'payment_cr', 'penalty', 'wtax')


		$sql1 = "
					SELECT * FROM (
						SELECT acct_id, acct_num, ttl_bal FROM ledger_datas 
						WHERE id IN(
								SELECT MAX(id) iid  FROM `ledger_datas`
								WHERE date01 <= ? AND status='active'
								AND led_type IN('adjustment','beginning', 'billing', 'cancel_cr', 'payment', 'payment_cancel', 'payment_cr', 'penalty', 'wtax')
								AND acct_id IN (
									SELECT id FROM accounts WHERE zone_id=? and (acct_status_key = 2 or acct_status_key = 4 or acct_status_key = 15)
								)
								GROUP by acct_id
							)
						) Tab1
					WHERE ttl_bal > 0  
		";

		$sql2 = "
			SELECT id, led_type, billing, penalty, bill_adj, discount, date01, IF(led_type='beginning',arrear, (billing+penalty)-discount)  debit1 FROM ledger_datas WHERE
			acct_id=? AND status='active'
			AND led_type  IN ('billing', 'penalty', 'beginning')
			AND date01 <= ?
			order by zort1 desc, id DESC
		";

		$acct_001  = DB::select($sql1, [$full_date, $zone]);

		$d1   = strtotime($full_date);
		$d30  = strtotime($full_date.' - 1 Month');
		$d60  = strtotime($full_date.' - 2 Month');
		$d90  = strtotime($full_date.' - 3 Month');
		$d180 = strtotime($full_date.' - 6 Month');
		$d365 = strtotime($full_date.' - 1 Year');


		foreach($acct_001 as $acct1)
		{
				$ttl_dat = [
					'd30' => 0,
					'd60' => 0,
					'd90' => 0,
					'd180' => 0,
					'd365' => 0,
					'more_365' => 0
				];

				$acct_002  = DB::select($sql2, [$acct1->acct_id, $full_date]);
			
				foreach($acct_002 as $act2)
				{
					$cdat = strtotime($act2->date01);
					if($cdat <= $d1 && $cdat >= $d30){ // 30 DAYS
						$ttl_dat['d30'] += $act2->debit1;
					}
					if($cdat < $d30 && $cdat >= $d60){ // 60 DAYS
						$ttl_dat['d60'] += $act2->debit1;
					}
					if($cdat < $d60 && $cdat >= $d90){ // 90 DAYS
						$ttl_dat['d90'] += $act2->debit1;
					}
					if($cdat < $d90 && $cdat >= $d180){ // 180 DAYS
						$ttl_dat['d180'] += $act2->debit1;
					}
					if($cdat < $d180 && $cdat >= $d365){ // 365 DAYS
						$ttl_dat['d365'] += $act2->debit1;
					}
					if($cdat < $d365){ // Greater 365 DAYS
						$ttl_dat['more_365'] += $act2->debit1;
					}
				}

				$ttl_oo1 = $acct1->ttl_bal;

				foreach($ttl_dat as $kk=>$h1){
					if($ttl_oo1 <= $h1){
						$ttl_dat[$kk] = round($ttl_oo1,2);
						$ttl_oo1 -= $h1;
					}else{
						$ttl_oo1 -= $h1;
						continue;
					}
				}

				foreach($ttl_dat as $kk=>$h1){
					if($h1 < 0){$ttl_dat[$kk] = 0;}
				}

				$acct1->brk001  = $ttl_dat;
				$acct1->account = (object) Accounts::find($acct1->acct_id)->toArray();

		}//

		$active_arr  = [];
		$disconn_arr = [];
		$other_ar    = [];
		$voluntaryDis  = [];
		
		foreach($acct_001 as $a1)
		{
			if($a1->account->acct_status_key == 4){
				$disconn_arr[] = $a1; 
			}
			elseif ($a1->account->acct_status_key == 15) {
				$voluntaryDis[] = $a1;
			}
			elseif($a1->account->acct_status_key == 2){
				$active_arr[] = $a1;
			}else{
				$other_ar[] = $a1;
			}
		}


		$zone_name = get_zone101($zone);

		return view('reports.ageing_all', compact('voluntaryDis','active_arr','disconn_arr','other_ar', 'full_date', 'zone_name'));
		


		// echo '<pre>';
		// print_r($acct_001);
		// print_r($ttl_dat);

		// $all_zones = Zones::where('id', $zone)->orderBy('zone_name', 'asc')->get();
		// return $this->ReportGetByZonePDF_MAR_3_2021($zone, $month, $full_date);
	}

	function ReportGetByZonePDF_MAR_3_2021($zone, $month, $full_date)
	{

		// die();
		// die();
		// die();

		$zone 	     = (int) $zone;
		$full_date   = date('Y-m-d',strtotime($full_date));

		// $ACTIVE_AGEING       = $this->ReportGetByZonePDF_Get_Ageing_Jan_04_2021_01($zone, $full_date, 2);
		// $DISCONNECTED_AGEING = $this->ReportGetByZonePDF_Get_Ageing_Jan_04_2021_01($zone, $full_date, 4);

		$all_zones = Zones::where('status', '!=', 'deleted')->where('id', $zone)->orderBy('zone_name', 'asc')->get();

		// echo '<pre>';
		// print_r($all_zones->toArray());
		// die();		

		$zon_cont = array();
		$zon_name = array();
		foreach($all_zones as $kk1 => $vv1){
			
			$ACTIVE_AGEING       = $this->ReportGetByZonePDF_Get_Ageing_Jan_04_2021_01($vv1->id, $full_date, 2);
			$DISCONNECTED_AGEING = $this->ReportGetByZonePDF_Get_Ageing_Jan_04_2021_01($vv1->id, $full_date, 4);

			foreach($ACTIVE_AGEING['result'] as $kk5 => $vv5){
				if($vv5['A'] <= 0){
					$DISCONNECTED_AGEING['result'][$kk5]    = $ACTIVE_AGEING['result'][$kk5];
					$DISCONNECTED_AGEING['acct_data'][$kk5] = $ACTIVE_AGEING['acct_data'][$kk5];

					unset($ACTIVE_AGEING['result'][$kk5]);
					unset($ACTIVE_AGEING['acct_data'][$kk5]);
					continue;
				}
				
				$tmp_arr = [];
				$AA = 'A';
				foreach($vv5 as $k6=>$v6){
					if($k6 == 'bal'){$tmp_arr['bal'] = $v6; continue;}
					if($v6 <= 0){continue;}
					$tmp_arr[$AA] = $v6;
					$AA++;
				}
				$ACTIVE_AGEING['result'][$kk5] = $tmp_arr;

			}
	

			$zon_cont[$vv1->id]['active'] = $ACTIVE_AGEING; 
			$zon_cont[$vv1->id]['discon'] = $DISCONNECTED_AGEING; 

			$zon_name[$vv1->id] = $vv1->zone_name;

			// break;
		}


		$all_params = compact(
			'zone',
			'ACTIVE_AGEING',
			'DISCONNECTED_AGEING',
			'full_date',
			'all_zones',
			'zon_name',
			'zon_cont'
		);


		//$vv1.' | '.$stt.' TOTAL'
		function ___CCC2(&$row, &$sheet, $ttl_1,  $LABEL1)
		{
			$sheet->setCellValue('A'.$row, $LABEL1);
			$sheet->setCellValue('C'.$row, @$ttl_1['A']<=0?'':@$ttl_1['A']);
			$sheet->setCellValue('D'.$row, @$ttl_1['B']<=0?'':@$ttl_1['B']);
			$sheet->setCellValue('E'.$row, @$ttl_1['C']<=0?'':@$ttl_1['C']);
			$sheet->setCellValue('F'.$row, @$ttl_1['D']<=0?'':@$ttl_1['D']);
			$sheet->setCellValue('G'.$row, '=SUM(C'.$row.':F'.$row.')');

			$sheet->cell('A'.$row.':H'.$row, function($cell) {
				$cell->setFontWeight('bold');
				$cell->setBackground('#CCCCCC'); 
			});	
		}//

		function ___CCC1(&$row, &$sheet, &$zone_ttl, $zon_cont, $kk1, $vv1, $stt)
		{

			$sheet->setCellValue('A'.$row, $vv1.'  STATUS : '.$stt);
			$sheet->cell('A'.$row, function($cell) {$cell->setFontWeight('bold');});			

			$row++;

			$ACTIVE_AGEING = $zon_cont;
			$my_acct       = @$ACTIVE_AGEING['acct_data'];
			
			$ttl_1 = array('A'=>0, 'B'=>0, 'C'=>0, 'D'=>0, 'E'=>0, 'bal'=>0);
			$cw1 = 0;
			foreach($ACTIVE_AGEING['result'] as $kk2 => $vv2)
			{
				$sheet->setCellValue('A'.$row, @$my_acct[$kk2]);
				$sheet->setCellValue('C'.$row, @$vv2['A']<=0?'':@$vv2['A']);
				$sheet->setCellValue('D'.$row, @$vv2['B']<=0?'':@$vv2['B']);
				$sheet->setCellValue('E'.$row, @$vv2['C']<=0?'':@$vv2['C']);
				$sheet->setCellValue('F'.$row, @$vv2['D']<=0?'':@$vv2['D']);
				$sheet->setCellValue('G'.$row, '=SUM(C'.$row.':F'.$row.')');
				
				$ttl_1['A']+= @$vv2['A'];
				$ttl_1['B']+= @$vv2['B'];
				$ttl_1['C']+= @$vv2['C'];
				$ttl_1['D']+= @$vv2['D'];
				$ttl_1['E']+= @$vv2['E'];
				$ttl_1['bal']+= @$vv2['bal'];

				$row++;
				$cw1++;
			}


			___CCC2($row, $sheet, $ttl_1, $vv1.' | '.$stt.' TOTAL : '.number_format($cw1,0));			

			$zone_ttl['A']   += @$ttl_1['A'];
			$zone_ttl['B']   += @$ttl_1['B'];
			$zone_ttl['C']   += @$ttl_1['C'];
			$zone_ttl['D']   += @$ttl_1['D'];
			$zone_ttl['E']   += @$ttl_1['E'];
			$zone_ttl['bal'] += @$ttl_1['bal'];


			$row++;
			$row++;
			
		}//		


		/******/
		Excel::load(public_path('excel02/ageing_list_90_4.xls'), 
		function($excel)use($all_params){

				$excel->sheet('Sheet1', function($sheet)use($all_params) {
					extract($all_params);
					
					$zone_lbl = '';
					if($zone > 0){
						$zone_lbl = '  -  '.get_zone101($zone);
					}

					$sheet->setCellValue('A5', 'As of '.date('F d, Y', strtotime($full_date)));//RES
					$row = 7;
					$col = 'A';

					$row++;

					// ___CCC1($row, $sheet,  $zon_cont);

					$grand_total = array('A'=>0, 'B'=>0, 'C'=>0, 'D'=>0, 'E'=>0, 'bal'=>0);

					foreach($zon_name as $kk1 => $vv1)
					{

						// $row++;

						$zone_ttl = array('A'=>0, 'B'=>0, 'C'=>0, 'D'=>0, 'E'=>0, 'bal'=>0);

						___CCC1($row, $sheet, $zone_ttl,  $zon_cont[$kk1]['active'], $kk1, $vv1, 'ACTIVE');
						___CCC1($row, $sheet, $zone_ttl,  $zon_cont[$kk1]['discon'], $kk1, $vv1, 'DISCONNECTED');
						___CCC2($row, $sheet, $zone_ttl, $vv1.' SUB TOTAL');

						$grand_total['A']   += @$zone_ttl['A'];
						$grand_total['B']   += @$zone_ttl['B'];
						$grand_total['C']   += @$zone_ttl['C'];
						$grand_total['D']   += @$zone_ttl['D'];
						$grand_total['E']   += @$zone_ttl['E'];
						$grand_total['bal'] += @$zone_ttl['bal'];

						$row++;
						$row++;
						$row++;

					}		

					___CCC2($row, $sheet, $grand_total, 'GRAND SUB TOTAL');
					$row++;
					$row++;
					$row++;
					$row++;
					$sheet->setCellValue('A'.$row, 'Prepared by:');
					$sheet->mergeCells('B'.$row.':D'.$row);
					$sheet->setCellValue('B'.$row, 'Noted:');

					$row++;
					// $sheet->setCellValue('A'.$row, 'JESSAMAE V. MILLAMA');
					$sheet->mergeCells('B'.$row.':D'.$row);
					// $sheet->setCellValue('B'.$row, 'ILDEFONSO C. ALBARRACIN');
					$row++;
					$sheet->setCellValue('A'.$row, 'Billing Officer');
					$sheet->mergeCells('B'.$row.':D'.$row);
					$sheet->setCellValue('B'.$row, 'General Manager D');

					$row++;
					$row++;



				});

		})->download('xls');
		
		return;
		return;
		return;

		/******/
		Excel::load(public_path('excel02/ageing_list_90_3.xls'), 
		function($excel)use($all_params){

				$excel->sheet('Sheet1', function($sheet)use($all_params) {
					extract($all_params);
					
					$zone_lbl = '';
					if($zone > 0){
						$zone_lbl = '  -  '.get_zone101($zone);
					}

					$sheet->setCellValue('A5', 'As of '.date('F d, Y', strtotime($full_date)).$zone_lbl);//RES
					
					$row = 12;
					$col = 'A';
					
					$ttl_amnt = 0;
					$less9    = 0;
					$frm9d_1y = 0;
					$frm1y_2y = 0;
					$frm2y_3y = 0;
					$frm3y_up = 0;
					
					$my_acct = @$ACTIVE_AGEING['acct_data'];

					foreach($ACTIVE_AGEING['result'] as $kk => $vv)
					{
						$sheet->setCellValue('A'.$row, @$my_acct[$kk]);
						$sheet->setCellValue('B'.$row, @$vv['bal']);
						$sheet->setCellValue('C'.$row, @$vv['A']<=0?'':@$vv['A']);
						$sheet->setCellValue('D'.$row, @$vv['B']<=0?'':@$vv['B']);
						$sheet->setCellValue('F'.$row, @$vv['C']<=0?'':@$vv['C']);
						$sheet->setCellValue('G'.$row, @$vv['D']<=0?'':@$vv['D']);
						$sheet->setCellValue('H'.$row, @$vv['E']<=0?'':@$vv['E']);
						
						$ttl_amnt += @$vv['bal'];
						$less9    += @$vv['A'];
						$frm9d_1y += @$vv['B'];
						$frm1y_2y += @$vv['C'];
						$frm2y_3y += @$vv['D'];
						$frm3y_up += @$vv['E'];						
						
						$row++;
					}
					
					$row++;
					$sheet->setCellValue('A'.$row, 'STATUS :  DISCONNECTED');
					$sheet->cell('A'.$row, function($cell) {
						$cell->setFontWeight('bold');
					});

					$row++;

					$my_acct = $DISCONNECTED_AGEING['acct_data'];
					foreach($DISCONNECTED_AGEING['result'] as $kk => $vv)
					{
						$sheet->setCellValue('A'.$row, @$my_acct[$kk]);
						$sheet->setCellValue('B'.$row, @$vv['bal']);
						$sheet->setCellValue('C'.$row, @$vv['A']<=0?'':@$vv['A']);
						$sheet->setCellValue('D'.$row, @$vv['B']<=0?'':@$vv['B']);
						$sheet->setCellValue('F'.$row, @$vv['C']<=0?'':@$vv['C']);
						$sheet->setCellValue('G'.$row, @$vv['D']<=0?'':@$vv['D']);
						$sheet->setCellValue('H'.$row, @$vv['E']<=0?'':@$vv['E']);
						
						$ttl_amnt += @$vv['bal'];
						$less9    += @$vv['A'];
						$frm9d_1y += @$vv['B'];
						$frm1y_2y += @$vv['C'];
						$frm2y_3y += @$vv['D'];
						$frm3y_up += @$vv['E'];
						
						$row++;
					}						
					

					$sheet->setCellValue('A'.$row, '---- END ---');
					$sheet->setCellValue('B'.$row, '');
					$sheet->setCellValue('C'.$row, '');
					$sheet->setCellValue('D'.$row, '');
					$sheet->setCellValue('F'.$row, '');
					$sheet->setCellValue('G'.$row, '');
					$sheet->setCellValue('H'.$row, '');
					$row++;
					$sheet->setCellValue('A'.$row, 'TOTAL');
					$sheet->setCellValue('B'.$row, $ttl_amnt);
					$sheet->setCellValue('C'.$row, $less9!=0?$less9:'');
					$sheet->setCellValue('D'.$row, $frm9d_1y!=0?$frm9d_1y:'');
					$sheet->setCellValue('F'.$row, $frm1y_2y!=0?$frm1y_2y:'');
					$sheet->setCellValue('G'.$row, $frm2y_3y!=0?$frm2y_3y:'');
					$sheet->setCellValue('H'.$row, $frm3y_up!=0?$frm3y_up:'');
					$row++;
					$row++;
					$row++;
					$row++;
					$sheet->setCellValue('A'.$row, 'Prepared by:');
					$sheet->mergeCells('B'.$row.':D'.$row);
					$sheet->setCellValue('B'.$row, 'Noted:');

					$row++;
					$sheet->setCellValue('A'.$row, 'JESSAMAE V. MILLAMA');
					$sheet->mergeCells('B'.$row.':D'.$row);
					$sheet->setCellValue('B'.$row, 'ILDEFONSO C. ALBARRACIN');
					$row++;
					$sheet->setCellValue('A'.$row, 'Billing Officer');
					$sheet->mergeCells('B'.$row.':D'.$row);
					$sheet->setCellValue('B'.$row, 'General Manager D');
					
					// $sheet->getColumnDimension('A')->setAutoSize(true);
					// $sheet->getColumnDimension('B')->setAutoSize(true);
					// $sheet->getColumnDimension('C')->setAutoSize(true);
					// $sheet->getColumnDimension('D')->setAutoSize(true);
					// $sheet->getColumnDimension('F')->setAutoSize(true);
					// $sheet->getColumnDimension('G')->setAutoSize(true);
					// $sheet->getColumnDimension('H')->setAutoSize(true);

				});

		})->download('xls');

	}	

	function ReportGetByZonePDF_before_jan_4_2021($zone, $month, $full_date)
	{
		

		$dat1 = $full_date;

		$new_arr = array(
					'm1' => '30 Days',
					'm2' => '60 Days',
					'm3' => '90 Days',
					'm4' => '120 Days',
					'm5' => '150 Days',
					'm6' => '180 Days',
					'y1' => '360 Days',
				   );

		$new_arr2 = array(
					 'm1' => '- 1 Month',
					 'm2' => '- 2 Month',
					 'm3' => '- 3 Month',
					 'm4' => '- 4 Month',
					 'm5' => '- 5 Month',
					 'm5' => '- 6 Month',
					 'y1' => '- 1 Year',
				    );


		$curr_date_label = date('F d, Y', strtotime($dat1));

		$v1_month1  = date('Y-m', strtotime($dat1));
		$v1_bill_start = date('Y-m-d',strtotime($v1_month1.'-'.$zone));
		$v1_bill_end   = date('Y-m-d',strtotime($dat1));
		$v1_bill_date_start = date('Y-m-d', strtotime($v1_bill_start.' '.@$new_arr2[$month]));

		$dat2 = date('Y-m-d', strtotime($dat1));



$period1_dd = date('Y-m-01', strtotime($dat1));
$read_period_data = ReadingPeriod::where('period', $period1_dd)->first();
$due_dates_raw = @$read_period_data->due_dates;
$due_dates = json_decode($due_dates_raw, true);

$m1 = $due_dates[$zone]; 
$m1 = str_replace('|','',$m1);
$m1 = explode('@',$m1);
$m1 = @$m1[1];

$has_current = false;


$t1 = strtotime($m1);//DUE DATE
$t2 = strtotime($dat2);//CURRENT DATE

if($t1 >= $t2){
	//~ $dat2 = date('Y-m-01', strtotime($dat2));
	//~ $dat2 = date('Y-m-d', strtotime($dat2.' - 1 day'));
}


//~ echo $dat2;
//~ die();

		
		
$zone = (int) $zone;		

$act1_active = DB::select("

	SELECT (L2.ttl_bal) TTL1, L2.ttl_bal,L2.date01, TAB1.* FROM
	(
		SELECT MAX(ledger_datas.id) as last_led,accounts.* from accounts
		LEFT JOIN
			ledger_datas ON ledger_datas.acct_id = accounts.id
		WHERE ledger_datas.date01 <= '$dat2'
		GROUP BY accounts.id
		ORDER BY
		  accounts.old_route ASC
	) as TAB1

	LEFT JOIN ledger_datas as L2
		ON L2.id = TAB1.last_led

	WHERE
		TAB1.zone_id = '$zone'
		AND TAB1.status != 'deleted'
		AND (TAB1.acct_status_key=2 || TAB1.acct_status_key=3 )
		AND L2.ttl_bal != 0

	ORDER BY TAB1.old_route ASC
	
");

//~ curr_bill_v1(3426, '2019-06-30', $zone);
//~ $period1_dd = date('Y-m-01', strtotime($dat1));

//~ echo  '<pre>';
//~ print_r($period1_dd);
//~ die();

//~ die();
//~ die();



		//~ $act1_discon = Accounts::where('zone_id', $zone)
						//~ ->where('status', '!=', 'deleted')
						//~ ->where('acct_status_key', '4')
						//~ ->where('zone_id', $zone)
						//~ ->whereHas('ledger_data4', function($query)use($dat1){
							//~ $query->where('date01', '<=', $dat1);
						//~ })
						//~ ->with('ledger_data4')
							//~ //->orderBy('route_id')
							//~ ->orderBy('old_route','asc')
								//~ ->get();

$act1_discon = DB::select("
	SELECT (L2.ttl_bal) TTL1, L2.ttl_bal,L2.date01, TAB1.* FROM
	(
		SELECT MAX(ledger_datas.id) as last_led,accounts.* from accounts
		LEFT JOIN
			ledger_datas ON ledger_datas.acct_id = accounts.id
		WHERE ledger_datas.date01 <= '$dat2'
		GROUP BY accounts.id
		ORDER BY
		  accounts.old_route ASC
	) as TAB1

	LEFT JOIN ledger_datas as L2
		ON L2.id = TAB1.last_led

	WHERE
		TAB1.zone_id = '$zone'
		AND TAB1.status != 'deleted'
		AND (TAB1.acct_status_key=4 || TAB1.acct_status_key=5 )
		AND L2.ttl_bal != 0

	ORDER BY TAB1.old_route ASC
");






		$new_xx = array();
		$stop1 = 0;
		foreach($new_arr as $kk=>$vv)
		//foreach($act1_active as $kk=>$vv)
		{
			if($kk == $month){$stop1 = 1;}
			$new_xx[$kk]=$vv;
			if($stop1 == 1)break;
		}

		//~ echo '<pre>';
		//~ print_r($new_xx);
		//~ die(0);

		$new_arr = $new_xx;

		$date1 = [];
		extract($date1);

		Fpdf::AddPage('P', 'Letter');
		Fpdf::SetMargins(6, 5, 5);
		Fpdf::SetFont('Courier',"B", 12);
		Fpdf::Ln();
		Fpdf::write(5, WD_NAME);
		Fpdf::Ln();
		Fpdf::write(4, WD_ADDRESS);
		Fpdf::Ln();
		Fpdf::write(4, 'Ageing of Account Recievable - Summary');
		Fpdf::Ln();
		Fpdf::write(4, 'As of '.@$curr_date_label);
		Fpdf::Ln();
		Fpdf::write(4, @$zone_lbl);
		Fpdf::Ln();


		$cell_height = 4;
		$extra1      = 5;

		$ind1 = 0;
		//$ind_max = 65;
		$ind_max = 47;

		$A3_ttl = 0;
		$A4_ttl = array(0,0,0,0,0,0,0);

		$SUM1_ttl = 0;
		$GRAND_TTL = 0;

		$zone_name = get_zone101($zone);
		$TTL_WITH_AGEING  = 0;
		$TTL_ALL_AGEING = 0;


		$dd11_raw = array(
					'k1',
					'v1',
					'dat1',
					'cell_height',
					'new_arr',
					'new_arr2',
					'ind1',
					'ind_max',
					'A3_ttl',
					'A4_ttl',
					'con_stat',
					'SUM1_ttl',
					'GRAND_TTL',
					'acct_stat11',
					'zone_name',
					'TTL_WITH_AGEING',
					'TTL_ALL_AGEING',
					'act1_active',
					'zone',
					'month',
					'extra1'
				);


		$con_stat = 'Active';
		ageing_head11($new_arr,$con_stat, array('is_first'=>true, 'extra1' => $extra1));

		//~ $act1_active
		//~ foreach($act1 as $k1 => $v1)
		foreach($act1_active as $k1 => $v1)
		{
			$ind1++;
			$TTL_ALL_AGEING++;
			$dd11 = compact($dd11_raw);
			$dd = ageing_body1($dd11);
			extract($dd);
		}//

		$acct_stat11 = 'Active';
		$dd11 = compact($dd11_raw);
		ageing_sub_total($dd11);


		$dd11 = compact($dd11_raw);
		ageing_foo1($new_arr, $dd11);


		Fpdf::AddPage('P', 'Letter');
		Fpdf::SetMargins(6, 5, 5);

		$con_stat = 'Disconnected';

		ageing_head11($new_arr, $con_stat,compact('curr_date_label', 'extra1'));


		$ind1 = 0;

		$SUM1_ttl = 0;

		foreach($act1_discon as $v1)
		{
			$ind1++;
			$TTL_ALL_AGEING++;
			$dd11 = compact($dd11_raw);
			$dd = ageing_body1($dd11);
			extract($dd);
		}//


		//$act1_discon

		$acct_stat11 = 'Disconnected';

		$dd11 = compact($dd11_raw);
		ageing_sub_total($dd11);

		$dd11 = compact($dd11_raw);
		ageing_foo1_final($new_arr, $dd11);
		ageing_foo1_signature();



		Fpdf::AliasNbPages();

		Fpdf::Output();
		exit;







	}//



	function ReportGetByZonePDF_OLD111($zone, $month, $full_date)
	{
		// $T1 = strtotime('TODAY');
		// $curr_period = date('Y-m-28');

		//$T1 = strtotime('TODAY');
		//$T1 = strtotime($period_year.'-'.$period_month);

		$T1 = strtotime($full_date);

		//$curr_period = date('Y-m-28');
		$curr_period = date('Y-m-01', $T1);
		$curr_date_label  =  date('l, F d, Y', $T1);

		$data1 = ageing_common(array('mm_arr', 'lab_days', 'zone_arr'));
		extract($data1);

		$zone_lbl = $zone_arr[$zone];

		$i = array_search($month, array_keys($mm_arr));
		$new_arr = array_slice($mm_arr, 0, ($i+1));
		$ageing_prev = array_slice($mm_arr,($i+1));


		if(empty($new_arr)){
			return;
		}

		$period_set = array();
		foreach($new_arr as  $mm){
			$T2 = strtotime($curr_period.' '.$mm);
			$period_set[] = date('Y-m-01', $T2);
		}


		// echo '<pre>';
		// print_r($period_set);
		// print_r($new_arr);
		// print_r($lab_days);
		// print_r($ageing_prev);
		// die();

		// $report_aging = Reports::whereRaw('billing_total != collected')
		// 				->orWhere('collected', null)
		// 				->where('period', $period_set[0])
		// 				->orderBy('full_name', 'asc')
		// 				->limit(100)
		// 				->get();

		//~ echo $curr_period;
		//~ die();

		$report_aging = Reports::where('rtype', 'ageing_of_accounts')
			->where('reports.period', $curr_period)
			->where('accounts.zone_id', $zone)
			->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
			//~ ->orderBy('reports.full_name', 'asc')
			->groupBy('reports.id')
			->addSelect(DB::raw('reports.*,
				accounts.zone_id as zone_orig,
				accounts.meter_number1
			'))
			//->limit(80)
			->get();


		if($report_aging->count() == 0)
		{

			$report_aging = Reports::where('rtype', 'ageing_of_accounts')
				->where('reports.period', $period_set[0])
				->where('accounts.zone_id', $zone)
				->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
				//~ ->orderBy('reports.full_name', 'asc')
				->groupBy('reports.id')
				->addSelect(DB::raw('reports.*,
					accounts.zone_id as zone_orig,
					accounts.meter_number1
				'))
				//->limit(80)
				->get();
		}


		$total_res = $report_aging->count();
		if($total_res == 0)
		{
			echo 'No reports for '.date('F Y', strtotime($curr_period));
			return;
		}

		 //~ echo '<pre>';
		 //~ print_r($report_aging->toArray());
		 //~ die();

		  //~ echo '<pre>';
		  //~ echo $total_res = $report_aging->count();
		  //~ print_r($report_aging[0]->toArray());
		  //~ exit();

		// $pdf_link = '/billing/report_get_by_zone/pdf/'.$zone.'/'.$month;
		// return view('billings.inc.billing_reports.pdf_aging1',  compact( 'report_aging'));
		// $pdf = PDF::loadView('billings.inc.billing_reports.pdf_aging1', compact( 'report_aging'));
		// return $pdf->stream('sales-view_'. date("Y-m-d") .'.pdf');

		$page_type = 'P';
		$per_page1 = 30;
		$per_page2 = 35;

		if($month == 'm5' || $month == 'm6' || $month == 'y1')
		{
			Fpdf::AddPage('L', 'Letter');
			$page_type = 'L';
			$per_page1 = 20;
			$per_page2 = 25;
		}else{
			Fpdf::AddPage('P', 'Letter');
		}

		Fpdf::SetMargins(5, 5, 5);
		Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Ln();
		Fpdf::Cell(100,4, WD_NAME,0,0,'L', false);
		Fpdf::Ln();
		Fpdf::write(4, WD_ADDRESS);
		Fpdf::Ln();
		Fpdf::write(4, 'Ageing of Recievable');
		Fpdf::Ln();
		Fpdf::write(4, 'As of '.$curr_date_label);
		Fpdf::Ln();
		Fpdf::write(4, $zone_lbl);

		//Fpdf::write(5, 'As of '.date('F d, Y'));

		Fpdf::Ln();

		Fpdf::SetFont('Courier',null, 7);

		$x = Fpdf::GetX();
		$y = Fpdf::GetY();
		$w = 20;
		$h = 2;

		$xy_arr = array(10, 40, 70,
					88, 105, 120,
					140, 155, 170);

		$w_sub  = array( 0,0,-10,
					  -15,-10,-10,
					  -15,-5,-15);

		$w_sub  = array( 0,0,0,
					  0,0,0,
					  0,0,0);

		RGBZPDF_Headers(compact(
			'x','y','w','h',
			'xy_arr','w_sub',
			'new_arr','month',
			'lab_days'
		));

		$x22=0;


		$grand_total = array();
		$sub_total = $lab_days;
		$current_sub = 0;
		$current_grand_total = 0;

		foreach($sub_total as $kk => $vv){
			$sub_total[$kk] = 0;
		}


		for($x22;$x22<=$per_page1;$x22++)
		{
			$rra1 = @$report_aging[$x22];
			if(!$rra1)
			{
				break;
			}

			$y = Fpdf::GetY();

			RGBZPDF_Content1(compact(
				'x','y','w','h',
				'xy_arr','w_sub',
				'rra1','new_arr','month', 'lab_days', 'ageing_prev'
			));

			$rra1->ageing_data;
			$age1 = (array) json_decode($rra1->ageing_data);

			foreach($sub_total as $kk => $vv)
			{
				$sub_total[$kk] += @$age1[$kk];
			}

			$current_sub += @$rra1->billing_total;

		}//

		 // echo '<pre>';
		 // print_r($sub_total);
		 // die();

		// RGBZPDF_Sub_total(compact(
		// 	'new_arr', 'sub_total', 'ageing_prev'
		// ));


		//$x=0;
		$per_page = $per_page2;
		$x23 = $per_page;
		for($x22;$x22<=$total_res;$x22++)
		{
			$rra1 = @$report_aging[$x22];
			if(!$rra1)
			{
				break;
			}

			if($x23>=$per_page)
			{

				$grand_total[] = $sub_total;
				RGBZPDF_Sub_total(compact(
					'new_arr', 'sub_total', 'ageing_prev', 'current_sub'
				));
				$current_grand_total+=$current_sub;
				$current_sub = 0;

				Fpdf::Ln();
				Fpdf::write(4, 'Page '.Fpdf::PageNo().' of {nb}');

				$sub_total = $lab_days;
				foreach($sub_total as $kk => $vv)
				{
					$sub_total[$kk] = 0;
				}

				Fpdf::AddPage($page_type, 'Letter');
				Fpdf::Ln();
				//Fpdf::Ln();
				//Fpdf::Ln();

				$x = Fpdf::GetX();
				$y = Fpdf::GetY();

				//$w = 30;
				//$h = 3;


				RGBZPDF_Headers(compact(
					'x','y','w','h',
					'xy_arr','w_sub',
					'new_arr','month',
					'lab_days'
				));

				$x23 = 0;
				//continue;
			}//


			$rra1 = @$report_aging[$x22];
			if(empty($rra1))
			{
				continue;
			}

			$y = Fpdf::GetY();

			RGBZPDF_Content1(compact(
				'x','y','w','h',
				'xy_arr','w_sub',
				'rra1','new_arr','month',
				'lab_days', 'ageing_prev'
			));

			$rra1->ageing_data;
			$age1 = (array) json_decode($rra1->ageing_data);

			foreach($sub_total as $kk => $vv)
			{
				$sub_total[$kk] += $age1[$kk];
			}

			$current_sub += @$rra1->billing_total;

			$x23++;

		}//


		$grand_total[] = $sub_total;

		$current_grand_total+=$current_sub;

		RGBZPDF_Sub_total(compact(
			'new_arr', 'sub_total', 'ageing_prev', 'current_sub'
		));

		$total_grand_1 = $grand_total[0];

		foreach($total_grand_1 as $kk => $vv){
			$total_grand_1[$kk] = 0;
		}

		foreach($grand_total as $vv1){
			foreach($vv1 as $kk=>$vv){
				$total_grand_1[$kk] += $vv;
			}
		}

		Fpdf::SetFont('Courier',"B", 8);
		RGBZPDF_Grand_total(compact(
			'new_arr', 'sub_total', 'ageing_prev', 'total_grand_1', 'current_grand_total'
		));

		Fpdf::Ln();

		Fpdf::write(4, 'Page '.Fpdf::PageNo().' of {nb}');

		// echo '<pre>';
		// print_r($total_grand_1);
		// die();

		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;



		echo '<pre>';
		print_r($report_aging->toArray());

	}//


	function ReportAddNew(Request $request)
	{
		extract($_POST);

		$remarks =  date('F Y', strtotime($period_year.'-'.$period_month.'-28')).' Reports';

		$has_report = HwdRequests::where('req_type', 'report_generate_request')
			->where('remarks','like', $remarks)
			->first();

		if($has_report)
		{
			$request->session()->flash('success', 'Failed to add report');
	 		return Redirect::to(URL::previous() . "#rm1");
		}

		 $new_req = new HwdRequests;
		 $new_req->req_type = 'report_generate_request';
		 $new_req->remarks = $remarks;
		 $new_req->status = 'pending';
		 $new_req->other_datas = json_encode($_POST);
		 $new_req->save();

		$request->session()->flash('success', 'Successfuly Added Report');
 		return Redirect::to(URL::previous() . "#rm1");

	}//End

	function ReportStartGenerate($rid, Request $request, $stat1='started'){
		$has_report = HwdRequests::where('req_type', 'report_generate_request')
			->where('id', $rid)
			->first();

		if(!$has_report)
		{
			$request->session()->flash('success', 'Failed to add report');
	 		return Redirect::to(URL::previous() . "#rm1");
		}

		$has_report->status = $stat1;
		$has_report->save();

		$request->session()->flash('success', 'Successfuly Started');
 		return Redirect::to(URL::previous() . "#rm1");
	}//End

	function ReportReGenerate($rid, Request $request)
	{
		return $this->ReportStartGenerate($rid, $request, 're-started');

	}//End

	function ReportGetBalances($zone, $full_date)
	{

		$dd_info = date_info1();

		$curr_period = date('Y-m-28', strtotime($full_date));
		//$curr_period = $dd_info['curr_period'];

		$acct_result = array();

		$result1 = Reports::where('rtype', 'ageing_of_accounts')
			->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
			->where('reports.period', $curr_period)
			->where('accounts.zone_id', $zone)
			->where(function($query){
				$query->whereRaw('reports.billing_total != reports.collected');
				$query->orWhere('reports.collected', null);
			})
			->where('accounts.zone_id', $zone)
			->orderBy('reports.full_name', 'asc')
			->groupBy('reports.id')
			->addSelect(DB::raw('reports.*,
				SUM(reports.billing_total) as total_balance,
				COUNT(reports.id) as acct_with_balance,
				accounts.zone_id as zone_orig,
				accounts.meter_number1
			'))
			//->limit(300)
			->get();

			// ->chunk(100, function($res1) use(&$acct_result){
			// 	$acct_result[] =  $res1->toArray();
			// });

			$acct_result[] =  $result1->toArray();
			$total_result  = $result1->count();

			$zones1  = Zones::where('status', '!=', 'deleted')->get();
			$zone_arr = array();
			foreach($zones1 as $zz)
			{
				$zone_arr[$zz->id] = $zz->zone_name;
			}
			$zone_lbl = $zone_arr[$zone];

			$pdf_link = '/billing/report_get_account_balances_pdf/'.$zone.'/'.$full_date;

			return view('billings.inc.billing_reports.ajax_balances1',compact(
				'acct_result',
				'total_result',
				'zone_lbl',
				'pdf_link'
			));


	}//End

	function ReportGetBalancesPDF($zone, $full_date)
	{

		$dd_info = date_info1();

		//$curr_period = $dd_info['curr_period'];
		$curr_period = date('Y-m-28', strtotime($full_date));

		$acct_result = array();

		$result1 = Reports::where('rtype', 'ageing_of_accounts')
			->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
			->where('reports.period', $curr_period)
			->where('accounts.zone_id', $zone)
			->where(function($query){
				$query->whereRaw('reports.billing_total != reports.collected');
				$query->orWhere('reports.collected', null);
			})
			->orderBy('reports.full_name', 'asc')
			->groupBy('reports.id')
			->addSelect(DB::raw('reports.*,
				SUM(reports.billing_total) as total_balance,
				COUNT(reports.id) as acct_with_balance,
				accounts.zone_id as zone_orig,
				accounts.meter_number1
			'))
			->get();

		$acct_result[] =  $result1->toArray();
		$total_result  = $result1->count();

		$zones1  = Zones::where('status', '!=', 'deleted')->get();
		$zone_arr = array();
		foreach($zones1 as $zz)
		{
			$zone_arr[$zz->id] = $zz->zone_name;
		}
		$zone_lbl = $zone_arr[$zone];


		$total_account_balance = 0;
		$total_bill = 0;

		$peso = 'Php ';

		/***************/
		/***************/
		/***************/

		$cel_sp = 3;
		$cel_wd = 30;
		$cel_height = 3;

	     Fpdf::AddPage('P', 'Letter');
	     Fpdf::SetMargins(6, 6, 6);
	     Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Ln();
	     Fpdf::Cell(75,4,WD_NAME,0,1,'L', false);
	     Fpdf::Cell(75,4,WD_ADDRESS,0,1,'L', false);
	     Fpdf::Cell(75,4,'Account Balances',0,1,'L', false);
	     Fpdf::Cell(75,4,'As of May 28, 2018',0,1,'L', false);
	     Fpdf::Cell(75,4,$zone_lbl,0,1,'L', false);

		Fpdf::SetFont('Courier',null, 7);


		RGB_report_head1($cel_sp, $cel_wd);

		$res11 = $result1->toArray();
		$cc = 0;

		$perpage1 = 55;
		$perpage2 = 65;

		$sub_total1 = array(0,0);

		foreach($res11 as $rr1)
		{
			$rr1 = (object) $rr1;

			$aging_data = (array) json_decode($rr1->ageing_data);

			$more_than_total = 0;
               foreach($aging_data as $kk=>$vv)
               {
                    $more_than_total+=$vv;
               }


			Fpdf::Cell($cel_wd,$cel_height,$rr1->account_num,0,0,'L', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd,$cel_height,$rr1->full_name,0,0,'L', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd,$cel_height,$rr1->meter_number1,0,0,'L', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd,$cel_height,'----',0,0,'L', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
			Fpdf::Cell($cel_wd-10,$cel_height, number_format($rr1->billing_total,2),0,0,'R', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);

			Fpdf::Cell($cel_wd-5,$cel_height, number_format($more_than_total, 2),0,0,'R', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd-15,$cel_height,'A',0,0,'C', false);
			Fpdf::Ln();

			// $total_account_balance += $rr1->total_balance;
			// $total_bill += $rr1->billing_total;

			$total_account_balance += $more_than_total;
			$total_bill += $rr1->billing_total;

			$cc++;

			$sub_total1[0] +=  $rr1->billing_total;
			// $sub_total1[1] +=  $rr1->total_balance;
			 $sub_total1[1] +=  $more_than_total;


			if($cc >= $perpage1)
			{
				break;
			}

		}
		Fpdf::Cell(0,2,'','B',0,'C', false);
		Fpdf::Ln();
		Fpdf::Ln();

		RGB_report_sub_total(compact('cel_wd','cel_height','sub_total1', 'cel_sp'));

		Fpdf::Cell(0,5,''.Fpdf::PageNo().' / {nb}',0,0,'L', false);


		Fpdf::AddPage('P', 'Letter');
		Fpdf::SetMargins(6, 6, 6);
		Fpdf::Ln();
		RGB_report_head1($cel_sp, $cel_wd);

		/**/
		$zz = 0;
		$cc = 0;

		$sub_total1 = array(0,0);

		foreach($res11 as $rr1)
		{
			if($cc <= $perpage1)
			{
				$cc++;
				continue;
			}

			$rr1 = (object) $rr1;

			$aging_data = (array) json_decode($rr1->ageing_data);

			$more_than_total = 0;
               foreach($aging_data as $kk=>$vv)
               {
                    $more_than_total+=$vv;
               }


			Fpdf::Cell($cel_wd,$cel_height,$rr1->account_num,0,0,'L', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd,$cel_height,$rr1->full_name,0,0,'L', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd,$cel_height,$rr1->meter_number1,0,0,'L', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd,$cel_height,'----',0,0,'L', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd  - 10,$cel_height, number_format($rr1->billing_total),0,0,'R', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     // Fpdf::Cell($cel_wd-5,$cel_height, number_format($rr1->total_balance, 2),0,0,'R', false);
			Fpdf::Cell($cel_wd-5,$cel_height, number_format($more_than_total, 2),0,0,'R', false);
		     Fpdf::Cell($cel_sp,$cel_height,'',0,0,'L', false);
		     Fpdf::Cell($cel_wd-15,$cel_height,'A',0,0,'C', false);
			Fpdf::Ln();

			// $total_account_balance += $rr1->total_balance;
			// $total_bill += $rr1->billing_total;

			$total_account_balance += $more_than_total;
			$total_bill += $rr1->billing_total;


			$sub_total1[0] +=  $rr1->billing_total;
			//$sub_total1[1] +=  $rr1->total_balance;
			$sub_total1[1] +=  $more_than_total;


			$cc++;
			$zz++;
			if($zz >= $perpage2)
			{


				Fpdf::Cell(0,2,'','T',0,'C', false);
				Fpdf::Ln();

				RGB_report_sub_total(compact('cel_wd','cel_height','sub_total1', 'cel_sp'));
				Fpdf::Ln();

				$sub_total1 = array(0,0);

				Fpdf::Cell(0,5,''.Fpdf::PageNo().' / {nb}',0,0,'L', false);
				$zz = 0;
				Fpdf::AddPage('P', 'Letter');
			     Fpdf::SetMargins(6, 6, 6);
				Fpdf::Ln();
				RGB_report_head1($cel_sp, $cel_wd);

			}

		}//
			/**/

			Fpdf::Cell(0,5,'','T',0,'C', false);
			Fpdf::Ln();
			RGB_report_sub_total(compact('cel_wd','cel_height','sub_total1', 'cel_sp'));
			Fpdf::Ln();
			Fpdf::Ln();

			Fpdf::SetFont('Courier',"B", 10);
			Fpdf::Cell(30,5,'Total Count :',0,0,'L', false);
			Fpdf::Cell(30,5, $result1->count(),0,0,'L', false);
			Fpdf::Cell(30,5, '',0,0,'L', false);
			Fpdf::Cell(32,5, '',0,0,'L', false);
			Fpdf::Cell(30,5, $peso.number_format($total_bill, 2),'B',0,'R', false);
			Fpdf::Cell($cel_sp ,5,'',0,0,'L', false);
			Fpdf::Cell(30,5, $peso.number_format($total_account_balance, 2),'B',0,'R', false);


			Fpdf::Ln();
			Fpdf::Ln();

			Fpdf::SetFont('Courier',null, 7);
			Fpdf::Cell(0,5,''.Fpdf::PageNo().' / {nb}',0,0,'L', false);


		     // Fpdf::AddPage();
		     // Fpdf::Cell(20,10,'Title',1,1,'C');

			Fpdf::AliasNbPages();
		     Fpdf::Output();
		     exit;
			/***************/
			/***************/
			/***************/

	}// End Sub

	function ReportGetBalancesMonthlyEnding_DATA($full_date)
	{

		$dd_info = date_info1();

		//$curr_period = $dd_info['curr_period'];
		$curr_period = date('Y-m-28', strtotime($full_date));

		$acct_result = array();

		$result1 = Reports::where('rtype', 'ageing_of_accounts')
			->where('period', $curr_period)
			->whereNotNull('billing_total')
			->whereNull('collected')
			->orWhere(function($query){
				$query->whereRaw('billing_total != collected');
			})
			//->whereRaw('billing_total != collected')
			//->orWhere('collected', null)
			//->orderBy('accounts.zone_id', 'asc')
			->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
			->groupBy('accounts.zone_id')
			->addSelect(DB::raw('reports.*,
				SUM(billing_total) as total_balance,
				COUNT(reports.id) as acct_with_balance,
				accounts.zone_id as zone_orig,
				accounts.meter_number1
			'))->get();

		// echo '<pre>';
		// print_r($result1->toArray());
		// die();

		$zones1  = Zones::where('status', '!=', 'deleted')->get();

		$zone_arr = array();
		foreach($zones1 as $zz)
		{
			$zone_arr[$zz->id] = $zz->zone_name;
		}

		return compact('result1', 'zone_arr');


		// $total_with_bal = 0;
		// $total_amount = 0;
		//
		// foreach($result1 as $rr1)
		// {
		// 	$total_with_bal+= 	$rr1->acct_with_balance;
		// 	$total_amount += $rr1->total_balance;
		// 	echo '<br />';
		// 	echo ''.$zone_arr[$rr1->zone_orig].'';
		// 	echo '<br />';
		// 	echo 'Account with balance: '.$rr1->acct_with_balance.'';
		// 	echo '<br />';
		// 	echo 'Amount : '.number_format($rr1->total_balance, 2).'';
		// 	echo '<br />';
		// 	echo '----------------------------';
		// 	echo '<br />';
		// 	echo '----------------------------';
		// 	echo '<br />';
		// 	echo '----------------------------';
		// 	echo '<br />';
		//
		// }
		//
		// echo 'All account with Balance :'.$total_with_bal;
		// echo '<br />';
		// echo 'Grand Total :'.$total_amount;

		// echo '<pre>';
		// print_r($zone_arr);
		// print_r($result1->toArray());
	}//End Sub


	function ReportGetBalancesMonthlyEnding($full_date)
	{

		$data1 = $this->ReportGetBalancesMonthlyEnding_DATA($full_date);
		extract($data1);

		$pdf_link = '/billing/report_get_account_monthly_ending_balance_pdf/'.$full_date;

		$display_date = date('F d, Y', strtotime($full_date));

		return view('billings.inc.billing_reports.ajax_balances1_monthly',
		compact('result1', 'zone_arr', 'pdf_link', 'display_date'));

	}//End Sub

	function ReportGetBalancesMonthlyEndingPDF($full_date)
	{

		$data1 = $this->ReportGetBalancesMonthlyEnding_DATA($full_date);
		extract($data1);
		//zone_orig
		//zone_arr

		// echo '<pre>';
		// print_r($result1->toArray());
		// die();
		$display_date = date('F d, Y', strtotime($full_date));


		Fpdf::AddPage('P', 'Letter');
	     Fpdf::SetMargins(6, 6, 6);
	     Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Ln();
	     Fpdf::Cell(75,4,WD_NAME,0,1,'L', false);
	     Fpdf::Cell(75,4,WD_ADDRESS,0,1,'L', false);
	     Fpdf::Cell(75,4,'Account Balances',0,1,'L', false);
	     Fpdf::Cell(75,4,'As of '.$display_date,0,1,'L', false);


		Fpdf::Ln();
		Fpdf::write(5, 'ROXAS');
		Fpdf::Ln();
		Fpdf::Cell(75,5,'',0,1,'L', false);
		Fpdf::Ln();
		//Fpdf::Cell(30,5,'Zone',0,0,'L', false);
		//Fpdf::Cell(30,5,"Account \nwith balance",0,0,'L', false);
		$x=Fpdf::GetX();
          $y=Fpdf::GetY();
		$w = 30;

		Fpdf::SetFont('Courier', null, 8);
		Fpdf::SetLeftMargin(50);

		Fpdf::MultiCell($w,3,'Zone','B', 'L');
		Fpdf::SetXY(40+50, $y-3);
		Fpdf::MultiCell($w,3,'Account with balance','B', 'C');
		Fpdf::SetXY(75+50, $y);
		Fpdf::MultiCell($w,3,'Amount','B', 'C');
		Fpdf::Ln();

		$w1 = 36;
		$w2 = 36;
		$w3 = 30;
		$ttl_balance = 0;
		$ttl_amount = 0;
		foreach($result1 as $rr1)
		{
			$ttl_balance += $rr1->acct_with_balance;
			$ttl_amount += $rr1->total_balance;

			Fpdf::Cell($w1,5,$zone_arr[$rr1->zone_orig],0,0,'L', false);
			Fpdf::Cell($w2,5, $rr1->acct_with_balance,0,0,'C', false);
			Fpdf::Cell($w3,5,'Php '.number_format($rr1->total_balance, 2),0,0,'C', false);
			Fpdf::Ln();
		}
		$x=Fpdf::GetX();
          $y=Fpdf::GetY();
		Fpdf::Line($x, $y, $x+110, $y);
		Fpdf::Ln();
		Fpdf::Cell($w1,5,'Grand Total ',0,0,'L', false);
		Fpdf::Cell($w2,5,''.$ttl_balance,0,0,'C', false);
		Fpdf::Cell($w3+1,5,'Php '.number_format($ttl_amount, 2),0,0,'C', false);


		//Fpdf::MultiCell(30,5,'Zone'.$x.'---'.$y,1, 'L');

		// Fpdf::Rect($x,$y,50,5);
		// $x=Fpdf::GetX() + 50;
		// Fpdf::SetXY($x+100,$y);
		// Fpdf::Rect($x,$y,50,5);
		// Fpdf::MultiCell(100,5,'Zone  Account with balance Amount',0, 'L');
		//Fpdf::Cell(30,5,'Amount',0,0,'L', false);


		Fpdf::Output();
		exit;



	}//End Sub




	function ReportGetDailyBillingAjax($zone, $full_date, $type1=''){

		$curr_period = date('Y-m-28', strtotime($full_date));
		$current_date_label = date('F d, Y', strtotime($full_date));

		$billing1  = BillingMdl::where('period', $curr_period)
					->whereNotNull('consumption')
					->with('account')
					->get();

		// echo '<pre>';
		// print_r($billing1->toArray());
		// die();


		$pdf_link = '/billing/report_get_report_acknowledgement_pdf/'.$zone.'/'.$full_date;
		return view('billings.inc.billing_reports.daily_billing_ajax',
		compact('pdf_link', 'billing1'));
	}









	static function 	___calculate_bill($reading1, $reading2, $type_key, $dis_key, $rate_info,  $discount_type){

				$prev_r = 0;
				$curr_r = (int) $reading1['curr_reading'];

				if(!empty($reading1['init_reading'])){
					$prev_r = (int) $reading1['init_reading'];
				}else{
					if(!empty($reading2)){
						$prev_r = (int) $reading2['curr_reading'];
					}
					//$curr_r = 0;
				}


				$consump =  $curr_r - $prev_r;
				$total_billing  = 0;
				$total_discount = 0;

 				$acct_type_key = $type_key;
				$acct_discount = $dis_key;

				if(!empty($rate_info[$acct_type_key])){
					$total_billing = BillingCtrl::__calculate_billing_rates($consump, $rate_info[$acct_type_key]);
				}

				if(!empty($discount_type[$acct_discount])){
					$total_discount =  $total_billing * ($discount_type[$acct_discount]['meta_value'] / 100)  ;
				}

				return array(
						'less_total' => $total_discount,
						'sub_total1' => $total_billing,
						'billing_total' => $total_billing - $total_discount,
				);

	}//End Private


	private  function 	___billingAccountGetData2($r_year=null, $r_month=null, $acct_num='none', $meter_num='none' , $lname='none', $zone='none')
	{

				$data1 = $this->__billingAccountExtraData();
				extract($data1);

				$curr_period =  date('Y-m', strtotime($r_year.'-'.$r_month));
				$prev_period =  date('Y-m', strtotime($curr_period.'  -1 month '));
				$prev_period2 =  date('Y-m', strtotime($curr_period.'  -2 month '));

				$billing_res_raw = BillingMdl::where('period', 'like', $curr_period.'%')
						->with(['reading_back1' =>function($query) use($curr_period){
									$query->where('period', 'like', $curr_period.'%');
							}, 'reading_back1.account1'])
						->with(['reading_back2' => function($query) use($prev_period){
									$query->where('period', 'like', $prev_period.'%');
							}])
						->with(['reading_back3' => function($query) use($prev_period2){
									$query->where('period', 'like', $prev_period2.'%');
							}]);

				$billing_res_raw->whereHas('reading_back1', function($query){
					$query->where('bill_stat', 'billed');
				});


				if($acct_num != 'none'){
					$billing_res_raw->whereHas('reading_back1.account1',
						function($query)use($acct_num){
							$query->where('acct_no', 'like', '%'.$acct_num.'');
					});
				}

				if($meter_num != 'none'){
					$billing_res_raw->whereHas('reading_back1.account1',
						function($query)use($meter_num){
							$query->where('meter_number1', 'like', '%'.$meter_num.'');
					});
				}

				if($lname != 'none'){
					$billing_res_raw->whereHas('reading_back1.account1',
						function($query)use($lname){
							$query->where('lname', 'like', $lname.'%');
					});
				}

				$billing_res  = $billing_res_raw
					->paginate(20)
					->toArray();

				//echo '<pre>';
				//print_r($billing_res);
				//die();

			/*******************************/
			/*******************************/

			foreach($billing_res['data'] as $kk => $vv){
						extract($vv);
						$bill_calc = $this->___calculate_bill(
									$reading_back1,
									$reading_back2,
									$reading_back1['account1']['acct_type_key'],
									$reading_back1['account1']['acct_discount'],
									$rate_info,
									$discount_type
								);
						extract($bill_calc);
						$vv['reading_back1']['less_total'] = $less_total;
						$vv['reading_back1']['sub_total1'] = $sub_total1;
						$vv['reading_back1']['billing_total'] = $billing_total;
						$vv['discount_info'] = null;

						if(@$discount_type[$reading_back1['account1']['acct_discount']]){
							$vv['discount_info'] = $discount_type[$reading_back1['account1']['acct_discount']];
						}

						$vv['reading_back1']['prev_reading'] = @$vv['reading_back2']['curr_reading'];

						if(!empty(@$vv['reading_back1']['init_reading'])){
							@$vv['reading_back1']['prev_reading'] = @$vv['reading_back1']['init_reading'];
						}

						$billing_res['data'][$kk]  = $vv;
			}//endofreach

			//echo '<pre>';
			//print_r($billing_res);
			//die();

			return $billing_res;
	}//END PRIVATE


	private function  __billingAccountExtraData()
	{

		$acct_type = AccountMetas::where('meta_type', 'account_type')
									->where('status', 'active')
									->orderBy('meta_name', 'asc')
									->get()
									->toArray();

		$bill_rates = BillingMeta::where('meta_type', 'billing_rates')
								->where('status', 'active')
								->orderBy('nsort', 'asc')
								->orderBy('id', 'asc')
								->paginate(300)
								->toArray();

		$zones  = Zones::where('status', '!=', 'deleted')
							->orderBy('zone_name', 'asc')
							->get()
							->toArray();

		$bill_discount = BillingMeta::where('meta_type', 'billing_discount')
								->where('status', 'active')
								->get()
								->toArray();

		$hw1_requests = HwdRequests::where('req_type','generate_billing_period_request')
								->orderBy('id', 'desc')
								->limit(30)
								->get()->toArray();


			$status_key_active_raw  =
						AccountMetas::where('meta_type', 'account_status')
							->where('meta_code', '1')
							->first();

			if($status_key_active_raw){
				$status_key_active = $status_key_active_raw->id;
			}else{
				$status_key_active = 0;
			}

		$active_acct_count = Accounts::where('acct_status_key', $status_key_active)
								->count();


		//$billed_acct  = BillingMdl::whereNotNull('billing_total')->count();
		$billed_acct  = Reading::where('bill_stat', 'billed')
			->where('period', date('Y-m-28'))
			->count();


			//BILLING RATES
			$rates_raw   = BillingMeta::where('meta_type', 'billing_rates')->where('status','active')->get()->toArray();
			$rate_info = array();
			foreach($rates_raw as $rww){
				$arr1 = (array) json_decode($rww['meta_data']);
				@$rate_info[$arr1['acct_type']][] = $arr1;
			}

			//~ echo '<pre>';
			//~ print_r($rate_info);
			//~ die();

			//BILLING RATES

			$bill_discount =
						BillingMeta::where('meta_type', 'billing_discount')
							->where('status', 'active')
							->get()
							->toArray();

			$discount_type = array();
			foreach($bill_discount as $bdd){
				$discount_type[$bdd['id']] = $bdd;
			}



		return  compact('acct_type',  'bill_rates', 'zones', 'bill_discount', 'hw1_requests', 'rate_info', 'discount_type', 'active_acct_count', 'billed_acct');
	}


	public static  function  __calculate_billing_rates($consumption, $acct_type_set, $meter_size_id=0)
	{


		foreach($acct_type_set as $ats)
		{

			$min1 = $ats['min_cu'];
			$max1 = $ats['max_cu'];

			//To fix the zero
			if($consumption <= 0)
			{
				$consumption=1;
			}

			 if(($min1 <= $consumption) && ($consumption <= $max1))
			 {

				$kk1 = array_search($meter_size_id, $ats['meta_id']);

				 $price_rate = @$ats['price_rate'][$kk1];
				 $min_charge = @$ats['min_charge'][$kk1];

				//  echo '<pre>';
				//  echo $min_charge;
				//  echo '<br />';
				//  echo $price_rate;
				//  echo '<br />';
				//  echo $min1;
				//  die();

				//  if($price_rate == 0)
				if(empty($price_rate))
				{
					 return $min_charge;
				 }else{
					 return ((($consumption - $min1) * $price_rate)
								+ $min_charge) + $price_rate;
				 }
			 }

		}

		return 0;
	}


	private function __get_billing_request_data(){

		$hw1_requests = HwdRequests::where('req_type','generate_billing_period_request')
						->get()->toArray();

		return $hw1_requests;
	}


	/*************/
	/*************/
	/*************/
	/*************/
	/*************/
	/*************/


	function RequestReProcess($req_id, Request $request)
	{
		$req001 = HwdRequests::find($req_id);

		// echo '<pre>';
		// print_r($req001->toArray());
		// die();

		if(!$req001){
			//Do Nothing
		}else{

			if(empty($req001->dkey1)){
				$ot_dta = json_decode($req001->other_datas);
				$period = date('Y-m-28', strtotime($ot_dta->period_year.'-'.$ot_dta->period_month));
				$req001->dkey1 = $period;
			}

			$req001->status = 'ongoing';
			$req001->save();
		}

		$request->session()->flash('success', 'Billing Request Process');
		return Redirect::to(URL::previous() . "#period_request");
	}







	/**************/
	/**************/

	function ZoneNew(Request $request){
		extract($_POST);
		
		//~ echo '<pre>';
		//~ print_r($_POST);
		//~ die();

		$new_item = new Zones;
		$new_item->zone_name = $name;
		$new_item->zone_code = $code;
		$new_item->zone_desc = $descr;
		$new_item->status = $status;
		$new_item->bill_nth = (int) @$bill_date;
		$new_item->save();

		$request->session()->flash('success', 'Zone Added');
		return Redirect::to(URL::previous() . "#zones");

		echo '<pre>';
		print_r($_POST);
		echo '<pre>';
	}

	function ZoneUpdate(Request $request){
		extract($_POST);
		
		//~ echo '<pre>';
		//~ print_r($_POST);
		//~ die();

		$new_meta = Zones::where('id', $id)->first();
		$new_meta->zone_name	 = $name;
		$new_meta->zone_code	 = $code;
		$new_meta->zone_desc	 = $descr;
		$new_meta->status		 = $status;
		$new_meta->bill_nth		 = (int) @$bill_date;
		$new_meta->save();

		$request->session()->flash('success', 'Zone Updated');
		return Redirect::to(URL::previous() . "#zones");
	}

	function ZoneDelete(Request $request){
	}



	function execut_billing_10011($zone_id, $period, Request $request)
	{
		$per1 = date('Y-m', strtotime($period));

		$R1 = Reading::where('period','like', $per1.'%');

		$R1->where(function($q1){
				$q1->where('current_consump', '!=', '');
				$q1->whereNotNull('curr_reading');
				$q1->orWhere('curr_reading', 0);
			});

		$R1->where('zone_id', $zone_id);

		$rate_version = BillingRateVersion::orderBy('id', 'desc')
			->first();

		$data11 = ServiceCtrl::___rates_and_discounts();
		extract($data11);

		$acct_type = array();
		$acctype1 = AccountMetas::where('meta_type', 'account_type')->get();

		foreach($acctype1 as $att)
		{
			@$acct_type[$att->id] = $att->toArray();
		}

        $data11['acct_type'] = $acct_type;


        $reading1 = $R1->get();

		//Isa isahin ang reading
		foreach($reading1 as $rr)
		{
              ServiceCtrl::proccess_arrears($rr, $per1);
              ServiceCtrl::proccess_billing($rr, $per1, $data11);
		}//endforeach


		$request->session()->flash('success', 'Billing Executed');
		return Redirect::to(URL::previous() . "");
	}



	function check_meter_available()
	{
		$meter_num = @$_GET['mtr'];
		$acct_1 = Accounts::where('status', 'active')
					->where('meter_number1', 'like', '%'.$meter_num.'%')
					->limit(1)
					->first();
		if( !$acct_1 )
		{
			return ['status' => 0, 'msg' => 'Meter # not found'];
		}	

		return ['status' => 1, 'msg' => 'Found 1 record', 'data' => $acct_1];

		// echo '<pre>';
		// print_r($acct_1->toArray());
	}//



}
