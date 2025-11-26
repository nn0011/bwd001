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


<div  class="filter_1 acct_list1">

	<input type="text"  placeholder="Account Number"  id="search_acct_num"
	value="<?php  echo @$acct_num=='none'?'':@$acct_num; ?>" />
	<input type="text"  placeholder="Meter #(Last)"
		id="search_meter_num" value="<?php echo @$meter_num == 'none'?'':@$meter_num; ?>" />
	<input type="text"  placeholder="Last Name"
	id="search_last_name"  value="<?php echo @$lname == 'none'?'':@$lname; ?>"  />
	
	<!--
	<input type="text"  placeholder="First Name"
	id="search_first_name"  value="<?php echo @$fname == 'none'?'':@$fname; ?>"  />
	-->
	
	<select  id="search_zone">
		<option value="">ZONE</option>
		 <?php  foreach($zones as $zz):?>
		 <option value="<?php echo $zz['id']; ?>"><?php echo strtoupper($zz['zone_name']); ?></option>
		 <?php endforeach; ?>
	</select>
	
	<select  id="search_status11">
		<option value="none">Account Status</option>
		<?php 
		
		$selected = '';
		foreach($acct_statuses as $ass): 
			
			if(@$acct_status == @$ass['id']){
					$selected = ' selected ';
			}
		
		?>
		<option value="<?php echo @$ass['id']; ?>"   <?php echo $selected; ?>><?php echo @$ass['meta_name']; ?></option>
		<?php 
		$selected = '';
		
		endforeach; ?>
	</select>
	
	<?php 
	/*
	<select  id="search_beginning">
		<option value="">All</option>
		<option value="1">No Beginning Balance</option>
	</select>
	*/
	?>

	<img src="/hwd1/img/search.jpg" class="but_filter"  onclick="do_search_now()"   />
	
	<br />
	<button style="margin-top:10px;"  onclick="acct_list_clear_filter_all()">Clear Filter</button>
	
	
	<br />
	<br />
	<div  style="float:right;display:inline-block;">
		<small>Quick Sort</small> <br />
		<select  id="sort_out_search"  onchange="go_change_sort_out()">
			<option value="0">All</option>
			<option value="100">Account Master List</option>
			<option value="1">Penalty Exempted</option>
			<option value="2">Senior Citizen</option>
<!--
			<option value="3">Stagard Consideration</option>			
-->
			
			<?php foreach($acct_types as $att): 
						$att = (object)$att;		
			?>
			<option value="<?php echo $att->id; ?>"><?php echo $att->meta_name; ?></option>
			<?php endforeach; ?>
			
		</select>
		<br />
		<br />
		
	</div>
	

</div>
<br />

<div class="scroll1">
	<table class="table10 table-bordered  table-hover">
		<tr  class="headings">
			<td width="3%">SEQ #.</td>

			<td width="3%">Route#</td>

			<td width="10%">Account No.</td>
			<td width="10%">Meter No.</td>
			<td width="20%">Name</td>
			<td width="10%">Zone</td>
			<td width="10%">Address & Zone</td>
<!--
			<td width="5%">Route #</td>
			<td width="5%">Begining Balance</td>
-->
			<td width="5%">Account Status</td>
<!--
			<td width="10%">Admin Request Status</td>
-->
		</tr>
		<?php //for($x=0;$x<=20;$x++): ?>

		<?php if(empty($accounts['data'])): ?>
			<tr>
				<td class="empty_no_data" colspan="7">
						No Result
				</td>
			</tr>
		<?php endif; ?>

		<?php

		$index = 0;
		$ind2  = 1;
		$page  = (int) @$_GET['page'];
		$page  = abs($page);
		
		if($page <= 0)
		{
			$page = 1;
		}else{
			$page = ($page * 100) - 99;	
		}
		
		$ind2 = $page;
		
		foreach($accounts['data'] as $acct): 
		
		//~ ob_clean();
		//~ echo '<pre>';
		//~ print_r($accounts['data']);
		//~ die();
		
		?>
		<!------>
		<!------>
		<tr  onclick="view_account_info_main(<?php echo $index; ?>)"  data-index="<?php echo $index; ?>"  data-box1="view_info_acct"  class="cursor1  trig1">
			<td><?php echo $ind2; //echo $acct['id']; ?></td>

			<td><?php echo $acct['route_id']; ?></td>
			<td><?php echo $acct['acct_no']; ?></td>
			<td><?php echo !empty($acct['meter_number1'])?$acct['meter_number1']:'None'; ?></td>
			<?php
			/*
				<td style="padding:10px;">
						<span style="font-size: 16px;"><?php echo strtoupper($acct['lname'].', '.$acct['fname'].' '.$acct['mi']); ?></span>
						<p><?php echo $acct['address1']; ?></p>
				</td>
			*/ ?>

			<td><?php echo strtoupper($acct['fname'].' '.$acct['lname']); ?></td>
			<td><?php echo @$zone_lab[@$acct['zone_id']]; ?></td>
			<td>
				<?php echo $acct['address1']; ?>				
				<small style="font-size:10px;color:#0059b3;" >Zone: <?php echo @$zone_lab[@$acct['zone_id']]; ?></small>				
				<?php /*Route# <?php echo $acct['old_route']; ?>  */?>
				
			</td>
			
			<?php 
			/*
			
			<td>
				<?php if(!empty($acct['route_id'])): ?>
					<span style="color:#cc0000;">Route #<?php echo $acct['route_id']; ?></span>
				<?php else: ?>
					---
				<?php endif; ?>
			</td>
			
			<td style="text-align:right;">
				<?php
					if(empty($acct['ledger_data'])){
							echo '<span style="color:red;">----</span>';
					}else{
						echo '<span style="color:blue;">'.number_format($acct['ledger_data'][0]['ttl_bal'], 2).'</span>';
					}
				
				?>
			</td>
			*/?>
			
			<td>
				
				
				<?php
				
				echo acct_status($acct['acct_status_key']);
				//echo request_stat101($acct['request001']['status']);
				
				//~ switch(@$stat_label[$acct['acct_status_key']])
				//~ {

					//~ case 'New Concessionaire':
						//~ echo @$stat_label[$acct['acct_status_key']] .
						//~ ' <br />  <span class="rd">NOT ACTIVE</span>' ;
					//~ break;

					//~ default:
						//~ echo @$stat_label[$acct['acct_status_key']];
					//~ break;

				//~ }

			?></td>
			

			 
		</tr>
		<!------>
		<!------>
		<?php $ind2++; $index++; endforeach; ?>
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
