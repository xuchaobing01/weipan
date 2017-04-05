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



		<li class="active"><a href="javascript:void(0)">会员管理</a></li>



		 <li><a href="<?php echo U('Channel/adduser');?>">添加会员</a></li>



	</ul>



	<div class="page-header wm-page-header">



	 	<form method="get" action="/User/Channel/custom.html" class="form-inline">



		



		<label>姓名</label>



		<input name="wechat_name" type="text" value="<?php echo ($_GET['wechat_name']); ?>" class="form-control" />



		<label>手机号</label>



		<input name="phone" type="text" value="<?php echo ($_GET['phone']); ?>" class="form-control" />

			<label>时间</label>
			<input name="starttime" type="text" value="<?php echo ($_GET['starttime']); ?>" class="form-control datetime-picker" />
			~
			<input name="endtime" type="text" value="<?php echo ($_GET['endtime']); ?>" class="form-control datetime-picker" />

		<button class="btn btn-primary" type="submit">



			<span class="glyphicon glyphicon-search"></span>



			查询



		</button>



		</form> 



	</div>

<script>
			function sub(id,star=0){
				$("#star").get(0).selectedIndex = star;
				$("input[name='orderid']").val(id);
			}
		</script>


	<TABLE class="table wm-table-bordered table-striped table-hover">



		<THEAD>



		<TR>



			<TH>ID</TH>



			



			<TH>姓名</TH>
			<TH>电话</TH>
			<!-- 
			<TH>一级人数</TH>
			<TH>二级人数</TH>
			<TH>三级人数</TH> -->

			<TH>注册时间</TH>



			<TH>余额</TH>
			<TH>总充值</TH>


			<TH>虚拟金额(元)</TH> 



			<TH>冻结金额</TH>



			<TH>状态</TH>



			<TH>操作</TH>



			



		</TR>



		</THEAD>



		<TBODY>



		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><TR>



			<TD><?php echo ($list["id"]); ?></TD>



			<TD><?php echo ($list["wechat_name"]); ?></TD>



			<TD><?php echo ($list["phone"]); ?></TD>
			
		<!-- 	<TD><?php if($list['num'][0]['count'] > 0): ?><a href="<?php echo U('Member/mychild',['lever'=>0,'pid'=>$list['id']]);?>"> <span style="color:red;"> <?php echo ((isset($list['num'][0]['count']) && ($list['num'][0]['count'] !== ""))?($list['num'][0]['count']):0); ?></span></a><?php else: ?> 0<?php endif; ?></TD>
			<TD><?php if($list['num'][1]['count'] > 0): ?><a href="<?php echo U('Member/mychild',['lever'=>1,'pid'=>$list['id']]);?>"> <span style="color:#0012ff;"> <?php echo ((isset($list['num'][1]['count']) && ($list['num'][1]['count'] !== ""))?($list['num'][1]['count']):0); ?></span></a><?php else: ?> 0<?php endif; ?></TD>
			<TD><?php if($list['num'][2]['count'] > 0): ?><a href="<?php echo U('Member/mychild',['lever'=>2,'pid'=>$list['id']]);?>"> <span style="color:green;"> <?php echo ((isset($list['num'][2]['count']) && ($list['num'][2]['count'] !== ""))?($list['num'][2]['count']):0); ?></span></a><?php else: ?> 0<?php endif; ?></TD> -->



			<TD>	<?php echo (date('Y-m-d H:i',$list["create_time"])); ?></TD>



			<TD><?php echo ($list["money"]); ?></TD>
			<TD><?php echo ((isset($list["paysum"]) && ($list["paysum"] !== ""))?($list["paysum"]):0); ?></TD>


			<TD><?php echo ($list["coin"]); ?></TD>



			<TD><?php echo ($list["dongjie"]); ?></td>



			<TD>
				<?php if($list["star"] == 1): ?><a href="###" class="btn btn-xs btn-primary">团队领导</a><?php endif; ?>

				<?php if($list["status"] == 1): ?><span style="color:red">冻结</span><?php else: ?>正常<?php endif; ?></td>
			<TD>
				<a href="<?php echo U('Channel/changeAgent?id='.$list['id'],array('agent_id'=>$list['shareid']));?>" class="btn btn-xs btn-danger">更改经纪人</a>
			</TD>
		</form>



		</TR><?php endforeach; endif; else: echo "" ;endif; ?>



	  </TBODY>



	</TABLE>



	<div><?php echo ($page); ?></div>



</div>
<div class="modal fade"  id="modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title">更改用户身份</h4>
					</div>
					<div class="modal-body">
						<form id="form" class="form" method="post" action="<?php echo U('change');?>">
							<input type="hidden" name="orderid"  />
							
							<div class="form-group">
								<label for="score">会员等级</label>
								<select  name="star" id='star' class="form-control">
									<option  value="0" >免费会员</option>
									<option  value="1">团队领导人</option>
								</select>

							</div>
							<button type="submit" class="btn btn-primary">确定</button>
						</form>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->




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

	<script type="text/javascript" src="<?php echo STATICS;?>/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
	<script>
        $(function(){
            $(".datetime-picker").datetimepicker({format: 'yyyy-mm-dd',language:'zh-CN',minView:2,autoclose:true});
        });

	</script>


</body>
</html>