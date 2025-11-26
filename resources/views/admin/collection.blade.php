<?php 


$admin_collection = '  class="active" ';?>

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
		<li class="tab01 active"  data-tab="dashboard11"><a href="#dashboard11">Collection</a></li>
<!--
		<li class="tab02"  data-tab="invoice11"><a href="#invoice11">Invoice Management</a></li>
-->
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="dashboard11">

		<div class="tab_item dashboard11">
			<div style="padding:15px;">


				<!--
								
				-->
				<div class="dec_box1">
					 <div class="dec_box_head">Collection</div>
					 <div class="dec_box_body">
						 <span style="font-size:10px;">Select date</span>
						 <input type="text" class="form-control date110" value="<?php echo date('Y-m-d'); ?>" 
						 		onchange="getCollectionInfo101()">
						 <div class="nn_res001">
							 Please Wait...
						 </div>
						 <br>
						 <div style="text-align:center;">
						 </div>
					 </div>
				</div>				
				<!--
								
				-->



			</div>
		</div>

		<div class="tab_item  invoice11">
			<div style="padding:15px;">
				@include('admin.incs.collection.invoice1')
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
      
      setTimeout(function(){
			getCollectionInfo101();
		  },1000);      

	});


	function getCollectionInfo101()
	{
		let myDate = jQuery('.date110').val();
		jQuery('.nn_res001').html('Please Wait...');
		jQuery.get('/admin/get_collection_info_by_date1001/'+myDate, function($d){
			jQuery('.nn_res001').html($d.html1);
		}).fail(function() {
			jQuery('.nn_res001').html('Failed to find collection... #2003');
		});
	}//

	function goto_report()
	{
		// alert('test');
	}	
	
	</script>

<style>
ul.item_list1 li{
	list-style: none !important;
}
.teller100 {
    border: 3px solid #000;
    padding: 15px;
    margin-bottom: 30px;
    border-radius: 10%;
}
</style>	
@endsection
