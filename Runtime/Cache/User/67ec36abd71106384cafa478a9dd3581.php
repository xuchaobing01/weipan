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
	
<link rel="stylesheet" href="<?php echo STATICS;?>/validation/css/validationEngine.jquery.css" />

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
<div class="page-topbar">
	<ol class="breadcrumb">
		<li><a href="/User/Index/home.html">控制台</a></li>
		<li class="active"><?php echo ($title); ?></li>
	</ol>
</div>
<div class="alert alert-info">
	<i class="fa fa-info-circle"></i>&nbsp;请按照微信公众平台的信息如实填写，否则会导致某些功能无法正常使用！
</div>
<form id="editForm" method="post" class="form-horizontal wx-form" role="form" action="">
	<input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" />
	<div class="form-group">
		<label for="accountId" class="col-sm-2 control-label">头像</label>
		<div class="col-sm-4" >
			<?php if(empty($info['headerpic'])) $info['headerpic'] = rtrim(C('site_url'),'/').'/Public/User/images/thumbnail.png';?>
			<input type="hidden" class="form-control" value="<?php echo ($info["headerpic"]); ?>" id="headerpic" name="headerpic" />
			<img id="headerpicHolder" style="width:80px;height:80px;" class="img-rounded" src="<?php echo ($info["headerpic"]); ?>"/>
			<div class="btn-group" style="vertical-align:bottom;">
				<span class="btn btn-xs btn-primary" onclick="selectAsset('headerpic',120,120)">
					<span class="glyphicon glyphicon-cloud-upload"></span>选择
				</span>
				<span class="btn btn-primary btn-xs" onclick="previewImg('headerpic')">
					<span class="glyphicon glyphicon-picture"></span>预览
				</span>
			</div>
		</div>
	</div>
	<div class="form-group">
	<label for="wxname" class="col-sm-2 control-label">公众号名称</label>
	<div class="col-sm-4">
		<input type="text" class="form-control validate[required,minSize[2]]" id="wxname" name="wxname" value="<?php echo ($info["wxname"]); ?>" />
	</div>
	</div>
	<div class="form-group">
		<label for="wxid" class="col-sm-2 control-label">公众号原始ID</label>
		<div class="col-sm-4">
		  <input id="wxid" type="text" class="form-control validate[required]" name="wxid" value="<?php echo ($info["wxid"]); ?>" />
		  <span class="help-block">请按照公众平台正确填写</span>
		</div>
	</div>
	<div class="form-group">
		<label for="weixin" class="col-sm-2 control-label">微信号</label>
		<div class="col-sm-4">
			<input class="form-control validate[required]" id="weixin" type="text" name="weixin" value="<?php echo ($info["weixin"]); ?>" />
		</div>
	</div>
	<div class="form-group">
		<label for="wxtype" class="col-sm-2 control-label">公众号类型</label>
		<div class="col-xs-2">
			<select name="wxtype" id="wxtype" class="form-control">
				<option value="订阅号" <?php if(($info["wxtype"]) == "订阅号"): ?>selected<?php endif; ?> >订阅号</option>
				<option value="服务号" <?php if(($info["wxtype"]) == "服务号"): ?>selected<?php endif; ?> >服务号</option>
			</select> 
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
				  <input type="checkbox" id="is_certified" <?php if(!empty($info["is_certified"])): ?>checked<?php endif; ?> name="is_certified" value="1" /> 已认证
				</label>
			</div>
		</div>
	</div>
	<div class="form-group" id="appId">
		<label for="weixin" class="col-sm-2 control-label">APPID</label>
		<div class="col-sm-4">
			<input class="form-control" type="text" name="appid" value="<?php echo ($info["appid"]); ?>" />
		</div>
	</div>
	<div class="form-group" id="appSecret">
		<label for="weixin" class="col-sm-2 control-label">APPSecret</label>
		<div class="col-sm-4">
			<input class="form-control" type="text" name="appsecret" value="<?php echo ($info["appsecret"]); ?>" />
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
				  <input type="checkbox" <?php if(!empty($info["encript_mode"])): ?>checked<?php endif; ?> name="encript_mode" value="1" /> 加密模式
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">EncodingAESKey</label>
		<div class="col-sm-4">
			<input class="form-control" type="text" name="encript_key" value="<?php echo ($info["encript_key"]); ?>" />
			<span class="help-block">加密模式时需要填写</span>
		</div>
	</div>
	<div class="form-group">
		<label for="province" class="col-sm-2 control-label">运营地区</label>
		<div id="cityWrapper" class="col-xs-4">
			<select name="province" class="form-control input-auto province validate[required]" data-val="<?php echo ($info["province"]); ?>"></select> 
			<select name="city" class="form-control input-auto city validate[required]" data-val="<?php echo ($info["city"]); ?>"></select>
		</div>
	</div>
	
	<div class="form-group">
	<label for="wxfans" class="col-sm-2 control-label">粉丝数</label>
	<div class="col-sm-2">
		<input name="wxfans" id="wxfans" value="<?php echo ((isset($info["wxfans"]) && ($info["wxfans"] !== ""))?($info["wxfans"]):0); ?>" type="text"  class="form-control validate[required,custom[integer]]" />
	</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-2">
			<button id="saveSetting" type="submit" class="btn btn-primary" >保存</button>
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
	
<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine-zh_CN.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/cxselect/js/jquery.cxselect.js"></script>
<script src="<?php echo STATICS;?>/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="<?php echo STATICS;?>/artDialog/plugins/iframeTools.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/js/upload.js?ver=<?php echo rand(0,9999);?>"></script>
<script type="text/javascript">
	$("#btnBack").click(function(e){
		location.href="<?php echo U('Index/home');?>";
	});
	$(function(){
		$("#editForm").validationEngine("attach",{ 
			promptPosition:"centerRight",
			scroll:true,
			showOneMessage:true
		});
		$('#wxtype').change(function(){
			toggle_app_el();
		});
		$('#is_certified').click(function(){
			toggle_app_el();
		});
		toggle_app_el();
	});
	
	// selectes 为数组形式，请注意顺序 
	$("#cityWrapper").cxSelect({ 
		selects:["province","city"], 
		nodata:"hidden",
		url:"<?php echo STATICS;?>/cxselect/js/city.min.js"
	});
	
	function toggle_app_el(){
		var type = $('#wxtype').val();
		if(type == '服务号'){
			show();
		}
		else if($('#is_certified').is(':checked')){
			show();
		}
		else hide();
	}
	
	function show(){
		$('#appId').show();
		$('#appSecret').show();
	}
	
	function hide(){
		$('#appId').hide();
		$('#appSecret').hide();
	}
</script>

</body>
</html>