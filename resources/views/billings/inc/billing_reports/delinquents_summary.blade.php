<div style="float:right;width:300px;"   class="date003 date22">
	<?php  report_date_filter(); ?>
	<!--
	 <small>Date:</small> <input type="text" class="form-control  delinque_full_date" data-toggle="datepicker"  placeholder="Please Choose Date" value="<?php echo date('Y-m-d'); ?>">
	-->
</div>

<h1>Summary of Delinquents</h1>

<!--
<select class="delinq_zone1 form-control "  style="width:300px;">
     <option value="">Please Select Zone</option>
     <?php foreach($zone1 as $zz): ?>
     <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
     <?php endforeach; ?>
</select>
-->

<button onclick="generate_delenquent_reports()" class="form-control ad_but1">Generate Delinquents Report</button>

<br />
<br />
<br />

<div style="text-align:center;display:none;" class="loading1_delinq">
     <br />
     <br />
     <img src="/ajax-loader.gif" />
     <br />
     <br />
     Please Wait..
     <br />
</div>
<div class="delinq_result">
</div>

<div style="display:none;">
</div>

<style>
.ad_but1{
     width: 230px !important;
    background: #108479;
    color: white;
}
</style>

<script>
function generate_delenquent_reports()
{
     //~ let z1 = jQuery('.delinq_zone1').val();
     //~ if(!z1){return;}

    let delinq_date = jQuery('.delinque_full_date').val();

    let mm1 =  jQuery('.date003 .mm_x_month').val();
    let mm2 =  jQuery('.date003 .mm_x_year').val();     
    delinq_date = mm2+'-'+mm1+'-28';
         
	
	$url1 = '/billing/report_get_delinquent_summary_pdf/0/'+delinq_date;
    window.open($url1, '_blank');         	

     //~ jQuery('.delinq_result').html('');
     //~ jQuery('.loading1_delinq').show();

     //~ let url1 = '/billing/report_get_delinquent_summary/'+z1+'/'+delinq_date;

     //~ jQuery.get( url1, function( data ) {
          //~ jQuery('.loading1_delinq').hide();
          //~ jQuery('.delinq_result').html(data);
	//~ });




}
</script>
