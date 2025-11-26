jQuery(document).ready(function(){
	
	jQuery('.date_find111').datepicker(
		{format: 'yyyy-mm-dd', autoHide:true}
	);
	
});

function change_date_find(){
	let date1 = jQuery('.date_find111').val();
	if(!date1){return;}
	window.location = '/collections/reports?dd='+date1;
}//
