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
	<?php 
	
	
	foreach($acct_reading['data'] as $kk => $vv){
		if(!empty($vv['reading1']['init_reading'])){
			$vv['reading1']['prev_reading'] = $vv['reading1']['init_reading']; 
		}
		$acct_reading['data'][$kk] = $vv;
	}


	$index1 = 0;
	foreach($acct_reading['data'] as $acct1): 
	
	?>
	<!------>
	<!------>
	<tr data-index="<?php echo $index1; ?>" data-box1="" class="cursor1  rowx<?php  echo $index1;  ?>  ">
		<td onclick="view_reading_info(<?php echo $index1; ?>)" ><?php echo $acct1['acct_no']; ?></td>
		<td><?php echo $acct1['reading1']['meter_number']; ?></td>
		<td><?php echo $acct1['lname']; ?>, <?php echo $acct1['fname']; ?> <?php echo $acct1['mi']; ?></td>
		<td><?php echo $acct1['address1']; ?></td>
		<td><?php echo $zone_label[$acct1['zone_id']]; ?></td>
		 <td style="text-align:right;"   class="prev_read_el"><?php 
					
					echo $acct1['reading1']['prev_reading']?$acct1['reading1']['prev_reading']:'----'; 
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
						<input type="text" class="inv_text_f curr_read_el"   placeholder="----"  value="<?php echo $acct1['reading1']['curr_reading']; ?>"  onchange="update_me11(<?php echo $index1; ?>, this)"  />
			<?php else: ?>
						<span class="rd"><?php echo $acct1['reading1']['curr_reading']?$acct1['reading1']['curr_reading']:'----'; ?></span>
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
				
				$currR1  =  (int) $acct1['reading1']['curr_reading'];
				$prevR1 =  (int) $acct1['reading1']['prev_reading'];
				
				if($currR1 <= 0  || $prevR1<=0){
					echo  '----';
				}else{
					$total_reading  = ( $currR1 -  $prevR1);
					echo $total_reading <= 0?'----': $total_reading; 
				}
				
			?>
			</span>
		</td>
		<td>Read</td>
	</tr>
	<!------>
	<!------>
	<?php $index1++; endforeach; ?>
								
</tbody></table>

<br />
<br />

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
			<button  onclick="">View billing info</button>
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



<script>
var reading_data1 = <?php echo  json_encode($acct_reading['data']); ?>;
var current_reading = null;
var curr_index = 0;

var  curr_read03 = 0;
var  prev_read03 = 0;
var  init_read03 = null;
var  now_period = "<?php echo date('Y-m'); ?>";
var  rec_period = "<?php echo date('Y-m', strtotime($r_year.'-'.$r_month)); ?>";
 

function view_reading_info($ind){
	curr_index = $ind;

	curr_read03 = reading_data1[$ind].reading1.curr_reading;
	prev_read03 = reading_data1[$ind].reading1.prev_reading;
	init_read03   =  reading_data1[$ind].reading1.init_reading  || 0;
	
	current_reading = reading_data1[$ind];
	
	trig1_v2('view_reading_acct01');
	
	setTimeout(function(){
		
		$acct_data = current_reading;
		
		curr_el =  jQuery('.rowx'+$ind+'  .curr_read_el').val() || 0;
		if(curr_el == 0){curr_el = '----';}
		
		jQuery('.view_acct_info_pop .field1').html($acct_data.lname+', '+$acct_data.fname+' '+$acct_data.mi);
		jQuery('.view_acct_info_pop .field2').html($acct_data.address1);
		jQuery('.view_acct_info_pop .field3').html($acct_data.acct_no);
		jQuery('.view_acct_info_pop .field4').html('None');
		jQuery('.view_acct_info_pop .field6').html($acct_data.zone_id);
		jQuery('.view_acct_info_pop .prev_reading').html(jQuery('.rowx'+$ind+'  .prev_read_el').html());
		jQuery('.view_acct_info_pop .curr_reading').html(curr_el);
		jQuery('.view_acct_info_pop .consump_pop').html(jQuery('.rowx'+$ind+'  .consump').html());
		
	},100);
	
}

function add_initial_reading_act(){
	trig1_v2("add_initial_reading");

	setTimeout(function(){
		
		$acct_data = current_reading;
		curr_el =  jQuery('.rowx'+$ind+'  .curr_read_el').val() || 0;
		if(curr_el == 0){curr_el = '----';}
		
		jQuery('.view_acct_info_pop .field1').html($acct_data.lname+', '+$acct_data.fname+' '+$acct_data.mi);
		jQuery('.view_acct_info_pop .field2').html($acct_data.address1);
		jQuery('.view_acct_info_pop .field3').html($acct_data.acct_no);
		jQuery('.view_acct_info_pop .field4').html('None');
		jQuery('.view_acct_info_pop .field6').html($acct_data.zone_id);
		
	},100);	
}

function save_initial_reading(){
	
	if(rec_period != now_period){
		alert('Add Initial Reading is not allowed.');
		return;
	}
	
	$init_reading1 = jQuery('.pop101 .init_reading_txt').val() || 0;
	
	if($init_reading1 == 0){
		jQuery('.rowx'+curr_index+'  .prev_read_el').html('----');
	}else{
		jQuery('.rowx'+curr_index+'  .prev_read_el').html($init_reading1);
		reading_data1[curr_index].reading1.prev_reading = $init_reading1;		
	}
	
	var reading_year = jQuery('#period_year').val();
	var reading_month = jQuery('#period_month').val();
	
	/**/
	jQuery.post( "/billing/reading/update_init_reading", { 
			'init_reading': $init_reading1,   
			'data1' : reading_data1[curr_index],
			'reading_year': reading_year,
			'reading_month':reading_month			
		}, function( data ) {
		  //$( ".result" ).html( data );
	});	
	/**/	
	
	setTimeout(function(){
		pop_close();
	}, 300);
	
	
}


function update_me11($ind,  $read1){
	var reading_value = jQuery($read1).val();
	var reading_year = jQuery('#period_year').val();
	var reading_month = jQuery('#period_month').val();
	var info_data = reading_data1[$ind];
	
	var prev_read = parseInt(reading_data1[$ind].reading1.prev_reading) || 0;
	var curr_read =  parseInt(reading_value) || 0;
	var consume  =  curr_read - prev_read;

	curr_read03 = curr_read;
	
	if(prev_read <=0 || curr_read<=0){
		jQuery('.rowx'+$ind+'  .consump').html('----');
	}else{
		if(consume <= 0){
			jQuery('.rowx'+$ind+'  .consump').html('----');
		}else{
			jQuery('.rowx'+$ind+'  .consump').html(consume);
		}
	}
	
	
	/**/
	jQuery.post( "/billing/reading/update_current_reading", { 
			'current_read': reading_value,   
			'acct_info': info_data,  
			'reading_year': reading_year,
			'reading_month':reading_month
		}, function( data ) {
		  //$( ".result" ).html( data );
	});	
	/**/
}


function  initialize_reading(){
}


</script>

