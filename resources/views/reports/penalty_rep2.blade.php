<?php

ob_start();
$npp = 45;
$pn  = 1;

?>

<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
</p>


<strong>Penalty Report Summary of <?php echo date('F Y', strtotime($date1)); ?></strong>
<br />
<strong>as of <?php echo date('l F d, Y', strtotime($date1)); ?></strong>
<br />
<br />
<br />
<br />


<table style="width:500px;" align="center" cellpadding="0" cellspacing="0">
	
	<tr class="bord_all">
		<td>ZONE</td>
		<td>CONS. NO.</td>
		<td class="rr">PENALTY</td>
		<td class="rr">BILLING</td>
	</tr>
	
	<?php 
	$ttl_due = 0;
	$ttl_bill = 0;
	$ttl_cons = 0;
	
	foreach($res1 as $r): ?>
	<tr>
		<td><?php echo $r['zone']['zone_name']; ?></td>
		<td><?php echo number_format($r['data'][0]->ID, 0); ?></td>
		<td class="rr"><?php echo number_format($r['data'][0]->DUE1, 2); ?></td>
		<td class="rr"><?php echo number_format($r['data'][0]->CURR_BILL,2); ?></td>
	</tr>
	<?php 
	
	$ttl_due  += @$r['data'][0]->DUE1;
	$ttl_bill += @$r['data'][0]->CURR_BILL;
	$ttl_cons += @$r['data'][0]->ID;
	
	endforeach; ?>
	
	<tr class="bord_top">
		<td>TOTAL</td>
		<td><?php echo number_format($ttl_cons, 0); ?></td>
		<td class="rr"><?php echo number_format($ttl_due, 2); ?></td>
		<td class="rr"><?php echo number_format($ttl_bill,2); ?></td>
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
				<?php echo REP_SIGN3; ?>
			</div>
			<br />
			<?php echo REP_SIGN3_TITLE; ?>
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





<br />
<br />
<br />
<br />







<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>

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
	padding-top:15px;
	font-weight:bold;
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
