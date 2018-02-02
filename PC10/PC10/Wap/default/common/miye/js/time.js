//获取当前时秒数Time()
function NowTime(){
	var d=new Date();
	var dT = d.getTime();
	//console.log(dT);
	return dT;		
}

function GetTime(year,month,day,hour,minute,seconds){
	month-=1;
	if(seconds>60||seconds<0||!seconds){
		seconds = 0;
	}else{
		seconds = seconds;
	}
	var MissionDate = new Date();
	MissionDate.setFullYear(year,month,day);
	MissionDate.setHours(hour,minute,seconds,0);
	var MD=MissionDate.getTime();
	// console.log(MissionDate);	
	return MD;	
};

//求时间差
function TimeLeft(Start,End){		
	var Start=Start;  //开始时间
	var End=End;    //结束时间
	var date3=End-Start  //时间差的毫秒数		  
	//计算出相差天数
	var days=Math.floor(date3/(24*3600*1000))		   
	//计算出小时数	
	// console.log(days);	  
	var leave1=date3%(24*3600*1000)    //计算天数后剩余的毫秒数
	var hours=Math.floor(leave1/(3600*1000))
	//计算相差分钟数
	var leave2=leave1%(3600*1000)        //计算小时数后剩余的毫秒数
	var minutes=Math.floor(leave2/(60*1000))		  
	//计算相差秒数
	var leave3=leave2%(60*1000)      //计算分钟数后剩余的毫秒数
	var seconds=Math.round(leave3/1000);
	if(seconds<0||minutes<0||hours<0||days<0){
	  var timeArray = new Array;
	  timeArray['d']=0;
	  timeArray['h']=0;
	  timeArray['m']=0;
	  timeArray['s']=0;
	  }else{		  
	  var timeArray = new Array;
	  timeArray['d']=days;
	  timeArray['h']=hours<10?"0"+hours:hours;
	  timeArray['m']=minutes<10?"0"+minutes:minutes;
	  timeArray['s']=seconds<10?"0"+seconds:seconds;
	}
	return timeArray;		  
// alert(" 相差 "+days+"天 "+hours+"小时 "+minutes+" 分钟"+seconds+" 秒")
}