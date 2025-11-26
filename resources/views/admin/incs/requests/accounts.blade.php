<?php



//~ echo '<pre>';
//~ print_r($acct_req['data']);
//~ die();

?>
<h1>Account Requests</h1>

<table class="table10 table-bordered  table-hover">
			<tbody><tr class="headings">
				<td width="3%">Refference #</td>
				<td width="15%">Name</td>
				<td width="20%">Complete Address</td>
				<td width="10%">Application Type</td>
				<td width="10%">Meter #</td>
				<td width="10%">Application Date</td>
				<td width="5%">Requested By</td>
				<td width="5%">Remaks</td>
				<td width="5%">Status</td>
			</tr>
			
			<?php 
			$index = 0;
			foreach($acct_req['data'] as $hqq): ?>
			<!------>
			<!------>
			<tr onclick="view_request_info(<?php echo $index; ?>)" data-index="0"  class="cursor1">
				<td><?php echo $hqq['id']; ?></td>
				<td>
							<?php echo $hqq['account']['lname'].', '.$hqq['account']['fname'].' '.$hqq['account']['mi']; ?>
							<?php if($hqq['status'] == 'pending'): ?>
								<span class="req_ind">New </span>
							<?php endif; ?>
				</td>
				<td><?php echo $hqq['account']['address1']; ?></td>
				<td><span class="rd"><?php 
						//echo $hqq['req_type']; 
						switch($hqq['req_type']){
							
							case 'new_account_approval':
									echo 'New Application';
							break;	
							
							case 'reconnection_approval':
									echo 'Reconnection Approval';
							break;	
							
							default:
								//echo ' New account ';
							break;
						}
				?></span></td>
				<td><?php  echo  @$hqq['account']['meter_number1']; ?></td>
				<td><?php  echo  date('F d, Y', strtotime($hqq['created_at'])); ?></td>
				<td>&nbsp;</td>
				<td>Remarks</td>
				<td>
						<?php
						
							echo request_stat101($hqq['status']);

							//~ switch($hqq['status']){

								//~ case 'approved':
									//~ echo '<span style="color:blue;">Approved</span>';
								//~ break;
								
								//~ case 'canceled':
									//~ echo '<span class="rd">Canceled</span>';
								//~ break;
								
								//~ case 'pending':
									//~ echo '<span>Pending</span>';
								//~ break;
								
								//~ default:
								//~ break;
							//~ }
							
							
							
						 ?>
						 <?php  /*<span style="color:blue">Approved</span>*/ ?>
				</td>
			</tr>
			<!------>
			<!------>
			<?php $index++; endforeach; ?>
</tbody></table>


<div class="view_request_info" style="display:none;">
	<div class="pop_view_info_table  view_acct_info_pop" >
		<h2>Account Info</h2>
		<ul class="item_list1">
			<li>Account Name   <span class="f1"></span></li>
			<li>Account Number   <span class="f2"></span></li>
			<li>Address   <span class="f7"></span> <div style="clear:both;"></div></li>
			<li>Zone   <span class="f3"></span></li>
			<li>Meter #   <span class="f4"></span></li>
<!--
			<li>Account Status   <span class="f5"></span></li>
			<li>Discount Type   <span class="f6"></span></li>
			<li>Application Date   <span class="f8"></span></li>
-->
		</ul>		
		<div style="text-align:center;margin-top:50px;display:none;"  class="cmd1_but1">
			<button  onclick="application_approved()">Approved</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button  onclick="application_canceled()">Cancel</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button  onclick="pop_close()">Close</button>
		</div>
	</div>
</div>




<script>
var  request_data = <?php echo json_encode($acct_req['data']); ?>;	
var curr_req = null;
function view_request_info($ind){
	curr_req = request_data[$ind];
	trig1_v2('view_request_info');
	setTimeout(function(){
		$full_name = request_data[$ind].account.lname+', '+request_data[$ind].account.fname+' '+request_data[$ind].account.mi;
		jQuery('.pop101  .f1').html($full_name);
		jQuery('.pop101  .f2').html(request_data[$ind].account.acct_no);
		jQuery('.pop101  .f3').html(request_data[$ind].account.zone_id);
		jQuery('.pop101  .f4').html(request_data[$ind].account.meter_number1);
		jQuery('.pop101  .f5').html(request_data[$ind].account.acct_status_key);
		jQuery('.pop101  .f6').html(request_data[$ind].account.acct_discount);
		jQuery('.pop101  .f7').html(request_data[$ind].account.address1);
		jQuery('.pop101  .f8').html(request_data[$ind].updated_at);
		
		if(request_data[$ind].status == 'pending'){
			jQuery('.pop101 .cmd1_but1').show();
		}
		
	}, 200);
}

function application_canceled(){
	var cancel1 = confirm('Sure to cancel ?');
	if(!cancel1){return;}
	window.location = '/admin/request/accounts/cancel_acct/'+curr_req.id;
}

function application_approved(){
	var approv1 = confirm('Sure to approve ?');
	if(!approv1){return;}
	window.location = '/admin/request/accounts/approve_acct/'+curr_req.id;
}


</script>
