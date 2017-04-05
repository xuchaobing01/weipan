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
	
<link rel="stylesheet" href="<?php echo STATICS;?>/validation/css/validationEngine.jquery.css">
<link type="text/css" rel="stylesheet" href="<?php echo STATICS;?>/datetimepicker/css/bootstrap-datetimepicker.min.css" />

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
	<ul class="nav nav-tabs wm-tabs">
		<li><a href="<?php echo U('Member/adlist');?>">幻灯片管理</a></li>
		<li  class="active"><a href="<?php echo U('Member/addad');?>">添加幻灯片</a></li>
	</ul>
	<form id="form" class="form-horizontal" method="post" style="margin-top:20px;">
		<div class="form-group">
			<label class="col-sm-2 control-label">图像</label>
			<div class="col-sm-4" >
				<input type="hidden" class="form-control" value="" id="imgUrl" name="imgUrl" />
				<img id="imgUrlHolder" style="width:375px;height:328px;" class="img-rounded" src="/Public/User/images/thumbnail.png"/>
				<div class="btn-group" style="vertical-align:bottom;">
				<span class="btn btn-xs btn-primary" onclick="selectAsset('imgUrl',120,120)">
					<span class="glyphicon glyphicon-cloud-upload"></span>选择
				</span>
					<span class="btn btn-primary btn-xs" onclick="previewImg('imgUrl')">
					<span class="glyphicon glyphicon-picture"></span>预览
				</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">排序</label>
			<div class="col-md-4">
				<input type="text" name="sort" class="validate[required] form-control" value="0" />
			</div>
		</div>
		
		<div class="form-group">
			<div class="col-md-4 col-xs-offset-2">
				<button type="submit" onclick="return check()" class="btn btn-success">保存</button>
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
	
<script type="text/javascript" src="<?php echo STATICS;?>/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="<?php echo STATICS;?>/spark/spark.util.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine-zh_CN.js"></script>
<script src="<?php echo STATICS;?>/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="<?php echo STATICS;?>/artDialog/plugins/iframeTools.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/js/upload.js?ver=<?php echo rand(0,9999);?>"></script>
<script>
$(function(){
	$("#form").validationEngine("attach",{ 
		promptPosition:"centerRight",
		scroll:true,
		showOneMessage:true
	});
	$(".datetime-picker").datetimepicker({format: 'yyyy-mm-dd',language:'zh-CN',minView:2,autoclose:true});
});
	function find(da) {
		$("input[name='type']").val(da);
	}
	
	function che() {
		var temp=/^(\+|-)?\d+($|\.\d+$)/
		var s = document.getElementById("money");
		if(temp.test(s.value)==false){
			alert("充值金额不正确！");
			return false;
		}
	}

	function check(){
		var pass1= $("input[name='password']").val();
		var repass1= $("input[name='repassword']").val();
		
		if(pass1 != repass1){
			alert("一级密码不一致！");
			return false;
		}
		
	}
</script>

</body>
</html>