<?php 



$billing_accounts = '  class="active" ';?>
<?php  $com_url = '../resources/views/billings/';?>

@extends('layouts.billings')

@section('content')

	@if ($errors->any())
		<div  style="padding:15px;">
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		</div>
	@endif

	@if(session()->has('success'))
		<div  style="padding:15px;">
			<div class="alert alert-warning">
					{!! session('success') !!}
			</div>
		</div>
	@endif


	<ul  class="tab1x tab1x_v2">
		<li class="tab01" data-tab="account_list"><a href="#account_list">Account List</a></li>
<!--
		<li class="tab01 active" data-tab="dashboard11"><a href="#dashboard11">Dashboard</a></li>
-->
		<li class="tab01"  data-tab="account_new"><a href="#account_new">New Accounts</a></li>
		<li class="tab01" data-tab="account_type"><a href="#account_type">Account Type</a></li>
		<li class="tab01" data-tab="account_status"><a href="#account_status">Account Status</a></li>
		<li class="tab01" data-tab="zones"><a href="#zones">Zones</a></li>
<!--
		<li class="tab01" data-tab="route"><a href="#route">Route</a></li>
-->
	</ul>
	

	<div class="box1_white  tab_cont_1"  data-default="account_list">

		<div class="tab_item dashboard11">
			<div style="padding:15px;">
				<?php include_once($com_url.'inc/billing_accounts/acct_dash.php'); ?>
			</div>
		</div>


		<div class="tab_item account_list">
			<?php include_once($com_url.'inc/billing_accounts/acct_list.php'); ?>
		</div>

		<div class="tab_item account_new">
			<div style="padding:15px;">
					@include('billings.inc.billing_accounts.acct_new')
			</div>
		</div>

		<div class="tab_item account_type">
			<div style="padding:15px;">
				<?php include_once($com_url.'inc/billing_accounts/acct_type_list.php'); ?>
			</div>
		</div>


		<div class="tab_item account_status">
			<div style="padding:15px;">
				<?php include_once($com_url.'inc/billing_accounts/acct_status_list.php'); ?>
			</div>
		</div>

		<div class="tab_item zones">
			<div style="padding:15px;">
				<?php include_once($com_url.'inc/billing_accounts/acct_zones.php'); ?>
			</div>
		</div>

		<div class="tab_item route">
			<div style="padding:15px;">
				<?php // include_once($com_url.'inc/billing_accounts/acct_routes.php');  ?>
			</div>
		</div>

	</div>


@endsection

@section('inv_include')

	<?php  include($com_url.'inc/php_mod/pop1.php'); ?>
	<?php  include_once($com_url.'inc/billing_accounts/acct_popups.php'); ?>
	<link href="/css/billing/account_ledger.css" rel="stylesheet">

	<link href="/css/billing/accounts.css" rel="stylesheet">
	<script src="/js/billing/account.js"></script>

<script>

	var curr_period_yy = '<?php echo date('Y');  ?>';
	var curr_period_mm = '<?php echo date('m');  ?>';
	var curr_period_dd = '<?php echo date('d');  ?>';

	var accounts_data = <?php  echo json_encode($accounts['data']);?>;
	var curr_account_index = 0;

	var hwd_request_new_acct = <?php echo json_encode($hwd_request_new_acct); ?>;
	var acct_type = <?php echo json_encode($acct_types); ?>;
	var zones = <?php echo json_encode($zones); ?>;
	var acct_status = <?php echo json_encode($acct_statuses); ?>;


	jQuery(document).ready(function(){
		<?php if(@$zone == 'none' || empty(@$zone)){ ?>
		<?php }else{ ?>
			jQuery('.acct_list1 #search_zone').val('<?php echo @$zone; ?>');
		<?php }?>
		
		
		jQuery('.acct_list1 #search_acct_num, .acct_list1 #search_meter_num,.acct_list1 #search_last_name').keyup(
			function($ev){
				if($ev.keyCode != '13'){
					return;
				}
				do_search_now();
			}
		);
		
		
	});
	
	
	function cancel_this_billing($nn, $lid)
	{
		let v1 = jQuery('.pop101 .last_val1').val();
		
		if($nn != v1){return;}
		
		let is_go = confirm('Are you sure to cancel this billing?');
		if(!is_go){return;}
		
		jQuery.get('/billing/cancel_last_billing/'+$lid,function($r){
			view_ledger101();
		});
		
	}//
	
	function cancel_this_adjustment($nn, $lid)
	{
		let v1 = jQuery('.pop101 .last_val1').val();
		if($nn != v1){return;}
		
		let is_go = confirm('Are you sure to cancel this billing adjustment?');
		if(!is_go){return;}	
		
		jQuery.get('/billing/cancel_this_adjustment/'+$lid,function($r){
			view_ledger101();
		});
		
	}

	function cancel_this_penalty($nn, $lid)
	{
		let v1 = jQuery('.pop101 .last_val1').val();
		//~ if($nn != v1){return;}

		let is_go = confirm('Are you sure to cancel this billing penalty?');
		if(!is_go){return;}
		
		
		jQuery.get('/billing/cancel_this_penalty/'+$lid,function($r){
			view_ledger101();
		});
		
	}//
	
	
	
	
function edit_add_beginning_bal_V2()
{
	 //~ let vv = ledger_account[curr_index];
	 let vv = accounts_data[curr_account_index];
	 
	 
	 curr_ledger_acct = vv;
	 //console.log(curr_ledger_acct);

	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	
	let amt = jQuery('.pop101 .beg_bal1_amt').val();
	let prd = jQuery('.pop101 .beg_bal1_prd').val();
	let iid = vv.id;

	let URL11  = '/billing/account_ledger/get_ledger_acct/update_beginning_v2?acct_id='+iid+'&amt='+amt+'&prd='+prd;
	
	
	jQuery('.cmd_buts').hide();
	jQuery('.please_wait').show();
	
	jQuery.get(URL11, function(data){
		view_acct_ledger(curr_index);
	});	
	
}		


function refresh_ledger_101()
{
	let vv = accounts_data[curr_account_index];
	let is_go = confirm('Are you sure to recalculate?');
	if(!is_go){return;}
	let url1 = '/billing/account_ledger/refresh_ledger_101/'+vv.id+'';
	jQuery.get(url1, function(data){
		view_acct_ledger(curr_index);
	});	
}//

</script>


<style>
table.led01{
	border:1px solid #ccc;
	width:100%;
	margin-top:30px;
}
table td{
	border:1px solid #ccc;
	padding:10px;
}

.tabview_cmd{
	padding:0;
	margin:0;
}
.tabview_cmd li{
    display: inline-block;
    border: 1px solid #ccc;
    padding: 10px;	
}

.acct_ledger01 td {
    border: 1px solid #ccc;
    padding: 5px !important;
    vertical-align: top;
}

.tabview_cmd  li{
	cursor:pointer;
}

.tabview_cmd  li.active{
	background:#ccc;
}

.tabview_cmd  li:hover{
	background:#ccc;
}
.name_info span{
	font-weight:bold;
}

@media  print {
	.button_hide,
	.cmds001,
	.tabview_cmd,
	br ,
	.account_history h1,
	.acct_list1,
	.tab1x_v2,
	.main_left{
		display:none !important;
	}
	
	.name_info br{
		display:block !important;
	}
}
.res11001 {
    padding: 30px;
}
.add_billing_adjustment_cont {
    padding: 15px;
}
</style>
<style>
.r1{width:100px;}	
.r2{width:100px;}	
.r3{width:100px;}	
.r4{width:50px;}	
.r5{width:50px;}	
.r6{width:100px;}	
.r7{width:100px;}	
.r8{width:100px;}	
.r9{width:100px;text-align:right;}	
</style>

@endsection
