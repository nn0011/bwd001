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

<body class="login_page">
  <div class="row main_row" style="height: 1245px;">
	<div class="user_info">
    
	 <div class="login_main">
	 <?php //<div class="panel-heading">Login</div> ?>
		<div class="panel-body cont_1">			
			<div class="filter_1">
				<input type="text" name="accnt_no" id="accnt_no" placeholder="Username">				
				<input type="password" name="passw" id="passw" placeholder="Password">				
			</div>			
			<div class="rem">
			<div class="checkbox">
			<label><input type="checkbox" name="remember"> Remember Me</label>
			</div>
            </div>
            <div class="log_func">
               <button type="submit" class="btn btn-primary btn-md">Login</button>
               <a href="#" class="btn btn-link">Forgot Your Password?</a>
            </div>	
		</div> <!--end of cont -->
	 </div> <!-- end of login main -->
	</div>	 
	
	  
</div>  

<?php include('../php_mod/pop1.php'); ?>	 

<style type="text/css">
	.login_page label:after {
	    content: ":";
	    position: absolute;
        left: 150px;
	}

	.login_page .text_input {
	    position: absolute;
	    left: 160px;
	    width: 100%;
    	max-width: 500px;
	}

	.login_page .pop_table2 .text_input {
	    text-align: left;
	    padding: 0;
	    font-size: 13px;
	    width: 300px;
	}

	.login_page .pop_table2 .amnt {
	    float: right;
	}

	.login_page .payment_section {
	    text-align: center;
	    padding: 25px;
	    background-color: #ccc;
	}

	.login_page .payment_section button {
	    width: 100%;
	    max-width: 131px;
	    margin-bottom: 5px;
	}

	.login_page .payment_section .btn2 {
	    position: relative;
	    left: -2px;
	}

	.login_page .payment_section input {
	    margin-bottom: 5px;
        background-image: url(../img/philippine-peso_16.png);
	    background-repeat: no-repeat;
	    background-position-y: 2px;
	    text-align: right;
	}

	.login_page .curr_bill_section span, 
	.login_page .curr_bill_section label {
    	color: red;
	}

	.login_page .cashier_view_info {
	    display: none;
	}

	.login_page .cont_1{
		width: 100%;
	    max-width: 450px;
    	margin: 0 auto;
	    padding: 35px 0 50px;
	}

	.login_main {
		margin: 0 auto;
	    width: 100%;
	    max-width: 700px;
	    position: relative;
	    top: 200px;
	    background-color: white;
	    /*border: 1px solid #108479;*/
	    border-radius: 15px;
        box-shadow: 0px 0px 5px 1px rgb(107, 160, 116);
    	border-color: unset;
	}

	.login_main .filter_1 input[type="text"],
	.login_main .filter_1 input[type="password"] {
	    width: 100%;
	    margin-bottom: 10px;
	    text-align: center;
	    font-size: 14px;
	}

	.login_main .filter_1 input[type="password"]{
	    padding: 10px;
	    color: #108479;
	    background: #e0e0e0;
	    border: 1px solid;
    	border-radius: 0px;
	}

	.login_page .row.main_row {
	    background-color: #12474f;
	}

	.panel-heading {
	    border-bottom: 1px solid #ccc;
	    background-color: #108479;
	    border-top-left-radius: 12px;
	    border-top-right-radius: 12px;
	    color: white;
	    font-size: 18px;
	    text-transform: uppercase;
	    font-weight: bold;
        border-top: 1px solid #108479;
	}

	input#accnt_no {
	    background-image: url(../img/user.png);	 
	}
	
	input#passw{
	    background-image: url(../img/key.png);
	}

	input#accnt_no,
	input#passw{
		background-repeat: no-repeat;
	    background-position-y: 8px;
	    background-position-x: 5px;
	}

	.login_page .user_info{
		background-color: unset;
     	border-bottom: unset; 
	}

	.rem{
		font-size: 14px
	}

	.log_func,
	.rem {
	    border: 0;	    
	    width: 100%;	    
	    margin: 0 75px;
	}

	.checkbox label:after {
	    content: "";
	}

	.log_func {
	    display: inline-flex;
	}

	.log_func button,
	.log_func a{
		font-size: 14px;
	   	/* end*/
	}
		
</style>

<script src="../js/js1.js"></script>
<link rel="stylesheet" href="../style.css">
<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/css-styles.css">
<script src="../bootstrap/js/bootstrap.min.js"></script>




<div id="screenleapDiv" style="position:fixed;right:1px;bottom:1px;visibility:hidden;width:1px;height:1px" installed="true" sharing="false" eventlisteneradded="true"></div></body></html>	