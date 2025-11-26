
<div class="over_due_cont1">
	
	<div class="over_due_form11">
			<input type="text"  autocomplete="off"  placeholder="Jan 26, 2019"  class="form-control"  id="overdue_date" />
			
			<select id="overdue_zone"   class="form-control" >
				<?php foreach($zone_label as $kk => $zl): ?>
				<option  value="<?php echo $kk; ?>"   <?php echo  $kk==@$zone?' selected ':''; ?>><?php echo $zl; ?></option>
				<?php endforeach; ?>
			</select>	
			
			<button  class="btn btn-sm"  onclick="add_over_due_step1()">Add </button>
			
	</div>
	
	<hr />
	<br />
	<br />

	<div  class="tt"  style="width:500px;">	
	<table class="table10 table-bordered  table-hover"><tbody>

				<tr class="headings">
					<td>Zone </td>
					<td>Period</td>
					<td>Date</td>
					<td>Status</td>
					<td>Actions</td>
				</tr>
				
				<?php 
				if(@$overdue){
				foreach($overdue as $ovv): ?>
				<tr>
					<td><?php echo get_zone101($ovv->zone_id); ?></td>
					<td><?php echo date('F Y', strtotime($ovv->period)); ?></td>
					<td><?php echo $ovv->date1; ?></td>
					<td><?php echo overdue_startus101( $ovv->status); ?></td>
					<td>
						<?php if($ovv->status == 0): ?>
							<button  class="btn btn-sm"   onclick="apply_overdue_now(<?php echo $ovv->id; ?>)">Apply Overdues</button>
						<?php endif; ?>
						<?php if($ovv->status == 2): ?>
							<button  class="btn btn-sm"   onclick="restart_overdue_now(<?php echo $ovv->id; ?>)">Restart Overdue</button>
						<?php endif; ?>
						
					</td>
				</tr>
				<?php endforeach;} ?>	
				
			</tbody></table>	
	</div>



</div>



<style>
.over_due_cont1{
	border:1px solid #ccc;
}	
.over_due_form11{
	padding:20px;
	width:500px;
}
</style>

<script>
	
function add_over_due_step1()
{
	let cc1 = confirm('Please Confirm.');
	if(!cc1){return;}	
	
	let o_date = jQuery('#overdue_date').val();
	let o_zone = jQuery('#overdue_zone').val();
	
	if(!o_date){alert('Date is required.');return;}
	if(!o_zone){alert('Zone is required.');return;}	
	
	window.location = '/billing/billing/overdue_add_job/'+o_zone+'/'+o_date;
}

function apply_overdue_now($job_id)
{
	let cc1 = confirm('Please Confirm.');
	if(!cc1){return;}	
	window.location = '/billing/billing/overdue_proccess_job/'+$job_id;
}
function restart_overdue_now($job_id)
{
	let cc1 = confirm('Please Confirm.');
	if(!cc1){return;}	
	window.location = '/billing/billing/overdue_proccess_job_restart/'+$job_id;
}

</script>
