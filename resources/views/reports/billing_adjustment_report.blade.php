<?php 



$max_num = 6;
$pg1 = 1;

?>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>

<div style="width:800px;position:relative;">
<h3>Billing Adjustment Summary</h3>

<table class="tab2" cellpadding="0" cellspacing="0" align="right">
	<tr style="font-weight:bold;text-decoration:underline;">
		<td colspan="2">&nbsp;</td>
		<td class="val1 rr">DEBIT</td>
		<td class="val1 rr">CREDIT</td>
	</tr>
</table>


<?php 

$ii = 0;
$oo = 0;

$ttl_debit  = 0;
$ttl_credit = 0;


$ct1 = count($ret3);
$ttl_pg = ceil(($ct1/$max_num));
//~ echo ($ttl_pg);
//~ die();


//~ echo '<pre>';
//~ print_r(@$ret3[$ii]);
//~ die();

for(;;): 

	$rr = @$ret3[$ii];
	if(empty($rr)){break;}

	$ii++;
	$oo++;
	
		
	//~ if($rr->adj_typ == '45|senior citizen'){
		//~ continue;
	//~ }
	
	

?>


<table style="width:100%;">
	
	<tr>
		<td class="item111">

Date : <?php echo date('l, F d, Y', strtotime($rr->date1)); ?>
<table class="tab1" cellpadding="0" cellspacing="0">
	<tr class="brd1">
		<td>BAM No.</td>
		<td>Account No.</td>
		<td>Name of Concessionaire</td>
		<td  class="cc">Bill Number</td>
		<td  class="cc">As Billed</td>
		<td  class="cc">Should be</td>
	</tr>
	
<?php 
	
	$prev_read = '';
	$curr_read = '';
	$bill_id = '';
	
	if(!empty(@$rr->bill1))
	{
		$ww1= explode('||', @$rr->bill1->read_PC);
		$prev_read = @$ww1[0];
		$curr_read = @$ww1[1];
		$bill_id = @$rr->bill1->id;
	}
	
?>
	
	<tr>
		<td>B-<?php echo $rr->bam; ?></td>
		<td><?php echo $rr->acct_no; ?></td>
		<td><?php echo strtoupper($rr->acct1->fname.' '.$rr->acct1->lname); ?></td>
		<td class="cc"><?php echo $bill_id; ?></td>
		<td class="cc"><?php echo $curr_read; ?></td>
		<td class="cc"><?php echo $curr_read; ?></td>
	</tr>
</table>

<table class="tab2" cellpadding="0" cellspacing="0" align="right">
	<?php 
	
		$led1 = $rr->ledger1;
	
	?>
	
	<tr>
		<td colspan="4">
			<?php echo $rr->adj_typ_desc; ?>
		</td>
<!--
		<td>Debit</td>
		<td>Credit</td>
-->
	</tr>
	
	<?php 
		
		$ar1 = number_format(@$led1->bill_adj ,2);
		$ar2 = '';
		
		if(@$led1->bill_adj < 0){
			$ar2 = number_format(abs(@$led1->bill_adj) ,2);
			$ar1 = '';
		}
		
		
		$ttl_debit  += @$led1->bill_adj; 
		$ttl_credit += @$led1->bill_adj; 
		
		
	
	?>
	<tr>
		<td>500-600</td>
		<td>METERED SALES TO GEN. CUSTOMERS</td>
		<td class="val1 rr"><?php echo $ar1; ?></td>
		<td class="val1 rr"><?php echo $ar2; ?></td>
	</tr>

	<tr>
		<td class="val1">125-121-01</td>
		<td class="val1">AR - CUSTOMER</td>
		<td class="val1 rr"><?php echo $ar2; ?></td>
		<td class="val1 rr"><?php echo $ar1; ?></td>
	</tr>


	
</table>


<div style="clear:both;"></div>
<br />
<br />


</td>
</tr>
</table>

<?php 

if($oo >= $max_num){
	
	
?>
<span class="ppg">pg <?php echo $pg1; ?> of <?php echo $ttl_pg; ?></span>
</div>

<?php
	$rr = @$ret3[$ii];
	if(empty($rr)){break;}
?>
<div class="fot1"></div>
<div style="width:800px;position:relative;">
<h3>Billing Adjustment Summary</h3>


<table class="tab2" cellpadding="0" cellspacing="0" align="right">
	<tr style="font-weight:bold;text-decoration:underline;">
		<td colspan="2">&nbsp;</td>
		<td class="val1 rr">DEBIT</td>
		<td class="val1 rr">CREDIT</td>
	</tr>
</table>

<?php $oo=0; $pg1++;} ?>


<?php endfor; ?>


<hr />

<table class="tab2" cellpadding="0" cellspacing="0" align="right">
	<tr style="font-weight:bold;text-decoration:underline;">
		<td colspan="2">&nbsp;</td>
		<td class="val1 rr">DEBIT</td>
		<td class="val1 rr">CREDIT</td>
	</tr>
</table>

<table class="tab2" cellpadding="0" cellspacing="0" align="right" style="font-weight:bold;width:800px;">

	<tr>
		<td>GRAND TOTAL</td>
		<td>&nbsp;</td>
		<td class="val1 rr"><?php echo number_format($ttl_debit,2); ?></td>
		<td class="val1 rr"><?php echo number_format($ttl_credit,2); ?></td>
	</tr>
	
</table>
<div style="clear:both;"></div>

<hr />

</div>



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
	font-size:10px;
}

.tab1,.tab2{
	width:600px;
}	
.tab1 td, .tab2 td{
	border:0px solid #ccc;
	padding:3px;
}
.tab2{
	margin-top:10px;
	width:600px;
}

.val1{
	width:70px;
}
.brd1 td{
	border:1px solid #ccc;
}
.cc{text-align:center;}
.rr{text-align:right;}
.ll{text-align:left;}
.repeat1{position:fixed;}
.item111{}

.ppg{display:none;}

@media print {
  .fot1{page-break-after: always;}
  .ppg{position:absolute;top:-10px;right:10px;}
  .ppg{display:inline-block;}
	
}

</style>
