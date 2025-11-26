<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceBillZone extends Model
{
	
	function bill_request(){
		return $this->belongsTo('App\HwdRequests', 'bill_period_id', 'id');
	}
	
}
