<?php 

		$full_name = $acct1->fname.' '.$acct1->mi.' '.$acct1->lname;

?>
<title><?php echo $acct1->acct_no; ?></title>
<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />
Ledger Account of <?php  echo $full_name; ?>
<br />
Acct # <?php echo $acct1->acct_no; ?>
<br />
As of <?php echo date('F d, Y'); ?>
</p>
	<table  class="acct_ledger01"    cellpadding="0" cellspacing="0">
		
		<tr>
			<td width="20%">Date</td>
			<td width="40%">Description</td>
			<td width="40%">Information</td>
		</tr>

		<?php  foreach($ledger_list1 as $acct): ?>
			<tr>
				<td><?php echo date('F d, Y @ H:iA', strtotime($acct->led_date2)); ?></td>
				<td>
					<p><?php echo $acct->led_title; ?></p>
				</td>
				<td>
					<?php echo $acct->led_desc1; ?>
				</td>
			</tr>
		<?php endforeach;  ?>

	</table>
	
	<br  />
	<br  />
	---END ---
	<br  />
	<br  />
	
<?php 
echo html_signature();

?>

<style>
*{
	font-family:'arial';
	font-size:12px;
}	
	
table td{
	border:1px solid #ccc;
	padding:3px;
}
	
.acct_ledger01 td {
    border: 1px solid #ccc;
    padding: 3px !important;
    vertical-align: top;
}
</style>
