<?php

//~ echo '<pre>';
//~ print_r($acct_statuses);
//~ die();

$zone_lab = array();
foreach($zones as $zz)
{
	$zone_lab[$zz['id']] = $zz['zone_name'];
}

?>

<div class="scroll1">
	<table class="table10 table-bordered  table-hover">
		<tr  class="headings">
			<td width="5%">SEQ #.</td>
			<td width="10%">Account No.</td>
			<td width="10%">Meter No.</td>
			<td width="20%">Name</td>
			<td width="20%">Address & Zone</td>
			<td width="5%">Account Status</td>

		</tr>


		<?php if(empty($accounts['data'])): ?>
			<tr>
				<td class="empty_no_data" colspan="7">
						No Result
				</td>
			</tr>
		<?php endif; ?>

		<?php

		$index = 0;
		foreach($accounts['data'] as $acct): 
		
		//~ ob_clean();
		//~ echo '<pre>';
		//~ print_r($accounts['data']);
		//~ die();
		
		
		?>
		<!------>
		<!------>
		<tr  onclick="view_account_info_main(<?php echo $index; ?>)"  data-index="<?php echo $index; ?>"  data-box1="view_info_acct"  class="cursor1  trig1">
			<td><?php echo $acct['id']; ?></td>
			<td><?php echo $acct['acct_no']; ?></td>
			<td><?php echo !empty($acct['meter_number1'])?$acct['meter_number1']:'None'; ?></td>
			<td><?php echo strtoupper($acct['fname'].' '.$acct['lname']); ?></td>
			<td>
				<?php echo $acct['address1']; ?>				
				<small style="font-size:10px;color:#0059b3;" >Zone: <?php echo @$zone_lab[@$acct['zone_id']]; ?></small>				

			</td>
			<td><?php echo acct_status($acct['acct_status_key']); ?></td>
		</tr>
		<!------>
		<!------>
		<?php $index++; endforeach; ?>
		<?php  //endfor; ?>

	</table>
</div>

<div style="padding:15px;">
	<br />
	<?php echo $accounts['current_page']; ?> of <?php echo $accounts['last_page']; ?>
	<br />
	<ul class="pagination pagination-sm">
	  <?php if(!empty($accounts['prev_page_url'])): ?>
	  <li><a href="<?php echo $accounts['prev_page_url'].'&quick_search='.@$_GET['quick_search'].'#account_list'; ?>">PREVIOUS</a></li>
	  <?php endif; ?>
	  <?php

		/*
	  for($x=1;$x<=$accounts['last_page'];$x++): ?>
	  <li  <?php echo $accounts['current_page']===$x?'  class="active" ':''; ?>><a href="<?php echo '/billing/accounts?page='.$x.'#account_list'; ?>"><?php echo $x; ?></a></li>
	  <?php endfor;
	  */

	   ?>
	  <?php if(!empty($accounts['next_page_url'])): ?>
	  <li><a href="<?php echo $accounts['next_page_url'].'&quick_search='.@$_GET['quick_search'].'#account_list'; ?>">NEXT</a></li>
	  <?php endif; ?>
	</ul>
</div>


<?php 

$pat1 = str_replace('billing/accounts', 'billing/accounts_pdf', $accounts['path']);

?>

<div style="padding:15px;text-align:right;">
	<a href="<?php echo $pat1.'?quick_search='.@$_GET['quick_search']; ?>" target="_blank"><button>Print Result</button></a>
</div>

<pre>
<?php 

//~ ($accounts['data'] = []);
//~ print_r($accounts); 

?>	
</pre>


<style>
.back_1 .box1{
	margin-top:30px !important; 
}
</style>


<script>
jQuery(document).ready(function(){
	//beg_stat
	jQuery('#search_beginning').val("<?php echo @$beg_stat; ?>").change();
	//quick_search
	
	<?php if(@$_GET['quick_search'] != 0): ?>
	jQuery('#sort_out_search').val("<?php echo (int) @$_GET['quick_search']; ?>");
	<?php endif; ?>
	
});	
</script>
