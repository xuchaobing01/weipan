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
	
<link rel="stylesheet" href="<?php echo STATICS;?>/kindeditor/themes/default/default.css" />
<link rel="stylesheet" href="<?php echo STATICS;?>/kindeditor/plugins/code/prettify.css" />

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
	<div class="page-header wm-page-header">
		<h3><?php echo ($title); ?></h3>
		<a class="btn btn-sm btn-default" href="<?php echo U('Img/index');?>">
			<span class="glyphicon glyphicon-chevron-left"></span>&nbsp;返回
		</a>
	</div>
	<form class="form-horizontal" role="form" action="<?php if($info): echo U('Img/upsave'); else: echo U('Img/insert'); endif; ?>" method="post">
		<input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" />
		<div class="form-group">
			<label for="keyword" class="col-sm-2 control-label">关键词</label>
			<div class="col-sm-3">
				<input type="text" class="form-control" id="keyword" value="<?php echo ($info["keyword"]); ?>"  name="keyword" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" >匹配模式</label>
			<div class="col-md-4">
				<label style="cursor:pointer;">
					<input type="radio" name="type" id="type1" value="1" <?php if(($info == null) OR ($info['type'] == 1)): ?>checked<?php endif; ?>>精准匹配
				</label>
				&nbsp;&nbsp;
				<label style="cursor:pointer;">
					<input type="radio" name="type" id="type0" value="0" <?php if($info AND ($info['type'] == 0)): ?>checked<?php endif; ?>>模糊匹配
				</label>
			</div>
			<div class="col-md-4" style="padding-top:5px;">
				<span class="label label-info"></span>
			</div>
		</div>
		<div class="form-group">
			<label for="keyword" class="col-sm-2 control-label">标题</label>
			<div class="col-sm-6">
				<input type="text" class="form-control" id="title" value="<?php echo ($info["title"]); ?>" name="title" />
			</div>
		</div>
		<div class="form-group">
			<label for="info" class="col-sm-2 control-label">简介</label>
			<div class="col-sm-6">
				<textarea  class="form-control" id="text" name="text" style="height:100px"><?php echo ($info["text"]); ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label for="img" class="col-sm-2 control-label">封面图片</label>
			<div class="col-sm-6" >
				<input type="hidden" class="form-control" value="<?php echo ($info["pic"]); ?>" id="pic" name="pic" />
				<img id="picHolder" style="width:216px;height:120px;" class="img-rounded" <?php if($info['pic'] == ''): ?>data-src="$holder.js/216x120/gray/text:720*400"<?php endif; ?> src="<?php echo ($info["pic"]); ?>"/>
				<div class="btn-group" style="vertical-align:bottom;">
					<span class="btn btn-xs btn-primary" onclick="selectAsset('pic',720,400)">
						<span class="glyphicon glyphicon-cloud-upload"></span>选择
					</span>
					<span class="btn btn-primary btn-xs" onclick="previewImg('pic')">
						<span class="glyphicon glyphicon-picture"></span>预览
					</span>
				</div>
			</div>
		</div>
		
		<div id="classify-addr" class="form-group">
			<label for="url" class="col-sm-2 control-label">图文外链地址</label>
			<div class="col-sm-6">
				<div class="input-group">
				<input type="text" class="form-control" id="url" value="<?php echo ($info["url"]); ?>" name="url" />
				<span class="input-group-btn">
					<button class="btn btn-primary" onclick="selectLink('url')" type="button">选择</button>
				</span>
				</div><!-- /input-group -->
				<span class="help-block">如果填写了图文详细内容，请留空！</span>
			</div>
		</div>
		<div class="form-group">
			<label for="sorts" class="col-sm-2 control-label">图文详细页内容</label>
			<div class="col-sm-6">
				<textarea class="form-control" name="info" id="content" rows="18"><?php echo ($info["info"]); ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary">保存</button>
			</div>
		</div>
	</form>
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
	
<script src="<?php echo STATICS;?>/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="<?php echo STATICS;?>/artDialog/plugins/iframeTools.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/js/upload.js?ver=<?php echo rand(0,9999);?>"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/kindeditor/kindeditor.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/kindeditor/plugins/code/prettify.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/kindeditor/config.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/js/holder.min.js"></script>

</body>
</html>