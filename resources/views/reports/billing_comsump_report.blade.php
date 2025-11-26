<?php

?>
<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />
</p>



<p>
	Billing Summary
	<br />
	<?php  echo $mm_date; ?>
</p>

<?php 

$xx =1;


$g_cons 		= 0;
$g_consum 		= 0;
$g_curr_bill 	= 0;
$g_penalty 		= 0;
$g_discount 	= 0;
$g_arrear 		= 0;


// echo '<pre>';
// print_r($zone_arr);
// die();

foreach($zone_arr as $kk1 => $vv1):

?>

<strong><?php echo $vv1; ?></strong>
<table>
	
	<tr class="bord_all">
		<td>Classification</td>
		<td class="cc">Cons. count</td>
		<td class="cc">Usage CUM</td>
		<td class="rr">Water Sales</td>
		<td class="rr">Penalty</td>
		<td class="rr">Discount</td>
		<td class="rr">Arrears</td>
	</tr>
	
<?php 

	$x_cons 		= 0;
	$x_consum 		= 0;
	$x_curr_bill 	= 0;
	$x_penalty 		= 0;
	$x_discount 	= 0;
	$x_arrear 		= 0;



//~ echo '<pre>';
//~ print_r($clas_arr);
//~ die();


/*
if($kk1 == 5){
	echo '<pre>';
	
	foreach($clas_arr as $kk=>$vv){
		$mm = get_billing_info_total($curr_period, $kk, $kk1);
		print_r($mm->toArray());
	}
	
	die();
}
*/ 
foreach($clas_arr as $kk=>$vv)
{
	
	//~ SUM(billing_total) as  ttl_curr_bill,  
	//~ SUM(consumption)  ttl_consum,
	//~ SUM(arrears)  ttl_arrear,
	//~ SUM(discount) ttl_discount,
	//~ SUM(penalty) ttl_penalty,
	//~ COUNT(id) ttl_cons
	
	$mm = get_billing_info_total($curr_period, $kk, $kk1);
	$mm = $mm[0];
	
	$ttl_cons 		= number_format($mm->ttl_cons,0);
	$ttl_consum 	= number_format($mm->ttl_consum,0);
	$ttl_curr_bill 	= number_format($mm->ttl_curr_bill,2);
	$ttl_penalty 	= number_format($mm->ttl_penalty,2);
	$ttl_discount 	= number_format($mm->ttl_discount,2);
	$ttl_arrear 	= number_format($mm->ttl_arrear,2);
	
	
?>	
	<tr>
		<td><?php  echo $vv; ?></td>
		<td class="cc"><?php  echo $ttl_cons; ?></td>
		<td class="rr"><?php  echo $ttl_consum; ?></td>
		<td class="rr"><?php  echo $ttl_curr_bill; ?></td>
		<td class="rr"><?php  echo $ttl_penalty; ?></td>
		<td class="rr"><?php  echo $ttl_discount; ?></td>
		<td class="rr"><?php  echo $ttl_arrear; ?></td>
	</tr>
<?php 

	$x_cons 		+= ($mm->ttl_cons);
	$x_consum 		+= ($mm->ttl_consum);
	$x_curr_bill 	+= ($mm->ttl_curr_bill);
	$x_penalty 		+= ($mm->ttl_penalty);
	$x_discount 	+= ($mm->ttl_discount);
	$x_arrear 		+= ($mm->ttl_arrear);

}

?>

<?php 

/***/


	$mm = get_billing_info_total($curr_period, 'senior', $kk1);
	$mm = $mm[0];
	
	$ttl_cons 		= number_format($mm->ttl_cons,0);
	$ttl_consum 	= number_format($mm->ttl_consum,0);
	$ttl_curr_bill 	= number_format($mm->ttl_curr_bill,2);
	$ttl_penalty 	= number_format($mm->ttl_penalty,2);
	$ttl_discount 	= number_format($mm->ttl_discount,2);
	$ttl_arrear 	= number_format($mm->ttl_arrear,2);


?>

	<tr>
		<td>SENIOR</td>
		<td class="cc"><?php  echo $ttl_cons; ?></td>
		<td class="rr"><?php  echo $ttl_consum; ?></td>
		<td class="rr"><?php  echo $ttl_curr_bill; ?></td>
		<td class="rr"><?php  echo $ttl_penalty; ?></td>
		<td class="rr"><?php  echo $ttl_discount; ?></td>
		<td class="rr"><?php  echo $ttl_arrear; ?></td>
	</tr>


<?php

$x_cons 		+= ($mm->ttl_cons);
$x_consum 		+= ($mm->ttl_consum);
$x_curr_bill 	+= ($mm->ttl_curr_bill);
$x_penalty 		+= ($mm->ttl_penalty);
$x_discount 	+= ($mm->ttl_discount);
$x_arrear 		+= ($mm->ttl_arrear);

/*****/

$g_cons 		+= $x_cons;
$g_consum 		+= $x_consum;
$g_curr_bill 	+= $x_curr_bill;
$g_penalty 		+= $x_penalty;
$g_discount 	+= $x_discount;
$g_arrear 		+= $x_arrear;

?>	

<tr class="bord_top">
	<td>Sub Total</td>
	<td class="cc"><?php  echo number_format($x_cons,0); ?></td>
	<td class="rr"><?php  echo number_format($x_consum,0); ?></td>
	<td class="rr"><?php  echo number_format($x_curr_bill,2); ?></td>
	<td class="rr"><?php  echo number_format($x_penalty,2); ?></td>
	<td class="rr"><?php  echo number_format($x_discount,2); ?></td>
	<td class="rr"><?php  echo number_format($x_arrear,2); ?></td>
</tr>
	
</table>	
<br />
<br />

<?php if($xx>=4): ?>
<div class="page_break"></div>
<p>
	Billing Summary
	<br />
	<?php  echo $mm_date; ?>
</p>

	
<?php $xx=0; endif; ?>


<?php 


$xx++;

endforeach; ?>


<strong>GRAND TOTAL</strong>
<table>
	
	<tr class="bord_all">
		<td>Classification</td>
		<td class="cc">Cons. count</td>
		<td class="cc">Usage CUM</td>
		<td class="rr">Water Sales</td>
		<td class="rr">Penalty</td>
		<td class="rr">Discount</td>
		<td class="rr">Arrears</td>
	</tr>
	<tr class="bord_all">
		<td>Sub Total</td>
		<td class="cc"><?php  echo number_format($g_cons,0); ?></td>
		<td class="rr"><?php  echo number_format($g_consum,0); ?></td>
		<td class="rr"><?php  echo number_format($g_curr_bill,2); ?></td>
		<td class="rr"><?php  echo number_format($g_penalty,2); ?></td>
		<td class="rr"><?php  echo number_format($g_discount,2); ?></td>
		<td class="rr"><?php  echo number_format($g_arrear,2); ?></td>
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
 
<style>
*{
	font-family:'sans-serif';
	font-size:9px;
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
	border:1px solid #ccc;
}
.bord_bot td{
	border-bottom:1px solid #000;
}
.bord_top td{
	border-top:1px solid #000;
	padding-top:3px;
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

//~ $cont_ttl = str_replace('--ttl_p--', $pn, $cont_ttl);

echo $cont_ttl;

// die();
?>
