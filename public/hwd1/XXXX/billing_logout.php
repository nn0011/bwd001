<?php 
$billing_logout = '  class="active" ';
?>
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
		
		<?php include_once('inc/left_menu1.php'); ?>

			
	 </div>
	 
	 <div class="tc1 main_right">
		<div class="cont_1">
			
			
			<ul  class="tab1x">
				<li class="tab01 active" data-tab="item1">Logout</li>
			</ul>
			
			<div class="box1_white  tab_cont_1"  data-default="item1">
				
				<div class="tab_item item1">
					<div style="padding:15px;">
						
						<button>Logout?</button>
					
					</div>
				</div>
				
				
			</div>
			
				
		</div>
	 </div>
	 
	 

</div>  



<script src="js/js1.js" ></script>
<link rel="stylesheet" href="style.css"  /> 

</body>
</html>
