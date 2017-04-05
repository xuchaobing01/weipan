/// <reference path="jquery-1.10.2.min.js" />

function hasCookie() {
    if (!navigator.cookieEnabled) {
        $('#err-msg').text('您的手机浏览器不支持或已经禁止使用cookie，无法正常登录，请开启或更换其他浏览器').show();
    }
}

function numKeyPress(e) {
    var k = e.keyCode || e.which;
    if (k >= 48 && k <= 57 || k == 8) {
        return true;
    }
    return false;
}

function getParam(paramName) {
    paramValue = "";
    isFound = false;
    if (this.location.search.indexOf("?") == 0 && this.location.search.indexOf("=") > 1) {
        arrSource = unescape(this.location.search).substring(1, this.location.search.length).split("&");
        i = 0;
        while (i < arrSource.length && !isFound) {
            if (arrSource[i].indexOf("=") > 0) {
                if (arrSource[i].split("=")[0].toLowerCase() == paramName.toLowerCase()) {
                    paramValue = arrSource[i].split("=")[1];
                    isFound = true;
                }
            }
            i++;
        }
    }
    return paramValue;
}

//移除url参数
function removeQueryParam(url, parameter) {
    var urlparts = url.split('?');

    if (urlparts.length >= 2) {
        var urlBase = urlparts.shift(); //get first part, and remove from array
        var queryString = urlparts.join("?"); //join it back up

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = queryString.split(/[&;]/g);
        for (var i = pars.length; i-- > 0;)               //reverse iteration as may be destructive
            if (pars[i].lastIndexOf(prefix, 0) !== -1)   //idiom for string.startsWith
                pars.splice(i, 1);
        url = urlBase + '?' + pars.join('&');
    }
    return url;
}

$.extend({
    //金额格式化
    formatMoney: function (num, n) {
        var digit = n ? n : 2,
            divisor = Math.pow(10, digit);
        num = Math.round(num * divisor) / divisor;
        return num.toFixed(n ? n : 2);
    },
    ConvertJSONDateToJSDateObject: function (cellval) {
        var date = new Date(parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10));
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var Minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        var Seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
        return date.getFullYear() + "-" + month + "-" + currentDate + " " + hour + ":" + Minutes + ":" + Seconds;
    },
    ConvertJSONDateToJSDateHourMinute: function (cellval) {
        var date = new Date(parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10));
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var Minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        return date.getFullYear() + "-" + month + "-" + currentDate + " " + hour + ":" + Minutes;
    },
    ConvertJSONDateToJSDatecurrentDate: function (cellval) {
        var date = new Date(parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10));
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        return currentDate;
    },
    ConvertJSONDateToJSDateInt: function (cellval) {
        var date = parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10);
        return date;
    },
    ConvertJSONDateToJSDate: function (cellval) {
        var date = new Date(parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10));
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        return date.getFullYear() + "-" + month + "-" + currentDate;
    },
    ConvertJSONDateToData: function (cellval) {
        var date = new Date(parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10));
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var Minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        var Seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
        return month + "-" + currentDate;
    },
    ConvertJSONDateToHour: function (cellval) {
        var date = new Date(parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10));
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var Minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        var Seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
        return hour + ":" + Minutes;
    },
    ConvertJSONOrderID: function (cellval) {
        var date = new Date(parseInt(cellval.replace("/Date(", "").replace(")/", ""), 10));
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
        var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var Minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        var Seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();

        var Milliseconds = date.getMilliseconds();
        if (date.getMilliseconds() < 10) {
            Milliseconds = "00" + date.getMilliseconds();
        }

        if (date.getMilliseconds() > 10 && date.getMilliseconds() < 100) {
            Milliseconds = "0" + date.getMilliseconds();
        }

        return "" + date.getFullYear() + month + currentDate + hour + Minutes + Seconds + Milliseconds;
    },
    WhereOrdersPoint: function (group, symbol, opentime, bl, datalist) {
        var op;
        if (!bl)//下单
        {
            op = $.grep(datalist, function (n) {
                return n.Group == group;
            });
        }
        else //平仓
        {
            var symbollist = $.grep(datalist, function (n) {
                return n.Symbol == symbol;
            });
            //取得最新配置强平数据
            if (symbollist.length > 1) {
                $.each(symbollist, function (i, n) {
                    if (opentime >= $.ConvertJSONDateToJSDateInt(n.CreateTime)) {
                        op = n;
                        return false;
                    }
                });
            }
            else {
                op = $.grep(datalist, function (n) {
                    return (n.Group == group && n.Symbol == symbol);
                });
                op = op[0];
            }
        }
        return op;
    },
    GetDateStr: function (AddDayCount) {
        var dd = new Date();
        dd.setDate(dd.getDate() + AddDayCount);//获取AddDayCount天后的日期
        var y = dd.getFullYear();
        var m = dd.getMonth() + 1;//获取当前月份的日期
        var d = dd.getDate();
        return y + "-" + m + "-" + d;
    }
});

// 方法四,数字千分位
function toThousands(num, cent, isThousand) {
    num = num.toString().replace(/\$|\,/g, '');
    var iszeao;
    // 检查传入数值为数值类型  
    if (isNaN(num))
        num = "0";
    // 获取符号(正/负数)  
    sign = (num == (num = Math.abs(num)));

    num = Math.floor(num * Math.pow(10, cent) + 0.50000000001);  // 把指定的小数位先转换成整数.多余的小数位四舍五入  
    cents = num % Math.pow(10, cent);              // 求出小数位数值  
    num = Math.floor(num / Math.pow(10, cent)).toString();   // 求出整数位数值  
    cents = cents.toString();               // 把小数位转换成字符串,以便求小数位长度  

    // 补足小数位到指定的位数  
    while (cents.length < cent)
        cents = "0" + cents;
    if (isThousand) {
        // 对整数部分进行千分位格式化.  
        for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3) ; i++)
            num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
    }
    if (cent > 0)
        iszeao = (((sign) ? '' : '-') + num + '.' + cents);
    else
        iszeao = (((sign) ? '' : '-') + num);
    return iszeao;
}