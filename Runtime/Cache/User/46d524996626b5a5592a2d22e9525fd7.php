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
	

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
	<?php if(empty($wxaccount["wxname"])): ?><div class="alert alert-warning">
		<h4>您还没有绑定公众号！<a href="<?php echo U('edit?id='.$wxaccount['id']);?>">立即绑定</a></h4>
	</div>
	<?php else: ?>
	<div class="page-header">
		<h3 style="color:rgb(77, 112, 148)"><?php echo ($wxaccount['wxname']); ?></h3>
		<span class="label label-primary" style="background-color:rgb(209, 202, 20);"><?php echo ($wxaccount['wxtype']); ?></span>
	</div>
	<table class="table table-bordered">
		<tbody>
		
		 <tr style="color:green">

			<td>会员总量: <?php echo ($tongji['user']); ?></td>

			<td>总充值: <?php echo ($tongji['paycount']); ?></td>

			<td>总交易次数: <?php echo ($tongji['ordercount']); ?></td>

			<td>总流水: <?php echo ($tongji['order']); ?></td>

		  </tr>

		   <tr style="color:red">

			<td>今日注册: <?php echo ($tongji['tuser']); ?></td>

			<td>今日充值: <?php echo ($tongji['tpaycount']); ?></td>

			<td>今日交易次数: <?php echo ($tongji['tordercount']); ?></td>

			<td>今日流水: <?php echo ($tongji['torder']); ?></td>

		  </tr>
		  
		
		  
		  
		  <tr>
			
			<td>文本回复: <?php echo ($chart['text_num']); ?></td>
			<td>图文回复: <?php echo ($chart['img_num']); ?></td>
			<td>语音回复: <?php echo ($chart['voice_num']); ?></td>
			<td>请求总数: <?php echo ($chart['total_request_num']); ?></td>
		  </tr>



		
		</tbody>
	</table>
	<div class="row">
		<div class="col-md-6">
			<span class="label label-default">API接口</span>
			<strong><?php echo C('api_domain');?>/api/<?php echo (session('token')); ?></strong>
		</div>
		<div class="col-md-4">
			<span class="label label-default">Token</span>
			<strong><?php echo ($wxaccount["token_sign"]); ?></strong>
		</div>
	</div><?php endif; ?>
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
	

</body>
</html>