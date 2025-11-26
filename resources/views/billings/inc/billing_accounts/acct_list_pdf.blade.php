<?php

$item_per_page = 35;
$item_per_next = 45;
$page = 1;


$zone_lab = array();
foreach($zones as $zz)
{
	$zone_lab[$zz['id']] = $zz['zone_name'];
}



?>

<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />
As of <?php echo date('F d, Y'); ?>
</p>






<div class="tab1 tabview1  tabview">
	
		
		<?php 
		
		$ttl_c = count($ress1);
		
		$head11 = 0;
		
		for($x=0;$x<$ttl_c;$x++)
		//foreach($ress1 as $rr)
		{
			
			$rr = @$ress1[$x];
			$rr->ledger_data4;
			
			$full_name = $rr->fname.' '.$rr->lname;
		?>
			
			<?php if($head11 == 0){ ?>
				
				<p style="padding:0;margin:0;">
					<?php 
						$qsearch = Q_Search_label(@$_GET['quick_search']);
						if(!empty($qsearch)){
							echo strtoupper($qsearch);
							echo '<br />';
						}
					?>

					<?php 
						$Z1 = get_zone101($zone); 
						if(!empty($Z1)){
							echo $Z1;
							echo '<br />';
						}
					?>
				</p>
				
				<table  cellpadding="0" cellspacing="0"  width="100%">
					<tr  class="headings">
						<td>ACCT</td>
						<td>NAME</td>
						<td>ZONE</td>
						<td>MTR #</td>
						<td>STATUS</td>
						<td  style="text-align:right;padding-right:10px;">BALANCE</td>
					</tr>
			<?php } ?>
		
			<tr>
				<td><?php echo $rr->acct_no; ?></td>
				<td><small  style="font-size:10px;"><?php echo substr($full_name,0,30); ?></small></td>
				<td><small style="font-size:10px;"><?php echo @$zone_lab[@$rr->zone_id]; ?></small></td>
				<td><small style="font-size:10px;"><?php echo empty(@$rr->meter_number1)?'NONE':@$rr->meter_number1; ?></small></td>
				<td><?php echo con_led_type_v3($rr->acct_status_key); ?></td>
				<td style="text-align:right;padding-right:10px;"><?php echo  @$rr->ledger_data4->ttl_bal != 0?number_format(@$rr->ledger_data4->ttl_bal,2):''; ?></td>
			</tr>
			
			<?php if($head11 >= $item_per_page){ 
				
				$item_per_page = $item_per_next;

				?>
					</table>
					
					<p class="page-number-c" style="padding:0px;margin:0px;">Page <?php echo $page; ?>  / ttl_pagexx</p>
					
					
					<div class="page_break"></div>
			<?php 
					$head11 = 0;
					$page++;
					continue;
			
			} ?>
			
		
		<?php 
				$head11++;
			}
		?>
	

<!--
		<tr>
			<td colspan="5">--- END ----</td>
		</tr>
	</table>
-->

</table>
	
<br />
<div style="font-weight:bold;">  --- END ----</div>

<p class="page-number-x">Page <?php echo $page; ?>  / ttl_pagexx</p>
</div>	

<?php 
$page ++;
?>

<div class="page_break"></div>
<p class="page-number-x">Page <?php echo $page; ?> / ttl_pagexx</p>


<?php 

echo html_signature();

?>


<!--
<span class="page-number">Page </span>
-->

<!--
<div class="page_break"></div>

	<table  class="led01 acct_ledger01  table10"  cellpadding="0" cellspacing="0">
		<tr  class="headings">
			<td class="w1">DATE</td>
			<td class="w2">PAR.</td>
			<td class="w3">REFF</td>
			<td class="w4">PERIOD</td>
			<td class="w5">PREV</td>
			<td class="w6">CUR</td>
			<td class="w7">CON</td>
			<td class="w8">BILL</td>
			<td class="w9">ARR</td>
			<td class="w10">DIS</td>
			<td class="w11">PEN</td>
			<td class="w12">ADJ</td>
			<td class="w13">PAY</td>
			<td class="w14">BAL</td>
		</tr>
		
	</table>
-->


	
	

	

	
<style>
*{
	font-size:12px;
    font-family: sans-serif;
}	
.r1{
	text-align:left;
	padding-left:30px;
}
.headings{
	font-weight:bold;
}

table{
	with:100% !important;
}
table *{
}
table td{
	border-bottom:0px solid #000000;
	padding:0px;
	padding-top:3px;
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
		left:0;
		bottom:10;
}
.page-number:after { 
		content: counter(page); 
		left:25;
		position:absolute;
 }
.page_break { page-break-before: always; }
.headings td{
	border:1px solid #000;
	padding-left:5px;
}
	
</style>



<?php 
$cont1 = ob_get_contents();
$cont1 = str_replace('ttl_pagexx', $page, $cont1);

ob_clean();
echo $cont1;

?>
