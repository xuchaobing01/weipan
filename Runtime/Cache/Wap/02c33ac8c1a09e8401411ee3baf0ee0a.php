<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="email=no">
    <title id="apptitle"></title>
    <!-- CSS ?? -->
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/style.css">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/common.css">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/ucenter.css">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/css_L.css">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/option_common.css">
    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/css/option_index.css">
    <link href="http://at.alicdn.com/t/font_2hn9yxdur6n7b9.css" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/weiyun/css/index.css">
    <!--<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/weiyun/css/layer.css">-->
    <!--<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/weiyun/css/public.css">-->
    <style>
        /*黑色页面样式*/
        body {
            color: #d8d7d7;
        }

        div {
            background: #1f1f1f;
        }

        .loading-wrapper {
            background: none;
        }

        li {
            color: #c1c1c1;
        }

        .account-info {
            background: #1f1f1f;
            border-bottom: 1px solid #000;
        }

        .pop-text {
            background-color: #fffe41;
        }

        .switch-product {
            background: #000;
        }

        .info-box {
            background: #000;
            border: 1px solid #000;
        }

        .info-nav {
            border-bottom: 1px solid #000;
        }

        .buy-choose {
            background: #1b1a1a
        }

        .a-d {
            color: #fff;
        }

        .price-trend li em {
            color: #5ba09b;
        }

        .price-current span {
            color: #ECECEC;
        }

        .price-trend li {
            color: #ececec;
        }

        .swiper-slide {
            background: #f1bc11;
        }

        .swiper-slide-active {
            background: #f1bc11;
        }

        .swiper-slide h3 {
            color: #561f1f;
        }

        .swiper-slide h5 {
            color: #561f1f;
        }

        .swiper-slide h4 {
            color: #000;
        }

        .switch-product ul li a {
            font-size: 1.3rem;
            height: auto;
        }

        .switch-product ul li.sw_active a {
            background: #000;
            color: #fff;
            border-bottom: 2px solid #d09b08;
        }

        .trend-nav li a {
            background: #3d3d3e;
        }

        .trend-nav li a.changed {
            background: #b98705;
        }

        .li-ct-transcation {
            background: rgb(31, 31, 31);
            border-bottom: 1px solid #0a0a0a;
        }

        .li-ct-transcation:hover {
            background: rgb(31, 31, 31);
            border-bottom: 1px solid #0a0a0a;
        }

        .realtimebox .realtimeleft .solid .box .real-left {
            border: 1px solid #0a0a0a;
        }

        .rec-table .rec-th:after, .rec-table .rec-td:after {
            border-bottom: 1px solid #000
        }

        .li-tt-transcation {
            color: #c1c1c1;
        }

        #show_bili {
            overflow: hidden;
        }

        .trade-count {
            margin: 10px 0 5px;
            width: 80%;
            float: left;
        }

        .icon {
            vertical-align: middle;
        }

        .l-tt-transcation {
            border-bottom: 1px solid #000;
            background: #000;
        }

        .rec-table .rec-th {
            background-color: #131313;
            font-size: 11px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .rec-table {
            background-color: #000
        }

        .rec-table .rec-td {
            color: #999;
        }

        .active,.pop-box {
            /*background: #fff;*/
            font-size:14px;
            color:#fff;
            background: #3c3b3b;
        }
        .active h1,.active h1 i, .active p, .active li{
            color:#fff;
        }
        .active h1 i.red,.active h1 i.red span{
            color:#f91800;
        }

        .pwd-btn {
            background: #555;
            font-size: 1.4rem;
        }

        .active h1 i span, .active h1 i b,.active h1 b,.active h1{
            font-size:14px;
            color:#fff;
        }
        .active li{
            color:#eee;
        }
        .realtimebox .realtimeleft .solid .box .real-left {
            background: #0a0a0a;
        }
        .active ul li:after {
            /*border: solid 1px #000;*/
        }
        .active ul li:after,.active ul li.not:after{
            border:1px solid #000!important;
            top:0px;
        }
        .active ul li.slct{
            color:#333;
            background: #b28c26;
            border:none;
        }
        .active ul li{
            height:30px;
        }
        .active ul{
            border-bottom: 1px solid #222;
        }
         .active ul li.slct:before {
             content: "";
             width: 30px;
             height: 22px;
             position: absolute;
             top: 11px;
             bottom: 0px;
             right: -1px;
             background: none;
             background-size: 100% 100%;
         }
        .active ul li.slct:after{
            border:none;
        }
        .M_trad_table {
            width: 100%;
            color: #fff;
            background: #3c3b3b;
            font-size:14px;
        }
        .M_trad_table td,.M_trad_table td.td{
            border:none;
        }
        .M_trad_table td span {
            color: #fff;
        }

        .M_trad_table .td_big {
            font-size: 14px;
        }
        /*页面黑色样式结尾*/
        .order_p {
            text-align: center;
        }

        .sec {
            text-align: center;
        }

        .priceClass {
            height: 51px;
            background: transparent;
            font-size: 12px;
        }

        #klineBottomTimeId span {
            font-size: 12px;
        }

        .priceGreenTipClass {
            background: url(/Public/Wap/jinrong/Mobile/Public/images/green.png) no-repeat;
            height: 21px;
            padding-left: 5px;
            font-size: 12px;
            line-height: 21px;
            width: 50px;
            color: white;
        }

        .priceRedTipClass {
            background: url(/Public/Wap/jinrong/Mobile/Public/images/red.png) no-repeat;
            height: 21px;
            padding-left: 5px;
            font-size: 12px;
            line-height: 21px;
            width: 50px;
            color: white;
        }

        .account-info {
            min-height: 5rem;
        }

        .dice-btn {
            margin: 0 auto;
            display: block;
            height: 14px;
            line-height: 14px;
            margin-bottom: 5px;
            font-size: 14px;
            width: 15%;
            float: right;
            margin-right: 14px;
            padding: 5px 0;
            margin-top: 5px;
        }

        .dice-btn:hover {
            color: white;
        }

        .ac2.active ul li {
            width: 18%;
        }

        #area {
            width: 100%;
            height: 200px;
            background: #fff;
        }

        #area div {
            background: #fff;
        }

        .dice-info {
            float: left;
        }

        .dice-info li {
            display: block;
            width: 100%;
            height: 30px;
            color: #7e040c;
            padding-left: 20px;
        }

        .profit_box {
            width: 100%;
            height: 10%;
            overflow: hidden;
            position: fixed;
            right: 0;
            bottom: 10%;
            z-index: 999;
        }

        .profit {
            width: 100%;
            position: absolute;
            bottom: -10rem;
        }

        /*.inner-profit{width: 90%;height:8.125rem;margin: 5% auto;overflow: hidden;border-radius: 90px 90px 90px 90px;background: rgba(0,0,0,.19);color: #fff;border: 1px solid #eee;}*/
        .inner-profit {
            width: 90%;
            height: 4.125rem;
            margin: 4% auto;
            overflow: hidden;
            border-radius: 90px 90px 90px 90px;
            background-image: url(/Public/Wap/jinrong/Mobile/Public/images/profit_bg.png);
            background-repeat: no-repeat;
            background-size: 100% 100%;
            color: #fff;
        }

        .profit-l {
            float: left;
            width: 55%;
            height: 100%;
            font-size: 1.8rem;
            padding-left: 1rem;
            position: relative;
            color: #fff;
            font-size: 0.5em;
        }

        .profit-l > p {
            width: 100%;
            height: 50%;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            line-height: 2em;
            color: #fff;
            font-size: 1em
        }

        .profit-r {
            float: left;
            width: 60%;
            height: 55%;
            margin-top: 5%;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            color: red;
            position: relative;;
            font-size: 1.5rem;
        }

        .profit-r > b {
            color: red;
            font-size: 1.9rem;
        }

        .profit-r > i {
            color: red;
            font-size: 1.5rem
        }

        .Ulogin-hr-wrap {
            position: relative;
            bottom: 0;
            margin-top: 4px;
            text-align: center;
            color: #ccc;
            font-size: 1rem;
            filter: alpha(opacity=80);
            -moz-opacity: 0.8;
            -khtml-opacity: 0.8;
            opacity: 0.8;
        }

        .Ulogin-hr-wrap {
            padding: 0px 0px;
        }

        .info {
            background-color: #666;
            border-radius: 4px;
            padding: 2px 2px;
            margin: 2px 0;
            display: inline-block;
            filter: alpha(opacity=100);
            -moz-opacity: 1;
            -khtml-opacity: 1;
            opacity: 1;
        }

        .uname {
            cursor: pointer;
            text-decoration: none;
            color: #ffffff;
            line-height: 1em;
        }

        #profit {
            color: red;
        }

        .amcharts-balloon-div.amcharts-balloon-div-categoryAxis div {
            background: #e32c2c;
        }

        div.header1 {
            float: left;
            font-size: 10px;
            padding: 10px 5px;
        }

        .header1.right > * {
            float: left;
        }

        .btn1 {
            display: block;
            border-radius: 6px;
            padding: 3px 18px;
            text-align: center;
        }

        .btn1-primary {
            background-color: #d3af30;
            color: #111;
        }

        .header1.right .btn1 {
            margin-right: 3px;
            position: relative;
            padding: 3px 5px;
        }

        .header1.right .btn1 span {
            background: #f91800;
            color: #fff;
            font-size: 0.7em;
            position: absolute;
            right: 0;
            top: -20px;
            border-radius: 5px;
        }

        .header1.right {
            position: absolute;
            right: 2px;
            top: 5px;
        }
        .select-gards{
            padding: 5px;
        }
        .select-gards, .select-gards div{
            background: #3c3b3b;
        }
        .select-row:after {
            content: "";
            display: block;
            overflow: hidden;
            clear: both;
        }

        .select-row > div > div {
            width: 120%;
            overflow: hidden;
        }

        .select-row > div {
            float: left;
            margin-top: 10px;
            height: 40px;
            line-height: 12px;
            overflow: hidden;
            text-align: center;
            margin-right: 5px;
        }

        .select-row > div a {
            position: relative;
            font-size: 0.9em;
            float: left;
            display: block;
            width: 83.3%;
            padding-top: 8px;
            height: 40px;
            /* background-color: #323232; */
            border: 1px solid #e39764;
            box-sizing: border-box;
            transition: width 0.2s;
            color: #fff;
        }

        .select-row > div.disabled a {
            background-color: #3E3E3E;
            border: 1px solid #363636;
            color: #7d7d7d;
        }

        .select-row > div a small {
            font-size: 10px;
            display: block;
        }

        .select-row > div a span {
            position: absolute;
            right: 0px;
            top: 10px;
            padding: 3px;
            background: #cf2e1d;
            color: #f59489;
            border-top-left-radius: 3px;
            border-bottom-left-radius: 3px;
            font-size: 12px;
        }

        .select-row.l_3 > div {
            width: 33.3%;
        }

        .select-row.l_4 > div {
            width: 25%;
        }

        .select-row div.active.number a {
            width: 55%;
        }

        .select-row > div.active a {
            background-color: #b28c26;
            color: #111111;
        }

        .select-row > div .ticket_number {
            color: #b28c26;
            transition: width 0.2s;
            float: left;
            width: 16%;
            height: 40px;
            box-sizing: border-box;
            text-align: center;
            background: #111;
            border: 0;
            border-radius: 0;
        }

        .select-row > div.active .ticket_number {

            width: 28%;

        }


        /*弹窗调整*/
        .pop-panel{ width: 100%;  position: fixed;  left:0; top:0; z-index: 999; display: none;}
        .pop-panel.show{ display: block;}
        .pop-panel.show>.pop-box{animation: popshow 0.2s;}
        .pop-panel>.pop-box{ border-radius:6px; overflow: hidden;  background-color: #3c3b3b; width: 90%; height: auto; position: absolute; top: 10%; left: 5%;}
        .pop-panel>.bg{ background: rgba(0,0,0,0.5); height: 100%; width: 100%; z-index: -1; position: absolute; top: 0; left: 0;}
        .pop-panel .pop-title{background-color: #5e5e5e; color: #aaaaaa; text-align: center; font-size: 16px; line-height: 45px; height: 45px;}
        .pop-panel .pop-content{ padding-top: 5px;}
        .pop-panel .pop-title>.btn{ position: absolute; right: 0; top: 0; width: 45px; height: 45px;border-radius: 0; border-top-right-radius: 5px; background: #515151; padding: 0;}
        .pop-panel p{color:#fff;}
        .pop-panel p.big_time{color:red}
        #buildConfirm {
            width: 96%;
            left: 2%;
        }
    </style>
    <!--js ??-->
    <script src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery-1.11.3.min.js"></script>
    <script src="/Public/Wap/jinrong/Mobile/Public/js/socket.io.min.js"></script>
    <script>
        var StockNameSpace;
        var IMAGES = "/Public/Wap/jinrong/Mobile/Public/images";
        var ROOT = "";
        var is_Close = <?php echo ($is_week); ?>;//<?php echo ($is_week); ?>;
        var capital_one = 'BTCCNY';
        var accessToken = '6900164f5d55ba2c699b427027109781';
        var host = 'http://60.205.162.163:8090/';
        var re_host = 'http://f.fangwuedu.com';
        var MIN_ORDER = 20;        //20151106
        var FLAG = 0;
        var WINNERT = 0.80;
        var wid = '';
        var apiRoot = ROOT + "/Mobile.php/Trading/";
        $("#apptitle").html('开始交易');
        function colsePop(selector, cb) {
            $(selector).hide();
            $('.mask').hide();
            $('body').removeClass('body-overflow');
            if (cb) {
                cb();
            }
        }
        var uid = 68329;
        function pop_Open() {
            $(".mask").show();
            //$("body").css("overflow","hidden");
        }
        function pop_Close() {
            $(".mask").hide();
            //$("body").css("overflow","auto");
        }
        var is_close = 0;
        var capital_one = 'BTCCNY';//比特币
    </script>
</head>
<body id="app">
<div class="wrap">
    <div class="index">
        <div class="outlook none" id="today_total">
            <!--今日盈亏-->
            <div class="outlook-box clearfix">
                <h3>今日盈亏(元)</h3>
                <p class="clearfix">
                    <span class="current-p now_plamount"></span>
                    <a id="trade_btn" class="p-btn">查看交易</a>
                </p>
            </div>
        </div>
        <div class="account-info clearfix">
            <div class="info-detail left" id="balance_"
                 style="float: left;background: url(<?php echo ($user['headimgurl']); ?>) left no-repeat;background-size: 4rem 4rem;">
                <?php if($_SESSION['is_sim'] == 1): ?><span class="a-u">实盘余额(元)</span>
                    <em class="a-d" id="money"><?php echo ($user['money']); ?></em>
                    <?php else: ?>
                    <span class="a-u">模拟资金</span>
                    <em class="a-d" id="minijin"><?php echo ($user['coin']); ?></em><?php endif; ?>
            </div>
            <div class="header1 right">
                <a href="<?php echo U('trading/coupon');?>" class="btn1 btn1-primary s mall"><i
                        class="iconfont icon-youhuiquan"></i>券</a>
                <a href="<?php echo U('User/recharge');?>" class="btn1 btn1-primary s mall">
                    <i class="iconfont icon-chongzhi"></i>充值</a>
                <a href="javascript:void(0)" id="mycoupon" class="btn1 btn1-primary s mall">
                    <i class="iconfont icon-shezhi"></i>
                    切换
                    <?php if( $_SESSION['is_sim'] == 1): ?>虚拟盘
                        <?php else: ?>
                        实盘<?php endif; ?>
                </a>
            </div>
        </div>
        <div class="switch-product" id="tabs">
            <ul class="product_switch clearfix" id="product_switch">
            </ul>
        </div>
        <div class="trade-box info-box">
            <div class="price-info clearfix"><h3 class="price-current"><span id="optionname">黄金</span><em
                    class="price_now_silver drop" id="now_price">0.00</em></h3>
                <ul class="price-trend clearfix">
                    <li class="">时间<em class="zuoshou_">0.00</em></li>
                    <li class="">最高<em class="height_">0.00</em></li>
                    <li class="">盘面
                        <?php if($_SESSION['is_sim'] == 1): ?><em class="panmian_">实盘</em>
                            <?php else: ?>
                            <em class="panmian_">模拟盘</em><?php endif; ?>
                    </li>
                    <li class="">最低<em class="low_">0.00</em></li>
                </ul>
            </div>
            <div class="swiper-container" id="options">
                <div class="swiper-wrapper" style="">
                    <div class="swiper-slide swiper-slide-visible" id="43" index="180"
                         style="width: 120px; height: 90px;">
                        <a href="javascript:void(0);"><h3>决胜时间</h3><h4><span>180</span> 秒</h4><h5>收益比例：80%</h5></a>
                    </div>
                    <div class="swiper-slide swiper-slide-visible swiper-slide-active" id="44" index="60"
                         style="width: 120px; height: 90px;"><a href="javascript:void(0);"><h3>决胜时间</h3><h4>
                        <span>60</span>
                        秒
                    </h4><h5>收益比例：85%</h5></a></div>
                    <div class="swiper-slide swiper-slide-visible" id="46" index="300"
                         style="width: 120px; height: 90px;">
                        <a
                                href="javascript:void(0);"><h3>决胜时间</h3><h4><span>300</span> 秒</h4><h5>收益比例：75%</h5></a>
                    </div>
                </div>
            </div>
            <ul class="buy-choose clearfix">
                <li><a href="javascript:void(0);" class="up">买涨</a></li>
                <li><a href="javascript:void(0);" class="down">买跌</a></li>
            </ul>
        </div>
        <div id="trade-box" style="display:none;">
            <div class="trade-box info-box" style=" padding-bottom: 0;">
    <span id="select_id" style="display:none;position:absolute;">
     </span>
                <div id="chart">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" id="svgId" width="415" height="175">
                        <defs>
                            <linearGradient id="highcharts" x1="0" y1="0" x2="0" y2="100%">
                                <stop offset=0 stop-color="rgb(0,197,205)" stop-opacity="1"/>
                                <stop offset=1 stop-color="rgb(0,229,238)" stop-opacity="0.1"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
            <ul class="trend-nav echart_ clearfix" id="time_diff">
                <li class="linechart"><a href="javascript:void(0);" class="changed" type="1">1分钟线</a></li>
                <li class="kchart"><a href="javascript:void(0);" type="3">3分钟线</a></li>
                <li class="kchart"><a href="javascript:void(0);" type="5">5分钟线</a></li>
                <li class="kchart"><a href="javascript:void(0);" type="15">15分钟线</a></li>
            </ul>
        </div>
        <div class="trend_chart ">
            <span>商品走势图</span>
            <div class="size_btn" v-if="map_time!='M1' && map_time!='1M'"><img @touchstart="map_zoom(1)"
                                                                               src="<?php echo RES;?>/jinrong/Mobile/weiyun/images/plus_btn.png"/>
                <img @touchstart="map_zoom(-1)" src="<?php echo RES;?>/jinrong/Mobile/weiyun/images/less_btn.png"></div>
            <div class="map">
                <div id="container" name="svgh" style="padding: 0px; width: 100%;
                                             height: 250px; font-size: 14px;">
                </div>
            </div>

            <ul class="btns" id="btns">
                <li @click="set_map('M1')" v-bind:class="{'active':map_time=='M1'}">分时图</li>
                <li @click="set_map('1M')" v-bind:class="{'active':map_time=='1M'}">1分钟K线</li>
                <li @click="set_map('M3')" v-bind:class="{'active':map_time=='M3'}">3分钟K线</li>
                <li @click="set_map('M5')" v-bind:class="{'active':map_time=='M5'}">5分钟K线</li>
                <li @click="set_map('M15')" v-bind:class="{'active':map_time=='M15'}">15分钟K线</li>
            </ul>
        </div>
        <div>
            <div id="show_bili">
                <div class="trade-count">
                    <span class="icon"></span>
                    今日已有<span class="trade-num" id="renshu">0</span>人参与交易，买卖<span class="trade-num"
                                                                                  id="trade_count">0</span>次
                </div>
            </div>
        </div>
    </div>
    <!--最新资讯-->
    <div class="info-box">
        <ul class="info-nav clearfix">
            <li><a class="selected" id="newodrers">最新成交</a></li>
            <li id="orders"><a>持仓订单</a></li>
            <li id="history"><a>交易记录</a></li>
        </ul>
        <div class="realtimebox info-d" id="realtimebox">
            <div class="realtimeleft">
                <div class="solid">
                    <div class="box">
                        <div id="marketEntrust" style="min-height:200px;">
                            <div class="real-left">
                                <ul class="l-tt-transcation">
                                    <li class="li-tt-transcation" style="width:20%;">买入时间</li>
                                    <li class="li-tt-transcation" style="width:20%;">买入资产</li>
                                    <li class="li-tt-transcation" style="width:20%;">买入方向</li>
                                    <li class="li-tt-transcation" style="width:40%;">买入量</li>
                                </ul>
                                <div class="box-ct-transcation">
                                    <ul class="l-ct-transcation" id="depth_buy_context">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="info-d none">
            <table class="rec-table rec-table6" id="now_list">
                <tr class="rec-tr">
                    <th class="rec-th">下单时间</th>
                    <th class="rec-th">资产类型</th>
                    <th class="rec-th">买入方向</th>
                    <th class="rec-th">执行价格</th>
                    <!--<th class="rec-th">当前价格</th>-->
                    <th class="rec-th">订单状态</th>
                </tr>
            </table>
            <div class="com-empty hide" style="padding-bottom: 100px;">
                <div class="come-txt"></div>
            </div>
        </div>
        <div class="info-d none" role="main">
            <table class="rec-table rec-table6" id="all_list">
                <tr class="rec-tr">
                    <th class="rec-th">资产类型</th>
                    <th class="rec-th">涨/跌</th>
                    <th class="rec-th">到期时间</th>
                    <th class="rec-th">买入金额</th>
                    <th class="rec-th">盈利情况</th>
                    <th class="rec-th">订单状态</th>
                </tr>
            </table>
            <div class="com-empty1" style="padding-bottom: 100px;">
                <div class="come-txt1">查看更多历史订单</div>
            </div>
        </div>
    </div>
</div>
<div class="floatVideo none">
    <div class="close-btn-video"><img class="gp-ad-video-close"
                                      src="<?php echo RES;?>/jinrong/Mobile/Public/images/gp-video-close.png"></div>
    <video id="myvideo" class="gp-video" width="140" height="78.75" autoplay playsinline="true" webkit-playsinline=""
           src=""></video>
</div>
<!--直播提醒-->
<!--输入交易密码-->
<div class="pop-box none" id="lgbox">
    <nav class="pop-nav">
        <h3>输入交易密码</h3>
    </nav>
    <form id="pwdForm" class="pwd-form" method="post" action="#">
        <input type="password" id="Pwd" class="form-input" placeholder="交易密码" maxlength="6">
        <input type="button" value="确认" class="pwd-btn" id="lgbtn">
        <a href="javascript:void(0);" class="p-link" id="forgot">忘记交易密码？</a>
    </form>
</div>
<!--参与投票-->
<!--建仓确认-->
<div class="pop-box none pop-panel" id="buildBox">
    <!--<nav class="pop-nav pop-title">-->
        <!--<a href="javascript:void(0);" class="back"></a>-->
        <!--<h3>确认购买<span id="now_time"></span></h3>-->
    <!--</nav>-->
    <div class="pop-title">购买<a class="btn close back" style="left:initial;"><i class="iconfont icon-guanbi"></i></a>
    </div>
    <div class="active pop-content pop_buy">
        <h1>购买：<b>100</b>元 <i class="red">预期收益：<span>185</span>元</i></h1>
        <ul class="btn-row">
            <li class="item in btns">
                <p><i>5000</i>元</p>
            </li>
            <li class="item in btns">
                <p><i>2000</i>元</p>
            </li>
            <li class="item in btns">
                <p><i>1000</i>元</p>
            </li>
            <li>
                <p><i>500</i>元</p>
            </li>
            <li class="slct">
                <p><i>200</i>元</p>
            </li>
            <li>
                <p><i>100</i>元</p>
            </li>
            <li>
                <p><i>10</i>元</p>
            </li>
            <li class="not">
                <p>其它金额</p>
                <input type="number" value="" id="input_money"/>
            </li>
        </ul>
    </div>
    <div class="select-gards">
        <div style="color:#fff;" v-if="ticket_list!=null && ticket_list.length>0">使用优惠券</div>
        <div class="select-row l_3">
            <template v-for="item in ticket_list">
                <div v-bind:class="{'active':ticket_index==$index,'number':item.count>1,'disabled':item.count==0}">
                    <div>
                        <a @click="select_ticket($index)">{{item.coupon.name}}
                            <small> {{item.count}}张</small>
                            <span v-if="item.count>1 && ticket_index==$index">{{item.count}}</span>
                        </a>
                        <input v-if="item.count>1 " @input="ticket_change(item.count)" type="number"
                            v-model="ticket_number" class="ticket_number"/>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <table class="form-group tradestd_table M_trad_table">
        <tbody>
        <tr class="tr01">
            <td class="td">
                资产类型：<span id="flow_span">USOIL</span></td>
            <td>结算周期：<span id="flow_span_time"> 60秒 </span></td>
        </tr>
        <tr class="tr01">
            <td class="td">订单方向：<span id="flow_span_dir" class="td_big ">看涨</span></td>
            <td class="">当前价格：<span id="flow_span_value" class="td_big ">0.00</span></td>
        </tr>
        </tbody>
    </table>
    <input type="button" class="pwd-btn change_product" value="确 认" id="buybtn">
    <!--余额不足，去充值-->
    <!--<a href="javascript:void(0);" class="pwd-btn chr tocharge none">余额不足，去充值</a>-->

</div>


<!--建仓成功-->
<div class="pop-box none" id="buildBox2">
    <nav class="pop-nav">
        <a href="javascript:void(0);" class="back"></a>
        <h3>玩色子<span id="now_time2"></span></h3>
    </nav>
    <div class="active ac2">
        <h1>下注：<b>10</b>元</h1>
        <ul>
            <li>
                <p><i>10</i>元</p>
            </li>
            <li>
                <p><i>9</i>元</p>
            </li>
            <li>
                <p><i>8</i>元</p>
            </li>
            <li>
                <p><i>7</i>元</p>
            </li>
            <li>
                <p><i>6</i>元</p>
            </li>
            <li class="slct">
                <p><i>5</i>元</p>
            </li>
            <li>
                <p><i>4</i>元</p>
            </li>
            <li>
                <p><i>3</i>元</p>
            </li>
            <li>
                <p><i>2</i>元</p>
            </li>
            <li>
                <p><i>1</i>元</p>
            </li>
        </ul>
    </div>
    <div id="area" style="width:60%;float: left;"></div>
    <ul class=" dice-info clearfix" style="width:40%">
        <li class="">时间<em class="zuoshou_">11:03:44</em></li>
        <li class="">最高<em class="height_">5159.31</em></li>
        <li class="">盘面
            <em class="panmian_">模拟盘</em>
        </li>
        <li class="">最低<em class="low_">5158.53</em></li>
    </ul>
    <input type="button" class="pwd-btn change_product" value="确 认" id="buybtn2">
    <!--余额不足，去充值-->
    <!--<a href="javascript:void(0);" class="pwd-btn chr tocharge none">余额不足，去充值</a>-->
    </form>
</div>
<div class="pop-box none  pop-panel" id="buildConfirm">
    <!--<nav class="pop-nav">-->
        <!--<a href="javascript:void(0);" class="back"></a>-->
        <!--<h3>心跳进行中...</h3>-->
    <!--</nav>-->
    <div class="pop-title">持仓进行中...<a class="btn close back" style="left:initial;"><i class="iconfont icon-guanbi"></i></a></div>
    <p class="order_p big_time" id="fnTimeCountDown"><span class="sec">59</span><span class="hm1"></span></p>
    <p class="order_p">执行价格：<i id="buy_price">0.00</i></p>
    <p class="order_p" id="dangqian">当前价格：<i id="flow_span_value1">0.00</i></p>
    <p class="order_p none" id="daoqi">到期价格：<i id="flow_span_daoqi">0.00</i></p>
    <table class="form-group tradestd_table M_trad_table">
        <tbody>
        <tr class="tr01">
            <td class="td">订单方向：<span id="flow_span_dir1" class="td_big ">看涨</span></td>
            <td class="">预测结果：<span id="flow_span_value2" class="td_big ">平局</span><span id="flow_span_value3"
                                                                                         class="td_big hide"></span>
            </td>
        </tr>
        </tbody>
    </table>
    <input type="button" class="pwd-btn" value="继续下单" id="setting">
</div>
<div class="mask none"></div>
<!--确认平仓-->
<div class="pop-box none" id="buildClose">
    <nav class="pop-nav">
        <a href="javavscript:void(0);" class="back"></a>
        <h3>确认平仓</h3>
    </nav>
    <form id="clForm" class="cl-form" method="post" action="javascript:void(0);">
        <div class="cl-i-list">
            <p class="c-1" id="pcname"></p>
            <p class="c-2 price_now_pop"></p>
            <p class="c-1" id="pccount"></p>
            <p class="c-3 " id="pcamount"></p>
        </div>
        <p class="cl-f clearfix">
            <input type="button" class="cl-f-btn f-l" onClick="$('a.back').click();" value="取 消">
            <input type="button" class="cl-f-btn f-r" value="确认平仓" id="confirmclose">
        </p>
    </form>
</div>
<!--查看交易-->
<div class="pop-box pop-width none" id="trade-list">
    <nav class="pop-title">
        <h3>今日盈亏：<a class="now_plamount"></a></h3>
        <a href="javascript:void(0);" class="close_list back" style="left: auto;"></a>
    </nav>
    <form id="clForm" class="cl-form" method="post" action="javascript:void(0)">
        <ul class="cl-list" id="myorders">
        </ul>
    </form>
</div>
<script type="text/javascript">
    var syzyj = 0;
    $("#syzyj").click(function () {
        var deal_amount = $(".slct p i").text();
        // alert(deal_amount);
        jQuery.ajax({
            type: 'post',
            url: ROOT + "/Wap/Trading/deal_zyq",
            data: "amount=" + deal_amount,
            dataType: 'JSON',
            async: true,
            error: function (data) {
                showLoading('下单失败！', 2000);
                pop_Close();
                return 0;
            },
            success: function (data_t) {
                if (data_t.stat == 1) {
                    showLoading(data_t.info, 2000);
                    syzyj = 1;
                } else {
                    showLoading(data_t.info, 2000);
                }
            }
        });
    })
</script>
<!-- <div class="news-detail fudai clearfix none" id="event101">
    <div class="n-nav">
        <img src="<?php echo RES;?>/jinrong/Mobile/Public/images/tree10.png" />
        <a href="javascript:void(0);" class="n-close"></a>
    </div>
    <p class="n-content">恭喜，您已获得<span>8</span>元体验券</p>
        <input class="notice-sub-4" value="确 认" type="button">
        <div class="f-c-line notice-jl clearfix">
            <p class="c-c-l clearfix">
                <input id="choose2" value="1" name="flag" type="checkbox">
                <label for="choose2"></label>
            </p>
            <label class="f-c-label">
                不再提醒
            </label>
        </div>
</div> -->
<style>
    .pop-box.trans {
        top: 2%;
        left: 0;
        width: 100%;
        border-radius: 0;
        background: transparent;
        z-index: 9999;
    }
</style>
<div class="pop-box trans" id="getNow" style="display:none;">
    <img src="<?php echo RES;?>/jinrong/Mobile/Public/images/apriltrans3.png" class="pop-shadow">
    <div class="pop-wrap">
        <div class="pw-img"></div>
        <div class="pop-nav">
            <a href="javascript:void(0);" class="p-n-close" id="guanbi_shixi"></a>
        </div>
        <div class="pop-text">
            <p>各位小伙伴们</p>
            <p>【港云外汇】第一季“练习生计划”</p>
            <p>已经开始了!!!!!</p>
            <p>活动期间新用户</p>
            <p><label>免费获得8888元</label>模拟资金</p>
            <p>体验60秒的快感~</p>
            <p>感觉不错再“<label>充值资金到实盘</label>”</p>
            <p>赚大钱吧！</p>
        </div>
        <a href="javascript:;" class="p-w-btn surePlan_js" id="close_shixi"></a>
    </div>
</div>
<div class="loading-wrapper" style="display: none;">
    <div class="loading-area">
        <div id="floatingBarsG1" class="floatingBarsG"></div>
        <p id="msg">登录中...</p>
    </div>
</div>
</div>
<!--<div class="profit_box" style="display: block;height:25px;opacity:1">-->
    <!--<ul class="profit" style="bottom: -119.776px;">-->
        <!--<li class="Ulogin-hr-wrap"><span class="info">-->
	<!--<img src="/Public/Wap/jinrong/Mobile/Public/ad/logo_png.png" width="17"> <font class="uname"-->
                                                                                   <!--id="profit_name"></font> 刚刚<font-->
                <!--color="red">&nbsp;&nbsp;盈利</font>-->
<!--<i id="profit"></i></span></li>-->
    <!--</ul>-->
<!--</div>-->
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/jq.cookies.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/amcharts.js?v=110" charset="utf-8"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/serial.js?v=110"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/tz_black.js?v=110"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/initializa.js?v=110"></script>

<script type="text/javascript" src="<?php echo RES;?>/jinrong/Mobile/Public/js/common.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/iscroll.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/idangerous.swiper.min.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/svgKline.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/svgMline.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/globalParameters.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/tools.js?k=1.6"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/drawSvg.js?k=1.1"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/weipan/drawdat_new.js?k=1.6"></script>
<!--<script src="<?php echo RES;?>/jinrong/Mobile/Public/js/one_user.js"></script>-->

<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/vue.min.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/layer.js"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/public.js"></script>


<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/index.js?v=110"></script>
<script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/Utils.js?v=110"></script>
<script type="text/javascript">
    $(function () {
        $('.jz-footer ul a').removeClass('active');
        $('.jz-footer ul li').eq(0).find('a').addClass('active');
    })
    var last_orderid = 0;
//    function getnewprofit() {
//        $.ajax({
//            type: 'get',
//            dataType: 'json',
//            url: "/Wap/Cron/get_newprofit",
//            success: function (msg) {
//                //$("#shuang11").text(msg.trade_amount);
//                // demo.update(msg.trade_amount);
//                if (msg.id > last_orderid) {
//                    last_orderid = msg.id;
//                    if (msg.phone == null) {
//                        $("#profit_name").text(msg.tel);
//                    } else {
//                        $("#profit_name").text(msg.phone);
//                    }
//                    $("#profit_amount").html(msg.amount + '元' + msg.direction + msg.cap_name);
//                    $("#profit").text(msg.profit + '元');
//                    $(".profit_box").fadeIn(800);
//                    $('.profit').css('bottom', '-10rem');
//                    $('.profit').stop().animate({'bottom': 0}, 400);
//                } else {
//                    $(".profit_box").fadeOut(800);
//                }
//            }
//        });
//    }
//    var time = setInterval(function () {
//        getnewprofit();
//    }, 3000);
</script>
<link rel="stylesheet" href="<?php echo RES;?>/jinrong/Mobile/Public/images/iconfont/iconfont.css">
<style>

    .jz-footer {
        height: 56px;
        bottom: 0;
         border-top: 0;
        box-shadow: 0 -3px 3px rgba(5,5,5,0.05);
        background: #3c3b3b!important;
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #fafafa), to(#fafafa));
    }
    .foot_nav li a{
        color:#fff;
    }
</style>
<footer class="jz-footer">

    <ul class="foot_nav jz-flex-row font-lg">
        <li class="jz-flex-col"> <a class="bd active" href="<?php echo U('Trading/index');?>"><i class="jz-icon icon-conduct-null"></i><span>交易</span></a></li>
        <!--<li class="jz-flex-col"> <a class="bd " href="<?php echo U('Trading/dice');?>"><i class="jz-icon iconfont icone-21466"></i><span>玩色子</span></a></li>-->
        <li class="jz-flex-col"><a class="bd " href="<?php echo U('User/insurance');?>"><i class="jz-icon new_icon-zhibo2"></i><span>新手教程</span></a> </li>
        <!--<li class="jz-flex-col"><a class="bd " href="<?php echo U('Trading/jueshengquan');?>"><i class="jz-icon icon-friends01"></i><span>决胜圈</span></a> </li>-->
        <li class="jz-flex-col"><a class="bd " href="<?php echo U('User/invite');?>"><i class="jz-icon icon-vip04"></i><span>全民经纪人</span></a> </li>
		<li class="jz-flex-col"><a class="bd " href="<?php echo U('User/private_person');?>"><i class="jz-icon icon-accounts-null "></i><span>账户</span></a> </li> 

    </ul>

</footer>
</body>
</html>