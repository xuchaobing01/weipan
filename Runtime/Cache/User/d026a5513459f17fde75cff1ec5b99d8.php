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
		<h4 class="left">会员编辑</h4>
		<a href="<?php echo U('member/index');?>" class="btn btn-sm btn-default">
			<span class="glyphicon glyphicon-chevron-left"></span>&nbsp;返回
		</a>
	</div>
	<ul class="nav nav-tabs wm-tabs">
		<li class="active"><a href="javascript:void(0)">会员信息</a></li>
		<li><a href="<?php echo U('Member/money',['id'=>$_GET['id']]);?>">充值记录</a></li>
		<!-- <li><a href="<?php echo U('Member/score',['id'=>$_GET['id']]);?>">积分记录</a></li>
		<li><a href="<?php echo U('Member/expense',['id'=>$_GET['id']]);?>">商品购买记录</a></li> -->
	</ul>
	<form id="form" class="form-horizontal" method="post" style="margin-top:20px;">
		<input type="hidden" name="id" value="<?php echo ($member["id"]); ?>" />
		<div class="form-group">
			<label class="control-label col-xs-2">昵称</label>
			<div class="col-md-4">
				<input type="text" class="form-control" disabled value="<?php echo ($member["wechat_name"]); ?>" />
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-xs-2">姓名</label>
			<div class="col-md-4">
				<input type="text" class="form-control" disabled value="<?php echo ($member["username"]); ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">电话</label>
			<div class="col-md-4">
				<input type="text" class="form-control" disabled value="<?php echo ($member["phone"]); ?>" />
	
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">余额</label>
			<div class="col-md-4">
				<div class="input-group">
					<input type="text" class="form-control" readonly value="<?php echo ($member["money"]); ?>">
					<span class="input-group-btn">
						<button data-toggle="modal" data-backdrop="static" data-target="#rechargeModal" class="btn btn-default" type="button">充值</button>
					</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-xs-2">积分</label>
			<div class="col-md-4">
				<input type="text" name="total_score" readonly class="form-control" value="<?php echo ($member["total_score"]); ?>" />
			</div>
		</div>
		
		
		<!-- <div class="form-group">
			<div class="col-md-4 col-xs-offset-2">
				<button class="btn btn-success">保存</button>
			</div>
		</div> -->
	</form>
</div>
<div class="modal fade" id="modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">会员充值</h4>
			</div>
			<div class="modal-body">
				<form id="form" class="form" method="post" action="<?php echo U('money',['id'=>$member['id']]);?>">
					<div class="form-group">
						<label for="money">充值金额</label>
						<input id="money" type="text" name="money" class="form-control" />
						<span class="help-block"></span>
					</div>
					<div class="form-group">
						<label for="remark">备注</label>
						<input id="remark" type="text" name="remark" class="form-control" />
						<span class="help-block"></span>
					</div>
					<button type="submit" class="btn btn-primary">确定</button>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="rechargeModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">会员充值</h4>
			</div>
			<div class="modal-body">
				<form id="form" class="form" method="post" action="<?php echo U('money',['id'=>$member['id']]);?>">
					<div class="form-group">
						<label for="money">充值金额</label>
						<input id="money" type="text" name="money" class="form-control" />
						<span class="help-block"></span>
					</div>
					<div class="form-group">
						<label for="remark">备注</label>
						<input id="remark" type="text" name="remark" class="form-control" />
						<span class="help-block"></span>
					</div>
					<button type="submit" class="btn btn-primary">确定</button>
				</form>
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
</script>

</body>
</html>