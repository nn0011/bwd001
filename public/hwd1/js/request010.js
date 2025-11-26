var request_indi = 0;

jQuery( document ).ready(function() {
	
		jQuery.get('/admin/service/request001', function($rs){
			
			request_indi = $rs.req_count;
			
			if(request_indi !=  0){
				//jQuery('.req1').after('<span class="req_ind">New</span>');
				jQuery('.req_ind11').show();
				jQuery('.req_ind.acct01').show();
			}
			
			if($rs.bill_req1 != 0){
				jQuery('.req_ind11').show();
				jQuery('.req_ind.bill01').show();
			}
			
		});
	
});
