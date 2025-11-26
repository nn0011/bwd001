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

<body class="billing_account">
 
 <div  class="row main_row">
	 <div class="bgc1 main_left tc1">
		 
		<div class="head_logo1">
			<img src="./img/logo1.jpg" />
		</div>
		
		<ul  class="menu1">
			<li><a href="#">Dashboard</a></li>
			<li><a href="#">Reports</a></li>
			<li><a href="#">Billing</a></li>
			<li><a href="#">Collection</a></li>
			<li><a href="#">Reading</a></li>
			<li class="active"><a href="#">Accounts</a></li>
			<li><a href="#">System User</a></li>
		</ul>
		
			
	 </div>
	 
	 <div class="tc1 main_right">
		<div class="cont_1">
			
			
			<ul  class="tab1x">
				<li class="tab01 active" data-tab="item1">Accounts List</li>
				<li class="tab02"  data-tab="item2">Graph</li>				
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
						<select>
							<option>Active</option>
							<option>Inactive</option>
						</select>
						<img src="./img/search.jpg" class="but_filter" />
						<img src="./img/print_icon.png" class="but_filter icon2" />
						<img src="./img/add_account_icon.png" class="but_filter icon2" />
					</div>
					<br />
					
					<div class="scroll1">
						<table class="table10 table-bordered  table-hover">
							<tr  class="headings">
								<td width="10%">Account No.</td>
								<td width="10%">Meter No.</td>
								<td width="15%">Name</td>
								<td width="30%">Complete Address</td>								
								<td width="5%">Status</td>
								<td width="10%">Action</td>
							</tr>
							<?php for($x=0;$x<=20;$x++): ?>
							<!------>
							<!------>
							<tr  data-index="<?php echo $x; ?>" class="cursor1">
								<td>00112233</td>
								<td>1155644 - 22</td>
								<td>Dela Cruz, Jimmy</td>
								<td>11 Golden Pheasant Street, Barangay Juan Dela Cerna, <br>Hinatuan , Surigao del Sur</td>								
								<?php 
								if (($x == 3 ) || ($x == 7 ) || ($x == 13 ) || ($x == 19 ))  {
									echo '<td class="status_inactive">Inactive</td>';
								}
								else{
									echo '<td class="status_active">Active</td>';
								}
								?>
								
								<td>
								 <span onclick="" data-box1="edit_account" class="btn btn-primary btn-md trig1">Edit</span>
								 <span onclick="" data-box1="view_account" class="btn btn-primary btn-md trig1">View</span>
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
				
				<div class="tab_item item2">
					<div style="padding:15px;">
						<div  class="filter_1">						
						<select>
							<option>Weekly</option>
						</select>
						<select>
							<option>Monthly</option>
						</select>
						<select>
							<option>Year</option>
						</select>
						
						<img src="./img/search.jpg" class="but_filter icon1" />
						<img src="./img/print_icon.png" class="but_filter icon2" />
						<img src="./img/add_account_icon.png" class="but_filter icon2" />
						<img src="./img/legend.png" class="but_filter icon2 legend_icon" />
					</div>
					<br />
					<div class="graph1">
						<img src="./img/graph.jpg"/>
					</div>
				</div>
				
				<div class="tab_item item3">
					<div style="padding:15px;">
						AAAAA3
					</div>
				</div>
				
			</div>
			
				
		</div>
	 </div>
	 
	 
	 

<?php include('./php_mod/pop1.php'); ?>	 

<div class="edit_account" style="display:none;">
	<div class="pop_view_info_table">
		<label>Full Name</label>
		 <span class="text_input filter_1">
		   <input type="text" name="lname" id="lname" placeholder="Lastname">, 
		   <input type="text" name="fname" id="fname" placeholder="Firstname"> 
		   <input type="text" name="mname" id="mname" placeholder="Middlename"> 
		 </span>
		<br>
		<label>Street</label>
		<span class="text_input filter_1">
		   <input type="text" name="strname" id="strname" placeholder="Steet Name">
		 </span>
		<br>
		<label>Barangay</label>
		<span class="text_input filter_1">
		   <input type="text" name="brgyname" id="brgyname" placeholder="Barangay Name"> 		   
		 </span>
		<br>
		<label>Province</label>
		<span class="text_input filter_1">
		   <select>
		   	 <option>Davao Del Norte</option>
		   	 <option>Davao Occidental</option>
		   	 <option>Davao Del Sur</option>
		   	 <option>Surigao Del Norte</option>
		   	 <option>Surigao Del Sur</option>
		   </select>
		 </span>
		<br>
		<label>Zone</label>
		<span class="text_input filter_1">
		   <select>
		   	 <option>A</option>
		   	 <option>B</option>
		   	 <option>C</option>
		   	 <option>D</option>
		   	 <option>E</option>
		   </select>
		 </span>
		<br>		
		<label>Contact No</label>
		<span class="text_input filter_1">
		   <input type="text" name="cntct" id="cntct" placeholder="Contact No">		   
		 </span>
		<br>
		<label>Birthdate</label>
		<span class="text_input filter_1">
		  <select>
		  	<?php for($x=2018;$x>=1965;$x--): ?>
		   	 <option><?php echo $x; ?></option>		   	 
		   	<?php endfor; ?>
		   </select>
		   <select>
		  	<?php for($y=1;$y<=12;$y++): ?>		  			
		   	 <option><?php echo $y; ?></option>		   	 
		   	<?php endfor; ?>
		   </select>
		   <select>
		  	<?php for($z=1;$z<=31;$z++): ?>
		   	 <option><?php echo $z; ?></option>		   	 
		   	<?php endfor; ?>
		   </select>
		 </span>
		<br>
		<label>Age</label>
		<span class="text_input filter_1">
		   <input type="text" name="age" id="age"> 		   
		 </span>
		<br>
		<label>Status</label>
		<span class="text_input filter_1">
		   <select>
		   		<option>Active</option>
		   		<option>Active</option>
		   </select>
		 </span>
		<br>
		<label>Acc. Type</label>
		<span class="text_input filter_1">
		   <select>
		   		<option>Commercial</option>
		   		<option>Residential</option>
		   </select>
		 </span>
		<br>
		<label>Discount Type</label>
		<span class="text_input filter_1">
		   <select>
		   		<option>Senior</option>
		   		<option>Different Abled</option>		   		
		   </select>
		 </span>
		<br>
		<label></label>	
		<span class="text_input filter_1 rad_group">
	      <input type="radio" name="optradio"><span class="radio_label">Government</span>
	      <input type="radio" name="optradio"><span class="radio_label">Non - Government</span>
	    </span>
	    <br>
	    <span class="text_input filter_1">
     	 <img src="./img/file_icon.png" class="icon1 btm_icon"/>
     	</span>
     	<br><br>
	    <span class="text_input filter_1 btn_group">
	     <button type="button" class="btn btn-primary btn-md">Save</button>
	     <button type="button" class="btn btn-primary btn-md">Cancel</button>
     	</span>
     	<span class="table_container doc_list">
     		<span style="color:#108479;">List of Documents</span>
     		<table class="table10 table-bordered table-hover">
			    <tbody>			    
			    <tr class="headings">
			    	<td width="15%">Filename</td>			    	
			    	<td width="5%"></td>
			    	<td width="5%"></td>
			    </tr>
			    <?php for($xx = 0;$xx <= 15;$xx++): ?>
			    <tr>
			    
			    	<td>Filename</td>			    	
			    	<td>View</td>
			    	<td>Download</td>			    
			    </tr>
			    <?php endfor; ?>	
			    </tbody>
		    </table>
     	</span>
	</div>	
</div>

<div class="view_account" style="display:none;">
	<div class="pop_view_info_table pop_table2">
		<label>Name</label>
		 <span class="text_input filter_1">Dela Cruz, Jimmy</span>
		<br>
		<label>Address</label>
		<span class="text_input filter_1">11 Golden Pheasant Street, Barangay Juan Dela Cerna, Hinatuan , Surigao del Sur</span>
		<br>
		<label>Zone</label>
		<span class="text_input filter_1">A</span>
		<br>
		<label>Contact No.</label>
		<span class="text_input filter_1">123 456 789</span>
		<br>
		<label>Status</label>
		<span class="text_input filter_1">Active</span>
		<br>
		<label>Account Type</label>
		<span class="text_input filter_1">Commercial</span>
		<br>
		<label>Discount Type</label>
		<span class="text_input filter_1">Senior</span>
		<br>
		<label>Age</label>
		<span class="text_input filter_1">70</span>
		<br><br>
		<span class="table_container view_accnt_pay_list ">
     		<span style="color:#108479;">List of Payments</span>
     		<table class="table10 table-bordered table-hover">
			    <tbody>			    
			    <tr class="headings">
			    	<td width="10%">
			    		Date
			    		<select style="color: #000;">
			    		<?php for($yy=2018;$yy>=2000;$yy--): ?>
			    			<option><?php echo $yy; ?></option>
			    		<?php endfor; ?>			    			
			    		</select>
			    	</td>			    	
			    	<td width="5%">Amount</td>
			    	<td width="5%">Status</td>
			    </tr>
			    <tr>
			    
			    	<td>January - February</td>			    	
			    	<td>P 123.25</td>
			    	<td>Paid</td>
			    </tr>
			    <tr>
			    
			    	<td>March - April</td>			    	
			    	<td>P 123.25</td>
			    	<td>Paid</td>
			    </tr>
			    <tr>
			    
			    	<td>May - June</td>			    	
			    	<td>P 123.25</td>
			    	<td>Paid</td>
			    </tr>
			    <tr>
			    
			    	<td>July - August</td>			    	
			    	<td>P 123.25</td>
			    	<td>Paid</td>
			    </tr>
			    <tr>
			    
			    	<td>September - October</td>			    	
			    	<td>P 123.25</td>
			    	<td>Paid</td>
			    </tr>
			    <tr>
			    
			    	<td>November - December</td>			    	
			    	<td>P 123.25</td>
			    	<td class="stat_unpaid">Unpaid</td>
			    </tr>
			    </tbody>
		    </table>

     	</span>
     	<br>
 	  	<span class="text_input filter_1">
     		<button type="button" class="btn btn-primary btn-md pay_list_pager"><< Previous</button>
     		<button type="button" class="btn btn-primary btn-md pay_list_pager">Next >></button>
     	</span>
     	<br><br><br><br>
     	<label class="btm_label">LAST PAYMENT MADE</label>
     	<span class="text_input filter_1 btm_txt btm_txt1">December 2018	<span class="amount">P 300.00</span></span>
     	<br>
     	<label class="btm_label">PREVIOUS BALANCE</label>
     	<span class="text_input filter_1 btm_txt btm_txt1"><span class="amount">P 300.00</span></span>
     	<br><br><br>
     	<label class="btm_label curr_bill">CURRENT BILL</label>
     	<span class="text_input filter_1 btm_txt btm_txt2"><span class="amount">P 123.25</span></span>
     	<br>

	</div>
</div>	
	 
	  
</div>  

<style type="text/css">

	.icon2{
		float:right;		
	}
	
	.icon1:hover,
	.icon1:focus,
	.icon2:hover,
	.icon2:focus{
		cursor: pointer;
	}
	.graph1 {
	    text-align: center;
	}

	.icon2.legend_icon {
	    height: 30px;
	    margin-top: 3px;
	    margin-right: 20px;
	}

	.status_inactive{
		color: red;
	}

	tr td {
	    text-align: center;
	}

	label:after {
	    content: ":";
	    position: absolute;
        left: 140px;
	}

	.billing_account .back_1 .box1 {
	    width: 100%;
	    max-width: 780px;
        height: 780px;
    	margin-top: 5%;
	}

	.billing_account .pop_view_info_table {
	    width: 100%;
	    max-width: 700px;
	}

	.text_input input[type="radio"] {
	    position: relative;
	    top: 5px;
	}

	.radio_label {
	    position: relative;
	    top: 2px;
	    margin-right: 25px;
	}

	.text_input {
	    position: absolute;
	    left: 160px;
	    width: 100%;
    	max-width: 500px;
	}
	
	.billing_account .pop_view_info_table label {
	    padding: 0;
	    color: #000;
	    margin-top: 28px;
	}

	.rad_group {
	    margin-top: 8px;
	}
	
	.btn_group {
	    margin-top: 25px;
	}

	.filter_1 input[name="strname"],
	.filter_1 input[name="brgyname"] {
	    width: 97.5%;
	}

	.btn-primary:hover {
	    color: #fff;
	    background-color: red;
	    border-color: red;
	}

	.table_container.doc_list {
	    position: absolute;
	    bottom: 100px;
	    right: 15px;
        width: 100%;
    	max-width: 350px;
    	height: 200px;
    	overflow-y: scroll;
	}

	.table_container.view_accnt_pay_list {
	    position: relative;
	    left: 0;
	    width: 100%;
	    max-width: 500px;
	    height: 200px;
	    overflow-y: scroll;
	}

	.stat_unpaid{
		color:blue;
	}

	.pop_table2 label {
	    margin-top: 5px !important;
	}

	.pop_table2 .text_input {
	    padding: 6px 10px;
	}

	.view_accnt_pay_list tr td {
	    text-align: center !important;
	}

	.pay_list_pager:first-child {
	    float: left;
	}

	.pay_list_pager:last-child {
	    float: right;
	}	

	.btm_label:after {
	    content: ":";
	    position: absolute;
	    left: 185px;
	}

	.btm_txt {
	    margin-left: 100px;
	}

	.amount {
	    position: absolute;
	    right: 250px;
	}

	.btm_txt2 span {
	    font-weight: bold;
	}

	.curr_bill, .btm_txt2 span {
	    font-size: 18px;
	}

	/* end */

</style>
<script src="./js/js1.js" ></script>
<link rel="stylesheet" href="./style.css"  /> 

</body>
</html>

