<?php 


namespace App\Services\Billings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Accounts;
use App\RemoteGenarate;
use App\LedgerData;


class CollectionService
{


	static
	function test001($acct_id)
	{
		// $acct_id = @$_GET['acct_id'];

		$full_paid_made =  LedgerData::where('status', 'active')
								->where('acct_id', $acct_id)
								->where('ttl_bal','<=', '0' )
								->orderBy('date01', 'asc')
								->orderBy('zort1', 'desc')
								->orderBy('id', 'desc')
									->first();

		$remaining_payable_raw =  LedgerData::where('status', 'active')
											->where('acct_id', $acct_id)
											->with('payled_v2')
											->orderBy('date01', 'asc')
											->orderBy('zort1', 'asc')
											->orderBy('id', 'asc');

		$total_payed = LedgerData::whereIn('led_type', ['payment', 'payment_cr'])
									->where('acct_id', $acct_id)
									->where('status', 'active');

		$total_payed_canceled = LedgerData::whereIn('led_type', ['payment_cancel', 'cancel_cr'])
									->where('status', 'active')
									->where('acct_id', $acct_id);

		if( $full_paid_made )
		{
			$remaining_payable_raw->where('id', '>', $full_paid_made->id);
			$total_payed->where('id', '>', $full_paid_made->id);
			$total_payed_canceled->where('id', '>', $full_paid_made->id);
		}


		$total_payed = $total_payed->sum('payment');
		$total_payed_canceled = $total_payed_canceled->sum('payment');
		$remaining_payable_raw = $remaining_payable_raw->get();

		$total_payed = $total_payed_orig  = round($total_payed + $total_payed_canceled, 2);

		


		$return_array = [
			'total_payed' => $total_payed_orig,
			'ledger' => $remaining_payable_raw->toArray()
		];

		return $return_array;

		echo '<pre>';
		print_r( $return_array );
		// die();
								
	}//





    static
	function get_users_zones_accounts()
	{
		$my_collector = @$_GET['my_collector'];
		$my_zones	  = @$_GET['my_zones'];
		
		$acct1_raw = Accounts::whereIn('zone_id', $my_zones)
								->doesntHave('remote_generate')
									->orderBy('zone_id')
										->orderBy('route_id');
											// ->limit(300)
												// ->get();


		$details  = '<h1>'.$acct1_raw->count().' Accounts remainings </h1>';
		// die();												

		$acct1 = $acct1_raw->limit(300)->get();
		
		if($acct1->count() > 0) 
		{
			foreach($acct1 as $ac)
			{
				$ledger_001 = self::test001($ac->id);
				$ac->new_ledger = $ledger_001;
				RemoteGenarate::insert([
					'acct_id' => $ac->id,
					'date01' => date('Y-m-d'),
					'acct_data' => $ac->toJson(),
				]);
			}//
	
	
			echo '
				<h1>..PROCESSING <br /> Please Wait..</h1> <br />
				'.$details.'
				<script>
				setTimeout(()=>{
					window.location.reload();
				},1000);
				</script>
			';
			die();

		}
		//
		//
		//
		//

		$col1    = User::find($my_collector);
		$zon1    = Zones::whereIn('id', $my_zones)->get();
		$my_user = DB::select("SELECT * FROM users WHERE id=?",[$my_collector]);		
		$banks   = Bank::get();
		$reading_period = ReadingPeriod::get();


		$accts = RemoteGenarate::where('date01', date('Y-m-d'))->get();

		$other_payable = OtherPayable::get();

		foreach($accts as $aa)
		{
			$new_acct01[] = json_decode($aa->acct_data);
		}

		// echo '<pre>';
		// print_r($new_acct01);
		// die();

		$res1 = array(
			'zone'=>$zon1->toArray(),
			'collector'=>$my_user[0], 
			'accounts'=>$new_acct01,
			'banks' => $banks->toArray(),
			'reading_period' => $reading_period->toArray(),
			'other_payables' => $other_payable->toArray()
		);

		return response()->json($res1);											

	}//End



}