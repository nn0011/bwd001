<?php $admin_accounts = '  class="active" ';?>

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
		<li class="tab01 active"  data-tab="dashboard11"><a href="#dashboard11">Dashboard</a></li>
		<li class="tab01" data-tab="account_list"><a href="#account_list">Account List</a></li>
		<?php /* <li class="tab01" data-tab="account_request"><a href="#account_request">Account Request</a></li>*/ ?> 
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="dashboard11">
		
		<div class="tab_item dashboard11">
			<div style="padding:15px;">
				@include('admin.incs.acccount.dashboard')
			</div>
		</div>
		
		<div class="tab_item account_list">
			<div style="padding:15px;">
				@include('admin.incs.acccount.account_list')
			</div>
		</div>
		
		<div class="tab_item account_request">
			<div style="padding:15px;">
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


