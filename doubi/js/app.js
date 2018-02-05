	var clicktag = 0;
	Zepto(function($) { 
	
		//处理移动端 click 事件 300 毫秒延迟		
		FastClick.attach(document.body);
		//默认值
		$(".feilei .cash").first().addClass('this'); //已修改20160317(默认选择第一个元素) kevin
		//默认选择设备
		var defaultCode=$("#default_code").val();		
		$("#consume > li[dno='"+defaultCode+"']").addClass("lion");

		//消费
		$("#consume > li").tap(function(){
			//防止反复提交,时间限制2秒内
			if(clicktag==1){								
				return ;				
			}			
			if(clicktag==0){
				clicktag=1;
				setTimeout(function () { clicktag = 0 }, 1500); 
			}	

			//设置默认样式						
			$("#consume > li").removeClass('lion');
			$(this).addClass('lion');			

			var cPrice = $("#balances").html();
			var price = $(this).attr('dprice');
			price = parseInt(price);
			if(parseInt(cPrice)<price){
				$.dialog({
                    content : '请领取逗币再启动',
                    title: "ok",
					width: 600,
                    time : 2000
                });					
				return ;
			}			
			
			var device_code = $(this).attr('dno');
			var device_id = $(this).attr('did');
			var nos = $(this).attr('cd');
			var dataid = $(this).attr('dataid');
			var dataid_price = parseInt(dataid);
			var message="201:AAAAA4";
			
			//2016-03-17 chw 单一的发送指令修改成根据价格动态的发送指令
			if(dataid_price==1){
				message+="01";
			}else if(dataid_price==10){
				message+="A1";
			}else{
				message+=(dataid_price+"1");
			}
			
			if(dataid_price >10){
				$.dialog({
                    content : nos+'设备智能价格不支持，请启动其他设备',
                    title: "ok",
					width: 600,
                    time : 2000
                });					
				return ;
				
			}

			var payInfoId = true;	//pay_info id		
			var isOnline= true;	
			//获取在线状态及pay_info的id
			$.ajax({
			  type: 'POST',
			  url: 'deviceonline.php',
			  data: {"device_code":device_code},
			  dataType: 'text',
			  timeout: 3000,
			  async:false,
			  success: function(data){
				  if(data == 1){
						payInfoId = false;	
						isOnline = false;
						return ;
				  }					  		  
					//pay_info 的id
					payInfoId = data;
				 		  
			  },
			  error: function(xhr, type){
				isOnline = false;	
			  }
			});	
			
			if(isOnline==false){				
				$.dialog({
                    content : nos+'设备智能服务临时维护中，请启动其他设备',
                    title: "ok",
					width: 600,
                    time : 2000
                });					
				return ;
			}		
			
			
				//在线发送指令
				var sendSuc = false;
				
				if(payInfoId){					
					var send_url = "http://120.24.81.106:3030/IntelligenceServer2/cgi/message_send.action";
					
					//var send_url = "http://10.169.116.160:3030/IntelligenceServer2/cgi/message_send.action";
					
					$.ajax({
					  type: 'POST',
					  url: send_url,
					  data: {datas:message,deviceId:device_code,transCode:"601",commandId:$.trim(payInfoId)},
					  dataType: 'json',
					  async : false,					  
					  success: function(data){
						if(data.code == 200){
							$.dialog({
								content : '设备已启动',
								title: "ok",
								width: 600,
								time : 2000
							});
							sendSuc = true;
							var curPrice = $("#balances").html();//余额
							var nedPrice = parseInt(curPrice)-(price);//扣除余额，以前是固定值1元，现在是根据价格动态扣除 2016-03-17 chw
							$("#balances").html(""+nedPrice); 	//启动后显示余额
											
							
						} else {
							$.dialog({
								content : '启动设备错误，请重新启动',
								title: "alert",
								width: 600,
								time : 2000
							});
							
						}
					  },
					  error: function(xhr, type,error){						
						$.dialog({
							content : '启动设备错误，请重新启动',
							title: "alert",
							width: 600,
							time : 2000
						});
					  }
					});
					var sprice = price;
					//发送成功，添加消费记录
					if(sendSuc){						
						$.post("consumpayinfos.php", 
							{"device_code":device_code,"device_id":device_id,
							"open_id":$("#openId").val(),price:sprice},
						   function(data){ 								
						   }
						);	
					}
				}
			});
    }); 