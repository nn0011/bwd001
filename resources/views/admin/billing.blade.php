<?php $admin_billing = '  class="active" ';?>

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
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="dashboard11">
		
		<div class="tab_item dashboard11">
			<div style="padding:15px;">


				<div class="dec_box1">
					 <div class="dec_box_head">Billing Schedule</div>
					 <div class="dec_box_body">
						 <ul class="item_list1">
							 <li>Current Period  <span><?php echo date('F Y'); ?></span></li>
							 <li>Billing Sched.  <span> <?php echo $bill_date = date('F 25, Y'); ?></span></li>
							 <li>Due Date  <span class="rd"><?php echo date('F d, Y', strtotime($bill_date.' +15 days')); ?></span></li>
						 </ul>									
						 <br>
						 <div style="text-align:center;">
						 </div>
					 </div>
				</div>
				

				<div class="dec_box1">
					 <div class="dec_box_head">Billing Status</div>
					 <div class="dec_box_body">
								<select class="form-control" onchange="get_billing_status1()">
										<option value="2018-07">July 2018</option>
										<option value="2018-06">June 2018</option>
										<option value="2018-05">May 2018</option>
									</select>
						
						 <ul class="item_list1">
							 <li>Active Accounts  <span>4</span></li>
							 <li>Accounts Billed  <span>5</span></li>
							 <li>Due   <span class="rd">-1</span></li>
							 <li>Unbilled  <span class="rd">-1</span></li>
						 </ul>
						 									
						 <br>
						 <div style="text-align:center;">
						 </div>
					 </div>
				</div>
					
					
					
				
				
			</div>
		</div>
		
		<div class="tab_item account_list">
			<div style="padding:15px;">
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

      jQuery('.date110').datepicker({
        autoHide: true,
        zIndex: 2048,
        format: 'yyyy-mm-dd'
      });		
		
	});
	</script>	
@endsection


