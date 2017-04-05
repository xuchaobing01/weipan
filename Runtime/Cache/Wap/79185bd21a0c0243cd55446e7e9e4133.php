<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>提现</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="wap-font-scale" content="no">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/style.css">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/common.css"> 
</head>
<style>

.com-columns.span3 .formbox-hd {
    /*min-width: 4em;*/
    min-width: 8em;
    width: 3em;
}
</style>
<body class="page-mobile ">
    <div class="wrapper withdraw-wrapper">
            <ul class="banklist">
            <li class="banklist-item ">
                <a href="javascript:void(0);" class="banklist-box">
                    <div class="banklist-img">
                        <!--<i class="bank-logo bank-jianshe"></i>-->
                        <img src="<?php echo ($user['headimgurl']); ?>" style="border-radius: 50%;width: 37px;    height: 37px;background-position: -148px -37px;display: inline-block;overflow: hidden;">
                    </div>
                    <div class="banklist-info">
                        <h3><?php echo ($user['wechat_name']); ?></h3>
                       <p>余额：<?php echo ($user['money']); ?> 元</p>
                        
                    </div>
                    <span class="banklist-sideicon hide"><i class="iconfont icon-right"></i></span>


                </a>
            </li>
        </ul>
<form method="post" action="<?php echo U('User/cash_out');?>" id='cash_form' >
        <ul class="com-columns span3">
            <!--<li class="comc-item">-->
                <!--<div class="com-formbox">-->
                    <!--<span class="formbox-hd">真实姓名</span>-->
                    <!--<span class="formbox-bd">-->
                        <!--<input type="text"  id="weixin" class="input-txt" placeholder="微信提现和银行提现需要(必填)"  name="weixin"/>-->

                    <!--</span>-->
                <!--</div>-->
            <!--</li>-->
            
            <!--<li class="comc-item">
                <div class="com-formbox">
                     <span class="formbox-hd">支付宝号</span>
                    <span class="formbox-bd">
                        <input type="text"  id="zhifubao" class="input-txt" placeholder="请输入您的支付宝"  name="zhifubao"/>
                    </span>
                </div>
            </li>-->

            <li class="comc-item">
                <div class="com-formbox">
                    <span class="formbox-hd">开户人姓名</span>
                    <span class="formbox-bd">
                        <input type="text"  id="weixin" class="input-txt" placeholder="请输入开户人姓名"  name="weixin" <?php if($usercash != '' ): ?>value="<?php echo ($usercash['weixin']); ?>" readonly="true"<?php endif; ?> />
                    </span>
                </div>
            </li>

            <li class="comc-item">
                <div class="com-formbox">
                    <span class="formbox-hd">身份证号</span>
                    <span class="formbox-bd">
                        <input type="text"  id="idcard" class="input-txt" placeholder="请输入您的身份证号"  name="idcard" <?php if($usercash != '' ): ?>value="<?php echo ($usercash['idcard']); ?>" readonly="true"<?php endif; ?> />

                    </span>
                </div>
            </li>
            
            <li class="comc-item">
                <div class="com-formbox">
                     <span class="formbox-hd">银行卡号</span>
                    <span class="formbox-bd">
                        <input type="text"  id="cardnum" class="input-txt" placeholder="请输入提现银行卡号"  name="cardnum" <?php if($usercash != '' ): ?>value="<?php echo ($usercash['cardnum']); ?>" readonly="true"<?php endif; ?> />
                
                    </span>
                </div>
            </li>
            
             <li class="comc-item">
                <div class="com-formbox">
                     <span class="formbox-hd">开户银行</span>
                    <span class="formbox-bd">
                        <input type="text" maxlength="12" id="card" class="input-txt" placeholder="请输入开户行"  name="card" <?php if($usercash != '' ): ?>value="<?php echo ($usercash['card']); ?>" readonly="true"<?php endif; ?> />

                    </span>
                </div>
            </li>

            <li class="comc-item">
                <div class="com-formbox">
                    <span class="formbox-hd">开户行绑定手机号</span>
                    <span class="formbox-bd">
                        <input type="text" maxlength="12" id="cardphone" class="input-txt" placeholder="请输入开户行绑定手机号"  name="cardphone" <?php if($usercash != '' ): ?>value="<?php echo ($usercash['cardphone']); ?>" readonly="true"<?php endif; ?>  />

                    </span>
                </div>
            </li>
            
            
            
            <li class="comc-item">
                <div class="com-formbox">
                    <span class="formbox-hd">提现金额</span>
                    <span class="formbox-bd">
                        <input type="number" maxlength="12" id="amount" class="input-txt" placeholder="请输入提现金额"  name="amount"/>
                    </span>
                </div>
            </li>
            <!-- <li class="comc-item">
                <div class="com-formbox">
                    <span class="formbox-bd"><input  type="tel"  id="verify_code" name="verify_code" maxlength="6" placeholder="请输入图形验证码" class="input-txt"/></span>
                    <span class="captchabtn"><img src="/mobile.php/verify/verify_img" alt="图形验证码" class="codeImg" id="verify"></span>
                </div>
            </li> -->
            <li class="comc-item">
                <div class="com-formbox">
                    <span class="formbox-bd"><input  type="tel"  id="SMSCode" name="SMSCode" maxlength="6" placeholder="请输入验证码" class="input-txt"/></span>
                    <span class="captchabtn"><a href="javascript:void(0);" class="btn" id="findPwd_btn">获取验证码</a><span class="txt hide" id="wait">60秒后重发</span></span>
                </div>
            </li>

             

        </ul>
</form>
<div class="def-p txtr">
       <!--  <a href="javascript:void(0);" class="fz13 link" id="findPwd_btn">没有接到电话？获取短信验证码</a> -->
</div>
<div class="def-p fcred fz12 pt10"><a href="#">★特别提示：</a> 提交申请前，请确认您已经了解我们的提现政策。如未了解，请点击以下链接</div> 
<div class="def-p fcred fz12 pt10"> <a href="http://weipan.haohuajie.pw/Wap/Index/content/id/1015/token/anuxzf1435128586.html">点此了解 >>>【提现说明】</a> </div>
<div class="def-p fcgray3 fz12 pt10">仅支持提现至实名认证信息办理的支付宝帐号或银行卡内</div> 
<div class="def-p fcred fz12 pt10">提现审核时间为工作日10:00-17:00（每日提现最大次数为3次）</div> 

        <div class="def-p fcgray3 fz12 pt10">通过审核后，提现资金将在10分钟内到账。</div> 

        <div class="def-p fcred fz12 pt10 hide" id="error">超过最大可提现金额</div>
        <!-- 通用按钮 S -->

        <div class="def-p com-btnbox mt20">
            <a href="javascript:void(0);" class="btn btn-1" id="btn_1">提交</a>
        </div>
        
        <!-- 通用按钮 E -->
        <!-- 底部的文字 S -->
        <!--<div class="btm-txt-padding"></div>
        <div class="btm-txt">
            <p><a href="https://www.xiaoying.com/my/withdrawNotice?app_ver=1"  class="link">提现说明</a></p>
        </div>-->
        <!-- 底部的文字 S -->

    </div>
  <div class="geetest" id="codebox">
    <div class="bg"></div>
    <div class="wrap">
      <div class="top">
        <a class="exit" id="close" href="javascript:;"></a>
        <div class="title"> 
          请通过验证
        </div>
      </div>
     
    </div>
  </div>
<div class="diy-dialog hide" id="msg_tips">              
<div class="dia-in">                
<div class="dia-hd"><h2>提示</h2></div>
                <div class="dia-bd"><p id="msg_a"></p></div>
                <div class="dia-btm"><a href="javascript:void(0);" id="isok"><span>我知道了</span></a>
                </div>
                </div>       
                </div>
                
<div class="diy-dialog hide " id="new_tips">              
<div class="dia-in">                
<div class="dia-hd"><h2>提示</h2></div>
                <div class="dia-bd"><p id="new_a">
                提现手续费：300元以下（含300元）收取手续费3元，300元以上收取提现金额1%的手续费</p>
<p id="">

                
                </p></div>
                <div class="dia-btm"><a href="javascript:void(0);" id="issend"><span>我知道了</span></a>
                </div>
                </div>       
                </div>              

<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery-1.11.3.min.js"></script>
<script type='text/javascript'>var ROOT = "";
$("#btn_1").click(function(){
    var weixin = $("#weixin").val();
//    var zhifubao = $("#zhifubao").val();
    var idcard = $("#idcard").val();
    var card = $("#card").val();
    var cardnum = $("#cardnum").val();
    var cardphone = $("#cardphone").val();
    if(weixin==''){
        $("#msg_a").text('请填写开户人姓名');
        $("#msg_tips").removeClass("hide");
        return false;
    }
//     if(zhifubao==''&&cardnum==''){
//         $("#msg_a").text('至少完善一个提现账号信息');
//         $("#msg_tips").removeClass("hide");
//        return false;
//    }
    if(idcard==''){
        $("#msg_a").text('请填写身份证号');
        $("#msg_tips").removeClass("hide");
        return false;
    }
    if(cardnum==''){
         $("#msg_a").text('请填写银行卡号');
         $("#msg_tips").removeClass("hide");
        return false;
    }
    if(cardnum !='' && card==''){
         $("#msg_a").text('请填写开户行信息');
         $("#msg_tips").removeClass("hide");
        return false;
    }
    if(cardphone==''){
        $("#msg_a").text('请填写开户绑定手机号');
        $("#msg_tips").removeClass("hide");
        return false;
    }
     $("#new_tips").removeClass("hide");

  });
$("#issend").click(function() {
    $("#new_tips").addClass("hide");  
    $("#cash_form").submit();
});
</script>
<script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/common.js"></script>
<script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/cash.js?id=1"></script> 

    </body>
</html>

</body>
</html>