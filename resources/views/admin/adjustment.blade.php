<?php $admin_adjustment = '  class="active" ';?>

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
		<li class="tab01 active"  data-tab="dashboard11"><a href="#dashboard11">Adjustment</a></li>
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="dashboard11">

		<div class="tab_item dashboard11">
			<div style="padding:15px;">


<h3 style="padding:0;margin:0;">Latest Adjustments</h3>
<br />

<input type="text" class="findAdjN form-control"  value="<?php echo strtolower(@$key1); ?>"  placeholder="Find by Last Name" onchange="findAdjustmentByName()" />
<br />


<div class="search_res01">

<table class="table table-bordered table-hover table-sm" style="font-size:12px;">
  <thead>
    <tr>
      <th scope="col">Date</th>
      <th scope="col">Reff#</th>
      <th scope="col">Name</th>
      <th scope="col">Description</th>
      <th scope="col"  style="text-align:right;">Amount</th>
      <th scope="col"  style="text-align:center;">Status</th>
    </tr>
  </thead>
  <tbody>
	
	
	<?php 
	
	$indx = 0;
	foreach($bill_adj as $ba1): ?>
	<tr>
	  <td><?php echo date('F d, Y', strtotime($ba1->date1_stamp)); ?></td>
	  <td style="text-align:center;"><?php echo $ba1->id; ?></td>
	  <td>
		  <a onclick="view_popup_adjustment(<?php echo $indx; ?>)">
		  <?php echo $ba1->acct->acct_no.' - '.$ba1->acct->fname.' '.$ba1->acct->lname; ?>
		  </a>
	  </td>
	  <td><?php echo $ba1->adj_typ_desc; ?></td>
	  <td style="text-align:right;">
		  <?php if($ba1->amount < 0 ): ?>
		  <span style="color:red;">
		  <?php else: ?>
		  <span>
		  <?php endif; ?>
			  <?php echo number_format($ba1->amount,2); ?>
		  </span>
	  </td>
	  <td style="text-align:center;">
		  <?php 
		  
			if($ba1->status == 'active'){
				echo '<span class="text-primary">ACTIVE</span>';
			}
			
			if($ba1->status == 'pending'){
				echo '<span class="text-warning">PENDING</span>';
			}
			
			if($ba1->status == 'cancel'){
				echo '<span class="text-danger">CANCEL</span>';
			}
		  
		  ?>
	  </td>
	</tr>
	<?php  $indx++; endforeach; ?>
    
  </tbody>
</table>

<?php 

echo $bill_adj->links(); 

?>

</div>



			</div>
		</div>

		<div class="tab_item  invoice11">
			<div style="padding:15px;">
			</div>
		</div>

		<div class="tab_item account_request">
			<div style="padding:15px;">
			</div>
		</div>




	</div>






<!------------------------------------>
<!------------------------------------>
<div class="add_new_payable" style="display:none;">
	<div style="padding:15px;">
			
		<div>
			
			<div style="font-size:24px;">
				<div class="name_01"></div>
				<div class="acctno_01"></div>
			</div>
			
			<br />
			
			<div>
				ADJ. DATE : <span style="font-size:18px;"> <span class="adjdate_001"></span></span>
				<br />
				AMOUNT : <span style="font-size:18px;">&#8369; <span class="amt_001"></span></span>
				<br />
				DESCRIPTION : <br /> 
				<p style="font-size:18px;" class="desc_001"></p>
				<br />
			</div>
			

<form method="post" action="/admin/adjustments/update_remarks_status"  class="form_update_rem">
			<input type="hidden" name="adj_id"  value=""  class="adj_id" />			
			<input type="hidden" name="acct_id" value="" class="acct_id" />
			{{ csrf_field() }}

			<div>
				
				<small>Amount</small>
				<input type="text" value="" class="form-control amt_002"  name="amt_002" />
				
				<small>Status</small>
				<select class="form-control stat_001" name="stat_001">
						<option value="pending">PENDING</option>
						<option value="active">APPROVE</option>
						<option value="cancel">CANCEL</option>
				</select>
				
				<small>Admin Remarks</small>
				<textarea class="form-control admin_rem_001" style="height:100px;"  name="admin_rem_001"></textarea>
				
				
				<button type="button" class="btn btn-primary btn-sm" onclick="save_canges()">SAVE</button>
				&nbsp;
				<button type="button" class="btn btn-danger btn-sm"  onclick="pop_close()">CLOSE</button>
					
			</div>
</form>

			
		</div>
		
	</div>
</div>
<!------------------------------------>
<!------------------------------------>



@endsection

@section('inv_include')

<?php include('../resources/views/billings/'.'inc/php_mod/pop1.php'); ?>


<script>

var ajust101 = <?php echo json_encode($bill_adj); ?>;

function view_popup_adjustment($idx)
{
	//~ alert($idx);
	trig1_v2('add_new_payable');
	
	setTimeout(function(){

			let myitem1 = ajust101.data[$idx];
			
			jQuery('.pop101 .name_01').html(myitem1.acct.fname+ ' ' +myitem1.acct.lname);
			jQuery('.pop101 .acctno_01').html(myitem1.acct.acct_no);
			jQuery('.pop101 .amt_001').html(myitem1.amount);
			jQuery('.pop101 .desc_001').html(myitem1.adj_typ_desc);
			jQuery('.pop101 .adjdate_001').html(myitem1.date1);
			jQuery('.pop101 .stat_001').val(myitem1.status);
			jQuery('.pop101 .adj_id').val(myitem1.id);
			jQuery('.pop101 .acct_id').val(myitem1.acct_id);
			jQuery('.pop101 .amt_002').val(myitem1.amount);
			
		
		},300);
	//~ console.log(ajust101.data[$idx]);
}	
	
	
function findAdjustmentByName()
{
	let name1 = jQuery('.findAdjN').val();
	
	if(!name1 || name1==''){
		return false;
	}
	
	let url1 = '/admin/adjustments/search_name/'+encodeURI(name1);
	
	window.location = url1;
	
	//~ jQuery('.search_res01').html('Please wait...');
	
	//~ jQuery.get(url1, function($res1){
		//~ jQuery('.search_res01').html($res1.html1);
	//~ }).fail(function() {
		//~ jQuery('.search_res01').html('Error Result...');
	//~ });
	
}

function save_canges()
{
	let vv = confirm('Are you sure to save?');
	if(!vv){return;}
	
	//~ let sta1 = jQuery('.pop101 .stat_001').val(myitem1.status);
	//~ let rem1 = jQuery('.pop101 .admin_rem_001').val(myitem1.status);
	
	jQuery('.pop101 .form_update_rem').submit();
	
}//

</script>
<style>
.back_1 .box1{
    margin-top: 50px !important;
}	
</style>
@endsection
