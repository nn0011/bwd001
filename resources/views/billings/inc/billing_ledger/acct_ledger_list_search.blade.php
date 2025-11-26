
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
</div>


