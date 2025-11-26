 <?php foreach($role1->users as $us1): ?>
 <div class="teller100">
	<strong><?php echo $us1->name; ?></strong>

	<a  href="/admin/gm/daily_collection_report?uid=<?php echo $us1->id; ?>&dd=<?php echo $date1; ?>"
		style="float: right" class="badge badge-secondary" target="_blank">
		View Report
	</a>

	<br />
	 <ul class="item_list1">
		<?php 
		/*
		 <li>Invoice Start  	<span><?php echo @$us1->daily_col01['inv_min']; ?></span></li>
		 <li>Invoice End  		<span><?php echo @$us1->daily_col01['inv_max']; ?></span></li>
		 <li>Total Invoice  		<span><?php echo $us1->daily_col01['inv_max'] - @$us1->daily_col01['inv_min']; ?></span></li>
		 */ ?>
		 <li>Total Collected  	<span>P <?php echo number_format($us1->daily_col01['TTL'],2); ?></span></li>
	 </ul>
	 
	 <br />
	 <div style="text-align:right;">


		<?php 
		/*
		<a href="/admin/get_collection_info_by_date1001_monthly_summary/<?php echo $date1; ?>/<?php echo $us1->id; ?>" target="_blank"><small style="cursor: pointer;">View Monthly</small></a>
		*/ ?>
	 </div>
 </div>
 <?php endforeach; ?>
