
<strong><?php echo $zone_lbl; ?></strong>
<br />
<strong>as of <?php echo $curr_date_label; ?></strong>
<br />
<strong>Total Account : <?php echo $acct_counts; ?></strong>
<div class="txt_1" style="text-align:right;">
     <a href="<?php echo @$pdf_link; ?>" target="_blank">Download PDF</a>
</div>
<table class="table10 table-bordered  table-hover"><tbody>

     <tr class="headings">
     	<td width="5%">Account # </td>
     	<td width="5%">Name</td>
          <td width="5%">A/R Others</td>
          <td width="5%" class="t_right">Current</td>
          <?php foreach($new_arr as $kk => $vv): ?>
          <td width="5%" class="currency t_right"><?php echo $lab_days[$kk]; ?></td>
          <?php endforeach; ?>
          <td width="5%" class="currency t_right">More than <?php echo $lab_days[$kk]; ?></td>
          <td width="5%"  class="currency t_right">WB Total</td>
     </tr>

     <?php
     
     foreach($acct_result as $acct):
      foreach($acct as $acc):
          $total_x  = 0;
          $acc =(object) $acc;

          $aging_data = (array) json_decode($acc->ageing_data);
          $data1 = (array) json_decode($acc->data1);
          
          $coll  = getCollection(@$data1['billing_id']);
          $current_remaining_bill  = $acc->billing_total - $coll;
          
     ?>
     <tr>
          <td ><?php echo $acc->account_num; ?></td>
          <td ><?php echo $acc->full_name; ?></td>
          <td>&nbsp;</td>
          <td class="t_right"><?php echo number_format($current_remaining_bill, 2); ?></td>
          <?php foreach($new_arr as $kk => $vv): ?>
          <?php

          ?>
          <td class="currency t_right">&#x20b1; <?php  echo @$aging_data[$kk]; ?></td>
          <?php endforeach;

          ?>
          <td class="currency">&#x20b1; <?php

                    $more_than_total = 0;

                    foreach($ageing_prev as $kk=>$vv)
                    {
                         $more_than_total+=@$aging_data[$kk];
                    }
                    echo number_format($more_than_total, 2);
           ?></td>
          <td class="currency t_right">&#x20b1;<?php
               $grand_total = 0;
               foreach($aging_data as $ag1)
               {
                    $grand_total+= $ag1;
               }
               
               $grand_total+= $current_remaining_bill;
               echo number_format($grand_total, 2);

          ?></td>
     </tr>
     <?php endforeach; ?>
     <?php endforeach; ?>

</tbody></table>
