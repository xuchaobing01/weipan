<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<meta charset="utf-8" />
	<meta name="keywords" content="微市场 微信公众号服务平台"/>
	<meta name="description" content="微信公众号服务平台  微官网 微相册 微客服  微订单"/>
	<meta name="baidu-site-verification" content="pKfXuVuMa3" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<title><?php echo C('PLATFORM_NAME');?></title>
	
	
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/bootstrap/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="/Public/Admin/me/css/bootstrap-me.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/font-awesome/css/font-awesome.min.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo STATICS;?>/pnotify/pnotify.custom.min.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo STATICS;?>/datetimepicker/css/bootstrap-datetimepicker.min.css" />
	<link rel="stylesheet" href="<?php echo STATICS;?>/validation/css/validationEngine.jquery.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/bs-context.css"/>
	
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	
<style type="text/css">
.vipcard{
  margin: 0 auto;
  position: relative;
  height: 159px;
  text-align: left;
  width: 267px;
}

#cardbg{
  height: 159px;
  width: 267px;
  position:absolute;
  border-radius: 8px;
  -webkit-border-radius:8px;
  -moz-border-radius:8px;
  box-shadow: 0 0 4px rgba(0, 0, 0, 0.6);
  -moz-box-shadow:0 0 4px rgba(0, 0, 0, 0.6);
  -webkit-box-shadow:0 0 8px rgba(0, 0, 0, 0.6);
  top:0;
  left:0;
  z-index:1;
}
.vipcard .logo {
  max-height:70px;
  position:absolute;
  top:8px;
  left:5px;
  z-index:2;
  width: 30px;
}
.vipcard .verify {
  display:inline-block;
  height:40px;
  top:105px;
  right:12px;
  text-align:right;
  line-height:24px;
  color:#000;
  font-size:20px;
  text-shadow:0 1px rgba(255, 255, 255, 0.2);
  z-index:2;
}

.vipcard h1 {
  position:absolute;
  right:10px;
  top:7px;
  text-shadow:0 1px rgba(255, 255, 255, 0.2);
  color:#000;
  font-size:11px;
  line-height:25px;
  text-align:right;
  font-weight: normal;
  z-index:2;
}
.vipcard .verify span {
display:inline-block;
text-align:left;
}
.vipcard .verify em {
display:block;
line-height:13px;
font-size:10px;
font-weight:normal;
font-style:normal;
}
.pdo {
  position:absolute;
  top:0;
  left:0;
  display:inline-block;
}
.userinfoArea td {
    padding: 8px 0 0px 15px;
}
#tishi{
  text-align: center;display: block;
}
.banner{
  display:block; width:213px;height: 278px;overflow: hidden;
}
.banner img{
  display:block; width:213px; border:0;
}
.bannerbtn{ position:relative; display:block}
.bannerbtn .qiaodaobtn{ position: absolute; display:block; bottom:0}
</style>

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
  <!-- Nav tabs -->
<div class="page-header">
        <h3>发送微信消息-所有人</h3>
    </div>

  <!-- Tab panes -->
  <form class="form-horizontal" method="post">
  <div class="tab-content">
    <!-- 会员卡设计 -->
    
    <div class="tab-pane active" id="home">    
       
        <div class="form-group">
          <label class="col-sm-2">内容</label>
          <div class="col-md-4">
            <input type="text" name="content"  id="type_name" class="form-control" />
            <span class="help-block">如：港云外汇新功能上线，欢迎你的参与。</span>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2">链接地址</label>
          <div class="col-md-4">
            <input type="text" name="link_url" value="<?php echo ($card["link_url"]); ?>" id="cardname" class="form-control" />
            <span class="help-block">如：http://weipan.haohuajie.pw/</span>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2">链接文字</label>
          <div class="col-md-4">
            <input type="text" name="link_name" value="" id="base_expense" class="form-control" />
            <span class="help-block">点击进入</span>
          </div>
        </div>
       
        <div class="form-group">
          <div class="col-md-4 col-sm-offset-2">
            <input type="submit" class="btn btn-primary" value="发送" />
          </div>
        </div>
    </div>

  </div>
  </form>
</div>

	<script type="text/javascript" src="<?php echo STATICS;?>/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php echo STATICS;?>/pnotify/pnotify.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/bootstrap-dialog/js/bootstrap-dialog.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery.ajaxfileupload.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery.form.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/js/spin.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine-zh_CN.js"></script>
	<script src="<?php echo STATICS;?>/spark/weimarket.admin.js"></script>
	
<script src="<?php echo STATICS;?>/artDialog/jquery.artDialog.js?skin=default"></script>
<script src="<?php echo STATICS;?>/artDialog/plugins/iframeTools.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/js/upload.js?ver=<?php echo rand(0,9999);?>"></script>
<script type="text/javascript" src="<?php echo RES;?>/js/date/WdatePicker.js"></script>
<script type="text/javascript" src="<?php echo RES;?>/js/cart/jscolor.js"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/js/holder.min.js"></script>

</body>
</html>