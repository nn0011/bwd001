<?php
if(@$print_serve[0]->status  == 'active'):
?>

	<div  class="print_form001">
		<?php

			//~ echo '<pre>';
			//~ print_r($print_serve->toArray());
			//~ echo '</pre>';
		?>
		<table class="mj001">
				<tr class="head001">
					<td style="width:10%;">Zone</td>
					<td style="width:10%;">Bill Start</td>
					<td style="width:10%;">Account Start</td>
					<td style="width:10%;">Period</td>
					<td style="width:10%;">Status</td>
					<td style="width:10%;text-align:center;">Action</td>
				</tr>

			<?php foreach($print_serve as $pps): ?>
			<tr>
				<td><?php  echo  $pps->zone_info->zone_name; ?> </td>
				<td><?php  echo  $pps->bill_start; ?> </td>
				<td><?php  echo  $pps->acct_start; ?> </td>
				<td><?php  echo  $pps->period; ?> </td>
				<td><?php  echo  $pps->status; ?> </td>
				<td style="text-align:center;">
					<button  onclick="stopNowService002(<?php  echo  $pps->id; ?>)">End Service</button>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>

	</div>


<?php endif; ?>


<?php
if(@$print_serve[0]->status  != 'active'):
?>

<div  class="print_form001">

	<input type="text"  class="date_discon1" value="<?php echo date('Y-m-d'); ?>" />

	<?php
	/*
		$date1 = date("Y-m");
	?>
	<select id="discon_period">
		<?php for($x=0;$x<=3;$x++):
			$ddxx = date('Y-m', strtotime($date1.'-01  -'.$x.' month '));
		?>
		<option value="<?php echo $ddxx; ?>"><?php echo date('F Y', strtotime($ddxx)); ?></option>
		<?php endfor;  ?>
	</select>
	*/ ?>
	<br />
	<br />

	<select id="discon_zone">
		<?php foreach($zone_label as $kk => $zl): ?>
		<option  value="<?php echo $kk; ?>"   <?php echo  $kk==@$zone?' selected ':''; ?>><?php echo $zl; ?></option>
		<?php endforeach; ?>
	</select>

	<br />
	<br />



	<small>Account id #</small><br />
	<input type="text"  placeholder="Account #"   value=""  autocomplete="off"  id="discon_acct"  />

	<br />
	<br />
	<button  onclick="list_all_discon_notice()">Disconnection Notice</button>
	<button  onclick="list_all_for_discon()">For Disconnection</button>
	<button  onclick="disconn_list001()">For Disconnection List</button>
	<select class="discon_lenth">
		<option value="1" selected>1 Month</option>
		<option value="2">2 Months</option>
		<option value="3">3 Months</option>
	</select>

</div>

<br />
<br />

<div  class="finder002">
</div>


<?php endif; ?>




<style>
.print_form001 {
}
.finder001 .head001 td{
	border:1px solid #ccc;
	padding:3px;
}
.mj001{
	margin-bottom:30px;
}
.mj001  td{
	font-size:12px;
	border:1px solid #ccc;
	padding:3px;
}
</style>

<script>

var print_url = '';

function list_all_discon_notice()
{
	//let discon_period = jQuery('#discon_period').val();
	let discon_date = jQuery('.date_discon1').val();
	let discon_zone = jQuery('#discon_zone').val();
	let discon_acct = jQuery('#discon_acct').val();

	if(!discon_acct){ discon_acct='none';}

	let url001 = '/disconnection_notice_list/v1/'+discon_date+'/'+discon_zone+'/'+discon_acct;

	print_url = url001;

	//alert(url001);
	//let url001 = '/disconnection_print_start/'+discon_zone+'/'+discon_period+'/'+discon_acct;
	//window.location =  url001;

	jQuery.get( url001, function( data ) {
	  jQuery( ".finder002" ).html( data );
		jQuery('.mm_disconn_date').datepicker({format:'YYYY-mm-dd', autoHide:true});

	});

}//

function print_notice_of_disconnection()
{
	let is_add = confirm('Are you sure to print notice of disconnection?');
	if(!is_add){return}

	//~ let url001 = '/disconnection_notice_list/v1/'+discon_date+'/'+discon_zone+'/'+discon_acct;
	//~ print_url

	let dis_date = jQuery('.mm_disconn_date').val();
	jQuery.get( 'http://127.0.0.1/hwd_print/disconn_print.php?url1='+print_url+'&dis_date='+dis_date, function( data ) {
		alert('Printing Started..');
	});


}//


function on_click_me1($me)
{

	let nnn = jQuery($me).is(":checked");
	if(nnn){
		jQuery($me).parent().parent().css({backgroundColor:'rgba(255,0,0,0.4)'});
	}else{
		jQuery($me).parent().parent().css({backgroundColor:'rgba(255,0,0,0)'});
	}
}

function make_for_disconnection()
{
	let is_add = confirm('Are you sure to mark as for disconnection.');
	if(!is_add){return}

	let vv = jQuery('.for_dis11:checked').length;
	if(vv <= 0){return;}

	let sere = jQuery('.for_dis11:checked').serialize();
	let url001 = '/make_for_disconnection_status/v1?'+sere;
	//window.location=url001;

	jQuery.get(url001, function( data ) {
		alert(data.msg);
		list_all_discon_notice();
	});
}//

function list_all_for_discon()
{
	let discon_date = jQuery('.date_discon1').val();
	let discon_zone = jQuery('#discon_zone').val();
	let discon_acct = jQuery('#discon_acct').val();

	if(!discon_acct){ discon_acct='none';}

	let url001 = '/for_disconnection_list/v1/'+discon_date+'/'+discon_zone+'/'+discon_acct;

	jQuery.get( url001, function( data ) {
	  jQuery( ".finder002" ).html( data );
	});

}//


function disconnect_line1001()
{
	let is_add = confirm('Are you sure to Disconnect this accounts?');
	if(!is_add){return}

	let vv = jQuery('.for_dis11:checked').length;
	if(vv <= 0){return;}

	let sere = jQuery('.for_dis11:checked').serialize();
	let url001 = '/for_disconnection_status_disconnect/v1?'+sere;
	//window.location=url001;

	jQuery.get(url001, function( data ) {
		alert(data.msg);
		list_all_for_discon();
	});
}//


function disconn_list001()
{
	let discon_date = jQuery('.date_discon1').val();
	let discon_zone = jQuery('#discon_zone').val();
	let discon_acct = jQuery('#discon_acct').val();
	let mon_len = jQuery('.discon_lenth').val();

	if(!discon_acct){ discon_acct='none';}

	let url001 = '/for_disconnection_list/v2/'+discon_date+'/'+discon_zone+'/'+discon_acct+'?mo='+mon_len;
	 window.open(url001, '_blank');

}


jQuery(document).ready(function(){
	jQuery('.date_discon1').datepicker({format:'YYYY-mm-dd', autoHide:true});
});
</script>
