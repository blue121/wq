<?php defined('IN_IA') or exit('Access Denied');?><style>
	@import url("http://fonts.useso.com/css?family=Open+Sans:300,400,600,700&amp;lang=en");
	body {
		font-size: 13px;
		color: #676a6c;
		overflow-x: hidden;
	}
	.overview_list_wrap {
		clear: both;
		margin-bottom: 25px;
		margin-top: 0;
		padding: 0;
	}
	.overview_list_title {
		-moz-border-bottom-colors: none;
		-moz-border-left-colors: none;
		-moz-border-right-colors: none;
		-moz-border-top-colors: none;
		background-color: #fff;
		border-color: #e7eaec;
		border-image: none;
		border-style: solid solid none;
		border-width: 4px 0 0;
		color: inherit;
		margin-bottom: 0;
		padding: 14px 15px 7px;
		min-height: 48px;
	}
	.label {
		font-family: 'Open Sans';
		font-size: 10px;
		font-weight: 600;
		padding: 3px 8px;
		text-shadow: none;
	}
	.overview_list_title h5 {
		display: inline-block;
		font-size: 14px;
		margin: 0 0 7px;
		padding: 0;
		text-overflow: ellipsis;
		float: left;
	}
	.overview_list_content {
		background-color: #fff;
		clear: both;
		color: inherit;
		padding: 15px 20px 20px 20px;
		border-color: #e7eaec;
		border-image: none;
		border-style: solid solid none;
		border-width: 1px 0;
	}
	.overview_list_content h1 {
		margin-top: 5px;
		font-size: 30px;
		font-family: "open sans";
		font-weight: 100 !important;
	}
/*自定义一行五列*/
	.col-sm-2dot4 {
		position: relative;
		min-height: 1px;
		padding-right: 15px;
		padding-left: 15px;
	}
	@media (min-width: 1200px) {
		.col-sm-2dot4 {
			float: left;
		}
		.col-sm-2dot4 {
			width: 20%;
		}
		.col-sm-pull-2dot4 {
			right: 20%;
		}
		.col-sm-push-2dot4 {
			left: 20%;
		}
		.col-sm-offset-2dot4 {
			margin-left: 20%;
		}
	}
</style>

<div class="row">
	<div class="col-sm-2dot4">
		<div class="overview_list_wrap">
			<div class="overview_list_title">
				<h5>总人数</h5>
			</div>
			<div class="overview_list_content">
				<h1><?php  echo $all_count;?></h1>
			</div>
		</div>
	</div>
	<div class="col-sm-2dot4">
		<div class="overview_list_wrap">
			<div class="overview_list_title">
				<h5>一级</h5>
			</div>
			<div class="overview_list_content">
				<h1><?php  echo $downline1_count;?></h1>
			</div>
		</div>
	</div>
	<div class="col-sm-2dot4">
		<div class="overview_list_wrap">
			<div class="overview_list_title">
				<h5>二级</h5>
			</div>
			<div class="overview_list_content">
				<h1><?php  echo $downline2_count;?></h1>
			</div>
		</div>
	</div>
	<div class="col-sm-2dot4">
		<div class="overview_list_wrap">
			<div class="overview_list_title">
				<h5>三级</h5>
			</div>
			<div class="overview_list_content">
				<h1><?php  echo $downline3_count;?></h1>
			</div>
		</div>
	</div>
	<div class="col-sm-2dot4">
		<div class="overview_list_wrap">
			<div class="overview_list_title">
				<h5>其他</h5>
			</div>
			<div class="overview_list_content">
				<h1><?php  echo $other_count;?></h1>
			</div>
		</div>
	</div>
</div>


<div class="panel panel-default">
	<div class="panel-body" id="scroll">
		<div class="pull-left">
			<form method="get" id="form1">
				<input name="c" value="site" type="hidden" />
				<input name="a" value="entry" type="hidden" />
				<input name="eid" value="<?php  echo $_GPC['eid'];?>" type="hidden" />
				<input name="do" value="partner" type="hidden" />
				<input name="act" value="overview" type="hidden" />
				<input name="m" value="superman_mall" type="hidden" />
				<?php  echo tpl_form_field_daterange('datelimit', array('start' => date('Y-m-d', $starttime),'end' => date('Y-m-d', $endtime)), '')?>
				<input type="hidden" value="" name="scroll">
			</form>
		</div>
		<div class="pull-right">
			<div class="checkbox">
				<label style="color:#57B9E6;"><input checked type="checkbox">分销商</label>&nbsp;
			</div>
		</div>
		<div style="margin-top:20px">
			<canvas id="myChart" width="1200" height="300"></canvas>
		</div>
	</div>
</div>
<script>
	require(['chart', 'daterangepicker'], function(c) {
		$('.daterange').on('apply.daterangepicker', function(ev, picker) {
			$('input[name="scroll"]').val($(document).scrollTop());
			$('#form1')[0].submit();
		});
		<?php  if($scroll) { ?>
		var scroll = "<?php  echo $scroll;?>";
		$("html,body").animate({scrollTop: scroll}, 300);
		<?php  } ?>
		var chart = null;
		var chartDatasets = null;
		var templates = {
			flow1: {
				label: '分销商',
				fillColor : "rgba(36,165,222,0.1)",
				strokeColor : "rgba(36,165,222,1)",
				pointColor : "rgba(36,165,222,1)",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "rgba(36,165,222,1)"
			}
		};

		function refreshData() {
			if(!chart || !chartDatasets) {
				return;
			}
			var visables = [];
			var i = 0;
			$('.checkbox input[type="checkbox"]').each(function(){
				if($(this).attr('checked')) {
					visables.push(i);
				}
				i++;
			});
			var ds = [];
			$.each(visables, function(){
				var o = chartDatasets[this];
				ds.push(o);
			});
			chart.datasets = ds;
			chart.update();
		}

		var url = location.href + '&#aaaa';
		$.post(url, function(data){
			var data = $.parseJSON(data);
			var datasets = data.datasets;
			if(!chart) {
				var label = data.label;
				var ds = $.extend(true, {}, templates);
				ds.flow1.data = datasets.flow1;
				var lineChartData = {
					labels : label,
					datasets : [ds.flow1]
				};
				var ctx = document.getElementById("myChart").getContext("2d");
				chart = new Chart(ctx).Line(lineChartData, {
					responsive: true
				});
				chartDatasets = $.extend(true, {}, chart.datasets);
			}
			refreshData();
		});

		$('.checkbox input[type="checkbox"]').on('click', function(){
			$(this).attr('checked', !$(this).attr('checked'));
			refreshData();
		});
	});
</script>