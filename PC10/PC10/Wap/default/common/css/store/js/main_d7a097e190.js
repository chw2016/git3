var Swiper = function(e, t) {
    "use strict";
    function i(e, t) {
        return document.querySelectorAll ? (t || document).querySelectorAll(e) : jQuery(e, t)
    }
    function n(e) {
        return "[object Array]" === Object.prototype.toString.apply(e) ? !0 : !1
    }
    function r() {
        var e = I - G;
        return t.freeMode && (e = I - G),
        t.slidesPerView > k.slides.length && !t.centeredSlides && (e = 0),
        0 > e && (e = 0),
        e
    }
    function o() {
        function e(e) {
            var i = new Image;
            i.onload = function() {
                k && void 0 !== k.imagesLoaded && k.imagesLoaded++,
                k.imagesLoaded === k.imagesToLoad.length && (k.reInit(), t.onImagesReady && k.fireCallback(t.onImagesReady, k))
            },
            i.src = e
        }
        var n = k.h.addEventListener,
        r = "wrapper" === t.eventTarget ? k.wrapper: k.container;
        if (k.browser.ie10 || k.browser.ie11 ? (n(r, k.touchEvents.touchStart, g), n(document, k.touchEvents.touchMove, w), n(document, k.touchEvents.touchEnd, m)) : (k.support.touch && (n(r, "touchstart", g), n(r, "touchmove", w), n(r, "touchend", m)), t.simulateTouch && (n(r, "mousedown", g), n(document, "mousemove", w), n(document, "mouseup", m))), t.autoResize && n(window, "resize", k.resizeFix), s(), k._wheelEvent = !1, t.mousewheelControl) {
            if (void 0 !== document.onmousewheel && (k._wheelEvent = "mousewheel"), !k._wheelEvent) try {
                new WheelEvent("wheel"),
                k._wheelEvent = "wheel"
            } catch(o) {}
            k._wheelEvent || (k._wheelEvent = "DOMMouseScroll"),
            k._wheelEvent && n(k.container, k._wheelEvent, d)
        }
        if (t.keyboardControl && n(document, "keydown", l), t.updateOnImagesReady) {
            k.imagesToLoad = i("img", k.container);
            for (var a = 0; a < k.imagesToLoad.length; a++) e(k.imagesToLoad[a].getAttribute("src"))
        }
    }
    function s() {
        var e, n = k.h.addEventListener;
        if (t.preventLinks) {
            var r = i("a", k.container);
            for (e = 0; e < r.length; e++) n(r[e], "click", f)
        }
        if (t.releaseFormElements) {
            var o = i("input, textarea, select", k.container);
            for (e = 0; e < o.length; e++) n(o[e], k.touchEvents.touchStart, h, !0)
        }
        if (t.onSlideClick) for (e = 0; e < k.slides.length; e++) n(k.slides[e], "click", p);
        if (t.onSlideTouch) for (e = 0; e < k.slides.length; e++) n(k.slides[e], k.touchEvents.touchStart, u)
    }
    function a() {
        var e, n = k.h.removeEventListener;
        if (t.onSlideClick) for (e = 0; e < k.slides.length; e++) n(k.slides[e], "click", p);
        if (t.onSlideTouch) for (e = 0; e < k.slides.length; e++) n(k.slides[e], k.touchEvents.touchStart, u);
        if (t.releaseFormElements) {
            var r = i("input, textarea, select", k.container);
            for (e = 0; e < r.length; e++) n(r[e], k.touchEvents.touchStart, h, !0)
        }
        if (t.preventLinks) {
            var o = i("a", k.container);
            for (e = 0; e < o.length; e++) n(o[e], "click", f)
        }
    }
    function l(e) {
        var t = e.keyCode || e.charCode;
        if (! (e.shiftKey || e.altKey || e.ctrlKey || e.metaKey)) {
            if (37 === t || 39 === t || 38 === t || 40 === t) {
                for (var i = !1,
                n = k.h.getOffset(k.container), r = k.h.windowScroll().left, o = k.h.windowScroll().top, s = k.h.windowWidth(), a = k.h.windowHeight(), l = [[n.left, n.top], [n.left + k.width, n.top], [n.left, n.top + k.height], [n.left + k.width, n.top + k.height]], d = 0; d < l.length; d++) {
                    var p = l[d];
                    p[0] >= r && p[0] <= r + s && p[1] >= o && p[1] <= o + a && (i = !0)
                }
                if (!i) return
            }
            O ? ((37 === t || 39 === t) && (e.preventDefault ? e.preventDefault() : e.returnValue = !1), 39 === t && k.swipeNext(), 37 === t && k.swipePrev()) : ((38 === t || 40 === t) && (e.preventDefault ? e.preventDefault() : e.returnValue = !1), 40 === t && k.swipeNext(), 38 === t && k.swipePrev())
        }
    }
    function d(e) {
        var i = k._wheelEvent,
        n = 0;
        if (e.detail) n = -e.detail;
        else if ("mousewheel" === i) if (t.mousewheelControlForceToAxis) if (O) {
            if (! (Math.abs(e.wheelDeltaX) > Math.abs(e.wheelDeltaY))) return;
            n = e.wheelDeltaX
        } else {
            if (! (Math.abs(e.wheelDeltaY) > Math.abs(e.wheelDeltaX))) return;
            n = e.wheelDeltaY
        } else n = e.wheelDelta;
        else if ("DOMMouseScroll" === i) n = -e.detail;
        else if ("wheel" === i) if (t.mousewheelControlForceToAxis) if (O) {
            if (! (Math.abs(e.deltaX) > Math.abs(e.deltaY))) return;
            n = -e.deltaX
        } else {
            if (! (Math.abs(e.deltaY) > Math.abs(e.deltaX))) return;
            n = -e.deltaY
        } else n = Math.abs(e.deltaX) > Math.abs(e.deltaY) ? -e.deltaX: -e.deltaY;
        if (t.freeMode) {
            var o = k.getWrapperTranslate() + n;
            if (o > 0 && (o = 0), o < -r() && (o = -r()), k.setWrapperTransition(0), k.setWrapperTranslate(o), k.updateActiveSlide(o), 0 === o || o === -r()) return
        } else(new Date).getTime() - q > 60 && (0 > n ? k.swipeNext() : k.swipePrev()),
        q = (new Date).getTime();
        return t.autoplay && k.stopAutoplay(!0),
        e.preventDefault ? e.preventDefault() : e.returnValue = !1,
        !1
    }
    function p(e) {
        k.allowSlideClick && (c(e), k.fireCallback(t.onSlideClick, k, e))
    }
    function u(e) {
        c(e),
        k.fireCallback(t.onSlideTouch, k, e)
    }
    function c(e) {
        if (e.currentTarget) k.clickedSlide = e.currentTarget;
        else {
            var i = e.srcElement;
            do {
                if (i.className.indexOf(t.slideClass) > -1) break;
                i = i.parentNode
            } while ( i );
            k.clickedSlide = i
        }
        k.clickedSlideIndex = k.slides.indexOf(k.clickedSlide),
        k.clickedSlideLoopIndex = k.clickedSlideIndex - (k.loopedSlides || 0)
    }
    function f(e) {
        return k.allowLinks ? void 0 : (e.preventDefault ? e.preventDefault() : e.returnValue = !1, t.preventLinksPropagation && "stopPropagation" in e && e.stopPropagation(), !1)
    }
    function h(e) {
        return e.stopPropagation ? e.stopPropagation() : e.returnValue = !1,
        !1
    }
    function g(e) {
        if (t.preventLinks && (k.allowLinks = !0), k.isTouched || t.onlyExternal) return ! 1;
        if (t.noSwiping && (e.target || e.srcElement) && v(e.target || e.srcElement)) return ! 1;
        if (U = !1, k.isTouched = !0, $ = "touchstart" === e.type, !$ || 1 === e.targetTouches.length) {
            k.callPlugins("onTouchStartBegin"),
            $ || k.isAndroid || (e.preventDefault ? e.preventDefault() : e.returnValue = !1);
            var i = $ ? e.targetTouches[0].pageX: e.pageX || e.clientX,
            n = $ ? e.targetTouches[0].pageY: e.pageY || e.clientY;
            k.touches.startX = k.touches.currentX = i,
            k.touches.startY = k.touches.currentY = n,
            k.touches.start = k.touches.current = O ? i: n,
            k.setWrapperTransition(0),
            k.positions.start = k.positions.current = k.getWrapperTranslate(),
            k.setWrapperTranslate(k.positions.start),
            k.times.start = (new Date).getTime(),
            D = void 0,
            t.moveStartThreshold > 0 && (K = !1),
            t.onTouchStart && k.fireCallback(t.onTouchStart, k, e),
            k.callPlugins("onTouchStartEnd")
        }
    }
    function w(e) {
        if (k.isTouched && !t.onlyExternal && (!$ || "mousemove" !== e.type)) {
            var i = $ ? e.targetTouches[0].pageX: e.pageX || e.clientX,
            n = $ ? e.targetTouches[0].pageY: e.pageY || e.clientY;
            if ("undefined" == typeof D && O && (D = !!(D || Math.abs(n - k.touches.startY) > Math.abs(i - k.touches.startX))), "undefined" != typeof D || O || (D = !!(D || Math.abs(n - k.touches.startY) < Math.abs(i - k.touches.startX))), D) return void(k.isTouched = !1);
            if (e.assignedToSwiper) return void(k.isTouched = !1);
            if (e.assignedToSwiper = !0, t.preventLinks && (k.allowLinks = !1), t.onSlideClick && (k.allowSlideClick = !1), t.autoplay && k.stopAutoplay(!0), !$ || 1 === e.touches.length) {
                if (k.isMoved || (k.callPlugins("onTouchMoveStart"), t.loop && (k.fixLoop(), k.positions.start = k.getWrapperTranslate()), t.onTouchMoveStart && k.fireCallback(t.onTouchMoveStart, k)), k.isMoved = !0, e.preventDefault ? e.preventDefault() : e.returnValue = !1, k.touches.current = O ? i: n, k.positions.current = (k.touches.current - k.touches.start) * t.touchRatio + k.positions.start, k.positions.current > 0 && t.onResistanceBefore && k.fireCallback(t.onResistanceBefore, k, k.positions.current), k.positions.current < -r() && t.onResistanceAfter && k.fireCallback(t.onResistanceAfter, k, Math.abs(k.positions.current + r())), t.resistance && "100%" !== t.resistance) {
                    var o;
                    if (k.positions.current > 0 && (o = 1 - k.positions.current / G / 2, k.positions.current = .5 > o ? G / 2 : k.positions.current * o), k.positions.current < -r()) {
                        var s = (k.touches.current - k.touches.start) * t.touchRatio + (r() + k.positions.start);
                        o = (G + s) / G;
                        var a = k.positions.current - s * (1 - o) / 2,
                        l = -r() - G / 2;
                        k.positions.current = l > a || 0 >= o ? l: a
                    }
                }
                if (t.resistance && "100%" === t.resistance && (k.positions.current > 0 && (!t.freeMode || t.freeModeFluid) && (k.positions.current = 0), k.positions.current < -r() && (!t.freeMode || t.freeModeFluid) && (k.positions.current = -r())), !t.followFinger) return;
                if (t.moveStartThreshold) if (Math.abs(k.touches.current - k.touches.start) > t.moveStartThreshold || K) {
                    if (!K) return K = !0,
                    void(k.touches.start = k.touches.current);
                    k.setWrapperTranslate(k.positions.current)
                } else k.positions.current = k.positions.start;
                else k.setWrapperTranslate(k.positions.current);
                return (t.freeMode || t.watchActiveIndex) && k.updateActiveSlide(k.positions.current),
                t.grabCursor && (k.container.style.cursor = "move", k.container.style.cursor = "grabbing", k.container.style.cursor = "-moz-grabbin", k.container.style.cursor = "-webkit-grabbing"),
                Q || (Q = k.touches.current),
                Z || (Z = (new Date).getTime()),
                k.velocity = (k.touches.current - Q) / ((new Date).getTime() - Z) / 2,
                Math.abs(k.touches.current - Q) < 2 && (k.velocity = 0),
                Q = k.touches.current,
                Z = (new Date).getTime(),
                k.callPlugins("onTouchMoveEnd"),
                t.onTouchMove && k.fireCallback(t.onTouchMove, k, e),
                !1
            }
        }
    }
    function m(e) {
        if (D && k.swipeReset(), !t.onlyExternal && k.isTouched) {
            k.isTouched = !1,
            t.grabCursor && (k.container.style.cursor = "move", k.container.style.cursor = "grab", k.container.style.cursor = "-moz-grab", k.container.style.cursor = "-webkit-grab"),
            k.positions.current || 0 === k.positions.current || (k.positions.current = k.positions.start),
            t.followFinger && k.setWrapperTranslate(k.positions.current),
            k.times.end = (new Date).getTime(),
            k.touches.diff = k.touches.current - k.touches.start,
            k.touches.abs = Math.abs(k.touches.diff),
            k.positions.diff = k.positions.current - k.positions.start,
            k.positions.abs = Math.abs(k.positions.diff);
            var i = k.positions.diff,
            n = k.positions.abs,
            o = k.times.end - k.times.start;
            5 > n && 300 > o && k.allowLinks === !1 && (t.freeMode || 0 === n || k.swipeReset(), t.preventLinks && (k.allowLinks = !0), t.onSlideClick && (k.allowSlideClick = !0)),
            setTimeout(function() {
                t.preventLinks && (k.allowLinks = !0),
                t.onSlideClick && (k.allowSlideClick = !0)
            },
            100);
            var s = r();
            if (!k.isMoved && t.freeMode) return k.isMoved = !1,
            t.onTouchEnd && k.fireCallback(t.onTouchEnd, k, e),
            void k.callPlugins("onTouchEnd");
            if (!k.isMoved || k.positions.current > 0 || k.positions.current < -s) return k.swipeReset(),
            t.onTouchEnd && k.fireCallback(t.onTouchEnd, k, e),
            void k.callPlugins("onTouchEnd");
            if (k.isMoved = !1, t.freeMode) {
                if (t.freeModeFluid) {
                    var a, l = 1e3 * t.momentumRatio,
                    d = k.velocity * l,
                    p = k.positions.current + d,
                    u = !1,
                    c = 20 * Math.abs(k.velocity) * t.momentumBounceRatio; - s > p && (t.momentumBounce && k.support.transitions ? ( - c > p + s && (p = -s - c), a = -s, u = !0, U = !0) : p = -s),
                    p > 0 && (t.momentumBounce && k.support.transitions ? (p > c && (p = c), a = 0, u = !0, U = !0) : p = 0),
                    0 !== k.velocity && (l = Math.abs((p - k.positions.current) / k.velocity)),
                    k.setWrapperTranslate(p),
                    k.setWrapperTransition(l),
                    t.momentumBounce && u && k.wrapperTransitionEnd(function() {
                        U && (t.onMomentumBounce && k.fireCallback(t.onMomentumBounce, k), k.callPlugins("onMomentumBounce"), k.setWrapperTranslate(a), k.setWrapperTransition(300))
                    }),
                    k.updateActiveSlide(p)
                }
                return (!t.freeModeFluid || o >= 300) && k.updateActiveSlide(k.positions.current),
                t.onTouchEnd && k.fireCallback(t.onTouchEnd, k, e),
                void k.callPlugins("onTouchEnd")
            }
            W = 0 > i ? "toNext": "toPrev",
            "toNext" === W && 300 >= o && (30 > n || !t.shortSwipes ? k.swipeReset() : k.swipeNext(!0)),
            "toPrev" === W && 300 >= o && (30 > n || !t.shortSwipes ? k.swipeReset() : k.swipePrev(!0));
            var f = 0;
            if ("auto" === t.slidesPerView) {
                for (var h, g = Math.abs(k.getWrapperTranslate()), w = 0, m = 0; m < k.slides.length; m++) if (h = O ? k.slides[m].getWidth(!0, t.roundLengths) : k.slides[m].getHeight(!0, t.roundLengths), w += h, w > g) {
                    f = h;
                    break
                }
                f > G && (f = G)
            } else f = A * t.slidesPerView;
            "toNext" === W && o > 300 && (n >= f * t.longSwipesRatio ? k.swipeNext(!0) : k.swipeReset()),
            "toPrev" === W && o > 300 && (n >= f * t.longSwipesRatio ? k.swipePrev(!0) : k.swipeReset()),
            t.onTouchEnd && k.fireCallback(t.onTouchEnd, k, e),
            k.callPlugins("onTouchEnd")
        }
    }
    function v(e) {
        var i = !1;
        do e.className.indexOf(t.noSwipingClass) > -1 && (i = !0),
        e = e.parentElement;
        while (!i && e.parentElement && -1 === e.className.indexOf(t.wrapperClass));
        return ! i && e.className.indexOf(t.wrapperClass) > -1 && e.className.indexOf(t.noSwipingClass) > -1 && (i = !0),
        i
    }
    function S(e, t) {
        var i, n = document.createElement("div");
        return n.innerHTML = t,
        i = n.firstChild,
        i.className += " " + e,
        i.outerHTML
    }
    function T(e, i, n) {
        function r() {
            var o = +new Date,
            u = o - s;
            a += l * u / (1e3 / 60),
            p = "toNext" === d ? a > e: e > a,
            p ? (k.setWrapperTranslate(Math.round(a)), k._DOMAnimating = !0, window.setTimeout(function() {
                r()
            },
            1e3 / 60)) : (t.onSlideChangeEnd && ("to" === i ? n.runCallbacks === !0 && k.fireCallback(t.onSlideChangeEnd, k) : k.fireCallback(t.onSlideChangeEnd, k)), k.setWrapperTranslate(e), k._DOMAnimating = !1)
        }
        var o = "to" === i && n.speed >= 0 ? n.speed: t.speed,
        s = +new Date;
        if (k.support.transitions || !t.DOMAnimation) k.setWrapperTranslate(e),
        k.setWrapperTransition(o);
        else {
            var a = k.getWrapperTranslate(),
            l = Math.ceil((e - a) / o * (1e3 / 60)),
            d = a > e ? "toNext": "toPrev",
            p = "toNext" === d ? a > e: e > a;
            if (k._DOMAnimating) return;
            r()
        }
        k.updateActiveSlide(e),
        t.onSlideNext && "next" === i && k.fireCallback(t.onSlideNext, k, e),
        t.onSlidePrev && "prev" === i && k.fireCallback(t.onSlidePrev, k, e),
        t.onSlideReset && "reset" === i && k.fireCallback(t.onSlideReset, k, e),
        ("next" === i || "prev" === i || "to" === i && n.runCallbacks === !0) && y(i)
    }
    function y(e) {
        if (k.callPlugins("onSlideChangeStart"), t.onSlideChangeStart) if (t.queueStartCallbacks && k.support.transitions) {
            if (k._queueStartCallbacks) return;
            k._queueStartCallbacks = !0,
            k.fireCallback(t.onSlideChangeStart, k, e),
            k.wrapperTransitionEnd(function() {
                k._queueStartCallbacks = !1
            })
        } else k.fireCallback(t.onSlideChangeStart, k, e);
        if (t.onSlideChangeEnd) if (k.support.transitions) if (t.queueEndCallbacks) {
            if (k._queueEndCallbacks) return;
            k._queueEndCallbacks = !0,
            k.wrapperTransitionEnd(function(i) {
                k.fireCallback(t.onSlideChangeEnd, i, e)
            })
        } else k.wrapperTransitionEnd(function(i) {
            k.fireCallback(t.onSlideChangeEnd, i, e)
        });
        else t.DOMAnimation || setTimeout(function() {
            k.fireCallback(t.onSlideChangeEnd, k, e)
        },
        10)
    }
    function x() {
        var e = k.paginationButtons;
        if (e) for (var t = 0; t < e.length; t++) k.h.removeEventListener(e[t], "click", C)
    }
    function b() {
        var e = k.paginationButtons;
        if (e) for (var t = 0; t < e.length; t++) k.h.addEventListener(e[t], "click", C)
    }
    function C(e) {
        for (var t, i = e.target || e.srcElement,
        n = k.paginationButtons,
        r = 0; r < n.length; r++) i === n[r] && (t = r);
        k.swipeTo(t)
    }
    function L() {
        J = setTimeout(function() {
            t.loop ? (k.fixLoop(), k.swipeNext(!0)) : k.swipeNext(!0) || (t.autoplayStopOnLast ? (clearTimeout(J), J = void 0) : k.swipeTo(0)),
            k.wrapperTransitionEnd(function() {
                "undefined" != typeof J && L()
            })
        },
        t.autoplay)
    }
    function E() {
        k.calcSlides(),
        t.loader.slides.length > 0 && 0 === k.slides.length && k.loadSlides(),
        t.loop && k.createLoop(),
        k.init(),
        o(),
        t.pagination && k.createPagination(!0),
        t.loop || t.initialSlide > 0 ? k.swipeTo(t.initialSlide, 0, !1) : k.updateActiveSlide(0),
        t.autoplay && k.startAutoplay(),
        k.centerIndex = k.activeIndex,
        t.onSwiperCreated && k.fireCallback(t.onSwiperCreated, k),
        k.callPlugins("onSwiperCreated")
    }
    if (document.body.__defineGetter__ && HTMLElement) {
        var M = HTMLElement.prototype;
        M.__defineGetter__ && M.__defineGetter__("outerHTML",
        function() {
            return (new XMLSerializer).serializeToString(this)
        })
    }
    if (window.getComputedStyle || (window.getComputedStyle = function(e) {
        return this.el = e,
        this.getPropertyValue = function(t) {
            var i = /(\-([a-z]){1})/g;
            return "float" === t && (t = "styleFloat"),
            i.test(t) && (t = t.replace(i,
            function() {
                return arguments[2].toUpperCase()
            })),
            e.currentStyle[t] ? e.currentStyle[t] : null
        },
        this
    }), Array.prototype.indexOf || (Array.prototype.indexOf = function(e, t) {
        for (var i = t || 0,
        n = this.length; n > i; i++) if (this[i] === e) return i;
        return - 1
    }), (document.querySelectorAll || window.jQuery) && "undefined" != typeof e && (e.nodeType || 0 !== i(e).length)) {
        var k = this;
        k.touches = {
            start: 0,
            startX: 0,
            startY: 0,
            current: 0,
            currentX: 0,
            currentY: 0,
            diff: 0,
            abs: 0
        },
        k.positions = {
            start: 0,
            abs: 0,
            diff: 0,
            current: 0
        },
        k.times = {
            start: 0,
            end: 0
        },
        k.id = (new Date).getTime(),
        k.container = e.nodeType ? e: i(e)[0],
        k.isTouched = !1,
        k.isMoved = !1,
        k.activeIndex = 0,
        k.centerIndex = 0,
        k.activeLoaderIndex = 0,
        k.activeLoopIndex = 0,
        k.previousIndex = null,
        k.velocity = 0,
        k.snapGrid = [],
        k.slidesGrid = [],
        k.imagesToLoad = [],
        k.imagesLoaded = 0,
        k.wrapperLeft = 0,
        k.wrapperRight = 0,
        k.wrapperTop = 0,
        k.wrapperBottom = 0,
        k.isAndroid = navigator.userAgent.toLowerCase().indexOf("android") >= 0;
        var P, A, I, W, D, G, N = {
            eventTarget: "wrapper",
            mode: "horizontal",
            touchRatio: 1,
            speed: 300,
            freeMode: !1,
            freeModeFluid: !1,
            momentumRatio: 1,
            momentumBounce: !0,
            momentumBounceRatio: 1,
            slidesPerView: 1,
            slidesPerGroup: 1,
            slidesPerViewFit: !0,
            simulateTouch: !0,
            followFinger: !0,
            shortSwipes: !0,
            longSwipesRatio: .5,
            moveStartThreshold: !1,
            onlyExternal: !1,
            createPagination: !0,
            pagination: !1,
            paginationElement: "span",
            paginationClickable: !1,
            paginationAsRange: !0,
            resistance: !0,
            scrollContainer: !1,
            preventLinks: !0,
            preventLinksPropagation: !1,
            noSwiping: !1,
            noSwipingClass: "swiper-no-swiping",
            initialSlide: 0,
            keyboardControl: !1,
            mousewheelControl: !1,
            mousewheelControlForceToAxis: !1,
            useCSS3Transforms: !0,
            autoplay: !1,
            autoplayDisableOnInteraction: !0,
            autoplayStopOnLast: !1,
            loop: !1,
            loopAdditionalSlides: 0,
            roundLengths: !1,
            calculateHeight: !1,
            cssWidthAndHeight: !1,
            updateOnImagesReady: !0,
            releaseFormElements: !0,
            watchActiveIndex: !1,
            visibilityFullFit: !1,
            offsetPxBefore: 0,
            offsetPxAfter: 0,
            offsetSlidesBefore: 0,
            offsetSlidesAfter: 0,
            centeredSlides: !1,
            queueStartCallbacks: !1,
            queueEndCallbacks: !1,
            autoResize: !0,
            resizeReInit: !1,
            DOMAnimation: !0,
            loader: {
                slides: [],
                slidesHTMLType: "inner",
                surroundGroups: 1,
                logic: "reload",
                loadAllSlides: !1
            },
            slideElement: "div",
            slideClass: "swiper-slide",
            slideActiveClass: "swiper-slide-active",
            slideVisibleClass: "swiper-slide-visible",
            slideDuplicateClass: "swiper-slide-duplicate",
            wrapperClass: "swiper-wrapper",
            paginationElementClass: "swiper-pagination-switch",
            paginationActiveClass: "swiper-active-switch",
            paginationVisibleClass: "swiper-visible-switch"
        };
        t = t || {};
        for (var R in N) if (R in t && "object" == typeof t[R]) for (var F in N[R]) F in t[R] || (t[R][F] = N[R][F]);
        else R in t || (t[R] = N[R]);
        k.params = t,
        t.scrollContainer && (t.freeMode = !0, t.freeModeFluid = !0),
        t.loop && (t.resistance = "100%");
        var O = "horizontal" === t.mode,
        z = ["mousedown", "mousemove", "mouseup"];
        k.browser.ie10 && (z = ["MSPointerDown", "MSPointerMove", "MSPointerUp"]),
        k.browser.ie11 && (z = ["pointerdown", "pointermove", "pointerup"]),
        k.touchEvents = {
            touchStart: k.support.touch || !t.simulateTouch ? "touchstart": z[0],
            touchMove: k.support.touch || !t.simulateTouch ? "touchmove": z[1],
            touchEnd: k.support.touch || !t.simulateTouch ? "touchend": z[2]
        };
        for (var V = k.container.childNodes.length - 1; V >= 0; V--) if (k.container.childNodes[V].className) for (var B = k.container.childNodes[V].className.split(/\s+/), H = 0; H < B.length; H++) B[H] === t.wrapperClass && (P = k.container.childNodes[V]);
        k.wrapper = P,
        k._extendSwiperSlide = function(e) {
            return e.append = function() {
                return t.loop ? e.insertAfter(k.slides.length - k.loopedSlides) : (k.wrapper.appendChild(e), k.reInit()),
                e
            },
            e.prepend = function() {
                return t.loop ? (k.wrapper.insertBefore(e, k.slides[k.loopedSlides]), k.removeLoopedSlides(), k.calcSlides(), k.createLoop()) : k.wrapper.insertBefore(e, k.wrapper.firstChild),
                k.reInit(),
                e
            },
            e.insertAfter = function(i) {
                if ("undefined" == typeof i) return ! 1;
                var n;
                return t.loop ? (n = k.slides[i + 1 + k.loopedSlides], n ? k.wrapper.insertBefore(e, n) : k.wrapper.appendChild(e), k.removeLoopedSlides(), k.calcSlides(), k.createLoop()) : (n = k.slides[i + 1], k.wrapper.insertBefore(e, n)),
                k.reInit(),
                e
            },
            e.clone = function() {
                return k._extendSwiperSlide(e.cloneNode(!0))
            },
            e.remove = function() {
                k.wrapper.removeChild(e),
                k.reInit()
            },
            e.html = function(t) {
                return "undefined" == typeof t ? e.innerHTML: (e.innerHTML = t, e)
            },
            e.index = function() {
                for (var t, i = k.slides.length - 1; i >= 0; i--) e === k.slides[i] && (t = i);
                return t
            },
            e.isActive = function() {
                return e.index() === k.activeIndex ? !0 : !1
            },
            e.swiperSlideDataStorage || (e.swiperSlideDataStorage = {}),
            e.getData = function(t) {
                return e.swiperSlideDataStorage[t]
            },
            e.setData = function(t, i) {
                return e.swiperSlideDataStorage[t] = i,
                e
            },
            e.data = function(t, i) {
                return "undefined" == typeof i ? e.getAttribute("data-" + t) : (e.setAttribute("data-" + t, i), e)
            },
            e.getWidth = function(t, i) {
                return k.h.getWidth(e, t, i)
            },
            e.getHeight = function(t, i) {
                return k.h.getHeight(e, t, i)
            },
            e.getOffset = function() {
                return k.h.getOffset(e)
            },
            e
        },
        k.calcSlides = function(e) {
            var i = k.slides ? k.slides.length: !1;
            k.slides = [],
            k.displaySlides = [];
            for (var n = 0; n < k.wrapper.childNodes.length; n++) if (k.wrapper.childNodes[n].className) for (var r = k.wrapper.childNodes[n].className, o = r.split(/\s+/), l = 0; l < o.length; l++) o[l] === t.slideClass && k.slides.push(k.wrapper.childNodes[n]);
            for (n = k.slides.length - 1; n >= 0; n--) k._extendSwiperSlide(k.slides[n]);
            i !== !1 && (i !== k.slides.length || e) && (a(), s(), k.updateActiveSlide(), k.params.pagination && k.createPagination(), k.callPlugins("numberOfSlidesChanged"))
        },
        k.createSlide = function(e, i, n) {
            i = i || k.params.slideClass,
            n = n || t.slideElement;
            var r = document.createElement(n);
            return r.innerHTML = e || "",
            r.className = i,
            k._extendSwiperSlide(r)
        },
        k.appendSlide = function(e, t, i) {
            return e ? e.nodeType ? k._extendSwiperSlide(e).append() : k.createSlide(e, t, i).append() : void 0
        },
        k.prependSlide = function(e, t, i) {
            return e ? e.nodeType ? k._extendSwiperSlide(e).prepend() : k.createSlide(e, t, i).prepend() : void 0
        },
        k.insertSlideAfter = function(e, t, i, n) {
            return "undefined" == typeof e ? !1 : t.nodeType ? k._extendSwiperSlide(t).insertAfter(e) : k.createSlide(t, i, n).insertAfter(e)
        },
        k.removeSlide = function(e) {
            if (k.slides[e]) {
                if (t.loop) {
                    if (!k.slides[e + k.loopedSlides]) return ! 1;
                    k.slides[e + k.loopedSlides].remove(),
                    k.removeLoopedSlides(),
                    k.calcSlides(),
                    k.createLoop()
                } else k.slides[e].remove();
                return ! 0
            }
            return ! 1
        },
        k.removeLastSlide = function() {
            return k.slides.length > 0 ? (t.loop ? (k.slides[k.slides.length - 1 - k.loopedSlides].remove(), k.removeLoopedSlides(), k.calcSlides(), k.createLoop()) : k.slides[k.slides.length - 1].remove(), !0) : !1
        },
        k.removeAllSlides = function() {
            for (var e = k.slides.length - 1; e >= 0; e--) k.slides[e].remove()
        },
        k.getSlide = function(e) {
            return k.slides[e]
        },
        k.getLastSlide = function() {
            return k.slides[k.slides.length - 1]
        },
        k.getFirstSlide = function() {
            return k.slides[0]
        },
        k.activeSlide = function() {
            return k.slides[k.activeIndex]
        },
        k.fireCallback = function() {
            var e = arguments[0];
            if ("[object Array]" === Object.prototype.toString.call(e)) for (var i = 0; i < e.length; i++)"function" == typeof e[i] && e[i](arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
            else "[object String]" === Object.prototype.toString.call(e) ? t["on" + e] && k.fireCallback(t["on" + e], arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]) : e(arguments[1], arguments[2], arguments[3], arguments[4], arguments[5])
        },
        k.addCallback = function(e, t) {
            var i, r = this;
            return r.params["on" + e] ? n(this.params["on" + e]) ? this.params["on" + e].push(t) : "function" == typeof this.params["on" + e] ? (i = this.params["on" + e], this.params["on" + e] = [], this.params["on" + e].push(i), this.params["on" + e].push(t)) : void 0 : (this.params["on" + e] = [], this.params["on" + e].push(t))
        },
        k.removeCallbacks = function(e) {
            k.params["on" + e] && (k.params["on" + e] = null)
        };
        var _ = [];
        for (var Y in k.plugins) if (t[Y]) {
            var X = k.plugins[Y](k, t[Y]);
            X && _.push(X)
        }
        k.callPlugins = function(e, t) {
            t || (t = {});
            for (var i = 0; i < _.length; i++) e in _[i] && _[i][e](t)
        },
        !k.browser.ie10 && !k.browser.ie11 || t.onlyExternal || k.wrapper.classList.add("swiper-wp8-" + (O ? "horizontal": "vertical")),
        t.freeMode && (k.container.className += " swiper-free-mode"),
        k.initialized = !1,
        k.init = function(e, i) {
            var n = k.h.getWidth(k.container, !1, t.roundLengths),
            r = k.h.getHeight(k.container, !1, t.roundLengths);
            if (n !== k.width || r !== k.height || e) {
                k.width = n,
                k.height = r;
                var o, s, a, l, d, p, u;
                G = O ? n: r;
                var c = k.wrapper;
                if (e && k.calcSlides(i), "auto" === t.slidesPerView) {
                    var f = 0,
                    h = 0;
                    t.slidesOffset > 0 && (c.style.paddingLeft = "", c.style.paddingRight = "", c.style.paddingTop = "", c.style.paddingBottom = ""),
                    c.style.width = "",
                    c.style.height = "",
                    t.offsetPxBefore > 0 && (O ? k.wrapperLeft = t.offsetPxBefore: k.wrapperTop = t.offsetPxBefore),
                    t.offsetPxAfter > 0 && (O ? k.wrapperRight = t.offsetPxAfter: k.wrapperBottom = t.offsetPxAfter),
                    t.centeredSlides && (O ? (k.wrapperLeft = (G - this.slides[0].getWidth(!0, t.roundLengths)) / 2, k.wrapperRight = (G - k.slides[k.slides.length - 1].getWidth(!0, t.roundLengths)) / 2) : (k.wrapperTop = (G - k.slides[0].getHeight(!0, t.roundLengths)) / 2, k.wrapperBottom = (G - k.slides[k.slides.length - 1].getHeight(!0, t.roundLengths)) / 2)),
                    O ? (k.wrapperLeft >= 0 && (c.style.paddingLeft = k.wrapperLeft + "px"), k.wrapperRight >= 0 && (c.style.paddingRight = k.wrapperRight + "px")) : (k.wrapperTop >= 0 && (c.style.paddingTop = k.wrapperTop + "px"), k.wrapperBottom >= 0 && (c.style.paddingBottom = k.wrapperBottom + "px")),
                    p = 0;
                    var g = 0;
                    for (k.snapGrid = [], k.slidesGrid = [], a = 0, u = 0; u < k.slides.length; u++) {
                        o = k.slides[u].getWidth(!0, t.roundLengths),
                        s = k.slides[u].getHeight(!0, t.roundLengths),
                        t.calculateHeight && (a = Math.max(a, s));
                        var w = O ? o: s;
                        if (t.centeredSlides) {
                            var m = u === k.slides.length - 1 ? 0 : k.slides[u + 1].getWidth(!0, t.roundLengths),
                            v = u === k.slides.length - 1 ? 0 : k.slides[u + 1].getHeight(!0, t.roundLengths),
                            S = O ? m: v;
                            if (w > G) {
                                if (t.slidesPerViewFit) k.snapGrid.push(p + k.wrapperLeft),
                                k.snapGrid.push(p + w - G + k.wrapperLeft);
                                else for (var T = 0; T <= Math.floor(w / (G + k.wrapperLeft)); T++) k.snapGrid.push(0 === T ? p + k.wrapperLeft: p + k.wrapperLeft + G * T);
                                k.slidesGrid.push(p + k.wrapperLeft)
                            } else k.snapGrid.push(g),
                            k.slidesGrid.push(g);
                            g += w / 2 + S / 2
                        } else {
                            if (w > G) if (t.slidesPerViewFit) k.snapGrid.push(p),
                            k.snapGrid.push(p + w - G);
                            else if (0 !== G) for (var y = 0; y <= Math.floor(w / G); y++) k.snapGrid.push(p + G * y);
                            else k.snapGrid.push(p);
                            else k.snapGrid.push(p);
                            k.slidesGrid.push(p)
                        }
                        p += w,
                        f += o,
                        h += s
                    }
                    t.calculateHeight && (k.height = a),
                    O ? (I = f + k.wrapperRight + k.wrapperLeft, c.style.width = f + "px", c.style.height = k.height + "px") : (I = h + k.wrapperTop + k.wrapperBottom, c.style.width = k.width + "px", c.style.height = h + "px")
                } else if (t.scrollContainer) c.style.width = "",
                c.style.height = "",
                l = k.slides[0].getWidth(!0, t.roundLengths),
                d = k.slides[0].getHeight(!0, t.roundLengths),
                I = O ? l: d,
                c.style.width = l + "px",
                c.style.height = d + "px",
                A = O ? l: d;
                else {
                    if (t.calculateHeight) {
                        for (a = 0, d = 0, O || (k.container.style.height = ""), c.style.height = "", u = 0; u < k.slides.length; u++) k.slides[u].style.height = "",
                        a = Math.max(k.slides[u].getHeight(!0), a),
                        O || (d += k.slides[u].getHeight(!0));
                        s = a,
                        k.height = s,
                        O ? d = s: (G = s, k.container.style.height = G + "px")
                    } else s = O ? k.height: k.height / t.slidesPerView,
                    t.roundLengths && (s = Math.round(s)),
                    d = O ? k.height: k.slides.length * s;
                    for (o = O ? k.width / t.slidesPerView: k.width, t.roundLengths && (o = Math.round(o)), l = O ? k.slides.length * o: k.width, A = O ? o: s, t.offsetSlidesBefore > 0 && (O ? k.wrapperLeft = A * t.offsetSlidesBefore: k.wrapperTop = A * t.offsetSlidesBefore), t.offsetSlidesAfter > 0 && (O ? k.wrapperRight = A * t.offsetSlidesAfter: k.wrapperBottom = A * t.offsetSlidesAfter), t.offsetPxBefore > 0 && (O ? k.wrapperLeft = t.offsetPxBefore: k.wrapperTop = t.offsetPxBefore), t.offsetPxAfter > 0 && (O ? k.wrapperRight = t.offsetPxAfter: k.wrapperBottom = t.offsetPxAfter), t.centeredSlides && (O ? (k.wrapperLeft = (G - A) / 2, k.wrapperRight = (G - A) / 2) : (k.wrapperTop = (G - A) / 2, k.wrapperBottom = (G - A) / 2)), O ? (k.wrapperLeft > 0 && (c.style.paddingLeft = k.wrapperLeft + "px"), k.wrapperRight > 0 && (c.style.paddingRight = k.wrapperRight + "px")) : (k.wrapperTop > 0 && (c.style.paddingTop = k.wrapperTop + "px"), k.wrapperBottom > 0 && (c.style.paddingBottom = k.wrapperBottom + "px")), I = O ? l + k.wrapperRight + k.wrapperLeft: d + k.wrapperTop + k.wrapperBottom, t.cssWidthAndHeight || (parseFloat(l) > 0 && (c.style.width = l + "px"), parseFloat(d) > 0 && (c.style.height = d + "px")), p = 0, k.snapGrid = [], k.slidesGrid = [], u = 0; u < k.slides.length; u++) k.snapGrid.push(p),
                    k.slidesGrid.push(p),
                    p += A,
                    t.cssWidthAndHeight || (parseFloat(o) > 0 && (k.slides[u].style.width = o + "px"), parseFloat(s) > 0 && (k.slides[u].style.height = s + "px"))
                }
                k.initialized ? (k.callPlugins("onInit"), t.onInit && k.fireCallback(t.onInit, k)) : (k.callPlugins("onFirstInit"), t.onFirstInit && k.fireCallback(t.onFirstInit, k)),
                k.initialized = !0
            }
        },
        k.reInit = function(e) {
            k.init(!0, e)
        },
        k.resizeFix = function(e) {
            k.callPlugins("beforeResizeFix"),
            k.init(t.resizeReInit || e),
            t.freeMode ? k.getWrapperTranslate() < -r() && (k.setWrapperTransition(0), k.setWrapperTranslate( - r())) : (k.swipeTo(t.loop ? k.activeLoopIndex: k.activeIndex, 0, !1), t.autoplay && (k.support.transitions && "undefined" != typeof J ? "undefined" != typeof J && (clearTimeout(J), J = void 0, k.startAutoplay()) : "undefined" != typeof et && (clearInterval(et), et = void 0, k.startAutoplay()))),
            k.callPlugins("afterResizeFix")
        },
        k.destroy = function() {
            var e = k.h.removeEventListener,
            i = "wrapper" === t.eventTarget ? k.wrapper: k.container;
            k.browser.ie10 || k.browser.ie11 ? (e(i, k.touchEvents.touchStart, g), e(document, k.touchEvents.touchMove, w), e(document, k.touchEvents.touchEnd, m)) : (k.support.touch && (e(i, "touchstart", g), e(i, "touchmove", w), e(i, "touchend", m)), t.simulateTouch && (e(i, "mousedown", g), e(document, "mousemove", w), e(document, "mouseup", m))),
            t.autoResize && e(window, "resize", k.resizeFix),
            a(),
            t.paginationClickable && x(),
            t.mousewheelControl && k._wheelEvent && e(k.container, k._wheelEvent, d),
            t.keyboardControl && e(document, "keydown", l),
            t.autoplay && k.stopAutoplay(),
            k.callPlugins("onDestroy"),
            k = null
        },
        k.disableKeyboardControl = function() {
            t.keyboardControl = !1,
            k.h.removeEventListener(document, "keydown", l)
        },
        k.enableKeyboardControl = function() {
            t.keyboardControl = !0,
            k.h.addEventListener(document, "keydown", l)
        };
        var q = (new Date).getTime();
        if (k.disableMousewheelControl = function() {
            return k._wheelEvent ? (t.mousewheelControl = !1, k.h.removeEventListener(k.container, k._wheelEvent, d), !0) : !1
        },
        k.enableMousewheelControl = function() {
            return k._wheelEvent ? (t.mousewheelControl = !0, k.h.addEventListener(k.container, k._wheelEvent, d), !0) : !1
        },
        t.grabCursor) {
            var j = k.container.style;
            j.cursor = "move",
            j.cursor = "grab",
            j.cursor = "-moz-grab",
            j.cursor = "-webkit-grab"
        }
        k.allowSlideClick = !0,
        k.allowLinks = !0;
        var K, Q, Z, $ = !1,
        U = !0;
        k.swipeNext = function(e) { ! e && t.loop && k.fixLoop(),
            !e && t.autoplay && k.stopAutoplay(!0),
            k.callPlugins("onSwipeNext");
            var i = k.getWrapperTranslate(),
            n = i;
            if ("auto" === t.slidesPerView) {
                for (var o = 0; o < k.snapGrid.length; o++) if ( - i >= k.snapGrid[o] && -i < k.snapGrid[o + 1]) {
                    n = -k.snapGrid[o + 1];
                    break
                }
            } else {
                var s = A * t.slidesPerGroup;
                n = -(Math.floor(Math.abs(i) / Math.floor(s)) * s + s)
            }
            return n < -r() && (n = -r()),
            n === i ? !1 : (T(n, "next"), !0)
        },
        k.swipePrev = function(e) { ! e && t.loop && k.fixLoop(),
            !e && t.autoplay && k.stopAutoplay(!0),
            k.callPlugins("onSwipePrev");
            var i, n = Math.ceil(k.getWrapperTranslate());
            if ("auto" === t.slidesPerView) {
                i = 0;
                for (var r = 1; r < k.snapGrid.length; r++) {
                    if ( - n === k.snapGrid[r]) {
                        i = -k.snapGrid[r - 1];
                        break
                    }
                    if ( - n > k.snapGrid[r] && -n < k.snapGrid[r + 1]) {
                        i = -k.snapGrid[r];
                        break
                    }
                }
            } else {
                var o = A * t.slidesPerGroup;
                i = -(Math.ceil( - n / o) - 1) * o
            }
            return i > 0 && (i = 0),
            i === n ? !1 : (T(i, "prev"), !0)
        },
        k.swipeReset = function() {
            k.callPlugins("onSwipeReset");
            var e, i = k.getWrapperTranslate(),
            n = A * t.slidesPerGroup;
            if ( - r(), "auto" === t.slidesPerView) {
                e = 0;
                for (var o = 0; o < k.snapGrid.length; o++) {
                    if ( - i === k.snapGrid[o]) return;
                    if ( - i >= k.snapGrid[o] && -i < k.snapGrid[o + 1]) {
                        e = k.positions.diff > 0 ? -k.snapGrid[o + 1] : -k.snapGrid[o];
                        break
                    }
                } - i >= k.snapGrid[k.snapGrid.length - 1] && (e = -k.snapGrid[k.snapGrid.length - 1]),
                i <= -r() && (e = -r())
            } else e = 0 > i ? Math.round(i / n) * n: 0;
            return t.scrollContainer && (e = 0 > i ? i: 0),
            e < -r() && (e = -r()),
            t.scrollContainer && G > A && (e = 0),
            e === i ? !1 : (T(e, "reset"), !0)
        },
        k.swipeTo = function(e, i, n) {
            e = parseInt(e, 10),
            k.callPlugins("onSwipeTo", {
                index: e,
                speed: i
            }),
            t.loop && (e += k.loopedSlides);
            var o = k.getWrapperTranslate();
            if (! (e > k.slides.length - 1 || 0 > e)) {
                var s;
                return s = "auto" === t.slidesPerView ? -k.slidesGrid[e] : -e * A,
                s < -r() && (s = -r()),
                s === o ? !1 : (n = n === !1 ? !1 : !0, T(s, "to", {
                    index: e,
                    speed: i,
                    runCallbacks: n
                }), !0)
            }
        },
        k._queueStartCallbacks = !1,
        k._queueEndCallbacks = !1,
        k.updateActiveSlide = function(e) {
            if (k.initialized && 0 !== k.slides.length) {
                k.previousIndex = k.activeIndex,
                "undefined" == typeof e && (e = k.getWrapperTranslate()),
                e > 0 && (e = 0);
                var i;
                if ("auto" === t.slidesPerView) {
                    if (k.activeIndex = k.slidesGrid.indexOf( - e), k.activeIndex < 0) {
                        for (i = 0; i < k.slidesGrid.length - 1 && !( - e > k.slidesGrid[i] && -e < k.slidesGrid[i + 1]); i++);
                        var n = Math.abs(k.slidesGrid[i] + e),
                        r = Math.abs(k.slidesGrid[i + 1] + e);
                        k.activeIndex = r >= n ? i: i + 1
                    }
                } else k.activeIndex = Math[t.visibilityFullFit ? "ceil": "round"]( - e / A);
                if (k.activeIndex === k.slides.length && (k.activeIndex = k.slides.length - 1), k.activeIndex < 0 && (k.activeIndex = 0), k.slides[k.activeIndex]) {
                    if (k.calcVisibleSlides(e), k.support.classList) {
                        var o;
                        for (i = 0; i < k.slides.length; i++) o = k.slides[i],
                        o.classList.remove(t.slideActiveClass),
                        k.visibleSlides.indexOf(o) >= 0 ? o.classList.add(t.slideVisibleClass) : o.classList.remove(t.slideVisibleClass);
                        k.slides[k.activeIndex].classList.add(t.slideActiveClass)
                    } else {
                        var s = new RegExp("\\s*" + t.slideActiveClass),
                        a = new RegExp("\\s*" + t.slideVisibleClass);
                        for (i = 0; i < k.slides.length; i++) k.slides[i].className = k.slides[i].className.replace(s, "").replace(a, ""),
                        k.visibleSlides.indexOf(k.slides[i]) >= 0 && (k.slides[i].className += " " + t.slideVisibleClass);
                        k.slides[k.activeIndex].className += " " + t.slideActiveClass
                    }
                    if (t.loop) {
                        var l = k.loopedSlides;
                        k.activeLoopIndex = k.activeIndex - l,
                        k.activeLoopIndex >= k.slides.length - 2 * l && (k.activeLoopIndex = k.slides.length - 2 * l - k.activeLoopIndex),
                        k.activeLoopIndex < 0 && (k.activeLoopIndex = k.slides.length - 2 * l + k.activeLoopIndex),
                        k.activeLoopIndex < 0 && (k.activeLoopIndex = 0)
                    } else k.activeLoopIndex = k.activeIndex;
                    t.pagination && k.updatePagination(e)
                }
            }
        },
        k.createPagination = function(e) {
            if (t.paginationClickable && k.paginationButtons && x(), k.paginationContainer = t.pagination.nodeType ? t.pagination: i(t.pagination)[0], t.createPagination) {
                var n = "",
                r = k.slides.length,
                o = r;
                t.loop && (o -= 2 * k.loopedSlides);
                for (var s = 0; o > s; s++) n += "<" + t.paginationElement + ' class="' + t.paginationElementClass + '"></' + t.paginationElement + ">";
                k.paginationContainer.innerHTML = n
            }
            k.paginationButtons = i("." + t.paginationElementClass, k.paginationContainer),
            e || k.updatePagination(),
            k.callPlugins("onCreatePagination"),
            t.paginationClickable && b()
        },
        k.updatePagination = function(e) {
            if (t.pagination && !(k.slides.length < 1)) {
                var n = i("." + t.paginationActiveClass, k.paginationContainer);
                if (n) {
                    var r = k.paginationButtons;
                    if (0 !== r.length) {
                        for (var o = 0; o < r.length; o++) r[o].className = t.paginationElementClass;
                        var s = t.loop ? k.loopedSlides: 0;
                        if (t.paginationAsRange) {
                            k.visibleSlides || k.calcVisibleSlides(e);
                            var a, l = [];
                            for (a = 0; a < k.visibleSlides.length; a++) {
                                var d = k.slides.indexOf(k.visibleSlides[a]) - s;
                                t.loop && 0 > d && (d = k.slides.length - 2 * k.loopedSlides + d),
                                t.loop && d >= k.slides.length - 2 * k.loopedSlides && (d = k.slides.length - 2 * k.loopedSlides - d, d = Math.abs(d)),
                                l.push(d)
                            }
                            for (a = 0; a < l.length; a++) r[l[a]] && (r[l[a]].className += " " + t.paginationVisibleClass);
                            t.loop ? void 0 !== r[k.activeLoopIndex] && (r[k.activeLoopIndex].className += " " + t.paginationActiveClass) : r[k.activeIndex].className += " " + t.paginationActiveClass
                        } else t.loop ? r[k.activeLoopIndex] && (r[k.activeLoopIndex].className += " " + t.paginationActiveClass + " " + t.paginationVisibleClass) : r[k.activeIndex].className += " " + t.paginationActiveClass + " " + t.paginationVisibleClass
                    }
                }
            }
        },
        k.calcVisibleSlides = function(e) {
            var i = [],
            n = 0,
            r = 0,
            o = 0;
            O && k.wrapperLeft > 0 && (e += k.wrapperLeft),
            !O && k.wrapperTop > 0 && (e += k.wrapperTop);
            for (var s = 0; s < k.slides.length; s++) {
                n += r,
                r = "auto" === t.slidesPerView ? O ? k.h.getWidth(k.slides[s], !0, t.roundLengths) : k.h.getHeight(k.slides[s], !0, t.roundLengths) : A,
                o = n + r;
                var a = !1;
                t.visibilityFullFit ? (n >= -e && -e + G >= o && (a = !0), -e >= n && o >= -e + G && (a = !0)) : (o > -e && -e + G >= o && (a = !0), n >= -e && -e + G > n && (a = !0), -e > n && o > -e + G && (a = !0)),
                a && i.push(k.slides[s])
            }
            0 === i.length && (i = [k.slides[k.activeIndex]]),
            k.visibleSlides = i
        };
        var J, et;
        k.startAutoplay = function() {
            if (k.support.transitions) {
                if ("undefined" != typeof J) return ! 1;
                if (!t.autoplay) return;
                k.callPlugins("onAutoplayStart"),
                t.onAutoplayStart && k.fireCallback(t.onAutoplayStart, k),
                L()
            } else {
                if ("undefined" != typeof et) return ! 1;
                if (!t.autoplay) return;
                k.callPlugins("onAutoplayStart"),
                t.onAutoplayStart && k.fireCallback(t.onAutoplayStart, k),
                et = setInterval(function() {
                    t.loop ? (k.fixLoop(), k.swipeNext(!0)) : k.swipeNext(!0) || (t.autoplayStopOnLast ? (clearInterval(et), et = void 0) : k.swipeTo(0))
                },
                t.autoplay)
            }
        },
        k.stopAutoplay = function(e) {
            if (k.support.transitions) {
                if (!J) return;
                J && clearTimeout(J),
                J = void 0,
                e && !t.autoplayDisableOnInteraction && k.wrapperTransitionEnd(function() {
                    L()
                }),
                k.callPlugins("onAutoplayStop"),
                t.onAutoplayStop && k.fireCallback(t.onAutoplayStop, k)
            } else et && clearInterval(et),
            et = void 0,
            k.callPlugins("onAutoplayStop"),
            t.onAutoplayStop && k.fireCallback(t.onAutoplayStop, k)
        },
        k.loopCreated = !1,
        k.removeLoopedSlides = function() {
            if (k.loopCreated) for (var e = 0; e < k.slides.length; e++) k.slides[e].getData("looped") === !0 && k.wrapper.removeChild(k.slides[e])
        },
        k.createLoop = function() {
            if (0 !== k.slides.length) {
                k.loopedSlides = "auto" === t.slidesPerView ? t.loopedSlides || 1 : t.slidesPerView + t.loopAdditionalSlides,
                k.loopedSlides > k.slides.length && (k.loopedSlides = k.slides.length);
                var e, i = "",
                n = "",
                r = "",
                o = k.slides.length,
                s = Math.floor(k.loopedSlides / o),
                a = k.loopedSlides % o;
                for (e = 0; s * o > e; e++) {
                    var l = e;
                    if (e >= o) {
                        var d = Math.floor(e / o);
                        l = e - o * d
                    }
                    r += k.slides[l].outerHTML
                }
                for (e = 0; a > e; e++) n += S(t.slideDuplicateClass, k.slides[e].outerHTML);
                for (e = o - a; o > e; e++) i += S(t.slideDuplicateClass, k.slides[e].outerHTML);
                var p = i + r + P.innerHTML + r + n;
                for (P.innerHTML = p, k.loopCreated = !0, k.calcSlides(), e = 0; e < k.slides.length; e++)(e < k.loopedSlides || e >= k.slides.length - k.loopedSlides) && k.slides[e].setData("looped", !0);
                k.callPlugins("onCreateLoop")
            }
        },
        k.fixLoop = function() {
            var e;
            k.activeIndex < k.loopedSlides ? (e = k.slides.length - 3 * k.loopedSlides + k.activeIndex, k.swipeTo(e, 0, !1)) : ("auto" === t.slidesPerView && k.activeIndex >= 2 * k.loopedSlides || k.activeIndex > k.slides.length - 2 * t.slidesPerView) && (e = -k.slides.length + k.activeIndex + k.loopedSlides, k.swipeTo(e, 0, !1))
        },
        k.loadSlides = function() {
            var e = "";
            k.activeLoaderIndex = 0;
            for (var i = t.loader.slides,
            n = t.loader.loadAllSlides ? i.length: t.slidesPerView * (1 + t.loader.surroundGroups), r = 0; n > r; r++) e += "outer" === t.loader.slidesHTMLType ? i[r] : "<" + t.slideElement + ' class="' + t.slideClass + '" data-swiperindex="' + r + '">' + i[r] + "</" + t.slideElement + ">";
            k.wrapper.innerHTML = e,
            k.calcSlides(!0),
            t.loader.loadAllSlides || k.wrapperTransitionEnd(k.reloadSlides, !0)
        },
        k.reloadSlides = function() {
            var e = t.loader.slides,
            i = parseInt(k.activeSlide().data("swiperindex"), 10);
            if (! (0 > i || i > e.length - 1)) {
                k.activeLoaderIndex = i;
                var n = Math.max(0, i - t.slidesPerView * t.loader.surroundGroups),
                r = Math.min(i + t.slidesPerView * (1 + t.loader.surroundGroups) - 1, e.length - 1);
                if (i > 0) {
                    var o = -A * (i - n);
                    k.setWrapperTranslate(o),
                    k.setWrapperTransition(0)
                }
                var s;
                if ("reload" === t.loader.logic) {
                    k.wrapper.innerHTML = "";
                    var a = "";
                    for (s = n; r >= s; s++) a += "outer" === t.loader.slidesHTMLType ? e[s] : "<" + t.slideElement + ' class="' + t.slideClass + '" data-swiperindex="' + s + '">' + e[s] + "</" + t.slideElement + ">";
                    k.wrapper.innerHTML = a
                } else {
                    var l = 1e3,
                    d = 0;
                    for (s = 0; s < k.slides.length; s++) {
                        var p = k.slides[s].data("swiperindex");
                        n > p || p > r ? k.wrapper.removeChild(k.slides[s]) : (l = Math.min(p, l), d = Math.max(p, d))
                    }
                    for (s = n; r >= s; s++) {
                        var u;
                        l > s && (u = document.createElement(t.slideElement), u.className = t.slideClass, u.setAttribute("data-swiperindex", s), u.innerHTML = e[s], k.wrapper.insertBefore(u, k.wrapper.firstChild)),
                        s > d && (u = document.createElement(t.slideElement), u.className = t.slideClass, u.setAttribute("data-swiperindex", s), u.innerHTML = e[s], k.wrapper.appendChild(u))
                    }
                }
                k.reInit(!0)
            }
        },
        E()
    }
};
Swiper.prototype = {
    plugins: {},
    wrapperTransitionEnd: function(e, t) {
        "use strict";
        function i() {
            if (e(r), r.params.queueEndCallbacks && (r._queueEndCallbacks = !1), !t) for (n = 0; n < s.length; n++) r.h.removeEventListener(o, s[n], i)
        }
        var n, r = this,
        o = r.wrapper,
        s = ["webkitTransitionEnd", "transitionend", "oTransitionEnd", "MSTransitionEnd", "msTransitionEnd"];
        if (e) for (n = 0; n < s.length; n++) r.h.addEventListener(o, s[n], i)
    },
    getWrapperTranslate: function(e) {
        "use strict";
        var t, i, n, r, o = this.wrapper;
        return "undefined" == typeof e && (e = "horizontal" === this.params.mode ? "x": "y"),
        this.support.transforms && this.params.useCSS3Transforms ? (n = window.getComputedStyle(o, null), window.WebKitCSSMatrix ? r = new WebKitCSSMatrix("none" === n.webkitTransform ? "": n.webkitTransform) : (r = n.MozTransform || n.OTransform || n.MsTransform || n.msTransform || n.transform || n.getPropertyValue("transform").replace("translate(", "matrix(1, 0, 0, 1,"), t = r.toString().split(",")), "x" === e && (i = window.WebKitCSSMatrix ? r.m41: parseFloat(16 === t.length ? t[12] : t[4])), "y" === e && (i = window.WebKitCSSMatrix ? r.m42: parseFloat(16 === t.length ? t[13] : t[5]))) : ("x" === e && (i = parseFloat(o.style.left, 10) || 0), "y" === e && (i = parseFloat(o.style.top, 10) || 0)),
        i || 0
    },
    setWrapperTranslate: function(e, t, i) {
        "use strict";
        var n, r = this.wrapper.style,
        o = {
            x: 0,
            y: 0,
            z: 0
        };
        3 === arguments.length ? (o.x = e, o.y = t, o.z = i) : ("undefined" == typeof t && (t = "horizontal" === this.params.mode ? "x": "y"), o[t] = e),
        this.support.transforms && this.params.useCSS3Transforms ? (n = this.support.transforms3d ? "translate3d(" + o.x + "px, " + o.y + "px, " + o.z + "px)": "translate(" + o.x + "px, " + o.y + "px)", r.webkitTransform = r.MsTransform = r.msTransform = r.MozTransform = r.OTransform = r.transform = n) : (r.left = o.x + "px", r.top = o.y + "px"),
        this.callPlugins("onSetWrapperTransform", o),
        this.params.onSetWrapperTransform && this.fireCallback(this.params.onSetWrapperTransform, this, o)
    },
    setWrapperTransition: function(e) {
        "use strict";
        var t = this.wrapper.style;
        t.webkitTransitionDuration = t.MsTransitionDuration = t.msTransitionDuration = t.MozTransitionDuration = t.OTransitionDuration = t.transitionDuration = e / 1e3 + "s",
        this.callPlugins("onSetWrapperTransition", {
            duration: e
        }),
        this.params.onSetWrapperTransition && this.fireCallback(this.params.onSetWrapperTransition, this, e)
    },
    h: {
        getWidth: function(e, t, i) {
            "use strict";
            var n = window.getComputedStyle(e, null).getPropertyValue("width"),
            r = parseFloat(n);
            return (isNaN(r) || n.indexOf("%") > 0) && (r = e.offsetWidth - parseFloat(window.getComputedStyle(e, null).getPropertyValue("padding-left")) - parseFloat(window.getComputedStyle(e, null).getPropertyValue("padding-right"))),
            t && (r += parseFloat(window.getComputedStyle(e, null).getPropertyValue("padding-left")) + parseFloat(window.getComputedStyle(e, null).getPropertyValue("padding-right"))),
            i ? Math.round(r) : r
        },
        getHeight: function(e, t, i) {
            "use strict";
            if (t) return e.offsetHeight;
            var n = window.getComputedStyle(e, null).getPropertyValue("height"),
            r = parseFloat(n);
            return (isNaN(r) || n.indexOf("%") > 0) && (r = e.offsetHeight - parseFloat(window.getComputedStyle(e, null).getPropertyValue("padding-top")) - parseFloat(window.getComputedStyle(e, null).getPropertyValue("padding-bottom"))),
            t && (r += parseFloat(window.getComputedStyle(e, null).getPropertyValue("padding-top")) + parseFloat(window.getComputedStyle(e, null).getPropertyValue("padding-bottom"))),
            i ? Math.round(r) : r
        },
        getOffset: function(e) {
            "use strict";
            var t = e.getBoundingClientRect(),
            i = document.body,
            n = e.clientTop || i.clientTop || 0,
            r = e.clientLeft || i.clientLeft || 0,
            o = window.pageYOffset || e.scrollTop,
            s = window.pageXOffset || e.scrollLeft;
            return document.documentElement && !window.pageYOffset && (o = document.documentElement.scrollTop, s = document.documentElement.scrollLeft),
            {
                top: t.top + o - n,
                left: t.left + s - r
            }
        },
        windowWidth: function() {
            "use strict";
            return window.innerWidth ? window.innerWidth: document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth: void 0
        },
        windowHeight: function() {
            "use strict";
            return window.innerHeight ? window.innerHeight: document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight: void 0
        },
        windowScroll: function() {
            "use strict";
            return "undefined" != typeof pageYOffset ? {
                left: window.pageXOffset,
                top: window.pageYOffset
            }: document.documentElement ? {
                left: document.documentElement.scrollLeft,
                top: document.documentElement.scrollTop
            }: void 0
        },
        addEventListener: function(e, t, i, n) {
            "use strict";
            "undefined" == typeof n && (n = !1),
            e.addEventListener ? e.addEventListener(t, i, n) : e.attachEvent && e.attachEvent("on" + t, i)
        },
        removeEventListener: function(e, t, i, n) {
            "use strict";
            "undefined" == typeof n && (n = !1),
            e.removeEventListener ? e.removeEventListener(t, i, n) : e.detachEvent && e.detachEvent("on" + t, i)
        }
    },
    setTransform: function(e, t) {
        "use strict";
        var i = e.style;
        i.webkitTransform = i.MsTransform = i.msTransform = i.MozTransform = i.OTransform = i.transform = t
    },
    setTranslate: function(e, t) {
        "use strict";
        var i = e.style,
        n = {
            x: t.x || 0,
            y: t.y || 0,
            z: t.z || 0
        },
        r = this.support.transforms3d ? "translate3d(" + n.x + "px," + n.y + "px," + n.z + "px)": "translate(" + n.x + "px," + n.y + "px)";
        i.webkitTransform = i.MsTransform = i.msTransform = i.MozTransform = i.OTransform = i.transform = r,
        this.support.transforms || (i.left = n.x + "px", i.top = n.y + "px")
    },
    setTransition: function(e, t) {
        "use strict";
        var i = e.style;
        i.webkitTransitionDuration = i.MsTransitionDuration = i.msTransitionDuration = i.MozTransitionDuration = i.OTransitionDuration = i.transitionDuration = t + "ms"
    },
    support: {
        touch: window.Modernizr && Modernizr.touch === !0 ||
        function() {
            "use strict";
            return !! ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch)
        } (),
        transforms3d: window.Modernizr && Modernizr.csstransforms3d === !0 ||
        function() {
            "use strict";
            var e = document.createElement("div").style;
            return "webkitPerspective" in e || "MozPerspective" in e || "OPerspective" in e || "MsPerspective" in e || "perspective" in e
        } (),
        transforms: window.Modernizr && Modernizr.csstransforms === !0 ||
        function() {
            "use strict";
            var e = document.createElement("div").style;
            return "transform" in e || "WebkitTransform" in e || "MozTransform" in e || "msTransform" in e || "MsTransform" in e || "OTransform" in e
        } (),
        transitions: window.Modernizr && Modernizr.csstransitions === !0 ||
        function() {
            "use strict";
            var e = document.createElement("div").style;
            return "transition" in e || "WebkitTransition" in e || "MozTransition" in e || "msTransition" in e || "MsTransition" in e || "OTransition" in e
        } (),
        classList: function() {
            "use strict";
            var e = document.createElement("div").style;
            return "classList" in e
        } ()
    },
    browser: {
        ie8: function() {
            "use strict";
            var e = -1;
            if ("Microsoft Internet Explorer" === navigator.appName) {
                var t = navigator.userAgent,
                i = new RegExp(/MSIE ([0-9]{1,}[\.0-9]{0,})/);
                null !== i.exec(t) && (e = parseFloat(RegExp.$1))
            }
            return - 1 !== e && 9 > e
        } (),
        ie10: window.navigator.msPointerEnabled,
        ie11: window.navigator.pointerEnabled
    }
},
(window.jQuery || window.Zepto) && !
function(e) {
    "use strict";
    e.fn.swiper = function(t) {
        var i = new Swiper(e(this)[0], t);
        return e(this).data("swiper", i),
        i
    }
} (window.jQuery || window.Zepto),
"undefined" != typeof module && (module.exports = Swiper),
function(e, t) {
    function i(e) {
        return e.replace(/([a-z])([A-Z])/, "$1-$2").toLowerCase()
    }
    function n(e) {
        return r ? r + e: e.toLowerCase()
    }
    var r, o, s, a, l, d, p, u, c, f, h = "",
    g = {
        Webkit: "webkit",
        Moz: "",
        O: "o"
    },
    w = window.document,
    m = w.createElement("div"),
    v = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i,
    S = {};
    e.each(g,
    function(e, i) {
        return m.style[e + "TransitionProperty"] !== t ? (h = "-" + e.toLowerCase() + "-", r = i, !1) : void 0
    }),
    o = h + "transform",
    S[s = h + "transition-property"] = S[a = h + "transition-duration"] = S[d = h + "transition-delay"] = S[l = h + "transition-timing-function"] = S[p = h + "animation-name"] = S[u = h + "animation-duration"] = S[f = h + "animation-delay"] = S[c = h + "animation-timing-function"] = "",
    e.fx = {
        off: r === t && m.style.transitionProperty === t,
        speeds: {
            _default: 400,
            fast: 200,
            slow: 600
        },
        cssPrefix: h,
        transitionEnd: n("TransitionEnd"),
        animationEnd: n("AnimationEnd")
    },
    e.fn.zeptoAnimate = function(i, n, r, o, s) {
        return e.isFunction(n) && (o = n, r = t, n = t),
        e.isFunction(r) && (o = r, r = t),
        e.isPlainObject(n) && (r = n.easing, o = n.complete, s = n.delay, n = n.duration),
        n && (n = ("number" == typeof n ? n: e.fx.speeds[n] || e.fx.speeds._default) / 1e3),
        s && (s = parseFloat(s) / 1e3),
        this.anim(i, n, r, o, s)
    },
    e.fn.anim = function(n, r, h, g, w) {
        var m, T, y, x = {},
        b = "",
        C = this,
        L = e.fx.transitionEnd,
        E = !1;
        if (r === t && (r = e.fx.speeds._default / 1e3), w === t && (w = 0), e.fx.off && (r = 0), "string" == typeof n) x[p] = n,
        x[u] = r + "s",
        x[f] = w + "s",
        x[c] = h || "linear",
        L = e.fx.animationEnd;
        else {
            T = [];
            for (m in n) v.test(m) ? b += m + "(" + n[m] + ") ": (x[m] = n[m], T.push(i(m)));
            b && (x[o] = b, T.push(o)),
            r > 0 && "object" == typeof n && (x[s] = T.join(", "), x[a] = r + "s", x[d] = w + "s", x[l] = h || "linear")
        }
        return y = function(t) {
            if ("undefined" != typeof t) {
                if (t.target !== t.currentTarget) return;
                e(t.target).unbind(L, y)
            } else e(this).unbind(L, y);
            E = !0,
            e(this).css(S),
            g && g.call(this)
        },
        r > 0 && (this.bind(L, y), setTimeout(function() {
            E || y.call(C)
        },
        1e3 * r + 25)),
        this.size() && this.get(0).clientLeft,
        this.css(x),
        0 >= r && setTimeout(function() {
            C.each(function() {
                y.call(this)
            })
        },
        0),
        this
    },
    m = null
} (window.Zepto || window.jQuery);