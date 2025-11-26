
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
	
	<table  class="led01 acct_ledger01  table10">
		<tr  class="headings">
			<td style="width:100px;">DATE</td>
			<td>PARTICULARS</td>
<!--
			<td style="width:200px;">INFO</td>
-->
			<td style="width:100px;">PERIOD</td>
			<td>ARREAR</td>
			<td>BILLING</td>
			<td>PAYMENT</td>
			<td>DISCOUNT</td>
			<td>PENALTY</td>
			<td>ADJUSTMENT</td>
			<td>READING</td>
			<td>CONSUMPTION</td>
			<td>TOTAL BALANCE</td>
		</tr>
		
		<?php  
		
		
		foreach($led001 as $ll): 
			//~ $total001 = 0;
			//~ $total001+= $ll->arrear;
			//~ $total001+= $ll->billing;
			//~ $total001+= $ll->payment;
			//~ $total001+= $ll->discount;
		?>
		<tr>
			<td><?php echo $ll->date01; ?></td>
			<td><?php echo part_name1($ll->led_type); ?></td>
<!--
			<td><?php echo $ll->ledger_info; ?></td>
-->
			<td><?php echo @$ll->period; ?></td>
			<td><?php echo number_format($ll->arrear, 2); ?></td>
			<td><?php echo number_format($ll->billing, 2); ?></td>
			<td><?php echo number_format($ll->payment, 2); ?></td>
			<td><?php echo number_format($ll->discount, 2); ?></td>
			<td><?php echo number_format($ll->penalty, 2); ?></td>
			<td><?php echo number_format($ll->bill_adj, 2); ?></td>
			<td style="text-align:right;"><?php echo $ll->reading; ?></td>
			<td style="text-align:right;"><?php echo $ll->consump; ?></td>
			<td  style="text-align:right;"><?php echo number_format($ll->ttl_bal, 2); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	
	
	<div style="text-align:right;padding-top:30px;padding-bottom:50px;">
		<button onclick="print_ledger_balace(<?php echo $acct_id; ?>)">Print Ledger</button>
		<button onclick="recalculate_ledger(<?php echo $acct_id; ?>)">Recalculate Ledger</button>
		<button onclick="add_edit_beginning_balance(<?php echo $acct_id; ?>)">Add Edit Beginning Balance</button>
		
<!--
		<a href="/billing/account_ledger/get_ledger_acct/print_pdf?acct_id=<?php echo $acct_id; ?>"  target="_blank">Print Ledger</a>
		<a href="/billing/account_ledger/get_ledger_acct/recalculate?acct_id=<?php echo $acct_id; ?>"  target="_blank">re-calculate Ledger</a>
-->
		
	</div>
	

<?php //$beginning ?>

	
<input type="hidden" value="<?php echo (float) @$beginning->ttl_bal; ?>"  class="beg_amount" />	
<input type="hidden" value="<?php echo @$beginning->period; ?>"  class="beg_period"  />
<input type="hidden" value="<?php echo @$beginning->acct_id; ?>"  class="beg_acct_id"  />



</div>




