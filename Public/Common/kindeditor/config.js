KindEditor.ready(function(K) {
	window.editor = K.create('#content', {
	resizeType : 1,
	allowPreviewEmoticons : false,
	allowImageUpload : true,
	uploadJson : '/index.php?m=User&c=Qiniu&a=kindEditorUpload',
	items : [
	'source','undo','clearhtml','hr', '|', 'fontname', 'fontsize','forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 
	'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
	'insertunorderedlist', '|', 'emoticons', 'image','multiimage','link', 'unlink','baidumap','lineheight','table','anchor','preview','print','code','cut']
	});
});