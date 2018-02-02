"use strict"; !
function() {
    function t() {
        clearTimeout(o),
        e.addClass("done")
    }
    var e = $(".js-tpl-weixin-list-item"),
    n = $(".js-tpl-weixin-bg");
    if (! (n.length <= 0)) {
        var i = n[0],
        o = setTimeout(function() {
            t()
        },
        2e3);
        i.onload = i.onerror = i.onabort = t,
        i.complete && t()
    }
} (),
function() {
    var t = $(".js-tpl-shop"),
    e = "/v2/showcase/homepage/goodscount.json";
    t.length && $.ajax({
        url: e,
        type: "GET",
        dataType: "json",
        data: {
            kdt_id: window._global.kdt_id
        }
    }).done(function(e) {
        if (0 === +e.code) {
            var n = t.find(".js-all-goods"),
            i = t.find(".js-new-goods"),
            o = t.find(".js-order"),
            a = e.data,
            s = "";
            s = (a.all_goods.count + "").length,
            n.find("a").attr("href", a.all_goods.url),
            n.find(".count").html(a.all_goods.count).addClass("l-" + s),
            s = (a.new_goods.count + "").length,
            i.find("a").attr("href", a.new_goods.url),
            i.find(".count").html(a.new_goods.count).addClass("l-" + s),
            o.find("a").attr("href", a.order.url)
        }
    })
} (),
function() {
    $(".js-select-coupon").on("click",
    function() {
        var t = $(this),
        e = t.data("id"),
        n = t.data("kdt-id"),
        i = window.motify;
        $.ajax({
            url: "/v2/guang/promocard/take.json",
            type: "POST",
            data: {
                groupId: e,
                kdtId: n
            }
        }).done(function(t) {
            i.log(0 === +t.code ? t.msg || "成功领取优惠券": t.msg || "网络错误")
        }).fail(function() {
            i.log("网络错误")
        })
    });
    var t = $(".js-scroll-notice");
    t.length && t.each(function() {
        function e() {
            s--,
            0 > s + o && (s = a),
            n.css({
                left: s
            })
        }
        var n = $(this),
        i = t.parents(".tpl-11-11-notice-inner"),
        o = t.width(),
        a = i.width(),
        s = 0;
        a >= o || (n.css({
            position: "relative"
        }), setInterval(e, 25))
    })
} (),
function() {
    var t = $(".js-custom-level"),
    e = $(".js-custom-point"),
    n = $(".js-custom-level-title-section");
    if (! ( + _global.fans_id <= 0 && +_global.buyer_id <= 0)) {
        var i = window._global.url.wap + "/showcase/component/point.jsonp?" + $.param({
            kdt_id: window._global.kdt_id
        }); (t.length > 0 || e.length > 0) && $.ajax({
            dataType: "jsonp",
            type: "GET",
            url: i,
            success: function(i) {
                0 === +i.code && (t.html(i.data.level || "会员"), e.html(i.data.point || "暂无数据"), n.removeClass("hide"))
            }
        })
    }
} (),
function() {
    window.Zepto &&
    function(t) { ["width", "height"].forEach(function(e) {
            var n = e.replace(/./,
            function(t) {
                return t[0].toUpperCase()
            });
            t.fn["outer" + n] = function(t) {
                var n = this;
                if (n) {
                    var i = n[e](),
                    o = {
                        width: ["left", "right"],
                        height: ["top", "bottom"]
                    };
                    return o[e].forEach(function(e) {
                        t && (i += parseInt(n.css("margin-" + e), 10))
                    }),
                    i
                }
                return null
            }
        })
    } (window.Zepto);
    var t = {
        showSubmenu: function(t) {
            var e = $(t.target),
            n = e.parents(".one"),
            i = e.hasClass(".js-mainmenu") ? e: n.find(".js-mainmenu"),
            o = n.find(".js-submenu"),
            a = o.find(".arrow"),
            s = e.parents(".js-navmenu"),
            r = s.find(".one");
            o.css("opacity", "0").toggle();
            var c = r.length,
            d = r.index(n),
            l = i.outerWidth(),
            u = (o.outerWidth() - i.outerWidth()) / 2,
            f = o.outerWidth() / 2;
            if (0 === o.size()) $(".js-submenu:visible").hide();
            else {
                var h = o.outerWidth(),
                p = n.outerWidth(),
                g = "auto",
                m = "auto",
                v = "auto",
                w = "auto";
                0 === d ? (g = i.position().left - u, m = f - a.outerWidth() / 2) : d === c - 1 && h > p ? (v = 8, w = l / 2 - v) : (g = i.position().left - u, m = f - a.outerWidth() / 2);
                var j = 5;
                0 > g && (m = m + g + j, g = j),
                0 > v && (w = w + v + j, v = j),
                o.css({
                    left: g,
                    right: v
                }),
                a.css({
                    left: m,
                    right: w
                }),
                $(".js-submenu:visible").not(o).hide(),
                o.css("opacity", "1")
            }
        },
        openNavmenu: function(t) {
            var e = $(".js-navmenu");
            e.slideToggle("fast"),
            $(".js-submenu:visible").hide(),
            $(".js-open-navmenu .caret").toggleClass("up-arrow"),
            t.stopPropagation()
        }
    };
    $(document).on("click",
    function() {
        $(".js-submenu:visible").hide(0),
        $(".js-open-navmenu .caret").removeClass("up-arrow")
    }),
    $("body").on("click", ".js-navmenu",
    function(t) {
        t.fromMenu = !0,
        $.kdt.clickLogHandler.call(t.target, t),
        t.stopPropagation()
    }),
    $("body").on("click", ".js-submenu",
    function(t) {
        t.fromMenu = !0,
        $.kdt.clickLogHandler.call(t.target, t),
        t.stopPropagation()
    }),
    $("body").on("click", ".js-mainmenu",
    function(e) {
        t.showSubmenu(e)
    }),
    $("body").on("click", ".js-open-navmenu",
    function(e) {
        t.openNavmenu(e)
    });
    var e = ".nav-on-bottom",
    n = $(".js-custom-paginations");
    $(e).size() + n.size() > 0 && $("body").css("padding-bottom", $(".js-navmenu").height() || n.height())
} (),
function() {
    function t(t) {
        "undefined" != typeof window.WeixinJSBridge && window.WeixinJSBridge.invoke("imagePreview", {
            current: t,
            urls: i
        })
    }
    if ("undefined" != typeof Swiper) { !
        function() {
            var t = $(".custom-image-swiper"),
            e = t.find(".swiper-container"),
            n = t.width();
            n > 320 && e.each(function(t, e) {
                var i = $(e),
                o = i.height(),
                a = n / 320 * o;
                i.height(a).find(".swiper-slide a").height(a).find("img").css({
                    "max-height": a,
                    "max-width": n
                })
            })
        } (),
        $(".custom-image-swiper").each(function(t, e) {
            var n = $(e),
            i = n.find(".swiper-container"),
            o = n.find(".swiper-pagination"),
            a = {
                mode: "horizontal",
                pagination: o[0],
                paginationClickable: !0,
                calculateHeight: !0,
                autoplay: 3500,
                updateOnImagesReady: !1,
                onFirstInit: function() {
                    var t = n.find(".js-slide-lazy");
                    t.each(function() {
                        var t = $(this);
                        t.attr("src", t.data("src"))
                    })
                },
                onTouchStart: function(t) {
                    t.stopAutoplay()
                },
                onTouchEnd: function(t) {
                    t.startAutoplay()
                }
            };
            i.find(".swiper-slide").length > 1 && new Swiper(i[0], a)
        });
        var e = window.navigator.userAgent.match(/MicroMessenger\/(\d+(\.\d+)*)/);
        if (null !== e && e.length) {
            var n = $(".js-image-swiper img, .custom-richtext img"),
            i = [],
            o = 0;
            n.each(function() {
                var e = $(this),
                n = e.attr("data-src") || e.attr("src");
                if (e.width() >= o && n) {
                    if (e.parents("a").length > 0) return;
                    i.push(n),
                    e.on("click",
                    function() {
                        t(n)
                    })
                }
            })
        } !
        function() {
            function t() {
                e += 100;
                var t = $(this);
                setTimeout(function() {
                    t.addClass("done")
                },
                e)
            }
            var e = 0,
            n = $(".js-tpl-fbb");
            n.find(".swiper-slide").each(t),
            n.swiper({
                mode: "horizontal",
                slidesPerView: "auto"
            })
        } (),
        function() {
            var t, e = $(".js-custom-scroll-nav"),
            n = e.find(".swiper-container");
            if (! (e.length <= 0)) {
                $(document).on("touchmove",
                function(t) {
                    t.preventDefault()
                });
                var i = 0,
                o = "";
                e.hasClass("custom-scroll-nav-left") ? (i = "-15deg", o = "RotateLeft") : e.hasClass("custom-scroll-nav-right") && (i = "15deg", o = "RotateRight");
                var a = e.data("animate"),
                s = e.data("animate-type");
                t = 0;
                var r = e.find(".swiper-slide");
                r.each(function(e) {
                    t += 100;
                    var n = $(this);
                    setTimeout(function() {
                        return function() {
                            var t = {
                                rotate: i,
                                skewX: i,
                                translate3d: "0, 0, 0"
                            };
                            1 >= e && (t.opacity = 1),
                            n.zeptoAnimate(t, 500)
                        }
                    } (e), t)
                });
                var c = $(".js-scroll-nav-blur"),
                d = $("body").height() / 2;
                e.find(".swiper-container").css({
                    height: d,
                    paddingTop: d,
                    overflow: "hidden"
                });
                var l, u = !1,
                f = 0;
                n.swiper({
                    mode: "vertical",
                    calculateHeight: !1,
                    slidesPerView: "auto",
                    freeModeFluid: !0,
                    watchActiveIndex: !0,
                    onTouchMove: function(t) {
                        if (u || (u = !0, l = setTimeout(function() {
                            c.addClass("custom-scroll-nav-blur-show")
                        },
                        400)), t.positions.current > -10 && (u = !1, clearTimeout(l), c.removeClass("custom-scroll-nav-blur-show")), t.activeIndex > 1 && f != t.activeIndex && 0 == $(t.getSlide(t.activeIndex)).css("opacity")) if (f = t.activeIndex, "zoom" === a) {
                            var e = 1.2;
                            "zoom_in" === s && (e = .8),
                            $(t.getSlide(t.activeIndex)).css({
                                opacity: 0,
                                "-webkit-transform": " scale(" + e + ") rotate(" + i + ") skewX(" + i + ") translate3d(0px, 0px, 0px)"
                            }).zeptoAnimate({
                                opacity: 1,
                                scale: "1",
                                rotate: i,
                                skewX: i,
                                translate3d: "0, 0, 0"
                            },
                            400);
                            for (var n = f; n--;) 0 == $(t.getSlide(n)).css("opacity") && $(t.getSlide(n)).css({
                                opacity: 0,
                                "-webkit-transform": " scale(" + e + ") rotate(" + i + ") skewX(" + i + ") translate3d(0px, 0px, 0px)"
                            }).zeptoAnimate({
                                opacity: 1,
                                scale: "1",
                                rotate: i,
                                skewX: i,
                                translate3d: "0, 0, 0"
                            },
                            400)
                        } else {
                            var r = $(t.getSlide(t.activeIndex)),
                            d = "bounceInLeft" + o;
                            "slide_right" === s && (d = "bounceInRight" + o),
                            r.addClass("animated " + d);
                            for (var n = f; n--;) 0 == $(t.getSlide(n)).css("opacity") && $(t.getSlide(n)).addClass("animated " + d)
                        }
                    },
                    onTouchEnd: function(t) {
                        t.positions.current > -10 && (u = !1, clearTimeout(l))
                    }
                })
            }
        } ()
    }
} ();