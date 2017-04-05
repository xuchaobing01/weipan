        var range = 600;             //距下边界长度/单位px  
        var elemt = 300;           //插入元素高度/单位px  
        var maxnum = 'no';            //设置加载最多次数  
        var num = 2;  
        var totalheight = 0;  
        var PAGESIZE= 15;  	
		var type= 0; 
 $(document).ready(function(){  
		load_water(1,15,0,1);
        var all_list = $("#all_list");                     //主体元素  
        $(window).scroll(function(){  
            var srollPos = $(window).scrollTop();    //滚动条距顶部距离(页面超出窗口的高度)  
              
            //console.log("滚动条到顶部的垂直高度: "+$(document).scrollTop());  
            //console.log("页面的文档高度 ："+$(document).height());  
            //console.log('浏览器的高度：'+$(window).height());  
              
            totalheight = parseFloat($(window).height()) + parseFloat(srollPos);  
            if(($(document).height()-range) <= totalheight  && num != maxnum) {  
				$(".more-btn").removeClass("hide");
				load_water(num,PAGESIZE,0,0);
                num++;  
            }  
        }); 
    $("#pay").click(function() {
		$("#all_list").removeClass("hide");
        $('#pay').attr('class', 'button margin-bottom12 jz-flex-col');
		$('#cash').attr('class', 'button button-gray button-f7 jz-flex-col');
		$("#cash_tips").addClass("hide");
		load_water(1,15,0,1);
    });	
    $("#cash").click(function() {
		$("#all_list").removeClass("hide");
        $('#cash').attr('class', 'button margin-bottom12 jz-flex-col');
		$('#pay').attr('class', 'button button-gray button-f7 jz-flex-col');
		$("#cash_tips").removeClass("hide");
		load_water(1,15,1,1);
    });	
	$("#screen").click(function() {
			$.ajax({
            type: "POST",
            dataType: "json",
            url: '/Wap/user/set_sim',
            beforeSend: function() {
                showLoading('切换中...');
            },
            success: function(n) {
                var i = n.status;
                switch (i) {
                case 1:
                    showLoading('切换成功！', 2000);
					setTimeout(location.reload(), 2000);
                    break;
                case - 0 : default:
                    showLoading(n.info, 3000);
                }
            },
            error: function() {
                hideLoading();
            },
            complete: function() {
                //hideLoading()
            }
        })
	});
 });  
function load_water(num,PAGESIZE,listtype,isnull){
	    $.ajax({
        type : "POST",
        url : "/wap/user/history_refresh",
        data : "page=" + num + "&pagesize=" + PAGESIZE + "&type="+listtype,
        datatype : 'JSON',
        async : true,
        beforeSend: function() {
            showLoading('载入中...');
        },
        success : function(response) {
			$(".more-btn").addClass("hide");
			hideLoading(); 
			if(response.status==1){
				if(isnull==1){
					$("#all_list").html('<tr class="rec-tr"><th class="rec-th">到期时间</th><th class="rec-th">资产类型</th><th class="rec-th">涨/跌</th><th class="rec-th">买入金额</th><th class="rec-th">盈利情况</th><th class="rec-th">订单状态</th> </tr>');
					$(".com-empty").addClass("hide");
				}
				$.each(response.list, function(key, value) {
                //  alert(value.cash_type==1);
                //$("#all_page").append('<div class="J_zclx"> <h4 class="L_J_zclx">' + value.cash_type + ' ￥' + value.amount + '<span class="J_Tmoney">' + value.auth_state + '</span></h4><div class="ui-block-a J_block01 J_border_B L_J_zclx01"><h5>' + value.create_time + '&nbsp&nbsp&nbsp' + value.cash + '&nbsp&nbsp&nbsp流水号：' + value.id + '</h5></div></div>');
				$("#all_list").append('<tr class="rec-tr" onclick="get_history(\''+value.id+'\',this)" id="'+value.id+'"><td class="rec-td fcgray3">'+value.trade_time+'</td><td class="rec-td">'+value.option_key+'</td><td class="rec-td">'+value.trade_direction+'</td><td class="rec-td fcgray3">'+value.trade_amount+'</td><td class="rec-td fcgray3">'+value.profit+'</td><td class="rec-td fcgray3">'+value.is_win+'</td></tr>');
				});
			}else{
				num==maxnum;
				if(num==1){
					$("#all_list").addClass("hide");
					$(".come-txt").text("您还没有任何记录");
					$(".com-empty").removeClass("hide");
				}else{
					$(".come-txt").text("没有更多记录");
					$(".com-empty").removeClass("hide");
				}
				return false; 
			}
        },
            error: function() {
                hideLoading();
            },
            complete: function() {
                //hideLoading()
            }
        //  "json");
    });
}
function get_history(id,obj) {
    return false;
	window.location.href =ROOT+"/wap/user/get_history/id/"+id+".html"; 
}