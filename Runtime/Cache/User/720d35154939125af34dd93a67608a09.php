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
	
<link type="text/css" rel="stylesheet" href="/Public/Admin/me/css/bootstrap-me.css" />
<style>
	.table-toolbar{
		margin:10px 0;
	}
</style>

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
	<div class="page-header">
		<h4>支付接口配置</h4>
	</div>
	<div role="tabpanel" class="tabbable tabbable-custom">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#" aria-controls="home">微信支付</a></li>
			<!-- <li role="presentation"><a href="<?php echo U('alipay_cfg');?>" aria-controls="profile">支付宝支付</a></li> -->
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<form class="form form-horizontal" id="editForm" method="post" action="/User/paycfg/index.html">
				<input type="hidden" name="id" value="<?php echo ($set["id"]); ?>" />
				<div class="form-group">
					<label class="control-label col-xs-2">MCHID</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="mchid" value="<?php echo ($set["config"]["mchid"]); ?>" />
						<span class="help-block">请填写微信支付商户号</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">AppId</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="appid" value="<?php echo ($set["config"]["appid"]); ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">KEY</label>
					<div class="col-md-6">
						<div class="input-group">
							<input type="text" class="form-control" name="key" value="<?php echo ($set["config"]["key"]); ?>" />
							<span class="input-group-btn">
								<button class="btn btn-default" id="genKey" type="button">随机生成</button>
							</span>
						</div>
						
						<span class="help-block">微信支付安全密钥，32位字符串</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">AppSecret</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="appsecret" value="<?php echo ($set["config"]["appsecret"]); ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-xs-2">&nbsp;</label>
					<div class="col-md-6">
						<label class="checkbox-inline">
							<input type="checkbox" name="status" value="1" <?php if($set['config']['status'] == 1): ?>checked<?php endif; ?>>启用
						</label>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4 col-xs-offset-2">
						<button class="btn btn-success" type="submit">保存</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="editModal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">支付配置</h4>
			</div>
			<div class="modal-body">
				
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
	
<script>
$('#editForm').ajaxForm({
	success:function(data){
		if(data.status==1){
			new PNotify({
				text: data.info,
				type: 'success'
			});
		}
		else{
			new PNotify({
				text: data.info,
				type: 'error'
			});
		}
	},
	dataType:'json'
});
$(function(){
	$('#genKey').click(function(){
		$.getJSON('/User/paycfg/genkey.html',function(resp){
			$('input[name="key"]').val(resp.key);
		})
	});
})
</script>

</body>
</html>