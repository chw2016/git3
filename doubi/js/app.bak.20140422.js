	var clicktag = 0;	
	Zepto(function($) {	
		//处理移动端 click 事件 300 毫秒延迟		
		FastClick.attach(document.body);
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
		
		
		//点击领取
		$("#consume > li").tap(function(){			
			//防止反复提交,时间限制2秒内
			if(clicktag==1){								
				return ;				
			}			
			if(clicktag==0){
				clicktag=1;
				setTimeout(function () { clicktag = 0 }, 1500); 
			}			
		
		});
			
		
        
    }); 