<?php

		
		$full_name = $acct1->fname.' '.$acct1->mi.' '.$acct1->lname;
		
			
		 Fpdf::AddPage('L', 'Letter');
		 Fpdf::SetMargins(3, 3, 5);

		 //Fpdf::SetMargins(5, 5, 5);
		 Fpdf::SetFont('Courier',"B", 10);
		 //Fpdf::Cell(50, 25, 'Hello World!');

		 Fpdf::Cell(75,5,'',0,1,'L', false);
		 Fpdf::Cell(75,5,WD_NAME,0,1,'L', false);
		 Fpdf::Cell(75,5,WD_ADDRESS,0,1,'L', false);
		 Fpdf::Cell(75,5,'',0,1,'L', false);

		 Fpdf::Cell(150,5,'Ledger Account of '.$full_name,0,1,'L', false);
		 Fpdf::Cell(150,5,'Acct # '.$acct1->acct_no,0,1,'L', false);
		 Fpdf::Cell(75,5,'As of '.date('F d, Y'),0,1,'L', false);
		 Fpdf::Cell(75,5,'',0,1,'L', false);
		 //~ Fpdf::Cell(75,5,'',0,1,'L', false);
		 //~ Fpdf::Cell(75,5,'Zone 1',0,1,'L', false);
		 //~ Fpdf::Cell(75,5,'',0,1,'L', false);

		 $cel_sp = 2;
		 $cel_wd = 30;
		 
		 Fpdf::SetFont('Courier',null, 8);

		Head00111($cel_wd, $cel_sp);

		 //~ Fpdf::AddPage();
		 //~ Fpdf::Cell(20,10,'Title',1,1,'C');
		 
		 $line_full = 27;
		 $next_line_full = 36;
		 $line12  = 1;
		 
		 //$ll = $led001[0];
		 
		 //~ for($xx=1;$xx<=100;$xx++):
		 foreach($led001  as $ll):
			 Fpdf::Cell($cel_wd,5, $ll->date01,'B',0,'L', false);
			 Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
			 Fpdf::Cell($cel_wd,5, $ll->led_type ,'B',0,'L', false);
			 Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
					
			 if(empty(trim($ll->ledger_info))){!$ll->ledger_info = '---';}
			 Fpdf::Cell($cel_wd,5, $ll->ledger_info  ,'B',0,'L', false);
			 Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
			 
			 if(empty(trim($ll->period))){!$ll->period = '---';}
			 Fpdf::Cell($cel_wd,5,  @$ll->period,'B',0,'L', false);
			 Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
			 Fpdf::Cell($cel_wd,5, number_format($ll->arrear, 2),'B',0,'L', false);
			 Fpdf::Cell($cel_sp,5,'',0,0,'L', false);
			 Fpdf::Cell($cel_wd - 5,5, number_format($ll->billing, 2),'B',0,'L', false);
			 Fpdf::Cell($cel_sp ,5,'',0,0,'L', false);
			 Fpdf::Cell($cel_wd - 15,5, number_format($ll->payment, 2),'B',0,'L', false);
			 Fpdf::Cell($cel_sp ,5,'',0,0,'L', false);
			 Fpdf::Cell($cel_wd - 15 ,5, number_format($ll->discount, 2),'B',0,'L', false);
			 Fpdf::Cell($cel_sp ,5,'',0,0,'L', false);
			 Fpdf::Cell($cel_wd -15,5, number_format($ll->penalty, 2),'B',0,'L', false);
			 Fpdf::Cell($cel_sp ,5,'',0,0,'L', false);
			 Fpdf::Cell($cel_wd ,5, number_format($ll->ttl_bal, 2),'B',0,'L', false);		 
			 Fpdf::Cell(75,5,'',0,1,'L', false);
			 
			 if($line12 >= $line_full){
					 Fpdf::AddPage('L', 'Letter');
					 Fpdf::SetMargins(3, 3, 5);
					 Fpdf::SetFont('Courier',null, 8);
					 Head00111($cel_wd, $cel_sp);
					 $line12 = 1;
					 $line_full = $next_line_full;
			 }
			 
			 $line12++;
			 
		endforeach;
		//~ endfor;
		 Fpdf::Cell(75,5,'',0,1,'L', false);
		 Fpdf::Cell(200 ,5,'----END----',0,0,'L', false);

		//~ echo date('Ymd-'.$acct1->acct_no).'.pdf';
		 //~ die();
		 
		 $fname = date('Ymd-'.$acct1->acct_no).'.pdf';
		Fpdf::SetTitle( $fname);

		 
		 Fpdf::Output('I',  $fname);
		 exit;



function Head00111($cel_wd, $cel_sp)
{
		 Fpdf::Cell($cel_wd,5,'DATE','BLRT',0,'L', false);
		 Fpdf::Cell($cel_sp,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd,5,'TYPE','BRT',0,'L', false);
		 Fpdf::Cell($cel_sp,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd,5,'INFO','BRT',0,'L', false);
		 Fpdf::Cell($cel_sp,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd,5,'PERIOD','BRT',0,'L', false);
		 Fpdf::Cell($cel_sp,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd,5,'ARREAR','BRT',0,'L', false);
		 Fpdf::Cell($cel_sp,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd - 5,5,'BILLING','BRT',0,'L', false);
		 Fpdf::Cell($cel_sp ,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd - 15,5,'PAYMENT','BRT',0,'L', false);
		 Fpdf::Cell($cel_sp ,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd - 15 ,5,'DISCOUNT','BRT',0,'L', false);
		 Fpdf::Cell($cel_sp ,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd -15,5,'PENALTY','BRT',0,'L', false);
		 Fpdf::Cell($cel_sp ,5,'','BT',0,'L', false);
		 Fpdf::Cell($cel_wd ,5,'TOTAL BALANCE','BRT',0,'L', false);	
		 
		 Fpdf::SetFont('Courier',null, 8);
		 Fpdf::Cell(75,5,'',0,1,'L', false);
		 
}



exit();
