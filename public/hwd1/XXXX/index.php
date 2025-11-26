<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>HWD</title>


<!--
<script src="bootstrap/js/jquery-3.3.1.slim.min.js" ></script>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" >
<script src="bootstrap/js/bootstrap.js"></script>
<script src="bootstrap/js/popper.min.js" ></script>
-->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</head>

<body>
 
 <div  class="row main_row">
	 <div class="bgc1 main_left tc1">
		 
		<div class="head_logo1">
			<img src="img/logo1.jpg" />
		</div>
		
		<ul  class="menu1">
			<li><a href="#">Dashboard</a></li>
			<li><a href="#">Reports</a></li>
			<li><a href="#">Billing</a></li>
			<li class="active"><a href="#">Collection</a></li>
			<li><a href="#">Reading</a></li>
			<li><a href="#">Accounts</a></li>
			<li><a href="#">System User</a></li>
		</ul>
		
			
	 </div>
	 
	 <div class="tc1 main_right">
		<div class="cont_1">
			
			
			<ul  class="tab1x">
				<li class="tab01 active" data-tab="item1">Paid</li>
				<li class="tab02"  data-tab="item2">Unpaid</li>
				<li class="tab03" data-tab="item3">Disconected</li>
				<li class="tab04" data-tab="item4">Graph</li>
			</ul>
			
			<div class="box1_white  tab_cont_1"  data-default="item1">
				
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
								<td>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Hinatuan , Surigao del Sur</td>
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
				
				<div class="tab_item item2">
					<div style="padding:15px;">
						AAAAA2
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
		<span class="bldme">Address :</span><span style="display: inline-block;width: 70%;vertical-align: top;padding-left: 10px;">11 Golden Pheasant Street, Barangay Juan Dela Cerna, Hinatuan , Surigao del Sur</span>
		<br />
		<span class="bldme">Zone :</span> A1
		<br />
		<span class="bldme">Contact Number :</span> 12356
		<br />
		<span class="bldme">Status :</span> Active
		<br />
		<span class="bldme">Account Type :</span> Business
		<br />
		<span class="bldme">Discount Type :</span> Senior
		<br />
		<span class="bldme">Age :</span> 70
		<br />
		<br />
		
		<table class="table10 table-bordered  table-hover">
			<tr  class="headings">
				<td >Date</td>
				<td >Amount</td>
				<td width="50px">Status</td>
			</tr>	
			<tr>
				<td>March</td>
				<td>1500.00</td>
				<td>Payed</td>
			</tr>	
			
			<tr>
				<td>Febuary</td>
				<td>1500.00</td>
				<td>Payed</td>
			</tr>	
			
			<tr>
				<td>January</td>
				<td>1500.00</td>
				<td>Payed</td>
			</tr>	
		</table>
		<br />
		<table style="width:100%;" class="pop_view_info_table_bill">
			
			<tr>
				<td> <span>Last Payment Made - Mar 20, 2018</span></td>
				<td  width="50px">1,500.00</td>
			</tr>

			<tr>
				<td> <span>Previous Balance:</span></td>
				<td  width="50px">1,500.00</td>
			</tr>
			
			<tr>
				<td> <span>Current Bill:</span></td>
				<td  width="50px">1,500.00</td>
			</tr>			
			
		</table>
		
			

			
			
	</div>
</div>
	 
	  
</div>  



<script src="js/js1.js" ></script>
<link rel="stylesheet" href="style.css"  /> 

</body>
</html>
