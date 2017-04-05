var deal_product = 'hot';
//var capital_one = 'XAUUSD';
var data_setInt = 0;
var data_setInt_option = 0;
var default_order = 10;//下单默认金额
var order_dirt = 0; //0看涨1看空
var deal_time = 0;//到期时间
var time_limit = 0;
var inteval_time = 1000;
var diff_one = 1; //1min 5min 5min 60min
var chgsta = 0;   //记录循环的变量
var times = '60';
//var timer_countDown = null;
//var new_order_id = 0;
var current_order_id=0;
var new_order_price = 0;
var e_price = 0;
var fate = 0;
var e_is = 0;
var shifoukaishi = 1;
window.ed = 0;
var rsecond=0;
capitalHJRMBBYR = 0;
$(function () {
    function getOption_type(deal_product) {
        $.ajax({
            type: 'post',
            url: ROOT + '/Wap/trading/getoption',
            data: {deal_product: deal_product},
            //dataType : 'JSON',
            //async : true,
            //timeout: 900,
            success: function (data_t) {
                data_t = eval('(' + data_t + ')');
                //$("#product_switch").empty();
                $.each(data_t, function (n, value) {
                    if (is_close == 1 && value.capital_key == 'BTCCNY') {

                    } else if (value.capital_key == capital_one) {
                        $("#product_switch").append('<li class="sw_active"><a href="javascript:void(0);" id="change_' + value.capital_key + '"  type="' + value.capital_key + '">' + value.capital_name + '</a></li>');
                    } else {
                        $("#product_switch").append('<li id="change_' + value.capital_key + '"   type="' + value.capital_key + '"><a href="javascript:;" id="change_' + value.capital_key + '"   type="' + value.capital_key + '">' + value.capital_name + '</a></li>');
                    }
                    $("#product_switch").html();
                    //$('#selectmenu3').selectmenu('refresh', true);
                });

                var myscroll;

                function loaded() {

                    myscroll = new iScroll("tabs");

                }

                window.addEventListener("DOMContentLoaded", loaded, false);
                /*var swiper_tabs = new Swiper('.switch-product', {
                 scrollbar: '.product_switch',
                 scrollbarHide: true,
                 slidesPerView: 'auto',
                 centeredSlides: true,
                 spaceBetween: 30,
                 grabCursor: true
                 });*/
            },
            error: function (data) {
                //alert('hear');
                location.reload();
            }
        });
    }

    function get_changdu(money) {
        money = parseInt(money);
        var changdu = 10;
        if (money <= 50) {
            changdu = 20;
        } else if (money <= 100) {
            changdu = 30;
        } else if (money <= 500) {
            changdu = 60;
        } else if (money <= 1000) {
            changdu = 70;
        } else if (money <= 2000) {
            changdu = 100;
        } else {
            changdu = 100;
        }
        if (money == 20) {
            changdu = 10;
        } else if (money == 50) {
            changdu = 15;
        } else if (money == 100) {
            changdu = 30;
        }
        else if (money == 200) {
            changdu = 40;
        } else if (money == 500) {
            changdu = 60;
        } else if (money == 1000) {
            changdu = 85;
        } else if (money == 2000) {
            changdu = 100;
        }
        return changdu;
    }

    $("#mycoupon").click(function () {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: ROOT + '/Wap/user/set_sim',
            beforeSend: function () {
                showLoading('切换中...');
            },
            success: function (n) {
                var i = n.status;
                switch (i) {
                    case 1:
                        showLoading('切换成功！', 2000);
                        setTimeout(function(){
                            window.location.href=window.location.href+"?ran="+10000*Math.random();
                        }, 2000);
                        break;
                    case -0 :
                    default:
                        showLoading(n.info, 3000);
                }
            },
            error: function () {
                hideLoading();
            },
            complete: function () {
                //hideLoading()
            }
        })
    });
    $("#close_notice_btn").click(function () {
        $("#sysNotice").addClass('none');
        var checked = $("#choose1").get(0).checked;
        if (checked) {
            $.cookie('close_Notice', 1, {path: '/', expires: 3600});
        }
        //jump('/mobile.php/public/go_to_room');
    });
    if ($.cookie('close_Notice')) {
        $("#sysNotice").addClass('none');
    } else {
        $("#sysNotice").removeClass('none');
    }
    $("#close_Notice").click(function () {
        var checked = $("#choose1").get(0).checked;
        if (checked) {
            $.cookie('close_Notice', 1, {path: '/', expires: 3600});
        }
        $("#sysNotice").addClass('none');
    });
    $("#ok_liveNotice").click(function () {
        $("#liveNotice").addClass('none');
        $(".gp-video").attr("src", 'http://21602.hlsplay.aodianyun.com/60s/stream.m3u8');
        $(".floatViewAd").hide();
        $(".floatVideo").show();
    });
    $("#close_liveNotice").click(function () {
        $("#liveNotice").addClass('none');
    });
    function get_newallorder() {
        var changdu = 0;
        var buy_id = '';
        var changdu = 0;
        $.ajax({
            type: 'get',
            url: ROOT + "/Wap/trading/get_newallorder_ajax",
            dataType: 'JSON',
            //async : true,
            //timeout: 900,
            success: function (newallorder_data) {

                if (newallorder_data['status'] == 1) {
                    // $("#renshu").html(newallorder_data['renshu']);
                    // $("#trade_count").html(newallorder_data['trade_count']);
                    $("#depth_buy_context").html('');
                    $.each(newallorder_data.list, function (key, value) {
                        if(value.capital_name == '玩色子'){
                            if (value.trade_direction == 0) {
                                value.trade_direction = '<font color=green>买小</font>';
                                buy_id = 'buy';
                            } else if (value.trade_direction == 1) {
                                value.trade_direction = '<font color=red>买大</font>';
                                buy_id = 'sell';
                            } else {
                            }
                        }
                        else{
                            if (value.trade_direction == 0) {
                                value.trade_direction = '<font color=green>买跌</font>';
                                buy_id = 'buy';
                            } else if (value.trade_direction == 1) {
                                value.trade_direction = '<font color=red>买涨</font>';
                                buy_id = 'sell';
                            } else {
                            }
                        }

                        changdu = get_changdu(value.trade_amount);
                        $("#depth_buy_context").append('<li class="li-ct-transcation"><div class="part-ct-transcation" style="width:20%;">' + value.shijian + '</div><div class="part-ct-transcation ' + buy_id + '" style="width:20%;">' + value.capital_name + '</div><div class="part-ct-transcation_right" style="width:20%;"><span id="buyPriceSpan1">' + value.trade_direction + '</span></div><div class="part-ct-transcation_right" style="padding-left: 0px"><span id="buyAmountSpan1">' + value.trade_amount + '</span></div><div class="part-ct-transcation" style="padding-left:0.8em"><span style="width:' + changdu + 'px;" class="' + buy_id + 'span" id="buySpanColor1"></span></div></li>');
                    });
                }
                if (newallorder_data['live_status'] !== 0 && $.cookie('liveNotice_new') != newallorder_data['live_status']) {
                    //$.cookie('liveNotice_new',newallorder_data['live_status']);
                    $.cookie('liveNotice_new', newallorder_data['live_status'], {path: '/', expires: 1});
                    if (newallorder_data['live_status'] == 8) {
                        $("#liveNotice .trade-num").text('美女分析师正在播报财经早餐，了解全球财经资讯，洞悉市场行情走向。每天早十点，与美女分析师相约决胜六十秒！');
                    } else {
                        $("#liveNotice .trade-num").text('金牌分析师直播带单正在进行中，点击左下方直播按钮，边听讲课边交易哦！赶快试试吧！');
                    }
                    $("#liveNotice").removeClass('none');
                }
            },
            error: function (data) {
                // alert("获取资产类型失败！");
            }
        });
    }

    /*function change_option(key,obj){
     $("#time_diff li").find("a").removeClass("changed");
     $("#change_"+key).addClass("changed");
     diff_one = 1;
     clearInterval(chgsta);
     capital_one = key;
     }
     */
    /*$("#product_switch li a").click(function() {
     $("#time_diff li").find("a").removeClass("changed");
     $("#time_diff li").find("a").eq(0).addClass("changed");
     diff_one = 1;
     clearInterval(chgsta);

     var p = $(this).attr('type');
     capital_one = p;
     });*/
    //资产切换end

    //金额显示
    // $('#deal_amount').keyup(function() {
    // var con_t = $(this).val();
    // con_t = con_t.replace("金额￥ ", "");
    // profit_change(0, con_t);

    // });


    /* $(".floatViewAd").on("click",function(){
     jump('http://www.huaimingjiaoyu.com/room/m/');
     });
     */
    $("#close_shixi").on("click", function () {
        $("#getNow").hide();
        $("body").css("overflow", "auto");
    });
    $("#guanbi_shixi").on("click", function () {
        $("#getNow").hide();
        $("body").css("overflow", "auto");
    });
    $(".floatViewAd").on("click", function () {
        $(".gp-video").attr("src", 'http://21602.hlsplay.aodianyun.com/60s/stream.m3u8');
        $(".floatViewAd").hide();
        $(".floatVideo").show();
    });
    $(".close-btn-video").on("click", function () {
        $(".floatViewAd").show();
        $(".gp-video").attr("src", '');
        $(".floatVideo").hide();
    });
    $(".up").on("click", function () {
        order_dirt = 1;

        $(".active ul").find('li').siblings().removeClass('slct');
        $(".active ul").find('li').eq(4).addClass('slct');

        set_shouyi();

        $('#buildBox').show();
        pop_Open();
        set_order_message();
        vue.get_ticket();
    });
    $(".back").on("click", function () {
        //clearTimeout(timer_countDown);
        $('#buildBox').hide();
        $('#buildConfirm').hide();
        pop_Close();
        var type = $("#time_diff a.changed").attr("type");
        var diff = type * 60;
        //clearInterval(window.param[getCapitalTypeById("sw_active")+diff]);
        //console.log(getCapitalTypeById("sw_active")+diff);
        //setTimeout(function () {drawKline_end(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "deal_value");}, 2000);//yang
    });
    $("#buybtn").on("click", function () {
        if (!$("#flow_span_value").text()) {
            return false;
        }

        $('#buildBox').hide();
        $("#daoqi").hide();
        $("#dangqian").show();
        $("#flow_span_value2").show();
        $("#flow_span_value3").hide();
        $("#buy_price").text();
        $("#fnTimeCountDown").html('<span class="sec">59</span>');
        deal_ajax(order_dirt);

        //show_date_time(60);
    });
    $("#setting").on("click", function () {
        //clearTimeout(timer_countDown);
        $('#buildBox').hide();
        $('#buildConfirm').hide();
        pop_Close();
        var type = $("#time_diff a.changed").attr("type");
        var diff = type * 60;
        //clearInterval(window.param[getCapitalTypeById("sw_active")+diff]);
        console.log(getCapitalTypeById("sw_active") + diff);
        //setTimeout(function () {drawKline_end(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "deal_value");}, 2000);//yang
    });
    $(".down").on("click", function () {
        order_dirt = 0;
        var money = 0;
        $(".active ul").find('li').siblings().removeClass('slct');
        $(".active ul").find('li').eq(4).addClass('slct');
        var money = $(".slct p i").text();
        if (money == '') {
            money = $("#input_money").val();
        }
        $("#input_money").val(money);
        $('.active h1 b').text(money);

        // times = $(".swiper-slide-active h4 span").html();
        // times = times.replace(" ", "");
        // times = parseInt(times);
        // var bili = set_profit(times);
        // var yuqi = 0;
        // var yuqi = money * bili;
        // yuqi = parseFloat(yuqi) + parseFloat(money);
        // $('.active h1 i').html('预期收益：<span>' + yuqi + '</span>元');
        set_shouyi();
        $('#buildBox').show();
        pop_Open();
        set_order_message();
    });
    $("#input_money").on("click", function () {
        $(".slct").removeClass("slct");

        var money = 100;
        $("#input_money").val(money);
        $('.active h1 b').text(money);
        // times = $(".swiper-slide-active h4 span").html();
        // times = times.replace(" ", "");
        // times = parseInt(times);
        // var bili = set_profit(times);
        // var yuqi = 0;
        // var yuqi = money * bili;
        // yuqi = parseFloat(yuqi) + parseFloat(money);
        // $('.active h1 i').html('预期收益：<span>' + yuqi + '</span>元');
        set_shouyi();
        vue.get_ticket();
    });
    $('#input_money').on('input propertychange',
        function () {
            $(".slct").removeClass("slct");
            var money = $("#input_money").val();
            if(parseFloat(money) > 5000){
                money = 5000;
            }
            $("#input_money").val(money);
            $('.active h1 b').text(money);

            // times = $(".swiper-slide-active h4 span").html();
            // times = times.replace(" ", "");
            // times = parseInt(times);
            // var bili = set_profit(times);
            // var yuqi = 0;
            // var yuqi = money * bili;
            // yuqi = parseFloat(yuqi) + parseFloat(money);
            // $('.active h1 i').html('预期收益：<span>' + yuqi + '</span>元');
            set_shouyi();
        });
    $('.active ul li:not(.not)').click(function () {
        $(this).addClass('slct').siblings().removeClass('slct');
        //var text=$(this).find('i').text();
        //$('.active h1 b').text(text);
        $('.active h2').show();
        $('.active ul li.not input').fadeOut();
    })
    $('.active ul li.not').click(function () {
        $(this).find('input').show().select();
    });
    $('.active ul li:not(.other)').click(function () {
        times = $(".swiper-slide-active h4 span").html();
        times = times.replace(" ", "");
        times = parseInt(times);

        var text = $(this).find('i').text();
        // var money = $("#input_money").val();
        // var bili = set_profit(times);
        // $('.active h1 b').text(text);
        // var yuqi = 0;
        // var yuqi = text * bili;
        // yuqi = parseFloat(yuqi) + parseFloat(text);
        // $('.active h1 i').html('预期收益：<span>' + yuqi + '</span>元');
        set_shouyi();

        vue.buy_money = parseFloat(text);
        //$('.active h2').hide();
    });
    function set_order_message() {
        $("#flow_span_dir").text("");
        $("#flow_span").text("");
        $("#flow_span_value").text("");
        $("#flow_span_mount").text("");
        $("#flow_span_time").text("");
        $("#flow_span_profit").text("");

        if (order_dirt == 0) {
            $("#flow_span_dir").html("<font color=green>买跌</font>");
            $("#flow_span_dir1").html("<font color=green>买跌</font>");
        } else {
            $("#flow_span_dir").html("<font color=red>买涨</font>");
            $("#flow_span_dir1").html("<font color=red>买涨</font>");
        }

        var amount = jQuery("#deal_amount").val();
        //amount = amount.replace("金额￥ ", "");
        if (amount == 0) amount = default_order;

        $("#flow_span_mount").text("￥" + amount);
        //$("#flow_span_time").text(jQuery("#finish_time").text().trim());
        //var aaaa= $("#selectmenu4 ").val();
        //var times = $(".swiper-slide-active").attr("index");
        //var times = $(".swiper-slide-active").attr("index");
        times = $(".swiper-slide-active h4 span").html();
        times = times.replace(" ", "");
        times = parseInt(times);
        $("#flow_span_time").text(times + '秒');

        var expect_profit = amount * (1 + WINNERT);
        $("#flow_span_profit").text(expect_profit);
        $("#flow_span").text(get_cap_name());
    }

    //判断是否允许1分钟下单
    //
    //
    function get_cap_name() {
        var name = $("#optionname").html();
        return name;
    }

    function get_cap_key() {
        var key = $(".sw_active a").attr("type");
        return key;
    }

    $("#st_order").click(function () {
        $("#st_order").css("display", "none");
        $("#ct_order").css("display", "none");
        $("#msg_order").removeAttr("hidden");
        jQuery.ajax({
            url: "delay",
            dataType: 'JSON',
            async: true,
            error: function (data) {
                //无法获取信息则正常下单
                $("#submittrad").popup("close");
                deal_ajax(order_index, order_dirt);
                return 0;
            },
            success: function (data_t) {
                //json_data = eval(data_t);
                if (data_t['is_delay'] == 1) {
                    var order_timeHand = setInterval(function () {
                        $("#submittrad").popup("close");
                        deal_ajax(order_dirt);
                        clearInterval(order_timeHand);
                    }, 1000);
                } else {
                    $("#submittrad").popup("close");
                    deal_ajax(order_dirt);
                }
                return 0;
            }
        });
    });
    //下单发送ajax
    function deal_ajax(dirc) {

        var deal_amount; //下单金额
        var captial; //资产种类
        var captial_id;
        var product; //资产
        var finish_time; //到期时间
        var dircetion; //方向
        var deal_value; //下单点位
        var yield1; //收益率
        var expect_profit; //收益值
        var faild_profit; //保底值
        var couponId=0; // 券id
        var coupon_count=0; // 券数目
        var msg;
        //获取收益率
        yield1 = $("#yield_winner").html();
        //expect_profit = $("#winner_rate").html();
        //expect_profit = expect_profit.replace("￥", ""); //去除￥
        faild_profit = $("#failer_rate").html();
        //faild_profit = faild_profit.replace("￥", ""); //去除￥
        var deal_amount = $(".slct p i").text();
        if (deal_amount == '') {
            deal_amount = $("#input_money").val();
        }
        if (!deal_amount) {
            deal_amount = default_order;
        } else if (deal_amount < default_order) { //最低下单金额限制
            showLoading('下单金额过低，无法交易！', 2000);
            pop_Close();
            return 0;
        }
        expect_profit = deal_amount * (1 + WINNERT);
        captial = get_cap_key();

        //deal_value = $("#nowpotis").text();
        deal_value = $("#flow_span_value").text();

        // 检查是否使用券
        if(vue.ticket_list != null && vue.ticket_list.length > 0){
            if(vue.ticket_index != null && vue.ticket_index >= 0){
                couponId = vue.ticket_list[vue.ticket_index].id;
                coupon_count = vue.ticket_number;
            }

        }
        finish_time = $("#flow_span_time").text();
        deal_time = parseInt(finish_time);
        jQuery.ajax({
            type: 'post',
            url: ROOT + "/Wap/Trading/deal_dq",
            data: "amount=" + deal_amount + "&trade_time=" + deal_time + "&capital=" + captial + "&product="
                    + deal_product + "&dircetion=" + dirc + "&sec=" + finish_time + "&deal_value=" + deal_value
                    + "&yield=" + yield1 +"&expect_profit=" + expect_profit + "&faild_profit=" + faild_profit
                    + "&couponId=" + couponId + "&coupon_count=" + coupon_count,
            dataType: 'JSON',
            async: true,
            //timeout: 900,
            error: function (data) {
                showLoading('下单失败！', 2000);
                pop_Close();
                return 0;
            },
            success: function (data_t) {
                //var json_data = eval('(' + data_t + ')');
                var json_data = eval(data_t);
                if (json_data['type'] == 'success') {
                    var  new_order_id = json_data['id'];
                    current_order_id=new_order_id;
                    var deal_value = json_data['deal_value'];
                    $("#buy_price").text(deal_value);
                    $("#buy_price").text(deal_value);
                    new_order_price = deal_value;
                    if (json_data['direction'] == 0) {
                        $("#flow_span_dir1").html('<font color="green">买跌</font>');
                    } else {
                        $("#flow_span_dir1").html('<font color="red">买涨</font>');
                    }
                    $('#buildConfirm').show();
                    var now_time = new Date().getTime();
                    var daoqi_time = deal_time * 1000;
                    var endTime = parseInt(now_time) + parseInt(daoqi_time);
                    $('.sec').text(deal_time);
                    setTimeout('countDown(' + endTime + ',' + deal_time  + ',' + new_order_id +')', 1000);
                    $("#money").html(json_data['usermoney']);
                    $("#minijin").html(json_data['sim_money']);
                    var dot_len = get_data_space(captial);
                    e_price = parseFloat(json_data['e_price']).toFixed(dot_len);
                    fate = parseFloat(json_data['fate']).toFixed(dot_len);
                    e_is = json_data['e_is'];
                    //$("#money").text(parseFloat($("#money").text())-parseFloat(deal_amount));
                    //$("#minijin").text(parseFloat($("#minijin").text())-parseFloat(deal_amount));
                    load_now(1, 15, captial, 1);
                } else if (json_data['type'] == 'error') {
                    //alert(json_data['error']);
                    showLoading(json_data['error'], 2000);
                    pop_Close();
                }
                return 0;
            }
        });

    }

    Date.prototype.format = function (format) {
        var o = {
            "M+": this.getMonth() + 1,
            // month
            "d+": this.getDate(),
            // day
            "h+": this.getHours(),
            // hour
            "m+": this.getMinutes(),
            // minute
            "s+": this.getSeconds(),
            // second
            "q+": Math.floor((this.getMonth() + 3) / 3),
            // quarter
            "S": this.getMilliseconds()
            // millisecond
        };

        if (/(y+)/.test(format)) {
            format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        }

        for (var k in o) {
            if (new RegExp("(" + k + ")").test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
            }
        }
        return format;
    };
    function change_time(time_xt) {
        //var now = new Date(time_xt * 1000).format("yyyy-MM-dd hh:mm:ss");
        var now = new Date(time_xt * 1000).format("hh:mm");
        return now;
    }

    //根据deal_time选择设置时间间隔
    function set_dif_time() {
        if (deal_time <= 0) {
            return;
        }

        var deal_time_t = deal_time + 300;
        var time_one = Math.ceil(deal_time_t / 900);
        time_one = time_one * 900;
        time_limit = time_one;
        var inx = 4;
        $("#selectmenu4").empty();
        while (inx > 0) {
            var time_txt = change_time(time_one);
            //if (inx == 3) {
            //	$(".main_M_S .ui-block-b .ui-select .ui-btn-text").text(time_txt);
            //}
            if (inx == 3) {
                $("#selectmenu4").append("<option value=" + time_one + " selected='selected'>" + time_txt + "</option>");
            } else {
                $("#selectmenu4").append("<option value=" + time_one + ">" + time_txt + "</option>");
            }
            //$('#selectmenu4').selectmenu('refresh', true);
            time_one = time_one + 900;
        }
    }

    function getTime() {
        $.ajaxSettings.async = false;
        $.getJSON(ROOT + '/mobile.php/Mobile/get_time',
            function (data) {
                deal_time = data;
            }
        );
    }


    //选择 1min 5min 或 15min等
    /*$("#time_diff li a").click(function(){
     return false;
     //如果选择时间间隔，就中断原有更新函数；
     var str = $(this).attr('type');
     $("#time_diff li a").removeClass("changed");
     $(this).addClass("changed");
     diff_one = str;
     clearInterval(chgsta);
     chgsta = window.setInterval(function() {draw(diff_one,capital_one);}, inteval_time);
     });*/
    function draw(inx, capital_key) {
        return false;
        $.ajax({
            type: 'post',
            url: ROOT + '/mobile.php/Mobile/getJqplot/inx/' + inx + '/captype/' + capital_key + '',
            data: 'diff=' + diff_one,
            //data : {inx:inx,capital_key:capital_key},
            dataType: 'JSON',
            async: true,
            timeout: 900,
            success: function (data, status, xhr) {
                var str = data['data'];
                var json_str = eval(str);
                var t_max = data['max'];
                var t_min = data['min'];
                var t_current = data['last'];
                var t_open = data['last_open'];
                var str_capital = data['capital_type'];
                var capital_dot = data['capital_dot'];
                //  alert(capital_dot);

                //deal_time = data['time_t'];
                var base_cursor_top = 10;
                var jqPlot_height = 210;
                var beijingtime = xhr.getResponseHeader('Date');
                var curDate = new Date(beijingtime);
                var shijian = curDate.getHours() + ":" + curDate.getMinutes() + ":" + curDate.getSeconds();
                $('.zuoshou_').text(shijian);
                $('#now_time').text('当前:' + shijian);
                var fix_t = 0;

                //if (ed != 0 && ed != null) {
                //    t_current = get_data_cur(ed, t_current);
                //    console.log(t_current);
                //}
                draw_highchart(0, fix_t, t_open, t_current, t_min, t_max, str, str_capital, data['min_time'], data['max_time'], capital_dot);
                //$('.zuoshou_').text(data['zuoshou_']);
                $('.height_').text(t_max);
                $('.jinkai_').text(t_open);
                $('.low_').text(t_min);
                if (t_current > t_open) {
                    $('#now_price').removeClass('drop');
                    $('#now_price').addClass('rise');
                } else if (t_current == t_open) {
                    $('#now_price').removeClass('rise');
                    $('#now_price').removeClass('drop');
                } else {
                    $('#now_price').removeClass('rise');
                    $('#now_price').addClass('drop');
                }

                if ((new_order_price > t_current && order_dirt == 0) || (new_order_price < t_current && order_dirt == 1)) {
                    $('#flow_span_value2').html('<font color=red>盈</font>');
                } else if ((new_order_price < t_current && order_dirt == 0) || (new_order_price > t_current && order_dirt == 1)) {
                    $('#flow_span_value2').html('<font color=green>亏</font>');
                } else {
                    $('#flow_span_value2').html('平局');
                }
                $('#now_price').text(t_current);
                $("#now_list .now_price").text(t_current);
                //deal_time = data['time_t'];
                //getTime();//获取系统时间
                //set_dif_time();
            },
            error: function (data) {
                //alert("获取数据失败");
            }
        });
    }

    function draw_highchart(index, fix_t, t_prelast, t_current, t_min, t_max, json_str, str_capital, min_time, max_time, capital_dot) {
        if (is_Close == "1") {
            window.clearInterval(data_setInt);
            return false;
        }
        if (index == 1) {
            var elementId = "#chartdiv2";
            if ($(elementId)) {
                $(elementId).unbind();
                // for iexplorer
                $(elementId).empty();
            }
        }
        else {
            var elementId = "#chartdiv";
            if ($(elementId)) {
                $(elementId).unbind();
                // for iexplorer
                $(elementId).empty();
            }
        }


        //var t_max = data['max'];
        //var t_min = data['min'];
        var max_time = json_str['max_time'];
        var min_time = json_str['min_time'];
        var max = json_str['max'];
        var min = json_str['min'];
        // alert(max_time);

        var plot2 = "#chartdiv";
        var str_div = 'chartdiv';
        if (index == 1) {
            plot2 = '#chartdiv2';
            str_div = 'chartdiv2';
        }

        if ($(plot2)) {
            $(plot2).unbind();
            $(plot2).empty();
        }

        //highchart设置
        var data = eval(json_str);
        //alert(json_str);
        var ohlc = [], ave5 = [], ave10 = [], ave30 = [], dataLength = data.length,
            // set the allowed units for data grouping
            groupingUnits = [[
                'week',
                [1]
            ], [
                'month',
                [1, 2, 3, 4, 6]
            ]],

            i = 0;

        for (i; i < dataLength; i += 1) {

            ohlc.push([
                data[i][0], // the date
                data[i][1], // open
                data[i][2], // high
                data[i][3], // low
                data[i][4] // close
            ]);
            /*ave5.push([
             data[i][0],
             data[i][1]
             ]);
             ave10.push([
             data[i][0],
             data[i][2]
             ]);
             ave30.push([
             data[i][0],
             data[i][3]
             ]);*/
        }
        Highcharts.setOptions({

            global: {
                useUTC: false//时区设置
            },

            credits: {
                enabled: false//去掉右下角的标志
            },

            zoom: {
                enabled: false//去掉右下角的标志
            },

            animation: {
                enabled: true//去掉动画
            },
            exporting: {
                enabled: false//去掉截图
            }
        });


        //alert(t_max);
        // create the chart
        //	$(plot2).highcharts('StockChart', {
        var options = {
            xAxis: {
                gridLineWidth: 0.3,
                gridLineColor: '#d0d0d3',
                tickPixelInterval: 80,
                ordinal: false,
                min: min_time,
                max: max_time,
                showLastLabel: true,
                labels: {
                    align: 'top',
                    y: 13,
                    x: -17,
                }
            },
            yAxis: {
                gridLineWidth: 0.3,
                allowDecimals: true,
                gridLineColor: '#d0d0d3',
                min: t_min,
                max: t_max,
                showLastLabel: true,
                tickPixelInterval: 50,
                labels: {
                    align: 'left',
                    x: 8,
                    y: 5,
                    formatter: function () {
                        // alert(this.value);
                        return this.value.toFixed(capital_dot);
                    }
                },
            },
            chart: {
                margin: [10, 60, 15, 10],
                plotBorderWidth: 1,
                backgroundColor: '#f8f8f8',
                plotBorderColor: '#c6e3ec',
                renderTo: str_div,
                plotShadow: true,
                //borderWidth:1,

            },
            rangeSelector: {
                enabled: false
            },
            navigator: {
                enabled: false
            },
            scrollbar: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            series: [{
                name: str_capital,
                data: data,
                type: 'candlestick',
                threshold: null,
                //	fillColor: '#fff',
            }],
            plotOptions: {
                //修改蜡烛颜色
                candlestick: {
                    color: '#33AA11',
                    upColor: '#DD2200',
                    lineColor: '#33AA11',
                    upLineColor: '#DD2200',
                    maker: {
                        states: {
                            hover: {
                                enabled: true,
                            }
                        }
                    }
                },
                line: {
                    lineWidth: 1,
                    color: '#000',
                    dataGrouping: {
                        enabled: false
                    },
                    allowPointSelect: true,
                    marker: {
                        states: {
                            hover: {
                                radius: 2
                            }
                        }
                    },

                },
                area: {
                    lineWidth: 1,
                    color: '#000',
                    dataGrouping: {
                        enabled: false
                    },
                    allowPointSelect: false,
                    marker: {
                        states: {
                            hover: {
                                radius: 2
                            }
                        }
                    },
                    events: {
                        click: function (event) {
                        }
                    }
                },
                navigator: {enabled: true},
                series: [{
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1,
                        }
                    }
                }
                ]
            },
            tooltip: {
                crosshairs: [true, true],
            }
        };

        var chart1 = new Highcharts.StockChart(options);

        var base_cursor_top = 10;
        var jqPlot_height = 155;

        fix_t = base_cursor_top + jqPlot_height / (chart1.yAxis[0].max - chart1.yAxis[0].min) * (chart1.yAxis[0].max - t_current);

        //移动线和框的设置
        if (index == 1) {

            document.getElementById("data_cursor2").style.top = fix_t + "px";

            if (t_prelast < t_current) {
                $("#move_line_data2").css("background", "#f00");
                $("#move_line_data2").css("color", "#fff");
                $("#data_cursor2").css("borderTopColor", "#f00");
            } else if (t_prelast > t_current) {
                $("#move_line_data2").css("background", "#22bb22");
                $("#move_line_data2").css("color", "#fff");
                $("#data_cursor2").css("borderTopColor", "#22bb22");
            }
            $("#move_line_data2").text(t_current);
            jQuery(".cur_value").eq(1).html(t_current);
        } else if (index == 0) {

            document.getElementById("data_cursor").style.top = fix_t + "px";
            if (t_prelast < t_current) {
                $("#move_line_data").css("background", "#f00");
                $("#move_line_data").css("color", "#fff");
                $("#data_cursor").css("borderTopColor", "#f00");
            } else if (t_prelast > t_current) {
                $("#move_line_data").css("background", "#22bb22");
                $("#move_line_data").css("color", "#fff");
                $("#data_cursor").css("borderTopColor", "#22bb22");
            }
            $("#move_line_data").text(t_current);
            var preone = t_current.slice(0, t_current.length - 3);
            var pretwo = t_current.slice(t_current.length - 3, t_current.length);

            var patt1 = new RegExp("/#f00/");

            /*if(patt1.test(st)){
             $('#nowpotis').css('color','#f00');
             } else {
             $('#nowpotis').css('color','#22bb22');
             }*/
            var fontcolor = $("#data_cursor").css("border-top-color");
            $('#nowpotis').css('color', fontcolor);
            $('#nowpotis').html(preone + "<span>" + pretwo + "</span>");
            if (t_current > t_prelast) {
                $("#flow_span_value").html('<font color=red>' + t_current + '</font>');
                $("#flow_span_value1").html('<font color=red>' + t_current + '</font>');
            } else if (t_current < t_prelast) {
                $("#flow_span_value").html('<font color=green>' + t_current + '</font>');
                $("#flow_span_value1").html('<font color=green>' + t_current + '</font>');
            } else {
                $("#flow_span_value").html(t_current);
                $("#flow_span_value1").html(t_current);
            }
            //$('#flow_span_value').text(t_current);
            //	alert($("#move_line_data").css("background"));
            jQuery(".cur_value").eq(0).html(t_current);
        }
    }

    function setdata() {
        window.clearInterval(data_setInt_option);
        //data_setInt_option = window.setInterval(function() {getOption_type(deal_product);}, inteval_time);
        window.clearInterval(data_setInt);
        draw(1, capital_one);
        data_setInt = window.setInterval(function () {
            draw(1, capital_one);
        }, inteval_time);
    }

    getOption_type(deal_product);
    //setdata();
    //window.setInterval(get_newallorder, 3000);
    get_newallorder();

    var _wrap = $('#depth_buy_context');
    var _interval = 3000;
    var _moving;
    var gundong_num = 1;
    _wrap.hover(function () {
        clearInterval(_moving);
    }, function () {
        _moving = setInterval(function () {
            var field = _wrap.find('li:last');
            var h = field.height();
            _wrap.prepend(field.css('marginTop', -h + 'px'));
            field.animate({'marginTop': 0}, 300);
            gundong_num++;
            if (gundong_num == 15) {
                $("#depth_buy_context").html('');
                get_newallorder();
                gundong_num = 1;
                //$('#me').trigger('click');
            }
        }, _interval)
    }).trigger('mouseleave');
//广告轮播
    new Swiper('#ad', {
        //paginationClickable: true,
        autoplay: 5000,
        loop: true,
        direction: 'horizontal',
        initialSlide: 0,
        effect: 'slide',
    });
//广告轮播
    new Swiper('#options', {
        paginationClickable: true,
        centeredSlides: true,
        slidesPerView: 2,
        watchActiveIndex: true,
    });
// //关闭广告
// $(".ad-close").on("click",function(e){
// 	$("#ad").hide();
// 	e.stopPropagation();
// });
//首页tab切换
    $("body").delegate(".info-nav li", "click", function () {
        $(this).parent().find('a').removeClass('selected');
        $(this).find('a').addClass('selected');
        var index = $('.info-nav li').index(this);
        $(this).parent().nextAll('.info-d').hide();
        $('.info-d').eq(index).show();
    });
    //关闭弹窗
    $(".back").on("click", function (e) {
        var p_btm = $("#buildConfirm,.mask");
        p_btm.hide();
        e.preventDefault();
    });
    $("#history").on("click", function (e) {
        $("#all_list").html('');
        load_history(1, 15, 0, 1);
    });
    $("#orders").on("click", function (e) {
        $("#now_list").html('');
        var zichan = $(".sw_active a").attr("type");
        load_now(1, 15, zichan, 1);
    });
    $(".come-txt1").on("click", function (e) {
        jump(ROOT + '/Wap/User/history');
    });
    $("#newodrers").on("click", function (e) {
        $("#depth_buy_context").html('');
        get_newallorder();
    });
});

function set_profit(valu_t) {
    if (valu_t == 60) {
        return 0.85;
    } else if (valu_t == 180) {
        return 0.80;
    } else if (valu_t == 300) {
        return 0.75;
    }
}

function set_shouyi(){
    times = $(".swiper-slide-active h4 span").html();
    times = times.replace(" ", "");
    times = parseInt(times);

    var money = $(".slct p i").text();
    if (money == '') {
        money = $("#input_money").val();
    }
    var bili = set_profit(times);
    $("#input_money").val(money);
    $('.active h1 b').text(money);
    var yuqi = 0;
    var yuqi = money * bili;
    // 这里检查是否使用了券，如果使用了增益券，价格需要增加
    yuqi = yuqi * vue.get_bili();

    yuqi = parseFloat(yuqi) + parseFloat(money);
    yuqi = Math.floor(yuqi);
    $('.active h1 i').html('预期收益：<span>' + yuqi + '</span>元');
}
function load_history(num, PAGESIZE, listtype, isnull) {
    $.ajax({
        type: "POST",
        url: ROOT + "/Wap/user/history_refresh",
        data: "page=" + num + "&pagesize=" + PAGESIZE + "&type=" + listtype,
        datatype: 'JSON',
        async: true,
        success: function (response) {
            $(".more-btn1").addClass("hide");
            $("#all_list").removeClass("hide");
            $("#all_list").empty();
            if (response.status == 1) {
                $("#all_list").append('<tr class="rec-tr"><th class="rec-th">资产类型</th><th class="rec-th">涨/跌</th><th class="rec-th">到期时间</th><th class="rec-th">买入金额</th><th class="rec-th">盈利情况</th> <th class="rec-th">订单状态</th> </tr>');
                $.each(response.list, function (key, value) {
                    $("#all_list").append('<tr class="rec-tr" onclick="get_history(\'' + value.id + '\',this)" id="' + value.id + '"><td class="rec-td">' + value.option_key + '</td><td class="rec-td">' + value.trade_direction + '</td><td class="rec-td fcgray3">' + value.trade_time + '</td><td class="rec-td fcgray3">' + value.trade_amount + '</td><td class="rec-td fcgray3">' + value.profit + '</td><td class="rec-td fcgray3">' + value.is_win + '</td></tr>');
                });
                $(".come-txt1").text("查看更多历史记录");
            } else {
                if (num == 1) {
                    $("#all_list").addClass("hide");
                    $(".come-txt1").text("您还没有任何记录");
                    $(".com-empty1").removeClass("hide");
                }
                return false;
            }
        },
        error: function () {
        },
        complete: function () {
            //hideLoading()
        }
        //  "json");
    });
}

function load_now(num, PAGESIZE, listtype, isnull) {
    $.ajax({
        type: "POST",
        url: ROOT + "/Wap/Trading/get_now_orders",
        data: "page=" + num + "&pagesize=" + PAGESIZE + "&type=" + listtype,
        datatype: 'JSON',
        success: function (response) {
            $(".more-btn").addClass("hide");
            $("#now_list").removeClass("hide");
            $("#now_list").empty();
            if (response.status == 1) {
                $("#now_list").append('<tr class="rec-tr"><th class="rec-th">下单时间</th><th class="rec-th">资产类型</th><th class="rec-th">买入方向</th><th class="rec-th">执行价格</th><th class="rec-th">当前价格</th><th class="rec-th">订单状态</th></tr>');
                $.each(response.list, function (key, value) {
                    $("#now_list").append('<tr class="rec-tr" onclick="get_history(\'' + value.id + '\',this)" id="' + value.id + '"><td class="rec-td fcgray3">' + value.trade_time + '</td><td class="rec-td">' + value.option_key + '</td><td class="rec-td">' + value.trade_direction + '</td><td class="rec-td fcgray3">' + value.trade_point + '</td><td class="rec-td fcgray3">' + value.is_settle + '</td></tr>');
                    $(".come-txt").text("");
                });
            } else {
                if (num == 1) {
                    $("#now_list").addClass("hide");
                    $(".come-txt").text("当前没有任何记录");
                    $(".com-empty").removeClass("hide");
                }
                return false;
            }
        },
        error: function () {
        },
        complete: function () {
            //hideLoading()
        }
        //  "json");
    });
}
var end_datas = new Array();
function getDecimalPoint(capitalType, price) {
    var capital_space_percent = price * 100000;
    if (capitalType == 'USDJPY'|| capitalType == "HJRMB"|| capitalType == "BYRMB") {
        capital_space_percent = price * 1000;
    } else if (capitalType == 'GOLD' || capitalType == 'USOIL' || capitalType == 'SILVER' || capitalType == 'XAUUSD' || capitalType == 'BTCCNY') {
        capital_space_percent = price * 100;
    } else if (capitalType == 'AUDJPY' || capitalType == "USAAPL" || capitalType == 'CADJPY' || capitalType == 'EURJPY' || capitalType == "XAGUSD") {
        capital_space_percent = price * 1000;
    } else if (capitalType == "CHINAA50" || capitalType == "HKG33") {
        capital_space_percent = price;
    }
    if (price == 0) {
        capital_space_percent = 0;
    }
    return capital_space_percent;
}
function set_float_price(capitalType, price) {
    var capital_space_percent = price / 100000;
    if (capitalType == 'USDJPY'|| capitalType == "HJRMB"|| capitalType == "BYRMB") {
        capital_space_percent = price / 1000;
    } else if (capitalType == 'GOLD' || capitalType == 'USOIL' || capitalType == 'SILVER' || capitalType == 'XAUUSD' || capitalType == 'BTCCNY') {
        capital_space_percent = price / 100;
    } else if (capitalType == 'AUDJPY' || capitalType == "USAAPL" || capitalType == 'CADJPY' || capitalType == 'EURJPY' || capitalType == "XAGUSD") {
        capital_space_percent = price / 1000;
    } else if (capitalType == "CHINAA50" || capitalType == "HKG33") {
        capital_space_percent = price;
    }
    return capital_space_percent;
}
function set_Kline_end(captial, e_price, diffrant, dot_len, order_dirt, cishu,orderid) {
    var type = $("#time_diff a.changed").attr("type");
    var diff = type * 60;
    //clearInterval(window.param[captial+diff]);
    var new_price_str = $("#now_price").text();
    //var new_price_str  ='3830.00';
    var now_price_1 = parseFloat(new_price_str).toFixed(dot_len);
    if (order_dirt == 1) {
        var new_pri1 = getDecimalPoint(captial, now_price_1) - end_datas[cishu];
        new_pri = parseFloat(set_float_price(captial, new_pri1)).toFixed(dot_len);
    } else {
        var new_pri1 = getDecimalPoint(captial, now_price_1) + end_datas[cishu];
        new_pri = parseFloat(set_float_price(captial, new_pri1)).toFixed(dot_len);
    }
    console.log(new_pri);
    if(current_order_id==orderid) {
        $("#fnTimeCountDown").text('结算中,请稍等...');
        if ((new_order_price > new_pri && order_dirt == 0) || (new_order_price < new_pri && order_dirt == 1)) {
            $('#flow_span_value2').html('<font color=red>盈</font>');
        } else if ((new_order_price < new_pri && order_dirt == 0) || (new_order_price > new_pri && order_dirt == 1)) {
            $('#flow_span_value2').html('<font color=green>亏</font>');
        } else {
            $('#flow_span_value2').html('平局');
        }
    }
    var kline = window.param.Kline;
    var datas = new Array();
    datas[0] = Math.round(new Date().getTime() / 1000);
    var high = parseFloat($("#height_").text()).toFixed(dot_len);
    var low = parseFloat($("#low_").text()).toFixed(dot_len);
    datas[1] = new_pri;
    datas[2] = new_pri;
    datas[5] = 0;
    datas[6] = 0;
    datas[7] = 0;
    datas[8] = 0;
    datas[9] = 0;
    datas[10] = 0;
    datas[11] = 0;
    datas[12] = 0;
    $('#now_price').html(new_pri);
    kline.modifyLastCandle(datas, diff);
}
function GetRandomNum(Min, Max) {
    var Range = Max - Min;
    var Rand = Math.random();
    return (Min + Math.round(Rand * Range));
}
function selectfrom(capitalType, price, diffrant, dot) {
    var big_price = diffrant;
    console.log(diffrant);
    //1.56
    if (diffrant >= 1) {
        var big_price = diffrant * getDecimalPoint(capitalType, diffrant);
    } else if (big_price.toString().indexOf('.') > 0) {
        var big_price = parseInt(big_price.toString().slice(-dot));
    } else {
        var big_price = 0;
    }
    var one_price = GetRandomNum(1, big_price);
    var two_price = big_price - one_price;
    var two_price = GetRandomNum(1, two_price);
    var three_price = parseInt(big_price - two_price - one_price);
    if (one_price < 0) {
        one_price = 0;
    }
    if (two_price < 0) {
        two_price = 0;
    }
    if (three_price < 0) {
        three_price = 0;
    }
    var datas = new Array();
    datas[0] = one_price;
    datas[1] = two_price;
    datas[2] = three_price;
    console.log(datas);
    return datas;
}

function get_r_second(){
    if(rsecond == 0){
        rsecond = Math.floor(Math.random() * 5  + 3);
    }
    return rsecond;
}

function countDown(endTime, dealtime,orderid) {

    //var endtime = new Date(endTime).getTime(); //取结束日期(毫秒值)
    var nowtime = new Date().getTime();
    var youtime = endTime - nowtime;
    var seconds = youtime / 1000;
    var CSecond = Math.floor(seconds % dealtime).toString(); //"%"是取余运算，可以理解为60进一后取余数，然后只要余数。
    var CMSecond = Math.floor(seconds * 100 % 100).toString();
    if (CSecond.length == 1) {
        CSecond = '0' + CSecond;
    }

    if (CMSecond.length == 1) {
        CMSecond = '0' + CMSecond;
    }

    // if (CSecond == get_r_second() && (window.ed == 0 || window.ed == null)) {
    //     console.log("rsecond:" + get_r_second());
    //     get_order(orderid);
    // }
    if (CSecond == 3) {
        var captial = $(".sw_active a").attr("type");
        var dot_len = get_data_space(captial);
        var now_price_1 = parseFloat($("#now_price").text()).toFixed(dot_len);
        var diffrant = parseFloat(Math.abs(e_price - now_price_1)).toFixed(dot_len);
        var type = $("#time_diff a.changed").attr("type");
        var diff = type * 60;
        console.log(diffrant);
        console.log(e_is);
        if ((order_dirt == 0 && now_price_1 <= new_order_price && !isNaN(diffrant) && diffrant <= fate && e_is && shifoukaishi !== 1) || (order_dirt == 1 && now_price_1 >= new_order_price && !isNaN(diffrant) && diffrant <= fate && e_is && shifoukaishi !== 1)) {
            end_datas = selectfrom(captial, e_price, diffrant, dot_len);
            console.log(1);
            set_Kline_end(captial, e_price, diffrant, dot_len, order_dirt, 0,orderid);
            shifoukaishi = 1;
        } else {
            //clearInterval(window.param[captial+diff]);
        }
    }
    if (CSecond == 2) {
        var captial = $(".sw_active a").attr("type");
        var dot_len = get_data_space(captial);
        var now_price_1 = parseFloat($("#now_price").text()).toFixed(dot_len);
        var diffrant = parseFloat(Math.abs(e_price - now_price_1).toFixed(dot_len));
        var type = $("#time_diff a.changed").attr("type");
        var diff = type * 60;
        if (shifoukaishi > 0 && shifoukaishi !== 2) {
            console.log(2);
            set_Kline_end(captial, e_price, diffrant, dot_len, order_dirt, 2,orderid);
            shifoukaishi = 2;
        }
    }
    if (CSecond == 1) {
        var captial = $(".sw_active a").attr("type");
        var dot_len = get_data_space(captial);
        var now_price_1 = parseFloat($("#now_price").text()).toFixed(dot_len);
        var diffrant = parseFloat(Math.abs(e_price - now_price_1).toFixed(dot_len));
        var type = $("#time_diff a.changed").attr("type");
        var diff = type * 60;
        if (shifoukaishi > 0 && shifoukaishi == 2) {
            console.log(3);
            shifoukaishi = 0;
            set_Kline_end(captial, e_price, diffrant, dot_len, order_dirt, 1,orderid);
        }
    }
    if (CSecond <= 0) {
        //$(".sec").text('00');
        //get_order(new_order_id);
        get_order(orderid, set_data);
        //  if(ed == null || ed == 0){
        //      get_order(orderid, set_data);
        //  }
        // else{
        //      set_data(ed,orderid);
        //      cishu = 0;
        //  }

        //clearInterval(end_time_Interval);
        //setTimeout(function () {drawKline_end(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "deal_value");}, 2000);
    }
    else {
        if(current_order_id==orderid)
        {
            $(".sec").text(CSecond);
        }
        setTimeout('countDown(' + endTime + ',' + dealtime + ',' + orderid +')', 1000);
    }
}
function set_daoqi(json_data,orderid) {
    //$("#fnTimeCountDown").css("font-size",'2em');
    setTimeout(function () {
        if (current_order_id == orderid) {
            $("#fnTimeCountDown").text('结算中,请稍等...');
            var dot_len = get_data_space(json_data['option_key']);
            if (json_data['is_win'] == '1') {
                $("#fnTimeCountDown").html('<font color=red>盈 +' + json_data['profit'] + '</font>');
            } else if (json_data['is_win'] == '2') {
                $("#fnTimeCountDown").html('<font color=black>平局 +0' + '</font>');
            } else {
                $("#fnTimeCountDown").html('<font color=green>亏 ' + json_data['profit'] + '</font>');
            }
            $("#dangqian").hide();
            $("#daoqi").show();
            $("#flow_span_daoqi").show();
            $("#flow_span_daoqi").html(parseFloat(json_data['settle_point']).toFixed(dot_len));
            $("#dangqian").html('当前价格：<i id="flow_span_value1">' + parseFloat(json_data['settle_point']).toFixed(dot_len) + '</i>');
        }
    }, 300);
}
var json_data = '';
function get_order(id, callback) {
    var type = $("#time_diff a.changed").attr("type");
    jQuery.ajax({
        type: 'post',
        url: ROOT + "/Wap/Trading/get_history_ajax",
        data: "id=" + id + "&type=" + type + '&deal_value=' + $("#flow_span_value").html(),
        dataType: 'JSON',
        async: true,
        timeout: 2000,
        error: function (data) {
            get_order(id);
            $("#flow_span_value2").hide();
            /* showLoading('网络错误，请在历史订单中查看！',2000);
             pop_Close();*/
            return 0;
        },
        success: function (data_t) {
            json_data = eval(data_t);

            if (json_data['status'] == '1') {
                ed = json_data;             // new add
                window.ed.id = id;
                window.ed.type = type;
                console.log(json_data);
                if(json_data["is_win"] == 1){
                    console.log("win:" + parseFloat(json_data['settle_point']).toFixed(2));
                }
                else if(json_data["is_win"] == 2){
                    console.log("ping:" + parseFloat(json_data['settle_point']).toFixed(2));
                }
                else{
                    console.log("lose:" + parseFloat(json_data['settle_point']).toFixed(2));
                }
                if(callback != null){
                    callback(ed,id);
                }
            } else {
                get_order(id);
            }
            return 0;
        }
    });
}

function set_data(json_data,orderid) {
    var dot_len = get_data_space(json_data['option_key']);
    if(current_order_id==orderid) {
        $("#flow_span_value2").hide();
        $("#flow_span_value2").hide();
        $("#dangqian").html('当前价格：<i id="flow_span_value_d">' + parseFloat(json_data['settle_point']).toFixed(dot_len) + '</i>');
        $("#flow_span_value3").show();
        $("#fnTimeCountDown").text('结算中,请稍等...');
        if (json_data['is_win'] == '1') {
            $("#flow_span_value3").html('<font color=red>盈' + '</font>');
        } else if (json_data['is_win'] == '2') {
            $("#flow_span_value3").html('<font color=black>平' + '</font>');
        } else {
            $("#flow_span_value3").html('<font color=green>亏</font>');
        }
        var text = $("#" + json_data.id + " span[class='select_time']").text();
        $("#money").text(parseFloat(json_data['money']).toFixed(2));
        $("#minijin").text(parseFloat(json_data['sim_money']).toFixed(2));

    }
    var kline = window.param.Kline;
    var datas = new Array();
    datas[0] = json_data['Klinetime'];
    datas[1] = parseFloat(json_data['close']).toFixed(dot_len);
    datas[2] = parseFloat(json_data['open']).toFixed(dot_len);
    datas[3] = parseFloat(json_data['high']).toFixed(dot_len);
    datas[4] = parseFloat(json_data['low']).toFixed(dot_len);
    datas[5] = 0;
    datas[6] = 0;
    datas[7] = 0;
    datas[8] = 0;
    datas[9] = 0;
    datas[10] = 0;
    datas[11] = 0;
    datas[12] = 0;
    var diff = json_data.type * 60;
    //setTimeout('set_daoqi(' + json_data + ',' + orderid  +')', 500);
    set_daoqi(json_data,orderid);
    kline.modifyLastCandle(datas, diff);
    json_data = null;
    window.ed = null;
    rsecond = 0;

}

function get_data_cur(json_data, t_current) {
    if (json_data == 0 || json_data == null)
        return t_current;
    var result = t_current;
    var dot_len = get_data_space(json_data['option_key']);
    var point = parseFloat(parseFloat(json_data['trade_point']).toFixed(dot_len));
    //var ran = parseFloat((Math.random() * 2).toFixed(dot_len));
    var ran = get_random(json_data['option_key'], dot_len);
    var trade_dir = json_data['trade_direction'];
    if (json_data['is_win'] == '1') {
        if(trade_dir == '1'){
            if (t_current > point) {
                result = t_current;
            }
            else {
                result =  point + ran;
            }
        }
        else{
            if (t_current < point) {
                result = t_current;
            }
            else {
                result = point -  ran;
            }

        }
    } else if (json_data['is_win'] == '2') {
        result = t_current;
    } else {
        if(trade_dir == '1'){
            if (t_current < point) {
                result = t_current;
            }
            else {
                result = point -  ran;
            }
        }
        else{
            if (t_current > point) {
                return t_current;
            }
            else {
                result =  point +  ran;
            }
        }
    }
    return parseFloat(result).toFixed(dot_len);
}

function get_random(capital_key,dot_len){
    var capital_space_percent = 2;

    if (capital_key == "USDJPY"|| capital_key == "HJRMB"|| capital_key == "BYRMB") {
        capital_space_percent = 0.001;
    } else if (capital_key == "XAUUSD" || capital_key == "SILVER" || capital_key == "USOIL" || capital_key == "BTCCNY") {
        capital_space_percent = 2;
    } else if (capital_key == 'CADJPY' || capital_key == 'EURJPY' || capital_key == 'XAGUSD' || capital_key == 'GBPUSD'
        || capital_key == 'EURUSD' || capital_key == 'HJRMB' || capitalHJRMBBYR == 'BYRMB') {
        capital_space_percent = 0.001;
    }

    return parseFloat((Math.random() * capital_space_percent).toFixed(dot_len));
}

window.get_data_cur = get_data_cur;
function get_data_space(capital_key) {
    var capital_space_percent = 5;

    if (capital_key == "USDJPY"|| capital_key == "HJRMB"|| capital_key == "BYRMB") {
        capital_space_percent = 3;
    } else if (capital_key == "XAUUSD" || capital_key == "SILVER" || capital_key == "USOIL" || capital_key == "BTCCNY") {
        capital_space_percent = 2;
    } else if (capital_key == 'USDJPY' || capital_key == 'CADJPY' || capital_key == 'EURJPY' || capital_key == 'XAGUSD') {
        capital_space_percent = 3;
    }
    return capital_space_percent;
}

function get_data_format(capital_key, data){
    var dotlen = get_data_space(capital_key);
    return parseFloat(data).toFixed(dot_len);
}


function get_current_seconds(){
    var date = new Date();
    return date.getHours() * 3600 + date.getMinutes() * 60 + date.getSeconds();
}

function set_pd(){
    var s = get_current_seconds();
    var p = Math.floor(s / 10) + 100;
    var d = p * 5 + Math.floor(Math.random()*10);
    var oldd = parseInt($("#trade_count").text());
    d = d > oldd ? d : oldd + 2;
    $("#renshu").text(p);
    $("#trade_count").text(d);
}
set_pd();
$(function(){
    set_pd();
    //交易数据
    setTimeout(set_pd, 12*1000);
    setTimeout(function(){
        $("#product_switch li a").first().trigger("click");
    }, 1000);

})


var overscroll = function(el) {
    // if(el == null){
    //     return false;
    // }
    el.addEventListener('touchstart', function() {
        var top = el.scrollTop
            , totalScroll = el.scrollHeight
            , currentScroll = top + el.offsetHeight;
        //If we're at the top or the bottom of the containers
        //scroll, push up or down one pixel.
        //
        //this prevents the scroll from "passing through" to
        //the body.
        if(top === 0) {
            el.scrollTop = 1;
        } else if(currentScroll === totalScroll) {
            el.scrollTop = top - 1;
        }
    });
    el.addEventListener('touchmove', function(evt) {
        //if the content is actually scrollable, i.e. the content is long enough
        //that scrolling can occur
        if(el.offsetHeight < el.scrollHeight)
            evt._isScroller = true;
    });
}
overscroll(document.querySelector('.scroll'));
document.body.addEventListener('touchmove', function(evt) {
    //In this case, the default behavior is scrolling the body, which
    //would result in an overflow.  Since we don't want that, we preventDefault.
    if(!evt._isScroller) {
        evt.preventDefault();
    }
});

