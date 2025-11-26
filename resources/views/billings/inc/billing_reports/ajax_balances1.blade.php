<div class="txt_1" style="text-align:right;">
     <a href="<?php echo @$pdf_link; ?>" target="_blank">Download PDF</a>
</div>
<div>
     Result: <?php echo $total_result; ?>
     <br />
     Zone : <?php echo $zone_lbl; ?>
</div>
<table class="table10 table-bordered  table-hover"><tbody>

     <tr class="headings">
     	<td width="5%">Account # </td>
     	<td width="5%">Name</td>
          <td width="5%">Meter Number</td>
          <td width="5%">A/R Others</td>
          <td width="5%">Current Bill</td>
          <td width="5%">Balance</td>
     </tr>

     <?php
     foreach($acct_result as $acct):
      foreach($acct as $acc):
          $total_x  = 0;
          $acc =(object) $acc;

          $aging_data = (array) json_decode($acc->ageing_data);

     ?>
     <tr>
          <td ><?php echo $acc->account_num; ?></td>
          <td ><?php echo $acc->full_name; ?></td>
          <td><?php echo $acc->meter_number1; ?></td>
          <td>&nbsp;</td>
          <td class="currency" >&#x20b1; <?php
          echo number_format($acc->billing_total, 2);

          ?></td>
          <td class="currency">&#x20b1; <?php
               $more_than_total = 0;
               foreach($aging_data as $kk=>$vv)
               {
                    $more_than_total+=$vv;
               }
               echo number_format($more_than_total, 2);
           ?></td>

     </tr>
     <?php endforeach; ?>
     <?php endforeach; ?>

</tbody></table>
