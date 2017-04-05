<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>微市场后台管理</title>
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/bootstrap/css/bootstrap.min.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/bootstrap-ext.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/AdminLTE.css.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/admin.css" media="all" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.10.2.min.js"></script>
    <![endif]-->
	<!--[if gte IE 9]>-->
    <script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-2.1.0.min.js"></script>
    <script type="text/javascript" src="<?php echo RES;?>/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
</head>
<body>
<nav class="navbar navbar-inverse navbar-flat navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="javascript:void(0)">微市场</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav main-nav">
				<li name="_MENU_USERS_"><a href="<?php echo U('Users/index');?>">客户管理</a></li>
				<li name="_MENU_GROUP_"><a href="<?php echo U('System/group');?>">客户组管理</a></li>
				<li name="_MENU_SYSTEM_"><a href="<?php echo U('System/index');?>">系统管理</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="#">账户信息</a></li>
					<li><a href="#">系统消息</a></li>
					<li class="divider"></li>
					<li><a href="<?php echo U('Admin/logout');?>">退出</a></li>
				  </ul>
				</li>
			</ul>
		</div>
	</div>
</nav>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 col-md-2 sidebar">
			
<input type="hidden" id="MENU_TARGET" value="_MENU_SYSTEM_" />
<ul class="nav nav-sidebar">
	<li><a href="<?php echo U('Menu/index');?>">菜单管理</a></li>
</ul>

		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			

		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo STATICS;?>/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(function(){
	var current = $('#MENU_TARGET').val();
	$('.main-nav>li[name="'+current+'"]').addClass('active');
});
</script>


</body>
</html>