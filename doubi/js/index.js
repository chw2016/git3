	var clicktag = 0;	
	Zepto(function($) {	
		//处理移动端 click 事件 300 毫秒延迟		
		FastClick.attach(document.body);
		//点击领取
		$(".lingquan .lq").tap(function(){			
			//防止反复提交,时间限制2秒内
			if(clicktag==1){								
				return ;				
			}			
			if(clicktag==0){
				clicktag=1;
				setTimeout(function () { clicktag = 0 }, 1500); 
			}
			
			var ccid = $(this).attr('ccid'); 
			var quantity = $(this).attr('quantity'); 
			var merchantid = $(this).attr('merchantid'); 
			
			var openId = $("#openId").val();
			var deviceCode = $("#deviceCode").val();
			var isSuc= false;
			  
			$.ajax({
			  type: 'POST',
			  url: 'insert_doubi.php',
			  data: {"user_id":openId,"merchant_id":merchantid,"card_config_id":ccid,"quantity":quantity,"device_code":deviceCode},
			  dataType: 'text',
			  timeout: 3000,
			  async:false,
			  success: function(data){
				  isSuc = true;							
					if(data == '1'){
						var sSpan = $("#surId"+ccid);
						var vid = sSpan.attr('vid');
						var curVid = parseInt(vid)+1;
						sSpan.html('已领'+curVid+'次');
					}
			  },
			  error: function(xhr, type){		
				  isSuc = false;							  
				
			  }
			});	
			
			
			//领取成功
			if(isSuc){
				$.dialog({
                    content : '已领取，请到逗币中心消费',
                    title: "ok",					
                    time : 1000,
					lock:true
				});
				
			}else {
				$.dialog({
                    content : '未领取成功，请重新领取',
                    title: "ok",					
                    time :1000,
					lock:true
				});
				
			}	
		
		});
			
		
        
    }); 