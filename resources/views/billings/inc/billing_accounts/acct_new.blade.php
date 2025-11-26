<div  style="width:500px;"  class="new_acct_form">
	<h2>New Account</h2>
	<br />
	<form   action="/billing/accounts/new" method="POST"  class="form-style-9"  onsubmit="return new_account_main(this);">
		<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
		<div class="name_fileds">
			<input type="text" class="form-control" autocomplete="off"  placeholder="Last Name"  name="lname"  id="new_acc_1">
			<input type="text" class="form-control" autocomplete="off"  placeholder="First Name"  name="fname"   id="new_acc_2">
			<input type="text" class="form-control" autocomplete="off"  placeholder="Middle"  name="mi"   id="new_acc_3">
		</div>
		 <input type="text" class="form-control" autocomplete="off" placeholder="Date of Birth (Jan 1, 1990)"  name="birth"   id="new_acc_4">
		 <input type="text" class="form-control" autocomplete="off" placeholder="Phone"   name="phone"   id="new_acc_5">
		 <input type="text" class="form-control" autocomplete="off" placeholder="Address 1"   name="address1"   id="new_acc_6">
<!--
		 <input type="text" class="form-control" autocomplete="off" placeholder="Address 2"   name="address2"   id="new_acc_7">
-->
		 <input type="text" class="form-control" autocomplete="off" placeholder="Installation Date (Jan 1, 1990)"   name="residence_date"   id="new_acc_9">
		 <br />
		 <br />
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
		 <select class="form-control" name="discount_type"   id="new_acc_12">
				<option value="">Discount type</option>
				<?php foreach($bill_discount as $bdd): ?>
				<option value="<?php echo $bdd['id']; ?>"><?php echo $bdd['meta_name']; ?></option>
				<?php endforeach; ?>
		</select>
		<br />
		<br />
		<button  type="submit">Save</button>
		&nbsp;&nbsp;&nbsp;
		<button>Cancel</button>
	</form>
	<div class="wait_loading">Please Wait...</div>
</div>


<style>
.new_acct_form{
	position: relative;
}
.wait_loading{
    border: 1px solid;
    color: white;
    background: rgba(0,0,0,0.5);
    position: absolute;
    width: 120%;
    top: -20px;
    height: 110%;
    text-align: center;
    padding-top: 50px;
    font-size: 30px;
    left: -20px;
	display:none;
	
}	
</style>
