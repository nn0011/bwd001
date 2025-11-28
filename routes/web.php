<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| aaa
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cash1', function () {
    return view('cash1_test');
});

Route::get('/logout', function () {
     Auth::logout();
     return redirect('/');
});


Route::get('/daily_collect_service',  'CashierCtrl@daily_collect_service');//ReportDaily


Auth::routes();

Route::group(['prefix' => '/water-meter'], function(){

	Route::get('/meter_management',  'ManageMeterCtrl@meter_management');
	Route::get('/add_water_meter',  'ManageMeterCtrl@add_water_meter');
	Route::get('/delete_water_meter',  'ManageMeterCtrl@delete_water_meter');
	Route::get('/update_water_meter',  'ManageMeterCtrl@update_water_meter');
	Route::get('/get_water_meters',  'ManageMeterCtrl@get_water_meters');

	Route::get('/ajax_list',  'ManageMeterCtrl@ajax_list');
	Route::get('/ajax_search',  'ManageMeterCtrl@ajax_search');
	Route::get('/ajax_search_account',  'ManageMeterCtrl@ajax_search_account');

	Route::get('/add_personel_name',  'ManageMeterCtrl@add_personel_name');
	Route::post('/add_to_history',  'ManageMeterCtrl@add_to_history');
	Route::get('/get_history/{meter}',  'ManageMeterCtrl@get_history');

	Route::post('/save_remarks_by_history_id',  'ManageMeterCtrl@save_remarks_by_history_id');
	
});

##############
##############
##############
Route::get('/test0001', 'BegBalCtrl@test0001');
Route::get('/test0002', 'BegBalCtrl@test0002');
##############
##############
##############





Route::get('/export/exp_accounts/{zone_id}', 'ExportCtrl@exp_accounts');
Route::get('/gps_data_all', 'HomeController@gps_data_all');



Route::get('/test99999', 
		'CashierCtrl@test99999');   

Route::get('/generate_montly_report_101/{date1}', 
		'CashierCtrl@generate_montly_report_101');   

Route::get('/generate_montly_report_101_check/{date1}',	'CashierCtrl@generate_montly_report_101_check');  
Route::get('/generate_montly_report_102_check/{date1}',	'CashierCtrl@generate_montly_report_102_check');  
Route::get('/generate_montly_report_102_reset/{date1}',	'CashierCtrl@generate_montly_report_102_reset');  


Route::get('/report_penalty_report/{zone_id}/{date1}', 
		'ReportCtrl@report_penalty_report');   

Route::get('/report_penalty_report_by_zone/{zone_id}/{date1}', 
		'ReportCtrl@report_penalty_report_by_zone');   
		
Route::get('/billing/consessionare_report_step1_disconnected_pdf/{date1}/{zone_id}', 
		'ReportCtrl@consessionare_report_step1_disconnected_pdf');   
		
Route::get('/billing/consessionare_report_step1_reconnected_pdf/{date1}/{zone_id}', 
		'ReportCtrl@consessionare_report_step1_reconnected_pdf');   
		
Route::get('/billing/consessionare_report_step1_new_consessionare_pdf/{date1}/{zone_id}', 
		'ReportCtrl@consessionare_report_step1_new_consessionare_pdf');   
		
Route::get('/billing/consessionare_report_step1_pending_approval_pdf/{date1}/{zone_id}', 
		'ReportCtrl@consessionare_report_step1_pending_approval_pdf');

Route::get('/billing/consessionare_report_step1_voluntary_disconnection_pdf/{date1}/{zone_id}', 
		'ReportCtrl@consessionare_report_step1_voluntary_disconnection_pdf');
		

Route::get('/report_adjustment_report_pdf/{date1}/{date2}', 
		'ReportCtrl@report_adjustment_report_pdf');


Route::get('/billing/cancel_last_billing/{ledger_id}', 
		'LedgerCtrl@cancel_last_billing');

Route::get('/billing/cancel_this_adjustment/{ledger_id}', 
		'LedgerCtrl@cancel_this_adjustment');

Route::get('/billing/cancel_this_penalty/{ledger_id}', 
		'LedgerCtrl@cancel_this_penalty');
		
		



Route::get('/save_update_new_date/{coll_id}/{new_date}', 
		'CashierCtrl@save_update_new_date');   

Route::get('/penalty0001/get_apply_penalty_for_billing_zone_list/{period1}', 'PenaltyCtrl@get_apply_penalty_for_billing_zone_list');

//+$pen_date+'/'+$zone_id+'/'+$period;
Route::get('/penalty0001/execute_penalty_by_zone_id_pen_date/{pen_date}/{zone_id}/{period}', 
			'PenaltyCtrl@execute_penalty_by_zone_id_pen_date');


Route::get('/collections/coll1/print_or222/{acct_id}', 
		'CashierCtrl@print_or111');   
		

Route::get('/reports/v2/for_disconnection', 
		'ReportCtrl@for_disconnection');   


Route::get('/disconnection_notice_list/v1/{date1}/{zone_id}/{acct_id}', 
		'PrintCtrl@disconnection_notice_list');   
		
Route::get('/make_for_disconnection_status/v1', 
		'ConnectionLedgerCtrl@make_for_disconnection_status');   

Route::get('/for_disconnection_list/v1/{date1}/{zone_id}/{acct_id}', 
		'ConnectionLedgerCtrl@for_disconnection_list');   


Route::get('/for_disconnection_list/v2/{date1}/{zone_id}/{acct_id}', 
		'PrintCtrl@for_disconnection_list_v2');   
		
		

Route::get('/for_disconnection_status_disconnect/v1', 
		'ConnectionLedgerCtrl@for_disconnection_status_disconnect');   

Route::get('/collections/get_receipt_html11', 
		'CashierCtrl@get_receipt_html11');   

Route::get('/collections/withholding_breakdown_html1', 
		'CashierCtrl@withholding_breakdown_html1');   


Route::get('/billing_billing_add_rates_meter_size/v1', 
		'BillingCtrl@billing_billing_add_rates_meter_size');   
		
Route::get('/billing_billing_add_rates_meter_size_redirect/v1', 
		'BillingCtrl@billing_billing_add_rates_meter_size_redirect');   
		
Route::get('/billing_billing_get_meter_sizes1001/v1', 
		'BillingCtrl@billing_billing_get_meter_sizes1001');   

Route::get('/billing_billing_get_price_rates_list/v1', 
		'BillingCtrl@billing_billing_get_price_rates_list');   


//Report
Route::get('/report_get_collectors', 
			'ReportCtrl@report_get_collectors');   
		


Route::get('/report_generate_reading_book/{date1}/{zone1}', 
			'ReportCtrl@report_generate_reading_book');   


Route::any('/create_remote_collection_session/rem-coll-{date1}', 'CashierCtrl@create_remote_collection_session');


//Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home', function(){

	$user1 = Auth::user();
	$id = $user1->id;

	$cur_roles = $user1->roles->toArray();

	if(empty($cur_roles))
    {
		return redirect('/');
	}
	
	switch($cur_roles[0]['name'])
     {

		case 'super_admin':
		case 'general_manager':
		case 'admin':
				return redirect('/admin');
		break;

		case 'billing_admin':
				return redirect('/billing');
		break;

		case 'collection_officer':
				return redirect('/collections');
		break;
		
		case 'accounting':
				return redirect('/accounting');
		break;		

	}


	return redirect('/');

	echo '<pre>';
	print_r($cur_roles[0]['name']);

});

Route::group(['prefix' => '/accounting',  'middleware' =>  ['auth', 'role_type:accounting'] ], function(){
	//~ Route::get('/',  'AccountingCtrl@');
});




Route::group(['prefix' => '/billing' ,  'middleware' =>  ['auth', 'role_type:billing_admin,admin']], function(){

	Route::get('/',  'BillingCtrl@index');

	/*********/
	$__billing_acounts = 0;
	Route::get('/accounts',  'BillingCtrl@AccountMain');
	#Route::get('/accounts/{acct_num}/{meter_num}/{lname}/{zone}',  'BillingCtrl@AccountFilter');
	
	
	Route::get('/accounts/rebill_from_reading/{read_id}/{acct_id}/{acct_no}',  'BillingCtrl@rebill_from_reading');
	
	# BILLING / ACCOUNTS
	Route::get('/accounts/{acct_num}/{meter_num}/{lname}/{zone}/{acct_status}/{beg_stat}',  'BillingCtrl@AccountFilter');
	Route::post('/accounts_update_route',  'BillingCtrl@accounts_update_route');

	Route::get('/accounts_pdf/{acct_num}/{meter_num}/{lname}/{zone}/{acct_status}/{beg_stat}',  'BillingCtrl@AccountFilterPDF');

	Route::post('/accounts/new',  'BillingCtrl@AccountNew');
	Route::post('/accounts/update',  'BillingCtrl@AccountUpdate');
	//~ Route::get('/accounts/delete',  'BillingCtrl@AccountDelete');
	
	Route::get('/accounts/add_beginning_bal_amt/{amnt}/{acct_id}/{acct_no}',  'BillingCtrl@AddBeginningBalAmt');
	Route::get('/accounts/delete_account/{acct_id}/{acct_no}',  'BillingCtrl@DeleteAccount');
	Route::get('/accounts/disconnect_account/{acct_id}/{acct_no}',  'BillingCtrl@DisconnectAccount');
	Route::get('/accounts/reconnect_account/{acct_id}/{acct_no}',  'BillingCtrl@ReconnectAccount');
	
	
	/*********/

	/*********/
	$__billing_acount_type = 0;
	Route::post('/account_type/new',  'BillingCtrl@AccountTypeNew');
	Route::post('/account_type/update',  'BillingCtrl@AccountTypeUpdate');
	Route::get('/account_type/delete',  'BillingCtrl@AccountTypeDelete');
	/*********/

	/*********/
	$__billing_acount_status = 0;
	Route::post('/account_status/new',  'BillingCtrl@AccountStatusNew');
	Route::post('/account_status/update',  'BillingCtrl@AccountStatusUpdate');
	Route::get('/account_status/delete',  'BillingCtrl@AccountStatusDelete');
	/*********/
	/*********/
	$__billing_zones = 0;
	Route::post('/zone/new',  'BillingCtrl@ZoneNew');
	Route::post('/zone/update',  'BillingCtrl@ZoneUpdate');
	Route::get('/zone/delete',  'BillingCtrl@ZoneDelete');
	/*********/

	/*********/
	$__billing_reading = 0;
	Route::get('/reading',  'BillingCtrl@ReadingMain');
	
	Route::get('/reading/execut_billing_10011/{zone_id}/{period}',  'BillingCtrl@execut_billing_10011');
	
	
	Route::get('/reading/{r_year}/{r_month}',  'BillingCtrl@ReadingWithDate');
	Route::get('/reading/{r_year}/{r_month}/filter/{acct_num}/{meter_num}/{lname}/{zone}',  'BillingCtrl@ReadingWithDateWithFilter');


	Route::post('/reading/update_previous_reading',  'BillingCtrl@update_previous_reading');

	Route::post('/reading/update_current_reading',  'BillingCtrl@ReadingUpdateCurrentReading');
	Route::post('/reading/update_init_reading',  'BillingCtrl@ReadingUpdateInitReading');

	Route::post('/reading/new_meter_officer',  'BillingCtrl@ReadingNewMeterOfficer');
	Route::post('/reading/update_meter_officer',  'BillingCtrl@ReadingUpdateMeterOfficer');
	Route::post('/reading/add_meter_number_act1',  'BillingCtrl@ReadingAddMeterNumber');
	
	Route::post('/reading/add_reading_period',  'ReadingCtrl@addReadingPeriod');
    Route::get('/reading_period_start/initilize_start/{date1}',  'ReadingCtrl@ReadingPeriodInitializeStart');
    Route::get('/reading_period_start/initilize_start_v2/{period1}/{zone_id}/{due_date}/{read_per_id}/{fine_date}/{read_date}',  'ReadingCtrl@initilize_start_v2');
    Route::get('/reading_period_start/initilize_get_zones_counts/{period1}',  'ReadingCtrl@initilize_get_zones_counts');
    
    Route::get('/reading_period_start/start_for_billing_execute/{period1}',  'ReadingCtrl@start_for_billing_execute');
    Route::get('/reading_period_start/execute_billing_by_20/{period1}/{zone_id}/{pen_date}',  'ReadingCtrl@execute_billing_by_20');


	Route::post('/reading/add_route1',  'ReadingCtrl@addRoute01');
	
     
     
	
	
	/*********/

	$__billing_billing = 0;
	/*********/
	Route::get('/billing',  'BillingCtrl@BillingMain');
	Route::get('/billing/print_bill',  'BillingCtrl@print_bill');
	
    Route::get('/billing/save_billing_number/{bill_id}',  'BillingCtrl@save_billing_number');
    Route::get('/billing/save_penalty_date/{bill_id}',  'BillingCtrl@save_penalty_date');
	
    
    Route::get('/billing/start_printing_service/{req_id}',  'BillingCtrl@StartPrintingService');
	Route::get('/billing/overdue_add_job/{zone_id}/{date1}',  'BillingCtrl@OverDueAddJob');
	Route::get('/billing/overdue_proccess_job/{job_id}',  'BillingCtrl@OverDueProccessJob');    
	Route::get('/billing/overdue_proccess_job_restart/{job_id}',  'BillingCtrl@OverDueProccessJobRestart');    
    
    
    
	Route::post('/billing/rates/add',  'BillingCtrl@BillingBilingRatesAdd');
	Route::post('/billing/rates/update',  'BillingCtrl@BillingBilingRatesUpdate');

	Route::post('/billing/discount/add',  'BillingCtrl@BillingBilingDiscountAdd');
	Route::post('/billing/discount/update',  'BillingCtrl@BillingBilingDiscountUpdate');
	
	
	Route::get('/billing/reprocess_bill/{bill_id}',  'BillingCtrl@BillingBilingReprocessBilling');
	
	
    Route::get('/billing/request/reprocess/{req_id}',  'BillingCtrl@RequestReProcess');
	Route::get('/billing/zone_bill_start/{zone_id}/{bill_period_id}',  'BillingCtrl@BillingBilingZoneBillingStart');

    Route::get('/billing/update_acct_type_process/{acct_type001}/{acct_no}/{acct_id}/{billing_id}',  'BillingCtrl@UpdateAcctTypeProcess');
    Route::get('/billing/fix_via_old_ledger/{acct_no}/{acct_id}/{billing_id}',  'BillingCtrl@FixViaOldLedger');
	//update_acct_type_process	
	
	Route::get('/billing/{r_year}/{r_month}',  'BillingCtrl@BillingMainDate');
	Route::get('/billing/{r_year}/{r_month}/filter/{acct_num}/{meter_num}/{lname}/{zone}',  'BillingCtrl@BillingMainDateFilter');
	

	
	

	$__billing_collection = 0;
	/*********/
	Route::get('/collection',  'BillingCtrl@CollectionMain');
	Route::get('/collection/search_account/{r_year}/{r_month}/filter/{acct_num}/{lname}',  'BillingCtrl@CollectionSearchAccount');
	Route::post('/collection/bank/add',  'BillingCollection@BankAdd');
	Route::post('/collection/bank/update',  'BillingCollection@BankUpdate');
	/*********/


     $_account_ledger = 0;
     Route::get('/account_ledger',  'LedgerCtrl@AccountLedgerMain');
     
     Route::get('/account_ledger/get_led_item/{led_id}',  'LedgerCtrl@get_led_item');
     Route::get('/account_ledger/disable_ledger_item/{led_id}',  'LedgerCtrl@disable_ledger_item');
     Route::get('/account_ledger/refresh_ledger_101/{acct_id}',  'LedgerCtrl@refresh_ledger_101');
    
     
     Route::get('/account_ledger/get_ledger_acct',  'LedgerCtrl@AccountLedgerGetAccount');
     Route::get('/account_ledger/add_bill_ajustment',  'LedgerCtrl@add_bill_ajustment');
     Route::get('/account_ledger/add_bill_ajustment_v2',  'LedgerCtrl@add_bill_ajustment_v2');
     Route::get('/account_ledger/view_ledger_account_info',  'LedgerCtrl@view_ledger_account_info');
     Route::get('/account_ledger/view_ledger_account_info_pdf',  'LedgerCtrl@view_ledger_account_info_pdf');
     
     Route::get('/account_ledger/get_ledger_acct/print_pdf',  'LedgerCtrl@AccountLedgerGetAccountPrintPdf1');
     Route::get('/account_ledger/get_ledger_acct/recalculate',  'LedgerCtrl@AccountLedgerRecalculate');
     
     Route::get('/account_ledger/get_ledger_acct/recalculate_v2',  'LedgerCtrl@recalculate_v2');
     
     
     Route::get('/account_ledger/get_ledger_acct/update_beginning',  'LedgerCtrl@AccountLedgerUpdateBeginning');
     Route::get('/account_ledger/get_ledger_acct/update_beginning_v2',  'LedgerCtrl@update_beginning_v2');
     
     Route::get('/account_ledger/get_ledger_acct/print_pdf_reading',  'LedgerCtrl@AccountLedgerGetAccountPrintPdf1Reading');
     Route::get('/account_ledger/get_ledger_acct/print_pdf_history',  'LedgerCtrl@AccountLedgerGetAccountPrintPdf1History');
     
     Route::get('/account_ledger/get_ledger_acct/{acct_id}',  'LedgerCtrl@AccountLedgerGetAccountById');
	 Route::get('/account_ledger/search_account/filter/{acct_num}/{meter_num}/{lname}',  'BillingCtrl@CollectionSearchAccount');
	 Route::get('/account_ledger/filter/{acct_num}/{meter_num}/{lname}/{zone}/{stype}',  'LedgerCtrl@AccountLedgerMain22');



	/*********/
	Route::post('/billing/period_request/add',  'BillingCtrl@BillingBilingRequestPeriod');
	Route::get('/billing/hwdjob/add1/{request_id}/{period}',  'BillingCtrl@BillingBilingHwdJobAdd1');
	/*********/


     /*********/
     Route::get('/reports',  'BillingCtrl@ReportsMain');


     Route::get('/reports/generate_audit_excel',  'AuditReportCtrl@generate_audit_excel');
	 

	 
     Route::get('/report_get_by_zone/{zone}/{month}/{full_date}',  'BillingCtrl@ReportGetByZone');
     Route::get('/report_get_by_zone/pdf/{zone}/{month}/{full_date}',  'BillingCtrl@ReportGetByZonePDF');

     Route::get('/report_account_recievable_summary/{zone}/{display_month}/{full_date}',  'BillingCtrl@ReportsAccountRecievableSummary');
     Route::get('/report_account_recievable_summary_pdf/{zone}/{display_month}/{full_date}',  'BillingCtrl@ReportsAccountRecievableSummaryPDF');
     Route::get('/report_account_recievable_summary_excel/{zone}/{display_month}/{full_date}',  'BillingCtrl@report_account_recievable_summary_excel');

     Route::post('/reports/add_new',  'BillingCtrl@ReportAddNew');
     Route::get('/reports/generate/{rid}',  'BillingCtrl@ReportStartGenerate');
     Route::get('/reports/regenerate/{rid}',  'BillingCtrl@ReportReGenerate');

     //Route::get('/report_get_account_balances1/{zone}',  'BillingCtrl@ReportGetBalances');
     Route::get('/report_get_account_balances1/{zone}/{full_date}',  'BillingCtrl@ReportGetBalances');
     Route::get('/report_get_account_balances_pdf/{zone}/{full_date}',  'BillingCtrl@ReportGetBalancesPDF');
     Route::get('/report_get_account_monthly_ending_balance/{full_date}',  'BillingCtrl@ReportGetBalancesMonthlyEnding');
     Route::get('/report_get_account_monthly_ending_balance_pdf/{full_date}',  'BillingCtrl@ReportGetBalancesMonthlyEndingPDF');
     /*********/

     Route::get('/report_get_delinquent_summary/{zone}/{full_date}',  'ReportCtrl@ReportGetDelinquentSummary1');
     Route::get('/report_get_delinquent_summary_pdf/{zone}/{full_date}',  'ReportCtrl@ReportGetDelinquentSummaryPDF1');
     
     

     Route::get('/report_get_report_acknowledgement/{zone}/{full_date}',  'ReportC	trl@ReportGetAcknowledgement');
     Route::get('/report_get_report_acknowledgement_pdf/{zone}/{full_date}',  'ReportCtrl@ReportGetAcknowledgementPDF');
     //Route::get('/report_get_report_acknowledgement_pdf/{zone}/{full_date}',  'BillingCtrl@ReportGetAcknowledgementPDF');
     

     Route::get('/daily_billing_get_ajax1/{zone}/{full_date}',  'BillingCtrl@ReportGetDailyBillingAjax');
     Route::get('/billing_summary_get_account_pdf/{zone}/{full_date}',  'ReportCtrl@BillingSummaryAccountPDF');
     Route::get('/billing_summary_get_zone_class_pdf/{full_date}',  'ReportCtrl@BillingSummaryZoneClassPDF');
     Route::get('/billing_summary_get_annual_pdf/{full_date}',  'ReportCtrl@BillingSummaryAnnualPDF');
     
     Route::get('/billing_summary_get_zone_class_pdf_all/{full_date}',  'ReportCtrl@billing_summary_get_zone_class_pdf_all');
     
	 Route::get('/logout',  'BillingCtrl@BillingLogout');


	/*************/
	Route::get('/clear_remote_temp_data',  'RemoteCollCtrl@clear_remote_temp_data');
	Route::get('/clear_temp_remote_collection_data',  'RemoteCollCtrl@clear_temp_remote_collection_data');
	/*************/

	/* METER CHECKER**/
	Route::get('/check_meter_available',  'BillingCtrl@check_meter_available');
	/***/

	#######
	# NW BILLING
	Route::any('/nwb_add_new',  'NonWaterBillCtrl@nwb_add_new');
	Route::any('/nwb_get_list',  'NonWaterBillCtrl@nwb_get_list');
	Route::any('/nwb_delete',  'NonWaterBillCtrl@nwb_delete');
	Route::any('/nwb_set_active',  'NonWaterBillCtrl@nwb_set_active');


	#######
	# Beginning Balance update
	Route::post('/save_beginning_balance',  'BegBalCtrl@save_beginning_balance');


});


$collections_group = 1;

Route::get('/collections/all_reports/daily',  'CashierCtrl@AllReportDaily');
Route::post('/collections/load_remote_collection_100010',  'CashierCtrl@load_remote_collection_100010');
Route::get('/collections/get_collection_uploaded_html1',  'CashierCtrl@get_collection_uploaded_html1');
Route::get('/collections/delete_this_upload/{upid}',  'CashierCtrl@delete_this_upload');
Route::get('/collections/load_collection_to_server/{upid}',  'CashierCtrl@load_collection_to_server');


Route::group(['prefix' => '/collections',  'middleware' =>  ['auth', 'role_type:collection_officer']], function(){

	 Route::get('/',  'CashierCtrl@main');
     Route::get('/logout',  'CashierCtrl@CashierLogout');
     Route::get('/invoices',  'CashierCtrl@Invoices');
     Route::get('/search/{an}/{ln}',  'CashierCtrl@searchAcct');
     Route::get('/invoices/get_current',  'CashierCtrl@InvoiceGetCurrent');
     Route::get('/print1',  'CashierCtrl@Print1');
     Route::get('/activities',  'CashierCtrl@Activities');
     
     Route::get('/reports',  'CashierCtrl@Reports');
     
     Route::get('/reports/daily',  'CashierCtrl@ReportDaily');//ReportDaily
     Route::get('/reports/for_disconnection/{zone_id}/{period}',  'CashierCtrl@ReportForDisconnection');
     Route::get('/reports/montly',  'CashierCtrl@ReportMonthly');
     //Route::get('/reports/annually',  'CashierCtrl@ReportAnnually');
     
     Route::get('/reports/summary/daily',  'CashierCtrl@ReportDailySummary');
     Route::get('/reports/summary/montly',  'CashierCtrl@ReportMonthlySummary');
     Route::get('/reports/summary/annually',  'CashierCtrl@ReportAnnuallySummary');     

     Route::post('/make_payment',  'CashierCtrl@makePayment');
     Route::post('/make_payment_non_water',  'CashierCtrl@makePaymentNonWater');
     Route::post('/invoices/new',  'CashierCtrl@InvoiceAddNew');
     
     Route::get('/coll1/get_acct_info_step1/{acct_id}', 
				'CashierCtrl@get_acct_info_step1');     
				
     Route::get('/coll1/make_payment_step1/{acct_id}', 
				'CashierCtrl@make_payment_step1');     				
				
     Route::get('/coll1/view_my_ledger101/{acct_id}', 
				'CashierCtrl@view_my_ledger101');     
				
     Route::get('/coll1/update_receipt_type/{coll_id}', 
				'CashierCtrl@update_receipt_type');     				

     Route::get('/coll1/add_none_water/{acct_id}', 
				'CashierCtrl@add_none_water');   
				  				
     Route::get('/coll1/print_or111/{acct_id}', 
				'CashierCtrl@print_or111');     		
				
     Route::get('/search_acct1_v1',  'CashierCtrl@search_acct1_v1');
     
								

});




$__services_all = 0;
$_report_generate1= 0;

Route::group(['prefix' => '/service'], function(){
	
	return;
	return;
	return;

	//Route::get('/billing_generate',  'ServiceCtrl@billingGenerate');
     Route::get('/generate_billing1',  'ServiceCtrl@generateBilling001');
     
	//Route::get('/generate_billing1/{period}',  'ServiceCtrl@generateBillingByPeriod');
	//Route::get('/due_generate/{period}',  'ServiceCtrl@DueGenerate');
	
     Route::get('/due_generate11',  'ServiceCtrl@DueGenerate11');
     Route::get('/due_generate22',  'ServiceCtrl@OverdueServiceStart');
     
     Route::get('/notice_disconnect_generate',  'ServiceCtrl@NoticeDisconnectGenerate');
	 
     //~ Route::get('/billing_printing_service/prepare',  'ServiceCtrl@BillingPrintingServicePrepare');
     //~ Route::get('/billing_printing_service/execute',  'ServiceCtrl@BillingPrintingServiceExecute');
     //Route::get('/billing_printing_service/print',  'ServiceCtrl@BillingPrintingServicePrint');

     Route::get('/report_generate1',  'ServiceCtrl@generateReport1');
     
     //~ Route::get('/billing_printing1',  'ServiceCtrl@BillingPrinting1');
     //~ Route::get('/testing/random_zone',  'ServiceCtrl@RandomZone1');
     
     Route::get('/reading_period_start',  'ServiceCtrl@ReadingPeriodStart');

});	

$__account_ajax= 0;
Route::group(['prefix' => '/ajax1'], function(){
     Route::get('/get_top3_reading/{account_id}',  'ReadingCtrl@getTop3ReadingHtml1');
     Route::get('/get_top3_billing/{account_id}',  'ReadingCtrl@getTop3BillingHtml1');
});	



Route::group(['prefix' => '/admin',  'middleware' =>  ['auth', 'role_type:admin,super_admin,general_manager'] ], function(){

	Route::get('/',  'AdminCtrl@index');
	Route::get('/all_requests',  'AdminCtrl@allRequests');

	Route::get('/get_collection_info_by_date1001/{date1}',  'AdminCtrl@get_collection_info_by_date1001');
	Route::get('/get_collection_info_by_date1001_monthly_summary/{date1}/{col_id}',  'AdminCtrl@get_collection_info_by_date1001_monthly_summary');
	

	Route::get('/request/accounts/approve_acct/{req_id}',  'AdminCtrl@requestApproveAccount');
	Route::get('/request/accounts/cancel_acct/{req_id}',  'AdminCtrl@requestCancelAccount');

	Route::get('/request/billing/approve/{req_id}',  'AdminCtrl@requestBillingApprove');
	Route::get('/request/billing/cancel/{req_id}',  'AdminCtrl@requestBillingCancel');

     Route::get('/request/invoice/approve/{req_id}',  'AdminCtrl@requestInvoiceApprove');
     Route::get('/request/invoice/cancel/{req_id}',   'AdminCtrl@requestInvoiceCancel');



	/*************/
	Route::get('/accounts',  'AdminCtrl@accountIndex');
	Route::get('/accounts/get/{acct}/{fname}/{zone}',  'AdminCtrl@accountGetSearch1_ajax');
	/*************/
	Route::get('/readings',  'AdminCtrl@readingIndex');

	Route::get('/collection',  'AdminCtrl@collectionIndex');
	Route::get('/adjustments',  'AdminCtrl@AdjusmentIndex');
	Route::get('/adjustments/search_name/{key1}',  'AdminCtrl@AdjusmentAjax01');
	Route::post('/adjustments/update_remarks_status',  'AdminCtrl@update_remarks_status');
	
	Route::get('/billing',  'AdminCtrl@billingIndex');

	Route::get('/system_account',  'AdminCtrl@systemAccountIndex');
	Route::post('/system_account/create_new_account',  'AdminCtrl@systemAccountCreateNewAccount');
	Route::post('/system_account/create_new_account_type',  'AdminCtrl@systemAccountCreateNewAccountType');
	Route::post('/system_account/edit_account',  'AdminCtrl@systemAccountEditAccount');
	
	//~ Route::get('/other_payable',  'AdminCtrl@otherPayableIndex');
	//~ Route::post('/other_payable/add_new',  'AdminCtrl@otherPayableAddNew');
	//~ Route::post('/other_payable/update',  'AdminCtrl@otherPayableUpdate');

	Route::get('/logout',  'AdminCtrl@systemAccountLogout');

	Route::get('/service/request001',  'AdminCtrl@ajaxRequest001');


	// GENERAL MANAGER

	Route::group(['prefix' => '/gm',  'middleware' =>  ['auth', 'role_type:general_manager']], function(){

		Route::get('/', function() {
			echo 'GM';
		});

		Route::get('/dashboard', 'GMCtrl@dashboard');

		Route::get('/init', 'GMCtrl@INIT_NWB');
		Route::get('/init_monthly_collection', 'GMCtrl@init_monthly_collection');
		Route::get('/init_daily_collection', 'GMCtrl@init_daily_collection');
		Route::get('/daily_collection_report', 'GMCtrl@daily_collection_report');


	});


	


});

//~ Route::group(['prefix' => '/admin',  'middleware' =>  ['auth', 'role_type:admin,super_admin'] ], function(){
//~ });

Route::get('/admin/other_payable',  'AdminCtrl@otherPayableIndex')->middleware('auth', 'role_type:admin,super_admin,billing_admin');
Route::post('/admin/other_payable/add_new',  'AdminCtrl@otherPayableAddNew')->middleware('auth', 'role_type:admin,super_admin,billing_admin');
Route::post('/admin/other_payable/update',  'AdminCtrl@otherPayableUpdate')->middleware('auth', 'role_type:admin,super_admin,billing_admin');


$__readings = 00;
Route::group(['prefix' => '/readings'], function(){
     //Route::get('/get_data1/{period1}',  'ReadingCtrl@getReadingInformaion1');
    Route::get('/get_data1/{period1}/{cid}',  'ReadingCtrl@getReadingInformaion1');
	Route::get('/get_zones/{uid}',  'ReadingCtrl@getZones001');
	Route::any('/upload_reading_data',  'ReadingCtrl@uploadReadingData');
	Route::any('/upload_reading_data/rnb_v1',  'ReadingCtrl@uploadReadingData_ReadNBill');
	Route::any('/officer_login',  'ReadingCtrl@OfficerLogin');
	Route::any('/update_gps_data_to_server',  'ReadingCtrl@update_gps_data_to_server');
	
});




$printing_start = 0;
//Route::any('/bill_print_start/{period}/{zone_id}/{acct_num}/{bill_num}',  'PrintCtrl@billPrintStart_List');
Route::any('/bill_print_start/{period}/{zone_id}/{acct_num}/{bill_num}/{bill_num_end}',  'PrintCtrl@billPrintStart_List');
Route::any('/before_printing_save_first/{period}/{zone_id}/{acct_num}/{bill_num}/{bill_num_end}',  'PrintCtrl@before_printing_save_first');
Route::any('/bill_print_save_billing_number/{period}/{zone_id}/{acct_num}/{bill_num}/{bill_num_end}',  'PrintCtrl@bill_print_save_billing_number');
Route::any('/reset_billing_number_by_zone/{period}/{zone_id}/{acct_num}/{bill_num}/{bill_num_end}',  'PrintCtrl@reset_billing_number_by_zone');




Route::any('/bill_print_start_001/{period}/{zone_id}/{acct_num}/{bill_num}',  'PrintCtrl@billPrintStart_Save');
Route::any('/bill_print_start_002/{period}/{zone_id}/{acct_num}/{bill_num}/{bill_num_end}',  'PrintCtrl@billPrintStart_Save_ajax');
Route::any('/bill_print_stop_service/{serv_id}',  'PrintCtrl@billPrintStopService');


Route::any('/disconnection_print_start/{zone_id}/{period}/{acct_num}',  'PrintCtrl@disconnectionPrintStart_List');
Route::get('/fix_ledger_zorting/{acct_id}', 'LedgerCtrl@fix_ledger_zorting');//UPDATE NEW CUSTOMER
Route::any('/disconnection_notice_print_pdf/{zone_id}/{period}/{dis_date}',  'PrintCtrl@disconnection_notice_print_pdf');
			


/***************************/
/***************************/
/***************************/
/***************************/
//~ Route::get('/update_new_customer_101', 'HwdJobCtrl@update_new_customer_101');//UPDATE NEW CUSTOMER
//~ Route::get('/update_senior_001', 'HwdJobCtrl@update_senior_001');//SENIOR
//~ Route::get('/load_prev_reading', 'HwdJobCtrl@load_prev_reading');//Load Previous reading
//~ Route::get('/update_beginning_balance101', 'HwdJobCtrl@update_beginning_balance101');//Load Psrevious reading


//~ Route::get('/fix_senior_feb_billing', 'HwdJobCtrl@fix_senior_feb_billing');//UPDATE NEW CUSTOMER
//~ Route::get('/fix_date_bill_read_ledger101/{zone_id}/{perxx}/{read_date}', 'HwdJobCtrl@fix_date_bill_read_ledger101');//UPDATE NEW CUSTOMER

//~ Route::get('/add_penalty_to_user_manual', 'HwdJobCtrl@add_penalty_to_user_manual');//UPDATE NEW CUSTOMER
// Route::get('/update_zorting101/{acct_id}', 'HwdJobCtrl@update_zorting101');//UPDATE NEW CUSTOMER

//~ Route::get('/zzz_temp001', 'HomeController@zzz_temp001');//UPDATE NEW CUSTOMER
//~ Route::get('/zzz_temp002', 'HomeController@zzz_temp002');//UPDATE NEW CUSTOMER
// Route::get('/zzz_temp003', 'HomeController@zzz_temp003');//UPDATE NEW CUSTOMER
//~ Route::get('/update_account_type_1001', 'HomeController@update_account_type_1001');//UPDATE NEW CUSTOMER



// Route::get('/collection_ledger_update_dec252020', 'HwdJobCtrl@collection_ledger_update_dec252020');//Begining Active
// Route::get('/collection_ledger_update_dec272020/{cid}', 'HwdJobCtrl@collection_ledger_update_dec272020');//Begining Active

// Route::get('/execute_refresh_ledger_by_zone', 'HwdJobCtrl@execute_refresh_ledger_by_zone');//UPDATE NEW CUSTOMER

// Route::get('/tidman_mawis_fix1001', 'HwdJobCtrl@tidman_mawis_fix1001');//UPDATE NEW CUSTOMER
// Route::get('/tidman_mawis_fix1002', 'HwdJobCtrl@tidman_mawis_fix1002');//UPDATE NEW CUSTOMER
//Route::get('/fix_penalty_z6789_feb_2021', 'HwdJobCtrl@fix_penalty_z6789_feb_2021');//UPDATE NEW CUSTOMER
// Route::get('/test00020', 'HwdJobCtrl@test00020');//UPDATE NEW CUSTOMER


// Route::get('/fix_z7_z8_penalty0001', 'HwdJobCtrl@fix_z7_z8_penalty0001');//UPDATE NEW CUSTOMER
// Route::get('/fix_z7_z8_penalty0002', 'HwdJobCtrl@fix_z7_z8_penalty0002');//UPDATE NEW CUSTOMER


