


<table class="table10 table-bordered  table-hover"   style="width:600px;">
	
		<tbody><tr class="headings">
			<td width="30%">Period</td>
<!--
			<td width="30%">Total Billable</td>
-->
			<td width="10%">Action</td>
		</tr>
		
		<?php  
		
		$curr_per = date('Y-m');
		
		$xx = 0;
		foreach($read_period as $rpp): 
		
				$per1 = date('Y-m', strtotime($rpp->period));
		
		?> 
		<!------>
		<!------>
		<tr onclick="">
			<td><?php echo date('F Y', strtotime($rpp->period));?></td>
<!--
			<td><?php //echo number_format((int) @$rpp->read1->ttl_active); ?></td>
-->
			<td><a onclick="for_billing_execute1(<?php echo $xx; ?>)">Start Billing</a></td>
		</tr>
		<!------>
		<!------>
		<?php $xx++; endforeach; ?>
				
</tbody></table>



<div class="reading_for_billing_pop" style="display:none;">
	<div  class="reading_for_billing_cont" style="padding:40px;">
		
		<div class="result001">
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
jQuery(document).ready(function(){
	jQuery('[data-toggle="datepicker"]').datepicker();
	
});	
</script>


<script>
var for_bill_list = <?php echo json_encode($read_period); ?>;
var fbe = null;
var cur_index = null;


function start_billing_101($zone_id)
{
	
	let penalty_date = jQuery('.pop101 .dd_'+$zone_id).val();
	if(!penalty_date){
		alert('Invalid date. code 1');
		return;
	}
	
	let is_date_valid = Date.parse(penalty_date);
	if(!is_date_valid){
		alert('Invalid date. code 2');
		return;
	}
	
	let  sure1 = confirm('Please confirm.');
	if(!sure1){return;}
	
	jQuery('.pop101 .reading_for_billing_cont .ww1').show();
	$period = fbe.period;
	
	
	let url11 = '/billing/reading_period_start/execute_billing_by_20/'+$period+'/'+$zone_id+'/'+penalty_date;
	billing_proces_execute(url11);
}//

async function  billing_proces_execute(url11)
{
	try{
		
		await jQuery.get(url11, function($res){
				
				if($res.constructor != Object){
					jQuery('.pop101 .reading_for_billing_cont .ww1').hide();
					alert('Error result..');
					return;
				}

				
			
				jQuery('.pop101 .reading_for_billing_cont .ww1 .ttl_count').html($res.billable);			
				jQuery('.pop101 .reading_for_billing_cont .ww1 .count').html($res.billed);			
					
				if($res.stat == 1){
					alert('Billing Complete');
					//jQuery('.pop101 .reading_for_billing_cont .ww1').hide();
					for_billing_execute1(cur_index);
					return;
				}
				
				setTimeout(function(){
					billing_proces_execute(url11);
				},500);
				
			
			}).promise();	
		
	}catch(e){
		alert('Error 2003');
		jQuery('.pop101 .reading_for_billing_cont .ww1').hide();
	}
	
}//



function for_billing_execute1($i)
{
	cur_index = $i;
	fbe =  for_bill_list[$i];
	trig1_v2('reading_for_billing_pop');
	get_111()
	
}//


async function get_111($period)
{
	try{
		
		$period = fbe.period;
		
		let url11 = '/billing/reading_period_start/start_for_billing_execute/'+$period;
		
		await jQuery.get(url11, function($res){
					jQuery('.pop101 .reading_for_billing_cont .result001').html($res.html);
					setTimeout(function(){
						jQuery('.datepick_77').datepicker({format: 'yyyy-mm-dd', autoHide:true});
					},300);
			}).promise();	
			
	}catch(e){
		alert('Error 2001');
	}		
}
	
</script>
<style>
.datepicker-container{
	z-index:99999 !important;
}
.datepick_77{
	padding:5px;
    width: 85px;
}
</style>
