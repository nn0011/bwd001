<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WaterMeter extends Model
{
    protected $fillable = ['meter_num', 'brand_name', 'meter_size', 'status'];
    
    function last_history()
    {
        return $this->hasOne('App\MeterHistory', 'meter_id', 'id')
                    ->orderBy('id', 'desc');
    }

    function histories()
    {
        return $this->hasMany('App\MeterHistory', 'meter_id', 'id')
                    // ->where('status', 'active')
                    ->orderBy('id', 'desc');
    }

}
