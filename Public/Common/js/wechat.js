/**
 * 微信浏览器专用JS
 */

function Wechat(options){
	if(navigator.userAgent.indexOf('MicroMessenger')==-1){
		return false;
	}
	if(options && typeof options == "function"){
		Wechat.readyFunction = options;
	}
	document.addEventListener('WeixinJSBridgeReady', Wechat.ready,false);
}

Wechat.ready=function(){
	if(Wechat.readyFunction){
		Wechat.readyFunction.call();
	}
}

/**
 * @method showOptionMenu 显示右上角按钮
 */
Wechat.showOptionMenu = function(){
	WeixinJSBridge.call('showOptionMenu');
}

/**
 * @method hideOptionMenu 隐藏右上角按钮
 */
Wechat.hideOptionMenu = function(){
	WeixinJSBridge.call('hideOptionMenu');
}

/**
 * @method showToolbar 显示导航栏
 */
Wechat.showToolbar = function(){
	WeixinJSBridge.call('showToolbar');
}

/**
 * @method hideToolbar 隐藏导航栏
 */
Wechat.hideToolbar = function(){
	WeixinJSBridge.call('hideToolbar');
}
/*
function shareFriend() {
	WeixinJSBridge.invoke('sendAppMessage',{
		"appid": wechat.appid,
		"img_url": wechat.imgUrl,
		"img_width": "100",
		"img_height": "100",
		"link": wechat.lineLink,
		"desc": wechat.desc,
		"title": wechat.title
		}, function(res) {
			_report('send_msg', res.err_msg);
	});
}

function shareTimeline(appid,title,imgUrl,lineLink,desc) {
    WeixinJSBridge.invoke('shareTimeline',{
		"appid": appid,
		"img_url": imgUrl,
		"img_width": "100",
		"img_height": "100",
		"link": lineLink,
		"desc": desc,
		"title": title
		}, function(res) {
		_report('timeline', res.err_msg);
	});
}

function shareWeibo(title,imgUrl,lineLink,desc) {
	WeixinJSBridge.invoke('shareWeibo',{
		"title": title,
		"img_url": imgUrl,
		"content": desc,
		"url": lineLink,
		}, function(res) {
		_report('weibo', res.err_msg);
	});
}
*/
/**
 @example
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	WeixinJSBridge.on('menu:share:appmessage', function(argv){
		shareFriend();
	});

	WeixinJSBridge.on('menu:share:timeline', function(argv){
		shareTimeline();
	});

	WeixinJSBridge.on('menu:share:weibo', function(argv){
		shareWeibo();
		});
	}, false);
*/