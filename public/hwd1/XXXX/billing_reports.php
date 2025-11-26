<?php 
$billing_reports = '  class="active" ';
?><!DOCTYPE html>
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
		
		<?php include_once('inc/left_menu1.php'); ?>

		
			
	 </div>
	 
	 <div class="tc1 main_right">
		<div class="cont_1">
			
			
			<ul  class="tab1x">
				<li class="tab01 active" data-tab="dashboard1">Dashboard</li>
				<li class="tab02"  data-tab="accounts1">Accounts</li>
				<li class="tab03" data-tab="item3">Billings</li>
				<li class="tab04" data-tab="item4">Collections</li>
				<li class="tab04" data-tab="item4">Consumptions</li>
			</ul>
			
			<div class="box1_white  tab_cont_1"  data-default="dashboard1">
				
				<div class="tab_item  dashboard1">
					<div style="padding:15px;">

						<div class="dec_box1">
							<div class="dec_box_head">Aging of account</div>
							<div class="dec_box_body">
								<button>Aging of account</button>
								<br />
								<br />
								<button>Aging receivables</button>
								<br />
								<br />
								<button>Aging receivables summary</button>
								<br>
								<br>
								<div style="text-align:center;"></div>
							</div>
						</div>

						<div class="dec_box1">
							<div class="dec_box_head">Balances</div>
							<div class="dec_box_body">
								<button>Account balances</button>
								<br />
								<br />
								<button>Monthly ending balances</button>
								<br />
								<br />
								<button>Ssummary of account balances</button>
								<br>
								<br>
								<div style="text-align:center;"></div>
							</div>
						</div>
																		
						
					</div>
				</div>
				
				<div class="tab_item  accounts1">
					<div style="padding:15px;">
						Coming Soon
					</div>
				</div>
				
				<div class="tab_item item2">
					<div style="padding:15px;">
						Coming Soon
					</div>
				</div>
				
				<div class="tab_item item3">
					<div style="padding:15px;">
						Coming Soon
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
