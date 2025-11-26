<?php 

$URLXX = @$_GET;
$URLXX['pdf']=1;
$pdf_url = url()->current().'?'.http_build_query($URLXX);

//~ $req_date = date('Y-m', strtotime($r_year.'-'.$r_month));
//~ $curr_date = date('Y-m');

$req_date = date('Y-m', strtotime('2019-06'));
$curr_date = date('Y-m', strtotime('2019-06'));


//~ echo $r_year.'/'.$r_month;
//~ die();


?>


<div style="float:right;display:inline-block;color:red;font-weight: bold;">Current Date :  <?php  echo date('F d, Y');  ?></div>
<h2>Period :  <span class="rd"><?php echo date('F Y', strtotime($r_year.'-'.$r_month)); ?></span></h2>
@include('billings.inc.billing_reading.reading_inc.acct_filter1')




<!------>
<!------>

<div class="hide_print">
	
<!--
	<ul>
		<li><a href="<?php echo $reading11['path']; ?>?status=0">Total Accounts ( <?php echo number_format(@$acct_all); ?> )</a></li>
		<li><a href="<?php echo $reading11['path']; ?>?status=2">New Accounts ( <?php echo number_format(@$acct_stat11['acct_new']); ?> )</a></li>
		<li><a href="<?php echo $reading11['path']; ?>?status=11">Active Accounts  ( <?php echo number_format(@$acct_stat11['acct_active22']); ?> )</a></li>
		<li><a href="<?php echo $reading11['path']; ?>?status=3">Disconnected Account ( <?php echo number_format(@$acct_stat11['acct_dis']); ?> )</a></li>
	</ul>
	
	<ul>
		<li><a href="<?php echo $reading11['path']; ?>?status=7">Read Accounts Actice and Disconnected ( <?php echo number_format(@$acct_stat11['acct_read']); ?>  )</a></li>
		<li><a href="<?php echo $reading11['path']; ?>?status=8">Read Accounts Active Only( <?php echo number_format(@$acct_stat11['acct_read_active']); ?>  )</a></li>
		<li><a href="<?php echo $reading11['path']; ?>?status=9">Read Accounts Disconnected Only( <?php echo number_format(@$acct_stat11['acct_read_disconnected']); ?>  )</a></li>
	</ul>
	
	<ul>
		<li><a href="<?php echo $reading11['path']; ?>?status=10">Unread Active Account Accounts ( <?php echo number_format(@$acct_stat11['acct_unread_active']); ?>  )</a></li> 
		<li><a href="<?php echo $reading11['path']; ?>?status=5">Disconnected Accounts w/ consumption  ( <?php echo number_format(@$acct_stat11['acct_dis_read']); ?> )</a></li>
		<li><a href="<?php echo $reading11['path']; ?>?status=6">Active Accounts w/ zero consumption ( <?php echo number_format(@$acct_stat11['acct_active_zero']); ?> )</a></li>
		<li>Abnormal Readings</li>
	</ul>
	
	<ul>
		<li><a href="#">Billable Account ( <?php echo number_format(@$acct_stat11['billable_reading']); ?> )</a></li>
		<li><a href="#">Billed Accounts ( <?php echo number_format(@$acct_stat11['billed_accounts']); ?> )</a></li>
		<li><a href="#">Unbilled Accounts ( <?php echo number_format(@$acct_stat11['unbilled_accounts']); ?> )</a></li>
	</ul>
	
-->
	
	
	<?php /*if(@$_GET['status'] == 13){?>
		<button  onclick="execut_billing_10011(<?php echo @$zone; ?>)">Execute Billing Now</button>
	<?php }*/ ?>
	
	

</div>
<div style="text-align:right;">
<!--
	<a href="<?php echo $reading11['path']; ?>?status=0"><button>All (<?php echo $acct_stat11['acct_all']; ?>)</button></a>
	<a href="<?php echo $reading11['path']; ?>?status=2"><button>New Accounts (<?php echo $acct_stat11['acct_new']; ?>)</button></a>
	<a href="<?php echo $reading11['path']; ?>?status=3"><button>Disconnected (<?php echo $acct_stat11['acct_dis']; ?>)</button></a>
	<a href="<?php echo $reading11['path']; ?>?status=4"><button>For Reconnection (<?php echo $acct_stat11['acct_4rec']; ?>)</button></a>
-->
</div>


<h2><?php echo reading_label1(); ?></h2>
<h3><?php echo get_zone101(@$zone); ?></h3>

<div style="text-align:right;">
	<a href="<?php echo $pdf_url; ?>" target="_blank"><button>PRINT RESULT</button></a>
</div>


<table class="table10 table-bordered  table-hover"><tbody>

	<tr class="headings">
		<td width="3%">#.</td>
		<td width="5%">Account No.</td>
<!--
		<td width="5%">Meter No.</td>
-->
		<td width="10%">Name</td>
		<td width="20%">Complete Address</td>
		<td width="5%">Zone</td>
		<td width="5%">Previous Reading</td>
		<td width="5%">Current Reading</td>
		<td width="5%">C.U. <br /> Meter</td>
		<td width="5%">Status</td>
	</tr>

	<?php //for($x=0;$x<=30;$x++): ?>
	<?php if(empty($reading11['data'])): ?>
	<tr>
		<td colspan="9" style="text-align:center;padding:30px;">
				<span style="font-size:24px;">No Result</span>
		</td>
	</tr>
	<?php endif; ?>

	<?php
	
	//~ echo '<pre>';
	//~ print_r($acct_reading);
	//~ die();
	
	if(!empty($acct_reading))
	{
			//~ foreach($acct_reading['data'] as $kk => $vv)
			//~ {

				//~ if(!empty($vv['reading1']['init_reading'])){
					//~ $vv['reading1']['prev_reading'] = $vv['reading1']['init_reading'];
				//~ }else{
					//~ if(!empty($vv['reading_prev'])){
						//~ $vv['reading1']['prev_reading'] = $vv['reading_prev']['curr_reading'];
					//~ }
				//~ }

				//~ $acct_reading['data'][$kk] = $vv;
			//~ }
	}

	$index1 = 0;
	if(!empty($reading11))
	{
			
	//foreach($acct_reading['data'] as $acct1):	
	
	$tab_index = 1;
	
	foreach($reading11['data'] as $read11):
	
		$acct1 = $read11['account1'];
		
		
		$currR1     =  (int) @$read11['curr_reading'];
		$prevR1    =  (int) @$read11['prev_reading'];
		$consump =  (int) @$read11['current_consump'];
		//yell
		
		$row_color = '';
		if($consump<=0){
			$row_color = ' yell ';
		}
		
		$page11 = 0;
		if(!empty($_GET['page'])){
			$page11 = @$_GET['page'];
			$page11 = ($page11 * 200) - 200;
		}
		
		
		$period11 = date('Y-m-1',strtotime($r_year.'-'.$r_month));
		$over_average = ave_reading($acct1['id'], $period11);
		
		$color_abnormal = '';
		if($consump  >= $over_average){
			$color_abnormal = ' abnormal_reading ';
		}
		

	?>
	<!------>
	<!---tabindex="<?php echo $index1; ?>"--->
	<tr   data-index="<?php echo $index1; ?>" data-box1="" class="cursor1  rowx<?php  echo $index1.$row_color.$color_abnormal;  ?>  ">
		<td><?php echo ($index1 +1 +$page11)	; ?></td>
		<td onclick="view_reading_info(<?php echo $index1; ?>)" ><?php echo $read11['account_number']; ?></td>
<!--
		<td><?php echo $read11['meter_number'];?></td>
-->
		<td><?php echo $acct1['lname']; ?>, <?php echo $acct1['fname']; ?> <?php echo $acct1['mi']; ?></td>
		<td><?php echo $acct1['address1']; ?></td>
		<td><?php echo @$zone_label[@$acct1['zone_id']]; ?></td>
		 <td style="text-align:right;"   class="prev_read_el">
			 
			<?php
			//$r_year, $r_month
			//~ $req_date = date('Y-m', strtotime($r_year.'-'.$r_month));
			//~ $curr_date = date('Y-m');

				if($req_date == $curr_date): ?>
					<input type="text" 
						class="inv_text_f prev_read_el  prev_val_<?php echo $index1; ?>  kk11"   
						placeholder=""  value="<?php echo @$read11['prev_reading']; ?>"  
						onchange="update_me11_prev(<?php echo $index1; ?>, this)" 
						onfocusin="focos_me(this)"
						tabindex="<?php echo $tab_index; ?>"
					 />
			<?php else: ?>
					<span class="rd  curr_read_el2"><?php 
						echo @$read11['prev_reading']?$read11['prev_reading']:''; 
					?></span>
			<?php endif; ?>
			 
			 
			 
			 <?php
					
					//echo @$read11['prev_reading'];
					
					// echo $acct1['reading1']['prev_reading']?$acct1['reading1']['prev_reading']:'----';
					// echo $acct1['reading1']['prev_reading']?$acct1['reading1']['prev_reading']:'----';
					
					/*
					if(empty($acct1['init_reading'])){
						echo $acct1['prev_reading']?$acct1['prev_reading']:'----';
					}else{
						echo $acct1['init_reading']?$acct1['init_reading']:'----';
					}
					*/
					
		 ?></td>
		<td  style="text-align:right;">
			<?php
			//$r_year, $r_month
			
			//~ $req_date = date('Y-m', strtotime($r_year.'-'.$r_month));
			//~ $curr_date = date('Y-m');
			
			$tab_index++;

					if($req_date == $curr_date): ?>
						<input type="text" 
							class="inv_text_f curr_read_el  curr_val_<?php echo $index1; ?>  kk11"   
							placeholder="----"  value="<?php echo @$read11['curr_reading']; ?>"  
							onchange="update_me11(<?php echo $index1; ?>, this)" 
							onfocusin="focos_me(this)"
							tabindex="<?php echo $tab_index; ?>"
						 />
			<?php else: ?>
						<span class="rd  curr_read_el2"><?php 
										echo @$read11['curr_reading']?$read11['curr_reading']:'----'; 
						?></span>
			<?php endif; ?>
		</td>
		<?php
		/*
		 <td><?php echo $acct1['prev_reading']?$acct1['prev_reading']:'----'; ?></td>
		<td><?php echo $acct1['curr_reading']?$acct1['curr_reading']:'----'; ?></td>
		*/ ?>
		<td>
			<span class="consump">
			<?php

				
				echo $consump;
				
				//~ if($consump  == 0)
				//~ {
				//~ }
				
				//~ if($currR1 <= 0  || $prevR1<=0){
					//~ echo  '----';
				//~ }else{
					//~ $total_reading  = ( $currR1 -  $prevR1);
					//~ echo $total_reading <= 0?'----': $total_reading;
				//~ }

			?>
			</span>
		</td>
		<td><?php echo acct_status($acct1['acct_status_key']); ?></td>
	</tr>
	<!------>
	<!------>
	<?php $index1++;
	$tab_index++;
	 endforeach;
	
}
	 ?>

</tbody></table>

<br />
<br />

<div style="padding:30px;"  class="hide_print">
	<?php   if(!empty($reading11['prev_page_url'])): 
	
		$new_get_prev = @$_GET;

		@$new_get_prev['page']--;
		$new_get = array();
		foreach(@$new_get_prev as $kk=>$vv){
			$new_get[] = $kk.'='.urlencode($vv);
		}
		
		$new_get = implode('&', $new_get);
		$reading11['prev_page_url'] = explode('?', $reading11['prev_page_url']);
		$reading11['prev_page_url'] = $reading11['prev_page_url'][0];	
	
	?>
	<a href="<?php echo $reading11['prev_page_url'].'?'.@$new_get; ?>#accounts"><button>&lsaquo; Prev</button></a>
		&nbsp;&nbsp;&nbsp;
	<?php  endif; ?>
	<?php echo @$reading11['current_page'] ?> of <?php echo @$reading11['last_page'] ?>
	&nbsp;&nbsp;&nbsp;
	<?php  if(!empty($reading11['next_page_url'])):
	
		$new_get_next = @$_GET;
		if(empty($new_get_next['page'])){
			$new_get_next['page'] = 1;
		}
		@$new_get_next['page']++;
		
		$new_get = array();
		foreach(@$new_get_next as $kk=>$vv){
			$new_get[] = $kk.'='.urlencode($vv);
		}
		
		$new_get = implode('&', $new_get);
		$reading11['next_page_url'] = explode('?', $reading11['next_page_url']);
		$reading11['next_page_url'] = $reading11['next_page_url'][0];
	
	 ?>
		<a href="<?php echo $reading11['next_page_url'].'?'.@$new_get; ?>#accounts"><button>Next &rsaquo;</button></a>
	<?php  endif; ?>
</div>

<?php
/*
<div style="padding:15px;">
	<ul class="pagination pagination-sm">
	  <li><a href="#">PREVIOUS</a></li>
	  <li><a href="#">1</a></li>
	  <li><a href="#">2</a></li>
	  <li><a href="#">3</a></li>
	  <li><a href="#">4</a></li>
	  <li><a href="#">5</a></li>
	  <li><a href="#">NEXT</a></li>
	</ul>
</div>
*/
?>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="view_reading_acct01" style="display:none;">

	<div class="pop_view_info_table  view_acct_info_pop">

		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>

		<br />

		<ul class="item_list1">
			<li>Account Number   <span  class="field3"></span></li>
			<li>Metter Number   <span  class="field4"></span></li>
			<li>Zone   <span  class="field6"></span></li>
			<li>Previous Reading   <span class="prev_reading"></span></li>
			<li>Currrent Reading   <span class="curr_reading rd"></span></li>
			<li>Consumption(cu. meter)    <span  class="consump_pop"></span></li>
			<?php
			/*<li>Account Type   <span  class="field7"></span></li>
			<li>Account Status   <span  class="field8"></span></li>
			*/ ?>
		</ul>

		<br />
		<br />
		<br />

		<div style="text-align:center;"  class="read_butt1">
<!--
			<button  onclick="add_initial_reading_act()">Add  Initial Reading !</button>
			<button  onclick="add_meter_reading_form()">Add Meter Number</button>
-->
			<button  onclick="got_billing1()">Go to Billing</button>
<!--
			<button  onclick="reconnect_line()">Reconnect Line</button>
			<button  onclick="disconnect_line()">Disconnect Line</button>
-->
<!--
			<button  onclick="goto_ledger()">Ledger</button>
-->
			<button  onclick="goto_account()">Account</button>
			<button  onclick="re_bill_account()">Re-Bill</button>
			<br />
			<br />
			<button  onclick="pop_close()">Close</button>
		</div>

	</div>
</div>
<!------------------------------------>
<!------------------------------------>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_initial_reading" style="display:none;">

	<div class="pop_view_info_table  view_acct_info_pop">

		<div class="head_info">
			<h2 class="field1"></h2>
			<p class="field2"></p>
		</div>

		<br />
		<ul class="item_list1">
			<li>Account Number   <span  class="field3"></span></li>
			<li>Metter Number   <span  class="field4"></span></li>
			<li>Zone   <span  class="field6"></span></li>
		</ul>

		<br />
		<br />
		<h3>Initial Reading: </h3>
		<input type="text"  value=""   placeholder="----"  class="init_reading_txt" />

		<div style="text-align:center;">
			<button  onclick="save_initial_reading()">Save</button>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<button  onclick="pop_close()">Cancel</button>
		</div>

	</div>

</div>
<!------------------------------------>
<!------------------------------------>

<!--------------------------------- --->
<!--------------------------------- --->
<div class="add_meter_number" style="display:none;">

	<div style="padding:20px;">
		<form action="/billing/reading/add_meter_number_act1" method="POST" class="form-style-9  xx_11">

			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
			<input type="hidden" name="acct_id" value=""  id="acct_id">

			<div class="head_info">
				<h2 class="field1"></h2>
				<p class="field2"></p>
			</div>

			<br />
			<ul class="item_list1">
				<li>Account Number   <span  class="field3"></span></li>
				<li>Zone   <span  class="field6"></span></li>
			</ul>

			<h3>Meter Number: </h3>
			<input type="text"  value=""   placeholder="----"  class="init_reading_txt"  name="meter_number" />

			<div style="text-align:center;">
				<button>Save</button>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<button  onclick="pop_close()"  type="button">Cancel</button>
			</div>


		</form>
	</div>

</div>
<!------------------------------------>
<!------------------------------------>
<style>
.yell{
	background:#FFFF00 !important;
}	
.abnormal_reading{
	background:rgba(231, 31,223,0.3);
}

@media print {
	.tab1x_v2,
	.filter_1,
	.hide_print,
    .main_left
    {
		display:none !important;
	}
}
.foc1.foc1.foc1{
	background:#C2F0FF !important;
}
.read_butt1 button{
	margin-bottom:15px;
}
</style>



<?php 

$per11 = date('Y-m-01',strtotime($r_year.'-'.$r_month));

?>

<script>
	
function goto_ledger(){
	
	let acct_no = current_reading.account_number;
	//window.location = '/billing/account_ledger/filter/'+acct_no+'/none/none/none/none/#account_list';
	//~ window.open('/billing/account_ledger/filter/'+acct_no+'/none/none/none/none/#account_list');
	window.location = '/billing/account_ledger/filter/'+acct_no+'/none/none/none/none/#account_list';
	
}

function goto_account(){
	let acct_no = current_reading.account_number;
	window.location ='/billing/accounts/'+acct_no+'/none/none/none/none/none/#account_list';
}

function disconnect_line()
{
	let conf1 = confirm('Are you sure to disconnect?');
	if(!conf1){return;}
	let acct_no = current_reading.account_number;
	let acct_id = current_reading.account_id;
	
	//~ console.log(current_reading.account_id);
	
	let nurl = '/billing/accounts/disconnect_account/'+acct_id+'/'+acct_no;
	window.location = nurl;	
}

function reconnect_line(){
	let conf1 = confirm('Are you sure to disconnect?');
	if(!conf1){return;}
	let acct_no = current_reading.account_number;
	let acct_id = current_reading.account_id;
	let nurl = '/billing/accounts/reconnect_account/'+acct_id+'/'+acct_no;
	window.location = nurl;	
}


function execut_billing_10011($zone_id)
{
	//~ let conf1 = confirm('Please confirm..');
	//~ if(!conf1){return;}
	//~ let nurl = '/billing/reading/execut_billing_10011/'+$zone_id+'/<?php echo $per11; ?>';
	//~ window.location = nurl;	
}


var $curr_index = 1;
var $max_index = <?php echo $tab_index; ?>;

var $pre_val = 0;


var $tt = null;

function focos_me($th)
{

	jQuery('.foc1').removeClass('foc1');
	jQuery($th).parent().parent().addClass('foc1');
	$curr_index = jQuery($th).attr('tabindex');
	$pre_val = jQuery($th).val();

	let vv = $th;
	try{clearTimeout($tt);}catch(e){}
	$tt = setTimeout(function(){
		jQuery(vv).select();
	},100);
	
	
}



function checkKey_V1(e) 
{
	//~ e = e || window.event;

    if (e.which == '38') {
        // up arrow
        if($curr_index <= 1){
			$curr_index = 1;
			return;
		}
		
        $curr_index = $curr_index - 2;

    }
    else if (e.which == '40') {
        // down arrow
        if($curr_index >= $max_index){
			$curr_index = $max_index;
			return;
		}
				
        $curr_index = parseInt($curr_index) + 2;
        
    }
    else if (e.which == '37') {
       // left arrow
        $curr_index = parseInt($curr_index) - 1;
    }
    else if (e.which == '39') {
       // right arrow
        $curr_index = parseInt($curr_index) + 1;
    }
    else if (e.which == '13') {
        $curr_index = parseInt($curr_index) + 2;
       // right arrow
      //~ jQuery('[tabindex=' + $curr_index + ']').focus();
    }else if (e.which == '8') {
		//DELETE
	}else if (e.which == '27') {
		//ESC
		//~ jQuery('[tabindex=' + $curr_index + ']').val($pre_val);
	}
    
    //~ alert(e.which);
    
	jQuery('[tabindex=' + $curr_index + ']').focus();
	
	
	
}//

jQuery(document).ready(function(){
	jQuery('.kk11').keydown(function( event ) {
	   checkKey_V1(event);
	});
	
	//~ jQuery('.kk11').on('focusin',function() {
		//~ let vv = this;
		//~ setTimeout(function(){
			//~ jQuery(vv).select();
			//~ console.log(vv);
		//~ },100);
	//~ });

	
});

//~ document.onkeydown = checkKey_V1;

function re_bill_account()
{
	let conf1 = confirm('Are you sure to execute billing');
	if(!conf1){return;}
	let acct_no = current_reading.account_number;
	let acct_id = current_reading.account_id;
	let reading_id = current_reading.id;
	
	let nurl = '/billing/accounts/rebill_from_reading/'+reading_id+'/'+acct_id+'/'+acct_no;
	//~ window.open(nurl, 'blank');
	//~ window.location = nurl;	
	jQuery.get(nurl,function( data ) {

		if(data.status == 0){
			alert(data.msg);
		}else{
			alert(data.msg);
			//~ location.reload();
		}
		
	});		
	
}

function got_billing1()
{
	let acct_no = current_reading.account_number;
	
	//billing/billing/2019/09/filter/111200010/none/none/all/
	window.location = '/billing/billing/<?php echo  $r_year."/".$r_month; ?>/filter/'+acct_no+'/none/none/all/';
}


	
</script>
