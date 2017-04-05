<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,height=device-height"/>
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/ucenter.css">
<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/style.css">
<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/common.css">
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery-1.11.3.min.js"></script>
<style>body {padding-bottom: 60px; }</style>
</head>
<title>设置</title>
<body>
<script type="text/javascript">
var ROOT = '';
</script>
<!--页面主体内容区域编辑开始-->
<section class="jz-wrapper jz-null-bottom">
    <div class="collect-wrap">
        <ul class="list-view collect-view margin-top5">
                        <li>
                <a href="<?php echo U('User/history');?>" class="list-jump" >
                <div class="list-wrap list-wrap1 jz-flex-row jz-flex-vh">
                    <div class="list-left jz-flex-col font-sb">
                        <i class="icon-business2 record-sty record-sty1"></i>联系我们
                    </div>
                </div>
                </a>
            </li>
                        <li>
						
                <a href="<?php echo U('User/change_password');?>" class="list-jump" >
                <div class="list-wrap list-wrap1 jz-flex-row jz-flex-vh">
                    <div class="list-left jz-flex-col font-sb">
                        <i class="icon-asfe-null record-sty record-sty1"></i>修改密码
                    </div>
                </div> 
                </a>
            </li>
			
           
        </ul>
<div class="def-p com-btnbox mb10 mt20">
                <a href="<?php echo U('User/login_out');?>" class="btn btn-4" id="logout">退出登录</a>
            </div>
    </div>
</section>

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