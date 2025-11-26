
<div  style="position:relative;">
     <?php  /*if($result1->count() != 0){ ?>
     <div class="txt_1" style="text-align:right;">
          <a href="<?php echo @$pdf_link; ?>" target="_blank">Download PDF</a>
     </div>
     <?php } /**/ ?>
     <table class="table10 table-bordered table-hover"><tbody>

          <tr class="headings">
               <td width="5%">Account No.</td>
               <td width="5%">Concessionaires</td>
               <td width="5%" class="txt_r">Bill Number</td>
               <td width="5%" class="txt_r">Previous Reading</td>
               <td width="5%" class="txt_r">Current Reading</td>
               <td width="5%" class="txt_r">Usage CUM</td>
               <td width="5%" class="txt_r">Classified</td>
               <td width="5%" class="txt_r">Water Sales</td>
               <td width="5%" class="txt_r">Meter maint. Fee</td>
               <td width="5%" class="txt_r">Meter Rental Fee</td>
               <td width="5%" class="txt_r">A/R others</td>
               <td width="5%" class="txt_r">Penalty</td>
               <td width="5%" class="txt_r">Arrears</td>
               <td width="5%" class="txt_r">Amount Due</td>
          </tr>

          <?php foreach($billing1 as $bb1):

               $full_name = $bb1->account->lname.', '.
     				   $bb1->account->fname.' '.
     				   $bb1->account->mi;

               $read_ifo = explode('||', $bb1->read_PC);

               //$str = substr("0000{$read_ifo[0]}", -4);

               ?>
          <tr>
               <td><?php echo $bb1->account->acct_no; ?></td>
               <td><?php echo $full_name; ?></td>
               <td><?php echo $bb1->id; ?></td>

               <td class="txt_r"><?php echo substr("0000{$read_ifo[0]}", -4); ?></td>
               <td class="txt_r"><?php echo substr("0000{$read_ifo[1]}", -4); ?></td>

               <td class="txt_r"><?php echo $bb1->consumption; ?></td>
               <td class="txt_r">Classified</td>
               <td class="txt_r"><?php echo number_format($bb1->billing_total, 2); ?></td>
               <td class="txt_r">---</td>
               <td class="txt_r">---</td>
               <td class="txt_r">---</td>
               <td class="txt_r">---</td>
               <td class="txt_r">---</td>
               <td class="txt_r">---</td>
          </tr>
     <?php endforeach; ?>


     </tbody></table>
</div>
<?php

// echo $zone_arr[$result1->zone_orig];
// echo '<pre>';
// print_r($result1->toArray());

?>
