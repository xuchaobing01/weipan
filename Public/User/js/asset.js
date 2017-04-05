var type = 1;
console.log('load asset.js');
function initUploadDialog(){
	
};

var page=-1;
function getMoreImg(f){
	if(f<0){
		if(page<=0){
			return;
		}
		page--;
	}
	else{page++;}
	$.ajax({
		url: "/user/index/imgs.html",
		data:{page:page},
		type:'get',
		dataType:'json',
		success:function(resp){
			$('#imgList').empty();
			$.each(resp,function(i,img){
				var $li = $('<li><img src="'+img.url+'" width="120px" /><a href="javascript:;" class="cBtn spr db " id="'+img.id+'"> </a></li>');
				$li.find('img').click(function(){
					$(this).toggleClass("selected");
					$("#imgList img.selected").not(this).removeClass("selected");
				});
				$li.find('img').dblclick(function(e){
					e.stopPropagation();
					$(this).click();
					$("#select").click();
				});
				$li.find('a').click(function(){
					var img_rm=$(this).parent();
					var img_id=$(this).attr("id");
					$.ajax({
						url:"/user/index/imgs_delete.html?id="+img_id,
						dataTpe:"json",
						success:function(e){
							$(img_rm).remove();
						}
					});

				});

				$('#imgList').append($li);
			});
			$('#imgList').attr('load-state',1);
		}
	});
}

function activeImg(){
	$(this).toggleClass("selected");
	$("#imgList img.selected").not(this).removeClass("selected");
}

function success(url){
	set(url);
	setTimeout("window.UPLOAD_DIALOG.close()", 1000);
}

function set(url){
	var $target = $("#"+BootstrapDialog.uploadTarget);
	if(typeof window[BootstrapDialog.uploadCallback] == 'function'){
		window[BootstrapDialog.uploadCallback](url);
		BootstrapDialog.uploadCallback = '';
	}
	else{
		$target.val(url);
		$target.change();
		var $targetHolder=$('#'+BootstrapDialog.uploadTarget+'Holder');
		if($targetHolder.length!=0){
			$targetHolder.attr('src', url);
		}
	}
}
//初始化
console.log($("#fileUpload_BTN").length);
$("#fileUpload_BTN").AjaxFileUpload({
	action: "/User/Qiniu/upload.html')}",
	onSubmit: function(filename) {
		return true;
	},
	onComplete: function(filename, resp) {
		if(resp.error !=0){
			alert(resp.message);
		}
		else{
			success(resp.url);
		}
	}
});
//当"历史图片"tab 被激活时加载图片
$('#selectAssetDialog a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	if($(e.target).attr('href') == '#history' && $('#imgList').attr('load-state')=='0'){
		getMoreImg(1);
	}
	if($(e.target).attr('href') == '#history'){
		$("#select").show();
	}else{
		$("#select").hide();
	}
});
$("#select").click(function(){
	console.log('select');
	set($("#imgList img.selected").attr("src"));
	window.UPLOAD_DIALOG.close();
});
$('#imgUrlCommit').click(function(){
	var url = $('#imgUrl').val();
	if(url){
		set(url);
		window.UPLOAD_DIALOG.close();
	}
});

$('#membercard img').click(function(){
	$(this).toggleClass("selected");
	$("#membercard img.selected").not(this).removeClass("selected");
});

$("#selectCard").click(function(){
	set($("#membercard img.selected").attr("src"));
	window.UPLOAD_DIALOG.close();
});