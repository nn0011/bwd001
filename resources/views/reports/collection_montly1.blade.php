<?php

ob_start();
$npp = 45;
$pn  = 1;

?>

<p style="font-size:10px;">
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
</p>


<strong>Monthly Collection Summary</strong>
<br />
<strong>All</strong>
<br />
<strong>For The Month of <?php echo $date3; ?></strong>
<br />
<br />
<br />


<table style="width:100%;" align="center" cellpadding="0" cellspacing="0">
	
	<tr class="bord_all">
		<td class="cc" style="width: 100px;">ZONE</td>
		<td class="cc">TOTAL COLLECTED</td>
		<td class="cc">CURRENT</td>
		<td class="cc">CURRENT YEAR <br /> ARREAR</td>
		<td class="cc">PREV YEAR <br /> ARREAR</td>
		<td class="cc">NON-WATER <br />COLLECTION</td>
		<td class="cc"  style="width: 100px;">W / TAX</td>
		<td class="cc">PENALTY</td>
	</tr>
	
	<?php 
	
$COL1 = 0;
$CUR1 = 0;
$CARR1 = 0;
$NWC = 0;
$WTAX = 0;
$PEN = 0;
$PREV_ARR = 0;
	
	foreach($rss1 as $r1): 
	
	
	?>
	<tr class="bod1 trun">
		<td><?php echo get_zone101($r1->zone_id); ?></td>
		<td class="rr"><?php echo $r1->COL1 <= 0?'':number_format($r1->COL1,2); ?></td>
		<td class="rr"><?php echo $r1->CUR1 <= 0?'':number_format(($r1->CUR1 + $r1->WTAX),2); ?></td>
		<td class="rr"><?php echo $r1->CARR1 <= 0?'':number_format($r1->CARR1,2); ?></td>
		<td class="rr"><?php echo $r1->PREV_ARR <= 0?'':number_format($r1->PREV_ARR,2); ?></td>
		<td class="rr"><?php echo $r1->NWC <= 0?'':number_format($r1->NWC,2); ?></td>
		<td class="rr"><?php echo $r1->WTAX <= 0?'':'( '.number_format($r1->WTAX,2).' )'; ?></td>
		<td class="rr"><?php echo $r1->PEN <= 0?'':' '.number_format($r1->PEN,2).' '; ?></td>
	</tr>
	<?php 
	
$COL1  += $r1->COL1;
$CUR1  += (@$r1->CUR1 + @$r1->WTAX);
$CARR1 += $r1->CARR1;
$NWC   += $r1->NWC;
$WTAX  += $r1->WTAX;	
$PEN   += $r1->PEN;
$PREV_ARR   += $r1->PREV_ARR;
	endforeach; ?>

	<tr class="bod1 bord_top">
		<td>TOTAL</td>
		<td class="rr"><?php echo $COL1 <= 0?'':number_format($COL1,2); ?></td>
		<td class="rr"><?php echo $CUR1 <= 0?'':number_format($CUR1,2); ?></td>
		<td class="rr"><?php echo $CARR1 <= 0?'':number_format($CARR1,2); ?></td>
		<td class="rr"><?php echo $PREV_ARR <= 0?'':number_format($PREV_ARR,2); ?></td>
		<td class="rr"><?php echo $NWC <= 0?'':number_format($NWC,2); ?></td>
		<td class="rr"><?php echo $WTAX <= 0?'':'( '.number_format($WTAX,2).' )'; ?></td>
		<td class="rr"><?php echo $PEN <= 0?'':' '.number_format($PEN,2).' '; ?></td>
	</tr>	
	
</table>

<?php 
//~ get_zone101($zone_id)
?>


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
			<span class="bl1">Checked by:</span>
			<br />
			<br />
			<br />
			<div class="cc" style="display:inline;border-bottom:1px solid #000;">
				<?php echo REP_SIGN2; ?>
			</div>
			<br />
			<?php echo REP_SIGN2_TITLE; ?>
		</td>
	</tr>
</table>

<br />
<br />


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
	font-size:8px;
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
	text-align:center !important;
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
.bod1 td{
	padding-left:10px;
	padding-right:15px;
	padding-top:5px;
}
.bord_all td{
	font-size:10px !important;
	border-bottom:1px solid #cccccc !important;
}

</style>

<?php

$cont_ttl = ob_get_contents();
ob_get_clean();

$cont_ttl = str_replace('--ttl_p--', $pn, $cont_ttl);

echo $cont_ttl;

// die();
?>
