<button   data-box1="new_zone"  onclick="uopo()">Routes</button>
<br />
<br />

<table class="table10 table-bordered  table-hover">
	
	<tbody><tr class="headings">
		<td width="5%">ROUTE #</td>
		<td width="50%">ADDRESS</td>
		<td width="10%"> ACTION</td>
	</tr>
	
<?php
	
	$index = 0;
	
	foreach($customer_routes as $zz): break; ?>
	<!------>
	<tr class="cursor1">
		<td><?php echo $zz->route_num; ?></td>
		<td><?php echo $zz->route_addr; ?></td>
		<td><button  onclick="editRoute001(<?php echo $index; ?>)">Edit</button></td>
	</tr>
	<!------>
<?php $index++; endforeach; ?>

</table>







<?php 

$add_route_01 = '0';

?>

<div class="add_route_01" style="display:none;">

	<div class="pop_view_info_table  add_route">

		<form action="/billing/reading/add_route1?vr=2" method="POST" class="form-style-9"  onsubmit="return  submit_add_route;">
			
				<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
				<input type="hidden" name="route_id" value=""  id="route_id"  class="route_id">

				<br />
				<h3>Add new Route: </h3>
				<br />
				<ul class="item_list1">
					<li>Route #   <span><input type="text"  value=""     name="route_num"   class="route_num" /></span></li>
					<li>Route Address  <span><input type="text"  value=""   name="route_addr"     class="route_addr" /></span></li>
				</ul>				
				<br />
				<br />
				
				<div style="text-align:center;">
					<button  onclick="add_new_route_go()">Save</button>
						&nbsp;&nbsp;&nbsp;&nbsp;
					<button  onclick="add_new_route_cancel()">Cancel</button>
				</div>
				
			
		</form>

	</div>

</div>



<script>

let  submit_add_route = false;
let  routeJS = <?php echo json_encode($customer_routes); ?>;

function  uopo()
{
	trig1_v2('add_route_01');
}

function add_new_route_go()
{
	
	let  route_name = jQuery('.pop101 .add_route .route_num').val();
	let  route_addr =  jQuery('.pop101 .add_route .route_addr').val();
	
	if(!route_name){
		alert('Route name required.');
		return ;
	}
	
	if(!route_addr){
		alert('Route address required.');
		return;
	}
	
	submit_add_route = true;
	
	jQuery('.pop101  .add_route  form').submit();
	
}//

function add_new_route_cancel()
{
	pop_close();
}

function editRoute001($ind){
	trig1_v2('add_route_01');
	
	setTimeout(function(){
		$curr_route = routeJS[$ind];
		jQuery('.pop101 .add_route .route_num').val($curr_route.route_num);
		jQuery('.pop101 .add_route .route_addr').val($curr_route.route_addr);
		jQuery('.pop101 .add_route .route_id').val($curr_route.id);
	}, 100);
	
}

</script>

<style>
.add_route  ul.item_list1 li{
	padding-bottom: 15px;
}
.add_route  ul.item_list1 li input{
	width:250px;
}
.add_route   .item_list1 span{
	width: auto;
}
</style>

