<?php 


namespace App\Services\Collections;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// use App\Collection;
use App\LedgerData;
use App\Arrear;
use App\Collection;
use App\OtherPayable;
use App\HwdLedger;
use App\NwbBalance;
use App\PayPenLed;
use App\Accounts;
use App\Invoice;
use App\BillingMdl;
use App\PayLed;
use App\CollectLedger;
use App\User;

use App\Services\Collections\CollectionService;

use Excel;


class CollectionReportService
{
	static
	function report_start_service() 
	{

		/**********************/
		/**********************/
		/**********************/
		/**********************/
		if( !strtotime(@$_GET['dd']) )
		{
			echo  'ERROR ON DATE';
			return;
		}

		$dd2 = date('Y-m',strtotime(@$_GET['dd']));

		$ids = self::daily_coll_max_ids();

		$col_all = Collection::where('payment_date','like', $dd2.'%')
									->whereIn('id', $ids)
									// ->whereHas('collect_report_ledger')
									->whereDoesntHave('collect_report_ledger')
									->with(['accounts', 'coll_ledger', 'billing'])
									->orderBy('invoice_num', 'asc')
									// ->where('invoice_num', '0106725')
										// ->limit(100)
										->get();




		if($col_all->count() == 0){
			echo 'DONE LOADED';
			die();
		}

		foreach( $col_all as $cc) 
		{
			
			// echo '<pre>';
			// print_r($cc->toArray());
			// die();

			$cc->my_collectables  = [];
			$cc->payable_list = [];
			$cc->balance_raw = [];
			$cc->payed_arr   = [];
			$cc->pycy = [];
			$cc->total_pay   = $cc->payment;			


			if(in_array($cc->status, ['cancel_cr'])) 
			{

				if( $_GET['store_to_collection_ledger']  == 1 )
				{
					$new_col_led = new CollectLedger;
					$new_col_led->coll_id = $cc->id;
					$new_col_led->acct_id = $cc->cust_id; 
					$new_col_led->admin_id = $cc->collector_id; 
					$new_col_led->led_id = @$cc->coll_ledger[0]->id; 
					$new_col_led->tax_val = $cc->tax_val; 
					$new_col_led->discount_val = $cc->discount1;  
					$new_col_led->collect_raw = json_encode($cc->my_collectables); 
					$new_col_led->collect_clean = json_encode($cc->payed_arr); 
					$new_col_led->pay_type = $cc->pay_type; 
					$new_col_led->coll_date  = $cc->payment_date; 
					$new_col_led->pycy = json_encode($cc->pycy); ;
					$new_col_led->amount = $cc->payment;
					$new_col_led->save(); 		
				}
	


				continue;
			}else{
				$cc->my_collectables = CollectionService::get_collectables($cc->coll_ledger[0]->id, $cc->cust_id, $cc);
			}



			if( !empty($cc->my_collectables['payable']) )
			{
				$amount = $cc->payment;

				//Payment Only
				// $additional_payment = CollectionService::get_existing_payment($cc->my_collectables); 
				//Payment Only

				// $cc->additional_payment = $cc->my_collectables['ttl_payment'] - $amount; // EMBED TO COLLECTION
				// $additional_payment     = $cc->my_collectables['ttl_payment'] - $amount;

				$cc->additional_payment = $cc->my_collectables['ttl_payment']; // EMBED TO COLLECTION
				$additional_payment     = $cc->my_collectables['ttl_payment'];

				// $amount += $additional_payment; // INIT THE EXISTING PAYMENT

				// $payable_raw = CollectionService::get_payable_raw($cc->my_collectables['payable'], @$cc->billing->penalty_date); // GET PAYABLE RAW
				$payable_raw    =  $cc->my_collectables['payable'];

				// echo '<pre>';
				// print_r($additional_payment);
				// print_r($cc->my_collectables);
				// die();

				/**///REFINE THE PAYABLES
				$existing_payment = $additional_payment;
				// echo $additional_payment;
				// die();


				$payable_list     = CollectionService::remove_payed_acct($payable_raw, $existing_payment);
				// echo '<pre>';
				// print_r($payable_list);
				// die();

				$balance_raw      = CollectionService::remove_payed_acct($payable_list[0], $amount);

				// echo '<pre>';
				// print_r($balance_raw);
				// die();

				/**/
				$break_down_raw = $balance_raw[1];

				$pycy = CollectionService::get_py_cy($break_down_raw, $cc->payment_date);

				// echo '<pre>';
				// print_r($pycy);
				// die();				

				// SET THE PAYABLES AND EMBED TO COLLECTION
				$cc->payable_list = $payable_list[0];
				$cc->balance_raw = $balance_raw[0];
				$cc->payed_arr   = $balance_raw[1];
				$cc->pycy = $pycy;
				$cc->total_pay   = $amount;

			}
			/**/
			/**/

			if( $_GET['store_to_collection_ledger']  == 1 )
			{
				$new_col_led = new CollectLedger;
				$new_col_led->coll_id = $cc->id;
				$new_col_led->acct_id = $cc->cust_id; 
				$new_col_led->admin_id = $cc->collector_id; 
				$new_col_led->led_id = $cc->coll_ledger[0]->id; 
				$new_col_led->tax_val = $cc->tax_val; 
				$new_col_led->discount_val = $cc->discount1;  
				$new_col_led->collect_raw = json_encode($cc->my_collectables); 
				$new_col_led->collect_clean = json_encode($cc->payed_arr); 
				$new_col_led->pay_type = $cc->pay_type; 
				$new_col_led->coll_date  = $cc->payment_date; 
				$new_col_led->pycy = json_encode($cc->pycy); ;
				$new_col_led->amount = $cc->payment;
				$new_col_led->save(); 		
			}
	
		}//

		if( $_GET['store_to_collection_ledger']  == 1 )
		{
			echo 'LOADING';
			die();
		}



		echo '<pre>';
		print_r($col_all->toArray());
		die();


		/**********************/
		/**********************/
		/**********************/
		/**********************/		


	}// END


	static
	function daily_coll_max_ids()
	{
		$dd = date('Y-m',strtotime(@$_GET['dd']));

		$sql1 = "
			SELECT id FROM (
				SELECT *, CAST(invoice_num as UNSIGNED) MMM FROM collections
				WHERE id IN (
					SELECT MAX(id) FROM collections 
					WHERE payment_date like '$dd%'
					GROUP BY invoice_num
				)
			) TAB1
			ORDER BY MMM ASC
		";

		$my_collection = DB::select($sql1);

		$ids = [];
		foreach($my_collection as $kk => $vv)
		{
			$ids[]=$vv->id;
		}

		return $ids;

	}//


	static
	function daily_coll_max_ids_v2($ss, $ee)
	{
		$ss = date('Y-m-d',strtotime(@$ss));
		$ee = date('Y-m-d',strtotime(@$ee));

		$sql1 = "
			SELECT id FROM (
				SELECT *, CAST(invoice_num as UNSIGNED) MMM FROM collections
				WHERE id IN (
					SELECT MAX(id) FROM collections 
					WHERE 
						payment_date >= '$ss%'
						AND
						payment_date <= '$ee%'
					GROUP BY invoice_num
				)
			) TAB1
			ORDER BY MMM ASC
		";

		$my_collection = DB::select($sql1);

		$ids = [];
		foreach($my_collection as $kk => $vv)
		{
			$ids[]=$vv->id;
		}

		return $ids;

	}//	


	static 
	function  daily_collection_excel()
	{

		$user = Auth::user();
		
		$user = User::find($user->id);		
		

		$collector_name = $user->name;
		$date1 = @$_GET['dd'];
		$zz = @$_GET['zz'];

		$dd = date('Y-m',strtotime(@$_GET['dd']));


		$ids = self::daily_coll_max_ids();

		$col_all = Collection::where('payment_date','like', $dd.'%')
									->whereHas('collect_report_ledger')
									// ->whereDoesntHave('collect_report_ledger')
									->whereIn('id', $ids)
									->with(['accounts', 'collect_report_ledger'])
									->orderBy('invoice_num', 'asc')
									// ->where('invoice_num', '106291')
									// ->limit(1)
										->get();

		$check_if_complete = Collection::where('payment_date','like', $dd.'%')
								->whereIn('id', $ids)
								->whereDoesntHave('collect_report_ledger')
									->orderBy('invoice_num', 'asc')
										->count();

		if( $check_if_complete > 0 ) {
			echo '
					<a href="/daily_collect_service?dd='.$dd.'&store_to_collection_ledger=1" target="blank">
						Execute Now - Please Wait until its done. 
					</a>
				';
			die();
		}

		// echo '<pre>';
		// print_r($col_all->toArray());
		// die();

		$new_coll_array = [];

		foreach($col_all as $cc)
		{
			$pay_date = date('Y-m-d', strtotime($cc->payment_date)); 
			$new_coll_array[$pay_date][] = $cc;
		}

		ksort($new_coll_array);

		Excel::load(public_path('excel02/daily_coll_report001.xls'), 
			function($excel) use ($new_coll_array, $date1) {


				//ADD SHEET OR INITIALIZE - START
				$index = 0;
				foreach($new_coll_array as $kk => $vv)
				{
					$sheet1 = $excel->getSheet($index);
					$new_sheet = $sheet1->copy();
					$new_sheet->setTitle(date('d',strtotime($kk)));
					$excel->addSheet($new_sheet);
					unset($new_sheet);
					$index++;
				}

				//REMOVE TEMPLATE
				$excel->removeSheetByIndex(0);	
				//REMOVE TEMPLATE - END

				//ADD SHEET OR INITIALIZE - END



				$index = 0;
				foreach($new_coll_array as $kk => $vv)
				{
					
					$COLLECTOR_RECEIPT = [];

					$GRAND_WATER_payment = 0;
					$GRAND_NON_WATER_payment = 0;
					$GRAND_TAX_VAL = 0;
					$GRAND_ADA_TTL = 0;
					$GRAND_CHECK_TTL = 0;
					$GRAND_CASH_TTL = 0;


					$WATER_payment = 0;
					$NON_WATER_payment = 0;
					$TAX_VAL = 0;
	
					$ADA_TTL = 0;
					$CHECK_TTL = 0;
					$CASH_TTL = 0;

					$sheet = $excel->getSheet($index);
					$sheet->setCellValue('A4', 'Daily Collection Report As of '. date('F d, Y',strtotime($kk)));//RES

					$row_6 = 6;

					foreach($vv as $nca)
					{
						$sheet->setCellValue('A'.$row_6, 'OR-0'.$nca->invoice_num);//RES

						//OVERIDE ON Column "A"
						$OR_TYP = 'OR';

						if(in_array($nca->status, ['cancel_cr', 'cancel_cr_nw', 'collector_receipt', 'cr_nw', '']))
						{
							$OR_TYP = 'CR';
							$sheet->setCellValue('A'.$row_6, $OR_TYP.'-0'.$nca->invoice_num.'');//RES
						}

						if( in_array($nca->status, ['cancel_cr_nw', 'or_nw', 'nw_cancel', 'cr_nw']))
						{
							$sheet->setCellValue('A'.$row_6, $OR_TYP.'-0'.$nca->invoice_num.' (NW)');//RES
						}


						// DESCRIPTIONS BY STATUS
						// DESCRIPTIONS BY STATUS
						// DESCRIPTIONS BY STATUS
						if( in_array($nca->status, ['active', 'or_nw']))
						{
							$sheet->setCellValue('B'.$row_6, substr(''.$nca->accounts->acct_no.' - '.$nca->accounts->lname.' '.$nca->accounts->fname, 0, 20));//RES
							$sheet->setCellValue('C'.$row_6, ''.$nca->payment);//RES
						}

						if( in_array($nca->status, ['cancel_cr', 'cancel_cr_nw', 'cancel_receipt', 'nw_cancel']))
						{
							$sheet->setCellValue('B'.$row_6, substr('CANCELED', 0, 20));//RES
							$row_6++;
							continue;
						}

						if( in_array($nca->status, ['collector_receipt', 'cr_nw']))
						{
							$sheet->setCellValue('B'.$row_6, substr('COLLECTOR RECEIPT', 0, 20));//RES

							$COLLECTOR_RECEIPT[] = $nca;

							$row_6++;
							continue;
						}



						if( in_array($nca->status, ['active']))
						{
							$WATER_payment += $nca->payment;
						}

						if( in_array($nca->status, ['or_nw']))
						{
							$NON_WATER_payment += $nca->payment;
						}

						if( in_array($nca->pay_type, ['ada']))
						{
							$ADA_TTL += $nca->payment;
						}
						if( in_array($nca->pay_type, ['cash']))
						{
							$CASH_TTL += $nca->payment;
						}
						if( in_array($nca->pay_type, ['check']))
						{
							$CHECK_TTL += $nca->payment;
						}


						// DESCRIPTIONS BY STATUS END
						// DESCRIPTIONS BY STATUS END
						// DESCRIPTIONS BY STATUS END

						if( $nca->tax_val > 0 ) 
						{
							$sheet->setCellValue('G'.$row_6, ''.$nca->tax_val);//RES
							$TAX_VAL += $nca->tax_val;
						}
		
						$pycy = json_decode( $nca->collect_report_ledger->pycy );
		
						// echo '<pre>';
						// print_r($pycy->py);
						// die();
		
						if( !empty($pycy)  && !in_array($nca->status, ['nw_cancel', 'cancel_cr_nw', 'cr_nw', 'or_nw']) )
						{
		
		
							if( round($pycy->py,2) > 0 )
							{
								$sheet->setCellValue('F'.$row_6, ''.round($pycy->py,2));
							}
		
							if( round($pycy->cy) > 0 )
							{
								$sheet->setCellValue('E'.$row_6, ''.round($pycy->cy,2));
							}
		
							if( round($pycy->cur) > 0 )
							{
								$sheet->setCellValue('D'.$row_6, ''.round($pycy->cur,2));
							}
		
							if( round($pycy->pen) > 0 )
							{
								$sheet->setCellValue('H'.$row_6, ''.round($pycy->pen,2));
							}
		
						}
		
						if( in_array($nca->status, ['nw_cancel', 'cancel_cr_nw', 'cr_nw', 'or_nw']) )
						{
							$sheet->setCellValue('I'.$row_6, ''.round($nca->payment,2));
						}
		
						$last_row = $row_6;


						$row_6++;
					}

					$sheet->setCellValue('A'.$row_6, 'SUB TOTAL');
					$sheet->setCellValue('B'.$row_6, '');
					$sheet->setCellValue('C'.$row_6, '=SUM(C6:C'.$last_row.')');
					$sheet->setCellValue('D'.$row_6, '=SUM(D6:D'.$last_row.')');
					$sheet->setCellValue('E'.$row_6, '=SUM(E6:E'.$last_row.')');
					$sheet->setCellValue('F'.$row_6, '=SUM(F6:F'.$last_row.')');
					$sheet->setCellValue('G'.$row_6, '=SUM(G6:G'.$last_row.')');
					$sheet->setCellValue('H'.$row_6, '=SUM(H6:H'.$last_row.')');
					$sheet->setCellValue('I'.$row_6, '=SUM(I6:I'.$last_row.')');

					$row_6++;
					$prev_row = $row_6-1;
					$sheet->setCellValue('C'.$row_6, '=D'.$prev_row.'+E'.$prev_row.'+F'.$prev_row.'+H'.$prev_row.'+I'.$prev_row.'');
					$row_6++;
					$row_6++;

					$sheet->setCellValue('A'.$row_6, 'OFFICIAL RECEIPT');
					$sheet->setCellValue('B'.$row_6, '');
					$sheet->setCellValue('C'.$row_6, '');
					$sheet->setCellValue('D'.$row_6, '');
					$sheet->setCellValue('E'.$row_6, '');
					$sheet->setCellValue('F'.$row_6, '');
					$sheet->setCellValue('G'.$row_6, '');
					$sheet->setCellValue('H'.$row_6, '');
					$sheet->setCellValue('I'.$row_6, '');
					$row_6++;
					$sheet->setCellValue('A'.$row_6, 'CASH');
					$sheet->setCellValue('B'.$row_6, 'CHECK');
					$sheet->setCellValue('C'.$row_6, 'ADA');
					$sheet->setCellValue('D'.$row_6, 'WATER');
					$sheet->setCellValue('E'.$row_6, 'NON-WATER');
					$sheet->setCellValue('F'.$row_6, 'W/TAX');
					$sheet->setCellValue('G'.$row_6, '');
					$sheet->setCellValue('H'.$row_6, '');
					$sheet->setCellValue('I'.$row_6, '');

					$row_6++;

					$sheet->setCellValue('A'.$row_6, $CASH_TTL.'');
					$sheet->setCellValue('B'.$row_6, $CHECK_TTL);
					$sheet->setCellValue('C'.$row_6, $ADA_TTL);
					$sheet->setCellValue('D'.$row_6, $WATER_payment.'');
					$sheet->setCellValue('E'.$row_6, $NON_WATER_payment.'');
					$sheet->setCellValue('F'.$row_6, $TAX_VAL.'');
					$sheet->setCellValue('G'.$row_6, '');
					$sheet->setCellValue('H'.$row_6, '');
					$sheet->setCellValue('I'.$row_6, '');
					$row_6++;
					$row_6++;
					$row_6++;

					$GRAND_WATER_payment += $WATER_payment;
					$GRAND_NON_WATER_payment += $NON_WATER_payment;
					$GRAND_TAX_VAL += $TAX_VAL;
					$GRAND_ADA_TTL += $ADA_TTL;
					$GRAND_CHECK_TTL += $CHECK_TTL;
					$GRAND_CASH_TTL += $CASH_TTL;					


					$WATER_payment = 0;
					$NON_WATER_payment = 0;
					$TAX_VAL = 0;
	
					$ADA_TTL = 0;
					$CHECK_TTL = 0;
					$CASH_TTL = 0;


					$sheet->setCellValue('A'.$row_6, 'COLLECTOR RECEIPT');

					$row_6++;

					$sheet->setCellValue('A'.$row_6, 'Receipt Number');
					$sheet->setCellValue('B'.$row_6, 'Payor');
					$sheet->setCellValue('C'.$row_6, 'Amount Collected');
					$sheet->setCellValue('D'.$row_6, 'Current');
					$sheet->setCellValue('E'.$row_6, 'Arrears (CY)');
					$sheet->setCellValue('F'.$row_6, 'Arrears (PY)');
					$sheet->setCellValue('G'.$row_6, 'W/Tax');
					$sheet->setCellValue('H'.$row_6, 'Penalty');
					$sheet->setCellValue('I'.$row_6, 'Amount');

					$row_6++;

					$start_cr_row = $row_6;
					if( !empty($COLLECTOR_RECEIPT) )
					{
						foreach($COLLECTOR_RECEIPT as $nca)
						{
							$OR_TYP = 'CR';
							$sheet->setCellValue('A'.$row_6, $OR_TYP.'-0'.$nca->invoice_num.'');//RES
							$sheet->setCellValue('B'.$row_6, substr(''.$nca->accounts->acct_no.' - '.$nca->accounts->lname.' '.$nca->accounts->fname, 0, 20));//RES
							$sheet->setCellValue('C'.$row_6, ''.$nca->payment);//RES
	
	
							if( $nca->tax_val > 0 ) 
							{
								$sheet->setCellValue('G'.$row_6, ''.$nca->tax_val);//RES
								$TAX_VAL += $nca->tax_val;
							}
			
							$pycy = json_decode( $nca->collect_report_ledger->pycy );
			
							// echo '<pre>';
							// print_r($pycy->py);
							// die();
			
							if( !empty($pycy)  && !in_array($nca->status, ['nw_cancel', 'cancel_cr_nw', 'cr_nw', 'or_nw']) )
							{
			
			
								if( round($pycy->py,2) > 0 )
								{
									$sheet->setCellValue('F'.$row_6, ''.round($pycy->py,2));
								}
			
								if( round($pycy->cy) > 0 )
								{
									$sheet->setCellValue('E'.$row_6, ''.round($pycy->cy,2));
								}
			
								if( round($pycy->cur) > 0 )
								{
									$sheet->setCellValue('D'.$row_6, ''.round($pycy->cur,2));
								}
			
								if( round($pycy->pen) > 0 )
								{
									$sheet->setCellValue('H'.$row_6, ''.round($pycy->pen,2));
								}
			
							}
			
							if( in_array($nca->status, ['nw_cancel', 'cancel_cr_nw', 'cr_nw', 'or_nw']) )
							{
								$sheet->setCellValue('I'.$row_6, ''.round($nca->payment,2));
							}




							if( in_array($nca->status, ['collector_receipt']))
							{
								$WATER_payment += $nca->payment;
							}
	
							if( in_array($nca->status, ['cr_nw']))
							{
								$NON_WATER_payment += $nca->payment;
							}
	
							if( in_array($nca->pay_type, ['ada']))
							{
								$ADA_TTL += $nca->payment;
							}
							if( in_array($nca->pay_type, ['cash']))
							{
								$CASH_TTL += $nca->payment;
							}
							if( in_array($nca->pay_type, ['check']))
							{
								$CHECK_TTL += $nca->payment;
							}							
	
	
	
							$last_row = $row_6;
	
							$row_6++;
						}
	
						$sheet->setCellValue('A'.$row_6, 'SUB TOTAL');
						$sheet->setCellValue('B'.$row_6, '');
						$sheet->setCellValue('C'.$row_6, '=SUM(C'.$start_cr_row.':C'.$last_row.')');
						$sheet->setCellValue('D'.$row_6, '=SUM(D'.$start_cr_row.':D'.$last_row.')');
						$sheet->setCellValue('E'.$row_6, '=SUM(E'.$start_cr_row.':E'.$last_row.')');
						$sheet->setCellValue('F'.$row_6, '=SUM(F'.$start_cr_row.':F'.$last_row.')');
						$sheet->setCellValue('G'.$row_6, '=SUM(G'.$start_cr_row.':G'.$last_row.')');
						$sheet->setCellValue('H'.$row_6, '=SUM(H'.$start_cr_row.':H'.$last_row.')');
						$sheet->setCellValue('I'.$row_6, '=SUM(I'.$start_cr_row.':I'.$last_row.')');							

					}

					$row_6++;
					$row_6++;
					$sheet->setCellValue('A'.$row_6, 'COLLECTOR RECEIPT');
					$row_6++;
					$sheet->setCellValue('A'.$row_6, 'CASH');
					$sheet->setCellValue('B'.$row_6, 'CHECK');
					$sheet->setCellValue('C'.$row_6, 'ADA');
					$sheet->setCellValue('D'.$row_6, 'WATER');
					$sheet->setCellValue('E'.$row_6, 'NON-WATER');
					$sheet->setCellValue('F'.$row_6, 'W/TAX');
					$sheet->setCellValue('G'.$row_6, '');
					$sheet->setCellValue('H'.$row_6, '');
					$sheet->setCellValue('I'.$row_6, '');
					$row_6++;

					$sheet->setCellValue('A'.$row_6, $CASH_TTL.'');
					$sheet->setCellValue('B'.$row_6, $CHECK_TTL);
					$sheet->setCellValue('C'.$row_6, $ADA_TTL);
					$sheet->setCellValue('D'.$row_6, $WATER_payment.'');
					$sheet->setCellValue('E'.$row_6, $NON_WATER_payment.'');
					$sheet->setCellValue('F'.$row_6, $TAX_VAL.'');
					$sheet->setCellValue('G'.$row_6, '');
					$sheet->setCellValue('H'.$row_6, '');
					$sheet->setCellValue('I'.$row_6, '');
					$row_6++;


					$GRAND_WATER_payment += $WATER_payment;
					$GRAND_NON_WATER_payment += $NON_WATER_payment;
					$GRAND_TAX_VAL += $TAX_VAL;
					$GRAND_ADA_TTL += $ADA_TTL;
					$GRAND_CHECK_TTL += $CHECK_TTL;
					$GRAND_CASH_TTL  += $CASH_TTL;					



					$sheet->setCellValue('A'.$row_6, 'SUMMARY');
					$row_6++;
					$sheet->setCellValue('A'.$row_6, 'CASH');
					$sheet->setCellValue('B'.$row_6, $GRAND_CASH_TTL.'');
					$row_6++;
					$sheet->setCellValue('A'.$row_6, 'CHECK');
					$sheet->setCellValue('B'.$row_6, $GRAND_CHECK_TTL.'');

					$sheet->setCellValue('C'.$row_6, 'WATER BILL');
					$sheet->setCellValue('D'.$row_6, $GRAND_WATER_payment.'');

					$row_6++;
					$sheet->setCellValue('A'.$row_6, 'ADA');
					$sheet->setCellValue('B'.$row_6, $GRAND_ADA_TTL.'');

					$sheet->setCellValue('C'.$row_6, 'NON-WATER BILL');
					$sheet->setCellValue('D'.$row_6, $GRAND_NON_WATER_payment.'');

					$row_6++;
					$sheet->setCellValue('A'.$row_6, 'SUB TOTAL');
					$sheet->setCellValue('B'.$row_6, ($GRAND_CASH_TTL+$GRAND_CHECK_TTL+$GRAND_ADA_TTL).'');

					$sheet->setCellValue('C'.$row_6, 'SUB TOTAL');
					$sheet->setCellValue('D'.$row_6, ($GRAND_NON_WATER_payment+$GRAND_WATER_payment).'');

					$row_6++;
					$row_6++;
					$sheet->setCellValue('A'.$row_6, 'GRAND TOTAL');
					$sheet->setCellValue('B'.$row_6, ($GRAND_CASH_TTL+$GRAND_CHECK_TTL+$GRAND_ADA_TTL).'');

					$row_6++;
					$row_6++;

					$sheet->mergeCells('A'.$row_6.':B'.$row_6);
					$sheet->mergeCells('C'.$row_6.':E'.$row_6);
					$sheet->mergeCells('G'.$row_6.':I'.$row_6);

					$sheet->setCellValue('A'.$row_6, "Prepared by: NALYN M. CORTEZ \n Teller");
					$sheet->setCellValue('C'.$row_6, "Checked / Verified by: MENCHEI BAUTISTA \n Cashier");
					$sheet->setCellValue('G'.$row_6, "Noted: JOSE HILARIO V. PANDILI JR. \n General Manager D");


					// $sheet->setCellValue('B'.$row_6, "Checked / Verified by: MENCHEI BAUTISTA \n Cashier");
					// $sheet->setCellValue('C'.$row_6, "
					// Noted: JOSE HILARIO V. PANDILI JR.
					// General Manager D
					// ");

					// $sheet2->cells('A'.$row_6.':I'.$row_6, function($cells) {
					// 	$cells->setBackground('#AAAAFF');
					// });

		
					$index++;
				}

				


				// $tempSheet = $excel->getSheet(0)->copy();
				// $tempSheet->setTitle('Jan 1');
				// $excel->addSheet($tempSheet);
				// unset($tempSheet);

			 


		})->download('xls');


	}// END




}// END
