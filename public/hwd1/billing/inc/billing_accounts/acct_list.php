

					<div  class="filter_1">
						<input type="text"  placeholder="Account Number" />
						<input type="text"  placeholder="Meter Number" />
						<input type="text"  placeholder="Last Name" />
						<select>
							<option>Zone A</option>
						</select>

						<img src="img/search.jpg" class="but_filter" />

					</div>
					<br />

					<div class="scroll1">
						<table class="table10 table-bordered  table-hover">
							<tr  class="headings">
								<td width="10%">Account No.</td>
								<td width="10%">Meter No.</td>
								<td width="50%">Name</td>
								<td width="10%">Status</td>
							</tr>
							<?php for($x=0;$x<=20;$x++): ?>
							<!------>
							<!------>
							<tr  onclick=""  data-index="<?php echo $x; ?>"  data-box1="new_initiative"  class="cursor1  trig1">
								<td>00112233</td>
								<td>1155644 - 22</td>
								<td style="padding:10px;">
										<span style="font-size: 16px;">Dela Cruz, Jimmy</span>
										<p>11 Golden Pheasant Street, Barangay Juan Dela Cerna, Lianga , Surigao del Sur</p>
								</td>
								<td>Active / <span class="rd"> Inactive</span></td>
							</tr>
							<!------>
							<!------>
							<?php  endfor; ?>

						</table>
					</div>

					<div style="padding:15px;">
							<ul class="pagination pagination-sm">
							  <li><a href="#">PREVIOUS</a></li>
							  <li><a href="#">1</a></li>
							  <li><a href="#">2</a></li>
							  <li><a href="#">3</a></li>
							  <li><a href="#">4</a></li>
							  <li><a href="#">5</a></li>
							  <li><a href="#">NEXT</a></li>
							</ul>
					</div>
