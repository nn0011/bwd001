
	<ul  class="menu1">

<?php if(Auth::user()->hasRole('general_manager')): ?>
	<li  <?php //echo @$admin_collection; ?>><a href="/admin/gm/dashboard">Report Charts</a></li>
	<li  <?php echo @$admin_collection; ?>><a href="/admin/collection">Collection</a></li>
	<li  <?php echo @$admin_adjustment; ?>><a href="/admin/adjustments">Adjustments</a></li>
	<li <?php echo @$admin_request; ?>><a href="/admin/all_requests">Requests</a>  <span class="req_ind11 req_ind"  style="display:none;">New</span></li>
<?php else: ?>

	<li <?php echo @$billing_dashboard; ?>><a href="/admin/">Dashboard</a></li>
	<li <?php echo @$admin_request; ?>><a href="/admin/all_requests">Requests</a>  <span class="req_ind11 req_ind"  style="display:none;">New</span></li>
	<li  <?php echo @$admin_system_acct; ?>><a href="/admin/system_account">System Account</a></li>
	<li  <?php echo @$billing_accounts; ?>><a href="/billing/accounts">Accounts</a></li>
	<li <?php echo @$billing_reports; ?>><a href="/billing/reports">Reports</a></li>
<?php endif; ?>	

<li  <?php echo @$billing_logout; ?>><a href="/admin/logout">Logout</a></li>

<!--
		<li <?php echo @$billing_reports; ?>><a href="/admin/billing_reports.php">Reports</a></li>
		<li  <?php echo @$admin_billing; ?>><a href="/admin/billing">Billing</a></li>
		<li  <?php echo @$billing_meter_reading; ?>><a href="/admin/readings">Reading</a></li>
-->
		<?php /*<li  <?php echo @$admin_accounts; ?>><a href="/admin/accounts">Accounts</a></li>*/ ?>
<!--
		<li  <?php echo @$admin_other_payable; ?>><a href="/admin/other_payable">Other Payable</a></li>		
-->


	</ul>

