/**
 * Created by 1195 on 2016-7-19.
 */

$(function(){
	PLACEORDER_NEW.computeLeftSidebar();
	PLACEORDER_NEW.bindEvent();  //绑定事件
	PLACEORDER_NEW.total_money();
	//PLACEORDER_NEW.keep_cart();
});
var PLACEORDER_NEW = {
	//事件绑定
	bindEvent             :function(){
		var self = this;
		var i = 0;

		$('#not_treatment_btn').on('touchend', function(){
			self.setClientList(this);
		});
		$('#my_treatment_btn').on('touchend', function(){
			self.setClientList(this);
			self.total_money();
		});
		
		$('#search').on('touchend', function(){
			self.setClientList();
		});
		$('#depts').on('change', function(){
			self.setClientList();
		});
		$('.function_options_list li').on('touchend touchstart', function(){
			$(this).addClass('active').siblings().removeClass('active');
		});
		/*  if ($('#not_treatment_btn').hasClass('active')) {
		 $('.function_options_list').find('li.receive_treatment_btn').show().siblings().hide();
		 }
		 $('#not_treatment_btn').on('click', function () {
		 $('.function_options_list').find('li.receive_treatment_btn').show().siblings().hide();
		 });
		 $('#my_treatment_btn').on('touchend', function () {
		 $('.function_options_list').find('li.receive_treatment_btn').hide().siblings().show();
		 });*/
		$('.client_list').on('click', 'li', function(){
			$(this).addClass('active').siblings().removeClass('active');
		});
		/*接诊按钮*/
		$('#func_treatment').on('click', function(){
			var client_id = $('#client_id').val();
			//var appointment_id = $('#appointment_id').val();
			var register_id = $('#register_id').val();
			var func_value = PLACEORDER_NEW.post(QuasarConfig.AjaxApiMap.Treatment, {
				client_id  :client_id,
				//appointment_id:appointment_id,
				register_id:register_id
			});
			console.log(func_value);
			if(func_value.status){
				console.log('接诊完成');
				var txt = '接诊完成';
				self.promptTips(txt);
				$('#my_treatment_btn').trigger('touchend').addClass('active').siblings().removeClass('active');
				$('#tab-pane1').removeClass('in active')
				$('#tab-pane2').addClass('in active');
				$('.function_options_list').find('li').hide();
			}else{
				console.log('没有选择客户');
				var txt = '没有选择客户';
				self.promptTips(txt);
			}
		});
		$('#cancel_reception').on('click', function(){
			var client_id = $('#client_id').val();
			//var appointment_id = $('#appointment_id').val();
			var register_id = $('#register_id').val();
			var func_value = PLACEORDER_NEW.post(QuasarConfig.AjaxApiMap.CancelTreatment, {
				client_id  :client_id,
				//appointment_id:appointment_id,
				register_id:register_id
			});
			console.log(func_value);
			if(func_value.status){
				console.log('取消接诊完成');
				var txt = '取消接诊完成';
				self.promptTips(txt);
				$('#not_treatment_btn').trigger('touchend').addClass('active').siblings().removeClass('active');
				$('#tab-pane1').addClass('in active');
				$('#tab-pane2').removeClass('in active')
				$('.function_options_list').find('li').hide();
			}else{
				console.log('无法取消接诊');
				var txt = '无法取消接诊';
				self.promptTips(txt);
			}
		});
		//产品选中事件
		$('.list_item').on('touchend', '.list_item_c', function(){
			//获取产品的数据内容
			var project_value = PLACEORDER_NEW.post('', {
				action:'get_project',
				type  :'operation',
				id    :$(this).attr('data-id')
			}, 'json');
			Object.prototype.toString.call(project_value);
			//console.log($(this).attr('data-id'));
			console.log(project_value);
			var str = '';
			var pro_tem = self.productTemplate+'';
			//console.log(pro_tem);
			//产品列表遍历
			$.each(project_value, function(index, value){
				//console.log(value);
				str += pro_tem.replace('$pro_name', value.BBY05).replace('$pro_price', parseFloat(value.price)).replace('$id', value.BBX01).replace('$type', value.BDA01);
				//console.log(str);
			});
			$('.pro_list').html(str);
			//复选框选中事件
			$('.pro_list .pro_item').on('touchend', function(){
				if($(this).find('.check_ipt').is(':checked')){
					$(this).find('.check_ipt').prop('checked', false);
					console.log('未选中');
				}else{
					$(this).find('.check_ipt').prop('checked', true);
					console.log('选中');
				}
				;
			});
			$('input.check_ipt').on('touchend', function(){
				event.preventDefault();  //清除默认事件
			});
		});
		//确认按钮事件
		$('.confirm_btn').on('touchend', function(){
			var arr = [];
			$('input[name=\'pro_checkbox\']:checked').each(function(){
				var type = $(this).attr('data-type');
				var name = $(this).parents('.pro_item_content').find('.pro_name').text();
				var price = $(this).parents('.pro_item_content').find('.pro_price').text();
				arr.push({id:this.value, type:type, name:name, price:price, number:1});
			})
			console.log(arr);
			var func_value = PLACEORDER_NEW.post(QuasarConfig.AjaxApiMap.SaveToShoppingCart, {
				data  :arr,
				action:'temp_save'
			});
		})

		//var keep_cart_value = self.keep_cart();
		//点击增加按钮
		$('.car_stock').on('touchend', '.add', function(){
			var num = '';
			var price = '';
			var input = $(this).siblings('.cart_text');   //s数量
			var obj = $(this).parents('tr'); //tr
			var index = $(this).attr('data-index');
			//单价
			var unit_price = parseFloat(obj.siblings('tr').find('.unit_price').text().replace('¥', ''));  //单价
			input.attr('value', parseInt(input.attr("value"))+1);  //+1
			num = Number(input.attr('value'));		//选中个数
			price = num*unit_price;			//单个项目总价
			obj.siblings("tr").find(".single-total").val(price);
			self.total_money();   //总价
			QuasarConfig.CartData[index-1].price = price;
			QuasarConfig.CartData[index-1].number = num;
			console.log(QuasarConfig.CartData)
			return QuasarConfig.CartData;
		});
		//点击删减按钮
		$('.car_stock').on('touchend', '.reduce', function(){
			var num = '';
			var price = '';
			var input = $(this).siblings('.cart_text');   //个数
			var obj = $(this).parents('tr'); //tr
			var index = $(this).attr('data-index');
			//单价
			var unit_price = parseFloat(obj.siblings('tr').find('.unit_price').text().replace('¥', ''));  //单价
			var val_num = parseInt(input.attr("value"));
			input.attr("value", parseInt(val_num)-1);//数量减1
			//数量大于1时 数量递减   数量小于1时 项目删除
			if(val_num>1){
				num = input.attr('value');		//选中个数
				price = num*unit_price;			//单个项目总价
				obj.siblings("tr").find(".single-total").val(price);
				self.total_money();  ////总价
				QuasarConfig.CartData[index-1].price = price;
				QuasarConfig.CartData[index-1].number = num;
				console.log(QuasarConfig.CartData)
				return QuasarConfig.CartData;
			}else{
				alert('删除');
				$(this).parents('li').remove();
				num = input.attr('0');
				self.total_money(); ////总价
				//self.keep_cart();
			}
		});
		//点击确认购买
		$('.btn_purchase_confirm').on('touchend', function(){
			console.log(QuasarConfig.CartData)
		});
	},
	//算出产品的总价
	total_money           :function(){
		var total_price = 0;
		$('.cart_pro_item').each(function(){
			var price = parseFloat($(this).find('.single-total').val());
			total_price += price;
		});
		$('.total_price').find('.number').text(total_price);
	},
	/* 操作处理信息*/
	promptTips            :function(txt){
		var prompt_tips = $('#prompt_tips');
		prompt_tips.fadeIn();
		prompt_tips.find('#prompt_message').text(txt);
		setTimeout(function(){
			prompt_tips.fadeOut()
		}, 2000)
	},
	/* ajax 请求 返回数据 */
	post                  :function(url, data){
		var self = this;
		var result = null;
		$.ajax({
			type      :'post',
			url       :url,
			data      :data,
			dataType  :'json',
			async     :false,
			beforeSend:function(){
				//未完成加载
				$('#loading').show();
			},
			success   :function(r){
				result = r;
			},
			complete  :function(){
				//完成加载
				$('#loading').hide();
			},
			error     :function(){
				console.log(url+'网络错误');
				var txt = url+'网络错误';
				self.promptTips(txt);
			}
		});
		return result;
	},
	//选中客户
	chooseClient          :function(id, vac01){
		var result = this.post(QuasarConfig.AjaxApiMap.GetClientInfo, {id:id});
		console.log(result);
		$('.client_information .client_name span').html(result['SCA06']);
		$('.client_information .channel span').html(result['Channel']);
		$('.client_information .exclusive_Consultant span').html(result['BCE03D']);
		$('.client_information .appointment_date span').html(result['SCF10']);
		$('#client_id').val(result['SCA01']);
		$('#appointment_id').val(result['SCF01']);
		$('#register_id').val(vac01);
		var client_id = $('#client_id').val();
		var appointment_id = $('#appointment_id').val();
		if($('#not_treatment_btn').hasClass('active')){
			if(client_id == ''){
				console.log('客户不存在');
				$('.function_options_list').find('li').hide();
			}else{
				console.log('客户存在');
				$('.function_options_list').find('li').hide().siblings('#func_treatment').show();
			}
		}
		if($('#my_treatment_btn').hasClass('active')){
			if(client_id == ''){
				console.log('客户不存在');
				$('.function_options_list').find('li').hide();
			}else{
				console.log('客户存在');
				$('.function_options_list').find('li').show().siblings('#func_treatment').hide();
			}
		}
	},
	//得到客户列表
	setClientList         :function(obj){
		if(!arguments[0]) obj = 'li[role=presentation].active';
		var keyword = $('#keyword').val();
		var bck01 = $('#depts').val();
		//var type = $('li[role=presentation].active').attr('data-value');
		var type = $(obj).attr('data-value');
		var result = '', html = '', layout = '';
		switch(type){
			case 'not_treatment':
				result = this.post(QuasarConfig.AjaxApiMap.NotTreatment, {dept:bck01, keyword:keyword});
				layout = 'client_list';
				break;
			case 'my_treatment':
				result = this.post(QuasarConfig.AjaxApiMap.MyTreatment, {dept:bck01, keyword:keyword});
				layout = 'client_list_2';
				break;
		}
		for(var i = 0; i<result.length; i++) html += this.writeSingleTemplate(result[i]['SCA06'], result[i]['ABW01'], result[i]['VAA10'], result[i]['VAA03'], result[i]['SCA01'], result[i]['VAC01']);
		$('#'+layout).html(html);
		//PLACEORDER_NEW.initSwiperPlugin('#scroller');
		//PLACEORDER_NEW.initSwiperPlugin('#scroller_2');
	},
	//左侧栏目
	computeLeftSidebar    :function(){
		var m_sidebar_left = $('#m_sidebar_left');
		var m_sidebar_left_h = m_sidebar_left.height();
		var header_h = m_sidebar_left.find('.title').height();
		var sec_h = m_sidebar_left.find('#condition').height();
		var m_tab_content_h = m_sidebar_left_h-header_h-sec_h;
		$('#m_tab_content').css('maxHeight', m_tab_content_h)
	},
	
	//上拉刷新
	initSwiperPlugin      :function(selected){
		var holdPosition = 0;
		var mySwiper = new Swiper(selected, {
			slidesPerView     :'auto',
			mode              :'vertical',
			watchActiveIndex  :true,
			onTouchStart      :function(){
				holdPosition = 0;
			},
			onResistanceBefore:function(s, pos){
				holdPosition = pos;
			},
			onTouchEnd        :function(){
				if(holdPosition>100){
					mySwiper.setWrapperTranslate(0, 40, 0);
					mySwiper.params.onlyExternal = true;
					$('.preloader').addClass('visible');
					loadNewSlides();
				}
			}
		});
		var slideNumber = 0;

		function loadNewSlides(){
			setTimeout(function(){
				//Prepend new slide
				var colors = ['red', 'blue', 'green', 'orange', 'pink'];
				var color = colors[Math.floor(Math.random()*colors.length)];
				/*mySwiper.prependSlide('<div class="title">sucaijiayuan.com '+slideNumber+'</div>', 'swiper-slide '+color+'-slide');*/
				//Release interactions and set wrapper
				mySwiper.setWrapperTranslate(0, 0, 0);
				mySwiper.params.onlyExternal = false;
				//Update active slide
				mySwiper.updateActiveSlide(0);
				//Hide loader
				$('.preloader').removeClass('visible');
			}, 1000);
			slideNumber++;
		}
	},
	writeSingleTemplate   :function(name, gender, age, number, id, vac01){
		return this.clientListItenTemplate.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace('{::name::}', name).replace('{::gender::}', gender).replace('{::number::}', number).replace('{::age::}', age).replace('{::id::}', id).replace('{::vac01::}', vac01);
	},
	//客户列表
	clientListItenTemplate:"<!--suppress ALL --><li onclick=\'PLACEORDER_NEW.chooseClient({::id::}, {::vac01::})\' class=\'swiper-slide\'>\n\t<div class=\'client_item\'>\n\t\t<div class=\'name\'>{::name::}</div>\n\t\t<div class=\'description\'>\n\t\t\t<div class=\'box\'>\n\t\t\t\t<div class=\'flex1\'>性 别：<span>{::gender::}</span></div>\n\t\t\t\t<div class=\'flex1\'>年 龄：<span>{::age::}</span></div>\n\t\t\t</div>\n\t\t\t<p>门诊号：<span>{::number::}</span></p></div>\n\t</div>\n</li>",
	//产品详情列表
	productTemplate       :"<li class=\"pro_item\" >"+
	"					<div class=\"pro_item_content\">"+
	"						<div class=\"checkbox\"><label><input class=\"check_ipt\" name=\'pro_checkbox\' type=\"checkbox\" value=\"$id\" data-type=\'$type\'></label></div>"+
	"						<div class=\"desc\"><span class=\"pro_name\">$pro_name</span><span class=\"pro_price\">¥$pro_price</span></div>"+
	"					</div>"+
	"				</li>",
}


