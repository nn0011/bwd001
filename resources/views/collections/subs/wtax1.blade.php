<?php

//~ echo '<pre>';
//~ print_r(@$arrear->toArray());
//~ die();


// echo '<pre>';
// 	print_r($balance->toArray());
// die();

$bill_amt = (@$led_data->ttl_bal);
$amt_due  = (@$balance->ttl_bal);
$w_tax1   = 0;

if($tax1)
{
	$tax_per = 0.02;
	$bill_amt = $bill_amt;
	$w_tax1   = (@$led_data->ttl_bal * $tax_per) - $tax_val;
	$amt_due  = (@$balance->ttl_bal) - $w_tax1;
	
	if(@$led_data->ttl_bal <= 0){
		$amt_due = 0;
	}
}

// echo $amt_due;
// die();


?>
<tr>
	<td><small>Bill</small></td>
	<td class="rtxt  bill_amt"><?php echo number_format(@$bill_amt, 2); ?></td>
</tr>

<!--
<tr>
	<td><small>Current Bill</small></td>
	<td class="rtxt  bill_amt"  ><?php echo number_format(@$arrear11->billing, 2); ?></td>
</tr>
-->

<tr>
	<td><small>Arrear</small></td>
	<td class="rtxt  bill_amt"  ><?php echo number_format(@$arrear->amount, 2); ?></td>
</tr>

<?php /*
<tr class="red">
	<td><small>W/Tax</small></td>
	<td class="rtxt w_tax1"><?php echo number_format($w_tax1, 2); ?></td>
</tr>*/ ?>

<tr class="red">
	<td><small>Due</small></td>
	<td class="rtxt amt_due">
		<?php //echo number_format($amt_due, 2); ?>
		<input type="number"  min="0" class="amt_due_txt rtxt" 
			placeholder="0.00"  value="<?php echo @$amt_due; ?>" 
			style="border: 0;margin-right: -17px !important;" 
			onchange="amt_due_change()"
			readonly
			/>		
	</td>
</tr>

<tr class="red">
	<td><small>Recieve</small></td>
	<td class="rtxt">
		<input type="number"  min="0" class="amount_recieve rtxt" 
		placeholder="0.00"  onchange="update_amount_recieve()" />
	</td>
</tr>

<tr>
	<td><small>Change</small></td>
	<td class="rtxt amt_change">0.00</td>
</tr>
<tr>
	<td>
		<input type="hidden"  name="bill91"   class="bill91"  value="<?php echo @$bill_amt; ?>" />
		<input type="hidden"  name="tax91"    class="tax91"   value="<?php echo @$w_tax1; ?>" />
		<input type="hidden"  name="due91"    class="due91"   value="<?php echo @$amt_due; ?>" />
		<input type="hidden"  name="bill_id91"    class="bill_id91"   value="<?php echo @$bill_id; ?>" />
	</td>
	<td></td>
</tr>




