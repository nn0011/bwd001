<?php 

header('Access-Control-Allow-Origin: *');
extract($_GET);

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => 'http://127.0.0.1:8000'.$url1
	//~ CURLOPT_URL => 'http://192.168.2.254:8585'.$url1
));
$resp = curl_exec($curl);
curl_close($curl);

if(!$resp){
	echo json_encode(array('status'=>'0'));
	die();
}

$data11 = json_decode($resp);

//~ echo '<pre>';
//~ var_dump($data11);
//~ die();

$per_arr = array_filter( explode('/', $url1) );

require __DIR__ . '/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;



function add_print_Hspace($cc)
{
	$spc = '';
	for($x=1;$x<=$cc;$x++)
	{
		$spc.=' ';
	}
	return $spc;
}//


function add_print_Vspace($cc)
{
	$spc = "";
	for($x=1;$x<=$cc;$x++)
	{
		$spc.="\n";
	}
	return $spc;
}//


function PrintBill($rr1, $period1, $printer)
{
	
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
	
	/**/
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
	/**/
	
	//~ $printer->text(add_print_Vspace(7));
	
	
	//~ $printer->close();

	//~ fwrite($printer, $line1);
	//~ fwrite($printer, add_print_Vspace(3));
	//~ fwrite($printer, $line2);
	//~ fwrite($printer, add_print_Vspace(4));
	//~ fwrite($printer, $line3);
	//~ fwrite($printer, add_print_Vspace(3));
	//~ fwrite($printer, $line4);
	//~ fwrite($printer, add_print_Vspace(1));
	//~ fwrite($printer, $line5);
	//~ fwrite($printer, add_print_Vspace(1));
	//~ fwrite($printer, $line5_2);
	//~ fwrite($printer, add_print_Vspace(1));
	//~ fwrite($printer, $line6);
	//~ fwrite($printer, add_print_Vspace(1));
	//~ fwrite($printer, $line7);
	//~ fwrite($printer, add_print_Vspace(1));
	//~ fwrite($printer, $line8);
	//~ fwrite($printer, add_print_Vspace(1));
	//~ fwrite($printer, $line9);
	//~ fwrite($printer, add_print_Vspace(1));
	//~ fwrite($printer, $line10);
	//~ fwrite($printer, add_print_Vspace(1));		

}//

//~ $fp2 = fopen('/dev/usb/lp0', 'w');
//~ PrintBill($rr1=null, $period1=null, $fp2);
//~ fclose($fp2);

try{
	
		//~ @$connector = new FilePrintConnector("/dev/usb/lp0");
		@$connector = new FilePrintConnector('billing001.txt');
		$printer = new Printer($connector);
		$printer->initialize();
		
		foreach($data11  as  $vv)
		{
				$printer->text(add_print_Vspace(7));
				PrintBill($vv, $per_arr[2], $printer);
				$printer->text(add_print_Vspace(8));
		}
		
		//~ $printer->cut();
		//~ $printer->feedForm();
		$printer->close();
		echo json_encode(array('status'=>'1'));
		
}catch(Exception $e){
	echo $e;
	echo json_encode(array('status'=>'0222'));
}

