<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zones extends Model
{
		function billzone()
		{
				return $this->hasOne('App\ServiceBillZone', 'zone_id', 'id')
					->orderBy('id', 'desc');
		}

		static
		function list()
		{
			$dat1 = Zones::get();
			$dat1 = $dat1->toArray();
			
			$ret1 = [];
			foreach($dat1 as $k => $v) 
			{
				$ret1[$v['id']] = $v;
			}

			return $ret1;
			ee($dat1);
		}//

}
