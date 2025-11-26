<div style="float:right;display:inline-block;color:red;font-weight: bold;">Current Date :  <?php  echo date('F d, Y');  ?></div>
<h2>Period :  <span class="rd"><?php echo date('F Y', strtotime($r_year.'-'.$r_month)); ?></span></h2>
@include('billings.inc.billing_reading.reading_inc.acct_filter1')

<!------>
<!------>
<table class="table10 table-bordered  table-hover"><tbody>

	<tr class="headings">
		<td width="5%">Account No.</td>
		<td width="5%">Meter No.</td>
		<td width="10%">Name</td>
		<td width="20%">Complete Address</td>
		<td width="5%">Zone</td>
		<td width="5%">Previous Reading</td>
		<td width="5%">Current Reading</td>
		<td width="5%">C.U. <br /> Meter</td>
		<td width="5%">Status</td>
	</tr>

	<?php //for($x=0;$x<=30;$x++): ?>
	<?php if(empty($acct_reading['data'])): ?>
	<tr>
		<td colspan="9" style="text-align:center;padding:30px;">
				<span style="font-size:24px;">No Result</span>
		</td>
	</tr>
	<?php endif; ?>

	<?php
	
	//~ echo '<pre>';
	//~ print_r($acct_reading);
	//~ die();
	
	if(!empty($acct_reading))
	{
			//~ foreach($acct_reading['data'] as $kk => $vv)
			//~ {

				//~ if(!empty($vv['reading1']['init_reading'])){
					//~ $vv['reading1']['prev_reading'] = $vv['reading1']['init_reading'];
				//~ }else{
					//~ if(!empty($vv['reading_prev'])){
						//~ $vv['reading1']['prev_reading'] = $vv['reading_prev']['curr_reading'];
					//~ }
				//~ }

				//~ $acct_reading['data'][$kk] = $vv;
			//~ }
	}

	$index1 = 0;
	if(!empty($acct_reading))
	{
			
	foreach($acct_reading['data'] as $acct1):

	?>
	<!------>
	<!------>
	<tr data-index="<?php echo $index1; ?>" data-box1="" class="cursor1  rowx<?php  echo $index1;  ?>  ">
		<td onclick="view_reading_info(<?php echo $index1; ?>)" ><?php echo $acct1['acct_no']; ?></td>
		<td><?php
				//echo $acct1['reading1']['meter_number'];
				echo $acct1['meter_number1'];
		?></td>
		<td><?php echo $acct1['lname']; ?>, <?php echo $acct1['fname']; ?> <?php echo $acct1['mi']; ?></td>
		<td><?php echo $acct1['address1']; ?></td>
		<td><?php echo $zone_label[$acct1['zone_id']]; ?></td>
		 <td style="text-align:right;"   class="prev_read_el"><?php
					
					echo @$acct1['reading1']['prev_reading'];
					
					// echo $acct1['reading1']['prev_reading']?$acct1['reading1']['prev_reading']:'----';
					// echo $acct1['reading1']['prev_reading']?$acct1['reading1']['prev_reading']:'----';
					
					/*
					if(empty($acct1['init_reading'])){
						echo $acct1['prev_reading']?$acct1['prev_reading']:'----';
					}else{
						echo $acct1['init_reading']?$acct1['init_reading']:'----';
					}
					*/
					
		 ?></td>
		<td  style="text-align:right;">
			<?php
			//$r_year, $r_month
			$req_date = date('Y-m', strtotime($r_year.'-'.$r_month));
			$curr_date = date('Y-m');

					if($req_date == $curr_date): ?>
						<input type="text" class="inv_text_f curr_read_el"   placeholder="----"  value="<?php echo @$acct1['reading1']['curr_reading']; ?>"  onchange="update_me11(<?php echo $index1; ?>, this)"  />
			<?php else: ?>
						<span class="rd  curr_read_el2"><?php 
						
										echo @$acct1['reading1']['curr_reading']?$acct1['reading1']['curr_reading']:'----'; 
						
						?></span>
			<?php endif; ?>
		</td>
		<?php
		/*
		 <td><?php echo $acct1['prev_reading']?$acct1['prev_reading']:'----'; ?></td>
		<td><?php echo $acct1['curr_reading']?$acct1['curr_reading']:'----'; ?></td>
		*/ ?>
		<td>
			<span class="consump">
			<?php

				$currR1  =  (int) @$acct1['reading1']['curr_reading'];
				$prevR1 =  (int) @$acct1['reading1']['prev_reading'];
				
				$consump =  (int) @$acct1['reading1']['current_consump'];
				echo $consump;
				//~ if($consump  == 0)
				//~ {
				//~ }
				
				//~ if($currR1 <= 0  || $prevR1<=0){
					//~ echo  '----';
				//~ }else{
					//~ $total_reading  = ( $currR1 -  $prevR1);
					//~ echo $total_reading <= 0?'----': $total_reading;
				//~ }

			?>
			</span>
		</td>
		<td>Read</td>
	</tr>
	<!------>
	<!------>
	<?php $index1++; endforeach;
	
}
	 ?>

</tbody></table>

<br />
<br />

<div style="padding:30px;">
	<?php   if(!empty($acct_reading['prev_page_url'])): ?>
	<a href="<?php echo $acct_reading['prev_page_url']; ?>#accounts"><button>&lsaquo; Prev</button></a>
		&nbsp;&nbsp;&nbsp;
	<?php  endif; ?>
	<?php echo @$acct_reading['current_page'] ?> of <?php echo @$acct_reading['last_page'] ?>
	&nbsp;&nbsp;&nbsp;
	<?php  if(!empty($acct_reading['next_page_url'])): ?>
		<a href="<?php echo $acct_reading['next_page_url']; ?>#accounts"><button>Next &rsaquo;</button></a>
	<?php  endif; ?>
</div>

<?php
/*
<div style="padding:15px;">
	<ul class="pagination pagination-sm">
	  <li><a href="#">PREVIOUS</a></li>
	  <li><a href="#">1</a></li>
	  <li><a href="#">2</a></li>
	  <li><a href="#">3</a></li>
	  <li><a href="#">4</a></li>
	  <li><a href="#">5</a></li>
	  <li><a href="#">NEXT</a></li>
	</ul>
</div>
*/
?>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="view_reading_acct01" style="display:none;">

	<div class="pop_view_info_table  view_acct_info_pop">

		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>

		<br />

		<ul class="item_list1">
			<li>Account Number   <span  class="field3"></span></li>
			<li>Metter Number   <span  class="field4"></span></li>
			<li>Zone   <span  class="field6"></span></li>
			<li>Previous Reading   <span class="prev_reading"></span></li>
			<li>Currrent Reading   <span class="curr_reading rd"></span></li>
			<li>Consumption(cu. meter)    <span  class="consump_pop"></span></li>
			<?php
			/*<li>Account Type   <span  class="field7"></span></li>
			<li>Account Status   <span  class="field8"></span></li>
			*/ ?>
		</ul>

		<br />
		<br />
		<br />

		<div style="text-align:center;">
			<button  onclick="add_initial_reading_act()">Add  Initial Reading !</button>
			<button  onclick="add_meter_reading_form()">Add Meter Number</button>
			<button  onclick="pop_close()">Close</button>
		</div>

	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_initial_reading" style="display:none;">

	<div class="pop_view_info_table  view_acct_info_pop">

		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>

		<br />
		<ul class="item_list1">
			<li>Account Number   <span  class="field3"></span></li>
			<li>Metter Number   <span  class="field4"></span></li>
			<li>Zone   <span  class="field6"></span></li>
		</ul>

		<br />
		<br />
		<h3>Initial Reading: </h3>
		<input type="text"  value=""   placeholder="----"  class="init_reading_txt" />

		<div style="text-align:center;">
			<button  onclick="save_initial_reading()">Save</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button  onclick="pop_close()">Cancel</button>
		</div>

	</div>

</div>
<!------------------------------------>
<!------------------------------------>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_meter_number" style="display:none;">

	<div style="padding:20px;">
		<form action="/billing/reading/add_meter_number_act1" method="POST" class="form-style-9  xx_11">

			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden" name="acct_id" value=""  id="acct_id">

			<div class="head_info">
				<h2 class="field1"></h2>
				<p class="field2"></p>
			</div>

			<br />
			<ul class="item_list1">
				<li>Account Number   <span  class="field3"></span></li>
				<li>Zone   <span  class="field6"></span></li>
			</ul>

			<h3>Meter Number: </h3>
			<input type="text"  value=""   placeholder="----"  class="init_reading_txt"  name="meter_number" />

			<div style="text-align:center;">
				<button>Save</button>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<button  onclick="pop_close()"  type="button">Cancel</button>
			</div>


		</form>
	</div>

</div>
<!------------------------------------>
<!------------------------------------>
