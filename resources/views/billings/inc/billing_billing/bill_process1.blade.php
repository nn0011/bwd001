<?php
    //~ $vvv = bill_request_list();

	$bill_req_html = bill_request_HTML();
	
	foreach($bill_req_html as $kk=>$vv){
		$vv->period_str = date('F Y', strtotime($vv->dkey1)); 
		$vv->zone_str = 'Zone '.$vv->bill_zone->zone_id;
		$vv->billing_date = date('Y-m-', strtotime($vv->dkey1)); 
	}
	
	//~ echo '<pre>';
	//~ print_r($bill_req_html->toArray());
	//~ die();
?>

<select onchange="change_zonebill()"  id="tab11_zonebill_select">
	<option value="0">Select..</option>
	<?php 
	$xx=0;
	foreach($bill_req_html as $v): ?>
	<option value="<?php echo $xx; ?>"><?php echo cc_date('F Y', $v->dkey1); ?></option>
	<?php $xx++; endforeach; ?>
</select>
<br />
<br />


<div style="max-width:1000px;"  id="tab11_zonebill">
	

	<?php 
	
	/*
	<table class="table10 table-bordered  table-hover"><tbody>

		<tr class="headings">
			<td>Period : </td>
			<td>Zone : </td>
			<td>Status</td>
			<td>Actions</td>
		</tr>

	<?php if(@$zonebilling) {foreach(@$zonebilling as $zz1): ?>
		<tr>
			<td><?php echo  date('F Y', strtotime($curr_period)); ?></td>
			<td><?php echo  $zz1->zone_name; ?></td>
			<td><?php 
					if(!$zz1->billzone){echo 'UNBILLED';}
					else{echo $zz1->billzone->status;}
			?></td>
			<td><?php 
					if(!$zz1->billzone){ echo '<button  onclick="start_process_bill('.$zz1->id.')">Start Proccess</button>';}
					else{echo '<button   onclick="start_process_bill('.$zz1->id.')">Re-Proccess</button>';}
			?> </td>
		</tr>
	<?php endforeach;}else{ echo '<td  colspan="3"  style="text-align:center;pading:25px;">No Billing </td>';} ?>	
	</tbody></table>
	*/ 
	
	?>
	
	
</div>


<div class="bill_process_step_pop" style="display:none;">
	<div style="padding:15px;" class="bill_process_step_cont">
		<h4>Please Choose Penalty Date</h4>
		<br />
		<br />
		<b>Period : </b> <span class="bill_pro_period"></span>
		<br	
		<b>Zone : </b> <span class="bill_pro_zone"></span>
		<br />
		<b>Bill Date : </b> <span class="bill_pro_bill_date"></span>
		<br />
		<br />
		<small>Due Date</small><br />
		<input type="text" class="datefine"  />
		<button onclick="start_process_bill_step2()">Save</button>
	</div>
</div>





<script>
var data11 = <?php echo json_encode($bill_req_html); ?>;

function change_zonebill()
{
	$ind = jQuery('#tab11_zonebill_select').val();
	jQuery('#tab11_zonebill').html(data11[$ind].html1);
}
</script>



<script>
var cur_zone = 0;
var cur_period ='';
function start_process_bill($zone_id, $dat11)
{
	cur_zone = $zone_id;
	
	//start_process_bill_step2($zone_id);
	//bill_process_step_pop
	trig1_v2('bill_process_step_pop');
	
	setTimeout(function(){
		console.log(data11[$ind]);
		console.log(data11[$ind].bill_zone.pen_date);
		//~ alert($zone_id);
		jQuery('.pop101 .datefine').datepicker({format: 'yyyy-mm-dd'});
		jQuery('.pop101 .bill_pro_zone').html($zone_id);
		jQuery('.pop101 .bill_pro_period').html(data11[$ind].period_str);
		jQuery('.pop101 .bill_pro_bill_date').html(data11[$ind].billing_date+$zone_id);
		cur_period = data11[$ind].billing_date+'01';
		setTimeout(function(){
			if($dat11 == '---'){return;}
			 jQuery('.pop101 .datefine').val($dat11);
			},50);
	}, 200);
}

function  start_process_bill_step2()
{
	let cc1 = confirm('Confirm to start..');
	if(!cc1){return;}
	
	$ind = jQuery('#tab11_zonebill_select').val();
	let sel_period = data11[$ind].id;
	
	let fine_date  =  jQuery('.pop101 .datefine').val();
	
	//~ alert('/billing/billing/zone_bill_start/'+$zone_id+'/'+sel_period);
	window.location = '/billing/billing/zone_bill_start/'+cur_zone+'/'+sel_period+'?fine_date='+fine_date+'&period='+cur_period;
}	
</script>

<style>
.datepicker-container{
z-index:99999 !important; 
}	
.datefine{padding:5px;}
</style>
