<?php

if(empty($pop101_width)){
	$pop101_width=500;
}

if(empty($pop101_height)){
	$pop101_height=500;
}

?>

<div class="back_1 pop101">
	<div class="box1">
		<?php /*<button class="close_me">Close</button>*/ ?>
		<a  class="close_me">X</a>
		<div class="cont"></div>
	</div>
</div>

<style>
.back_1{
	position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: rgba(18, 71, 79, 0.7);
    display:none;
    overflow:scroll;
}
.back_1 .box1{
	background:white;
	min-width:<?php echo $pop101_width; ?>px;
	min-height:<?php echo $pop101_height; ?>px;
	display:block;
	width:300px;
	margin:0 auto;
	margin-top:10%;
	position:relative;
	border-radius: 15px;
	padding-bottom:30px;
}
.back_1 .close_me{
    text-align: center;
    width: auto;
    height: auto;
    border-radius: 50%;
    font-size: 14px;
    position: absolute;
    top: 10px;
    right: 10px;
    font-weight: bold;
    border: 0px solid;
    padding: 5px;
    cursor: pointer;
    color: #108479;
}
.back_1 .cont{
	box-sizing:border-box;
}
.float1_back{
	overflow:scroll;
}
</style>
<script>
jQuery(document).ready(function(){
	jQuery('.back_1 .close_me').click(function(){
			jQuery('.back_1').hide();
			jQuery('.back_1').attr('class', 'back_1 pop101');
	});

	jQuery('.trig1').click(function(){
			$b1 = jQuery(this).data('box1');
			jQuery('.back_1 .cont').html(jQuery('.'+$b1).html());
			jQuery('.back_1').show();
	});
});
function trig1_v2($b1){
	jQuery('.back_1 .cont').html(jQuery('.'+$b1).html());
	jQuery('.back_1').show();
}
function pop_close(){
	jQuery('.back_1').hide();
	jQuery('.back_1').attr('class', 'back_1 pop101');
}
</script>
