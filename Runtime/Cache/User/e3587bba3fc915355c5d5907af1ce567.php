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
		<li><a href="<?php echo U('Member/detail',['id'=>$_GET['id']]);?>">会员信息</a></li>
		<li class="active"><a href="javascript:void(0)">充值记录</a></li>
		<!-- <li ><a href="<?php echo U('Member/score',['id'=>$_GET['id']]);?>">积分记录</a></li>
		<li><a href="<?php echo U('Member/expense',['id'=>$_GET['id']]);?>">商品购买记录</a></li> -->
	</ul>
	<button style="margin:20px 0;" class="btn btn-success" type="button"  data-toggle="modal" data-target="#dialog"><i class="fa fa-rmb fa-lg"></i>&nbsp;充值</button>
	<TABLE class="table wm-table-bordered table-hover table-striped">
		<THEAD>
		<TR>
			<TH>金额</TH>
			<TH>状态</TH>
			<TH>时间</TH>
		</TR>
		</THEAD>
		<TBODY>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><TR>
			<TD>
			<?php if($list['price'] >= 0): ?><span class="text-success">+<?php echo ($list["price"]); ?></span>
			<?php else: ?>
			<span class="text-danger"><?php echo ($list["price"]); ?></span><?php endif; ?>
			</TD>
			<TD><?php if($list['status'] == 2): ?><span class="text-success">已完成</span>
			<?php else: ?>
			<span class="text-danger">未支付</span><?php endif; ?></TD>
			<TD><?php echo (date('Y-m-d H:i',$list["createtime"])); ?></TD>
		</TR><?php endforeach; endif; else: echo "" ;endif; ?>
	  </TBODY>
	</TABLE>
	<div class="modal fade" id="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">会员充值</h4>
		  </div>
		  <div class="modal-body">
			<form id="form" class="form" method="post">
				<div class="form-group">
					<label for="money">金额</label>
					<input type="text" value="" name="money" class="validate[required,custom[number]] form-control" id="money" />
					<span class="help-block">充值金额必须大于0</span>
				</div>
				<div class="form-group">
					<label for="remark">说明</label>
					<input type="text" value="" name="remark" class="form-control" id="remark" />
					<span class="help-block">充值说明</span>
				</div>
				<button type="submit" class="btn btn-primary">保存</button>
			</form>
		  </div>
		</div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
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
	
<script src="<?php echo RES;?>/js/date/WdatePicker.js"></script>
<script src="<?php echo STATICS;?>/spark/spark.util.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine-zh_CN.js"></script>
<script>
$(function(){
	$("#form").validationEngine("attach",{ 
		promptPosition:"bottomRight",
		scroll:true,
		showOneMessage:true
	});
});
</script>

</body>
</html>