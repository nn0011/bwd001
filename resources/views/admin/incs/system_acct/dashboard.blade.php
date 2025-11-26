<button  onclick="add_new_system_account_action()">Add  new account</button>
<br />
<br />


<div class="scroll1">
	<table class="table10 table-bordered  table-hover">
		<tbody><tr class="headings">
			<td width="5%">Acct ID #.</td>
			<td width="70%">Name</td>
			<td width="10%">Account Type</td>
			<td width="10%">Action</td>
		</tr>
		<!------>
		<!------>
		<?php
		$index = 0;
		 foreach($users1 as $uu): ?>
		<tr onclick="" class="cursor1">
			<td><?php echo $uu['id']; ?></td>
			<td><?php echo $uu['name']; ?></td>
			<td><?php echo $uu['roles'][0]['description']; ?></td>
			<td><button  onclick="edit_account11(<?php echo $index; ?>)">Edit</button></td>
		</tr>
		<?php  $index++; endforeach; ?>
		<!------>
		<!------>
	</tbody></table>
</div>
				



<!--------------------------------- --->
<!--------------------------------- --->
<div class="view_account" style="display:none;">
	
	<div class="pop_view_info_table  view_acct_info_pop">
		
		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>
		
		<br />
		<b>Zone Assignments</b>
		<br />
		<ul class="item_list1">
			<li>Zone 1   <span  class="field3"></span></li>
			<li>Zone 2   <span  class="field3"></span></li>
			<li>Zone 3   <span  class="field3"></span></li>
		</ul>

	</div>
</div>
<!------------------------------------>
<!------------------------------------>
<div class="add_new_account" style="display:none;">
	<div style="padding:15px;">
		
	<h2>Add new account</h2>
	
	<form action="/admin/system_account/create_new_account" method="POST" class="form-style-9">
		<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">		
		
		<input type="text" class="form-control" autocomplete="off" placeholder="Full Name" name="fname" id="new_acc_1">
		<input type="text" class="form-control" autocomplete="off" placeholder="User Name" name="user_name" id="new_acc_2">
		<input type="password" class="form-control" autocomplete="off" placeholder="password" name="user_pass1" id="new_acc_3">
		<input type="password" class="form-control" autocomplete="off" placeholder="repeat-password" name="user_pass2" id="new_acc_4">
		<br>
		<br>
		<select class="form-control" name="acct_type" id="new_acc_5">
			<?php foreach($role1 as $rr): ?>
			<option value="<?php echo $rr['name']; ?>"><?php echo $rr['description']; ?></option>
			<?php endforeach; ?>
		</select>
		<br>
		<br>
		<button type="submit">Save</button>
			&nbsp;&nbsp;&nbsp;
		<button  type="button" onclick="pop_close()">Cancel</button>
	</form>
	
		</div>
</div>


<div class="edit_account1" style="display:none;">
	<div style="padding:15px;"  class="edit_acct">
		
	<h2>Edit account</h2>
	
	<form action="/admin/system_account/edit_account" method="POST" class="form-style-9">
		<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">		
		<input type="hidden" name="id"  id="e_acc_0" value="0">		
		
		<input type="text" class="form-control" autocomplete="off" placeholder="Full Name" name="fname" id="e_acc_1">
		<input type="text" class="form-control" autocomplete="off" placeholder="User Name" name="user_name" id="e_acc_2">
		<input type="password" class="form-control" autocomplete="off" placeholder="password" name="user_pass1" id="e_acc_3">
		<input type="password" class="form-control" autocomplete="off" placeholder="repeat-password" name="user_pass2" id="e_acc_4">
		<br>
		<br>
		<select class="form-control" name="acct_type" id="e_acc_5">
			<?php foreach($role1 as $rr): ?>
			<option value="<?php echo $rr['name']; ?>"><?php echo $rr['description']; ?></option>
			<?php endforeach; ?>
		</select>
		<br>
		<br>
		<button type="submit">Save</button>
			&nbsp;&nbsp;&nbsp;
		<button  type="button"  onclick="pop_close()">Cancel</button>
	</form>
	
		</div>
</div>



<script>
var account_11 = <?php echo json_encode($users1); ?>;	
function 	add_new_system_account_action(){
	trig1_v2('add_new_account');
}
function edit_account11($ind){
	trig1_v2('edit_account1');

	setTimeout(function(){
		$cur1 = account_11[$ind];
		jQuery('.edit_acct #e_acc_0').val($cur1.id);
		jQuery('.edit_acct #e_acc_1').val($cur1.name);
		jQuery('.edit_acct #e_acc_2').val($cur1.username);
		jQuery('.edit_acct #e_acc_5').val($cur1.roles[0].name);
	},100);
}
</script>
<style>
.back_1 .box1{min-height:auto !important;padding-bottom:50px !important;}	
</style>

