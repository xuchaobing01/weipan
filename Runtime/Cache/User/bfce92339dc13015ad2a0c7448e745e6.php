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
	
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	
<style>
	#select{
		margin-left: 50px;
		display: none;
	}
	.btn-primary.btn-shadow {
	-webkit-box-shadow: inset 0 -4px 0 #2a6496;
	box-shadow: inset 0 -4px 0 #2a6496;
	border: 0;
	color: #fff;
	}
	.btn-upload input[type="file"]{
	position: absolute;
	opacity: 0;
	left: 0;
	top: 0;
	right: 0;
	bottom: 0;
	width: 100%;
	margin:0;
	padding:0;
	cursor: pointer;
}
</style>
<style>
	html,body{
		height: 100%;
		width: 100%;
		margin: 0;
		overflow: hidden;
	}
	.nav{
		height: 10%;
	}
	div.tab-content{
		height: 100%;
	}
	#history{
		margin: 0px;;
		height: 85%;
		overflow-y: scroll;
		overflow-x: hidden;
	}
	
</style>
<style type="text/css">
	ul{
		list-style:none;
		padding:0;
	}
	#history li,#membercard li{
		display:inline-block;
		margin-right: 8px;
		margin-bottom: 10px;
		background-color: #ace;
	}
	#history li img,#membercard li img{
		cursor:pointer;
		opacity:0.8;
        max-height: 100px;
	}
	#history img.selected,#membercard img.selected{
		border:solid 3px green;
		opacity:1;
	}
    #history li{
        position: relative;
    }
    .cBtn {
        width: 25px;
        height: 25px;
        background: url(<?php echo RES;?>/images/sprBg.png) no-repeat;
        background-size: 400px auto;
        -webkit-background-size: 400px auto;
        position: absolute;
        right: -10px;
        top: -10px;
    }
    .cBtn:hover{
        display: inline-block;
    }
</style>

	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery-1.8.2.min.js"></script>
</head>
<body>
	<!-- 页面主体部分-->
	
<div id="selectAssetDialog" class="tabbable-custom" style="padding-top: 10px;">
	<ul class="nav nav-tabs wm-tabs">
		<li class="active"><a href="#upload" data-toggle="tab">本地上传</a></li>
		<?php if($_GET['type'] == 'membercard'): ?><li><a href="#membercard" data-toggle="tab">系统会员卡</a></li><?php endif; ?>
		<li><a href="#network" data-toggle="tab">网络图片</a></li>
		<li><a href="#history" data-toggle="tab">历史图片</a></li>
        <li><button type="button" id="select" class="btn btn-success" >选择图片</button>&nbsp;</li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content" style="height: 300px;">
		<div class="tab-pane active fade in" id="upload">
			<span class="btn-upload btn btn-primary btn-lg btn-shadow">
			<input class="upload" type="file" name="file" id="fileUpload_BTN" />
			<i class="fa fa-image"></i>&nbsp;选择图片
			</span>
		</div>
		<?php if($_GET['type'] == 'membercard'): ?><div class="tab-pane fade in" id="membercard">
			<div class="row">
				<div class="col-md-12">
					<div class="page-header" style="margin-top:20px;">
						<button type="button" id="selectCard" class="btn btn-success">选择</button>&nbsp;
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<ul>
						<li><img src="<?php echo RES;?>/images/card/card_bg01.png" width="150px" height='89px'/></li>
						<li><img src="<?php echo RES;?>/images/card/card_bg02.png" width="150px" height='89px'/></li>
						<li><img src="<?php echo RES;?>/images/card/card_bg03.png" width="150px" height='89px'/></li>
						<li><img src="<?php echo RES;?>/images/card/card_bg04.png" width="150px" height='89px'/></li>
						<li><img src="<?php echo RES;?>/images/card/card_bg05.png" width="150px" height='89px'/></li>
						<li><img src="<?php echo RES;?>/images/card/card_bg06.png" width="150px" height='89px'/></li>
						<li><img src="<?php echo RES;?>/images/card/card_bg07.png" width="150px" height='89px'/></li>
					</ul>
				</div>
			</div>
		</div><?php endif; ?>
		<div class="tab-pane fade in" id="network">
			<div class="input-group">
				<input type="text" id="imgUrl" class="form-control" />
				<span class="input-group-btn">
					<button class="btn btn-primary" id="imgUrlCommit" type="button">确定</button>
				</span>
			</div><!-- /input-group -->
		</div>
		<div class="tab-pane fade" id="history">
			<div class="row">
				<div class="col-md-12">
					<ul id="imgList" load-state="0">
						
					</ul>
					<a href="javascript::void(0)" style="margin-left: 30px;" onclick="getMoreImg(-1)">上一页</a>
					<a href="javascript::void(0)" style="margin-left: 30px;" onclick="getMoreImg(1)">下一页</a>
				</div>
			</div>
		</div>
	</div>
</div>

	<script type="text/javascript" src="<?php echo STATICS;?>/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo STATICS;?>/js/jquery.ajaxfileupload.js"></script>
	
<script type="text/javascript">
var type = 1;
var page=-1;
function getMoreImg(f){
	if(f<0){
		if(page<=0){
			return;
		}
		page--;
	}
	else{page++;}
	$.ajax({
		url: "/user/index/imgs.html",
		data:{page:page},
		type:'get',
		dataType:'json',
		success:function(resp){
			$('#imgList').empty();
			$.each(resp,function(i,img){
				var $li = $('<li><img src="'+img.url+'" width="120px" /><a href="javascript:;" class="cBtn spr db " id="'+img.id+'"> </a></li>');
				$li.find('img').click(function(){
					$(this).toggleClass("selected");
					$("#imgList img.selected").not(this).removeClass("selected");
				});
				$li.find('img').dblclick(function(e){
					e.stopPropagation();
					$(this).click();
					$("#select").click();
				});
				$li.find('a').click(function(){
					var img_rm=$(this).parent();
					var img_id=$(this).attr("id");
					$.ajax({
						url:"/user/index/imgs_delete.html?id="+img_id,
						dataTpe:"json",
						success:function(e){
							$(img_rm).remove();
						}
					});

				});

				$('#imgList').append($li);
			});
			$('#imgList').attr('load-state',1);
		}
	});
}

function activeImg(){
	$(this).toggleClass("selected");
	$("#imgList img.selected").not(this).removeClass("selected");
}


function set(url){
	var callback = parent.BootstrapDialog.uploadCallback;
	if(typeof parent.window[callback] == 'function'){
		parent.window[callback](url);
		BootstrapDialog.uploadCallback = '';
	}
	else{
		var $target = $("#"+parent.BootstrapDialog.uploadTarget,parent.window.document);
		$target.val(url);
		$target.change();
		var $targetHolder=$('#'+parent.BootstrapDialog.uploadTarget+'Holder',parent.window.document);
		if($targetHolder.length!=0){
			$targetHolder.attr('src', url);
		}
	}
	setTimeout("parent.window.UPLOAD_DIALOG.close()", 1000);
}
//初始化
$("#fileUpload_BTN").AjaxFileUpload({
	action: "/User/Qiniu/upload.html",
	onSubmit: function(filename) {
		return true;
	},
	onComplete: function(filename, resp) {
		if(resp.error !=0){
			alert(resp.message);
		}
		else{
			set(resp.url);
		}
	}
});
//当"历史图片"tab 被激活时加载图片
$('#selectAssetDialog a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	if($(e.target).attr('href') == '#history' && $('#imgList').attr('load-state')=='0'){
		getMoreImg(1);
	}
	if($(e.target).attr('href') == '#history'){
		$("#select").show();
	}else{
		$("#select").hide();
	}
});
$("#select").click(function(){
	set($("#imgList img.selected").attr("src"));
});
$('#imgUrlCommit').click(function(){
	var url = $('#imgUrl').val();
	if(url){
		set(url);
	}
});

$('#membercard img').click(function(){
	$(this).toggleClass("selected");
	$("#membercard img.selected").not(this).removeClass("selected");
});

$("#selectCard").click(function(){
	set($("#membercard img.selected").attr("src"));
});
</script>

</body>
</html>