<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use App\Services\Collections\CollectionReportService;
use Excel;

class AuditReportCtrl extends Controller
{

    function generate_audit_excel()
    {
        $ss = @$_GET['ss'];
        $ee = @$_GET['ee'];

		$daily_col_ids = CollectionReportService::daily_coll_max_ids_v2($ss, $ee);

		$col_all = Collection::whereIn('id', $daily_col_ids)
                            ->selectRaw('*, CONVERT(payment_date, DATE) as pp')
                            ->with(['accounts'])
                            ->orderBy('pp', 'asc')
                            ->orderBy('invoice_num', 'asc')
                            // ->limit(10)
                            ->get();        

        // echo $col_all->count();
        // echo '<pre>';
        // print_r($col_all->toArray());
        // die();

        $date_str = "COLLECTION FROM ".date('m/d/Y',strtotime($ss))." TO ".date('m/d/Y',strtotime($ee));

        // die();

		// Excel::load(public_path('excel02/daily_coll_report002.xls'), 
		Excel::load(public_path('excel02/daily_coll_report002_v2.xls'), 
			function($excel) use ($col_all, $date_str) {

                $sheet = $excel->getSheet(0);

                $sheet->setCellValue('A4', strtoupper( $date_str ));


                $row_6 = 6;
                foreach($col_all as $ca1)
                {
                    $A1 = date('m/d/Y', strtotime( $ca1->payment_date ));
                    $B1 = $ca1->invoice_num;
                    $C1 = $ca1->payment;

                    $E1  = ''.$ca1->accounts->acct_no;
                    $F1  = trim(strtoupper(''.@$ca1->accounts->fname.' '.@$ca1->accounts->mi.' '.@$ca1->accounts->lname));

                    $sheet->setCellValue('A'.$row_6, $A1);
                    $sheet->setCellValue('B'.$row_6, $B1);
                    $sheet->setCellValue('C'.$row_6, $C1);
                    $sheet->setCellValue('E'.$row_6, $E1);
                    $sheet->setCellValue('F'.$row_6, $F1);

                    if( in_array($ca1->status, ['cancel_cr', 'cancel_cr_nw', 'cancel_receipt', 'nw_cancel']))
                    {
                        $sheet->setCellValue('C'.$row_6, 0);
                        $sheet->setCellValue('D'.$row_6, 'CANCELED');
                        $sheet->setCellValue('E'.$row_6, '');
                        $sheet->setCellValue('F'.$row_6, '');
    
                    }


                    $row_6++;
                }



		})->download('xls');        


        
    }//

}//
