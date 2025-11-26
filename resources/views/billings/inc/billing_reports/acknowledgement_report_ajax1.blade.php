<strong>as of <?php echo $current_date_label; ?> </strong>
<br />
<br />

<div  style="position:relative;">
     <?php  if($result1->count() != 0){ ?>
     <div class="txt_1" style="text-align:right;">
          <a href="<?php echo @$pdf_link; ?>" target="_blank">Download PDF</a>
     </div>
     <?php } /**/ ?>
     <table class="table10 table-bordered table-hover"><tbody>

          <tr class="headings">
               <td width="5%">Account No.</td>
               <td width="5%">Concessionaires</td>
               <td width="5%" class="txt_r">Bill Number</td>
               <td width="5%" class="txt_r">Usage CUM</td>
               <td width="5%" class="txt_r">Billed Ammount</td>
               <td width="5%">Remarks</td>
          </tr>

          <?php if($result1->count() != 0): ?>
          <?php foreach($result1 as $rr1):

               $dd1 = null;

               if(!empty($rr1->data1)){
                    $dd1 = json_decode($rr1->data1);
               }

               // echo '<pre>';
               // print_r($dd1);
               // die();

          ?>
          <tr>
               <td><?php echo $rr1->account_num; ?></td>
               <td><?php echo $rr1->full_name; ?></td>
               <td class="txt_r"><?php echo @$dd1->billing_id; ?></td>
               <td class="txt_r"><?php echo @$dd1->cum; ?></td>
               <td class="txt_r"><?php echo number_format($rr1->billing_total, 2); ?></td>
               <td>---</td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
               <tr>
                    <td colspan="6" style="padding:20px;font-size:18;text-align:center;">No Billing as of <?php echo $current_date_label; ?> </td>
               </tr>
          <?php endif; ?>


     </tbody></table>
</div>
<?php

// echo $zone_arr[$result1->zone_orig];
// echo '<pre>';
// print_r($result1->toArray());

?>
