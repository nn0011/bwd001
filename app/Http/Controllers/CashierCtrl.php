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
use App\Collection;
use App\Invoice;
use App\User;
use App\OtherPayable;
use App\Bank;
use App\LedgerData;
use App\report1;
use App\HwdLedger;
use App\Role;
use App\TempCollection;
use App\CollUpload;
use App\Banks;
use App\ReadingPeriod;
use App\BillingDue;
use App\BillingNw;
use App\PayPenLed;
use App\CollectLedger;
use App\RemoteGenarate;


use App\Services\Collections\CollectionService;
use App\Services\Collections\CollectionReportService;




use App\Http\Controllers\HwdLedgerCtrl;
use App\Http\Controllers\LedgerCtrl;
use App\Http\Controllers\HwdJobCtrl;


use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Fpdf;
use PDF;
use Excel;
use NWBill;

class CashierCtrl extends Controller
{
	
	function load_collection_to_server($upid)
	{
		
		$data_upload = CollUpload::where('id', $upid)->first();	
		if($data_upload->status == 'loaded'){
			return array('status'=>0, 'msg'=>'No collection to import 1');
		}

		$temp_coll = TempCollection::whereDoesntHave('my_collection')->get();

		// echo '<pre>';
		// print_r($temp_coll);
		// die();

		$data_upload->status = 'loaded';
		$data_upload->save();

		

		if($temp_coll->count() == 0){
			return array('status'=>0, 'msg'=>'No collection to import 2');
		}
		
		$coll_insert_arr = array();

		
		foreach($temp_coll as $col1)
		{
			$new_ar = $col1->toArray();
			
			unset($new_ar['id']);
			unset($new_ar['upload_id']);
			unset($new_ar['up_fname']);
			
			$new_ar['rem_id']   = $col1->id;
			$new_ar['rem_stat'] = 'remote';

			//~ $coll_insert_arr[] = $new_ar;			
			
			$col_id = Collection::insertGetId($new_ar);

			/*
			 * 
			 * 
			 * 
			 * */
			 
			$acct1  = Accounts::find($col1->cust_id); 
			 
			$payment = $col1->payment;
			 
			$last_coll  = LedgerData::where('acct_id', $col1->cust_id)
							->where('status', 'active')
								->orderBy('id', 'desc')
									->first();
			$ttl_bal = 0;
			$orig_ttl_bal = 0;

			if($last_coll){
				$ttl_bal = $last_coll->ttl_bal;
				$orig_ttl_bal = $last_coll->ttl_bal;
			}
			
			
			
			$led_type = '';
			$ledger_info = '';
			
			if($col1->status == 'active'){
				$led_type = 'payment';
				$ledger_info = 'Bill Payment - Remote';
				$ttl_bal -= $payment;
			}

			if($col1->status == 'collector_receipt'){
				$led_type = 'payment_cr';
				$ledger_info = 'Bill Payment - Remote';
				$ttl_bal -= $payment;
			}

			if($col1->status == 'cr_nw'){
				$led_type = 'cr_nw';
				$ledger_info = 'Non-Water Bill Payment - Remote';
				$ttl_bal -= $payment;
			}

			if($col1->status == 'or_nw'){
				$led_type = 'or_nw';
				$ledger_info = 'Non-Water Bill Payment - Remote';
				$ttl_bal -= $payment;
			}
			
			
			if($col1->status == 'cancel_cr'){
				$led_type = 'cancel_cr';
				$ledger_info = 'Cancel Payment';
				$ttl_bal += $payment;
				$payment = $payment * -1;
			}

			if($col1->status == 'cancel_receipt'){
				$led_type = 'payment_cancel';
				$ledger_info = 'Cancel Payment';
				$ttl_bal += $payment;
				$payment = $payment * -1;
			}

			
			if($col1->status == 'nw_cancel'){
				$led_type = 'nw_cancel';
				$ledger_info = 'Cancel Payment';
				$ttl_bal += $payment;
				$payment = $payment * -1;
			}


			
			$led_insert = array(
					'led_type' => $led_type,
					'acct_id' => $col1->cust_id,
					'ttl_bal' => $ttl_bal,
					'payment' => $payment,
					'ledger_info' => $ledger_info,
					'status' => 'active',
					'acct_num' => $acct1->acct_no,
					'date01' => $col1->payment_date,
					'period' => date('Y-m-01', strtotime($col1->payment_date)),
					'reff_no' => $col1->invoice_num,
					'coll_id' => $col_id,
					'created_at'=>$new_ar['created_at'],
					'updated_at'=>$new_ar['updated_at']
			);

			// EXECUTE THE NON-BILL-DEBIT
			// EXECUTE THE NON-BILL-DEBIT
			if($col1->status == 'or_nw')
			{

				// $ttl_bal -= $payment;

				// HAS A BALANCES
				$new_payment = $payment;
				$coll_info = json_decode( $col1->coll_info,  true );

				if( @$coll_info['payed'][0]['pre_val'] != @$coll_info['payed'][0]['val'] ) 
				{
					$new_payment = @$coll_info['payed'][0]['pre_val'];
				}



				$debit_led_insert =  $led_insert;
				$debit_led_insert['payment'] = 0;
				$debit_led_insert['ledger_info'] = 'Non-Water Bill Debit - Remote';
				$debit_led_insert['bill_adj'] = (abs($new_payment) * -1);
				$debit_led_insert['led_type']  = 'or_nw_debit';
				$debit_led_insert['ttl_bal']  = $orig_ttl_bal + (abs($new_payment) );
				
				LedgerData::insert($debit_led_insert);	

				$led_insert['ttl_bal'] = $debit_led_insert['ttl_bal'] - (abs($payment) );
			}			
			// EXECUTE THE NON-BILL-DEBIT
			// EXECUTE THE NON-BILL-DEBIT
			

			LedgerData::insert($led_insert);			
		}//

		//~ print_r($coll_insert_arr);
		//~ print_r($new_ar);
		//~ print_r($col1->toArray());
		//~ die();		
		
		//~ $new_ledger_data = new LedgerData;
		//~ $new_ledger_data->led_type = $led_type;
		//~ $new_ledger_data->acct_id =$acct_id;
		//~ $new_ledger_data->ttl_bal = $ttl_bal;
		//~ $new_ledger_data->payment = $payment_made;
		//~ $new_ledger_data->ledger_info = 'Bill Payment '.strtoupper($method);
		//~ $new_ledger_data->status = 'active';
		//~ $new_ledger_data->acct_num = $acct11->acct_no;
		//~ $new_ledger_data->date01 = $payment_date.date(' H:i:s');
		//~ $new_ledger_data->period = date('Y-m-').'01';
		//~ $new_ledger_data->reff_no = $new_col->invoice_num;
		//~ $new_ledger_data->coll_id = $new_col->id;
		//~ $new_ledger_data->save();		
		
		
		//~ if($temp_coll->count() == 0){
		return array('status'=>1, 'msg'=>$temp_coll->count().' collection imported');
		//~ }
	}
	
	function delete_this_upload($upid)
	{
		$data_upload = CollUpload::where('status', '!=', 'deleted')
							->where('id', $upid)
									->first();
									
		if(!$data_upload){return false;}
		$data_upload->status = 'deleted';
		$data_upload->save();
	}
	
	function get_collection_uploaded_html1()
	{
		
		$data_upload = CollUpload::where('status', '!=', 'deleted')
					->orderBy('id', 'desc')
						->limit(20)
							->get();
		$html = '
			<table class="tab002" cellspacing="0" cellpadding="0">
					<tr class="head1">
						<td>File Code</td>
						<td>Date</td>
						<td>Status</td>
						<td>&nbsp;</td>
					</tr>
			
		
		';
		if($data_upload->count() == 0)
		{
			$html .= '
				<tr class="body1">
					<td colspan="4">Upload data not found</td>
				</tr>
				';
		}else{
			
			foreach($data_upload as $dd1)
			{
				
				$str11 = '
						<td class="act1"> <small ondblclick="load_collection_to_server('.$dd1->id.')">LOAD DATA</small> |  <small ondblclick="delete_this_upload('.$dd1->id.')">Delete</small></td>
				';
				
				if($dd1->status == 'loaded'){
					$str11 = '<td class="act1">&nbsp;</td>';
				} 
				
				$html.='
					<tr class="body1">
						<td>'.$dd1->file_name.'</td>
						<td>'.date('F d, Y @ H:i:s', strtotime($dd1->created_at)).'</td>
						<td>'.$dd1->status.'</td>
						'.$str11.'
					</tr>				
				';
			}
		}
		$html .= '</table>';
		
		return array('status'=>1, 'msg'=> 'Data Retrieved', 'html1'=>$html);
		
	}
	
	function load_remote_collection_100010()
	{
		
		if(empty(@$_FILES['file']))
		{
			return array('status'=> '0', 'msg'=> 'File not uploaded');
		}
		
		if(@$_FILES['file']['type'] != 'application/json')
		{
			return array('status'=> '0', 'msg'=> 'Invalid File');
		}
		
		//~ echo '<pre>';
		//~ print_r($_FILES);
		//~ die();

		$time1 = time();
		//~ $file_name = time();
		$tmp_name = $_FILES["file"]["tmp_name"];
		$new_file_name = $time1.'-'.$_FILES["file"]["name"];
		$ff1 = 'upload1/'.$new_file_name;

		$f2  = fopen($tmp_name,'r');
		$jsd = fgets($f2, 64000000);
		fclose($f2);
		
		$my_data = json_decode($jsd, true);
		
		
		
		if(!$my_data){
			return array('status'=> '0', 'msg'=> 'Invalid File');
		}
		
		if(empty($my_data[0])){
			return array('status'=> '0', 'msg'=> 'Invalid Data');
		}
		
		if(empty($my_data[0]['collector_id'])){
			return array('status'=> '0', 'msg'=> 'Invalid Collector ID');
		}
		if(empty($my_data[0]['invoice_num'])){
			return array('status'=> '0', 'msg'=> 'Invalid Invoice #');
		}
		if(empty($my_data[0]['payment'])){
			return array('status'=> '0', 'msg'=> 'Invalid Payment');
		}
		
		$coll1 = User::find($my_data[0]['collector_id']);
		

		if(!$coll1){
			return array('status'=> '0', 'msg'=> 'Invalid Collector ID');
		}
		
		
		move_uploaded_file($tmp_name, $ff1);
		sleep(1);
		
		$n1 = new CollUpload;
		$n1->file_name = $new_file_name;
		$n1->file_name_code = $new_file_name;
		$n1->status = 'pending';
		$n1->save();
		
		
		$pre_insert_arr = array();
		
		//~ echo '<pre>';
		//~ print_r($my_data);
		//~ die();
		
		
		foreach($my_data  as $mm1)
		{
			$mm1 = (object) $mm1;
			
			
			//~ echo '<pre>';
			//~ print_r($mm1);
			//~ die();
			
			$hasOne = TempCollection::find($mm1->id);
			if(!$hasOne)
			{
				$mm1->upload_id = $n1->id;
				$mm1->up_fname = $new_file_name;
				$pre_insert_arr[] = (array) $mm1;
			}
		}
		
		if(!empty($pre_insert_arr))
		{
			TempCollection::insert($pre_insert_arr);
		}
		
		
		
		
	}//
	
	function create_remote_collection_session($date1)
	{

		$my_collector = @$_GET['my_collector'];
		$my_zones	  = @$_GET['my_zones'];

		$acct1 = Accounts::whereIn('zone_id', $my_zones)
					->whereDoesntHave('remote_generate')
					->orderBy('zone_id')
					->orderBy('route_id');

		$details  = '<h1>'.$acct1->count().' Accounts remainings </h1>';

		$acct1 = $acct1->limit(50)
						->get();


		$get_ledger_data = function($max_aid, $acct_id) 
		{
			if( empty($max_aid)  || $max_aid <= 0){
				return [];
			}

			if( !$max_aid ){
				return [];
			}

			return LedgerData::where('id','>=', $max_aid)
						->where('status', 'active')
						->where('acct_id', $acct_id)
						->get()
						->toArray();
		};


		$get_aid_max = function($aid) {

				return  LedgerData::where('acct_id', $aid)
					->where('status', 'active')
					->whereIn('led_type', ['billing'])
					->whereRaw('
						Exists(
							select * from `arrears` 
							WHERE 
								`ledger_datas`.`period` = `arrears`.`period` 
							AND
								`ledger_datas`.`acct_id` = `arrears`.`acct_id` 
							AND
								`amount` <= 0
						)
					')
					->max('id');

		};


		foreach($acct1 as $nn)
		{
			$nn->ledger = $get_ledger_data($get_aid_max($nn->id), $nn->id);
			
			if( empty($nn->ledger) )
			{
				$nn->ledger = LedgerData::where('status', 'active')
						->where('acct_id', $nn->id)
						->get()
						->toArray();
			}
		}

		$is_no_more = true;

		foreach($acct1 as $nn)
		{
			$is_no_more = false;
			RemoteGenarate::insert([
				'acct_id' => $nn->id,
				'date01' => date('Y-m-d'),
				'acct_data' => $nn->toJson(),
			]);		
		}//

		if($is_no_more == false)
		{

			echo '
				<h1>..PROCESSING <br /> Please Wait..</h1> <br />
				'.$details.'
				<script>
				setTimeout(()=>{
					window.location.reload();
				},1000);
				</script>
			';

			die();
		}





		$acct222 = RemoteGenarate::where('date01', date('Y-m-d'))->get();


		/**/ 
		$reading_period = ReadingPeriod::get();
		$banks   = Bank::get();
		$zon1    = Zones::whereIn('id', $my_zones)->get();
		$new_acct01 = [];

		foreach($acct222 as $aa)
		{
			$new_acct01[] = json_decode($aa->acct_data);
		}		

		// echo '<pre>';
		// print_r($new_acct01);
		// die();


		// echo 'AAAA';
		// echo '<pre>';
		// print_r($new_acct01);
		// die();

		$collect_list_raw = User::whereHas('roles', function($q){
					$q->where('roles.id', 4);
				})->pluck('id');

		$collect_list = implode(', ', $collect_list_raw->toArray());

		$my_user = DB::select("SELECT * FROM users WHERE id IN ($collect_list) ");		

		$other_payables = OtherPayable::where('paya_stat', 'active')->get();

		// echo '<pre>';
		// print_r($my_user);
		// die();
		// die();
		// die();

		return $res1 = array(
			'zone'=>$zon1->toArray(),
			'collector'=> $my_user, 
			'accounts'=>$new_acct01,
			'banks' => $banks->toArray(),
			'reading_period' => $reading_period->toArray(),
			'other_payables' => $other_payables->toArray()
		);



		echo '<pre>';
		print_r($acct1->toArray());
		die();
		die();
		die();
		die();
		die();
		die();
		die();

		//~ $date1 = date('Y-m-d');
		
		$my_collector = @$_GET['my_collector'];
		$my_zones	  = @$_GET['my_zones'];
		
		$col1 = User::find($my_collector);
		$zon1 = Zones::whereIn('id', $my_zones)
							->get();
		

		$my_user = DB::select("SELECT * FROM users WHERE id=?",[$my_collector]);					
		
		
		$acct1 = Accounts::whereIn('zone_id', $my_zones)
								->with(['ledger_data3' => function($q){
										$q->where('status', 'active');
									}])
								//~ ->with(['ledger_data2' => function($q){
											//~ $my_per = date('Y-m');
											//~ $q->where('date01','like', $my_per.'%');
											//~ $q->where('status', 'active');
											//~ $q->orderBy('id', 'asc');
										//~ }])
									->orderBy('zone_id')
										->orderBy('route_id')
											->get();
											
		
		//~ echo '<pre>';
		//~ print_r($my_zones);
		//~ die();
		
		$banks = Bank::get();
		
										
		foreach($acct1 as $aa)
		{
			$ledger_data2 = LedgerData::where('status', 'active')
								->where('acct_id', $aa->id)
									->orderBy('date01', 'desc')
									->orderBy('zort1', 'desc')
									->orderBy('id', 'desc')
										->limit(5)
											->get()
												->toArray();
												
			$aa->ledger_data2 = $ledger_data2;			
		}//
		

		$res1 = array(
						'zone'=>$zon1->toArray(),
						'collector'=>$my_user[0], 
						'accounts'=>$acct1->toArray(),
						'banks' => $banks->toArray()
					);
		
		return response()->json($res1);
		
		echo '<pre>';
		print_r($res1);
		//~ print_r($zon1->toArray());
		//~ print_r($col1->toArray());
		//~ print_r($acct1->toArray());
	}//

	function generate_montly_report_102_reset($date01)
	{
		$date1 = date('Y-m', strtotime($date01));

		$sql1 = "
			SELECT  *
			FROM report1s
			where dpaydate like '$date1%'		
		";

		DB::table('report1s')->where('dpaydate', 'like', "$date1%")->delete();


		echo '<script>alert("Done Removed.");window.location.href="/generate_montly_report_102_check/'.$date1.'-01";</script>';


	}//
	
	function generate_montly_report_102_check($date01)
	{
		$date1 = date('Y-m', strtotime($date01));

		$sql1 = "
					SELECT 
						CC.*, 
						AA.acct_no, AA.acct_status_key, AA.acct_type_key, AA.acct_discount,AA.zone_id
					FROM collections CC

					LEFT JOIN accounts as AA ON AA.id=CC.cust_id

					WHERE CC.id IN (
						SELECT MAX(id) FROM collections
						WHERE payment_date like '$date1%'
						GROUP BY invoice_num
					)
					AND CC.id NOT IN(
							#####
							#####
							SELECT id FROM collections
							WHERE id IN (
								SELECT max(id) FROM collections
								WHERE invoice_num IN (
									SELECT invoice_num FROM collections
									where payment_date like '$date1%'
									AND status like '%cancel%'
								)
								GROUP BY invoice_num
							)
							AND status like '%cancel%'
							#####
							#####
					)

					AND CC.payment_date like '$date1%'
					### AND CC.col_id NOT IN( )
					AND NOT EXISTS(
						SELECT * FROM report1s WHERE coll_id=CC.id LIMIT 1
					)
				
					LIMIT 100
		";


		$coll1 = DB::select($sql1);
		$sql1 = str_replace("AA.acct_discount,AA.zone_id", " AA.acct_discount,AA.zone_id, COUNT(*) TT1 ", $sql1);
		$coll2 = DB::select($sql1);

		// ee($coll2, __FILE__, __LINE__);
		// ee($coll1, __FILE__, __LINE__);

		if( empty($coll1) ) {
			echo '--- DONE EXECUTE --- ';
			die();
		}


		/**/
		$batch_insert = [];
		foreach( $coll1 as $k => $v  ) 
		{

			$json_rs1 = json_decode($v->coll_info, 1);
			$v->jj = $json_rs1;

			$break_dd = $break_dd2 = CollectionService::coll_report_breakdown($v);

			// ee($v->payment, __FILE__, __LINE__);
			unset($break_dd2['amt']);
			unset($break_dd2['bil']);



			$period1 = date('Y-m-01',strtotime($v->payment_date));

			if( in_array($v->status, ['or_nw', 'cr_nw']) ) 
			{
				$break_dd['cur'] = 0;
				$break_dd['cy'] = 0;
				$break_dd['py'] = 0;
				$break_dd['pen'] = 0;
				$break_dd['nwb'] = $v->payment;

				$break_dd2['cur'] = 0;
				$break_dd2['cy'] = 0;
				$break_dd2['py'] = 0;
				$break_dd2['pen'] = 0;				
				$break_dd2['nwb'] = $v->payment;			

			}

			$ttl_diff1  = $v->payment- array_sum($break_dd2);
			if( $v->payment > array_sum($break_dd2)) 
			{
				$break_dd['cur'] = $break_dd2['cur'] = $break_dd2['cur'] + $ttl_diff1;
			}

			// ee1($break_dd2, __FILE__, __LINE__);
			// ee($v, __FILE__, __LINE__);

			#############
			#############
			if ( $v->payment.'' !=  array_sum($break_dd2).'' ) 
			{
				echo 'ERROR ON BREAKDOWN PLEASE REPAIR';
				echo '<br />';
				echo array_sum($break_dd2);
				echo '<br />';
				echo $v->payment;
				echo '<br />';
				ee1($break_dd2, __FILE__, __LINE__);
				ee1($v, __FILE__, __LINE__);

				if( !empty($batch_insert) ) {
					report1::insert($batch_insert);
				}
				die();
			}
			#############
			#############



			$batch_insert[]   = [
				'rep_type'    => 'collection_report_1',
				'reff_1'      => $v->invoice_num,
				'reff_2'      => $v->status,
				'acct_id'     => $v->cust_id,
				'coll_id'     => $v->id,
				'acct_stat'   => $v->acct_status_key,
				'acct_type'   => $v->acct_type_key,
				'fcollected'  => $v->payment,
				'ftax'   	  => $v->tax_val,
				'fdis'   	  => 0,
				'fadjust'     => 0,
				'dperiod'  	  => $period1,
				'dpaydate'    => $v->payment_date,
				'zone_id'  	  => $v->zone_id,
				'fcurrent'    => @$break_dd['cur'],
				'farrear'  	  => @$break_dd['cy'],
				'fnon_wat'    => @$break_dd['nwb'],
				'fprv_arr'    => @$break_dd['py'],
				'penalty'  	  => @$break_dd['pen'],
			];


		}// END FOR-EACH

		//
		report1::insert($batch_insert);
		/**/



		

		// ee($batch_insert, __FILE__, __LINE__);
		echo '
			PROCESSING..... <br />
			Please wait until the number reach zero.
			<br />'.@$coll2[0]->TT1.'

			<script>
			setTimeout(function(){
				window.location.reload();
			}, 500);
			</script>
		
		';

	
	}//

	function generate_montly_report_101_check($date01)
	{
		return $this->test99999($date01, 'check_only');

	}

	function generate_montly_report_101($date01)
	{
		return $this->test99999($date01);
	}

	function test99999($date1, $cmd='')
	{
		$date1 = date('Y-m', strtotime($date1));
		$sql1 = "

				SELECT
						CC.id, CC.collector_id, CC.invoice_num, CC.payment,
						CC.payment_date, CC.cust_id,CC.pay_type,CC.billing_id,
						CC.tax_val,CC.status,
						AA.acct_no, AA.acct_status_key, AA.acct_type_key, AA.acct_discount,
						AA.zone_id
				FROM `collections` AS CC
				LEFT JOIN accounts as AA ON AA.id=CC.cust_id

				WHERE CC.payment_date like '$date1%'
				AND
				(
					CC.status = 'active' OR
					CC.status = 'collector_receipt' OR
					CC.status = 'or_nw' OR
					CC.status = 'cr_nw'
				 )


				AND NOT EXISTS(
					SELECT id FROM report1s WHERE report1s.acct_id=AA.id AND
					report1s.reff_1=CC.invoice_num AND report1s.rep_type='collection_report_1' LIMIT 1
				 )

				 ORDER BY CC.invoice_num ASC
				 LIMIT 100
		";

		$sql2 = "
				SELECT
				COUNT(CC.id) as TTL
				FROM `collections` AS CC
				WHERE CC.payment_date like '$date1%'
				AND
				(
					CC.status = 'active' OR
					CC.status = 'collector_receipt' OR
					CC.status = 'or_nw' OR
					CC.status = 'cr_nw'
				 )
		";

		$sql3 = "
				SELECT
				COUNT(CC.id) as TTL
				FROM `collections` AS CC
				WHERE CC.payment_date like '$date1%'
				AND EXISTS(
					SELECT id FROM report1s WHERE report1s.acct_id=CC.cust_id AND
					report1s.reff_1=CC.invoice_num AND report1s.rep_type='collection_report_1' LIMIT 1
				 )
				AND
				(
					CC.status = 'active' OR
					CC.status = 'collector_receipt' OR
					CC.status = 'or_nw' OR
					CC.status = 'cr_nw'
				 )
		";


		$coll1 = DB::select($sql1);
		$coll2 = DB::select($sql2);
		$coll3 = DB::select($sql3);

		$total_collection_count = $coll2[0]->TTL;
		$total_collection_reported = $coll3[0]->TTL;

		if($cmd=='check_only')
		{
			return array(
				'status' => 1, 'msg'=>'Check Stat',
				'tcc' => $total_collection_count, 'tcr' => $total_collection_reported
			);
		}//


		//~ echo '<pre>';
		//~ print_r($total_collection_reported);
		//~ echo '/';
		//~ print_r($total_collection_count);
		//~ print_r($coll1);
		//~ die();

		if(empty($coll1))
		{
			return array(
				'status'=>0, 'msg'=>'No report to process',
				'tcc' => $total_collection_count, 'tcr'=> $total_collection_reported
			);
		}

		foreach($coll1 as $c1)
		{

			$period1 = date('Y-m-01',strtotime($c1->payment_date));
			$taxme = 0;
			$senior_discount = 0;


			if($c1->acct_discount == SENIOR_ID)
			{
				$sen_led1 = LedgerData::where('period',$period1)
								->where('acct_id', $c1->cust_id)
									->where('ledger_info', 'like', '%'.'senior'.'%')
										->first();
				$senior_discount = @$sen_led1->bill_adj;
			}//

			$zone11 = Zones::find($c1->zone_id);

			$rep_type 	= 'collection_report_1';
			$reff_1 	= $c1->invoice_num;
			$reff_2 	= $c1->status;
			$acct_id 	= $c1->cust_id;
			$coll_id 	= $c1->id;
			$acct_stat 	= $c1->acct_status_key;
			$acct_type 	= $c1->acct_type_key;
			$fcollected = $c1->payment;
			$ftax 		= $c1->tax_val;
			$dperiod 	= $period1;
			$dpaydate 	= $c1->payment_date;
			$zone_id 	= $c1->zone_id;

			$fcurrent 	= null;
			$farrear 	= null;
			$fdis 		= $senior_discount;

			$fnon_wat 	= null;
			$fadjust 	= null;
			$fadjust 	= null;
			$fpenalty	= null;
			$fprv_arr   = null;

			if($c1->status == 'or_nw' || $c1->status == 'cr_nw')
			{
				$fcurrent 	= null;
				$farrear 	= null;
				$fdis 		= null;
				$fadjust 	= null;
				$fnon_wat 	= $c1->payment;
				$fpenalty   = null;
			}else{

				$pay_last = LedgerData::
								//where('period',$period1)
								//->
								where('acct_id', $c1->cust_id)
								//~ ->where('reff_no', $c1->id)
									->where(function($q2)use($c1){
										$q2->where('reff_no', $c1->invoice_num);
										$q2->orWhere('reff_no', $c1->id);
									})
									->where(function($q1){
											$q1->where('led_type', 'cancel_cr');
											$q1->orWhere('led_type', 'or_nw');
											$q1->orWhere('led_type', 'payment');
											$q1->orWhere('led_type', 'payment_cancel');
											$q1->orWhere('led_type', 'payment_cr');
										})
										->orderBy('date01', 'desc')
										->orderBy('zort1', 'desc')
										->orderBy('id','desc')
											->first();
					if(!$pay_last) 
					{
						return array('status'=>0, 'msg'=>'ERROR PAYMENT NOT IN LEDGER',
										'tcc' => $total_collection_count,
										'tcr'=>$total_collection_reported,
										'data'=>$c1
						);
						echo 'ERROR PAYMENT NOT IN LEDGER';
						die();
					}//


					$curr_bill = BillingMdl::where('account_id', $c1->cust_id)
										->where('period',$period1)
										->where('status','active')
										->whereHas('ledger12', function($q1)use($pay_last){
											$q1->where('led_type', 'billing');
											$q1->whereRaw(' ledger_datas.reff_no = billing_mdls.id ');
											$q1->where('id','<', $pay_last->id);
										})
										->first();

					

					if($curr_bill){
						//After Billing
					}else{
						//Before Billing
						$period1 = date('Y-m-d',strtotime($period1.' - 1 month'));
					}


					$rr = tview_ledger_billing_ttl($c1->cust_id, $pay_last->id);
					$rr_et = DB::select($rr);


					//~ print_r($rr_et);
					//~ echo '<br />';
					//~ echo '<br />';
					//~ continue;


					$pay_arrear = 0;
					$pay_bill = 0;
					$pay_penal = 0;

					$payment1 =  $c1->payment;

					if(empty($rr_et)){
						$pay_arrear = $c1->payment;
						$pay_bill = 0;
					}


					foreach($rr_et as $r)
					{

							if($r->A2 == 0){continue;}

							if($r->A1 > 0)
							{

								$payment1 -= $r->A1;

								if($payment1 <= 0){
										$pay_arrear = $c1->payment;
									break;}

								$pay_arrear = $r->A1;
								$pay_bill = $payment1;
								$pay_penal = $r->A4;

							}else{
								$payment1 -= $r->AR1;
								$pay_bill = $c1->payment;
								$pay_penal = $r->A4;
							}


							break;
						}//

					$fcurrent 	= $pay_bill;
					$farrear 	= $pay_arrear;
					$fpenalty   = $pay_penal;

					if(@$period1 != @$r->period){
						$fcurrent 	= null;
						$farrear 	= null;
						$fpenalty   = null;
						$fprv_arr 	= $c1->payment;
					}//



			}//



			//~ echo $period1;
			//~ echo '<pre>';
			//~ print_r($c1);
			//~ print_r($rr_et);

			//~ echo $fcurrent;
			//~ echo '<br />';
			//~ echo $farrear;
			//~ echo '<br />';
			//~ echo $fpenalty;
			//~ die();


			$new_rep = new report1;
			$new_rep->rep_type = $rep_type;
			$new_rep->reff_1 = $reff_1;
			$new_rep->reff_2 = $reff_2;
			$new_rep->acct_id = $acct_id;
			$new_rep->coll_id = $coll_id;
			$new_rep->acct_stat = $acct_stat;
			$new_rep->acct_type = $acct_type;
			$new_rep->fcollected = $fcollected;
			$new_rep->ftax = $ftax;
			$new_rep->fdis = $fdis;
			$new_rep->fadjust = $fadjust;
			$new_rep->dperiod = $dperiod;
			$new_rep->dpaydate = $dpaydate;
			$new_rep->zone_id = $zone_id;
			$new_rep->fcurrent = $fcurrent;
			$new_rep->farrear = $farrear;
			$new_rep->fnon_wat = $fnon_wat;
			$new_rep->fprv_arr = $fprv_arr;
			$new_rep->penalty = $fpenalty;
			$new_rep->save();


		}//

		return array('status'=>1, 'msg'=>'On Progress',
				'tcc' => $total_collection_count, 'tcr'=>$total_collection_reported
		);
		echo 'On Progress';

	}//



	function save_update_new_date($coll_id, $new_date, Request $request)
	{

		if(@$_GET['key'] == 1){
			$t=time();
			$_SESSION['key_sess'] = $t;
			return array('key'=>$t, 'sess'=> $t);
		}

		$new_key = @$_GET['new_key'];
		$old_key = @$_SESSION['key_sess'];

		unset($_SESSION['key_sess']);

		if($new_key != $old_key){
			return array('status' => 0, 'msg' => 'Session Invalid', 'sess'=>$old_key);
		}

		$coll = Collection::find($coll_id);
		if(!$coll){
			return array('status' => 0, 'msg' => 'Collection not found');
		}

		$hours = date('H:i:s', strtotime($coll->payment_date));
		$new_date = date('Y-m-d', strtotime($new_date));
		$new_date = $new_date.' '.$hours;
		$coll->payment_date = $new_date;
		$coll->save();

		return array('status' => 1, 'msg' => 'Done updated', 'new_date'=>$new_date);

	}//


	function search_acct1_v1()
	{
		$nn_ar = explode(',',trim(@$_GET['nn']));
		$vv = trim(@$_GET['vv']);

		$lname = trim(@$nn_ar[0]);
		$fname = trim(@$nn_ar[1]);

		$acct1 = Accounts::select('*');

		if($lname == '' && $fname == '' && $vv == ''){
			//~ echo 'No result found';
			return array('status' => '0', 'msg'=>'Record not found');
		}


		if($lname != ''){
			$acct1->where('lname', 'like', $lname.'%');
		}

		if($fname != ''){
			$acct1->where('fname', 'like', $fname.'%');
		}

		if($vv != ''){
			$acct1->where('acct_no', 'like', '%'.$vv.'%');
		}

		$acct1->where('status', '!=', 'deleted');

		$acct1->limit(20);
		$acct1 = $acct1->get();

		//~ echo 'AAA';
		if(count($acct1) == 0){
			//~ echo 'Record not found';
			//~ return;
			return array('status' => '0', 'msg'=>'Record not found', 'html1' => 'Record not found', 'data1'=>array());
		}


		$inx = 0;
		$ret1 =  '<ul>';
		foreach($acct1 as $ac)
		{
			$ac->fname = preg_replace("/[^\p{Xwd}. -]/u", "", $ac->fname);
			$ac->lname = preg_replace("/[^\p{Xwd}. -]/u", "", $ac->lname);
			$ac->address1 = preg_replace("/[^\p{Xwd}. -]/u", "", $ac->address1);


			$ac->fname = strtoupper(str_replace('Ã','Ñ',$ac->fname));
			$ac->lname = strtoupper(str_replace('Ã','Ñ',$ac->lname));
			$ac->address1 = strtoupper(str_replace('Ã','Ñ',$ac->address1));


			$full_name = substr($ac->lname.', '.$ac->fname,0,40);
			$full_name.='<br /> Account # '.$ac->acct_no;
			//~ $full_name = str_replace("'",' ',$full_name);

			$ret1.=
				'<li  class="me'.$ac->id.'" onclick="get_data11xx(\''.$ac->acct_no.'\',\''.$ac->id.'\', \''.$full_name.'\', \''.$inx.'\')">'
				.strtoupper(substr($ac->acct_no.' '.$ac->lname.', '.$ac->fname, 0, 40)).
				'  &nbsp;<small style="font-size:10px;">'.substr($ac->address1,0,30).'</small>'.
				'</li>';
			$inx++;
		}
		$ret1.='</ul>';


		return array(
			'status' => '1',
			'msg'=>'success',
			'html1' => $ret1,
			'data1'=>$acct1
		);


	}//



	function withholding_breakdown_html1()
	{


		// $brk1 = CollectionService::get_breakdown_collection_data();
		// echo '<pre>';
		// print_r($brk1);
		// die();

		$acct_id = @$_GET['acct_id'];

		// ['payment_date', 'due_date']
		// $payable_list = CollectionService::get_remaining_collectable('694165', $acct_id, ['2023-02-21', '2023-02-21']);
		// echo '<pre>';
		// print_r($payable_list);
		// die();

		$result1 = BillingMdl::where('account_id', $acct_id)
					->orderBy('period', 'desc')
					->first();
							
		$bill_id = 0;

		$tax_val = 0;
		if($result1){
			$bill_id = $result1->id;

			$tax_val = Collection::where('billing_id', $bill_id)
						->where(function($query){
							$query->where('status', 'active');
						})
						->sum('tax_val');
		}


		$led_data = LedgerData::where('acct_id', $acct_id)
					->where(function($query){
						$query->where('led_type', '=', 'billing');
						$query->orWhere('led_type', '=', 'beginning');
						$query->orWhere('led_type', '=', 'adjustment');
					})
					->where('status','active')
					->orderBy('date01', 'desc')
					->orderBy('zort1', 'desc')
					->orderBy('id', 'desc')
						->first();

		$arrear11 = LedgerData::where('acct_id', $acct_id)
					->where(function($query){
						$query->where('led_type', '=', 'billing');
						$query->orWhere('led_type', '=', 'beginning');
					})
					->where('status','active')
					->orderBy('date01', 'desc')
					->orderBy('zort1', 'desc')
					->orderBy('id', 'desc')
						->first();


		$balance = LedgerData::where('acct_id', $acct_id)
					->where(function($query){
						//~ $query->where('led_type', '=', 'billing');
						//~ $query->orWhere('led_type', '=', 'beginning');
						//~ $query->orWhere('led_type', '=', 'adjustment');
					})
					->where('status','active')
					->orderBy('date01', 'desc')
					->orderBy('zort1', 'desc')
					->orderBy('id', 'desc')
						->first();

		$arrear = getArrearV2($acct_id);
		$adjustment = (float) getLatestAdjustment($acct_id);

		// $payment_breakdown = balance_breakdown($acct_id, $balance);
		// $payment_breakdown = Dec292020____get_payment_breakdown($acct_id);
					
		$rs001 = feb_05_2021_daily_col_break($acct_id, null, null, $cmd=3);
		$all_debit1 = $rs001['all_debit1'];
		$all_debit2 = $rs001['all_debit2'];
		$arr001 = [];
		foreach($all_debit1 as $kk=>$ar1){
			if($ar1 > 0){
				$lqw  = explode('||', $all_debit2[$kk]);
				$ddd001 = strtoupper(date('M Y', strtotime($lqw[0])) . ' - '.$lqw[1]);
				$arr001[] = ['pre_val'=>$ar1, 'desc' => $ddd001];
			}
		}

		$payment_breakdown = $arr001;


		$tax1 = false;
		$arr1 = compact('led_data', 'balance', 'tax1', 'bill_id', 'tax_val', 'arrear11', 'arrear');

		$html1 = view('collections.subs.wtax1', $arr1)->render();

		// echo '<pre>';
		// print_r(@$payment_breakdown);
		// die();	


		$acct_id  = (int) @$_GET['acct_id'];
		$new_break_down = CollectionService::get_breakdown_collection_data($acct_id);
		// ee($new_break_down, __FILE__, __LINE__);
		
		$tax1 = true;
		$arr1 = compact(
							'led_data', 'balance', 'tax1', 'bill_id',
							'tax_val', 'result1', 'arrear', 'adjustment',
							'payment_breakdown','arrear11', 'new_break_down'
						);
		




		$html2 = view('collections.subs.wtax1', $arr1)->render();
		// $html3 = view('collections.subs.break1', $arr1)->render();
		$html3 = view('collections.subs.break2', $arr1)->render();

		// echo $html3 ;
		// die();


		//~ echo '<pre>';
		//~ print_r($payment_breakdown);
		//~ die();
		//~ die();
		//~ die();
		// echo $html1;
		// die();


		return array(
				'status' => 1,
				'html1' =>$html1,
				'html2' => $html2,
				'html3' => $html3,
				'breakdown' => $new_break_down

		);

	}


	function get_receipt_html11()
	{
		$invnum  = get_invoice_current();
		$invnum  = sprintf("%07d", $invnum);
		
		$html1 = '
			<small>Receipt #</small>
			<br />
			<input type="text"  value="'.$invnum.'"  class="current_inv"  style="padding:5px;text-align:center;" />
			<br />
			<ul class="reciept_type_sty">
				<li><input type="radio" name="or_type1"  class="or_type1"  value="OR"  placeholder="OR"  checked />OR</li>
				<li><input type="radio" name="or_type1"  class="or_type1"  value="CR"  placeholder="CR" />CR</li>
			</ul>
		';
		return array('status'=>1, 'invoice_num'=> $invnum, 'html1'=>$html1);
	}//


	function print_or111($acct_id)
	{

		// echo '<pre>';
		// CollectionService::test001($acct_id);
		// print_r($mmm);
		// print_r($collect1->payment);
		// die();

		// $userId = Auth::id();

		$acct1 = Accounts::find($acct_id);
		if(!$acct1){ return array('status' => 0, 'msg'=>'Account not found');}

		$curr_date = date('Y-m-d');

		$collect1 = Collection::where('cust_id', $acct_id)
						//~ ->where('collector_id', $userId)
						->where('payment_date','>=', $curr_date)//DON'T ALLOW PREVIOUS PRINT
						// ->where('payment_date','like', $curr_date.'%')
								//~ ->where('collection_type', 'bill_payment')
									->orderBy('id', 'desc')
									 ->with('ledger')
										->first();


		if(!$collect1){
			return array('status' => 0, 'msg'=>'No recent collection');
		}

		if($collect1->status == 'cancel_cr'){
			return array('status' => 0, 'msg'=>'Canceled Receipt Printing not allowed');
		}

		if($collect1->status == 'cancel_receipt'){
			return array('status' => 0, 'msg'=>'Canceled Receipt Printing not allowed');
		}

		$collect1->accounts;
		$usermm = User::find($collect1->collector_id);

		$pay_date_key1 = date('Ym', strtotime($collect1->payment_date));
		// $pay_date_key1 = "202505";
		// ee($pay_date_key1, __FILE__, __LINE__);
		// ee($usermm->toArray(), __FILE__, __LINE__);

		// echo '<pre>';
		// print_r($collect1->toArray());
		// die();

		$last_ledger = LedgerData::where('status', 'active')
			->whereIn('led_type', ['payment', 'payment_cancel', 'payment_cr', 'cancel_cr'])		
			->where('acct_id', $acct_id)
			->orderBy('date01', 'desc')
			->orderBy('zort1', 'desc')
			->orderBy('id', 'desc')
			->first();



		/*
		
		*/


		$payment_info = CollectionService::payables_and_payed($acct_id, 0, $collect1->id);
		$brk1 = CollectionService::breakdown($payment_info['payables'], ($payment_info['payment_made'] - $collect1->payment));
		$b1   = CollectionService::process_payment($brk1, $collect1->payment);
		// dd($b1, __FILE__, __LINE__);

		$coll_info = [];		
		if(!empty($collect1->coll_info))
		{
			$coll_info_raw = json_decode($collect1->coll_info, 1);
			$coll_info = $coll_info_raw['payed'];
		}
		// dd($coll_info, __FILE__, __LINE__);



		### INSERT BILLING INFO START
		foreach($coll_info as $k => $v ) {
			if( $v['led_type'] != 'billing'){ continue; }
			$billing1 = BillingMdl::find($v['reff_no']);
			$v['senior'] = @$billing1->discount;

			$coll_info[$k] = $v;
		}
		### INSERT BILLING INFO START

		$brk1 = [];
		foreach($coll_info as $bb) 
		{

			// REMOVED ZERO
			// REMOVED ZERO
			if( (float) $bb['val'] <= 0 ) {
				continue;
			}

			$desc1 = strtoupper(substr($bb['typ'],0,3).'-'.date('M-Y', strtotime($bb['period']) ));
			$brk1[] = [$desc1, $bb['val'], (float) @$bb['senior']];
		}

		// ee($collect1->toArray(), __FILE__, __LINE__);

		########## ARREAR ENTRY
		########## ARREAR ENTRY
		
		$brk1_temp1 = [];
		foreach($brk1 as $k => $v) 
		{
			$brk_ff1 = (explode('-', $v[0]));
			$brk_ff1 = date('Ym', strtotime($brk_ff1[1].' '.$brk_ff1[2]));
			$brk1_temp1[$brk_ff1][] = $v; 
		}
		// echo $pay_date_key1;
		// ee1(explode('NW_', 'NW_-SEP-2025'), __FILE__, __LINE__);
		// ee($brk1_temp1, __FILE__, __LINE__);

		$current_bill = @$brk1_temp1[$pay_date_key1];
		unset($brk1_temp1[$pay_date_key1]);

		$bill_total = 0;
		$ttl_arrear = 0;
		$nw_total   = 0;

		foreach($current_bill as $k => $v) 
		{
			$cnt_nw = explode('NW_', $v[0]);
			if( count($cnt_nw) > 1 ) {
				$nw_total +=  $v[1];
				unset($current_bill[$k]);
				continue;
			}
			$bill_total  += $v[1];
		}


		foreach($brk1_temp1 as $k => $v) 
		{
			foreach($v as $k2 => $v2) 
			{
				$cnt_nw = explode('NW_', $v2[0]);
				if( count($cnt_nw) > 1 ) {
					$nw_total +=  $v2[1];
					continue;
				}
				$ttl_arrear += $v2[1];
			}
		}

		$new_brk1 = [];
		if( !empty($current_bill) ) {
			$new_brk1 = $current_bill;
		}

		if( $ttl_arrear > 0 ) {
			$new_brk1[] = ['ARREAR', $ttl_arrear, 0];
		}

		if( $nw_total > 0 ) {
			$new_brk1[] = ['NW-BILL', $nw_total, 0];
		}


		$brk1 = $new_brk1;

		// ee1($nw_total, __FILE__, __LINE__);
		// ee($brk1, __FILE__, __LINE__);
		########## ARREAR ENTRY END
		########## ARREAR ENTRY END



		// ee($brk1, __FILE__, __LINE__);

		
		$collect1->change =  $collect1->amt_rec - $collect1->payment;
		$collect1->remaining =  @$last_ledger->ttl_bal;
		
		if(in_array($collect1->status, ['or_nw','nw_cancel', 'cr_nw', 'cancel_cr_nw']))
		{
			$brk1[] = ['NWB', $collect1->payment];
			$brk1[] = [substr(strtoupper($collect1->nw_desc), 0, 30), ''];
			// $brk1[] = ['GL/SL'.substr(strtoupper($collect1->glsl), 0, 30), ''];
			$collect1->change = 0;
			$collect1->remaining = 0;
		}




		// ee($collect1->toArray(), __FILE__ ,  __LINE__);
		// ee($brk1, __FILE__, __LINE__);

		$collect1->payment_id = $collect1->id;
		$collect1->full_name  = $collect1->accounts->fname.' '.$collect1->accounts->mi.' '.$collect1->accounts->lname;
		$collect1->account_num = $collect1->accounts->acct_no;
		$collect1->brkdwn = $brk1;
		$collect1->address1 = substr($collect1->accounts->address1, 0, 30);
		$collect1->tin_id = $collect1->accounts->tin_id;
		$collect1->other_id = $collect1->accounts->other_id;
		$collect1->collector_name = $usermm->name;
		// collector_name		


		return (
				array(
					'status'=>1, 
					'data'=>$collect1, 
					'data2' => $coll_info, 
					'colname'=>$usermm->name, 
					'data4'=>$coll_info
				)
			);

################### END
################### END
################### END
################### END
################### END
################### END


		// echo ($payment_info['payment_made'] - $collect1->payment);
		// echo '<pre>';
		// print_r($b1);
		// print_r($coll_info=json_decode($collect1->coll_info));
		// die();





		// echo '<pre>';
		// print_r($usermm->name);
		// die();


		$break_down2 = balance_breakdown_receipt($acct_id, @$collect1->ledger->ttl_bal);
		$break_down3 = balance_breakdown_receipt_22($collect1);
		$break_down4 = balance_breakdown_receipt_334($collect1);



		//Added Feb 5 2021 start
		$break_down7 = array();
		$break_down8 = array();

		$nw_typ_all = ['cancel_cr_nw', 'cr_nw', 'nw_cancel','or_nw'];
		
		if(in_array($collect1->status, $nw_typ_all)){
			//
		}else{
			$print_0001 = feb_05_2021_daily_col_break($acct_id, $collect1->invoice_num, $collect1->payment_date, 2);		
			extract($print_0001);//or_number2, all_debit2, break_down1

			// echo '<pre>';
			// print_r($print_0001);
			// die();

			//
			$brk01_01 = $break_down1[$or_number2[$collect1->invoice_num]];

			$brkdown02 = [];
			foreach($brk01_01 as $m1){
				$br02 = explode('|', $m1);
				$val01 = $br02[0];
				$db_01 = $br02[1];
				$brkdown02[] = ['amt'=>$val01, 'inf' => $all_debit2[$db_01]];
			}

			foreach($brkdown02 as $m1)
			{
				$bk1 = explode('||',$m1['inf']);
				if($bk1[1] == 'beginning'){}
				if($bk1[1] == 'billing'){}
				if($bk1[1] == 'penalty'){}

				$break_down7[] = array(
						'typ' => $bk1[1],
						'date' => $bk1[0],
						'amt' => $m1['amt']
				);

				$break_down8[] = array(
					'desc'    => '',
					'pre_val' => $m1['amt'],
					'val'     => $m1['amt'],
					'typ'     => $bk1[1],
					'date1'   => $bk1[0]
				);				

			}
			//
		}
		//Added Feb 5 2021 END

		// echo '<pre>';
		// print_r($break_down7);
		// print_r($break_down8);
		// die();	

		// ADDED  DEC 30, 2020 START
		// ADDED  DEC 30, 2020 START
		// ADDED  DEC 30, 2020 START
		$break_down5 = array();
		$break_down6 = array();
		$break_down5_counter = 0;

		foreach($collect1->paymet_ledger as $pl1){
			
			$date1 = '';
			//pen_id
			if($pl1->typ == 'beginning'){
				$temp_led = LedgerData::find($pl1->pen_id);
				$date1 = $temp_led->date01;
			}
			
			if($pl1->typ == 'billing'){
				$temp_led = BillingMdl::find($pl1->pen_id);
				$date1 = $temp_led->bill_date;
			}
			
			if($pl1->typ == 'penalty'){
				$temp_led = BillingDue::find($pl1->pen_id);
				$date1 = $temp_led->due_date;
			}
			
			$break_down5[$break_down5_counter] = array(
					'typ' => $pl1->typ,
					'date' => $date1,
					'amt' => $pl1->amt
			);

			$break_down6[$break_down5_counter] = array(
				'desc'    => '',
				'pre_val' => $pl1->amt,
				'val'     => $pl1->amt,
				'typ'     => $pl1->typ,
				'date1'   => $date1
			);


			$break_down5_counter++;
		}
		
		// $break_down4 = $break_down5;	
		// $break_down3 = $break_down6; 

		//Feb 5 2021 Start
		$break_down4 = $break_down7;	
		$break_down3 = $break_down8; 		
		//Feb 5 2021 End

		// ADDED  DEC 30, 2020 END
		// ADDED  DEC 30, 2020 END
		// ADDED  DEC 30, 2020 END			

		// echo '<pre>';
		return (array(
				'status'=>1, 
				'data'=>$collect1, 
				'data2' => $break_down3, 
				'colname'=>$usermm->name, 
				'data4'=>$break_down4
		)
		);

		//~ balance_breakdown($acct_id, $balance);

		//~ echo '<pre>';
		//~ echo 'AAA';
		//~ print_r($mm);
		//~ print_r($collect1->toArray());

		//~ echo 'TEST';
	}//


	function add_none_water($acct_id)
	{

		$userId = Auth::id();

		$acct1 = Accounts::find($acct_id);
		if(!$acct1){ return array('status' => 0, 'msg'=>'Account not found');}

		$nw_inv = (int)@$_GET['nw_inv'];
		$ttl_amt = (float) @$_GET['ttl_amt'];
		$ttl_amt_db = (float) @$_GET['ttl_amt_db'];//DEBIT
		$nw_desc = @$_GET['nw_desc'];
		$nw_reff = @$_GET['nw_reff'];
		$glsl_code = @$_GET['glsl_code'];

		$or_t1  = @$_GET['or_t1'];
		$trx_date  = @$_GET['trx_date'];
		$nw_iid  = @$_GET['nw_iid'];

		$nw_tax_amount  = round((float) @$_GET['nw_tax_amount'], 2);
		$nw_mode_payment  = strtolower(@$_GET['nw_mode_payment']);

		$both_check_num    = (@$_GET['both_check_num']);
		$check_date		   = (@$_GET['check_date']);
		$bank_name22 	   = (@$_GET['bank_name22']);
		$bank_branches_22  = (@$_GET['bank_branches_22']);

		$trans_date = date('Y-m-d',strtotime($trx_date));

		if($nw_inv <= 0 ){
			return array('status'=>0, 'msg'=>'Invalid invoice number');
		}

		$check_invoice = Collection::where('invoice_num',$nw_inv)
							->where('payment_date', '>=', '2025-06-08' )### SALES INVOICE STARTED
							->orderBy('id', 'desc')
								->first();

		if($check_invoice)
		{

			if($or_t1 == 'OR'){
				return array('status'=>0, 'msg'=>'Invoice #'.$nw_inv.' is already used. @'.__LINE__);
			}elseif($or_t1 == 'CR'){

				if(
					$check_invoice->status == 'nw_cancel' ||
					$check_invoice->status == 'cancel_cr_nw' ||
					$check_invoice->status == 'cancel_receipt'
				  )
				{
					if($check_invoice->invoice_num != $nw_inv){
						return array('status'=>0, 'msg'=>'Invoice #'.$nw_inv.' is not equal to previous invoice #');
					}

				}else{
					return array('status'=>0, 'msg'=>'Invoice #'.$nw_inv.' is already used. ERROR 130');
				}

			}else{
				return array('status'=>0, 'msg'=>'Invalid OR TYPE');
			}
		}
		else
		{
			if($or_t1 == 'CR')
			{
				return array('status'=>0, 'msg'=>'Invalid invoice #. CODE 138');
			}//
			
		}//

		//non_water_bill_payment


		$inv_set = Invoice::where('seq_start','<=', $nw_inv)
					 ->where('seq_end','>=', $nw_inv)
						 ->first();

		if(!$inv_set){
			return array('status'=>0, 'msg'=>'Invoice number is not registered');
		}


		$new_stat1 = 'or_nw';
		$ledtype1  =  'payment_none_water';

		if($or_t1 == 'OR'){
			//
		}elseif($or_t1 == 'CR'){
			$new_stat1 = 'cr_nw';
		}

		if( !in_array($nw_mode_payment, ['cash', 'check']) ) 
		{
			$nw_mode_payment = 'cash';
		}

		$banks = get_bank_list();
		$banks = $banks->toArray();		


		$acct111 = Accounts::find($acct_id);

		$new_col = new Collection;
		$new_col->collector_id = $userId;
		$new_col->collection_type = 'non_water_bill_payment';
		$new_col->invoice_num = $nw_inv;
		$new_col->payment = $ttl_amt;
		//~ $new_col->status ='active';
		$new_col->status = $new_stat1;

		$new_col->payment_date = $trans_date.' '.date('H:i:s');

		$new_col->cust_id = $acct_id;
		// $new_col->pay_type = 'cash';
		$new_col->pay_type = $nw_mode_payment;
		$new_col->zone_id = $acct111->zone_id;

		$new_col->nw_desc = @$nw_desc;
		$new_col->nw_reff = @$nw_reff;
		$new_col->nw_type = '1';
		$new_col->nw_glsl = trim(@$glsl_code);
		$new_col->tax_val = $nw_tax_amount;

		if( $nw_mode_payment == 'check' ) 
		{
			$new_col->bank_id   = $banks[$bank_name22]['id'];
			$new_col->check_no  = $both_check_num;
			$new_col->bank_info = $banks[$bank_name22]['bank_name'].' - '.$bank_branches_22;
			$new_col->chk_full  = $ttl_amt;
			$new_col->chk_date  = @$check_date;			
		}

		$new_col->save();


		$ledger2 = LedgerData::
						where('acct_id', $acct_id)
						->where('status','active')
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						//~ ->orderBy('date01', 'desc')
						->orderBy('id', 'desc')
						->first();


		$ttl_bal = 0;

		if($ledger2){
			$ttl_bal = $ledger2->ttl_bal;
		}



		$ttl_bal+=$ttl_amt_db;


		$new_ledger_data = new LedgerData;
		$new_ledger_data->led_type = $new_stat1.'_debit';
		$new_ledger_data->acct_id = $acct_id;
		$new_ledger_data->ttl_bal = $ttl_bal;
		$new_ledger_data->ledger_info = 'Non-Water Bill Debit';
		$new_ledger_data->status = 'active';
		$new_ledger_data->acct_num = @$ledger2->acct_num;

		$new_ledger_data->date01 = $trans_date.' '.date('H:i:s');

		$new_ledger_data->period = date('Y-m-', strtotime($trans_date)).'01';
		$new_ledger_data->reff_no = $nw_inv;
		$new_ledger_data->bill_adj = (abs($ttl_amt_db) * -1);
		$new_ledger_data->nw_desc = @$nw_desc;
		$new_ledger_data->nw_reff = @$nw_reff;
		$new_ledger_data->nw_type = '1';
		$new_ledger_data->coll_id = $new_col->id;
		$new_ledger_data->glsl = trim(@$glsl_code);
		$new_ledger_data->save();


		if( $nw_tax_amount > 0 )
		{
			$wtax = true;
			$wtax_value = $nw_tax_amount;
			$method = $nw_mode_payment;
			
			$ttl_bal -= $wtax_value;  
			no_bill_apply_tax($wtax, $wtax_value, $ttl_bal, $acct_id, $method, $new_col, $trans_date.' '.date('H:i:s')) ;
		}//

		$ttl_bal-=$ttl_amt;


		$new_ledger_data = new LedgerData;
		$new_ledger_data->led_type = $new_stat1;
		$new_ledger_data->acct_id = $acct_id;
		$new_ledger_data->ttl_bal = $ttl_bal;
		$new_ledger_data->ledger_info = 'Non-Water Bill Payment';
		$new_ledger_data->status = 'active';
		$new_ledger_data->acct_num = @$ledger2->acct_num;

		$new_ledger_data->date01 = $trans_date.' '.date('H:i:s');

		$new_ledger_data->period = date('Y-m-', strtotime($trans_date)).'01';
		$new_ledger_data->reff_no = $nw_inv;
		$new_ledger_data->payment = abs($ttl_amt);
		$new_ledger_data->nw_desc = @$nw_desc;
		$new_ledger_data->nw_reff = @$nw_reff;
		$new_ledger_data->nw_type = '1';
		$new_ledger_data->coll_id = $new_col->id;
		$new_ledger_data->glsl = trim(@$glsl_code);
		$new_ledger_data->save();


		$nw_inv++;
		$inv_set->seq_c = $nw_inv;
		$inv_set->save();


		$payable1 = get_other_payable_by_id($nw_iid);

		if($payable1)
		{

			if($payable1->id == RECONNECT_ID)
			{

				$acct111->acct_status_key = 2;
				$acct111->save();

				$change_labels = array();

				$from1 = acct_status(4);
				$to1   = acct_status(2);

				$change_labels[] = '
					Account status change from '.$from1.'
					to '.$to1.'
				';

				$info1 = '<ul>';
				foreach($change_labels as $cl1){$info1.='<li>'.$cl1.'</li>';}
				$info1 .= '<ul>';

				$new_led = new HwdLedger;
				$new_led->led_type = 'account';
				$new_led->led_title = 'Account modificaton';
				$new_led->status1 = 'active';
				$new_led->led_key1 = $acct111->id;
				$new_led->led_date2 = date('Y-m-d H:i:s');
				$new_led->led_desc1 = $info1;
				$new_led->ctyp1 = 'account_modify';
				$new_led->save();

			}

		}//


		return array(
			'status'=>1,
			'msg'=>'Done added',
			'new_inv' => $nw_inv
		);


	}//




	function update_receipt_type($coll_id)
	{

		$current_coll = Collection::find($coll_id);

		$coll_type = 'active';
		$col_cod   = (int) @$_GET['ctype'];
		$status2 = '';

		if(!$current_coll){
			return array('stat'=> 0, 'msg'=> 'Collection not found');
		}

		if($col_cod == 1 || $col_cod == 2 || $col_cod == 3 ){
		}else{
			return array('stat'=> 0, 'msg'=> 'Invalid Status');
		}

		dec272020_delete_pay_led_by_coll_id($current_coll->id);


		if($col_cod == 1){$coll_type = 'active';$status2 = 'Official Receipt';}//official_receipt
		if($col_cod == 2){$coll_type = 'collector_receipt';$status2 = 'Collector Receipt';}
		if($col_cod == 3){$coll_type = 'cancel_receipt';$status2 = 'Cancel Receipt';}


		 $ledger2 = LedgerData::
						where('acct_id', $current_coll->cust_id)
						->where('status','active')
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						->orderBy('id', 'desc')
						->first();



		$ttl_bal = 0;

		if($ledger2){
			$ttl_bal = $ledger2->ttl_bal;
		}

		//$ttl_bal-=$payment_made;

		if($col_cod == 1){}//official_receipt

		if($col_cod == 2){}//Collectors Receipt

			// echo '<pre>';
			// echo $ttl_bal;
			// echo '<br />';
			// echo $col_cod;
			// echo '<br />';
			// print_r($current_coll->toArray());
			// die();

		if(
			$col_cod == 3  && (
					$current_coll->status != 'cancel_receipt' &&
					$current_coll->status != 'cancel_cr'  &&
					$current_coll->status!= 'nw_cancel'
				)
		)
		{

			$mm = $current_coll->collection_type;

			$led_type1 = 'payment_cancel';
			$status101 = 'cancel_receipt';

			if($current_coll->status == 'collector_receipt')
			{
				$led_type1 = 'cancel_cr';
				$status101 = 'cancel_cr';
			}

			if($mm == 'non_water_bill_payment')
			{

				$led_type1 = 'nw_cancel';
				$status101 = 'nw_cancel';

				if($current_coll->status == 'cr_nw')
				{
					$led_type1 = 'cancel_cr_nw';
					$status101 = 'cancel_cr_nw';
				}

			}


			$ttl_bal += (@$current_coll->payment + @$current_coll->tax_val);


			//Added Jan 30
			$nw_debit1 = 0;
			$new_payment = -1 * (abs($current_coll->payment + @$current_coll->tax_val ));
			$prev_adj = 0;

			if($mm == 'non_water_bill_payment')
			{

				$debit_2 = LedgerData::where('led_type', 'or_nw_debit')
							->where('acct_id', $current_coll->cust_id)
							->where('status','active')
							->where('reff_no', $current_coll->invoice_num)
							->first();

				if($debit_2)
				{
					$prev_adj = ( (float) abs(@$debit_2->bill_adj) )  - $current_coll->tax_val;
					$nw_debit1 = ( (float) abs(@$debit_2->bill_adj) )  - $current_coll->tax_val;
					$new_payment = ( -1 * (abs($current_coll->payment ))) ;
					$ttl_bal  -= ($nw_debit1 + $current_coll->tax_val);
				}
			}
			//Added Jan 30 END



			// echo $ttl_bal;
			// die();

			$new_ledger_data = new LedgerData;
			$new_ledger_data->led_type = $led_type1;
			$new_ledger_data->acct_id = $current_coll->cust_id;
			$new_ledger_data->ttl_bal = $ttl_bal;

			//Added Jan 30
			//~ $new_ledger_data->payment = -1 * abs($current_coll->payment + @$current_coll->tax_val);
			$new_ledger_data->payment = $new_payment;
			$new_ledger_data->bill_adj = $prev_adj;
			//Added Jan 30 END
 
			$new_ledger_data->ledger_info = 'Cancel Payment';
			$new_ledger_data->status = 'active';
			$new_ledger_data->acct_num = $ledger2->acct_num;
			$new_ledger_data->date01 = date('Y-m-d H:i:s');
			$new_ledger_data->period = date('Y-m-').'01';
			$new_ledger_data->reff_no = $current_coll->invoice_num;
			$new_ledger_data->coll_id = $current_coll->id;
			$new_ledger_data->save();

			$current_coll->status = $status101;
			$current_coll->save();


			LedgerData::where('coll_id', $current_coll->id)->first();
			
			BillingNw::where('typ', 'nw_child_pay')->where('id1', $current_coll->id)->delete();



		}//Cancel Receipt


		//~ $current_coll->status = $coll_type;
		//~ $current_coll->save();

		return array('stat'=> 1, 'msg'=> 'Done Updated', 'status2' => $status2);

	}//

	function view_my_ledger101()
	{

		return LedgerCtrl::AccountLedgerGetAccount();

	}


	function make_payment_step1_no_billing($acct_id, $inv_set)
	{

		$userId = Auth::id();
		$led = getLatestLegerV3($acct_id);
		if(!$led){$led = getLatestLegerV2($acct_id);}

		if(!$led){return array('status'=>0, 'msg'=>'No water bill found');}

		if($led->ttl_bal <= 0){return array('status'=>0, 'msg'=>'No outstanding balance');}


		$bill_id = (int) @$_GET['bill_id'];
		$inv_num  = '';
		$amt = (float) @$_GET['amt'];

		$method 	= @$_GET['method'];
		$bcash  	= (float) @$_GET['bcash'];
		$bcheck 	= (float) @$_GET['bcheck'];
		$ada_amount = (float) @$_GET['ada_amount'];
		$chk_full 	= @$_GET['chk_full'];
		$chk_date 	= @$_GET['chk_date'];


		$invoice_num  = (int) @$_GET['inv'];

		$bchecknum 	  = @$_GET['bchecknum'];
		$bbankname    = (int) @$_GET['bbankname'];
		$bbankbranch  = @$_GET['bbankbranch'];
		$wtax 		  = @$_GET['wtax'];
		$wtax_value   = @$_GET['wtax_value'];
		$trx_date     = @$_GET['trx_date'];
		$amt_due_x    = @$_GET['amt_due_x'];// FOR OVER PAYMENT ALLOWED ONLY


		$cur_date33   = strtotime(date('Y-m-d'));
		$trans_date33 = strtotime($trx_date);


		$min_month    = strtotime(date('Y-m-d').' -1 Month');
		if($trans_date33 <= $min_month){
			return array('status'=>0, 'msg'=>'Invalid transaction date Minimum');
		}

		$payment_date = date('Y-m-d',$trans_date33);

		$banks = get_bank_list();
		$banks = $banks->toArray();

		$ttl_bal = $led->ttl_bal;

		if($method == 'check'){$amt = $bcheck;}
		if($method == 'both'){$amt = $bcheck + $bcash;}
		if($method == 'ada'){$amt = $ada_amount;}

		$amount_recieve = $amt;


		if($led->ttl_bal < $amt){
			$amt = $led->ttl_bal;
		}
		
		// FOR OVER PAYMENT ALLOWED ONLY
		if($led->ttl_bal <  $amt)
		{
			if($amt_due_x > $amt){
				$amt = $amt_due_x;
			}
		}
		// FOR OVER PAYMENT ALLOWED ONLY				


		if($amt <= 0){
			return array('status'=>0, 'msg'=>'Invalid Amount');
		}


		$payment_made = $amt;

		$ttl_bal -= $amt;


		$acct111 = Accounts::find($acct_id);

		$col_status = 'active';

		$ort1 = @$_GET['ort1'];
		if($ort1 == 'CR')
		{
			$col_status = 'collector_receipt';
		}


		$new_col = new Collection;
		$new_col->collector_id = $userId;
		$new_col->collection_type = 'bill_payment';
		$new_col->invoice_num = $invoice_num;
		$new_col->payment = $payment_made;
		$new_col->status = $col_status;
		$new_col->payment_date = $payment_date.date(' H:i:s');
		$new_col->cust_id = $acct_id;
		$new_col->amt_rec = $amount_recieve;
		$new_col->zone_id = $acct111->zone_id;
		$new_col->arrear1 = $payment_made;

		$ww1 = 0;
		$per_c1 = 0;

		if($wtax == 'true')
		{
			$ww1 = $wtax_value;
		}

		$new_col->tax_per = $per_c1;
		$new_col->tax_val = $ww1;


		if($method != 'cash'){
			//~ $new_col->pay_type = 'check';
			$new_col->pay_type  = $method;
			$new_col->bank_id   = $banks[$bbankname]['id'];
			$new_col->check_no  = $bchecknum;
			$new_col->bank_info = $banks[$bbankname]['bank_name'].' - '.$bbankbranch;
			$new_col->chk_full  = $chk_full;
			$new_col->chk_date  = @$chk_date;
		}

		//$inv_set

		$new_col->save();

		$invoice_num++;

		$inv_set->seq_c = $invoice_num;
		$inv_set->save();


		$acct11 = Accounts::find($acct_id);

		$led_type = 'payment';

		if($ort1 == 'CR'){
			$led_type = 'payment_cr';
		}

		$new_ledger_data = new LedgerData;
		$new_ledger_data->led_type = @$led_type;
		$new_ledger_data->acct_id = @$acct_id;
		$new_ledger_data->ttl_bal = @$ttl_bal;
		$new_ledger_data->payment = @$payment_made;
		$new_ledger_data->ledger_info = 'Bill Payment '.strtoupper(@$method);
		$new_ledger_data->status = 'active';
		$new_ledger_data->acct_num = @$acct11->acct_no;
		$new_ledger_data->date01 = @$payment_date.date(' H:i:s');
		$new_ledger_data->period = date('Y-m-').'01';
		$new_ledger_data->reff_no = @$new_col->invoice_num;
		$new_ledger_data->coll_id = @$new_col->id;
		$new_ledger_data->save();

		no_bill_apply_tax($wtax, $wtax_value, $ttl_bal, $acct_id, $method, $new_col, $trans_date33_d = $payment_date.date(' H:i:s'));


		//Record Payment Ledger
		$payment_ledger = new HwdJobCtrl;
		$payment_ledger->collection_ledger_update_dec272020($acct_id);

		return array('status'=>1, 'new_invoice'=>$invoice_num);


	}//

	function make_payment_step1_v2($acct_id)
	{

		$vars_all = CollectionService::request_data($acct_id);
		$is_good = CollectionService::collections_conditions($vars_all);
		if($is_good != 'good') {return $is_good;} 
		extract($vars_all);

		$payment_info = CollectionService::payables_and_payed($acct_id, 0);
		$brk1 = CollectionService::breakdown($payment_info['payables'], $payment_info['payment_made']);

		######
		# MAKE -NW-BILLING - PRIORITY
		######
		// $brk1_new = [];
		// foreach( $brk1 as $k => $v ) {
		// 	if( $v['typ'] == 'nw_billing' ) {
		// 		$brk1_new[] = $v;
		// 		unset($brk1[$k]);
		// 	}
		// }
		// $brk1  = array_merge($brk1_new, $brk1);
		######
		# MAKE -NW-BILLING - PRIORITY
		######

		$json_payment_breakdown   = CollectionService::process_payment($brk1, $payment_made);

		################
		################
		$non_wat1 = [];
		foreach( $json_payment_breakdown['payed'] as $k => $v ) {
			if( $v['typ'] == 'nw_billing' ) {

				$nw_item = BillingNw::where('typ', 'nw_child')
								->where('date1', $v['period'])
									->where('id1', $v['reff_no'])
									->first();
				if( !$nw_item ) {
					// ERROR
					// return array('status'=>0, 'msg'=>' NWB not found ');
					// return false; 
					continue;
				}
				
				$nw_item->for_payment = $v['val'];
				$non_wat1[] = $nw_item->toArray();
			}
		}//

		// ee($non_wat1, __FILE__, __LINE__);
		// ee($json_payment_breakdown, __FILE__, __LINE__);
		################
		################
		// echo '<pre>';
		// print_r($json_payment_breakdown['payed']);
		// die();

		$payed_nwb = 0;
		$has_nwb   = 0;

		foreach($json_payment_breakdown['payed'] as $payed_nw)
		{
			if( 'other_payable' == $payed_nw['typ'] )
			{
				$payed_nwb += round($payed_nw['amount'], 2);
				$has_nwb   = 1;
			}
		}

		$new_col = new Collection;
		$new_col->collector_id = $userId;
		$new_col->collection_type = 'bill_payment';
		$new_col->invoice_num = $invoice_num;
		$new_col->payment = $payment_made;
		$new_col->status = $col_stat11;
		$new_col->payment_date = $trans_date33_d.' '.date('H:i:s');
		$new_col->cust_id = $acct_id;
		$new_col->amt_rec = $amount_recieve;
		$new_col->tax_per = $per_c1;
		$new_col->tax_val = $ww1;
		$new_col->zone_id = $acct111->zone_id;

		$new_col->has_nw = $has_nwb;
		$new_col->nw_amt = $payed_nwb;

		if($method != 'cash')
		{
			$new_col->pay_type  = @$method;
			$new_col->bank_id   = @$banks[$bbankname]['id'];
			$new_col->check_no  = @$bchecknum;
			$new_col->bank_info = @$banks[$bbankname]['bank_name'].' - '.$bbankbranch;
			$new_col->chk_full  = @$chk_full;
			$new_col->chk_date  = @$chk_date;
		}

		$new_col->coll_info = json_encode($json_payment_breakdown);
		$new_col->save();


		$invoice_num++;

		$inv_set->seq_c = $invoice_num;
		$inv_set->save();


		$ledger2 = LedgerData::
						where('acct_id', @$acct_id)
						->where('status','active')
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						->orderBy('id', 'desc')
						->first();

		$ttl_bal = 0;

		if($ledger2){
			$ttl_bal = $ledger2->ttl_bal;
		}

		$acct11 = Accounts::find(@$acct_id);


		if($wtax == 'true')
		{
			$led_type = 'wtax';

			$ttl_bal -= $ww1;

			$new_ledger_data = new LedgerData;
			$new_ledger_data->led_type 	  = @$led_type;
			$new_ledger_data->acct_id     = @$acct_id;
			$new_ledger_data->ttl_bal     = @$ttl_bal;
			$new_ledger_data->payment     = @$ww1;
			$new_ledger_data->ledger_info = 'Withholding Tax '.strtoupper(@$method);
			$new_ledger_data->status      = 'active';
			$new_ledger_data->acct_num    = @$acct11->acct_no;
			$new_ledger_data->date01 	  = @$trans_date33_d;
			$new_ledger_data->period 	  = date('Y-m-01');
			$new_ledger_data->reff_no 	  = @$new_col->invoice_num;
			$new_ledger_data->coll_id 	  = @$new_col->id;
			$new_ledger_data->save();
		}



		//$ttl_bal+=$total_billing;


		$led_type = 'payment';

		if($ort1 == 'CR'){
			$led_type = 'payment_cr';
		}

		$ttl_bal-= $payment_made;

		$new_ledger_data = new LedgerData;
		$new_ledger_data->led_type = @$led_type;
		$new_ledger_data->acct_id =@$acct_id;
		$new_ledger_data->ttl_bal = @$ttl_bal;
		$new_ledger_data->payment = @$payment_made;
		$new_ledger_data->ledger_info = 'Bill Payment '.strtoupper(@$method);
		$new_ledger_data->status = 'active';
		$new_ledger_data->acct_num = @$acct11->acct_no;
		$new_ledger_data->date01 = @$trans_date33_d;
		$new_ledger_data->period = date('Y-m-01');
		$new_ledger_data->reff_no = @$new_col->invoice_num;
		$new_ledger_data->coll_id = @$new_col->id;
		$new_ledger_data->save();

		#############
		#############
		$multi_insert = [];
		foreach($non_wat1 as $k => $v ) {
			$multi_insert[] = [
				'acct_id' => @$acct_id,// Account ID
				'id1' => @$new_col->id, //Collection ID
				'id2' => $v['id'],// nw_child ID
				'amt_1' => $v['for_payment'], // for_payment
				'date1' => $v['date1'], // period
				'typ' => 'nw_child_pay', // period
				'status' => 'active', // period
			];
		}
		if( !empty($multi_insert) ) {
			BillingNw::insert($multi_insert);
		}
		#############
		#############


		return array('status'=>1, 'new_invoice'=>@$invoice_num);

		
	}//


	function make_payment_step1($acct_id)
	{
		return $this->make_payment_step1_v2($acct_id);


		//~ return;
		//~ return;
		//~ return;
		//~ return;

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

		if($trans_date33 > $cur_date33){
			//return array('status'=>0, 'msg'=>'Invalid transaction date '.date('F d, Y', $trans_date33));
		}


		if($trans_date33 <= $min_month){
			return array('status'=>0, 'msg'=>'Invalid transaction date '.date('F d, Y', $trans_date33));
		}


		if($invoice_num == 0){
			return array('status'=>0, 'msg'=>'Invalid Invoice');
		}

		$inv_set = Invoice::where('seq_start','<=', $invoice_num)
					 ->where('seq_end','>=', $invoice_num)
						->first();

		if(!$inv_set){
			return array('status'=>0, 'msg'=>'Invalid Invoice');
		}

		$check_invoice = Collection::where('invoice_num',$invoice_num)
							->where('payment_date', '>=', '2025-06-08' )### SALES INVOICE STARTED
							->orderBy('id', 'desc')
								->first();

		if($check_invoice)
		{
			if($ort1 == 'OR')
			{
				return array('status'=>0, 'msg'=>'Invoice #'.$invoice_num.' is already used. ERROR 1 2412');
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


		$result1 = BillingMdl::where('account_id', $acct_id)
					->orderBy('period', 'desc')
						->first();


		if(!$result1){
			//~ return array('status'=>0, 'msg'=>'Billing not found');
			return  $this->make_payment_step1_no_billing($acct_id, $inv_set);
		}//



		if($result1->id != $bill_id){
			return array('status'=>0, 'msg'=>'Bill not match');
		}


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


		//~ echo '<pre>';
		//~ echo $amount_recieve;
		//~ die();

		//~ return array('status'=>0, 'msg'=>'Ayos');
		//~ echo 'AAA';
		//~ die();
		//~ die();
		//~ die();


		$acct111 = Accounts::find($acct_id);


		$total_payment = Collection::
							//where('payment_date', '>=', $period_sub)
							  where('created_at', '>', $result1->created_at)
								->where('cust_id', $acct_id)
								->where(function($query){
									$query->where('status', 'active');
									$query->orWhere('status', 'collector_receipt');
								})
								//~ ->where('collection_type', 'bill_payment')
								->where(function($q2){
										$q2->where('collection_type', 'bill_payment');
										$q2->orWhere('collection_type', 'bill_cancel_payment');
										$q2->orWhere('collection_type', 'bill_payment_coll');
									})
								//~ ->get();
								->sum('payment');


		//~ echo '<pre>';
		//~ print_r($total_payment->toArray());
		//~ die();

		$ttl_tax = Collection::
							//where('payment_date', '>=', $period_sub)
							  where('created_at', '>', $result1->created_at)
								->where('cust_id', $acct_id)
								->where(function($query){
									$query->where('status', 'active');
									$query->orWhere('status', 'collector_receipt');
								})
								->where('collection_type', 'bill_payment')
								->sum('tax_val');


		$total_payment += $ttl_tax;

		$arrear      = getArrearV2($acct_id);
		$adjustment  = (float) getLatestAdjustment($acct_id);

		$total_bill  = (float) @$result1->curr_bill;
		$total_bill += (float) @$arrear->amount;
		$total_bill += (float) @$result1->penalty;

		//~ echo $result1->penalty;
		//~ die();

		//DISCOUNT
		if($result1->discount > 0){
			$total_bill -= $result1->discount;
		}
		//DISCOUNT

		$total_bill -= $adjustment;


		//~ $tax_val = Collection::where('billing_id', $bill_id)
					//~ ->where(function($query){
						//~ $query->where('status', 'active');
					//~ })
					//~ ->sum('tax_val');

		$tax_val = wtax_val1($bill_id);

		if($wtax == 'true')
		{
			//~ $per_c1 = $wtax_value / 100;
			//~ $ww1 = ($result1->curr_bill  * $per_c1)  - $tax_val;
			//~ $total_bill -= $ww1;
			//~ $total_bill -= $ww1;
		}

		$amount_due  = round($total_bill - $total_payment,2);

		$ww1 = 0;
		$per_c1 = 0;

		if($wtax == 'true')
		{

			$ww1 = $wtax_value;

			/*
			$per_c1 = $wtax_value / 100;
			$ww1 = ($result1->curr_bill  * $per_c1) - $tax_val;

			if($amount_due > $amt)
			{

				if($amt <= $result1->curr_bill){
					$ww1 = ($amt  * $per_c1);
				}else{
					$r1 = $amount_due - $amt;
					$r2  = $result1->curr_bill - $r1;
					$ww1 = ($r2  * $per_c1);
				}

			}*/

		}


		$led_1 = LedgerData::where('status','active')
						->where('acct_id', $acct_id)
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						->orderBy('id', 'desc')
						->first();

		if($led_1)
		{
			$amount_due = round($led_1->ttl_bal,2);
		}
		
		// FOR OVER PAYMENT ALLOWED ONLY
		if($amt_due_x > $amount_due){
			$amount_due = $amt_due_x;
		}
		// FOR OVER PAYMENT ALLOWED ONLY		

		//~ echo '<pre>';
		//~ echo $total_bill;
		//~ echo $amount_due;
		//~ die();

		if($amount_due <= 0){
			return array('status'=>0, 'msg'=>'Full payment is alreay done.');
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


		//$payment_made

		$breakdown1 = bill_payment_breakdown1($result1, $payment_made);

		//~ echo '<pre>';
		//~ echo $payment_made;
		//~ print_r($breakdown1);
		//~ die();

		$trans_date33_d = date('Y-m-d', $trans_date33);

		$new_col = new Collection;
		$new_col->collector_id = $userId;
		$new_col->collection_type = 'bill_payment';
		$new_col->billing_id =$result1->id;
		$new_col->invoice_num = $invoice_num;
		$new_col->payment = $payment_made;
		$new_col->status = $col_stat11;

		$new_col->payment_date = $trans_date33_d.' '.date('H:i:s');

		$new_col->cust_id = $acct_id;
		$new_col->amt_rec = $amount_recieve;

		$new_col->tax_per = $per_c1;
		$new_col->tax_val = $ww1;

		$new_col->bill1    =  (float) @$breakdown1['bill11'];
		$new_col->arrear1  =  (float) @$breakdown1['arr11'];
		$new_col->penalty1 =  (float) @$breakdown1['pena11'];

		$new_col->zone_id = $acct111->zone_id;

		if($method != 'cash'){
			//~ $new_col->pay_type = 'check';
			$new_col->pay_type  = @$method;
			$new_col->bank_id   = @$banks[$bbankname]['id'];
			$new_col->check_no  = @$bchecknum;
			$new_col->bank_info = @$banks[$bbankname]['bank_name'].' - '.$bbankbranch;
			$new_col->chk_full  = @$chk_full;
			$new_col->chk_date  = @$chk_date;
		}

		$new_col->save();

		$invoice_num++;

		$inv_set->seq_c = $invoice_num;
		$inv_set->save();


		$ledger2 = LedgerData::
						where('acct_id', @$acct_id)
						->where('status','active')
						//~ ->orderBy('date01', 'desc')
						->orderBy('date01', 'desc')
						->orderBy('zort1', 'desc')
						->orderBy('id', 'desc')
						->first();

		$ttl_bal = 0;

		if($ledger2){
			$ttl_bal = $ledger2->ttl_bal;
		}

		$acct11 = Accounts::find(@$acct_id);


		if($wtax == 'true')
		{
			$led_type = 'wtax';

			$ttl_bal -= $ww1;

			$new_ledger_data = new LedgerData;
			$new_ledger_data->led_type 	  = @$led_type;
			$new_ledger_data->acct_id     = @$acct_id;
			$new_ledger_data->bill_id     = @$result1->id;
			$new_ledger_data->ttl_bal     = @$ttl_bal;
			$new_ledger_data->payment     = @$ww1;
			$new_ledger_data->ledger_info = 'Withholding Tax '.strtoupper(@$method);
			$new_ledger_data->status      = 'active';
			$new_ledger_data->acct_num    = @$acct11->acct_no;

			//$new_ledger_data->date01 = date('Y-m-d');
			$new_ledger_data->date01 	  = @$trans_date33_d;

			$new_ledger_data->period 	  = date('Y-m-01');
			$new_ledger_data->reff_no 	  = @$new_col->invoice_num;
			$new_ledger_data->coll_id 	  = @$new_col->id;
			$new_ledger_data->save();
		}



		//$ttl_bal+=$total_billing;


		$led_type = 'payment';

		if($ort1 == 'CR'){
			$led_type = 'payment_cr';
		}

		$ttl_bal-= $payment_made;

		$new_ledger_data = new LedgerData;
		$new_ledger_data->led_type = @$led_type;
		$new_ledger_data->acct_id =@$acct_id;
		$new_ledger_data->bill_id = @$result1->id;
		$new_ledger_data->ttl_bal = @$ttl_bal;
		$new_ledger_data->payment = @$payment_made;
		$new_ledger_data->ledger_info = 'Bill Payment '.strtoupper(@$method);
		$new_ledger_data->status = 'active';
		$new_ledger_data->acct_num = @$acct11->acct_no;

		//$new_ledger_data->date01 = date('Y-m-d');
		$new_ledger_data->date01 = @$trans_date33_d;
		$new_ledger_data->period = date('Y-m-01');

		//$new_ledger_data->reff_no = $new_col->id;
		$new_ledger_data->reff_no = @$new_col->invoice_num;
		$new_ledger_data->coll_id = @$new_col->id;
		$new_ledger_data->save();


		//Record Payment Ledger
		$payment_ledger = new HwdJobCtrl;
		$payment_ledger->collection_ledger_update_dec272020($acct_id);

		return array('status'=>1, 'new_invoice'=>@$invoice_num);
	}//


	function get_acct_info_step1_billing_not_found($acct_id)
	{

		$arrear = getArrearV2($acct_id);
		$last_ledger = getLatestLeger($acct_id);

		$ttl = 0;
		$last_ledger = getLatestLeger($acct_id);

		if($last_ledger){$ttl = $last_ledger->ttl_bal;}


		$str1 = '
		<tr class="headings">
			<td class="ltxt">&nbsp;</td>
			<td class="rtxt">&nbsp;</td>
		</tr>

		<tr>';
		$str1 .= '<td>Billing</td>';
		$str1 .= '<td  class="rtxt">0</td>';
		$str1 .= '</tr>';

		$str1 .= '<tr>';
		$str1 .= '<td>Arrear</td>';
		$str1 .= '<td  class="rtxt">'.number_format(@$arrear->amount,2).'</td>';
		$str1 .= '</tr>';

		$str1 .= '<tr>';
		$str1 .= '<td>Penalty</td>';
		$str1 .= '<td  class="rtxt">0</td>';
		$str1 .= '</tr>';

		$str1 .= '<tr>';
		$str1 .= '<td>Senior</td>';
		$str1 .= '<td  class="rtxt red ">0</td>';
		$str1 .= '</tr>';

		$str1 .= '<tr>';
		$str1 .= '<td>Adjustment</td>';
		$str1 .= '<td  class="rtxt red ">0</td>';
		$str1 .= '</tr>';

		return array(
					'status' => 1,
					'bill_id' => 0,
					'total_bill' => number_format(@$ttl,2),
					'current_bill'  => number_format(0, 2),
					'bill_arear' => number_format((float) @$result1->arrears, 2),
					'collected' => 0,
					'amount_due' => number_format(@$ttl, 2),
					'bill_balance' => number_format($ttl, 2),
					'break_down' => $str1

				);
	}//


	function get_acct_info_step1($acct_id)
	{

		$result1 = BillingMdl::where('account_id', $acct_id)
				->orderBy('period', 'desc')
				->first();

		if(!$result1)
		{
			return  $this->get_acct_info_step1_billing_not_found($acct_id);
			//~ return array('status' => 0, 'msg' => 'Billing Not Found');
		}


		$arrear = getArrearV2($acct_id);
		$adjustment = (float) getLatestAdjustment($acct_id);


		$total_bill = ($result1->curr_bill + @$arrear->amount  + $result1->penalty);

		$total_bill -= $result1->discount;
		$total_bill -= $adjustment;

		$collection1 = Collection::
							//where('payment_date', '>=', $period_sub)
							where('payment_date', '>=', $result1->bill_date)->
							where('cust_id', $acct_id)
							->where('status', 'active')
							//~ ->get();
							->sum('payment');

		$ttl = 0;
		$last_ledger = getLatestLeger($acct_id);

		if($last_ledger)
		{
			$ttl = $last_ledger->ttl_bal;
		}


		//~ echo '<pre>';
		//~ print_r($last_ledger->toArray());
		//~ die();

		//~ $amount_due  = $total_bill - $collection1;

		//~ echo '<pre>	';
		//~ echo $collection1;
		//~ print_r($collection1->toArray());
		//~ print_r($result1->toArray());
		//~ die();

		$str1 = '
		<tr class="headings">
			<td class="ltxt">&nbsp;</td>
			<td class="rtxt">&nbsp;</td>
		</tr>

		<tr>';
		$str1 .= '<td>Billing</td>';
		$str1 .= '<td  class="rtxt">'.number_format(@$result1->curr_bill,2).'</td>';
		$str1 .= '</tr>';

		$str1 .= '<tr>';
		$str1 .= '<td>Arrear</td>';
		$str1 .= '<td  class="rtxt">'.number_format(@$arrear->amount,2).'</td>';
		$str1 .= '</tr>';

		$str1 .= '<tr>';
		$str1 .= '<td>Penalty</td>';
		$str1 .= '<td  class="rtxt">'.number_format(@$result1->penalty,2).'</td>';
		$str1 .= '</tr>';

		$str1 .= '<tr>';
		$str1 .= '<td>Senior</td>';
		$str1 .= '<td  class="rtxt red ">'.number_format(@$result1->discount,2).'</td>';
		$str1 .= '</tr>';

		$str1 .= '<tr>';
		$str1 .= '<td>Adjustment</td>';
		$str1 .= '<td  class="rtxt red ">'.number_format(@$adjustment,2).'</td>';
		$str1 .= '</tr>';


		return array(
			'status' => 1,
			'bill_id' => $result1->id,
			'total_bill' => number_format(@$total_bill,2),
			'current_bill'  => number_format(@$result1->curr_bill, 2),
			'bill_arear' => number_format((float) @$result1->arrears, 2),
			'collected' => $collection1,
			//'amount_due' => number_format(@$amount_due, 2),
			'amount_due' => number_format(@$ttl, 2),
			'break_down' => $str1,
			'bill_balance' => number_format($ttl, 2)
		);




		//~ echo $total_bill;
		//~ echo '<pre>';
		//~ print_r($result1->toArray());
	}


	function Activities()
	{
		$userId = Auth::id();

		$date1 = date('Y-m-d');
		if(!empty(@$_GET['dd'])){
			$date1 = date('Y-m-d', strtotime(@$_GET['dd']));
		}

		$colls = Collection::
			//where('collector_id', $userId)->
			with(['billing', 'billing.account', 'accounts'])
			->where('payment_date', 'like', $date1.'%')
			->where('collector_id', $userId)
			->orderBy('created_at', 'desc')
			->limit(300)
			->get();

		foreach($colls as $cc)
		{
			$cc->full_name = @$cc->accounts->lname.', '.@$cc->accounts->fname.' '.@$cc->accounts->mi;
			$cc->pay_date = date('F d, Y @ H:i A', strtotime(@$cc->payment_date));
			$cc->amount_txt = number_format(@$cc->payment,2);

			//~ $stat1 = 'Official Receipt';

			//~ if($cc->status == 'active'){
			//~ }

			//~ if($cc->status == 'collector_receipt'){
				//~ $stat1 = 'Collector Receipt';
			//~ }

			//~ if($cc->status == 'cancel_receipt'){
				//~ $stat1 = 'Cancelled Receipt';
			//~ }

			$stat1 = receipt_name1($cc->status);

			$cc->status2 = $stat1;
		}

		$user_info = User::find($userId)->toArray();
		return view('collections.activities', compact('colls'));

		echo '<pre>';
		print_r($colls->toArray());
	}//


	function main()
	{
		//~ die();
		$userId = Auth::id();

		//$data1=$this->__data1();
		$invoices = Invoice::where('stat', 'active')
					->where('uid', $userId)
					->orderBy('seq_start', 'asc')
					->limit(20)
					->get();

		//~ $userId = Auth::id();
		$user_info = User::find($userId)->toArray();

		$banks = Bank::where('status', 'active')->orderBy('bank_name', 'asc')->get();

		//~ return view('collections.main', compact('invoices', 'user_info', 'banks'));
		return view('collections.main2', compact('invoices', 'user_info', 'banks'));

		echo '<pre>';
		print_r($user_info->toArray());

	}



	function searchAcct($an, $ln)
	{
		//hanapin ang account basi sa mga inputs
		//$accts_x  =  Accounts::where('accounts.acct_status_key', $acct_stat->id);

		$accts_x  =  Accounts::select(DB::raw('accounts.*'));

		$accts_x->where(function($query) use($an, $ln){

			if(!empty($an)  && $an != 'none'){
				$query->where('accounts.acct_no', 'like', $an.'%');
			}

			if(!empty($ln)  && $ln != 'none'){
				$query->where('accounts.lname', 'like', $ln.'%');
			}

		});

		$accts_raw =  $accts_x->paginate(20);

		foreach($accts_raw as $acc1)
		{

			$f_bill_amount = 0;
			$f_arrear_amount = 0;
			$f_collection_amount = 0;
			$f_penalty_amount = 0;
			$f_discount = 0;
			$f_adjust = getLatestAdjustment($acc1->id);;

			$bill1 = getLatestBillingV1($acc1->id);
			$arrear1 = getArrearV2($acc1->id);
			$collection1 = 0;

			$bill_period_str = '';
			//$bill_period_str = 'Balance before Jan 1, 2019';


			if($arrear1)
			{
				$acc1->arrear1 = $arrear1->toArray();
				$period_sub = date('Y-m-d',strtotime(date('Y-m', strtotime($arrear1->period)).'-'.$acc1->zone_id.' + 1 day'));

				$collection1 = Collection::
									//where('payment_date', '>=', $period_sub)
									where('created_at', '>', $bill1->created_at)
									->where('cust_id', $acc1->id)
									->sum('payment');

				$f_arrear_amount = $arrear1->amount;
				$bill_period_str = date('F Y', strtotime($arrear1->period));
			}else{
				$acc1->arrear1=null;
			}


			if($bill1){
				$read1 = getReadingByPeriod($acc1->id, $bill1->period);
				$acc1->reading1 =  array($read1->toArray());
				$acc1->billing1 = $bill1->toArray();
				$f_bill_amount = $bill1->curr_bill;

				//~ $penalty1 = getPenalty($acc1->id, $bill1->period);
				$penalty1 = getPenaltyV2($acc1->id, $bill1->created_at);

				//~ echo $acc1->id;
				//~ echo '<pre>';
				//~ var_dump($penalty1);
				//~ die();

				if($penalty1){
					$f_penalty_amount = $penalty1->due_amount;
					$acc1->penalty = $penalty1->toArray();
				}else{
					$acc1->penalty = null;
				}

				$f_discount = $bill1->discount;

			}else{
				$acc1->reading1 = null;
				$acc1->billing1 = null;
				$acc1->penalty = null;
			}

			$acc1->collection1 = $collection1;
			$f_collection_amount = $collection1;


			//~ $f_bill_amount;
			//~ $f_arrear_amount;
			//~ $f_collection_amount;
			//~ $f_penalty_amount;
			//~ $f_discount;

			$acc1->collectable = ($f_bill_amount+$f_arrear_amount+$f_penalty_amount)-($f_discount+$f_adjust);
			$acc1->recievable = ($f_bill_amount+$f_arrear_amount+$f_penalty_amount)-($f_discount+$f_adjust);
			$acc1->collected  = $f_collection_amount;
			//$acc1->remaining_balance  = $acc1->collectable - $acc1->collected;
			$acc1->remaining_balance  = $acc1->collectable - $acc1->collected;

			$acc1->period_read = @$penalty1->penalty;
			$acc1->adjust = @$f_adjust;

			//~ echo '<pre>';
			//~ print_r($acc1->toArray());
			//~ die();
		}



		ob_start();
		$accts = $accts_raw->toArray();
		include_once('../resources/views/collections/search_res11.blade.php');
		$content_here = ob_get_contents();
		ob_end_clean();
		return  array('html'=>$content_here, 'js_data' => $accts);


		echo '<pre>';
		print_r($accts_raw->toArray());

	}

	function searchAcct2222($an, $ln)
	{

		//FIND ACCOUNT
		//FIND ACCOUNT
		//Kunin ang active na account
		$acct_stat = AccountMetas::where('meta_type', 'account_status')
			->where('old_id','1')
			->first();

		//hanapin ang account basi sa mga inputs
		$accts_x  =  Accounts::where('accounts.acct_status_key', $acct_stat->id);

		$accts_x->where(function($query) use($an, $ln){

			if(!empty($an)  && $an != 'none'){
				$query->where('accounts.acct_no', 'like', $an.'%');
			}

			if(!empty($ln)  && $ln != 'none'){
				$query->where('accounts.lname', 'like', $ln.'%');
			}

		});
		$accts_x->select(DB::raw('accounts.*'));
		//$accts_raw =  $accts_x->limit(20)->get();
		$accts_raw =  $accts_x->paginate(20);


		$period_request = HwdRequests::where('req_type', 'generate_billing_period_request')
			->where('status', 'completed')
			->orderBy('dkey1', 'desc')
			->first();

		// $period = date('Y-m-28');
		// $per_arr = array();
		// $per_arr[] = date('Y-m-28');

		$period = $period_request->dkey1;
		$per_arr = array();
		$per_arr[] = $period_request->dkey1;

		//~ echo $period;
		//~ die();

		//~ echo '<pre>';
		//~ print_r($per_arr);
		//~ die();

		//$period_request

		//$per_arr[] = date('Y-m-28', strtotime($period.' -1 Month '));
		//$per_arr[] = date('Y-m-28', strtotime($period.' -2 Month '));

		// echo '<pre>';
		// print_r($accts_raw->toArray());
		// die();

		//Isa isahin ang bawat account
		foreach($accts_raw as $acc1)
		{
			//ihanda ang total_collection
			$billing_total = 0;

			$arrear = 0;

			//kunin ang reading ng kasalukuyang account
			//kunin lamang ang naka billed na account
			//kunin lamang ang kasalukuyang period
			//ang period ay hindi na kailangan kunin lamang ang huling bill at reading
			$read11 = Reading::where('account_id', $acc1->id)
			->where('bill_stat', 'billed')
			//->whereIn('period', $per_arr)
			->orderBy('period', 'desc')
			->limit(1)
			->get();

			//~ echo '<pre>';
			//~ print_r($read11->toArray());
			//~ die();


			//ihanda ang lalagyan ng billing id
			$col_ids = array();

			//isa isahin ang nakuhang reading
			foreach ($read11 as $rr1)
			{
				//kunin ang billing basi sa reading id
				$billing1 = BillingMdl::where('reading_id', $rr1->id)
					->where('status', 'active')
					->first();



				if(!$billing1)
				{
					continue;
					// return array('err' => '0001', 'msg' => 'No billing for ')
				}


				//collectahin ang billing id
				$col_ids[] = $billing1->id;

				//idagdag sa billing total
				$billing_total += $billing1->billing_total;

				//Idag-dag ang arrear sa total billing
				//$billing_total += $billing1->arrears;
				$arrear_data = getArrearV1($acc1->id, $period);
				if($arrear_data){
					$billing_total += $arrear_data->amount;
					$billing1->arrears = $arrear_data->amount;
				}


				//idag-dag ang penalty
				//~ $billing_total += $billing1->penalty;
				$penalty_data = getPenalty($acc1->id, $period);
				if($penalty_data){
					$billing_total += $penalty_data->due_amount;
					$billing1->penalty = $penalty_data->due_amount;
				}




				//idag-dag ang penalty
				$billing_total -= (float) $billing1->discount;

				//Gawing numero na may dalawang zero ang discount at penalty
				$billing1->discount = number_format($billing1->discount,2);
				$billing1->penalty = number_format($billing1->penalty,2);

				//Idagdag ang nakuhang billing sa reading upang itoy ihanda
				$rr1->billing1 = $billing1->toArray();

			}//

			// pagkatapos makuha ang billing total
			// Collectahin ang lahat ng collection basi doon sa na-ipong billing id
			$coll = Collection::whereIn('billing_id', $col_ids)
				->orderBy('id', 'desc')
				->get();


			//$bill_date = date('Y-m-d', strtotime($billing1->bill_date.' -2 month'));
			$bill_date = $billing1->bill_date;
			$next_bill_date = date('Y-m-d', strtotime($bill_date.' +1 month'));

			$total_collected = Collection::where('payment_date','>', $bill_date)
				->where('payment_date','<=', $next_bill_date)
				->where('cust_id', $billing1->account_id)
				->sum('payment');
				//->get();


			//~ echo '<pre>';
			//~ echo $bill_date;
			//~ echo '<br />';
			//~ echo $next_bill_date;
			//~ echo '<br />';
			//~ print_r($total_collected);
			//~ die();



			//pagkatapos makuha ang collection isa isahin at kunin lamang ang BAYAD o payment at e TOTAL

			$collected = 0;
			$advance_pay = 0;

			foreach($coll as $cc)
			{
				$collected += $cc->payment;
			}

			//Overide the collected
			//~ echo $total_collected;
			//~ die();

			$collected = $total_collected;

			//Kung may nakolecta
			if($collected != 0)
			{
				//At kung ang balance ay mababa pa sa zero
				if(@$coll[0]->balance_payment < 0)
				{
					//edag dag sa nacolecta
					$collected += abs(@$coll[0]->balance_payment);
					$advance_pay = abs(@$coll[0]->balance_payment);
				}
			}
			//~ die();

			//~ echo $billing_total;
			//~ die();


			$acc1->reading1 = (array) $read11->toArray();
			$acc1->collections = $coll->toArray();
			$acc1->collectable = $billing_total;

			$acc1->collected = $collected;
			$acc1->advance_pay = $advance_pay;
			$acc1->remaining_balance = $billing_total - $collected;

			//~ echo '<pre>';
			//~ print_r($acc1->toArray());
			//~ die();

			$acc1->period_read =  date('F Y', strtotime(@$acc1->reading1[0]['period']));


		}//


		echo '<pre>';
		print_r($accts_raw->toArray());
		die();


		/**/
		ob_start();
		$accts = $accts_raw->toArray();
		include_once('../resources/views/collections/search_res11.blade.php');
		$content_here = ob_get_contents();
		ob_end_clean();

		//~ echo '<pre>';
		//~ print_r($accts);
		//~ die();

		return  array('html'=>$content_here, 'js_data' => $accts);
		/*









		*/


		echo $content_here;
		die();
		/**/
		echo '<pre>';
		print_r($per_arr);
		print_r($per_arr);
		print_r($accts_raw->toArray());
		die();

		return;
		return;
		return;

		$accts_x->has('reading3');
		$accts_x->with('reading3.billing.collection');
		//$accts_raw	 =  $accts_x->paginate(20);
		$accts_raw	 =  $accts_x->limit(20)->get();
		//FIND ACCOUNT END
		//FIND ACCOUNT END


		//GENERATE  JOINTS
		/**/
		foreach($accts_raw as $A1)
		{
			foreach($A1->reading3 as $A2)
			{
					$A2->billing;
					//$A2->billing->collection;
			}
		}
		/**/
		//GENERATE  JOINTS END


		//echo '<pre>';
		//print_r($accts_raw->toArray());
		//die();


		$accts['data']  =  $accts_raw->toArray();
		$data1  =  $this->__data1();
		extract($data1);

		foreach($accts['data'] as  $A1_key => $A1)
		{

				$grand_total_collectable = 0;
				$total_collection = 0;

				foreach($A1['reading3'] as  $read3_key=> $read3)
				{

						/*CALCULATE THE CONSUMPTION RATE START*/
						/*CALCULATE THE CONSUMPTION RATE START*/
						if(!empty($read3['init_reading'])){
							$read3['prev_reading'] = $read3['init_reading'];
							$accts['data'][$A1_key]['reading3'][$read3_key]['prev_reading'] = $read3['init_reading'];
						}else{

							$prev_read3_key  = $read3_key + 1;

							if(!empty($accts['data'][$A1_key]['reading3'][$prev_read3_key])){

								$accts['data'][$A1_key]['reading3'][$read3_key]['prev_reading'] =
											$accts['data'][$A1_key]['reading3'][$prev_read3_key]['curr_reading'];

								$read3['prev_reading'] =
											$accts['data'][$A1_key]['reading3'][$prev_read3_key]['curr_reading'];

							}//endif

						}//endif

						$ttl_consumption = (int) $accts['data'][$A1_key]['reading3'][$read3_key]['curr_reading']  -
									(int) $accts['data'][$A1_key]['reading3'][$read3_key]['prev_reading'];

						$read3['A_ttl_consump']  =
								$accts['data'][$A1_key]['reading3'][$read3_key]['A_ttl_consump'] =
										$ttl_consumption;
						/*CALCULATE THE CONSUMPTION RATE END*/
						/*CALCULATE THE CONSUMPTION RATE END*/

						/*CALCULATE THE PRICE RATE START*/
						/*CALCULATE THE PRICE RATE START*/
						$current_rate = $A1['acct_type_key'];
						$sub_total = 0;

						foreach($bill_rates[$current_rate] as $ats){

							$min1 = $ats['min_cu'];
							$max1 = $ats['max_cu'];

							 if(($min1 <= $ttl_consumption) && ($ttl_consumption <= $max1)){
								 if($ats['price_rate'] == 0){
									 $sub_total =  $ats['min_charge'];
								 }else{
									 $sub_total =  ((($ttl_consumption - $min1) * $ats['price_rate'])
											+ $ats['min_charge']) + $ats['price_rate'];
								 }

								 break;
							 }

						}//Endforeach

						$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_sub_total'] = $sub_total;
						$read3['billing']['F_sub_total'] = $sub_total;

						/*CALCULATE THE PRICE RATE START  END*/
						/*CALCULATE THE PRICE RATE START  END*/

						/*CALCULATE THE DISCOUNT RATE START*/
						/*CALCULATE THE DISCOUNT RATE START*/

						$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_less_amout'] =  $read3['billing']['F_less_amout'] = '';
						$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_less_percent'] = $read3['billing']['F_less_percent'] = '';
						$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_less_name'] =  $read3['billing']['F_less_name']  = '';
						$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_billing_total'] = $read3['billing']['F_billing_total']  = $sub_total;

						if(!empty($discounts[$A1['acct_discount']])){

							$dis_name = $discounts[$A1['acct_discount']][0]['dis_name'];
							$dis_value = (float) $discounts[$A1['acct_discount']][0]['dis_value'];
							$less_desc =  $sub_total * ($dis_value / 100)  ;

							$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_less_amout'] =  $read3['billing']['F_less_amout'] = $less_desc;
							$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_less_percent'] = $read3['billing']['F_less_percent'] = $dis_value;
							$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_less_name'] =  $read3['billing']['F_less_name']  = $dis_name;
							$accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_billing_total'] = $read3['billing']['F_billing_total']  = $sub_total-$less_desc;

						}
						$grand_total_collectable = $grand_total_collectable + $accts['data'][$A1_key]['reading3'][$read3_key]['billing']['F_billing_total'];
						/*CALCULATE THE DISCOUNT RATE END*/
						/*CALCULATE THE DISCOUNT RATE END*/

						//$total_collection
						if(!empty($read3['billing']['collection'])){
							foreach($read3['billing']['collection']  as $coll){
								$total_collection += $coll['payment'];
							}
						}



				}//EndForeach  $A1['reading3']

				if(!empty($accts['data'][$A1_key]['reading3'][0]['billing']['collection'])){
					$total_collection += abs($accts['data'][$A1_key]['reading3'][0]['billing']['collection'][0]['balance_payment']);
				}

				$remaining_balance = $grand_total_collectable;


				$accts['data'][$A1_key]['grand_total_collectable'] = $grand_total_collectable;
				$accts['data'][$A1_key]['total_collection'] = $total_collection;
				$accts['data'][$A1_key]['remaining_balance'] = $grand_total_collectable - $total_collection;

				$A1 = $accts['data'][$A1_key];

				//echo '<pre>';
				//print_r($coll->toArray());
				//die();


		}//$accts['data']


		//echo '<pre>';
		//print_r($accts);
		//die();
		//return view('collections.search_res', compact('accts'));

		ob_start();
		include_once('../resources/views/collections/search_res.blade.php');
		$content_here = ob_get_contents();
		ob_end_clean();

		return  array('html'=>$content_here, 'js_data' => $accts);


	}

	function Invoices()
	{

		$userId = Auth::id();

		$invoices = Invoice::
						//~ orderBy('seq_start', 'desc')
						orderBy('id', 'desc')
							->where('uid', $userId)
								->paginate(1000);

		return view('collections.invoice', compact('invoices'));
	}

	function makePaymentNonWater(Request $request)
	{

		return;
		return;
		return;
		return;


		extract($_POST);
		$acct = $data;


		$userId = Auth::id();
		$customer_id= $acct['id'];
		$zone_id = $acct['zone_id'];



		$ot_pay = OtherPayable::find($non_water_item);
		if(!$ot_pay){
			return array('status'=> 'error', 'code'=>'4');
		}

		//~ if( < $ot_pay->paya_amount)
		//~ {
			//~ return array('status'=> 'error', 'code'=>'5');
		//~ }


		//~ echo '<pre>';
		//~ print_r($acct->toArray());
		//~ die();


		//~ $bank_id;
		//~ $bank_check;
		//~ $bank_info;

		$pay_type = 'cash';

		if($bank_id != 0)
		{
			if(empty($bank_check))
			{
				return array('status'=> 'error', 'code'=>'6'); // Check number is required
			}
			if(empty($bank_info))
			{
				return array('status'=> 'error', 'code'=>'6'); // Bank information is required
			}

			$pay_type = 'check';
		}


		//add_non_water_bill
		LedgerCtrl::add_non_water_bill($customer_id, array(
				'acct_num' => $acct['acct_no'],
				'paya_amount' =>$ot_pay->paya_amount,
				'paya_title' => $ot_pay->paya_title,
				'reff_no' => $ot_pay->id
			));

		sleep(1);


		if($cash1 > 0)
		{
			$pre_pay = $this->pre_payment1();

			if($pre_pay['status'] == 'error')
			{
				return $pre_pay['status'].' - '.$pre_pay['code'];
			}


			$new_payment = new Collection;
			$new_payment->collection_type = 'non_water_bill_payment';
			$new_payment->status = 'active';
			$new_payment->payment_date = date('Y-m-d h:i:s');
			//$new_payment->billing_id = $billing_id;
			$new_payment->invoice_num = $invoice;
			$new_payment->collector_id = $userId;
			$new_payment->payment = $cash1;//$ot_pay->paya_amount;
			//$new_payment->balance_payment = ;
			$new_payment->cust_id = $customer_id;
			$new_payment->zone_id = $zone_id;
			$new_payment->pay_type = $pay_type;
			//$new_payment->cust_id = $customer_id;
			$new_payment->bank_id = $bank_id;
			$new_payment->check_no = $bank_check;
			$new_payment->bank_info = $bank_info;

			$new_payment->save();



			LedgerCtrl::add_payment_none_water($customer_id, array(
					'amount' => $cash1,
					'invoice' => $invoice,
					'acct_num' => $acct['acct_no'],
					'reff_no' => $invoice
				));

		}else{
			//
		}


		$invoices = Invoice::where('stat', 'active')
					->orderBy('seq_start', 'asc')
					->limit(20)
					->get();

		$new_set_str = '';
		foreach($invoices as $invs):
			$new_set_str.='<option value="'.$invs->id.'">'.$invs->seq_start.' to '.$invs->seq_end.'</option>';
		endforeach;

		return array('status'=> 'success', 'invs' => $invoices, 'inv_set' => $new_set_str);

	}//End Method

	function makePayment(Request $request)
	{

		return;
		return;
		return;
		/**/

		extract($_POST);
		$acct = $data;

		$userId = Auth::id();

		$pre_pay = $this->pre_payment1();

		if($pre_pay['status'] == 'error')
		{
			return $pre_pay['status'].' - '.$pre_pay['code'];
		}


		$amount_val = $amount;

		// $advance_payment  =  false;
		// $str_pay = array();
		// $pre_pay = $this->payment_process1();

		//~ echo '<pre>';
		//~ print_r($acct);
		//~ die();



		if(count($acct['reading1']) == 0)
		{
			return array('status'=> 'error', 'code' => '7');
		}

		$reading_data = $acct['reading1'][0];
		$billing_data = $acct['billing1'];

		//Ihanda ang mga babayaran  BILLING, ARREAR at  PENALTY
		$total_billing = $billing_data['billing_total'] + $billing_data['arrears'] + ((double) @$billing_data['penalty']) ;
		$billing_id =  $billing_data['id'];
		$customer_id =  $billing_data['account_id'];
		$zone_id =  $acct['zone_id'];

		$coll = Collection::where('billing_id', $billing_id)->sum('payment');

		$total_balance = $total_billing - $coll;

		if($total_balance <= 0)
		{
			return array('status'=> 'error', 'code' => '8', 'msg' => 'Payment is already made.');
		}

		$advance_payment = $total_balance - $amount_val;



		$pay_type = 'cash';

		if($bank_id != 0)
		{
			if(empty($bank_check))
			{
				return array('status'=> 'error', 'code'=>'6'); // Check number is required
			}
			if(empty($bank_info))
			{
				return array('status'=> 'error', 'code'=>'6'); // Bank information is required
			}

			$pay_type = 'check';
		}





		$new_payment = new Collection;
		$new_payment->collection_type = 'bill_payment';
		$new_payment->status = 'active';
		$new_payment->payment_date = date('Y-m-d h:i:s');
		$new_payment->billing_id = $billing_id;
		$new_payment->invoice_num = $invoice;
		$new_payment->collector_id = $userId;
		$new_payment->payment = $amount_val;
		$new_payment->balance_payment = $advance_payment;
		$new_payment->cust_id = $customer_id;
		$new_payment->zone_id = $zone_id;
		$new_payment->pay_type = $pay_type;

		$new_payment->bank_id = $bank_id;
		$new_payment->check_no = $bank_check;
		$new_payment->bank_info = $bank_info;


		$new_payment->save();

		$inv1 = Invoice::where('seq_end', '<=', $invoice)
				->where('uid', $userId)
			    ->where('stat','active')
			    ->get();

	    if($inv1->count() != 0)
		{
			foreach($inv1 as $vv)
			{
				$vv->stat = 'full';
				$vv->save();
			}
		}

		$invoices = Invoice::where('stat', 'active')
					->where('uid', $userId)
					->orderBy('seq_start', 'asc')
					->limit(20)
					->get();

		$mmm = array(
			'invoice' => $invoice,
			'payment_id' => $new_payment->id,
			'billing_ids' => $billing_id,
			'full_name' => $acct['lname'].', '.$acct['fname'].' '.$acct['mi'],
			'acct_no' =>$acct['acct_no'],
			'amount' => $amount,
			'remaining_balance' => $total_balance,
			'invoice' => $invoice,
			'cash1' => $cash1,
			'period' =>$billing_data['period'],
			'total_billing' => $total_billing,
			'colllected' => $coll,
			'arrears' => $billing_data['arrears'],
			'current_bill' => $billing_data['billing_total'],
			'total_remaining_balance' => $advance_payment,
			//'label1' => $str_pay,
		);

		//~ HwdLedgerCtrl::CollectionProcess($acct['id'],$mmm);


		//~ LedgerCtrl::add_payment($customer_id, array(
				//~ 'amount' => $amount,
				//~ 'invoice' => $invoice
			//~ ));

		 $ledger2 = LedgerData::
				where('acct_id', $customer_id)
				->where('status','active')
				->orderBy('date01', 'desc')
				->orderBy('zort1', 'desc')
				//~ ->orderBy('date01', 'desc')
				->orderBy('id', 'desc')
				->first();

		$ttl_bal = 0;

		if($ledger2){
			$ttl_bal = $ledger2->ttl_bal;
		}

		//$ttl_bal+=$total_billing;
		$ttl_bal-=$amount;

		$new_ledger_data = new LedgerData;
		$new_ledger_data->led_type ='payment';
		$new_ledger_data->acct_id =$customer_id;
		$new_ledger_data->bill_id = $billing_id;
		$new_ledger_data->ttl_bal = $ttl_bal;
		$new_ledger_data->payment = $amount;
		$new_ledger_data->ledger_info = 'Bill Payment';
		$new_ledger_data->status = 'active';
		$new_ledger_data->acct_num = $acct['acct_no'];
		$new_ledger_data->date01 = date('Y-m-d');
		$new_ledger_data->period = date('Y-m-').'01';
		$new_ledger_data->reff_no = $new_payment->id;
		$new_ledger_data->save();


		$this->Print1($mmm);

		$new_set_str = '';
		foreach($invoices as $invs):
			$new_set_str.='<option value="'.$invs->id.'">'.$invs->seq_start.' to '.$invs->seq_end.'</option>';
		endforeach;


		return array('status'=> 'success', 'invs' => $invoices, 'inv_set' => $new_set_str);
		return;
		return;
		return;
		return;
		return;

			//Kunin ang ang reading at ebaliktad ito para e processo ang pinaka lumang reading
			$rev_me = array_reverse($acct['reading1']);
			foreach($rev_me as $rev)
			{

				//
				if($amount_val == 0){break;}

				elseif($amount_val < 0){
					$amount_val = abs($amount_val);
					$advance_payment = true;
				}

				$bill_id = $rev['billing1']['id'];
				$bill_payable = $rev['billing1']['billing_total'];

				$period1 = date('M Y', strtotime($rev['billing1']['period']));
				$str_pay[] = $period1;

				$coll = Collection::where('billing_id', $bill_id)->get()->toArray();

				$total_colected = 0;
				foreach($coll as $cc){
					$total_colected += $cc['payment'];
				}
				$bill_payable = $bill_payable - $total_colected;

				if($bill_payable <= 0){
					continue;
				}

				//$new_payment->collector_id = '';
				//$new_payment->invoice_num = '';

				$new_payment = new Collection;
				$new_payment->collection_type = 'bill_payment';
				$new_payment->status = 'active';
				$new_payment->payment_date = date('Y-m-d h:i:s');
				$new_payment->billing_id = $bill_id;
				$new_payment->invoice_num = $invoice;
				$new_payment->collector_id = $userId;


				if($amount_val >= $bill_payable){$new_payment->payment = $bill_payable;}
				else{$new_payment->payment = $amount_val;}

				$new_payment->balance_payment = $bill_payable - $amount_val;
				$new_payment->save();

				if(($bill_payable - $amount_val) <= 0){
					$amount_val = $bill_payable - $amount_val;
				}else{
					$amount_val = 0;
				}

			}//Endforeach

			//SELECT * FROM `invoices` WHERE seq_end <= 10299 AND stat='active'
			//$invoice

			 $inv1 = Invoice::where('seq_end', '<=', $invoice)
					->where('stat','active')
					->get();

			if($inv1->count() != 0)
			{

				foreach($inv1 as $vv){
					$vv->stat = 'full';
					$vv->save();
				}

			}


			$invoices = Invoice::where('stat', 'active')
						->orderBy('seq_start', 'asc')
						->limit(20)
						->get();

			HwdLedgerCtrl::CollectionProcess($acct['id']);

			//$invoice
			//$new_payment->id
			//$new_payment->billing_id
			//$str_pay
			$mmm = array(
				'invoice' => $invoice,
				'payment_id' => $new_payment->id,
				'billing_ids' => $new_payment->billing_id,
				'label1' => $str_pay,
				'full_name' => $acct['lname'].', '.$acct['fname'].' '.$acct['mi'],
				'acct_no' =>$acct['acct_no'],
				'amount' => $amount,
				'remaining_balance' => $acct['remaining_balance'],
				'invoice' => $invoice,
				'cash1' => $cash1

			);

			$this->Print1($mmm);

			$new_set_str = '';
			foreach($invoices as $invs):
				$new_set_str.='<option value="'.$invs->id.'">'.$invs->seq_start.' to '.$invs->seq_end.'</option>';
			endforeach;

			return array('status'=> 'success', 'invs' => $invoices, 'inv_set' => $new_set_str);


		/**/
	}

	private function payment_process1()
	{
	}

	private function pre_payment1()
	{
		extract($_POST);

		//Tingnan kung ang invoice ay valid
		$invoice_exist = Collection::where('invoice_num', $invoice)->first();

		// kung hindi valid itigil ang processo
		if($invoice_exist){
			return array('status'=> 'error', 'code' => '1');
		}


		//tingnan ulit kung valid
		$inv_data = Invoice::find($inv_set);

		// hominto at itigil ang processo
		if(!$inv_data)
		{
			return array('status'=> 'error', 'code'=> '2');
		}


		// tinanan kung ang recipt ay nasa pagitan ng ginagamitan ng reciept
		if(($inv_data->seq_start <= $invoice) && ($invoice <= $inv_data->seq_end))
		{

			// kung parrehas ang markahan ng full o puno na ang set ng reciept
			if($inv_data->seq_end == $inv_data->seq_c)
			{
				$inv_data->stat = 'full';
				$inv_data->save();
			}
			//at kung hindi parehas ay dagdagan lamang ang sequuece
			else
			{
				$inv_data->seq_c = $invoice + 1;
				$inv_data->save();
			}

		}else{
			return array('status'=> 'error', 'code'=>'3');
		}



		return array('status'=> 'success');
	}//end





	private function  __data1()
	{

		$acct_type =
				AccountMetas::where('meta_type', 'account_type')
					->where('status', 'active')
					->orderBy('meta_name', 'asc')
					->get()
					->toArray();

		$bill_rates_raw =
				BillingMeta::where('meta_type', 'billing_rates')
					->where('status', 'active')
					->get()
					->toArray();

		$zones  =
				Zones::where('status', '!=', 'deleted')
					->orderBy('zone_name', 'asc')
					->get()
					->toArray();

		$bill_discount =
				BillingMeta::where('meta_type', 'billing_discount')
					->where('status', 'active')
					->get()
					->toArray();


		//PREPARATION START
		$bill_rates = array();
		foreach($bill_rates_raw as $rww){
			$arr1 = (array) json_decode($rww['meta_data']);
			$bill_rates[$arr1['acct_type']][] = $arr1;
		}

		$discounts  = array();
		foreach($bill_discount as $rww){
			$arr1 = (array) json_decode($rww['meta_data']);
			$discounts[$rww['id']][] = $arr1;
			//$discounts[$arr1['dis_id']][] = $arr1;
		}

		$acct_type_list  = array();
		foreach($acct_type as $rww){
			$acct_type_list[$rww['id']] = $rww;
		}

		//PREPARATION START

		/*
		echo '<pre>';
		print_r($acct_type);
		die();
		/**/

		return  compact('acct_type_list',  'zones', 'bill_rates', 'discounts');


	}


	function InvoiceAddNew(Request $request)
	{
		$userId = Auth::id();


		$new_inv = new Invoice;
		$new_inv->seq_start = (int) $_POST['seq_start'];
		$new_inv->seq_end = (int) $_POST['seq_end'];
		$new_inv->date_stat = date('Y-m-d H:i:s');
		$new_inv->seq_c = (int) $_POST['seq_start'];
		$new_inv->stat = 'pending';
		$new_inv->uid = $userId;
		$new_inv->save();

		$req1 = new HwdRequests;
		$req1->req_type = 'invoice_request';
		$req1->reff_id = $new_inv->id;
		$req1->remarks = 'Invoice approval for  '.$new_inv->seq_start.' to '.$new_inv->seq_end;
		$req1->status = 'pending';
		$req1->other_datas = @json_encode($_POST);
		$req1->save();

		$request->session()->flash('success', 'New Set  Invoice Added');
		return Redirect::to(URL::previous());


	}

	function InvoiceGetCurrent()
	{
		$userId = Auth::id();

		$invoices =
			Invoice::where('stat', 'active')
				->where('uid', $userId)
				->orderBy('seq_start', 'asc')
				->limit(5)
				->get();

		return $invoices;
		echo '<pre>';
		print_r($invoices->toArray());

	}


	function CashierLogout()
	{
		Auth::logout();
		return redirect('/');
	}


	function Print1($dd)
	{

		return;
		return;
		return;

		//~ return;
		extract($dd);
		// $mmm = array(
		// 	'invoice' => $invoice,
		// 	'payment_id' => $new_payment->id,
		// 	'billing_ids' => $new_payment->billing_id,
		// 	'label1' => $str_pay
		// );

		//$label1_l = implode(', ',$label1);

		//
		// return;
		// return;
		// return;

		//~ $connector = new FilePrintConnector("/dev/usb/lp0");
		$connector = new FilePrintConnector("/Applications/XAMPP/xamppfiles/htdocs/hwd_print/payment.txt");
		$printer = new Printer($connector);
		$printer->initialize();

		$top_mar = 10;

		for($y=1;$y<=$top_mar;$y++){
			$printer->text("\n");
		}

		$printer->text('                  '.$payment_id."--$invoice\n\n");
		$printer->text('                  '.date('M d, Y')."\n");
		$printer->text('      '.WD_NAME."\n\n");
		$printer->text('      '.$full_name."\n");
		$printer->text('      '.$acct_no."\n");
		$printer->text("\n \n \n \n ");
		$kk = 0;

		$post_amount = $amount;

		if($remaining_balance > $amount)
		{
			//~ $printer->text('    Partial Payment'."\n");
		}else{
			$post_amount = $remaining_balance;
		}

		//~ foreach($label1 as $ll1)
		//~ {
			//~ if($kk == 0){
				//~ $printer->text('     WB - '.$ll1.'          '.number_format($post_amount,2)."\n");
			//~ }else{
				//~ $printer->text('     WB - '.$ll1.''."\n");
			//~ }
			//~ $kk ++;
		//~ }

		if($remaining_balance > $amount)
		{
			//~ $printer->text('     Balance of '.$remaining_balance.''."\n");
		}

		//$change =  $amount - $remaining_balance;
		$change =  $cash1 - $post_amount;

		if($change < 0){
			$change = 0;
		}

		$printer->text("\n \n \n \n ");
		$printer->text('   Recieve : '.number_format($cash1,2)."\n");
		$printer->text('    Change : '.number_format($change, 2)."\n");
		$printer->text('                            '.number_format($post_amount,2)."\n");
		$printer->text("\n . \n");


		$m = explode('.', $post_amount);
		$numW1 = num2word(@$m[0]).' Pesos ';
		$numW2 = num2word(@$m[1]);
		if(!empty($numW2)){
			$numW2 = ' And '.$numW2.' Centavo';
		}

		$string1 = $numW1.$numW2;
		//$string1 = 'Nine Thousand Nine Hundred Ninety  Nine Pesos Only';
		$str2 = array_filter(explode(' ', $string1));


		$xxx  = 0;
		$xxx_str = '';
		$printer->text("     ");

		foreach($str2 as $ss2){
			$printer->text($ss2." ");
			if($xxx >= 3){
				$printer->text("\n");
				$printer->text("     ");
				$xxx = 0;
			}else{
				$xxx++;
			}
		}
		//$printer->text("\n . \n");
		$printer->cut();
		$printer->feedForm();
		$printer->close();
	}//


	function Reports()
	{
		$zones = Zones::where('status', '!=', 'deleted')->get();
		return view('collections.reports', compact('zones'));
	}


	function ReportDaily_v3($list_type='daily', $cmd=0, $uid=0)
	{
		$user = Auth::user();


		//222222222
		if($cmd == 2)
			{$user = User::find($uid);}
		//222222222


		$CY = date('Y');
		$PY = date('Y');

		$date1 = date('Y-m-d');
		$title1_x = 'Daily Collection Report';

		if(!empty(@$_GET['dd'])){
			$date1 = @$_GET['dd'];
		}

		$zonex = 0;
		if(!empty(@$_GET['zz'])){
			$zonex = @$_GET['zz'];
		}

		$zonex = (int) $zonex;

		$is_zon = Zones::find($zonex);


		$date1_x = date('l, F d, Y', strtotime($date1));

		if($list_type == 'monthly')
		{
			$date1 = date('Y-m', strtotime($date1));
			$title1_x = 'Monthly Collection Report';
			$date1_x = date('F Y', strtotime($date1));
		}


		$by_zone = '';
		if($is_zon){
			$by_zone = '  '.$is_zon->zone_name;
		}


		/*
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 */

		 $date_33  = date('Y-m-d', strtotime($date1));
		 $period11 = date('Y-m', strtotime($date1));
		 $by_mon   = date('Y-m', strtotime($date1));

		 $sql101 = "
				SELECT MIN(CC1.invoice_num) AS S1, MAX(CC1.invoice_num) as S2
				FROM `collections` as CC1 WHERE CC1.payment_date like ? AND collector_id=?
		 ";

		 $sql101_data = [$date1.'%', $user->id];

		 //ALL
		 if($cmd == 1)
		 {
			 $sql101 = "
					SELECT MIN(CC1.invoice_num) AS S1, MAX(CC1.invoice_num) as S2
					FROM `collections` as CC1 WHERE CC1.payment_date like ?
			 ";
			 $sql101_data = [$date1.'%'];
		 }//

		 $rs22 = DB::select($sql101, $sql101_data);

		 $S1 = $rs22[0]->S1;
		 $S2 = $rs22[0]->S2;

		 if(empty($S1) || empty($S2)){


			//222222222
			 if($cmd == 2)
			 {
				 return  array();
			 }
			 //222222222

			 echo 'No collection for '.date('F d, Y', strtotime($date1));
			 die();
		 }


		 $sql102 = "
				 SELECT CC1.*,CONCAT(AA.acct_no, ' ', AA.fname,' ',AA.lname) as full_name FROM `collections` as CC1
				 LEFT JOIN accounts AA on AA.id=CC1.cust_id
				 WHERE EXISTS
				 (
						 SELECT * FROM (SELECT MAX(id) AS DD,  invoice_num FROM `collections`
						 WHERE (invoice_num BETWEEN ? AND ?)
								  AND collector_id=?
						 GROUP BY invoice_num) AS TAB1
						 WHERE TAB1.DD = CC1.id
				 )
				 AND CC1.payment_date like ?
				 ORDER BY CC1.invoice_num ASC
		 ";
		 $sql102_data = [$S1, $S2, $user->id, $date1.'%'];

		 //ALL
		 if($cmd == 1)
		 {
			 $sql102 = "
					 SELECT CC1.* FROM `collections` as CC1
					 WHERE EXISTS
					 (
							 SELECT * FROM (SELECT MAX(id) AS DD,  invoice_num FROM `collections`
							 WHERE (invoice_num BETWEEN ? AND ?)
							 GROUP BY invoice_num) AS TAB1
							 WHERE TAB1.DD = CC1.id
					 )
					 AND CC1.payment_date like ?
					 ORDER BY CC1.invoice_num ASC
			 ";
			 $sql102_data = [$S1, $S2, $date1.'%'];
		 }

		 $rs1 = DB::select($sql102, $sql102_data);


		 $coll = $rs1;


		 //~ echo '<pre>';
		 //~ print_r($rs1);
		 //~ die();


 		 //222222222
		 if($cmd == 2)
		 {
			 return compact(
						'coll',
						'user'
				);
		 }
 		 //222222222


	}

	function ReportDaily_v2($list_type='daily', $cmd=0, $uid=0)
	{
		
		$user = Auth::user();


		 if($cmd == 2)
		 {
			 $user = User::find($uid);
			 //~ echo '<pre>';
			 //~ print_r($user->toArray());
			 //~ die();
		 }
		 
		$dd = @$_GET['dd'];
		$iid = (int) @$_GET['iid'];		
		
		if($iid != 0){
			 $user = User::find($iid);
		}
		
		//~ echo $cmd;
		


		$CY = date('Y');
		$PY = date('Y');

		$date1 = date('Y-m-d');
		$title1_x = 'Daily Collection Report';

		if(!empty(@$_GET['dd'])){
			$date1 = @$_GET['dd'];
		}
		
		//~ echo $date1;
		//~ die();

		$zonex = 0;
		if(!empty(@$_GET['zz'])){
			$zonex = @$_GET['zz'];
		}

		$zonex = (int) $zonex;

		$is_zon = Zones::find($zonex);


		$date1_x = date('l, F d, Y', strtotime($date1));

		if($list_type == 'monthly')
		{
			$date1 = date('Y-m', strtotime($date1));
			$title1_x = 'Monthly Collection Report';
			$date1_x = date('F Y', strtotime($date1));
		}


		$by_zone = '';
		if($is_zon){
			$by_zone = '  '.$is_zon->zone_name;
		}


		/*
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 */
		 
		 //~ echo $user->id;
		 //~ die();

		 $date_33  = date('Y-m-d', strtotime($date1));
		 $period11 = date('Y-m', strtotime($date1));
		 $by_mon   = date('Y-m', strtotime($date1));

		 $sql101 = "
				SELECT MIN(CC1.invoice_num) AS S1, MAX(CC1.invoice_num) as S2
				FROM `collections` as CC1 WHERE CC1.payment_date like ? AND collector_id=?
		 ";

		 $sql101_data = [$date1.'%', $user->id];

		 //ALL
		 if($cmd == 1)
		 {
			 $sql101 = "
					SELECT MIN(CC1.invoice_num) AS S1, MAX(CC1.invoice_num) as S2
					FROM `collections` as CC1 WHERE CC1.payment_date like ?
			 ";
			 $sql101_data = [$date1.'%'];
		 }//

		 $rs22 = DB::select($sql101, $sql101_data);

		 $S1 = $rs22[0]->S1;
		 $S2 = $rs22[0]->S2;

		 if(empty($S1) || empty($S2)){

			 if($cmd == 2)
			 {
				 return  array();
			 }

			 echo 'No collection for '.date('F d, Y', strtotime($date1));
			 die();
		 }


		 $sql102 = "
				 SELECT * FROM `collections` as CC1
				 WHERE EXISTS
				 (
						 SELECT * FROM (SELECT MAX(id) AS DD,  invoice_num FROM `collections`
						 WHERE (invoice_num BETWEEN ? AND ?)
								  AND collector_id=?
						 GROUP BY invoice_num) AS TAB1
						 WHERE TAB1.DD = CC1.id
				 )
				 AND CC1.payment_date like ?
				 ORDER BY invoice_num ASC
		 ";
		 $sql102_data = [$S1, $S2, $user->id, $date1.'%'];

		 //ALL
		 if($cmd == 1)
		 {
			 $sql102 = "
					 SELECT * FROM `collections` as CC1
					 WHERE EXISTS
					 (
							 SELECT * FROM (SELECT MAX(id) AS DD,  invoice_num FROM `collections`
							 WHERE (invoice_num BETWEEN ? AND ?)
							 GROUP BY invoice_num) AS TAB1
							 WHERE TAB1.DD = CC1.id
					 )
					 AND CC1.payment_date like ?
					 ORDER BY invoice_num ASC
			 ";
			 $sql102_data = [$S1, $S2, $date1.'%'];
		 }

		 $rs1 = DB::select($sql102, $sql102_data);




		foreach ($rs1 as $kk=>$vv)
		{
			$my_coll = (object) ((array) $vv);
			$acc1 = Accounts::find($vv->cust_id);
			if($acc1){
				$vv->accounts = (object) $acc1->toArray();
			}else{
					$vv->accounts = null;
			}

			$vv->last_col = $my_coll;

			$vv->total_payment = 0;


			$vv->curr_bill  = [];
			$vv->adjust101  = [];
			$vv->penalty101  = 0;

			$vv->xxx_bill  =  0;
			$vv->xxx_arre  =  0;

			if(
				$vv->status == 'or_nw' ||
				$vv->status == 'cr_nw' ||
				$vv->status == 'cancel_cr_nw' ||
				$vv->status == 'nw_cancel'
			){
				continue;
			}


			$dd1 = date('Y-m-d',strtotime($date1.'+ 1 day'));
			
			
			
			$led_last_per = LedgerData::where('led_type', 'billing')
								->where('acct_id', $vv->cust_id)
								 ->where('status', 'active')
								 ->orderBy('date01', 'desc')
								->orderBy('zort1', 'desc')
									 ->orderBy('id','DESC')
										->first();
			
			
			if($led_last_per){
				$period11 = $led_last_per->period;
			}
			
			//~ echo '<pre>';
			//~ print_r($led_last_per->toArray());
			//~ die();



			$current_bill = LedgerData::where('acct_id', $vv->cust_id)
								 ->where('period', 'like', $period11.'%')
								 ->where('led_type', 'billing')
								 ->where('status', 'active')
								 ->with(['arrear2' => function($q1)use($period11){
										$q1->where('period', 'like', $period11.'%');
									 }])
								->orderBy('date01', 'desc')
								->orderBy('zort1', 'desc')
								 ->orderBy('id','DESC')
								 ->get()
								 ->toArray();
			
			
			//~ echo '<pre>';
			//~ print_r($current_bill);
			//~ die();
			

			if(!empty(@$current_bill))
			{

				$vv->curr_bill = @$current_bill;

				$last_bill_id = @$current_bill[0]['id'];

				$dd1 = date('Y-m-d',strtotime($date1.'+ 1 day'));


				$coll_created = date('Y-m-d H:i:s', strtotime($vv->created_at.' + 10 second'));


				$total_payment = LedgerData::where('id', '>=', $last_bill_id)
									 ->where('created_at', '<=', $coll_created)
									 ->where('date01', '<', $dd1)
									 ->where(function($q){
											$q->where('led_type', 'payment_cr');
											$q->orWhere('led_type', 'payment');
											$q->orWhere('led_type', 'cancel_cr');
											$q->orWhere('led_type', 'payment_cancel');
										})
									->where('acct_id', $vv->cust_id)
									->sum('payment');

				$total_adjustment = LedgerData::where('id', '>=', $last_bill_id)
									 ->where('created_at', '<=', $coll_created)
									 ->where('date01', '<', $dd1)
									 ->where(function($q){
											$q->where('led_type', 'adjustment');
											$q->orWhere('led_type', 'wtax');
											$q->orWhere('led_type', 'witholding');
										})
									->where('acct_id', $vv->cust_id)
									->selectRaw('SUM(bill_adj) as A, SUM(discount) as D, SUM(payment) as P')
									->get()
									->toArray();

				$total_penalty = LedgerData::where('id', '>=', $last_bill_id)
									 ->where('created_at', '<=', $coll_created)
									 ->where('date01', '<', $dd1)
									 ->where(function($q){
											$q->orWhere('led_type', 'penalty');
										})
									 ->where('acct_id', $vv->cust_id)
									 ->sum('penalty');



				$vv->total_payment = $total_payment;
				$vv->adjust101 = $total_adjustment;
				$vv->penalty101 = $total_penalty;

				$amm1 = (float) @$vv->curr_bill[0]['arrear2']['amount'];


				//~ $vv->xxx_bill = $vv->curr_bill[0]['billing'];
				//~ $vv->xxx_arre = $vv->curr_bill[0]['arrear2']['amount'] - $total_payment;
				
				//~ print_r();
				//~ die();
				
				$bill_1 = $vv->curr_bill[0]['billing'];
				$arre_1 = $vv->curr_bill[0]['arrear2']['amount'];
				$ttlp_1 = $total_payment;

				$prev_pay = $ttlp_1 - ($vv->payment);
				

				//292.83
				$arre_1 -=  $prev_pay;
				
				//PENALTY FIX to Be added to  Arrear
				//ADDED March 30,2020
				//~ $arre_1 +=  $vv->penalty101;
				
				if($vv->penalty101 > 0){
					//~ $arre_1 += $vv->penalty101;
					$arre_1 += $vv->payment;
					//~ $arre_1 += $bill_1;
				}
				
				//ADDED March 30,2020 END

				if($arre_1 >= 0){

					if($vv->payment <= $arre_1)
					{
						$vv->xxx_arre = $vv->payment;
					}
					else{
						$vv->xxx_arre = $arre_1;
						$vv->xxx_bill = ($vv->payment - $arre_1);
						//~ $vv->xxx_arre = $arre_1 - $vv->penalty101;
						//~ $vv->xxx_bill = ($vv->payment - $arre_1) - $vv->penalty101;
					}

				}else{
						$vv->xxx_bill = $vv->payment;
				}
				
				



			}else{
				
				//~ echo '<pre>';
				//~ print_r($vv);
				//~ die();
				
				if($vv->arrear1){
					$vv->xxx_arre = $vv->arrear1;
				}
				
			}



			//~ $vv->curr_bill[0]['billing'];




		}

		$coll = $rs1;

		// echo '<pre>';
		// print_r($rs1);
		// die();


		 if($cmd == 2)
		 {
			 return compact(
						'date1_x',
						'title1_x',
						'coll',
						'col2',
						'coll_name',
						'user'
				);
		 }


		$pdf = PDF::loadView('collections.pdf.daily_col_rep', @compact(
				'date1_x',
				'title1_x',
				'coll',
				'col2',
				'coll_name',
				'user'
		));

		return $pdf->stream('daily_collection_report_'.date('Ymd').'.pdf');


	}//



	function AllReportDaily()
	{
		$dd = @$_GET['dd'];
		$iid = @$_GET['iid'];


		if(empty($iid))
		{
			return $this->ReportDaily_v2('daily');
		}

		if($iid != 'all')
		{
			return $this->ReportDaily_v2('daily');
		}

		$roles = Role::find(4);
		$usr = $roles->users;


		$ll_res = array();
		foreach($usr as $u)
		{
			$ll_res[] = $this->ReportDaily_v3('daily', 2, $u->id);
		}

		//~ echo '<pre>';
		//~ echo count($ll_res);
		//~ print_r($ll_res);
		//~ die();

		$pdf = PDF::loadView('collections.pdf.daily_col_rep_all', compact('ll_res'));

		return $pdf->stream('daily_collection_report_'.date('Ymd').'.pdf');


	}//



	function ReportDaily_v5($list_type='daily', $cmd=0, $uid=0)
	{
		$date1 = @$_GET['dd'];
		$zz = @$_GET['zz'];
		
		$ret1 = $this->ReportDaily_v4('daily', 3);
		extract($ret1);
		
		
		$new_coll = array();

		foreach($coll1 as $c1)
		{
			$c1 = (object) $c1;
			
			if($c1->accounts['acct_discount'] == 45){
				$new_coll[$c1->accounts['zone_id']]['sen'][] = $c1;
				continue;
			}
			
			$new_coll[$c1->accounts['zone_id']][$c1->accounts['acct_type_key']][] = $c1;
		}
		
		$zone_keys = array_keys($new_coll);
		$my_zones_raw = Zones::whereIn('id', $zone_keys)
								->orderBy('id','asc')
										->get()
											->toArray();
		
		$my_zones = array();
		foreach($my_zones_raw as $z1){
			$z1 = (object) $z1;
			$my_zones[$z1->id] = $z1;
		} 
		
		
		$meta_ids = array();
		
		foreach($zone_keys as $z1)	
		{
			$meta_ids_raw = array_keys($new_coll[$z1]);
			foreach($meta_ids_raw as $m1)
			{
				$meta_ids[$m1] = $m1;
			}
		}

		$meta_ids1 = array_diff($meta_ids, array('sen'));
		$acct_meta1_raw = AccountMetas::whereIn('id', $meta_ids1)->get()->toArray();

		$acct_meta1 = array();
		foreach($acct_meta1_raw as $z1){
			$z1 = (object) $z1;
			$acct_meta1[$z1->id] = $z1;
		}
		
		
		//~ sort($zone_keys);
		$ret1 = compact('new_coll', 'date1', 'acct_meta1', 'meta_ids1', 'my_zones');
		return view('collections.pdf.daily_col_rep3', $ret1);
	}//
	
	
	static function get_pre_coll_ledger($coll1, $per1)
	{
		
		$due_dates  = json_decode(@$per1->due_dates2, true);
		$bill_dates = json_decode(@$per1->read_dates, true);
		
		if(in_array($coll1->status, [
			'cancel_cr',
			'cancel_receipt',
			'nw_cancel'])){
				return false;
			}
		
		$my_coll_led = LedgerData::where('acct_id', $coll1->cust_id)
						->where('status','active')
							->where('reff_no', $coll1->invoice_num)
								->where('coll_id', @$coll1->new_coll_id)
								->orderBy('date01', 'desc')
									->orderBy('zort1', 'desc')
										->orderBy('id', 'desc')
											->first();
		
		
		//Assume The xisting of leaders
		
		$debit1 = LedgerData::where('acct_id', $coll1->cust_id)
					->whereIn('led_type', [
							'beginning',
							'billing',
							'adjustment',
							'penalty'					
					])
					->where('id','<', $my_coll_led->id)
						->where('status','active')
							->orderBy('date01', 'asc')
							->orderBy('zort1', 'asc')
								->orderBy('id', 'asc')
									->get();
								
		
		$payment1 = LedgerData::where('acct_id', $coll1->cust_id)
					->whereIn('led_type', [
						'cr_nw',
						'payment_cr',
						'wtax',
						'payment',
						'or_nw_debit',
						'or_nw',
						'cancel_cr',
						'cr_nw_debit',
						'payment_cancel',
						'nw_cancel'				
					])
					->where('id','<', $my_coll_led->id)
						->where('status','active')
						->orderBy('date01', 'desc')
							->orderBy('zort1', 'desc')
								->orderBy('id', 'desc')
									->get();
		
		
		$total_payment = 0;
		
		foreach($payment1 as $p1)
		{
			switch($p1->led_type)
			{
				case 'payment':
				case 'payment_cancel':
				case 'payment_cr':
				case 'cancel_cr':
				case 'or_nw':
				case 'nw_cancel':
				case 'cr_nw':
					$total_payment+= $p1->payment;
				break;
				case 'or_nw_debit':				
				case 'cr_nw_debit':				
				case 'wtax':				
					$total_payment+= $p1->bill_adj;
				break;
			}
		}//
								
		
		$ttl_debit = 0;
		$new_arr   = array();
		foreach($debit1 as $db1)
		{
			$cr1 = 0;
			
			switch($db1->led_type)
			{
				case 'penalty':
					$cr1 = $db1->penalty;
					$ttl_debit+= $db1->penalty;
				break;
				
				case 'beginning':
					$cr1 = $db1->arrear;
					$ttl_debit+= $db1->arrear;
				break;
				
				case 'billing':
					$cr1 = ($db1->billing - $db1->discount);
					$ttl_debit+= ($db1->billing - $db1->discount);
				break;
				
				case 'adjustment':
					$cr1 = $db1->bill_adj;
					$ttl_debit-= $db1->bill_adj;
				break;				
			};
			
			$new_arr[] = ['cr1' => $cr1, 'typ' => $db1->led_type, 'lid' => $db1->id, 'date1' => $db1->date01];
		}//
		
		//Old Payment
		$lastlp1 = 0;
		foreach($new_arr as $kk => $n1)
		{
			if($total_payment <= 0){break;}
			
			$pre_pay  =  $total_payment;
			
			$total_payment -=  round($n1['cr1'],2);
			
			if($total_payment < 0){
				$lastlp1 = $n1['cr1'] - $pre_pay;
				
				$n1['cr1'] = round($lastlp1,2);
				$new_arr[$kk] = $n1;
				break;
			}
			
			unset($new_arr[$kk]);
		}//
		
		
		$My_payment = $coll1->payment;
		$led_payed  = array(); 
		foreach($new_arr as $kk => $n1)
		{
			if($My_payment <= 0){break;}
			
			$pre_pay  =  $My_payment;
			$My_payment -=  round($n1['cr1'],2);
			
			if($My_payment < 0){
				$lastlp1 = $n1['cr1'] - $pre_pay;
				$n1['cr1'] = round($lastlp1,2);
				$led_payed[] = $n1;
				break;
			}
			$led_payed[] = $new_arr[$kk];
		}
		
		
		//~ return $led_payed;
		
		$pay_date =  strtotime($py_d = date('Y-m-d', strtotime($coll1->payment_date)));
		$due_date =  strtotime(@$due_dates[$coll1->zone_id]);
		
		$is_due = false;
		if($pay_date AND $due_date){
			if($pay_date > $due_date){
				$is_due = true;
			}
		}
		
		if($is_due){
			
		}
		
		$DUE 	 = 0;
		$PENALTY = 0;
		$BILL 	 = 0;
		
		foreach($led_payed as $lp1)
		{
			$lp1 = (object) $lp1;
			$lp1->date1;
		}
		
		
		//ar / pe / bi
		//~ var_dump($is_due);
		echo '<pre>';
		print_r($bill_dates);
		print_r($bill_dates);
		print_r($py_d);
		echo '<br />';
		//~ print_r($due_dates);
		print_r($led_payed);
		//~ print_r($coll1->toArray());
		die();
		
	}//

	function ReportDaily_v4($list_type='daily', $cmd=0, $uid=0)
	{
		
		$user = Auth::user();
		
		if($cmd == 2){$user = User::find($uid);}
		
		$collector_name = $user->name;
		
		$date1 = @$_GET['dd'];
		$zz = @$_GET['zz'];
		
		$my_zone = Zones::find($zz);
		
		$coll1_pre_raw = Collection::where('payment_date','like', $date1.'%')
								->where('collector_id', $user->id)
									 ->where('status', '!=', 'deleted');
		
		if($my_zone)
		{
			$coll1_pre_raw->whereHas('accounts', function($q1)use($zz){
							$q1->where('zone_id',$zz);
					});
		}
		
						 
		$coll1_pre  =  $coll1_pre_raw->groupBy('invoice_num')
								 ->selectRaw("*, MAX(id) as new_coll_id")
								  ->with('accounts')
								   ->orderBy('invoice_num', 'asc')
									->get();
		
		$r_period = date('Y-m-01',strtotime($date1));
		$per1 = ReadingPeriod::where('period', $r_period)->first();
		//~ $due_dates = json_decode($per1->due_dates2,true);

		
		$coll1 = array();
		$x = 0;
		
		/**/
		foreach($coll1_pre as $c1)
		{
			
			if($c1->new_coll_id != $c1->id){
				$c1 = Collection::find($c1->new_coll_id);
				$c1->accounts;
			}

			$collected =  $c1->payment;
			
			$r1 = get_coll_info1($c1);
			
			//~ if($c1->invoice_num == 3982){
				//~ echo '<pre>';
				//~ print_r($r1);
				//~ die();
			//~ }
			
			
			if(!empty($r1)){
				$arr_ret = get_coll_info1_step2($collected, $r1);
			}

			//~ $coll_led1 = CashierCtrl::get_pre_coll_ledger($c1, $per1);
			
			$c1->info77   = @$arr_ret;
			$c1->coll_led = @$coll_led1;
			
			$coll1[] = (object) $c1->toArray();
			$x++;
		}
		/**/ 
		
	
						
		$ret1 = compact('coll1', 'date1', 'collector_name');
		
		if($cmd == 3){
			return $ret1;
		}
		
		// echo '<pre>';
		// print_r($coll1);
		//~ print_r($ret1);
		//~ die();
		
		return view('collections.pdf.daily_col_rep4', $ret1);
		
	}//




	function Dec142020_DailyCollectionReport($list_type='daily', $cmd=0, $uid=0)
	{
		
		 $user = Auth::user();
		
		 if($cmd == 2){
			 $user = User::find($uid);
		 }
		 
		 if($cmd == 1){
			 $iid = @$_GET['iid'];			 
			 $user = User::find($iid);
		 }
		
		
		$dd             = date('Y-m-d',strtotime(@$_GET['dd']));
		$current_time   = strtotime(date('Y-m-01',strtotime(@$_GET['dd'])));
		$arrear_cy_time = strtotime(date('Y-01-01',strtotime(@$_GET['dd'])));
		$arrear_py_time = strtotime(date('Y-01-01',strtotime(@$_GET['dd'])));
		
		$my_stat = array('active', );
		
		$sql1 = "
			SELECT * FROM (
				SELECT *, CAST(invoice_num as UNSIGNED) MMM FROM collections
				WHERE id IN (
					SELECT MAX(id) FROM collections 
					WHERE payment_date like '$dd%'
					GROUP BY invoice_num
				)
			) TAB1
			ORDER BY MMM ASC
		";
		
		$current_period01 = date("Y-m-01", strtotime($dd));
		$read_period      = ReadingPeriod::where('period','like',$current_period01)->first();
		$curr_duedate     = json_decode($read_period->due_dates2, true);

		// echo '<pre>';
		// print_r($curr_duedate);
		// die();

		//~ echo $sql1;
		//~ die();
		
		$my_collection = DB::select($sql1);
		
		foreach($my_collection as $m1)
		{
			$my_accounts  = Accounts::find($m1->cust_id);
			$my_coll_led  = PayPenLed::where('cid', $m1->id)
								->selectRaw("
*, 
(SELECT due_date FROM billing_dues WHERE billing_dues.id=pay_pen_leds.pen_id AND pay_pen_leds.typ='penalty'  LIMIT 1) dd1,
(SELECT bill_date FROM billing_mdls WHERE billing_mdls.id=pay_pen_leds.pen_id AND pay_pen_leds.typ='billing' LIMIT 1) dd2,
(SELECT date01 FROM ledger_datas WHERE ledger_datas.id=pay_pen_leds.pen_id AND pay_pen_leds.typ='beginning'  LIMIT 1) dd3
								")->get();							
			
			$col_break = array(
							'penalty' => 0,
							'billing' => 0,
							'cy'	  => 0,
							'py'      => 0,
							'op'      => 0
						);
						
			foreach($my_coll_led as $mcl1)
			{
				if($mcl1['typ'] == 'penalty'){
					$col_break['penalty'] += $mcl1['amt'];
				}

				if($mcl1['typ'] == 'billing'){
					$bill_datetime = strtotime($mcl1['dd2']);
					if($bill_datetime >= $current_time){
						$col_break['billing'] += $mcl1['amt'];
					}elseif($bill_datetime < $current_time && $bill_datetime >= $arrear_cy_time ){
						$col_break['cy'] += $mcl1['amt'];
					}elseif($bill_datetime < $arrear_cy_time){
						$col_break['py'] += $mcl1['amt'];
					}
				}

				if($mcl1['typ'] == 'beginning'){
					$bill_datetime = strtotime($mcl1['dd3']);
					if($bill_datetime < $current_time && $bill_datetime >= $arrear_cy_time ){
						$col_break['cy'] += $mcl1['amt'];
					}elseif($bill_datetime < $arrear_cy_time){
						$col_break['py'] += $mcl1['amt'];
					}					
				}
				
			}//

			$m1->accounts   = $my_accounts->toArray();
			$m1->col_led    = $my_coll_led->toArray();
			$m1->col_break  = $col_break;

			// DUE DATE PAYMENT GOES TO CY Arrear
			$payment_date01 = date("Y-m-d", strtotime($m1->payment_date));
			if(!empty($m1->col_led)){
				$zone_due_date = strtotime($curr_duedate[$m1->zone_id]);
				$time_pay      = strtotime($payment_date01);
				if($time_pay > $zone_due_date){
					$m1->col_break['cy'] += $m1->col_break['billing']; 
					$m1->col_break['billing'] = 0;
				}
			}


		}// endforeach
		
		$var01 = Dec142020___get_collection_total_information($dd);

		$full_date = $dd;

		$all_params = compact('my_collection', 'var01', 'user', 'full_date');

		$nwb_payment = array(
			'or_nw',
			'cr_nw',
		);

		foreach($my_collection as $kk => $vv)
		{

			if(in_array($vv->status, $nwb_payment)){
				continue;
			}

			$brk_ttl = 0;
			foreach($vv->col_break as $k2 => $v2){
				$brk_ttl += $v2;
			}

			$vv->col_break['op'] = 0; 

			$dif_1 =  $brk_ttl - $vv->payment;
			if($dif_1 < 0){
				$vv->col_break['op'] = $vv->payment - $brk_ttl; 
			}
		}


		// echo '<pre>';
		// print_r($my_collection);
		// die();
		
		// $pdf = PDF::loadView('collections.pdf.daily_col_rep_new', compact('my_collection', 'var01', 'user'));
		// return $pdf->stream('daily_collection_report_'.date('Ymd').'.pdf');	
		// return view('collections.pdf.daily_col_rep_new_html', compact('my_collection', 'var01', 'user'));
		return view('collections.pdf.daily_col_rep_new_html', compact('my_collection', 'var01', 'user'));
		

		Excel::load(public_path('excel02/daily_coll_report001.xls'), 
			function($excel)use($all_params){

				$excel->sheet('Sheet1', function($sheet)use($all_params) {
					extract($all_params);
					
					$sheet->setCellValue('A4', 'Daily Collection Report As of '.date('F d, Y', strtotime(@$full_date)));//RES
					$row = 5;
					$col = 'A';
					$row++;

					$wb_payment = array(
								'active',
								'collector_receipt',
							);
			
					$nwb_payment = array(
								'or_nw',
								'cr_nw',
							);
			
					$all_cancel = array(
								'cancel_cr',
								'cancel_cr_nw',
								'cancel_receipt',
								'nw_cancel'
							);	


					$grand_total = array('A'=>0, 'B'=>0, 'C'=>0, 'D'=>0, 'E'=>0, 'bal'=>0);

					foreach($my_collection as $mm)
					{
						if(in_array($mm->status, $all_cancel)){
							$sheet->setCellValue('A'.$row, 'OR-'.@$mm->invoice_num);
							$sheet->setCellValue('B'.$row, 'Canceled');
							$row++;
							continue;
						}

						
						$full_name = '';
						$full_name .= $mm->accounts['acct_no'];
						$full_name .= ' - '.$mm->accounts['fname'];
						$full_name .= ' '.$mm->accounts['lname'];
						$full_name .= ' '.$mm->accounts['mi'];
						$full_name = strtoupper($full_name);

						$col_break1 = $mm->col_break;

						$sheet->setCellValue('A'.$row, 'OR-'.@$mm->invoice_num);
						$sheet->setCellValue('B'.$row, @$full_name);
						$sheet->setCellValue('C'.$row, @$mm->payment);
						$sheet->setCellValue('D'.$row, @$col_break1['billing']<=0?'':@$col_break1['billing']);
						$sheet->setCellValue('E'.$row, @$col_break1['cy']<=0?'':@$col_break1['cy']);
						$sheet->setCellValue('F'.$row, @$col_break1['py']<=0?'':@$col_break1['py']);
						$sheet->setCellValue('G'.$row, @$col_break1['penalty']<=0?'':@$col_break1['penalty']);
						$sheet->setCellValue('I'.$row, @$col_break1['op']<=0?'':round(@$col_break1['op'], 2));

						if($mm->tax_val > 0){
							$sheet->setCellValue('J'.$row, @$mm->tax_val <=0 ?'':round(@$mm->tax_val, 2));
						}						

						if(in_array($mm->status, $nwb_payment)){
							$sheet->setCellValue('A'.$row, 'OR-'.@$mm->invoice_num.'(NWB)');
							$sheet->setCellValue('H'.$row, @$mm->payment);
							$row++;
							continue;
						}

						$row++;

					}
						
					$sheet->setCellValue('A'.$row, 'TOTAL');
					$sheet->setCellValue('C'.$row, '=SUM(C6:C'.($row-1).')');
					$sheet->setCellValue('D'.$row, '=SUM(D6:D'.($row-1).')');
					$sheet->setCellValue('E'.$row, '=SUM(E6:E'.($row-1).')');
					$sheet->setCellValue('F'.$row, '=SUM(F6:F'.($row-1).')');
					$sheet->setCellValue('G'.$row, '=SUM(G6:G'.($row-1).')');
					$sheet->setCellValue('H'.$row, '=SUM(H6:H'.($row-1).')');
					$sheet->setCellValue('I'.$row, '=SUM(I6:I'.($row-1).')');
					$sheet->setCellValue('J'.$row, '=SUM(J6:J'.($row-1).')');
					
					// $sheet->setCellValue('C'.$row, @$vv2['A']<=0?'':@$vv2['A']);
					// $sheet->setCellValue('D'.$row, @$vv2['B']<=0?'':@$vv2['B']);
					// $sheet->setCellValue('E'.$row, @$vv2['C']<=0?'':@$vv2['C']);
					// $sheet->setCellValue('F'.$row, @$vv2['D']<=0?'':@$vv2['D']);

					$row++;
					$row++;
					$row++;
					$row++;
					
					$sheet->setCellValue('B'.$row, 'Prepared by:');
					$sheet->mergeCells('C'.$row.':D'.$row);
					$sheet->setCellValue('C'.$row, 'Checked by:');
					$sheet->mergeCells('E'.$row.':G'.$row);
					$sheet->setCellValue('E'.$row, 'Noted by:');

					$row++;
					$sheet->setCellValue('B'.$row, REP_SIGN4);
					$sheet->mergeCells('C'.$row.':D'.$row);
					$sheet->setCellValue('C'.$row, REP_SIGN1);
					$sheet->mergeCells('E'.$row.':G'.$row);
					$sheet->setCellValue('E'.$row, WD_MANAGER);

					$row++;
					$sheet->setCellValue('B'.$row, REP_SIGN4_TITLE);
					$sheet->mergeCells('C'.$row.':D'.$row);
					$sheet->setCellValue('C'.$row, REP_SIGN1_TITLE);
					$sheet->mergeCells('E'.$row.':G'.$row);
					$sheet->setCellValue('E'.$row, WD_MANAGER_RA);

					$row++;
					$row++;



				});

		})->download('xls');
		
		
	}//	


	// Feb_05_2021_DailyCollectionReport
	// static
	function daily_collection_report_static($uid)
	{
		return $this->Feb_05_2021_DailyCollectionReport('daily', 2, $uid);

	}//


	function Feb_05_2021_DailyCollectionReportSummary($list_type='daily', $cmd=0, $uid=0)
	{
		$user = Auth::user();
		if($cmd == 2){$user = User::find($uid);}
		$collector_name = $user->name;
		$dd = $date1 = date('Y-m-d',strtotime(@$_GET['dd']));

		$sql1 = "
			SELECT * FROM (
				SELECT *, CAST(invoice_num as UNSIGNED) MMM FROM collections
				WHERE id IN (
					SELECT MAX(id) FROM collections 
					WHERE payment_date like '$dd%'
					AND collector_id= ?
					AND zone_id=?
					GROUP BY invoice_num
				)
			) TAB1
			ORDER BY MMM ASC
		";

		$nw_or = ['cancel_cr_nw', 'cr_nw','nw_cancel','or_nw','cancel_cr', 'cancel_receipt'];		

		$all_zones = Zones::where('status', '!=', 'deleted')->get();



		foreach($all_zones as $zon_dat)
		{
			$my_collection = DB::select($sql1, [$user->id, $zon_dat->id]);
		
			$ttl_001 = ['pen'=>0, 'bil'=>0,'py'=>0, 'cy'=>0, 'nwb'=> 0, 'tax'=>0, 'pay'=> 0];

			foreach($my_collection as $m1)
			{
				if(in_array($m1->status, $nw_or)){
					$m1->break_dd = []; 
					$ttl_001['nwb'] += $m1->payment;
					$ttl_001['pay'] += $m1->payment;
					continue;
				}

				$break_dd = feb_05_2021_daily_col_break($m1->cust_id, $m1->invoice_num, $dd);
				$ttl_001['pen'] += $break_dd['pen'];
				$ttl_001['py'] += $break_dd['py'];
				$ttl_001['cy'] += $break_dd['cy'];
				$ttl_001['bil'] += $break_dd['bil'];
				$ttl_001['nwb'] += $break_dd['nwb'];				
				$ttl_001['tax'] += $m1->tax_val;
				$ttl_001['pay'] += $m1->payment;

				// print_r( $break_dd);
			}

			$zon_dat->ttl_brk = $ttl_001;
	
		}

		return view('collections.pdf.daily_col_rep_new_html3', compact('user', 'date1','all_zones'));


		print_r($my_collection);
		print_r($all_zones->toArray());

	}


	function daily_collect_service()
	{
		if( @$_GET['store_to_collection_ledger']  == 1 )
		{
			CollectionReportService::report_start_service();
		}		

	}

	function Feb_05_2021_DailyCollectionReport($list_type='daily', $cmd=0, $uid=0)
	{



		/***/
		/***/
		/***/
		/***/
		/***/
		/***/
		
		$user = Auth::user();
		
		if($cmd == 2){$user = User::find($uid);}
		
		$collector_name = $user->name;
		
		$date1 = @$_GET['dd'];
		$zz = @$_GET['zz'];

		$dd = date('Y-m-d',strtotime(@$_GET['dd']));

		#########
		#########
		$user->id = 57;
		#########
		#########


		$sql1 = "
			SELECT 
				TAB1.*, accounts.acct_no, accounts.lname, accounts.fname
			FROM (
				SELECT *, CAST(invoice_num as UNSIGNED) MMM FROM collections
				WHERE id IN (
					SELECT MAX(id) FROM collections 
					WHERE payment_date like '$dd%'
					AND collector_id= ?
					GROUP BY invoice_num
				)
			) TAB1
			LEFT JOIN accounts ON accounts.id=TAB1.cust_id

			ORDER BY MMM ASC
		";
		$my_collection = DB::select($sql1, [$user->id]);
		// ee($my_collection, __FILE__, __LINE__);


		return view('collections.pdf.daily_col_rep_new_html4', compact('my_collection'));

		return;
		return;
		return;
		return;

		echo $dd;
		ee($my_collection, __FILE__, __LINE__);

		$nw_or = ['cancel_cr_nw', 'cr_nw','nw_cancel','or_nw','cancel_cr', 'cancel_receipt'];		
		
		foreach($my_collection as $m1)
		{
			$my_accounts    = Accounts::selectRaw('acct_no, lname,fname')->where('id', $m1->cust_id)->first();
			if( $my_accounts ) {
				$m1->accounts   = $my_accounts->toArray();
			}else{
				$m1->accounts   = [];
			}

			$m1->particular = [];
		
			if(in_array($m1->status, $nw_or)){$m1->break_dd = []; continue;}

			// $break_dd = feb_05_2021_daily_col_break($m1->cust_id, $m1->invoice_num, $dd);
			// $m1->break_dd   = $break_dd;
			
			$break_dd = CollectionService::coll_report_breakdown($m1);
			// ee($break_dd, __FILE__, __LINE__);

			$m1->break_dd = $break_dd;

			if( $break_dd['py'] > 0 || $break_dd['cy']  > 0 || $break_dd['cur']  > 0 ) {
				$m1->particular[] = 'Water Bill';
			}
			if($break_dd['pen'] > 0 ) {
				$m1->particular[] = 'Surcharge';
			}
			if($break_dd['nwb'] > 0 ) {
				$m1->particular[] = 'Others';
			}

		}//

		// ee($my_collection,__FILE__, __LINE__);

		return view('collections.pdf.daily_col_rep_new_html4', compact('my_collection', 'user', 'date1'));


		
		echo '<pre>';
		print_r($my_collection);
		die();


	}//

	//collections/reports/daily
	//collections/reports/daily
	function ReportDaily($list_type='daily')
	{
		if(@$_GET['monthly'] == 1){
			return CollectionService::daily_collection_excel();
		}

		if(@$_GET['sum'] == 1){
			return $this->Feb_05_2021_DailyCollectionReport($list_type);
		}
		else{
			return $this->Feb_05_2021_DailyCollectionReportSummary($list_type='daily');
		}

		return;
		return;
		return;
		

		return $this->Dec142020_DailyCollectionReport($list_type);

		if(@$_GET['sum'] == 1)
		{
			return $this->ReportDaily_v4($list_type);
		}
		
		return $this->ReportDaily_v5($list_type);
		
		
		return $this->ReportDaily_v2($list_type);
		//~ return $this->ReportDaily_v2($list_type);
		//~ return $this->ReportDaily_v2($list_type);

		//~ $bill = BillingMdl::find(19);
		//~ $bb = bill_payment_breakdown1($bill, 700);
		//~ echo '<pre>';
		//~ print_r($bb);
		//~ die();

		$user = Auth::user();

		//~ echo '<pre>';
		//~ print_r($user->id);
		//~ die();

		$CY = date('Y');
		$PY = date('Y');


		$date1 = date('Y-m-d');
		$title1_x = 'Daily Collection Report';

		if(!empty(@$_GET['dd'])){
			$date1 = @$_GET['dd'];
		}

		$zonex = 0;
		if(!empty(@$_GET['zz'])){
			$zonex = @$_GET['zz'];
		}

		$zonex = (int) $zonex;


		$is_zon = Zones::find($zonex);



		$date1_x = date('l, F d, Y', strtotime($date1));

		if($list_type == 'monthly')
		{
			$date1 = date('Y-m', strtotime($date1));
			$title1_x = 'Monthly Collection Report';
			$date1_x = date('F Y', strtotime($date1));
		}

		//~ echo $date1;
		//~ die();

		$coll_raw = Collection::where('payment_date', 'like', $date1.'%')
					->with('accounts')
					//~ ->with('billing')
					//~ ->with('ledger')
					//~ ->with(['arrear' => function($query){
						//~ $query->orderBy('id', 'desc');
					//~ }])
					//~ ->where('status', 'active')
					//~ ->where(function($q1){
						//~ $q1->where('status', 'active');
						//~ $q1->orWhere('status', 'cr_nw');
						//~ $q1->orWhere('status', 'or_nw');
					//~ })
					->where('collector_id', $user->id);



		$by_zone = '';
		if($is_zon){
			$coll_raw->whereHas('accounts', function($query)use($zonex){
				$query->where('zone_id', $zonex);
			});
			$by_zone = '  '.$is_zon->zone_name;
		}

		$coll  = $coll_raw
					->orderBy('invoice_num', 'asc')
					->groupBy('invoice_num')
					->get();


		$rs1 = DB::select("
						SELECT  * FROM collections as CC

						WHERE EXISTS(
						    SELECT * FROM (
						        SELECT MAX(id) iid, invoice_num, cust_id FROM `collections`
						    		WHERE payment_date like '2019-09-16%' AND collector_id=10
						    		GROUP BY invoice_num
						    		ORDER BY invoice_num asc
						       ) AS TAB1
						    WHERE TAB1.iid=CC.id
						)


						ORDER BY CC.invoice_num ASC
				");


		foreach($rs1 as $r1)
		{
			// $acct1 = DB::select("SELECT * FROM  accounts WHERE id=?", [$r1->cust_id]);
			$acc1 = Accounts::find($r1->cust_id);
			$r1->accounts = ((object) $acc1->toArray());

			$r1->last_col =
					Collection::where('invoice_num', $r1->invoice_num)
							->orderBy('id','desc')
								->first();
		}
		// $coll = $rs1;

		// echo '<pre>';
		// print_r($rs1);//accounts
		// print_r($coll->toArray());
		// die();


		// echo '<pre>';
		// foreach ($coll as $kk => $vv) {
		// 	if($vv->status == 'active'){
		// 		// continue;
		// 	}
		// 	print_r($vv->toArray());
		// }
		// die();




		pdf_heading1($title1_x, $date1_x);


		help_collector_daily_report_heading1("Offical Receipt ".$by_zone);

		$total_collected = 0;
		$total_bill = 0;
		$total_arrear = 0;
		$ttl_check = 0;
		$ttl_cash = 0;
		$ttl_ada = 0;
		$ttl_discount = 0;
		$total_arrear22 = 0;

		$ttl_water = 0;
		$ttl_non_water = 0;

		$grand_ttl_check = 0;
		$grand_ttl_cash = 0;
		$grand_ttl_ada = 0;
		$grand_ttl_all = 0;

		$grand_ttl_water = 0;
		$grand_ttl_non_water = 0;

		$ttl_item = 0;

		Fpdf::SetFont('Courier',"", 8);

		$acc_total = array();

		foreach($coll as $cc)
		{
			$dd =
				help_collector_daily_report_func111($cc);
				help_collector_daily_report_func222($dd);
				help_collector_daily_report_func333($dd);

			if($cc->status == 'active')
			{
				$total_collected+=$cc->payment;

				if($cc->pay_type == 'cash'){
					$ttl_cash+=$cc->payment;
				}

				if($cc->pay_type == 'check'){
					$ttl_check+=$cc->payment;
				}

				if($cc->pay_type == 'ada'){
					$ttl_ada+=$cc->payment;
				}

				$ttl_water += $cc->payment;
			}

			if($cc->status == 'or_nw')
			{
				$total_collected+=$cc->payment;

				if($cc->pay_type == 'cash'){
					$ttl_cash+=$cc->payment;
				}

				if($cc->pay_type == 'check'){
					$ttl_check+=$cc->payment;
				}

				if($cc->pay_type == 'ada'){
					$ttl_ada+=$cc->payment;
				}

				$ttl_non_water = $cc->payment;
			}


			$ttl_item++;

		}//

		$grand_ttl_check += $ttl_check;
		$grand_ttl_cash  += $ttl_cash;
		$grand_ttl_ada   += $ttl_ada;

		$grand_ttl_water += $ttl_water;
		$grand_ttl_non_water += $ttl_non_water;

		//~ echo $ttl_non_water;
		//~ echo '<br />';
		//~ echo $ttl_water;
		//~ die();

		help_collector_daily_report_subttl111(compact(
			'total_collected',
			'total_bill',
			'total_arrear',
			'total_arrear22',
			'ttl_discount',
			'ttl_cash',
			'ttl_check',
			'ttl_ada',
			'ttl_non_water',
			'ttl_water'
		));


		Fpdf::Ln();
		Fpdf::Ln();


		help_collector_daily_report_heading1("Collector's Receipt ".$by_zone);
		Fpdf::SetFont('Courier',"", 8);

		$col2 = Collection::where('payment_date', 'like', $date1.'%')
					->where(function($qq1){
						$qq1->where('status', 'collector_receipt');
						$qq1->orWhere('status', 'cancel_cr');
						$qq1->orWhere('status', 'cancel_cr_nw');

						$qq1->orWhere('status', 'cr_nw');
					})
					->with('accounts')
					->with('last_col')
					->where('collector_id', $user->id)
					->orderBy('invoice_num', 'asc')
					->groupBy('invoice_num')
					->get();

		// echo '<pre>';
		// print_r($col2->toArray());
		// die();

		$total_collected = 0;
		$total_bill = 0;
		$total_arrear = 0;
		$ttl_check = 0;
		$ttl_cash = 0;
		$ttl_ada  = 0;
		$ttl_discount = 0;
		$total_arrear22 = 0;


		$ttl_water = 0;
		$ttl_non_water = 0;


		foreach($col2 as $cc)
		{
			$dd =
				help_collector_daily_report_func111($cc, true);
				help_collector_daily_report_func222($dd);
				help_collector_daily_report_func333($dd);

			if($cc->status == 'collector_receipt')
			{
				$total_collected+=$cc->payment;

				if($cc->pay_type == 'cash'){
					$ttl_cash+=$cc->payment;
				}

				if($cc->pay_type == 'check'){
					$ttl_check+=$cc->payment;
				}

				if($cc->pay_type == 'ada'){
					$ttl_ada+=$cc->payment;
				}

				$ttl_water += $cc->payment;
			}

			if($cc->status == 'cr_nw')
			{
				$ttl_non_water += $cc->payment;
				$total_collected+=$cc->payment;

				if($cc->pay_type == 'cash'){
					$ttl_cash+=$cc->payment;
				}

				if($cc->pay_type == 'check'){
					$ttl_check+=$cc->payment;
				}

				if($cc->pay_type == 'ada'){
					$ttl_ada+=$cc->payment;
				}

			}

		}//

		$grand_ttl_check += $ttl_check;
		$grand_ttl_cash  += $ttl_cash;
		$grand_ttl_ada  += $ttl_ada;

		$grand_ttl_all = $grand_ttl_check + $grand_ttl_cash + $grand_ttl_ada;


		$grand_ttl_water += $ttl_water;
		$grand_ttl_non_water += $ttl_non_water;

		help_collector_daily_report_subttl111(compact(
			'total_collected',
			'total_bill',
			'total_arrear',
			'total_arrear22',
			'ttl_discount',
			'ttl_cash',
			'ttl_check',
			'ttl_ada',
			'ttl_non_water',
			'ttl_water'
		));



		Fpdf::Ln();
		Fpdf::Ln();

		//~ echo '<pre>';
		//~ print_r($user->toArray());
		//~ die();

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		//$user->name
		//~ Fpdf::Cell(30,6, 'WIN','',0,'L', false);
		Fpdf::Cell(30,6, strtoupper($user->username),'',0,'L', false);
		Fpdf::Cell(30,6, 'SUB TOTALS',0,0,'R', false);
		Fpdf::Cell(20,6, '','',0,'R', false);
		Fpdf::Cell(20,6, number_format($grand_ttl_all, 2),'TBRL',0,'R', false);
		Fpdf::Cell(20,6, number_format($total_bill, 2),'',0,'R', false);
		Fpdf::Cell(20,6, number_format($total_arrear, 2),'',0,'R', false);

		//~ Fpdf::Cell(20,6, number_format($total_collected, 2),'TBRL',0,'R', false);
		//~ Fpdf::Cell(20,6, number_format($total_bill, 2),'',0,'R', false);
		//~ Fpdf::Cell(20,6, number_format($total_arrear, 2),'',0,'R', false);

		Fpdf::SetFont('Courier',"", 8);

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'CASH',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_cash, 2),0,0,'R', false);


		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'CHECK',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_check, 2),0,0,'R', false);


		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'ADA',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_ada, 2),0,0,'R', false);


		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'WATER BILL',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_water, 2),0,0,'R', false);

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'NON-WATER BILL',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_non_water, 2),0,0,'R', false);


		Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(30,6, 'Total Items:','',0,'L', false);
		Fpdf::Cell(30,6, 'GRAND TOTALS:',0,0,'R', false);
		Fpdf::Cell(20,6, '','',0,'R', false);
		Fpdf::Cell(20,6, number_format($grand_ttl_all, 2),'TBRL',0,'R', false);
		Fpdf::Cell(20,6, number_format($total_bill, 2),'',0,'R', false);
		Fpdf::Cell(20,6, number_format($total_arrear, 2),'',0,'R', false);

		Fpdf::SetFont('Courier',"", 8);

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(10,6, '',0,0,'R', false);
		//~ Fpdf::Cell(40,6, '131 / 0',0,0,'L', false);
		Fpdf::Cell(40,6, $ttl_item.' / 0',0,0,'L', false);
		Fpdf::Cell(20,6, 'CASH',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_cash, 2),0,0,'R', false);

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'CHECK',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_check, 2),0,0,'R', false);

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'ADA',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_ada, 2),0,0,'R', false);

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'WATER BILL',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_water, 2),0,0,'R', false);

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(50,6, '',0,0,'R', false);
		Fpdf::Cell(20,6, 'NON-WATER BILL',0,0,'L', false);
		Fpdf::Cell(30,6, number_format($grand_ttl_non_water, 2),0,0,'R', false);




		Fpdf::Ln();
		Fpdf::Ln();
		Fpdf::Ln();

		pdf_footer_signature();


		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;


	}


	function ReportForDisconnection($zone_id, $period)
	{

		$zone = Zones::find($zone_id);

		$date1 = date('Y-m', strtotime($period));

		$title1_x = 'List of Conncessionaires for Disconnection';
		$date1_x = 'Target Date of Disconnection:    '.date('l, F d, Y');


		$func1 = function($query)use($date1){
					$query->where('period', 'like', $date1.'%');
					$query->whereDoesntHave('collection', function($qq2){
						$qq2->where('balance_payment','<=', 0);
					});
				  };

		$func2 = function($query){
					$query->whereDoesntHave('collection');
				};


		$accnt = Accounts::whereHas('bill1', $func1)
				->where('zone_id', $zone_id)
				->with(['bill1' =>$func1, 'bill1.collection_total'])
				->get();



		//~ foreach($accnt as $a){
			//~ $a->billing_all;
		//~ }

		//~ echo '<pre>';
		//~ print_r($accnt->toArray());
		//~ die();

		pdf_heading1($title1_x, $date1_x);

		Fpdf::write(4, $zone->zone_name);
		Fpdf::Ln();
		Fpdf::Ln();

		Fpdf::SetLeftMargin(0);

		$x = Fpdf::GetX();
		$y = Fpdf::GetY();

		$x = Fpdf::GetX();
		$y = Fpdf::GetY();
		$x = 0;


		$new_x = array(
			30,//0
			33,//1
			35,//2
			20,//3
			20,//4
			20,//5
			30,//6
		);

		Fpdf::MultiCell($new_x[0],6,'Account No.','LTB' , 'L');
		$x += $new_x[0];
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell($new_x[1],6,'Name','LTB' , 'L');
		$x += $new_x[1];
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell($new_x[2],3,"Address \n ",'LTB' , 'L');
		$x += $new_x[2];
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell($new_x[3],6,'Meter No.','LTB' , 'C');
		$x += $new_x[3];

		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell($new_x[5],3,"Reading \n ",'LTB' , 'C');
		$x += $new_x[5];


		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell($new_x[4],3,'Number of Months','LTB' , 'C');
		$x += $new_x[4];


		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell($new_x[5],3,"Amount \n ",'LTB' , 'C');
		$x += $new_x[5];

		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell($new_x[6],3,"Remarks  \n ",'LTBR' , 'C');

		//~ $x += 20;
		//~ Fpdf::SetXY($x, $y);
		//~ Fpdf::MultiCell(25,6,'Amount','LTBR' , 'C');

		Fpdf::Ln();

		$total_collected = 0;
		$total_bill = 0;
		$total_arrear = 0;
		$ttl_check = 0;
		$ttl_cash = 0;
		$ttl_discount = 0;

		Fpdf::SetFont('Courier',"", 8);







		$item_height = 2;

		$acc_total = array();


		$grand_total = 0;

		foreach($accnt as $cc)
		{

			$d1 = date_create($cc->bill1->period);
			$d2 = date_create($cc->first_bill->period);

			$interval = date_diff($d1, $d2);

			$read1 = explode('||',$cc->bill1->read_PC);


			//~ echo $interval->format('%m');
			//~ die();

			//~ echo '<pre>';
			//~ print_r($cc->bill1->collection_total);
			//~ die();

			$total_ammount = $cc->bill1->curr_bill +$cc->bill1->arrears;

			if(!empty($cc->bill1->collection_total)){
				$total_ammount = $total_ammount - $cc->bill1->collection_total->total_payed;
			}


			Fpdf::SetLeftMargin(5);
			Fpdf::Cell($new_x[0]-5,$item_height, $cc->acct_no,0,0,'L', false);
			Fpdf::Cell($new_x[1],$item_height, $cc->fname.' '.$cc->lname, 0 ,0,'L', false);
			Fpdf::Cell($new_x[2],$item_height, $cc->address1,0,0,'L', false);
			Fpdf::Cell($new_x[3],$item_height, $cc->meter_number1,0,0,'L', false);
			Fpdf::Cell($new_x[4],$item_height, @$read1[1],0,0,'C', false);
			Fpdf::Cell($new_x[4],$item_height, $interval->format('%m'),0,0,'C', false);
			Fpdf::Cell($new_x[5],$item_height, number_format($total_ammount,2),0,0,'R', false);
			Fpdf::Cell($new_x[6],$item_height, '','B',0,'R', false);
			Fpdf::Ln();
			Fpdf::Ln();


			$grand_total+=$total_ammount;
		}

		Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Cell(0,$item_height, '','B',0,'R', false);
		Fpdf::Ln();
		Fpdf::Ln();
		Fpdf::Cell(50,$item_height, 'Total',0,0,'L', false);
		Fpdf::Cell(123,$item_height, number_format($grand_total,2),0,0,'R', false);





		//~ Fpdf::SetFont('Courier',"", 8);









		Fpdf::Ln();
		Fpdf::Ln();
		Fpdf::Ln();

		//~ pdf_footer_signature();


		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;


	}


	function ReportDailySummary($list_type='daily')
	{

		//~ echo tview_dis_act_history();
		//~ echo tview_ledger_billing_ttl(339);
		//~ die();

		$zones = Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')->get();

		$date1 = date('Y-m-d');

		if(!empty(@$_GET['dd'])){
			$date1 = @$_GET['dd'];
		}


		$title1_x = 'Daily Collection Report';
		$date1_x = date('l, F d, Y', strtotime($date1));

		if($list_type == 'monthly')
		{
			$date1 = date('Y-m', strtotime($date1));
			$title1_x = 'Monthly Collection Report Summary';
			$date1_x = date('F Y', strtotime($date1));
		}

		//~ echo $date1;
		//~ die();

		pdf_heading1($title1_x, $date1_x);

		$x = Fpdf::GetX();
		$y = Fpdf::GetY();

		$x = Fpdf::GetX();
		$y = Fpdf::GetY();
		$x = 0;

		Fpdf::MultiCell(20,6,'Zone','LTB' , 'L');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Total Collected','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,6,'Water Bill','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Non-Water Bill','LTB' , 'C');
		$x += 25;
		//~ Fpdf::SetXY($x, $y);
		//~ Fpdf::MultiCell(25,2,'None - Water Bill Collection','LTB' , 'C');
		//~ $x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Current Year Arrears','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Previous Year Arrears','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(30,6,'W/Tax','LTB' , 'C');
		$x += 30;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(30,6,'Penalty','LTBR' , 'C');


		$total_collected = 0;
		$total_billed = 0;
		$total_arrear = 0;
		$total_non_water = 0;
		$ttl_Tax = 0;

		foreach($zones as $zz)
		{

			$coll_all = help_collector_monthly($date1, $zz->id);

			//~ echo '<pre>';
			//~ print_r($coll_all->toArray());

			$A1 = (float)@$coll_all->ttl;

			//~ $A2 = (float)@$coll_all->bill1;
			//~ $A3 = (float)@$coll_all->ttl_nw;
			//~ $A4 = (float)@$coll_all->arrear1;
			//~ $A5 = 0;
			//~ $A6 = (float)@$coll_all->ttl_tax;
			//~ $A7 = (float)@$coll_all->penalty1;

			$A2 = 0;
			$A3 = 0;
			$A4 = 0;
			$A5 = 0;
			$A6 = 0;
			$A7 = 0;


			$total_collected+=$A1;
			$total_billed+=@$A2;
			$total_arrear+=@$A4;
			$total_non_water += $A3;
			$ttl_Tax+=$A6;

			Fpdf::SetLeftMargin(5);
			Fpdf::Cell(20,4, $zz->zone_name, '',0,'L', false);
			Fpdf::Cell(25,4, number_format($A1,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format($A2,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format($A3,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format($A4,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format($A5,2), '',0,'R', false);
			Fpdf::Cell(30,4, number_format($A6,2), '',0,'R', false);
			Fpdf::Cell(30,4, number_format($A7,2), '',0,'R', false);
			Fpdf::Ln();
		}


		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(205,2, '', 'T',0,'L', false);
		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(20,4, 'Total', '',0,'L', false);
		Fpdf::Cell(25,4, number_format($total_collected, 2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format($total_billed, 2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format($total_non_water, 2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format($total_arrear, 2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0, 2), '',0,'R', false);
		Fpdf::Cell(30,4, number_format($ttl_Tax, 2), '',0,'R', false);
		Fpdf::Cell(30,4, number_format(0, 2), '',0,'R', false);
		Fpdf::Ln();


		Fpdf::Ln();
		Fpdf::Ln();

		pdf_footer_signature();

		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;

	}//

	function ReportDailySummary_OLD($list_type='daily')
	{

		$date1 = date('Y-m-d');
		$title1_x = 'Daily Collection Report';
		$date1_x = date('l, F d, Y');

		if($list_type == 'monthly')
		{
			$date1 = date('Y-m');
			$title1_x = 'Monthly Collection Report Summary';
			$date1_x = date('F Y');
		}

		if(!empty(@$_GET['dd'])){
			$date1 = @$_GET['dd'];
		}


		$coll = Collection::where('collections.payment_date', 'like', $date1.'%')
					->select(DB::raw('
						collections.*,
						SUM(collections.payment) as zone_collected,
						COUNT(collections.invoice_num) as total_inv,
						GROUP_CONCAT(billing_mdls.id) as bill_ids,
						GROUP_CONCAT(billing_mdls.arrears) as bill_arrears,
						GROUP_CONCAT(billing_mdls.penalty) as bill_penalty,
						GROUP_CONCAT(billing_mdls.billing_total) as bill_current,
						GROUP_CONCAT(billing_mdls.discount) as bill_discount
					'))
					->leftJoin('billing_mdls', 'billing_mdls.id', '=', 'collections.billing_id')
					->where('collection_type', 'bill_payment')
					->orderBy('collections.invoice_num', 'asc')
					->groupBy('collections.zone_id')
					->get();


		$coll_non_water = Collection::where('collections.payment_date', 'like', $date1.'%')
					->select(DB::raw('
						collections.zone_id,
						SUM(collections.payment) as nw_zone_collect
					'))
					->where('collections.collection_type', 'non_water_bill_payment')
					->groupBy('collections.zone_id')
					->get();

		$non_water_total = array();
		foreach($coll_non_water as $cnw)
		{
			$non_water_total[$cnw->zone_id] = $cnw->nw_zone_collect;
		}

		//~ echo '<pre>';
		//~ print_r($coll_non_water->toArray());
		//~ die();

		$zones = Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')->get();

		foreach($coll as $kk => $c1)
		{

			$arrear_curr = array();
			$bill_curr = array();

			$bill_ids = explode(',',$c1->bill_ids);
			$bill_arrears = explode(',',$c1->bill_arrears);
			$bill_current = explode(',',$c1->bill_current);


			foreach($bill_ids as $k2 => $v2)
			{
				$arrear_curr[$v2] =  @$bill_arrears[$k2];
				$bill_curr[$v2] =  @$bill_current[$k2];
			}

			$c1->total_arrear = @array_sum($arrear_curr);
			$c1->total_current_bill = @array_sum($bill_curr);

		}

		$zonedata = array();
		foreach($coll as $cc)
		{
			$zonedata[$cc->zone_id]  = $cc;
		}


		//~ echo '<pre>';
		//~ print_r($coll->toArray());
		//~ die();

		//~ pdf_heading1('Daily Collection Report Summary');
		pdf_heading1($title1_x, $date1_x);

		$x = Fpdf::GetX();
		$y = Fpdf::GetY();

		$x = Fpdf::GetX();
		$y = Fpdf::GetY();
		$x = 0;

		Fpdf::MultiCell(20,6,'Zone','LTB' , 'L');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Total Collected','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,6,'Water Bill','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Non-Water Bill','LTB' , 'C');
		$x += 25;
		//~ Fpdf::SetXY($x, $y);
		//~ Fpdf::MultiCell(25,2,'None - Water Bill Collection','LTB' , 'C');
		//~ $x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Current Year Arrears','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Previous Year Arrears','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(30,6,'W/Tax','LTB' , 'C');
		$x += 30;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(30,6,'Penalty','LTBR' , 'C');
		/*
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
		*/

		//~ Fpdf::Ln();

		$total_collected = 0;
		$total_billed = 0;
		$total_arrear = 0;
		$total_non_water = 0;
		foreach($zones as $zz)
		{
			$total_collected+=@$zonedata[$zz->id]->zone_collected +@$non_water_total[$zz->id];
			$total_billed+=@$zonedata[$zz->id]->total_current_bill;
			$total_arrear+=@$zonedata[$zz->id]->total_arrear;
			$total_non_water += @$non_water_total[$zz->id];

			Fpdf::SetLeftMargin(5);
			Fpdf::Cell(20,4, $zz->zone_name, '',0,'L', false);
			Fpdf::Cell(25,4, number_format((@$zonedata[$zz->id]->zone_collected + @$non_water_total[$zz->id]), 2), '',0,'R', false);
			//~ Fpdf::Cell(25,4, number_format(@$zonedata[$zz->id]->payment, 2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$zonedata[$zz->id]->total_current_bill, 2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$non_water_total[$zz->id], 2), '',0,'R', false);
			//~ Fpdf::Cell(25,4, number_format(0, 2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$zonedata[$zz->id]->total_arrear, 2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(0, 2), '',0,'R', false);
			Fpdf::Cell(30,4, number_format(0, 2), '',0,'R', false);
			Fpdf::Cell(30,4, number_format(0, 2), '',0,'R', false);
			Fpdf::Ln();
		}

		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(205,2, '', 'T',0,'L', false);
		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(20,4, 'Total', '',0,'L', false);
		Fpdf::Cell(25,4, number_format($total_collected, 2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format($total_billed, 2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format($total_non_water, 2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format($total_arrear, 2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0, 2), '',0,'R', false);
		Fpdf::Cell(30,4, number_format(0, 2), '',0,'R', false);
		Fpdf::Cell(30,4, number_format(0, 2), '',0,'R', false);
		Fpdf::Ln();


		Fpdf::Ln();
		Fpdf::Ln();

		pdf_footer_signature();

		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;

	}//


	function ReportMonthly()
	{
		$this->ReportDaily('monthly');
	}

	function ReportMonthlySummary()
	{
		//~ $this->ReportDailySummary('monthly');

		$date1 = @$_GET['dd'];
		$date1 = strtotime($date1);

		if(!$date1){
			echo 'INVALID DATE';
			return;
		}

		$date2 = date('Y-m',$date1);

		$sql1 = "
			SELECT
				zone_id,
				SUM((COL1)) AS COL1,
				SUM((CUR1)) as CUR1,
				SUM((CARR1)) AS CARR1,
				SUM(NWC) as NWC,
				SUM(WTAX) as WTAX,
				SUM(PEN) as PEN,
				SUM(PREV_ARR) as PREV_ARR

			FROM
			(
				SELECT
						zone_id,
						((fcollected)) AS COL1,
						((fcurrent)) as CUR1,
						((farrear)) AS CARR1,
						(fnon_wat) as NWC,
						(ftax) as WTAX,
						(penalty) as PEN,
						(fprv_arr) as PREV_ARR


						FROM `report1s`
				WHERE dperiod like '$date2%'
			) AS TAB1
			GROUP BY zone_id
		";

		$rss1 = DB::select($sql1);

		// echo '<pre>';
		// print_r($rss1);
		// die();


		$date3 = date('F Y',$date1);

		$pdf = PDF::loadView('reports.collection_montly1', compact(
			'rss1',
			'date3'
		));

		return $pdf->stream('montly_collection_summary_'.date('Ymd').'.pdf');
		echo '<pre>';
		print_r($rss1);
	}//

	function ReportAnnually()
	{
	}


	function ReportAnnuallySummary()
	{

		$date1 = date('Y-m-d');
		$title1_x = 'Annual Collection Report Summary';
		$date1_x = date('Y');

		pdf_heading1($title1_x, 'Year '.$date1_x);

		Fpdf::Cell(0,10, 'By Year ', '',0,'L', false);
		Fpdf::Ln();

		$this->__annual_heading1('Month');


		$total_all = 0;
		$total_water = 0;
		$total_non_water = 0;
		$total_MM_arrear = 0;
		$A4_ttl = 0;

		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(145,2, '', '',2,'L', false);

		for($v=1;$v<=12;$v++)
		{

			$dd1 = date('Y-m', strtotime($date1_x.'-'.$v));
			$coll_all = help_collector_monthly($dd1);

			$A1 = $coll_all->ttl;
			$A2 = $coll_all->bill1;
			$A3 = $coll_all->ttl_nw;
			$A4 = $coll_all->ttl_tax;
			$A5 = 0;
			$A6 = $coll_all->penalty1;
			$A7 = $coll_all->arrear1;

			$total_all += @$A1;
			$total_water += @$A2;
			$total_non_water += @$A3;
			$total_MM_arrear += @$A7;

			$A4_ttl += $A4;


			$mm = date('F', strtotime($date1_x.'-'.$v));
			Fpdf::SetLeftMargin(5);
			Fpdf::Cell(20,4, $mm, '',0,'L', false);
			Fpdf::Cell(25,4, number_format(@$A1,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A2,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A3,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A4,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A5,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A6,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A7,2), '',0,'R', false);
			Fpdf::Ln();

		}

		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(195,2, '', 'B',2,'L', false);
		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(20,4, 'Total', '',0,'L', false);
		Fpdf::Cell(25,4, number_format(@$total_all,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_water,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_non_water,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format($A4_ttl,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_MM_arrear,2), '',0,'R', false);
		Fpdf::Ln();

		//~ echo '<pre>';
		//~ print_r($coll->toArray());
		//~ die();
		Fpdf::Ln();
		Fpdf::Ln();

		Fpdf::Cell(0,10, 'By Zone ', '',0,'L', false);
		Fpdf::Ln();
		$this->__annual_heading1('Zone');

		$zones = Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')->get();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(145,2, '', '',2,'L', false);

		$total_all_by_zone = 0;
		$total_water_by_zone = 0;
		$total_non_water_by_zone = 0;
		$total_MM_arrear_by_zone = 0;
		$A4_ttl = 0;

		foreach($zones as $zz)
		{
			$v = $zz->id;

			$dd1 = date('Y', strtotime($date1_x.'-01'));
			$coll_all = help_collector_monthly($dd1, $v);

			//~ echo '<pre>';
			//~ print_r($coll_all->toArray());

			$A1 = $coll_all->ttl;
			$A2 = $coll_all->bill1;
			$A3 = $coll_all->ttl_nw;
			$A4 = $coll_all->ttl_tax;
			$A5 = 0;
			$A6 = 0;
			$A7 = $coll_all->arrear1;

			$total_all_by_zone += @$A1;
			$total_water_by_zone += @$A2;
			$total_non_water_by_zone += @$A3;
			$total_MM_arrear_by_zone += @$A7;
			$A4_ttl += $A4;

			Fpdf::SetLeftMargin(5);
			Fpdf::Cell(20,4, $zz->zone_name, '',0,'L', false);
			Fpdf::Cell(25,4, number_format(@$A1,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A2,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A3,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A4,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A5,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A6,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$A7,2), '',0,'R', false);
			Fpdf::Ln();
		}//

		//~ die();

		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(195,2, '', 'B',2,'L', false);
		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(20,4, 'Total', '',0,'L', false);
		Fpdf::Cell(25,4, number_format(@$total_all_by_zone,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_water_by_zone,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_non_water_by_zone,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format($A4_ttl,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_MM_arrear_by_zone,2), '',0,'R', false);
		Fpdf::Ln();

		Fpdf::Ln();
		Fpdf::Ln();


		pdf_footer_signature();

		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;






	}/////


	function ReportAnnuallySummary_001()
	{


		$date1 = date('Y-m-d');
		$title1_x = 'Annual Collection Report Summary';
		$date1_x = date('Y');
		$raw_db = DB::raw('
								collections.*,
								MONTH(payment_date) as month1,
								SUM(payment) as m_total
							');

		$coll_all = Collection::where('payment_date', 'like', $date1_x.'%')
					->select($raw_db)
					->groupBy('month1')
					->orderBy('invoice_num', 'asc')
					->get();

		$coll_water_bill = Collection::where('payment_date', 'like', $date1_x.'%')
					->where('collection_type', 'bill_payment')
					->select($raw_db)
					->groupBy('month1')
					->orderBy('invoice_num', 'asc')
					->get();

		$coll_non_water_bill = Collection::where('payment_date', 'like', $date1_x.'%')
					->where('collection_type', 'non_water_bill_payment')
					->select($raw_db)
					->groupBy('month1')
					->orderBy('invoice_num', 'asc')
					->get();

		$coll_by_zone_all = Collection::where('payment_date', 'like', $date1_x.'%')
						//->where('collection_type', 'bill_payment')
						->select($raw_db)
						->groupBy('zone_id')
						->orderBy('invoice_num', 'asc')
						->get();

		$coll_by_zone = Collection::where('payment_date', 'like', $date1_x.'%')
					->where('collection_type', 'bill_payment')
					->select($raw_db)
					->groupBy('zone_id')
					->orderBy('invoice_num', 'asc')
					->get();

		$coll_by_zone_non_water = Collection::where('payment_date', 'like', $date1_x.'%')
					->where('collection_type', 'non_water_bill_payment')
					->select($raw_db)
					->groupBy('zone_id')
					->orderBy('invoice_num', 'asc')
					->get();

		//~ $bill_info  = $this->_bill_info_pay($date1_x);
		//~ echo '<pre>';
		//~ print_r($coll_by_zone_non_water->toArray());
		//~ echo $date1_x;
		//~ die();

		$col_zone_water  = array();

		foreach($coll_by_zone as $cbz)
		{
			$bb_info = $this->_bill_info_pay($date1_x, $cbz->zone_id);
			//~ $col_zone_water[$cbz->zone_id]['water'] = $cbz->payment;
			$col_zone_water[$cbz->zone_id]['water'] = $bb_info[0]['MM_bill'];
			$col_zone_water[$cbz->zone_id]['MM_arrear'] = $bb_info[0]['MM_arrear'];
			$col_zone_water[$cbz->zone_id]['MM_discount'] = $bb_info[0]['MM_discount'];
			$col_zone_water[$cbz->zone_id]['MM_penalty'] = $bb_info[0]['MM_penalty'];
		}

		foreach($coll_by_zone_non_water as $cbz)
		{
			$col_zone_water[$cbz->zone_id]['non_water'] = $cbz->m_total;
		}

		foreach($coll_by_zone_all as $cbz)
		{
			$col_zone_water[$cbz->zone_id]['all'] = $cbz->m_total;
		}

		//~ echo '<pre>';
		//~ print_r($col_zone_water);
		//~ die();

		$month_arr = array();

		foreach($coll_all as $cc)
		{
			$month_arr[$cc->month1]['all'] = $cc->m_total;
		}

		foreach($coll_water_bill as $cc)
		{
			$dd = date('Y-m',strtotime($date1_x.'-'.$cc->month1));
			$bill_info  = $this->_bill_info_pay($dd);

			//~ $month_arr[$cc->month1]['water'] = $cc->m_total;
			$month_arr[$cc->month1]['water'] = $bill_info[0]['MM_bill'];
			$month_arr[$cc->month1]['MM_arrear'] = $bill_info[0]['MM_arrear'];
			$month_arr[$cc->month1]['MM_discount'] = $bill_info[0]['MM_discount'];
			$month_arr[$cc->month1]['MM_penalty'] = $bill_info[0]['MM_penalty'];
		}

		foreach($coll_non_water_bill as $cc)
		{
			$month_arr[$cc->month1]['non_water'] = $cc->m_total;
		}

		//~ echo '<pre>';
		//~ print_r($month_arr);
		//~ die();


		pdf_heading1($title1_x, 'Year '.$date1_x);

		Fpdf::Cell(0,10, 'By Year ', '',0,'L', false);
		Fpdf::Ln();

		$this->__annual_heading1('Month');


		$total_all = 0;
		$total_water = 0;
		$total_non_water = 0;
		$total_MM_arrear = 0;
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(145,2, '', '',2,'L', false);

		for($v=1;$v<=12;$v++)
		{

			$total_all += @$month_arr[$v]['all'];
			$total_water += @$month_arr[$v]['water'];
			$total_non_water += @$month_arr[$v]['non_water'];
			$total_MM_arrear += @$month_arr[$v]['MM_arrear'];

			$mm = date('F', strtotime($date1_x.'-'.$v));
			Fpdf::SetLeftMargin(5);
			Fpdf::Cell(20,4, $mm, '',0,'L', false);
			Fpdf::Cell(25,4, number_format(@$month_arr[$v]['all'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$month_arr[$v]['water'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$month_arr[$v]['non_water'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$month_arr[$v]['MM_discount'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$month_arr[$v]['MM_penalty'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$month_arr[$v]['MM_arrear'],2), '',0,'R', false);
			Fpdf::Ln();
			//~ echo date('F', strtotime($date1_x.'-'.$v));
			//~ echo '<br />';
		}

		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(195,2, '', 'B',2,'L', false);
		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(20,4, 'Total', '',0,'L', false);
		Fpdf::Cell(25,4, number_format(@$total_all,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_water,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_non_water,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_MM_arrear,2), '',0,'R', false);
		Fpdf::Ln();

		//~ echo '<pre>';
		//~ print_r($coll->toArray());
		//~ die();
		Fpdf::Ln();
		Fpdf::Ln();

		Fpdf::Cell(0,10, 'By Zone ', '',0,'L', false);
		Fpdf::Ln();
		$this->__annual_heading1('Zone');

		$zones = Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')->get();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(145,2, '', '',2,'L', false);

		$total_all_by_zone = 0;
		$total_water_by_zone = 0;
		$total_non_water_by_zone = 0;
		$total_MM_arrear_by_zone = 0;

		foreach($zones as $zz)
		{
			$v = $zz->id;

			$total_all_by_zone += @$col_zone_water[$v]['all'];
			$total_water_by_zone += @$col_zone_water[$v]['water'];
			$total_non_water_by_zone += @$col_zone_water[$v]['non_water'];
			$total_MM_arrear_by_zone += @$col_zone_water[$v]['MM_arrear'];

			Fpdf::SetLeftMargin(5);
			Fpdf::Cell(20,4, $zz->zone_name, '',0,'L', false);
			Fpdf::Cell(25,4, number_format(@$col_zone_water[$v]['all'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$col_zone_water[$v]['water'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$col_zone_water[$v]['non_water'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$col_zone_water[$v]['MM_discount'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$col_zone_water[$v]['MM_penalty'],2), '',0,'R', false);
			Fpdf::Cell(25,4, number_format(@$col_zone_water[$v]['MM_arrear'],2), '',0,'R', false);
			Fpdf::Ln();
		}//
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(195,2, '', 'B',2,'L', false);
		Fpdf::Ln();
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(20,4, 'Total', '',0,'L', false);
		Fpdf::Cell(25,4, number_format(@$total_all_by_zone,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_water_by_zone,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_non_water_by_zone,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(0,2), '',0,'R', false);
		Fpdf::Cell(25,4, number_format(@$total_MM_arrear_by_zone,2), '',0,'R', false);
		Fpdf::Ln();

		Fpdf::Ln();
		Fpdf::Ln();


		pdf_footer_signature();

		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;

	}//End


	function		_bill_info_pay($date1_x, $zone=0)
	{
		$raw_db = DB::raw('
									SUM(billing_mdls.billing_total) as MM_bill,
									SUM(billing_mdls.arrears) as MM_arrear,
									SUM(billing_mdls.discount) as MM_discount,
									SUM(billing_mdls.penalty) as MM_penalty
							');

		$test2   = BillingMdl::whereHas('collection', function($query)use($date1_x, $zone){

							$query->where('payment_date', 'like', $date1_x.'%');

							if($zone != 0)
							{
								$query->where('zone_id', $zone);
							}

						})
						->select($raw_db)
						->get();

		return $test2->toArray();
	}//

	function		_coll_info_pay($date1_x)
	{

		$raw_db = DB::raw('
								collections.collection_type,
								collections.payment_date,
								collections.zone_id,
								collections.billing_id,
								MONTH(collections.payment_date) as month1,
								SUM(collections.payment) as payment_by_bill
							');

		$test1 = Collection::where('collections.payment_date', 'like', $date1_x.'%')
					->where('collections.collection_type', 'bill_payment')
					->select($raw_db)
					->groupBy('month1')
					->orderBy('collections.invoice_num', 'asc')
					->get();

		$test2  = $this->_bill_info_pay($date1_x);
		return  $test2;
	}

	function 	__annual_heading1($tt = 'Month')
	{

		$x = Fpdf::GetX();
		$y = Fpdf::GetY();
		$x = 0;

		Fpdf::MultiCell(20,6,$tt,'LTB' , 'L');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,6,'Collected','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,6,'Water Bill','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,3,'Non-Water Bill','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,6,'W/Tax','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,6,'Discounts','LTB' , 'C');
		$x += 25;
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,6,'Penalty','LTB' , 'C');
		$x += 25;

		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(25,6,'Arrears','LTBR' , 'C');
		$x += 25;
	}



	/*Load Data*/




}
