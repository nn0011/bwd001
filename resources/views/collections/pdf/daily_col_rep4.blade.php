<?php


$user_pub  = Auth::user();
$user_name =  $user_pub->name;
//~ echo '<pre>';
//~ print_r($user_pub->toArray());
//~ die();



ob_start();
$npp = 1000;
$pn  = 1;


$cash_only  = 0;
$check_only = 0;
$ada_only   = 0;
$wtax_only  = 0;


//~ echo '<pre>';
//~ print_r($coll1);
//~ die();

foreach($coll1 as $cc1){
	
	if(
		$cc1->status == 'active' || 
		$cc1->status == 'collector_receipt'||
		$cc1->status == 'or_nw' || 
		$cc1->status == 'cr_nw'		
		
	){ 	
	
		if($cc1->pay_type == 'cash'){
			$cash_only += $cc1->payment;
		}
		
		if($cc1->pay_type == 'check'){
			$check_only += $cc1->payment;
		}
		
		if($cc1->pay_type == 'ada'){
			$ada_only += $cc1->payment;
		}
		
		if($cc1->pay_type == 'both'){
			$check_only += $cc1->chk_full;
			$cash_only += $cc1->payment - $cc1->chk_full;
		}

		if($cc1->tax_val > 0 ){
			$wtax_only += $cc1->tax_val;
		} 		
	
	}//
	

	
}//


//~ die();


$waterbill     = 0;
$non_waterbill = 0;

$ttl_recieved   = 0;	
$ttl_current    = 0;
$ttl_arrear_cy  = 0;
$ttl_arrear_py  = 0;
$ttl_penalty    = 0;
$ttl_amt    	= 0;
$ttl_adance_pay = 0;


$xxx = 0;

$c1 = true;

for(;;):

	if(empty($c1)){
		break;
	}

?>

<div class="page_box1">

<div class="hide_me1 mm<?php echo $pn; ?>">
	<p class="hide_me2 zz<?php echo $pn; ?>">
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
	</p>

	<strong>Daily Collection Report - Summary</strong>
	<br />
	
	<em><strong><?php echo date('l, F d, Y',strtotime(@$date1));  ?></strong></em>
	
</div>


<table class="tab11">
	
	<tr class="bord_bot hide_me1  nn<?php echo $pn; ?>">
		<td style="width:80px;">OR #</td>
		<td style="width:200px;">NAME</td>
		<td class="rr">COLLECTED</td>
		<td class="rr">CURRENT</td>
		<td class="rr">ARREAR(C.Y.)</td>
		<td class="rr">ARREAR(P.Y.)</td>
		<td class="rr">PENALTY</td>
		<td class="rr">ADV.Pay.</td>
		<td>AMOUNT</td>
	</tr>

<?php 





//~ foreach($coll1 as $c1): 
for($xx=0;$xx<=$npp;$xx++):


$c1 = @$coll1[$xxx];

if(empty($c1)){break;}

$xxx++;




?>

<?php 



$c1 = (object) $c1;
$c1->accounts = (object) $c1->accounts;



?>	
	
	<?php 
	
			if(
				$c1->status == 'active' || 
				$c1->status == 'collector_receipt'
			): 
			
			
			
			
?>
<?php  

	$info77 = @$c1->info77;
	
	$A1 = '';
	$A2 = '';
	$A3 = '';
	$A4 = '';
	$A4_2 = '';
	$A5 = '';
	$A6 = '';


	$py_year1 = AAA_PyArrearCollection($c1);
	
	if($c1->invoice_num == 3109){
		//~ echo '<pre>';
		//~ print_r($info77);
		//~ die();
	}
	
	if($py_year1 > 0){
		@$info77['ar'] = @$info77['ar'] - $py_year1;
		$A3 = number_format($py_year1,2);
	}
	
	if(@$info77['ar'] > 0){
		$A2 = number_format(@$info77['ar'],2);
	}

	if(@$info77['bi'] > 0)
	{
		
	    $bongut1 = @$info77['ar']+@$info77['pe']+@$info77['bi']+(-1*@$info77['ap']);		

		/**
		if($c1->invoice_num == 3558){
			echo '<pre>';
			print_r($bongut1);
			echo '<br />';
			print_r($c1->payment);
			print_r($info77);
			//~ die();
		}
		/**/ 
		
		$aaa = round((float) $bongut1,2);
		$bbb = round((float) $c1->payment,2);
		
		
		if($aaa == $bbb){
			$A1 = number_format(@$info77['bi'],2);
		}else{
			@$info77['bi'] = @$info77['bi'] + @$info77['ap'];
		}
		
		$A1 = number_format(@$info77['bi'], 2);
	}

	if(@$info77['pe'] > 0){
		$A4 = number_format(@$info77['pe'],2);
		$ttl_penalty   += @$info77['pe'];
	}
	
	if(@$info77['pe'] > 0)
	{
		$new_A2 = @$info77['bi'] + @$info77['ar'];
		//~ $new_A2 = @$info77['bi'] ;
		//~ $A3 = $A2;
		
		$A2 = number_format($new_A2,2);
		$A1 = '';

		//~ $ttl_arrear_py += @$info77['ar'];
		$ttl_arrear_cy += @$info77['bi'];
		$ttl_arrear_cy += @$info77['ar'];
	}else{
		$ttl_current   += @$info77['bi'];
		$ttl_arrear_cy += @$info77['ar'];
	}
	
	
	if(@$info77['ap'] != 0){
		$A4_2 = number_format((@$info77['ap'] * -1 ), 2);
		$ttl_adance_pay+=(@$info77['ap'] * -1);
		//~ $A1 = number_format(@$info77['bi'],2);
	}
	
	$ttl_arrear_py += $py_year1;
	
	
	if(empty(@$info77)  && $c1->collection_type == 'bill_payment')
	{
		//~ $A2 = number_format($c1->payment,2);
		//~ $ttl_arrear_py += $c1->payment;
		$ttl_arrear_cy += $c1->payment;
	}
	
	$ttl_recieved += round((float) $c1->payment,2);
	$waterbill    += round((float) $c1->payment,2);
	
	
	$my_or111 = $c1->invoice_num;
	$my_or111 = sprintf("%07d",$my_or111);
	
	
?>			
		<tr>
			<td>OR-<?php echo $my_or111; ?></td>
			<td><?php echo substr($c1->accounts->acct_no.' - '.$c1->accounts->lname.', '.$c1->accounts->fname,0,30); ?></td>
			<td class="rr"><?php echo number_format($c1->payment,2); ?></td> <!-- Collected -->
			<td class="rr"><?php echo $A1; ?></td><!-- Current -->
			<td class="rr"><?php echo $A2.''; ?></td><!-- ARREAR(C.Y.)-->
			<td class="rr"><?php echo $A3; ?></td><!-- ARREAR(P.Y.) -->
			<td class="rr"><?php echo $A4; ?></td><!-- Penalty -->
			<td class="rr"><?php echo $A4_2; ?></td><!-- Advance payment -->
			<td class="rr"><?php echo $A5; ?></td><!-- Amount -->
		</tr>
	<?php endif; ?>
	
	
	

	<?php if(
				$c1->status == 'cancel_cr' || 
				$c1->status == 'cancel_receipt'
			): 
			
			
	$my_or111 = $c1->invoice_num;
	$my_or111 = sprintf("%07d",$my_or111);
				
			?>
		<tr>
			<td>OR-<?php echo $my_or111; ?></td>
			<td colspan="8">CANCELLED</td>
		</tr>
	<?php endif; ?>
	
	
<?php 

		if(
			$c1->status == 'or_nw' || 
			$c1->status == 'cr_nw'
		): 
		
		$ttl_recieved  += round((float) $c1->payment,2);
		$ttl_amt       += round((float) $c1->payment,2);
		
		$non_waterbill += round((float) $c1->payment,2);
		
		
?>
<?php 

	$my_or111 = $c1->invoice_num;
	$my_or111 = sprintf("%07d",$my_or111);

	
?>			

		<tr>
			<td>OR-<?php echo $my_or111; ?></td>
			<td><?php echo substr($c1->accounts->acct_no.'-'.$c1->accounts->lname.', '.$c1->accounts->fname,0,25); ?></td>
			<td class="rr"><?php echo number_format($c1->payment,2); ?></td>
			<td class="rr"></td>
			<td class="rr"></td>
			<td class="ll" colspan="2">
				GL : <?php echo strtoupper($c1->nw_glsl); ?>
				<br />
				<?php echo strtoupper($c1->nw_desc); ?>
			</td>
			<td class="rr"></td>
			<td class="rr"><?php echo number_format($c1->payment,2); ?></td>
		</tr>


	<?php endif; ?>
			
				
	<?php if(
				$c1->status == 'nw_cancel' ||
				$c1->status == 'cancel_cr_nw'
			): 
			

	$my_or111 = $c1->invoice_num;
	$my_or111 = sprintf("%07d",$my_or111);

			
			?>
		<tr>
			<td>OR-<?php echo $my_or111; ?></td>
			<td colspan="7">CANCELLED</td>
		</tr>
	<?php endif; ?>
	

	<?php if(
				$c1->tax_val > 0 
			): ?>

		<tr>
			<td colspan="6">&nbsp;</td>
			<td colspan="1">W/TAX</td>
			<td class="rr">( <?php echo number_format($c1->tax_val,2); ?> )</td>
		</tr>
			
				
	<?php 
	
	//~ $wtax_only += $c1->tax_val;
	
	
	$xx++;
	
	endif; ?>
	
	

	<?php
	if(($c1->pay_type == 'check' || $c1->pay_type == 'both') &&  ($c1->status == 'active'))
	{
		
		//~ $check_only += $c1->payment;

	?>
	
		<tr>
			<td colspan="6">&nbsp;</td>
			<td colspan="3">
					<?php echo 'CHECK #'.$c1->check_no; ?>
					<?php if($c1->pay_type == 'both'): ?>
						<br /> Both Check + Amount  <?php echo number_format($c1->amt_rec - $c1->chk_full,2) ; ?>
					<?php endif; ?>
			</td>
		</tr>

	<?php
		//~ $item_count++;
		
		$xx++;
		
	}
	?>	

	<?php
	
	if(($c1->pay_type == 'ada') &&  ($c1->status == 'active'))
	{
		//~ $ada_only += $c1->payment;
		

	?>
	
		<tr>
			<td colspan="6">&nbsp;</td>
			<td colspan="3"><?php echo 'ADA - REFF #'.$c1->check_no; ?></td>
		</tr>

	<?php
		//~ $item_count++;
		
		$xx++;
		
	}
	
	?>
	
	
	<?php  
	
	//~ if(($c1->pay_type == 'cash') &&  ($c1->status == 'active' || $c1->status == 'collector_receipt'))
	//~ {
		//~ $cash_only += $c1->payment;
	//~ }
	
	//~ if($c1->status == 'or_nw' || $c1->status == 'cr_nw')
	//~ {
		//~ $cash_only += $c1->payment;
	//~ } 	
	
	
	?>	
	
	
	
	
	<?php 
	
	//~ endforeach; 
	endfor;
	?>


<?php 
/*
	<tr class="under11">
		<td colspan="2">TOTAL TRANSACTION  :  <?php echo count($coll1); ?></td>
		<td class="rr"><?php  echo number_format($ttl_recieved,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_current,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_arrear_cy,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_arrear_py,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_penalty,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_amt,2); ?></td>
	</tr>
*/ ?>
	
</table>

<!--
	<span class="page-number-tt  hide_me1">Pg. <?php echo $pn; ?></span>
-->

</div>


<?php if(empty($c1)){break;} ?>

<!--
<div class="page_break"></div>
-->



<?php 

$pn++;
endfor; 

?>


<table class="tab11">
	<tr class="under11">
		<td colspan="2">TOTAL TRANSACTION  :  <?php echo count($coll1); ?></td>
		<td class="rr"><?php  echo number_format($ttl_recieved,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_current,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_arrear_cy,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_arrear_py,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_penalty,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_adance_pay,2); ?></td>
		<td class="rr"><?php  echo number_format($ttl_amt,2); ?></td>
	</tr>
</table>

<br />
<br />


<b>SUMMARY</b>
<br />
<br />


<table cellpadding="0" cellspacing="0" class="cc1">
	<tr>
		<td>
			<!--- --->
				<table cellpadding="0" cellspacing="0" class="w200">
					<tr>
						<td>CASH</td>
						<td class="rr"><?php echo number_format(@$cash_only,2); ?></td>
					</tr>

					<tr>
						<td>CHECK</td>
						<td class="rr"><?php echo number_format(@$check_only,2); ?></td>
					</tr>

					<tr>
						<td>ADA</td>
						<td class="rr"><?php echo number_format(@$ada_only,2); ?></td>
					</tr>
					
					<tr>
						<td>W/TAX</td>
						<td class="rr">( <?php echo number_format(@$wtax_only,2); ?> )</td>
					</tr>					
					

					<tr class="bord_top">
						<td>TOTAL</td>
						<td class="rr"><?php echo number_format((@$cash_only + @$check_only + @$ada_only),2); ?></td>
					</tr>

				</table>

			<!--- --->

		</td>
		<td>
			<!--- --->

				<table cellpadding="0" cellspacing="0" class="w200">
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>					

					<tr>
						<td>WATER BILL</td>
						<td class="rr"><?php echo number_format(@$waterbill,2); ?></td>
					</tr>
					<tr>
						<td>NON-WATER BILL</td>
						<td class="rr"><?php echo number_format(@$non_waterbill,2); ?></td>
					</tr>

					<tr class="bord_top">
						<td>TOTAL</td>
						<td class="rr"><?php echo number_format((@$waterbill + @$non_waterbill),2); ?></td>
					</tr>

				</table>
			<!--- --->
		</td>
	</tr>
</table>




<br />
<br />

<table style="width:800px;">
	<tr>
		<td class="cc">
			<span class="bl1">Prepared By :</span>
			<br />
			<br />
			<br />
			<div style="display:inline-block;padding-left:50px;padding-right:50px;border-bottom:1px solid #000;"><?php echo strtoupper($user_name); ?></div>
			<br />
			Teller
			<?php //echo REP_SIGN1_TITLE; ?>
		</td>
		<td class="cc">
			<span class="bl1">Checked by:</span>
			<br />
			<br />
			<br />
			<div style="display:inline-block;padding-left:50px;padding-right:50px;border-bottom:1px solid #000;"><?php echo REP_SIGN1; ?></div>
			<br />
			<?php echo REP_SIGN1_TITLE; ?>
		</td>

		<td class="cc">
			<span class="bl1">Noted by:</span>
			<br />
			<br />
			<br />
			<div style="display:inline-block;padding-left:50px;padding-right:50px;border-bottom:1px solid #000;"><?php echo WD_MANAGER; ?></div>
			<br />
			<?php echo WD_MANAGER_RA; ?>
		</td>

	</tr>
</table>

<br />
<br />


<style>
*{
	font-family:'sans-serif';
	font-size:14px;
}
.cc1 td{
	padding-right:30px;
}

.page_box1{
	position:relative;
}
.page-number-tt{
	position:absolute;
	top:0;
	right:0;
}

.hide_me1{
	visibility:hidden;
}
.hide_me1.mm1{
	visibility:visible;
    position: static;	
}
.hide_me1.nn1{
	visibility:visible;
    position: static;	
}


.hide_me2{
	display:none;
}

.hide_me2.zz1{
	display:block;
}


.bl1{
	
}

table.tab11{
	width:1084px;
}

table *{}

table td{
	border-bottom:0px solid #ccc;
	padding:0px;
}
.bord_all td{
	border:1px solid #ccc;
}
.bord_bot td{
	border-bottom:1px solid #ccc;
}
.bord_top td{
	border-top:1px solid #ccc;
}
.ll{
	text-align:left;
}
.rr{
	text-align:right;
	padding-right:15px;
}
.cc{
	text-align:center;

}

.under ,
.trun
td{
	border-bottom:1px solid #ccc;
}



.rh1{
	text-align:right;
}
.bld_me{font-weight:bold;}
.page-number{
		position:fixed;
		left:0;
		bottom:10;
}
.page-number:after {
		content: counter(page);
 }

.page_break { 
	page-break-before: always; 
}

.page_break:last-child{
	display:none !important;
}

.page-number-x{
		position:absolute;
		left:0;
		bottom:0;
}
.page-number-c{
		position:fixed;
		left:0;
		bottom:10px;
}
.page-number{
		position:absolute;
		right:0;
		top:10;
}
.page-number:after {
		content: counter(page);
		left:25;
		position:absolute;
 }
 
.w200{width:300px;}
.sub_ttl1 td{
	border:1px solid #ccc;
	padding:3px;
	text-align:right;
	padding-right: 20px;
}

.under11 td{border-top:1px solid #ccc;}

@media  print {
	
	.hide_me1{
		visibility:visible !important;
	}
	
	
}
</style>

<?php

//~ $cont_ttl = ob_get_contents();
//~ ob_get_clean();

//~ $cont_ttl = str_replace('--ttl_p--', $pn, $cont_ttl);

//~ echo $cont_ttl;

// die();
?>
