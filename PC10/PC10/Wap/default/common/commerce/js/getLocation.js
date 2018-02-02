function GetLocation(oneven,Html){
	var position_option = { enableHighAccuracy:true,maximumAge:30000,timeout:20000 };
	
	function getPositionSuccess( position )
	{
	        var lat = position.coords.latitude;
	        var lng = position.coords.longitude;
	        var acc = position.coords.accuracy;
        	//alert(acc);
			var mylocation =lat+","+lng;
			//alert(mylocation);
			Html.html('正在定位…');
			$.post('http://api.map.baidu.com/geocoder/v2/',{
				ak: '2WQAlmlNeRT29pY8vTqCN7kO',
				location:mylocation,
				output:'json'
				} ,
				 function(data) {
				//alert(data);
				if(data.status==0){
					var ncity = data.result.addressComponent.city//province;//formatted_address;
				//alert(data.result.addressComponent.district);
				// ncity+=","+ data.result.addressComponent.city;
				// ncity+=","+ data.result.addressComponent.district;
				Html.html(ncity);
                $("#latitude").val(lat);
                $("#longitude").val(lng);

				}else{
					Html.html('请重新获取。');
				}
			},"jsonp");
	} 

	function getPositionError(error)
	 {
	    switch (error.code)
	 {
	        case error.TIMEOUT:
	            alert("连接超时，请重试");
	            break;
	        case error.PERMISSION_DENIED:
	            alert("您拒绝了使用位置共享服务，查询已取消");
	            break;
	        case error.POSITION_UNAVAILABLE:
	            alert("获取位置信息失败");
	            break;
	    }
	}
	  //$(".getlocation")
	  oneven.click(function(){navigator.geolocation.getCurrentPosition(getPositionSuccess,getPositionError,position_option);
	    });
}