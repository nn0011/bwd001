<?php 

$max_num = 50;
$pg1 = 1;


$ct1 = count($active_account);
$ttl_pg = ceil(($ct1/$max_num));
//~ echo $ct1;
//~ die();
?>

<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
</p>


<?php 

$ind1 = 0;

$aa1 = null;
for(;;):
?>
<div style="width:800px;position:relative;">
<strong>Reading Book</strong>
<br />
<strong><?php echo get_zone101($zone1); ?></strong>
<br />


<table class="tab21" cellspacing="0" cellpadding="0">
	<tr class="brd1">
<!--
		<td class="q0">&nbsp;</td>
-->
		<td class="q1">R.#</td>
		<td class="q2">Acct #</td>
		<td class="q3">Mtr #</td>
		<td class="q4">Name</td>
		<td class="q5 cc">Status</td>
		<td class="q6 cc"><?php echo date('F',strtotime($period_prev)); ?></td>
		<td class="q7 cc"><?php echo date('F',strtotime($period)); ?></td>
		<td class="q8 cc"><?php echo date('F',strtotime($period_next)); ?></td>
		<td class="q9 cc">Remarks</td>
	</tr>

	<?php 
	
		//~ foreach($active_account as $aa1): 
		for($x=1;$x<=$max_num;$x++):
		
		$aa1 = @$active_account[$ind1];
		
		$ind1++;
		if(empty($aa1)){break;}
		
		$prev1 =  '';
		$curr1 =  '';
		
		if(!empty($aa1->reading1))
		{
			$prev1 =  $aa1->reading1->prev_reading;
			$curr1 =  $aa1->reading1->curr_reading;
		}
		
		$stat1 = '';
		if($aa1->acct_status_key == 2){$start1='A';}
		if($aa1->acct_status_key == 3){$start1='A';}
		
		if($aa1->acct_status_key == 4){$start1='D';}
		if($aa1->acct_status_key == 5){$start1='D';}
		
		if($aa1->acct_status_key == 1){$start1='N';}
	
	?>
	<tr>
<!--
		<td class="q0"><?php echo $ind1; ?></td>
-->
		<td class="q1"><?php echo $aa1->route_id; ?></td>
		<td class="q2"><?php echo $aa1->acct_no; ?></td>
		<td class="q3"><?php echo $aa1->meter_number1; ?></td>
		<td class="q4"><?php echo substr($aa1->lname.', '.$aa1->fname,0,18); ?></td>
		<td class="q5 cc"><?php echo @$start1; ?></td>
		<td class="q6 cc"><?php echo @$prev1; ?></td>
		<td class="q7 cc"><?php echo @$curr1; ?></td>
		<td class="q8 bot1 cc">&nbsp;</td>
		<td class="q9 bot1 cc bor_le">&nbsp;</td>
	</tr>
	<?php endfor; ?>
	
	
</table>


<span class="ppg">pg <?php echo $pg1; ?> of <?php echo $ttl_pg; ?></span>

<?php  

	if(empty($aa1)){break;}
	$pg1++;

?>

	<div class="fot1"></div>




<?php 
endfor; ?>

<hr />
TOTAL : <?php echo ($ind1 -1); ?>

</div>


<style>
*{
	font-size:12px;
	font-family:arial;
}
.tab21{
	width:100%;
}	
.tab21 td{
	padding:2px;
}

.cc{text-align:center;}
.rr{text-align:right;}
.ll{text-align:left;}
.brd1 td{
	border:1px solid #ccc;
}
.brd1 td{
	border:1px solid #ccc;
}
td.bot1{
	border-bottom:1px solid #ccc;
}
.bor_le{
	border-left:10px solid #FFF;
	margin:0;padding:0;
}
.q1{width:40px;}
.q2{width:90px;word-break:break-all;}
.q3{width:90px;word-break:keep-all;}
.q4{width:150px;word-break:break-all;}
.q5{width:50px;}
.q6{}
.q7{}
.q8{}
.q9{}
@media print {
  .fot1{page-break-after: always;}
  .ppg{position:absolute;top:-10px;right:10px;}
  .ppg{display:inline-block;}
	
}
</style>
