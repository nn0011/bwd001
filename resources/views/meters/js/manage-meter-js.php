
<script>

jQuery(document).ready(function(){

    meter_list_init();

    add_personel_loop_result();

});

// Globals
var ajax_meter_result = [];
var ajax_meter_result_current = null;
var personel_list = <?php echo json_encode($personel->toArray()); ?>;

//Actions
var account_result   = [];
var account_selected = null;


function search_meter_number() {

    jQuery('.scroll1').html('Please wait...');

    let meter_num = jQuery('.search_meter_input').val().trim();

    jQuery.get("/water-meter/ajax_search?meter_num="+meter_num, function( data ) { 
        jQuery('.scroll1').html(data.html1);
        ajax_meter_result = data.meter_list;
    });

}

function meter_list_init() {

    jQuery('.scroll1').html('Please wait...');

    jQuery.get("/water-meter/ajax_list", function( data ) { 
        jQuery('.scroll1').html(data.html1);
        ajax_meter_result = data.meter_list;
    });

}


function save_new_water_meter() {

   // meter_num, brand_select, brand_text, size_select
   let meter_num = jQuery('.pop101 .meter_num').val().trim();
   let brand_select = jQuery('.pop101 .brand_select').val().trim();
   let brand_text = jQuery('.pop101 .brand_text').val().trim();
   let size_select = jQuery('.pop101 .size_select').val().trim();
   let is_new   = jQuery('.pop101 .is_new').val().trim();
   let meter_id   = jQuery('.pop101 .meter_id').val().trim();

   if( meter_num == '') {
        alert("Meter number is empty.");
        return;
   }

   if( brand_select=='' &&  brand_text == '') {
        alert("Please select Brand.");
        return;
   }

   jQuery.get("/water-meter/add_water_meter?meter_num="+meter_num
                    +"&brand_name="+brand_text
                    +"&meter_size="+size_select
                    +"&is_new="+is_new
                    +"&meter_id="+meter_id
                    +"&brand_select="+brand_select, 
            function( data ) {

       if(data.status == 1) {
         alert('Meter Saved');
         meter_list_init();
         return;
       } 

        alert(data.msg);
   });

   // alert("ayos");

}

function add_personel_loop_result()
{
    $html1 = '';
    $html2 = '<option value="">Select Personel</option>';

    for (let i = 0; i < personel_list.length; i++) {
        $html1 = $html1+'<tr><td>'+personel_list[i].meta_name+'</td></tr>'
        $html2 = $html2+'<option value="'+personel_list[i].meta_name+'">'+personel_list[i].meta_name+'</option>'
    }

    jQuery('.pop101 .tab_personel').html($html1);
    jQuery('.personel_select').html($html2);
}

function add_personel_show_form()
{
    trig1_v2('add_personel_form');
    add_personel_loop_result();
}

function add_personel_submit()
{
    let personel_name = jQuery('.pop101 .personel_name').val().trim();

    if( personel_name =="" ){
        alert('Please insert name.');
        return;
    }

    jQuery.get("/water-meter/add_personel_name?person_name="+personel_name, function( data ) {
        personel_list = data.personel_list;
        add_personel_loop_result();
        jQuery('.pop101 .personel_name').val('');
    });


}


</script>

