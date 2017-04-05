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
		<li><a href="<?php echo U('Member/couponlist');?>">券管理</a></li>
		<li  class="active"><a href="<?php echo U('Member/addcoupon');?>">添加券</a></li>
	</ul>
	<form id="form" class="form-horizontal" method="post" style="margin-top:20px;">
		<input type="hidden" name="id" value="<?php echo ($data['id']); ?>">
		<div class="form-group">
			<label class="control-label col-xs-2">名称</label>
			<div class="col-md-4">
				<input type="text" name="name" class="validate[required] form-control" value="<?php echo ($data['name']); ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">类型</label>
			<div class="col-md-4">
				<select class="form-control" name="type" value="<?php echo ($data['type']); ?>">
					<option value="Win">必盈</option>
					<option value="Incr">增益</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">有效期天数</label>
			<div class="col-md-4">
				<input type="text" name="overdue_time" value="<?php echo ($data['overdue_time']); ?>" class="validate[required] form-control" value="0" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">价格</label>
			<div class="col-md-4">
				<input type="text" name="amount" value="<?php echo ($data['amount']); ?>" class="validate[required] form-control" value="0" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">适用范围</label>
			<div class="col-md-4">
				<select class="form-control" name="use_area" value="<?php echo ($data['use_area']); ?>">
					<option value="ALL">全场使用</option>
					<option value="BTCCNY">比特币</option>
				</select>

			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">要求消费(元)</label>
			<div class="col-md-4">
				<input type="text" name="satisfy_amount" value="<?php echo ($data['satisfy_amount']); ?>" class="validate[required] form-control" value="0" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">注册赠送张数</label>
			<div class="col-md-4">
				<input type="text" name="register_present" value="<?php echo ($data['register_present']); ?>" class="validate[required] form-control" value="0" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">首次充值赠送比例</label>
			<div class="col-md-4">
				<input type="text" name="recharge_present" value="<?php echo ($data['recharge_present']); ?>" class="validate[required] form-control" value="0" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">使用说明</label>
			<div class="col-md-4">
				<textarea class="form-control validate[required]" name="remark" id="remark" rows="10"><?php echo ($data["remark"]); ?></textarea>
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
<script src="<?php echo STATICS;?>/kindeditor/kindeditor.js" type="text/javascript"></script>
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

    var editor;
    KindEditor.ready(function(K) {
        editor = K.create('#remark', {
            resizeType : 1,
            allowPreviewEmoticons : false,
            allowImageUpload : true,
            uploadJson : '/index.php?m=User&c=Qiniu&a=kindEditorUpload',
            items : [
                'source','undo','plainpaste','wordpaste','clearhtml','quickformat','selectall','fullscreen','fontname', 'fontsize','subscript','superscript','indent','outdent','|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','hr']
        });
    });
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