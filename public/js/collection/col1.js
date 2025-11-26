
function go_search_acct()
{

	var acct_num = jQuery('#s_accnt_no').val().trim() || 'none';
	var lname = jQuery('#s_fn').val().trim() || 'none';

	if(acct_num == 'none'  && lname == 'none'){return;}

	last_url =  "/collections/search/"+acct_num+'/'+lname;
	jQuery.get(last_url)
	 .done(function( data ) {
			jQuery('#result_here').html(data.html);
			result_data = data.js_data;
	 });
}

function update_amount_pp1()
{
	let am1 = jQuery('.pop101 #amount_pay').val();
	jQuery('.pop101 #amount_pp1').val(am1);
}

function  refresh_last_url()
{
	jQuery.get(last_url)
	 .done(function( data ) {
			jQuery('#result_here').html(data.html);
			result_data = data.js_data;
	 });
}

function water_bill_execute()
{
	pop_close();	
	view_acct_info2(current_search_index);
}

function none_water_bill_execute()
{
	var acct = result_data.data[current_search_index];
	curr_acct = acct;	
	
	//~ console.log(curr_acct);
	
	pop_close();	
	trig1_v2('none_water_bill_pop1');
	setTimeout(function(){
		
		jQuery('.pop101  .invoice_number_used').val(curr_invoice);
		
		let full_name = curr_acct.lname+', '+curr_acct.fname + ' ' + curr_acct.mi;
		
		jQuery('.pop101  .non_wa_name').html(full_name);
		jQuery('.pop101  .non_wa_acct').html(curr_acct.acct_no);
		jQuery('.pop101  .non_wa_meter').html(curr_acct.meter_number1);
		jQuery('.pop101  .non_wa_addr').html(curr_acct.address1);
		
		//none_water_bill_pop1
	}, 50);
}

function view_acct_info($ind)
{
	current_search_index = $ind;
	trig1_v2('select_type_of_payment');
}


var current_search_index = null;

function view_acct_info2($ind)
{
	trig1_v2('cashier_view_info');

	setTimeout(function()
     {

		var acct = result_data.data[$ind];

		curr_acct = acct;
		jQuery('.pop101 .val1').html(acct.lname+', '+acct.fname+' '+acct.mi);
		jQuery('.pop101 .val2').html(acct.address1);
		jQuery('.pop101 .val3').html(acct.acct_no);
		jQuery('.pop101 .val4').html(acct.period_read);


		let prev_read  = 0;
		let curr_read  = 0;
		let bill_total = 0;
		let collectable = 0;
		let discount = 0;
		let penalty = 0;

		let arrear  =  acct.arrear1.amount;
				
		if(acct.reading1)
		{
			prev_read = acct.reading1[0].prev_reading;
			curr_read = acct.reading1[0].curr_reading;
			bill_total = acct.billing1.billing_total;
			collectable = acct.collectable;
			discount  =  acct.billing1.discount;
			penalty  =  acct.billing1.penalty;
		}


		let cunsum = parseInt(curr_read)  -  parseInt(prev_read);

		jQuery('.pop101  .reading1  .bal1').html(prev_read || '---');
		jQuery('.pop101  .reading1  .bal2').html(curr_read || '---');
		jQuery('.pop101  .reading1  .bal3').html(cunsum || '---');


		//~ if(acct.reading1[0]){
			//~ jQuery('.pop101  .info1  .bal1').html(formatDollar(acct.billing1.billing_total));
		//~ }else{
			//~ jQuery('.pop101  .info1  .bal1').html(formatDollar(0));
		//~ }
		

		jQuery('.pop101  .info1  .bal2').html(formatDollar(bill_total));
		jQuery('.pop101  .info1  .bal3').html(formatDollar(collectable));

		jQuery('.pop101  .info1  .span_arrear').html(arrear.toFixed(2));
		jQuery('.pop101  .info1  .span_discount').html(discount || '0.00');
		jQuery('.pop101  .info1  .span_penalty').html(formatDollar(penalty.toFixed(2)));
		jQuery('.pop101  .info1  .span_adjust').html(formatDollar(acct.adjust.toFixed(2)));

		jQuery('.pop101  .payment001  .bal1').html(formatDollar(acct.collected));
		jQuery('.pop101  .payment001  .bal2').html(formatDollar(acct.remaining_balance));
		jQuery('.pop101  .invoice_number_used').val(curr_invoice);

		jQuery('.pop101 #amount_pay').val(acct.remaining_balance);
		//adjust
		update_amount_pp1();


	},200);
	
	
}

function view_acct_info2_BBB($ind)
{
	
	trig1_v2('cashier_view_info');

	setTimeout(function()
     {

		var acct = result_data.data[$ind];
		console.log(acct);

		curr_acct = acct;
		jQuery('.pop101 .val1').html(acct.lname+', '+acct.fname+' '+acct.mi);
		jQuery('.pop101 .val2').html(acct.address1);
		jQuery('.pop101 .val3').html(acct.acct_no);
		jQuery('.pop101 .val4').html(acct.period_read);

		if(acct.reading1.length == 0)
		{
			acct.reading1[0] = {};
			acct.reading1[0].billing1 = {};
		}

		//let prev_read = acct.reading1[0].init_reading||acct.reading1[1].curr_reading;
		let prev_read = acct.reading1[0].init_reading||acct.reading1[0].prev_reading;
		let curr_read = acct.reading1[0].curr_reading;

		let cunsum = parseInt(curr_read)  -  parseInt(prev_read);

		jQuery('.pop101  .reading1  .bal1').html(prev_read || '---');
		jQuery('.pop101  .reading1  .bal2').html(curr_read || '---');
		jQuery('.pop101  .reading1  .bal3').html(cunsum || '---');


		if(acct.reading1[1]){
			jQuery('.pop101  .info1  .bal1').html(formatDollar(acct.reading1[1].billing1.billing_total));
		}else{
			jQuery('.pop101  .info1  .bal1').html(formatDollar(0));
		}
		
		console.log(acct);

		jQuery('.pop101  .info1  .bal2').html(formatDollar(acct.reading1[0].billing1.billing_total));
		jQuery('.pop101  .info1  .bal3').html(formatDollar(acct.collectable));

		let arrear  =  acct.reading1[0].billing1.arrears;
		let discount  =  acct.reading1[0].billing1.discount;
		let penalty  =  acct.reading1[0].billing1.penalty;

		jQuery('.pop101  .info1  .span_arrear').html(arrear);
		jQuery('.pop101  .info1  .span_discount').html(discount);
		jQuery('.pop101  .info1  .span_penalty').html(penalty);

		jQuery('.pop101  .payment001  .bal1').html(formatDollar(acct.collected));
		jQuery('.pop101  .payment001  .bal2').html(formatDollar(acct.remaining_balance));
		jQuery('.pop101  .invoice_number_used').val(curr_invoice);

		jQuery('.pop101 #amount_pay').val(acct.remaining_balance);
		update_amount_pp1();


	},200);
}

function formatDollar(num)
{
	if(!num)
	{
		num = 0;
	}
	return '<small  style="font-size:9px;">&#x20b1;</small> '+num.toLocaleString();
}


function make_payment()
{

	if(curr_invoice == 0)
	{
		alert('Invoice is empty. Please add new Invoice set.');
		return;
	}

	let cash1 =  jQuery('.pop101 #amount_pp1').val();

	if(!cash1){
		alert('Please enter cash.');
		return;
	}

	let  bank_id = jQuery('.pop101 #bank_id').val();
	let  bank_check = jQuery('.pop101 #bank_check').val();
	let  bank_info = jQuery('.pop101 #bank_info').val();	
	
	if(bank_id !=0)
	{
		
		bank_check = bank_check.trim();
		bank_info = bank_info.trim();		
		
		if(bank_check == '')
		{
			alert('Please enter check number.');
			return;
		}
		
		if(bank_check == '')
		{
			alert('Please enter check number.');
			return;
		}
		
	}




	

	let is_sure = confirm('are you sure to pay?');

	if(!is_sure)
	{return;}
	
	//~ return;

	var amount_pay = jQuery('.pop101 #amount_pay').val();
	
	let inv_set = jQuery('#invoice_set').val();
	let inv_used = jQuery('.pop101 .invoice_number_used').val();

	jQuery('.payment_loading1').show();


	jQuery.post( "/collections/make_payment", {
			'amount':amount_pay, 
			'data': curr_acct,
			'_token':csrf1, 
			'invoice': inv_used, 
			'inv_set': inv_set, 
			'cash1': cash1,
			'bank_id':bank_id,
			'bank_check':bank_check,
			'bank_info':bank_info
	})
	 .done(function( data ) {

			if(data.status == 'error'){
				if(data.code == 1){alert('Invoice '+inv_used+' is already in used.');}
				if(data.code == 2){alert('Invoice '+inv_used+' is invalid.');}
				if(data.code == 3){alert('Invoice '+inv_used+' is not valid invoice set');}
				jQuery('.payment_loading1').hide();
				return;
			}

			if(data.status == 'success')
			{
				invoices = data.invs;

				if(invoices.length != 0){
					curr_invoice = invoices[0].seq_c;
				}else{
					curr_invoice = 0;
				}

				jQuery('#invoice_set').html(data.inv_set);
				jQuery('#invoice_set').change();
			}

			jQuery('.payment_loading1').hide();

			pop_close();
			refresh_last_url();

	 }).catch(function(err){
		 console.log(err);
		 alert('Payment Failed.');
		 jQuery('.payment_loading1').hide();
	 });
}

function navigate_xx1($url1)
{
	last_url = $url1;
	refresh_last_url();
}

function make_payment_non_water()
{
	
	if(curr_invoice == 0)
	{
		alert('Invoice is empty. Please add new Invoice set.');
		return;
	}

	let cash1 =  jQuery('.pop101 #amount_pp1').val();
	
	let  bank_id = jQuery('.pop101 #bank_id').val();
	let  bank_check = jQuery('.pop101 #bank_check').val();
	let  bank_info = jQuery('.pop101 #bank_info').val();
	
	if(!cash1)
	{
		alert('Please enter cash.');
		return;
	}
	
	if(bank_id !=0)
	{
		
		
		bank_check = bank_check.trim();
		bank_info = bank_info.trim();		
		
		if(bank_check == '')
		{
			alert('Please enter check number.');
			return;
		}
		
		if(bank_check == '')
		{
			alert('Please enter check number.');
			return;
		}
		
	}
	
	let is_sure = confirm('are you sure to pay?');
	if(!is_sure)
	{return;}
	
	let inv_set = jQuery('#invoice_set').val();
	let inv_used = jQuery('.pop101 .invoice_number_used').val();
	
	let non_water_item = jQuery('.pop101 #non_water_item').val();
	
	jQuery('.payment_loading1').show();


	jQuery.post( "/collections/make_payment_non_water", {
				'data': curr_acct,
				'_token':csrf1, 
				'invoice': inv_used, 
				'inv_set': inv_set, 
				'cash1': cash1, 
				'non_water_item':non_water_item, 
				'bank_id':bank_id, 
				'bank_check':bank_check, 
				'bank_info':bank_info
	})
	 .done(function( data ) {

			if(data.status == 'error'){
				if(data.code == 1){alert('Invoice '+inv_used+' is already in used.');}
				if(data.code == 2){alert('Invoice '+inv_used+' is invalid.');}
				if(data.code == 3){alert('Invoice '+inv_used+' is not valid invoice set');}
				if(data.code == 4){alert('Failed to find other payable.');}
				if(data.code == 5){alert('Payment failed. it needs full payment.');}
				jQuery('.payment_loading1').hide();
				return;
			}

			if(data.status == 'success')
			{
				invoices = data.invs;

				if(invoices.length != 0){
					curr_invoice = invoices[0].seq_c;
				}else{
					curr_invoice = 0;
				}

				jQuery('#invoice_set').html(data.inv_set);
				jQuery('#invoice_set').change();
			}

			jQuery('.payment_loading1').hide();

			pop_close();
			refresh_last_url();

	 }).catch(function(err){
		 console.log(err);
		 alert('Payment Failed.');
		 jQuery('.payment_loading1').hide();
	 });
	
	
	
}//END
