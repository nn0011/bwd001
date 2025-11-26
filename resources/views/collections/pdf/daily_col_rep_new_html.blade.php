<?php

$wb_payment = array(
			'active',
			'collector_receipt',
		);

$nwb_payment = array(
			'or_nw',
			'cr_nw',
		);

$all_cancel = array(
			'cancel_cr',
			'cancel_cr_nw',
			'cancel_receipt',
			'nw_cancel'
		);

?>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />
<table  cellpadding="0" cellspacing="0">
	<thead>
	<tr>
		<td colspan="7">
			Daily Collection Report
			<br />
			<?php  echo date('l, M d, Y', strtotime($var01['dd'])); ?> 	
		</td>
		<td>
			<span class="RRR111"></span>
		</td>
	</tr>

	<tr class="bord_all">
		<td>Reciept <br /> Number</td>
		<td>Payor</td>
		<td class="cc">Amount <br /> Collected</td>
		<td class="cc">Current</td>
		<td class="cc">Arrears <br /> (CY)</td>
		<td class="cc">Arrears <br /> (PY)</td>
		<td class="cc">Penalty</td>
		<td class="rr">N.W.B</td>
		<td class="rr">W/Tax</td>
		<td class="rr">Advance</td>
	</tr>

	</thead>

	<tbody>
		<?php 
		
		
		foreach($my_collection as $cc): 
		
			$acct = (object) $cc->accounts;
			$full_name = substr($acct->acct_no.' '.$acct->lname.', '.$acct->fname,0,30);
			
			
			if(in_array($cc->status, $nwb_payment))://IF NON-WATER BILL
			
			$label_01 =  '<br />GL CODE # '.$cc->nw_glsl.'&nbsp;&nbsp;&nbsp;'.$cc->nw_desc;			
			?>
			
			<tr class="dddxx">
				<td><?php echo 'OR-'.$cc->invoice_num.' (NWB)'; ?></td>
				<td><?php echo $full_name.$label_01; ?></td>
				<td class="rr"><?php echo number_format($cc->payment,2); ?></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"><?php echo number_format($cc->payment,2); ?></td>
				<td class="rr"></td>
				<td class="rr"></td>
			</tr>			
			
			
			<?php endif;//ENDIF NON-WATER BILL

			if(in_array($cc->status, $wb_payment))://IF WATER BILL
				
				$col_break = $cc->col_break;

				$tax_label 		 = "";
				$amount_label    = "";
				$check_label     = "";
				
				$billing_label = '';
				$penalty_label = '';
				$py_label      = '';
				$cy_label      = '';
				$op_label      = '';

				
				if($cc->tax_val > 0){
					$tax_label       = "<br />W \ TAX ";
					$amount_label    =  '( '.number_format($cc->tax_val,2).' )';
				}
				
				if(($cc->pay_type == 'check')){
					$check_label = '<br />CHECK #'.$cc->check_no;
				}
				
				if($col_break['billing'] > 0){
					$billing_label = number_format($col_break['billing'],2);
				}

				if($col_break['penalty'] > 0){
					$penalty_label = number_format($col_break['penalty'],2);
				}

				if($col_break['py'] > 0){
					$py_label = number_format($col_break['py'],2);
				}

				if($col_break['cy'] > 0){
					$cy_label = number_format($col_break['cy'],2);
				}
				
				if($col_break['op'] > 0){
					$op_label = number_format($col_break['op'],2);
				}
				
				$ORCR = "OR-";
				if($cc->status == 'collector_receipt'){
					$ORCR = "CR-";
				}

			?>
			<tr>
				<td>
					<?php echo $ORCR.$cc->invoice_num; ?>
				</td>
				<td><?php echo $full_name.$tax_label.$check_label; ?>
				</td>
				<td class="rr"><?php echo number_format($cc->payment,2); ?></td>
				<td class="rr"><?php echo $billing_label; ?></td>
				<td class="rr"><?php echo $cy_label; ?></td>
				<td class="rr"><?php echo $py_label; ?></td>
				<td class="rr"><?php echo $penalty_label; ?></td>
				<td class="rr"></td>
				<td class="rr"><?php echo $amount_label; ?></td>
				<td class="rr"><?php echo $op_label; ?></td>
			</tr>
			<?php 
		
		endif;//ENDIF WATER BILL
		
	
	endforeach; 
	
?>


<?php 


$ttl_payment = 0;
$ttl_current = 0;
$ttl_cy      = 0;
$ttl_py      = 0;
$ttl_penalty = 0;
$ttl_amt     = 0;
$ttl_tax     = 0;

$ttl_cash    = 0;
$ttl_check   = 0;
$ttl_ada   	 = 0;

$ttl_wb      = 0;
$ttl_nwb     = 0;
$ttl_op      = 0;

foreach($my_collection as $cc)
{
	if(in_array($cc->status, $wb_payment))
	{
		
		$col_break = $cc->col_break;
		
		if($col_break['billing'] > 0){
			$ttl_current += (float) $col_break['billing'];
		}

		if($col_break['penalty'] > 0){
			$ttl_penalty += (float)  $col_break['penalty'];
		}

		if($col_break['py'] > 0){
			$ttl_py += (float)  $col_break['py'];
		}

		if($col_break['cy'] > 0){
			$ttl_cy +=  (float)  $col_break['cy'];
		}

		if($col_break['op'] > 0){
			$ttl_op +=  (float)  $col_break['op'];
		}

		if($cc->tax_val > 0){
			$ttl_tax += $cc->tax_val;
			// $ttl_payment += $cc->tax_val;
		}

		
		$ttl_payment += $cc->payment;
		
		$ttl_wb += $cc->payment;
		
	}

	if(in_array($cc->status, $nwb_payment))
	{
		$ttl_amt += $cc->payment;
		$ttl_payment += $cc->payment;
		$ttl_nwb  += $cc->payment;
	}
	
	if(in_array($cc->status, array_merge($wb_payment, $nwb_payment)))
	{
		$my_payment = $cc->payment;

		if($cc->tax_val > 0){
			$my_payment += $cc->tax_val;
		}

		if($cc->pay_type == 'cash'){
			$ttl_cash+= $my_payment;
		}

		if($cc->pay_type == 'check'){
			$ttl_check+= $my_payment;
		}
		if($cc->pay_type == 'ada'){
			$ttl_ada+= $my_payment;
		}

		
	}

	
	
	
}// 

?>	

<tr class="bord_all">
	<td colspan="2">SUB TOTAL</td>
	<td class="rr"><?php echo number_format($ttl_payment,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_current,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_cy,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_py,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_penalty,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_amt,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_tax,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_op,2); ?></td>
</tr>	


	</tbody>




</table>


<br />
<br /><br />
<br /><br />
<br />

<b>SUMMARY</b>
<br />
<br />





<table cellpadding="0" cellspacing="0" class="">
	<tr>
		<td>
			<!--- --->
				<table cellpadding="0" cellspacing="0" class="w200">
					<tr>
						<td>CASH</td>
						<td class="rr"><?php echo number_format($ttl_cash,2); ?></td>
					</tr>

					<tr>
						<td>CHECK</td>
						<td class="rr"><?php echo number_format($ttl_check,2); ?></td>
					</tr>

					<tr>
						<td>w/Tax</td>
						<td class="rr"><?php echo number_format($ttl_tax,2); ?></td>
					</tr>


					<tr>
						<td>ADA</td>
						<td class="rr"><?php echo number_format($ttl_ada,2); ?></td>
					</tr>

					<tr class="bord_top">
						<td>SUB TOTAL</td>
						<td class="rr"><?php echo number_format(($ttl_cash + $ttl_check + $ttl_ada) - $ttl_tax,2); ?></td>
					</tr>

				</table>

			<!--- --->

		</td>
		<td>
			<!--- --->

				<table cellpadding="0" cellspacing="0" class="w200">
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td>WATER BILL</td>
						<td class="rr"><?php echo number_format(@$ttl_wb,2); ?></td>
					</tr>
					<tr>
						<td>NON-WATER BILL</td>
						<td class="rr"><?php echo number_format(@$ttl_nwb,2); ?></td>
					</tr>

					<tr class="bord_top">
						<td>SUB TOTAL</td>
						<td class="rr"><?php echo number_format(($ttl_wb + $ttl_nwb),2); ?></td>
					</tr>

				</table>
			<!--- --->
		</td>
		
	</tr>
</table>

<br />
<br />
<h3>GRAND TOTAL : <?php echo number_format(($ttl_payment),2); ?></h3>


<br />







<br />








<br />
<br />



<table style="width:100%;">
	<tr>
		<td>
			<span class="bl1">Prepared By :</span>
			<br />
			<br />
			<br />
			<?php echo REP_SIGN1; ?>			
			<br />
			<?php echo REP_SIGN1_TITLE; ?>
		</td>
		<td>
			<span class="bl1">Checked by:</span>
			<br />
			<br />
			<br />
			<?php echo REP_SIGN2; ?>			
			<br />
			<?php echo REP_SIGN2_TITLE; ?>
		</td>

		<td>
			<span class="bl1">Noted by:</span>
			<br />
			<br />
			<br />
			<?php echo WD_MANAGER; ?>			
			<br />
			<?php echo WD_MANAGER_RA; ?>
		</td>		

	</tr>
</table>

<br />
<br />









<style>
html,body{
	margin:0;
	padding:20px;
}
*{
	font-family:'sans-serif';
	font-size:11px;
}

.cc1 td{
	padding-right:30px;
}

table{
	width:100%;
	max-with:800px;
}
table *{
}
table td{
	border:0px solid #ccc;
	padding:2px;
	vertical-align:top;
	padding-bottom:0  !important;
	padding-top:0 !important;
	margin-bottom:0  !important;
	margin-top:0 !important;

}
.bord_all td{
	border:1px solid #ccc;
}
.bord_bot td, .bord_bot1{
	border-bottom:1px solid #ccc;
}
.bord_top td, .bord_top1{
	border-top:1px solid #ccc;
}
.ll{
	text-align:left;
}
.rr{
	text-align:right;
	padding-right:15px;
}
.cc{
	text-align:center;
}

.under ,
.trun
td{
	border-bottom:1px solid #ccc;
}



.rh1{
	text-align:right;
}
.bld_me{font-weight:bold;}
.page-number{
		position:fixed;
		left:0;
		bottom:10;
}
.page-number:after {
		content: counter(page);
	}

.page_break { page-break-before: always; }



.page-number-x{
		position:absolute;
		left:0;
		bottom:0;
}
.page-number-c{
		position:fixed;
		left:0;
		bottom:10px;
}
.page-number{
		position:absolute;
		left:0;
		bottom:10;
}
.page-number:after {
		content: counter(page);
		left:25;
		position:absolute;
	}
.w200{
	width:200px;
}
.sub_ttl1 td{
	border:1px solid #ccc;
	padding:3px;
	text-align:right;
	padding-right: 20px;
}

body,
html{
	padding-bottom:50px;
	margin-bottom:50px;
	counter-reset: pagexx;
}


.RRR111{
	counter-increment: pagexx;
}




@media print {
	.RRR111::after {
		content: "Page " counter(pagexx);
  }
}


</style>
	