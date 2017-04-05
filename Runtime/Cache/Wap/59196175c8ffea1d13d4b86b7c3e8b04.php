<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>带我赚钱</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="wap-font-scale" content="no">
	<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/style.css">
	<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/common.css"> 
	<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/ucenter.css"> 
	<style>
	.video-box {
  width: 100%;
  background: #000;
}
.video-box iframe {
  display: block;
  width: 99% !important;
  height: 225px !important;
  margin: 0 auto;
  padding: 0.1em 0;
}
</style>
</head>
<body class="page-mobile ">

    <div class="wrapper insurance-wrapper">

    <div class="h10"></div>
    <div class="insurance-box" style="min-height: 400px;">
       
     
       <!--  <a href="<?php echo U('about/help');?>" class="insurance-list orange-line">

            <i class="iconfont icon-right"></i>

            <span class="insurance-img"><img src="<?php echo RES;?>/jinrong/Mobile/Public/images/insurance-sltd.png" ></span>

            <span class="insurance-info">新手学堂</span> 

			<p>小白到操盘高手的进阶之路</p>

        </a> -->
        <!--<a href="http://weipan.haohuajie.pw/caidan/zb.html" class="insurance-list blue-line">-->
            <!--<i class="iconfont icon-right"></i>-->
            <!--<span class="insurance-img"><img src="<?php echo RES;?>/jinrong/Mobile/Public/images/insurance-dcjc.png" ></span>-->
            <!--<span class="insurance-info">心跳直播</span>-->
			       <!--<p>气质美女陪你一起赚</p>-->
        <!--</a>-->
        <a href="<?php echo U('user/share');?>" class="insurance-list blue-line">
            <i class="iconfont icon-right"></i>
            <span class="insurance-img"><img src="<?php echo RES;?>/jinrong/Mobile/Public/images/share.png" ></span>
            <span class="insurance-info">分享教程</span>
             <p>快速分享 成为全民经纪人</p>
        </a>
        <a href="<?php echo U('user/play');?>" class="insurance-list blue-line">
            <i class="iconfont icon-right"></i>
            <span class="insurance-img"><img src="<?php echo RES;?>/jinrong/Mobile/Public/images/play.png" ></span>
            <span class="insurance-info">玩法教程</span>
             <p>玩弄于鼓掌 财富滚滚来</p>
        </a>
<div align="center"><a href="http://weipan.haohuajie.pw/caidan/zb.html"><img src="http://weipan.haohuajie.pw/Uploads/886/zb.jpg" width="96%" border="0"  /></a></div>
    </div>
</div>
  <script src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery-1.11.3.min.js"></script>
  <script type='text/javascript'>var ROOT = "";
  </script>
  <script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/common.js"></script>
  <script type="text/javascript">
    $(function(){
        $('.jz-footer ul a').removeClass('active');
        $('.jz-footer ul li').eq(1).find('a').addClass('active');
    })
  </script>

<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/images/iconfont/iconfont.css">
<style>

    .jz-footer {
        height: 56px;
        bottom: 0;
         border-top: 0;
        box-shadow: 0 -3px 3px rgba(5,5,5,0.05);
        background: #3c3b3b!important;
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #fafafa), to(#fafafa));
    }
    .foot_nav li a{
        color:#fff;
    }
</style>
<footer class="jz-footer">

    <ul class="foot_nav jz-flex-row font-lg">
        <li class="jz-flex-col"> <a class="bd active" href="<?php echo U('Trading/index');?>"><i class="jz-icon icon-conduct-null"></i><span>交易</span></a></li>
        <!--<li class="jz-flex-col"> <a class="bd " href="<?php echo U('Trading/dice');?>"><i class="jz-icon iconfont icone-21466"></i><span>玩色子</span></a></li>-->
        <li class="jz-flex-col"><a class="bd " href="<?php echo U('User/insurance');?>"><i class="jz-icon new_icon-zhibo2"></i><span>新手教程</span></a> </li>
        <!--<li class="jz-flex-col"><a class="bd " href="<?php echo U('Trading/jueshengquan');?>"><i class="jz-icon icon-friends01"></i><span>决胜圈</span></a> </li>-->
        <li class="jz-flex-col"><a class="bd " href="<?php echo U('User/invite');?>"><i class="jz-icon icon-vip04"></i><span>全民经纪人</span></a> </li>
		<li class="jz-flex-col"><a class="bd " href="<?php echo U('User/private_person');?>"><i class="jz-icon icon-accounts-null "></i><span>账户</span></a> </li> 

    </ul>

</footer>
</body>
</html>