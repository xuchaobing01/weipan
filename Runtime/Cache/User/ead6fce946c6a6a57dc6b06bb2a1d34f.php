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
	<ul class="nav nav-tabs wm-tabs">
		<li class="active"><a href="javascript:void(0)">邀请码管理</a></li>

	</ul>
	<div class="page-header wm-page-header">

		<form method="get" action="/User/Member/invitelist.html" class="form-inline">
			<label>创建人手机号</label>
			<input name="phone" type="text" value="<?php echo ($_GET['phone']); ?>" class="form-control" />
			<button class="btn btn-primary" type="submit">
				<span class="glyphicon glyphicon-search"></span>
				查询
			</button>&nbsp
			<a class="btn btn-primary"  href="<?php echo U('Member/addinvite');?>">添加邀请码</a>
		</form>

	</div>

	<TABLE class="table wm-table-bordered table-striped table-hover">
		<THEAD>
		<TR>
			<TH>ID</TH>
			<TH>邀请码</TH>
			<TH>创建者手机号</TH>
			<TH>是否使用</TH>
			<TH>使用者手机号</TH>
		</TR>
		</THEAD>

		<TBODY>

		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><TR>
			<TD><?php echo ($list["id"]); ?></TD>
			<TD><?php echo ($list["code"]); ?></TD>
			<TD><?php echo ($list["phone"]); ?></TD>
			<TD><?php if($list["is_used"] == 1 ): ?>已使用<?php else: ?> 未使用<?php endif; ?></TD>
			<TD><?php echo ($list["used_phone"]); ?></TD>
		</form>

		</TR><?php endforeach; endif; else: echo "" ;endif; ?>

	  </TBODY>

	</TABLE>

	<div><?php echo ($page); ?></div>
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




</body>
</html>