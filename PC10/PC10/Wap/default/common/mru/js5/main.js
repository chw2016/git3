/* 
* @Author: 星辉
* @Date:   2015-02-06 10:23:20
* @Last Modified by:   星辉
* @Last Modified time: 2015-04-30 18:07:57
*/

'use strict';


(function($) {
  	$.fn.slider = function(options){ 
  		var sliderLength = $(this).children().length;
		$(this).each(function(){
        $(this).children().each(function(i,o){
          $(this).attr("EQ",i);
          $(this).attr("id","slider"+i);
        })
        var ThisHtml = $(this).html(),
        boxStyle={
            "overflow":"hidden",
            "position":"relative",
            "width":"100%"
          };
        $(this).css(boxStyle);
        $(this).html('<div class="sliderBoxWrap" style="width:100%;height:100%;">'+ThisHtml+'</div>');
        var o                      = $.extend({},$.fn.slider.setting,options),
            cl                     = $(this).children('.sliderBoxWrap').children().length,
            initP                  = null,
            moveP                  = null,
            mousedown              = false,
            cx                     = null,
            mL                     = 0,
            EQ                     = 0,
            autoSlider             = null,
            sliderWidth            = $(this).width(),                           //初始化滑动块的宽度；
            sliderHeight           = $(this).height(),                          //初始化滑动块的高度；
            sliderSwapWidth        = sliderWidth*cl,                    //初始化滑动块包裹的宽度；
            sliderSwapHeight       = sliderHeight*cl;                   //初始化滑动块包裹的高度;

	  		if(o.direction=="x"){
	  			$(this).children('.sliderBoxWrap').css({
	  				"display": '-webkit-box',	  				
	  			});
          $(this).children('.sliderBoxWrap').css('-webkit-transition-timing-function', 'ease-in-out'); //初始化动画曲线
            if(!$(this).children('.sliderBoxWrap').children().eq(0).hasClass('on')) $(this).children('.sliderBoxWrap').children().eq(0).addClass("on");             //如果第一个slider没有加上on，则会自动帮加上。    

	  			onStart($(this).children('.sliderBoxWrap'),function(e){
            document.body.addEventListener('touchmove', function(e) {                  //阻止冒泡事件
              e.stopPropagation();
            });
//            e.preventDefault();
            e.stopPropagation();
            $(this).css('-webkit-transition', 'all 0s');                    //重置动画时间        
	  				if(e.type == "touchstart"){
	  					initP = window.event.touches[0].pageX;
	  				}else if(e.type == "mousedown"){
              initP = e.pageX;
              mousedown = true;
            };
	  			});

          onMove($(this).children('.sliderBoxWrap'),function(e){
            if (e.type == "touchmove") {
              moveP = window.event.touches[0].pageX;
            }else if(mousedown){
              moveP = e.pageX;
              }
            cx =-(initP-moveP);
            //$(this).css({"transform":"translate3d("+(mL+cx)+"px,0,0"+")"});
            for(var i = 0 ; i<sliderLength; i++){
            var Left =[];
                Left[i] = i*sliderWidth+mL+cx;

            if(EQ == sliderLength-1){
                    Left[0]=sliderWidth+cx;
                  }else if(EQ == 0){
                    Left[sliderLength-1] = (0-sliderWidth)+cx;
                  } 
            $("#slider"+i).css("left",Left[i]);
          }   
            // $(this).css({"margin-left":(mL+cx)});
          });

          onEnd($(this).children('.sliderBoxWrap'),function(e){
           // console.log(EQ);   
            if(cx < (0-80) && (mL-sliderWidth)> (0-sliderSwapWidth)){
             //$(this).css({"transform":"translate3d("+(mL-sliderWidth)+"px,0,0"+")"});
             $(this).css({"margin-left":(mL-sliderWidth)});
              mL -=sliderWidth;                                     //重新获取滑动块位置
              $(this).css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
              $(this).children().siblings().removeClass("on");
              $(this).children().eq(EQ+1).addClass("on");         
            }else if(cx > 80 && mL < 0){
              //$(this).css({"transform":"translate3d("+(mL+sliderWidth)+"px,0,0"+")"});
              $(this).css({"margin-left":(mL+sliderWidth)});
              mL +=sliderWidth;                                     //重新获取滑动块位置
              $(this).css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
              $(this).children().siblings().removeClass("on");
              $(this).children().eq(EQ-1).addClass("on");  
            
            }else {
               // $(this).css({"transform":"translate3d("+(mL)+"px,0,0"+")"});
               $(this).css({"margin-left":(mL)});
               $(this).css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
            };
            
            initP       = null,     //初值控制值
            moveP       = null,     //每次获取到的值
            cx          = null; 
            mousedown   = false;
            EQ          = Number($(this).children(".on").attr("eq"));
            if(EQ == 0){
              $(this).children('.sliderBoxWrap').children().eq(-1)
              .prependTo(".sliderBoxWrap");
              //alert(0)
              }else if( EQ == cl-1){
                
              }
            if(o.speed>0){
	           autoSlider = setInterval(function(){
	            // $(this).children('.sliderBoxWrap').css({"transform":"translate3d(0,"+(mL-sliderHeight)+"px,0"+")"});
	             $(this).children('.sliderBoxWrap').css({"margin-left":(mL-sliderHeight)});
	            mL -=sliderHeight;                                     //重新获取滑动块位置
	            $(this).children('.sliderBoxWrap').css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
	            console.log(mL);
	            $(this).children('.sliderBoxWrap').children().siblings().removeClass("on");
	            $(this).children('.sliderBoxWrap').children().eq(EQ+1).addClass("on");       
	           },o.speed)
	        }            
           
           // EQ =( Math.abs((mL/sliderWidth)));
           // console.log(EQ);    
          });   

	  		}else if(o.direction=="y"){
  	  			 $(this).children('.sliderBoxWrap').css('-webkit-transition-timing-function', 'ease-in-out'); //初始化动画曲线
              if(!$(this).children('.sliderBoxWrap').children().eq(0).hasClass('on')){
              	$(this).children('.sliderBoxWrap').children().eq(0).addClass("on");             //如果第一个slider没有加上on，则会自动帮加上。    
              } 

            onStart($(this).children('.sliderBoxWrap'),function(e){
              document.body.addEventListener('touchmove', function(e) {                  //阻止冒泡事件
                e.stopPropagation();
              });
              e.preventDefault();
              e.stopPropagation();
              $(this).css('-webkit-transition', 'all 0s');                    //重置动画时间        
              if(e.type == "touchstart"){
                initP = window.event.touches[0].pageY;
              }else if(e.type == "mousedown"){
                initP = e.pageY;
                mousedown = true;
              };
            });

            onMove($(this).children('.sliderBoxWrap'),function(e){
              if (e.type == "touchmove") {
                moveP = window.event.touches[0].pageY;
              }else if(mousedown){
                moveP = e.pageY;
                }
              cx =-(initP-moveP);
              //$(this).css({"transform":"translate3d(0,"+(mL+cx)+"px,0"+")"});
               $(this).css({"margin-left":(mL+cx)});
            });

            onEnd($(this).children('.sliderBoxWrap'),function(e){
             console.log(EQ);   
              if(cx < (0-50) && (mL-sliderHeight)> (0-sliderSwapHeight)){
               // $(this).css({"transform":"translate3d(0,"+(mL-sliderHeight)+"px,0"+")"});
               $(this).css({"margin-left":(mL-sliderHeight)});
                mL -=sliderHeight;                                     //重新获取滑动块位置
                $(this).css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
                $(this).children().siblings().removeClass("on");
                $(this).children().eq(EQ+1).addClass("on");         
              }else if(cx > 50 && mL < 0){
               // $(this).css({"transform":"translate3d(0,"+(mL+sliderHeight)+"px,0"+")"});
               $(this).css({"margin-left":(mL+sliderHeight)});
                mL +=sliderHeight;                                     //重新获取滑动块位置
                $(this).css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
                $(this).children().siblings().removeClass("on");
                $(this).children().eq(EQ-1).addClass("on");  
              
              }else {
                  //$(this).css({"transform":"translate3d(0,"+(mL)+"px,0"+")"});
                  $(this).css({"margin-left":(mL)});
                  $(this).css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
              };
              
              initP       = null,     //初值控制值
              moveP       = null,     //每次获取到的值
              cx          = null; 
              mousedown   = false;
              EQ          = Number($(this).children(".on").attr("eq"));
              if(EQ == 0){
                $(this).children('.sliderBoxWrap').children().eq(-1)
                .prependTo(".sliderBoxWrap");
                //alert(0)
                }else if( EQ == cl-1){
                  
                }
             // EQ =( Math.abs((mL/sliderWidth)));
             // console.log(EQ); 
          });
        };	
        if(o.speed>0){
           autoSlider = setInterval(function(){
            // $(this).children('.sliderBoxWrap').css({"transform":"translate3d(0,"+(mL-sliderHeight)+"px,0"+")"});
             $(this).children('.sliderBoxWrap').css({"margin-top":(mL-sliderHeight)});
            mL -=sliderHeight;                                     //重新获取滑动块位置
            $(this).children('.sliderBoxWrap').css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
            console.log(mL);
            $(this).children('.sliderBoxWrap').children().siblings().removeClass("on");
            $(this).children('.sliderBoxWrap').children().eq(EQ+1).addClass("on");       
           },o.speed)
        }
	  	});
	  	return this;
  	}

  	var onStart  = function(THIS,handler){
    			handler = handler;
    			try{
    				if(!handler) throw("onStart的处理程序未引用！");
    			}
    			catch(err){
    				console.error(err);
    			}
    			THIS.on("touchstart mousedown",handler);
    		},
  	   	onMove	 = function(THIS,handler){
    			handler = handler;
    			try{
    				if(!handler) throw("onMove的处理程序未引用！");
    			}
    			catch(err){
    				console.error(err);
    			}
    			THIS.on("touchmove mousemove",handler);
    		},
  	   	onEnd	 = function(THIS,handler){
    			handler = handler;
    			try{
    				if(!handler) throw("onEnd的处理程序未引用！");
    			}
    			catch(err){
    				console.error(err);
    			}
    			THIS.on("touchend mouseup ",handler);
    		},
  	   	offEvent = function(THIS){
  			 THIS.off("touchstart touchmove touchend mousedown mousemove mouseup mouseout");
  		  };

  	$.fn.slider.setting = {
	  	direction 	: "x",			//默认为左右的，可定义为上下的：y
	  	loop 	    	: true,			//默认可循环
	  	speed		    : 0,			  //0为不自动播放
	  	lightApp	  : false, 		//滑动类型，默认为轮播图，true为场景滑动
  	}
  	
  
})(jQuery); 