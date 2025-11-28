<?php

namespace App;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;

class LedgerData extends Model
{

	function previous(){
		// get previous  user
		return self::where('id', '<', $this->id)
				->orderBy('id','desc')
					->first();
	
	}
	

	
		//~ function prev()
		//~ {
				//~ return $this->hasOne('App\BillingMdl', 'reading_id', 'id')
								//~ ->where('status', 'active');
		//~ }
		
	
	function ledger_data_wb()
	{
		return $this->hasOne('App\LedgerData', 'acct_id', 'acct_id')
					->orWhere('led_type', 'adjustment')
						->orWhere('led_type', 'payment_cr')
						->orWhere('led_type', 'payment')
						->orWhere('led_type', 'wtax')
						->orWhere('led_type', 'beginning')
						->orderBy('id', 'desc');
	}//
	
	function pre_bill()
	{
		//~ return $this->hasMany('App\ExportBilling', 'B', 'acct_no');
	}		
	
	function acct()
	{
		return $this->hasOne('App\Accounts', 'id', 'acct_id');
	}
	
	function penalty()
	{//BillingDue
		return $this->hasMany('App\BillingDue', 'acct_id', 'acct_id');
	}
	function penalty2()
	{
		return $this->hasOne('App\BillingDue', 'acct_id', 'acct_id');
	}
	
	function arrear2()
	{
		return $this->hasOne('App\Arrear', 'acct_id', 'acct_id');
	}
	
	//~ function reading1(){
		//~ return $this->hasOne('App\Accounts', 'id', 'acct_id');
	//~ }
	
	function coll1()
	{
		//reff_no
		return $this->hasMany('App\Collection', 'billing_id', 'reff_no');
	}
	
	function adjustments()
	{
		return $this->hasOne('App\BillingAdjMdl', 'id', 'reff_no');
	}

	function adjust_me()
	{
		return $this->hasMany('App\BillingAdjMdl', 'bill_id', 'bill_id')
						->where('status', 'active');
	}

	
	function billing01()
	{
		return $this->hasOne('App\BillingMdl', 'id', 'bill_id');
	}


	function begin_paypenled()
	{
		return $this->hasMany('App\PayPenLed', 'uid', 'acct_id')
					->where('typ', 'beginning');
	}

	function wtax()
	{
		return $this->hasOne('App\LedgerData', 'coll_id', 'coll_id')
						->where('led_type', 'wtax')
							->where('status', 'active'); 
	}

	function zero_bal()
	{
		return $this->hasOne('App\LedgerData', 'acct_id', 'acct_id')
			->where('led_type', 'billing')
			->where('arrear','<=', '0')
			->where('status', 'active')
			->orderBy('id', 'desc');
	}

	function arrear_led()
	{
		return $this->hasOne('App\Arrear', 'acct_id', 'acct_id');
					// ->where('period', $this->period);
	}

	function arrear_led2()
	{
		return $this->hasOne('App\Arrear', 'period', 'period');
	}

	function collection()
	{
		return $this->hasOne('App\Collection', 'id', 'coll_id');
	}




	static
	function get_latest_ledger_logs($acct_id)
	{
		$SQL001 = "
				SELECT coll_id FROM 
					ledger_datas LD1
				WHERE 
				LD1.acct_id = ?
				AND
				LD1.ttl_bal <= 0
				AND
				EXISTS(
					SELECT * FROM collections COL1 WHERE  
						COL1.id=LD1.coll_id AND 
						LD1.acct_id=COL1.cust_id AND  
						COL1.status IN('cancel_cr','cancel_cr_nw','cancel_receipt', 'nw_cancel')
				)		

				ORDER BY  LD1.id  desc
		";


		$reslt2	= DB::select($SQL001, [$acct_id]);
		$reslt2 = json_decode(json_encode($reslt2), true);

		$coll_ids = [];
		foreach($reslt2 as $k => $v) { $coll_ids[] = $v['coll_id']; }
		$coll_ids = implode(',', $coll_ids);

		if( $coll_ids != '') {
			$coll_ids = ' AND ( coll_id NOT IN( '.$coll_ids.') OR coll_id is NULL  ) ';
		}


		// $coll_ids = '';
		// ee1($coll_ids, __FILE__, __LINE__);

		
		$sql1 = "
				SELECT * FROM ledger_datas 
				WHERE id > 
							(
								SELECT MAX(id) id FROM 
									ledger_datas LD1
								WHERE 
								acct_id = ?
								AND
								ttl_bal <= 0
								AND
								NOT EXISTS(
									SELECT * FROM collections COL1 WHERE  
										COL1.id=LD1.coll_id AND 
										LD1.acct_id=COL1.cust_id AND  
										COL1.status IN('cancel_cr','cancel_cr_nw','cancel_receipt', 'nw_cancel')
								)		

								ORDER BY  LD1.id  desc
								LIMIT 1			
							)	

				AND
					acct_id = ?

				$coll_ids
		";

		$reslt1	= DB::select($sql1, [$acct_id, $acct_id]);
		$reslt1 = json_decode(json_encode($reslt1), true);
		// $reslt1 = array_reverse($reslt1);

		// ee1($reslt2, __FILE__, __LINE__);
		// ee($reslt1, __FILE__, __LINE__);

		return $reslt1;
	}//

	
		
		
}
