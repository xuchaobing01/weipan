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

		<li class="active"><a href="javascript:void(0)">未审核</a></li>
		<li><a href="<?php echo U('Member/approved');?>">已审核</a></li>

	</ul>

	<TABLE class="table wm-table-bordered table-striped table-hover">

		<THEAD>

		<TR>

			<TH>ID</TH>

			<TH>微信名称</TH>

			<TH>内容</TH>

			<TH>图片</TH>

			<TH>发布时间</TH>
			<TH>操作</TH>


		</TR>

		</THEAD>

		<TBODY>

		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><TR>

			<TD><?php echo ($list["id"]); ?></TD>

			<TD><?php echo ($list["uname"]); ?></TD>

			<TD><?php echo ($list["Content"]); ?></TD>
			<TD>
				<?php if(is_array($list['imglist'])): $i = 0; $__LIST__ = $list['imglist'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><a href="javascript:void(0);" onclick="previewImg2(this);">
						<img src="<?php echo ($img); ?>" style="width:40px;height:40px;">
					</a><?php endforeach; endif; else: echo "" ;endif; ?>
			</TD>
			<TD><?php echo (date('Y-m-d H:i',$list["create_time"])); ?></TD>



			<TD>
				<a class="btn btn-xs" onclick="javascript:spark.confirm_jump('确定审核通过此信息，是否确定','/User/Member/approveInfo/id/<?php echo ($list["id"]); ?>.html')">审核</a>
				<a class="btn btn-xs" onclick="javascript:spark.confirm_jump('确定删除此信息，是否确定','/User/Member/delcomment/id/<?php echo ($list["id"]); ?>.html')">删除</a>
			</td>
		</form>

		</TR><?php endforeach; endif; else: echo "" ;endif; ?>

	  </TBODY>

	</TABLE>

	<div><?php echo ($page); ?></div>

</div>
	<script>
        function previewImg2(obj, width,height){
            var url = $(obj).find("img").attr("src");
            width=width?(width+"px"):"auto";
            height=height?(height+"px"):"auto";
            if(url){
                var html='<img src="'+url+'" style="height:'+height+';width:'+width+';" />';
            }
            else{
                var html='没有图片';
            }
            art.dialog({title:'图片预览',content:html});
        }
	</script>

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
<script src="<?php echo RES;?>/js/date/WdatePicker.js"></script>


</body>
</html>