<?php  $cash_url = '/hwd1/';?>

<?php

//~ get_coll_header_info();

//~ $pr_id = 432;
//~ $pr_id = sprintf("%07d", $pr_id);
//~ echo $pr_id;
//~ die();


$payable = get_other_payable();
$all_acct = all_account();
$invnum  = get_invoice_current();
$banks = get_bank_list();

//~ echo var_dump($invnum);
//~ die();
//~ die();
//~ die();

foreach($banks as $bb2)
{
	$bb2->branch_txt = '';
	$bb2->branch_array = array();

	$bran1 = array_filter(explode(',',$bb2->branches));
	if(empty($bran1)){continue;}

	$str = '';
	foreach($bran1 as $b3){
		$str .= '<option>'.trim($b3).'</option>';
	}
	$bb2->branch_txt = $str;
	$bb2->branch_array = $bran1;

}


	$dat01_1  = strtotime(date("Y-m-d"));
	$dat00_0  = date('Y-m-d');

	if(!empty($trd = @$_GET['trd']))
	{
		$dat01_2 = strtotime($trd);
		if($dat01_2 <= $dat01_1)
		{
			$dat00_0 = date('Y-m-d', $dat01_2);
		}else{
			$dat00_0 = date('Y-m-d', $dat01_2);
		}
	}




//~ echo '<pre>';
//~ print_r($banks->toArray());
//~ die();
//~ var_dump($invnum);
//~ die();

?>

@extends('layouts.cashier')

@section('content')



<div class="cont001">
		<div class="cashier-main-col cashier-main-col1">
			
			<div>
					<small>Transaction Date</small>
					<br />
					<input type="text" value="<?php echo $dat00_0; ?>" class="date_find222" onchange="date_find222_change()"  />
			</div>
			
			<div class="find_search">

				<input type="hidden" class="select222" value="" />

				<table class="search_tab11">
					<tr>
						<td>
							<small>Account #</small>
							<br />
							<input type="text" class="acct0001" onclick="acct_click_me()"  />
						</td>
						<td>
							<small>Last, First</small>
							<br />
							<input type="text" class="find0001"  />
						</td>
						<td>
							<small>&nbsp;</small>
							<br />
							<button  onclick="get_acct_info444()">FIND</button>
						</td>
					</tr>
				</table>
				<?/* */ ?>

			</div>
			<hr class="hr_1"/>

			<div class="client_info_2">

				<table  class="tab_half">
					<tr>
						<td>

							<div class="list_result1">
							</div>

						</td>
					</tr>
				</table>
				
			<div class="account_break_down">

					<table class="led01  acct_ledger01 table10">
					<tbody class="break_down2">
					</tbody>
					</table>

			</div>
				
				

			</div>
		</div> <?php /* end of cashier-main-col1 */ ?>

		<div class="cashier-main-col cashier-main-col2">
			<div class="payment_method_2 water_bill">
				
				<small>Payment Method</small>
				<br />
				<ul class="payment_method_1">
					<li><input type="radio" name="pay_meth"  class="cash1"  value="cash" checked  onclick="change_pay_type(1)" />Cash</li>
					<li><input type="radio" name="pay_meth"  class="check1" value="check" onclick="change_pay_type(0)" />Check</li>
					<li><input type="radio" name="pay_meth"  class="both1" value="both" onclick="change_pay_type(0)" />Both</li>
					<li><input type="radio" name="pay_meth"  class="ada1"  value="ada" onclick="change_pay_type(0)" />ADA</li>
				</ul>
				
				<div>
					
					<table style="width:100%"> 
						<tr>
							<td style="width:50%;">
									<table style="width: 200px;border: 2px solid #ccc;padding: 5px;border-collapse: separate;">
										<tr>
											<td class="withholding-tax1">
												<small>
													<input type="checkbox"
														class="withholding_check"
														onchange="withholding_proc1()"
															/>&nbsp;&nbsp;W/Tax
												</small>												
											</td>
											<td class="rtxt  withholding_val">
												<input type="number"  min="0"
													class="withholding_val_txt rtxt"
													placeholder="0" value="0"
													onchange="withholding_proc33()"
													/>

											</td>
										</tr>					
									</table>
								
							</td>
							<td style="width:50%;">
									<div class="receipt_cont1">Please Wait.</div>
							</td>
						</tr>
					</table>
						
						
						


				</div>
				
				
				<div class="mm1_tabs">
					
					
					<table style="width:100%;">
					<tr>
						<td style="width:50%;border-right: 1px solid #ccc;" class="non_cash11">
							
							<div  class="item1">
								<small>Cash</small>
								<input type="number"  class="both_cash_amount" placeholder="0.00" />
							</div>

							<div  class="item1">
								<small>CHK. AMOUNT</small>
								<input type="number"  class="both_check_amount_full"  placeholder="0.00" />
							</div>
							
							<div  class="item1">
								<small>A/R AMOUNT</small>
								<input type="number"  class="both_check_amount"  placeholder="0.00" />
							</div>
							
							
							<div  class="item1">
								<small>ADA</small>
								<input type="number"  class="ada_amount"  placeholder="0.00" />
							</div>

							<div  class="item1">
								<small>REFF #</small>
								<input type="text"  class="both_check_num"  placeholder="Check number"  style="width: 150px;" />
							</div>

							<div  class="item1">
								<small>CHK DATE </small>
								<input type="text"  class="check_date"  placeholder="Check Date"  style="width: 150px;" />
							</div>


							<small>Bank</small><br />
							<select onchange="bank_change()" class="bank_name11">
								<option value="-1">Select Bank</option>
								<?php
								$bank_x = 0;
								foreach($banks as $bb){ ?>
									<option value="<?php echo $bank_x; ?>"><?php echo $bb->bank_name; ?></option>
								<?php $bank_x++; } ?>
							</select>
							<br />
							<small>Branch</small><br />
							<select class="bank_branches"></select>					
						</td>
						
						<td style="width:50%;padding-left: 10px;">
								<div  class="client_info1" style="font-weight:bold;font-size:16px;">
								</div>
								<table class="tab_half  break_down1 nn1">
								</table>						
						</td>
						</tr>
					</table>
					
					
					
					
					<div class="tab1"  style="display:none;">

					</div>
					
					<div class="tab2">
						
<!--
							<div class="please_wait1">
								<div class="label1s">Please Wait</div>
							</div>
-->
							
							
							
					</div>
					
				</div>
														
					
				<table class="tab_half">
					<tr>

						<td class="half-2" style="position:relative;">



								<div style="clear:both;"></div>

								<hr class="hr_3" />
								<div style="text-align:right;">
									<button onclick="save_payment101()" class="my_foc001">Save (F12)</button>
									<button onclick="print_or101()"  class="my_foc001">Print OR</button>
									<button onclick="view_ledger101()">View Ledger</button>
									<button onclick="add_none_water_bill()">Add None Water Bill</button>
									<button onclick="other_customer_trans_pop()">Other Customer</button>

								</div>



						</td>
					</tr>
				</table>







	</div>
		</div> <?php /* end of cashier-main-col2 */ ?>

		<hr class="hr_4" />

<div class="view_ledger_pop" style="display:none;">
	<div class="view_ledger_cont">

		<div class="contai11">
			Please Wait
		</div>

	</div>
</div>

<?php

$othx = get_other_payable();

?>
<script>
	var other_paya1 = <?php echo json_encode($othx->toArray()); ?>;
</script>

<div class="add_new_non_water_bill_pop" style="display:none;">
	<div class="add_new_non_water_bill_cont">

		<div class="cont222">
			<h2>NON-WATER BILL</h2>
			<input type="hidden" value="0" class="cat_typ_id"  />

			<div class="row">


				<div class="col-md-3"  style="border:0;">
						<small>REFF #</small>
						<br />
						<input type="text"  class="both_check_num"  placeholder="Check number"  style="width: 150px;" />
				</div>
				<div class="col-md-3"  style="border:0;">
						<small>CHK DATE </small>
						<br />
						<input type="date"  class="check_date"  placeholder="Check Date"  style="width: 150px;" />
				</div>

				<div class="col-md-3"  style="border:0;">
						<small>Bank</small><br />
						<select onchange="bank_change2()" class="bank_name22">
							<option value="-1">Select Bank</option>
							<?php
							$bank_x = 0;
							foreach($banks as $bb){ ?>
								<option value="<?php echo $bank_x; ?>"><?php echo $bb->bank_name; ?></option>
							<?php $bank_x++; } ?>
						</select>
				</div>

				<div class="col-md-3"  style="border:0;">
					<small>Branch</small><br />
					<select class="bank_branches_22"></select>							
				</div>


				<div class="col-md-3"  style="border:0;">
						<small>Mode of payment</small><br />
						<select class="nw_mode_payment">
							<option value="cash">CASH</option>
							<option value="check">CHECK</option>
						</select>
				</div>

				<div class="col-md-12" style="border:0;">
						<small>Type of payment</small><br />
						<select class="cat_type" onchange="non_water_pay_type_change()">
							<option value="">Others</option>
							<?php
							$xx = 0;
							foreach($othx as $oo): ?>
							<option value="<?php echo $xx; ?>"><?php echo @$oo->paya_title.' '.number_format(@$oo->paya_amount,2); ?></option>
							<?php

							$xx++;
							endforeach; ?>
						</select>
				</div>

			</div>



			<div>
				<small>Amount Collectable</small><br />
				<input type="number" class="nw_total_amount_debit  form-control" />
			</div>

			<div>
				<small>Witholding Tax</small><br />
				<input type="number" class="nw_tax_amount  form-control" />
			</div>


			<div>
				<small>Amount Received</small><br />
				<input type="number" class="nw_total_amount  form-control" />
			</div>


			<div>
				<small>Description</small><br />
				<textarea class="nw_desc"></textarea>
			</div>
			
			<div>
				<small>GL/SL Code</small><br />
				<input type="text" class="glsl_code form-control" />
			</div>
			
			<div>
				<small>Refference #</small><br />
				<input type="text" class="nw_reff" />
			</div>
			<br />
			<br />
			<div>
				<button onclick="save_none_weter()">Save</button>
			</div>


		</div>

	</div>
</div>

<div class="other_customer_pop" style="display:none;">
	<div class="other_customer_cont">

		<div class="cont222">
			<strong>OTHER CUSTOMERS</strong>
			<br />
			<br />
			<div class="tab_but">
				<button class="active">Payment</button>
				<button>Previous Transaction</button>
			</div>
			<br />
			<br />
			
			<div class="previous_tab">
				Please Wait...
				
				<div class="scrol11">
					<table cellpadding="0" cellspacing="0" border=1 class="ot_cus_tab1">
						<tr class="head1">
							<td class="w11">#</td>
							<td class="w12">Date</td>
							<td class="w13">Invoice #</td>
							<td class="w14">Name</td>
							<td class="w15">Amount</td>
							<td class="w16 cc">Status</td>
							<td class="w17">&nbsp;</td>
						</tr>
						
						<?php for($x=1;$x<=100;$x++): ?>
						<tr>
							<td><?php echo $x; ?></td>
							<td>Mar 3, 2020</td>
							<td>11221</td>
							<td>James Cameron</td>
							<td>1,500.00</td>
							<td class="cc">Active</td>
							<td class="cc">
								<small>View Info</small>
								|
								<small>Print O.R.</small>

							</td>
						</tr>
						<?php  endfor; ?>
					</table>
				</div>				
			</div>

			
			<div class="payment_tab" style="display:none;">
				<small>Transaction Date</small>
				<input type="text"  class="form-control" />
				<br />
				
				<small>Invoice #</small>
				<input type="text"  class="form-control" />
				<br />
				<small>Name / Company </small>
				<input type="text"  class="form-control" />
				<br />
				<small>Amount</small>
				<input type="number"  class="form-control" />
				<br />
				<small>Description</small>
				<textarea class="form-control"></textarea>
				<br />				
				<small>GL/SL CODE</small>
				<input type="text"  class="form-control" />
				<br />				
				<br />				
				<button>Save Payment</button>
			</div>
			
			
		</div>
	</div>
</div>



<style>
.previous_tab .scrol11{
	height:300px;
	overflow-y:scroll;
	padding:15px;
}	

.ot_cus_tab1{
	width:100%;
}
.ot_cus_tab1 .head1 td{
	background-color:#108479;
	color:white;
	border-right:1px solid #fff;
}
.ot_cus_tab1 td{
	padding:5px;
}

.tab_but button{
	border:1px solid #108479;
	color:white;
}
.tab_but button.active{
	color:#108479;
	background:white;
}

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

.withholding_val_txt{
    border: 0;
    margin-right: -13px;
	width: 75px;
}

.please_wait1{
    background: rgba(0,0,0,0.5);
    color: white;
    text-align: center;
    position: absolute;
    width: 100%;
    padding: 150px;
    display:none;
    z-index:9999;
}

.reciept_type_sty{
    padding: 0;
    margin: 0;
}
.reciept_type_sty li{
    list-style: none;
    display: inline-block;
    padding: 5px;
    border: 0px solid #ccc;
    text-align: center;
    width: 80px;
}

.list_result1{
	border: 1px solid #ccc;
	padding: 10px;
	overflow: scroll;
	height: 150px !important;
	width:100% !important;
}

.list_result1 ul{
	padding:0;
	margin:0;
	width:1000px;
}

.list_result1 li{
	list-style:none;
	border-bottom:1px solid #ccc;
	padding-bottom:2px;
	padding-top:2px;
	padding-left:5px;
	padding-right:5px;
	cursor:pointer;
}
.list_result1 li:hover{
	background:#ccc;
}
.list_result1 ul{
	width:100% !important;
}
.cashier-main-col .account_break_down {
    height: 200px !important;
    margin-top: 0;
}
.cashier-main-col.cashier-main-col1{
	width:45% !important;
}
.cashier-main-col.cashier-main-col2{
    width: 54% !important;	
}
.cashier_page .payment_method_1 li {
    margin-right: 0;
    display: inline-block;
    border: 0px solid #ccc;
    padding: 5px;
    padding-right: 20px;
}
.main_row{
	height: 600px !important;
}
.amount_recieve{
	margin-right: -12px !important;
}

.current_inv{
    padding: 5px;
    text-align: center;
    font-weight: bold;
    font-size: 20px;	
}

.cashier-main-col.cashier-main-col2 * {
    font-size: 16px !important;
}
table.tab_half.break_down1.nn1 .rtxt {
    font-weight: bold;
}

.non_cash11{
}
.non_cash11 .item1 {
    position: relative;
    width: 240px;
    border-bottom:1px solid #ccc;
    margin-bottom: 10px;
}
.non_cash11 .item1 input{
	float: right;
	text-align: right;
	width: 70px;
	margin-top: -5px;
	border: 0;
}


.cashier_page .add_new_non_water_bill_cont div{
	margin-bottom: 0px;
}

.cashier_page .add_new_non_water_bill_cont textarea.nw_desc{
	    min-height: 50px;
}
.client_info_2 * {
    font-size: 14px !important;
    font-weight: bold;
}
.acct_ledger01 td {
    font-size: 14px !important;
}


.both_check_amount{width:100px !important;}
.both_check_amount_full{width:100px !important;}
.cc{text-align:center;}
.ll{text-align:left;}
.rr{text-align:right;}

</style>


<script src="/js/jquery-code-scanner.js"></script>


<script>

//~ var all_acct = <?php echo json_encode($all_acct); ?>;
var all_acct = [];

var result_data = null;
var curr_acct = null;
var last_url = '';
var invoices = <?php echo json_encode($invoices); ?>;
var banks = <?php echo json_encode($banks->toArray()); ?>;
var nw_invoice = <?php echo  (int) get_nw_inv(); ?>;
var trans_date = "<?php echo $dat00_0; ?>";

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

jQuery(document).ready(function(){
	get_receipt_html11();
	
	jQuery('.acct0001').codeScanner({
			onScan: function ($element, code) {
				jQuery('.acct0001').val(code);
				get_acct_info444();

			}		
		});

});


function get_receipt_html11()
{
	jQuery.get('/collections/get_receipt_html11', function($data){
		jQuery('.receipt_cont1').html($data.html1);
	});
}

jQuery(document).ready(function(){
	jQuery('.date_find222').datepicker(
		{format: 'yyyy-mm-dd', autoHide:true}
	);

	jQuery('.check_date').datepicker(
		{format: 'yyyy-mm-dd', autoHide:true}
	);

	keyevent_start();
});


function date_find222_change()
{
	let dd1 = jQuery('.date_find222').val();
	window.location = '/collections/?trd='+dd1;
}

function keyevent_start()
{
	
	jQuery( ".acct0001" ).keyup(function($event) {

		if($event.keyCode  == 27){
			jQuery( ".acct0001" ).val('');
			jQuery( ".find0001" ).val('');
			jQuery('.list_result1').html('');
			return;
		}

		if($event.keyCode  != 13){
			return;
		}
		get_acct_info444();
	});


	jQuery( ".find0001" ).keyup(function($event) {
		if($event.keyCode  == 27){
			jQuery( ".acct0001" ).val('');
			jQuery( ".find0001" ).val('');
			jQuery('.list_result1').html('');
			return;
		}


		if($event.keyCode  != 13){
			return;
		}
		get_acct_info444();
	});
	
	
	jQuery(document).keyup(function($event){
		
	}); 

	
	

}//


function non_water_pay_type_change()
{

	let gg = jQuery('.pop101 .cat_type').val();
	if(gg == ''){
		jQuery('.pop101 .nw_total_amount').val('');
		jQuery('.pop101 .nw_desc').val('');
		return;
	}

	let mmm = other_paya1[gg];
	//~ console.log(mmm);

	jQuery('.pop101 .nw_total_amount_debit').val(mmm.paya_amount);
	jQuery('.pop101 .nw_total_amount').val(mmm.paya_amount);
	jQuery('.pop101 .nw_desc').val(mmm.paya_title);
	jQuery('.pop101 .cat_typ_id').val(mmm.id);
	jQuery('.pop101 .glsl_code').val(mmm.glsl_code);
	// jQuery('.pop101 .glsl_code').val(mmm.glsl_code);


}//


function change_pay_type(typ1)
{
	//~ alert(typ1);
	if(typ1 == 1)
	{
		jQuery('.tab2').show();
		jQuery('.tab1').hide();
	}
	else
	{
		jQuery('.tab2').hide();
		jQuery('.tab1').show();
	}
}


function other_customer_trans_pop()
{
	trig1_v2('other_customer_pop');
}


function acct_click_me()
{
	jQuery('.acct0001').val('');
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
