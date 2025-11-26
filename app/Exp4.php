<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exp4 extends Model
{
	protected $table = 'zzz_temp002';
	
	function accts()
	{
		return $this->hasOne('App\Accounts', 'acct_no', 'B');
	}
	
}//
