<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
	
	function meta_name()
	{
			return $this->hasOne('App\AccountMetas', 'id', 'acct_type_id');
	}
	
}
