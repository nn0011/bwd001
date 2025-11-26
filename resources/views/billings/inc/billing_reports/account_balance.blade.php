<div style="float:right;width:300px;"  class="date002 date22">
	<?php  report_date_filter(); ?>

<!--
     <small>Date:</small> <input type="text" class="form-control  account_balance_full_date" data-toggle="datepicker"  placeholder="Please Choose Date" value="<?php echo date('Y-m-d'); ?>">
-->
</div>

<h1>Account Balances</h1>

<br />

<div class="zone_mm">

     <select class="sel2_zone1">
          <option value="">Please Select Zone</option>
          <?php foreach($zone1 as $zz): ?>
          <option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
          <?php endforeach; ?>
     </select>
     &nbsp;&nbsp;&nbsp;
     <button onclick="generate_account_balance()">Account Balance</button>
     &nbsp;&nbsp;&nbsp;
     <button onclick="get_monthly_ending_balance()"> Monthly Ending Balance</button>
</div>

<br />
<br />

<div style="text-align:center;display:none;" class="loading1_acct_balance">
     <br />
     <br />
     <img src="/ajax-loader.gif" />
     <br />
     <br />
     Please Wait..<br />
</div>
<div class="account_balance_result">
</div>


<script>
function generate_account_balance()
{
     let z1 = jQuery('.sel2_zone1').val();
     let d1 = jQuery('.account_balance_full_date').val();

     if(!z1){return;}

    let mm1 =  jQuery('.date002 .mm_x_month').val();
    let mm2 =  jQuery('.date002 .mm_x_year').val();     
    d1 = mm2+'-'+mm1+'-28';

    //let $urlx = "/billing/report_get_account_balances1/"+z1+'/'+d1;
    let $urlx_pdf  = "/billing/report_get_account_balances_pdf/"+z1+'/'+d1;
    window.open($urlx_pdf, '_blank');

     //~ jQuery('.account_balance_result').html('');
     //~ jQuery('.loading1_acct_balance').show();

     //~ jQuery.get( $urlx, function( data ) {
          //~ jQuery('.loading1_acct_balance').hide();
          //~ jQuery('.account_balance_result').html(data);
	//~ });


}//

function get_monthly_ending_balance()
{
    let d1 = jQuery('.account_balance_full_date').val();
    
    let mm1 =  jQuery('.date002 .mm_x_month').val();
    let mm2 =  jQuery('.date002 .mm_x_year').val();     
    d1 = mm2+'-'+mm1+'-28';     
     
     //~ let $urlx = "/billing/report_get_account_monthly_ending_balance/"+d1;
     
    let $urlx = "/billing/report_get_account_monthly_ending_balance_pdf/"+d1;
    window.open($urlx, '_blank');
    
     //~ jQuery('.account_balance_result').html('');
     //~ jQuery('.loading1_acct_balance').show();

     //~ jQuery.get( $urlx, function( data ) {
          //~ jQuery('.loading1_acct_balance').hide();
          //~ jQuery('.account_balance_result').html(data);
	//~ });

}
</script>
