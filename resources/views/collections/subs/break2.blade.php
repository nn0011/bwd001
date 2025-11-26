<tr class="headings">
	<td class="ltxt" colspan="3">BILLING BREAKDOWN</td>
</tr>

<?php

$my_total = 0;

$indx = 0;
foreach($new_break_down as $pb)
{
	$pb = (object) $pb;

	$my_total += round($pb->amt, 2);
?>
<tr>
	<td>
		<input type="checkbox" onclick="breakdown_payable_input_change()" class="breakdown_payable_input" name="breakdown_payable_input[]"  value="<?php echo  $indx; ?>" checked />
	</td>
	<td>
	<?php 
	
	if( $pb->typ != 'other_payable' ){
		$dd1 = $pb->typ.' - '.date('M-Y', strtotime($pb->period));
	}else{
		$dd1 = @$pb->other_payable.' - '.date('M-Y', strtotime($pb->period));
	}
	
	
	//~ $dd1 = str_replace('SENIOR CITIZEN','', $pb->desc);
	
	
	echo strtoupper($dd1); 
	
	?></td>
	<td  class="rtxt"><?php echo number_format((float) abs(@$pb->amt),2); ?></td>
</tr>
<?php 
$indx++;
} ?>

<tr  class="headings">
	{{-- <td colspan="2"   class="ltxt">Total</td> --}}
	<td colspan="3"  class="rtxt"  style="font-size: 18px !important"><?php echo  number_format($my_total, 2); ?></td>
</tr>
