<div>

	<table>
	<tr>
		<td width="25%">Date</td>
		<td width="75%">Description</td>
	</tr>


	<?php foreach($ledger_list1 as $acct): ?>
	<tr>
		<td><?php echo date('F d, Y @ H:iA', strtotime($acct->led_date2)); ?></td>
		<td>
			<p><?php echo $acct->led_title; ?></p>
			<?php echo $acct->led_desc1; ?>
			<?php
			/*<ul  class="item_list1">
				<li>Reading Perid : <span>October 2018</span></li>
				<li>Previous Reading :  <span>20</span></li>
				<li>Current Reading :  <span>33</span></li>
				<li>Consumption :  <span>13</span></li>
			</ul>*/ ?>
		</td>
	</tr>
	<?php endforeach; ?>

	<?php
	/*
	<tr>
		<td>Jan 21, 2018</td>
		<td>
			Payment Made
			<ul  class="item_list1">
				<li>Current Bill :  <span>200.00</span></li>
				<li>Penalty :  <span>20.00</span></li>
				<li>Arrears :  <span>3,642.15</span></li>
			</ul>
			<ul   class="item_list1"  style="background:#ffc299;padding:10px;">
				<li>Total Balance :  <span>3,862.15</span></li>
				<li>Payment Amount : <span>600.00</span></li>
				<li>Remaining Balance : <span>3,262.15</span></li>
			</ul>
		</td>
	</tr>



	<tr>
		<td>Jan 21, 2018</td>
		<td>
			Penalty for Billing September 2018
			<ul  class="item_list1">
				<li>Penalty Amount : <span>20.00</span></li>
				<li>Total Balance :  <span>3,862.15</span></li>
			</ul>
		</td>
	</tr>


	<tr>
		<td>Jan 21, 2018</td>
		<td>
			Billing for September 2018
			<ul  class="item_list1">
				<li>Billing Period : <span>September 2018</span></li>
				<li>Current Bill :  <span>200.00</span></li>
				<li>Arrears :  <span>3,642.15</span></li>
				<li>Total Balance :  <span>3,842.15</span></li>
			</ul>
		</td>
	</tr>

	<tr>
		<td>Jan 21, 2018</td>
		<td>
			Reading for September 2018
			<ul  class="item_list1">
				<li>Reading Perid : <span>September 2018</span></li>
				<li>Previous Reading :  <span>10</span></li>
				<li>Current Reading :  <span>20</span></li>
				<li>Consumption :  <span>10</span></li>
			</ul>
		</td>
	</tr>


	<?php for($xx=0;$xx<=20;$xx++): ?>
	<tr>
		<td>Jan 21, 2018</td>
		<td>New application request</td>
	</tr>
	<?php endfor; ?>
	*/ ?>



	</table>



</div>
