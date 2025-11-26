	<?php  $cash_url = '/hwd1/';?>

@extends('layouts.cashier')

@section('content')

<div style="clear:both;"></div>



<div class="white_box1">
	
	<br />
	<br />
	<br />
	
	<div style="clear:both;"></div>
	
		<div class="date_find">
			<?php 
			
				$dat1 = date('Y-m-d');
				if(!empty(@$_GET['dd'])){
					$dat1 = date('Y-m-d', strtotime(@$_GET['dd']));
				}
			
			?>
			<input type="text"  value="<?php echo $dat1; ?>"  class="date_find111"  onchange="change_date_find()" />
		</div>
		
	<div style="clear:both;"></div>
	<br />
		

	<div class="tabs001">
		



		<table class="table10 table-bordered  table-hover"><tbody>

			<tr class="headings">
				<td width="10%" style="text-align:left;">Date :</td>
				<td width="10%"  style="text-align:left;">Invoice No.</td>
				<td width="30%"  style="text-align:left;">Name</td>
				<td width="5%"  style="text-align:right;">Amount</td>
				<td width="10%"  style="text-align:right;">Status</td>
				<td width="10%"  style="text-align:right;">&nbsp;</td>
			</tr>

			<?php 
			
			$ind = 0;
			foreach($colls as $cc): ?>
		     <tr>
				<td style="text-align:left;"><?php echo date('M d, Y @ H:i A',strtotime($cc->payment_date)); ?></td>
				<td style="text-align:left;"><?php echo sprintf('%07d',$cc->invoice_num); ?></td>
				<td style="text-align:left;"><?php echo $cc->full_name; ?></td>
				<td style="text-align:right;"><?php echo number_format($cc->payment, 2); ?></td>
				<td class="vv_<?php echo $ind; ?>"><?php echo $cc->status2; ?></td>
				<td><button onclick="edit_receipt_type(<?php echo $ind; ?>)">Edit Receipt Type</button></td>
			</tr>
			<?php $ind++; endforeach;  ?>

		</tbody></table>
		
		<?php 
		
		$col1_1 = get_coll_header_info($dat1);
		//~ echo '<pre>';
		//~ print_r($col1_1->ttl_col);
		//~ echo '</pre>';
		
		?>
		
		<div class="summary_rep1">
			<div>
				<table class="tab_half">
					<tr>
						<td>Transaction</td>
						<td class="rtxt"><?php echo $col1_1->ttl_trx; ?></td>
					</tr>
					
					<tr>
						<td>Total Collected</td>
						<td  class="rtxt"><?php echo number_format($col1_1->ttl_col, 2); ?></td>
					</tr>
					
				</table>
			</div>
		</div>




	</div>

</div>



<div class="edit_receipt_type_pop" style="display:none;">
	<div class="edit_receipt_type_cont">
		
		<div class="contai11">
			<table class="tab11">

			<tbody>
				<tr>
					<td>Name</td>
					<td class="vname">---</td>
				</tr>

				<tr>
					<td>Payment Date</td>
					<td class="vdate">---</td>
				</tr>
				
				<tr>
					<td>New Payment Date &nbsp;&nbsp;<small class="red_but" onclick="save_new_payment_date()">Save</small></td>
					<td class="vdate22">
						<input type="text" class="vdate22_inp" />
					</td>
				</tr>
				
				
    			<tr>
                    <td>Receipt #</td>
					<td class="vreceipt">---</td>
				</tr>
    			<tr>
                    <td>Receipt Type</td>
					<td class="vreceipt_type">---</td>
				</tr>
    			<tr>
                    <td>Total Amount Payed</td>
					<td class="vamount">---</td>
				</tr>				
    
				
			</tbody>
				
			</table>
			
			<hr />
<!--
			<button onclick="official_receipt()">Mark as Official Receipt</button>
			<br />
			<button onclick="collector_receipt()">Mark as Collectors Receipt</button>
			<br />
-->
			<button onclick="cancel_receipt()">Cancel Receipt</button>
			
			
		</div>
		
	</div>
</div>



@endsection


@section('inv_include')
@include('billings.inc.php_mod.pop1')


<link rel="stylesheet" href="/css/collection/col2.css">
<script src="/css/collection/col2.js"></script>

<script>
var coll1 = <?php echo json_encode($colls); ?>;	

async function save_new_payment_date()
{
	let confi = confirm('Are you sure to save to new date?');
	if(!confi){return;}
	
	let get_date = jQuery('.pop101 .vdate22_inp').val();
	
	let timestamp = new Date(get_date);
	if (isNaN(timestamp) != false) {alert('Invalid Date.');return;}
	
	let id_m = curr_coll.id;
	
	let url1 = "/save_update_new_date/"+id_m+"/"+get_date;
	let new_key = '';
	
	await jQuery.get(url1+'?key=1', function(dd){
		new_key = dd.key;
	}).promise();


	await jQuery.get(url1+'?new_key='+new_key, function(dd){
		location.reload();
	}).promise();
	
	
}//

</script>


<style>
.datepicker-container{
	z-index:999999 !important;
}
.red_but{
	color:red;
	font-weight:bold;
	cursor:pointer;
}
</style>


@endsection
