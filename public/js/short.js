jQuery(document).ready(function(){
	//~ jQuery('.select111').select2();
	//~ jQuery('.cont001 .select222').select2();
	
	
	jQuery('.cashier_click_001').click(function($e){
		jQuery('.acct0001').focus();
	});
	
	jQuery('.acct0001').focus(function($e){
		jQuery('.acct0001').val('');
	});
	
	
	
});


shortcut.add("F12",function() {
	save_payment101();
});

shortcut.add("F10",function() {
	alert('test');
});


var current_less = 0;
var orig_acct_info = null;



async function print_or101()
{
	jQuery(".my_foc001").blur(); 
	
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}
	let cc = all_acct[nn];
		
	
	//~ if(!acct_info_data){return;}
	//~ if(acct_info_data.amount_due <= 0){return;}
	
	let confirm1 = confirm('Please confirm to print O.R.');
	if(!confirm1){return;}
	
	//~ console.log(cc.id);
	
	//~ $url_data = "http://localhost:8888/hwd_print/collector_print.php?iid="+cc.id;
	$url_data = "http://localhost/hwd_print/collector_print.php?iid="+cc.id;
	
	let RR = null;
	await jQuery.get($url_data, 
		function( data ){
			RR = data;
			//~ alert('AAA');
	}).promise();	
	
	let xx = JSON.parse(RR);
	
	console.log(xx);
	if(xx.status == 0){
		//~ alert('FAILD');
		alert(xx.msg);
		return;
	}
	
	alert('Printing');
	
}//


function withholding_proc33()
{
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}
		
	let w_val =  jQuery('.withholding_val_txt').val();
	if(!w_val){return;}	
	
	let total_dis_percent = (w_val / 100);	
	
	//~ let ch1 = jQuery('.withholding_check').is(':checked');
	
}

function withholding_proc22()
{

	return;
	return;
	return;

	let nn = jQuery('.select222').val();
	if(nn == ""){return;}
	
	let w_val =  jQuery('.withholding_val_txt').val();
	if(!w_val){return;}
	
	let total_dis_percent = (w_val / 100);	
		
	let ch1 = jQuery('.withholding_check').is(':checked');
	
	if(ch1)
	{
		jQuery('.break_down1.nn1').html(coll_resu.html2);
	}else{
		jQuery('.break_down1.nn1').html(coll_resu.html1);
	}
	
}//

function withholding_proc1()	
{
	withholding_proc22();
	return;
	return;
	return;
	
	if(!acct_info_data){return;}
	
	if(acct_info_data.amount_due <= 0){
		console.log('Failed');
		return;
	}
	
	let w_val =  jQuery('.withholding_val_txt').val();
	
	if(!w_val){return;}
	
	let total_dis_percent = (w_val / 100);
	let loc_current_bill = parseFloat(orig_acct_info.current_bill.replace(',', ''));
	let ttl_dis_val = (total_dis_percent * loc_current_bill);
	
	
	
	
	//~ alert(loc_current_bill);
	
	let ischeck = jQuery('.withholding_check').is(':checked');
	
	
	
	if(!ischeck)
	{
		jQuery('.withholding_val_txt').val(2);
		jQuery('.withholding_val_txt').prop("disabled", true);
		
		//~ acct_info_data.current_bill = orig_acct_info.current_bill;
		acct_info_data.bill_balance = orig_acct_info.bill_balance;
		acct_info_data.total_bill   = orig_acct_info.total_bill;
		acct_info_data.amount_due   = orig_acct_info.amount_due;
		
		
		jQuery('.break_down1 .bill_amt').html(acct_info_data.total_bill);
		jQuery('.break_down1 .amt_due').html(acct_info_data.bill_balance);
		jQuery('.break_down1 .w_tax1').html('');
		
		
		return false;
	}
	
	jQuery('.withholding_val_txt').prop("disabled", false);
	
	
	
	current_less = ttl_dis_val;
	
	//~ acct_info_data.current_bill = (acct_info_data.current_bill - current_less).toFixed(2);
	
	
	
	acct_info_data.total_bill   = addCommas((acct_info_data.total_bill.replace(',', '') - current_less).toFixed(2));
	
	jQuery('.break_down1 .bill_amt').html(acct_info_data.total_bill);
	jQuery('.break_down1 .w_tax1').html((ttl_dis_val.toFixed(2)));
	
	
	if(acct_info_data.amount_due  > loc_current_bill){
		ttl_dis_val = (total_dis_percent * ttl_dis_val);
	}
	
	current_less = 0;
	
	acct_info_data.bill_balance = addCommas((acct_info_data.bill_balance.replace(',', '') - current_less).toFixed(2));
	acct_info_data.amount_due   = addCommas((acct_info_data.amount_due.replace(',', '') - current_less).toFixed(2));
	
	
	$due = acct_info_data.amount_due - (acct_info_data.amount_due * total_dis_percent);
	
	jQuery('.break_down1 .amt_due').html($due);
	//~ jQuery('.break_down1 .amt_due').html(0);
	
	//~ console.log(acct_info_data);
	//~ console.log(acct_info_data.current_bill);
	
	
}//

	
	
function save_none_weter()
{
	
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}

	let cc = all_acct[nn];
	
	let conf  =  confirm('Please confirm');
	if(!conf){return;}
	
	let nw_inv = jQuery('.current_inv').val();
	
	let nw_total_amount = jQuery('.pop101  .nw_total_amount').val();
	let nw_total_amount_debit = jQuery('.pop101  .nw_total_amount_debit').val();
	let nw_desc = jQuery('.pop101  .nw_desc').val();
	let nw_reff = jQuery('.pop101  .nw_reff').val();
	let glsl_code = jQuery('.pop101  .glsl_code').val();
	let nw_tax_amount = jQuery('.pop101  .nw_tax_amount').val();
	let nw_mode_payment = jQuery('.pop101  .nw_mode_payment').val();
	
	let acct_id = cc.id;
	let or_t1 = jQuery('.or_type1:checked').val();
	let trx_date = jQuery('.date_find222').val();
	let nw_item_id = jQuery('.pop101  .cat_typ_id').val();

	let both_check_num = jQuery('.pop101  .both_check_num').val();
	let check_date = jQuery('.pop101  .check_date').val();
	let bank_name22 = jQuery('.pop101  .bank_name22').val();
	let bank_branches_22 = jQuery('.pop101  .bank_branches_22').val();
	
	
	jQuery.get( "/collections/coll1/add_none_water/"+acct_id+'?nw_inv='+nw_inv
			+'&ttl_amt='+nw_total_amount
			+'&ttl_amt_db='+nw_total_amount_debit
			+'&nw_reff='+nw_reff
			+'&glsl_code='+glsl_code
			+'&nw_desc='+nw_desc 
			+'&or_t1='+or_t1
			+'&nw_iid='+nw_item_id
			+'&nw_tax_amount='+nw_tax_amount
			+'&nw_mode_payment='+nw_mode_payment

			+'&both_check_num='+both_check_num
			+'&check_date='+check_date
			+'&bank_name22='+bank_name22
			+'&bank_branches_22='+bank_branches_22
			
			+'&trx_date='+trx_date, 
			function( data ) {
				
				alert(data.msg);
				nw_invoice = data.new_inv;
				jQuery('.pop101 .non_wat_invoice').val(nw_invoice);
				add_none_water_bill();
				get_receipt_html11();
		
	});	
		
	
}//

function add_none_water_bill(){
	
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}
	
	trig1_v2('add_new_non_water_bill_pop');
	setTimeout(function(){
		jQuery('.pop101 .non_wat_invoice').val(nw_invoice);
	}, 300);
	
	
}//
	

function bank_change()
{
	let bank_index = jQuery('.bank_name11').val();
	
	if(bank_index == '-1'){
		jQuery('.bank_branches').html('');
		return;}
	
	let rr = banks[bank_index];
	
	//~ console.log(rr.branch_txt);
	jQuery('.bank_branches').html(rr.branch_txt);
	
}//

function bank_change2()
{
	let bank_index = jQuery('.pop101 .bank_name22').val();
	
	if(bank_index == '-1'){
		jQuery('.bank_branches_22').html('');
		return;}
	
	let rr = banks[bank_index];
	
	//~ console.log(rr.branch_txt);
	jQuery('.bank_branches_22').html(rr.branch_txt);
	
}//



function view_ledger101()
{
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}	
	
	let cc = all_acct[nn];
	trig1_v2('view_ledger_pop');
	setTimeout(function(){
		
		jQuery.get( "/collections/coll1/view_my_ledger101/"+cc.id+'?acct_id='+cc.id, function( data ) {
			jQuery('.pop101 .contai11').html(data);
		});	
		
	}, 300);
	
}//


function  get_acct_info111()
{
	
	return;
	return;
	return;
	
	let nn = jQuery('.pop101 .select111').val();
	if(nn == ""){return;}
	
	let cc = all_acct[nn];
	
	jQuery('.pop101 .lname').val(cc.lname);
	jQuery('.pop101 .fname').val(cc.fname);
	jQuery('.pop101 .mi').val(cc.mi);
	
	jQuery.get( "/collections/coll1/get_acct_info_step1/"+cc.id, function( data ) {
		jQuery('.pop101 .bill_amount1').val(data.total_bill);
		jQuery('.pop101 .current_bill1').val(data.current_bill);
		//break_down1
		
		
		
		
	});	
}//


var acct_info_data = null;

var coll_resu = null;


function get_acct_info444()
{

	let nn = jQuery('.find0001').val();//NAME
	let vv = jQuery('.acct0001').val();//ACCOUNT #
	if(nn == "" && vv == ""){return;}
	
	let $url1 = '/collections/search_acct1_v1?nn='+nn+'&vv='+vv;
	//~ window.location = $url1;
	
	jQuery.get($url1, function($res){
		jQuery('.list_result1').html($res.html1);
		all_acct = $res.data1;
		
		setTimeout(function(){
			jQuery('.list_result1 li').first().trigger('click');
			jQuery('.acct0001').blur();
			
		},100);
		
	});
	
	
}//



var glb_pay_breakdown = [];
var glb_pay_breakdown_selected = [];

function breakdown_payable_input_change()
{
	var indx1 = [];
	var ttlx1 = 0;
	
	glb_pay_breakdown_selected = []; 

	jQuery('.breakdown_payable_input:checked').each(function(i){
		indx1 = $(this).val();
		glb_pay_breakdown_selected.push(indx1);
		ttlx1 = ttlx1 + glb_pay_breakdown[indx1].amt;
	});

	jQuery('.due91').val(ttlx1.toFixed(2));

	var options = { 
		minimumFractionDigits: 2,
		maximumFractionDigits: 2 
	};
	
	// jQuery('.rtxt.amt_due').html(ttlx1.toLocaleString('en', options));
	jQuery('.amt_change').html('0.00');
	jQuery('.amount_recieve').val('');

}



function get_data11xx($nu, $no, $full_name, $ind){
	
	//~ alert($ind);
	//~ alert($nu+' = '+$no);
	jQuery('.find_search  .select222').val($ind);
	
	//~ jQuery('.list_result1 li').css('background-color','#fff');
	//~ jQuery('.list_result1 .me'+$no).css('background-color','#ccc');
	
	jQuery('.list_result1 li').css('color','#000');
	jQuery('.list_result1 li').css('background-color','#fff');
	jQuery('.list_result1 .me'+$no).css('background-color','#007bff');
	jQuery('.list_result1 .me'+$no).css('color','#fff');
	

	jQuery('.client_info1').html($full_name);
	
	let $url1 = '/collections/withholding_breakdown_html1/?acct_id='+$no;
	
	jQuery.get($url1, function($res){
		
		coll_resu = $res;
		
		jQuery('.break_down1.nn1').html($res.html1);
		jQuery('.withholding_check').prop( "checked", false );
		jQuery('.withholding_val_txt').val('');
		jQuery('.withholding_val_txt').val('');
		
		jQuery('.break_down2').html($res.html3);
		jQuery('.break_down2').html($res.html3);

		glb_pay_breakdown = $res.breakdown;
		breakdown_payable_input_change();



			
	});	
	
}


async function  get_acct_info333()
{
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}
	
	let cc = all_acct[nn];
	
	let $url1 = '/collections/withholding_breakdown_html1/?acct_id='+cc.id;
	
	jQuery.get($url1, function($res){
		
		coll_resu = $res;
		
		jQuery('.break_down1.nn1').html($res.html1);
		jQuery('.withholding_check').prop( "checked", false );
		
		jQuery('.break_down2').html($res.html3);
			
	});
	
}

async function  get_acct_info222()
{
	
	get_acct_info333();
	
	return;
	return;
	return;
	
	
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}
	
	jQuery('.please_wait1').show();
	
	
	let cc = all_acct[nn];
	
	jQuery('.client_info_2 .client_name').html(cc.lname+', '+cc.fname+' '+cc.mi);
	jQuery('.client_info_2 .acct_num span').html(cc.acct_no);
	
	jQuery('.break_down1 .bill_amt').html('');
	jQuery('.break_down1 .amt_due').html('');
	jQuery('.break_down1 .bill_arrear').html('');
	jQuery('.break_down1 .amt_change').html('');
	
	
	jQuery('.break_down1 .amount_recieve').val('');	
	
	jQuery('.both_cash_amount').val('');
	jQuery('.both_check_amount').val('');
	jQuery('.both_check_num').val('');
	jQuery('.bank_name11').val('-1').change();
	
	
	jQuery('.withholding_check').prop("disabled", true);
	jQuery('.withholding_check').prop("checked", false);
	jQuery('.withholding_val_txt').prop("disabled", true);
	jQuery('.withholding_val_txt').val(2);

	jQuery('.break_down1 .w_tax1').html('');

	

	await jQuery.get( "/collections/coll1/get_acct_info_step1/"+cc.id, function( data ){
		
		//jQuery('.client_info_2 .bill_amount1').val(data.total_bill);
		//jQuery('.client_info_2 .current_bill1').val(data.current_bill);
		
		if(data.status == 0){
			alert(data.msg);
			return;
		}
		
		acct_info_data = data;
		orig_acct_info = {
				'amount_due':data.amount_due,
				'bill_balance':data.bill_balance,
				'total_bill':data.total_bill,
				'current_bill':data.current_bill
			};
		
		jQuery('.break_down1 .bill_amt').html(data.total_bill);
		jQuery('.break_down1 .amt_due').html(data.bill_balance);
		jQuery('.break_down1 .bill_arrear').html(data.bill_arear);
		
		jQuery('.break_down2').html(data.break_down);
		
		let amt_due = parseFloat(data.amount_due.replace(',', ''));
		if(amt_due > 0)
		{
			jQuery('.withholding_check').prop("disabled", false);
		}//

		
		
	}).promise();	
	
	jQuery('.please_wait1').hide();
	
}//


function amt_due_change(){
	let conf1 = confirm('Are you sure to change amt due for advance payment purpose?');
	if(!conf1){
		jQuery('.amt_due_txt').val(jQuery('.due91').val());
		return;
	}
	
	jQuery('.due91').val(jQuery('.amt_due_txt').val());
}//

function update_amount_recieve222()
{
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}

	let amt_due = jQuery('.due91').val();
	let amt_rec = jQuery('.break_down1 .amount_recieve').val();	
	
	let change = amt_rec  -  amt_due;
	
	if(change<=0){
		change = 0;
	}
	
	jQuery('.break_down1 .amt_change').html(change.toFixed(2));	
	
}//


function update_amount_recieve()
{
	update_amount_recieve222();
	
	return;
	return;
	return;
	
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}
	
	
	
	//~ console.log(acct_info_data);
	
	let amt_due = parseFloat(acct_info_data.amount_due.replace(',', ''));
	let amt_rec = jQuery('.break_down1 .amount_recieve').val();
	
	let change = amt_rec  -  amt_due;
	
	if(change<=0){
		change = 0;
	}
	
	jQuery('.break_down1 .amt_change').html(change.toFixed(2));
}//


async function save_payment102()	
{
	let confirm1 = confirm('Please confirm payent');
	if(!confirm1){return;}

	let nn = jQuery('.select222').val();
	if(nn == ""){return;}	
	
	let amt_rec = jQuery('.break_down1 .amount_recieve').val();
	let cc = all_acct[nn];
	let inv_f = jQuery('.current_inv').val();
	
	
}


async  function save_payment101()	
{
	
	
	//~ save_payment102();
	//~ return;
	//~ return;
	//~ return;
	
	jQuery(".my_foc001").blur(); 
	

	let confirm1 = confirm('Please confirm payent');
	if(!confirm1){return;}
	
	let nn = jQuery('.select222').val();
	if(nn == ""){return;}	
	
	jQuery('.please_wait1').show();
	
	
	let amt_rec = jQuery('.break_down1 .amount_recieve').val();
	
	//let bill_id = acct_info_data.bill_id;
	let bill_id = jQuery('.bill_id91').val();
	
	let cc = all_acct[nn];
	//~ alert(cc);
	
	let inv_f = jQuery('.current_inv').val();
	
	let payment_method = jQuery('input[name=pay_meth]:checked').val();
	
	let both_cash_amount = jQuery('.both_cash_amount').val();
	let both_check_amount = jQuery('.both_check_amount').val();
	let both_check_num = jQuery('.both_check_num').val();
	let bank_name11 = jQuery('.bank_name11').val();
	let bank_branches = jQuery('.bank_branches').val();
	
	//~ let bank_branches = jQuery('.bank_branches').val();
	//~ alert(payment_method);
	//~ return;
	
	let ischeck = jQuery('.withholding_check').is(':checked');
	let w_val   =  jQuery('.withholding_val_txt').val();
	//~ if(!w_val){return;}
	
	
	//let w_val   =  jQuery('.withholding_val_txt').val();
	let or_t1 = jQuery('.or_type1:checked').val();
	
	let trx_date = jQuery('.date_find222').val();
	let ada_amount = jQuery('.ada_amount').val();
	
	let chk_full = jQuery('.both_check_amount_full').val();
	let chk_date = jQuery('.check_date').val();
	let amt_due_x = jQuery('.due91').val();	// FOR OVER PAYMENT ALLOWED ONLY
	
	let bb_selected  = JSON.stringify(glb_pay_breakdown_selected);
	let bb_breakdown = JSON.stringify(glb_pay_breakdown);
	let bb_amount    = 	jQuery('.due91').val();

	
	//~ alert(trx_date);
	//~ return;

	await jQuery.get( "/collections/coll1/make_payment_step1/"+cc.id+'?bill_id='+bill_id+
				'&amt='+amt_rec+'&inv='+inv_f+
				'&bcash='+both_cash_amount+'&bcheck='+both_check_amount+
				'&bchecknum='+both_check_num+'&bbankname='+bank_name11+
				'&bbankbranch='+bank_branches+
				'&method='+payment_method+
				'&wtax='+ischeck+
				'&wtax_value='+w_val+
				'&ort1='+or_t1+
				'&ada_amount='+ada_amount+
				'&chk_full='+chk_full+
				'&trx_date='+trx_date+
				'&chk_date='+chk_date+
				'&amt_due_x='+amt_due_x+	// FOR OVER PAYMENT ALLOWED ONLY
				'&bb_selected='+bb_selected+
				'&bb_breakdown='+bb_breakdown+
				'&bb_amount='+bb_amount
				, 
		function( data ) 
		{
		
			if(data.status == 0){
				alert(data.msg);
				return;
			}
			
			//~ jQuery('.withholding_check').prop( "checked", false );

			get_receipt_html11();
			get_acct_info222();
			
			let chng = jQuery('.amt_change').html();
			
			alert("Done Save : \nCHANGE : "+chng);
			
			defaut111();
			
			return;
			return;
			return;
			return;
			return;
			return;
			
			jQuery('.current_inv').val(data.new_invoice);
			jQuery('.break_down1 .amount_recieve').val('');	
			
			jQuery('.both_cash_amount').val('');
			jQuery('.both_check_amount').val('');
			jQuery('.both_check_num').val('');
			jQuery('.bank_name11').val('-1').change();
			
			get_acct_info222();
			alert('Done Save');
	
	}).promise();	


	jQuery('.please_wait1').hide();

	
}//


function defaut111()
{
	
	jQuery('.cash1').prop("checked", true);
	jQuery('.both_cash_amount').val('');
	jQuery('.both_check_amount').val('');
	jQuery('.both_check_num').val('');
	jQuery('.both_check_amount_full').val('');
	jQuery('.bank_name11').val('-1').change();
	
}//



function addCommas(nStr) 
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
