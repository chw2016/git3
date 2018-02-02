/* 
* @Author: Samphay
* @Date:   2014-11-28 09:57:33
* @Last Modified by:   星辉
* @Last Modified time: 2015-03-27 15:55:39
* @Verson: Alpha 1.0
*/
function slider(sliderBox,sliderSwrap,slider,speed){								  //滑动切换Alpha版
		      /*var sliderSwrap        = $(".sliderSwrap"),                           //滑动块的包裹；
				slider                 = $(".slider"),                                //滑动块的位置；*/
				var sliderLength       = slider.length,                               //获取滑动块的个数；
				sliderWidth            = sliderBox.width(),                           //初始化滑动块的宽度；
				sliderHeight           = sliderBox.height(),                          //初始化滑动块的高度；
				sliderSwapWidth        = sliderWidth*(sliderLength-1),                    //初始化滑动块包裹的宽度；
				sliderSwapHeight       = sliderHeight*(sliderLength-1),                   //初始化滑动块包裹的高度；
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
				stopIphone             = null,
				SliderTip              = $(".SliderTip"),
				countHtML			   = "<div class='countSlider'></div>";
				// THIS 				   = $(this);
				// countHtMLStyle         = 'background-color:red;position:absolute;bottom:0;width:100%;'
				sliderBox.append(countHtML);
				slider.each(function(i,o){
		          $(this).attr("EQ",i);
		        })
				$(".countSlider").css({
					
					"position": 'relative',
					"bottom": '16px',
					"margin": '0 auto',
					"width" : 12*sliderLength,
					"z-index" : "2"
					

				}).addClass('myul');
				for(var a = 0; a < sliderLength; a++){
					htmlC = "<div class='CC' id=C";
					htmlC+= a;
					htmlC+="></div>";
					$(".countSlider").append(htmlC)
				};
				$(".CC").css({
					"width": "6px",
				    "height": "6px",
				    "background-color": "rgba(255,255,255,.8)",
				    "border-radius": "50%",
				   " -webkit-border-radius": "50%",
				   "margin-left": "3px",
				   "margin-right": "3px"
				})
				$("#C"+0).css('background-color', '#999');
				//alert(countHtML);
				sliderBox.css("position","relative");
				slider.css("position","absolute");
				slider.each(function(i,o){
					$(this).css("left",i*sliderWidth);
					$(this).attr("id","slider"+i)
				})
				slider.css('-webkit-transition-timing-function', 'ease-in-out'); //初始化动画曲线
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
				EQ = Number($(this).children(".on").attr("eq"));
				 // console.log(EQ);	
				// clearTimeout(autoSlider);
				startTime = e.timeStamp;
				if (e.type == "touchstart") {
					initP = window.event.touches[0].pageX;
					 slider.css('-webkit-transition', 'all 0s');                    //重置动画时间				
				}else if(e.type == "mousedown"){
					initP = e.pageX;
					mousedown = true;
					}
			}
			function OnMove(e){
				//e.preventDefault();
				//e.stopPropagation();
				
				if (e.type == "touchmove") {
						moveP = window.event.touches[0].pageX;
						mT    = window.event.touches[0].pageY;
					}else if(mousedown){
						moveP = e.pageX;
						mT 	  = e.pageY;
						}
					cx =-(initP-moveP);
					/*console.log(mT);
					if(mT !== 0){
						CloseAction();
					}else{
						OpenAction();
					}*/
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
				
			}
			function OnEnd(e){
				
				CloseAction();
				// endTime = e.timeStamp;				
				
				if(cx<(0-80)){	
											
						for(var i = 0 ; i<sliderLength; i++){
							var Left =[];
							  	Left[i] = i*sliderWidth+mL-sliderWidth;	
							  	if(EQ == sliderLength-1){
							  		Left[0]=0;
							  	}
							  		
							$("#slider"+i).css("left",Left[i]>sliderSwapWidth?sliderWidth:Left[i]);
							
						};
						mL-=sliderWidth;
						if(mL<(0-sliderSwapWidth)){
							mL = 0;
						}
						if((EQ+1)>(sliderLength-1)){
							EQ = -1;
						};	
						//console.log(EQ+1)
						slider.removeClass("on");
						slider.eq(EQ+1).addClass("on");
						SliderTip.siblings().removeClass("on");
						SliderTip.eq(EQ+1).addClass("on");	
											
						// mL -=sliderWidth;                                    				  //重新获取滑动块位置
						slider.css('-webkit-transition', 'all .4s');                    //初始化动画时间
						
						OpenAction();	
				}else if(cx>80){
					   
						for(var i = 0 ; i<sliderLength; i++){
							var Left =[];
							  	Left[i] = i*sliderWidth+sliderWidth+mL
							 	if(EQ == 0 ){
							  		Left[sliderLength-1]=0;
							  	}								
							$("#slider"+i).css("left",Left[i])
						}
						mL+=sliderWidth;
						if(mL>0){
							mL = 0-sliderSwapWidth;
						}
						if((EQ-1)<0){
							EQ = sliderLength;
						}
						//console.log("←"+(EQ-1));
						slider.siblings().removeClass("on");
						slider.eq(EQ-1).addClass("on");
						SliderTip.siblings().removeClass("on");
						SliderTip.eq(EQ-1).addClass("on");
						// mL +=sliderWidth;                                     //重新获取滑动块位置
						slider.css('-webkit-transition', 'all .4s');                    //初始化动画时间
						success();
						
						OpenAction();		
				}else {
					  
					  	for(var i = 0 ; i<sliderLength; i++){
					  		var Left = [];
					  		Left[i] = i*sliderWidth+mL
					  		if(EQ == sliderLength-1){
							  		Left[0]=sliderWidth;
							  	}else if(EQ == 0){
							  		Left[sliderLength-1] = (0-sliderWidth)
							  	}						
							$("#slider"+i).css("left",Left[i])
						}
					 	slider.css('-webkit-transition', 'all 0.2s');                    //初始化动画时间					
					    
					    OpenAction();
				};
				
				
				/* 初始化值 */
				// EQ =( Math.abs((mL/sliderWidth)));
				// EQ = Number(sliderSwrap.children(".on").attr("eq"));
				EQ = Number($(this).children(".on").attr("eq"));
				// console.log(EQ);				
				$(".CC").css("background-color","rgba(255,255,255,.8)");
				$("#C"+EQ).css("background-color","#999");
				if(EQ==0){
					$(".Aleft").hide();
					}else if( EQ==sliderLength-1){
						$(".Aright").hide();
						}else{
							$('.Arrow').show();
							}
				initP		= null,			//初值控制值
				moveP		= null,			//每次获取到的值
				cx          = null;	
				mousedown   = false;
				stopIphone = false;	
			}
		var	autoPlay = function(speed,index){

 			if(speed>0){
 					
	           	autoSlider = setInterval(function(e){
	           		EQ = Number(sliderSwrap.children(".on").attr("eq"));
	           		for(var i = 0 ; i<sliderLength; i++){
							var Left =[];
							var ii = (i-EQ);
								if(ii<(-2)){
									ii +=sliderLength;
								}
								console.log(ii);
							  	Left[i] = ii*sliderWidth;	
							  	/*if(eQ == sliderLength-1){
							  		sliderSwrap.css('-webkit-transition', 'all 0s');                    //初始化动画时间     
							  		Left[0]  = sliderWidth;
							  		
							  	}else if(eQ == 1){
							  		sliderSwrap.css('-webkit-transition', 'all 0s');                    //初始化动画时间     
							  		Left[0]  = sliderWidth;
							  	};*/
							  		
							$("#slider"+i).css("left",Left[i]);
							//console.log(i*sliderWidth+(mL-sliderWidth))
						};
						
	           		slider.removeClass("on");
	           		EQ++;
	           		if((EQ)==(sliderLength)){
							EQ = 0;
						};
					$("#slider"+(EQ)).addClass("on");
					$(".CC").removeClass("on");
					$("#C"+(EQ)).addClass("on");
					$(".CC").css("background-color","rgba(255,255,255,.2)");
					$("#C"+(EQ)).css("background-color","rgb(255,255,255)");
					
					
				EQ = Number(sliderSwrap.children(".on").attr("eq"));
	           	// console.log(EQ)
				sliderSwrap.css('-webkit-transition', 'all 0.2s');                    //初始化动画时间     
	           },speed);
	           	
	        };
		} 

		autoPlay(speed)
	                 
		
}


function Panel(panelBox,panelHead,panelContent,ifSibling){//面板展开
		if(ifSibling){ifSibling=true;}else{ifSibling=false;}
		panelContent.hide(0);
		function OpenPanel(e){//开启功能
				panelHead.on('click',PanelToggle);
				return true;
			}
		function ClosePanel(e){//关闭功能
				panelHead.off('click');
				return false;
			};			
	    OpenPanel();			
		function PanelToggle(){
			var THIS = $(this);			
		   if(THIS.parent().find(panelContent).is(":hidden")){
			if(ifSibling) {panelBox.find(panelContent).slideUp(80);panelHead.removeClass('on');}
			THIS.parent().find(panelContent).slideDown(180)//.fadeIn(80);
			THIS.addClass('on');
			}else{
				THIS.parent().find(panelContent).slideUp(80)//.fadeOut(80);
				THIS.removeClass('on');
			}	
		}
}

function mySelector(mySelect,Selected,only,mySelectAll_L){
	var /*mySelect       = $(".myselect"),         //设置选择对象
	    Selected       = "selected",             //已选的Class Name 
		mySelectAll_L  = $(".slectAll"),         //全选按钮的位置
		only           = 3,                      //可选择的长度*/
		SelectLength   = mySelect.length,        //选择对象的长度
		SelectedLength = 0;                      //初始化已选择的长度
		if(isNaN(only)){
			mySelectAll_L = only;
			only=0;
			}else if(only==undefined||only>SelectLength){
				only=0;
				}
		//console.log(mySelectAll_L)
	function OpenSelect(e){//开启功能
				mySelect.on('click',SelectToggle);
				if(mySelectAll_L!=undefined){mySelectAll_L.on('click',SelectAll);}
				return true;
			}
	function CloseSelect(e){//关闭功能
				mySelect.off('click');
				mySelectAll_L.off('click');
				return false;
			};			
	OpenSelect();
	function SelectToggle(){
		var THIS = $(this);
		if(!THIS.hasClass(Selected)){
				if(SelectedLength<only ||only==0){
				THIS.addClass(Selected);
				SelectedLength+=1;
				    if(SelectLength==SelectedLength&&only==0){
						//if(mySelectAll_L!=undefined){mySelectAll_L.html("全取消")};					
					}
				//console.log(SelectedLength);
				}else if(only==1){
				  mySelect.removeClass(Selected); 
				  THIS.addClass(Selected);  
				}else {
			         alert("超过了可选范围")
		        }
		}else {
				THIS.removeClass(Selected);
				SelectedLength-=1;
				if(mySelectAll_L!=undefined){
					//mySelectAll_L.html("全选")
				   mySelectAll_L.removeClass(Selected);
				};			
				
				//console.log(SelectedLength);
		}
		
	}
	function SelectAll(){		
		if(SelectedLength<=only&&only!=0){
			alert("你还可以选择"+(only-SelectedLength)+"个！")
		}else{
			if(SelectedLength<SelectLength ){
				mySelectAll_L.addClass(Selected)
				mySelect.addClass(Selected);
				SelectedLength=SelectLength;
				//mySelectAll_L.html("全取消");
			//console.log(SelectedLength);
			}else {
				mySelectAll_L.removeClass(Selected)
				mySelect.removeClass(Selected);
				SelectedLength=0;
				//mySelectAll_L.html("全选");
				//console.log(SelectedLength);
			}
	   }
	}
}
function myFadeIn(){
	var iTemLength = $("body *").length;
	//console.log(iTemLength);
	$("body *").css("opacity","0");
	for(var i = 0 ; i<=iTemLength;i++){
		$("body *").eq(i).animate({"opacity":"1"},(i*100+200)>1200 ? 1200:(i*100+200))
	}
}
function success(){
			
			}
function square(element){
	//alert();
			element.height(parseFloat(element.width()));
			element.width(parseFloat(element.width()))
			}

