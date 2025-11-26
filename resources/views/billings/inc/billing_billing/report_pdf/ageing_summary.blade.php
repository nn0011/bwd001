<?php 

//full_date

$curr_date  = date('Y-m-01', strtotime($full_date));


?>
<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />
Ageing of Account Recievable - Summary
<br />
<br />

<b>ACTIVE</b> 

<table style="width:100%" cellpadding="0" cellspacing="0">
	
	<tr class="bld_me">
		<td>&nbsp;</td>
<!--
		<td>A/R Others</td>
-->
		<td class="rh1">Current</td>
		<?php foreach($new_arr22 as $nn){ ?>
		<td class="rh1"><?php echo $nn; ?></td>
		<?php } ?>
		<td class="rh1"> > <?php echo $nn; ?></td>
		<td class="rh1">WB Total</td>
	</tr>

	<?php 
	
	$subTtl = 0;
	foreach($contain1 as $cc1){ 
		$ttl_wb = 0;
		
		$arr_dat = $cc1['data'];
		
		?>
	<tr>
		<td><?php echo $cc1['name']; ?></td>
<!--
		<td></td>
-->
		<td class="rh1"><?php 
		
		$ttl1 = (float) @$cc1['data'][$curr_date];
		$ttl_wb+=$ttl1;
		echo number_format($ttl1,2); 
		unset($arr_dat[$curr_date]);
		
		?></td>
		<?php foreach($new_arr22 as $nn){ ?>
		<td  class="rh1"><?php 
			 $mm2 = date('Y-m-01', strtotime($curr_date.' - '.$nn)); 
			 $ttl1 = (float) @$cc1['data'][$mm2];
			 $ttl_wb+=$ttl1;
			 echo number_format($ttl1,2);
		?></td>
		<?php 
			unset($arr_dat[$mm2]);
		} ?>
		<td   class="rh1">
			<?php
				$more1 = 0;
				foreach($arr_dat as $ad1){
					$more1+=$ad1;
				}
				$ttl1 = @$more1;
				$ttl_wb+=@$more1;
				echo number_format($ttl1,2);
				
			?>
		</td>
		<td class="rh1">
			<?php
				echo number_format($ttl_wb,2);
			 ?>
		</td>
		
	</tr>
	<?php 
		$subTtl += $ttl_wb;
	
		 } ?>
	
	<tr class="bld_me">
		<td>Sub Total</td>
		<td></td>
		<?php foreach($new_arr22 as $nn){ ?>
		<td></td>
		<?php } ?>	
		<td></td>
		<td class="rh1">
			<?php
				echo number_format($subTtl,2);
			 ?>
		</td>
	</tr>

</table>
<br />
<br />

<hr />

<?php html_signature(); ?>


<style>
*{
	font-family:'serif';
	font-size:10px;
}	
table{
	width:100%;
}
table *{
}
table td{
	border-bottom:1px solid #ccc;
	padding:5px;
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
	
</style>
