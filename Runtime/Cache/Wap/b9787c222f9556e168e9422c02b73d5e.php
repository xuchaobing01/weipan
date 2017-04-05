<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh_CN">

<head>

    <meta charset="UTF-8">

    <title><?php echo ($res["title"]); ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />

    <meta name="viewport" content="initial-scale=1, user-scalable=no" />

    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <meta name="format-detection" content="telephone=no" />

    <link href="<?php echo RES;?>/css/ktv/style.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="<?php echo RES;?>/css/yl/news.css" />

	<script src="<?php echo STATICS;?>/js/zepto.min.js" type="text/javascript"></script>

</head>

<body>

<div class="content">

	<div class="marginb">

        <!--<h3><?php echo ($res["title"]); ?></h3>-->

        <h5 style="color:#aaa;font-size:12px;"><?php echo ($res["author"]); ?>&nbsp; <?php echo (date("Y-m-d",$res["updatetime"])); ?></h5>

    </div>

    <div class="page-bizinfo">

    <a id="biz-link" class="btn" href="###"  data-transition="slide" >

        <div class="arrow">

            <div class="icons arrow-r"></div>

        </div>

        <div class="logo">

            <div class="circle"></div>

            <img id="img" src="<?php echo ($tpl["headerpic"]); ?>">

        </div>

        <div id="nickname"><?php echo ($tpl["wxname"]); ?></div>

        <div id="weixinid">微信号:<?php echo ($tpl["weixin"]); ?></div>

    </a>

    </div>

    <?php if(($res["showpic"]) == "1"): ?><div class="showpic">

            <img src="<?php echo ($res["pic"]); ?>" />

        </div><?php endif; ?>

    <?php echo htmlspecialchars_decode($res['info']) ?>

    <?php echo htmlspecialchars_decode($res['content']) ?>

</div>

<div style="display:none"><?php echo htmlspecialchars_decode($tpl['tongji']) ?></div>


<script type="text/javascript">
function displayit(n){
	$('.menu_font').not('#menu_list'+n).hide();
	if($('#menu_list'+n)){
		$('#menu_list'+n).toggle();
	}
}

function closeall(){
	var count = document.getElementById("top_menu").getElementsByTagName("ul").length;
	for(i=0;i<count;i++){
		document.getElementById("top_menu").getElementsByTagName("ul").item(i).style.display='none';
	}
	document.getElementById("plug-wrap").style.display='none';
}
</script>
<style type="text/css">
.top_menu label{
	display:inline;
}
.plug-menu:focus{
	outline:0 none !important;
}
</style>

<div class="copyright">

<?php if($iscopyright == 1): echo ($homeInfo["copyright"]); ?>

	<?php else: ?>

	<?php echo ($siteCopyright); endif; ?>

</div>

<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script type="text/javascript">

	wx.config({

		debug: false,

		appId:'<?php  echo $shareConfig['appid'];?>', // 必填，公众号的唯一标识

		timestamp:<?php  echo $shareConfig['timestamp'];?>, // 必填，生成签名的时间戳

		nonceStr: '<?php  echo $shareConfig['noncestr'];?>', // 必填，生成签名的随机串

		signature: '<?php  echo $shareConfig['signature'];?>',// 必填，签名

		jsApiList: [ 'checkJsApi',

			'onMenuShareTimeline',

			'onMenuShareAppMessage',

			'onMenuShareQQ',

			'onMenuShareWeibo',

			'hideMenuItems',

			'showMenuItems',

			'hideAllNonBaseMenuItem',

			'showAllNonBaseMenuItem']

	});

	var wxData = {

        "imgUrl" : '<?php echo $res['pic'];?>',

        "link" : '<?php echo C('WAP_DOMAIN').U('Index/article',['token'=>$token,'id'=>$_GET['id']]); ?>',

        "desc" : '<?php echo $res['text'].$res['summary'];?>',

        "title" : "<?php echo $res['title'];?>"

    };

	wx.ready(function(){

		//分享到朋友圈

		wx.onMenuShareTimeline({

			title: wxData.title, // 分享标题

			link: wxData.link, // 分享链接

			imgUrl: wxData.imgUrl, // 分享图标

			success: function () {

				alert('分享成功！');

			},

			cancel: function () { 

				// 用户取消分享后执行的回调函数

			}

		});

		//分享给朋友

		wx.onMenuShareAppMessage({

			title: wxData.title, // 分享标题

			desc: wxData.desc, // 分享描述

			link: wxData.link, // 分享链接

			imgUrl: wxData.imgUrl, // 分享图标

			type: '', // 分享类型,music、video或link，不填默认为link

			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空

			success: function () { 

				alert('分享成功！');

			},

			cancel: function () { 

				// 用户取消分享后执行的回调函数

			}

		});

		wx.onMenuShareQQ({

			title: wxData.title, // 分享标题

			desc: wxData.desc, // 分享描述

			link: wxData.link, // 分享链接

			imgUrl: wxData.imgUrl, // 分享图标

			success: function () { 

			   alert('分享成功！');

			},

			cancel: function () { 

			   // 用户取消分享后执行的回调函数

			}

		});

	});

	wx.error(function(res){

		

	});

	function toggleClass(el,style){

		$(el).toggleClass(style);

	}

</script>

</body>

</html>