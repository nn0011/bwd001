<?php 


// echo '<pre>';
// print_r($recent_water_meter->toArray());

?>

<?php if($accounts->count() == 0): ?>
    No result found.
<?php return; endif; ?>

<table class="table10 table-bordered  table-hover">
<tbody>

<?php 
/* 
<tr class="headings">
    <td width="10%">Acct #</td>
    <td width="10%">First Name</td>
    <td width="10%">Last Name</td>
    <td width="10%"></td>
    <td width="10%"></td>
    <td width="10%"></td>
</tr>
*/ ?> 

<?php 
$index = 0; 
foreach($accounts as $rwm): ?>
<tr onclick="" class="cursor1">
    <td><input type="radio" name="assign_me" value="<?php echo $index; ?>" /></td>
    <td><?php echo $rwm->acct_no; ?></td>
    <td><?php echo $rwm->fname; ?></td>
    <td><?php echo $rwm->lname; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
<?php $index++; endforeach; ?>
        
</tbody></table>

<button class="form-control btn btn-success" onclick="assign_to_account()">Assign</button>