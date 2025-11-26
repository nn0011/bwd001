
<div class="add_personel_form" style="display:none;">
	<div style="padding:15px;">

            <div>
                <h3>Add Personel </h3>
                <hr />
                <div class="flex">
                    <input type="text" class="form-control personel_name" placeholder="Personel Name" style="width:300px;" /> 
                    <button class="form-control btn btn-primary" 
                                style="width:100px;margin-top:10px;" onclick="add_personel_submit()">Save</button>
                </div>
                <br />
                <div>
                    <table class="table10 table-bordered  table-hover tab_personel">

                    </table>
                </div>

                
            </div>  
            <br />
            <br />

			<div class="cmd_buttons1">
				<!-- <button class="form-controlx" onclick="pop_close()">Close</button> -->
			</div>
			
		</div>
		
	</div>		
</div>
<!--  -->
<!--  -->



<div class="add_meter_form" style="display:none;">
	<div style="padding:15px;">

            <div>
                <h3>Add Water Meter </h3>
                <hr />
                <input type="hidden" class="is_new" value="yes" />
                <input type="hidden" class="meter_id" value="0" />
                <input type="text" class="form-control meter_num" placeholder="Meter #" /> 
                
                <select class="form-control brand_select">
                    <option value="">Select Meter Brand</option>
                    <?php foreach($meter_brand as $m): ?>
                    <option value="<?php echo $m->meta_name ?>"><?php echo $m->meta_name ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" class="form-control brand_text" placeholder="New Brand Name" />

                <select class="form-control size_select">
                    <?php foreach($meter_size as $m): ?>
                    <option value="<?php echo strtoupper($m->meta_name) ?>"><?php echo $m->meta_name ?></option>
                    <?php endforeach; ?>
                </select>
                
            </div>  
            <br />
            <br />
            <br />

			<div class="cmd_buttons1">
				<button class="form-controlx" onclick="save_new_water_meter()">Save</button>
				<button class="form-controlx" onclick="pop_close()">Close</button>
			</div>
			
		</div>
		
	</div>		
</div>


<!--  -->
<!--  -->


<div class="assign_meters" style="display:none;">
	<div style="padding:15px;">

            <div>
                <h3>Assign Meter </h3>
                <div style="display: flex;">
                    <input type="text" placeholder="Account #" class="form-control acct_no" />
                    <input type="text" placeholder="Last Name" class="form-control lname" />
                    <input type="text" placeholder="First Name" class="form-control fname" />
                </div>
                <button class="form-control btn btn-success" onclick="assign_meter_search_account()">Search</button>
                <br />
                <div class="acct_search_result"></div>
            </div>  
            <br />
            <br />
            <br />

			<div class="cmd_buttons1">
				<!-- <button class="form-controlx">Save</button>
				<button class="form-controlx" onclick="pop_close()">Close</button> -->
			</div>
			
		</div>
		
	</div>		
</div>


<div class="view_meter_history_pop" style="display:none;">
	<div style="padding:15px;">

            <div>
                <!-- <h3>View History</h3> -->
                <!-- <br /> -->
                <div class="acct_search_result">
                
                    <strong>Meter Info</strong> <small class="edit-but" onclick="edit_meter_info()">Edit</small> <br />
                    <table class="table10 table-bordered  table-hover">
                        <tr>
                            <td>Meter #</td>
                            <td  class="meter_no">-----</td>
                        </tr>
                        <tr>
                            <td>Brand Name</td>
                            <td  class="meter_brand">-----</td>
                        </tr>
                        <tr>
                            <td>Meter Size</td>
                            <td  class="meter_size">-----</td>
                        </tr>

                    </table>

                    <br />
                    <small class="edit-but green">Add Remarks</small>
                    <div class="history_cont">Please wait..</div>
                


                </div>
            </div>  
            <br />
            <br />
            <br />

			<div class="cmd_buttons1">
				<!-- <button class="form-controlx">Save</button>
				<button class="form-controlx" onclick="pop_close()">Close</button> -->
			</div>
			
		</div>
		
	</div>		
</div>


<!--  -->
<!--  -->
<!--  -->
<!--  -->

<div class="assign_meters_to" style="display:none;">
	<div style="padding:15px;">

            <div>
                <h3>Assign Meter</h3>
                <div style="display: ;">

                    <div>
                        <table class="table10 table-bordered  table-hover">
                            <tr>
                                <td>ACCT #</td>
                                <td class="acct_no">-----</td>
                            </tr>

                            <tr>
                                <td>Name</td>
                                <td  class="full_name">-----</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td  class="address">-----</td>
                            </tr>

                            <tr>
                                <td>Meter #</td>
                                <td  class="meter_no">-----</td>
                            </tr>

                            <tr>
                                <td>Brand Name</td>
                                <td  class="meter_brand">-----</td>
                            </tr>

                        </table>
                    </div>

                    <br />
                    <br />

                    <select class="form-control personel_select">
                        <option value="">Select Personel</option>
                    </select>

                    <select class="form-control type_select">
                        <option value="">Type</option>
                        <option value="installed">Installed</option>
                        <option value="disconnect">Disconnect</option>
                        <option value="reconnect">Reconnect</option>
                    </select>
                    
                    <input type="text" class="form-control date_trx" placeholder="Date" value="<?php echo date('d-m-Y'); ?>" />

                </div>
                <button class="form-control btn btn-success" onclick="assign_meter_submit_form()">Save</button>
                <br />
                <!-- <div class="acct_search_result"></div> -->
            </div>  
            <br />
            <br />
            <br />

			
		</div>
		
	</div>		
</div>
<!--  -->
<!--  -->
<!--  -->
<!--  -->

<div class="add_edit_remarks_pop" style="display:none;">

	<div style="padding:15px;">

        <h3>Add/Edit Remarks</h3>
        <br />
        <textarea class="form-control remaks_text" style="resize: none;height:200px;" placeholder="Remarks here."></textarea>
        <input type="hidden" class="history_id" value="0" />
        <br />
        <small class="edit-but green" onclick="save_remarks_by_history()">Save</small>
        <small class="edit-but red" onclick="save_remarks_by_history__pop_close()">Cancel</small>

	</div>		
</div>
<!--  -->
<!--  -->
<!--  -->
<!--  -->

