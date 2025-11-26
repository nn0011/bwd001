<?php
if(empty($r_month)){
	$r_month = (int) date('m');
} 
?>
<div class="filter_1">
	
	<input type="text" placeholder="Account Number" id="search_acct_num"    value="<?php  echo  (@$acct_num != 'none' && !empty(@$acct_num))  ? @$acct_num: ''; ?>">
	<input type="text" placeholder="Last Name" id="search_last_name"  value="<?php echo  (@$lname != 'none' && !empty(@$lname))  ? @$lname: ''; ?>">
	<img src="/hwd1/img/search.jpg" class="but_filter" onclick="go_collection_filter()">
	
	
	<div style="float:right;">
		<span style="color: #108479;font-size: 18px;font-weight: bold;">Period :</span>
		<select id="period_month">
			<option value="1"  <?php echo  @$r_month==1? ' selected ': ''; ?>>Janaury</option>
			<option value="2"  <?php echo  @$r_month==2? ' selected ': ''; ?>>February</option>
			<option value="3"  <?php echo  @$r_month==3? ' selected ': ''; ?>>March</option>
			<option value="4"   <?php echo  @$r_month==4? ' selected ': ''; ?>>April</option>
			<option value="5"   <?php echo  @$r_month==5? ' selected ': ''; ?>>May</option>
			<option value="6"   <?php echo  @$r_month==6? ' selected ': ''; ?>>June</option>
			<option value="7"   <?php echo  @$r_month==7? ' selected ': ''; ?>>July</option>
			<option value="8"   <?php echo  @$r_month==8? ' selected ': ''; ?>>August</option>
			<option value="9"   <?php echo  @$r_month==9? ' selected ': ''; ?>>September</option>
			<option value="10"   <?php echo  @$r_month==10? ' selected ': ''; ?>>October</option>
			<option value="11"   <?php echo  @$r_month==11? ' selected ': ''; ?>>November</option>
			<option value="12"   <?php echo  @$r_month==12? ' selected ': ''; ?>>December</option>
		</select>
		<select id="period_year">
			<?php 
			 $yy =  (int) date('Y');
			 for($xx=$yy; $xx>=($yy - 30); $xx--): ?> 
			 <option value="<?php echo $xx; ?>"    <?php echo  @$r_year==$xx? ' selected ': ''; ?>><?php echo $xx; ?></option>
			 <?php endfor; ?>
		</select>
		<img src="/hwd1/img/search.jpg" class="but_filter" onclick="go_collection_filter()">
	</div>
	
</div>
<!------>
<!------>
<script>


function go_collection_filter(){
	var hash = window.location.hash;
	
	$acct = jQuery('#search_acct_num').val();
	$lname = jQuery('#search_last_name').val();
	
	$url_part1 = '/';
	
	if(!$acct){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$acct+'/';}
	
	if(!$lname){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$lname+'/';}

	if($url_part1 == '/none/none/'){return;}

	var period_month = jQuery('#period_month').val();
	var period_year = jQuery('#period_year').val();

	$url_a =  '/billing/collection/search_account/'+period_year+'/'+period_month+'/filter'+$url_part1;

	jQuery.get( $url_a, function( data ) {
		 jQuery('.result001').html(data);
	});	
	
}
</script>








