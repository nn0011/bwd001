<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempCollection extends Model
{
	function my_collection()
	{
		return $this->hasMany('App\Collection', 'invoice_num', 'invoice_num')
				->where('payment_date', '>=', '2025-06-01'); ## NEW SERVICE INVOICE IMPLEMENTED
	}
}
