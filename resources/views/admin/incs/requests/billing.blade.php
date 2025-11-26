<h1>Billing Requests</h1>

<table class="table10 table-bordered  table-hover"><tbody>

	<tr class="headings">
		<td width="10%">Request name</td>
		<td width="30%">Request description </td>
		<td width="5%">Date dequested</td>
		<td width="5%">Date approved</td>
		<td width="5%">Request status</td>
	</tr>

	<?php

			$index = 0;

			foreach($billing_req['data'] as $kk => $bill):

					$dd1 = json_decode($bill['other_datas']);

					$name1 =  'Billing '.date('F Y', strtotime($dd1->period_year.'-'.$dd1->period_month));
					$billing_req['data'][$kk]['req_name'] = $name1;
					$billing_req['data'][$kk]['remarks'] = $bill['remarks'] ?$bill['remarks']:'none';

	?>
		<tr  onclick="view_biling_request_info(<?php echo $index; ?>)">
			<td>
					<?php echo $name1; ?>
							<?php if($bill['status'] == 'pending'): ?>
								<span class="req_ind">New </span>
							<?php endif; ?>
			</td>
			<td><?php echo $bill['remarks'] ?$bill['remarks']:'none'; ?></td>
			<td><?php echo  date('F d, Y', strtotime($bill['updated_at']));?></td>
			<td><?php  if(!empty($bill['date_stat'])){echo  date('F d, Y', strtotime($bill['date_stat']));}else{echo '-----';} ?></td>
			<td>
						<?php
							switch($bill['status']){

								case 'approved':
									echo '<span style="color:blue;">Approved</span>';
								break;

								case 'canceled':
									echo '<span class="rd">Canceled</span>';
								break;

								case 'ongoing':
									echo '<span  style="color:blue;">Started</span>';
								break;

								case 'pending':
									echo '<span>Pending</span>';
								break;

								default:
								break;
							}
						 ?>

			</td>
		</tr>
	<?php $index++; endforeach; ?>


</tbody></table>

<div class="view_billing_request_info" style="display:none;">
	<div class="pop_view_info_table  view_acct_info_pop" >

		<div>
				<h3 class="f1"></h3>
				<span>Date Requested: <b>Jan 2015</b></span>
				<p>
					<b>Remarks:</b> <br />
					<span class="f2"></span>
				</p>
		</div>

		<div style="text-align:center;margin-top:50px;display:none;"  class="cmd1_but1">
			<button  onclick="approve_biil_req()">Approved</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button  onclick="cancel_biil_req()">Cancel</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button  onclick="pop_close()">Close</button>
		</div>


	</div>
</div>



<script>
var billin_req_data = <?php echo json_encode($billing_req['data']); ?>;
var curr_bill_req = null;

function view_biling_request_info($ind){
		curr_bill_req = billin_req_data[$ind];
		trig1_v2('view_billing_request_info');

		setTimeout(function(){
				jQuery('.pop101 .f1').html(curr_bill_req.req_name);
				jQuery('.pop101 .f2').html(curr_bill_req.remarks);
				if(curr_bill_req.status == 'pending'){
					jQuery('.pop101 .cmd1_but1').show();
				}
		}, 200);

}//func

function approve_biil_req(){
	var approv1 = confirm('Sure to approve ?');
	if(!approv1){return;}
	window.location = '/admin/request/billing/approve/'+curr_bill_req.id;
}//func

function cancel_biil_req(){
	var cancel1 = confirm('Sure to cancel ?');
	if(!cancel1){return;}
	window.location = '/admin/request/billing/cancel/'+curr_bill_req.id;
}//func
</script>

<style>
.back_1  .box1{
    min-height: 200px !important;
}
</style>
