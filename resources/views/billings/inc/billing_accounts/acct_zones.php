<button   class="trig1"  data-box1="new_zone">Create ZONE</button>
<br />
<br />

<table class="table10 table-bordered  table-hover">
	<tbody><tr class="headings">
		<td width="5%">Sequence ID</td>
		<td width="5%">ZONE CODE</td>
		<td width="50%">Name</td>
		<td width="10%">Status</td>
	</tr>
	<?php
		$index = 0;

	foreach($zones as $zz): ?>
	<!------>
		<tr onclick="view_zone(<?php echo $index; ?>)" data-index="<?php echo $index; ?>" data-box1="update_zone" class="cursor1  trig1">
		<td><?php echo $zz['id']; ?></td>
		<td><?php echo $zz['zone_code']; ?></td>
		<td style="padding:10px;">
			<?php echo $zz['zone_name']; ?>
		</td>
		<td><?php echo $zz['status']; ?></td>
	</tr>
	<!------>
	<?php $index++; endforeach; ?>

</table>
