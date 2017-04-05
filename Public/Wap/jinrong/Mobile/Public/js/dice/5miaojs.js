/**
 * Created by JesseDing <dingxj7788@gmail.com> on 15/9/17.
 */
(function () {
	var gameId = 10002649;
	window.gameId = gameId;
    //yw.init({gameId: gameId, barrageEnabled: true, rankEnabled: true, disableSplash: true});
    //var iconUrl = "http://m.5miao.com/games/" + gameId + "/icon.png";
    //
    //var con = document.createElement("div");
    //con.style.display = "none";
    //var icon = document.createElement("img");
    //con.appendChild(icon);
    //icon.src = iconUrl;
    //document.body.insertBefore(con, document.body.firstChild);
    //addWxScript();
    //var defaultShare = document.title;
    //dataForWeixin.desc = defaultShare;
    //dataForWeixin.img = iconUrl;
    //dataForWeixin.title = defaultShare;
    //dataForWeixin.url = location.href;
    //dataForWeixin.success = onShareCall;
    //
    //yw.on('share.close', onShareCall);

	function onShareCall() {
		shareCall && shareCall();
	}

	window.setShareCall = function (call) {
		shareCall = call;
	}

	window.showRank = function (score, msg) {
		yw.showRank(score);
		document.title = dataForWeixin.desc = "投骰子运气倍棒，连赢{0}把，毫无压力！".replace("{0}",msg);
		updateShare();
	}
	window.ywShare = function (callback) {
		yw.openSocialShareMenu(null, callback);
	}
	window.updateShare = function () {
		refreshWxData();
		yw.setShare({
			title: dataForWeixin.title,
			text: dataForWeixin.desc,
			imageUrl: dataForWeixin.img
		});
	}
	window.updateShare();
})()