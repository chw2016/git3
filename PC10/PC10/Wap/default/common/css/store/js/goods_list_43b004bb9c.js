define("wap/showcase/modules/goods_list/box_stream", [],
function() {
    var t = function(t) {
        this._conf = {
            container: null,
            rowCount: 4,
            rowContainerClassName: "",
            rowContainerTagType: "div",
            rowCintainerWidths: [],
            _rowContainers: []
        },
        t && this.init(t)
    };
    return t.prototype = {
        init: function(t) {
            t = this._conf = $.extend(this._conf, t || {}),
            this._initRowContainers(t)
        },
        _initRowContainers: function(t) {
            var i, n, o, e, o = $(t.container).children();
            for (e = t.rowCount, i = 0; e > i; i++) n = document.createElement(t.rowContainerTagType),
            n.className = t.rowContainerClassName,
            n.style.cssText = "float:left;z-index:" + (e - i),
            t.rowCintainerWidths[i] && (n.style.width = t.rowCintainerWidths[i] + "px"),
            t.container.insertBefore(n, o[0]),
            t._rowContainers[i] = n;
            for (n = t._rowContainers, i = 0; i < o.length; i++) n[i % e].appendChild(o[i]);
            i = null,
            n = null,
            o = null,
            e = null
        },
        _sortOut: function() {
            var t, i, n, o, e = this._conf,
            s = e._rowContainers,
            a = 0,
            r = 0,
            h = 0,
            l = -1;
            for (i = 0; i < s.length; i++) t = s[i].offsetHeight,
            t > h && (h = t, a = i),
            (0 > l || l > t) && (l = t, r = i);
            return o = $(s[a]).children().last()[0],
            t = o.offsetHeight,
            h - l > t ? (s[r].appendChild(o), n = s[r].offsetHeight >= h ? !1 : !0) : n = !1,
            e = null,
            s = null,
            a = null,
            r = null,
            h = null,
            l = null,
            t = null,
            i = null,
            o = null,
            n
        },
        sortOut: function(t) {
            for (; this._sortOut(););
            "function" == typeof t && t()
        }
    },
    t
}),
window.Utils = window.Utils || {},
$.extend(window.Utils, {
    makeRandomString: function(t) {
        var i = "",
        n = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        t = t || 10;
        for (var o = 0; t > o; o++) i += n.charAt(Math.floor(Math.random() * n.length));
        return i
    }
}),
define("wap/components/util/number",
function() {}),
define("wap/components/pop", ["wap/components/util/number"],
function() {
    var t = function() {},
    i = Backbone.View.extend({
        initialize: function(i) {
            var n = window.Utils.makeRandomString();
            $("body").append('<div id="' + n + '" class="' + i.className + '" style="overflow:hidden;visibility: hidden;"></div>'),
            this.nPopContainer = $("#" + n);
            var o = window.Utils.makeRandomString();
            $("body").append('<div id="' + o + '"                 style="display:none; width: 100%; height: 100%;                 position: absolute; top:0; left:0;                 background-color: rgba(0, 0, 0, ' + (i.transparent || ".9") + ');z-index:1000;opacity:0;transition: opacity ease 0.2s;"></div>'),
            this.nBg = $("#" + o),
            this.nBg.on("click", _(this.hide).bind(this)),
            i.contentViewClass && (this.contentViewClass = i.contentViewClass, this.contentViewOptions = _.extend({
                el: this.nPopContainer
            },
            i.contentViewOptions || {}), this.contentView = new this.contentViewClass(_.extend({
                onHide: _(this.hide).bind(this)
            },
            this.contentViewOptions))),
            this.animationTime = i.animationTime || 1e3,
            this.isCanNotHide = i.isCanNotHide,
            this.onShow = i.onShow || t,
            this.onHide = i.onHide || t,
            this.html = i.html
        },
        render: function(t) {
            return this.renderOptions = t || {},
            this.contentViewClass ? this.contentView.render(this.renderOptions) : this.html && this.nPopContainer.html(this.html),
            this
        },
        show: function() {
            var t = $(window);
            return this.top = t.scrollTop(),
            $("html").css("position", "relative"),
            this.nBg.show().css({
                opacity: "1",
                "transition-property": "none"
            }),
            setTimeout(_(function() {
                t.scrollTop(0),
                $("body,html").css({
                    overflow: "hidden",
                    height: t.height()
                }),
                this.nPopContainer.css("visibility", "visible"),
                this.doShow && this.doShow(),
                this.onShow()
            }).bind(this), 200),
            this
        },
        hide: function() {
            var t = $(window);
            this.isCanNotHide || (this.doHide && this.doHide(), setTimeout(_(function() {
                $("html,body").css({
                    overflow: "visible",
                    height: "auto"
                });
                var i = this.top;
                if (t.scrollTop(i), i !== t.scrollTop()) {
                    var n = function() {
                        t.scrollTop(i),
                        i !== t.scrollTop() && setTimeout(n)
                    };
                    setTimeout(n)
                }
                this.nBg.css({
                    opacity: 0,
                    "transition-property": "opacity"
                }),
                setTimeout(_(function() {
                    this.destroy(),
                    $("html").css("position", "static")
                }).bind(this), 200)
            }).bind(this), this.animationTime), this.onHide())
        },
        destroy: function() {
            this.nPopContainer.remove(),
            this.nBg.remove(),
            this.contentView && this.contentView.remove(),
            this.remove()
        }
    });
    return i
}),
define("wap/components/popup", ["wap/components/pop"],
function(t) {
    var i = t.extend({
        initialize: function(i) {
            this.onClickBg = i.onClickBg ||
            function() {},
            this.onBeforePopupShow = i.onBeforePopupShow ||
            function() {},
            this.onAfterPopupHide = i.onAfterPopupHide ||
            function() {},
            t.prototype.initialize.apply(this, [i]),
            this.nPopContainer.css("opacity", "0")
        },
        events: {},
        doShow: function() {
            this.contentView && this.contentView.height ? this.height = this.contentView.height() : this.contentView || (this.height = this.nPopContainer.height()),
            this.onBeforePopupShow(),
            this.nPopContainer.css({
                height: this.height + "px",
                transform: "translate3d(0,100%,0)",
                "-webkit-transform": "translate3d(0,100%,0)",
                opacity: .5,
                position: "absolute",
                "z-index": 1100
            }),
            this.bodyPadding = $("body").css("padding"),
            $("body").css("padding", "0px"),
            setTimeout(_(function() {
                this.nPopContainer.css({
                    transform: "translate3d(0,0,0)",
                    "-webkit-transform": "translate3d(0,0,0)",
                    "-webkit-transition": "all ease " + this.animationTime + "ms",
                    transition: "all ease " + this.animationTime + "ms",
                    opacity: 1
                })
            }).bind(this)),
            setTimeout(_(function() {
                this.contentView && this.contentView.onAfterPopupShow && this.contentView.onAfterPopupShow()
            }).bind(this), this.animationTime)
        },
        doHide: function() {
            this.nPopContainer.css({
                transform: "translate3d(0,100%,0)",
                "-webkit-transform": "translate3d(0,100%,0)",
                opacity: .5
            }),
            setTimeout(_(function() {
                $("body").css("padding", this.bodyPadding),
                this.onAfterPopupHide()
            }).bind(this), this.animationTime)
        }
    });
    return i
}),
window.Zepto &&
function(t) { ["width", "height"].forEach(function(i) {
        var n = i.replace(/./,
        function(t) {
            return t[0].toUpperCase()
        });
        t.fn["outer" + n] = function(t) {
            var n = this;
            if (n) {
                var o = n[i](),
                e = {
                    width: ["left", "right"],
                    height: ["top", "bottom"]
                };
                return e[i].forEach(function(i) {
                    t && (o += parseInt(n.css("margin-" + i), 10))
                }),
                o
            }
            return null
        }
    })
} (Zepto),
define("vendor/zepto/outer",
function() {}),
require(["wap/showcase/modules/goods_list/box_stream", "wap/components/popup", "vendor/zepto/outer"],
function(t, i) { !
    function(i) {
        var n = i(".sc-goods-list.waterfall");
        n.length && n.each(function() {
            var n, o, e, s, a, r, h, l, d = i(this),
            c = 2;
            n = i(".goods-photo", d),
            n.each(function() {
                o = i(this),
                e = o.parents(".photo-block").width(),
                s = o.data("width") || 1,
                a = o.data("height") || 1,
                r = e / (s / a),
                r = r,
                o.height(r)
            }),
            h = d.outerWidth(),
            h >= 540 ? (c = 3, l = [176.5, 176.5, 176.5]) : l = h >= 360 ? [175, 175] : [155, 155];
            var p = new t({
                container: d[0],
                rowCount: c,
                rowContainerClassName: "",
                rowContainerTagType: "li",
                rowCintainerWidths: l
            });
            p.sortOut()
        })
    } ($),
    function(t) {
        var n = t(".js-goods-buy"),
        o = !1;
        n.click(function(n) {
            var e = t(this);
            if (n.preventDefault(), n.stopPropagation(), !window._global.is_mobile) return void motify.log("预览不支持进行购买，<br/>实际效果请在手机上进行。");
            if (o) motify.log("请勿重复提交。");
            else {
                var s = e.data("alias"),
                a = e.data("postage"),
                r = e.data("buyway"),
                h = e.data("id"),
                l = e.data("title"),
                d = e.data("price"),
                c = e.parents(".js-goods"),
                p = e.parents(".link").find(".photo-block img").data("src");
                if ("0" == r) {
                    var u = c.attr("href");
                    window.location.href = u
                } else {
                    e.parent().find(".goods-buy").addClass("ajax-loading"),
                    o = !0;
                    var w = t.ajax({
                        type: "get",
                        timeout: 5e3,
                        url: "/v2/showcase/sku/skudata.json",
                        data: {
                            alias: s
                        },
                        dataType: "json",
                        cache: !1,
                        success: function(t) {
                            if (0 !== +t.code) return void motify.log(t.msg);
                            var n = t.data,
                            o = (n.list[0], n.stock_num),
                            e = h;
                            if (o) {
                                var s = new Date,
                                r = new Date(1e3 * n.start_sold_time),
                                c = (r - s) / 1e3;
                                if (n.start_sold_time && c > 0) {
                                    var u = parseInt(c / 3600),
                                    w = parseInt((c - 3600 * u) / 60),
                                    f = parseInt(c - 3600 * u - 60 * w),
                                    m = f + "秒";
                                    return (0 !== w || 0 !== u) && (m = w + "分" + m),
                                    0 !== u && (m = u + "时" + m),
                                    void motify.log("距开售 还剩" + m)
                                }
                                var g = new i({
                                    contentViewClass: BuyView,
                                    className: "sku-layout sku-box-shadow",
                                    contentViewOptions: {
                                        config: {
                                            isAdmin: window._global.is_owner_team,
                                            isMobile: window._global.is_mobile
                                        },
                                        skuViewConfig: {
                                            bottom: 0,
                                            left: 0,
                                            right: 0,
                                            top: 50
                                        },
                                        isAddCart: !1,
                                        isMultiBtn: !0,
                                        isCartBtnHide: window._global.hide_shopping_cart,
                                        logURL: window._global.logURL,
                                        baseUrl: window._global.url.wap,
                                        wxpay_env: window._global.wxpay_env
                                    },
                                    animationTime: 300,
                                    isCanNotHide: !1
                                });
                                g.render({
                                    sku: n,
                                    goods_id: e,
                                    postage: a,
                                    difTitle: !0,
                                    title: l,
                                    picture: [p],
                                    price: d,
                                    origin: "",
                                    acitvity: {},
                                    activity_alias: window._global.activity_alias,
                                    activity_id: window._global.activity_id,
                                    activity_type: window._global.activity_type
                                }).show()
                            } else motify.log("该商品已售罄!")
                        },
                        error: function(t, i) {
                            "timeout" === i ? (w.abort(), motify.log("连接超时")) : "error" === i && (w.abort(), motify.log("请求失败，请刷新或重新打开本页!"))
                        },
                        complete: function() {
                            o = !1,
                            e.parent().find(".goods-buy").removeClass("ajax-loading")
                        }
                    })
                }
            }
        })
    } ($)
}),
define("main",
function() {});