<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<meta charset="utf-8" />
<meta name="keywords" content="【港云外汇】代理平台"/>
<meta name="description" content="港云外汇代理"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>【港云外汇】代理管理系统</title>
<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/bootstrap/css/bootstrap-flatly.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/user.css" />
<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/bs-context.css"/>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="<?php echo STATICS;?>/js/html5shiv.js"></script>
  <script src="<?php echo STATICS;?>/js/respond.min.js"></script>
<![endif]-->
<link type="text/css" media="all" rel="stylesheet" href="<?php echo RES;?>/css/login.css" />
<script type="text/javascript" src="<?php echo C('CDN_JQUERY');?>"></script>
<style>
  .brand_logo{
    /*background-image: url(<?php echo C('LOGIN_PAGE_LOGO');?>);*/
  }
  .center {
  height: 500px;
  }
</style>
</head>
<body>
  <div class="head">
    <div class="container">
      <div class="brand_logo">
       <!--  <img height="100%" src="/Public/User/images/logo2.jpg" /> -->
      </div>

    </div>
  </div>
  <div class="center">
    <div class="login-container">
      <div class="panel panel-default">
        <div class="panel-heading"><strong style="padding:6px 0;">【港云外汇】代理登录</strong></div>
        <div class="panel-body" style="padding: 15px 28px 20px 28px;">
          <form id="loginForm" class="form-horizontal" action="#" method="post">
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                <input id="user" class="form-control" type="text" placeholder="用户名"/>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                <input id="password" class="form-control" type="password" placeholder="密码" />
              </div>
            </div>
            <div class="form-group">
              <label class="checkbox-inline">
                <input type="checkbox" />自动登录
                <!--<span id="findPassword">忘记密码?</span>-->
              </label>
            </div>
            <div class="form-group">
              <button id="submit" data-loading-text="正在登录..." class="btn btn-block btn-success" type="submit" >登录</button>
            </div>
            <div class="form-group">
              <div id="alertInfo" style="padding:5px 10px;display:none;magin-bottom:0px;font-size:12px;" class="alert alert-danger">登录失败</div>
            </div>
          </form>
        </div>
      </div>
      
    </div> 
  </div>
  <div class="foot-wrapper">
    <div class="footer-line"></div>
    <div class="splitter"></div>
    <div class="foot-info">
      <ul>
        <li><a class="copyright" href="#" target="_blank">花儿网络科技</a></li>
       
        <li><span class="copyright">Copyright © 2013-2017  皖ICP备14006087号-2 </span></li>
      </ul>
    </div>
  </div>
  <script type="text/javascript" src="<?php echo STATICS;?>/bootstrap/js/bootstrap.min.js"></script>
  <script type="text/javascript">
    $("#submit").click(function(e){
      var param={};
      param.username=$("#user").val();
      param.password=$("#password").val();
      if(param.loginId==""){
        $("#alertInfo").html("登录名不能为空！").stop(true,true).show().fadeOut(6000);
        return false;
      }
      else if(param.password==''){
        $("#alertInfo").html("密码不能为空！").stop(true,true).show().fadeOut(6000);
        return false;
      }
      if($("#autoLogin:checked").length>0){
        param.auto_login=1;
      }

      $.ajax({
        url:'<?php echo U("Channel/login");?>',
        type:'post',
        data:param,
        dataType:'json',
        beforeSend:function(){
          $("#user").attr("disabled","true");
          $("#password").attr("disabled","true");
          $("#submit").button('loading');
        },
        success:function(data){
          if(data.statusCode==0){
            window.location.href='<?php echo U("Channel/main");?>';
          }
          else{
            $("#alertInfo").html(data.message).stop(true,true).show().fadeOut(6000);
            $("#submit").button("reset");
          }
          $("#user").removeAttr("disabled");
          $("#password").removeAttr("disabled");
          
        },
        error:function(){
          $("#alertInfo").html("用户名或密码错误！").stop(true,true).show().fadeOut(6000);
          $("#user").removeAttr("disabled");
          $("#password").removeAttr("disabled");
          $("#submit").button('loading');
        }
      });
      return false;
    });
  </script>
</body>
</html>