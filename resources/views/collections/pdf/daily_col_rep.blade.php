<?php


//~ echo '<pre>';
$iid = (int) @$_GET['iid'];

$user2 = App\User::find($iid);
if($user2){
	$user_name =  $user2->name;
}else{
	$user_name =  'ADMIN';
}

//~ echo '<pre>';
//~ print_r($user2->toArray());
//~ die();

ob_start();
$npp = 45;
$pn  = 1;

?>
<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
</p>


<?php


	$sub_col = 0;
	$item_count = 1;
	$head_me = true;
	$firstx = 1;
	$sub_colZZ = 0;

	$cash_ttl1 = 0;
	$check_ttl1 = 0;
	$non_water_ttl1 = 0;
	$wtax_ttl1 = 0;
 	$ada_ttl1 = 0;
	$water_ttl1 = 0;


	$curr_bill_total = 0;
	$curr_arrear_total = 0;


	$G_cash_ttl1 = 0;
	$G_check_ttl1 = 0;
	$G_non_water_ttl1 = 0;
	$G_wtax_ttl1 = 0;
 	$G_ada_ttl1 = 0;
	$G_water_ttl1 = 0;


	$col2 = [];

	// echo '<pre>';
	// print_r($coll);
	// die();
	if(empty($coll)){

		ob_get_clean();
		echo 'No Record Found';
		echo '<br />';
		echo $date1_x;
		die();

	}


	foreach($coll as $c):

			$dd = help_collector_daily_report_func111($c);
			extract($dd);
			
			//~ echo '<pre>';
			//~ print_r($dd);
			//~ die();


			$curr_bill1 = (object) @$c->curr_bill[0];


			$curr_pen = 0;
			$curr_bill_xx = 0;
			$curr_arrear = 0;

			//~ echo '<pre>';
			//~ print_r($curr_bill1);
			//~ die();

			if(!empty(@$curr_bill1)){
				$curr_bill_xx = @$curr_bill1->billing;
			}

			if(!empty(@$curr_bill1->penalty2)){
				$curr_pen = @$curr_bill1->penalty2['due1'];
			}

			if(!empty(@$curr_bill1->arrear2)){
				$curr_arrear = @$curr_bill1->arrear2['amount'];
			}


			$total_current_bill = $curr_pen + $curr_bill_xx;

			//~ echo $total_current_bill;
			//~ echo '<pre>';
			//~ print_r($curr_bill1);
			//~ die();




	?>

	<?php

			if($item_count >= $npp){
				$pn++;
				$head_me = true;
				$item_count = 0;
			}

	?>

		<?php if($head_me): ?>


		<?php if($firstx != 1): ?>
			<tr>
				<td colspan="8">--- Continue ---</td>
			</tr>
		</table>
		<div class="page_break">&nbsp;</div>
		<?php
		endif;

		$firstx++;

		?>

			<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>

		<?php echo @$date1_x; ?>
		<br />
		<?php echo @$title1_x; ?>
		<br />
		<strong>OFFICIAL RECEIPT</strong>
		<br />
		<br />


		<table cellpadding="0" cellspacing="0">
			<tr class="bord_all">
				<td>Reciept Number</td>
				<td>Payor</td>
				<td class="cc">Amount<br /> Collected</td>
				<td class="cc">Current</td>
				<td class="cc">Arrears<br />(CY)</td>
				<td class="cc">Arrears<br />(PY)</td>
				<td class="cc">Penalty</td>
				<td class="cc">Amount</td>
<!--
				<td class="cc">TTL_PAY</td>
-->
			</tr>

		<?php

		$head_me = false;
		endif;

		?>

		<tr class="item_bo">
			<td class="ll"><?php echo $OR1; ?></td>
			<td class="ll"><?php echo $NAME1; ?></td>
			<td class="rr"><?php echo $AMOUNT_COL; ?></td>
			<td class="rr"><?php echo $A1//echo @$c->xxx_bill; ?></td>
			<td class="rr"><?php echo $A2//echo @$c->xxx_arre; ?></td>
			<td></td>
			<td style="text-align:right;padding-right:15px;"><?php echo $A4; ?></td>

			<?php 
				if((($c->status == 'or_nw') || ($c->status == 'cr_nw') )  && $c->nw_glsl != null)
				{		
			?>	
			<td class="rr"><?php echo $AMOUNT_COL; ?></td>
			<?php }else{ ?>
				<td></td>
				<?php } ?>
<!--
			<td  class="rr"><?php echo @$c->total_payment; ?></td>
-->
		</tr>

		<?php
		if($c->tax_val > 0  && ($c->status == 'active'))
		{

			$wtax_ttl1+=$c->tax_val;
		?>
		<tr class="item_bo">
			<td class="ll"><?php //echo $OR1; ?></td>
			<td class="ll">W \ TAX</td>
			<td class="rr"><?php //echo $AMOUNT_COL; ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo number_format($c->tax_val,2); ?></td>
		</tr>

		<?php
			$item_count++;
		}
		?>


		<?php
		if((($c->status == 'or_nw') || ($c->status == 'cr_nw') )  && $c->nw_glsl != null)
		{

		?>
		<tr class="item_bo">
			{{-- <td class="ll"><?php //echo $OR1; ?></td> --}}
			<td class="ll" colspan="6"><?php echo 'GL CODE # '.$c->nw_glsl.'&nbsp;&nbsp;&nbsp;'.$c->nw_desc; ?></td>

		</tr>

		<?php
			$item_count++;
		}
		?>

		<?php
		// if($c->tax_val > 0  && ($c->status == 'active'))

		if(($c->pay_type == 'check') &&  ($c->status == 'active'))
		{

		?>
		<tr class="item_bo">
			<td class="ll"><?php //echo $OR1; ?></td>
			<td class="ll"><?php echo 'CHECK #'.$inv1->check_no; ?></td>
			<td class="rr"><?php //echo $AMOUNT_COL; ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

		<?php
			$item_count++;
		}
		?>


		<?php

		if(($c->pay_type == 'ada') &&  ($c->status == 'active'))
		{

		?>
		<tr class="item_bo">
			<td class="ll"><?php //echo $OR1; ?></td>
			<td class="ll"><?php echo 'ADA - CHECK #'.$inv1->check_no; ?></td>
			<td class="rr"><?php //echo $AMOUNT_COL; ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

		<?php
			$item_count++;
		}
		?>



	<?php

	$item_count++;
	$sub_col += (float) @$AMOUNT_COL;
	if(@$AMOUNT_COL != ''){
		$sub_colZZ +=  $c->payment;
		$curr_bill_total += @$c->xxx_bill;
		$curr_arrear_total+=@$c->xxx_arre;
	}else{
			if($c->status == 'cancel_receipt' || $c->status == 'nw_cancel' ||  $c->status == 'cancel_cr'){
			}else{
				$col2[] = $c;
			}
	}

	if(($c->pay_type == 'cash') && ($c->status == 'active' || $c->status == 'or_nw'))
	{
			$cash_ttl1+=$c->payment;
	}

	if(($c->pay_type == 'check') &&  ($c->status == 'active' || $c->status == 'or_nw'))
	{$check_ttl1+=$c->payment;}

	if(($c->pay_type == 'ada') &&  ($c->status == 'active' || $c->status == 'or_nw'))
	{$ada_ttl1+=$c->payment;}

	if(($c->status == 'or_nw'))
	{$non_water_ttl1+=$c->payment;}

	if($c->status == 'active')
	{
		$water_ttl1+=$c->payment;
	}



	endforeach; ?>

	<tr class="bord_top">
		<td colspan="2">SUB TOTAL</td>
		<td  class="rr"><?php echo number_format($sub_colZZ,2)?></td>
		<td  class="rr"><?php echo number_format($curr_bill_total,2)?></td>
		<td  class="rr"><?php echo number_format($curr_arrear_total,2)?></td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr"><?php echo number_format($non_water_ttl1,2)?></td>
	</tr>

</table>
<br />

<table cellpadding="0" cellspacing="0" class="sub_ttl1">
	<tr>
		<td>Cash</td>
		<td>Check</td>
		<td>ADA</td>
		<td>Water</td>
		<td>Non-Water</td>
		<td>W \ Tax</td>
	</tr>

	<tr>
		<td><?php echo number_format($cash_ttl1,2); ?></td>
		<td><?php echo number_format($check_ttl1,2); ?></td>
		<td><?php echo number_format($ada_ttl1,2); ?></td>
		<td><?php echo number_format($water_ttl1,2); ?></td>
		<td><?php echo number_format($non_water_ttl1,2); ?></td>
		<td><?php echo number_format($wtax_ttl1,2); ?></td>
	</tr>
</table>

<?php
$G_cash_ttl1 += $cash_ttl1;
$G_check_ttl1 += $check_ttl1;
$G_non_water_ttl1 += $non_water_ttl1;
$G_wtax_ttl1 += $wtax_ttl1;
$G_ada_ttl1 += $ada_ttl1;
$G_water_ttl1 += $water_ttl1;
?>


<br />
<br />
<br />
<?php $pn++; ?>
<div class="page_break">&nbsp;</div>
<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>

<?php echo @$date1_x; ?>
<br />
<?php echo @$title1_x; ?>
<br />
<strong>COLLECTOR RECEIPT</strong>
<br />
<br />

<table cellpadding="0" cellspacing="0">
	<tr class="bord_all">
		<td>Reciept Number</td>
		<td>Payor</td>
		<td class="cc">Amount<br /> Collected</td>
		<td class="cc">Current</td>
		<td class="cc">Arrears<br />(CY)</td>
		<td class="cc">Arrears<br />(PY)</td>
		<td class="cc">Meter Maint.<br /> Fee</td>
		<td class="cc">Amount</td>
	</tr>


	<?php

	$sub_col = 0;
	$sub_colZZ=0;
	// $col2 = [];

	$cash_ttl1 = 0;
	$check_ttl1 = 0;
	$non_water_ttl1 = 0;
	$wtax_ttl1 = 0;
	$ada_ttl1 = 0;
	$water_ttl1=0;


	$curr_bill_total = 0;
	$curr_arrear_total =0;


	foreach($col2 as $c):

			$dd = help_collector_daily_report_func111($c, true);
			extract($dd);
	?>
		<tr class="item_bo">
			<td class="ll"><?php echo $OR1; ?></td>
			<td class="ll"><?php echo $NAME1; ?></td>
			<td class="rr"><?php echo $AMOUNT_COL; ?></td>
			<td class="rr"><?php echo $A1; ?></td>
			<td class="rr"><?php echo $A2; ?></td>
			<td></td>
			<td></td>
		</tr>

		<?php
		// if($c->tax_val > 0  && ($c->status == 'active'))

		if(($c->pay_type == 'ada') &&  ($c->status == 'collector_receipt'))
		{

		?>
		<tr class="item_bo">
			<td class="ll"><?php //echo $OR1; ?></td>
			<td class="ll"><?php echo 'ADA - CHECK #'.$inv1->check_no; ?></td>
			<td class="rr"><?php //echo $AMOUNT_COL; ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

		<?php
			$item_count++;
		}
		?>


		<?php
		// if($c->tax_val > 0  && ($c->status == 'active'))

		if(($c->pay_type == 'check') &&  ($c->status == 'collector_receipt'))
		{

		?>
		<tr class="item_bo">
			<td class="ll"><?php //echo $OR1; ?></td>
			<td class="ll"><?php echo 'CHECK #'.$inv1->check_no; ?></td>
			<td class="rr"><?php //echo $AMOUNT_COL; ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>

		<?php
			$item_count++;
		}
		?>




	<?php

	$sub_col += (float) @$AMOUNT_COL;

	if(@$AMOUNT_COL != ''){
		$sub_colZZ +=  $c->payment;
		$curr_bill_total += @$c->xxx_bill;
		$curr_arrear_total+=@$c->xxx_arre;
	}


	if(($c->pay_type == 'cash') && ($c->status == 'collector_receipt' || $c->status == 'cr_nw'))
	{$cash_ttl1+=$c->payment;}

	if(($c->pay_type == 'check') &&  ($c->status == 'collector_receipt' || $c->status == 'cr_nw'))
	{$check_ttl1+=$c->payment;}

	if(($c->pay_type == 'ada') &&  ($c->status == 'collector_receipt' || $c->status == 'cr_nw'))
	{$ada_ttl1+=$c->payment;}

	if(($c->status == 'cr_nw'))
	{$non_water_ttl1+=$c->payment;}


	if($c->status == 'collector_receipt')
	{
		$water_ttl1+=$c->payment;
	}


	endforeach; ?>

	<tr class="bord_top">
		<td colspan="2">SUB TOTAL</td>
		<td  class="rr"><?php echo number_format($sub_colZZ,2); ?></td>
		<td  class="rr"><?php echo number_format($curr_bill_total,2)?></td>
		<td  class="rr"><?php echo number_format($curr_arrear_total,2)?></td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
	</tr>

</table>
<br />

<table cellpadding="0" cellspacing="0" class="sub_ttl1">
	<tr>
		<td>Cash</td>
		<td>Check</td>
		<td>ADA</td>
		<td>Water</td>
		<td>Non-Water</td>
		<td>W \ Tax</td>
	</tr>

	<tr>
		<td><?php echo number_format($cash_ttl1,2); ?></td>
		<td><?php echo number_format($check_ttl1,2); ?></td>
		<td><?php echo number_format($ada_ttl1,2); ?></td>
		<td><?php echo number_format($water_ttl1,2); ?></td>
		<td><?php echo number_format($non_water_ttl1,2); ?></td>
		<td><?php echo number_format($wtax_ttl1,2); ?></td>
	</tr>
</table>

<?php
$G_cash_ttl1 += $cash_ttl1;
$G_check_ttl1 += $check_ttl1;
$G_non_water_ttl1 += $non_water_ttl1;
$G_wtax_ttl1 += $wtax_ttl1;
$G_ada_ttl1 += $ada_ttl1;
$G_water_ttl1 += $water_ttl1;

?>



<br />
<br />

<?php $pn++; ?>
<div class="page_break">&nbsp;</div>
<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>



<?php echo @$date1_x; ?>
<br />
<?php echo @$title1_x; ?>
<br />
<b>SUMMARY</b>
<br />
<br />

<strong><?php //echo $coll_name; ?></strong>


<table cellpadding="0" cellspacing="0" class="">
	<tr>
		<td>
			<!--- --->
				<table cellpadding="0" cellspacing="0" class="w200">
					<tr>
						<td>CASH</td>
						<td class="rr"><?php echo number_format($G_cash_ttl1,2); ?></td>
					</tr>

					<tr>
						<td>CHECK</td>
						<td class="rr"><?php echo number_format($G_check_ttl1,2); ?></td>
					</tr>

					<tr>
						<td>ADA</td>
						<td class="rr"><?php echo number_format($G_ada_ttl1,2); ?></td>
					</tr>

					<tr class="bord_top">
						<td>SUB TOTAL</td>
						<td class="rr"><?php echo number_format(($G_ada_ttl1 + $G_check_ttl1 + $G_cash_ttl1),2); ?></td>
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
						<td class="rr"><?php echo number_format($G_water_ttl1,2); ?></td>
					</tr>
					<tr>
						<td>NON-WATER BILL</td>
						<td class="rr"><?php echo number_format($G_non_water_ttl1,2); ?></td>
					</tr>

					<tr class="bord_top">
						<td>SUB TOTAL</td>
						<td class="rr"><?php echo number_format(($G_water_ttl1 + $G_non_water_ttl1),2); ?></td>
					</tr>

				</table>
			<!--- --->
		</td>
	</tr>
</table>

<br />
<br />
<h3>GRAND TOTAL : <?php echo number_format(($G_water_ttl1 + $G_non_water_ttl1),2); ?></h3>




<br />
<br />

<hr />




<?php

$fname1 = '

<br /><br />

'.strtoupper($user_name).'
<br /><br />
Teller
';

html_signature($fname1); ?>




<style>
*{
	font-family:'sans-serif';
	font-size:10px;
}
table{
	width:100%;
}
table *{
}
table td{
	border-bottom:0px solid #ccc;
	padding:2px;
}
.bord_all td{
	border:1px solid #ccc;
}
.bord_bot td{
	border-bottom:1px solid #ccc;
}
.bord_top td{
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

</style>

<?php

$cont_ttl = ob_get_contents();
ob_get_clean();

$cont_ttl = str_replace('--ttl_p--', $pn, $cont_ttl);

echo $cont_ttl;

// die();
?>
