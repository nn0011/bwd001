
jQuery(document).ready(function(){

     setTimeout(function(){
          var hash = window.location.hash;
          jQuery("a[href='"+hash+"']").trigger('click');
     }, 100);



	//jQuery('.date_birth').datepicker({ dateFormat: 'yy-mm-dd' });
      jQuery('.date_birth').datepicker({
        autoHide: true,
        zIndex: 2048,
        format: 'yyyy-mm-dd'
      });
      
      
		jQuery('.new_acct_form #new_acc_4, .new_acct_form #new_acc_9').datepicker({
			autoHide: true,
			zIndex: 999999,
			format: 'yyyy-mm-dd'
		});      



});


function new_account_submit($t){
	if(!jQuery('.pop101 #in1').val()){return false;}
	if(!jQuery('.pop101 #in2').val()){return false;}
	return true;
}

function update_account_submit($t){
	if(!jQuery('.pop101 #in1').val()){return false;}
	if(!jQuery('.pop101 #in2').val()){return false;}
	return true;
}

function update_zone_submit($t){
	if(!jQuery('.pop101 #in1').val()){return false;}
	if(!jQuery('.pop101 #in2').val()){return false;}
	return true;
}



function new_account_main()
{
	if(!jQuery('.new_acct_form #new_acc_1').val()){Warning01('Last name is required');return false;}
	//~ if(!jQuery('.new_acct_form #new_acc_2').val()){Warning01('First name is required');return false;}
	//~ if(!jQuery('.new_acct_form #new_acc_3').val()){Warning01('Middle initial is required');return false;}
	//~ if(!jQuery('.new_acct_form #new_acc_4').val()){Warning01('Date of birth is required');return false;}
	//~ if(!jQuery('.new_acct_form #new_acc_5').val()){Warning01('Phone is required');return false;}
	//~ if(!jQuery('.new_acct_form #new_acc_6').val()){Warning01('Address is required');return false;}
	//if(!jQuery('.new_acct_form #new_acc_7').val()){return false;}
	//if(!jQuery('.new_acct_form #new_acc_8').val()){return false;}
	//~ if(!jQuery('.new_acct_form #new_acc_9').val()){Warning01('Date of residence is required');return false;}

	//if(!jQuery('.new_acct_form #new_acc_10').val()){return false;}
	if(!jQuery('.new_acct_form #new_acc_10').val()){Warning01('Zone is required');return false;}
	if(!jQuery('.new_acct_form #new_acc_11').val()){Warning01('Account Type is required');return false;}
	//if(!jQuery('.new_acct_form #new_acc_12').val()){return false;}
	jQuery('.wait_loading').show();

	return true;
}


function Warning01($msg)
{
	alert($msg);
}



/**** Account LIST *************/
/**** Account LIST *************/
/**** Account LIST *************/


function view_hwd_request_new_acct($i){
     trig1_v2('view_info_acct_info_only');
     view_account_info_main_v2(hwd_request_new_acct[$i].account);

}

function view_ledger101()
{
     trig1_v2('view_acct_ledger101_cont');
     jQuery('.pop101').addClass('width_800');
     
     
     setTimeout(function(){
			
			let vv = current_req_account;
			
			jQuery.get("/billing/account_ledger/view_ledger_account_info?acct_id="+vv.id, function(data){
				jQuery('.pop101 .res11001').html(data);
			});				
				
			},200);

	//~ console.log(current_req_account);
}

function view_acct_ledger(curr_index)
{
	//~ console.log(curr_index);
	view_ledger101();
}


function add_bill_adjustment_save_V2()
{
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	let $my_csrf = jQuery('.my_csrf').val();
	
	let amt = jQuery('.pop101 .bill_adjustment_amount').val();
	let des_note = jQuery('.pop101 .bill_ajustment_note').val();

	let adj_type = jQuery('.pop101 .type_adjust').val();
	let adj_reff = jQuery('.pop101 .bill_to_adjust').val();
	
	let acct_id = current_req_account.id;
	let acct_no = current_req_account.acct_no;
	
	let int_amt = parseFloat(amt);
	
	if(!int_amt){alert('Please add amount');return;}
	//~ if(int_amt <= 0){alert('Please add amount');return;}
	
	
	let url111 = '/billing/account_ledger/add_bill_ajustment_v2/?my_csrf='+$my_csrf+'&acct_id='+acct_id+'&acct_no='+acct_no+'&amt='+amt+'&desc='+des_note+'&adj_type='+adj_type+'&adj_reff='+adj_reff;

	jQuery('.cmd_buts').hide();
	jQuery('.please_wait').show();


	jQuery.get(url111, function(data){
		view_acct_ledger(curr_index);
	});	

}


function ledger_1($m, $tt)
{
	jQuery('.tabview').hide();
	jQuery('.'+$m).show();
	
	jQuery('.tabview_cmd li').removeClass('active');
	jQuery('.tabview_cmd li.'+$tt).addClass('active');
	
	//active
}

var curr_index = null;

function view_account_info_main($ind)
{
	 curr_account_index = $ind;
	 curr_index  = $ind;
     current_req_account = accounts_data[$ind];

	setTimeout(function(){
		jQuery('.view_acct_info_pop .field1').html(accounts_data[$ind].lname+', '+accounts_data[$ind].fname+' '+accounts_data[$ind].mi);
		jQuery('.view_acct_info_pop .field2').html(accounts_data[$ind].address1);
		jQuery('.view_acct_info_pop .field3').html(accounts_data[$ind].acct_no);
		jQuery('.view_acct_info_pop .field4').html(accounts_data[$ind].meter_number1);
		jQuery('.view_acct_info_pop .field5').html(accounts_data[$ind].acct_created);
		jQuery('.view_acct_info_pop .field6').html(accounts_data[$ind].zone_lab);
		jQuery('.view_acct_info_pop .field7').html(accounts_data[$ind].acct_type_lab);
		jQuery('.view_acct_info_pop .field8').html(accounts_data[$ind].acct_stat_lab);
		jQuery('.view_acct_info_pop .field9').html(accounts_data[$ind].bill_dis_lab);
		jQuery('.view_acct_info_pop .field10').html(accounts_data[$ind].bill_count);
	},100);
}


var current_req_account = {};

function view_info_acct_goto_reading()
{
     //~ window.location = '/billing/reading/'+curr_period_yy+'/'+curr_period_mm+'/filter/'+current_req_account.acct_no+'/none/none/none/#accounts';
	var $ind = curr_account_index;

	trig1_v2('quick_preview_reading');

	setTimeout(function(){

			jQuery('.pop101 .quick_preview_reading_pop .field1').html(accounts_data[$ind].lname+', '+accounts_data[$ind].fname+' '+accounts_data[$ind].mi);
			jQuery('.pop101 .quick_preview_reading_pop .field2').html(accounts_data[$ind].acct_no);
			jQuery('.pop101 .quick_preview_reading_pop .field3').html(accounts_data[$ind].meter_number1);
			jQuery('.pop101 .quick_preview_reading_pop .field4').html(accounts_data[$ind].zone_lab);

			jQuery.get('/ajax1/get_top3_reading/'+accounts_data[$ind].id, {}, function(response) {
				jQuery('.pop101  .quick_preview_reading_pop .res_cont01').html(response);
			});


	}, 100);

}
function view_info_acct_goto_current_reading(){
	//~ var $ind = curr_account_index;
     window.location = '/billing/reading/'+curr_period_yy+'/'+curr_period_mm+'/filter/'+current_req_account.acct_no+'/none/none/none/#accounts';
}


function view_info_acct_goto_billing()
{
     //~ window.location = '/billing/billing/'+curr_period_yy+'/'+curr_period_mm+'/filter/'+current_req_account.acct_no+'/none/none/none/#accounts';
	var $ind = curr_account_index;
	trig1_v2('quick_preview_billing');

	setTimeout(function(){

			jQuery('.pop101 .quick_preview_billing_pop .field1').html(accounts_data[$ind].lname+', '+accounts_data[$ind].fname+' '+accounts_data[$ind].mi);
			jQuery('.pop101 .quick_preview_billing_pop .field2').html(accounts_data[$ind].acct_no);
			jQuery('.pop101 .quick_preview_billing_pop .field3').html(accounts_data[$ind].meter_number1);
			jQuery('.pop101 .quick_preview_billing_pop .field4').html(accounts_data[$ind].zone_lab);

			jQuery.get('/ajax1/get_top3_billing/'+accounts_data[$ind].id, {}, function(response) {
				jQuery('.pop101  .quick_preview_billing_pop .res_cont01').html(response);
			});

	}, 100);

}

function view_info_acct_goto_ledger()
{
	var $ind = curr_account_index;
    let url11 = '/billing/account_ledger/filter/'+accounts_data[$ind].acct_no+'/none/none/none/none/#account_list';
    window.open(url11, '_blank');
}//



function view_account_info_main_v2($acct_data){
     //console.log($acct_data);
	setTimeout(function(){
		jQuery('.view_acct_info_pop .field1').html($acct_data.lname+', '+$acct_data.fname+' '+$acct_data.mi);
		jQuery('.view_acct_info_pop .field2').html($acct_data.address1);
		jQuery('.view_acct_info_pop .field3').html($acct_data.acct_no);
		jQuery('.view_acct_info_pop .field4').html($acct_data.meter_number1);
		jQuery('.view_acct_info_pop .field5').html($acct_data.acct_created);
		jQuery('.view_acct_info_pop .field6').html($acct_data.zone_lab);
		jQuery('.view_acct_info_pop .field7').html($acct_data.acct_stat_lab);
		jQuery('.view_acct_info_pop .field8').html($acct_data.acct_type_lab);
		jQuery('.view_acct_info_pop .field9').html($acct_data.bill_dis_lab);
		jQuery('.view_acct_info_pop .field10').html('0');
	},100);
}

/***********/
function view_info_acct_modify_acct()
{

	var $ind = curr_account_index;

	trig1_v2('view_info_acct_modify_acct_pop');

	jQuery('.view_info_acct_modify_acct_pop #new_acc_ID').val(accounts_data[$ind].id);

	jQuery('.view_info_acct_modify_acct_pop #new_acc_1').val(accounts_data[$ind].lname);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_2').val(accounts_data[$ind].fname);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_3').val(accounts_data[$ind].mi);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_4').val(accounts_data[$ind].birth_date);

	jQuery('.view_info_acct_modify_acct_pop #new_acc_5').val(accounts_data[$ind].tel1);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_6').val(accounts_data[$ind].address1);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_7').val(accounts_data[$ind].address2);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_9').val(accounts_data[$ind].install_date);

	jQuery('.view_info_acct_modify_acct_pop #new_acc_10').val(accounts_data[$ind].zone_id);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_11').val(accounts_data[$ind].acct_type_key);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_12').val(accounts_data[$ind].acct_discount);
	jQuery('.view_info_acct_modify_acct_pop #new_acc_13').val(accounts_data[$ind].acct_status_key);
	jQuery('.view_info_acct_modify_acct_pop #new_acct_meter_size').val(accounts_data[$ind].meter_size_id);
	jQuery('.view_info_acct_modify_acct_pop #new_acct_penalty_exempt').val(accounts_data[$ind].pen_exempt);
	jQuery('.view_info_acct_modify_acct_pop #new_acct_employee').val(accounts_data[$ind].is_employee);
	jQuery('.view_info_acct_modify_acct_pop #tin_id').val(accounts_data[$ind].tin_id);
	jQuery('.view_info_acct_modify_acct_pop #other_id').val(accounts_data[$ind].other_id);

	//jQuery('.pop101 .date_birth111').datepicker({ dateFormat: 'yy-mm-dd' });
	
	setTimeout(function(){
		
		jQuery('.pop101 #new_acc_4, .pop101 #new_acc_9').datepicker({
			autoHide: true,
			zIndex: 999999,
			format: 'yyyy-mm-dd'
		});
		
	}, 1000);
	

}


function view_info_acct_add_doc(){
}

function view_info_acct_add_meter_number(){
     //accounts_data[curr_account_index]
     // console.log(accounts_data[curr_account_index]);
     pop_close();
     trig1_v2('add_meter_number');


     setTimeout(function(){
          let vv = accounts_data[curr_account_index];
          
          console.log(vv);
          
          jQuery('.pop101 .field3').html(vv.acct_no);
          jQuery('.pop101 .field6').html(vv.zone_lab);
          jQuery('.pop101 .field6_1').html(vv.meter_number1);
          jQuery('.pop101 #acct_id').val(vv.id);
          jQuery('.pop101 .meter_number_text').val(vv.meter_number1);
          jQuery('.pop101 .acct_number_text').val(vv.acct_no);
          
        //   jQuery('.pop101 .long1').val(vv.last_gps_data.lng1);
        //   jQuery('.pop101 .lat1').val(vv.last_gps_data.lat1);

          jQuery('.pop101 .meter_rem').val(vv.mtr_rem);

	}, 300);
}

var add_meter_number_form = false;
function view_info_acct_add_meter_number_send()
{
     //~ if(!jQuery('.pop101 .meter_number_text').val()){alert('Meter number is required');return false;}
     if(!jQuery('.pop101 .acct_number_text').val()){alert('Account number is required');return false;}
     
     add_meter_number_form = true;
     
     jQuery('.pop101 .add_meter_number_form1').submit();
}

function view_info_acct_add_init_reading()
{
     pop_close();
     trig1_v2('add_initial_reading');
     setTimeout(function(){
          let vv = accounts_data[curr_account_index];
          jQuery('.pop101 #acct_id').val(vv.id);
          jQuery('.pop101 #data1').val(JSON.stringify(vv));

     }, 100);



}

var add_initial_reading_form = false;
function view_info_acct_add_init_reading_save()
{
     let vv = accounts_data[curr_account_index];

     if(!vv.meter_number1)
     {
          alert('Meter number is required before adding initial reading');
          return;
     }

     if(!jQuery('.pop101 .init_reading_txt').val()){alert('Initial reading is required');return false;}
     add_initial_reading_form = true;
     jQuery('.pop101 .add_initial_reading').submit();
}




function view_info_acct_view_billing(){
}
/**********/
function view_info_acct_modify_acct_pop(){
}

function view_info_acct_modify_acct_pop_cancel(){
	trig1_v2('view_info_acct');
	view_account_info_main(curr_account_index);
}

function view_info_acct_modify_acct_pop_update(){
	return true;
}
/**********/


function pre_do_search_now()
{
	$acct = jQuery('#search_acct_num').val();
	$meter = jQuery('#search_meter_num').val();
	$lname = jQuery('#search_last_name').val();
	$fname = jQuery('#search_first_name').val();
	$zone = jQuery('#search_zone').val();
	$acct_stat = jQuery('#search_status11').val();
	$beg_stat = jQuery('#search_beginning').val();

	$url_part1 = '/';

	if(!$acct){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$acct+'/';}

	if(!$meter){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$meter+'/';}

	if(!$lname){
		$url_part1 = $url_part1+'none/';
	}else{
		$url_part1 = $url_part1+$lname+'/';
		//$url_part1 = $url_part1+$lname+'|'+$fname+'';
	}

	if(!$zone){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$zone+'/';}

	$url_part1 = $url_part1+$acct_stat+'/';

	if(!$beg_stat){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$beg_stat+'/';}


	if($url_part1 == '/none/none/none/none/none/none'){return;}

	return $url_part1;
}

function do_search_now()
{
	let $url_part1 = pre_do_search_now();
	// alert($url_part1);
	window.location='/billing/accounts'+$url_part1+'#account_list';
}



function go_change_sort_out()
{
	let search_val = jQuery('#sort_out_search').val();
	let $url_part1 = pre_do_search_now();
	window.location='/billing/accounts'+$url_part1+'?quick_search='+search_val+'#account_list';
}//


/***********************/
function view_acct_type($ind)
{
	setTimeout(function(){
		jQuery('.pop101 #in1').val(acct_type[$ind].meta_name);
		jQuery('.pop101 #in2').val(acct_type[$ind].meta_code);
		jQuery('.pop101 #in3').val(acct_type[$ind].meta_desc);
		jQuery('.pop101 #in4').val(acct_type[$ind].status);
		jQuery('.pop101 #out1').val(acct_type[$ind].id);
	}, 300);
}


function view_acct_status($ind)
{
	setTimeout(function(){
		jQuery('.pop101 #in1').val(acct_status[$ind].meta_name);
		jQuery('.pop101 #in2').val(acct_status[$ind].meta_code);
		jQuery('.pop101 #in3').val(acct_status[$ind].meta_desc);
		jQuery('.pop101 #in4').val(acct_status[$ind].status);
		jQuery('.pop101 #out1').val(acct_status[$ind].id);
	}, 300);
}

function view_zone($ind){
	setTimeout(function(){
		jQuery('.pop101 #in1').val(zones[$ind].zone_name);
		jQuery('.pop101 #in2').val(zones[$ind].zone_code);
		jQuery('.pop101 #in3').val(zones[$ind].zone_desc);
		jQuery('.pop101 #in4').val(zones[$ind].status);
		jQuery('.pop101 #bill_date').val(zones[$ind].bill_nth);
		jQuery('.pop101 #out1').val(zones[$ind].id);
	}, 300);
}


function acct_list_clear_filter_all(){
	window.location = '/billing/accounts/#account_list';
}


function add_beginning_balance(){
		let c1 = accounts_data[curr_account_index];
		trig1_v2('add_beginning_balance');
		setTimeout(function(){
			jQuery('.pop101 .field1').html(c1.fname+' '+c1.lname); // Full Name
			jQuery('.pop101 .field2').html(c1.acct_no); // Full Name
			jQuery('.pop101 .field3').html(c1.meter_number1); // Full Name
			jQuery('.pop101 .field4').html(c1.zone_lab); // Full Name
			jQuery('.pop101 .field5').html(c1.address1); // Full Name
			jQuery('.pop101 .field6').html(); // Full Name
		}, 100);
}

function add_beginning_balance_submit()
{
		let c1 = accounts_data[curr_account_index];
		let amt =   jQuery('.pop101 #beginning_bal_amt').val();
		let remarks =   jQuery('.pop101 #beginning_bal_remarks').val();

		window.location='/billing/accounts/add_beginning_bal_amt/'+amt+'/'+c1.id+'/'+c1.acct_no+'?remaks='+remarks;
}


function delete_accounts()
{
	let conf1 = confirm('Are you sure to delete this account?');
	if(!conf1){return;}

	let c1 = accounts_data[curr_account_index];
	window.location='/billing/accounts/delete_account/'+c1.id+'/'+c1.acct_no;

}


function edit_add_beginning_bal_V1()
{
	 let vv = current_req_account;


	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	
	let amt = jQuery('.pop101 .beg_bal1_amt').val();
	let prd = jQuery('.pop101 .beg_bal1_prd').val();
	let iid = vv.id;

	let URL11  = '/billing/account_ledger/get_ledger_acct/update_beginning_v2?acct_id='+iid+'&amt='+amt+'&prd='+prd;
	
	
	jQuery('.cmd_buts').hide();
	jQuery('.please_wait').show();
	
	jQuery.get(URL11, function(data){
		view_acct_ledger(11);
	});	
	
	 
	
}
