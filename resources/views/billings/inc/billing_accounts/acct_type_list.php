
<button class="trig1"  data-box1="new_acct_type">Create New Account Type</button>
<br />
<br />

<table class="table10 table-bordered  table-hover">
	<tbody><tr class="headings">
		<td width="5%">Sequence ID</td>
		<td width="5%">Acct. Type ID</td>
		<td width="50%">Name</td>
		<td width="50%">Description</td>
		<td width="10%">Status</td>
	</tr>
	<!------>
	<?php
	$index = 0;
	foreach($acct_types as $cc): ?>
	<!------>
	<tr onclick="view_acct_type(<?php echo $index; ?>)" data-index="<?php echo $index; ?>" data-box1="update_acct_type" class="cursor1  trig1">
		<td><?php echo $cc['id']; ?></td>
		<td><?php echo $cc['meta_code']; ?></td>
		<td style="padding:10px;"><?php echo $cc['meta_name']; ?></td>
		<td style="padding:10px;"><?php echo $cc['meta_desc']; ?></td>
		<td><?php echo $cc['status']; ?></td>
	</tr>
	<!------>
	<?php $index++; endforeach; ?>
</table>
