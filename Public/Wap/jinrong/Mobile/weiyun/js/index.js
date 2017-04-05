//vue初始化
var vue;
function vue_init() {
    vue = new Vue({
        el: "#app",
        data: {
            placard: "公告",
            user_money: 0,
            // pdata: pageData,//页面数据
            // couponnumber: couponnumber,//卷数量
            pi: 0,//当前页面索引
            select_good: "",//选择的产品名称
            select_time: 0,//选择的时间
            dir: true,//当前选中方向 true 涨 false 跌
            map_time: "M1",//分时图选择
            type_prices: null,//当前所有价格列表
            type_name: "BTCCNY",//选择的产品类型
            people_num: 0,//参与人数
            transactions_num: 0,//买卖次数
            head_ad: true,//头部广告显示状态
            ticket_list: null,
            ticket_index: null,
            ticket_number: 1,
            buy_money: 0,
            //headerImg: headerImg,//头像
            //server_interval: server_interval,//服务器与本地的时间差
            //cur_time: 0,//当前时间
        },
        events: {
            'openlist': function (msg) {//监听是否弹出持仓单
                //if (msg.current_time) {
                //    //this.cur_time = msg.current_time.replace(/-/g, '/');//更新当前时间
                //    var time_str = msg.current_time.replace(/-/g, '/');
                //    var s_i = new Date(time_str).getTime() - new Date().getTime();
                //    this.server_interval = s_i;
                //}
                var self = this;
                this.$broadcast('openlist', msg);
                this.$broadcast('money_change', this.type_prices);//发送改变金额
                this.$broadcast('position_update', msg);//下单成功更新持仓单列表               
            },
            'opencurrent': function (msg) {//弹出倒计时
                //this.open_timeout(msg);
                this.$broadcast('openlist', msg);
                this.$broadcast('money_change', this.type_prices);//发送改变金额
            },
            'history_update': function (msg) {//监听历史记录是否更新
                this.$broadcast('history_update', msg);
            },
            'buy': function (msg) {
                this.buy(msg.swl)
            },
            'start_guide': function (msg) {
                this.start_guide(msg);
            },
            'update_user_money': function (msg) {//更新用户金额
                if (msg && msg.money) {
                    this.user_money = msg.money;
                } else {
                    this.update_user_money();
                }
            },
            'reduce_couponnumber': function (msg) {//优惠卷数量减去
                this.couponnumber = parseInt(this.couponnumber - msg.number);
            },
            'show_order_close': function (msg) {
                this.$broadcast('close_msg', msg);
                this.update_user_money();
            },
            'show_head_ad': function (msg) {//显示头部广告
                this.head_ad = true;
            },
            'hide_head_ad': function (msg) {//隐藏头部广告s
                this.head_ad = false;
            }
        },
        methods: {

            init: function () {//初始化
                // this.update_user_money(function () {
                //     loadpagecom();
                // });
                // this.getDataInfo();
                // this.sel_good("0", this.pdata[0].SymbolName);
                //this.$broadcast('guide');
                this.minute_update();

                // this.$broadcast('tab_change', { index: 0 });
            },

            update_time: function () {
                //var client_date = new Date();
                //var ctime = new Date(client_date.getTime() + this.server_interval);
                //this.cur_time = ctime.Format("yyyy/MM/dd hh:mm:ss");
            },
            lighthint: function (msg) {//
                this.$broadcast('light_hint', msg);//添加到倒计时结果列表
            },
            add_close_msg: function (msg) {//平仓显示
                this.$broadcast('add_result', msg);//添加到倒计时结果列表
            },
            start_guide: function (bF) {//开始播放新手教程
                this.$broadcast('guide', {bF: bF});
            },
            roll_msg: function (bF) {//领取红包
                this.$broadcast('roll_msg', {bF: bF});
            },
            set_map: function (str) {//分时图选择
                var type_name = getCapitalTypeById("sw_active");
                if (this.map_time != str || this.type_name != type_name) {
                    this.map_time = str;
                    this.type_name = type_name;

                    if (this.type_name == "" || !this.type_name) {
                        this.type_name = "BTCCNY";
                    }
                    InitializaChart(this.type_name, this.map_time, 480);
                }
            },
            map_zoom: function (dir) {//地图缩放
                setZoom(dir)
            },
            showTab: function (index) {//TAB切换
                var msg = {index: index}
                this.$broadcast('tab_change', msg);
            },
            add_deal: function (item, peopleNum, transactionsNum) {//最新成交
                this.people_num = peopleNum;
                this.transactions_num = transactionsNum;
                this.$broadcast('add_deal', item);
            },
            minute_update: function () {//更新走势图
                console.log('minute_update');
                InitializaChart(this.type_name, this.map_time, 480);
                //setTimeout(this.minute_update, 1000 * 60);//一分钟延迟
            },

            update_price: function (data) {//服务器推数据
                server_charts(data);
                var testDate = new Date();
            },


            getDataInfo: function () {//轮询获取获取数据
                var self = this;
                var arr = []//设置价格方向
                for (var i = 0; i < this.pdata.length; i++) {
                    var vdata = this.pdata[i];
                    var key = vdata.TypeName.toString();
                    ;//类型名称
                    var val = vdata.Bid.toFixed(decimal_list[key]);
                    self.set_setting_price(key, 'Bid', vdata.Bid.toFixed(decimal_list[key]));
                    self.set_setting_price(key, 'Low', vdata.Low.toFixed(decimal_list[key]));
                    self.set_setting_price(key, 'High', vdata.High.toFixed(decimal_list[key]));
                    arr[key] = [val, 1];
                }
                self.type_prices = arr;
                self.$broadcast('money_change', arr);//发送改变金额
                //self.charts_update(arr);
                //self.user_money = data[0]["Balance"];//用户余额
            },

            copy_obj: function (obj) {
                var o = $.extend(true, {}, obj);
                return o;
            },
            charts_time: function () {//轮询画线
                if (this.type_prices) {
                    this.charts_update(this.type_prices);
                }
            },
            set_setting_price: function (typename, key, val) {
                val = parseFloat(val);
                val = val.toFixed(decimal_list[typename]);
                this.set_setting(typename, key, val);
            },
            set_setting: function (typename, key, val) {//设置对象属性
                for (var i = 0; i < this.pdata.length; i++) {
                    var data = this.pdata[i];
                    if (data.TypeName == typename) {
                        this.pdata[i][key] = val;
                        return data;
                    }
                }
            },
            sel_good: function (index, good) {
                //导航选择产品
                if (this.select_good != good) {
                    this.select_good = good;
                    this.pi = index;

                    this.map_time = "M1";
                    this.type_name = this.pdata[index].TypeName;

                    InitializaChart(this.type_name, this.map_time, 480);
                    this.$broadcast('sel_good', {type: this.type_name});//发送选择合约事件
                }
            },
            sel_time: function (index) {//选择时间
                var i = this.pi;
                this.$broadcast('sel_time', {index: index});//发送选择合约事件
            },
            buy: function (swl) {
                if (!isLogin(swl)) return;
                if (!isSetPhone(swl)) return;
                //下单
                var i = this.pi;
                var obj = this.pdata[i].lsSetting[mySwipers[i].activeIndex % this.pdata[i].lsSetting.length];
                var btns = this.pdata[i].InvestmentAmount;//价格按钮组
                var tid = this.pdata[i].TradingLimitedSettingsID;//产品ID
                var cid = this.pdata[i].ContractTypeID;//
                var select_time = parseInt(obj.TimeType) * 60;
                var msg = {
                    swl: swl,
                    btns: btns,
                    bl: obj.ProfitRate,
                    select_time: select_time,
                    select_good: this.select_good,
                    tid: tid,
                    type_name: this.type_name,
                    c_money: this.pdata[this.pi].Bid,
                    cid: cid
                };
                this.$broadcast('openbuy', msg);
            },
            buycom: function () {
                //下单完成显示列表
            },
            ticket_change: function (t) {
                t = parseInt(t);
                this.ticket_number > t && (this.ticket_number = t), this.ticket_number && this.ticket_number < 1 && (this.ticket_number = 1), this.buy2_change()
            }, select_ticket: function (t) {
                var i = this.ticket_list[t];

                // if (this.prompts = i.Message + "", "" != this.prompt_hide && (this.prompt_time && clearTimeout(this.prompt_time), this.prompt_time = setTimeout(this.prompt_hide, 3e3)), 0 != i.Number) {
                //     if (this.ticket_index == t ? (this.ticket_index = null, this.is_momney = !0, this.buy_change(this.p_money)) : this.ticket_index = t, null != this.ticket_index) {
                //         var e = this.ticket_list[this.ticket_index], s = e.Number;
                //         this.ticket_number = s, this.is_momney && (this.p_money = this.buy_money), 2 != e.Activity_type_id ? (this.is_momney = !1, this.buy_change(e.Coupon_amount)) : (this.is_momney = !0, this.buy_change(this.p_money))
                //     }
                //     this.buy2_change()
                // }
                this.ticket_index = t;
                var e = this.ticket_list[this.ticket_index], s = e.count;
                this.ticket_number = 1;
                this.buy2_change();
            },
            get_ticket: function () {
                var t = this;
                money = t.buy_money == 0 ? parseFloat($(".slct p i").text()) : t.buy_money;
                this.ticket_index = null, this.ticket_list = null, this.ticket_number = null, $.get("/wap/trading/getUserCoupon", {
                    type_name: t.type_name,
                    money: money
                }, function (i) {
                    t.ticket_list = i
                })
            },
            buy2_change: function () {
                set_shouyi();
            },
            get_bili: function () {
                var bili = 1;
                if (this.ticket_list != null && this.ticket_list.length > 0 && null != this.ticket_index) {
                    if (this.ticket_list[this.ticket_index].coupon_type == "Incr" && this.ticket_number > 0) {
                        for(var i = 0; i < this.ticket_number; i++){
                            bili = bili * 1.1;
                        }
                    }
                }
                return bili;
            }
        }
    });
    vue.init();

}


/**
 * Created by Administrator on 2016/8/8.
 */
var mySwipers = [];//所有时间选择器
$(function () {
    vue_init();//双向绑定初始

    //决胜时间初始
    // for (var i = 0; i < pageData.length; i++) {
    //     mySwipers[i] = initSwiper(i);
    // };
    // setTab('#tabs-container', ".tab");
    // //$(".page_load").css('display', "none");
    // if($(".head_ad .swiper-wrapper").find(".swiper-slide").length>1){
    //     var head_ad_swiper = new Swiper('.head_ad', {
    //         pagination: '.swiper-pagination',
    //         autoplay: 5000,//可选选项，自动滑动
    //         loop:true,
    //     })
    // }
})

function initSwiper(index) {//初始化Swiper
    return new Swiper('.card_box_' + index, {
        effect: 'coverflow',
        slidesPerView: "auto",
        loop: true,
        centeredSlides: true,
        initialSlide: 1,
        slideToClickedSlide: true,
        coverflow: {
            rotate: 0,
            stretch: 20,
            depth: 150,
            modifier: 2,
            slideShadows: true
        },
        onSlideChangeEnd: function (e) {
            //console.log($(".active div[data-swiper-slide-index=" + e.activeIndex + "]").text())
            //console.log(e.activeIndex)
            vue.sel_time(e.activeIndex);
        },
    })
}

function setTab(id, tab) {
    var tabsSwiper = new Swiper(id, {
        speed: 500,
        onSlideChangeEnd: function () {
        },
        onSlideChangeStart: function () {
            vue.showTab(tabsSwiper.activeIndex)
            $(tab + " .active").removeClass('active')
            $(tab + " a").eq(tabsSwiper.activeIndex).addClass('active')
        },

    })
    $(tab + " a").on('touchstart mousedown', function (e) {
        e.preventDefault()
        $(tab + " .active").removeClass('active')
        $(this).addClass('active')
        tabsSwiper.slideTo($(this).index())
    })
    $(tab + " a").click(function (e) {
        e.preventDefault()
    })
}


//最新成交控制器
Vue.component('latest-deal-component', {
    template: '<table class="latest_deal"><thead><tr><td>开仓时间</td><td>合约</td><td>方向</td><td  colspan="2" >订金</td></tr></thead>' +
    '<tbody><tr v-bind:class="{\'mz\':item.Direction==1,\'mt\':item.Direction==2}" v-for="item in list"><td>{{item.Subtime}}</td><td><span>{{item.Symbol}}</span></td><td><span class="text-red" v-if="item.Direction==1">买涨</span><span class="text-green" v-if="item.Direction==2">买跌</span></td><td style="text-align:right" width="50" ><template v-if="item.BuyCount!=0">{{item.BuyCount}}</template><template v-if="item.BuyCount==0">券</template></td><td width="80"><span class="databar" v-bind:style="{ width:parseInt(parseInt(item.BuyCount)/4998*80+2) +\'px\' }"  ></span></td></tr></tbody></table>',
    events: {
        'add_deal': function (obj) {//最新成交 添加数据
            var data = {
                'Subtime': obj.Subtime,
                'Symbol': obj.Symbol,
                'Direction': obj.Direction,
                'BuyCount': parseFloat(parseFloat(parseFloat(obj.BuyCount)).toFixed(2))
            };
            if (this.list) {
                if (this.list.length >= 10) {
                    this.list.pop();
                }
                this.list = [data].concat(this.list);
            } else {
                this.list = [data];
            }
        }
    },
    data: function () {
        return {
            list: null,
        }
    },
    methods: {}
})

//平仓提示控制器
Vue.component('close-msg-component', {
    template: '<div class="close_msg " v-bind:class="{\'open\':show}" v-if="data" >' +
    '<template v-if="data.ProfitFlag==0">您购买{{data.Symbol}}判断无涨跌,请再接再厉!</template>' +
    '<template v-if="data.ProfitFlag==1">恭喜!您购买的{{data.Symbol}}盈利+{{data.Profit}},请继续盈战!</template>' +
    '<template v-if="data.ProfitFlag==2">很遗憾!您购买{{data.Symbol}}判断失误,请再接再厉!</template><a class="close_btn" @click="to_hide()"><i class="iconfont icon-guanbi"></i></a>' +
    '</div>',
    events: {
        'close_msg': function (msg) {//平仓添加数据
            this.list.push(msg);
            if (!this.status) {//非显示状态
                this.to_show();
            }
        }
    },
    data: function () {
        return {
            list: [],//消息队列
            status: false,//是否开始
            data: null,
            show: false,
        }
    },
    methods: {
        to_show: function () {
            if (this.list.length <= 0) return;
            this.data = this.list.shift();
            setTimeout(this.to_hide, 5000);
            this.show = true;
            this.status = true;
        },
        to_hide: function () {
            this.show = false;
            setTimeout(this.to_hide_time, 500);
        },
        to_hide_time: function () {
            this.status = false;
            if (!this.show) {//非显示状态
                this.to_show();
            }
        }
    }
})

//红包控制器
Vue.component('roll-msg-component', {
    template: '<div class="roll_msg " v-bind:class="{\'open\':show==2,\'hide\':show==1,\'hide_com\':show==0}" >' +
    '<template v-if="show!=0"><img src="/Style/imgs/roll_msg.png" @click="confirm()"></template>' +
    '</div>',
    events: {
        'roll_msg': function (msg) {
            if (msg && msg.bF) {
                this.b_f = msg.bF;
            }
            this.show = 2;

        }
    },
    data: function () {
        return {
            show: 0,
            b_f: null,
        }
    },
    methods: {
        confirm: function () {
            this.show = 1;
            setTimeout(this.hide_com, 500)
        },
        hide_com: function () {
            var self = this;
            this.show = 0;
            alertMsg2({
                title: '提示',
                content: "恭喜您！您已获得1张10元必盈券+9张10元增益券，请点击我的优惠券查看。",
                btn: ['开始新手教程'],
                yes: function (index) {
                    if (self.b_f) {
                        self.b_f();
                    }
                    layer.close(index);
                }
            })
        }
    }
})
//今日排行
Vue.component('ranking-component', {
    template: '<table class="latest_deal"><thead><tr><td>排名</td><td>用户</td><td>盈利金额</td></tr></thead>' +
    '<tbody><tr class="ranking_{{item.num}}" v-for="item in list"><td class="num"><i class="iconfont icon-paiming{{item.num}}" v-if="item.num<=3"></i><span v-if="item.num>3">{{item.num}}</span></td><td>{{item.DisplayName}}</td><td  style="text-align:center" >{{item.Profit.toFixed(2)}}</td></tr></tbody></table><p class="nodata" v-if="list==null || list.length==0">暂无排行</p>',
    events: {
        'tab_change': function (msg) {//tab切换
            if (msg.index == 0) {//当前显示
                this.show = true;
                if (!this.isInit) {
                    this.isInit = true;
                    this.time_getlist();
                }
            } else {//当前隐藏
                this.show = false;
            }
        },
    },
    data: function () {

        return {
            isInit: false,//是否初始化
            show: false,
            list: [],
        }
    },
    methods: {
        time_getlist: function () {
            if (this.show) {
                this.getlist();
            }
            setTimeout(this.time_getlist.bind(this), 1000 * 60);
        }
        ,
        getlist: function () {
            postMsg('/Ashx/TradingLimited.ashx', {act: "DayProfitRankingList"}, function (result) {
                this.list = result;
            }.bind(this));
        },
    }
})


//公告组件
Vue.component('bulletin-component', {
    template: '<span class="bulletin">{{text}}</span>',
    props: ['text'],
    events: {},
    data: function () {
        return {}
    },
    methods: {}
})

//APP下载弹框控制器
Vue.component('download-app-component', {
    template: '<div class="app_msg " v-bind:class="{\'open\':show==2,\'hide\':show==1,\'hide_com\':show==0}" >' +
    '<template v-if="show!=0"><a href="#"><img src="/Style/imgs/tc.png"  style="margin-top:150px;"></a><img class="close_btn" src="/Style/imgs/close_btn.png" @click="close()" ></template>' +
    '</div>',
    events: {
        'app_msg': function (msg) {
            if (msg && msg.bF) {
                this.b_f = msg.bF;
            }
            this.show = 2;

        }
    },
    data: function () {
        return {
            show: 0,
            b_f: null,
        }
    },
    methods: {
        confirm: function () {
            this.show = 1;
            setTimeout(this.hide_com, 500)
        },
        close: function () {
            this.show = 1;
            setTimeout(function () {
                this.show = 0;
            }.bind(this), 500)
        },
        hide_com: function () {
            var self = this;
            this.show = 0;

        }
    }
})


//平仓提示控制器
Vue.component('light-hint', {
    template: '<div class="light_hint_box" ><div class="light_hint" v-for="item in list"><i class="iconfont icon--yingli text-yellow"></i>{{item.UserDisplayName}} <span class="text-red">盈利+{{item.Profit}}元</span></div>' +
    '</div>',
    events: {
        'light_hint': function (msg) {//平仓添加数据
            if (msg.UserDisplayName.length > 7) {
                msg.UserDisplayName = msg.UserDisplayName.substr(0, 7) + "...";
            }
            this.add_msg(msg);
        }
    },
    data: function () {
        return {
            list: [],//消息队列
            status: false,//是否开始
            data: null,
            show: false,
        }
    },
    methods: {
        add_msg: function (msg) {
            this.list.push(msg);
            if (this.list.length > 10) {
                this.list.shift();
            }
        },
    }
})