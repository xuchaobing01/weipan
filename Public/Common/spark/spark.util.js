/**
 * @spark.util JS 常用工具集
 * @author yanqizheng
 * @data 2014/03/17
 */
(function(){
	window.spark= window.spark || {};
	spark.alertHtml='<div class="modal fade" id="-spark-alert-dialog" tabindex="-1" role="dialog" aria-labelledby="-spark-alert-dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="-spark-alert-dialog-title">温馨提示</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button></div></div></div></div>';
	spark.confirmHtml='<div class="modal fade" id="-spark-confirm-dialog" tabindex="-1" role="dialog" aria-labelledby="-spark-alert-dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title" id="-spark-alert-dialog-title">温馨提示</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default btn-ok">确定</button><button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">关闭</button></div></div></div></div>';
	
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
})(window);