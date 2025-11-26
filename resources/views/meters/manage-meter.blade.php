<?php $meter_management = '  class="active" ';?>
<?php  $com_url = '../resources/views/billings/';?>
<?php // ?>

@extends('layouts.billings')

@section('content')

@if ($errors->any())
<div  style="padding:15px;">
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
</div>
@endif

@if(session()->has('success'))
<div  style="padding:15px;">
    <div class="alert alert-warning">
            {!! session('success') !!}
    </div>
</div>
@endif



<ul  class="tab1x tab1x_v2">
    <li class="tab01 active" data-tab="dashboard11"><a href="#dashboard11">Dashboard</a></li>
<!--
    <li class="tab01" data-tab="rm1"><a href="#rm1">Reports Management</a></li>
-->
<!--
    <li class="tab01" data-tab="contx1"><a href="#contx1">Aging of Account</a></li>
      <li class="tab01" data-tab="contx2"><a href="#contx2">Account Balances</a></li>
    <li class="tab01" data-tab="contx3"><a href="#contx3">Summary of Delinquents</a></li>
    <li class="tab01" data-tab="contx4"><a href="#contx4">Acknowledgement</a></li>
    <li class="tab01" data-tab="contx5"><a href="#contx5">Billing Summary</a></li>
    <li class="tab01" data-tab="contx6"><a href="#contx6">Accounts Reports</a></li>
-->
</ul>


<div class="box1_white  tab_cont_1"  data-default="dashboard11">


    <div class="tab_item dashboard11">
        <div style="padding:15px;">
            
            <div style="flex-direction:row; float:right; display:flex;" class="meter_cmd">
                <button class="form-control btn btn-success" style="min-width:100px" onclick="trig1_v2('add_meter_form');">Add Meter</button>
                <button class="form-control btn btn-success" style="min-width:100px" onclick="add_personel_show_form()">Add Personel</button>
            </div>
            <div style="display: flex;width:400px" class="meter_search">
                {{-- <small>Search Meter #</small><br /> --}}
                <input type="text" placeholder="Search Meter #" class="form-control search_meter_input" onchange="search_meter_number()" />
                <button class="form-control btn btn-primary" style="width:100px" onclick="search_meter_number()" >Search</button>
                {{-- <button class="form-control btn btn-primary" style="width:100px" >Clear</button> --}}
            </div>

            <div class="scroll1">
                Please wait...
            </div>



            
        </div>
    </div>    
</div>







<?php include('../resources/views/meters/popup/manage-meter-pop.php'); ?>


@endsection

@section('inv_include')

<?php include($com_url.'inc/php_mod/pop1.php'); ?>
<?php include('../resources/views/meters/js/manage-meter-js.php'); ?>
<?php include('../resources/views/meters/js/manage-meter-js-actions.php'); ?>

<style>
.meter_cmd button,    
.meter_search button{
    margin-right: 5px;
}    
</style>

<link rel="stylesheet" href="/hwd1/css/billing.common.css">

@endsection