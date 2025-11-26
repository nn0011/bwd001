
<table class="table10 table-bordered  table-hover"><tbody>
	
	<tr class="headings">
		<td width="10%">Account No.</td>
		<td width="50%">Name</td>
		<td width="3%" style="text-align:right;">Period </td>
		<td width="3%" style="text-align:right;">Collectable</td>
		<td width="3%" style="text-align:right;">Collected</td>
		<td width="3%" style="text-align:right;">Balance</td>
	</tr>
	
	<?php if(empty($accts['data'])): ?>
		<tr>
			<td colspan="6" style="padding:20px;text-align:center;">
				<span style="font-size:18px;">No Result</span>
			</td>
		</tr>
	<?php endif; ?>
	
	<?php foreach($accts['data'] as $acc): 
	
					$total_collected = 0;
					$bal_payment = 0;
					
					if(!empty($acc['reading1']['billing'])){
							extract($acc['reading1']['billing']);
							if(!empty($collection)){
							
									if($collection[0]['balance_payment']  <  0){
										$bal_payment =  abs($collection[0]['balance_payment'] );
									}
									
									foreach($collection as $collect){
										$total_collected+= $collect['payment'];
									}
							}
					}
					
					$total_collected+=$bal_payment;
	
	?>
	<!------>
	<!------>
	<tr data-index="0" data-box1="" class="cursor1  rowx0  ">
		<td onclick=""><?php echo $acc['acct_no']; ?></td>
		<td>
				<?php echo $acc['lname'].', '.$acc['fname'].'  '.$acc['mi']; ?>				
				<p style="font-size:10px;"><?php echo $acc['address1']; ?></p>
		</td>
		<td style="text-align:right;"><?php echo date('F Y', strtotime($r_year.'-'.$r_month)); ?></td>
		<td style="text-align:right;">
			&#x20b1; <?php echo number_format(@$billing_total, 2); ?>
		</td>		
		<td style="text-align:right;">
			&#x20b1; <?php echo number_format($total_collected, 2); ?>
		</td>		
		<td style="text-align:right;">
			<span class="rd">&#x20b1;  <?php echo number_format((@$billing_total - $total_collected ), 2); ?></span>
		</td>		
	</tr>
	<!------>
	<!------>
	<?php endforeach; ?>
</tbody></table>
