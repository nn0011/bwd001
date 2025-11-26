<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingDue extends Model
{
    function account()
    {
		return $this->hasOne('App\Accounts', 'id', 'acct_id');
	}
}
