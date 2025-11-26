<?php

ob_start();

?>


<?php

//~ $result1
$ind = 0;
$sleep1 = 0;

for(;;):


?>

<table class="tab1" cellspacing="10" cellpadding="0">
	
	<?php for($x=0;$x<=2;$x++): 
			
			if(empty(@$result1[$ind])){
				break;
			}
			$curr_acct = @$result1[$ind]; 
	
	?>
	<tr>
		
		<?php for($y=0;$y<=1;$y++): ?>
			<td class="bor1">
					
					<div class="acct_no"><?php echo @$curr_acct->acct_no; ?></div>
					<div class="curr_date"><?php echo date('F d, Y'); ?></div>
				
					<p style="text-align:center;">
						REPUBLIKA NG PILIPINAS
						<br />
						TAGBINA WATER DISTRICT
						<br />
						Tagbina, Surigao del Sur
						<br />
						<strong>NOTICE OF DISCONNECTION</strong>
					</p>
					<p>
						<br />
						<div class="qq">MR./Mrs./Ms : </div>
						
						<div class="qq q1">
							<b><?php echo strtoupper(@$curr_acct->fname.' '.@$curr_acct->lname.' '); ?></b>
							<br />
							<?php echo strtoupper(@$curr_acct->address1); ?>
						</div>
					</p>
					
					<div class="mssg11">
						<p>
							We would like to inform you that you have an overdue account with us
							amounting to <b>P<?php echo number_format(@$curr_acct->ttl_bal,2); ?></b> corresponding supply of water you have contracted
							with us.
						</p>
						<p>
							We request that you settle this account on or before <b><?php echo $n_date; ?></b>,
							anytime between <b>8:00 A.M. and 5:00 P.M.</b>
						</p>
						<p>
							Otherwise, we will be constrained much to our regret to disconnect your water
							service without any further notice.
						</p>

						<p>
							A reconnection fee of <b>P75.00</b> is also required for disconnection services prior to
							reconnection.
						</p>
						
						<p>
							If payment has been made, please disregard this notice and accept our thanks.
						</p>
						<br />

						<p>
							Sincerly 
							<br />
							ILDEFONSO C. ALBARRACIN
							<br />
							General Manager D
						</p>
					</div>
					
						
			</td>
			<?php    endfor; ?>
	</tr>
	
	<?php $ind++; $sleep1++; endfor; ?>

</table>

<?php
	
	if($sleep1 >= 3){
		//~ break;
		//~ sleep(1);
		//~ $sleep1 = 0;
	}
		

	if(empty(@$result1[$ind])){
		break;
	}
	
?>


<div class="page_break"></div>


<?php 

endfor; ?>

<style>
.page_break { page-break-before: always; }

html{
	margin:0;
	padding:0;
	font-size:11px;
}
p{
	padding:0;
	margin:0;
}
table.tab1{
	width:100%;
}
.bor1{
	height:320px;
	border:1px solid #000;
	width:50%;
	overflow:hidden;
	vertical-align:top;
	padding:5px;
	position:relative;
}
.qq{
	display:inline-block;
	padding-left:10px;
	border:0px solid #000;
	text-align:left;
	vertical-align:top;
}
.q1{
	width:280px;
}
.mssg11{
	padding:10px;
}
.mssg11 p{
	margin-bottom:5px;
	line-height:100%;
	text-align:justify;
}
.acct_no{display:inline-block;position:absolute;left:0;padding-left:10px;}
.curr_date{display:inline-block;position:absolute;right:0;padding-right:10px;}
</style>
