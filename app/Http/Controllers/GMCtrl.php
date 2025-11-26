<?php

namespace App\Http\Controllers;

use App\Accounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


use App\Collection;
use App\HwdLedger;
use App\TempReport;
use App\BillingAdjMdl;
use App\Services\Collections\CollectionService;
use App\Services\GM\GMServices;

use App\Http\Controllers\CashierCtrl;


class GMCtrl extends Controller
{
    function init_daily_collection()
    {

        if( @$_GET['reset'] == 1 )
        {
            TempReport::where('typ1', 'gm_daily_coll_rep')
                        ->delete();
    
            echo 'Please Wait... Processing';
            echo '<br />';
            echo '<script>setTimeout(()=>{
                document.location.href="/admin/gm/init_daily_collection";
            }, 1000);</script>';
            
            die();            
            die();            
        }//



        $days = [];
        for($x=0;$x<=30;$x++){
            $days[] = date('Y-m-d', strtotime(' Today - '.$x.' Days'));
        }

        $res1 = TempReport::where('typ1', 'gm_daily_coll_rep')
                        ->where('date1','>=' , date('Y-m-d', strtotime('today - 30 days')))
                            ->where('status', 'active')
                                ->get();        

        $days_in = $res1->pluck('str1')->toArray();
        $days_diff = array_diff($days, $days_in);

        if(empty($days_diff))
        {
            echo 'DONE';
            die();
        }

        foreach($days_diff as $dd1)
        {

            $coll1 = Collection::whereRaw("
                                id IN 
                                (

                                    SELECT MAX(id) idx FROM `collections` 
                                    where payment_date like '$dd1%'
                                    GROUP BY invoice_num                                

                                )
                            ")
                            ->whereIn('status', ['active', 'collector_receipt', 'or_nw', 'cr_nw'])
                            ->sum('payment');

            TempReport::insert([
                    'typ1' => 'gm_daily_coll_rep',
                    'date1' => $dd1,
                    'str1' => $dd1,
                    'amt_1' => $coll1,
                    'status' => 'active'
                ]);

                break;
        }//

        // echo '<pre>';
        // print_r($days_in);
        // print_r($days);
        // print_r($days_diff);

        echo 'Please Wait... Processing... '.@$dd1;
        echo '<br />';
        echo '<script>setTimeout(()=>{
            document.location.href="/admin/gm/init_daily_collection";
        }, 1000);</script>';        

            
    }//

    function init_monthly_collection()
    {
        if( @$_GET['reset'] == 1 )
        {
            TempReport::where('typ1', 'gm_collector_summary')
                        ->delete();
    
            echo 'Please Wait... Processing';
            echo '<br />';
            echo '<script>setTimeout(()=>{
                document.location.href="/admin/gm/init_monthly_collection";
            }, 1000);</script>';
            
            die();            
            die();            
        }//


        $now_date = date('Y-m');
        $dates = [];

        for($x=0;$x<=5;$x++)
        {
            $dates[] = date("Y-m-01", strtotime( date('Y-m-01').' - '.$x.' Month' ));
        }

        $is_process_exist = false;

        $new_dates = [];
        foreach($dates as $dd)
        {
            $new_dates[] = $dd;

            $res1 = TempReport::where('typ1', 'gm_collector_summary')
                            ->where('str1','like', $dd.'%')
                                ->where('status', 'active')
                                    ->first();
            
            if( $res1 ) {
                continue;
            }

            $is_process_exist = true;

            $collect_info = GMServices::get_collection_per_month($dd);
            
            $new_Temp = new TempReport;
            $new_Temp->str1  = $dd;
            $new_Temp->date1 = $dd;
            $new_Temp->data1 = json_encode($collect_info);
            $new_Temp->status = 'active';
            $new_Temp->typ1 = 'gm_collector_summary';
            $new_Temp->save();

            break;
        }//

        if( $is_process_exist ) {
            echo 'Please Wait... Processing '.date('F Y', strtotime($dd)).'...';
            echo '<br />';
            echo '<script>setTimeout(()=>{
                document.location.reload();
            }, 1000);</script>';
            return;
        }//

        echo 'DONE PROCESSING.';

    }//

    function dashboard()
    {
        $accounts        = GMServices::get_employee_account();
        $collection_data = GMServices::get_gm_collection_summary();
        $accounts_info   = GMServices::get_accounts_info(); 
        $adjustment_data = GMServices::get_adjustment_dashboard_chart();
        $daily_collect   = GMServices::get_daily_collection_data();

        $acct_tick     = [];
        $new_acct_data = [];
        $dis_acct_data = [];
        $rec_acct_data = [];
        
        foreach( $accounts_info as $kk => $vv )
        {
            $acct_tick[]   = date( 'M-Y', strtotime($kk) );
            $new_acct_data[] = @$vv['new'][0]['ttl']; 
            $dis_acct_data[] = @$vv['dis'][0]['ttl']; 
            $rec_acct_data[] = @$vv['rec'][0]['ttl']; 
        }//

        $acct_data_info = [
            'tic' => $acct_tick,
            'new' => $new_acct_data,
            'dis' => $dis_acct_data,
            'rec' => $rec_acct_data,
        ];//

		return view('admin.gm.dashboard', compact('accounts', 'collection_data', 'acct_data_info', 'adjustment_data', 'daily_collect'));

        
    }//


    function INIT_NWB()
    {

        $collection_with_NWB = Collection::where('coll_info', 'like', '%Non-Water Bill Debit%')->whereNull('has_nw');

        $limit1 = 10;

        $all_count = $collection_with_NWB->count();
        $collection_with_NWB = $collection_with_NWB->limit($limit1)->get();


        if( $collection_with_NWB->count() <= 0 ){
            echo 'DONE';
            die();
        }

        foreach($collection_with_NWB as $nwb)
        {
            $coll_info = json_decode( $nwb->coll_info, 1);
            
            if( !empty( @$coll_info['payed'] ) ) 
            {
                $ttl_nwb = 0;
                $has_nwb = 0;
                foreach( $coll_info['payed'] as $pp1 )
                {
                    if( $pp1['typ'] == 'other_payable' )
                    {
                        $ttl_nwb += round($pp1['amount'], 2);
                        $has_nwb = 1;
                    }
                }

                $nwb->nw_amt = $ttl_nwb;
                $nwb->has_nw = $has_nwb;
                $nwb->save();

            }
            // echo '<pre>';
            // print_r($coll_info);
            // die();
        }//


        echo 'Please Wait... Processing '.$all_count;

        echo '<br />';

        echo '<script>setTimeout(()=>{
            document.location.reload();
        }, 1000);</script>';




    }//


    function daily_collection_report()
    {
        $uid = @$_GET['uid'];
        $dd = @$_GET['dd'];
        
        $mm = new CashierCtrl;
        return $mm->daily_collection_report_static($uid);

    }//

}
