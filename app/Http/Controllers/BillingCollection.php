<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Bank;
use App\Collection;
use App\Accounts;
use App\BillingMdl;

//Bank

class BillingCollection extends Controller
{
	
	function BankAdd(Request $request)
	{
		
		//~ echo '<pre>';
		//~ print_r($_POST);
		
		$bb = new Bank;
		$bb->bank_name = trim(@$_POST['bank_name']);
		$bb->bank_info = trim(@$_POST['bank_desc']);
		$bb->branches = trim(@$_POST['bank_branches']);
		$bb->save();
		
		
		$request->session()->flash('success', 'Bank added.');
		return Redirect::to(URL::previous() . "");
		
		
		/*		
		Array
		(
			[_token] => 8YiunP4Www5LSZ7sNcRSdsnwKFcmRXZkIEZVruyd
			[bank_name] => BDO
			[bank_desc] => BDO
			[bank_stat] => active
		)*/		
	}
	
	function BankUpdate(Request $request)
	{
		$id = @$_POST['id'];
		
		$bb = Bank::find($id);
		$bb->bank_name = trim(@$_POST['bank_name']);
		$bb->bank_info = trim(@$_POST['bank_desc']);
		$bb->status = trim(@$_POST['bank_stat']);
		$bb->branches = trim(@$_POST['bank_branches']);
		$bb->save();
		
		$request->session()->flash('success', 'Bank updated.');
		return Redirect::to(URL::previous() . "");		
		
		//~ echo '<pre>';
		//~ print_r($_POST);		
	}
	
	
}
