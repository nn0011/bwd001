<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CollUpload;
use App\TempCollection;
use App\RemoteGenarate;

class RemoteCollCtrl extends Controller
{

    function clear_remote_temp_data()
    {
        TempCollection::truncate();
        CollUpload::truncate();
        return [
            'status' => 1,
            'code' => 1,
            'msg' => 'Temp Data Cleared.'
        ];
    }//


    function clear_temp_remote_collection_data()
    {

        RemoteGenarate::truncate();

        return [
            'status' => 1,
            'code' => 1,
            'msg' => 'Temp Data Cleared.'
        ];
    }//



}//
