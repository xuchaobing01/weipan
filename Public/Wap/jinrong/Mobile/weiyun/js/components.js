!function (t) {
    function i(s) {
        if (e[s])return e[s].exports;
        var n = e[s] = {exports: {}, id: s, loaded: !1};
        return t[s].call(n.exports, n, n.exports, i), n.loaded = !0, n.exports
    }

    var e = {};
    return i.m = t, i.c = e, i.p = "F:/", i(0)
}([function (t, i, e) {
    e(4), e(3), e(1), e(2), e(5)
}, function (t, i) {
    Vue.component("pop-list-component", {
        template: '<div class="pop-panel" id="order" v-bind:class="{\'show\':show}"><div class="pop-box"><div class="pop-title">{{state==-1?"盈战中":"盈战结束"}}<a class="btn close" @click="this.show=false"><i class="iconfont icon-guanbi"></i></a></div><div class=" pop_order" ><div class="box active" ><div class="top"><div class="left">合约：{{product}}</div> <div class="left" style="margin-left:20px">定金：{{deposit}}</div></div><div class="content" v-if="state==-1"><h2 class="text-red" v-if="select_time>=0"><template v-if="select_time>0" >{{parseInt(select_time+0.999)}}</template><template v-if="select_time<=0" >{{select_time.toFixed(1)}}</template><!--.<small>{{mic}}</small>--></h2><h2 class="text-red" v-if="select_time<0">结算中</h2><div>执行价格：{{open_money}}</div><div>当前价格：<span v-bind:class="{\'text-red\':forecast==1,\'text-green\':forecast==2}" >{{current_money}}</span></div><div class="attrs"><div>订单方向：<span class="text-red" v-if="dir==1">买涨 ▲</span><span class="text-green" v-if="dir==2">买跌 ▼</span></div><div>预测结果： <template v-if="select_time>=0" ><span class="text-red" v-if="forecast==1">盈</span><span class="text-green" v-if="forecast==2">亏</span><span v-if="forecast==0">平</span></template><span v-if="select_time<0" >结算中</span></div></div></div><div class="content" v-if="state!=-1"><h2 class="text-red" v-if="state==1">盈 +{{(parseFloat(deposit)+parseFloat(profit)).toFixed(2)}}</h2><h2 class="text-green" v-if="state==2">亏 {{profit}}</h2><h2 v-if="state==0">平 {{profit}}</h2><div>执行价格：{{open_money}}</div><div>平仓价格：<span v-bind:class="{\'text-red\':state==1,\'text-green\':state==2}" >{{close_money}}</span></div><div class="attrs"><div>订单方向：<span class="text-red" v-if="dir==1">买涨 ▲</span><span class="text-green" v-if="dir==2">买跌 ▼</span></div><div>预测结果： <span class="text-red" v-if="state==1">盈</span><span class="text-green" v-if="state==2">亏</span><span v-if="state==0">平</span></div></div></div><div class="row"> <a class="btn btn-primary close" style="margin-top:5px;" @click="this.show=false">继续下单</a></div></div> </div><div class="bg"></div></div>',
        data: function () {
            return this.init(), {
                show: !1,
                state: -1,
                oid: 0,
                type_name: null,
                select_time: -1,
                open_money: 0,
                current_money: 0,
                dir: 1,
                forecast: 0,
                money_trend: 0,
                open_time: null,
                sec: 0,
                mic: 0,
                is_count_down: !0,
                time_dif: 0,
                deviation: 0,
                close_time_num: 0,
                close_time: null,
                close_money: 0,
                profit: 0,
                cmoney: 0,
                product: null,
                deposit: null,
                results: []
            }
        },
        events: {
            openlist: function (t) {
                this.select_time = -1, this.state = -1, this.cmoney = 0, this.oid = t.oid, this.dir = t.swl, this.product = t.product, this.deposit = parseFloat(t.deposit).toFixed(2), this.type_name = t.type_name, this.open_money = parseFloat(t.open_money).toFixed(decimal_list[this.type_name]), this.open_time = t.open_time, this.close_time = t.close_time, this.select_time = parseInt(new Date(this.close_time).getTime() - new Date(t.current_time).getTime()) / 1e3, this.deviation = .7 / (parseInt(new Date(this.close_time).getTime() - new Date(this.open_time).getTime()) / 1e3), this.time_dif = new Date(t.current_time).getTime() - (new Date).getTime(), this.is_count_down = !0, this.close_time_num = new Date(this.close_time).getTime(), this.current_money = this.open_money, this.show = !0
            }, money_change: function (t) {
                this.show && (0 == this.cmoney ? this.current_money = t[this.type_name][0] : this.current_money = this.cmoney, 1 == this.dir && this.current_money > this.open_money || 2 == this.dir && this.current_money < this.open_money ? this.forecast = 1 : 1 == this.dir && this.current_money < this.open_money || 2 == this.dir && this.current_money > this.open_money ? this.forecast = 2 : this.forecast = 0)
            }, add_result: function (t) {
                this.results.push(t), 0 != this.oid && this.oid == t.TradingLimitedID && (this.cmoney = t.ClosePrice.toFixed(2)), 0 != this.show && 0 != this.oid && this.oid == t.TradingLimitedID || this.$dispatch("show_order_close", t)
            }, close: function (t) {
                this.show = !1
            }
        },
        methods: {
            init: function () {
                setInterval(this.count_down, 100)
            }, count_down: function () {
                if (this.show && this.is_count_down) {
                    if (this.select_time < 0)return this.is_count_down = !1, void setTimeout(this.getresult, 800);
                    this.close_time_num = this.close_time_num + 1e3 * this.deviation / 10, this.select_time = (this.close_time_num - ((new Date).getTime() + this.time_dif)) / 1e3
                }
            }, update: function () {
                this.show
            }, getOrder: function (t) {
                for (var i = 0; i < this.results.length; i++) {
                    var e = this.results[i];
                    if (t == e.TradingLimitedID)return e
                }
                return null
            }, getresult: function () {
                this.getclient() && setTimeout(this.getTimeResult, 1e3)
            }, getclient: function () {
                var t = this, i = this.getOrder(this.oid);
                if (i) {
                    var e = i.Profit;
                    return parseFloat(i.Profit) > 0 && (i.Profit = parseFloat(i.Profit) - this.deposit), t.showResult(i), t.$dispatch("show_order_close", {
                        Symbol: i.Symbol,
                        Profit: e,
                        ProfitFlag: i.ProfitFlag
                    }), !1
                }
                return !0
            }, getTimeResult: function () {
                if (this.getclient()) {
                    var t = this;
                    postMsg("Ashx/TradingLimited.ashx", {
                        Act: "GetTradingLimitedHistory",
                        OrderID: this.oid
                    }, function (i) {
                        i.TradingLimitedID > 0 && t.showResult(i)
                    })
                }
            }, showResult: function (t) {
                var i = this;
                i.profit = parseFloat(t.Profit).toFixed(2);
                var e = t.OpenPrice;
                i.close_money = parseFloat(t.ClosePrice).toFixed(2), 1 == i.dir && i.close_money > e || 2 == i.dir && i.close_money < e ? i.state = 1 : 1 == i.dir && i.close_money < e || 2 == i.dir && i.close_money > e ? i.state = 2 : i.state = 0
            }, close: function () {
                this.show = !1
            }
        }
    })
}, function (t, i) {
    Vue.component("history-list-component", {
        template: ' <table><thead> <tr><td>合约</td><td>方向</td><td>平仓时间</td><td>订金</td><td>盈利状况</td><td>状态</td></tr></thead><tbody><template v-for="item in list"><tr class="mt" @click="select($index)" ><td>{{item.Symbol}}</td><td><span class="text-red" v-if="item.Action==1">涨</span><span class="text-green" v-if="item.Action==2">跌</span></td><td>{{item.close_time}}</td><td>{{item.Amount}}</td> <td ><span class="text-red" v-if="item.static==1">+{{item.Profit2}}</span> <span class="text-green" v-if="item.static==2">{{item.Profit}}</span><span v-if="item.static==0">{{item.Profit}}</span></td><td  class="text-red" v-if="item.static==1" >盈利</td><td class="text-green" v-if="item.static==2" >亏损</td><td  v-if="item.static==0">平局</td></tr> <tr v-if="active==$index"><td colspan="6"><table class="open"><tr><td colspan="2">订单号：{{ConvertJSONOrderID(item.OpenTime)+item.TradingLimitedID}}</td></tr><tr><td>开仓时间：{{item.open_time}}</td><td>周期：{{item.TimeTypeStr}}</td></tr><tr><td>开仓价格：{{item.OpenPrice}}</td><td>平仓价格：{{item.ClosePrice}}</td></tr></table></td></tr></template></tbody></table><a class="bottom_more" href="/History.aspx" v-if="list!=null && list.length!=0">查看更多</a><p class="nodata" v-if="list==null || list.length==0">暂无数据</p>',
        data: function () {
            return {show: !1, list: null, isupdate: !0, active: -1}
        },
        events: {
            tab_change: function (t) {
                2 == t.index ? (this.show = !0, (null == this.list || this.isupdate) && this.getlist()) : this.show = !1
            }, history_update: function (t) {
                this.show ? this.getlist() : this.isupdate = !0
            }
        },
        methods: {
            getlist: function () {
                var t = this;
                postMsg("Ashx/TradingLimited.ashx", {Act: "GetLsTradingLimitedHistoryTop"}, function (i) {
                    if (!i.List)return null;
                    for (var e = 0; e < i.List.length; e++) {
                        i.List[e].close_time = getLocalTime(i.List[e].CloseTime).substr(5), i.List[e].open_time = getLocalTime(i.List[e].OpenTime).substr(5), i.List[e].OpenPrice = i.List[e].OpenPrice.toFixed(i.List[e].DecimalDigit), i.List[e].ClosePrice = i.List[e].ClosePrice.toFixed(i.List[e].DecimalDigit), i.List[e].Profit = parseFloat(i.List[e].Profit).toFixed(2), i.List[e].Amount = parseFloat(i.List[e].Amount).toFixed(2), i.List[e].Profit2 = (parseFloat(i.List[e].Profit) + parseFloat(i.List[e].Amount)).toFixed(2);
                        var s = i.List[e].Action, n = i.List[e].OpenPrice, o = i.List[e].ClosePrice;
                        1 == s && o > n || 2 == s && o < n ? i.List[e]["static"] = 1 : 1 == s && o < n || 2 == s && o > n ? i.List[e]["static"] = 2 : i.List[e]["static"] = 0
                    }
                    t.list = i.List, t.isupdate = !1
                })
            }, select: function (t) {
                this.active = this.active == t ? -1 : t
            }, ConvertJSONOrderID: function (t) {
                var i = $.ConvertJSONOrderID(t);
                return i
            }
        }
    })
}, function (t, i) {
    Vue.component("pop-buy-component", {
        template: '<div class="pop-panel" id="buy" v-bind:class="{\'show\':this.show}"><div class="pop-box"><div class="pop-title">购买<a class="btn close" @click="this.show=false"><i class="iconfont icon-guanbi"></i></a></div><div class="pop-content pop_buy"><div class="btn-gards" ><div class="mask" v-if="!is_momney"></div><div class="btn-row l_4" ><a v-for="item in btns" @click="select_money(item)" v-bind:class="{\'active\':this.buy_money==item && is_momney}" >{{item}}元</a><a><input type="number" placeholder="其它金额" v-on:input="tinput($event)" v-on:focus="tfocus($event)"    v-on:blur="tblur($event)"  v-model="other_money" /></a></div> </div><div class="select-gards" ><div style="margin-top:10px;" v-if="ticket_list!=null && ticket_list.length>0">使用优惠券</div><div class="select-row l_3"><template v-for="item in ticket_list"><div v-bind:class="{\'active\':ticket_index==$index,\'number\':item.Number>1,\'disabled\':item.Number==0}"><div><a @click="select_ticket($index)" >{{item.Activity_type_name}}<small>{{item.Coupon_amount}}元 {{item.NumberTotals}}张</small><span  v-if="item.Number>1 && ticket_index==$index">{{item.Number}}</span></a><input v-if="item.Number>1 " @input="ticket_change(item.Number)" type="number" v-model="ticket_number" class="ticket_number"/></div></div></template></div> </div><div class="prompt_box"><div class="prompt" v-bind:class="{\'show\':prompts!=\'\',\'hide\':prompts==\'\'}">{{{prompts}}}</div></div><div class="buy_title">购买：{{buy_money.toFixed(2)}}元<span>预期收入：{{think_money.toFixed(2)}}元</span></div><div class="attrs"><div>合约：{{select_good}}</div><div>结算周期：{{select_time}}秒</div><div>订单方向：<span class="text-red" v-if="swl==1">买涨 ▲</span><span class="text-green" v-if="swl==2">买跌 ▼</span></div><div class="text-green" v-if="dir==2">当前价格：{{c_money}} ▼ </div><div class="text-red" v-if="dir==1">当前价格：{{c_money}} ▲ </div></div><div class="footer"><a class="btn btn-primary define" @click="define()" v-bind:class="{\'bind\':!buy_btn_act}">{{buy_btn_str}}</a></div></div></div><div class="bg"></div></div>',
        data: function () {
            return {
                tid: 0,
                show: !1,
                buy_money: 0,
                think_money: 0,
                select_time: 0,
                select_good: "",
                other_money: "",
                btns: [],
                bl: 0,
                dir: 1,
                swl: 1,
                c_money: 0,
                type_name: null,
                buy_btn_str: "确定",
                buy_btn_act: !0,
                isbuy: !0,
                buy_active: !0,
                ticket_list: null,
                ticket_index: null,
                ticket_number: 1,
                is_momney: !0,
                p_money: null,
                prompts: "",
                prompt_time: null
            }
        },
        events: {
            openbuy: function (t) {
                var i = this;
                loadshow(), postMsg("/Ashx/TradingLimited.ashx", {
                    act: "CheckTranTime",
                    Type: 1,
                    ContractTypeID: t.cid
                }, function (e) {
                    loadhide(), e.Success ? (i.buy_active = !0, i.buy_btn_act = !0, i.isbuy = !0, i.buy_btn_str = "确定") : (i.buy_active = !1, i.buy_btn_act = !1, i.isbuy = !1, i.buy_btn_str = "该合约还未开盘"), i.data_init(t)
                })
            }, money_change: function (t) {
                this.show && (this.c_money = t[this.type_name][0], this.dir = t[this.type_name][1])
            }, close: function (t) {
                this.show = !1
            }
        },
        methods: {
            data_init: function (t) {
                this.btns = t.btns.split(",").reverse(), this.buy_money = 10, this.select_time = t.select_time, this.select_good = t.select_good, this.swl = t.swl, this.bl = t.bl / 100, this.c_money = t.c_money, this.other_money = "", this.tid = t.tid, this.type_name = t.type_name, this.p_money = 100, this.is_momney = !0, this.ticket_index = null, this.ticket_list = null, this.select_money(100), this.show = !0
            }, ticket_change: function (t) {
                this.ticket_number > t && (this.ticket_number = t), this.ticket_number && this.ticket_number < 1 && (this.ticket_number = 1), this.buy2_change()
            }, prompt_hide: function () {
                this.prompts = ""
            }, select_ticket: function (t) {
                var i = this.ticket_list[t];
                if (this.prompts = i.Message + "", "" != this.prompt_hide && (this.prompt_time && clearTimeout(this.prompt_time), this.prompt_time = setTimeout(this.prompt_hide, 3e3)), 0 != i.Number) {
                    if (this.ticket_index == t ? (this.ticket_index = null, this.is_momney = !0, this.buy_change(this.p_money)) : this.ticket_index = t, null != this.ticket_index) {
                        var e = this.ticket_list[this.ticket_index], s = e.Number;
                        this.ticket_number = s, this.is_momney && (this.p_money = this.buy_money), 2 != e.Activity_type_id ? (this.is_momney = !1, this.buy_change(e.Coupon_amount)) : (this.is_momney = !0, this.buy_change(this.p_money))
                    }
                    this.buy2_change()
                }
            }, get_ticket: function () {
                if (this.buy_active) {
                    var t = this;
                    this.ticket_index = null, this.ticket_list = null, this.ticket_number = null, loadshow(), postMsg("/Ashx/UserCoupon.ashx", {
                        act: "GetViewUserCouponInfo",
                        tlid: this.tid,
                        timetype: this.select_time / 60,
                        money: this.buy_money
                    }, function (i) {
                        loadhide(), t.ticket_list = i.List
                    })
                }
            }, tinput: function (t) {
                var i = parseFloat(t.target.value);
                i && this.buy_change(i)
            }, tfocus: function (t) {
                this.isbuy = !1
            }, tblur: function (t) {
                loadshow(), this.isbuy = !0, this.tinput(t), this.get_ticket(), this.buy2_change()
            }, select_money: function (t) {
                t = parseFloat(t), this.buy_money = t, this.get_ticket(), this.buy2_change()
            }, buy_change: function (t) {
                t = parseFloat(t), this.buy_money = t, this.buy2_change()
            }, buy2_change: function () {
                if (this.think_money = this.buy_money + this.buy_money * this.bl, null != this.ticket_index) {
                    var t = this.ticket_list[this.ticket_index].Activity_type_id;
                    if (2 == t) {
                        var i = this.ticket_list[this.ticket_index].Coupon_amount;
                        this.think_money = this.think_money + i * this.bl * this.ticket_number
                    } else {
                        var i = this.ticket_list[this.ticket_index].Coupon_amount;
                        this.think_money = this.think_money - i * this.ticket_number
                    }
                }
            }, close: function () {
                this.show = !1
            }, define: function () {
                if (this.isbuy) {
                    loadshow();
                    var t = this, i = null;
                    null != this.ticket_index && (i = this.ticket_list[this.ticket_index].Activity_id), postMsg("Ashx/TradingLimited.ashx", {
                        Act: "AddTradingLimited",
                        tlid: this.tid,
                        action: this.swl,
                        timetype: this.select_time / 60,
                        fr: 10,
                        price: this.c_money,
                        money: this.buy_money,
                        activityid: i,
                        couponnumber: this.ticket_number
                    }, function (i) {
                        if (i.Success) {
                            loadhide(), t.close();
                            var e = {
                                product: t.select_good,
                                deposit: i.OrderMoney,
                                current_time: i.CurrentTime.replace(/-/g, "/"),
                                swl: t.swl,
                                open_money: i.PriceCurrent,
                                open_time: i.OpenTime.replace(/-/g, "/"),
                                type_name: t.type_name,
                                close_time: i.CloseTime.replace(/-/g, "/"),
                                oid: i.OrderID
                            };
                            t.$dispatch("openlist", e), t.$dispatch("update_user_money", {money: i.Balance}), t.$dispatch("reduce_couponnumber", {number: t.ticket_number})
                        } else loadhide(), 32 == i.Result ? alertMsg({
                                content: i.Msg,
                                title: "提示",
                                btn: ["去充值", "取消"],
                                yes: function (t) {
                                    location.href = "/Recharge.aspx"
                                }
                            }) : alertMsg(i.Msg)
                    })
                }
            }
        }
    })
}, function (t, i) {
    Vue.component("position-list-component", {
        template: ' <table><thead><tr><td>合约</td><td>方向</td><td>开仓时间</td><td>开仓价格</td><td>当前价格</td><td>订金</td></tr></thead><tbody><tr class="mz" v-for="item in list" @click="list_open(item)"><td>{{item.Symbol}}</td><td><span class="text-red" v-if="item.Action==1">涨</span><span class="text-green" v-if="item.Action==2">跌</span></td><td>{{item.open_time}}</td><td>{{item.OpenPrice}}</td> <td v-bind:class="{\'text-red\':type_prices[item.TypeName][1]==1,\'text-green\':type_prices[item.TypeName][1]==2}" >{{type_prices[item.TypeName][0]}}</td><td>{{item.Amount}}</td></tr> </tbody></table><p class="nodata" v-if="list==null || list.length==0">暂无数据</p>',
        data: function () {
            return setInterval(this.update_time, 1e3), {
                show: !1,
                isInit: !1,
                list: null,
                server_interval: 0,
                type_prices: null,
                c_time: 0
            }
        },
        events: {
            tab_change: function (t) {
                1 == t.index ? (this.show = !0, this.isInit || (this.getlist(), this.isInit = !0)) : this.show = !1
            }, money_change: function (t) {
                this.type_prices = t
            }, position_update: function (t) {
                this.getlist()
            }
        },
        methods: {
            update_time: function () {
                if (this.c_time = this.get_current_time(), this.list)for (var t = 0; t < this.list.length; t++) {
                    var i = this.list[t], e = new Date(i.CloseTime), s = new Date(this.c_time);
                    s > e && (this.list.splice(t, 1), setTimeout(this.dispMsg, 2e3))
                }
            }, get_current_time: function () {
                return new Date((new Date).getTime() + this.server_interval).Format("yyyy/MM/dd hh:mm:ss")
            }, getlist: function () {
                var t = this;
                postMsg("Ashx/TradingLimited.ashx", {Act: "GetUserTradingLimited"}, function (i) {
                    if (i.Success) {
                        var e = i.CurrDateTime.replace(/-/g, "/");
                        if (t.server_interval = new Date(e).getTime() - (new Date).getTime(), !i.LsUserTradingLimitedInfo)return null;
                        for (var s = 0; s < i.LsUserTradingLimitedInfo.length; s++) {
                            var n = i.LsUserTradingLimitedInfo[s];
                            i.LsUserTradingLimitedInfo[s].result = -1, i.LsUserTradingLimitedInfo[s].close_money = 0, i.LsUserTradingLimitedInfo[s].Profit = 0, i.LsUserTradingLimitedInfo[s].open_time = n.OpenTime.substr(5), i.LsUserTradingLimitedInfo[s].CloseTime = n.CloseTime.replace(/-/g, "/"), i.LsUserTradingLimitedInfo[s].OpenTime = n.OpenTime.replace(/-/g, "/"), i.LsUserTradingLimitedInfo[s].OpenPrice = n.OpenPrice.toFixed(decimal_list[n.TypeName])
                        }
                        t.list = i.LsUserTradingLimitedInfo
                    }
                })
            }, dispMsg: function () {
                this.$dispatch("history_update", {})
            }, list_open: function (t) {
                var i = {
                    product: t.Symbol,
                    deposit: t.Amount,
                    current_time: this.get_current_time(),
                    swl: t.Action,
                    open_money: t.OpenPrice,
                    open_time: t.OpenTime.replace(/-/g, "/"),
                    type_name: t.TypeName,
                    close_time: t.CloseTime.replace(/-/g, "/"),
                    oid: t.TradingLimitedID
                };
                this.$dispatch("opencurrent", i)
            }
        }
    })
}, function (t, i) {
    Vue.component("guide-component", {
        template: '<div class="guide_btn"><a class="close"></a><template v-if="webstatus==1 && live_url!=\'\'"><img src="/Style/imgs/zb.png" @click="tolive()" class="" ></template> <template v-if="webstatus==2"><img src="/Style/imgs/guide.png" @click="toActive(1)"  ></template></div><template v-if="active>0"><div class="guide guide_box"  v-if="active!=0 && active!=7" id="guide" v-bind:class="{\'step1\':active==1,\'step2\':active==2,\'step3\':active==3,\'step4\':active==4,\'step5\':active==5,\'step6\':active==6}" ><div class="girl"><img v-if="active==1 || active==5"  src="/Style/imgs/girl.png"><img v-if="active==2 || active==6"  src="/Style/imgs/girl2.png"></div> <img src="/Style/imgs/wz1.png" v-bind:src="\'/Style/Themes/\'+path+\'/wz1.png\'" class="guide_wz1" v-if="active==1" ><div class="sign"><img src="/Style/imgs/arrow.png"></div><div class="prompt">{{{msg}}}<div class="btns_2"><a class="btn btn-primary prive" v-if="active>1" @click="toActive(active-1)">上一步</a><a v-if="active<7"  class="btn btn-primary next" @click="toActive(active+1)">下一步</a></div></div><div class="bg1" ></div><div class="bg2" ></div><img v-if="active==4" src="/Style/imgs/tk1.gif" class="tk" /><img v-if="active==5" src="/Style/imgs/tk2.gif" class="tk" /><img v-if="active==6" src="/Style/imgs/tk3.gif" class="tk" /><div class="mask2"  @click="windclick()" ></div></div><div v-if="active==7" class="guide_end"><div class="contents"><img src="/Style/imgs/ani.png" ><img src="/Style/imgs/wz2.png" class="guide_wz2" ></div><a class="btn define" @click="windclick()"  >确定</a></div></template>',
        data: function () {
            window.location.host;
            return {live_url: liveRoomUrl, webstatus: 1, active: 0, path: systemThemes, msg: "", b_f: null}
        },
        events: {
            guide: function (t) {
                t && t.bF && (this.b_f = t.bF), 0 == this.active && this.toActive(1)
            }, sel_good: function (t) {
                1 == this.active && this.toActive(2)
            }, sel_time: function (t) {
                2 == this.active && this.toActive(3)
            }
        },
        methods: {
            tolive: function () {
                location.href = "http://" + this.live_url
            }, windclick: function () {
                switch (this.active) {
                    case 1:
                        this.toActive(2);
                        break;
                    case 2:
                        this.toActive(3);
                        break;
                    case 3:
                        this.toActive(4);
                        break;
                    case 4:
                        this.toActive(5);
                        break;
                    case 5:
                        this.toActive(6);
                        break;
                    case 6:
                        this.toActive(7);
                        break;
                    case 7:
                        this.$dispatch("show_head_ad", {}), this.active = 0, this.unlock(), this.b_f && this.b_f()
                }
            }, toActive: function (t) {
                switch (this.active = t, this.active) {
                    case 1:
                        this.$dispatch("hide_head_ad", {}), this.msg = "<h2>第一步</h2>根据必盈券使用规则，选择比特币。", this.toTop(0), this.lock();
                        break;
                    case 2:
                        this.msg = "<h2>第二步</h2>选择时长60秒。", this.toTop(0), this.lock();
                        break;
                    case 3:
                        this.msg = "<h2>第三步</h2>判断比特币当前走势，选择买涨或买跌。", this.toTop(0), this.lock();
                        break;
                    case 4:
                        this.msg = "<h2>第四步</h2>选择必盈券并确定下单。", this.toTop(0), this.lock();
                        break;
                    case 5:
                        this.msg = "<h2>第五步</h2>下单成功，请静待结果。", this.toTop(0), this.lock();
                        break;
                    case 6:
                        this.msg = "<h2>第六步</h2>60秒倒计时结束，恭喜您用10元必盈券成功盈利8元。", this.toTop(0), this.lock()
                }
                $("#guide").removeClass("guide"), setTimeout('$("#guide").addClass("guide")', 10)
            }, lock: function () {
                $("body").addClass("guide_body"), $("html").addClass("guide_body")
            }, unlock: function () {
                $("body").removeClass("guide_body"), $("html").removeClass("guide_body")
            }, getPosition: function (t) {
                var i = $(t).offset().top, e = $(t).offset().left;
                return {x: e, y: i}
            }, toObj: function (t) {
                var i = $(t).offset().top;
                this.toTop(i)
            }, toTop: function (t) {
                document.body.scrollTop = t
            }
        }
    })
}]);