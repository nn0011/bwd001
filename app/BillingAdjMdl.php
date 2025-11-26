<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingAdjMdl extends Model
{
		function bill1()
		{
				return $this->hasOne('App\BillingMdl', 'account_id', 'acct_id')
							->orderBy('id', 'desc');
		}

		function acct()
		{
				return $this->hasOne('App\Accounts', 'id', 'acct_id')
								->orderBy('id', 'desc');
		}

		function ledger1()
		{
				return $this->hasOne('App\LedgerData', 'reff_no', 'id')
								->where('status', 'active')
								->where('led_type','adjustment')
								->orderBy('id', 'desc');
		}
		
		
		
}
