<?php

//~ ini_set('max_execution_time', 3600); //30 minutes

//$url = 'http://127.0.0.1:8000/service/generate_billing1/'.date('Y-m');
//~ $url = 'http://192.168.0.17:8000/service/billing_printing_service/execute';
//~ $url = 'http://192.168.0.17:8585/service/reading_period_start';
//~ $url = 'http://192.168.2.254:8585/service/reading_period_start';
//~ $url = 'http://192.168.2.254:8585/service/reading_period_start';
//~ $url = 'http://127.0.0.1:8000/service/reading_period_start';

$url = 'http://192.168.2.254:8585/service/reading_period_start';

//php service_reading_init.serv.php

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
		sleep(5);

}
