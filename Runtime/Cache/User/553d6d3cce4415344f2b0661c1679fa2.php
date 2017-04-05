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
	
<style>
.action {
    background: none repeat scroll 0 0 #FFFFFF;
    box-shadow: 1px 1px 3px #666666;
    width: 359px;
}
td{text-align: -webkit-left;font-size:12px;}
</style>

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div class="container-fluid">
	<form class="form-horizontal" action="" method="post">
	<input type="hidden" value="<?php echo ($show["id"]); ?>" id="id" name="id" /> 
	<div class="form-group">
		<label class="control-label col-xs-3">父级菜单</label>
		<div class="col-xs-6">
			<select name="pid" id="pid" class="form-control">
				<option  value="0">请选择菜单</option>
				<?php if(is_array($class)): $i = 0; $__LIST__ = $class;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$class): $mod = ($i % 2 );++$i;?><option  value="<?php echo ($class["id"]); ?>" <?php if($show['pid'] == $class['id']): ?>selected<?php endif; ?>><?php echo ($class["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3">菜单名称</label>
		<div class="col-xs-6">
			<input class="form-control" id="title" name="title" title="主菜单名称" value="<?php echo ($show["title"]); ?>" type="text" />
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3">关联关键词</label>
		<div class="col-xs-6">
			<input class="form-control" id="keyword" name="keyword" title="关联关键词" value="<?php echo ($show["keyword"]); ?>" type="text" />
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3">外链接url</label>
		<div class="col-xs-6">
			<input class="form-control" name="url" id="url"  title="外链接url" value="<?php echo ($show["url"]); ?>" type="text" />
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3">显示</label>
		<div class="col-xs-6">
			<label class="radio-inline">
			<input type="radio" name="is_show" <?php if($show == '' OR $show['is_show'] == 1): ?>checked="checked"<?php endif; ?> value="1">是
			</label>
			<label class="radio-inline">
			<input type="radio" name="is_show" <?php if($show != '' AND $show['is_show'] == 0): ?>checked="checked"<?php endif; ?> value="0">否
			</label>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-xs-3">排序</label>
		<div class="col-xs-6">
			<input class="form-control" id="sort" name="sort" title="排序" value="<?php echo ((isset($show["sort"]) && ($show["sort"] !== ""))?($show["sort"]):1); ?>" type="text" />
		</div>
	</div>
	<div class="form-group hidden">
		<div class="col-xs-offset-3 col-xs-6">
			<input class="btn btn-primary" type="submit" name="submit" value="提交">
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
	
<script type="text/javascript">
	function submit(callback){
		var data={};
		data['pid']=$('#pid').val();
		data['id']=$('#id').val();
		data['title']=$('#title').val();
		data['keyword']=$('#keyword').val();
		data['url']=$('#url').val();
		data['sort']=$('#sort').val();
		data['is_show']=$('input:radio:checked').val();
		data['__hash__']=$('input[name="__hash__"]').val();
		$.ajax({
			url:"<?php echo U('Diymen/class_edit');?>",
			type:'POST',
			data:data,
			dataType:'json',
			success:function(resp){
				if(resp['status']==0){
					parent[callback](resp.record_id);
				}
			}
		})
	}
</script>

</body>
</html>