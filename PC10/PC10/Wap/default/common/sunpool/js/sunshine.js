function getsunshine()
{
	var sunshine = $('select#area-2 option:selected').val();											//获得日照小时

	//安徽省
	if(sunshine==3){
		document.getElementById("sunshine").innerHTML="3.66";											//安庆
	}else if(sunshine==4){
		document.getElementById("sunshine").innerHTML="3.89";											//蚌埠
	}else if(sunshine==5){
		document.getElementById("sunshine").innerHTML="4.01";											//亳州
	}else if(sunshine==6){
		document.getElementById("sunshine").innerHTML="3.66";											//池州
	}else if(sunshine==7){
		document.getElementById("sunshine").innerHTML="3.88";											//滁州
	}else if(sunshine==8){
		document.getElementById("sunshine").innerHTML="3.87";											//阜阳
	}else if(sunshine==9){
		document.getElementById("sunshine").innerHTML="3.7";											//合肥
	}else if(sunshine==10){
		document.getElementById("sunshine").innerHTML="4.05";											//淮北
	}else if(sunshine==11){
		document.getElementById("sunshine").innerHTML="3.89";											//淮南
	}else if(sunshine==12){
		document.getElementById("sunshine").innerHTML="3.54";											//黄山
	}else if(sunshine==13){
		document.getElementById("sunshine").innerHTML="3.67";											//六安
	}else if(sunshine==14){
		document.getElementById("sunshine").innerHTML="3.75";											//马鞍山
	}else if(sunshine==15){
		document.getElementById("sunshine").innerHTML="4.05";											//宿州
	}else if(sunshine==16){
		document.getElementById("sunshine").innerHTML="3.66";											//铜陵
	}else if(sunshine==17){
		document.getElementById("sunshine").innerHTML="3.75";											//芜湖
	}else if(sunshine==18){	
		document.getElementById("sunshine").innerHTML="3.75";											//宣城
	//福建
	}else if(sunshine==20){	
		document.getElementById("sunshine").innerHTML="3.54";											//福州
	}else if(sunshine==21){	
		document.getElementById("sunshine").innerHTML="3.62";											//龙岩
	}else if(sunshine==22){	
		document.getElementById("sunshine").innerHTML="3.66";											//南平
	}else if(sunshine==23){	
		document.getElementById("sunshine").innerHTML="3.54";											//宁德
	}else if(sunshine==24){	
		document.getElementById("sunshine").innerHTML="3.63";											//莆田
	}else if(sunshine==25){	
		document.getElementById("sunshine").innerHTML="4.02";											//泉州
	}else if(sunshine==26){	
		document.getElementById("sunshine").innerHTML="3.5";											//三明
	}else if(sunshine==27){	
		document.getElementById("sunshine").innerHTML="3.02";											//厦门
	}else if(sunshine==28){	
		document.getElementById("sunshine").innerHTML="3.73";											//漳州
	//甘肃
	}else if(sunshine==30){	
		document.getElementById("sunshine").innerHTML="4.51";											//白银
	}else if(sunshine==31){	
		document.getElementById("sunshine").innerHTML="4.43";											//定西
	}else if(sunshine==32){	
		document.getElementById("sunshine").innerHTML="3.94";											//甘南
	}else if(sunshine==33){	
		document.getElementById("sunshine").innerHTML="4.56";											//嘉峪关
	}else if(sunshine==34){	
		document.getElementById("sunshine").innerHTML="4.67";											//金昌
	}else if(sunshine==35){	
		document.getElementById("sunshine").innerHTML="4.56";											//酒泉
	}else if(sunshine==36){	
		document.getElementById("sunshine").innerHTML="4.43";											//兰州
	}else if(sunshine==37){	
		document.getElementById("sunshine").innerHTML="4.45";											//临夏
	}else if(sunshine==38){	
		document.getElementById("sunshine").innerHTML="4.1";											//陇南
	}else if(sunshine==39){	
		document.getElementById("sunshine").innerHTML="4.18";											//平凉
	}else if(sunshine==40){	
		document.getElementById("sunshine").innerHTML="4.36";											//庆阳
	}else if(sunshine==41){	
		document.getElementById("sunshine").innerHTML="4.03";											//天水
	}else if(sunshine==42){	
		document.getElementById("sunshine").innerHTML="4.33";											//武威
	}else if(sunshine==43){	
		document.getElementById("sunshine").innerHTML="4.57";											//张掖
	//广东
	}else if(sunshine==45){	
		document.getElementById("sunshine").innerHTML="3.88";											//潮州
	}else if(sunshine==46){	
		document.getElementById("sunshine").innerHTML="3.69";											//佛山
	}else if(sunshine==47){	
		document.getElementById("sunshine").innerHTML="3.69";											//广州
	}else if(sunshine==48){	
		document.getElementById("sunshine").innerHTML="3.76";											//河源
	}else if(sunshine==49){	
		document.getElementById("sunshine").innerHTML="3.76";											//惠州
	}else if(sunshine==50){	
		document.getElementById("sunshine").innerHTML="3.87";											//江门
	}else if(sunshine==51){	
		document.getElementById("sunshine").innerHTML="3.88";											//揭阳
	}else if(sunshine==52){	
		document.getElementById("sunshine").innerHTML="3.91";											//茂名
	}else if(sunshine==53){	
		document.getElementById("sunshine").innerHTML="3.75";											//梅州
	}else if(sunshine==54){	
		document.getElementById("sunshine").innerHTML="3.69";											//清远
	}else if(sunshine==55){	
		document.getElementById("sunshine").innerHTML="3.88";											//汕头
	}else if(sunshine==56){	
		document.getElementById("sunshine").innerHTML="4.29";											//汕尾
	}else if(sunshine==57){	
		document.getElementById("sunshine").innerHTML="3.44";											//韶关
	}else if(sunshine==58){	
		document.getElementById("sunshine").innerHTML="3.91";											//深圳
	}else if(sunshine==59){	
		document.getElementById("sunshine").innerHTML="4.09";											//阳江
	}else if(sunshine==60){	
		document.getElementById("sunshine").innerHTML="3.78";											//云浮
	}else if(sunshine==61){	
		document.getElementById("sunshine").innerHTML="3.91";											//湛江
	}else if(sunshine==62){	
		document.getElementById("sunshine").innerHTML="3.65";											//肇庆
	}else if(sunshine==63){	
		document.getElementById("sunshine").innerHTML="3.87";											//珠海
	}else if(sunshine==64){	
		document.getElementById("sunshine").innerHTML="3.87";											//中山
	}else if(sunshine==65){	
		document.getElementById("sunshine").innerHTML="3.69";											//东莞
	//广西
	}else if(sunshine==67){	
		document.getElementById("sunshine").innerHTML="3.73";											//百色
	}else if(sunshine==68){	
		document.getElementById("sunshine").innerHTML="4.08";											//北海
	}else if(sunshine==69){	
		document.getElementById("sunshine").innerHTML="3.72";											//崇左
	}else if(sunshine==70){	
		document.getElementById("sunshine").innerHTML="3.86";											//防城港
	}else if(sunshine==71){	
		document.getElementById("sunshine").innerHTML="3.59";											//贵港
	}else if(sunshine==72){	
		document.getElementById("sunshine").innerHTML="3.16";											//桂林
	}else if(sunshine==73){	
		document.getElementById("sunshine").innerHTML="3.37";											//河池
	}else if(sunshine==74){	
		document.getElementById("sunshine").innerHTML="5.98";											//贺州
	}else if(sunshine==75){	
		document.getElementById("sunshine").innerHTML="3.59";											//来宾
	}else if(sunshine==76){	
		document.getElementById("sunshine").innerHTML="3.39";											//柳州
	}else if(sunshine==77){	
		document.getElementById("sunshine").innerHTML="3.75";											//南宁
	}else if(sunshine==78){	
		document.getElementById("sunshine").innerHTML="3.86";											//钦州
	}else if(sunshine==79){	
		document.getElementById("sunshine").innerHTML="3.65";											//梧州
	}else if(sunshine==80){	
		document.getElementById("sunshine").innerHTML="3.75";											//玉林
	//海南
	}else if(sunshine==82){	
		document.getElementById("sunshine").innerHTML="4.43";											//海口
	}else if(sunshine==83){	
		document.getElementById("sunshine").innerHTML="4.52";											//儋州
	}else if(sunshine==84){	
		document.getElementById("sunshine").innerHTML="4.27";											//琼海
	}else if(sunshine==85){	
		document.getElementById("sunshine").innerHTML="4.55";											//东方市
	}else if(sunshine==86){	
		document.getElementById("sunshine").innerHTML="4.55";											//三亚
	}else if(sunshine==87){	
		document.getElementById("sunshine").innerHTML="5.5";											//万宁
	}else if(sunshine==88){	
		document.getElementById("sunshine").innerHTML="5.5";											//文昌
	//贵州
	}else if(sunshine==90){	
		document.getElementById("sunshine").innerHTML="3.57";											//安顺
	}else if(sunshine==91){	
		document.getElementById("sunshine").innerHTML="3.29";											//毕节
	}else if(sunshine==92){	
		document.getElementById("sunshine").innerHTML="3.35";											//贵阳
	}else if(sunshine==93){	
		document.getElementById("sunshine").innerHTML="4.15";											//六盘水
	}else if(sunshine==94){	
		document.getElementById("sunshine").innerHTML="3.16";											//黔东南
	}else if(sunshine==95){	
		document.getElementById("sunshine").innerHTML="3.16";											//黔南
	}else if(sunshine==96){	
		document.getElementById("sunshine").innerHTML="4.21";											//黔西南
	}else if(sunshine==97){	
		document.getElementById("sunshine").innerHTML="3.03";											//铜仁
	}else if(sunshine==98){	
		document.getElementById("sunshine").innerHTML="3.13";											//遵义
	//河北
	}else if(sunshine==100){	
		document.getElementById("sunshine").innerHTML="4.32";											//保定
	}else if(sunshine==101){	
		document.getElementById("sunshine").innerHTML="4.33";											//沧州
	}else if(sunshine==102){	
		document.getElementById("sunshine").innerHTML="4.44";											//承德
	}else if(sunshine==103){	
		document.getElementById("sunshine").innerHTML="4.21";											//邯郸
	}else if(sunshine==104){	
		document.getElementById("sunshine").innerHTML="4.3";											//衡水
	}else if(sunshine==105){	
		document.getElementById("sunshine").innerHTML="4.32";											//廊坊
	}else if(sunshine==106){	
		document.getElementById("sunshine").innerHTML="4.21";											//秦皇岛
	}else if(sunshine==107){	
		document.getElementById("sunshine").innerHTML="4.29";											//石家庄
	}else if(sunshine==108){	
		document.getElementById("sunshine").innerHTML="4.36";											//唐山
	}else if(sunshine==109){	
		document.getElementById("sunshine").innerHTML="4.25";											//邢台
	}else if(sunshine==110){	
		document.getElementById("sunshine").innerHTML="4.53";											//张家口
	//吉林
	}else if(sunshine==112){	
		document.getElementById("sunshine").innerHTML="4.17";											//白城
	}else if(sunshine==113){	
		document.getElementById("sunshine").innerHTML="3.93";											//白山
	}else if(sunshine==114){	
		document.getElementById("sunshine").innerHTML="4.05";											//长春
	}else if(sunshine==115){	
		document.getElementById("sunshine").innerHTML="3.97";											//吉林
	}else if(sunshine==116){	
		document.getElementById("sunshine").innerHTML="4.02";											//辽源
	}else if(sunshine==117){	
		document.getElementById("sunshine").innerHTML="4.13";											//四平
	}else if(sunshine==118){	
		document.getElementById("sunshine").innerHTML="4.07";											//松原
	}else if(sunshine==119){	
		document.getElementById("sunshine").innerHTML="3.98";											//通化
	}else if(sunshine==120){	
		document.getElementById("sunshine").innerHTML="3.9";											//延边
	//河南
	}else if(sunshine==122){	
		document.getElementById("sunshine").innerHTML="4.21";											//安阳
	}else if(sunshine==123){	
		document.getElementById("sunshine").innerHTML="4.14";											//鹤壁
	}else if(sunshine==124){	
		document.getElementById("sunshine").innerHTML="4.15";											//焦作
	}else if(sunshine==125){	
		document.getElementById("sunshine").innerHTML="4.07";											//开封
	}else if(sunshine==126){	
		document.getElementById("sunshine").innerHTML="3.95";											//洛阳
	}else if(sunshine==127){	
		document.getElementById("sunshine").innerHTML="3.98";											//漯河
	}else if(sunshine==128){	
		document.getElementById("sunshine").innerHTML="3.8";											//南阳
	}else if(sunshine==129){	
		document.getElementById("sunshine").innerHTML="3.89";											//平顶山
	}else if(sunshine==130){	
		document.getElementById("sunshine").innerHTML="4.14";											//濮阳
	}else if(sunshine==131){	
		document.getElementById("sunshine").innerHTML="3.98";											//三门峡
	}else if(sunshine==132){	
		document.getElementById("sunshine").innerHTML="4.1";											//商丘
	}else if(sunshine==133){	
		document.getElementById("sunshine").innerHTML="4.15";											//新乡
	}else if(sunshine==134){	
		document.getElementById("sunshine").innerHTML="3.83";											//信阳
	}else if(sunshine==135){	
		document.getElementById("sunshine").innerHTML="4.02";											//许昌
	}else if(sunshine==136){	
		document.getElementById("sunshine").innerHTML="4.02";											//郑州
	}else if(sunshine==137){	
		document.getElementById("sunshine").innerHTML="3.98";											//周口
	}else if(sunshine==138){	
		document.getElementById("sunshine").innerHTML="3.83";											//驻马店
	}else if(sunshine==139){	
		document.getElementById("sunshine").innerHTML="4.18";											//济源
	//黑龙江
	}else if(sunshine==141){	
		document.getElementById("sunshine").innerHTML="4.05";											//大庆
	}else if(sunshine==142){	
		document.getElementById("sunshine").innerHTML="3.53";											//大兴安岭
	}else if(sunshine==143){	
		document.getElementById("sunshine").innerHTML="3.96";											//哈尔滨
	}else if(sunshine==144){	
		document.getElementById("sunshine").innerHTML="3.71";											//鹤岗
	}else if(sunshine==145){	
		document.getElementById("sunshine").innerHTML="3.64";											//黑河
	}else if(sunshine==146){	
		document.getElementById("sunshine").innerHTML="3.85";											//鸡西
	}else if(sunshine==147){	
		document.getElementById("sunshine").innerHTML="3.82";											//佳木斯
	}else if(sunshine==148){	
		document.getElementById("sunshine").innerHTML="3.85";											//牡丹江
	}else if(sunshine==149){	
		document.getElementById("sunshine").innerHTML="3.85";											//七台河
	}else if(sunshine==150){	
		document.getElementById("sunshine").innerHTML="3.94";											//齐齐哈尔
	}else if(sunshine==151){	
		document.getElementById("sunshine").innerHTML="3.82";											//双鸭山
	}else if(sunshine==152){	
		document.getElementById("sunshine").innerHTML="3.95";											//绥化
	}else if(sunshine==153){	
		document.getElementById("sunshine").innerHTML="3.75";											//伊春
	//宁夏
	}else if(sunshine==155){	
		document.getElementById("sunshine").innerHTML="4.44";											//固原
	}else if(sunshine==156){	
		document.getElementById("sunshine").innerHTML="4.6";											//石嘴山
	}else if(sunshine==157){	
		document.getElementById("sunshine").innerHTML="4.54";											//吴忠
	}else if(sunshine==158){	
		document.getElementById("sunshine").innerHTML="5.45";											//银川
	}else if(sunshine==159){	
		document.getElementById("sunshine").innerHTML="4.59";											//中卫
	//湖北
	}else if(sunshine==161){	
		document.getElementById("sunshine").innerHTML="3.67";											//鄂州
	}else if(sunshine==162){	
		document.getElementById("sunshine").innerHTML="3.2";											//恩施
	}else if(sunshine==163){	
		document.getElementById("sunshine").innerHTML="3.67";											//黄冈
	}else if(sunshine==164){	
		document.getElementById("sunshine").innerHTML="3.66";											//黄石
	}else if(sunshine==165){	
		document.getElementById("sunshine").innerHTML="3.62";											//荆门
	}else if(sunshine==166){	
		document.getElementById("sunshine").innerHTML="3.54";											//荆州
	}else if(sunshine==167){	
		document.getElementById("sunshine").innerHTML="3.61";											//十堰
	}else if(sunshine==168){	
		document.getElementById("sunshine").innerHTML="3.7";											//随州
	}else if(sunshine==169){	
		document.getElementById("sunshine").innerHTML="3.67";											//武汉
	}else if(sunshine==170){	
		document.getElementById("sunshine").innerHTML="3.46";											//咸宁
	}else if(sunshine==171){	
		document.getElementById("sunshine").innerHTML="3.74";											//襄阳
	}else if(sunshine==172){	
		document.getElementById("sunshine").innerHTML="3.7";											//孝感
	}else if(sunshine==173){	
		document.getElementById("sunshine").innerHTML="3.35";											//宜昌
	}else if(sunshine==174){	
		document.getElementById("sunshine").innerHTML="3.62";											//仙桃
	}else if(sunshine==175){	
		document.getElementById("sunshine").innerHTML="3.54";											//潜江
	}else if(sunshine==176){	
		document.getElementById("sunshine").innerHTML="2.94";											//天门
	}else if(sunshine==177){	
		document.getElementById("sunshine").innerHTML="3.62";											//神农架
	//湖南
	}else if(sunshine==179){	
		document.getElementById("sunshine").innerHTML="3.36";											//长沙
	}else if(sunshine==180){	
		document.getElementById("sunshine").innerHTML="3.24";											//常德
	}else if(sunshine==181){	
		document.getElementById("sunshine").innerHTML="3.34";											//郴州
	}else if(sunshine==182){	
		document.getElementById("sunshine").innerHTML="3.29";											//衡阳
	}else if(sunshine==183){	
		document.getElementById("sunshine").innerHTML="3.03";											//怀化
	}else if(sunshine==184){	
		document.getElementById("sunshine").innerHTML="3.19";											//娄底
	}else if(sunshine==185){	
		document.getElementById("sunshine").innerHTML="3.19";											//邵阳
	}else if(sunshine==186){	
		document.getElementById("sunshine").innerHTML="3.3";											//湘潭
	}else if(sunshine==187){	
		document.getElementById("sunshine").innerHTML="3.03";											//湘西
	}else if(sunshine==188){	
		document.getElementById("sunshine").innerHTML="3.29";											//益阳
	}else if(sunshine==189){	
		document.getElementById("sunshine").innerHTML="3.22";											//永州
	}else if(sunshine==190){	
		document.getElementById("sunshine").innerHTML="3.45";											//岳阳
	}else if(sunshine==191){	
		document.getElementById("sunshine").innerHTML="3.1";											//张家界
	}else if(sunshine==192){	
		document.getElementById("sunshine").innerHTML="3.33";											//株洲
	//浙江
	}else if(sunshine==194){	
		document.getElementById("sunshine").innerHTML="3.69";											//杭州
	}else if(sunshine==195){	
		document.getElementById("sunshine").innerHTML="3.69";											//湖州
	}else if(sunshine==196){	
		document.getElementById("sunshine").innerHTML="3.69";											//嘉兴
	}else if(sunshine==197){	
		document.getElementById("sunshine").innerHTML="3.56";											//金华
	}else if(sunshine==198){	
		document.getElementById("sunshine").innerHTML="3.53";											//丽水
	}else if(sunshine==199){	
		document.getElementById("sunshine").innerHTML="3.59";											//宁波
	}else if(sunshine==200){	
		document.getElementById("sunshine").innerHTML="3.52";											//衢州
	}else if(sunshine==201){	
		document.getElementById("sunshine").innerHTML="3.69";											//绍兴
	}else if(sunshine==202){	
		document.getElementById("sunshine").innerHTML="3.58";											//台州
	}else if(sunshine==203){	
		document.getElementById("sunshine").innerHTML="3.58";											//温州
	}else if(sunshine==204){	
		document.getElementById("sunshine").innerHTML="3.92";											//舟山
	//江苏
	}else if(sunshine==206){	
		document.getElementById("sunshine").innerHTML="3.77";											//常州
	}else if(sunshine==207){	
		document.getElementById("sunshine").innerHTML="4.02";											//淮安
	}else if(sunshine==208){	
		document.getElementById("sunshine").innerHTML="4.09";											//连云港
	}else if(sunshine==209){	
		document.getElementById("sunshine").innerHTML="3.88";											//南京
	}else if(sunshine==210){	
		document.getElementById("sunshine").innerHTML="3.93";											//南通
	}else if(sunshine==211){	
		document.getElementById("sunshine").innerHTML="3.78";											//苏州
	}else if(sunshine==212){	
		document.getElementById("sunshine").innerHTML="4.01";											//宿迁
	}else if(sunshine==213){	
		document.getElementById("sunshine").innerHTML="3.88";											//泰州
	}else if(sunshine==214){	
		document.getElementById("sunshine").innerHTML="3.78";											//无锡
	}else if(sunshine==215){	
		document.getElementById("sunshine").innerHTML="4.12";											//徐州
	}else if(sunshine==216){	
		document.getElementById("sunshine").innerHTML="4.02";											//盐城
	}else if(sunshine==217){	
		document.getElementById("sunshine").innerHTML="3.88";											//扬州
	}else if(sunshine==218){	
		document.getElementById("sunshine").innerHTML="3.88";											//镇江
	//江西
	}else if(sunshine==220){	
		document.getElementById("sunshine").innerHTML="4.88";											//抚州
	}else if(sunshine==221){	
		document.getElementById("sunshine").innerHTML="3.41";											//赣州
	}else if(sunshine==222){	
		document.getElementById("sunshine").innerHTML="3.34";											//吉安
	}else if(sunshine==223){	
		document.getElementById("sunshine").innerHTML="3.6";											//景德镇
	}else if(sunshine==224){	
		document.getElementById("sunshine").innerHTML="3.49";											//九江
	}else if(sunshine==225){	
		document.getElementById("sunshine").innerHTML="3.44";											//南昌
	}else if(sunshine==226){	
		document.getElementById("sunshine").innerHTML="3.33";											//萍乡
	}else if(sunshine==227){	
		document.getElementById("sunshine").innerHTML="3.58";											//上饶
	}else if(sunshine==228){	
		document.getElementById("sunshine").innerHTML="3.34";											//新余
	}else if(sunshine==229){	
		document.getElementById("sunshine").innerHTML="3.34";											//宜春
	}else if(sunshine==230){	
		document.getElementById("sunshine").innerHTML="3.58";											//鹰潭
	//辽宁
	}else if(sunshine==232){	
		document.getElementById("sunshine").innerHTML="4.25";											//鞍山
	}else if(sunshine==233){	
		document.getElementById("sunshine").innerHTML="4.18";											//本溪
	}else if(sunshine==234){	
		document.getElementById("sunshine").innerHTML="4.38";											//朝阳
	}else if(sunshine==235){	
		document.getElementById("sunshine").innerHTML="4.56";											//大连
	}else if(sunshine==236){	
		document.getElementById("sunshine").innerHTML="3.97";											//丹东
	}else if(sunshine==237){	
		document.getElementById("sunshine").innerHTML="4.18";											//抚顺
	}else if(sunshine==238){	
		document.getElementById("sunshine").innerHTML="4.19";											//阜新
	}else if(sunshine==239){	
		document.getElementById("sunshine").innerHTML="4.31";											//葫芦岛
	}else if(sunshine==240){	
		document.getElementById("sunshine").innerHTML="4.32";											//锦州
	}else if(sunshine==241){	
		document.getElementById("sunshine").innerHTML="4.18";											//辽阳
	}else if(sunshine==242){	
		document.getElementById("sunshine").innerHTML="4.25";											//盘锦
	}else if(sunshine==243){	
		document.getElementById("sunshine").innerHTML="4.18";											//沈阳
	}else if(sunshine==244){	
		document.getElementById("sunshine").innerHTML="4.17";											//铁岭
	}else if(sunshine==245){	
		document.getElementById("sunshine").innerHTML="4.28";											//营口
	//内蒙古
	}else if(sunshine==247){	
		document.getElementById("sunshine").innerHTML="4.64";											//阿拉善盟
	}else if(sunshine==248){	
		document.getElementById("sunshine").innerHTML="4.5";											//巴彦淖尔
	}else if(sunshine==249){	
		document.getElementById("sunshine").innerHTML="4.58";											//包头
	}else if(sunshine==250){	
		document.getElementById("sunshine").innerHTML="4.36";											//赤峰
	}else if(sunshine==251){	
		document.getElementById("sunshine").innerHTML="4.61";											//鄂尔多斯
	}else if(sunshine==252){	
		document.getElementById("sunshine").innerHTML="5.57";											//呼和浩特
	}else if(sunshine==253){	
		document.getElementById("sunshine").innerHTML="3.83";											//通辽
	}else if(sunshine==254){	
		document.getElementById("sunshine").innerHTML="4.32";											//通辽
	}else if(sunshine==255){	
		document.getElementById("sunshine").innerHTML="4.6";											//乌海
	}else if(sunshine==256){	
		document.getElementById("sunshine").innerHTML="4.53";											//乌兰察布
	}else if(sunshine==257){	
		document.getElementById("sunshine").innerHTML="4.33";											//锡林郭勒
	}else if(sunshine==258){	
		document.getElementById("sunshine").innerHTML="4.07";											//兴安盟
	//青海
	}else if(sunshine==260){	
		document.getElementById("sunshine").innerHTML="4.59";											//果洛
	}else if(sunshine==261){	
		document.getElementById("sunshine").innerHTML="4.79";											//海北
	}else if(sunshine==262){	
		document.getElementById("sunshine").innerHTML="4.48";											//海东
	}else if(sunshine==263){	
		document.getElementById("sunshine").innerHTML="4.79";											//海南
	}else if(sunshine==264){	
		document.getElementById("sunshine").innerHTML="4.56";											//黄南
	}else if(sunshine==265){	
		document.getElementById("sunshine").innerHTML="5.01";											//海西
	}else if(sunshine==266){	
		document.getElementById("sunshine").innerHTML="5.45";											//西宁
	}else if(sunshine==267){	
		document.getElementById("sunshine").innerHTML="4.79";											//玉树
	//山东
	}else if(sunshine==269){	
		document.getElementById("sunshine").innerHTML="4.29";											//滨州
	}else if(sunshine==270){	
		document.getElementById("sunshine").innerHTML="4.32";											//德州
	}else if(sunshine==271){	
		document.getElementById("sunshine").innerHTML="4.29";											//东营
	}else if(sunshine==272){	
		document.getElementById("sunshine").innerHTML="4.2";											//菏泽
	}else if(sunshine==273){	
		document.getElementById("sunshine").innerHTML="4.29";											//济南
	}else if(sunshine==274){	
		document.getElementById("sunshine").innerHTML="4.21";											//济宁
	}else if(sunshine==275){	
		document.getElementById("sunshine").innerHTML="4.29";											//莱芜
	}else if(sunshine==276){	
		document.getElementById("sunshine").innerHTML="4.27";											//聊城
	}else if(sunshine==277){	
		document.getElementById("sunshine").innerHTML="4.24";											//临沂
	}else if(sunshine==278){	
		document.getElementById("sunshine").innerHTML="4.23";											//青岛
	}else if(sunshine==279){	
		document.getElementById("sunshine").innerHTML="4.16";											//日照
	}else if(sunshine==280){	
		document.getElementById("sunshine").innerHTML="4.29";											//泰安
	}else if(sunshine==281){	
		document.getElementById("sunshine").innerHTML="4.4";											//威海
	}else if(sunshine==282){	
		document.getElementById("sunshine").innerHTML="4.25";											//潍坊
	}else if(sunshine==283){	
		document.getElementById("sunshine").innerHTML="4.31";											//烟台
	}else if(sunshine==284){	
		document.getElementById("sunshine").innerHTML="4.12";											//枣庄
	}else if(sunshine==285){	
		document.getElementById("sunshine").innerHTML="4.27";											//淄博
	//山西
	}else if(sunshine==287){	
		document.getElementById("sunshine").innerHTML="4.33";											//长治
	}else if(sunshine==288){	
		document.getElementById("sunshine").innerHTML="4.55";											//大同
	}else if(sunshine==289){	
		document.getElementById("sunshine").innerHTML="4.18";											//晋城
	}else if(sunshine==290){	
		document.getElementById("sunshine").innerHTML="4.46";											//晋中
	}else if(sunshine==291){	
		document.getElementById("sunshine").innerHTML="4.33";											//临汾
	}else if(sunshine==292){	
		document.getElementById("sunshine").innerHTML="4.46";											//吕梁
	}else if(sunshine==293){	
		document.getElementById("sunshine").innerHTML="4.53";											//朔州
	}else if(sunshine==294){	
		document.getElementById("sunshine").innerHTML="4.83";											//太原
	}else if(sunshine==295){	
		document.getElementById("sunshine").innerHTML="4.45";											//忻州
	}else if(sunshine==296){	
		document.getElementById("sunshine").innerHTML="4.41";											//阳泉
	}else if(sunshine==297){	
		document.getElementById("sunshine").innerHTML="4.11";											//运城
	//陕西
	}else if(sunshine==299){	
		document.getElementById("sunshine").innerHTML="3.59";											//安康
	}else if(sunshine==300){	
		document.getElementById("sunshine").innerHTML="3.87";											//宝鸡
	}else if(sunshine==301){	
		document.getElementById("sunshine").innerHTML="3.76";											//汉中
	}else if(sunshine==302){	
		document.getElementById("sunshine").innerHTML="3.8";											//商洛
	}else if(sunshine==303){	
		document.getElementById("sunshine").innerHTML="4.13";											//铜川
	}else if(sunshine==304){	
		document.getElementById("sunshine").innerHTML="4";												//渭南
	}else if(sunshine==305){	
		document.getElementById("sunshine").innerHTML="3.93";											//西安
	}else if(sunshine==306){	
		document.getElementById("sunshine").innerHTML="3.93";											//咸阳
	}else if(sunshine==307){	
		document.getElementById("sunshine").innerHTML="4.32";											//延安
	}else if(sunshine==308){	
		document.getElementById("sunshine").innerHTML="4.6";											//榆林
	//西藏
	}else if(sunshine==310){	
		document.getElementById("sunshine").innerHTML="5.65";											//阿里
	}else if(sunshine==311){	
		document.getElementById("sunshine").innerHTML="4.91";											//昌都
	}else if(sunshine==312){	
		document.getElementById("sunshine").innerHTML="5.53";											//拉萨
	}else if(sunshine==313){	
		document.getElementById("sunshine").innerHTML="4.81";											//林芝
	}else if(sunshine==314){	
		document.getElementById("sunshine").innerHTML="5.11";											//那曲
	}else if(sunshine==315){	
		document.getElementById("sunshine").innerHTML="5.16";											//日喀则
	}else if(sunshine==316){	
		document.getElementById("sunshine").innerHTML="5.53";											//山南
	//四川
	}else if(sunshine==318){	
		document.getElementById("sunshine").innerHTML="4.63";											//阿坝
	}else if(sunshine==319){	
		document.getElementById("sunshine").innerHTML="3.24";											//巴中
	}else if(sunshine==320){	
		document.getElementById("sunshine").innerHTML="2.88";											//成都
	}else if(sunshine==321){	
		document.getElementById("sunshine").innerHTML="3.24";											//达州
	}else if(sunshine==322){	
		document.getElementById("sunshine").innerHTML="3.2";											//德阳
	}else if(sunshine==323){	
		document.getElementById("sunshine").innerHTML="4.71";											//甘孜
	}else if(sunshine==324){	
		document.getElementById("sunshine").innerHTML="3.12";											//广安
	}else if(sunshine==325){	
		document.getElementById("sunshine").innerHTML="3.43";											//广元
	}else if(sunshine==326){	
		document.getElementById("sunshine").innerHTML="3.1";											//乐山
	}else if(sunshine==327){	
		document.getElementById("sunshine").innerHTML="4.54";											//凉山
	}else if(sunshine==328){	
		document.getElementById("sunshine").innerHTML="3.06";											//泸州
	}else if(sunshine==329){	
		document.getElementById("sunshine").innerHTML="3.26";											//眉山
	}else if(sunshine==330){	
		document.getElementById("sunshine").innerHTML="3.2";											//绵阳
	}else if(sunshine==331){	
		document.getElementById("sunshine").innerHTML="3.14";											//内江
	}else if(sunshine==332){	
		document.getElementById("sunshine").innerHTML="3.12";											//南充
	}else if(sunshine==333){	
		document.getElementById("sunshine").innerHTML="4.83";											//攀枝花
	}else if(sunshine==334){	
		document.getElementById("sunshine").innerHTML="3.22";											//遂宁
	}else if(sunshine==335){	
		document.getElementById("sunshine").innerHTML="3.75";											//雅安
	}else if(sunshine==336){	
		document.getElementById("sunshine").innerHTML="3.15";											//宜宾
	}else if(sunshine==337){	
		document.getElementById("sunshine").innerHTML="3.18";											//资阳
	}else if(sunshine==338){	
		document.getElementById("sunshine").innerHTML="3.15";											//自贡
	//新疆
	}else if(sunshine==340){	
		document.getElementById("sunshine").innerHTML="4.44";											//阿克苏
	}else if(sunshine==341){	
		document.getElementById("sunshine").innerHTML="4.13";											//阿勒泰
	}else if(sunshine==342){	
		document.getElementById("sunshine").innerHTML="4.43";											//巴音郭楞
	}else if(sunshine==343){	
		document.getElementById("sunshine").innerHTML="4.08";											//博尔塔拉
	}else if(sunshine==344){	
		document.getElementById("sunshine").innerHTML="4.09";											//昌吉
	}else if(sunshine==345){	
		document.getElementById("sunshine").innerHTML="4.54";											//哈密
	}else if(sunshine==346){	
		document.getElementById("sunshine").innerHTML="4.83";											//和田
	}else if(sunshine==347){	
		document.getElementById("sunshine").innerHTML="4.54";											//喀什
	}else if(sunshine==348){	
		document.getElementById("sunshine").innerHTML="4";												//克拉玛依	
	}else if(sunshine==349){	
		document.getElementById("sunshine").innerHTML="4.74";											//克孜勒苏
	}else if(sunshine==350){	
		document.getElementById("sunshine").innerHTML="4.03";											//塔城
	}else if(sunshine==351){	
		document.getElementById("sunshine").innerHTML="4.46";											//吐鲁番
	}else if(sunshine==352){	
		document.getElementById("sunshine").innerHTML="4.2";											//乌鲁木齐
	}else if(sunshine==353){	
		document.getElementById("sunshine").innerHTML="4.1";											//伊犁
	}else if(sunshine==354){	
		document.getElementById("sunshine").innerHTML="4.01";											//石河子
	}else if(sunshine==355){	
		document.getElementById("sunshine").innerHTML="4.47";											//阿拉尔
	}else if(sunshine==356){	
		document.getElementById("sunshine").innerHTML="4.59";											//图木舒克
	}else if(sunshine==357){	
		document.getElementById("sunshine").innerHTML="4.09";											//五家渠
	//云南
	}else if(sunshine==359){	
		document.getElementById("sunshine").innerHTML="4.59";											//保山
	}else if(sunshine==360){	
		document.getElementById("sunshine").innerHTML="4.82";											//楚雄
	}else if(sunshine==361){	
		document.getElementById("sunshine").innerHTML="4.76";											//大理
	}else if(sunshine==362){	
		document.getElementById("sunshine").innerHTML="4.54";											//德宏
	}else if(sunshine==363){	
		document.getElementById("sunshine").innerHTML="4.42";											//迪庆
	}else if(sunshine==364){	
		document.getElementById("sunshine").innerHTML="4.61";											//红河
	}else if(sunshine==365){	
		document.getElementById("sunshine").innerHTML="4.75";											//昆明
	}else if(sunshine==366){	
		document.getElementById("sunshine").innerHTML="4.78";											//丽江
	}else if(sunshine==367){	
		document.getElementById("sunshine").innerHTML="4.59";											//临沧
	}else if(sunshine==368){	
		document.getElementById("sunshine").innerHTML="4.41";											//怒江
	}else if(sunshine==369){	
		document.getElementById("sunshine").innerHTML="4.55";											//普洱
	}else if(sunshine==370){	
		document.getElementById("sunshine").innerHTML="4.52";											//曲靖
	}else if(sunshine==371){	
		document.getElementById("sunshine").innerHTML="4.27";											//文山
	}else if(sunshine==372){	
		document.getElementById("sunshine").innerHTML="4.67";											//西双版纳
	}else if(sunshine==373){	
		document.getElementById("sunshine").innerHTML="4.69";											//玉溪
	}else if(sunshine==374){	
		document.getElementById("sunshine").innerHTML="3.1";											//昭通
	//北京
	}else if(sunshine==376){	
		document.getElementById("sunshine").innerHTML="4.32";											//北京
	//上海
	}else if(sunshine==378){	
		document.getElementById("sunshine").innerHTML="3.81";											//上海
	//天津
	}else if(sunshine==380){	
		document.getElementById("sunshine").innerHTML="4.37";											//天津
	//重庆
	}else if(sunshine==382){	
		document.getElementById("sunshine").innerHTML="3.06";											//重庆
	}
}