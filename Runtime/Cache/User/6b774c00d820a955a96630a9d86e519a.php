<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<meta charset="utf-8" />
	<meta name="keywords" content="掘金六十秒 免费微信营销  微信营销平台 " />
	<meta name="description" content="免费微信营销平台 合肥微信营销平台 "/>
	<meta name="baidu-site-verification" content="pKfXuVuMa3" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
<title> <?php echo C('site_title');?> <?php echo C('site_name');?></title>

	
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/bootstrap/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo STATICS;?>/font-awesome/css/font-awesome.min.css" />
	<link type="text/css" rel="stylesheet" href="/Public/Admin/me/css/bootstrap-me.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/user.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo RES;?>/css/bs-context.css"/>
	
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="<?php echo STATICS;?>/js/html5shiv.js"></script>
	<script src="<?php echo STATICS;?>/js/respond.min.js"></script>
	<![endif]-->
	<style type="text/css">
		.navbar-nav>li>a{
			font-weight: bold;
			color: white;
		}
		
		.navbar-nav>li>a:hover{
			background-color: black;
		}

		.navbar-nav>li.active>a{
			color: white;
		}

		.wx-subnav{
			padding: 0 4px;
		}
		.wx-subnav a{
			color: gray;
		}
		.wx-subnav a:hover{
			color: rgb(66, 139, 202);
		}
		.wx-subnav a.active{
			color: rgb(66, 139, 202);
			border-radius: 0;
			border-left: 2px solid rgb(66, 139, 202);
		}
	</style>
	


	<script type="text/javascript" src="<?php echo C('CDN_JQUERY');?>"></script>
	<!--百度统计异步代码 -->
	<script>
	var _hmt = _hmt || [];
	(function() {
	  var hm = document.createElement("script");
	  hm.src = "//hm.baidu.com/hm.js?b48a0ed48d652448701edf73042ae5ec";
	  var s = document.getElementsByTagName("script")[0]; 
	  s.parentNode.insertBefore(hm, s);
	})();
	</script>
</head>
<body class="theme-bright breakpoint-1200" style="padding-top: 58px;">
    <header class="header navbar navbar-fixed-top" role="banner">
      <div class="container-fluid">
        <ul class="nav navbar-nav">
          <li class="nav-toggle">
            <a href="javascript:void(0);" title="">
              <i class="icon-reorder">
              </i>
            </a>
          </li>
        </ul>
        <a class="navbar-brand" href="#">
			
			<strong style="    margin-left: 20px;">港云外汇</strong>
        </a>
        <ul class="nav navbar-nav navbar-left hidden-xs hidden-sm">
			<?php if(is_array($__MENU__["root"])): $i = 0; $__LIST__ = $__MENU__["root"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(strpos($vo['url'],'vshop')){ ?>
				<li class><a target="_blank" href="http://w.weimarket.cn/<?php echo ($vo['url']); ?>?uid=<?php echo session('uid');?>"><?php echo ($vo["title"]); ?></a></li>
			<?php }else{ ?>
				<li class="<?php if($vo['active']) echo 'active';?>"><a href="<?php echo U($vo['url']);?>"><?php echo ($vo["title"]); ?></a></li>
			<?php } endforeach; endif; else: echo "" ;endif; ?>

        </ul>
        <ul class="nav navbar-nav navbar-right">
          <!--<li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope">
              </i>
              <span class="badge">
                5
              </span>
            </a>
            <ul class="dropdown-menu extended notification">
              <li class="title">
                <p>
                  你有5条通知信息。
                </p>
              </li>
              <li>
                <a href="javascript:void(0);">
                  <span class="label label-success">
                    <i class="icon-plus">
                    </i>
                  </span>
                  <span class="message">
                    新用户注册.
                  </span>
                  <span class="time">
                    1分钟之前
                  </span>
                </a>
              </li>
              <li>
                <a href="javascript:void(0);">
                  <span class="label label-success">
                    <i class="icon-plus">
                    </i>
                  </span>
                  <span class="message">
                    New user registration.
                  </span>
                  <span class="time">
                    10 mins
                  </span>
                </a>
              </li>
              <li class="footer">
                <a href="javascript:void(0);">
                  View all notifications
                </a>
              </li>
            </ul>
          </li>-->
			<li>
			<a  href="#" data-toggle="modal" data-target="#linkModal">
				<i class="fa fa-question-circle"></i>&nbsp;帮助
			</a>
			</li>
          <li>
			<a class="dropdown-toggle username" data-toggle="dropdown" href="javascript:void(0)">
			<?php if($_SESSION['account_name']): echo (session('account_name')); ?>(<?php echo ($_SESSION['uname']); ?>)
			<?php else: ?>
			<?php echo (session('uname')); endif; ?>
			<i class="fa fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu" role="menu">
			<li><a href="javascript:void(0)" class="ajax-url" src="<?php echo U('Index/passedit');?>">修改密码</a></li>
			</ul>
			</li>
			<li><a href="<?php echo U('User/logout');?>"><i class="text-danger fa fa-lg fa-power-off"></i></a></li>
        </ul>
      </div>
    </header>
    <div id="container">
      <div id="sidebar" class="sidebar-fixed">
        <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;"><div id="sidebar-content" style=" width: auto; overflow-x: hidden;overflow-y:visible;height: 100%;">
			<ul id="nav">
				<?php if(is_array($__MENU__['current']['child'])): $i = 0; $__LIST__ = $__MENU__['current']['child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i; if($key == ''): if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
							<a href="javascript:void(0)" class="ajax-url"  src="<?php if(strpos($menu['url'],'/')===0){echo $menu['url'];}else{echo U($menu['url']);}?>"><?php echo ($menu["title"]); ?></a>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php else: ?>
					<li>
						<a class="menu-group" href="javascript:void(0)">
							<span><?php echo ($key); ?></span>
							<span class="fa fa-chevron-right wx-sidenav-icon"></span>
						</a>
						<ul class="sub-menu">
							<?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
								<a href="javascript:void(0)" class="ajax-url" src="<?php if(strpos($menu['url'],'/')===0){echo $menu['url'];}else{echo U($menu['url']);}?>"><i class="fa fa-chevron-right"></i>&nbsp;<?php echo ($menu["title"]); ?></a>
							</li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            
          </ul>
        <div class="fill-nav-space"></div></div><div class="slimScrollBar" style="width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; z-index: 99; right: 1px; height: 147.687804878049px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div>
		</div>
      </div>
      <div id="content">
        
	<iframe  frameBorder="0" class="contentFrame" src="" ></iframe>

      </div>
    </div>
	<!-- 帮助页面 -->
	<div class="modal fade" id="linkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg">
		<div class="modal-content">
			<iframe id="linkFrame" state="0" frameborder="0" style="border:0;width:100%;height:360px;" src=""></iframe>
		</div>
	  </div>
	</div>
</body>
	<script type="text/javascript" src="<?php echo STATICS;?>/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$("#nav>li>.menu-group").click(function(e){
			var $li = $(this).parent();
			$li.toggleClass('open');
			
			if($li.hasClass('open')){ //打开菜单
				$li.find('.sub-menu').slideDown(500);
				$li.addClass('current');
				$(this).find('.wx-sidenav-icon').removeClass('fa-chevron-right').addClass('fa-chevron-down');
			}
			else{
				$li.find('.sub-menu').slideUp(500);
				$li.removeClass('current');
				$(this).find('.wx-sidenav-icon').removeClass('fa-chevron-down').addClass('fa-chevron-right');
			}
		});
		
		$("a.ajax-url").click(function(e){
			$("#content>iframe").attr("src",$(this).attr("src"));
		});
		$(function(){
			$('#sidebar a.ajax-url:first').click();
		})
		$('#linkModal').on('show.bs.modal',function(){
			if($('#linkFrame').attr('state')=='0'){
				$('#linkFrame').attr('src',"/index.php?m=User&c=Help&a=link");
				$('#linkFrame').attr('state',1);
			}
		})
	</script>
	
	
	
</html>