<div style="float:right;width:300px;"   class="date004 date22">
	<?php  report_date_filter(); ?>
	
<!--
     <small>Date:</small> <input type="text" class="form-control  acknow_full_date" data-toggle="datepicker"  placeholder="Please Choose Date" value="<?php echo date('Y-m-d'); ?>">
-->
</div>
<h1>Acknowledgement Report</h1>
<select class="acknow_zone1 form-control "  style="width:300px;">
     <option value="">Please Select Zone</option>
     <?php foreach($zone1 as $zz): ?>
     <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
     <?php endforeach; ?>
</select>

<button onclick="generate_acknowledgement_report()" class="form-control ad_but1">Generate Report</button>

<br />
<br />
<br />

<div style="text-align:center;display:none;" class="loading1_acknow">
     <br />
     <br />
     <img src="/ajax-loader.gif" />
     <br />
     <br />
     Please Wait..
     <br />
</div>

<div class="acknow_result">
</div>

<div style="display:none;">
</div>

<style>
.ad_but1{
     width: 230px !important;
    background: #108479;
    color: white;
}
.txt_r{text-align: right !important;}
</style>

<script>
function generate_acknowledgement_report()
{

     let z1 = jQuery('.acknow_zone1').val();
     if(!z1){return;}

     let delinq_date = jQuery('.acknow_full_date').val();
     
    let mm1 =  jQuery('.date004 .mm_x_month').val();
    let mm2 =  jQuery('.date004 .mm_x_year').val();     
    delinq_date = mm2+'-'+mm1+'-28';     

	///billing/report_get_report_acknowledgement_pdf/4/2018-10-28
     let url1 = '/billing/report_get_report_acknowledgement_pdf/'+z1+'/'+delinq_date;
    window.open(url1, '_blank');         

     //~ jQuery('.acknow_result').html('');
     //~ jQuery('.loading1_acknow').show();

     //~ let url1 = '/billing/report_get_report_acknowledgement/'+z1+'/'+delinq_date;

     //~ jQuery.get( url1, function( data ) {
          //~ jQuery('.loading1_acknow').hide();
          //~ jQuery('.acknow_result').html(data);
	//~ });


}
</script>
