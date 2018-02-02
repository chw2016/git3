/**
 * Created by Sean on 14-4-16.
 */
var WX = function () {
    var handleSubscribe = function(){
        jQuery('#subscribe').on('click',function(){
            var ua = navigator.userAgent;
            if(ua.indexOf('iPhone') > -1 || ua.indexOf('Mac') > -1){
                if (typeof WeixinJSBridge != "undefined" && WeixinJSBridge.invoke){
                    WeixinJSBridge.invoke('profile',{
                        'username':$(this).attr('tag'),
                        'scene':'57'
                    });
                }
            }
            else{
                window.location.href = 'weixin://contacts/profile/'+$(this).attr('tag');
            }
        });
    }

    return {
        init: function (imgUrl,link,desc,title,appid) {
            handleSubscribe();

            function shareFriend() {
                WeixinJSBridge.invoke('sendAppMessage',{
                    "appid": appid,
                    "img_url": imgUrl,
                    "img_width": "50",
                    "img_height": "50",
                    "link": link,
                    "desc": desc,
                    "title": title
                }, function() {});
            }
            function shareTimeline() {
                WeixinJSBridge.invoke('shareTimeline',{
                    "img_url": imgUrl,
                    "img_width": "50",
                    "img_height": "50",
                    "link": link,
                    "desc": desc,
                    "title": title
                }, function() {});
            }

            document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
                WeixinJSBridge.on('menu:share:appmessage', function(argv){
                    shareFriend();
                });

                WeixinJSBridge.on('menu:share:timeline', function(argv){
                    shareTimeline();
                });
            }, false);
        }
    };
}();