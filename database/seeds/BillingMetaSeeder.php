<?php

use Illuminate\Database\Seeder;
use App\BillingMeta;
use App\AccountMetas;
use App\BillingRateVersion;

class BillingMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		$name1='Residential'; 
		$dd1=array('01', 'RES'); 
		$dd2= array(200, 200, 467.50, 805, 1215); 
		$dd3=array(0, 26.75, 33.75, 41, 46.40);
		$this->mmm00($name1,$dd1,$dd2,$dd3, 14);		
		
		$name1='Commercial B'; 
		$dd1=array('04', 'COM B'); 
		$dd2= array(300, 300, 701, 1207, 1822); 
		$dd3=array(0, 40.10, 50.6, 61.50, 69.60);
		$this->mmm00($name1,$dd1,$dd2,$dd3);		
		
		$name1='Commercial A'; 
		$dd1=array('03', 'COMM A');
		$dd2= array(350, 350, 818, 1408.50, 2126); 
		$dd3=array(0, 46.80, 59.05, 71.75, 81.20);		
		$this->mmm00($name1,$dd1,$dd2,$dd3);	
		

		$name1='Commercial 1'; 
		$dd1=array('05', 'COMM 1');
		$dd2= array(400, 400, 935, 1610, 2430); 
		$dd3=array(0, 53.50, 67.50, 82, 92.80);		
		$this->mmm00($name1,$dd1,$dd2,$dd3);	

		$name1='Gov 1'; 
		$dd1=array('07', 'GOVT./COMMERCIAL 1'); 
		$dd2= array(200, 200, 467.50, 805, 1215); 
		$dd3=array(0, 26.75, 33.75, 41, 46.40);
		$this->mmm00($name1,$dd1,$dd2,$dd3);	
		
		$name1='Gov A'; 
		$dd1=array('06', 'GOVT./COMMERCIAL A'); 			
		$this->mmm00($name1,$dd1,$dd2,$dd3);	
		
		$name1='GOVT'; 
		$dd1=array('02', 'GOVT'); 			
		$this->mmm00($name1,$dd1,$dd2,$dd3);	
		
		$name1='GOVT. B'; 
		$dd1=array('08', 'GOVT. B'); 			
		$this->mmm00($name1,$dd1,$dd2,$dd3);			
		

			$rates1 = BillingMeta::where('meta_type', 'billing_rates')
						->select(DB::raw('id,meta_data,updated_at'))
						->get()->toArray();

			$ratesV1 = new BillingRateVersion;
			$ratesV1->rates_description	 = "Billing Rates as of ".date('Y-m-d  H:i:s');
			$ratesV1->meta_data = json_encode($rates1);
			$ratesV1->save();


    }//
    
    
    private function mmm00(		
							$name1='Residential', 
							$dd1=array('01', 'RES'), 
							$dd2= array(200, 200, 467.50, 805, 1215), 
							$dd3=array(0, 26.75, 33.75, 41, 46.40)
				)
    {
			$data1 = array(
						'rate_name' => $name1.' 1 - 10 cu',
						'min_charge' => $dd2[0],
						'minr' => 1,
						'maxr' => 10,
						'prate' => $dd3[0],
						'old_id' => $dd1[0],
						'met_name' => $dd1[1],
					);
			$this->exe_data1($data1);


			$data1 = array(
						'rate_name' => $name1.' 11 - 20 cu',
						'min_charge' => $dd2[1],
						'minr' => 11,
						'maxr' => 20,
						'prate' => $dd3[1],
						'old_id' => $dd1[0],
						'met_name' => $dd1[1],

					);
			$this->exe_data1($data1);


			$data1 = array(
						'rate_name' => $name1.' 21 - 30 cu',
						'min_charge' => $dd2[2],
						'minr' => 21,
						'maxr' => 30,
						'prate' => $dd3[2],
						'old_id' => $dd1[0],
						'met_name' => $dd1[1],

					);
			$this->exe_data1($data1);


			$data1 = array(
						'rate_name' => $name1.' 31 - 40 cu',
						'min_charge' => $dd2[3],
						'minr' => 31,
						'maxr' => 40,
						'prate' => $dd3[3],
						'old_id' => $dd1[0],
						'met_name' => $dd1[1],

					);
			$this->exe_data1($data1);


			$data1 = array(
						'rate_name' => $name1.' 41 - 9999 cu',
						'min_charge' => 1215,
						'minr' => 41,
						'maxr' => 9999,
						'prate' => $dd3[4],
						'old_id' => $dd1[0],
						'met_name' => $dd1[1],

					);
			$this->exe_data1($data1);
		
	}

    private function  exe_data1($data1){
			extract($data1);

			$amm = AccountMetas::where('old_id', $old_id)
										//->where('meta_name', $met_name)
										->where('meta_type', 'account_type')
										->first();
				

			$rate_arr = array(
								'rname'=> $rate_name,
								'acct_type'=>$amm->id,
								'min_charge'=>$min_charge,
								'min_cu'=>$minr,
								'max_cu'=>$maxr,
								'price_rate'=> $prate,
								'rate_desc'=>$rate_name,
							);

			$new_rate  =  new  BillingMeta;
			$new_rate->meta_type =  'billing_rates';
			$new_rate->status = 'active';
			$new_rate->meta_name =  $rate_name;
			$new_rate->meta_desc =  $rate_name;
			$new_rate->meta_data = json_encode($rate_arr);
			$new_rate->save();
	}//


}
