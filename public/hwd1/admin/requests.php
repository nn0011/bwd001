<?php 
$billing_requests = '  class="active" ';
?>
<?php include('html_part/header.php');?>

	 
	 <div class="tc1 main_right">
		<div class="cont_1">
			
			
			<ul  class="tab1x">
				<li class="tab01 active" data-tab="dashboard1">Dashboard</li>
				<li class="tab02"  data-tab="accounts1">Application Approval</li>
				<li class="tab03" data-tab="item3">Billing Request</li>
				<li class="tab04" data-tab="item4">Other Approval</li>
			</ul>
			
			<div class="box1_white  tab_cont_1"  data-default="dashboard1">
				
				<div class="tab_item  dashboard1">
					<div style="padding:15px;">
						
						Welcome
						
					</div>
				</div>
				
				<div class="tab_item  accounts1">
					<div style="padding:15px;">
						
						<table class="table10 table-bordered  table-hover">
							<tbody><tr class="headings">
								<td width="10%">Refference #</td>
								<td width="15%">Name</td>
								<td width="30%">Complete Address</td>
								<td width="5%">Application Type</td>
								<td width="5%">Application Date</td>
								<td width="5%">Requested By</td>
								<td width="5%">Remaks</td>
								<td width="5%">Status</td>
							</tr>
							<?php  for($x=1;$x<=10;$x++) : ?>

							<!------>
							<!------>
							<tr onclick="" data-index="0" data-box1="new_initiative" class="cursor1  trig1">
								<td>00112233</td>
								<td>Dela Cruz, Jimmy</td>
								<td>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Surigao del Sur</td>
								<td><span class="rd">New / Reconection / Discount / Senior Citizine  </span></td>
								<td>Jan 21, 2018</td>
								<td>Jason, Marquez</td>
								<td>Remarks</td>
								<td><span class="rd">Pending / Denied / </span><span style="color:blue">Approved</span></td>
							</tr>
							<!------>
							<!------>
							<?php endfor; ?>
						</tbody></table>

						
						
					</div>
				</div>
				
				<div class="tab_item item2">
					<div style="padding:15px;">
						Coming Soon
					</div>
				</div>
				
				<div class="tab_item item3">
					<div style="padding:15px;">
						
						<table class="table10 table-bordered  table-hover">
							<tbody><tr class="headings">
								<td width="10%">Refference #</td>
								<td width="15%">Requested By</td>
								<td width="30%">Request Description</td>
								<td width="5%">Requestesd Date</td>
								<td width="5%">Remaks</td>
								<td width="5%">Status</td>
							</tr>
							<?php  for($x=1;$x<=10;$x++) : ?>

							<!------>
							<!------>
							<tr onclick="" data-index="0" data-box1="new_initiative" class="cursor1  trig1">
								<td>00112233</td>
								<td>Jason, Marquez</td>
								<td>
										<ul>
											<li>Generate Billing for Januany 2018</li>
											<li>Printing of single bill for  Juan Dele Cerna. January 2018 Bill</li>
											<li>Approval for Notice Disconnection  January 2018 </li>
											<li>Approval for Disconnection  January 2018 </li>
										</ul>
								</td>
								<td>Jan 21, 2018</td>
								<td>Remarks</td>
								<td><span class="rd">Pending / Denied / </span><span style="color:blue">Approved</span></td>
							</tr>
							<!------>
							<!------>
							<?php endfor; ?>
						</tbody></table>						
						
					</div>
				</div>
				
				<div class="tab_item item4">
					<div style="padding:15px;">
						Coming Soon
					</div>
				</div>
				
				<div class="tab_item item5">
					<div style="padding:15px;">
						Coming Soon
					</div>
				</div>
				
				<div class="tab_item item6">
					<div style="padding:15px;">
						Coming Soon
					</div>
				</div>
				
				
			</div>
			
				
		</div>
	 </div>
	 
	 
	 

<?php include('php_mod/pop1.php'); ?>	 

<div class="new_initiative" style="display:none;">
	<div class="pop_view_info_table">
		
		<ul class="item_list1 ">
			<li>Request Date  <span>January 20, 2018</span></li>
			<li>Name  <span>Dela Cruz, Jimmy</span></li>
			<li>Address  <span>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Surigao del Sur</span><br class="clear_me" /></li>
			<li>Zone  <span>A1</span></li>
			<li>Discount Type  <span>Senior</span></li>
			<li>Account Type  <span>Business</span></li>
		</ul>
		<br class="clear_me" />
		<ul class="item_list1 ">
			<li>Application   <span>Reconnection</span></li>
			<li>Requested By  <span>Jason Marquez</span></li>
		</ul>
		<br />
		<br />
		<span class="bold">Remaks:</span>
		<p>Not all hatches are game.I've seen it sulk just standing up and not hitting.</p>
		<br />
		<br />
		<p style="text-align:center;">
			<button>Approve</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button>Cancel</button>
		</p>

			
	</div>
</div>
	 
	  
</div>  




<script src="../js/js1.js" ></script>
<link rel="stylesheet" href="../style.css"  /> 

</body>
</html>
