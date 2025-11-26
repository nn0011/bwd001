<div class="txt_1" style="text-align:right;">
     <a href="<?php echo @$pdf_link; ?>" target="_blank">Download PDF</a>
</div>

<h3>as of <?php  echo $display_date;?></h3>
<table class="table10 table-bordered  table-hover"><tbody>

     <tr class="headings">
     	<td width="5%">Zone</td>
          <td width="5%">Account with balance</td>
          <td width="5%">Amount</td>
     </tr>
     <?php
     $arr_total = array(0,0);
     foreach($result1 as $rr1):
          $arr_total[0] +=  $rr1->acct_with_balance;
          $arr_total[1] +=  $rr1->total_balance;
     ?>
     <tr>
          <td><?php echo $zone_arr[$rr1->zone_orig]; ?></td>
          <td><?php echo $rr1->acct_with_balance; ?></td>
          <td>Php <?php echo number_format($rr1->total_balance, 2); ?></td>
     </tr>
     <?php endforeach; ?>
     <tr>
          <td>Total </td>
          <td><?php echo number_format($arr_total[0]); ?></td>
          <td>Php <?php echo number_format($arr_total[1], 2); ?></td>
     </tr>


</tbody></table>
