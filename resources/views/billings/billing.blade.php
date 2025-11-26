<?php $billing_billing = '  class="active" ';?>
<?php  $com_url = '../resources/views/billings/';?>
<?php

//~ echo $curr_period;
//~ die();

	$zone_label = array();
	if(!empty(@$zones)){
		foreach($zones as $zz){
			$zone_label[$zz['id']] = $zz['zone_name'];
		}
	}


	$acct_type_label = array();
	foreach($acct_type as $att1){
		$acct_type_label[$att1['id']] = $att1['meta_desc'];
	}

	//$r_month = '7';
	//$r_year    = '2018';
?>


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
		<li class="tab01 active" data-tab="dashboard11"><a href="#dashboard11">Dashboard</a></li>
-->
		<li class="tab01 active" data-tab="accounts"><a href="#accounts">Accounts</a></li>
		<li class="tab01"  data-tab="rates"><a href="#rates">Rates</a></li>
		<li class="tab01"  data-tab="discounts"><a href="#discounts">Discounts</a></li>
<!--
		<li class="tab01"  data-tab="period_request"><a href="#period_request">Billing Period Request</a></li>
		<li class="tab01"  data-tab="bill_procces_period"><a href="#bill_procces_period">Proccess Billing</a></li>
-->
		<li class="tab01"  data-tab="overdue"><a href="#overdue">Penalty</a></li>
		<li class="tab01"  data-tab="remote_collection"><a href="#remote_collection">Remote Collection</a></li>
		<li class="tab01"  data-tab="non_water_billing"><a href="#non_water_billing">Non-Water Billing</a></li>
<!--
		<li class="tab01"  data-tab="disconnection"><a href="#disconnection">Disconnection printing</a></li>
-->
<!--
		<li class="tab01"  data-tab="bill_printing"><a href="#bill_printing">Bill Printing</a></li>
-->

	</ul>

	<div class="box1_white  tab_cont_1"  data-default="accounts">

		<div class="tab_item dashboard11">
			<div style="padding:15px;">

				<?php
				/*
				<div style="float:right;display:inline-block;color:red;font-weight: bold;">Current Date :  <?php  echo date('F d, Y');  ?></div>
				<h2>Period :  <span class="rd"><?php echo date('F Y'); ?></span></h2>
				*/ ?>

					@include('billings.inc.billing_billing.bill_dashboard')


			</div>
		</div>

		<div class="tab_item accounts">
			<div style="padding:15px;">
				@include('billings.inc.billing_billing.bill_accounts3')
			</div>
		</div>

		<div class="tab_item rates">
			<div style="padding:15px;">
				@include('billings.inc.billing_billing.bill_rates')
			</div>
		</div>

		<div class="tab_item discounts">
			<div style="padding:15px;">
				@include('billings.inc.billing_billing.bill_discounts')
			</div>
		</div>


		<div class="tab_item period_request">
			<div style="padding:15px;">
				@include('billings.inc.billing_billing.bill_period_request')
			</div>
		</div>

		<div class="tab_item bill_printing">
			<div style="padding:15px;">
				<h2>Bill Printing</h2>
				@include('billings.inc.billing_billing.bill_printing')
			</div>
		</div>
		
		<div class="tab_item disconnection">
			<div style="padding:15px;">
				<h2>Disconnection Printing</h2>
				@include('billings.inc.billing_billing.disconnection_printing')
			</div>
		</div>

		<div class="tab_item overdue">
			<div style="padding:15px;">
				<h2>Over Due</h2>
				@include('billings.inc.billing_billing.overdue')
			</div>
		</div>
		

		<div class="tab_item  bill_procces_period">
			<div style="padding:15px;">
				<h2>Billing Proccess Period</h2>
				@include('billings.inc.billing_billing.bill_process1')

			</div>
		</div>
		
		<div class="tab_item  remote_collection">
			<div style="padding:15px;">
				<h2>Remote Collection</h2>
				@include('billings.inc.billing_billing.remote_collection1')

			</div>
		</div>
		
		<div class="tab_item  non_water_billing">
			<div style="padding:15px;">
				<h2>Non-Water BIlling</h2>
				@include('billings.inc.billing_billing.non_water_biling_1')
			</div>
		</div>


	</div>


@endsection

@section('inv_include')



	<?php include($com_url.'inc/php_mod/pop1.php'); ?>	 
	<script>
		jQuery(document).ready(function(){
			setTimeout(function(){
				var hash = window.location.hash;
				jQuery("a[href='"+hash+"']").trigger('click');
			}, 100);
		});
		

		
	</script>
	<style></style>
	<link rel="stylesheet" href="/hwd1/css/billing.common.css">
@endsection
