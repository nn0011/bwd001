<?php

//full_date

$curr_date  = date('Y-m-d');

$no_month = (int) @$_GET['mo'];
if($no_month<=1){$no_month = 1;}



//~ [last_id] => 308692
//~ [acct_id] => 1004
//~ [ttl_bal] => 4336.18
//~ [date01] => 2019-08-02
//~ [acct_no] => 211207755
//~ [fname] =>
//~ [lname] => HWD OFFICE-1
//~ [old_route] => 10


?>
<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />



	<?php

	$max_item = 50;

	$xx = $max_item;
	$xd=0;
	$page =1;
	$xxx = 1;
	
	
	$total1010 = 0;

	foreach($result1 as $rs1):

		if($rs1->PER2 < $no_month){
			continue;
		}
		
		
		$total1010 += $rs1->ttl_bal;

	?>

	<?php if($xx >= $max_item){$xx=0;
			//~ $max_item = 60;

		?>
	</table>
	<?php if($xd != 0): ?>
	<div class="page_break"></div>
	<?php endif; ?>
	<p class="page-number-x">Page <?php echo $page; ?> </p>

Disconnection List as of <?php echo date('F d, Y'); ?>
<br />
<?php echo $zon_name; ?>
<br />
<br />


	<table style="width:100%" cellpadding="0" cellspacing="0">
		<tr class="bld_me trun">
			<td>#</td>
			<td>Account #</td>
			<td>Account Name</td>
			<td>Address</td>
			<td>Meter #</td>
			<td>No. Months</td>
			<td>Readings</td>
			<td>Amount</td>
			<td>Remarks</td>
		</tr>
	<?php $page++;} ?>

	<tr class="">
		<td><?php echo $xxx; ?></td>
		<td><?php echo $rs1->acct_no; ?></td>
		<td><?php echo strtoupper( $rs1->fname.' '.$rs1->lname); ?></td>
		<td><?php echo strtoupper( substr($rs1->address1,0,20)); ?></td>
		<td><?php echo substr(strtoupper( $rs1->meter_number1),0,50); ?></td>
		<td class="ch1"><?php echo ($rs1->PER2); ?></td>
		<td><?php echo ( $rs1->curr_reading); ?></td>
		<td class="rh1"><?php echo number_format($rs1->ttl_bal,2); ?></td>
		<td class="under"></td>
	</tr>

	<?php

	$xd++;
	$xx++;
	$xxx++;

	endforeach; 
	

?>

<?php if( empty($result1) ): ?>


Disconnection List as of <?php echo date('F d, Y'); ?>
<br />
<?php echo $zon_name; ?>
<br />
<br />


	<table style="width:100%" cellpadding="0" cellspacing="0">
		<tr class="bld_me trun">
			<td>#</td>
			<td>Account #</td>
			<td>Account Name</td>
			<td>Address</td>
			<td>Meter #</td>
			<td>No. Months</td>
			<td>Readings</td>
			<td>Amount</td>
			<td>Remarks</td>
		</tr>


<?php endif; ?>



	<tr class="bld_me trun trtop">
		<td colspan="4">TOTAL</td>
<!--
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
-->
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="rh1"><?php echo number_format($total1010, 2); ?></td>
		<td class="under"></td>
	</tr>
	
</table>
<br />
<br />

<hr />

<?php html_signature(); ?>


<style>
*{
	font-family:'serif';
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

.under ,
.trun
td{
	border-bottom:1px solid #ccc;
}

.above ,
.trtop
td{
	border-top:1px solid #ccc;
}


.lh1{
	text-align:left;
}
.rh1{
	text-align:right;
	padding-right:10px;
}
.ch1{
	text-align:center;
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

</style>
