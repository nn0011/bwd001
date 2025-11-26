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


	<table  class="led01 acct_ledger01  table10"    cellpadding="0" cellspacing="0">
		<tr  class="headings">
			<td>READING DATE</td>
			<td>METER #</td>
			<td>PERIOD</td>
			<td>PREVIOUS READING</td>
			<td>CURRENT READING</td>
			<td>C.U. CONSUMPTION</td>
		</tr>
		<?php foreach($reading12 as $rr1): ?>
		<tr>
			<td><?php echo $rr1->curr_read_date;  ?></td>
			<td><?php echo $rr1->meter_number;  ?></td>
			<td><?php echo date('F Y',strtotime($rr1->period)); ?></td>
			<td><?php echo $rr1->prev_reading;  ?></td>
			<td><?php echo $rr1->curr_reading;  ?></td>
			<td><?php echo $rr1->current_consump;  ?> c.u.</td>
		</tr>
		<?php endforeach; ?>
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
<!--
	font-family:'monospace';
-->
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
    font-size:12px;
}
</style>
