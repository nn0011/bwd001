<?php

use Illuminate\Database\Seeder;
use App\Zones;
use App\Accounts;
use App\Arrear;
use App\BillingDue;
use App\BillingMdl;
use App\Collection;
use App\LedgerData;
use App\Reading;
use App\ReadingPeriod;


class AccountInsertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		// Period
		// Fine {"1":"2025-09-17","2":"2025-09-17","3":"2025-09-17","4":"2025-09-17"}
		// Due  {"1":"2025-09-16","2":"2025-09-16","3":"2025-09-16","4":"2025-09-16"}
		// Read {"1":"2025-09-01","2":"2025-09-01","3":"2025-09-01","4":"2025-09-01"}
		/*
		`id`, 
		`zone_id`, 
		`officer_id`, 
		`account_id`, 
		`account_number`, 
		`meter_number`, 
		`period`, 
		`curr_reading`, 
		`prev_reading`, 
		`status`, 
		`date_read`, 
		`init_reading`, 
		`bill_stat`, 
		`current_consump`, 
		`prev_read_date`, 
		`curr_read_date`, 
		`mrsn_old`
		*/

		$acct1 = Accounts::find(1);
		
		#########
		#########
		#########
		$reading_period = ReadingPeriod::whereIn('period', $period_arr = ['2025-07-01', '2025-08-01', '2025-09-01'])->get();

		if( $reading_period->count() <= 0 ) 
		{
			$insert_me = [];
			foreach( $period_arr as $v ) 
			{
				$sub_v = date('Y-m', strtotime($v));
				$fine_dates = '{"1":"'.$sub_v.'-17","2":"'.$sub_v.'-17","3":"'.$sub_v.'-17","4":"'.$sub_v.'-17"}';
				$due_dates  = '{"1":"'.$sub_v.'-16","2":"'.$sub_v.'-16","3":"'.$sub_v.'-17","4":"'.$sub_v.'-16"}';
				$read_dates = '{"1":"'.$sub_v.'-01","2":"'.$sub_v.'-01","3":"'.$sub_v.'-01","4":"'.$sub_v.'-01"}';

				$insert_me[] = [
					'status' => 'pending',
					'period' => $v,
					'fine_dates' => $fine_dates,
					'due_dates2' => $due_dates,
					'read_dates' => $read_dates,
				];
			}
			
			ReadingPeriod::insert($insert_me);
		}
		#########
		#########
		#########

		




    }


}
