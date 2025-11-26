<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountMetas extends Model
{
    static
    function acct_type()
    {
        $dat1 = AccountMetas::where('meta_type', 'account_type')->get();
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
