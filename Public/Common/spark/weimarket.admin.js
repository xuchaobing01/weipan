/*!
 * weimarket common js for backend
 * @version 0.1.1
 */
/**
 *加载动画插件
 */

/**
 * @spark.util JS 常用工具集
 * @author yanqizheng
 * @data 2014/03/17
 */
(function(){
	window.spark= window.spark || {};
	spark.MODAL_OVERLAY_TPL = '<div class="spark-modal-overlay"></div>';
	spark.INDICATOR_TPL = '<div id="sparkIndicator" style="position: fixed;left:50%;top:50%;margin-top:-40px;margin-left:-40px;border-radius:10px;background:rgba(0,0,0,.8); 	width:80px;height:80px;z-index: 10001;"></div>';
	spark.PRELOADER_TPL = '<div id="sparkPreloader" style="position: fixed;left:50%;top:40%;margin-top:-40px;margin-left:-120px;border-radius:10px;background:rgb(190, 190, 190); 	width:240px;height:80px;padding:10px;z-index: 10002;"><div style="height:40px;position:relative;" class="spin"></div><div class="text" style="color:#333;text-align:center;padding: 5px 0px 10px;">正在加载...</div></div>';
	
	spark.alertHtml='<div class="modal fade" id="-spark-alert-dialog" tabindex="-1" role="dialog" aria-labelledby="-spark-alert-dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="-spark-alert-dialog-title">温馨提示</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">确定</button></div></div></div></div>';
	spark.confirmHtml='<div class="modal fade" id="-spark-confirm-dialog" tabindex="-1" role="dialog" aria-labelledby="-spark-alert-dialog" aria-hidden="true"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="-spark-alert-dialog-title">温馨提示</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default btn-ok">确定</button><button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">取消</button></div></div></div></div>';
	
	spark.alert=function(message){
		if(!spark.alertDialog){
			spark.alertDialog=$(spark.alertHtml).appendTo('body');
		}
		var config={title:'温馨提示',content:''};
		if(typeof message == 'string'){
			config.content=message;
		}
		else{
			config.title = message.title||config.title;
			config.content = message.content;
		}
		spark.alertDialog.find('.modal-title').html(config.title);
		spark.alertDialog.find('.modal-body').html(config.content);
		spark.alertDialog.modal('show');
	}
	
	/**
	 *@method spark.confirm 用户确认对话框
	 *@param message 提示用户的信息
	 *@param okCallback 用户确定后执行的函数
	 *@param cancelCallback 用户取消后执行的函数
	 */
	spark.confirm=function(message,okCallback,cancelCallback){
		if(!spark.confirmDialog){
			spark.confirmDialog=$(spark.confirmHtml).appendTo('body');
		}
		spark.confirmDialog.find('.modal-body').html(message);
		spark.confirmDialog.find('.btn-ok').unbind('click').bind('click',function(){
			spark.confirmDialog.modal('hide');
			okCallback();
		});
		spark.confirmDialog.modal('show');
	}
	
	/**
	 *@method spark.confirm_jump 跳转确认对话框
	 *@param message 提示的信息
	 *@param url 用户确定后跳转的地址
	 */
	spark.confirm_jump = function(message,url){
		spark.confirm(message,function(){
			location.href=url;
		});
	}
	spark.redirect = function(url,timeout){
		if(timeout ==undefined || isNaN(parseInt(timeout))){
			location.href = url;
		}
		else {
			setTimeout(function(){
				location.href = url;
			},parseInt(timeout));
		}
	}
})(window);

if(typeof PNotify == 'function'){
	PNotify.prototype.options.delay = 3000;
	function notify(text,type){
		type = type || 'success';
		new PNotify({text:text,type:type});
	}
}
if(typeof BootstrapDialog == 'function'){
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_DEFAULT] = '通知';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_INFO] = '通知';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_PRIMARY] = '通知';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_SUCCESS] = '成功';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_WARNING] = '警告';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_DANGER] = '警告';
	BootstrapDialog.DEFAULT_TEXTS['OK'] = '确定';
	BootstrapDialog.DEFAULT_TEXTS['CANCEL'] = '取消';
	BootstrapDialog.DEFAULT_TEXTS['CONFIRM'] = '确认';
	window.BootDialog = BootstrapDialog;
	BootDialog.showFrame = function(title,url,height,options){
		options = options || {};
		var dialog = new BootDialog(options);
		height = height || 320;
		dialog.setTitle(title);
		var $content = $('<iframe src="'+url+'"frameborder="0" style="height:'+height+'px;width:100%;"></iframe>');
		dialog.setMessage($content);
		dialog.open();
		return  dialog;
	}
}
if(typeof Spinner == 'function'){
	$.fn.spin = function(opts) {
		this.each(function() {
			var $this = $(this),data = $this.data();
			if (data.spinner) {
				data.spinner.stop();
				delete data.spinner;
			}
			if (opts !== false) {
				data.spinner = new Spinner($.extend({color: $this.css('color')}, opts)).spin(this);
			}
		});
		return this;
	};
	
	spark.showIndicator = function(showOverlay){
		if(showOverlay == undefined) showOverlay = true;
		if($('#sparkIndicator').length==0){
			$(spark.INDICATOR_TPL).appendTo('body');
			new Spinner({color:'#fff', lines: 10,length:10,width:4}).spin(document.getElementById('sparkIndicator'));
		}
		if(showOverlay==true)spark.showOverlay();
	}
	spark.hideIndicator = function(){
		$('#sparkIndicator').remove();
		spark.hideOverlay();
	}
	spark.showPreloader = function(text,showOverlay){
		text = text|| '正在加载';
		if(showOverlay == undefined) showOverlay = true;
		if($('#sparkPreloader').length==0){
			var el = $(spark.PRELOADER_TPL);
			el.find('.text').text(text);
			el.appendTo('body');
			new Spinner({color:'#333', lines: 10,length:6,width:3,radius:8,top:'10px'}).spin($('#sparkPreloader>.spin')[0]);
			if(showOverlay==true)spark.showOverlay();
		}
	}
	spark.hidePreloader = function(options){
		$('#sparkPreloader').remove();
		spark.hideOverlay();
	}
	
	spark.showOverlay = function(){
		if($('.spark-modal-overlay').length != 0){
			$('.spark-modal-overlay').addClass('visible');
		}
		else{
			$(spark.MODAL_OVERLAY_TPL).addClass('visible').appendTo('body');
		}
	}

	spark.hideOverlay = function(){
		$('.spark-modal-overlay').removeClass('visible');
	}
	spark.toggleOverlay = function(){
		$('.spark-modal-overlay').toggleClass('visible');
	}
}
$(function(){
	$("[data-toggle]").each(function(){
		var toggle = $(this).attr('data-toggle');
		if(toggle == 'upload-image'){ //处理上传图片
			$(this).on('click',function(){
				selectAsset($(this).attr('data-target'));
			});
		}
		else if(toggle == 'spark-upload-image'){
			var dataOption = $(this).attr('data-option');
			if(dataOption !=''){
				var option = JSON.parse(dataOption);
			}
			else var option = {};
			$(this).append('<input type="hidden" name="'+option.name+'" id="'+option.name+'" value="'+option.src+'" /><img id="'+option.name+'Holder" src="'+option.src+'" /><div class="upload-image-btn"><span class="btn btn-xs clear"><i class="fa fa-trash"></i>&nbsp;清除</span><span class="btn btn-xs select"><i class="fa fa-edit"></i>&nbsp;选择</span></div>');
			$(this).find('.select').click(function(){
				selectAsset(option.name);
			});
			$(this).find('.clear').click(function(){
				var $wrapper = $(this).parents('.upload-image-wrapper');
				$wrapper.find('input:hidden').val('');
				$wrapper.find('img').attr('src','');
				$wrapper.removeClass('not-empty');
			});
			$(this).find('input:hidden').change(function(){
				if($(this).val()!=''){
					$(this).parent().addClass('not-empty');
				}
				else $(this).parent().removeClass('not-empty');
			});
		}
		
		else if(toggle == 'datetime-picker'){ //日期选择
			var minView = $(this).attr('data-minview')||2;
			var format = $(this).attr('data-format')||"yyyy-mm-dd";
			$(this).datetimepicker({
				format: format,//日期格式
				language:'zh-CN',//语言
				minView:minView,
				autoclose:true
			});
		}
		else if(toggle == 'preview-qrcode'){
			$(this).on('click',function(){
				$.getJSON($(this).attr('data-src'),function(res){
					BootDialog.show({
						'title':'扫描二维码预览页面',
						'message':'<div class="responsive-img" style="text-align:center;"><img src="'+res.qrcode+'"</div>'
					})
				});
			})
		}
		else if(toggle == 'validate' && this.tagName == 'FORM'){
			var position = $(this).attr('data-postion') || "centerRight";
			$(this).validationEngine("attach",{ 
				promptPosition:position,
				scroll:true,
				showOneMessage:true
			});
		}
		else if(toggle=='ajax-link'){
			$(this).on('click',function(){
				var $this = $(this);
				var tip = $(this).attr('data-tip');
				if(tip){
					spark.confirm(tip,function(){
						spark.showIndicator();
						$.getJSON($this.attr('data-href'),function(res){
							if(res.errcode == 0){
								notify(res.errmsg);
								if($this.attr('data-reload') == 'true'){
									setTimeout(function(){
										location.reload();
									},2000);
								}
							}
							else notify(res.errmsg,'error');
							spark.hideIndicator();
						});
					});
				}
				else{
					spark.showIndicator();
					$.getJSON($this.attr('data-href'),function(res){
						if(res.errcode == 0){
							notify(res.errmsg);
							if($this.attr('data-reload') == 'true'){
								setTimeout(function(){
									location.reload();
								},2000);
							}
						}
						else notify(res.errmsg,'error');
						spark.hideIndicator();
					});
				}
				return false;
			})
		}
		else if(toggle=='remote-dialog'){
			$(this).click(function(){
				BootDialog.showFrame($(this).attr('data-title'),$(this).attr('data-href'),400);
			});
		}
	});
	$('[data-toggle="popover"]').popover();
	$("[data-toggle='upload-image']").click(function(){
		selectAsset($(this).attr('data-target'));
	});
})

function sprintf(){
	var arg = arguments,
		str = arg[0] || '',
		i, n;
	for (i = 1, n = arg.length; i < n; i++) {
		str = str.replace(/%s/, arg[i]);
	}
	return str;
}