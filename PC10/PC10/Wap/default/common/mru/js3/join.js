$(function(){
	if($('#form').length > 0){
		$('#form').validate({
		rules: {
			name : "required",			
   			gender: "required",
			age : "required",
			educate : "required",
			jiguan : "required",
			phone : "required",
			email : "required",
			address : "required"
		},
		messages: {
   			name: "请输入姓名",
   			gender: "请选择性别",
   			age: "请输入年龄",
   			educate: "请选择学历",
   			phone: "请填写电话",
   			jiguan: "请填写籍贯",
   			email: "请填写邮箱",
			address : "请填写地址"
   		},
   		errorPlacement: function(error, element) {  
   			var tipsContent = error.html();
		    msg.alert(tipsContent,"知道了",function(){});
		    //error.appendTo(element.parent());  
		    return false;
		},
		submitHandler:function(form){
            alert("submitted");   
            //数据
            var data = {};
            data.name= $("[name='name']").val();		
   			data.gender= $("[name='gender']").val();
			data.age = $("[name='age']").val();
			data.educate = $("[name='educate']").val();
			data.phone = $("[name='phone']").val();
			data.jiguan = $("[name='jiguan']").val();
			data.email = $("[name='email']").val();
			data.address = $("[name='address']").val();
			console.log(data)
        },    
		onkeyup: false,
		onfocusout: false
	});
	}
	
	$('.submit').click(function(){
		$('form').trigger('submit');
	})
})
