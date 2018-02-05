$(function(){
	//首页TAB
	$(".user-ul-li").click(function(){
		$(this).addClass('on').siblings().removeClass('on');
		var index= $(this).index();
		$(".main-ul>li").eq(index).show().siblings().hide();
	})
	//弹出框打勾
	$(".Mask-T-tab").click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	})
	
	//确认收到显示弹出
	$("#curriculum").click(function(){
		$("#reminder").addClass('is-visible')
	})
	$("#editclass").click(function(){
		$("#reform").addClass('is-visible')
	})
	$(".Mask-tab-btn").click(function(){
		$(".Mask").removeClass('is-visible')
	})
	$(".course-btn-date").click(function(){
		$("#evaluate").addClass('is-visible');
	})
	$(".icon-swop").click(function(){
		$("#cut").addClass('is-visible');
	})
	$(".icon-relieve").click(function(){
		$("#relieve").addClass('is-visible')
	})
	//下拉框
	$("#select").click(function(){
		$(".Mask-pull-down").toggleClass('on');
	})
	$(".Mask-pull-down-ul-li").click(function(){
		var text = $(this).html();
		$("#select").find("input").val(text);
		$("#select").removeClass('on');
	})
	//星级
	$("#star-full span").click(function(){
		var index=$(this).index()//获取当前索引
		$("#star-full span:lt("+index+1+")").addClass("on")//给灰色的星星加点亮CLASS
		$("#star-full span:gt("+index+")").removeClass("on")//去掉点亮的class
	})
})
//选择日期
function scrollDate(opt,callback){
			      $(opt.obj).mobiscroll().date({
			          theme: 'ios',     // Specify theme like: theme: 'ios' or omit setting to use default
			          mode: 'Scroller',       // Specify scroller mode like: mode: 'mixed' or omit setting to use default
			          display: 'bottom', // Specify display mode like: display: 'bottom' or omit setting to use default
			          lang: "zh",       // Specify language like: lang: 'pl' or omit settring to use default
			          onSelect: function (valueText, inst) {

			              function _setVal(obj,date){
			                  $(obj).mobiscroll("setVal",date)
			              }
			              if(typeof callback === "function"){
			                  callback.call(this,{
			                      valueText:valueText,
			                      inst:inst,
			                      _setVal :_setVal
			                  });
			              }
			          },
			          minDate: opt.minDate,  // More info about minDate: http://docs.mobiscroll.com/2-14-0/datetime#!opt-minDate
			          maxDate: opt.maxDate,   // More info about maxDate: http://docs.mobiscroll.com/2-14-0/datetime#!opt-maxDate
			          stepMinute: 1  // More info about stepMinute: http://docs.mobiscroll.com/2-14-0/datetime#!opt-stepMinute
			      });
			   }
 function scrollDateAction (obj,callback) {
			        scrollDate({
			            "obj":obj,
			            "minDate": new Date(2010,0,1),
			            "maxDate" : new Date(2030,0,1)
			        },function(data){
			            var date = data.valueText;
			            $(this).text(date);
			            $(this).next().val(date);
			            typeof callback === "function"?
			            callback():
			            null;
			        })
			    }
			    //选择日期
function scrollDatetime(opt,callback){
			      $(opt.obj).mobiscroll().datetime({
			          theme: 'ios',     // Specify theme like: theme: 'ios' or omit setting to use default
			          mode: 'Scroller',       // Specify scroller mode like: mode: 'mixed' or omit setting to use default
			          display: 'bottom', // Specify display mode like: display: 'bottom' or omit setting to use default
			          lang: "zh",       // Specify language like: lang: 'pl' or omit settring to use default
			          onSelect: function (valueText, inst) {

			              function _setVal(obj,date){
			                  $(obj).mobiscroll("setVal",date)
			              }
			              if(typeof callback === "function"){
			                  callback.call(this,{
			                      valueText:valueText,
			                      inst:inst,
			                      _setVal :_setVal
			                  });
			              }
			          },
			          minDate: opt.minDate,  // More info about minDate: http://docs.mobiscroll.com/2-14-0/datetime#!opt-minDate
			          maxDate: opt.maxDate,   // More info about maxDate: http://docs.mobiscroll.com/2-14-0/datetime#!opt-maxDate
			          stepMinute: 1  // More info about stepMinute: http://docs.mobiscroll.com/2-14-0/datetime#!opt-stepMinute
			      });
			   }
 function scrollDatetimeAction (obj,callback) {
			        scrollDatetime({
			            "obj":obj,
			            "minDate": new Date(2010,0,1),
			            "maxDate" : new Date(2020,0,1)
			        },function(data){
			            var date = data.valueText;
			            $(this).text(date);
			            $(this).next().val(date);
			            typeof callback === "function"?
			            callback():
			            null;
			        })
			    }
		//执行日期方法
    		$(function(){
				scrollDateAction("#start_time",function(){
					
				});
				scrollDateAction("#end_time",function(){
					
				})
    		})