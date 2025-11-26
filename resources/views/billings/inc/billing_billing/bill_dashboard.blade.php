<div class="dec_box1">
     <div class="dec_box_head">Billing Status</div>
     <div class="dec_box_body">
		<?php $period11 = date('Y-m', strtotime($r_year.'-'.$r_month)); ?>
		<select class="form-control"  onchange="get_billing_status1()">
			<?php for($x=0;$x<=2;$x++){ 
					$pp_val  = date('Y-m', strtotime($period11.' -'.$x.'month'));
					$pp_lab  = date('F Y', strtotime($period11.'  -'.$x.'month'));
			?>
			<option  value="<?php echo $pp_val; ?>"><?php echo $pp_lab; ?></option>
			<?php } ?>
		</select>
		
	     <ul class="item_list1">
		     <li>Active Accounts  <span><?php echo $active_acct_count; ?></span></li>
		     <li>Accounts Billed  <span><?php echo $billed_acct; ?></span></li>
		     <li>Unbilled  <span class="rd"><?php echo $active_acct_count - $billed_acct; ?></span></li>
	     </ul>									
	     <br>
	     <div style="text-align:center;">
	     </div>
     </div>
</div>

<script>
function get_billing_status1(){
	alert('coming soon');
}	
</script>
