/*
* @Author: Samphay
* @Date:   2014-11-28 09:57:33
* @Last Modified by:   星辉
* @Last Modified time: 2015-02-06 15:09:52
* @Verson: Alpha 1.0
*/


/*
 * Usage
 *
 *
CSS:
<style type="text/css" media="screen">
    .myul{ display: flex; display: -webkit-box; }
    .myli{ -webkit-box-flex: 1; -moz-box-flex: 1; }
    .sliderBox{ width: 100%; height: 100%; overflow: hidden; position: relative; }
    .sliderWrap{ width: 100%; height: 100%; display: -webkit-box; background-color: #ccc; }
    .slider{ width: 100%; height: 100%; z-index: 1; background-color: #ccc; }
</style>

HTML
<div class="sliderBox">
    <div class="sliderWrap myul">
        <div class="slider containBg">0</div>
        <div class="slider containBg">1</div>
        <div class="slider containBg">2</div>
        <div class="slider containBg">3</div>
    </div>
</div>

JS
slider($(".sliderBox"),function(index){
    console.log(index)
}, {'showCount':false});
 *
 *
 */
function slider(sliderBox,callback,option){//滑动切换Alpha版
    if (typeof callback != 'function') {
        callback = function(index){}
    };
    //config
    option  = $.extend({}, {
        'slider'     : 'slider',
        'sliderWrap' : 'sliderWrap',
        'showCount' : true
    }, option);
    slider                 = sliderBox.find('.'+option.slider);
    sliderSwrap            = sliderBox.find('.'+option.sliderWrap);
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
    stopIphone             = null,
    SliderTip              = $(".SliderTip"),
    countHtML			   = "<div class='countSlider'></div>";
    // countHtMLStyle         = 'background-color:red;position:absolute;bottom:0;width:100%;'
    if (option.showCount) {
        sliderBox.append(countHtML);
        $(".countSlider").css({
            "position": 'relative',
            "bottom": '16px',
            "margin": '0 auto',
            "width" : 20*sliderLength


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
            "background-color": "#fff",
            "border-radius": "50%",
           " -webkit-border-radius": "50%",
           "margin-left": "5px",
           "margin-right": "5px",
           "box-shadow" : "2px 2px 2px rgba(0,0,0,0)"
        })
        $("#C"+0).css('background-color', 'rgb(77,208,200)');
        //alert(countHtML);
    };
    sliderBox.css("position","relative");
    sliderSwrap.css('-webkit-transition-timing-function', 'ease-in-out'); //初始化动画曲线
    if(!slider.eq(0).hasClass('on')) slider.eq(0).addClass("on");SliderTip.eq(0).addClass("on");             //如果第一个slider没有加上on，则会自动帮加上。
    function index(){
        return slider.filter('.on').index()
    }

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
        initP = window.event.touches[0].pageX;
         sliderSwrap.css('-webkit-transition', 'all 0s');                    //重置动画时间
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
        }else if(mousedown){
            moveP = e.pageX;
            }
        cx =-(initP-moveP);
        //console.log(cx);
        //$(".testText").html(sliderWidth);
        //sliderSwrap.css({"margin-left":(mL+cx)+"px"});
        sliderSwrap.css({"transform":"translate3d("+(mL+cx)+"px,0,0"+")"});


    }
    function OnEnd(e){
    CloseAction();
    endTime = e.timeStamp;
    //$(".testText").html(sliderWidth);


    if(cx<(0-80) && (mL-sliderWidth)> (0-sliderSwapWidth)){
            //sliderSwrap.animate({"margin-left":(mL-sliderWidth)+"px"},200);
            sliderSwrap.css({"transform":"translate3d("+(mL-sliderWidth)+"px,0,0"+")"});
            slider.siblings().removeClass("on");
            slider.eq(EQ+1).addClass("on");
            SliderTip.siblings().removeClass("on");
            SliderTip.eq(EQ+1).addClass("on");

    mL -=sliderWidth;                                     //重新获取滑动块位置
    sliderSwrap.css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
    callback(index());

    OpenAction();
    }else if(cx>80 && mL < 0){
           // sliderSwrap.animate({"margin-left":(mL+sliderWidth)+"px"},200);
            sliderSwrap.css({"transform":"translate3d("+(mL+sliderWidth)+"px,0,0"+")"});
            slider.siblings().removeClass("on");
            slider.eq(EQ-1).addClass("on");
            SliderTip.siblings().removeClass("on");
            SliderTip.eq(EQ-1).addClass("on");
    mL +=sliderWidth;                                     //重新获取滑动块位置
    sliderSwrap.css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
    callback(index());

    OpenAction();
    }else {
          // sliderSwrap.animate({"margin-left":(mL)+"px"},200);
          sliderSwrap.css({"transform":"translate3d("+(mL)+"px,0,0"+")"});
          sliderSwrap.css('-webkit-transition', 'all 0.2s');                    //初始化动画时间
         callback(index())
           OpenAction();
    };

    /* 初始化值 */
    EQ =( Math.abs((mL/sliderWidth)));
    //console.log(EQ);
    $(".CC").css("background-color","#fff");
    $("#C"+EQ).css("background-color","rgb(77,208,200)");
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
}
