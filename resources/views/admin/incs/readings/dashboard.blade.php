<div class="dec_box1">
	<div class="dec_box_head">Reading Status</div>
	<div class="dec_box_body">
			
			<small>Period  :</small>
			<select class="form-control" onchange="alert('coming soon')">
					<?php  
					$curr_date =  date('Y-m-').'20';
					for($x=0;$x<10;$x++): ?>
					<option value="<?php echo date('Y-m',  strtotime($curr_date.' -'.$x.' Month ')); ?>"><?php echo date('F Y',  strtotime($curr_date.' -'.$x.' Month ')); ?></option>
					<?php  endfor; ?>
			</select>
							
		
		<ul class="item_list1">
			<li><a onclick="">Active Accounts</a>  <span class="rd"><?php echo $active_acct_count; ?></span></li>
			<li><a onclick="">Account Read</a>  <span class="rd"><?php echo $read_accout_count; ?></span></li>
			<li><a onclick="">Unread</a>  <span class="rd"><?php echo $active_acct_count - $read_accout_count; ?></span></li>
		</ul>									
		<br>
		<div style="text-align:center;">
		</div>
	</div>
</div>


<div class="dec_box1">
	<div class="dec_box_head">Officer Status</div>
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
