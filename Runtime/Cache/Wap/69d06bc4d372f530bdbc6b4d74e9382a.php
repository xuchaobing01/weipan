<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>充值</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="wap-font-scale" content="no">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/style.css">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/common.css">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/pay.css">
</head>
<body>
<div class="mobile_wrap">
    <div class="balance">
        <h2>余额<i><?php echo ($user["money"]); ?></i>元</h2>
    </div>

    <div class="active">
        <h1>充<b>500</b>元 </h1>
        <h2>支付宝、银联充值快速到账</h2>
        <ul>
            <li>
                <p><i>5000</i>元</p>
                
            </li>
            <li>
                <p><i>2000</i>元</p>
                
            </li>
            <li>
                <p><i>1000</i>元</p>
                
            </li>
            <li>

                <p><i>800</i>元</p>
                
            </li>
            <li class="slct">
                <p><i>500</i>元</p>
                
            </li>
            <li>
                <p><i>300</i>元</p>
                
            </li>
            <li>
                <p><i>200</i>元</p>
                
            </li>
            <li>
                <p><i>100</i>元</p>
                
            </li>
            <li class="not">
                <p>其它金额</p>
                <input type="number" value="" id="input_money"/>
            </li>
        </ul>
    </div>

    <div class="prompt" id="top">
        <p>提示：支付宝、银联充值快速到账。港云外汇不限支付金额，若提示订单超出单笔限额，请核实您账户及网银的每日消费限额。<!-- <a href="recharge_no.html" style="color: #ffed20;">不要返现直接充值
</a> --></p>
        <input class="but_sub" type="submit" value="马上充值" />
        <span><input type="checkbox" /><i></i>我已阅读并同意《充返活动协议》，知悉充值返现金额满足活动要求即可提现。<a>查看协议详情</a></span>
    </div>

    <div class="explain">
        <div class="text">
            <p>尊敬的用户、为保障您的合法权益，请您在充值前仔细阅读本协议。在您点击“马上充值”按钮后，我们默认您已经知悉如下活动条款。</p>
        </div>

        <div class="text">
            <p>一.余额构成</p>
            <p>您实际支付的充值本金加上港云外汇的赢取的利润会构成您的账户余额（人民币）。</p>
        </div>
        <div class="text">
            <p>二.充值余额使用规则</p>
            <p>余额可用于在港云外汇中进行各类投资。无任何限制。</p>
        </div>
        <div class="text">
            <p>三.特别声明</p>
            <p>1.请您根据自己的投资情况进行充值，港云外汇对充值次数不设任何限制；</p>
            <p>2.充返活动福利仅提供给正当、合法使用港云外汇客户。每位参与者的港云外汇账号、手机设备号、身份证号和微信号都必须是唯一的，任意信息与其他用户重复都不能参加该活动； 活动中，一旦发现作弊行为，港云外汇有权取消相关账户活动返现金额、追回作弊所得（对应赠送奖品）、回收账号使用权，并保留取消作弊人后续参与港云外汇任何活动的权利，必要时会追究其法律责任；
            </p>
            <p>3.本系统内容最终解释权归港云外汇所有。</p>
        </div>
    </div>
    <div class="box_show">
        <div class="layout">
            <h1>温馨提示</h1>
            <p>充100元以上才能享受畅玩哦 确定不要畅玩吗</p>
            <a href="#" class="solid" id="close_win">我要畅玩</a>
            <a href="#" id="gotopay">不要畅玩，继续充值</a>
        </div>
    </div><!--mobile_wrap-->
    <script type='text/javascript'>var ROOT = "";</script>
    <script src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery.touchSlider.js"></script>
    <script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery.event.drag.js"></script>
    <script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/common.js"></script>
    <script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/pay.js"></script>
    <div class="loading-wrapper" style="display: none;">
        <div class="loading-area">
            <div id="floatingBarsG1" class="floatingBarsG"></div>
            <p id="msg">提交中...</p>
        </div>
        <div class="mask" style="opacity: 0.3;"></div>
    </div>
</body>
</html>

<!--
	<form method="post" action="/mobile.php/Weixin/weixinpay" data-ajax='false'>
    <div data-role="contentmain_L" style="width:96%; margin:0 auto;">
        <div class="ui-grid-a  main_L_bg" style="width:100%;">
           		 <div class="ui-block-a">
               		 <h4 style=" margin-left:16px;">
               		     入金金额：
               		 </h4>
            	</div>
           		 <div class="ui-block-b main_L_W">
                	<div data-role="fieldcontain">
                   		 <input class="input_L" name="total_fee" id="total_fee" placeholder="请输入金金额" value="" type="text"
                  		  data-mini="true">
               		 </div>
            	</div>
        </div>
		<input type="submit" value="确 认" data-role="button"  data-transition="none" class="button_L">
	</div>
	</form>
</div>
-->
</body>
</html>