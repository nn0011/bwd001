<?php 
$stat_label  = array();
foreach($acct_statuses as $ass){
	$stat_label[$ass['id']] = $ass['meta_name'];
}
?>
<table class="table10 table-bordered  table-hover">
	<thead><tr class="headings">
		<td width="5%">SEQ #.</td>
		<td width="10%">Account No.</td>
		<td width="10%">Meter No.</td>
		<td width="20%">Name</td>
		<td width="40%">Address</td>
		<td width="10%">Status</td>
	</tr>
	</thead>
	<tbody class="tabl_search_res">
	<?php  
	$index = 0;
	foreach($res['data'] as $acct): ?>
	<tr  onclick="account_list_view_account(<?php echo $index; ?>)"   class="cursor1">
		<td><?php echo $acct['id']; ?></td>
		<td><?php echo $acct['acct_no']; ?></td>
		<td><?php echo $acct['meter_number1']; ?></td>
		<td>
			<?php echo strtoupper($acct['lname'].', '.$acct['fname'].' '.$acct['mi']); ?>
		</td>
		<td>
			<?php echo $acct['address1']; ?>
		</td>
		<td><?php  echo $stat_label[$acct['acct_status_key']]; ?></td>
	</tr>
	<?php $index++; endforeach;  ?>
					
</tbody></table>

<br />
<br />

<?php if(!empty($res['prev_page_url'])): ?>
<button  onclick="acct_list_goto_page('<?php echo $res['prev_page_url']; ?>')"  style="margin-right:50px;">Prev</button>
<?php endif; ?>

<?php echo $res['current_page']; ?> of <?php echo $res['last_page']; ?>


<?php if(!empty($res['next_page_url'])): ?>
	<button  onclick="acct_list_goto_page('<?php echo $res['next_page_url']; ?>')"  style="margin-left:50px;">Next</button>
<?php endif; ?>
