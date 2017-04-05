<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
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
<link type="text/css" rel="stylesheet" href="<?php echo RES;?>/css/default.css" />
<style>
.ui-list .ui-item{
line-height: 30px;
}
.ui-list.icon-left .ui-icon {
	width: 60px;
	height: 60px;
	top: 8px;
}
.ui-list.icon-left > .ui-item {
    padding-left: 78px;
}
.ui-list .ui-item {
    /* line-height: 50px; */
    height: 80px;
}
</style>
</head>
<body class="ui-app" menu-active="MENU_CATE">
<p class="ui-caption">&nbsp;</p>
<ul class="ui-list arrow-right icon-left">
	<?php if(is_array($cates)): $i = 0; $__LIST__ = $cates;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="ui-item">
		<img class="ui-icon" src="<?php echo ($vo["img"]); ?>" /><a href="<?php echo U('Cate/cate?cid='.$vo['id'].'&token='.$token);?>"><?php echo ($vo["name"]); ?></a>
	</li><?php endforeach; endif; else: echo "" ;endif; ?>
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