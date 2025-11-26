<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HwdLedger;
use App\BillingMdl;
use App\Accounts;
use App\AccountMetas;


class HwdLedgerCtrl extends Controller
{
     static function top20_ledger($user_id)
     {
          $ledger_list = HwdLedger::where('led_key1', $user_id)
                         //->orderBy('id', 'asc')
                         ->orderBy('id', 'asc')
                         //~ ->limit(20)
                         ->get();
          //~ echo '<pre>';
          //~ print_r($ledger_list->toArray());
          //~ die();
          return $ledger_list;
     }
     

     static function newAcctApply($user_id)
     {
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'New application request';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'account1';
          $new_led->save();
     }

     static function newAcctApplyRequest(){}

     static function newAcctAcknowledgement($user_id, $type1='approved')
     {
          if($type1 == 'approved' || $type1 == 'dennied'){}
          else{return 0;}

          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Application '.$type1;
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'account1';
          $new_led->save();
     }

     static function appPaymentRequest($user_id){
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Application payment ready';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'account1';
          $new_led->save();
     }

     static function appPaymentAcknowledgement($user_id, $status='payment_made'){
          //
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Application payment made';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'account1';
          $new_led->save();
     }

     static function connectServiceRequest($user_id){
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Meter connection requested';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'service1';
          $new_led->save();
     }

     static function connectServiceAcknowledgement($user_id){
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Meter for installation and approved';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'service1';
          $new_led->save();
     }

     static function connectServiceProcess($user_id){
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Meter installed ';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'service1';
          $new_led->save();
     }

     static function initialReading($user_id, $info=null){
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Initial reading made.';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');

          $info001 = '
               <ul  class="item_list1">
               <li>Meter Number : <span>'.$info['mtr'].'</span></li>
               <li>Initial reading : <span>'.$info['init'].'</span></li>
               </ul>
          ';
          $new_led->led_desc1 = $info001;
          $new_led->ctyp1 = 'reading1';

          $new_led->save();
     }

     static function accountActivationRequest($user_id){
          return;
          return;
          return;
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Account activation request';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->save();
          HwdLedgerCtrl::accountActivationAcknowledgement($user_id);
     }
     
     static function  checkNewUpdateFromNew($acct)
     {
		 $date = date('Y-m');
		 if($date != '2018-10'){return;}
		 
		if((int) $acct->acct_discount != 0)
		{
			$acctMet = AccountMetas::where('id', $acct->acct_discount)->first();
			
			if($acctMet)
			{
				//Senior
				if( (int)$acctMet->meta_value   ==  5){
				}
				
				//Government
				if( (int)$acctMet->meta_value   ==  2){
				}
				
			}
		}
		 
		 $current_bill = BillingMdl::where('period', 'like', $date.'%')
					->where('account_id', $acct->id)
					->first();
		
		
		
		
	 }//
     

     static function accountActivationAcknowledgement($user_id){
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Account activation completed';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'reading1';
          $new_led->save();
     }

     static function ReadingRequest($user_id)
     {
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Reading Period Request';
          $new_led->status1 = 'requested';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->ctyp1 = 'reading1';
          $new_led->save();
     }

     static function ReadingProcess($user_id, $info=null){
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = @$info[5].'Reading for '.@$info[0].'';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');

          // <li>Meter Number : <span>'.$info['mtr'].'</span></li>
          // <li>Initial reading : <span>'.$info['init'].'</span></li>

          $info001 = '
               <ul  class="item_list1">
               <li>Period : <span>'.@$info[0].'</span></li>
               <li>Meter No. :  <span>'.@$info[1].'</span></li>
               <li>Previous :  <span>'.@$info[2].'</span></li>
               <li>Current :  <span>'.@$info[3].'</span></li>
               <li>Consumption :  <span>'.@$info[4].'</span></li>
               </ul>
          ';
          $new_led->led_desc1 = $info001;
          $new_led->ctyp1 = 'reading1';

          $new_led->save();
          //HwdLedgerCtrl::ReadingAcknowledgement($user_id);
     }

     static function MeterNumberChange($user_id, $info=null)
     {
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Meter number '.$info[1];
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $info001 = '
               <ul  class="item_list1">
                    <li>Meter Number : <span>'.@$info[0].'</span></li>
               </ul>
          ';
          $new_led->led_desc1 = $info001;
          $new_led->ctyp1 = 'reading1';
          $new_led->save();
     }

     static function AccountNumberChange($user_id, $info=null)
     {
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Account Number '.$info[1];
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $info001 = '
               <ul  class="item_list1">
                    <li>Account Number : <span>'.@$info[0].'</span></li>
               </ul>
          ';
          $new_led->led_desc1 = $info001;
          $new_led->ctyp1 = 'reading1';
          $new_led->save();
     }


     static function ReadingAcknowledgement($user_id)
     {
          return;
          return;
          return;
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Reading Completed';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->save();
     }

     static function BillingRequest($user_id, $status='requested'){
          return;
          return;
          return;
          $new_led = new HwdLedger;
          $new_led->led_type = 'active';
          $new_led->led_title = 'Billing : '.$status;
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');
          $new_led->save();
     }

     static function BillingApproved($user_id)
     {
          //HwdLedgerCtrl::BillingRequest($user_id, 'approved');
     }

     static function BillingAcknowledgement($user_id, $info=array())
     {
          //HwdLedgerCtrl::BillingRequest($user_id, 'completed');

          $readable_period = date('F Y', strtotime(@$info[0]));

          //ReBilling
          $reBilling = '';
          if(!$info[9]){
               $reBilling = 'Re-Billing due to reading correction <br />';
          }

          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = $reBilling.'Billing for '.@$readable_period.'';
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');

          // <li>Meter Number : <span>'.$info['mtr'].'</span></li>
          // <li>Initial reading : <span>'.$info['init'].'</span></li>
          // <li>Prev. Reading :  <span>'.@$info[1].'</span></li>
          // <li>Current. Reading :  <span>'.@$info[2].'</span></li>

          $info001 = '
               <ul  class="item_list1 ledlist">
               <li>Period : <span>'.@$readable_period.'</span></li>
               <li>Type :  <span>'.@$info[4].'</span></li>
               <li>Consumption :  <span>'.@$info[3].'</span></li>
               <li>Current Bill :  <span>'.@number_format($info[8], 2).'</span></li>
               <li>Discount Type :  <span>'.@$info[6]['meta_name'].'('.$info[6]['meta_value'].'%)'.'</span></li>
               <li>Discount Value :  <span>'.@number_format($info[7],2).'</span></li>
               <li>Penalty :  <span>0</span></li>
               <li>Current Total Bill :  <span>'.@number_format($info[5], 2).'</span></li>
               <li>Arears :  <span>'.@number_format($info[10], 2).'</span></li>
               </ul>
          ';

          $new_led->led_desc1 = $info001;
          $new_led->ctyp1 = 'billing1';

          $new_led->save();

     }


     static function CollectionProcess($user_id, $info=array()){

          $readable_period = date('F Y', strtotime(@$info['period']));

          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Collection made with sum of Php. '.number_format($info['amount'],2);
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          
          $new_led->led_date2 = date('Y-m-d H:i:s');
          if($info['led_date'] == true){
			  $new_led->led_date2 = @$info['led_date'];
		  }

          $info001 = '
               <ul  class="item_list1 ledlist">
               <li>Invoice number : <span>'.@$info['invoice'].'</span></li>
               <li>Payed amount : <span>'.@number_format($info['amount'],2).'</span></li>
          ';
         
         if($info['is_bank'] == true){
			$info001.='
               <li>Bank ID : <span>'.@$info['bank_id'].'</span></li>
               <li>Check No. : <span>'.@$info['check_no'].'</span></li>
               <li>Bank ID : <span>'.@$info['branch'].'</span></li>
			';
		 }

		$info001.='</ul>';
          
/*
	   <li>Billing number : <span>'.@$info['billing_ids'].'</span></li>
	   <li>Period : <span>'.@$readable_period.'</span></li>
	   <li>Current bill : <span>'.@number_format($info['current_bill'],2).'</span></li>
	   <li>Arrears : <span>'.@number_format($info['arrears'],2).'</span></li>
	   <li class="no_border">&nbsp;</li>
	   <li>Total Billing : <span>'.@number_format($info['total_billing'],2).'</span></li>
	   <li>Total Collected : <span>'.@number_format($info['colllected'],2).'</span></li>
	   <li>Total Balance : <span>'.@number_format($info['remaining_balance'],2).'</span></li>
	   <li class="no_border">&nbsp;</li>
	   <li>Remaining balance : <span>'.@number_format($info['total_remaining_balance'],2).'</span></li>
*/
          $new_led->led_desc1 = $info001;
          $new_led->ctyp1 = 'collection1';
          $new_led->c_typ2 = 'firstload';

          $new_led->save();
     }

     static function CollectionAcknowledge(){}

     static function DueProcess($user_id, $info=array())
    {
          $readable_period = date('F Y', strtotime(@$info['period']));

          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Penalty for '.$readable_period;
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');		
          $info001 = '
               <ul  class="item_list1 ledlist">
               <li>Billing number : <span>'.@$info['billing_id'].'</span></li>
               <li>Period : <span>'.@$readable_period.'</span></li>
               <li>Current bill : <span>'.@number_format($info['billed'],2).'</span></li>
               <li>Total collected : <span>'.@number_format($info['collected'],2).'</span></li>
               <li class="no_border">&nbsp;</li>
               <li>Total balance : <span>'.@number_format($info['balance'],2).'</span></li>
               <li>Total penalty : <span>'.@number_format($info['penalty'],2).'</span></li>
               <li class="no_border">&nbsp;</li>
               </ul>
          ';          
          $new_led->led_desc1 = $info001;
          $new_led->ctyp1 = 'due1';
          
          $new_led->save();          
	}//

     static function DisconnectNoticeProcess($user_id, $info=array())
     {
          $readable_period = date('F Y', strtotime(@$info['period']));
          
          $new_led = new HwdLedger;
          $new_led->led_type = 'account';
          $new_led->led_title = 'Notice of Disconnection '.$readable_period;
          $new_led->status1 = 'active';
          $new_led->led_key1 = $user_id;
          $new_led->led_date2 = date('Y-m-d H:i:s');		
          
          $info001 = '
               <ul  class="item_list1 ledlist">
               <li>Billing number : <span>'.@$info['bill_id'].'</span></li>
               <li>Billing Date : <span>'.@$info['bill_date'].'</span></li>
               <li>Period : <span>'.@$readable_period.'</span></li>
               <li>Current bill : <span>'.@number_format($info['billing_total'],2).'</span></li>
               <li>Penalty : <span>'.@number_format($info['penalty'],2).'</span></li>
               <li>Penalty Date : <span>'.@$info['penalty_date'].'</span></li>
               <li>Arrear : <span>'.@number_format($info['arrears'],2).'</span></li>
               <li class="no_border">&nbsp;</li>
               <li>Total balance : <span>'.@number_format($info['total_bill'], 2).'</span></li>
               <li class="no_border">&nbsp;</li>
               </ul>
          ';
          
          $new_led->led_desc1 = $info001;
          $new_led->ctyp1 = 'billing1';
          
          $new_led->save();               
                    
	 }//
	 
     static function DisconnectAccountAddToLedger($user_id, $info=array())
     {
	 }
	 
	 
     static function ReconnectAccountAddToLedger($user_id, $info=array())
     {
	 }	 
	 
		
     static function DisconnectProcess(){}
     static function DisconnectAcknowledgement(){}

     static function ReconnRequest(){}
     static function ReconnApproval(){}
     static function ReconnAcknowledgement(){}
     
     
     

}
