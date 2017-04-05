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

	<ul class="nav nav-tabs wm-tabs">

		<li class="active"><a href="javascript:void(0)">提现管理</a></li>

	

	</ul>

	<div class="page-header wm-page-header">

	 	 <form method="get" action="/User/Member/yongjin.html" class="form-inline">
	 	
	 			
	 	
	 			<label>姓名</label>
	 	
	 			<input name="wechat_name" type="text" value="<?php echo ($_GET['wechat_name']); ?>" class="form-control" />
	 	
	 			<label>手机号</label>
	 	
	 			<input name="phone" type="text" value="<?php echo ($_GET['phone']); ?>" class="form-control" />
	 	
	 			<button class="btn btn-primary" type="submit">
	 	
	 				<span class="glyphicon glyphicon-search"></span>
	 	
	 				查询
	 	
	 			</button>
	 	
	 			</form> 

	</div>

	<TABLE class="table wm-table-bordered table-striped table-hover">

		<THEAD>
		<TR>
			<TH>ID</TH>
			<TH>开户人姓名</TH>
			<TH>身份证号</TH>
			<TH>开户绑定手机</TH>
			<TH>开户行</TH>
			<TH>银行卡</TH>
			<TH>提现金额</TH>
			<TH>提现申请时间</TH>
			<TH>会员姓名</TH>
			<TH>状态</TH>
			<TH>操作</TH>
		</TR>
		</THEAD>

		<TBODY>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><TR>
			<TD><?php echo ($list["id"]); ?></TD>
			<TD><?php echo ($list["weixin"]); ?></TD>
			<TD><?php echo ($list["idcard"]); ?></TD>
			<TD><a href="<?php echo U('Member/detail',array('id'=>$list['userid']));?>"><?php echo ($list["cardphone"]); ?></a></TD>
			<TD><?php echo ($list["card"]); ?></TD>
			<TD><?php echo ($list["cardnum"]); ?></TD>
			<TD><?php echo ($list["price"]); ?></TD>
			<TD><?php echo (date('Y-m-d H:i',$list["createtime"])); ?></TD>
			<TD><?php echo ($list["wechat_name"]); ?></TD>
			<TD>
				<?php if($list["status"] == 2): ?><span style="color:red">已审核</span>
				<?php elseif($list["status"] == 1): ?>
					<span style="color:green">打回</span>
				<?php else: ?>
					未审核<?php endif; ?>
			</TD>
			<TD>
				<?php if($list["status"] == 0): ?><a class="btn btn-xs btn-danger" onclick="spark.confirm_jump('确认已给该订单打款，是否确定','/User/Member/queren_yongjin/id/<?php echo ($list["id"]); ?>.html')">支付宝打款</a>
					<a class="btn btn-xs btn-danger" onclick="spark.confirm_jump('确认已给该订单打款，是否确定','/User/Member/tixian_yongjin/id/<?php echo ($list["id"]); ?>.html')">微信打款</a>
					<a class="btn btn-xs " onclick="spark.confirm_jump('用户提现金额返回到余额中，是否确定','/User/Member/back_yongjin/id/<?php echo ($list["id"]); ?>.html')">返回</a><?php endif; ?>
			</TD>
		</form>

		</TR><?php endforeach; endif; else: echo "" ;endif; ?>

	  </TBODY>

	</TABLE>

	<div><?php echo ($page); ?></div>

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
	

<script src="<?php echo RES;?>/js/date/WdatePicker.js"></script>


</body>
</html>