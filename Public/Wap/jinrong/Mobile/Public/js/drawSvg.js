function get_friends()  
{  
         $.ajax({  
             type: "get",  
             async: false,  
             url: "http://m.wogaogongyi.com.cn/api/get_new_win60/uid-"+uid,  
             dataType: "jsonp",  
             success: function(data){
				if(data.count > 0){
					$("#friends").append('<i class="friends_tip" style="display: block;background:#f00;border-radius:50%;width: 0.6em;height: 0.6em;top: 5px;right:-0.2em;position:absolute;margin-right: 45%;"></i>');
				}
             },  
             error: function(){   
             }  
         });  
}
function show_price(){	
	var nowprice = $("#now_price").html();
	if(nowprice=='0.00'){
		//window.location.reload();
	}
}

$(document).ready(function(){

	 /* setInterval(function(){
        ajaxCurl("POST","/Wap/trading/setmin",'',true,function(res){});
      },60000);//定时 五秒
	  */
	  
  	//1:请求资产类型
  	ajaxCurl("GET","/Wap/trading/getCapitalTypes",'',true,function(res){
	  //存储资产列表 此为全局变量
      window.param.CapitalList = res;
	  //2:加载热门资产
	  setCapitalList("hot");
	  //画图
	  setTimeout(function(){
		drawKline(getCapitalTypeById("sw_active"), getDiffById("time_diff"), "svgId", "", "deal_value");
	  },1000);
	  setExpirationTime('time_menu');
	  setExpirationTime('time_menu1');
	});
   window.setInterval(function() {show_price();}, 10000);
//get_friends();
  //window.setInterval(function() {get_friends();}, 10000);
});
  
