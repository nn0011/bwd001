<strong>as of <?php echo $current_date_label; ?> </strong>
<br />
<br />

<div  style="width:800px;position:relative;">
     <?php  if($result1){ ?>
     <div class="txt_1" style="text-align:right;">
          <a href="<?php echo @$pdf_link; ?>" target="_blank">Download PDF</a>
     </div>
     <?php } ?>
     <table class="table10 table-bordered table-hover"><tbody>

          <tr class="headings">
               <td width="5%">Zone</td>
               <td width="5%">Concessionaires</td>
               <td width="5%">Arrears</td>
          </tr>

          <?php if($result1): ?>
          <tr>
               <td><?php echo $zone_arr[$result1->zone_orig]; ?></td>
               <td><?php echo $result1->acct_with_balance; ?></td>
               <td><?php echo number_format($result1->total_balance, 2); ?></td>
          </tr>
          <?php else: ?>
               <tr>
                    <td colspan="3" style="padding:20px;font-size:18;text-align:center;">No Billing as of <?php echo $current_date_label; ?> </td>
               </tr>
          <?php endif; ?>


     </tbody></table>
</div>
<?php

// echo $zone_arr[$result1->zone_orig];
// echo '<pre>';
// print_r($result1->toArray());
?>
