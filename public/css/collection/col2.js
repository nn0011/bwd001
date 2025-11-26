jQuery(document).ready(function(){
	jQuery('.date_find111').datepicker(
		{format: 'yyyy-mm-dd', autoHide:true}
	);
});

var curr_coll = null;
var curr_ind = null;


function change_date_find(){
	let date1 = jQuery('.date_find111').val();
	if(!date1){return;}
	window.location = '/collections/activities?dd='+date1;
}//


function edit_receipt_type($ind){
	//~ console.log(coll1[$ind]);	
	curr_ind = $ind;
	curr_coll = coll1[$ind];
	trig1_v2('edit_receipt_type_pop');
	setTimeout(function(){
		
		jQuery('.pop101 .vname').html(curr_coll.full_name);
		jQuery('.pop101 .vdate').html(curr_coll.pay_date);
		jQuery('.pop101 .vreceipt').html(curr_coll.invoice_num);
		jQuery('.pop101 .vreceipt_type').html(curr_coll.status2);
		jQuery('.pop101 .vamount').html(curr_coll.amount_txt);
		
		jQuery('.pop101 .vdate22 input').datepicker(
			{format: 'yyyy-mm-dd', autoHide:true}
		);		
		
	}, 200)
}


function official_receipt(){
	
	if(curr_coll == null){
		return;
	}
	
	let conf  =  confirm('Please confirm action');
	if(!conf){return;}
	
	jQuery.get( "/collections/coll1/update_receipt_type/"+curr_coll.id+'?ctype=1', function( data ) {
		
		if(data.stat == 0){
			alert(data.msg);
			return;
		}
		
		if(data.stat == 1){
			alert(data.msg);
			
			jQuery('.vv_'+curr_ind).html(data.status2);
			jQuery('.pop101 .vreceipt_type').html(data.status2);

			return;
		}
		
	});

	
}//

function collector_receipt(){
	if(curr_coll == null){
		return;
	}

	let conf  =  confirm('Please confirm action');
	if(!conf){return;}

	jQuery.get( "/collections/coll1/update_receipt_type/"+curr_coll.id+'?ctype=2', function( data ) {
		
		if(data.stat == 0){
			alert(data.msg);
			return;
		}
		
		if(data.stat == 1){
			alert(data.msg);
			jQuery('.vv_'+curr_ind).html(data.status2);
			jQuery('.pop101 .vreceipt_type').html(data.status2);			
			return;
		}		
		
	});

	
}//

function cancel_receipt(){
	if(curr_coll == null){
		return;
	}
	
	let conf  =  confirm('Please confirm action');
	if(!conf){return;}
	
	
	jQuery.get( "/collections/coll1/update_receipt_type/"+curr_coll.id+'?ctype=3', function( data ) {
		
		if(data.stat == 0){
			alert(data.msg);
			return;
		}
		
		if(data.stat == 1){
			alert(data.msg);
			jQuery('.vv_'+curr_ind).html(data.status2);
			jQuery('.pop101 .vreceipt_type').html(data.status2);			
			pop_close();
			return;
		}		
		
		
	});
}//
