<button onclick="add_new_reading_period()">Add Reading Period</button>

<div style="padding-top:50px;">

<table class="table10 table-bordered  table-hover"   style="width:600px;">
	
		<tbody><tr class="headings">
			<td width="50%">Period</td>
			<td width="20%">Status</td>
			<td width="20%">Count</td>
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
			<td><?php echo date('F Y', strtotime($rpp->period)); ?>  <?php if($curr_per == $per1) {?> &nbsp;&nbsp;&nbsp;<strong style="color:red;">Current period</strong> <?php } ?></td>
			<td><?php echo $rpp->status; ?> </td>
			<td><?php echo number_format((int) @$rpp->read1->ttl_active); ?> / <?php echo $acct_all; ?></td>
			<td><a onclick="re_initial_reading(<?php echo $xx; ?>)">Initialize</a></td>
		</tr>
		<!------>
		<!------>
		<?php $xx++; endforeach; ?>
				
</tbody></table>

	
	
</div>


<div class="add_edit_period" style="display:none;">
	<div style="padding:20px;">
		
		<form action="/billing/reading/add_reading_period" method="POST" class="form-style-9  xx_11">

			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden" name="period_id" value="0"  id="period_id">
			
			<?php 
			
			$curr_y = date('Y')+1;
			$curr_y2 = date('Y');
			$curr_m = (int) date('m');
				
			?>
			<div style="padding:15px;">
					<h2>Add New Period</h2>
				
					<select class="form-control"  name="period_year" id="period_year">
						<?php for($x=$curr_y; $x>=($curr_y - 3);$x--): ?>
						<option   <?php echo $curr_y2==$x?' selected ':'' ?>><?php echo $x; ?></option>
						<?php  endfor; ?>
					</select>
					
					<select id="period_month"  class="form-control"  name="period_month">
						<?php for($x=1;$x<=12;$x++): ?>
						<option value="<?php echo $x; ?>"  <?php echo $curr_m==$x?' selected ':''; ?>><?php echo date('F', strtotime('2018-'.$x.'-1')); ?></option>
						<?php endfor; ?>
					</select>
					
					<select id="period_status"  class="form-control"  name="period_status">
						<option value="active">active</option>
						<option value="inactive">inactive</option>
					</select>
			
			</div>


			<div style="text-align:center;">
				<button>Save</button>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<button  onclick="pop_close()"  type="button">Cancel</button>
			</div>
			
		</form>
	</div>

</div>



<div class="initialize_reading_pop" style="display:none;">
	<div  class="initialize_reading_cont" style="padding:40px;">
		
		<div class="result001">
			Please Wait...
		</div>
		
		
		 <?php /* foreach(@$zones as $zz):?>
			<option value="<?php echo $zz['id']; ?>"   <?php echo  $zz['id']==@$zone?' selected ':''; ?>><?php echo strtoupper($zz['zone_name']); ?></option>
		 <?php endforeach; */ ?>
		
		
		<div class="ww1" style="display:none;">
			<div class="white txtcenter please_wait">
				<span class="count">0</span> / <span class="ttl_count">0</span>
				<br />
				Please Wait..
			</div>
		</div>
		
	</div>
</div>


<style>
.white{color:white;}
.txtcenter{text-align:center;}
.please_wait{
	padding-top:25%;
	font-size:21px;
}	

.please_wait .count{}
	
.back_1 .box1{margin-top: 30px !important;}
.tab_read_period1{width:100%;}

.tab_read_period1 td{
	border:1px solid #ccc;
	padding:5px;
	
}
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
.dd1_date{
	padding:5px;
}
.pop101 .box1.w700{
	    min-width: 700px !important;
}
</style>

<script>

var cur1 = null;	
var loop_me = 0;
	
function initialize_reading_period111($zone_id)
{
	
	let  sure1 = confirm('Please confirm your action');
	if(!sure1){return;}
	
	let period = cur1.period;
	let zone_id = $zone_id;
	let read_d = jQuery('.pop101 .read_z_'+$zone_id).val();
	let due_d = jQuery('.pop101 .due_z_'+$zone_id).val();
	let fin_d = jQuery('.pop101 .pen_z_'+$zone_id).val();
	let read_per_id = jQuery('.pop101 .read_period11').val(); 
	
	if(!fin_d){
		alert('Please assign penalty date');
		return;
	}

	if(!due_d){
		alert('Please assign due date');
		return;
	}
	
	if(!read_d){
		alert('Please assign Reading Schedule');
		return;
	}	
	
	let url11 = '/billing/reading_period_start/initilize_start_v2/'+cur1.period+'/'+zone_id+'/'+due_d+'/'+read_per_id+'/'+fin_d+'/'+read_d;
	//~ window.location = url11;
	
	jQuery('.pop101 .ww1').show();
	
	loop_me11(url11);
	
}//

async function loop_me11(url11)
{
	try{
		await jQuery.get(url11, function($res){
			
				  jQuery('.pop101 .ww1 .count').html($res.count1);
				  jQuery('.pop101 .ww1 .ttl_count').html($res.total_count);

				  //~ re_initial_reading_2();
				
				if($res.stat==1)
				{
					
				  re_initial_reading_2();
				  alert('Done.');
				  jQuery('.pop101 .ww1').hide();
				  
				}else{
					
				  //~ jQuery('.pop101 .ww1 .count').html($res.count1);
				  //~ jQuery('.pop101 .ww1 .ttl_count').html($res.total_count);
				  setTimeout(function(){
						loop_me11(url11);
					}, 500);
				}
			
			}).promise();	
	}catch(e){
		alert('Error 2000');
	}	
}//





function add_new_reading_period()
{
	trig1_v2('add_edit_period');
}

async function re_initial_reading_2()
{
	trig1_v2('initialize_reading_pop');
	
	try{
		
		
		jQuery('.back_1 .box1').addClass('w700');
		
		let period = cur1.period;
		
		let url11 = '/billing/reading_period_start/initilize_get_zones_counts/'+cur1.period;
		
		 await jQuery.get(url11, function($res){
				
				jQuery('.pop101 .result001').html($res.html);
				
				setTimeout(function(){
						dd1_date_pic1();				
					},300);
			
			}).promise();	
	
	}catch(e){
		alert('Error 2000');
	}	

}

function re_initial_reading($iin)
{
	cur1 = read_period[$iin];
	re_initial_reading_2($iin);
	
	return;
	//~ return;
	/*
	let  sure1 = confirm('Are you sure to run initilize?');
	if(!sure1){return;}
	let cur_per = read_period[$iin];
	window.location = '/billing/reading_period_start/initilize_start/'+cur_per.period;
	*/
	//cur_per.period
}//

var	read_period = <?php echo json_encode($read_period->toArray()); ?>;

</script>


<script>
function dd1_date_pic1()
{
	jQuery('.pop101 .dd1_date').datepicker({format: 'yyyy-mm-dd', autoHide:true});
}	
</script>

<style>
.pop101 .box1.w700 {
    min-width: 820px !important;
}	
</style>
