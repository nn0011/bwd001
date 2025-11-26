<tr class="headings">
	<td class="ltxt" colspan="2">BILLING BREAKDOWN</td>
</tr>

<?php

foreach($payment_breakdown as $pb){
	$pb = (object) $pb;
?>
<tr>
	<td><?php 
	
	$dd1 = $pb->desc;
	
	
	//~ $dd1 = str_replace('SENIOR CITIZEN','', $pb->desc);
	
	
	echo strtoupper($dd1); 
	
	?></td>
	<td  class="rtxt"><?php echo number_format((float) abs(@$pb->pre_val),2); ?></td>
</tr>
<?php } ?>


<?php 
/*
<tr>
	<td>Billing</td>
	<td  class="rtxt"><?php echo number_format(@$result1->curr_bill,2); ?></td>
</tr>

<tr>
	<td>Arrear</td>
	<td  class="rtxt"><?php echo number_format(@$arrear->amount,2); ?></td>
</tr>

<tr>
	<td>Penalty</td>
	<td  class="rtxt"><?php echo number_format(@$result1->penalty,2); ?></td>
</tr>

<tr>
	<td>Senior</td>
	<td  class="rtxt red "><?php echo number_format(@$result1->discount,2); ?></td>
</tr>

<tr>
	<td>Adjustment</td>
	<td  class="rtxt red "><?php echo number_format(@$adjustment,2); ?></td>
</tr>		
*/?>
