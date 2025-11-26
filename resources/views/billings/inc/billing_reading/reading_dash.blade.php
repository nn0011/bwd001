<h2>Current Reading Period :  <span class="rd"><?php echo date('F Y'); ?></span></h2>
<hr />
<div class="dec_box1">
	<div class="dec_box_head">Reading Status</div>
	<div class="dec_box_body">
		<h4><?php echo date('F Y'); ?></h4>
		<ul class="item_list1">
						<li><a onclick="">Active Accounts</a>  <span class="rd"><?php echo @$acct_reading['total']; ?></span></li>
						<li><a onclick="">Reading Counts</a>  <span class="rd"><?php echo @$read_count; ?></span></li>
						<li><a onclick="">Unread Counts</a>  <span class="rd"><?php echo @$no_read_count; ?></span></li>
					</ul>									
		<br>
		<div style="text-align:center;">
		</div>
	</div>
</div>

<?php
/*
<div class="dec_box1">
	<div class="dec_box_head">Officer Reading Status</div>
	<div class="dec_box_body">
		<ul class="item_list1">
						<li><a onclick="">April, Maya J</a>  <span class="rd">354 / 1000</span></li>
						<li><a onclick="">Steven, Jordan M</a>  <span class="rd">754 / 1000</span></li>
					</ul>									
		<br>
		<div style="text-align:center;">
		</div>
	</div>
</div>
*/ ?>
