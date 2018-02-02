$(function(){
	var r = $("body").attr("r");
	var g = $("body").attr("g");
	var b = $("body").attr("b");
	//$(".title").css({"background-color":"rgba("+r+","+g+","+b+",1)","color":"rgba("+r+","+g+","+b+",1)"});
	//$(".title.shop:before").css({"border-bottom-color":"rgba("+r+","+g+","+b+",1)"})
	if($(".tap").hasClass("tapon")){
		//$(".tapon").children().eq(0).children().eq(0).css({"border":"1px solid rgba("+r+","+g+","+b+",1)","background-color":"rgba(194,1,22,0)"})
		}
	$(".tap").on("touchstart mouseenter",function(){
		var THIS = $(this);
		if(THIS.data("tap") =="indexItem" ){
			THIS.children().eq(0).children().eq(0).css({"border":"1px solid rgba(194,1,22,0.3)","background-color":"rgba(194,1,22,0)"})
			}else 
	    if(THIS.data("tap") =="item" ){
			
			THIS.children().eq(0).children().eq(0).css({"border":"1px solid rgba("+r+","+g+","+b+",1)","background-color":"rgba(194,1,22,0)"})
			}
		});
	$(".tap").on("touchend mouseleave",function(){
		var THIS = $(this);
		if(THIS.data("tap") =="indexItem" ){
			THIS.children().eq(0).children().eq(0).css({"border":"1px solid rgba(194,1,22,0)","background-color":"rgba(194,1,22,0.3)"})
			}else
		if(THIS.data("tap") =="item" ){
			if(THIS.hasClass("tapon")){
		$(".tapon").children().eq(0).children().eq(0).css({"border":"1px solid rgba("+r+","+g+","+b+",1)","background-color":"rgba(194,1,22,0)"})
		}else{
			THIS.children().eq(0).children().eq(0).css({"border":"1px solid rgba(255,143,0,0)","background-color":"rgba(255,143,0,0.3)"})
			}}
		});
	})


function sliderY(sliderBox,sliderSwrap,slider){//滑动切换Alpha版
		      /*var sliderSwrap        = $(".sliderSwrap"),                           //滑动块的包裹；
				slider                 = $(".slider"),                                //滑动块的位置；*/
				var sliderLength       = slider.length,                               //获取滑动块的个数；
				sliderWidth            = sliderBox.width(),                           //初始化滑动块的宽度；
				sliderHeight           = sliderBox.height(),                          //初始化滑动块的高度；
				sliderSwapWidth        = sliderWidth*sliderLength,                    //初始化滑动块包裹的宽度；
				sliderSwapHeight       = sliderHeight*sliderLength,                   //初始化滑动块包裹的高度；
				startTime              = null,                                        //开始触摸的时间戳
				moveTime               = null,                                        //移动的时间戳
				endTime                = null,                                        //结束触摸的时间戳
				mousedown          	   = null,                                        //初始化鼠标按下
				mL                     = 0,	                                          //初始化滑动块位置
				mT                     = 0,                                           //初始化滑动块竖向位置
				initP                  = null,
				moveP                  = null,
				cx                     = null,
				EQ                     = 0,                                           //初始化个数
				stopIphone             =null,
				SliderTip              = $(".SliderTip");
				
				sliderSwrap.css('-webkit-transition-timing-function', 'ease-in-out'); //初始化动画曲线
			if(!slider.eq(0).hasClass('on')) slider.eq(0).addClass("on");SliderTip.eq(0).addClass("on");             //如果第一个slider没有加上on，则会自动帮加上。    
			 
			function OpenAction(e){//开启滑动功能
				sliderSwrap.on('mousedown touchstart',OnStart);
				sliderSwrap.on('mousemove touchmove',OnMove);
				sliderSwrap.on('mouseup touchend mouseout ',OnEnd);
				return true;
			}
			function CloseAction(e){//关闭滑动功能
				sliderSwrap.off('mousedown touchstart');
				sliderSwrap.off('mousemove touchmove');
				sliderSwrap.off('mouseup touchend mouseout ');
				return false;
			}
			OpenAction();
				
			function OnStart(e){
			  document.body.addEventListener('touchmove', function(e) {                  //阻止冒泡事件
                e.stopPropagation();
				});
				e.preventDefault();
				e.stopPropagation();
				//$(".testText").html(window.event);
				//console.log(window.event.touches[0]);
				startTime = e.timeStamp;
				if (e.type == "touchstart") {
					initP = window.event.touches[0].pageY;
					 sliderSwrap.css('-webkit-transition', 'all 0s');                    //重置动画时间				
				}else if(e.type == "mousedown"){
					initP = e.pageY;
					mousedown = true;
					}
			}
			function OnMove(e){
				//e.preventDefault();
				//e.stopPropagation();
				if (e.type == "touchmove") {
						moveP = window.event.touches[0].pageY;
					}else if(mousedown){
						moveP = e.pageY;
						}
					cx =-(initP-moveP);
					//console.log(cx);
					//$(".testText").html(sliderWidth);
					//sliderSwrap.css({"margin-left":(mL+cx)+"px"});
					sliderSwrap.css({"transform":"translate3d(0,"+(mT+cx)+"px,0"+")"});
		
				
			}
			function OnEnd(e){
				CloseAction();
				endTime = e.timeStamp;
				//$(".testText").html(sliderWidth);
				
				
				if(cx<(0-80) && (mT-sliderHeight)> (0-sliderSwapHeight)){
						//sliderSwrap.animate({"margin-left":(mL-sliderWidth)+"px"},200);
						sliderSwrap.css({"transform":"translate3d(0,"+(mT-sliderHeight)+"px,0"+")"});
						slider.siblings().removeClass("on");
						slider.eq(EQ+1).addClass("on");
						SliderTip.siblings().removeClass("on");
						SliderTip.eq(EQ+1).addClass("on");
						
				mT -=sliderHeight;                                     //重新获取滑动块位置
				sliderSwrap.css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
				success()
				OpenAction();	
				}else if(cx>80 && mT < 0){
					   // sliderSwrap.animate({"margin-left":(mL+sliderWidth)+"px"},200);
						sliderSwrap.css({"transform":"translate3d(0,"+(mT+sliderHeight)+"px,0"+")"});
						slider.siblings().removeClass("on");
						slider.eq(EQ-1).addClass("on");
						SliderTip.siblings().removeClass("on");
						SliderTip.eq(EQ-1).addClass("on");
				mT +=sliderHeight;                                     //重新获取滑动块位置
				sliderSwrap.css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
				success()
				OpenAction();		
				}else {
					  // sliderSwrap.animate({"margin-left":(mL)+"px"},200);
					  sliderSwrap.css({"transform":"translate3d(0,"+(mT)+"px,0"+")"});
					  sliderSwrap.css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
					 success()
					   OpenAction();
				};		
				
				/* 初始化值 */
				EQ =( Math.abs((mL/sliderHeight)));
				//console.log(EQ)
				if(EQ==0){
					$(".left").hide();
					}else if( EQ==sliderLength-1){
						$(".right").hide();
						}else{
							$('.Arrow').show();
							}
				initP		= null,			//初值控制值
				moveP		= null,			//每次获取到的值
				cx          = null;	
				mousedown   = false;
				stopIphone = false;	
			}
		
}

function hav(location,display){
		if(display=="show"){
			location.show(0);
			}else{
		location.hide(0);
			}
	}







/**************************************************************************/
 $(function(){
    $(".s.f").each(function(i,o){
      if($.trim($(o).text()).length>=3){
        $(this).css("font-size","16px");
      };
      if($.trim($(o).text()).length<3){
          $(this).css({"font-size":"16px", "line-height":"46px"});
      };
      if($.trim($(o).text()).length==1){
      	$(this).css("font-size","20px")
      }
    })
  })


$(function(){
	$(".Home").hide(0);
	
	var aC = null;
	var bodyY =null;
	var bodyYC = null;
	var at =null;
	var bt =null;
	$("body").on("touchstart",function(){
		bodyY = window.event.touches[0].pageY;
		//console.log(bodyY);
		
	})
	$("body").on("touchmove",function(){
		bodyYC = window.event.touches[0].pageY;
		aC =bodyYC - bodyY;
			
		})
	$("body").on("touchend",function(){
		//alert(aC);
				if(aC>0){
					//$(".Home").show(0);
					$(".hav .item").each(function(i,o){
						
						if($(o).data("tap")=="item"){
							//$(o).removeClass('opacity');
						}
					})
					clearTimeout(at);
					clearTimeout(bt);
					at = setTimeout(function(){
						//$(".Home").fadeOut(500);
					},3000);
					bt = setTimeout(function(){
					$(".hav .item").each(function(i,o){
						
						if($(o).data("tap")=="item"){
							//$(o).addClass('opacity');
						}
					})
					},3000);
				}else{
					clearTimeout(bt);
					clearTimeout(at);
					$(".hav .item").each(function(i,o){
						
						if($(o).data("tap")=="item"){
							//$(o).addClass('opacity');
						}
					});										
					//$(".Home").hide(0);
				}
			})
})


$(function(){
	//console.log()
    var bEditTitle = $('#bEditTitle').val();
	if($(".hav .item").length >0){
        if(bEditTitle != 1){
            $("title").text($.trim($(".hav .item").eq(0).text()))
        }
	}
	if($(".havbox").hasClass('wenjuan')){
		$(".havbox").removeClass('wenjuan')
	}
		
	$(".hav .item").on('click', function(event) {
		event.preventDefault();
		$("title").text($.trim($(this).text()))
	});
	if($(".hav .item").length ==2){
		$(".hav .item").each(function(i,o){		
			// $(this).css("margin-left","18%");
			// console.log(i+","+$(o).css("margin-left"))		
	});	
	}	
	$(".iconBox").html("")	
})
