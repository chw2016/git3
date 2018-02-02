
function getIt(){
		var a=parseInt(Math.random() * 8)+1;
		//var a= 5;
		return a;
		}
function EQ(){
		var ts = $("#ts").val();
	if(parseInt(ts)>0){
		ts -=1;
		Go(getIt());
		$("#ts").val(ts);		
		}else{
			alert("次数用完了！")
			}
	}


var all=8;
var aa;

function Go(a){
/* var a=parseInt(Math.random() * 8)+1;*/
 var b=8;
 var t=8*b+a;
     
 var d=1;
 //alert(t);
 $("#d0").html("<span style='color:#fff;font-size:14px'>抽奖中···</span>");
 document.getElementById("aa").value=a;
 document.getElementById("dd").value=d;
 document.getElementById("tt").value=t;
 aa=setTimeout("turn()",20);
}
//a 中奖号
//c 转数
//t 统计数
//d 转的当前号
function turn(){
 var a=parseInt(document.getElementById("aa").value);
 var d=parseInt(document.getElementById("dd").value);
 var t=parseInt(document.getElementById("tt").value);
 if(t==1){
  for(var i=1;i<=all;i++){
	 // console.log(a+";"+i);
   if(i==a){
	   var jiangpin = $("#d"+i).html();
    document.getElementById("d"+i).style.backgroundColor="rgba(255,0,0,0.5)";
    document.getElementById("d"+i).style.color="#ffffff";
	alert("恭喜你获得："+jiangpin);
   }else{
    document.getElementById("d"+i).style.backgroundColor="#fff";
    document.getElementById("d"+i).style.color="#156A00";
   }
   $("#d0").html("<span onclick='EQ()'>再来！</span>");
  }
 }else{
// alert(t);
  if(d>all)d=1;
  for(var i=1;i<=8;i++){
   if(i==d){
    document.getElementById("d"+i).style.backgroundColor="rgba(255,255,255,0.5)";
    
   }else{
    document.getElementById("d"+i).style.backgroundColor="#fff";
    document.getElementById("d"+i).style.color="#156A00";
   }
  }
  d++;
 }
  
 t--;
 document.getElementById("aa").value=a;
 document.getElementById("dd").value=d;
 document.getElementById("tt").value=t;
 if(t==0){
	clearTimeout(aa);
	return t=1;
 }else{
  if(t>60){
    aa=setTimeout("turn()",200);
  }else{
   if(t<(parseInt(Math.random() * 5)+2)){
    aa=setTimeout("turn()",700);
   }else if(t<10){
    aa=setTimeout("turn()",200);
   }else if(t<20){
    aa=setTimeout("turn()",100);
   }else{
	aa=setTimeout("turn()",50);
	   }
  }
 }
}