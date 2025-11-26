			<input type="hidden" name="_token" value="<?php echo  csrf_token() ?>">
					
			<div class="name_fileds">
				<input type="text" class="form-control" autocomplete="off" placeholder="Last Name" name="lname" id="new_acc_1">
				<input type="text" class="form-control" autocomplete="off" placeholder="First Name" name="fname" id="new_acc_2">
				<input type="text" class="form-control" autocomplete="off" placeholder="Middle" name="mi" id="new_acc_3">
			</div>
			
			 <input type="text" class="form-control" autocomplete="off" placeholder="Phone" name="phone" id="new_acc_5">
			 <input type="text" class="form-control" autocomplete="off" placeholder="Address 1" name="address1" id="new_acc_6">
			
			<ul class="zone_check">
				<?php foreach($zones as $zz): ?>
				<li><input type="checkbox"  value="<?php echo $zz['id']; ?>"   name="zone_ass[]"   id="ck1_<?php echo $zz['id']; ?>"/> <?php echo $zz['zone_name']; ?></li>
				<?php endforeach; ?>
			</ul>
			 
			 <br>
			 <br>
			
			<select class="form-control" name="status" id="new_acc_10">
				<option value="active">Active</option>
				<option value="inactive">Inactive</option>
			</select>
			
			<br>
			<br>

			<button type="submit">Save</button>
				&nbsp;&nbsp;&nbsp;
			<button>Cancel</button>
