function view_reading_info($ind)
{
	curr_index = $ind;
	

	if(reading_data1[$ind])
	{
		curr_read03 = reading_data1[$ind].curr_reading;
		prev_read03 = reading_data1[$ind].prev_reading;
		init_read03   =  reading_data1[$ind].init_reading  || 0;
	}

	current_reading = reading_data1[$ind];

	trig1_v2('view_reading_acct01');

	setTimeout(function(){

		//$acct_data = current_reading;
		$acct_data = reading_data1[$ind].account1;

		curr_el =  jQuery('.rowx'+$ind+'  .curr_read_el').val() || 0;
		if(curr_el == 0)
		{
			curr_el =  jQuery('.rowx'+$ind+'  .curr_read_el2').html();
			if(!curr_el)
			{curr_el = '----';}
		}
		
		//~ console.log($acct_data);

		jQuery('.view_acct_info_pop .field1').html($acct_data.lname+', '+$acct_data.fname+' '+$acct_data.mi);
		jQuery('.view_acct_info_pop .field2').html($acct_data.address1);
		jQuery('.view_acct_info_pop .field3').html($acct_data.acct_no);
		jQuery('.view_acct_info_pop .field4').html($acct_data.meter_number1);
		jQuery('.view_acct_info_pop .field6').html($acct_data.zone_id);
		jQuery('.view_acct_info_pop .prev_reading').html(jQuery('.rowx'+$ind+'  .prev_read_el').html());
		jQuery('.view_acct_info_pop .curr_reading').html(curr_el);
		jQuery('.view_acct_info_pop .consump_pop').html(jQuery('.rowx'+$ind+'  .consump').html());

	},100);

}

function add_initial_reading_act(){
	trig1_v2("add_initial_reading");

	setTimeout(function(){

		$acct_data = current_reading;
		// curr_el =  jQuery('.rowx'+$ind+'  .curr_read_el').val() || 0;
		// if(curr_el == 0){curr_el = '----';}

		jQuery('.view_acct_info_pop .field1').html($acct_data.lname+', '+$acct_data.fname+' '+$acct_data.mi);
		jQuery('.view_acct_info_pop .field2').html($acct_data.address1);
		jQuery('.view_acct_info_pop .field3').html($acct_data.acct_no);
		jQuery('.view_acct_info_pop .field4').html('None');
		jQuery('.view_acct_info_pop .field6').html($acct_data.zone_id);

	},100);
}

function save_initial_reading(){

     if(!current_reading.meter_number1)
     {
          alert('Meter number is required before adding initial reading');
          return false;
     }

	if(rec_period != now_period){
		alert('Add Initial Reading is not allowed.');
		return;
	}

	$init_reading1 = jQuery('.pop101 .init_reading_txt').val() || 0;

	if($init_reading1 == 0){
		jQuery('.rowx'+curr_index+'  .prev_read_el').html('----');
	}else{
		jQuery('.rowx'+curr_index+'  .prev_read_el').html($init_reading1);
		if(reading_data1[curr_index].reading1){
			reading_data1[curr_index].reading1.prev_reading = $init_reading1;
		}
	}

	var reading_year = jQuery('#period_year').val();
	var reading_month = jQuery('#period_month').val();

	/**/
	jQuery.post( "/billing/reading/update_init_reading", {
			'init_reading': $init_reading1,
			'data1' : reading_data1[curr_index],
			'reading_year': reading_year,
			'reading_month':reading_month
		}, function( data ) {
		  //$( ".result" ).html( data );
	});
	/**/

	setTimeout(function(){
		pop_close();
	}, 300);


}


function update_me11($ind,  $read1)
{

	var reading_value = jQuery($read1).val();
	var reading_year = jQuery('#period_year').val();
	var reading_month = jQuery('#period_month').val();
	var info_data = reading_data1[$ind];

	if(!reading_data1[$ind]){
		reading_data1[$ind] = {};
	}

	var prev_read = parseInt(reading_data1[$ind].prev_reading) || 0;
	var curr_read =  parseInt(reading_value) || 0;
	var consume  =  curr_read - prev_read;

	curr_read03 = curr_read;

	if(prev_read <=0 || curr_read<=0){
		jQuery('.rowx'+$ind+'  .consump').html('----');
	}else{
		if(consume <= 0){
			jQuery('.rowx'+$ind+'  .consump').html('----');
		}else{
			jQuery('.rowx'+$ind+'  .consump').html(consume);
		}
	}

	/**/
	jQuery.post( "/billing/reading/update_current_reading", {
			'current_read': reading_value,
			'acct_info': info_data,
			'reading_year': reading_year,
			'reading_month':reading_month
		}, function( data ) {
		  //$( ".result" ).html( data );
			if(data.stat == "failed"){
				jQuery($read1).val('');
				jQuery('.rowx'+$ind+'  .consump').html(0);
			}
	});
	/**/
}


function update_me11_prev($ind,  $read1)
{
	
	var prev_reading_val = jQuery('.prev_val_'+$ind).val();
	var reading_value = jQuery('.curr_val_'+$ind).val();

	//var reading_value = jQuery($read1).val();
	var reading_year = jQuery('#period_year').val();
	var reading_month = jQuery('#period_month').val();
	var info_data = reading_data1[$ind];

	if(!reading_data1[$ind]){
		reading_data1[$ind] = {};
	}

	var prev_read = parseInt(prev_reading_val) || 0;
	var curr_read =  parseInt(reading_value) || 0;
	var consume  =  curr_read - prev_read;

	curr_read03 = curr_read;
	
	

	if(prev_read <=0 || curr_read<=0){
		jQuery('.rowx'+$ind+'  .consump').html('----');
	}else{
		if(consume <= 0){
			jQuery('.rowx'+$ind+'  .consump').html('----');
		}else{
			jQuery('.rowx'+$ind+'  .consump').html(consume);
		}
	}
	
	//~ alert('gold');
	//~ return;
	//~ return;
	//~ return;
	//~ return;

	/**/
	jQuery.post( "/billing/reading/update_previous_reading", {
			'previous_reading': prev_reading_val,
			'current_read': reading_value,
			'acct_info': info_data,
			'reading_year': reading_year,
			'reading_month':reading_month
		}, function( data ) {
		  //$( ".result" ).html( data );
			if(data.stat == "failed"){
				jQuery($read1).val('');
				jQuery('.rowx'+$ind+'  .consump').html(0);
			}
	});
	/**/	
	
}


function  initialize_reading(){
}

function add_meter_reading_form(){
	trig1_v2("add_meter_number");
	setTimeout(function(){

			//console.log(current_reading);
			//current_reading.fname
			//current_reading.lname
			//current_reading.mi
			//current_reading.acct_no

			jQuery('.pop101 .field1').html(current_reading.lname+', '+current_reading.fname+' '+current_reading.mi);
			jQuery('.pop101 .field2').html(current_reading.address1);
			jQuery('.pop101 .field3').html(current_reading.acct_no);
			jQuery('.pop101 #acct_id').val(current_reading.id);
			jQuery('.pop101 .init_reading_txt').val(current_reading.meter_number1);


	}, 200);
}/**/
