    var tt = 0;
	function checklogin() {		
		$.ajax({
			type: 'POST',
			url: ROOT+"/Wap/trading/getSameUser",//"{:U('/Trade/get_time')}",
			success: function(msg){
				//
					var msg = eval('('+msg+')');
							
					if(msg[0]){ //0是锁定 1是正常
						//如果锁定 清除session,直接跳转到登录页面
						window.location.href = ROOT+"/mobile.php";
					}
			}
		});
		
	}
	
	tt = window.setInterval(checklogin,5000);
	

