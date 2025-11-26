<?php  
/*
<button onclick="add_new_meter_officer()">Add New Officer</button>
<br />
<br />
*/ 
?>

<table class="table10 table-bordered  table-hover">
	<tbody><tr class="headings">
		<td width="3%">ID</td>
		<td width="50%">Name</td>
		<td width="10%">Zones</td>
	</tr>
	<!------>
	<?php 
	$index = 0;
	foreach($reading_off as $oo): ?>
	<!------>
	<tr onclick="update_meter_officer(<?php echo $index;  ?>)"  class="cursor1">
		<td><?php echo $oo['id']; ?></td>
		<td><?php echo $oo['name'];?></td>
		<td><?php echo $oo['zones_txt']; ?></td>
	</tr>
	<!------>
	<?php $index++; endforeach; ?>
	<!------>
								
</tbody></table>






<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_new_meter_officer" style="display:none;">
	<div class="pop_view_info_table">
		<h3>Add New Meter Officer</h3>
		
		<form action="/billing/reading/new_meter_officer" method="POST" class="form-style-9" onsubmit="">
			@include('billings.inc.billing_reading.reading_inc.form_1')
		</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>
<div class="update_meter_officer" style="display:none;">
	<div class="pop_view_info_table">
		<h3 class="meter_off_name">Noel Gregor O. Ilaco</h3>
		
		<form action="/billing/reading/update_meter_officer" method="POST" class="form-style-9" onsubmit="">
			<input type="hidden"  name="uid" value=""  id="officer_id" />
			@include('billings.inc.billing_reading.reading_inc.form_1')
		</form>
	</div>
</div>
<!------------------------------------>
<!------------------------------------>





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
    margin-top: 0px;	
	width: 40%;
}
</style>


<script>
var  official = <?php echo json_encode($reading_off); ?>;	

function add_new_meter_officer(){
	return;
	return;
	trig1_v2("add_new_meter_officer");
}

function update_meter_officer($ind){
	
	var  oo = official[$ind];
	trig1_v2("update_meter_officer");
	
	/**/
	setTimeout(function(){
			
		jQuery('.pop_view_info_table #officer_id').val(oo.id);
		jQuery('.pop_view_info_table  .meter_off_name').html(oo.name);

		if(oo.hwd_officer){
			jQuery('.pop_view_info_table #new_acc_6').val(oo.hwd_officer.address1);	
			jQuery('.pop_view_info_table #new_acc_10').val(oo.hwd_officer.stat);

			var mm = oo.hwd_officer.zones;		
			var spl = mm.split('|');
			
			jQuery.each(spl, function( index, value ) {
				yy = value || 0;
				if(yy!=0){
					jQuery('.pop_view_info_table #ck1_'+yy).prop("checked", true);
				}
			});
			
		}//
		

	},100);
	/**/
}

</script>






