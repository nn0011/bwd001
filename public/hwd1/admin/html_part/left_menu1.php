<div>
	<h2 style="text-align:center;color:white;">Admin</h2>
</div>

<ul  class="menu1">
	<li <?php echo @$billing_dashboard; ?>><a href="/admin/dashboard.php">Dashboard</a></li>
	<li  <?php echo @$billing_accounts; ?>><a href="/admin/accounts.php">Accounts</a></li>
	<li <?php echo @$billing_requests; ?>><a href="/admin/requests.php">Requests</a></li>
	<li <?php echo @$billing_reports; ?>><a href="/admin/reports.php">Reports</a></li>
	<li  <?php echo @$billing_billing; ?>><a href="/admin/billing.php">Billing</a></li>
	<li  <?php echo @$billing_collection; ?>><a href="/admin/collection.php">Collection</a></li>
	<li  <?php echo @$billing_meter_reading; ?>><a href="/admin/meter_reading.php">Reading</a></li>
	<li  <?php echo @$billing_accounts; ?>><a href="/admin/system_accounts.php">System Account</a></li>
	<li  <?php echo @$billing_logout; ?>><a href="/admin/logout.php">Logout</a></li>
</ul>
