<?php $billing_acct_ledger = '  class="active" ';?>
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
<!--
		<li class="tab01 " data-tab="ledger_of_accounts"><a href="#ledger_of_accounts">Ledger of Accounts</a></li>
-->
		<li class="tab01 active" data-tab="account_history"><a href="#account_history">Ledger</a></li>
	</ul>


	<div class="box1_white  tab_cont_1"  data-default="account_history">


		<div class="tab_item ledger_of_accounts">
			<div style="padding:15px;">
				<h1>Ledger</h1>
			</div>
		</div>
		
		
		<div class="tab_item account_history">
			<div style="padding:15px;">
				<h1>Acccount History</h1>
				@include('billings.inc.billing_ledger.acct_ledger_list')
			</div>
		</div>		


		<div class="tab_item account_list">
			<?php //include_once($com_url.'inc/billing_accounts/acct_list.php'); ?>
		</div>






	</div>


@endsection

@section('inv_include')

	<?php include($com_url.'inc/php_mod/pop1.php'); ?>
	<link href="/css/billing/account_ledger.css" rel="stylesheet">
	<script src="/js/billing/account_ledger.js"></script>

<script>
	<?php 
		$arr_ACCT_LIST = $acct_list->toArray();
	?>
	
	var ledger_account =  <?php echo json_encode($arr_ACCT_LIST['data']); ?>;
	
	function ledger_1($m, $tt)
	{
		jQuery('.tabview').hide();
		jQuery('.'+$m).show();
		
		jQuery('.tabview_cmd li').removeClass('active');
		jQuery('.tabview_cmd li.'+$tt).addClass('active');
		
		//active
	}
	
	//~ function add_bill_adjustment()
	//~ {
		//~ console.log(curr_ledger_acct);
	//~ }


	jQuery(document).ready(function(){
		
		
		jQuery('.acct_list1 #search_acct_num, .acct_list1 #search_meter_num,.acct_list1 #search_last_name').keyup(
			function($ev){
				if($ev.keyCode != '13'){
					return;
				}
				acct_ledger_search();
			}
		);		
		
	});
	
	
	function clear_filter_001122()
	{
		jQuery('.acct_list1 #search_acct_num, .acct_list1 #search_meter_num,.acct_list1 #search_last_name').val('');
	}

	
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

@media print {
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
</style>

@endsection
