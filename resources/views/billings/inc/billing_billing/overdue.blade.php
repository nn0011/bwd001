<?php 

$reading_period = get_reading_period();

?>



<table class="table10 table-bordered  table-hover"   style="width:600px;">
	
		<tbody><tr class="headings">
			<td width="30%">Period</td>
			<td width="10%">Action</td>
		</tr>
		
		<?php  
		
		$curr_per = date('Y-m');
		
		$xx = 0;
		foreach($reading_period as $rpp): 
		
				$per1 = date('Y-m', strtotime($rpp->period));
		
		?> 
		<!------>
		<!------>
		<tr onclick="">
			<td><?php echo date('F Y', strtotime($rpp->period));?></td>
			<td><a onclick="apply_penalty_for_billing(<?php echo $xx; ?>)">Apply Penalty</a></td>
		</tr>
		<!------>
		<!------>
		<?php $xx++; endforeach; ?>
				
</tbody></table>

<div class="apply_penalty_for_billing_pop" style="display:none;">
	<div  class="apply_penalty_for_billing_cont" style="padding:40px;">
		
		<div class="apply_penalty_for_billing_result">
			Please Wait...
		</div>
		
		<div class="ww1" style="display:none;">
			<div class="white txtcenter please_wait">
				<span class="count">0</span> / <span class="ttl_count">0</span>
				<br />
				Please Wait..
			</div>
		</div>
		
	</div>
</div>




<script>
var pen_period = <?php echo json_encode($reading_period); ?>;
var pen_period_curr = null;
var pen_period_index = null;

function apply_penalty_for_billing($index)
{
	pen_period_index = $index;
	pen_period_curr  = pen_period[$index];
	
	trig1_v2('apply_penalty_for_billing_pop');
	get_apply_penalty_for_billing_zone_list();
}

async function get_apply_penalty_for_billing_zone_list()
{
	
	let url1 = '/penalty0001/get_apply_penalty_for_billing_zone_list/'+pen_period_curr.period;
	await jQuery.get(url1, function($res){
		jQuery('.pop101 .apply_penalty_for_billing_result').html($res.html);
	}).promise();
	
}//

async function execute_penalty_now($pen_date, $zone_id, $period)
{
	
	
	
	//~ alert($pen_date);
	//~ alert($zone_id);
	//~ alert($period);
	//~ console.log(pen_period_curr);
	///penalty0001/execute_penalty_by_zone_id_pen_date/2019-06-17/1/2019-06-01
	
	jQuery('.pop101 .ww1').show();
	
	//~ return false;
	//~ return false;
	//~ return false;
	//~ return false;
	
	let url1 = '/penalty0001/execute_penalty_by_zone_id_pen_date/'+$pen_date+'/'+$zone_id+'/'+$period;
	
	try{
		await jQuery.get(url1, function($res){
			
			if($res.status == 0){
				alert($res.msg);
				jQuery('.pop101 .ww1').hide();
				get_apply_penalty_for_billing_zone_list();				
				return  false;
			}
			
			jQuery('.pop101 .ttl_count').html($res.ttp);
			jQuery('.pop101 .count').html($res.rr);
			
			setTimeout(function(){
				execute_penalty_now($pen_date, $zone_id, $period);
			}, 300);
			//~ alert('TEST');
			
		}).promise();	
	}catch(e){
			alert('ERROR CODE 101099');
			jQuery('.pop101 .ww1').hide();
	}
	
}//

</script>

<style>
.back_1 .box1{
	margin-top:50px !important	;
}
.table10.hh td{
	border:1px solid #ccc;
}


.white{color:white;}
.txtcenter{text-align:center;}
.please_wait{
	padding-top:25%;
	font-size:21px;
}	

.please_wait .count{}


.ww1{
    position: absolute;
    top: 0;
    background: rgba(0,0,0,0.7);
    left: 0;
    z-index: 999999;
    width: 100%;
    height: 100%;	
    display:none;
}
</style>
