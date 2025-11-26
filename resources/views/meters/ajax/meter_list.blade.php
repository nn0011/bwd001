<?php 


// echo '<pre>';
// print_r($recent_water_meter->toArray());

?>

<?php if($recent_water_meter->count() == 0): ?>
    No result found.
<?php return; endif;  ?>

<table class="table10 table-bordered  table-hover">
<tbody>
    
<tr class="headings">
    <td width="10%">Meter #</td>
    <td width="10%">Brand Name</td>
    <td width="10%">Meter Size</td>
    <td width="30%">Current Status</td>
    <td width="20%">Action</td>
</tr>

<?php 
$index = 0;
foreach($recent_water_meter as $rwm): ?>
<tr onclick="" class="cursor1">
    <td><?php echo $rwm->meter_num; ?></td>
    <td><?php echo $rwm->brand_name; ?></td>
    <td><?php echo $rwm->meter_size; ?></td>
    <td>
        <?php 
            if( !empty($rwm->last_history)  ):    
        ?>
                <?php if( $rwm->last_history->typ != 'DISCONNECT' ): ?>
                <strong><?php echo $rwm->last_history->typ;  ?></strong>
                <br />
                By : <?php echo $rwm->last_history->served_name;  ?>
                <br />
                <?php echo $rwm->last_history->account->acct_no.' - '.$rwm->last_history->account->lname.', '.$rwm->last_history->account->fname.' '.$rwm->last_history->account->mi; ?>
                <br />
                <?php echo $rwm->last_history->account->address1;  ?>
                <br />
                <?php echo date('F d, Y', strtotime($rwm->last_history->served_date));  ?>

                <?php else: ?>
                AVAILABLE - ( USED )
                <?php endif; ?>

            <?php else: ?>
            NEW 
            <?php endif; ?>
        </td>
    <td>
        <?php /*
        <button class="btn btn-success" 
        onclick="assign_meter_popup('<?php echo $index; ?>', '<?php echo $rwm->id; ?>', '<?php echo $rwm->meter_num; ?>')">Assign</button>
        */ ?>
        <?php /*
        <button class="btn btn-success">Disconnect</button>
        <button class="btn btn-success">Reconnect</button>
        */ ?>
        <?php /*
        <button class="btn btn-success" onclick="view_meter_history('<?php echo $index; ?>', '<?php echo $rwm->id; ?>', '<?php echo $rwm->meter_num; ?>')">Info</button>
        */ ?>

        <small class="edit-but green" onclick="assign_meter_popup('<?php echo $index; ?>', '<?php echo $rwm->id; ?>', '<?php echo $rwm->meter_num; ?>')">Assign</small>        
        <small class="edit-but blue"  onclick="view_meter_history('<?php echo $index; ?>', '<?php echo $rwm->id; ?>', '<?php echo $rwm->meter_num; ?>')">Info</small>        
</td>
</tr>
<?php $index++; endforeach; ?>
        
</tbody></table>
