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
		
		
		foreach($my_collection as $cc): 
		
			$acct = (object) $cc->accounts;
			$full_name = substr(@$acct->acct_no.' '.@$acct->lname.', '.@$acct->fname,0,30);

				/****************/
				/****************/
				/****************/
				$coll_info = json_decode($cc->coll_info);
				$all_amt = 0;
				if(!empty(@$coll_info->payed))
				{
					foreach(@$coll_info->payed as $pp)
					{
						if($pp->typ=='other_payable')
						{
							$full_name.='<br />( NWB - Balance) - ';
							$full_name.= @$pp->other_payable;
							break;
						}
					}

				}

				/****************/
				/****************/
				/****************/			

			
			
			if(in_array($cc->status, $nwb_payment))://IF NON-WATER BILL
			
			$label_01 =  '<br />GL CODE # '.$cc->nw_glsl.'&nbsp;&nbsp;&nbsp;'.$cc->nw_desc;			

			
			$tax_label = '';
			$check_label = '';

			if($cc->tax_val > 0){
				// $tax_label       = "<br />W \ TAX ";
			}
			
			if(($cc->pay_type == 'check')){
				$check_label = '<br />CHECK #'.$cc->check_no;
			}
				


			?>
			
			<tr class="dddxx">
				<?php 

				$my_or111 = sprintf("%07d", $cc->invoice_num);

				?>
				<td><?php echo 'OR-'.$my_or111.' (NWB)'; ?></td>
				<td><?php echo $full_name.$label_01.$tax_label.$check_label; ?></td>
				<td class="rr"><?php //echo implode( '/', $cc->particular) ?></td>
				<td class="rr"><?php echo number_format($cc->payment,2); ?></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"><?php echo number_format(($cc->payment + $cc->tax_val),2); ?></td>
				<td class="rr"><?php echo ($cc->tax_val > 0)?'( '.number_format($cc->tax_val,2).' )':' '; ?></td>
			</tr>			
			
			<?php endif;//ENDIF NON-WATER BILL  ?>

			<?php 
			if(in_array($cc->status, $all_cancel))://IF NON-WATER BILL
			?>
			<tr class="dddxx">
				<?php 

				$my_or111 = sprintf("%07d", $cc->invoice_num);

				?>
				<td><?php echo 'OR-'.$my_or111.''; ?></td>
				<td>Canceled</td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
				<td class="rr"></td>
			</tr>				

			<?php 
			endif;//ENDIF NON-WATER BILL
			?>

			 <?php 

			if(in_array($cc->status, $wb_payment))://IF WATER BILL
				
				$col_break = $cc->break_dd;


				/****************/
				/****************/
				/****************/
				$coll_info = json_decode($cc->coll_info);
				$all_amt = 0;
				if(!empty(@$coll_info->payed))
				{
					foreach(@$coll_info->payed as $pp)
					{
						if($pp->typ=='other_payable')
						{
							// or_nw
							// $cc->nw_desc = $pp->other_payable;
							@$col_break['nwb'] += $pp->amount;							
							// break;
						}
					}

				}

				/****************/
				/****************/
				/****************/
				

				$tax_label 		 = "";
				$amount_label    = "";
				$check_label     = "";
				
				$billing_label = '';
				$penalty_label = '';
				$py_label      = '';
				$cy_label      = '';
				$op_label      = '';
				$nwb_label      = '';				

				
				if($cc->tax_val > 0){
					$tax_label       = "<br />W \ TAX ";
					$amount_label    =  '( '.number_format($cc->tax_val,2).' )';
				}
				
				if(($cc->pay_type == 'check')){
					$check_label = '<br />CHECK #'.$cc->check_no;
				}
				
				if(@$col_break['bil'] > 0){
					$billing_label = number_format(@$col_break['bil'],2);
				}

				if($col_break['pen'] > 0){
					$penalty_label = number_format($col_break['pen'],2);
				}

				if($col_break['py'] > 0){
					$py_label = number_format($col_break['py'],2);
				}

				if($col_break['cy'] > 0){
					$cy_label = number_format($col_break['cy'],2);
				}

				if(@$col_break['nwb'] > 0){
					$nwb_label = number_format(@$col_break['nwb'],2);
				}				
				
				// if($col_break['op'] > 0){
				// 	$op_label = number_format($col_break['op'],2);
				// }
				
				$ORCR = "OR-";
				if($cc->status == 'collector_receipt'){
					$ORCR = "CR-";
				}

			?>
			<tr>
				<td>
					<?php
						
						$my_or111 = sprintf("%07d", $cc->invoice_num);
					?>
					
					<?php echo $ORCR.$my_or111; ?>
				</td>
				<td><?php echo $full_name.$tax_label.$check_label; ?></td>
				<td class="ll"><?php echo implode( '/', $cc->particular) ?></td>
				<td class="rr"><?php echo number_format($cc->payment,2); ?></td>
				<td class="rr"><?php echo $billing_label; ?></td>
				<td class="rr"><?php echo $cy_label; ?></td>
				<td class="rr"><?php echo $py_label; ?></td>
				<td class="rr"><?php echo $penalty_label; ?></td>
				<td class="rr"><?php echo $nwb_label; ?></td>				
				<td class="rr"><?php echo $amount_label; ?></td>
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


	// WATER BILL
	// WATER BILL
	if(in_array($cc->status, $wb_payment))
	{
		
		$col_break = $cc->break_dd;


		/****************/
		/****************/
		/****************/
		$coll_info = json_decode($cc->coll_info);
		$all_amt = 0;
		if(!empty(@$coll_info->payed))
		{
			foreach(@$coll_info->payed as $pp)
			{
				if($pp->typ=='other_payable')
				{
					// or_nw
					// $cc->nw_desc = $pp->other_payable;
					@$col_break['nwb'] += $pp->amount;			
					// $cc->payment -= $pp->amount; 				
					// break;
				}
			}

		}

		/****************/
		/****************/
		/****************/




		
		if(@$col_break['bil'] > 0){
			$ttl_current += (float) @$col_break['bil'];
		}

		if($col_break['pen'] > 0){
			$ttl_penalty += (float)  $col_break['pen'];
		}

		if($col_break['py'] > 0){
			$ttl_py += (float)  $col_break['py'];
		}

		if($col_break['cy'] > 0){
			$ttl_cy +=  (float)  $col_break['cy'];
		}

		if(@$col_break['nwb'] > 0){
			$ttl_nwb +=  (float)  @$col_break['nwb'];
			$ttl_amt +=  (float)  @$col_break['nwb'];
			$ttl_wb  -=  (float)  @$col_break['nwb'];
		}		

		// if($col_break['op'] > 0){
		// 	$ttl_op +=  (float)  $col_break['op'];
		// }

		if($cc->tax_val > 0){
			$ttl_tax += $cc->tax_val;
			// $ttl_payment += $cc->tax_val;
		}

		
		$ttl_payment += $cc->payment;
		$ttl_wb += $cc->payment;



		
	}
	// WATER BILL END
	// WATER BILL END


	// NON-WATER BILL
	// NON-WATER BILL
	if(in_array($cc->status, $nwb_payment))
	{
		$ttl_amt += $cc->payment;
		$ttl_payment += $cc->payment;
		$ttl_nwb  += $cc->payment;

		if($cc->tax_val > 0) 
		{
			// $ttl_payment += $cc->tax_val;
			$ttl_tax += $cc->tax_val;
			$ttl_amt += $cc->tax_val;
		}
	}
	// NON-WATER BILL END 
	// NON-WATER BILL END
	
	if(in_array($cc->status, array_merge($wb_payment, $nwb_payment)))
	{
		$my_payment = $cc->payment;

		if($cc->tax_val > 0){
			// $my_payment += $cc->tax_val;
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
	<td colspan="3">SUB TOTAL</td>
	<td class="rr"><?php echo number_format($ttl_payment,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_current,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_cy,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_py,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_penalty,2); ?></td>
	<td class="rr"><?php echo number_format($ttl_amt,2); ?></td>
	<td class="rr">( <?php echo number_format($ttl_tax,2); ?> ) </td>
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
						<td class="rr">( <?php echo number_format($ttl_tax,2); ?> )</td>
					</tr>


					<tr>
						<td>ADA</td>
						<td class="rr"><?php echo number_format($ttl_ada,2); ?></td>
					</tr>

					<tr class="bord_top">
						<td>SUB TOTAL</td>
						<td class="rr"><?php echo number_format(($ttl_cash + $ttl_check + $ttl_ada),2); ?></td>
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
			<?php echo strtoupper($user->name); ?>			
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
	