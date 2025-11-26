<?php 
//$bill_num_end;

//~ echo $bill_num;
//~ die();

if(($bill001->count() == 0)){
?>
<div class="lo11"  style="width:700px;">
	No Result
</div>
<?php return; 
} ?>


<div class="lo11"  style="width:700px;">
	<div style="text-align:right;">
		<button  onclick="save_billing_number001()" style="float:left;">Save Billing Number</button>
		<button  onclick="start_printing221()">Start Printing</button>
	</div>
	<br />
	<br />

	<table style="width:100%;">
			<tr class="head001">
				<td style="width:10%;">ID</td>
				<td style="width:10%;">CUR Bill #</td>
				<td style="width:10%;">Bill #</td>
				<td style="width:10%;">Account #</td>
				<td style="width:50%;">Full name</td>
<!--
				<td style="width:50%;">Address</td>
-->
			</tr>
			<?php 
			
			$index=1;
			
			$bn0 = $bill_num;
			$is_blank = false;
			
			$has_num = (int) @$bill001[0]->bill_num_01;
			
			
			if($has_num <= 0){
				$is_blank = true;
			}
			
			foreach($bill001 as $bb): 
			
			$curr_bill = @$bb->bill_num_01;
			
			//~ if($bn0 > $curr_bill && !$is_blank){
			
			if($curr_bill > 0)
			{
				if($bn0 > $curr_bill){
					continue;
				}
			}
			
			$acct1 = $bb->account;
			
			//~ $bb->ledger_data;
			//~ $bb->ledger_data->ledger_info = $bn0;
			//~ $bb->ledger_data->save();
			
			
			?>
			<tr>
				<td><?php echo $index; ?></td>
				<td><?php echo @$bb->bill_num_01; ?></td>
				<td><?php echo $bn0; ?></td>
				<td><?php echo $bb->reading1->account_number	; ?></td>
				<td>
					<span><?php  echo $acct1->lname; ?>, <?php  echo $acct1->fname; ?>, <?php  echo $acct1->mi; ?></span>
				</td>
<!--
				<td>
					<?php echo $bb->account->address1; ?>
				</td>
-->
			</tr>
			<?php
			
			if((int) $bill_num_end  != 0){
				if($bn0 >= $bill_num_end){
					break;
				}
			}
			
			
			$bn0++;
			$index++;
			 endforeach; ?>
	</table>
	<br />
	<br />

</div>


<div style="text-align:right;">
<!--
	<button  onclick="RESET_BILLING_NUMBER()">RESET BILLING NUMBER</button>
-->
</div>

<style>
.lo11{
	font-size:12px;
}
.lo11 td{	
	padding:5px;
	border:1px solid #ccc;
}	
</style>
