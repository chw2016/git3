
var shareUrl = "";
window.onload = function () {
    scroll();
}

//分享
function wx_share_out(shareTitle,imgUrl,descContent,shareUrl) {
    var appid = '';
    var lineLink = shareUrl;
    if(imgUrl=='' || imgUrl=='0' || imgUrl=='null') {
        var imgs = document.getElementsByTagName("img");
        if(imgs.length>0) {
            var urlm = /http:\/\//i;
            imgUrl = imgs[0].src;
            if(urlm.test(imgUrl)) {} else {
                //if(imgUrl.indexOf('http:\/\/')>0) { } else {
                imgUrl = 'http://'+window.location.host+imgUrl;
            }
        }
        //imgUrl = $("img:first").attr('src');
    }

    function shareFriend() {
        WeixinJSBridge.invoke('sendAppMessage',{
            "appid": appid,
            "img_url": imgUrl,
            "img_width": "200",
            "img_height": "200",
            "link": lineLink,
            "desc": descContent,
            "title": shareTitle
        }, function(res) {
            //_report('send_msg', res.err_msg);
        })
    }
    function shareTimeline() {
        WeixinJSBridge.invoke('shareTimeline',{
            "img_url": imgUrl,
            "img_width": "200",
            "img_height": "200",
            "link": lineLink,
            "desc": descContent,
            "title": shareTitle
        }, function(res) {
            //_report('timeline', res.err_msg);
        });
    }
    function shareWeibo() {
        WeixinJSBridge.invoke('shareWeibo',{
            "content": descContent,
            "url": lineLink,
        }, function(res) {
            //_report('weibo', res.err_msg);
        });
    }
    // 当微信内置浏览器完成内部初始化后会触发WeixinJSBridgeReady事件。
    document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
        // 发送给好友
        WeixinJSBridge.on('menu:share:appmessage', function(argv){
            shareFriend();
        });
        // 分享到朋友圈
        WeixinJSBridge.on('menu:share:timeline', function(argv){
            shareTimeline();
        });
        // 分享到微博
        WeixinJSBridge.on('menu:share:weibo', function(argv){
            shareWeibo();
        });
    }, false);
}

/*
 * 删除左右两端的空格
 */
function Trim(str) {
    return str.replace(/(^\s*)|(\s*$)/g, "");
}

/*
 * 定义数组
 */
function GetSide(m, n) {
    //初始化数组
    var arr = [];
    for (var i = 0; i < m; i++) {
        arr.push([]);
        for (var j = 0; j < n; j++) {
            arr[i][j] = i * n + j;
        }
    }
    //获取数组最外圈
    var resultArr = [];
    var tempX = 0,
        tempY = 0,
        direction = "Along",
        count = 0;
    while (tempX >= 0 && tempX < n && tempY >= 0 && tempY < m && count < m * n) {
        count++;
        resultArr.push([tempY, tempX]);
        if (direction == "Along") {
            if (tempX == n - 1)
                tempY++;
            else
                tempX++;
            if (tempX == n - 1 && tempY == m - 1)
                direction = "Inverse"
        }
        else {
            if (tempX == 0)
                tempY--;
            else
                tempX--;
            if (tempX == 0 && tempY == 0)
                break;
        }
    }
    return resultArr;
}

var index = 0,           //当前亮区位置
    prevIndex = 0,          //前一位置
    Speed = 300,           //初始速度
    Time,            //定义对象
    arr = GetSide(3, 4),         //初始化数组
    EndIndex = 0,           //决定在哪一格变慢
    tb = document.getElementById("tb"),     //获取tb对象
    cycle = 0,           //转动圈数
    EndCycle = 0,           //计算圈数
    flag = false,           //结束转动标志
    quick = 0;           //加速
//btn = document.getElementById("btn1")

function StartGame(num, result, islottery) {
    clearInterval(Time);
    cycle = 0;
    flag = false;
    //EndIndex = Math.floor(Math.random() * 10);
    //EndIndex = EndIndex > 10 || EndIndex == 0 ? 10 : EndIndex;
    EndIndex = 7;
    EndCycle = Math.floor(Math.random() * (10 - 5) + 5);
    //EndCycle = 5;
    Time = setInterval("Star(" + num + ",'" + result + "'," + islottery + ")", Speed);
}
function Star(num, result, islottery) {
    //跑马灯变速
    if (flag == false) {
        //走五格开始加速
        if (quick == 3) {
            clearInterval(Time);
            Speed = 50;
            Time = setInterval("Star(" + num + ",'" + result + "'," + islottery + ")", Speed);
        }
        if (num == 10) {
            //跑N圈减速
            if (cycle == (EndCycle + 1) && index == parseInt(1)) {
                clearInterval(Time);
                Speed = 300;
                flag = true;       //触发结束
                Time = setInterval("Star(" + num + ",'" + result + "'," + islottery + ")", Speed);

            }
        } else {
            //跑N圈减速
            if (cycle == (EndCycle + 1) && index == parseInt(EndIndex)) {
                clearInterval(Time);
                Speed = 300;
                flag = true;       //触发结束
                Time = setInterval("Star(" + num + ",'" + result + "'," + islottery + ")", Speed);

            }
        }
    }

    if (index >= arr.length) {
        index = 0;
        cycle++;
    }

    //结束转动并选中号码
    //trim里改成数字就可以减速，变成Endindex的话就没有减速效果了
    if (flag == true && index == parseInt(Trim('' + num)) - 1) {
        quick = 0;
        clearInterval(Time);
    }
    var tb=document.getElementById("tb");
    tb.rows[arr[index][0]].cells[arr[index][1]].className = "playcurr";
    if (index > 0)
        prevIndex = index - 1;
    else {
        prevIndex = arr.length - 1;
    }
    tb.rows[arr[prevIndex][0]].cells[arr[prevIndex][1]].className = "playnormal";
    index++;
    quick++;
    if (flag == true && index == parseInt(Trim('' + num)) && islottery) {
        //抽奖次数加1
        document.getElementById("cj_lottery_num").value = parseInt(document.getElementById("cj_lottery_num").value) + 1;
        alertTips("2", document.getElementById("cj_lottery_num").value);
        //document.getElementById("cwxCn").innerHTML = "恭喜你！中了" + result + "！" + "&nbsp;<a href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx1f64bd08be82ec7f&redirect_uri=http%3a%2f%2fwww.wangtu.com%2fweixin%2fpage%2fmenu%2fGetPrize.aspx&response_type=code&scope=snsapi_base&state=1#wechat_redirect'>领取奖品>></a>";
        cwxbox.box.show("恭喜你！中了" + result + "！" + "&nbsp;<a href="+geturl+">领取奖品>></a>");
        $(".times").html($(".times").html()-1);
        document.getElementById("liji1").innerHTML = "再抽一次";
        choujianging = false;
    } else if (flag == true && index == parseInt(Trim('' + num))) {
        //抽奖次数加1
        document.getElementById("cj_lottery_num").value = parseInt(document.getElementById("cj_lottery_num").value) + 1;
        alertTips("2", document.getElementById("cj_lottery_num").value);
        //document.getElementById("cwxCn").innerHTML = "好可惜哦，只差一点点，如果再抽" + result + "次还不中，就送你一次必中！";
        cwxbox.box.show("好可惜哦，只差一点点，如果再抽" + result + "次还不中，就送你一次必中！");
        document.getElementById("liji1").innerHTML = "再抽一次";
        choujianging = false;
    }

}
//AJAX
var xmlHttp;      //这个函数是一直固定不变的，你只需要调用它，检验是否能创建 XMLHttpRequest对象
function createXmlHttpRequest() {
    if (window.XMLHttpRequest) {
        xmlHttp = new XMLHttpRequest();

        if (xmlHttp.overrideMimeType) {
            xmlHttp.overrideMimeType("text/xml");
        }
    }
    else if (window.ActiveXObject) {
        try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    if (!xmlHttp) {
        //window.alert
        cwxbox.box.show("你的浏览器不支持创建XMLhttpRequest对象 \n- 请换用IE 或 火狐");
    }
    return xmlHttp;
}

var choujianging = false;


//统计浏览
//function InsertBrowse() {
//    var url = document.getElementById("HIDurl").value;
//    var purl = document.getElementById("HIDpurl").value;
//    var ip = document.getElementById("HIDip").value;
//    var id = document.getElementById("HIDdlid").value;

//    var type = "1";
//    xmlHttp.open("GET", "/weixin/ajax/Program.aspx?op=InsertBrowse&url=" + url + "&ip=" + ip + "&id=" + id + "&purl=" + purl + "&cache=" + Math.random(), true);
//    xmlHttp.onreadystatechange = InsertBrowseResult;
//    xmlHttp.send(null);

//}


//function InsertBrowseResult() {
////    var result = xmlHttp.responseText;
////    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {

////    }
//}

function ChouJiang() {
    //window.scrollTo(0, document.getElementById("kuang1").offsetTop);
    //cwxbox.box.show(" 本活动已结束，请到【网途】公众号参加新的抽奖活动！");
    xmlHttp=createXmlHttpRequest();
    var openid = document.getElementById("user_id").value;
    if (openid == null || openid == "") {
        cwxbox.box.show("连接失败，请重新打开页面，如不能解决问题请更换手机再试！");
    } else {
        $.post(url,function(data){
            if(data.status==1){
                StartGame(data.data.row, data.info,true);
            }else{
                alert(data.info);
            }
        },"json");
    }
}


function GetResult() {

    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
        var result1 = xmlHttp.responseText;
        //alert(result1);
        var r = result1.split(";");
        if (r[0] == "true") {

            StartGame(10, "" + r[1], true);

        } else {
            if (r[1] == "true") {
                StartGame(6, "" + r[2], false);
            } else {
                document.getElementById("cwxCn").innerHTML = r[2];
                cwxbox.box.show(document.getElementById("cwxCn").innerHTML);
                choujianging = false;
            }
        }

    }
}

function alertTips(type, count) {
    if (type == "1") {
        switch (count) {
            case "1": document.getElementById("cwxBg").setAttribute("onclick", "cwxbox.box.hide(1)"); document.getElementById("cwxBg_exit").setAttribute("onclick", "cwxbox.box.hide(1)"); break;
            case "2": document.getElementById("cwxBg").setAttribute("onclick", "cwxbox.box.hide(2)"); document.getElementById("cwxBg_exit").setAttribute("onclick", "cwxbox.box.hide(2)"); break;
            default: document.getElementById("cwxBg").setAttribute("onclick", "cwxbox.box.hide()"); document.getElementById("cwxBg_exit").setAttribute("onclick", "cwxbox.box.hide()"); break;
        }
    }
    else {
        switch (count) {
            case "2": document.getElementById("cwxBg").setAttribute("onclick", "cwxbox.box.hide(2)"); document.getElementById("cwxBg_exit").setAttribute("onclick", "cwxbox.box.hide(2)"); break;
            default: document.getElementById("cwxBg").setAttribute("onclick", "cwxbox.box.hide()"); document.getElementById("cwxBg_exit").setAttribute("onclick", "cwxbox.box.hide()"); break;
        }
    }
}

function GetUserInfoList() {
    if (createXmlHttpRequest()) {
        xmlHttp.open("GET", "/weixin/ajax/Program.aspx?op=GetUserInfoList&cache=" + Math.random(), true);
        xmlHttp.onreadystatechange = GetLotteryInfoListResult;
        xmlHttp.send(null);
    }
}
function GetLotteryInfoListResult() {

    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
        var result1 = xmlHttp.responseXML;
        //alert(xmlHttp.responseText);
        var reg = /(\d{3})\d{4}(\d{4})/;
        var xmlDoc = result1;
        document.getElementById("lie1").innerHTML = "";
        var nodeList = xmlDoc.documentElement.getElementsByTagName("ds");  // IE  
        for (var i = 0; i < nodeList.length; i++) {
            document.getElementById("lie1").innerHTML += "<li><b class=\"xinxi1\">" + (nodeList[i].getElementsByTagName("phone"))[0].firstChild.nodeValue.replace(reg, "$1****$2") + "</b><b class=\"xinxi2\">" + namereplace((nodeList[i].getElementsByTagName("username"))[0].firstChild.nodeValue) + "</b><b class=\"xinxi3\">奖品已领取</b></li>";
        }
        scroll();
        //InsertBrowse();
        document.getElementById("over").style.display = "none";
    }

}

function scroll() {
    var speed = 250;
    var liebiao = document.getElementById("liebiao");
    var liebiao2 = document.getElementById("lie2");
    var liebiao1 = document.getElementById("lie1");
    liebiao2.innerHTML = liebiao1.innerHTML
    function Marquee() {
        if (liebiao2.offsetTop - liebiao.scrollTop <= 0)
            liebiao.scrollTop -= liebiao1.offsetHeight
        else {
            liebiao.scrollTop++
        }
    }
    var MyMar = setInterval(Marquee, speed)
    liebiao.onmouseover = function () { clearInterval(MyMar) }
    liebiao.onmouseout = function () { MyMar = setInterval(Marquee, speed) }
}

function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
    var flags = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) { flags = false; break; }
    }
    return flags;
}

function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}
function namereplace(name) {
    var n = name.split("");
    for (i = 1; i < n.length; i++) {
        n[i] = n[i].replace(n[i], "*");
    }
    return n.join("");
}