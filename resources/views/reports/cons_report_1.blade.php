<?php

ob_start();	
$npp = 40;
$pn  = 1;

?>

<p style="font-weight:bold;">
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
</p>

<?php
$seq = 0;
$item1 = 0;
$r = false;
while(true):
?>

<strong><?php echo @$report_name; ?></strong>
<br />
<strong><?php echo @$as_of_name; ?></strong>
<br />
<br />


<table style="width:100%;" align="center" cellpadding="0" cellspacing="0" class="tab22">
	
	<tr class="bord_all">
		<td style="width:30px;">NO.</td>
		<td style="width:60px;">ACCT #</td>
		<td>CONS.</td>
		<td style="width:80px;">ZONE</td>
		<td>METER #</td>
		<td style="width:80px;">DATE</td>
	</tr>
	
	<?php 

	//foreach($result1 as $r): 
	for($x=1;$x<=$npp;$x++):
		$seq++;
		$r = @$result1[$item1];
		if(!$r){break;}
	
	?>
	<tr>
		<td><?php echo $seq; ?></td>
		<td><?php echo $r->acct_no; ?></td>
		<td><?php echo strtoupper($r->fname.' '.$r->lname); ?></td>
		<td><?php echo strtoupper($r->zone_name); ?></td>
		<td><?php echo strtoupper($r->meter_number1); ?></td>
		<td><?php echo date('M d, Y', strtotime($r->led_date2)); ?></td>
	</tr>
	<?php 
	
	$item1++;
	
	endfor;
	
	//~ endforeach; /**/ ?>
	
	<?php /*
	<tr class="bord_top">
		<td>TOTAL</td>
		<td><?php echo number_format($ttl_cons, 0); ?></td>
		<td class="rr"><?php echo number_format($ttl_due, 2); ?></td>
		<td class="rr"><?php echo number_format($ttl_bill,2); ?></td>
	</tr>
	*/ ?>
	
</table>

<?php if($r){ ?>
<hr />
--- CONTINUE ----
<br />
<?php }else{ ?>
<hr />
--- END ----

<br />
<br />
<br />

<strong>TOTAL CONS. : <?php echo $seq - 1; ?></strong>

<?php } ?>
<br />
<br />
<br />

<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>

<?php 

if(!$r){break;}


?>
<div class="page_break"></div>

<?php 
$pn++;
endwhile;

?>







<style>
*{
	font-family:'Arial, Helvetica, sans-serif' !important;
	font-size:11px;
	color:#000000;
	font-weight:normal;
}
table{
	width:100%;
}
table *{
}
table td{
	border-bottom:0px solid #000;
	padding:2px;
}
.bord_all td{
	border:1px solid #000;
}
.bord_bot td{
	border-bottom:1px solid #000;
}
.bord_top td{
	border-top:1px solid #000;
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
	border-bottom:1px solid #000;
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
.tab22 td{
	padding:1px; 
}

</style>

<?php

$cont_ttl = ob_get_contents();
ob_get_clean();

$cont_ttl = str_replace('--ttl_p--', $pn, $cont_ttl);

echo $cont_ttl;

// die();
?>
