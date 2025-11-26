<?php  $cash_url = '/hwd1/';?>

@extends('layouts.cashier')

@section('content')

<div style="clear:both;"></div>
<div class="white_box1">
	<div class="buttons">
		<button onclick="add_new_invoice_form();">Add Invoices</button>
	</div>
	<div style="clear:both;"></div>

	<div class="tabs001">


		<table class="table10 table-bordered  table-hover"><tbody>

			<tr class="headings">
				<td width="30%">Invoice Sequense #</td>
				<td width="5%">Date Issued</td>
				<td width="5%">Status</td>
			</tr>

			<?php foreach($invoices as $invs): ?>
		     <tr onclick="">
				<td><?php echo sprintf('%07d', $invs->seq_start); ?> - <?php echo sprintf('%07d', $invs->seq_end); ?></td>
				<td><?php echo date('F d, Y',strtotime($invs->date_stat)); ?></td>
				<td><?php echo $invs->stat; ?></td>
			</tr>
			<?php endforeach; ?>

		</tbody></table>




	</div>

</div>

<div class="add_new_invoice" style="display:none;">
	<div class="padd20 new_invoice1">
		<form   action="/collections/invoices/new" method="POST"  class="form-style-9"  onsubmit="return go_add_sequence(this)">
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">

			<h2>Invoice information</h2>

			<ul class="item_list1  ">
				<li>Sequence Start  <span class="rd"><input type="number" min="0" placeholder="######"  name="seq_start" class="seq_start_input" onchange="seq_val_change()"  /></span></li>
				<li>Sequence End  <span class="rd"><input type="number" min="0" placeholder="######"   name="seq_end"  class="seq_end_input" onchange="seq_val_change()"  /></span></li>
				<li>Total Invoice  <span class="rd count1"></span></li>
			</ul>
			<div style="text-align:center;margin-top:50px;">
				<button>Save</button>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<button>Cancel</button>
			</div>
		</form>
	</div>
</div>


<script>
function add_new_invoice_form()
{
	trig1_v2('add_new_invoice');
}
function	go_add_sequence()
{
	let seq_start_input = jQuery('.pop101 .seq_start_input').val();
	let seq_end_input = jQuery('.pop101 .seq_end_input').val();

	if(seq_start_input <= 0){
		alert('input error. code 1');
		return false;
	}
	if(seq_end_input <= 0){
		alert('input error.  code 2');
		return false;
	}
	if(seq_end_input == seq_start_input){
		alert('input error.  code 3 ');
		return false;
	}
	if(seq_end_input < seq_start_input){
		alert('input error.  code 4');
		return false;
	}

	$conf  = confirm('Please confirm...');
	if(!$conf){return false;}
	return true;
}
function seq_val_change()
{
	let seq_start_input = jQuery('.pop101 .seq_start_input').val();
	let seq_end_input = jQuery('.pop101 .seq_end_input').val();
	if(seq_start_input <= 0){return false;}
	if(seq_end_input <= 0){return false;}
	if(seq_end_input == seq_start_input){return false;}
	if(seq_end_input < seq_start_input){return false;}
	jQuery('.pop101 .count1').html(seq_end_input-seq_start_input);
}
</script>


@endsection

@section('inv_include')

	@include('billings.inc.php_mod.pop1')



<style>
.padd20{
	padding:20px;
}
.white_box1{
	background:#fff;
	color:#333;
	margin-top:50px;
	padding:15px;
}

.white_box1 .buttons{
	float:right;
	margin-bottom:15px;
}

.white_box1 .headings td{
	text-align:left;
}
.new_invoice1 input[type="number"]{
	border:0;
	padding:5px;
    text-align: right;
}
.no_step,
.new_invoice1  input[type=number]::-webkit-inner-spin-button,
.new_invoice1  input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.new_invoice1 .item_list1{
	margin-top:20px;
}
.new_invoice1 ul.item_list1 li{
	margin-bottom: 10px;
     padding-bottom: 10px;
}
.back_1 .box1{
	min-height: 100px;
}
</style>

@endsection
