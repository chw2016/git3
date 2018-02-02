/**
 * Validate插件帮助手册
 一、导入jquery和jquery.validate两个js，如果需要也加上validate_helper扩展js
二、默认校验规则
(1)required:true                必输字段
(2)remote:"check.php"      使用ajax方法调用check.php验证输入值
(3)email:true                    必须输入正确格式的电子邮件
(4)url:true                        必须输入正确格式的网址
(5)date:true                      必须输入正确格式的日期 日期校验ie6出错，慎用
(6)dateISO:true                必须输入正确格式的日期(ISO)，例如：2009-06-23，1998/01/22 只验证格式，不验证有效性
(7)number:true                 必须输入合法的数字(负数，小数)
(8)digits:true                    必须输入整数
(9)creditcard:                   必须输入合法的信用卡号
(10)equalTo:"#field"          输入值必须和#field相同
(11)accept:                       输入拥有合法后缀名的字符串（上传文件的后缀）
(12)maxlength:5               输入长度最多是5的字符串(汉字算一个字符)
(13)minlength:10              输入长度最小是10的字符串(汉字算一个字符)
(14)rangelength:[5,10]      输入长度必须介于 5 和 10 之间的字符串")(汉字算一个字符)
(15)range:[5,10]               输入值必须介于 5 和 10 之间
(16)max:5                        输入值不能大于5
(17)min:10                       输入值不能小于10

// 重置表单
validator.resetForm();

8异步验证
remote：URL
使用ajax方式进行验证，默认会提交当前验证的值到远程地址，如果需要提交其他的值，可以使用data选项
远程地址只能输出 "true" 或 "false"，不能有其它输出
remote: "check-email.php"
remote: {
    url: "check-email.php",     //后台处理程序
    type: "post",               //数据发送方式
    dataType: "json",           //接受数据格式
    data: {                     //要传递的数据
        username: function() {
            return $("#username").val();
        }
    }
}

详情请点击页面或者搜索validate插件

http://www.cnblogs.com/hejunrex/archive/2011/11/17/2252193.html
*/

// 手机号码验证
jQuery.validator.addMethod("isPhone", function(value, element) {
    var mobile = /^1[\d]{10}$/;
    return this.optional(element) || (mobile.test(value));
}, "请填写正确的手机号码哦~");

// 手机号码验证
jQuery.validator.addMethod("isEmail", function(value, element) {
    var phone = /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/;
    return this.optional(element) || (phone.test(value));
}, "请填写正确的邮箱哦~");
