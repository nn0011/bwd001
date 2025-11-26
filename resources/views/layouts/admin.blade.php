<?php

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>ADMIN</title>

	<link rel="stylesheet" href="/jss/bootstrap.min.css">
	
	<script src="/jss/jquery.min.js"></script>
	<script src="/jss/bootstrap.min.js"></script>

	<link href="/datepicker1/datepicker.min.css" rel="stylesheet" />
	<script src="/datepicker1/datepicker.min.js"></script>

	<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="/jqplot/excanvas.js"></script><![endif]-->
	<script src="/jqplot/jquery.jqplot.min.js"></script>
	<link href="/jqplot/jquery.jqplot.min.css" rel="stylesheet" />

    <script class="include" language="javascript" type="text/javascript" src="/jqplot/plugins/jqplot.barRenderer.js"></script>
    <script class="include" language="javascript" type="text/javascript" src="/jqplot/plugins/jqplot.categoryAxisRenderer.js"></script>
    <script class="include" language="javascript" type="text/javascript" src="/jqplot/plugins/jqplot.pointLabels.js"></script>


</head>
<body>

 <div  class="row main_row">
	 <div class="bgc1 main_left tc1">

		<div class="head_logo1">
		</div>

		<?php  include_once('../resources/views/layouts/hwd1_html/admin_left_menu1.php');  ?>

	 </div>

	 <div class="tc1 main_right">
		<div class="cont_1">
			{{-- <div id="chart1" style="width:500px;"></div> --}}
			@yield('content')
		</div>
	 </div>

	@yield('inv_include')

</div>



<script src="/hwd1/js/js1.js" ></script>
<script src="/hwd1/js/request010.js" ></script>
<link rel="stylesheet" href="/hwd1/style.css"  />

@yield('scripts')


<style>
.form-control{
	display:inline-block;
	width:100%;
	margin-bottom:10px;
}
.name_fileds input{
    display: inline-block;
    width: 32.7%;
}

.req_ind{
    color: red;
    text-shadow: 0px 0px 20px yellow;
    font-size: 10px;
    margin-left: 5px;
}
</style>

<?php  include_once('../resources/views/layouts/hwd1_html/foot01.php');  ?>

</body>
</html>
<?php /* */ ?>
