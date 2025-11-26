<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exp1 extends Model
{

    protected $table = 'exp1';
    
    function acct1(){
          return  $this->belongsTo('App\Accounts', 'A', 'acct_no');
	}
	
    //~ function acct2(){
          //~ return  $this->belongsTo('App\Accounts', 'B', 'acct_no');
	//~ }
	

}
