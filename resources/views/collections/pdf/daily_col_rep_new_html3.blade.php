<?php /*
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />*/ ?>
<table  cellpadding="0" cellspacing="0"  style="max-width:1024px;">
	<thead>
	<tr>
		<td colspan="7">
			Daily Collection Report
			<br />
			<?php  echo date('l, M d, Y', strtotime(@$date1)); ?> 	
		</td>
		<td>
			<span class="RRR111"></span>
		</td>
	</tr>

	<tr class="bord_all">
		<td>ZONE</td>
		<td class="cc">Amount <br /> Collected</td>
		<td class="cc">Current</td>
		<td class="cc">Arrears <br /> (CY)</td>
		<td class="cc">Arrears <br /> (PY)</td>
		<td class="cc">Penalty</td>
		<td class="rr">N.W.B</td>
		<td class="rr">W/Tax</td>
	</tr>

	</thead>

	<tbody>

<?php 

	$ttl_001 = ['pen'=>0, 'bil'=>0,'py'=>0, 'cy'=>0, 'nwb'=> 0, 'tax'=>0, 'pay'=> 0];
	
?>		

		<?php foreach($all_zones as $cc): ?>
			
			<tr class="dddxx">
				<td><?php echo strtoupper($cc->zone_name); ?></td>
				<td class="rr"><?php echo ($n44 = $cc->ttl_brk['pay']) <= 0?'-':number_format($n44,2); ?></td>
				<td class="rr"><?php echo ($n44 = $cc->ttl_brk['bil']) <= 0?'-':number_format($n44,2); ?></td>
				<td class="rr"><?php echo ($n44 = $cc->ttl_brk['cy']) <= 0?'-':number_format($n44,2); ?></td>
				<td class="rr"><?php echo ($n44 = $cc->ttl_brk['py']) <= 0?'-':number_format($n44,2); ?></td>
				<td class="rr"><?php echo ($n44 = $cc->ttl_brk['pen']) <= 0?'-':number_format($n44,2); ?></td>
				<td class="rr"><?php echo ($n44 = $cc->ttl_brk['nwb']) <= 0?'-':number_format($n44,2); ?></td>
				<td class="rr"><?php echo ($n44 = $cc->ttl_brk['tax']) <= 0?'-':number_format($n44,2); ?></td>
			</tr>				

		<?php 
	
	$ttl_001['pay'] += $cc->ttl_brk['pay'];
	$ttl_001['bil'] += $cc->ttl_brk['bil'];
	$ttl_001['cy']  += $cc->ttl_brk['cy'];
	$ttl_001['py']  += $cc->ttl_brk['py'];
	$ttl_001['pen'] += $cc->ttl_brk['pen'];
	$ttl_001['nwb'] += $cc->ttl_brk['nwb'];
	$ttl_001['tax'] += $cc->ttl_brk['tax'];
	
	endforeach; ?>
		<tr class="bord_all">
			<td>TOTAL</td>
			<td class="rr"><?php echo ($n44 = $ttl_001['pay']) <= 0?'-':number_format($n44,2); ?></td>
			<td class="rr"><?php echo ($n44 = $ttl_001['bil']) <= 0?'-':number_format($n44,2); ?></td>
			<td class="rr"><?php echo ($n44 = $ttl_001['cy']) <= 0?'-':number_format($n44,2); ?></td>
			<td class="rr"><?php echo ($n44 = $ttl_001['py']) <= 0?'-':number_format($n44,2); ?></td>
			<td class="rr"><?php echo ($n44 = $ttl_001['pen']) <= 0?'-':number_format($n44,2); ?></td>
			<td class="rr"><?php echo ($n44 = $ttl_001['nwb']) <= 0?'-':number_format($n44,2); ?></td>
			<td class="rr"><?php echo ($n44 = $ttl_001['tax']) <= 0?'-':number_format($n44,2); ?></td>
		</tr>	


	</tbody>


</table>

<?php 


/*




<br />
<h3>GRAND TOTAL : <?php echo number_format(($ttl_001['pay']),2); ?></h3>




<table style="width:100%;">
	<tr>
		<td>
			<span class="bl1">Prepared By :</span>
			<br />
			<br />
			<br />
			<?php echo strtoupper($user->name); ?>			
			<br />
			Teller 1
		</td>
		<td>
			<span class="bl1">Checked by:</span>
			<br />
			<br />
			<br />
			<?php echo REP_SIGN1; ?>			
			<br />
			<?php echo REP_SIGN1_TITLE; ?>
		</td>

		<td>
			<span class="bl1">Noted by:</span>
			<br />
			<br />
			<br />
			<?php echo WD_MANAGER; ?>			
			<br />
			<?php echo WD_MANAGER_RA; ?>
		</td>		

	</tr>
</table>

<br />
<br />






*/ ?>


<style>
html,body{
	margin:0;
	padding:20px;
}
*{
	font-family:'sans-serif';
	font-size:11px;
}

.cc1 td{
	padding-right:30px;
}

table{
	width:100%;
	max-with:800px;
}
table *{
}
table td{
	border:0px solid #ccc;
	padding:2px;
	vertical-align:top;
	padding-bottom:0  !important;
	padding-top:0 !important;
	margin-bottom:0  !important;
	margin-top:0 !important;

}
.bord_all td{
	border:1px solid #ccc;
}
.bord_bot td, .bord_bot1{
	border-bottom:1px solid #ccc;
}
.bord_top td, .bord_top1{
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

body,
html{
	padding-bottom:50px;
	margin-bottom:50px;
	counter-reset: pagexx;
}


.RRR111{
	counter-increment: pagexx;
}




@media print {
	.RRR111::after {
		content: "Page " counter(pagexx);
  }
}


</style>
	