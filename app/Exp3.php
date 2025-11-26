<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exp3 extends Model
{
    protected $table = 'exp3';
    
    function acct2(){
          return  $this->belongsTo('App\Accounts', 'A', 'acct_no');
	}
	
	function reading(){
          return  $this->hasOne('App\Reading', 'account_number', 'A');
	}
	
    
}
