<div class="filter_1">

	<input type="text" placeholder="Account Number" id="search_acct_num"    value="<?php  echo  (@$acct_num != 'none' && !empty(@$acct_num))  ? @$acct_num: ''; ?>">
	<input type="text" placeholder="Meter Number" id="search_meter_num"   value="<?php echo  (@$meter_num != 'none' && !empty(@$meter_num))  ? @$meter_num: ''; ?>">
	<input type="text" placeholder="Last Name" id="search_last_name"  value="<?php echo  (@$lname != 'none' && !empty(@$lname))  ? @$lname: ''; ?>">
	
	<select id="search_zone">
		<option value="">ZONE</option>
		 <?php  foreach(@$zones as $zz):?>
		 <option value="<?php echo $zz['id']; ?>"   <?php echo  $zz['id']==@$zone?' selected ':''; ?>><?php echo strtoupper($zz['zone_name']); ?></option>
		 <?php endforeach; ?>
	</select>
	
	
	<select id="search_status">
		 <option value="0">Status</option>
		 <option value="1" <?php echo @$_GET['status']==1?' selected ':''; ?>>Zero / Negative Consumption</option>
		 <option value="2" <?php echo @$_GET['status']==2?' selected ':''; ?>>New Accounts</option>
		 <option value="3" <?php echo @$_GET['status']==3?' selected ':''; ?>>Disconnected</option>
		 <option value="4" <?php echo @$_GET['status']==4?' selected ':''; ?>>For Reconnection</option>
		 <option value="5" <?php echo @$_GET['status']==5?' selected ':''; ?>>Disconnected Accounts w/ consumption</option>
		 <option value="6" <?php echo @$_GET['status']==6?' selected ':''; ?>>Active Accounts w/ zero consumption</option>
		 <option value="7" <?php echo @$_GET['status']==7?' selected ':''; ?>>Read Accounts (Active and Disconnected)</option>
		 <option value="8" <?php echo @$_GET['status']==8?' selected ':''; ?>>Read Accounts (Active only)</option>
		 <option value="9" <?php echo @$_GET['status']==9?' selected ':''; ?>>Read Accounts (Disconnected only)</option>
		 <option value="10" <?php echo @$_GET['status']==10?' selected ':''; ?>>Unread Active Accounts</option>
		 <option value="11" <?php echo @$_GET['status']==11?' selected ':''; ?>>Active Accounts</option>
		 <option value="12" <?php echo @$_GET['status']==12?' selected ':''; ?>>Abnormal Readings</option>
		 <option value="13" <?php echo @$_GET['status']==13?' selected ':''; ?>>Billable Account</option>
	</select>

	<img src="/hwd1/img/search.jpg" class="but_filter" onclick="go_reading_filter()">

	<div style="padding-top:10px;padding-bottom:10px;">
		<span style="color: #108479;font-size: 18px;font-weight: bold;">Period :</span>
		<select id="period_month">
			<option value="1"  <?php echo  $r_month==1? ' selected ': ''; ?>>Janaury</option>
			<option value="2"  <?php echo  $r_month==2? ' selected ': ''; ?>>February</option>
			<option value="3"  <?php echo  $r_month==3? ' selected ': ''; ?>>March</option>
			<option value="4"   <?php echo  $r_month==4? ' selected ': ''; ?>>April</option>
			<option value="5"   <?php echo  $r_month==5? ' selected ': ''; ?>>May</option>
			<option value="6"   <?php echo  $r_month==6? ' selected ': ''; ?>>June</option>
			<option value="7"   <?php echo  $r_month==7? ' selected ': ''; ?>>July</option>
			<option value="8"   <?php echo  $r_month==8? ' selected ': ''; ?>>August</option>
			<option value="9"   <?php echo  $r_month==9? ' selected ': ''; ?>>September</option>
			<option value="10"   <?php echo  $r_month==10? ' selected ': ''; ?>>October</option>
			<option value="11"   <?php echo  $r_month==11? ' selected ': ''; ?>>November</option>
			<option value="12"   <?php echo  $r_month==12? ' selected ': ''; ?>>December</option>
		</select>
		<select id="period_year">
			<?php
			 $yy =  (int) date('Y');
			 for($xx=$yy; $xx>=($yy - 30); $xx--): ?>
			 <option value="<?php echo $xx; ?>"    <?php echo  $r_year==$xx? ' selected ': ''; ?>><?php echo $xx; ?></option>
			 <?php endfor; ?>
		</select>
		<img src="/hwd1/img/search.jpg" class="but_filter" onclick="go_period_search()">
	</div>

</div>
<!------>
<!------>
<script>
function go_period_search(){
	var hash = window.location.hash;
	var period_month = jQuery('#period_month').val();
	var period_year = jQuery('#period_year').val();
	window.location = '/billing/reading/'+period_year+'/'+period_month+hash;
}

function go_reading_filter(){
	var hash = window.location.hash;

	$acct = jQuery('#search_acct_num').val();
	$meter = jQuery('#search_meter_num').val();
	$lname = jQuery('#search_last_name').val();
	$zone = jQuery('#search_zone').val();
	$status = jQuery('#search_status').val();

	$url_part1 = '/';

	if(!$acct){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$acct+'/';}

	if(!$meter){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$meter+'/';}

	if(!$lname){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$lname+'/';}

	if(!$zone){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$zone+'/';}

	if(!$status){
		
	}
	else{
		$url_part1+='?status='+$status;
	}
	

	if($url_part1 == '/none/none/none/none/'){return;}
	window.location='/billing/reading/<?php echo $r_year; ?>/<?php echo $r_month; ?>'+'/filter'+$url_part1+hash;

}
</script>
