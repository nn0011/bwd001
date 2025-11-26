<button>Create ZONE</button>
<br />
<br />

<table class="table10 table-bordered  table-hover">
	<tbody><tr class="headings">
		<td width="5%">Sequence ID</td>
		<td width="5%">ZONE ID</td>
		<td width="50%">Name</td>
		<td width="10%">Status</td>
	</tr>
	<?php for($x=1;$x<=20;$x++): ?>
	<!------>
	<tr onclick="" data-index="0" data-box1="new_initiative" class="cursor1  trig1">
		<td><?php echo $x; ?></td>
		<td>01<?php echo $x; ?></td>
		<td style="padding:10px;">
			ZONE 0<?php echo $x; ?>
		</td>
		<td>Active / <span class="rd"> Inactive</span></td>
	</tr>
	<!------>
	<?php endfor; ?>

	
	
						
</table>
