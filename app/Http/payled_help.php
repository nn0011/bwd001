<?php

// die();

use App\PayPenLed;
use App\LedgerData;
use App\Collection;




// LOCAL FUNCTION 
// LOCAL FUNCTION 
// LOCAL FUNCTION 

function billing_adjustment_reff_bill($acct_id)
{
	$sql1 = "
			SELECT * FROM `ledger_datas`
			WHERE acct_id=? AND led_type='adjustment' AND bill_adj > 0	AND status='active'
	";	

	$sql2 = "
			SELECT * FROM `ledger_datas`
			WHERE acct_id=? AND led_type='billing' AND status='active' AND id < ?
			ORDER BY id DESC
			LIMIT 1
	";	

	$sql3 = "
			SELECT * FROM `ledger_datas`
			WHERE acct_id=? AND led_type='billing' AND status='active' AND id = ?
			ORDER BY id DESC
			LIMIT 1
	";	


	$all_adjustment = DB::select($sql1, [$acct_id]);

	$bill_adjust = [];
	foreach( $all_adjustment as $v1 ) {
		
		$bills = DB::select($sql2, [$acct_id, $v1->id]);

		if( !empty($v1->adj_reff) ) {
			$bills = DB::select($sql3, [$acct_id, $v1->adj_reff]);
		}

		foreach( $bills as $v2 ) {
			$bill_adjust[$v2->id] = $v1;
			break;
		}
	}//

	return $bill_adjust;
}//

function get_billings_top_10($acct_id)
{
	$sql1 = "
			SELECT * FROM `ledger_datas`
			WHERE acct_id=?  AND  led_type IN ('billing',  'beginning') AND status='active'
			ORDER BY `ledger_datas`.`id` DESC
			LIMIT 10;
		";

	return DB::select($sql1, [$acct_id]);
}//


function ff($va1){
	ff1($va1);
	die();
}

function ff1($va1){
	echo '<pre>';
	if( method_exists($va1, 'toArray') ) {
		print_r(@$va1->toArray());
	}else{
		print_r((array) $va1);
	}

	// try{
	// 	print_r(@$va1->toArray());
	// }catch(Exception $e){
	// 	print_r((array) $va1);
	// }
	echo '</pre>';
}//


function feb_05_2021_daily_col_break($acct_id, $curr_or, $date01, $cmd=1)
{

	$led01 = DB::select("
					SELECT MAX(id) my_id FROM `ledger_datas` where acct_id=?	
					AND led_type IN ('cancel_cr','payment_cancel', 'payment_cr', 'payment', 'adjustment')
					AND status='active'
					GROUP BY reff_no
			", [$acct_id]);

	$led02 = DB::select("
			SELECT id as my_id FROM ledger_datas where acct_id=? AND  led_type='wtax' AND status='active'
	", [$acct_id]);

	$ids_all =[];
	foreach($led01 as $mm){$ids_all[] = $mm->my_id;}
	foreach($led02 as $mm){$ids_all[] = $mm->my_id;}

	$led0_r1    = DB::table('ledger_datas')
					->whereIn('id', $ids_all)
					->orderBy('zort1','asc')
					->orderBy('id','asc')
					->get();

	$ttl_all_list = [];
	$all_credit1  = []; 
	$tobe_debit   = [];
	$or_number    = [];
	$or_number2   = [];

	foreach($led0_r1 as $mm)
	{
		if($mm->led_type == 'adjustment'){
			if($mm->bill_adj < 0 ){$tobe_debit[] = $mm->id;continue;}
			@$ttl_all_list['adj_'.$mm->reff_no] += (float) @$mm->bill_adj; 
			@$all_credit1[$mm->id] += (float) @$mm->bill_adj; 
			continue;
		}
		@$ttl_all_list[$mm->reff_no] += (float) @$mm->payment; 
		@$all_credit1[$mm->id] += (float) @$mm->payment;

		$or_number[$mm->id] = $mm->reff_no;
		$or_number2[$mm->reff_no] = $mm->id;
	}


	$adjust_utang_sql = "
				SELECT * FROM (SELECT id, acct_id,acct_num, status, date01, payment,bill_adj, PP1, (PP1+bill_adj) DD, reff_no, led_type FROM (
					SELECT led1.*, (SELECT led2.payment FROM ledger_datas led2 
						WHERE led1.reff_no=led2.reff_no 
						AND (led_type='or_nw' OR led_type='cr_nw')
						AND led2.acct_id=? AND status='active'
					LIMIT 1
					) PP1 FROM `ledger_datas` led1
					WHERE led1.led_type='or_nw_debit'  AND  led1.acct_id=?   AND status='active' ) TAB1) TAB2
					WHERE DD < 0
				";

	$adjust_utang = DB::select($adjust_utang_sql, [$acct_id, $acct_id]);
	$adjust_utang_deff = [];
	foreach($adjust_utang as $uts)
	{  
		$tobe_debit[] =  $uts->id;
		$adjust_utang_deff[$uts->id] =  $uts->DD;
	}



	$debit_arr  = ['beginning', 'billing', 'penalty'];
	$payble = DB::table('ledger_datas')
				->select(['led_type', 'arrear', 'billing', 'discount', 'penalty', 'date01', 'reff_no', 'id', 'zort1', 'bill_adj'])
				->where('acct_id', $acct_id)
				->whereIn('led_type', $debit_arr)
				->orWhereIn('id', $tobe_debit)
				->where('status', 'active')
				->orderBy('date01', 'asc')
				->orderBy('zort1', 'asc')
				->orderBy('id', 'asc')
				->get();
			

	$all_debit1 = [];
	$all_debit2 = [];
	foreach($payble as $kk => $vv){
		$my_amt = 0;
		if($vv->led_type == 'beginning'){$my_amt  = $vv->arrear;}
		if($vv->led_type == 'billing'){$my_amt    = $vv->billing - $vv->discount;}
		if($vv->led_type == 'penalty'){$my_amt    = $vv->penalty;}			
		if($vv->led_type == 'adjustment'){$my_amt = abs($vv->bill_adj);}		
		if($vv->led_type == 'or_nw_debit'){$my_amt = abs($adjust_utang_deff[$vv->id]);}			
		$all_debit1[$vv->id] = $my_amt;
		$all_debit2[$vv->id] = $vv->date01.'||'.$vv->led_type;
	}



	$xx=0;
	$break_down1 = [];
	$break_down2 = [];

	foreach($all_credit1 as $ck => $cv){

		foreach($all_debit1 as $dk=> $dv){
			
			if($cv <= 0){break;}
			if($dv <= 0){continue;}

			$pre_cv = $cv;

			$cv = round($cv-$dv, 2);

			if($cv >= 0){//FULL PAYMENT
				$all_debit1[$dk] = 0;
				$break_down2[$dk][] = $dv.'|'.$ck;
				$break_down1[$ck][] = $dv.'|'.$dk;
			}else{//PARTIAL PAYMENT
				$all_debit1[$dk] = $dv - $pre_cv;
				$break_down2[$dk][] = $pre_cv.'|'.$ck;
				$break_down1[$ck][] = $pre_cv.'|'.$dk;
				break;
			}

		}
		// if($xx >= 5){break;}
		$xx++;
	}

	if($cmd == 3){
		return 	compact('all_debit2','all_debit1');
	}



	$current_bill_dd = $date01;
	$curr_arrear_dd  = strtotime($date01);
	$PrevYearEnd = strtotime(date('Y-12-31', strtotime($current_bill_dd.' - 1 Year')));
	$curr_month01 = strtotime(date('Y-m-01',strtotime($date01)));


	$penalty  = 0;
	$curr_arr = 0;
	$prev_arr = 0;
	$curr_bil = 0;
	$nwb_bal  = 0;	
	
	try{	
		foreach($break_down1[$or_number2[$curr_or]] as $mm1)
		{
			
			$d1 = explode('|', $mm1);
			$d2 = explode('||', $all_debit2[$d1[1]]);

			if(trim($d2[1]) == 'penalty'){
				$penalty += round((float) $d1[0], 2);
			}

			if($d2[1] == 'billing' || $d2[1] == 'beginning')
			{
				$time_date_bill = strtotime($d2[0]);

				if($time_date_bill <= $PrevYearEnd){
					$prev_arr += round((float) $d1[0], 2);
				}elseif($time_date_bill < $curr_month01){
					$curr_arr += round((float) $d1[0], 2);
				}elseif($time_date_bill >= $curr_month01){
					$curr_bil += round((float) $d1[0], 2);
				}
			}

			if($d2[1] == 'or_nw_debit')
			{
				$nwb_bal += round((float) $d1[0], 2);
			}		

		}
	}catch(Exception $ee){
		// echo '<pre>';
		// print_r($break_down1);
		// echo '<br />';
		// echo $curr_or;
		// die();

	}	


	if($cmd == 2){

		// echo '<pre>';
		// print_r($break_down1);
		// die();
		return 	compact('break_down1', 'all_debit2', 'or_number2');
	}


	return [
		'pen' => $penalty,
		'bil' => $curr_bil,
		'py' => $prev_arr,
		'cy' => $curr_arr,
		'nwb' => $nwb_bal		
	];
		

}



function dec272020_delete_pay_led_by_coll_id($coll_id)
{
	PayPenLed::where('cid', $coll_id)->delete();
}

function nov252020__remove_zero_and_less(&$remain_bal)
{
	foreach($remain_bal as $kk => $rb1){
		if($rb1['amt'] <= 0){
			unset($remain_bal[$kk]);
		}
	}
}

function nov252020__subtract_adjusments(&$remain_bal, &$adjust_insert, $my_adjust_vv)
{
	
	$ttl_adjustment = $my_adjust_vv['amt'];

	foreach($remain_bal as $kk => $rb1)
	{
		if($ttl_adjustment <= 0){continue;}
		
		if($ttl_adjustment > $rb1['amt']){
			$ttl_adjustment -= $rb1['amt'];
			$remain_bal[$kk]['amt'] = 0;
			
			$adjust_insert[] = array(
							'reff_no' => $rb1['reff_no'], 
							'typ'     => $rb1['typ'], 
							'date01'  => $my_adjust_vv['date01'], 
							'amt'     => $rb1['amt'], 
							'adj_id'  => $my_adjust_vv['reff_no'] 
						);
			
		}elseif($ttl_adjustment < $rb1['amt']){
			$remain_bal[$kk]['amt'] -= $ttl_adjustment;
			
			$adjust_insert[] = array(
							'reff_no' => $rb1['reff_no'], 
							'typ'     => $rb1['typ'], 
							'date01'  => $my_adjust_vv['date01'], 
							'amt'     => $ttl_adjustment, 
							'adj_id'  => $my_adjust_vv['reff_no'] 
						);
			
			$ttl_adjustment = 0;
			break;
		}
	}

	// echo '<pre>';
	// print_r($adjust_insert);
	// die();	
	
}

function nov252020__penalty_process($payment_made, $remain_bal, $acct_id)
{
	// $pen_reserve = round($payment_made * 0.1, 2); // 10% for Penalty
	$pen_reserve = round($payment_made, 2); // 
	$pen_payed   = [];
	$pen_payed_x = 0;
	$pen_remain  = 0;
	$has_pen     = 0;
	
	foreach($remain_bal as $rb1)
	{
		if($rb1['typ'] == 'penalty')
		{
			$has_pen++;
			
			$pen_payed[$pen_payed_x]['pen_id']  = $rb1['reff_no'];
			$pen_payed[$pen_payed_x]['acct_id'] = $acct_id;
			
			if(($rb1['amt'] - $pen_reserve) <= 0){
				$pen_payed[$pen_payed_x]['amt'] = $rb1['amt'];
			}else{
				$pen_payed[$pen_payed_x]['amt'] = $pen_reserve;
			}
			
			$pen_remain = $pen_reserve - $pen_payed[$pen_payed_x]['amt'];
			
			$pen_reserve -= $rb1['amt'];
			if($pen_reserve <= 0){
				break;
			}
		}
		
		$pen_payed_x++;
	}
	
	return compact('has_pen', 'pen_remain', 'pen_reserve', 'pen_payed');
}//ENDFUNC		
		
function nov252020__billing_and_beginning_process($payment_made, $remain_bal, $acct_id, $pen_remain)
{		
		
		// print_r($remain_bal);
		// $pen_reserve1      = round($payment_made * 0.1, 2);
		$pen_reserve1      = round($payment_made, 2);
		$pay_made_les_pen  = $payment_made - $pen_reserve1;
		$pay_made_remain1  = $pay_made_les_pen + $pen_remain ;
		
		// $pay_made_remain1  = $payment_made ;//NO  PENALTY 10%
		
		$begin_payed   = [];
		$begin_payed_x = 0;
		foreach($remain_bal as $rb1)
		{
			if($pay_made_remain1 <= 0){
				break;
			}						
			
			if($rb1['typ'] == 'beginning')
			{
				$begin_payed[$begin_payed_x]['acct_id'] = $acct_id;
				$begin_payed[$begin_payed_x]['beg_id']  = $rb1['id'];
				
				if(($rb1['amt'] - $pay_made_remain1) <= 0){
					$begin_payed[$begin_payed_x]['amt'] = $rb1['amt'];
				}else{
					$begin_payed[$begin_payed_x]['amt'] = $pay_made_remain1;
				}
				
				$pay_made_remain1 -= $rb1['amt'];
				
				if($pay_made_remain1 <= 0){
					break;
				}						
			}
			
			$begin_payed_x++;
		}

		
		
		$bill_payed   = [];
		$bill_payed_x = 0;

		$pen_payed   = [];
		$pen_payed_x = 0;

		$my_all_pay  = [];
		$my_all_pay_x = 0;

		foreach($remain_bal as $rb1)
		{
			if($pay_made_remain1 <= 0)
			{
				break;
			}						
			
			if($rb1['typ'] == 'billing')
			{
				$bill_payed[$bill_payed_x]['acct_id'] = $acct_id;
				$bill_payed[$bill_payed_x]['bill_id']   = $rb1['reff_no'];
				
				if(($rb1['amt'] - $pay_made_remain1) <= 0){
					$bill_payed[$bill_payed_x]['amt'] = $rb1['amt'];
				}else{
					$bill_payed[$bill_payed_x]['amt'] = $pay_made_remain1;
				}
				
				$pay_made_remain1 -= $rb1['amt'];
				
				$bill_payed[$bill_payed_x]['typ1'] = 'bill';
				$my_all_pay[$my_all_pay_x] = $bill_payed[$bill_payed_x];
				
				if($pay_made_remain1 <= 0){
					break;
				}					
			}

			if($rb1['typ'] == 'penalty')
			{
				$pen_payed[$pen_payed_x]['pen_id']  = $rb1['reff_no'];
				$pen_payed[$pen_payed_x]['acct_id'] = $acct_id;

				if(($rb1['amt'] - $pay_made_remain1) <= 0){
					$pen_payed[$pen_payed_x]['amt'] = $rb1['amt'];
				}else{
					$pen_payed[$pen_payed_x]['amt'] = $pay_made_remain1;
				}
				$pay_made_remain1 -= $rb1['amt'];

				$pen_payed[$pen_payed_x]['typ1'] = 'fine';
				$my_all_pay[$my_all_pay_x] = $pen_payed[$pen_payed_x];

				if($pay_made_remain1 <= 0){
					break;
				}			
			}

			
			$bill_payed_x++;
			$pen_payed_x++;
			$my_all_pay_x++;
		}
		
		return compact('bill_payed', 'begin_payed', 'pen_payed', 'my_all_pay');
		
}//		

function nov252020__debug_insert_list($acct_id, $bill_payed, $begin_payed, $pen_payed)
{
		
		$insert_list = array();

		$cid    = 0;
		
		foreach($pen_payed as $pp001)
		{
			$pen_id = $pp001['pen_id'];
			$amt    = $pp001['amt'];
			
			$insert_list[]  =  array(
								'uid'=> $acct_id, 
								'cid'=> $cid, 
								'pen_id'=> $pen_id, 
								'amt' => $amt,
								'typ' => 'penalty'
							);
		}

		foreach($begin_payed as $pp001)
		{
			$pen_id = $pp001['beg_id'];
			$amt    = $pp001['amt'];

			$insert_list[]  =  array(
								'uid'=> $acct_id, 
								'cid'=> $cid, 
								'pen_id'=> $pen_id, 
								'amt' => $amt,
								'typ' => 'beginning'
							);

		}

		
		foreach($bill_payed as $pp001)
		{
			$pen_id = $pp001['bill_id'];
			$amt    = $pp001['amt'];

			$insert_list[]  =  array(
								'uid'=> $acct_id, 
								'cid'=> $cid, 
								'pen_id'=> $pen_id, 
								'amt' => $amt,
								'typ' => 'billing'
							);

		}


		
		echo '<pre>';
		print_r($insert_list);
		//~ print_r($remain_bal);
		die();

}
		
function nov252020__insert_payment_breakdown($acct_id, $cid, $all_payed, $coll_info)		
{
		extract($all_payed);//pen_payed,begin_payed,bill_payed,my_all_pay
		//PayPenLed
		$insert_list = array();

		//~ $cid    = $new_col->id;
		//~ $cid    = 0;
		$payment_date = $coll_info->payment_date;
		
		foreach($begin_payed as $pp001)
		{
			$pen_id = $pp001['beg_id'];
			$amt    = $pp001['amt'];

			$insert_list[]  =  array(
								'uid'=> $acct_id, 
								'cid'=> $cid, 
								'pen_id'=> $pen_id, 
								'amt' => $amt,
								'typ' => 'beginning',
								'date11' => $payment_date
							);

		}

		/*
		foreach($pen_payed as $pp001)
		{
			$pen_id = $pp001['pen_id'];
			$amt    = $pp001['amt'];
			
			$insert_list[]  =  array(
								'uid'=> $acct_id, 
								'cid'=> $cid, 
								'pen_id'=> $pen_id, 
								'amt' => $amt,
								'typ' => 'penalty',
								'date11' => $payment_date
							);
		}
		
		
		foreach($bill_payed as $pp001)
		{
			$pen_id = $pp001['bill_id'];
			$amt    = $pp001['amt'];

			$insert_list[]  =  array(
								'uid'=> $acct_id, 
								'cid'=> $cid, 
								'pen_id'=> $pen_id, 
								'amt' => $amt,
								'typ' => 'billing',
								'date11' => $payment_date
							);

		}
		*/

		foreach($my_all_pay as $pp001)
		{
			if($pp001['typ1'] == 'bill')
			{
				$pen_id = $pp001['bill_id'];
				$amt    = $pp001['amt'];
	
				$insert_list[]  =  array(
									'uid'=> $acct_id, 
									'cid'=> $cid, 
									'pen_id'=> $pen_id, 
									'amt' => $amt,
									'typ' => 'billing',
									'date11' => $payment_date
								);
			}else{

				$pen_id = $pp001['pen_id'];
				$amt    = $pp001['amt'];
				
				$insert_list[]  =  array(
									'uid'=> $acct_id, 
									'cid'=> $cid, 
									'pen_id'=> $pen_id, 
									'amt' => $amt,
									'typ' => 'penalty',
									'date11' => $payment_date
								);				

			}

		}		

		
		PayPenLed::insert($insert_list);
}//Endfunc

function feb022021__payable_ledger101($payable_ledgers, $acct_id, &$payment_made, &$r1)
{
		$my_adjust  = [];
		$remain_bal = [];
		$remain_xx  = 0;


		foreach($payable_ledgers as $pl1)
		{
			if($pl1->led_type == 'adjustment')
			{

				if($pl1->bill_adj >= 0){

					$adjust_001  = PayPenLed::where('uid', $acct_id)
										->where('status', 'active')
											->where('adj_id', $pl1->reff_no)
												->sum('amt');
				}else{


				}
				
				/*

				$amt01 = round($pl1->bill_adj - $exist_penalty, 2);

				$my_adjust[$remain_xx]['id']      = $pl1->id;
				$my_adjust[$remain_xx]['amt']     = $amt01;
				$my_adjust[$remain_xx]['typ']     = $pl1->led_type;
				$my_adjust[$remain_xx]['date01']  = $pl1->date01;
				$my_adjust[$remain_xx]['reff_no'] = $pl1->reff_no;
				
				*/
			}			
		}

		echo '<pre>';
		print_r($payable_ledgers->toArray());
		die();

		foreach($payable_ledgers as $pl1)
		{
			
			if($pl1->led_type == 'beginning')
			{
				if($pl1->arrear <= 0){continue;}
				
				$exist_beginning  = PayPenLed::where('uid', $acct_id)
										->where('status', 'active')
											->where('typ', 'beginning')
												->sum('amt');

				if($exist_beginning >= $pl1->arrear){continue;}

				// $amt01 = ($payment_made < $pl1->arrear)?$payment_made:$pl1->arrear;
				$amt01 = ($payment_made < $pl1->arrear)?$pl1->arrear:$payment_made;

				$pen_id = $pl1->id;
				$amt    = $amt01;
	
				$insert_list001[]  =  array(
									'uid'=> $acct_id, 
									'cid'=> $r1->id, 
									'pen_id'=> $pen_id, 
									'amt' => $amt,
									'typ' => 'beginning',
									'date11' => $pl1->date01
								);
				
				PayPenLed::insert($insert_list001);
				$payment_made = $payment_made - $amt01;
				// if($payment_made <= 0){break;}
				dd(1);

			}//@@@@@@@@@@@@@@@@@@
			
			if($pl1->led_type == 'billing')
			{

				$ttl_billing = $pl1->billing - $pl1->discount;

				$exist_billing = PayPenLed::where('uid', $acct_id)
										->where('status', 'active')
											->where('typ', 'billing')
												->where('pen_id', $pl1->reff_no)
													->sum('amt');

				// if($ttl_billing  >= $exist_billing){continue;}
				if($exist_billing  >= $ttl_billing){continue;}

				// $amt01 = ($payment_made < $ttl_billing)?$payment_made:$ttl_billing;
				$amt01 = ($payment_made < $ttl_billing)?$ttl_billing:$payment_made;

				$pen_id = $pl1->reff_no;
				$amt    = $amt01;
	
				$insert_list001[]  =  array(
									'uid'=> $acct_id, 
									'cid'=> $r1->id, 
									'pen_id'=> $pen_id, 
									'amt' => $amt,
									'typ' => 'billing',
									'date11' => $pl1->date01
								);

				PayPenLed::insert($insert_list001);
				$payment_made = $payment_made - $amt01;
				// if($payment_made <= 0){break;}

			}//
			
			if($pl1->led_type == 'penalty')
			{
				$ttl_penalty = $pl1->penalty;

				$exist_penalty  = PayPenLed::where('uid', $acct_id)
									->where('status', 'active')
										->where('typ', 'penalty')
											->where('pen_id', $pl1->reff_no)
												->sum('amt');

				// if($ttl_penalty  >= $exist_penalty){continue;}
				if($exist_penalty  >= $ttl_penalty){continue;}

				// $amt01  = ($payment_made < $ttl_penalty)?$payment_made:$ttl_penalty;
				$amt01  = ($payment_made < $ttl_penalty)?$ttl_penalty:$payment_made;

				$pen_id = $pl1->reff_no;
				$amt    = $amt01;

				$insert_list001[]  =  array(
					'uid'=> $acct_id, 
					'cid'=> $r1->id, 
					'pen_id'=> $pen_id, 
					'amt' => $amt,
					'typ' => 'penalty',
					'date11' => $pl1->date01
				);

				PayPenLed::insert($insert_list001);
				$payment_made = $payment_made - $amt01;
				// if($payment_made <= 0){break;}		
			}
			


			if($payment_made <= 0){break;}
			
		}
		
		
		// echo '<pre>';
		// print_r($remain_bal);
		// echo 'AAAA';
		// die();		
		
		return compact('my_adjust', 'remain_bal');
			
}//END

function nov252020__payable_ledger101($payable_ledgers, $acct_id, &$payment_made)
{
		$my_adjust  = [];
		$remain_bal = [];
		$remain_xx  = 0;

		foreach($payable_ledgers as $pl1)
		{
			
			if($pl1->led_type == 'beginning')
			{
				
				$exist_beginning  = PayPenLed::where('uid', $acct_id)
										->where('status', 'active')
											->where('typ', 'beginning')
												->sum('amt');
																
				$remain_bal[$remain_xx]['id']      = $pl1->id;
				$remain_bal[$remain_xx]['amt']     = round($pl1->arrear - $exist_beginning, 2);
				$remain_bal[$remain_xx]['typ']     = $pl1->led_type;
				$remain_bal[$remain_xx]['date01']  = $pl1->date01;
				$remain_bal[$remain_xx]['reff_no'] = $pl1->reff_no;
			}
			
			if($pl1->led_type == 'billing')
			{

				$ttl_billing = $pl1->billing - $pl1->discount;

				if($pl1->arrear < 0)
				{
					
					$my_arrear = abs($pl1->arrear);

					if($my_arrear > $ttl_billing){
						$my_arrear = $ttl_billing;
					}

					$over_pay  = PayPenLed::where('uid', $acct_id)
										->where('status', 'active')
											->where('typ', 'over_pay')
												->where('pen_id', $pl1->reff_no)
													->first();
					if(!$over_pay)
					{
						$new_over_pay         = new PayPenLed;
						$new_over_pay->uid    = $acct_id;

						$new_over_pay->status = 'active';
						$new_over_pay->typ    = 'over_pay';
						$new_over_pay->pen_id = $pl1->reff_no;
						$new_over_pay->date11 = $pl1->date01;
						$new_over_pay->amt    = $my_arrear;
						$new_over_pay->save();
					}
				}

				$exist_billing  = PayPenLed::where('uid', $acct_id)
										->where('status', 'active')
											->where(function($q1){
												$q1->orWhere('typ', 'billing');
												$q1->orWhere('typ', 'over_pay');
											})
											->where('pen_id', $pl1->reff_no)
													->sum('amt');
				

				$remain_bal[$remain_xx]['id']      = $pl1->id;
				$remain_bal[$remain_xx]['amt']     = round($ttl_billing - $exist_billing, 2);
				$remain_bal[$remain_xx]['typ']     = $pl1->led_type;
				$remain_bal[$remain_xx]['date01']  = $pl1->date01;
				$remain_bal[$remain_xx]['reff_no'] = $pl1->reff_no;
			}//
			
			if($pl1->led_type == 'penalty')
			{
				$exist_penalty  = PayPenLed::where('uid', $acct_id)
													->where('status', 'active')
														->where('typ', 'penalty')
															->where('pen_id', $pl1->reff_no)
																->sum('amt');
													
				$remain_bal[$remain_xx]['id']      = $pl1->id;
				$remain_bal[$remain_xx]['amt']     = round($pl1->penalty - $exist_penalty, 2);
				$remain_bal[$remain_xx]['typ']     = $pl1->led_type;
				$remain_bal[$remain_xx]['date01']  = $pl1->date01;
				$remain_bal[$remain_xx]['reff_no'] = $pl1->reff_no;
			}
			
			if($pl1->led_type == 'adjustment')
			{

				// $payment_made += @$pl1->bill_adj;

				// if($pl1->bill_adj >= 0){
				// 	$payment_made += $pl1->bill_adj;
				// }else{
				// }
				
				/*
				$exist_penalty  = PayPenLed::where('uid', $acct_id)
										->where('status', 'active')
											->where('adj_id', $pl1->reff_no)
												->sum('amt');
				
				$amt01 = round($pl1->bill_adj - $exist_penalty, 2);

				$my_adjust[$remain_xx]['id']      = $pl1->id;
				$my_adjust[$remain_xx]['amt']     = $amt01;
				$my_adjust[$remain_xx]['typ']     = $pl1->led_type;
				$my_adjust[$remain_xx]['date01']  = $pl1->date01;
				$my_adjust[$remain_xx]['reff_no'] = $pl1->reff_no;
				
				
				*/
			}
			
			$remain_xx++;
		}
		
		
		// echo '<pre>';
		// print_r($remain_bal);
		//~ die();		
		
		return compact('my_adjust', 'remain_bal');
			
}//END

function dec082020__insert_adjustments($adjust_insert, $acct_id)
{
	if(!empty($adjust_insert))
	{
		
		$insert_adj_list = [];
		foreach($adjust_insert as $ai){
			
			$insert_adj_list[]  =  array(
								'uid'=> $acct_id, 
								'cid'=> 0, 
								'pen_id'=> $ai['reff_no'], 
								'amt' => $ai['amt'],
								'typ' => $ai['typ'],
								'adj_id' => $ai['adj_id'],
								'date11' => $ai['date01']
							);					
		}
		
		if(!empty($insert_adj_list)){

			// echo '<pre>';
			// print_r($insert_adj_list);
			// die();

			PayPenLed::insert($insert_adj_list);
		}
	}
	
}//

function dec082020__execute_payled_breakdown($gg_vars, $payable_only=false)// compact('acct_id', 'payable_ledgers', 'payment_made', 'coll_info');
{
	extract($gg_vars);

	$var001 = nov252020__payable_ledger101($payable_ledgers, $acct_id, $payment_made);		
	extract($var001); //compact('my_adjust', 'remain_bal')



	nov252020__remove_zero_and_less($remain_bal);
	nov252020__remove_zero_and_less($my_adjust);

	$remain_bal    = array_reverse($remain_bal);//REVERSE
	$adjust_insert = [];

	foreach($my_adjust as $kk => $rb1){
		nov252020__subtract_adjusments($remain_bal,$adjust_insert, $rb1);
	}
	
	nov252020__remove_zero_and_less($remain_bal);

	//INSERT PART   INSERT PART  INSERT PART 
	dec082020__insert_adjustments($adjust_insert, $acct_id);			

	$remain_bal = array_reverse($remain_bal);//REVERSE
	
	if($payable_only ==true){
		return $remain_bal;
	}


	// $var001 = nov252020__penalty_process($payment_made, $remain_bal, $acct_id);
	// extract($var001);//compact('has_pen', 'pen_remain', 'pen_reserve', 'pen_payed')
	// if($has_pen<=0){$pen_remain = $pen_reserve;}
	
	// if($acct_id == '1021'){
	// 	print_r($remain_bal);
	// 	die();
	// }

	$pen_remain = $payment_made;
	$var001 = nov252020__billing_and_beginning_process($payment_made, $remain_bal, $acct_id, $pen_remain);
	extract($var001);//'bill_payed', 'begin_payed', 'pen_payed', 'my_all_pay'

	$all_payed = compact('pen_payed', 'bill_payed', 'begin_payed', 'my_all_pay');
	
	return $all_payed;
}//

function dec132020_get_remain_bal($acct_id, $date1=null)
{
	if(empty($date1)){
		$date1 = date('Y-m-d');
	}
	
	$led_types = array('beginning', 'billing', 'penalty', 'adjustment');
	$payable_ledgers  = LedgerData::whereIn('led_type', $led_types)
							->where('status', 'active')
							->where('date01','<=', $date1)
							->where('acct_id', $acct_id)
							->orderBy('date01', 'asc')
							->orderBy('zort1', 'asc')
								->orderBy('id', 'asc')
								->get();
	$payment_made = 0;
	$gg_vars   = compact('acct_id', 'payable_ledgers', 'payment_made');											
	$remain_bal = dec082020__execute_payled_breakdown($gg_vars, true);//Remaining balance only
		
	return $remain_bal;
}//



function  Dec142020___get_collection_total_information($dd)
{

		$last_collection1 = Collection::where('payment_date', '<', $dd)
								->orderBy('id','desc')
									->first();
		
		$prev_collection = date('Y-m-d', strtotime(@$last_collection1->payment_date));
		
		$sql1 = "
		
			SELECT SUM(payment) ttl_pp FROM (
				SELECT * FROM collections
				WHERE id IN (
					SELECT MAX(id) FROM collections 
					WHERE payment_date >= '$prev_collection 12:00:00'
					AND   payment_date <= '$prev_collection 18:00:00'
					GROUP BY invoice_num
				)
			) TAB1
			WHERE status IN ('active','collector_receipt','or_nw','cr_nw')
		";
		
		$rrl1 = DB::select($sql1);
		$undeposited_coll = $rrl1[0]->ttl_pp;
		
		
		$sql1 = "
		
			SELECT SUM(payment) ttl_pp FROM (
				SELECT * FROM collections
				WHERE id IN (
					SELECT MAX(id) FROM collections 
					WHERE payment_date < '$dd 12:00:00'
					AND   payment_date >= '$dd 6:00:00'
					GROUP BY invoice_num
				)
			) TAB1
			WHERE status IN ('active','collector_receipt','or_nw','cr_nw')
		";
		
		$rrl1 = DB::select($sql1);
		$morning_coll = $rrl1[0]->ttl_pp;	
		
		$sql1 = "
		
			SELECT SUM(payment) ttl_pp FROM (
				SELECT * FROM collections
				WHERE id IN (
					SELECT MAX(id) FROM collections 
					WHERE payment_date >= '$dd 12:00:00'
					AND   payment_date <= '$dd 18:00:00'
					GROUP BY invoice_num
				)
			) TAB1
			WHERE status IN ('active','collector_receipt','or_nw','cr_nw')
		";		
		
		$rrl1 = DB::select($sql1);
		$afternoon_coll = $rrl1[0]->ttl_pp;
		
		
		$sql1 = "
				SELECT MAX(MMM) OR_MAX, MIN(MMM) OR_MIN FROM 
				(
					SELECT *, CAST(invoice_num as UNSIGNED  ) MMM FROM (
						SELECT * FROM collections
						WHERE id IN (
							SELECT MAX(id) FROM collections 
							WHERE payment_date >= '$dd 06:00:00'
							AND   payment_date <= '$dd 18:00:00'
							GROUP BY invoice_num
						)
					) TAB1
					WHERE status IN ('active','collector_receipt','or_nw','cr_nw')
				) TAB2
					
		";					
		$rrl1 = DB::select($sql1);
		$or_min_max = $rrl1[0];
		
		return compact(
					'undeposited_coll',
					'morning_coll',
					'afternoon_coll',
					'or_min_max',
					'prev_collection',
					'dd'
				);

}//ENDFUNC

function Dec292020____get_payment_breakdown($acct_id, $date1=null)
{
	$res1 = dec132020_get_remain_bal($acct_id, $date1);

	$payment_breakdown = array();
	$x = 0;

	foreach($res1 as $r1)
	{
		$payment_breakdown[$x]['desc']    = date('M Y', strtotime($r1['date01'])).'  -  '.$r1['typ'];
		$payment_breakdown[$x]['pre_val'] = $r1['amt'];
		$x++;
	}

	// echo '<pre>';
	// print_r($payment_breakdown);
	// die();

	return $payment_breakdown;
}

		
		
//~ Route::get('/collection_ledger_update_dec252020', 'HwdJobCtrl@collection_ledger_update_dec252020');//Begining Active
