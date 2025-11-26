<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExportBilling extends Model
{
    function reading001()
    {
		//~ return $this->hasOne('App\Reading', 'mrsn_old', '');
		return $this->hasOne('App\Reading', 'mrsn_old', 'F');
	}
	
	function acct01()
	{
		return $this->hasOne('App\Accounts',  'acct_no', 'B');
	}
	
	function acct02()
	{
		return $this->hasOne('App\Accounts',  'acct_no', 'A');
	}	

	//~ function ledger_data()
	//~ {
		//~ return $this->hasOne('App\LedgerData',  'acct_no', 'A');
	//~ }	
	
	function arrear1()
	{
		return $this->hasOne('App\ExportBilling',  'A', 'A')
			->where('I', '3');
			//->whereRaw('K=J');
	}
	
	function penalty1()
	{
		return $this->hasOne('App\ExportBilling',  'A', 'A')
			->where('I', '10');
	}	
	
	
}
