<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\AccountMetas;
use App\Accounts;
use App\Zones;
use App\Reading;
use App\BillingMdl;
use App\BillingMeta;
use App\Collection;
use App\Invoice;
use App\LedgerData;


class ExportCtrl extends Controller
{
	
	function exp_accounts($zone_id)
	{
		
		//~ $acct1 = Accounts::where('zone_id', $zone_id)
					//~ ->where('status','active')
						//~ ->get();
						
		$acct1 = DB::select("
							SELECT
							 
								AA.id,
								AA.acct_no,
								AA.fname,
								AA.lname,
								AA.zone_id,
								AA.acct_type_key,
								AA.acct_status_key,
								AA.status,
								AA.meter_number1,
								AA.route_id,
								AA.meter_size_id
								  
							FROM accounts	AS AA 
							
							WHERE AA.zone_id=? AND status != 'delete'
							
							", [$zone_id]);
		
		
		foreach($acct1 as $b)
		{
			$b->rem_bal=0;
			$b->led1  = null;
			$led1 = LedgerData::
						where('acct_id', $b->id)
						->selectRaw('
							id, 
							acct_id, 
							ttl_bal,
							date01,
							period, 
							acct_num,
							arrear,
							billing,
							payment,
							discount,
							penalty,
							reading,
							consump,
							reff_no,
							bill_adj,
							coll_id,
							nw_reff,
							nw_type,
							nw_desc,
							ledger_info,
							led_type
							')
						->where('status','active')
						->orderBy('id', 'desc')
							//~ ->first();
							->limit(5)
							->get();
			if($led1){
				//~ $b->rem_bal=$led1->ttl_bal;
				$b->led1=$led1->toArray();
			}
			
			
		}
		
		echo '<pre>';
		print_r($acct1);
		
		return array('type'=>'data_export', 'accounts'=> $acct1, 'ver'=>time());
		
	}
	
	
}
