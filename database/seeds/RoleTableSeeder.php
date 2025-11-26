<?php

use Illuminate\Database\Seeder;
use App\Role;


class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
		$role_employee = new Role();
		$role_employee->name = 'admin';
		$role_employee->description = 'Admin';
		$role_employee->save();		

		$role_employee = new Role();
		$role_employee->name = 'super_admin';
		$role_employee->description = 'Super Admin';
		$role_employee->save();		

		$role_employee = new Role();
		$role_employee->name = 'billing_admin';
		$role_employee->description = 'Billing Admin';
		$role_employee->save();		
		
		$role_employee = new Role();
		$role_employee->name = 'collection_officer';
		$role_employee->description = 'Collection Officer';
		$role_employee->save();		
		
		$role_employee = new Role();
		$role_employee->name = 'reading_officer';
		$role_employee->description = 'Reading Officer';
		$role_employee->save();		

    }
    
}
