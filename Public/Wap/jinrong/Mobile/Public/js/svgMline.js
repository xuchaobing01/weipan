/*
 *
 *接受的数据格式是[毫秒表示的时间,收盘价]
 *
 *
 /*****************************************************************************************************************************/
if (!StockNameSpace) {
	StockNameSpace = {};
}
StockNameSpace.Mline = function() {
	// 定义全局变量
	var MaxData = -1,
	MinData = 10000000, 
	Ratio, // 价格与高度的比率
	ChartWidth, // chart宽度
	Suffix,
	FontSize = 18,
	TipHeight = 40,
	TipWidth = 160,
	InitAxis=[],
	InitDatas=[],
	MlineData,
	TipFlg=-1,
	DisplayLine=false,
	SpaceLeftWidth = 0,//k线图左侧留白
	SpaceRightWidth = 50,//k线右侧留白
	HeightRatio = 0.8, // chart与画布的比率
	CanvasHeight, // 画布的高度
	ChartMarginTop, // 图表距离顶端的距离
	ChartMarginBottom = 10, Count, // 总点数
	Step, // 两个点之间的间隔
	SvgId,
	DecimalPoint,
	NameSpace = "http://www.w3.org/2000/svg",
	ChartHeight;// chart高度
	
	/**
	 * path的对象
	 */
	function Path() {
		this.visibility = 'visible';
		this.style = ""
		this.strokeColor="#ccc";
		this.fillOpacity = 1;
		this.strokeOpacity = 1;
		this.fill = '#DD2200';
		this.d = '';
		this.startX = 0;
		this.startY = 0;
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
		this.setStrokeColor = function(color) {
			this.strokeColor = color;
		}
		this.draw = function() {
			var p = document.createElementNS(NameSpace, "path");
			p.setAttribute("id", this.id);
			p.setAttribute("d", this.d);
			p.setAttribute("fill", this.fill);
			p.setAttribute("stroke-width", "1");
			p.setAttribute("style", this.style);
			 p.setAttribute("stroke", this.strokeColor);
			p.setAttribute("stroke-linejoin", "round");
			p.setAttribute("stroke-linecap", "round");
			p.setAttribute("fill-opacity", this.fillOpacity);
			p.setAttribute("stroke-opacity", this.strokeOpacity);
			var g = document.getElementById(this.groupId);
			g.appendChild(p);
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
				strokeColor, fillOpacity, strokeOpacity) {
			this.x = x;
			this.y = y;
			this.width = width;
			this.height = height;
			this.visibility = visible || 'visible';
			this.fillColor = fillColor || '#ccc';
			this.strokeColor = strokeColor || '#ccc';
			this.fillOpacity = fillOpacity || 1;
			this.strokeOpacity = strokeOpacity || 1;
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
			rect.setAttribute("stroke-width", "1");
			rect.setAttribute("style", this.style || 'fill-opacity:'
					+ this.fillOpacity + ',stroke-opacity:'
					+ this.strokeOpacity);

			var group = document.getElementById(this.groupId);
			group.appendChild(rect);
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
				var x = 0,y=0;
				g.addEventListener("mousemove", function(e) {
//					//console.log(DisplayLine);
					removeById("drawCircleId"+Suffix+"in");
					removeById("drawCircleId"+Suffix+"out");
					removeById("tipMsgId"+Suffix);
					removeById("mlineTipPrice"+Suffix);
					removeById("mlineTipDate"+Suffix);
					x = e.clientX - SvgX;
					y = e.clientY - SvgY;
					var index=0;
					for(var i=0;i<InitAxis.length;i++){
						if(x>=InitAxis[i][0] && x<InitAxis[i][1]){
							index = i;
							break;
						}
					}
//					//console.log(index,y);
					y = CanvasHeight - (InitDatas[index][1] - MinData) * Ratio - ChartMarginBottom;
					drawCircle(x,y);
					addTip(index,x,y);
					if(!DisplayLine){
						DisplayLine = true;
						drawLine(MlineData);
					}
				});
				g.addEventListener("mouseout", function(e) {
					x = e.clientX - SvgX;
					y = e.clientY - SvgY;
					if (!(x > 0 && y > 0 && x < ChartWidth - 3 && y < CanvasHeight - 3)) {
						DisplayLine = false;
						removeById("mlineDrawLine"+Suffix);
						removeById("drawCircleId"+Suffix+"in");
						removeById("drawCircleId"+Suffix+"out");
						removeById("tipMsgId"+Suffix);
						removeById("mlineTipPrice"+Suffix);
						removeById("mlineTipDate"+Suffix);
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
	function startDraw(data,svgId,suffix,decimalPoint) {
		removeById("klineChartId"+Suffix);//删除K线
		removeById("tipMsgId"+Suffix);
		removeById("tipId"+Suffix);
//		//console.log(decimalPoint);
		//console.log(data);
		MlineData = data;
		DecimalPoint = decimalPoint;
		Suffix = suffix;
		if (!!svgId) {
			SvgId = svgId+Suffix;
		}
		setSvgX();
		// 计算最大值最小值
		formatData(data);
		// 初始化
		init(data);
		//清空旧图
		clearSvg()
		// 开始画图
		draw(data);
		//画右侧价格
		drawMlinePrice();
	}
	// 清空svg
	function clearSvg() {
		removeById("mlineChartId"+Suffix);
		removeById("klineChartId"+Suffix);
	}
	//画背景方框
	function drawBackGroundRect() {
		var rect = new Rect();
		rect.setAttr(0.5, 0.5, ChartWidth-0.5, CanvasHeight, 'visible', '#F8F7F7',
				'#ccc', 1, 1);
		rect.setGroupId("mlineChartId"+Suffix);
		rect.draw();
	}
	//画水平横线
	function drawHorizalLine(){
		var step = CanvasHeight/6;
		var p = new Path();
		p.setGroupId("mlineChartId"+Suffix);
		for(var i=0;i<6;i++){
			p.setDM(0, i*step);
			p.setDL(ChartWidth, i*step);
		}
		p.setStrokeOpacity(0.2);
		p.done();
		p.draw();
	}
	/**
	 *修改最后一个点的数据
	 */
	function modifyLastPoint(data,svgId,suffix,decimalPoint){
		var newTime = data[0];//时间
		var newPrice = data[1];//最新价
		var lastData = MlineData[MlineData.length-1];
		var lastTime = lastData[0];//上一个点的时间
		var lastPrice = lastData[1];//上一个点的价格
		if(newTime == lastTime){
			updateLastPoint(data,svgId,suffix,decimalPoint);//更新最后一个点
		}else{
			addLastPoint(data,svgId,suffix,decimalPoint);//新增最后一个点
		}
	}
	
	/**
	 *更新最后一个点
	 */
	function updateLastPoint(data,svgId,suffix,decimalPoint){
		var y = CanvasHeight - (data[1] - MinData) * Ratio - ChartMarginBottom;
		var x = ChartWidth;
		drawPriceTipDiv(x,y,data[1],suffix);
	}

	/**
	 *新增最后一个点
	 */
	function addLastPoint(data,svgId,suffix,decimalPoint){
		MlineData.push([data[0],data[1]]);
		startDraw(MlineData,svgId,suffix,decimalPoint);
	}
	/**
	 * 右侧价格带箭头的弹出框
	 */
	function drawPriceTipDiv(x, y, price,suffix) {
		//右侧价格带箭头的弹出框要在畫k線之前清楚掉
		removeById("mlinePriceTipId"+suffix);
		removeById("mlineDashedId"+suffix);
		var div = document.createElement('div');
		div.setAttribute("id", "mlinePriceTipId"+suffix);
		x = x;
		y = y + SvgY-10;
		div.setAttribute("style", "position:absolute;left:" + x + "px;top:" + y+ "px");
		var html = '';
		html = "<div class='priceGreenTipClass'>" + price + "</div>";
		div.innerHTML = html;
		document.getElementById("chart"+suffix).appendChild(div);
		var div = document.createElement('div');
		div.setAttribute("id", 'mlineDashedId'+suffix);
		x = 0;
		y = y + 10;
		var w = ChartWidth;
		div.setAttribute("style","width:"+ w+ "px;height:0;border-bottom:#449d44 1px dashed;position:absolute;left:"+ x + "px;top:" + y + "px;z-index:1");
		document.getElementById("chart"+suffix).appendChild(div);
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
	function formatData(data) {
//		//console.log(MaxData);
		for ( var i = 0; i < data.length; i++) {
//			 //console.log(data[i][1]);
			if (data[i][1] > MaxData) {
				MaxData = parseFloat(data[i][1]);
			}
			if (data[i][1] < MinData) {
				MinData = parseFloat(data[i][1]);
			}
		}
//		//console.log(MaxData,MinData);
	}
	function init(data) {
//		//console.log(SvgId);
		var svg = document.getElementById(SvgId);
		CanvasHeight = svg.height.animVal.value;
		ChartWidth = $(window).width()-SpaceRightWidth- SpaceLeftWidth;
		ChartHeight = CanvasHeight * HeightRatio;
		// //console.log(ChartWidth,ChartHeight,MaxData);
		Ratio = ChartHeight / (MaxData - MinData);
		Count = data.length;
		Step = ChartWidth / Count;
		ChartMarginTop = CanvasHeight - ChartHeight;
		for(var i=0;i<data.length;i++){
			InitAxis.push([i*Step,(i+1)*Step]);
			InitDatas[i]=data[i];
		}
	}
	function drawGroup(id,listener) {
		var g = new Group();
		g.setId(id);
		g.setListener(listener);
		g.draw();
	}
	//描边
	function drawLine(data){
		var p = new Path();
		var x = 0, y = 0;
		for ( var i = 0; i < data.length; i++) {
			// //console.log(data[i],Ratio,ChartMarginTop);
			if (i == 0) {
				y = CanvasHeight - (data[i][1] - MinData) * Ratio
						- ChartMarginBottom;
				p.setDM(x, y-1);
				x = x + Step;
			} else {
				y = CanvasHeight - (data[i][1] - MinData) * Ratio- ChartMarginBottom;
				if(i == (data.length-1)){
					x = ChartWidth;
				}
				p.setDL(x, y-1);
				x = x + Step;
			}
		}
		p.setStyle("stroke-width:1;stroke:#00b8c3;fill:#F8F7F7;fill-opacity:0;stroke-linejoin:round;stroke-linecap:round");
		p.setId("mlineDrawLine"+Suffix);
		p.setGroupId("mlineChartId"+Suffix);
		p.draw();
	}
	function Circle(){
		this.setAttr = function(x,y,radius,strokeColor,strokeWidth,fillColor,fillOpacity,strokeOpacity,type){
			this.x = x;
			this.y = y;
			this.radius = radius;
			this.strokeColor = strokeColor;
			this.strokeWidth = strokeWidth;
			this.fillColor = fillColor;
			this.fillOpacity = fillOpacity;
			this.strokeOpacity = strokeOpacity;
			this.type = type;
		}
		this.draw=function(){
			var c = document.createElementNS(NameSpace,"circle");
			c.setAttribute("cx",this.x);
			c.setAttribute("cy",this.y);
			c.setAttribute("r",this.radius);
			c.setAttribute("stroke",this.strokeColor);
			c.setAttribute("stroke-width",this.strokeWidth);
			c.setAttribute("fill",this.fillColor);
			c.setAttribute("fill-opacity",this.fillOpacity);
			c.setAttribute("stroke-opacity",this.strokeOpacity);
			c.setAttribute("id","drawCircleId"+Suffix+this.type);
			document.getElementById("mlineChartId"+Suffix).appendChild(c);
		}
	}
	function drawCircle(x,y){
		var c = new Circle();
		c.setAttr(x, y, 4, "blue", 1, "green", 1,1, "in");
		c.draw();
		c.setAttr(x, y, 10, "blue", 1, "green", 0.15,0, "out");
		c.draw();
	}
	function draw(data) {
		removeById("mlineChartId"+Suffix);
		removeById("klinePriceId"+Suffix);
		removeById("mlinePriceId"+Suffix);
		removeById("priceTipId"+Suffix);
		removeById("dashedId"+Suffix);
		//console.log("priceTipId"+Suffix,"dashedId"+Suffix);
		drawGroup("mlineChartId"+Suffix,true);
		drawBackGroundRect();
		drawHorizalLine();
		var p = new Path();
		p.setStrokeColor('#00c6d2');
		var x = 0, y = 0;
		for ( var i = 0; i < data.length; i++) {
			// //console.log(data[i],Ratio,ChartMarginTop);
			if (i == 0) {
				y = CanvasHeight - (data[i][1] - MinData) * Ratio
						- ChartMarginBottom;
				p.setDM(x, y);
				x = x + Step;
			} else {
				y = CanvasHeight - (data[i][1] - MinData) * Ratio
						- ChartMarginBottom;
				// //console.log(x,y);
                                if(i == (data.length-1)){
					x = ChartWidth;
				}
				p.setDL(x, y);
				x = x + Step;
			}
		}
		p.setDL(ChartWidth, CanvasHeight);
		p.setDL(0, CanvasHeight);
		p.done();
		p.setFill("url(#highcharts)");
		p.setGroupId("mlineChartId"+Suffix);
		p.draw();
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
		console.log("mSvgX"+SvgX,"SvgY"+SvgY);
	}
	function drawMlinePrice() {
		var div = document.createElement('div');
		div.setAttribute("id", "mlinePriceId"+Suffix);
		var x = parseInt(SvgX) + parseInt(ChartWidth) + SpaceLeftWidth;
		console.log("price",SvgY);
		div.setAttribute("style", "position:absolute;left:" + x + "px;top:"+SvgY+"px");
		var diff = (MaxData - MinData) / 3;
		diff = diff.toFixed(DecimalPoint);
		var html = "<div class='priceClass'>"
				+ (MaxData + ChartMarginTop / Ratio).toFixed(DecimalPoint)
				+ "</div>";
		html = html + "<div class='priceClass'>"
				+ (MaxData - diff).toFixed(DecimalPoint) + "</div>";
		html = html + "<div class='priceClass'>"
				+ (MaxData - diff * 2).toFixed(DecimalPoint) + "</div>";
		html = html + "<div class='priceClass'>"
				+ (MinData - ChartMarginBottom / Ratio).toFixed(DecimalPoint)
				+ "</div>";
		div.innerHTML = html;
		document.getElementById("chart"+Suffix).appendChild(div);
	}
	/**
	 * 初始化数据数组和坐标数组
	 */
	function initData(datas) {
		var j = 0;
		for ( var i = datas.length - 1; i >= 0; i--) {
			InitAxis[i] = [
					ChartWidth - (j + 1) * (CandleWidth + CandleSpaceInterval),
					ChartWidth - j * (CandleWidth + CandleSpaceInterval) ];
			InitDatas[i] = datas[i];
			j++;
		}
	}
	/**
	 * 添加弹框
	 */
	function addTip(index,x,y) {
		var data = InitDatas[index];
		var angle = 5;
		var rect = document.createElementNS(NameSpace, "path");
		rect.setAttribute("id", "tipId"+Suffix);
		rect.setAttribute("fill", "#ffffff");
		rect.setAttribute("stroke", "#addeee");
		rect.setAttribute("fill-opacity", "0.9");
		var d = 'M';
		x = parseInt(x);
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
			if ((ChartWidth - x) < TipWidth) {
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
		} else if ((CanvasHeight - y) < TipHeight / 2) {
			leftBottom[0] = upAngel[0];
			leftBottom[1] = CanvasHeight - 5;
			rightBottom[0] = leftBottom[0] + TipWidth;
			rightBottom[1] = leftBottom[1];
			rightTop[0] = rightBottom[0];
			rightTop[1] = rightBottom[1] - TipHeight;
			leftTop[0] = rightTop[0] - TipWidth;
			leftTop[1] = rightTop[1];
			if ((ChartWidth - x) < TipWidth) {
				var yy = CanvasHeight - 5;
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
			if ((ChartWidth - x) < TipWidth) {
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
		rect.setAttribute("id","tipMsgId"+Suffix);
		document.getElementById('mlineChartId'+Suffix).appendChild(rect);
		drawText(leftTop[0]+5, leftTop[1] + FontSize, 'Date:' + (new Date(parseFloat(data[0]+'000'))).format("MM:dd hh:mm"),
				'mlineChartId'+Suffix,'mlineTipDate'+Suffix);
		drawText(leftTop[0]+5, leftTop[1] + FontSize * 2, 'Strike rate:'
				+ data[1], 'mlineChartId'+Suffix,'mlineTipPrice'+Suffix);
	}
	function drawText(x, y, msg, groupId,id) {
		var text = new Text();
		text.setGroupId(groupId);
		text.setId(id);
		text.setX(x);
		text.setY(y);
		text.setMsg(msg);
		text.draw();
	}
	return {
		"startDraw" : startDraw,
		"modifyLastPoint":modifyLastPoint
	};
};