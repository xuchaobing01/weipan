<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<title>订单列表</title>
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
<link type="text/css" rel="stylesheet" href="/Public/Common/bootstrap/css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="/Public/Common/flat-ui/css/flat-ui.css" />
<link type="text/css" rel="stylesheet" href="/Public/Shop/css/default.css">
<style>
	body{
		background-color: rgb(238, 238, 238);
	}
	h4,h5{
		font-size: 18px;
	}
	.order-item .media-object{
		max-width: 100px;
	}
	.order-item .media-heading{
		font-size: 16px;
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
</style>
</head>
<body>
<div style="padding-top: 20px;">
	<?php if(!empty($item_orders)): if(is_array($item_orders)): $i = 0; $__LIST__ = $item_orders;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><ul class="order-list">
		<li class="order">
			<div class="order-top">订单号:  <a href="<?php echo U('Order/checkOrder',array('orderId'=>$vo['orderId'],'status'=>$status,'token'=>$token));?>"><?php echo ($vo["orderId"]); ?></a></div>
			<ul class="media-list">
				<?php if(is_array($vo["items"])): $i = 0; $__LIST__ = $vo["items"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><li class="media order-item">
					<a class="pull-left" href="<?php echo U('Item/index',array('id'=>$item['itemId'],'token'=>$token));?>">
					  <img class="media-object" src="<?php echo ($item["img"]); ?>" alt="">
					</a>
					<div class="media-body">
						<h5 class="media-heading"><?php echo ($item["title"]); ?></h5>
						<?php if($vo['status'] == 4): if($vo['has_comment'] == 0): ?><a class="pull-right" href="<?php echo U('Order/comment',['token'=>$token,'order_id'=>$vo['orderId'],'item_id'=>$item['itemId']]);?>">去评价</a>
						<?php else: ?>
							<a class="pull-right" href="<?php echo U('Order/comment',['token'=>$token,'order_id'=>$vo['orderId'],'item_id'=>$item['itemId']]);?>">查看评价</a><?php endif; endif; ?>
						<?php if(!empty($item["spec"])): ?><p>规格：<?php echo ($item["spec"]); ?></p><?php endif; ?>
						<p>¥<?php echo ($item["oprice"]); ?>&nbsp;x&nbsp;<?php echo ($item["quantity"]); ?></p>
					</div>
				</li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
			<div class="order-bottom">
				<div>总价: ¥<b><?php echo ($vo["order_sumPrice"]); ?></b>&nbsp;&nbsp;运费: ¥<b><?php echo ($vo["freeprice"]); ?></b></div>
			</div>
			<div class="order-action">
				<?php if($vo["status"] == 1): ?><a href="<?php echo U('Order/pay',array('orderId'=>$vo['orderId'],'token'=>$token));?>" id="order118_action_pay" class="btn btn-xs btn-danger">付款</a>
				<a href="<?php echo U('Order/cancelOrder',array('orderId'=>$vo['orderId'],'token'=>$token));?>" class="btn btn-xs btn-default">取消订单</a>
				<?php elseif($vo["status"] == 2): ?>
				<a class="btn btn-xs btn-default" href="<?php echo U('Order/checkOrder',array('orderId'=>$vo['orderId'],'status'=>$status,'token'=>$token));?>">查看订单</a>
				<?php elseif($vo["status"] == 3): ?>
				<?php $ellipse = time() - $vo['fahuo_time'];$timer=7*24*3600 - $ellipse; ?>
				<a href="<?php echo U('Order/confirmOrder',array('orderId'=>$vo['orderId'],'status'=>$status,'token'=>$token));?>" class="btn btn-xs btn-warning time-ticker" data-time="<?php echo ($timer); ?>" data-text="确认收货">确认收货</a>
				<?php if($vo['has_apply'] == true): ?><a class="btn btn-xs btn-default" href="<?php echo U('Order/back',array('orderId'=>$vo['orderId'],'status'=>$status,'token'=>$token));?>">查看申请</a>
				<?php elseif($ellipse < 32*3600): ?>
				<a class="btn btn-xs btn-default" href="<?php echo U('Order/back',array('orderId'=>$vo['orderId'],'status'=>$status,'token'=>$token));?>">申请退换货</a><?php endif; ?>
				<a class="btn btn-xs btn-default" href="<?php echo U('Order/checkDelivery?name='.$vo['userfree'].'&oid='.$vo['freecode'].'&token='.$token);?>">查看物流</a>
				<?php elseif($vo["status"] == 4): ?>
				<a class="btn btn-xs btn-default" href="<?php echo U('Order/checkOrder',array('orderId'=>$vo['orderId'],'status'=>$status,'token'=>$token));?>">查看订单</a><?php endif; ?>
			</div>
		</li>
	</ul><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    <div class="wrap_line margin1" style="display:none;">
		<div class="public_index">
			<div class="information_index">
				<div class="awoke">
					您目前还没有已生成的订单<br>去<a href="/mall/index.php?token=".<?php echo ($token); ?>>商城首页</a>，挑选喜爱的商品，体验购物乐趣吧。
				</div>
			</div>

		</div>
		<div class="wrap_bottom"></div>
	</div>
    <div class="clear"></div>
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
<script>
	function tick(item){
		var intDiff = item.time;
		var day=0,
	        hour=0,
	        minute=0,
	        second=0;//时间默认值        
	    if(intDiff > 0){
	        day = Math.floor(intDiff / (60 * 60 * 24));
	        hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
	        minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
	        second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
	    }
	    if (minute <= 9) minute = '0' + minute;
	    if (second <= 9) second = '0' + second;
		var s = day+"天 "+hour+':'+minute+':'+second+' 确认收货';
	    $(item.el).html(s);
		item.time--;
	}
	var els = [];
	$(function(){
		$('.time-ticker').each(function(i,el){
			var timer = parseInt($(el).attr('data-time'));
			if(timer>0) els.push({el:el,time:timer,text:$(el).attr('data-text')});
		});
		
		if(els.length>0){
			window._interval = window.setInterval(function(){
		    	for(var i=0;i<els.length;i++){
					tick(els[i]);
				}
		    }, 1000);
		}
	});
</script>
</body>
</html>