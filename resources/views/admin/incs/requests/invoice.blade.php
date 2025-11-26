<h1>Invoice Requests</h1>

<table class="table10 table-bordered  table-hover"><tbody>

	<tr class="headings">
		<td width="30%">Request name</td>
		<td width="5%">Date dequested</td>
		<td width="5%">Date approved</td>
		<td width="5%">Request status</td>
	</tr>

	<?php

			$index = 0;
			$has_new = 0;

			foreach($invoice_req['data'] as $kk => $bill):

					//$dd1 = json_decode($bill['other_datas']);


	?>
		<tr  onclick="view_invoice_request_info(<?php echo $index; ?>)">
			<td>
					<?php echo $bill['remarks'] ?$bill['remarks']:'none'; ?>
					<?php if($bill['status'] == 'pending'): ?>
						<span class="req_ind">New </span>
					<?php $has_new++; endif; ?>
			</td>
			<td><?php echo  date('F d, Y', strtotime($bill['updated_at']));?></td>
			<td>&nbsp;</td>
			<td><?php echo $bill['status']; ?></td>
		</tr>
	<?php $index++; endforeach; ?>


</tbody></table>

<div class="view_invoice_request_info" style="display:none;">
	<div class="pop_view_info_table  view_acct_info_pop" >

		<div>
			<h3 class="f1"></h3>
			<span>Date Requested: <b>Jan 2015</b></span>
			<br />
			<span>Status: <b class="f3"></b></span>

		</div>

		<div style="text-align:center;margin-top:50px;display:none;"  class="cmd1_but1 invoice_buts1">
			<button  onclick="approve_invoice_req()">Approved</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button  onclick="cancel_invoice_req()">Cancel</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button  onclick="pop_close()">Close</button>
		</div>


	</div>
</div>



<script>
var invoice_req_data = <?php echo json_encode($invoice_req['data']); ?>;
var curr_invoice_req = null;

function view_invoice_request_info($ind){
	curr_invoice_req = invoice_req_data[$ind];
	trig1_v2('view_invoice_request_info');

	setTimeout(function(){
		jQuery('.pop101 .f1').html(curr_invoice_req.remarks);
		jQuery('.pop101 .f2').html(curr_invoice_req.remarks);
		jQuery('.pop101 .f3').html(curr_invoice_req.status);
		if(curr_invoice_req.status == 'pending'){
			jQuery('.pop101 .invoice_buts1').show();
		}
	}, 200);

}//func

function approve_invoice_req(){
	var approv1 = confirm('Sure to approve ?');
	if(!approv1){return;}
	window.location = '/admin/request/invoice/approve/'+curr_invoice_req.id;
}//func

function cancel_invoice_req(){
	var cancel1 = confirm('Sure to cancel ?');
	if(!cancel1){return;}
	window.location = '/admin/request/invoice/cancel/'+curr_invoice_req.id;
}//func
</script>


<script>
jQuery(document).ready(function(){
	setTimeout(function(){
		<?php if($has_new != 0): ?>
			jQuery('.req_ind11').show();
			jQuery('.req_ind.invoice01').show();
			jQuery('.invoice_buts1').show();
		<?php endif; ?>
	}, 100);
});
</script>
