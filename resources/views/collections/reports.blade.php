<?php

$all_zones = get_zone102();

//~ die();

$cash_url = '/hwd1/';?>

@extends('layouts.cashier')

@section('content')

<div style="clear:both;"></div>
<div class="white_box1">
	<br />
	<br />
	<br />
	<div style="clear:both;"></div>

	<div style="clear:both;"></div>

		<div class="date_find">
			<?php

				$dat1 = date('Y-m-d');
				if(!empty(@$_GET['dd'])){
					$dat1 = date('Y-m-d', strtotime(@$_GET['dd']));
				}

			?>

			<input type="text"  value="<?php echo $dat1; ?>"  class="date_find111"  onchange="change_date_find()" />

		</div>

	<div style="clear:both;"></div>
	<br />

	<div class="tabs001">

		<div class="mm">
			<ul>
<!--
				<li><a href="/collections/reports/daily?dd=<?php echo $dat1;?>"   target="_blank">Daily Collection Reports</a></li>
-->
				<li><a href="/collections/reports/daily?dd=<?php echo $dat1;?>&sum=1"   target="_blank">Daily Collection Summary</a></li>
				<li>
					<a target="_blank" onclick="Goto_daily_per_zone()">Daily Collection Reports By Zone</a>
					&nbsp;&nbsp;&nbsp;
					<select  class="nn_zone_id">
						<option value="">ALL</option>
						<?php  foreach($all_zones as $az1): ?>
						<option value="<?php echo $az1->id; ?>"><?php echo strtoupper($az1->zone_name); ?></option>
						<?php endforeach; ?>
					</select>
				</li>
				<?php
				/*<li><a href="/collections/reports/for_disconnection?dd=<?php echo $dat1;?>"   target="_blank">Daily Collection Reports</a></li>*/
				?>
<!--
				<li><a href="/collections/reports/summary/daily?dd=<?php echo $dat1;?>"   target="_blank">Daily Collection Summary Reports</a></li>
-->
<!--
				<li><a href="/collections/reports/montly?dd=<?php echo $dat1;?>"   target="_blank">Monthly Collection Reports</a></li>
-->
<!--
				<li><a href="/collections/reports/summary/montly?dd=<?php echo $dat1;?>"   target="_blank">Monthly Collection Summary Reports</a></li>
-->
				<li><a  onclick="generate_monthly_report()">Execute Summary Report</a></li>
<!--
				<li><a href="/collections/reports/summary/annually?dd=<?php echo $dat1;?>"   target="_blank">Annual Collection Summary Reports</a></li>
-->
				<li><a onclick="disconnect_step1()">For Disconnecion</a></li>
			</ul>
		</div>


<!--
		<table class="table10 table-bordered  table-hover"><tbody>

			<tr class="headings">
				<td width="10%" style="text-align:left;">Date :</td>
				<td width="10%"  style="text-align:left;">Invoice No.</td>
				<td width="50%"  style="text-align:left;">Name</td>
				<td width="5%"  style="text-align:right;">Amount</td>
			</tr>

			<?php
			/*
			 foreach($colls as $cc): ?>
		     <tr onclick="">
				<td style="text-align:left;"><?php echo date('M d, Y @ H:i A',strtotime($cc->payment_date)); ?></td>
				<td style="text-align:left;"><?php echo $cc->invoice_num; ?></td>
				<td style="text-align:left;"><?php echo $cc->billing->account->lname.', '.$cc->billing->account->fname.' '.$cc->billing->account->mi; ?></td>
				<td style="text-align:right;"><?php echo number_format($cc->payment, 2); ?></td>
			</tr>
			<?php endforeach;
			*/
			?>

		</tbody></table>
-->


	</div>

</div>


<div class="disconnection" style="display:none;">
	<div class="disconnection_pop">
		<h2>Disconnection</h2>

		<select class="period11 form-control">
			<option value="1">1 MONTH</option>
			<option value="2">2 MONTH</option>
			<option value="3">3 MONTH</option>
		</select>
		<br />
		<br />


		<select class="zone11 form-control">
			<?php foreach($zones as $zz):?>
			<option value="<?php echo $zz->id?>"><?php echo $zz->zone_name?></option>
			<?php endforeach; ?>
		</select>
		<br />
		<br />

		<button type="button" class="btn btn-secondary btn-sm" onclick="disconnect_step2()">Submit</button>


	</div>
</div>


<div class="generate_monthly_report" style="display:none;">
	<div class="generate_monthly_report_pop padd40">
		<h3>Monthly Report</h3>	
		<small>Select Month</small>
		<br />
		<select class="mm_rep"  onchange="check_report_99()">
			<?php 
				$date1 = date('Y-m-01');
				$m = 0;
				for($m;$m<=10;$m++){
					$tt = strtotime($date1.' - '.$m.'MONTH');
			?>
			<option value="<?php echo date('Y-m-01', $tt); ?>"><?php echo date('F Y', $tt); ?></option>
			<?php } ?>
		</select>
		<br />
		<br />
		<br />
		<button type="button" class="btn btn-secondary btn-sm" onclick="generate_report_99()">GENERATE REPORT</button>
		<br />
		<br />
		<button type="button" class="btn btn-primary btn-sm" onclick="view_monthly_report_99()">VIEW REPORT</button>
		<br />
		<br />
		<br />
		{{-- <p style="font-size:18px;">
			<span class="tcr"></span> / <span class="tcc"></span>
		</p> --}}
		<br />
		<br />
		<br />

		<button type="button" class="btn btn-danger btn-sm" onclick="reset_reexecute()" style="background: red;">Reset and re-execute</button>
	</div>
</div>



<style>
.disconnection_pop{
	padding:30px;
}
.padd40{
	padding:40px;
}
</style>




@endsection


@section('inv_include')


@include('billings.inc.php_mod.pop1')
<link rel="stylesheet" href="/css/collection/collection1.css">
<script src="/js/collection/collection.js"></script>

<link rel="stylesheet" href="/css/collection/col3.css">
<script src="/css/collection/col3.js"></script>



<script>
//href="/collections/reports/for_disconnection"

async function check_report_99()
{
	return;
	return;
	return;
	let dd1 = jQuery('.pop101 .mm_rep').val();
	await jQuery.get('/generate_montly_report_101_check/'+dd1, function($res1){
			
			jQuery('.pop101 .tcr').html($res1.tcr);
			jQuery('.pop101 .tcc').html($res1.tcc);
			
		}).promise();	
}//

async function generate_report_99()
{
	let conf11 = confirm('Are you sure to generate report?');
	if(!conf11){return;}

	let dd1 = jQuery('.pop101 .mm_rep').val();
	window.open('/generate_montly_report_102_check/'+dd1, 'blank');


	return;
	return;
	return;
	return;


	
	// let dd1 = jQuery('.pop101 .mm_rep').val();
	
	
	let good1=1;
	let msg1 = '';
	for (i = 0; i < 100; i++) {
		
		await jQuery.get('/generate_montly_report_101/'+dd1, function($res1){
				
				if($res1.status == 0){
					good1 = 0;
				}

				jQuery('.pop101 .tcr').html($res1.tcr);
				jQuery('.pop101 .tcc').html($res1.tcc);
				
				msg1 = $res1.msg;
				
			}).promise();
		
		if(good1 <= 0){
			break;
		}
	}//for
	
	alert(msg1);
	
	
}//

function view_monthly_report_99()
{
	let dd1 = jQuery('.pop101 .mm_rep').val();
	window.open('/collections/reports/summary/montly?dd='+dd1, '_blank');
}

function generate_monthly_report()
{
	trig1_v2('generate_monthly_report');
	setTimeout(function(){
			check_report_99();
		},500);
}

function disconnect_step1()
{
	trig1_v2('disconnection');
}
function disconnect_step2()
{
	let z1 = jQuery('.pop101 .zone11').val();
	let p1 = jQuery('.pop101 .period11').val();
	// window.open('/collections/reports/for_disconnection/'+z1+'/'+p1, '_blank');
	window.open('/for_disconnection_list/v2/<?php echo date('Y-m-d'); ?>/'+z1+'/none?mo='+p1, '_blank');

}

function Goto_daily_per_zone()
{
	$zone_id = jQuery('.nn_zone_id').val();
	window.open('/collections/reports/daily?dd=<?php echo $dat1;?>&zz='+$zone_id,'_blank');
}


function reset_reexecute() 
{
	let conf11 = confirm('Are you sure to reset and re-execute report?');
	if(!conf11){return;}
	
	let dd1 = jQuery('.pop101 .mm_rep').val();
	window.open('/generate_montly_report_102_reset/'+dd1, 'blank');
}

</script>


@endsection
