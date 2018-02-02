/* 
* @Author: Administrator
* @Date:   2015-09-09 11:21:01
* @Last Modified by:   Administrator
* @Last Modified time: 2015-09-09 17:16:35
*/

$(function(){
	$(".btn-submit").click(function(){
		var user=$("[name=user]").val();
		var tel=$("[name=tel]").val();
		var home=$("[name=home]").val();
		var file=$("[name=file]").val();
		if(user==''){
			msg.alert("您还没有填写姓名,请输入姓名");
			$("[name=user]").focus();
			return false;
		}
		if(tel==''){
			msg.alert("您还没有填写手机,请输入手机");
			$("[name=tel]").focus();
			return false;
		}else{
			if(tel.length!=11){
				msg.alert("您输入的手机号码不正确");
				$("[name=tel]").focus();
				return false;
			}
		}
		if(home==''){
			msg.alert("您还没有填写地址,请输入地址");
			$("[name=home]").focus();
			return false;
		}
		if(file==''){
			msg.alert("您还没上传刮刮卡图片")
		}
		$(".btn-submit").click(function(){
		$('.Mask').addClass('is-visible');
		})
		$(".icon-cancel").click(function(){
			$('.Mask').removeClass('is-visible');
		})
	})
})