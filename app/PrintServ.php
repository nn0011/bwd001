<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrintServ extends Model
{

	function zone_info()
	{
		return $this->hasOne('App\Zones', 'id', 'zone_id');
	}
	
}
