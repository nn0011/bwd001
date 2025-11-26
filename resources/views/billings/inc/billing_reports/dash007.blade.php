<h1>Reports</h1>

<br />


<ul>
	<li><a onclick="ageing_report_step1()">Ageing Reports</a></li>
	<li><a onclick="billing_repor_step1()">Billing Reports</a></li>
	<li><a onclick="penalty_report_step1()">Penalty Reports</a></li>
	<li><a onclick="collection_report1();">Collection Reports</a></li>
	<li><a onclick="consessionare_report_step1()">Consessionaire Reports</a></li>
	<li><a onclick="adjustment_report_step1()">Adjustment Report</a></li>
	<li><a onclick="reading_book_step1()">Reading Book</a></li>
	<li><a onclick="disconnection_list_step1()">Disconnection List</a></li>
<!--
	<li><a onclick="">Account Balance</a></li>
-->
<!--
	<li><a onclick="">Summary of Delinquent</a></li>
-->
<!--
	<li><a onclick="">Acknowledgement Report</a></li>
-->
</ul>

<!---- COLLECTION ---->


<!---- CONSESSIONARE ---->
<div class="consess_report_pop1" style="display:none;">
	<div class="consess_report_cont1   cont11" >	
		
		<h3>Consessionare Report</h3>
		<small>DATE</small>
		<br />
		<input type="text" class="date1" />
		
		<br />
		<br />
		
		<small>SELECT ZONE</small>
		<br />
		<select class="zone1 form-control "  style="width:300px;">
			 <option value="">Please Select Zone</option>
			 <?php foreach($zone1 as $zz): ?>
			 <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
			 <?php endforeach; ?>
		</select>
		<br />
		<br />
		
		<button onclick="consessionare_report_step1_disconnected()">DISCONNECTED</button>
		<br />
		<br />
		<button onclick="consessionare_report_step1_reconnected()">RECONNECTED</button>
		<br />
		<br />
		<button onclick="consessionare_report_step1_new_consessionare()">NEW CONSESSIONARE</button>
		<br />
		<br />
		<button onclick="consessionare_report_step1_pending_approval()">PENDING APPROVAL</button>
		<br />
		<br />
		<button onclick="consessionare_report_step1_voluntary_disconnection()">VOLUNTARY DISCONNECTION</button>
		
		
	</div>
</div>


<!---- BILLING ---->
<div class="billing_report_pop1" style="display:none;">
	<div class="billing_report_cont1   cont11" >	
		<h3>Billing Report</h3>
		<small>DATE</small>
		<br />
		<input type="text" class="date1" />
		
		<br />
		<br />
		
		<small>SELECT ZONE</small>
		<br />
		<select class="zone1 form-control "  style="width:300px;">
			 <option value="">Please Select Zone</option>
			 <option value="all">ALL</option>
			 <?php foreach($zone1 as $zz): ?>
			 <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
			 <?php endforeach; ?>
		</select>
		<br />
		<br />
		
		<button onclick="billing_repor_step2()">GENERATE BILLING ACCOUNTS</button>
		<br />
		<br />
		<button onclick="billing_repor_step3()">GENERATE MONTHLY SUMMARY</button>
		<br />
		<br />
		<button onclick="billing_repor_step3_2()">GENERATE MONTHLY SUMMARY ALL ZONES</button>
		<br />
		<br />
		<button onclick="billing_repor_step4()">GENERATE ANNUALLY</button>
		
		
	</div>
</div>

<!---- AGEING ---->
<div class="ageing_report_pop1" style="display:none;">
	<div class="ageing_report_cont1   cont11" >
		<h3>Ageing of account reports</h3>
		
		<small>DATE</small>
		<br />
		<input type="text" class="date1" />
		
		<br />
		<br />
		
		<small>SELECT ZONE</small>
		<br />
		<select class="zone1 form-control "  style="width:300px;">
			 <option value="">Please Select Zone</option>
			 <?php foreach($zone1 as $zz): ?>
			 <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
			 <?php endforeach; ?>
		</select>
		<br />
		<br />
		 <select  class="sel_month1">
			  <option value="m1">30 Days</option>
			  <option value="m2">60 Days</option>
			  <option value="m3">90 Days</option>
			  <option value="m4">120 Days</option>
			  <option value="m5">150 Days</option>
			  <option value="m6">180 Days</option>
			  <option value="y1">360 Days</option>
		 </select>		
		<br />
		<br />
		
		<button onclick="ageing_report_step2()">GENERATE AGEING OF ACCOUNTS</button>
		<br />
		<br />
		<button onclick="ageing_report_step3()">GENERATE AGEING SUMMARY</button>


	</div>
</div>


<!---- PENALTY ---->
<div class="penaly_report_pop1" style="display:none;">
	<div class="penaly_report_cont1   cont11" >
		
		<h3>Penalty Reports</h3>
		
		<small>DATE</small>
		<br />
		<input type="text" class="date1" />
		
		<br />
		<br />
		<small>SELECT ZONE </small>
		<br />
		<select class="zone1 form-control "  style="width:300px;">
			 <option value="">Please Select Zone</option>
			 <option value="all">ALL</option>
			 <?php foreach($zone1 as $zz): ?>
			 <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
			 <?php endforeach; ?>
		</select>
		
		<br />
		
		<br />
		<button onclick="penalty_report_step2()">GENERATE REPORT</button>
		
		<br />
		<br />
		<button onclick="penalty_report_step3()">GENERATE REPORT BY ZONES</button>
		
<!--
		<br />
		<br />
		<button onclick="penalty_report_step2()">GENERATE REPORT BY ACCOUNT TYPE</button>
-->
	</div>
</div>


<!---- COLLECTION ---->
<div class="collection_report_pop" style="display:none;">
	<div class="collection_report_cont   cont11" >
		
		<h3>COLLECTION REPORT</h3>
<!--
		<small>AS OF</small>
		<br />
-->
		<input type="text" class="date1" placeholder="Date..." />
		<br />
		<br />
		
		<div class="coll_rep_result">
			Please Wait...
		</div>

		<hr style="border: 2px solid #000;" />
		<h5 style="font-size: 18px">Generate Auditor's Excel Collection Report</h5>
		
		<small>Start date</small>
		<input type="date" class="form-control aud_start_date" />
		<small>End date</small>
		<input type="date" class="form-control aud_end_date"  />
		<br />
		<br />
		<button class="form-control" onclick="generate_auditor_excel()">Generate Now</button>

		
<!--
		<br />
		<br />
		


		
		<br />
		<button onclick="">GENERATE REPORT</button>
-->
		
	</div>
</div>

<!---- COLLECTION ---->
<div class="adjustment_report_pop" style="display:none;">
	<div class="adjustment_report_cont   cont11" >
		
		<h3>ADJUSTMENT REPORT</h3>
		<br />
		<table style="width:100%;">
			<tr>
				<td>
					<small>Start</small>
					<br />
					<input type="text" class="date1" placeholder="Date start..." />
				</td>
				<td>
					<small>End</small>
					<br />
					<input type="text" class="date2" placeholder="Date end..." />
				</td>
			</tr>
		</table>
		
		<br />

		<button onclick="adjustment_report_step2()">GENERATE REPORT	</button>
		
	</div>
</div>


<div class="reading_book_pop" style="display:none;">
	<div class="reading_book_cont   cont11" >
		
		<h3>Reading Book</h3>
		<br />

		<small>Select Date</small>
		<br />
		<input type="text" class="date1" placeholder="Date start..." />
		<br />
		<small>SELECT ZONE </small>
		<br />
		<select class="zone1 form-control "  style="width:300px;">
			 <option value="">Please Select Zone</option>
			 <?php foreach($zone1 as $zz): ?>
			 <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
			 <?php endforeach; ?>
		</select>		
		<br />

		<button onclick="reading_book_step2()">GENERATE REPORT	</button>
		
	</div>
</div>



<div class="disconnection_list_pop" style="display:none;">
	<div class="disconnection_list_cont   cont11" >
		
		<h3>Disconnection List</h3>
		<br />

		<small>Select Date</small>
		<br />
		<input type="text" class="date1" placeholder="Date start..." />
		<br />
		<small>SELECT ZONE </small>
		<br />
		<select class="zone1 form-control "  style="width:300px;">
			 <option value="">Please Select Zone</option>
			 <?php foreach($zone1 as $zz): ?>
			 <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
			 <?php endforeach; ?>
		</select>		
		<br />

		<button onclick="disconnection_list_step2()">GENERATE DISCONNECTION LIST</button>
		
	</div>
</div>




<script>

function generate_auditor_excel()
{
	let confi1 = confirm("Please Confirm?");
	if( !confi1 ) { return;}
	
	let start1 = jQuery('.pop101 .aud_start_date').val();
	let end1   = jQuery('.pop101 .aud_end_date').val();
	// /billing/reports/generate_audit_excel

	window.open('/billing/reports/generate_audit_excel?ss='+start1+'&ee='+end1, '__blank');
	

}//


/***COMMON START***/
function date_init1($cls){
     setTimeout(function(){
		 
			 jQuery($cls).datepicker({
			   autoHide: true,
			   zIndex: 9999999,
			   format: 'yyyy-mm-dd'
			 });
					 
		 }, 500);     
	
}
/***COMMON START***/


/***AGEING START***/

function ageing_check_fields(){
	let conf1 = confirm('Are you sure to generate report?');
	if(!conf1){return false;}
	
	let date1 = jQuery('.pop101 .date1').val();
	let zone_id = jQuery('.pop101 .zone1').val();
	let sel_month1 = jQuery('.pop101 .sel_month1').val();
	
	if(!date1 || !zone_id)
	{
		alert('Please select Date and Zone');
		return 0;
	}
	return {zone_id:zone_id, date1:date1, sel_month1:sel_month1};
}//	
	
function ageing_report_step1()
{
     trig1_v2('ageing_report_pop1');
     date_init1('.pop101 .date1');
}

function ageing_report_step2()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/report_get_by_zone/pdf/'+d.zone_id+'/'+d.sel_month1+'/'+d.date1;
	window.open(url1,'_blank');
}//

function ageing_report_step3()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/report_account_recievable_summary_pdf/'+d.zone_id+'/'+d.sel_month1+'/'+d.date1;
	window.open(url1,'_blank');	
}
/***AGEING END***/
	
/***PENALTY START***/
function penalty_report_step1(){
     trig1_v2('penaly_report_pop1');
     date_init1('.pop101 .date1');
}

function penalty_report_step2()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	
	let url1 = '/report_penalty_report/'+d.zone_id+'/'+d.date1;
	window.open(url1,'_blank');
	
}//

function penalty_report_step3()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	
	let url1 = '/report_penalty_report_by_zone/'+d.zone_id+'/'+d.date1;
	window.open(url1,'_blank');
}

/***PENALTY END***/

/***BILLING START***/
function billing_repor_step1(){
     trig1_v2('billing_report_pop1');
     date_init1('.pop101 .date1');
}

function billing_repor_step2()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/billing_summary_get_account_pdf/'+d.zone_id+'/'+d.date1;
	window.open(url1,'_blank');
}

function billing_repor_step3()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/billing_summary_get_zone_class_pdf/'+d.date1;
	window.open(url1,'_blank');
}

function billing_repor_step3_2()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/billing_summary_get_zone_class_pdf_all/'+d.date1;
	window.open(url1,'_blank');
}


function billing_repor_step4()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/billing_summary_get_annual_pdf/'+d.date1;
	window.open(url1,'_blank');
}

function consessionare_report_step1()
{
     trig1_v2('consess_report_pop1');
     date_init1('.pop101 .date1');
     setTimeout(function(){
		jQuery('.pop101 .zone1').append('<option value="all">ALL</option>');
	 },500);
}

function consessionare_report_step1_reconnected()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/consessionare_report_step1_reconnected_pdf/'+d.date1+'/'+d.zone_id;
	window.open(url1,'_blank');
}

function consessionare_report_step1_disconnected()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/consessionare_report_step1_disconnected_pdf/'+d.date1+'/'+d.zone_id;
	window.open(url1,'_blank');
}

function consessionare_report_step1_new_consessionare()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/consessionare_report_step1_new_consessionare_pdf/'+d.date1+'/'+d.zone_id;
	window.open(url1,'_blank');
}

function consessionare_report_step1_pending_approval()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/consessionare_report_step1_pending_approval_pdf/'+d.date1+'/'+d.zone_id;
	window.open(url1,'_blank');	
}


function consessionare_report_step1_voluntary_disconnection()
{
	let d = ageing_check_fields();
	if(d == 0){return 0;}
	let url1 = '/billing/consessionare_report_step1_voluntary_disconnection_pdf/'+d.date1+'/'+d.zone_id;
	window.open(url1,'_blank');	
}

function collection_report1()
{
	
    trig1_v2('collection_report_pop');
	jQuery.get('/report_get_collectors',function($res){
		
			date_init1('.pop101 .date1');
			jQuery('.pop101 .coll_rep_result').html($res.usr_html);
			
	 });
	
    //~ setTimeout(function(){
		 //~ date_init1('.pop101 .date1');
	//~ },300);
	  
}//

function daily_collect1()
{
	let coll_id = jQuery('.pop101 .coll_id').val();
	let cur_date = jQuery('.pop101 .date1').val();
	window.open('/collections/all_reports/daily?dd='+cur_date+'&iid='+coll_id,'__blank');
	
}//

/***BILLING END***/


function adjustment_report_step1()
{
     trig1_v2('adjustment_report_pop');
     date_init1('.pop101 .date1,.pop101 .date2');
}

function adjustment_report_step2()
{
	let conf1 = confirm('Are you sure to generate report?');
	if(!conf1){return false;}
	
	let date1 = jQuery('.pop101 .date1').val();
	let date2 = jQuery('.pop101 .date2').val();
	
	window.open('/report_adjustment_report_pdf/'+date1+'/'+date2,'__blank');
}//


function reading_book_step1()
{
     trig1_v2('reading_book_pop');
     date_init1('.pop101 .date1,.pop101 .date2');	
}

function reading_book_step2()
{
	let conf1 = confirm('Are you sure to generate reading book.');
	if(!conf1){return false;}
	
	let date1 = jQuery('.pop101 .date1').val();
	let zone1 = jQuery('.pop101 .zone1').val();	
	window.open('/report_generate_reading_book/'+date1+'/'+zone1,'__blank');

}//

function disconnection_list_step1()
{
     trig1_v2('disconnection_list_pop');
     date_init1('.pop101 .date1,.pop101 .date2');	
}

function disconnection_list_step2()
{
	let conf1 = confirm('Are you sure to generate reading book.');
	if(!conf1){return false;}
	let date1 = jQuery('.pop101 .date1').val();
	let zone1 = jQuery('.pop101 .zone1').val();	
	//~ window.open('/report_generate_reading_book/'+date1+'/'+zone1,'__blank');
	//~ alert('Done');
	window.open('/for_disconnection_list/v2/'+date1+'/'+zone1+'/none?mo=1','__blank');
	
}
</script>



<style>
.cont11{
	padding:30px;
}	
.back_1 .box1{
	margin-top:20px !important;
}
</style>
