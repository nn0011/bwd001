<?php

ob_start();
$npp = 45;
$pn  = 1;


//~ echo $_GET['iid'];
//~ die();


//~ 'date1_x',
//~ 'title1_x',
//~ 'coll',
//~ 'col2',
//~ 'coll_name',
//~ 'user'
//~ $ll_res;

$my_date = date('F d, Y', strtotime(@$_GET['dd']));


?>
<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
</p>

<?php 

$sum_ttl = array();
$cc1 = 0;

foreach($ll_res as $ll):

if(empty($ll)){continue;}

extract($ll);


?>
<!--
<br />

<hr/>
<h1 style="font-size:14px;"><?php echo strtoupper($user->name); ?></h1>
<hr/>
-->

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
	
	
	$colls_ttl_collected = 0;


	$col2 = [];

	if(empty($coll))
	{
		ob_get_clean();
		echo 'No Record Found';
		echo '<br />';
		echo $date1_x;
		die();
	}


	foreach($coll as $c):

			$dd = help_collector_daily_report_func555($c);
			extract($dd);

	?>

	<?php

			if($item_count >= $npp)
			{
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
		
		
		<?php /*
		<?php echo @$date1_x; ?>
		<br />
		<?php echo @$title1_x; ?>
		<br />
		*/ ?>
		
		<strong>Daily Collection Report - <?php echo $my_date; ?></strong><br/>
		<strong><?php echo strtoupper($user->name); ?> - OFFICIAL RECEIPT</strong>
		<table cellpadding="0" cellspacing="0">
			<tr class="bord_all">
				<td class="rq1">Reciept #</td>
				<td class="rq2">Payor</td>
				<td class="rq3 cc">Amount<br /> Collected</td>
				<td class="rq4 cc">Current</td>
				<td class="rq5 cc">Arrears<br />(CY)</td>
				<td class="rq6 cc">Arrears<br />(PY)</td>
				<td class="rq7 cc">Meter Fee</td>
				<td class="rq8 cc">Amount</td>
			</tr>

		<?php

		$head_me = false;
		endif;

		?>

		<tr class="item_bo">
			<td class="ll"><?php echo $OR1; ?></td>
			<td class="ll"><?php echo substr($NAME1,0,30); ?></td>
			<td class="rr"><?php echo $AMOUNT_COL; ?></td>
			<td class="rr"><?php echo $A1//echo @$c->xxx_bill; ?></td>
			<td class="rr"><?php echo $A2//echo @$c->xxx_arre; ?></td>
			<td></td>
			<td></td>
			<td></td>
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
			<td class="ll"><?php //echo $OR1; ?></td>
			<td class="rr" colspan="7"><?php echo 'GL CODE # '.$c->nw_glsl.'&nbsp;&nbsp;&nbsp;'.$c->nw_desc; ?></td>
			
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
	if(@$AMOUNT_COL != '')
	{
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
		<td  class="rr"></td>
		<td  class="rr"></td>
		<td  class="rr"></td>
		<td  class="rr"></td>
		<td  class="rr"></td>
	</tr>

	<?php if(empty($col2)){ ?>

	<tr class="bord_top trun bld_me" style="font-size:14px;">
		<td colspan="2">TOTAL</td>
		<td  class="rr"><?php echo number_format($sub_colZZ,2)?></td>
		<td  class="rr"></td>
		<td  class="rr"></td>
		<td  class="rr"></td>
		<td  class="rr"></td>
		<td  class="rr"></td>
	</tr>

	<?php } ?>
	
	

</table>

<?php 

$colls_ttl_collected  +=  $sub_colZZ;

$sum_ttl[$cc1]['name'] = strtoupper($user->name);
$sum_ttl[$cc1]['ttl'] = $sub_colZZ;
$sum_ttl[$cc1]['count'] = $item_count -1;

$cc1++;

continue;
continue;
continue;
continue;


if(empty($col2)){continue;}

?>


<?php
$G_cash_ttl1 += $cash_ttl1;
$G_check_ttl1 += $check_ttl1;
$G_non_water_ttl1 += $non_water_ttl1;
$G_wtax_ttl1 += $wtax_ttl1;
$G_ada_ttl1 += $ada_ttl1;
$G_water_ttl1 += $water_ttl1;
?>

<br />
<strong> <?php echo strtoupper($user->name); ?> COLLECTOR RECEIPT</strong>
<table cellpadding="0" cellspacing="0">
	<tr class="bord_all">
		<td class="rq1">Reciept Number</td>
		<td class="rq2">Payor</td>
		<td class="rq3 cc">Amount<br /> Collected</td>
		<td class="rq4 cc">Current</td>
		<td class="rq5 cc">Arrears<br />(CY)</td>
		<td class="rq6 cc">Arrears<br />(PY)</td>
		<td class="rq7 cc">Meter Maint.<br /> Fee</td>
		<td class="rq8 cc">Amount</td>
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

			$dd = help_collector_daily_report_func555($c, true);
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
	
<?php $colls_ttl_collected  +=  $sub_colZZ;?>
	
	<tr class="bord_top trun bld_me">
		<td colspan="2">TOTAL</td>
		<td  class="rr"><?php echo number_format($colls_ttl_collected,2)?></td>
		<td  class="rr"><?php echo number_format($curr_bill_total,2)?></td>
		<td  class="rr"><?php echo number_format($curr_arrear_total,2)?></td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
		<td  class="rr">0.00</td>
	</tr>	
	
</table>


<?php endforeach; ?>

<br />
<br />
<br />
<strong>SUMMARY</strong>
<br />

<?php 

//~ $sum_ttl[$cc1]['name'] = strtoupper($user->name);
//~ $sum_ttl[$cc1]['ttl'] = $sub_colZZ;
//~ $sum_ttl[$cc1]['count'] = $item_count;

?>

<table cellpadding="0" cellspacing="0" style="width:400px;"> 
	<tr class="bord_all">
		<td>NAME</td>
		<td class="cc">No. Trans.</td>
		<td class="rr">Collected</td>
	</tr>
	<?php
	
	$ttl_count = 0;
	$ttl_ttl   = 0;
	foreach($sum_ttl as $m): 
	
	$ttl_count+= $m['count'];
	$ttl_ttl+= $m['ttl'];
	
	?>
	<tr class="bord_all">
		<td><?php echo $m['name']; ?></td>
		<td class="cc"><?php echo $m['count']; ?></td>
		<td class="rr"><?php echo number_format($m['ttl'], 2); ?></td>
	</tr>	
	<?php endforeach; ?>
	<tr class="bord_all" style="font-weight:bold;">
		<td>Total</td>
		<td class="cc"><?php echo $ttl_count; ?></td>
		<td class="rr"><?php echo number_format($ttl_ttl, 2); ?></td>
	</tr>	

</table>



<br />
<br />
<br />
<br />

<table style="width:100%;">
	<tr>
		<td class="cc">
			<span class="bl1">Prepared By :</span>
			<br />
			<br />
			<br />
			<div class="cc"  style="display:inline;border-bottom:1px solid #000;">
				<?php echo REP_SIGN1; ?>
			</div>
			<br />
			<?php echo REP_SIGN1_TITLE; ?>
		</td>
		<td class="cc">
			<span class="bl1">Approved by:</span>
			<br />
			<br />
			<br />
			<div class="cc" style="display:inline;border-bottom:1px solid #000;">
				<?php echo WD_MANAGER; ?>
			</div>
			<br />
			<?php echo WD_MANAGER_RA; ?>
		</td>
	</tr>
</table>

<br />
<br />





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

.rq1{width:70px;}
.rq2{width:200px;}
.rq3{width:100px;}
.rq4{width:70px;}
.rq5{width:70px;}
.rq6{}
.rq7{}
.rq8{}

</style>

<?php

$cont_ttl = ob_get_contents();
ob_get_clean();

$cont_ttl = str_replace('--ttl_p--', $pn, $cont_ttl);

echo $cont_ttl;

// die();
?>
