<?php $billing_accounts = '  class="active" ';?>
<?php include('html_part/header.php');?>

	 
	 <div class="tc1 main_right">
		<div class="cont_1">
			
			
			<ul  class="tab1x">
				<li class="tab01 active" data-tab="dashboard11">Dashboard</li>
				<li class="tab01"  data-tab="account_new">New Accounts</li>
				<li class="tab01" data-tab="account_list">Account List</li>
				<li class="tab01" data-tab="account_type">Account Type</li>
				<li class="tab01" data-tab="account_status">Account Status</li>
				<?php  /*
				<li class="tab03" data-tab="item1">Application List </li>
				<li class="tab03" data-tab="item1">Disconected Acccounts</li>
				*/ ?> 
				<li class="tab01" data-tab="zones">Zones</li>
			</ul>
			
			<div class="box1_white  tab_cont_1"  data-default="dashboard11">
				
				<div class="tab_item dashboard11">
					<div style="padding:15px;">
						<?php include_once('inc/billing_accounts/acct_dash.php'); ?>
					</div>
				</div>
				
				
				<div class="tab_item account_list">
					<?php include_once('inc/billing_accounts/acct_list.php'); ?>
				</div>
				
				<div class="tab_item account_new">
					<div style="padding:15px;">
							
							<div  style="width:500px;">
								<div class="name_fileds">
									<input type="text" class="form-control"  placeholder="Last Name">
									<input type="text" class="form-control" placeholder="First Name">
									<input type="text" class="form-control" placeholder="Middle">
								</div>
								 <input type="text" class="form-control" placeholder="Street Name">
								 <input type="text" class="form-control" placeholder="Barangay">
								 <input type="text" class="form-control" placeholder="City">
								 <select class="form-control" ><option >ZONE</option></select>
								 <input type="text" class="form-control" placeholder="Phone">
								 <input type="text" class="form-control" placeholder="Date of Birth">
								 <select class="form-control"><option >Account type</option></select>
								 <select class="form-control"><option >Discount type</option></select>
								 <input type="radio" >Government
								 <br />
								<input type="radio" >  Non-Government
								<br />
								<br />
								<button>Save</button>
								<button>Cancel</button>
							</div>
							
					</div>
				</div>
				
				<div class="tab_item account_type">
					<div style="padding:15px;">
					<?php include_once('inc/billing_accounts/acct_type_list.php'); ?>
					</div>
				</div>

				
				<div class="tab_item account_status">
					<div style="padding:15px;">
					<?php include_once('inc/billing_accounts/acct_status_list.php'); ?>
					</div>
				</div>
				
				<div class="tab_item zones">
					<div style="padding:15px;">
					<?php include_once('inc/billing_accounts/acct_zones.php'); ?>
					</div>
				</div>
				
				
			</div>
			
				
		</div>
	 </div>
	 
	 
	 

<?php include('php_mod/pop1.php'); ?>	 
<?php include_once('inc/billing_accounts/acct_popups.php'); ?>


</div>  



<script src="../js/js1.js" ></script>
<link rel="stylesheet" href="../style.css"  /> 
<style>
.form-control{
	display:inline-block;
	width:100%;
	margin-bottom:10px;
}	
.name_fileds input{
    display: inline-block;
    width: 32.7%;	
}	
</style>
</body>
</html>
