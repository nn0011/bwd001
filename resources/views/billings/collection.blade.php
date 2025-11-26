<?php $billing_collection = '  class="active" ';?>
<?php  $com_url = '../resources/views/billings/';?>
<?php

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
		<li class="tab01 active"  data-tab="banks"><a href="#banks">Banks</a></li>
		
			<!--
			<li class="tab01 active" data-tab="dashboard11"><a href="#dashboard11">Dashboard</a></li>
			<li class="tab01" data-tab="accounts"><a href="#accounts">Accounts</a></li>
			-->
		<?php
			/*
		  ?>
		<li class="tab01"  data-tab="rates"><a href="#rates">Rates</a></li>
		<li class="tab01"  data-tab="discounts"><a href="#discounts">Discounts</a></li>
		*/ ?>
		
	</ul>


	<div class="box1_white  tab_cont_1"  data-default="banks">
		

		<div class="tab_item banks">
			<div style="padding:15px;">
					
					<button onclick="bank_add_new()">Add Bank</button>
					<br />
					<br />
					
					<div style="max-width:500px;">
						<ul class="item_list1">
							<?php  
							$x=0;
							foreach($banks as $bb) {?>
							<li><?php echo $bb->bank_name; ?>  <span class="clas_link"  onclick="edit_bank001(<?php echo $x; ?>)">Edit</span></li>
							<?php $x++;} ?>
<!--
							<li>Total Collection  <span class="">Edit</span></li>
							<li>Total Balance  <span class="">Edit</span></li>
-->
						</ul>					
					</div>
					
			</div>
		</div>		

		<div class="tab_item dashboard11">
			<div style="padding:15px;">

				<div class="dec_box1">
					<div class="dec_box_head">Period June 2018</div>
					<div class="dec_box_body">

						<ul class="item_list1">
							<li>Total Collectable  <span>&#x20b1; <?php  echo number_format($dash_info['total_collectable'], 2); ?></span></li>
							<li>Total Collection  <span>&#x20b1; <?php  echo number_format($dash_info['total_period_collection'], 2); ?></span></li>
							<li>Total Balance  <span class="rd"> &#x20b1;<?php  echo number_format(( $dash_info['total_collectable'] - $dash_info['total_period_collection']), 2); ?></span></li>
						</ul>
						<br>
						<div style="text-align:center;">
						</div>
					</div>
				</div>

				<div class="dec_box1">
					<div class="dec_box_head">Today's Collection</div>
					<div class="dec_box_body">

						<ul class="item_list1">
							<li>Total  <span>&#x20b1;<?php  echo number_format($dash_info['today_collection'], 2); ?></span></li>
						</ul>
						<br>
						<div style="text-align:center;">
						</div>
					</div>
				</div>



			</div>
		</div>

		<div class="tab_item accounts">
			<div style="padding:15px;">
				@include('billings.inc.billing_collection.collection_accounts')
			</div>
		</div>

		<div class="tab_item rates">
			<div style="padding:15px;">

			</div>
		</div>

		<div class="tab_item discounts">
			<div style="padding:15px;">

			</div>
		</div>


	</div>
	
	
<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_new_banks" style="display:none;">
	<div style="padding:15px;">
	<form   action="/billing/collection/bank/add" method="POST"  class="form-style-9"   id="form_bank_add"  onsubmit="">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden" name="id"  id="bank_id" value="0">
			
				<h2 class="title1">New Bank</h2>
				<br />
				
				<span class="sml">Bank Name : </span>
				<input type="text" class="form-control" autocomplete="off"  
					placeholder="Name"  name="bank_name"  id="bank_name">

				<span class="sml">Bank Branches</span>
				<textarea   name="bank_branches"    
					class="form-control"  id="bank_branches" placeholder="Branch 1, Branch 2, Branch 2....."></textarea>

				
<!--
				<span class="sml">Bank information</span>
				<textarea   placeholder="information"  name="bank_desc"    class="form-control"  id="bank_desc"></textarea>
-->
								
				<span class="sml">Bank Status</span>
				<select  class="form-control"  name="bank_stat"  id="bank_stat">
					<option value="active">Active</option>
					<option value="inactive">Inactive</option>
				</select>
				
				<br />
				<br />
				
				<div style="text-align:center;">
					<button>Save</button>
				</div>
				
				<div class="name_fileds">
				</div>

	</form>		
	</div>
</div>
<!------------------------------------>	


@endsection

@section('inv_include')

<?php include($com_url.'inc/php_mod/pop1.php'); ?>
 <script src="/js/collection/collection.js"></script>

<script>
var banks_list = <?php echo json_encode($banks); ?>;	
</script>

<script>
jQuery(document).ready(function(){
	setTimeout(function(){
		var hash = window.location.hash;
		jQuery("a[href='"+hash+"']").trigger('click');
	}, 100);
});

function bank_add_new()
{
	//alert('test');
	trig1_v2('add_new_banks');
}

function edit_bank001($ind)
{
	trig1_v2('add_new_banks');
	
	setTimeout(function(){
		let mm = banks_list[$ind];
		
		jQuery('.pop101 #form_bank_add').attr('action', '/billing/collection/bank/update');
		jQuery('.pop101 #bank_name').val(mm.bank_name);
		jQuery('.pop101 #bank_desc').val(mm.bank_info);
		jQuery('.pop101 #bank_branches').val(mm.branches);
		jQuery('.pop101 #bank_stat').val(mm.status);
		jQuery('.pop101 #bank_id').val(mm.id);
		
	}, 200);
	
}

</script>
	
<style>
.clas_link{
	cursor:pointer;
}	
</style>

	<link rel="stylesheet" href="/hwd1/css/billing.common.css">
@endsection
