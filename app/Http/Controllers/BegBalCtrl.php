<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

// use App\BillingNw;

// use App\AccountMetas;
use App\Accounts;
// use App\Zones;
// use App\HwdRequests;
// use App\Reading;
// use App\HwdOfficials;
// use App\BillingMdl;
// use App\BillingMeta;
// use App\BillingRateVersion;
// use App\BillingDue;
// use App\HwdJob;
// use App\Reports;
// use App\BillPrint;
// use App\Collection;
// use App\Arrear;
// use App\ReadingPeriod;
// use App\ServiceBillZone;
// use App\OverdueStat;
use App\LedgerData;

// use App\Http\Controllers\HwdLedgerCtrl;
// use App\Http\Controllers\LedgerCtrl;
// use Mike42\Escpos\Printer;
// use Mike42\Escpos\PrintConnectors\FilePrintConnector;


class BegBalCtrl extends Controller
{

	function save_beginning_balance()
	{

		$date1   = @$_POST['date1'];
		$acct_id = (int) @$_POST['acct_id'];
		$ttl_bal = (float) @$_POST['ttl_bal'];

		####
		####
		if( !strtotime(@$_POST['date1']) ) {  $date1 = date('Y-m-d'); }
		if( $acct_id <= 0 ) {
			echo 'ERROR : Account not found. ';
			die();
		}
		####
		####
		$account = Accounts::find($acct_id);
		if( !$account ) {
			echo 'ERROR : Account not found. ';
			die();
		}
		####
		####
		if( $ttl_bal <= 0 ) {
			echo 'ERROR : Beginning balance  ';
			die();
		}
		####
		####

		$ttl_count = LedgerData::where('acct_id', $acct_id)->where('status', 'active')->count();
		echo $ttl_count; 
		####
		####
		if( $ttl_count > 0 ) {
			echo 'ERROR : Ledger already has value  ';
			die();
		}
		####
		####


		
		$py_arrear = (float) @$_POST['py_arrear'];
		$cy_arrear = (float) @$_POST['cy_arrear'];
		$nwb_arrear = (float) @$_POST['nwb_arrear'];
		$current = (float) @$_POST['current'];
		$penalty = (float) @$_POST['penalty'];

		LedgerData::insert([
			'acct_id' => $acct_id,
			'status' => 'active',
			'led_type' => 'beginning',
			'period' => date('Y-m-01', strtotime($date1)),
			'date01' => $date1,
			'arrear' => $ttl_bal - $nwb_arrear,
			'ttl_bal' => $ttl_bal - $nwb_arrear,
			'beg_data1' => json_encode([
								'ttl_bal' => $ttl_bal - $nwb_arrear,
								'py_arrear' => $py_arrear,
								'cy_arrear' => $cy_arrear,
								'nwb_arrear' => $nwb_arrear,
								'date1' => $date1,
								'current' => $current,
								'penalty' => $penalty,
							]),
		]);

		if( $nwb_arrear >= 1 ) 
		{
				LedgerData::insert([
					'acct_id' => $acct_id,
					'status' => 'active',
					'led_type' => 'nw_billing',
					'ledger_info' => 'NWB Beginning',
					'period' => date('Y-m-01', strtotime($date1)),
					'date01' => $date1,
					'billing' => $nwb_arrear,
					'ttl_bal' => $ttl_bal,
				]);			
		}//


		echo 'SUCCESS : SAVED  ';


		// led_type
		// period
		// date01
		// arrear
		// ttl_bal
		// acct_id 
		// beg_data1
		// ee(@$_POST, __FILE__, __LINE__);

		// [ttl_bal] => 500
		// [py_arrear] => 
		// [cy_arrear] => 500
		// [nwb_arrear] => 
		// [date1] => 2025-10-01
		// [_token] => Dln5FjG31nwkqkzSGbwgcVOxOAryth8MtGboQ5K9
		// [acct_id] => 1

		// billing
		// ledger_info
		// led_type = nw_billing
		// period
		// date01
		// ttl_bal
		// acct_id
		// reff_no		


	}//


	function test0001()
	{
		die();
		die();
		die();
		
		$rs1 = DB::select("
					SELECT accounts11.* 
						FROM accounts11 
					WHERE 
						NOT EXISTS
						(
							SELECT accounts.* FROM  accounts WHERE accounts11.id=accounts.id LIMIT 1
						) 
					LIMIT 100
			");

		
		if(  count($rs1) <= 0 ) {
			echo 'TAPOS';
			die();
		}

		$re_insert = [];
		foreach($rs1 as $k => $v) {
			$v = ( array ) $v;
			extract($v);
			$re_insert[] = [
				'id' => $id,
				'acct_no' => $acct_no,
				'lname' => $lname,
				'fname' => $fname,
				'mi' => $mi,
				'address1' => $address1,
				'zone_id' => $zone_id,
				'acct_type_key' => $acct_type_key,
				'acct_status_key' => $acct_status_key,
				'acct_discount' => $acct_discount,
				'meter_size_id' => $meter_size_id,
				'install_date' => $install_date,
				'route_id' => $route_id,
				'meter_number1' => $meter_number1,
				'mi' => $mi,
				'status' => $status,
			];
		}

		Accounts::insert($re_insert);

		// ee($re_insert, __FILE__, __LINE__);


		echo "
		Ploading Please wait....
		<script>
		setTimeout(function(){
			window.location.reload();
		}, 500);		
		</script>
		";

		ee($re_insert, __FILE__, __LINE__);

	}//



	function test0002()
	{

		// echo '<h1>PAUSED</h1>'; 
		// die();

		$rs1 = Accounts::
				whereNull('temp01')
				// whereNotNull('temp01')
				// ->where('id', '678')
				->with(['ledger_data2' => function($q1) {
					$q1->where('status', 'active');
					// $q1->orderBy('id', 'asc');
					$q1->orderBy('date01', 'asc');
					$q1->orderBy('id', 'asc');
				}])
				->orderBy('id', 'asc')
				->limit(20)
				->get();
		
		// ee($rs1->toArray(), __FILE__, __LINE__);


		if( $rs1->count() <= 0 ) {
			echo '<h1>DONE DONE</h1>'; 
			die();
		}

		// echo $rs1->count();
		// die();

		$rs2 = Accounts::whereNull('temp01')->count();
		echo '<h1> Remaining :: '.$rs2.'</h1>'; 

			
		// ee($rs1->toArray(), __FILE__, __LINE__);

		// if(empty($rs1) )

		foreach($rs1 as $k => $v)
		{
			## BEGINNING 
			// $ttl_bal1 = 0; 
			// foreach( $v->ledger_data2 as $k2 => $v2 ) {
			// 	if( $v2->led_type == 'beginning' ) 
			// 	{
			// 		$ttl_bal1 = $v2->arrear;
			// 		$v2->ttl_bal = $ttl_bal1;
			// 		$v2->save();
			// 		unset($v->ledger_data2[$K2]);
			// 		break;
			// 	}
			// }

			$ttl_bal1 = 0; 
			foreach( $v->ledger_data2 as $k2 => $v2 ) {
				$ttl_bal1 +=  $v2->debit01 - $v2->credit01;
				$v2->ttl_bal = $ttl_bal1;
				$v2->save();
			}

			$v->temp01 = 1;
			$v->save();
		}//

		echo "
		Ploading Please wait....
		<script>
		setTimeout(function(){
			window.location.reload();
		}, 500);		
		</script>
		";

		ee($rs1->toArray(), __FILE__, __LINE__);



	}//


}
