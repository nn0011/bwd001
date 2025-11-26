<?php 
$billing_collection = '  class="active" ';
?>
<?php include('html_part/header.php');?>

	 
	 <div class="tc1 main_right">
		<div class="cont_1">
			
			
			<ul  class="tab1x">
				<li class="tab01 active" data-tab="dashboard11">Dashboard</li>
				<li class="tab02"  data-tab="item1">Account Due</li>
				<li class="tab03" data-tab="item1">Uncollected Account</li>
			</ul>
			
			<div class="box1_white  tab_cont_1"  data-default="dashboard11">
				
				<div class="tab_item item1">
					
					<div  class="filter_1">
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
								<td width="5%">Account No.</td>
								<td width="15%">Name</td>
								<td width="30%">Complete Address</td>
								<td width="5%">Previous Balance</td>
								<td width="5%">Current Balance</td>
								<td width="10%">Due Date</td>
								<td width="5%">Status</td>
							</tr>
							<?php for($x=0;$x<=20;$x++): ?>
							<!------>
							<!------>
							<tr  onclick=""  data-index="<?php echo $x; ?>"  data-box1="new_initiative"  class="cursor1  trig1">
								<td>00112233</td>
								<td>Dela Cruz, Jimmy</td>
								<td>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Surigao del Sur</td>
								<td>0.00</td>
								<td>1,500.00</td>
								<td>June 21, 2018</td>
								<td><span class="rd">Collected</span></td>
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
						<?php include('inc/billing_common/dash_collection.php'); ?>
					</div>
				</div>
				
				<div class="tab_item item3">
					<div style="padding:15px;">
						AAAAA3
					</div>
				</div>
				
				<div class="tab_item item4">
					<div style="padding:15px;">
						AAAAA4
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
		
		<span class="bldme">Name :</span> Dela Cruz, Jimmy
		<br />
		<span class="bldme">Address :</span><span style="display: inline-block;width: 70%;vertical-align: top;padding-left: 10px;">11 Golden Pheasant Street, Barangay Juan Dela Cerna, Surigao del Sur</span>
		<br />
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

			
			
	</div>
</div>
	 
	  
</div>  



<script src="../js/js1.js" ></script>
<link rel="stylesheet" href="../style.css"  /> 

</body>
</html>
