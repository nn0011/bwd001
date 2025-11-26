<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HwdRequests extends Model
{

	function account()
	{
		return $this->belongsTo('App\Accounts', 'reff_id', 'id');
	}

	function billing_due()
	{
		return $this->hasOne('App\HwdRequests', 'reff_id', 'id')
			->where('req_type', 'billing_due_request');
	}
	
	function bill_zone()
	{
		return $this->hasOne('App\ServiceBillZone', 'bill_period_id', 'id');
	}

}
