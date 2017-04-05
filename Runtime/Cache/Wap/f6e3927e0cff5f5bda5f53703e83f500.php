<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <title>邀请好友</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="wap-font-scale" content="no">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/invite.css"> 
</head>
<body class="body-bg2">
    <div class="wrapper">
    <section class="page-view page-earn" style="padding-bottom: 0;">
        <div class="content-box content-box2" style="padding-bottom: 45px;">
            <div class="container">
                <div class="earn-tab text-center">
                    <span>已邀请好友</span>
                </div>
                <p class="p1" ></p>
                <div class="p2" style="font-size: 15px;">
                   <span  id="metion">一级：<?php echo ((isset($list['num'][0]['count']) && ($list['num'][0]['count'] !== ""))?($list['num'][0]['count']):0); ?>人；</span> 
                   <span  id="metion">二级：<?php echo ((isset($list['num'][1]['count']) && ($list['num'][1]['count'] !== ""))?($list['num'][1]['count']):0); ?>人；</span>
                   <span  id="metion">三级：<?php echo ((isset($list['num'][2]['count']) && ($list['num'][2]['count'] !== ""))?($list['num'][2]['count']):0); ?>人</span>
                </div>
              <p class="p3" style="opacity: 0.6"></p>
                <p class="tip-p tip-p2 text-right"><!--<span class="text-left" id="metion">有效投资<i class="tip-i"></i></span>--></p>
            </div>
        </div>
        <div class="earn-wraper invite-wraper">
            <div class="container">
                <ul style="padding-top: 20px;">
                    <?php if(is_array($list1)): $i = 0; $__LIST__ = $list1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="box-flex">
                        <div class="col-flex">
                            <span class="avatar">
                                <img src="<?php echo ($vo['headimgurl']); ?>" onerror="javascript:this.src='<?php echo RES;?>/jinrong/Mobile/Public/images/headimg.jpg';"> 
                            </span>
                            <div class="earn-l">
                                <p class="earn-money earn-money02"><span class="name"><?php echo ($vo['wechat_name']); ?></span>
                                   <?php echo substr($vo['phone'],0,3).'****'.substr($vo['phone'],7,4);?>  一级</p>
                                <p><?php echo date('Y-m-d H:i:s',$vo['create_time']);?> 注册</p> 
                            </div>
                        </div>
                                                    <span class="invite-r">
                                                                                                      <!--   <a href="javascript:void(0);" class="get-btn3">已提醒</a> -->
                                                                                                        </span> 
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    
                    <?php if(is_array($list2)): $i = 0; $__LIST__ = $list2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="box-flex">
                        <div class="col-flex">
                            <span class="avatar">
                                <img src="<?php echo ($vo['headimgurl']); ?>" onerror="javascript:this.src='<?php echo RES;?>/jinrong/Mobile/Public/images/headimg.jpg';"> 
                            </span>
                            <div class="earn-l">
                                <p class="earn-money earn-money02"><span class="name"><?php echo ($vo['wechat_name']); ?></span>
                                   <?php echo substr($vo['phone'],0,3).'****'.substr($vo['phone'],7,4);?>  二级</p>
                                <p><?php echo date('Y-m-d H:i:s',$vo['create_time']);?> 注册</p> 
                            </div>
                        </div>
                                                    <span class="invite-r">
                                                                                                      <!--   <a href="javascript:void(0);" class="get-btn3">已提醒</a> -->
                                                                                                        </span> 
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    
                    <?php if(is_array($list3)): $i = 0; $__LIST__ = $list3;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="box-flex">
                        <div class="col-flex">
                            <span class="avatar">
                                <img src="<?php echo ($vo['headimgurl']); ?>" onerror="javascript:this.src='<?php echo RES;?>/jinrong/Mobile/Public/images/headimg.jpg';"> 
                            </span>
                            <div class="earn-l">
                                <p class="earn-money earn-money02"><span class="name"><?php echo ($vo['wechat_name']); ?></span>
                                   <?php echo substr($vo['phone'],0,3).'****'.substr($vo['phone'],7,4);?>  三级</p>
                                <p><?php echo date('Y-m-d H:i:s',$vo['create_time']);?> 注册</p> 
                            </div>
                        </div>
                                                    <span class="invite-r">
                                                                                                      <!--   <a href="javascript:void(0);" class="get-btn3">已提醒</a> -->
                                                                                                        </span> 
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                                                       </ul>
            </div>
        </div>
    </section>
</div>
<script type='text/javascript'>var ROOT = "";</script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/common.js"></script>
<script>
function send_code(uid,invite,obj) {
        $.post(ROOT+"/mobile.php/public/send_privilege_code.html?t="+Date.parse(new Date()),{uid:uid,invite_uid:invite},function(rs){
            if(rs.status==1){
                        $(obj).attr("class", "get-btn3"); 
                        $(obj).text("已提醒"); 
                        alert('提醒成功，我们已经向您的好友发送提醒短信。');
                        //showLoading('提醒成功，我们已经向您的好友发送提醒短信。', 2000);
                        }else{
                        alert(rs.message);
                    }
                },"json")
}
function help() {
        alert('好友有过充值记录，均为已充值好友。');
}
</script>
</body>
</html>