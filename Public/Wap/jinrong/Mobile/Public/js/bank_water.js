        var range = 200;             //距下边界长度/单位px  
        var elemt = 500;           //插入元素高度/单位px  
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
    });  
function load_water(num,PAGESIZE,listtype,isnull){
	    $.ajax({
        type : "POST",
        url : "/Wap/user/bank_water_show",
        data : "page=" + num + "&pagesize=" + PAGESIZE + "&type="+listtype,
        datatype : 'JSON',
        async : true,
        success : function(response) {
			$(".more-btn").addClass("hide");
			if(response==''){
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
			if(isnull==1){
				$("#all_list").html('<tr class="rec-tr"><th class="rec-th">类型</th><th class="rec-th">金额</th><th class="rec-th">时间</th><th class="rec-th">状态</th></tr>');
				$(".com-empty").addClass("hide");
            }
				$.each(response, function(key, value) {
                //  alert(value.cash_type==1);
                //$("#all_page").append('<div class="J_zclx"> <h4 class="L_J_zclx">' + value.cash_type + ' ￥' + value.amount + '<span class="J_Tmoney">' + value.auth_state + '</span></h4><div class="ui-block-a J_block01 J_border_B L_J_zclx01"><h5>' + value.create_time + '&nbsp&nbsp&nbsp' + value.cash + '&nbsp&nbsp&nbsp流水号：' + value.id + '</h5></div></div>');
				$("#all_list").append('<tr class="rec-tr"><td class="rec-td">'+value.cash_type+'</td><td class="rec-td">'+value.amount+'元</td><td class="rec-td fcgray3">'+value.create_time+'</td><td class="rec-td fcgray3">'+value.auth_state+'</td></tr>');
            });
        },
        error : function(data) {
            //alert("获取数据失败");
        },
        //  "json");
    });
}