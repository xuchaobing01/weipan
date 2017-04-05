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
	td{
		max-width: 700px;
		text-overflow: ellipsis;
		overflow: hidden;
	}
</style>

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
<?php if(($wx['wxtype'] == '订阅号') AND ($wx['is_certified'] == 0)): ?><div class="alert alert-info">
	<h3><i class="fa fa-exclamation-triangle fa-lg text-danger"></i>&nbsp;您的公众号不支持生成自定义菜单！</h3>
	请使用服务号，或认证的订阅号！
</div>
<?php else: ?>
<div class="alert alert-info">
	<i class="fa fa-exclamation-triangle fa-lg"></i>&nbsp;一级菜单最多只能开启3个，二级子菜单最多开启5个!
</div>
<!--<form class="form-inline" role="form" action="" method="post">
	<div class="form-group">
		<label class="control-label" for="appid">AppId:</label>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" value="<?php echo ($diymen["appid"]); ?>" id="appid" name="appid" placeholder="APPID" />
	</div>
	<div class="form-group">
		<label class="control-label" for="appsecret">AppSecret:</label>
	</div>
	<div class="form-group">
		<input type="text" class="form-control" value="<?php echo ($diymen["appsecret"]); ?>" id="appsecret" name="appsecret" placeholder="AppSecret" />
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary" >保存</button>
	</div>
</form>-->
<div class="page-header">
	<button class="btn btn-success btn-edit" id="iframe1" url="<?php echo U('Diymen/class_edit');?>" title="添加主菜单"><span class="glyphicon glyphicon-plus"></span>&nbsp;添加菜单</button>
	<button type="button" onclick="spark.confirm_jump('自定义菜单最多勾选3个，每个菜单的子菜单最多5个，请确认!', '<?php echo U('Diymen/class_send');?>');" class="btn btn-primary">生成自定义菜单</button>
	<button type="button" onclick="spark.confirm_jump('是否确定删除微信会话界面的自定义菜单？', '<?php echo U('Diymen/menu_remove');?>');" class="btn btn-danger">删除自定义菜单</button>
</div>
<table class="table wm-table-bordered table-striped table-hover"> 
		<thead>
			<tr>
				<th>显示顺序</th>
				<th>主菜单名称</th>
				<th>关联关键词</th>
				<th>外链URL</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
		<?php if(is_array($class)): $i = 0; $__LIST__ = $class;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class): $mod = ($i % 2 );++$i;?><tr class="info">
			<td>
				<span><?php echo ($class["sort"]); ?></span>
			</td>
			<td>
			  <div>
				<span><?php echo ($class["title"]); ?></span>
			  </div>
			</td>
			<td><span><?php echo ($class["keyword"]); ?></span></td>
			<td>
			<span><?php if($class['url'] == false): ?>无链接地址<?php else: echo ($class["url"]); endif; ?></span>
			</td>
			<td>
				<a class="btn btn-default btn-xs btn-edit" url="<?php echo U('Diymen/class_edit',array('id'=>$class['id']));?>">修改</a>
				<a class="btn btn-xs btn-danger" href="javascript:spark.confirm_jump('您确定要删除吗?', '<?php echo U('Diymen/class_del',array('id'=>$class['id']));?>');">删除</a>
			</td>
		</tr>
		<?php if(is_array($class['class'])): $i = 0; $__LIST__ = $class['class'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class1): $mod = ($i % 2 );++$i;?><tr>
			<td>
				<span><?php echo ($class1["sort"]); ?></span>
			</td>
			<td>
				<div class="board">
					<span>┣&nbsp;&nbsp;<?php echo ($class1["title"]); ?></span>
				</div>
			</td>
			<td>
				<span><?php echo ($class1["keyword"]); ?></span>
			</td>
			<td>
				<span>
					<?php if($class1['url'] == false): ?>无链接地址<?php else: echo ($class1["url"]); endif; ?>
				</span>
			</td>
			<td>
				<a class="btn btn-default btn-xs btn-edit" url="<?php echo U('Diymen/class_edit',array('id'=>$class1['id']));?>" title="修改主菜单">修改</a>
				<a class="btn btn-xs btn-danger" href="javascript:spark.confirm_jump('您确定要删除吗?', '<?php echo U('Diymen/class_del',array('id'=>$class1['id']));?>');">删除</a>
			</td>
		</tr><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
</div>
<!-- Modal -->
<div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">新建菜单项</h4>
			</div>
			<div class="modal-body">
				<iframe id="modalFrame" frameBorder="0" src="<?php echo U('Diymen/class_edit');?>" style="width:100%;height:320px;border:0;" ></iframe>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				<button id="btnSave" type="button" class="btn btn-primary">保存</button>
			</div>
		</div>
	</div><?php endif; ?>
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
	
<script type="text/javascript" src="<?php echo STATICS;?>/artDialog/jquery.artDialog.js?skin=default"></script>
<script type="text/javascript" src="<?php echo STATICS;?>/artDialog/plugins/iframeTools.js"></script>
<script type="text/javascript" src="<?php echo RES;?>/js/spark.util.js"></script>
<script type="text/javascript">
$('.btn-edit').click(function(){
	var src = $(this).attr('url');
	var timestamp = (new Date()).getTime();
	if(src.indexOf('?')>0){
		src+='&timestamp='+timestamp;
	}
	else{
		src+='?timestamp='+timestamp;
	}
	$('#modalFrame').attr('src',src);
	$('#menuModal').modal({'backdrop': 'static','keyboard': false});
});

$('#btnSave').on('click', function (e) {
	var ret = $('#modalFrame')[0].contentWindow.submit('callback');
})

/**
 * @method callback 子窗口数据提交成功时调用
 */
function callback(id){
	$('#menuModal').modal('hide');
	location.reload();
}
</script>

</body>
</html>