<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

use App\BillingNw;

// use App\AccountMetas;
// use App\Accounts;
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
// use App\LedgerData;

// use App\Http\Controllers\HwdLedgerCtrl;
// use App\Http\Controllers\LedgerCtrl;
// use Mike42\Escpos\Printer;
// use Mike42\Escpos\PrintConnectors\FilePrintConnector;


class NonWaterBillCtrl extends Controller
{

	
	function nwb_add_new(Request $request)
	{
		$post     = $request->all();
		$d_start  = $post['paya_date_start'];
		$ttl_amt  = $post['paya_amount'];
		$per_bill = $post['paya_per_bill'];
		$today    = date('Y-m-d');

		$acct_id = $post['acct_id'];
		$paya_id = $post['other_paya_id'];
		$paya_name = $post['paya_name'];
		$paya_desc = $post['paya_desc'];
		$paya_code = $post['paya_code'];
		
		if(strtotime($today) > strtotime($d_start)) {
			// echo 'ERROR: Billing date should not be backward';
			// return ;
		}

		if(
			$ttl_amt <= 0 || 
			$per_bill <= 0 || 
			$ttl_amt < $per_bill 
		 ) {
			echo 'ERROR: Please check the amount and the per billing amount';
			return ;
		}

		$ttl_months = ceil($ttl_amt / $per_bill);

		$paya_months = [];
		$new_ttl_amt = $ttl_amt; 
		for($x=0;$x<$ttl_months;$x++) 
		{
			$deduct_11 = $per_bill;
			if( $new_ttl_amt <= $per_bill) {
				$deduct_11 = $new_ttl_amt;
			}
			$new_ttl_amt -=  $per_bill;
			$paya_months[] = [
								'acct_id' => $acct_id,
								'typ' => 'nw_child',
								'date1' => date('Y-m-01', strtotime($d_start.' +'.$x.' Months')), 
								'amt_1' => $deduct_11,
								'title' => $paya_name.' - Partial ',
								'status' => 'pending',
								'code1' => $paya_code,
							];
		}

		######
		######
		######
		$new_nwbill = new BillingNw;
		$new_nwbill->acct_id = $acct_id;
		$new_nwbill->typ = 'nw_mother';

		$new_nwbill->amt_1 = $ttl_amt;
		$new_nwbill->amt_2 = $per_bill;
		$new_nwbill->date1 = $d_start;

		$new_nwbill->paya_id = $paya_id;
		$new_nwbill->title = $paya_name;
		$new_nwbill->remark = $paya_desc;
		$new_nwbill->code1 = $paya_code;
		$new_nwbill->status = 'pending';
		$new_nwbill->save();

		foreach($paya_months as $k => $v ) {
			$v['id1'] = $new_nwbill->id;
			$paya_months[$k] = $v;
		}

		######
		######
		######
		BillingNw::insert($paya_months);

		// DB::table('billing_nws')->insert([
		// 	'acct_id' => '',
		// 	'paya_id' => '',
		// 	'typ' => '',
		// ]);

		ee1($post, __FILE__, __LINE__);
		ee($paya_months, __FILE__, __LINE__);
		
	}//

	function nwb_get_list()
	{
		$acct_id = (int) $_GET['acct_id'];

		$nw_bills = BillingNw::where('typ', 'nw_mother')
						->where('acct_id', $acct_id)
						->get()
						->toArray();
		// ee($nw_bills, __FILE__, __LINE__);

		$strres ='';

		foreach($nw_bills as $k => $v) {

			$ttl_months = ceil($v['amt_1'] / $v['amt_2']) - 1;

			$strres.= '
					<tr>
						<td>#'.$v['id'].'</td>
						<td>'.$v['title'].'</td>
						<td>'.$v['code1'].'</td>
						<td>'.number_format($v['amt_1'],2).'</td>
						<td>'.number_format($v['amt_2'],2).'</td>
						<td>'.date('F Y', strtotime($v['date1'])).'</td>
						<td>'.date('F Y', strtotime($v['date1'].' + '.$ttl_months.' Months' )).'</td>
						<td>#'.$v['status'].'</td>
						<td>
						'.( $v['status']=='active'?'':'
							<a onclick="delete_nw_bill('.$v['id'].')">Delete</a>
							| 
							<a onclick="set_nw_bill_active('.$v['id'].')">Set Active</a>
							').'
						</td>
					</tr>
			';
		}//		


echo  '
		<table class="led01 acct_ledger01 table10"><tbody>
		<tr class="headings">
			<td>ID</td>
			<td>Title</td>
			<td>Code</td>
			<td>Total</td>
			<td>Per-billing</td>
			<td>Billing Start</td>
			<td>Billing End</td>
			<td>Status</td>
			<td>CMD</td>
		</tr>
		'.$strres.'
		</tbody></table>	

		<br />
		<br />
		<br />
		<br />
		<br />
';		

	}//

	function nwb_delete()
	{
		$nw_id = (int) $_GET['nw_id'];

		if( $nw_id <= 0 ) {
			echo 'ERROR: Id Not found';
			return ;
		}

		$billingNW = BillingNw::where('status', 'pending')
								->where('typ', 'nw_mother')
								->where('id', $nw_id)
								->first();

		if(!$billingNW) {
			echo 'ERROR: Id Not found 2';
			return ;
		}

		BillingNw::where('status', 'pending')
					->where('typ', 'nw_child')
					->where('id1', $billingNW->id)
					->delete();
		sleep(1);

		$billingNW->delete();

		echo 'SUCCESS: DELETED';

	}//

	function nwb_set_active()
	{
		$nw_id = (int) $_GET['nw_id'];

		if( $nw_id <= 0 ) {
			echo 'ERROR: Id Not found';
			return ;
		}

		$billingNW = BillingNw::where('status', 'pending')
								->where('typ', 'nw_mother')
								->where('id', $nw_id)
								->first();

		if(!$billingNW) {
			echo 'ERROR: Id Not found 2';
			return ;
		}

		$billingNW->status = 'active';
		$billingNW->update();

		echo 'SUCCESS: Updated';


	}//

}
