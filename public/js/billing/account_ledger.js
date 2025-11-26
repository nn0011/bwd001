	//ledger_account

var curr_ledger_acct = null;
var curr_index = null;


function refresh_ledger_101($acct_id)
{
	var is_s = confirm('Are you sure to refresh ledger?');
	if(!is_s){return;}
	
	is_s = confirm('This will alter records in ledger.. are you sure?');
	if(!is_s){return;}
		
	let mm  = Date.now();
	jQuery.get('/billing/account_ledger/refresh_ledger_101/'+$acct_id+'?tt='+mm, function(data){
		view_acct_ledger(curr_index);
	}).promise();
}//

async function edit_ledger_status($led_id)
{
	
	//~ return;
	//~ return;
	//~ return;
	
	let mm  = Date.now();
	let dd = null;
	
	await jQuery.get('/billing/account_ledger/get_led_item/'+$led_id+'?tt='+mm, function(data){
		dd = data;
	}).promise();
	
	if(dd == null){
		alert('Failed to retrieve');
		return;
	}
	
	var is_s = confirm('Are you sure to disable ledger entry reff# '+dd.reff_no);
	if(!is_s){return;}
	
	is_s = confirm('This will alter record and need to refresh ledger. are you still sure yo remove this entry?');
	if(!is_s){return;}	
	
	await jQuery.get('/billing/account_ledger/disable_ledger_item/'+$led_id+'?tt='+mm, function(data){
		view_acct_ledger(curr_index);
	}).promise();
	
	
	//~ alert($led_id);
	//~ console.log(curr_ledger_acct);
	//~ alert('test');
}//

function add_bill_adjustment_save_V2()
{
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	let $my_csrf = jQuery('.my_csrf').val();
	
	let amt = jQuery('.pop101 .bill_adjustment_amount').val();
	let des_note = jQuery('.pop101 .bill_ajustment_note').val();
	let acct_id = curr_ledger_acct.id;
	let acct_no = curr_ledger_acct.acct_no;
	
	let int_amt = parseFloat(amt);
	
	if(!int_amt){alert('Please add amount');return;}
	//~ if(int_amt <= 0){alert('Please add amount');return;}
	
	//~ console.log(curr_ledger_acct);
	
	let url111 = '/billing/account_ledger/add_bill_ajustment_v2/?my_csrf='+$my_csrf+'&acct_id='+acct_id+'&acct_no='+acct_no+'&amt='+amt+'&desc='+des_note;

	jQuery('.cmd_buts').hide();
	jQuery('.please_wait').show();


	jQuery.get(url111, function(data){
		view_acct_ledger(curr_index);
	});	

}



function recalculate_ledger_V2()
{
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	
	
	
	let vv = ledger_account[curr_index];
	
	let url1  = '/billing/account_ledger/get_ledger_acct/recalculate_v2?acct_id='+vv.id;
	
	jQuery('.cmd_buts').hide();
	jQuery('.please_wait').show();
	
	
	jQuery.get(url1, function(data){
		view_acct_ledger(curr_index);
		jQuery('.cmd_buts').show();
		jQuery('.please_wait').hide();
	});	
	
	
	
}//

function edit_add_beginning_bal_V1()
{
	 let vv = ledger_account[curr_index];
	 curr_ledger_acct = vv;
	 //console.log(curr_ledger_acct);

	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	
	let amt = jQuery('.pop101 .beg_bal1_amt').val();
	let prd = jQuery('.pop101 .beg_bal1_prd').val();
	let iid = vv.id;

	let URL11  = '/billing/account_ledger/get_ledger_acct/update_beginning_v2?acct_id='+iid+'&amt='+amt+'&prd='+prd;
	
	
	jQuery('.cmd_buts').hide();
	jQuery('.please_wait').show();
	
	jQuery.get(URL11, function(data){
		view_acct_ledger(curr_index);
	});	
	
	 
	
}


function view_acct_ledger($ind)
{
	 curr_index = $ind;
	
     trig1_v2('view_accout_ledger22_pop');
     jQuery('.pop101').addClass('width_800');
     
	 let vv = ledger_account[$ind];
	 curr_ledger_acct = vv;

     
	 setTimeout(function(){
	 });
	 
	jQuery.get("/billing/account_ledger/view_ledger_account_info?acct_id="+vv.id, function(data){
		jQuery('.pop101 .con1').html(data);
	});

}//


function view_acct_ledger_original($ind)
{
     //trig1_v2('view_accout_ledger_cont');
     
     setTimeout(function(){

         let vv = ledger_account[$ind];
         curr_ledger_acct = vv;
         //~ console.log(vv);
         
         jQuery('.pop101 .field1').html(vv.lname+', '+vv.fname+' '+vv.mi);
		 jQuery('.pop101 .field2').html(vv.address1);

          /*
          jQuery.post("/billing/reading/update_current_reading", {
          		'current_read': reading_value,
          		'acct_info': info_data,
          		'reading_year': reading_year,
          		'reading_month':reading_month
          	}, function( data ) {
          	  //$( ".result" ).html( data );
          });
          */

		jQuery('.acct_ledger_info').show();
		  
         jQuery.get("/billing/account_ledger/get_ledger_acct?acct_id="+vv.id, function(data){
			 
			 //.name_info  .acct_num
			 //.name_info  .full_name

			 jQuery('.name_info  .full_name').html(vv.lname+', '+vv.fname+' '+vv.mi);
			 jQuery('.name_info  .address').html(vv.address1);			 
			 jQuery('.name_info  .acct_num').html(vv.acct_no);			
			 jQuery('.name_info  .meter_num').html(vv.meter_number1);			
			 jQuery('.name_info  .acct_stat').html(vv.my_stat.meta_name);			
			 jQuery('.name_info  .acct_class').html(acct_class[vv.acct_type_key]);			
			  	
			 //~ acct_class
			  
              jQuery('.acct_ledger_result').hide();
              jQuery('.acct_ledger_info .content1').html(data);
              
			  //acct_ledger_result
               //jQuery('.pop101 .ledger_list1').html(data);
               //$( ".result" ).html( data );
               //$( ".result" ).html( data );
          });


     },100);

}//

var acct_class = [];
acct_class[7] = 'COM 1';
acct_class[8] = 'COM B';
acct_class[9] = 'COMM A';
acct_class[10] = 'GOVT./COMMERCIAL 1';
acct_class[11] = 'GOVT./COMMERCIAL A';
acct_class[12] = 'GOVT';
acct_class[13] = 'GOVT. B';
acct_class[14] = 'RES';


function back_to_result()
{
	  jQuery('.acct_ledger_result').show();
	  jQuery('.acct_ledger_info .content1').html('<div style="padding:30px;">Loading Please wait</div>');	
	  jQuery('.acct_ledger_info').hide();
}

function acct_ledger_search()
{

	$acct = jQuery('#search_acct_num').val();
	$meter = jQuery('#search_meter_num').val();
	$lname = jQuery('#search_last_name').val();
	//$fname = jQuery('#search_first_name').val();
	$zone = jQuery('#search_zone').val();
	$cus_stat = jQuery('#acct_status010').val();
	
	
	$url_part1 = '/';

	if(!$acct){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$acct+'/';}

	if(!$meter){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$meter+'/';}

	if(!$lname){
		$url_part1 = $url_part1+'none/';
	}else{
		
		//if(!$fname){$fname = 'NONE';}
		//$url_part1 = $url_part1+$lname+'|'+$fname+'/';
		$url_part1 = $url_part1+$lname+'/';
		
	}

	if(!$zone){$url_part1 = $url_part1+'none/';}
	else{$url_part1 = $url_part1+$zone+'/';}
	
	if(!$cus_stat){$url_part1 = $url_part1+'none/';}
	else{
		//~ $url_part1 = $url_part1+'?stat='+$cus_stat;		
		$url_part1 = $url_part1+$cus_stat+'/';		
	}
	
	
	//~ alert($url_part1);
	if($url_part1 == '/none/none/none/none/'){return;}
	
	window.location='/billing/account_ledger/filter'+$url_part1+'#account_list';
	
}



function add_bill_adjustment_show_pop()
{
	 trig1_v2('add_billing_adjustment_pop');
}

function add_bill_adjustment_save()
{
	let conf1 = confirm('Please confirm action.');
	if(!conf1){return;}
	let $my_csrf = jQuery('.my_csrf').val();
	
	let amt = jQuery('.pop101 .bill_adjustment_amount').val();
	let des_note = jQuery('.pop101 .bill_ajustment_note').val();
	let acct_id = curr_ledger_acct.id;
	let acct_no = curr_ledger_acct.acct_no;
	
	let int_amt = parseFloat(amt);
	
	if(!int_amt){alert('Please add amount');return;}
	if(int_amt <= 0){alert('Please add amount');return;}
	
	//~ console.log(curr_ledger_acct);
	
	window.location='/billing/account_ledger/add_bill_ajustment/?my_csrf='+$my_csrf+'&acct_id='+acct_id+'&acct_no='+acct_no+'&amt='+amt+'&desc='+des_note;
}

