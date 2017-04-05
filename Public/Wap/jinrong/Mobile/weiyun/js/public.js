var isError = true;//是否能弹警告窗
//window.onbeforeunload = function () {
//    isError = false;
//    layer.closeAll();
//}
/**
 * Created by Administrator on 2016/8/8.
 */
function postMsg(url, data, fun) {
    // data = JSON.stringify(data);
    return $.ajax({
        type: "POST",
        //contentType: "application/json",
        url: url,
        data: data,
        timeout: 1000 * 60 * 3, //超时时间：15秒
        dataType: 'json',
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            if (XMLHttpRequest.status != 200) {
                netError();
            }
            return;
        },
        complete: function (XMLHttpRequest, textStatus) {
            if (textStatus == "timeout") {
                netError();
                return;
            }
        },
        success: function (res) {
            //alertMsg(JSON.stringify(res));
            if (!res || res == "") {
                //netError();
                return;
            }
            fun(res);
        }
    });

}
function netError() {
    loadhide();
    if (!isError) return;
    var error_msg = alertMsg("网络开了小差，操作会受影响哦,<br/>请您再尝试一次,将网络找回来.");
}
function getLocalTime(cellval) {
    var date = new Date(parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10));
    var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
    var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
    var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
    var Minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
    var Seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
    return date.getFullYear() + "-" + month + "-" + currentDate + " " + hour + ":" + Minutes + ":" + Seconds;
}
function loadshow() {
    try {
        if (typeof loadpagecom === "function") {
            loadpagecom(true);
        }
    } catch (e) {

    }
    $('.loader').addClass('show');
}

function loadhide() {
    $('.loader').removeClass('show')
}

function endFun() {
    console.log('关闭弹窗');
}

function alertMsg(obj) {
    if (obj.content) {
        if (!obj.className) obj.className = 'pop';
        return layer.open(obj);
    } else {
        return layer.open({
            content: obj,
            title: "提示",
            className: 'pop',
            btn: ['确定'],
            end: endFun,
        });
    }
}
function alertMsg2(obj) {
    if (obj.content) {
        if (!obj.className) obj.className = 'pop no_close';
        obj.shadeClose = false;
        return layer.open(obj);
    } else {
        return layer.open({
            content: obj,
            shadeClose: false,
            title: "提示",
            className: 'pop no_close',
            btn: ['确定'],
            end: endFun,
        });
    }
}


function alertWarnQQ(msg, callback1, callback2, close) {
    if (typeof close == "undefined") {
        close = true;
    }
    layer.open({
        className: 'popuo-login1 pop no_close',
        shadeClose: false,
        title: "提示",
        content: '<div class="mo">' + msg + '</div>', //如果警告，就调用这个dom 
        btn: ['确定', '取消'],
        yes: function (index, layero) {
            if (close)
                layer.close(index);
            if (callback1) {
                callback1(index, layero);
            }
        },
        no: function (index, layero) {
            if (callback2) {
                callback2(index, layero)
            }
        }

    });
}


function goURL(url) {
    window.location.href = url;
}
var m = {};
m.alertInfo = function (msg, callback) {
    layer.open({
        content: msg,
        className: "pop",
        title: "提示",
        btn: ['确定'],
        yes: function (index, layero) {
            layer.close(index);
            if (callback) {
                callback(index, layero);
            }
        }
    });
}
m.alertOK = function (msg, callback) {
    layer.open({
        className: 'pop',
        title: "成功提示",
        content: msg, //如果支付成功，就调用这个dom
        btn: ['确定'],
        yes: function (index, layero) {
            layer.close(index);
            if (callback) {
                callback(index, layero);
            }
        }
    });
}
m.alertError = function (msg, callback) {
    layer.open({
        content: msg,
        className: 'pop',
        title: "警告",
        btn: ['确定'],
        yes: function (index, layero) {
            layer.close(index);
            if (callback) {
                callback(index, layero);
            }
        }
    });
}


var loadIndex = 0;
m.loading = function (msg) {
    //加载层
    loadIndex = layer.open({
        type: 2,
        shadeClose: true,
        content: msg
    });
}
m.loadEnd = function () {
    layer.close(loadIndex);
}


function upfile(name, backfun) {
    var fd = new FormData();
    var ajax = new XMLHttpRequest();
    fd.append("act", 'UpLoadFile');
    fd.append("folder", 'IdentityCard');
    var imgObj = document.getElementById(name);
    /* 把文件添加到表单里 */
    fd.append("fd-file", imgObj.files[0]);
    ajax.open("post", "/Ashx/UpLoad.ashx", true);
    loadshow();
    ajax.onload = function () {
        loadhide();
        data = JSON.parse(ajax.responseText);
        if (backfun) {
            backfun(data)
        }
    };
    ajax.send(fd);
}


/**
     * 异步加载依赖的javascript文件
     * src：script的路径
     * callback：当外部的javascript文件被load的时候，执行的回调
     */
function loadAsyncScript(src, callback) {
    if ($("script[src='" + src + "']").length > 0) {
        if (callback) callback();
        return;
    }
    var head = document.getElementsByTagName("head")[0];
    var script = document.createElement("script");
    script.setAttribute("type", "text/javascript");
    script.setAttribute("src", src);
    script.setAttribute("async", false);
    head.appendChild(script);

    //fuck ie! duck type
    if (document.all) {
        script.onreadystatechange = function () {
            var state = this.readyState;
            if (state === 'loaded' || state === 'complete') {
                callback();
            }
        }
    } else {
        //firefox, chrome
        script.onload = function () {
            callback();
        }
    }
}

function getAsteriskEnd(str, showLength) {
    if (!showLength) {
        showLength = 2;
    }
    if (str.length > showLength) {
        var asterisk = str.length - showLength;
        str = str.substring(0, showLength);
        for (var i = 0; i < asterisk; i++) {
            str += "*";
        }
    }
    return str;
}

function isWeiXin() {
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
}


var dataForWeixin = {};
var shareAfter = function () {

    var wxid = $("#wxid").val() || 0;
    $.post("/Ashx/Share.ashx", { act: "ShareAfter", PageTitle: document.title, PageUrl: window.location.href, ck_wxid: wxid }, function (data) {
        var url = window.location.href.toLowerCase();
        if (!data.Success && dataForWeixin.code) {
            alertMsg({
                content: data.Message,
                shadeClose: true,
                title: "提示",
                className: 'pop',
                btn: ['马上注册'],
                yes: function () {
                    window.location.href = data.Data;
                },
            });
        }
    }, "json")
}

$(function () {
    if (isWeiXin()) {
        loadshow();
        loadAsyncScript("https://res.wx.qq.com/open/js/jweixin-1.0.0.js", function () {
            wx.ready(function () {
                wx.hideOptionMenu();
            });
            var url = "/Ashx/Share.ashx";
            var wxid = $("#wxid").val() || 0;
            var login = $.getUrlParam("login") || 0;
            $.ajax({
                type: 'POST',
                url: url,
                dataType: "json",
                data: { act: "WeiXinShare", url: window.location.href, ck_wxid: wxid, login: login },
                error: function () {
                    loadhide();
                },
                success: function (data) {
                    loadhide();
                    if (!data.Success) {
                        return;
                    }
                    data = data.Data;
                    var shareurl = data.shareurl;
                    if ($("#hfdig")[0]) {//存在，分享原始页面
                        var hfdigVal = $("#hfdig").val();//描述
                        data.Avatar = $("#hfcoverimg").val();//封面图
                        data.Meta = hfdigVal;
                        data.SiteName = document.title.trim();
                        shareurl = window.location.href;
                    }

                    dataForWeixin = {
                        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                        appId: data.appId,
                        title: data.SiteName,
                        timestamp: data.timestamp,
                        nonceStr: data.nonceStr,
                        signature: data.signature,
                        url: shareurl,
                        MsgImg: data.Avatar,
                        desc: data.Meta,
                        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', "closeWindow", "showOptionMenu", 'hideOptionMenu'],
                        fakeid: "",
                        login: data.login,
                        code: data.Code,
                        callback: function () { }
                    };

                    initWx();
                }
            });
        });
    }
});




function initWx() {
    wx.config({
        debug: dataForWeixin.debug, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: dataForWeixin.appId, // 必填，公众号的唯一标识
        timestamp: dataForWeixin.timestamp, // 必填，生成签名的时间戳
        nonceStr: dataForWeixin.nonceStr, // 必填，生成签名的随机串
        signature: dataForWeixin.signature, // 必填，签名，见附录1
        jsApiList: dataForWeixin.jsApiList  // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });

    wx.ready(function () {
        wx.showOptionMenu();
        //在此输入各种API
        //分享到朋友圈
        wx.onMenuShareTimeline({
            title: dataForWeixin.title, // 分享标题
            link: dataForWeixin.url, // 分享链接
            imgUrl: dataForWeixin.MsgImg, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                if (typeof shareAfter === "function") {
                    shareAfter();
                }
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //分享给朋友
        wx.onMenuShareAppMessage({
            title: dataForWeixin.title, // 分享标题
            desc: dataForWeixin.desc, // 分享描述
            link: dataForWeixin.url, // 分享链接
            imgUrl: dataForWeixin.MsgImg, // 分享图标
            type: '', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
                if (typeof shareAfter === "function") {
                    shareAfter();
                }
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //QQ
        wx.onMenuShareQQ({
            title: dataForWeixin.title, // 分享标题
            desc: dataForWeixin.desc, // 分享描述
            link: dataForWeixin.url, // 分享链接
            imgUrl: dataForWeixin.MsgImg, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                if (typeof shareAfter === "function") {
                    shareAfter();
                }
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //QQ微博
        wx.onMenuShareWeibo({
            title: dataForWeixin.title, // 分享标题
            desc: dataForWeixin.desc, // 分享描述
            link: dataForWeixin.url, // 分享链接
            imgUrl: dataForWeixin.TLImg, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
                if (typeof shareAfter === "function") {
                    shareAfter();
                }
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，
        //所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
    });
    wx.error(function (res) {
        // alertMsg("网络开了小差，操作会受影响哦,<br/>请您再尝试一次,将网络找回来.")
        //alert(res);
        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
    });
}



// 对Date的扩展，将 Date 转化为指定格式的String
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符， 
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字) 
// 例子： 
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423 
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18 
Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}


$(function () {
    try {
        if (window.top.location !== window.location) {
            top.location.href = self.location.href;
        }
        var maxNum = 5;
        var index = 0;
        var timer = setInterval(function () {
            $("iframe").not($("iframe[id^='__WeixinJS']")).remove();
            if (index > maxNum)
                clearInterval(timer);
            index++;
        }, 300);
    } catch (e) {
        console.log('删除iframe错误!');
    }
});

/*前台获取url参数*/
(function ($) {
    $.getUrlParam = function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    }
})(jQuery);

function loadImage(id, src, callback) {
    var imgloader = new window.Image();
    //当图片成功加载到浏览器缓存
    imgloader.onload = function (evt) {
        loadshow();
        if (typeof (imgloader.readyState) == 'undefined') {
            imgloader.readyState = 'undefined';
        }
        //在IE8以及以下版本中需要判断readyState而不是complete
        if ((imgloader.readyState == 'complete' || imgloader.readyState == "loaded") || imgloader.complete) {
            callback({ 'msg': 'ok', 'src': src, 'id': id });
            loadhide();
        } else {
            imgloader.onreadystatechange(evt);
        }
    };
    //当加载出错或者图片不存在
    imgloader.onerror = function (evt) {
        callback({ 'msg': 'error', 'id': id });
    }
    //当加载状态改变                       
    imgloader.onreadystatechange = function (e) {
        //此方法只有IE8以及一下版本会调用		
    }

    imgloader.src = src;

}