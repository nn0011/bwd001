<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeterHistory extends Model
{
    function account()
    {
        return $this->hasOne('App\Accounts', 'id', 'acct_id')
                ->selectRaw('id, acct_no, fname, lname, mi, address1');
    }

}
