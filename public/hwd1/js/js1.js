jQuery(document).ready(function(){
	
	$mm = jQuery(window).height();
	jQuery('.main_row').height($mm);
	jQuery('.main_row').css({'minHeight':$mm+'px'});
	
	jQuery('.tab1x  li').click(function(){
		
		$tab_name = jQuery(this).data('tab');
		
		jQuery('.tab1x  li').removeClass('active');
		jQuery(this).addClass('active');
		
		jQuery('.tab_cont_1 .tab_item').hide();
		jQuery('.tab_cont_1 .'+$tab_name).show();
	
	});
	
	$def_tab = jQuery('.tab_cont_1').data('default');
	jQuery('.tab_cont_1  .'+$def_tab).show();
	btm_icon();
	
});


function view_item($ind){
	alert($ind);
}

function btm_icon(){
	jQuery('.btm_icon').on('click',function(){
		jQuery( ".doc_list" ).toggle();
	});
}