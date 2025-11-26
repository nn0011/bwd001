<?php

use Illuminate\Database\Seeder;
use App\AccountMetas;

class AccountMetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'New Concessionaire';
			$newMeta->meta_code  = '0';
			$newMeta->meta_desc = 'New Concessionaire';
			$newMeta->old_id = '0';
			$newMeta->meta_type	 = 'account_status';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'Active';
			$newMeta->meta_code  = '1';
			$newMeta->meta_desc = 'Active';
			$newMeta->old_id = '1';
			$newMeta->meta_type	 = 'account_status';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'For Disconnection';
			$newMeta->meta_code  = '2';
			$newMeta->meta_desc = 'For Disconnection';
			$newMeta->old_id = '2';
			$newMeta->meta_type	 = 'account_status';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'Disconnected';
			$newMeta->meta_code  = '3';
			$newMeta->meta_desc = 'Disconnected';
			$newMeta->old_id = '3';
			$newMeta->meta_type	 = 'account_status';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'For Reconnection';
			$newMeta->meta_code  = '4';
			$newMeta->meta_desc = 'For Reconnection';
			$newMeta->old_id = '4';
			$newMeta->meta_type	 = 'account_status';
			$newMeta->status = 'active';
			$newMeta->save();

               $newMeta = new AccountMetas;
			$newMeta->meta_name = 'Pending Approval';
			$newMeta->meta_code  = '-1';
			$newMeta->meta_desc = 'Pending Approval';
			$newMeta->old_id = '-1';
			$newMeta->meta_type	 = 'account_status';
			$newMeta->status = 'active';
			$newMeta->save();


			/************************************/
			/************************************/
			/************************************/

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'COM 1';
			$newMeta->meta_code  = '05';
			$newMeta->meta_desc = 'COM 1';
			$newMeta->old_id = '05';
			$newMeta->meta_type	 = 'account_type';
			$newMeta->status = 'active';
			$newMeta->save();


			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'COM B';
			$newMeta->meta_code  = '04';
			$newMeta->meta_desc = 'COM B';
			$newMeta->old_id = '04';
			$newMeta->meta_type	 = 'account_type';
			$newMeta->status = 'active';
			$newMeta->save();


			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'COMM A';
			$newMeta->meta_code  = '03';
			$newMeta->meta_desc = 'COMM A';
			$newMeta->old_id = '03';
			$newMeta->meta_type	 = 'account_type';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'GOVT./COMMERCIAL 1';
			$newMeta->meta_code  = '07';
			$newMeta->meta_desc = 'GOVT./COMMERCIAL 1';
			$newMeta->old_id = '07';
			$newMeta->meta_type	 = 'account_type';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'GOVT./COMMERCIAL A';
			$newMeta->meta_code  = '06';
			$newMeta->meta_desc = 'GOVT./COMMERCIAL A';
			$newMeta->old_id = '06';
			$newMeta->meta_type	 = 'account_type';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'GOVT';
			$newMeta->meta_code  = '02';
			$newMeta->meta_desc = 'GOVT';
			$newMeta->old_id = '02';
			$newMeta->meta_type	 = 'account_type';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'GOVT. B';
			$newMeta->meta_code  = '08';
			$newMeta->meta_desc = 'GOVT. B';
			$newMeta->old_id = '08';
			$newMeta->meta_type	 = 'account_type';
			$newMeta->status = 'active';
			$newMeta->save();

			$newMeta = new AccountMetas;
			$newMeta->meta_name = 'RES';
			$newMeta->meta_code  = '01';
			$newMeta->meta_desc = 'RES';
			$newMeta->old_id = '01';
			$newMeta->meta_type	 = 'account_type';
			$newMeta->status = 'active';
			$newMeta->save();


    }
}
