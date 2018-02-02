//开启右上角分享按钮
function onBridgeReady(){
    WeixinJSBridge.call('showOptionMenu');
}
if(typeof WeixinJSBridge == "undefined"){
    if( document.addEventListener ){
        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
    }else if (document.attachEvent){
        document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
    }
}else{
    onBridgeReady();
}
//微信分享信息定义
function _WXShare(obj){
    //初始化参数
    var img,width,height,title,desc,url,appid;
    img=obj.img || 'http://a.zhixun.in/plug/img/ico-share.png';
    width=obj.width||100;
    height=obj.height||100;
    title=obj.title||document.title;
    desc=obj.desc||document.title;
    url=obj.url||document.location.href;
    appid=obj.appid||'';
    //微信内置方法
    function _ShareFriend() {
        WeixinJSBridge.invoke('sendAppMessage',{
            'appid': appid,
            'img_url': img,
            'img_width': width,
            'img_height': height,
            'link': url,
            'desc': desc,
            'title': title
            }, function(res){
              _report('send_msg', res.err_msg);
        })
    }
    function _ShareTL() {
        WeixinJSBridge.invoke('shareTimeline',{
              'img_url': img,
              'img_width': width,
              'img_height': height,
              'link': url,
              'desc': desc,
              'title': title
              }, function(res) {
              _report('timeline', res.err_msg);
        });
    }
    function _ShareWB() {
        WeixinJSBridge.invoke('shareWeibo',{
              'content': desc,
              'url': url,
              }, function(res) {
              _report('weibo', res.err_msg);
        });
    }
    // 当微信内置浏览器初始化后会触发WeixinJSBridgeReady事件。
    document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
            // 发送给好友
            WeixinJSBridge.on('menu:share:appmessage', function(argv){
                _ShareFriend();
          });
            // 分享到朋友圈
            WeixinJSBridge.on('menu:share:timeline', function(argv){
                _ShareTL();
                });

            // 分享到微博
            WeixinJSBridge.on('menu:share:weibo', function(argv){
                _ShareWB();
           });
    }, false);
}
//横屏时提示
// window.addEventListener("orientationchange", function(){
//     if(window.orientation != 0){
//         alert('为保证最佳浏览体验，请使用竖屏 :)');
//     }
// }, false);

_WXShare({
  img:_shareImg,
  title:_shareTitle,
  desc:_shareDesc,
  url:_shareUrl
});

KISSY.use('node,gallery/slide/1.2/index',function(S,Node,Slide){
  var $=Node.all;
  //幻灯
  if($('#J_inSlider').length==1){
    var UISlider=new Slide('J_inSlider',{
      autoSlide:true,
      effect:'hSlide',
      timeout:3000,
      speed:300,
      eventType:'mouseover',
      triggerDelay:200,
      selectedClass:'current',
      carousel:true,
      touchmove:true,
      adaptive_width:function(){
        return S.DOM.width(window);
      }
    });
  }
  // //通用导航显示隐藏
  // var baseNav=$('.plug-menu');
  // baseNav.on('click',function(){
  //   var span = $(this).children('span');
  //   if(span.attr('class') == 'open'){
  //           span.removeClass('open');
  //           span.addClass('close');
  //           $('.plug-btn').removeClass('open');
  //           $('.plug-btn').addClass('close');
  //   }else{
  //           span.removeClass('close');
  //           span.addClass('open');
  //           $('.plug-btn').removeClass('close');
  //           $('.plug-btn').addClass('open');
  //   }
  // });
  // baseNav.on('touchmove',function(event){event.preventDefault();});
  //项目详情页，展开更多
  var projecDMore=$('#J_detailContent a.more');
  var projectDIntro=$('#J_detailContent .intro');
  var projectDTxt=$('#J_detailContent .txt');
  projecDMore.on('click',function(){
    if($(this).hasClass('more-click')){
      $(this).removeClass('more-click').html('更多<span></span>');
      projectDIntro.show();
      projectDTxt.hide();
    }else{
      $(this).addClass('more-click').html('收起<span></span>');
      projectDIntro.hide();
      projectDTxt.show();
    }
  });
  //图片放大
  var zoomImgEl=$('*[data-rel=zoomImg] img');
  var BODY=$('body');
  //预览大图
  var styleTxt='<style id="J_imgzoomStyle">body,html{overflow: hidden;}::-webkit-scrollbar{width:0px;z-index: 90;}</style>';
  var J_dialogZoom=$('#J_dialogZoom');
  var dialogImages=$('.dialog-imgzoom>.images');
  var dialogClose=$('.dialog-imgzoom-close');
  var dialogTips=$('.dialog-imgzoom-tips');
  zoomImgEl.on('tap',function(){
        var src=$(this).attr('src') || '';
        if(src!=''){
            BODY.prepend(styleTxt);
            dialogImages.children('img').attr('src',src);
            J_dialogZoom.show();
            setTimeout(function(){
                dialogTips.removeClass('fadeIn').addClass('fadeOut');
            },2000);
        }
    });
    dialogImages.on('tap',function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).css('width','100%');
            // $(window).off('scroll.elasticity').off('touchmove.elasticity');
        }else{
            $(this).addClass('active');
            $(this).css('width','250%');
        }
    });
    dialogClose.on('tap',function(){
        J_dialogZoom.hide();
        dialogImages.removeClass('active');
        $('#J_imgzoomStyle').remove();
    });
});