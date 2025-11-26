<html>
	<head>
		<title>Summary</title>
	</head>
	<body>
		
		
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>

<br />
Daily Collection Summary
as of <?php echo date('l, F d, Y',strtotime($date1)); ?>

<br />
<br />

<div style="max-width:900px;width:100%;">
	<strong style="font-size:14px;"><?php echo strtoupper($my_coll->name); ?></strong>
	<table class="tab1" cellpadding="3" cellspacing="0">
		<tr  class="hh1">
			<td>Date</td>
			<td class="ct">Invoice Start</td>
			<td class="ct">Invoice End</td>
			<td class="ct">Invoice Total</td>
			<td class="rt">Total Collection</td>
		</tr>
		
<?php

$total_collection  =  0;
$total_invoice     =  0;

?>
		<?php foreach($new_arr as $kk => $na1): ?>
		<?php if($na1->coll1['INV_TTL'] <= 0): ?> 
		<tr>
			<td><?php echo date('F d, Y - l', strtotime($na1->date1)); ?></td>
			<td colspan="4" class="ct">NO COLLECTION</td>

		</tr>
		<?php continue; endif; ?>
<?php 

$total_collection += $na1->coll1['TTL'];
$total_invoice    += $na1->coll1['INV_TTL'];


?>
		<tr>
			<td><?php echo date('F d, Y - l', strtotime($na1->date1)); ?></td>
			<td class="ct"><?php echo $na1->coll1['inv_min'] ?></td>
			<td class="ct"><?php echo $na1->coll1['inv_max'] ?></td>
			<td class="ct"><?php echo number_format($na1->coll1['INV_TTL'],0) ?></td>
			<td class="rt"><?php echo number_format($na1->coll1['TTL'],2) ?></td>
		</tr>
		<?php endforeach; ?>
		
		<tr class="hh1">
			<td class="lt" colspan="3">TOTAL</td>
			<td class="ct"><?php echo number_format($total_invoice,0) ?></td>
			<td class="rt"><?php echo number_format($total_collection,2) ?></td>
		</tr>
		
	</table>
</div>
		
		
	</body>
	
<style>
@page { size: auto;  margin: 5mm; }

*{
	font-size:12px;
	font-family:arial;
}
.lt{text-align:left;}
.rt{text-align:right;}
.ct{text-align:center;}

table td, table th{
	border:1px solid #ccc;
}

.tab1{
	width:100%;
}

.hh1 td{
	font-weight:bold;
	font-size:14px;
}

</style>
</html>
