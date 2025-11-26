<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{

	function adjustment1()
	{
		return $this->hasMany('App\BillingAdjMdl', 'acct_id', 'id')
				->orderBy('id', 'desc');
	}
	

	function acct_status(){
		return $this->hasOne('App\AccountMetas', 'id', 'acct_status_key');
	}


	function export_bill(){
		return $this->hasMany('App\ExportBilling', 'A', 'acct_no')
				//~ ->where('period', 'like', '2018%')
				->orderBy('id', 'desc');
	}

	function readings(){
		return $this->hasMany('App\Reading', 'account_id', 'id')
				->where('period', 'like', '2018%')
				->orderBy('id', 'desc');
	}

	function reading1(){
		return $this->hasOne('App\Reading', 'account_id', 'id')
				//->where('period', 'like', $n.'%')
				->orderBy('id', 'desc');
	}

	function reading_prev(){
		return $this->hasOne('App\Reading', 'account_id', 'id')
				//->where('period', 'like', $n.'%')
				->orderBy('id', 'desc');
	}

	function reading_billed(){
		return $this->hasMany('App\Reading', 'account_id', 'id')
					->orderBy('id', 'desc');
	}

	function  read33(){
		return $this->hasMany('App\Reading', 'account_id', 'id')
			->where('status', 'active')
			->orderBy('id', 'desc');
	}


	/********/
	//DONT REMOVE THE LIMIT AND ORDER BY
	//That will affect Results of Collections
	function  reading3(){
		return $this->hasMany('App\Reading', 'account_id', 'id')
			//->whereNotNull('bill_stat')
			->where('status', 'active')
			->where('bill_stat', 'billed')
			->orderBy('id', 'desc');
		/*
		return $this->hasMany('App\Reading', 'account_id', 'id')
			->where('status', 'active')
			->orderBy('id', 'desc')
			->limit(3);
			*/
	}
	//DONT REMOVE THE LIMIT AND ORDER BY


	function  request001(){
		return $this->hasOne('App\HwdRequests', 'reff_id', 'id')
			->orderBy('id', 'desc');
	}

	
	

	function read_officer_curr(){
		return $this->hasOne('App\Reading', 'account_id', 'id')
						->orderBy('id', 'desc');
	}

	function read_officer_prev(){
		return $this->hasOne('App\Reading', 'account_id', 'id')
						->orderBy('id', 'desc');
	}

	function billing22()
	{
		return $this->hasMany('App\BillingMdl', 'account_id', 'id')
						->orderBy('id', 'desc');
	}

	function billing_all()
	{
		return $this->hasMany('App\BillingMdl', 'account_id', 'id')
						->orderBy('period', 'desc');
	}

	function billing_40()
	{
		return $this->hasMany('App\BillingMdl', 'account_id', 'id')
						->orderBy('period', 'asc');
	}

	function billing_41()
	{
		return $this->hasOne('App\BillingMdl', 'account_id', 'id')
						->orderBy('period', 'asc');
	}

	function billing_42()
	{
		return $this->hasOne('App\BillingMdl', 'account_id', 'id')
						->orderBy('period', 'asc');
	}

	
	function bill1()
	{
		return $this->hasOne('App\BillingMdl', 'account_id', 'id')
						->orderBy('period', 'desc');
	}	
	
	

	function first_bill()
	{
		return $this->hasOne('App\BillingMdl', 'account_id', 'id')
						->orderBy('period', 'asc');
	}	


	function ageing()
	{
		return $this->hasMany('App\AgingRecievable', 'user_id', 'id')
						->orderBy('period', 'desc');
	}

	function reports()
	{
		return $this->hasMany('App\Reports', 'user_id', 'id');
						//->orderBy('period', 'desc');
	}

	//HwdLedger
	function ledger_last_20()
	{
		return $this->hasMany('App\HwdLedger', 'led_key1', 'id')
				->where('led_type','account')
				->orderBy('id', 'desc');
	}

	function my_zone()
	{
		return $this->hasOne('App\Zones', 'id', 'zone_id');
	}

	function my_stat()
	{
		return $this->hasOne('App\AccountMetas', 'id', 'acct_status_key');
	}

	function pre_bill()
	{
		//~ return $this->hasOne('App\ExportBilling', 'id', 'acct_no');
		return $this->hasMany('App\ExportBilling', 'B', 'acct_no');
	}
	
	function ledger_data()
	{
		//~ return $this->hasMany('App\LedgerData', 'acct_id', 'id');
		return $this->hasMany('App\LedgerData', 'acct_num', 'acct_no');
	}
	
	function ledger_data2()
	{
		return $this->hasMany('App\LedgerData', 'acct_id', 'id');
	}

	function ledger_data3()
	{
		return $this->hasOne('App\LedgerData', 'acct_id', 'id')
				->orderBy('id', 'desc');
	}

	function ledger_data4()
	{
		return $this->hasOne('App\LedgerData', 'acct_id', 'id')
				->orderBy('id', 'desc');
	}

	function ledger_data5()
	{
		return $this->hasOne('App\LedgerData', 'acct_id', 'id')
				->orderBy('id', 'desc');
	}

	function ledger_data6()
	{
		return $this->hasMany('App\LedgerData', 'acct_id', 'id');
	}

	function ledger_data7()
	{
		return $this->hasOne('App\LedgerData', 'acct_id', 'id')
				->where('status','active')
				->orderBy('id', 'desc');
	}


	function arrears()
	{
		return $this->hasMany('App\Arrear', 'acct_id', 'id');
	}

	function arrears2()
	{
		return $this->hasOne('App\Arrear', 'acct_id', 'id');
	}
	
	function arrears3()
	{
		return $this->hasOne('App\Arrear', 'acct_id', 'id')
				->orderBy('id', 'desc');
	}
	
	function arrear_last()
	{
		return $this->hasOne('App\Arrear', 'acct_id', 'id')
				->orderBy('id', 'desc');
	}

	function collect22()
	{
		return $this->hasMany('App\Collection', 'cust_id', 'id');
	}

	function collect23()
	{
		return $this->hasMany('App\Collection', 'cust_id', 'id');
	}
	
	function gps_data()
	{
		return $this->hasMany('App\GpsAcct', 'acct_id', 'id');
	}
	
	function last_gps_data()
	{
		return $this->hasOne('App\GpsAcct', 'acct_id', 'id')
				->orderBy('id', 'desc');
	}


	function remote_generate()
	{
		$date01 = date('Y-m-d');
		return $this->hasOne('App\RemoteGenarate', 'acct_id', 'id')
					->where('date01', $date01);
	}	
	
	function nwb_billing()
	{
		return $this->hasMany('App\BillingNw', 'acct_id', 'id')
					->where('typ', 'nw_child');
					// ->where('status', 'billed');
	}

}//
