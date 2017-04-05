<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<meta charset="utf-8" />
	<meta name="keywords" content="微市场 微信公众号服务平台"/>
	<meta name="description" content="微信公众号服务平台  微官网 微相册 微客服  微订单"/>
	<meta name="baidu-site-verification" content="pKfXuVuMa3" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<title><?php echo C('PLATFORM_NAME');?></title>
	
	
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/bootstrap/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="/Public/Admin/me/css/bootstrap-me.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/font-awesome/css/font-awesome.min.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo STATICS;?>/pnotify/pnotify.custom.min.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo STATICS;?>/datetimepicker/css/bootstrap-datetimepicker.min.css" />
	<link rel="stylesheet" href="<?php echo STATICS;?>/validation/css/validationEngine.jquery.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/bs-context.css"/>
	
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	
<link type="text/css" rel="stylesheet" href="<?php echo STATICS;?>/daterangepicker/daterangepicker-bs3.css" />
<style>
.bs-callout {
	padding: 20px;
	margin: 20px 0;
	border: 1px solid rgb(238, 238, 238);
	border-left-width: 5px;
	border-radius: 3px;
}
.bs-callout-info {
	border-left-color: rgb(91, 192, 222);
}
</style>

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container">
	<div>
		<div class="btn-toolbar" role="toolbar">
			<div class="btn-group btn-group-sm">
				<button id="7days" name="7" type="button" class="btn <?php echo $range==7?'btn-success':'btn-default';?>">最近7天</button>
				<button id="15days" name="15" type="button" class="btn <?php echo $range==15?'btn-success':'btn-default';?>">最近15天</button>
				<button id="30days" name="30" type="button" class="btn <?php echo $range==30?'btn-success':'btn-default';?>">最近30天</button>
			</div>
			<div class="btn-group btn-group-sm" style="width: 180px;">
				<input type="text" id="daterange" style="text-align:center;" name="daterange" class="form-control input-sm" value="<?php echo strpos($range,'~')?$range:'';?>" placeholder="自定义日期范围" />
			</div>
		</div>
	</div>
	<hr style="margin:10px 0;"/>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">用户关注趋势</h3>
		</div>
		<div class="panel-body">
			<div id="main" style="height:400px"></div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">消息请求数</h3>
		</div>
		<div class="panel-body">
			<div id="canvas2" style="height:400px"></div>
		</div>
	</div>
	<div class="panel panel-info hidden">
		<div class="panel-heading">
			<h3 class="panel-title">用户增长趋势</h3>
		</div>
		<div class="panel-body">
			<div id="canvas3" style="height:400px"></div>
		</div>
	</div>
	<div class="page-header">
		<h4>详细数据
		<a href="<?php echo U('export').'?daterange='.$_GET['daterange'];?>" target="_blank" class="btn btn-link pull-right">导出Excel</a>
		</h4>
	</div>
	<table class="table table-striped table-hover">
 		<thead>
 			<tr>
				<th>日期</th>
                <th>文本消息数</th>
 				<th>图片消息数</th>
				<th>语音消息数</th>
				<th>菜单点击数</th>
				<th>关注人数</th>				
				<th>取消关注人数</th> 
				<th>总消息数/日</th>
 				</tr>
 			</thead>
 		<tbody>
           </tbody>
		   <tfoot>
             <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo (date('Y-m-d',$list["time"])); ?></td>
					<td><?php echo ($list["textnum"]); ?></td>
					<td><?php echo ($list["imgnum"]); ?></td>
					<td><?php echo ($list["videonum"]); ?></td>
					<td><?php echo ($list["menunum"]); ?></td>
					<td><?php echo ($list["follownum"]); ?></td>
					<td><?php echo ($list["unfollownum"]); ?></td>											 
					<td><?php echo $list['textnum']+$list['imgnum']+$list['videonum']+$list['voicenum']?></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tfoot> 			
 	</table>
</div>

	<script type="text/javascript" src="<?php echo STATICS;?>/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php echo STATICS;?>/pnotify/pnotify.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/bootstrap-dialog/js/bootstrap-dialog.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery.ajaxfileupload.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery.form.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/js/spin.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine-zh_CN.js"></script>
	<script src="<?php echo STATICS;?>/spark/weimarket.admin.js"></script>
	
<script src="<?php echo STATICS;?>/js/moment.min.js"></script>
<script src="<?php echo STATICS;?>/daterangepicker/daterangepicker.js"></script>
 <!-- ECharts单文件引入 -->
<script src="http://echarts.baidu.com/build/echarts-plain.js"></script>
<script type="text/javascript">
	// 基于准备好的dom，初始化echarts图表
	var chart1 = echarts.init(document.getElementById('main'));
	var chart2 = echarts.init(document.getElementById('canvas2'));
	//var chart3 = echarts.init(document.getElementById('canvas3'));
	var option = {
		tooltip: {
			show: true
		},
		toolbox: {
			show : true,
			feature : {
				mark : {show: false},
				dataView : {show: false, readOnly: false},
				magicType : {show: true, type: ['line', 'bar']},
				restore : {show: true},
				saveAsImage : {show: true}
			}
		},
		legend: {
			data:['新关注人数','取消关注人数','净增加人数']
		},
		xAxis : [
			{
				type : 'category',
				data : <?php echo ($name); ?>
			}
		],
		yAxis : [
			{
				type : 'value'
			}
		],
		series : [
			{
				"name":"新关注人数",
				"type":"line",
				"data":<?php echo ($followNum); ?>
			},
			{
				"name":"取消关注人数",
				"type":"line",
				"data":<?php echo ($unfollowNum); ?>
			},
			{
				"name":"净增加人数",
				"type":"line",
				"data":<?php echo ($truefollowNum); ?>
			}
		]
	};
	var canvas2_option = {
		tooltip: {
			show: true
		},
		toolbox: {
			show : true,
			feature : {
				mark : {show: false},
				dataView : {show: false, readOnly: false},
				magicType : {show: true, type: ['line', 'bar']},
				restore : {show: true},
				saveAsImage : {show: true}
			}
		},
		legend: {
			data:['文本消息数','菜单点击数','图片消息数']
		},
		xAxis : [
			{
				type : 'category',
				data : <?php echo ($name); ?>
			}
		],
		yAxis : [
			{
				type : 'value'
			}
		],
		series : [
			{
				"name":"文本消息数",
				"type":"line",
				"data":<?php echo ($textNum); ?>
			},
			{
				"name":"菜单点击数",
				"type":"line",
				"data":<?php echo ($menuNum); ?>
			},
			{
				"name":"图片消息数",
				"type":"line",
				"data":<?php echo ($imgNum); ?>
			}
		]
	};
	/*var canvas3_option = {
		tooltip: {
			show: true
		},
		toolbox: {
			show : true,
			feature : {
				mark : {show: false},
				dataView : {show: false, readOnly: false},
				magicType : {show: true, type: ['line', 'bar']},
				restore : {show: true},
				saveAsImage : {show: true}
			}
		},
		legend: {
			data:['用户增长趋势']
		},
		xAxis : [
			{
				type : 'category',
				data : <?php echo ($name); ?>
			}
		],
		yAxis : [
			{
				type : 'value'
			}
		],
		series : [
			{
				"name":"用户增长趋势",
				"type":"line",
				"data":<?php echo ($totalNum); ?>
			}
		]
	};*/
	// 为echarts对象加载数据 
	chart1.setOption(option);
	chart2.setOption(canvas2_option);
	//chart3.setOption(canvas3_option);
	var url = "<?php echo U('index');?>";
	$(function(){
		$('#7days,#15days,#30days').click(function(){
			if(!$(this).hasClass('btn-success')){
				location.href = url+'?daterange='+$(this).attr('name');
			}
		});
	})
</script>
<script>
	var zh_CN = {
		applyLabel: '确定',
		cancelLabel: '取消',
		fromLabel: '从',
		toLabel: '至',
		weekLabel: 'W',
		customRangeLabel: '自定义',
		daysOfWeek: ['日','一','二','三','四','五','六'],
		monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
		firstDay: moment.localeData()._week.dow
	};
	$('#daterange').daterangepicker(
		{ 
			format: 'YYYY-MM-DD',
			startDate: moment().subtract(6, 'days'),
			endDate: moment(),
			separator: '~',
			locale: zh_CN,
			/*ranges: {
			   '今天': [moment(), moment()],
			   '昨天': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			   '最近7天': [moment().subtract(6, 'days'), moment()],
			   '最近30天': [moment().subtract(29, 'days'), moment()],
			   '本月': [moment().startOf('month'), moment().endOf('month')],
			   '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			},*/
		},
		function(start, end, label) {
			var range = start.format('YYYY-MM-DD') + ' ~ ' + end.format('YYYY-MM-DD')
			$('#dateRange').val(range);
			location.href = url+'?daterange='+range;
			
		}
	);
</script>

</body>
</html>