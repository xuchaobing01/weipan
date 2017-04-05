$(document).ready(function(){
    $("#submit").click(function(e){
        e.preventDefault();
        
        //姓名验证
        var real_name=document.getElementById("real_name").value;
        var reg = /^[\u4E00-\u9FA5\uF900-\uFA2D]{2,}$/; 
        
        if(real_name.length==0){
			showLoading('姓名不能为空', 2000);
            return false;
        }else if(!reg.test(real_name)){
			showLoading('请输入正确的姓名', 2000);
            return false;
        }
        
        //身份证号码验证
        
        var id_number = $.trim($('#id_number').val());
        var reg=/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
        //var Errors = "身份证号码不正确";
		//alert(id_number.length);
        if(id_number.length==0){
			showLoading('身份证号码不能为空', 2000);
            return false;
        }else if(!reg.test(id_number)){
			showLoading('请输入正确的身份证号码', 2000);
            return false;
        }
        /*    
        //银行卡号验证
        var id_bank=document.getElementById("bank_card_number").value;
        var reg=/^([0-9]{10,})$/;
        if(id_bank.length==0){
            $("#hint").addClass("error_area L_error").text("*  银行卡号码不能为空");
            return false;
        }else if(!reg.test(id_bank)){
            $("#hint").addClass("error_area L_error").text("请输入正确的银行卡号");
            return false;
        }else{
            $("#hint").removeClass("error_area L_error").text("");
        }
        //开户银行验证
        var bank_deposit=document.getElementById("bank_deposit").value;
		if(bank_deposit.length==0){
            $("#hint").addClass("error_area L_error").text("*  开户银行不能为空");
            return false;
        }else{   
            $("#hint").removeClass("error_area L_error").text("");
        }
        //银行地址验证
        var bank_address=document.getElementById("bank_address").value;
        var reg=/^[\u4E00-\u9FA5\uF900-\uFA2D]{1,}$/;
        
        if(bank_address.length==0){
            $("#hint").addClass("error_area L_error").text("*  银行地址不能为空");
            return false;
        }else if(!reg.test(bank_address)){
            $("#hint").addClass("error_area L_error").text("*  请输入正确的银行地址");
            return false;
        }else{   
            $("#hint").removeClass("error_area L_error").text("");
        }
       
        */
        //信息提交
        //submit_ajax(bank_card,bank,bank_address,real_name,id_number);
		//submit_ajax(real_name, id_number, id_bank, bank_deposit, bank_address);
		post_ajax(real_name, id_number);
    }); 
	function post_ajax(real_name, id_number){
			$.ajax({
            type: "POST",
            dataType: "json",
            url: ROOT + '/Wap/user/bank_info_save',
            data:'real_name=' + real_name + '&id_number=' + id_number,
            beforeSend: function() {
                showLoading('提交中...')
            },
            success : function(data_t) {
                if (data_t['status'] == 1) {
                    showLoading('提交成功！', 2000);
                    //alert(data_t['info']);
					window.location.href=ROOT+'/Wap/user/private_person';
                } else {
                    showLoading('提交失败，请重试', 2000);
                }
            },
            error: function() {
                hideLoading()
            },
            complete: function() {
                //hideLoading()
            }
        })
	}
$("#overseaUser").click(function() {
	showLoading('海外户籍用户请联系客服进行认证', 5000);
	});
});
