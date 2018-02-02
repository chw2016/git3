var xtztr1 = null;
function checkXtztr(text)
{
    var anzhuangrongliang=null;
    var daikuanleixing =null;
    if($(".rl").val()){
        anzhuangrongliang = $(".rl").val();
    }
    var wudingleixing = null;
    var wdStyle=$(".wudingStyle ");//屋顶类型
    $.each(wdStyle,function(o,i){
        if($(this).hasClass("on")){
            wudingleixing=$(this).attr("msg");
        }
    })

    var dkStyle=$(".moneyStyle ");//屋顶类型
    $.each(dkStyle,function(o,i){
        if($(this).hasClass("on")){
            daikuanleixing=$(this).attr("msg");
        }
    })
    var keyongmianji = $(".rongliang").val();
    if(wudingleixing==1)
    {
        if(isNaN(keyongmianji))
        {
            alert("请输入数字");
        }else{


            var rl = anzhuangrongliang;
            if(rl>0&&rl<=50)
            {
                xtztr1 = rl*8.5/10;
            }else if(rl>50&&rl<=200)
            {
                xtztr1 =rl*8/10;
            }else if (rl>200)
            {
                xtztr1 =rl*8/10;
            }
        }
    }else {
        if(isNaN(keyongmianji))
        {
            alert("请输入数字");
        }else{
            var rl = anzhuangrongliang;
            if(rl>0&&rl<=50)
            {
                xtztr1 =rl*8.5/10;
            }else if(rl>50&&rl<=200)
            {
                xtztr1 =rl*8/10;
            }else if (rl>200)
            {
                xtztr1 =rl*8/10;
            }
        }
    }
    if(text == 1) {
//这里修改了
       if (daikuanleixing == 1) {
            daikuanleixing = 2;
        }else{
           daikuanleixing=1
       }

    }
    xtztr1 = parseFloat(xtztr1);
    var daikuan = document.getElementById("daikuan").value;
    var bili = document.getElementById("daikuan").value;								//贷款比例
    var lilv = document.getElementById("lilv").value;									//贷款利率
    var lilv = lilv/100;
    var nianxian = document.getElementById("nianxian").value;							//贷款年限
    var daikuanbenjin = bili*xtztr1;													//贷款本金

    var a = daikuanbenjin/100;
    var i = lilv/12;
    var n = nianxian*12;
    /*等额本金*/
    var n1 = parseFloat(1)+parseFloat(n);
    xtztr1 = xtztr1;
    /*等额本息*/
    var k = parseFloat(1)+parseFloat(i);
    var k1 = Math.pow(k,n);
    k1 = k1.toString();
    k1 = k1.substr(0,15);
    k1 = parseFloat(k1);
    var k2 = Math.pow(k,n);
    k2 = k2.toString();
    k2 = k2.substr(0,15);
    k2 = parseFloat(k2);
    k2 = parseFloat(k2)-parseFloat(1);
    if(daikuan>0)
    {

        if(daikuanleixing==1)
        {
            if(n==0)
            {
                document.getElementById("xtztr").innerHTML=xtztr;
            }
            var de1 = n1*a*i/2;
            var de = parseFloat(xtztr1)+parseFloat(de1);

            document.getElementById("xtztr").innerHTML=de.toFixed(3);
        }
        if(daikuanleixing==2)
        {
            if(n==0)
            {
                document.getElementById("xtztr").innerHTML=xtztr;
            }
            var k3 = k1/k2;
            var del = (n*a*i*(k1/k2))-parseFloat(a);
            var de=parseFloat(xtztr1)+parseFloat(del);

            document.getElementById("xtztr").innerHTML=de.toFixed(3);

        }
    }
   xtztr1 = $("#xtztr").text();
}
                function getData()
                    {

                       /* var nianxian=$("#nianxian").val();
                        if(nianxian=='0'){
                          //  alert('请输入正确的贷款年限');
                            $(".resultBox").hide();
                            $("#nianxian").focus();
                            return false;
                        }*/
                        keyongmianji = $(".rongliang").val();

                        check();
                        sum();
                        getJs();
                        getJs1();
                        IRR();
                    }


                    function check()
                    {
                        var c1= $('select#area-1 option:selected').val();
                        var c2=$('select#area-2 option:selected').val();
                        var c3=keyongmianji;
                        var c4=$("#dianjia").val().trim();
                        var c5=(keyongmianji*66/1000).toFixed(0);
                        if(c1=="")
                        {
                            alert("请选择省份");
                            flag = false;
                            return false;
                        }else if(c2=="")
                        {
                            alert("请选择地区");
                            flag = false;
                            return false;
                        }else if(c4=="") {
                            alert("请输入电价");
                            $("#dianjia").focus();
                            flag = false;
                            return false;
                        }
			var bili = document.getElementById("daikuan").value;								//贷款比例
                        var nianxian = document.getElementById("nianxian").value;	
			if(bili > 0){
			  /* if(nianxian<=0){
			      alert('贷款年限不能为0');
                              flag = false;
                   $("#nianxian").focus();
			      return false;
			   }*/

			}
			
			if(bili > 100 || bili < 0){
			      alert('比例应该为0-100之间的数字');
                              flag = false;
			      return false;	
			}

                    }
			function sum()
			{
				//document.getElementById("25years").style.display="";
                //
				////显示
				//	document.getElementById("25years").style.display="";
				//	document.getElementById("hidden").style.display="";
				//	document.getElementById("hidden1").style.display="";
				//	document.getElementById("hidden2").style.display="";
				//	document.getElementById("25yearsT1").style.display="";
				//	document.getElementById("25yearsT2").style.display="";
				//	document.getElementById("25yearsT3").style.display="";
				//	document.getElementById("main").style.display="block";
				//	document.getElementById("line").style.display="block";
				//	document.getElementById("hjxyb").style.display="";
				//	document.getElementById("bz1").style.display="";
				//	document.getElementById("bz2").style.display="";
				//	document.getElementById("bz3").style.display="";
				//	document.getElementById("bz4").style.display="";

                var wudingleixing = null;
                var wdStyle=$(".wudingStyle ");//屋顶类型
                $.each(wdStyle,function(o,i){
                    if($(this).hasClass("on")){
                        wudingleixing=$(this).attr("msg");
                    }
                })
				//获得计算数据
											//获得屋顶类型simple
                if(!wudingleixing){
                    wudingleixing= localStorage.getItem("wudingleixing");
                }

				var wudingkeyongmianji = keyongmianji;													//获得屋顶面积
                if(wudingleixing==1) {
                    var anzhuangrongliang = (wudingkeyongmianji * 66 / 1000).toFixed(0);
                }else{
                    var anzhuangrongliang = (wudingkeyongmianji *100/1000).toFixed(0);
                }
                if($(".rl").val()){
                    anzhuangrongliang = $(".rl").val();
                }else{
                    alert("安装容量不可以为空");
                    $(".rl").focus();
                    flag = false;
                    return false;
                }
				//计算系统容量钱
				if(anzhuangrongliang>0&&anzhuangrongliang<=50)
				{
					var money = 10;
				}else if(anzhuangrongliang>50&&anzhuangrongliang<=200)
				{
					var money = 9;
				}else if(anzhuangrongliang>200)
				{
					var money = 8;
				}
				var ziyongbili =document.getElementById("bili").value;															//*自用比例
				var dianjia =document.getElementById("dianjia").value;															//*电价
                var daikuleixing=$(".moneyStyle");
                var dengebenjin=1;

                var chaoxiang = null;




                var pStyle=$(".directionStyle");//朝向
                $.each(pStyle,function(o,i){
                    if($(this).hasClass("on")){
                        chaoxiang=$(this).attr("msg");
                    }
                })
                if(!chaoxiang){
                    chaoxiang=localStorage.getItem("chaoxiang");
                }
												//*朝向
                var tuoliu = document.getElementById("tuoliu").innerHTML;														//*脱硫
				var sunshine = document.getElementById("sunshine").innerHTML;													//*日照小时
                var yunweichengben = document.getElementById("ywcb").value;														//运维成本
				var guojiabutie=document.getElementById("guojiabutie").value;													//*国家补贴
				var gjbtnx = document.getElementById("butienianxian1").value;													//国家补贴年限
				var difangbutie = document.getElementById("difangbutie").value;													//获得地方补贴
				var butienianxian = document.getElementById("butienianxian").value;												//获得补贴年限
				//计算
				var a = parseFloat(dianjia)+parseFloat(guojiabutie);														//地方补贴没有
				var a1 = parseFloat(dianjia)+parseFloat(guojiabutie)+parseFloat(difangbutie);								//地方补贴有
				var b = parseFloat(tuoliu)+parseFloat(guojiabutie)+parseFloat(difangbutie);
				var c = ziyongbili/100;																				<!--自用比例-->
                var nianfadianliang=10000;//测试



				var guangfudianjia = c*parseFloat(a)+(1-c)*parseFloat(b);											//光伏电价
				var guangfudianjia1 = c*parseFloat(a1)+(1-c)*parseFloat(b);											//光伏电价-有地方补贴
				var fadianshouyi = nianfadianliang*guangfudianjia;													//发电收益
				var yunyingchengbenzhanbi = parseFloat(yunweichengben);												//运营成本占比
				var chengben = 	fadianshouyi*yunyingchengbenzhanbi;													//成本
				if(chaoxiang=="n")
				{
					var zibenxianjinliu1 = 0-money*anzhuangrongliang*1000;												//资本现金流。第0年
					var leijixianjinliu1 = zibenxianjinliu1;															//累计现金流动。第0年
                    var nianfadianliang = anzhuangrongliang*sunshine*0.8*365;													//年发电量

				}else if(chaoxiang=="dn")
				{
					var zibenxianjinliu1 = 0-money*anzhuangrongliang*1000*0.95;												//资本现金流。第0年
					var leijixianjinliu1 = zibenxianjinliu1;															//累计现金流动。第0年
					var nianfadianliang = anzhuangrongliang*sunshine*0.8*365*0.95;													//年发电量
				}else if(chaoxiang=="d")
				{
					var zibenxianjinliu1 = 0-money*anzhuangrongliang*1000*0.8;												//资本现金流。第0年
					var leijixianjinliu1 = zibenxianjinliu1;															//累计现金流动。第0年
					var nianfadianliang = anzhuangrongliang*sunshine*0.8*365*0.8;													//年发电量
				}else if(chaoxiang=="xn")
				{
					var zibenxianjinliu1 = 0-money*anzhuangrongliang*1000*0.95;												//资本现金流。第0年
					var leijixianjinliu1 = zibenxianjinliu1;															//累计现金流动。第0年
					var nianfadianliang = anzhuangrongliang*sunshine*0.8*365*0.95;													//年发电量
				}else if(chaoxiang=="x")
				{
					var zibenxianjinliu1 = 0-money*anzhuangrongliang*1000*0.8;												//资本现金流。第0年
					var leijixianjinliu1 = zibenxianjinliu1;															//累计现金流动。第0年
					var nianfadianliang = anzhuangrongliang*sunshine*0.8*365*0.8;													//年发电量
				}else if(chaoxiang=="pp")
				{
					var zibenxianjinliu1 = 0-money*anzhuangrongliang*1000*0.9;												//资本现金流。第0年
					var leijixianjinliu1 = zibenxianjinliu1;															//累计现金流动。第0年
					var nianfadianliang = anzhuangrongliang*sunshine*0.8*365*0.9;													//年发电量
				}

				var zibenxianjinliu = fadianshouyi - chengben;														//资本现金流。第1年
				var leijixianjinliu = leijixianjinliu1 + zibenxianjinliu;											//累计现金流。第一年
                var lj1 = null;
                if(wudingleixing==1)
                {

                        var rl = anzhuangrongliang;
                        if(rl>0&&rl<=50)
                        {
                            var xtztr1 = rl*8.5/10;
                        }else if(rl>50&&rl<=200)
                        {
                            var xtztr1 =rl*8/10;
                        }else if (rl>200)
                        {
                            var xtztr1 =rl*8/10;
                        }

                }else {

                        var rl = anzhuangrongliang;
                        if(rl>0&&rl<=50)
                        {
                            var xtztr1 =rl*8.5/10;
                        }else if(rl>50&&rl<=200)
                        {
                            var xtztr1=rl*8/10;
                        }else if (rl>200)
                        {
                            var xtztr1 =rl*8/10;
                        }


                }

				//输出25年表
                xtztr2 = $("#xtztr").text();
                if(xtztr2){
                    lj1 = xtztr2;
                }else if(xtztr1) {
                    lj1 = xtztr1;                                           //系统总投入;
                }else{
	            lj1 = localStorage.getItem("xtztr");      
		}
                //alert(lj1);
                $("#ztr").text(lj1);//系统总投入````加的

                localStorage.setItem('xtztr',lj1);
				document.getElementById("06").innerHTML=lj1*10000*(-1);								//资本现金0
				document.getElementById("05").innerHTML=lj1*10000*(-1);								//累计现金0
				//第一行年发电量
				for(i=1;i<=25;i++)
				{
					document.getElementById(i+"0").innerHTML=nianfadianliang.toFixed(2);
					nianfadianliang=nianfadianliang*0.996;
					if(i==25)
					{
						var s = 0;
						for(k=1;k<=25;k++)
						{
							var n = document.getElementById(k+"0").innerHTML;
							 s =parseFloat(s)+parseFloat(n);
						}document.getElementById("sum").innerHTML=s.toFixed(2);
                        $("#zfdl").text($("#sum").text());//加的
							break;

					break;
					}
				}
				//自用比例
				var c = ziyongbili/100;
				var k1 = parseFloat(dianjia)+parseFloat(guojiabutie)+parseFloat(difangbutie);					//当地电价+国家补贴+地方补贴
				var k11= parseFloat(dianjia)+parseFloat(guojiabutie);											//没有地方补贴
				var k111= parseFloat(dianjia)+parseFloat(difangbutie);											//没有国家补贴

				var k2 = parseFloat(tuoliu)+parseFloat(guojiabutie)+parseFloat(difangbutie);					//脱硫+国家补贴+地方补贴
				var k22= parseFloat(tuoliu)+parseFloat(guojiabutie);											//没有地方补贴
				var k222=parseFloat(tuoliu)+parseFloat(difangbutie);											//没有国家补贴

				var none = c*dianjia+(1-c)*tuoliu;																//没有国家补贴，没有地方补贴
				//光复电价
				butienianxian = parseInt(butienianxian);
				gjbtnx = parseInt(gjbtnx);
				if(gjbtnx>0||butienianxian>0)
				{
						if(gjbtnx>butienianxian)
						{
							for(i=1;i<=butienianxian;i++)															//
							{
									var guangfudianjia = c*k1+(1-c)*k2;
									document.getElementById(i+"1").innerHTML=guangfudianjia.toFixed(3);		//待Break
									if(i==butienianxian)
									{
											butienianxian=++butienianxian;
											for(i=butienianxian;i<=gjbtnx;i++)
											{
													var guangfudianjia = c*k11+(1-c)*k22;
													document.getElementById(i+"1").innerHTML=guangfudianjia.toFixed(3);		//待Break
													if(i==gjbtnx)
													{
														gjbtnx = ++gjbtnx;
														for(i=gjbtnx;i<=25;i++)
														{
															document.getElementById(i+"1").innerHTML=none.toFixed(3);
														}
													}
											}
									}
							}
						}else if(gjbtnx<butienianxian)
						{
							for(i=1;i<=gjbtnx;i++)
							{
									var guangfudianjia = c*k1+(1-c)*k2;
									document.getElementById(i+"1").innerHTML=guangfudianjia.toFixed(3);		//待Break
									if(i>=gjbtnx)
									{
											gjbtnx = ++gjbtnx;
											for(i=gjbtnx;i<=butienianxian;i++)
											{
													var guangfudianjia = c*k111+(1-c)*k222;
													document.getElementById(i+"1").innerHTML=guangfudianjia.toFixed(3);		//待Break
													if(i==butienianxian)
													{
														butienianxian = ++butienianxian;
														for(i=butienianxian;i<=25;i++)
														{
															document.getElementById(i+"1").innerHTML=none.toFixed(3);
														}
													}
											}
									}
							}
						}
				}
				if(gjbtnx==0)
				{
						if(butienianxian==0)
						{
							for(i=1;i<=25;i++)
							{
								document.getElementById(i+"1").innerHTML=none.toFixed(3);
							}
						}
						for(i=1;i<=butienianxian;i++)
						{
							var guangfudianjia = c*k111+(1-c)*k222;
							document.getElementById(i+"1").innerHTML=guangfudianjia.toFixed(3);
							if(i>=butienianxian)
							{
								butienianxian = ++butienianxian;
								for(i=butienianxian;i<=25;i++)
								{
									document.getElementById(i+"1").innerHTML=none.toFixed(3);
								}
							}
						}

				}
				if(butienianxian==0)
				{

						if(gjbtnx==0)
						{
							for(i=1;i<=25;i++)
							{
								document.getElementById(i+"1").innerHTML=none.toFixed(3);
							}
						}
						for(i=1;i<=gjbtnx;i++)
						{
							var guangfudianjia = c*k11+(1-c)*k22;
							document.getElementById(i+"1").innerHTML=guangfudianjia.toFixed(3);
							if(i>=gjbtnx)
							{
								gjbtnx = ++gjbtnx;
								for(i=gjbtnx;i<=25;i++)
								{
									document.getElementById(i+"1").innerHTML=none.toFixed(3);
								}
							}
						}

				}
				if(gjbtnx==butienianxian)
				{
					for(i=1;i<=gjbtnx;i++)
					{
						var guangfudianjia = c*k1+(1-c)*k2;
						document.getElementById(i+"1").innerHTML=guangfudianjia.toFixed(3);														//待Break
						if(i>=gjbtnx)
						{
								gjbtnx = ++gjbtnx;
								for(i=gjbtnx;i<=25;i++)
								{
									document.getElementById(i+"1").innerHTML=none.toFixed(3);
								}
						}
					}
				}
				var gfdjsum = 0;
				for(i=1;i<=25;i++)
				{
					var gfdjsum1 = document.getElementById(i+"1").innerHTML;
					var gfdjsum = parseFloat(gfdjsum)+parseFloat(gfdjsum1);
					if(i==25)
					{
						var sum111 = gfdjsum/25;
						//document.getElementById("gfdj1").innerHTML=sum111.toFixed(3);
						break;
					}
				}
				//发电收益
				for(i=1;i<=25;i++)
				{
					if(i>25)
					{
						break;
					}

					var fdsy1 = document.getElementById(i+"0").innerHTML;
					var gfdj1 = document.getElementById(i+"1").innerHTML;
					var fdsy = document.getElementById(i+"2").innerHTML=(fdsy1*gfdj1).toFixed(2);
				}if(i=25)
				{
					var xxx=0;
					for(k=1;k<=25;k++)
						{
							if(k>25)
							{
								break;	
							}
							var n = document.getElementById(k+"2").innerHTML;
							xxx =parseFloat(xxx)+parseFloat(n);
						}document.getElementById("sum2").innerHTML=xxx.toFixed(3);	
				}
				//运营成本占比
				for(i=1;i<=25;i++)
				{
					if(i>25)
					{
						break;
					}
					document.getElementById(i+"3").innerHTML=yunyingchengbenzhanbi;
					//document.getElementById("sum3").innerHTML=yunyingchengbenzhanbi;
				}
				//运维成本yunweichengben
				for(i=1;i<=25;i++)
				{
					if(i>25)
					{
						break;	
					}
					var fdsy = document.getElementById(i+"2").innerHTML;
					var yycbzb = document.getElementById(i+"3").innerHTML;
					var ywcb = document.getElementById(i+"4").innerHTML=(fdsy*yycbzb/100).toFixed(2);
				}if(i=25)
				{
					var xxxx=0;
					for(k=1;k<=25;k++)
						{
							if(k>25)
							{
								break;
							}
							var n = document.getElementById(k+"4").innerHTML;
							xxxx =parseFloat(xxxx)+parseFloat(n);
						}
                    //document.getElementById("sum4").innerHTML=xxxx.toFixed(3);
				}
				//资本现金流
				for(i=1;i<=25;i++)
				{
					if(i>25)
					{
						break;	
					}
					var fdsy = document.getElementById(i+"2").innerHTML;
					var yycbzb = document.getElementById(i+"3").innerHTML;
					var ywcb = fdsy*yycbzb/100;
					var zbxjl = document.getElementById(i+"5").innerHTML=(parseFloat(fdsy)-parseFloat(ywcb)).toFixed(2);
				}if(i=25)
				{
					var xxxxx=0;
					for(k=1;k<=25;k++)
						{
							if(k>25)
							{
								break;	
							}
							var n = document.getElementById(k+"5").innerHTML;
							xxxxx =parseFloat(xxxxx)+parseFloat(n);
						}
				}
				//累计现金流

				var old = 0;
				for(i=1;i<=26;i++)
				{

					var kk = i-1;
					var up = document.getElementById(kk+"6").innerHTML;
					var zbxjll = document.getElementById(i+"5").innerHTML;
					var leijixianjin = parseFloat(up)+parseFloat(zbxjll);
					document.getElementById(i+"6").innerHTML=leijixianjin.toFixed(2);
					if(i==25)
					{
						var maxleiji = document.getElementById("256").innerHTML;
                        //alert(7);
						document.getElementById("fdzsr1").innerHTML=(maxleiji/10000).toFixed(3);
						break;
					}

				}
				//获得效益表数据
			document.getElementById("xtrl1").innerHTML=anzhuangrongliang;
			var yearsSum = document.getElementById("sum").innerHTML;
			document.getElementById("jybzm1").innerHTML=(yearsSum/1000*0.33).toFixed(3);
			var jpco = document.getElementById("jpco1").innerHTML=(yearsSum/1000*0.87).toFixed(3);
			document.getElementById("jpso1").innerHTML=(yearsSum/1000*9.3).toFixed(3);
			document.getElementById("jpnxoy1").innerHTML=(yearsSum/1000*2.3).toFixed(3);
			document.getElementById("jpyc1").innerHTML=(yearsSum/1000*0.5).toFixed(3);
			document.getElementById("zzsm1").innerHTML=parseInt(jpco);



			//计算收回年限
			for(i=1;i<=26;i++)
			{
				var z = document.getElementById(i+"6").innerHTML;
                z = parseFloat(z);
				if(z>0)
				{

					var c =i-1;
					var h13=document.getElementById(c+"6").innerHTML;
					var g14=document.getElementById(i+"5").innerHTML;
					var h13g14=(h13/g14).toFixed(3);
					document.getElementById("hsnx1").innerHTML=(c-h13g14).toFixed(2);
					break;
				}
			}

			}
<!--曲线图-->			
			function getJs()
			{
                        require.config({
                            paths:{
                                echarts:'/tpl/static/chart/echarts-map',
                                'echarts/chart/bar' : '/tpl/static/chart/echarts-map',
                                'echarts/chart/line': '/tpl/static/chart/echarts-map',
                                'echarts/chart/map' : '/tpl/static/chart/echarts-map'
                            }
                        });

                        require(
                            [
                                'echarts',
                                'echarts/chart/bar',
                                'echarts/chart/line',
                                'echarts/chart/map'
                            ],
                        function (ec) {
                            //--- 折柱 ---
                            var myChart = ec.init(document.getElementById('main'));
                            myChart.setOption({
                                tooltip : {
                                    trigger: 'axis'
                                },
                                //legend: {
                                //    data:['年发电量','运维成本']
                                //},
                                //toolbox: {
                                //    show : true,
                                //    feature : {
                                //        mark : {show: true},
                                //        dataView : {show: true, readOnly: false},
                                //        magicType : {show: true, type: ['line', 'bar']},
                                //        restore : {show: true},
                                //        saveAsImage : {show: true}
                                //    }
                                //},
                                calculable : true,
                                xAxis : [
                                    {
                                        type : 'category',
                                        data : [
                '第一年', '第二年', '第三年', '第四年', '第五年', '第六年', '第七年', '第八年', '第九年', '第十年', '第十一年', '第十二年', '第十三年', '第十四年', '第十五年', '第十六年', '第十七年', '第十八年', '第十九年', '第二十年', '第二十一年', '第二十二年', '第二十三年', '第二十四年', '第二十五年',
                                        ]
                                    }
                                ],
                                yAxis : [
                                    {
                                        type : 'value',
                                        splitArea : {show : true}
                                    }
                                ],
                                series : [
                                    {
                                        name:'年发电量',
                                        type:'bar',
                                        data:[
                document.getElementById("10").innerHTML,document.getElementById("20").innerHTML,document.getElementById("30").innerHTML,document.getElementById("40").innerHTML,document.getElementById("50").innerHTML,document.getElementById("60").innerHTML,document.getElementById("70").innerHTML,document.getElementById("80").innerHTML,document.getElementById("90").innerHTML,document.getElementById("100").innerHTML,document.getElementById("110").innerHTML,document.getElementById("120").innerHTML,document.getElementById("130").innerHTML,document.getElementById("140").innerHTML,document.getElementById("150").innerHTML,document.getElementById("160").innerHTML,document.getElementById("170").innerHTML,document.getElementById("180").innerHTML,document.getElementById("190").innerHTML,document.getElementById("200").innerHTML,document.getElementById("210").innerHTML,document.getElementById("220").innerHTML,document.getElementById("230").innerHTML,document.getElementById("240").innerHTML,document.getElementById("250").innerHTML,
                                        ]
                                    },
                                    {
                                        name:'运维成本',
                                        type:'bar',
                                        data:[
                document.getElementById("14").innerHTML,document.getElementById("24").innerHTML,document.getElementById("34").innerHTML,document.getElementById("44").innerHTML,document.getElementById("54").innerHTML,document.getElementById("64").innerHTML,document.getElementById("74").innerHTML,document.getElementById("84").innerHTML,document.getElementById("94").innerHTML,document.getElementById("104").innerHTML,document.getElementById("114").innerHTML,document.getElementById("124").innerHTML,document.getElementById("134").innerHTML,document.getElementById("144").innerHTML,document.getElementById("154").innerHTML,document.getElementById("164").innerHTML,document.getElementById("174").innerHTML,document.getElementById("184").innerHTML,document.getElementById("194").innerHTML,document.getElementById("204").innerHTML,document.getElementById("214").innerHTML,document.getElementById("224").innerHTML,document.getElementById("234").innerHTML,document.getElementById("244").innerHTML,document.getElementById("254").innerHTML,
                                        ]
                                    }
                                ]
                            });


                        }
                    );


			}
<!--减排js数据-->
	function getJs1()
	{
        // Step:3 conifg ECharts's path, link to echarts.js from current page.
        // Step:3 为模块加载器配置echarts的路径，从当前页面链接到echarts.js，定义所需图表路径
        require.config({
            paths:{
                echarts:'/tpl/static/chart/echarts-map',
                'echarts/chart/bar' : '/tpl/static/chart/echarts-map',
                'echarts/chart/line': '/tpl/static/chart/echarts-map',
                'echarts/chart/map' : '/tpl/static/chart/echarts-map'
            }
        });

        require(
            [
                'echarts',
                'echarts/chart/bar',
                'echarts/chart/line',
                'echarts/chart/map'
            ],
        function (ec) {
            //--- 折柱 ---
            var myChartline = ec.init(document.getElementById('line'));
            myChartline.setOption({
               title : {
					text: '',
					subtext: ''
				},
				tooltip : {
					trigger: 'axis'
				},
				//legend: {
				//	data:['累计现金流曲线']
				//},
				//toolbox: {
				//	show : true,
				//	feature : {
				//		mark : {show: true},
				//		dataView : {show: true, readOnly: false},
				//		magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
				//		restore : {show: true},
				//		saveAsImage : {show: true}
				//	}
				//},
				calculable : true,
				xAxis : [
					{
						type : 'category',
						boundaryGap : false,
						data : [
1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,
						]
					}
				],
				yAxis : [
					{
						type : 'value'
					}
				],
				series : [
					{
						name:'累计现金流曲线',
						type:'line',
						smooth:true,
						itemStyle: {normal: {areaStyle: {type: 'default'}}},
						data:[
document.getElementById("16").innerHTML,document.getElementById("26").innerHTML,document.getElementById("36").innerHTML,document.getElementById("46").innerHTML,document.getElementById("56").innerHTML,document.getElementById("66").innerHTML,document.getElementById("76").innerHTML,document.getElementById("86").innerHTML,document.getElementById("96").innerHTML,document.getElementById("106").innerHTML,document.getElementById("116").innerHTML,document.getElementById("126").innerHTML,document.getElementById("136").innerHTML,document.getElementById("146").innerHTML,document.getElementById("156").innerHTML,document.getElementById("166").innerHTML,document.getElementById("176").innerHTML,document.getElementById("186").innerHTML,document.getElementById("196").innerHTML,document.getElementById("206").innerHTML,document.getElementById("216").innerHTML,document.getElementById("226").innerHTML,document.getElementById("236").innerHTML,document.getElementById("246").innerHTML,document.getElementById("256").innerHTML,
                        ]
                    }
                ]
            });
            
           
        }
    );	
	}
