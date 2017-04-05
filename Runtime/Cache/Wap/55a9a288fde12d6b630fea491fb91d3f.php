<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->

<meta name="apple-mobile-web-app-capable" content="yes" />

<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,height=device-height"/>

<meta name="format-detection" content="telephone=no">

<title>跳转提示</title>

<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/weui.css">

</head>

<body>

<div class="weui_msg">

    <div class="weui_icon_area"><i class="weui_icon_safe weui_icon_safe_warn"></i></div>

    <div class="weui_text_area">

        <h2 class="weui_msg_title"><?php echo ($message['success']); ?></h2>

        <p class="weui_msg_desc"><?php echo ($message['message']); ?></p>

    </div>    <div class="weui_opr_area">

        <p class="weui_btn_area">



            <a href="javascript:history.back(-1);" class="weui_btn weui_btn_default">好的</a>

        </p>

    </div>

    <div class="weui_extra_area">

        页面自动 <a id="href" href="<?php if($message['status'] == 1 ): echo U('User/private_person');?> <?php else: ?>javascript:history.back(-1);<?php endif; ?>">跳转</a> 等待时间： <b id="wait">3</b>

    </div>

</div>

<script type="text/javascript">

(function(){

var wait = document.getElementById('wait'),href = document.getElementById('href').href;

var interval = setInterval(function(){

	var time = --wait.innerHTML;

	if(time <= 0) {

		location.href = href;

		clearInterval(interval);

	};

}, 1000);

})();

</script>

</body>

</html>