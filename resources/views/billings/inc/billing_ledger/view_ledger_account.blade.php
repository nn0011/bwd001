<?php

//~ echo '<pre>';
//~ print_r($account_info->toArray());
//~ die();

$_SESSION["my_csrf"]=uniqid();


//account_info
?>


<h4><?php echo $account_info->fname.' '.$account_info->lname; ?></h4>
<p>
	#<?php echo $account_info->acct_no; ?>
	<br />
	<?php echo get_zone101($account_info->zone_id); ?>
	<br />
	<?php echo $account_info->address1; ?>
</p>



<br />


<ul class="tabview_cmd">
	<li onclick="ledger_1('tabview1', 'tt1')"  class="tt1 active">Account Ledger</li>
	<li onclick="ledger_1('tabview2', 'tt2')"  class="tt2">History</li>
	<li onclick="ledger_1('tabview3', 'tt3')"  class="tt3">Reading Ledger</li>
	<li onclick="ledger_1('tabview4', 'tt4');get_nwb_list()"  class="tt4">NW-BILLING </li>
	<li onclick="ledger_1('tabview5', 'tt5');"  class="tt5">Beginning Balance</li>
</ul>

{{--  --}}
{{--  --}}

<div class="tab1 tabview5  tabview"  style="display:none; margin-top:30px;">
	<div class="begin_bal_cont">
			<form class="begin_bal_form01" onsubmit="return false;">
				<div class="row rox">
					<div class="col-md-12">
							<small>Total Beginning</small>
							<input type="text" class="form-control ttl_bal" name="ttl_bal" placeholder="0.00" style="text-align: right" />
					</div>
					<div class="col-md-12">
							<small>Current</small>
							<input type="text" class="form-control current" name="current"  placeholder="0.00" style="text-align: right" />
					</div>
					<div class="col-md-12">
							<small>Penalty</small>
							<input type="text" class="form-control penalty" name="penalty"  placeholder="0.00" style="text-align: right" />
					</div>
					<div class="col-md-12">
							<small>PY-Arrear</small>
							<input type="text" class="form-control py_arrear" name="py_arrear"  placeholder="0.00" style="text-align: right" />
					</div>
					<div class="col-md-12">
							<small>CY-Arrear</small>
							<input type="text" class="form-control cy_arrear" name="cy_arrear"  placeholder="0.00" style="text-align: right" />
					</div>
					<div class="col-md-12">
							<small>NWB/Others</small>
							<input type="text" class="form-control nwb_arrear" name="nwb_arrear"  placeholder="0.00" style="text-align: right" />
					</div>
					<div class="col-md-12">
							<small>Date</small>
							<input type="date" class="form-control date1" name="date1"  style="text-align: right" />
					</div>
				</div>
			</form>
	</div>
	<button onclick="save_beginning_balance()">Update Save</button>
</div>


<div class="tab1 tabview4  tabview"  style="display:none; margin-top:30px;">
	<div class="nwb_billing_cont">...</div>
	<button onclick="trig1_v2('bbb1');">Add NW-Billing</button>
</div>

<div class="tab1 tabview3  tabview"  style="display:none; margin-top:30px;">
	<table  class="led01 acct_ledger01  table10">
		<tr  class="headings">
			<td>READING DATE</td>
			<td>METER #</td>
			<td>PERIOD</td>
			<td>PREVIOUS READING</td>
			<td>CURRENT READING</td>
			<td>C.U. METER</td>
		</tr>
		<?php foreach($reading12 as $rr1): ?>
		<tr>
			<td><?php echo $rr1->curr_read_date;  ?></td>
			<td><?php echo $rr1->meter_number;  ?></td>
			<td><?php echo date('F Y',strtotime($rr1->period)); ?></td>
			<td><?php echo $rr1->prev_reading;  ?></td>
			<td><?php echo $rr1->curr_reading;  ?></td>
			<td><?php echo $rr1->current_consump;  ?></td>
		</tr>
		<?php endforeach; ?>
	</table>	
	
	<div style="text-align:right;padding-top:30px;padding-bottom:50px;">
		<a href="/billing/account_ledger/get_ledger_acct/print_pdf_reading?acct_id=<?php echo $acct_id; ?>"  target="_blank">Print Reading Ledger</a>
	</div>	
	
</div>




<div  class="tab1  tabview2 tabview"  style="display:none; margin-top:30px;">

	<table  class="acct_ledger01">
		
		<tr>
			<td width="20%">Date</td>
			<td width="40%">Description</td>
<!--
			<td width="10%">Type</td>
-->
			<td width="40%">Information</td>
		</tr>

		<?php  foreach($ledger_list1 as $acct): ?>
			<tr>
				<td><?php echo date('F d, Y @ H:iA', strtotime($acct->led_date2)); ?></td>
				<td>
					<p><?php echo $acct->led_title; ?></p>
				</td>
<!--
				<td><?php echo $acct->ctyp1; ?></td>
-->
				<td>
					
					<?php echo $acct->led_desc1; ?>
					
						<?php /*if($acct->ctyp1 == 'billing1'): ?>
							<ul>
								<li>Billing total : 52.55</li>
								<li>Arear : 52.55</li>
								<li>Billing total : 52.55</li>
							</ul>
						<?php elseif($acct->ctyp1 == 'collection1'): ?>
							<ul>
								<li>Payed amount : 945.90</li>
								<li>Remaining balance : 52.55</li>
							</ul>
						<?php else: ?>

						<?php  endif; */?>
				</td>
			</tr>
		<?php endforeach;  ?>

	</table>
	
	<div style="text-align:right;padding-top:30px;padding-bottom:50px;">
		<a href="/billing/account_ledger/get_ledger_acct/print_pdf_history?acct_id=<?php echo $acct_id; ?>"  target="_blank">Print History</a>
	</div>	


</div>


<div class="tab1 tabview1  tabview">
	<br />

	<table  class="led01 acct_ledger01  table10">
		
		<tr  class="headings">
			<td class="r1">DATE</td>
			<td class="r2">PARTICULAR</td>
			<td class="r3">REFF</td>
			<td class="r4">PRV</td>
			<td class="r5">CUR</td>
			<td class="r6">C.u.M</td>
			<td class="r7">DEBIT</td>
			<td class="r8">CREDIT</td>
			<td class="r9">BALANCE</td>
		</tr>
		
	</table>
	
	<div class="scrol1111">
		<table  class="led01 acct_ledger01  table10"  style="margin-top: -25px !important;width:100%;">
			<tr  class="headings" style="visibility:hidden;">		
				<td class="r1">DATE</td>
				<td class="r2">PARTICULAR</td>
				<td class="r3">REFF</td>
				<td class="r4">PRV</td>
				<td class="r5">CUR</td>
				<td class="r6">C.u.M</td>
				<td class="r7">DEBIT</td>
				<td class="r8">CREDIT</td>
				<td class="r9">BALANCE</td>
			</tr>


			<?php  
			
			
			$vv1 = 0;
			
			foreach($led001 as $ll): 
			
				
				$ttl1 = $ll->ttl_bal;
				
				$bill_num = '';

				if($ll->led_type == 'billing'){
					$bill_num = get_billing_number($ll->reff_no);					
				}
				
				if($ll->led_type == 'billing'  && $ll->discount > 0  ){
					//~ $ttl1  = $ll->billing; 
				}
				
				
				$reading_info = array(
					'prev3' =>'',
					'curr3' =>$ll->reading,
					'cons3' => $ll->consump
				);
				
				if($ll->led_type == 'billing'){
					$rr3 = getReadingByPeriod($ll->acct_id, $ll->period);
					$reading_info['prev3']= @$rr3->prev_reading;
				}
				
				
				$A_DATE    = '';
				$A_PAR     = part_name1($ll->led_type);
				$A_REFF    = $ll->reff_no.'<br />'.$bill_num;
				$A_PERIOD  = '';
				$A_PREV    = $reading_info['prev3'];
				$A_CUR     = $reading_info['curr3'];
				$A_CON     = $reading_info['cons3'];
				$A_BILL    = '';
				$A_ARR 	   = '';
				$A_DIS     = '';
				$A_PEN     = '';
				$A_ADJ     = '';
				$A_PAY     = '';
				$A_BAL     = '';
				
				
				if($ll->led_type == 'penalty')
				{
					//~ $A_DATE = date('m/d/Y', strtotime($ll->created_at));
					$A_DATE = date('m/d/Y', strtotime($ll->date01)); 
				}else{
					$A_DATE = date('m/d/Y', strtotime($ll->date01)); 
				}
				
				
				if($ll->led_type == 'billing'){
					$A_PERIOD.= date('Y/m/d', strtotime(@$ll->date01.' -1 month' )); 
					$A_PERIOD.= ' - ';
					$A_PERIOD.= date('Y/m/d', strtotime(@$ll->date01)); 
				}
				
				if(!empty($ll->billing)){
					$A_BILL  .= number_format($ll->billing, 2); 
				}

				if(!empty($ll->arrear)){
					$A_ARR = number_format($ll->arrear, 2); 
				}
				
				if(!empty($ll->penalty)){
					$A_PEN = number_format($ll->penalty, 2); 
				}
				
				$A_ADJD = '';
				$A_ADJC = '';
				
				if(!empty($ll->bill_adj))
				//~ if($ll->led_type=='adjustment')
				{
					//$A_ADJ = number_format($ll->bill_adj, 2);
					if($ll->led_type != 'billing'){
						if($ll->bill_adj <= 0){$A_ADJD = number_format(abs($ll->bill_adj), 2);}
						else{$A_ADJC = number_format(abs($ll->bill_adj), 2);}
					}
				}
				
				
				$A_PAY_D = '';
				$A_PAY_C = '';
				
				if(!empty($ll->payment)){
					
					if($ll->payment <= 0)
					{
						$A_PAY_D = number_format(abs($ll->payment), 2);
					}else{
						$A_PAY_C = number_format(abs($ll->payment), 2);
					}
					//~ $A_PAY = number_format($ll->payment, 2);
				}
				
				if(!empty($ll->discount)){
					$A_DIS = number_format($ll->discount, 2); 
				}
				
				if(!empty($ttl1)){
					$A_BAL =  number_format($ttl1, 2); 
				}



				//For Senior
				$is_sen = stripos($ll->ledger_info, 'senior');
				
				if($is_sen !== false &&  $ll->led_type=='adjustment')
				{
					$A_DIS = $A_ADJ;
					$A_ADJ = '';
					$A_PAR .= 'SENIOR CITIZEN';
				}
				
				
				$A_PAR = strtoupper($A_PAR);
				
				$led_info = strtoupper($ll->ledger_info);
				
				$adj_remark = '';
				
				if($ll->led_type=='adjustment')
				{
					$adj_remark= get_adj_desc($ll->reff_no);
					$adj_remark = strtoupper($adj_remark);
				}
				
				
				
				
			?>
			
			<tr>
				<?php if($ll->led_type=='billing'){ ?>
						<td><span ondblclick="cancel_this_billing(<?php echo $vv1; ?>, <?php echo $ll->id; ?>)"><?php echo $A_DATE; ?></span></td>
				<?php }elseif($ll->led_type=='penalty'){ ?>
						<td><span ondblclick="cancel_this_penalty(<?php echo $vv1; ?>, <?php echo $ll->id; ?>)"><?php echo $A_DATE; ?></span></td>
				<?php }elseif($ll->led_type=='adjustment'){ ?>
						<td><span ondblclick="cancel_this_adjustment(<?php echo $vv1; ?>, <?php echo $ll->id; ?>)"><?php echo $A_DATE; ?></span></td>
				<?php }else{ ?>
						<td><span><?php echo $A_DATE; ?></span></td>
				<?php } ?>
				<td>
					<p>
						<?php echo !empty($A_PAR)?$A_PAR.'<br />':''; ?>
						<?php echo !empty($A_PERIOD)?$A_PERIOD.'<br />':''; ?>
						<?php echo !empty($led_info)?$led_info.'<br />':''; ?>
						<?php echo !empty($adj_remark)?$adj_remark.'<br />':''; ?>

					</p>
				</td>
				<td class="<?php echo $ll->id; ?>"><?php echo $A_REFF; ?></td>
				<td><?php   echo $A_PREV; ?></td>
				<td><?php   echo $A_CUR; ?></td>
				<td><?php   echo $A_CON; ?></td>
				<td>
					<?php   echo $A_BILL; ?>
					<?php  echo $A_PEN; ?>
					<?php  echo $A_ADJD; ?>
					<?php  echo $A_PAY_D; ?>
					
				</td>
				<td>
					<?php  echo $A_ADJC; ?>
					<?php   echo $A_PAY_C; ?>
					<?php   echo $A_DIS; ?>
				</td>
				<td><?php    echo $A_BAL; ?></td>
			</tr>			
			
			<?php 
			
				$vv1++;
			
			endforeach; ?>
			
			<tr>
				<td colspan="9">--- END ----</td>
			</tr>
			
			
			
			
		</table>
		
		<input type="hidden"  class="last_val1"  value="<?php echo $vv1-1; ?>"  />
		
	</div>
	
	
	

	
	
	
	
	<div style="text-align:right;padding-top:30px;padding-bottom:50px;"  class="cmds001">
		<div class="cmd_buts">
			<a href="/billing/account_ledger/view_ledger_account_info_pdf?acct_id=<?php echo $account_info->id; ?>" target="_blank">
				<button>Print Ledger</button>
			</a>
			<button onclick="trig1_v2('add_adjustment_form1_pop');">Add Adjustment</button>
			
			<button onclick="trig1_v2('edit_beginning_bal001_pop');">Add Edit Beginning Balance</button>

			<button onclick="refresh_ledger_101()">Refresh Ledger</button>
			{{-- <button onclick="trig1_v2('bbb1');">Add NW-Billing</button> --}}

<?php 
			/*
			
			<?php
			
			/*
			<button onclick="recalculate_ledger_V2()">Recalculate Ledger</button>
			*/  ?>
		</div>
		<div class="please_wait" style="display:none;">
			Please Wait.......
		</div>
	</div>	
	

<?php //$beginning ?>

	
<input type="hidden" value="<?php echo (float) @$beginning->ttl_bal; ?>"  class="beg_amount" />	
<input type="hidden" value="<?php echo @$beginning->period; ?>"  class="beg_period"  />
<input type="hidden" value="<?php echo @$beginning->acct_id; ?>"  class="beg_acct_id"  />
<input type="hidden" value="<?php echo @$_SESSION["my_csrf"]; ?>"  class="my_csrf"  />

</div>


<?php 

$billing_to_adjusts = get_billings_top_10($account_info->id);

?>

<div class="add_adjustment_form1_pop" style="display:none;">
	<div  class="add_adjustment_form1_cont" >
		<div class="add_billing_adjustment_cont">
			<h2>Add Billing Adjustment</h2>
			<select class="type_adjust form-control">
				<option value="credit">Credit</option>
				<option value="debit">Debit</option>
			</select>
			<br>
			<select class="bill_to_adjust form-control">
				<?php foreach($billing_to_adjusts as $vv): ?>
				<option value="<?php echo $vv->id; ?>">
					<?php 
						if($vv->led_type != 'penalty') 
						{
							echo $vv->ledger_info; 
						}else{
							echo $vv->ledger_info.' - '.date('F Y',strtotime($vv->period)); 
						}
					?>
				</option>
				<?php endforeach; ?>
			</select>

			<br>
			<input type="number" value="" class="form-control bill_adjustment_amount" min="0" placeholder="Amount">
			<br>
			<textarea class="form-control bill_ajustment_note"></textarea>
			<br>

			<div class="please_wait" style="display:none;">
				Please Wait.......
			</div>
			<div class="cmd_buts">
				<button onclick="add_bill_adjustment_save_V2()">Save</button>
				<button onclick="view_acct_ledger(curr_index)">Cancel</button>
			</div>			

			
		</div>
	
	</div>
</div>



<div class="edit_beginning_bal001_pop" style="display:none;">
	<div  class="edit_beginning_bal001_cont" >
		<div class="add_billing_adjustment_cont">
			
			<h2>Edit Beginning balance</h2>
			<div>
				<input type="number" value="" class="form-control beg_bal1_amt" 
					   min="0" placeholder="Beginning Balance">
				
				<input type="text"   class="form-control  beg_bal1_prd"  placeholder="Date"  />
				<?php 
				/*
				<select class="form-control  beg_bal1_prd">
					<?php for($x=0;$x<=10;$x++){ 
							
							$my_dat_dd = date('Y-m-01',strtotime(date('Y-m-d').' - '.$x.' Month'));
							$my_dat_rr = date('F Y',strtotime(date('Y-m-d').' - '.$x.' Month'));
							
						?>
						<option value="<?php echo $my_dat_dd; ?>"><?php echo $my_dat_rr; ?></option>
					<?php } ?>
				</select>
				*/ ?>
				<br>
				
				<div class="please_wait" style="display:none;">
					Please Wait.......
				</div>
				<div class="cmd_buts">
					<button onclick="edit_add_beginning_bal_V2()">Save</button>
					<button onclick="view_acct_ledger(curr_index)">Cancel</button>
				</div>			
								
				
				
			</div>
				

		</div>
	</div>
</div>


<?php 

$other_p = NWBill::get_other_payable();
$other_p = $other_p->toArray();

?>
<div class="bbb1" style="display:none;">

<form onsubmit="return false;" class="form_paya01">	
	<div style="padding:15px;padding-top:50px">
		<input type="hidden" class="form-control acct_id" value="0"  name="acct_id" />
		
		<small>Other Payables</small>
		<select class="form-control other_paya_sel33"  onchange="change_other_payable()"   name="other_paya_id">
			<option value="">Select</option>
			<?php foreach($other_p as $k => $v): ?>
			<option value="<?php echo $v['id']; ?>"><?php echo $v['paya_title']; ?> - <?php echo number_format($v['paya_amount'], 2); ?></option>
			<?php endforeach;  ?>
		</select>
		<small>Names</small>
		<input type="text" class="form-control other_paya_name" placeholder="Description"   name="paya_name" />
		<textarea placeholder="remarks" class="form-control other_paya_desc"  name="paya_desc"></textarea>

		<small>Total Amount</small>
		<input type="text" class="form-control other_paya_amt" placeholder="Total Amount"  name="paya_amount" />

		<small>Monthly billing amount</small>
		<input type="text" class="form-control other_paya_bill_amt" placeholder="0.00"  name="paya_per_bill" />

		<small>Billing Start</small>
		<input type="date" class="form-control other_paya_date_start" placeholder="Billing Start"  name="paya_date_start" />
		
		<small>Accounting Code</small>
		<input type="text" class="form-control other_paya_code" placeholder="------"  name="paya_code" />
		<br />
		<br />
		<button class="form-controlx btn btn-small" onclick="other_payable_save()">SAVE</button>
	</div>
</form>
</div>



<script>
var other_paya = <?php echo json_encode($other_p); ?>;

jQuery(document).ready(function() {

});

function add_new_nw_billing()
{
	// trig1_v2('bbb1');
}//

function other_payable_save()
{
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}

	$data1 = jQuery('.pop101 .form_paya01').serializeArray();
	$data1.push({name:'_token', value:csrf_key});	

	POST_JS_v2('/billing/nwb_add_new',$data1, function($rs){

		if( $rs.split('ERROR').length > 1 ) {
			alert($rs);
			return;
		}

		alert('Added..');
		pop_close();

	});


}//

function change_other_payable()
{
	// console.log(current_req_account);
	// form_paya01
	
	// let conf1 = confirm('Please confirm action.');
	// if(!conf1){return;}

	var vl1 = jQuery('.pop101 .other_paya_sel33').val();//

	other_paya.map((v,i)=>{

		if(v.id == vl1) {
			jQuery('.pop101 .other_paya_name').val(v.paya_title);
			jQuery('.pop101 .other_paya_desc').val(v.paya_title);
			jQuery('.pop101 .other_paya_amt').val(v.paya_amount);
			jQuery('.pop101 .other_paya_code').val(v.glsl_code);
			jQuery('.pop101 .acct_id').val(current_req_account.id);

			return false;
		}//

	});

}//



function get_nwb_list() {

	GET_JS_v2('/billing/nwb_get_list?acct_id='+current_req_account.id, function($ret) {
		jQuery('.pop101 .nwb_billing_cont').html($ret);
	});
}//

function delete_nw_bill(nw_bill_id)
{
	let conf1 = confirm('Please confirm DELETE action.');
	if(!conf1){return;}

	GET_JS_v2('/billing/nwb_delete?nw_id='+nw_bill_id, function($rs) {

		if( $rs.split('ERROR').length > 1 ) {
			alert($rs);
			return;
		}

		alert($rs);
		get_nwb_list();
	});
}//

function set_nw_bill_active(nw_bill_id)
{
	let conf1 = confirm('Please confirm SET-ACTIVE action.');
	if(!conf1){return;}

	GET_JS_v2('/billing/nwb_set_active?nw_id='+nw_bill_id, function($rs) {

		if( $rs.split('ERROR').length > 1 ) {
			alert($rs);
			return;
		}

		alert($rs);
		get_nwb_list();
	});

}


function save_beginning_balance()
{
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}

	$data1 = jQuery('.pop101 .begin_bal_form01').serializeArray();
	$data1.push({name:'_token', value:csrf_key});	
	$data1.push({name: 'acct_id', value:current_req_account.id});	

	POST_JS_v2('/billing/save_beginning_balance',$data1, function($rs){

		 if( $rs.split('ERROR').length > 1 ) {
		 	alert($rs);
		 	return;
		 }

		alert($rs);
		view_ledger101();
		// pop_close();

	});


}


</script>

<style>
.rox > div {
	border:0;
}	
</style>