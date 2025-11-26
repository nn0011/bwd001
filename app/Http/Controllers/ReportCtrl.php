<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\AccountMetas;
use App\Accounts;
use App\Zones;
use App\HwdRequests;
use App\Reading;
use App\HwdOfficials;
use App\BillingMdl;
use App\BillingDue;

use App\Role;
use App\Collection;
use App\Reports;
use App\HwdLedger;
use App\User;
use App\BillingAdjMdl;
use App\LedgerData;
use Fpdf;
use PDF;
use Excel;




class ReportCtrl extends Controller
{
	
	function report_generate_reading_book($date1, $zone1)
	{
		$d1 = strtotime($date1);//Start
		$d2 = strtotime($date1 . ' - 1 Month');//Start
		$d3 = strtotime($date1 . ' + 1 Month');//Start
		
		$period = date('Y-m-01',$d1);
		$period_prev = date('Y-m-01',$d2);
		$period_next = date('Y-m-01',$d3);
		
		
		$active_account = Accounts::where('status', '!=', 'deleted')
									->where(function($q2){
											$q2->where('acct_status_key','!=', '-1');
											//~ $q2->where('acct_status_key', '2');
											//~ $q2->orWhere('acct_status_key', '2');
										})
										->where('zone_id', $zone1)
										->with(['reading1' => function($q1)use($period){
													$q1->where('period', $period);
											}])
											->orderBy('route_id', 'asc')
												->get();
		
		//~ echo '<pre>';
		//~ print_r($active_account->toArray());
		//~ die();
		
			
		return view('reports.reading_book_reff1', compact(
				'active_account',
				'period',
				'period_prev',
				'period_next',
				'zone1'
			));		
							
	}
	
	
	
	function report_adjustment_report_pdf($date1, $date2)
	{
		
		//~ return;
		//~ return;
		//~ return;
		
		
		$d1 = strtotime($date1);//Start
		$d2 = strtotime($date2);//End
		
		if($d1 > $d2){
			echo 'Date Error..';
			die();
		}
		
		$res1 = BillingAdjMdl::where('status','active')
					->where(function($q2){
							$q2->whereNull('adj_typ');
							$q2->orWhere('adj_typ', 'billing');
						})
						->where('date1','>=', date('Y-m-d', $d1))
							->where('date1','<=', date('Y-m-d', $d2))
								->with(['acct'])
								->with(['ledger1'])
									->get();
		
		$ret3 = array();
		$x=0;
		foreach($res1 as $rr)
		{
			$ret3[$x] = (object) array();
			$ret3[$x]->bam     		= $rr->id;
			$ret3[$x]->acct_id      = $rr->acct_id;
			$ret3[$x]->acct_no      = $rr->acct_no;
			$ret3[$x]->date1        = $rr->date1;
			$ret3[$x]->adj_typ_desc = $rr->adj_typ_desc;
			
			$ret3[$x]->bill1 = null;
			$cbil1 = BillingMdl::where('account_id', $rr->acct_id)
						->where('status','active')
						->where('period', '<=', '2020-02-13')
							->orderBy('id', 'desc')
								->first();
			
			if($cbil1){
				$ret3[$x]->bill1 = (object) $cbil1->toArray();
			}
			
			$ret3[$x]->acct1   = (object) $rr->acct->toArray();
			
			$ret3[$x]->ledger1 = null;
			
			if($rr->ledger1){
				$ret3[$x]->ledger1 = (object) $rr->ledger1->toArray();
			}

			$x++;
		}

		//~ echo '<pre>';
		//~ print_r($res1->toArray());
		//~ die();
		
		
			//~ $pdf = PDF::loadView('reports.cons_report_1', compact(
						//~ 'result1', 
						//~ 'zone_id', 
						//~ 'report_name',
						//~ 'as_of_name'
					//~ ));
			//~ return $pdf->stream('cons_report1'.date('Ymd').'.pdf');
			
		return view('reports.billing_adjustment_report', compact(
				'ret3'
			));
		
	}//
	
	function report_get_collectors()
	{
		$roles = Role::find(4);
		$usr = $roles->users;
		
		$usr_html = '<select class="coll_id">';
		
		$usr_html .= '<option value="all">ALL</option>';
		
		foreach($usr as $u)
		{
			$usr_html .= '<option value="'.$u->id.'">'.strtoupper($u->name).'</option>';
		}
		$usr_html .= '</select>';
		
		$butt11 = '
				<br />
				<br />
				<button onclick="daily_collect1()">Daily Collection Report</button>		
		';
		
		return array(
				'usr_html' => $usr_html.$butt11
				
		);
		//~ echo '<pre>';
		//~ print_r($usr->toArray());
		
	}
	
	function consessionare_report_step1_voluntary_disconnection_pdf($period1, $zone_id)
	{
		return $this->consessionare_report_step1_disconnected_pdf($period1, $zone_id, $cmd='voluntary_disconnection');
	}
	
	function consessionare_report_step1_pending_approval_pdf($period1, $zone_id)
	{
		return $this->consessionare_report_step1_disconnected_pdf($period1, $zone_id, $cmd='pending_approval');
	}
	
	function consessionare_report_step1_new_consessionare_pdf($period1, $zone_id)
	{
		return $this->consessionare_report_step1_disconnected_pdf($period1, $zone_id, $cmd='new_consessionare');
	}
	
	function consessionare_report_step1_reconnected_pdf($period1, $zone_id)
	{
		return $this->consessionare_report_step1_disconnected_pdf($period1, $zone_id, $cmd='reconnected');
	}
	
	function consessionare_report_step1_disconnected_pdf($period1, $zone_id, $cmd='')
	{
			$date1 = date('Y-m-d', strtotime($period1.' + 1 day'));
			$date2 = date('Y-m-01', strtotime($period1));
			
			$sql_stat = "    AND HL.led_desc1 like '%to Disconnected%'   ";
			$sql_stat1 = "    AND HL.ctyp1='account_modify'   ";
			
			$report_name = strtoupper('DISCONNECTED '.date('F Y', strtotime($date2)));
			$as_of_name =  strtoupper('AS OF '.date('F d, Y', strtotime($period1)));
			
			
			
			if($cmd == 'reconnected'){
				$sql_stat = "    AND HL.led_desc1 like '%to Active%'   ";
				$sql_stat .= "    AND HL.led_desc1 like '%from Disconnected%'   ";
				$report_name = strtoupper('RECONNECTED '.date('F Y', strtotime($date2)));
			}
			
			if($cmd == 'new_consessionare')
			{
				$sql_stat = ""; 
				$sql_stat1 = "  AND led_title like '%Application approved%'  AND HL.ctyp1='account1'   ";
				$report_name = strtoupper('NEW CONSESSIONARE '.date('F Y', strtotime($date2)));
			}
			
			if($cmd == 'pending_approval')
			{
				$sql_stat = ""; 
				$sql_stat1 = "  AND led_title like '%New application%'  AND HL.ctyp1='account1'   ";
				$report_name = strtoupper('PENDING APPROVAL '.date('F Y', strtotime($date2)));
			}
			
			if($cmd == 'voluntary_disconnection')
			{
				$sql_stat = ""; 
				$sql_stat1 = "  AND HL.led_desc1 like '%to Voluntary%'  AND HL.ctyp1='account_modify'   ";
				$report_name = strtoupper('VOLUNTARY DISCONNECTION '.date('F Y', strtotime($date2)));
			}
			
			
			$sql1 = "
				SELECT 
					HL.led_key1,
					HL.led_date2, 
					AA.acct_no, 
					AA.fname, 
					AA.lname, 
					AA.acct_status_key,
					AA.meter_number1,
					ZZ.zone_name,
					AA.status
				FROM `hwd_ledgers` as HL 
				LEFT JOIN accounts as AA on AA.id=HL.led_key1
				LEFT JOIN zones as ZZ on ZZ.id = AA.zone_id
				WHERE 
					HL.led_date2 < ? AND 
					HL.led_date2 >= ?  AND
					AA.status != 'deleted' 
					
			";
			
			
			$sql1 .= $sql_stat; 
			$sql1 .= $sql_stat1; 
			
			
			if($zone_id != 'all'){
				
				$sql1.="
					
					AND AA.zone_id = ?
				
				";
			}
			
			$result1 = DB::select($sql1, [$date1, $date2, $zone_id]);

			// echo $sql1;
			// die();
			
			//~ echo '<pre>';
			//~ print_r($result1);
			//~ die();
			
			
			$pdf = PDF::loadView('reports.cons_report_1', compact(
						'result1', 
						'zone_id', 
						'report_name',
						'as_of_name'
					));
			return $pdf->stream('cons_report1'.date('Ymd').'.pdf');				
		
	}	
	
	
	function report_penalty_report_by_zone($zone_id, $date1)
	{
		
		
		$period1 = date('Y-m-01', strtotime($date1));
		
		$zone1 = Zones::where('status','active')
					->orderBy('id', 'asc')
						->get()
						->toArray();
		
		$res1 = array();
		$jj=0;
		foreach($zone1 as $z1){
			
			$due1 = BillingDue::where('period', $period1)
						->where('due1', '>', 0)
						->where('due_stat', 'active')
						->whereHas('account', function($q1)use($z1){
							$q1->where('zone_id', $z1['id']);
							//~ $q1->where('status', 'active');
						})
						->selectRaw('SUM(due1) as PP1, SUM(bill_balance) as PP2, COUNT(id) as PP3 ')
							->first();
			
			$res1[$jj]['data'][] = (object) ['ID'=>$due1->PP3,'CURR_BILL'=>$due1->PP2, 'DUE1'=>$due1->PP1];
			$res1[$jj]['zone'] = $z1;
			$jj++;			
			
		}//
		
		$pdf = PDF::loadView('reports.penalty_rep2', compact('res1', 'zone_id', 'date1'));
		return $pdf->stream('report_penalty_report_by_zone_'.date('Ymd').'.pdf');	
		
	}//
	
	
	function XXXX_report_penalty_report_by_zone($zone_id, $date1)
	{
		
		$sql1 = $this->report_penalty_report(1, $date1, 1);
		$sql2 = "SELECT COUNT(id) as ID, SUM(curr_bill) as CURR_BILL, SUM(due1) as DUE1 FROM ($sql1) AS TAB1";		
		$period1 = date('Y-m-01', strtotime($date1));
		
		/*
		$penalty_result = DB::select($sql2, [$period1, $period1, $zone_id]);
		echo '<pre>';
		print_r($penalty_result);
		die();
		*/
		
		$zone1 = Zones::where('status','active')
					->orderBy('id', 'asc')
						->get()
						->toArray();
		
		
		$res1 = array();
		$jj=0;
		foreach($zone1 as $z1){
			$res1[$jj]['data'] = DB::select($sql2, [$period1, $period1, $z1['id']]);
			$res1[$jj]['zone'] = $z1;
			$jj++;
		}
		
		//~ echo '<pre>';
		//~ print_r($res1);
		//~ die();
		
		$pdf = PDF::loadView('reports.penalty_rep2', compact('res1', 'zone_id', 'date1'));
		return $pdf->stream('report_penalty_report_by_zone_'.date('Ymd').'.pdf');				
		
		
		
	}//
	
	function report_penalty_report_0001($zone_id, $date1, $dd=0)
	{
		$period1 = date('Y-m-01', strtotime($date1));
		
		$due1 = BillingAdjMdl::where('period', $period1)
					->where('due_stat', 'active')
						->with('account')
							->get();

	}//
	
	
	function report_penalty_report($zone_id, $date1, $dd=0)
	{
		
		$period1 = date('Y-m-01', strtotime($date1));
		
		$sql1 = "
				SELECT 
						AA.id, 
						AA.acct_no, 
						AA.fname, 
						AA.lname, 
						AA.zone_id,
						AA.route_id,
						BD.due1,
						BD.bill_id,
						BM.curr_bill,
						BM.consumption,
						BM.read_PC,
						BM.due_stat,
						BM.bill_num_01, 
						BM.discount 
					FROM accounts as AA
						LEFT JOIN billing_dues as BD ON (BD.acct_id = AA.id AND  BD.period=?)
						LEFT JOIN billing_mdls as BM ON (BM.id=BD.bill_id)
				WHERE EXISTS(
				   SELECT acct_id FROM `billing_dues` WHERE period = ? AND AA.id = acct_id
				  )
				  
					
					";

		
		if($zone_id != 'all')
		{
				$sql1 .= "	

							  AND 
								AA.zone_id = ?
							
						";
		}
		
					
		$sql1 .= "
				
				  AND 
					BD.due_stat = 'active'
					
				  ORDER BY AA.route_id ASC
			";
			
			if($dd == 1){
				return $sql1;
			}
			
			
		
		$penalty_result = DB::select($sql1, [$period1, $period1, $zone_id]);
		
		foreach($penalty_result as $pr1)
		{
			//~ $pr1->bill_id
			$adj1 = BillingAdjMdl::where('bill_id', $pr1->bill_id)
						->where('status', 'active')
							->sum('amount');
			
			$pr1->all_adj = $adj1;
			//~ $pr1->curr_bill = $pr1->curr_bill - $adj1;
			//~ $pr1->curr_bill = $pr1->curr_bill;
			
		}//
		
		// echo '<pre>';
		// print_r($penalty_result);
		// die();

		$this->report_penalty_report_to_excel(compact('penalty_result', 'zone_id', 'date1'));

		// return view('reports.penalty_rep1', compact('penalty_result', 'zone_id', 'date1'));
		
		
		// $pdf = PDF::loadView('reports.penalty_rep1', compact('penalty_result', 'zone_id', 'date1'));
		// return $pdf->stream('daily_penalty_report_'.date('Ymd').'.pdf');		
		
	}//

	private 
	function report_penalty_report_to_excel($dat1)
	{
		extract($dat1); //compact('penalty_result', 'zone_id', 'date1')

		// foreach(@$penalty_result as $k => $v) {
		// 	$pr = $v;
		// 	$bill_net = $pr->curr_bill - $pr->discount;
		// }

		Excel::load(public_path('excel02/penalty_reports_001.xlsx'), 
		function($excel) use ($penalty_result, $date1) {
	
			$sheet = $excel->getSheet(0);
			$sheet->setCellValue('A4', strtoupper( date('F d, Y', strtotime($date1)) ));
	
	
			$row_6 = 7;
			$xx = 1;

			$ttl_biling  = 0;
			$ttl_penalty = 0;

			foreach($penalty_result as $k => $v)
			{

				$pr = $v;
				$bill_net = $pr->curr_bill - $pr->discount;

				$A1 = $xx;
				$B1 = $pr->bill_id;
				$C1 = $pr->acct_no;
				$D1 = $pr->fname.' '.$pr->lname;
				$E1 = round($bill_net,2);
				$F1 = round($pr->due1,2);

				$ttl_biling  += round($E1,2);
				$ttl_penalty += round($F1,2);

				$sheet->setCellValue('A'.$row_6, $A1);
				$sheet->setCellValue('B'.$row_6, $B1);
				$sheet->setCellValue('C'.$row_6, $C1);
				$sheet->setCellValue('D'.$row_6, $D1);
				$sheet->setCellValue('E'.$row_6, $E1);
				$sheet->setCellValue('F'.$row_6, $F1);

	
				$row_6++;
				$xx++;
			}

			$sheet->mergeCells('A'.$row_6.':D'.$row_6);
			$sheet->getStyle('A'.$row_6.':F'.$row_6)->getFont()->setBold(true);

			$sheet->setCellValue('A'.$row_6, 'TOTAL');
			$sheet->setCellValue('E'.$row_6, $ttl_biling);
			$sheet->setCellValue('F'.$row_6, $ttl_penalty);
			$sheet->getStyle('A'.$row_6.':F'.$row_6)
				->getFill()
				->setFillType('solid')
				->getStartColor()
				->setRGB('F2F1F0');
	
	
	})->download('xlsx');        		


	}//
	
	function for_disconnection()
	{
	}

	
	function ReportGetAcknowledgement($zone, $full_date, $type1='')
	{
		$curr_period = date('Y-m-28', strtotime($full_date));
		$current_date_label = date('F d, Y', strtotime($full_date));

		$result1 = Reports::where('rtype', 'ageing_of_accounts')
			->where('period', $curr_period)
			->where('accounts.zone_id', $zone)
			->whereNotNull('billing_total')
			->whereNull('collected')
			->orWhere(function($query){
				$query->whereRaw('billing_total != collected');
			})
			->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
			->orderBy('reports.full_name', 'asc')
			->addSelect(DB::raw('reports.*,
				accounts.zone_id as zone_orig,
				accounts.meter_number1
			'))
			//->first();
			->get();

		$zones1  = Zones::where('status', '!=', 'deleted')->get();
		$zone_arr = array();
		foreach($zones1 as $zz)
		{
			$zone_arr[$zz->id] = $zz->zone_name;
		}

		if($type1 == 'pdf')
		{
			return compact('curr_period', 'current_date_label', 'result1', 'pdf_link', 'zone_arr');
		}

		// echo '<pre>';
		// print_r($result1->toArray());
		///billing/report_get_report_acknowledgement/4/2018-08-15

		$pdf_link = '/billing/report_get_report_acknowledgement_pdf/'.$zone.'/'.$full_date;

		return view('billings.inc.billing_reports.acknowledgement_report_ajax1',
		compact('curr_period', 'current_date_label', 'result1', 'pdf_link', 'zone_arr'));
	}	
	
	function ReportGetAcknowledgementPDF($zone, $full_date)
	{
		$report1 = $this->ReportGetAcknowledgement($zone, $full_date, 'pdf');
		extract($report1);

		if(!$result1){
			 echo 'No Acknowledgement Report as of '.$current_date_label;
		 	return;
		}

		Fpdf::AddPage('P', 'Letter');
	     Fpdf::SetMargins(6, 6, 6);
	     Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Ln();
	     Fpdf::Cell(75,4,WD_NAME,0,1,'L', false);
	     Fpdf::Cell(75,4,WD_ADDRESS,0,1,'L', false);
		Fpdf::Cell(75,4,'Acknowledgement Report',0,1,'L', false);
		Fpdf::Cell(75,4,$zone_arr[$zone],0,1,'L', false);
	     Fpdf::Cell(75,4,'As of '.$current_date_label,0,1,'L', false);
		Fpdf::Ln();
		Fpdf::Ln();

		Fpdf::SetFont('Courier',"B", 8);
		Fpdf::Cell(30,5,'Account No.','B',0,'L', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(40,5, 'Concessionaires','B',0,'L', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(30,5,'Bill No.','B',0,'C', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(20,5,'Usage CUM','B',0,'L', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(30,5,'Billed Amount','B',0,'R', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(30,5,'Remarks','B',0,'L', false);
		Fpdf::Ln();

		Fpdf::SetFont('Courier',null, 8);

		$sub_total_arr = array(0,0);
		$grand_total_arr = array(0,0);

		$xx = 0;
		foreach($result1 as $kk => $rr1){

			if($xx >= 50)
			{
				$xx = 0;

				$grand_total_arr[0]+=$sub_total_arr[0];
				$grand_total_arr[1]+=$sub_total_arr[1];

				Fpdf::Cell(30,7,'Subtotal','T',0,'L', false);
				Fpdf::Cell(2,7,'','T',0,'L', false);
				Fpdf::Cell(40,7, '','T',0,'L', false);
				Fpdf::Cell(2,7,'','T',0,'L', false);
				Fpdf::Cell(30,7,'','T',0,'L', false);
				Fpdf::Cell(2,7,'','T',0,'L', false);
				Fpdf::Cell(20,7,number_format($sub_total_arr[0]),'T',0,'L', false);
				Fpdf::Cell(2,7,'','T',0,'L', false);
				Fpdf::Cell(30,7,number_format($sub_total_arr[1]),'T',0,'R', false);
				Fpdf::Cell(2,7,'','T',0,'L', false);
				Fpdf::Cell(30,7,'','T',0,'L', false);
				Fpdf::Ln();
				Fpdf::Cell(0,5,'Page '.Fpdf::PageNo().' / {nb}',0,0,'L', false);
				$sub_total_arr = array(0,0);


				Fpdf::SetFont('Courier',"B", 8);
				Fpdf::AddPage('P', 'Letter');
				Fpdf::Ln();
				Fpdf::Cell(30,5,'Account No.','B',0,'L', false);
				Fpdf::Cell(2,5,'',0,0,'L', false);
				Fpdf::Cell(40,5, 'Concessionaires','B',0,'L', false);
				Fpdf::Cell(2,5,'',0,0,'L', false);
				Fpdf::Cell(30,5,'Bill No.','B',0,'L', false);
				Fpdf::Cell(2,5,'',0,0,'L', false);
				Fpdf::Cell(20,5,'Usage CUM','B',0,'L', false);
				Fpdf::Cell(2,5,'',0,0,'L', false);
				Fpdf::Cell(30,5,'Billed Amount','B',0,'R', false);
				Fpdf::Cell(2,5,'',0,0,'L', false);
				Fpdf::Cell(30,5,'Remarks','B',0,'L', false);
				Fpdf::Ln();
				Fpdf::SetFont('Courier',null, 6);

			}



			$dd1 = null;
               if(!empty($rr1->data1)){
                    $dd1 = json_decode($rr1->data1);
               }

			$sub_total_arr[0] += @$dd1->cum;
			$sub_total_arr[1] += @$rr1->billing_total;

			Fpdf::Cell(30,3,$rr1->account_num,0,0,'L', false);
			Fpdf::Cell(2,3,'',0,0,'L', false);
			Fpdf::Cell(40,3, $rr1->full_name,0,0,'L', false);
			Fpdf::Cell(2,3,'',0,0,'L', false);
			Fpdf::Cell(30,3,@$dd1->billing_id,0,0,'C', false);
			Fpdf::Cell(2,3,'',0,0,'L', false);
			Fpdf::Cell(20,3,@$dd1->cum,0,0,'C', false);
			Fpdf::Cell(2,3,'',0,0,'L', false);
			Fpdf::Cell(30,3,number_format($rr1->billing_total, 2),0,0,'R', false);
			Fpdf::Cell(2,3,'',0,0,'L', false);
			Fpdf::Cell(30,3,'----',0,0,'L', false);
			Fpdf::Ln();
			$xx++;
			unset($result1[$kk]);
		}

		$grand_total_arr[0]+=$sub_total_arr[0];
		$grand_total_arr[1]+=$sub_total_arr[1];


		Fpdf::Cell(30,7,'Subtotal','T',0,'L', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(40,7, '','T',0,'L', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(30,7,'','T',0,'L', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(20,7,number_format($sub_total_arr[0]),'T',0,'C', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(30,7,number_format($sub_total_arr[1]),'T',0,'R', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(30,7,'','T',0,'L', false);
		Fpdf::Ln();
		$sub_total_arr = array(0,0);

		Fpdf::Cell(30,7,'Grand Total','T',0,'L', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(40,7, '','T',0,'L', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(30,7,'','T',0,'L', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(20,7,number_format($grand_total_arr[0]),'T',0,'C', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(30,7,number_format($grand_total_arr[1]),'T',0,'R', false);
		Fpdf::Cell(2,7,'','T',0,'L', false);
		Fpdf::Cell(30,7,'','T',0,'L', false);
		Fpdf::Ln();

		Fpdf::Cell(0,5,'Page '.Fpdf::PageNo().' / {nb}',0,0,'L', false);
		Fpdf::Ln();

		Fpdf::AliasNbPages();

		Fpdf::Output();
		exit;

	}//
	
	
	
	
	function billing_summary_get_zone_class_pdf_all($full_date)
	{
		$curr_period = date('Y-m', strtotime($full_date));
		$current_date_label = date('F d, Y', strtotime($full_date));
		$mm_date = date('F Y', strtotime($full_date));
		
		//~ echo $curr_period;
		//~ die();

		$acct_class = AccountMetas::where('meta_type', 'account_type')
							->orderBy('meta_name')
								->get();

		$zones = Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')->get();
								
		$clas_arr = array();
		foreach($acct_class as $acc)
		{
			$clas_arr[$acc->id] = $acc->meta_name;
		}
		
		$zone_arr = array();
		foreach($zones as $zz)
		{
			$zone_arr[$zz->id] = $zz->zone_name;
		}
								
		$pdf = PDF::loadView('reports.billing_comsump_report', 
								compact(
										'clas_arr', 
										'curr_period', 
										'zone_arr', 
										'mm_date'
									));

		return $pdf->stream('billing_comsump_report.pdf');


	}//
	
	function BillingSummaryZoneClassPDF($full_date)
	{
		$curr_period = date('Y-m-28', strtotime($full_date));
		$current_date_label = date('F d, Y', strtotime($full_date));
		$mm = date('F Y', strtotime($full_date));

		
		$acct_class = AccountMetas::where('meta_type', 'account_type')
								->orderBy('meta_name')
								->get();

		$zones = Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')
								->get();
								
								
		$clas_arr = array();
		foreach($acct_class as $acc)
		{
			$clas_arr[$acc->id] = $acc->meta_name;
		}
		
		$zone_arr = array();
		foreach($zones as $zz)
		{
			$zone_arr[$zz->id] = $zz->zone_name;
		}


		pdf_heading1('Billing Summary ', $mm);
		Fpdf::SetFont('Courier',"", 7);
		
		Fpdf::SetFont('Courier',"B", 7);
		Fpdf::Cell(15,10, 'By Classification', '',0,'L', false);	
		Fpdf::SetFont('Courier',"", 7);
		Fpdf::Ln();		
		$this->BillingSummaryZoneClassPDF___part_heading();
		Fpdf::Ln();		
		
		Fpdf::SetLeftMargin(5);

		$ttl_usage = 0;
		$ttl_water_sales = 0;
		$ttl_penalty = 0;
		$ttl_arrear = 0;
		$ttl_discount = 0;		
		$ttl_cons = 0;		
		
		
		foreach($clas_arr as $kk=>$vv)
		{
			
			$usage = 0;
			$water_sales = 0;
			$penalty = 0;
			$arrear = 0;
			$discount = 0;
			$cons = 0;
			
			$class_info_raw  = $this->BillingSummaryZoneClassPDF___getClassInfo($kk, $curr_period);
			$class_info = $class_info_raw[0];
			
			//~ echo '<pre>';
			//~ print_r($class_info->toArray());
			//~ die();
			
			if(!empty($class_info->ttl_curr_bill))
			{
				$water_sales = $class_info->ttl_curr_bill;
			}

			if(!empty($class_info->ttl_consum))
			{
				$usage = $class_info->ttl_consum;
			}
			
			if(!empty($class_info->ttl_arrear))
			{
				$arrear = $class_info->ttl_arrear;
			}
			
			if(!empty($class_info->ttl_discount))
			{
				$discount = $class_info->ttl_discount;
			}
			
			if(!empty($class_info->ttl_penalty))
			{
				$penalty = $class_info->ttl_penalty;
			}

			if(!empty($class_info->ttl_cons))
			{
				$cons = $class_info->ttl_cons;
			}			
			
			//~ print_r($class_info->toArray());
				
			Fpdf::Cell(35,6, $vv, '',0,'L', false);	
			Fpdf::Cell(20,6, number_format(@$cons,0), '0',0,'C', false);	
			Fpdf::Cell(20,6, number_format(@$usage,0), '0',0,'C', false);	
			Fpdf::Cell(30,6, number_format(@$water_sales,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$penalty,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$discount,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$arrear,2), '0',0,'R', false);	
			Fpdf::Ln();		
			
			$ttl_usage += $usage;
			$ttl_water_sales += $water_sales;
			$ttl_penalty += $penalty;
			$ttl_arrear += $arrear;
			$ttl_discount += $discount;
			$ttl_cons += $cons;
			
		}
		
		Fpdf::Cell(195,2, '', 'B',0,'L', false);	
		Fpdf::Ln();		

		Fpdf::SetFont('Courier',"B", 7);
		Fpdf::Cell(35,6, 'Total', '',0,'L', false);	
		Fpdf::Cell(20,6, number_format(@$ttl_cons,0), '0',0,'C', false);	
		Fpdf::Cell(20,6, number_format(@$ttl_usage,0), '0',0,'C', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_water_sales,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_penalty,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_discount,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_arrear,2), '0',0,'R', false);	
		Fpdf::Ln();				
		Fpdf::Ln();				

		Fpdf::SetFont('Courier',"", 7);
		
		Fpdf::SetFont('Courier',"B", 7);
		Fpdf::Cell(15,10, 'By Zone', '',0,'L', false);	
		Fpdf::SetFont('Courier',"", 7);
		Fpdf::Ln();		
		$this->BillingSummaryZoneClassPDF___part_heading('Zone');
		Fpdf::Ln();			
		
		
		$ttl_usage = 0;
		$ttl_water_sales = 0;
		$ttl_penalty = 0;
		$ttl_arrear = 0;
		$ttl_discount = 0;				
		$ttl_cons = 0;		

		foreach($zone_arr as $kk=>$vv)
		{
			$usage = 0;
			$water_sales = 0;
			$penalty = 0;
			$arrear = 0;
			$discount = 0;			
			$cons = 0;

			$class_info_raw  = $this->BillingSummaryZoneClassPDF___getZoneInfo($kk, $curr_period);
			$class_info = $class_info_raw[0];			
			
			if(!empty($class_info->ttl_curr_bill)){$water_sales = $class_info->ttl_curr_bill;}
			if(!empty($class_info->ttl_consum)){$usage = $class_info->ttl_consum;}
			if(!empty($class_info->ttl_arrear)){$arrear = $class_info->ttl_arrear;}
			if(!empty($class_info->ttl_discount)){$discount = $class_info->ttl_discount;}
			if(!empty($class_info->ttl_penalty)){$penalty = $class_info->ttl_penalty;}
			if(!empty($class_info->ttl_cons)){$cons = $class_info->ttl_cons;}			
			
			Fpdf::Cell(35,6, $vv, '',0,'L', false);	
			Fpdf::Cell(20,6, number_format(@$cons,0), '0',0,'C', false);	
			Fpdf::Cell(20,6, number_format(@$usage,0), '0',0,'C', false);	
			Fpdf::Cell(30,6, number_format(@$water_sales,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$penalty,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$discount,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$arrear,2), '0',0,'R', false);	
			Fpdf::Ln();					
			
			$ttl_usage += $usage;
			$ttl_water_sales += $water_sales;
			$ttl_penalty += $penalty;
			$ttl_arrear += $arrear;
			$ttl_discount += $discount;
			$ttl_cons += $cons;

		}//
		
		Fpdf::Cell(195,2, '', 'B',0,'L', false);	
		Fpdf::Ln();				
		
		Fpdf::SetFont('Courier',"B", 7);
		Fpdf::Cell(35,6, 'Total', '',0,'L', false);	
		Fpdf::Cell(20,6, number_format(@$ttl_cons,0), '0',0,'C', false);	
		Fpdf::Cell(20,6, number_format(@$ttl_usage,0), '0',0,'C', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_water_sales,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_penalty,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_discount,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_arrear,2), '0',0,'R', false);	
		Fpdf::Ln();				
		Fpdf::Ln();				
		

		//~ echo '<pre>';
		//~ print_r($clas_arr);	
		//~ print_r($zone_arr);	
		//~ die();
		
		Fpdf::Ln();		
		Fpdf::Ln();		
		pdf_footer_signature();
		Fpdf::Ln();		
		Fpdf::Ln();				
		
		
		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;
		
	}//End
	
	function BillingSummaryZoneClassPDF___getClassInfo($cls_id, $curr_period)
	{
		$new_cur = date('Y-m',strtotime($curr_period));
		
		//$billing1  = BillingMdl::where('period', $curr_period)
		$billing1  = BillingMdl::where('period','like', $new_cur.'%')
						->where('status','active')
						->whereNotNull('consumption')
						->whereHas('account', function($query)use($cls_id){
							$query->where('acct_type_key',$cls_id);
							//~ $query->where('status','active');
						})
						->select(DB::raw('
								SUM(billing_total) as  ttl_curr_bill,  
								SUM(consumption)  ttl_consum,
								SUM(arrears)  ttl_arrear,
								SUM(discount) ttl_discount,
								SUM(penalty) ttl_penalty,
								COUNT(id) ttl_cons
						'))
						->get();	
						

		//Fix the accuracy of the Penalty
		//START HERE 
		$bill_due = BillingDue::where('period','like', $new_cur.'%')
					->where('due_stat','active')
					->whereHas('account', function($q1)use($cls_id){
							$q1->where('acct_type_key',$cls_id);
						})
						->selectRaw('SUM(due1) as PP1')
							->first();
												
		$billing1[0]->ttl_penalty = $bill_due->PP1;
		//END HERE						
									
		return $billing1;
	}//
	
	function BillingSummaryZoneClassPDF___getZoneInfo($zone_id, $curr_period)
	{
		
		$new_cur = date('Y-m',strtotime($curr_period));
		
		//$billing1  = BillingMdl::where('period', $curr_period)
		$billing1  = BillingMdl::where('period','like', $new_cur.'%')
						->where('status','active')
						->whereNotNull('consumption')
						->whereHas('account', function($query)use($zone_id){
							$query->where('zone_id',$zone_id);
							//~ $query->where('status','active');
						})
						->select(DB::raw('
								SUM(billing_total) as  ttl_curr_bill,  
								SUM(consumption)  ttl_consum,
								SUM(arrears)  ttl_arrear,
								SUM(discount) ttl_discount,
								SUM(penalty) ttl_penalty,
								COUNT(id) ttl_cons
						'))
						->get();


		
		//Fix the accuracy of the Penalty
		//START HERE 
		$bill_due = BillingDue::where('period','like', $new_cur.'%')
					->where('due_stat','active')
					->whereHas('account', function($q1)use($zone_id){
							$q1->where('zone_id', $zone_id);
							//~ $q1->where('status','active');
						})
						->selectRaw('SUM(due1) as PP1')
							->first();
												
		$billing1[0]->ttl_penalty = $bill_due->PP1;
		//END HERE
		
		return $billing1;
	}//
	

	function BillingSummaryZoneClassPDF___part_heading($title_mm='Classification')
	{
		$x = Fpdf::GetX();
		$y = Fpdf::GetY();
		$x = 5;

		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(35,6,$title_mm,'LTB' , 'C');
		$x += 35;		

		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(20,3,"Cons.\ncount",'LTB' , 'C');
		$x += 20;
				
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(20,3,"Usage\nCUM",'LTB' , 'C');
		$x += 20;
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(30,6,"Water Sales",'LTB' , 'R');
		$x += 30;		
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(30,6,"Penalty",'LTB' , 'R');
		$x += 30;		
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(30,6,"Discount",'LTB' , 'R');
		$x += 30;		
				
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(30,6,"Arrears",'LTBR' , 'R');
		$x += 30;				
		//~ Fpdf::Ln();		
		//~ Fpdf::SetLeftMargin(5);
		Fpdf::Cell(0,2, '', '',0,'L', false);			
	}//
	
	
	function BillingSummaryAccountPDF_V2($zone, $full_date, $type1)
	{
		
		if($zone == 'all'){
			$my_zone = 'ALL ZONE';
			echo ' Please specify zone...';
			die();
		}

		$my_zone = get_zone101($zone);

		
		//~ echo $my_zone;
		//~ echo '<br />';
		//~ echo $full_date;
		//~ echo '<br />';
		//~ echo $type1;
		//~ die();
		
		$curr_period = date('Y-m', strtotime($full_date));
		$current_date_label = date('F d, Y', strtotime($full_date));
		$mm = date('F Y', strtotime($full_date));

		$billing1  = BillingMdl::where('period','like', $curr_period.'%')
						->where('billing_mdls.status','active')
						->whereHas('account', function($query)use($zone){
							
								if($zone != 'all'){
									$query->where('zone_id', $zone);
								} else {
									//
								}
								
						})
						->whereNotNull('consumption')
						->where('billing_mdls.status','active')
						->with('account')
						->with(['bill_arrear' => function($query) use($curr_period) {
							$query->where('period', $curr_period.'-01');
						}])
						->with(['nw_bill' => function($query) use($curr_period) {
							$query->where('date1', $curr_period.'-01');
							$query->where('typ', 'nw_child');
						}])
						//~ ->orderBy('account.route_id')
						->selectRaw('billing_mdls.*,route_id')
						->leftJoin('accounts', 'accounts.id', '=', 'billing_mdls.account_id')
						->orderBy('route_id', 'asc')
						->get();
						//~ ->sortBy('account.route_id');
					
		
		$acct_class = AccountMetas::where('meta_type', 'account_type')->orderBy('meta_name')->get();
		$zones      = Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')->get();
		
		#####
		$zone_arr = array();
		foreach($zones as $zz){
			$zone_arr[$zz->id] = $zz->zone_name;
		}//
										
								
		#####
		$clas_arr = array();
		foreach($acct_class as $acc){
			$clas_arr[$acc->id] = $acc->meta_name;
		}//

		// $zero1 = LedgerData::get_latest_ledger_logs(2650);
		// ee($zero1, __FILE__, __LINE__);
		// ee($billing1->toArray(), __FILE__, __LINE__);
		
		return view('reports.billing_report1', compact('billing1', 'my_zone', 'full_date' ));
				
		// $pdf = PDF::loadView('reports.billing_report1', compact(
		// 			'billing1',
		// 			'my_zone',
		// 			'full_date'
		// 		));
		// return $pdf->stream('billing_report_'.date('Ymd').'.pdf');			
	
	}//
	
	function BillingSummaryAccountPDF($zone, $full_date, $type1='')
	{
		return $this->BillingSummaryAccountPDF_V2($zone, $full_date, $type1);
	}//
	
	
	function BillingSummaryAccountPDF___part_heading()
	{
		$x = Fpdf::GetX();
		$y = Fpdf::GetY();
		$x = 0;

		Fpdf::MultiCell(15,3,'Account No.','LTB' , 'L');
		$x += 20;
		
		Fpdf::SetXY($x, $y);
		Fpdf::MultiCell(30,6,'Concessionaires','LTB' , 'L');
		$x += 30;
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(15,3,"Bill Number",'LTB' , 'C');
		$x += 15;
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(15,3,'Prev. Reading','LTB' , 'C');
		$x += 15;
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(15,3,'Cur. Reading','LTB' , 'C');
		$x += 15;
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(15,3,"Usage\nCUM",'LTB' , 'C');
		$x += 15;
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(20,6,"Classified",'LTB' , 'C');
		$x += 20;		
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(20,6,"Water Sales",'LTB' , 'C');
		$x += 20;		
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(20,6,"Penalty",'LTB' , 'C');
		$x += 20;		
		
		Fpdf::SetXY($x, $y);		
		Fpdf::MultiCell(20,6,"Arrears",'LTBR' , 'C');
		$x += 20;				
		
		Fpdf::SetLeftMargin(5);
		Fpdf::Cell(185,1, '', 'T',0,'L', false);			
	}
	
	function BillingSummaryAnnualPDF___get_by_month($month)
	{
		$billing1  = BillingMdl::where('period','like', $month.'%')
						->where('status','active')
						->whereNotNull('consumption')
						->whereHas('account', function($query){
							//~ $query->where('zone_id',$zone_id);
						})
						->select(DB::raw('
								SUM(billing_total) as  ttl_curr_bill,  
								SUM(consumption)  ttl_consum,
								SUM(arrears)  ttl_arrear,
								SUM(discount) ttl_discount,
								SUM(penalty) ttl_penalty,
								COUNT(id) ttl_cons
						'))
						->get();
		
		//Fix the accuracy of the Penalty
		//START HERE 
		$bill_due = BillingDue::where('period','like', $month.'%')
						->where('due_stat','active')
						->whereHas('account')
							->selectRaw('SUM(due1) as PP1')
								->first();
												
		$billing1[0]->ttl_penalty = $bill_due->PP1;
		//END HERE
		
		//~ if($bill_due->PP1){
			//~ echo '<pre>';
			//~ print_r($billing1->toArray());
			//~ die();
		//~ }
		
		
		return $billing1;		
	}/***/
	
	function BillingSummaryAnnualPDF($full_date)
	{

		$curr_period = date('Y-m-28', strtotime($full_date));
		$current_date_label = date('F d, Y', strtotime($full_date));
		$mm = date('Y', strtotime($full_date));
		
		pdf_heading1('Annual Billing Summary ', 'For the year '.$mm);
		Fpdf::SetFont('Courier',"", 7);

		$this->BillingSummaryZoneClassPDF___part_heading('Month');
		Fpdf::Ln();		

		$m_start = 1;
		$m_end = 2;
		
		//~ $class_info_raw = $this->BillingSummaryAnnualPDF___get_by_month('2018-10');
		//~ echo '<pre>';
		//~ print_r($class_info_raw->toArray());
		//~ die();
		
		$ttl_usage = 0;
		$ttl_water_sales = 0;
		$ttl_penalty = 0;
		$ttl_arrear = 0;
		$ttl_discount = 0;		
		$ttl_cons = 0;		
		
		for($x=1;$x<=12;$x++)
		{
			$usage = 0;
			$water_sales = 0;
			$penalty = 0;
			$arrear = 0;
			$discount = 0;
			$cons = 0;			
			
			$month = $mm.'-'.$x;
			$month_by_month = date('Y-m', strtotime($month));
			$class_info_raw = $this->BillingSummaryAnnualPDF___get_by_month($month_by_month);
	
			$class_info = $class_info_raw[0];

			//~ echo '<pre>';
			//~ echo $month;
			//~ print_r($class_info->toArray());
			//~ die();
			
			if(!empty($class_info->ttl_curr_bill))
			{
				$water_sales = $class_info->ttl_curr_bill;
			}

			if(!empty($class_info->ttl_consum))
			{
				$usage = $class_info->ttl_consum;
			}
			
			if(!empty($class_info->ttl_arrear))
			{
				$arrear = $class_info->ttl_arrear;
			}
			
			if(!empty($class_info->ttl_discount))
			{
				$discount = $class_info->ttl_discount;
			}
			
			if(!empty($class_info->ttl_penalty))
			{
				$penalty = $class_info->ttl_penalty;
			}

			if(!empty($class_info->ttl_cons))
			{
				$cons = $class_info->ttl_cons;
			}
			
			
			Fpdf::Cell(35,6, date('F', strtotime($month)), '',0,'L', false);	
			Fpdf::Cell(20,6, number_format(@$cons,0), '0',0,'C', false);	
			Fpdf::Cell(20,6, number_format(@$usage,0), '0',0,'C', false);	
			Fpdf::Cell(30,6, number_format(@$water_sales,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$penalty,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$discount,2), '0',0,'R', false);	
			Fpdf::Cell(30,6, number_format(@$arrear,2), '0',0,'R', false);	
			Fpdf::Ln();		
			
			$ttl_usage += $usage;
			$ttl_water_sales += $water_sales;
			$ttl_penalty += $penalty;
			$ttl_arrear += $arrear;
			$ttl_discount += $discount;
			$ttl_cons += $cons;			
			
		}
		
		Fpdf::Cell(195,2, '', 'B',0,'L', false);	
		Fpdf::Ln();			

		Fpdf::SetFont('Courier',"B", 7);
		Fpdf::Cell(35,6, 'Total', '',0,'L', false);	
		
		Fpdf::Cell(20,6, number_format(@$ttl_cons,0), '0',0,'C', false);	
		Fpdf::Cell(20,6, number_format(@$ttl_usage,0), '0',0,'C', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_water_sales,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_penalty,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_discount,2), '0',0,'R', false);	
		Fpdf::Cell(30,6, number_format(@$ttl_arrear,2), '0',0,'R', false);	
		
		Fpdf::Ln();				
		Fpdf::Ln();				
		Fpdf::SetFont('Courier',"", 7);		
		

		Fpdf::Ln();		
		Fpdf::Ln();		
		pdf_footer_signature();
		Fpdf::Ln();		
		Fpdf::Ln();			
					
		//~ Fpdf::Cell(35,6, $vv, '',0,'L', false);	
		//~ Fpdf::Cell(20,6, number_format(@$cons,0), '0',0,'C', false);	
		//~ Fpdf::Cell(20,6, number_format(@$usage,0), '0',0,'C', false);	
		//~ Fpdf::Cell(30,6, number_format(@$water_sales,0), '0',0,'R', false);	
		//~ Fpdf::Cell(30,6, number_format(@$penalty,0), '0',0,'R', false);	
		//~ Fpdf::Cell(30,6, number_format(@$discount,0), '0',0,'R', false);	
		//~ Fpdf::Cell(30,6, number_format(@$arrear,0), '0',0,'R', false);	
		//~ Fpdf::Ln();		
		
		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;

	}//
	
	

	function ReportGetDelinquentSummary1($zone, $full_date, $gg='')
	{
		$curr_period = date('Y-m-28', strtotime($full_date));
		$current_date_label = date('F d, Y', strtotime($full_date));

		$result1 = Reports::where('rtype', 'ageing_of_accounts')
			->where('period', $curr_period)
			->where('accounts.zone_id', $zone)
			->whereNotNull('billing_total')
			->whereNull('collected')
			->orWhere(function($query){
				$query->whereRaw('billing_total != collected');
			})
			->leftJoin('accounts', 'accounts.id', '=', 'reports.user_id')
			->groupBy('accounts.zone_id')
			->addSelect(DB::raw('reports.*,
				SUM(billing_total) as total_balance,
				COUNT(reports.id) as acct_with_balance,
				accounts.zone_id as zone_orig,
				accounts.meter_number1
			'))
			->first();
			//->get();

		$zones1  = Zones::where('status', '!=', 'deleted')->get();
		$zone_arr = array();
		foreach($zones1 as $zz)
		{
			$zone_arr[$zz->id] = $zz->zone_name;
		}

		if($gg == 'pdf'){
			return compact('result1', 'zone_arr', 'current_date_label', 'pdf_link');
		}

		// echo $zone_arr[$result1->zone_orig];
		// echo '<pre>';
		// print_r($result1->toArray());
		// die();
		$pdf_link = '/billing/report_get_delinquent_summary_pdf/'.$zone.'/'.$full_date;

		return view('billings.inc.billing_reports.delinquents_summary_ajax1',
		compact('result1', 'zone_arr', 'current_date_label', 'pdf_link'));

	}

	function ReportGetDelinquentSummaryPDF1($zone, $full_date)
	{
		
		$curr_period = date('Y-m-28', strtotime($full_date));
		$current_date_label = date('F d, Y', strtotime($full_date));
		$mm = date('F Y', strtotime($full_date));
		
		$acct_class = AccountMetas::where('meta_type', 'account_type')
								->orderBy('meta_name')
								->get();

		$zones = Zones::where('status', '!=', 'deleted')->orderBy('zone_name', 'asc')
								->get();
								
								
		$clas_arr = array();
		foreach($acct_class as $acc)
		{
			$clas_arr[$acc->id] = $acc->meta_name;
		}
		
		$zone_arr = array();
		foreach($zones as $zz)
		{
			$zone_arr[$zz->id] = $zz->zone_name;
		}		

		pdf_heading1('Summary of Delinquents', 'For '.$mm);
		Fpdf::SetFont('Courier',"", 7);
		Fpdf::SetFont('Courier',"B", 7);


		Fpdf::SetLeftMargin(40);

		Fpdf::Ln();
		Fpdf::Cell(30,5,'Zone','B',0,'L', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(40,5, 'Concessionaires','B',0,'R', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(40,5,'Arrears','B',0,'R', false);
		Fpdf::Ln();
		Fpdf::SetFont('Courier',"", 7);

		//~ echo '<pre>';		
		$ttl_cons = 0;
		$ttl_arrear = 0;
		foreach($zone_arr as $kk => $vv)
		{
				$rs1_raw = $this->ReportGetDelinquentSummaryPDF1_____get_result1($kk, $full_date);			
				$rs1 = $rs1_raw[0];
				//~ print_r($rs1->toArray());
				Fpdf::Cell(30,5,$vv,0,0,'L',false);
				Fpdf::Cell(2,5,'',0,0,'L', false);
				Fpdf::Cell(40,5, number_format($rs1->ttl_cons),0,0,'R', false);
				Fpdf::Cell(2,5,'',0,0,'L', false);
				Fpdf::Cell(40,5,number_format($rs1->ttl_arrear, 2),0,0,'R', false);
				Fpdf::Ln();
				$ttl_cons += $rs1->ttl_cons;
				$ttl_arrear += $rs1->ttl_arrear;				
		}
		
		Fpdf::SetFont('Courier',"B", 7);
		Fpdf::Cell(30,5,'Grand Total','T',0,'L', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(40,5, $ttl_cons,'T',0,'R', false);
		Fpdf::Cell(2,5,'',0,0,'L', false);
		Fpdf::Cell(40,5,number_format($ttl_arrear, 2),'T',0,'R', false);
				
		Fpdf::SetLeftMargin(10);

		Fpdf::Ln();		
		Fpdf::Ln();		
		Fpdf::Ln();		
		Fpdf::Ln();		
		pdf_footer_signature();
		Fpdf::Ln();		
		Fpdf::Ln();							
				
		//~ die();		
		Fpdf::AliasNbPages();
		Fpdf::Output();
		exit;
		
		//~ $dd = $this->ReportGetDelinquentSummary1($zone, $full_date, 'pdf');
		//~ extract($dd);
		
		//~ if(!$result1){
			 //~ echo 'No Billing as of '.$current_date_label;
		 	//~ return;
		//~ }

		//~ Fpdf::AddPage('P', 'Letter');
	     //~ Fpdf::SetMargins(6, 6, 6);
	     //~ Fpdf::SetFont('Courier',"B", 8);
		//~ Fpdf::Ln();
	     //~ Fpdf::Cell(75,4,'Summary of Delinquents',0,1,'L', false);
	     //~ Fpdf::Cell(75,4,'As of '.$current_date_label,0,1,'L', false);
		//~ Fpdf::Ln();
		//~ Fpdf::Ln();
		//~ Fpdf::Cell(30,5,'Zone','B',0,'L', false);
		//~ Fpdf::Cell(2,5,'',0,0,'L', false);
		//~ Fpdf::Cell(40,5, 'Concessionaires','B',0,'R', false);
		//~ Fpdf::Cell(2,5,'',0,0,'L', false);
		//~ Fpdf::Cell(40,5,'Arrears','B',0,'R', false);
		//~ Fpdf::Ln();

		//~ Fpdf::Cell(30,5,$zone_arr[$result1->zone_orig],0,0,'L', false);
		//~ Fpdf::Cell(2,5,'',0,0,'L', false);
		//~ Fpdf::Cell(40,5, $result1->acct_with_balance,0,0,'R', false);
		//~ Fpdf::Cell(2,5,'',0,0,'L', false);
		//~ Fpdf::Cell(40,5,number_format($result1->total_balance, 2),0,0,'R', false);
		//~ Fpdf::Ln();
		//~ Fpdf::Ln();
		//~ Fpdf::Cell(30,5,'Grand Total','B',0,'L', false);
		//~ Fpdf::Cell(2,5,'',0,0,'L', false);
		//~ Fpdf::Cell(40,5, $result1->acct_with_balance,'B',0,'R', false);
		//~ Fpdf::Cell(2,5,'',0,0,'L', false);
		//~ Fpdf::Cell(40,5,number_format($result1->total_balance, 2),'B',0,'R', false);

		//~ Fpdf::Output();
		//~ exit;
	}
		
	
	function ReportGetDelinquentSummaryPDF1_____get_result1($zone_id, $curr_period)
	{
		$billing1  = BillingMdl::where('period', $curr_period)
						->where('status','active')
						->whereNotNull('consumption')
						->whereHas('account', function($query)use($zone_id){
							$query->where('zone_id',$zone_id);
						})
						->where(function($query){
							$query->where('arrears', '!=', NULL);
							$query->where('arrears', '!=', 0);
						})
						->select(DB::raw('
								SUM(billing_total) as  ttl_curr_bill,  
								SUM(consumption)  ttl_consum,
								SUM(arrears)  ttl_arrear,
								SUM(discount) ttl_discount,
								SUM(penalty) ttl_penalty,
								COUNT(id) ttl_cons
						'))
						->get();				
				//~ echo '<pre>';
				//~ print_r($billing1->toArray());
				//~ die();		
		return $billing1;		
	}
	
	
	
	
	
	
	
	
	
	
}//











