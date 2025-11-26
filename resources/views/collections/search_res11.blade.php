
	<div class="cashier_view">

		<table class="table10 table-bordered  table-hover">
			<tbody>

			<tr class="headings">
				<td width="10%"  style="text-align:left;">Account No.</td>
				<td width="15%"  style="text-align:left;">Name</td>
				<td width="30%"  style="text-align:left;">Complete Address</td>
<!--
				<td width="5%"  style="text-align:right;">Current Balance</td>
-->
			</tr>
			
			<?php if(count($accts['data']) == 0): ?>
			<tr>
				<td colspan="3">
					<div style="padding:30px;text-align:left; font-size:18px;">
						No Result
					</div>
				</td>
			</tr>
			<?php endif; ?>
			
			<?php
			$index = 0;
			foreach($accts['data'] as $ac):  ?>
			<tr>
				<td><a onclick="view_acct_info(<?php echo $index; ?>)"><?php  echo $ac['acct_no']; ?></a></td>
				<td><?php  echo $ac['lname'].', '.$ac['fname'].' '.$ac['mi']; ?></td>
				<td><?php  echo $ac['address1']; ?></td>
<!--
				<td style="text-align:right;"><span style="font-size:9px;">&#x20b1;</span> <span <?php if($ac['remaining_balance'] <=0 ){echo 'style="color:red;"';} ?>><?php  echo number_format($ac['remaining_balance'], 2); ?></span></td>
-->
			</tr>
			<?php
			$index++;
			endforeach; ?>

		</tbody></table>

	</div>

	<div class="pagination_container">
		 <ul class="pagination pagination-sm">
			 <?php if(!empty($accts['prev_page_url'])): ?>
			<li><a onclick="navigate_xx1('<?php echo  $accts['prev_page_url']; ?>')"><< Previous</a></li>
			<?php endif; ?>

			 <?php if(!empty($accts['next_page_url'])): ?>
			<li><a onclick="navigate_xx1('<?php echo  $accts['next_page_url']; ?>')">Next >></a></li>
			<?php endif; ?>
		 </ul>
	</div>
