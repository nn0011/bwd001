<?php
$acct_reading['data'] = array('1');



?>


<button  class="trig1"  data-box1="add_discount11">Add Discount Type</button>
<br />
<br />
<!------>
<!------>
<table class="table10 table-bordered  table-hover"><tbody>
	
	<tr class="headings">
		<td width="5%">Discount Name : </td>
		<td width="5%">Discount Description </td>
		<td width="5%">Discount Percentage </td>
		<td width="5%">Status </td>
		<td width="5%">Action</td>
	</tr>
	
	<?php //for($x=0;$x<=30;$x++): ?>
	<?php 
	$index1 = 0;
	foreach($bill_discount as $bill_d): ?>
		
		<?php 
			
			//$bill_data = (array) json_decode($bill_r['meta_data']);
		
		?>
	

		<tr>
			<td width="5%"><?php echo $bill_d['meta_name']; ?></td>
			<td width="5%"><?php echo $bill_d['meta_desc']; ?></td>
			<td width="5%"><?php echo $bill_d['meta_value']; ?>%</td>
			<td width="5%"><?php echo $bill_d['status']; ?></td>
			<td width="5%">
					<button  onclick="edit_discount(<?php echo $index1; ?>)">Edit</button>
			</td>
		</tr>

		
	<?php $index1++; endforeach; ?>
								
</tbody></table>



<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_discount11" style="display:none;">
	<div style="padding:15px;">
	<form   action="/billing/billing/discount/add" method="POST"  class="form-style-9"  onsubmit="">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">		
				<h2>Add New Discount Type</h2>
				<br />
				<span class="sml">Discount Name : </span>
				<input type="text" class="form-control" autocomplete="off"  placeholder="Name"  name="dis_name"  id="new_rate1">
				
				<span class="sml">Discount Description </span>
				<textarea   placeholder="Description"  name="dis_desc"    class="form-control"></textarea>
								
				<span class="sml">Discount percentage </span>
				<input type="number"  min="0"  max="100" class="form-control" autocomplete="off"  placeholder="Discount %"  name="dis_value"  id="new_rate2"  step="any"  >


				<br />
				<br />
				
				<div style="text-align:center;">
					<button>Save</button>
				</div>
				
				<div class="name_fileds">
				</div>

	</form>		
	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_discount12" style="display:none;">
	<div style="padding:15px;">
	<form   action="/billing/billing/discount/update" method="POST"  class="form-style-9"  onsubmit="">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">		
			<input type="hidden"  name="dis_id"  id="new_rate1">

				<h2>Edit Discount Type</h2>
				<br />
				<span class="sml">Discount Name : </span>
				<input type="text" class="form-control" autocomplete="off"  placeholder="Name"  name="dis_name"  id="new_rate2">
				
				<span class="sml">Discount Description </span>
				<textarea   placeholder="Description"  name="dis_desc"    class="form-control"    id="new_rate3"></textarea>
								
				<span class="sml">Discount percentage </span>
				<input type="number"  min="0"  max="100" class="form-control" autocomplete="off"  placeholder="Discount %"  name="dis_value"  step="any" id="new_rate4"   >


				<br />
				<br />
				
				<div style="text-align:center;">
					<button>Save</button>
				</div>
				
				<div class="name_fileds">
				</div>
				
				
	</form>		
	</div>
</div>
<!------------------------------------>
<!------------------------------------>


<script>
var  discount_data  =  <?php echo json_encode($bill_discount); ?>;
function edit_discount($ind){
	var cur_dis = discount_data[$ind];
	
	trig1_v2('add_discount12');
	setTimeout(function(){
		jQuery('.pop101 #new_rate1').val(cur_dis.id);
		jQuery('.pop101 #new_rate2').val(cur_dis.meta_name);
		jQuery('.pop101 #new_rate3').val(cur_dis.meta_desc);
		jQuery('.pop101 #new_rate4').val(cur_dis.meta_value);
	}, 200);
}	
</script>


