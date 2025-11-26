<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

use App\AccountMetas;
use App\Accounts;
use App\Zones;
use App\HwdRequests;
use App\Reading;
use App\HwdOfficials;
use App\BillingMdl;
use App\BillingMeta;
use App\BillingRateVersion;
use App\BillingDue;
use App\HwdJob;
use App\Http\Controllers\BillingCtrl;
use App\Reports;
use App\BillPrint;
use App\Collection;
use App\Arrear;
use App\ReadingPeriod;
use App\ServiceBillZone;
use App\OverdueStat;
use App\LedgerData;
use App\BillingNw;


use App\Services\Collections\CollectionService;




use App\Http\Controllers\HwdLedgerCtrl;
use App\Http\Controllers\LedgerCtrl;


use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;


class ServiceCtrl extends Controller
{


	function billingGenerate()
	{
		$req1 = HwdRequests::where('req_type', 'generate_billing_period_request')
					->where('status', 'admin_approved')
					->orderBy('id', 'desc')
					->limit(10)
					->get()
					->toArray();

		if(empty($req1)){return;}

		$data1 = $this->___billing_generate_data1();
		extract($data1);


		foreach($req1 as $rr):

			extract((array) json_decode($rr['other_datas']));
			$period1 = date('Y-m', strtotime($period_year.'-'.$period_month));

			$billing_count = BillingMdl::whereNull('billing_total')
						->where('period','like', $period1.'%')->count();

			if($billing_count <= 0){
				continue;
			}


			$bill1 = BillingMdl::whereNull('billing_total')
						->where('period','like', $period1.'%')
						->with('reading1.account1')
						->with('reading1')
						->limit(20)
						->get()
						->toArray();


			foreach($bill1 as $kk=>$vv)
			{

					$prev_period = date('Y-m', strtotime($period1.' -1 month'));
					$prev_reading2 = Reading::where('period', 'like', $prev_period.'%')
									->where('account_id',  $vv['reading1']['account_id'])
									->first();

					if($prev_reading2){
						$bill1[$kk]['reading1']['prev_reading'] = $prev_reading2->curr_reading;
					}

					if(!empty($vv['reading1']['init_reading'])){
						$bill1[$kk]['reading1']['prev_reading'] = $vv['reading1']['init_reading'];
					}

					extract($bill1[$kk]['reading1']);

					$billing_total = 0;
					$less_desc  = 0;

					$consump1 = (int) $curr_reading - (int) $prev_reading;
					$rate_set = $rate_info[$account1['acct_type_key']];

					$billing_total = $this->__calculate_billing_rates($consump1, $rate_set);

					if(!empty($discount_type[$account1['acct_discount']])){
						$my_discount  = $discount_type[$account1['acct_discount']];
						if(!empty($my_discount)){
							$less_desc =  $billing_total * ($my_discount['meta_value'] / 100)  ;
						}
					}

					$bill_update = BillingMdl::find($vv['id']);
					$bill_update->billing_total = $billing_total - $less_desc;
					$bill_update->bill_date  = date('Y-m-d');
					$bill_update->save();

			}
			
		endforeach;


	}//END METHOD
	
	function BillingPrintingServicePrepare()
	{
			$this->BillingPrintingService();
	}//
	
	function BillingPrintingServiceExecute()
	{
		$for_printing = BillPrint::where('stat1', 'pending')
				->orderBy('zone_id', 'asc')
				->orderBy('bill_id', 'asc')
				->limit(2)
				->get();
				
		foreach($for_printing as $fpp)
		{
			
			if(file_exists($fpp->bill_path))
			{
				$fp1 = fopen($fpp->bill_path, 'r');
				$fp2 = fopen('/dev/usb/lp0', 'w');
				
				while(!feof($fp1))
				{
					$line = fgets($fp1);
					$wrt1 = fwrite($fp2, $line);
					
					if(((int) $wrt1) >= 20)
					{
						//~ usleep(500000);
					}
					
					//~ echo fwrite_stream($fp2, $line);
					//~ echo '<br />';
				}
				
				fclose($fp2);
				
				fclose($fp1);

			}
			
		}
		
		echo '<pre>';
		print_r($for_printing->toArray());
	}//
	
	
	function BillingPrintingService()
	{
		$is_test = 1;
		
		
		ini_set('max_execution_time', 3600); //30 minutes
		
		$billing_service = HwdRequests::where('req_type', 'billing_print_request')
				->where('status', 'started')
				->first();
		
		
		
		if(!$billing_service)
		{
			echo 'No Printing serivice';
			return;
		}
		
		//
		//
		$bills_raw = BillingMdl::where('period', $billing_service->dkey1);
		
		if($is_test == 0)
		{
			$bills_raw->whereDoesntHave('print1');
		}
		
		$bills = $bills_raw->with('account')
						->limit(4)
						->get();
		//
		//
			
		$zones_tmp = Zones::where('status', '!=', 'deleted')->get();
		$zones = array();
		foreach($zones_tmp as $zt)
		{
			$zones[$zt->id] = strtolower(str_replace(' ', '',$zt->zone_name));
		}

		if($bills->count() == 0)
		{
			
			if($is_test == 0)
			{
				$billing_service->status = 'ended';
				$billing_service->save();
			}
			
			echo 'Service ended';
			return;
		}
		
		
		
		
		
		$feedStatus = 1;
		foreach($bills as $bb)
		{
			
			//~ if (!is_dir($myDir)) {
				//~ mkdir($myDir, 0777, true); // true for recursive create
			//~ }
			$pp = str_replace('-','_', $bb->period);
			$dir1 = 'billing_print/'.$pp.'/'.$zones[$bb->account->zone_id].'/'; 
			
			if (!is_dir($dir1)) {
				mkdir($dir1, 0777, true); // true for recursive create
			}
			
			$file1 = $dir1.$bb->id.'.txt';
						
			//$bb->account->zone_id
			 //~ echo $file1;
			 //~ echo '<br />';
			//~ echo '<pre>';
			//~ print_r($bb->toArray());
			//~ echo '</pre>';
			//~ die();
			
			if($is_test == 0)
			{
				$connector = new FilePrintConnector($file1);
			}
			else
			{
				$connector = new FilePrintConnector("/dev/usb/lp0");
			}			
			
			$printer = new Printer($connector);			
			$printer->initialize();
			$printer->text(add_print_Vspace(7));
			$this->PrintBill($bb, $bb->period, $printer);
			$printer->close();		
			
			if($is_test == 0)                                        
			{
				$bill_print = new  BillPrint;
				$bill_print->bill_id = $bb->id;
				$bill_print->stat1 = 'pending';
				$bill_print->period = $bb->period;
				$bill_print->zone_id = $bb->account->zone_id;
				$bill_print->bill_path = $file1;
				$bill_print->save();
			}
			else
			{
				break;
			}
			
		}//endfor
		
		//$printer->feedForm();
		//$printer->text("\n\n");
		//$printer->text("\n\n");

		
		echo 'inprogress..';
	}//
	
	function PrintBill($rr1, $period1, $printer)
	{
		
		//~ echo '<pre>';
		//~ print_r($rr1->toArray());
		//~ die();
		
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
		$consump   = '0';

		$bill_amount = number_format($rr1->billing_total, 2);
		$penalty = number_format($penal=($rr1->billing_total * 0.1), 2);
		$arrears  = number_format($rr1->arrears, 2);
		$other_charge = '0.00';
		$due_before = number_format($rr1->billing_total, 2); 
		$due_after = number_format(($rr1->billing_total + $penal), 2); 
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
		$line8.=add_print_Hspace($long_spac);
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
		$line9.=add_print_Hspace($long_spac);
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

	function BillingPrinting1_ORIG()
	{

		return;
		return;
		return;
		return;
		//set_time_limit(0);
		ini_set('max_execution_time', 1800); //30 minutes

		$period1 = '2018-08-28';
		$res1 = BillingMdl::where('period',$period1)
				->whereDoesntHave('print1', function($query)use($period1){
				     $query->where('period', $period1);
				 })
				 ->with('account')
				 ->limit(2)
				 ->get();

		$rr1 = $res1[0];

		$full_name = $rr1->account->lname.', '.
				   $rr1->account->fname.' '.
				   $rr1->account->mi;

		$date_period = date('m/1', strtotime($period1))." ".
		 		     date('m/28', strtotime($period1));

		$read_ifo = explode('||', $rr1->read_PC);
		$connector = new FilePrintConnector("/dev/usb/lp0");
 		$printer = new Printer($connector);
 		$printer->initialize();
		$printer->text(Printer::FF);
		$printer->text(substr($rr1->account->acct_no, 0, 6)."   ");
		$printer->text($full_name);
		$printer->text("\n\n");
		$printer->text(substr($rr1->account->meter_number1,0,6)."   ");
		$printer->text($rr1->account->address1);
		$printer->text("\n\n\n");
		$printer->text($date_period."   ");
		$printer->text($read_ifo[0]."  ");
		$printer->text($read_ifo[1]."  ");
		$printer->text($rr1->consumption."\n\n");
		$printer->text("DUE DATE  ".date('m/d/Y', strtotime($period1+' + 20 days')));
		$printer->text("\n\n");
		$printer->text(number_format($rr1->billing_total, 2)."  \n");
		$printer->text(number_format(($rr1->billing_total * 0.1), 2)."  \n");
		$printer->text("\n\n");
		$printer->text(number_format($rr1->billing_total, 2)."  \n");
		$printer->text(number_format(($rr1->billing_total * 1.1), 2)."  \n");
		$printer->close();

		// foreach($res1 as $rr1)
		// {
		// 	$print_me = new BillPrint;
		// 	$print_me->bill_id = $rr1->id;
		// 	$print_me->period = $period1;
		// 	$print_me->stat1 = 'printed';
		// 	$print_me->save();
		//
		// 	$printer->text("\n\n");
		// 	$printer->text("Account # : ".$rr1->id."\n");
		// 	$printer->feedForm();
		// 	$printer->cut();
		// }
		//
		// $printer->close();

		echo '<pre>';
		// $rr1->account->acct_no;
		// $rr1->id;
		// $rr1->account->meter_number1;
		// $rr1->account->address1;
		// $rr1->account->lname.', '.$rr1->account->fname.' '.$rr1->account->mi;
		// echo date('M 1', strtotime($period1)).' To '.
 		// date('M 28', strtotime($period1));
		// $read_ifo = explode('||', $rr1->read_PC);
		// $rr1->consumption
		// print_r($read_ifo);

		print_r($res1->toArray());

	}//
	
	function generateBillingByEach()
	{
		
	}

	function generateBilling001()
	{
		
		
		die();
		die();
		die();
		die();
		
		//~ echo 'sadfsdfs';
		//~ die();
		
		$hwd_req1 = HwdRequests::where('req_type', 'generate_billing_period_request')
			->where('status', 'ongoing')
			->orderBy('dkey1', 'desc')
			->first();		
			
		if(!$hwd_req1)
		{echo 'Completed 1';return;}
		

		
		$curr_perdiod = date('Y-m', strtotime($hwd_req1->dkey1));
		
		$sbz1 = ServiceBillZone::where('status', 'ONPROGRESS')
							->where('bill_period_id', $hwd_req1->id)
							->first();
							
									
		if(!$sbz1){
			$hwd_req1->status = 'completed';
			$hwd_req1->save();
			echo 'Request Completed!';return;
		}
		
		$reading1 = Reading::where('period','like', $curr_perdiod.'%')
			->whereNotNull('curr_reading')
			->where('curr_reading', '!=', '')
			->where(function($query){
				$query->whereNull('bill_stat');
				$query->orWhere('bill_stat', 'unbilled');
			})
			//~ ->where('account_number','111207697')
			->where('zone_id', $sbz1->zone_id)
			->limit(100)
			->get();
			
		if(empty($reading1->toArray()))
		{
			$sbz1->status = 'COMPLETED';
			$sbz1->save();
			
			echo 'ZONE COMPLETED!';
			return;
		}
		
							
		//~ echo $curr_perdiod;
		//~ die();			
		
		
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
		//Last month na period


		//~ echo '<pre>';
		//~ print_r($reading1->toArray());
		//~ die();
		//~ echo $curr_perdiod;
		//~ die();

		//Isa isahin ang reading
		foreach($reading1 as $rr)
		{
              ServiceCtrl::proccess_arrears($rr, $curr_perdiod);
              ServiceCtrl::proccess_billing($rr, $curr_perdiod, $data11);
		}//endforeach


		echo 'Completed 33';		
		
		
		//~ $sbz1->zone_id;
		//~ echo '<pre>';
		//~ print_r($sbz1->toArray());
		//~ echo 'TTTT';
		
		
	}

     static function get_arrear($acct_id, $period)
     {
          $total_arear = Arrear::where('acct_id', $acct_id)
               ->where('period','like', $period.'%')
               ->orderBy('id','desc')
               ->first();
               
          if($total_arear)
          {
               return $total_arear->amount;
          }else{
               return 0;
          }

     }//End

     static function proccess_billing($rr, $curr_perdiod, $dd, $data_only=false)
     {
		 
		 global $extra_msg;
		 
		  $curr_perdiod = date('Y-m',strtotime($curr_perdiod));
		  $full_period  = $curr_perdiod.'-01';
		  
          extract($dd);

          $rate_version = BillingRateVersion::orderBy('id', 'desc')->first();

		  
			// ee($rr->toArray(), __FILE__, __LINE__);

		//   echo '<pre>';
		//   print_r($rate_version->toArray());
		//   die();

          $rr->account1;
          $rr->billing;


          $curr = (int) $rr->curr_reading;
          $prev = (int) $rr->prev_reading;
          $consump = $curr - $prev;
		 
		 
		 if(empty($rr->account1->acct_type_key)){
			 $rr->account1->acct_type_key = RES_ID;//Define in helper
		 }
		 
          $total_billing = BillingCtrl::__calculate_billing_rates($consump, $rate_info[$rr->account1->acct_type_key], $rr->account1->meter_size_id);

		//   echo $consump;
		//   echo '<pre>';	
		//   echo $rr->account1->acct_type_key;
		//   echo '<br />';
		//   echo $total_billing;
		//   echo '<br />';
		//   print_r($rate_info[$rr->account1->acct_type_key]);
		//   die();
		  
		  //~ $gov_type = array(10,11,12,13);
		  //~ if(in_array($rr->account1->acct_type_key, $gov_type))
		  //~ {
			  //~ $rr->account1->acct_discount = null;
		  //~ }

          $acct_discount_id = (int) @$rr->account1->acct_discount;
          $dis001 = @$discount_type[$acct_discount_id];

          $total_discount = 0;
          $total_all = 0;

          if(!empty($dis001))
          {
               $total_discount =  round($total_billing * ($dis001['meta_value'] / 100), 2);
          }
          
          if($consump>30)
          {
			  $total_discount = 0;
		  }


          $total_all = $total_billing  - $total_discount;

          $account_type_name = $acct_type[$rr->account1->acct_type_key]['meta_name'];

          $new_bill = true;

          $current_arrear = ServiceCtrl::get_arrear($rr->account_id,   $curr_perdiod);
          


		  $reading_period = ReadingPeriod::where('period', $full_period)
								->first();
		  
		  
		  $zone_read_date = $curr_perdiod.'-'.$rr->zone_id;
		  
		  $fine_date  =  null;
		  
		  if($reading_period)
		  {
			 $rd1 = json_decode($reading_period->read_dates, TRUE);
			 $zone_read_date = $rd1[$rr->zone_id];

			 $fine_dates = json_decode($reading_period->fine_dates, TRUE);
			 $fine_date = $fine_dates[$rr->zone_id];
			 
		  }//
		  
		  //~ echo '<pre>';
		  //~ print_r($reading_period->toArray());
		  //~ die();
		  
				          
          //~ echo '<pre>';
          //~ echo $current_arrear;
          //~ echo '<br />';
          //~ echo $total_billing;
          //~ echo '<br />';
          //~ die();

          $info = array(
               @$curr_perdiod,//0
               @$rr->prev_reading,//1
               @$rr->curr_reading,//2
               @$consump,//3
               $account_type_name,//4
               $total_all,//5,
               $dis001,//6
               $total_discount, //7,
               $total_billing, // 8
               $new_bill, //9
               $current_arrear // 10
          );
         
         //~ echo '<pre>';
         //~ print_r($rr->toArray());
         //~ echo $total_discount;
 		 //~ die();
 		 
 		 if($data_only == true){
			return $info;
			die();
		 }


          $bill_info = null;
          
          $bill_id = 0;
          $read_id = $rr->id;

          if($rr->billing)
          {
			
			$prev_read  = $rr->prev_reading;
			$curr_read  = $rr->curr_reading;
			
			 if(!empty($rr->init_reading))
			 {
				 $prev_read = $rr->init_reading;
			 }
			  
          	//$rr->billing->billing_total = $total_billing  - $total_discount;
          	$rr->billing->billing_total = $total_billing;
          	$rr->billing->curr_bill = $total_billing;
          	$rr->billing->due_stat = null;
          	$rr->billing->consumption = (int) @$consump;
            $rr->billing->read_PC = $prev_read.'||'.$curr_read;
			$rr->billing->arrears = $current_arrear;
			$rr->billing->discount = $total_discount;
			$rr->billing->penalty = 0;
			$rr->billing->due_stat = null;
          	$rr->billing->bill_date	 = @$zone_read_date;
          	$rr->billing->penalty_date	 = @$fine_date;
          	$rr->billing->acct_type	= $rr->account1->acct_type_key;
          	$rr->billing->meter_size_id	= $rr->account1->meter_size_id;
          	$rr->billing->save();

          	//$bill_info = $rr->billing->toArray();
          	$bill_info = $rr->billing;
          	$info[9] = false;
          	$bill_id = $rr->billing->id;
          }
          else
          {
          	$new_billing = new BillingMdl;
          	$new_billing->period = $curr_perdiod.'-01';
          	$new_billing->reading_id = $rr->id;
          	$new_billing->rate_id	 = $rate_version->id;
          	$new_billing->status	 = 'active';
          	//~ $new_billing->bill_date	 = date('Y-m-d');
          	$new_billing->bill_date	 = @$zone_read_date;
          	$new_billing->prep_by	 = 1;
          	$new_billing->billing_total = $total_billing;
          	//$new_billing->billing_total = $total_billing  - $total_discount;
          	$new_billing->curr_bill = $total_billing;
          	$new_billing->account_id = $rr->account_id;
          	$new_billing->consumption = (int) @$consump;
          	
			$prev_read  = $rr->prev_reading;
			$curr_read  = $rr->curr_reading;
			
			 if(!empty($rr->init_reading))
			 {
				 $prev_read = $rr->init_reading;
			 }
          	
          	//$new_billing->read_PC = $rr->prev_reading.'||'.$rr->curr_reading;
            $new_billing->read_PC = $prev_read.'||'.$curr_read;          	
			$new_billing->arrears = $current_arrear;
			$new_billing->discount = $total_discount;
			$new_billing->penalty_date = $fine_date;
          	$new_billing->acct_type	= $rr->account1->acct_type_key;
          	$new_billing->meter_size_id	= $rr->account1->meter_size_id;
          	$new_billing->save();
          	
          	
          	$bill_id = $new_billing->id;
          	

          	//$bill_info = $new_billing->toArray();
          	$bill_info = $new_billing;
          }//
          

			
          //$new_ledger_data = new LedgerData;
          
			 $ledger1 = LedgerData::
					where('period', 'like', $curr_perdiod.'%')
					->where('acct_id', $rr->account1->id)
					->where('led_type','billing')
					->where('status', 'active')
					//~ ->orderBy('date01', 'desc')
					->orderBy('zort1', 'desc')
					->orderBy('id', 'desc')
					->first(); 
			
			 //~ echo '<pre>';
			 //~ print_r($ledger1->toArray());
			 //~ die();	
					         

			 $ledger2 = LedgerData::
					where('acct_id', $rr->account1->id)
					->where('status', 'active')
					//~ ->orderBy('date01', 'desc')
					->orderBy('zort1', 'desc')
					->orderBy('id', 'desc')
					->first();               
          
          if($ledger1){
			  //Do Nothing
			  
				$ledger1->billing =$total_billing;
				$ledger1->discount = $total_discount;
				$ledger1->reading =$curr_read;
				$ledger1->consump =(int) @$consump;
				$ledger1->save();
			  
		  }else{
			
			$ttl_bal = 0;
			
			if($ledger2){
				$ttl_bal = $ledger2->ttl_bal;
			}
			
			$ttl_bal+=$total_billing;
			$ttl_bal-=$total_discount;
			  
			  
			$new_ledger_data = new LedgerData;
			$new_ledger_data->led_type ='billing';
			$new_ledger_data->acct_id = $rr->account_id;
			$new_ledger_data->bill_id = $bill_id;
			$new_ledger_data->read_id = $rr->id;
			$new_ledger_data->billing = $total_billing;
			$new_ledger_data->discount = $total_discount;
			$new_ledger_data->ttl_bal =$ttl_bal;
			$new_ledger_data->reading =$curr_read;
			$new_ledger_data->consump = (int) @$consump;
			$new_ledger_data->ledger_info = 'Billing '.date('F Y', strtotime($curr_perdiod)).' '.$extra_msg;
			$new_ledger_data->status = 'active';
			$new_ledger_data->acct_num = $rr->account1->acct_no;
			$new_ledger_data->date01 = @$zone_read_date;
			$new_ledger_data->period = $curr_perdiod.'-01';
			$new_ledger_data->reff_no = $bill_id;
			$new_ledger_data->save();

			##############
			##############
			$billing_nonWa = BillingNw::where('typ', 'nw_child')
								->where('acct_id', $rr->account_id)
								->where('date1','like', $curr_perdiod.'%')
								->get();
			
			foreach($billing_nonWa as $k10 => $v10) 
			{
				$nw_bill = new LedgerData;
				$nw_bill->led_type ='nw_billing';
				$nw_bill->acct_id  = $rr->account_id;
				$nw_bill->bill_id  = $bill_id;
				$nw_bill->billing  = $v10->amt_1;
				$nw_bill->status   = 'active';
				$nw_bill->ttl_bal  = ($ttl_bal + $v10->amt_1);
				$nw_bill->ledger_info = $v10->title.' - '.(date('F Y', strtotime($v10->date1)));
				$nw_bill->period = date('Y-m-01', strtotime($v10->date1));
				$nw_bill->reff_no = $v10->id1;
				$nw_bill->date01 = @$zone_read_date;
				$nw_bill->save();

				$v10->status = 'billed';
				$v10->id2 = $bill_id;
				$v10->save();
			}//
			
		  }
		  
          $rr->bill_stat = 'billed';
          $rr->save();		  


     }//End
     

     
     static function proccess_arrears($rr, $curr_perdiod, $data_only=false) 
	 {
		 $rr->account1;
		 $zone_id = $rr->account1->zone_id;

		 
		 $led1 = LedgerData::where('status', 'active')
					->where('led_type', 'billing')
						->where('acct_id', $rr->account1->id)
							->where('period', $curr_perdiod)
								->first();
		
		 
		 if(!$led1){
			 
			 $last_led = LedgerData::where('status', 'active')
								->where('acct_id', $rr->account1->id)
									->orderBy('zort1','desc')
										->orderBy('id','desc')
											->first();	 
			 
		 }else{
			 
			 $last_led = LedgerData::where('status', 'active')
									->where('acct_id', $rr->account1->id)
										->where('id', '<', $led1->id)
											->orderBy('zort1','desc')
												->orderBy('id','desc')
													->first();	 
		 }//
		
		
		if($data_only == true){
			return $last_led;
		}

		// if( @$last_led ) {
		// 	ee1(@$last_led->toArray(), __FILE__, __LINE__);
		// }

		##
		##
		$ledger_id = 0;
		if($last_led) { $ledger_id = $last_led->id; }

		$acct_id  = $rr->account1->id;
		$payment_info = CollectionService::payables_and_payed($acct_id);
		foreach($payment_info['payables'] as $k => $v) {
			if( $v->id >  $ledger_id ) {
				unset($payment_info['payables'][$k]);
			}
		}

		$brk1 = CollectionService::breakdown($payment_info['payables'], 0);
		$nwb_amt = 0;
		foreach($brk1 as $k => $v) {
			if( $v['led_type'] == 'nw_billing' ) {
				$nwb_amt += $v['val'];
			}
		}
		##
		##

		// ee($brk1, __FILE__, __LINE__);

		 $led_amt = 0;
		 $led_amt_nwb = $nwb_amt;

		 if($last_led){$led_amt = @$last_led->ttl_bal;}
		 
		 $my_arrear = Arrear::where('acct_id', $rr->account_id)
								->where('period', 'like', $curr_perdiod)
									->first();
		
		if($my_arrear){
			$my_arrear->amount = $led_amt;
			$my_arrear->nwb = $led_amt_nwb;
			$my_arrear->save();			
		}else{
			$my_arrear = new Arrear;
			$my_arrear->acct_id = $rr->account_id;
			$my_arrear->acct_id_str = $rr->account1->acct_no;
			$my_arrear->amount = $led_amt;
			$my_arrear->period = $curr_perdiod;
			$my_arrear->arr_type = 'billing';
			$my_arrear->nwb = $led_amt_nwb;
			$my_arrear->save();			
		}						
									
		 
	 }//
     
     static function proccess_arrears_JUN_14_2020($rr, $curr_perdiod, $data_only=false){
		 $rr->account1;
		 
		 $zone_id = $rr->account1->zone_id;
		 
         $prev_period001 = date('Y-m', strtotime($curr_perdiod.'-28  -1 Month ' ));
         $curr_perdiod   = $curr_perdiod;
         
         $ledger_date =  date('Y-m', strtotime($curr_perdiod.'-'.$zone_id ));
         

		 $my_arrear = Arrear::where('acct_id', $rr->account_id)
					->where('period', 'like', $curr_perdiod.'%')
					->orderBy('period','desc')
					->first();  
					
		if($data_only == true){
			 $xxx = LedgerData::
						where('acct_id', $rr->account1->id)
						->where('status','active')
						->where('led_type','!=', 'billing')
						->orderBy('id', 'desc')
						->first();
			return $xxx;
		}
					
		 if(!$my_arrear)
		 {
			
					
				 $ledger1 = LedgerData::
							where('acct_id', $rr->account1->id)
							->where(function($query)use($rr){
							})
							->where('status','active');
							
				$ledger1->where('led_type','!=', 'billing');
				
				$ledger1 = $ledger1
							->orderBy('zort1', 'desc')
							->orderBy('id', 'desc')
							->first(); 
										
					
					
			 if(!$ledger1){
				$new_arrear = new Arrear;
				$new_arrear->acct_id = $rr->account_id;
				$new_arrear->acct_id_str = $rr->account1->acct_no;
				$new_arrear->amount = 0;
				$new_arrear->period = $curr_perdiod.'-01';
				$new_arrear->arr_type = 'billing';
				$new_arrear->save();					 
			 }else{
				$new_arrear = new Arrear;
				$new_arrear->acct_id = $rr->account_id;
				$new_arrear->acct_id_str = $rr->account1->acct_no;
				$new_arrear->amount = $ledger1->ttl_bal;
				$new_arrear->period = $curr_perdiod.'-01';
				$new_arrear->arr_type = 'billing';
				$new_arrear->save();					 
			 }
			 
		 }else{
					
				 $ledger1 = LedgerData::
							where('acct_id', $rr->account1->id)
							->where(function($query)use($rr){
							})
							->where('status', 'active');
							
				$ledger1->where('led_type','!=', 'billing');
				$ledger1 = $ledger1->orderBy('id', 'desc')
							->first(); 
					
					
					
			 if(!$ledger1){
				 //Do Nothing
				 $my_arrear->amount = 0;
				 $my_arrear->save();
			 }else{
				 $my_arrear->amount = $ledger1->ttl_bal;
				 $my_arrear->save();
			 }

							
			 
		 }
		 
		 
	 }
     


	static function get_coll22($cust_id, $date_start, $date_end)
	{
		return Collection::where('cust_id', $cust_id)
				->where('payment_date','>=', $date_start)
				->where('payment_date','<=', $date_end)
				->where('status', 'active')
				->where('collection_type', 'bill_payment')
				->sum('payment');
	}//



	function generateBilling001_OLD()
	{
		die();
		$hwd_req1 = HwdRequests::where('req_type', 'generate_billing_period_request')
			//->where('status', 'ongoing')
			->first();
			//->toArray();

		if(!$hwd_req1)
		{
			echo 'Completed 1';
			return;
		}


		$data1 = json_decode($hwd_req1->other_datas);

		$curr_perdiod = date('Y-m',
				strtotime($data1->period_year.'-'.$data1->period_month));

		$reading1 = Reading::where('period', $curr_perdiod.'-28')
			->whereNotNull('curr_reading')
			->where('curr_reading', '!=', '')
			->where(function($query){
				// $query->whereNull('bill_stat');
				// $query->orWhere('bill_stat', 'unbilled');
			})
			->limit(50)
			->get();
			//->toArray();

		if(empty($reading1->toArray()))
		{
			$hwd_req1->status = 'completed';
			$hwd_req1->save();
			echo 'Completed 2';
			return;
		}


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



		foreach($reading1 as $rr)
		{

			$prev_reading = 0;
			$prev_reading_str = '';
			$curr_reading = 0;
			$curr_reading_str = '';
			$total_discount = 0;

			$rr->load(['old_reading' => function($query) use($curr_perdiod) {
				$period2 = date('Y-m', strtotime($curr_perdiod.'  -1 Month'));
				$query->where('period', 'like', $period2.'%');
				},
				'account1',
				'billing'
				]);


			$prev_reading = (int) @$rr->old_reading->curr_reading;
			$prev_reading_str = @$rr->old_reading->curr_reading;

			if(!empty($rr->init_reading)){
				$prev_reading = (int) $rr->init_reading;
				$prev_reading_str = $rr->init_reading;
			}

			$curr_reading = (int) $rr->curr_reading;
			$curr_reading_str =  $rr->curr_reading;
			$consump =  $curr_reading - $prev_reading;

			//$acct_type[$rr->account1->acct_type_key];
			$account_type_name = $acct_type[$rr->account1->acct_type_key]['meta_name'];

			// echo '<pre>';
			// print_r($acct_type[$rr->account1->acct_type_key]);
			// print_r($rr->toArray());
			// die();

			$total_billing = BillingCtrl::__calculate_billing_rates($consump, $rate_info[$rr->account1->acct_type_key]);

			$acct_discount_id = (int) @$rr->account1->acct_discount;
			$dis001 = @$discount_type[$acct_discount_id];

			if(!empty($dis001)){
				$total_discount =  $total_billing * ($dis001['meta_value'] / 100)  ;
			}

			// echo '<pre>';
			// echo $curr_perdiod;
			// echo $total_discount;
			// print_r($dis001);
			// print_r($rate_info[$rr->account1->acct_type_key]);

			$total_all = $total_billing  - $total_discount;

			$new_bill = true;

			$info = array(
				@$curr_perdiod,//0
				$prev_reading_str,//1
				$curr_reading_str,//2
				@$consump,//3
				$account_type_name,//4
				$total_all,//5,
				$dis001,	//6
				$total_discount, //7,
				$total_billing, // 8
				$new_bill //9
			);


			 echo '<pre>';
	 		 print_r($reading1->toArray());
	 	      print_r($info);
	 		 die();


			// $readable_period = date('F Y', strtotime(@$info[0]));

	          // $info001 = '
	          //      <ul  class="item_list1 ledlist">
	          //      <li>Period : <span>'.@$readable_period.'</span></li>
	          //      <li>Type :  <span>'.@$info[4].'</span></li>
	          //      <li>Prev. Reading :  <span>'.@$info[1].'</span></li>
	          //      <li>Current. Reading :  <span>'.@$info[2].'</span></li>
	          //      <li>Consumption :  <span>'.@$info[3].'</span></li>
			// 	<li>Current Bill :  <span>'.@number_format($info[8], 2).'</span></li>
	          //      <li>Discount Type :  <span>'.@$info[6]['meta_name'].'('.$info[6]['meta_value'].'%)'.'</span></li>
	          //      <li>Discount Value :  <span>'.@number_format($info[7],2).'</span></li>
	          //      <li>Penalty :  <span>0</span></li>
			// 	<li>Current Total Bill :  <span>'.@number_format($info[5], 2).'</span></li>
	          //      <li>Arears :  <span>0</span></li>
	          //      </ul>
	          // ';
			// echo $info001;
			// die();

			$bill_info = null;

			if($rr->billing){
				$rr->billing->billing_total = $total_billing  - $total_discount;
				$rr->billing->due_stat = null;
				$rr->billing->consumption = (int) @$consump;
				$rr->billing->read_PC = $prev_reading.'||'.$curr_reading;
				$rr->billing->save();

				//$bill_info = $rr->billing->toArray();
				$bill_info = $rr->billing;
				$info[9] = false;

			}else{

				$new_billing = new BillingMdl;
				$new_billing->period = $curr_perdiod.'-28';
				$new_billing->reading_id = $rr->id;
				$new_billing->rate_id	 = $rate_version->id;
				$new_billing->status	 = 'active';
				$new_billing->bill_date	 = date('Y-m-d');
				$new_billing->prep_by	 = 1;
				$new_billing->billing_total = $total_billing  - $total_discount;
				$new_billing->account_id = $rr->account_id;

				$new_billing->consumption = (int) @$consump;
				$new_billing->read_PC = $prev_reading.'||'.$curr_reading;
				$new_billing->save();

				//$bill_info = $new_billing->toArray();
				$bill_info = $new_billing;

			}

			$rr->bill_stat = 'billed';
			$rr->save();


			HwdLedgerCtrl::BillingAcknowledgement($rr->account_id, $info);


		}//foreach

		echo 'Done PROCESSING';
		//echo 'Done';
		//echo '<pre>';
		//print_r($reading1->toArray());
	}//end method



	function generateBillingByPeriod($period){
		return;
		return;
		return;
		return;

			$period1 = date('Y-m', strtotime($period));
			$read11 = Reading::with(['old_reading' => function($query)use($period1){
									$period2 = date('Y-m', strtotime($period1.'  -1 Month'));
									$query->where('period', 'like', $period2.'%');
							}])
							->with('account1')
							->with('billing')
							->where(function($query){
									$query->orWhereNull('bill_stat');
									$query->orWhere('bill_stat', '!=', 'billed');
							})
							->where('period', 'like', $period1.'%')
							//->paginate(20);
							->limit(20)
							->get();//->toArray();

			//echo '<pre>';
			//print_r($read11->toArray());
			//die();

			if(empty($read11->toArray())){
				return  array('billing_status' => 'done', 'period' => $period1);
			}

			$rate_version = BillingRateVersion::orderBy('id', 'desc')->first();

			$data11 = ServiceCtrl::___rates_and_discounts();
			extract($data11);

			die();

			foreach($read11 as $rr)
			{

						$prev_reading = 0;
						$total_discount = 0;

						if(!empty($rr->old_reading)){
							$prev_reading = (int) $rr->old_reading->curr_reading;
						}

						if(!empty($rr->init_reading)){
							$prev_reading = (int) $rr->init_reading;
						}

						$curr_reading = (int) $rr->curr_reading;
						$consump 		=  $curr_reading - $prev_reading;

						$total_billing = BillingCtrl::__calculate_billing_rates($consump, $rate_info[$rr->account1->acct_type_key]);

						if(!empty($discount_type[(int) $rr->account1->acct_discount])){
							$total_discount =  $total_billing * ($discount_type[(int) $rr->account1->acct_discount]['meta_value'] / 100)  ;
						}

						//print_r($rate_info[$rr->account1->acct_type_key]);
						//echo $consump;
						//echo $total_billing  - $total_discount;
						//echo "\n";

						/**/
						if($rr->billing){
								$rr->billing->billing_total = $total_billing  - $total_discount;
								$rr->billing->due_stat = null;
								$rr->billing->save();
						}else{
								$new_billing = new BillingMdl;
								$new_billing->period = $period1.'-28';
								$new_billing->reading_id = $rr->id;
								$new_billing->rate_id	 = $rate_version->id;
								$new_billing->status	 = 'active';
								$new_billing->bill_date	 = date('Y-m-d');
								$new_billing->prep_by	 = 1;
								$new_billing->billing_total = $total_billing  - $total_discount;
								$new_billing->account_id = $rr->account_id;
								$new_billing->save();
						}

						$rr->bill_stat = 'billed';
						$rr->save();
						/**/

			}//Endforeach

			return  array('billing_status' => 'ongoing', 'period' => $period1);

	}//end func
	
	
	//Ang Notice of Disconnection ay nangyayari 15 araw pagkatapos ng Penalty
	// O di kaya 1 Buwan na walang Activity
	function NoticeDisconnectGenerate()
	{
		
		$current_date   =  date('Y-m-28');
		$prev_billing     =  date('Y-m-1',  strtotime($current_date.'- 1 Month'));
		$penalty_date   =  date('Y-m-16',  strtotime($current_date.'- 1 Month'));// +10 days
		$discon_date     =  date('Y-m-27',  strtotime($current_date.'- 1 Month'));
		$discon_for_period  =  date('Y-m-28',  strtotime($current_date.'- 2 Month'));

		//~ echo $prev_billing;
		//~ echo '<br />';
		
		//~ echo '<br />';
		//~ echo $current_date ;
		//~ echo '<br />';
		//~ echo $prev_billing.'//Billing sa '.$discon_period;
		//~ echo '<br />';
		//~ echo $discon_period;
		//~ echo '<br />';
		
		//~ $hwd_req1 = 
		//~ HwdRequests::where('req_type', 'generate_billing_period_request')
		//~ ->doesntHave('billing_due')
		//~ ->where('status', 'completed')
		//~ ->limit(10)
		//~ ->get();

		$bill_result1 = BillingMdl::where('period', $discon_for_period)
			->whereDoesnthave('collection_total')
			->whereHas('account', function($query){
					$query->where('conn_stat', 'active');
			})
			->whereDoesnthave('collection_by_user', function($query)use($prev_billing){
					$query->where('payment_date', '>', $prev_billing);
					$query->where('collection_type', 'bill_payment');
			})
			->with('collection_total')
			->with('account')
			->with(['collection_by_user' => function($query)use($prev_billing){
					$query->where('payment_date', '>', $prev_billing);
			}])
			->limit(10)
			->get();
			
		//~ echo '<pre>';
		//~ print_r($bill_result1->toArray());
		//~ die();
		
		if($bill_result1->count() == 0)
		{
			echo 'No Disconnection';
			exit();
		}
		
		foreach($bill_result1 as $bbr)
		{
			
			$bbr->account->conn_stat = 'for_disconnection';
			$bbr->account->save();

			$info = array(
				'billing_total' => $bbr->billing_total,
				'penalty' => $bbr->penalty,
				'period' => $bbr->period,
				'bill_id' => $bbr->id,
				'arrears' => $bbr->arrears,
				'total_bill' => (double) @$bbr->arrears+ (double) @$bbr->penalty +(double) @$bbr->billing_total,
				'bill_date' => $prev_billing,	
				'penalty_date' => $penalty_date,
			);
			
			$user_id = $bbr->account->id;
			HwdLedgerCtrl::DisconnectNoticeProcess($user_id, $info);
			
		}
		
		echo 'Proccessing';
		
		//~ echo '<pre>';
		//~ print_r($bill_result1->toArray());				
		
	}//
	
	function DueGenerate11()
	{
		
		die();
		die();
		die();
		
		$current_date  = date('Y-m-d');
		
		$hwd_req1 = 
					HwdRequests::where('req_type', 'generate_billing_period_request')
					->doesntHave('billing_due')
					->where('status', 'completed')
					->limit(10)
					->get();
					
					
		//~ echo '<pre>';
		//~ print_r($hwd_req1->toArray());
		//~ die();
		
		
		if($hwd_req1->count() == 0){
			echo 'No penalty procced';
			exit();
		}
					
		foreach($hwd_req1  as  $hr1)
		{
			$per11 = date('Y-m',strtotime($hr1->dkey1));

			$bill_result1 = BillingMdl::where('period', 'like', $per11.'%')
						->with('collection_total')
						->whereNull('penalty')
						->limit(10)
						->get();	
						
			//~ echo '<pre>';
			//~ print_r($bill_result1->toArray());
			//~ die();						
						
			if($bill_result1->count() == 0)
			{
				continue;
				
				// Gumawa ng Request para sa  TANDAAN na ang process ay TAPOS na
				$new_due = new HwdRequests;
				$new_due->reff_id = $hr1->id;
				$new_due->req_type = 'billing_due_request';
				$new_due->status = 'active';
				$new_due->remarks = 'BILLING DUE for '.date('F Y', strtotime($hr1->dkey1));
				$new_due->other_datas = $hr1->other_datas;
				$new_due->dkey1 = $hr1->dkey1;
				$new_due->save();
				continue;
			}			
			
			//ang PENALTY ay 16 araw mula sa pinakahuling billing
			//$due_date = date('Y-m-16', strtotime($hr1->dkey1.' + 1 Month'));
			$due_date = date('Y-m-01', strtotime($per11.'-01 + 1 Month'));
			
			//Ang billing ay ika 1 sa susunod na buwan  e.g.  January bill will be proccess in Feb 1
 			$bill_generate  = date('Y-m-01', strtotime($per11.'-01 + 1 Month'));
			
			// inihanda lamang ang TIME ng  kasalukuyan at ang  penalty TIME
			$cur_tim = strtotime($current_date);
			$due_tim = strtotime($due_date);
			
			// Kung ang Arraw na ito ba ay penalty TIME o hindi
			//if($cur_tim >= $due_tim)
			if($cur_tim >= $due_tim)
			{
				echo "Penalty Applied \n";
				//E Proccesso lang PENALTY
				$this->addBillingPenalty($bill_result1);
			}else{
				//Walang gagawin
				echo "Hindi pa  Penalty ".$hr1->dkey1." \n";
				//~ $this->addBillingPenalty($bill_result1);
				
			}
			
		}

					
	}//
	
	private function  addBillingPenalty($bill_result1)
	{

		//~ echo '<pre>';
		//~ print_r($bill_result1->toArray());
		//~ die();

		
		
		//Isa isahin ang billing 
		foreach($bill_result1 as $br11)
		{
			//ihanda ang billing id para sa ledger na gagawin
			$billing_id = $br11->id;
			$account_id = $br11->account_id;
			
			//Ihanda ang Utang at mga nakolecta amount 
			// Kunin lamang ang balance
			$debit =  $br11->billing_total - $br11->discount;
			$collected = (double) @$br11->collection_total->total_payed;
			$balance1 = $debit - $collected;

			//~ echo '<pre>';
			//~ echo $debit;
			//~ print_r($bill_result1->toArray());
			//~ die();
			
			//mag kakaron ng penalty kung mataas sa ZERO ang balance
			//at kung wala ay zero ang penalty
			if($balance1 > 0)
			{
				$total_penalty  = $balance1 * 0.1;
				$br11->penalty = $total_penalty;
				$br11->save();
				
				$info_arr = array(
					'period' => $br11->period,
					'billing_id' => $billing_id,
					'billed' => $debit,
					'collected' => $collected,
					'balance' => $balance1,
					'penalty' => $total_penalty
				);
				
				
				HwdLedgerCtrl::DueProcess($account_id, $info_arr);
				

				LedgerCtrl::add_penalty($account_id, array(
						'period' => $br11->period, 
						'amount' => $total_penalty,
						'bill_id' => $billing_id,
						'read_id' => 0
					));		
				
				
				
			}else{
				$br11->penalty = 0;
				$br11->save();
			}
		}
	}
	

	
	
	function DueGenerate11_XX()
	{
		die();
		die();
		die();


		$hwd_req1 = HwdRequests::where('req_type', 'generate_billing_period_request')
			->doesntHave('billing_due')
			->where('status', 'completed')
			->first();

		if($hwd_req1)
		{
			$new_due = new HwdRequests;
			$new_due->reff_id = $hwd_req1->id;
			$new_due->req_type = 'billing_due_request';
			$new_due->status = 'pending';
			$new_due->remarks = 'BILLING DUE '.$hwd_req1->remarks;
			$new_due->other_datas = $hwd_req1->other_datas;
			$new_due->save();

			$other_datas = json_decode($hwd_req1->other_datas);
			$period1 = date('Y-m-28', strtotime($other_datas->period_year.'-'.$other_datas->period_month));
			$due_date = date('Y-m-d',strtotime($period1.' + 10 days'));

			$new_due->date_stat = $due_date;
			$new_due->save();

			echo 'Due Added';
			return;
		}

		$today_date = date('Y-m-d');

		$hwd_billing_due = HwdRequests::where('req_type', 'billing_due_request')
			->where('status', 'pending')
			->where('date_stat','<', $today_date)
			->first();

		if(!$hwd_billing_due)
		{
			echo 'No Due proccess.';
			return;
		}


		$other_datas = json_decode($hwd_billing_due->other_datas);
		$period1 = date('Y-m-28', strtotime($other_datas->period_year.'-'.$other_datas->period_month));

		$due_date = date('Y-m-d',strtotime($period1.' + 10 days'));

		$today_time = time();

		$bill1 = BillingMdl::where('period','like', $period1)
					->whereNotNull('billing_total')
					->whereNull('due_stat')
					->with('collection')
					->limit(10)
					->get();

		if(empty($bill1->toArray())){
			$hwd_billing_due->status = 'completed';
			$hwd_billing_due->save();
			echo 'No Due proccess.';
			return;
		}

		foreach($bill1 as $bb1){
			//$bb1->billing_total;
			$total_collect = 0;
			foreach($bb1->collection as $cc1){
				$total_collect +=  $cc1->payment;
			}

			$bill_balance = $bb1->billing_total - $total_collect;
			if($bill_balance > 0){

				$bb1->due_stat = 'has-due';
				$bb1->save();

				$due1  = $bill_balance * 0.1;
				$due_amount  = $bill_balance * 1.1;

				$curr_due = BillingDue::where('bill_id', $bb1->id)
						->first();

				if($curr_due){
					$curr_due->bill_balance = $bill_balance;
					$curr_due->due1 = $due1;
					$curr_due->due_amount = $due_amount;
					$curr_due->save();
				}else{
					$curr_due  = new BillingDue;
					$curr_due->bill_id = $bb1->id;
					$curr_due->due_date = $due_date;
					$curr_due->due_stat = 'active';
					$curr_due->bill_balance = $bill_balance;
					$curr_due->due1 = $due1;
					$curr_due->due_amount = $due_amount;
					$curr_due->save();
				}

			}else{
				$bb1->due_stat = 'no-due';
				$bb1->save();
			}
		}

		 //echo '<pre>';
		 //print_r($bill1->toArray());
		 //echo 'Due on going.';
	}//

	function DueGenerate($period){
		return;
		return;
		return;
		return;

		$period1 = date('Y-m', strtotime($period));
		$due_date = date('Y-m-d', strtotime($period1.'-28 +10 days'));

		$bill1 = BillingMdl::where('period','like', $period1.'%')
					->whereNotNull('billing_total')
					->whereNull('due_stat')
					->limit(10)
					->get();

		//echo '<pre>';
		//print_r($bill1->toArray());
		//die();

		if(empty($bill1->toArray())){
			return  array('generate_due_status' => 'done', 'period' => $period1);
		}

		foreach($bill1 as $bb){

				$total_collect = $bb->collSum();

				if($total_collect >= $bb->billing_total){
					$bb->due_stat = 'no-due';
					$bb->save();
				}else{

					$due_blance  = $bb->billing_total - $total_collect;
					
					$due1  = $bb->billing_total * 0.1;
					$due_amount  = $bb->billing_total * 1.1;

					$curr_due = BillingDue::where('bill_id', $bb->id)->first();

					if($curr_due){
						$curr_due->bill_balance = $due_blance;
						$curr_due->due1 = $due1;
						$curr_due->due_amount = $due_amount;
						$curr_due->save();
					}else{
						$curr_due  = new BillingDue;
						$curr_due->bill_id = $bb->id;
						$curr_due->due_date = $due_date;
						$curr_due->due_stat = 'active';
						$curr_due->bill_balance = $due_blance;
						$curr_due->due1 = $due1;
						$curr_due->due_amount = $due_amount;
						$curr_due->save();
					}
					$bb->penalty = $due1;
					$bb->due_stat = 'has-due';
					$bb->save();
				}

		}

		return  array('generate_due_status' => 'ongoing', 'period' => $period1);

	}//


	function generateReport1()
	{
		//generate_billing_period_request

		$report_list = HwdRequests::where('req_type', 'report_generate_request')
				//~ ->where('status', 'started')
				->where(function($query){
					$query->orWhere('status', 'started');
					$query->orWhere('status', 're-started');
				})
				->first();
				
		
		if(!$report_list){ echo 'no service'; return;}

		$dd = json_decode($report_list->other_datas);
		$period1 =  date('Y-m', strtotime($dd->period_year.'-'.$dd->period_month.''));
		
		//~ echo $period1;
		//~ die();

		$is_billing_ready = HwdRequests::where('req_type', 'generate_billing_period_request')
				->where('dkey1','like',$period1.'%')
				->where('status', 'completed')
				->first();

		if(!$is_billing_ready){
			//~ echo $period1;
			echo 'service stop.. billing is not yet ready';
			return;
		}
		

		$acct = Accounts::
			  // whereHas('billing_all', function($query)use($period1){
				// 	$query->where('billing_mdls.period','!=', $period1);
			  //  })
			   whereDoesntHave('reports',function($query)use($period1){
				   $query->where('period','like',$period1.'%');
			   })
			   ->with(['billing_all'=> function($query)use($period1){
				   $query->where('billing_mdls.period','like', $period1.'%');
			   }, 'billing_all.collection_total'])
			   ->orderBy('route_id', 'asc')
			   ->limit(100)
			   ->get();
		
		if($report_list->status == 're-started')
		{
			Reports::where('period','like', $period1.'%')->delete();
			$report_list->status = 'started';
			$report_list->save();
			echo 'Report  is reset';
			exit();
		}
		
		//~ echo '<pre>';
		//~ echo $report_list->status;
		//~ print_r($acct->count());
		//~ die();

		$dd11 = ageing_common(array('mm_arr','lab_days'));
		extract($dd11);

		if($acct->count() == 0)
		{
			echo 'Stop The Services '.$report_list->id;
			$report_list->status = 'completed';
			$report_list->save();
			return;
		}

		//~ echo  '<pre>';
		//~ echo $period1;
		//~ print_r($acct->toArray());
		//~ print_r($dd11);
		//~ echo 'AAAA';
		//~ die();
		

		foreach($acct as $ac)
		{

			$ageing_arr = array();
			
			foreach($mm_arr as $kk=>$vv)
			{
				$pp1 = date('Y-m-01', strtotime($period1.' '.$vv));
				$val1 = getPeriodBalance($pp1, $ac->id);
				$ageing_arr[$kk] = $val1;
			}
			
			$ageing_arr['my'] = getAfterPeriodBalance($vv, $ac->id);
			
			
			//~ $is_report_exist = 
					//~ Reports::where('period', $period1)
						//~ ->where('account_num', $ac->acct_no)
						//~ ->where('user_id', $ac->id)
						//~ ->first();


			
			/*
			 * 
			 * 
			 * 
			 * 
			 * 
			 * 
			 * 
			 */
			 
			 // NEW NEW
			 // NEW NEW
			 // NEW NEW
			 $new_rep = new Reports;
			 $new_rep->user_id = $ac->id;
			 $new_rep->account_num = $ac->acct_no;
			 $new_rep->full_name = $ac->lname.', '.$ac->fname.' '.$ac->mi;
			 $new_rep->acct_type_id = $ac->acct_type_key;
			 $new_rep->acct_status_id = $ac->acct_status_key;
			 $new_rep->zone_id = $ac->zone_id;
			 $new_rep->period = $period1.'-01';

			 if($ac->billing_all->count() != 0)
			 {

				 $current_bill = $ac->billing_all[0]->billing_total;
				 $arrears = $ac->billing_all[0]->arrears;
				 $penalty = $ac->billing_all[0]->penalty;
				 $discount = $ac->billing_all[0]->discount;
				 
				 $total_billing = (($current_bill+$penalty) - $discount);
				 
				 //~ $new_rep->billing_total = 
						//~ $ac->billing_all[0]->billing_total;

				 $new_rep->billing_total = $total_billing;

				 if($ac->billing_all[0]->collection_total)
				 {
						 $new_rep->collected = 
							$ac->billing_all[0]->collection_total->total_payed;
				 }
				 

				 $read_pc = array_filter(explode('||', $ac->billing_all[0]->read_PC));
				 if(!empty($read_pc))
				 {
						 $new_rep->data1 = json_encode(array(
								 'cum'=>$ac->billing_all[0]->consumption,
								 'prev'=>@$read_pc[0],
								 'curr'=>@$read_pc[1],
								 'billing_id'=>$ac->billing_all[0]->id,
								 'reading_id'=>$ac->billing_all[0]->reading_id
							));
				 }

			 }

			 $new_rep->rtype = 'ageing_of_accounts';
			 $new_rep->status = 'active';
			 $new_rep->ageing_data = json_encode($ageing_arr);
			 $new_rep->save();
			 
		}

		//dd($acct);
		//echo '<pre>';
		//print_r($acct->toArray());
		echo $report_list->remarks.' ---- Reports on going.';
	}//



	function RandomZone1()
	{
		$acct = Accounts::whereNull('zone_id')
			->limit(50)
			->get();

		if($acct->count() == 0 ){echo 'No More';return;}

		foreach($acct as $acc)
		{
			$acc->zone_id = rand(1, 16);
			$acc->save();
		}

		echo 'on going.';
	}


	/*************/
	/*************/
	/*************/
	/*************/
	/*************/

	private function  ___billing_generate_data1(){
			$rates_raw   =
					BillingMeta::where('meta_type', 'billing_rates')
						->where('status','active')
						->get()
						->toArray();

			$rate_info = array();
			foreach($rates_raw as $rww){
				 $arr1 = (array) json_decode($rww['meta_data']);
				 $rate_info[$arr1['acct_type']][] = $arr1;
			}

			$bill_discount =
				BillingMeta::where('meta_type', 'billing_discount')
					->where('status', 'active')
					->get()
					->toArray();

			$discount_type = array();
			foreach($bill_discount as $bdd){
				$discount_type[$bdd['id']] = $bdd;
			}

			return compact('rate_info','discount_type');
	}


	private  function __calculate_billing_rates($consumption, $acct_type_set){

		foreach($acct_type_set as $ats){

			$min1 = $ats['min_cu'];
			$max1 = $ats['max_cu'];

			 if(($min1 <= $consumption) && ($consumption <= $max1)){
				 if($ats['price_rate'] == 0){
					 return $ats['min_charge'];
				 }else{
					 return ((($consumption - $min1) * $ats['price_rate'])
								+ $ats['min_charge']) + $ats['price_rate'];
				 }
			 }

		}

		return 0;
	}


	static  function ___rates_and_discounts(){
			//BILLING RATES
			$rates_raw   = BillingMeta::where('meta_type', 'billing_rates')->where('status','active')->get()->toArray();
			$rate_info = array();
			foreach($rates_raw as $rww){
				$arr1 = (array) json_decode($rww['meta_data']);
				$rate_info[$arr1['acct_type']][] = $arr1;
			}
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


			return compact('rate_info', 'discount_type');
	}
	
	
	//reading_period_start
	function ReadingPeriodStart()
	{
			
			$rrd1 = ReadingPeriod::where('status', 'ongoing')->first();
			
			if(!$rrd1){
				echo 'Done';
				return;				
			}
			
			//~ echo '<pre>';
			//~ print_r($rrd1->toArray());
			//~ die();

			$period1 = date('Y-m', strtotime($rrd1->period)); 
			$prev_period = date('Y-m', strtotime($rrd1->period.' -1 month')); 
			
			//~ echo $period1;
			//~ echo '<br />';
			//~ echo $prev_period;
			//~ die();
			
			$acccunts = Accounts::
							whereDoesntHave('reading1', function($query) use($period1){
								$query->where('period', 'like', $period1.'%');
							})
							->with(['reading_prev'  => function($query)use($prev_period){
								$query->where('period', 'like', $prev_period.'%');
							}])
							//~ ->whereNotNull('meter_number1')
							->limit(100)
							->get();
									
			//~ echo '<pre>';
			//~ print_r($acccunts->toArray());
			//~ die();
			
			if(empty($acccunts->toArray())){
				$rrd1->status  = 'completed';
				$rrd1->save();
				
				echo 'Done';
				return;
			}

			foreach($acccunts as $acct)
			{
				
				if(!$acct->reading_prev)
				{
					$read1 = new Reading;
					$read1->zone_id = $acct->zone_id;
					$read1->account_id = $acct->id;
					$read1->account_number = $acct->acct_no;
					$read1->meter_number = $acct->meter_number1;
					$read1->period = $period1.'-1';
					$read1->prev_reading = null;
					$read1->status = 'active';
					$read1->bill_stat = 'unbilled';
					$read1->prev_read_date = null;
					$read1->save();					
					
				}else{
					$read1 = new Reading;
					$read1->zone_id = $acct->zone_id;
					$read1->account_id = $acct->id;
					$read1->account_number = $acct->acct_no;
					$read1->meter_number = $acct->meter_number1;
					$read1->period = $period1.'-1';
					$read1->prev_reading = $acct->reading_prev->curr_reading;
					$read1->status = 'active';
					$read1->bill_stat = 'unbilled';
					$read1->prev_read_date = $acct->reading_prev->curr_read_date;
					$read1->save();
				}
			}
			
			echo 'on progress';
			
	}
	
	
	

	function   OverdueServiceStart()
	{
		
		return;
		return;
		return;
		return;
		
			
		$current_date = date('Y-m-d');
			
		$service_date = ServiceBillZone::where('status', 'COMPLETED')
							->where('pen_stat', 'pending')
							->where('pen_date', '<=', $current_date)
							->orderBy('pen_date', 'asc')
							->first();
							
		if(!$service_date){
			echo 'no overdue process';
			return;
		}
		
		$period = date('Y-m', strtotime($service_date->period));
		$zone_id = $service_date->zone_id;
		$overdue_date = $service_date->pen_date;
		
		//~ echo $period;
		//~ echo '<br />';
		//~ echo $zone_id;
		//~ echo '<br />';
		//~ echo $overdue_date;
		//~ echo '<pre>';
		//~ print_r($service_date->toArray());
		//~ die();
		
		
		//~ $over_stat = OverdueStat::where('status', 1)
						//~ ->orderBy('date1', 'asc')
							//~ ->first();		
		
		//~ if(!$over_stat){
			//~ echo 'no overdue process';
			//~ return;
		//~ }
								
		//~ $period = date('Y-m', strtotime($over_stat->period));
		//~ $zone_id = $over_stat->zone_id;
		//~ $overdue_date = $over_stat->date1;
		
		
		
		$bill1 = BillingMdl::where('billing_mdls.period','like', $period.'%')
							->where('A.zone_id', $zone_id)
							->whereNull('due_stat')
							->leftJoin('accounts as A', 'A.id', '=', 'billing_mdls.account_id')
							->leftJoin('arrears as AR', function($join) use($period){
								$join->on('AR.acct_id','=','A.id');
								$join->on('AR.arr_type','=',  DB::raw("'billing'"));
								$join->on('AR.period','like', DB::raw("'".$period.'%'."'"));
							})
							->select(DB::raw('
									billing_mdls.*, A.acct_no ,A.zone_id, AR.amount as arrear_amout
							'))
							->groupBy('billing_mdls.id')
							->limit(30)
							->get();		

		if(empty($bill1->toArray())){
			echo 'No More';
			$service_date->pen_stat = 'done';
			$over_stat->save();
			die();
		}
		
		
		//~ if(empty($bill1->toArray())){
			//~ echo 'No More';
			//~ $over_stat->status = 2;
			//~ $over_stat->save();
			//~ die();
		//~ }
		
		//~ echo '<pre>';
		//~ echo $period;
		//~ echo '<br />';
		//~ echo $zone_id;
		//~ print_r($bill1->toArray());
		//~ die();
		
		
		
		$limit = 10;
		foreach($bill1 as $bb1)
		{
			
				$total_recivables = $bb1->curr_bill  + $bb1->arrear_amout; 
				$total_collected   = Collection::where('cust_id', $bb1->account_id)
										->where(function($query)use($overdue_date, $bb1){
												$query->where('payment_date', '<=',$overdue_date);
												$query->where('payment_date', '>=', date('Y-m-'.$bb1->zone_id, strtotime($overdue_date)) );
										})
										->sum('payment');
				
				$balance = ($total_recivables - $total_collected);
				
				//~ echo '<pre>';
				//~ echo $balance;
				//~ echo '<br />';
				//~ echo $total_collected;
				//~ $bb1->account2;
				//~ print_r($bb1->toArray());
				//~ die();
				
				
				if($balance > 0){
					
					
					//~ LedgerData::where('bill_id', $bb1->id)
						//~ ->where('led_type', 'penalty')
							//~ ->delete();
						
					//~ BillingDue::where('bill_id', $bb1->id)	
							//~ ->delete();
							
					
					$due_amout = (@$bb1->curr_bill - @$bb1->discount) * 0.1;
					$due = (@$bb1->curr_bill - @$bb1->discount) * 0.1;
					
					
					$penalty11 = BillingDue::where('period', $bb1->period)
									->where('acct_id', $bb1->account_id)
										->first();
					
					if(!$penalty11){
						$new_due = new BillingDue;
						$new_due->bill_id = $bb1->id;
						$new_due->due_amount = $due_amout;
						$new_due->due_date = date('Y-m-d', strtotime($overdue_date.''));
						$new_due->due_stat = 'active';
						$new_due->acct_id = $bb1->account_id;
						$new_due->acct_no = $bb1->acct_no;
						$new_due->period = $bb1->period;
						$new_due->save();
					}else{
						$penalty11->due_amount = $due_amout;
						$penalty11->due_date = date('Y-m-d', strtotime($overdue_date.''));
						$penalty11->save();
					}
					
					
					//echo 'Due applied <br />';
					$bb1->due_stat = 'has-due';
					$bb1->penalty  = $due_amout;
					
					$pen_date = date('Y-m-d', strtotime($overdue_date.''));
					
					$led2 = LedgerData::where('acct_id', $bb1->account_id)
								->where('led_type','!=','penalty')
								->where('date01', '<=',$pen_date)
								->where('status', 'active')
								->orderBy('date01', 'desc')
								->orderBy('id', 'desc')
								->first();
								
						$prev_bal = 0;
						if($led2)
						{$prev_bal = (float) $led2->ttl_bal;}
						$prev_bal += (float) @$due_amout;
						
						$pre_ledger = LedgerData::where('acct_id', $bb1->account_id)
										->where('period',$period.'-01')
										->where('led_type', 'penalty')
										->where('status', 'active')
										->first();
										
										
						//~ echo $period;
						//~ die();
						
									
						if(!$pre_ledger)
						{
							$led1 = new LedgerData;
							$led1->acct_id = $bb1->account_id;
							$led1->date01 = $pen_date;
							$led1->status = 'active';
							$led1->led_type = 'penalty';
							$led1->penalty = (float) @$due_amout;
							$led1->ttl_bal = $prev_bal;
							$led1->period = $period.'-01';
							$led1->ledger_info = 'Bill Reff: #'.$bb1->id.' <br />Current Balance: '.number_format($led2->ttl_bal, 2).'';
							$led1->bill_id = $bb1->id;
							$led1->save();				
						}else{
							//NOTHING
							$pre_ledger->penalty = (float) @$due_amout;
							$pre_ledger->ttl_bal = $prev_bal;
							$pre_ledger->period = $period.'-01';
							$pre_ledger->ledger_info = 'Bill Reff: #'.$bb1->id.' <br />Current Balance: '.number_format($led2->ttl_bal, 2).'';
							$pre_ledger->save();
						}
						
					
					
				}else{
					$bb1->due_stat = 'no-due';
					$bb1->penalty  = 0;
					
					//echo 'No Due <br />';
				}
				$bb1->save();
				
				
				//~ break;
				//~ break;
				//~ break;
				//~ break;
							
				//~ if($limit<=0){break;}		
				$limit--;
		}
		
		//~ echo $period;

			echo 'Ongoing';
			die();
					
		//~ echo '<pre>';
		//~ print_r($bill1->toArray());
	}
	

	
	



}
