	<button   class="trig1"  data-box1="new_acct_status">Create New Account Status</button>
	<br />
	<br />

	<table class="table10 table-bordered  table-hover">
		<tbody><tr class="headings">
			<td width="5%">Sequence ID</td>
			<td width="5%">Acct. Status CODE</td>
			<td width="50%">Name</td>
			<td width="10%">Status</td>
		</tr>

		<!------>
		<?php
		$index = 0;
		foreach($acct_statuses as $acct_stat): ?>
		<!------>
		<tr onclick="view_acct_status(<?php echo $index; ?>)" data-index="<?php echo $index; ?>" data-box1="update_acct_status" class="cursor1  trig1">

			<td><?php echo $acct_stat['id']; ?></td>
			<td><?php echo $acct_stat['meta_code']; ?></td>
			<td><?php echo $acct_stat['meta_name']; ?></td>
			<?php /*<td style="padding:10px;"><?php echo $acct_stat['meta_name']; ?></td> */ ?>
			<td><?php echo $acct_stat['status']; ?></td>
		</tr>
		<!------>
		<?php $index++;  endforeach; ?>

	</table>
