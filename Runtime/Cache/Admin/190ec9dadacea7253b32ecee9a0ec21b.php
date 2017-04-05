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
			
<ul class="nav nav-sidebar">
<li><a href="<?php echo U('Users/index');?>">平台用户</a></li>
	<li  class="active"><a href="<?php echo U('Users/add');?>">添加用户</a></li>
	<li><a href="#">用户统计</a></li>
	<li><a href="<?php echo U('UserGroup/index');?>">用户组</a></li>
	<li><a href="#">添加用户组</a></li>
</ul>
<input type="hidden" id="MENU_TARGET" value="_MENU_USERS_" />

		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			
<div class="container2">
	<?php if(($info["id"]) > "0"): ?><form class="form-horizontal" action="<?php echo U('Users/edit');?>" method="post" name="form" id="myform">
	<input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" />
	<?php else: ?>
	<form class="form-horizontal" action="<?php echo U('Users/add');?>" method="post" name="form" id="myform"><?php endif; ?>
		<div class="form-group">
			<label class="col-xs-2 control-label">用户名称</label>
			<div class="col-md-6">
				<input type="text" name="username" class="form-control" value="<?php echo ($info["username"]); ?>" <?php if(($info["username"]) == "admin"): ?>readonly="readonly"<?php endif; ?>>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-2 control-label">密　　码</label>
			<div class="col-md-6">
				<input type="password" name="password" class="form-control" value="" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-2 control-label">确认密码</label>
			<div class="col-md-6">
				<input type="password" name="repassword" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-2 control-label">用户等级</label>
			<div class="col-md-6">
				<select name="gid" class="form-control">
					<?php if(is_array($role)): $i = 0; $__LIST__ = $role;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($vo["id"]) == $info["gid"]): ?>selected=""<?php endif; ?> ><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-2 control-label">用户状态</label>
			<div class="col-md-6">
				<label class="radio-inline">
				<input type="radio" class="radio" value="1" name="status" id="status1" <?php if(($info["status"] == 1) OR ($info['status'] == '') ): ?>checked=""<?php endif; ?> >启用
				</label>
				<label class="radio-inline">
				<input type="radio" class="radio" value="0" name="status" id="status2" <?php if(($info["status"]) == "0"): ?>checked=""<?php endif; ?> >
					禁用
				</label>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-2 control-label">到期时间</label>
			<div class="col-md-6">
				<input type="text" onClick="WdatePicker()"  name="viptime" id="viptime" class="form-control" value="<?php echo (date('Y-m-d',$info["viptime"])); ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-2 control-label">备注说明</label>
			<div class="col-md-6">
				<input type="text" name="remark" class="form-control" value="<?php echo ($info["remark"]); ?>"/>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-6 col-xs-offset-2">
				<input type="submit" class="btn btn-success" value="保存" />
			</div>
		</div>
	</form>
</div>

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

<script src="/Public/User/js/date/WdatePicker.js"></script>
<script type="text/javascript">
</script>

</body>
</html>