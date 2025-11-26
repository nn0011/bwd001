<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
			
		$role_admin  = Role::where('name', 'admin')->first();
		$user1 = new User();
		$user1->name = 'Admin1 Admin1';
		$user1->username = 'admin1';
		$user1->password = bcrypt('123456');
		$user1->save();
		$user1->roles()->attach($role_admin);
			
		$role_admin  = Role::where('name', 'super_admin')->first();
		$user1 = new User();
		$user1->name = 'Super1 Super1';
		$user1->username = 'super1';
		$user1->password = bcrypt('123456');
		$user1->save();
		$user1->roles()->attach($role_admin);		

		$role_admin  = Role::where('name', 'billing_admin')->first();
		$user1 = new User();
		$user1->name = 'Billing1 Billing1';
		$user1->username = 'billing1';
		$user1->password = bcrypt('123456');
		$user1->save();
		$user1->roles()->attach($role_admin);		

		$role_admin  = Role::where('name', 'collection_officer')->first();
		$user1 = new User();
		$user1->name = 'Collection1 Collection1';
		$user1->username = 'collector1';
		$user1->password = bcrypt('123456');
		$user1->save();
		$user1->roles()->attach($role_admin);		

		$role_admin  = Role::where('name', 'reading_officer')->first();
		$user1 = new User();
		$user1->name = 'Reading1 Reading1';
		$user1->username = 'reading1';
		$user1->password = bcrypt('123456');
		$user1->save();
		$user1->roles()->attach($role_admin);
		
		
		
    }
    
    
}
