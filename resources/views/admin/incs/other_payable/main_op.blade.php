<div style="padding:15px;">
	
<button onclick="onclick_add_payable()">Add  Payable</button>
<br>
<br>

<div class="scroll1">
	<table class="table10 table-bordered  table-hover">
		<tbody>
			
		<tr class="headings">
			<td width="30%">Name</td>
			<td width="20%">Description</td>
			<td width="15%">Code</td>
			<td width="15%">Amount</td>
			<td width="10%">Status</td>
			<td width="10%">Action</td>
		</tr>

		<?php $ind=0; foreach($other_pay as $opp){ ?>
		<tr onclick="" class="cursor1">
			<td><?php echo $opp->paya_title; ?></td>
			<td><?php echo $opp->paya_desc; ?></td>
			<td><?php echo $opp->glsl_code; ?></td>
			<td>Php <?php echo number_format($opp->paya_amount, 2); ?></td>
			<td><?php echo $opp->paya_stat; ?></td>
			<td><button onclick="view_paya(<?php echo $ind; ?>)">Edit</button></td>
		</tr>
		<?php  $ind++;} ?>
		
	</tbody></table>
</div>
				


<!------------------------------------>
<!------------------------------------>
<div class="add_new_payable" style="display:none;">
	<div style="padding:15px;">
			
		<h2 class="paya_h1">Add new payable</h2>
		
		<form action="/admin/other_payable/add_new" method="POST" class="form-style-9  new_paya1"  onsubmit="return paya_go_submit;">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">		
			<input type="hidden" name="paya_id" value="0" id="paya_id">		
			
			
			<input type="text" class="form-control" autocomplete="off" placeholder="Payable name" name="paya_title" id="paya_title">
			<textarea placeholder="Description"  class="form-control  paya_desc"  id="paya_desc"  name="paya_desc"></textarea>
			<input type="number" class="form-control" autocomplete="off" placeholder="Amount"  min="1" name="paya_amount" id="paya_amount">
			<input type="text" class="form-control" autocomplete="off" placeholder="GL/SL #" name="glsl_type" id="glsl_type">
			<br>
			<select class="form-control" name="paya_stat" id="paya_stat">
				<option value="active">Active</option>
				<option value="inactive">Inactive</option>
			</select>			
			<br>
			<button type="button"  onclick="go_save_paya()">Save</button>
				&nbsp;&nbsp;&nbsp;
			<button type="button" onclick="pop_close()">Cancel</button>
		</form>
		
		</div>
</div>
<!------------------------------------>
<!------------------------------------>



<script>
var paya1 = <?php echo json_encode($other_pay->toArray()); ?>;
var paya_go_submit = false;	
function onclick_add_payable()
{
	trig1_v2('add_new_payable');
}

function go_save_paya()
{
	paya_go_submit = true;
	setTimeout(function(){
		jQuery('.box1  .new_paya1').submit();
	}, 100);
	
}
function view_paya($ind)
{
	trig1_v2('add_new_payable');
	setTimeout(function(){
		let pp = paya1[$ind];
		jQuery('.box1  .paya_h1').html('Edit other payment');
		
		jQuery('.box1  .new_paya1').attr('action', '/admin/other_payable/update');
		jQuery('.box1  #paya_id').val(pp.id);
		jQuery('.box1  #paya_title').val(pp.paya_title);
		jQuery('.box1  #paya_desc').val(pp.paya_desc);
		jQuery('.box1  #paya_amount').val(pp.paya_amount);
		jQuery('.box1  #paya_stat').val(pp.paya_stat);
		jQuery('.box1  #glsl_type').val(pp.glsl_code);
	}, 100);
}

</script>
<style>
.back_1 .box1{min-height:auto !important;padding-bottom:50px !important;}	
#paya_desc{
	height:100px;
}
</style>

			</div>
