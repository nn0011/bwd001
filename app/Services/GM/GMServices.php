<?php 

namespace App\Services\GM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Collection;
use App\Accounts;
use App\TempReport;
use App\HwdLedger;
use App\BillingAdjMdl;
use App\Services\Collections\CollectionService;

class GMServices
{
    static 
    function get_collection_per_month($mm)
    {

        $mm = date('Y-m', strtotime($mm));

        $results = DB::select("
                SELECT 
                (
                    SELECT 
                        SUM(payment) monthly_ttl 
                    FROM collections 
                    WHERE id IN (
                        SELECT MAX(id) FROM `collections`
                        WHERE payment_date like ?
                        GROUP BY invoice_num
                    )
                    AND status IN ('active', 'collector_receipt')        
                ) as SUM_WB,
                (
                    SELECT 
                        SUM(payment) monthly_ttl 
                    FROM collections 
                    WHERE id IN (
                        SELECT MAX(id) FROM `collections`
                        WHERE payment_date like ?
                        GROUP BY invoice_num
                    )
                    AND status IN ('or_nw', 'cr_nw')        
                ) as SUM_NWB

            ", [$mm.'%', $mm.'%']);


            $nw_amt = Collection::whereIn('status', ['active', 'collector_receipt'])
                            ->where('has_nw', 1)
                            ->where('payment_date', 'like', $mm.'%')
                            ->sum('nw_amt');

            $SUM_WB  = round($results['0']->SUM_WB - $nw_amt, 2);
            $SUM_NWB = round($results['0']->SUM_NWB + $nw_amt, 2);            
        
        // echo $SUM_WB;
        // echo '<br />';
        // echo $SUM_NWB;

        return [
            'SUM_WB' => $SUM_WB,
            'SUM_NWB' => $SUM_NWB
        ];

    }//

    static
    function get_employee_account()
    {
        $accounts = Accounts::where('is_employee', 1)
                            ->with('ledger_data7')
                                ->orderBy('lname', 'ASC')
                                    ->get();

        foreach($accounts as $acct)
        {
            $new_break_down = CollectionService::get_breakdown_collection_data($acct->id);

            $num_billing = 0;

            foreach( $new_break_down as $nbd1 ) 
            {
                if( $nbd1['typ'] == 'billing' ) 
                {
                    $num_billing++;
                }
            }

            $acct->num_bill = $num_billing;

        }//

        return $accounts;
    }//


    static
    function get_gm_collection_summary()
    {
        $collection_report = TempReport::where('status', 'active')
                                ->where('typ1', 'gm_collector_summary')
                                ->orderBy('date1', 'asc')
                                ->limit(6)
                                ->get();

        $coll_tick = [];
        $coll_nwb = [];
        $coll_wb = [];

        foreach($collection_report as $cr1)
        {
            $data1 = json_decode( $cr1->data1 ); 

            $coll_tick[] = date('M Y', strtotime($cr1->str1));
            $coll_nwb[]  = $data1->SUM_NWB;
            $coll_wb[]   = $data1->SUM_WB;
        }

        $collection_data = compact('coll_tick', 'coll_wb', 'coll_nwb');

        return $collection_data;

    }//


    static 
    function get_accounts_info()
    {
        $new_accounts = ['%from Pending%', '%To Active%'];
        $disconnted = ['%from Active%', '%To Disconn%'];
        $reconnect = ['%from Disconn%', '%To Active%'];

        $new_date = date('Y-m-01');

        $arr1 = [];

        for( $x=0;$x<=11;$x++ )
        {
            $date1 = date('Y-m', strtotime($new_date.' - '.$x.' Month'));
            $new_acct_results = self::HWD_Account_Count($date1, $new_accounts);
            $dis_acct_results = self::HWD_Account_Count($date1, $disconnted);
            $rec_acct_results = self::HWD_Account_Count($date1, $reconnect);

            $arr1[$date1] = [
                'new' => $new_acct_results->toArray(),
                'dis' => $dis_acct_results->toArray(),
                'rec' => $rec_acct_results->toArray(),
            ]; 
            
        }//

        $arr1 = array_reverse($arr1);

        return $arr1;

        echo '<pre>';
        print_r($arr1);
        die();        
    }//


    static function HWD_Account_Count($year, $cond1)
    {
        return HwdLedger::where('led_date2', 'like', $year.'%')
                            ->where('led_desc1','like', $cond1[0])
                            ->where('led_desc1','like', $cond1[1])
                            ->selectRaw('DATE_FORMAT(led_date2, "%Y-%m") dd1, DATE_FORMAT(led_date2, "%b") dd2, COUNT(id) ttl')
                            ->groupBy('dd1')                        
                            ->get();

    }//


    static 
    function get_adjustment_dashboard_chart()
    {
        $billing1  = BillingAdjMdl::where('status', 'active')->orderBy('date1', 'desc')->first();
        $adj_dates = date('Y-m-01', strtotime($billing1->date1.' - 5 Months') );

        $adjust6M  = BillingAdjMdl::where('status', 'active');

        $adjust6M->where('date1', '>=', $adj_dates);
        $result = $adjust6M->orderBy('date1', 'asc')->get();

        $adj_res1 = [];
        foreach($result as $r1)
        {   
           $adj_dates = date('Y-m-01', strtotime($r1->date1) );

           if( $r1->amount > 0) {
                @$adj_res1[$adj_dates]['pos_val'] += $r1->amount;
           }else{
                @$adj_res1[$adj_dates]['neg_val'] += abs($r1->amount);
           }
        }

        $tick_adj     = [];
        $pos_data     = [];
        $neg_data     = [];

        foreach(@$adj_res1 as $kk => $vv)
        {
            $tick_adj[] = date('M-Y', strtotime($kk) );

            if( @$vv['pos_val'] > 0 ) {
                $pos_data[] = @$vv['pos_val'];
            }else{
                $pos_data[] = 0;
            }

            if( (float) @$vv['neg_val'] > 0 ) {
                $neg_data[] = @$vv['neg_val'];
            }else{
                $neg_data[] = 0;
            }

        }

       return  $adj_data_info = [
                        'tic' => $tick_adj,
                        'pos' => $pos_data,
                        'neg' => $neg_data
                    ];//


    }//


    static 
    function get_daily_collection_data()
    {

        $daily_coll  = TempReport::where('status', 'active')
                                ->where('typ1', 'gm_daily_coll_rep')
                                ->orderBy('date1', 'asc')
                                // ->limit(15)
                                ->get();

        $daily_coll_tic   = [];
        $daily_coll_val1  = [];
        foreach($daily_coll as $d1){

            $daily_coll_tic[] = date(' m/d ',strtotime($d1->str1));
            $daily_coll_val1[]  = round($d1->amt_1, 2);
        }

        return compact('daily_coll_tic', 'daily_coll_val1');

    }//



}//

?>