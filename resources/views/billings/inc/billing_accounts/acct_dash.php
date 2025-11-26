<div class="dec_box1"  style="min-width: 450px;">
	<div class="dec_box_head">Accounts Summary</div>
	<div class="dec_box_body">
		<ul class="item_list1">
			<li>Active Accounts  <span><?php echo number_format($acct_active, 0); ?></span></li>
			<?php /*<li>Notice for Disconnections  <span>360</span></li>*/ ?>
			<li>Not Active  <span><?php echo number_format($acct_new_con, 0); ?></span></li>
			<li>Disconnected Accounts  <span><?php echo number_format($acct_discon, 0); ?></span></li>
			<?php /*<li>Discounted Accounts  <span>302</span></li>*/ ?>
		</ul>
		<br>
		<div style="text-align:center;">
		</div>
	</div>
</div>

<div class="dec_box1">
	<div class="dec_box_head">	Application Reqeusts</div>
	<div class="dec_box_body">
		<ul class="item_list1">
			<?php
			$ind = 0;
			foreach($hwd_request_new_acct as $hrna): ?>
			<li><a onclick="view_hwd_request_new_acct(<?php echo $ind; ?>)"><?php echo @$hrna['account']['acct_no']; ?></a>  <span class="<?php echo $hrna['status'] == 'pending'?'rd':''; ?>"><?php echo $hrna['status']; ?></span></li>
			<?php
			$ind++;
			 endforeach; ?>
		</ul>
		<br>
		<div style="text-align:center;">
		</div>
	</div>
</div>
