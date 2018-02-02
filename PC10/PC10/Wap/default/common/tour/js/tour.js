/*
* @Author: zhang
* @Date:   2015-04-14 08:41:09
* @Last Modified by:   zhang
* @Last Modified time: 2015-04-21 17:18:54
*/

'use strict';
$(function(){

	// 支付功能
	$('.alipay').on('click',function(){
		var choiceDate = $('#selectedDates').text();
	})

	// 下拉框成人
	$('#man>.left').on('click',function(event){
		event.stopPropagation();
		event.preventDefault();
		$(this).find('.showDDL').toggle();

	})
    //这里是联系人个数
    $("#man>.left").find('.showDDL').find('div').on('click',function(){

        $('#adult').html($(this).text()+"<div class='tangle'></div>");
        var num=$(this).text();

          $(".copy").not(":first").remove();
            var k=num-1;
            var i=0;
             for(i;i<k;i++){
                $(".bbc").append($(".copy:first").clone());
             }
    })


	// 下拉框儿童
	$('#man>.right').on('click',function(event){
		event.stopPropagation();
		event.preventDefault();
		$(this).find('.showDDL').toggle();
		$(this).find('.showDDL').find('div').on('click',function(){
			$('#child').html($(this).text()+"<div class='tangle'></div>");
		})
	})

	$('.bbc').on('click','.tourist1',function(event){
		event.stopPropagation();
		event.preventDefault();
        var a=$(this);
		$(this).find('.showDDL').toggle();
		$(this).find('.showDDL').find('div').on('click',function(){
     //       alert($(this).text());
            a.find(".selectedDate").html($(this).text()+"<div class='tangle'></div>");
		})
	})

	$('.tourist2').on('click',function(event){
		event.stopPropagation();
		event.preventDefault();
		$(this).find('.showDDL').toggle();
		$(this).find('.showDDL').find('div').on('click',function(){
			$('#card2').html($(this).text()+"<div class='tangle'></div>");
		})
	})

	$(document).click(function () {
        $('#man>.right,#man>.left,.tourist1,.tourist2').find('.showDDL').hide();
	})

	$('#man>.left').find('.showDDL').find('div').on('click',function(event){
		event.stopPropagation();
		event.preventDefault();
        $('#adult').html($(this).text()+"<div class='tangle'></div>");
        $(this).parent().hide();
	})

	$('#man>.right').find('.showDDL').find('div').on('click',function(event){
		event.stopPropagation();
		event.preventDefault();
        $('#child').html($(this).text()+"<div class='tangle'></div>");
        $(this).parent().hide();
	})

	$('.tourist1').find('.showDDL').find('div').on('click',function(event){
		event.stopPropagation();
		event.preventDefault();
        $('#card1').html($(this).text()+"<div class='tangle'></div>");
        $(this).parent().hide();
	})

	$('.tourist2').find('.showDDL').find('div').on('click',function(event){
		event.stopPropagation();
		event.preventDefault();
        $('#card2').html($(this).text()+"<div class='tangle'></div>");
        $(this).parent().hide();
	})
	// 上述只是处理下拉框，下拉框问题结束
	// 出境旅游特价
	$('.wrap-lan').on('touchstart',function(event){
		event.stopPropagation();
		event.preventDefault();
		if($('#showLeftLan').hasClass('flag')){
			$('#showLeftLan').animate({'left': '-60%'}, 600).removeClass('flag');
		    $('#rightBody').animate({'left': '0'}, 600).removeClass('flag');
		}else{
			$('#showLeftLan').animate({'left': '0'}, 600).addClass('flag');
		    $('#rightBody').animate({'left': '60%'}, 600).addClass('flag');
		}

	})
	$('#rightBody').on('touchstart',function(){
		if($('#showLeftLan').hasClass('flag')){
			$('#showLeftLan').animate({'left': '-60%'}, 600).removeClass('flag');
		    $('#rightBody').animate({'left': '0'}, 600).removeClass('flag');
		}
	})

	// 点击旅游左侧栏目
	$('.zoneWrap>.zone').on('click',function(){
		if($(this).find('.right').find('div').hasClass('symbol')){
			$(this).next().slideUp();
			$(this).find('.right').find('div').addClass('symbolAdd').removeClass('symbol');
		}else if($(this).find('.right').find('div').hasClass('symbolAdd')){
			$(this).next().slideDown();
			$(this).find('.right').find('div').addClass('symbol').removeClass('symbolAdd');
		}
		$(this).parent().siblings().find('.zone').find('.right').find('div').addClass('symbolAdd').removeClass('symbol');
		$(this).parent().siblings().find('.list').slideUp();
	})

	// 出境特价游点击不同的栏目
	$('.zoneLinks>ol>li').on('click',function(){
		$('.loading').text("");
		$(this).addClass('gray-color').siblings().removeClass('gray-color');
		$('.zoneLinks>ul').eq($(this).index()).show().siblings('ul').hide();
		$(this).parent().siblings('ul').find('li').find('div').removeClass('choice');
		$(this).parent().siblings('ul').find('li:first').find('div').addClass('choice');
	})

	// 点击不同
	$('.zoneLinks>ul>li').on('click',function(){
		$(this).find('div').addClass('choice').parent().siblings().find('div').removeClass('choice');
	})

	// 控制左侧栏的大小
	window.onload = function(){
	$('.zoneLinks>ol>li:first').trigger('click');
		if($(document).height() <= $(window).height()){
            $('#showLeftLan').css({'min-height':$(window).height()+'px'});
		}else{
	        $('#showLeftLan').css({'min-height':$(document).height()+'px'});
		}
	}

	// 地址切换页面选择提交更改地区
    $('.zoneLinks > ol > li').click(function(){
    	var id = $(this).data('id'),
    	    childId = $(this).parent().siblings('ul').eq($(this).index()).find('li:first').find('div').data('id');
    	    $('.wraps').empty();
    	    $('.wraps').html("<div style='color:#818181;'>加载中...</div>");
    	$.post(url,{pid:id,cid:childId},function(data){
    		if(data.status == 1){
    			$('.wraps').html("<div style='color:#818181;'>"+data.info+"</div>");
    		}else{
    			$('.wraps').empty();
    			$('.wraps').append($(data.info)) ;
    		}
    	},'json');
    })

    // 点击国家切换
    $('.imgload > li').click(function(){
    	$('.loading').text("");
    	var cid = $(this).find('div').data('id');
    	$('.wraps').empty();
    	$('.wraps').html("<div style='color:#818181;'>加载中...</div>");
    	$.post(url,{cid:cid},function(data){
    		if(data.status == 1){
    			$('.wraps').html("<div style='color:#818181;'>"+data.info+"</div>");
    		}else{
    			$('.wraps').empty();
    			$('.wraps').append($(data.info)) ;
    		}
    	},'json');
    	$(window).scroll(function(){
    		$('.loading').text("加载中...");
	        var diff = Number($(window).height()) + Number(20);
	        if($(document).height() - $(window).scrollTop() < diff){
	        	var asize = $('.wraps > a').size();
	        	setTimeout(function(){
	        		$.post(urlload,{asize:asize,cid:cid},function(data){
	        			if(data.status == 1){
	        				$('.loading').text("已显示全部");
	        				$(window).off('scroll');
	        			}else{
	        				$('.wraps > a:last').after($(data.info));
	        				$(window).on('scroll');
	        			}
	        		},'json')
	        	},500);
	        }
    	})
    })


    $('.list > li').click(function(){
    	$('.loading').text("");
    	var cid = $(this).data('id');
    	$('.wraps').empty();
    	$('.wraps').html("<div style='color:#818181;'>加载中...</div>");
    	$.post(url,{cid:cid},function(data){
    		if(data.status == 1){
    			$('.wraps').html("<div style='color:#818181;'>"+data.info+"</div>");
    		}else{
    			$('.wraps').empty();
    			$('.wraps').append($(data.info)) ;
    		}
    	},'json');

    	// 先找到当前li的父亲ul里面的大洲
    	var guojia = $(this).text()
    	    ,dazhou = $(this).parent().prev().find('.left').text();

    	$('.zoneLinks > ol').find('li').each(function(){
    		if($(this).text() == dazhou){
    			$(this).addClass('gray-color').siblings().removeClass('gray-color');
    		}
    	})

    	$('.imgload > li').each(function(){
    		if($(this).find('div').text() == guojia){
    			$(this).parent().show();
    			$(this).parent().siblings('.imgload').hide();
    			$(this).find('div').addClass('choice').parent().siblings().find('div').removeClass('choice');
    		}
    	})
    	$('#showLeftLan').animate({'left': '-60%'}, 600).removeClass('flag');
		$('#rightBody').animate({'left': '0'}, 600).removeClass('flag');
		$(window).scroll(function(){
    		$('.loading').text("加载中...");
	        var diff = Number($(window).height()) + Number(20);
	        if($(document).height() - $(window).scrollTop() < diff){
	        	var asize = $('.wraps > a').size();
	        	setTimeout(function(){
	        		//$(window).off('scroll');
	        		$.post(urlload,{asize:asize,cid:cid},function(data){
	        			if(data.status == 1){
	        				$('.loading').text(data.info);
	        				$(window).off('scroll');
	        			}else{
	        				$('.wraps > a:last').after($(data.info));
	        				$(window).on('scroll');
	        			}
	        		},'json')
	        	},500);
	        }
    	})
    })

   //立即支付
   $('.alipay').click(function(){
       var adult = $.trim($('#adult').text());
   	   if(adult == "请选择成人人数"){
   	   	    msg.alert("请选择成人人数");
   	   	    return false;
   	   }
       var iChild = parseInt($.trim($('#child').text()));
       iChild = isNaN(iChild) ? 0 : iChild;

       var $Money = $("input[name='order_money']");
       var $HMoney = $("input[name='order_money_hidden']");
       var sMoney = $HMoney.val();
       var aMoney = sMoney.split('|');
       var iChildMoney = 0;
       var money = adultMoney= 0;
       if (typeof(aMoney[1]) != 'undefined') {
            iChildMoney = parseInt(aMoney[1]) * iChild;
       };
       var adultMoney = aMoney[0] * adult;
       money = parseInt(adultMoney) + parseInt(iChildMoney);
       money = typeof(money) == 'undefined' ? 0 : money;
       $Money.val(money);

       var tour=[];
       $("input.tour:visible").each(function(e,t){
           if(!$(t).val()){
               msg.alert("请把姓名填写完全");
               bErr = true;
               return false;
           }else{
               tour[e]=$(t).val();
           }
       });
       if (bErr) { return false; };
       tour=tour.join(',');

       var phone=[];
       $("input.phone:visible").each(function(e,t){
           if(!$(t).val()){
               msg.alert("请把手机号码填写完全");
               bErr = true;
               return false;
           }else{
               phone[e]=$(t).val();
           }
       });
       if (bErr) { return false; };
       phone=phone.join(',');



       var car_name=[];
       var bErr    = false;
       $(".car_name").each(function(e,t){
           var sText = $.trim($(this).text());
           if(!$(t).text() || sText == '请选择游客证件'){
               msg.alert("请选择游客证件");
               bErr = true;
               return false;
           }else{
               car_name[e]=$(t).text();
           }
       });
       if (bErr) { return false; };
       car_name=car_name.join(',');


       var cardno=[];
       $("input.cardno:visible").each(function(e,t){
           if(!$(t).val()){
               msg.alert("请把手机号码填写完全");
               bErr = true;
               return false;
           }else{
               cardno[e]=$(t).val();
           }
       });
       if (bErr) { return false; };
       cardno=cardno.join(',');
      // alert(car_name);return false;
   	   var productid = $(this).data('id'),
   	       selectedDates = $.trim($('#selectedDates').text()),
   	       child = $.trim($('#child').text());
   	     //  tour1 = $.trim($('#tour1').val()),
   	     //  card1 = $.trim($('#card1').text()),
   	     //  cardno1 = $.trim($('#cardno1').val()),
   	    //   tour2 = $.trim($('#tour2').val()),
   	     //  card2 = $.trim($('#card2').text()),
   	    //   cardno2 = $.trim($('#cardno2').val()),
          //  phone = $.trim($('#phone').val());

   	   if(selectedDates == "请选择出游日期"){
   	   	    msg.alert("请选择出游日期");
   	   	    return false;
   	   }
       if(phone == "请填写手机号码"){
           msg.alert("请填写手机号码");
           return false;
       }


   	   var data = {
   	   	        productid:productid,
   	   	        selectedDates:selectedDates,
   	   	        adult:adult,
   	   	        child:child,
           tour:tour,//姓名
           cardno:cardno,//证件编号
           phone:phone,//手机号码
           car_name:car_name//证件类型
   	   	    };
   	   $.post(urlpay,data,function(data){
   	   	    if(data.status == 0){
   	   	    	msg.alert(data.info,1000);
                $("#orderid").val(data.orderid);
                $("#myweipayform").submit();
   	   	    	/*setTimeout(function(){
   	   	    		window.location.reload();
   	   	    	},1200)*/
   	   	    }else{
   	   	    	msg.alert(data.info,1000);
   	   	    }
   	   },'json');
   })

    // 取消订单
    $('.cancelOrder').click(function(){
    	var id = $(this).data('id'),
    	    data = {id:id};
    	msg.confirm('确定取消该订单吗？',function(){
    		$.post(urlpay,data,function(data){
    			if(data.status == 0){
    				msg.alert(data.info,1000);
            $("#orderid").val(data.orderid);
    				/*setTimeout(function(){
   	   	    			window.location.reload();
   	   	    		},1200)
            */
            $("#myweipayform").submit();
    			}else{
    				msg.alert(data.info,1000);
    			}
    		},'json');
    	})
    })

    // 返回
    $('.wrap-lan > .arrow').click(function(){
    	window.history.go(-1);
    })

    // 点击价格
    $('.trvalPrice').click(function(){
    	$('.jiage').toggle();
    })

    // 多客服在线咨询
     $(".register > .submit > .left").click(function(){
        var url = "{weikucms::U('Wap/Duokf/sendkfmsg',array('token'=>$token,'openid'=>$openid,'dopenid'=>$_GET['dopenid']))}";
        $.post(url,{},function(data){
            if(data.code == 0){
                $(".motify-inner").text(data.msg);
                $(".motify").show();
                setTimeout(function(){
                    WeixinJSBridge.invoke('closeWindow',{},function(res){
                    });
                },'2000');
            }else{
                $(".motify-inner").text(data.msg);
                $(".motify").show();
                setTimeout(function(){
                    $(".motify").hide();
                },'2000');
                return false;
            }
        },'json');
    });
})
