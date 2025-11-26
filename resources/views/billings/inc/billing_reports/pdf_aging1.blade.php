<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="/css/admin.css" rel="stylesheet">
	<title>Sales View</title>

<style>
.table10{width:100%;}
.table10 td{
	font-size:12px;
	padding:10px;
    padding-top: 3px;
    padding-bottom: 3px;
}
.table10  .headings td{
	color:white;
	background:#108479;
	text-align:left;
    /*border-right: 1px solid #fff;*/
    padding: 10px;
    font-size: 12px;
}

.table10 td {
    font-size: 12px;
    padding: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ccc;
}
.table10{
     max-width: 1024px;
     margin: 0 auto;
     border: 1px solid #ccc;
}

</style>

</head>
<body>

<table class="table10 table-bordered  table-hover"><tbody>

     <thead>
          <tr class="headings">
          	<td width="5%">Account # </td>
          	<td width="5%">Name</td>
               <td width="5%">A/R Others</td>
               <td width="5%">Current</td>
          </tr>
     </thead>

     <?php
      foreach($report_aging as $acc):
          $total_x  = 0;

     ?>
     <tr>
     	<td ><?php echo $acc->account_num; ?></td>
     	<td><?php echo $acc->full_name; ?></td>
          <td>&nbsp;</td>
          <td>Php <?php echo number_format($acc->billing_total, 2); ?></td>
     </tr>
     <?php endforeach; ?>

</tbody></table>

</body>
</html>
