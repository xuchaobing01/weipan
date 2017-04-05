// Initialize your app
var app = new Framework7({
	modalTitle: '',
	modalButtonOk: '确定',
	modalButtonCancel: '取消',
	onAjaxStart: function (xhr) {
        app.showIndicator();
    },
    onAjaxComplete: function (xhr) {
        app.hideIndicator();
    }
});

// Export selectors engine
var $$ = Framework7.$;

// Add view
var mainView = app.addView('.view-main', {
    dynamicNavbar: true
});

// Event listener to run specific code for specific pages
$$(document).on('pageInit', function (e) {
    var page = e.detail.page;
	if(page.name == 'userinfo'){
		initPage4Name();
	}
	else if(page.name == 'set'){
		$$(page.container).find('.button.active').on('click',function(e){
			var $page = $(this).parents('.content-block');
			var optLimit = parseInt($(this).attr('data-opt-number'));
			var opts = $page.find('input:checked');
			if(opts.length == 0){
				app.alert('您至少要选择一项！');
				return false;
			}
			else if(opts.length > optLimit){
				app.alert('最多只能选择'+optLimit+'项！');
				return false;
			}
			var data = {};
			data['ans'] = opts.map(function(el,i){
				return $(this).val()
			}).toArray().join(',');
			data['qid'] = $(this).attr('data-timu-id');
			$.ajax({
				url:$(this).attr('data-href'),
				data:data,
				type:'post',
				dataType:'text',
				success:function(resp){
					mainView.loadPage(resp);
				}
			});
			return false;
		});
	}
	else if(page.name == 'comment'){
		console.log(page.name);
		$$(page.container).find('.next-btn').on('click',function(e){
			var $page = $(this).parents('.content-block');
			var txt = $page.find('textarea').val();
			if(txt == ''){
				app.alert('您的建议是我们最大的动力哦！');
				return false;
			}
			var data = {'jianyi':txt};
			console.log(data);
			$.ajax({
				url:$(this).attr('data-href'),
				data:data,
				type:'post',
				dataType:'text',
				success:function(resp){
					mainView.loadPage(resp);
				}
			});
			return false;
		});
	}
});


function initPage4Name(){
	console.log("init page...");
	$("#save-btn").on("click",function(){
		var $username = $("#username");
		var username = $username.val();
		var $phone = $("#phone");
		var phone = $phone.val();
		if(username == ""){
			app.alert("请输入用户名!");
			$username.focus();
			return false;
		}
		if(phone == ""){
			app.alert("请输入手机号码!");
			$phone.focus();
			return false;
		}

		var regu =/^[0-9]{11}$/;

		var re = new RegExp(regu);

		if(!re.test(phone)){
			app.alert("请输入正确的手机号码!");
			$phone.focus();
			return false;
		}

		var submitData = {
			"username":username,
			"phone":phone
		};

		$.ajax({
			type : "POST",
			url : $(this).attr('data-url'),
			data : submitData,
			contentType: "application/x-www-form-urlencoded; charset=utf-8",
			dataType : "json",
			success : function(data){
				if(data.success == true){
					mainView.loadPage(data.url);
				}
				else{
					app.alert('提交错误!');
				}
			}
		});
	});
}