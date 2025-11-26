<p>
	<?php echo WD_NAME; ?>
	<br />
	<?php echo WD_ADDRESS; ?>
	<br />
</p>

<table cellspacing="0" cellpadding="0">
	
	<thead>
	<tr>
		<td colspan="10">
			Ageing of Receivable - Water Bill - <?php echo $zone_name; ?>
			<br />
			<?php  echo date('F Y', strtotime($full_date)); ?>
			<br />
			<?php // echo $my_zone; ?>
		
		</td>
	</tr>
	<tr class="head11 bord_all">
		<td class="vc1">#</td>
		<td class="vc1">Account No.</td>
		<td class="vc1 rr">30 Days</td>
		<td class="vc1 rr">60 Days</td>
		<td class="vc1 rr">90 Days</td>
		<td class="vc1 rr">180 Days</td>
		<td class="vc1 rr">1 Year</td>
		<td class="vc1 rr"> Over 1 Year</td>
		<td class="vc1 rr">Total</td>
	</tr>
	</thead>
	
	<tr>
		<td colspan="10">
			<br />
			<h3>ACTIVE</h3>
		</td>
	</tr>

	<?php 

	$all_ttl = 1;
	$xx=1;

	$ttl_dat = [
				'd30' => 0,
				'd60' => 0,
				'd90' => 0,
				'd180' => 0,
				'd365' => 0,
				'more_365' => 0,
				'ttl1' => 0
			];

			// dd($active_arr); die();
			//committed
	foreach($active_arr as $aa): 
	?>
	<tr>
		<td class="vc1"><?php echo $xx; ?></td>
		<td class="vc1"><?php echo substr(strtoupper($aa->account->acct_no.'-'.$aa->account->lname.', '.$aa->account->fname),0,30); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d30']<=0?'':number_format($aa->brk001['d30'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d60']<=0?'':number_format($aa->brk001['d60'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d90']<=0?'':number_format($aa->brk001['d90'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d180']<=0?'':number_format($aa->brk001['d180'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d365']<=0?'':number_format($aa->brk001['d365'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['more_365']<=0?'':number_format($aa->brk001['more_365'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->ttl_bal<=0?'':number_format($aa->ttl_bal,2); ?></td>
	</tr>
	<?php 

	$ttl_dat['d30'] += $aa->brk001['d30'];
	$ttl_dat['d60'] += $aa->brk001['d60'];
	$ttl_dat['d90'] += $aa->brk001['d90'];
	$ttl_dat['d180'] += $aa->brk001['d180'];
	$ttl_dat['d365'] += $aa->brk001['d365'];
	$ttl_dat['more_365'] += $aa->brk001['more_365'];
	$ttl_dat['ttl1'] += $aa->ttl_bal;

	$xx++;
	$all_ttl++;
	endforeach; ?>

	<tr class="bld_me" style="background:#eee">
		<td class="vc1" colspan="2">ACTIVE - TOTAL : <?php echo number_format($xx-1,0); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat['d30']<=0?'':number_format($ttl_dat['d30'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat['d60']<=0?'':number_format($ttl_dat['d60'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat['d90']<=0?'':number_format($ttl_dat['d90'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat['d180']<=0?'':number_format($ttl_dat['d180'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat['d365']<=0?'':number_format($ttl_dat['d365'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat['more_365']<=0?'':number_format($ttl_dat['more_365'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat['ttl1']<=0?'':number_format($ttl_dat['ttl1'],2); ?></td>
	</tr>	


	<tr>
		<td colspan="10">
			<br />
			<h3>VOLUNTARY DISCONNECTED</h3>
		</td>
	</tr>
	<?php 
	$xx=1;

	$ttl_dat4 = [
				'd30' => 0,
				'd60' => 0,
				'd90' => 0,
				'd180' => 0,
				'd365' => 0,
				'more_365' => 0,
				'ttl1' => 0
			];
			// dd($voluntaryDis); die();
			
	foreach($voluntaryDis as $aa): 
	?>
	<tr>
		<td class="vc1"><?php echo $xx; ?></td>
		<td class="vc1"><?php echo substr(strtoupper($aa->account->acct_no.'-'.$aa->account->lname.', '.$aa->account->fname),0,30); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d30']<=0?'':number_format($aa->brk001['d30'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d60']<=0?'':number_format($aa->brk001['d60'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d90']<=0?'':number_format($aa->brk001['d90'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d180']<=0?'':number_format($aa->brk001['d180'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d365']<=0?'':number_format($aa->brk001['d365'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['more_365']<=0?'':number_format($aa->brk001['more_365'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->ttl_bal<=0?'':number_format($aa->ttl_bal,2); ?></td>
	</tr>
	<?php 

	$ttl_dat4['d30'] += $aa->brk001['d30'];
	$ttl_dat4['d60'] += $aa->brk001['d60'];
	$ttl_dat4['d90'] += $aa->brk001['d90'];
	$ttl_dat4['d180'] += $aa->brk001['d180'];
	$ttl_dat4['d365'] += $aa->brk001['d365'];
	$ttl_dat4['more_365'] += $aa->brk001['more_365'];
	$ttl_dat4['ttl1'] += $aa->ttl_bal;

	$xx++;
	$all_ttl++;
	endforeach; ?>

	
	<tr class="bld_me" style="background:#eee">
		<td class="vc1" colspan="2">VOLUNTARY DISCONNECTED - TOTAL : <?php echo number_format($xx-1,0); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat4['d30']<=0?'':number_format($ttl_dat4['d30'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat4['d60']<=0?'':number_format($ttl_dat4['d60'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat4['d90']<=0?'':number_format($ttl_dat4['d90'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat4['d180']<=0?'':number_format($ttl_dat4['d180'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat4['d365']<=0?'':number_format($ttl_dat4['d365'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat4['more_365']<=0?'':number_format($ttl_dat4['more_365'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat4['ttl1']<=0?'':number_format($ttl_dat4['ttl1'],2); ?></td>
	</tr>	

	
	<tr>
		<td colspan="10">
			<br />
			<h3>DISCONNECTED</h3>
		</td>
	</tr>
	<?php 
	$xx=1;

	$ttl_dat2 = [
				'd30' => 0,
				'd60' => 0,
				'd90' => 0,
				'd180' => 0,
				'd365' => 0,
				'more_365' => 0,
				'ttl1' => 0
			];
			
			
	foreach($disconn_arr as $aa): 
	?>
	<tr>
		<td class="vc1"><?php echo $xx; ?></td>
		<td class="vc1"><?php echo substr(strtoupper($aa->account->acct_no.'-'.$aa->account->lname.', '.$aa->account->fname),0,30); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d30']<=0?'':number_format($aa->brk001['d30'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d60']<=0?'':number_format($aa->brk001['d60'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d90']<=0?'':number_format($aa->brk001['d90'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d180']<=0?'':number_format($aa->brk001['d180'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['d365']<=0?'':number_format($aa->brk001['d365'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->brk001['more_365']<=0?'':number_format($aa->brk001['more_365'],2); ?></td>
		<td class="vc1 rr"><?php echo $aa->ttl_bal<=0?'':number_format($aa->ttl_bal,2); ?></td>
	</tr>
	<?php 
	$xx++;
	$all_ttl++;

	$ttl_dat2['d30'] += $aa->brk001['d30'];
	$ttl_dat2['d60'] += $aa->brk001['d60'];
	$ttl_dat2['d90'] += $aa->brk001['d90'];
	$ttl_dat2['d180'] += $aa->brk001['d180'];
	$ttl_dat2['d365'] += $aa->brk001['d365'];
	$ttl_dat2['more_365'] += $aa->brk001['more_365'];
	$ttl_dat2['ttl1'] += $aa->ttl_bal;

	endforeach; ?>

	<tr class="bld_me" style="background:#eee">
		<td class="vc1" colspan="2">DISCONNECTED - TOTAL : <?php echo number_format($xx-1,0); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat2['d30']<=0?'':number_format($ttl_dat2['d30'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat2['d60']<=0?'':number_format($ttl_dat2['d60'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat2['d90']<=0?'':number_format($ttl_dat2['d90'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat2['d180']<=0?'':number_format($ttl_dat2['d180'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat2['d365']<=0?'':number_format($ttl_dat2['d365'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat2['more_365']<=0?'':number_format($ttl_dat2['more_365'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat2['ttl1']<=0?'':number_format($ttl_dat2['ttl1'],2); ?></td>
	</tr>		

	<?php 

	$ttl_dat3['d30']  = $ttl_dat['d30'] + $ttl_dat2['d30'] + $ttl_dat4['d30'];
	$ttl_dat3['d60']  = $ttl_dat['d60'] + $ttl_dat2['d60'] + $ttl_dat4['d60'] ;
	$ttl_dat3['d90']  = $ttl_dat['d90'] + $ttl_dat2['d90'] + $ttl_dat4['d90'];
	$ttl_dat3['d180'] = $ttl_dat['d180'] + $ttl_dat2['d180'] + $ttl_dat4['d180'];
	$ttl_dat3['d365'] = $ttl_dat['d365'] + $ttl_dat2['d365'] + $ttl_dat4['d365'];
	$ttl_dat3['more_365'] = $ttl_dat['more_365'] + $ttl_dat2['more_365'] + $ttl_dat4['more_365'];
	$ttl_dat3['ttl1'] = $ttl_dat['ttl1'] + $ttl_dat2['ttl1'] + $ttl_dat4['ttl1'];

	?>

	<tr class="bld_me" style="background:#ddd">
		<td class="vc1" colspan="2">ALL - TOTAL : <?php echo number_format($all_ttl-1,0); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat3['d30']<=0?'':number_format($ttl_dat3['d30'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat3['d60']<=0?'':number_format($ttl_dat3['d60'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat3['d90']<=0?'':number_format($ttl_dat3['d90'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat3['d180']<=0?'':number_format($ttl_dat3['d180'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat3['d365']<=0?'':number_format($ttl_dat3['d365'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat3['more_365']<=0?'':number_format($ttl_dat3['more_365'],2); ?></td>
		<td class="vc1 rr"><?php echo $ttl_dat3['ttl1']<=0?'':number_format($ttl_dat3['ttl1'],2); ?></td>
	</tr>	

</table>




<br />
<br />
<br />
<br />

<table cellspacing="0" cellpadding="0">
	<tr>
		<td class="cc">
			<span class="bl1">Prepared By :</span>
			<br />
			<br />
			<br />
			<div class="cc"  style="display:inline;border-bottom:1px solid #000;">
				<?php echo REP_SIGN3; ?>
			</div>
			<br />
			<?php echo REP_SIGN3_TITLE; ?>
		</td>
		<td class="cc">
			<span class="bl1">Approved by:</span>
			<br />
			<br />
			<br />
			<div class="cc" style="display:inline;border-bottom:1px solid #000;">
				<?php echo WD_MANAGER; ?>
			</div>
			<br />
			<?php echo WD_MANAGER_RA; ?>
		</td>
	</tr>
</table>

<br />
<br />

 
<style>
*{
	font-family:monospace;
	font-size:12px;
	line-height:100%;
}
table{
	width:900px;
}
table *{
}
table td{
	border-bottom:0px solid #000;
	padding:2px;
	vertical-align:top;
	padding-bottom:3px;

}
.bord_all td{
	border:1px solid #ccc;
}
.bord_bot td{
	border-bottom:1px solid #000;
}
.bord_top td{
	border-top:1px solid #000;
	padding-top:3px;
	font-weight:bold;
}
.ll{
	text-align:left;
}
.rr{
	text-align:right;
}
.cc{
	text-align:center;
}
.vc1{vertical-align: middle;    border-bottom: 1px dashed #ccc;}

.under ,
.trun
td{
	border-top:1px solid #ccc;
	border-bottom:1px solid #ccc;
	font-weight:bold;
	font-size:11px;
	padding-top:5px;
	padding-bottom:5px;
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

.page_break { page-break-before: always; }



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
		left:0;
		bottom:10;
}
.page-number:after {
		content: counter(page);
		left:25;
		position:absolute;
 }
.w200{
	width:200px;
}
.sub_ttl1 td{
	border:1px solid #ccc;
	padding:3px;
	text-align:right;
	padding-right: 20px;
}

.tt0{width:50px;}
.tt1{width:65px;}
.tt2{width:150px;}
.tt3{width:50px;}
.tt4{width:50px;}
.tt5{width:50px;}
.tt6{width:50px;}
.tt7{width:50px;}
.tt8{width:90px;}
.tt9{width:50px;}
.tt10{width:90px;}

.paR_5{padding-right:15px;}

</style>
