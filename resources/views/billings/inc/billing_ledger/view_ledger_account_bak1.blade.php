<?php

//~ echo '<pre>';
//~ print_r($led001->toArray());
//~ die();

$_SESSION["my_csrf"]=uniqid();

//account_info
?>


<h4><?php echo $account_info->fname.' '.$account_info->lname; ?></h4>
<p>
	#<?php echo $account_info->acct_no; ?>
	<br />
	<?php echo $account_info->address1; ?>
</p>



<br />


<ul class="tabview_cmd">
	<li onclick="ledger_1('tabview1', 'tt1')"  class="tt1 active">Account Ledger</li>
	<li onclick="ledger_1('tabview2', 'tt2')"  class="tt2">History</li>
	<li onclick="ledger_1('tabview3', 'tt3')"  class="tt3">Reading Ledger</li>
</ul>



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
<!--
			<td width="10%">Type</td>
-->
			<td width="40%">Information</td>
		</tr>

		<?php  foreach($ledger_list1 as $acct): ?>
			<tr>
				<td><?php echo date('F d, Y @ H:iA', strtotime($acct->led_date2)); ?></td>
				<td>
					<p><?php echo $acct->led_title; ?></p>
				</td>
<!--
				<td><?php echo $acct->ctyp1; ?></td>
-->
				<td>
					
					<?php echo $acct->led_desc1; ?>
					
						<?php /*if($acct->ctyp1 == 'billing1'): ?>
							<ul>
								<li>Billing total : 52.55</li>
								<li>Arear : 52.55</li>
								<li>Billing total : 52.55</li>
							</ul>
						<?php elseif($acct->ctyp1 == 'collection1'): ?>
							<ul>
								<li>Payed amount : 945.90</li>
								<li>Remaining balance : 52.55</li>
							</ul>
						<?php else: ?>

						<?php  endif; */?>
				</td>
			</tr>
		<?php endforeach;  ?>

	</table>
	
	<div style="text-align:right;padding-top:30px;padding-bottom:50px;">
		<a href="/billing/account_ledger/get_ledger_acct/print_pdf_history?acct_id=<?php echo $acct_id; ?>"  target="_blank">Print History</a>
	</div>	


</div>


<div class="tab1 tabview1  tabview">
	<br />
	
	<table  class="led01 acct_ledger01  table10">
		<tr  class="headings">
			
				<td class="w1">DATE</td>
				<td class="w2">PAR.</td>
				<td class="w3">REFF</td>
				<td class="w4">PERIOD</td>
				<td class="w5">PREV</td>
				<td class="w6">CUR</td>
				<td class="w7">CON</td>
				<td class="w8">BILL</td>
				<td class="w9">ARR</td>
				<td class="w10">DIS</td>
				<td class="w11">PEN</td>
				<td class="w12">ADJ</td>
				<td class="w13">PAY</td>
				<td class="w14">BAL</td>
			
<!--
			<td class="w1">DATE</td>
			<td class="w2">PARTICULAR</td>
			<td class="w3">REFFERENCE</td>
			<td class="w4">PERIOD</td>
			<td class="w5">PREV READING</td>
			<td class="w6">CURRENT READING</td>
			<td class="w7">C.U.</td>
			<td class="w8">BILLING</td>
			<td class="w9">ARREAR</td>
			<td class="w10">DISCOUNT</td>
			<td class="w11">PENALTY</td>
			<td class="w12">ADJUSTMENT</td>
			<td class="w13">PAYMENT</td>
			<td class="w14">BALLANCE</td>
-->
			
			
		</tr>
	</table>
	
	<div class="scrol1111">
		<table  class="led01 acct_ledger01  table10"  style="margin-top: -25px !important;display: inline-block;">
			
			<tr  class="headings" style="visibility:hidden;">
<!--
				<td class="w1">DATE</td>
				<td class="w2">PARTICULAR</td>
				<td class="w3">REFFERENCE</td>
				<td class="w4">PERIOD</td>
				<td class="w5">PREV READING</td>
				<td class="w6">CURRENT READING</td>
				<td class="w7">C.U.</td>
				<td class="w8">BILLING</td>
				<td class="w9">ARREAR</td>
				<td class="w10">DISCOUNT</td>
				<td class="w11">PENALTY</td>
				<td class="w12">ADJUSTMENT</td>
				<td class="w13">PAYMENT</td>
				<td class="w14">BALLANCE</td>
-->
				<?php /**/ ?>
				<td class="w1">DATE</td>
				<td class="w2">PAR.</td>
				<td class="w3">REFF</td>
				<td class="w4">PERIOD</td>
				<td class="w5">PREV</td>
				<td class="w6">CUR</td>
				<td class="w7">CON</td>
				<td class="w8">BILL</td>
				<td class="w9">ARR</td>
				<td class="w10">DIS</td>
				<td class="w11">PEN</td>
				<td class="w12">ADJ</td>
				<td class="w13">PAY</td>
				<td class="w14">BAL</td>
				<?php /**/ ?>
			</tr>
			
			<?php  
			
			
			foreach($led001 as $ll): 
			
				
				$ttl1 = $ll->ttl_bal;
				
				$bill_num = '';

				if($ll->led_type == 'billing'){
					$bill_num = get_billing_number($ll->reff_no);					
				}
				
				if($ll->led_type == 'billing'  && $ll->discount > 0  ){
					$ttl1  = $ll->billing; 
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
				$A_PAR     = part_name1($ll->led_type);
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
					$A_DATE = date('m/d/Y', strtotime($ll->created_at)); 
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
				
				if(!empty($ll->discount)){
					$A_DIS = number_format($ll->discount, 2); 
				}
				
				if(!empty($ttl1)){
					$A_BAL =  number_format($ttl1, 2); 
				}



				//For Senior
				$is_sen = stripos($ll->ledger_info, 'senior');
				
				if($is_sen !== false){
					$A_DIS = $A_ADJ;
					$A_ADJ = '';
					$A_PAR = 'SENIOR CITIZEN';
				}
				
				
				$A_PAR = strtoupper($A_PAR);
				
			?>
			<tr>
				<td><span ondblclick="edit_ledger_status(<?php echo $ll->id; ?>)"><?php echo $A_DATE; ?></span></td>
				<td><?php echo $A_PAR; ?></td>
				<td><?php echo $A_REFF; ?></td>
				<td><?php echo $A_PERIOD;?></td>
				<td style="text-align:right;"><?php   echo $A_PREV; ?></td>
				<td style="text-align:right;"><?php   echo $A_CUR; ?></td>
				<td style="text-align:right;"><?php   echo $A_CON; ?></td>
				<td   style="text-align:right;"><?php echo $A_BILL; ?></td>
				<td style="text-align:right;"><?php   echo $A_ARR; ?></td>
				<td  style="text-align:right;"><?php  echo $A_DIS; ?></td>
				<td  style="text-align:right;"><?php  echo $A_PEN; ?></td>
				<td  style="text-align:right;"><?php  echo $A_ADJ; ?></td>
				<td style="text-align:right;"><?php   echo $A_PAY; ?></td>				
				<td  style="text-align:right;"><?php  echo $A_BAL; ?></td>
			</tr>
			
			<?php 
				/*
			 if($ll->led_type == 'billing'  && $ll->discount > 0  ): ?>
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
			<?php endif;*/ 
			
			?>
			
			<?php endforeach; ?>
			
				<tr>
					<td colspan="14">--- END ----</td>
				</tr>
			
			
			
		</table>
	</div>
	
	
	
	
	
	
	<div style="text-align:right;padding-top:30px;padding-bottom:50px;"  class="cmds001">
		<div class="cmd_buts">
			<a href="/billing/account_ledger/view_ledger_account_info_pdf?acct_id=<?php echo $account_info->id; ?>" target="_blank">
				<button>Print Ledger</button>
			</a>
			<button onclick="trig1_v2('add_adjustment_form1_pop');">Add Adjustment</button>
			
			<button onclick="trig1_v2('edit_beginning_bal001_pop');">Add Edit Beginning Balance</button>
			
			<?php
			/*
			<button onclick="refresh_ledger_101(<?php echo $account_info->id; ?>)">Refresh Ledger</button>
			 * 
			<button onclick="recalculate_ledger_V2()">Recalculate Ledger</button>
			*/  ?>
		</div>
		<div class="please_wait" style="display:none;">
			Please Wait.......
		</div>
	</div>	
	

<?php //$beginning ?>

	
<input type="hidden" value="<?php echo (float) @$beginning->ttl_bal; ?>"  class="beg_amount" />	
<input type="hidden" value="<?php echo @$beginning->period; ?>"  class="beg_period"  />
<input type="hidden" value="<?php echo @$beginning->acct_id; ?>"  class="beg_acct_id"  />
<input type="hidden" value="<?php echo @$_SESSION["my_csrf"]; ?>"  class="my_csrf"  />

</div>




<div class="add_adjustment_form1_pop" style="display:none;">
	<div  class="add_adjustment_form1_cont" >
		<div class="add_billing_adjustment_cont">
			<h2>Add Billing Adjustment</h2>
			<input type="number" value="" class="form-control bill_adjustment_amount" min="0" placeholder="Amount">
			<br>
			<textarea class="form-control bill_ajustment_note"></textarea>
			<br>

			<div class="please_wait" style="display:none;">
				Please Wait.......
			</div>
			<div class="cmd_buts">
				<button onclick="add_bill_adjustment_save_V2()">Save</button>
				<button onclick="view_acct_ledger(curr_index)">Cancel</button>
			</div>			

			
		</div>
	
	</div>
</div>



<div class="edit_beginning_bal001_pop" style="display:none;">
	<div  class="edit_beginning_bal001_cont" >
		<div class="add_billing_adjustment_cont">
			
			<h2>Edit Beginning balance</h2>
			<div>
				<input type="number" value="" class="form-control beg_bal1_amt" 
					   min="0" placeholder="Beginning Balance">
				<select class="form-control  beg_bal1_prd">
						<?php 
						
						for($x=0;$x<=10;$x++): 
							$time1 = strtotime(date('Y-m-01').' - '.$x.' Month');
						?>
						<option value="<?php echo date('Y-m-d', $time1); ?>"><?php echo date('F Y', $time1); ?></option>
						<?php
						 
						$x++;
						
						endfor; ?>
				</select>
				<br>
				
				<div class="please_wait" style="display:none;">
					Please Wait.......
				</div>
				<div class="cmd_buts">
					<button onclick="edit_add_beginning_bal_V1()">Save</button>
					<button onclick="view_acct_ledger(curr_index)">Cancel</button>
				</div>			
								
				
				
			</div>
				

		</div>
	</div>
</div>


