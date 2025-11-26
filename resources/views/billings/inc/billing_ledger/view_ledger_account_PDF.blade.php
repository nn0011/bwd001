<?php

$_SESSION["my_csrf"]=uniqid();

$full_name = $account_info->fname.' '.$account_info->mi.' '.$account_info->lname;

$start_reff = abs((int) @$_GET['start_reff']);


?>


<p>
<?php echo WD_NAME; ?>
<br />
<?php echo WD_ADDRESS; ?>
<br />
Ledger Account of <?php  echo $full_name; ?>
<br />
Acct # <?php echo $account_info->acct_no; ?>
<br />
As of <?php echo date('F d, Y'); ?>







<div class="tab1 tabview1  tabview">
	
	
	<table  class="led01 acct_ledger01  table10"  cellpadding="0" cellspacing="0">
		<tr  class="headings">
			<?php
			/*
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
			*/ 
			?>
			<?php 
			/*
			<td class="w1">DATE</td>
			<td class="w2">PARTICULAR</td>
			<td class="w3">REFFERENCE</td>
			<td class="w4">PERIOD</td>
			<td class="w5">PREV READING</td>
			<td class="w6">CURRENT READING</td>
			<td class="w7">C.U.</td>
			<td class="w8">BILLING</td>
			<td class="w9">ARREAR</td>
			<td class="w10">DISCOUNT</td>
			<td class="w11">PENALTY</td>
			<td class="w12">ADJUSTMENT</td>
			<td class="w13">PAYMENT</td>
			<td class="w14">BALLANCE</td>			
			*/?>
			
			<td class="r1">DATE</td>
			<td class="r2">PARTICULAR</td>
			<td class="r3">REFF</td>
			<td class="r4">PRV</td>
			<td class="r5">CUR</td>
			<td class="r6">C.u.M</td>
			<td class="r7">DEBIT</td>
			<td class="r8">CREDIT</td>
			<td class="r9">BALANCE</td>

			
		</tr>
		

			<?php  
			
			//~ for($x=0;$x<=10;$x++){
			foreach($led001 as $ll): 

			if( $start_reff >  $ll->id ) 
			{
				continue;
			}
			
				
				$ttl1 = $ll->ttl_bal;
				
				
				$bill_num = '';

				if($ll->led_type == 'billing'){
					$bill_num = get_billing_number($ll->reff_no);					
				}
								
				
				if($ll->led_type == 'billing'  && $ll->discount > 0  ){
					//~ $ttl1  = $ll->billing; 
				}
				
				
				$reading_info = array(
					'prev3' =>'',
					'curr3' =>$ll->reading,
					'cons3' => $ll->consump
				);
				if($ll->led_type == 'billing'){
					$rr3 = getReadingByPeriod($ll->acct_id, $ll->period);
					$reading_info['prev3']= @$rr3->prev_reading;
				}
				
				
				
				$A_DATE    = '';
				$A_PAR     = part_name1($ll->led_type);
				$A_REFF    = $ll->reff_no.'<br />'.$bill_num;
				$A_PERIOD  = '';
				$A_PREV    = $reading_info['prev3'];
				$A_CUR     = $reading_info['curr3'];
				$A_CON     = $reading_info['cons3'];
				$A_BILL    = '';
				$A_ARR 	   = '';
				$A_DIS     = '';
				$A_PEN     = '';
				$A_ADJ     = '';
				$A_PAY     = '';
				$A_BAL     = '';
				
				
				
				if($ll->led_type == 'penalty')
				{
					$A_DATE = date('m/d/Y', strtotime($ll->created_at)); 
				}else{
					$A_DATE = date('m/d/Y', strtotime($ll->date01)); 
				}
				
				
				if($ll->led_type == 'billing'){
					$A_PERIOD.= date('Y/m/d', strtotime(@$ll->date01.' -1 month' )); 
					$A_PERIOD.= ' - ';
					$A_PERIOD.= date('Y/m/d', strtotime(@$ll->date01)); 
				}
				
				if(!empty($ll->billing)){
					$A_BILL  .= number_format($ll->billing, 2); 
				}

				if(!empty($ll->arrear)){
					$A_ARR = number_format($ll->arrear, 2); 
				}
				
				if(!empty($ll->penalty)){
					$A_PEN = number_format($ll->penalty, 2); 
				}
				
				if(!empty($ll->bill_adj)){
					$A_ADJ = number_format($ll->bill_adj, 2); 
				}
			
				if(!empty($ll->payment)){
					$A_PAY = number_format($ll->payment, 2); 
				}
				
				$A_ADJD = '';
				$A_ADJC = '';
				
				if(!empty($ll->bill_adj))
				//~ if($ll->led_type=='adjustment')
				{
					//$A_ADJ = number_format($ll->bill_adj, 2);
					if($ll->bill_adj <= 0){$A_ADJD = number_format(abs($ll->bill_adj), 2);}
					else{$A_ADJC = number_format(abs($ll->bill_adj), 2);}
				}
				
				$A_PAY_D = '';
				$A_PAY_C = '';
				
				if(!empty($ll->payment)){
					
					if($ll->payment <= 0)
					{
						$A_PAY_D = number_format(abs($ll->payment), 2);
					}else{
						$A_PAY_C = number_format(abs($ll->payment), 2);
					}
					//~ $A_PAY = number_format($ll->payment, 2);
				}
				
				if(!empty($ll->discount)){
					$A_DIS = number_format($ll->discount, 2); 
				}
				
				
				
				if(!empty($ttl1)){
					$A_BAL =  number_format($ttl1, 2); 
				}


				//For Senior
				$is_sen = stripos($ll->ledger_info, 'senior');
				
				if($is_sen !== false &&  $ll->led_type=='adjustment')
				{
					$A_DIS = $A_ADJ;
					$A_ADJ = '';
					$A_PAR .= 'SENIOR CITIZEN';
				}

				
				$A_PAR = strtoupper($A_PAR);

				$led_info = strtoupper($ll->ledger_info);
				
				$adj_remark = '';
				
				if($ll->led_type=='adjustment')
				{
					$adj_remark= get_adj_desc($ll->reff_no);
					$adj_remark = strtoupper($adj_remark);
				}				
				
				
			?>
			
			
			
			<tr>
				<td class="r1"><?php echo $A_DATE; ?></td>
				<td class="r2">
					<p>
						<?php echo !empty($A_PAR)?$A_PAR.'<br />':''; ?>
						<?php echo !empty($A_PERIOD)?$A_PERIOD.'<br />':''; ?>
						<?php echo !empty($led_info)?$led_info.'<br />':''; ?>
						<?php echo !empty($adj_remark)?$adj_remark.'<br />':''; ?>
						<small style="font-size:10px;">REFF : <?php echo $ll->id; ?></small>

					</p>
				</td>
				<td class="r3"><?php echo $A_REFF; ?></td>
				<td class="r4"><?php   echo $A_PREV; ?></td>
				<td class="r5"><?php   echo $A_CUR; ?></td>
				<td class="r6"><?php   echo $A_CON; ?></td>
				<td class="r7">
					<?php   echo $A_BILL; ?>
					<?php  echo $A_PEN; ?>
					<?php  echo $A_ADJD; ?>
					<?php  echo $A_PAY_D; ?>
					
				</td>
				<td class="r8">
					<?php  echo $A_ADJC; ?>
					<?php   echo $A_PAY_C; ?>
					<?php   echo $A_DIS; ?>
				</td>
				<td class="r9"><?php    echo $A_BAL; ?></td>
			</tr>			
			

			
			<?php 
			/*
			<tr>
				<td><?php echo $A_DATE; ?></td>
				<td><?php echo $A_PAR; ?></td>
				<td><?php echo $A_REFF; ?></td>
				<td><?php echo $A_PERIOD;?></td>
				<td style="text-align:right;"><?php   echo $A_PREV; ?></td>
				<td style="text-align:right;"><?php   echo $A_CUR; ?></td>
				<td style="text-align:right;"><?php   echo $A_CON; ?></td>
				<td   style="text-align:right;"><?php echo $A_BILL; ?></td>
				<td style="text-align:right;"><?php   echo $A_ARR; ?></td>
				<td  style="text-align:right;"><?php  echo $A_DIS; ?></td>
				<td  style="text-align:right;"><?php  echo $A_PEN; ?></td>
				<td  style="text-align:right;"><?php  echo $A_ADJ; ?></td>
				<td style="text-align:right;"><?php   echo $A_PAY; ?></td>				
				<td  style="text-align:right;"><?php  echo $A_BAL; ?></td>
			</tr>
						
			
			
			
			<?php 
			
			/*
			<tr>
				<td><?php echo date('m/d/Y', strtotime($ll->date01)); ?></td>
				<td><?php echo part_name1($ll->led_type); ?></td>
				
				<td><?php echo $ll->reff_no; ?></td>
				<td><?php 
				
					//echo @$ll->period; 
					if($ll->led_type == 'billing'){
						//~ $pp1 = date('Y-m-d', strtotime(@$ll->period));
						echo date('Y/m/d', strtotime(@$ll->date01.' -1 month' )); 
						echo ' - ';
						echo date('Y/m/d', strtotime(@$ll->date01)); 
						
					}
				
				?></td>
				<td style="text-align:right;"><?php echo $reading_info['prev3']; ?></td>
				<td style="text-align:right;"><?php echo $reading_info['curr3']; ?></td>
				<td style="text-align:right;"><?php echo $reading_info['cons3']; ?></td>
				<td   style="text-align:right;"><?php 
					if(!empty($ll->billing)){
						echo number_format($ll->billing, 2); 
					}
				?></td>
				<td style="text-align:right;"><?php  
					if(!empty($ll->arrear)){
						echo number_format($ll->arrear, 2); 
					}
				
				?></td>
				<td><?php //echo number_format(0, 2); ?></td>
				<td  style="text-align:right;"><?php 
						
						if(!empty($ll->penalty)){
							echo number_format($ll->penalty, 2); 
						}
				
				?></td>
				
				<td  style="text-align:right;"><?php 
					if(!empty($ll->bill_adj)){
						echo number_format($ll->bill_adj, 2); 
					}
				?></td>
				
				<td style="text-align:right;"><?php 
						
						if(!empty($ll->payment)){
							echo number_format($ll->payment, 2); 
						}
				
				?></td>
				
				<td  style="text-align:right;"><?php 
					if(!empty($ttl1)){
						echo number_format($ttl1, 2); 
					}
				
				?></td>
			</tr>
			* 
			*/?> 
			
			<?php /*if($ll->led_type == 'billing'  && $ll->discount > 0  ): ?>
				<tr>
					<td><?php echo $ll->date01; ?></td>
					<td>Senior Citizen</td>
					
					<td><?php echo $ll->reff_no; ?></td>
					<td><?php //echo @$ll->period; ?></td>
					<td><?php //echo number_format(0, 2); ?></td>
					<td><?php //echo number_format(0, 2); ?></td>
					<td><?php //echo number_format(0, 2); ?></td>
					<td><?php //echo number_format(0, 2); ?></td>
					<td><?php //echo number_format(0, 2); ?></td>
					<td style="text-align:right;"><?php echo number_format($ll->discount, 2); ?></td>
					<td style="text-align:right;"><?php //echo $ll->reading; ?></td>
					<td style="text-align:right;"><?php //echo $ll->consump; ?></td>
					<td style="text-align:right;"><?php //echo $ll->consump; ?></td>
					<td  style="text-align:right;"><?php echo number_format($ll->ttl_bal, 2); ?></td>
				</tr>
			<?php endif; */ ?>
			
			<?php endforeach; //} ?>
			
				<tr>
					<td colspan="9">--- END ----</td>
				</tr>
					
		
		
	</table>

</div>	

<?php 

echo html_signature("<br/><br/><br/>
".REP_SIGN3."<br />
Billing In-Charge
");

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


	
	
<!--
<span class="page-number" style="display:none;">Page </span>
-->

	

	
<style>
*{
<!--
	font-family:'monospace';
-->
	font-size:12px;
}	
table{
	with:100%;
}
table *{
}
table td{
	border:1px solid #ccc;
	padding:3px;
}
.page-number{ 
		position:fixed;
		left:0;
		bottom:10;
}
.page-number:after { 
		content: counter(page); 
 }
.page_break { page-break-before: always; }


.w3{width:50px;}

.r1{width:50px;}	
.r2{width:150px;}	
.r3{width:50px;}	
.r4{width:30px;text-align:center;}	
.r5{width:30px;text-align:center;}	
.r6{width:30px;text-align:center;}	
.r7{width:75px;text-align:right;padding-right:10px;}	
.r8{width:75px;text-align:right;padding-right:10px;}	
.r9{width:75px;text-align:right;padding-right:10px;}

</style>



