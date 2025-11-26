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

use PDF;
use Fpdf;

use App\Http\Controllers\HwdLedgerCtrl;
use App\Http\Controllers\LedgerCtrl;


use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintBuffers\ImagePrintBuffer;
use Mike42\Escpos\CapabilityProfile;



class PrintCtrl extends Controller
{
	
	function disconnection_notice_print_pdf($zone_id,$period, $dis_date)
	{
		//~ echo $dis_date;
		//~ echo '<br />';
		//~ echo $period;
		//~ echo '<br />';
		//~ echo $zone_id;

		$sql22 = "
				SELECT TAB1.*,
						LD1.ttl_bal,
						LD1.date01,
						LD1.led_type,
						LD1.period,
						BM1.curr_reading,
						AA.acct_no,
						AA.fname,
						AA.lname,
						old_route,
						AA.address1,
						AA.meter_number1  FROM
					(
						SELECT MAX(id) as last_id,acct_id
							FROM `ledger_datas`
								group by acct_id
					) as TAB1

				LEFT JOIN ledger_datas as LD1
					ON LD1.id=TAB1.last_id

				LEFT JOIN accounts AA
					ON AA.id = TAB1.acct_id

				LEFT JOIN readings as BM1
					ON (AA.id = BM1.account_id AND BM1.period=LD1.period)

				WHERE ttl_bal >= 50
					AND (AA.acct_status_key=2 OR AA.acct_status_key=3)
						AND AA.zone_id=$zone_id

					 ORDER BY AA.route_id  ASC
		" ;

		$result1 = DB::select($sql22);

		$n_date = date('l, F d, Y', strtotime($dis_date));

		//~ echo '<pre>';
		//~ print_r($result1);
		//~ die();

		$pdf = PDF::loadView('reports.disconnection_notice', compact('result1','n_date'));
		return $pdf->stream('disconnection_notice'.strtotime('NOW').'.pdf');


		//~ return $pdf->save('disconnection_notice'.strtotime('NOW').'.pdf');
		//~ return $pdf->render();
	}	
	
	
	
	
	function for_disconnection_list_v2($date1, $zone)
	{
		return $this->disconnection_notice_list_pdf($date1, $zone);
	}


	function disconnection_notice_list_pdf($date1, $zone_id)
	{

		$sql22 = "
				SELECT TAB1.*,
						LD1.ttl_bal,
						LD1.date01,
						LD1.led_type,
						LD1.period,
						BM1.curr_reading,
						AA.acct_no,
						AA.fname,
						AA.lname,
						old_route,
						AA.address1,
						AA.meter_number1  FROM
					(
						SELECT MAX(id) as last_id,acct_id
							FROM `ledger_datas`
								group by acct_id
					) as TAB1

				LEFT JOIN ledger_datas as LD1
					ON LD1.id=TAB1.last_id

				LEFT JOIN accounts AA
					ON AA.id = TAB1.acct_id

				LEFT JOIN readings as BM1
					ON (AA.id = BM1.account_id AND BM1.period=LD1.period)

				WHERE ttl_bal >= 1
					AND (AA.acct_status_key=2 OR AA.acct_status_key=3)
						AND AA.zone_id=$zone_id

					 ORDER BY AA.route_id  ASC
		" ;

		$result1 = DB::select($sql22);

		// echo '<pre>';
		// print_r($result1);
		// die();

		foreach($result1 as $m)
		{
			$acc1 = $m->acct_id;
			$my_id = $m->last_id;
			$or_ttl_bal = (float) @$m->ttl_bal;
			$ttl_bal = (float) @$m->ttl_bal;

			$sql2xx = "
				SELECT * FROM `ledger_datas`
				WHERE acct_id=$acc1
				 AND(led_type='billing' OR led_type='beginning')
				 ORDER BY id desc
			";

			$rs2 = DB::select($sql2xx);

			$xx1 = 0;

			//~ echo '<pre>';
			//~ echo $ttl_bal;
			//~ echo '<br />';

			//~ echo $ttl_bal;
			//~ die();

			$curr_period = date('Y-m-01');
			$mmm = 0;

			foreach($rs2 as $r1){

				$curr_period = $r1->period;

				//~ print_r($r1);

				if($r1->led_type == 'billing'){
					$ttl_bal -= $r1->billing;
				}

				if($r1->led_type == 'beginning'){
					$ttl_bal -= $r1->arrear;

					$curr_period = date('Y-m-01', strtotime($r1->date01));
				}

				if($r1->led_type == 'penalty'){
					$ttl_bal -= $r1->penalty;
				}



				if($ttl_bal <= 0){
					$mmm = $ttl_bal;
					break;
				}

			}//

			$m->PER1 = $curr_period;

			$per1 = date('Y-m-d',strtotime(date('Y-m-01').''));

			$datetime1 = date_create($curr_period);
			$datetime2 = date_create($per1);

			$interval = date_diff($datetime1, $datetime2);
			$num_mon = (int) $interval->format('%m');

			if($num_mon == 0 && $or_ttl_bal > 0){
				$num_mon = 1;
			}

			$m->PER2 = $num_mon;

			//~ $m->PER1X = round($mmm,2);

			//~ echo '<pre>';
			//~ print_r($rs2);
			//~ echo '<hr />';
			//~ echo '<hr />';
			//~ echo '<hr />';
			//~ die();
			//~ $m->last_id
			//~ $m->acct_id
		}

		$zon_d = Zones::find($zone_id);
		$zon_name = strtoupper($zon_d->zone_name);

		// echo '<pre>';
		// print_r($result1);

		$pdf = PDF::loadView('billings.inc.billing_billing.report_pdf.disconnection_list', compact('result1', 'zon_name'));
		return $pdf->stream('disconnection_list'.strtotime('NOW').'.pdf');


	}

	function disconnection_notice_list($date1, $zone_id, $acct_id)
	{
		//~ return $this->disconnection_notice_list_pdf($date1, $zone_id, $acct_id);
		//~ die();

		$date_start = date('Y-m-01', strtotime($date1));
		//~ $date_start = date('Y-m-d', strtotime($date1));
		$period1 = date('Y-m', strtotime($date1));

		$discon_list_raw = Accounts::where('zone_id', $zone_id)
						->whereHas('bill1',  $bill1 = function($query)use($date1, $date_start, $period1){
							//~ $query->where('period', 'like', $period1.'%');
						})
						->whereHas('ledger_data3', $ledger_data3 = function($query)use($date1, $date_start){
							$query->where('date01','<=', $date1);
							//~ $query->where('date01','>=', $date_start);
							$query->where('led_type', '!=', 'beginning');
							$query->where('status', 'active');
						})
						->where('acct_status_key', '2')
						->with(['ledger_data3' => $ledger_data3])
						->with(['bill1' => $bill1])
						->orderBy('route_id', 'asc')
						->get();


			$discon_list = [];

			$sk11 = true;

			if($acct_id != 'none'){
					$sk11 = false;
			}

			foreach($discon_list_raw as $dl)
			{

				if($sk11 == false && $dl->acct_no != $acct_id){
							continue;
				}else{
					$sk11 = true;
				}

				if($dl->ledger_data3->ttl_bal <= 0){continue;}
				//~ $discon_list[] = $dl->toArray();
				$discon_list[] = $dl;
			}

			if(@$_GET['dd'] == 1){
				return $discon_list;
			}


			return view('billings.inc.billing_billing.disconnection_list', compact('discon_list', 'date1', 'acct_id'));

			echo '<pre>';
			print_r($discon_list);



		//~ $exec_date =
			//~ LedgerData::where('led_type', '!=', 'beginning')
				//~ ->where('status', 'active')
				//~ ->where('date01', '<=', $date1)
				//~ ->where('date01', '>=', $date_start)
				//~ ->where('acct_id', $acct_id)
				//~ ->orderBy('date01', 'desc')

	}//

	function reset_billing_number_by_zone($period, $zone_id, $acct_num, $bill_num, $bill_num_end)
	{
		$res1 = $this->before_printing_save_first($period, $zone_id, $acct_num, $bill_num, $bill_num_end, 'get_data_1');

		foreach($res1 as $r)
		{
			$r->bill_num_01 = null;
			$r->save();
		}

	}

	function bill_print_save_billing_number($period, $zone_id, $acct_num, $bill_num, $bill_num_end)
	{
		$res1 = $this->before_printing_save_first($period, $zone_id, $acct_num, $bill_num, $bill_num_end, 'get_data_1');

		$bill_num = (int) $bill_num;
		$bill_num_orig = (int) $bill_num;

		if($bill_num <= 0){return false;}

		$first_bill_num = (int) @$res1[0]->bill_num_01;

		$new_ent = false;

		if($first_bill_num <= 0){
			$first_bill_num = $bill_num;
			$new_ent = true;
		}else{
			//~ $bill_num = $first_bill_num;
			//~ $first_bill_num = $bill_num;
		}



		foreach($res1 as $r1)
		{
			if(@$r1->bill_num_01 > 0)
			{
				continue;
			}

			//~ if($bill_num_orig > $bill_num){
				//~ $bill_num++;
				//~ continue;
			//~ }

			//~ echo '<br />';
			//~ echo $bill_num.' - '.$r1->account->lname.' - '.$r1->account->acct_no.' - '.$r1->account->id;

			$cur_num = (int) @$r1->bill_num_01;



			if($cur_num > 0){
				//~ echo '<br />';
				//~ echo $bill_num.' - '.$r1->account->lname.' - '.$r1->account->acct_no.' - '.$r1->account->id;
			}else{
				$r1->bill_num_01=$bill_num;
				$r1->save();
			}

			$bill_num++;
		}

		echo 'DONE SAVE';
	}//


	function before_printing_save_first($period, $zone_id, $acct_num, $bill_num, $bill_num_end, $cmd='')
	{
		//~ echo $zone_id;
		//~ die();


			$bill001_raw = BillingMdl::
						whereHas('reading1', function($query)use($zone_id, $acct_num){
							//~ $query->where('zone_id', $zone_id);
							//~ if($acct_num != 'none'){
								//~ $query->where('account_number', $acct_num);
							//~ }
						})
						->where('period', 'like', $period.'%')
						->where('billing_total', '!=', 0)
						->with('reading1')
						->with('ledger_data')
						->with('account')
						->leftJoin('accounts', 'accounts.id', '=', 'billing_mdls.account_id')
						->leftJoin('account_metas AS AM','AM.id','=','accounts.acct_type_key')
						->where('accounts.acct_status_key', '2');

						if($zone_id != 'all'){
							$bill001_raw->where('accounts.zone_id', $zone_id);
						}

						$bill001_raw->selectRaw('billing_mdls.*, accounts.route_id, AM.meta_name')
							->orderBy(DB::raw('-billing_mdls.bill_num_01'), 'desc')//'-accounts.account_number', 'desc')
							->orderBy('accounts.route_id', 'asc');


			//~ $bill001_raw->where(function($q22){
					//~ $q22->whereNull('bill_num_01');
					//~ $q22->orWhere('bill_num_01', '');
					//~ $q22->orWhere('bill_num_01','<=', 0);
				//~ });



			$bill001 = $bill001_raw->get();

			//~ echo '<pre>';
			//~ print_r($bill001->toArray());
			//~ die();

			if($cmd == 'get_data_1'){
				return $bill001;
			}


			$url001 = '/bill_print_start_002/'.$period.'/'.$zone_id.'/'.$acct_num.'/'.$bill_num.'/'.$bill_num_end;
			$final_url = 'http://localhost/hwd_print/print2.php?url1='.$url001;
			return Redirect::to($final_url);

	}///

	function billPrintStart_List($period, $zone_id, $acct_num, $bill_num, $bill_num_end)
	{

		    //~ $billing_exist = BillingMdl::where('bill_num_01', $bill_num)->first();

			$bill001 = $this->before_printing_save_first($period, $zone_id, $acct_num, $bill_num, $bill_num_end, 'get_data_1');


			/********/
			/********/
			/********/
			/********/
			return view('billings.inc.billing_billing.bill_printing_ajax',
						compact('bill001', 'bill_num', 'bill_num_end'));

			die();
			die();
			die();
			/********/
			/********/
			/********/
			/********/


			if($acct_num == 'none')
			{
				return view('billings.inc.billing_billing.bill_printing_ajax',
							compact('bill001', 'bill_num', 'bill_num_end'));
			}


			if($bill001->count() == 0)
			{
				return  '<div class="lo11"  style="width:700px;">No Result</div>';
			}

			//~ $min_id = $bill001[0]->id;
			$min_id = $bill001[0]->account->route_id;

		    if($billing_exist)
		    {
				$min_id = $billing_exist->account->route_id;
				//~ $acct_num = $billing_exist->account->acct_no;
			}//



			$bill002_raw = BillingMdl::
						whereHas('reading1', function($query)use($zone_id, $acct_num){
							$query->where('zone_id', $zone_id);

							if($acct_num != 'none'){
								//~ $query->where('account_number', $acct_num);
							}

						})
						->where('period', 'like', $period.'%')
						//~ ->where('billing_mdls.id','>=' ,$min_id)
						->where('accounts.route_id','>=' ,$min_id)
						->where('billing_total', '!=', 0)
						->with('reading1')
						->leftJoin('accounts', 'accounts.id', '=', 'billing_mdls.account_id')
						->selectRaw('billing_mdls.*, accounts.route_id')
						//->orderBy('accounts.bill_num_01', 'asc')
						->orderBy('accounts.route_id', 'asc');

			$bill001 = $bill002_raw->get();

			//~ echo '<pre>';
			//~ print_r($bill001->toArray());
			//~ die();

			return view('billings.inc.billing_billing.bill_printing_ajax', compact('bill001', 'bill_num', 'bill_num_end'));

			echo '<pre>';
			print_r($bill001->toArray());
	}//


	function disconnectionPrintStart_List__AAA($zone_id,$period,$acct_num)
	{
		$connector = new FilePrintConnector("/dev/usb/lp0");
		$printer = new Printer($connector);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text(add_print_Vspace(1));
		$printer->text("REPUBLIKA NG PILIPINAS");
		$printer->text(add_print_Vspace(1));
		$printer->close();


	}

	function disconnectionPrintStart_List($zone_id,$period,$acct_num)
	{
		$date1 = date('Y-m', strtotime($period));

		$func1 = function($query)use($date1){
					$query->where('period', 'like', $date1.'%');
					$query->whereDoesntHave('collection', function($qq2){
						$qq2->where('balance_payment','<=', 0);
					});
					$query->where('billing_total', '!=', 0);

				  };

		$func2 = function($query){
					$query->whereDoesntHave('collection');
				};


		$accnt = Accounts::whereHas('bill1', $func1)
				->where('zone_id', $zone_id)
				->with(['bill1' =>$func1])
				->get();

		echo $acct_num;
		echo '<pre>';
		print_r($accnt->toArray());
		die();

		$connector = new FilePrintConnector("/dev/usb/lp0");
		$printer = new Printer($connector);
		//~ $printer->cut();
		//~ $printer->feedForm();

		$vv = 0;
		$print=0;
		foreach($accnt as $acc)
		//for($x=0;$x<=10;$x++)
		{

			if($acc->acct_no ==  $acct_num){
				$print = 1;
			}


			if($print == 0){
				continue;
			}


			 $this->NoticePrinting101($printer, $acc);

			if($vv >= 1){
				$printer->cut();
				$printer->feedForm();
				$vv = 0;
				continue;
			}else{
				$printer->text(add_print_Vspace(3));
			}

			$vv++;
		}



		//~ $printer->text(add_print_Vspace(2));
		//~ $this->NoticePrinting101($printer);
		//~ $printer->cut();
		//~ $printer->feedForm();
		//~ $printer->text(add_print_Vspace(3));


		$printer->close();


	}


	function  NoticePrinting101($printer, $dd)
	{
		$str01 = '0123456789012345678901234567890123456789012345678901234567890123456789001234567890123456000000000000';
		$whole =  strlen($str01);
		$half   = $whole / 2;
		$quart  =  $half / 2;


		$ttt = function($printer, $given_text, $next=0){
			$str01 = '0123456789012345678901234567890123456789012345678901234567890123456789001234567890123456000000000000';
			$whole =  strlen($str01);
			$half   = $whole / 2;
			$quart  =  $half / 2;

			//$str_given = "REPUBLIKA NG PILIPINAS";
			$str_given = $given_text;

			$str_whole =  strlen($str_given);
			$str_half =   $str_whole / 2;

			$start_print = $quart - $str_half;

			$printer->text(add_print_Hspace($start_print));
			$printer->text($str_given);

			if($next == 0){
				$next_start =  ($half - $start_print) - ($str_half  - $start_print) ;
			}else{
				$next_start = $next;
			}
			$printer->text(add_print_Hspace($next_start));
			$printer->text($str_given);

			$printer->text(add_print_Vspace(1));
		};

		$ttt($printer, "REPUBLIKA NG PILIPINAS", 27);
		$ttt($printer, WD_NAME, 26);
		$ttt($printer, WD_ADDRESS, 24);
		$printer->text(add_print_Vspace(1));
		$ttt($printer, "Notice of Disconnection", 26);

		//"Febuary 20, 2019"
		$ddate_full = date('F d, Y');
		$printer->text(add_print_Hspace($half - 18));
		$printer->text($ddate_full);
		$printer->text(add_print_Hspace($half - 15));
		$printer->text($ddate_full);
		$printer->text(add_print_Vspace(1));

		$printer->text($dd->acct_no);
		$printer->text(add_print_Hspace(($half - 8)));
		$printer->text($dd->acct_no);
		$printer->text(add_print_Vspace(1));

		$str11 = "Mr./Mrs/Ms  ".$dd->fname.' '.$dd->lname;
		$str_len1 = strlen($str11);
		$printer->text($str11);
		$printer->text(add_print_Hspace(($half - ($str_len1)) + 2));
		$printer->text($str11);
		$printer->text(add_print_Vspace(1));

		$str12 = "Mr./Mrs/Ms  ";
		$str_len2 = strlen($str12);
		$addres = $dd->address1;
		$str_len3 = strlen($addres);
		$full_len  = $str_len3+$str_len2;

		$printer->text(add_print_Hspace($str_len2));
		$printer->text($addres);
		$printer->text(add_print_Hspace($str_len2));
		$printer->text(add_print_Hspace(($half - ($full_len)) + 2));
		$printer->text($addres);
		$printer->text(add_print_Vspace(2));

		$strp1 = "We would like to inform you that you have";
		$strp2 = "an overdue account with us amounting to";
		//~ $strp3 = "Php  231.00  corresponding supply of water ";
		$strp4 = "you have contracted with us.";

		$ttt($printer, $strp1, 10);
		$ttt($printer, $strp2, 10);

		$printer->text(add_print_Hspace(3));
		$printer->setUnderline(true);
		$printer->text('Php  '.number_format($dd->bill1->billing_total, 2));
		$printer->setUnderline(false);
		$printer->text('  corresponding supply of water ');
		$printer->text(add_print_Hspace(10));
		$printer->setUnderline(true);
		$printer->text('Php  '.number_format($dd->bill1->billing_total, 2));
		$printer->setUnderline(false);
		$printer->text('  corresponding supply of water ');
		$printer->text(add_print_Vspace(1));

		//$ttt($printer, $strp3, 10);
		$ttt($printer, $strp4, 20);
		$printer->text(add_print_Vspace(1));

		$str22_1 = 'We request that you settle this account';
		$str22_2 = 'on or before Tuesday, February 26, 2019';
		$str22_3 = 'anytime between 8:00 A.M. and 5:00 P.M. ';
		$str22_4 = 'Otherwise, we will be constrained much to';
		$str22_5 = 'our regret  to disconnect your water service';
		$str22_6 = 'without any further notice.';


		$ttt($printer, $str22_1, 10);

		$printer->text(add_print_Hspace(3));
		$printer->text('on or before ');
		$printer->setUnderline(true);
		$printer->text('Tuesday, February 26, 2019');
		$printer->setUnderline(false);
		$printer->text(add_print_Hspace(12));
		$printer->text('on or before ');
		$printer->setUnderline(true);
		$printer->text('Tuesday, February 26, 2019');
		$printer->setUnderline(false);
		$printer->text(add_print_Vspace(1));

		$ttt($printer, $str22_3, 10);
		//~ $printer->text(add_print_Vspace(1));
		$ttt($printer, $str22_4, 10);
		$ttt($printer, $str22_5, 7);
		$ttt($printer, $str22_6, 23);
		$printer->text(add_print_Vspace(1));

		$str22_1 = 'A reconnecton fee of Php 50.00 is also required';
		$str22_2 = 'for disconnection services prior to reconnection';

		$printer->text(add_print_Hspace(1));
		$printer->text('A reconnecton fee of ');
		$printer->setUnderline(true);
		$printer->text('Php 50.00');
		$printer->setUnderline(false);
		$printer->text(' is also required');
		$printer->text(add_print_Hspace(5));
		$printer->text('A reconnecton fee of ');
		$printer->setUnderline(true);
		$printer->text('Php 50.00');
		$printer->setUnderline(false);
		$printer->text(' is also required');
		$printer->text(add_print_Vspace(1));

		$ttt($printer, $str22_2, 5);

		$str22_1 = 'If payment has made, please disregard this notice';
		$str22_2 = 'and accept our thanks.';

		$ttt($printer, $str22_1, 5);
		//~ $ttt($printer, $str22_2, 5);
		$printer->text($str22_2);
		$printer->text(add_print_Hspace($half - 21));
		$printer->text($str22_2);


		$printer->text(add_print_Vspace(1));

		$str22_1 = 'Sincerly, ';
		//~ $str22_2 = '___________________________';
		//~ $str22_3 = 'General Manager D';
		$str22_2 = WD_MANAGER;
		$str22_3 = WD_MANAGER_RA;

		$printer->text(add_print_Hspace($quart - 5));
		$printer->text($str22_1);
		$printer->text(add_print_Hspace(44));
		$printer->text($str22_1);
		$printer->text(add_print_Vspace(1));

		$printer->text(add_print_Hspace($quart - 5));
		$printer->text($str22_2);
		$printer->text(add_print_Hspace(27));
		$printer->text($str22_2);
		$printer->text(add_print_Vspace(1));

		$printer->text(add_print_Hspace($quart ));
		$printer->text($str22_3);
		$printer->text(add_print_Hspace(35));
		$printer->text($str22_3);
		$printer->text(add_print_Vspace(1));

	}

	//bill_print_start_002
	function  billPrintStart_Save_ajax($period, $zone_id, $acct_num, $bill_num,  $bill_num_end, Request $request)
	{


		$res1 = $this->before_printing_save_first($period, $zone_id, $acct_num, $bill_num, $bill_num_end, 'get_data_1');

		//~ $bill002 = @$res1->toArray();
		$new_cont1 = array();

		$bn0 = $bill_num;
		foreach($res1 as $r){
			$curr_bill = @$r->bill_num_01;
			if($bn0 > $curr_bill){continue;}

			$new_cont1[] = $r->toArray();

			if((int) $bill_num_end  != 0){
				if($curr_bill >= $bill_num_end){
					break;
				}
			}

		}//


		echo json_encode($new_cont1);
		exit;

		return;
		return;
		return;
		return;
		return;
		//~ echo $zone_id;
		//~ die();

		$limit001 = 10000;

		if((int) $bill_num_end == $bill_num ){
			$limit001 = 1;
		}elseif((int)$bill_num_end > $bill_num){
			$limit001 =  ((int)$bill_num_end - (int)$bill_num) + 1;
		}


		$min_id = 0;

		$bill001_raw = BillingMdl::
						whereHas('reading1', function($query)use($zone_id, $acct_num){
							$query->where('zone_id', $zone_id);
							if($acct_num != 'none'){
								$query->where('account_number', $acct_num);
							}
						})
						->where('period', 'like', $period.'%')
						->where('billing_total', '!=', 0)
						->with('reading1')
						->with('account')
						->with('ledger_data')
						->leftJoin('accounts as AA', 'AA.id', '=', 'billing_mdls.account_id')
						->leftJoin('account_metas AS AM','AM.id','=','AA.acct_type_key')
						->selectRaw('billing_mdls.*, AA.route_id, AM.meta_name')
						->orderBy('AA.route_id', 'asc');

			$bill001 = $bill001_raw->limit(1)->get();

			//~ $min_id = $bill001[0]->id;
			$min_id = $bill001[0]->account->route_id;

			//~ echo $min_id;
			//~ die();

			//~ echo '<pre>';
			//~ print_r($bill001->toArray());
			//~ die();


			$acct_nu = $bill001[0]->reading1->account_number;


			$bill002_raw = BillingMdl::
						whereHas('reading1', function($query)use($zone_id, $acct_num){
							$query->where('zone_id', $zone_id);
						})
						->where('billing_mdls.period', 'like', $period.'%')
						//~ ->where('billing_mdls.id', '>=', $min_id)
						->where('AA.route_id','>=' ,$min_id)
						->where('billing_mdls.billing_total', '!=', 0)
						->with('reading1')
						->with('account')
						->with('ledger_data')
						->leftJoin('accounts as AA', 'AA.id', '=', 'billing_mdls.account_id')
						->leftJoin('account_metas AS AM','AM.id','=','AA.acct_type_key')
						->selectRaw('billing_mdls.*, AA.route_id, AM.meta_name')
						->orderBy('AA.route_id', 'asc');

			//~ $bill002_raw->select('*');

			$bill002 = $bill002_raw
						->limit($limit001)
						->get()
						->toArray();


			//~ ->leftJoin('accounts', 'accounts.id', '=', 'billing_mdls.account_id')
			//~ ->selectRaw('billing_mdls.*, accounts.route_id')
			//~ ->orderBy('accounts.route_id', 'asc');


			//~ foreach($bill002 as $mm)
			//~ {
				//~ echo $mm['account']['fname'].' '.$mm['account']['lname'];
				//~ echo '<br />';
			//~ }
			//~ die();


			echo json_encode($bill002);
			exit;

			echo '<pre>';
			print_r($bill002);
			echo $limit001;

	}//


	function  billPrintStart_Save($period, $zone_id, $acct_num, $bill_num, Request $request)
	{
			$bill001_raw = BillingMdl::
						whereHas('reading1', function($query)use($zone_id, $acct_num){
							$query->where('zone_id', $zone_id);
							if($acct_num != 'none'){
								$query->where('account_number', $acct_num);
							}
						})
						->where('period', 'like', $period.'%')
						->where('billing_total', '!=', 0)

						->with('reading1')

						->with('account');

			$bill001 = $bill001_raw->limit(1)->get();

			$min_id = $bill001[0]->id;
			$acct_nu = $bill001[0]->reading1->account_number;

			//~ $new_serv = new PrintServ;
			//~ $new_serv->zone_id = trim($zone_id);
			//~ $new_serv->bill_id = trim($min_id);
			//~ $new_serv->bill_start = trim($bill_num);
			//~ $new_serv->acct_start = trim($acct_nu);
			//~ $new_serv->period = $period.'-01';
			//~ $new_serv->status = 'active';
			//~ $new_serv->save();

			$bill002_raw = BillingMdl::
						whereHas('reading1', function($query)use($zone_id, $acct_num){
							$query->where('zone_id', $zone_id);
						})
						->where('period', 'like', $period.'%')
						->where('id', '>=', $min_id)
						->where('billing_total', '!=', 0)
						->with('reading1')
						->with('account')
						->with('ledger_data')
						->leftJoin('accounts as AA', 'AA.id', '=', 'billing_mdls.account_id')
						->leftJoin('account_metas AS AM','AM.id','=','AA.acct_type_key')
						->selectRaw('billing_mdls.*, AA.route_id, AM.meta_name')
						->orderBy('AA.route_id', 'asc');



			//$bill002 = $bill002_raw->get();
			$bill002 = $bill002_raw->get()->toArray();

			echo '<pre>';
			echo json_encode($bill002);
			//~ print_r($bill002->toArray());
			die();

			$connector = new FilePrintConnector("/dev/usb/lp0");
			$printer = new Printer($connector);
			$printer->initialize();

			foreach($bill002 as $bb)
			{
				$printer->text(add_print_Vspace(7));
				$this->PrintBill($bb, $period.'-01', $printer);
				$printer->text(add_print_Vspace(8));
			}

			$printer->close();


			//~ $connector = new FilePrintConnector("/dev/usb/lp0");



			$request->session()->flash('success', 'Billing service updated');
			return Redirect::to(URL::previous() . "#bill_printing");
			//~ echo $min_id;
			//~ echo '<pre>';
			//~ print_r($bill001->toArray());
	}//

	function billPrintStopService($serv_id, Request $request)
	{
		$nn = PrintServ::find($serv_id);

		if(!$nn){
			$request->session()->flash('success', 'Billing service updated');
			return Redirect::to(URL::previous() . "#bill_printing");
		}

		$nn->status = 'stopped';
		$nn->save();

		$request->session()->flash('success', 'Printing Stop.');
		return Redirect::to(URL::previous() . "#bill_printing");
	}


	function PrintBill($rr1, $period1, $printer)
	{

		//~ echo '<pre>';
		//~ print_r($rr1->toArray());
		//~ die();

		@$next_moth = date('m/16/y', strtotime($period1+'-01 + 1 Month'));

		$full_name = $rr1->account->lname.', '.
				   $rr1->account->fname.' '.
				   $rr1->account->mi;

		$date_period = date('m/1', strtotime($period1))." ".
		 		     date('m/28', strtotime($period1));

		$read_ifo = explode('||', $rr1->read_PC);

		/**/
		/**/
		/**/
		$half_len = 40;

		//~ $acct_num = '0411206712';
		//~ $acct_name = 'NOEL GREGOR O. ILACO';

		//~ $meter_number = '000170382116';
		//~ $address = 'Tambakan';
		//~ $period_coverd = "08/04 09/04";
		//~ $prev_read = '113';
		//~ $curr_read  = '113';
		//~ $consump   = '0';

		//~ $bill_amount = '1,500.00';
		//~ $penalty = '150.00';
		//~ $arrears  = '100.00';
		//~ $other_charge = '0.00';
		//~ $due_before = '1,500.00';
		//~ $due_after = '1,650.00';
		//~ $due_after = '1,750.00';
		//~ $total_payment = '1,750.00';


		$acct_num = $rr1->account->acct_no;
		$acct_name = strtoupper($full_name);

		$meter_number = $rr1->account->meter_number1;
		$address = $rr1->account->address1;
		$period_coverd = $date_period;
		$prev_read = $read_ifo[0];
		$curr_read  = $read_ifo[1];
		$consump   = (int) $curr_read  -  (int) $prev_read;

		$ttl_bill = $rr1->billing_total - $rr1->discount;

		$bill_amount = number_format($rr1->billing_total, 2);
		$penalty = number_format($penal=($ttl_bill * 0.1), 2);
		$arrears  = number_format($rr1->arrears, 2);
		$discount  = number_format($rr1->discount, 2);

		$other_charge = '0.00';
		$due_before = number_format($ttl_bill + $rr1->arrears , 2);
		$due_after = number_format(($ttl_bill + $penal + $rr1->arrears), 2);
		//$total_payment = number_format(($rr1->billing_total + $penal), 2);
		$total_payment = '';
		$line1='';




		//BILL HWD COPY START
		$line1.=$acct_num;
		$line1.=add_print_Hspace(2);
		$line1.=$acct_name;
		//BILL HWD COPY END

		//BILL CUSTOMER COPY START
		$total_str=strlen($line1);
		$remain1=$half_len - $total_str;

		$line1.=add_print_Hspace($remain1);

		$line1.=$acct_num;
		$line1.=add_print_Hspace(2);
		$line1.=$acct_name;
		//BILL CUSTOMER COPY END

		//BILL HWD COPY START
		$line2='';
		$line2.=$meter_number;
		$line2.=add_print_Hspace(2);
		$line2.=$address;
		//BILL HWD COPY END


		//BILL CUSTOMER COPY START
		$total_str=strlen($line2);
		$remain1=$half_len - $total_str;
		$line2.=add_print_Hspace($remain1);

		$line2.=$meter_number;
		$line2.=add_print_Hspace(2);
		$line2.=$address;
		//BILL CUSTOMER COPY END


		//BILL HWD COPY START
		$line3='';
		$line3.=$period_coverd;
		$line3.=add_print_Hspace(3);
		$line3.=$prev_read;
		$line3.=add_print_Hspace(5);
		$line3.=$curr_read;
		$line3.=add_print_Hspace(6);
		$line3.=$consump;
		//BILL HWD COPY END

		$pre1 =  $line3;
		//BILL CUSTOMER COPY START
		$total_str=strlen($line3);
		$remain1=$half_len - $total_str;
		$line3.=add_print_Hspace($remain1);
		$line3.=$pre1;
		//BILL CUSTOMER COPY START

		$long_spac = 29;

		//HWD Copy
		$line4='';
		$line4.=add_print_Hspace($long_spac);
		$line4.=$bill_amount;

		//Pre
		$pre1 =  $line4;
		$total_str=strlen($line4);
		$remain1=$half_len - $total_str;
		$line4.=add_print_Hspace($remain1);

		//Customer Copy
		$line4.=$pre1;

		///////////
		///////////
		///////////

		//HWD Copy
		$line5='';
		$line5.=add_print_Hspace($long_spac);
		$line5.=$penalty;
		//Pre
		$pre1 =  $line5;
		$total_str=strlen($line5);
		$remain1=$half_len - $total_str;
		$line5.=add_print_Hspace($remain1);
		//Customer Copy
		$line5.=$pre1;


		//HWD Copy
		$line5_2='';
		$line5_2.=add_print_Hspace($long_spac);
		$line5_2.=$discount;
		//Pre
		$pre1 =  $line5_2;
		$total_str=strlen($line5_2);
		$remain1=$half_len - $total_str;
		$line5_2.=add_print_Hspace($remain1);
		//Customer Copy
		$line5_2.=$pre1;

		///////////
		///////////
		///////////

		//HWD Copy
		$line6='';
		$line6.=add_print_Hspace($long_spac);
		$line6.=$arrears;

		//Pre
		$pre1 =  $line6;
		$total_str=strlen($line6);
		$remain1=$half_len - $total_str;
		$line6.=add_print_Hspace($remain1);

		//Customer Copy
		$line6.=$pre1;

		///////////
		///////////
		///////////

		//HWD Copy
		$line7='';
		$line7.=add_print_Hspace($long_spac);
		$line7.=$other_charge;

		//Pre
		$pre1 =  $line7;
		$total_str=strlen($line7);
		$remain1=$half_len - $total_str;
		$line7.=add_print_Hspace($remain1);

		//Customer Copy
		$line7.=$pre1;

		///////////
		///////////
		///////////

		//HWD Copy
		$line8='';
		$line8.=add_print_Hspace($long_spac - 15);
		$line8.=$next_moth;
		$line8.=add_print_Hspace(7);
		$line8.=$due_before;

		//Pre
		$pre1 =  $line8;
		$total_str=strlen($line8);
		$remain1=$half_len - $total_str;
		$line8.=add_print_Hspace($remain1);

		//Customer Copy
		$line8.=$pre1;

		///////////
		///////////
		///////////

		//HWD Copy
		$line9='';
		$line9.=add_print_Hspace($long_spac-15);
		$line9.=$next_moth;
		$line9.=add_print_Hspace(7);
		$line9.=$due_after;

		//Pre
		$pre1 =  $line9;
		$total_str=strlen($line9);
		$remain1=$half_len - $total_str;
		$line9.=add_print_Hspace($remain1);

		//Customer Copy
		$line9.=$pre1;

		///////////
		///////////
		///////////

		//HWD Copy
		$line10='';
		$line10.=add_print_Hspace($long_spac);
		$line10.=$total_payment;

		//Pre
		$pre1 =  $line10;
		$total_str=strlen($line10);
		$remain1=$half_len - $total_str;
		$line10.=add_print_Hspace($remain1);

		//Customer Copy
		$line10.=$pre1;

		///////////
		///////////
		///////////
		//$printer->initialize();

		//~ $connector = new FilePrintConnector("/dev/usb/lp0");
		//~ $printer = new Printer($connector);
		//~ $printer->initialize();

		//$baf= $printer->getPrintBuffer();
		//echo '<pre>';
		//echo @count($baf);
		//echo '</pre>';

		$printer->text($line1);
		$printer->text(add_print_Vspace(3));
		$printer->text($line2);
		$printer->text(add_print_Vspace(4));
		$printer->text($line3);
		$printer->text(add_print_Vspace(3));
		$printer->text($line4);
		$printer->text(add_print_Vspace(1));
		$printer->text($line5);
		$printer->text(add_print_Vspace(1));
		$printer->text($line5_2);
		$printer->text(add_print_Vspace(1));
		$printer->text($line6);
		$printer->text(add_print_Vspace(1));
		$printer->text($line7);
		$printer->text(add_print_Vspace(1));
		$printer->text($line8);
		$printer->text(add_print_Vspace(1));
		$printer->text($line9);
		$printer->text(add_print_Vspace(1));
		$printer->text($line10);
		$printer->text(add_print_Vspace(1));

		//~ $printer->close();


	}//






}
