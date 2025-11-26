<?php 


?>
<div style="float:right; width:300px;"  class="date001 date22">
<?php  report_date_filter(); ?>
<!--
     <input type="text" class="form-control  ageing_full_date" data-toggle="datepicker"  placeholder="Please Choose Date" value="<?php echo date('Y-m-d'); ?>">
-->
</div>

<h1>Aging of Receivable</h1>

<br />

<div class="zone_mm  filter001">

     <select class="sel_zone1">
          <option value="">Please Select Zone</option>
          <?php foreach($zone1 as $zz): ?>
          <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
          <?php endforeach; ?>
     </select>

     <select  class="sel_month1">
          <option value="m1">30 Days</option>
          <option value="m2">60 Days</option>
          <option value="m3">90 Days</option>
          <option value="m4">120 Days</option>
          <option value="m5">150 Days</option>
          <option value="m6">180 Days</option>
          <option value="y1">360 Days</option>
     </select>
     

     <button onclick="generate_report()"> Generate Report</button>
     &nbsp;&nbsp;&nbsp;
     <button onclick="generate_account_recievable_summary()"> Generate Summary Report</button>

</div>
<br />
<br />

<div style="text-align:center;display:none;" class="loading1">
     <br />
     <br />
     <img src="/ajax-loader.gif" />
     <br />
     <br />
     Please Wait..
     <br />
</div>
<div class="ageing_result">
</div>

<style>
.currency{
     text-align:right !important;
}
.zone_mm select{
     padding:3px;
}
.t_right{
     text-align:right !important;
}
.ageing_sub_total1{
     font-weight:bold;
}
.ageing_sub_total1 td{
     padding-bottom:15px !important;
     padding-top:15px  !important;
}
.date22  select{
	width:48%;
	width:auto;
}
</style>

<script>
var age_curr_month = <?php echo (int) date('m'); ?>;
jQuery(document).ready(function(){

     jQuery('[data-toggle="datepicker"]').datepicker({
       autoHide: true,
       zIndex: 2048,
       format: 'yyyy-mm-dd'
     });
     
	jQuery('.mm_x_day').val(<?php echo date('d'); ?>);

});


function generate_report()
{
     let z1 = jQuery('.sel_zone1').val();
     let m1 = jQuery('.sel_month1').val();

     if(!z1){return;}
     if(!m1){return;}
     
    let mm1 =  jQuery('.date001 .mm_x_month').val();
    let mm2 =  jQuery('.date001 .mm_x_year').val();
    let mm3 =  jQuery('.date001 .mm_x_day').val();
    
    $url1 ='/billing/report_get_by_zone/pdf/'+z1+'/'+m1+'/'+mm2+'-'+mm1+'-'+mm3;
    window.open($url1, '_blank');

    
    //http://localhost:8000/billing/report_get_by_zone/pdf/3/m5/2018-10-24
    
     //date001
     //filter001
     //z1
     //m1
     
     
     //alert('test');

     //~ let ageing_full_date = jQuery('.ageing_full_date').val();

     //~ jQuery('.ageing_result').html('');
     //~ jQuery('.loading1').show();

     //~ jQuery.get( "/billing/report_get_by_zone/"+z1+'/'+m1+'/'+ageing_full_date, function( data ) {
          //~ jQuery('.loading1').hide();
          //~ jQuery('.ageing_result').html(data);
	//~ });


     /*
     jQuery.post( "/billing/report_get_by_zone", {
               'zone': z1,
               'month': m1,
               '_token':csrf_key
		}, function( data ) {
          jQuery('.loading1').hide();
          jQuery('.ageing_result').html(data);
	});
     */


}//


function generate_account_recievable_summary()
{
     let z1 = jQuery('.sel_zone1').val();
     let m1 = jQuery('.sel_month1').val();

     if(!z1){return;}
     if(!m1){return;}
     
    let mm1 =  jQuery('.date001 .mm_x_month').val();
    let mm2 =  jQuery('.date001 .mm_x_year').val();
    let mm3 =  jQuery('.date001 .mm_x_day').val();
    
    $url1 ='/billing/report_account_recievable_summary_pdf/'+z1+'/'+m1+'/'+mm2+'-'+mm1+'-'+mm3;
    window.open($url1, '_blank');
         

     //~ let ageing_full_date = jQuery('.ageing_full_date').val();
     //~ $url1 = '/billing/report_account_recievable_summary/'+z1+'/'+m1+'/'+ageing_full_date;

     //~ jQuery('.ageing_result').html('');
     //~ jQuery('.loading1').show();

     //~ jQuery.get($url1, function( data ) {
          //~ jQuery('.loading1').hide();
          //~ jQuery('.ageing_result').html(data);
	//~ });
	
	
	
}//
</script>
