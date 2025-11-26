<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
		function billing(){
				return $this->hasOne('App\BillingMdl', 'reading_id', 'id')
								->where('status', 'active');
		}
		
		function billing2(){
				return $this->hasOne('App\BillingMdl', 'reading_id', 'id');
		}		
		
		function account1(){
			return  $this->belongsTo('App\Accounts', 'account_id', 'id');
		}
		
		function old_reading(){
				return $this->hasOne('App\Reading', 'account_id', 'account_id')
								->orderBy('id', 'desc');
		}
		
		function init_ledger()
		{
				return $this->hasOne('App\HwdLedger', 'led_key3', 'id')
								->where('led_type', 'init_reading_ledger')
								->orderBy('id', 'desc');
		}
		
}
