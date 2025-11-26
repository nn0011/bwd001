<?php


	$cash_url = '/hwd1/';
	$head_info  = get_coll_header_info();

	$num_trx  = ($head_info->ttl_trx);
	$ttl_coll = number_format($head_info->ttl_col,2);

	//~ echo '<pre>';
	//~ print_r($head_info->toArray());
	//~ die();

	//echo date("Y-m-d");
	//die();

	$dat01_1  = strtotime(date("Y-m-d"));
	$dat00_0  = date('Y-m-d');

	if(!empty($trd = @$_GET['trd']))
	{
		$dat01_2 = strtotime($trd);
		if($dat01_2 <= $dat01_1){
			$dat00_0 = date('Y-m-d', $dat01_2);
		}
	}


?>
<!DOCTYPE html>
<html><head>
<meta charset="UTF-8">
<title><?php echo WD_NAME; ?></title>

<script src="/jss/jquery.min.js"></script>
<script src="/jss/bootstrap.min.js"></script>
<script src="/datepicker1/datepicker.min.js"></script>
<?php /*<script src="<?php  echo $cash_url; ?>js/js1.js"></script>*/ ?>
<script src="<?php  echo $cash_url; ?>bootstrap/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="<?php  echo $cash_url; ?>style.css">
<link rel="stylesheet" href="<?php  echo $cash_url; ?>bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php  echo $cash_url; ?>css/css-styles.css">
<link rel="stylesheet" href="/jss/bootstrap.min.css">
<link href="/datepicker1/datepicker.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php  echo $cash_url; ?>css/collection-common.css">

</head>

<body class="cashier_page">



	<div class="row main_row">
		<div class="user_info">
				<div class="user_details">
					<span class="date_total_col"><?php echo date('F d, Y ', strtotime($dat00_0)); ?>  | <?php echo @$num_trx; ?> | <?php echo @$ttl_coll; ?></span>
					<a href="/collections/"><span>Cashier</span></a>
						&nbsp;&nbsp; | &nbsp;&nbsp;
					<a href="/collections/invoices"><span>Invoices</span></a>
						&nbsp;&nbsp; | &nbsp;&nbsp;
					<a href="/collections/activities"><span>Activities</span></a>
						&nbsp;&nbsp; | &nbsp;&nbsp;
					<a href="/collections/reports"><span>Reports</span></a>
					<span class="logo-head" style="float: left;"><img src="<?php echo LOGO_SRC; ?>" style="width:50px;height:auto;"></span>
					<span class="user_avatar"><img src="<?php  echo $cash_url; ?>img/cashier_avatar.png"> <span class="username" style="display:none;"><div><?php echo @$user_info['name']; ?></div></span></span>
					<span class="logout_btn"><a href="/collections/logout">Logout</a></span>
					<br />
				</div>
		</div>

		<div style="clear:both;"></div>

		<div class="cashier_click_001">
		</div>
		<div class="cashier_main">
		    @yield('content')
	    </div>

	</div>

@yield('inv_include')
<style>
.user_info{height:auto;}

.cashier_page .user_info,
.cashier_main{
    z-index: 2;	
}
.cashier_click_001{
    display: inline-block;
    width: 100%;
    height: 100%;
    position: absolute;
    z-index: 1;	
}
</style>

<?php  include_once('../resources/views/layouts/hwd1_html/foot01.php');  ?>


</body></html>
