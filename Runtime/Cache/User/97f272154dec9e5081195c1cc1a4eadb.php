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
	

<link rel="stylesheet" href="<?php echo STATICS;?>/kindeditor/themes/default/default.css" />

<link rel="stylesheet" href="<?php echo STATICS;?>/kindeditor/plugins/code/prettify.css" />


	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	

<div class="container-fluid">



	<ul class="nav nav-tabs" role="tablist">

		<li role="presentation" class="active"><a href="#" aria-controls="home">参数设置</a></li>

	</ul>

	<div class="page-header wm-page-header">



	</div>

	<form class="form-horizontal" method="post" >
		<input type="hidden" name="id" value="<?php echo ($set["id"]); ?>" />

		<div class="form-group">

			<label class="col-sm-2">一级返佣(百分比)</label>

			<div class="col-md-6">

				<div class="input-group">

					<input type="text" class="form-control" name="first" value="<?php echo ($set["first"]); ?>"

						   style="width:50px;"/>

												<span class="input-group-btn" >

								<button class="btn btn-default" type="button" style="margin-right: 676px;">%

								</button>

							</span>

				</div>

			</div>

		</div>

		<div class="form-group">

			<label class="col-sm-2">二级返佣(百分比)</label>

			<div class="col-md-6">

				<div class="input-group">

					<input type="text" class="form-control" name="second" value="<?php echo ($set["second"]); ?>"

						   style="width:50px;"/>

												<span class="input-group-btn" >

								<button class="btn btn-default" type="button" style="margin-right: 676px;">%

								</button>

							</span>

				</div>

			</div>

		</div>

		<div class="form-group">

			<label class="col-sm-2">三级返佣(百分比)</label>

			<div class="col-md-6">

				<div class="input-group">

					<input type="text" class="form-control" name="three" value="<?php echo ($set["three"]); ?>"

						   style="width:50px;"/>

												<span class="input-group-btn" >

								<button class="btn btn-default" type="button" style="margin-right: 676px;">%

								</button>

							</span>

				</div>

			</div>

		</div>

		<div class="form-group">
         <label class="col-sm-2">是否开启控制</label>
        <div class="col-md-4">
          <label>
          <input type="radio"  name="status"  <?php if($set['status'] == 1): ?>checked="checked"<?php endif; ?> value="1" />开启

          <input type="radio"  name="status"   <?php if($set['status'] == 0): ?>checked="checked"<?php endif; ?> value="0" />不开启

          </label>
           <span class="help-block">开启状态下，下面配置才会生效 </span>
        </div>
      </div>

		<div class="form-group">

			<label class="col-sm-2">虚拟盘成功率</label>

			<div class="col-md-6">

				<div class="input-group">

					<input type="text" class="form-control" name="suc_xu" value="<?php echo ($set["suc_xu"]); ?>"

						   style="width:50px;"/>

												<span class="input-group-btn" >

								<button class="btn btn-default" type="button" style="margin-right: 676px;">（请填写1-100比率值 100表示百分百成功）

								</button>

							</span>

				</div>

			</div>

		</div>

		<div class="form-group">

			<label class="col-sm-2">实盘成功率</label>

			<div class="col-md-6">

				<div class="input-group">

					<input type="text" class="form-control" name="suc_true" value="<?php echo ($set["suc_true"]); ?>"

						   style="width:50px;"/>

												<span class="input-group-btn" >

								<button class="btn btn-default" type="button" style="margin-right: 676px;">（请填写1-100比率值 100表示百分百成功）

								</button>

							</span>

				</div>

			</div>

		</div>


<div class="form-group">

			<label class="col-sm-2">团队领导人拿点</label>

			<div class="col-md-6">

				<div class="input-group">

					<input type="text" class="form-control" name="lingdao" value="<?php echo ($set["lingdao"]); ?>"

						   style="width:50px;"/>

												<span class="input-group-btn" >

								<button class="btn btn-default" type="button" style="margin-right: 676px;">（%）

								</button>

							</span>

				</div>

			</div>

		</div>


		<div class="form-group">
			<label class="col-sm-2">是否开启邀请码注册</label>
			<div class="col-md-4">
				<label>
					<input type="radio"  name="invite_status"  <?php if($set['invite_status'] == 1): ?>checked="checked"<?php endif; ?> value="1" />开启
					<input type="radio"  name="invite_status"   <?php if($set['invite_status'] == 0): ?>checked="checked"<?php endif; ?> value="0" />不开启

				</label>

			</div>
		</div>

		<div class="form-group">

			<div class="col-md-4 col-sm-offset-2">

				<input type="hidden" name="id" value="<?php echo ($set["id"]); ?>">

				<input type="submit" id="ready"  class="btn btn-primary ashui" value="保存" />

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



<script src="<?php echo STATICS;?>/kindeditor/kindeditor.js" type="text/javascript"></script>

<script src="<?php echo STATICS;?>/kindeditor/lang/zh_CN.js" type="text/javascript"></script>

<script src="<?php echo STATICS;?>/kindeditor/plugins/code/prettify.js" type="text/javascript"></script>

<script src="<?php echo RES;?>/js/date/WdatePicker.js" type="text/javascript"></script>

<script src="<?php echo STATICS;?>/vote/common.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo STATICS;?>/validation/js/jquery.validationEngine-zh_CN.js"></script>

<script type="text/javascript" src="<?php echo STATICS;?>/js/holder.min.js"></script>

<script type="text/javascript" charset="utf-8" src="<?php echo STATICS;?>/ueditor/ueditor.config.js"></script>

<script type="text/javascript" charset="utf-8" src="<?php echo STATICS;?>/ueditor/ueditor.all.min.js"> </script>

<script type="text/javascript" charset="utf-8" src="<?php echo STATICS;?>/ueditor/lang/zh-cn/zh-cn.js"> </script>

<script type="text/javascript">

$(function(){

	$("#editForm").validationEngine("attach",{

		promptPosition:"centerRight",

		scroll:true,

		showOneMessage:true

	});

	var ue = UE.getEditor('UEContent');

});



var editor;

KindEditor.ready(function(K) {

	editor = K.create('#info', {

	resizeType : 1,

	allowPreviewEmoticons : false,

	allowImageUpload : true,

	uploadJson : '/index.php?m=User&c=Qiniu&a=kindEditorUpload',

	items : [

	'source','undo','plainpaste','wordpaste','clearhtml','quickformat','selectall','fullscreen','fontname', 'fontsize','subscript','superscript','indent','outdent','|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline','hr']

	});

});
</script>

<!-- 单图上传 -->

<script type="text/javascript">

	$("[data-toggle='upload-reward-img']>.upload-file").AjaxFileUpload({

		action: "<?php echo U('Qiniu/upload');?>",

		onComplete: function(filename, resp) {

			if(resp.error !=0){

				alert(resp.message);

			}

			else{

				var wrapper = $(sprintf(TPL,resp.url,targetName,resp.url));

				wrapper.appendTo('#'+target);

				wrapper.find('.img-remove').click(function(){

					$(this).parent().remove();

				});

			}

		}

	});



	$(function(){

		$('[data-toggle="upload-reward-img"]').click(function(){

			window.target = $(this).attr('data-target');

			window.targetName = $(this).attr('data-target-name');

		});

		$('.img-remove').click(function(){

			$(this).parent().remove();

		});

		$('.upload-music').click(function(){

			window.targetMusic = $(this).attr('data-target');

		});

	})

	</script>

<!-- 单图上传 end-->


</body>
</html>