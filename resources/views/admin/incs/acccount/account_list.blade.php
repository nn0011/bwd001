<?php 
$stat_label  = array();
foreach($acct_statuses as $ass){
	$stat_label[$ass['id']] = $ass['meta_name'];
}
?>
<div class="filter_1  acct001_filter">
	<input type="text" placeholder="Account Number" id="search_acct_num">
	<input type="text" placeholder="Last Name" id="search_last_name">
	<select id="search_zone">
		<option value="">ZONE</option>
		 <option value="2">ZONE1</option>
		 <option value="1">ZONE2</option>
	</select>
	<img src="/hwd1/img/search.jpg" class="but_filter" onclick="account_search1()">
</div>


<div class="scroll1  acct_search_resu">
	@include('admin.incs.acccount.ajax.ajax_temp1')
</div>


<!--------------------------------- --->
<!--------------------------------- --->
<div class="acct_list_view_acct" style="display:none;">
	
	<div class="pop_view_info_table  view_acct_info_pop">
		
		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>
		
		<br />
		
		<ul class="item_list1">
			<li>Account Number   <span  class="field3"></span></li>
			<li>Metter Number   <span  class="field4"></span></li>
			<li>Account Created   <span  class="field5"></span></li>
			<li>Zone   <span  class="field6"></span></li>
			<li>Account Type   <span  class="field7"></span></li>
			<li>Account Status   <span  class="field8"></span></li>
			<li>Discount Type   <span  class="field9"></span></li>
			<li>Number of Bills   <span  class="field10"></span></li>
		</ul>

	</div>
</div>
<!------------------------------------>
<!------------------------------------>



<script>
	var  acct_list = <?php  echo json_encode($res['data']); ?>;
	var curr_acct = null;
	
	function account_list_view_account($ind){
		curr_acct = acct_list[$ind];
		trig1_v2('acct_list_view_acct');
		setTimeout(function(){
			$full_name = curr_acct.lname+', '+curr_acct.fname+' '+curr_acct.mi;
			jQuery('.pop101  .field1').html($full_name);
			jQuery('.pop101  .field2').html(curr_acct.address1);
			jQuery('.pop101  .field3').html(curr_acct.acct_no);
			jQuery('.pop101  .field4').html(curr_acct.meter_number1);
			jQuery('.pop101  .field5').html(curr_acct.acct_created);
			jQuery('.pop101  .field6').html(curr_acct.zone_lab);
			jQuery('.pop101  .field7').html(curr_acct.acct_type_lab);
			jQuery('.pop101  .field8').html(curr_acct.acct_stat_lab);
			jQuery('.pop101  .field9').html(curr_acct.bill_dis_lab);
			jQuery('.pop101  .field10').html(curr_acct.num_of_bill);
		}, 200);
	}
	
	function account_search1(){
		$par1 = jQuery('.acct001_filter #search_acct_num').val();
		$par2 = jQuery('.acct001_filter #search_last_name').val();
		$par3 = jQuery('.acct001_filter #search_zone').val();
		if(!$par1){$par1='none';}
		if(!$par2){$par2='none';}
		if(!$par3){$par3='none';}
		var s_url = '/'+$par1+'/'+$par2+'/'+$par3+'';
		if(s_url == '/none/none/none'){return false;}
		s_url = '/admin/accounts/get'+s_url;
		jQuery.get(s_url)
		 .done(function( data ) {
				jQuery('.acct_search_resu').html(data.html);
				acct_list = data.data1;
		 });		
	}
	
	function acct_list_goto_page($url){
		jQuery.get($url)
		 .done(function( data ) {
				jQuery('.acct_search_resu').html(data.html);
				acct_list = data.data1;
		 });				
	}
	
</script>

<style>
.back_1 .box1{min-height:auto !important;padding-bottom:50px !important;}	
</style>

