<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class BillingMdl extends Model
{
	
		function collection_by_user(){
			return $this->hasMany('App\Collection', 'cust_id', 'account_id')
				->where('status', 'active')
				->orderBy('id', 'desc');
		}

		function collection(){
			return $this->hasMany('App\Collection', 'billing_id', 'id')
				->where('status', 'active')
				->orderBy('id', 'desc');
		}

		function collection_total()
		{
			return $this->hasOne('App\Collection', 'billing_id', 'id')
				->selectRaw('SUM(payment) as total_payed, billing_id, payment_date')
				->where(function($query){
					$query->where('status', 'active');
					$query->orWhere('status', 'collector_receipt');
				})
				->groupBy('billing_id');
		}

		function due1(){
				return $this->hasOne('App\BillingDue', 'bill_id', 'id')
					->where('billing_mdls.status', 'active')
					->orderBy('billing_mdls.id', 'desc');
		}
		
		function due2(){
				return $this->hasMany('App\BillingDue', 'bill_id', 'id');
					//->where('billing_mdls.status', 'active')
		}

		function due3(){
				return $this->hasOne('App\BillingDue', 'bill_id', 'id');
					//->where('billing_mdls.status', 'active')
		}
		
		function arrear(){
				return $this->hasMany('App\Arrear', 'acct_id', 'account_id');
					//->where('billing_mdls.status', 'active')
		}
		
		function arrear2()
		{
				return $this->hasOne('App\Arrear', 'acct_id', 'account_id')
						->orderBy('id', 'desc');
		}
		
		function bill_arrear()
		{
				return $this->hasOne('App\Arrear', 'acct_id', 'account_id')
						->orderBy('id', 'desc');
		}
		
		function coll1(){
			return $this->hasMany('App\Collection', 'cust_id', 'account_id')
				->where('status', 'active')
				->orderBy('id', 'desc');
		}		
		
		function coll2($date_start, $date_end){
			return $this->hasMany('App\Collection', 'cust_id', 'account_id')
				->where('payment_date','>=', $date_start)
				->where('payment_date','<=', $date_end)
				->where('status', 'active')
				->orderBy('id', 'desc');
		}		
		
		

		function  collSum(){
				return  $this->collection()->sum('payment');
		}

		function  reading1(){
				return  $this->belongsTo('App\Reading', 'reading_id', 'id');
		}

		function  reading_back1(){
				return  $this->belongsTo('App\Reading', 'account_id', 'account_id');
		}

		function  reading_back2(){
				return  $this->belongsTo('App\Reading', 'account_id', 'account_id');
		}

		function  reading_back3(){
				return  $this->belongsTo('App\Reading', 'account_id', 'account_id');
		}

		function  account(){
				return  $this->belongsTo('App\Accounts', 'account_id', 'id');
		}

		function  account2(){
				return  $this->hasOne('App\Accounts', 'id', 'account_id');
		}
		
		function print1()
		{
			return $this->hasMany('App\BillPrint', 'bill_id', 'id');
		}
		
		function ledger1_begin()
		{
			return $this->hasOne('App\HwdLedger', 'led_key3', 'id')
			->where('led_type', 'begining_balance');
		}
		
		function ledger_data()
		{
			return $this->hasOne('App\LedgerData', 'bill_id', 'id')
			->where('led_type', 'billing');
		}
		
		function ledger11()
		{
			return $this->hasMany('App\HwdLedger', 'led_key1', 'id');
		}
		
		function ledger12()
		{
			return $this->hasOne('App\LedgerData', 'acct_id', 'account_id');
		}
		
		function penalty4(){
				return $this->hasOne('App\BillingDue', 'acct_id', 'account_id');
		}		
		
		function senior1(){
				return $this->hasOne('App\BillingAdjMdl', 'ref_no', 'account_id')	
								->where('adj_typ', 'like', SENIOR_ID.'|'.'%');
		}		
		
		function nw_bill()
		{
			return $this->hasMany('App\BillingNw', 'acct_id', 'account_id');
		}


}
