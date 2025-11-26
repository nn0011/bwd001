<?php $admin_request = '  class="active" ';?>

@extends('layouts.admin')

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
		<li class="tab01 active"  data-tab="account_list"><a href="#account_list">Accounts  <span class="req_ind  acct01" style="display:none;">New</span></a></li>
		<li class="tab01" data-tab="billing"><a href="#billing">Billing  <span class="req_ind  bill01" style="display:none;">New</span></a></li>
		<li class="tab01" data-tab="invoice"><a href="#invoice">Invoice  <span class="req_ind  invoice01" style="display:none;">New</span> </a></li>
<!--
		<li class="tab01" data-tab="account_type"><a href="#account_type">Reports</a></li>
		<li class="tab01" data-tab="account_status"><a href="#account_status">Reading</a></li>
		<li class="tab01" data-tab="account_status"><a href="#account_status">Service</a></li>
-->
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="account_list">

		<div class="tab_item account_list">
			<div style="padding:15px;">
				@include('admin.incs.requests.accounts')
			</div>
		</div>

		<div class="tab_item billing">
			<div style="padding:15px;">
				@include('admin.incs.requests.billing')
			</div>
		</div>

		<div class="tab_item account_type">
			<div style="padding:15px;">
			</div>
		</div>


		<div class="tab_item account_status">
			<div style="padding:15px;">
			</div>
		</div>

		<div class="tab_item zones">
			<div style="padding:15px;">
			</div>
		</div>


		<div class="tab_item invoice">
			<div style="padding:15px;">
				@include('admin.incs.requests.invoice')
			</div>
		</div>


	</div>

@endsection

@section('inv_include')
	<?php include('../resources/views/billings/'.'inc/php_mod/pop1.php'); ?>
	<script>
	jQuery(document).ready(function(){
		setTimeout(function(){
			var hash = window.location.hash;
			jQuery("a[href='"+hash+"']").trigger('click');
		}, 100);
	});
	</script>
@endsection
