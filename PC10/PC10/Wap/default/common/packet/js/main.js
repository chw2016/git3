	var bgH = $(window).height();
	var bgW = $(window).width();
/*	alert("高："+bgH +",宽："+bgW);*/
/*$(function(){
	$("body").css({"height":bgH,"width":bgW,"background-size":bgW +"px "+bgH+"px"});
	$(".text").css({"top":(( 82 /568)*bgH),"width":((200/320)*bgW),"height":((80/568)*bgH)});
    $(".textok").css({"width":((200/320)*bgW),"height":((80/568)*bgH)});
	$(".mid").css({"top":(( 82 /568)*bgH),"width":((200/320)*bgW),"height":((244/568)*bgH)});
	$(".texttap").css({"top":(( 82 /568)*bgH),"width":((200/320)*bgW),"height":((80/568)*bgH)});
	$(".finger,.logo,.btn").css({"height":((180/568)*bgH)});
	$(".btn").css({"height":((55/568)*bgH)});
	$(".btntext").css({"height":((55/568)*bgH)+"px"});
	$(".box").css({"top":(( 8 /568)*bgH),"width":((70/320)*bgW),"height":((40/568)*bgH)});
	$(".rpbox").css({"left":((42/320)*bgW),"height":((150/568)*bgH)});
	$(".moneyclose").css({"top":(( 90 /568)*bgH),"color":"#999"});
	//$(".gift").css({"top":((35/568)*bgH)});
	//$(".pls").css({"top":((70/568)*bgH)});
	//$(".inputnum").css({"top":((110/568)*bgH)});
	//$(".rpbtn").css({"height":((20/568)*bgH),"width":((176/320)*bgW)});
	$(".over").css({"top":(-(18/568)*bgH)});
	});*/

$(function(){
	$(".tap").bind("touchstart click",function(){
		var THIS =$(this);
		THIS.css({"color":"#999"});
		THIS.bind("touchend click" ,function(){
			THIS.css({"color":"#fff"});
			if(THIS.data('tap')=='money'){
			$(".money").fadeIn(50);
		}
			else if(THIS.data('tap')=='rull'){
			$(".rull").fadeIn(50);}
			else if(THIS.data('tap')=='rank'){
			$(".rank").fadeIn(50);}
			else if(THIS.data('tap')=='share'){
			$(".share").fadeIn(50);}
            else if(THIS.data('tap')=='goda'){
                $(".checkpublic").fadeIn(50);
            }
			else if(THIS.data('tap')=='index'){
			window.location.href='/0/rp';
			}
			/*else if(THIS.data('tap')=='help'){
                THIS.fadeOut(50);
			$('.bottom').fadeIn(50);
                //$(".texttap").css("top","0px");
			}*/
			else if(THIS.data('tap')=='myrp'){
			$('.myrp').fadeIn(50);
			return false;
			}
			else if(THIS.data('tap')=='snrull'){
			$('.snrull').fadeIn(50);
			THIS.css({"color":"#f00"});
			};
			});
		});
	$(".close").bind("touchstart click",function(){
		var THIS =$(this);
		THIS.css({"color":"#f00"});
		THIS.bind("touchend click",function(){
			if(THIS.hasClass("moneyclose")){
				THIS.css({"color":"#f00"});}
				else{
					THIS.css({"color":"#f00"});}
			$(".showbox").fadeOut(50);
			});
		});
	$(".closesn").bind("touchstart click",function(){
		var THIS =$(this);
		THIS.css({"color":"#f00"});
		THIS.bind("touchend click",function(){
			
				THIS.css({"color":"#f00"});
				
			$(".snrull").fadeOut(50);
			});
		});
	$(".btn").bind("touchstart",function(){
		var THIS =$(this);
		THIS.find(".box").attr("id","tapped");
		THIS.bind("touchend",function(){
			THIS.find(".box").removeAttr("id");
			});
		});
	$(".getit").bind("touchstart",function(){
		var THIS =$(this);
		if($(".state").data("state")=="0"||$("input").data("state")=="0"){
		THIS.css({"background-color":"#cb1c1c"});
		THIS.bind("touchend",function(){
			THIS.css({"background-color":"#B71114","color":"#Fff"});
			
			});}
		});
	$(".share,.checkpublic").bind("touchstart",function(){
		var THIS =$(this);
		THIS.bind("touchend",function(){
			$(".showbox").fadeOut(50);
			});
		});

	})

$(function(){
	$("input").focus(function(){
		$(".close").fadeOut(50);
		});
	$("input").blur(function(){
		$(".close").fadeIn(50);
		});
	})


