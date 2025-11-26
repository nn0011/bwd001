<?php

//~ echo '<pre>';
//~ echo count($penalty_result);
//~ print_r($penalty_result);
//~ die();

ob_start();
$npp = 46;
$pn  = 1;
$ttl_item = count($penalty_result);
$whil11=true;

$xx=0;


?>
<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
</p>



<?php 


$ttl_due = 0;
$ttl_bill = 0;

while($whil11): 

?>
<strong>Daily Penalty Report</strong>
<br />
<strong><?php echo date('l F d, Y', strtotime($date1)); ?></strong>
<br />
<strong><?php echo get_zone101($zone_id); ?></strong>
<br />
<br />

<table style="width:600px" cellpadding="0" cellspacing="0">
	<tr class="bord_all">
		<td>#</td>
		<td>Bill No.</td>
		<td>Account No.</td>
		<td>Account Name</td>
		<td class="rr">Penalty</td>
		<td class="rr">Water Bill</td>
	</tr>
	
	<?php $yy = 1;
	
	//foreach($penalty_result as $pr): 
	while(true):
	$pr = @$penalty_result[$xx];
	if(!$pr){break;}
	
	if($yy >= $npp){
		// break;
	}	
	
	$bill_net = $pr->curr_bill - $pr->discount;
	
	?>
	<tr>
		<td><?php echo $xx+1; ?></td>
		<td><?php echo $pr->bill_num_01; ?></td>
		<td><?php echo $pr->acct_no; ?></td>
		<td><?php echo $pr->fname.' '.$pr->lname; ?></td>
		<td class="rr"><?php echo round($pr->due1,2); ?></td>
		<td class="rr"><?php echo round($bill_net,2); ?></td>
	</tr>
	<?php
	
	$ttl_due += $pr->due1;
	$ttl_bill += $bill_net;

	
	$xx++;
	$yy++;	

	endwhile; ?>
	
	
	
	<tr class="bord_top">
		<td></td>
		<td></td>
		<td></td>
		<td>Sub Total</td>
		<td class="rr"><?php echo round($ttl_due,2); ?></td>
		<td class="rr"><?php echo round($ttl_bill,2); ?></td>
	</tr>
	
	
</table>

<?php

if($xx >= $ttl_item){
	break;
}


?>

<?php 
/*
<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>

<div class="page_break"></div>
*/ ?>

<?php 


$pn++;

endwhile; ?>

<br />
<br />
<br />
<br />


<div style="text-align:right;padding-left:300px">
	<table cellpadding="0" cellspacing="0" border="0"  style="width:300px;font-weight:bold;">
		<tr class="bord_bot">
			<td>CONSESSIONARE</td>
			<td class="rr"><?php echo number_format($xx,0); ?></td>
		</tr>
		
		<tr class="bord_bot">
			<td>GRAND TOTAL BILL</td>
			<td class="rr"><?php echo number_format($ttl_bill,2); ?></td>
		</tr>
		
		<tr class="bord_bot">
			<td>GRAND TOTAL PENALTY</td>
			<td  class="rr"><?php echo number_format($ttl_due,2); ?></td>
		</tr>
	</table>
</div>



<br />
<br />
<br />
<br />

<table style="width:600px;">
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
