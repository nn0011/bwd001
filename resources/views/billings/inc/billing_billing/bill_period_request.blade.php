<button  onclick="period_request_form()">Generate Billing Period</button>
<br />
<br />
<table class="table10 table-bordered  table-hover"><tbody>

	<tr class="headings">
		<td width="10%">Period : </td>
		<td width="30%">Period Description </td>
		<td width="5%">Date Requested</td>
		<td width="5%">Date Approved</td>
		<td width="5%">Request Status</td>
<!--
		<td width="5%">Print Status</td>
-->
		<?php  /*<td width="5%">Action</td> */ ?>
	</tr>

	<?php
			
			$index = 0;
			foreach($hw1_requests as $hwr):

					extract((array) json_decode($hwr['other_datas']));

	?>
		<tr>
			<td><?php echo date('F Y', strtotime($period_year.'-'.$period_month)); ?></td>
			<td><?php echo empty($hwr['remarks'])?"No description": $hwr['remarks']; ?></td>
			<td><?php echo  date('F d, Y', strtotime($hwr['updated_at']));?></td>
			<td><?php  if(!empty($hwr['date_stat'])){echo  date('F d, Y', strtotime($hwr['date_stat']));}else{echo '-----';} ?></td>
			<td><?php
					switch($hwr['status']){

						case 'approved':
							echo '<span  style="color:blue;">Approved</span>';
							echo '<br />';
							echo '<button  onclick="generate_billing22('.$hwr['id'].', \''.date('Y-m', strtotime($period_year.'-'.$period_month)).'\')">Proccess Now!</button>';
						break;

						case 'ongoing':
							echo '<span  style="color:blue;">Ongoing</span>';
						break;

						case 'canceled':
							echo '<span  style="color:red;">Canceled</span>';
						break;

						case 'completed':
							echo '<span  style="color:blue;">Completed</span>';
							echo '<br />';
							echo '<button  onclick="reProcess('.$hwr['id'].')">Reprocess</button>';
						break;

						default:
							echo '<span  class="rd">Pending</span>';
						break;
					}

				 ?></td>
<!--
			<td width="5%"><button  onclick="start_printing_service(<?php  echo $index;?>)">Start Printing  Service</button></td>
-->
			<?php /*<td width="5%"><button  onclick="">Edit</button></td> */ ?>
		</tr>
	<?php 
	
	$index++;
	
	endforeach; ?>



</tbody></table>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="generate_period_request_form" style="display:none;">
	<div style="padding:15px;">
	<form   action="/billing/billing/period_request/add" method="POST"  class="form-style-9"  onsubmit="">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
				<h2>Generate Period Request</h2>
				<br />

				<div class="name_fileds">
					<div  class="f1">
						<span class="sml">Period Month   </span>
						<select id="period_month"  name="period_month" class="form-control">
							<option value="1">Janaury</option>
							<option value="2">February</option>
							<option value="3">March</option>
							<option value="4">April</option>
							<option value="5">May</option>
							<option value="6">June</option>
							<option value="7">July</option>
							<option value="8">August</option>
							<option value="9">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
						</select>
					</div>

					<div  class="f1">
						<span class="sml">Period Year</span>
						<select id="period_year"  name="period_year" class="form-control">
							<?php
							$yy = date('Y');
							for($x=$yy;$x>=($yy - 10); $x--): ?>
							<option value="<?php echo  $x; ?>"><?php echo  $x; ?></option>
							<?php endfor; ?>
						</select>
					</div>




				</div>


				<span class="sml">Period  Description</span>
				<textarea   placeholder="Description"  name="dis_desc"    class="form-control" style="height:150px;"></textarea>

				<br />
				<br />

				<div style="text-align:center;">
					<button>Sent Request</button>
				</div>

				<div class="name_fileds">
				</div>

	</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<?php
foreach($hw1_requests as  $kk => $hwR)
{
	$hwR['date_read'] = date('F Y', strtotime($hwR['dkey1']));
	$hw1_requests[$kk] = $hwR;
}
?>


<script>
	
var billing_service = <?php echo json_encode($hw1_requests); ?>;

function period_request_form()
{
	trig1_v2('generate_period_request_form');
}

function generate_billing22($req_id, $periodx)
{
	$url1 = '/billing/billing/hwdjob/add1/'+$req_id+'/'+$periodx;
	window.location = $url1;
}
function reProcess($id)
{
	let res1 = confirm('You sure to reprocces this billing?');
	if(!res1){return;}
	$url1 = '/billing/billing/request/reprocess/'+$id;
	window.location = $url1;
}

function start_printing_service($ind)
{
	let res1 = confirm('You sure to start this billing print service '+billing_service[$ind].date_read+'?');
	if(!res1){return;}
	window.location = '/billing/billing/start_printing_service/'+billing_service[$ind].id;
}

</script>

<style>
.name_fileds  .f1{
    border: 0px solid #ccc;
    width: 45%;
    box-sizing: border-box;
    display: inline-block;
    margin-right: 15px;
}
</style>
