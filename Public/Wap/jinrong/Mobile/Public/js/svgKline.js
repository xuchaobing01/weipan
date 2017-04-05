/*
 *
 *接受的数据格式是[毫秒表示的时间,开盘价,最高价,最低价,收盘价]
 *
 *

 /*****************************************************************************************************************************/

//定义全局变量
if (!StockNameSpace) {
	StockNameSpace = {
		MADisplay : false,//均线开关
		BollDisplay : false,//布林带开关
	};
}
StockNameSpace.Kline = function() {
	var MA5Color='#6fb7b7',//MA5颜色
	MA10Color='#82d900',//MA10颜色
	KlineUpColor='#DD2200',//k线上涨颜色
	KlineDownColor='#33AA11',//k线下跌颜色
	BollColor='#CA8EC2',//布林带颜色
	CandleCount = 30,//蜡烛图的个数
	MADisplay = StockNameSpace.MADisplay,//均线开关
	BollDisplay = StockNameSpace.BollDisplay,//布林带开关
	SpaceLeftWidth = 12,//k线图左侧留白
	SpaceRightWidth = 50,//k线右侧留白
	BottomTimeDisplay = true,//底部时间开关
	BottomTimeDiff = 10,//底部两个时间间隔蜡烛图的个数
	Canvaswidth, // 画布宽
	Canvasheight, // 画布高
	SvgId, // 画布id
	Ratio, // 价格与高度的比率
	CandleStep, // 每个蜡烛图和留白的宽带和
	MaxData = 0, // 最大成交价
	MinData = 100000, // 最低成交价
	VerticalCount = 6, // 竖直方向分了4段
	HorizalCount = 4, // 水平方向分了6段
	TipWidth = 120, // 提示框的宽度
	TipHeight = 110, // 提示框的高度
	LastCandleData = {}, // 最后一个蜡烛图的数据
	ISIE = navigator.userAgent.indexOf('MSIE'), CandleDataArr, // 蜡烛图数据数组
	CandleWidth, // 单个蜡烛图的宽度
	CandleSpaceInterval = 3, // 两个蜡烛图之间的距离
	ChartWidth, // 整张图表的宽度
	ChartHeight, // 整张图标的高度
	Ratio, // 价格和高度的比率
	Suffix,//后缀 区分 svg svg2
	SpaceRatio = 0.60, // 上下留白的比率
	ChartMarginBottom, // 图表距离画布底端的距离
	ChartMarginTop, // 图标距离画布顶端的距离
	FontSize = 18, // 字体大小
	InitDatas = [], // 弹出tip框用的数据缓存数组
	InitAxis = [], // 弹出tip框用的坐标数组
	TipFlg = -1, // 判断鼠标落在哪个坐标数组中的标志位
	TipAxise = [], // 缓存弹框的坐标
	TipData = [], // 缓存弹框的数据
	SvgY, // svg画布距离浏览器顶端的距离
	SvgX, // svg画布距离浏览器左端的距离
	OriginalData,//http请求回应的原始数据
	MADATA={},//移动平均线数据
	DecimalPoint, TimeDiff, NameSpace = 'http://www.w3.org/2000/svg';
	var VStep, // 水平背景线的竖直间隔
	HStep;// 竖直背景线的水平间隔
	// var VStep = Canvasheight/4,//水平背景线的竖直间隔
	// HStep=Canvaswidth/6;//竖直背景线的水平间隔
	/**
	 * 封装数据结构
	 */

	/**
	 * path的对象
	 */
	function Path() {
		this.dashed = false;
		this.visibility = 'visible';
		this.style = ""
		this.fillOpacity = 1;
		this.strokeOpacity = 1;
		this.fill = '#DD2200';
		this.stroke = '#DD2200';
		this.d = '';
		this.startX = 0;
		this.startY = 0;
		this.stockWidth = 1;
		this.setId = function(id) {
			this.id = id;
		}
		this.setStartX = function(x) {
			this.startX = x;
		}
		this.setStartY = function(y) {
			this.startY = y;
		}
		this.setFill = function(color) {
			this.fill = color;
		}
		this.setStroke = function(color) {
			this.stroke = color;
		}
		this.setFillOpacity = function(opacity) {
			this.fillOpacity = opacity;
		}
		this.setStrokeOpacity = function(opacity) {
			this.strokeOpacity = opacity;
		}
		this.setD = function(d) {
			this.d = d + ' Z';
		}
		this.setDM = function(x, y) {
			this.d = this.d + ' M' + x + ' ' + y;
			return this;
		}
		this.done = function() {
			this.d = this.d + ' Z';
			return this;
		}
		this.setDL = function(x, y) {
			this.d = this.d + ' L' + x + ' ' + y;
			return this;
		}
		this.setStyle = function(style) {
			this.style = style
		}
		this.setGroupId = function(gId) {
			this.groupId = gId;
		}
		this.draw = function() {
			var p = document.createElementNS(NameSpace, "path");
			p.setAttribute("id", this.id);
			p.setAttribute("d", this.d);
			p.setAttribute("style", this.style);
			p.setAttribute("fill", this.fill);
			p.setAttribute("stroke-width", this.stockWidth);
			p.setAttribute("stroke", this.stroke);
			p.setAttribute("fill-opacity", this.fillOpacity);
			p.setAttribute("stroke-opacity", this.strokeOpacity);
            if(this.dashed){
                p.setAttribute("stroke-dasharray", '3 2');
			}
			var g = document.getElementById(this.groupId);
			if(null != g){
				g.appendChild(p);
			}
		}
	}

	/**
	 * group
	 */
	function Group() {
		this.index = 1;
		this.visibility = 'visible';
		this.listener = false;// 是否有注册事件
		this.setListener = function(listener) {
			this.listener = listener;
		}
		this.setVisibility = function(visible) {
			this.visibility = visible;
		}
		this.setIndex = function(index) {
			this.index = index;
		}
		this.setStyle = function(style) {
			this.style = style;
		}
		this.setId = function(gId) {
			this.id = gId;
		}
		this.draw = function() {
			var g = document.createElementNS(NameSpace, "g");
			g.setAttribute("visibility", this.visibility);
			g.setAttribute("index", this.index);
			g.setAttribute("id", this.id);
			if (this.listener) {
				setSvgX();
				var x = 0;
				g.addEventListener("mousemove",function(e) {
					x = e.clientX - SvgX;
					y = e.clientY - SvgY;
					// for ( var i = 0; i < InitAxis.length; i++) {
					for ( var i = (InitAxis.length-1),ii=0; i >=0; i--,ii++) {
						var temp = InitAxis[i];
						if (x >= temp[0] && temp[1] > x) {
							if (TipFlg != i) {
								removeById("yline"+Suffix);
								removeById("tipId"+Suffix);
								removeById("tipMsgId"+Suffix);
								var high = InitDatas[i][2];// tip箭头指向最高价
								y = Canvasheight
										- ChartMarginBottom
										- (high - MinData)
										* Ratio;// 要记得减去底部留白
								// x = (i + 1)
								x =ChartWidth -(ii * (CandleWidth + CandleSpaceInterval)+ CandleSpaceInterval+ CandleWidth/2-1);

								TipAxise = [];// 重置弹框坐标
								TipAxise.push(x);// 缓存弹框的坐标
								TipAxise.push(y);// 缓存弹框的坐标
								TipData = InitDatas[i];// 缓存弹框的数据
								addTip();// 添加弹框
								addTipAxise(x+SpaceLeftWidth);// 添加竖向的直线
								TipFlg = i;
								ii = ii + 1;
							}
							break;
						}
					}
				});
				g.addEventListener("mouseout",function(e) {
					TipFlg = -1;// 重置标志位
					x = e.clientX - SvgX;
					y = e.clientY - SvgY;
					if (!(x > 0 && y > 0 && x < ChartWidth - 3 && y < Canvasheight - 3)) {
						$("#tipId"+Suffix).fadeOut();
						$("#tipMsgId"+Suffix).fadeOut("normal",function(){
							removeById("yline"+Suffix);
							removeById("tipId"+Suffix);
							removeById("tipMsgId"+Suffix);
						});
							
					}
				});
			}
			var svg = document.getElementById(SvgId);
			svg.appendChild(g);
		}
	}
	function Text() {
		this.setGroupId = function(id) {
			this.groupId = id;
		}
		this.setX = function(x) {
			this.x = x;
		}
		this.setY = function(y) {
			this.y = y;
		}
		this.setMsg = function(msg) {
			this.textContent = msg;
		}
		this.setId = function(id) {
			this.id = id;
		}
		this.draw = function() {
			var text = document.createElementNS(NameSpace, 'text');
			text.setAttribute("id", this.id);
			text.setAttribute('x', this.x);
			text.setAttribute('y', this.y);
			text.textContent = this.textContent;
			document.getElementById(this.groupId).appendChild(text);
		}
	}
	/**
	 * 矩形的对象
	 */
	function Rect() {
		this.setFillColor = function(color) {
			this.fillColor = color;
		}
		this.setStrokeColor = function(color) {
			this.strokeColor = color;
		}
		this.setId = function(id) {
			this.id = id;
		}
		this.setGroupId = function(gId) {
			this.groupId = gId;
		}
		this.visibility = 'visible';
		this.setStyle = function(style) {
			this.style = style;
		}
		this.setAttr = function(x, y, width, height, visible, fillColor,
				strokeColor, fillOpacity, strokeOpacity, strokedasharray, strokeWidth) {
			this.x = x;
			this.y = y;
			this.width = width;
			this.height = height;
			this.visibility = visible || 'visible';
			this.fillColor = fillColor || '#ccc';
			this.strokeColor = strokeColor || '#ccc';
			this.fillOpacity = fillOpacity || 1;
			this.strokeOpacity = strokeOpacity || 1;
			this.strokeWidth = strokeWidth || 1;
			this.strokedasharray = strokedasharray || '3 2';
		}
		this.draw = function() {
			var rect = document.createElementNS(NameSpace, "rect");
			rect.setAttribute("x", this.x);
			rect.setAttribute("y", this.y);
			rect.setAttribute("id", this.id);
			rect.setAttribute("width", this.width);
			rect.setAttribute("height", this.height);
			rect.setAttribute("visibility", this.visibility);
			rect.setAttribute("fill", this.fillColor);
			rect.setAttribute("stroke", this.strokeColor);
			rect.setAttribute("stroke-width", this.strokeWidth);
            rect.setAttribute("stroke-dasharray", this.strokedasharray);
			rect.setAttribute("style", this.style || 'fill-opacity:'
					+ this.fillOpacity + ',stroke-opacity:'
					+ this.strokeOpacity);
			var group = document.getElementById(this.groupId);
			group.appendChild(rect);
		}
	}
	/** *************************************************************************************** */
	/**
	 * @param datas:k线数据
	 *            diff:时间类型(60 300 900 3600) type: svgId:画布Id
	 */
	var startDraw = this.startDraw = function(datas, diff,svgId,suffix,decimalPoint,type) {
		//console.log("start");
		DecimalPoint = decimalPoint;
		OriginalData = datas;
		Suffix = suffix;
		if (!!svgId) {
			SvgId = svgId+Suffix;
		}
		//console.log('svgid:'+SvgId);
		TipFlg = -1;
		TipAxise = [];
		TimeDiff = diff;
		MaxData = -1;
		MinData = 100000;
		// yue add
		var minu = diff / 60;
		var arr = [];
		if(minu > 1){
			var low = 0;
			var high = 0;
			var num = Math.ceil(datas["data"].length / CandleCount);
			for(var i = 0; i < datas["data"].length; i++){
				low = low == 0 ?  parseFloat(datas["data"][i][4]) : Math.min(parseFloat(datas["data"][i][4]),low);
				high =  Math.max(parseFloat(datas["data"][i][3]), high);
				if(i % num == num - 1 || i == datas["data"].length - 1){
					var d = datas["data"][i];
					d[4] = low;
					d[3] = high;
					arr.push(d);
					low = 0;
					high = 0;
				}
			}
		}
		else{
			arr = datas["data"].slice(-1*CandleCount);
		}
		// var arr = datas["data"].slice(-1*CandleCount);

		//var arr = datas["data"];
		getRatio(arr);
		init(arr);
		//清空时间和价格
		clearDiv();
		draw(arr);
	}
	function clearDiv(){
		removeById("klineBottomTimeId"+Suffix);// 底部时间
		removeById("klinePriceId"+Suffix);// 右侧价格
		removeById("mlinePriceId"+Suffix);
		removeById("mlineBottomTimeId"+Suffix);// 底部时间
	}
	function draw(data){
		removeById("mlineChartId"+Suffix);
		removeById("klineChartId"+Suffix);//删除分时线
		drawGroup("klineChartId"+Suffix,false);
		drawBackGroundRect();//画背景框
		drawHorizalLine();//水平横线
		drawVerticalLine();// 垂直线
		//console.log('o4');
		drawCandles(data);//蜡烛图
		if(BottomTimeDisplay){
			addBottomTime();//底部时间坐标
		}		
		drawCandlePrice();//右侧价格坐标
		
		//console.log(MADisplay+'0012');//杨ma平均线
		if(MADisplay){
			drawMaLine(data);//画移动平均线
		}
		if(BollDisplay){
			drawBoll(data);//画布林带
		}
		//$("#select_id").css({"display":"block"});
	}
	/**
	 *布林带
	 */
	function drawBoll(data){
		startDrawLine(data,BollColor,8);
		startDrawLine(data,BollColor,9);
		startDrawLine(data,BollColor,10);
	}
    /**
     *画移动平均线
     *分为5日 10日 20日 30日
     */
	function drawMaLine(data){
		startDrawLine(data,MA5Color,11);
		startDrawLine(data,MA10Color,12);
	}

	function startDrawLine(datas,color,indexNum){
		var x, y, j = 1;
		var path = new Path();
			path.setGroupId('klineChartId'+Suffix);
		for ( var i = datas.length - 1; i >= 0; i--) {
			var obj = datas[i];
			var price = obj[indexNum];
			y = Canvasheight - (price - MinData) * Ratio - ChartMarginBottom;
			x = ChartWidth + SpaceLeftWidth - j * (CandleWidth + CandleSpaceInterval)+CandleSpaceInterval;
			if (i == datas.length - 1) {
				path.setDM(x,y);
			}else{
				path.setDL(x, y);
			}
			j++;
		}
		path.setFill(color);
		path.setStroke(color);
		path.setStyle("stroke-width:1;stroke:"+color+";fill:"+color+";fill-opacity:0;stroke-linejoin:round;stroke-linecap:round");
		path.draw();
	}
	/**
	 * 根据id删除元素
	 */
	function removeById(id) {
//		////console.log(id);
		var obj = document.getElementById(id);
		if (obj) {
			var parentElement = obj.parentNode;
			if (parentElement) {
				parentElement.removeChild(obj);
			}
		}
	}
	function  ScollPostion() {//滚动条位置
        var t, l, w, h;
        if (document.documentElement && document.documentElement.scrollTop) {
            t = document.documentElement.scrollTop;
            l = document.documentElement.scrollLeft;
            w = document.documentElement.scrollWidth;
            h = document.documentElement.scrollHeight;
        } else if (document.body) {
            t = document.body.scrollTop;
            l = document.body.scrollLeft;
            w = document.body.scrollWidth;
            h = document.body.scrollHeight;
        }
        return { top: t, left: l, width: w, height: h };
    }
	function setSvgX(){
		var scroll = ScollPostion().top;
		var svg = document.getElementById(SvgId);
		if(!StockNameSpace.SvgX){
			SvgX = svg.getBoundingClientRect()['x'];
			//////console.log(SvgX);
			if (!SvgX) {
				SvgX = svg.getBoundingClientRect()['left'];// ie做兼容
			}
			StockNameSpace.SvgX = SvgX;
		}else{
			SvgX = StockNameSpace.SvgX;
		}
		if(!StockNameSpace.SvgY){
			SvgY = svg.getBoundingClientRect()['y'];
			if (!SvgY) {
				SvgY = svg.getBoundingClientRect()['top'];// ie做兼容
			}
			SvgY = parseFloat(SvgY) + parseFloat(scroll);
			StockNameSpace.SvgY = SvgY;
		}else{
			SvgY = StockNameSpace.SvgY;
		}
		//console.log("SvgX"+SvgX,"SvgY"+SvgY);
		$("#select_id").css({"display":"block","top":SvgY,"left":SpaceLeftWidth});
	}
	/**
	 * 计算最大值最小值 格式化日期 time开收高低
	 */
	function getRatio(arr) {
		if(!MADisplay){
			$(".M_trade_mine").css({"display":"none"});
			$(".M_trade_mine2").css({"display":"none"});
		}
		for ( var i = 0; i < arr.length; i++) {
			var item = arr[i];
			if (item[3] > MaxData) {
				MaxData =parseFloat(item[3]);
			}
			if (item[4] < MinData) {
				MinData = parseFloat(item[4]);
			}
			for(var j=8;j<13;j++){
				if((MADisplay && (j==11 || j==12)) || (BollDisplay && (j==8 || j==9 || j==10))){
					if(item[j] > MaxData){
						MaxData = parseFloat(item[j]);
					}
					if(item[j] < MinData){
						MinData = parseFloat(item[j]);
					}
				}
			}
		}
	}
	/**
	 * 初始化数据
	 */
	function init(datas) {
		
		var chartSize = getChartSize();// 返回svg画布的宽度 高度
		Canvaswidth = chartSize[0];
		Canvasheight = chartSize[1];
		ChartWidth = Canvaswidth - SpaceRightWidth - SpaceLeftWidth;// 图表的宽度
		// 设置单个蜡烛图的宽度
		CandleWidth = (ChartWidth - SpaceLeftWidth) / CandleCount - CandleSpaceInterval;//
		
		// 图表的高度是画布高度乘以比率
		ChartHeight = Canvasheight * SpaceRatio;
		// 图表距离浏览器顶部的距离
		ChartMarginTop = Canvasheight * (1 - SpaceRatio) / 2;
		VStep = Canvasheight / HorizalCount,// 水平背景线的竖直间隔
		HStep = ChartWidth / VerticalCount;// 竖直背景线的水平间隔
		// 图表距离浏览器左侧的距离和距离顶部的距离相同
		ChartMarginBottom = ChartMarginTop;
		Ratio = ChartHeight / (MaxData - MinData);// 计算高度和价格的比率
		initData(datas);// 初始化数据和坐标数组
		CandleDataArr = datas.slice(0);// 数组复制
		setSvgX();//设置svg距离浏览器顶部的距离
	}
	/**
	 * 初始化数据数组和坐标数组
	 */
	function initData(datas) {
		var j = 0;
		for ( var i = datas.length - 1; i >= 0; i--) {
			InitAxis[i] = [
					ChartWidth+SpaceLeftWidth - (j + 1) * (CandleWidth + CandleSpaceInterval),
					ChartWidth+SpaceLeftWidth - j * (CandleWidth + CandleSpaceInterval) ];
			InitDatas[i] = datas[i];
			j++;
		}
	}

	function drawCandlePrice() {
		var div = document.createElement('div');
		div.setAttribute("id", "klinePriceId"+Suffix);
		var x = parseInt(SvgX) + parseInt(ChartWidth) + SpaceLeftWidth;
		if($(window).width()>=$(document).width()){
			x = parseInt(ChartWidth) + SpaceLeftWidth +5;
		}
		
		//console.log("svgx:"+DecimalPoint,"ChartWidth:"+ChartWidth);
		var y = SvgY;
		div.setAttribute("style", "background:transparent;position:absolute;left:" + x + "px;top:"+y+"px");
		var diff = (MaxData - MinData) / 3;
		
		diff = diff.toFixed(DecimalPoint);
		var html = "<div class='priceClass'>"
				+ (MaxData + ChartMarginTop / Ratio).toFixed(DecimalPoint)
				+ "</div>";
				//console.log(typeof(MaxData),typeof(diff),typeof(DecimalPoint));
		html = html + "<div class='priceClass'>"
				+ (MaxData - diff).toFixed(DecimalPoint) + "</div>";
		html = html + "<div class='priceClass'>"
				+ (MaxData - diff * 2).toFixed(DecimalPoint) + "</div>";
		html = html + "<div class='priceClass'>"
				+ (MinData - ChartMarginBottom / Ratio).toFixed(DecimalPoint)
				+ "</div>";
		div.innerHTML = html;
		document.getElementById("chart").appendChild(div);
	}

	function getChartSize() {
		var svg = document.getElementById(SvgId);
		var Wwidth = $(window).width();
		var Dwidth = $(document).width();
		var width = Wwidth;
		if(Wwidth > Dwidth){
			width = Dwidth;
		}
		var hwidth = $(".info-box").width();
		if(width > hwidth){
			width = hwidth;
		}
		$("#svgId").css({"width":width});
		return [ width, svg.getAttribute('height')-5 ];
	}


	/**
	 * 画分组
	 */
	function drawGroup(id,listener) {
		var g = new Group();
		g.setId(id);
		g.setListener(listener);
		g.draw();
	}

	function drawBackGroundRect() {	
		var rect = new Rect();
		rect.setGroupId('klineChartId'+Suffix);
		// (x,y,宽度，高度，是否可见，填充颜色，边框填充颜色，fill透明度，边框透明度)
		//rect.setAttr(SpaceLeftWidth, 0.5, ChartWidth - 2, Canvasheight - 2, 'visible','#F8F7F7', '#ccc', 0.5, 0.5);
        rect.setAttr(SpaceLeftWidth, 0.5, ChartWidth - 2, Canvasheight - 2, 'visible','#111111', '#333', 0.3, 0.3, '3 2', '0.5'); //背景改成黑色
		rect.draw();
	}
	/**
	 * 画蜡烛图
	 */
	function drawCandles(datas) {
		var x, y, open, high, low, close, j = 0, candleHeight;
		var time = new Date().getTime();
		for ( var i = datas.length - 1; i >= 0; i--) {
			var path = new Path();
			path.setGroupId('klineChartId'+Suffix);
			open = datas[i][1];
			high = datas[i][3];
			low = datas[i][4];
			close = datas[i][2];
			candleHeight = (open - close) * Ratio;
			var color = candleHeight > 0 ? KlineDownColor : KlineUpColor;
			path.setFill(color);
			path.setStroke(color);
			var price = candleHeight > 0 ? open : close;
			candleHeight = Math.abs(candleHeight);
			//y = Canvasheight - (price - MinData) * Ratio - ChartMarginBottom;
			// console.log(datas[i],i);
			y = (MaxData - price) * Ratio + ChartMarginTop;
			x = ChartWidth + SpaceLeftWidth - j * (CandleWidth + CandleSpaceInterval) - 2;
			if (i == datas.length - 1) {
				path.setId('lastCandleId'+Suffix);
			}
			path.setStartX(x);
			path.setStartY(y);
			path.setDM(x, y);
			x = x - CandleWidth;
			path.setDL(x, y);
			y = y + candleHeight;
			path.setDL(x, y);
			x = x + CandleWidth;
			path.setDL(x, y);
			x = x - CandleWidth / 2;
			y = y - candleHeight - (high - price) * Ratio;
			path.setDM(x, y);
			y = y + (high - low) * Ratio;
			path.setDL(x, y);
			path.done();
			path.draw();
			j++;
		}
		LastCandleData[TimeDiff] = datas[datas.length-1];
		updateLastCandle(TimeDiff);
		// 如果有弹出框，左移时要把x坐标左移
		if (TipFlg != -1) {
			TipAxise[0] = TipAxise[0] - CandleWidth - CandleSpaceInterval;
			
			addTip();// 添加弹框
			addTipAxise(TipFlg * (CandleWidth + CandleSpaceInterval)
					- CandleSpaceInterval - CandleWidth / 2);
		}
	}

	/**
	 * 画水平线
	 */
	function drawHorizalLine() {
		var path = new Path();
		path.setGroupId("klineChartId"+Suffix);
        path.dashed = true;
        path.fill = '#111';
        path.stroke = '#111';
        path.fillOpacity = 0.3;
        path.strokeOpacity = 0.3;
        path.stockWidth = 0.5;
		path.setStyle('stroke:#ccc');
		for ( var i = 1; i < HorizalCount; i++) {
			var y = 0, x = 0;
			y = i * VStep;
			y = parseInt(y) + 0.5;
			x = ChartWidth + SpaceLeftWidth;
			x = parseInt(x) - 0.5;
			path.setDM(SpaceLeftWidth, y);
			path.setDL(x, y);
		}
		path.done();
		path.draw();
	}


    /**
     * 画垂直线
     */
    function drawVerticalLine() {
        var path = new Path();
        path.setGroupId("klineChartId"+Suffix);
        path.dashed = true;
        path.fill = '#111';
        path.stroke = '#111';
        path.fillOpacity = 0.3;
        path.strokeOpacity = 0.3;
        path.stockWidth = 0.5;
        path.setStyle('stroke:#ccc');
        for ( var i = 1; i < VerticalCount; i++) {
            var y = 0, x = 0;
            //x = i * HStep;
            //x = parseInt(x) + 0.5;
            // y = ChartHeight;
            // y = parseInt(y) - 0.5;
            // path.setDM(SpaceLeftWidth, y);
            // path.setDL(x, y);
            var x = ChartWidth+SpaceLeftWidth -2 - i*HStep;
            path.setDM(x,0);
            path.setDL(x,(Canvasheight-1));
        }
        path.done();
        path.draw();
    }
	/**
	 * 添加标识价格的横向虚线
	 */
	function addTipAxise(x) {
		// var x = (TipFlg+1)*(CandleWidth + CandleSpaceInterval) -
		// CandleSpaceInterval - CandleWidth/2;
		var pp = document.createElementNS(NameSpace, "path");
		pp.setAttribute("id", 'yline'+Suffix);
		pp.setAttribute("stroke", "#ccc");
		pp.setAttribute("stroke-width", "1");
		pp.setAttribute("fill-opacity", "0.9");
		pp.setAttribute("stroke-opacity", "0.9");
		pp.setAttribute("d", "M" + x + " 0 L" + x + ' ' + Canvasheight + ' Z');
		document.getElementById('klineChartId'+Suffix).appendChild(pp);
	}
	/**
	 * 添加弹框
	 */
	function addTip() {
		if (TipData.length == 0) {
			// //////console.log('return false addTip');
			return false;// 没有数据直接返回
		}
		var data = TipData;
		var x = TipAxise[0];
		var y = TipAxise[1];
		// //////////console.log('TipAxise[1]=',y);
		var angle = 10;
		var rect = document.createElementNS(NameSpace, "path");
		rect.setAttribute("id", "tipId"+Suffix);
		rect.setAttribute("fill", "#ffffff");
		rect.setAttribute("stroke", "#09B300");
		rect.setAttribute("fill-opacity", "0.8");
		var d = 'M';
		x = parseInt(x)+SpaceLeftWidth;
		y = parseInt(y);
		var upAngel = [ 0, 0 ], belowAngel = [ 0, 0 ], leftTop = [ 0, 0 ], rightTop = [
				0, 0 ], leftBottom = [ 0, 0 ], rightBottom = [ 0, 0 ];
		upAngel[0] = x + angle;
		upAngel[1] = y - angle;
		belowAngel[0] = upAngel[0];
		belowAngel[1] = y + angle;
		d = d + x + ' ' + y + ' L';
		if (y < TipHeight / 2) {
			var yy = 5;
			leftTop[0] = upAngel[0];
			leftTop[1] = yy;
			rightTop[0] = leftTop[0] + TipWidth;
			rightTop[1] = leftTop[1];
			rightBottom[0] = rightTop[0];
			rightBottom[1] = rightTop[1] + TipHeight;
			leftBottom[0] = rightBottom[0] - TipWidth;
			leftBottom[1] = rightBottom[1];
			if ((Canvaswidth - x) < TipWidth) {
				var yy = 5;
				upAngel[0] = x - angle;
				upAngel[1] = y - angle;
				belowAngel[0] = x - angle;
				belowAngel[1] = y + angle;
				rightTop[0] = upAngel[0];
				rightTop[1] = yy;
				leftTop[0] = rightTop[0] - TipWidth;
				leftTop[1] = rightTop[1];
				leftBottom[0] = leftTop[0];
				leftBottom[1] = leftTop[1] + TipHeight;
				rightBottom[0] = leftBottom[0] + TipWidth;
				rightBottom[1] = leftBottom[1];
				d = d + upAngel[0] + ' ' + upAngel[1] + ' L';
				d = d + rightTop[0] + ' ' + rightTop[1] + ' L';
				d = d + leftTop[0] + ' ' + leftTop[1] + ' L';
				d = d + leftBottom[0] + ' ' + leftBottom[1] + ' L';
				d = d + rightBottom[0] + ' ' + rightBottom[1] + ' L';
				d = d + belowAngel[0] + ' ' + belowAngel[1] + ' Z';
			} else {
				d = d + upAngel[0] + ' ' + upAngel[1] + ' L';
				d = d + leftTop[0] + ' ' + leftTop[1] + ' L';
				d = d + rightTop[0] + ' ' + rightTop[1] + ' L';
				d = d + rightBottom[0] + ' ' + rightBottom[1] + ' L';
				d = d + leftBottom[0] + ' ' + leftBottom[1] + ' L';
				d = d + belowAngel[0] + ' ' + belowAngel[1] + ' Z';
			}
		} else if ((Canvasheight - y) < TipHeight / 2) {
			leftBottom[0] = upAngel[0];
			leftBottom[1] = Canvasheight - 5;
			rightBottom[0] = leftBottom[0] + TipWidth;
			rightBottom[1] = leftBottom[1];
			rightTop[0] = rightBottom[0];
			rightTop[1] = rightBottom[1] - TipHeight;
			leftTop[0] = rightTop[0] - TipWidth;
			leftTop[1] = rightTop[1];
			if ((Canvaswidth - x) < TipWidth) {
				var yy = Canvasheight - 5;
				upAngel[0] = x - angle;
				upAngel[1] = y - angle;
				belowAngel[0] = upAngel[0];
				belowAngel[1] = y + angle;
				rightBottom[0] = belowAngel[0];
				rightBottom[1] = yy;
				leftBottom[0] = rightBottom[0] - TipWidth;
				leftBottom[1] = rightBottom[1];
				leftTop[0] = leftBottom[0];
				leftTop[1] = leftBottom[1] - TipHeight;
				rightTop[0] = leftTop[0] + TipWidth;
				rightTop[1] = leftTop[1];
				d = d + upAngel[0] + ' ' + upAngel[1] + ' L';
				d = d + rightTop[0] + ' ' + rightTop[1] + ' L';
				d = d + leftTop[0] + ' ' + leftTop[1] + ' L';
				d = d + leftBottom[0] + ' ' + leftBottom[1] + ' L';
				d = d + rightBottom[0] + ' ' + rightBottom[1] + ' L';
				d = d + belowAngel[0] + ' ' + belowAngel[1] + ' Z';

			} else {
				d = d + upAngel[0] + ' ' + upAngel[1] + ' L';
				d = d + leftTop[0] + ' ' + leftTop[1] + ' L';
				d = d + rightTop[0] + ' ' + rightTop[1] + ' L';
				d = d + rightBottom[0] + ' ' + rightBottom[1] + ' L';
				d = d + leftBottom[0] + ' ' + leftBottom[1] + ' L';
				d = d + belowAngel[0] + ' ' + belowAngel[1] + ' Z';
			}
		} else {
			leftTop[0] = upAngel[0];
			leftTop[1] = y - TipHeight / 2;
			rightTop[0] = leftTop[0] + TipWidth;
			rightTop[1] = leftTop[1];
			rightBottom[0] = rightTop[0];
			rightBottom[1] = rightTop[1] + TipHeight;
			leftBottom[0] = rightBottom[0] - TipWidth;
			leftBottom[1] = rightBottom[1];
			if ((Canvaswidth - x) < TipWidth) {
				upAngel[0] = x - angle;
				upAngel[1] = y - angle / 2;
				belowAngel[0] = upAngel[0];
				belowAngel[1] = y + angle / 2;
				rightTop[0] = upAngel[0];
				rightTop[1] = y - TipHeight / 2;
				leftTop[0] = rightTop[0] - TipWidth;
				leftTop[1] = rightTop[1];
				leftBottom[0] = leftTop[0];
				leftBottom[1] = leftTop[1] + TipHeight;
				rightBottom[0] = leftBottom[0] + TipWidth;
				rightBottom[1] = leftBottom[1];
				d = d + upAngel[0] + ' ' + upAngel[1] + ' L';
				d = d + rightTop[0] + ' ' + rightTop[1] + ' L';
				d = d + leftTop[0] + ' ' + leftTop[1] + ' L';
				d = d + leftBottom[0] + ' ' + leftBottom[1] + ' L';
				d = d + rightBottom[0] + ' ' + rightBottom[1] + ' L';
				d = d + belowAngel[0] + ' ' + belowAngel[1] + ' Z';
			} else {
				d = d + upAngel[0] + ' ' + upAngel[1] + ' L';
				d = d + leftTop[0] + ' ' + leftTop[1] + ' L';
				d = d + rightTop[0] + ' ' + rightTop[1] + ' L';
				d = d + rightBottom[0] + ' ' + rightBottom[1] + ' L';
				d = d + leftBottom[0] + ' ' + leftBottom[1] + ' L';
				d = d + belowAngel[0] + ' ' + belowAngel[1] + ' Z';
			}
		}
		rect.setAttribute("d", d);
		rect.setAttribute("stroke-width", "1");
		document.getElementById('klineChartId'+Suffix).appendChild(rect);
		var g = document.createElementNS(NameSpace, 'g');
		g.setAttribute("id", "tipMsgId"+Suffix);
		document.getElementById("klineChartId"+Suffix).appendChild(g);
		//console.log(data[0]);
		drawText(leftTop[0] + 6, leftTop[1] + FontSize + 5, 'Date:' + getDates(data[0],true),
				'tipMsgId'+Suffix);
		drawText(leftTop[0] + 6, leftTop[1] + FontSize * 2 + 5, 'Open:'
				+ data[1], 'tipMsgId'+Suffix);
		drawText(leftTop[0] + 6, leftTop[1] + FontSize * 3 + 5, 'High:'
				+ data[3], 'tipMsgId'+Suffix);
		drawText(leftTop[0] + 6, leftTop[1] + FontSize * 4 + 5, 'Low:'
				+ data[4], 'tipMsgId'+Suffix);
		drawText(leftTop[0] + 6, leftTop[1] + FontSize * 5 + 5, 'Close:'
				+ data[2], 'tipMsgId'+Suffix);

	}

	function drawText(x, y, msg, id) {
		var text = new Text();
		text.setGroupId(id);
		text.setX(x);
		text.setY(y);
		text.setMsg(msg);
		text.draw();
	}
	/**
	 *添加底部时间
	 */
	function addBottomTime() {
		removeById("klineBottomTimeId"+Suffix);
		//到毫秒的时间戳
		var millionseconds = parseInt(CandleDataArr[CandleDataArr.length - 1][0]);
		var newDate = new Date();
		var index = CandleDataArr.length-1;
		newDate.setTime(CandleDataArr[index][0]+"000");
		var minutes = newDate.getMinutes();
		minutes = parseInt(minutes) % 10;
		var left = parseInt(ChartWidth) + parseInt(SvgX)+SpaceLeftWidth;
		if($(window).width()>=$(document).width()){
			left = parseInt(ChartWidth) + SpaceLeftWidth +5;
		}
		
		left = left -20;
		var step = (CandleWidth + CandleSpaceInterval) * 5;
		var step1 = (CandleWidth + CandleSpaceInterval) * 2.5;
		var path = new Path();
		path.setGroupId('klineChartId'+Suffix);
		path.setStroke("#ccc");
			path.setFill("#ccc");
		for(var i=0;i<13;i++){
			var x = ChartWidth+SpaceLeftWidth -2 - i*step1;
			// if(i == 12){
			// 	x = SpaceLeftWidth;
			// }
			path.setDM(x,Canvasheight-1);
			path.setDL(x,(Canvasheight+10));			
			path.draw();
		}
		var left1 = left - step;
		var left2 = left1 - step;
		var left3 = left2 - step;
		var left4 = left3 - step;
		var left5 = left4 - step;
		var left6 = left5 - step;
		var left7 = left6 - step;
		var y = parseInt(Canvasheight+SvgY)+2;
		var time = getDates(newDate);
		var div = document.createElement('div');
		div.setAttribute("id", "klineBottomTimeId"+Suffix);
		div.setAttribute("style","height:15px");
		var msg = '';
		//msg += "<span style='position:absolute;left:" + left7 + "px;top:" + y
		//		+ "px'>" + time[7] + "</span>";
		msg += "<span style='position:absolute;left:" + left6 + "px;top:" + y
				+ "px'>" + time[6] + "</span>";
		msg += "<span style='position:absolute;left:" + left5 + "px;top:" + y
				+ "px'>" + time[5] + "</span>";
		msg += "<span style='position:absolute;left:" + left4 + "px;top:" + y
				+ "px'>" + time[4] + "</span>";
		msg += "<span style='position:absolute;left:" + left3 + "px;top:" + y
				+ "px'>" + time[3] + "</span>";
		msg += "<span style='position:absolute;left:" + left2 + "px;top:" + y
				+ "px'>" + time[2] + "</span>";
		msg += "<span style='position:absolute;left:" + left1 + "px;top:" + y
				+ "px'>" + time[1] + "</span>";
		msg += "<span style='position:absolute;left:" + left + "px;top:" + y
				+ "px'>" + time[0] + "</span>";
		// console.log(msg);
		div.innerHTML = msg;
		document.getElementById("chart").appendChild(div);
	}

	this.modifyLastCandle = function(data, diff) {
		// console.log(data);
		TimeDiff = diff;
		var temp_ = OriginalData["data"];
		LastCandleData[diff]=temp_[temp_.length-1];
		
		var lastTime = formatTime(LastCandleData[diff][0],diff);
		var currentTime = formatTime(data[0],diff);
		var currentPrice = data[1];
		if(currentTime<lastTime  &&  capital_one=='BTCCNY'){
			drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "now_price");
			//lastTime = undefined;
			console.log("重画");
						return;
		}
		//console.log("上一个："+lastTime,"当前的："+currentTime);
		if (lastTime != currentTime && lastTime != undefined) {
			LastCandleData[diff][0] = currentTime;
			LastCandleData[diff][1] = currentPrice;
			LastCandleData[diff][2] = currentPrice;
			LastCandleData[diff][3] = currentPrice;
			LastCandleData[diff][4] = currentPrice;
			LastCandleData[diff][5] = data[5];
			LastCandleData[diff][6] = data[6];
			LastCandleData[diff][7] = data[7];
			LastCandleData[diff][8] = data[8];
			LastCandleData[diff][9] = data[9];
			LastCandleData[diff][10] = data[10];
			LastCandleData[diff][11] = data[11];
			LastCandleData[diff][12] = data[12];
			CandleDataArr.push(LastCandleData[diff].slice(0));
			CandleDataArr.shift();
			drawNewLastCandle(0,diff,data);
		} else if (lastTime == currentTime && lastTime != undefined) {
			//console.log("更新结算价格：",lastTime,currentTime,currentPrice);
			if (currentPrice > LastCandleData[diff][3]) {
				LastCandleData[diff][3] = currentPrice;
			}
			if (currentPrice < LastCandleData[diff][4]) {
				LastCandleData[diff][4] = currentPrice;
			}
			LastCandleData[diff][2] = currentPrice;
			CandleDataArr.pop();
			CandleDataArr.push(LastCandleData[diff].slice(0));// slice在这的作用是复制数组防止引用
			if (currentPrice > MaxData || currentPrice < MinData) {
				/*addLastCandle();
				displayImg("candle");
				drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "now_price");*/
				addLastCandle();
				return false;
			}
			drawNewLastCandle(1,diff,data);
		} else if (lastTime == undefined) {
			// 第一次更新蜡烛图
			LastCandleData[diff][0] = currentTime;//时间
			LastCandleData[diff][1] = currentPrice;//开
			LastCandleData[diff][2] = currentPrice;//收
			LastCandleData[diff][3] = currentPrice;//高
			LastCandleData[diff][4] = currentPrice;//低
			LastCandleData[diff][5] = currentPrice;
			LastCandleData[diff][6] = currentPrice;
			LastCandleData[diff][7] = currentPrice;
			LastCandleData[diff][8] = currentPrice;
			LastCandleData[diff][9] = currentPrice;
			LastCandleData[diff][10] = currentPrice;
			LastCandleData[diff][11] = currentPrice;
			LastCandleData[diff][12] = currentPrice;
			CandleDataArr.push(LastCandleData[diff].slice(0));
			CandleDataArr.shift();
			drawNewLastCandle(0,diff,data);
		}
	}
	function formatTime(millionseconds,type){
		if(!millionseconds){
			return millionseconds;
		}
		if((millionseconds+'').length == 10){
			millionseconds = millionseconds + '000';
		}
		type = type/60;
		var date = new Date();
		date.setTime(millionseconds);
		date.setSeconds(0);
		date.setMilliseconds(0);
		var minutes = date.getMinutes();
		if(minutes ==0 || type == 1){
			return parseInt(date.getTime()/1000);
		}else{
			minutes = parseInt(minutes/type)*type;
			date.setMinutes(minutes);
			return parseInt(date.getTime());
		}
	}

	function drawNewLastCandle(type,diff,data) {
		if (type == 0) {
			//新增
			addLastCandle();
		} else if (type == 1) {
			//更新
			updateLastCandle(diff);
		}
	}
	/**
	 * 添加新的蜡烛图
	 */
	function addLastCandle() {

		////console.log("addLastCandle");
		removeById("klineChartId"+Suffix);
		removeById("klinePriceId"+Suffix);
		removeById("klineMaId"+Suffix);
		var temp = Array();
		temp["data"] = CandleDataArr;
		startDraw(temp,TimeDiff,'svgId',Suffix,DecimalPoint);
	}
	/**
	 *更新最后一根蜡烛图
	 */
	function updateLastCandle(diff) {
		////console.log("updateLastCandle");
		removeById("lastCandleId"+Suffix);
		if(BottomTimeDisplay){
			addBottomTime();
		}		
		var path = new Path();
		path.setGroupId('klineChartId'+Suffix);
		path.setId('lastCandleId'+Suffix);
		var open = LastCandleData[diff][1], high = LastCandleData[diff][3], low = LastCandleData[diff][4], close = LastCandleData[diff][2];
		$(".height_").html('');
		$(".height_").html(high);
		$(".low_").html('');
		$(".low_").html(low);
		$("#flow_span_value1").html('');
		$("#flow_span_value1").html(close);
		var t = new Date();
		t.setTime(LastCandleData[diff][0]+"000");
		var time_ = getDates(t)[0]+":"+new Date().getSeconds();
		$(".zuoshou_").html("");
		$(".zuoshou_").html(time_);
		$("#now_time").html("");
		$("#now_time").html(time_);
		// if(close>MaxData){
		// 	close = MaxData;
		// }
		// if(close < MinData){
		// 	close = MinData;
		// }
		$("#flow_span_value").html('');
		$("#flow_span_value").html(close);
		var c_hight = (open - close) * Ratio;
		var color = c_hight > 0 ? KlineDownColor : KlineUpColor;
		$("#now_price").css({"color":color});
		

		//console.log(c_hight);
		if(c_hight>0){
			$("#now_price").css({'background':'url(/Public/Wap/jinrong/Mobile/Public/images/arrow-drop.png)',"background-repeat":"no-repeat","background-position":"right 23%","background-size":"11px 25px"});
		}else{
			$("#now_price").css({'background':'url(/Public/Wap/jinrong/Mobile/Public/images/arrow-rise.png)',"background-repeat":"no-repeat","background-position":"right 23%","background-size":"11px 25px"});
		}
		path.setFill(color);
		path.setStroke(color);
		var price = c_hight > 0 ? open : close;
		c_hight = Math.abs(c_hight);
		y = Canvasheight - (price - MinData) * Ratio - ChartMarginBottom;
		x = ChartWidth + SpaceLeftWidth - 2;
		drawPriceTipDiv(x, Canvasheight - (close - MinData) * Ratio
				- ChartMarginBottom, open, close);
		path.setStartX(x);
		path.setStartY(y);
		path.setDM(x, y);
		x = x - CandleWidth;
		path.setDL(x, y);
		y = y + c_hight;
		path.setDL(x, y);
		x = x + CandleWidth;
		path.setDL(x, y);
		x = x - CandleWidth / 2;
		y = y - c_hight - (high - price) * Ratio;
		path.setDM(x, y);
		y = y + (high - low) * Ratio;
		path.setDL(x, y);
		path.done();
		path.draw();

	}
	/**
	 * 右侧价格带箭头的弹出框
	 */
	function drawPriceTipDiv(x, y, open, close) {
		x = x;
		y = y + SvgY;
		//右侧价格带箭头的弹出框要在畫k線之前清楚掉
		removeById("priceTipId"+Suffix);
		removeById("dashedId"+Suffix);
		var div = document.createElement('div');
		div.setAttribute("id", "priceTipId"+Suffix);
		x = x+5;
		// y = parseInt(SvgY)+y-10;
		y = y - 10;
		div.setAttribute("style", "position:absolute;left:" + x + "px;top:" + y
				+ "px");
		var html = '';
		////console.log(open,close);
		if (open > close) {
			html = "<div class='priceGreenTipClass'>" + close + "</div>";
		} else {
			html = "<div class='priceRedTipClass'>" + close + "</div>";
		}
		////console.log(html);
		div.innerHTML = html;
		document.getElementById("chart"+Suffix).appendChild(div);
		////console.log(div);
		var div = document.createElement('div');
		div.setAttribute("id", 'dashedId'+Suffix);
		x = SpaceLeftWidth+3;
		y = y + 10;
		var w = ChartWidth - CandleWidth / 2 - 12
		if (open > close) {
			div
					.setAttribute(
							"style",
							"width:"
									+ w
									+ "px;height:0;border-bottom:#449d44 1px dashed;position:absolute;left:"
									+ x + "px;top:" + y + "px;z-index:1");
		} else {
			div.setAttribute("style","width:"
									+ w
									+ "px;height:0;border-bottom:#d9534f 1px dashed;position:absolute;left:"
									+ x + "px;top:" + y + "px;z-index:1");
		}
		document.getElementById("chart"+Suffix).appendChild(div);
		////console.log(div);
	}
	function getStr(num){
		num = parseInt(num);
		if(num<10){
			return "0"+num;
		}else{
			return num;
		}
	}
	/**
	 * 格式化日期
	 */
	function getDates(date,flg) {
		var months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sept','Oct','Nov','Dec'];
		if(flg){
			var time = new Date();
			time.setTime(date+'000');
			var hour = time.getHours();
			var minute = time.getMinutes();
			if(hour<10){
				hour = '0'+hour;
			}
			if(minute<10){
				minute='0'+minute;
			}
			return hour+':'+minute;
		}
		var type=1;
		var diff = TimeDiff / 60;
		//console.log("TimeDiff=",TimeDiff);
		var hour = date.getHours();
		var minute = date.getMinutes();
		var day = date.getDate();
		//console.log("hour="+hour,"minutes="+minute);
		var dateDiff = 10;
		var dateArr = [];
		if (diff != 1) {
			type = diff;
		}
		
			var count = parseInt(minute / type);
			var totalCount = 0;
	        for(var i=1,j=0;i<70;i++,j++){
	        	if(totalCount>7){
	        		break;
	        	}
                if(count == 0){
					if(j%5 == 0){						
						dateArr.push(getStr(hour)+':00');
						totalCount = totalCount +1;
						j=0;
					}					
					count = parseInt((60-type)/type);
					hour = parseInt(hour) - 1;
					if(hour == 0){
						hour='00';
					}
					if(hour == -1){
						hour = 23;
					}
				}else{
					if(j%5 == 0){
						dateArr.push(getStr(hour)+":"+getStr(count*type));
						totalCount = totalCount +1;
						j=0;
					}					
					count = count - 1;
				}
	        }
			return dateArr;
		
	}
};

