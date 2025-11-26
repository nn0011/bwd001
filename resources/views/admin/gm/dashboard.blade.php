<?php $billing_dashboard = '  class="active" ';?>
<?php  $com_url = '../resources/views/billings/';?>

@extends('layouts.admin')

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
	</ul>

	<div class="box1_white  tab_cont_1"  data-default="dashboard11">
		
		<div class="tab_item dashboard11">

			<div style="padding:15px;">

				<h1>Welcome</h1>
				<hr />

				<h2>Monthly Account Activity Chart</h2>

				<div id="chart1"></div>

			</div>



		</div>

        <div class="employee_acct01" style="padding: 15px;margin-bottom:30px;">

            <div style="padding:15px;">
                <h2>Daily Collection Chart (last 30 Days)</h2>
                <div style="text-align: right;"><a onclick="refresh_daily_collection_data()"  target="blank"><small>Refresh Data</small></a> </div>
              </div>
  
              <div id="daily_coll_001"></div>
              <br />
              <br />
  

            <div style="padding:15px;">
              <h2>Monthly Collection Chart</h2>
              <div style="text-align: right;"><a onclick="refresh_collection_data()"  target="blank"><small>Refresh Data</small></a> </div>
            </div>

            <div id="chart2"></div>
            <br />
            <br />

            <h2>Adjustments Chart</h2>

            <div id="chart3"></div>
            <br />
            <br />



			<div style="padding:15px;">
				<h2>Employee Water Bill Balance</h2>
			</div>


            <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Account #</th>
                    <th>Full Name</th>
                    <th># of Months</th>
                    <th>Balance</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach($accounts as $acct): ?>
                  <tr>
                    <td><?php echo $acct->acct_no; ?></td>
                    <td><?php echo strtoupper($acct->lname.', '.$acct->fname.' '.$acct->mi); ?></td>
                    <td><?php echo @$acct->num_bill>0?@$acct->num_bill:''; ?></td>
                    <td style="text-align: right;"><?php echo number_format($acct->ledger_data7->ttl_bal,2); ?></td>
                  </tr>
                <?php endforeach; ?>

                </tbody>
              </table>



            
        </div>

		
	</div>


	
@endsection

@section('scripts')
<?php 
/*
	<?php include($com_url.'inc/php_mod/pop1.php'); ?>	 
	<?php include_once($com_url.'inc/billing_accounts/acct_popups.php'); ?>

*/ ?>	


<script>
jQuery(document).ready(function(){

    var dd_new = <?php echo json_encode($acct_data_info['new']); ?>;
    var dd_dis = <?php echo json_encode($acct_data_info['dis']); ?>;
    var dd_rec = <?php echo json_encode($acct_data_info['rec']); ?>;
    var dd_tic = <?php echo json_encode($acct_data_info['tic']); ?>;
    
    var plot1 = $.jqplot('chart1', [dd_new, dd_dis, dd_rec], {

        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true},
			pointLabels: { show: true }
        },

        series:[
            {label:'New Accounts'},
            {label:'Disconnected'},
            {label:'Reconnected'}
        ],
        legend: {
            show: true,
            placement: 'outsideGrid'
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: dd_tic
            },
            yaxis: {
                // pad: 1.05,
				padMax:1.3,				
                tickOptions: {formatString: '%d'}
            }
        }
    });


    var coll_tick = <?php echo json_encode($collection_data['coll_tick']) ?>;
    var coll_wb = <?php echo json_encode($collection_data['coll_wb']) ?>;
    var coll_nwb = <?php echo json_encode($collection_data['coll_nwb']) ?>;

    var plot2 = $.jqplot('chart2', [coll_wb, coll_nwb], {

        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true},
			pointLabels: { show: true }
        },

        series:[
            {label:'Water Bill'},
            {label:'NWB'},
        ],
        legend: {
            show: true,
            placement: 'outsideGrid'
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: coll_tick
            },
            yaxis: {
				padMax:1.3,				
                tickOptions: {
                    formatString:"%'.2f"
                }
                
            }
        }
    });


    var adj_tick = <?php echo json_encode($adjustment_data['tic']) ?>;
    var adj_pos = <?php echo json_encode($adjustment_data['pos']) ?>;
    var adj_neg = <?php echo json_encode($adjustment_data['neg']) ?>;

    var plot3 = $.jqplot('chart3', [adj_pos, adj_neg], {

        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true},
			pointLabels: { show: true }
        },

        series:[
            {label:'Positive Adjustments'},
            {label:'Negative Adjustments'},
        ],
        legend: {
            show: true,
            placement: 'outsideGrid'
        },
        axes: {
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: adj_tick
            },
            yaxis: {
				padMax:1.3,				
                tickOptions: {
                    formatString:"%'.2f"
                }
                
            }
        }
    });

    var daily_coll_tic = <?php echo json_encode($daily_collect['daily_coll_tic']) ?>;
    var daily_coll_val1 = <?php echo json_encode($daily_collect['daily_coll_val1']) ?>;

    var plot4 = $.jqplot('daily_coll_001', [daily_coll_val1], {

                        seriesDefaults:{
                            renderer:$.jqplot.BarRenderer,
                            rendererOptions: {fillToZero: true},
                            pointLabels: { show: true }
                        },

                        series:[
                            {label:'Daily Collection'},
                            // {label:'Negative Adjustments'},
                        ],
                        legend: {
                            show: true,
                            placement: 'outsideGrid'
                        },
                        axes: {
                            xaxis: {
                                renderer: $.jqplot.CategoryAxisRenderer,
                                ticks: daily_coll_tic
                            },
                            yaxis: {
                                padMax:1.3,				
                                tickOptions: {
                                    formatString:"%'.2f"
                                }
                                
                            }
                        }

                    });



});


function refresh_collection_data()
{
    var confir1 = confirm('Are you sure?');
    if( !confir1 ){return;}

    var confir2 = confirm('Are you sure?');
    if( !confir2 ){return;}

    window.open('/admin/gm/init_monthly_collection?reset=1','blank');
}//

function refresh_daily_collection_data()
{
    var confir1 = confirm('Are you sure?');
    if( !confir1 ){return;}

    var confir2 = confirm('Are you sure?');
    if( !confir2 ){return;}

    window.open('/admin/gm/init_daily_collection?reset=1','blank');
}//


</script>
@endsection


