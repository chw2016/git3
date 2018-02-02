/*******************************
 * @Copyright:智讯科技
 * @Creation date:2015.3.11
 *******************************/
 var REG = {
		name: /^[a-zA-Z\u4e00-\u9fa5]{2,6}$/,
		phone: /(^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$)|(^0{0,1}1[0|1|2|3|4|5|6|7|8|9][0-9]{9}$)/,
		passwd:/^[0-9]{6,8}$/,
		id:/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[A-Z])$/
  }
 Zepto(function($){
 	//区块链接跳转
 	$('*[data-href]').tap(function(){
 		window.location.href=$(this).attr('data-href');
 	});
 	//登录
 	var submitLogin=$('#J_submitLogin');
 	var login_phone=$('#login_phone');
 	var login_passwd=$('#login_passwd');
 	submitLogin.tap(function(){
 		var phone=$.trim(login_phone.val());
 		var passwd=$.trim(login_passwd.val());
 		if(phone==''){
 			$.dialog({
	            content:'手机号不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.phone.test(phone)){
 			$.dialog({
	            content:'请输入正确的手机号码',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(passwd==''){
 			$.dialog({
	            content:'密码不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}

 		var el=$.loading({
	        content:'正在登录'
	    });
	    var DATA={
	    	waid:$('#waid').val(),
	    	uid:$('#uid').val(),
	    	phone:phone,
	    	password:passwd
	    };
	    $.post($('#submitUrl').val(),DATA,function(data){
	    	/*
			status=200为成功
			status=500为账号密码不正确
			status=其他为网络错误等
			url=跳转链接
	    	*/
	    	if(data.status==200){
	    		document.location.href=data.url;
	    	}else if(data.status==500){
	    		$.dialog({
		            content:data.error,
		            button:['我知道了']
		        });
	    	}else{
	    		$.dialog({
		            content:'网络错误，请重试',
		            button:['我知道了']
		        });
	    	}
	    	el.hide();
	    },'json');
 	});
 	var reg_name=$('#reg_name');
 	var reg_phone=$('#reg_phone');
 	var reg_passwd=$('#reg_passwd');
 	var reg_type=$('#reg_type');
 	var reg_rule=$('#reg_rule');
 	var submitReg=$('#J_submitReg');
 	var reg_companyname=$('#reg_companyname');
 	var J_regShowName=$('#J_regShowName');
     var idCard=$('#J_userId');
     var tagCodeCls = $('.tag-code-cls');
     var tag_codes = $('#tag_codes').val();
	//选择身份
	reg_type.on('change',function(){
		var selected=$(this).prop('selectedIndex');
		var option=$(this).find('option');
		if($(this).val()==0){
			$(this).addClass('gray');
		}else{
			$(this).removeClass('gray');
		}
		if('' != tag_codes && '' != JSON.parse(tag_codes)[$(this).val()]){
			tagCodeCls.show();
		}else{
			tagCodeCls.hide();
		}
		if(option.eq(selected).attr('data-type')=='company'){
            idCard.hide();
			J_regShowName.show();
		}else if(option.eq(selected).attr('data-type')=='OWNER'){
            J_regShowName.hide();
            idCard.show();
        }else{
			J_regShowName.hide();
            idCard.hide();
		}
	});
	submitReg.tap(function(){
 		var name=$.trim(reg_name.val());
 		var phone=$.trim(reg_phone.val());
 		var passwd=$.trim(reg_passwd.val());
 		var type=reg_type.val();
 		var company=$.trim(reg_companyname.val()), id='';
 		var rule=reg_rule.prop('checked') ? 1 : 0;

 		var selected=reg_type.prop('selectedIndex');
		var option=reg_type.find('option');
		var typestr=option.eq(selected).attr('data-type');

        if('OWNER'==typestr){
            id=$.trim($('#user_id').val());
        }

 		if(name==''){
 			$.dialog({
	            content:'姓名不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.name.test(name)){
 			$.dialog({
	            content:'姓名为2~6字符',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(phone==''){
 			$.dialog({
	            content:'手机号不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.phone.test(phone)){
 			$.dialog({
	            content:'请输入正确的手机号码',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(passwd==''){
 			$.dialog({
	            content:'密码不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.passwd.test(passwd)){
 			$.dialog({
	            content:'密码为6~8位数字',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(type==0){
 			$.dialog({
	            content:'请选择您的身份',
	            button:['我知道了']
	        });
	        return false;
 		}
        if(typestr=='OWNER' && id==''){
            $.dialog({
                content:'请输入您的18号身份证号',
                button:['我知道了']
            });
            return false;
        }
        if(typestr=='OWNER' && !REG.id.test(id)){
            $.dialog({
                content:'请输入正确的身份证号',
                button:['我知道了']
            });
            return false;
        }
 		if(type=='company' && (company.length<2 || company.length>50)){
 			$.dialog({
	            content:'请输入正确的公司名称',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(rule==0 && reg_rule.length>0){
 			$.dialog({
	            content:'请同意经纪人注册条款',
	            button:['我知道了']
	        });
	        return false;
 		}

 		var el=$.loading({
	        content:'正在提交'
	    });
	    var DATA={
            waid:$('#waid').val(),
            uid:$('#uid').val(),
            ruid:$('#ruid').val(),
            username:name,
	    	phone:phone,
            password:passwd,
            tag:type,
            idCard:id,
            company:company,
            tag_code:$('#tag_code').val()
	    };
	    $.post($('#submitUrl').val(),DATA,function(data){
	    	/*
			status=200为成功
			status=500为手机号已注册
			status=其他为网络错误等
			url=跳转链接
	    	*/
	    	if(data.status==200){
	    		document.location.href=data.url;
	    	}else if(data.status==500){
	    		$.dialog({
		            content:data.error,
		            button:['我知道了']
		        });
	    	}else{
	    		$.dialog({
		            content:'网络错误，请重试',
		            button:['我知道了']
		        });
	    	}
	    	el.hide();
	    },'json');
 	});
	
	//修改个人信息
	var user_name=$('#user_name');
 	var user_phone=$('#user_phone');
 	var user_type=$('#user_type');
 	var user_id=$('#user_id');
 	var J_submitProfile=$('#J_submitProfile');
 	var userId=$('#J_userId');
 	var J_commpanyName=$('#J_commpanyName');
 	var user_company=$('#user_company');
 	
 	if($('#tag_code_edit').length>0 && '' != tag_codes && '' != JSON.parse(tag_codes)[user_type.val()] && undefined != JSON.parse(tag_codes)[user_type.val()]) {
		$('#tag_code_edit').val(JSON.parse(tag_codes)[user_type.val()]);
	}
	//选择身份
	user_type.on('change',function(){
		var selected=user_type.prop('selectedIndex');
		var option=user_type.find('option');
		var typestr=option.eq(selected).attr('data-type');
		if($(this).val()==0){
			$(this).addClass('gray');
		}else{
			$(this).removeClass('gray');
		}
		if('' != tag_codes && '' != JSON.parse(tag_codes)[$(this).val()]){
			if($('#tag_code_edit').length>0){
				$('#tag_code_edit').val('');
			}
			tagCodeCls.show();
		}else{
			tagCodeCls.hide();
		}
		if(option.eq(selected).attr('data-type')=='company'){
            userId.hide();
			J_commpanyName.show();
		}else if(option.eq(selected).attr('data-type')=='OWNER'){
            J_commpanyName.hide();
            userId.show();
		}else{
			userId.hide();
			J_commpanyName.hide();
		}
	});
	//保存个人信息
	J_submitProfile.tap(function(){
 		var name=$.trim(user_name.val());
 		var phone=$.trim(user_phone.val());
        var type=user_type.val(), id='';
        var typerel=user_type.attr('data-rel');
        var company=$.trim(user_company.val());
        if(type==typerel){
            id=$.trim($('#user_id').val());
        }

        var selected=user_type.prop('selectedIndex');
		var option=user_type.find('option');
		var typestr=option.eq(selected).attr('data-type');

 		if(name==''){
 			$.dialog({
	            content:'姓名不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.name.test(name)){
 			$.dialog({
	            content:'姓名为2~6字符',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(phone==''){
 			$.dialog({
	            content:'手机号不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.phone.test(phone)){
 			$.dialog({
	            content:'请输入正确的手机号码',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(type==0){
 			$.dialog({
	            content:'请选择您的身份',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(typestr=='OWNER' && id==''){
 			$.dialog({
	            content:'请输入您的18号身份证号',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(typestr=='OWNER' && !REG.id.test(id)){
 			$.dialog({
	            content:'请输入正确的身份证号',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(typestr=='company' &&  (company.length<2 || company.length>50)){
 			$.dialog({
	            content:'请输入正确的公司名称',
	            button:['我知道了']
	        });
	        return false;
 		}
 	

 		var el=$.loading({
	        content:'正在提交'
	    });
	    var DATA={
            waid:$('#waid').val(),
            uid:$('#uid').val(),
	    	name:name,
	    	phone:phone,
            tag:type,
            idCard:id,
            company:company,
            tag_code:$('#tag_code_edit').val()
	    };
	    $.post($('#submitUrl').val(),DATA,function(data){
	    	/*
			status=200为成功
			status=其他为网络错误等
			url=跳转链接
	    	*/
	    	if(data.status==200){
	    		document.location.href=data.url;
	    	}else if(data.status==500){
                $.dialog({
                    content:data.error,
                    button:['我知道了']
                });
            }else{
	    		$.dialog({
		            content:'网络错误，请重试',
		            button:['我知道了']
		        });
	    	}
	    	el.hide();
	    },'json');
 	});


	//修改用户角色
	var cuser_name=$('#cuser_name');
 	var cuser_phone=$('#cuser_phone');
 	var cuser_code=$('#cuser_code');
 	var J_submitCuser=$('#J_submitCuser');
	//保存修改
	J_submitCuser.tap(function(){
 		var name=$.trim(cuser_name.val());
 		var phone=$.trim(cuser_phone.val());
 		var code=$.trim(cuser_code.val());
 		if(name==''){
 			$.dialog({
	            content:'姓名不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.name.test(name)){
 			$.dialog({
	            content:'姓名为2~6字符',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(phone==''){
 			$.dialog({
	            content:'手机号不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.phone.test(phone)){
 			$.dialog({
	            content:'请输入正确的手机号码',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(code==''){
 			$.dialog({
	            content:'请输入您的邀请码',
	            button:['我知道了']
	        });
	        return false;
 		}
 		var el=$.loading({
	        content:'正在提交'
	    });
	    var DATA={
            waid:$('#waid').val(),
            uid:$('#uid').val(),
            role:$('#role').val(),
	    	name:name,
	    	phone:phone,
	    	code:code
	    };
	    $.post($('#submitUrl').val(),DATA,function(data){
	    	/*
			status=200为成功
			status=500为邀请码不正确
			status=其他为网络错误等
			url=跳转链接
	    	*/
	    	if(data.status==200){
	    		document.location.href=data.url;
	    	}else if(data.status==500){
	    		$.dialog({
		            content:data.error,
		            button:['我知道了']
		        });
	    	}else{
	    		$.dialog({
		            content:'网络错误，请重试',
		            button:['我知道了']
		        });
	    	}
	    	el.hide();
	    },'json');
 	});
	//提交投诉与建议
	var user_suggest=$('#user_suggest');
	var J_submitSuggest=$('#J_submitSuggest');
	J_submitSuggest.tap(function(){
		var content=$.trim(user_suggest.val());
 		if(user_suggest==''){
 			$.dialog({		
	            content:'意见与建议内容不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		var el=$.loading({
	        content:'正在提交'
	    });
	    var DATA={
            waid:$('#waid').val(),
            uid:$('#uid').val(),
	    	content:content
	    };
	    $.post($('#submitUrl').val(),DATA,function(data){
	    	/*
			status=200为成功
			status=其他为网络错误等
			url=跳转链接
	    	*/
	    	if(data.status==200){
	    		document.location.href=data.url;
	    	}else{
	    		$.dialog({
		            content:'网络错误，请重试',
		            button:['我知道了']
		        });
	    	}
	    	el.hide();
	    },'json');
	});
	//var bank_user=$('#bank_user');
	var bank_user2=$('#bank_user2');
	//var bank_phone=$('#bank_phone');
	var bank_userid=$('#bank_userid');
	var bank_name=$('#bank_name');
	var bank_code=$('#bank_code');
	var img=$('#img');
	var img1=$('#img1');
	var img2=$('#img2');
	var ID_img=$('#ID_img');
	var ID_Sideimg=$('#ID_Sideimg');
	var CARD_img=$('#CARD_img');
	var J_submitInfo=$('#J_submitInfo');
	//保存银行卡信息
	J_submitInfo.tap(function(){
		var type=$(this).attr('data-type');

        var user2=$.trim(bank_user2.val());
        var name=$.trim(bank_name.val());
        //var phone=$.trim(bank_phone.val());
        var id=$.trim(bank_userid.val());
        var code=$.trim(bank_code.val());
        var img=$('#img').val();
		var img1=$('#img1').val();
		var img2=$('#img2').val();
        var idimg='',idsideimg='',cardimg='';
        var idimgl='',idsideimgl='',cardimgl='';

        if(type=='add'){
            idimg=ID_img.val();
            idsideimg=ID_Sideimg.val();
            cardimg=CARD_img.val();
            idimgl=ID_img.attr('name');
            idsideimgl=ID_Sideimg.attr('name');
            cardimgl=CARD_img.attr('name');
        }
		if(user2==''){
 			
			if(name&&code){
				
			}else{
				$.dialog({
	            content:'必须选择一种支付方式',
	            button:['我知道了']
					});	
				return false;
			}
			
 		}
		
		
		/*
 		if(user==''){
 			$.dialog({
	            content:'账户名不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.name.test(user)){
 			$.dialog({
	            content:'账户名为2~6字符',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(phone==''){
 			$.dialog({
	            content:'手机号不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.phone.test(phone)){
 			$.dialog({
	            content:'请输入正确的手机号码',
	            button:['我知道了']
	        });
	        return false;
 		}*/
 		if(id==''){
 			$.dialog({
	            content:'请输入您的18号身份证号',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.id.test(id)){
 			$.dialog({
	            content:'请输入正确的身份证号',
	            button:['我知道了']
	        });
	        return false;
 		}
 		// if(name==''){
 		// 	$.dialog({
	  //           content:'开户行信息不能为空',
	  //           button:['我知道了']
	  //       });
	  //       return false;
 		// }
		if(img==''){
			$.dialog({
	            content:'请上传身份证正面图片',
	            button:['我知道了']
	        });
	        return false;
		}
		if(img1==''){
			$.dialog({
	            content:'请上传身份证反面图片',
	            button:['我知道了']
	        });
	        return false;
		}
		/*if(img2==''){
			$.dialog({
	            content:'请上传银行卡正面图片',
	            button:['我知道了']
	        });
	        return false;
		}*/

        // if(type=='add' && idimg=='' && idimgl==''){
        //     $.dialog({
        //         content:'请上传身份证正面图片',
        //         button:['我知道了']
        //     });
        //     return false;
        // }
        // if(type=='add' && idsideimg=='' && idsideimgl==''){
        //     $.dialog({
        //         content:'请上传身份证反面图片',
        //         button:['我知道了']
        //     });
        //     return false;
        // }
        // if(type=='add' && cardimg=='' && cardimgl==''){
        //     $.dialog({
        //         content:'请上传银行卡正面图片',
        //         button:['我知道了']
        //     });
        //     return false;
        // }

 		var el=$.loading({
	        content:'正在提交'
	    });
	    var DATA={
            //type:type,
            //bank_account:user,
            //bank_phone:phone,
			user2:user2,
            bank_idcard:id,
            bank_name:name,
            bank_card:code,
            img:img,
            img1:img1,
            img2:img2,

            /*bank_idcard_img1:idimg,
            bank_idcard_img2:idsideimg,
            bank_card_img:cardimg,
            bank_idcard_img1l:idimgl,
            bank_idcard_img2l:idsideimgl,
            bank_card_imgl:cardimgl*/
	    };
	    $.post(bankinfo,DATA,function(data){
	    	/*
			status=200为成功
			status=其他为网络错误等
			url=跳转链接
	    	*/
	    	if(data.status==1){
	    		var DG=$.dialog({
		            content:'恭喜您，提交成功！',
		            button:['我知道了']
		        });
		        DG.on('dialog:action',function(e){
			        document.location.href=$('#Url').val();
			    });
	    	}else{
	    		$.dialog({
		            content:'网络错误，请重试',
		            button:['我知道了']
		        });
	    	}
	    	el.hide();
	    },'json');
 	});
	
	//选项卡
	var tabYjNav=$('.tab-yj-nav>li');
	var tabYjBox=$('.tab-yf-box');
	tabYjNav.tap(function(){
		$(this).addClass('active').siblings('li').removeClass('active');
		var index=tabYjNav.index(this);
		tabYjBox.eq(index).show().siblings('.tab-yf-box').hide();
	});

	//首页幻灯
	if($('.ui-slider').length>0){
		var slider = new fz.Scroll('.ui-slider', {
	        role: 'slider',
	        indicator: true,
	        autoplay: true,
	        interval: 3000
	    });
	}

    //查看更多
    var showMoreTxt=$('.hpi-block .showmore');
    showMoreTxt.tap(function(){
    	var prev=$(this).prev('p');
    	if($(this).hasClass('close')){
    		prev.addClass('ui-nowrap-multi');
    		$(this).removeClass('close');
    	}else{
    		prev.removeClass('ui-nowrap-multi');
    		$(this).addClass('close');
    	}
    });
    var teamSearch=$('#J_teamSearch');
    var searchBar=$('#J_teamSearch .ui-searchbar');
    var searchInput=$('#J_teamSearch .ui-searchbar-input input');
    var searchInputClose=$('#J_teamSearch .ui-icon-close');
    var searchInputCancle=$('#J_teamSearch .ui-searchbar-cancel');
    var teamSearchItem=$('#J_teamSearchItem')
    searchBar.tap(function(){
	    teamSearch.addClass('focus');
	    searchInput.focus();
	});
	function QueryKey(key){
		teamSearchItem.html('<tr><td colspan="3"><div class="ui-tips ui-loading-wrap"><span class="ui-loading"></span><p>努力搜索中</p></div></td></tr>');
		$.post($('#submitUrl').val(),{keyword:key, waid:$('#waid').val(), uid:$('#uid').val()},function(data){
			/*
			key为空时，回传所有列表
			satus:200为有值，500为空值，其他为错误
			html:'<tr><td>1</td><td>王大宝</td><td data-href="tel:18809990000">18809990000</td></tr><tr><td>1</td><td>王大宝</td><td data-href="tel:18809990000">18809990000</td></tr>'
			*/
			if(data.status==200){
				teamSearchItem.html(data.html);
			}else if(data.status==500){
				teamSearchItem.html('<tr><td colspan="3"><div class="ui-tips ui-tips-info"><i></i>暂无搜索结果</div></td></tr>');
			}else{
				teamSearchItem.html('<tr><td colspan="3"><div class="ui-tips ui-tips-warn"><i></i>网络错误，请刷新页面重试</div></td></tr>');
			}
		},'json');
	}
	searchInputCancle.tap(function(){
	    teamSearch.removeClass('focus');
	    QueryKey('');
	});
	searchInputClose.tap(function(){
		searchInput.val('').focus();
		QueryKey('');
	});
	searchInput.on('input',function(){
		var key=$.trim($(this).val());
		QueryKey(key);
	});

	//推荐客户
	var customer_name=$('#customer_name');
	var customer_phone=$('#customer_phone');
	var customer_project=$('#customer_project');
	var customer_mark=$('#customer_mark');
	var chooseProject=$('#J_chooseProject');
	var J_submitCustomer=$('#J_submitCustomer');

	//添加手机号
	// function AddPhoneDom(n){
	// 	return '<li class="li-phone"><label for="customer_phone_"'+n+' class="label">手机'+n+'</label><input type="text" placeholder="请输入手机号码'+n+'" id="customer_phone" pattern="[0-9]*"><i class="icon-remove"></i></li>';
	// }
	var J_TJJJRPhone=$('#J_TJJJRPhone');
	var AddPhone=$('#J_TJJJRPhone .icon-add');
	var RemovePhone=$('#J_TJJJRPhone .icon-remove');
	var TJJRPhone2=$('#J_TJJJRPhone .li-phone');
	AddPhone.tap(function(){
		TJJRPhone2.show();
		AddPhone.hide();
	});
	//删除手机号
	RemovePhone.tap(function(){
		TJJRPhone2.hide().find('input').val('');
		AddPhone.show();
	});

	var dialogChooseProject=$('.dialog-choose-project');
	var J_submitProjectChoose=$('#J_submitProjectChoose');
	var J_cancelProjectChoose=$('#J_cancelProjectChoose');
	var accountSubmitFixed=$('.account-submit-fixed');

	//项目选择
	chooseProject.tap(function(){
      //  alert(5);
		dialogChooseProject.removeClass('fadeOutDown').show();
		setTimeout(function(){
			accountSubmitFixed.show();
		},600);
		$('html,body').css('overflow','hidden');
	});
	//关闭
	J_cancelProjectChoose.tap(function(){
		dialogChooseProject.addClass('fadeOutDown').hide();
		accountSubmitFixed.hide();
		$('html,body').css('overflow','auto');
	});

	var dcpuiform=$('.dcp-main .ui-form');
	var dcpformitem=$('.dcp-main .ui-form-item');
	dcpformitem.tap(function(){

		var input=$(this).find('input');
       //alert(input);
       // $("#customer_project").val(input.val());
		if(input.prop('checked')){
			//return false;
			input.prop('checked',false);
		}else{
			var checked=dcpuiform.find(':checked');
			//for(var i=0;i<checked.length;i++){
            //checked[i].checked = false;
        //}
			input.prop('checked',true);
		}
	});
	//确定选择
	J_submitProjectChoose.tap(function(){
		var checked=dcpuiform.find(':checked');
		var arr=[];
		checked.each(function(){
			arr.push($(this).val());
		});
		customer_project.val('您已选择了 '+arr.length+' 个项目').attr('data-ids',arr.join(','));
		J_cancelProjectChoose.trigger('tap');
	});

	J_submitCustomer.tap(function(){
 		var name=$.trim(customer_name.val());
 		var phone1=$.trim(customer_phone.val());
 		var pids=customer_project.attr('data-ids');

 		var mark=$.trim(customer_mark.val());
 		if(name==''){
 			$.dialog({
	            content:'姓名不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.name.test(name)){
 			$.dialog({
	            content:'姓名为2~6字符',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(phone1==''){
 			$.dialog({
	            content:'手机号不能为空',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(!REG.phone.test(phone1)){
 			$.dialog({
	            content:'请输入正确的手机号码',
	            button:['我知道了']
	        });
	        return false;
 		}
 		if(pids==''){
 			$.dialog({
	            content:'请选择至少一个楼盘',
	            button:['我知道了']
	        });
	        return false;
 		}
 		var el=$.loading({
	       content:'正在提交'
	    });
	    var DATA={
            //waid:$('#waid').val(),
            //uid:$('#uid').val(),
            customer_name:name,
            customer_phone:phone1,
	    	pids:pids,
            remark:mark
	    };
	    $.post($('#submitUrl').val(),DATA,function(data){

	    	/*
			status=200为成功
			status=其他为网络错误等
			html:反馈结果 DOM结构：'<ul class="dcp-result"><li><div class="div">高新华府<span class="txt color-gray">审核中</span><i class="icon-loader"></i></div></li><li><div class="div">高新华府<span class="txt color-green">推荐成功</span><i class="icon-happy"></i></div></li><li><div class="div">高新华府<span class="txt color-red">推荐失败</span><i class="icon-unhappy"></i></div><p>30天内到访过该项目</p></li></ul>'
			url=跳转链接
	    	*/
			//alert(data);
	    	if(data.status==1){
	    		var DG=$.dialog({
		            content:'推荐成功',
		            button:['我知道了']
		        });
		        DG.on('dialog:action',function(e){
		        	if(e.index==0){
		        		document.location.href=$('#Url').val();
		        	}
			    });
	    	}else if(data.status==2){
	    		$.dialog({
		            content:'此客户已被推荐，请勿重复推荐',
		            button:['我知道了']
		        });
	    	}
	    	else{
	    		$.dialog({
		            content:'网络错误，请重试',
		            button:['我知道了']
		        });
	    	}
	    	el.hide();
	    },'json');
 	});
	//邀请好友
	var inivteFriend=$('#J_inivteFriend');
	var dialogSharetips=$('.dialog-sharetips');
	inivteFriend.on('tap',function(){
		dialogSharetips.show();
	});
	dialogSharetips.on('tap',function(){
		dialogSharetips.hide();
	})
	//查看示例
	var buttonExample=$('.upload-example>button');
	buttonExample.on('tap',function(){
		var img=$(this).attr('data-img');
		$.dialog({
	            content:'<img src="'+img+'" alt="" width="280" />',
	            button:['我知道了']
	        });
	});

	//评价置业顾问
	var customerVeal = $('#customerVeal');
	customerVeal.on('click',function(){
		var customerHtml = '<div class="newdialog-bg"><div class="newdialog"><div class="dialog-zypj"><i data-role="dismiss" class="iconcloase box1"></i><h5>········置业顾问评价········</h5><ul><li><i class=""></i><i class="icon1-pj-hp iconpj" data-pj="positive"></i><br/>好评</li><li><i class=""></i><i class="icon1-pj-zp iconpj" data-pj="moderate"></i><br/>中评</li><li><i class=""></i><i class="icon1-pj-cp iconpj" data-pj="negative"></i><br/>差评</li></ul><div class="div-outarea"><textarea placeholder="可选填" id="pjcontent"></textarea></div><div class="ui-dialog-tj"><button type="button" data-role="button" id="dialogButton0">提交</button></div></div></div></div>';
		/*var dig = $.dialog({		
	        content:customerHtml
	    });*/
		$('#forDialog').html(customerHtml);
		$('#forDialog').show();
		$("#dialogButton0").on("tap",function(){
        	if($('.dialog-zypj ul li i.active').length==0){
	 			alert('请选择评价!');
		        customerVeal.trigger("tap");
		        return false;
	 		}
	 		$('#forDialog').hide();
	    	var el=$.loading({
		        content:'正在提交'
		    });
		    var DATA={
		    	comment_status:$('.dialog-zypj ul li i.active').attr('data-pj'),
		    	content:$('#pjcontent').val(),
	            waid:waid,
	            consultant_uid:consultantUid,
	            jjr_uid:jjrUid,
	            jjr_name:jjrName,
	            jjr_phone:jjrPhone,
	            customer_id:customerId,
	            customer_name:customerName,
	            customer_phone:customerPhone,
	            customer_status:customerStatus,
	            pid:pid
		    };
		    $.post(postUrl,DATA,function(data){
				/*status=200为成功
				status=其他为网络错误等
				url=跳转链接*/
		    	if(data.status==200){
		    		el.hide();
		    		var compDig = $.dialog({
			            content:'提交成功'
			        });
		    		//$(".ui-dialog-ft button").hide();
		    		setTimeout(function(){compDig.hide()},3000);
		    	}else{
			    	el.hide();
		    		$.dialog({
			            content:'网络错误，请重试',
			            button:['我知道了']
			        });
		    	}
		    },'json');
        });
 		$('.dialog-zypj ul li').on('tap',function(){
 			$('.dialog-zypj ul li i.iconpj').removeClass('active');
 			$('.dialog-zypj ul li i').removeClass('icon1-pj-right');
 			$(this).find('i').eq(0).addClass('icon1-pj-right');
 			$(this).find('.iconpj').addClass('active');
 		});
 		$('.iconcloase').on('tap',function() {
 			$('#forDialog').hide();
 		})
 		
	});
	//二维码生成
	var QRcode=$('#J_QR');
    if(QRcode.length==1){
        QRcode.qrcode({width:174,height:174,text:QRcode.attr('data-url')});
    }
	    //发短信验证码
    //验证手机号码

    $(".fa1").click(function(){
        var phone=$("#reg_phone").val();
        var flag=$(this).attr('flag');
        if(phone==''||phone.length!=11){
            $.dialog({
	            content:'请输入正确号码',
	            button:['我知道了']
	        });
            return false;
        }
        if(flag==0){
            return false;
        }
        $(this).text("发送中...");
        $(this).attr('flag','0');
        var url= $("#ruid").val();
        $.post(url,{phone:phone},function(data){
            if(data.status==1){
                $(".fa1").text('发送成功');

            }else if(data.status==2){
                $.dialog({
	            content:'请稍后再发送',
	            button:['我知道了']
	             });
                return false;
            }else{
                $.dialog({
	            content:'验证码发送失败',
	            button:['我知道了']
				});
                $(this).attr('flag','1');
                return false;
            }
        },'json');
    })
    var flag=true;
    $("#J_submitReg1").click(function(){
        if($("input[name=ra1]:checked").length==0){
			$.dialog({
	            content:'你必须同意协议',
	            button:['我知道了']
	        });
			return false;
        }
        var name=$("#reg_name").val();
        if(!name){
            $.dialog({
	            content:'姓名不能为空',
	            button:['我知道了']
	        });
			return false;
        }
        var phone=$("#reg_phone").val();
        if(!phone||phone.length!=11){
            $.dialog({
	            content:'请输入正确的手机号码',
	            button:['我知道了']
	        });
			return false;
        }
        var code=$("#tag_code").val();
        if(!code){
            $.dialog({
	            content:'请输入手机验证码',
	            button:['我知道了']
	        });
            return false;
        }
        var fid1=$(".fid1").val();
        var fid2=$(".fid2").val();
        /*if(!fid1){
            $.dialog({
	            content:'请选择城市',
	            button:['我知道了']
	        });
            return false;
        }
		if(!fid2){
            $.dialog({
	            content:'请选择单位',
	            button:['我知道了']
	        });
            return false;
        }*/
        var y_phone=$("#y_phone").val();
		/*if(y_phone==phone){
            $.dialog({
	            content:'不能推荐自己',
	            button:['我知道了']
	        });
            return false;
        }*/
        if(!flag){
            return false;
        }
        flag=false;
        $("#J_submitReg1").text('提交中...');
        $.post($('#cwaid').val(),{code:code,name:name,phone:phone,fid1:fid1,fid2:fid2,from_phone:y_phone},function(data){
            if(data.status==1){
                $.dialog({
	            content:'申请成功',
	            button:['我知道了']
					});
                setTimeout(function(){
                    location.href= $('#cuid').val();
                },1000)
            }else if(data.status==3){
                $.dialog({
	            content:'该手机号码已经注册',
	            button:['我知道了']
	        });
                $(".code").attr('flag','1');
                $('.code').attr('flag',0);
                $("#J_submitReg1").text('注册');
                flag=true;
            }
            else if(data.status==4){
                $.dialog({
	            content:'该号码已经推荐多个人了',
	            button:['我知道了']
	        });
                $(".code").attr('flag','1');
                $('.code').attr('flag',0);
                $("#J_submitReg1").text('注册');
                flag=true;
            }
			else if(data.status==2){
                $.dialog({
	            content:'手机验证失败',
	            button:['我知道了']
	        });
                $(".code").attr('flag','1');
                $('.code').attr('flag',0);
                $("#J_submitReg1").text('注册');
                flag=true;
            }else{
                $.dialog({
	            content:'申请失败',
	            button:['我知道了']
	        });
                $('.code').attr('flag',0);
                $("#J_submitReg1").text('注册');
                flag=true;
            }
        },'json')


    })
 });