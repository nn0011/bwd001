<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReadingPeriod extends Model
{
	
	function  read1()
	{
		return $this->hasOne('App\Reading', 'period', 'period')
			->selectRaw('period, status, COUNT(id) as ttl_active')
			->where('status', 'active')
			->groupBy('period');		
	}

	function  read2()
	{
	}

	static
	function reading_info($period)
	{
		$reading_info = ReadingPeriod::where('period', $period)->first();
		$fine_dates   = json_decode($reading_info['fine_dates'], true);
		$due_dates2   = json_decode($reading_info['due_dates2'], true);
		$read_dates   = json_decode($reading_info['read_dates'], true);
		
		return compact('fine_dates', 'due_dates2', 'read_dates');
		// ee($due_dates2);
		// ee($reading_info->toArray());
	}//
	
}
