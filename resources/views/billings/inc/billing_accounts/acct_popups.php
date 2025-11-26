
<!--------------------------------- --->
<!--------------------------------- --->
<?php

	$view_info_acct_modify_acct_pop = '0';

?>
<div class="view_info_acct_modify_acct_pop" style="display:none;">

	<div class="pop_view_info_table  view_info_acct_modify_acct_pop">

	<form   action="/billing/accounts/update" method="POST"  class="form-style-9"  onsubmit="return  view_info_acct_modify_acct_pop_update(this)">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden" name="id" value="<?php echo  csrf_token() ?>"  id="new_acc_ID">

			<div class="name_fileds">
				<input type="text" class="form-control" autocomplete="off"  placeholder="Last Name"  name="lname"  id="new_acc_1">
				<input type="text" class="form-control" autocomplete="off"  placeholder="First Name"  name="fname"   id="new_acc_2">
				<input type="text" class="form-control" autocomplete="off"  placeholder="Middle"  name="mi"   id="new_acc_3">
			</div>

			 <input type="text" class="form-control" autocomplete="off" placeholder="Date of Birth"  name="birth"   id="new_acc_4">
			 <input type="text" class="form-control" autocomplete="off" placeholder="Phone"   name="phone"   id="new_acc_5">
			 <input type="text" class="form-control" autocomplete="off" placeholder="Address 1"   name="address1"   id="new_acc_6">
<!--
			 <input type="text" class="form-control" autocomplete="off" placeholder="Address 2"   name="address2"   id="new_acc_7">
-->
			 <input type="text" class="form-control" autocomplete="off" placeholder="Installation Date"   name="residence_date"   id="new_acc_9">

			 <select class="form-control"  name="zone"   id="new_acc_10">
				 <option value="">ZONE</option>
				 <?php  foreach($zones as $zz):?>
				 <option value="<?php echo $zz['id']; ?>"><?php echo strtoupper($zz['zone_name']); ?></option>
				 <?php endforeach; ?>
			</select>

			 <select class="form-control" name="acct_type"   id="new_acc_11">
				 <option value="">Account type</option>
				 <?php  foreach($acct_types as $zz):?>
				 <option value="<?php echo $zz['id']; ?>"><?php echo ($zz['meta_name']); ?></option>
				 <?php endforeach; ?>
			</select>


			 <select class="form-control" name="acct_status"   id="new_acc_13">
				 <option value="">Account Status</option>
				 <?php  foreach($acct_statuses as $zz):?>
				 <option value="<?php echo $zz['id']; ?>"><?php echo ($zz['meta_name']); ?></option>
				 <?php endforeach; ?>
			</select>

			 <select class="form-control" name="discount_type"   id="new_acc_12">
				<option value="">Discount type</option>
				<?php foreach($bill_discount as $bdd): ?>
				<option value="<?php echo $bdd['id']; ?>"><?php echo $bdd['meta_name']; ?></option>
				<?php endforeach; ?>
			</select>

		     <small>Meter Sizes</small><br />
			 <select class="form-control" name="meter_size_id"   id="new_acct_meter_size">
				<option value="">Meter Size</option>
				<?php foreach($meter_sizes as $ms): ?>
				<option value="<?php echo $ms->id; ?>"><?php echo $ms->meta_name; ?></option>
				<?php endforeach; ?>
			</select>

			<small>Penalty Exemption</small><br />
			 <select class="form-control" name="penalty_exempt_id"   id="new_acct_penalty_exempt">
				<option value="0">No</option>
				<option value="1">Yes</option>
			</select>

			<small>Is Employee?</small><br />
			 <select class="form-control" name="employee"   id="new_acct_employee">
				<option value="0">No</option>
				<option value="1">Yes</option>
			</select>

			<small>TIN #</small><br />
			<input type="text" class="form-control" autocomplete="off" placeholder="TIN #"   name="tin_id"   id="tin_id">

			<small>OTHER ID. (SC, PWD, SOLO PARENT, ETC.)  </small><br />
			<input type="text" class="form-control" autocomplete="off" placeholder="TIN #"   name="other_id"   id="other_id">


			<?php
			 /*
			 <select class="form-control" name="new_acc_route"   id="new_acc_route">
				<option value="">Route #</option>
				<?php foreach($customer_routes as $bdd): ?>
				<option value="<?php echo $bdd['route_num']; ?>">Route #<?php echo $bdd['route_num']; ?></option>
				<?php endforeach; ?>
			</select>
			*/ ?>
			<br />
			<br />
			<br />

			<div style="text-align:center;">
				<button  type="submit">Update</button>
				&nbsp;&nbsp;&nbsp;
				<button  onclick="view_info_acct_modify_acct_pop_cancel()">Cancel</button>
			</div>

	</form>

	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<!--------------------------------- --->
<!--------------------------------- --->
<?php

	$view_info_acct_info_only = '0';

?>
<div class="view_info_acct_info_only" style="display:none;">

	<div class="pop_view_info_table  view_acct_info_pop">

		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>

		<br />

		<ul class="item_list1">
			<li>Account Number   <span  class="field3"></span></li>
			<li>Metter Number   <span  class="field4"></span></li>
			<li>Account Created   <span  class="field5"></span></li>
			<li>Zone   <span  class="field6"></span></li>
			<li>Account Type   <span  class="field7"></span></li>
			<li>Account Status   <span  class="field8"></span></li>
			<li>Discount Type   <span  class="field9"></span></li>
			<li>Number of Bills   <span  class="field10"></span></li>
		</ul>

		<br />

	</div>
</div>
<!------------------------------------>
<!------------------------------------>


<!--------------------------------- --->
<!--------------------------------- --->
<?php

	$view_info_acct = '0';

?>
<div class="view_info_acct" style="display:none;">

	<div class="pop_view_info_table  view_acct_info_pop">

		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>

		<br />

		<ul class="item_list1">
			<li>Account Number   <span  class="field3"></span></li>
			<li>Metter Number   <span  class="field4"></span></li>
			<li>Account Created   <span  class="field5"></span></li>
			<li>Zone   <span  class="field6"></span></li>
			<li>Account Type   <span  class="field7"></span></li>
			<li>Account Status   <span  class="field8"></span></li>
			<li>Discount Type   <span  class="field9"></span></li>
			<li>Number of Bills   <span  class="field10"></span></li>
		</ul>

		<br />
		<br />
		<br />

		<div style="text-align:left;">

			<ul class="cmd_menu1">
				<li><a onclick="view_info_acct_modify_acct()">Modify account</a></li>
				<li><a onclick="view_info_acct_add_meter_number()">Add / Edit Meter Number</a></li>
				<li><a onclick="/*update_route1()*/update_route01_act01();">Update Route</a></li>
<!--
				<li><a onclick="view_info_acct_add_docu()">Add documents</a></li>
-->
<!--
				<li><a onclick="view_info_acct_add_init_reading()">Add initial reading</a></li>
-->
<!--
				<li><a onclick="view_info_acct_goto_reading()">Reading</a></li>
				<li><a onclick="view_info_acct_goto_ledger()">Ledger</a></li>

-->
				<li><a onclick="view_ledger101()">Ledger</a></li>
<!--
				<li><a onclick="view_info_acct_goto_billing()">Billing</a></li>
-->

<!--
				<li><a onclick="add_beginning_balance()">Add Beginning</a></li>
-->
			</ul>

<br />
<br />
<br />
<br />
<br />
<a onclick="delete_accounts()">Delete Account</a>
			<?php
			/*
			<button onclick="view_info_acct_modify_acct()">Modify account</button>
			&nbsp;&nbsp;&nbsp;
			<button onclick="view_info_acct_add_meter_number()">Add Meter Number</button>
			&nbsp;&nbsp;&nbsp;
			<button onclick="view_info_acct_add_docu()">Add documents</button>
			*/
			?>
			<?php /*<button  onclick="view_info_acct_view_billing()">View billing info</button>*/?>
		</div>

	</div>
</div>
<!------------------------------------>
<!------------------------------------>


<div class="view_acct_ledger101_pop" style="display:none;">

	<div class="view_acct_ledger101_cont">

		<div class="res11001">Please wait</div>

	</div>

</div>

<!--------------------------------- --->
<!--------------------------------- --->
<?php

	$view_info_acct_old_example_only = '0';

?>
<div class="view_info_acct_old_example_only" style="display:none;">

	<div class="pop_view_info_table">

		<div class="head_info">
			<h2>Dela Cruz, Jimmy</h2>
			<p>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Lianga , Surigao del Sur</p>
		</div>
		<br />
		<ul class="item_list1">
			<li>Account Number   <span>00112233</span></li>
			<li>Metter Number   <span>9854672</span></li>
			<li>Account Created   <span>June 19, 2001</span></li>
			<li>Zone   <span>A1</span></li>
			<li>Account Type   <span>Residential -  Non-Government</span></li>
			<li>Account Status   <span>Active</span></li>
			<li>Discount Type   <span>None</span></li>
			<li>Number of Bills   <span>14</span></li>
		</ul>

		<br />
		<br />
		<br />

		<div style="text-align:center;">
			<button>Modify account</button>
			<button>Add documents</button>
			<button>View billing info</button>
		</div>


	</div>
</div>
<!------------------------------------>
<!------------------------------------>


<!--------------------------------- --->
<!--------------------------------- --->
<?php

	$new_initiative = '0';

?>
<div class="new_initiative" style="display:none;">

	<div class="pop_view_info_table">

		<span style="font-size: 16px;">Dela Cruz, Jimmy</span>
		<p>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Lianga , Surigao del Sur</p>

		<hr />

		<ul class="item_list1">
			<li>Account Status   <span>Active</span></li>
			<li>Account Created   <span>June 19, 2001</span></li>
			<li>Account Number   <span>00112233</span></li>
			<li>Metter Number   <span>9854672</span></li>
			<li>Zone   <span>A1</span></li>
			<li>Account Type   <span>Residential -  Non-Government</span></li>
			<li>Discount Type   <span>None</span></li>
		</ul>

		<hr />
		<div style="text-align:center;">
			<button>Modify account</button>
			<button>Add documents</button>
			<button>View billing info</button>
		</div>


	</div>
</div>
<!------------------------------------>
<!------------------------------------>
<?php

	$new_acct_type = '0';

?>
<div class="new_acct_type" style="display:none;">
	<div class="pop_view_info_table">
		<h2>New Account Type</h2>
		<form   action="/billing/account_type/new" method="POST"  class="form-style-9"  onsubmit="return new_account_submit(this)">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<?php echo acct_type_fields(); ?>
			<br />
			<br />
			<button type="submit">Save</button>
			&nbsp;&nbsp;&nbsp;
			<button>Cancel</button>
		</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>
<?php

	$update_acct_type = '0';

?>
<div class="update_acct_type" style="display:none;">
	<div class="pop_view_info_table">
		<h2>Update Account Type</h2>
		<form   action="/billing/account_type/update" method="POST"  class="form-style-9"  onsubmit="return update_account_submit(this)">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden"   name="id"  value=""  id="out1">
			<?php echo acct_type_fields(); ?>
			<br />
			<br />
			<button type="submit">Save</button>
			&nbsp;&nbsp;&nbsp;
			<button>Cancel</button>
		</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>
<?php

	$new_acct_status = '0';

?>
<div class="new_acct_status" style="display:none;">
	<div class="pop_view_info_table">
		<h2>New Account Status</h2>
		<form   action="/billing/account_status/new" method="POST"  class="form-style-9"  onsubmit="return new_acct_status_submit(this)">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<?php echo acct_type_fields(); ?>
			<br />
			<br />
			<button type="submit">Save</button>
			&nbsp;&nbsp;&nbsp;
			<button>Cancel</button>
		</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>
<?php

	$update_acct_status = '0';

?>
<div class="update_acct_status" style="display:none;">
	<div class="pop_view_info_table">
		<h2>Update Account Status</h2>
		<form   action="/billing/account_status/update" method="POST"  class="form-style-9"  onsubmit="return update_acct_status_submit(this)">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden"   name="id"  value=""  id="out1">
			<?php echo acct_type_fields(); ?>
			<br />
			<br />
			<button type="submit">Save</button>
			&nbsp;&nbsp;&nbsp;
			<button>Cancel</button>
		</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<?php

	$new_zone = '0';

?>
<div class="new_zone" style="display:none;">
	<div class="pop_view_info_table">
		<h2>New Zone</h2>
		<form   action="/billing/zone/new" method="POST"  class="form-style-9"  onsubmit="return new_zone_submit(this)">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<?php echo acct_type_fields(); ?>
			<br />
			<br />
			<button type="submit">Save</button>
			&nbsp;&nbsp;&nbsp;
			<button>Cancel</button>
		</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<?php

	$update_zone = '0';

?>

<div class="update_zone" style="display:none;">
	<div class="pop_view_info_table">
		<h2>Update Zone</h2>
		<form   action="/billing/zone/update" method="POST"  class="form-style-9"  onsubmit="return update_zone_submit(this)">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden"   name="id"  value=""  id="out1">
			<?php echo acct_type_fields(); ?>
			<br />
			<br />
			<button type="submit">Save</button>
			&nbsp;&nbsp;&nbsp;
			<button type="button" onclick="pop_close()">Cancel</button>
		</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<?php function  acct_type_fields(){?>
		<input type="text" class="form-control" autocomplete="off"  placeholder="Name"  name="name"  id="in1">
		<input type="text" class="form-control" autocomplete="off"  placeholder="Code"   name="code"   id="in2">
		<textarea class="form-control new_acct_txtA1" placeholder="Remarks"   name="descr"   id="in3"></textarea>
		<small>Bill date Every nth of the month</small>
		<select class="form-control" name="bill_date"  id="bill_date">
			<?php for($x=1;$x<=30;$x++): ?>
			<option value="<?php echo $x; ?>"><?php echo $x; ?></option>
			<?php endfor; ?>
		</select>
		 <select class="form-control"  name="status"   id="in4">
			<option value="active">Active</option>
			<option value="inactive">Inactive</option>
			<option value="deleted">Delete</option>
		</select>
<?php } ?>

<!------------------------------------>
<!------------------------------------>
<!------------------------------------>


<?php

	$add_meter_number = '0';

?>

<div class="add_meter_number" style="display:none;">

	<div style="padding:20px;">
		<form action="/billing/reading/add_meter_number_act1?vr=2" method="POST" class="form-style-9  add_meter_number_form1"  onsubmit="return add_meter_number_form;">

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
				<li>Meter #   <span  class="field6_1"></span></li>
			</ul>

			<br/>
			<strong>Account Number : </strong>
			<input type="text"  value=""   placeholder="----"  class="acct_number_text form-control"  name="acct_number" autocomplete="off" />
			
			<br/>
			<strong>Meter Number : </strong>
			<input type="text"  value=""   placeholder="----"  class="meter_number_text"  name="meter_number" autocomplete="off" />
			<div style="padding:0;margin-top:-20px;text-align:right;"><small class="badge badge-warning" style="cursor:pointer;" 
					onclick="check_meter_available()">is available?</small></div>
			
			<br />
			<strong>Meter Remarks : </strong>
			<br />
			<textarea class="form-control meter_rem" id="meter_rem" name="meter_remarks" style="resize:none;height:150px;"></textarea>
			<br />
			
			<!-- 
			<strong>Long / Lat </strong>
			<input type="text"  value=""   placeholder="Long"  class="long1 form-control"  name="long1" autocomplete="off" />
			<input type="text"  value=""   placeholder="Lat"  class="lat1 form-control"   name="lat1" autocomplete="off" /> -->
			<!-- <br />
			<br />
			<br /> 
			-->

			<div style="text-align:center;">
				<button onclick="view_info_acct_add_meter_number_send()"   type="button">Save</button>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<button  onclick="view_info_acct_modify_acct_pop_cancel()"  type="button">Cancel</button>
			</div>

		</form>
	</div>

</div>

<?php

$add_initial_reading = '0';

?>

<div class="add_initial_reading" style="display:none;">

	<div class="pop_view_info_table  view_acct_info_pop">

		<form action="/billing/reading/update_init_reading?vr=2" method="POST" class="form-style-9  add_meter_number_form1"  onsubmit="return add_initial_reading_form;">

			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden" name="acct_id" value=""  id="acct_id">
			<input type="hidden" name="reading_year" value="<?php echo date('Y'); ?>">
			<input type="hidden" name="reading_month" value="<?php echo date('m'); ?>">
			<input type="hidden" name="data1" value=""   id="data1">


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
			<input type="text"  value=""   placeholder="----"  class="init_reading_txt"  name="init_reading_txt" autocomplete="off"  />

			<div style="text-align:center;">
				<button  onclick="view_info_acct_add_init_reading_save()">Save</button>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<button  onclick="view_info_acct_modify_acct_pop_cancel()">Cancel</button>
			</div>
		</form>

	</div>
</div>

<?php
	$quick_preview_reading = 0;
?>
<div class="quick_preview_reading" style="display:none;">
		<div class="quick_preview_reading_pop">
				<ul class="item_list1">
					<li>Account Name   <span  class="field1"></span></li>
					<li>Account Number   <span  class="field2"></span></li>
					<li>Metter Number   <span  class="field3"></span></li>
					<li>Zone   <span  class="field4"></span></li>
				</ul>
				<div class="res_label">Reading Preview :  Previous / Current / Consumption </div>
				<div class="res_cont01">
					Please Wait...
				</div>

				<br />
				<br />

				<button  onclick="view_info_acct_modify_acct_pop_cancel()">go back</button>
				<button  onclick="view_info_acct_goto_current_reading()">Go To Current Reading</button>
		</div>
</div>

<?php

	$quick_preview_billing = 0;

?>
<div class="quick_preview_billing" style="display:none;">
		<div class="quick_preview_billing_pop">
				<ul class="item_list1">
					<li>Account Name   <span  class="field1"></span></li>
					<li>Account Number   <span  class="field2"></span></li>
					<li>Metter Number   <span  class="field3"></span></li>
					<li>Zone   <span  class="field4"></span></li>
				</ul>
				<h3>Billing info : </h3>
				<div class="res_cont01">
					Please Wait...
				</div>
				<br />
				<br />
				<button  onclick="view_info_acct_modify_acct_pop_cancel()">go back</button>
		</div>
</div>

<?php
	$add_beginning_balance = 0;
?>

<div class="add_beginning_balance" style="display:none;">
		<div class="add_beginning_balance_pop">

				<div class="add_beginning_bal_cont">
					<ul class="item_list1">
						<li>Account Name   <span  class="field1"></span></li>
						<li>Address   <span  class="field5"></span></li>
						<li>Account Number   <span  class="field2"></span></li>
						<li>Metter Number   <span  class="field3"></span></li>
						<li>Zone   <span  class="field4"></span></li>
					</ul>
					<br />
					<br />
					<input type="number" class="form-control"  id="beginning_bal_amt"  value=""  placeholder="Beginning Balance" />
					<br />
					<textarea class="form-control"  id="beginning_bal_remarks"  placeholder="Remarks" style="resize: none;"></textarea>
					<br />
					<button type="button" class="btn btn-primary btn-sm"  onclick="add_beginning_balance_submit()">Submit</button>

					<br />
					<br />
					<br />
					<br />
					<br />
					<br />
					<button  onclick="view_info_acct_modify_acct_pop_cancel()">go back</button>
				</div>
		</div>
</div>

<div class="update_route01" style="display:none;">
		<div class="update_route01_pop">

				<div class="update_route01_cont" style="padding: 15px;">
					<form onsubmit="return false;" class="form2">
						<h1 class="fname1">----</h1>
						<br />
						Route # <br />
						<input type="text" class="form-control route_num" name="route_num" />
						<input type="hidden" name="acct_id" value="" class="acct_id"  />
					</form>

					<br />
					<br />
					<br />
					<br />
					<br />

					<button  onclick="update_route01_act02()">Update / Save</button>

				</div>
		</div>
</div>


<style>
.add_beginning_bal_cont{
	padding:30px;
}
.quick_preview_reading_pop{
		padding:20px;
		padding-top:50px;
}
.quick_preview_billing_pop{
		padding:20px;
		padding-top:50px;
}
.res_label{
    padding-top: 20px;
    padding-bottom: 20px;
    font-weight: bold;
    font-size: 16px;
}
</style>


<script>
function check_meter_available()
{
	$mtr_num = jQuery('.pop101 .meter_number_text').val();
	// alert($mtr_num);
	
	jQuery.get("/billing/check_meter_available?mtr="+$mtr_num, function($res) {
		// let res_js = JSON.parse($res);
		// console.log($res);
		if($res.status == 1) 
		{
$msg = `
Meter not avaialble
Used by :
Account # : `+$res.data.acct_no+`
Name  : `+$res.data.lname+' '+$res.data.fname+`
Address  : `+$res.data.address1+`
`;
alert($msg);
return;
		}

if($res.status == 0) 
{
	alert('Meter is available.');
}

		
	}).fail(function() {
		alert('Error');
	});



}//	


async function update_route01_act01()
{
	trig1_v2('update_route01');
	await delay2('300');
	
	let vv = accounts_data[curr_account_index];
	console.log(vv);

	//
	if(vv.mi == null){vv.mi = '';}
	
	//
	let fname1 = vv.lname+', '+vv.fname+', '+ vv.mi;
	jQuery('.pop101 .fname1').html(fname1);
	
	//
	jQuery('.pop101 .route_num').val(vv.route_id);
	jQuery('.pop101 .acct_id').val(vv.id);

}

// SAVE INPUT
async function update_route01_act02() {

	let is_go = confirm('Are you sure to update route?');
	if(!is_go){return;}
	let post_data1 = post_data_01('.pop101 .form2');

	POST_JS_v2('/billing/accounts_update_route', post_data1, function(res) {
		if( res.split('SUCCESS').length >= 2 ) {
			alert(res);
			window.location.reload();
			return;
		} 

		alert(res);
	});
}//
</script>