<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <title>微市场后台管理</title>
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/bootstrap/css/bootstrap.min.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/font-awesome/css/font-awesome.min.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/AdminLTE.css" media="all" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.10.2.min.js"></script>
    <![endif]-->
	<!--[if gte IE 9]>-->
    <script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-2.1.0.min.js"></script>
    <script type="text/javascript" src="<?php echo RES;?>/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
<style>
	.menu_group input{
		margin-right: 6px;
	}
	.menu_group label{
		cursor: pointer;
	}
	.menu_item{
		margin: 5px 0;
	}
	.menu_item_actions{
		padding-left: 20px;
	}
	.menu_item_actions label{
		margin-right: 10px;
	}
	.group_title{
		margin-top: 10px;
		padding: 6px 5px;
		background: rgb(235, 235, 235);
		border-left: 3px solid rgb(145, 145, 145);
	}
</style>

</head>
<body class="skin-blue">
<header class="header">
			<a href="index.html" class="logo">
                微市场
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope"></i>
                                <span class="label label-success">4</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 4 messages</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li><!-- start message -->
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="img/avatar3.png" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li><!-- end message -->
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="img/avatar2.png" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    AdminLTE Design Team
                                                    <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">See All Messages</a></li>
                            </ul>
                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span>Admin<i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="img/avatar3.png" class="img-circle" alt="User Image" />
                                    <p>
                                        Jane Doe - Web Developer
                                        <small>Member since Nov. 2012</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="#" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
		<div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="active">
                            <a href="<?php echo U('Dashboard/index');?>">
                                <i class="fa fa-dashboard"></i><span>控制台</span>
                            </a>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-user"></i>
                                <span>客户管理</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li>
								<a href="<?php echo U('Users/index');?>"><i class="fa fa-angle-double-right"></i>客户列表</a>
								</li>
                                <li><a href="<?php echo U('Users/add');?>"><i class="fa fa-angle-double-right"></i>新建客户</a></li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-group"></i>
                                <span>用户组管理</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo U('UserGroup/index');?>"><i class="fa fa-angle-double-right"></i>用户组列表</a></li>
                                <li><a href="<?php echo U('UserGroup/edit');?>"><i class="fa fa-angle-double-right"></i>添加用户组</a></li>
                            </ul>
                        </li>
						<li class="treeview">
                            <a href="#">
                                <i class="fa fa-sitemap"></i><span>代理商</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="<?php echo U('Agent/index');?>"><i class="fa fa-angle-double-right"></i>代理商列表</a></li>
                                <li><a href="<?php echo U('Agent/edit');?>"><i class="fa fa-angle-double-right"></i>添加代理商</a></li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-cogs"></i><span>系统管理</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
								<li><a href="<?php echo U('User/index');?>"><i class="fa fa-angle-double-right"></i>系统管理员</a></li>
                                <li><a href="<?php echo U('Custom/index');?>"><i class="fa fa-angle-double-right"></i>官网模板</a></li>
                                <li><a href="<?php echo U('Menu/index');?>"><i class="fa fa-angle-double-right"></i>菜单管理</a></li>
                                <li><a href="<?php echo U('System/index');?>"><i class="fa fa-angle-double-right"></i>系统设置</a></li>
                            </ul>
                        </li>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>
			<!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
						<?php echo ($meta_title); ?>
                        <small><?php echo ($sub_title); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#">主页</a></li>
                        <li class="active">控制台</li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
				
<div class="container-fluid">
	<form method="post">
	<?php if(is_array($menus)): $i = 0; $__LIST__ = $menus;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$root): $mod = ($i % 2 );++$i;?><div class="panel panel-default menu_group">
		<div class="panel-heading">
			<label><input name="rule[]" class="simple" type="checkbox" <?php if($rules[$root['id']] == true): ?>checked<?php endif; ?> value="<?php echo ($root["id"]); ?>" /><?php echo ($root["title"]); ?></label>
		</div>
		<div class="panel-body">
			<?php $group = '';?>
			<?php if(is_array($root["child"])): $i = 0; $__LIST__ = $root["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i; if($group != $menu['group']): $group=$menu['group'];?>
				<div class="group_title">
				<label><input class="menu_item_group simple" type="checkbox" value="<?php echo ($group); ?>" /><?php echo ($group); ?></label>
				</div><?php endif; ?>
			<div data-group="<?php echo ($group); ?>" class="menu_item">
				<label><input class="menu simple" name="rule[]" <?php if($rules[$menu['id']] == true): ?>checked<?php endif; ?> type="checkbox" value="<?php echo ($menu["id"]); ?>" /><?php echo ($menu["title"]); ?></label>
				<?php if(!empty($menu["actions"])): ?><div class="menu_item_actions">
				<?php if(is_array($menu["actions"])): $i = 0; $__LIST__ = $menu["actions"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$action): $mod = ($i % 2 );++$i;?><label><input class="menu simple" name="rule[]" <?php if($rules[$action['id']]) echo 'checked';?> type="checkbox" value="<?php echo ($action["id"]); ?>" /><?php echo ($action["title"]); ?></label><?php endforeach; endif; else: echo "" ;endif; ?>
				</div><?php endif; ?>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div><?php endforeach; endif; else: echo "" ;endif; ?>
	<button type="submit" class="btn btn-success">保存</button>
	</form>
</div>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
	</div>
<script type="text/javascript" src="<?php echo STATICS;?>/bootstrap/js/bootstrap.min.js"></script>
 <!-- AdminLTE App -->
<script src="<?php echo RES;?>/js/AdminLTE/app.js" type="text/javascript"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->

<script type="text/javascript">
	$(function(){
		$('.panel-heading input').click(function(){
			var sub_inputs = $(this).parents('.panel').find('.panel-body input');
			if($(this).is(':checked')){
				sub_inputs.prop('checked',true);
			}
			else{
				sub_inputs.prop('checked',false);
			}
		});
		
		$('.menu_item_group').click(function(){
			var group = $(this).val();
			var sub_inputs = $('.menu_item[data-group="'+group+'"]').find('input');
			if($(this).is(':checked')){
				sub_inputs.prop('checked',true);
				$(this).parents('.panel').find('.panel-heading input').prop('checked',true);
			}
			else{
				sub_inputs.prop('checked',false);
			}
		});
		
		$('.menu').click(function(){
			var group = $(this).parents('.menu_item').prop('data-group');
			if($(this).is(':checked')){
				$(this).parents('.panel').find('.panel-heading input').prop('checked',true);
				$('.menu_item_group[value="'+group+'"]').prop('checked',true);
			}
		});
		
		$('.menu_item_group').each(function(i,el){
			var group = $(el).val();
			if($('.menu_item[data-group="'+group+'"] input:checked').length>0) $(el).prop('checked',true);
		});
	})
</script>

</body>
</html>