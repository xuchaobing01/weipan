<?php if (!defined('THINK_PATH')) exit();?>


<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <link type="text/css" href="<?php echo RES;?>/jinrong/Mobile/weiyun/css/public.css?v=110" rel="stylesheet" />
    <script>
        //页面加载效果
        function loadpage() {
            var load_html = '<div class="page_load" id="page_losd" style="position:fixed; width:100%; height:100%; background:#000000; z-index:999999; top:0; left:0; bottom:0; right:0;">' +
                '<div class="loader show">' +
                '<div class="act">' +
                '  <div class="spinner">  <div class="double-bounce1"></div>  <div class="double-bounce2"></div></div>' +
                ' </div>' +
                '<div class="bg"></div>' +
                '  <span class="text">页面加载中...</span>' +
                '</div></div>'
            document.write(load_html);

        }
        loadpage();
        document.onreadystatechange = loadpagecom;
        //加载状态为complete时移除loading效果
        function loadpagecom(close) {
            if (close || document.readyState == "complete") {
                var loadingMask = document.getElementById('page_losd');
                if (!loadingMask) return;
                loadingMask.parentNode.removeChild(loadingMask);
            }
        }
    </script>
    <link href="http://at.alicdn.com/t/font_2hn9yxdur6n7b9.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo RES;?>/jinrong/Mobile/Public/js/jquery-1.11.3.min.js"></script>
    <script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/vue.min.js"></script>
    <script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/public.js?v=110"></script>
    <script src="<?php echo RES;?>/jinrong/Mobile/weiyun/js/layer.js"></script>
    <link type="text/css" href="<?php echo RES;?>/jinrong/Mobile/weiyun/css/layer.css" rel="stylesheet" />


    <title>我的优惠券</title>
    <meta name="format-detection" content="telephone=no" />
    <link type="text/css" href="<?php echo RES;?>/jinrong/Mobile/weiyun/css/detail.css?v=110" rel="stylesheet" />
    <style>
        .card-coupons{}
        .card-coupons>div a{ width: 33.3%}

        .card-coupons>.voucher-box{ padding: 0 15px;}
        .card-coupons>.voucher-box li{ background: #ab3c3c; box-sizing: border-box; padding: 0.4em 0 0.4em 0.8em; width: 100%; border-left:1.2em solid #dfb938; margin-top: 20px; position: relative; color: #dfb938; overflow: hidden;}
        .card-coupons>.voucher-box li>span{ color: #dfb938; font-size: 0.9em; display:block; height:20px;}
        .card-coupons>.voucher-box li>.price{ margin: 1em 0 0.6em 0;}
        .card-coupons>.voucher-box li>.price>b{ font-size:3.9em; margin-right: 0.1em;}
        .card-coupons>.voucher-box li>p{ font-size: 0.8em; line-height: 1.5em;}
        .right-name{ position: absolute; z-index: 1;  width: 40px;  right: 17px;  top: 0; border-left:dashed #d3d3d3 1px;  height: 100%; text-align: center; font-size: 1.3em; box-sizing: border-box; padding-top: 1.7em; line-height: 1.2em;padding-left: 8px;}
        .y-uan{position: absolute;z-index: 2; width: 42px; height: 42px; right: -28px; top: 0; background: #000; border-radius: 50px; margin-top: 2.8em;}
        .s-liang{ position: absolute; z-index: 3; width: 52px; height: 52px; right:72px; top: 1em; border: #CBC935 solid 2px; border-radius: 100%; line-height: 52px; text-align: center; color: #fff8a6; transform:rotate(-15deg)}

        .gray {
            -webkit-filter: grayscale(100%);
            -moz-filter: grayscale(100%);
            -ms-filter: grayscale(100%);
            -o-filter: grayscale(100%);
            filter: grayscale(100%);
            filter: gray;
        }
        .bottom_more
        {
            margin:15px;
            background:#303030;
        }
    </style>
    <script type="text/javascript">
        var vue;
        $(function () {
            vue=new Vue({
                el: 'body',
                data: {
                    index: 0,
                    items: ['未使用', '已使用', '已过期'],
                    page_all: 0,
                    page: 1,
                    list:null,
                },
                methods: {
                    init: function () {
                        this.nextPage();
                    },
                    nextPage: function () {
                        var self = this;
                        var pagesize = 10;
                        loadshow();
                        $.get('/wap/trading/getmycoupon', { "pageindex": self.page, "pagesize": pagesize, "type": this.index + 1 }, function (data) {
                            loadhide();
                            self.page_all = Math.ceil(data.totals / pagesize);
                            if (self.list == null || self.list.length==0) {
                                self.list = data.List;
                            } else {
                                self.list = self.list.concat(data.List)
                            }

                            self.page++;
                        })
                    },
                    tab: function (index) {
                        this.page = 1;
                        this.index = index;
                        this.list = null;
                        this.nextPage();
                    },
                    to_detail: function (obj) {
                        obj.status = this.index;
                        window.sessionStorage.coupon = JSON.stringify(obj);
                        location.href = "/wap/trading/coupon_detail";
                    },
                    getLocalTime: function (str) {
                        return str;
                    }

                }
            })
            vue.init();

        })

    </script>
    <title>
        港云外汇
    </title></head>
<body>

<div class="card-coupons">
    <div class="head-menu uk-block  uk-block-default"><a  v-for="item in items" v-bind:class="{ 'active': index==$index }" @click="tab($index)" href="#">{{ item }}</a></div>
    <div class="voucher-box" >
        <ul  v-bind:class="{'gray':index!=0}">
            <li v-for="item in list" @click="to_detail(item)">
                <span ><template v-if="item.coupon.satisfy_amount>0">满{{item.coupon.satisfy_amount}}元使用</template></span>
                <div class="price"><b>{{item.coupon.amount}}</b>元</div>
                <p>使用范围：<template v-if="item.coupon.use_area">{{item.coupon.use_area}}</template></p>
                <p>有效时间：{{item.add_time}}~{{item.over_time}}</p>
                <div class="right-name">{{item.coupon.name}}</div>
                <div class="y-uan"></div>
                <div class="s-liang"><b>{{item.count}}</b><small>张</small></div>
            </li>
        </ul>
    </div>
    <a class="bottom_more" @click="nextPage()" v-if="page_all>=page">加载更多</a><a v-if="page>page_all" class="bottom_no_more"  >没有更多了~</a>
</div>


<div class="loader">
    <div class="act">
        <div class="spinner">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
    </div>
    <div class="bg"></div>
</div>

</body>
</html>