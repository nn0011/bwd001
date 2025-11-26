<?php

// echo '<pre>';
// print_r($led001->toArray());
// die();

$_SESSION["my_csrf"]=uniqid();

?>
<ul class="tabview_cmd">
	<li onclick="ledger_1('tabview1', 'tt1')"  class="tt1 active">Account Ledger</li>
	<li onclick="ledger_1('tabview2', 'tt2')"  class="tt2">History</li>
	<li onclick="ledger_1('tabview3', 'tt3')"  class="tt3">Reading Ledger</li>
	<li onclick="ledger_1('tabview4', 'tt4')"  class="tt4">NW-Billing Ledger</li>
</ul>




<div class="tab1 tabview4  tabview"  style="display:none; margin-top:30px;">


	<div style="text-align:right;padding-top:30px;padding-bottom:50px;">
		<a href="/billing/account_ledger/get_ledger_acct/print_pdf_reading?acct_id=<?php echo $acct_id; ?>"  target="_blank">AAAA</a>
	</div>

</div>



<div class="tab1 tabview3  tabview"  style="display:none; margin-top:30px;">
	<table  class="led01 acct_ledger01  table10">
		<tr  class="headings">
			<td>READING DATE</td>
			<td>METER #</td>
			<td>PERIOD</td>
			<td>PREVIOUS READING</td>
			<td>CURRENT READING</td>
			<td>C.U. METER</td>
		</tr>
		<?php foreach($reading12 as $rr1): ?>
		<tr>
			<td><?php echo $rr1->curr_read_date;  ?></td>
			<td><?php echo $rr1->meter_number;  ?></td>
			<td><?php echo date('F Y',strtotime($rr1->period)); ?></td>
			<td><?php echo $rr1->prev_reading;  ?></td>
			<td><?php echo $rr1->curr_reading;  ?></td>
			<td><?php echo $rr1->current_consump;  ?></td>
		</tr>
		<?php endforeach; ?>
	</table>

	<div style="text-align:right;padding-top:30px;padding-bottom:50px;">
		<a href="/billing/account_ledger/get_ledger_acct/print_pdf_reading?acct_id=<?php echo $acct_id; ?>"  target="_blank">Print Reading Ledger</a>
	</div>

</div>



<div  class="tab1  tabview2 tabview"  style="display:none; margin-top:30px;">

	<table  class="acct_ledger01">

		<tr>
			<td width="20%">Date</td>
			<td width="40%">Description</td>
			<td width="40%">Information</td>
		</tr>

		<?php  foreach($ledger_list1 as $acct): ?>
			<tr>
				<td><?php echo date('F d, Y @ H:iA', strtotime($acct->led_date2)); ?></td>
				<td>
					<p><?php echo $acct->led_title; ?></p>
				</td>
				<td>
					<?php echo $acct->led_desc1; ?>
				</td>
			</tr>
		<?php endforeach;  ?>

	</table>

	<div style="text-align:right;padding-top:30px;padding-bottom:50px;">
		<a href="/billing/account_ledger/get_ledger_acct/print_pdf_history?acct_id=<?php echo $acct_id; ?>"  target="_blank">Print History</a>
	</div>


</div>


<div class="tab1 tabview1  tabview">

	<table  class="led01 acct_ledger01  table10">
		
		
		<tr  class="headings">
			<?php /*
			<td style="width:100px;">DATE</td>
			<td>PARTICULARS</td>
			<td>REFERENCE NO.</td>
			<td style="width:100px;">PERIOD COVERED</td>
			<td>PREVIOUS READING</td>
			<td>CURRENT READING</td>
			<td>CONSUMPTION</td>
			<td>BILLING AMOUNT</td>
			<td>ARREAR</td>
			<td>DISCOUNT</td>
			<td>PENALTY</td>
			<td>ADJUSTMENT</td>
			<td>PAYMENT</td>
			<td>BALANCE</td>
			*/ ?>
			
			<td class="r1">DATE</td>
			<td class="r2">PARTICULAR</td>
			<td class="r3">REFF</td>
			<td class="r4">PRV</td>
			<td class="r5">CUR</td>
			<td class="r6">C.u.M</td>
			<td class="r7">DEBIT</td>
			<td class="r8">CREDIT</td>
			<td class="r9">BALANCE</td>
			
		</tr>
		
		
		

		<?php


		foreach($led001 as $ll):


				
				$ttl1 = $ll->ttl_bal;
				
				
				$bill_num = '';

				if($ll->led_type == 'billing'){
					$bill_num = get_billing_number($ll->reff_no);					
				}
								
				
				if($ll->led_type == 'billing'  && $ll->discount > 0  ){
					//~ $ttl1  = $ll->billing; 
				}
				
				
				$reading_info = array(
					'prev3' =>'',
					'curr3' =>$ll->reading,
					'cons3' => $ll->consump
				);
				if($ll->led_type == 'billing'){
					$rr3 = getReadingByPeriod($ll->acct_id, $ll->period);
					$reading_info['prev3']= @$rr3->prev_reading;
				}
				
				
				
				$A_DATE    = '';
				$A_PAR     = $ll->id.'<br />'.part_name1($ll->led_type);
				$A_REFF    = $ll->reff_no.'<br />'.$bill_num;
				$A_PERIOD  = '';
				$A_PREV    = $reading_info['prev3'];
				$A_CUR     = $reading_info['curr3'];
				$A_CON     = $reading_info['cons3'];
				$A_BILL    = '';
				$A_ARR 	   = '';
				$A_DIS     = '';
				$A_PEN     = '';
				$A_ADJ     = '';
				$A_PAY     = '';
				$A_BAL     = '';
				
				
				
				if($ll->led_type == 'penalty')
				{
					//~ $A_DATE = date('m/d/Y', strtotime($ll->created_at)); 
					$A_DATE = date('m/d/Y', strtotime($ll->date01)); 
				}else{
					$A_DATE = date('m/d/Y', strtotime($ll->date01)); 
				}
				
				
				if($ll->led_type == 'billing'){
					$A_PERIOD.= date('Y/m/d', strtotime(@$ll->date01.' -1 month' )); 
					$A_PERIOD.= ' - ';
					$A_PERIOD.= date('Y/m/d', strtotime(@$ll->date01)); 
				}
				
				if(!empty($ll->billing)){
					$A_BILL  .= number_format($ll->billing, 2); 
				}

				if(!empty($ll->arrear)){
					$A_ARR = number_format($ll->arrear, 2); 
				}
				
				if(!empty($ll->penalty)){
					$A_PEN = number_format($ll->penalty, 2); 
				}
				
				if(!empty($ll->bill_adj)){
					$A_ADJ = number_format($ll->bill_adj, 2); 
				}
			
				if(!empty($ll->payment)){
					$A_PAY = number_format($ll->payment, 2); 
				}
				
				$A_ADJD = '';
				$A_ADJC = '';
				
				if(!empty($ll->bill_adj))
				//~ if($ll->led_type=='adjustment')
				{
					//$A_ADJ = number_format($ll->bill_adj, 2);
					if($ll->bill_adj <= 0){$A_ADJD = number_format(abs($ll->bill_adj), 2);}
					else{$A_ADJC = number_format(abs($ll->bill_adj), 2);}
				}
				
				$A_PAY_D = '';
				$A_PAY_C = '';
				
				if(!empty($ll->payment)){
					
					if($ll->payment <= 0)
					{
						$A_PAY_D = number_format(abs($ll->payment), 2);
					}else{
						$A_PAY_C = number_format(abs($ll->payment), 2);
					}
					//~ $A_PAY = number_format($ll->payment, 2);
				}
				
				if(!empty($ll->discount)){
					$A_DIS = number_format($ll->discount, 2); 
				}
				
				
				
				if(!empty($ttl1)){
					$A_BAL =  number_format($ttl1, 2); 
				}


				//For Senior
				$is_sen = stripos($ll->ledger_info, 'senior');
				
				if($is_sen !== false &&  $ll->led_type=='adjustment')
				{
					//~ $A_DIS = $A_ADJ;
					$A_ADJ = '';
					$A_PAR .= 'SENIOR CITIZEN';
				}
				
				$A_PAR = strtoupper($A_PAR);

				$led_info = strtoupper($ll->ledger_info);
				
				$adj_remark = '';
				
				if($ll->led_type=='adjustment')
				{
					$adj_remark= get_adj_desc($ll->reff_no);
					$adj_remark = strtoupper($adj_remark);
				}	
				
				if(
					$ll->led_type=='cancel_cr' || 
					$ll->led_type=='cancel_cr_nw' || 
					$ll->led_type=='cr_nw' || 
					$ll->led_type=='cr_nw_debit' || 
					$ll->led_type=='nw_cancel' || 
					$ll->led_type=='or_nw' || 
					$ll->led_type=='or_nw_debit' || 
					$ll->led_type=='payment' || 
					$ll->led_type=='payment_cancel' || 
					$ll->led_type=='payment_cr'  
				)
				{
					//~ $A_REFF = $ll->reff_no.'<br />'.$bill_num;
					$A_REFF = sprintf('%07d',$ll->reff_no);
				}	

				if(
					$ll->led_type=='or_nw_debit' || $ll->led_type=='cr_nw_debit'
				)
				{
					//~ $A_REFF = $ll->reff_no.'<br />'.$bill_num;
					$A_REFF = '';
				}	
				
				
				





		?>
		
		
			
			<tr>
				<td class="r1"><?php echo $A_DATE; ?></td>
				<td class="r2">
					<p>
						<?php echo !empty($A_PAR)?$A_PAR.'<br />':''; ?>
						<?php echo !empty($A_PERIOD)?$A_PERIOD.'<br />':''; ?>
						<?php echo !empty($led_info)?$led_info.'<br />':''; ?>
						<?php echo !empty($adj_remark)?$adj_remark.'<br />':''; ?>

					</p>
				</td>

				<td class="r3"><?php echo $A_REFF; ?></td>
				<td class="r4"><?php   echo $A_PREV; ?></td>
				<td class="r5"><?php   echo $A_CUR; ?></td>
				<td class="r6"><?php   echo $A_CON; ?></td>
				<td class="r7">
					<?php   echo $A_BILL; ?>
					<?php  echo $A_PEN; ?>
					<?php  echo $A_ADJD; ?>
					<?php  echo $A_PAY_D; ?>
					
				</td>
				<td class="r8">
					<?php  echo $A_ADJC; ?>
					<?php   echo $A_PAY_C; ?>
					<?php   echo $A_DIS; ?>
				</td>
				<td class="r9"><?php    echo $A_BAL; ?></td>
			</tr>			
					
		
		
		
		
		<?php /* ?>
		
		
		<tr>
			<td><?php echo date('m/d/Y', strtotime($ll->date01)); ?></td>
			<td><?php echo part_name1($ll->led_type); ?></td>

			<td><?php echo $ll->reff_no .' <br /> '.$ll->bill_ref; ?></td>
			<td><?php

				//echo @$ll->period;
				if($ll->led_type == 'billing'){
					//~ $pp1 = date('Y-m-d', strtotime(@$ll->period));
					echo date('Y/m/d', strtotime(@$ll->date01.' -1 month' ));
					echo ' - ';
					echo date('Y/m/d', strtotime(@$ll->date01));

				}

			?></td>
			<td style="text-align:right;"><?php echo $reading_info['prev3']; ?></td>
			<td style="text-align:right;"><?php echo $reading_info['curr3']; ?></td>
			<td style="text-align:right;"><?php echo $reading_info['cons3']; ?></td>
			<td   style="text-align:right;"><?php
				if(!empty($ll->billing)){
					echo number_format($ll->billing, 2);
				}
			?></td>
			<td style="text-align:right;"><?php
				if(!empty($ll->arrear)){
					echo number_format($ll->arrear, 2);
				}

			?></td>
			<td><?php //echo number_format(0, 2); ?></td>
			<td  style="text-align:right;"><?php

					if(!empty($ll->penalty)){
						echo number_format($ll->penalty, 2);
					}

			?></td>

			<td  style="text-align:right;"><?php
				if(!empty($ll->bill_adj)){
					echo number_format($ll->bill_adj, 2);
				}
			?></td>

			<td style="text-align:right;"><?php

					if(!empty($ll->payment)){
						echo number_format($ll->payment, 2);
					}

			?></td>

			<td  style="text-align:right;"><?php
				if(!empty($ttl1)){

					if(
					  $ll->led_type == 'non_water_bill' ||
					  $ll->led_type == 'payment_none_water' ||
					  $ll->led_type == 'nw_cancel'
					)
					{
						echo number_format($ttl1, 2);
					}else{
						echo number_format($ttl1, 2);
					}
				}

			?></td>
		</tr>
		
		<?php /**/ ?>
		

		<?php /*if($ll->led_type == 'billing'  && $ll->discount > 0  ): ?>
			<tr>
				<td><?php echo $ll->date01; ?></td>
				<td>Senior Citizen</td>

				<td><?php echo $ll->reff_no; ?></td>
				<td><?php //echo @$ll->period; ?></td>
				<td><?php //echo number_format(0, 2); ?></td>
				<td><?php //echo number_format(0, 2); ?></td>
				<td><?php //echo number_format(0, 2); ?></td>
				<td><?php //echo number_format(0, 2); ?></td>
				<td><?php //echo number_format(0, 2); ?></td>
				<td style="text-align:right;"><?php echo number_format($ll->discount, 2); ?></td>
				<td style="text-align:right;"><?php //echo $ll->reading; ?></td>
				<td style="text-align:right;"><?php //echo $ll->consump; ?></td>
				<td style="text-align:right;"><?php //echo $ll->consump; ?></td>
				<td  style="text-align:right;"><?php echo number_format($ll->ttl_bal, 2); ?></td>
			</tr>
		<?php endif;*/ ?>

		<?php endforeach; ?>
	</table>


	<div style="text-align:right;padding-top:30px;padding-bottom:50px;"  class="cmds001">
		<!--
		<button onclick="print_ledger_balace(<?php echo $acct_id; ?>)">Print Ledger</button>
		-->
		<button onclick="window.print();">Print Ledger</button>
		<button onclick="add_bill_adjustment_show_pop();">Add Adjustment</button>
		
		<!--
		<button onclick="recalculate_ledger(<?php echo $acct_id; ?>)">Recalculate Ledger</button>
		<button onclick="add_edit_beginning_balance(<?php echo $acct_id; ?>)">Add Edit Beginning Balance</button>
		-->

		<!--
		<a href="/billing/account_ledger/get_ledger_acct/print_pdf?acct_id=<?php echo $acct_id; ?>"  target="_blank">Print Ledger</a>
		<a href="/billing/account_ledger/get_ledger_acct/recalculate?acct_id=<?php echo $acct_id; ?>"  target="_blank">re-calculate Ledger</a>
		-->

	</div>


<?php //$beginning ?>


<input type="hidden" value="<?php echo (float) @$beginning->ttl_bal; ?>"  class="beg_amount" />
<input type="hidden" value="<?php echo @$beginning->period; ?>"  class="beg_period"  />
<input type="hidden" value="<?php echo @$beginning->acct_id; ?>"  class="beg_acct_id"  />
<input type="hidden" value="<?php echo @$_SESSION["my_csrf"]; ?>"  class="my_csrf"  />





</div>
