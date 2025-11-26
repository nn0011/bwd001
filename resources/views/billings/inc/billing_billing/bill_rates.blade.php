<?php
$acct_reading['data'] = array('1');

//~ die();


?>


<button  class="trig1"  data-box1="add_rates11">Add Rates</button>
&nbsp;&nbsp;
<button onclick="sizes_manage1();">Meter Sizes</button>
<br />
<br />
<!------>
<!------>
<table class="table10 table-bordered  table-hover"><tbody>
	
	<tr class="headings">
		<td width="5%">Rate Name : </td>
		
<!--
		<td width="5%">Minimum Charge </td>
		<td width="5%">Rate </td>
-->
		
		<td width="5%">Account Type  </td>
		<td width="5%">Rate Description</td>
		<td width="5%">Action</td>
	</tr>
	
	<?php //for($x=0;$x<=30;$x++): ?>
	<?php 
	$index1 = 0;
	foreach($bill_rates['data'] as $bill_r): ?>
		
		<?php 
			
			$bill_data = (array) json_decode($bill_r['meta_data']);
		
		?>
	

		<tr>
			<td width="5%"> <?php echo $bill_r['meta_name']; ?></td>
			
<!--
			<td width="5%"> Php. <?php //echo @$bill_data['min_charge']; ?> </td>
			<td width="5%"> Php. <?php //echo @$bill_data['price_rate']; ?> / cu. m </td>
-->
			
			<td width="5%"> <?php echo @$acct_type_label[$bill_data['acct_type']]; ?> </td>
			<td width="5%">
				<p style="font-size:10px;">
					<?php echo @$bill_data['rate_desc']; ?>
				</p>
			</td>
			<td width="5%">
				<button  onclick="edit_rates1(<?php echo $index1; ?>)">Edit</button>
			</td>
		</tr>

		
	<?php $index1++; endforeach; ?>
								
</tbody></table>



<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_rates11" style="display:none;">
	<div style="padding:15px;">
	<form   action="/billing/billing/rates/add" method="POST"  class="form-style-9"  onsubmit="">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">		
				<h2>Add New Rates</h2>
				<br />
				<span class="sml">Rate Name : </span>
				<input type="text" class="form-control" autocomplete="off"  placeholder="Rate Name"  name="rname"  id="new_rate1">
				<span class="sml">Account Type : </span>
				
				<select  name="acct_type"  class="form-control">
					<?php foreach($acct_type as $att): ?>
					<option  value="<?php echo $att['id'] ?>"><?php echo $att['meta_desc'] ?></option>
					<?php endforeach; ?>
				</select>
				
				<span class="sml">Minimum Charge Php: </span>
				<input type="number"  min="0" class="form-control" autocomplete="off"  placeholder="Php."  name="min_charge"  id="new_rate2"  step="any">
				
				<div class="min_max">
					<div>
						<span class="sml">Min cu. m : </span>
						<input type="number"  min="1" class="form-control" autocomplete="off"  placeholder="cu. m"   name="min_cu"  id="new_rate3" /> 
					</div>
					<div> 
						<span class="sml">Max cu. m : </span>
						<input type="text"    min="1"   class="form-control" autocomplete="off"    name="max_cu"  id="new_rate4"   placeholder="cu. m" /> 
					</div>
				</div>
				
				<span class="sml">Rate  per cu. m : </span>
				<input type="number"  min="0"  class="form-control" autocomplete="off"  placeholder="Php."  name="price_rate"  id="new_rate5"    step="any">
				
				<span class="sml">Rate Description </span>
				<textarea   placeholder="Description"  name="rate_desc"    class="form-control"></textarea>
				
				<div style="text-align:center;">
					<button>Save</button>
				</div>
				
				<div class="name_fileds">
				</div>

	</form>		
	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_rates12" style="display:none;">
	<div style="padding:15px;">
	<form   action="/billing/billing/rates/update" method="POST"  class="form-style-9"  onsubmit="">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">		
			<input type="hidden"  name="rate_id"  id="new_rate1">
			
				<h2>Edit Rates</h2>
				<br />
				<span class="sml">Rate Name : </span>
				<input type="text" class="form-control" autocomplete="off"  placeholder="Rate Name"  name="rname"  id="new_rate2">
				<span class="sml">Account Type : </span>
				<select  name="acct_type"  class="form-control"    id="new_rate3">
					<?php foreach($acct_type as $att): ?>
					<option  value="<?php echo $att['id'] ?>"><?php echo $att['meta_desc'] ?></option>
					<?php endforeach; ?>
				</select>
				
				<div class="min_max">
					<div>
						<span class="sml">Min cu. m : </span>
						<input type="number"  min="1" class="form-control" autocomplete="off"  placeholder="cu. m"   name="min_cu"  id="new_rate5" /> 
					</div>
					<div> 
						<span class="sml">Max cu. m : </span>
						<input type="text"    min="1"   class="form-control" autocomplete="off"    name="max_cu"  placeholder="cu. m"    id="new_rate6"  /> 
					</div>
				</div>

				<div class="min_1"  style="padding-top:20px;padding-bottom:20px;">
					Please Wait..
					<br  />
					Retrieving price rates.
					<?php 
					
					/*
					
					<table class="mm11">
						<tr>
							<td>Size</td>
							<td>Min Charge</td>
							<td>Rate per c.u.m.</td>
						</tr>
						<tr>
							<td>1 / 2 " </td>
							<td>
								<input type="number"  min="0" class="form-control" autocomplete="off"  placeholder="Php."  name="min_charge"   step="any"    id="new_rate4">	
							</td>
							<td>
								<input type="number"  min="0"  class="form-control" autocomplete="off"  placeholder="Php."  name="price_rate"    step="any"    id="new_rate7">
							</td>
						</tr>
						
					</table>
					*/ 
					?> 
				</div>
				
				
				
				<?php 
				/*
				<span class="sml">Minimum Charge Php: </span>
				<input type="number"  min="0" class="form-control" autocomplete="off"  placeholder="Php."  name="min_charge"   step="any"    id="new_rate4">
				
				<span class="sml">Rate  per cu. m : </span>
				<input type="number"  min="0"  class="form-control" autocomplete="off"  placeholder="Php."  name="price_rate"    step="any"    id="new_rate7">
				*/?>
				
				<span class="sml">Rate Description </span>
				<textarea   placeholder="Description"  name="rate_desc"    class="form-control"  style="resize: none; height: 100px;"      id="new_rate8"></textarea>
				
				
				<br />
				<br />
				<span class="sml">Status</span>
				
				<select class="rate_stat form-control"  name="rate_sta1">
					<option value="active">Active</option>
<!--
					<option value="inactive">Inactive</option>
-->
					<option value="deleted">Delete</option>
				</select>
				
				<br />
				<br />
				
				
				<div style="text-align:center;">
					<button>Save</button>
				</div>
				
				<div class="name_fileds">
				</div>

	</form>		
	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<div class="meter_sizes_pop" style="display:none;">
	<div class="meter_sizes_cont">
		<div>
			<small>New Size</small><br />
			<input type="hidden" class="tx_type" value="0" />
			<input type="hidden" class="meta_id" value="0" />
			<input type="text"  placeholder="Sizes" class="meter_size" />
			<button onclick="save_new_size_1001()">Save</button>
			<button onclick="clear_my_inputs()">Clear</button>
			<br />
			<input type="text"  placeholder="Sequence" class="nsort"  style="display:none;" />			
		</div>
		<div>
			<div class="res0023">Please wait..</div>
		</div>
		
	</div>
</div>



<style>
.meter_size{
	padding:5px;
}	
.table10 td{
	vertical-align:top;
}	
.sml{
	font-size:9px;
}
.min_max{
}
.min_max div{
	border: 0px solid #ccc;
	display: inline-block;
	width: 100px;
	margin-right: 15px;	
}
.mm11{
	width:100%;
}
.mm11 td{
	padding:5px;
	border:1px solid #ccc;
}

.mm11 #new_rate4{
	width: 80px !important;
}

.mm11 #new_rate7{
	width: 80px !important;
}
.meter_sizes_cont{
	padding:25px;
}
.meter_size1{
	width:100%;
	margin-top:20px;
}
.meter_size1 td{
	border:1px solid #ccc;
	padding:5px;
}

</style>

<script>
var  rates_data1  =  <?php echo json_encode($bill_rates['data']); ?>;
var  meter_sizes = [];

function edit_rates1($ind){
	var cur_rate = rates_data1[$ind];
	var cur_rate_dat =  JSON.parse(rates_data1[$ind].meta_data);
	trig1_v2('add_rates12');
	setTimeout(function(){
		jQuery('.pop101 #new_rate1').val(cur_rate.id);
		jQuery('.pop101 #new_rate2').val(cur_rate.meta_name);
		jQuery('.pop101 #new_rate3').val(cur_rate_dat.acct_type);
		jQuery('.pop101 #new_rate4').val(cur_rate_dat.min_charge);
		jQuery('.pop101 #new_rate5').val(cur_rate_dat.min_cu);
		jQuery('.pop101 #new_rate6').val(cur_rate_dat.max_cu);
		jQuery('.pop101 #new_rate7').val(cur_rate_dat.price_rate);
		jQuery('.pop101 #new_rate8').val(cur_rate_dat.rate_desc);
	}, 200);
	
	let url001 = '/billing_billing_get_price_rates_list/v1?rr_id='+cur_rate.id;
	jQuery.get(url001, function( data ) {
		if(data.status != 'success'){
			alert(data.msg);
			return;
		}
		
		jQuery('.pop101 .min_1').html(data.html1);
		
	});
	
	
}//

function sizes_manage1()
{
	trig1_v2('meter_sizes_pop');
	setTimeout(function(){
			get_meter_sizes_1001();
		},500);
}

function get_meter_sizes_1001()
{
	let url001 = '/billing_billing_get_meter_sizes1001/v1';
	jQuery.get(url001, function( data ) {
		//~ console.log(data);
		jQuery('.pop101 .res0023').html(data.html1);
		meter_sizes = data.data1;
	});	
}

function save_new_size_1001()
{
	let ms_1 = jQuery('.pop101 .meter_size').val();
	if(!ms_1){return;}
	
	let tx_type = jQuery('.pop101 .tx_type').val();
	let meta_id = jQuery('.pop101 .meta_id').val();
	let nsort = jQuery('.pop101 .nsort').val();
	
	//~ alert('test');
	alert(nsort);
	
	let url001 = '/billing_billing_add_rates_meter_size/v1/?val='+ms_1+'&tx='+tx_type+'&meta_id='+meta_id+'&nsort='+nsort;
	
	jQuery.get(url001, function( data ) {
		
		if(data.status != 'success'){
			alert(data.msg);
			return;
		}
		
		alert(data.msg);
		
		jQuery('.pop101 .meter_size').val('');
		
		jQuery('.pop101 .res0023').html('Please Wait...');
		get_meter_sizes_1001();
		clear_my_inputs();
		
	});	
	
}//

function edit_meter_size_223($ind1)
{
	let met1 = meter_sizes[$ind1];
	jQuery('.pop101 .meter_size').val(met1.meta_name);
	jQuery('.pop101 .tx_type').val(1);
	jQuery('.pop101 .meta_id').val(met1.id);
	jQuery('.pop101 .nsort').val(met1.nsort);
	jQuery('.pop101 .nsort').show();
}

function clear_my_inputs()
{
	jQuery('.pop101 .meter_size').val('');	
	jQuery('.pop101 .tx_type').val(0);
	jQuery('.pop101 .meta_id').val(0);
	jQuery('.pop101 .nsort').val(0);
	jQuery('.pop101 .nsort').hide();
}

</script>

