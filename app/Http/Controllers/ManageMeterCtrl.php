<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WaterMeter;
use App\AccountMetas;
use App\BillingMeta;
use App\Accounts;
use App\MeterHistory;

class ManageMeterCtrl extends Controller
{

    function add_to_history(Request $request)
    {

        $personel_select = trim($request->input('personel_select'));
        $type_select   = trim($request->input('type_select'));
        $date_trx   = date('Y-m-d', strtotime(trim($request->input('date_trx'))));
        $acct_id   = trim($request->input('acct_id'));
        $meter_id   = trim($request->input('meter_id'));

        $new_history = new MeterHistory;
        $new_history->acct_id = $acct_id;
        $new_history->meter_id = $meter_id;
        $new_history->served_date = $date_trx;
        $new_history->served_name = $personel_select;
        $new_history->typ = $type_select;
        $new_history->status = 'active';
        $new_history->save();

        $water_meter = WaterMeter::find($meter_id);

        $acct_cur = Accounts::find($acct_id);
        $acct_cur->meter_number1 =  $water_meter->meter_num;
        $acct_cur->save();

        return ['status' => 1, 'msg' => 'Good'];

    }

    function add_personel_name(Request $request) 
    {
        $person_name = strtoupper(trim($request->input('person_name')));

        $person_exist  = AccountMetas::where('meta_type', 'personel')->where('meta_name', $person_name)->first();

        if( $person_exist ){
            return ['status' => 0, 'msg' => 'Person already exist'];
        }

        $new_person = new AccountMetas;
        $new_person->meta_name = $person_name;
        $new_person->meta_type = 'personel';
        $new_person->save();

        $person_list  = AccountMetas::where('meta_type', 'personel')->orderBy('meta_name', 'asc')->get();

        return ['status' => 1, 'msg' => 'Good', 'personel_list' => $person_list->toArray()];
    }

    function ajax_search_account(Request $request)
    {
        $acct_no = trim($request->input('acct_no'));
        $fname   = trim($request->input('fname'));
        $lname   = trim($request->input('lname'));

        $accounts_raw = Accounts::where('status', 'active');
        
        if( empty( $fname ) && empty( $lname ) && empty( $acct_no ) ) {
            return ['status' => 0, 'msg' => 'Search Failed.'];
        }

        if( !empty( $fname ) ) { 
            $accounts_raw->where('fname', 'like', $fname."%");
        }

        if( !empty( $lname ) ) { 
            $accounts_raw->where('lname', 'like', $lname."%");
        }

        if( !empty( $acct_no ) ) { 
            $accounts_raw->where('acct_no', 'like', $acct_no."%");
        }

        $accounts = $accounts_raw->limit(10)->get();

        $html_result =  view('meters.ajax.search_account_results', compact('accounts'))->render();

        return [
            'status' => 1, 
            'msg' => 'Good',
            'html1' => $html_result, 
            'accounts' => $accounts
        ];

    }

    function ajax_search(Request $request)
    {
        $meter_num = $request->input('meter_num');
        $recent_water_meter = WaterMeter::where('meter_num','like', $meter_num.'%' )
                                    ->with('last_history.account')
                                    ->orderBy('created_at', 'desc')
                                        ->get();
        // return view('meters.ajax.meter_list', compact('recent_water_meter'));
        $recent_meters_view = view('meters.ajax.meter_list', compact('recent_water_meter'))->render();
        return ['status' => 1, 'msg' => 'Good', 'html1' => $recent_meters_view, 'meter_list' => $recent_water_meter];
    }

    function ajax_list() 
    {
        $recent_water_meter = WaterMeter::orderBy('created_at', 'desc')
                                    ->with('last_history.account')
                                    ->get();

        // echo '<pre>';
        // print_r($recent_water_meter->toArray());
        // die();

        $recent_meters_view = view('meters.ajax.meter_list', compact('recent_water_meter'))->render();
        return ['status' => 1, 'msg' => 'Good', 'html1' => $recent_meters_view, 'meter_list' => $recent_water_meter];
    }

    // WaterMeter $meter
    function get_history(Request $request, $meter)
    {
        $meter_info = WaterMeter::with(['histories.account'])->find($meter);
        $recent_meters_view = view('meters.ajax.meter_history', compact('meter_info'))->render();

        return [
                'status' => 1,
                'html1' => $recent_meters_view, 
                'meter_info' => $meter_info
            ];

        return $recent_meters_view;
        echo '<pre>';
        print_r($meter_info->toArray());

    }//



    function meter_management()
    {
        $personel  = AccountMetas::where('meta_type', 'personel')
                        ->orderBy('meta_name')
                            ->get();

        $meter_brand  = AccountMetas::where('meta_type', 'meter_brand')
                                ->orderBy('meta_name')
                                    ->get();
    

        $meter_size = BillingMeta::where('meta_type', 'meter_size')
                                ->orderBy('meta_name')
                                ->get();

        return view('meters.manage-meter', compact('personel', 'meter_size', 'meter_brand'));
    }

    function add_water_meter(Request $request) 
    {
        // meter_num, brand_name, meter_size, status
        $meter_num = $request->input('meter_num');
        $brand_name = $request->input('brand_name');
        $meter_size = $request->input('meter_size');
        $brand_select = $request->input('brand_select');
        $meter_id = $request->input('meter_id');
        $is_new = $request->input('is_new');
        
        //Overide Meter Name with select
        if( !empty($brand_select) ) {
            $brand_name = $brand_select;
        } 

        $brand_name = strtoupper(trim($brand_name));

        $brand_exist = AccountMetas::where('meta_type', 'meter_brand')
                            ->where('meta_name', $brand_name)
                            ->first();

        // Save Brand if not exist                    
        if( !$brand_exist ) 
        {
            $new_brand = new AccountMetas;
            $new_brand->meta_name  = $brand_name;
            $new_brand->meta_desc  = $brand_name;
            $new_brand->meta_type  = 'meter_brand';
            $new_brand->status     = 'active';
            $new_brand->save();
        }

        //
        if( $is_new != 'yes' ) 
        {
            // return ['status' => 0, 'msg' => 'THIS IS AN UPDATE ONLY'];

            $water_meter = WaterMeter::find($meter_id);

            $is_exist = WaterMeter::where('meter_num', strtoupper(trim($meter_num)) )
                            ->where('id', '!=', $meter_id)
                            ->first();

            if( $is_exist ) {
                    return ['status' => 0, 'msg' => 'Meter already exist.'];
            }

            $water_meter->meter_num  = strtoupper(trim($meter_num)); 
            $water_meter->brand_name = strtoupper(trim($brand_name)); 
            $water_meter->meter_size = strtoupper(trim($meter_size)); 
            $water_meter->save();

            return ['status' => 1, 'msg' => 'Success'];

        }

        

        // FILTER TO BE ADDED
       $is_exist = WaterMeter::where('meter_num', strtoupper(trim($meter_num)) )->first();
       
       if( $is_exist ) {
            return ['status' => 0, 'msg' => 'Meter already exist.'];
       }

        WaterMeter::create([
            'meter_num' => strtoupper(trim($meter_num)), 
            'brand_name' => strtoupper(trim($brand_name)), 
            'meter_size' => strtoupper(trim($meter_size)),
            'status'  => 'active'
        ]);

        return ['status' => 1, 'msg' => 'Success'];
    }


    function delete_water_meter(Request $request) 
    {
        $meter_id = $request->input('wmi');

        $water_meter = WaterMeter::where('id', $meter_id )
                        ->where('status', 'active')
                            ->first();

        if( !$water_meter ) {
            return ['status' => 0, 'msg' => 'Meter not found'];
        }

        $water_meter->status = 'delete';
        $water_meter->save();

        return ['status' => 1, 'msg' => 'Meter Deleted'];
    }


    function update_water_meter(Request $request) 
    {
        $meter_id = $request->input('wmi');

        $water_meter = WaterMeter::where('id', $meter_id )
                        ->where('status', 'active')
                            ->first();

        if( !$water_meter ) {
            return ['status' => 0, 'msg' => 'Meter not found'];
        }

        // meter_num, brand_name, meter_size, status
        $meter_num  = $request->input('meter_num');
        $brand_name = $request->input('brand_name');
        $meter_size = $request->input('meter_size');

        $is_exist = WaterMeter::where('status', 'active')
                    ->where('id','!=', $meter_id )
                    ->where('meter_num', $meter_num)
                    ->first();        
                            
            if( $is_exist ) {
                return ['status' => 0, 'msg' => 'Meter already exist.'];
            }
    

        // FILTER TO BE ADDED
        $water_meter->meter_num  =  strtoupper(trim($meter_num));
        $water_meter->brand_name =  strtoupper(trim($brand_name));
        $water_meter->meter_size =  strtoupper(trim($meter_size));
        $water_meter->save();

        return ['status' => 1, 'msg' => 'Meter updated'];
    }

    function get_water_meters(Request $request) 
    {
        return WaterMeter::get();
    }

    function save_remarks_by_history_id(Request $request)
    {
        $remaks_text = $request->input('remaks_text');
        $history_id  = $request->input('history_id');

        $history = MeterHistory::find($history_id);
        $history->remaks = strtoupper( trim($remaks_text) ) ;
        $history->save();
        

    }


}
