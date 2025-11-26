<?php

ob_start();
$npp = 55;
$pn  = 1;

//~ $coll1 = $new_coll[14];


$coll1 = array();




$grand_recieved   = 0;	
$grand_current    = 0;
$grand_arrear_cy  = 0;
$grand_arrear_py  = 0;
$grand_penalty    = 0;
$grand_amt     	  = 0;



?>


<?php 





$xxx = 0;

$c1 = true;


$max_line11 = 55;
$line11 = 5;



?>

<p class="hide_me2 zz<?php echo $pn; ?>">
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
</p>


<?php function header_uu11($date1){ ?>
	
	
<strong>Daily Collection Report</strong>
<br />
<em><strong><?php echo date('l, F d, Y',strtotime(@$date1));  ?></strong></em>
<br />

<div class="zone_box">
	<div class="clasi_box">
		<div class="item_box">
			
			<table class="tab11">
				<tr class="bord_bot bold1">
					<td class="or4">OR #</td>
					<td class="nam4">NAME</td>
					<td class="col4 rr">COLLECTED</td>
					<td class="cur4 rr">CURRENT</td>
					<td class="cy4 rr">ARREAR(C.Y.)</td>
					<td class="py4 rr">ARREAR(P.Y.)</td>
					<td class="pen4 rr">PENALTY</td>
					<td class="amt4">AMOUNT</td>
				</tr>
			</table>


		
		</div>
	</div>
</div>
<?php }//
header_uu11($date1);
?>


<?php 

ksort($new_coll);

foreach($new_coll as $kk => $vv):




	
	$myz = $my_zones[$kk];
	
	$class1 = $vv;
	
?>

<?php 

	if($line11 >= $max_line11)
	{
		echo '<div class="page_break"></div>';
		header_uu11($date1);
		$line11 = 2;
	}

?>	



<div class="zone_box">
	



	

	<strong><?php echo $myz->zone_name; ?></strong>
	

<?php 
$line11 += 2 ; 


$class_recieved   = 0;	
$class_current    = 0;
$class_arrear_cy  = 0;
$class_arrear_py  = 0;
$class_penalty    = 0;
$class_amt     	  = 0;


	foreach($class1 as $c_k => $c_v):
		$my_classi = @$acct_meta1[$c_k];
		
		$list_items = $c_v;

?>


<?php 

	if($line11 >= $max_line11)
	{
		echo '</div>';
		echo '<div class="page_break"></div>';
		header_uu11($date1);
		
		echo '
		
				<div class="zone_box">
					<strong>'.$myz->zone_name.'</strong>
					
		';
		
		$line11 = 2;
	}

?>	


		<div class="clasi_box">
			
			<?php 
			
			$clasi_name = @$my_classi->meta_name;
			
			if($c_k == 'sen'): 
				$clasi_name = 'SENIOR';
			endif; ?>
			
			
			<strong><?php echo $clasi_name; ?></strong>
			<br />
			
			
			<div class="item_box">
				<table class="tab11">
					
				<?php 
				
$line11 += 2 ; 


$ttl_recieved   = 0;	
$ttl_current    = 0;
$ttl_arrear_cy  = 0;
$ttl_arrear_py  = 0;
$ttl_penalty    = 0;
$ttl_amt    	= 0;
					
				
				foreach($list_items as $ll1):
					
					$c1 = (object) $ll1;
					$c1->accounts = (object) $c1->accounts;	

$line11 += 1 ; 


				?>
				
				
<?php 

/*********************/
			if(
				$c1->status == 'active' || 
				$c1->status == 'collector_receipt'
			): 
			
			$ttl_recieved += $c1->payment;
			
			
	$info77 = $c1->info77;
	
	$A1 = '';
	$A2 = '';
	$A3 = '';
	$A4 = '';
	$A5 = '';

	if(@$info77['ar'] > 0){
		$A2 = number_format(@$info77['ar'],2);
	}

	if(@$info77['bi'] > 0){
		$A1 = number_format(@$info77['bi'],2);
	}

	if(@$info77['pe'] > 0){
		$A4 = number_format(@$info77['pe'],2);
		$ttl_penalty   += @$info77['pe'];
		
	}
	
	if(@$info77['pe'] > 0){
		$A3 = $A2;
		$A2 = $A1;
		$A1 = '';

		$ttl_arrear_py += @$info77['ar'];
		$ttl_arrear_cy += @$info77['bi'];
	}else{
		$ttl_current   += @$info77['bi'];
		$ttl_arrear_cy += @$info77['ar'];
	}



	
	if(empty(@$info77)  && $c1->collection_type == 'bill_payment')
	{
		$A3 = number_format($c1->payment,2);
		
		$ttl_arrear_py += $c1->payment;
	}	
	
			
			
			
?>				
					<tr>
						<td class="or4">OR-<?php echo $c1->invoice_num; ?></td>
						<td class="nam4"><?php echo $c1->accounts->acct_no; ?> - <?php echo $c1->accounts->lname.', '.$c1->accounts->fname; ?></td>
						<td class="col4 rr"><?php echo number_format($c1->payment,2); ?></td>
						<td class="cur4 rr"><?php echo $A1; ?></td>
						<td class="cy4  rr"><?php echo $A2; ?></td>
						<td class="py4  rr"><?php echo $A3; ?></td>
						<td class="pen4 rr"><?php echo $A4; ?></td>
						<td class="amt4 rr"><?php echo $A5; ?></td>
					</tr>
<?php
			endif;
/*********************/
?>

<?php 		

/*********************/
			if(
				$c1->status == 'cancel_cr' || 
				$c1->status == 'cancel_receipt'
			): 
?>

				<tr>
					<td  class="or4">OR-<?php echo $c1->invoice_num; ?></td>
					<td colspan="7">CANCELLED</td>
				</tr>
<?php 
			endif; 
/*********************/

?>

<?php 

/*********************/

		if(
			$c1->status == 'or_nw' || 
			$c1->status == 'cr_nw'
		): 
		
		$ttl_recieved += $c1->payment;
		$ttl_amt += $c1->payment;
		
		$line11 += 1 ; 
		
		
?>

			<tr>
				<td class="or4">OR-<?php echo $c1->invoice_num; ?></td>
				<td class="nam4"><?php echo substr($c1->accounts->acct_no.'-'.$c1->accounts->lname.', '.$c1->accounts->fname,0,25); ?></td>
				<td class="col4 rr"><?php echo number_format($c1->payment,2); ?></td>
				<td class="rr"></td>
<!--
				<td class="rr"></td>
				<td class="rr"></td>
-->
				<td class="rr" colspan="3">
					GL : <?php echo strtoupper($c1->nw_glsl); ?><br />
					<?php echo strtoupper($c1->nw_desc); ?>
				</td>
				<td class="amt4 rr"><?php echo number_format($c1->payment,2); ?></td>
			</tr>

<?php 
		endif; 
/*********************/

?>


<?php 		
/*********************/
			if(
				$c1->status == 'nw_cancel' ||
				$c1->status == 'cancel_cr_nw'
			): 
?>
		<tr>
			<td class="or4">OR-<?php echo $c1->invoice_num; ?></td>
			<td colspan="7">CANCELLED</td>
		</tr>
<?php 
		endif; 
/*********************/
?>


<?php 
/*********************/

			if(
				$c1->tax_val > 0 
			):
			
				$line11 += 1 ; 
			
			
			 ?>

				<tr>
					<td colspan="5">&nbsp;</td>
					<td colspan="2">W/TAX</td>
					<td class="amt4 rr">( <?php echo number_format($c1->tax_val,2); ?> )</td>
				</tr>
			
				
<?php 	
			endif; 
/*********************/

?>

						
				<?php endforeach; ?>
				
					<tr class="under11 bold1"  style="color:blue;">
						<td colspan="2"><?php echo $clasi_name; ?> TOTAL  <?php //echo $line11;  ?></td>
						<td class="rr"><?php  echo number_format($ttl_recieved,2); ?></td>
						<td class="rr"><?php  echo number_format($ttl_current,2); ?></td>
						<td class="rr"><?php  echo number_format($ttl_arrear_cy,2); ?></td>
						<td class="rr"><?php  echo number_format($ttl_arrear_py,2); ?></td>
						<td class="rr"><?php  echo number_format($ttl_penalty,2); ?></td>
						<td class="rr"><?php  echo number_format($ttl_amt,2); ?></td>
					</tr>				
				
				</table>
				
				
				
			</div>
			
			
		</div>


<?php 


$class_recieved   += $ttl_recieved;	
$class_current    += $ttl_current;
$class_arrear_cy  += $ttl_arrear_cy;
$class_arrear_py  += $ttl_arrear_py;
$class_penalty    += $ttl_penalty;
$class_amt     	  += $ttl_amt;


endforeach;//class1

?>

		<div class="clasi_box">
			<div class="item_box">
				
					<table class="tab11">
						
						<tr class="bord_bot" style="visibility:hidden;">
							<td class="or4">OR #</td>
							<td class="nam4">NAME</td>
							<td class="col4 rr">COLLECTED</td>
							<td class="cur4 rr">CURRENT</td>
							<td class="cy4 rr">ARREAR(C.Y.)</td>
							<td class="py4 rr">ARREAR(P.Y.)</td>
							<td class="pen4 rr">PENALTY</td>
							<td class="amt4">AMOUNT</td>
						</tr>						
						
						<tr class="under11">
							<td colspan="8">&nbsp;</td>
						</tr>
						<tr class="under11 bold1" style="color:red;">
							<td colspan="2"><?php echo $myz->zone_name; ?> TOTAL</td>
							<td class="rr"><?php  echo number_format($class_recieved,2); ?></td>
							<td class="rr"><?php  echo number_format($class_current,2); ?></td>
							<td class="rr"><?php  echo number_format($class_arrear_cy,2); ?></td>
							<td class="rr"><?php  echo number_format($class_arrear_py,2); ?></td>
							<td class="rr"><?php  echo number_format($class_penalty,2); ?></td>
							<td class="rr"><?php  echo number_format($class_amt,2); ?></td>
						</tr>		
					</table>				
			
			</div>
		</div>


<?php 



$grand_recieved   += $class_recieved;	
$grand_current    += $class_current;
$grand_arrear_cy  += $class_arrear_cy;
$grand_arrear_py  += $class_arrear_py;
$grand_penalty    += $class_penalty;
$grand_amt     	  += $class_amt;


echo '

<br />
<br />
<br />


';

?>

</div>
<?php 

$line11 += 4 ; 


endforeach; 



?>



<hr />
<div class="zone_box">
		<div class="clasi_box">
			<div class="item_box">
				
					<table class="tab11">
						
						<tr class="bord_bot" style="visibility:hidden;">
							<td class="or4">OR #</td>
							<td class="nam4">NAME</td>
							<td class="col4 rr">COLLECTED</td>
							<td class="cur4 rr">CURRENT</td>
							<td class="cy4 rr">ARREAR(C.Y.)</td>
							<td class="py4 rr">ARREAR(P.Y.)</td>
							<td class="pen4 rr">PENALTY</td>
							<td class="amt4">AMOUNT</td>
						</tr>						
						
						<tr class="under11">
							<td colspan="8">&nbsp;</td>
						</tr>
						<tr class="under11 bold1" style="color:red;">
							<td colspan="2">GRAND TOTAL</td>
							<td class="rr"><?php  echo number_format($grand_recieved,2); ?></td>
							<td class="rr"><?php  echo number_format($grand_current,2); ?></td>
							<td class="rr"><?php  echo number_format($grand_arrear_cy,2); ?></td>
							<td class="rr"><?php  echo number_format($grand_arrear_py,2); ?></td>
							<td class="rr"><?php  echo number_format($grand_penalty,2); ?></td>
							<td class="rr"><?php  echo number_format($grand_amt,2); ?></td>
						</tr>		
					</table>				
			
			</div>
		</div>
</div>



<br />
<br />
<br />
<br />

<table style="width:800px;">
	<tr>
		<td class="cc">
			<span class="bl1">Prepared By :</span>
			<br />
			<br />
			<br />
			<div style="display:inline-block;padding-left:50px;padding-right:50px;border-bottom:1px solid #000;"><?php echo REP_SIGN1; ?></div>
			<br />
			<?php echo REP_SIGN1_TITLE; ?>
		</td>
		<td class="cc">
			<span class="bl1">Checked by:</span>
			<br />
			<br />
			<br />
			<div style="display:inline-block;padding-left:50px;padding-right:50px;border-bottom:1px solid #000;"><?php echo REP_SIGN2; ?></div>
			<br />
			<?php echo REP_SIGN2_TITLE; ?>
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
	font-size:12px;
}

.zone_box {
    padding-left: 20PX;
}

.clasi_box {
    padding-left: 30PX;
}
.item_box{
    padding-left: 20PX;
}



.page_box1{
	position:relative;
}
.page-number-tt{
	position:absolute;
	top:0;
	right:0;
}
.bold1{
	font-weight:bold;
}

/*
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
*/

.bl1{
	
}

table.tab11{
	width:800px;
    table-layout: fixed;
    white-space: nowrap;	
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
 
.w200{width:200px;}
.sub_ttl1 td{
	border:1px solid #ccc;
	padding:3px;
	text-align:right;
	padding-right: 20px;
}

.under11 td{border-top:1px solid #ccc;}


.or4{width: 80px;}
.nam4{width: 180px;overflow:hidden;}
.col4{text-align: right;width: 80px;}
.cur4{width: 80px;}
.cy4{width: 80px;}
.py4{width: 80px;}
.pen4{width: 80px;}




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
