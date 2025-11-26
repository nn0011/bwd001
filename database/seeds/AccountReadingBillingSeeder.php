<?php

use Illuminate\Database\Seeder;
use App\AccountMetas;
use App\Accounts;
use App\Zones;
use App\HwdRequests;
use App\Reading;
use App\HwdOfficials;
use App\BillingMdl;
use App\BillingMeta;
use App\BillingRateVersion;
use App\HwdJob;
use App\User;
use App\Role;


class AccountReadingBillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
			$status_key_active_raw  = 
						AccountMetas::where('meta_type', 'account_status')
							->where('meta_code', '1')
							->first();		

			$acct_type_resi_raw  = 
						AccountMetas::where('meta_type', 'account_type')
							->where('old_id', '01')
							->first();									

			$acct_status_id = $status_key_active_raw->id;											
			$acct_type_id = $acct_type_resi_raw->id;										
			$zone_id = 1;
				
			/**/
			//$total_1 = 3000;
			$total_1 = 100;
			
			for($x=0; $x<=$total_1;$x++)
			{
					$zone_id = rand(1, 8);
					
					$namex = $this->randName001();
					$acct = new Accounts;
					$acct->fname = $namex[1];
					$acct->lname = $namex[0];	
					$acct->mi = 'P';	
					$acct->address1 = 'Address 000'.$x;	
					$acct->tel1 = '123456';	
					$acct->residence_date = '1990-01-01';
					$acct->zone_id = $zone_id;	
					$acct->acct_type_key = $acct_type_id;	
					$acct->acct_status_key = $acct_status_id;	
					$acct->status = 'active';
					$acct->birth_date = '1990-01-01';
					$acct->meter_number1 = '111'.$x;
					$acct->conn_stat = 'active';
					$acct->save();
					
					//GENERATE ACOUNT NUMBER
					//$acct->acct_no = date('Ymd').'-111-'.$acct->id;
					$acct->acct_no = '111-'.$acct->id;
					$acct->save();
					
					//~ $new_req = new HwdRequests;
					//~ $new_req->reff_id = $acct->id;
					//~ $new_req->req_type = 'new_account_approval';
					//~ $new_req->remarks = ' New account approval reqeust for  account # : '.$acct->acct_no;
					//~ $new_req->status = 'approved';
					//~ $new_req->date_stat = date('Y-m-d H:i:s');
					//~ $new_req->save();
					
					/*
					$time1 = strtotime(date('Y-m-d').'  -2 Months');
					$read1 = new Reading;
					$read1->zone_id = $acct->zone_id;
					$read1->account_id = $acct->id;
					$read1->account_number = $acct->acct_no;
					$read1->meter_number = $acct->meter_number1;
					$read1->period	 = date('Y-m-28', $time1);
					$read1->curr_reading = '00'.rand(10, 30);
					$read1->status = 'active';
					$read1->date_read = date('Y-m-d H:i:s', $time1);
					$read1->init_reading	= '0001';
					$read1->save();
					*/
					
					$months = -2;
					
					$time1 = strtotime(date('Y-m-d').'  '.$months.' Months');
					$read1 = new Reading;
					$read1->zone_id = $acct->zone_id;
					$read1->account_id = $acct->id;
					$read1->account_number = $acct->acct_no;
					$read1->meter_number = $acct->meter_number1;
					$read1->period	 = date('Y-m-28', $time1);
					$read1->curr_reading = '00'.rand(5, 20);
					$read1->status = 'active';
					$read1->date_read = date('Y-m-d H:i:s', $time1);
					$read1->init_reading	= '0001';
					$read1->prev_reading	= '0001';
					$read1->current_consump = ((int)$read1->curr_reading) - ((int)$read1->prev_reading);
					$read1->save();
					
					$months = $months + 1;
					
					$curr_read =  $read1->curr_reading;
					$read_start = 21;
					$read_end   = $read_start + 9;  
					//for($months;$months>=0;$months++)
					while(true)
					{
						if($months > 0)
						{
							break;
						}
						
						//$time1 = strtotime(date('Y-m-d'));
						$time1 = strtotime(date('Y-m-d').'  '.$months.' Months');
						
						$read2 = new Reading;
						$read2->zone_id = $acct->zone_id;
						$read2->account_id = $acct->id;
						$read2->account_number = $acct->acct_no;
						$read2->meter_number = $acct->meter_number1;
						$read2->period	 = date('Y-m-28', $time1);
						$read2->curr_reading = '00'.rand($read_start, $read_end);
						$read2->status = 'active';
						$read2->date_read = date('Y-m-d H:i:s', $time1);
						$read2->prev_reading = $curr_read;

						$read2->current_consump = ((int)$read2->curr_reading) - ((int)$read2->prev_reading);
						
						
						$read2->save();
						
						$curr_read = $read2->curr_reading;
						$months = $months + 1;
						
						$read_start  = $read_end + 5;
						$read_end   = $read_start  + 10;
					}
					
					
			}
			
    }
    
    private function  randName001(){
			$names = array(
					'Adam','Adrian','Alan','Alexander','Andrew','Anthony','Austin','Benjamin','Blake','Boris','Brandon','Brian','Cameron','Carl','Charles',
					'Christian','Christopher','Colin','Connor','Dan','David','Dominic','Dylan','Edward','Eric','Evan','Frank','Gavin','Gordon','Harry','Ian',
					'Isaac','Jack','Jacob','Jake','James','Jason','Joe','John','Jonathan','Joseph','Joshua','Julian','Justin','Keith','Kevin','Leonard','Liam','Lucas',
					'Luke','Matt','Max','Michael','Nathan','Neil','Nicholas','Oliver','Owen','Paul','Peter','Phil','Piers','Richard','Robert','Ryan','Sam','Sean',
					'Sebastian','Simon','Stephen','Steven','Stewart','Thomas','Tim','Trevor','Victor','Warren','William',
			);

			//PHP array containing surnames.
			$surnames = array(
					'Abraham','Allan','Alsop','Anderson','Arnold','Avery','Bailey','Baker','Ball','Bell','Berry','Black','Blake','Bond',
					'Bower','Brown','Buckland','Burgess','Butler','Cameron','Campbell','Carr','Chapman','Churchill','Clark','Clarkson',
					'Coleman','Cornish','Davidson','Davies','Dickens','Dowd','Duncan','Dyer','Edmunds','Ellison','Ferguson','Fisher',
					'Forsyth','Fraser','Gibson','Gill','Glover','Graham','Grant','Gray','Greene','Hamilton','Hardacre','Harris','Hart',
					'Hemmings','Henderson','Hill','Hodges','Howard','Hudson','Hughes','Hunter','Ince','Jackson','James','Johnston',
					'Jones','Kelly','Kerr','King','Knox','Lambert','Langdon','Lawrence','Lee','Lewis','Lyman','MacDonald','Mackay',
					'Mackenzie','MacLeod','Manning','Marshall','Martin','Mathis','May','McDonald','McLean','McGrath','Metcalfe',
					'Miller','Mills','Mitchell','Morgan','Morrison','Murray','Nash','Newman','Nolan','North','Ogden','Oliver','Paige','Parr',
					'Parsons','Paterson','Payne','Peake','Peters','Piper','Poole','Powell','Pullman','Quinn','Rampling','Randall','Rees','Reid',
					'Roberts','Robertson','Ross','Russell','Rutherford','Sanderson','Scott','Sharp','Short','Simpson','Skinner','Smith','Springer',
					'Stewart','Sutherland','Taylor','Terry','Thomson','Tucker','Turner','Underwood','Vance','Vaughan','Walker','Wallace',
					'Walsh','Watson','Welch','White','Wilkins','Wilson','Wright','Young'					
			);

			//Generate a random forename.
			$random_name = $names[mt_rand(0, sizeof($names) - 1)];

			//Generate a random surname.
			$random_surname = $surnames[mt_rand(0, sizeof($surnames) - 1)];

			//Combine them together and print out the result.
			//return $random_name . ' ' . $random_surname;
			return array($random_surname, $random_name);
	}
    
}
