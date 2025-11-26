<!DOCTYPE html>
<html><head>
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

<body class="cashier_page">
  <div class="row main_row" style="height: 1245px;">
	<div class="user_info">
    <div class="user_details">
        <span class="user_avatar"><img src="../img/cashier_avatar.png"> <span class="username">Cashier Name</span></span> / <span class="logout_btn"> Logout</span>
	</div>
	 <div class="cashier_main">
		<div class="cont_1">
			<div class="filter_1">
				<input type="text" name="accnt_no" id="accnt_no" placeholder="Account No.">
				<input type="text" name="fn" id="fn" placeholder="First Name">
				<img src="../img/search.jpg">
			</div>

		</div> <!--end of cont -->
		<div class="cashier_view">
		    <table class="table10 table-bordered  table-hover">
		        <tbody>
		        <tr class="headings">
		            <td width="10%">Account No.</td>
		            <td width="10%">Meter No.</td>
		            <td width="15%">Name</td>
		            <td width="30%">Complete Address</td>
		            <td width="5">Bill</td>
		            <td width="5%">Status</td>
		            <td width="5%">Action</td>
		        </tr>
		        <?php for($x=0;$x<=15;$x++):?>
		        <tr class="cursor1 trig1" data-box1="cashier_view_info">
		        	<?php if(($x == 3) || ($x == 7) || ($x == 14)) {?>
					<td>00112233</td>
		            <td>336611-123</td>
		            <td>Agusa, Jimmy</td>
		            <td>Lianga Street, Barangay, Surigao</td>
		            <td>P 100.23</td>
		            <td><span class="payment_status2">Disconnected</span></td>
		            <td>View</td>
		            <?php }else if(($x == 2) || ($x == 5) || ($x == 11)) {?>
		            <td>00112233</td>
		            <td>336611-123</td>
		            <td>Agusa, Jimmy</td>
		            <td>Lianga Street, Barangay, Surigao</td>
		            <td>P 100.23</td>
		            <td><span class="payment_status3">Unpaid</span></td>
		            <td>View</td>
		        	<?php }else{ ?>
	        		<td>00112233</td>
		            <td>336611-123</td>
		            <td>Agusa, Jimmy</td>
		            <td>Lianga Street, Barangay, Surigao</td>
		            <td>P 100.23</td>
		            <td><span class="payment_status">Paid</span></td>
		            <td>View</td>
		        	<?php } ?>
		        </tr>
		        <?php endfor; ?>

		    </tbody></table>
		</div>
		<div class="pagination_container">
		 <ul class="pagination pagination-sm">
		    <li><a href="#"><< Previous</a></li>
		    <li><a href="#">1</a></li>
		    <li><a href="#">2</a></li>
		    <li><a href="#">3</a></li>
		    <li><a href="#">4</a></li>
		    <li><a href="#">5</a></li>
		    <li><a href="#">Next >></a></li>
		 </ul>
		</div>
	 </div> <!-- end of cashier main -->
	</div>


</div>

<?php include('../php_mod/pop1.php'); ?>

<div class="cashier_view_info">
	<div class="pop_view_info_table pop_table2">
		<ul class="item_list1">
            <li>Name   <span>Agusa, Jimmy</span></li>
            <li>Address<span>Lianga Street, Barangay, Surigao</span></li>
            <li>Account No.<span>00112233</span></li>
            <li>Meter No.<span>336611-123</span></li>
            <li>Last Payment Made<span>P 300.00</span></li>
            <li>Last Payment Date<span>May 2, 2018</span></li>
            <br>
            <li>Previous Balance<span>P 1,200.36</span></li>
            <li>Discount<span>P 200.00</span></li>
            <li>Current Bill<span>June 2018 - P 200.00</span></li>
            <br>
            <li>Total Bill<span>P 1,600.36</span></li>
        </ul>
		<br><br>
		<div class="payment_section">
			<input type="number">
			<br>
			<button class="btn1">Make Payment</button>
			<br>
			<button class="btn2">Cancel</button>
		</div>

	</div>
</div>
<style type="text/css">
	.cashier_page label:after {
	    content: ":";
	    position: absolute;
        left: 150px;
	}

	.cashier_page .text_input {
	    position: absolute;
	    left: 160px;
	    width: 100%;
    	max-width: 500px;
	}

	.cashier_page .pop_table2 .text_input {
	    text-align: left;
	    padding: 0;
	    font-size: 13px;
	    width: 300px;
	}

	.cashier_page .pop_table2 .amnt {
	    float: right;
	}

	.cashier_page .payment_section {
	    text-align: center;
	    padding: 25px;
	    background-color: #ccc;
	}

	.cashier_page .payment_section button {
	    width: 100%;
	    max-width: 131px;
	    margin-bottom: 5px;
	}

	.cashier_page .payment_section .btn2 {
	    position: relative;
	    left: -2px;
	}

	.cashier_page .payment_section input {
	    margin-bottom: 5px;
        background-image: url(../img/philippine-peso_16.png);
	    background-repeat: no-repeat;
	    background-position-y: 2px;
	    text-align: right;
	}

	.cashier_page .curr_bill_section span,
	.cashier_page .curr_bill_section label {
    	color: red;
	}

	.cashier_page .cashier_view_info {
	    display: none;
	}

</style>

<script src="../js/js1.js"></script>
<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/css-styles.css">
<script src="../bootstrap/js/bootstrap.min.js"></script>




<div id="screenleapDiv" style="position:fixed;right:1px;bottom:1px;visibility:hidden;width:1px;height:1px" installed="true" sharing="false" eventlisteneradded="true"></div></body></html>
