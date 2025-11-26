<?php 

// {{ csrf_field() }}

$other_p = NWBill::get_other_payable();
$other_p = $other_p->toArray();
// ee($other_p, __FILE__, __LINE__);

?>

<div class="rem_cont">


	<div style="text-align: right;padding-bottom:15px;">
		<button class="form-controlx btn btn-small" onclick="add_new_nw_billing()">Add New</button>
	</div>




</div>


<hr />



<div class="bbb1" style="display:none;">
	<div style="padding:15px;padding-top:50px">

		<small>Other Payables</small>
		<select class="form-control other_paya_sel33"  onchange="change_other_payable()">
			<option value="">Select</option>
			<?php foreach($other_p as $k => $v): ?>
			<option value="<?php echo $v['id']; ?>"><?php echo $v['paya_title']; ?> - <?php echo number_format($v['paya_amount'], 2); ?></option>
			<?php endforeach; ?>
		</select>
		<small>Names</small>
		<input type="text" class="form-control other_paya_name" placeholder="Description" />
		<textarea placeholder="remarks" class="form-control other_paya_desc"></textarea>

		<small>Total Amount</small>
		<input type="text" class="form-control other_paya_amt" placeholder="Total Amount" />

		<small>Monthly billing amount</small>
		<input type="text" class="form-control other_paya_bill_amt" placeholder="0.00" />

		<small>Billing Start</small>
		<input type="date" class="form-control other_paya_date_start" placeholder="Billing Start" />
		
		<small>Accounting Code</small>
		<input type="text" class="form-control other_paya_code" placeholder="------" />
		<br />
		<br />
		<button class="form-controlx btn btn-small" onclick="">SAVE</button>
	</div>
</div>





<style>

</style>


<script>
var other_paya = <?php echo json_encode($other_p); ?>;

jQuery(document).ready(function(){

});

function add_new_nw_billing()
{
	trig1_v2('bbb1');
}//

function change_other_payable()
{
	var vl1 = jQuery('.pop101 .other_paya_sel33').val();

	other_paya.map((v,i)=>{

		if(v.id == vl1) {
			jQuery('.pop101 .other_paya_name').val(v.paya_title)
			jQuery('.pop101 .other_paya_desc').val(v.paya_title)
			jQuery('.pop101 .other_paya_amt').val(v.paya_amount)
			jQuery('.pop101 .other_paya_code').val(v.glsl_code)
		}

	});

}//
</script>
