
<div  class="filter_1 acct_list1">
	
	<input type="text"  placeholder="Account Number"  id="search_acct_num"
	value="<?php  echo @$acct_num=='none'?'':@$acct_num; ?>" />
	<input type="text"  placeholder="Meter #(Last)"
		id="search_meter_num" value="<?php echo @$meter_num == 'none'?'':@$meter_num; ?>" />
	<input type="text"  placeholder="Last Name"
	id="search_last_name"  value="<?php echo @$lname == 'none'?'':@$lname; ?>"  />

	<select  id="search_zone">
		<option value="">ZONE</option>
		 <?php /* foreach($zones as $zz):?>
		 <option value="<?php echo $zz['id']; ?>"><?php echo strtoupper($zz['zone_name']); ?></option>
		 <?php endforeach;*/ ?>
	</select>

	<img src="/hwd1/img/search.jpg" class="but_filter"  onclick="acct_ledger_search()"   />

</div>
<br />
<br />

<div></div>

<div class="scroll1 acct_ledger_info"  style="display:none;">
	<button onclick="back_to_result()" style="margin-bottom:30px;">Back to Result</button>
	<div class="name_info">
		<span  class="acct_num"></span>
		<br />
		<span  class="full_name"></span>
		<br />
		<span  class="address"></span>
		<br />
		<br />
	</div>
	<div class="content1"></div>
</div>

<div class="scroll1 acct_ledger_result">
	<table class="table10 table-bordered  table-hover">
		
		<tr  class="headings">
			<td width="10%">Account No.</td>
               <td width="20%">Name</td>
			<td width="20%">Address & Zone</td>
               <td width="10%">Meter No.</td>
			<td width="10%">Account Status</td>
		</tr>

		<?php if($acct_list->count() == 0): ?>
			<tr>
				<td class="empty_no_data" colspan="5">
						No Result
				</td>
			</tr>
		<?php endif; ?>

          <?php
          $index1 = 0;
          foreach($acct_list as $acct): ?>
               <tr class="item_item">
     			<td><a  onclick="view_acct_ledger(<?php echo $index1; ?>)"><?php echo $acct->acct_no; ?></a></td>
                    <td><?php echo $acct->lname.', '.$acct->fname.' '.$acct->mi; ?></td>
     			<td>
                         <?php echo $acct->address1; ?><br />
                         <?php echo @$acct->my_zone->zone_name; ?><br />
                    </td>
                    <td><?php echo $acct->meter_number1; ?></td>
     			<td>
                         <?php echo @$acct->my_stat->meta_name; ?><br />
                    </td>
     		</tr>
          <?php $index1++; endforeach; ?>

	</table>


		<?php 
			$accounts = $acct_list->toArray();
		?>
		<div style="padding:15px;">
				<br />
				<?php echo $accounts['current_page']; ?> of <?php echo $accounts['last_page']; ?>
				<br />
				<ul class="pagination pagination-sm">
				  <?php if(!empty($accounts['prev_page_url'])): ?>
				  <li><a href="<?php echo $accounts['prev_page_url'].'#account_list'; ?>">PREVIOUS</a></li>
				  <?php endif; ?>
				  <?php

				  /*
				  for($x=1;$x<=$accounts['last_page'];$x++): ?>
				  <li  <?php echo $accounts['current_page']===$x?'  class="active" ':''; ?>><a href="<?php echo '/billing/accounts?page='.$x.'#account_list'; ?>"><?php echo $x; ?></a></li>
				  <?php endfor;
				  */

				   ?>
				  <?php if(!empty($accounts['next_page_url'])): ?>
				  <li><a href="<?php echo $accounts['next_page_url'].'#account_list'; ?>">NEXT</a></li>
				  <?php endif; ?>
				</ul>
		</div>
	
	
	
</div>


<div class="ledger_info001_cont" style="display:none;">
	<div class="ledger_info001">
	</div>
</div>

<style>
</style>




