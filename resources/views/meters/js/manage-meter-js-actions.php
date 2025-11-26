
<script>

jQuery(document).ready(function(){
});

var meter_info = null;
var meter_list_index = null;

function assign_meter_submit_form()
{
    // personel_select
    // type_select
    // date_trx

    // account_selected
    // ajax_meter_result_current

    let personel_select =  jQuery('.pop101 .personel_select').val().trim().toUpperCase(); 
    let type_select     =  jQuery('.pop101 .type_select').val().trim().toUpperCase();
    let date_trx        =  jQuery('.pop101 .date_trx').val().trim().toUpperCase();

    console.log(account_selected);
    console.log(ajax_meter_result_current);

    jQuery.post( "/water-meter/add_to_history", {
               'personel_select': personel_select,
               'type_select': type_select,
               'date_trx': date_trx,
               'acct_id': account_selected.id,
               'meter_id': ajax_meter_result_current.id,
               '_token':csrf_key
		}, function( data ) {
            alert('Success.');
            meter_list_init(); // From Manage-meter.php
            pop_close();
        //   jQuery('.loading1').hide();
        //   jQuery('.ageing_result').html(data);
	});    

}

function assign_to_account()
{
    // account_selected
    // ajax_meter_result_current

    let index_selected = jQuery('.pop101 input[name="assign_me"]:checked').val();
    account_selected = account_result[index_selected];
    trig1_v2('assign_meters_to');

    jQuery('.pop101 .acct_no').html(account_selected.acct_no);
    jQuery('.pop101 .full_name').html((account_selected.lname+', '+account_selected.lname+' '+account_selected.mi));
    jQuery('.pop101 .address').html(account_selected.address1);
    jQuery('.pop101 .meter_no').html(ajax_meter_result_current.meter_num);
    jQuery('.pop101 .meter_brand').html(ajax_meter_result_current.brand_name);

}

function assign_meter_popup($ind, $mid, $mnum) 
{
    trig1_v2('assign_meters');
    ajax_meter_result_current = ajax_meter_result[$ind];
}

meter_info = null;
meter_list_index = null;

function view_meter_history($ind, $mid, $mnum) 
{
    meter_list_index = $ind;

    trig1_v2('view_meter_history_pop');
    ajax_meter_result_current = ajax_meter_result[$ind];

    console.log(ajax_meter_result_current);

    jQuery('.pop101 .meter_no').html(ajax_meter_result_current.meter_num);
    jQuery('.pop101 .meter_brand').html(ajax_meter_result_current.brand_name);
    jQuery('.pop101 .meter_size').html(ajax_meter_result_current.meter_size);

    jQuery.get( "/water-meter/get_history/"+ajax_meter_result_current.id, function( data ) {
        
        jQuery('.pop101 .history_cont').html(data.html1);
        console.log(data.meter_info);
        meter_info = data.meter_info;

	});    

}//

function edit_meter_info() 
{
    trig1_v2('add_meter_form');
    console.log(ajax_meter_result_current);
    setTimeout(()=>{
        jQuery('.pop101 .meter_num').val(ajax_meter_result_current.meter_num); 
        jQuery('.pop101 .brand_select').val(ajax_meter_result_current.brand_name);
        jQuery('.pop101 .size_select').val(ajax_meter_result_current.meter_size);
        jQuery('.pop101 .is_new').val('no');
        jQuery('.pop101 .meter_id').val(ajax_meter_result_current.id);
    },500);
}


function assign_meter_search_account() 
{
   let acct_no = jQuery('.pop101 .acct_no').val().trim();
   let lname = jQuery('.pop101 .lname').val().trim();
   let fname = jQuery('.pop101 .fname').val().trim();

   jQuery.get("/water-meter/ajax_search_account?acct_no="+acct_no+"&lname="+lname+"&fname="+fname, function( data ) {
        jQuery('.pop101  .acct_search_result').html(data.html1);
        account_result = data.accounts;
    });
}

function add_edit_remarks_pop($index) 
{
    console.log(meter_info.histories[$index].remaks);
    // trig1_v2('add_edit_remaks_pop');
    trig1_v2('add_edit_remarks_pop');

    setTimeout(()=>{
        jQuery('.pop101 .remaks_text').val(meter_info.histories[$index].remaks);
        jQuery('.pop101 .history_id').val(meter_info.histories[$index].id);
    },500);
}//

function save_remarks_by_history()
{
  let  remaks_text =  jQuery('.pop101 .remaks_text').val();
  let  history_id =    jQuery('.pop101 .history_id').val();


  jQuery.post( "/water-meter/save_remarks_by_history_id", {
               'remaks_text': remaks_text,
               'history_id': history_id,
               '_token':csrf_key
		}, function( data ) {
            alert('Success.');
            meter_list_init(); // From Manage-meter.php
            view_meter_history(meter_list_index, meter_info.id, meter_info.meter_num);
	});    

}//

function save_remarks_by_history__pop_close()
{
    view_meter_history(meter_list_index, meter_info.id, meter_info.meter_num);
}

</script>

