<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<title>收货地址</title>
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
<link type="text/css" rel="stylesheet" href="/Public/Shop/css/default.css">
<script charset="utf-8" type="text/javascript" src="/Public/Shop/js/dialog.js" id="dialog_js"></script>
<script charset="utf-8" type="text/javascript" src="/Public/Shop/js/jquery.validate.js" ></script>
<script charset="utf-8" type="text/javascript" src="/Public/Shop/js/mlselection.js" ></script>
</head>
<body>
<div id="content">
    <div class="wrap">
        <ul class="address_list">
        <?php if(is_array($address_list)): $i = 0; $__LIST__ = $address_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                <p><?php echo ($vo["consignee"]); ?>(<?php echo ($vo["mobile"]); ?>)</p>
                <p><?php echo ($vo["sheng"]); ?>&nbsp;<?php echo ($vo["shi"]); ?>&nbsp;<?php echo ($vo["qu"]); ?>&nbsp;<?php echo ($vo["address"]); ?></p>
                <p class="new_line"><br /></p>
                <p class="address_action">
                    <span class="edit">
					<a href="<?php echo U('User/edit_address',array('id'=>$vo['id'],'token'=>$token));?>"><i class="fa fa-edit"></i>&nbsp;编辑</a>
					</span>
                    <span>
					<a href="<?php echo U('User/address',array('id'=>$vo['id'],'type'=>'del','token'=>$token));?>" class="delete float_none"><i class="fa fa-trash"></i>&nbsp;删除</a>
					</span>
                </p>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
		<a class="ui-button ui-success ui-block" href="<?php echo U('User/addaddress',['token'=>$token]);?>">新增地址</a>
    </div>
</div>
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