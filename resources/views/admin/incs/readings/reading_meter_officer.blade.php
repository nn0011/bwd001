
<table class="table10 table-bordered  table-hover">
	<tbody><tr class="headings">
		<td width="3%">ID</td>
		<td width="50%">Name</td>
		<td width="10%">Zones</td>
		<td width="10%">Status</td>
	</tr>
	<!------>
	<?php 
	$index = 0;
	foreach($reading_off as $oo): ?>
	<!------>
	<tr onclick="view_meter_official(<?php echo $index;  ?>)" class="cursor1">
		<td><?php echo $oo['id']; ?></td>
		<td><?php echo $oo['name']; ?></td>
		<td><?php  echo $oo['zones_txt'];?></td>
		<td><?php echo $oo['hwd_officer']['stat']; ?></td>
	</tr>
	<!------>
	<?php $index++; endforeach; ?>
	<!------>
								
</tbody></table>



<!--------------------------------- --->
<!--------------------------------- --->
<div class="acct_list_view_acct" style="display:none;">
	
	<div class="pop_view_info_table  view_acct_info_pop">
		
		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>
		
		<br />
		<b>Zone Assignments</b>
		<br />
		<ul class="item_list1">
			<li>Zone 1   <span  class="field3"></span></li>
			<li>Zone 2   <span  class="field3"></span></li>
			<li>Zone 3   <span  class="field3"></span></li>
		</ul>

	</div>
</div>
<!------------------------------------>
<!------------------------------------>


<script>
var  official = <?php echo json_encode($reading_off); ?>;	
var curr_off = null;

function view_meter_official($ind){
	curr_off = official[$ind];	
	trig1_v2("acct_list_view_acct");
	
	setTimeout(function(){
		jQuery('.pop101  .field1').html(curr_off.name);
		jQuery('.pop101  .field2').html(curr_off.hwd_officer.address1);
		jQuery('.pop101  .field3').html('1250/3000');
	},100);
	
}//

</script>




<style>
.zone_check{
    padding: 0;
    list-style: none;
    margin: 0;
    display: inline-block;	
}
.zone_check li{
    border: 0px solid;
    display: inline-block;
    vertical-align: top;
    margin-right: 15px;
    margin-top: 15px;	
}
.back_1 .box1{min-height:auto !important;padding-bottom:50px !important;}	

</style>



