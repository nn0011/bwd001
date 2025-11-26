<?php $billing_billing = '  class="active" ';?>
<?php include('html_part/header.php');?>

	 <div class="tc1 main_right">
		<div class="cont_1">


			<ul  class="tab1x">
				<li class="tab05 active" data-tab="item1">Dashboard</li>
				<li class="tab01" data-tab="item2">Billing Accounts</li>
				<li class="tab02"  data-tab="item2">Unpaid</li>
				<li class="tab03" data-tab="item2">Disconected</li>
				<li class="tab04" data-tab="item6">Billing Request</li>
				<li class="tab04" data-tab="item6">Disconnection Notice</li>
			</ul>

			<div class="box1_white  tab_cont_1"  data-default="item1">

				<div class="tab_item item2">

					<div  class="filter_1">

						<div style="display:inline-block; float:right;">
							<select>
								<option>Period - June 2018</option>
							</select>
						</div>

						<input type="text"  placeholder="Account Number" />
						<input type="text"  placeholder="Meter Number" />
						<input type="text"  placeholder="Last Name" />
						<select>
							<option>Zone A</option>
						</select>

						<img src="img/search.jpg" class="but_filter" />

					</div>
					<br />

					<div class="scroll1">
						<table class="table10 table-bordered  table-hover">
							<tr  class="headings">
								<td width="10%">Account No.</td>
								<td width="10%">Meter No.</td>
								<td width="15%">Name</td>
								<td width="30%">Complete Address</td>
								<td width="5%">Previous Reading</td>
								<td width="5%">Current Reading</td>
								<td width="5%">Consumption</td>
								<td width="5%">Status</td>
							</tr>
							<?php for($x=0;$x<=20;$x++): ?>
							<!------>
							<!------>
							<tr  onclick=""  data-index="<?php echo $x; ?>"  data-box1="new_initiative"  class="cursor1  trig1">
								<td>00112233</td>
								<td>1155644 - 22</td>
								<td>Dela Cruz, Jimmy</td>
								<td>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Surigao del Sur</td>
								<td>00000245</td>
								<td>00000258</td>
								<td>13</td>
								<td>Read</td>
							</tr>
							<!------>
							<!------>
							<?php  endfor; ?>

						</table>
					</div>

					<div style="padding:15px;">
							<ul class="pagination pagination-sm">
							  <li><a href="#">PREVIOUS</a></li>
							  <li><a href="#">1</a></li>
							  <li><a href="#">2</a></li>
							  <li><a href="#">3</a></li>
							  <li><a href="#">4</a></li>
							  <li><a href="#">5</a></li>
							  <li><a href="#">NEXT</a></li>
							</ul>
					</div>


				</div>

				<div class="tab_item item1">
					<div style="padding:15px;">
						<?php include('inc/billing_common/main_dashboard.php'); ?>
					</div>
				</div>






				<div class="tab_item item6">
					<div style="padding:15px;">

							<button onclick="make_billing_request()">Make Billing Request</button>
							<br />
							<br />
							<table class="table10 table-bordered  table-hover">
								<tbody><tr class="headings">
									<td width="150px">Period</td>
									<td>Reading information</td>
									<td>Request Status</td>
									<td>Action</td>
								</tr>

								<?php for($x=0;$x<=10;$x++): ?>
								<tr>
									<td>June  2018</td>
									<td>

										<ul class="item_list1 item_box2">
											<li>Active Accounts  <span>3,500</span></li>
											<li>Meter Read  <span>2,500</span></li>
											<li>Unread Meter <span class="rd">1,000</span></li>
										</ul>

									</td>
									<td><span style="color:blue;">Approved</span></td>
									<td>
										<button>Generate Print</button>
									</td>
								</tr>
								<?php endfor; ?>

							</tbody></table>


					</div>
				</div>





			</div>


		</div>
	 </div>




<?php include('php_mod/pop1.php'); ?>

<div class="new_initiative" style="display:none;">
	<div class="pop_view_info_table">

		<span class="bldme">Name :</span> Dela Cruz, Jimmy
		<br />
		<span class="bldme">Address :</span><span style="display: inline-block;width: 70%;vertical-align: top;padding-left: 10px;">11 Golden Pheasant Street, Barangay Juan Dela Cerna, Surigao del Sur</span>

		<br />
		<br />

		<ul class="item_list1 ">
			<li>Billing Period  <span>June, 2018</span></li>
			<li>Zone  <span>A1</span></li>
			<li>Account Status  <span>Active</span></li>
			<li>Discount Type  <span>Senior</span></li>
			<li>Account Type  <span>Business</span></li>
			<li>Previous Reading  <span>00000245</span></li>
			<li>Current Reading(June, 2018)  <span>00000258</span></li>
			<li>Consumption(cubic meter) <span class="rd">13</span></li>
		</ul>
		<br />
		<br />
		<ul class="item_list1 ">
			<li>Last Payment Made - Mar 20, 2018  <span>1,500</span></li>
			<li>Previous Balance:  <span>1,500</span></li>
			<li>Current Bill <span class="rd">1,000</span></li>
		</ul>

		<br />
		<div style="text-align:center;">
			<button>Print Bill</button>&nbsp;&nbsp;&nbsp;
			<button>Modify Current Reading</button>
		</div>




	</div>
</div>


</div>




<script src="../js/js1.js" ></script>
<link rel="stylesheet" href="../style.css"  />

<style>
.request_billing_button{
    border: 1px solid #108479;
    font-size: 18px;
    padding: 6px;
    padding-bottom: 10px;
    color: #fff;
    padding-top: 10px;
    background: #12474f;
    padding-left: 15px;
    padding-right: 15px;
}

.filter_1 select, .filter_1 button{
	vertical-align:top;
}

</style>

<script>
function make_billing_request(){
	$sure = confirm('are you sure to create Billing Request?');
	if(!$sure){return;}
	alert('Request Sent.');
}
</script>

</body>
</html>
