<?php
$billing_meter_reading = '  class="active" ';

?>
<?php include('html_part/header.php');?>


	 <div class="tc1 main_right">
		<div class="cont_1">


			<ul  class="tab1x">
				<li class="tab01 active" data-tab="dashboard11">Dashboard</li>
				<li class="tab02"  data-tab="item1">Meter Officer</li>
				<li class="tab02"  data-tab="item3">Zones</li>
				<li class="tab03"  data-tab="item4">Accounts</li>
			</ul>

			<div class="box1_white  tab_cont_1"  data-default="dashboard11">

				<div class="tab_item item1">

					<br />

					<div class="scroll1">
						<table class="table10 table-bordered  table-hover">
							<tr  class="headings">
								<td width="10%">ID</td>
								<td width="70%">Name</td>
								<td width="20%">Zones</td>
							</tr>
							<?php for($x=0;$x<=10;$x++): ?>
							<!------>
							<!------>
							<tr  onclick=""  data-index="<?php echo $x; ?>"  data-box1="new_initiative"  class="cursor1  trig1">
								<td>#<?php echo $x; ?></td>
								<td>James <?php echo $x; ?></td>
								<td>Zone 1, Zone 2, Zone 3</td>
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

				<div class="tab_item dashboard11">
					<div style="padding:15px;">
						<?php include('inc/billing_common/dash_meter_reading.php'); ?>
					</div>
				</div>

				<div class="tab_item item3">
					<div style="padding:15px;">
					<br />

					<select style="margin-bottom:10px">
						<option>June 2018</option>
						<option>May 2018</option>
						<option>April 2018</option>
						<option>March 2018</option>
					</select>

					<div class="scroll1">
						<table class="table10 table-bordered  table-hover"  style="width:400px">
							<tr  class="headings">
								<td width="20%">Zone ID</td>
								<td width="80%">Zone Info</td>
							</tr>
							<?php for($x=1;$x<=10;$x++): ?>
							<!------>
							<!------>
							<tr  onclick=""  data-index="<?php echo $x; ?>"  data-box1="new_initiative"  class="cursor1">
								<td>#<?php echo $x; ?></td>
								<td>

									<ul class="item_list1"  style="width: 300px;padding: 20px;float:right;">
										<li>Zone  <span>A<?php echo $x; ?></span></li>
										<li>Zone officer  <span>James A. Galay</span></li>
										<li>Active Accounts  <span>3,500</span></li>
										<li>Meter Read  <span>2,500</span></li>
										<li>Unread Meter <span class="rd">1,000</span></li>
									</ul>

								</td>
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
				</div>

				<div class="tab_item item4">
					<div style="padding:15px;">

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
							<tr  onclick=""  data-index="<?php echo $x; ?>"  data-box1="new_initiative"  class="cursor1 ">
								<td>00112233</td>
								<td>1155644 - 22</td>
								<td>Dela Cruz, Jimmy</td>
								<td>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Lianga , Surigao del Sur</td>
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
				</div>

				<div class="tab_item item5">
					<div style="padding:15px;">
						AAAAA5
					</div>
				</div>

				<div class="tab_item item6">
					<div style="padding:15px;">
						AAAAA6
					</div>
				</div>


			</div>


		</div>
	 </div>




<?php include('php_mod/pop1.php'); ?>

<div class="new_initiative" style="display:none;">
	<div class="pop_view_info_table">
			<h3>James Galay</h3>
			<hr />

			<ul class="item_list1">
				<li>Zones  <span>Zone A1, Zone A2, Zone A3</span></li>
			</ul>


	</div>
</div>


</div>



<script src="../js/js1.js" ></script>
<link rel="stylesheet" href="../style.css"  />

</body>
</html>
