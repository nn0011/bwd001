<?php


//~ echo  '<pre>';
//~ print_r($discon_list[0]->toArray());
//~ die();

?>

<?php if(empty($discon_list)): ?>
	No Disconnection List Found
<?php return; endif; ?>

<div style="text-align:right;">
	<button onclick="print_notice_of_disconnection()">Print Notice of Disconnection</button>
	<br /><br />
	<input type="text"  placeholder="Date"  class="mm_disconn_date" />
	<br />
</div>





<div class="lo11">

	<div>Disconnection Notice List Zone 1</div>
	<div> as of <?php echo $date11 = date('F d, Y', strtotime($date1)); ?></div>

	<br />

	<div  class="with_scroll">
			<table style="width:100%;">
			<tbody>
				<tr class="head001">
				<td style="width:5%;">

				</td>
				<td style="width:10%;">Seq</td>
				<td style="width:10%;">Route #</td>
				<td style="width:10%;">Account #</td>
				<td style="width:50%;">Full name</td>
				<td style="width:10%;">Status</td>
				<td style="width:10%;text-align:center;">Total Balance</td>
			</tr>
			<?php

			$sk11 = true;

			if($acct_id != 'none'){
					$sk11 = false;
			}

			$yyy = 1;

			foreach($discon_list as $d):

				if($sk11 == false && $d->acct_no != $acct_id){
							continue;
				}else{
					$sk11 = true;
				}

				$full_name = $d->lname.', '.$d->fname;

				$json1_raw = array(
									'acct_id'=>$d->id,
									'bill_id'=>$d->bill1->id,
									'acct_no'=>$d->acct_no,
									'date1' => $date11
									);



			?>
			<tr>
				<td><input type="checkbox" name="mm[]"  onclick="on_click_me1(this)"
							value="<?php echo base64_encode(json_encode($json1_raw)); ?>"   class="for_dis11" /></td>
							<td><?php echo $yyy; ?></td>
							<td><?php echo $d->route_id; ?></td>
							<td><?php echo $d->acct_no; ?></td>
				<td><span><?php echo strtoupper($full_name); ?></span></td>
				<td><span>Active</span></td>
				<td class="tr1"><?php echo number_format($d->ledger_data3->ttl_bal,2); ?></td>
			</tr>
			<?php $yyy++; endforeach; ?>
			</tbody></table>

			<br />

	</div>

	<button onclick="make_for_disconnection()">For Disconnection</button>


</div>



<style>
.tr1{
	text-align:right;
}
.lo11{
	font-size:12px;
	width:700px;
}
.lo11 td{
	padding:5px;
	border:1px solid #ccc;
}
.with_scroll{
	border:1px solid #ccc;
}

.with_scroll {
    height: 400px;
    border: 1px solid #ccc;
    overflow-x: scroll;
    padding-right: 15px;
}

</style>
