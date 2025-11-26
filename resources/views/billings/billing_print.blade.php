<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->
</head>
<body>

    <br />
    <br />
    <table cellspacing="0" cellpadding="0" class=" tab1">
        <tr>
            <td colspan="3" class="cc" style="font-size: 14px;font-weight:bold;padding:10px;padding-top:20px;">
                <img src="/hinatuan-logo.jpg"  style="width: 200px;"/>
            </td>
        </tr>

        <tr>
            <td colspan="3" class="cc" style="font-size: 14px;font-weight:bold;padding:10px">
                STATEMENT OF ACCOUNT
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px dashed #000;" colspan="3"></td>
        </tr>


        <tr>
            <td>Account No.</td>
            <td colspan="2"><?php echo $curr_bill['account']['acct_no']; ?></td>
        </tr>

        <tr>
            <td>Name</td>
            <td colspan="2"><?php echo $curr_bill['account']['fname'].' '.$curr_bill['account']['lname'].$curr_bill['account']['mi']; ?></td>
        </tr>

        <tr>
            <td>Address</td>
            <td colspan="2"><?php echo $curr_bill['account']['address1']; ?></td>
        </tr>


        <tr>
            <td>Meter Number / Size</td>
            <td colspan="2"><?php echo $curr_bill['account']['meter_number1'].' / '.@$meter_sizes[$curr_bill['meter_size_id']]['meta_name']; ?></td>
        </tr>
        <tr>
            <td>Consumer Type</td>
            <td colspan="2"><?php echo  @$acct_types[$curr_bill['acct_type']]['meta_name']; ?></td>
        </tr>
        <tr>
            <td>Zone/Sequence</td>
            <td colspan="2"> <?php echo $zones[$curr_bill['account']['zone_id']]['zone_name']; ?> / {{$curr_bill['acct_type']}}</td>
        </tr>

        <tr>
            <td style="border-bottom: 1px dashed #000;" colspan="3"></td>
        </tr>


        <tr>
            <td></td>
            <td>Reading Date</td>
            <td class="rr">Reading</td>
        </tr>

<?php 

$read_PC = array_filter(explode('||', $curr_bill['read_PC']));

?>        
        <tr>
            <td>Present</td>
            <td class="ll"><?php echo date('M d, Y', strtotime($curr_bill['reading1']['curr_read_date'])); ?></td>
            <td class="rr"><?php echo $read_PC[0]; ?></td>
        </tr>
        <tr>
            <td>Previous</td>
            <td class="ll"><?php echo date('M d, Y', strtotime($curr_bill['reading1']['prev_read_date'])); ?></td>
            <td class="rr"><?php echo $read_PC[1]; ?></td>
        </tr>

        <tr>
            <td>CU.M</td>
            <td></td>
            <td class="rr"><?php echo $curr_bill['reading1']['current_consump']; ?></td>
        </tr>

 
        <tr>
            <td style="border-bottom: 1px dashed #000;" colspan="3"></td>
        </tr>

<?php 

$my_current_bill = $curr_bill['curr_bill'];
$my_current_arrear = $curr_bill['arrears'];
$my_discount = $curr_bill['discount'];

$acct_type = $curr_bill['acct_type'];

if( in_array($acct_type, GOV_TYPE) ) {
    $my_penalty = (($curr_bill['curr_bill'] - $my_discount) * PENALTY_PERCENT_GOV);
} else {
    $my_penalty = (($curr_bill['curr_bill']  - $my_discount) * PENALTY_PERCENT);
}

// COMBINATION OF ARREAR AND BILLING
if( $typ1 == 2 ) {
    $my_current_bill = ( $my_current_bill+$my_current_arrear );  
    $my_current_arrear = 0;
}


$penalty_date = date('m/d/Y', $due_date_time = strtotime(@$reading_info['fine_dates'][$curr_bill['reading1']['zone_id']]));

?>


        <tr>
            <td>Current Bill</td>
            <td></td>
            <td class="rr"><?php echo number_format($my_current_bill, 2); ?></td>
        </tr>        

        <tr>
            <td>Arrear</td>
            <td></td>
            <td class="rr"><?php echo number_format($my_current_arrear, 2); ?></td>
        </tr>
        
        
        <tr>
            
            <td>Senior</td>
            <td></td>
            <td class="rr"><?php echo number_format($my_discount , 2); ?></td>
        </tr>        

        <tr>
            <td style="border-bottom: 1px dashed #000;" colspan="3"></td>
        </tr>

        <tr>
            <td>DUE Date</td>
            <td></td>
            <td class="rr"><?php echo date('m/d/Y', $due_date_time = strtotime(@$reading_info['due_dates2'][$curr_bill['reading1']['zone_id']])); ?></td>
        </tr>        
        <tr>
            <td colspan="2">Penalty - <?php echo $penalty_date; ?></td>
            <td class="rr"><?php echo  number_format($my_penalty, 2); ?></td>
        </tr> 

<?php 

// $my_penalty
// $my_current_bill
// $my_current_arrear
// $my_discount


?>        
        
        <tr>
            <td>TOTAL</td>
            <td></td>
            <td class="rr"><?php echo number_format(( $un_due_ttl = ($my_current_bill+ $my_current_arrear) - $my_discount), 2); ?></td>
        </tr>        
        <tr>
            <td>After Due Date </td>
            <td></td>
            <td class="rr"><?php echo number_format(( $un_due_ttl + $my_penalty ), 2); ?></td>
        </tr>        

        <tr>
            <td style="border-bottom: 1px dashed #000;" colspan="3"></td>
        </tr>

        <tr>
<td colspan="3">
<pre style="font-size: 6px;line-height: 100%;background:none;">
NOTICE
1. Please bring this on when making payment to our office
2. A five-day grace period from due date shall be granted 
   before your water service shall be discontinued

IMPORTANT
1. A 10% penalty charge is added to bill unpaid after due date
2. Reconnection charge of P200 and full payment of arrears shall 
   be made before a disconnected service is re-activated 
3. In case water meter installed was broken / damage the consumer 
   is liable to pay the replacement cost and failure to do so the service 
   shall be disconnected without further notice 
4. if payment is made by check please make the check payable to 
    <?php echo WD_NAME; ?>.
</pre>    
<br />
<p style="text-align:center;padding:0;margin:0;">
    DISCONNECTION DATE
    <br />

<?php 

echo date('M d, Y', strtotime(date('Y-m-d', $due_date_time).' +5 days'));

?>    

</p>
<p class="cc" style="padding: 15px;font-size:8px;">
NOTE: THIS RECIEPT ONLY SHOW YOUR CURRENT BILL
<br />
PLEASE ASK THE TELLER FOR YOUR REMAINING BALANCE
</p>
<p class="cc" style="padding:0px;font-size:8px;">
    Meter Reader : 
<?php 

if( empty( $curr_bill['reading1']['officer_id'] ) ) {
    echo 'Billing Administrator';
}else{
    echo $officer[$curr_bill['reading1']['officer_id']]['name'];
}

?>
    <br />
    Thank you for on-time payment
</p>

</td>            
        </tr>


    </table>

    <br />
    <br />
    <br />


<style>
body{
    font-size: 10px;
    line-height: 110%;    
}    
.tab1{
    width: 300px;
    border:1px solid #ccc;
    margin:0 auto;
    margin-top:30px;
}
.tab1 td{
    padding:3px;
}
.MAIN1{
    width:400px;
    border:1px solid #000;
    min-height: 100px;
    padding:15px;
}    
.ll{text-align: left;}
.rr{text-align: right;}
.cc{text-align: center;}
.jj{text-align: justify;}
</style>
</body>
</html>
