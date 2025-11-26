<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{

     function  billing()
     {
          return  $this->belongsTo('App\BillingMdl', 'billing_id', 'id');
     }

     function  accounts()
     {
          return  $this->belongsTo('App\Accounts', 'cust_id', 'id');
     }

     function  ledger_all(){
          return  $this->hasMany('App\LedgerData', 'acct_id', 'cust_id');
	 }
	      
     function  ledger(){
          return  $this->hasOne('App\LedgerData', 'acct_id', 'cust_id')
					->orderBy('id','desc');
          //~ return  $this->hasOne('App\LedgerData', 'reff_no', 'id');
	 }
	 
     function  ledger2(){
          return  $this->hasOne('App\LedgerData', 'acct_id', 'cust_id');
	 }
	 
     function  arrear(){
          return  $this->hasOne('App\Arrear', 'acct_id', 'cust_id');
	 }

     function  last_col(){
          return  $this->hasOne('App\Collection', 'invoice_num', 'invoice_num')
						->orderBy('id', 'desc');
	 }
	 
     function  rep1(){
          return  $this->hasOne('App\report1', 'coll_id', 'id')
						->orderBy('id', 'desc');
	 }//
	 
	 
	 // ADDED  DEC 30, 2020 START
		  function  paymet_ledger(){
			  return  $this->hasMany('App\PayPenLed', 'cid', 'id');
	  }
	 // ADDED  DEC 30, 2020 END	 

      function  coll_ledger(){
          return  $this->hasMany('App\LedgerData', 'coll_id', 'id');
	 }

      function  all_my_ledger(){
          return  $this->hasMany('App\LedgerData', 'acct_id', 'cust_id');
	 }

      function collect_report_ledger()
      {
          return  $this->hasOne('App\CollectLedger', 'coll_id', 'id');
      }


      function get_collection_break($date1)
      {
          $results = DB::select("
               SELECT 
               (
               SELECT 
                    SUM(payment) monthly_ttl 
               FROM collections 
               WHERE id IN (
                    SELECT MAX(id) FROM `collections`
                    WHERE payment_date like ?
                    GROUP BY invoice_num
               )
               AND status IN ('active', 'collector_receipt')        
               ) as SUM_WB,
               (
               SELECT 
                    SUM(payment) monthly_ttl 
               FROM collections 
               WHERE id IN (
                    SELECT MAX(id) FROM `collections`
                    WHERE payment_date like ?
                    GROUP BY invoice_num
               )
               AND status IN ('or_nw', 'cr_nw')        
               ) as SUM_NWB


          ", [$date1, $date1]);


      }//


}
