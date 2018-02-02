$(function(){
	$(".phone input").blur(function(){
		if($(this).val()){
			var tel = $(this).val();
			var reg = /^0?1[3|4|5|8][0-9]\d{8}$/;
			if (reg.test(tel)) {
			    // $(".tips header").html("输入错误!");
			    // $(".tips .tipsContent").html("<div>您输入的手机号码不正确！</div>");
			}else{
			    var tipsContent = "<div>您输入的这个<span style='color:rgb(255,38,38);font-weight:600'>";
			    	tipsContent	+= tel;
			    	tipsContent	+= "</span>人家识别不了哦！</div>";
			    $(".tips footer").show();
			    $(".tips header").addClass('warning').html("输入错误!");
			    $(".tips .confirmTips").addClass('warning');
			    $(".tips .tipsContent").html(tipsContent);
			    $(".blur").addClass('tipShow');
			};
		}else{
			var tipsContent = "手机号码不能为空哦！"
			$(".tips header").html("对不起!");
		 	$(".tips header").addClass('warning');
			$(".tips .confirmTips").addClass('warning');
		    $(".tips .tipsContent").html(tipsContent);
		    $(".tips footer").hide();
		    $(".blur").addClass('tipShow');
		    setTimeout(function(){
		    	$(".blur").removeClass('tipShow');
		    },1000)
		}
	})
})

$(function(){
	/*这段代码是用于选择大米品类用的*/
	var a = 1;
	$(".selectMi").click(function(){
		if($("#selectMiList").is(":hidden")){
			$("#selectMiList").fadeIn(200);
			var miList   = new IScroll(".selectMiList",{click:true});
		}else{
			$("#selectMiList").fadeOut(200);
		}
	});
	
	
	$(".selectMiwm").click(function(){
		if($("#selectMiListwm").is(":hidden")){
			$("#selectMiListwm").fadeIn(200);
			var miList   = new IScroll(".selectMiListwm",{click:true});
		}else{
			$("#selectMiListwm").fadeOut(200);
		}
	});
	
	
	$(".adc").click(function(){
		if($("#selectMiList1").is(":hidden")){
			$("#selectMiList1").fadeIn(200);
			var miList   = new IScroll(".selectMiList1",{click:true});
		}else{
			$("#selectMiList1").fadeOut(200);
		}
	});
	
	$(document).on("click",".miList",function(){
		var selectMi = $.trim($(this).text());
		$(".selectedMi").html(selectMi).addClass('on');
		$("#selectMiList").fadeOut(200);
	});
	
	
	$(document).on("click",".miListwm",function(){
		var selectMi = $.trim($(this).text());
		$(".selectedMiwm").html(selectMi).addClass('on');
		$("#selectMiListwm").fadeOut(200);
	});
	
	$(document).on("click",".miList1",function(){
		var selectMi = $.trim($(this).text());
		$(".selectedMi1").html(selectMi).addClass('on');
		$("#selectMiList1").fadeOut(200);
	});	
})

$(function(){
	/*附近商店选择的操作*/
	
	$(".selectShop").click(function(e) {
		EQ = Number($(this).attr("EQ"));
		if(typeof $(this).attr("EQ") === "undefined"){
			return false;
		}
		// alert(EQ)
		$(".blur").addClass('selectShopping');
		var shopList = new IScroll(".listWrap",{click:true});		
		
			$(".selectShopList .listWrap li").removeClass('on');
			$(".selectShopList .listWrap li").eq(EQ).addClass('on');
		
	});
	$(".cancel").click(function(e) {
		$(this).parents(".blur").removeClass('selectShopping');
		$(".selectShopList .listWrap li").removeClass('on');
		
	});
	$(document).on("click",".selectShopList .listWrap li",function(){
		$(".selectShopList .listWrap li").removeClass('on');		
		$(this).addClass('on');		
	});
	$(".confirm").click(function(){
		$(this).parents(".blur").removeClass('selectShopping');
		var selectShop = $.trim($(".selectShopList .listWrap li.on").text());
		$(".selectedShop").html(selectShop);
		$(".selectShopList .listWrap li").each(function(i,o){
			if($(this).hasClass('on')){
				$(".selectShop").attr("EQ",i).data("id",$(this).data("id"));
			}
		})
	})
})

$(function(){
	/*附近地图选择的操作*/
	$(".location").click(function(e) {
		// $(".blur").addClass('mapping');
		$(".baiduMap").fadeIn(500);
		var map = new BMap.Map("map");
		var geoc = new BMap.Geocoder();
 		var lng = Number($("#located").data("lng"));
 		var lat = Number($("#located").data("lat"));
 		$("#dragSelect").data("lng",lng);
		$("#dragSelect").data("lat",lat);
 		$("#dragSelect").html($.trim($("#located").text()));
 		function getAds(lng,lat){
 			var point2 = new BMap.Point(lng,lat);
			map.centerAndZoom(point2, 16);
			var marker = new BMap.Marker(point2);// 创建标注
			map.addOverlay(marker);             // 将标注添加到地图中
			marker.enableDragging();         // 可拖拽	
			marker.addEventListener("dragend",function(){
				var p = marker.getPosition();       //获取marker的位置
				$("#dragSelect").data("lng",p.lng);
				$("#dragSelect").data("lat",p.lat);
				//alert("marker的位置是" + p.lng + "," + p.lat);  
				geoc.getLocation(p, function(rs){
					var addComp = rs.addressComponents,
						address = (rs.address);
						$("#dragSelect").html(address);
						//alert(address)
					// alert(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);
					// $("#located").html(address);
				});  
			});
 		}
 		if(lng == 0 && lat == 0){
 			var geolocation = new BMap.Geolocation();			
			geolocation.getCurrentPosition(function(r){
				if(this.getStatus() == BMAP_STATUS_SUCCESS){
					var pt = r.point;
					lng = pt.lng;
					lat = pt.lat;
					getAds(lng,lat);
				}
			});
		}else{			
			getAds(lng,lat);				
		}
	});
	$(".cancelMap").click(function(e) {
		$(this).parents(".baiduMap").fadeOut(500);
	});
	$(".confirmMap").click(function(){
		var DSLng = $("#dragSelect").data("lng"),
			DSLat =	$("#dragSelect").data("lat"),
			DSAds = $.trim($("#dragSelect").text());
		$("#located").data("lng",DSLng);
		$("#located").data("lat",DSLat);
		$("#located").html(DSAds);
		getShopList(DSLng,DSLat);
		$(this).parents(".baiduMap").fadeOut(500);
	});

	$("#locate").click(function(){
		$(this).html("正在定位");
		var geolocation = new BMap.Geolocation(),//地理定义
			geoc = new BMap.Geocoder();
		geolocation.getCurrentPosition(function(r){
			if(this.getStatus() == BMAP_STATUS_SUCCESS){
				var pt = r.point;
				
				$("#located").data("lng",pt.lng);
				$("#located").data("lat",pt.lat);
				
				$("#dragSelect").data("lng",pt.lng);
				$("#dragSelect").data("lat",pt.lat);
			
				getShopList(pt.lng,pt.lat);	
				getShopList2(pt.lng,pt.lat);	
				geoc.getLocation(pt, function(rs){
					var addComp = rs.addressComponents,
						address = (rs.address);
					// alert(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);
					$("#located").html(address);
					$("#dragSelect").html(address);
				}); 
				 $("#locate").html("定位成功");
				 setTimeout(function(){ $("#locate").html("定位");},1000);
			}else{
				$("#locate").html("定位失败");
				setTimeout(function(){ $("#locate").html("定位");},1000);
			}
		})
	})
})

$(function(){
	/*提示框操作*/
	$(".confirmTips").click(function(){
		confirmTips(function(){
			$(".phone input").val(null).focus();
		})
	})
})

/*提示框操作*/
function confirmTips(handle){
	$(".blur").removeClass('tipShow');
	handle();
}

function getShopList(lng,lat,url){
	 url = URL;
	$(".selectedShop").html("正在获取商店列表...");	
	setTimeout(function(){
		var shopList = {};
			shopHtmlAll = "";
			shopHtml = "";
		$.post(url,{
			lng:lng,
			lat:lat
		}, function(data, textStatus, xhr) {
			var shopLength = getJsonLength(data);
			if(shopLength>0){
				$.each(data,function(i,o){
					for(var ii = 0 ;ii <shopLength; ii++){
						shopList[ii]={
						"id":o.id,
						"name":o.name
					};
						shopHtml = '<li class="myul" EQ="'+(ii)+'" data-id="'+o.id+'"><div class="shopName">';
						shopHtml += o.name;
				shopHtml += '</div><div class="shopCheck"></div></li>';
				shopHtmlAll += shopHtml;
					}					
				})
				$(".listWrap ul").html(shopHtmlAll);
				// console.log(shopList);
				$(".selectedShop").html(shopList[0].name);
				$(".selectShop").attr("EQ",0).data("id",shopList[0].id).addClass("on");
			}else{
				$(".selectShop").removeAttr("EQ").removeData("id").removeClass("on");
				$(".selectedShop").html("您附近没有商铺哦！")
			}
			
		},"json");		
	},500)
}


function getShopList2(lng,lat,url){
	
	 url = URL2;
	 var lng=lng;
	 var lat=lat;

 $.post(url,{lng:lng,lat:lat},function(data){
	 $("#selectMiList1 ul").html(data.str);
	 var sPage = $.trim(data.page);
	 if(sPage.length == 0){
	 $("#abc").html('您附近没有社区哦');
	}else{
	 $("#abc").html(data.page);
	}
},'json');

 
}

function getJsonLength(jsonData){
	var jsonLength = 0;
	for(var item in jsonData){
	jsonLength++;
	}
	return jsonLength;
}


$(function(){
	$(".submit").click(function(){
		if($(this).attr("submit") == "0"){
			var tipsContent = "正在提交订单..."
			$(".tips header").html("稍安勿躁!");
		 	$(".tips header").addClass('warning');
			$(".tips .confirmTips").addClass('warning');
		    $(".tips .tipsContent").html(tipsContent);
		    $(".tips footer").hide();
		    $(".blur").addClass('tipShow');
		    setTimeout(function(){
		    	$(".blur").removeClass('tipShow');
		    },1000)
			return false;
		}
		var mi = $(".selectedMi.on").data("id"),
		    lng = $("#located").data("lng"),
			lat = $("#located").data("lat"),
			address = $.trim($("#located").text()),
			phone = $(".phone input").val(),
			shopName = $.trim($(".selectedShop").text()),
			shopId	= $(".selectShop").data("id"),
			notice	= $(".notice input").val();	
			openid =$("#openid").val();
		    abc=$("#abc").text();
		
		/*alert($(".selectShop").attr("EQ"));
		alert($(".selectShop").data("id"));*/
		if(mi==="-1"){
			var tipsContent = "大米品类你还没选呢！"
			$(".tips header").html("对不起!");
		 	$(".tips header").addClass('warning');
			$(".tips .confirmTips").addClass('warning');
		    $(".tips .tipsContent").html(tipsContent);
		    $(".tips footer").hide();
		    $(".blur").addClass('tipShow');
		    setTimeout(function(){
		    	$(".blur").removeClass('tipShow');
		    },1000)
			return false;
		};
		if(phone===""){
			var tipsContent = "手机号码还没有告诉我呢！"
			$(".tips header").html("对不起!");
		 	$(".tips header").addClass('warning');
			$(".tips .confirmTips").addClass('warning');
		    $(".tips .tipsContent").html(tipsContent);
		    $(".tips footer").hide();
		    $(".blur").addClass('tipShow');
		    setTimeout(function(){
		    	$(".blur").removeClass('tipShow');
		    },1000)
			return false;
		};
		if(lng===0){
			var tipsContent = "地址还没选好哦！"
			$(".tips header").html("对不起!");
		 	$(".tips header").addClass('warning');
			$(".tips .confirmTips").addClass('warning');
		    $(".tips .tipsContent").html(tipsContent);
		    $(".tips footer").hide();
		    $(".blur").addClass('tipShow');
		    setTimeout(function(){
		    	$(".blur").removeClass('tipShow');
		    },1000)
			return false;
		};
		if(typeof shopId === "undefined"){
			var tipsContent = "商店还没选好哦！"
			$(".tips header").html("对不起!");
		 	$(".tips header").addClass('warning');
			$(".tips .confirmTips").addClass('warning');
		    $(".tips .tipsContent").html(tipsContent);
		    $(".tips footer").hide();
		    $(".blur").addClass('tipShow');
		    setTimeout(function(){
		    	$(".blur").removeClass('tipShow');
		    },1000)
			return false;
		};
		/*if(notice===""){
			var tipsContent = "购米需求还没有告诉我呢！"
			$(".tips header").html("对不起!");
		 	$(".tips header").addClass('warning');
			$(".tips .confirmTips").addClass('warning');
		    $(".tips .tipsContent").html(tipsContent);
		    $(".tips footer").hide();
		    $(".blur").addClass('tipShow');
		    setTimeout(function(){
		    	$(".blur").removeClass('tipShow');
		    },1000)
			return false;
		};*/
		$(this).html("正在提交订单...");
		$(this).attr('submit','0');
		$.post(send_URL, {
			mi : mi,
			lng : lng,
			lat : lat,
			address : address,
			phone : phone,
			shopName :shopName,
			shopId : shopId,
			notice : notice,
			openid : openid,
			abc:abc
		}, function(data, textStatus, xhr) {
			if(data.status == 0){				
				setTimeout(function(){
					$(".submit").html(data.info);
					setTimeout(function(){
						window.location.href = data.url;
					},500);
				},1000);
			}else{
				alert(data.info);
				$(".submit").html(data.info);
			}
			
			/*optional stuff to do after success */
		},'json');

		setTimeout(function(){
			if($(".submit").attr('submit') == "0"){
				$(".submit").html("提交失败！");
				$(".submit").attr('submit','1');
				setTimeout(function(){
					$(".submit").html("重新提交");
				},1000)
			}
		},.5*60*1000)
	})
})


$(function(){
	// 继续下单
	$('.continueOrder').bind('click',function(e){
		e.stopPropagation();
	    var tipsContent = "您确定要继续下单吗？";
	    $(".tips footer").show();
	    $(".tips header").hide(0);
	    $(".tips .confirmTips").addClass('continueOrdering');
	    $(".tips .tipsContent").html(tipsContent);
	    $(".blur").addClass('tipShow');
	});
	// 取消订单
    $('.cencelOrder').bind('click',function(e){ 
    	e.stopPropagation();              
        var tipsContent = "您确定要取消订单吗？";
        $(".tips footer").show();
        $(".tips header").hide(0);
       	$(".tips .confirmTips").addClass('warning confirmCancelOrder');
        $(".tips .tipsContent").html(tipsContent);
        $(".blur").addClass('tipShow');       
    });
    //关闭弹框
    $(".cancelTips").click(function(e){
    	e.stopPropagation();
    	$(".blur").removeClass('tipShow');
    })
})
