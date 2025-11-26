<?php $billing_meter_reading = '  class="active" ';?>
<?php  $com_url = '../resources/views/billings/';?>
<?php

	$zone_label = array();
	foreach($zones as $zz){
		$zone_label[$zz['id']] = $zz['zone_name'];
	}

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
		<li class="tab01" data-tab="reading_period"><a href="#reading_period">Reading Period</a></li>
		<li class="tab01"  data-tab="meterofficer"><a href="#meterofficer">Meter Officer</a></li>
<!--
		<li class="tab01"  data-tab="forbilling"><a href="#forbilling">For Billing</a></li>
-->
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="accounts">

		<div class="tab_item dashboard11">
			<div style="padding:15px;">
				@include('billings.inc.billing_reading.reading_dash')
			</div>
		</div>


		<div class="tab_item meterofficer">
			<div style="padding:15px;">
				@include('billings.inc.billing_reading.reading_meter_officer')
			</div>
		</div>

		<div class="tab_item accounts">
			<div style="padding:15px;">
				@include('billings.inc.billing_reading.reading_accounts')
			</div>
		</div>

		<div class="tab_item reading_period">
			<div style="padding:15px;">
				@include('billings.inc.billing_reading.reading_period')
			</div>
		</div>
		
		<div class="tab_item forbilling">
			<div style="padding:15px;">
				@include('billings.inc.billing_reading.reading_for_billing')
			</div>
		</div>
		
		
		

	</div>


@endsection

@section('inv_include')
	<?php include($com_url.'inc/php_mod/pop1.php'); ?>

     <script src="/js/billing/reading_account.js"></script>


     <script>
     var  reading_data1 = <?php 
     echo  json_encode(@$reading11['data']); 
     //echo  json_encode(@$acct_reading['data']); 
     //reading11
     ?>;
     var current_reading = null;
     var curr_index = 0;
     var  curr_read03 = 0;
     var  prev_read03 = 0;
     var  init_read03 = null;
     var  now_period = "<?php echo date('Y-m'); ?>";
     var  rec_period = "<?php echo date('Y-m', strtotime($r_year.'-'.$r_month)); ?>";
     </script>


	<script>
	jQuery(document).ready(function(){
		
		setTimeout(function(){
			var hash = window.location.hash;
			jQuery("a[href='"+hash+"']").trigger('click');
		}, 100);
		
		
		jQuery('.filter_1 #search_acct_num, .filter_1 #search_meter_num,.filter_1 #search_last_name').keyup(
			function($ev){
				if($ev.keyCode != '13'){
					return;
				}
				go_reading_filter();
			}
		);		
		
		
		
		
	});
	</script>
	<link rel="stylesheet" href="/hwd1/css/billing.common.css">

@endsection
