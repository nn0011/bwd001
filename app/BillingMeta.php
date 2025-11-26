<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingMeta extends Model
{
    static
    function meter_size()
    {
        $dat1 = BillingMeta::where('meta_type', 'meter_size')->get();
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
