var map_data = {};//分时图数据 quotesArr数据数组 start_time最后数据的时间
var curTime = null;//当前时间
var p_code = null;
var p_range = null;
//var isgeting = false;//是否获取数据中
var geting_list = [];//拉取服务器时 主动推送的数据
function InitializaChart(code, range, count)
{
    var testDate = new Date();
   // console.log("开始请求:[" + testDate.getMinutes() + ":" + testDate.getSeconds() + ":" + testDate.getMilliseconds() + "]=======================================================================" );
    loadshow();
    setDataLast();
    p_code = code;
    p_range = range;
    var local_resdata = map_data[code + range + 'resdata'];//内存获取数据
    var start_time = null;
    //isgeting = true;
    $.ajax({
        type: "POST",
        cache: false,
        dataType: "json",
        // url: "/Ashx/HighstockChart.ashx",
        url: "/Wap/trading/highStockChart",
        data: { "type": code, "code": code, "range": range, "count": count,"start_time" : start_time },
        success: function (resdata) {
            var testDate = new Date();
            //console.log("请求结束:[" + testDate.getMinutes() + ":" + testDate.getSeconds() + ":" + testDate.getMilliseconds() + "]");
            //isgeting = false;
            if (range != "M1") {
                InitializaK(code, range, resdata);
            }
            else {
                InitializaM(code, range, resdata);
            }
            loadhide();
        }
       
    });
}

function InitializaM(code, range, resdata) {
    var vdata = eval(resdata.quotesArr);
    if (vdata.length == 0) {
        return
    }
    var data = [];
    for (var i = 0; i < vdata.length; i++) {
        var vd = vdata[i];
        var t = vd[0].substr(10, 6);
        data[i] = { 'date': t, 'visits': vd[1] }
    }

    var chart = AmCharts.makeChart('container', {
        "type": "serial",
        "theme": "tz_black",
        "dataProvider": data,
        'marginRight': 0,
        'marginTop': 0,
        "valueAxes": [{
            "position": "left",
            "title": "Unique visitors"
        }],
        "graphs": [{
            "id": "g1",
            "fillAlphas": 0.4,
            "valueField": "visits",
            "balloonText": "<div style='font-size:12px;background:#d8d7d7;'>价格:<b>[[value]]</b></div>"
        }],
        "chartScrollbar": {
            "enabled": false,
            "graph": "g1",
        },
        "categoryAxis": {
            "gridPosition": "start",
            "axisThickness": 0,
            "tickLength": 0,
            "startOnAxis": true, 
        },
        "chartCursor": {
            "categoryBalloonDateFormat": "JJ:NN, DD MMMM",
            "cursorPosition": "mouse",
            "zoomable": false,
         
        },


        "categoryField": "date",

        "export": {
            "enabled": true,
            "dateFormat": "YYYY-MM-DD HH:NN:SS"
        },
        "valueAxes": [
                 {

                     "id": "ValueAxis-1",
                     "inside": true,
                     "axisAlpha": 0.12,
                 }
        ],
    });

}


var czoom = 0;//缩放因子
var zoom_number = 10;//缩放次数
var min_len = 20;//最少显示数量
var init_zoom = min_len;//默认显示条数
var charts = null;
var charts_range = null;//K线图时间
function InitializaK(code, range, resdata) {
    charts_range = range;//记录选择的区间
    initChartsItem();//清除画线数据
    var vdata = eval(resdata.quotesArr);
    if (vdata.length == 0) return;
    var data = formatData(vdata);
    var is_mouse_show = $("#IsShowCartDetail").val();

    var last_data = data[data.length - 1];//取服务器最后一条数据
    
    if (charts_data[code] && charts_data[code].date == last_data.date) {//判断时间是否相等
        var cdata = charts_data[code];
        if (cdata.low < last_data.low) {
            data[data.length - 1].low = cdata.low;
            }
        if (cdata.high > last_data.hig ) {
            data[data.length - 1].hig = cdata.high;
        }
    }
    if (is_mouse_show == 1) {
        charts = AmCharts.makeChart('container',
                {
                    "type": "serial",
                    "theme": "tz_black",
                    "dataDateFormat": "YYYY-MM-DD\nHH:NN:SS",
                    "balloonDateFormat": "MMM DD, YYY",
                    "categoryField": "date",
                    "shortMonthNames": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                    "mouseWheelZoomEnabled": true,
                    "zoomOutOnDataUpdate": false,
                    "zoomOutText": "",
                    'marginRight': 0,
                    'marginTop': 0,
                    "chartCursor": {
                        "enabled": true,
                        "selectWithoutZooming": true,
                        "tabIndex": 2,
                        "valueLineBalloonEnabled": true,
                        "valueLineEnabled": true,
                        "leaveAfterTouch": false,
                        "selectionAlpha": 0,
                        "zoomable": false,

                    },

                    "trendLines": [],
                    "graphs": [
                        {
                            "balloonText": "<span style='font-size:11px;'>开盘价:<b>[[open]]</b><br>最高价:<b>[[high]]</b><br>最低价:<b>[[low]]</b><br>收盘价:<b>[[close]]</b><br></span>",
                            "closeField": "close",
                            "fillAlphas": 0.9,
                            "fillColors": "#E63234",
                            "highField": "high",
                            "id": "g1",
                            "lineColor": "#E63234",
                            "lowField": "low",
                            "negativeFillColors": "#1EB83F",
                            "negativeLineColor": "#1EB83F",
                            "openField": "open",
                            "title": "Price:",
                            "type": "candlestick",
                            "valueField": "close",
                        }
                    ],
                    "guides": [],
                    "valueAxes": [
                        {

                            "id": "ValueAxis-1",
                            "inside": true,
                            "axisAlpha": 0.12,
                            "offset": 5,
                            "guides": [
                                {
                                    "id": "price_line",
                                    "inside": true,
                                    "label": "150",
                                    "dashLength": 2,
                                    "color": "#c0a33f",
                                    "lineAlpha": 0.9,
                                    "value": 0,
                                    "position": "right",
                                    "lineColor": "#E63234"
                                }
                            ]
                        }
                    ],
                    "categoryAxis": {
                        "gridPosition": "start",
                        "axisThickness": 0,
                        "tickLength": 0,
                    },
                    "listeners": [{
                        "event": "zoomed",
                        "method": function (e) {
                            var chartData = e.chart.dataProvider;
                            if (e.startIndex < chartData.length - 1) {
                                czoom = e.startIndex;
                            } else {
                                czoom = chartData.length - 1
                            }
                            e.chart.zoomToIndexes(czoom, chartData.length - 1);


                        }
                    }, {
                        "event": "init",
                        "method": function (e) {
                            var chartData = e.chart.dataProvider;
                            e.chart.zoomToIndexes(chartData.length - init_zoom, chartData.length - 1);
                            drawInit(e);
                        },
                    },

                    ],
                    "allLabels": [],
                    "balloon": {},
                    "titles": [],
                    "export": {
                        "enabled": true,
                        "dateFormat": "YYYY-MM-DD HH:NN:SS"
                    },
                    "dataProvider": data
                }
        );
    } else {
        charts = AmCharts.makeChart('container',
               {
                   "type": "serial",
                   "theme": "tz_black",
                   "dataDateFormat": "YYYY-MM-DD\nHH:NN:SS",
                   "balloonDateFormat": "MMM DD, YYY",
                   "categoryField": "date",
                   "shortMonthNames": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                   "mouseWheelZoomEnabled": true,
                   "zoomOutOnDataUpdate": false,
                   "zoomOutText": "",
                   'marginRight': 0,
                   'marginTop': 0,
                   "trendLines": [],
                   "graphs": [
                       {
                           "balloonText": "",
                           "closeField": "close",
                           "fillAlphas": 0.9,
                           "fillColors": "#E63234",
                           "highField": "high",
                           "id": "g1",
                           "lineColor": "#E63234",
                           "lowField": "low",
                           "negativeFillColors": "#1EB83F",
                           "negativeLineColor": "#1EB83F",
                           "openField": "open",
                           "title": "Price:",
                           "type": "candlestick",
                           "valueField": "close",
                       }
                   ],
                   "guides": [],
                   "valueAxes": [
                       {

                           "id": "ValueAxis-1",
                           "inside": true,
                           "axisAlpha": 0.12,
                           "offset": 5,
                           "guides": [
                               {
                                   "id": "price_line",
                                   "inside": true,
                                   "label": "150",
                                   "dashLength": 2,
                                   "color": "#c0a33f",
                                   "lineAlpha": 0.9,
                                   "value": 0,
                                   "position": "right",
                                   "lineColor": "#E63234"
                               }
                           ]
                       }
                   ],
                   "categoryAxis": {
                       "gridPosition": "start",
                       "axisThickness": 0,
                       "tickLength": 0,
                   },
                   "listeners": [{
                       "event": "zoomed",
                       "method": function (e) {
                           var chartData = e.chart.dataProvider;
                           if (e.startIndex < chartData.length - 1) {
                               czoom = e.startIndex;
                           } else {
                               czoom = chartData.length - 1
                           }
                           e.chart.zoomToIndexes(czoom, chartData.length - 1);


                       }
                   }, {
                       "event": "init",
                       "method": function (e) {
                           var chartData = e.chart.dataProvider;
                           e.chart.zoomToIndexes(chartData.length - init_zoom, chartData.length - 1);
                           drawInit(e);
                       },
                   },

                   ],
                   "allLabels": [],
                   "balloon": {},
                   "titles": [],
                   "export": {
                       "enabled": true,
                       "dateFormat": "YYYY-MM-DD HH:NN:SS"
                   },
                   "dataProvider": data
               }
       );
    }
    //setChartsEvent();//设置事件
}

//设置缩放
function setZoom(dir) {
    var all = charts.dataProvider.length - 1;
    czoom += parseInt(all / zoom_number) * dir;
    if (dir == 1 && czoom > all - min_len) {
        czoom = all - min_len;
    } else if (dir == -1 && czoom <= 0) {
        czoom = 0;
    }
    charts.zoomToIndexes(czoom, all);
}
//格式化数据
function formatData(vdata) {
    var data = [];
    for (var i = 0; i < vdata.length; i++) {
        var vd = vdata[i];
        data[i] = { 'date': getDataTime(vd[0]), 'open': vd[1], 'high': vd[3], 'low': vd[4], 'close': vd[2] }
    }
    return data;
}
 
function allToFixed(v,l) {
    return parseFloat(v).toFixed(l);
}

//格式化时间
function getDataTime(v) {
    v=v.replace(/\//g, "-");
    var y = v.substring(5, 10);
    var t = v.substr(11, 5);
    return y + '\n' + t
}

var isChartInit = false;//走势图是否初始完成
var isTouch = false;//是否触摸
var isNew = true;//是否是新蜡烛
var chart_ptime = 0;//上一个时间
var cData = null;//当前蜡烛数据
var intervalObj = null;//循环器
var get_ptime=0;//主动拉数据的时间
function drawInit(e) {//绘图初始化完成
    var chartData = e.chart.dataProvider;
    cData = chartData[chartData.length - 1];//第一次则获取最后一条数据
    chart_ptime = getChartsTime(cData.date);//保存最后一条的时间
    isChartInit = true;
}

function addChrtsItem(data) {//添加一条数据
    get_ptime = chart_ptime;//记录最后一条时间
    charts.dataProvider.shift();

    var addTimeData = { "open": data.close, "high": data.close, "low": data.close, "close": data.close, "date": data.date };
    addTimeData.open = charts_data[p_code].open;
    if (charts_data[p_code].high > addTimeData.high) {
        addTimeData.high = charts_data[p_code].high;
    }
    if (charts_data[p_code].low < addTimeData.low) {
        addTimeData.low = charts_data[p_code].low;
    }

    chart_ptime = getChartsTime(data.date);//保存最后一条的时间
    charts.dataProvider.push(addTimeData);
    cData = addTimeData;
    charts.validateData();
    //拉数据改变倒数第二条
    // setTimeout(getLastTwo, 600);
}
function getLastTwo() {
    var ptimeStr = new Date(get_ptime * 1000).Format("yyyy-MM-dd hh:mm:ss");
    $.ajax({
        type: "POST",
        cache: false,
        dataType: "json",
        url: "/Wap/trading/highStockChart",
        data: { "type": p_code, "code": p_code, "range": p_range, "time": ptimeStr },
        success: function (resdata) {
            var vdata = eval(resdata.quotesArr);
            if (vdata.length == 0) {
                return
            }
            var data = formatData(vdata);
            //console.log(vdata);
            charts.dataProvider[charts.dataProvider.length - 2] = data[0];
            charts.validateData();
        }
    });
}
var charts_data = {};
function server_charts(data) {//服务器主推所有数据
    for (var i = 0; i < data.length; i++) {//格式转换
        var key = data[i].TypeName.toString();
        var ask = data[i].ask.toFixed(2);
        var ctm = data[i].ctm;
        ctm = ctm.replace("T", " ");
        ctm = ctm.replace(/-/g, '/');
        if (charts_data[key] ) {
            if (charts_data[key].date != getDataTime(ctm)) {//不是一分钟
                charts_data[key] = null;
            }
        }
        if (!charts_data[key]) {//不是一分钟
            charts_data[key] = {
                "date": getDataTime(ctm),//时间需要处理
                "open": ask, "high": ask, "low": ask, "close": ask
            }//更新数据

        } else {
            charts_data[key].close = ask;//更新平仓价
            if (ask > charts_data[key].high) {//最高
                charts_data[key].high = ask;
            }
            if (ask < charts_data[key].low) {//最低
                charts_data[key].low = ask;
            }
        }
        if(p_code==key){
            //console.log(ctm + "   " + key + ":" + JSON.stringify(charts_data[key]));
            charts_update(ctm, ask);
        }


    }
}
function charts_update(time, ask) {//服务器主动推数据
    
    //if (isgeting) {
    //    var testDate = new Date();
    //    console.log("写入队列:[" + testDate.getMinutes() + ":" + testDate.getSeconds() + ":" + testDate.getMilliseconds() + "]" + JSON.stringify(data));
    //    geting_list.push(data)
    //}
   
    if (!isChartInit || isTouch || time == "" || p_range=="M1") return;
    var dtime = new Date(time);
    var server_time = dtime.getTime() / 1000;//获取时间搓
    var all_time = getChartsRangeIndex() * 60;
    var drawLen = parseInt(((server_time - chart_ptime) / 60) / getChartsRangeIndex());
    if (drawLen > 0) {//条件满足后添加一条蜡烛
        addChrtsItem({
            "date": getDataTime(time),//时间需要处理
            "open": ask, "high": ask, "low": ask, "close": ask
        });
    }
    var cMoney = ask;
    if (cData.high < cMoney) {
        cData.high = cMoney;
    } else if (cData.low > cMoney) {
        cData.low = cMoney;
    }
    cData.close = cMoney;
    charts.valueAxes[0].guides[0].value = cMoney;
    charts.valueAxes[0].guides[0].label = cMoney;
    charts.dataProvider[charts.dataProvider.length - 1] = cData;//charts_data[p_code];
    charts.validateData();
}


function initChartsItem() {//初始化数据
    isTouch = false;
    isNew = true;
    chart_ptime=0;
    cData = null;
}

function setChartsEvent() {
    var show_box = document.getElementById("container");
    show_box.addEventListener('touchstart', function (event) {
        isTouch = true;
    })
    show_box.addEventListener('touchend', function (event) {
        isTouch = false;
    })
}

function setDataLast() {
    if (!charts) return;
    cData = charts.dataProvider[charts.dataProvider.length - 1];
}


function getChartsRangeIndex() {//获取k线图的索引
    var key = 1;
    if (charts_range == "1M") {
    } else if (charts_range == "M5") {
        key = 5;
    } else if (charts_range == "M15") {
        key = 15;
    } else if (charts_range == "M30") {
        key = 30;
    }
    return key;
}


function getChartsTime(date) {//k线图时间转换成时间戳
    var cdata = new Date();
    var year = cdata.getFullYear();
    var res = year + "-" + date.replace('\n', " ") + ":00";
    res=res.replace(/-/g, '/');
    var cdate=new Date(res);
    return parseInt(cdate.getTime()/1000);
}

