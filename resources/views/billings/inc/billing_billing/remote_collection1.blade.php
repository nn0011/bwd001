<?php 


?>

<div class="rem_cont">


	<div style="text-align: right;padding-bottom:15px;">
		<button class="form-controlx btn btn-small" onclick="clear_temp_remote_collection_data()">CLEAR REMOTE COLLECTION</button>
	</div>


	<form method="get" target="_blank" action="/create_remote_collection_session/rem-coll-<?php echo date('Y-m-d'); ?>" onsubmit="return generate_collection_data();" class="">
		
		{{ csrf_field() }}

		
		Collector <br />
		<select  class="form-control my_collector" name="my_collector">
			<?php 
			
			if(@$collectors):
				foreach($collectors->users as $cc): ?>
				<option value="<?php echo $cc->id; ?>"><?php echo $cc->name; ?></option>
				<?php endforeach;
			endif;
			 ?>
		</select>

		<br />
		<br />
		Zones <br />

		<select multiple="multiple" class="form-control h200 my_zones"  name="my_zones[]">
			<?php 
				foreach($zones as $zz): 
					$zz = (object) $zz;
			?>
				<option value="<?php echo $zz->id; ?>"><?php echo $zz->zone_name; ?></option>
			<?php endforeach; ?>
		</select>

		<br />
		<br />
		<button class="form-control but1"  onclick="generate_collection_data()">Generate Collection Data</button>
	</form>
</div>


<hr />

<h2>Load Remote Collection</h2>
<div class="box11">
	<input type="hidden" name="_token"  id="q_token" value="{{ csrf_token() }}">
	<input id="json_file" type="file" accept="application/JSON" name="json_file" />
	<br />
	<input class="btn btn-success" type="button" value="Upload"  onclick="load_remote_collection()">
	
	<br />
	<br />
	<div style="text-align: right;padding-bottom:15px;">
		<button class="form-controlx btn btn-small" onclick="clear_uploaded_data()">CLEAR DATA</button>
	</div>
	
	<div class="files_wait">Please Wait...</div>		
</div>


<div class="my_load1">
	<div class="pwait1">PLEASE WAIT<br />... LOADING ...</div>
</div>


<style>
.h200{height:200px !important;}
.but1{width:200px !important;}

.my_load1{
	position:fixed;
	width:100%;
	height:100%;
	background:rgba(0,0,0,0.70);
	z-index:10;
	top:0;
	left:0;
	display:none;
}
.pwait1{
    font-size: 34px;
    text-align: center;
    padding-top: 150px;
    color: white;
}

.tab002{
	width:100%;
}
.tab002 td{
	border:1px solid #ccc;
	padding:5px;
}
.tab002 tr.head1{
	color:white;
	background:#108479;
}
.act1 small:hover{
	text-decoration:underline;
}
.act1 small{
	cursor:pointer;
}
</style>


<script>
jQuery(document).ready(function(){
		get_uploaded_file();
});

function load_collection_to_server($up_id)
{
	let confirm1 = confirm('Are you sure to load collection?');
	if(!confirm1){return false;}
	
	jQuery.get('/collections/load_collection_to_server/'+$up_id, function($dd){
		get_uploaded_file();
	});		
}

function delete_this_upload($up_id)
{
	let confirm1 = confirm('Are you sure to delete?');
	if(!confirm1){return false;}
		
	jQuery.get('/collections/delete_this_upload/'+$up_id, function($dd){
		get_uploaded_file();
	});	
}	

function generate_collection_data()
{
	let confirm1 = confirm('Are you sure to generate?');
	if(!confirm1){return false;}
	
	let my_collector = jQuery('.rem_cont .my_collector').val();
	let my_zones = jQuery('.rem_cont .my_zones').val();
	
	if(!my_collector){return false;}
	if(my_zones.length <= 0){return false;}
	
	return true;
	
}

function get_uploaded_file()
{
	jQuery.get('/collections/get_collection_uploaded_html1', function($dd){
		jQuery('.files_wait').html($dd.html1);
	});
}//


function clear_uploaded_data()
{
	let confirm1 = confirm('Are you sure to clear?');
	if(!confirm1){return false;}

	jQuery.get('/billing/clear_remote_temp_data', function($dd){
		alert('Remote data cleared.');
		window.location.reload();
	});


}//

function clear_temp_remote_collection_data()
{
	let confirm1 = confirm('Are you sure to clear?');
	if(!confirm1){return false;}

	jQuery.get('/billing/clear_temp_remote_collection_data', function($dd){
		alert('Remote data cleared.');
		window.location.reload();
	});


}//


async function load_remote_collection()
{
	let confirm1 = confirm('Are you sure to generate?');
	if(!confirm1){return false;}
	
	var file_data = document.getElementById('json_file').files[0];
    if(!file_data){return false;}	
    
    var q_token = jQuery("#q_token").val();
	
	var form_data = new FormData();
    form_data.append("file", file_data);
    form_data.append("_token", q_token);
    
    
    jQuery('.my_load1').show();
    
	await jQuery.ajax({
		method: 'post',
		processData: false,
		contentType: false,
		cache: false,
		data: form_data,
		enctype: 'multipart/form-data',
		url: '/collections/load_remote_collection_100010',
		success: function (response) {
			//~ jQuery('#json_file').val('');
			//~ get_def_data1();
			//~ alert('Test');
			get_uploaded_file();

		}
	}).promise();	    

    jQuery('.my_load1').hide();

	
}//
	
</script>
