<?php

?>
<p>
	<?php echo WD_NAME; ?>
	<br />
	<?php echo WD_ADDRESS; ?>
	<br />
</p>

<p>
	Monthly Billing Summary
	<br />
	<?php echo date('F Y', strtotime($full_date)); ?>
	<br />
	<?php echo $my_zone; ?>
</p>

<?php 

$x=0;
$mm = 0;
$bb = true;
$pn = 1;

$fpag = 53;
$npag = 53;

$count22 = 1;



$sub_ttl_dis = 0;
$sub_ttl1 = 0;
$sub_ttl_wsales = 0;
$sub_ttl_others = 0;

$sub_usage = 0;



?>


<table cellspacing="0" cellpadding="0">
	<tr class="head11 bord_all">
		<td class="vc1 tt0">#</td>
		<td class="vc1 tt1">Account No.</td>
		<td class="vc1 tt2">Concessionaires</td>
		<td class="vc1 tt3 cc">Classified</td>
		<td class="vc1 tt4 cc">Bill<br />Number</td>
		<td class="vc1 tt5 cc">Prev.<br />Reading</td>
		<td class="vc1 tt6 cc">Cur.<br />Reading</td>
		<td class="vc1 tt7 cc">Usage<br />CUM</td>
		<td class="vc1 tt8 cc">Water<br />Sales</td>
		<td class="vc1 tt9 cc">SC</td>
		<td class="vc1 tt9 cc">Arrear</td>
		<td class="vc1 tt9 cc">Others</td>
		<td class="vc1 tt10 cc">Total</td>
	</tr>

<?php 
for(;;):

if(!$bb){break;}

	?>	
	
	<?php 
	
		for($y=0;$y<=$fpag;$y++):
		
		$bb = @$billing1[$mm];
		
		if(!$bb){break;}
	
		$r1 = explode('||',$bb->read_PC);

		// 
		$nw_bill = (array) $bb->nw_bill->toArray();
		// ee($nw_bill, __FILE__, __LINE__);
		$ttl_nw_bill = 0;
		foreach($nw_bill as $k => $v) {
			$ttl_nw_bill +=  $v['amt_1'];
		}
		// 
		// 

		
		$sub_ttl1+= ( $bb->curr_bill - $bb->discount ) +$bb->bill_arrear->amount +$ttl_nw_bill;
		$sub_ttl_dis+= $bb->discount;
		$sub_ttl_wsales+= $bb->curr_bill;
		$sub_usage += $bb->consumption;
		$sub_ttl_others += $ttl_nw_bill;



	
	?>
	<tr class="item1">
		<td><?php echo $bb->account->route_id; ?></td>
		<td><?php echo $bb->account->acct_no; ?></td>
		<td><?php echo substr($bb->account->lname.', '.$bb->account->fname.' '.$bb->account->mi, 0,20); ?></td>
		<td class="cc"><?php //echo ctype_str($bb->account->acct_type_key);?> <?php 
			
			if($bb->discount > 0){
				echo 'SC';
			}else{
				echo ctype_str($bb->account->acct_type_key);
			}
			
		?></td>
		<td class="cc"><?php echo $bb->id; ?></td>
		<td class="cc"><?php echo $r1[0]; ?></td>
		<td class="cc"><?php echo $r1[1]; ?></td>
		<td class="cc"><?php echo $bb->consumption; ?></td>
		<td class="rr paR_5"><?php echo number_format($bb->curr_bill,2); ?></td>
		<td class="cc"><?php echo $bb->discount <= 0?'': number_format($bb->discount,2); ?></td>
		<td class="cc"><?php echo number_format($bb->bill_arrear->amount,2); ?></td><!-- ARREAR -->
		<td class="cc"><?php echo number_format($ttl_nw_bill,2); ?></td><!-- ARREAR -->
		<td class="rr paR_5"><?php echo number_format(($bb->curr_bill - $bb->discount)+$bb->bill_arrear->amount+$ttl_nw_bill,2); ?></td>
	</tr>
	<?php 
	
	$mm++;
	$count22++;
	endfor; ?>
	
	<?php 
	/*
	<tr class="item1 trun">
		<td colspan="5">Sub Total</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="cc"><?php echo number_format($sub_usage, 0); ?></td>
		<td class="rr paR_5"><?php echo number_format($sub_ttl_wsales, 2); ?></td>
		<td class="cc"><?php echo number_format($sub_ttl_dis, 2); ?></td>
		<td class="rr paR_5"><?php echo number_format($sub_ttl1,2); ?></td>
	</tr>
	*/ ?>

<?php
$fpag = $npag;
$pn++;

 endfor; ?>	


<tr class="item1 trun">
	<td colspan="7">Total Record : <?php echo number_format($count22 -1,0); ?></td>
	<td class="cc"><?php echo number_format($sub_usage, 0); ?></td>
	<td class="rr paR_5"><?php echo number_format($sub_ttl_wsales, 2); ?></td>
	<td class="cc"><?php echo number_format($sub_ttl_dis, 2); ?></td>
	<td class="cc"></td><!-- ARREAR -->
	<td class="cc"><?php echo number_format($sub_ttl_others, 2); ?></td><!-- ARREAR -->
	<td class="rr paR_5"><?php echo number_format($sub_ttl1,2); ?></td>
</tr>

	
</table>


<?php /*if($bb){
 ?>
<div class="page_break"></div>
<p class="page-number-x">Page <?php echo $pn; ?></p>
<?php }*/ ?>
<!--
<p class="page-number-x">Page <?php echo $pn; ?> / --ttl_p-- </p>
-->



<?php /*

<table cellspacing="0" cellpadding="0">
	
	<tr class="head11 bord_all">
		<td class="vc1 tt0">#</td>
		<td class="vc1 tt1">Account No.</td>
		<td class="vc1 tt2">Concessionaires</td>
		<td class="vc1 tt3 cc">Classified</td>
		<td class="vc1 tt4 cc">Bill<br />Number</td>
		<td class="vc1 tt5 cc">Prev.<br />Reading</td>
		<td class="vc1 tt6 cc">Cur.<br />Reading</td>
		<td class="vc1 tt7 cc">Usage<br />CUM</td>
		<td class="vc1 tt8 cc">Water<br />Sales</td>
		<td class="vc1 tt9 cc">SC</td>
		<td class="vc1 tt10 cc">Total</td>
	</tr>


	<tr class="item1 trun">
		<td colspan="7">Total Record : <?php echo number_format($count22 -1,0); ?></td>
		<td class="cc"><?php echo number_format($sub_usage, 0); ?></td>
		<td class="rr paR_5"><?php echo number_format($sub_ttl_wsales, 2); ?></td>
		<td class="cc"><?php echo number_format($sub_ttl_dis, 2); ?></td>
		<td class="rr paR_5"><?php echo number_format($sub_ttl1,2); ?></td>
	</tr>
	
</table>

*/ ?>
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
	font-size:11px;
	line-height:100%;
}
table{
	width:100%;
}
table *{
}
table td{
	border-bottom:0px solid #000;
	padding:0px;
	vertical-align:top;
	padding-bottom:3px;
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
}
.cc{
	text-align:center;
}
.vc1{vertical-align: middle;}

.under ,
.trun
td{
	border-top:1px solid #ccc;
	border-bottom:1px solid #ccc;
	font-weight:bold;
	font-size:11px;
	padding-top:5px;
	padding-bottom:5px;
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

.tt0{width:50px;}
.tt1{width:65px;}
.tt2{width:150px;}
.tt3{width:50px;}
.tt4{width:50px;}
.tt5{width:50px;}
.tt6{width:50px;}
.tt7{width:50px;}
.tt8{width:90px;}
.tt9{width:50px;}
.tt10{width:90px;}

.paR_5{padding-right:15px;}

</style>

<?php

$cont_ttl = ob_get_contents();
ob_get_clean();

$cont_ttl = str_replace('--ttl_p--', $pn, $cont_ttl);

echo $cont_ttl;

// die();
?>
