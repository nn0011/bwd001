


<div  class="filter_1 acct_list1">
	<input type="text"  placeholder="Account Number"  id="search_acct_num"
	value="<?php  echo @$acct_num=='none'?'':@$acct_num; ?>" />
	<input type="text"  placeholder="Meter #(Last)"
		id="search_meter_num" value="<?php echo @$meter_num == 'none'?'':@$meter_num; ?>" />
	<input type="text"  placeholder="Last Name"
	id="search_last_name"  value="<?php echo @$lname == 'none'?'':@$lname; ?>"  />

	<select  id="search_zone">
		<option value="">ZONE</option>
		 <?php  foreach($zones as $zz):
		 $zz = $zz->toArray();
		 ?>
		 <option value="<?php echo $zz['id']; ?>"><?php echo strtoupper($zz['zone_name']); ?></option>
		 <?php endforeach; ?>
	</select>
	
	
	<select id="acct_status010">
		<option value="">Account Status</option>
		<option value="1">Active</option>
		<option value="2">Disconnected</option>
		<option value="3">No Beginning Balance</option>
	</select>

	<img src="/hwd1/img/search.jpg" class="but_filter"  onclick="acct_ledger_search()"   />
	<br />
	<button onclick="clear_filter_001122()">Clear</button>

</div>


<br />
<br />

<div></div>

<div class="scroll1 acct_ledger_info"  style="display:none;">
	<button onclick="back_to_result()" style="margin-bottom:30px;"  class="button_hide">Back to Result</button>
	<div class="name_info">
		<h3>CUSTOMER LEDGER ACCOUNT</h3>
		NAME : <span  class="full_name"></span>
		<br />
		ADDRESS: <span  class="address"></span>
		<br />
		ACCOUNT NO: <span  class="acct_num"></span>
		<br />
		METER NO: <span  class="meter_num"></span>
		<br />
		STATUS:  <span  class="acct_stat"></span>
		<br />
		CLASSIFICATION: <span  class="acct_class"></span>
		<br />
		<br />
		<br />
	</div>
	<div class="content1"></div>
</div>

<div class="scroll1 acct_ledger_result">
	<table class="table10 table-bordered  table-hover">
		
		<tr  class="headings">
			<td width="10%">Account No.</td>
			<td width="10%">Account ID.</td>
		   <td width="20%">Name</td>
			<td width="20%">Address & Zone</td>
               <td width="10%">Meter No.</td>
			<td width="10%">Account Status</td>
			<td width="10%">BAL</td>
			<td width="10%">DATE</td>
		</tr>

		<?php if($acct_list->count() == 0): ?>
			<tr>
				<td class="empty_no_data" colspan="5">
						No Result
				</td>
			</tr>
		<?php endif; ?>

          <?php
          $index1 = 0;
          foreach($acct_list as $acct): ?>
               <tr class="item_item   <?php if(@$acct->ledger_data4->ttl_bal > 0){echo ' has11 ';}?>">
     			<?php /*<td><a  onclick="view_acct_ledger(<?php echo $index1; ?>)"><?php echo $acct->acct_no; ?></a></td>*/ ?>
     			
     			<td><a  onclick="view_acct_ledger(<?php echo $index1; ?>)"><?php echo $acct->acct_no; ?></a></td>
     			<td><a><?php echo $acct->id; ?></a></td>
				<td><?php echo $acct->lname.', '.$acct->fname.' '.$acct->mi; ?></td>
     			<td>
					 <?php echo $acct->address1; ?><br />
					 <?php echo @$acct->my_zone->zone_name; ?><br />
				</td>
				<td><?php echo $acct->meter_number1; ?></td>
     			<td>
					 <?php echo @$acct->my_stat->meta_name; ?><br />
				</td>
				<td>
					 <span style="<?php if(@$acct->ledger_data4->ttl_bal < 0){/*echo 'color:red;';*/}?>"><?php echo number_format((float) @$acct->ledger_data4->ttl_bal,2); ?></span><br />
				</td>
				<td>
					<?php echo @$acct->ledger_data4->date01; ?>
				</td>
     		</tr>
          <?php $index1++; endforeach; ?>

	</table>


		<?php 
			$accounts = $acct_list->toArray();
		?>
		<div style="padding:15px;">
				<br />
				<?php echo $accounts['current_page']; ?> of <?php echo $accounts['last_page']; ?>
				<br />
				<ul class="pagination pagination-sm">
				  <?php if(!empty($accounts['prev_page_url'])): ?>
				  <li><a href="<?php echo $accounts['prev_page_url'].'#account_list'; ?>">PREVIOUS</a></li>
				  <?php endif; ?>
				  <?php

					/*
				  for($x=1;$x<=$accounts['last_page'];$x++): ?>
				  <li  <?php echo $accounts['current_page']===$x?'  class="active" ':''; ?>><a href="<?php echo '/billing/accounts?page='.$x.'#account_list'; ?>"><?php echo $x; ?></a></li>
				  <?php endfor;
				  */

				   ?>
				  <?php if(!empty($accounts['next_page_url'])): ?>
				  <li><a href="<?php echo $accounts['next_page_url'].'#account_list'; ?>">NEXT</a></li>
				  <?php endif; ?>
				</ul>
		</div>
	
	
	
</div>


<div class="ledger_info001_cont" style="display:none;">
	<div class="ledger_info001">
		
	</div>
</div>


<div class="view_accout_ledger_cont" style="display:none;">

	<div class="pop_view_info_table  view_accout_ledger_body">

		<form action="/billing/reading/update_init_reading?vr=2" method="POST" class="form-style-9  add_meter_number_form1"  onsubmit="return add_initial_reading_form;">
			
			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden" name="acct_id" value=""  id="acct_id">
			<input type="hidden" name="reading_year" value="<?php echo date('Y'); ?>">
			<input type="hidden" name="reading_month" value="<?php echo date('m'); ?>">
			<input type="hidden" name="data1" value=""   id="data1">


			<div class="head_info">
				<h2 class="field1"></h2>
				<p class="field2"></p>
			</div>

			<!-- <ul class="item_list1">
				<li>Account Number   <span  class="field3"></span></li>
				<li>Metter Number   <span  class="field4"></span></li>
				<li>Zone   <span  class="field6"></span></li>
			</ul> -->

			<div class="ledger_list1"  style="height:350px;overflow:scroll;overflow-x: hidden;">
				loading....
			</div>

<?php /*

               <div style="height:350px;overflow:scroll;overflow-x: hidden;">

                    <table>
                    <tr>
                         <td wtidth="20%">Date</td>
                         <td wtidth="80%">Description</td>
                    </tr>


				<?php //foreach($acct_list as $acct): ?>
                    <tr>
                         <td>Jan 21, 2018</td>
                         <td>
                              Reading for October 2018
                              <ul  class="item_list1">
                                   <li>Reading Perid : <span>October 2018</span></li>
                                   <li>Previous Reading :  <span>20</span></li>
                                   <li>Current Reading :  <span>33</span></li>
                                   <li>Consumption :  <span>13</span></li>
                              </ul>
                         </td>
                    </tr>
				<?php //endforeach; ?>

                    <tr>
                         <td>Jan 21, 2018</td>
                         <td>
                              Payment Made
                              <ul  class="item_list1">
                                   <li>Current Bill :  <span>200.00</span></li>
                                   <li>Penalty :  <span>20.00</span></li>
                                   <li>Arrears :  <span>3,642.15</span></li>
                              </ul>
                              <ul   class="item_list1"  style="background:#ffc299;padding:10px;">
                                   <li>Total Balance :  <span>3,862.15</span></li>
                                   <li>Payment Amount : <span>600.00</span></li>
                                   <li>Remaining Balance : <span>3,262.15</span></li>
                              </ul>
                         </td>
                    </tr>



                    <tr>
                         <td>Jan 21, 2018</td>
                         <td>
                              Penalty for Billing September 2018
                              <ul  class="item_list1">
                                   <li>Penalty Amount : <span>20.00</span></li>
                                   <li>Total Balance :  <span>3,862.15</span></li>
                              </ul>
                         </td>
                    </tr>


                    <tr>
                         <td>Jan 21, 2018</td>
                         <td>
                              Billing for September 2018
                              <ul  class="item_list1">
                                   <li>Billing Period : <span>September 2018</span></li>
                                   <li>Current Bill :  <span>200.00</span></li>
                                   <li>Arrears :  <span>3,642.15</span></li>
                                   <li>Total Balance :  <span>3,842.15</span></li>
                              </ul>
                         </td>
                    </tr>

                    <tr>
                         <td>Jan 21, 2018</td>
                         <td>
                              Reading for September 2018
                              <ul  class="item_list1">
                                   <li>Reading Perid : <span>September 2018</span></li>
                                   <li>Previous Reading :  <span>10</span></li>
                                   <li>Current Reading :  <span>20</span></li>
                                   <li>Consumption :  <span>10</span></li>
                              </ul>
                         </td>
                    </tr>


                    <?php for($xx=0;$xx<=20;$xx++): ?>
                    <tr>
                         <td>Jan 21, 2018</td>
                         <td>New application request</td>
                    </tr>
                    <?php endfor; ?>

                    </table>

               </div>

<?php /**/ ?>

			<br />
			<br />


		</form>

	</div>

</div>



<div class="edit_beginning_bal_pop" style="display:none;">
	<div  class="edit_beginning_bal_cont">
		<h2>Edit Beginning balance</h2>
		<div>			
			<input type="number"  value=""  class="form-control beg_bal1_amt"  min="0" placeholder="Beginning Balance" />
			<select class="form-control  beg_bal1_prd">
				<?php for($x=0;$x<=10;$x++): 
					$date1 = date('Y-m-01', strtotime('- '.$x.' Month'));
					$date_read = date('F Y', strtotime('- '.$x.' Month'));
				?>
					<option value="<?php echo $date1; ?>"><?php echo $date_read; ?></option>
				<?php endfor; ?>
			</select>
			<br />
			<button onclick="save_beginning_balace()">Save</button>
		</div>
	</div>
</div>



<div class="add_billing_adjustment_pop"  style="display:none;">
	<div class="add_billing_adjustment_cont">
		<h2>Add Billing Adjustment</h2>
			<input type="number"  value=""  class="form-control bill_adjustment_amount"  min="0" placeholder="Amount" />
			<br />
			<textarea class="form-control bill_ajustment_note"></textarea>
			<br />
			<button onclick="add_bill_adjustment_save()">Save</button>
	</div>
</div>




<div class="view_accout_ledger22_pop" style="display:none;">
	<div class="view_accout_ledger22_cont">
		<div class="con1">
			Please Wait....
		</div>
	</div>
</div>



<style>
.add_billing_adjustment_cont{
	padding:15px;
}	
.edit_beginning_bal_cont{
	padding:15px;
}
	
.acct_ledger01 td{
		border:1px solid #ccc;
		padding:15px;
		vertical-align:top;
		padding-bottom:25px;
}	

.acct_ledger01  .item_list1{
    width: 320px;
    border: 2px solid #ccc;
    padding: 15px;	
}

.name_info  .acct_num{
	
}

.name_info  .full_name{
}

.name_info  .address{
}
.has11{ background:rgba(255,0,0,0.2);}

</style>

<script>
jQuery(document).ready(function(){
	jQuery('#acct_status010').val("<?php echo @$stype; ?>").change();
});	
	
function print_ledger_balace($id){
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	
	window.open('/billing/account_ledger/get_ledger_acct/print_pdf?acct_id='+$id);
}//

function recalculate_ledger($id){
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	window.location = '/billing/account_ledger/get_ledger_acct/recalculate?acct_id='+$id;
}

var curr_acct_id = null;

function add_edit_beginning_balance($id)
{
	
	curr_acct_id = $id;
	
	trig1_v2('edit_beginning_bal_pop');
	setTimeout(function(){
		let amt = jQuery('.beg_amount').val();
		let prd = jQuery('.beg_period').val();
		
		jQuery('.pop101 .beg_bal1_amt').val(amt);
		jQuery('.pop101 .beg_bal1_prd').val(prd);
	}, 200);
}

function save_beginning_balace(){
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	
	let amt = jQuery('.pop101 .beg_bal1_amt').val();
	let prd = jQuery('.pop101 .beg_bal1_prd').val();
	let iid = curr_acct_id;
	
	window.location = '/billing/account_ledger/get_ledger_acct/update_beginning?acct_id='+iid+'&amt='+amt+'&prd='+prd;
}
	
</script>
