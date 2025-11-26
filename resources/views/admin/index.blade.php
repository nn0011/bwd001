<?php $billing_dashboard = '  class="active" ';?>
<?php  $com_url = '../resources/views/billings/';?>

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
		<li class="tab01 active" data-tab="dashboard11"><a href="#dashboard11">Dashboard</a></li>
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="dashboard11">
		
		<div class="tab_item dashboard11">
			<div style="padding:15px;">
				<h1>Welcome</h1>
			</div>
		</div>

		
	</div>


	
@endsection

@section('inv_include')
<?php 
/*
	<?php include($com_url.'inc/php_mod/pop1.php'); ?>	 
	<?php include_once($com_url.'inc/billing_accounts/acct_popups.php'); ?>
	<script>
	jQuery(document).ready(function(){
		setTimeout(function(){
			var hash = window.location.hash;
			jQuery("a[href='"+hash+"']").trigger('click');
		}, 100);
	});
	</script>
*/ ?>	
@endsection


