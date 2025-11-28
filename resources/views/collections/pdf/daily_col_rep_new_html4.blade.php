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

<table  cellpadding="0" cellspacing="0"  style="max-width:1024px;">


	<thead>
	<tr>
		<td colspan="7">
			Daily Collection Report
			<br />
			<?php  echo date('l, M d, Y', strtotime(@$date1)); ?> 	
		</td>
		<td>
			<span class="RRR111"></span>
		</td>
	</tr>

	<tr class="bord_all">
		<td style="width:80px;">Reciept <br /> Number</td>
		<td style="width:200px;">Payor</td>
		<td style="width:200px;">Particulars</td>
		<td class="cc">Amount <br /> Collected</td>
		<td class="cc">Current</td>
		<td class="cc">Arrears <br /> (CY)</td>
		<td class="cc">Arrears <br /> (PY)</td>
		<td class="cc">Penalty</td>
		<td class="rr">N.W.B</td>
		<td class="rr">W/Tax</td>
	</tr>

	</thead>

	<tbody>
		<?php 
		
		$ttl001 = 0;
		foreach($my_collection as $cc): 

			$ttl001 += $cc->payment;

			// $acct = (object) $cc->accounts;
			$full_name = substr(@$cc->acct_no.' '.@$cc->lname.', '.@$cc->fname,0,30);

				


			?>
			
			<tr>
				<td>
					<?php
						
						$my_or111 = sprintf("%07d", $cc->invoice_num);
					?>
					
					<?php echo $my_or111; ?>
				</td>
				<td><?php echo $full_name;
				// .$tax_label.$check_label; ?></td>
				<td class="ll"><?php //echo implode( '/', $cc->particular) ?></td>
				<td class="rr"><?php echo number_format($cc->payment,2); ?></td>
				<td class="rr"><?php //echo $billing_label; ?></td>
				<td class="rr"><?php // echo $cy_label; ?></td>
				<td class="rr"><?php //echo $py_label; ?></td>
				<td class="rr"><?php //echo $penalty_label; ?></td>
				<td class="rr"><?php //echo $nwb_label; ?></td>				
				<td class="rr"><?php //echo $amount_label; ?></td>
			</tr>
			<?php 
		
		
	
	endforeach; 
	
?>




<tr class="bord_all">
	<td colspan="3">SUB TOTAL</td>
	<td class="rr"><?php echo number_format($ttl001,2); ?></td>
	<td class="rr"><?php //echo number_format($ttl_current,2); ?></td>
	<td class="rr"><?php //echo number_format($ttl_cy,2); ?></td>
	<td class="rr"><?php //echo number_format($ttl_py,2); ?></td>
	<td class="rr"><?php //echo number_format($ttl_penalty,2); ?></td>
	<td class="rr"><?php //echo number_format($ttl_amt,2); ?></td>
	<td class="rr">( <?php //echo number_format($ttl_tax,2); ?> ) </td>
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
						<td class="rr"><?php //echo number_format($ttl_cash,2); ?></td>
					</tr>

					<tr>
						<td>CHECK</td>
						<td class="rr"><?php //echo number_format($ttl_check,2); ?></td>
					</tr>

					<tr>
						<td>w/Tax</td>
						<td class="rr">( <?php// echo number_format($ttl_tax,2); ?> )</td>
					</tr>


					<tr>
						<td>ADA</td>
						<td class="rr"><?php// echo number_format($ttl_ada,2); ?></td>
					</tr>

					<tr class="bord_top">
						<td>SUB TOTAL</td>
						<td class="rr"><?php //echo number_format(($ttl_cash + $ttl_check + $ttl_ada),2); ?></td>
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
						<td class="rr"><?php //echo number_format(@$ttl_wb,2); ?></td>
					</tr>
					<tr>
						<td>NON-WATER BILL</td>
						<td class="rr"><?php //echo number_format(@$ttl_nwb,2); ?></td>
					</tr>

					<tr class="bord_top">
						<td>SUB TOTAL</td>
						<td class="rr"><?php //echo number_format(($ttl_wb + $ttl_nwb),2); ?></td>
					</tr>

				</table>
			<!--- --->
		</td>
		
	</tr>
</table>

<br />
<br />
<h3>GRAND TOTAL : <?php // echo number_format(($ttl_payment),2); ?></h3>


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
			<?php // echo strtoupper($user->name); ?>			
			<br />
			Teller 1
		</td>
		<td>
			<span class="bl1">Checked by:</span>
			<br />
			<br />
			<br />
			<?php echo REP_SIGN1; ?>			
			<br />
			<?php echo REP_SIGN1_TITLE; ?>
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
	