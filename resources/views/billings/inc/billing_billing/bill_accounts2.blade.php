<?php
$acct_reading['data'] = $billing_res['data'];
?>
<div style="float:right;display:inline-block;color:red;font-weight: bold;">Current Date :  <?php  echo date('F d, Y');  ?></div>
<br />
<?php /**/ ?>
@include('billings.inc.billing_billing.reading_inc.acct_filter1') 

<h2>Period :  <span class="rd"><?php echo date('F Y', strtotime($r_year.'-'.$r_month)); ?></span></h2>

<!------>
<!------>

<table class="table10 table-bordered  table-hover"><tbody>
	
	<tr class="headings">
		<td width="10%">Account No.</td>
		<td width="50%">Name</td>
		<td width="3%" style="text-align:right;">Previous </td>
		<td width="3%"  style="text-align:right;">Current </td>
		<td width="3%"  style="text-align:right;">C.U.</td>
		<td width="7%"  style="text-align:right;">Sub Total</td>
		<td width="5%"  style="text-align:right;">Discounts</td>
		<td width="7%"  style="text-align:right;">
					Total on or before 
					<br />
					<?php echo date('F d,  Y', strtotime($r_year.'-'.$r_month.'-28   +10 days')); ?>
		</td>
		<td width="7%"  style="text-align:right;">
					Total After
					<br />
					<?php echo date('F d,  Y', strtotime($r_year.'-'.$r_month.'-28   +10 days')); ?>
		</td>
		
	</tr>
	
	<?php //for($x=0;$x<=30;$x++): ?>
	
	<?php if(empty($acct_reading['data'])): ?>
		<tr>
			<td colspan="9" style="padding:15px;text-align:center;">
				<span style="font-size:18px;font-weight:bold;">No Result</span>
			</td>
		</tr>
	<?php endif; ?>
	
	<?php 
	

	$index1 = 0;
	foreach($acct_reading['data'] as $acct1): 
	
			extract($acct1['reading_back1']);
			extract($acct1['reading_back1']['account1']);
			
	?>
	<!------>
	<!------>
	<tr data-index="<?php echo $index1; ?>" data-box1="" class="cursor1  rowx<?php  echo $index1;  ?>  ">
		<td onclick="" ><?php echo $acct_no; ?></td>
		<td>
				<?php echo $lname; ?>, <?php echo $fname; ?> <?php echo $mi; ?>
				<p style="font-size:10px;">
						<?php echo $address1; ?>
						<br />
						<span class="rd">Account Type # <?php echo $acct_type_label[$acct_type_key]; ?></span>
				</p>
				<span class="rd"><?php echo $zone_label[$zone_id]; ?></span>
				<br />
				<span class="rd">Meter # <?php echo $meter_number; ?></span>
		</td>
		 <td style="text-align:right;"   class="prev_read_el"><?php 
					if(!empty($init_reading)){
						$prev_reading = $init_reading;
					}
					
					echo !empty($prev_reading)?$prev_reading:'----'; 
		 ?></td>
		<td  style="text-align:right;">
				<span class=""><?php echo $curr_reading?$curr_reading:'----'; ?></span>
		</td>
		<td  style="text-align:right;">
			<span class="consump">
				<?php   echo ((int)$curr_reading - (int)$prev_reading) ?>  
			</span>
		</td>
		<td style="text-align:right;">
				<span style="font-size:9px;">&#x20b1;</span> <?php echo  number_format($sub_total1, 2) ?>
		</td>
		<td style="text-align:right;">
				 <span style="font-size:9px;">&#x20b1;</span> <?php echo  number_format($less_total, 2) ?>
				 <br />
				 <span style="font-size: 9px;line-height: 100%;display: inline-block;margin-top: 15px;">
				 <?php if(!empty($acct1['discount_info'])): ?>
				 <?php echo ($acct1['discount_info']['meta_value']); ?>%
				 <?php echo ($acct1['discount_info']['meta_name']); ?>
				 <?php endif; ?>
				 </span>
		</td>
		
		<td style="text-align:right;">
				<span style="font-size:9px;">&#x20b1;</span> <?php  echo  number_format($billing_total, 2) ?>
		</td>		
		
		<td style="text-align:right;">
				<span style="font-size:9px;">&#x20b1;</span> <?php  echo  number_format(($billing_total * 1.1), 2) ?>
		</td>		
		
	</tr>
	<!------>
	<!------>
	<?php $index1++; endforeach; ?>
								
</tbody></table>

<br />
<br />
<?php  echo  $billing_res['current_page'].' of '.$billing_res['last_page'];  ?>
<br />
<br />

<div style="padding:15px;">
	<ul class="pagination pagination-sm">
	  <?php if(!empty($billing_res['prev_page_url'])): ?>
	  <li  style="margin-right:50px;"><a href="<?php echo $billing_res['prev_page_url']; ?>#accounts">PREVIOUS</a></li>
	  <?php endif; ?>
	  <?php if(!empty($billing_res['next_page_url'])): ?>
	  <li><a href="<?php echo $billing_res['next_page_url']; ?>#accounts">NEXT</a></li>
	  <?php endif; ?>
	</ul>
</div>





<style>
.pagination>li{
	display:inline-block;
}	
</style>
