$(document).ready(function() {
	$('#input_money').click(function() {
            $('.active h1 b').html('0');
            $('.active h1 i').text('不返现');
    });
    $('#input_money').on('input propertychange',
    function() {
        $(".slct").removeClass("slct");
        var money = $("#input_money").val();
		if(money<0){
			$("#input_money").val('10');
            $('.active h1 b').text('10');
            $('.active h1 i').html('返现<span>' + 10 + '</span>元');
		}else if (money < 100) {
            $('.active h1 b').text(money);
            $('.active h1 i').text('不返现');
        } else {
            $(".slct").removeClass("slct");
            $('.active h1 b').text(money);
            $('.active h1 i').html('返现<span>' + money + '</span>元');
        }
    });
    $('.active ul li:not(.not)').click(function() {
        $(this).addClass('slct').siblings().removeClass('slct');
        //var text=$(this).find('i').text();
        //$('.active h1 b').text(text);
        $('.active h2').show();
        $('.active ul li.not input').fadeOut();
    })
    /*$('.active ul li.not input').blur(function(){
		var text=$(this).val();
		$('.active h1 b').text(text);
	$('.active h1 i').text('不返现');
	})
	*/
    $('.active ul li.not').click(function() {
        $(this).find('input').show().select();
    }); 
	$('.active ul li:not(.other)').click(function() {
        //$(this).addClass('slct').siblings().removeClass('slct');
        var text = $(this).find('i').text();
        $('.active h1 b').text(text);
        $('.active h1 i').html('返现<span>' + text + '</span>元');
        //$('.active h2').hide();
    });
    /*$('.active ul li.other').click(function(){
	var text=$(this).find('i').text();
	$('.active h2 i').text($(this).find('span').text());
	$('.active h1 b').text(text);
	$('.active h1 i').text('不返现');
})*/
    $('.prompt .but_sub').click(function() {
		var money = $(".slct p i").text();
		if(money==''){
			money = $("#input_money").val();
		}
		if(money<10){
			showLoading('最低充值金额为10元',2000);
		}else if (money<100) {
           //$('.box_show').fadeIn();
            goto_pay(money);
        } else {
            goto_pay(money);
        }
    });
	$('#gotopay').click(function() {
        var money = $("#input_money").val();
        goto_pay(money);
    }); 
	$('#close_win').click(function() {

        $('.box_show').fadeOut();
    });
	$('.prompt span a').click(function() {
        if ($(".explain").is(":hidden")) {
            $('.explain').slideDown();
            $('html,body').animate({
                scrollTop: $('#top').offset().top
            },
            1000);
        } else {
            $('.explain').slideUp();
        }
    })
	function goto_pay(money) {
        showLoading('提交中...');
        setTimeout(jump(ROOT + '/Wap/User/weixinpay?total_fee=' + money), 2000);
    }
    function tabs_cg(Oobj, Otabch, event) { //选项卡切换  1.点击的对象  2.切换的的对象  3.事件
        $(Otabch).hide();
        $(Otabch).first().fadeIn();
        $(Oobj)[event](function() {
            $(this).addClass('acti').siblings().removeClass('acti');
            $(Otabch).hide();
			$(Otabch).eq($(this).index()).show();
        })
    }

    //img_auto('.banner',0.51207729468599)
    function img_auto(obj, valut) { //轮播图调整大小  1.外层Div  2.数值
        $(window).resize(function() {
            abc();
        }) 
		abc();
	function abc() {

            var widt = $('body').width();
            if (widt < 414) $(obj).height(valut * widt + "px");
        }
    }

    function Mobile(main_div, btn_prev, btn_next, main_image, cli_cirl, ClassName) { //手机轮播

        $(main_div).hover(function() {
            $(btn_prev).fadeIn()
        },
        function() {
            $(btn_prev).fadeOut()
        });
        $(main_div).hover(function() {
            $(next).fadeIn()
        },
        function() {
            $(next).fadeOut()
        });

        $dragBln = false;

        $(main_image).touchSlider({
            flexible: true,
            speed: 200,
            btn_prev: $(btn_prev),
            btn_next: $(btn_next),
            paging: $(cli_cirl),
            counter: function(e) {
                $(cli_cirl).removeClass(ClassName).eq(e.current - 1).addClass(ClassName);
            }
        });

        $(main_image).bind("mousedown",
        function() {
            $dragBln = false;
        });

        $(main_image).bind("dragstart",
        function() {
            $dragBln = true;
        });

        $(main_image).find(btn_prev).click(function() {
            if ($dragBln) {
                return false;
            }
        });
        $(main_image).find(btn_next).click(function() {
            if ($dragBln) {
                return false;
            }
        });

        timer = setInterval(function() {
            $(btn_next).click();
        },
        5000);

        $(main_div).hover(function() {
            clearInterval(timer);
        },
        function() {
            timer = setInterval(function() {
                $(btn_next).click();
            },
            5000);
        });

        $(main_image).bind("touchstart",
        function() {
            clearInterval(timer);
        }).bind("touchend",
        function() {
            timer = setInterval(function() {
                $(btn_next).click();
            },
            5000);
        });

    }

    function hide(obj, value) { //隐藏对象  1.要隐藏的对象  2.滚动条的值
        $(window).scroll(function() {
            var et = $(window).scrollTop();
            if (et > value) {
                $(obj).fadeIn();
            } else {
                $(obj).fadeOut();
            }
        })
    }

});