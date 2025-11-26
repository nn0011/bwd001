<?php  $cash_url = '/hwd1/';?>

<?php 

$payable = get_other_payable();
$all_acct = all_account();

?>

@extends('layouts.cashier')

@section('content')

	<div class="inv_cont">
		<div class="invoice_set1">
			Invoice set used
			<select id="invoice_set">
				<?php foreach($invoices as $invs): ?>
					<option value="<?php echo $invs->id; ?>"><?php echo $invs->seq_start.' to '.$invs->seq_end;  ?></option>
				<?php endforeach;  ?>
			</select>
			<input type="text"  value="<?php echo @$invoices[0]->seq_c; ?>"  id="current_invoice" style="display:none;"   />
		</div>
	</div>

		<div class="cont_1">

			<div class="filter_1">
				<input type="text" name="accnt_no" id="s_accnt_no" placeholder="Account No.">
				<input type="text" name="fn" id="s_fn" placeholder="First Name">
				<img src="<?php  echo $cash_url; ?>img/search.jpg"  onclick="go_search_acct()">
			</div>
			
			
		</div> <!--end of cont -->

		<div id="result_here"></div>

<!-- ------------------------>
<!-- ------------------------>
<!-- ------------------------>


	<div class="cashier_view_info">
		<div class="pop_view_info_table pop_table2">
			<ul class="item_list1 ">
				<li>Name   <span class="val1"></span></li>
				<li>Address<span  class="val2"></span> <div style="clear:both;"></div></li>
				<li>Account No.<span class="val3"></span></li>
				<?php
				/*
				<li>Last Payment Made<span>P 300.00</span></li>
				<li>Last Payment Date<span>May 2, 2018</span></li>
				<br>
				*/ ?>
			</ul>
			 <br />
			<ul class="item_list1  reading1">
				<li>Billing Period <span class="val4"></span></li>
				<li>Previous Reading<span class="bal1"></span></li>
				<li>Current  Reading <span  class="bal2"></span></li>
				<li>Consumption(cu.m)   <span  class="bal3"></span></li>
			</ul>
			 <br />
			<ul class="item_list1  info1">
				<!-- <li>Previous Bill<span class="bal1"></span></li> -->
				<li>Current Bill <span  class="bal2"></span></li>
				<li>Discounts <span  class="span_discount"></span></li>
				<li>Penalty <span  class="span_penalty"></span></li>
				<li>Arears <span class="span_arrear"></span></li>
				<li>Adjustment <span class="span_adjust"></span></li>
				<!-- <li>Total Bill <span  class="bal3 rd"></span></li> -->
			</ul>
			 <br />
			<ul class="item_list1  payment001">
				<li>Total Payment Made <span class="bal1 rd"></span></li>
				<li>Balance  <span  class="bal2 rd"></span></li>
			</ul>
			<br><br>

			Invoice Number :<input type="number"  class="no_step invoice_number_used" autocomplete="off"  min="0">
			<div class="payment_section">
				
				<?php  $water_bill_cash_check = 1;?>
				
				<div class="col-md-6">
					
					<select id="bank_id"  onchange="bank_change()">
						<option value="0">Cash</option>
						<?php foreach($banks as $b1){ ?>
						<option value="<?php echo $b1->id; ?>"><?php echo $b1->bank_name; ?></option>
						<?php  } ?>
					</select>
						
					<div class="mm_bank_info dis_non">
						<small>Check number</small>
						<input type="text"  id="bank_check"  class="no_step no_bg">
						<textarea  class="" placeholder="Branch, Addres, Tel, Etc."  id="bank_info"  style="resize: none;width:100%;"></textarea>
					</div>								
				
				</div>
				
				<div class="col-md-6">
					
						<div class="payment1_cont">
							<small>Payment:</small>
							<input type="number"   id="amount_pay"  min="0"  max="99999"  class="no_step" onchange="update_amount_pp1()">
							<div class="cash_box">
								<small>Cash</small>
								<input type="number"   id="amount_pp1"  min="0"  max="99999"  class="no_step">
							</div>
						</div>					

						<br>
						<button class="btn1"  onclick="make_payment()">Make Payment</button>
						
						<br>
						<button class="btn2"  onclick="pop_close()">Close</button>

					
				</div>
				
				<div style="clear:both;"></div>
				
				
				
				
				<div class="payment_loading1" style="display:none;">
					Printing....
					<br />
					Please Wait
					<br />
					<img src="/ajax-loader3.gif" />
				</div>

				
			</div>
		</div>
	</div>
	

	<div class="select_type_of_payment" style="display:none;">
		<div class="pop_view_info_table">
			
			<div style="text-align:center;">
					<br />
					<br />
					<span style="font-size:21px;">Select what payment type.</span>
					<br />
					<br />
					<button type="button" class="btn btn-success  btn-lg"  onclick="water_bill_execute()">Water Bill</button>
						&nbsp;&nbsp;&nbsp;
					<button type="button" class="btn btn-primary  btn-lg"   onclick="none_water_bill_execute()">None Water Bill</button>
					<br />
					<br />
			</div>
			
		</div>
	</div>
	
	
	<?php  $none_water_bill_cash_check = 1;?>

	<div class="none_water_bill_pop1" style="display:none;">
		<div class="pop_view_info_table">
			<span style="font-size:21px;">Select payable.</span>
			<br />
			<br />
			<select  class="form-control"  id="non_water_item" style="height:auto; padding:10px;font-size:22px">
				<?php foreach($payable as $pp20): ?>
				<option  value="<?php echo $pp20->id; ?>"><?php echo $pp20->paya_title; ?> - Php <?php echo number_format($pp20->paya_amount, 2); ?></option>
				<?php endforeach; ?>
			</select>
			<br />
			<br />
			<div style="font-size:18px;"  class="non_wa_name"></div>
			<div style="font-size:14px;">
				Account No. : <strong  class="non_wa_acct"></strong>
				<br />
				Meter No. : <strong class="non_wa_meter"></strong>
				<br />
				Address  : <strong  class="non_wa_addr"></strong>
				<br />
			</div>
			<br />
			<br />
			Invoice Number :<input type="number"  class="no_step invoice_number_used" autocomplete="off"  min="0">
			<br />
			<br />
				
			<div class="payment_section">
				
				<div class="payment_loading1" style="display:none;">
					Printing....
					<br>
					Please Wait
					<br>
					<img src="/ajax-loader3.gif">
				</div>
				
				<div class="col-md-6">
					
					<select id="bank_id"  onchange="bank_change()">
						<option value="0">Cash</option>
						<?php foreach($banks as $b1){ ?>
						<option value="<?php echo $b1->id; ?>"><?php echo $b1->bank_name; ?></option>
						<?php  } ?>
					</select>
						
					<div class="mm_bank_info dis_non">
						<small>Check number</small>
						<input type="text"  id="bank_check"  class="no_step no_bg">
						<textarea  class="" placeholder="Branch, Addres, Tel, Etc."  id="bank_info"  style="resize: none;width:100%;"></textarea>
					</div>					
					
					
				</div>
				<div class="col-md-6" style="text-align:left;">
					<small>Amount</small>
					<input type="number" id="amount_pp1" min="0" max="99999" class="no_step">
										
					<br>
					<button class="btn1" onclick="make_payment_non_water()">Make Payment</button>
					<br>
					<button class="btn2" onclick="pop_close()">Close</button>
				</div>
					
				<div style="clear:both;"></div>

				
			</div>
			
			
			
		</div>
	</div>
	
	
	<div class="new_cashier_pop" style="display:none;">
		<div class="new_cashier_cont">
			
			<div class="find_search">
				<small>Find</small>
				<br />
				<select  class="select111" onchange="get_acct_info111()">
					<option value="">Find Client</option>
					<?php 
					$xx=0;
					foreach($all_acct as $acct): ?>
					<option value="<?php echo $xx;  ?>"><?php echo $acct['lname'].', '.$acct['fname'].' - '.$acct['acct_no']; ?></option>
					<?php 
						$xx++;
					endforeach; ?>
				</select>
			</div>
			
			<div class="client_info">
				<small>Client</small>
				<br />
				<table>
					<tr>
						<td>
							<small>L. Name</small>
							<br />
							<input type="text"  class="lname"  disabled  />
						</td>
						<td>
							<small>F. Name</small>
							<br />
							<input type="text"  class="fname" disabled />
						</td>

						<td>
							<small>M.I.</small>
							<br />
							<input type="text"  class="mi"  disabled />
						</td>
					</tr>
				</table>
			</div>
			
			<div class="receipt_info">
				<small>Receipt</small>
				<br />
				<div>
					<table>
						<tr>
							<td>
								Type : 
								<select class="receipt_type">
									<option value="official">Official Receipt</option>
									<option value="collector">Collector Receipt</option>
								</select>
							</td>
							<td>
								OR # <input type="text" />  
							</td>
							
						</tr>
					</table>
				</div>
			</div>

			<div class="payment_info">
				<small>Payment</small>
				<br />
				<div>
					Method  
					<ul class="method_list">
						<li>Cash</li>
						<li>Check</li>
						<li>Both</li>
					</ul>
				</div>
			</div>

			
			
			<div class="bill_info">
				<small>Bill</small>
				<br />
				
			</div>

			<div class="payment_info">
				<small>Payment</small>
				<br />
				<br />
				
				<small>Bill Amount</small>
				<input type="number"  min="0" class="current_bill1" />
				<br />
				
				<small>Amount Due</small>
				<input type="number"  min="0"  class="bill_amount1"  />
				<br />

				<small>Amount Recieve</small>
				<input type="number"  min="0" class="amount_recieve" value="0"  onchange="update_amount_recieve()" />
				<br />
				
				<small>Change</small>
				<input type="number"  min="0"  />
				<br />
			</div>
			
			<div>
				<button>Print O.R.</button>
				<button>Validator</button>
			</div>
			
		</div>
	</div>
	
	

<style>
.cashier_page .payment_section{
	padding:10px;
}
.payment_section  .col-md-6{
	 box-sizing: border-box;
	 border:0;
	 text-align:left;
}
.payment_section #bank_id{
    width: 100%;
    padding: 3px;
    font-size: 14px;
    margin-bottom: 10px;    
}
.cashier_page .payment_section input.no_bg{
	background-image:none;
	text-align:left;
}
.dis_non{
	display:none;
}
.select2-container{z-index:999999 !important;}

</style>




<script>
var all_acct = <?php echo json_encode($all_acct); ?>;
var result_data = null;
var curr_acct = null;
var last_url = '';
var invoices = <?php echo json_encode($invoices); ?>;
//var curr_invoice = invoices[0].seq_c;
var curr_invoice = 0;
if(invoices.length != 0){
  curr_invoice = invoices[0].seq_c;
}

var csrf1 = '<?php echo csrf_token(); ?>';

<?php $bank_change = 1; ?>
function bank_change()
{
	
	let bb_id = jQuery('.pop101 #bank_id').val();
	if(bb_id == 0)
	{
		jQuery('.pop101 .mm_bank_info').addClass('dis_non');
		jQuery('.pop101 .cash_box').removeClass('dis_non');
	}
	else
	{
		jQuery('.pop101 .mm_bank_info').removeClass('dis_non');
		jQuery('.pop101 .cash_box').addClass('dis_non');
	}
	
}
</script>



@endsection

@section('inv_include')

@include('billings.inc.php_mod.pop1')
<link rel="stylesheet" href="/select2/css/select2.min.css">
<script src="/select2/js/select2.full.min.js"></script>


<link rel="stylesheet" href="/css/collection/collection1.css">
<link rel="stylesheet" href="/css/collection/col1.css">
<script src="/js/collection/collection.js"></script>
<script src="/js/shortcut.js"></script>
<script src="/js/short.js"></script>


<div class="print1" style="display:none;">
</div>

@endsection
