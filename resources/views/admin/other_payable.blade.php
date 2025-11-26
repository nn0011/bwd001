<?php $admin_other_payable = '  class="active" ';?>

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
		<li class="tab01 active"  data-tab="dashboard11"><a href="#dashboard11">Dashboard</a></li>
<!--
		<li class="tab01 "  data-tab="acct_type"><a href="#acct_type">Other </a></li>
-->
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="dashboard11">
		
		<div class="tab_item dashboard11">
			<div style="padding:15px;">
				@include('admin.incs.other_payable.main_op')
			</div>
		</div>
		
		<div class="tab_item acct_type">
			<div style="padding:15px;">
				<?php 
				/*@include('admin.incs.system_acct.acct_type')*/ ?>
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

		
	});
	</script>	
@endsection


