<?php


// echo  '<pre>';
// print_r($discon_list[0]->toArray());
// die();

?>

<?php if(empty($discon_list)): ?>
	No For Disconnection List Found
<?php return; endif; ?>



<div class="lo11">

	<div>For Disconnection Zone 1</div>
	<div> as of <?php echo $date11 = date('F d, Y', strtotime($date1)); ?></div>
	<br />

	<div  class="with_scroll">
			<table style="width:100%;">
			<tbody>
				<tr class="head001">
				<td style="width:5%;">

				</td>
				<td style="width:10%;">Account #</td>
				<td style="width:50%;">Full name</td>
				<td style="width:10%;">Meter No.</td>
				<td style="width:10%;">Status</td>
				<td style="width:10%;text-align:center;">Total Balance</td>
			</tr>
			<?php foreach($discon_list as $d):

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
				<td><?php echo $d->acct_no; ?></td>
				<td><span><?php echo strtoupper($full_name); ?></span></td>
				<td><span><?php echo strtoupper($d->meter_number1); ?></span></td>
				<td><span>Active</span></td>
				<td class="tr1"><?php echo number_format($d->ledger_data3->ttl_bal,2); ?></td>
			</tr>
			<?php endforeach; ?>
			</tbody></table>

			<br />

	</div>

	<button onclick="disconnect_line1001()">Disconnect Line</button>
	<button onclick="disconnect_line1001()">Print for disconnection</button>


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
