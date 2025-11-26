<h3>Ageing of Account Recievable - Summary</h3>

<strong><?php echo $zone_lbl; ?></strong>
<br />
<strong>as of <?php echo $curr_date_label; ?></strong>
<br />
<div class="txt_1" style="text-align:right;">
     <a href="<?php echo @$pdf_link; ?>" target="_blank">Download PDF</a>
</div>
<h3>Active</h3>
<table class="table10 table-bordered  table-hover"><tbody>

     <tr class="headings">
     	<td width="5%">&nbsp;</td>
          <td width="5%">A/R Others</td>
          <td width="5%" class="t_right">Current</td>
          <?php foreach($new_arr as $kk => $vv): ?>
          <td width="5%" class="currency t_right"><?php echo $lab_days[$kk]; ?></td>
          <?php endforeach; ?>
          <td width="5%" class="currency t_right">More than <?php echo $lab_days[$kk]; ?></td>
          <td width="5%"  class="currency t_right">WB Total</td>
     </tr>

     <?php
     $total_1 = array();

     foreach($acct_type1 as $at1):
          $kk001 = 'type'.$at1['id'].date('Ymd',strtotime($curr_period));
          $current_bill = (float) @$ff_data[$kk001]['bill'];
          $sub_total_bill =  $current_bill;
          $total_x = 0;
          @$total_1[$total_x] += $current_bill;
          $total_x++;
     ?>
     <tr>
     	<td width="5%"><?php echo $at1['meta_name']; ?></td>
          <td width="5%">-</td>
          <td width="5%" class="t_right"><?php echo number_format($current_bill, 2); ?></td>
          <?php foreach($period_set as $kk => $vv):
               $kk11  = 'type'.$at1['id'].date('Ymd',strtotime($vv));
               $billx =  (float) @$ff_data[$kk11]['bill'];
               $sub_total_bill+=$billx;

               @$total_1[$total_x] += $billx;
               $total_x++;


          ?>
          <td width="5%" class="currency t_right"><?php echo number_format($billx,2); ?></td>
          <?php endforeach; ?>
          <td width="5%" class="currency t_right"><?php

               $kk11 = 'type_m_'.$at1['id'];
               $billx =  (float) @$ff_data[$kk11]['bill'];
               $sub_total_bill+=$billx;

               @$total_1[$total_x] += $billx;
               $total_x++;

               @$total_1[$total_x] += $sub_total_bill;
               $total_x++;

               echo number_format($billx,2);

          ?></td>
          <td width="5%"  class="currency t_right"><?php echo number_format($sub_total_bill,2); ?></td>
     </tr>
     <?php

     endforeach;

     $total_x = 0;

     ?>

     <tr class="ageing_sub_total1">
     	<td width="5%">Sub Total</td>
          <td width="5%">-</td>
          <td width="5%" class="t_right"><?php echo number_format($total_1[$total_x], 2); $total_x++; ?></td>
          <?php foreach($period_set as $kk => $vv):
          ?>
          <td width="5%" class="currency t_right"><?php echo number_format($total_1[$total_x], 2); $total_x++; ?></td>
          <?php endforeach; ?>
          <td width="5%" class="currency t_right"><?php

                echo number_format($total_1[$total_x], 2); $total_x++;

          ?></td>
          <td width="5%"  class="currency t_right"><?php echo number_format($total_1[$total_x], 2); ?></td>
     </tr>

</tbody></table>
<br />
<hr />

<h3>Disconnected</h3>

<table class="table10 table-bordered  table-hover"><tbody>

     <tr class="headings">
     	<td width="5%">&nbsp;</td>
          <td width="5%">A/R Others</td>
          <td width="5%" class="t_right">Current</td>
          <?php foreach($new_arr as $kk => $vv): ?>
          <td width="5%" class="currency t_right"><?php echo $lab_days[$kk]; ?></td>
          <?php endforeach; ?>
          <td width="5%" class="currency t_right">More than <?php echo $lab_days[$kk]; ?></td>
          <td width="5%"  class="currency t_right">WB Total</td>
     </tr>

     <?php
     $total_1 = array();

     foreach($acct_type1 as $at1):

          $kk001 = 'type'.$at1['id'].date('Ymd',strtotime($curr_period));
          $current_bill = (float) @$ff_data_discon[$kk001]['bill'];
          $sub_total_bill =  $current_bill;
          $total_x = 0;

          @$total_1[$total_x] += $current_bill;
          $total_x++;
     ?>
     <tr>
     	<td width="5%"><?php echo $at1['meta_name']; ?></td>
          <td width="5%">-</td>
          <td width="5%" class="t_right"><?php echo number_format($current_bill, 2); ?></td>
          <?php foreach($period_set as $kk => $vv):
               $kk11  = 'type'.$at1['id'].date('Ymd',strtotime($vv));
               $billx =  (float) @$ff_data_discon[$kk11]['bill'];
               $sub_total_bill+=$billx;

               @$total_1[$total_x] += $billx;
               $total_x++;


          ?>
          <td width="5%" class="currency t_right"><?php echo number_format($billx,2); ?></td>
          <?php endforeach; ?>
          <td width="5%" class="currency t_right"><?php

               $kk11 = 'type_m_'.$at1['id'];
               $billx =  (float) @$ff_data_discon[$kk11]['bill'];
               $sub_total_bill+=$billx;

               @$total_1[$total_x] += $billx;
               $total_x++;

               @$total_1[$total_x] += $sub_total_bill;
               $total_x++;

               echo number_format($billx,2);

          ?></td>
          <td width="5%"  class="currency t_right"><?php echo number_format($sub_total_bill,2); ?></td>
     </tr>
     <?php

     endforeach;

     $total_x = 0;

     ?>

     <tr class="ageing_sub_total1">
     	<td width="5%">Sub Total</td>
          <td width="5%">-</td>
          <td width="5%" class="t_right"><?php echo number_format($total_1[$total_x], 2); $total_x++; ?></td>
          <?php foreach($period_set as $kk => $vv):
          ?>
          <td width="5%" class="currency t_right"><?php echo number_format($total_1[$total_x], 2); $total_x++; ?></td>
          <?php endforeach; ?>
          <td width="5%" class="currency t_right"><?php

                echo number_format($total_1[$total_x], 2); $total_x++;

          ?></td>
          <td width="5%"  class="currency t_right"><?php echo number_format($total_1[$total_x], 2); ?></td>
     </tr>

</tbody></table>
