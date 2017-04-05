function previewImg(target,width,height){
	width=width?(width+"px"):"auto";
	height=height?(height+"px"):"auto";
	if($('#'+target).val()){
		var html='<img src="'+$('#'+target).val()+'" style="height:'+height+';width:'+width+';" />';
	}
	else{
		var html='没有图片';
	}
	art.dialog({title:'图片预览',content:html});
}

function selectAsset2(target,width,height,type){
	art.dialog.data('width', width);
	art.dialog.data('height', height);
	art.dialog.data('target', target);
	art.dialog.data('lastpic', $('#'+target).val());
	type = type || 'default';
	art.dialog.open('/index.php?m=User&c=Index&a=asset&tpl=2&type='+type,{lock:false,title:'选择图片',width:680,height:440,yesText:'关闭',background: '#000',opacity: 0.87});
}

function selectAsset(target,type){
	BootstrapDialog.uploadTarget = target;
	if(window.UPLOAD_DIALOG == undefined){
		/*window.UPLOAD_DIALOG = new BootstrapDialog();
        UPLOAD_DIALOG.setTitle('图片管理');
		var $message = $('<div></div>');
		$message.load('/index.php?m=User&c=Index&a=asset&type=default',function(){
			//setTimeout('',1000);
			//initUploadDialog();
		});
        UPLOAD_DIALOG.setMessage($message);
        UPLOAD_DIALOG.open();*/
		window.UPLOAD_DIALOG = BootDialog.showFrame('图片管理','/index.php?m=User&c=Index&a=asset&type=default');
	}
	else {
		window.UPLOAD_DIALOG.open();
	}
}

function selectLink(target,width,height,type){
	width = width || 680;
	height = height || 440;
	type = type || 'default';
	art.dialog.data('target', target);
	art.dialog.open('/index.php?m=User&c=Index&a=link',{lock:false,title:'选择功能链接',width:680,height:440,yesText:'关闭',background: '#000',opacity: 0.87});

}