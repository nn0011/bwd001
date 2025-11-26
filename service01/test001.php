<?php


//$url = 'http://127.0.0.1:8000/service/generate_billing1/'.date('Y-m');
//~ $url = 'http://192.168.2.254:8585/calibrate_data';
//~ $url = 'http://127.0.0.1:8000/ledger_penalty_fix';
//~ $url = 'http://127.0.0.1:8000/update_new_customer_101';
//~ $url = 'http://127.0.0.1:8000/load_reading_for_may_june';
//~ $url = 'http://127.0.0.1:8000/ledger_data_process_as_of_june';

//~ $url = 'http://192.168.2.254:8585/update_new_customer_101';

//~ $url = 'http://127.0.0.1:8000/ledger_data_process_as_of_june';
//~ $url = 'http://192.168.2.8:8000/ledger_data_process_as_of_june';
//~ $url = 'http://192.168.2.254:8585/ledger_data_process_as_of_june';


//~ $url = 'http://127.0.0.1:8000/update_new_customer_101';
//~ $url = 'http://127.0.0.1:8000/update_senior_001';
//~ $url = 'http://127.0.0.1:8000/load_prev_reading';
//~ $url = 'http://127.0.0.1:8000/update_beginning_balance101';

//~ /update_beginning_balance101

// $url = 'http://192.168.1.254:8585/execute_refresh_ledger_by_zone';
$url = 'http://127.0.0.1:8000/daily_collect_service?dd=2023-10-23&sum=1&store_to_collection_ledger=1';


while(true)
{

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url
		));
		$resp = curl_exec($curl);
		curl_close($curl);
		echo $resp."\n";
		sleep(1);

}
