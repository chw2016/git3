	//调用微信JS api 支付
	function jsApiCall( jsApiParameters)
	{
		
		var jsPs = eval('(' + jsApiParameters + ')');
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			jsPs ,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				 if(res.err_msg == "get_brand_wcpay_request:ok" ){
					 //更新支付明细
					 //返回支付的汇总记录
					$.ajax({
					  type: 'POST',
					  url: 'payinfoupdate.php',
					  data: {"app_id":jsPs.appId,	
							"prepay_id":jsPs.package,
							"open_id":$("#openId").val()},
					  dataType: 'text',			  
					  async:false,
					  success: function(data){
						  $("#balances").html(data);			  
					  },
					  error: function(xhr, type){				
						$.dialog({
							content : '充值异常，请重新充值',
							title: "alert",
							width: 600,
							time : 2000
						});
					  }
					});					
											
				 }										 
			}
		);
	}
	
	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
			var jsApiParameters =  null;	
			var curPrice = $('#current_m').val();
			$.ajax({
			  type: 'POST',
			  url: 'jsApiParameters.php',
			  data: {"total_price":curPrice,"open_id":$("#openId").val()},
			  dataType: 'text',
			  timeout: 3000,
			  async:false,
			  success: function(data){
				  if(0 != data){
					jsApiParameters = data;	
				  }				  
			  },
			  error: function(xhr, type){				
				$.dialog({
                    content : '充值异常，请重新充值',
                    title: "alert",
					width: 600,
                    time : 2000
                });
			  }
			});	

			$.dialog({
                    content : '加载中...',
                    title: "load",					
                    time : 500
            });
			if(jsApiParameters != null){				
				 jsApiCall(jsApiParameters);
			}
			
		   
		}
	}
	var clicktag = 0;  
	
	Zepto(function($) { 
	
		//处理移动端 click 事件 300 毫秒延迟		
		FastClick.attach(document.body);
		
		
		//默认值
		$(".cash[sv='1']").addClass('this');
		
		//默认选择设备
		var defaultCode=$("#default_code").val();
		$("#consume > li[dno='"+defaultCode+"']").addClass("lion");
		
		//充值选择
		$(".cash").tap(
			function(){
				var sv = $(this).attr('sv'); 
				$("#current_m").val(sv);
				//样式				
				$(".cash").removeClass('this');
				$(this).addClass('this');				
			}
		);
		
		
		
		
		//支付
		$("#paymoney").tap(function(){
			callpay();		
		});
		
		//消费
		$("#consume > li").tap(function(){
			//判断是否需要充值
			var cPrice = $("#balances").html();
			//设置默认样式						
			$("#consume > li").removeClass('lion');
			$(this).addClass('lion');
			
			if(parseInt(cPrice)<=0){				
				$.dialog({
                    content : '请充值再启动',
                    title: "ok",
					width: 600,
                    time : 2000
                });				
				//alert("请充值再启动");
				return ;
			}
			var device_code = $(this).attr('dno');
			var nos = $(this).attr('cd');
			var message = "201:AAAAA401";
			
			
			
			var returns = true;
		
			//提交消费记录
			/*
			$.post("consumpayinfos.php", 
				{"app_id":$("#appId").val(),
				"device_code":device_code,
				"open_id":$("#openId").val()},			   
			   function(data){
				   if(data!='0'){
					var curPrice = $("#balances").html();
					var nedPrice = parseInt(curPrice)-1;
					$("#balances").html(""+nedPrice ); 
					returns = data;
				   } else {
					   returns = false;
				   }				
			   }
			);	
			*/
			
			var isOnline= true;			
			$.ajax({
			  type: 'POST',
			  url: 'consumpayinfos.php',
			  data: {"app_id":$("#appId").val(),"device_code":device_code,"open_id":$("#openId").val()},
			  dataType: 'text',
			  timeout: 3000,
			  async:false,
			  success: function(data){				
				  if(data == 1){
						returns = false;	
						isOnline = false;
						return ;
				  }				  
				  if(data == 0){
					  returns = false;
					  return ;					  
				  }				  
				 //设备正常
				var curPrice = $("#balances").html();
				var nedPrice = parseInt(curPrice)-1;
				$("#balances").html(""+nedPrice ); 
				returns = data;
				 		  
			  },
			  error: function(xhr, type){				
				$.dialog({
                    content : '启动设备错误，请重新启动',
                    title: "alert",
					width: 600,
                    time : 2000
                });
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
			
			//防止反复提交,时间限制2秒内
			if(clicktag==1){								
				return ;				
			}			
			if(clicktag==0){
				clicktag=1;
				setTimeout(function () { clicktag = 0 }, 2000); 
			}		
			
			
		
			if(returns){
				
				$.dialog({
                    content : '正在启动'+nos,
                    title: "ok",
					width: 600,
                    time : 3000
				});
			
			
				$.post("http://120.24.81.106:3030/IntelligenceServer2/cgi/message_send.action", 
					{datas:message,deviceId:device_code,transCode:"601",
					 commandId:returns},			   
				   function(data){     
						//returns= true;
				   }
				);
			}
		
			});
			
			
			 //加载余额	
			$.ajax({
			  type: 'POST',
			  url: 'getpayinfos.php',
			  data: {"app_id":$("#appId").val(),"open_id":$("#openId").val()},
			  dataType: 'text',
			  async:false,
			  success: function(data){
				  $("#balances").html(data);		  
			  },
			  error: function(xhr, type){				
				$.dialog({
                    content : '加载余额错误，请重新扫码',
                    title: "alert",
					width: 600,
                    time : 2000
                });
			  }
			});	
        
    }); 