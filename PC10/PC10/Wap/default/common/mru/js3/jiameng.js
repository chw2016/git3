$(function(){
	if($('#form').length > 0){
		$('#form').validate({
		rules: {
			name : "required",			
   			gender: "required",
			age : "required",
			educate : "required",
			phone : "required",
			email : "required",
			position : "required",
			area : "required",
			touzi : "required",
			totouzi : "required",
			isZijin : "required",
			project : "required",
			mianji : "required",
			team_nums : "required",
			is_project_crp : "required",
			address : "required",
			detail : "required"
		},
		messages: {
   			name: "请输入申请人",
   			gender: "请选择性别",
   			age: "请输入年龄",
   			educate: "请选择学历",
   			phone: "请填写电话",
   			email: "请填写邮箱",
   			position : "请填写现从事职业",
			area : "请填写拟加盟合作区域",
			touzi : "请填写现有投资规模：",
			totouzi : "请填写拟投资规模",
			isZijin : "请选择是否自有资金",
			project : "现经营项目",
			mianji : "请填写店面面积",
			team_nums : "请填写店面团队人数",

			is_project_crp : "请选择是否整体项目合作",
			address : "请填写地址",
			detail : "请填写加盟需求说明"
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
			data.email = $("[name='email']").val();
			data.position = $("[name='position']").val();
			data.area = $("[name='area']").val();
			data.touzi = $("[name='touzi']").val();
			data.totouzi = $("[name='totouzi']").val();
			data.isZijin = $("[name='isZijin']").val();
			data.project = $("[name='project']").val();
			data.mianji = $("[name='mianji']").val();
			data.team_nums = $("[name='team_nums']").val();
			data.is_project_crp = $("[name='is_project_crp']").val();
			data.address = $("[name='address']").val();
			data.detail = $("[name='detail']").val();
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
