// 对Date的扩展，将 Date 转化为指定格式的String 
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符， 
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字) 
// 例子： 
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423 
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18 
Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1,                 //月份 
        "d+": this.getDate(),                    //日 
        "h+": this.getHours(),                   //小时 
        "m+": this.getMinutes(),                 //分 
        "s+": this.getSeconds(),                 //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds()             //毫秒 
    };
    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

Date.prototype.GetZoneTime = function (offset) {
    var d = new Date();
    var localTime = d.getTime();
    var localOffset = d.getTimezoneOffset() * 60000; //获得当地时间偏移的毫秒数
    var utc = localTime + localOffset; //utc即GMT时间
    return new Date(utc + (3600000 * offset));
}

var menudata = {
    "n1": [["董事长致词", "chairman.aspx"], ["我们目标", "infor.aspx?info1001"], ["公司架构", "infor.aspx?info1002"], ["核心管理理念", "infor.aspx?info1003"], ["人力资源理念", "infor.aspx?info1004"], ["媒体报道", "newslist.aspx?tid=0"]],
    "n2": [["实物金银", "infor.aspx?info1006"], ["预付款买卖", "infor.aspx?info1007"], ["保本理财", "infor.aspx?info1008"], ["黄金定投", "infor.aspx?info1009"]],
    //"n6": [["代理商登录", "javascript:;"], ["客户登录", "javascript:;"]],
    "n3": [["合作优势", "infor.aspx?info1010"], ["合作支持", "infor.aspx?info1011"], ["加盟流程", "infor.aspx?info1012"], ["加盟条件", "infor.aspx?info1013"], ["Q＆A", "infor.aspx?info1028"]],
    "n4": [["黄金基础知识", "infor.aspx?info1014"], ["黄金投资的优势", "infor.aspx?info1015"], ["影响金价因素", "infor.aspx?info1016"], ["专业名词", "infor.aspx?info1017"]],
    "n5": [["常见问题", "infor.aspx?info1018"], ["招贤纳士", "infor.aspx?info1019"]]
};
var fillmenu = function (o) {
    var n = $(o).attr("m");
    var fl = window.location.pathname.toLowerCase();
    fl = fl.substring(fl.lastIndexOf("/") + 1);
    if (fl == "") fl = "default.aspx";
    var iss = false;
    if (fl == tu) { $(".m1").removeClass("hover"); $(o).addClass("hover"); }
    if (n != undefined) {
        var sm = eval("menudata.n" + n);
        $(".submenu").html("");
        for (var i = 0; i < sm.length; i++) {
            if (!iss) {
                var tu = sm[i][1].toLowerCase();
                tu = tu.substring(tu.lastIndexOf("/") + 1);
                iss = (fl == tu);
            }
            $("<a href=\"" + sm[i][1] + "\">" + sm[i][0] + "</a>").appendTo(".submenu");
        }
        $(".submenu").css("left", $(o).attr("pleft") + "px");
    } else
        $(".submenu").html("");
    var murl = $(o).attr("href").toLowerCase();
    murl = murl.substring(murl.lastIndexOf("/") + 1);
    if (iss || fl == murl) {
        $(".m1").removeClass("hover");
        $(o).addClass("hover");
    }
}

//s:菜單標題容器, d:選項卡內容容器, f:“被選擇”樣式類定義, c:如果菜單項為標題子標籤，則需要設置對應選擇器
var switchtab = function (s, d, f, c) {
    $(s).click(function () {
        if (c != "" && c != undefined)
            $(this).children(c).addClass(f).parent().siblings().children(c).removeClass(f);
        else
            $(this).addClass(f).siblings().removeClass(f);
        $($(d).get($(this).index())).show().siblings(d).hide();
    });
};

$(function () {
    $(".logout_bx").click(function () {
        var rt = confirm("确定要退出？");
        if (rt) {
            $.post("/common/procdata.ashx", { a: 2 }, function (data) {
                var result = eval("(" + data + ")");
                alert(result.msg);
                if (result.code == 0)
                    window.location.reload();
            });
        }
        return false;
    });
});