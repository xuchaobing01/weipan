<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
<title>我的购物车</title>
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
<link type="text/css" rel="stylesheet" href="/Public/Common/bootstrap/css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="/Public/Common/flat-ui/css/flat-ui.css" />
<style>
	body{
		background-color: rgb(238, 238, 238);
	}
	.order-item .media-object{
		max-width: 100px;
	}
	.media{
		padding: 5px 0;
	}
	.order-item .media-heading{
		font-size: 18px;
	}
	.order{
		background-color:white;
		color: rgb(105, 105, 105);
		padding:5px 15px;
		border-top: 1px solid rgb(221, 221, 221);
		/*border-bottom: 1px solid rgb(221, 221, 221);*/
	}
	.order-top{
		padding-bottom: 5px;
		border-bottom: 1px solid rgb(221, 221, 221);
	}
	.order-bottom{
		padding-top: 5px;
		text-align:right;
		border-top: 1px solid rgb(221, 221, 221);
	}
	.order-action{
		padding-top: 5px;
		text-align:right;
		border-top: 1px solid rgb(221, 221, 221);
	}
	.order .media-list{
		margin: 10px 0;
	}
	.time-ticker{
		font-size: 10px;
	}
	.media-list .media:not(:last-child){
		border-bottom: 1px solid rgb(221, 221, 221);
	}
	.item_num_set{
		padding: 5px 0;
	}
	.item_num{
		border: 0;
		outline: 0;
		text-align: center;
		width: 40px;
		border-bottom: 1px solid rgb(109, 109, 109);
	}
	.item_trash{
		font-size: 18px;
		padding: 5px;
	}
</style>
</head>
<body class="ui-app" menu-active="MENU_CART">
<div style="padding-top: 20px;">
	<?php if(count($item)==0){ ?>
	<div class="null_shopping">
        <div class="cart_pic"></div>
        <h4>您还没有宝贝，赶快去逛逛吧！</h4>
        <p>
            <a class="enter" href="<?php echo U('Index/index?token='.$token);?>">马上去逛逛</a>
        </p>
    </div>  
   <?php }else{ ?>
	<ul class="order-list">
		<li class="order" id="cart_item_<?php echo ($vo["id"]); ?>">
			<div class="order-top text-success">商品清单</div>
			<ul class="media-list">
				<?php if(is_array($item)): $i = 0; $__LIST__ = $item;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="media order-item">
					<a class="pull-left" href="<?php echo U('Item/index',array('id'=>$vo['itemId'],'token'=>$token));?>">
					  <img class="media-object" src="<?php echo ($vo["img"]); ?>" alt="">
					</a>
					<div class="media-body">
						<h5 class="media-heading"><?php echo ($vo["title"]); ?></h5>
						<p>
							¥<small><?php echo ($vo["oprice"]); ?></small>
							<span class="pull-right" id="item<?php echo ($vo["id"]); ?>_subtotal">¥<?php echo sprintf("%01.2f",$vo['num']*$vo['oprice']); ?></span>
						</p>
						<div class="item_num_set">
							<span onClick="decrease_quantity(<?php echo ($vo["id"]); ?>);" class="btn btn-xs btn-success"><i class="fa fa-minus"></i></span>
							<input id="input_item_<?php echo ($vo["id"]); ?>" value="<?php echo ($vo["num"]); ?>" orig="1" changed="<?php echo ($vo["num"]); ?>" onKeyUp="change_quantity(<?php echo ($vo["id"]); ?>, this,'<?php echo ($token); ?>');"type="number"  class="item_num" name="quantity" value="<?php echo ($vo["num"]); ?>" />
							<span onClick="add_quantity(<?php echo ($vo["id"]); ?>);" class="btn btn-xs btn-success"><i class="fa fa-plus"></i></span>
							<span onClick="drop_cart_item(<?php echo ($vo["id"]); ?>,'<?php echo ($token); ?>');" class="text-danger item_trash pull-right"><i class="fa fa-trash-o"></i></span>
						</div>
					</div>
				</li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
			<div class="order-bottom">
				<div>总价: <b id="cart_amount">¥<?php echo sprintf("%01.2f",$vo['num']*$vo['oprice']); ?></b></div>
			</div>
		</li>
	</ul>
	</volist>
	<div class="container-fluid">
	<a href="<?php echo U('Order/jiesuan?token='.$token);?>" class="btn btn-block btn-danger">去结算</a>
	</div>
	<?php } ?>
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