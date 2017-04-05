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
	


	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	

    <div class="container-fluid">

        <div class="page-header">

            <h3 style="color:rgb(77, 112, 148)"><?php echo ($wxaccount['wxname']); ?></h3>

            <span class="label label-primary" style="background-color:rgb(209, 202, 20);"><?php echo ($wxaccount['wxtype']); ?></span>

        </div>

        <table class="table table-bordered">

            <tbody>

            <tr>

                <td>会员名: <?php echo ($user['username']); ?></td>
                <td>手机: <?php echo ($user['phone']); ?></td>

                <td>注册时间: <?php echo date('Y-m-d H:i:s',$user['create_time'])?></td>


            </tr>

            <tr><td colspan="3"></td></tr>

            <tr>

                <td>线上总人数: <?php echo ((isset($user["count"]) && ($user["count"] !== ""))?($user["count"]):0); ?></td>
                <td>线上总充值: <?php echo ((isset($user["pay"]) && ($user["pay"] !== ""))?($user["pay"]):0); ?></td>

                <TD>当前盈亏: <?php echo $user['win']-$user['win_amont'] ;?>/<?php echo ((isset($user["las"]) && ($user["las"] !== ""))?($user["las"]):0); ?></TD>





            </tr>
            <tr><td colspan="3"></td></tr>
            <tr>

                <td>已结算金额: <?php echo ((isset($user["allgive"]) && ($user["allgive"] !== ""))?($user["allgive"]):0); ?></td>


                <td>结算时间:  <?php if($user['givetime']>0){echo date('Y-m-d H:i:s',$user['givetime']);}?></td>



            </tr>




            </tbody>

        </table>

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
	


</body>
</html>