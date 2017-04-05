<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<title>用户中心</title>
<meta charset="utf-8" />
<meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0" name="viewport" />
<link type="text/css" rel="stylesheet" href="/Public/Common/font-awesome/css/font-awesome.min.css" />
<link type="text/css" rel="stylesheet" href="/Public/Common/spark/h5.css" />
<script charset="utf-8" src="/Public/Common/js/jquery-2.1.0.min.js" type="text/javascript"></script>
<style>
.ui-top-bar, .ui-bottom-bar{
	background-image:none;
	border-top:1px solid <?php echo ($config["theme_color"]); ?>;
}
.ui-bottom-bar-button.js-active .ui-icon {
color: <?php echo ($config["theme_color"]); ?>;
}
.ui-bottom-bar-button.js-active {
color: <?php echo ($config["theme_color"]); ?>;
}
</style>
<link type="text/css" rel="stylesheet" href="/Public/Shop/css/default.css" />
</head>
<body menu-active="MENU_USER" class="ui-app">
	<div class="ui-caption">我的订单</div>
	<ul class="ui-list arrow-right">
		<li class="ui-item"><a href="<?php echo U('order_list',['token'=>$token,'status'=>1]);?>">待付款<span class="ui-right"><span class="ui-gray"><?php echo ($order_count[0]); ?></span></span></a></li>
		<li class="ui-item"><a href="<?php echo U('order_list',['token'=>$token,'status'=>2]);?>">待发货<span class="ui-right"><span class="ui-gray"><?php echo ($order_count[1]); ?></span></span></a></li>
		<li class="ui-item"><a href="<?php echo U('order_list',['token'=>$token,'status'=>3]);?>">待收货<span class="ui-right"><span class="ui-gray"><?php echo ($order_count[2]); ?></span></span></a></li>
		<li class="ui-item"><a href="<?php echo U('order_list',['token'=>$token,'status'=>4]);?>">已完成<span class="ui-right">待评价<span class="ui-red"><?php echo ($order_count[3]); ?></span></span></a></li>
		<li class="ui-item"><a href="<?php echo U('order_list',['token'=>$token,'status'=>5]);?>">已取消<span class="ui-right"><span class="ui-gray"><?php echo ($order_count[4]); ?></span></span></a></li>
	</ul>
	<ul class="ui-list arrow-right">
		<li class="ui-item"><a href="<?php echo U('User/collect_list',['token'=>$token]);?>">我的收藏<span class="ui-right"></span></a></li>
		<li class="ui-item"><a href="<?php echo U('User/address',['token'=>$token]);?>">收货地址<span class="ui-right"><span class="ui-gray"><?php echo ($addr_count); ?></span></span></a></li>
	</ul>
	﻿<div id="footer">
    <p class="foot_nav">
        <a href="/shop/index/index?token=<?php echo ($token); ?>">商城首页</a> | <a href="/shop/user/index?token=<?php echo ($token); ?>">会员中心</a>
    </p>
</div>
<nav class="ui-bottom-bar">
	<a data-id="MENU_INDEX" class="ui-bottom-bar-button" href="/shop/index/index?token=<?php echo ($token); ?>">
		<span class="ui-icon fa fa-home"></span>
		<span class="ui-label">主页</span>
	</a>
	<a data-id="MENU_CATE" class="ui-bottom-bar-button" href="/shop/cate/index?token=<?php echo ($token); ?>">
		<span class="ui-icon fa fa-list"></span>
		<span class="ui-label">分类</span>
	</a>
	<a data-id="MENU_CART" class="ui-bottom-bar-button" href="/shop/cart/index?token=<?php echo ($token); ?>">
		<span class="ui-icon fa fa-shopping-cart"></span>
		<span class="ui-label">购物车</span>
	</a>
	<a data-id="MENU_USER" class="ui-bottom-bar-button" href="/shop/user/index?token=<?php echo ($token); ?>">
		<span class="ui-icon fa fa-user"></span>
		<span class="ui-label">我的账户</span>
	</a>
</nav>
<script type="text/javascript" src="/Public/Common/spark/h5.js"></script>
<script type="text/javascript">
	$(function(){
		$('.ui-bottom-bar >a[data-id="'+$('body').attr('menu-active')+'"]').addClass('js-active');
	})
</script>
</body>
</html>