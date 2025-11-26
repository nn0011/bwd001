<?php $billing_reports = '  class="active" ';?>
<?php  $com_url = '../resources/views/billings/';?>
<?php // ?>


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
		<li class="tab01 active" data-tab="dashboard11"><a href="#dashboard11">Dashboard</a></li>
<!--
		<li class="tab01" data-tab="rm1"><a href="#rm1">Reports Management</a></li>
-->
<!--
		<li class="tab01" data-tab="contx1"><a href="#contx1">Aging of Account</a></li>
          <li class="tab01" data-tab="contx2"><a href="#contx2">Account Balances</a></li>
		<li class="tab01" data-tab="contx3"><a href="#contx3">Summary of Delinquents</a></li>
		<li class="tab01" data-tab="contx4"><a href="#contx4">Acknowledgement</a></li>
		<li class="tab01" data-tab="contx5"><a href="#contx5">Billing Summary</a></li>
		<li class="tab01" data-tab="contx6"><a href="#contx6">Accounts Reports</a></li>
-->
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="dashboard11">
		
		<div class="tab_item contx6">
			<div style="padding:15px;">
                    @include('billings.inc.billing_reports.contx6')
			</div>
		</div>
		
				

		<div class="tab_item dashboard11">
			<div style="padding:15px;">
                    @include('billings.inc.billing_reports.dash007')
			</div>
		</div>

		<div class="tab_item rm1">
			<div style="padding:15px;">
                    @include('billings.inc.billing_reports.rm1')
			</div>
		</div>

		<div class="tab_item contx1">
			<div style="padding:15px;">
                    @include('billings.inc.billing_reports.aging')
			</div>
		</div>

		<div class="tab_item contx2">
			<div style="padding:15px;">
				@include('billings.inc.billing_reports.account_balance')
			</div>
		</div>

		<div class="tab_item contx3">
			<div style="padding:15px;">
				@include('billings.inc.billing_reports.delinquents_summary')
			</div>
		</div>

		<div class="tab_item contx4">
			<div style="padding:15px;">
				@include('billings.inc.billing_reports.acknowledgement_report')
			</div>
		</div>

		<div class="tab_item contx5">
			<div style="padding:15px;">
				@include('billings.inc.billing_reports.daily_billing')
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
