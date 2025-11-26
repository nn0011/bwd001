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
	
	<?php
		$date1 = date("Y-m");
	?>
	<select id="search_period_all">
		<?php for($x=0;$x<=3;$x++): 
			$ddxx = date('Y-m', strtotime($date1.'-01  -'.$x.' month '));
		?>
		<option value="<?php echo $ddxx; ?>"><?php echo date('F Y', strtotime($ddxx)); ?></option>
		<?php endfor;  ?>
	</select>
	
	<br />
	<br />
	
	<select id="search_zone_for_printing">
		<?php foreach($zone_label as $kk => $zl): ?>
		<option  value="<?php echo $kk; ?>"   <?php echo  $kk==@$zone?' selected ':''; ?>><?php echo $zl; ?></option>
		<?php endforeach; ?>
		<option value="all">All</option>
	</select>	

	<br />
	<br />
	
	<small>Bill # Start</small><br />
	<input type="text"  placeholder="Bill  #"  class="bill"  value="" autocomplete="off"  id="for_print_start_bill"  />

	<br />
	<br />

	<small>Bill # End</small><br />
	<input type="text"  placeholder="Bill  # End"  class="bill_end"  value="" autocomplete="off"  id="for_print_start_bill_end"  />

	<br />
	<br />
	
	<small>Account id #</small><br />
	<input type="text"  placeholder="Account #"   value=""  autocomplete="off"  id="for_print_start_acct"  />
	
	<br />
	<br />
	<button  onclick="findForPrint101()">Find</button>
	
</div>

<br />
<br />

<div  class="finder001">
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
function findForPrint101()
{
		let zz = jQuery('#search_zone_for_printing').val();
		let bb_A = jQuery('#for_print_start_acct').val();
		let bb_N = jQuery('#for_print_start_bill').val();
		let bb_N_E = jQuery('#for_print_start_bill_end').val();
		let bb_period = jQuery('#search_period_all').val();
		
		if(!bb_A){ bb_A='none'; }
		if(!bb_N){ alert('Failed to start.'); return; }
		if(!bb_period){ alert('Failed to start.'); return; }
		if(!bb_N_E){bb_N_E = 'none';}
		
		let url001 = '/bill_print_start/'+bb_period+'/'+zz+'/'+bb_A+'/'+bb_N+'/'+bb_N_E;
		
		//~ alert('http://192.168.0.17:8585'+url001);
		
		jQuery.get( url001, function( data ) {
		  jQuery( ".finder001" ).html( data );
		  alert('Billing Retrived ');
		});		
}

function start_printing221()
{
		let mm = confirm('Are you sure to print ?');
		if(!mm){return;}
		
		let zz = jQuery('#search_zone_for_printing').val();
		let bb_A = jQuery('#for_print_start_acct').val();
		let bb_N = jQuery('#for_print_start_bill').val();
		let bb_N_E = jQuery('#for_print_start_bill_end').val();
		let bb_period = jQuery('#search_period_all').val();
		
		if(!bb_A){ bb_A='none';}
		if(!bb_N){ alert('Failed to start.'); return;}
		if(!bb_period){ alert('Failed to start.'); return;}	
		if(!bb_N_E){bb_N_E = 'none';}
		
		
		let url001 = '/bill_print_start_002/'+bb_period+'/'+zz+'/'+bb_A+'/'+bb_N+'/'+bb_N_E;
		//~ alert('http://192.168.0.17:8585'+url001);
		
		//~ $urlxxx = 'http://localhost/billing_print22.php?url1='+url001;
		//$urlxxx = 'http://localhost/hwd_print/print2.php?url1='+url001;
		$urlxxx = '/before_printing_save_first/'+bb_period+'/'+zz+'/'+bb_A+'/'+bb_N+'/'+bb_N_E;
		
		
		window.open($urlxxx, '_blank');
		window.focus();


		//~ jQuery.get( $urlxxx, function( data ) {
			//~ alert('Printing');
		//~ });			
		
}

function stopNowService002($idd)
{
		let url001 = '/bill_print_stop_service/'+$idd;
		window.location = url001;
}

function save_billing_number001()
{
	let ff = confirm('Are you sure to save billing number?');
	if(!ff){return;}
	

	let zz = jQuery('#search_zone_for_printing').val();
	let bb_A = jQuery('#for_print_start_acct').val();
	let bb_N = jQuery('#for_print_start_bill').val();
	let bb_N_E = jQuery('#for_print_start_bill_end').val();
	let bb_period = jQuery('#search_period_all').val();
	
	if(!bb_A){ bb_A='none';}
	if(!bb_N){ alert('Failed to start.'); return;}
	if(!bb_period){ alert('Failed to start.'); return;}	
	if(!bb_N_E){bb_N_E = 'none';}
	
	let url001 = '/bill_print_save_billing_number/'+bb_period+'/'+zz+'/'+bb_A+'/'+bb_N+'/'+bb_N_E;

	jQuery.get( url001, function( data ) {
		alert('Done Save');
		findForPrint101();		
	});			
	//~ window.open(url001,'_blank');
}//


function RESET_BILLING_NUMBER()
{
	let ff = confirm('WARNING THIS WILL CHANGE YOUR BILLING REFFERENCE NUMBER');
	if(!ff){return;}	
	
	ff = confirm('ARE YOU SURE?');
	if(!ff){return;}
	
	
	let zz = jQuery('#search_zone_for_printing').val();
	let bb_A = jQuery('#for_print_start_acct').val();
	let bb_N = jQuery('#for_print_start_bill').val();
	let bb_N_E = jQuery('#for_print_start_bill_end').val();
	let bb_period = jQuery('#search_period_all').val();
	
	if(!bb_A){ bb_A='none';}
	if(!bb_N){ alert('Failed to start.'); return;}
	if(!bb_period){ alert('Failed to start.'); return;}	
	if(!bb_N_E){bb_N_E = 'none';}
	
	
	let url001 = '/reset_billing_number_by_zone/'+bb_period+'/'+zz+'/'+bb_A+'/'+bb_N+'/'+bb_N_E;

	jQuery.get( url001, function( data ) {
		alert('Done Reset');
		findForPrint101();		
	});			
	
}

</script>
