<?php   include_once('../resources/views/layouts/hwd1_html/header.php');  ?>

	 <div class="tc1 main_right">
		<div class="cont_1">

        @yield('content')

		</div>
	 </div>

	@yield('inv_include')

</div>



<script src="/hwd1/js/js1.js" ></script>
<link rel="stylesheet" href="/hwd1/style.css"  />
<style>
.form-control{
	display:inline-block;
	width:100%;
	margin-bottom:10px;
}
.name_fileds input{
    display: inline-block;
    width: 32.7%;
}
</style>

<?php  include_once('../resources/views/layouts/hwd1_html/foot01.php');  ?>

</body>
</html>
