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
	<div class="page-header wm-page-header">
		<h4 class="left">代理编辑</h4>
		<a href="<?php echo U('member/channel');?>" class="btn btn-sm btn-default">
			<span class="glyphicon glyphicon-chevron-left"></span>&nbsp;返回
		</a>
	</div>
	
	<form id="form" class="form-horizontal" method="post" style="margin-top:20px;">
		<input type="hidden" name="id" value="<?php echo ($member["id"]); ?>" />
		<div class="form-group">
			<label class="control-label col-xs-2">用户名</label>
			<div class="col-md-4">
				<input type="text" name="username" class="validate[required] form-control" value="<?php echo ($member["username"]); ?>" />
			</div>
		</div>
		
		
		<div class="form-group">
			<label class="control-label col-xs-2">电话</label>
			<div class="col-md-4">
				<input type="text" name="phone" disabled class="validate[required] form-control"  value="<?php echo ($member["phone"]); ?>" />
				<span class="help-block">渠道关联的手机号，该手机号发展的会员都能拿盈亏返点（*） </span>
			</div>	
		</div>
		
		<div class="form-group">
			<label class="control-label col-xs-2">办公室地址</label>
			<div class="col-md-4">
				<input type="text" name="address" class="form-control"  value="<?php echo ($member["address"]); ?>" />
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-xs-2">身份证信息</label>
			<div class="col-md-4">
				<input type="text" name="idcard" class="form-control" value="<?php echo ($member["idcard"]); ?>" />
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-xs-2">公司名称</label>
			<div class="col-md-4">
				<input type="text" name="company" class=" form-control" value="<?php echo ($member["company"]); ?>" />
			</div>
		</div>
		
		<div class="form-group">
		<label class="control-label col-xs-2">盈亏返点(百分比)</label>
			<div class="col-md-6">
				<div class="input-group">
					<input type="text" class="form-control" name="fandian" value="<?php echo ((isset($member["fandian"]) && ($member["fandian"] !== ""))?($member["fandian"]):0); ?>" style="width:50px;"/>
					<span class="input-group-btn" >
						<button class="btn btn-default" type="button" style="margin-right: 676px;">%</button>
					</span>
				</div>
			</div>
		</div>	
		<div class="form-group">
			<label class="control-label col-xs-2">密码</label>
			<div class="col-md-4">
				<input type="password" name="password" class="form-control" />
					   
					   <span class="help-block">为空则不修改密码 </span>
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
	}
</script>

</body>
</html>