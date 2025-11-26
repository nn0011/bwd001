
<ul  class="menu1">
	<li <?php echo @$billing_dashboard; ?>><a href="/billing/">Dashboard</a></li>
	<li <?php echo @$billing_reports; ?>><a href="/billing/reports">Reports</a></li>
	<li  <?php echo @$billing_billing; ?>><a href="/billing/billing">Billing</a></li>
	<li  <?php echo @$billing_collection; ?>><a href="/billing/collection">Banks</a></li>
	<li  <?php echo @$billing_meter_reading; ?>><a href="/billing/reading">Reading</a></li>
<!--
	<li  <?php echo @$billing_acct_ledger; ?>><a href="/billing/account_ledger">Account Ledger</a></li>
-->
	<li  <?php echo @$admin_other_payable; ?>><a href="/admin/other_payable">Other Payable</a></li>		
	<li  <?php echo @$meter_management; ?>><a href="/water-meter/meter_management">Meter Management</a></li>		
	<li  <?php echo @$billing_accounts; ?>><a href="/billing/accounts">Accounts</a></li>
	<li  <?php echo @$billing_logout; ?>><a href="/billing/logout">Logout</a></li>
</ul>
