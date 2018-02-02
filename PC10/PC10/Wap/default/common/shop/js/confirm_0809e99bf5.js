window.Utils = window.Utils || {},
$.extend(window.Utils, {
    validMobile: function(e) {
        return e = "" + e,
        /^((\+86)|(86))?(1)\d{10}$/.test(e)
    },
    validPhone: function(e) {
        return e = "" + e,
        /^0[0-9\-]{10,13}$/.test(e)
    },
    validNumber: function(e) {
        return /^\d+$/.test(e)
    },
    validEmail: function(e) {
        return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(e)
    },
    validPostalCode: function(e) {
        return e = "" + e,
        /^\d{6}$/.test(e)
    }
}),
define("wap/components/util/valid",
function() {}),
define("wap/components/address/model/address", ["wap/components/util/valid"],
function() {
    return Backbone.Model.extend({
        urlRoot: window._global.url.trade + "/buyer/address/item.json",
        defaults: {
            tel: "",
            province: "",
            postal_code: "",
            county: "",
            city: "",
            area_code: "",
            address_detail: "",
            user_name: ""
        },
        validate: function(e) {
            var t = window.Utils;
            return e.user_name ? e.tel ? e.area_code && "0" !== e.area_code ? e.address_detail ? "" === e.postal_code || t.validPostalCode(e.postal_code) ? t.validMobile(e.tel) || t.validPhone(e.tel) ? void 0 : {
                msg: "请填写正确的<br />手机号码或电话号码",
                name: "tel"
            }: {
                msg: "邮政编码格式不正确",
                name: "postal_code"
            }: {
                msg: "请填写详细地址",
                name: "address_detail"
            }: {
                msg: "请选择地区",
                name: ""
            }: {
                msg: "请填写联系电话",
                name: "tel"
            }: {
                msg: "请填写名字",
                name: "user_name"
            }
        },
        update: function(e, t) {
            return $.ajax({
                url: this.url(),
                type: t || "POST",
                dataType: "JSON",
                data: e
            })
        }
    })
}),
define("wap/components/address/model/express_address_collection", ["wap/components/address/model/address"],
function(e) {
    return Backbone.Collection.extend({
        model: e,
        url: window._global.url.trade + "/buyer/address/list.json",
        parse: function(e) {
            var t = (e || {}).data || [];
            return this.total = parseInt(t.length),
            t
        }
    })
}),
window.Utils = window.Utils || {},
$.extend(window.Utils, {
    makeRandomString: function(e) {
        var t = "",
        n = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        e = e || 10;
        for (var i = 0; e > i; i++) t += n.charAt(Math.floor(Math.random() * n.length));
        return t
    }
}),
define("wap/components/util/number",
function() {}),
define("text!wap/components/address/templates/addressForm.html", [],
function() {
    return '<form class="js-address-fm address-ui address-fm">\n    <div class="block" style="margin-bottom:10px;">\n        <% if(typeof data.id !== \'undefined\') { %>\n            <input type="hidden" name="id" value="<%=data.id %>" />\n        <% } %>\n        <div class="block-module">\n            <label class="form-row form-text-row">\n                <em class="form-text-label">收货人</em>\n                <span class="input-wrapper"><input type="text" name="user_name" class="form-text-input" value="<%=data.user_name %>" placeholder="名字" /></span>\n            </label>\n        </div>\n        <div class="block-module">\n            <label class="form-row form-text-row">\n                <em class="form-text-label">联系电话</em>\n                <span class="input-wrapper"><input type="tel" name="tel" class="form-text-input" value="<%=data.tel %>" placeholder="手机或固话" /></span>\n            </label>\n        </div>\n        <div class="block-module">\n            <div class="form-row form-text-row">\n                <em class="form-text-label">选择地区</em>\n                <div class="input-wrapper input-region js-area-select">\n                   \n                </div>\n            </div>\n        </div>\n        <div class="block-module">\n            <label class="form-row form-text-row">\n                <em class="form-text-label">详细地址</em>\n                <span class="input-wrapper"><input type="text" name="address_detail" class="form-text-input" value="<%=data.address_detail %>" placeholder="街道门牌信息" /></span>\n            </label>\n        </div>\n        <div class="block-module">\n            <label class="form-row form-text-row">\n                <em class="form-text-label">邮政编码</em>\n                <span class="input-wrapper"><input type="tel" maxlength="6" name="postal_code" class="form-text-input" value="<%=data.postal_code %>" placeholder="邮政编码" /></span>\n            </label>\n        </div>    \n    </div>\n    <% if(typeof data.id !== \'undefined\' && !cannotDelete) { %>\n        <div class="block js-address-delete block-top-0">\n            <div class="block-module">\n                <a class=\'delete\' href="javascript:;">删除收货地址</a>\n            </div>\n        </div>\n    <% } %>\n    <div>\n        <div class="action-container">\n            <a class="js-address-save btn btn-block btn-blue">保存</a>\n            <a class="js-address-cancel btn btn-block">取消</a>\n        </div>\n    </div>\n</form>'
}),
function() {
    var e = this,
    t = e._,
    n = {},
    i = Array.prototype,
    s = Object.prototype,
    o = Function.prototype,
    a = i.push,
    r = i.slice,
    d = i.concat,
    l = s.toString,
    c = s.hasOwnProperty,
    u = i.forEach,
    h = i.map,
    p = i.reduce,
    f = i.reduceRight,
    m = i.filter,
    v = i.every,
    w = i.some,
    g = i.indexOf,
    y = i.lastIndexOf,
    _ = Array.isArray,
    b = Object.keys,
    C = o.bind,
    x = function(e) {
        return e instanceof x ? e: this instanceof x ? void(this._wrapped = e) : new x(e)
    };
    "undefined" != typeof exports ? ("undefined" != typeof module && module.exports && (exports = module.exports = x), exports._ = x) : e._ = x,
    x.VERSION = "1.5.2";
    var k = x.each = x.forEach = function(e, t, i) {
        if (null != e) if (u && e.forEach === u) e.forEach(t, i);
        else if (e.length === +e.length) {
            for (var s = 0,
            o = e.length; o > s; s++) if (t.call(i, e[s], s, e) === n) return
        } else for (var a = x.keys(e), s = 0, o = a.length; o > s; s++) if (t.call(i, e[a[s]], a[s], e) === n) return
    };
    x.map = x.collect = function(e, t, n) {
        var i = [];
        return null == e ? i: h && e.map === h ? e.map(t, n) : (k(e,
        function(e, s, o) {
            i.push(t.call(n, e, s, o))
        }), i)
    };
    var A = "Reduce of empty array with no initial value";
    x.reduce = x.foldl = x.inject = function(e, t, n, i) {
        var s = arguments.length > 2;
        if (null == e && (e = []), p && e.reduce === p) return i && (t = x.bind(t, i)),
        s ? e.reduce(t, n) : e.reduce(t);
        if (k(e,
        function(e, o, a) {
            s ? n = t.call(i, n, e, o, a) : (n = e, s = !0)
        }), !s) throw new TypeError(A);
        return n
    },
    x.reduceRight = x.foldr = function(e, t, n, i) {
        var s = arguments.length > 2;
        if (null == e && (e = []), f && e.reduceRight === f) return i && (t = x.bind(t, i)),
        s ? e.reduceRight(t, n) : e.reduceRight(t);
        var o = e.length;
        if (o !== +o) {
            var a = x.keys(e);
            o = a.length
        }
        if (k(e,
        function(r, d, l) {
            d = a ? a[--o] : --o,
            s ? n = t.call(i, n, e[d], d, l) : (n = e[d], s = !0)
        }), !s) throw new TypeError(A);
        return n
    },
    x.find = x.detect = function(e, t, n) {
        var i;
        return S(e,
        function(e, s, o) {
            return t.call(n, e, s, o) ? (i = e, !0) : void 0
        }),
        i
    },
    x.filter = x.select = function(e, t, n) {
        var i = [];
        return null == e ? i: m && e.filter === m ? e.filter(t, n) : (k(e,
        function(e, s, o) {
            t.call(n, e, s, o) && i.push(e)
        }), i)
    },
    x.reject = function(e, t, n) {
        return x.filter(e,
        function(e, i, s) {
            return ! t.call(n, e, i, s)
        },
        n)
    },
    x.every = x.all = function(e, t, i) {
        t || (t = x.identity);
        var s = !0;
        return null == e ? s: v && e.every === v ? e.every(t, i) : (k(e,
        function(e, o, a) {
            return (s = s && t.call(i, e, o, a)) ? void 0 : n
        }), !!s)
    };
    var S = x.some = x.any = function(e, t, i) {
        t || (t = x.identity);
        var s = !1;
        return null == e ? s: w && e.some === w ? e.some(t, i) : (k(e,
        function(e, o, a) {
            return s || (s = t.call(i, e, o, a)) ? n: void 0
        }), !!s)
    };
    x.contains = x.include = function(e, t) {
        return null == e ? !1 : g && e.indexOf === g ? -1 != e.indexOf(t) : S(e,
        function(e) {
            return e === t
        })
    },
    x.invoke = function(e, t) {
        var n = r.call(arguments, 2),
        i = x.isFunction(t);
        return x.map(e,
        function(e) {
            return (i ? t: e[t]).apply(e, n)
        })
    },
    x.pluck = function(e, t) {
        return x.map(e,
        function(e) {
            return e[t]
        })
    },
    x.where = function(e, t, n) {
        return x.isEmpty(t) ? n ? void 0 : [] : x[n ? "find": "filter"](e,
        function(e) {
            for (var n in t) if (t[n] !== e[n]) return ! 1;
            return ! 0
        })
    },
    x.findWhere = function(e, t) {
        return x.where(e, t, !0)
    },
    x.max = function(e, t, n) {
        if (!t && x.isArray(e) && e[0] === +e[0] && e.length < 65535) return Math.max.apply(Math, e);
        if (!t && x.isEmpty(e)) return - 1 / 0;
        var i = {
            computed: -1 / 0,
            value: -1 / 0
        };
        return k(e,
        function(e, s, o) {
            var a = t ? t.call(n, e, s, o) : e;
            a > i.computed && (i = {
                value: e,
                computed: a
            })
        }),
        i.value
    },
    x.min = function(e, t, n) {
        if (!t && x.isArray(e) && e[0] === +e[0] && e.length < 65535) return Math.min.apply(Math, e);
        if (!t && x.isEmpty(e)) return 1 / 0;
        var i = {
            computed: 1 / 0,
            value: 1 / 0
        };
        return k(e,
        function(e, s, o) {
            var a = t ? t.call(n, e, s, o) : e;
            a < i.computed && (i = {
                value: e,
                computed: a
            })
        }),
        i.value
    },
    x.shuffle = function(e) {
        var t, n = 0,
        i = [];
        return k(e,
        function(e) {
            t = x.random(n++),
            i[n - 1] = i[t],
            i[t] = e
        }),
        i
    },
    x.sample = function(e, t, n) {
        return arguments.length < 2 || n ? e[x.random(e.length - 1)] : x.shuffle(e).slice(0, Math.max(0, t))
    };
    var F = function(e) {
        return x.isFunction(e) ? e: function(t) {
            return t[e]
        }
    };
    x.sortBy = function(e, t, n) {
        var i = F(t);
        return x.pluck(x.map(e,
        function(e, t, s) {
            return {
                value: e,
                index: t,
                criteria: i.call(n, e, t, s)
            }
        }).sort(function(e, t) {
            var n = e.criteria,
            i = t.criteria;
            if (n !== i) {
                if (n > i || void 0 === n) return 1;
                if (i > n || void 0 === i) return - 1
            }
            return e.index - t.index
        }), "value")
    };
    var T = function(e) {
        return function(t, n, i) {
            var s = {},
            o = null == n ? x.identity: F(n);
            return k(t,
            function(n, a) {
                var r = o.call(i, n, a, t);
                e(s, r, n)
            }),
            s
        }
    };
    x.groupBy = T(function(e, t, n) { (x.has(e, t) ? e[t] : e[t] = []).push(n)
    }),
    x.indexBy = T(function(e, t, n) {
        e[t] = n
    }),
    x.countBy = T(function(e, t) {
        x.has(e, t) ? e[t]++:e[t] = 1
    }),
    x.sortedIndex = function(e, t, n, i) {
        n = null == n ? x.identity: F(n);
        for (var s = n.call(i, t), o = 0, a = e.length; a > o;) {
            var r = o + a >>> 1;
            n.call(i, e[r]) < s ? o = r + 1 : a = r
        }
        return o
    },
    x.toArray = function(e) {
        return e ? x.isArray(e) ? r.call(e) : e.length === +e.length ? x.map(e, x.identity) : x.values(e) : []
    },
    x.size = function(e) {
        return null == e ? 0 : e.length === +e.length ? e.length: x.keys(e).length
    },
    x.first = x.head = x.take = function(e, t, n) {
        return null == e ? void 0 : null == t || n ? e[0] : r.call(e, 0, t)
    },
    x.initial = function(e, t, n) {
        return r.call(e, 0, e.length - (null == t || n ? 1 : t))
    },
    x.last = function(e, t, n) {
        return null == e ? void 0 : null == t || n ? e[e.length - 1] : r.call(e, Math.max(e.length - t, 0))
    },
    x.rest = x.tail = x.drop = function(e, t, n) {
        return r.call(e, null == t || n ? 1 : t)
    },
    x.compact = function(e) {
        return x.filter(e, x.identity)
    };
    var j = function(e, t, n) {
        return t && x.every(e, x.isArray) ? d.apply(n, e) : (k(e,
        function(e) {
            x.isArray(e) || x.isArguments(e) ? t ? a.apply(n, e) : j(e, t, n) : n.push(e)
        }), n)
    };
    x.flatten = function(e, t) {
        return j(e, t, [])
    },
    x.without = function(e) {
        return x.difference(e, r.call(arguments, 1))
    },
    x.uniq = x.unique = function(e, t, n, i) {
        x.isFunction(t) && (i = n, n = t, t = !1);
        var s = n ? x.map(e, n, i) : e,
        o = [],
        a = [];
        return k(s,
        function(n, i) { (t ? i && a[a.length - 1] === n: x.contains(a, n)) || (a.push(n), o.push(e[i]))
        }),
        o
    },
    x.union = function() {
        return x.uniq(x.flatten(arguments, !0))
    },
    x.intersection = function(e) {
        var t = r.call(arguments, 1);
        return x.filter(x.uniq(e),
        function(e) {
            return x.every(t,
            function(t) {
                return x.indexOf(t, e) >= 0
            })
        })
    },
    x.difference = function(e) {
        var t = d.apply(i, r.call(arguments, 1));
        return x.filter(e,
        function(e) {
            return ! x.contains(t, e)
        })
    },
    x.zip = function() {
        for (var e = x.max(x.pluck(arguments, "length").concat(0)), t = new Array(e), n = 0; e > n; n++) t[n] = x.pluck(arguments, "" + n);
        return t
    },
    x.object = function(e, t) {
        if (null == e) return {};
        for (var n = {},
        i = 0,
        s = e.length; s > i; i++) t ? n[e[i]] = t[i] : n[e[i][0]] = e[i][1];
        return n
    },
    x.indexOf = function(e, t, n) {
        if (null == e) return - 1;
        var i = 0,
        s = e.length;
        if (n) {
            if ("number" != typeof n) return i = x.sortedIndex(e, t),
            e[i] === t ? i: -1;
            i = 0 > n ? Math.max(0, s + n) : n
        }
        if (g && e.indexOf === g) return e.indexOf(t, n);
        for (; s > i; i++) if (e[i] === t) return i;
        return - 1
    },
    x.lastIndexOf = function(e, t, n) {
        if (null == e) return - 1;
        var i = null != n;
        if (y && e.lastIndexOf === y) return i ? e.lastIndexOf(t, n) : e.lastIndexOf(t);
        for (var s = i ? n: e.length; s--;) if (e[s] === t) return s;
        return - 1
    },
    x.range = function(e, t, n) {
        arguments.length <= 1 && (t = e || 0, e = 0),
        n = arguments[2] || 1;
        for (var i = Math.max(Math.ceil((t - e) / n), 0), s = 0, o = new Array(i); i > s;) o[s++] = e,
        e += n;
        return o
    };
    var P = function() {};
    x.bind = function(e, t) {
        var n, i;
        if (C && e.bind === C) return C.apply(e, r.call(arguments, 1));
        if (!x.isFunction(e)) throw new TypeError;
        return n = r.call(arguments, 2),
        i = function() {
            if (! (this instanceof i)) return e.apply(t, n.concat(r.call(arguments)));
            P.prototype = e.prototype;
            var s = new P;
            P.prototype = null;
            var o = e.apply(s, n.concat(r.call(arguments)));
            return Object(o) === o ? o: s
        }
    },
    x.partial = function(e) {
        var t = r.call(arguments, 1);
        return function() {
            return e.apply(this, t.concat(r.call(arguments)))
        }
    },
    x.bindAll = function(e) {
        var t = r.call(arguments, 1);
        if (0 === t.length) throw new Error("bindAll must be passed function names");
        return k(t,
        function(t) {
            e[t] = x.bind(e[t], e)
        }),
        e
    },
    x.memoize = function(e, t) {
        var n = {};
        return t || (t = x.identity),
        function() {
            var i = t.apply(this, arguments);
            return x.has(n, i) ? n[i] : n[i] = e.apply(this, arguments)
        }
    },
    x.delay = function(e, t) {
        var n = r.call(arguments, 2);
        return setTimeout(function() {
            return e.apply(null, n)
        },
        t)
    },
    x.defer = function(e) {
        return x.delay.apply(x, [e, 1].concat(r.call(arguments, 1)))
    },
    x.throttle = function(e, t, n) {
        var i, s, o, a = null,
        r = 0;
        n || (n = {});
        var d = function() {
            r = n.leading === !1 ? 0 : new Date,
            a = null,
            o = e.apply(i, s)
        };
        return function() {
            var l = new Date;
            r || n.leading !== !1 || (r = l);
            var c = t - (l - r);
            return i = this,
            s = arguments,
            0 >= c ? (clearTimeout(a), a = null, r = l, o = e.apply(i, s)) : a || n.trailing === !1 || (a = setTimeout(d, c)),
            o
        }
    },
    x.debounce = function(e, t, n) {
        var i, s, o, a, r;
        return function() {
            o = this,
            s = arguments,
            a = new Date;
            var d = function() {
                var l = new Date - a;
                t > l ? i = setTimeout(d, t - l) : (i = null, n || (r = e.apply(o, s)))
            },
            l = n && !i;
            return i || (i = setTimeout(d, t)),
            l && (r = e.apply(o, s)),
            r
        }
    },
    x.once = function(e) {
        var t, n = !1;
        return function() {
            return n ? t: (n = !0, t = e.apply(this, arguments), e = null, t)
        }
    },
    x.wrap = function(e, t) {
        return function() {
            var n = [e];
            return a.apply(n, arguments),
            t.apply(this, n)
        }
    },
    x.compose = function() {
        var e = arguments;
        return function() {
            for (var t = arguments,
            n = e.length - 1; n >= 0; n--) t = [e[n].apply(this, t)];
            return t[0]
        }
    },
    x.after = function(e, t) {
        return function() {
            return--e < 1 ? t.apply(this, arguments) : void 0
        }
    },
    x.keys = b ||
    function(e) {
        if (e !== Object(e)) throw new TypeError("Invalid object");
        var t = [];
        for (var n in e) x.has(e, n) && t.push(n);
        return t
    },
    x.values = function(e) {
        for (var t = x.keys(e), n = t.length, i = new Array(n), s = 0; n > s; s++) i[s] = e[t[s]];
        return i
    },
    x.pairs = function(e) {
        for (var t = x.keys(e), n = t.length, i = new Array(n), s = 0; n > s; s++) i[s] = [t[s], e[t[s]]];
        return i
    },
    x.invert = function(e) {
        for (var t = {},
        n = x.keys(e), i = 0, s = n.length; s > i; i++) t[e[n[i]]] = n[i];
        return t
    },
    x.functions = x.methods = function(e) {
        var t = [];
        for (var n in e) x.isFunction(e[n]) && t.push(n);
        return t.sort()
    },
    x.extend = function(e) {
        return k(r.call(arguments, 1),
        function(t) {
            if (t) for (var n in t) e[n] = t[n]
        }),
        e
    },
    x.pick = function(e) {
        var t = {},
        n = d.apply(i, r.call(arguments, 1));
        return k(n,
        function(n) {
            n in e && (t[n] = e[n])
        }),
        t
    },
    x.omit = function(e) {
        var t = {},
        n = d.apply(i, r.call(arguments, 1));
        for (var s in e) x.contains(n, s) || (t[s] = e[s]);
        return t
    },
    x.defaults = function(e) {
        return k(r.call(arguments, 1),
        function(t) {
            if (t) for (var n in t) void 0 === e[n] && (e[n] = t[n])
        }),
        e
    },
    x.clone = function(e) {
        return x.isObject(e) ? x.isArray(e) ? e.slice() : x.extend({},
        e) : e
    },
    x.tap = function(e, t) {
        return t(e),
        e
    };
    var $ = function(e, t, n, i) {
        if (e === t) return 0 !== e || 1 / e == 1 / t;
        if (null == e || null == t) return e === t;
        e instanceof x && (e = e._wrapped),
        t instanceof x && (t = t._wrapped);
        var s = l.call(e);
        if (s != l.call(t)) return ! 1;
        switch (s) {
        case "[object String]":
            return e == String(t);
        case "[object Number]":
            return e != +e ? t != +t: 0 == e ? 1 / e == 1 / t: e == +t;
        case "[object Date]":
        case "[object Boolean]":
            return + e == +t;
        case "[object RegExp]":
            return e.source == t.source && e.global == t.global && e.multiline == t.multiline && e.ignoreCase == t.ignoreCase
        }
        if ("object" != typeof e || "object" != typeof t) return ! 1;
        for (var o = n.length; o--;) if (n[o] == e) return i[o] == t;
        var a = e.constructor,
        r = t.constructor;
        if (a !== r && !(x.isFunction(a) && a instanceof a && x.isFunction(r) && r instanceof r)) return ! 1;
        n.push(e),
        i.push(t);
        var d = 0,
        c = !0;
        if ("[object Array]" == s) {
            if (d = e.length, c = d == t.length) for (; d--&&(c = $(e[d], t[d], n, i)););
        } else {
            for (var u in e) if (x.has(e, u) && (d++, !(c = x.has(t, u) && $(e[u], t[u], n, i)))) break;
            if (c) {
                for (u in t) if (x.has(t, u) && !d--) break;
                c = !d
            }
        }
        return n.pop(),
        i.pop(),
        c
    };
    x.isEqual = function(e, t) {
        return $(e, t, [], [])
    },
    x.isEmpty = function(e) {
        if (null == e) return ! 0;
        if (x.isArray(e) || x.isString(e)) return 0 === e.length;
        for (var t in e) if (x.has(e, t)) return ! 1;
        return ! 0
    },
    x.isElement = function(e) {
        return ! (!e || 1 !== e.nodeType)
    },
    x.isArray = _ ||
    function(e) {
        return "[object Array]" == l.call(e)
    },
    x.isObject = function(e) {
        return e === Object(e)
    },
    k(["Arguments", "Function", "String", "Number", "Date", "RegExp"],
    function(e) {
        x["is" + e] = function(t) {
            return l.call(t) == "[object " + e + "]"
        }
    }),
    x.isArguments(arguments) || (x.isArguments = function(e) {
        return ! (!e || !x.has(e, "callee"))
    }),
    "function" != typeof / . / &&(x.isFunction = function(e) {
        return "function" == typeof e
    }),
    x.isFinite = function(e) {
        return isFinite(e) && !isNaN(parseFloat(e))
    },
    x.isNaN = function(e) {
        return x.isNumber(e) && e != +e
    },
    x.isBoolean = function(e) {
        return e === !0 || e === !1 || "[object Boolean]" == l.call(e)
    },
    x.isNull = function(e) {
        return null === e
    },
    x.isUndefined = function(e) {
        return void 0 === e
    },
    x.has = function(e, t) {
        return c.call(e, t)
    },
    x.noConflict = function() {
        return e._ = t,
        this
    },
    x.identity = function(e) {
        return e
    },
    x.times = function(e, t, n) {
        for (var i = Array(Math.max(0, e)), s = 0; e > s; s++) i[s] = t.call(n, s);
        return i
    },
    x.random = function(e, t) {
        return null == t && (t = e, e = 0),
        e + Math.floor(Math.random() * (t - e + 1))
    };
    var V = {
        escape: {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#x27;"
        }
    };
    V.unescape = x.invert(V.escape);
    var D = {
        escape: new RegExp("[" + x.keys(V.escape).join("") + "]", "g"),
        unescape: new RegExp("(" + x.keys(V.unescape).join("|") + ")", "g")
    };
    x.each(["escape", "unescape"],
    function(e) {
        x[e] = function(t) {
            return null == t ? "": ("" + t).replace(D[e],
            function(t) {
                return V[e][t]
            })
        }
    }),
    x.result = function(e, t) {
        if (null == e) return void 0;
        var n = e[t];
        return x.isFunction(n) ? n.call(e) : n
    },
    x.mixin = function(e) {
        k(x.functions(e),
        function(t) {
            var n = x[t] = e[t];
            x.prototype[t] = function() {
                var e = [this._wrapped];
                return a.apply(e, arguments),
                B.call(this, n.apply(x, e))
            }
        })
    };
    var E = 0;
    x.uniqueId = function(e) {
        var t = ++E + "";
        return e ? e + t: t
    },
    x.templateSettings = {
        evaluate: /<%([\s\S]+?)%>/g,
        interpolate: /<%=([\s\S]+?)%>/g,
        escape: /<%-([\s\S]+?)%>/g
    };
    var L = /(.)^/,
    I = {
        "'": "'",
        "\\": "\\",
        "\r": "r",
        "\n": "n",
        "	": "t",
        "\u2028": "u2028",
        "\u2029": "u2029"
    },
    O = /\\|'|\r|\n|\t|\u2028|\u2029/g;
    x.template = function(e, t, n) {
        var i;
        n = x.defaults({},
        n, x.templateSettings);
        var s = new RegExp([(n.escape || L).source, (n.interpolate || L).source, (n.evaluate || L).source].join("|") + "|$", "g"),
        o = 0,
        a = "__p+='";
        e.replace(s,
        function(t, n, i, s, r) {
            return a += e.slice(o, r).replace(O,
            function(e) {
                return "\\" + I[e]
            }),
            n && (a += "'+\n((__t=(" + n + "))==null?'':_.escape(__t))+\n'"),
            i && (a += "'+\n((__t=(" + i + "))==null?'':__t)+\n'"),
            s && (a += "';\n" + s + "\n__p+='"),
            o = r + t.length,
            t
        }),
        a += "';\n",
        n.variable || (a = "with(obj||{}){\n" + a + "}\n"),
        a = "var __t,__p='',__j=Array.prototype.join,print=function(){__p+=__j.call(arguments,'');};\n" + a + "return __p;\n";
        try {
            i = new Function(n.variable || "obj", "_", a)
        } catch(r) {
            throw r.source = a,
            r
        }
        if (t) return i(t, x);
        var d = function(e) {
            return i.call(this, e, x)
        };
        return d.source = "function(" + (n.variable || "obj") + "){\n" + a + "}",
        d
    },
    x.chain = function(e) {
        return x(e).chain()
    };
    var B = function(e) {
        return this._chain ? x(e).chain() : e
    };
    x.mixin(x),
    k(["pop", "push", "reverse", "shift", "sort", "splice", "unshift"],
    function(e) {
        var t = i[e];
        x.prototype[e] = function() {
            var n = this._wrapped;
            return t.apply(n, arguments),
            "shift" != e && "splice" != e || 0 !== n.length || delete n[0],
            B.call(this, n)
        }
    }),
    k(["concat", "join", "slice"],
    function(e) {
        var t = i[e];
        x.prototype[e] = function() {
            return B.call(this, t.apply(this._wrapped, arguments))
        }
    }),
    x.extend(x.prototype, {
        chain: function() {
            return this._chain = !0,
            this
        },
        value: function() {
            return this._wrapped
        }
    })
}.call(this),
define("underscore",
function(e) {
    return function() {
        var t;
        return t || e._
    }
} (this)),
define("components/regions_data/main", ["require", "underscore"],
function(e) {
    function t(e, t) {
        u.chain(e).keys().forEach(t)
    }
    function n(e) {
        return e.charAt(0).toUpperCase() + e.slice(1)
    }
    function i(e, t) {
        var n = new $.Deferred;
        return $.ajax({
            url: e,
            dataType: "jsonp",
            data: t
        }).then(function(e) {
            0 === e.code ? n.resolve(e.data) : n.reject(e.msg)
        }).fail(function() {
            n.reject("fail")
        }),
        n.promise()
    }
    function s(e) {
        if (void 0 != e) {
            e += "";
            var t = e.length;
            return "00" == e.slice( - 2) ? s(e.slice(0, t - 2)) : e
        }
    }
    function o(e, t) {
        if (void 0 != e) {
            e += "";
            var n = t - e.length;
            if (n > 0) for (; n--;) e += "0";
            return e
        }
    }
    function a(e, n) {
        t(e,
        function(t) {
            n[t] = e[t]
        })
    }
    function r(e) {
        var t;
        if (!c || c._beforeStr != e) return t = u.isString(e) ? -1 != e.indexOf("r") ? m.cacheAll() : -1 != e.indexOf("c") ? m.cacheProvinceCityList() : m.cacheProvinceList() : m.cacheProvinceList(),
        c = t,
        c._beforeStr = e,
        t
    }
    function d(e) {
        var n, r = new $.Deferred;
        if (0 > e) return [];
        if (void 0 === e) return n = [],
        t(f.province_list,
        function(e) {
            n.push({
                id: e,
                name: f.province_list[e]
            })
        }),
        r.resolve(n),
        r.promise();
        var d, l = s(e),
        e = o(e, 6),
        c = [];
        if (d = v[Math.floor(l.length / 2)], void 0 === d) r.resolve(c);
        else if (t(f[d],
        function(e) {
            e.slice(0, l.length) == l && c.push({
                id: e,
                name: f[d][e]
            })
        }), 0 === c.length) {
            var u = p.list;
            if (void 0 !== w[e]) return w[e];
            w[e] = r.promise(),
            i(u, {
                region_id: e
            }).then(function(e) {
                a(e, f[d]),
                t(e,
                function(t) {
                    c.push({
                        id: t,
                        name: e[t]
                    })
                }),
                r.resolve(c)
            }).always(function() {
                delete w[e]
            })
        } else r.resolve(c);
        return r.promise()
    }
    function l(e) {
        return c ? c.then(function() {
            return d(e)
        }) : d(e)
    }
    var c, u = e("underscore"),
    h = _global.url.www + "/common/region",
    p = {
        cityList: "/cityList.jsonp",
        countyList: "/countyList.jsonp",
        provinceList: "/provinceList.jsonp",
        provinceCityList: "/provinceCityList.jsonp",
        list: "/list.jsonp",
        all: "/all.jsonp"
    },
    f = {},
    m = {},
    v = ["province_list", "city_list", "county_list"],
    w = {};
    return t(v,
    function(e) {
        f[v[e]] = {}
    }),
    t(p,
    function(e) {
        p[e] = h + p[e],
        -1 != ["provinceList", "provinceCityList", "all"].indexOf(e) && (m["cache" + n(e)] = function() {
            return i(p[e]).then(function(t) {
                return u.extend(f, "provinceList" == e ? {
                    province_list: t
                }: t),
                $.Deferred().resolve(f)
            })
        })
    }),
    {
        preload: r,
        loadList: l
    }
}),
define("text!wap/components/address/templates/areaSelect.html", [],
function() {
    return '<span>\n	<select id="province" name="province" data-next-type="城市" data-next="city">\n		<option data-code="0" value="省份">省份</option>\n	</select>\n</span>\n<span>\n	<select id="city" name="city" data-next-type="区县" data-next="county">\n		<option data-code="0" value="城市">城市</option>\n	</select>\n</span>\n<span>\n	<select id="county" name="county">\n		<option data-code="0" value="区县">区县</option>\n	</select>\n</span>'
}),
define("text!wap/components/address/templates/areaOption.html", [],
function() {
    return '<option data-code="0" value="<%= type %>"><%= type %></option>\n<% _.each(data, function(value, key, list){ %>\n	<option data-code="<%= value.id %>" value="<%= value.name %>" <%- value.id == selectedValue ? \'selected\' : \'\' %>>\n		<%= value.name %>\n	</option>\n<%}) %>\n\n'
}),
define("wap/components/address/logistics_area", ["components/regions_data/main", "text!wap/components/address/templates/areaSelect.html", "text!wap/components/address/templates/areaOption.html"],
function(e, t, n) {
    var i = function() {},
    s = Backbone.View.extend({
        optionTemplate: _.template(n),
        initialize: function(e) {
            return e = e || {},
            this.loadingAddress = !1,
            this
        },
        events: {
            "change #province, #city": "changeArea",
            "change #county": "changeCounty"
        },
        reset: function(e) {
            switch (e) {
            case "province":
                this.$("#city").html(this.optionTemplate({
                    type:
                    "城市",
                    data: []
                }));
            case "city":
                this.$("#county").html(this.optionTemplate({
                    type:
                    "区县",
                    data: []
                }))
            }
            this.model.set("finalCode", 0)
        },
        changeCounty: function() {
            var e = this.$("#county option:selected").data("code");
            this.model.set("finalCode", e)
        },
        changeArea: function(e) {
            if (!this.loadingAddress) {
                var t = $(e.target),
                n = t.find("option:selected").data("code"),
                i = t.data("next-type"),
                s = t.attr("id"),
                o = t.data("next");
                this.reset(s),
                "0" !== n && this.loadList(o, i, 0, n)
            }
        },
        loadList: function(e, t, n, s, o) {
            this.loadingAddress = !0,
            o = o || i,
            this.model.getList({
                code: s,
                callback: _(function(i) {
                    this.loadingAddress = !1,
                    this.$("#" + e).html(this.optionTemplate({
                        data: i,
                        type: t,
                        selectedValue: n
                    })),
                    o(i)
                }).bind(this)
            })
        },
        render: function(e) {
            if (this.$el.append(t), this.defaultCode = e.dfcode, !this.defaultCode) return this.loadList("province", "省份", 0),
            this;
            var n = 1e4 * parseInt(this.defaultCode / 1e4),
            i = 100 * parseInt(this.defaultCode / 100);
            return this.loadList("province", "省份", n, 0, _(function() {
                this.loadList("city", "城市", i, n, _(function() {
                    this.loadList("county", "区县", this.defaultCode, i),
                    this.model.set("finalCode", this.defaultCode)
                }).bind(this))
            }).bind(this)),
            this
        }
    });
    return s
}),
define("wap/components/address/logistics_address_edit", ["wap/components/address/model/address", "wap/components/util/number", "text!wap/components/address/templates/addressForm.html", "wap/components/address/logistics_area"],
function(e, t, n, i) {
    return Backbone.View.extend({
        template: _.template(n),
        initialize: function(t) {
            var n = function() {},
            i = window.Utils.makeRandomString();
            $("body").append('<div id="' + i + '" class="modal order-modal"></div>'),
            this.nViewContainer = $("#" + i),
            this.ajaxType = t.type,
            this.model = t.model ? t.model: new e,
            this.doNotSavetoServer = t.doNotSavetoServer,
            this.cannotDelete = t.cannotDelete,
            this.areaModel = t.areaModel,
            this.onCancelAddAddress = _(function(e) {
                this.hide(),
                (t.onCancelAddAddress || n)(e),
                this.remove()
            }).bind(this),
            this.onFinishEditAddress = _(function(e) {
                this.hide(),
                (t.onFinishEditAddress || n)({
                    address_model: e
                }),
                this.remove()
            }).bind(this),
            this.autoPopup = t.autoPopup === !0,
            this.listenTo(this.model, "invalid", this.error)
        },
        events: {
            "click .js-address-cancel": "onCancelAddress",
            "click .js-address-save": "onSaveClicked",
            "click .js-address-delete": "onDeleteClicked"
        },
        render: function() {
            return this.$el.html(this.template({
                data: this.model.toJSON(),
                cannotDelete: this.cannotDelete
            })),
            this.nViewContainer.html(this.el),
            this.show(),
            this.areaBrainView = new i({
                model: this.areaModel,
                el: $(".js-area-select")
            }).render({
                dfcode: this.model.get("area_code")
            }),
            this
        },
        onSaveClicked: function() {
            var e = this,
            t = $("[name=id]", this.$el),
            n = this.model.attributes;
            if (_.each(n,
            function(t, n) {
                e.model.set(n, $("[name=" + n + "]", e.$el).val() || "")
            }), this.model.set("area_code", this.areaModel.get("finalCode")), this.model.set("id", t.length > 0 ? t.val() : ""), this.model.isValid()) {
                if (this.doNotSavetoServer) return void this.onFinishEditAddress(this.model);
                this.model.update(n, this.ajaxType).done(function(t) {
                    var n = JSON.parse(t);
                    0 == n.code ? (n.data.id !== !0 && 1 !== n.data.id && e.model.set("id", n.data.id), e.onFinishEditAddress(e.model)) : motify.log("更新收货地址失败")
                }).fail(function() {
                    motify.log("更新收货地址失败")
                })
            }
        },
        onCancelAddress: function() {
            confirm("确定要放弃此次编辑嘛？") && this.onCancelAddAddress({
                autoPopup: this.autoPopup
            })
        },
        onDeleteClicked: function(e) {
            e.stopPropagation();
            var t = this;
            if (confirm("确定要删除这个收获地址么？")) {
                var n = $("[name=id]", this.$el).val();
                this.model.destroy({
                    processData: !0,
                    data: {
                        id: n
                    },
                    wait: !0,
                    success: function(e, n) {
                        return n = n || {},
                        t.onFinishEditAddress(),
                        !0
                    },
                    error: function() {
                        motify.log("删除失败。。。")
                    }
                })
            }
        },
        error: function(e, t) {
            motify.log(t.msg),
            this.$el.find('input[name="' + t.name + '"]').focus()
        },
        show: function() {
            this.nViewContainer.addClass("active")
        },
        hide: function() {
            this.nViewContainer.removeClass("active")
        }
    })
}),
define("wap/components/list", [],
function() {
    var e = function() {},
    t = Backbone.View.extend({
        constructor: function(e) {
            e = e || {},
            this.options = e || {},
            this.options.itemOptions = this.options.itemOptions || {},
            this.setupListView(e),
            Backbone.View.call(this, e)
        },
        setupListView: function(t) {
            _.defaults(t, {
                itemView: Backbone.View,
                collection: new Backbone.Collection
            }),
            this.items = [],
            this.itemView = t.itemView,
            this.collection = t.collection,
            this.onAfterListChange = t.onAfterListChange || e,
            this.onAfterListLoad = t.onAfterListLoad || e,
            this.onEmptyList = t.onEmptyList || e,
            this.onItemClick = t.onItemClick || e,
            this.onViewItemAdded = t.onViewItemAdded || e,
            this.displaySize = t.displaySize || -1,
            this.emptyHTML = t.emptyHTML || "列表为空！"
        },
        _setupListeners: function() {
            this.listenTo(this.collection, "add", this.addSingleItem, this),
            this.listenTo(this.collection, "reset", this.addAll, this),
            this.listenTo(this.collection, "remove", this.onItemRemove, this),
            !!this.setupListeners && this.setupListeners()
        },
        render: function(e) {
            return this.displaySize = -1 == (e || {}).displaySize ? -1 : this.displaySize,
            this.addAll(),
            this
        },
        addAll: function() {
            this.removeAllItems(),
            this._setupListeners(),
            0 === this.collection.length ? this.onEmptyList() : this.collection.each(function(e) {
                this.addSingleItem(e)
            },
            this),
            this.onAfterListLoad({
                list: this.collection
            })
        },
        addSingleItem: function(e) {
            if (! (this.displaySize >= 0 && this.items.length >= this.displaySize)) {
                var t = new this.itemView(_.extend(this.options.itemOptions, {
                    model: e
                }));
                return this.items.push(t),
                this.addListItemListeners(t),
                t.render(),
                this.onViewItemAdded({
                    list: this.items,
                    viewItem: t
                }),
                (this.listEl || this.$el).append(t.el),
                t
            }
        },
        onItemRemove: function(e) {
            this.removeSingleItem(e),
            this.onAfterListChange()
        },
        removeSingleItem: function(e) {
            var t = this.getViewByModel(e);
            t && this.removeSingleView(t)
        },
        removeSingleView: function(e) {
            var t;
            this.stopListening(e),
            e && (this.stopListening(e.model), e.remove(), t = this.items.indexOf(e), this.items.splice(t, 1)),
            0 === this.collection.length && this.onEmptyList()
        },
        addListItemListeners: function(e) {
            var t = this;
            this.listenTo(e, "all",
            function() {
                var t = "item:" + arguments[0],
                n = _.toArray(arguments);
                n.splice(0, 1),
                n.unshift(t, e),
                this.trigger.apply(this, n),
                "item:click" == t && this.onItemClick()
            }),
            this.listenTo(e.model, "change",
            function() {
                t.onAfterListChange({
                    list: this.collection
                })
            })
        },
        getViewByModel: function(e) {
            return _.find(this.items,
            function(t) {
                return t.model === e
            })
        },
        getViewList: function() {
            return this.items
        },
        dispatchEventToAllView: function(e, t) {
            _.each(this.items,
            function(n) {
                n.trigger(e, t)
            })
        },
        removeAllItems: function() {
            this.collection.each(function(e) {
                this.removeSingleItem(e)
            },
            this),
            this.onAfterListChange({
                list: this.collection
            })
        },
        remove: function() {
            Backbone.View.prototype.remove.call(this, arguments),
            this.removeAllItems()
        },
        fetchRender: function() {
            return this.collection.fetch({
                data: {
                    count: this.options.count
                },
                success: _(function() {
                    this.render(),
                    !!this.onFetchSuccess && this.onFetchSuccess()
                }).bind(this)
            }),
            this
        }
    });
    return t
}),
define("text!wap/components/address/templates/addressItem.html", [],
function() {
    return '<div id="js-address-item-<%= data.id %>" class="js-address-item block-module <%= data.selected %>">\n    <h4>\n        <% if(typeof(data.name)!== \'undefined\'){ %>\n            <%= data.name %>, <%= data.tel %>\n        <% } else if(typeof(data.user_name)!== \'undefined\'){ %>\n            <%= data.user_name %>, <%= data.tel %>\n        <% } %>\n    </h4>\n    <span class="address-str address-str-sf"><%= data.province %><%= data.city %><%= data.county %><%= data.address_detail %></span>\n    <div class="address-opt <% if( data.address_type==\'express\'){ %> js-edit-address <% } %>">\n        <% if( data.address_type==\'express\'){ %>\n            <i class="icon_circle-info">i</i>\n        <% } %>\n    </div>\n</div>'
}),
define("wap/components/address/logistics_address_list", ["wap/components/address/model/address", "wap/components/address/logistics_address_edit", "wap/components/list", "text!wap/components/address/templates/addressItem.html"],
function(e, t, n, i) {
    var s = Backbone.View.extend({
        initialize: function(e) {
            this.addressType = e.addressType || "self-fetch",
            this.template = _.template(e.itemTemplateHtml || ""),
            this.onItemClick = e.onItemClick ||
            function() {},
            this.highLightItem = e.highLightItem ||
            function() {},
            this.areaModel = e.areaModel
        },
        events: {
            click: "onClick",
            "click .js-edit-address": "onEditClicked"
        },
        onClick: function(e) {
            var t = $(e.target);
            t.hasClass("js-edit-address") || t.hasClass("icon_circle-info") || this.onItemClick({
                address: this.model.toJSON(),
                addressType: this.addressType
            })
        },
        onEditClicked: function() {
            var e = function() {
                this.render(),
                this.highLightItem()
            };
            return function() {
                this.addressEditView = new t({
                    onFinishEditAddress: _(e).bind(this),
                    model: this.model,
                    type: "PUT",
                    areaModel: this.areaModel
                }),
                this.addressEditView.render()
            }
        } (),
        render: function() {
            return this.$el.html(this.template({
                data: _.extend(this.model.toJSON(), {
                    address_type: this.addressType
                })
            })),
            this
        }
    });
    return Backbone.View.extend({
        initialize: function(e) {
            var t = window.Utils.makeRandomString();
            $("body").append('<div id="' + t + '" class="modal order-modal"></div>'),
            this.$el = $("#" + t),
            this.template = _.template(e.templateHtml || ""),
            this.addressType = e.addressType,
            this.autoPopup = e.autoPopup,
            this.selectedAddress = e.selectedAddress,
            this.areaModel = e.areaModel || {},
            this.onAddressChange = e.onAddressChange ||
            function() {},
            this.onCancel = e.onCancel ||
            function() {},
            e.collection.comparator = function(e, t) {
                return t.id - e.id
            },
            this.listOpt = {
                itemView: s,
                finishedHTML: " ",
                collection: e.collection,
                itemOptions: {
                    addressType: e.addressType,
                    itemTemplateHtml: i,
                    onItemClick: _(this.onItemClick).bind(this),
                    highLightItem: _(this.highLight).bind(this),
                    areaModel: this.areaModel
                },
                onAfterListLoad: _(this.onAfterListLoad).bind(this),
                onAfterListChange: _(this.onAfterListChange).bind(this)
            }
        },
        events: {
            "click .js-cancel": "onCancelClicked",
            "click .js-add-address": "onAddAddressClicked"
        },
        onAfterListChange: function(e) {
            this.onAfterListLoad(e)
        },
        onAfterListLoad: function() {
            this.highLight(this.selectedAddress)
        },
        onItemClick: function(e) {
            this.hide(),
            this.onAddressChange(e),
            this.selectedAddress = e.address
        },
        onCancelClicked: function() {
            this.onCancel(this.addressType),
            this.hide()
        },
        onAddAddressClicked: function() {
            var n = function(e) {
                e && (this.collection.add(e.address_model), this.render(), this.show())
            },
            i = function() {
                0 === this.listOpt.collection.length ? this.hide() : this._doShow()
            };
            return function(s) {
                s = s || {},
                this.addressEditView = new t({
                    onFinishEditAddress: _(n).bind(this),
                    onCancelAddAddress: _(i).bind(this),
                    type: "POST",
                    autoPopup: (s || {}).autoPopup,
                    model: s.selectedAddress ? new e(s.selectedAddress) : null,
                    areaModel: this.areaModel,
                    cannotDelete: s.cannotDelete
                }),
                this.addressEditView.render()
            }
        } (),
        render: function() {
            return this.$el.html(this.template({
                address_type: this.addressType
            })),
            this.listOpt.el = this.$(".js-address-container"),
            this.addressListView = new n(this.listOpt),
            this.listOpt.collection.length > 0 ? this.addressListView.render() : this.addressListView.fetchRender(),
            this
        },
        show: function(e) {
            e = e || {},
            this._doShow(e.selectedAddress),
            "express" == this.addressType && 0 === this.listOpt.collection.length && this.onAddAddressClicked(_.extend(e, {
                cannotDelete: !0
            }))
        },
        highLight: function(e) {
            var t = e || this.selectedAddress || {};
            this.$(".address-selected").removeClass("address-selected"),
            this.$("#js-address-item-" + t.id).addClass("address-selected")
        },
        _doShow: function(e) {
            this.$el.addClass("active"),
            this.highLight(e),
            Backbone.EventCenter.trigger("address_list:show")
        },
        hide: function() {
            this.$el.removeClass("active"),
            Backbone.EventCenter.trigger("address_list:hide")
        }
    })
}),
window.Utils = window.Utils || {},
$.extend(window.Utils, {
    getRecentlyUsedAddress: function() {
        var e = null;
        if (window.localStorage) {
            var t = $.trim(window.localStorage.getItem("recently_used_address"));
            if ( - 1 !== t.indexOf("undefined") && (window.localStorage.removeItem("recently_used_address"), t = ""), t) try {
                e = $.parseJSON(t)
            } catch(n) {}
        }
        return e
    },
    saveLastUsedAddress: function(e) {
        if (!e || !e.user_name) return ! 1;
        var t = JSON.stringify(e);
        if (window.localStorage) try {
            window.localStorage.setItem("recently_used_address", t)
        } catch(n) {}
    },
    wx2KdtAddress: function(e) {
        if (!e) return null;
        if (!e.userName && e.user_name) return e;
        var t = {
            user_name: e.userName,
            province: e.proviceFirstStageName,
            city: e.addressCitySecondStageName,
            county: e.addressCountiesThirdStageName,
            address_detail: e.addressDetailInfo,
            tel: e.telNumber,
            postal_code: e.addressPostalCode,
            is_weixin: !0
        };
        return t
    },
    kdt2WxAddress: function(e) {
        if (!e) return null;
        if (!e.user_name && e.userName) return e;
        var t = {
            userName: e.user_name,
            proviceFirstStageName: e.province,
            addressCitySecondStageName: e.city,
            addressCountiesThirdStageName: e.county,
            addressDetailInfo: e.address_detail,
            telNumber: e.tel,
            addressPostalCode: e.postal_code
        };
        return t
    },
    getPureAddressData: function(e) {
        if (!e || !e.userName) return ! 1;
        var t = {
            userName: e.userName,
            proviceFirstStageName: e.proviceFirstStageName,
            addressCitySecondStageName: e.addressCitySecondStageName,
            addressCountiesThirdStageName: e.addressCountiesThirdStageName,
            addressDetailInfo: e.addressDetailInfo,
            telNumber: e.telNumber,
            addressPostalCode: e.addressPostalCode,
            area_code: e.nationalCode
        };
        return t
    },
    getPureKdtAddressData: function(e) {
        return e && e.userName ? {
            user_name: e.userName,
            province: e.proviceFirstStageName,
            city: e.addressCitySecondStageName,
            county: e.addressCountiesThirdStageName,
            address_detail: e.addressDetailInfo,
            tel: e.telNumber,
            postal_code: e.addressPostalCode,
            area_code: e.nationalCode
        }: !1
    },
    flashAnimate: function(e) {
        e.addClass("animated flashWarn"),
        window.setTimeout(function() {
            e.removeClass("flashWarn")
        },
        1e3)
    },
    getTpl: function(e) {
        var t = $(e);
        return t ? _.template(t.html()) : void 0
    }
}),
define("wap/components/util/address",
function() {}),
define("text!wap/components/address/templates/addressPanel.html", [],
function() {
    return '<div class="block block-form block-border-top-none block-border-bottom-none">\n    <div class="js-order-address block-item-express-panel" style="padding-left:0;">\n        <div class="opt-wrapper"><a href="javascript:;" class="btn btn-xxsmall btn-grayeee butn-edit-address js-butn-edit-address">修改</a></div>\n        <ul >\n            <li><span>\n                <% if(typeof(data.address.name)!== \'undefined\'){ %>\n                    <%= data.address.name %>\n                <% } else if(typeof(data.address.user_name)!== \'undefined\'){ %>\n                    <%= data.address.user_name %>\n                <% } %>\n            </span>, <%= data.address.tel %></li>\n            <li><%= data.address.province %> <%= data.address.city %> <%= data.address.county %> </li>\n            <li><%= data.address.address_detail %></li>\n        </ul>\n    </div>\n\n    <% if(data.address_type==\'self-fetch\'){ %>\n        <div class="clearfix block-item  <% if( data.order_state > 2) { %> self-fetch-info-show <% } %>">\n            <label>预约人：</label>\n            <input  class="txt txt-black ellipsis js-name" placeholder="到店人姓名" value="<%= data.address.user_name %>" <% if( data.order_state > 2) { %> readonly="readonly"<% } %>></input>\n        </div>\n        <div class="clearfix block-item  <% if( data.order_state > 2) { %> self-fetch-info-show <% } %>">\n            <label>联系方式：</label>\n            <input  type=\'number\' class="txt txt-black ellipsis js-phone" placeholder="用于短信接收和便于卖家联系" value="<%= data.address.user_tel %>" <% if( data.order_state > 2) { %> readonly="readonly"<% } %>></input>\n        </div>\n        <div class="clearfix block-item  <% if( data.order_state > 2) { %> self-fetch-info-show <% } %>">\n            <label class="pull-left">预约时间：</label>\n            <input style="width:105px" class="txt txt-black js-time pull-left date-time" type="date" placeholder="日期" value="<%= data.address.user_time.split(\' \')[0] || \'\' %>"  <% if( data.order_state > 2) { %> readonly="readonly"<% } %>/>\n            <input style="width:70px" class="txt txt-black js-time pull-left date-time" type="time" placeholder="时间" value="<%= data.address.user_time.split(\' \')[1] || \'\' %>"  <% if( data.order_state > 2) { %> readonly="readonly"<% } %>/>\n        </div>\n    <% } %>\n</div>\n <% if( data.address_type == \'express\' ){ %>\n    <div class=\'js-logistics-tips logistics-tips font-size-12 c-orangef60 hide\'>很抱歉，该地区暂不支持配送。</div>\n<% } %>\n'
}),
define("text!wap/components/address/templates/noAddressPanel.html", [],
function() {
    return '<div class="js-order-address block-item-express-panel">\n    <div class="js-butn-edit-address address-tip"><span>添加收货地址</span></div>\n</div>'
}),
define("text!wap/components/address/templates/addressList.html", [],
function() {
    return '<div class="js-scene-address-list <% if(address_type==\'express\'){ %> scene <% } %>">\n    <div class="address-ui address-list">\n        <% if(address_type==\'express\'){ %>\n            <h4 class="list-title text-right">            \n                <a class="js-add-address add-address" href="javascript:;">新增收货地址</a>           \n                <a class="js-cancel cancel" href="javascript:;">返回</a>\n            </h4>\n        <% } %>\n        <div class="block">\n            <div class="js-address-container address-container"> </div>\n        </div>\n        <% if(address_type!=\'express\'){ %>\n            <div class=\'action-container\'>\n                <button type="button" class="js-cancel btn btn-block">返回</button>\n            </div>\n        <% } %>\n    </div>\n</div>'
}),
define("wap/components/address/logistics_express", ["wap/components/address/model/address", "wap/components/address/model/express_address_collection", "wap/components/address/logistics_address_edit", "wap/components/address/logistics_address_list", "wap/components/util/address", "text!wap/components/address/templates/addressPanel.html", "text!wap/components/address/templates/noAddressPanel.html", "text!wap/components/address/templates/addressList.html"],
function(e, t, n, i, s, o, a, r) {
    var d = function() {};
    return Backbone.View.extend({
        template: _.template(o),
        initialize: function(e) {
            e = e || {},
            this.address = window._global.expressAddress,
            this.areaModel = e.areaModel,
            this.onAddressChanged = e.onAddressChanged || d,
            this.not_saveToSever = e.not_saveToSever,
            this.order_state = window._global.order_state,
            this.hasAddress() && 0 === window._global.expressType ? this.doNotSaveToServerNextTime = !0 : this.address = Utils.getRecentlyUsedAddress()
        },
        events: {
            "click .js-butn-edit-address": "onChangeAddressClicked"
        },
        onChangeAddressClicked: function(s) {
            return this.order_state >= 3 ? void 0 : window._global.no_user_login ? void(this.addressEditView = new n({
                onFinishEditAddress: _(this.onFinishEditAddress).bind(this),
                model: this.address ? new e(this.address) : null,
                doNotSavetoServer: !0,
                cannotDelete: !0,
                areaModel: this.areaModel
            }).render()) : void(this.addressListView ? this.addressListView.show(_.extend({
                selectedAddress: this.address
            },
            s)) : (this.addressListView = new i({
                templateHtml: r,
                addressType: "express",
                collection: new t(window._global.address_list || []),
                onAddressChange: _(this.onAddressChange).bind(this),
                autoPopup: (s || {}).autoPopup,
                selectedAddress: this.address,
                areaModel: this.areaModel
            }).render(), this.addressListView.show(_.extend({
                selectedAddress: this.address
            },
            s))))
        },
        onFinishEditAddress: function(e) {
            this.address = e.address_model.toJSON(),
            this.saveAddressToLocal(this.address),
            this.render()
        },
        saveAddressToLocal: function(e) {
            Utils.saveLastUsedAddress(e)
        },
        onAddressChange: function(e) {
            this.address = e.address,
            this.saveAddressToServer && !this.not_saveToSever ? this.saveAddressToServer(this.address, "express") : this.$el.html(this.template({
                data: {
                    address: this.address,
                    address_type: "express"
                }
            })),
            this.saveAddressToLocal(this.address)
        },
        hasAddress: function() {
            return !! this.address && !!this.address.user_name
        },
        getAddress: function() {
            return this.address
        },
        render: function(e) {
            return this.show(e),
            this
        },
        show: function(e) {
            if (e = e || {},
            e.order_state && (this.order_state = e.order_state), this.$el.removeClass("hide"), this.hasAddress()) this.$el.html(this.template({
                data: {
                    address: this.address,
                    address_type: "express"
                }
            })),
            e.not_saveToSever || this.not_saveToSever || this.saveAddressToServer && this.saveAddressToServer(this.address, "express");
            else {
                var t = _.template(a);
                if (this.$el.html(t()), "self-fetch" === e.from) return;
                this.onChangeAddressClicked({
                    autoPopup: !0
                })
            }
            this.order_state >= 3 && this.$(".js-butn-edit-address").hide()
        },
        showTips: function() {
            $(".js-logistics-tips").removeClass("hide"),
            $(".js-express").addClass("no-border-bottom")
        },
        hideTips: function() {
            $(".js-logistics-tips").addClass("hide"),
            $(".js-express").removeClass("no-border-bottom")
        }
    })
}),
define("wap/trade/confirm/view/logistics_express_kdt", ["wap/components/address/logistics_express"],
function(e) {
    return e.extend({
        saveAddressToServer: function(e, t) {
            var n = this;
            return n.cannotDeliver = !1,
            n.hideTips(),
            Backbone.EventCenter.trigger("address:hidePaymentArea"),
            this.doNotSaveToServerNextTime ? (this.doNotSaveToServerNextTime = !1, Backbone.EventCenter.trigger("address:has"), void this.onAddressChanged({
                logisticsWay: "express",
                isResetPriceData: !1
            })) : (n.sendingToServer = !0, void $.ajax({
                url: window._global.url.trade + "/trade/order/address.json",
                type: "POST",
                dataType: "json",
                timeout: 15e3,
                data: _.extend({
                    is_weixin: !1
                },
                e, {
                    order_id: window._global.order_id
                }),
                success: function(i) {
                    var s = i.code,
                    o = i.data;
                    0 === s ? (n.$el.html(n.template({
                        data: {
                            address: e,
                            address_type: t
                        }
                    })), Backbone.EventCenter.trigger("address:change"), n.onAddressChanged({
                        logisticsWay: "express",
                        isResetPriceData: !0,
                        data: o
                    })) : 10600 === s ? (n.$el.html(n.template({
                        data: {
                            address: e,
                            address_type: t
                        }
                    })), n.showTips(), n.cannotDeliver = !0) : motify.log(i.msg)
                },
                error: function() {
                    motify.log("更新收货地址失败")
                },
                complete: function() {
                    n.sendingToServer = !1
                }
            }))
        }
    })
}),
define("wap/trade/confirm/view/logistics_express_wx", ["wap/trade/confirm/view/logistics_express_kdt", "wap/components/util/address"],
function(e) {
    return e.extend({
        onChangeAddressClicked: function() { ! wxReady || window._global.order_state >= 3 || wxReady && wxReady(_(function() {
                window.WeixinJSBridge && window.WeixinJSBridge.invoke("editAddress", window._global.address_token, _(function(e) {
                    var t = e.err_msg;
                    if ("edit_address:ok" == t) {
                        var n = Utils.getPureKdtAddressData(e);
                        n ? (this.address = n, this.saveAddressToServer(this.address, "express"), this.saveAddressToLocal(this.address)) : motify.log("地址数据错误")
                    } else if ("access_control:not_allow" === t) return void this.trigger("wx-address:error")
                }).bind(this))
            }).bind(this))
        }
    })
}),
define("wap/components/address/config", [],
function() {
    var e = window._global;
    return {
        permissions: {
            wxAddress: e.wxaddress_env,
            wxPay: e.wxpay_env,
            aliPay: e.alipay_env
        }
    }
}),
define("wap/components/address/logistics_express_wx", ["wap/components/address/logistics_express", "wap/components/util/address"],
function(e) {
    return e.extend({
        onChangeAddressClicked: function() { ! wxReady || window._global.order_state >= 3 || wxReady && wxReady(_(function() {
                window.WeixinJSBridge && window.WeixinJSBridge.invoke("editAddress", window._global.address_token, _(function(e) {
                    var t = e.err_msg;
                    if ("edit_address:ok" == t) {
                        var n = Utils.getPureKdtAddressData(e);
                        n ? this.onAddressChange({
                            address: n
                        }) : motify.log("地址数据错误")
                    }
                }).bind(this))
            }).bind(this))
        }
    })
}),
define("wap/components/address/model/selffetch_address_collection", ["wap/components/address/model/address"],
function(e) {
    return Backbone.Collection.extend({
        model: e,
        url: window._global.url.trade + "/trade/selffetch/list.json",
        parse: function(e) {
            var t = (e || {}).data || {};
            this.total = parseInt(t.total);
            var n = (t || {}).list || [];
            return this.total >= 0 || (this.total = n.length),
            n
        }
    })
}),
define("wap/components/address/logistics_selffetch", ["wap/components/address/model/selffetch_address_collection", "wap/components/address/logistics_address_list", "text!wap/components/address/templates/addressPanel.html", "text!wap/components/address/templates/addressList.html"],
function(e, t, n, i) {
    return Backbone.View.extend({
        template: _.template(n),
        initialize: function(e) {
            e = e || {},
            this.address = window._global.selffetchAddress,
            this.order_state = window._global.order_state,
            this.not_saveToSever = e.not_saveToSever
        },
        events: {
            "click .js-butn-edit-address": "onEditSelfFetchClicked"
        },
        onEditSelfFetchClicked: function() {
            this.selfFetchAddressListView ? this.selfFetchAddressListView.show({
                selectedAddress: this.address
            }) : (this.selfFetchAddressListView = new t({
                el: $("#js-self-fetch-modal"),
                templateHtml: i,
                addressType: "self-fetch",
                collection: new e,
                onAddressChange: _(this.onAddressChange).bind(this),
                onCancel: _(this.onCancelAddressSelect).bind(this),
                selectedAddress: this.address
            }).render(), this.selfFetchAddressListView.show({
                selectedAddress: this.address
            }))
        },
        onAddressChange: function() {
            var e = function() {
                var e = new Date;
                return e.setDate(e.getDate() + 1),
                e.setMinutes(e.getMinutes() - e.getTimezoneOffset()),
                e.toJSON().slice(0, 10) + " " + e.toJSON().slice(11, 16)
            };
            return function(t) {
                t = t || {},
                this.address = t.address || {},
                this.address.user_time = this.address.user_time || e(),
                this.$el.html(this.template({
                    data: {
                        address: this.address,
                        address_type: "self-fetch",
                        order_state: this.order_state
                    }
                })),
                t.not_saveToSever || this.not_saveToSever || $.ajax({
                    url: window._global.url.trade + "/trade/selffetch/order.json",
                    type: "PUT",
                    dataType: "json",
                    timeout: 5e3,
                    data: {
                        order_no: window._global.order_no,
                        express_type: t.address.id,
                        address: this.address
                    },
                    beforeSend: function() {},
                    success: function() {
                        Backbone.EventCenter.trigger("address:change")
                    },
                    error: function(e, t, n) {},
                    complete: function(e, t) {}
                })
            }
        } (),
        onCancelAddressSelect: function() {
            return this.address && 0 !== this.address.length ? void 0 : (this.trigger("no-address:cancel", {
                addressType: "self-fetch",
                from: "self-fetch"
            }), this)
        },
        render: function() {
            return this.show(),
            this
        },
        show: function(e) {
            return e = e || {},
            e.order_state && (this.order_state = e.order_state, this.address.user_time = this.address.user_time || e.user_time, this.address.user_tel = this.address.user_tel || e.user_tel, this.address.user_name = this.address.user_name || e.user_name),
            this.$el.removeClass("hide"),
            this.address && 0 !== this.address.length ? (this.onAddressChange({
                address: this.address,
                not_saveToSever: e.not_saveToSever
            }), void(this.order_state >= 3 && this.$(".js-butn-edit-address").hide())) : (this.onEditSelfFetchClicked(), this)
        }
    })
}),
define("wap/components/tabber", ["require"],
function() {
    var e = function() {},
    t = Backbone.View.extend({
        initialize: function(t) {
            this.activeKey = t.activeKey || "type",
            this.activeClass = t.activeClass || "active",
            this.cbOnClicked = t.onClicked || e,
            this.cbOnDisabledClicked = t.onDisabledClicked || e,
            this.initDefault(t)
        },
        events: {
            click: "onClicked"
        },
        initDefault: function(e) {
            e.defaultData && e.defaultData.length > 0 && this.active(e.defaultData)
        },
        onClicked: function(e) {
            var t = $(e.target);
            t.blur();
            var n = t.data(this.activeKey);
            if (this.activeData !== n && n && n.length > 0) {
                if (this.disabled === !0) return void(this.activeData !== n && this.cbOnDisabledClicked());
                this.$("." + this.activeClass).removeClass(this.activeClass),
                t.addClass(this.activeClass),
                this.activeData = n,
                this.cbOnClicked(_.extend(e || {},
                {
                    value: n,
                    nTarget: t
                }))
            }
        },
        active: function(e, t) {
            this.onClicked(_.extend({
                target: this.$el.find("[data-" + this.activeKey + '="' + e + '"]:first')
            },
            t || {}))
        },
        setDisable: function(e) {
            this.disabled = e
        },
        setData: function(e) {
            this.activeData = e,
            this.$("." + this.activeClass).removeClass(this.activeClass),
            this.$el.find("[data-" + this.activeKey + '="' + this.activeData + '"]:first').addClass(this.activeClass)
        },
        getData: function() {
            return this.activeData
        }
    });
    return t
}),
define("wap/components/address/model/area", ["components/regions_data/main"],
function(e) {
    var t = function() {},
    n = Backbone.Model.extend({
        defaults: {
            finalCode: 0
        },
        initialize: function() {
            return e.preload("pc"),
            this
        },
        getList: function(n) {
            n = n || {};
            var i = n.callback || t,
            s = n.code;
            e.loadList(s).then(_(function(e) {
                i(e)
            }).bind(this))
        }
    });
    return n
}),
define("wap/components/address/logistics_selector", ["wap/components/address/config", "wap/components/address/logistics_express", "wap/components/address/logistics_express_wx", "wap/components/address/logistics_selffetch", "wap/components/tabber", "wap/components/address/model/area"],
function(e, t, n, i, s, o) {
    return Backbone.View.extend({
        initialize: function(i) {
            this.areaModel = new o,
            i = i || {},
            this.onAddressChanged = i.onAddressChanged ||
            function() {},
            this.onWxAddressFailed = i.onWxAddressFailed ||
            function() {},
            this.not_saveToSever = i.not_saveToSever,
            this.defaultLogistics = window._global.expressType > 0 ? "self-fetch": "express";
            var a = i.logisticsExpressKdtView || t,
            r = i.logisticsExpressWxView || n,
            d = window.navigator.userAgent,
            l = d.match(/MicroMessenger\/(\d+(\.\d+)*)/),
            c = null !== l && l.length,
            u = c ? parseFloat(l[1]) : 0;
            this.LogisticsExpressView = c ? 5 > u ? a: e.permissions.wxAddress ? r: a: a,
            this.logisticsExpressKdtView = a,
            this.logisticsViews = {
                express: null,
                "self-fetch": null
            },
            this.tabberContainer = this.$(".js-logistics-select"),
            window._global.allow_self_fetch ? this.tabberContainer.find(".js-tabber-self-fetch").removeClass("hide") : this.tabberContainer.parent().addClass("hide"),
            this.tabber = new s({
                el: this.tabberContainer,
                activeClass: "tag-orange",
                activeKey: "type",
                defaultData: this.defaultLogistics,
                onClicked: _(this.onTabberClicked).bind(this),
                onDisabledClicked: _(this.onDisabledTabberClicked).bind(this)
            }),
            this.tabber.setDisable(window._global.order_state > 2)
        },
        onTabberClicked: function(e) {
            Backbone.EventCenter.trigger("address:switchAddressType");
            var t = (this.logisticsViews.express, e.value);
            if (this.logisticsWay = t, this.$(".js-logistics-content").addClass("hide"), this.logisticsViews[t]) this.logisticsViews[t].show(e),
            this.$(".js-" + t).removeClass("hide");
            else {
                var n = "express" == t ? this.LogisticsExpressView: i,
                s = new n({
                    areaModel: this.areaModel,
                    onAddressChanged: this.onAddressChanged,
                    not_saveToSever: this.not_saveToSever
                });
                this.listenTo(s, "no-address:cancel", this.switch2Other),
                this.listenTo(s, "wx-address:error", _(this.onWxAddressError).bind(this)),
                this.logisticsViews[t] = s,
                this.$(".js-" + t).html(s.render().el).removeClass("hide")
            }
            this.onAddressChanged({
                logisticsWay: t
            })
        },
        switch2Other: function(e) {
            var t = "express" == e.addressType ? "self-fetch": "express";
            this.tabber.active(t, e)
        },
        onDisabledTabberClicked: function() {
            motify.log("您不能再修改配送方式")
        },
        onPayBtnClicked: function(e) {
            this.$(".js-butn-edit-address").hide();
            var t = window._global.order_state > 2 ? window._global.order_state: 3,
            n = _.extend(e, {
                order_state: t,
                not_saveToSever: !0
            });
            this.tabber.setDisable(!0),
            this.logisticsViews[this.logisticsWay].show(n)
        },
        getAddress: function() {
            return this.logisticsViews[this.logisticsWay].getAddress()
        },
        onWxAddressError: function() {
            this.logisticsViews.express && this.logisticsViews.express.remove();
            var e = new this.logisticsExpressKdtView({
                areaModel: this.areaModel
            });
            this.listenTo(e, "no-address:cancel", this.switch2Other),
            this.logisticsViews.express = e,
            this.$(".js-express").html(e.render().el).removeClass("hide"),
            this.logisticsViews.express.onChangeAddressClicked(),
            this.onWxAddressFailed()
        }
    })
}),
function(e, t) {
    var n = e.Wreqr,
    i = e.Wreqr = {};
    return e.Wreqr.VERSION = "1.3.1",
    e.Wreqr.noConflict = function() {
        return e.Wreqr = n,
        this
    },
    i.Handlers = function(e, t) {
        var n = function(e) {
            this.options = e,
            this._wreqrHandlers = {},
            t.isFunction(this.initialize) && this.initialize(e)
        };
        return n.extend = e.Model.extend,
        t.extend(n.prototype, e.Events, {
            setHandlers: function(e) {
                t.each(e,
                function(e, n) {
                    var i = null;
                    t.isObject(e) && !t.isFunction(e) && (i = e.context, e = e.callback),
                    this.setHandler(n, e, i)
                },
                this)
            },
            setHandler: function(e, t, n) {
                var i = {
                    callback: t,
                    context: n
                };
                this._wreqrHandlers[e] = i,
                this.trigger("handler:add", e, t, n)
            },
            hasHandler: function(e) {
                return !! this._wreqrHandlers[e]
            },
            getHandler: function(e) {
                var t = this._wreqrHandlers[e];
                if (t) return function() {
                    var e = Array.prototype.slice.apply(arguments);
                    return t.callback.apply(t.context, e)
                }
            },
            removeHandler: function(e) {
                delete this._wreqrHandlers[e]
            },
            removeAllHandlers: function() {
                this._wreqrHandlers = {}
            }
        }),
        n
    } (e, t),
    i.CommandStorage = function() {
        var n = function(e) {
            this.options = e,
            this._commands = {},
            t.isFunction(this.initialize) && this.initialize(e)
        };
        return t.extend(n.prototype, e.Events, {
            getCommands: function(e) {
                var t = this._commands[e];
                return t || (t = {
                    command: e,
                    instances: []
                },
                this._commands[e] = t),
                t
            },
            addCommand: function(e, t) {
                var n = this.getCommands(e);
                n.instances.push(t)
            },
            clearCommands: function(e) {
                var t = this.getCommands(e);
                t.instances = []
            }
        }),
        n
    } (),
    i.Commands = function(e) {
        return e.Handlers.extend({
            storageType: e.CommandStorage,
            constructor: function(t) {
                this.options = t || {},
                this._initializeStorage(this.options),
                this.on("handler:add", this._executeCommands, this);
                var n = Array.prototype.slice.call(arguments);
                e.Handlers.prototype.constructor.apply(this, n)
            },
            execute: function(e, t) {
                e = arguments[0],
                t = Array.prototype.slice.call(arguments, 1),
                this.hasHandler(e) ? this.getHandler(e).apply(this, t) : this.storage.addCommand(e, t)
            },
            _executeCommands: function(e, n, i) {
                var s = this.storage.getCommands(e);
                t.each(s.instances,
                function(e) {
                    n.apply(i, e)
                }),
                this.storage.clearCommands(e)
            },
            _initializeStorage: function(e) {
                var n, i = e.storageType || this.storageType;
                n = t.isFunction(i) ? new i: i,
                this.storage = n
            }
        })
    } (i),
    i.RequestResponse = function(e) {
        return e.Handlers.extend({
            request: function() {
                var e = arguments[0],
                t = Array.prototype.slice.call(arguments, 1);
                return this.hasHandler(e) ? this.getHandler(e).apply(this, t) : void 0
            }
        })
    } (i),
    i.EventAggregator = function(e, t) {
        var n = function() {};
        return n.extend = e.Model.extend,
        t.extend(n.prototype, e.Events),
        n
    } (e, t),
    i.Channel = function() {
        var n = function(t) {
            this.vent = new e.Wreqr.EventAggregator,
            this.reqres = new e.Wreqr.RequestResponse,
            this.commands = new e.Wreqr.Commands,
            this.channelName = t
        };
        return t.extend(n.prototype, {
            reset: function() {
                return this.vent.off(),
                this.vent.stopListening(),
                this.reqres.removeAllHandlers(),
                this.commands.removeAllHandlers(),
                this
            },
            connectEvents: function(e, t) {
                return this._connect("vent", e, t),
                this
            },
            connectCommands: function(e, t) {
                return this._connect("commands", e, t),
                this
            },
            connectRequests: function(e, t) {
                return this._connect("reqres", e, t),
                this
            },
            _connect: function(e, n, i) {
                if (n) {
                    i = i || this;
                    var s = "vent" === e ? "on": "setHandler";
                    t.each(n,
                    function(n, o) {
                        this[e][s](o, t.bind(n, i))
                    },
                    this)
                }
            }
        }),
        n
    } (i),
    i.radio = function(e) {
        var n = function() {
            this._channels = {},
            this.vent = {},
            this.commands = {},
            this.reqres = {},
            this._proxyMethods()
        };
        t.extend(n.prototype, {
            channel: function(e) {
                if (!e) throw new Error("Channel must receive a name");
                return this._getChannel(e)
            },
            _getChannel: function(t) {
                var n = this._channels[t];
                return n || (n = new e.Channel(t), this._channels[t] = n),
                n
            },
            _proxyMethods: function() {
                t.each(["vent", "commands", "reqres"],
                function(e) {
                    t.each(i[e],
                    function(t) {
                        this[e][t] = s(this, e, t)
                    },
                    this)
                },
                this)
            }
        });
        var i = {
            vent: ["on", "off", "trigger", "once", "stopListening", "listenTo", "listenToOnce"],
            commands: ["execute", "setHandler", "setHandlers", "removeHandler", "removeAllHandlers"],
            reqres: ["request", "setHandler", "setHandlers", "removeHandler", "removeAllHandlers"]
        },
        s = function(e, t, n) {
            return function(i) {
                var s = e._getChannel(i)[t],
                o = Array.prototype.slice.call(arguments, 1);
                return s[n].apply(s, o)
            }
        };
        return new n
    } (i),
    e.Wreqr
} (Backbone, _),
define("vendor/backbone_wreqr",
function() {}),
define("wap/components/pop_page", [],
function() {
    var e = Backbone.View.extend({
        initialize: function(e) {
            this.contentViewClass = e.contentViewClass,
            this.contentViewOptions = e.contentViewOptions,
            this.nPageContents = e.nPageContents
        },
        render: function() {
            return this.contentView = new this.contentViewClass(_.extend({
                onHide: _(this.hide).bind(this),
                el: this.$el
            },
            this.contentViewOptions)).render(),
            this
        },
        show: function() {
            _.each(this.nPageContents,
            function(e) {
                e.hide()
            }),
            this.$el.show(),
            window.scrollTo(0, 0)
        },
        hide: function() {
            this.$el.hide(),
            _.each(this.nPageContents,
            function(e) {
                e.show()
            })
        },
        destroy: function() {
            this.contentView && this.contentView.remove()
        }
    });
    return e
}),
window.Zepto &&
function(e) {
    e.fn.serializeArray = function() {
        var t, n, i = [];
        return e([].slice.call(this.get(0).elements)).each(function() {
            t = e(this),
            n = t.attr("type"),
            "fieldset" != this.nodeName.toLowerCase() && !this.disabled && "submit" != n && "reset" != n && "button" != n && ("radio" != n && "checkbox" != n || this.checked) && i.push({
                name: t.attr("name"),
                value: t.val()
            })
        }),
        i
    },
    e.fn.serialize = function() {
        var e = [];
        return this.serializeArray().forEach(function(t) {
            e.push(encodeURIComponent(t.name) + "=" + encodeURIComponent(t.value))
        }),
        e.join("&")
    },
    e.fn.submit = function(t) {
        if (t) this.bind("submit", t);
        else if (this.length) {
            var n = e.Event("submit");
            this.eq(0).trigger(n),
            n.isDefaultPrevented() || this.get(0).submit()
        }
        return this
    }
} (Zepto),
define("vendor/zepto/form",
function() {}),
window.Utils = window.Utils || {},
$.extend(window.Utils, {
    needConfirm: function(e, t, n) {
        var i = window.confirm(e);
        i ? t && "function" == typeof t && t.apply() : n && "function" == typeof n && n.apply()
    }
}),
define("wap/components/util/confirm",
function() {}),
define("wap/components/pay", ["wap/components/list", "vendor/zepto/form", "wap/components/util/confirm"],
function(e) {
    var t = Backbone.View.extend({
        template: _.template('<button type="button" data-pay-type="<%= data.code %>" class="btn btn-block btn-large <%= getBtnClass(data.code) %>"><%= data.name %></button>'),
        initialize: function(e) {
            this.onOtherPayClicked = e.onOtherPayClicked ||
            function() {},
            this.payUrl = e.payUrl,
            this.orderId = e.orderId,
            this.wxPayResultUrl = e.wxPayResultUrl,
            this.getPayDataExtr = e.getPayDataExtr,
            this.onPayBtnClicked = e.onPayBtnClicked
        },
        events: {
            "click button": "onButtonClick"
        },
        onButtonClick: function(e) {
            var t = $(e.target),
            n = t.data("pay-type");
            return "other" === n ? void this.onOtherPayClicked() : "codpay" == n ? void((Utils || {}).needConfirm && Utils.needConfirm("下单提醒：您正在选择货到付款，下单后由商家发货，快递送货上门并收款。", _(function() {
                this.doPay(n)
            }).bind(this))) : void this.doPay(n)
        },
        doPay: function(e) {
            var t = this.getPayDataExtr();
            if (t) {
                var n = {
                    order_id: this.orderId,
                    buy_way: e
                };
                this.submitPay(_.extend(t, n), e)
            }
        },
        submitPay: function(e, t) {
            if ("aliwap" == t) {
                var n = Number($(".js-real-pay-temp").data("real-pay"));
                if (n > 1999) return void motify.log("支付宝单笔支付最多支付1999元", 0)
            }
            this._submitPay(e, t)
        },
        _submitPay: function(e, t) {
            var n = this;
            $.ajax({
                url: this.payUrl,
                type: "POST",
                dataType: "json",
                timeout: 15e3,
                data: e,
                cache: !1,
                beforeSend: function() {
                    motify.log("正在处理订单...", 0),
                    n.$("button").prop("disabled", !0).html("正在努力加载，请稍等...")
                },
                success: function(i) {
                    n.onPayBtnClicked(e),
                    motify.clear();
                    var s = i.code;
                    switch (s) {
                    case 0:
                        var o = i.data.pay_data,
                        a = i.data.redirect_url,
                        r = i.data.pay_return_url,
                        d = i.data.pay_return_data;
                        if ("wxapppay" === t) n.doFinishWxAppPay(d);
                        else if ("wxpay" === t) n.doFinishWxPay(o, a, r, d);
                        else {
                            if (!o || !o.submit_url) return void motify.log("支付过程出错，请联系客服！");
                            n.doFinishOtherPay(o)
                        }
                        break;
                    case 11022:
                    case 11023:
                        alert(i.msg),
                        wxReady && wxReady(function() {
                            window.WeixinJSBridge && window.WeixinJSBridge.invoke("closeWindow", {})
                        });
                        break;
                    case 11010:
                        window.Utils.needConfirm(i.msg,
                        function() {
                            e.accept_price = 1,
                            n.submitPay(e, t)
                        },
                        function() {
                            motify.log("正在跳转中...", 0),
                            window.location.href = window._global.url.wap + "/showcase/goods?alias=" + window._global.goods_alias
                        });
                        break;
                    case 11012:
                    case 11024:
                    case 11026:
                    case 11027:
                        motify.log("正在跳转...");
                        var l = "wxpay" != t ? window._global.url.trade + "/trade/order/result?order_id=" + n.orderId + "#wechat_webview_type=1": n.wxPayResultUrl;
                        window.location.href = l;
                        break;
                    case 21e3:
                        window.location.reload();
                        break;
                    default:
                        n.render(),
                        motify.log(i.msg)
                    }
                },
                error: function() {
                    motify.clear(),
                    motify.log("生成支付单失败。"),
                    n.render()
                },
                complete: function() {
                    n.render()
                }
            })
        },
        doFinishOtherPay: function(e) {
            if (!this.submitted) {
                var t = '<form method="post" action="' + e.submit_url + '">';
                delete e.submit_url,
                _(e).map(function(e, n) {
                    t += '<input type="hidden" name="' + n + '" value="' + e + '" />'
                }),
                t += "</form>";
                var n = $(t);
                n.submit(),
                this.submitted = !0
            }
        },
        doFinishWxPay: function(e, t, n, i) {
            var s = this;
            "string" == typeof e && (e = $.parseJSON(e)),
            window.WeixinJSBridge && window.WeixinJSBridge.invoke("getBrandWCPayRequest", e,
            function(e) {
                var n = e.err_msg;
                return "get_brand_wcpay_request:ok" === n ? (motify.log("支付成功，正在处理订单...", 0), window.location.href = t, !1) : void("get_brand_wcpay_request:fail" === n ? (motify.log("支付失败，请重新尝试！"), s.render()) : (motify.clear(), s.render()))
            })
        },
        doFinishWxAppPay: function() {
            function e(e, t) {
                t || (t = "weixin"),
                isIosDevice ? (e = encodeURIComponent(e), document.location.hash = "#func=appWXPay&params=" + e) : isAndroid && window.android.appWXPay(e)
            }
            return function(t) {
                e("kdt_id=" + t.kdt_id + "&order_no=" + t.order_no)
            }
        } (),
        render: function() {
            var e = this;
            return this.$el.html(this.template(_.extend({
                data: this.model.toJSON()
            },
            {
                getBtnClass: function() {
                    return parseInt(e.model.get("order")) > 0 ? " btn-white": " btn-green"
                }
            }))),
            this.$el.css("margin-bottom", "10px"),
            this
        }
    }),
    n = Backbone.View.extend({
        className: "pay-way-opts",
        initialize: function(e) {
            this.collection = e.collection,
            $("body").append('<div                 id="confirm-pay-way-opts-popup-bg"                 style="display:none; width: 100%; height: 100%;                 position: fixed; top:0; left:0;                 background-color: rgba(0, 0, 0, .5);"></div>'),
            this.bg = $("#confirm-pay-way-opts-popup-bg"),
            this.bg.on("click", _(this.hide).bind(this)),
            this.listOpt = {
                el: this.$el,
                itemView: t,
                collection: this.collection,
                itemOptions: e.itemOptions
            }
        },
        events: {
            "click #confirm-pay-way-opts-popup-bg": "hide"
        },
        render: function() {
            return this.payWayListView = new e(this.listOpt).render(),
            this
        },
        show: function() {
            this.$el.addClass("active"),
            this.bg.show()
        },
        hide: function() {
            this.$el.removeClass("active"),
            this.bg.hide()
        }
    });
    return Backbone.View.extend({
        initialize: function(e) {
            this.collection = new Backbone.Collection,
            this.nPayTips = e.nPayTips,
            this.itemOptions = e.itemOptions || {},
            this.pagePayWaySize = e.pagePayWaySize || 3,
            this.payUrl = e.payUrl || window._global.url.trade + "/trade/order/pay.json",
            this.orderId = e.orderId || window._global.order_id,
            this.orderPrice = e.orderPrice,
            this.getPayDataExtr = e.getPayDataExtr ||
            function() {
                return {}
            },
            this.wxPayResultUrl = e.wxPayResultUrl,
            this.onPayBtnClicked = e.onPayBtnClicked ||
            function() {},
            this.otherPayText = e.otherPayText;
            var n = e.payWays,
            i = new Backbone.Collection;
            _.each(n,
            function(e, t) {
                e.id = t,
                e.order = t,
                i.add(new Backbone.Model(e))
            }),
            this.listOpt = {
                el: this.$el,
                itemView: t,
                collection: this.collection,
                itemOptions: _.extend(this.itemOptions, {
                    onOtherPayClicked: _(this.onOtherPayClicked).bind(this),
                    payUrl: this.payUrl,
                    orderId: this.orderId,
                    getPayDataExtr: this.getPayDataExtr,
                    wxPayResultUrl: this.wxPayResultUrl,
                    onPayBtnClicked: this.onPayBtnClicked
                })
            },
            this.allPayWayCollection = i,
            this.initPagePayWay()
        },
        events: {
            "click .js-other-pay": "onOtherPayClicked"
        },
        render: function() {
            return this.payWayListView = new e(this.listOpt).render(),
            this.allPayWayCollection.length > this.pagePayWaySize && this.initPopPayWayListView(),
            this
        },
        initPagePayWay: function() {
            if (0 === this.allPayWayCollection.length) return this.nPayTips && this.nPayTips.html("无可用的支付方式"),
            this;
            if (this.allPayWayCollection.length <= this.pagePayWaySize) for (var e = 0; e < this.allPayWayCollection.length; e++) this.collection.add(this.allPayWayCollection.get(e));
            else {
                for (var e = 0; e < this.pagePayWaySize - 1; e++) this.collection.add(this.allPayWayCollection.get(e));
                this.collection.add(new Backbone.Model({
                    code: "other",
                    name: this.otherPayText || "其他支付方式",
                    order: e
                }))
            }
        },
        initPopPayWayListView: function() {
            $("body").append('<div id="confirm-pay-way-opts-popup" class="popup"></div>'),
            this.popPayWayListView = new n({
                collection: this.allPayWayCollection,
                el: $("#confirm-pay-way-opts-popup"),
                itemOptions: this.itemOptions
            }).render()
        },
        onOtherPayClicked: function() {
            this.popPayWayListView || this.initPopPayWayListView(),
            this.popPayWayListView.show()
        }
    })
}),
define("wap/trade/message_view", [],
function() {
    return Backbone.View.extend({
        initialize: function(e) {
            this.onHide = e.onHide ||
            function() {},
            this.nTarget = e.nTarget
        },
        events: {
            "click .js-cancel": "hide"
        },
        render: function() {
            var e = this.nTarget.parent().parent().clone();
            this.$(".js-list").empty().append(e),
            this.$("a.link").hide();
            var t = $(".js-message");
            t.length > 0 ? this.$(".js-message-container").html(t.clone().html()) : (this.$(".js-message-container").hide(), this.$("h2").hide())
        },
        hide: function() {
            this.onHide()
        }
    })
}),
define("text!wap/trade/confirm/view/coupon/templates/useCoupon.html", [],
function() {
    return '<div class="block-module order-coupon order-coupon-used">\n    <h4 class="block-module-title">选择优惠：</h4>\n    <div class="js-normal-coupon coupon-info">\n        <span class="coupon-title"><%=couponData.name %></span></em>\n        <p><i class="coupon-condition"><%=couponData.condition %></i></p>\n    </div>\n    <% if ( orderState < 3 ) { %>\n    <div class="opt-wrapper"><a href="javascript:;" class="js-change-coupon btn btn-xxsmall btn-grayeee">更换</a></div>\n    <% } %>\n</div>'
}),
define("text!wap/trade/confirm/view/coupon/templates/availableCoupon.html", [],
function() {
    return '<div class="block-module order-coupon order-coupon-used">\n    <h4 class="block-module-title">选择优惠：</h4>\n    <div class="js-normal-coupon coupon-info">\n        <span class="coupon-title">您有 <%=available %> 个可用优惠</span>\n        <% if ( orderState < 3 ) { %>\n        <div class="opt-wrapper"><a href="javascript:;" class="js-change-coupon btn btn-xxsmall btn-grayeee">使用</a></div>\n        <% } %>\n    </div>\n</div>'
}),
define("text!wap/trade/confirm/view/coupon/templates/emptyCoupon.html", [],
function() {
    return '<div class="block-module order-coupon order-coupon-used">\n    <h4 class="block-module-title">选择优惠：</h4>\n    <div class="js-normal-coupon coupon-info">\n        <span class="coupon-title">未使用优惠</span>\n        <% if ( orderState < 3 ) { %>\n        <div class="opt-wrapper"><a href="javascript:;" class="js-change-coupon opt-link">使用优惠码</a></div>\n        <% } %>\n    </div>\n</div>'
}),
define("wap/trade/confirm/view/coupon/model", [],
function() {
    var e = Backbone.Model.extend({
        idAttribute: "m_id",
        defaults: {
            coupon: null,
            order: null
        },
        initialize: function() {}
    }),
    t = Backbone.Collection.extend({
        model: e
    });
    return t
}),
define("text!wap/trade/confirm/view/coupon/templates/codeInfo.html", [],
function() {
    return "<% if(coupon.value > 0) { %>\n    <em>￥-<%=coupon.value.toFixed(2) %></em>\n<% } %>\n<p><%=coupon.condition %></p>"
}),
define("wap/trade/confirm/view/coupon/view/code_view", ["text!wap/trade/confirm/view/coupon/templates/codeInfo.html", "wap/components/list"],
function(e, t) {
    var n = function() {},
    i = Backbone.View.extend({
        events: {},
        initialize: function(e) {
            e = e || {},
            this.useCoupon = e.useCoupon || n,
            this.listOpt = {
                el: this.$(".js-coupon-container"),
                itemView: e.CouponItemView,
                finishedHTML: " ",
                collection: e.collection,
                itemOptions: {
                    itemTemplateHtml: $("#tpl-address-item").html(),
                    onItemClick: this.useCoupon
                }
            },
            this.orderId = e.orderId,
            this.urlTrade = e.urlTrade
        },
        render: function() {
            return this.CodeListView = new t(this.listOpt),
            this.CodeListView.render(),
            this.codeInputerEle = this.$(".js-code-inputer"),
            this.codeInputer = new s({
                el: this.codeInputerEle,
                useCoupon: this.useCoupon,
                orderId: this.orderId,
                urlTrade: this.urlTrade
            }),
            this
        }
    }),
    s = Backbone.View.extend({
        events: {
            "input .js-code-txt": "checkCode",
            "click .js-valid-code": "validCode"
        },
        codeInfoTemplate: _.template(e),
        initialize: function(e) {
            e = e || {},
            this.useCoupon = e.useCoupon,
            this.orderId = e.orderId,
            this.urlTrade = e.urlTrade,
            this.nCodeTxt = this.$(".js-code-txt"),
            this.nValidBtn = this.$(".js-valid-code")
        },
        checkCode: function(e) {
            e.preventDefault();
            var t = this.getCode();
            this.nValidBtn.prop("disabled", 0 === t.length)
        },
        getCode: function() {
            var e = this,
            t = e.nCodeTxt.val();
            return t = $.trim(t)
        },
        validCode: function(e) {
            var t = this;
            e.preventDefault();
            var n = t.getCode();
            return n ? void t.validToServer(n) : (motify.log("请输入优惠码"), t.nCodeTxt.focus(), !1)
        },
        validToServer: function(e) {
            var t = this,
            n = this.urlTrade + "/trade/order/validateCode.json",
            i = {
                order_id: t.orderId,
                code: e
            };
            $.ajax({
                url: n,
                type: "POST",
                dataType: "json",
                timeout: 8e3,
                cache: !1,
                data: i,
                beforeSend: function() {
                    t.nValidBtn.text("验证中..."),
                    t.nValidBtn.prop("disabled", !0)
                },
                success: function(e) {
                    0 === e.code ? t.showCouponInfo(e.data) : motify.log(e.msg)
                },
                error: function() {},
                complete: function() {
                    t.nValidBtn.text("验证"),
                    t.nValidBtn.prop("disabled", !1)
                }
            })
        },
        showCouponInfo: function(e) {
            var t = this,
            n = Utils.checkIsExist(e);
            if (n) motify.log("该优惠码您已经拥有，<br />已为您自动选中～");
            else {
                var i = t.codeInfoTemplate(e);
                this.$(".js-coupon-info").html(i),
                Utils.setCouponIdAttr(t.$el, e)
            }
            this.useCoupon(e)
        }
    });
    return i
}),
define("text!wap/trade/confirm/view/coupon/templates/couponItem.html", [],
function() {
    return '<label class="label-check">\n    <div class="label-check-img"></div>\n    <div class="coupon-info">\n        <span><%=coupon.name %></span>\n        <em>￥-<%=coupon.value.toFixed(2) %></em>\n        <p><%=coupon.condition %></p>\n    </div>\n</label>\n'
}),
define("wap/trade/confirm/view/coupon/view/item_view", ["text!wap/trade/confirm/view/coupon/templates/couponItem.html"],
function(e) {
    var t = function() {},
    n = Backbone.View.extend({
        tagName: "div",
        className: "block-module order-coupon order-coupon-item",
        template: _.template(e),
        events: {
            click: "onClick"
        },
        initialize: function(e) {
            e = e || {},
            this.onItemClick = e.onItemClick || t
        },
        render: function() {
            var e = this.model.toJSON(),
            t = this.template(e);
            return this.$el.html(t),
            Utils.setCouponIdAttr(this.$el, e),
            this
        },
        onClick: function(e) {
            e.preventDefault();
            var t = this.model.toJSON();
            this.onItemClick(t)
        }
    });
    return n
}),
define("wap/trade/confirm/view/coupon/view/card_view", ["wap/trade/confirm/view/coupon/view/item_view", "wap/components/list"],
function(e, t) {
    var n = function() {},
    i = Backbone.View.extend({
        initialize: function(t) {
            t = t || {},
            this.collection = t.collection || [],
            this.listOpt = {
                el: this.$(".js-coupon-container"),
                itemView: e,
                finishedHTML: " ",
                collection: t.collection,
                itemOptions: {
                    itemTemplateHtml: $("#tpl-address-item").html(),
                    onItemClick: t.useCoupon || n
                }
            }
        },
        render: function() {
            return this.CodeListView = new t(this.listOpt),
            this.CodeListView.render(),
            this.collection.length > 0 && this.$el.show(),
            this
        }
    });
    return i
}),
define("wap/trade/confirm/view/coupon/view/confirm_view", [],
function() {
    var e = function() {},
    t = Backbone.View.extend({
        events: {
            "click .js-confirm-coupon": "confirmUse"
        },
        initialize: function(t) {
            t = t || {},
            this.onCouponChanged = t.onCouponChanged || e,
            this.nTotalCoupon = this.$(".js-total-privilege")
        },
        confirmUse: function(e) {
            e.preventDefault(),
            this.$el.hide(),
            this.onCouponChanged(this.selectedCoupon)
        },
        useCoupon: function(e) {
            if (this.selectedCoupon = e, !e) return ! 1;
            var t = e.coupon,
            n = t.value || 0,
            i = "总优惠：¥" + n.toFixed(2);
            this.nTotalCoupon.html(i)
        },
        show: function() {
            this.$el.show()
        }
    });
    return t
}),
define("wap/trade/confirm/view/coupon/view/selector_view", ["wap/trade/confirm/view/coupon/model", "wap/trade/confirm/view/coupon/view/code_view", "wap/trade/confirm/view/coupon/view/card_view", "wap/trade/confirm/view/coupon/view/confirm_view", "wap/trade/confirm/view/coupon/view/item_view", "wap/components/list"],
function(e, t, n, i, s, o) {
    var a = function() {},
    r = Backbone.View.extend({
        events: {
            "click .js-not-use": "notUse"
        },
        initialize: function(e) {
            e = e || {},
            this.onCouponChanged = e.onCouponChanged || a,
            this.orderState = e.orderState,
            this.selectedCoupon = null,
            this.coupons = e.coupons || {},
            this.currentCoupon = e.currentCoupon || {},
            this.orderId = e.orderId,
            this.urlTrade = e.urlTrade
        },
        render: function() {
            return this.codeCollection = new e(this.coupons.codes || []),
            this.codesView = new t({
                el: this.$(".js-code-container"),
                collection: this.codeCollection,
                useCoupon: _(this.useCoupon).bind(this),
                CouponItemView: s,
                orderId: this.orderId,
                urlTrade: this.urlTrade
            }).render(),
            this.cardCollection = new e(this.coupons.cards || []),
            this.listOpt = {
                el: $(".js-card-container .js-coupon-container"),
                itemView: s,
                finishedHTML: " ",
                collection: this.cardCollection,
                itemOptions: {
                    itemTemplateHtml: $("#tpl-address-item").html(),
                    onItemClick: _(this.useCoupon).bind(this) || a
                }
            },
            this.CodeListView = new o(this.listOpt),
            this.CodeListView.render(),
            this.cardCollection.length > 0 && $(".js-card-container").show(),
            this.confirmUseView = new i({
                el: $(".js-confirm-use-coupon"),
                onCouponChanged: this.onCouponChanged
            }),
            this.confirmShow = _(this.confirmUseView.show).bind(this.confirmUseView) || a,
            this
        },
        notUse: function(e) {
            var t = this;
            e.preventDefault();
            var n = t.coupons.none_coupon;
            this.useCoupon(n)
        },
        useCoupon: function(e) {
            this.selectedCoupon = e,
            this.confirmUseView && this.confirmUseView.useCoupon(e),
            this.show()
        },
        show: function() {
            var e = this,
            t = e.selectedCoupon;
            if (!t) return ! 1;
            e.$el.find(".order-coupon-item").removeClass("active");
            var n = Utils.findCouponItem(t);
            n.addClass("active")
        },
        autoUseCoupon: function() {
            var e = this,
            t = e.coupons,
            n = e.orderState,
            i = null;
            if (1 === n || 2 === n) {
                if (!t) return ! 1;
                i = t.default_coupon ? t.default_coupon: t.none_coupon
            } else 3 === n && (i = this.currentCoupon);
            this.useCoupon(i),
            this.onCouponChanged(i)
        }
    });
    return r
}),
window.Utils = window.Utils || {},
$.extend(window.Utils, {
    setCouponIdAttr: function(e, t) {
        var n = t.coupon,
        i = "coupon-" + n.type + "-" + n.id;
        e.attr("id", i)
    },
    findCouponItem: function(e) {
        var t = e.coupon,
        n = "#coupon-" + t.type + "-" + t.id,
        i = $(n);
        return i
    },
    checkIsExist: function(e) {
        var t = this.findCouponItem(e),
        n = t.length > 0;
        return n
    }
}),
define("wap/components/util/coupon",
function() {}),
define("wap/trade/confirm/view/coupon/main", ["text!wap/trade/confirm/view/coupon/templates/useCoupon.html", "text!wap/trade/confirm/view/coupon/templates/availableCoupon.html", "text!wap/trade/confirm/view/coupon/templates/emptyCoupon.html", "wap/trade/confirm/view/coupon/view/selector_view", "wap/components/util/coupon"],
function(e, t, n, i) {
    var s = window._global,
    o = function() {},
    a = Backbone.View.extend({
        el: ".js-used-coupon",
        events: {
            "click .js-change-coupon": "showCouponSelector"
        },
        useCouponTemplate: _.template(e),
        availableCouponTemplate: _.template(t),
        emptyCouponTemplate: _.template(n),
        initialize: function(e) {
            e = e || {},
            this.orderState = s.order_state || 1,
            this.hasAddress = 0,
            this.coupons = window._global.coupons || {},
            this.currentCoupon = window._global.current_coupon || {},
            this.orderId = window._global.order_id,
            this.urlTrade = window._global.url.trade,
            this.no_user_login = window._global.no_user_login,
            this.onCouponChanged = e.onCouponChanged || o,
            this.contentEle = $(".js-page-content").find(".content"),
            this.footerEle = $(".footer"),
            this.modalEle = $(".js-modal"),
            this.orderTotalEle = $(".js-order-total")
        },
        render: function(e) {
            return e = e || {},
            !this.coupons && this.orderState < 3 ? this: this.no_user_login ? this: (this.couponSelectorView = new i({
                el: $(".js-coupon-ui"),
                orderState: this.orderState,
                coupons: this.coupons,
                currentCoupon: this.currentCoupon,
                orderId: this.orderId,
                urlTrade: this.urlTrade,
                onCouponChanged: _(function(e) {
                    this.onCouponChanged(e),
                    this.hideCouponSelector(),
                    this.updateCouponPanel(e)
                }).bind(this)
            }).render(), this.couponSelectorView.autoUseCoupon(), this)
        },
        showCouponSelector: function() {
            this.couponSelectorView.show(),
            motify.clear(),
            $(".scene").filter(".js-scene-coupon-list").removeClass("hide").siblings(".scene").addClass("hide"),
            this.contentEle.hide(),
            this.footerEle.hide(),
            this.modalEle.addClass("active"),
            this.couponSelectorView.confirmShow()
        },
        hideCouponSelector: function() {
            this.modalEle.removeClass("active"),
            this.contentEle.show(),
            this.footerEle.show()
        },
        updateCouponPanel: function(e) {
            var t = this,
            n = "";
            if (t.usedCoupon = e, !e) return ! 1;
            var i = e.coupon || {};
            if (0 !== i.id) n = t.useCouponTemplate({
                couponData: i,
                orderState: t.orderState
            });
            else {
                var s = t.coupons ? t.coupons.total: 0;
                n = s > 0 ? t.availableCouponTemplate({
                    available: s,
                    orderState: t.orderState
                }) : t.emptyCouponTemplate({
                    orderState: t.orderState
                })
            }
            t.$el.html(n),
            t.$el.show(),
            (1 === t.orderState || 2 === t.orderState) && Utils.flashAnimate(t.$(".block-module"))
        }
    });
    return a
}),
define("text!wap/trade/confirm/templates/totlePrice.html", [],
function() {
    return "<p>￥<%= pay %> + ￥<%= postage %>运费\n    <% if (decrease && decrease > 0) { %>\n        - ￥<%= decrease %>优惠\n    <% } %>\n    <% if (order.operation) { %>\n        <% var tmp = order.operation.new_pay - order.operation.origin_pay; %>\n        <%= (tmp > 0) ? '+' : '-' %> ￥<%= new Number(Math.abs(tmp) / 100).toFixed(2) %>\n    <% } %>\n</p>\n<strong class=\"js-real-pay c-orange js-real-pay-temp\">\n    需付：￥<%= real_pay %>\n</strong>"
}),
require(["wap/trade/confirm/view/logistics_express_kdt", "wap/trade/confirm/view/logistics_express_wx", "wap/components/address/logistics_selector", "wap/components/util/address", "vendor/backbone_wreqr", "wap/components/pop_page", "wap/components/pay", "wap/trade/message_view", "wap/trade/confirm/view/coupon/main", "text!wap/trade/confirm/templates/totlePrice.html"],
function(e, t, n, i, s, o, a, r, d, l) {
    Backbone.EventCenter = _.extend({},
    Backbone.Events),
    window.EC = window.EC || new Backbone.Wreqr.EventAggregator; {
        var c = Backbone.View.extend({
            initialize: function() {
                Backbone.EventCenter.on("address_list:show", this.hideContainer),
                Backbone.EventCenter.on("address_list:hide", this.showContainer),
                Backbone.EventCenter.on("address:has", this.showPaymentArea),
                Backbone.EventCenter.on("address:change", this.showPaymentArea),
                Backbone.EventCenter.on("address:hidePaymentArea", this.hidePaymentArea),
                Backbone.EventCenter.on("address:switchAddressType", this.hidePaymentArea),
                this.totalPriceTemplate = _.template(l),
                this.nTotalPrice = $(".js-order-total");
                var i = ((((window._global.ump || {}).order || {}).coupons || {}).money || {}).order,
                s = window._global.ump || {};
                this.priceData = {
                    real_pay: i.real_pay,
                    postage: s.final.postage / 100
                };
                var o = $("#js-logistics-container");
                o.length > 0 && (this.expressWaysView = new n({
                    el: o,
                    onAddressChanged: _(this.onAddressChanged).bind(this),
                    logisticsExpressKdtView: e,
                    logisticsExpressWxView: t,
                    onWxAddressFailed: _(this.hideWxPay).bind(this),
                    not_saveToSever: window._global.order_state > 2
                })),
                this.couponView = new d({
                    onCouponChanged: _(function(e) {
                        this.couponData = e,
                        this.changePrice()
                    }).bind(this)
                }).render();
                var r = window._global.payWays,
                c = window._global.url.trade;
                this.payWaysContainer = $("#confirm-pay-way-opts"),
                this.pagePayWayListView = new a({
                    payWays: r,
                    el: this.payWaysContainer,
                    nPayTips: $(".js-pay-tip"),
                    nContainer: $(".container"),
                    payUrl: c + "/trade/order/pay.json",
                    wxReturnUrl: c + "/pay/wxpay/return.json",
                    wxPayResultUrl: c + "/trade/order/result?order_id=" + window._global.order_id + "&order_paid=1#wechat_webview_type=1&refresh",
                    orderId: window._global.order_id,
                    pagePayWaySize: parseInt(window._global.pagePayWaySize || 2),
                    onPayBtnClicked: _(this.onPayBtnClicked).bind(this),
                    getPayDataExtr: _(function() {
                        var e = {};
                        if (this.couponData) {
                            var t = this.couponData.coupon;
                            t && (e.coupon_id = t.id, e.coupon_type = t.type)
                        }
                        if (!this.expressWaysView) return e; {
                            var n = this.expressWaysView.logisticsWay,
                            i = this.expressWaysView.logisticsViews[n];
                            i.address
                        }
                        if ("express" === n) return i.cannotDeliver ? (motify.log("该地区暂不支持配送<br/>请修改收货地址"), !1) : i.sendingToServer ? (motify.log("收货地址提交中，请稍等"), !1) : (e.express_type = n, e);
                        var s = $(".js-self-fetch .js-name").val().trim(),
                        o = $(".js-self-fetch .js-phone").val().trim(),
                        a = $(".js-self-fetch .js-time[type=date]").val().trim(),
                        r = $(".js-self-fetch .js-time[type=time]").val().trim();
                        return s ? Utils.validPhone(o) || Utils.validMobile(o) ? a && r ? _.extend(e, {
                            user_name: s,
                            user_tel: o,
                            user_time: a + " " + r,
                            express_type: n
                        }) : (motify.log("请填写预约时间"), !1) : (motify.log("请填写正确的联系方式"), !1) : (motify.log("请填写您的姓名！"), !1)
                    }).bind(this)
                }).render(),
                $.fn.MiniCounter && $(".js-mini-counter").MiniCounter({
                    callback: function() {
                        $(".js-counter-msg").html('<h3>订单状态：您的订单已取消。</h3><hr /><p class="c-orange">您的订单因超时未付款，已经自动取消。</p>')
                    }
                }),
                wxReady && (wxReady(function() {
                    initImagePre()
                }), window._global.order_state > 2 && this.showPaymentArea())
            },
            events: {
                "click .js-show-message": "onShowMessageClicked"
            },
            hideWxPay: function() {},
            onShowMessageClicked: function(e) {
                this.messagePopPage = new o({
                    nPageContents: [$("#js-page-content")],
                    el: $("#sku-message-poppage"),
                    contentViewClass: r,
                    contentViewOptions: {
                        nTarget: $(e.target)
                    }
                }).render().show()
            },
            onPayBtnClicked: function(e) {
                this.expressWaysView && this.expressWaysView.onPayBtnClicked(e)
            },
            hideContainer: function() {},
            showContainer: function() {},
            showPaymentArea: function() {
                $(".js-step-topay").removeClass("hide")
            },
            hidePaymentArea: function() {
                $(".js-step-topay").addClass("hide")
            },
            onAddressChanged: function(e) {
                if (this.isSelfFetch = "self-fetch" === e.logisticsWay, e.isResetPriceData) {
                    var t = (e.data || {}).pay_data || {};
                    this.priceData.real_pay = t.real_pay / 100,
                    this.priceData.postage = t.postage / 100
                }
                this.changePrice()
            },
            changePrice: function() {
                var e = ((((window._global.ump || {}).order || {}).coupons || {}).money || {}).order,
                t = window._global.ump || {},
                n = this.priceData.real_pay,
                i = this.priceData.postage,
                s = this.priceData.real_pay,
                o = 0,
                a = (this.priceData.real_pay, window._global.order_state);
                if (e) {
                    n = s - i,
                    this.isSelfFetch && (s -= i, i = 0),
                    this.couponData && (o = this.couponData.coupon.value || this.couponData.order.decrease, 3 > a ? s -= o: n += o),
                    t.order.operation && window._global.order_state > 2 && (n -= (t.order.operation.new_pay - t.order.operation.origin_pay) / 100);
                    var r = this.totalPriceTemplate({
                        order: t.order || {},
                        postage: i.toFixed(2),
                        real_pay: s.toFixed(2),
                        decrease: o.toFixed(2),
                        pay: n.toFixed(2)
                    });
                    this.nTotalPrice.html(r),
                    (1 === a || 2 === a) && Utils.flashAnimate(this.nTotalPrice)
                }
            }
        });
        new c({
            el: $("body")
        })
    }
}),
define("main",
function() {});