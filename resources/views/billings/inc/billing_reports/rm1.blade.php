<h1>Report Management</h1>

<div class="zone_mm" style="text-align:right;">
     <button onclick="generate_new_reports()">Generate New Report</button>
</div>

<br />

<table class="table10 table-bordered  table-hover"><tbody>

     <tr class="headings">
          <td width="5%">Period</td>
     	<td width="5%">Descrition</td>
          <td width="5%">Status</td>
          <td width="5%">Action</td>
     </tr>

     <?php foreach($report_list as $rep1): ?>
     <tr>
          <td><?php echo $rep1->remarks; ?></td>
          <td>
               <?php
                    $other_datas = json_decode($rep1->other_datas);
                    echo $other_datas->dis_desc;
               ?>
          </td>
          <td><?php echo $rep1->status; ?></td>
          <td width="5%">
               <?php if($rep1->status == 'pending'): ?>
                    <a href="/billing/reports/generate/<?php echo $rep1->id; ?>">Start Service</a>
               <?php elseif($rep1->status == 'completed'): ?>
                    <a href="/billing/reports/regenerate/<?php echo $rep1->id; ?>">Restart Service</a>
               <?php endif; ?>

          </td>
     </tr>
     <?php endforeach; ?>

</tbody></table>




<!--------------------------------- --->
<!--------------------------------- --->
<div class="generate_period_request_form" style="display:none;">
	<div style="padding:15px;">
	<form   action="/billing/reports/add_new" method="POST"  class="form-style-9"  onsubmit="">

		<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
		<h2>Generate Report</h2>
		<br />

		<div class="name_fileds">

			<div  class="f1">
     			<span class="sml">Period Month </span>
     			<select id="period_month"  name="period_month" class="form-control">
     				<option value="1">January</option>
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


		<span class="sml">Report  Description</span>
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






<script>

function generate_new_reports(){
     trig1_v2('generate_period_request_form')
}

</script>
