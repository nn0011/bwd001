<?php



//$acct_reading['data'] = $billing_res['data'];
$acct_reading['data'] = $billing_res['data'];

/*
echo '<pre>';
print_r($zone_label);
echo '</pre>';
/**/
?>
<div style="float:right;display:inline-block;color:red;font-weight: bold;">Current Date :  <?php  echo date('F d, Y');  ?></div>
<br />
<?php /**/ ?>
@include('billings.inc.billing_billing.reading_inc.acct_filter1') 

<h2>Period :  <span class="rd"><?php echo date('F Y', strtotime($r_year.'-'.$r_month)); ?></span></h2>

<!------>
<!------>

<table class="table10 table-bordered  table-hover"><tbody>
	
	<tr class="headings">
		
<!--
		<td width="5%">ID.</td>
-->
		<td width="5%">Bill No.</td>
		<td width="10%">Account No.</td>
		<td width="20%">Name</td>
		<td width="5%"  style="text-align:left;">Zone</td>
		<td width="5%"  style="text-align:left;">Penalty Date</td>
		<td width="5%"  style="text-align:left;">Type </td>
		<td width="3%"  style="text-align:right;">Previous </td>
		<td width="3%"  style="text-align:right;">Current </td>
		<td width="3%"  style="text-align:right;">C.U.</td>
		<td width="7%"  style="text-align:right;">Current bill</td>
		<td width="5%"  style="text-align:right;">Discounts</td>
		<td width="7%"  style="text-align:right;">Arrear</td>
		<td width="7%"  style="text-align:right;">Others</td>
		<td width="7%"  style="text-align:right;">Total Balance</td>
<!--
		<td width="7%"  style="text-align:right;">Ledger Balance</td>
		<td width="7%"  style="text-align:right;">Diff</td>
-->
	</tr>
	
	<?php //for($x=0;$x<=30;$x++): ?>
	
	<?php if(empty($acct_reading['data'])): ?>
		<tr>
			<td colspan="10" style="padding:15px;text-align:center;">
				<span style="font-size:18px;font-weight:bold;">No Result</span>
			</td>
		</tr>
	<?php endif; ?>
	
	<?php 
	

	$index1 = 0;
	foreach($acct_reading['data'] as $acct1): 
	
			extract($acct1);
			
			//~ echo '<pre>';
			//~ print_r($acct1['bill1']);
			//~ die();
			
			$read1 = @explode('||', $bill1['read_PC']);
			$prev_read = @$read1[0];
			$curr_read = @$read1[1];
			$consump = @$bill1['consumption'];
			$arrear33_nwb = (float) @$arrears3['nwb'];
			$arrear33 = (float) @$arrears3['amount'] - $arrear33_nwb;
			$_ledger_bal = (float) @$ledger_data4['ttl_bal'];
			
			$bill_no = @$acct1['bill1']['bill_num_01'];
			$bill_id = @$acct1['bill1']['id'];
			
			$penalty_date1 = @$acct1['bill1']['penalty_date'];
			
			if(empty(trim($penalty_date1))){
				$penalty_date1 = 'NONE';
			}else{
				$penalty_date1 = date('M d', strtotime($penalty_date1));
			}
			
			
			
	?>
	<!------>
	<!------>
	<tr data-index="<?php echo $index1; ?>" data-box1="" class="cursor1  rowx<?php  echo $index1;  ?>  ">
<!--
		<td><?php echo @$id; ?></td>
-->
		<td><?php echo empty(@$bill_no)?@$bill_id:@$bill_no; ?></td>
		<td onclick="view_billing_info(<?php echo $index1; ?>);" ><?php echo @$acct_no; ?></td>
		<td onclick="view_billing_info(<?php echo $index1; ?>);" >
				<?php echo $lname; ?>, <?php echo $fname; ?> <?php echo $mi; ?>
		</td>
		<td><span class="rd"><?php echo $zone_label[$zone_id]; ?></span></td>
		<td><span class="rd"><?php echo $penalty_date1; ?></span></td>
		
		<td><span class="rd"><?php echo ctype_str($acct_type_key); ?></span></td>
		
		
		 <td style="text-align:right;"   class="prev_read_el">
			 <?php echo $prev_read;?>
		 </td>
		 
		 
		<td  style="text-align:right;">
				<span class=""><?php echo $curr_read;  ?></span>
		</td>
		<td  style="text-align:right;">
			<span class="consump">
				<?php  echo $consump; ?>  
			</span>
		</td>
		<td style="text-align:right;">
				<span style="font-size:9px;"></span> <?php  echo  number_format($bill1['billing_total'], 2); ?>
		</td>
		<td style="text-align:right;">
				 <span style="font-size:9px;"></span> <?php echo  number_format(abs($bill1['discount']), 2) ?>
		</td>
		<td style="text-align:right;">
				<span style="font-size:9px;"></span> <?php  echo  number_format($arrear33 , 2); ?>
		</td>		


		<td style="text-align:right;">
				<span style="font-size:9px;"></span> 
				<?php 

					$nwbills = (array) @$bill1['nw_bill'];
					$nwbills_ttl1 = 0;
					foreach($nwbills as $k => $v) {
						$nwbills_ttl1 += $v['amt_1'];
					}

					echo  number_format($nwbills_ttl1 + $arrear33_nwb, 2); 

				?>
		</td>
		
			<?php 
				//$total_balance = ($bill1['arrears'] + $bill1['penalty'] + $bill1['billing_total']) - $bill1['discount'];
				$total_balance = ( $bill1['billing_total']+$arrear33) - $bill1['discount']  + $nwbills_ttl1 + $arrear33_nwb;
			?>		

			

		<td style="text-align:right;font-weight:bold;color:red;<?php echo $total_balance<0?'background:yellow;':''; ?>">
				<span style="font-size:9px;"></span> <?php  echo  number_format($total_balance, 2); ?>
		</td>		
		
		
		<?php 
		
		/*
		<td style="text-align:right;"><?php  echo  number_format($_ledger_bal, 2); ?></td>	
		<?php 
		
		$ttt = $_ledger_bal-$total_balance;
		$rut= '';
		if($ttt != 0){
			$rut = 'background:yellow;';
		}
		?>	
		<td style="text-align:right;<?php echo $rut; ?>"><?php  echo  number_format($_ledger_bal-$total_balance, 2); ?></td>		
		*/?>
	</tr>
	<!------>
	<!------>
	<?php $index1++; endforeach; ?>
								
</tbody></table>

<br />
<br />
<?php  echo  $billing_res['current_page'].' of '.$billing_res['last_page'];  ?>
<br />
<br />

<div style="padding:15px;">
	<ul class="pagination pagination-sm">
	  <?php if(!empty($billing_res['prev_page_url'])): ?>
	  <li  style="margin-right:50px;"><a href="<?php echo $billing_res['prev_page_url']; ?>#accounts">PREVIOUS</a></li>
	  <?php endif; ?>
	  <?php if(!empty($billing_res['next_page_url'])): ?>
	  <li><a href="<?php echo $billing_res['next_page_url']; ?>#accounts">NEXT</a></li>
	  <?php endif; ?>
	</ul>
</div>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="billing_info_01" style="display:none;">
	
	<div style="padding:15px;">
		
		<h2 class="acct_name"></h2>		
		<p class="acct_addr"></p>		
		
		<ul class="item_list1">
			<li>Account Number   <span class="acct_no"></span></li>
			<li>Metter Number   <span class="meter_no"></span></li>
			<li>Bill Number &nbsp;&nbsp;<small class="save_bill_num" onclick="save_bill_number()">Save Bill</small>   <span class="bill_no"><input type="text" placeholder="---" /></span></li>
			<li>Penalty Date &nbsp;&nbsp;<small class="save_bill_num" onclick="save_penalty_date()">Save Penalty Date</small>   <span class="penalty_date"><input type="text" placeholder="---" /></span></li>
<!--
			<li>Zone  <span class="acct_zone"></span></li>
			<li>Account Type   <span class="acct_type"></span></li>
			<li>Discount Type   <span class="disc_type"></span></li>
-->
		</ul>

<!--
		<br />
		<br />
		
		<ul class="item_list1">
				<li>Bill #	   <span class="bill_number"></span></li>
				<li>Current bill	   <span class="current_bill"></span></li>
				<li>Discounts   <span class="discount"></span></li>
				<li>Penalty   <span class="penalty"></span></li>
				<li>Arrear   <span class="arrear"></span></li>
				<li>Total Balance   <span class="total_balance"></span></li>
		</ul>
-->
		
		<br />
		<br />
		
		<div class="loading_gif1">
			<img src="/hwd1/img/ajax-loader.gif" />
		</div>
		
		<div class="cmd_buttons1">
<!--
			<button onclick="print_simple_bill()">Print Simple Bill</button>
-->
			<button onclick="goto_reading()">Goto Reading</button>
			<button onclick="goto_account()">Goto Account</button>
			<button onclick="print_bill_now()">Print Bill</button>
			<button onclick="print_bill_now_gov()">Print Bill (GOV) </button>
<!--
			<button onclick="goto_ledger()">Ledger</button>
-->
<!--
			<button onclick="reprocess_bill_1101()">Reprocess Bill</button>			
-->
			
<!--
			<button onclick="change_acct_type()">Change Account Type</button>
-->
<!--
			<button onclick="fix_via_ledger()">Fix Via Ledger</button>
-->
			<?php /*<button onclick="reprocess_bill_1101()">Reprocess Bill</button>*/ ?>
			<br />
			<br />
			<br />
			<button onclick="pop_close()">Close</button>
		</div>


		
	</div>
</div>	
<!--------------------------------- --->
<!--------------------------------- --->

<div class="billing_info_02" style="display:none;">
	<div style="padding:15px;">
		<h2 class="acct_name"></h2>		
		<p class="acct_addr"></p>
		
		<div>
			
			<small>Account Type: </small> <br />
			<select  name="acct_type"  class="form-control  acct_type">
				<?php foreach($acct_type as $att): ?>
				<option  value="<?php echo $att['id'] ?>"><?php echo $att['meta_desc'] ?></option>
				<?php endforeach; ?>
			</select>		
		
			<div class="cmd_buttons1">
				<button onclick="update_and_process_billing()">Update and Process</button>
				<button onclick="pop_close()">Close</button>
			</div>
			
		</div>
		
	</div>		
</div>


<pre>
	<?php 
	
	//~ print_r($acct_type);
	
	?>
</pre>


<script>


jQuery(document).ready(function(){
	jQuery('.gg1').keydown(function( event ) {
		//~ console.log(event.keyCode);
		if(event.keyCode == 13){
			go_reading_filter();
			return;
		}
	});
});	
	

	
	var  billing_res = <?php echo json_encode($acct_reading['data']); ?>;
	var current_bb = {};
	
	function goto_ledger()
	{
		let gg = '/billing/account_ledger/filter/'+current_bb.acct_no+'/none/none/none/none/#account_list';
		//window.location = '/billing/account_ledger/filter/'+current_bb.acct_no+'/none/none/none/none/#account_list';
		//~ window.open(gg, '__blank');
		window.location = gg;
	}
	
	function view_billing_info($ind)
	{
		let acct_1 = billing_res[$ind];
		current_bb = acct_1;
		
		trig1_v2('billing_info_01');
		setTimeout(function(){
			jQuery('.pop101 .acct_name').html(acct_1.lname+', '+acct_1.fname+' '+acct_1.mi);
			jQuery('.pop101 .acct_addr').html(acct_1.address1);
			jQuery('.pop101 .acct_no').html(acct_1.acct_no);
			jQuery('.pop101 .meter_no').html(acct_1.meter_number1);
			
			jQuery('.pop101 .current_bill').html(acct_1.bill1.curr_bill);
			jQuery('.pop101 .discount').html(acct_1.bill1.discount);
			jQuery('.pop101 .penalty').html(acct_1.bill1.penalty);
			jQuery('.pop101 .arrear').html(acct_1.bill1.arrears);
			jQuery('.pop101 .total_balance').html(acct_1.bill1.billing_total);
			
			jQuery('.pop101 .bill_no input').val(acct_1.bill1.bill_num_01);
			jQuery('.pop101 .penalty_date input').val(acct_1.bill1.penalty_date);
			
			jQuery('.pop101 .penalty_date input').datepicker({format:'YYYY-mm-dd', autoHide:true});
			
			//~ jQuery('.pop101 .acct_zone').html(acct_1.address1);
			//~ jQuery('.pop101 .acct_type').html(acct_1.address1);
			//~ jQuery('.pop101 .disc_type').html(acct_1.address1);
			
		}, 200);
	}
	
	
	function print_simple_bill()
	{
		
		return;
		return;
		return;
		let confirm11 = confirm('Are you sure to print billing?');
		if(!confirm11){return;}
		
		jQuery('.pop101 .loading_gif1').show();
		jQuery('.pop101 .cmd_buttons1').hide();
		
		
		//~ console.log(current_bb);
		//iframe_001
		//~ let iframe11 = document.createElement('iframe');
		//~ iframe.onload = function() { iframe_after_load(); }; // before setting 'src'
		//~ iframe.src = 'http://localhost/hwd_print/print1.php'; 
		
		jQuery.post( "http://localhost/hwd_print/print1.php", {data:current_bb},function( data ) {
			
				if(data.status == 1){
					alert('Printing done.');
				}
				
				if(data.status == 0){
					alert('Printing failure.');
				}

				jQuery('.pop101 .loading_gif1').hide();
				jQuery('.pop101 .cmd_buttons1').show();				
				
		});
	}
	
	function iframe_after_load()
	{
		alert("Thanks");
	}
	
	function reprocess_bill_1101()
	{
		let confirm1 = confirm('Are you sure to reprocess billing?');
		if(!confirm1){return;}
		window.location='/billing/billing/reprocess_bill/'+current_bb.bill1.id;
	}
	
	function change_acct_type()
	{
		//~ console.log(current_bb.acct_type_key);
		
		trig1_v2('billing_info_02');
		
		setTimeout(function(){
			let acct_1 = current_bb;
			
			//~ jQuery('.pop101 .acct_name').html(acct_1.lname+', '+acct_1.fname+' '+acct_1.mi);
			//~ jQuery('.pop101 .acct_addr').html(acct_1.address1);
			//~ jQuery('.pop101 .acct_no').html(acct_1.acct_no);
			//~ jQuery('.pop101 .meter_no').html(acct_1.meter_number1);
			
			jQuery('.pop101 .acct_type').val(acct_1.acct_type_key).change();
			
			
		}, 200);

		
	}
	
	function update_and_process_billing()
	{
		let confirm11 = confirm('Change account type and reprocess billing?');
		if(!confirm11){return;}		
		
		let act_t = jQuery('.pop101 .acct_type').val();
		let acct_1 = current_bb;
		
		window.location = '/billing/billing/update_acct_type_process/'+act_t+'/'+acct_1.acct_no+'/'+acct_1.id+'/'+acct_1.bill1.id;
		//~ console.log(acct_1);
	}//
	
	function fix_via_ledger()
	{
		let confirm11 = confirm('Change account type and reprocess billing?');
		if(!confirm11){return;}		
		let acct_1 = current_bb;
		
		window.location = '/billing/billing/fix_via_old_ledger/'+acct_1.acct_no+'/'+acct_1.id+'/'+acct_1.bill1.id;
	}
	
	function save_bill_number()
	{
		let confirm11 = confirm('Are you sure to save bill number?');
		if(!confirm11){return;}				
		
		let bb1  = current_bb; 
		let bill_no = jQuery('.pop101 .bill_no input').val();
		
		if(!bill_no){alert('Bill Number Error');return;}
		
		let url1 = '/billing/billing/save_billing_number/'+bb1.bill1.id+'?bill_no='+bill_no
		//~ window.open(url1,'blank');
			
		jQuery.get(url1,function( data ) {

			if(data.status == 0){
				alert(data.msg);
			}else{
				alert(data.msg);
				location.reload();
			}
			
		});		
		
		
	}
	
	function save_penalty_date()
	{		
		let confirm11 = confirm('Are you sure to save penalty date?');
		if(!confirm11){return;}
		
		let bb1  = current_bb; 
		let fine_date = jQuery('.pop101 .penalty_date input').val();		
		if(!fine_date){alert('Penalty date error');return;}
		
		let url1 = '/billing/billing/save_penalty_date/'+bb1.bill1.id+'?pen_date='+fine_date;
		
		jQuery.get(url1,function( data ) {

			if(data.status == 0){
				alert(data.msg);
			}else{
				alert(data.msg);
				location.reload();
			}
			
		});				
		
		//~ window.open(url1,'blank');

	}//
	
	<?php $date_ll = $r_year."/".$r_month; ?>	
	function goto_reading()
	{
		let acct_1 = current_bb;
		let $ttt = "/billing/reading/<?php echo $date_ll; ?>/filter/"+acct_1.acct_no+"/none/none/none/?status=0";
		window.location =  $ttt;
	}
	
	function goto_account()
	{
		let acct_1 = current_bb;
		let url22 = '/billing/accounts/'+acct_1.acct_no+'/none/none/none/none/none/#account_list';
		window.location =  url22	;
	}

	function print_bill_now()
	{
		window.open('/billing/billing/print_bill?bid='+current_bb.bill1.id, 'blank');
	}//

	function print_bill_now_gov()
	{
		window.open('/billing/billing/print_bill?bid='+current_bb.bill1.id+'&typ=2', 'blank');
	}
	

	
</script>

<style>
.pagination>li{
	display:inline-block;
}
.loading_gif1{
	    text-align: center;
	    display:none;
}
.cmd_buttons1{
		text-align:center;
}
.cursor1:hover{
	background:#FFFF00 !important;
}
.penalty_date input,
.bill_no input{
	border: 0;
    background: none;
    text-align: right;	
}

.save_bill_num{
	cursor:pointer;
	color:red;
}
.save_bill_num:hover{
	font-weight:bold;
}
</style>
