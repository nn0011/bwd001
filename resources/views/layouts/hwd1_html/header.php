<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin</title>

<?php
/*
<script src="bootstrap/js/jquery-3.3.1.slim.min.js" ></script>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" >
<script src="bootstrap/js/bootstrap.js"></script>
<script src="bootstrap/js/popper.min.js" ></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
*/ ?>

<link rel="stylesheet" href="/jss/bootstrap.min.css">
<script src="/jss/jquery.min.js"></script>
<script src="/jss/bootstrap.min.js"></script>
<?php /*
<link href="/jquery_ui/jquery-ui.css" rel="stylesheet">
<script src="/jquery_ui/jquery-ui.js"></script>
*/ ?>

<link href="/datepicker1/datepicker.min.css" rel="stylesheet">
<script src="/datepicker1/datepicker.min.js"></script>
<script>
var csrf_key = '<?php echo csrf_token(); ?>';


function POST_JS_v2($url, $data1, $func1)
{
	jQuery.post( $url, $data1 ).done(function( data ) {	
			$func1(data);
			return;
	}).fail(function(a,b,c) {

		$resp = JSON.parse(a.responseText);
		jQuery('.err').html($resp.msg);
		console.log($resp);
		console.log(b);
		console.log(c);

	});
}//

function GET_JS_v2($url, $func1)
{
	jQuery.get( $url ).done($func1)
	.fail(function(a,b,c) {

		$resp = JSON.parse(a.responseText);
		jQuery('.err').html($resp.msg);
		console.log($resp);
		console.log(b);
		console.log(c);

	});

}//


function post_data_01($cls_name)
{
	// $csrf  = jQuery('meta[name="csrf-token"]').attr('content');	
	$data1 = jQuery($cls_name).serializeArray();
	$data1.push({name:'_token', value: '<?php echo csrf_token(); ?>'});
	return $data1; 
}//

const delay2 = ms => new Promise(res => setTimeout(res, ms));

</script>

</head>
<body>

 <div  class="row main_row">
	 <div class="bgc1 main_left tc1">
		 
<?php 
$user1 = Auth::user()->roles()->first();
if($user1->name == 'admin'){ ?>
		<?php include_once('admin_left_menu1.php'); ?>
<?php }else{ ?>
		<?php include_once('left_menu1.php'); ?>
<?php } ?>
		 
	 </div>
