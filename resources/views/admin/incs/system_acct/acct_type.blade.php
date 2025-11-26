<button  onclick="add_system_account_type()">Add  Account type</button>
<br />
<br />

<table class="table10 table-bordered  table-hover">
		<tbody><tr class="headings">
			<td width="5%">Type ID #.</td>
			<td width="70%">Name</td>
			<td width="10%">Action</td>
		</tr>
		<!------>
		<!------>
		<?php foreach($role1 as $rr1): ?>
			<tr onclick="" class="cursor1">
				<td><?php  echo $rr1['id'];?></td>
				<td><?php  echo $rr1['description'];?></td>
				<td><button onclick="">Edit</button></td>
			</tr>
		<?php endforeach; ?>

</tbody></table>


<div class="system_account_type_pop1"  style="display:none;">
	<div style="padding:15px;">
		<h2>Add new account</h2>
		<form action="/admin/system_account/create_new_account_type" method="POST" class="form-style-9">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">		
			 <input type="text" class="form-control" autocomplete="off" 
							placeholder="Name" name="name" id="acct_type_name">
			<br>
			<br>
			<div style="text-align:center;">
				<button type="submit">Save</button>
				&nbsp;&nbsp;&nbsp;
				<button type="button" onclick="pop_close()">Close</button>
			</div>

		</form>
	</div>
</div>


<script>
function add_system_account_type(){
	trig1_v2('system_account_type_pop1');
}	
</script>
