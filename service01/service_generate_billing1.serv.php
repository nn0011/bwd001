<?php


//$url = 'http://127.0.0.1:8000/service/generate_billing1/'.date('Y-m');
//~ $url = 'http://192.168.0.17:8000/service/generate_billing1';
//~ $url = 'http://192.168.0.17:8585/service/generate_billing1';
//~ $url = 'http://192.168.2.254:8585/service/generate_billing1';
//~ $url = 'http://192.168.2.8:8000/service/generate_billing1';
//~ $url = 'http://127.0.0.1:8000/service/generate_billing1';
$url = 'http://192.168.2.254:8585/service/generate_billing1';




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
		sleep(3);
}//
