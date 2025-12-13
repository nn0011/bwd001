<?php 


namespace App\Services\Collections;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// use App\Collection;
use App\LedgerData;
use App\Arrear;
use App\Collection;
use App\OtherPayable;
use App\HwdLedger;
use App\NwbBalance;
use App\PayPenLed;
use App\Accounts;
use App\Invoice;
use App\BillingMdl;
use App\BillingAdjMdl;
use App\User;

use Excel;

class CollectionService2
{

	static 
	function get_reading_period($date1)
	{
		$my_period = date("Y-m-01", strtotime($date1));

		$sql1 = "
			SELECT * FROM reading_periods
			WHERE period = '$my_period'		
			LIMIT 1
		";
		$res1 = DB::select($sql1);

		$return_data = [];
		foreach($res1 as $k => $v)
		{
			$return_data[$v->id] = (array) $v;
		}

		return $return_data;
		ee($return_data, __FILE__, __LINE__);
	}//
	
	

	static 
	function get_other_payables()
	{
		$sql1 = "SELECT * FROM other_payables where paya_stat='active' ";
		$res1 = DB::select($sql1);

		$return_data = [];
		foreach($res1 as $k => $v)
		{
			$return_data[$v->id] = (array) $v;
		}

		return $return_data;
		ee($return_data, __FILE__, __LINE__);
	}//

	static 
	function ledger_collection($date1, $collector_id)
	{
$sql1 = "

SELECT *, 
(
		SELECT 
			concat('[',
			group_concat(json_object(
								'id',id, 
								'acct_id', acct_id,
								'acct_num', acct_num,
								'date01', date01,
								'bill_id', bill_id,
								'reff_no', reff_no,
								'led_type', led_type,
								'debit01', debit01,
								'credit01', credit01,
								'ttl_bal', ttl_bal
							)), ']' ) GG
		FROM ledger_datas

		WHERE id > (

			SELECT id FROM ledger_datas
			WHERE
			acct_id=TAB1.acct_id 
			AND 
			id < TAB1.max_id
			AND
			ttl_bal <= 0
			order by date01 desc, zort1 desc, id desc
			LIMIT 1
		) 

		AND 
			id < TAB1.max_id
		AND 
			acct_id=TAB1.acct_id 
			
		order by date01 asc, zort1 asc, id asc
) TTT
FROM 
(
		SELECT 
		LD1.reff_no,
		LD1.coll_id,
		CC.collector_id,
		LD1.date01,
		LD1.acct_id,
		LD1.acct_num,
		max(LD1.id) max_id ,
		group_concat(LD1.id) ids ,
		group_concat(LD1.payment) payments,
		group_concat(LD1.credit01) credit01s,
		group_concat(LD1.led_type) led_types,
		LD1.status,
		AA.fname,
		AA.lname
		FROM 
		ledger_datas LD1
		LEFT JOIN accounts AA ON AA.id= LD1.acct_id
		LEFT JOIN collections CC ON CC.id = LD1.coll_id
		WHERE LD1.coll_id is not null
		AND LD1.date01='$date1'
		AND CC.collector_id=$collector_id
		AND LD1.credit01 > 0
		#AND reff_no=83937
		GROUP BY LD1.coll_id
		ORDER BY LD1.reff_no asc
) TAB1		

";

		$res1 = DB::select($sql1);
		return $res1;
		ee($res1, __FILE__, __LINE__);




	}//

}//


