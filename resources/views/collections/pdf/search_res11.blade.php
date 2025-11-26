<?php
ob_start();
$npp = 48;
$pn  = 1;

?>
<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />
<?php echo @$date1_x; ?>
</p>


	<?php

	$sub_col = 0;
	$item_count = 1;
	$head_me = true;
	$firstx = 1;

	foreach($coll as $c):

			$dd = help_collector_daily_report_func111($c);
			extract($dd);
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
				<td class="cc">Meter Maint.<br /> Fee</td>
				<td class="cc">Amount</td>
			</tr>

		<?php

		$head_me = false;
		endif;

		?>

		<tr class="item_bo">
			<td class="ll"><?php echo $OR1; ?></td>
			<td class="ll"><?php echo $NAME1; ?></td>
			<td class="rr"><?php echo $AMOUNT_COL; ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	<?php

	$item_count++;
	$sub_col += (float) @$AMOUNT_COL;
	endforeach; ?>

	<tr class="bord_top">
		<td colspan="2">SUB TOTAL</td>
		<td  class="rr"><?php echo number_format($sub_col,2)?></td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
	</tr>

</table>
<br />
<br />

<table cellpadding="0" cellspacing="0" class="w200">
	<tr>
		<td>Cash</td>
		<td>0.00</td>
	</tr>
	<tr>
		<td>Check</td>
		<td>0.00</td>
	</tr>

	<tr class="bord_top">
		<td>Sub total</td>
		<td>0.00</td>
	</tr>

</table>

<br />
<br />
<br />
<?php $pn++; ?>
<div class="page_break">&nbsp;</div>
<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>

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
	foreach($col2 as $c):

			$dd = help_collector_daily_report_func111($c, true);
			extract($dd);
	?>
		<tr class="item_bo">
			<td class="ll"><?php echo $OR1; ?></td>
			<td class="ll"><?php echo $NAME1; ?></td>
			<td class="rr"><?php echo $AMOUNT_COL; ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	<?php

	$sub_col += (float) @$AMOUNT_COL;
	endforeach; ?>

	<tr class="bord_top">
		<td colspan="2">SUB TOTAL</td>
		<td  class="rr"><?php echo number_format($sub_col,2)?></td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
	</tr>

</table>

<br />
<br />

<table cellpadding="0" cellspacing="0" class="w200">
	<tr>
		<td>Cash</td>
		<td>0.00</td>
	</tr>
	<tr>
		<td>Check</td>
		<td>0.00</td>
	</tr>

	<tr class="bord_top">
		<td>Sub total</td>
		<td>0.00</td>
	</tr>

</table>

<br />
<br />

<?php $pn++; ?>
<div class="page_break">&nbsp;</div>
<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>

<?php echo @$title1_x; ?>
<br />

<strong><?php echo $coll_name; ?></strong>


<table cellpadding="0" cellspacing="0" class="">
	<tr>
		<td>
			<!--- --->
				<table cellpadding="0" cellspacing="0" class="w200">
					<tr>
						<td>Cash</td>
						<td>0.00</td>
					</tr>
					<tr>
						<td>Check</td>
						<td>0.00</td>
					</tr>

					<tr class="bord_top">
						<td>Sub total</td>
						<td>0.00</td>
					</tr>

				</table>

			<!--- --->

		</td>
		<td>
			<!--- --->

				<table cellpadding="0" cellspacing="0" class="w200">
					<tr>
						<td>WATER BILL</td>
						<td>0.00</td>
					</tr>
					<tr>
						<td>NON-WATER BILL</td>
						<td>0.00</td>
					</tr>

					<tr class="bord_top">
						<td>Sub total</td>
						<td>0.00</td>
					</tr>

				</table>
			<!--- --->
		</td>
	</tr>
</table>


<br />
<br />

<hr />

<?php html_signature(); ?>









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

</style>

<?php
$cont_ttl = ob_get_contents();
ob_get_clean();

$cont_ttl = str_replace('--ttl_p--', $pn, $cont_ttl);

echo $cont_ttl;
?>
