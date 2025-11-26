<?php 

	$pre_n = 'daily_billing_'; 
	$pre_n2 = 'billing_summary_'; 

?>
<div style="float:right; width:300px;"  class="date005 date22">
<?php  report_date_filter(); ?>
	
<!--
     <small>Date:</small> <input type="text" class="form-control  <?php echo $pre_n; ?>full_date" data-toggle="datepicker"  placeholder="Please Choose Date" value="<?php echo date('Y-m-d'); ?>">
-->
</div>
<h1>Billing Summary</h1>
<select class="<?php echo $pre_n; ?>zone1 form-control "  style="width:300px;">
     <option value="">Please Select Zone</option>
     <?php foreach($zone1 as $zz): ?>
     <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
     <?php endforeach; ?>
</select>

<button onclick="<?php echo $pre_n; ?>generate()" class="form-control ad_but1">Account Summary</button>
<button onclick="<?php echo $pre_n; ?>generate_montly_sum()" class="form-control ad_but1">Monthly Zone Summary</button>
<button onclick="<?php echo $pre_n; ?>generate_annualy_sum()" class="form-control ad_but1">Annualy  Summary</button>

<br />
<br />
<br />

<div style="text-align:center;display:none;" class="<?php echo $pre_n; ?>loading1">
     <br />
     <br />
     <img src="/ajax-loader.gif" />
     <br />
     <br />
     Please Wait..
     <br />
</div>

<div class="<?php echo $pre_n; ?>result">
</div>

<div style="display:none;">
</div>


<script>
function <?php echo $pre_n; ?>generate()
{

     let z1 = jQuery('.<?php echo $pre_n; ?>zone1').val();
     if(!z1){return;}

     let delinq_date = jQuery('.<?php echo $pre_n; ?>full_date').val();
     
    let mm1 =  jQuery('.date005 .mm_x_month').val();
    let mm2 =  jQuery('.date005 .mm_x_year').val();
    delinq_date = mm2+'-'+mm1+'-28';     

    let $urlx = '/billing/<?php echo $pre_n2; ?>get_account_pdf/'+z1+'/'+delinq_date;
    window.open($urlx, '_blank');     

     //~ jQuery('.<?php echo $pre_n; ?>result').html('');
     //~ jQuery('.<?php echo $pre_n; ?>loading1').show();

     //~ let url1 = '/billing/<?php echo $pre_n; ?>get_ajax1/'+z1+'/'+delinq_date;

     //~ jQuery.get( url1, function( data ) {
          //~ jQuery('.<?php echo $pre_n; ?>loading1').hide();
          //~ jQuery('.<?php echo $pre_n; ?>result').html(data);
	//~ });

}//

function <?php echo $pre_n; ?>generate_montly_sum()
{
    let mm1 =  jQuery('.date005 .mm_x_month').val();
    let mm2 =  jQuery('.date005 .mm_x_year').val();
    delinq_date = mm2+'-'+mm1+'-28';     

    let $urlx = '/billing/billing_summary_get_zone_class_pdf/'+delinq_date;
    window.open($urlx, '_blank');         
}

function <?php echo $pre_n; ?>generate_annualy_sum()
{
    let mm1 =  jQuery('.date005 .mm_x_month').val();
    let mm2 =  jQuery('.date005 .mm_x_year').val();
    delinq_date = mm2+'-'+mm1+'-28';     

    let $urlx = '/billing/billing_summary_get_annual_pdf/'+delinq_date;
    window.open($urlx, '_blank');         
}

</script>
