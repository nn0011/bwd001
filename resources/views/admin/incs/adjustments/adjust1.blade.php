
<table class="table table-bordered table-hover table-sm" style="font-size:12px;">
  <thead>
    <tr>
      <th scope="col">Date</th>
      <th scope="col">Reff#</th>
      <th scope="col">Name</th>
      <th scope="col">Description</th>
      <th scope="col"  style="text-align:right;">Amount</th>
      <th scope="col"  style="text-align:center;">Status</th>
    </tr>
  </thead>
  <tbody>
	
	
	<?php foreach($bill_adj as $ba1): ?>
	<tr>
	  <td><?php echo date('F d, Y', strtotime($ba1->date1_stamp)); ?></td>
	  <td style="text-align:center;"><?php echo $ba1->id; ?></td>
	  <td>
		  <a>
		  <?php echo $ba1->acct->acct_no.' - '.$ba1->acct->fname.' '.$ba1->acct->lname; ?>
		  </a>
	  </td>
	  <td><?php echo $ba1->adj_typ_desc; ?></td>
	  <td style="text-align:right;">
		  <?php if($ba1->amount < 0 ): ?>
		  <span style="color:red;">
		  <?php else: ?>
		  <span>
		  <?php endif; ?>
			  <?php echo number_format($ba1->amount,2); ?>
		  </span>
	  </td>
	  <td style="text-align:center;"><?php echo strtoupper($ba1->status); ?></td>
	</tr>
	<?php endforeach; ?>
    
    
  </tbody>
</table>

<?php 

 //~ $bill_adj->links(); 

?>
<?php 

$pages_me = $bill_adj->render(); 
echo $pages_me;

?>

