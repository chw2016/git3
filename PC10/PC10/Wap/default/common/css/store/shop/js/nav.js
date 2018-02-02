var _global = {
    "js": {
        "hide_wx_nav": true
    }
};
_global.spm = {
    logType: "f"
};
function wxReady(e) {
    if (window.WeixinJSBridge) e && e();
    else {
        var t = setTimeout(function() {
                window.WeixinJSBridge && e && e()
            },
            1e3);
        document.addEventListener("WeixinJSBridgeReady",
            function() {
                clearTimeout(t),
                e && e()
            })
    }
}
$(function() {
    wxReady(_global.js.hide_wx_nav ?
        function() {
            WeixinJSBridge.call("hideToolbar")
        }: function() {
        WeixinJSBridge.call("showToolbar")
    })
}),
    function() {
        $.kdt = $.kdt || {},
            $.extend($.kdt, {
                getFormData: function(e) {
                    var t = e.serializeArray(),
                        n = {};
                    return $.map(t,
                        function(e) {
                            n[e.name] = e.value
                        }),
                        n
                },
                spm: function() {
                    var e = $.kdt.getParameterByName("spm");
                    return "" !== $.trim(e) ? window._global.spm.logType && window._global.spm.logId && (e += "_" + window._global.spm.logType + window._global.spm.logId) : e = window._global.spm.logType + window._global.spm.logId || "",
                        e
                },
                isIOS: function() {
                    return /(iPhone|iPad|iPod)/i.test(navigator.userAgent) ? !0 : !1
                },
                isAndroid: function() {
                    return /Android/i.test(navigator.userAgent) ? !0 : !1
                },
                isWeixin: function() {
                    return $.kdt._weixinEle = $.kdt._weixinEle || $(document.documentElement),
                        $.kdt._weixinEle.hasClass("wx_mobile")
                },
                isMobile: function() {
                    return window._global.is_mobile
                },
                isWifi: !1,
                isCellular: !1,
                getParameterByName: function(e) {
                    e = e.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
                    var t = "[\\?&]" + e + "=([^&#]*)",
                        n = new RegExp(t),
                        o = n.exec(window.location.search);
                    return null === o ? "": decodeURIComponent(o[1].replace(/\+/g, " "))
                },
                removeParameter: function(e, t) {
                    var n = e.split("?");
                    if (n.length >= 2) {
                        for (var o = encodeURIComponent(t) + "=", i = n[1].split(/[&;]/g), a = i.length; a-->0;) - 1 !== i[a].lastIndexOf(o, 0) && i.splice(a, 1);
                        return e = n[0] + "?" + i.join("&")
                    }
                    return e
                },
                addParameter: function(e, t) {
                    if (!e || 0 == e.length) return "";
                    var n = e.split("#");
                    e = n[0];
                    for (var o in t) if (t.hasOwnProperty(o)) {
                        if ("" === $.trim(t[o])) continue;
                        if (e.indexOf("?") < 0) e += "?" + $.trim(o) + "=" + $.trim(t[o]);
                        else {
                            var i = {},
                                a = e.split("?");
                            $.each(a[1].split("&"),
                                function(e, t) {
                                    var n = t.split("=");
                                    "" !== $.trim(n[1]) && (i[n[0]] = n[1])
                                }),
                                $.each(t,
                                    function(e, t) {
                                        "" !== $.trim(t) && (i[e] = t)
                                    });
                            var r = [];
                            $.each(i,
                                function(e, t) {
                                    r.push(e + "=" + t)
                                }),
                                e = a[0] + "?" + r.join("&")
                        }
                    }
                    return 2 === n.length && (e += "#" + n[1]),
                        e
                },
                log: function(e, t) {
                    var n = new Image,
                        o = Math.floor(2147483648 * Math.random()).toString(36);
                    key = "log_" + o,
                        window[key] = n,
                        n.onload = n.onerror = n.onabort = function() {
                            n.onload = n.onerror = n.onabort = null,
                                window[key] = null,
                                n = null,
                            t === Object(t) && _.isFunction(t.resolve) && t.resolve()
                        },
                        n.src = $.kdt.addParameter(e, {
                            time: Date.now()
                        })
                },
                getTaobaoModal: function() {
                    return $.kdt._taobaoEle = $.kdt._taobaoEle || $("#js-fuck-taobao")
                },
                fuckTaobao: function(e) {
                    return ($.kdt.isIOS() || $.kdt.isAndroid()) && $.kdt.isWeixin() && (e.indexOf("taobao.com") >= 0 || e.indexOf("tmall.com") >= 0)
                },
                openModal: function() {
                    this._opened || ($.kdt.isIOS() ? ($.kdt.getTaobaoModal().find(".js-step-2").addClass("step-2"), this._opened = !0) : $.kdt.isAndroid() && ($.kdt.getTaobaoModal().find(".js-step-2").addClass("step-and-2"), this._opened = !0)),
                        $.kdt.getTaobaoModal().removeClass("hide")
                },
                openLink: function(e, t) {
                    if (void 0 !== e && null !== e) {
                        if ($.kdt.fuckTaobao(e)) return $.kdt.openModal();
                        if (t = t || !1) {
                            var n = window.open(e, "_blank");
                            n.focus()
                        } else location.href = e
                    }
                },
                parseWeibo: function(e) {
                    e = e.toLowerCase();
                    var t;
                    if (e.indexOf("/showcase/") >= 0) {
                        var n = e.match(/\/showcase\/(\w+)\?alias=(\w+)(&|$)/i);
                        t = {
                            type: n[1],
                            alias: n[2]
                        }
                    } else if (e.indexOf("/x/") >= 0) {
                        var o = e.match(/\/x\/(\w+)$/i);
                        t = {
                            type: "x",
                            alias: o[1]
                        }
                    }
                    return t
                }
            })
    } (),
    $(function() {
        wxReady(function() {
            WeixinJSBridge.invoke("getNetworkType", {},
                function(e) {
                    var t = $.trim(e.err_msg);
                    "network_type:wifi" === t && ($(document.documentElement).addClass("wifi"), $.kdt.isWifi = !0),
                    ("network_type:edge" === t || "network_type:wwan" === t) && ($.kdt.isCellular = !0);
                    var n = {
                            spm: $.kdt.spm(),
                            fm: "display_network",
                            referer_url: encodeURIComponent(document.referrer),
                            title: $.trim(document.title),
                            msg: encodeURIComponent(t)
                        },
                        o = $.kdt.addParameter(_global.logURL, n);
                    $.kdt.log(o)
                })
        })
    }),
    function(e, t, n, o) {
        var i = e(t);
        e.fn.lazyload = function(n) {
            function a(e, t) {
                var n, o, i, a = s.width,
                    r = s.height,
                    d = t / e;
                return t >= r && e >= a ? (o = a, i = a * d, n = {
                    marginTop: (r - i) / 2
                }) : t >= r && a >= e ? n = {
                    marginLeft: (a - e) / 2,
                    marginTop: (r - t) / 2
                }: r >= t && e >= a ? (o = a, i = a * d, n = {
                    marginTop: (r - i) / 2
                }) : r >= t && a >= e && (n = {
                    marginLeft: (a - e) / 2,
                    marginTop: (r - t) / 2
                }),
                    o = o || e,
                    i = i || t,
                {
                    width: o,
                    height: i,
                    style: n
                }
            }
            function r() {
                var t = 0;
                l.each(function() {
                    var n = e(this);
                    if (!s.skip_invisible || n.is(":visible")) if (e.abovethetop(this, s) || e.leftofbegin(this, s));
                    else if (e.belowthefold(this, s) || e.rightoffold(this, s)) {
                        if (++t > s.failure_limit) return ! 1
                    } else n.trigger("appear"),
                        t = 0
                })
            }
            var d, l = this,
                s = {
                    threshold: 0,
                    failure_limit: 0,
                    event: "scroll",
                    effect: "show",
                    container: t,
                    data_attribute: "src",
                    skip_invisible: !1,
                    appear: null,
                    load: null,
                    width: null,
                    height: null,
                    resize: !1
                };
            return n && (o !== n.failurelimit && (n.failure_limit = n.failurelimit, delete n.failurelimit), o !== n.effectspeed && (n.effect_speed = n.effectspeed, delete n.effectspeed), e.extend(s, n)),
                d = s.container === o || s.container === t ? i: e(s.container),
            0 === s.event.indexOf("scroll") && d.bind(s.event,
                function() {
                    return r()
                }),
                this.each(function() {
                    var t = this,
                        n = e(t);
                    t.loaded = !1,
                        n.parent().addClass("loading"),
                        n.one("appear",
                            function() {
                                if (!this.loaded) {
                                    if (s.appear) {
                                        var o = l.length;
                                        s.appear.call(t, o, s)
                                    }
                                    e("<img />").bind("load",
                                        function() {
                                            if (s.resize) {
                                                var o = a(this.width, this.height);
                                                n.hide().attr("src", "").width(o.width).height(o.height).css({
                                                    marginLeft: 0,
                                                    marginTop: 0
                                                }).parent().removeClass("loading"),
                                                    n.attr("src", n.data(s.data_attribute)).css(o.style)[s.effect](s.effect_speed)
                                            } else n.hide().attr("src", n.data(s.data_attribute))[s.effect](s.effect_speed).parent().removeClass("loading");
                                            t.loaded = !0;
                                            var i = e.grep(l,
                                                function(e) {
                                                    return ! e.loaded
                                                });
                                            if (l = e(i), s.load) {
                                                var r = l.length;
                                                s.load.call(t, r, s)
                                            }
                                        }).attr("src", n.data(s.data_attribute))
                                }
                            }),
                    0 !== s.event.indexOf("scroll") && n.bind(s.event,
                        function() {
                            t.loaded || n.trigger("appear")
                        })
                }),
                i.bind("resize",
                    function() {
                        r()
                    }),
            /iphone|ipod|ipad.*os 5/gi.test(navigator.appVersion) && i.bind("pageshow",
                function(t) { (t.originalEvent || {}).persisted && l.each(function() {
                    e(this).trigger("appear")
                })
                }),
                e(t).ready(function() {
                    r()
                }),
                this
        },
            e.belowthefold = function(n, a) {
                var r;
                return r = a.container === o || a.container === t ? i.height() + i.scrollTop() : e(a.container).offset().top + e(a.container).height(),
                r <= e(n).offset().top - a.threshold
            },
            e.rightoffold = function(n, a) {
                var r;
                return r = a.container === o || a.container === t ? i.width() + i.scrollLeft() : e(a.container).offset().left + e(a.container).width(),
                r <= e(n).offset().left - a.threshold
            },
            e.abovethetop = function(n, a) {
                var r;
                return r = a.container === o || a.container === t ? i.scrollTop() : e(a.container).offset().top,
                r >= e(n).offset().top + a.threshold + (e(n).height() || a.height)
            },
            e.leftofbegin = function(n, a) {
                var r;
                return r = a.container === o || a.container === t ? i.scrollLeft() : e(a.container).offset().left,
                r >= e(n).offset().left + a.threshold + (e(n).width() || a.width)
            },
            e.inviewport = function(t, n) {
                return ! (e.rightoffold(t, n) || e.leftofbegin(t, n) || e.belowthefold(t, n) || e.abovethetop(t, n))
            }
    } ($, window, document),
    function() {
        function e(e) {
            "undefined" != typeof window.WeixinJSBridge && WeixinJSBridge.invoke("imagePreview", {
                current: e,
                urls: v
            })
        }
        var t = $.kdt.spm();
        $.kdt.clickLogHandler = function(e) {
            function n() {
                var n = $.Deferred().done(function() { ($.kdt.isMobile() || !a) && $.kdt.openLink(l)
                    }),
                    o = {
                        spm: t,
                        fm: "click",
                        url: window.encodeURIComponent(i),
                        referer_url: encodeURIComponent(document.referrer),
                        title: $.trim(d)
                    };
                e.fromMenu && $.extend(o, {
                    click_type: "menu"
                }),
                null !== r && void 0 !== r && $.extend(o, {
                    click_id: r
                });
                var s = $.kdt.addParameter(_global.logURL, o);
                $.kdt.log(s, n)
            }
            var o = $(this),
                i = o.attr("href"),
                a = "_blank" === o.attr("target"),
                r = o.data("goods-id"),
                d = o.prop("title") || o.text();
        };
            $(document).on("click", "a", $.kdt.clickLogHandler);
        var n = function() {
                var e = [],
                    t = $(".js-goods");
                return t.length < 1 ? null: (t.each(function(t, n) {
                    var o = $(n);
                    e.push(o.data("goods-id"))
                }), e.join(","))
            } (),
            o = {
                spm: t,
                fm: "display",
                referer_url: encodeURIComponent(document.referrer),
                title: $.trim(document.title)
            };
        n && $.extend(o, {
            display_goods: n
        });
        var a = $.kdt.addParameter(_global.logURL, o);
        $.kdt.log(a),
            $(".js-lazy").lazyload({
                threshold: 100
            }),
            $(".js-goods-lazy").lazyload({
                threshold: 100,
                appear: function() {
                    var e, n = $(this).parents(".js-goods").first().data("goods-id");
                    e = t.lastIndexOf("_") === t.length - 1 ? t + "SI" + n: t + "_SI" + n,
                        $.kdt.log($.kdt.addParameter(_global.logURL, {
                            spm: e,
                            fm: "display",
                            referer_url: encodeURIComponent(document.referrer)
                        }))
                }
            });
        var r = $(document.documentElement),
            d = $(".js-mp-info"),
            l = window.navigator.userAgent,
            s = l.match(/MicroMessenger\/(\d+(\.\d+)*)/),
            c = null !== s && s.length,
            u = c ? s[1] : "",
            f = u.split("."),
            g = [5, 2, 0],
            m = !0;
        for (i in g) {
            if (!f[i]) break;
            if (parseInt(f[i]) < g[i]) {
                m = !0;
                break
            }
            if (parseInt(f[i]) > g[i]) {
                m = !1;
                break
            }
        }
        var p = $.kdt.isAndroid() && $.kdt.isWeixin() && m;
        p || d.on("click", ".js-follow-mp",
            function() {
                return r.trigger("fullguide:show", "follow"),
                    !1
            }),
            $(".custom-image-swiper").each(function(e, t) {
                var n = $(t),
                    o = n.find(".swiper-container"),
                    i = n.find(".swiper-pagination"),
                    a = {
                        mode: "horizontal",
                        pagination: i[0],
                        paginationClickable: !0,
                        calculateHeight: !0,
                        autoplay: 3500,
                        onSlideChangeStart: function(e) {
                            var t = n.find(".js-slide-lazy"),
                                o = e.activeIndex;
                            t.eq(o).trigger("appear"),
                                t.eq(o + 1).trigger("appear")
                        },
                        onTouchStart: function(e) {
                            e.stopAutoplay()
                        },
                        onTouchEnd: function(e) {
                            e.startAutoplay()
                        }
                    };
                if (o.find(".swiper-slide").length > 1) {
                    $(".content").width() >= 540 && $.extend(a, {
                        slidesPerView: 1.5
                    }); {
                        new Swiper(o[0], a)
                    }
                }
            }),
            $(".js-slide-lazy").lazyload({
                threshold: 100
            });
        var l = window.navigator.userAgent,
            h = l.match(/MicroMessenger\/(\d+(\.\d+)*)/);
        if (null !== h && h.length) {
            var w = $(".js-image-swiper img, .custom-richtext img"),
                v = [],
                b = 0;
            w.each(function() {
                var t = $(this),
                    n = t.attr("data-src") || t.attr("src");
                if (t.width() >= b && n) {
                    if (t.parents("a").length > 0) return;
                    v.push(n),
                        t.on("click",
                            function() {
                                e(n)
                            })
                }
            })
        }
    } ($, window, document),
    function() {
        $.kdt.getTaobaoModal().on("touchstart",
            function(e) {
                $(e.target);
                e.target.className.indexOf("step-") < 0 && $.kdt.getTaobaoModal().addClass("hide")
            })
    } (),
    function() {
        var e = {
            showSubmenu: function(e) {
                var t = $(e.target),
                    n = t.parents(".one"),
                    o = t.hasClass(".js-mainmenu") ? t: n.find(".js-mainmenu"),
                    i = n.find(".js-submenu"),
                    a = i.find(".arrow"),
                    r = t.parents(".js-navmenu"),
                    d = r.find(".one"),
                    l = d.length,
                    s = d.index(n),
                    c = o.outerWidth(),
                    u = (i.outerWidth() - o.outerWidth()) / 2,
                    f = i.outerWidth() / 2;
                if (0 === i.size()) $(".js-submenu:visible").hide();
                else {
                    var g = i.outerWidth(),
                        m = n.outerWidth(),
                        p = "auto",
                        h = "auto",
                        w = "auto",
                        v = "auto";
                    0 === s ? (p = o.position().left - u, h = f - a.outerWidth() / 2) : s === l - 1 && g > m ? (w = 8, v = c / 2 - w) : (p = o.position().left - u, h = f - a.outerWidth() / 2);
                    var b = 5;
                    0 > p && (h = h + p + b, p = b),
                    0 > w && (v = v + w + b, w = b),
                        i.css({
                            left: p,
                            right: w
                        }),
                        a.css({
                            left: h - 1,
                            right: v
                        }),
                        $(".js-submenu:visible").not(i).hide(),
                        i.toggle()
                }
            },
            openNavmenu: function(e) {
                var t = $(".js-navmenu");
                t.slideToggle("fast"),
                    $(".js-submenu:visible").hide(),
                    $(".js-open-navmenu .caret").toggleClass("up-arrow"),
                    e.stopPropagation()
            }
        };
        $(document).on("click",
            function() {
                $(".js-submenu:visible").hide(0),
                    $(".js-open-navmenu .caret").removeClass("up-arrow")
            }),
        $(document).on("scroll",
            function() {
                $(".js-submenu:visible").hide(0),
                    $(".js-open-navmenu .caret").removeClass("up-arrow")
            }),
            $("body").on("click", ".js-navmenu",
                function(e) {
                    e.fromMenu = !0,
                        $.kdt.clickLogHandler.call(e.target, e),
                        e.stopPropagation()
                }),
            $("body").on("click", ".js-submenu",
                function(e) {
                    e.fromMenu = !0,
                        $.kdt.clickLogHandler.call(e.target, e),
                        e.stopPropagation()
                }),
            $("body").on("click", ".js-mainmenu",
                function(t) {

                    e.showSubmenu(t)
                }),
            $("body").on("click", ".js-open-navmenu",
                function(t) {
                    e.openNavmenu(t)
                });
        var t = ".nav-on-bottom",
            n = $(".js-custom-paginations");
        $(t).size() + n.size() > 0 && $("body").css("padding-bottom", $(".js-navmenu").height() || n.height())
    } ();
