<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />
As of <?php echo date('F d, Y'); ?>
</p>

<h2><?php echo strtoupper(reading_label1()); ?></h2>
<h3><?php echo get_zone101(@$zone); ?></h3>

<table cellpadding="0" cellspacing="0"><tbody>

	<tr class="headings">
		<td width="7%">Account No.</td>
		<td width="25%">Name</td>
		<td width="10%">Zone</td>
		<td width="5%">Prev</td>
		<td width="5%">Curr</td>
		<td width="5%">C.U.</td>
	</tr>

	<?php if(empty($reading11['data'])): ?>
	<tr>
		<td colspan="9" style="text-align:center;padding:30px;">
				<span style="font-size:24px;">No Result</span>
		</td>
	</tr>
	<?php endif; ?>

	<?php
	


	$index1 = 0;
	if(!empty($reading11))
	{
			
	//foreach($acct_reading['data'] as $acct1):	
	
	$tab_index = 1;
	
	foreach($reading11['data'] as $read11):
	
		$acct1 = $read11['account1'];
		
		
		$currR1     =  (int) @$read11['curr_reading'];
		$prevR1    =  (int) @$read11['prev_reading'];
		$consump =  (int) @$read11['current_consump'];
		
		
		$full_name = substr($acct1['fname'].' '.$acct1['lname'], 0 ,40);
	

	?>
	
	<tr>
		<td><?php echo $read11['account_number']; ?></td>
		<td><?php echo $full_name; ?></td>
		<td><?php echo get_zone101(@$acct1['zone_id']); ?></td>
		<td><?php echo $prevR1; ?></td>
		<td><?php echo $currR1; ?></td>
		<td><?php echo $consump;?></td>
	</tr>
	<!------>
	<!------>
	<?php $index1++;
	$tab_index++;
	 endforeach;
	
}
	 ?>	
	 
<tr>
	<td colspan="6" style="border-bottom:1px solid #000;"></td>
</tr>

</tbody></table>
--- END ----


<style>
*{
	font-size:12px;
	font-family:sans-serif;
}	
table{
	width:100%;
}
.headings td{
	border:1px solid #000;
	padding:5px;
}
td{
	padding:3px !important;
}
</style>
