<?php
$acct_reading['data'] = $billing_res['data'];
?>
<div style="float:right;display:inline-block;color:red;font-weight: bold;">Current Date :  <?php  echo date('F d, Y');  ?></div>
<br />
@include('billings.inc.billing_billing.reading_inc.acct_filter1')
<h2>Period :  <span class="rd"><?php echo date('F Y', strtotime($r_year.'-'.$r_month)); ?></span></h2>

<!------>
<!------>
<table class="table10 table-bordered  table-hover"><tbody>
	
	<tr class="headings">
		<td width="10%">Account No.</td>
		<td width="50%">Name</td>
		<td width="3%"  style="text-align:right;">Previous </td>
		<td width="3%"  style="text-align:right;">Current </td>
		<td width="3%"  style="text-align:right;">C.U.</td>
		<td width="7%"  style="text-align:right;">Sub Total</td>
		<td width="15%"  style="text-align:right;">Discounts</td>
		<td width="7%"  style="text-align:right;">Total</td>
	</tr>
	
	<?php //for($x=0;$x<=30;$x++): ?>
	<?php 
	

	$index1 = 0;
	foreach($acct_reading['data'] as $acct1): ?>
	<!------>
	<!------>
	<tr data-index="<?php echo $index1; ?>" data-box1="" class="cursor1  rowx<?php  echo $index1;  ?>  ">
		<td onclick="" ><?php echo $acct1['acct_no']; ?></td>
		<td>
				<?php echo $acct1['lname']; ?>, <?php echo $acct1['fname']; ?> <?php echo $acct1['mi']; ?>
				<p style="font-size:10px;"><?php echo $acct1['address1']; ?></p>
				<span class="rd"><?php echo $zone_label[$acct1['zone_id']]; ?></span>
				<br />
				<span class="rd">Meter # <?php echo $acct1['meter_number']; ?></span>
				<br />
				<span class="rd">Account Type # <?php echo $acct_type_label[$acct1['acct_type_key']]; ?></span>
		</td>
		 <td style="text-align:right;"   class="prev_read_el"><?php 
					
					//echo $acct1['prev_reading']?$acct1['prev_reading']:'----'; 
					if(!empty($acct1['init_reading'])){
						echo $acct1['init_reading'];
					}else{
						echo $acct1['prev_reading'];
					}

		 ?></td>
		<td  style="text-align:right;">
				<span class=""><?php echo $acct1['curr_reading']?$acct1['curr_reading']:'----'; ?></span>
		</td>
		<?php 
		/*
		 <td><?php echo $acct1['prev_reading']?$acct1['prev_reading']:'----'; ?></td>
		<td><?php echo $acct1['curr_reading']?$acct1['curr_reading']:'----'; ?></td>
		*/ ?> 
		<td  style="text-align:right;">
			<span class="consump">
				<?php  echo $acct1['consump_cu'];?>  
			</span>
		</td>
		<td style="text-align:right;">
				<span style="font-size:9px;">&#x20b1;</span> <?php echo  number_format($acct1['billing_initial'], 2) ?>
		</td>
		<td style="text-align:right;">
			<?php if(!empty($acct1['discount_type'])): ?>
				<?php echo $acct1['discount_type']; ?>
				<br />
				Less <?php echo $acct1['discount_val']; ?>%
				<br />
				<span style="font-size:9px;">&#x20b1;</span> <?php echo  number_format($acct1['discount_less'], 2) ?>
			<?php else: ?>
				None
			<?php endif; ?>
		</td>
		<td style="text-align:right;">
				<span style="font-size:9px;">&#x20b1;</span> <?php echo  number_format($acct1['total_billing'], 2) ?>
		</td>		
	</tr>
	<!------>
	<!------>
	<?php $index1++; endforeach; ?>
								
</tbody></table>

<br />
<br />

<div style="padding:15px;">
	<ul class="pagination pagination-sm">
	  <li><a href="#">PREVIOUSx</a></li>
	  <li><a href="#">1</a></li>
	  <li><a href="#">2</a></li>
	  <li><a href="#">3</a></li>
	  <li><a href="#">4</a></li>
	  <li><a href="#">5</a></li>
	  <li><a href="#">NEXT</a></li>
	</ul>
</div>




<script>

</script>

