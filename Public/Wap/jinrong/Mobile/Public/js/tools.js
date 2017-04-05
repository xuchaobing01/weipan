var SocketURL = 'data.win60s.com:8090/';
var klineSocketUrl = SocketURL + 'kline';
var mlineSocketUrl = SocketURL + 'mline';
function ajaxCurl(httpType, httpUrl, data, async, callback) {
    $.ajax({
        type: httpType,
        url: httpUrl,
        dataType: 'JSON',
        data: data,// "page=" + num + "&pagesize=" + PAGESIZE + "&type="+listtype,
        async: async,
        success: function (res) {
            //console.log(res);
            callback(res);
        },
        error: function (e) {
            //console.log(e);
        }
    });
}
//设置到期时间
function setExpirationTime(id) {
    var time = new Date();
    time.setMilliseconds(0);
    time.setSeconds(0);
    var minute = time.getMinutes();
    minute = parseInt(minute / 15) * 15;
    var minutes = [];
    time.setMinutes(minute);
    var html = "";
    for (var i = 0; i < 6; i++) {
        time.setTime(time.getTime() + 15 * 60 * 1000);
        var temp = time.getHours() + ":" + (time.getMinutes() == 0 ? "00" : time.getMinutes());
        minutes.push(temp);
        html += "<li role='presentation'><a role='menuitem' href='###' tabindex='-1'>";
        html += temp;
        html += "</a></li>";
    }
    $("#" + id + "display").text(minutes[0]);
    $("#" + id).html('');
    $("#" + id).html(html);
    $("#" + id + " li a").click(function () {
        $("#" + id + "display").text($(this).text());
    });
    // console.log(minutes);
}
$("#time_menu li a").click(function () {
    var t = $(this).text();
    $("#finish_time_display").text(t);
});

//根据文本获取K线时间
function getDiffByText(text) {
    var diff = text.split(' ');
    diff = diff[0];
    return diff * 60;
}
//根据id获取K线时间
function getDiffById(id) {
    var text = $("#" + id + " span[class='select_time']").text();
    var type = $("#time_diff a.changed").attr("type");
    return type * 60;
}
//获取资产类型
function getCapitalTypeById(className) {
    return $("." + className).children("a").attr("type");
}
//获取资产名称
function getCapitalTextById(id) {
    return $("#" + id).text();
}

function setCapitalList(type) {
    var CapitalArray = [];
    var CapitalList = window.param.CapitalList;
    //console.log(CapitalList);
    $("#capitalType").html('');
    $("#capitalType1").html('');
    var html = '';
    for (var i = 0; i < CapitalList.length; i++) {
        if (type == CapitalList[i]['product_key']) {
            CapitalArray.push([CapitalList[i]['capital_key'], CapitalList[i]['capital_name']]);
            html += '<li role="presentation"><a capkey="' + CapitalList[i]['capital_key'] + '" href="javascript:;" tabindex="-1" role="menuitem">' + CapitalList[i]['capital_name'] + '</a></li>';
        }
    }
    //资产类型不为空，显示资产数组中的第一个值
    if (CapitalArray.length > 0) {
        setValueById("btnCapital", CapitalArray[0][1], CapitalArray[0][0]);
        setValueById("btnCapital1", CapitalArray[1][1], CapitalArray[1][0]);
        $("#capitalType").html(html);
        $("#capitalType1").html(html);
        window.param.CapitalArray = CapitalArray;
        //资产下拉列表点击事件
        $("#capitalType li a").click(function () {
            // alert('setCapitalclick');
            $(".loading").css({
                "display": "block"
            });
            $("#btnCapital").attr("capkey", $(this).attr('capkey'));
            $("#btnCapital").html('');
            $("#btnCapital").html($(this).html());
            $("#trade_font_m").text(getCapitalTypeById("sw_active") + '将上涨或下跌？');
            $("#M_candle").css({
                "border": "1px solid #e56358",
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/02-2.png') center no-repeat"
            });
            $("#M_line").css({
                "border": "1px solid #bdbdbd",
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/01.png') center no-repeat"
            });
            drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "");
        });
        $("#capitalType1 li a").click(function () {
            $(".loading2").css({
                "display": "block"
            });
            $("#btnCapital1").attr("capkey", $(this).attr('capkey'));
            $("#btnCapital1").html('');
            $("#btnCapital1").html($(this).html());
            $("#trade_font_m1").text(getCapitalTypeById("sw_active") + '将上涨或下跌？');
            $("#M_candle1").css({
                "border": "1px solid #e56358",
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/02-2.png') center no-repeat"
            });
            $("#M_line1").css({
                "border": "1px solid #bdbdbd",
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/01.png') center no-repeat"
            });
            drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff2"), "svgId", "2");
        });
    } else {
        //资产类型为空，弹出提示
        // alert("There is no data in this category!");
    }
}
/*
 * 画K线图
 * @param capitalType
 */
function drawKline(capitalType, diff, svgId, suffix, priceId) {

    var time = new Date().getTime();
    if(capitalType == null)
        capitalType = getCapitalTypeById("sw_active");

    //console.log('capitalType:'+capitalType+'--diff:'+diff+'--svgId:'+svgId+'--suffix:'+suffix+'--priceId:'+priceId);
    //capitalType:BTCCNY--diff:60--svgId:svgId--suffix:--priceId:deal_value
    var data = "capitalType=" + capitalType + "&diff=" + diff + "&suffix=" + suffix;
    ajaxCurl("POST", "/Wap/trading/klineChart", data, true, function (res) {
        //存储资产列表 此为全局变量
        // console.log(res);
        // console.log('oo1');
        startDrawKline(res, diff, svgId, suffix, capitalType);//1
        var data = res["data"];
        setCurrentPrice(priceId, data[data.length - 1][4]);
    });

    /* getKlineData(capitalType, diff, suffix,function(res) {
     console.log(res);
     console.log('oo1');
     startDrawKline(res, diff, svgId, suffix, capitalType);//1
     var data = res["data"];
     setCurrentPrice(priceId, data[data.length - 1][4]);
     });*/
}
function drawKline_end(capitalType, diff, svgId, suffix, priceId) {
    // alert('drawkline');
    console.log('008');
    console.log(capitalType, diff, svgId, suffix, priceId);
    getKlineData(capitalType, diff, suffix, function (res) {
        // alert(1);
        //console.log(res);
        clearInterval(window.param[capitalType + diff]);
        window.param[capitalType + diff] = setInterval(function () {
            // console.log(capitalType+klineSocketId+suffix,11111);
            drawLastKline(capitalType, diff, suffix); //每秒一次画蜡烛图
        }, 1000);
        var data = res["data"];
        //setCurrentPrice(priceId, data[data.length - 1][4]);
    });
}
/*
 * 画折线图
 * @param capitalType
 */
function drawMline(capitalType, diff, svgId, suffix, priceId) {
    // console.log('oo20');
    getMlineData(capitalType, diff, suffix, function (res) {
        // console.log(res);
        // console.log('oo3');
        startDrawMline(capitalType, res, diff, svgId, suffix);
        var data = res["data"];
        setCurrentPrice(priceId, data[data.length - 1][1]);
    });
}
/**
 *开始画分时
 */
function startDrawMline(capitalType, res, diff, svgId, suffix) {
    var mline = new StockNameSpace.Mline();
    console.log(capitalType, diff, svgId, suffix, res);
    mline.startDraw(res["data"], svgId, suffix, res["decimalPoint"], diff);
    if (suffix == '') {
        window.param.Mline = mline;
    } else if (suffix == '2') {
        window.param.MlineTwo = mline;
    }
    clearInterval(window.param[capitalType + mlineSocketId + suffix]);
    //判断是否开市
    if (week()) {
        //每秒中请求一次
        window.param[capitalType + mlineSocketId + suffix] = setInterval(function () {
            drawLastMline(capitalType, diff, suffix, svgId, res["decimalPoint"]);
        }, 5000);
    }
    if (suffix == '2') {
        $(".loading2").css({
            "display": "none"
        });
    } else {
        $(".loading").css({
            "display": "none"
        });
    }
}

/**
 *画分时的最后一根
 */
function drawLastMline(capitalType, diff, suffix, svgId, decimalPoint) {
    getLastMlineData(capitalType, diff, suffix, function (data) {
        //
        if (window.ed != 0 && window.ed != null) {
            console.log("data1 before:" + data[1]);
            data[1] = window.get_data_cur(window.ed, data[1]);
            console.log("data1 after:" + data[1]);
        }
        if (suffix == '') {
            var currentCapitalType = getCapitalTypeById("sw_active");
            var currentTime = getDiffById('time_diff');
            if (currentCapitalType == data[3] && currentTime == data[4] && currentTime == diff) {
                setCurrentPrice("flow_span_value", data[1]);
                setCurrentPrice("flow_span_value1", data[1]);
                t_current = data[1];
                $("#now_list .now_price").text(t_current);
                setCurrentPrice("now_price", data[1]);
                if ((new_order_price > t_current && order_dirt == 0) || (new_order_price < t_current && order_dirt == 1)) {
                    $('#flow_span_value2').html('<font color=red>盈</font>');
                } else if ((new_order_price < t_current && order_dirt == 0) || (new_order_price > t_current && order_dirt == 1)) {
                    $('#flow_span_value2').html('<font color=green>亏</font>');
                } else {
                    $('#flow_span_value2').html('平局');
                }
                window.param.Mline.modifyLastPoint(data, svgId, suffix, decimalPoint);
            }
        } else {
            setCurrentPrice("flow_span_value", data[1]);
            var currentCapitalType = getCapitalTypeById("sw_active");
            var currentTime = getDiffById('time_diff2');
            // 是同類型的資產才更新最後一根
            if (currentCapitalType == data[3] && currentTime == data[4] && currentTime == diff) {
                setCurrentPrice("deal_value1", data[1]);
                window.param.MlineTwo.modifyLastPoint(data, svgId, suffix, decimalPoint);
            }
        }
    });
}

/**
 *开始画k线
 */
function startDrawKline(res, diff, svgId, suffix, capitalType) {
    console.log(diff);

    console.log('002');
    var kline = new StockNameSpace.Kline();
    removeById("priceTipId" + suffix);
    removeById("dashedId" + suffix);
    removeById("mlinePriceTipId" + suffix);
    removeById("mlineDashedId" + suffix);
    if (suffix == '') {
        var currentCapitalType = getCapitalTypeById("sw_active");
        var currentTime = getDiffById('time_diff');
        if (currentCapitalType == res['capitalType'] && currentTime == res['diff'] && currentTime == diff) {
            var temp = res['data'];
            var data = temp[temp.length - 1];
            //console.log(data);
            setCurrentPrice("now_price", data[4]);
            setCurrentPrice("flow_span_value", data[4]);
            console.log('003');
            kline.startDraw(res, diff, svgId, suffix, res["decimalPoint"]);
            console.log('004');
            window.param.Kline = kline;
        }
    } else {
        var currentCapitalType = getCapitalTypeById("sw_active");
        var currentTime = getDiffById('time_diff2');
        if (currentCapitalType == res['capitalType'] && currentTime == res['diff'] && currentTime == diff) {
            var temp = res['data'];
            var data = temp[temp.length - 1];
            setCurrentPrice("deal_value1", data[4]);
            kline.startDraw(res, diff, svgId, suffix, res["decimalPoint"]);
            window.param.KlineTwo = kline;
        }
    }
    clearInterval(window.param[capitalType + diff + suffix]);
    //判断是否开市
    // if(week()){
    //console.log("存入",capitalType+diff);
    var second = (diff / 60) * 1000;
    window.param[capitalType + diff] = setInterval(function () {
        // console.log(capitalType+klineSocketId+suffix,11111);
        if(capitalType == null)
            capitalType = getCapitalTypeById("sw_active");
        drawLastKline(capitalType, diff, suffix); //每秒一次画蜡烛图
    }, 1000);
    //}
    if (suffix == '2') {
        $(".loading2").css({
            "display": "none"
        });
    } else {
        $(".loading").css({
            "display": "none"
        });
    }
}
/**
 *画最后一根k线图
 */
function drawLastKline(capitalType, diff, suffix) {

    var dames = "capitalType=" + capitalType + "&diff=" + diff + "&suffix=" + suffix;
    ajaxCurl("POST", "/Wap/trading/lastKlineParameter", dames, true, function (data) {
        //
        // 新K线图
        newKdata = [data[5]]
        server_charts(newKdata);

        if (window.ed != 0 && window.ed != null) {
            console.log("data1 before:" + data[1]);
            data[1] = window.get_data_cur(window.ed, data[1]);
            console.log("data1 after:" + data[1]);
        }
        if (suffix == '') {
            setCurrentPrice("now_price", data[1]);//实时更新弹窗中的价格
            var currentCapitalType = getCapitalTypeById("sw_active");
            var currentTime = getDiffById('time_diff');
            // console.log(data);
            // console.log(currentCapitalType,data[2],currentTime,data[3],diff);
            if (currentCapitalType == data[2] && currentTime == data[3] && currentTime == diff) {
                t_current = data[1];
                $("#now_list .now_price").text(t_current);
                setCurrentPrice("now_price", data[1]);

                if ((new_order_price > t_current && order_dirt == 0) || (new_order_price < t_current && order_dirt == 1)) {
                    $('#flow_span_value2').html('<font color=red>盈</font>');
                } else if ((new_order_price < t_current && order_dirt == 0) || (new_order_price > t_current && order_dirt == 1)) {
                    $('#flow_span_value2').html('<font color=green>亏</font>');
                } else {
                    $('#flow_span_value2').html('平局');
                }
                setCurrentPrice("flow_span_value1", data[1]);
                window.param.Kline.modifyLastCandle(data, diff);
            }
        } else {
            setCurrentPrice("flow_span_value", data[1]);
            setCurrentPrice("now_price", data[1]);//实时更新弹窗中的价格
            var currentCapitalType = getCapitalTypeById("sw_active");
            var currentTime = getDiffById('time_diff2');
            //是同類型的資產才更新最後一根
            if (currentCapitalType == data[2] && currentTime == data[3] && currentTime == diff) {
                setCurrentPrice("now_price", data[1]);
                window.param.KlineTwo.modifyLastCandle(data, diff);
            }
        }
    });


    getLastKlineData(capitalType, diff,suffix, function(data) {});
}

/**
 * 根据id删除元素
 */
function removeById(id) {
    var obj = document.getElementById(id);
    if (obj) {
        var parentElement = obj.parentNode;
        if (parentElement) {
            parentElement.removeChild(obj);
        }
    }
}

/**
 *设置完成时间的点击事件
 */
$('#finish_time button').click(function () {
    $(this).siblings().removeClass("selected");
    $(this).addClass("selected");
});

$('#finish_time button1').click(function () {
    $(this).siblings().removeClass("selected");
    $(this).addClass("selected");
});


//连接socket服务器
// console.log(klineSocketUrl);
// var klineSocket = io.connect(klineSocketUrl);
// var klineSocketId = null;
// klineSocket.on('connect', function (sock) {
//     klineSocketId = klineSocket.id;
// });
/*获取K线数据
 * @param capitalType:资产类型如EURUSD diff:时间如60 300
 */
function getKlineData(capitalType, diff, suffix, callback) {
    var time = new Date().getTime();
    var emitType = 'kline' + capitalType + diff + time;
    console.log(emitType);
    // klineSocket.emit('klineChart', {capitalType: capitalType, diff: diff, suffix: '', emitType: emitType});
    // klineSocket.on(emitType, callback);
}
// var mlineSocket = io.connect(mlineSocketUrl);
// var mlineSocketId = null;
// mlineSocket.on('connect', function () {
//     mlineSocketId = mlineSocket.id;
// });

/*
 * 获取k线最后一根的数据
 * @param capitalType:资产类型如EURUSD diff:时间如60 300
 */
function getLastKlineData(capitalType, diff, suffix, callback) {
    var time = new Date().getTime();
    var emitType = 'kline' + capitalType + 'last' + diff + suffix + time;
    // klineSocket.emit('lastKlineParameter', {capitalType: capitalType, diff: diff, suffix: suffix, emitType: emitType});
    // klineSocket.on(emitType, callback);
}

/*获取折线数据
 * @param capitalType:资产类型如EURUSD diff:时间如60 300
 */
function getMlineData(capitalType, diff, suffix, callback) {
    var time = new Date().getTime();
    var emitType = 'Mlinedata' + capitalType + time
    // mlineSocket.emit('mlineChart', {capitalType: capitalType, diff: diff, suffix: suffix, emitType: emitType});
    // mlineSocket.on(emitType, callback);
}
/**
 *获取折线最后一个点的数据
 */
function getLastMlineData(capitalType, diff, suffix, callback) {
    var time = new Date().getTime();
    var emitType = 'Mline' + capitalType + 'last' + diff + time;
    mlineSocket.emit('lastMlineParameter', {capitalType: capitalType, diff: diff, suffix: suffix, emitType: emitType});
    mlineSocket.on(emitType, callback);
}
/**
 *设置看涨看跌点击事件
 */
$("#up_btn").click(function () {
    setUpDownValue("btnCapital", "deal_value", "deal_amount", $(this).text(), 180, 20);
});
$("#up_btn1").click(function () {
    setUpDownValue1("btnCapital1", "deal_value1", "deal_amount1", $(this).text(), 500, 20);
});
$("#down_btn").click(function () {
    setUpDownValue("btnCapital", "deal_value", "deal_amount", $(this).text(), 180, 20);
});
$("#down_btn1").click(function () {
    setUpDownValue1("btnCapital1", "deal_value1", "deal_amount1", $(this).text(), 500, 20);
});
//取消下单的点击事件
$("#ct_order").click(function () {
    $("#model_t").css("display", "none");
    $("#box").css("display", "none");
});
$("#ct_order1").click(function () {
    $("#model_t1").css("display", "none");
    $("#box1").css("display", "none");
});
//下单的点击事件
$("#st_order").click(function () {
    $("#msg_order").css({
        "display": "block"
    });
});
$("#st_order1").click(function () {
    $("#msg_order1").css({
        "display": "block"
    });
});


//根据下拉列表画k线或者折线
$("#select_id a").click(function () {
    //清空定时任务
    var capitalType = getCapitalTypeById("sw_active");
    clearTimeout(window.param[capitalType + klineSocketId]);
    $(".loading").css({
        "display": "block"
    });
    if ($(this).attr("value") == "candle") {
        displayImg("candle");
        drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "now_price");
    } else if ($(this).attr("value") == "line") {
        displayImg("line", capitalType);
        drawMline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "now_price");
    } else if ($(this).attr("value") == "ma") {
        displayImg("ma");
        drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "now_price");
    } else if ($(this).attr("value") == "boll") {
        displayImg("boll");
        drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "now_price");
    }
});
$("#select_id_t a").click(function () {
    //清空定时任务
    var capitalType = getCapitalTypeById("sw_active");
    clearTimeout(window.param[capitalType + klineSocketId + '2']);

    $(".loading2").css({
        "display": "block"
    });
    if ($(this).attr("value") == "candle") {
        displayImg1("candle");
        drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff2"), "svgId", "2", "now_price");
    } else if ($(this).attr("value") == "line") {
        displayImg1("line", capitalType);
        drawMline(getCapitalTypeById("sw_active"), getDiffById("time_diff2"), "svgId", "2", "now_price");
    } else if ($(this).attr("value") == "ma") {
        displayImg1("ma");
        drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff2"), "svgId", "2", "now_price");
    } else if ($(this).attr("value") == "boll") {
        displayImg1("boll");
        drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff2"), "svgId", "2", "now_price");
    }
})
function displayImg(v, capitalType) {
    if (v == "boll") {
        $("#chart_type_input").val("candle");
        //$("#BOLL_line").css({"border":"1px solid #e56358","background":"url('"+getRootPath_web()+"/Public/template/default/images/022-1.png') center no-repeat"});
        $("#BOLL_line").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/011-1.png') center no-repeat"
        });
        $("#MA_line").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/022.png') center no-repeat"
        });
        $("#M_candle").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/02-2.png') center no-repeat"
        });
        $("#M_line").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/01.png') center no-repeat"
        });
        //添加ma均线显示
        $(".M_trade_mine").css({"display": "none"});
        StockNameSpace.MADisplay = false;
        StockNameSpace.BollDisplay = true;
    } else if (v == "ma") {
        $("#chart_type_input").val("candle");
        $("#MA_line").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/022-1.png') center no-repeat"
        });
        $("#BOLL_line").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/011.png') center no-repeat"
        });
        $("#M_candle").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/02-2.png') center no-repeat"
        });
        $("#M_line").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/01.png') center no-repeat"
        });
        //$("#BOLL_line").css({"border":"1px solid #bdbdbd","background":"url('"+getRootPath_web()+"/Public/template/default/images/011-1.png') center no-repeat"});
        //添加ma均线显示
        $(".M_trade_mine").css({"display": "block"});
        StockNameSpace.MADisplay = true;
        StockNameSpace.BollDisplay = false;
    } else if (v == "candle") {
        $("#chart_type_input").val("candle");
        $("#M_candle").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/02-2.png') center no-repeat"
        });
        var color;
        StockNameSpace.BollDisplay = false;
        StockNameSpace.MADisplay = false;
        if (StockNameSpace.BollDisplay) {
            color = "#e56358";
            $("#BOLL_line").css({
                "border": '1px solid ' + color,
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/011-1.png') center no-repeat"
            });
        } else {
            color = "#bdbdbd";
            $("#BOLL_line").css({
                "border": '1px solid ' + color,
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/011.png') center no-repeat"
            });
        }

        if (StockNameSpace.MADisplay) {
            color = "#e56358";
            $("#MA_line").css({
                "border": '1px solid ' + color,
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/022-1.png') center no-repeat"
            });
        } else {
            color = "#bdbdbd";
            $("#MA_line").css({
                "border": '1px solid ' + color,
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/022.png') center no-repeat"
            });
        }

        $("#M_line").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/01.png') center no-repeat"
        });
        //添加ma均线显示
        $(".M_trade_mine").css({"display": "block"});
    } else if (v == "line") {
        var diff = getDiffById("time_diff");
        clearInterval(window.param[capitalType + diff]);
        removeById("klineBottomTimeId");
        $("#chart_type_input").val("line");
        $("#M_line").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/01-1.png') center no-repeat"
        });
        $("#BOLL_line").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/011.png') center no-repeat"
        });
        $("#MA_line").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/022.png') center no-repeat"
        });
        $("#M_candle").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/02.png') center no-repeat"
        });
        //取消ma均线显示
        $(".M_trade_mine").css({"display": "none"});
    }
}
function displayImg1(v, capitalType) {
    if (v == "boll") {
        $("#chart_type_input1").val("candle");
        //$("#BOLL_line").css({"border":"1px solid #e56358","background":"url('"+getRootPath_web()+"/Public/template/default/images/022-1.png') center no-repeat"});
        $("#BOLL_line1").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/011-1.png') center no-repeat"
        });
        $("#MA_line1").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/022.png') center no-repeat"
        });
        $("#M_candle1").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/02-2.png') center no-repeat"
        });
        $("#M_line1").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/01.png') center no-repeat"
        });
        //添加ma均线显示
        $(".M_trade_mine2").css({"display": "none"});
        StockNameSpace.MADisplay = false;
        StockNameSpace.BollDisplay = true;
    } else if (v == "ma") {
        $("#chart_type_input1").val("candle");
        $("#MA_line1").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/022-1.png') center no-repeat"
        });
        $("#BOLL_line1").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/011.png') center no-repeat"
        });
        $("#M_candle1").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/02-2.png') center no-repeat"
        });
        $("#M_line1").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/01.png') center no-repeat"
        });
        //$("#BOLL_line").css({"border":"1px solid #bdbdbd","background":"url('"+getRootPath_web()+"/Public/template/default/images/011-1.png') center no-repeat"});
        //添加ma均线显示
        $(".M_trade_mine2").css({"display": "block"});
        StockNameSpace.MADisplay = true;
        StockNameSpace.BollDisplay = false;
    } else if (v == "candle") {
        $("#chart_type_input1").val("candle");
        $("#M_candle1").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/02-2.png') center no-repeat"
        });
        var color;
        StockNameSpace.BollDisplay = false;
        StockNameSpace.MADisplay = false;
        if (StockNameSpace.BollDisplay) {
            color = "#e56358";
            $("#BOLL_line1").css({
                "border": '1px solid ' + color,
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/011-1.png') center no-repeat"
            });
        } else {
            color = "#bdbdbd";
            $("#BOLL_line1").css({
                "border": '1px solid ' + color,
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/011.png') center no-repeat"
            });
        }

        if (StockNameSpace.MADisplay) {
            color = "#e56358";
            $("#MA_line1").css({
                "border": '1px solid ' + color,
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/022-1.png') center no-repeat"
            });
        } else {
            color = "#bdbdbd";
            $("#MA_line1").css({
                "border": '1px solid ' + color,
                "background": "url('" + getRootPath_web() + "/Public/template/default/images/022.png') center no-repeat"
            });
        }

        $("#M_line1").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/01.png') center no-repeat"
        });
        //添加ma均线显示
        $(".M_trade_mine2").css({"display": "block"});
    } else if (v == "line") {
        var diff = getDiffById("time_diff2");
        clearInterval(window.param[capitalType + diff + "2"]);
        removeById("klineBottomTimeId" + "2");
        $("#chart_type_input1").val("line");
        $("#M_line1").css({
            "border": "1px solid #e56358",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/01-1.png') center no-repeat"
        });
        $("#BOLL_line1").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/011.png') center no-repeat"
        });
        $("#MA_line1").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/022.png') center no-repeat"
        });
        $("#M_candle1").css({
            "border": "1px solid #bdbdbd",
            "background": "url('" + getRootPath_web() + "/Public/template/default/images/02.png') center no-repeat"
        });
        //取消ma均线显示
        $(".M_trade_mine2").css({"display": "none"});
    }
}
//添加时间点击事件
$("#time_diff li a").click(function () {
    var diff = getDiffById("time_diff");
    $(this).parent().siblings().children("a").removeClass("changed");
    $(this).addClass("changed");
    var capitalType = getCapitalTypeById("sw_active");
    clearTimeout(window.param[capitalType + diff]);
    diff = getDiffById("time_diff");
    if ($("#chart_type_input").val() == "candle") {
        drawKline(capitalType, diff, "svgId", "", "now_price");
    } else {
        drawMline(capitalType, diff, "svgId", "", "now_price");
    }
});
//关闭广告
$(".ad-close").on("click", function (e) {
    $("#ad").hide();
    var diff = getDiffById("time_diff");
    var capitalType = getCapitalTypeById("sw_active");
    clearTimeout(window.param[capitalType + diff]);
    delete StockNameSpace.SvgX;
    delete StockNameSpace.SvgY;
    drawKline(capitalType, diff, "svgId", "", "now_price");
    e.stopPropagation();
});
$("#time_diff2 span").click(function () {
    $(".loading2").css({
        "display": "block"
    });
    $(this).siblings().removeClass("select_time");
    $(this).addClass("select_time");
    var diff = getDiffById("time_diff2");
    var capitalType = getCapitalTypeById("sw_active");
    clearTimeout(window.param[capitalType + "2"]);
    if ($("#chart_type_input1").val() == "candle") {
        drawKline(capitalType, diff, "svgId", "2", "now_price");
    } else {
        drawMline(capitalType, diff, "svgId", "2", "now_price");
    }
});

function setValueById(id, value1, value2) {
    $("#" + id).html('');
    $("#" + id).html(value1);
    $("#" + id).attr("capkey", value2);
}
//通过点击资产header设置资产列表
$("body").delegate("#product_switch li a", "click", function () {
    var capitalType = $(".sw_active a").attr("type");
    var capitalType_now = $(this).attr("type");
    if (capitalType_now !== 'BTCCNY' && is_Close == 1) {
        showLoading('该资产已休市', 2000);
        //showLoading('该资产维护中，请稍后再试', 2000);
        var diff = getDiffById("time_diff");
        console.log("清除1", capitalType + diff);
        clearInterval(window.param[capitalType + diff]);
        drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "now_price");
        return false;
        //location.reload();
        $("#trade-box").html('<div class="trade-box info-box"style=" padding-bottom: 0;"><span id="select_id"style="display:none;position:absolute;"><a class="M_a_1 M_a_2 M_a_4"id="MA_line"href="javascript:;"value="ma"></a><a class="M_a_1 M_a_display"id="M_candle"href="javascript:;"value="candle"></a><input type="hidden"value="candle"id="chart_type_input"/></span><div id="chart"><svg xmlns="http://www.w3.org/2000/svg"version="1.1"id="svgId"width="415"height="175"><defs><linearGradient id="highcharts"x1="0"y1="0"x2="0"y2="100%"><stop offset=0 stop-color="rgb(0,197,205)"stop-opacity="1"/><stop offset=1 stop-color="rgb(0,229,238)"stop-opacity="0.1"/></linearGradient></defs></svg></div></div><ul class="trend-nav echart_ clearfix"id="time_diff"><li class="linechart"><a href="javascript:void(0);"class="changed"type="1">1分钟线</a></li><li class="kchart"><a href="javascript:void(0);"type="3">3分钟线</a></li><li class="kchart"><a href="javascript:void(0);"type="5">5分钟线</a></li><li class="kchart"><a href="javascript:void(0);"type="15">15分钟线</a></li></ul></div><div>');
    } else if (is_close == 1) {
        /*$("#now_price").text('休市');
         $("#trade-box").html('<div class="M_trdiv" id="xiushi" style="height: 100%;"><img src="/Mobile/Public/images/zanwu.jpg" width="100%" alt="image"></div>');
         */
        //showLoading('该资产已休市', 2000);
        //return;
    }
    $(this).parent().siblings().removeClass("sw_active");
    $(this).parent().addClass("sw_active");
    var diff = $(".changed").attr("type") * 60;
    diff_one = 1;
    clearInterval(chgsta);
    capital_one = capitalType_now;
    $("#optionname").text($(this).text());
    console.log("清除", capitalType + diff);
    clearInterval(window.param[capitalType + diff]);
    drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "");
    // 切换tab
    vue.set_map("M1");
});


//添加click事件
function addClickById(id, callback) {
    $("#" + id).bind("click", callback);
}

function addClickByClass(className, callback) {
    $("." + className).bind("click", callback);
}
//设置下单金额的点击事件
$(".btn-amount").click(function () {
    $("#deal_amt").css({"display": "block"});
});
$(".btn-amount1").click(function () {
    $("#deal_amt1").css({"display": "block"});
});
$("#deal_amt li a").click(function (displayId) {
    $("#deal_amount").html('');
    $("#deal_amount").html($(this).text());
    $("#deal_amt").css({"display": "none"});
    $("#expect_profit").text('￥' + $(this).text() * 1.8);
});
$("#deal_amt1 li a").click(function (displayId) {
    $("#deal_amount1").html('');
    $("#deal_amount1").html($(this).text());
    $("#deal_amt1").css({"display": "none"});
    $("#expect_profit1").text('￥' + $(this).text() * 1.8);
});

//设置看涨 看跌的值
function setUpDownValue(capitalTypeId, priceId, amountId, text, top, left) {
    $("#model_t").css("display", "block");
    $("#box").css({
        "display": "block",
        "position": "absolute",
        "top": top + "px",
        "left": left + "px"
    });
    //资产类型
    $("#flow_span").text(getCapitalTextById(capitalTypeId));
    //订单方向
    $("#flow_span_dir").text(text);
    //价格
    $("#flow_span_value").text($("#" + priceId).text());
    $("#flow_span_value1").text($("#" + priceId).text());
    //金额
    $("#flow_span_mount").text($("#" + amountId).text());
    //时间
    var time = $("#time_menudisplay").text();
    if (!time) {
        time = $("button.selected").val();
    }
    $("#flow_span_time").text(time);

    //预期收益
    $("#flow_span_profit").text($("#expect_profit").text());
}
function setUpDownValue1(capitalTypeId, priceId, amountId, text, top, left) {
    $("#model_t1").css("display", "block");
    $("#box1").css({
        "display": "block",
        "position": "absolute",
        "top": top + "px",
        "left": left + "px"
    });
    //资产类型
    $("#flow_span1").text(getCapitalTextById(capitalTypeId));
    //订单方向
    $("#flow_span_dir1").text(text);
    //价格
    $("#flow_span_value1").text($("#" + priceId).text());
    //金额
    $("#flow_span_mount1").text($("#" + amountId).text());
    //时间
    var time = $("#time_menu1display").text();
    if (!time) {
        time = $("button.selected").val();
    }
    $("#flow_span_time1").text(time);
    //预期收益
    $("#flow_span_profit1").text($("#expect_profit1").text());
}
//设置最新价格
function setCurrentPrice(id, price) {
    if (capital_one !== "BTCCNY" && is_close == 1) {
        return;
    }
    $("#" + id).html('');
    $("#" + id).html(price);
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
function getRootPath_web() {
    //获取当前网址，如： http://localhost:8083/uimcardprj/share/meun.jsp
    var curWwwPath = window.document.location.href;
    //获取主机地址之后的目录，如： uimcardprj/share/meun.jsp
    var pathName = window.document.location.pathname;
    var pos = curWwwPath.indexOf(pathName);
    //获取主机地址，如： http://localhost:8083
    var localhostPaht = curWwwPath.substring(0, pos);
    //获取带"/"的项目名，如：/uimcardprj
    var projectName = pathName.substring(0, pathName.substr(1).indexOf('/') + 1);
    return (localhostPaht);
}

function week() {
    return true;
    // var day = new Date().getDay();
    // if(day >0 && day < 6){
    //   return true;
    // }else{
    //   return false;
    // }
}
