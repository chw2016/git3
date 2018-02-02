/*!
 * jQuery JavaScript Library v1.9.1
 * http://jquery.com/
 *
 * Includes Sizzle.js
 * http://sizzlejs.com/
 *
 * Copyright 2005, 2012 jQuery Foundation, Inc. and other contributors
 * Released under the MIT license
 * http://jquery.org/license
 *
 * Date: 2013-2-4
 */
function msgLoad(t, e, i, n) {
    var r = $(t),
    o = r.height(),
    s = r.find("li").outerHeight(),
    a = o / s,
    l = n ? n: o / s,
    h = r.find("ul"),
    c = (h.css("top"), l * s);
    "top" == i ? (h.prepend(e), h.css("top", -c).animate({
        top: 0
    },
    500,
    function() {
        h.find("li:gt(" + (a - 1) + ")").remove()
    })) : "start" == i ? (h.prepend(e).css("top", -c), h.animate({
        top: 0
    },
    500,
    function() {
        h.find("li:gt(" + (a - 1) + ")").remove()
    })) : "end" == i ? (h.append(e), h.animate({
        top: -c
    },
    500,
    function() {
        h.find("li:lt(" + l + ")").remove(),
        h.css("top", 0)
    })) : (h.append(e), h.animate({
        top: -c
    },
    500,
    function() {
        h.find("li:lt(" + l + ")").remove(),
        h.css("top", 0)
    }))
}
function showVoteChart(t, e, i) {
    $("#container").highcharts({
        credits: !1,
        chart: {
            type: "column",
            margin: [10],
            backgroundColor: "transparent"
        },
        title: {
            text: ""
        },
        xAxis: {
            type: "category",
            categories: t,
            lineWidth: "2",
            labels: {
                align: "center",
                y: 20,
                style: {
                    fontSize: "18px",
                    fontFamily: "microsoft yahei, sans-serif",
                    color: "#" + i
                }
            }
        },
        yAxis: {
            min: 0,
            minorGridLineWidth: 0,
            gridLineWidth: 0,
            title: {
                text: ""
            },
            labels: {
                enabled: !1
            }
        },
        legend: {
            enabled: !1
        },
        plotOptions: {
            bar: {
                borderColor: "#fff",
                borderWidth: 2
            },
            column: {
                pointPadding: .2,
                borderColor: "#fff",
                borderWidth: 2,
                pointWidth: 30
            }
        },
        tooltip: {
            enabled: !1,
            pointFormat: "<b>{point.y:.1f}%</b>"
        },
        series: [{
            name: "Population",
            data: e,
            dataLabels: {
                enabled: !0,
                rotation: 0,
                color: "#" + i,
                align: "center",
                text: "%",
                x: 0,
                y: -25,
                style: {
                    fontSize: "18px",
                    fontFamily: "Microsoft yahei, sans-serif"
                },
                formatter: function() {
                    return this.y + "\u7968"
                }
            }
        }]
    })
} !
function(t, e) {
    function i(t) {
        var e = t.length,
        i = le.type(t);
        return le.isWindow(t) ? !1 : 1 === t.nodeType && e ? !0 : "array" === i || "function" !== i && (0 === e || "number" == typeof e && e > 0 && e - 1 in t)
    }
    function n(t) {
        var e = Te[t] = {};
        return le.each(t.match(ce) || [],
        function(t, i) {
            e[i] = !0
        }),
        e
    }
    function r(t, i, n, r) {
        if (le.acceptData(t)) {
            var o, s, a = le.expando,
            l = "string" == typeof i,
            h = t.nodeType,
            c = h ? le.cache: t,
            u = h ? t[a] : t[a] && a;
            if (u && c[u] && (r || c[u].data) || !l || n !== e) return u || (h ? t[a] = u = Q.pop() || le.guid++:u = a),
            c[u] || (c[u] = {},
            h || (c[u].toJSON = le.noop)),
            ("object" == typeof i || "function" == typeof i) && (r ? c[u] = le.extend(c[u], i) : c[u].data = le.extend(c[u].data, i)),
            o = c[u],
            r || (o.data || (o.data = {}), o = o.data),
            n !== e && (o[le.camelCase(i)] = n),
            l ? (s = o[i], null == s && (s = o[le.camelCase(i)])) : s = o,
            s
        }
    }
    function o(t, e, i) {
        if (le.acceptData(t)) {
            var n, r, o, s = t.nodeType,
            l = s ? le.cache: t,
            h = s ? t[le.expando] : le.expando;
            if (l[h]) {
                if (e && (o = i ? l[h] : l[h].data)) {
                    le.isArray(e) ? e = e.concat(le.map(e, le.camelCase)) : e in o ? e = [e] : (e = le.camelCase(e), e = e in o ? [e] : e.split(" "));
                    for (n = 0, r = e.length; r > n; n++) delete o[e[n]];
                    if (! (i ? a: le.isEmptyObject)(o)) return
                } (i || (delete l[h].data, a(l[h]))) && (s ? le.cleanData([t], !0) : le.support.deleteExpando || l != l.window ? delete l[h] : l[h] = null)
            }
        }
    }
    function s(t, i, n) {
        if (n === e && 1 === t.nodeType) {
            var r = "data-" + i.replace(Ce, "-$1").toLowerCase();
            if (n = t.getAttribute(r), "string" == typeof n) {
                try {
                    n = "true" === n ? !0 : "false" === n ? !1 : "null" === n ? null: +n + "" === n ? +n: Se.test(n) ? le.parseJSON(n) : n
                } catch(o) {}
                le.data(t, i, n)
            } else n = e
        }
        return n
    }
    function a(t) {
        var e;
        for (e in t) if (("data" !== e || !le.isEmptyObject(t[e])) && "toJSON" !== e) return ! 1;
        return ! 0
    }
    function l() {
        return ! 0
    }
    function h() {
        return ! 1
    }
    function c(t, e) {
        do t = t[e];
        while (t && 1 !== t.nodeType);
        return t
    }
    function u(t, e, i) {
        if (e = e || 0, le.isFunction(e)) return le.grep(t,
        function(t, n) {
            var r = !!e.call(t, n, t);
            return r === i
        });
        if (e.nodeType) return le.grep(t,
        function(t) {
            return t === e === i
        });
        if ("string" == typeof e) {
            var n = le.grep(t,
            function(t) {
                return 1 === t.nodeType
            });
            if (Xe.test(e)) return le.filter(e, n, !i);
            e = le.filter(e, n)
        }
        return le.grep(t,
        function(t) {
            return le.inArray(t, e) >= 0 === i
        })
    }
    function d(t) {
        var e = qe.split("|"),
        i = t.createDocumentFragment();
        if (i.createElement) for (; e.length;) i.createElement(e.pop());
        return i
    }
    function p(t, e) {
        return t.getElementsByTagName(e)[0] || t.appendChild(t.ownerDocument.createElement(e))
    }
    function f(t) {
        var e = t.getAttributeNode("type");
        return t.type = (e && e.specified) + "/" + t.type,
        t
    }
    function g(t) {
        var e = ri.exec(t.type);
        return e ? t.type = e[1] : t.removeAttribute("type"),
        t
    }
    function m(t, e) {
        for (var i, n = 0; null != (i = t[n]); n++) le._data(i, "globalEval", !e || le._data(e[n], "globalEval"))
    }
    function y(t, e) {
        if (1 === e.nodeType && le.hasData(t)) {
            var i, n, r, o = le._data(t),
            s = le._data(e, o),
            a = o.events;
            if (a) {
                delete s.handle,
                s.events = {};
                for (i in a) for (n = 0, r = a[i].length; r > n; n++) le.event.add(e, i, a[i][n])
            }
            s.data && (s.data = le.extend({},
            s.data))
        }
    }
    function v(t, e) {
        var i, n, r;
        if (1 === e.nodeType) {
            if (i = e.nodeName.toLowerCase(), !le.support.noCloneEvent && e[le.expando]) {
                r = le._data(e);
                for (n in r.events) le.removeEvent(e, n, r.handle);
                e.removeAttribute(le.expando)
            }
            "script" === i && e.text !== t.text ? (f(e).text = t.text, g(e)) : "object" === i ? (e.parentNode && (e.outerHTML = t.outerHTML), le.support.html5Clone && t.innerHTML && !le.trim(e.innerHTML) && (e.innerHTML = t.innerHTML)) : "input" === i && ei.test(t.type) ? (e.defaultChecked = e.checked = t.checked, e.value !== t.value && (e.value = t.value)) : "option" === i ? e.defaultSelected = e.selected = t.defaultSelected: ("input" === i || "textarea" === i) && (e.defaultValue = t.defaultValue)
        }
    }
    function x(t, i) {
        var n, r, o = 0,
        s = typeof t.getElementsByTagName !== V ? t.getElementsByTagName(i || "*") : typeof t.querySelectorAll !== V ? t.querySelectorAll(i || "*") : e;
        if (!s) for (s = [], n = t.childNodes || t; null != (r = n[o]); o++) ! i || le.nodeName(r, i) ? s.push(r) : le.merge(s, x(r, i));
        return i === e || i && le.nodeName(t, i) ? le.merge([t], s) : s
    }
    function b(t) {
        ei.test(t.type) && (t.defaultChecked = t.checked)
    }
    function k(t, e) {
        if (e in t) return e;
        for (var i = e.charAt(0).toUpperCase() + e.slice(1), n = e, r = Si.length; r--;) if (e = Si[r] + i, e in t) return e;
        return n
    }
    function w(t, e) {
        return t = e || t,
        "none" === le.css(t, "display") || !le.contains(t.ownerDocument, t)
    }
    function T(t, e) {
        for (var i, n, r, o = [], s = 0, a = t.length; a > s; s++) n = t[s],
        n.style && (o[s] = le._data(n, "olddisplay"), i = n.style.display, e ? (o[s] || "none" !== i || (n.style.display = ""), "" === n.style.display && w(n) && (o[s] = le._data(n, "olddisplay", L(n.nodeName)))) : o[s] || (r = w(n), (i && "none" !== i || !r) && le._data(n, "olddisplay", r ? i: le.css(n, "display"))));
        for (s = 0; a > s; s++) n = t[s],
        n.style && (e && "none" !== n.style.display && "" !== n.style.display || (n.style.display = e ? o[s] || "": "none"));
        return t
    }
    function S(t, e, i) {
        var n = yi.exec(e);
        return n ? Math.max(0, n[1] - (i || 0)) + (n[2] || "px") : e
    }
    function C(t, e, i, n, r) {
        for (var o = i === (n ? "border": "content") ? 4 : "width" === e ? 1 : 0, s = 0; 4 > o; o += 2)"margin" === i && (s += le.css(t, i + Ti[o], !0, r)),
        n ? ("content" === i && (s -= le.css(t, "padding" + Ti[o], !0, r)), "margin" !== i && (s -= le.css(t, "border" + Ti[o] + "Width", !0, r))) : (s += le.css(t, "padding" + Ti[o], !0, r), "padding" !== i && (s += le.css(t, "border" + Ti[o] + "Width", !0, r)));
        return s
    }
    function A(t, e, i) {
        var n = !0,
        r = "width" === e ? t.offsetWidth: t.offsetHeight,
        o = ci(t),
        s = le.support.boxSizing && "border-box" === le.css(t, "boxSizing", !1, o);
        if (0 >= r || null == r) {
            if (r = ui(t, e, o), (0 > r || null == r) && (r = t.style[e]), vi.test(r)) return r;
            n = s && (le.support.boxSizingReliable || r === t.style[e]),
            r = parseFloat(r) || 0
        }
        return r + C(t, e, i || (s ? "border": "content"), n, o) + "px"
    }
    function L(t) {
        var e = $,
        i = bi[t];
        return i || (i = M(t, e), "none" !== i && i || (hi = (hi || le("<iframe frameborder='0' width='0' height='0'/>").css("cssText", "display:block !important")).appendTo(e.documentElement), e = (hi[0].contentWindow || hi[0].contentDocument).document, e.write("<!doctype html><html><body>"), e.close(), i = M(t, e), hi.detach()), bi[t] = i),
        i
    }
    function M(t, e) {
        var i = le(e.createElement(t)).appendTo(e.body),
        n = le.css(i[0], "display");
        return i.remove(),
        n
    }
    function P(t, e, i, n) {
        var r;
        if (le.isArray(e)) le.each(e,
        function(e, r) {
            i || Ai.test(t) ? n(t, r) : P(t + "[" + ("object" == typeof r ? e: "") + "]", r, i, n)
        });
        else if (i || "object" !== le.type(e)) n(t, e);
        else for (r in e) P(t + "[" + r + "]", e[r], i, n)
    }
    function D(t) {
        return function(e, i) {
            "string" != typeof e && (i = e, e = "*");
            var n, r = 0,
            o = e.toLowerCase().match(ce) || [];
            if (le.isFunction(i)) for (; n = o[r++];)"+" === n[0] ? (n = n.slice(1) || "*", (t[n] = t[n] || []).unshift(i)) : (t[n] = t[n] || []).push(i)
        }
    }
    function N(t, e, i, n) {
        function r(a) {
            var l;
            return o[a] = !0,
            le.each(t[a] || [],
            function(t, a) {
                var h = a(e, i, n);
                return "string" != typeof h || s || o[h] ? s ? !(l = h) : void 0 : (e.dataTypes.unshift(h), r(h), !1)
            }),
            l
        }
        var o = {},
        s = t === Xi;
        return r(e.dataTypes[0]) || !o["*"] && r("*")
    }
    function E(t, i) {
        var n, r, o = le.ajaxSettings.flatOptions || {};
        for (r in i) i[r] !== e && ((o[r] ? t: n || (n = {}))[r] = i[r]);
        return n && le.extend(!0, t, n),
        t
    }
    function H(t, i, n) {
        var r, o, s, a, l = t.contents,
        h = t.dataTypes,
        c = t.responseFields;
        for (a in c) a in n && (i[c[a]] = n[a]);
        for (;
        "*" === h[0];) h.shift(),
        o === e && (o = t.mimeType || i.getResponseHeader("Content-Type"));
        if (o) for (a in l) if (l[a] && l[a].test(o)) {
            h.unshift(a);
            break
        }
        if (h[0] in n) s = h[0];
        else {
            for (a in n) {
                if (!h[0] || t.converters[a + " " + h[0]]) {
                    s = a;
                    break
                }
                r || (r = a)
            }
            s = s || r
        }
        return s ? (s !== h[0] && h.unshift(s), n[s]) : void 0
    }
    function I(t, e) {
        var i, n, r, o, s = {},
        a = 0,
        l = t.dataTypes.slice(),
        h = l[0];
        if (t.dataFilter && (e = t.dataFilter(e, t.dataType)), l[1]) for (r in t.converters) s[r.toLowerCase()] = t.converters[r];
        for (; n = l[++a];) if ("*" !== n) {
            if ("*" !== h && h !== n) {
                if (r = s[h + " " + n] || s["* " + n], !r) for (i in s) if (o = i.split(" "), o[1] === n && (r = s[h + " " + o[0]] || s["* " + o[0]])) {
                    r === !0 ? r = s[i] : s[i] !== !0 && (n = o[0], l.splice(a--, 0, n));
                    break
                }
                if (r !== !0) if (r && t["throws"]) e = r(e);
                else try {
                    e = r(e)
                } catch(c) {
                    return {
                        state: "parsererror",
                        error: r ? c: "No conversion from " + h + " to " + n
                    }
                }
            }
            h = n
        }
        return {
            state: "success",
            data: e
        }
    }
    function B() {
        try {
            return new t.XMLHttpRequest
        } catch(e) {}
    }
    function O() {
        try {
            return new t.ActiveXObject("Microsoft.XMLHTTP")
        } catch(e) {}
    }
    function z() {
        return setTimeout(function() {
            Ji = e
        }),
        Ji = le.now()
    }
    function R(t, e) {
        le.each(e,
        function(e, i) {
            for (var n = (on[e] || []).concat(on["*"]), r = 0, o = n.length; o > r; r++) if (n[r].call(t, e, i)) return
        })
    }
    function j(t, e, i) {
        var n, r, o = 0,
        s = rn.length,
        a = le.Deferred().always(function() {
            delete l.elem
        }),
        l = function() {
            if (r) return ! 1;
            for (var e = Ji || z(), i = Math.max(0, h.startTime + h.duration - e), n = i / h.duration || 0, o = 1 - n, s = 0, l = h.tweens.length; l > s; s++) h.tweens[s].run(o);
            return a.notifyWith(t, [h, o, i]),
            1 > o && l ? i: (a.resolveWith(t, [h]), !1)
        },
        h = a.promise({
            elem: t,
            props: le.extend({},
            e),
            opts: le.extend(!0, {
                specialEasing: {}
            },
            i),
            originalProperties: e,
            originalOptions: i,
            startTime: Ji || z(),
            duration: i.duration,
            tweens: [],
            createTween: function(e, i) {
                var n = le.Tween(t, h.opts, e, i, h.opts.specialEasing[e] || h.opts.easing);
                return h.tweens.push(n),
                n
            },
            stop: function(e) {
                var i = 0,
                n = e ? h.tweens.length: 0;
                if (r) return this;
                for (r = !0; n > i; i++) h.tweens[i].run(1);
                return e ? a.resolveWith(t, [h, e]) : a.rejectWith(t, [h, e]),
                this
            }
        }),
        c = h.props;
        for (W(c, h.opts.specialEasing); s > o; o++) if (n = rn[o].call(h, t, c, h.opts)) return n;
        return R(h, c),
        le.isFunction(h.opts.start) && h.opts.start.call(t, h),
        le.fx.timer(le.extend(l, {
            elem: t,
            anim: h,
            queue: h.opts.queue
        })),
        h.progress(h.opts.progress).done(h.opts.done, h.opts.complete).fail(h.opts.fail).always(h.opts.always)
    }
    function W(t, e) {
        var i, n, r, o, s;
        for (r in t) if (n = le.camelCase(r), o = e[n], i = t[r], le.isArray(i) && (o = i[1], i = t[r] = i[0]), r !== n && (t[n] = i, delete t[r]), s = le.cssHooks[n], s && "expand" in s) {
            i = s.expand(i),
            delete t[n];
            for (r in i) r in t || (t[r] = i[r], e[r] = o)
        } else e[n] = o
    }
    function F(t, e, i) {
        var n, r, o, s, a, l, h, c, u, d = this,
        p = t.style,
        f = {},
        g = [],
        m = t.nodeType && w(t);
        i.queue || (c = le._queueHooks(t, "fx"), null == c.unqueued && (c.unqueued = 0, u = c.empty.fire, c.empty.fire = function() {
            c.unqueued || u()
        }), c.unqueued++, d.always(function() {
            d.always(function() {
                c.unqueued--,
                le.queue(t, "fx").length || c.empty.fire()
            })
        })),
        1 === t.nodeType && ("height" in e || "width" in e) && (i.overflow = [p.overflow, p.overflowX, p.overflowY], "inline" === le.css(t, "display") && "none" === le.css(t, "float") && (le.support.inlineBlockNeedsLayout && "inline" !== L(t.nodeName) ? p.zoom = 1 : p.display = "inline-block")),
        i.overflow && (p.overflow = "hidden", le.support.shrinkWrapBlocks || d.always(function() {
            p.overflow = i.overflow[0],
            p.overflowX = i.overflow[1],
            p.overflowY = i.overflow[2]
        }));
        for (r in e) if (s = e[r], tn.exec(s)) {
            if (delete e[r], l = l || "toggle" === s, s === (m ? "hide": "show")) continue;
            g.push(r)
        }
        if (o = g.length) {
            a = le._data(t, "fxshow") || le._data(t, "fxshow", {}),
            "hidden" in a && (m = a.hidden),
            l && (a.hidden = !m),
            m ? le(t).show() : d.done(function() {
                le(t).hide()
            }),
            d.done(function() {
                var e;
                le._removeData(t, "fxshow");
                for (e in f) le.style(t, e, f[e])
            });
            for (r = 0; o > r; r++) n = g[r],
            h = d.createTween(n, m ? a[n] : 0),
            f[n] = a[n] || le.style(t, n),
            n in a || (a[n] = h.start, m && (h.end = h.start, h.start = "width" === n || "height" === n ? 1 : 0))
        }
    }
    function _(t, e, i, n, r) {
        return new _.prototype.init(t, e, i, n, r)
    }
    function X(t, e) {
        var i, n = {
            height: t
        },
        r = 0;
        for (e = e ? 1 : 0; 4 > r; r += 2 - e) i = Ti[r],
        n["margin" + i] = n["padding" + i] = t;
        return e && (n.opacity = n.width = t),
        n
    }
    function Y(t) {
        return le.isWindow(t) ? t: 9 === t.nodeType ? t.defaultView || t.parentWindow: !1
    }
    var G, q, V = typeof e,
    $ = t.document,
    U = t.location,
    Z = t.jQuery,
    K = t.$,
    J = {},
    Q = [],
    te = "1.9.1",
    ee = Q.concat,
    ie = Q.push,
    ne = Q.slice,
    re = Q.indexOf,
    oe = J.toString,
    se = J.hasOwnProperty,
    ae = te.trim,
    le = function(t, e) {
        return new le.fn.init(t, e, q)
    },
    he = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,
    ce = /\S+/g,
    ue = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,
    de = /^(?:(<[\w\W]+>)[^>]*|#([\w-]*))$/,
    pe = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
    fe = /^[\],:{}\s]*$/,
    ge = /(?:^|:|,)(?:\s*\[)+/g,
    me = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g,
    ye = /"[^"\\\r\n]*"|true|false|null|-?(?:\d+\.|)\d+(?:[eE][+-]?\d+|)/g,
    ve = /^-ms-/,
    xe = /-([\da-z])/gi,
    be = function(t, e) {
        return e.toUpperCase()
    },
    ke = function(t) { ($.addEventListener || "load" === t.type || "complete" === $.readyState) && (we(), le.ready())
    },
    we = function() {
        $.addEventListener ? ($.removeEventListener("DOMContentLoaded", ke, !1), t.removeEventListener("load", ke, !1)) : ($.detachEvent("onreadystatechange", ke), t.detachEvent("onload", ke))
    };
    le.fn = le.prototype = {
        jquery: te,
        constructor: le,
        init: function(t, i, n) {
            var r, o;
            if (!t) return this;
            if ("string" == typeof t) {
                if (r = "<" === t.charAt(0) && ">" === t.charAt(t.length - 1) && t.length >= 3 ? [null, t, null] : de.exec(t), !r || !r[1] && i) return ! i || i.jquery ? (i || n).find(t) : this.constructor(i).find(t);
                if (r[1]) {
                    if (i = i instanceof le ? i[0] : i, le.merge(this, le.parseHTML(r[1], i && i.nodeType ? i.ownerDocument || i: $, !0)), pe.test(r[1]) && le.isPlainObject(i)) for (r in i) le.isFunction(this[r]) ? this[r](i[r]) : this.attr(r, i[r]);
                    return this
                }
                if (o = $.getElementById(r[2]), o && o.parentNode) {
                    if (o.id !== r[2]) return n.find(t);
                    this.length = 1,
                    this[0] = o
                }
                return this.context = $,
                this.selector = t,
                this
            }
            return t.nodeType ? (this.context = this[0] = t, this.length = 1, this) : le.isFunction(t) ? n.ready(t) : (t.selector !== e && (this.selector = t.selector, this.context = t.context), le.makeArray(t, this))
        },
        selector: "",
        length: 0,
        size: function() {
            return this.length
        },
        toArray: function() {
            return ne.call(this)
        },
        get: function(t) {
            return null == t ? this.toArray() : 0 > t ? this[this.length + t] : this[t]
        },
        pushStack: function(t) {
            var e = le.merge(this.constructor(), t);
            return e.prevObject = this,
            e.context = this.context,
            e
        },
        each: function(t, e) {
            return le.each(this, t, e)
        },
        ready: function(t) {
            return le.ready.promise().done(t),
            this
        },
        slice: function() {
            return this.pushStack(ne.apply(this, arguments))
        },
        first: function() {
            return this.eq(0)
        },
        last: function() {
            return this.eq( - 1)
        },
        eq: function(t) {
            var e = this.length,
            i = +t + (0 > t ? e: 0);
            return this.pushStack(i >= 0 && e > i ? [this[i]] : [])
        },
        map: function(t) {
            return this.pushStack(le.map(this,
            function(e, i) {
                return t.call(e, i, e)
            }))
        },
        end: function() {
            return this.prevObject || this.constructor(null)
        },
        push: ie,
        sort: [].sort,
        splice: [].splice
    },
    le.fn.init.prototype = le.fn,
    le.extend = le.fn.extend = function() {
        var t, i, n, r, o, s, a = arguments[0] || {},
        l = 1,
        h = arguments.length,
        c = !1;
        for ("boolean" == typeof a && (c = a, a = arguments[1] || {},
        l = 2), "object" == typeof a || le.isFunction(a) || (a = {}), h === l && (a = this, --l); h > l; l++) if (null != (o = arguments[l])) for (r in o) t = a[r],
        n = o[r],
        a !== n && (c && n && (le.isPlainObject(n) || (i = le.isArray(n))) ? (i ? (i = !1, s = t && le.isArray(t) ? t: []) : s = t && le.isPlainObject(t) ? t: {},
        a[r] = le.extend(c, s, n)) : n !== e && (a[r] = n));
        return a
    },
    le.extend({
        noConflict: function(e) {
            return t.$ === le && (t.$ = K),
            e && t.jQuery === le && (t.jQuery = Z),
            le
        },
        isReady: !1,
        readyWait: 1,
        holdReady: function(t) {
            t ? le.readyWait++:le.ready(!0)
        },
        ready: function(t) {
            if (t === !0 ? !--le.readyWait: !le.isReady) {
                if (!$.body) return setTimeout(le.ready);
                le.isReady = !0,
                t !== !0 && --le.readyWait > 0 || (G.resolveWith($, [le]), le.fn.trigger && le($).trigger("ready").off("ready"))
            }
        },
        isFunction: function(t) {
            return "function" === le.type(t)
        },
        isArray: Array.isArray ||
        function(t) {
            return "array" === le.type(t)
        },
        isWindow: function(t) {
            return null != t && t == t.window
        },
        isNumeric: function(t) {
            return ! isNaN(parseFloat(t)) && isFinite(t)
        },
        type: function(t) {
            return null == t ? String(t) : "object" == typeof t || "function" == typeof t ? J[oe.call(t)] || "object": typeof t
        },
        isPlainObject: function(t) {
            if (!t || "object" !== le.type(t) || t.nodeType || le.isWindow(t)) return ! 1;
            try {
                if (t.constructor && !se.call(t, "constructor") && !se.call(t.constructor.prototype, "isPrototypeOf")) return ! 1
            } catch(i) {
                return ! 1
            }
            var n;
            for (n in t);
            return n === e || se.call(t, n)
        },
        isEmptyObject: function(t) {
            var e;
            for (e in t) return ! 1;
            return ! 0
        },
        error: function(t) {
            throw new Error(t)
        },
        parseHTML: function(t, e, i) {
            if (!t || "string" != typeof t) return null;
            "boolean" == typeof e && (i = e, e = !1),
            e = e || $;
            var n = pe.exec(t),
            r = !i && [];
            return n ? [e.createElement(n[1])] : (n = le.buildFragment([t], e, r), r && le(r).remove(), le.merge([], n.childNodes))
        },
        parseJSON: function(e) {
            return t.JSON && t.JSON.parse ? t.JSON.parse(e) : null === e ? e: "string" == typeof e && (e = le.trim(e), e && fe.test(e.replace(me, "@").replace(ye, "]").replace(ge, ""))) ? new Function("return " + e)() : void le.error("Invalid JSON: " + e)
        },
        parseXML: function(i) {
            var n, r;
            if (!i || "string" != typeof i) return null;
            try {
                t.DOMParser ? (r = new DOMParser, n = r.parseFromString(i, "text/xml")) : (n = new ActiveXObject("Microsoft.XMLDOM"), n.async = "false", n.loadXML(i))
            } catch(o) {
                n = e
            }
            return n && n.documentElement && !n.getElementsByTagName("parsererror").length || le.error("Invalid XML: " + i),
            n
        },
        noop: function() {},
        globalEval: function(e) {
            e && le.trim(e) && (t.execScript ||
            function(e) {
                t.eval.call(t, e)
            })(e)
        },
        camelCase: function(t) {
            return t.replace(ve, "ms-").replace(xe, be)
        },
        nodeName: function(t, e) {
            return t.nodeName && t.nodeName.toLowerCase() === e.toLowerCase()
        },
        each: function(t, e, n) {
            var r, o = 0,
            s = t.length,
            a = i(t);
            if (n) {
                if (a) for (; s > o && (r = e.apply(t[o], n), r !== !1); o++);
                else for (o in t) if (r = e.apply(t[o], n), r === !1) break
            } else if (a) for (; s > o && (r = e.call(t[o], o, t[o]), r !== !1); o++);
            else for (o in t) if (r = e.call(t[o], o, t[o]), r === !1) break;
            return t
        },
        trim: ae && !ae.call("\ufeff\xa0") ?
        function(t) {
            return null == t ? "": ae.call(t)
        }: function(t) {
            return null == t ? "": (t + "").replace(ue, "")
        },
        makeArray: function(t, e) {
            var n = e || [];
            return null != t && (i(Object(t)) ? le.merge(n, "string" == typeof t ? [t] : t) : ie.call(n, t)),
            n
        },
        inArray: function(t, e, i) {
            var n;
            if (e) {
                if (re) return re.call(e, t, i);
                for (n = e.length, i = i ? 0 > i ? Math.max(0, n + i) : i: 0; n > i; i++) if (i in e && e[i] === t) return i
            }
            return - 1
        },
        merge: function(t, i) {
            var n = i.length,
            r = t.length,
            o = 0;
            if ("number" == typeof n) for (; n > o; o++) t[r++] = i[o];
            else for (; i[o] !== e;) t[r++] = i[o++];
            return t.length = r,
            t
        },
        grep: function(t, e, i) {
            var n, r = [],
            o = 0,
            s = t.length;
            for (i = !!i; s > o; o++) n = !!e(t[o], o),
            i !== n && r.push(t[o]);
            return r
        },
        map: function(t, e, n) {
            var r, o = 0,
            s = t.length,
            a = i(t),
            l = [];
            if (a) for (; s > o; o++) r = e(t[o], o, n),
            null != r && (l[l.length] = r);
            else for (o in t) r = e(t[o], o, n),
            null != r && (l[l.length] = r);
            return ee.apply([], l)
        },
        guid: 1,
        proxy: function(t, i) {
            var n, r, o;
            return "string" == typeof i && (o = t[i], i = t, t = o),
            le.isFunction(t) ? (n = ne.call(arguments, 2), r = function() {
                return t.apply(i || this, n.concat(ne.call(arguments)))
            },
            r.guid = t.guid = t.guid || le.guid++, r) : e
        },
        access: function(t, i, n, r, o, s, a) {
            var l = 0,
            h = t.length,
            c = null == n;
            if ("object" === le.type(n)) {
                o = !0;
                for (l in n) le.access(t, i, l, n[l], !0, s, a)
            } else if (r !== e && (o = !0, le.isFunction(r) || (a = !0), c && (a ? (i.call(t, r), i = null) : (c = i, i = function(t, e, i) {
                return c.call(le(t), i)
            })), i)) for (; h > l; l++) i(t[l], n, a ? r: r.call(t[l], l, i(t[l], n)));
            return o ? t: c ? i.call(t) : h ? i(t[0], n) : s
        },
        now: function() {
            return (new Date).getTime()
        }
    }),
    le.ready.promise = function(e) {
        if (!G) if (G = le.Deferred(), "complete" === $.readyState) setTimeout(le.ready);
        else if ($.addEventListener) $.addEventListener("DOMContentLoaded", ke, !1),
        t.addEventListener("load", ke, !1);
        else {
            $.attachEvent("onreadystatechange", ke),
            t.attachEvent("onload", ke);
            var i = !1;
            try {
                i = null == t.frameElement && $.documentElement
            } catch(n) {}
            i && i.doScroll && !
            function r() {
                if (!le.isReady) {
                    try {
                        i.doScroll("left")
                    } catch(t) {
                        return setTimeout(r, 50)
                    }
                    we(),
                    le.ready()
                }
            } ()
        }
        return G.promise(e)
    },
    le.each("Boolean Number String Function Array Date RegExp Object Error".split(" "),
    function(t, e) {
        J["[object " + e + "]"] = e.toLowerCase()
    }),
    q = le($);
    var Te = {};
    le.Callbacks = function(t) {
        t = "string" == typeof t ? Te[t] || n(t) : le.extend({},
        t);
        var i, r, o, s, a, l, h = [],
        c = !t.once && [],
        u = function(e) {
            for (r = t.memory && e, o = !0, a = l || 0, l = 0, s = h.length, i = !0; h && s > a; a++) if (h[a].apply(e[0], e[1]) === !1 && t.stopOnFalse) {
                r = !1;
                break
            }
            i = !1,
            h && (c ? c.length && u(c.shift()) : r ? h = [] : d.disable())
        },
        d = {
            add: function() {
                if (h) {
                    var e = h.length; !
                    function n(e) {
                        le.each(e,
                        function(e, i) {
                            var r = le.type(i);
                            "function" === r ? t.unique && d.has(i) || h.push(i) : i && i.length && "string" !== r && n(i)
                        })
                    } (arguments),
                    i ? s = h.length: r && (l = e, u(r))
                }
                return this
            },
            remove: function() {
                return h && le.each(arguments,
                function(t, e) {
                    for (var n; (n = le.inArray(e, h, n)) > -1;) h.splice(n, 1),
                    i && (s >= n && s--, a >= n && a--)
                }),
                this
            },
            has: function(t) {
                return t ? le.inArray(t, h) > -1 : !(!h || !h.length)
            },
            empty: function() {
                return h = [],
                this
            },
            disable: function() {
                return h = c = r = e,
                this
            },
            disabled: function() {
                return ! h
            },
            lock: function() {
                return c = e,
                r || d.disable(),
                this
            },
            locked: function() {
                return ! c
            },
            fireWith: function(t, e) {
                return e = e || [],
                e = [t, e.slice ? e.slice() : e],
                !h || o && !c || (i ? c.push(e) : u(e)),
                this
            },
            fire: function() {
                return d.fireWith(this, arguments),
                this
            },
            fired: function() {
                return !! o
            }
        };
        return d
    },
    le.extend({
        Deferred: function(t) {
            var e = [["resolve", "done", le.Callbacks("once memory"), "resolved"], ["reject", "fail", le.Callbacks("once memory"), "rejected"], ["notify", "progress", le.Callbacks("memory")]],
            i = "pending",
            n = {
                state: function() {
                    return i
                },
                always: function() {
                    return r.done(arguments).fail(arguments),
                    this
                },
                then: function() {
                    var t = arguments;
                    return le.Deferred(function(i) {
                        le.each(e,
                        function(e, o) {
                            var s = o[0],
                            a = le.isFunction(t[e]) && t[e];
                            r[o[1]](function() {
                                var t = a && a.apply(this, arguments);
                                t && le.isFunction(t.promise) ? t.promise().done(i.resolve).fail(i.reject).progress(i.notify) : i[s + "With"](this === n ? i.promise() : this, a ? [t] : arguments)
                            })
                        }),
                        t = null
                    }).promise()
                },
                promise: function(t) {
                    return null != t ? le.extend(t, n) : n
                }
            },
            r = {};
            return n.pipe = n.then,
            le.each(e,
            function(t, o) {
                var s = o[2],
                a = o[3];
                n[o[1]] = s.add,
                a && s.add(function() {
                    i = a
                },
                e[1 ^ t][2].disable, e[2][2].lock),
                r[o[0]] = function() {
                    return r[o[0] + "With"](this === r ? n: this, arguments),
                    this
                },
                r[o[0] + "With"] = s.fireWith
            }),
            n.promise(r),
            t && t.call(r, r),
            r
        },
        when: function(t) {
            var e, i, n, r = 0,
            o = ne.call(arguments),
            s = o.length,
            a = 1 !== s || t && le.isFunction(t.promise) ? s: 0,
            l = 1 === a ? t: le.Deferred(),
            h = function(t, i, n) {
                return function(r) {
                    i[t] = this,
                    n[t] = arguments.length > 1 ? ne.call(arguments) : r,
                    n === e ? l.notifyWith(i, n) : --a || l.resolveWith(i, n)
                }
            };
            if (s > 1) for (e = new Array(s), i = new Array(s), n = new Array(s); s > r; r++) o[r] && le.isFunction(o[r].promise) ? o[r].promise().done(h(r, n, o)).fail(l.reject).progress(h(r, i, e)) : --a;
            return a || l.resolveWith(n, o),
            l.promise()
        }
    }),
    le.support = function() {
        var e, i, n, r, o, s, a, l, h, c, u = $.createElement("div");
        if (u.setAttribute("className", "t"), u.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", i = u.getElementsByTagName("*"), n = u.getElementsByTagName("a")[0], !i || !n || !i.length) return {};
        o = $.createElement("select"),
        a = o.appendChild($.createElement("option")),
        r = u.getElementsByTagName("input")[0],
        n.style.cssText = "top:1px;float:left;opacity:.5",
        e = {
            getSetAttribute: "t" !== u.className,
            leadingWhitespace: 3 === u.firstChild.nodeType,
            tbody: !u.getElementsByTagName("tbody").length,
            htmlSerialize: !!u.getElementsByTagName("link").length,
            style: /top/.test(n.getAttribute("style")),
            hrefNormalized: "/a" === n.getAttribute("href"),
            opacity: /^0.5/.test(n.style.opacity),
            cssFloat: !!n.style.cssFloat,
            checkOn: !!r.value,
            optSelected: a.selected,
            enctype: !!$.createElement("form").enctype,
            html5Clone: "<:nav></:nav>" !== $.createElement("nav").cloneNode(!0).outerHTML,
            boxModel: "CSS1Compat" === $.compatMode,
            deleteExpando: !0,
            noCloneEvent: !0,
            inlineBlockNeedsLayout: !1,
            shrinkWrapBlocks: !1,
            reliableMarginRight: !0,
            boxSizingReliable: !0,
            pixelPosition: !1
        },
        r.checked = !0,
        e.noCloneChecked = r.cloneNode(!0).checked,
        o.disabled = !0,
        e.optDisabled = !a.disabled;
        try {
            delete u.test
        } catch(d) {
            e.deleteExpando = !1
        }
        r = $.createElement("input"),
        r.setAttribute("value", ""),
        e.input = "" === r.getAttribute("value"),
        r.value = "t",
        r.setAttribute("type", "radio"),
        e.radioValue = "t" === r.value,
        r.setAttribute("checked", "t"),
        r.setAttribute("name", "t"),
        s = $.createDocumentFragment(),
        s.appendChild(r),
        e.appendChecked = r.checked,
        e.checkClone = s.cloneNode(!0).cloneNode(!0).lastChild.checked,
        u.attachEvent && (u.attachEvent("onclick",
        function() {
            e.noCloneEvent = !1
        }), u.cloneNode(!0).click());
        for (c in {
            submit: !0,
            change: !0,
            focusin: !0
        }) u.setAttribute(l = "on" + c, "t"),
        e[c + "Bubbles"] = l in t || u.attributes[l].expando === !1;
        return u.style.backgroundClip = "content-box",
        u.cloneNode(!0).style.backgroundClip = "",
        e.clearCloneStyle = "content-box" === u.style.backgroundClip,
        le(function() {
            var i, n, r, o = "padding:0;margin:0;border:0;display:block;box-sizing:content-box;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;",
            s = $.getElementsByTagName("body")[0];
            s && (i = $.createElement("div"), i.style.cssText = "border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px", s.appendChild(i).appendChild(u), u.innerHTML = "<table><tr><td></td><td>t</td></tr></table>", r = u.getElementsByTagName("td"), r[0].style.cssText = "padding:0;margin:0;border:0;display:none", h = 0 === r[0].offsetHeight, r[0].style.display = "", r[1].style.display = "none", e.reliableHiddenOffsets = h && 0 === r[0].offsetHeight, u.innerHTML = "", u.style.cssText = "box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%;", e.boxSizing = 4 === u.offsetWidth, e.doesNotIncludeMarginInBodyOffset = 1 !== s.offsetTop, t.getComputedStyle && (e.pixelPosition = "1%" !== (t.getComputedStyle(u, null) || {}).top, e.boxSizingReliable = "4px" === (t.getComputedStyle(u, null) || {
                width: "4px"
            }).width, n = u.appendChild($.createElement("div")), n.style.cssText = u.style.cssText = o, n.style.marginRight = n.style.width = "0", u.style.width = "1px", e.reliableMarginRight = !parseFloat((t.getComputedStyle(n, null) || {}).marginRight)), typeof u.style.zoom !== V && (u.innerHTML = "", u.style.cssText = o + "width:1px;padding:1px;display:inline;zoom:1", e.inlineBlockNeedsLayout = 3 === u.offsetWidth, u.style.display = "block", u.innerHTML = "<div></div>", u.firstChild.style.width = "5px", e.shrinkWrapBlocks = 3 !== u.offsetWidth, e.inlineBlockNeedsLayout && (s.style.zoom = 1)), s.removeChild(i), i = u = r = n = null)
        }),
        i = o = s = a = n = r = null,
        e
    } ();
    var Se = /(?:\{[\s\S]*\}|\[[\s\S]*\])$/,
    Ce = /([A-Z])/g;
    le.extend({
        cache: {},
        expando: "jQuery" + (te + Math.random()).replace(/\D/g, ""),
        noData: {
            embed: !0,
            object: "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000",
            applet: !0
        },
        hasData: function(t) {
            return t = t.nodeType ? le.cache[t[le.expando]] : t[le.expando],
            !!t && !a(t)
        },
        data: function(t, e, i) {
            return r(t, e, i)
        },
        removeData: function(t, e) {
            return o(t, e)
        },
        _data: function(t, e, i) {
            return r(t, e, i, !0)
        },
        _removeData: function(t, e) {
            return o(t, e, !0)
        },
        acceptData: function(t) {
            if (t.nodeType && 1 !== t.nodeType && 9 !== t.nodeType) return ! 1;
            var e = t.nodeName && le.noData[t.nodeName.toLowerCase()];
            return ! e || e !== !0 && t.getAttribute("classid") === e
        }
    }),
    le.fn.extend({
        data: function(t, i) {
            var n, r, o = this[0],
            a = 0,
            l = null;
            if (t === e) {
                if (this.length && (l = le.data(o), 1 === o.nodeType && !le._data(o, "parsedAttrs"))) {
                    for (n = o.attributes; a < n.length; a++) r = n[a].name,
                    r.indexOf("data-") || (r = le.camelCase(r.slice(5)), s(o, r, l[r]));
                    le._data(o, "parsedAttrs", !0)
                }
                return l
            }
            return "object" == typeof t ? this.each(function() {
                le.data(this, t)
            }) : le.access(this,
            function(i) {
                return i === e ? o ? s(o, t, le.data(o, t)) : null: void this.each(function() {
                    le.data(this, t, i)
                })
            },
            null, i, arguments.length > 1, null, !0)
        },
        removeData: function(t) {
            return this.each(function() {
                le.removeData(this, t)
            })
        }
    }),
    le.extend({
        queue: function(t, e, i) {
            var n;
            return t ? (e = (e || "fx") + "queue", n = le._data(t, e), i && (!n || le.isArray(i) ? n = le._data(t, e, le.makeArray(i)) : n.push(i)), n || []) : void 0
        },
        dequeue: function(t, e) {
            e = e || "fx";
            var i = le.queue(t, e),
            n = i.length,
            r = i.shift(),
            o = le._queueHooks(t, e),
            s = function() {
                le.dequeue(t, e)
            };
            "inprogress" === r && (r = i.shift(), n--),
            o.cur = r,
            r && ("fx" === e && i.unshift("inprogress"), delete o.stop, r.call(t, s, o)),
            !n && o && o.empty.fire()
        },
        _queueHooks: function(t, e) {
            var i = e + "queueHooks";
            return le._data(t, i) || le._data(t, i, {
                empty: le.Callbacks("once memory").add(function() {
                    le._removeData(t, e + "queue"),
                    le._removeData(t, i)
                })
            })
        }
    }),
    le.fn.extend({
        queue: function(t, i) {
            var n = 2;
            return "string" != typeof t && (i = t, t = "fx", n--),
            arguments.length < n ? le.queue(this[0], t) : i === e ? this: this.each(function() {
                var e = le.queue(this, t, i);
                le._queueHooks(this, t),
                "fx" === t && "inprogress" !== e[0] && le.dequeue(this, t)
            })
        },
        dequeue: function(t) {
            return this.each(function() {
                le.dequeue(this, t)
            })
        },
        delay: function(t, e) {
            return t = le.fx ? le.fx.speeds[t] || t: t,
            e = e || "fx",
            this.queue(e,
            function(e, i) {
                var n = setTimeout(e, t);
                i.stop = function() {
                    clearTimeout(n)
                }
            })
        },
        clearQueue: function(t) {
            return this.queue(t || "fx", [])
        },
        promise: function(t, i) {
            var n, r = 1,
            o = le.Deferred(),
            s = this,
            a = this.length,
            l = function() {--r || o.resolveWith(s, [s])
            };
            for ("string" != typeof t && (i = t, t = e), t = t || "fx"; a--;) n = le._data(s[a], t + "queueHooks"),
            n && n.empty && (r++, n.empty.add(l));
            return l(),
            o.promise(i)
        }
    });
    var Ae, Le, Me = /[\t\r\n]/g,
    Pe = /\r/g,
    De = /^(?:input|select|textarea|button|object)$/i,
    Ne = /^(?:a|area)$/i,
    Ee = /^(?:checked|selected|autofocus|autoplay|async|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped)$/i,
    He = /^(?:checked|selected)$/i,
    Ie = le.support.getSetAttribute,
    Be = le.support.input;
    le.fn.extend({
        attr: function(t, e) {
            return le.access(this, le.attr, t, e, arguments.length > 1)
        },
        removeAttr: function(t) {
            return this.each(function() {
                le.removeAttr(this, t)
            })
        },
        prop: function(t, e) {
            return le.access(this, le.prop, t, e, arguments.length > 1)
        },
        removeProp: function(t) {
            return t = le.propFix[t] || t,
            this.each(function() {
                try {
                    this[t] = e,
                    delete this[t]
                } catch(i) {}
            })
        },
        addClass: function(t) {
            var e, i, n, r, o, s = 0,
            a = this.length,
            l = "string" == typeof t && t;
            if (le.isFunction(t)) return this.each(function(e) {
                le(this).addClass(t.call(this, e, this.className))
            });
            if (l) for (e = (t || "").match(ce) || []; a > s; s++) if (i = this[s], n = 1 === i.nodeType && (i.className ? (" " + i.className + " ").replace(Me, " ") : " ")) {
                for (o = 0; r = e[o++];) n.indexOf(" " + r + " ") < 0 && (n += r + " ");
                i.className = le.trim(n)
            }
            return this
        },
        removeClass: function(t) {
            var e, i, n, r, o, s = 0,
            a = this.length,
            l = 0 === arguments.length || "string" == typeof t && t;
            if (le.isFunction(t)) return this.each(function(e) {
                le(this).removeClass(t.call(this, e, this.className))
            });
            if (l) for (e = (t || "").match(ce) || []; a > s; s++) if (i = this[s], n = 1 === i.nodeType && (i.className ? (" " + i.className + " ").replace(Me, " ") : "")) {
                for (o = 0; r = e[o++];) for (; n.indexOf(" " + r + " ") >= 0;) n = n.replace(" " + r + " ", " ");
                i.className = t ? le.trim(n) : ""
            }
            return this
        },
        toggleClass: function(t, e) {
            var i = typeof t,
            n = "boolean" == typeof e;
            return this.each(le.isFunction(t) ?
            function(i) {
                le(this).toggleClass(t.call(this, i, this.className, e), e)
            }: function() {
                if ("string" === i) for (var r, o = 0,
                s = le(this), a = e, l = t.match(ce) || []; r = l[o++];) a = n ? a: !s.hasClass(r),
                s[a ? "addClass": "removeClass"](r);
                else(i === V || "boolean" === i) && (this.className && le._data(this, "__className__", this.className), this.className = this.className || t === !1 ? "": le._data(this, "__className__") || "")
            })
        },
        hasClass: function(t) {
            for (var e = " " + t + " ",
            i = 0,
            n = this.length; n > i; i++) if (1 === this[i].nodeType && (" " + this[i].className + " ").replace(Me, " ").indexOf(e) >= 0) return ! 0;
            return ! 1
        },
        val: function(t) {
            var i, n, r, o = this[0]; {
                if (arguments.length) return r = le.isFunction(t),
                this.each(function(i) {
                    var o, s = le(this);
                    1 === this.nodeType && (o = r ? t.call(this, i, s.val()) : t, null == o ? o = "": "number" == typeof o ? o += "": le.isArray(o) && (o = le.map(o,
                    function(t) {
                        return null == t ? "": t + ""
                    })), n = le.valHooks[this.type] || le.valHooks[this.nodeName.toLowerCase()], n && "set" in n && n.set(this, o, "value") !== e || (this.value = o))
                });
                if (o) return n = le.valHooks[o.type] || le.valHooks[o.nodeName.toLowerCase()],
                n && "get" in n && (i = n.get(o, "value")) !== e ? i: (i = o.value, "string" == typeof i ? i.replace(Pe, "") : null == i ? "": i)
            }
        }
    }),
    le.extend({
        valHooks: {
            option: {
                get: function(t) {
                    var e = t.attributes.value;
                    return ! e || e.specified ? t.value: t.text
                }
            },
            select: {
                get: function(t) {
                    for (var e, i, n = t.options,
                    r = t.selectedIndex,
                    o = "select-one" === t.type || 0 > r,
                    s = o ? null: [], a = o ? r + 1 : n.length, l = 0 > r ? a: o ? r: 0; a > l; l++) if (i = n[l], !(!i.selected && l !== r || (le.support.optDisabled ? i.disabled: null !== i.getAttribute("disabled")) || i.parentNode.disabled && le.nodeName(i.parentNode, "optgroup"))) {
                        if (e = le(i).val(), o) return e;
                        s.push(e)
                    }
                    return s
                },
                set: function(t, e) {
                    var i = le.makeArray(e);
                    return le(t).find("option").each(function() {
                        this.selected = le.inArray(le(this).val(), i) >= 0
                    }),
                    i.length || (t.selectedIndex = -1),
                    i
                }
            }
        },
        attr: function(t, i, n) {
            var r, o, s, a = t.nodeType;
            if (t && 3 !== a && 8 !== a && 2 !== a) return typeof t.getAttribute === V ? le.prop(t, i, n) : (o = 1 !== a || !le.isXMLDoc(t), o && (i = i.toLowerCase(), r = le.attrHooks[i] || (Ee.test(i) ? Le: Ae)), n === e ? r && o && "get" in r && null !== (s = r.get(t, i)) ? s: (typeof t.getAttribute !== V && (s = t.getAttribute(i)), null == s ? e: s) : null !== n ? r && o && "set" in r && (s = r.set(t, n, i)) !== e ? s: (t.setAttribute(i, n + ""), n) : void le.removeAttr(t, i))
        },
        removeAttr: function(t, e) {
            var i, n, r = 0,
            o = e && e.match(ce);
            if (o && 1 === t.nodeType) for (; i = o[r++];) n = le.propFix[i] || i,
            Ee.test(i) ? !Ie && He.test(i) ? t[le.camelCase("default-" + i)] = t[n] = !1 : t[n] = !1 : le.attr(t, i, ""),
            t.removeAttribute(Ie ? i: n)
        },
        attrHooks: {
            type: {
                set: function(t, e) {
                    if (!le.support.radioValue && "radio" === e && le.nodeName(t, "input")) {
                        var i = t.value;
                        return t.setAttribute("type", e),
                        i && (t.value = i),
                        e
                    }
                }
            }
        },
        propFix: {
            tabindex: "tabIndex",
            readonly: "readOnly",
            "for": "htmlFor",
            "class": "className",
            maxlength: "maxLength",
            cellspacing: "cellSpacing",
            cellpadding: "cellPadding",
            rowspan: "rowSpan",
            colspan: "colSpan",
            usemap: "useMap",
            frameborder: "frameBorder",
            contenteditable: "contentEditable"
        },
        prop: function(t, i, n) {
            var r, o, s, a = t.nodeType;
            if (t && 3 !== a && 8 !== a && 2 !== a) return s = 1 !== a || !le.isXMLDoc(t),
            s && (i = le.propFix[i] || i, o = le.propHooks[i]),
            n !== e ? o && "set" in o && (r = o.set(t, n, i)) !== e ? r: t[i] = n: o && "get" in o && null !== (r = o.get(t, i)) ? r: t[i]
        },
        propHooks: {
            tabIndex: {
                get: function(t) {
                    var i = t.getAttributeNode("tabindex");
                    return i && i.specified ? parseInt(i.value, 10) : De.test(t.nodeName) || Ne.test(t.nodeName) && t.href ? 0 : e
                }
            }
        }
    }),
    Le = {
        get: function(t, i) {
            var n = le.prop(t, i),
            r = "boolean" == typeof n && t.getAttribute(i),
            o = "boolean" == typeof n ? Be && Ie ? null != r: He.test(i) ? t[le.camelCase("default-" + i)] : !!r: t.getAttributeNode(i);
            return o && o.value !== !1 ? i.toLowerCase() : e
        },
        set: function(t, e, i) {
            return e === !1 ? le.removeAttr(t, i) : Be && Ie || !He.test(i) ? t.setAttribute(!Ie && le.propFix[i] || i, i) : t[le.camelCase("default-" + i)] = t[i] = !0,
            i
        }
    },
    Be && Ie || (le.attrHooks.value = {
        get: function(t, i) {
            var n = t.getAttributeNode(i);
            return le.nodeName(t, "input") ? t.defaultValue: n && n.specified ? n.value: e
        },
        set: function(t, e, i) {
            return le.nodeName(t, "input") ? void(t.defaultValue = e) : Ae && Ae.set(t, e, i)
        }
    }),
    Ie || (Ae = le.valHooks.button = {
        get: function(t, i) {
            var n = t.getAttributeNode(i);
            return n && ("id" === i || "name" === i || "coords" === i ? "" !== n.value: n.specified) ? n.value: e
        },
        set: function(t, i, n) {
            var r = t.getAttributeNode(n);
            return r || t.setAttributeNode(r = t.ownerDocument.createAttribute(n)),
            r.value = i += "",
            "value" === n || i === t.getAttribute(n) ? i: e
        }
    },
    le.attrHooks.contenteditable = {
        get: Ae.get,
        set: function(t, e, i) {
            Ae.set(t, "" === e ? !1 : e, i)
        }
    },
    le.each(["width", "height"],
    function(t, e) {
        le.attrHooks[e] = le.extend(le.attrHooks[e], {
            set: function(t, i) {
                return "" === i ? (t.setAttribute(e, "auto"), i) : void 0
            }
        })
    })),
    le.support.hrefNormalized || (le.each(["href", "src", "width", "height"],
    function(t, i) {
        le.attrHooks[i] = le.extend(le.attrHooks[i], {
            get: function(t) {
                var n = t.getAttribute(i, 2);
                return null == n ? e: n
            }
        })
    }), le.each(["href", "src"],
    function(t, e) {
        le.propHooks[e] = {
            get: function(t) {
                return t.getAttribute(e, 4)
            }
        }
    })),
    le.support.style || (le.attrHooks.style = {
        get: function(t) {
            return t.style.cssText || e
        },
        set: function(t, e) {
            return t.style.cssText = e + ""
        }
    }),
    le.support.optSelected || (le.propHooks.selected = le.extend(le.propHooks.selected, {
        get: function(t) {
            var e = t.parentNode;
            return e && (e.selectedIndex, e.parentNode && e.parentNode.selectedIndex),
            null
        }
    })),
    le.support.enctype || (le.propFix.enctype = "encoding"),
    le.support.checkOn || le.each(["radio", "checkbox"],
    function() {
        le.valHooks[this] = {
            get: function(t) {
                return null === t.getAttribute("value") ? "on": t.value
            }
        }
    }),
    le.each(["radio", "checkbox"],
    function() {
        le.valHooks[this] = le.extend(le.valHooks[this], {
            set: function(t, e) {
                return le.isArray(e) ? t.checked = le.inArray(le(t).val(), e) >= 0 : void 0
            }
        })
    });
    var Oe = /^(?:input|select|textarea)$/i,
    ze = /^key/,
    Re = /^(?:mouse|contextmenu)|click/,
    je = /^(?:focusinfocus|focusoutblur)$/,
    We = /^([^.]*)(?:\.(.+)|)$/;
    le.event = {
        global: {},
        add: function(t, i, n, r, o) {
            var s, a, l, h, c, u, d, p, f, g, m, y = le._data(t);
            if (y) {
                for (n.handler && (h = n, n = h.handler, o = h.selector), n.guid || (n.guid = le.guid++), (a = y.events) || (a = y.events = {}), (u = y.handle) || (u = y.handle = function(t) {
                    return typeof le === V || t && le.event.triggered === t.type ? e: le.event.dispatch.apply(u.elem, arguments)
                },
                u.elem = t), i = (i || "").match(ce) || [""], l = i.length; l--;) s = We.exec(i[l]) || [],
                f = m = s[1],
                g = (s[2] || "").split(".").sort(),
                c = le.event.special[f] || {},
                f = (o ? c.delegateType: c.bindType) || f,
                c = le.event.special[f] || {},
                d = le.extend({
                    type: f,
                    origType: m,
                    data: r,
                    handler: n,
                    guid: n.guid,
                    selector: o,
                    needsContext: o && le.expr.match.needsContext.test(o),
                    namespace: g.join(".")
                },
                h),
                (p = a[f]) || (p = a[f] = [], p.delegateCount = 0, c.setup && c.setup.call(t, r, g, u) !== !1 || (t.addEventListener ? t.addEventListener(f, u, !1) : t.attachEvent && t.attachEvent("on" + f, u))),
                c.add && (c.add.call(t, d), d.handler.guid || (d.handler.guid = n.guid)),
                o ? p.splice(p.delegateCount++, 0, d) : p.push(d),
                le.event.global[f] = !0;
                t = null
            }
        },
        remove: function(t, e, i, n, r) {
            var o, s, a, l, h, c, u, d, p, f, g, m = le.hasData(t) && le._data(t);
            if (m && (c = m.events)) {
                for (e = (e || "").match(ce) || [""], h = e.length; h--;) if (a = We.exec(e[h]) || [], p = g = a[1], f = (a[2] || "").split(".").sort(), p) {
                    for (u = le.event.special[p] || {},
                    p = (n ? u.delegateType: u.bindType) || p, d = c[p] || [], a = a[2] && new RegExp("(^|\\.)" + f.join("\\.(?:.*\\.|)") + "(\\.|$)"), l = o = d.length; o--;) s = d[o],
                    !r && g !== s.origType || i && i.guid !== s.guid || a && !a.test(s.namespace) || n && n !== s.selector && ("**" !== n || !s.selector) || (d.splice(o, 1), s.selector && d.delegateCount--, u.remove && u.remove.call(t, s));
                    l && !d.length && (u.teardown && u.teardown.call(t, f, m.handle) !== !1 || le.removeEvent(t, p, m.handle), delete c[p])
                } else for (p in c) le.event.remove(t, p + e[h], i, n, !0);
                le.isEmptyObject(c) && (delete m.handle, le._removeData(t, "events"))
            }
        },
        trigger: function(i, n, r, o) {
            var s, a, l, h, c, u, d, p = [r || $],
            f = se.call(i, "type") ? i.type: i,
            g = se.call(i, "namespace") ? i.namespace.split(".") : [];
            if (l = u = r = r || $, 3 !== r.nodeType && 8 !== r.nodeType && !je.test(f + le.event.triggered) && (f.indexOf(".") >= 0 && (g = f.split("."), f = g.shift(), g.sort()), a = f.indexOf(":") < 0 && "on" + f, i = i[le.expando] ? i: new le.Event(f, "object" == typeof i && i), i.isTrigger = !0, i.namespace = g.join("."), i.namespace_re = i.namespace ? new RegExp("(^|\\.)" + g.join("\\.(?:.*\\.|)") + "(\\.|$)") : null, i.result = e, i.target || (i.target = r), n = null == n ? [i] : le.makeArray(n, [i]), c = le.event.special[f] || {},
            o || !c.trigger || c.trigger.apply(r, n) !== !1)) {
                if (!o && !c.noBubble && !le.isWindow(r)) {
                    for (h = c.delegateType || f, je.test(h + f) || (l = l.parentNode); l; l = l.parentNode) p.push(l),
                    u = l;
                    u === (r.ownerDocument || $) && p.push(u.defaultView || u.parentWindow || t)
                }
                for (d = 0; (l = p[d++]) && !i.isPropagationStopped();) i.type = d > 1 ? h: c.bindType || f,
                s = (le._data(l, "events") || {})[i.type] && le._data(l, "handle"),
                s && s.apply(l, n),
                s = a && l[a],
                s && le.acceptData(l) && s.apply && s.apply(l, n) === !1 && i.preventDefault();
                if (i.type = f, !(o || i.isDefaultPrevented() || c._default && c._default.apply(r.ownerDocument, n) !== !1 || "click" === f && le.nodeName(r, "a") || !le.acceptData(r) || !a || !r[f] || le.isWindow(r))) {
                    u = r[a],
                    u && (r[a] = null),
                    le.event.triggered = f;
                    try {
                        r[f]()
                    } catch(m) {}
                    le.event.triggered = e,
                    u && (r[a] = u)
                }
                return i.result
            }
        },
        dispatch: function(t) {
            t = le.event.fix(t);
            var i, n, r, o, s, a = [],
            l = ne.call(arguments),
            h = (le._data(this, "events") || {})[t.type] || [],
            c = le.event.special[t.type] || {};
            if (l[0] = t, t.delegateTarget = this, !c.preDispatch || c.preDispatch.call(this, t) !== !1) {
                for (a = le.event.handlers.call(this, t, h), i = 0; (o = a[i++]) && !t.isPropagationStopped();) for (t.currentTarget = o.elem, s = 0; (r = o.handlers[s++]) && !t.isImmediatePropagationStopped();)(!t.namespace_re || t.namespace_re.test(r.namespace)) && (t.handleObj = r, t.data = r.data, n = ((le.event.special[r.origType] || {}).handle || r.handler).apply(o.elem, l), n !== e && (t.result = n) === !1 && (t.preventDefault(), t.stopPropagation()));
                return c.postDispatch && c.postDispatch.call(this, t),
                t.result
            }
        },
        handlers: function(t, i) {
            var n, r, o, s, a = [],
            l = i.delegateCount,
            h = t.target;
            if (l && h.nodeType && (!t.button || "click" !== t.type)) for (; h != this; h = h.parentNode || this) if (1 === h.nodeType && (h.disabled !== !0 || "click" !== t.type)) {
                for (o = [], s = 0; l > s; s++) r = i[s],
                n = r.selector + " ",
                o[n] === e && (o[n] = r.needsContext ? le(n, this).index(h) >= 0 : le.find(n, this, null, [h]).length),
                o[n] && o.push(r);
                o.length && a.push({
                    elem: h,
                    handlers: o
                })
            }
            return l < i.length && a.push({
                elem: this,
                handlers: i.slice(l)
            }),
            a
        },
        fix: function(t) {
            if (t[le.expando]) return t;
            var e, i, n, r = t.type,
            o = t,
            s = this.fixHooks[r];
            for (s || (this.fixHooks[r] = s = Re.test(r) ? this.mouseHooks: ze.test(r) ? this.keyHooks: {}), n = s.props ? this.props.concat(s.props) : this.props, t = new le.Event(o), e = n.length; e--;) i = n[e],
            t[i] = o[i];
            return t.target || (t.target = o.srcElement || $),
            3 === t.target.nodeType && (t.target = t.target.parentNode),
            t.metaKey = !!t.metaKey,
            s.filter ? s.filter(t, o) : t
        },
        props: "altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),
        fixHooks: {},
        keyHooks: {
            props: "char charCode key keyCode".split(" "),
            filter: function(t, e) {
                return null == t.which && (t.which = null != e.charCode ? e.charCode: e.keyCode),
                t
            }
        },
        mouseHooks: {
            props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),
            filter: function(t, i) {
                var n, r, o, s = i.button,
                a = i.fromElement;
                return null == t.pageX && null != i.clientX && (r = t.target.ownerDocument || $, o = r.documentElement, n = r.body, t.pageX = i.clientX + (o && o.scrollLeft || n && n.scrollLeft || 0) - (o && o.clientLeft || n && n.clientLeft || 0), t.pageY = i.clientY + (o && o.scrollTop || n && n.scrollTop || 0) - (o && o.clientTop || n && n.clientTop || 0)),
                !t.relatedTarget && a && (t.relatedTarget = a === t.target ? i.toElement: a),
                t.which || s === e || (t.which = 1 & s ? 1 : 2 & s ? 3 : 4 & s ? 2 : 0),
                t
            }
        },
        special: {
            load: {
                noBubble: !0
            },
            click: {
                trigger: function() {
                    return le.nodeName(this, "input") && "checkbox" === this.type && this.click ? (this.click(), !1) : void 0
                }
            },
            focus: {
                trigger: function() {
                    if (this !== $.activeElement && this.focus) try {
                        return this.focus(),
                        !1
                    } catch(t) {}
                },
                delegateType: "focusin"
            },
            blur: {
                trigger: function() {
                    return this === $.activeElement && this.blur ? (this.blur(), !1) : void 0
                },
                delegateType: "focusout"
            },
            beforeunload: {
                postDispatch: function(t) {
                    t.result !== e && (t.originalEvent.returnValue = t.result)
                }
            }
        },
        simulate: function(t, e, i, n) {
            var r = le.extend(new le.Event, i, {
                type: t,
                isSimulated: !0,
                originalEvent: {}
            });
            n ? le.event.trigger(r, null, e) : le.event.dispatch.call(e, r),
            r.isDefaultPrevented() && i.preventDefault()
        }
    },
    le.removeEvent = $.removeEventListener ?
    function(t, e, i) {
        t.removeEventListener && t.removeEventListener(e, i, !1)
    }: function(t, e, i) {
        var n = "on" + e;
        t.detachEvent && (typeof t[n] === V && (t[n] = null), t.detachEvent(n, i))
    },
    le.Event = function(t, e) {
        return this instanceof le.Event ? (t && t.type ? (this.originalEvent = t, this.type = t.type, this.isDefaultPrevented = t.defaultPrevented || t.returnValue === !1 || t.getPreventDefault && t.getPreventDefault() ? l: h) : this.type = t, e && le.extend(this, e), this.timeStamp = t && t.timeStamp || le.now(), void(this[le.expando] = !0)) : new le.Event(t, e)
    },
    le.Event.prototype = {
        isDefaultPrevented: h,
        isPropagationStopped: h,
        isImmediatePropagationStopped: h,
        preventDefault: function() {
            var t = this.originalEvent;
            this.isDefaultPrevented = l,
            t && (t.preventDefault ? t.preventDefault() : t.returnValue = !1)
        },
        stopPropagation: function() {
            var t = this.originalEvent;
            this.isPropagationStopped = l,
            t && (t.stopPropagation && t.stopPropagation(), t.cancelBubble = !0)
        },
        stopImmediatePropagation: function() {
            this.isImmediatePropagationStopped = l,
            this.stopPropagation()
        }
    },
    le.each({
        mouseenter: "mouseover",
        mouseleave: "mouseout"
    },
    function(t, e) {
        le.event.special[t] = {
            delegateType: e,
            bindType: e,
            handle: function(t) {
                var i, n = this,
                r = t.relatedTarget,
                o = t.handleObj;
                return (!r || r !== n && !le.contains(n, r)) && (t.type = o.origType, i = o.handler.apply(this, arguments), t.type = e),
                i
            }
        }
    }),
    le.support.submitBubbles || (le.event.special.submit = {
        setup: function() {
            return le.nodeName(this, "form") ? !1 : void le.event.add(this, "click._submit keypress._submit",
            function(t) {
                var i = t.target,
                n = le.nodeName(i, "input") || le.nodeName(i, "button") ? i.form: e;
                n && !le._data(n, "submitBubbles") && (le.event.add(n, "submit._submit",
                function(t) {
                    t._submit_bubble = !0
                }), le._data(n, "submitBubbles", !0))
            })
        },
        postDispatch: function(t) {
            t._submit_bubble && (delete t._submit_bubble, this.parentNode && !t.isTrigger && le.event.simulate("submit", this.parentNode, t, !0))
        },
        teardown: function() {
            return le.nodeName(this, "form") ? !1 : void le.event.remove(this, "._submit")
        }
    }),
    le.support.changeBubbles || (le.event.special.change = {
        setup: function() {
            return Oe.test(this.nodeName) ? (("checkbox" === this.type || "radio" === this.type) && (le.event.add(this, "propertychange._change",
            function(t) {
                "checked" === t.originalEvent.propertyName && (this._just_changed = !0)
            }), le.event.add(this, "click._change",
            function(t) {
                this._just_changed && !t.isTrigger && (this._just_changed = !1),
                le.event.simulate("change", this, t, !0)
            })), !1) : void le.event.add(this, "beforeactivate._change",
            function(t) {
                var e = t.target;
                Oe.test(e.nodeName) && !le._data(e, "changeBubbles") && (le.event.add(e, "change._change",
                function(t) { ! this.parentNode || t.isSimulated || t.isTrigger || le.event.simulate("change", this.parentNode, t, !0)
                }), le._data(e, "changeBubbles", !0))
            })
        },
        handle: function(t) {
            var e = t.target;
            return this !== e || t.isSimulated || t.isTrigger || "radio" !== e.type && "checkbox" !== e.type ? t.handleObj.handler.apply(this, arguments) : void 0
        },
        teardown: function() {
            return le.event.remove(this, "._change"),
            !Oe.test(this.nodeName)
        }
    }),
    le.support.focusinBubbles || le.each({
        focus: "focusin",
        blur: "focusout"
    },
    function(t, e) {
        var i = 0,
        n = function(t) {
            le.event.simulate(e, t.target, le.event.fix(t), !0)
        };
        le.event.special[e] = {
            setup: function() {
                0 === i++&&$.addEventListener(t, n, !0)
            },
            teardown: function() {
                0 === --i && $.removeEventListener(t, n, !0)
            }
        }
    }),
    le.fn.extend({
        on: function(t, i, n, r, o) {
            var s, a;
            if ("object" == typeof t) {
                "string" != typeof i && (n = n || i, i = e);
                for (s in t) this.on(s, i, n, t[s], o);
                return this
            }
            if (null == n && null == r ? (r = i, n = i = e) : null == r && ("string" == typeof i ? (r = n, n = e) : (r = n, n = i, i = e)), r === !1) r = h;
            else if (!r) return this;
            return 1 === o && (a = r, r = function(t) {
                return le().off(t),
                a.apply(this, arguments)
            },
            r.guid = a.guid || (a.guid = le.guid++)),
            this.each(function() {
                le.event.add(this, t, r, n, i)
            })
        },
        one: function(t, e, i, n) {
            return this.on(t, e, i, n, 1)
        },
        off: function(t, i, n) {
            var r, o;
            if (t && t.preventDefault && t.handleObj) return r = t.handleObj,
            le(t.delegateTarget).off(r.namespace ? r.origType + "." + r.namespace: r.origType, r.selector, r.handler),
            this;
            if ("object" == typeof t) {
                for (o in t) this.off(o, i, t[o]);
                return this
            }
            return (i === !1 || "function" == typeof i) && (n = i, i = e),
            n === !1 && (n = h),
            this.each(function() {
                le.event.remove(this, t, n, i)
            })
        },
        bind: function(t, e, i) {
            return this.on(t, null, e, i)
        },
        unbind: function(t, e) {
            return this.off(t, null, e)
        },
        delegate: function(t, e, i, n) {
            return this.on(e, t, i, n)
        },
        undelegate: function(t, e, i) {
            return 1 === arguments.length ? this.off(t, "**") : this.off(e, t || "**", i)
        },
        trigger: function(t, e) {
            return this.each(function() {
                le.event.trigger(t, e, this)
            })
        },
        triggerHandler: function(t, e) {
            var i = this[0];
            return i ? le.event.trigger(t, e, i, !0) : void 0
        }
    }),
    /*!
 * Sizzle CSS Selector Engine
 * Copyright 2012 jQuery Foundation and other contributors
 * Released under the MIT license
 * http://sizzlejs.com/
 */
    function(t, e) {
        function i(t) {
            return fe.test(t + "")
        }
        function n() {
            var t, e = [];
            return t = function(i, n) {
                return e.push(i += " ") > S.cacheLength && delete t[e.shift()],
                t[i] = n
            }
        }
        function r(t) {
            return t[j] = !0,
            t
        }
        function o(t) {
            var e = N.createElement("div");
            try {
                return t(e)
            } catch(i) {
                return ! 1
            } finally {
                e = null
            }
        }
        function s(t, e, i, n) {
            var r, o, s, a, l, h, c, p, f, g;
            if ((e ? e.ownerDocument || e: W) !== N && D(e), e = e || N, i = i || [], !t || "string" != typeof t) return i;
            if (1 !== (a = e.nodeType) && 9 !== a) return [];
            if (!H && !n) {
                if (r = ge.exec(t)) if (s = r[1]) {
                    if (9 === a) {
                        if (o = e.getElementById(s), !o || !o.parentNode) return i;
                        if (o.id === s) return i.push(o),
                        i
                    } else if (e.ownerDocument && (o = e.ownerDocument.getElementById(s)) && z(e, o) && o.id === s) return i.push(o),
                    i
                } else {
                    if (r[2]) return K.apply(i, J.call(e.getElementsByTagName(t), 0)),
                    i;
                    if ((s = r[3]) && F.getByClassName && e.getElementsByClassName) return K.apply(i, J.call(e.getElementsByClassName(s), 0)),
                    i
                }
                if (F.qsa && !I.test(t)) {
                    if (c = !0, p = j, f = e, g = 9 === a && t, 1 === a && "object" !== e.nodeName.toLowerCase()) {
                        for (h = u(t), (c = e.getAttribute("id")) ? p = c.replace(ve, "\\$&") : e.setAttribute("id", p), p = "[id='" + p + "'] ", l = h.length; l--;) h[l] = p + d(h[l]);
                        f = pe.test(t) && e.parentNode || e,
                        g = h.join(",")
                    }
                    if (g) try {
                        return K.apply(i, J.call(f.querySelectorAll(g), 0)),
                        i
                    } catch(m) {} finally {
                        c || e.removeAttribute("id")
                    }
                }
            }
            return b(t.replace(se, "$1"), e, i, n)
        }
        function a(t, e) {
            var i = e && t,
            n = i && (~e.sourceIndex || $) - (~t.sourceIndex || $);
            if (n) return n;
            if (i) for (; i = i.nextSibling;) if (i === e) return - 1;
            return t ? 1 : -1
        }
        function l(t) {
            return function(e) {
                var i = e.nodeName.toLowerCase();
                return "input" === i && e.type === t
            }
        }
        function h(t) {
            return function(e) {
                var i = e.nodeName.toLowerCase();
                return ("input" === i || "button" === i) && e.type === t
            }
        }
        function c(t) {
            return r(function(e) {
                return e = +e,
                r(function(i, n) {
                    for (var r, o = t([], i.length, e), s = o.length; s--;) i[r = o[s]] && (i[r] = !(n[r] = i[r]))
                })
            })
        }
        function u(t, e) {
            var i, n, r, o, a, l, h, c = G[t + " "];
            if (c) return e ? 0 : c.slice(0);
            for (a = t, l = [], h = S.preFilter; a;) { (!i || (n = ae.exec(a))) && (n && (a = a.slice(n[0].length) || a), l.push(r = [])),
                i = !1,
                (n = he.exec(a)) && (i = n.shift(), r.push({
                    value: i,
                    type: n[0].replace(se, " ")
                }), a = a.slice(i.length));
                for (o in S.filter) ! (n = de[o].exec(a)) || h[o] && !(n = h[o](n)) || (i = n.shift(), r.push({
                    value: i,
                    type: o,
                    matches: n
                }), a = a.slice(i.length));
                if (!i) break
            }
            return e ? a.length: a ? s.error(t) : G(t, l).slice(0)
        }
        function d(t) {
            for (var e = 0,
            i = t.length,
            n = ""; i > e; e++) n += t[e].value;
            return n
        }
        function p(t, e, i) {
            var n = e.dir,
            r = i && "parentNode" === n,
            o = X++;
            return e.first ?
            function(e, i, o) {
                for (; e = e[n];) if (1 === e.nodeType || r) return t(e, i, o)
            }: function(e, i, s) {
                var a, l, h, c = _ + " " + o;
                if (s) {
                    for (; e = e[n];) if ((1 === e.nodeType || r) && t(e, i, s)) return ! 0
                } else for (; e = e[n];) if (1 === e.nodeType || r) if (h = e[j] || (e[j] = {}), (l = h[n]) && l[0] === c) {
                    if ((a = l[1]) === !0 || a === T) return a === !0
                } else if (l = h[n] = [c], l[1] = t(e, i, s) || T, l[1] === !0) return ! 0
            }
        }
        function f(t) {
            return t.length > 1 ?
            function(e, i, n) {
                for (var r = t.length; r--;) if (!t[r](e, i, n)) return ! 1;
                return ! 0
            }: t[0]
        }
        function g(t, e, i, n, r) {
            for (var o, s = [], a = 0, l = t.length, h = null != e; l > a; a++)(o = t[a]) && (!i || i(o, n, r)) && (s.push(o), h && e.push(a));
            return s
        }
        function m(t, e, i, n, o, s) {
            return n && !n[j] && (n = m(n)),
            o && !o[j] && (o = m(o, s)),
            r(function(r, s, a, l) {
                var h, c, u, d = [],
                p = [],
                f = s.length,
                m = r || x(e || "*", a.nodeType ? [a] : a, []),
                y = !t || !r && e ? m: g(m, d, t, a, l),
                v = i ? o || (r ? t: f || n) ? [] : s: y;
                if (i && i(y, v, a, l), n) for (h = g(v, p), n(h, [], a, l), c = h.length; c--;)(u = h[c]) && (v[p[c]] = !(y[p[c]] = u));
                if (r) {
                    if (o || t) {
                        if (o) {
                            for (h = [], c = v.length; c--;)(u = v[c]) && h.push(y[c] = u);
                            o(null, v = [], h, l)
                        }
                        for (c = v.length; c--;)(u = v[c]) && (h = o ? Q.call(r, u) : d[c]) > -1 && (r[h] = !(s[h] = u))
                    }
                } else v = g(v === s ? v.splice(f, v.length) : v),
                o ? o(null, s, v, l) : K.apply(s, v)
            })
        }
        function y(t) {
            for (var e, i, n, r = t.length,
            o = S.relative[t[0].type], s = o || S.relative[" "], a = o ? 1 : 0, l = p(function(t) {
                return t === e
            },
            s, !0), h = p(function(t) {
                return Q.call(e, t) > -1
            },
            s, !0), c = [function(t, i, n) {
                return ! o && (n || i !== P) || ((e = i).nodeType ? l(t, i, n) : h(t, i, n))
            }]; r > a; a++) if (i = S.relative[t[a].type]) c = [p(f(c), i)];
            else {
                if (i = S.filter[t[a].type].apply(null, t[a].matches), i[j]) {
                    for (n = ++a; r > n && !S.relative[t[n].type]; n++);
                    return m(a > 1 && f(c), a > 1 && d(t.slice(0, a - 1)).replace(se, "$1"), i, n > a && y(t.slice(a, n)), r > n && y(t = t.slice(n)), r > n && d(t))
                }
                c.push(i)
            }
            return f(c)
        }
        function v(t, e) {
            var i = 0,
            n = e.length > 0,
            o = t.length > 0,
            a = function(r, a, l, h, c) {
                var u, d, p, f = [],
                m = 0,
                y = "0",
                v = r && [],
                x = null != c,
                b = P,
                k = r || o && S.find.TAG("*", c && a.parentNode || a),
                w = _ += null == b ? 1 : Math.random() || .1;
                for (x && (P = a !== N && a, T = i); null != (u = k[y]); y++) {
                    if (o && u) {
                        for (d = 0; p = t[d++];) if (p(u, a, l)) {
                            h.push(u);
                            break
                        }
                        x && (_ = w, T = ++i)
                    }
                    n && ((u = !p && u) && m--, r && v.push(u))
                }
                if (m += y, n && y !== m) {
                    for (d = 0; p = e[d++];) p(v, f, a, l);
                    if (r) {
                        if (m > 0) for (; y--;) v[y] || f[y] || (f[y] = Z.call(h));
                        f = g(f)
                    }
                    K.apply(h, f),
                    x && !r && f.length > 0 && m + e.length > 1 && s.uniqueSort(h)
                }
                return x && (_ = w, P = b),
                v
            };
            return n ? r(a) : a
        }
        function x(t, e, i) {
            for (var n = 0,
            r = e.length; r > n; n++) s(t, e[n], i);
            return i
        }
        function b(t, e, i, n) {
            var r, o, s, a, l, h = u(t);
            if (!n && 1 === h.length) {
                if (o = h[0] = h[0].slice(0), o.length > 2 && "ID" === (s = o[0]).type && 9 === e.nodeType && !H && S.relative[o[1].type]) {
                    if (e = S.find.ID(s.matches[0].replace(be, ke), e)[0], !e) return i;
                    t = t.slice(o.shift().value.length)
                }
                for (r = de.needsContext.test(t) ? 0 : o.length; r--&&(s = o[r], !S.relative[a = s.type]);) if ((l = S.find[a]) && (n = l(s.matches[0].replace(be, ke), pe.test(o[0].type) && e.parentNode || e))) {
                    if (o.splice(r, 1), t = n.length && d(o), !t) return K.apply(i, J.call(n, 0)),
                    i;
                    break
                }
            }
            return L(t, h)(n, e, H, i, pe.test(t)),
            i
        }
        function k() {}
        var w, T, S, C, A, L, M, P, D, N, E, H, I, B, O, z, R, j = "sizzle" + -new Date,
        W = t.document,
        F = {},
        _ = 0,
        X = 0,
        Y = n(),
        G = n(),
        q = n(),
        V = typeof e,
        $ = 1 << 31,
        U = [],
        Z = U.pop,
        K = U.push,
        J = U.slice,
        Q = U.indexOf ||
        function(t) {
            for (var e = 0,
            i = this.length; i > e; e++) if (this[e] === t) return e;
            return - 1
        },
        te = "[\\x20\\t\\r\\n\\f]",
        ee = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",
        ie = ee.replace("w", "w#"),
        ne = "([*^$|!~]?=)",
        re = "\\[" + te + "*(" + ee + ")" + te + "*(?:" + ne + te + "*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|(" + ie + ")|)|)" + te + "*\\]",
        oe = ":(" + ee + ")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|" + re.replace(3, 8) + ")*)|.*)\\)|)",
        se = new RegExp("^" + te + "+|((?:^|[^\\\\])(?:\\\\.)*)" + te + "+$", "g"),
        ae = new RegExp("^" + te + "*," + te + "*"),
        he = new RegExp("^" + te + "*([\\x20\\t\\r\\n\\f>+~])" + te + "*"),
        ce = new RegExp(oe),
        ue = new RegExp("^" + ie + "$"),
        de = {
            ID: new RegExp("^#(" + ee + ")"),
            CLASS: new RegExp("^\\.(" + ee + ")"),
            NAME: new RegExp("^\\[name=['\"]?(" + ee + ")['\"]?\\]"),
            TAG: new RegExp("^(" + ee.replace("w", "w*") + ")"),
            ATTR: new RegExp("^" + re),
            PSEUDO: new RegExp("^" + oe),
            CHILD: new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + te + "*(even|odd|(([+-]|)(\\d*)n|)" + te + "*(?:([+-]|)" + te + "*(\\d+)|))" + te + "*\\)|)", "i"),
            needsContext: new RegExp("^" + te + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + te + "*((?:-\\d)?\\d*)" + te + "*\\)|)(?=[^-]|$)", "i")
        },
        pe = /[\x20\t\r\n\f]*[+~]/,
        fe = /^[^{]+\{\s*\[native code/,
        ge = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,
        me = /^(?:input|select|textarea|button)$/i,
        ye = /^h\d$/i,
        ve = /'|\\/g,
        xe = /\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g,
        be = /\\([\da-fA-F]{1,6}[\x20\t\r\n\f]?|.)/g,
        ke = function(t, e) {
            var i = "0x" + e - 65536;
            return i !== i ? e: 0 > i ? String.fromCharCode(i + 65536) : String.fromCharCode(i >> 10 | 55296, 1023 & i | 56320)
        };
        try {
            J.call(W.documentElement.childNodes, 0)[0].nodeType
        } catch(we) {
            J = function(t) {
                for (var e, i = []; e = this[t++];) i.push(e);
                return i
            }
        }
        A = s.isXML = function(t) {
            var e = t && (t.ownerDocument || t).documentElement;
            return e ? "HTML" !== e.nodeName: !1
        },
        D = s.setDocument = function(t) {
            var n = t ? t.ownerDocument || t: W;
            return n !== N && 9 === n.nodeType && n.documentElement ? (N = n, E = n.documentElement, H = A(n), F.tagNameNoComments = o(function(t) {
                return t.appendChild(n.createComment("")),
                !t.getElementsByTagName("*").length
            }), F.attributes = o(function(t) {
                t.innerHTML = "<select></select>";
                var e = typeof t.lastChild.getAttribute("multiple");
                return "boolean" !== e && "string" !== e
            }), F.getByClassName = o(function(t) {
                return t.innerHTML = "<div class='hidden e'></div><div class='hidden'></div>",
                t.getElementsByClassName && t.getElementsByClassName("e").length ? (t.lastChild.className = "e", 2 === t.getElementsByClassName("e").length) : !1
            }), F.getByName = o(function(t) {
                t.id = j + 0,
                t.innerHTML = "<a name='" + j + "'></a><div name='" + j + "'></div>",
                E.insertBefore(t, E.firstChild);
                var e = n.getElementsByName && n.getElementsByName(j).length === 2 + n.getElementsByName(j + 0).length;
                return F.getIdNotName = !n.getElementById(j),
                E.removeChild(t),
                e
            }), S.attrHandle = o(function(t) {
                return t.innerHTML = "<a href='#'></a>",
                t.firstChild && typeof t.firstChild.getAttribute !== V && "#" === t.firstChild.getAttribute("href")
            }) ? {}: {
                href: function(t) {
                    return t.getAttribute("href", 2)
                },
                type: function(t) {
                    return t.getAttribute("type")
                }
            },
            F.getIdNotName ? (S.find.ID = function(t, e) {
                if (typeof e.getElementById !== V && !H) {
                    var i = e.getElementById(t);
                    return i && i.parentNode ? [i] : []
                }
            },
            S.filter.ID = function(t) {
                var e = t.replace(be, ke);
                return function(t) {
                    return t.getAttribute("id") === e
                }
            }) : (S.find.ID = function(t, i) {
                if (typeof i.getElementById !== V && !H) {
                    var n = i.getElementById(t);
                    return n ? n.id === t || typeof n.getAttributeNode !== V && n.getAttributeNode("id").value === t ? [n] : e: []
                }
            },
            S.filter.ID = function(t) {
                var e = t.replace(be, ke);
                return function(t) {
                    var i = typeof t.getAttributeNode !== V && t.getAttributeNode("id");
                    return i && i.value === e
                }
            }), S.find.TAG = F.tagNameNoComments ?
            function(t, e) {
                return typeof e.getElementsByTagName !== V ? e.getElementsByTagName(t) : void 0
            }: function(t, e) {
                var i, n = [],
                r = 0,
                o = e.getElementsByTagName(t);
                if ("*" === t) {
                    for (; i = o[r++];) 1 === i.nodeType && n.push(i);
                    return n
                }
                return o
            },
            S.find.NAME = F.getByName &&
            function(t, e) {
                return typeof e.getElementsByName !== V ? e.getElementsByName(name) : void 0
            },
            S.find.CLASS = F.getByClassName &&
            function(t, e) {
                return typeof e.getElementsByClassName === V || H ? void 0 : e.getElementsByClassName(t)
            },
            B = [], I = [":focus"], (F.qsa = i(n.querySelectorAll)) && (o(function(t) {
                t.innerHTML = "<select><option selected=''></option></select>",
                t.querySelectorAll("[selected]").length || I.push("\\[" + te + "*(?:checked|disabled|ismap|multiple|readonly|selected|value)"),
                t.querySelectorAll(":checked").length || I.push(":checked")
            }), o(function(t) {
                t.innerHTML = "<input type='hidden' i=''/>",
                t.querySelectorAll("[i^='']").length && I.push("[*^$]=" + te + "*(?:\"\"|'')"),
                t.querySelectorAll(":enabled").length || I.push(":enabled", ":disabled"),
                t.querySelectorAll("*,:x"),
                I.push(",.*:")
            })), (F.matchesSelector = i(O = E.matchesSelector || E.mozMatchesSelector || E.webkitMatchesSelector || E.oMatchesSelector || E.msMatchesSelector)) && o(function(t) {
                F.disconnectedMatch = O.call(t, "div"),
                O.call(t, "[s!='']:x"),
                B.push("!=", oe)
            }), I = new RegExp(I.join("|")), B = new RegExp(B.join("|")), z = i(E.contains) || E.compareDocumentPosition ?
            function(t, e) {
                var i = 9 === t.nodeType ? t.documentElement: t,
                n = e && e.parentNode;
                return t === n || !(!n || 1 !== n.nodeType || !(i.contains ? i.contains(n) : t.compareDocumentPosition && 16 & t.compareDocumentPosition(n)))
            }: function(t, e) {
                if (e) for (; e = e.parentNode;) if (e === t) return ! 0;
                return ! 1
            },
            R = E.compareDocumentPosition ?
            function(t, e) {
                var i;
                return t === e ? (M = !0, 0) : (i = e.compareDocumentPosition && t.compareDocumentPosition && t.compareDocumentPosition(e)) ? 1 & i || t.parentNode && 11 === t.parentNode.nodeType ? t === n || z(W, t) ? -1 : e === n || z(W, e) ? 1 : 0 : 4 & i ? -1 : 1 : t.compareDocumentPosition ? -1 : 1
            }: function(t, e) {
                var i, r = 0,
                o = t.parentNode,
                s = e.parentNode,
                l = [t],
                h = [e];
                if (t === e) return M = !0,
                0;
                if (!o || !s) return t === n ? -1 : e === n ? 1 : o ? -1 : s ? 1 : 0;
                if (o === s) return a(t, e);
                for (i = t; i = i.parentNode;) l.unshift(i);
                for (i = e; i = i.parentNode;) h.unshift(i);
                for (; l[r] === h[r];) r++;
                return r ? a(l[r], h[r]) : l[r] === W ? -1 : h[r] === W ? 1 : 0
            },
            M = !1, [0, 0].sort(R), F.detectDuplicates = M, N) : N
        },
        s.matches = function(t, e) {
            return s(t, null, null, e)
        },
        s.matchesSelector = function(t, e) {
            if ((t.ownerDocument || t) !== N && D(t), e = e.replace(xe, "='$1']"), !(!F.matchesSelector || H || B && B.test(e) || I.test(e))) try {
                var i = O.call(t, e);
                if (i || F.disconnectedMatch || t.document && 11 !== t.document.nodeType) return i
            } catch(n) {}
            return s(e, N, null, [t]).length > 0
        },
        s.contains = function(t, e) {
            return (t.ownerDocument || t) !== N && D(t),
            z(t, e)
        },
        s.attr = function(t, e) {
            var i;
            return (t.ownerDocument || t) !== N && D(t),
            H || (e = e.toLowerCase()),
            (i = S.attrHandle[e]) ? i(t) : H || F.attributes ? t.getAttribute(e) : ((i = t.getAttributeNode(e)) || t.getAttribute(e)) && t[e] === !0 ? e: i && i.specified ? i.value: null
        },
        s.error = function(t) {
            throw new Error("Syntax error, unrecognized expression: " + t)
        },
        s.uniqueSort = function(t) {
            var e, i = [],
            n = 1,
            r = 0;
            if (M = !F.detectDuplicates, t.sort(R), M) {
                for (; e = t[n]; n++) e === t[n - 1] && (r = i.push(n));
                for (; r--;) t.splice(i[r], 1)
            }
            return t
        },
        C = s.getText = function(t) {
            var e, i = "",
            n = 0,
            r = t.nodeType;
            if (r) {
                if (1 === r || 9 === r || 11 === r) {
                    if ("string" == typeof t.textContent) return t.textContent;
                    for (t = t.firstChild; t; t = t.nextSibling) i += C(t)
                } else if (3 === r || 4 === r) return t.nodeValue
            } else for (; e = t[n]; n++) i += C(e);
            return i
        },
        S = s.selectors = {
            cacheLength: 50,
            createPseudo: r,
            match: de,
            find: {},
            relative: {
                ">": {
                    dir: "parentNode",
                    first: !0
                },
                " ": {
                    dir: "parentNode"
                },
                "+": {
                    dir: "previousSibling",
                    first: !0
                },
                "~": {
                    dir: "previousSibling"
                }
            },
            preFilter: {
                ATTR: function(t) {
                    return t[1] = t[1].replace(be, ke),
                    t[3] = (t[4] || t[5] || "").replace(be, ke),
                    "~=" === t[2] && (t[3] = " " + t[3] + " "),
                    t.slice(0, 4)
                },
                CHILD: function(t) {
                    return t[1] = t[1].toLowerCase(),
                    "nth" === t[1].slice(0, 3) ? (t[3] || s.error(t[0]), t[4] = +(t[4] ? t[5] + (t[6] || 1) : 2 * ("even" === t[3] || "odd" === t[3])), t[5] = +(t[7] + t[8] || "odd" === t[3])) : t[3] && s.error(t[0]),
                    t
                },
                PSEUDO: function(t) {
                    var e, i = !t[5] && t[2];
                    return de.CHILD.test(t[0]) ? null: (t[4] ? t[2] = t[4] : i && ce.test(i) && (e = u(i, !0)) && (e = i.indexOf(")", i.length - e) - i.length) && (t[0] = t[0].slice(0, e), t[2] = i.slice(0, e)), t.slice(0, 3))
                }
            },
            filter: {
                TAG: function(t) {
                    return "*" === t ?
                    function() {
                        return ! 0
                    }: (t = t.replace(be, ke).toLowerCase(),
                    function(e) {
                        return e.nodeName && e.nodeName.toLowerCase() === t
                    })
                },
                CLASS: function(t) {
                    var e = Y[t + " "];
                    return e || (e = new RegExp("(^|" + te + ")" + t + "(" + te + "|$)")) && Y(t,
                    function(t) {
                        return e.test(t.className || typeof t.getAttribute !== V && t.getAttribute("class") || "")
                    })
                },
                ATTR: function(t, e, i) {
                    return function(n) {
                        var r = s.attr(n, t);
                        return null == r ? "!=" === e: e ? (r += "", "=" === e ? r === i: "!=" === e ? r !== i: "^=" === e ? i && 0 === r.indexOf(i) : "*=" === e ? i && r.indexOf(i) > -1 : "$=" === e ? i && r.slice( - i.length) === i: "~=" === e ? (" " + r + " ").indexOf(i) > -1 : "|=" === e ? r === i || r.slice(0, i.length + 1) === i + "-": !1) : !0
                    }
                },
                CHILD: function(t, e, i, n, r) {
                    var o = "nth" !== t.slice(0, 3),
                    s = "last" !== t.slice( - 4),
                    a = "of-type" === e;
                    return 1 === n && 0 === r ?
                    function(t) {
                        return !! t.parentNode
                    }: function(e, i, l) {
                        var h, c, u, d, p, f, g = o !== s ? "nextSibling": "previousSibling",
                        m = e.parentNode,
                        y = a && e.nodeName.toLowerCase(),
                        v = !l && !a;
                        if (m) {
                            if (o) {
                                for (; g;) {
                                    for (u = e; u = u[g];) if (a ? u.nodeName.toLowerCase() === y: 1 === u.nodeType) return ! 1;
                                    f = g = "only" === t && !f && "nextSibling"
                                }
                                return ! 0
                            }
                            if (f = [s ? m.firstChild: m.lastChild], s && v) {
                                for (c = m[j] || (m[j] = {}), h = c[t] || [], p = h[0] === _ && h[1], d = h[0] === _ && h[2], u = p && m.childNodes[p]; u = ++p && u && u[g] || (d = p = 0) || f.pop();) if (1 === u.nodeType && ++d && u === e) {
                                    c[t] = [_, p, d];
                                    break
                                }
                            } else if (v && (h = (e[j] || (e[j] = {}))[t]) && h[0] === _) d = h[1];
                            else for (; (u = ++p && u && u[g] || (d = p = 0) || f.pop()) && ((a ? u.nodeName.toLowerCase() !== y: 1 !== u.nodeType) || !++d || (v && ((u[j] || (u[j] = {}))[t] = [_, d]), u !== e)););
                            return d -= r,
                            d === n || d % n === 0 && d / n >= 0
                        }
                    }
                },
                PSEUDO: function(t, e) {
                    var i, n = S.pseudos[t] || S.setFilters[t.toLowerCase()] || s.error("unsupported pseudo: " + t);
                    return n[j] ? n(e) : n.length > 1 ? (i = [t, t, "", e], S.setFilters.hasOwnProperty(t.toLowerCase()) ? r(function(t, i) {
                        for (var r, o = n(t, e), s = o.length; s--;) r = Q.call(t, o[s]),
                        t[r] = !(i[r] = o[s])
                    }) : function(t) {
                        return n(t, 0, i)
                    }) : n
                }
            },
            pseudos: {
                not: r(function(t) {
                    var e = [],
                    i = [],
                    n = L(t.replace(se, "$1"));
                    return n[j] ? r(function(t, e, i, r) {
                        for (var o, s = n(t, null, r, []), a = t.length; a--;)(o = s[a]) && (t[a] = !(e[a] = o))
                    }) : function(t, r, o) {
                        return e[0] = t,
                        n(e, null, o, i),
                        !i.pop()
                    }
                }),
                has: r(function(t) {
                    return function(e) {
                        return s(t, e).length > 0
                    }
                }),
                contains: r(function(t) {
                    return function(e) {
                        return (e.textContent || e.innerText || C(e)).indexOf(t) > -1
                    }
                }),
                lang: r(function(t) {
                    return ue.test(t || "") || s.error("unsupported lang: " + t),
                    t = t.replace(be, ke).toLowerCase(),
                    function(e) {
                        var i;
                        do
                        if (i = H ? e.getAttribute("xml:lang") || e.getAttribute("lang") : e.lang) return i = i.toLowerCase(),
                        i === t || 0 === i.indexOf(t + "-");
                        while ((e = e.parentNode) && 1 === e.nodeType);
                        return ! 1
                    }
                }),
                target: function(e) {
                    var i = t.location && t.location.hash;
                    return i && i.slice(1) === e.id
                },
                root: function(t) {
                    return t === E
                },
                focus: function(t) {
                    return t === N.activeElement && (!N.hasFocus || N.hasFocus()) && !!(t.type || t.href || ~t.tabIndex)
                },
                enabled: function(t) {
                    return t.disabled === !1
                },
                disabled: function(t) {
                    return t.disabled === !0
                },
                checked: function(t) {
                    var e = t.nodeName.toLowerCase();
                    return "input" === e && !!t.checked || "option" === e && !!t.selected
                },
                selected: function(t) {
                    return t.parentNode && t.parentNode.selectedIndex,
                    t.selected === !0
                },
                empty: function(t) {
                    for (t = t.firstChild; t; t = t.nextSibling) if (t.nodeName > "@" || 3 === t.nodeType || 4 === t.nodeType) return ! 1;
                    return ! 0
                },
                parent: function(t) {
                    return ! S.pseudos.empty(t)
                },
                header: function(t) {
                    return ye.test(t.nodeName)
                },
                input: function(t) {
                    return me.test(t.nodeName)
                },
                button: function(t) {
                    var e = t.nodeName.toLowerCase();
                    return "input" === e && "button" === t.type || "button" === e
                },
                text: function(t) {
                    var e;
                    return "input" === t.nodeName.toLowerCase() && "text" === t.type && (null == (e = t.getAttribute("type")) || e.toLowerCase() === t.type)
                },
                first: c(function() {
                    return [0]
                }),
                last: c(function(t, e) {
                    return [e - 1]
                }),
                eq: c(function(t, e, i) {
                    return [0 > i ? i + e: i]
                }),
                even: c(function(t, e) {
                    for (var i = 0; e > i; i += 2) t.push(i);
                    return t
                }),
                odd: c(function(t, e) {
                    for (var i = 1; e > i; i += 2) t.push(i);
                    return t
                }),
                lt: c(function(t, e, i) {
                    for (var n = 0 > i ? i + e: i; --n >= 0;) t.push(n);
                    return t
                }),
                gt: c(function(t, e, i) {
                    for (var n = 0 > i ? i + e: i; ++n < e;) t.push(n);
                    return t
                })
            }
        };
        for (w in {
            radio: !0,
            checkbox: !0,
            file: !0,
            password: !0,
            image: !0
        }) S.pseudos[w] = l(w);
        for (w in {
            submit: !0,
            reset: !0
        }) S.pseudos[w] = h(w);
        L = s.compile = function(t, e) {
            var i, n = [],
            r = [],
            o = q[t + " "];
            if (!o) {
                for (e || (e = u(t)), i = e.length; i--;) o = y(e[i]),
                o[j] ? n.push(o) : r.push(o);
                o = q(t, v(r, n))
            }
            return o
        },
        S.pseudos.nth = S.pseudos.eq,
        S.filters = k.prototype = S.pseudos,
        S.setFilters = new k,
        D(),
        s.attr = le.attr,
        le.find = s,
        le.expr = s.selectors,
        le.expr[":"] = le.expr.pseudos,
        le.unique = s.uniqueSort,
        le.text = s.getText,
        le.isXMLDoc = s.isXML,
        le.contains = s.contains
    } (t);
    var Fe = /Until$/,
    _e = /^(?:parents|prev(?:Until|All))/,
    Xe = /^.[^:#\[\.,]*$/,
    Ye = le.expr.match.needsContext,
    Ge = {
        children: !0,
        contents: !0,
        next: !0,
        prev: !0
    };
    le.fn.extend({
        find: function(t) {
            var e, i, n, r = this.length;
            if ("string" != typeof t) return n = this,
            this.pushStack(le(t).filter(function() {
                for (e = 0; r > e; e++) if (le.contains(n[e], this)) return ! 0
            }));
            for (i = [], e = 0; r > e; e++) le.find(t, this[e], i);
            return i = this.pushStack(r > 1 ? le.unique(i) : i),
            i.selector = (this.selector ? this.selector + " ": "") + t,
            i
        },
        has: function(t) {
            var e, i = le(t, this),
            n = i.length;
            return this.filter(function() {
                for (e = 0; n > e; e++) if (le.contains(this, i[e])) return ! 0
            })
        },
        not: function(t) {
            return this.pushStack(u(this, t, !1))
        },
        filter: function(t) {
            return this.pushStack(u(this, t, !0))
        },
        is: function(t) {
            return !! t && ("string" == typeof t ? Ye.test(t) ? le(t, this.context).index(this[0]) >= 0 : le.filter(t, this).length > 0 : this.filter(t).length > 0)
        },
        closest: function(t, e) {
            for (var i, n = 0,
            r = this.length,
            o = [], s = Ye.test(t) || "string" != typeof t ? le(t, e || this.context) : 0; r > n; n++) for (i = this[n]; i && i.ownerDocument && i !== e && 11 !== i.nodeType;) {
                if (s ? s.index(i) > -1 : le.find.matchesSelector(i, t)) {
                    o.push(i);
                    break
                }
                i = i.parentNode
            }
            return this.pushStack(o.length > 1 ? le.unique(o) : o)
        },
        index: function(t) {
            return t ? "string" == typeof t ? le.inArray(this[0], le(t)) : le.inArray(t.jquery ? t[0] : t, this) : this[0] && this[0].parentNode ? this.first().prevAll().length: -1
        },
        add: function(t, e) {
            var i = "string" == typeof t ? le(t, e) : le.makeArray(t && t.nodeType ? [t] : t),
            n = le.merge(this.get(), i);
            return this.pushStack(le.unique(n))
        },
        addBack: function(t) {
            return this.add(null == t ? this.prevObject: this.prevObject.filter(t))
        }
    }),
    le.fn.andSelf = le.fn.addBack,
    le.each({
        parent: function(t) {
            var e = t.parentNode;
            return e && 11 !== e.nodeType ? e: null
        },
        parents: function(t) {
            return le.dir(t, "parentNode")
        },
        parentsUntil: function(t, e, i) {
            return le.dir(t, "parentNode", i)
        },
        next: function(t) {
            return c(t, "nextSibling")
        },
        prev: function(t) {
            return c(t, "previousSibling")
        },
        nextAll: function(t) {
            return le.dir(t, "nextSibling")
        },
        prevAll: function(t) {
            return le.dir(t, "previousSibling")
        },
        nextUntil: function(t, e, i) {
            return le.dir(t, "nextSibling", i)
        },
        prevUntil: function(t, e, i) {
            return le.dir(t, "previousSibling", i)
        },
        siblings: function(t) {
            return le.sibling((t.parentNode || {}).firstChild, t)
        },
        children: function(t) {
            return le.sibling(t.firstChild)
        },
        contents: function(t) {
            return le.nodeName(t, "iframe") ? t.contentDocument || t.contentWindow.document: le.merge([], t.childNodes)
        }
    },
    function(t, e) {
        le.fn[t] = function(i, n) {
            var r = le.map(this, e, i);
            return Fe.test(t) || (n = i),
            n && "string" == typeof n && (r = le.filter(n, r)),
            r = this.length > 1 && !Ge[t] ? le.unique(r) : r,
            this.length > 1 && _e.test(t) && (r = r.reverse()),
            this.pushStack(r)
        }
    }),
    le.extend({
        filter: function(t, e, i) {
            return i && (t = ":not(" + t + ")"),
            1 === e.length ? le.find.matchesSelector(e[0], t) ? [e[0]] : [] : le.find.matches(t, e)
        },
        dir: function(t, i, n) {
            for (var r = [], o = t[i]; o && 9 !== o.nodeType && (n === e || 1 !== o.nodeType || !le(o).is(n));) 1 === o.nodeType && r.push(o),
            o = o[i];
            return r
        },
        sibling: function(t, e) {
            for (var i = []; t; t = t.nextSibling) 1 === t.nodeType && t !== e && i.push(t);
            return i
        }
    });
    var qe = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",
    Ve = / jQuery\d+="(?:null|\d+)"/g,
    $e = new RegExp("<(?:" + qe + ")[\\s/>]", "i"),
    Ue = /^\s+/,
    Ze = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,
    Ke = /<([\w:]+)/,
    Je = /<tbody/i,
    Qe = /<|&#?\w+;/,
    ti = /<(?:script|style|link)/i,
    ei = /^(?:checkbox|radio)$/i,
    ii = /checked\s*(?:[^=]|=\s*.checked.)/i,
    ni = /^$|\/(?:java|ecma)script/i,
    ri = /^true\/(.*)/,
    oi = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,
    si = {
        option: [1, "<select multiple='multiple'>", "</select>"],
        legend: [1, "<fieldset>", "</fieldset>"],
        area: [1, "<map>", "</map>"],
        param: [1, "<object>", "</object>"],
        thead: [1, "<table>", "</table>"],
        tr: [2, "<table><tbody>", "</tbody></table>"],
        col: [2, "<table><tbody></tbody><colgroup>", "</colgroup></table>"],
        td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],
        _default: le.support.htmlSerialize ? [0, "", ""] : [1, "X<div>", "</div>"]
    },
    ai = d($),
    li = ai.appendChild($.createElement("div"));
    si.optgroup = si.option,
    si.tbody = si.tfoot = si.colgroup = si.caption = si.thead,
    si.th = si.td,
    le.fn.extend({
        text: function(t) {
            return le.access(this,
            function(t) {
                return t === e ? le.text(this) : this.empty().append((this[0] && this[0].ownerDocument || $).createTextNode(t))
            },
            null, t, arguments.length)
        },
        wrapAll: function(t) {
            if (le.isFunction(t)) return this.each(function(e) {
                le(this).wrapAll(t.call(this, e))
            });
            if (this[0]) {
                var e = le(t, this[0].ownerDocument).eq(0).clone(!0);
                this[0].parentNode && e.insertBefore(this[0]),
                e.map(function() {
                    for (var t = this; t.firstChild && 1 === t.firstChild.nodeType;) t = t.firstChild;
                    return t
                }).append(this)
            }
            return this
        },
        wrapInner: function(t) {
            return this.each(le.isFunction(t) ?
            function(e) {
                le(this).wrapInner(t.call(this, e))
            }: function() {
                var e = le(this),
                i = e.contents();
                i.length ? i.wrapAll(t) : e.append(t)
            })
        },
        wrap: function(t) {
            var e = le.isFunction(t);
            return this.each(function(i) {
                le(this).wrapAll(e ? t.call(this, i) : t)
            })
        },
        unwrap: function() {
            return this.parent().each(function() {
                le.nodeName(this, "body") || le(this).replaceWith(this.childNodes)
            }).end()
        },
        append: function() {
            return this.domManip(arguments, !0,
            function(t) { (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) && this.appendChild(t)
            })
        },
        prepend: function() {
            return this.domManip(arguments, !0,
            function(t) { (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) && this.insertBefore(t, this.firstChild)
            })
        },
        before: function() {
            return this.domManip(arguments, !1,
            function(t) {
                this.parentNode && this.parentNode.insertBefore(t, this)
            })
        },
        after: function() {
            return this.domManip(arguments, !1,
            function(t) {
                this.parentNode && this.parentNode.insertBefore(t, this.nextSibling)
            })
        },
        remove: function(t, e) {
            for (var i, n = 0; null != (i = this[n]); n++)(!t || le.filter(t, [i]).length > 0) && (e || 1 !== i.nodeType || le.cleanData(x(i)), i.parentNode && (e && le.contains(i.ownerDocument, i) && m(x(i, "script")), i.parentNode.removeChild(i)));
            return this
        },
        empty: function() {
            for (var t, e = 0; null != (t = this[e]); e++) {
                for (1 === t.nodeType && le.cleanData(x(t, !1)); t.firstChild;) t.removeChild(t.firstChild);
                t.options && le.nodeName(t, "select") && (t.options.length = 0)
            }
            return this
        },
        clone: function(t, e) {
            return t = null == t ? !1 : t,
            e = null == e ? t: e,
            this.map(function() {
                return le.clone(this, t, e)
            })
        },
        html: function(t) {
            return le.access(this,
            function(t) {
                var i = this[0] || {},
                n = 0,
                r = this.length;
                if (t === e) return 1 === i.nodeType ? i.innerHTML.replace(Ve, "") : e;
                if (! ("string" != typeof t || ti.test(t) || !le.support.htmlSerialize && $e.test(t) || !le.support.leadingWhitespace && Ue.test(t) || si[(Ke.exec(t) || ["", ""])[1].toLowerCase()])) {
                    t = t.replace(Ze, "<$1></$2>");
                    try {
                        for (; r > n; n++) i = this[n] || {},
                        1 === i.nodeType && (le.cleanData(x(i, !1)), i.innerHTML = t);
                        i = 0
                    } catch(o) {}
                }
                i && this.empty().append(t)
            },
            null, t, arguments.length)
        },
        replaceWith: function(t) {
            var e = le.isFunction(t);
            return e || "string" == typeof t || (t = le(t).not(this).detach()),
            this.domManip([t], !0,
            function(t) {
                var e = this.nextSibling,
                i = this.parentNode;
                i && (le(this).remove(), i.insertBefore(t, e))
            })
        },
        detach: function(t) {
            return this.remove(t, !0)
        },
        domManip: function(t, i, n) {
            t = ee.apply([], t);
            var r, o, s, a, l, h, c = 0,
            u = this.length,
            d = this,
            m = u - 1,
            y = t[0],
            v = le.isFunction(y);
            if (v || !(1 >= u || "string" != typeof y || le.support.checkClone) && ii.test(y)) return this.each(function(r) {
                var o = d.eq(r);
                v && (t[0] = y.call(this, r, i ? o.html() : e)),
                o.domManip(t, i, n)
            });
            if (u && (h = le.buildFragment(t, this[0].ownerDocument, !1, this), r = h.firstChild, 1 === h.childNodes.length && (h = r), r)) {
                for (i = i && le.nodeName(r, "tr"), a = le.map(x(h, "script"), f), s = a.length; u > c; c++) o = h,
                c !== m && (o = le.clone(o, !0, !0), s && le.merge(a, x(o, "script"))),
                n.call(i && le.nodeName(this[c], "table") ? p(this[c], "tbody") : this[c], o, c);
                if (s) for (l = a[a.length - 1].ownerDocument, le.map(a, g), c = 0; s > c; c++) o = a[c],
                ni.test(o.type || "") && !le._data(o, "globalEval") && le.contains(l, o) && (o.src ? le.ajax({
                    url: o.src,
                    type: "GET",
                    dataType: "script",
                    async: !1,
                    global: !1,
                    "throws": !0
                }) : le.globalEval((o.text || o.textContent || o.innerHTML || "").replace(oi, "")));
                h = r = null
            }
            return this
        }
    }),
    le.each({
        appendTo: "append",
        prependTo: "prepend",
        insertBefore: "before",
        insertAfter: "after",
        replaceAll: "replaceWith"
    },
    function(t, e) {
        le.fn[t] = function(t) {
            for (var i, n = 0,
            r = [], o = le(t), s = o.length - 1; s >= n; n++) i = n === s ? this: this.clone(!0),
            le(o[n])[e](i),
            ie.apply(r, i.get());
            return this.pushStack(r)
        }
    }),
    le.extend({
        clone: function(t, e, i) {
            var n, r, o, s, a, l = le.contains(t.ownerDocument, t);
            if (le.support.html5Clone || le.isXMLDoc(t) || !$e.test("<" + t.nodeName + ">") ? o = t.cloneNode(!0) : (li.innerHTML = t.outerHTML, li.removeChild(o = li.firstChild)), !(le.support.noCloneEvent && le.support.noCloneChecked || 1 !== t.nodeType && 11 !== t.nodeType || le.isXMLDoc(t))) for (n = x(o), a = x(t), s = 0; null != (r = a[s]); ++s) n[s] && v(r, n[s]);
            if (e) if (i) for (a = a || x(t), n = n || x(o), s = 0; null != (r = a[s]); s++) y(r, n[s]);
            else y(t, o);
            return n = x(o, "script"),
            n.length > 0 && m(n, !l && x(t, "script")),
            n = a = r = null,
            o
        },
        buildFragment: function(t, e, i, n) {
            for (var r, o, s, a, l, h, c, u = t.length,
            p = d(e), f = [], g = 0; u > g; g++) if (o = t[g], o || 0 === o) if ("object" === le.type(o)) le.merge(f, o.nodeType ? [o] : o);
            else if (Qe.test(o)) {
                for (a = a || p.appendChild(e.createElement("div")), l = (Ke.exec(o) || ["", ""])[1].toLowerCase(), c = si[l] || si._default, a.innerHTML = c[1] + o.replace(Ze, "<$1></$2>") + c[2], r = c[0]; r--;) a = a.lastChild;
                if (!le.support.leadingWhitespace && Ue.test(o) && f.push(e.createTextNode(Ue.exec(o)[0])), !le.support.tbody) for (o = "table" !== l || Je.test(o) ? "<table>" !== c[1] || Je.test(o) ? 0 : a: a.firstChild, r = o && o.childNodes.length; r--;) le.nodeName(h = o.childNodes[r], "tbody") && !h.childNodes.length && o.removeChild(h);
                for (le.merge(f, a.childNodes), a.textContent = ""; a.firstChild;) a.removeChild(a.firstChild);
                a = p.lastChild
            } else f.push(e.createTextNode(o));
            for (a && p.removeChild(a), le.support.appendChecked || le.grep(x(f, "input"), b), g = 0; o = f[g++];) if ((!n || -1 === le.inArray(o, n)) && (s = le.contains(o.ownerDocument, o), a = x(p.appendChild(o), "script"), s && m(a), i)) for (r = 0; o = a[r++];) ni.test(o.type || "") && i.push(o);
            return a = null,
            p
        },
        cleanData: function(t, e) {
            for (var i, n, r, o, s = 0,
            a = le.expando,
            l = le.cache,
            h = le.support.deleteExpando,
            c = le.event.special; null != (i = t[s]); s++) if ((e || le.acceptData(i)) && (r = i[a], o = r && l[r])) {
                if (o.events) for (n in o.events) c[n] ? le.event.remove(i, n) : le.removeEvent(i, n, o.handle);
                l[r] && (delete l[r], h ? delete i[a] : typeof i.removeAttribute !== V ? i.removeAttribute(a) : i[a] = null, Q.push(r))
            }
        }
    });
    var hi, ci, ui, di = /alpha\([^)]*\)/i,
    pi = /opacity\s*=\s*([^)]*)/,
    fi = /^(top|right|bottom|left)$/,
    gi = /^(none|table(?!-c[ea]).+)/,
    mi = /^margin/,
    yi = new RegExp("^(" + he + ")(.*)$", "i"),
    vi = new RegExp("^(" + he + ")(?!px)[a-z%]+$", "i"),
    xi = new RegExp("^([+-])=(" + he + ")", "i"),
    bi = {
        BODY: "block"
    },
    ki = {
        position: "absolute",
        visibility: "hidden",
        display: "block"
    },
    wi = {
        letterSpacing: 0,
        fontWeight: 400
    },
    Ti = ["Top", "Right", "Bottom", "Left"],
    Si = ["Webkit", "O", "Moz", "ms"];
    le.fn.extend({
        css: function(t, i) {
            return le.access(this,
            function(t, i, n) {
                var r, o, s = {},
                a = 0;
                if (le.isArray(i)) {
                    for (o = ci(t), r = i.length; r > a; a++) s[i[a]] = le.css(t, i[a], !1, o);
                    return s
                }
                return n !== e ? le.style(t, i, n) : le.css(t, i)
            },
            t, i, arguments.length > 1)
        },
        show: function() {
            return T(this, !0)
        },
        hide: function() {
            return T(this)
        },
        toggle: function(t) {
            var e = "boolean" == typeof t;
            return this.each(function() { (e ? t: w(this)) ? le(this).show() : le(this).hide()
            })
        }
    }),
    le.extend({
        cssHooks: {
            opacity: {
                get: function(t, e) {
                    if (e) {
                        var i = ui(t, "opacity");
                        return "" === i ? "1": i
                    }
                }
            }
        },
        cssNumber: {
            columnCount: !0,
            fillOpacity: !0,
            fontWeight: !0,
            lineHeight: !0,
            opacity: !0,
            orphans: !0,
            widows: !0,
            zIndex: !0,
            zoom: !0
        },
        cssProps: {
            "float": le.support.cssFloat ? "cssFloat": "styleFloat"
        },
        style: function(t, i, n, r) {
            if (t && 3 !== t.nodeType && 8 !== t.nodeType && t.style) {
                var o, s, a, l = le.camelCase(i),
                h = t.style;
                if (i = le.cssProps[l] || (le.cssProps[l] = k(h, l)), a = le.cssHooks[i] || le.cssHooks[l], n === e) return a && "get" in a && (o = a.get(t, !1, r)) !== e ? o: h[i];
                if (s = typeof n, "string" === s && (o = xi.exec(n)) && (n = (o[1] + 1) * o[2] + parseFloat(le.css(t, i)), s = "number"), !(null == n || "number" === s && isNaN(n) || ("number" !== s || le.cssNumber[l] || (n += "px"), le.support.clearCloneStyle || "" !== n || 0 !== i.indexOf("background") || (h[i] = "inherit"), a && "set" in a && (n = a.set(t, n, r)) === e))) try {
                    h[i] = n
                } catch(c) {}
            }
        },
        css: function(t, i, n, r) {
            var o, s, a, l = le.camelCase(i);
            return i = le.cssProps[l] || (le.cssProps[l] = k(t.style, l)),
            a = le.cssHooks[i] || le.cssHooks[l],
            a && "get" in a && (s = a.get(t, !0, n)),
            s === e && (s = ui(t, i, r)),
            "normal" === s && i in wi && (s = wi[i]),
            "" === n || n ? (o = parseFloat(s), n === !0 || le.isNumeric(o) ? o || 0 : s) : s
        },
        swap: function(t, e, i, n) {
            var r, o, s = {};
            for (o in e) s[o] = t.style[o],
            t.style[o] = e[o];
            r = i.apply(t, n || []);
            for (o in e) t.style[o] = s[o];
            return r
        }
    }),
    t.getComputedStyle ? (ci = function(e) {
        return t.getComputedStyle(e, null)
    },
    ui = function(t, i, n) {
        var r, o, s, a = n || ci(t),
        l = a ? a.getPropertyValue(i) || a[i] : e,
        h = t.style;
        return a && ("" !== l || le.contains(t.ownerDocument, t) || (l = le.style(t, i)), vi.test(l) && mi.test(i) && (r = h.width, o = h.minWidth, s = h.maxWidth, h.minWidth = h.maxWidth = h.width = l, l = a.width, h.width = r, h.minWidth = o, h.maxWidth = s)),
        l
    }) : $.documentElement.currentStyle && (ci = function(t) {
        return t.currentStyle
    },
    ui = function(t, i, n) {
        var r, o, s, a = n || ci(t),
        l = a ? a[i] : e,
        h = t.style;
        return null == l && h && h[i] && (l = h[i]),
        vi.test(l) && !fi.test(i) && (r = h.left, o = t.runtimeStyle, s = o && o.left, s && (o.left = t.currentStyle.left), h.left = "fontSize" === i ? "1em": l, l = h.pixelLeft + "px", h.left = r, s && (o.left = s)),
        "" === l ? "auto": l
    }),
    le.each(["height", "width"],
    function(t, e) {
        le.cssHooks[e] = {
            get: function(t, i, n) {
                return i ? 0 === t.offsetWidth && gi.test(le.css(t, "display")) ? le.swap(t, ki,
                function() {
                    return A(t, e, n)
                }) : A(t, e, n) : void 0
            },
            set: function(t, i, n) {
                var r = n && ci(t);
                return S(t, i, n ? C(t, e, n, le.support.boxSizing && "border-box" === le.css(t, "boxSizing", !1, r), r) : 0)
            }
        }
    }),
    le.support.opacity || (le.cssHooks.opacity = {
        get: function(t, e) {
            return pi.test((e && t.currentStyle ? t.currentStyle.filter: t.style.filter) || "") ? .01 * parseFloat(RegExp.$1) + "": e ? "1": ""
        },
        set: function(t, e) {
            var i = t.style,
            n = t.currentStyle,
            r = le.isNumeric(e) ? "alpha(opacity=" + 100 * e + ")": "",
            o = n && n.filter || i.filter || "";
            i.zoom = 1,
            (e >= 1 || "" === e) && "" === le.trim(o.replace(di, "")) && i.removeAttribute && (i.removeAttribute("filter"), "" === e || n && !n.filter) || (i.filter = di.test(o) ? o.replace(di, r) : o + " " + r)
        }
    }),
    le(function() {
        le.support.reliableMarginRight || (le.cssHooks.marginRight = {
            get: function(t, e) {
                return e ? le.swap(t, {
                    display: "inline-block"
                },
                ui, [t, "marginRight"]) : void 0
            }
        }),
        !le.support.pixelPosition && le.fn.position && le.each(["top", "left"],
        function(t, e) {
            le.cssHooks[e] = {
                get: function(t, i) {
                    return i ? (i = ui(t, e), vi.test(i) ? le(t).position()[e] + "px": i) : void 0
                }
            }
        })
    }),
    le.expr && le.expr.filters && (le.expr.filters.hidden = function(t) {
        return t.offsetWidth <= 0 && t.offsetHeight <= 0 || !le.support.reliableHiddenOffsets && "none" === (t.style && t.style.display || le.css(t, "display"))
    },
    le.expr.filters.visible = function(t) {
        return ! le.expr.filters.hidden(t)
    }),
    le.each({
        margin: "",
        padding: "",
        border: "Width"
    },
    function(t, e) {
        le.cssHooks[t + e] = {
            expand: function(i) {
                for (var n = 0,
                r = {},
                o = "string" == typeof i ? i.split(" ") : [i]; 4 > n; n++) r[t + Ti[n] + e] = o[n] || o[n - 2] || o[0];
                return r
            }
        },
        mi.test(t) || (le.cssHooks[t + e].set = S)
    });
    var Ci = /%20/g,
    Ai = /\[\]$/,
    Li = /\r?\n/g,
    Mi = /^(?:submit|button|image|reset|file)$/i,
    Pi = /^(?:input|select|textarea|keygen)/i;
    le.fn.extend({
        serialize: function() {
            return le.param(this.serializeArray())
        },
        serializeArray: function() {
            return this.map(function() {
                var t = le.prop(this, "elements");
                return t ? le.makeArray(t) : this
            }).filter(function() {
                var t = this.type;
                return this.name && !le(this).is(":disabled") && Pi.test(this.nodeName) && !Mi.test(t) && (this.checked || !ei.test(t))
            }).map(function(t, e) {
                var i = le(this).val();
                return null == i ? null: le.isArray(i) ? le.map(i,
                function(t) {
                    return {
                        name: e.name,
                        value: t.replace(Li, "\r\n")
                    }
                }) : {
                    name: e.name,
                    value: i.replace(Li, "\r\n")
                }
            }).get()
        }
    }),
    le.param = function(t, i) {
        var n, r = [],
        o = function(t, e) {
            e = le.isFunction(e) ? e() : null == e ? "": e,
            r[r.length] = encodeURIComponent(t) + "=" + encodeURIComponent(e)
        };
        if (i === e && (i = le.ajaxSettings && le.ajaxSettings.traditional), le.isArray(t) || t.jquery && !le.isPlainObject(t)) le.each(t,
        function() {
            o(this.name, this.value)
        });
        else for (n in t) P(n, t[n], i, o);
        return r.join("&").replace(Ci, "+")
    },
    le.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),
    function(t, e) {
        le.fn[e] = function(t, i) {
            return arguments.length > 0 ? this.on(e, null, t, i) : this.trigger(e)
        }
    }),
    le.fn.hover = function(t, e) {
        return this.mouseenter(t).mouseleave(e || t)
    };
    var Di, Ni, Ei = le.now(),
    Hi = /\?/,
    Ii = /#.*$/,
    Bi = /([?&])_=[^&]*/,
    Oi = /^(.*?):[ \t]*([^\r\n]*)\r?$/gm,
    zi = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/,
    Ri = /^(?:GET|HEAD)$/,
    ji = /^\/\//,
    Wi = /^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/,
    Fi = le.fn.load,
    _i = {},
    Xi = {},
    Yi = "*/".concat("*");
    try {
        Ni = U.href
    } catch(Gi) {
        Ni = $.createElement("a"),
        Ni.href = "",
        Ni = Ni.href
    }
    Di = Wi.exec(Ni.toLowerCase()) || [],
    le.fn.load = function(t, i, n) {
        if ("string" != typeof t && Fi) return Fi.apply(this, arguments);
        var r, o, s, a = this,
        l = t.indexOf(" ");
        return l >= 0 && (r = t.slice(l, t.length), t = t.slice(0, l)),
        le.isFunction(i) ? (n = i, i = e) : i && "object" == typeof i && (s = "POST"),
        a.length > 0 && le.ajax({
            url: t,
            type: s,
            dataType: "html",
            data: i
        }).done(function(t) {
            o = arguments,
            a.html(r ? le("<div>").append(le.parseHTML(t)).find(r) : t)
        }).complete(n &&
        function(t, e) {
            a.each(n, o || [t.responseText, e, t])
        }),
        this
    },
    le.each(["ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend"],
    function(t, e) {
        le.fn[e] = function(t) {
            return this.on(e, t)
        }
    }),
    le.each(["get", "post"],
    function(t, i) {
        le[i] = function(t, n, r, o) {
            return le.isFunction(n) && (o = o || r, r = n, n = e),
            le.ajax({
                url: t,
                type: i,
                dataType: o,
                data: n,
                success: r
            })
        }
    }),
    le.extend({
        active: 0,
        lastModified: {},
        etag: {},
        ajaxSettings: {
            url: Ni,
            type: "GET",
            isLocal: zi.test(Di[1]),
            global: !0,
            processData: !0,
            async: !0,
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            accepts: {
                "*": Yi,
                text: "text/plain",
                html: "text/html",
                xml: "application/xml, text/xml",
                json: "application/json, text/javascript"
            },
            contents: {
                xml: /xml/,
                html: /html/,
                json: /json/
            },
            responseFields: {
                xml: "responseXML",
                text: "responseText"
            },
            converters: {
                "* text": t.String,
                "text html": !0,
                "text json": le.parseJSON,
                "text xml": le.parseXML
            },
            flatOptions: {
                url: !0,
                context: !0
            }
        },
        ajaxSetup: function(t, e) {
            return e ? E(E(t, le.ajaxSettings), e) : E(le.ajaxSettings, t)
        },
        ajaxPrefilter: D(_i),
        ajaxTransport: D(Xi),
        ajax: function(t, i) {
            function n(t, i, n, r) {
                var o, u, v, x, k, T = i;
                2 !== b && (b = 2, l && clearTimeout(l), c = e, a = r || "", w.readyState = t > 0 ? 4 : 0, n && (x = H(d, w, n)), t >= 200 && 300 > t || 304 === t ? (d.ifModified && (k = w.getResponseHeader("Last-Modified"), k && (le.lastModified[s] = k), k = w.getResponseHeader("etag"), k && (le.etag[s] = k)), 204 === t ? (o = !0, T = "nocontent") : 304 === t ? (o = !0, T = "notmodified") : (o = I(d, x), T = o.state, u = o.data, v = o.error, o = !v)) : (v = T, (t || !T) && (T = "error", 0 > t && (t = 0))), w.status = t, w.statusText = (i || T) + "", o ? g.resolveWith(p, [u, T, w]) : g.rejectWith(p, [w, T, v]), w.statusCode(y), y = e, h && f.trigger(o ? "ajaxSuccess": "ajaxError", [w, d, o ? u: v]), m.fireWith(p, [w, T]), h && (f.trigger("ajaxComplete", [w, d]), --le.active || le.event.trigger("ajaxStop")))
            }
            "object" == typeof t && (i = t, t = e),
            i = i || {};
            var r, o, s, a, l, h, c, u, d = le.ajaxSetup({},
            i),
            p = d.context || d,
            f = d.context && (p.nodeType || p.jquery) ? le(p) : le.event,
            g = le.Deferred(),
            m = le.Callbacks("once memory"),
            y = d.statusCode || {},
            v = {},
            x = {},
            b = 0,
            k = "canceled",
            w = {
                readyState: 0,
                getResponseHeader: function(t) {
                    var e;
                    if (2 === b) {
                        if (!u) for (u = {}; e = Oi.exec(a);) u[e[1].toLowerCase()] = e[2];
                        e = u[t.toLowerCase()]
                    }
                    return null == e ? null: e
                },
                getAllResponseHeaders: function() {
                    return 2 === b ? a: null
                },
                setRequestHeader: function(t, e) {
                    var i = t.toLowerCase();
                    return b || (t = x[i] = x[i] || t, v[t] = e),
                    this
                },
                overrideMimeType: function(t) {
                    return b || (d.mimeType = t),
                    this
                },
                statusCode: function(t) {
                    var e;
                    if (t) if (2 > b) for (e in t) y[e] = [y[e], t[e]];
                    else w.always(t[w.status]);
                    return this
                },
                abort: function(t) {
                    var e = t || k;
                    return c && c.abort(e),
                    n(0, e),
                    this
                }
            };
            if (g.promise(w).complete = m.add, w.success = w.done, w.error = w.fail, d.url = ((t || d.url || Ni) + "").replace(Ii, "").replace(ji, Di[1] + "//"), d.type = i.method || i.type || d.method || d.type, d.dataTypes = le.trim(d.dataType || "*").toLowerCase().match(ce) || [""], null == d.crossDomain && (r = Wi.exec(d.url.toLowerCase()), d.crossDomain = !(!r || r[1] === Di[1] && r[2] === Di[2] && (r[3] || ("http:" === r[1] ? 80 : 443)) == (Di[3] || ("http:" === Di[1] ? 80 : 443)))), d.data && d.processData && "string" != typeof d.data && (d.data = le.param(d.data, d.traditional)), N(_i, d, i, w), 2 === b) return w;
            h = d.global,
            h && 0 === le.active++&&le.event.trigger("ajaxStart"),
            d.type = d.type.toUpperCase(),
            d.hasContent = !Ri.test(d.type),
            s = d.url,
            d.hasContent || (d.data && (s = d.url += (Hi.test(s) ? "&": "?") + d.data, delete d.data), d.cache === !1 && (d.url = Bi.test(s) ? s.replace(Bi, "$1_=" + Ei++) : s + (Hi.test(s) ? "&": "?") + "_=" + Ei++)),
            d.ifModified && (le.lastModified[s] && w.setRequestHeader("If-Modified-Since", le.lastModified[s]), le.etag[s] && w.setRequestHeader("If-None-Match", le.etag[s])),
            (d.data && d.hasContent && d.contentType !== !1 || i.contentType) && w.setRequestHeader("Content-Type", d.contentType),
            w.setRequestHeader("Accept", d.dataTypes[0] && d.accepts[d.dataTypes[0]] ? d.accepts[d.dataTypes[0]] + ("*" !== d.dataTypes[0] ? ", " + Yi + "; q=0.01": "") : d.accepts["*"]);
            for (o in d.headers) w.setRequestHeader(o, d.headers[o]);
            if (d.beforeSend && (d.beforeSend.call(p, w, d) === !1 || 2 === b)) return w.abort();
            k = "abort";
            for (o in {
                success: 1,
                error: 1,
                complete: 1
            }) w[o](d[o]);
            if (c = N(Xi, d, i, w)) {
                w.readyState = 1,
                h && f.trigger("ajaxSend", [w, d]),
                d.async && d.timeout > 0 && (l = setTimeout(function() {
                    w.abort("timeout")
                },
                d.timeout));
                try {
                    b = 1,
                    c.send(v, n)
                } catch(T) {
                    if (! (2 > b)) throw T;
                    n( - 1, T)
                }
            } else n( - 1, "No Transport");
            return w
        },
        getScript: function(t, i) {
            return le.get(t, e, i, "script")
        },
        getJSON: function(t, e, i) {
            return le.get(t, e, i, "json")
        }
    }),
    le.ajaxSetup({
        accepts: {
            script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"
        },
        contents: {
            script: /(?:java|ecma)script/
        },
        converters: {
            "text script": function(t) {
                return le.globalEval(t),
                t
            }
        }
    }),
    le.ajaxPrefilter("script",
    function(t) {
        t.cache === e && (t.cache = !1),
        t.crossDomain && (t.type = "GET", t.global = !1)
    }),
    le.ajaxTransport("script",
    function(t) {
        if (t.crossDomain) {
            var i, n = $.head || le("head")[0] || $.documentElement;
            return {
                send: function(e, r) {
                    i = $.createElement("script"),
                    i.async = !0,
                    t.scriptCharset && (i.charset = t.scriptCharset),
                    i.src = t.url,
                    i.onload = i.onreadystatechange = function(t, e) { (e || !i.readyState || /loaded|complete/.test(i.readyState)) && (i.onload = i.onreadystatechange = null, i.parentNode && i.parentNode.removeChild(i), i = null, e || r(200, "success"))
                    },
                    n.insertBefore(i, n.firstChild)
                },
                abort: function() {
                    i && i.onload(e, !0)
                }
            }
        }
    });
    var qi = [],
    Vi = /(=)\?(?=&|$)|\?\?/;
    le.ajaxSetup({
        jsonp: "callback",
        jsonpCallback: function() {
            var t = qi.pop() || le.expando + "_" + Ei++;
            return this[t] = !0,
            t
        }
    }),
    le.ajaxPrefilter("json jsonp",
    function(i, n, r) {
        var o, s, a, l = i.jsonp !== !1 && (Vi.test(i.url) ? "url": "string" == typeof i.data && !(i.contentType || "").indexOf("application/x-www-form-urlencoded") && Vi.test(i.data) && "data");
        return l || "jsonp" === i.dataTypes[0] ? (o = i.jsonpCallback = le.isFunction(i.jsonpCallback) ? i.jsonpCallback() : i.jsonpCallback, l ? i[l] = i[l].replace(Vi, "$1" + o) : i.jsonp !== !1 && (i.url += (Hi.test(i.url) ? "&": "?") + i.jsonp + "=" + o), i.converters["script json"] = function() {
            return a || le.error(o + " was not called"),
            a[0]
        },
        i.dataTypes[0] = "json", s = t[o], t[o] = function() {
            a = arguments
        },
        r.always(function() {
            t[o] = s,
            i[o] && (i.jsonpCallback = n.jsonpCallback, qi.push(o)),
            a && le.isFunction(s) && s(a[0]),
            a = s = e
        }), "script") : void 0
    });
    var $i, Ui, Zi = 0,
    Ki = t.ActiveXObject &&
    function() {
        var t;
        for (t in $i) $i[t](e, !0)
    };
    le.ajaxSettings.xhr = t.ActiveXObject ?
    function() {
        return ! this.isLocal && B() || O()
    }: B,
    Ui = le.ajaxSettings.xhr(),
    le.support.cors = !!Ui && "withCredentials" in Ui,
    Ui = le.support.ajax = !!Ui,
    Ui && le.ajaxTransport(function(i) {
        if (!i.crossDomain || le.support.cors) {
            var n;
            return {
                send: function(r, o) {
                    var s, a, l = i.xhr();
                    if (i.username ? l.open(i.type, i.url, i.async, i.username, i.password) : l.open(i.type, i.url, i.async), i.xhrFields) for (a in i.xhrFields) l[a] = i.xhrFields[a];
                    i.mimeType && l.overrideMimeType && l.overrideMimeType(i.mimeType),
                    i.crossDomain || r["X-Requested-With"] || (r["X-Requested-With"] = "XMLHttpRequest");
                    try {
                        for (a in r) l.setRequestHeader(a, r[a])
                    } catch(h) {}
                    l.send(i.hasContent && i.data || null),
                    n = function(t, r) {
                        var a, h, c, u;
                        try {
                            if (n && (r || 4 === l.readyState)) if (n = e, s && (l.onreadystatechange = le.noop, Ki && delete $i[s]), r) 4 !== l.readyState && l.abort();
                            else {
                                u = {},
                                a = l.status,
                                h = l.getAllResponseHeaders(),
                                "string" == typeof l.responseText && (u.text = l.responseText);
                                try {
                                    c = l.statusText
                                } catch(d) {
                                    c = ""
                                }
                                a || !i.isLocal || i.crossDomain ? 1223 === a && (a = 204) : a = u.text ? 200 : 404
                            }
                        } catch(p) {
                            r || o( - 1, p)
                        }
                        u && o(a, c, u, h)
                    },
                    i.async ? 4 === l.readyState ? setTimeout(n) : (s = ++Zi, Ki && ($i || ($i = {},
                    le(t).unload(Ki)), $i[s] = n), l.onreadystatechange = n) : n()
                },
                abort: function() {
                    n && n(e, !0)
                }
            }
        }
    });
    var Ji, Qi, tn = /^(?:toggle|show|hide)$/,
    en = new RegExp("^(?:([+-])=|)(" + he + ")([a-z%]*)$", "i"),
    nn = /queueHooks$/,
    rn = [F],
    on = {
        "*": [function(t, e) {
            var i, n, r = this.createTween(t, e),
            o = en.exec(e),
            s = r.cur(),
            a = +s || 0,
            l = 1,
            h = 20;
            if (o) {
                if (i = +o[2], n = o[3] || (le.cssNumber[t] ? "": "px"), "px" !== n && a) {
                    a = le.css(r.elem, t, !0) || i || 1;
                    do l = l || ".5",
                    a /= l,
                    le.style(r.elem, t, a + n);
                    while (l !== (l = r.cur() / s) && 1 !== l && --h)
                }
                r.unit = n,
                r.start = a,
                r.end = o[1] ? a + (o[1] + 1) * i: i
            }
            return r
        }]
    };
    le.Animation = le.extend(j, {
        tweener: function(t, e) {
            le.isFunction(t) ? (e = t, t = ["*"]) : t = t.split(" ");
            for (var i, n = 0,
            r = t.length; r > n; n++) i = t[n],
            on[i] = on[i] || [],
            on[i].unshift(e)
        },
        prefilter: function(t, e) {
            e ? rn.unshift(t) : rn.push(t)
        }
    }),
    le.Tween = _,
    _.prototype = {
        constructor: _,
        init: function(t, e, i, n, r, o) {
            this.elem = t,
            this.prop = i,
            this.easing = r || "swing",
            this.options = e,
            this.start = this.now = this.cur(),
            this.end = n,
            this.unit = o || (le.cssNumber[i] ? "": "px")
        },
        cur: function() {
            var t = _.propHooks[this.prop];
            return t && t.get ? t.get(this) : _.propHooks._default.get(this)
        },
        run: function(t) {
            var e, i = _.propHooks[this.prop];
            return this.pos = e = this.options.duration ? le.easing[this.easing](t, this.options.duration * t, 0, 1, this.options.duration) : t,
            this.now = (this.end - this.start) * e + this.start,
            this.options.step && this.options.step.call(this.elem, this.now, this),
            i && i.set ? i.set(this) : _.propHooks._default.set(this),
            this
        }
    },
    _.prototype.init.prototype = _.prototype,
    _.propHooks = {
        _default: {
            get: function(t) {
                var e;
                return null == t.elem[t.prop] || t.elem.style && null != t.elem.style[t.prop] ? (e = le.css(t.elem, t.prop, ""), e && "auto" !== e ? e: 0) : t.elem[t.prop]
            },
            set: function(t) {
                le.fx.step[t.prop] ? le.fx.step[t.prop](t) : t.elem.style && (null != t.elem.style[le.cssProps[t.prop]] || le.cssHooks[t.prop]) ? le.style(t.elem, t.prop, t.now + t.unit) : t.elem[t.prop] = t.now
            }
        }
    },
    _.propHooks.scrollTop = _.propHooks.scrollLeft = {
        set: function(t) {
            t.elem.nodeType && t.elem.parentNode && (t.elem[t.prop] = t.now)
        }
    },
    le.each(["toggle", "show", "hide"],
    function(t, e) {
        var i = le.fn[e];
        le.fn[e] = function(t, n, r) {
            return null == t || "boolean" == typeof t ? i.apply(this, arguments) : this.animate(X(e, !0), t, n, r)
        }
    }),
    le.fn.extend({
        fadeTo: function(t, e, i, n) {
            return this.filter(w).css("opacity", 0).show().end().animate({
                opacity: e
            },
            t, i, n)
        },
        animate: function(t, e, i, n) {
            var r = le.isEmptyObject(t),
            o = le.speed(e, i, n),
            s = function() {
                var e = j(this, le.extend({},
                t), o);
                s.finish = function() {
                    e.stop(!0)
                },
                (r || le._data(this, "finish")) && e.stop(!0)
            };
            return s.finish = s,
            r || o.queue === !1 ? this.each(s) : this.queue(o.queue, s)
        },
        stop: function(t, i, n) {
            var r = function(t) {
                var e = t.stop;
                delete t.stop,
                e(n)
            };
            return "string" != typeof t && (n = i, i = t, t = e),
            i && t !== !1 && this.queue(t || "fx", []),
            this.each(function() {
                var e = !0,
                i = null != t && t + "queueHooks",
                o = le.timers,
                s = le._data(this);
                if (i) s[i] && s[i].stop && r(s[i]);
                else for (i in s) s[i] && s[i].stop && nn.test(i) && r(s[i]);
                for (i = o.length; i--;) o[i].elem !== this || null != t && o[i].queue !== t || (o[i].anim.stop(n), e = !1, o.splice(i, 1)); (e || !n) && le.dequeue(this, t)
            })
        },
        finish: function(t) {
            return t !== !1 && (t = t || "fx"),
            this.each(function() {
                var e, i = le._data(this),
                n = i[t + "queue"],
                r = i[t + "queueHooks"],
                o = le.timers,
                s = n ? n.length: 0;
                for (i.finish = !0, le.queue(this, t, []), r && r.cur && r.cur.finish && r.cur.finish.call(this), e = o.length; e--;) o[e].elem === this && o[e].queue === t && (o[e].anim.stop(!0), o.splice(e, 1));
                for (e = 0; s > e; e++) n[e] && n[e].finish && n[e].finish.call(this);
                delete i.finish
            })
        }
    }),
    le.each({
        slideDown: X("show"),
        slideUp: X("hide"),
        slideToggle: X("toggle"),
        fadeIn: {
            opacity: "show"
        },
        fadeOut: {
            opacity: "hide"
        },
        fadeToggle: {
            opacity: "toggle"
        }
    },
    function(t, e) {
        le.fn[t] = function(t, i, n) {
            return this.animate(e, t, i, n)
        }
    }),
    le.speed = function(t, e, i) {
        var n = t && "object" == typeof t ? le.extend({},
        t) : {
            complete: i || !i && e || le.isFunction(t) && t,
            duration: t,
            easing: i && e || e && !le.isFunction(e) && e
        };
        return n.duration = le.fx.off ? 0 : "number" == typeof n.duration ? n.duration: n.duration in le.fx.speeds ? le.fx.speeds[n.duration] : le.fx.speeds._default,
        (null == n.queue || n.queue === !0) && (n.queue = "fx"),
        n.old = n.complete,
        n.complete = function() {
            le.isFunction(n.old) && n.old.call(this),
            n.queue && le.dequeue(this, n.queue)
        },
        n
    },
    le.easing = {
        linear: function(t) {
            return t
        },
        swing: function(t) {
            return.5 - Math.cos(t * Math.PI) / 2
        }
    },
    le.timers = [],
    le.fx = _.prototype.init,
    le.fx.tick = function() {
        var t, i = le.timers,
        n = 0;
        for (Ji = le.now(); n < i.length; n++) t = i[n],
        t() || i[n] !== t || i.splice(n--, 1);
        i.length || le.fx.stop(),
        Ji = e
    },
    le.fx.timer = function(t) {
        t() && le.timers.push(t) && le.fx.start()
    },
    le.fx.interval = 13,
    le.fx.start = function() {
        Qi || (Qi = setInterval(le.fx.tick, le.fx.interval))
    },
    le.fx.stop = function() {
        clearInterval(Qi),
        Qi = null
    },
    le.fx.speeds = {
        slow: 600,
        fast: 200,
        _default: 400
    },
    le.fx.step = {},
    le.expr && le.expr.filters && (le.expr.filters.animated = function(t) {
        return le.grep(le.timers,
        function(e) {
            return t === e.elem
        }).length
    }),
    le.fn.offset = function(t) {
        if (arguments.length) return t === e ? this: this.each(function(e) {
            le.offset.setOffset(this, t, e)
        });
        var i, n, r = {
            top: 0,
            left: 0
        },
        o = this[0],
        s = o && o.ownerDocument;
        if (s) return i = s.documentElement,
        le.contains(i, o) ? (typeof o.getBoundingClientRect !== V && (r = o.getBoundingClientRect()), n = Y(s), {
            top: r.top + (n.pageYOffset || i.scrollTop) - (i.clientTop || 0),
            left: r.left + (n.pageXOffset || i.scrollLeft) - (i.clientLeft || 0)
        }) : r
    },
    le.offset = {
        setOffset: function(t, e, i) {
            var n = le.css(t, "position");
            "static" === n && (t.style.position = "relative");
            var r, o, s = le(t),
            a = s.offset(),
            l = le.css(t, "top"),
            h = le.css(t, "left"),
            c = ("absolute" === n || "fixed" === n) && le.inArray("auto", [l, h]) > -1,
            u = {},
            d = {};
            c ? (d = s.position(), r = d.top, o = d.left) : (r = parseFloat(l) || 0, o = parseFloat(h) || 0),
            le.isFunction(e) && (e = e.call(t, i, a)),
            null != e.top && (u.top = e.top - a.top + r),
            null != e.left && (u.left = e.left - a.left + o),
            "using" in e ? e.using.call(t, u) : s.css(u)
        }
    },
    le.fn.extend({
        position: function() {
            if (this[0]) {
                var t, e, i = {
                    top: 0,
                    left: 0
                },
                n = this[0];
                return "fixed" === le.css(n, "position") ? e = n.getBoundingClientRect() : (t = this.offsetParent(), e = this.offset(), le.nodeName(t[0], "html") || (i = t.offset()), i.top += le.css(t[0], "borderTopWidth", !0), i.left += le.css(t[0], "borderLeftWidth", !0)),
                {
                    top: e.top - i.top - le.css(n, "marginTop", !0),
                    left: e.left - i.left - le.css(n, "marginLeft", !0)
                }
            }
        },
        offsetParent: function() {
            return this.map(function() {
                for (var t = this.offsetParent || $.documentElement; t && !le.nodeName(t, "html") && "static" === le.css(t, "position");) t = t.offsetParent;
                return t || $.documentElement
            })
        }
    }),
    le.each({
        scrollLeft: "pageXOffset",
        scrollTop: "pageYOffset"
    },
    function(t, i) {
        var n = /Y/.test(i);
        le.fn[t] = function(r) {
            return le.access(this,
            function(t, r, o) {
                var s = Y(t);
                return o === e ? s ? i in s ? s[i] : s.document.documentElement[r] : t[r] : void(s ? s.scrollTo(n ? le(s).scrollLeft() : o, n ? o: le(s).scrollTop()) : t[r] = o)
            },
            t, r, arguments.length, null)
        }
    }),
    le.each({
        Height: "height",
        Width: "width"
    },
    function(t, i) {
        le.each({
            padding: "inner" + t,
            content: i,
            "": "outer" + t
        },
        function(n, r) {
            le.fn[r] = function(r, o) {
                var s = arguments.length && (n || "boolean" != typeof r),
                a = n || (r === !0 || o === !0 ? "margin": "border");
                return le.access(this,
                function(i, n, r) {
                    var o;
                    return le.isWindow(i) ? i.document.documentElement["client" + t] : 9 === i.nodeType ? (o = i.documentElement, Math.max(i.body["scroll" + t], o["scroll" + t], i.body["offset" + t], o["offset" + t], o["client" + t])) : r === e ? le.css(i, n, a) : le.style(i, n, r, a)
                },
                i, s ? r: e, s, null)
            }
        })
    }),
    t.jQuery = t.$ = le,
    "function" == typeof define && define.amd && define.amd.jQuery && define("jquery", [],
    function() {
        return le
    })
} (window),
function(t, e) {
    var i = function() {
        var e = t._data(document, "events");
        return e && e.click && t.grep(e.click,
        function(t) {
            return "rails" === t.namespace
        }).length
    };
    i() && t.error("jquery-ujs has already been loaded!");
    var n;
    t.rails = n = {
        linkClickSelector: "a[data-confirm], a[data-method], a[data-remote], a[data-disable-with]",
        inputChangeSelector: "select[data-remote], input[data-remote], textarea[data-remote]",
        formSubmitSelector: "form",
        formInputClickSelector: "form input[type=submit], form input[type=image], form button[type=submit], form button:not([type])",
        disableSelector: "input[data-disable-with], button[data-disable-with], textarea[data-disable-with]",
        enableSelector: "input[data-disable-with]:disabled, button[data-disable-with]:disabled, textarea[data-disable-with]:disabled",
        requiredInputSelector: "input[name][required]:not([disabled]),textarea[name][required]:not([disabled])",
        fileInputSelector: "input[type=file]",
        linkDisableSelector: "a[data-disable-with]",
        CSRFProtection: function(e) {
            var i = t('meta[name="csrf-token"]').attr("content");
            i && e.setRequestHeader("X-CSRF-Token", i)
        },
        fire: function(e, i, n) {
            var r = t.Event(i);
            return e.trigger(r, n),
            r.result !== !1
        },
        confirm: function(t) {
            return confirm(t)
        },
        ajax: function(e) {
            return t.ajax(e)
        },
        href: function(t) {
            return t.attr("href")
        },
        handleRemote: function(i) {
            var r, o, s, a, l, h, c, u;
            if (n.fire(i, "ajax:before")) {
                if (a = i.data("cross-domain"), l = a === e ? null: a, h = i.data("with-credentials") || null, c = i.data("type") || t.ajaxSettings && t.ajaxSettings.dataType, i.is("form")) {
                    r = i.attr("method"),
                    o = i.attr("action"),
                    s = i.serializeArray();
                    var d = i.data("ujs:submit-button");
                    d && (s.push(d), i.data("ujs:submit-button", null))
                } else i.is(n.inputChangeSelector) ? (r = i.data("method"), o = i.data("url"), s = i.serialize(), i.data("params") && (s = s + "&" + i.data("params"))) : (r = i.data("method"), o = n.href(i), s = i.data("params") || null);
                u = {
                    type: r || "GET",
                    data: s,
                    dataType: c,
                    beforeSend: function(t, r) {
                        return r.dataType === e && t.setRequestHeader("accept", "*/*;q=0.5, " + r.accepts.script),
                        n.fire(i, "ajax:beforeSend", [t, r])
                    },
                    success: function(t, e, n) {
                        i.trigger("ajax:success", [t, e, n])
                    },
                    complete: function(t, e) {
                        i.trigger("ajax:complete", [t, e])
                    },
                    error: function(t, e, n) {
                        i.trigger("ajax:error", [t, e, n])
                    },
                    crossDomain: l
                },
                h && (u.xhrFields = {
                    withCredentials: h
                }),
                o && (u.url = o);
                var p = n.ajax(u);
                return i.trigger("ajax:send", p),
                p
            }
            return ! 1
        },
        handleMethod: function(i) {
            var r = n.href(i),
            o = i.data("method"),
            s = i.attr("target"),
            a = t("meta[name=csrf-token]").attr("content"),
            l = t("meta[name=csrf-param]").attr("content"),
            h = t('<form method="post" action="' + r + '"></form>'),
            c = '<input name="_method" value="' + o + '" type="hidden" />';
            l !== e && a !== e && (c += '<input name="' + l + '" value="' + a + '" type="hidden" />'),
            s && h.attr("target", s),
            h.hide().append(c).appendTo("body"),
            h.submit()
        },
        disableFormElements: function(e) {
            e.find(n.disableSelector).each(function() {
                var e = t(this),
                i = e.is("button") ? "html": "val";
                e.data("ujs:enable-with", e[i]()),
                e[i](e.data("disable-with")),
                e.prop("disabled", !0)
            })
        },
        enableFormElements: function(e) {
            e.find(n.enableSelector).each(function() {
                var e = t(this),
                i = e.is("button") ? "html": "val";
                e.data("ujs:enable-with") && e[i](e.data("ujs:enable-with")),
                e.prop("disabled", !1)
            })
        },
        allowAction: function(t) {
            var e, i = t.data("confirm"),
            r = !1;
            return i ? (n.fire(t, "confirm") && (r = n.confirm(i), e = n.fire(t, "confirm:complete", [r])), r && e) : !0
        },
        blankInputs: function(e, i, n) {
            var r, o, s = t(),
            a = i || "input,textarea",
            l = e.find(a);
            return l.each(function() {
                if (r = t(this), o = r.is("input[type=checkbox],input[type=radio]") ? r.is(":checked") : r.val(), !o == !n) {
                    if (r.is("input[type=radio]") && l.filter('input[type=radio]:checked[name="' + r.attr("name") + '"]').length) return ! 0;
                    s = s.add(r)
                }
            }),
            s.length ? s: !1
        },
        nonBlankInputs: function(t, e) {
            return n.blankInputs(t, e, !0)
        },
        stopEverything: function(e) {
            return t(e.target).trigger("ujs:everythingStopped"),
            e.stopImmediatePropagation(),
            !1
        },
        callFormSubmitBindings: function(i, n) {
            var r = i.data("events"),
            o = !0;
            return r !== e && r.submit !== e && t.each(r.submit,
            function(t, e) {
                return "function" == typeof e.handler ? o = e.handler(n) : void 0
            }),
            o
        },
        disableElement: function(t) {
            t.data("ujs:enable-with", t.html()),
            t.html(t.data("disable-with")),
            t.bind("click.railsDisable",
            function(t) {
                return n.stopEverything(t)
            })
        },
        enableElement: function(t) {
            t.data("ujs:enable-with") !== e && (t.html(t.data("ujs:enable-with")), t.data("ujs:enable-with", !1)),
            t.unbind("click.railsDisable")
        }
    },
    n.fire(t(document), "rails:attachBindings") && (t.ajaxPrefilter(function(t, e, i) {
        t.crossDomain || n.CSRFProtection(i)
    }), t(document).delegate(n.linkDisableSelector, "ajax:complete",
    function() {
        n.enableElement(t(this))
    }), t(document).delegate(n.linkClickSelector, "click.rails",
    function(i) {
        var r = t(this),
        o = r.data("method"),
        s = r.data("params");
        if (!n.allowAction(r)) return n.stopEverything(i);
        if (r.is(n.linkDisableSelector) && n.disableElement(r), r.data("remote") !== e) {
            if (! (!i.metaKey && !i.ctrlKey || o && "GET" !== o || s)) return ! 0;
            var a = n.handleRemote(r);
            return a === !1 ? n.enableElement(r) : a.error(function() {
                n.enableElement(r)
            }),
            !1
        }
        return r.data("method") ? (n.handleMethod(r), !1) : void 0
    }), t(document).delegate(n.inputChangeSelector, "change.rails",
    function(e) {
        var i = t(this);
        return n.allowAction(i) ? (n.handleRemote(i), !1) : n.stopEverything(e)
    }), t(document).delegate(n.formSubmitSelector, "submit.rails",
    function(i) {
        var r = t(this),
        o = r.data("remote") !== e,
        s = n.blankInputs(r, n.requiredInputSelector),
        a = n.nonBlankInputs(r, n.fileInputSelector);
        if (!n.allowAction(r)) return n.stopEverything(i);
        if (s && r.attr("novalidate") == e && n.fire(r, "ajax:aborted:required", [s])) return n.stopEverything(i);
        if (o) {
            if (a) {
                setTimeout(function() {
                    n.disableFormElements(r)
                },
                13);
                var l = n.fire(r, "ajax:aborted:file", [a]);
                return l || setTimeout(function() {
                    n.enableFormElements(r)
                },
                13),
                l
            }
            return ! t.support.submitBubbles && t().jquery < "1.7" && n.callFormSubmitBindings(r, i) === !1 ? n.stopEverything(i) : (n.handleRemote(r), !1)
        }
        setTimeout(function() {
            n.disableFormElements(r)
        },
        13)
    }), t(document).delegate(n.formInputClickSelector, "click.rails",
    function(e) {
        var i = t(this);
        if (!n.allowAction(i)) return n.stopEverything(e);
        var r = i.attr("name"),
        o = r ? {
            name: r,
            value: i.val()
        }: null;
        i.closest("form").data("ujs:submit-button", o)
    }), t(document).delegate(n.formSubmitSelector, "ajax:beforeSend.rails",
    function(e) {
        this == e.target && n.disableFormElements(t(this))
    }), t(document).delegate(n.formSubmitSelector, "ajax:complete.rails",
    function(e) {
        this == e.target && n.enableFormElements(t(this))
    }), t(function() {
        var e = t("meta[name=csrf-token]").attr("content"),
        i = t("meta[name=csrf-param]").attr("content");
        t('form input[name="' + i + '"]').val(e)
    }))
} (jQuery),
function(t) {
    t.fn.bxSlider = function(e) {
        var i = {
            alignment: "horizontal",
            controls: !0,
            speed: 500,
            pager: !0,
            margin: 0,
            next_text: "next",
            next_image: "",
            prev_text: "prev",
            prev_image: "",
            auto: !1,
            pause: 3500,
            auto_direction: "next",
            auto_hover: !0,
            auto_controls: !1,
            ticker: !1,
            ticker_controls: !1,
            ticker_direction: "next",
            ticker_hover: !0,
            stop_text: "stop",
            start_text: "start",
            wrapper_class: "bxslider_wrap"
        },
        n = t.extend(i, e);
        return this.each(function() {
            function e() {
                "next" == n.ticker_direction && "horizontal" == n.alignment ? o.animate({
                    left: "-=5px"
                },
                n.speed / 5, "linear",
                function() {
                    parseInt(o.css("left")) <= -((a + 1) * c) && o.css("left", -c),
                    e()
                }) : "prev" == n.ticker_direction && "horizontal" == n.alignment ? o.animate({
                    left: "+=5px"
                },
                n.speed / 5, "linear",
                function() {
                    parseInt(o.css("left")) >= -c && o.css("left", -((a + 1) * c)),
                    e()
                }) : "next" == n.ticker_direction && "vertical" == n.alignment ? o.animate({
                    top: "-=5px"
                },
                n.speed / 5, "linear",
                function() {
                    parseInt(o.css("top")) <= -((a + 1) * (u + n.margin)) && o.css("top", -(u + n.margin)),
                    e()
                }) : "prev" == n.ticker_direction && "vertical" == n.alignment && o.animate({
                    top: "+=4px"
                },
                n.speed / 5, "linear",
                function() {
                    parseInt(o.css("top")) > -(u + n.margin) && o.css("top", -((a + 1) * (u + n.margin - 1))),
                    e()
                })
            }
            function i(t) {
                n.ticker && (v = "linear"),
                f || ("horizontal" == n.alignment ? (p = c, x = "left") : "vertical" == n.alignment && (p = u + n.margin, x = "top"), d = t * p, k[x] = -d, f = !0, o.animate(k, n.speed, v,
                function() {
                    f = !1,
                    y > a ? (o.css(x, -p), y = 1) : 1 > y && (o.css(x, -(p * a)), y = a),
                    r(y)
                }))
            }
            function r(e) {
                n.pager && t(".bx_pager a").removeClass("active").eq(e - 1).addClass("active")
            }
            var o = t(this),
            s = o.children(),
            a = o.children().length,
            l = o.children(":first").clone(),
            h = o.children(":last").clone(),
            c = 0,
            u = 0,
            d = 0,
            p = 0,
            f = !1,
            g = !0,
            m = !0,
            y = 1,
            v = "swing",
            x = "",
            b = "",
            k = {};
            if (o.append(l).prepend(h), o.wrap('<div class="bxslider_container"></div>'), o.parent().wrap('<div class="' + n.wrapper_class + '"></div>'), "horizontal" == n.alignment ? (o.children().css({
                "float": "left",
                listStyle: "none",
                marginRight: n.margin
            }), c = l.outerWidth(!0), o.css({
                width: "99999px",
                position: "relative",
                left: -c
            }), o.parent().css({
                position: "relative",
                overflow: "hidden",
                width: c - n.margin
            })) : "vertical" == n.alignment && (s.each(function() {
                t(this).height() > u && (u = t(this).height())
            }), c = l.outerWidth(), o.children().css({
                height: u,
                listStyle: "none",
                marginBottom: n.margin
            }), o.css({
                height: "99999px",
                width: c,
                position: "relative",
                top: -(u + n.margin)
            }), o.parent().css({
                position: "relative",
                overflow: "hidden",
                height: u
            })), n.pager && !n.ticker) {
                o.parent().after('<div class="bx_pager"></div>');
                var w;
                s.each(function(e) {
                    w = t('<a href="#">' + (e + 1) + "</a>"),
                    o.parent().siblings(".bx_pager").append(w),
                    w.click(function() {
                        return f = !1,
                        m = !1,
                        o.stop(),
                        i(e + 1),
                        y = e + 1,
                        n.auto ? (clearInterval(b), o.parent().siblings(".auto_controls").find("a").html(n.start_text), g = !1) : n.ticker && (o.parent().siblings(".ticker_controls").find("a").html(n.start_text), g = !1),
                        !1
                    })
                }),
                r(1)
            }
            if (n.controls && !n.ticker && (o.parent().after("" != n.next_image || "" != n.prev_image ? '<a class="prev" href="#"><img src="' + n.prev_image + '" /></a><a class="next" href="#"><img src="' + n.next_image + '" /></a>': '<a class="prev" href="#">' + n.prev_text + '</a><a class="next" href="#">' + n.next_text + "</a>"), o.parent().siblings(".next").click(function() {
                return f || i(++y),
                n.auto && (clearInterval(b), o.parent().siblings(".auto_controls").find("a").html(n.start_text), g = !1),
                !1
            }), o.parent().siblings(".prev").click(function() {
                return f || i(--y),
                n.auto && (clearInterval(b), o.parent().siblings(".auto_controls").find("a").html(n.start_text), g = !1),
                !1
            })), n.auto && !n.ticker && (b = setInterval(function() {
                i("next" == n.auto_direction ? ++y: --y)
            },
            n.pause), n.auto_hover && o.hover(function() {
                clearInterval(b)
            },
            function() {
                g && (b = setInterval(function() {
                    i("next" == n.auto_direction ? ++y: --y)
                },
                n.pause))
            }), n.auto_controls && (o.parent().after('<div class="auto_controls"><a class="auto_link" href="#">' + n.stop_text + "</a></div>"), o.parent().siblings(".auto_controls").find("a").click(function() {
                return g ? (clearInterval(b), t(this).html(n.start_text), g = !1) : (b = setInterval(function() {
                    i("next" == n.auto_direction ? ++y: --y)
                },
                n.pause), t(this).html(n.stop_text), g = !0),
                !1
            }))), n.ticker) {
                var m = !0;
                e(),
                o.hover(function() {
                    o.stop()
                },
                function() {
                    m && e()
                }),
                n.ticker_controls && (o.parent().after('<div class="ticker_controls"><a class="ticker_link" href="#">' + n.stop_text + "</a></div>"), o.parent().siblings(".ticker_controls").find("a").click(function() {
                    return m ? (o.stop(), t(this).html(n.start_text), m = !1) : (f = !1, t(this).html(n.stop_text), e(), m = !0),
                    !1
                }))
            }
        })
    }
} (jQuery),
function() {
    function t(t, e) {
        var i;
        t || (t = {});
        for (i in e) t[i] = e[i];
        return t
    }
    function e() {
        var t, e = arguments.length,
        i = {},
        n = function(t, e) {
            var i, r;
            "object" != typeof t && (t = {});
            for (r in e) e.hasOwnProperty(r) && (i = e[r], t[r] = i && "object" == typeof i && "[object Array]" !== Object.prototype.toString.call(i) && "number" != typeof i.nodeType ? n(t[r] || {},
            i) : e[r]);
            return t
        };
        for (t = 0; e > t; t++) i = n(i, arguments[t]);
        return i
    }
    function i(t, e) {
        return parseInt(t, e || 10)
    }
    function n(t) {
        return "string" == typeof t
    }
    function r(t) {
        return "object" == typeof t
    }
    function o(t) {
        return "[object Array]" === Object.prototype.toString.call(t)
    }
    function s(t) {
        return "number" == typeof t
    }
    function a(t) {
        return de.log(t) / de.LN10
    }
    function l(t) {
        return de.pow(10, t)
    }
    function h(t, e) {
        for (var i = t.length; i--;) if (t[i] === e) {
            t.splice(i, 1);
            break
        }
    }
    function c(t) {
        return t !== Y && null !== t
    }
    function u(t, e, i) {
        var o, s;
        if (n(e)) c(i) ? t.setAttribute(e, i) : t && t.getAttribute && (s = t.getAttribute(e));
        else if (c(e) && r(e)) for (o in e) t.setAttribute(o, e[o]);
        return s
    }
    function d(t) {
        return o(t) ? t: [t]
    }
    function p() {
        var t, e, i = arguments,
        n = i.length;
        for (t = 0; n > t; t++) if (e = i[t], "undefined" != typeof e && null !== e) return e
    }
    function f(e, i) {
        Ce && i && i.opacity !== Y && (i.filter = "alpha(opacity=" + 100 * i.opacity + ")"),
        t(e.style, i)
    }
    function g(e, i, n, r, o) {
        return e = ce.createElement(e),
        i && t(e, i),
        o && f(e, {
            padding: 0,
            border: We,
            margin: 0
        }),
        n && f(e, n),
        r && r.appendChild(e),
        e
    }
    function m(e, i) {
        var n = function() {};
        return n.prototype = new e,
        t(n.prototype, i),
        n
    }
    function y(t, e, n, r) {
        var o = V.lang,
        t = +t || 0,
        s = -1 === e ? (t.toString().split(".")[1] || "").length: isNaN(e = ve(e)) ? 2 : e,
        e = void 0 === n ? o.decimalPoint: n,
        r = void 0 === r ? o.thousandsSep: r,
        o = 0 > t ? "-": "",
        n = String(i(t = ve(t).toFixed(s))),
        a = n.length > 3 ? n.length % 3 : 0;
        return o + (a ? n.substr(0, a) + r: "") + n.substr(a).replace(/(\d{3})(?=\d)/g, "$1" + r) + (s ? e + ve(t - n).toFixed(s).slice(2) : "")
    }
    function v(t, e) {
        return Array((e || 2) + 1 - String(t).length).join(0) + t
    }
    function x(t, e, i) {
        var n = t[e];
        t[e] = function() {
            var t = Array.prototype.slice.call(arguments);
            return t.unshift(n),
            i.apply(this, t)
        }
    }
    function b(t, e) {
        for (var i, n, r, o, s, a = "{",
        l = !1,
        h = []; - 1 !== (a = t.indexOf(a));) {
            if (i = t.slice(0, a), l) {
                for (n = i.split(":"), r = n.shift().split("."), s = r.length, i = e, o = 0; s > o; o++) i = i[r[o]];
                n.length && (n = n.join(":"), r = /\.([0-9])/, o = V.lang, s = void 0, /f$/.test(n) ? (s = (s = n.match(r)) ? s[1] : -1, i = y(i, s, o.decimalPoint, n.indexOf(",") > -1 ? o.thousandsSep: "")) : i = $(n, i))
            }
            h.push(i),
            t = t.slice(a + 1),
            a = (l = !l) ? "}": "{"
        }
        return h.push(t),
        h.join("")
    }
    function k(t) {
        return de.pow(10, fe(de.log(t) / de.LN10))
    }
    function w(t, e, i, n) {
        var r, i = p(i, 1);
        for (r = t / i, e || (e = [1, 2, 2.5, 5, 10], n && n.allowDecimals === !1 && (1 === i ? e = [1, 2, 5, 10] : .1 >= i && (e = [1 / i]))), n = 0; n < e.length && (t = e[n], !(r <= (e[n] + (e[n + 1] || e[n])) / 2)); n++);
        return t *= i
    }
    function T(t, e) {
        var i, n = e || [[_e, [1, 2, 5, 10, 20, 25, 50, 100, 200, 500]], [Xe, [1, 2, 5, 10, 15, 30]], [Ye, [1, 2, 5, 10, 15, 30]], [Ge, [1, 2, 3, 4, 6, 8, 12]], [qe, [1, 2]], [Ve, [1, 2]], [$e, [1, 2, 3, 4, 6]], [Ue, null]],
        r = n[n.length - 1],
        o = K[r[0]],
        s = r[1];
        for (i = 0; i < n.length && (r = n[i], o = K[r[0]], s = r[1], !(n[i + 1] && t <= (o * s[s.length - 1] + K[n[i + 1][0]]) / 2)); i++);
        return o === K[Ue] && 5 * o > t && (s = [1, 2, 5]),
        n = w(t / o, s, r[0] === Ue ? me(k(t / o), 1) : 1),
        {
            unitRange: o,
            count: n,
            unitName: r[0]
        }
    }
    function S(e, i, n, r) {
        var o, s = [],
        a = {},
        l = V.global.useUTC,
        h = new Date(i),
        u = e.unitRange,
        d = e.count;
        if (c(i)) {
            u >= K[Xe] && (h.setMilliseconds(0), h.setSeconds(u >= K[Ye] ? 0 : d * fe(h.getSeconds() / d))),
            u >= K[Ye] && h[oe](u >= K[Ge] ? 0 : d * fe(h[Q]() / d)),
            u >= K[Ge] && h[se](u >= K[qe] ? 0 : d * fe(h[te]() / d)),
            u >= K[qe] && h[ae](u >= K[$e] ? 1 : d * fe(h[ie]() / d)),
            u >= K[$e] && (h[le](u >= K[Ue] ? 0 : d * fe(h[ne]() / d)), o = h[re]()),
            u >= K[Ue] && (o -= o % d, h[he](o)),
            u === K[Ve] && h[ae](h[ie]() - h[ee]() + p(r, 1)),
            i = 1,
            o = h[re]();
            for (var r = h.getTime(), f = h[ne](), g = h[ie](), m = l ? 0 : (864e5 + 6e4 * h.getTimezoneOffset()) % 864e5; n > r;) s.push(r),
            u === K[Ue] ? r = J(o + i * d, 0) : u === K[$e] ? r = J(o, f + i * d) : l || u !== K[qe] && u !== K[Ve] ? r += u * d: r = J(o, f, g + i * d * (u === K[qe] ? 1 : 7)),
            i++;
            s.push(r),
            ni(ri(s,
            function(t) {
                return u <= K[Ge] && t % K[qe] === m
            }),
            function(t) {
                a[t] = qe
            })
        }
        return s.info = t(e, {
            higherRanks: a,
            totalRange: u * d
        }),
        s
    }
    function C() {
        this.symbol = this.color = 0
    }
    function A(t, e) {
        var i, n, r = t.length;
        for (n = 0; r > n; n++) t[n].ss_i = n;
        for (t.sort(function(t, n) {
            return i = e(t, n),
            0 === i ? t.ss_i - n.ss_i: i
        }), n = 0; r > n; n++) delete t[n].ss_i
    }
    function L(t) {
        for (var e = t.length,
        i = t[0]; e--;) t[e] < i && (i = t[e]);
        return i
    }
    function M(t) {
        for (var e = t.length,
        i = t[0]; e--;) t[e] > i && (i = t[e]);
        return i
    }
    function P(t, e) {
        for (var i in t) t[i] && t[i] !== e && t[i].destroy && t[i].destroy(),
        delete t[i]
    }
    function D(t) {
        q || (q = g(je)),
        t && q.appendChild(t),
        q.innerHTML = ""
    }
    function N(t, e) {
        var i = "Highcharts error #" + t + ": www.highcharts.com/errors/" + t;
        if (e) throw i;
        ue.console && console.log(i)
    }
    function E(t) {
        return parseFloat(t.toPrecision(14))
    }
    function H(t, e) {
        U = p(t, e.animation)
    }
    function I() {
        var t = V.global.useUTC,
        e = t ? "getUTC": "get",
        i = t ? "setUTC": "set";
        J = t ? Date.UTC: function(t, e, i, n, r, o) {
            return new Date(t, e, p(i, 1), p(n, 0), p(r, 0), p(o, 0)).getTime()
        },
        Q = e + "Minutes",
        te = e + "Hours",
        ee = e + "Day",
        ie = e + "Date",
        ne = e + "Month",
        re = e + "FullYear",
        oe = i + "Minutes",
        se = i + "Hours",
        ae = i + "Date",
        le = i + "Month",
        he = i + "FullYear"
    }
    function B() {}
    function O(t, e, i, n) {
        this.axis = t,
        this.pos = e,
        this.type = i || "",
        this.isNew = !0,
        !i && !n && this.addLabel()
    }
    function z(t, e) {
        this.axis = t,
        e && (this.options = e, this.id = e.id)
    }
    function R(t, e, i, n, r, o) {
        var s = t.chart.inverted;
        this.axis = t,
        this.isNegative = i,
        this.options = e,
        this.x = n,
        this.total = null,
        this.points = {},
        this.stack = r,
        this.percent = "percent" === o,
        this.alignOptions = {
            align: e.align || (s ? i ? "left": "right": "center"),
            verticalAlign: e.verticalAlign || (s ? "middle": i ? "bottom": "top"),
            y: p(e.y, s ? 4 : i ? 14 : -6),
            x: p(e.x, s ? i ? -6 : 6 : 0)
        },
        this.textAlign = e.textAlign || (s ? i ? "right": "left": "center")
    }
    function j() {
        this.init.apply(this, arguments)
    }
    function W() {
        this.init.apply(this, arguments)
    }
    function F(t, e) {
        this.init(t, e)
    }
    function _(t, e) {
        this.init(t, e)
    }
    function X() {
        this.init.apply(this, arguments)
    }
    var Y, G, q, V, $, U, Z, K, J, Q, te, ee, ie, ne, re, oe, se, ae, le, he, ce = document,
    ue = window,
    de = Math,
    pe = de.round,
    fe = de.floor,
    ge = de.ceil,
    me = de.max,
    ye = de.min,
    ve = de.abs,
    xe = de.cos,
    be = de.sin,
    ke = de.PI,
    we = 2 * ke / 360,
    Te = navigator.userAgent,
    Se = ue.opera,
    Ce = /msie/i.test(Te) && !Se,
    Ae = 8 === ce.documentMode,
    Le = /AppleWebKit/.test(Te),
    Me = /Firefox/.test(Te),
    Pe = /(Mobile|Android|Windows Phone)/.test(Te),
    De = "http://www.w3.org/2000/svg",
    Ne = !!ce.createElementNS && !!ce.createElementNS(De, "svg").createSVGRect,
    Ee = Me && parseInt(Te.split("Firefox/")[1], 10) < 4,
    He = !Ne && !Ce && !!ce.createElement("canvas").getContext,
    Ie = ce.documentElement.ontouchstart !== Y,
    Be = {},
    Oe = 0,
    ze = function() {},
    Re = [],
    je = "div",
    We = "none",
    Fe = "rgba(192,192,192," + (Ne ? 1e-4: .002) + ")",
    _e = "millisecond",
    Xe = "second",
    Ye = "minute",
    Ge = "hour",
    qe = "day",
    Ve = "week",
    $e = "month",
    Ue = "year",
    Ze = "stroke-width",
    Ke = {};
    ue.Highcharts = ue.Highcharts ? N(16, !0) : {},
    $ = function(e, i, n) {
        if (!c(i) || isNaN(i)) return "Invalid date";
        var r, e = p(e, "%Y-%m-%d %H:%M:%S"),
        o = new Date(i),
        s = o[te](),
        a = o[ee](),
        l = o[ie](),
        h = o[ne](),
        u = o[re](),
        d = V.lang,
        f = d.weekdays,
        o = t({
            a: f[a].substr(0, 3),
            A: f[a],
            d: v(l),
            e: l,
            b: d.shortMonths[h],
            B: d.months[h],
            m: v(h + 1),
            y: u.toString().substr(2, 2),
            Y: u,
            H: v(s),
            I: v(s % 12 || 12),
            l: s % 12 || 12,
            M: v(o[Q]()),
            p: 12 > s ? "AM": "PM",
            P: 12 > s ? "am": "pm",
            S: v(o.getSeconds()),
            L: v(pe(i % 1e3), 3)
        },
        Highcharts.dateFormats);
        for (r in o) for (; - 1 !== e.indexOf("%" + r);) e = e.replace("%" + r, "function" == typeof o[r] ? o[r](i) : o[r]);
        return n ? e.substr(0, 1).toUpperCase() + e.substr(1) : e
    },
    C.prototype = {
        wrapColor: function(t) {
            this.color >= t && (this.color = 0)
        },
        wrapSymbol: function(t) {
            this.symbol >= t && (this.symbol = 0)
        }
    },
    K = function() {
        for (var t = 0,
        e = arguments,
        i = e.length,
        n = {}; i > t; t++) n[e[t++]] = e[t];
        return n
    } (_e, 1, Xe, 1e3, Ye, 6e4, Ge, 36e5, qe, 864e5, Ve, 6048e5, $e, 26784e5, Ue, 31556952e3),
    Z = {
        init: function(t, e, i) {
            var n, r, o, e = e || "",
            s = t.shift,
            a = e.indexOf("C") > -1,
            l = a ? 7 : 3,
            e = e.split(" "),
            i = [].concat(i),
            h = function(t) {
                for (n = t.length; n--;)"M" === t[n] && t.splice(n + 1, 0, t[n + 1], t[n + 2], t[n + 1], t[n + 2])
            };
            if (a && (h(e), h(i)), t.isArea && (r = e.splice(e.length - 6, 6), o = i.splice(i.length - 6, 6)), s <= i.length / l && e.length === i.length) for (; s--;) i = [].concat(i).splice(0, l).concat(i);
            if (t.shift = 0, e.length) for (t = i.length; e.length < t;) s = [].concat(e).splice(e.length - l, l),
            a && (s[l - 6] = s[l - 2], s[l - 5] = s[l - 1]),
            e = e.concat(s);
            return r && (e = e.concat(r), i = i.concat(o)),
            [e, i]
        },
        step: function(t, e, i, n) {
            var r = [],
            o = t.length;
            if (1 === i) r = n;
            else if (o === e.length && 1 > i) for (; o--;) n = parseFloat(t[o]),
            r[o] = isNaN(n) ? t[o] : i * parseFloat(e[o] - n) + n;
            else r = e;
            return r
        }
    },
    function(e) {
        ue.HighchartsAdapter = ue.HighchartsAdapter || e && {
            init: function(t) {
                var i, r = e.fx,
                o = r.step,
                s = e.Tween,
                a = s && s.propHooks;
                i = e.cssHooks.opacity,
                e.extend(e.easing, {
                    easeOutQuad: function(t, e, i, n, r) {
                        return - n * (e /= r) * (e - 2) + i
                    }
                }),
                e.each(["cur", "_default", "width", "height", "opacity"],
                function(t, e) {
                    var i, n, l = o;
                    "cur" === e ? l = r.prototype: "_default" === e && s && (l = a[e], e = "set"),
                    (i = l[e]) && (l[e] = function(r) {
                        return r = t ? r: this,
                        "align" !== r.prop ? (n = r.elem, n.attr ? n.attr(r.prop, "cur" === e ? Y: r.now) : i.apply(this, arguments)) : void 0
                    })
                }),
                x(i, "get",
                function(t, e, i) {
                    return e.attr ? e.opacity || 0 : t.call(this, e, i)
                }),
                i = function(e) {
                    var i, n = e.elem;
                    e.started || (i = t.init(n, n.d, n.toD), e.start = i[0], e.end = i[1], e.started = !0),
                    n.attr("d", t.step(e.start, e.end, e.pos, n.toD))
                },
                s ? a.d = {
                    set: i
                }: o.d = i,
                this.each = Array.prototype.forEach ?
                function(t, e) {
                    return Array.prototype.forEach.call(t, e)
                }: function(t, e) {
                    for (var i = 0,
                    n = t.length; n > i; i++) if (e.call(t[i], t[i], i, t) === !1) return i
                },
                e.fn.highcharts = function() {
                    var t, e, i = "Chart",
                    r = arguments;
                    return n(r[0]) && (i = r[0], r = Array.prototype.slice.call(r, 1)),
                    t = r[0],
                    t !== Y && (t.chart = t.chart || {},
                    t.chart.renderTo = this[0], new Highcharts[i](t, r[1]), e = this),
                    t === Y && (e = Re[u(this[0], "data-highcharts-chart")]),
                    e
                }
            },
            getScript: e.getScript,
            inArray: e.inArray,
            adapterRun: function(t, i) {
                return e(t)[i]()
            },
            grep: e.grep,
            map: function(t, e) {
                for (var i = [], n = 0, r = t.length; r > n; n++) i[n] = e.call(t[n], t[n], n, t);
                return i
            },
            offset: function(t) {
                return e(t).offset()
            },
            addEvent: function(t, i, n) {
                e(t).bind(i, n)
            },
            removeEvent: function(t, i, n) {
                var r = ce.removeEventListener ? "removeEventListener": "detachEvent";
                ce[r] && t && !t[r] && (t[r] = function() {}),
                e(t).unbind(i, n)
            },
            fireEvent: function(i, n, r, o) {
                var s, a = e.Event(n),
                l = "detached" + n; ! Ce && r && (delete r.layerX, delete r.layerY),
                t(a, r),
                i[n] && (i[l] = i[n], i[n] = null),
                e.each(["preventDefault", "stopPropagation"],
                function(t, e) {
                    var i = a[e];
                    a[e] = function() {
                        try {
                            i.call(a)
                        } catch(t) {
                            "preventDefault" === e && (s = !0)
                        }
                    }
                }),
                e(i).trigger(a),
                i[l] && (i[n] = i[l], i[l] = null),
                o && !a.isDefaultPrevented() && !s && o(a)
            },
            washMouseEvent: function(t) {
                var e = t.originalEvent || t;
                return e.pageX === Y && (e.pageX = t.pageX, e.pageY = t.pageY),
                e
            },
            animate: function(t, i, n) {
                var r = e(t);
                t.style || (t.style = {}),
                i.d && (t.toD = i.d, i.d = 1),
                r.stop(),
                i.opacity !== Y && t.attr && (i.opacity += "px"),
                r.animate(i, n)
            },
            stop: function(t) {
                e(t).stop()
            }
        }
    } (ue.jQuery);
    var Je = ue.HighchartsAdapter,
    Qe = Je || {};
    Je && Je.init.call(Je, Z);
    var ti = Qe.adapterRun,
    ei = Qe.getScript,
    ii = Qe.inArray,
    ni = Qe.each,
    ri = Qe.grep,
    oi = Qe.offset,
    si = Qe.map,
    ai = Qe.addEvent,
    li = Qe.removeEvent,
    hi = Qe.fireEvent,
    ci = Qe.washMouseEvent,
    ui = Qe.animate,
    di = Qe.stop,
    Qe = {
        enabled: !0,
        x: 0,
        y: 15,
        style: {
            color: "#666",
            cursor: "default",
            fontSize: "11px",
            lineHeight: "14px"
        }
    };
    V = {
        colors: "#2f7ed8,#0d233a,#8bbc21,#910000,#1aadce,#492970,#f28f43,#77a1e5,#c42525,#a6c96a".split(","),
        symbols: ["circle", "diamond", "square", "triangle", "triangle-down"],
        lang: {
            loading: "Loading...",
            months: "January,February,March,April,May,June,July,August,September,October,November,December".split(","),
            shortMonths: "Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec".split(","),
            weekdays: "Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday".split(","),
            decimalPoint: ".",
            numericSymbols: "k,M,G,T,P,E".split(","),
            resetZoom: "Reset zoom",
            resetZoomTitle: "Reset zoom level 1:1",
            thousandsSep: ","
        },
        global: {
            useUTC: !0,
            canvasToolsURL: "http://code.highcharts.com/3.0.7/modules/canvas-tools.js",
            VMLRadialGradientURL: "http://code.highcharts.com/3.0.7/gfx/vml-radial-gradient.png"
        },
        chart: {
            borderColor: "#4572A7",
            borderRadius: 5,
            defaultSeriesType: "line",
            ignoreHiddenSeries: !0,
            spacing: [10, 10, 15, 10],
            style: {
                fontFamily: '"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif',
                fontSize: "12px"
            },
            backgroundColor: "#FFFFFF",
            plotBorderColor: "#C0C0C0",
            resetZoomButton: {
                theme: {
                    zIndex: 20
                },
                position: {
                    align: "right",
                    x: -10,
                    y: 10
                }
            }
        },
        title: {
            text: "Chart title",
            align: "center",
            margin: 15,
            style: {
                color: "#274b6d",
                fontSize: "16px"
            }
        },
        subtitle: {
            text: "",
            align: "center",
            style: {
                color: "#4d759e"
            }
        },
        plotOptions: {
            line: {
                allowPointSelect: !1,
                showCheckbox: !1,
                animation: {
                    duration: 1e3
                },
                events: {},
                lineWidth: 2,
                marker: {
                    enabled: !0,
                    lineWidth: 0,
                    radius: 4,
                    lineColor: "#FFFFFF",
                    states: {
                        hover: {
                            enabled: !0
                        },
                        select: {
                            fillColor: "#FFFFFF",
                            lineColor: "#000000",
                            lineWidth: 2
                        }
                    }
                },
                point: {
                    events: {}
                },
                dataLabels: e(Qe, {
                    align: "center",
                    enabled: !1,
                    formatter: function() {
                        return null === this.y ? "": y(this.y, -1)
                    },
                    verticalAlign: "bottom",
                    y: 0
                }),
                cropThreshold: 300,
                pointRange: 0,
                states: {
                    hover: {
                        marker: {}
                    },
                    select: {
                        marker: {}
                    }
                },
                stickyTracking: !0
            }
        },
        labels: {
            style: {
                position: "absolute",
                color: "#3E576F"
            }
        },
        legend: {
            enabled: !0,
            align: "center",
            layout: "horizontal",
            labelFormatter: function() {
                return this.name
            },
            borderWidth: 1,
            borderColor: "#909090",
            borderRadius: 5,
            navigation: {
                activeColor: "#274b6d",
                inactiveColor: "#CCC"
            },
            shadow: !1,
            itemStyle: {
                cursor: "pointer",
                color: "#274b6d",
                fontSize: "12px"
            },
            itemHoverStyle: {
                color: "#000"
            },
            itemHiddenStyle: {
                color: "#CCC"
            },
            itemCheckboxStyle: {
                position: "absolute",
                width: "13px",
                height: "13px"
            },
            symbolWidth: 16,
            symbolPadding: 5,
            verticalAlign: "bottom",
            x: 0,
            y: 0,
            title: {
                style: {
                    fontWeight: "bold"
                }
            }
        },
        loading: {
            labelStyle: {
                fontWeight: "bold",
                position: "relative",
                top: "1em"
            },
            style: {
                position: "absolute",
                backgroundColor: "white",
                opacity: .5,
                textAlign: "center"
            }
        },
        tooltip: {
            enabled: !0,
            animation: Ne,
            backgroundColor: "rgba(255, 255, 255, .85)",
            borderWidth: 1,
            borderRadius: 3,
            dateTimeLabelFormats: {
                millisecond: "%A, %b %e, %H:%M:%S.%L",
                second: "%A, %b %e, %H:%M:%S",
                minute: "%A, %b %e, %H:%M",
                hour: "%A, %b %e, %H:%M",
                day: "%A, %b %e, %Y",
                week: "Week from %A, %b %e, %Y",
                month: "%B %Y",
                year: "%Y"
            },
            headerFormat: '<span style="font-size: 10px">{point.key}</span><br/>',
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
            shadow: !0,
            snap: Pe ? 25 : 10,
            style: {
                color: "#333333",
                cursor: "default",
                fontSize: "12px",
                padding: "8px",
                whiteSpace: "nowrap"
            }
        },
        credits: {
            enabled: !0,
            text: "Highcharts.com",
            href: "http://www.highcharts.com",
            position: {
                align: "right",
                x: -10,
                verticalAlign: "bottom",
                y: -5
            },
            style: {
                cursor: "pointer",
                color: "#909090",
                fontSize: "9px"
            }
        }
    };
    var pi = V.plotOptions,
    Je = pi.line;
    I();
    var fi = /rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]?(?:\.[0-9]+)?)\s*\)/,
    gi = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/,
    mi = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/,
    yi = function(t) {
        var n, r, o = [];
        return function(t) {
            t && t.stops ? r = si(t.stops,
            function(t) {
                return yi(t[1])
            }) : (n = fi.exec(t)) ? o = [i(n[1]), i(n[2]), i(n[3]), parseFloat(n[4], 10)] : (n = gi.exec(t)) ? o = [i(n[1], 16), i(n[2], 16), i(n[3], 16), 1] : (n = mi.exec(t)) && (o = [i(n[1]), i(n[2]), i(n[3]), 1])
        } (t),
        {
            get: function(i) {
                var n;
                return r ? (n = e(t), n.stops = [].concat(n.stops), ni(r,
                function(t, e) {
                    n.stops[e] = [n.stops[e][0], t.get(i)]
                })) : n = o && !isNaN(o[0]) ? "rgb" === i ? "rgb(" + o[0] + "," + o[1] + "," + o[2] + ")": "a" === i ? o[3] : "rgba(" + o.join(",") + ")": t,
                n
            },
            brighten: function(t) {
                if (r) ni(r,
                function(e) {
                    e.brighten(t)
                });
                else if (s(t) && 0 !== t) {
                    var e;
                    for (e = 0; 3 > e; e++) o[e] += i(255 * t),
                    o[e] < 0 && (o[e] = 0),
                    o[e] > 255 && (o[e] = 255)
                }
                return this
            },
            rgba: o,
            setOpacity: function(t) {
                return o[3] = t,
                this
            }
        }
    };
    B.prototype = {
        init: function(t, e) {
            this.element = "span" === e ? g(e) : ce.createElementNS(De, e),
            this.renderer = t,
            this.attrSetters = {}
        },
        opacity: 1,
        animate: function(t, i, n) {
            i = p(i, U, !0),
            di(this),
            i ? (i = e(i), n && (i.complete = n), ui(this, t, i)) : (this.attr(t), n && n())
        },
        attr: function(t, e) {
            var r, o, s, a, l, h, d, f = this.element,
            g = f.nodeName.toLowerCase(),
            m = this.renderer,
            y = this.attrSetters,
            v = this.shadows,
            x = this;
            if (n(t) && c(e) && (r = t, t = {},
            t[r] = e), n(t)) r = t,
            "circle" === g ? r = {
                x: "cx",
                y: "cy"
            } [r] || r: "strokeWidth" === r && (r = "stroke-width"),
            x = u(f, r) || this[r] || 0,
            "d" !== r && "visibility" !== r && "fill" !== r && (x = parseFloat(x));
            else {
                for (r in t) if (l = !1, o = t[r], s = y[r] && y[r].call(this, o, r), s !== !1) {
                    if (s !== Y && (o = s), "d" === r) o && o.join && (o = o.join(" ")),
                    /(NaN| {2}|^$)/.test(o) && (o = "M 0 0");
                    else if ("x" === r && "text" === g) for (s = 0; s < f.childNodes.length; s++) a = f.childNodes[s],
                    u(a, "x") === u(f, "x") && u(a, "x", o);
                    else if (!this.rotation || "x" !== r && "y" !== r) if ("fill" === r) o = m.color(o, f, r);
                    else if ("circle" !== g || "x" !== r && "y" !== r) if ("rect" === g && "r" === r) u(f, {
                        rx: o,
                        ry: o
                    }),
                    l = !0;
                    else if ("translateX" === r || "translateY" === r || "rotation" === r || "verticalAlign" === r || "scaleX" === r || "scaleY" === r) l = d = !0;
                    else if ("stroke" === r) o = m.color(o, f, r);
                    else if ("dashstyle" === r) {
                        if (r = "stroke-dasharray", o = o && o.toLowerCase(), "solid" === o) o = We;
                        else if (o) {
                            for (o = o.replace("shortdashdotdot", "3,1,1,1,1,1,").replace("shortdashdot", "3,1,1,1").replace("shortdot", "1,1,").replace("shortdash", "3,1,").replace("longdash", "8,3,").replace(/dot/g, "1,3,").replace("dash", "4,3,").replace(/,$/, "").split(","), s = o.length; s--;) o[s] = i(o[s]) * p(t["stroke-width"], this["stroke-width"]);
                            o = o.join(",")
                        }
                    } else "width" === r ? o = i(o) : "align" === r ? (r = "text-anchor", o = {
                        left: "start",
                        center: "middle",
                        right: "end"
                    } [o]) : "title" === r && (s = f.getElementsByTagName("title")[0], s || (s = ce.createElementNS(De, "title"), f.appendChild(s)), s.textContent = o);
                    else r = {
                        x: "cx",
                        y: "cy"
                    } [r] || r;
                    else d = !0;
                    if ("strokeWidth" === r && (r = "stroke-width"), ("stroke-width" === r || "stroke" === r) && (this[r] = o, this.stroke && this["stroke-width"] ? (u(f, "stroke", this.stroke), u(f, "stroke-width", this["stroke-width"]), this.hasStroke = !0) : "stroke-width" === r && 0 === o && this.hasStroke && (f.removeAttribute("stroke"), this.hasStroke = !1), l = !0), this.symbolName && /^(x|y|width|height|r|start|end|innerR|anchorX|anchorY)/.test(r) && (h || (this.symbolAttr(t), h = !0), l = !0), v && /^(width|height|visibility|x|y|d|transform|cx|cy|r)$/.test(r)) for (s = v.length; s--;) u(v[s], r, "height" === r ? me(o - (v[s].cutHeight || 0), 0) : o); ("width" === r || "height" === r) && "rect" === g && 0 > o && (o = 0),
                    this[r] = o,
                    "text" === r ? (o !== this.textStr && delete this.bBox, this.textStr = o, this.added && m.buildText(this)) : l || u(f, r, o)
                }
                d && this.updateTransform()
            }
            return x
        },
        addClass: function(t) {
            var e = this.element,
            i = u(e, "class") || "";
            return - 1 === i.indexOf(t) && u(e, "class", i + " " + t),
            this
        },
        symbolAttr: function(t) {
            var e = this;
            ni("x,y,r,start,end,width,height,innerR,anchorX,anchorY".split(","),
            function(i) {
                e[i] = p(t[i], e[i])
            }),
            e.attr({
                d: e.renderer.symbols[e.symbolName](e.x, e.y, e.width, e.height, e)
            })
        },
        clip: function(t) {
            return this.attr("clip-path", t ? "url(" + this.renderer.url + "#" + t.id + ")": We)
        },
        crisp: function(t, e, i, n, r) {
            var o, s, a = {},
            l = {},
            t = t || this.strokeWidth || this.attr && this.attr("stroke-width") || 0;
            s = pe(t) % 2 / 2,
            l.x = fe(e || this.x || 0) + s,
            l.y = fe(i || this.y || 0) + s,
            l.width = fe((n || this.width || 0) - 2 * s),
            l.height = fe((r || this.height || 0) - 2 * s),
            l.strokeWidth = t;
            for (o in l) this[o] !== l[o] && (this[o] = a[o] = l[o]);
            return a
        },
        css: function(e) {
            var n, r = this.element,
            o = this.textWidth = e && e.width && "text" === r.nodeName.toLowerCase() && i(e.width),
            s = "",
            a = function(t, e) {
                return "-" + e.toLowerCase()
            };
            if (e && e.color && (e.fill = e.color), this.styles = e = t(this.styles, e), o && delete e.width, Ce && !Ne) f(this.element, e);
            else {
                for (n in e) s += n.replace(/([A-Z])/g, a) + ":" + e[n] + ";";
                u(r, "style", s)
            }
            return o && this.added && this.renderer.buildText(this),
            this
        },
        on: function(t, e) {
            var i = this,
            n = i.element;
            return Ie && "click" === t ? (n.ontouchstart = function(t) {
                i.touchEventFired = Date.now(),
                t.preventDefault(),
                e.call(n, t)
            },
            n.onclick = function(t) { ( - 1 === Te.indexOf("Android") || Date.now() - (i.touchEventFired || 0) > 1100) && e.call(n, t)
            }) : n["on" + t] = e,
            this
        },
        setRadialReference: function(t) {
            return this.element.radialReference = t,
            this
        },
        translate: function(t, e) {
            return this.attr({
                translateX: t,
                translateY: e
            })
        },
        invert: function() {
            return this.inverted = !0,
            this.updateTransform(),
            this
        },
        htmlCss: function(e) {
            var i = this.element;
            return (i = e && "SPAN" === i.tagName && e.width) && (delete e.width, this.textWidth = i, this.updateTransform()),
            this.styles = t(this.styles, e),
            f(this.element, e),
            this
        },
        htmlGetBBox: function() {
            var t = this.element,
            e = this.bBox;
            return e || ("text" === t.nodeName && (t.style.position = "absolute"), e = this.bBox = {
                x: t.offsetLeft,
                y: t.offsetTop,
                width: t.offsetWidth,
                height: t.offsetHeight
            }),
            e
        },
        htmlUpdateTransform: function() {
            if (this.added) {
                var t = this.renderer,
                e = this.element,
                n = this.translateX || 0,
                r = this.translateY || 0,
                o = this.x || 0,
                s = this.y || 0,
                a = this.textAlign || "left",
                l = {
                    left: 0,
                    center: .5,
                    right: 1
                } [a],
                h = a && "left" !== a,
                u = this.shadows;
                if (f(e, {
                    marginLeft: n,
                    marginTop: r
                }), u && ni(u,
                function(t) {
                    f(t, {
                        marginLeft: n + 1,
                        marginTop: r + 1
                    })
                }), this.inverted && ni(e.childNodes,
                function(i) {
                    t.invertChild(i, e)
                }), "SPAN" === e.tagName) {
                    var d, g, m, u = this.rotation;
                    d = 0;
                    var y, v = 1,
                    x = 0;
                    m = i(this.textWidth);
                    var b = this.xCorr || 0,
                    k = this.yCorr || 0,
                    w = [u, a, e.innerHTML, this.textWidth].join(",");
                    w !== this.cTT && (c(u) && (d = u * we, v = xe(d), x = be(d), this.setSpanRotation(u, x, v)), d = p(this.elemWidth, e.offsetWidth), g = p(this.elemHeight, e.offsetHeight), d > m && /[ \-]/.test(e.textContent || e.innerText) && (f(e, {
                        width: m + "px",
                        display: "block",
                        whiteSpace: "normal"
                    }), d = m), m = t.fontMetrics(e.style.fontSize).b, b = 0 > v && -d, k = 0 > x && -g, y = 0 > v * x, b += x * m * (y ? 1 - l: l), k -= v * m * (u ? y ? l: 1 - l: 1), h && (b -= d * l * (0 > v ? -1 : 1), u && (k -= g * l * (0 > x ? -1 : 1)), f(e, {
                        textAlign: a
                    })), this.xCorr = b, this.yCorr = k),
                    f(e, {
                        left: o + b + "px",
                        top: s + k + "px"
                    }),
                    Le && (g = e.offsetHeight),
                    this.cTT = w
                }
            } else this.alignOnAdd = !0
        },
        setSpanRotation: function(t) {
            var e = {};
            e[Ce ? "-ms-transform": Le ? "-webkit-transform": Me ? "MozTransform": Se ? "-o-transform": ""] = e.transform = "rotate(" + t + "deg)",
            f(this.element, e)
        },
        updateTransform: function() {
            var t = this.translateX || 0,
            e = this.translateY || 0,
            i = this.scaleX,
            n = this.scaleY,
            r = this.inverted,
            o = this.rotation;
            r && (t += this.attr("width"), e += this.attr("height")),
            t = ["translate(" + t + "," + e + ")"],
            r ? t.push("rotate(90) scale(-1,1)") : o && t.push("rotate(" + o + " " + (this.x || 0) + " " + (this.y || 0) + ")"),
            (c(i) || c(n)) && t.push("scale(" + p(i, 1) + " " + p(n, 1) + ")"),
            t.length && u(this.element, "transform", t.join(" "))
        },
        toFront: function() {
            var t = this.element;
            return t.parentNode.appendChild(t),
            this
        },
        align: function(t, e, i) {
            var r, o, s, a, l = {};
            return o = this.renderer,
            s = o.alignedObjects,
            t ? (this.alignOptions = t, this.alignByTranslate = e, (!i || n(i)) && (this.alignTo = r = i || "renderer", h(s, this), s.push(this), i = null)) : (t = this.alignOptions, e = this.alignByTranslate, r = this.alignTo),
            i = p(i, o[r], o),
            r = t.align,
            o = t.verticalAlign,
            s = (i.x || 0) + (t.x || 0),
            a = (i.y || 0) + (t.y || 0),
            ("right" === r || "center" === r) && (s += (i.width - (t.width || 0)) / {
                right: 1,
                center: 2
            } [r]),
            l[e ? "translateX": "x"] = pe(s),
            ("bottom" === o || "middle" === o) && (a += (i.height - (t.height || 0)) / ({
                bottom: 1,
                middle: 2
            } [o] || 1)),
            l[e ? "translateY": "y"] = pe(a),
            this[this.placed ? "animate": "attr"](l),
            this.placed = !0,
            this.alignAttr = l,
            this
        },
        getBBox: function() {
            var e, i = this.bBox,
            n = this.renderer,
            r = this.rotation;
            e = this.element;
            var o = this.styles,
            s = r * we;
            if (!i) {
                if (e.namespaceURI === De || n.forExport) {
                    try {
                        i = e.getBBox ? t({},
                        e.getBBox()) : {
                            width: e.offsetWidth,
                            height: e.offsetHeight
                        }
                    } catch(a) {} (!i || i.width < 0) && (i = {
                        width: 0,
                        height: 0
                    })
                } else i = this.htmlGetBBox();
                n.isSVG && (n = i.width, e = i.height, Ce && o && "11px" === o.fontSize && "22.7" === e.toPrecision(3) && (i.height = e = 14), r && (i.width = ve(e * be(s)) + ve(n * xe(s)), i.height = ve(e * xe(s)) + ve(n * be(s)))),
                this.bBox = i
            }
            return i
        },
        show: function() {
            return this.attr({
                visibility: "visible"
            })
        },
        hide: function() {
            return this.attr({
                visibility: "hidden"
            })
        },
        fadeOut: function(t) {
            var e = this;
            e.animate({
                opacity: 0
            },
            {
                duration: t || 150,
                complete: function() {
                    e.hide()
                }
            })
        },
        add: function(t) {
            var e, n = this.renderer,
            r = t || n,
            o = r.element || n.box,
            s = o.childNodes,
            a = this.element,
            l = u(a, "zIndex");
            if (t && (this.parentGroup = t), this.parentInverted = t && t.inverted, void 0 !== this.textStr && n.buildText(this), l && (r.handleZ = !0, l = i(l)), r.handleZ) for (r = 0; r < s.length; r++) if (t = s[r], n = u(t, "zIndex"), t !== a && (i(n) > l || !c(l) && c(n))) {
                o.insertBefore(a, t),
                e = !0;
                break
            }
            return e || o.appendChild(a),
            this.added = !0,
            hi(this, "add"),
            this
        },
        safeRemoveChild: function(t) {
            var e = t.parentNode;
            e && e.removeChild(t)
        },
        destroy: function() {
            var t, e, i = this,
            n = i.element || {},
            r = i.shadows,
            o = i.renderer.isSVG && "SPAN" === n.nodeName && i.parentGroup;
            if (n.onclick = n.onmouseout = n.onmouseover = n.onmousemove = n.point = null, di(i), i.clipPath && (i.clipPath = i.clipPath.destroy()), i.stops) {
                for (e = 0; e < i.stops.length; e++) i.stops[e] = i.stops[e].destroy();
                i.stops = null
            }
            for (i.safeRemoveChild(n), r && ni(r,
            function(t) {
                i.safeRemoveChild(t)
            }); o && 0 === o.div.childNodes.length;) n = o.parentGroup,
            i.safeRemoveChild(o.div),
            delete o.div,
            o = n;
            i.alignTo && h(i.renderer.alignedObjects, i);
            for (t in i) delete i[t];
            return null
        },
        shadow: function(t, e, i) {
            var n, r, o, s, a, l, h = [],
            c = this.element;
            if (t) {
                for (s = p(t.width, 3), a = (t.opacity || .15) / s, l = this.parentInverted ? "(-1,-1)": "(" + p(t.offsetX, 1) + ", " + p(t.offsetY, 1) + ")", n = 1; s >= n; n++) r = c.cloneNode(0),
                o = 2 * s + 1 - 2 * n,
                u(r, {
                    isShadow: "true",
                    stroke: t.color || "black",
                    "stroke-opacity": a * n,
                    "stroke-width": o,
                    transform: "translate" + l,
                    fill: We
                }),
                i && (u(r, "height", me(u(r, "height") - o, 0)), r.cutHeight = o),
                e ? e.element.appendChild(r) : c.parentNode.insertBefore(r, c),
                h.push(r);
                this.shadows = h
            }
            return this
        }
    };
    var vi = function() {
        this.init.apply(this, arguments)
    };
    vi.prototype = {
        Element: B,
        init: function(t, e, i, n) {
            var r, o, s = location;
            r = this.createElement("svg").attr({
                version: "1.1"
            }),
            o = r.element,
            t.appendChild(o),
            -1 === t.innerHTML.indexOf("xmlns") && u(o, "xmlns", De),
            this.isSVG = !0,
            this.box = o,
            this.boxWrapper = r,
            this.alignedObjects = [],
            this.url = (Me || Le) && ce.getElementsByTagName("base").length ? s.href.replace(/#.*?$/, "").replace(/([\('\)])/g, "\\$1").replace(/ /g, "%20") : "",
            this.createElement("desc").add().element.appendChild(ce.createTextNode("Created with Highcharts 3.0.7")),
            this.defs = this.createElement("defs").add(),
            this.forExport = n,
            this.gradients = {},
            this.setSize(e, i, !1);
            var a;
            Me && t.getBoundingClientRect && (this.subPixelFix = e = function() {
                f(t, {
                    left: 0,
                    top: 0
                }),
                a = t.getBoundingClientRect(),
                f(t, {
                    left: ge(a.left) - a.left + "px",
                    top: ge(a.top) - a.top + "px"
                })
            },
            e(), ai(ue, "resize", e))
        },
        isHidden: function() {
            return ! this.boxWrapper.getBBox().width
        },
        destroy: function() {
            var t = this.defs;
            return this.box = null,
            this.boxWrapper = this.boxWrapper.destroy(),
            P(this.gradients || {}),
            this.gradients = null,
            t && (this.defs = t.destroy()),
            this.subPixelFix && li(ue, "resize", this.subPixelFix),
            this.alignedObjects = null
        },
        createElement: function(t) {
            var e = new this.Element;
            return e.init(this, t),
            e
        },
        draw: function() {},
        buildText: function(t) {
            for (var e = t.element,
            n = this,
            r = n.forExport,
            o = p(t.textStr, "").toString().replace(/<(b|strong)>/g, '<span style="font-weight:bold">').replace(/<(i|em)>/g, '<span style="font-style:italic">').replace(/<a/g, "<span").replace(/<\/(b|strong|i|em|a)>/g, "</span>").split(/<br.*?>/g), s = e.childNodes, a = /style="([^"]+)"/, l = /href="(http[^"]+)"/, h = u(e, "x"), c = t.styles, d = t.textWidth, g = c && c.lineHeight, m = s.length; m--;) e.removeChild(s[m]);
            d && !t.added && this.box.appendChild(e),
            "" === o[o.length - 1] && o.pop(),
            ni(o,
            function(o, s) {
                var p, m = 0,
                o = o.replace(/<span/g, "|||<span").replace(/<\/span>/g, "</span>|||");
                p = o.split("|||"),
                ni(p,
                function(o) {
                    if ("" !== o || 1 === p.length) {
                        var y, v = {},
                        x = ce.createElementNS(De, "tspan");
                        if (a.test(o) && (y = o.match(a)[1].replace(/(;| |^)color([ :])/, "$1fill$2"), u(x, "style", y)), l.test(o) && !r && (u(x, "onclick", 'location.href="' + o.match(l)[1] + '"'), f(x, {
                            cursor: "pointer"
                        })), o = (o.replace(/<(.|\n)*?>/g, "") || " ").replace(/&lt;/g, "<").replace(/&gt;/g, ">"), " " !== o && (x.appendChild(ce.createTextNode(o)), m ? v.dx = 0 : v.x = h, u(x, v), !m && s && (!Ne && r && f(x, {
                            display: "block"
                        }), u(x, "dy", g || n.fontMetrics(/px$/.test(x.style.fontSize) ? x.style.fontSize: c.fontSize).h, Le && x.offsetHeight)), e.appendChild(x), m++, d)) for (var b, k, o = o.replace(/([^\^])-/g, "$1- ").split(" "), v = t._clipHeight, w = [], T = i(g || 16), S = 1; o.length || w.length;) delete t.bBox,
                        b = t.getBBox(),
                        k = b.width,
                        !Ne && n.forExport && (k = n.measureSpanWidth(x.firstChild.data, t.styles)),
                        b = k > d,
                        b && 1 !== o.length ? (x.removeChild(x.firstChild), w.unshift(o.pop())) : (o = w, w = [], o.length && (S++, v && S * T > v ? (o = ["..."], t.attr("title", t.textStr)) : (x = ce.createElementNS(De, "tspan"), u(x, {
                            dy: T,
                            x: h
                        }), y && u(x, "style", y), e.appendChild(x), k > d && (d = k)))),
                        o.length && x.appendChild(ce.createTextNode(o.join(" ").replace(/- /g, "-")))
                    }
                })
            })
        },
        button: function(i, n, r, o, s, a, l, h, c) {
            var u, d, p, f, g, m, y = this.label(i, n, r, c, null, null, null, null, "button"),
            v = 0,
            i = {
                x1: 0,
                y1: 0,
                x2: 0,
                y2: 1
            },
            s = e({
                "stroke-width": 1,
                stroke: "#CCCCCC",
                fill: {
                    linearGradient: i,
                    stops: [[0, "#FEFEFE"], [1, "#F6F6F6"]]
                },
                r: 2,
                padding: 5,
                style: {
                    color: "black"
                }
            },
            s);
            return p = s.style,
            delete s.style,
            a = e(s, {
                stroke: "#68A",
                fill: {
                    linearGradient: i,
                    stops: [[0, "#FFF"], [1, "#ACF"]]
                }
            },
            a),
            f = a.style,
            delete a.style,
            l = e(s, {
                stroke: "#68A",
                fill: {
                    linearGradient: i,
                    stops: [[0, "#9BD"], [1, "#CDF"]]
                }
            },
            l),
            g = l.style,
            delete l.style,
            h = e(s, {
                style: {
                    color: "#CCC"
                }
            },
            h),
            m = h.style,
            delete h.style,
            ai(y.element, Ce ? "mouseover": "mouseenter",
            function() {
                3 !== v && y.attr(a).css(f)
            }),
            ai(y.element, Ce ? "mouseout": "mouseleave",
            function() {
                3 !== v && (u = [s, a, l][v], d = [p, f, g][v], y.attr(u).css(d))
            }),
            y.setState = function(t) { (y.state = v = t) ? 2 === t ? y.attr(l).css(g) : 3 === t && y.attr(h).css(m) : y.attr(s).css(p)
            },
            y.on("click",
            function() {
                3 !== v && o.call(y)
            }).attr(s).css(t({
                cursor: "default"
            },
            p))
        },
        crispLine: function(t, e) {
            return t[1] === t[4] && (t[1] = t[4] = pe(t[1]) - e % 2 / 2),
            t[2] === t[5] && (t[2] = t[5] = pe(t[2]) + e % 2 / 2),
            t
        },
        path: function(e) {
            var i = {
                fill: We
            };
            return o(e) ? i.d = e: r(e) && t(i, e),
            this.createElement("path").attr(i)
        },
        circle: function(t, e, i) {
            return t = r(t) ? t: {
                x: t,
                y: e,
                r: i
            },
            this.createElement("circle").attr(t)
        },
        arc: function(t, e, i, n, o, s) {
            return r(t) && (e = t.y, i = t.r, n = t.innerR, o = t.start, s = t.end, t = t.x),
            t = this.symbol("arc", t || 0, e || 0, i || 0, i || 0, {
                innerR: n || 0,
                start: o || 0,
                end: s || 0
            }),
            t.r = i,
            t
        },
        rect: function(t, e, i, n, o, s) {
            return o = r(t) ? t.r: o,
            o = this.createElement("rect").attr({
                rx: o,
                ry: o,
                fill: We
            }),
            o.attr(r(t) ? t: o.crisp(s, t, e, me(i, 0), me(n, 0)))
        },
        setSize: function(t, e, i) {
            var n = this.alignedObjects,
            r = n.length;
            for (this.width = t, this.height = e, this.boxWrapper[p(i, !0) ? "animate": "attr"]({
                width: t,
                height: e
            }); r--;) n[r].align()
        },
        g: function(t) {
            var e = this.createElement("g");
            return c(t) ? e.attr({
                "class": "highcharts-" + t
            }) : e
        },
        image: function(e, i, n, r, o) {
            var s = {
                preserveAspectRatio: We
            };
            return arguments.length > 1 && t(s, {
                x: i,
                y: n,
                width: r,
                height: o
            }),
            s = this.createElement("image").attr(s),
            s.element.setAttributeNS ? s.element.setAttributeNS("http://www.w3.org/1999/xlink", "href", e) : s.element.setAttribute("hc-svg-href", e),
            s
        },
        symbol: function(e, i, n, r, o, s) {
            var a, l, h, c = this.symbols[e],
            c = c && c(pe(i), pe(n), r, o, s),
            u = /^url\((.*?)\)$/;
            return c ? (a = this.path(c), t(a, {
                symbolName: e,
                x: i,
                y: n,
                width: r,
                height: o
            }), s && t(a, s)) : u.test(e) && (h = function(t, e) {
                t.element && (t.attr({
                    width: e[0],
                    height: e[1]
                }), t.alignByTranslate || t.translate(pe((r - e[0]) / 2), pe((o - e[1]) / 2)))
            },
            l = e.match(u)[1], e = Be[l], a = this.image(l).attr({
                x: i,
                y: n
            }), a.isImg = !0, e ? h(a, e) : (a.attr({
                width: 0,
                height: 0
            }), g("img", {
                onload: function() {
                    h(a, Be[l] = [this.width, this.height])
                },
                src: l
            }))),
            a
        },
        symbols: {
            circle: function(t, e, i, n) {
                var r = .166 * i;
                return ["M", t + i / 2, e, "C", t + i + r, e, t + i + r, e + n, t + i / 2, e + n, "C", t - r, e + n, t - r, e, t + i / 2, e, "Z"]
            },
            square: function(t, e, i, n) {
                return ["M", t, e, "L", t + i, e, t + i, e + n, t, e + n, "Z"]
            },
            triangle: function(t, e, i, n) {
                return ["M", t + i / 2, e, "L", t + i, e + n, t, e + n, "Z"]
            },
            "triangle-down": function(t, e, i, n) {
                return ["M", t, e, "L", t + i, e, t + i / 2, e + n, "Z"]
            },
            diamond: function(t, e, i, n) {
                return ["M", t + i / 2, e, "L", t + i, e + n / 2, t + i / 2, e + n, t, e + n / 2, "Z"]
            },
            arc: function(t, e, i, n, r) {
                var o = r.start,
                i = r.r || i || n,
                s = r.end - .001,
                n = r.innerR,
                a = r.open,
                l = xe(o),
                h = be(o),
                c = xe(s),
                s = be(s),
                r = r.end - o < ke ? 0 : 1;
                return ["M", t + i * l, e + i * h, "A", i, i, 0, r, 1, t + i * c, e + i * s, a ? "M": "L", t + n * c, e + n * s, "A", n, n, 0, r, 0, t + n * l, e + n * h, a ? "": "Z"]
            }
        },
        clipRect: function(t, e, i, n) {
            var r = "highcharts-" + Oe++,
            o = this.createElement("clipPath").attr({
                id: r
            }).add(this.defs),
            t = this.rect(t, e, i, n, 0).add(o);
            return t.id = r,
            t.clipPath = o,
            t
        },
        color: function(t, i, n) {
            var r, s, a, l, h, d, p, f, g = this,
            m = /^rgba/,
            y = [];
            if (t && t.linearGradient ? s = "linearGradient": t && t.radialGradient && (s = "radialGradient"), s) {
                n = t[s],
                a = g.gradients,
                h = t.stops,
                i = i.radialReference,
                o(n) && (t[s] = n = {
                    x1: n[0],
                    y1: n[1],
                    x2: n[2],
                    y2: n[3],
                    gradientUnits: "userSpaceOnUse"
                }),
                "radialGradient" === s && i && !c(n.gradientUnits) && (n = e(n, {
                    cx: i[0] - i[2] / 2 + n.cx * i[2],
                    cy: i[1] - i[2] / 2 + n.cy * i[2],
                    r: n.r * i[2],
                    gradientUnits: "userSpaceOnUse"
                }));
                for (f in n)"id" !== f && y.push(f, n[f]);
                for (f in h) y.push(h[f]);
                return y = y.join(","),
                a[y] ? t = a[y].id: (n.id = t = "highcharts-" + Oe++, a[y] = l = g.createElement(s).attr(n).add(g.defs), l.stops = [], ni(h,
                function(t) {
                    m.test(t[1]) ? (r = yi(t[1]), d = r.get("rgb"), p = r.get("a")) : (d = t[1], p = 1),
                    t = g.createElement("stop").attr({
                        offset: t[0],
                        "stop-color": d,
                        "stop-opacity": p
                    }).add(l),
                    l.stops.push(t)
                })),
                "url(" + g.url + "#" + t + ")"
            }
            return m.test(t) ? (r = yi(t), u(i, n + "-opacity", r.get("a")), r.get("rgb")) : (i.removeAttribute(n + "-opacity"), t)
        },
        text: function(t, e, i, n) {
            var r = V.chart.style,
            o = He || !Ne && this.forExport;
            return n && !this.forExport ? this.html(t, e, i) : (e = pe(p(e, 0)), i = pe(p(i, 0)), t = this.createElement("text").attr({
                x: e,
                y: i,
                text: t
            }).css({
                fontFamily: r.fontFamily,
                fontSize: r.fontSize
            }), o && t.css({
                position: "absolute"
            }), t.x = e, t.y = i, t)
        },
        html: function(e, i, n) {
            var r = V.chart.style,
            o = this.createElement("span"),
            s = o.attrSetters,
            a = o.element,
            l = o.renderer;
            return s.text = function(t) {
                return t !== a.innerHTML && delete this.bBox,
                a.innerHTML = t,
                !1
            },
            s.x = s.y = s.align = function(t, e) {
                return "align" === e && (e = "textAlign"),
                o[e] = t,
                o.htmlUpdateTransform(),
                !1
            },
            o.attr({
                text: e,
                x: pe(i),
                y: pe(n)
            }).css({
                position: "absolute",
                whiteSpace: "nowrap",
                fontFamily: r.fontFamily,
                fontSize: r.fontSize
            }),
            o.css = o.htmlCss,
            l.isSVG && (o.add = function(e) {
                var i, n = l.box.parentNode,
                r = [];
                if (this.parentGroup = e) {
                    if (i = e.div, !i) {
                        for (; e;) r.push(e),
                        e = e.parentGroup;
                        ni(r.reverse(),
                        function(e) {
                            var r;
                            i = e.div = e.div || g(je, {
                                className: u(e.element, "class")
                            },
                            {
                                position: "absolute",
                                left: (e.translateX || 0) + "px",
                                top: (e.translateY || 0) + "px"
                            },
                            i || n),
                            r = i.style,
                            t(e.attrSetters, {
                                translateX: function(t) {
                                    r.left = t + "px"
                                },
                                translateY: function(t) {
                                    r.top = t + "px"
                                },
                                visibility: function(t, e) {
                                    r[e] = t
                                }
                            })
                        })
                    }
                } else i = n;
                return i.appendChild(a),
                o.added = !0,
                o.alignOnAdd && o.htmlUpdateTransform(),
                o
            }),
            o
        },
        fontMetrics: function(t) {
            var t = i(t || 11),
            t = 24 > t ? t + 4 : pe(1.2 * t),
            e = pe(.8 * t);
            return {
                h: t,
                b: e
            }
        },
        label: function(i, n, r, o, s, a, l, h, u) {
            function d() {
                var t, i;
                t = A.element.style,
                y = (void 0 === v || void 0 === x || C.styles.textAlign) && A.getBBox(),
                C.width = (v || y.width || 0) + 2 * M + P,
                C.height = (x || y.height || 0) + 2 * M,
                w = M + S.fontMetrics(t && t.fontSize).b,
                T && (m || (t = pe( - L * M), i = h ? -w: 0, C.box = m = o ? S.symbol(o, t, i, C.width, C.height, N) : S.rect(t, i, C.width, C.height, 0, N[Ze]), m.add(C)), m.isImg || m.attr(e({
                    width: C.width,
                    height: C.height
                },
                N)), N = null)
            }
            function p() {
                var t, e = C.styles,
                e = e && e.textAlign,
                i = P + M * (1 - L);
                t = h ? 0 : w,
                !c(v) || "center" !== e && "right" !== e || (i += {
                    center: .5,
                    right: 1
                } [e] * (v - y.width)),
                (i !== A.x || t !== A.y) && A.attr({
                    x: i,
                    y: t
                }),
                A.x = i,
                A.y = t
            }
            function f(t, e) {
                m ? m.attr(t, e) : N[t] = e
            }
            function g() {
                A.add(C),
                C.attr({
                    text: i,
                    x: n,
                    y: r
                }),
                m && c(s) && C.attr({
                    anchorX: s,
                    anchorY: a
                })
            }
            var m, y, v, x, b, k, w, T, S = this,
            C = S.g(u),
            A = S.text("", 0, 0, l).attr({
                zIndex: 1
            }),
            L = 0,
            M = 3,
            P = 0,
            D = 0,
            N = {},
            l = C.attrSetters;
            ai(C, "add", g),
            l.width = function(t) {
                return v = t,
                !1
            },
            l.height = function(t) {
                return x = t,
                !1
            },
            l.padding = function(t) {
                return c(t) && t !== M && (M = t, p()),
                !1
            },
            l.paddingLeft = function(t) {
                return c(t) && t !== P && (P = t, p()),
                !1
            },
            l.align = function(t) {
                return L = {
                    left: 0,
                    center: .5,
                    right: 1
                } [t],
                !1
            },
            l.text = function(t, e) {
                return A.attr(e, t),
                d(),
                p(),
                !1
            },
            l[Ze] = function(t, e) {
                return T = !0,
                D = t % 2 / 2,
                f(e, t),
                !1
            },
            l.stroke = l.fill = l.r = function(t, e) {
                return "fill" === e && (T = !0),
                f(e, t),
                !1
            },
            l.anchorX = function(t, e) {
                return s = t,
                f(e, t + D - b),
                !1
            },
            l.anchorY = function(t, e) {
                return a = t,
                f(e, t - k),
                !1
            },
            l.x = function(t) {
                return C.x = t,
                t -= L * ((v || y.width) + M),
                b = pe(t),
                C.attr("translateX", b),
                !1
            },
            l.y = function(t) {
                return k = C.y = pe(t),
                C.attr("translateY", k),
                !1
            };
            var E = C.css;
            return t(C, {
                css: function(t) {
                    if (t) {
                        var i = {},
                        t = e(t);
                        ni("fontSize,fontWeight,fontFamily,color,lineHeight,width,textDecoration,textShadow".split(","),
                        function(e) {
                            t[e] !== Y && (i[e] = t[e], delete t[e])
                        }),
                        A.css(i)
                    }
                    return E.call(C, t)
                },
                getBBox: function() {
                    return {
                        width: y.width + 2 * M,
                        height: y.height + 2 * M,
                        x: y.x - M,
                        y: y.y - M
                    }
                },
                shadow: function(t) {
                    return m && m.shadow(t),
                    C
                },
                destroy: function() {
                    li(C, "add", g),
                    li(C.element, "mouseenter"),
                    li(C.element, "mouseleave"),
                    A && (A = A.destroy()),
                    m && (m = m.destroy()),
                    B.prototype.destroy.call(C),
                    C = S = d = p = f = g = null
                }
            })
        }
    },
    G = vi;
    var xi;
    if (!Ne && !He) {
        Highcharts.VMLElement = xi = {
            init: function(t, e) {
                var i = ["<", e, ' filled="f" stroked="f"'],
                n = ["position: ", "absolute", ";"],
                r = e === je; ("shape" === e || r) && n.push("left:0;top:0;width:1px;height:1px;"),
                n.push("visibility: ", r ? "hidden": "visible"),
                i.push(' style="', n.join(""), '"/>'),
                e && (i = r || "span" === e || "img" === e ? i.join("") : t.prepVML(i), this.element = g(i)),
                this.renderer = t,
                this.attrSetters = {}
            },
            add: function(t) {
                var e = this.renderer,
                i = this.element,
                n = e.box,
                n = t ? t.element || t: n;
                return t && t.inverted && e.invertChild(i, n),
                n.appendChild(i),
                this.added = !0,
                this.alignOnAdd && !this.deferUpdateTransform && this.updateTransform(),
                hi(this, "add"),
                this
            },
            updateTransform: B.prototype.htmlUpdateTransform,
            setSpanRotation: function(t, e, i) {
                f(this.element, {
                    filter: t ? ["progid:DXImageTransform.Microsoft.Matrix(M11=", i, ", M12=", -e, ", M21=", e, ", M22=", i, ", sizingMethod='auto expand')"].join("") : We
                })
            },
            pathToVML: function(t) {
                for (var e, i = t.length,
                n = []; i--;) s(t[i]) ? n[i] = pe(10 * t[i]) - 5 : "Z" === t[i] ? n[i] = "x": (n[i] = t[i], !t.isArc || "wa" !== t[i] && "at" !== t[i] || (e = "wa" === t[i] ? 1 : -1, n[i + 5] === n[i + 7] && (n[i + 7] -= e), n[i + 6] === n[i + 8] && (n[i + 8] -= e)));
                return n.join(" ") || "x"
            },
            attr: function(t, e) {
                var i, r, o, a, l, h = this.element || {},
                d = h.style,
                p = h.nodeName,
                f = this.renderer,
                m = this.symbolName,
                y = this.shadows,
                v = this.attrSetters,
                x = this;
                if (n(t) && c(e) && (i = t, t = {},
                t[i] = e), n(t)) i = t,
                x = "strokeWidth" === i || "stroke-width" === i ? this.strokeweight: this[i];
                else for (i in t) if (r = t[i], l = !1, o = v[i] && v[i].call(this, r, i), o !== !1 && null !== r) {
                    if (o !== Y && (r = o), m && /^(x|y|r|start|end|width|height|innerR|anchorX|anchorY)/.test(i)) a || (this.symbolAttr(t), a = !0),
                    l = !0;
                    else if ("d" === i) {
                        if (r = r || [], this.d = r.join(" "), h.path = r = this.pathToVML(r), y) for (o = y.length; o--;) y[o].path = y[o].cutOff ? this.cutOffPath(r, y[o].cutOff) : r;
                        l = !0
                    } else if ("visibility" === i) {
                        if (y) for (o = y.length; o--;) y[o].style[i] = r;
                        "DIV" === p && (r = "hidden" === r ? "-999em": 0, Ae || (d[i] = r ? "visible": "hidden"), i = "top"),
                        d[i] = r,
                        l = !0
                    } else "zIndex" === i ? (r && (d[i] = r), l = !0) : -1 !== ii(i, ["x", "y", "width", "height"]) ? (this[i] = r, "x" === i || "y" === i ? i = {
                        x: "left",
                        y: "top"
                    } [i] : r = me(0, r), this.updateClipping ? (this[i] = r, this.updateClipping()) : d[i] = r, l = !0) : "class" === i && "DIV" === p ? h.className = r: "stroke" === i ? (r = f.color(r, h, i), i = "strokecolor") : "stroke-width" === i || "strokeWidth" === i ? (h.stroked = r ? !0 : !1, i = "strokeweight", this[i] = r, s(r) && (r += "px")) : "dashstyle" === i ? ((h.getElementsByTagName("stroke")[0] || g(f.prepVML(["<stroke/>"]), null, null, h))[i] = r || "solid", this.dashstyle = r, l = !0) : "fill" === i ? "SPAN" === p ? d.color = r: "IMG" !== p && (h.filled = r !== We ? !0 : !1, r = f.color(r, h, i, this), i = "fillcolor") : "opacity" === i ? l = !0 : "shape" === p && "rotation" === i ? (this[i] = h.style[i] = r, h.style.left = -pe(be(r * we) + 1) + "px", h.style.top = pe(xe(r * we)) + "px") : "translateX" === i || "translateY" === i || "rotation" === i ? (this[i] = r, this.updateTransform(), l = !0) : "text" === i && (this.bBox = null, h.innerHTML = r, l = !0);
                    l || (Ae ? h[i] = r: u(h, i, r))
                }
                return x
            },
            clip: function(t) {
                var e, i = this;
                return t ? (e = t.members, h(e, i), e.push(i), i.destroyClip = function() {
                    h(e, i)
                },
                t = t.getCSS(i)) : (i.destroyClip && i.destroyClip(), t = {
                    clip: Ae ? "inherit": "rect(auto)"
                }),
                i.css(t)
            },
            css: B.prototype.htmlCss,
            safeRemoveChild: function(t) {
                t.parentNode && D(t)
            },
            destroy: function() {
                return this.destroyClip && this.destroyClip(),
                B.prototype.destroy.apply(this)
            },
            on: function(t, e) {
                return this.element["on" + t] = function() {
                    var t = ue.event;
                    t.target = t.srcElement,
                    e(t)
                },
                this
            },
            cutOffPath: function(t, e) {
                var n, t = t.split(/[ ,]/);
                return n = t.length,
                (9 === n || 11 === n) && (t[n - 4] = t[n - 2] = i(t[n - 2]) - 10 * e),
                t.join(" ")
            },
            shadow: function(t, e, n) {
                var r, o, s, a, l, h, c, u = [],
                d = this.element,
                f = this.renderer,
                m = d.style,
                y = d.path;
                if (y && "string" != typeof y.value && (y = "x"), l = y, t) {
                    for (h = p(t.width, 3), c = (t.opacity || .15) / h, r = 1; 3 >= r; r++) a = 2 * h + 1 - 2 * r,
                    n && (l = this.cutOffPath(y.value, a + .5)),
                    s = ['<shape isShadow="true" strokeweight="', a, '" filled="false" path="', l, '" coordsize="10 10" style="', d.style.cssText, '" />'],
                    o = g(f.prepVML(s), null, {
                        left: i(m.left) + p(t.offsetX, 1),
                        top: i(m.top) + p(t.offsetY, 1)
                    }),
                    n && (o.cutOff = a + 1),
                    s = ['<stroke color="', t.color || "black", '" opacity="', c * r, '"/>'],
                    g(f.prepVML(s), null, null, o),
                    e ? e.element.appendChild(o) : d.parentNode.insertBefore(o, d),
                    u.push(o);
                    this.shadows = u
                }
                return this
            }
        },
        xi = m(B, xi);
        var bi = {
            Element: xi,
            isIE8: Te.indexOf("MSIE 8.0") > -1,
            init: function(t, e, i) {
                var n, r;
                if (this.alignedObjects = [], n = this.createElement(je), r = n.element, r.style.position = "relative", t.appendChild(n.element), this.isVML = !0, this.box = r, this.boxWrapper = n, this.setSize(e, i, !1), !ce.namespaces.hcv) {
                    ce.namespaces.add("hcv", "urn:schemas-microsoft-com:vml");
                    try {
                        ce.createStyleSheet().cssText = "hcv\\:fill, hcv\\:path, hcv\\:shape, hcv\\:stroke{ behavior:url(#default#VML); display: inline-block; } "
                    } catch(o) {
                        ce.styleSheets[0].cssText += "hcv\\:fill, hcv\\:path, hcv\\:shape, hcv\\:stroke{ behavior:url(#default#VML); display: inline-block; } "
                    }
                }
            },
            isHidden: function() {
                return ! this.box.offsetWidth
            },
            clipRect: function(e, i, n, o) {
                var s = this.createElement(),
                a = r(e);
                return t(s, {
                    members: [],
                    left: (a ? e.x: e) + 1,
                    top: (a ? e.y: i) + 1,
                    width: (a ? e.width: n) - 1,
                    height: (a ? e.height: o) - 1,
                    getCSS: function(e) {
                        var i = e.element,
                        n = i.nodeName,
                        e = e.inverted,
                        r = this.top - ("shape" === n ? i.offsetTop: 0),
                        o = this.left,
                        i = o + this.width,
                        s = r + this.height,
                        r = {
                            clip: "rect(" + pe(e ? o: r) + "px," + pe(e ? s: i) + "px," + pe(e ? i: s) + "px," + pe(e ? r: o) + "px)"
                        };
                        return ! e && Ae && "DIV" === n && t(r, {
                            width: i + "px",
                            height: s + "px"
                        }),
                        r
                    },
                    updateClipping: function() {
                        ni(s.members,
                        function(t) {
                            t.css(s.getCSS(t))
                        })
                    }
                })
            },
            color: function(t, e, i, n) {
                var r, o, s, a = this,
                l = /^rgba/,
                h = We;
                if (t && t.linearGradient ? s = "gradient": t && t.radialGradient && (s = "pattern"), s) {
                    var c, u, d, p, f, m, y, v, x = t.linearGradient || t.radialGradient,
                    b = "",
                    t = t.stops,
                    k = [],
                    w = function() {
                        o = ['<fill colors="' + k.join(",") + '" opacity="', f, '" o:opacity2="', p, '" type="', s, '" ', b, 'focus="100%" method="any" />'],
                        g(a.prepVML(o), null, null, e)
                    };
                    if (d = t[0], v = t[t.length - 1], d[0] > 0 && t.unshift([0, d[1]]), v[0] < 1 && t.push([1, v[1]]), ni(t,
                    function(t, e) {
                        l.test(t[1]) ? (r = yi(t[1]), c = r.get("rgb"), u = r.get("a")) : (c = t[1], u = 1),
                        k.push(100 * t[0] + "% " + c),
                        e ? (f = u, m = c) : (p = u, y = c)
                    }), "fill" === i) if ("gradient" === s) i = x.x1 || x[0] || 0,
                    t = x.y1 || x[1] || 0,
                    d = x.x2 || x[2] || 0,
                    x = x.y2 || x[3] || 0,
                    b = 'angle="' + (90 - 180 * de.atan((x - t) / (d - i)) / ke) + '"',
                    w();
                    else {
                        var T, h = x.r,
                        S = 2 * h,
                        C = 2 * h,
                        A = x.cx,
                        L = x.cy,
                        M = e.radialReference,
                        h = function() {
                            M && (T = n.getBBox(), A += (M[0] - T.x) / T.width - .5, L += (M[1] - T.y) / T.height - .5, S *= M[2] / T.width, C *= M[2] / T.height),
                            b = 'src="' + V.global.VMLRadialGradientURL + '" size="' + S + "," + C + '" origin="0.5,0.5" position="' + A + "," + L + '" color2="' + y + '" ',
                            w()
                        };
                        n.added ? h() : ai(n, "add", h),
                        h = m
                    } else h = c
                } else l.test(t) && "IMG" !== e.tagName ? (r = yi(t), o = ["<", i, ' opacity="', r.get("a"), '"/>'], g(this.prepVML(o), null, null, e), h = r.get("rgb")) : (h = e.getElementsByTagName(i), h.length && (h[0].opacity = 1, h[0].type = "solid"), h = t);
                return h
            },
            prepVML: function(t) {
                var e = this.isIE8,
                t = t.join("");
                return e ? (t = t.replace("/>", ' xmlns="urn:schemas-microsoft-com:vml" />'), t = -1 === t.indexOf('style="') ? t.replace("/>", ' style="display:inline-block;behavior:url(#default#VML);" />') : t.replace('style="', 'style="display:inline-block;behavior:url(#default#VML);')) : t = t.replace("<", "<hcv:"),
                t
            },
            text: vi.prototype.html,
            path: function(e) {
                var i = {
                    coordsize: "10 10"
                };
                return o(e) ? i.d = e: r(e) && t(i, e),
                this.createElement("shape").attr(i)
            },
            circle: function(t, e, i) {
                var n = this.symbol("circle");
                return r(t) && (i = t.r, e = t.y, t = t.x),
                n.isCircle = !0,
                n.r = i,
                n.attr({
                    x: t,
                    y: e
                })
            },
            g: function(t) {
                var e;
                return t && (e = {
                    className: "highcharts-" + t,
                    "class": "highcharts-" + t
                }),
                this.createElement(je).attr(e)
            },
            image: function(t, e, i, n, r) {
                var o = this.createElement("img").attr({
                    src: t
                });
                return arguments.length > 1 && o.attr({
                    x: e,
                    y: i,
                    width: n,
                    height: r
                }),
                o
            },
            rect: function(t, e, i, n, o, s) {
                var a = this.symbol("rect");
                return a.r = r(t) ? t.r: o,
                a.attr(r(t) ? t: a.crisp(s, t, e, me(i, 0), me(n, 0)))
            },
            invertChild: function(t, e) {
                var n = e.style;
                f(t, {
                    flip: "x",
                    left: i(n.width) - 1,
                    top: i(n.height) - 1,
                    rotation: -90
                })
            },
            symbols: {
                arc: function(t, e, i, n, r) {
                    var o = r.start,
                    s = r.end,
                    a = r.r || i || n,
                    i = r.innerR,
                    n = xe(o),
                    l = be(o),
                    h = xe(s),
                    c = be(s);
                    return s - o === 0 ? ["x"] : (o = ["wa", t - a, e - a, t + a, e + a, t + a * n, e + a * l, t + a * h, e + a * c], r.open && !i && o.push("e", "M", t, e), o.push("at", t - i, e - i, t + i, e + i, t + i * h, e + i * c, t + i * n, e + i * l, "x", "e"), o.isArc = !0, o)
                },
                circle: function(t, e, i, n, r) {
                    return r && (i = n = 2 * r.r),
                    r && r.isCircle && (t -= i / 2, e -= n / 2),
                    ["wa", t, e, t + i, e + n, t + i, e + n / 2, t + i, e + n / 2, "e"]
                },
                rect: function(t, e, i, n, r) {
                    var o, s = t + i,
                    a = e + n;
                    return c(r) && r.r ? (o = ye(r.r, i, n), s = ["M", t + o, e, "L", s - o, e, "wa", s - 2 * o, e, s, e + 2 * o, s - o, e, s, e + o, "L", s, a - o, "wa", s - 2 * o, a - 2 * o, s, a, s, a - o, s - o, a, "L", t + o, a, "wa", t, a - 2 * o, t + 2 * o, a, t + o, a, t, a - o, "L", t, e + o, "wa", t, e, t + 2 * o, e + 2 * o, t, e + o, t + o, e, "x", "e"]) : s = vi.prototype.symbols.square.apply(0, arguments),
                    s
                }
            }
        };
        Highcharts.VMLRenderer = xi = function() {
            this.init.apply(this, arguments)
        },
        xi.prototype = e(vi.prototype, bi),
        G = xi
    }
    vi.prototype.measureSpanWidth = function(t, e) {
        var i = ce.createElement("span"),
        n = ce.createTextNode(t);
        return i.appendChild(n),
        f(i, e),
        this.box.appendChild(i),
        i.offsetWidth
    };
    var ki;
    He && (Highcharts.CanVGRenderer = xi = function() {
        De = "http://www.w3.org/1999/xhtml"
    },
    xi.prototype.symbols = {},
    ki = function() {
        function t() {
            var t, i = e.length;
            for (t = 0; i > t; t++) e[t]();
            e = []
        }
        var e = [];
        return {
            push: function(i, n) {
                0 === e.length && ei(n, t),
                e.push(i)
            }
        }
    } (), G = xi),
    O.prototype = {
        addLabel: function() {
            var e, i = this.axis,
            n = i.options,
            r = i.chart,
            o = i.horiz,
            a = i.categories,
            h = i.names,
            u = this.pos,
            d = n.labels,
            f = i.tickPositions,
            o = o && a && !d.step && !d.staggerLines && !d.rotation && r.plotWidth / f.length || !o && (r.margin[3] || .33 * r.chartWidth),
            g = u === f[0],
            m = u === f[f.length - 1],
            h = a ? p(a[u], h[u], u) : u,
            a = this.label,
            y = f.info;
            i.isDatetimeAxis && y && (e = n.dateTimeLabelFormats[y.higherRanks[u] || y.unitName]),
            this.isFirst = g,
            this.isLast = m,
            n = i.labelFormatter.call({
                axis: i,
                chart: r,
                isFirst: g,
                isLast: m,
                dateTimeLabelFormat: e,
                value: i.isLog ? E(l(h)) : h
            }),
            u = o && {
                width: me(1, pe(o - 2 * (d.padding || 10))) + "px"
            },
            u = t(u, d.style),
            c(a) ? a && a.attr({
                text: n
            }).css(u) : (e = {
                align: i.labelAlign
            },
            s(d.rotation) && (e.rotation = d.rotation), o && d.ellipsis && (e._clipHeight = i.len / f.length), this.label = c(n) && d.enabled ? r.renderer.text(n, 0, 0, d.useHTML).attr(e).css(u).add(i.labelGroup) : null)
        },
        getLabelSize: function() {
            var t = this.label,
            e = this.axis;
            return t ? (this.labelBBox = t.getBBox())[e.horiz ? "height": "width"] : 0
        },
        getLabelSides: function() {
            var t = this.axis,
            e = this.labelBBox.width,
            t = e * {
                left: 0,
                center: .5,
                right: 1
            } [t.labelAlign] - t.options.labels.x;
            return [ - t, e - t]
        },
        handleOverflow: function(t, e) {
            var i = !0,
            n = this.axis,
            r = n.chart,
            o = this.isFirst,
            s = this.isLast,
            a = e.x,
            l = n.reversed,
            h = n.tickPositions;
            if (o || s) {
                var c = this.getLabelSides(),
                u = c[0],
                c = c[1],
                r = r.plotLeft,
                d = r + n.len,
                h = (n = n.ticks[h[t + (o ? 1 : -1)]]) && n.label.xy && n.label.xy.x + n.getLabelSides()[o ? 0 : 1];
                o && !l || s && l ? r > a + u && (a = r - u, n && a + c > h && (i = !1)) : a + c > d && (a = d - c, n && h > a + u && (i = !1)),
                e.x = a
            }
            return i
        },
        getPosition: function(t, e, i, n) {
            var r = this.axis,
            o = r.chart,
            s = n && o.oldChartHeight || o.chartHeight;
            return {
                x: t ? r.translate(e + i, null, null, n) + r.transB: r.left + r.offset + (r.opposite ? (n && o.oldChartWidth || o.chartWidth) - r.right - r.left: 0),
                y: t ? s - r.bottom + r.offset - (r.opposite ? r.height: 0) : s - r.translate(e + i, null, null, n) - r.transB
            }
        },
        getLabelPosition: function(t, e, i, n, r, o, s, a) {
            var l = this.axis,
            h = l.transA,
            u = l.reversed,
            d = l.staggerLines,
            p = l.chart.renderer.fontMetrics(r.style.fontSize).b,
            f = r.rotation,
            t = t + r.x - (o && n ? o * h * (u ? -1 : 1) : 0),
            e = e + r.y - (o && !n ? o * h * (u ? 1 : -1) : 0);
            return f && 2 === l.side && (e -= p - p * xe(f * we)),
            !c(r.y) && !f && (e += p - i.getBBox().height / 2),
            d && (e += s / (a || 1) % d * (l.labelOffset / d)),
            {
                x: t,
                y: e
            }
        },
        getMarkPath: function(t, e, i, n, r, o) {
            return o.crispLine(["M", t, e, "L", t + (r ? 0 : -i), e + (r ? i: 0)], n)
        },
        render: function(t, e, i) {
            var n = this.axis,
            r = n.options,
            o = n.chart.renderer,
            s = n.horiz,
            a = this.type,
            l = this.label,
            h = this.pos,
            c = r.labels,
            u = this.gridLine,
            d = a ? a + "Grid": "grid",
            f = a ? a + "Tick": "tick",
            g = r[d + "LineWidth"],
            m = r[d + "LineColor"],
            y = r[d + "LineDashStyle"],
            v = r[f + "Length"],
            d = r[f + "Width"] || 0,
            x = r[f + "Color"],
            b = r[f + "Position"],
            f = this.mark,
            k = c.step,
            w = !0,
            T = n.tickmarkOffset,
            S = this.getPosition(s, h, T, e),
            C = S.x,
            S = S.y,
            A = s && C === n.pos + n.len || !s && S === n.pos ? -1 : 1,
            L = n.staggerLines;
            this.isActive = !0,
            g && (h = n.getPlotLinePath(h + T, g * A, e, !0), u === Y && (u = {
                stroke: m,
                "stroke-width": g
            },
            y && (u.dashstyle = y), a || (u.zIndex = 1), e && (u.opacity = 0), this.gridLine = u = g ? o.path(h).attr(u).add(n.gridGroup) : null), !e && u && h && u[this.isNew ? "attr": "animate"]({
                d: h,
                opacity: i
            })),
            d && v && ("inside" === b && (v = -v), n.opposite && (v = -v), e = this.getMarkPath(C, S, v, d * A, s, o), f ? f.animate({
                d: e,
                opacity: i
            }) : this.mark = o.path(e).attr({
                stroke: x,
                "stroke-width": d,
                opacity: i
            }).add(n.axisGroup)),
            l && !isNaN(C) && (l.xy = S = this.getLabelPosition(C, S, l, s, c, T, t, k), this.isFirst && !this.isLast && !p(r.showFirstLabel, 1) || this.isLast && !this.isFirst && !p(r.showLastLabel, 1) ? w = !1 : !L && s && "justify" === c.overflow && !this.handleOverflow(t, S) && (w = !1), k && t % k && (w = !1), w && !isNaN(S.y) ? (S.opacity = i, l[this.isNew ? "attr": "animate"](S), this.isNew = !1) : l.attr("y", -9999))
        },
        destroy: function() {
            P(this, this.axis)
        }
    },
    z.prototype = {
        render: function() {
            var t, i = this,
            n = i.axis,
            r = n.horiz,
            o = (n.pointRange || 0) / 2,
            s = i.options,
            l = s.label,
            h = i.label,
            u = s.width,
            d = s.to,
            f = s.from,
            g = c(f) && c(d),
            m = s.value,
            y = s.dashStyle,
            v = i.svgElem,
            x = [],
            b = s.color,
            k = s.zIndex,
            w = s.events,
            T = n.chart.renderer;
            if (n.isLog && (f = a(f), d = a(d), m = a(m)), u) x = n.getPlotLinePath(m, u),
            o = {
                stroke: b,
                "stroke-width": u
            },
            y && (o.dashstyle = y);
            else {
                if (!g) return;
                f = me(f, n.min - o),
                d = ye(d, n.max + o),
                x = n.getPlotBandPath(f, d, s),
                o = {
                    fill: b
                },
                s.borderWidth && (o.stroke = s.borderColor, o["stroke-width"] = s.borderWidth)
            }
            if (c(k) && (o.zIndex = k), v) x ? v.animate({
                d: x
            },
            null, v.onGetPath) : (v.hide(), v.onGetPath = function() {
                v.show()
            },
            h && (i.label = h = h.destroy()));
            else if (x && x.length && (i.svgElem = v = T.path(x).attr(o).add(), w)) for (t in s = function(t) {
                v.on(t,
                function(e) {
                    w[t].apply(i, [e])
                })
            },
            w) s(t);
            return l && c(l.text) && x && x.length && n.width > 0 && n.height > 0 ? (l = e({
                align: r && g && "center",
                x: r ? !g && 4 : 10,
                verticalAlign: !r && g && "middle",
                y: r ? g ? 16 : 10 : g ? 6 : -4,
                rotation: r && !g && 90
            },
            l), h || (i.label = h = T.text(l.text, 0, 0, l.useHTML).attr({
                align: l.textAlign || l.align,
                rotation: l.rotation,
                zIndex: k
            }).css(l.style).add()), n = [x[1], x[4], p(x[6], x[1])], x = [x[2], x[5], p(x[7], x[2])], r = L(n), g = L(x), h.align(l, !1, {
                x: r,
                y: g,
                width: M(n) - r,
                height: M(x) - g
            }), h.show()) : h && h.hide(),
            i
        },
        destroy: function() {
            h(this.axis.plotLinesAndBands, this),
            delete this.axis,
            P(this)
        }
    },
    R.prototype = {
        destroy: function() {
            P(this, this.axis)
        },
        render: function(t) {
            var e = this.options,
            i = e.format,
            i = i ? b(i, this) : e.formatter.call(this);
            this.label ? this.label.attr({
                text: i,
                visibility: "hidden"
            }) : this.label = this.axis.chart.renderer.text(i, 0, 0, e.useHTML).css(e.style).attr({
                align: this.textAlign,
                rotation: e.rotation,
                visibility: "hidden"
            }).add(t)
        },
        setOffset: function(t, e) {
            var i = this.axis,
            n = i.chart,
            r = n.inverted,
            o = this.isNegative,
            s = i.translate(this.percent ? 100 : this.total, 0, 0, 0, 1),
            i = i.translate(0),
            i = ve(s - i),
            a = n.xAxis[0].translate(this.x) + t,
            l = n.plotHeight,
            o = {
                x: r ? o ? s: s - i: a,
                y: r ? l - a - e: o ? l - s - i: l - s,
                width: r ? i: e,
                height: r ? e: i
            }; (r = this.label) && (r.align(this.alignOptions, null, o), o = r.alignAttr, r.attr({
                visibility: this.options.crop === !1 || n.isInsidePlot(o.x, o.y) ? Ne ? "inherit": "visible": "hidden"
            }))
        }
    },
    j.prototype = {
        defaultOptions: {
            dateTimeLabelFormats: {
                millisecond: "%H:%M:%S.%L",
                second: "%H:%M:%S",
                minute: "%H:%M",
                hour: "%H:%M",
                day: "%e. %b",
                week: "%e. %b",
                month: "%b '%y",
                year: "%Y"
            },
            endOnTick: !1,
            gridLineColor: "#C0C0C0",
            labels: Qe,
            lineColor: "#C0D0E0",
            lineWidth: 1,
            minPadding: .01,
            maxPadding: .01,
            minorGridLineColor: "#E0E0E0",
            minorGridLineWidth: 1,
            minorTickColor: "#A0A0A0",
            minorTickLength: 2,
            minorTickPosition: "outside",
            startOfWeek: 1,
            startOnTick: !1,
            tickColor: "#C0D0E0",
            tickLength: 5,
            tickmarkPlacement: "between",
            tickPixelInterval: 100,
            tickPosition: "outside",
            tickWidth: 1,
            title: {
                align: "middle",
                style: {
                    color: "#4d759e",
                    fontWeight: "bold"
                }
            },
            type: "linear"
        },
        defaultYAxisOptions: {
            endOnTick: !0,
            gridLineWidth: 1,
            tickPixelInterval: 72,
            showLastLabel: !0,
            labels: {
                x: -8,
                y: 3
            },
            lineWidth: 0,
            maxPadding: .05,
            minPadding: .05,
            startOnTick: !0,
            tickWidth: 0,
            title: {
                rotation: 270,
                text: "Values"
            },
            stackLabels: {
                enabled: !1,
                formatter: function() {
                    return y(this.total, -1)
                },
                style: Qe.style
            }
        },
        defaultLeftAxisOptions: {
            labels: {
                x: -8,
                y: null
            },
            title: {
                rotation: 270
            }
        },
        defaultRightAxisOptions: {
            labels: {
                x: 8,
                y: null
            },
            title: {
                rotation: 90
            }
        },
        defaultBottomAxisOptions: {
            labels: {
                x: 0,
                y: 14
            },
            title: {
                rotation: 0
            }
        },
        defaultTopAxisOptions: {
            labels: {
                x: 0,
                y: -5
            },
            title: {
                rotation: 0
            }
        },
        init: function(t, e) {
            var i = e.isX;
            this.horiz = t.inverted ? !i: i,
            this.xOrY = (this.isXAxis = i) ? "x": "y",
            this.opposite = e.opposite,
            this.side = this.horiz ? this.opposite ? 0 : 2 : this.opposite ? 1 : 3,
            this.setOptions(e);
            var n = this.options,
            r = n.type;
            this.labelFormatter = n.labels.formatter || this.defaultLabelFormatter,
            this.userOptions = e,
            this.minPixelPadding = 0,
            this.chart = t,
            this.reversed = n.reversed,
            this.zoomEnabled = n.zoomEnabled !== !1,
            this.categories = n.categories || "category" === r,
            this.names = [],
            this.isLog = "logarithmic" === r,
            this.isDatetimeAxis = "datetime" === r,
            this.isLinked = c(n.linkedTo),
            this.tickmarkOffset = this.categories && "between" === n.tickmarkPlacement ? .5 : 0,
            this.ticks = {},
            this.minorTicks = {},
            this.plotLinesAndBands = [],
            this.alternateBands = {},
            this.len = 0,
            this.minRange = this.userMinRange = n.minRange || n.maxZoom,
            this.range = n.range,
            this.offset = n.offset || 0,
            this.stacks = {},
            this.oldStacks = {},
            this.stackExtremes = {},
            this.min = this.max = null;
            var o, n = this.options.events; - 1 === ii(this, t.axes) && (t.axes.push(this), t[i ? "xAxis": "yAxis"].push(this)),
            this.series = this.series || [],
            t.inverted && i && this.reversed === Y && (this.reversed = !0),
            this.removePlotLine = this.removePlotBand = this.removePlotBandOrLine;
            for (o in n) ai(this, o, n[o]);
            this.isLog && (this.val2lin = a, this.lin2val = l)
        },
        setOptions: function(t) {
            this.options = e(this.defaultOptions, this.isXAxis ? {}: this.defaultYAxisOptions, [this.defaultTopAxisOptions, this.defaultRightAxisOptions, this.defaultBottomAxisOptions, this.defaultLeftAxisOptions][this.side], e(V[this.isXAxis ? "xAxis": "yAxis"], t))
        },
        update: function(i, n) {
            var r = this.chart,
            i = r.options[this.xOrY + "Axis"][this.options.index] = e(this.userOptions, i);
            this.destroy(!0),
            this._addedPlotLB = this.userMin = this.userMax = Y,
            this.init(r, t(i, {
                events: Y
            })),
            r.isDirtyBox = !0,
            p(n, !0) && r.redraw()
        },
        remove: function(t) {
            var e = this.chart,
            i = this.xOrY + "Axis";
            ni(this.series,
            function(t) {
                t.remove(!1)
            }),
            h(e.axes, this),
            h(e[i], this),
            e.options[i].splice(this.options.index, 1),
            ni(e[i],
            function(t, e) {
                t.options.index = e
            }),
            this.destroy(),
            e.isDirtyBox = !0,
            p(t, !0) && e.redraw()
        },
        defaultLabelFormatter: function() {
            var t, e = this.axis,
            i = this.value,
            n = e.categories,
            r = this.dateTimeLabelFormat,
            o = V.lang.numericSymbols,
            s = o && o.length,
            a = e.options.labels.format,
            e = e.isLog ? i: e.tickInterval;
            if (a) t = b(a, this);
            else if (n) t = i;
            else if (r) t = $(r, i);
            else if (s && e >= 1e3) for (; s--&&t === Y;) n = Math.pow(1e3, s + 1),
            e >= n && null !== o[s] && (t = y(i / n, -1) + o[s]);
            return t === Y && (t = i >= 1e3 ? y(i, 0) : y(i, -1)),
            t
        },
        getSeriesExtremes: function() {
            var t = this,
            e = t.chart;
            t.hasVisibleSeries = !1,
            t.dataMin = t.dataMax = null,
            t.stackExtremes = {},
            t.buildStacks(),
            ni(t.series,
            function(i) {
                if (i.visible || !e.options.chart.ignoreHiddenSeries) {
                    var n;
                    n = i.options.threshold;
                    var r;
                    t.hasVisibleSeries = !0,
                    t.isLog && 0 >= n && (n = null),
                    t.isXAxis ? (n = i.xData, n.length && (t.dataMin = ye(p(t.dataMin, n[0]), L(n)), t.dataMax = me(p(t.dataMax, n[0]), M(n)))) : (i.getExtremes(), r = i.dataMax, i = i.dataMin, c(i) && c(r) && (t.dataMin = ye(p(t.dataMin, i), i), t.dataMax = me(p(t.dataMax, r), r)), c(n) && (t.dataMin >= n ? (t.dataMin = n, t.ignoreMinPadding = !0) : t.dataMax < n && (t.dataMax = n, t.ignoreMaxPadding = !0)))
                }
            })
        },
        translate: function(t, e, i, n, r, o) {
            var a = this.len,
            l = 1,
            h = 0,
            c = n ? this.oldTransA: this.transA,
            n = n ? this.oldMin: this.min,
            u = this.minPixelPadding,
            r = (this.options.ordinal || this.isLog && r) && this.lin2val;
            return c || (c = this.transA),
            i && (l *= -1, h = a),
            this.reversed && (l *= -1, h -= l * a),
            e ? (t = t * l + h, t -= u, t = t / c + n, r && (t = this.lin2val(t))) : (r && (t = this.val2lin(t)), "between" === o && (o = .5), t = l * (t - n) * c + h + l * u + (s(o) ? c * o * this.pointRange: 0)),
            t
        },
        toPixels: function(t, e) {
            return this.translate(t, !1, !this.horiz, null, !0) + (e ? 0 : this.pos)
        },
        toValue: function(t, e) {
            return this.translate(t - (e ? 0 : this.pos), !0, !this.horiz, null, !0)
        },
        getPlotLinePath: function(t, e, i, n) {
            var r, o, s, a, l = this.chart,
            h = this.left,
            c = this.top,
            t = this.translate(t, null, null, i),
            u = i && l.oldChartHeight || l.chartHeight,
            d = i && l.oldChartWidth || l.chartWidth;
            return r = this.transB,
            i = o = pe(t + r),
            r = s = pe(u - t - r),
            isNaN(t) ? a = !0 : this.horiz ? (r = c, s = u - this.bottom, (h > i || i > h + this.width) && (a = !0)) : (i = h, o = d - this.right, (c > r || r > c + this.height) && (a = !0)),
            a && !n ? null: l.renderer.crispLine(["M", i, r, "L", o, s], e || 0)
        },
        getPlotBandPath: function(t, e) {
            var i = this.getPlotLinePath(e),
            n = this.getPlotLinePath(t);
            return n && i ? n.push(i[4], i[5], i[1], i[2]) : n = null,
            n
        },
        getLinearTickPositions: function(t, e, i) {
            for (var n, e = E(fe(e / t) * t), i = E(ge(i / t) * t), r = []; i >= e && (r.push(e), e = E(e + t), e !== n);) n = e;
            return r
        },
        getLogTickPositions: function(t, e, i, n) {
            var r = this.options,
            o = this.len,
            s = [];
            if (n || (this._minorAutoInterval = null), t >= .5) t = pe(t),
            s = this.getLinearTickPositions(t, e, i);
            else if (t >= .08) for (var h, c, u, d, f, o = fe(e), r = t > .3 ? [1, 2, 4] : t > .15 ? [1, 2, 4, 6, 8] : [1, 2, 3, 4, 5, 6, 7, 8, 9]; i + 1 > o && !f; o++) for (c = r.length, h = 0; c > h && !f; h++) u = a(l(o) * r[h]),
            u > e && (!n || i >= d) && s.push(d),
            d > i && (f = !0),
            d = u;
            else e = l(e),
            i = l(i),
            t = r[n ? "minorTickInterval": "tickInterval"],
            t = p("auto" === t ? null: t, this._minorAutoInterval, (i - e) * (r.tickPixelInterval / (n ? 5 : 1)) / ((n ? o / this.tickPositions.length: o) || 1)),
            t = w(t, null, k(t)),
            s = si(this.getLinearTickPositions(t, e, i), a),
            n || (this._minorAutoInterval = t / 5);
            return n || (this.tickInterval = t),
            s
        },
        getMinorTickPositions: function() {
            var t, e = this.options,
            i = this.tickPositions,
            n = this.minorTickInterval,
            r = [];
            if (this.isLog) for (t = i.length, e = 1; t > e; e++) r = r.concat(this.getLogTickPositions(n, i[e - 1], i[e], !0));
            else if (this.isDatetimeAxis && "auto" === e.minorTickInterval) r = r.concat(S(T(n), this.min, this.max, e.startOfWeek)),
            r[0] < this.min && r.shift();
            else for (i = this.min + (i[0] - this.min) % n; i <= this.max; i += n) r.push(i);
            return r
        },
        adjustForMinRange: function() {
            var t, e, i, n, r, o, s = this.options,
            a = this.min,
            l = this.max,
            h = this.dataMax - this.dataMin >= this.minRange;
            if (this.isXAxis && this.minRange === Y && !this.isLog && (c(s.min) || c(s.max) ? this.minRange = null: (ni(this.series,
            function(t) {
                for (r = t.xData, i = o = t.xIncrement ? 1 : r.length - 1; i > 0; i--) n = r[i] - r[i - 1],
                (e === Y || e > n) && (e = n)
            }), this.minRange = ye(5 * e, this.dataMax - this.dataMin))), l - a < this.minRange) {
                var u = this.minRange;
                t = (u - l + a) / 2,
                t = [a - t, p(s.min, a - t)],
                h && (t[2] = this.dataMin),
                a = M(t),
                l = [a + u, p(s.max, a + u)],
                h && (l[2] = this.dataMax),
                l = L(l),
                u > l - a && (t[0] = l - u, t[1] = p(s.min, l - u), a = M(t))
            }
            this.min = a,
            this.max = l
        },
        setAxisTranslation: function(t) {
            var e, i = this.max - this.min,
            r = 0,
            o = 0,
            s = 0,
            a = this.linkedParent,
            l = this.transA;
            this.isXAxis && (a ? (o = a.minPointOffset, s = a.pointRangePadding) : ni(this.series,
            function(t) {
                var a = t.pointRange,
                l = t.options.pointPlacement,
                h = t.closestPointRange;
                a > i && (a = 0),
                r = me(r, a),
                o = me(o, n(l) ? 0 : a / 2),
                s = me(s, "on" === l ? 0 : a),
                !t.noSharedTooltip && c(h) && (e = c(e) ? ye(e, h) : h)
            }), a = this.ordinalSlope && e ? this.ordinalSlope / e: 1, this.minPointOffset = o *= a, this.pointRangePadding = s *= a, this.pointRange = ye(r, i), this.closestPointRange = e),
            t && (this.oldTransA = l),
            this.translationSlope = this.transA = l = this.len / (i + s || 1),
            this.transB = this.horiz ? this.left: this.bottom,
            this.minPixelPadding = l * o
        },
        setTickPositions: function(t) {
            var e, i = this,
            n = i.chart,
            r = i.options,
            o = i.isLog,
            s = i.isDatetimeAxis,
            l = i.isXAxis,
            h = i.isLinked,
            u = i.options.tickPositioner,
            d = r.maxPadding,
            f = r.minPadding,
            g = r.tickInterval,
            m = r.minTickInterval,
            y = r.tickPixelInterval,
            v = i.categories;
            h ? (i.linkedParent = n[l ? "xAxis": "yAxis"][r.linkedTo], n = i.linkedParent.getExtremes(), i.min = p(n.min, n.dataMin), i.max = p(n.max, n.dataMax), r.type !== i.linkedParent.options.type && N(11, 1)) : (i.min = p(i.userMin, r.min, i.dataMin), i.max = p(i.userMax, r.max, i.dataMax)),
            o && (!t && ye(i.min, p(i.dataMin, i.min)) <= 0 && N(10, 1), i.min = E(a(i.min)), i.max = E(a(i.max))),
            i.range && (i.userMin = i.min = me(i.min, i.max - i.range), i.userMax = i.max, t) && (i.range = null),
            i.beforePadding && i.beforePadding(),
            i.adjustForMinRange(),
            !v && !i.usePercentage && !h && c(i.min) && c(i.max) && (n = i.max - i.min) && (c(r.min) || c(i.userMin) || !f || !(i.dataMin < 0) && i.ignoreMinPadding || (i.min -= n * f), c(r.max) || c(i.userMax) || !d || !(i.dataMax > 0) && i.ignoreMaxPadding || (i.max += n * d)),
            i.min === i.max || void 0 === i.min || void 0 === i.max ? i.tickInterval = 1 : h && !g && y === i.linkedParent.options.tickPixelInterval ? i.tickInterval = i.linkedParent.tickInterval: (i.tickInterval = p(g, v ? 1 : (i.max - i.min) * y / me(i.len, y)), !c(g) && i.len < y && !this.isRadial && (e = !0, i.tickInterval /= 4)),
            l && !t && ni(i.series,
            function(t) {
                t.processData(i.min !== i.oldMin || i.max !== i.oldMax)
            }),
            i.setAxisTranslation(!0),
            i.beforeSetTickPositions && i.beforeSetTickPositions(),
            i.postProcessTickInterval && (i.tickInterval = i.postProcessTickInterval(i.tickInterval)),
            i.pointRange && (i.tickInterval = me(i.pointRange, i.tickInterval)),
            !g && i.tickInterval < m && (i.tickInterval = m),
            s || o || g || (i.tickInterval = w(i.tickInterval, null, k(i.tickInterval), r)),
            i.minorTickInterval = "auto" === r.minorTickInterval && i.tickInterval ? i.tickInterval / 5 : r.minorTickInterval,
            i.tickPositions = t = r.tickPositions ? [].concat(r.tickPositions) : u && u.apply(i, [i.min, i.max]),
            t || (!i.ordinalPositions && (i.max - i.min) / i.tickInterval > me(2 * i.len, 200) && N(19, !0), t = s ? (i.getNonLinearTimeTicks || S)(T(i.tickInterval, r.units), i.min, i.max, r.startOfWeek, i.ordinalPositions, i.closestPointRange, !0) : o ? i.getLogTickPositions(i.tickInterval, i.min, i.max) : i.getLinearTickPositions(i.tickInterval, i.min, i.max), e && t.splice(1, t.length - 2), i.tickPositions = t),
            h || (o = t[0], s = t[t.length - 1], h = i.minPointOffset || 0, r.startOnTick ? i.min = o: i.min - h > o && t.shift(), r.endOnTick ? i.max = s: i.max + h < s && t.pop(), 1 === t.length && (i.min -= .001, i.max += .001))
        },
        setMaxTicks: function() {
            var t = this.chart,
            e = t.maxTicks || {},
            i = this.tickPositions,
            n = this._maxTicksKey = [this.xOrY, this.pos, this.len].join("-"); ! this.isLinked && !this.isDatetimeAxis && i && i.length > (e[n] || 0) && this.options.alignTicks !== !1 && (e[n] = i.length),
            t.maxTicks = e
        },
        adjustTickAmount: function() {
            var t = this._maxTicksKey,
            e = this.tickPositions,
            i = this.chart.maxTicks;
            if (i && i[t] && !this.isDatetimeAxis && !this.categories && !this.isLinked && this.options.alignTicks !== !1) {
                var n = this.tickAmount,
                r = e.length;
                if (this.tickAmount = t = i[t], t > r) {
                    for (; e.length < t;) e.push(E(e[e.length - 1] + this.tickInterval));
                    this.transA *= (r - 1) / (t - 1),
                    this.max = e[e.length - 1]
                }
                c(n) && t !== n && (this.isDirty = !0)
            }
        },
        setScale: function() {
            var t, e, i, n, r = this.stacks;
            if (this.oldMin = this.min, this.oldMax = this.max, this.oldAxisLength = this.len, this.setAxisSize(), n = this.len !== this.oldAxisLength, ni(this.series,
            function(t) { (t.isDirtyData || t.isDirty || t.xAxis.isDirty) && (i = !0)
            }), n || i || this.isLinked || this.forceRedraw || this.userMin !== this.oldUserMin || this.userMax !== this.oldUserMax) {
                if (!this.isXAxis) for (t in r) for (e in r[t]) r[t][e].total = null,
                r[t][e].cum = 0;
                this.forceRedraw = !1,
                this.getSeriesExtremes(),
                this.setTickPositions(),
                this.oldUserMin = this.userMin,
                this.oldUserMax = this.userMax,
                this.isDirty || (this.isDirty = n || this.min !== this.oldMin || this.max !== this.oldMax)
            } else if (!this.isXAxis) {
                this.oldStacks && (r = this.stacks = this.oldStacks);
                for (t in r) for (e in r[t]) r[t][e].cum = r[t][e].total
            }
            this.setMaxTicks()
        },
        setExtremes: function(e, i, n, r, o) {
            var s = this,
            a = s.chart,
            n = p(n, !0),
            o = t(o, {
                min: e,
                max: i
            });
            hi(s, "setExtremes", o,
            function() {
                s.userMin = e,
                s.userMax = i,
                s.eventArgs = o,
                s.isDirtyExtremes = !0,
                n && a.redraw(r)
            })
        },
        zoom: function(t, e) {
            return this.allowZoomOutside || (c(this.dataMin) && t <= this.dataMin && (t = Y), c(this.dataMax) && e >= this.dataMax && (e = Y)),
            this.displayBtn = t !== Y || e !== Y,
            this.setExtremes(t, e, !1, Y, {
                trigger: "zoom"
            }),
            !0
        },
        setAxisSize: function() {
            var t, e, i = this.chart,
            n = this.options,
            r = n.offsetLeft || 0,
            o = n.offsetRight || 0,
            s = this.horiz;
            this.left = e = p(n.left, i.plotLeft + r),
            this.top = t = p(n.top, i.plotTop),
            this.width = r = p(n.width, i.plotWidth - r + o),
            this.height = n = p(n.height, i.plotHeight),
            this.bottom = i.chartHeight - n - t,
            this.right = i.chartWidth - r - e,
            this.len = me(s ? r: n, 0),
            this.pos = s ? e: t
        },
        getExtremes: function() {
            var t = this.isLog;
            return {
                min: t ? E(l(this.min)) : this.min,
                max: t ? E(l(this.max)) : this.max,
                dataMin: this.dataMin,
                dataMax: this.dataMax,
                userMin: this.userMin,
                userMax: this.userMax
            }
        },
        getThreshold: function(t) {
            var e = this.isLog,
            i = e ? l(this.min) : this.min,
            e = e ? l(this.max) : this.max;
            return i > t || null === t ? t = i: t > e && (t = e),
            this.translate(t, 0, 1, 0, 1)
        },
        addPlotBand: function(t) {
            this.addPlotBandOrLine(t, "plotBands")
        },
        addPlotLine: function(t) {
            this.addPlotBandOrLine(t, "plotLines")
        },
        addPlotBandOrLine: function(t, e) {
            var i = new z(this, t).render(),
            n = this.userOptions;
            return i && (e && (n[e] = n[e] || [], n[e].push(t)), this.plotLinesAndBands.push(i)),
            i
        },
        autoLabelAlign: function(t) {
            return t = (p(t, 0) - 90 * this.side + 720) % 360,
            t > 15 && 165 > t ? "right": t > 195 && 345 > t ? "left": "center"
        },
        getOffset: function() {
            var t, e, i, n, r, o, s, a = this,
            l = a.chart,
            h = l.renderer,
            u = a.options,
            d = a.tickPositions,
            f = a.ticks,
            g = a.horiz,
            m = a.side,
            y = l.inverted ? [1, 0, 3, 2][m] : m,
            v = 0,
            x = 0,
            b = u.title,
            k = u.labels,
            w = 0,
            T = l.axisOffset,
            S = l.clipOffset,
            C = [ - 1, 1, 1, -1][m],
            A = 1,
            L = p(k.maxStaggerLines, 5);
            if (a.hasData = t = a.hasVisibleSeries || c(a.min) && c(a.max) && !!d, a.showAxis = l = t || p(u.showEmpty, !0), a.staggerLines = a.horiz && k.staggerLines, a.axisGroup || (a.gridGroup = h.g("grid").attr({
                zIndex: u.gridZIndex || 1
            }).add(), a.axisGroup = h.g("axis").attr({
                zIndex: u.zIndex || 2
            }).add(), a.labelGroup = h.g("axis-labels").attr({
                zIndex: k.zIndex || 7
            }).add()), t || a.isLinked) {
                if (a.labelAlign = p(k.align || a.autoLabelAlign(k.rotation)), ni(d,
                function(t) {
                    f[t] ? f[t].addLabel() : f[t] = new O(a, t)
                }), a.horiz && !a.staggerLines && L && !k.rotation) {
                    for (i = a.reversed ? [].concat(d).reverse() : d; L > A;) {
                        for (t = [], n = !1, k = 0; k < i.length; k++) r = i[k],
                        o = (o = f[r].label && f[r].label.getBBox()) ? o.width: 0,
                        s = k % A,
                        o && (r = a.translate(r), t[s] !== Y && r < t[s] && (n = !0), t[s] = r + o);
                        if (!n) break;
                        A++
                    }
                    A > 1 && (a.staggerLines = A)
                }
                ni(d,
                function(t) { (0 === m || 2 === m || {
                        1 : "left",
                        3 : "right"
                    } [m] === a.labelAlign) && (w = me(f[t].getLabelSize(), w))
                }),
                a.staggerLines && (w *= a.staggerLines, a.labelOffset = w)
            } else for (i in f) f[i].destroy(),
            delete f[i];
            b && b.text && b.enabled !== !1 && (a.axisTitle || (a.axisTitle = h.text(b.text, 0, 0, b.useHTML).attr({
                zIndex: 7,
                rotation: b.rotation || 0,
                align: b.textAlign || {
                    low: "left",
                    middle: "center",
                    high: "right"
                } [b.align]
            }).css(b.style).add(a.axisGroup), a.axisTitle.isNew = !0), l && (v = a.axisTitle.getBBox()[g ? "height": "width"], x = p(b.margin, g ? 5 : 10), e = b.offset), a.axisTitle[l ? "show": "hide"]()),
            a.offset = C * p(u.offset, T[m]),
            a.axisTitleMargin = p(e, w + x + (2 !== m && w && C * u.labels[g ? "y": "x"])),
            T[m] = me(T[m], a.axisTitleMargin + v + C * a.offset),
            S[y] = me(S[y], 2 * fe(u.lineWidth / 2))
        },
        getLinePath: function(t) {
            var e = this.chart,
            i = this.opposite,
            n = this.offset,
            r = this.horiz,
            o = this.left + (i ? this.width: 0) + n,
            n = e.chartHeight - this.bottom - (i ? this.height: 0) + n;
            return i && (t *= -1),
            e.renderer.crispLine(["M", r ? this.left: o, r ? n: this.top, "L", r ? e.chartWidth - this.right: o, r ? n: e.chartHeight - this.bottom], t)
        },
        getTitlePosition: function() {
            var t = this.horiz,
            e = this.left,
            n = this.top,
            r = this.len,
            o = this.options.title,
            s = t ? e: n,
            a = this.opposite,
            l = this.offset,
            h = i(o.style.fontSize || 12),
            r = {
                low: s + (t ? 0 : r),
                middle: s + r / 2,
                high: s + (t ? r: 0)
            } [o.align],
            e = (t ? n + this.height: e) + (t ? 1 : -1) * (a ? -1 : 1) * this.axisTitleMargin + (2 === this.side ? h: 0);
            return {
                x: t ? r: e + (a ? this.width: 0) + l + (o.x || 0),
                y: t ? e - (a ? this.height: 0) + l: r + (o.y || 0)
            }
        },
        render: function() {
            var t, e = this,
            i = e.chart,
            n = i.renderer,
            r = e.options,
            o = e.isLog,
            s = e.isLinked,
            a = e.tickPositions,
            h = e.axisTitle,
            u = e.stacks,
            d = e.ticks,
            p = e.minorTicks,
            f = e.alternateBands,
            g = r.stackLabels,
            m = r.alternateGridColor,
            y = e.tickmarkOffset,
            v = r.lineWidth,
            x = i.hasRendered && c(e.oldMin) && !isNaN(e.oldMin);
            t = e.hasData;
            var b, k, w = e.showAxis;
            if (ni([d, p, f],
            function(t) {
                for (var e in t) t[e].isActive = !1
            }), (t || s) && (e.minorTickInterval && !e.categories && ni(e.getMinorTickPositions(),
            function(t) {
                p[t] || (p[t] = new O(e, t, "minor")),
                x && p[t].isNew && p[t].render(null, !0),
                p[t].render(null, !1, 1)
            }), a.length && (ni(a.slice(1).concat([a[0]]),
            function(t, i) {
                i = i === a.length - 1 ? 0 : i + 1,
                (!s || t >= e.min && t <= e.max) && (d[t] || (d[t] = new O(e, t)), x && d[t].isNew && d[t].render(i, !0), d[t].render(i, !1, 1))
            }), y && 0 === e.min && (d[ - 1] || (d[ - 1] = new O(e, -1, null, !0)), d[ - 1].render( - 1))), m && ni(a,
            function(t, i) {
                i % 2 === 0 && t < e.max && (f[t] || (f[t] = new z(e)), b = t + y, k = a[i + 1] !== Y ? a[i + 1] + y: e.max, f[t].options = {
                    from: o ? l(b) : b,
                    to: o ? l(k) : k,
                    color: m
                },
                f[t].render(), f[t].isActive = !0)
            }), e._addedPlotLB || (ni((r.plotLines || []).concat(r.plotBands || []),
            function(t) {
                e.addPlotBandOrLine(t)
            }), e._addedPlotLB = !0)), ni([d, p, f],
            function(t) {
                var e, n, r = [],
                o = U ? U.duration || 500 : 0,
                s = function() {
                    for (n = r.length; n--;) t[r[n]] && !t[r[n]].isActive && (t[r[n]].destroy(), delete t[r[n]])
                };
                for (e in t) t[e].isActive || (t[e].render(e, !1, 0), t[e].isActive = !1, r.push(e));
                t !== f && i.hasRendered && o ? o && setTimeout(s, o) : s()
            }), v && (t = e.getLinePath(v), e.axisLine ? e.axisLine.animate({
                d: t
            }) : e.axisLine = n.path(t).attr({
                stroke: r.lineColor,
                "stroke-width": v,
                zIndex: 7
            }).add(e.axisGroup), e.axisLine[w ? "show": "hide"]()), h && w && (h[h.isNew ? "attr": "animate"](e.getTitlePosition()), h.isNew = !1), g && g.enabled) {
                var T, S, r = e.stackTotalGroup;
                r || (e.stackTotalGroup = r = n.g("stack-labels").attr({
                    visibility: "visible",
                    zIndex: 6
                }).add()),
                r.translate(i.plotLeft, i.plotTop);
                for (T in u) for (S in n = u[T]) n[S].render(r)
            }
            e.isDirty = !1
        },
        removePlotBandOrLine: function(t) {
            for (var e = this.plotLinesAndBands,
            i = this.options,
            n = this.userOptions,
            r = e.length; r--;) e[r].id === t && e[r].destroy();
            ni([i.plotLines || [], n.plotLines || [], i.plotBands || [], n.plotBands || []],
            function(e) {
                for (r = e.length; r--;) e[r].id === t && h(e, e[r])
            })
        },
        setTitle: function(t, e) {
            this.update({
                title: t
            },
            e)
        },
        redraw: function() {
            var t = this.chart.pointer;
            t.reset && t.reset(!0),
            this.render(),
            ni(this.plotLinesAndBands,
            function(t) {
                t.render()
            }),
            ni(this.series,
            function(t) {
                t.isDirty = !0
            })
        },
        buildStacks: function() {
            var t = this.series,
            e = t.length;
            if (!this.isXAxis) {
                for (; e--;) t[e].setStackedPoints();
                if (this.usePercentage) for (e = 0; e < t.length; e++) t[e].setPercentStacks()
            }
        },
        setCategories: function(t, e) {
            this.update({
                categories: t
            },
            e)
        },
        destroy: function(t) {
            var e, i = this,
            n = i.stacks,
            r = i.plotLinesAndBands;
            t || li(i);
            for (e in n) P(n[e]),
            n[e] = null;
            for (ni([i.ticks, i.minorTicks, i.alternateBands],
            function(t) {
                P(t)
            }), t = r.length; t--;) r[t].destroy();
            ni("stackTotalGroup,axisLine,axisGroup,gridGroup,labelGroup,axisTitle".split(","),
            function(t) {
                i[t] && (i[t] = i[t].destroy())
            })
        }
    },
    W.prototype = {
        init: function(t, e) {
            var n = e.borderWidth,
            r = e.style,
            o = i(r.padding);
            this.chart = t,
            this.options = e,
            this.crosshairs = [],
            this.now = {
                x: 0,
                y: 0
            },
            this.isHidden = !0,
            this.label = t.renderer.label("", 0, 0, e.shape, null, null, e.useHTML, null, "tooltip").attr({
                padding: o,
                fill: e.backgroundColor,
                "stroke-width": n,
                r: e.borderRadius,
                zIndex: 8
            }).css(r).css({
                padding: 0
            }).add().attr({
                y: -999
            }),
            He || this.label.shadow(e.shadow),
            this.shared = e.shared
        },
        destroy: function() {
            ni(this.crosshairs,
            function(t) {
                t && t.destroy()
            }),
            this.label && (this.label = this.label.destroy()),
            clearTimeout(this.hideTimer),
            clearTimeout(this.tooltipTimeout)
        },
        move: function(e, i, n, r) {
            var o = this,
            s = o.now,
            a = o.options.animation !== !1 && !o.isHidden;
            t(s, {
                x: a ? (2 * s.x + e) / 3 : e,
                y: a ? (s.y + i) / 2 : i,
                anchorX: a ? (2 * s.anchorX + n) / 3 : n,
                anchorY: a ? (s.anchorY + r) / 2 : r
            }),
            o.label.attr(s),
            a && (ve(e - s.x) > 1 || ve(i - s.y) > 1) && (clearTimeout(this.tooltipTimeout), this.tooltipTimeout = setTimeout(function() {
                o && o.move(e, i, n, r)
            },
            32))
        },
        hide: function() {
            var t, e = this;
            clearTimeout(this.hideTimer),
            this.isHidden || (t = this.chart.hoverPoints, this.hideTimer = setTimeout(function() {
                e.label.fadeOut(),
                e.isHidden = !0
            },
            p(this.options.hideDelay, 500)), t && ni(t,
            function(t) {
                t.setState()
            }), this.chart.hoverPoints = null)
        },
        hideCrosshairs: function() {
            ni(this.crosshairs,
            function(t) {
                t && t.hide()
            })
        },
        getAnchor: function(t, e) {
            var i, n, r = this.chart,
            o = r.inverted,
            s = r.plotTop,
            a = 0,
            l = 0,
            t = d(t);
            return i = t[0].tooltipPos,
            this.followPointer && e && (e.chartX === Y && (e = r.pointer.normalize(e)), i = [e.chartX - r.plotLeft, e.chartY - s]),
            i || (ni(t,
            function(t) {
                n = t.series.yAxis,
                a += t.plotX,
                l += (t.plotLow ? (t.plotLow + t.plotHigh) / 2 : t.plotY) + (!o && n ? n.top - s: 0)
            }), a /= t.length, l /= t.length, i = [o ? r.plotWidth - l: a, this.shared && !o && t.length > 1 && e ? e.chartY - s: o ? r.plotHeight - a: l]),
            si(i, pe)
        },
        getPosition: function(t, e, i) {
            var n, r = this.chart,
            o = r.plotLeft,
            s = r.plotTop,
            a = r.plotWidth,
            l = r.plotHeight,
            h = p(this.options.distance, 12),
            c = i.plotX,
            i = i.plotY,
            r = c + o + (r.inverted ? h: -t - h),
            u = i - e + s + 15;
            return 7 > r && (r = o + me(c, 0) + h),
            r + t > o + a && (r -= r + t - (o + a), u = i - e + s - h, n = !0),
            s + 5 > u && (u = s + 5, n && i >= u && u + e >= i && (u = i + s + h)),
            u + e > s + l && (u = me(s, s + l - e - h)),
            {
                x: r,
                y: u
            }
        },
        defaultFormatter: function(t) {
            var e, i = this.points || d(this),
            n = i[0].series;
            return e = [n.tooltipHeaderFormatter(i[0])],
            ni(i,
            function(t) {
                n = t.series,
                e.push(n.tooltipFormatter && n.tooltipFormatter(t) || t.point.tooltipFormatter(n.tooltipOptions.pointFormat))
            }),
            e.push(t.options.footerFormat || ""),
            e.join("")
        },
        refresh: function(t, e) {
            var i, n, r, o = this.chart,
            s = this.label,
            l = this.options,
            h = {},
            c = [];
            r = l.formatter || this.defaultFormatter;
            var u, h = o.hoverPoints,
            f = l.crosshairs,
            g = this.shared;
            if (clearTimeout(this.hideTimer), this.followPointer = d(t)[0].series.tooltipOptions.followPointer, n = this.getAnchor(t, e), i = n[0], n = n[1], !g || t.series && t.series.noSharedTooltip ? h = t.getLabelConfig() : (o.hoverPoints = t, h && ni(h,
            function(t) {
                t.setState()
            }), ni(t,
            function(t) {
                t.setState("hover"),
                c.push(t.getLabelConfig())
            }), h = {
                x: t[0].category,
                y: t[0].y
            },
            h.points = c, t = t[0]), r = r.call(h, this), h = t.series, r === !1 ? this.hide() : (this.isHidden && (di(s), s.attr("opacity", 1).show()), s.attr({
                text: r
            }), u = l.borderColor || t.color || h.color || "#606060", s.attr({
                stroke: u
            }), this.updatePosition({
                plotX: i,
                plotY: n
            }), this.isHidden = !1), f) for (f = d(f), s = f.length; s--;) g = t.series,
            l = g[s ? "yAxis": "xAxis"],
            f[s] && l && (h = s ? p(t.stackY, t.y) : t.x, l.isLog && (h = a(h)), 1 === s && g.modifyValue && (h = g.modifyValue(h)), l = l.getPlotLinePath(h, 1), this.crosshairs[s] ? this.crosshairs[s].attr({
                d: l,
                visibility: "visible"
            }) : (h = {
                "stroke-width": f[s].width || 1,
                stroke: f[s].color || "#C0C0C0",
                zIndex: f[s].zIndex || 2
            },
            f[s].dashStyle && (h.dashstyle = f[s].dashStyle), this.crosshairs[s] = o.renderer.path(l).attr(h).add()));
            hi(o, "tooltipRefresh", {
                text: r,
                x: i + o.plotLeft,
                y: n + o.plotTop,
                borderColor: u
            })
        },
        updatePosition: function(t) {
            var e = this.chart,
            i = this.label,
            i = (this.options.positioner || this.getPosition).call(this, i.width, i.height, t);
            this.move(pe(i.x), pe(i.y), t.plotX + e.plotLeft, t.plotY + e.plotTop)
        }
    },
    F.prototype = {
        init: function(t, e) {
            var i, n = e.chart,
            r = n.events,
            o = He ? "": n.zoomType,
            n = t.inverted;
            this.options = e,
            this.chart = t,
            this.zoomX = i = /x/.test(o),
            this.zoomY = o = /y/.test(o),
            this.zoomHor = i && !n || o && n,
            this.zoomVert = o && !n || i && n,
            this.runChartClick = r && !!r.click,
            this.pinchDown = [],
            this.lastValidTouch = {},
            e.tooltip.enabled && (t.tooltip = new W(t, e.tooltip)),
            this.setDOMEvents()
        },
        normalize: function(e, i) {
            var n, r, e = e || ue.event;
            return e.target || (e.target = e.srcElement),
            e = ci(e),
            r = e.touches ? e.touches.item(0) : e,
            i || (this.chartPosition = i = oi(this.chart.container)),
            r.pageX === Y ? (n = me(e.x, e.clientX - i.left), r = e.y) : (n = r.pageX - i.left, r = r.pageY - i.top),
            t(e, {
                chartX: pe(n),
                chartY: pe(r)
            })
        },
        getCoordinates: function(t) {
            var e = {
                xAxis: [],
                yAxis: []
            };
            return ni(this.chart.axes,
            function(i) {
                e[i.isXAxis ? "xAxis": "yAxis"].push({
                    axis: i,
                    value: i.toValue(t[i.horiz ? "chartX": "chartY"])
                })
            }),
            e
        },
        getIndex: function(t) {
            var e = this.chart;
            return e.inverted ? e.plotHeight + e.plotTop - t.chartY: t.chartX - e.plotLeft
        },
        runPointActions: function(t) {
            var e, i, n, r = this.chart,
            o = r.series,
            s = r.tooltip,
            a = r.hoverPoint,
            l = r.hoverSeries,
            h = r.chartWidth,
            c = this.getIndex(t);
            if (s && this.options.tooltip.shared && (!l || !l.noSharedTooltip)) {
                for (e = [], i = o.length, n = 0; i > n; n++) o[n].visible && o[n].options.enableMouseTracking !== !1 && !o[n].noSharedTooltip && o[n].tooltipPoints.length && (r = o[n].tooltipPoints[c]) && r.series && (r._dist = ve(c - r.clientX), h = ye(h, r._dist), e.push(r));
                for (i = e.length; i--;) e[i]._dist > h && e.splice(i, 1);
                e.length && e[0].clientX !== this.hoverX && (s.refresh(e, t), this.hoverX = e[0].clientX)
            }
            l && l.tracker ? (r = l.tooltipPoints[c]) && r !== a && r.onMouseOver(t) : s && s.followPointer && !s.isHidden && (t = s.getAnchor([{}], t), s.updatePosition({
                plotX: t[0],
                plotY: t[1]
            }))
        },
        reset: function(t) {
            var e = this.chart,
            i = e.hoverSeries,
            n = e.hoverPoint,
            r = e.tooltip,
            e = r && r.shared ? e.hoverPoints: n; (t = t && r && e) && d(e)[0].plotX === Y && (t = !1),
            t ? r.refresh(e) : (n && n.onMouseOut(), i && i.onMouseOut(), r && (r.hide(), r.hideCrosshairs()), this.hoverX = null)
        },
        scaleGroups: function(t, e) {
            var i, n = this.chart;
            ni(n.series,
            function(r) {
                i = t || r.getPlotBox(),
                r.xAxis && r.xAxis.zoomEnabled && (r.group.attr(i), r.markerGroup && (r.markerGroup.attr(i), r.markerGroup.clip(e ? n.clipRect: null)), r.dataLabelsGroup && r.dataLabelsGroup.attr(i))
            }),
            n.clipRect.attr(e || n.clipBox)
        },
        pinchTranslate: function(t, e, i, n, r, o, s, a) {
            t && this.pinchTranslateDirection(!0, i, n, r, o, s, a),
            e && this.pinchTranslateDirection(!1, i, n, r, o, s, a)
        },
        pinchTranslateDirection: function(t, e, i, n, r, o, s, a) {
            var l, h, c, u = this.chart,
            d = t ? "x": "y",
            p = t ? "X": "Y",
            f = "chart" + p,
            g = t ? "width": "height",
            m = u["plot" + (t ? "Left": "Top")],
            y = a || 1,
            v = u.inverted,
            x = u.bounds[t ? "h": "v"],
            b = 1 === e.length,
            k = e[0][f],
            w = i[0][f],
            T = !b && e[1][f],
            S = !b && i[1][f],
            i = function() { ! b && ve(k - T) > 20 && (y = a || ve(w - S) / ve(k - T)),
                h = (m - w) / y + k,
                l = u["plot" + (t ? "Width": "Height")] / y
            };
            i(),
            e = h,
            e < x.min ? (e = x.min, c = !0) : e + l > x.max && (e = x.max - l, c = !0),
            c ? (w -= .8 * (w - s[d][0]), b || (S -= .8 * (S - s[d][1])), i()) : s[d] = [w, S],
            v || (o[d] = h - m, o[g] = l),
            o = v ? 1 / y: y,
            r[g] = l,
            r[d] = e,
            n[v ? t ? "scaleY": "scaleX": "scale" + p] = y,
            n["translate" + p] = o * m + (w - o * k)
        },
        pinch: function(e) {
            var i = this,
            n = i.chart,
            r = i.pinchDown,
            o = n.tooltip && n.tooltip.options.followTouchMove,
            s = e.touches,
            a = s.length,
            l = i.lastValidTouch,
            h = i.zoomHor || i.pinchHor,
            c = i.zoomVert || i.pinchVert,
            u = h || c,
            d = i.selectionMarker,
            p = {},
            f = 1 === a && (i.inClass(e.target, "highcharts-tracker") && n.runTrackerClick || n.runChartClick),
            g = {}; (u || o) && !f && e.preventDefault(),
            si(s,
            function(t) {
                return i.normalize(t)
            }),
            "touchstart" === e.type ? (ni(s,
            function(t, e) {
                r[e] = {
                    chartX: t.chartX,
                    chartY: t.chartY
                }
            }), l.x = [r[0].chartX, r[1] && r[1].chartX], l.y = [r[0].chartY, r[1] && r[1].chartY], ni(n.axes,
            function(t) {
                if (t.zoomEnabled) {
                    var e = n.bounds[t.horiz ? "h": "v"],
                    i = t.minPixelPadding,
                    r = t.toPixels(t.dataMin),
                    o = t.toPixels(t.dataMax),
                    s = ye(r, o),
                    r = me(r, o);
                    e.min = ye(t.pos, s - i),
                    e.max = me(t.pos + t.len, r + i)
                }
            })) : r.length && (d || (i.selectionMarker = d = t({
                destroy: ze
            },
            n.plotBox)), i.pinchTranslate(h, c, r, s, p, d, g, l), i.hasPinched = u, i.scaleGroups(p, g), !u && o && 1 === a && this.runPointActions(i.normalize(e)))
        },
        dragStart: function(t) {
            var e = this.chart;
            e.mouseIsDown = t.type,
            e.cancelClick = !1,
            e.mouseDownX = this.mouseDownX = t.chartX,
            e.mouseDownY = this.mouseDownY = t.chartY
        },
        drag: function(t) {
            var e, i = this.chart,
            n = i.options.chart,
            r = t.chartX,
            o = t.chartY,
            s = this.zoomHor,
            a = this.zoomVert,
            l = i.plotLeft,
            h = i.plotTop,
            c = i.plotWidth,
            u = i.plotHeight,
            d = this.mouseDownX,
            p = this.mouseDownY;
            l > r ? r = l: r > l + c && (r = l + c),
            h > o ? o = h: o > h + u && (o = h + u),
            this.hasDragged = Math.sqrt(Math.pow(d - r, 2) + Math.pow(p - o, 2)),
            this.hasDragged > 10 && (e = i.isInsidePlot(d - l, p - h), i.hasCartesianSeries && (this.zoomX || this.zoomY) && e && !this.selectionMarker && (this.selectionMarker = i.renderer.rect(l, h, s ? 1 : c, a ? 1 : u, 0).attr({
                fill: n.selectionMarkerFill || "rgba(69,114,167,0.25)",
                zIndex: 7
            }).add()), this.selectionMarker && s && (r -= d, this.selectionMarker.attr({
                width: ve(r),
                x: (r > 0 ? 0 : r) + d
            })), this.selectionMarker && a && (r = o - p, this.selectionMarker.attr({
                height: ve(r),
                y: (r > 0 ? 0 : r) + p
            })), e && !this.selectionMarker && n.panning && i.pan(t, n.panning))
        },
        drop: function(e) {
            var i = this.chart,
            n = this.hasPinched;
            if (this.selectionMarker) {
                var r, o = {
                    xAxis: [],
                    yAxis: [],
                    originalEvent: e.originalEvent || e
                },
                s = this.selectionMarker,
                a = s.x,
                l = s.y; (this.hasDragged || n) && (ni(i.axes,
                function(t) {
                    if (t.zoomEnabled) {
                        var e = t.horiz,
                        i = t.toValue(e ? a: l),
                        e = t.toValue(e ? a + s.width: l + s.height); ! isNaN(i) && !isNaN(e) && (o[t.xOrY + "Axis"].push({
                            axis: t,
                            min: ye(i, e),
                            max: me(i, e)
                        }), r = !0)
                    }
                }), r && hi(i, "selection", o,
                function(e) {
                    i.zoom(t(e, n ? {
                        animation: !1
                    }: null))
                })),
                this.selectionMarker = this.selectionMarker.destroy(),
                n && this.scaleGroups()
            }
            i && (f(i.container, {
                cursor: i._cursor
            }), i.cancelClick = this.hasDragged > 10, i.mouseIsDown = this.hasDragged = this.hasPinched = !1, this.pinchDown = [])
        },
        onContainerMouseDown: function(t) {
            t = this.normalize(t),
            t.preventDefault && t.preventDefault(),
            this.dragStart(t)
        },
        onDocumentMouseUp: function(t) {
            this.drop(t)
        },
        onDocumentMouseMove: function(t) {
            var e = this.chart,
            i = this.chartPosition,
            n = e.hoverSeries,
            t = this.normalize(t, i);
            i && n && !this.inClass(t.target, "highcharts-tracker") && !e.isInsidePlot(t.chartX - e.plotLeft, t.chartY - e.plotTop) && this.reset()
        },
        onContainerMouseLeave: function() {
            this.reset(),
            this.chartPosition = null
        },
        onContainerMouseMove: function(t) {
            var e = this.chart,
            t = this.normalize(t);
            t.returnValue = !1,
            "mousedown" === e.mouseIsDown && this.drag(t),
            (this.inClass(t.target, "highcharts-tracker") || e.isInsidePlot(t.chartX - e.plotLeft, t.chartY - e.plotTop)) && !e.openMenu && this.runPointActions(t)
        },
        inClass: function(t, e) {
            for (var i; t;) {
                if (i = u(t, "class")) {
                    if ( - 1 !== i.indexOf(e)) return ! 0;
                    if ( - 1 !== i.indexOf("highcharts-container")) return ! 1
                }
                t = t.parentNode
            }
        },
        onTrackerMouseOut: function(t) {
            var e = this.chart.hoverSeries; ! e || e.options.stickyTracking || this.inClass(t.toElement || t.relatedTarget, "highcharts-tooltip") || e.onMouseOut()
        },
        onContainerClick: function(e) {
            var i, n, r, o = this.chart,
            s = o.hoverPoint,
            a = o.plotLeft,
            l = o.plotTop,
            h = o.inverted,
            e = this.normalize(e);
            e.cancelBubble = !0,
            o.cancelClick || (s && this.inClass(e.target, "highcharts-tracker") ? (i = this.chartPosition, n = s.plotX, r = s.plotY, t(s, {
                pageX: i.left + a + (h ? o.plotWidth - r: n),
                pageY: i.top + l + (h ? o.plotHeight - n: r)
            }), hi(s.series, "click", t(e, {
                point: s
            })), o.hoverPoint && s.firePointEvent("click", e)) : (t(e, this.getCoordinates(e)), o.isInsidePlot(e.chartX - a, e.chartY - l) && hi(o, "click", e)))
        },
        onContainerTouchStart: function(t) {
            var e = this.chart;
            1 === t.touches.length ? (t = this.normalize(t), e.isInsidePlot(t.chartX - e.plotLeft, t.chartY - e.plotTop) ? (this.runPointActions(t), this.pinch(t)) : this.reset()) : 2 === t.touches.length && this.pinch(t)
        },
        onContainerTouchMove: function(t) { (1 === t.touches.length || 2 === t.touches.length) && this.pinch(t)
        },
        onDocumentTouchEnd: function(t) {
            this.drop(t)
        },
        setDOMEvents: function() {
            var t, e = this,
            i = e.chart.container;
            this._events = t = [[i, "onmousedown", "onContainerMouseDown"], [i, "onmousemove", "onContainerMouseMove"], [i, "onclick", "onContainerClick"], [i, "mouseleave", "onContainerMouseLeave"], [ce, "mousemove", "onDocumentMouseMove"], [ce, "mouseup", "onDocumentMouseUp"]],
            Ie && t.push([i, "ontouchstart", "onContainerTouchStart"], [i, "ontouchmove", "onContainerTouchMove"], [ce, "touchend", "onDocumentTouchEnd"]),
            ni(t,
            function(t) {
                e["_" + t[2]] = function(i) {
                    e[t[2]](i)
                },
                0 === t[1].indexOf("on") ? t[0][t[1]] = e["_" + t[2]] : ai(t[0], t[1], e["_" + t[2]])
            })
        },
        destroy: function() {
            var t = this;
            ni(t._events,
            function(e) {
                0 === e[1].indexOf("on") ? e[0][e[1]] = null: li(e[0], e[1], t["_" + e[2]])
            }),
            delete t._events,
            clearInterval(t.tooltipTimeout)
        }
    },
    _.prototype = {
        init: function(t, n) {
            var r = this,
            o = n.itemStyle,
            s = p(n.padding, 8),
            a = n.itemMarginTop || 0;
            this.options = n,
            n.enabled && (r.baseline = i(o.fontSize) + 3 + a, r.itemStyle = o, r.itemHiddenStyle = e(o, n.itemHiddenStyle), r.itemMarginTop = a, r.padding = s, r.initialItemX = s, r.initialItemY = s - 5, r.maxItemWidth = 0, r.chart = t, r.itemHeight = 0, r.lastLineHeight = 0, r.render(), ai(r.chart, "endResize",
            function() {
                r.positionCheckboxes()
            }))
        },
        colorizeItem: function(t, e) {
            var i, n = this.options,
            r = t.legendItem,
            o = t.legendLine,
            s = t.legendSymbol,
            a = this.itemHiddenStyle.color,
            n = e ? n.itemStyle.color: a,
            l = e ? t.color: a,
            a = t.options && t.options.marker,
            h = {
                stroke: l,
                fill: l
            };
            if (r && r.css({
                fill: n,
                color: n
            }), o && o.attr({
                stroke: l
            }), s) {
                if (a && s.isMarker) for (i in a = t.convertAttribs(a)) r = a[i],
                r !== Y && (h[i] = r);
                s.attr(h)
            }
        },
        positionItem: function(t) {
            var e = this.options,
            i = e.symbolPadding,
            e = !e.rtl,
            n = t._legendItemPos,
            r = n[0],
            n = n[1],
            o = t.checkbox;
            t.legendGroup && t.legendGroup.translate(e ? r: this.legendWidth - r - 2 * i - 4, n),
            o && (o.x = r, o.y = n)
        },
        destroyItem: function(t) {
            var e = t.checkbox;
            ni(["legendItem", "legendLine", "legendSymbol", "legendGroup"],
            function(e) {
                t[e] && (t[e] = t[e].destroy())
            }),
            e && D(t.checkbox)
        },
        destroy: function() {
            var t = this.group,
            e = this.box;
            e && (this.box = e.destroy()),
            t && (this.group = t.destroy())
        },
        positionCheckboxes: function(t) {
            var e, i = this.group.alignAttr,
            n = this.clipHeight || this.legendHeight;
            i && (e = i.translateY, ni(this.allItems,
            function(r) {
                var o, s = r.checkbox;
                s && (o = e + s.y + (t || 0) + 3, f(s, {
                    left: i.translateX + r.legendItemWidth + s.x - 20 + "px",
                    top: o + "px",
                    display: o > e - 6 && e + n - 6 > o ? "": We
                }))
            }))
        },
        renderTitle: function() {
            var t = this.padding,
            e = this.options.title,
            i = 0;
            e.text && (this.title || (this.title = this.chart.renderer.label(e.text, t - 3, t - 4, null, null, null, null, null, "legend-title").attr({
                zIndex: 1
            }).css(e.style).add(this.group)), t = this.title.getBBox(), i = t.height, this.offsetWidth = t.width, this.contentGroup.attr({
                translateY: i
            })),
            this.titleHeight = i
        },
        renderItem: function(t) {
            var i, n = this,
            r = n.chart,
            o = r.renderer,
            s = n.options,
            a = "horizontal" === s.layout,
            l = s.symbolWidth,
            h = s.symbolPadding,
            c = n.itemStyle,
            u = n.itemHiddenStyle,
            d = n.padding,
            f = a ? p(s.itemDistance, 8) : 0,
            m = !s.rtl,
            y = s.width,
            v = s.itemMarginBottom || 0,
            x = n.itemMarginTop,
            k = n.initialItemX,
            w = t.legendItem,
            T = t.series || t,
            S = T.options,
            C = S.showCheckbox,
            A = s.useHTML; ! w && (t.legendGroup = o.g("legend-item").attr({
                zIndex: 1
            }).add(n.scrollGroup), T.drawLegendSymbol(n, t), t.legendItem = w = o.text(s.labelFormat ? b(s.labelFormat, t) : s.labelFormatter.call(t), m ? l + h: -h, n.baseline, A).css(e(t.visible ? c: u)).attr({
                align: m ? "left": "right",
                zIndex: 2
            }).add(t.legendGroup), (A ? w: t.legendGroup).on("mouseover",
            function() {
                t.setState("hover"),
                w.css(n.options.itemHoverStyle)
            }).on("mouseout",
            function() {
                w.css(t.visible ? c: u),
                t.setState()
            }).on("click",
            function(e) {
                var i = function() {
                    t.setVisible()
                },
                e = {
                    browserEvent: e
                };
                t.firePointEvent ? t.firePointEvent("legendItemClick", e, i) : hi(t, "legendItemClick", e, i)
            }), n.colorizeItem(t, t.visible), S && C) && (t.checkbox = g("input", {
                type: "checkbox",
                checked: t.selected,
                defaultChecked: t.selected
            },
            s.itemCheckboxStyle, r.container), ai(t.checkbox, "click",
            function(e) {
                hi(t, "checkboxClick", {
                    checked: e.target.checked
                },
                function() {
                    t.select()
                })
            })),
            o = w.getBBox(),
            i = t.legendItemWidth = s.itemWidth || l + h + o.width + f + (C ? 20 : 0),
            s = i,
            n.itemHeight = l = o.height,
            a && n.itemX - k + s > (y || r.chartWidth - 2 * d - k) && (n.itemX = k, n.itemY += x + n.lastLineHeight + v, n.lastLineHeight = 0),
            n.maxItemWidth = me(n.maxItemWidth, s),
            n.lastItemY = x + n.itemY + v,
            n.lastLineHeight = me(l, n.lastLineHeight),
            t._legendItemPos = [n.itemX, n.itemY],
            a ? n.itemX += s: (n.itemY += x + l + v, n.lastLineHeight = l),
            n.offsetWidth = y || me((a ? n.itemX - k - f: s) + d, n.offsetWidth)
        },
        render: function() {
            var e, i, n, r, o = this,
            s = o.chart,
            a = s.renderer,
            l = o.group,
            h = o.box,
            c = o.options,
            u = o.padding,
            d = c.borderWidth,
            f = c.backgroundColor;
            o.itemX = o.initialItemX,
            o.itemY = o.initialItemY,
            o.offsetWidth = 0,
            o.lastItemY = 0,
            l || (o.group = l = a.g("legend").attr({
                zIndex: 7
            }).add(), o.contentGroup = a.g().attr({
                zIndex: 1
            }).add(l), o.scrollGroup = a.g().add(o.contentGroup)),
            o.renderTitle(),
            e = [],
            ni(s.series,
            function(t) {
                var i = t.options;
                p(i.showInLegend, i.linkedTo === Y ? Y: !1, !0) && (e = e.concat(t.legendItems || ("point" === i.legendType ? t.data: t)))
            }),
            A(e,
            function(t, e) {
                return (t.options && t.options.legendIndex || 0) - (e.options && e.options.legendIndex || 0)
            }),
            c.reversed && e.reverse(),
            o.allItems = e,
            o.display = i = !!e.length,
            ni(e,
            function(t) {
                o.renderItem(t)
            }),
            n = c.width || o.offsetWidth,
            r = o.lastItemY + o.lastLineHeight + o.titleHeight,
            r = o.handleOverflow(r),
            (d || f) && (n += u, r += u, h ? n > 0 && r > 0 && (h[h.isNew ? "attr": "animate"](h.crisp(null, null, null, n, r)), h.isNew = !1) : (o.box = h = a.rect(0, 0, n, r, c.borderRadius, d || 0).attr({
                stroke: c.borderColor,
                "stroke-width": d || 0,
                fill: f || We
            }).add(l).shadow(c.shadow), h.isNew = !0), h[i ? "show": "hide"]()),
            o.legendWidth = n,
            o.legendHeight = r,
            ni(e,
            function(t) {
                o.positionItem(t)
            }),
            i && l.align(t({
                width: n,
                height: r
            },
            c), !0, "spacingBox"),
            s.isResizing || this.positionCheckboxes()
        },
        handleOverflow: function(t) {
            var e = this,
            i = this.chart,
            n = i.renderer,
            r = this.options,
            o = r.y,
            o = i.spacingBox.height + ("top" === r.verticalAlign ? -o: o) - this.padding,
            s = r.maxHeight,
            a = this.clipRect,
            l = r.navigation,
            h = p(l.animation, !0),
            c = l.arrowSize || 12,
            u = this.nav;
            return "horizontal" === r.layout && (o /= 2),
            s && (o = ye(o, s)),
            t > o && !r.useHTML ? (this.clipHeight = i = o - 20 - this.titleHeight, this.pageCount = ge(t / i), this.currentPage = p(this.currentPage, 1), this.fullHeight = t, a || (a = e.clipRect = n.clipRect(0, 0, 9999, 0), e.contentGroup.clip(a)), a.attr({
                height: i
            }), u || (this.nav = u = n.g().attr({
                zIndex: 1
            }).add(this.group), this.up = n.symbol("triangle", 0, 0, c, c).on("click",
            function() {
                e.scroll( - 1, h)
            }).add(u), this.pager = n.text("", 15, 10).css(l.style).add(u), this.down = n.symbol("triangle-down", 0, 0, c, c).on("click",
            function() {
                e.scroll(1, h)
            }).add(u)), e.scroll(0), t = o) : u && (a.attr({
                height: i.chartHeight
            }), u.hide(), this.scrollGroup.attr({
                translateY: 1
            }), this.clipHeight = 0),
            t
        },
        scroll: function(t, e) {
            var i = this.pageCount,
            n = this.currentPage + t,
            r = this.clipHeight,
            o = this.options.navigation,
            s = o.activeColor,
            a = o.inactiveColor,
            o = this.pager,
            l = this.padding;
            n > i && (n = i),
            n > 0 && (e !== Y && H(e, this.chart), this.nav.attr({
                translateX: l,
                translateY: r + 7 + this.titleHeight,
                visibility: "visible"
            }), this.up.attr({
                fill: 1 === n ? a: s
            }).css({
                cursor: 1 === n ? "default": "pointer"
            }), o.attr({
                text: n + "/" + this.pageCount
            }), this.down.attr({
                x: 18 + this.pager.getBBox().width,
                fill: n === i ? a: s
            }).css({
                cursor: n === i ? "default": "pointer"
            }), r = -ye(r * (n - 1), this.fullHeight - r + l) + 1, this.scrollGroup.animate({
                translateY: r
            }), o.attr({
                text: n + "/" + i
            }), this.currentPage = n, this.positionCheckboxes(r))
        }
    },
    /Trident\/7\.0/.test(Te) && x(_.prototype, "positionItem",
    function(t, e) {
        var i = this,
        n = function() {
            t.call(i, e)
        };
        i.chart.renderer.forExport ? n() : setTimeout(n)
    }),
    X.prototype = {
        init: function(t, i) {
            var n, r = t.series;
            t.series = null,
            n = e(V, t),
            n.series = t.series = r,
            r = n.chart,
            this.margin = this.splashArray("margin", r),
            this.spacing = this.splashArray("spacing", r);
            var o = r.events;
            this.bounds = {
                h: {},
                v: {}
            },
            this.callback = i,
            this.isResizing = 0,
            this.options = n,
            this.axes = [],
            this.series = [],
            this.hasCartesianSeries = r.showAxes;
            var s, a = this;
            if (a.index = Re.length, Re.push(a), r.reflow !== !1 && ai(a, "load",
            function() {
                a.initReflow()
            }), o) for (s in o) ai(a, s, o[s]);
            a.xAxis = [],
            a.yAxis = [],
            a.animation = He ? !1 : p(r.animation, !0),
            a.pointCount = 0,
            a.counters = new C,
            a.firstRender()
        },
        initSeries: function(t) {
            var e = this.options.chart;
            return (e = Ke[t.type || e.type || e.defaultSeriesType]) || N(17, !0),
            e = new e,
            e.init(this, t),
            e
        },
        addSeries: function(t, e, i) {
            var n, r = this;
            return t && (e = p(e, !0), hi(r, "addSeries", {
                options: t
            },
            function() {
                n = r.initSeries(t),
                r.isDirtyLegend = !0,
                r.linkSeries(),
                e && r.redraw(i)
            })),
            n
        },
        addAxis: function(t, i, n, r) {
            var o = i ? "xAxis": "yAxis",
            s = this.options;
            new j(this, e(t, {
                index: this[o].length,
                isX: i
            })),
            s[o] = d(s[o] || {}),
            s[o].push(t),
            p(n, !0) && this.redraw(r)
        },
        isInsidePlot: function(t, e, i) {
            var n = i ? e: t,
            t = i ? t: e;
            return n >= 0 && n <= this.plotWidth && t >= 0 && t <= this.plotHeight
        },
        adjustTickAmounts: function() {
            this.options.chart.alignTicks !== !1 && ni(this.axes,
            function(t) {
                t.adjustTickAmount()
            }),
            this.maxTicks = null
        },
        redraw: function(e) {
            var i, n, r = this.axes,
            o = this.series,
            s = this.pointer,
            a = this.legend,
            l = this.isDirtyLegend,
            h = this.isDirtyBox,
            c = o.length,
            u = c,
            d = this.renderer,
            p = d.isHidden(),
            f = [];
            for (H(e, this), p && this.cloneRenderTo(), this.layOutTitles(); u--;) if (e = o[u], e.options.stacking && (i = !0, e.isDirty)) {
                n = !0;
                break
            }
            if (n) for (u = c; u--;) e = o[u],
            e.options.stacking && (e.isDirty = !0);
            ni(o,
            function(t) {
                t.isDirty && "point" === t.options.legendType && (l = !0)
            }),
            l && a.options.enabled && (a.render(), this.isDirtyLegend = !1),
            i && this.getStacks(),
            this.hasCartesianSeries && (this.isResizing || (this.maxTicks = null, ni(r,
            function(t) {
                t.setScale()
            })), this.adjustTickAmounts(), this.getMargins(), ni(r,
            function(t) {
                t.isDirty && (h = !0)
            }), ni(r,
            function(e) {
                e.isDirtyExtremes && (e.isDirtyExtremes = !1, f.push(function() {
                    hi(e, "afterSetExtremes", t(e.eventArgs, e.getExtremes())),
                    delete e.eventArgs
                })),
                (h || i) && e.redraw()
            })),
            h && this.drawChartBox(),
            ni(o,
            function(t) {
                t.isDirty && t.visible && (!t.isCartesian || t.xAxis) && t.redraw()
            }),
            s && s.reset && s.reset(!0),
            d.draw(),
            hi(this, "redraw"),
            p && this.cloneRenderTo(!0),
            ni(f,
            function(t) {
                t.call()
            })
        },
        showLoading: function(e) {
            var i = this.options,
            n = this.loadingDiv,
            r = i.loading;
            n || (this.loadingDiv = n = g(je, {
                className: "highcharts-loading"
            },
            t(r.style, {
                zIndex: 10,
                display: We
            }), this.container), this.loadingSpan = g("span", null, r.labelStyle, n)),
            this.loadingSpan.innerHTML = e || i.lang.loading,
            this.loadingShown || (f(n, {
                opacity: 0,
                display: "",
                left: this.plotLeft + "px",
                top: this.plotTop + "px",
                width: this.plotWidth + "px",
                height: this.plotHeight + "px"
            }), ui(n, {
                opacity: r.style.opacity
            },
            {
                duration: r.showDuration || 0
            }), this.loadingShown = !0)
        },
        hideLoading: function() {
            var t = this.options,
            e = this.loadingDiv;
            e && ui(e, {
                opacity: 0
            },
            {
                duration: t.loading.hideDuration || 100,
                complete: function() {
                    f(e, {
                        display: We
                    })
                }
            }),
            this.loadingShown = !1
        },
        get: function(t) {
            var e, i, n = this.axes,
            r = this.series;
            for (e = 0; e < n.length; e++) if (n[e].options.id === t) return n[e];
            for (e = 0; e < r.length; e++) if (r[e].options.id === t) return r[e];
            for (e = 0; e < r.length; e++) for (i = r[e].points || [], n = 0; n < i.length; n++) if (i[n].id === t) return i[n];
            return null
        },
        getAxes: function() {
            var t = this,
            e = this.options,
            i = e.xAxis = d(e.xAxis || {}),
            e = e.yAxis = d(e.yAxis || {});
            ni(i,
            function(t, e) {
                t.index = e,
                t.isX = !0
            }),
            ni(e,
            function(t, e) {
                t.index = e
            }),
            i = i.concat(e),
            ni(i,
            function(e) {
                new j(t, e)
            }),
            t.adjustTickAmounts()
        },
        getSelectedPoints: function() {
            var t = [];
            return ni(this.series,
            function(e) {
                t = t.concat(ri(e.points || [],
                function(t) {
                    return t.selected
                }))
            }),
            t
        },
        getSelectedSeries: function() {
            return ri(this.series,
            function(t) {
                return t.selected
            })
        },
        getStacks: function() {
            var t = this;
            ni(t.yAxis,
            function(t) {
                t.stacks && t.hasVisibleSeries && (t.oldStacks = t.stacks)
            }),
            ni(t.series,
            function(e) { ! e.options.stacking || e.visible !== !0 && t.options.chart.ignoreHiddenSeries !== !1 || (e.stackKey = e.type + p(e.options.stack, ""))
            })
        },
        showResetZoom: function() {
            var t = this,
            e = V.lang,
            i = t.options.chart.resetZoomButton,
            n = i.theme,
            r = n.states,
            o = "chart" === i.relativeTo ? null: "plotBox";
            this.resetZoomButton = t.renderer.button(e.resetZoom, null, null,
            function() {
                t.zoomOut()
            },
            n, r && r.hover).attr({
                align: i.position.align,
                title: e.resetZoomTitle
            }).add().align(i.position, !1, o)
        },
        zoomOut: function() {
            var t = this;
            hi(t, "selection", {
                resetSelection: !0
            },
            function() {
                t.zoom()
            })
        },
        zoom: function(t) {
            var e, i, n = this.pointer,
            o = !1; ! t || t.resetSelection ? ni(this.axes,
            function(t) {
                e = t.zoom()
            }) : ni(t.xAxis.concat(t.yAxis),
            function(t) {
                var i = t.axis,
                r = i.isXAxis; (n[r ? "zoomX": "zoomY"] || n[r ? "pinchX": "pinchY"]) && (e = i.zoom(t.min, t.max), i.displayBtn && (o = !0))
            }),
            i = this.resetZoomButton,
            o && !i ? this.showResetZoom() : !o && r(i) && (this.resetZoomButton = i.destroy()),
            e && this.redraw(p(this.options.chart.animation, t && t.animation, this.pointCount < 100))
        },
        pan: function(t, e) {
            var i, n = this,
            r = n.hoverPoints;
            r && ni(r,
            function(t) {
                t.setState()
            }),
            ni("xy" === e ? [1, 0] : [1],
            function(e) {
                var r = t[e ? "chartX": "chartY"],
                o = n[e ? "xAxis": "yAxis"][0],
                s = n[e ? "mouseDownX": "mouseDownY"],
                a = (o.pointRange || 0) / 2,
                l = o.getExtremes(),
                h = o.toValue(s - r, !0) + a,
                s = o.toValue(s + n[e ? "plotWidth": "plotHeight"] - r, !0) - a;
                o.series.length && h > ye(l.dataMin, l.min) && s < me(l.dataMax, l.max) && (o.setExtremes(h, s, !1, !1, {
                    trigger: "pan"
                }), i = !0),
                n[e ? "mouseDownX": "mouseDownY"] = r
            }),
            i && n.redraw(!1),
            f(n.container, {
                cursor: "move"
            })
        },
        setTitle: function(t, i) {
            var n, r, o = this,
            s = o.options;
            r = s.title = e(s.title, t),
            n = s.subtitle = e(s.subtitle, i),
            s = n,
            ni([["title", t, r], ["subtitle", i, s]],
            function(t) {
                var e = t[0],
                i = o[e],
                n = t[1],
                t = t[2];
                i && n && (o[e] = i = i.destroy()),
                t && t.text && !i && (o[e] = o.renderer.text(t.text, 0, 0, t.useHTML).attr({
                    align: t.align,
                    "class": "highcharts-" + e,
                    zIndex: t.zIndex || 4
                }).css(t.style).add())
            }),
            o.layOutTitles()
        },
        layOutTitles: function() {
            var e = 0,
            i = this.title,
            n = this.subtitle,
            r = this.options,
            o = r.title,
            r = r.subtitle,
            s = this.spacingBox.width - 44; ! i || (i.css({
                width: (o.width || s) + "px"
            }).align(t({
                y: 15
            },
            o), !1, "spacingBox"), o.floating || o.verticalAlign) || (e = i.getBBox().height, e >= 18 && 25 >= e && (e = 15)),
            n && (n.css({
                width: (r.width || s) + "px"
            }).align(t({
                y: e + o.margin
            },
            r), !1, "spacingBox"), !r.floating && !r.verticalAlign && (e = ge(e + n.getBBox().height))),
            this.titleOffset = e
        },
        getChartSize: function() {
            var t = this.options.chart,
            e = this.renderToClone || this.renderTo;
            this.containerWidth = ti(e, "width"),
            this.containerHeight = ti(e, "height"),
            this.chartWidth = me(0, t.width || this.containerWidth || 600),
            this.chartHeight = me(0, p(t.height, this.containerHeight > 19 ? this.containerHeight: 400))
        },
        cloneRenderTo: function(t) {
            var e = this.renderToClone,
            i = this.container;
            t ? e && (this.renderTo.appendChild(i), D(e), delete this.renderToClone) : (i && i.parentNode === this.renderTo && this.renderTo.removeChild(i), this.renderToClone = e = this.renderTo.cloneNode(0), f(e, {
                position: "absolute",
                top: "-9999px",
                display: "block"
            }), ce.body.appendChild(e), i && e.appendChild(i))
        },
        getContainer: function() {
            var e, r, o, s, a = this.options.chart;
            this.renderTo = e = a.renderTo,
            s = "highcharts-" + Oe++,
            n(e) && (this.renderTo = e = ce.getElementById(e)),
            e || N(13, !0),
            r = i(u(e, "data-highcharts-chart")),
            !isNaN(r) && Re[r] && Re[r].destroy(),
            u(e, "data-highcharts-chart", this.index),
            e.innerHTML = "",
            e.offsetWidth || this.cloneRenderTo(),
            this.getChartSize(),
            r = this.chartWidth,
            o = this.chartHeight,
            this.container = e = g(je, {
                className: "highcharts-container" + (a.className ? " " + a.className: ""),
                id: s
            },
            t({
                position: "relative",
                overflow: "hidden",
                width: r + "px",
                height: o + "px",
                textAlign: "left",
                lineHeight: "normal",
                zIndex: 0,
                "-webkit-tap-highlight-color": "rgba(0,0,0,0)"
            },
            a.style), this.renderToClone || e),
            this._cursor = e.style.cursor,
            this.renderer = a.forExport ? new vi(e, r, o, !0) : new G(e, r, o),
            He && this.renderer.create(this, e, r, o)
        },
        getMargins: function() {
            var t, e = this.spacing,
            i = this.legend,
            n = this.margin,
            r = this.options.legend,
            o = p(r.margin, 10),
            s = r.x,
            a = r.y,
            l = r.align,
            h = r.verticalAlign,
            u = this.titleOffset;
            this.resetMargins(),
            t = this.axisOffset,
            u && !c(n[0]) && (this.plotTop = me(this.plotTop, u + this.options.title.margin + e[0])),
            i.display && !r.floating && ("right" === l ? c(n[1]) || (this.marginRight = me(this.marginRight, i.legendWidth - s + o + e[1])) : "left" === l ? c(n[3]) || (this.plotLeft = me(this.plotLeft, i.legendWidth + s + o + e[3])) : "top" === h ? c(n[0]) || (this.plotTop = me(this.plotTop, i.legendHeight + a + o + e[0])) : "bottom" !== h || c(n[2]) || (this.marginBottom = me(this.marginBottom, i.legendHeight - a + o + e[2]))),
            this.extraBottomMargin && (this.marginBottom += this.extraBottomMargin),
            this.extraTopMargin && (this.plotTop += this.extraTopMargin),
            this.hasCartesianSeries && ni(this.axes,
            function(t) {
                t.getOffset()
            }),
            c(n[3]) || (this.plotLeft += t[3]),
            c(n[0]) || (this.plotTop += t[0]),
            c(n[2]) || (this.marginBottom += t[2]),
            c(n[1]) || (this.marginRight += t[1]),
            this.setChartSize()
        },
        initReflow: function() {
            function t(t) {
                var o = n.width || ti(r, "width"),
                s = n.height || ti(r, "height"),
                t = t ? t.target: ue;
                i.hasUserSize || !o || !s || t !== ue && t !== ce || ((o !== i.containerWidth || s !== i.containerHeight) && (clearTimeout(e), i.reflowTimeout = e = setTimeout(function() {
                    i.container && (i.setSize(o, s, !1), i.hasUserSize = null)
                },
                100)), i.containerWidth = o, i.containerHeight = s)
            }
            var e, i = this,
            n = i.options.chart,
            r = i.renderTo;
            i.reflow = t,
            ai(ue, "resize", t),
            ai(i, "destroy",
            function() {
                li(ue, "resize", t)
            })
        },
        setSize: function(t, e, i) {
            var n, r, o, s = this;
            s.isResizing += 1,
            o = function() {
                s && hi(s, "endResize", null,
                function() {
                    s.isResizing -= 1
                })
            },
            H(i, s),
            s.oldChartHeight = s.chartHeight,
            s.oldChartWidth = s.chartWidth,
            c(t) && (s.chartWidth = n = me(0, pe(t)), s.hasUserSize = !!n),
            c(e) && (s.chartHeight = r = me(0, pe(e))),
            f(s.container, {
                width: n + "px",
                height: r + "px"
            }),
            s.setChartSize(!0),
            s.renderer.setSize(n, r, i),
            s.maxTicks = null,
            ni(s.axes,
            function(t) {
                t.isDirty = !0,
                t.setScale()
            }),
            ni(s.series,
            function(t) {
                t.isDirty = !0
            }),
            s.isDirtyLegend = !0,
            s.isDirtyBox = !0,
            s.getMargins(),
            s.redraw(i),
            s.oldChartHeight = null,
            hi(s, "resize"),
            U === !1 ? o() : setTimeout(o, U && U.duration || 500)
        },
        setChartSize: function(t) {
            var e, i, n, r, o = this.inverted,
            s = this.renderer,
            a = this.chartWidth,
            l = this.chartHeight,
            h = this.options.chart,
            c = this.spacing,
            u = this.clipOffset;
            this.plotLeft = e = pe(this.plotLeft),
            this.plotTop = i = pe(this.plotTop),
            this.plotWidth = n = me(0, pe(a - e - this.marginRight)),
            this.plotHeight = r = me(0, pe(l - i - this.marginBottom)),
            this.plotSizeX = o ? r: n,
            this.plotSizeY = o ? n: r,
            this.plotBorderWidth = h.plotBorderWidth || 0,
            this.spacingBox = s.spacingBox = {
                x: c[3],
                y: c[0],
                width: a - c[3] - c[1],
                height: l - c[0] - c[2]
            },
            this.plotBox = s.plotBox = {
                x: e,
                y: i,
                width: n,
                height: r
            },
            a = 2 * fe(this.plotBorderWidth / 2),
            o = ge(me(a, u[3]) / 2),
            s = ge(me(a, u[0]) / 2),
            this.clipBox = {
                x: o,
                y: s,
                width: fe(this.plotSizeX - me(a, u[1]) / 2 - o),
                height: fe(this.plotSizeY - me(a, u[2]) / 2 - s)
            },
            t || ni(this.axes,
            function(t) {
                t.setAxisSize(),
                t.setAxisTranslation()
            })
        },
        resetMargins: function() {
            var t = this.spacing,
            e = this.margin;
            this.plotTop = p(e[0], t[0]),
            this.marginRight = p(e[1], t[1]),
            this.marginBottom = p(e[2], t[2]),
            this.plotLeft = p(e[3], t[3]),
            this.axisOffset = [0, 0, 0, 0],
            this.clipOffset = [0, 0, 0, 0]
        },
        drawChartBox: function() {
            var t, e = this.options.chart,
            i = this.renderer,
            n = this.chartWidth,
            r = this.chartHeight,
            o = this.chartBackground,
            s = this.plotBackground,
            a = this.plotBorder,
            l = this.plotBGImage,
            h = e.borderWidth || 0,
            c = e.backgroundColor,
            u = e.plotBackgroundColor,
            d = e.plotBackgroundImage,
            p = e.plotBorderWidth || 0,
            f = this.plotLeft,
            g = this.plotTop,
            m = this.plotWidth,
            y = this.plotHeight,
            v = this.plotBox,
            x = this.clipRect,
            b = this.clipBox;
            t = h + (e.shadow ? 8 : 0),
            (h || c) && (o ? o.animate(o.crisp(null, null, null, n - t, r - t)) : (o = {
                fill: c || We
            },
            h && (o.stroke = e.borderColor, o["stroke-width"] = h), this.chartBackground = i.rect(t / 2, t / 2, n - t, r - t, e.borderRadius, h).attr(o).add().shadow(e.shadow))),
            u && (s ? s.animate(v) : this.plotBackground = i.rect(f, g, m, y, 0).attr({
                fill: u
            }).add().shadow(e.plotShadow)),
            d && (l ? l.animate(v) : this.plotBGImage = i.image(d, f, g, m, y).add()),
            x ? x.animate({
                width: b.width,
                height: b.height
            }) : this.clipRect = i.clipRect(b),
            p && (a ? a.animate(a.crisp(null, f, g, m, y)) : this.plotBorder = i.rect(f, g, m, y, 0, -p).attr({
                stroke: e.plotBorderColor,
                "stroke-width": p,
                zIndex: 1
            }).add()),
            this.isDirtyBox = !1
        },
        propFromSeries: function() {
            var t, e, i, n = this,
            r = n.options.chart,
            o = n.options.series;
            ni(["inverted", "angular", "polar"],
            function(s) {
                for (t = Ke[r.type || r.defaultSeriesType], i = n[s] || r[s] || t && t.prototype[s], e = o && o.length; ! i && e--;)(t = Ke[o[e].type]) && t.prototype[s] && (i = !0);
                n[s] = i
            })
        },
        linkSeries: function() {
            var t = this,
            e = t.series;
            ni(e,
            function(t) {
                t.linkedSeries.length = 0
            }),
            ni(e,
            function(e) {
                var i = e.options.linkedTo;
                n(i) && (i = ":previous" === i ? t.series[e.index - 1] : t.get(i)) && (i.linkedSeries.push(e), e.linkedParent = i)
            })
        },
        render: function() {
            var e, n = this,
            r = n.axes,
            o = n.renderer,
            s = n.options,
            a = s.labels,
            l = s.credits;
            n.setTitle(),
            n.legend = new _(n, s.legend),
            n.getStacks(),
            ni(r,
            function(t) {
                t.setScale()
            }),
            n.getMargins(),
            n.maxTicks = null,
            ni(r,
            function(t) {
                t.setTickPositions(!0),
                t.setMaxTicks()
            }),
            n.adjustTickAmounts(),
            n.getMargins(),
            n.drawChartBox(),
            n.hasCartesianSeries && ni(r,
            function(t) {
                t.render()
            }),
            n.seriesGroup || (n.seriesGroup = o.g("series-group").attr({
                zIndex: 3
            }).add()),
            ni(n.series,
            function(t) {
                t.translate(),
                t.setTooltipPoints(),
                t.render()
            }),
            a.items && ni(a.items,
            function(e) {
                var r = t(a.style, e.style),
                s = i(r.left) + n.plotLeft,
                l = i(r.top) + n.plotTop + 12;
                delete r.left,
                delete r.top,
                o.text(e.html, s, l).attr({
                    zIndex: 2
                }).css(r).add()
            }),
            l.enabled && !n.credits && (e = l.href, n.credits = o.text(l.text, 0, 0).on("click",
            function() {
                e && (location.href = e)
            }).attr({
                align: l.position.align,
                zIndex: 8
            }).css(l.style).add().align(l.position)),
            n.hasRendered = !0
        },
        destroy: function() {
            var t, e = this,
            i = e.axes,
            n = e.series,
            r = e.container,
            o = r && r.parentNode;
            for (hi(e, "destroy"), Re[e.index] = Y, e.renderTo.removeAttribute("data-highcharts-chart"), li(e), t = i.length; t--;) i[t] = i[t].destroy();
            for (t = n.length; t--;) n[t] = n[t].destroy();
            ni("title,subtitle,chartBackground,plotBackground,plotBGImage,plotBorder,seriesGroup,clipRect,credits,pointer,scroller,rangeSelector,legend,resetZoomButton,tooltip,renderer".split(","),
            function(t) {
                var i = e[t];
                i && i.destroy && (e[t] = i.destroy())
            }),
            r && (r.innerHTML = "", li(r), o && D(r));
            for (t in e) delete e[t]
        },
        isReadyToRender: function() {
            var t = this;
            return ! Ne && ue == ue.top && "complete" !== ce.readyState || He && !ue.canvg ? (He ? ki.push(function() {
                t.firstRender()
            },
            t.options.global.canvasToolsURL) : ce.attachEvent("onreadystatechange",
            function() {
                ce.detachEvent("onreadystatechange", t.firstRender),
                "complete" === ce.readyState && t.firstRender()
            }), !1) : !0
        },
        firstRender: function() {
            var t = this,
            e = t.options,
            i = t.callback;
            t.isReadyToRender() && (t.getContainer(), hi(t, "init"), t.resetMargins(), t.setChartSize(), t.propFromSeries(), t.getAxes(), ni(e.series || [],
            function(e) {
                t.initSeries(e)
            }), t.linkSeries(), hi(t, "beforeRender"), t.pointer = new F(t, e), t.render(), t.renderer.draw(), i && i.apply(t, [t]), ni(t.callbacks,
            function(e) {
                e.apply(t, [t])
            }), t.cloneRenderTo(!0), hi(t, "load"))
        },
        splashArray: function(t, e) {
            var i = e[t],
            i = r(i) ? i: [i, i, i, i];
            return [p(e[t + "Top"], i[0]), p(e[t + "Right"], i[1]), p(e[t + "Bottom"], i[2]), p(e[t + "Left"], i[3])]
        }
    },
    X.prototype.callbacks = [];
    var wi = function() {};
    wi.prototype = {
        init: function(t, e, i) {
            return this.series = t,
            this.applyOptions(e, i),
            this.pointAttr = {},
            t.options.colorByPoint && (e = t.options.colors || t.chart.options.colors, this.color = this.color || e[t.colorCounter++], t.colorCounter === e.length) && (t.colorCounter = 0),
            t.chart.pointCount++,
            this
        },
        applyOptions: function(e, i) {
            var n = this.series,
            r = n.pointValKey,
            e = wi.prototype.optionsToObject.call(this, e);
            return t(this, e),
            this.options = this.options ? t(this.options, e) : e,
            r && (this.y = this[r]),
            this.x === Y && n && (this.x = i === Y ? n.autoIncrement() : i),
            this
        },
        optionsToObject: function(t) {
            var e = {},
            i = this.series,
            n = i.pointArrayMap || ["y"],
            r = n.length,
            s = 0,
            a = 0;
            if ("number" == typeof t || null === t) e[n[0]] = t;
            else if (o(t)) for (t.length > r && (i = typeof t[0], "string" === i ? e.name = t[0] : "number" === i && (e.x = t[0]), s++); r > a;) e[n[a++]] = t[s++];
            else "object" == typeof t && (e = t, t.dataLabels && (i._hasPointLabels = !0), t.marker && (i._hasPointMarkers = !0));
            return e
        },
        destroy: function() {
            var t, e = this.series.chart,
            i = e.hoverPoints;
            e.pointCount--,
            i && (this.setState(), h(i, this), !i.length) && (e.hoverPoints = null),
            this === e.hoverPoint && this.onMouseOut(),
            (this.graphic || this.dataLabel) && (li(this), this.destroyElements()),
            this.legendItem && e.legend.destroyItem(this);
            for (t in this) this[t] = null
        },
        destroyElements: function() {
            for (var t, e = "graphic,dataLabel,dataLabelUpper,group,connector,shadowGroup".split(","), i = 6; i--;) t = e[i],
            this[t] && (this[t] = this[t].destroy())
        },
        getLabelConfig: function() {
            return {
                x: this.category,
                y: this.y,
                key: this.name || this.category,
                series: this.series,
                point: this,
                percentage: this.percentage,
                total: this.total || this.stackTotal
            }
        },
        select: function(t, e) {
            var i = this,
            n = i.series,
            r = n.chart,
            t = p(t, !i.selected);
            i.firePointEvent(t ? "select": "unselect", {
                accumulate: e
            },
            function() {
                i.selected = i.options.selected = t,
                n.options.data[ii(i, n.data)] = i.options,
                i.setState(t && "select"),
                e || ni(r.getSelectedPoints(),
                function(t) {
                    t.selected && t !== i && (t.selected = t.options.selected = !1, n.options.data[ii(t, n.data)] = t.options, t.setState(""), t.firePointEvent("unselect"))
                })
            })
        },
        onMouseOver: function(t) {
            var e = this.series,
            i = e.chart,
            n = i.tooltip,
            r = i.hoverPoint;
            r && r !== this && r.onMouseOut(),
            this.firePointEvent("mouseOver"),
            n && (!n.shared || e.noSharedTooltip) && n.refresh(this, t),
            this.setState("hover"),
            i.hoverPoint = this
        },
        onMouseOut: function() {
            var t = this.series.chart,
            e = t.hoverPoints;
            e && -1 !== ii(this, e) || (this.firePointEvent("mouseOut"), this.setState(), t.hoverPoint = null)
        },
        tooltipFormatter: function(t) {
            var e = this.series,
            i = e.tooltipOptions,
            n = p(i.valueDecimals, ""),
            r = i.valuePrefix || "",
            o = i.valueSuffix || "";
            return ni(e.pointArrayMap || ["y"],
            function(e) {
                e = "{point." + e,
                (r || o) && (t = t.replace(e + "}", r + e + "}" + o)),
                t = t.replace(e + "}", e + ":,." + n + "f}")
            }),
            b(t, {
                point: this,
                series: this.series
            })
        },
        update: function(t, e, i) {
            var n, o = this,
            s = o.series,
            a = o.graphic,
            l = s.data,
            h = s.chart,
            c = s.options,
            e = p(e, !0);
            o.firePointEvent("update", {
                options: t
            },
            function() {
                o.applyOptions(t),
                r(t) && (s.getAttribs(), a) && (t && t.marker && t.marker.symbol ? o.graphic = a.destroy() : a.attr(o.pointAttr[o.state || ""])),
                n = ii(o, l),
                s.xData[n] = o.x,
                s.yData[n] = s.toYData ? s.toYData(o) : o.y,
                s.zData[n] = o.z,
                c.data[n] = o.options,
                s.isDirty = s.isDirtyData = !0,
                !s.fixedBox && s.hasCartesianSeries && (h.isDirtyBox = !0),
                "point" === c.legendType && h.legend.destroyItem(o),
                e && h.redraw(i)
            })
        },
        remove: function(t, e) {
            var i, n = this,
            r = n.series,
            o = r.points,
            s = r.chart,
            a = r.data;
            H(e, s),
            t = p(t, !0),
            n.firePointEvent("remove", null,
            function() {
                i = ii(n, a),
                a.length === o.length && o.splice(i, 1),
                a.splice(i, 1),
                r.options.data.splice(i, 1),
                r.xData.splice(i, 1),
                r.yData.splice(i, 1),
                r.zData.splice(i, 1),
                n.destroy(),
                r.isDirty = !0,
                r.isDirtyData = !0,
                t && s.redraw()
            })
        },
        firePointEvent: function(t, e, i) {
            var n = this,
            r = this.series.options; (r.point.events[t] || n.options && n.options.events && n.options.events[t]) && this.importEvents(),
            "click" === t && r.allowPointSelect && (i = function(t) {
                n.select(null, t.ctrlKey || t.metaKey || t.shiftKey)
            }),
            hi(this, t, e, i)
        },
        importEvents: function() {
            if (!this.hasImportedEvents) {
                var t, i = e(this.series.options.point, this.options).events;
                this.events = i;
                for (t in i) ai(this, t, i[t]);
                this.hasImportedEvents = !0
            }
        },
        setState: function(t) {
            var i = this.plotX,
            n = this.plotY,
            r = this.series,
            o = r.options.states,
            s = pi[r.type].marker && r.options.marker,
            a = s && !s.enabled,
            l = s && s.states[t],
            h = l && l.enabled === !1,
            c = r.stateMarkerGraphic,
            u = this.marker || {},
            d = r.chart,
            p = this.pointAttr,
            t = t || "";
            t === this.state || this.selected && "select" !== t || o[t] && o[t].enabled === !1 || t && (h || a && !l.enabled) || t && u.states && u.states[t] && u.states[t].enabled === !1 || (this.graphic ? (o = s && this.graphic.symbolName && p[t].r, this.graphic.attr(e(p[t], o ? {
                x: i - o,
                y: n - o,
                width: 2 * o,
                height: 2 * o
            }: {}))) : (t && l && (o = l.radius, u = u.symbol || r.symbol, c && c.currentSymbol !== u && (c = c.destroy()), c ? c.attr({
                x: i - o,
                y: n - o
            }) : (r.stateMarkerGraphic = c = d.renderer.symbol(u, i - o, n - o, 2 * o, 2 * o).attr(p[t]).add(r.markerGroup), c.currentSymbol = u)), c && c[t && d.isInsidePlot(i, n) ? "show": "hide"]()), this.state = t)
        }
    };
    var Ti = function() {};
    Ti.prototype = {
        isCartesian: !0,
        type: "line",
        pointClass: wi,
        sorted: !0,
        requireSorting: !0,
        pointAttrToOptions: {
            stroke: "lineColor",
            "stroke-width": "lineWidth",
            fill: "fillColor",
            r: "radius"
        },
        colorCounter: 0,
        init: function(e, i) {
            var n, r, o = e.series;
            this.chart = e,
            this.options = i = this.setOptions(i),
            this.linkedSeries = [],
            this.bindAxes(),
            t(this, {
                name: i.name,
                state: "",
                pointAttr: {},
                visible: i.visible !== !1,
                selected: i.selected === !0
            }),
            He && (i.animation = !1),
            r = i.events;
            for (n in r) ai(this, n, r[n]); (r && r.click || i.point && i.point.events && i.point.events.click || i.allowPointSelect) && (e.runTrackerClick = !0),
            this.getColor(),
            this.getSymbol(),
            this.setData(i.data, !1),
            this.isCartesian && (e.hasCartesianSeries = !0),
            o.push(this),
            this._i = o.length - 1,
            A(o,
            function(t, e) {
                return p(t.options.index, t._i) - p(e.options.index, t._i)
            }),
            ni(o,
            function(t, e) {
                t.index = e,
                t.name = t.name || "Series " + (e + 1)
            })
        },
        bindAxes: function() {
            var t, e = this,
            i = e.options,
            n = e.chart;
            e.isCartesian && ni(["xAxis", "yAxis"],
            function(r) {
                ni(n[r],
                function(n) {
                    t = n.options,
                    (i[r] === t.index || i[r] !== Y && i[r] === t.id || i[r] === Y && 0 === t.index) && (n.series.push(e), e[r] = n, n.isDirty = !0)
                }),
                e[r] || N(18, !0)
            })
        },
        autoIncrement: function() {
            var t = this.options,
            e = this.xIncrement,
            e = p(e, t.pointStart, 0);
            return this.pointInterval = p(this.pointInterval, t.pointInterval, 1),
            this.xIncrement = e + this.pointInterval,
            e
        },
        getSegments: function() {
            var t, e = -1,
            i = [],
            n = this.points,
            r = n.length;
            if (r) if (this.options.connectNulls) {
                for (t = r; t--;) null === n[t].y && n.splice(t, 1);
                n.length && (i = [n])
            } else ni(n,
            function(t, o) {
                null === t.y ? (o > e + 1 && i.push(n.slice(e + 1, o)), e = o) : o === r - 1 && i.push(n.slice(e + 1, o + 1))
            });
            this.segments = i
        },
        setOptions: function(t) {
            var i = this.chart.options,
            n = i.plotOptions,
            r = n[this.type];
            return this.userOptions = t,
            t = e(r, n.series, t),
            this.tooltipOptions = e(i.tooltip, t.tooltip),
            null === r.marker && delete t.marker,
            t
        },
        getColor: function() {
            var t, e = this.options,
            i = this.userOptions,
            n = this.chart.options.colors,
            r = this.chart.counters;
            t = e.color || pi[this.type].color,
            t || e.colorByPoint || (c(i._colorIndex) ? e = i._colorIndex: (i._colorIndex = r.color, e = r.color++), t = n[e]),
            this.color = t,
            r.wrapColor(n.length)
        },
        getSymbol: function() {
            var t = this.userOptions,
            e = this.options.marker,
            i = this.chart,
            n = i.options.symbols,
            i = i.counters;
            this.symbol = e.symbol,
            this.symbol || (c(t._symbolIndex) ? t = t._symbolIndex: (t._symbolIndex = i.symbol, t = i.symbol++), this.symbol = n[t]),
            /^url/.test(this.symbol) && (e.radius = 0),
            i.wrapSymbol(n.length)
        },
        drawLegendSymbol: function(t) {
            var e, i = this.options,
            n = i.marker,
            r = t.options;
            e = r.symbolWidth;
            var o = this.chart.renderer,
            s = this.legendGroup,
            t = t.baseline - pe(.3 * o.fontMetrics(r.itemStyle.fontSize).b);
            i.lineWidth && (r = {
                "stroke-width": i.lineWidth
            },
            i.dashStyle && (r.dashstyle = i.dashStyle), this.legendLine = o.path(["M", 0, t, "L", e, t]).attr(r).add(s)),
            n && n.enabled && (i = n.radius, this.legendSymbol = e = o.symbol(this.symbol, e / 2 - i, t - i, 2 * i, 2 * i).add(s), e.isMarker = !0)
        },
        addPoint: function(t, e, i, n) {
            var r, o = this.options,
            s = this.data,
            a = this.graph,
            l = this.area,
            h = this.chart,
            c = this.xData,
            u = this.yData,
            d = this.zData,
            f = this.xAxis && this.xAxis.names,
            g = a && a.shift || 0,
            m = o.data;
            if (H(n, h), i && ni([a, l, this.graphNeg, this.areaNeg],
            function(t) {
                t && (t.shift = g + 1)
            }), l && (l.isArea = !0), e = p(e, !0), n = {
                series: this
            },
            this.pointClass.prototype.applyOptions.apply(n, [t]), a = n.x, l = c.length, this.requireSorting && a < c[l - 1]) for (r = !0; l && c[l - 1] > a;) l--;
            c.splice(l, 0, a),
            u.splice(l, 0, this.toYData ? this.toYData(n) : n.y),
            d.splice(l, 0, n.z),
            f && (f[a] = n.name),
            m.splice(l, 0, t),
            r && (this.data.splice(l, 0, null), this.processData()),
            "point" === o.legendType && this.generatePoints(),
            i && (s[0] && s[0].remove ? s[0].remove(!1) : (s.shift(), c.shift(), u.shift(), d.shift(), m.shift())),
            this.isDirtyData = this.isDirty = !0,
            e && (this.getAttribs(), h.redraw())
        },
        setData: function(t, e) {
            var i, r = this.points,
            a = this.options,
            l = this.chart,
            h = null,
            c = this.xAxis,
            u = c && c.names;
            this.xIncrement = null,
            this.pointRange = c && c.categories ? 1 : a.pointRange,
            this.colorCounter = 0;
            var d = [],
            f = [],
            g = [],
            m = t ? t.length: [];
            i = p(a.turboThreshold, 1e3);
            var y = this.pointArrayMap,
            y = y && y.length,
            v = !!this.toYData;
            if (i && m > i) {
                for (i = 0; null === h && m > i;) h = t[i],
                i++;
                if (s(h)) {
                    for (u = p(a.pointStart, 0), a = p(a.pointInterval, 1), i = 0; m > i; i++) d[i] = u,
                    f[i] = t[i],
                    u += a;
                    this.xIncrement = u
                } else if (o(h)) if (y) for (i = 0; m > i; i++) a = t[i],
                d[i] = a[0],
                f[i] = a.slice(1, y + 1);
                else for (i = 0; m > i; i++) a = t[i],
                d[i] = a[0],
                f[i] = a[1];
                else N(12)
            } else for (i = 0; m > i; i++) t[i] !== Y && (a = {
                series: this
            },
            this.pointClass.prototype.applyOptions.apply(a, [t[i]]), d[i] = a.x, f[i] = v ? this.toYData(a) : a.y, g[i] = a.z, u && a.name) && (u[a.x] = a.name);
            for (n(f[0]) && N(14, !0), this.data = [], this.options.data = t, this.xData = d, this.yData = f, this.zData = g, i = r && r.length || 0; i--;) r[i] && r[i].destroy && r[i].destroy();
            c && (c.minRange = c.userMinRange),
            this.isDirty = this.isDirtyData = l.isDirtyBox = !0,
            p(e, !0) && l.redraw(!1)
        },
        remove: function(t, e) {
            var i = this,
            n = i.chart,
            t = p(t, !0);
            i.isRemoving || (i.isRemoving = !0, hi(i, "remove", null,
            function() {
                i.destroy(),
                n.isDirtyLegend = n.isDirtyBox = !0,
                n.linkSeries(),
                t && n.redraw(e)
            })),
            i.isRemoving = !1
        },
        processData: function(t) {
            var e, i = this.xData,
            n = this.yData,
            r = i.length;
            e = 0;
            var o, s, a = this.xAxis,
            l = this.options,
            h = l.cropThreshold,
            c = this.isCartesian;
            if (c && !this.isDirty && !a.isDirty && !this.yAxis.isDirty && !t) return ! 1;
            for (c && this.sorted && (!h || r > h || this.forceCrop) && (t = a.min, a = a.max, i[r - 1] < t || i[0] > a ? (i = [], n = []) : (i[0] < t || i[r - 1] > a) && (e = this.cropData(this.xData, this.yData, t, a), i = e.xData, n = e.yData, e = e.start, o = !0)), a = i.length - 1; a >= 0; a--) r = i[a] - i[a - 1],
            r > 0 && (s === Y || s > r) ? s = r: 0 > r && this.requireSorting && N(15);
            this.cropped = o,
            this.cropStart = e,
            this.processedXData = i,
            this.processedYData = n,
            null === l.pointRange && (this.pointRange = s || 1),
            this.closestPointRange = s
        },
        cropData: function(t, e, i, n) {
            var r, o = t.length,
            s = 0,
            a = o,
            l = p(this.cropShoulder, 1);
            for (r = 0; o > r; r++) if (t[r] >= i) {
                s = me(0, r - l);
                break
            }
            for (; o > r; r++) if (t[r] > n) {
                a = r + l;
                break
            }
            return {
                xData: t.slice(s, a),
                yData: e.slice(s, a),
                start: s,
                end: a
            }
        },
        generatePoints: function() {
            var t, e, i, n, r = this.options.data,
            o = this.data,
            s = this.processedXData,
            a = this.processedYData,
            l = this.pointClass,
            h = s.length,
            c = this.cropStart || 0,
            u = this.hasGroupedData,
            p = [];
            for (o || u || (o = [], o.length = r.length, o = this.data = o), n = 0; h > n; n++) e = c + n,
            u ? p[n] = (new l).init(this, [s[n]].concat(d(a[n]))) : (o[e] ? i = o[e] : r[e] !== Y && (o[e] = i = (new l).init(this, r[e], s[n])), p[n] = i);
            if (o && (h !== (t = o.length) || u)) for (n = 0; t > n; n++) n === c && !u && (n += h),
            o[n] && (o[n].destroyElements(), o[n].plotX = Y);
            this.data = o,
            this.points = p
        },
        setStackedPoints: function() {
            if (this.options.stacking && (this.visible === !0 || this.chart.options.chart.ignoreHiddenSeries === !1)) {
                var t, e, i, n, r, o = this.processedXData,
                s = this.processedYData,
                a = [],
                l = s.length,
                h = this.options,
                c = h.threshold,
                u = h.stack,
                h = h.stacking,
                d = this.stackKey,
                p = "-" + d,
                f = this.negStacks,
                g = this.yAxis,
                m = g.stacks,
                y = g.oldStacks;
                for (i = 0; l > i; i++) n = o[i],
                r = s[i],
                e = (t = f && c > r) ? p: d,
                m[e] || (m[e] = {}),
                m[e][n] || (y[e] && y[e][n] ? (m[e][n] = y[e][n], m[e][n].total = null) : m[e][n] = new R(g, g.options.stackLabels, t, n, u, h)),
                e = m[e][n],
                e.points[this.index] = [e.cum || 0],
                "percent" === h ? (t = t ? d: p, f && m[t] && m[t][n] ? (t = m[t][n], e.total = t.total = me(t.total, e.total) + ve(r) || 0) : e.total += ve(r) || 0) : e.total += r || 0,
                e.cum = (e.cum || 0) + (r || 0),
                e.points[this.index].push(e.cum),
                a[i] = e.cum;
                "percent" === h && (g.usePercentage = !0),
                this.stackedYData = a,
                g.oldStacks = {}
            }
        },
        setPercentStacks: function() {
            var t = this,
            e = t.stackKey,
            i = t.yAxis.stacks;
            ni([e, "-" + e],
            function(e) {
                for (var n, r, o, s = t.xData.length; s--;) r = t.xData[s],
                n = (o = i[e] && i[e][r]) && o.points[t.index],
                (r = n) && (o = o.total ? 100 / o.total: 0, r[0] = E(r[0] * o), r[1] = E(r[1] * o), t.stackedYData[s] = r[1])
            })
        },
        getExtremes: function() {
            var t, e, i, n, r = this.yAxis,
            o = this.processedXData,
            s = this.stackedYData || this.processedYData,
            a = s.length,
            l = [],
            h = 0,
            c = this.xAxis.getExtremes(),
            u = c.min,
            c = c.max;
            for (n = 0; a > n; n++) if (e = o[n], i = s[n], t = null !== i && i !== Y && (!r.isLog || i.length || i > 0), e = this.getExtremesFromAll || this.cropped || (o[n + 1] || e) >= u && (o[n - 1] || e) <= c, t && e) if (t = i.length) for (; t--;) null !== i[t] && (l[h++] = i[t]);
            else l[h++] = i;
            this.dataMin = p(void 0, L(l)),
            this.dataMax = p(void 0, M(l))
        },
        translate: function() {
            this.processedXData || this.processData(),
            this.generatePoints();
            for (var t = this.options,
            e = t.stacking,
            i = this.xAxis,
            n = i.categories,
            r = this.yAxis,
            o = this.points,
            a = o.length,
            l = !!this.modifyValue,
            h = t.pointPlacement,
            u = "between" === h || s(h), d = t.threshold, t = 0; a > t; t++) {
                var f = o[t],
                g = f.x,
                m = f.y,
                y = f.low,
                v = r.stacks[(this.negStacks && d > m ? "-": "") + this.stackKey];
                r.isLog && 0 >= m && (f.y = m = null),
                f.plotX = i.translate(g, 0, 0, 0, 1, h, "flags" === this.type),
                e && this.visible && v && v[g] && (v = v[g], m = v.points[this.index], y = m[0], m = m[1], 0 === y && (y = p(d, r.min)), r.isLog && 0 >= y && (y = null), f.total = f.stackTotal = v.total, f.percentage = "percent" === e && f.y / v.total * 100, f.stackY = m, v.setOffset(this.pointXOffset || 0, this.barW || 0)),
                f.yBottom = c(y) ? r.translate(y, 0, 1, 0, 1) : null,
                l && (m = this.modifyValue(m, f)),
                f.plotY = "number" == typeof m && 1 / 0 !== m ? r.translate(m, 0, 1, 0, 1) : Y,
                f.clientX = u ? i.translate(g, 0, 0, 0, 1) : f.plotX,
                f.negative = f.y < (d || 0),
                f.category = n && n[f.x] !== Y ? n[f.x] : f.x
            }
            this.getSegments()
        },
        setTooltipPoints: function(t) {
            var e, i, n, r, o = [],
            s = this.xAxis,
            a = s && s.getExtremes(),
            l = s ? s.tooltipLen || s.len: this.chart.plotSizeX,
            h = [];
            if (this.options.enableMouseTracking !== !1) {
                for (t && (this.tooltipPoints = null), ni(this.segments || this.points,
                function(t) {
                    o = o.concat(t)
                }), s && s.reversed && (o = o.reverse()), this.orderTooltipPoints && this.orderTooltipPoints(o), t = o.length, r = 0; t > r; r++) if (s = o[r], e = s.x, e >= a.min && e <= a.max) for (n = o[r + 1], e = i === Y ? 0 : i + 1, i = o[r + 1] ? ye(me(0, fe((s.clientX + (n ? n.wrappedClientX || n.clientX: l)) / 2)), l) : l; e >= 0 && i >= e;) h[e++] = s;
                this.tooltipPoints = h
            }
        },
        tooltipHeaderFormatter: function(t) {
            var e, i = this.tooltipOptions,
            n = i.xDateFormat,
            r = i.dateTimeLabelFormats,
            o = this.xAxis,
            a = o && "datetime" === o.options.type,
            i = i.headerFormat,
            o = o && o.closestPointRange;
            if (a && !n) if (o) {
                for (e in K) if (K[e] >= o) {
                    n = r[e];
                    break
                }
            } else n = r.day;
            return a && n && s(t.key) && (i = i.replace("{point.key}", "{point.key:" + n + "}")),
            b(i, {
                point: t,
                series: this
            })
        },
        onMouseOver: function() {
            var t = this.chart,
            e = t.hoverSeries;
            e && e !== this && e.onMouseOut(),
            this.options.events.mouseOver && hi(this, "mouseOver"),
            this.setState("hover"),
            t.hoverSeries = this
        },
        onMouseOut: function() {
            var t = this.options,
            e = this.chart,
            i = e.tooltip,
            n = e.hoverPoint;
            n && n.onMouseOut(),
            this && t.events.mouseOut && hi(this, "mouseOut"),
            i && !t.stickyTracking && (!i.shared || this.noSharedTooltip) && i.hide(),
            this.setState(),
            e.hoverSeries = null
        },
        animate: function(e) {
            var i, n = this,
            o = n.chart,
            s = o.renderer;
            i = n.options.animation;
            var a, l = o.clipBox,
            h = o.inverted;
            i && !r(i) && (i = pi[n.type].animation),
            a = "_sharedClip" + i.duration + i.easing,
            e ? (e = o[a], i = o[a + "m"], e || (o[a] = e = s.clipRect(t(l, {
                width: 0
            })), o[a + "m"] = i = s.clipRect( - 99, h ? -o.plotLeft: -o.plotTop, 99, h ? o.chartWidth: o.chartHeight)), n.group.clip(e), n.markerGroup.clip(i), n.sharedClipKey = a) : ((e = o[a]) && (e.animate({
                width: o.plotSizeX
            },
            i), o[a + "m"].animate({
                width: o.plotSizeX + 99
            },
            i)), n.animate = null, n.animationTimeout = setTimeout(function() {
                n.afterAnimate()
            },
            i.duration))
        },
        afterAnimate: function() {
            var t = this.chart,
            e = this.sharedClipKey,
            i = this.group;
            i && this.options.clip !== !1 && (i.clip(t.clipRect), this.markerGroup.clip()),
            setTimeout(function() {
                e && t[e] && (t[e] = t[e].destroy(), t[e + "m"] = t[e + "m"].destroy())
            },
            100)
        },
        drawPoints: function() {
            var e, i, n, r, o, s, a, l, h, c, u = this.points,
            d = this.chart,
            f = this.options.marker,
            g = this.markerGroup;
            if (f.enabled || this._hasPointMarkers) for (r = u.length; r--;) o = u[r],
            i = fe(o.plotX),
            n = o.plotY,
            h = o.graphic,
            a = o.marker || {},
            e = f.enabled && a.enabled === Y || a.enabled,
            c = d.isInsidePlot(pe(i), n, d.inverted),
            e && n !== Y && !isNaN(n) && null !== o.y ? (e = o.pointAttr[o.selected ? "select": ""], s = e.r, a = p(a.symbol, this.symbol), l = 0 === a.indexOf("url"), h ? h.attr({
                visibility: c ? Ne ? "inherit": "visible": "hidden"
            }).animate(t({
                x: i - s,
                y: n - s
            },
            h.symbolName ? {
                width: 2 * s,
                height: 2 * s
            }: {})) : c && (s > 0 || l) && (o.graphic = d.renderer.symbol(a, i - s, n - s, 2 * s, 2 * s).attr(e).add(g))) : h && (o.graphic = h.destroy())
        },
        convertAttribs: function(t, e, i, n) {
            var r, o, s = this.pointAttrToOptions,
            a = {},
            t = t || {},
            e = e || {},
            i = i || {},
            n = n || {};
            for (r in s) o = s[r],
            a[r] = p(t[o], e[r], i[r], n[r]);
            return a
        },
        getAttribs: function() {
            var e, i, n, r = this,
            o = r.options,
            s = pi[r.type].marker ? o.marker: o,
            a = s.states,
            l = a.hover,
            h = r.color,
            u = {
                stroke: h,
                fill: h
            },
            d = r.points || [],
            p = [],
            f = r.pointAttrToOptions,
            g = o.negativeColor,
            m = s.lineColor;
            for (o.marker ? (l.radius = l.radius || s.radius + 2, l.lineWidth = l.lineWidth || s.lineWidth + 1) : l.color = l.color || yi(l.color || h).brighten(l.brightness).get(), p[""] = r.convertAttribs(s, u), ni(["hover", "select"],
            function(t) {
                p[t] = r.convertAttribs(a[t], p[""])
            }), r.pointAttr = p, h = d.length; h--;) {
                if (u = d[h], (s = u.options && u.options.marker || u.options) && s.enabled === !1 && (s.radius = 0), u.negative && g && (u.color = u.fillColor = g), e = o.colorByPoint || u.color, u.options) for (n in f) c(s[f[n]]) && (e = !0);
                e ? (s = s || {},
                i = [], a = s.states || {},
                e = a.hover = a.hover || {},
                o.marker || (e.color = yi(e.color || u.color).brighten(e.brightness || l.brightness).get()), i[""] = r.convertAttribs(t({
                    color: u.color,
                    fillColor: u.color,
                    lineColor: null === m ? u.color: Y
                },
                s), p[""]), i.hover = r.convertAttribs(a.hover, p.hover, i[""]), i.select = r.convertAttribs(a.select, p.select, i[""])) : i = p,
                u.pointAttr = i
            }
        },
        update: function(i, n) {
            var r, o = this.chart,
            s = this.type,
            a = Ke[s].prototype,
            i = e(this.userOptions, {
                animation: !1,
                index: this.index,
                pointStart: this.xData[0]
            },
            {
                data: this.options.data
            },
            i);
            this.remove(!1);
            for (r in a) a.hasOwnProperty(r) && (this[r] = Y);
            t(this, Ke[i.type || s].prototype),
            this.init(o, i),
            p(n, !0) && o.redraw(!1)
        },
        destroy: function() {
            var t, e, i, n, r, o = this,
            s = o.chart,
            a = /AppleWebKit\/533/.test(Te),
            l = o.data || [];
            for (hi(o, "destroy"), li(o), ni(["xAxis", "yAxis"],
            function(t) { (r = o[t]) && (h(r.series, o), r.isDirty = r.forceRedraw = !0, r.stacks = {})
            }), o.legendItem && o.chart.legend.destroyItem(o), e = l.length; e--;)(i = l[e]) && i.destroy && i.destroy();
            o.points = null,
            clearTimeout(o.animationTimeout),
            ni("area,graph,dataLabelsGroup,group,markerGroup,tracker,graphNeg,areaNeg,posClip,negClip".split(","),
            function(e) {
                o[e] && (t = a && "group" === e ? "hide": "destroy", o[e][t]())
            }),
            s.hoverSeries === o && (s.hoverSeries = null),
            h(s.series, o);
            for (n in o) delete o[n]
        },
        drawDataLabels: function() {
            var i, n, r, o, s = this,
            a = s.options,
            l = a.cursor,
            h = a.dataLabels,
            a = s.points; (h.enabled || s._hasPointLabels) && (s.dlProcessOptions && s.dlProcessOptions(h), o = s.plotGroup("dataLabelsGroup", "data-labels", s.visible ? "visible": "hidden", h.zIndex || 6), n = h, ni(a,
            function(a) {
                var u, d, f, g = a.dataLabel,
                m = a.connector,
                y = !0;
                if (i = a.options && a.options.dataLabels, u = p(i && i.enabled, n.enabled), g && !u) a.dataLabel = g.destroy();
                else if (u) {
                    if (h = e(n, i), u = h.rotation, d = a.getLabelConfig(), r = h.format ? b(h.format, d) : h.formatter.call(d, h), h.style.color = p(h.color, h.style.color, s.color, "black"), g) c(r) ? (g.attr({
                        text: r
                    }), y = !1) : (a.dataLabel = g = g.destroy(), m && (a.connector = m.destroy()));
                    else if (c(r)) {
                        g = {
                            fill: h.backgroundColor,
                            stroke: h.borderColor,
                            "stroke-width": h.borderWidth,
                            r: h.borderRadius || 0,
                            rotation: u,
                            padding: h.padding,
                            zIndex: 1
                        };
                        for (f in g) g[f] === Y && delete g[f];
                        g = a.dataLabel = s.chart.renderer[u ? "text": "label"](r, 0, -999, null, null, null, h.useHTML).attr(g).css(t(h.style, l && {
                            cursor: l
                        })).add(o).shadow(h.shadow)
                    }
                    g && s.alignDataLabel(a, g, h, null, y)
                }
            }))
        },
        alignDataLabel: function(e, i, n, r, o) {
            var s = this.chart,
            a = s.inverted,
            l = p(e.plotX, -999),
            h = p(e.plotY, -999),
            c = i.getBBox(); (e = this.visible && s.isInsidePlot(e.plotX, e.plotY, a)) && (r = t({
                x: a ? s.plotWidth - h: l,
                y: pe(a ? s.plotHeight - l: h),
                width: 0,
                height: 0
            },
            r), t(n, {
                width: c.width,
                height: c.height
            }), n.rotation ? (a = {
                align: n.align,
                x: r.x + n.x + r.width / 2,
                y: r.y + n.y + r.height / 2
            },
            i[o ? "attr": "animate"](a)) : (i.align(n, null, r), a = i.alignAttr, "justify" === p(n.overflow, "justify") ? this.justifyDataLabel(i, n, a, c, r, o) : p(n.crop, !0) && (e = s.isInsidePlot(a.x, a.y) && s.isInsidePlot(a.x + c.width, a.y + c.height)))),
            e || i.attr({
                y: -999
            })
        },
        justifyDataLabel: function(t, e, i, n, r, o) {
            var s, a, l = this.chart,
            h = e.align,
            c = e.verticalAlign;
            s = i.x,
            0 > s && ("right" === h ? e.align = "left": e.x = -s, a = !0),
            s = i.x + n.width,
            s > l.plotWidth && ("left" === h ? e.align = "right": e.x = l.plotWidth - s, a = !0),
            s = i.y,
            0 > s && ("bottom" === c ? e.verticalAlign = "top": e.y = -s, a = !0),
            s = i.y + n.height,
            s > l.plotHeight && ("top" === c ? e.verticalAlign = "bottom": e.y = l.plotHeight - s, a = !0),
            a && (t.placed = !o, t.align(e, null, r))
        },
        getSegmentPath: function(t) {
            var e = this,
            i = [],
            n = e.options.step;
            return ni(t,
            function(r, o) {
                var s, a = r.plotX,
                l = r.plotY;
                e.getPointSpline ? i.push.apply(i, e.getPointSpline(t, r, o)) : (i.push(o ? "L": "M"), n && o && (s = t[o - 1], "right" === n ? i.push(s.plotX, l) : "center" === n ? i.push((s.plotX + a) / 2, s.plotY, (s.plotX + a) / 2, l) : i.push(a, s.plotY)), i.push(r.plotX, r.plotY))
            }),
            i
        },
        getGraphPath: function() {
            var t, e = this,
            i = [],
            n = [];
            return ni(e.segments,
            function(r) {
                t = e.getSegmentPath(r),
                r.length > 1 ? i = i.concat(t) : n.push(r[0])
            }),
            e.singlePoints = n,
            e.graphPath = i
        },
        drawGraph: function() {
            var t = this,
            e = this.options,
            i = [["graph", e.lineColor || this.color]],
            n = e.lineWidth,
            r = e.dashStyle,
            o = "square" !== e.linecap,
            s = this.getGraphPath(),
            a = e.negativeColor;
            a && i.push(["graphNeg", a]),
            ni(i,
            function(i, a) {
                var l = i[0],
                h = t[l];
                h ? (di(h), h.animate({
                    d: s
                })) : n && s.length && (h = {
                    stroke: i[1],
                    "stroke-width": n,
                    zIndex: 1
                },
                r ? h.dashstyle = r: o && (h["stroke-linecap"] = h["stroke-linejoin"] = "round"), t[l] = t.chart.renderer.path(s).attr(h).add(t.group).shadow(!a && e.shadow))
            })
        },
        clipNeg: function() {
            var t, e = this.options,
            i = this.chart,
            n = i.renderer,
            r = e.negativeColor || e.negativeFillColor,
            o = this.graph,
            s = this.area,
            a = this.posClip,
            l = this.negClip;
            t = i.chartWidth;
            var h = i.chartHeight,
            c = me(t, h),
            u = this.yAxis;
            r && (o || s) && (r = pe(u.toPixels(e.threshold || 0, !0)), e = {
                x: 0,
                y: 0,
                width: c,
                height: r
            },
            c = {
                x: 0,
                y: r,
                width: c,
                height: c
            },
            i.inverted && (e.height = c.y = i.plotWidth - r, n.isVML && (e = {
                x: i.plotWidth - r - i.plotLeft,
                y: 0,
                width: t,
                height: h
            },
            c = {
                x: r + i.plotLeft - t,
                y: 0,
                width: i.plotLeft + r,
                height: t
            })), u.reversed ? (i = c, t = e) : (i = e, t = c), a ? (a.animate(i), l.animate(t)) : (this.posClip = a = n.clipRect(i), this.negClip = l = n.clipRect(t), o && this.graphNeg && (o.clip(a), this.graphNeg.clip(l)), s && (s.clip(a), this.areaNeg.clip(l))))
        },
        invertGroups: function() {
            function t() {
                var t = {
                    width: e.yAxis.len,
                    height: e.xAxis.len
                };
                ni(["group", "markerGroup"],
                function(i) {
                    e[i] && e[i].attr(t).invert()
                })
            }
            var e = this,
            i = e.chart;
            e.xAxis && (ai(i, "resize", t), ai(e, "destroy",
            function() {
                li(i, "resize", t)
            }), t(), e.invertGroups = t)
        },
        plotGroup: function(t, e, i, n, r) {
            var o = this[t],
            s = !o;
            return s && (this[t] = o = this.chart.renderer.g(e).attr({
                visibility: i,
                zIndex: n || .1
            }).add(r)),
            o[s ? "attr": "animate"](this.getPlotBox()),
            o
        },
        getPlotBox: function() {
            return {
                translateX: this.xAxis ? this.xAxis.left: this.chart.plotLeft,
                translateY: this.yAxis ? this.yAxis.top: this.chart.plotTop,
                scaleX: 1,
                scaleY: 1
            }
        },
        render: function() {
            var t, e = this.chart,
            i = this.options,
            n = i.animation && !!this.animate && e.renderer.isSVG,
            r = this.visible ? "visible": "hidden",
            o = i.zIndex,
            s = this.hasRendered,
            a = e.seriesGroup;
            t = this.plotGroup("group", "series", r, o, a),
            this.markerGroup = this.plotGroup("markerGroup", "markers", r, o, a),
            n && this.animate(!0),
            this.getAttribs(),
            t.inverted = this.isCartesian ? e.inverted: !1,
            this.drawGraph && (this.drawGraph(), this.clipNeg()),
            this.drawDataLabels(),
            this.drawPoints(),
            this.options.enableMouseTracking !== !1 && this.drawTracker(),
            e.inverted && this.invertGroups(),
            i.clip !== !1 && !this.sharedClipKey && !s && t.clip(e.clipRect),
            n ? this.animate() : s || this.afterAnimate(),
            this.isDirty = this.isDirtyData = !1,
            this.hasRendered = !0
        },
        redraw: function() {
            var t = this.chart,
            e = this.isDirtyData,
            i = this.group,
            n = this.xAxis,
            r = this.yAxis;
            i && (t.inverted && i.attr({
                width: t.plotWidth,
                height: t.plotHeight
            }), i.animate({
                translateX: p(n && n.left, t.plotLeft),
                translateY: p(r && r.top, t.plotTop)
            })),
            this.translate(),
            this.setTooltipPoints(!0),
            this.render(),
            e && hi(this, "updatedData")
        },
        setState: function(t) {
            var e = this.options,
            i = this.graph,
            n = this.graphNeg,
            r = e.states,
            e = e.lineWidth,
            t = t || "";
            this.state !== t && (this.state = t, r[t] && r[t].enabled === !1 || (t && (e = r[t].lineWidth || e + 1), i && !i.dashstyle && (t = {
                "stroke-width": e
            },
            i.attr(t), n && n.attr(t))))
        },
        setVisible: function(t, e) {
            var i, n = this,
            r = n.chart,
            o = n.legendItem,
            s = r.options.chart.ignoreHiddenSeries,
            a = n.visible;
            i = (n.visible = t = n.userOptions.visible = t === Y ? !a: t) ? "show": "hide",
            ni(["group", "dataLabelsGroup", "markerGroup", "tracker"],
            function(t) {
                n[t] && n[t][i]()
            }),
            r.hoverSeries === n && n.onMouseOut(),
            o && r.legend.colorizeItem(n, t),
            n.isDirty = !0,
            n.options.stacking && ni(r.series,
            function(t) {
                t.options.stacking && t.visible && (t.isDirty = !0)
            }),
            ni(n.linkedSeries,
            function(e) {
                e.setVisible(t, !1)
            }),
            s && (r.isDirtyBox = !0),
            e !== !1 && r.redraw(),
            hi(n, i)
        },
        show: function() {
            this.setVisible(!0)
        },
        hide: function() {
            this.setVisible(!1)
        },
        select: function(t) {
            this.selected = t = t === Y ? !this.selected: t,
            this.checkbox && (this.checkbox.checked = t),
            hi(this, t ? "select": "unselect")
        },
        drawTracker: function() {
            var t, e = this,
            i = e.options,
            n = i.trackByArea,
            r = [].concat(n ? e.areaPath: e.graphPath),
            o = r.length,
            s = e.chart,
            a = s.pointer,
            l = s.renderer,
            h = s.options.tooltip.snap,
            c = e.tracker,
            u = i.cursor,
            d = u && {
                cursor: u
            },
            u = e.singlePoints,
            p = function() {
                s.hoverSeries !== e && e.onMouseOver()
            };
            if (o && !n) for (t = o + 1; t--;)"M" === r[t] && r.splice(t + 1, 0, r[t + 1] - h, r[t + 2], "L"),
            (t && "M" === r[t] || t === o) && r.splice(t, 0, "L", r[t - 2] + h, r[t - 1]);
            for (t = 0; t < u.length; t++) o = u[t],
            r.push("M", o.plotX - h, o.plotY, "L", o.plotX + h, o.plotY);
            c ? c.attr({
                d: r
            }) : (e.tracker = l.path(r).attr({
                "stroke-linejoin": "round",
                visibility: e.visible ? "visible": "hidden",
                stroke: Fe,
                fill: n ? Fe: We,
                "stroke-width": i.lineWidth + (n ? 0 : 2 * h),
                zIndex: 2
            }).add(e.group), ni([e.tracker, e.markerGroup],
            function(t) {
                t.addClass("highcharts-tracker").on("mouseover", p).on("mouseout",
                function(t) {
                    a.onTrackerMouseOut(t)
                }).css(d),
                Ie && t.on("touchstart", p)
            }))
        }
    },
    Qe = m(Ti),
    Ke.line = Qe,
    pi.area = e(Je, {
        threshold: 0
    }),
    Qe = m(Ti, {
        type: "area",
        getSegments: function() {
            var t, e, i, n, r, o = [],
            s = [],
            a = [],
            l = this.xAxis,
            h = this.yAxis,
            c = h.stacks[this.stackKey],
            u = {},
            d = this.points,
            p = this.options.connectNulls;
            if (this.options.stacking && !this.cropped) {
                for (n = 0; n < d.length; n++) u[d[n].x] = d[n];
                for (r in c) null !== c[r].total && a.push( + r);
                a.sort(function(t, e) {
                    return t - e
                }),
                ni(a,
                function(n) { (!p || u[n] && null !== u[n].y) && (u[n] ? s.push(u[n]) : (t = l.translate(n), i = c[n].percent ? c[n].total ? 100 * c[n].cum / c[n].total: 0 : c[n].cum, e = h.toPixels(i, !0), s.push({
                        y: null,
                        plotX: t,
                        clientX: t,
                        plotY: e,
                        yBottom: e,
                        onMouseOver: ze
                    })))
                }),
                s.length && o.push(s)
            } else Ti.prototype.getSegments.call(this),
            o = this.segments;
            this.segments = o
        },
        getSegmentPath: function(t) {
            var e, i = Ti.prototype.getSegmentPath.call(this, t),
            n = [].concat(i),
            r = this.options;
            e = i.length;
            var o, s = this.yAxis.getThreshold(r.threshold);
            if (3 === e && n.push("L", i[1], i[2]), r.stacking && !this.closedStacks) for (e = t.length - 1; e >= 0; e--) o = p(t[e].yBottom, s),
            e < t.length - 1 && r.step && n.push(t[e + 1].plotX, o),
            n.push(t[e].plotX, o);
            else this.closeSegment(n, t, s);
            return this.areaPath = this.areaPath.concat(n),
            i
        },
        closeSegment: function(t, e, i) {
            t.push("L", e[e.length - 1].plotX, i, "L", e[0].plotX, i)
        },
        drawGraph: function() {
            this.areaPath = [],
            Ti.prototype.drawGraph.apply(this);
            var t = this,
            e = this.areaPath,
            i = this.options,
            n = i.negativeColor,
            r = i.negativeFillColor,
            o = [["area", this.color, i.fillColor]]; (n || r) && o.push(["areaNeg", n, r]),
            ni(o,
            function(n) {
                var r = n[0],
                o = t[r];
                o ? o.animate({
                    d: e
                }) : t[r] = t.chart.renderer.path(e).attr({
                    fill: p(n[2], yi(n[1]).setOpacity(p(i.fillOpacity, .75)).get()),
                    zIndex: 0
                }).add(t.group)
            })
        },
        drawLegendSymbol: function(t, e) {
            e.legendSymbol = this.chart.renderer.rect(0, t.baseline - 11, t.options.symbolWidth, 12, 2).attr({
                zIndex: 3
            }).add(e.legendGroup)
        }
    }),
    Ke.area = Qe,
    pi.spline = e(Je),
    xi = m(Ti, {
        type: "spline",
        getPointSpline: function(t, e, i) {
            var n, r, o, s, a = e.plotX,
            l = e.plotY,
            h = t[i - 1],
            c = t[i + 1];
            if (h && c) {
                t = h.plotY,
                o = c.plotX;
                var u, c = c.plotY;
                n = (1.5 * a + h.plotX) / 2.5,
                r = (1.5 * l + t) / 2.5,
                o = (1.5 * a + o) / 2.5,
                s = (1.5 * l + c) / 2.5,
                u = (s - r) * (o - a) / (o - n) + l - s,
                r += u,
                s += u,
                r > t && r > l ? (r = me(t, l), s = 2 * l - r) : t > r && l > r && (r = ye(t, l), s = 2 * l - r),
                s > c && s > l ? (s = me(c, l), r = 2 * l - s) : c > s && l > s && (s = ye(c, l), r = 2 * l - s),
                e.rightContX = o,
                e.rightContY = s
            }
            return i ? (e = ["C", h.rightContX || h.plotX, h.rightContY || h.plotY, n || a, r || l, a, l], h.rightContX = h.rightContY = null) : e = ["M", a, l],
            e
        }
    }),
    Ke.spline = xi,
    pi.areaspline = e(pi.area),
    bi = Qe.prototype,
    xi = m(xi, {
        type: "areaspline",
        closedStacks: !0,
        getSegmentPath: bi.getSegmentPath,
        closeSegment: bi.closeSegment,
        drawGraph: bi.drawGraph,
        drawLegendSymbol: bi.drawLegendSymbol
    }),
    Ke.areaspline = xi,
    pi.column = e(Je, {
        borderColor: "#FFFFFF",
        borderWidth: 1,
        borderRadius: 0,
        groupPadding: .2,
        marker: null,
        pointPadding: .1,
        minPointLength: 0,
        cropThreshold: 50,
        pointRange: null,
        states: {
            hover: {
                brightness: .1,
                shadow: !1
            },
            select: {
                color: "#C0C0C0",
                borderColor: "#000000",
                shadow: !1
            }
        },
        dataLabels: {
            align: null,
            verticalAlign: null,
            y: null
        },
        stickyTracking: !1,
        threshold: 0
    }),
    xi = m(Ti, {
        type: "column",
        pointAttrToOptions: {
            stroke: "borderColor",
            "stroke-width": "borderWidth",
            fill: "color",
            r: "borderRadius"
        },
        cropShoulder: 0,
        trackerGroups: ["group", "dataLabelsGroup"],
        negStacks: !0,
        init: function() {
            Ti.prototype.init.apply(this, arguments);
            var t = this,
            e = t.chart;
            e.hasRendered && ni(e.series,
            function(e) {
                e.type === t.type && (e.isDirty = !0)
            })
        },
        getColumnMetrics: function() {
            var t, e, i = this,
            n = i.options,
            r = i.xAxis,
            o = i.yAxis,
            s = r.reversed,
            a = {},
            l = 0;
            n.grouping === !1 ? l = 1 : ni(i.chart.series,
            function(n) {
                var r = n.options,
                s = n.yAxis;
                n.type === i.type && n.visible && o.len === s.len && o.pos === s.pos && (r.stacking ? (t = n.stackKey, a[t] === Y && (a[t] = l++), e = a[t]) : r.grouping !== !1 && (e = l++), n.columnIndex = e)
            });
            var r = ye(ve(r.transA) * (r.ordinalSlope || n.pointRange || r.closestPointRange || 1), r.len),
            h = r * n.groupPadding,
            u = (r - 2 * h) / l,
            d = n.pointWidth,
            n = c(d) ? (u - d) / 2 : u * n.pointPadding,
            d = p(d, u - 2 * n);
            return i.columnMetrics = {
                width: d,
                offset: n + (h + ((s ? l - (i.columnIndex || 0) : i.columnIndex) || 0) * u - r / 2) * (s ? -1 : 1)
            }
        },
        translate: function() {
            var t = this.chart,
            e = this.options,
            i = e.borderWidth,
            n = this.yAxis,
            r = this.translatedThreshold = n.getThreshold(e.threshold),
            o = p(e.minPointLength, 5),
            e = this.getColumnMetrics(),
            s = e.width,
            a = this.barW = ge(me(s, 1 + 2 * i)),
            l = this.pointXOffset = e.offset,
            h = -(i % 2 ? .5 : 0),
            c = i % 2 ? .5 : 1;
            t.renderer.isVML && t.inverted && (c += 1),
            Ti.prototype.translate.apply(this),
            ni(this.points,
            function(t) {
                var e, i = p(t.yBottom, r),
                u = ye(me( - 999 - i, t.plotY), n.len + 999 + i),
                d = t.plotX + l,
                f = a,
                g = ye(u, i),
                u = me(u, i) - g;
                ve(u) < o && o && (u = o, g = pe(ve(g - r) > o ? i - o: r - (n.translate(t.y, 0, 1, 0, 1) <= r ? o: 0))),
                t.barX = d,
                t.pointWidth = s,
                i = ve(d) < .5,
                f = pe(d + f) + h,
                d = pe(d) + h,
                f -= d,
                e = ve(g) < .5,
                u = pe(g + u) + c,
                g = pe(g) + c,
                u -= g,
                i && (d += 1, f -= 1),
                e && (g -= 1, u += 1),
                t.shapeType = "rect",
                t.shapeArgs = {
                    x: d,
                    y: g,
                    width: f,
                    height: u
                }
            })
        },
        getSymbol: ze,
        drawLegendSymbol: Qe.prototype.drawLegendSymbol,
        drawGraph: ze,
        drawPoints: function() {
            var t, i = this,
            n = i.options,
            r = i.chart.renderer;
            ni(i.points,
            function(o) {
                var s = o.plotY,
                a = o.graphic;
                s === Y || isNaN(s) || null === o.y ? a && (o.graphic = a.destroy()) : (t = o.shapeArgs, a ? (di(a), a.animate(e(t))) : o.graphic = r[o.shapeType](t).attr(o.pointAttr[o.selected ? "select": ""]).add(i.group).shadow(n.shadow, null, n.stacking && !n.borderRadius))
            })
        },
        drawTracker: function() {
            var t = this,
            e = t.chart,
            i = e.pointer,
            n = t.options.cursor,
            r = n && {
                cursor: n
            },
            o = function(i) {
                var n, r = i.target;
                for (e.hoverSeries !== t && t.onMouseOver(); r && !n;) n = r.point,
                r = r.parentNode;
                n !== Y && n !== e.hoverPoint && n.onMouseOver(i)
            };
            ni(t.points,
            function(t) {
                t.graphic && (t.graphic.element.point = t),
                t.dataLabel && (t.dataLabel.element.point = t)
            }),
            t._hasTracking || (ni(t.trackerGroups,
            function(e) {
                t[e] && (t[e].addClass("highcharts-tracker").on("mouseover", o).on("mouseout",
                function(t) {
                    i.onTrackerMouseOut(t)
                }).css(r), Ie) && t[e].on("touchstart", o)
            }), t._hasTracking = !0)
        },
        alignDataLabel: function(t, i, n, r, o) {
            var s = this.chart,
            a = s.inverted,
            l = t.dlBox || t.shapeArgs,
            h = t.below || t.plotY > p(this.translatedThreshold, s.plotSizeY),
            c = p(n.inside, !!this.options.stacking);
            l && (r = e(l), a && (r = {
                x: s.plotWidth - r.y - r.height,
                y: s.plotHeight - r.x - r.width,
                width: r.height,
                height: r.width
            }), !c) && (a ? (r.x += h ? 0 : r.width, r.width = 0) : (r.y += h ? r.height: 0, r.height = 0)),
            n.align = p(n.align, !a || c ? "center": h ? "right": "left"),
            n.verticalAlign = p(n.verticalAlign, a || c ? "middle": h ? "top": "bottom"),
            Ti.prototype.alignDataLabel.call(this, t, i, n, r, o)
        },
        animate: function(t) {
            var e = this.yAxis,
            i = this.options,
            n = this.chart.inverted,
            r = {};
            Ne && (t ? (r.scaleY = .001, t = ye(e.pos + e.len, me(e.pos, e.toPixels(i.threshold))), n ? r.translateX = t - e.len: r.translateY = t, this.group.attr(r)) : (r.scaleY = 1, r[n ? "translateX": "translateY"] = e.pos, this.group.animate(r, this.options.animation), this.animate = null))
        },
        remove: function() {
            var t = this,
            e = t.chart;
            e.hasRendered && ni(e.series,
            function(e) {
                e.type === t.type && (e.isDirty = !0)
            }),
            Ti.prototype.remove.apply(t, arguments)
        }
    }),
    Ke.column = xi,
    pi.bar = e(pi.column),
    bi = m(xi, {
        type: "bar",
        inverted: !0
    }),
    Ke.bar = bi,
    pi.scatter = e(Je, {
        lineWidth: 0,
        tooltip: {
            headerFormat: '<span style="font-size: 10px; color:{series.color}">{series.name}</span><br/>',
            pointFormat: "x: <b>{point.x}</b><br/>y: <b>{point.y}</b><br/>",
            followPointer: !0
        },
        stickyTracking: !1
    }),
    bi = m(Ti, {
        type: "scatter",
        sorted: !1,
        requireSorting: !1,
        noSharedTooltip: !0,
        trackerGroups: ["markerGroup"],
        takeOrdinalPosition: !1,
        drawTracker: xi.prototype.drawTracker,
        setTooltipPoints: ze
    }),
    Ke.scatter = bi,
    pi.pie = e(Je, {
        borderColor: "#FFFFFF",
        borderWidth: 1,
        center: [null, null],
        clip: !1,
        colorByPoint: !0,
        dataLabels: {
            distance: 30,
            enabled: !0,
            formatter: function() {
                return this.point.name
            }
        },
        ignoreHiddenPoint: !0,
        legendType: "point",
        marker: null,
        size: null,
        showInLegend: !1,
        slicedOffset: 10,
        states: {
            hover: {
                brightness: .1,
                shadow: !1
            }
        },
        stickyTracking: !1,
        tooltip: {
            followPointer: !0
        }
    }),
    Je = {
        type: "pie",
        isCartesian: !1,
        pointClass: m(wi, {
            init: function() {
                wi.prototype.init.apply(this, arguments);
                var e, i = this;
                return i.y < 0 && (i.y = null),
                t(i, {
                    visible: i.visible !== !1,
                    name: p(i.name, "Slice")
                }),
                e = function(t) {
                    i.slice("select" === t.type)
                },
                ai(i, "select", e),
                ai(i, "unselect", e),
                i
            },
            setVisible: function(t) {
                var e, i = this,
                n = i.series,
                r = n.chart;
                i.visible = i.options.visible = t = t === Y ? !i.visible: t,
                n.options.data[ii(i, n.data)] = i.options,
                e = t ? "show": "hide",
                ni(["graphic", "dataLabel", "connector", "shadowGroup"],
                function(t) {
                    i[t] && i[t][e]()
                }),
                i.legendItem && r.legend.colorizeItem(i, t),
                !n.isDirty && n.options.ignoreHiddenPoint && (n.isDirty = !0, r.redraw())
            },
            slice: function(t, e, i) {
                var n = this.series;
                H(i, n.chart),
                p(e, !0),
                this.sliced = this.options.sliced = t = c(t) ? t: !this.sliced,
                n.options.data[ii(this, n.data)] = this.options,
                t = t ? this.slicedTranslation: {
                    translateX: 0,
                    translateY: 0
                },
                this.graphic.animate(t),
                this.shadowGroup && this.shadowGroup.animate(t)
            }
        }),
        requireSorting: !1,
        noSharedTooltip: !0,
        trackerGroups: ["group", "dataLabelsGroup"],
        pointAttrToOptions: {
            stroke: "borderColor",
            "stroke-width": "borderWidth",
            fill: "color"
        },
        getColor: ze,
        animate: function(t) {
            var e = this,
            i = e.points,
            n = e.startAngleRad;
            t || (ni(i,
            function(t) {
                var i = t.graphic,
                t = t.shapeArgs;
                i && (i.attr({
                    r: e.center[3] / 2,
                    start: n,
                    end: n
                }), i.animate({
                    r: t.r,
                    start: t.start,
                    end: t.end
                },
                e.options.animation))
            }), e.animate = null)
        },
        setData: function(t, e) {
            Ti.prototype.setData.call(this, t, !1),
            this.processData(),
            this.generatePoints(),
            p(e, !0) && this.chart.redraw()
        },
        generatePoints: function() {
            var t, e, i, n, r = 0,
            o = this.options.ignoreHiddenPoint;
            for (Ti.prototype.generatePoints.call(this), e = this.points, i = e.length, t = 0; i > t; t++) n = e[t],
            r += o && !n.visible ? 0 : n.y;
            for (this.total = r, t = 0; i > t; t++) n = e[t],
            n.percentage = r > 0 ? n.y / r * 100 : 0,
            n.total = r
        },
        getCenter: function() {
            var t, e, n = this.options,
            r = this.chart,
            o = 2 * (n.slicedOffset || 0),
            s = r.plotWidth - 2 * o,
            a = r.plotHeight - 2 * o,
            r = n.center,
            n = [p(r[0], "50%"), p(r[1], "50%"), n.size || "100%", n.innerSize || 0],
            l = ye(s, a);
            return si(n,
            function(n, r) {
                return e = /%$/.test(n),
                t = 2 > r || 2 === r && e,
                (e ? [s, a, l, l][r] * i(n) / 100 : n) + (t ? o: 0)
            })
        },
        translate: function(t) {
            this.generatePoints();
            var e, i, n, r, o, s = 0,
            a = this.options,
            l = a.slicedOffset,
            h = l + a.borderWidth,
            c = a.startAngle || 0,
            u = this.startAngleRad = ke / 180 * (c - 90),
            c = (this.endAngleRad = ke / 180 * ((a.endAngle || c + 360) - 90)) - u,
            d = this.points,
            p = a.dataLabels.distance,
            a = a.ignoreHiddenPoint,
            f = d.length;
            for (t || (this.center = t = this.getCenter()), this.getX = function(e, i) {
                return n = de.asin((e - t[1]) / (t[2] / 2 + p)),
                t[0] + (i ? -1 : 1) * xe(n) * (t[2] / 2 + p)
            },
            r = 0; f > r; r++) o = d[r],
            e = u + s * c,
            (!a || o.visible) && (s += o.percentage / 100),
            i = u + s * c,
            o.shapeType = "arc",
            o.shapeArgs = {
                x: t[0],
                y: t[1],
                r: t[2] / 2,
                innerR: t[3] / 2,
                start: pe(1e3 * e) / 1e3,
                end: pe(1e3 * i) / 1e3
            },
            n = (i + e) / 2,
            n > .75 * c && (n -= 2 * ke),
            o.slicedTranslation = {
                translateX: pe(xe(n) * l),
                translateY: pe(be(n) * l)
            },
            e = xe(n) * t[2] / 2,
            i = be(n) * t[2] / 2,
            o.tooltipPos = [t[0] + .7 * e, t[1] + .7 * i],
            o.half = -ke / 2 > n || n > ke / 2 ? 1 : 0,
            o.angle = n,
            h = ye(h, p / 2),
            o.labelPos = [t[0] + e + xe(n) * p, t[1] + i + be(n) * p, t[0] + e + xe(n) * h, t[1] + i + be(n) * h, t[0] + e, t[1] + i, 0 > p ? "center": o.half ? "right": "left", n]
        },
        setTooltipPoints: ze,
        drawGraph: null,
        drawPoints: function() {
            var e, i, n, r, o = this,
            s = o.chart.renderer,
            a = o.options.shadow;
            a && !o.shadowGroup && (o.shadowGroup = s.g("shadow").add(o.group)),
            ni(o.points,
            function(l) {
                i = l.graphic,
                r = l.shapeArgs,
                n = l.shadowGroup,
                a && !n && (n = l.shadowGroup = s.g("shadow").add(o.shadowGroup)),
                e = l.sliced ? l.slicedTranslation: {
                    translateX: 0,
                    translateY: 0
                },
                n && n.attr(e),
                i ? i.animate(t(r, e)) : l.graphic = i = s.arc(r).setRadialReference(o.center).attr(l.pointAttr[l.selected ? "select": ""]).attr({
                    "stroke-linejoin": "round"
                }).attr(e).add(o.group).shadow(a, n),
                l.visible === !1 && l.setVisible(!1)
            })
        },
        sortByAngle: function(t, e) {
            t.sort(function(t, i) {
                return void 0 !== t.angle && (i.angle - t.angle) * e
            })
        },
        drawDataLabels: function() {
            var t, e, i, n, r, o, s, a, l, h, c, u, d = this,
            f = d.data,
            g = d.chart,
            m = d.options.dataLabels,
            y = p(m.connectorPadding, 10),
            v = p(m.connectorWidth, 1),
            x = g.plotWidth,
            g = g.plotHeight,
            b = p(m.softConnector, !0),
            k = m.distance,
            w = d.center,
            T = w[2] / 2,
            S = w[1],
            C = k > 0,
            A = [[], []],
            L = [0, 0, 0, 0],
            P = function(t, e) {
                return e.y - t.y
            };
            if (d.visible && (m.enabled || d._hasPointLabels)) {
                for (Ti.prototype.drawDataLabels.apply(d), ni(f,
                function(t) {
                    t.dataLabel && A[t.half].push(t)
                }), c = 0; ! s && f[c];) s = f[c] && f[c].dataLabel && (f[c].dataLabel.getBBox().height || 21),
                c++;
                for (c = 2; c--;) {
                    var D, f = [],
                    N = [],
                    E = A[c],
                    H = E.length;
                    if (d.sortByAngle(E, c - .5), k > 0) {
                        for (u = S - T - k; S + T + k >= u; u += s) f.push(u);
                        if (r = f.length, H > r) {
                            for (t = [].concat(E), t.sort(P), u = H; u--;) t[u].rank = u;
                            for (u = H; u--;) E[u].rank >= r && E.splice(u, 1);
                            H = E.length
                        }
                        for (u = 0; H > u; u++) {
                            t = E[u],
                            o = t.labelPos,
                            t = 9999;
                            var I, B;
                            for (B = 0; r > B; B++) I = ve(f[B] - o[1]),
                            t > I && (t = I, D = B);
                            if (u > D && null !== f[u]) D = u;
                            else for (H - u + D > r && null !== f[u] && (D = r - H + u); null === f[D];) D++;
                            N.push({
                                i: D,
                                y: f[D]
                            }),
                            f[D] = null
                        }
                        N.sort(P)
                    }
                    for (u = 0; H > u; u++) t = E[u],
                    o = t.labelPos,
                    n = t.dataLabel,
                    h = t.visible === !1 ? "hidden": "visible",
                    t = o[1],
                    k > 0 ? (r = N.pop(), D = r.i, l = r.y, (t > l && null !== f[D + 1] || l > t && null !== f[D - 1]) && (l = t)) : l = t,
                    a = m.justify ? w[0] + (c ? -1 : 1) * (T + k) : d.getX(0 === D || D === f.length - 1 ? t: l, c),
                    n._attr = {
                        visibility: h,
                        align: o[6]
                    },
                    n._pos = {
                        x: a + m.x + ({
                            left: y,
                            right: -y
                        } [o[6]] || 0),
                        y: l + m.y - 10
                    },
                    n.connX = a,
                    n.connY = l,
                    null === this.options.size && (r = n.width, y > a - r ? L[3] = me(pe(r - a + y), L[3]) : a + r > x - y && (L[1] = me(pe(a + r - x + y), L[1])), 0 > l - s / 2 ? L[0] = me(pe( - l + s / 2), L[0]) : l + s / 2 > g && (L[2] = me(pe(l + s / 2 - g), L[2])))
                } (0 === M(L) || this.verifyDataLabelOverflow(L)) && (this.placeDataLabels(), C && v && ni(this.points,
                function(t) {
                    e = t.connector,
                    o = t.labelPos,
                    (n = t.dataLabel) && n._pos ? (h = n._attr.visibility, a = n.connX, l = n.connY, i = b ? ["M", a + ("left" === o[6] ? 5 : -5), l, "C", a, l, 2 * o[2] - o[4], 2 * o[3] - o[5], o[2], o[3], "L", o[4], o[5]] : ["M", a + ("left" === o[6] ? 5 : -5), l, "L", o[2], o[3], "L", o[4], o[5]], e ? (e.animate({
                        d: i
                    }), e.attr("visibility", h)) : t.connector = e = d.chart.renderer.path(i).attr({
                        "stroke-width": v,
                        stroke: m.connectorColor || t.color || "#606060",
                        visibility: h
                    }).add(d.group)) : e && (t.connector = e.destroy())
                }))
            }
        },
        verifyDataLabelOverflow: function(t) {
            var e, i = this.center,
            n = this.options,
            r = n.center,
            o = n = n.minSize || 80;
            return null !== r[0] ? o = me(i[2] - me(t[1], t[3]), n) : (o = me(i[2] - t[1] - t[3], n), i[0] += (t[3] - t[1]) / 2),
            null !== r[1] ? o = me(ye(o, i[2] - me(t[0], t[2])), n) : (o = me(ye(o, i[2] - t[0] - t[2]), n), i[1] += (t[0] - t[2]) / 2),
            o < i[2] ? (i[2] = o, this.translate(i), ni(this.points,
            function(t) {
                t.dataLabel && (t.dataLabel._pos = null)
            }), this.drawDataLabels()) : e = !0,
            e
        },
        placeDataLabels: function() {
            ni(this.points,
            function(t) {
                var e, t = t.dataLabel;
                t && ((e = t._pos) ? (t.attr(t._attr), t[t.moved ? "animate": "attr"](e), t.moved = !0) : t && t.attr({
                    y: -999
                }))
            })
        },
        alignDataLabel: ze,
        drawTracker: xi.prototype.drawTracker,
        drawLegendSymbol: Qe.prototype.drawLegendSymbol,
        getSymbol: ze
    },
    Je = m(Ti, Je),
    Ke.pie = Je,
    t(Highcharts, {
        Axis: j,
        Chart: X,
        Color: yi,
        Legend: _,
        Pointer: F,
        Point: wi,
        Tick: O,
        Tooltip: W,
        Renderer: G,
        Series: Ti,
        SVGElement: B,
        SVGRenderer: vi,
        arrayMin: L,
        arrayMax: M,
        charts: Re,
        dateFormat: $,
        format: b,
        pathAnim: Z,
        getOptions: function() {
            return V
        },
        hasBidiBug: Ee,
        isTouchDevice: Pe,
        numberFormat: y,
        seriesTypes: Ke,
        setOptions: function(t) {
            return V = e(V, t),
            I(),
            V
        },
        addEvent: ai,
        removeEvent: li,
        createElement: g,
        discardElement: D,
        css: f,
        each: ni,
        extend: t,
        map: si,
        merge: e,
        pick: p,
        splat: d,
        extendClass: m,
        pInt: i,
        wrap: x,
        svg: Ne,
        canvas: He,
        vml: !Ne && !He,
        product: "Highcharts",
        version: "3.0.7"
    })
} (),
function(t) {
    t.fn.kxbdSuperMarquee = function(e) {
        var i = t.extend({},
        t.fn.kxbdSuperMarquee.defaults, e);
        return this.each(function() {
            function e() {
                var t = "left" == i.direction || "right" == i.direction ? "scrollLeft": "scrollTop";
                if (i.isMarquee) {
                    if (i.loop > 0 && (N += i.scrollAmount, N > A * i.loop)) return k[t] = 0,
                    clearInterval(c);
                    var n = k[t] + ("left" == i.direction || "up" == i.direction ? 1 : -1) * i.scrollAmount
                } else if (i.duration) {
                    if (! (u++<f)) return n = g,
                    clearInterval(a),
                    void(h = !1);
                    h = !0;
                    var n = Math.ceil(s(u, d, p, f));
                    u == f && (n = g)
                } else {
                    var n = g;
                    clearInterval(a)
                }
                "left" == i.direction || "up" == i.direction ? n >= A && (n -= A) : 0 >= n && (n += A),
                k[t] = n,
                i.isMarquee ? c = setTimeout(e, i.scrollDelay) : f > u ? (a && clearTimeout(a), a = setTimeout(e, i.scrollDelay)) : h = !1
            }
            function n(t) {
                h = !0;
                var n = "left" == i.direction || "right" == i.direction ? "scrollLeft": "scrollTop",
                r = "left" == i.direction || "up" == i.direction ? 1 : -1;
                D += r,
                void 0 == t && i.navId && (x.eq(P).removeClass("navOn"), P += r, P >= y ? P = 0 : 0 > P && (P = y - 1), x.eq(P).addClass("navOn"), D = P);
                var o = 0 > D ? A: 0;
                u = 0,
                d = k[n],
                g = void 0 != t ? t: o + i.distance * D % A,
                p = 1 == r ? g > d ? g - d: g + A - d: g > d ? g - A - d: g - d,
                f = i.duration,
                a && clearTimeout(a),
                a = setTimeout(e, i.scrollDelay)
            }
            function r() {
                l = setInterval(function() {
                    n()
                },
                1e3 * i.time)
            }
            function o() {
                clearInterval(l)
            }
            function s(t, e, i, n) {
                return - i * (t /= n) * (t - 2) + e
            }
            var a, l, h, c, u, d, p, f, g, m, y, v, x, b = t(this),
            k = b.get(0),
            w = b.width(),
            T = b.height(),
            S = b.children(),
            C = S.children(),
            A = 0,
            L = "left" == i.direction || "right" == i.direction ? 1 : 0,
            M = [],
            P = 0,
            D = 0,
            N = 0;
            S.css(L ? "width": "height", 1e4);
            var E = "<ul>";
            if (i.isEqual) {
                m = C[L ? "outerWidth": "outerHeight"](),
                y = C.length,
                A = m * y;
                for (var H = 0; y > H; H++) M.push(H * m),
                E += "<li>" + (H + 1) + "</li>"
            } else C.each(function(e) {
                M.push(A),
                A += t(this)[L ? "outerWidth": "outerHeight"](),
                E += "<li>" + (e + 1) + "</li>"
            });
            E += "</ul>",
            (L ? w: T) > A || (S.append(C.clone()).css(L ? "width": "height", 2 * A), i.navId && (v = t(i.navId).append(E).hover(o, r), x = t("li", v), x.each(function(e) {
                t(this).bind(i.eventNav,
                function() {
                    h || P != e && (n(M[e]), x.eq(P).removeClass("navOn"), P = e, t(this).addClass("navOn"))
                })
            }), x.eq(P).addClass("navOn")), k[L ? "scrollLeft": "scrollTop"] = "right" == i.direction || "down" == i.direction ? A: 0, i.isMarquee ? (c = setTimeout(e, i.scrollDelay), i.controlBtn && t.each(i.controlBtn,
            function(e, n) {
                t(n).bind(i.eventA,
                function() {
                    i.direction = e,
                    i.oldAmount = i.scrollAmount,
                    i.scrollAmount = i.newAmount
                }).bind(i.eventB,
                function() {
                    i.scrollAmount = i.oldAmount
                })
            })) : (i.isAuto && (r(), b.hover(o, r)), i.btnGo && t.each(i.btnGo,
            function(e, s) {
                t(s).bind(i.eventGo,
                function() {
                    1 != h && (i.direction = e, n(), i.isAuto && (o(), r()))
                })
            })))
        })
    },
    t.fn.kxbdSuperMarquee.defaults = {
        isMarquee: !1,
        isEqual: !0,
        loop: 0,
        newAmount: 3,
        eventA: "mousedown",
        eventB: "mouseup",
        isAuto: !0,
        time: 5,
        duration: 50,
        eventGo: "click",
        direction: "left",
        scrollAmount: 1,
        scrollDelay: 10,
        eventNav: "click"
    },
    t.fn.kxbdSuperMarquee.setDefaults = function(e) {
        t.extend(t.fn.kxbdSuperMarquee.defaults, e)
    }
} (jQuery);