/*   var qs = function(e) {
      return document.querySelector(e);
    };
     var button = document.getElementById("send");

    var geetest = qs(".geetest");
    button.onclick = function() {
      geetest.style.display = "block";
    };
    var close = document.getElementById("close");
    close.onclick = function() {
      geetest.style.display = "none";
    };
    qs(".bg").onclick = function() {
      geetest.style.display = "none";
    };
    window.gt_custom_ajax = function(result, id, message) {
      if(result) {
        qs('#' + id).parentNode.parentNode.style.display = "none";
	var InterValObj; //timer变量，控制时间  
	var count = 60; //间隔函数，1秒执行  
	var curCount;//当前剩余秒数  
		curCount = count;
    value = $('#' + id).find('input');
    var data = {"geetest_challenge":value[0].value,"geetest_validate":value[1].value,"geetest_seccode":value[2].value}
	$.post(ROOT + '/mobile.php/Public/send_voice_data',{ 
    data:JSON.stringify(data)
    },function(result){
	  if(result.status==0){
	   showLoading(result.message, 1000);
	  GeeTest[0].refresh();
	  }else{
				var time = 60;
				function timeCountDown(){
					if(time==0){
						clearInterval(timer);
						$("#send").show();//启用按钮	removeClass				
						$("#wait").addClass('hide');
						return true;
					}
					$("#send").hide();
					$('#wait').text(time+"秒后重发");
					$("#send").removeClass('hide');
					time--;
					return false;
				}
				$("#send").hide();
				timeCountDown();
				var timer = setInterval(timeCountDown,1000);
				//InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次  
				$("#msg_a").text(result.message);
                $("#msg_tips").removeClass("hide");
				GeeTest[0].refresh();
			  } 
      },"json"
		)
      }
    }
*/
var captcha_img = $('#verify');  
var verifyimg = captcha_img.attr("src");  
captcha_img.attr('title', '点击刷新');  
captcha_img.click(function(){  
    if( verifyimg.indexOf('?')>0){  
        $(this).attr("src", verifyimg+'&random='+Math.random());  
    }else{  
        $(this).attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());  
    }  
}); 
	var button = document.getElementById("send");
	var InterValObj; //timer变量，控制时间  
	var count = 60; //间隔函数，1秒执行  
	var curCount;//当前剩余秒数  
	curCount = count;
	function get_sms(){
    var verify_code = $("#verify_code").val();
	//var phone = $("#username").val();
	var data = {"verify_code":verify_code}
	$.post(ROOT + '/Wap/User/send_voice_data',{ 
    data:JSON.stringify(data)
    },function(result){
	  if(result.status==0){
	   showLoading(result.message, 1000);
	  }else{
				var time = 60;
				function timeCountDown(){
					if(time==0){
						clearInterval(timer);
						$("#send").show();//启用按钮	removeClass				
						$("#wait").addClass('hide');
						return true;
					}else{
					$("#send").hide();
					$('#wait').text(time+"秒后重发");
					$("#send").removeClass('hide');
						time--;
						return false;
					}
				}
				$("#send").hide();
				timeCountDown();
				var timer = setInterval(timeCountDown,1000);
				//InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次  
                //showLoading("验证码已发送！", 1000);
				$("#msg_a").text(result.message);
                $("#msg_tips").removeClass("hide");
			  }
	},"json")
    }
	function get_sms_text(){
    var verify_code = $("#verify_code").val();
	//var phone = $("#username").val();
	var data = {"verify_code":verify_code}
	$.post(ROOT + '/Wap/User/img_send_sms_text',{ 
    data:JSON.stringify(data)
    },function(result){
	  if(result.status==0){
	   showLoading(result.message, 1000);
	  }else{
				var time = 60;
				function timeCountDown(){
					if(time==0){
						clearInterval(timer);
						$("#send").show();//启用按钮	removeClass				
						$("#wait").addClass('hide');
						return true;
					}else{
					$("#send").hide();
					$('#wait').text(time+"秒后重发");
					$("#send").removeClass('hide');
						time--;
						return false;
					}
				}
				$("#send").hide();
				timeCountDown();
				var timer = setInterval(timeCountDown,1000);
				//InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次  
                //showLoading("验证码已发送！", 1000);
				$("#msg_a").text(result.message);
                $("#msg_tips").removeClass("hide");
			  }
	},"json")
    }
    $("#isok").click(function() {
        $("#msg_tips").addClass("hide");
    });
	    $("#send").click(function() {
        get_sms();
    });
	    $("#findPwd_btn").click(function() {
        get_sms_text();
    });