<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\GpsAcct;
use App\Accounts;
use App\Exp4;
use App\Reading;
use App\LedgerData;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //~ $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    
    
    function gps_data_all()
    {
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST');
		
		$fname 		= trim(@$_GET['fname']);
		$lname 		= trim(@$_GET['lname']);
		$acct_no 	= trim(@$_GET['acct_no']);
		$zone1 		= trim(@$_GET['zone']);
		
		$accts_raw = Accounts::whereHas('last_gps_data');		
		
		$accts_raw->where(function($q1)use($fname, $lname, $acct_no){
			
				if(!empty($fname)){
					$q1->where('fname', 'like', $fname.'%');
				}
				if(!empty($lname)){
					$q1->where('lname', 'like', $lname.'%');
				}
				
				if(!empty($acct_no)){
					$q1->where('acct_no', 'like', $acct_no.'%');
				}
				
			});

		if(!empty($zone1)){
			$accts_raw->where('zone_id', 'like', $zone1.'%');
		}
		
		
		$accts	   = $accts_raw->with('last_gps_data')->get();
		
		$gps_json = [];
		$gps_json['type'] = 'FeatureCollection';
		$gps_json['features'] = [];

		foreach($accts as $a1)
		{
			$full_name = strtoupper($a1->fname.' '.$a1->lname);
			$full_name = preg_replace('/[^A-Za-z0-9. -]/', '', $full_name);
			

			
			$gps_json['features'][] = array(
					'type' => 'Feature',
					'properties' => [
										'accountType'=>'meter', 
										//~ 'accountNumber' => $a1->meter_number1, 
										'accountNumber' => $a1->acct_no, 
										'accountName' => $full_name
									],
					'geometry' => [
									'type'=>'Point', 
									'coordinates' => [$a1->last_gps_data->lat1, $a1->last_gps_data->lng1]
								  ],
			);
		}
		
		return $gps_json;
		//~ echo '<pre>';
		//~ print_r($accts->toArray());
		//~ print_r(json_encode($gps_json));
	}
    
    
    function zzz_temp001()
    {

		$zone_id    = 13;
		$zone_old   = 131;

		 $rs111 = Exp4::where('C', $zone_old)
					->whereNull('AA')
						->limit(100)
							->get();
		
		if($rs111->count() <= 0){
			echo 'Done';
			return;
		} 
		 
		
		$meter_size = 41; 
		
		foreach($rs111 as $rs1)
		{
			$new_acct_01 					= new Accounts;
			$new_acct_01->acct_no 			= trim($rs1->A);
			$new_acct_01->fname 			= trim($rs1->I);
			$new_acct_01->lname 			= trim($rs1->H);
			$new_acct_01->mi 				= trim($rs1->J);
			$new_acct_01->address1 			= trim($rs1->K);
			$new_acct_01->zone_id 			= $zone_id;
			$new_acct_01->acct_type_key 	= 14;
			$new_acct_01->acct_status_key  	= 2;
			$new_acct_01->status  			= 'active';
			$new_acct_01->meter_number1 	= $rs1->B;
			$new_acct_01->route_id 			= (int) $rs1->F;
			$new_acct_01->old_route 		= (int) $rs1->F;
			$new_acct_01->meter_size_id 	= 41;
			$new_acct_01->tel1 			    = $rs1->M;
			$new_acct_01->save();
			
			$rs1->AA = 1;
			$rs1->save();
		}//
		
		
		echo 'Continue';
		
		//~ echo '<pre>';
		//~ print_r($rs1);
	}
    
    
	function zzz_temp002()
	{
		$readin1 = Exp4::whereNull('Z')
						->whereHas('accts')
							->with('accts:id,acct_no,zone_id')
							//~ ->with('accts:*')
							->limit(100)
							->selectRaw('id, B, C, D, E, F, G, H')
								->get();

		if($readin1->count() <= 0){
			echo 'Done';
			return;
		} 
		
		foreach($readin1 as $rr)
		{
			
			$prev_date = explode('/',$rr->D);
			$prev_date = date('Y-m-d', strtotime($prev_date[2].'-'.$prev_date[0].'-'.$prev_date[1]));

			$curr_date = explode('/',$rr->E);
			$curr_date = date('Y-m-d', strtotime($curr_date[2].'-'.$curr_date[0].'-'.$curr_date[1]));
			
			$prev_reading = $rr->F;
			$curr_reading = $rr->G;
			$cons	      = $rr->H;
			$mtr		  = $rr->C;
			$acct_no	  = $rr->B;
			$acct_id	  = $rr->accts->id;
			$zone_id	  = $rr->accts->zone_id;
			
			$read1 					= new Reading;
			$read1->zone_id 		= $zone_id;
			$read1->account_id		= $acct_id;
			$read1->account_number	= $acct_no;
			$read1->meter_number	= $curr_reading;
			$read1->period			= '2020-10-01';
			$read1->curr_reading	= $curr_reading;
			$read1->prev_reading	= $prev_reading;
			$read1->status			= 'active';
			$read1->date_read		= $curr_date;
			$read1->current_consump	= $cons;
			$read1->prev_read_date	= $prev_date;
			$read1->curr_read_date	= $curr_date;
			$read1->save();
			
			$rr->Z = 1;
			$rr->save();
			
		}
		
		echo 'Continue';

		
	}//

    function zzz_temp003()
    {
		$sql1 = "
		
			SELECT * FROM ( 
					SELECT AA.id, AA.acct_no, 
					(
						SELECT ROUND(SUM(E),2) FROM zzz_temp003 WHERE A=AA.acct_no 
					) oct_arrear 
				FROM accounts AA 
				) TAB1
			
			
			WHERE NOT EXISTS (
				SELECT id FROM ledger_datas WHERE acct_id=TAB1.id AND led_type='beginning' limit 1
			)
			
			LIMIT 100
				
		
		";
		
		$bal1 = DB::select($sql1);
		
		//~ echo '<pre>';
		//~ print_r($bal1);
		//~ die();
		
		if(empty($bal1) ){
			echo 'DONE';
			return;
		}
		
		foreach($bal1 as $bb)
		{
			
			$arrear = 0;
			
			if($bb->oct_arrear > 0){
				$arrear = $bb->oct_arrear;
			}
			
			$ld1 				= new LedgerData;
			$ld1->acct_id  		= @$bb->id;
			$ld1->arrear  		= $arrear;
			$ld1->ttl_bal  		= $arrear;
			$ld1->date01  		= '2020-10-31';
			$ld1->period  		= '2020-10-01';
			$ld1->status  		= 'active';
			$ld1->led_type  	= 'beginning';
			$ld1->acct_num  	= $bb->acct_no;
			$ld1->zort1  		= 0;			
			$ld1->save();
			
            DB::table('zzz_temp003')
                ->where('A', $bb->acct_no)
                ->update(['X' => 1]);			
			
		}
		
		echo 'CONTINUE';
		
		
		
	}//////////
	
    function zzz_temp003_XX()
    {
		$bal1 = DB::select("

SELECT TAB1.*, accounts.id acct_id FROM (SELECT ZT1.id, ZT1.A, ZT1.E, ZT1.K, (SELECT ROUND(SUM(E),2) FROM zzz_temp003 WHERE A=ZT1.A AND id < ZT1.id ) 
oct_arrear 
FROM `zzz_temp003` ZT1
	WHERE ZT1.C=2  AND ZT1.K like '2020-10%'  AND ZT1.X is null ) TAB1
	LEFT JOIN accounts on accounts.acct_no = TAB1.A
	
		LIMIT  100 

				");
		
		
		if(empty($bal1) ){
			echo 'DONE';
			return;
		}

		foreach($bal1 as $bb)
		{
			
			$arrear = 0;
			
			if($bb->oct_arrear > 0){
				$arrear = $bb->oct_arrear;
			}
			
			$ld1 				= new LedgerData;
			$ld1->acct_id  		= $bb->acct_id;
			$ld1->arrear  		= $arrear;
			$ld1->ttl_bal  		= $arrear;
			$ld1->date01  		= '2020-09-01';
			$ld1->period  		= '2020-09-01';
			$ld1->status  		= 'active';
			$ld1->led_type  	= 'beginning';
			$ld1->acct_num  	= $bb->A;
			$ld1->zort1  		= 0;			
			$ld1->save();
			
			//~ DB::query("UPDATE zzz_temp003 SET X=1 WHERE id=".$bb->id);
            DB::table('zzz_temp003')
                ->where('id', $bb->id)
                ->update(['X' => 1]);			
			
		}
		
		echo 'CONTINUE';

		
		//~ echo '<pre>';
		//~ print_r($bal1);
		//~ LedgerData
		
		/*
		$balance = Exp4::whereNull('K')
						->limit(500)
							->get();
		
		if($balance->count() <= 0){
			echo 'Done';
			return;
		} 
				
		
		foreach($balance as $bb)
		{
			$prev_date = explode('/',$bb->B);
			$prev_date = date('Y-m-d', strtotime($prev_date[2].'-'.$prev_date[0].'-'.$prev_date[1]));
			$bb->K = $prev_date;
			$bb->save();
		}
		echo 'Continue';
		*/ 
		
		
	}//
    
    
    function update_account_type_1001()
    {
		$zone = '111';
		
		$gov1  = Accounts::where('acct_no', 'like', $zone.'02'.'%')->get();
		$coma  = Accounts::where('acct_no', 'like', $zone.'04'.'%')->get();
		$comb  = Accounts::where('acct_no', 'like', $zone.'05'.'%')->get();
		$comc  = Accounts::where('acct_no', 'like', $zone.'06'.'%')->get();
		$bulk  = Accounts::where('acct_no', 'like', $zone.'07'.'%')->get();
		
		$govt_s = 12;
		$coma_s = 9;
		$comb_s = 8;
		$comc_s = 7;
		$bulk_s = 15;
		
		function ___update_acct_type($gov1, $govt_s){
			foreach($gov1 as $g){
				$g->acct_type_key = $govt_s;
				$g->save();
			}
		}
		___update_acct_type($gov1, $govt_s);
		___update_acct_type($coma, $coma_s);
		___update_acct_type($comb, $comb_s);
		___update_acct_type($comc, $comc_s);
		___update_acct_type($bulk, $bulk_s);
		
		
	}//
    
    
    
    
}
