/* 
* @Author: 星辉
* @Date:   2015-01-06 08:53:12
* @Last Modified by:   星辉
* @Last Modified time: 2015-03-18 14:48:50
*/

$(function(){
				var goods 	 	= $(".addOne");						//初始化商品位置
				var TotalCountL	= $("#num");									//初始化总数量位置
				var TotalPriceL	= $("#price");									//初始化总价格位置
				var GoodsData   = GoodsDatas;									//获取商品数据
				var TotalCount  = 0;											//初始化总数量
				var TotalPrice  = 0;											//初始化总价格
				var cartlist_a	='<div class="myul light-red goodsItem"><div class="goods">';
				var cartlist_b	='</div>';
				var cartlist_b1	='<div class="circle light-red deleteone" key="';
				var cartlist_b2	='<div class="circle light-red delete" key="';
				var cartlist_c	='" data-count="';
				var cartlist_d	='" goodsId="';

				var cartlist_e	='">×</div>';

				var cartlist_e0	='">-</div>';
				var cartlist_e1	='</div>';
				var cartlist    ="";
				var cartlistL	= $(".Cartlist");				
				var Cart 		= {};	
				var goods_list	= {};
				
				if(localStorage.CartJson){
					Cart = JSON.parse(localStorage.CartJson);
					goods_list=Cart["goods_list"];
				}else{
					localStorage.CartJson="";
					//console.log(localStorage.CartJson);
				}
					

				//console.log(locart["TotalPrice"]);
				//
				if(Cart["TotalCount"]>0){
					$(".shopCartHasGoods").removeClass('hide');
					$(".shopCartIsNullBox").addClass('hide');
					TotalCount  = Cart["TotalCount"];											//初始化总数量
					TotalPrice  = Cart["TotalPrice"];											//初始化总价格
					TotalCountL.html(TotalCount);
					TotalPriceL.html(TotalPrice);

					$.each(Cart["goods_list"],function(index, el) {
						$("#index_"+index).data('count',$(el)[0].count);
						$("#delete_"+index).data('count',$(el)[0].count);
						if($(el)[0].count > 0){
							$("#index_"+index).children('.countBuy').fadeIn(0).html($(el)[0].count);
							$("#delete_"+index).removeClass('hide');	
						}
					});

				}else{
					$(".shopCartHasGoods").addClass('hide');
					$(".shopCartIsNullBox").removeClass('hide');
				}
				// goods.each(locart,function(){

					// })

				if(localStorage.CartList){
					$(".Cartlist").html(localStorage.CartList);
					//console.log(localStorage.CartList);
				}else{
					localStorage.CartList="";
					//console.log(localStorage.CartList);
				}

	

				$(".addOne").on('touchstart',function(e) {
					$(this).off("touchend");
					var ISMove = false;
					// e.stopPropagation;

                    // e.preventDefault;

					//console.log(Cart);
					$(this).on("touchmove",function(){
						ISMove = true;
					})
					$(this).on("touchend",function(){
						if(!ISMove){
							var THIS    = $(this);
							var count   = parseInt(THIS.data('count'));					//获取购买数量
							var key     = parseInt(THIS.attr('key'));					//获取该商品在本次加载的键值
							var goodsId = parseInt(THIS.attr('goodsid'));				//获取该商品的id(唯一)
							var price   = parseFloat(GoodsData[goodsId].vprice)			//通过键值获取该商品的价格
							var goodsname = GoodsData[goodsId].name;				//通过键值获取该商品的名称
							var shopname = GoodsData[goodsId].username;					//通过键值获取该商店的名称
							var shopId	 = GoodsData[goodsId].sid;						//通过键值获取该商店的id
//alert(price);alert(goodsname)
					var stock	 = GoodsData[goodsId].stock;
					var goods 	 	= $(".addOne");							//通过键值获取库存

					if(stock <= count){
						alert('当前商品库存不够哦');
						return;
					}	

					count      += 1;											//每点击一次，数量+1
					THIS.data('count',count);						 			//储存点击数量
					THIS.find(".countBuy").fadeIn(50).html(count);   			//显示该商品的添加数量
					$("#delete_"+goodsId).data('count',count);
					$("#delete_"+goodsId).removeClass('hide');
                    
					TotalPrice+=price;											//计算总价格
					TotalCount+=1;												//计算总数量
					var TPrice =Number(TotalPrice.toFixed(2)) ;					//解决浮点型加法的BUG
					//console.log(TotalCount);
					//console.log(CartJson);
					TotalPrice = TPrice
					if(TotalCount>0){
						$(".shopCartHasGoods").removeClass('hide');
						$(".shopCartIsNullBox").addClass('hide');
					}else{
						$(".shopCartHasGoods").addClass('hide');
						$(".shopCartIsNullBox").removeClass('hide');
					}
					TotalCountL.html(TotalCount);
					TotalPriceL.html(TotalPrice);
					
					Cart["TotalPrice"]=TotalPrice;
					Cart["TotalCount"]=TotalCount;


				    goods_list[goodsId]={
				    	"shopId":shopId,
						"goodsname":goodsname,
						"count":count,
						"price":price,
                        "shopname":shopname
					};		


					Cart["goods_list"]={};		
		

					Cart["goods_list"] =goods_list;
					//[shopId,goodsname,count,price];
					//console.log(Cart);

					var cartlist_1 = goodsname+":"+price+"x"+count+"=￥"+Number((price*count).toFixed(2))+"<span class='storename'>["+shopname+"]</span>";
					cartlist = cartlist_a+cartlist_1+cartlist_b+cartlist_b1+key+cartlist_c+count+cartlist_d+goodsId+cartlist_e0+cartlist_b2+key+cartlist_c+count+cartlist_d+goodsId+cartlist_e+cartlist_e1;
					
					if(cartlistL.children().hasClass('G'+goodsId)){
						$(".G"+goodsId).html(cartlist);
						
					}else{
						cartlistL.append("<div class='G"+goodsId+"'>"+cartlist+"</div>");
					}
					localStorage.CartJson=JSON.stringify(Cart);
					localStorage.CartList=$(".Cartlist").html();
					//console.log(localStorage.CartList);
						}
					});
				});


				$(document).on('click',".delete", function(e) {
			
					// e.preventDefault();
					// e.stopPropagation();
					var goods 	 	= $(".addOne");	
					var THIS    = $(this);
					var count   = parseInt(THIS.data('count'));					//获取购买数量
					var key     = parseInt(THIS.attr('key'));					//获取该商品在本次加载的键值
					var goodsId = parseInt(THIS.attr('goodsId'));				//获取该商品的id(唯一)
                    var price   = parseFloat(goods_list[goodsId].price)			//通过键值获取该商品的价格
                    var goodsname = goods_list[goodsId].goodsname;				//通过键值获取该商品的名称
                    var shopname = goods_list[goodsId].shopname;					//通过键值获取该商店的名称
                    var shopId	 = goods_list[goodsId].shopId;						//通过键值获取该商店的id
                    //console.log(goods_list);

					//console.log(count)
					TotalPrice-=Number((price*count).toFixed(2));				//重新计算总价格
					var TPrice =Number(TotalPrice.toFixed(2)) ;					//解决浮点型加法的BUG
					TotalPrice = TPrice;
					TotalCount-=count;											//重新计算总数量
					count      -= count;										//清空
					THIS.data('count',count);						 			//储存点击数量
					$("#delete_"+goodsId).data('count',count);	
					$("#delete_"+goodsId).addClass('hide');			
					TotalCountL.html(TotalCount);
					TotalPriceL.html(TotalPrice);
					$(this).parents(".G"+goodsId).slideUp(200,function(){$(this).remove();localStorage.CartList=$(".Cartlist").html();});
					//console.log($(".Cartlist").html())
					if(TotalCount>0){
						$(".shopCartHasGoods").removeClass('hide');
						$(".shopCartIsNullBox").addClass('hide');
					}else{
						$(".shopCartHasGoods").find('.shopCartContent').hide();
						$(".shopCartHasGoods").find('.arrowCricleSwrap').removeClass('on');
						$(".shopCartHasGoods").addClass('hide');
						$(".shopCartIsNullBox").removeClass('hide');
						
					}

					$(".addOne").each(function(i,o){
			     		if($(o).attr("goodsId")==goodsId){
			     			$(o).data('count', '0');
			     			$(o).find('.countBuy').html(0).hide();
			     		}
			    	});
			    	
			    	Cart["TotalPrice"]=TotalPrice;
					Cart["TotalCount"]=TotalCount;


                    goods_list[goodsId]={
                        "shopId":shopId,
                        "goodsname":goodsname,
                        "count":count,
                        "price":price,
                        "shopname":shopname
                    };


                    Cart["goods_list"]={};
		

					Cart["goods_list"] =goods_list;
					//[shopId,goodsname,count,price];
					//console.log(Cart);
					localStorage.CartJson=JSON.stringify(Cart);
					//console.log(localStorage.CartJson);
					// localStorage.CartList=$(".Cartlist").html();
					//console.log(localStorage.CartList);

				});
				
				$(".deleteone").on('touchend', function(e) {
					// e.preventDefault();
					// e.stopPropagation();
				
					var goods 	 	= $(".addOne");	
					var THIS    = $(this);
					var count   = parseInt(THIS.data('count'));					//获取购买数量
					var key     = parseInt(THIS.attr('key'));					//获取该商品在本次加载的键值
					var goodsId = parseInt(THIS.attr('goodsId'));				//获取该商品的id(唯一)
		  			var price   = parseFloat(goods_list[goodsId].price);			//通过键值获取该商品的价格
                    var goodsname = goods_list[goodsId].goodsname;				//通过键值获取该商品的名称
                    var shopname = goods_list[goodsId].shopname;					//通过键值获取该商店的名称
                    var shopId	 = goods_list[goodsId].shopId;						//通过键值获取该商店的id
				//alert(price);alert(goodsname)
					if(THIS.hasClass('wares')){

							if(count>0){
								// alert(count)
								TotalPrice-=Number((price).toFixed(2));					//重新计算总价格
								var TPrice =Number(TotalPrice.toFixed(2)) ;				//解决浮点型加法的BUG
								TotalPrice = TPrice;
								TotalCount-=1;											//重新计算总数量
								TotalCountL.html(TotalCount);
								TotalPriceL.html(TotalPrice);
								count  	   -=1;
								THIS.data('count',count);						 		//储存点击数量
								
								goods.each(function(i,o){
						     		if($(o).attr("goodsId")==goodsId){
						     				$(o).data('count', count);
						     				$(o).find('.countBuy').html(count);
						     				
						     			}	     		
					    		});	

								if(TotalCount>0){

									$(".shopCartHasGoods").removeClass('hide');
									$(".shopCartIsNullBox").addClass('hide');

								}else{
									$(".shopCartHasGoods").find('.shopCartContent').hide();
									$(".shopCartHasGoods").find('.arrowCricleSwrap').removeClass('on');
									$(".shopCartHasGoods").addClass('hide');
									$(".shopCartIsNullBox").removeClass('hide');
								}
								if(count==0){
									$("#delete_"+goodsId).data('count',"0");	
									$("#delete_"+goodsId).addClass('hide');	
									$(".G"+goodsId).slideUp(200,function(){$(".G"+goodsId).remove();localStorage.CartList=$(".Cartlist").html();});
									goods.each(function(i,o){
							     		if($(o).attr("goodsId")==goodsId){
							     			$(o).data('count', '0');
							     			$(o).find('.countBuy').html(0).hide();
							     		}
					    			});
								}
							}
							
								
					}else{

						// alert(price);alert(goodsname)
						if(count>0){

							TotalPrice-=Number((price).toFixed(2));					//重新计算总价格
							var TPrice =Number(TotalPrice.toFixed(2)) ;				//解决浮点型加法的BUG
							TotalPrice = TPrice;
							TotalCount-=1;											//重新计算总数量
							TotalCountL.html(TotalCount);
							TotalPriceL.html(TotalPrice);
							count  	   -=1;
							alert(goodsId)
							THIS.data('count',count);						 		//储存点击数量
							$("#delete_"+goodsId).data('count',count);
							goods.each(function(i,o){
							console.log(i);
					     		if($(o).attr("goodsId")==goodsId){
					     				$(o).data('count', count);
					     				$(o).find('.countBuy').html(count);
					     				
					     			}	     		
				    		});	

							if(TotalCount>0){

								$(".shopCartHasGoods").removeClass('hide');
								$(".shopCartIsNullBox").addClass('hide');

							}else{
								$(".shopCartHasGoods").find('.shopCartContent').hide();
								$(".shopCartHasGoods").find('.arrowCricleSwrap').removeClass('on');
								$(".shopCartHasGoods").addClass('hide');
								$(".shopCartIsNullBox").removeClass('hide');
							}
							if(count==0){
								$("#delete_"+goodsId).data('count',"0");	
								$("#delete_"+goodsId).addClass('hide');	
								$(this).parents(".G"+goodsId).slideUp(200,function(){$(this).remove();localStorage.CartList=$(".Cartlist").html();});
								goods.each(function(i,o){
						     		if($(o).attr("goodsId")==goodsId){
						     			$(o).data('count', '0');
						     			$(o).find('.countBuy').html(0).hide();
						     		}
				    			});
							}

						}
					}	
					var cartlist_1 = goodsname+":"+price+"x"+count+"=￥"+Number((price*count).toFixed(2))+"<span class='storename'>["+shopname+"]</span>";
					cartlist = cartlist_a+cartlist_1+cartlist_b+cartlist_b1+key+cartlist_c+count+cartlist_d+goodsId+cartlist_e0+cartlist_b2+key+cartlist_c+count+cartlist_d+goodsId+cartlist_e+cartlist_e1;
					
					if(cartlistL.children().hasClass('G'+goodsId)){
						$(".G"+goodsId).html(cartlist);
						localStorage.CartList=$(".Cartlist").html();	
					}else{
						cartlistL.append("<div class='G"+goodsId+"'>"+cartlist+"</div>");
						localStorage.CartList=$(".Cartlist").html();	
					};

					Cart["TotalPrice"]=TotalPrice;
					Cart["TotalCount"]=TotalCount;

                    goods_list[goodsId]={
                        "shopId":shopId,
                        "goodsname":goodsname,
                        "count":count,
                        "price":price,
                        "shopname":shopname
                    };

                    Cart["goods_list"]={};
		

					Cart["goods_list"] =goods_list;
					//[shopId,goodsname,count,price];
					//console.log(Cart);
					localStorage.CartJson=JSON.stringify(Cart);
					//console.log(localStorage.CartJson);
					
					//console.log(localStorage.CartList);
					
				});




			})
