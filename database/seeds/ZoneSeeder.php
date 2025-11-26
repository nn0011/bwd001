<?php

use Illuminate\Database\Seeder;
use App\Zones;


class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

			$init_name  = 'ZONE 01';
			$init_id  = '010';
			$new_zone = new  Zones;
			$new_zone->zone_name = $init_name;
			$new_zone->zone_code = $init_id;
			$new_zone->zone_desc = $init_name;
			$new_zone->old_zone_id = $init_id;
			$new_zone->status = 'active';
			$new_zone->save();

               //
			// $init_name  = 'ZONE 01 BOOK 1';
			// $init_id  = '011';
			// $new_zone = new  Zones;
			// $new_zone->zone_name = $init_name;
			// $new_zone->zone_code = $init_id;
			// $new_zone->zone_desc = $init_name;
			// $new_zone->old_zone_id = $init_id;
			// $new_zone->status = 'active';
			// $new_zone->save();


			$init_name  = 'ZONE 02';
			$init_id  = '020';
			$new_zone = new  Zones;
			$new_zone->zone_name = $init_name;
			$new_zone->zone_code = $init_id;
			$new_zone->zone_desc = $init_name;
			$new_zone->old_zone_id = $init_id;
			$new_zone->status = 'active';
			$new_zone->save();

               //
			// $init_name  = 'ZONE 02 BOOK 1';
			// $init_id  = '021';
			// $new_zone = new  Zones;
			// $new_zone->zone_name = $init_name;
			// $new_zone->zone_code = $init_id;
			// $new_zone->zone_desc = $init_name;
			// $new_zone->old_zone_id = $init_id;
			// $new_zone->status = 'active';
			// $new_zone->save();


			$init_name  = 'ZONE 03';
			$init_id  = '030';
			$new_zone = new  Zones;
			$new_zone->zone_name = $init_name;
			$new_zone->zone_code = $init_id;
			$new_zone->zone_desc = $init_name;
			$new_zone->old_zone_id = $init_id;
			$new_zone->status = 'active';
			$new_zone->save();

               //
			// $init_name  = 'ZONE 03 BOOK 1';
			// $init_id  = '031';
			// $new_zone = new  Zones;
			// $new_zone->zone_name = $init_name;
			// $new_zone->zone_code = $init_id;
			// $new_zone->zone_desc = $init_name;
			// $new_zone->old_zone_id = $init_id;
			// $new_zone->status = 'active';
			// $new_zone->save();


			$init_name  = 'ZONE 04';
			$init_id  = '040';
			$new_zone = new  Zones;
			$new_zone->zone_name = $init_name;
			$new_zone->zone_code = $init_id;
			$new_zone->zone_desc = $init_name;
			$new_zone->old_zone_id = $init_id;
			$new_zone->status = 'active';
			$new_zone->save();

               //
			// $init_name  = 'ZONE 04 BOOK 1';
			// $init_id  = '041';
			// $new_zone = new  Zones;
			// $new_zone->zone_name = $init_name;
			// $new_zone->zone_code = $init_id;
			// $new_zone->zone_desc = $init_name;
			// $new_zone->old_zone_id = $init_id;
			// $new_zone->status = 'active';
			// $new_zone->save();


			$init_name  = 'ZONE 05';
			$init_id  = '050';
			$new_zone = new  Zones;
			$new_zone->zone_name = $init_name;
			$new_zone->zone_code = $init_id;
			$new_zone->zone_desc = $init_name;
			$new_zone->old_zone_id = $init_id;
			$new_zone->status = 'active';
			$new_zone->save();

               //
			// $init_name  = 'ZONE 05 BOOK 1';
			// $init_id  = '051';
			// $new_zone = new  Zones;
			// $new_zone->zone_name = $init_name;
			// $new_zone->zone_code = $init_id;
			// $new_zone->zone_desc = $init_name;
			// $new_zone->old_zone_id = $init_id;
			// $new_zone->status = 'active';
			// $new_zone->save();


			$init_name  = 'ZONE 06';
			$init_id  = '060';
			$new_zone = new  Zones;
			$new_zone->zone_name = $init_name;
			$new_zone->zone_code = $init_id;
			$new_zone->zone_desc = $init_name;
			$new_zone->old_zone_id = $init_id;
			$new_zone->status = 'active';
			$new_zone->save();

			// $init_name  = 'ZONE 06 BOOK 1';
			// $init_id  = '061';
			// $new_zone = new  Zones;
			// $new_zone->zone_name = $init_name;
			// $new_zone->zone_code = $init_id;
			// $new_zone->zone_desc = $init_name;
			// $new_zone->old_zone_id = $init_id;
			// $new_zone->status = 'active';
			// $new_zone->save();

			$init_name  = 'ZONE 07';
			$init_id  = '070';
			$new_zone = new  Zones;
			$new_zone->zone_name = $init_name;
			$new_zone->zone_code = $init_id;
			$new_zone->zone_desc = $init_name;
			$new_zone->old_zone_id = $init_id;
			$new_zone->status = 'active';
			$new_zone->save();

			// $init_name  = 'ZONE 07 BOOK 1';
			// $init_id  = '071';
			// $new_zone = new  Zones;
			// $new_zone->zone_name = $init_name;
			// $new_zone->zone_code = $init_id;
			// $new_zone->zone_desc = $init_name;
			// $new_zone->old_zone_id = $init_id;
			// $new_zone->status = 'active';
			// $new_zone->save();

			$init_name  = 'TEMP ZONE';
			$init_id  = '110';
			$new_zone = new  Zones;
			$new_zone->zone_name = $init_name;
			$new_zone->zone_code = $init_id;
			$new_zone->zone_desc = $init_name;
			$new_zone->old_zone_id = $init_id;
			$new_zone->status = 'active';
			$new_zone->save();

			// $init_name  = 'TEMP ZONE BOOK 1';
			// $init_id  = '111';
			// $new_zone = new  Zones;
			// $new_zone->zone_name = $init_name;
			// $new_zone->zone_code = $init_id;
			// $new_zone->zone_desc = $init_name;
			// $new_zone->old_zone_id = $init_id;
			// $new_zone->status = 'active';
			// $new_zone->save();



    }
}
