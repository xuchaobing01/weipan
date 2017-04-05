/*
 * 注意：
 * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
 * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
 * 3. 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
 *
 * 如有问题请通过以下渠道反馈：
 * 邮箱地址：weixin-open@qq.com
 * 邮件主题：【微信JS-SDK反馈】具体问题
 * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
 */
wx.ready(function () {
  // 1 判断当前版本是否支持指定 JS 接口，支持批量判断


  // 5 图片接口
  // 5.1 拍照、本地选图
  var images = {
    localId: [],
    serverId: []
  };
  document.querySelector('#btn').onclick = function () {
    wx.chooseImage({
      success: function (res) {
        images.localId = res.localIds;
		if(res.localIds.length > 9){
			alert('最大只能上传9张图片');
			return;
		}
		$.each(res.localIds, function(key, val) {     
			$(".weui_uploader_files").append('<li class="weui_uploader_file" style="background-image:url('+res.localIds[key]+')"></li>');
			console.log(res.localIds[key]);
		});
		$("#number").text(res.localIds.length+'/9');
		if(res.localIds.length== 9){
			$(".weui_uploader_input_wrp").remove();
			
		}
        //alert('已选择 ' + res.localIds.length + ' 张图片');
      }
    });
  };

  // 5.2 图片预览
  /*document.querySelector('#previewImage').onclick = function () {
    wx.previewImage({
      current: 'http://img5.douban.com/view/photo/photo/public/p1353993776.jpg',
      urls: [
        'http://img3.douban.com/view/photo/photo/public/p2152117150.jpg',
        'http://img5.douban.com/view/photo/photo/public/p1353993776.jpg',
        'http://img3.douban.com/view/photo/photo/public/p2152134700.jpg'
      ]
    });
  };*/
$(document).bind('propertychange input', function () {  
        var counter = $('.weui_textarea').val().length;
		$("#zishu").text(counter);
});
function post_content(serverId,content){
	var json_serverId = JSON.stringify(serverId);
	$.post("/Wap/Trading/post_content",
                {  	'uid':uid,
                    'serverId':json_serverId,  
                    'content':content
                },  
                function(data){
					// data = eval('(' + data+ ')');
					console.log(data);
                    if(data.status==1){
						$ ('#toast').show ().delay (3000).fadeOut (100,window.history.back(-1));
					}else{
						alert('上传失败，请重试');
					}
                });
}
  // 5.3 上传图片
  document.querySelector('#showTooltips').onclick = function () {
	var content = $('.weui_textarea').val();
	if (content=='') {
        alert('发表的内容不能为空');
		return;
    };
    if (images.localId.length == 0) {
      return;
    }
    var i = 0, length = images.localId.length;
    images.serverId = [];
	$("#loadingToast").show();
    function upload() {
      wx.uploadImage({
        localId: images.localId[i],
		isShowProgressTips: 1,
        success: function (res) {
          i++;
          //alert('已上传：' + i + '/' + length);
		  
          images.serverId.push(res.serverId);
          if (i < length) {
            upload();
          }else{
			  $("#loadingToast").hide();
			  post_content(images.serverId,content);
			  console.log(images.serverId);
		  }
        },
        fail: function (res) {
          alert(JSON.stringify(res));
        }
      });
    }
    upload();
  };

});
wx.error(function (res) {
  alert(res.errMsg);
});
