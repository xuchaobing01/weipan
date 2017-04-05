$(document).ready(function() {
	$('#submit_but').click(function(e) {
		e.preventDefault();
		//var email = $.trim($('#old_password').val());
		var old_password = $.trim($('#old_password').val());
		var new_password = $.trim($('#new_password').val());
		var re_password = $.trim($('#re_password').val());
		if (old_password.length < 6 || old_password.length > 32) {
			//alert('密码长度必须是6~32位');
			showLoading('原密码输入错误', 2000);
			return false;
		}
		if (new_password.length < 6 || new_password.length > 32) {
			//alert('新密码长度必须是6~32位');
			showLoading('新密码长度必须是6~32位', 2000);
			return false;
		}
		if (new_password != re_password) {
			//alert('两次输入密码不一致');
			showLoading('两次输入密码不一致', 2000);
			return false;
		}
		return_ajax(old_password, new_password, re_password);
		
	});
	function return_ajax(old_password, new_password, re_password) {
		$.ajax({
			type : 'post',
			url : '/wap/user/save_password',
			data : 'old_password=' + old_password + '&new_password=' + new_password + '&re_password=' + re_password,
			dataType : 'JSON',
			async : true,
			success : function(data_t) {
				showLoading(data_t['info'], 2000); 
				if(data_t['status']==1 )
					setTimeout(window.location.href =ROOT+"/Wap/User/setting", 2000);
				
			},
			error : function(data) {
				alert("获取数据失败");
			},
		});
	}

});
