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
	
<style type="text/css">
	.tab-pane{
		padding-top: 10px;
	}
	
	.tab-pane button{
		margin:8px 10px;
	}
</style>

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
	<div class="">
		<h3>链接选择</h3>
		<p class="text-success">选择相应模块，并点击复制链接！</p>
	</div>
	<div class="row">
		<div class="col-md-12">
		<div class="page-header" style="margin-top:20px;">
			<button type="button" id="copy-button" data-clipboard-text="" class="btn btn-success">复制链接</button>&nbsp;
		</div>
		</div>
	</div>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#system" data-toggle="tab">功能模块</a></li>
		<li><a href="#category" data-toggle="tab">分类</a></li>
		<li><a href="#article" data-toggle="tab">文章</a></li>
		<li><a href="#game" data-toggle="tab">游戏</a></li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<div class="tab-pane active" id="system">
			<div class="row">
				<div class="col-md-12">
					<?php if(is_array($sysModules)): $i = 0; $__LIST__ = $sysModules;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><button url="<?php echo ($vo1["url"]); ?>" class="btn btn-default"><?php echo ($vo1["name"]); ?></button><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="category">
			<div class="row">
				<div class="col-md-12">
					<?php if(is_array($catModules)): $i = 0; $__LIST__ = $catModules;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?><button url="<?php echo ($vo2["url"]); ?>" class="btn btn-default"><?php echo ($vo2["name"]); ?></button><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="article">
			<div class="row">
				<div class="col-md-12">
					<?php if(is_array($artModules)): $i = 0; $__LIST__ = $artModules;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo3): $mod = ($i % 2 );++$i;?><button url="<?php echo ($vo3["url"]); ?>" class="btn btn-default"><?php echo ($vo3["name"]); ?></button><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="game">
			<div class="row">
				<div class="col-md-12">
					<?php if(is_array($gameModules)): $i = 0; $__LIST__ = $gameModules;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo4): $mod = ($i % 2 );++$i;?><button url="<?php echo ($vo4["url"]); ?>" class="btn btn-default"><?php echo ($vo4["name"]); ?></button><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
		</div>
	</div>
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
	
<script src="/Public/Common/zero-clipboard/ZeroClipboard.js"></script>
<script type="text/javascript">
	var $modules=$(".tab-pane button");
	$modules.click(function(e){
		$(this).toggleClass('btn-success');
		$modules.filter('.btn-success').not(this).removeClass('btn-success');
		if($(this).hasClass('btn-success')){
			$('#copy-button').attr('data-clipboard-text',$(this).attr('url'));
		}
	});

	var client = new ZeroClipboard( document.getElementById("copy-button") );
	client.on( "ready", function( readyEvent ) {
		// alert( "ZeroClipboard SWF is ready!" );
		client.on( "aftercopy", function( event ) {
			// `this` === `client`
			// `event.target` === the element that was clicked
			//event.target.style.display = "none";
			console.log(event.data["text/plain"]);
			notify('复制成功！');
		} );
	} );
</script>

</body>
</html>