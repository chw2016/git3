define("wap/components/quantity", [],
function() {
    var t = function() {},
    i = Backbone.View.extend({
        template: _.template('<div class="quantity">            <div class="response-area response-area-minus"></div>            <button class="minus" type="button" <% if (data.readonly){ %> disabled <% } %> ></button>            <input type="number" class="txt" value="<%= data.num %>" <% if (data.readonly){ %> readonly <% } %>/>            <button class="plus" type="button" <% if (data.readonly){ %> disabled <% } %>></button>            <div class="response-area response-area-plus"></div>            <div class="txtCover"></div>        </div>'),
        initialize: function() {
            var i = function(t, i, e) {
                var n;
                return e > i ? (n = i, 0 !== i && (this.disabled = !0)) : n = e > t ? e: t > i ? i: t,
                n
            };
            return function(e) {
                this.onCountChange = e.onCountChange || t,
                this.onOverLimit = e.onOverLimit || t,
                this.limitNum = parseInt(e.limitNum),
                this.leastNum = e.leastNum || 1,
                this.onBelowLeast = e.onBelowLeast || t,
                this.disabled = e.disabled,
                this.num = i.call(this, e.num, this.limitNum, this.leastNum),
                this.readonly = e.readonly
            }
        } (),
        events: {
            "click .response-area-minus": "subCount",
            "click .response-area-plus": "addCount",
            "click .txtCover": "txtFocus",
            "blur .txt": "txtBlur"
        },
        txtFocus: function(t) {
            this.disabled || (this.$el.find(".txt").focus(), $(t.target).css("display", "none"))
        },
        txtBlur: function() {
            this.$el.find(".txtCover").css("display", "block")
        },
        subCount: function() {
            this.changeNum( - 1)
        },
        addCount: function() {
            this.changeNum(1)
        },
        changeNum: function(t) {
            if (!this.readonly && (this.refreshNum(), !this.disabled)) {
                var i = this.num + parseInt(t);
                if (1 == parseInt(t) && i > this.limitNum) return void this.onOverLimit(i);
                if ( - 1 == parseInt(t) && i < this.leastNum) return void this.onBelowLeast(i);
                1 > i && (i = 1),
                this.updateBtnStatus(i),
                this.updateNum(i)
            }
        },
        updateBtnStatus: function() {
            var t = function(t) {
                t.addClass("disabled"),
                t.attr("disabled", "true")
            },
            i = function(t) {
                t.removeClass("disabled"),
                t.removeAttr("disabled")
            };
            return function(e) {
                e > 1 ? i(this.nMinus) : t(this.nMinus),
                this.limitNum > 0 && e >= this.limitNum ? t(this.nPlus) : i(this.nPlus)
            }
        } (),
        updateNum: function(t) {
            this.disabled || (this.num = +t, this.$("input").val(this.num), this.onCountChange(this.num))
        },
        refreshNum: function() {
            var t = parseInt(this.$("input").val());
            t > 0 ? this.num = t: (this.num = 1, this.updateNum(this.num))
        },
        setLimitNum: function(t) {
            this.disabled || 1 > t || (this.limitNum = +t, this.limitNum < this.num && this.updateNum(this.limitNum))
        },
        validateNum: function() {
            var t = parseInt(this.$("input").val());
            return this.$("input").val(t),
            t > this.limitNum || 0 === this.limitNum ? void this.onOverLimit(t) : t < this.leastNum ? void this.onBelowLeast(t) : !0
        },
        getNum: function() {
            return this.validateNum() ? (this.refreshNum(), this.num) : void 0
        },
        render: function() {
            return this.$el.html(this.template({
                data: {
                    num: this.num,
                    readonly: this.readonly
                }
            })),
            this.nMinus = this.$(".minus"),
            this.nPlus = this.$(".plus"),
            this.updateBtnStatus(this.num),
            this
        }
    });
    return i
}),
define("wap/components/uploader/photo_uploader", [],
function() {
    var t = function() {
        if (navigator.userAgent.match(/(Android (1.0|1.1|1.5|1.6|2.0|2.1))|(Windows Phone (OS 7|8.0))|(XBLWP)|(ZuneWP)|(w(eb)?OSBrowser)|(webOS)|(Kindle\/(1.0|2.0|2.5|3.0))/)) return ! 1;
        var t = document.createElement("input");
        return t.type = "file",
        !t.disabled
    },
    i = Backbone.View.extend({
        initialize: function() {
            this.nInput = this.$("input"),
            this.nUploader = this.$("button")
        },
        events: {
            "click input": "onInputClicked",
            "change input": "onFileChanged"
        },
        render: function() {
            t() || this.nUploader.css("padding-left", "10px").html("您的浏览器不支持图片上传").attr("disabled", "disabled")
        },
        onInputClicked: function() {},
        onFileChanged: function(t) {
            var i = this,
            e = t.target.files[0],
            n = new FileReader;
            this.file = e,
            n.onload = function(t) {
                i.$("img").removeClass("hide").attr("src", t.target.result)
            },
            n.readAsDataURL(e),
            this.getUploadToken()
        },
        getUploadToken: function() {
            var t = this;
            $.ajax({
                url: window._global.url.wap + "/common/qiniu/upToken.jsonp",
                type: "get",
                dataType: "jsonp",
                timeout: 5e3,
                cache: !1,
                data: {
                    scope: window._global.js.qn_public
                },
                beforeSend: function() {
                    t.nUploader.html("正在上传...")
                },
                success: function(i) {
                    t.doUploadPhoto(i.data.uptoken)
                },
                error: function(t, i, e) {},
                complete: function(t, i) {}
            })
        },
        doUploadPhoto: function(t) {
            var i = this,
            e = new FormData;
            e.append("token", t),
            e.append("file", this.file);
            var n = this.file.name.split("."),
            s = "";
            n.length > 1 && (s = "." + n[n.length - 1]),
            e.append("x:ext", s),
            $.ajax({
                url: "http://up.qiniu.com",
                type: "post",
                data: e,
                dataType: "json",
                processData: !1,
                contentType: !1,
                beforeSend: function() {
                    i.nInput.data("uploaded", "false")
                },
                success: function(t) {
                    i.$("img").removeClass("hide").attr("src", t.data.attachment_full_url + "!100x100.jpg"),
                    i.nInput.data("value", t.data.attachment_full_url),
                    i.nInput.data("uploaded", "true"),
                    i.nUploader.html("修改")
                },
                error: function(t, e, n) {
                    i.nUploader.html("重新上传")
                },
                complete: function(t, i) {}
            })
        }
    });
    return i
}),
window.Utils = window.Utils || {},
$.extend(window.Utils, {
    validMobile: function(t) {
        return t = "" + t,
        /^((\+86)|(86))?(1)\d{10}$/.test(t)
    },
    validPhone: function(t) {
        return t = "" + t,
        /^0[0-9\-]{10,13}$/.test(t)
    },
    validNumber: function(t) {
        return /^\d+$/.test(t)
    },
    validEmail: function(t) {
        return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(t)
    },
    validPostalCode: function(t) {
        return t = "" + t,
        /^\d{6}$/.test(t)
    }
}),
define("wap/components/util/valid",
function() {}),
define("text!wap/showcase/sku/templates/message.html", [],
function() {
    return '<% if(messages.length !== 0) {%>\n    <div class=\'messageView\'>\n        <% for (var j = 0, len = messages.length; j < len; j++) { %>\n        <dl class="clearfix">\n            <dt class="model-title pull-left"><label for="ipt-<%=j %>"><% if (messages[j].required == \'1\') { %><sup class="required">*</sup><% } %><%=messages[j].name %></label>    \n            </dt>\n            <span class=\'split pull-left\'>|</span>\n            <dd class="comment-wrapper clearfix">\n                <% if (messages[j].multiple == \'0\') { %>\n                    <% if (messages[j].type == \'image\') { %>\n                        <input data-valid-type="<%=messages[j].type %>" <% if (messages[j].required == \'1\') { %>required<% } %> tabindex="<%=j + 1 %>" id="ipt-<%=j %>" name="message_<%=j %>" type="file" capture="camera" accept="image/*" class="js-message photo-input" >\n                        <button class="btn btn-white image-input-trigger pull-right">拍照&nbsp;或&nbsp;选择相片</button>\n                        <div class=\'image-input-show clearfix\'>\n                            <img class="hide" width=50 height=50 />\n                        </div>\n                    <% }else { %>\n                        <input data-valid-type="<%=messages[j].type %>" <% if (messages[j].required == \'1\') { %>required<% } %> tabindex="<%=j + 1 %>" id="ipt-<%=j %>" name="message_<%=j %>" type="<%=messages[j].type %>" class="txt js-message font-size-14" />\n                    <% } %>\n                <% } else { %>\n                <textarea data-valid-type="<%=messages[j].type %>" <% if (messages[j].required == \'1\') { %>required<% } %> tabindex="<%=j + 1 %>" id="ipt-<%=j %>" name="message_<%=j %>" cols="32" rows="1" class="txta js-message font-size-14"></textarea>\n                <% } %>\n                <% if (messages[j].type != \'image\') { %>\n                    <div class=\'txtCover\'></div>\n                <% } %>\n            </dd>\n        </dl>\n        <% } %>\n    </div>\n<% } %>'
}),
define("wap/showcase/sku/views/message", ["wap/components/uploader/photo_uploader", "wap/components/util/valid", "text!wap/showcase/sku/templates/message.html"],
function(t, i, e) {
    var n = Backbone.View.extend({
        template: _.template(e),
        initialize: function() {
            this.messages = this.options.messages || []
        },
        events: {
            "click .txtCover": "txtFocus",
            "blur .txt,.txta": "txtBlur"
        },
        txtFocus: function(t) {
            var i = $(t.target);
            i.parent().find(".txt,.txta").focus(),
            i.parent().find(".txta").attr("rows", "2"),
            i.css("display", "none")
        },
        txtBlur: function(t) {
            var i = $(t.target);
            i.parent().find(".txtCover").css("display", "block"),
            i.hasClass("txta") && i.attr("rows", "1")
        },
        render: function() {
            this.$el.html(this.template({
                messages: this.messages
            }));
            var i = this.$(".photo-input");
            return this.photoUploaders = [],
            i.each(_(function(i, e) {
                var n = new t({
                    el: $(e).parent()
                }).render();
                this.photoUploaders.push(n)
            }).bind(this)),
            this
        },
        validate: function(t) {
            for (var i, e, n, s, o = this,
            a = this.messages,
            l = 0,
            u = a.length; u > l; l++) if (i = "message_" + l, e = t[i], n = a[l], _.isEmpty(e)) {
                if ("1" == n.required) return o.$el.find("#ipt-" + l).focus(),
                motify.log("image" == n.type ? "请上传 " + n.name + "。": "请填写 " + n.name + "。"),
                !1
            } else {
                if ("image" == n.type && (s = o.$el.find("#ipt-" + l).data("uploaded"), "false" == s || !s)) return motify.log("图片还在上传中，请稍等。。"),
                !1;
                if ("tel" == n.type && !Utils.validNumber(e)) return o.$el.find("#ipt-" + l).focus(),
                motify.log("请填写正确的" + n.name + "。"),
                !1;
                if ("email" == n.type && !Utils.validEmail(e)) return o.$el.find("#ipt-" + l).focus(),
                motify.log("请填写正确的" + n.name + "。"),
                !1
            }
            return ! 0
        },
        getData: function() {
            var t = {};
            return this.$("dl .js-message").each(function(i, e) {
                if ("file" == e.type) var n = $(e).data("value");
                t[e.name] = n || e.value || ""
            }),
            this.validate(t) ? t: null
        }
    });
    return n
}),
define("wap/components/list", [],
function() {
    var t = function() {},
    i = Backbone.View.extend({
        constructor: function(t) {
            t = t || {},
            this.options = t || {},
            this.options.itemOptions = this.options.itemOptions || {},
            this.setupListView(t),
            Backbone.View.call(this, t)
        },
        setupListView: function(i) {
            _.defaults(i, {
                itemView: Backbone.View,
                collection: new Backbone.Collection
            }),
            this.items = [],
            this.itemView = i.itemView,
            this.collection = i.collection,
            this.onAfterListChange = i.onAfterListChange || t,
            this.onAfterListLoad = i.onAfterListLoad || t,
            this.onEmptyList = i.onEmptyList || t,
            this.onItemClick = i.onItemClick || t,
            this.onViewItemAdded = i.onViewItemAdded || t,
            this.displaySize = i.displaySize || -1,
            this.emptyHTML = i.emptyHTML || "列表为空！"
        },
        _setupListeners: function() {
            this.listenTo(this.collection, "add", this.addSingleItem, this),
            this.listenTo(this.collection, "reset", this.addAll, this),
            this.listenTo(this.collection, "remove", this.onItemRemove, this),
            !!this.setupListeners && this.setupListeners()
        },
        render: function(t) {
            return this.displaySize = -1 == (t || {}).displaySize ? -1 : this.displaySize,
            this.addAll(),
            this
        },
        addAll: function() {
            this.removeAllItems(),
            this._setupListeners(),
            0 === this.collection.length ? this.onEmptyList() : this.collection.each(function(t) {
                this.addSingleItem(t)
            },
            this),
            this.onAfterListLoad({
                list: this.collection
            })
        },
        addSingleItem: function(t) {
            if (! (this.displaySize >= 0 && this.items.length >= this.displaySize)) {
                var i = new this.itemView(_.extend(this.options.itemOptions, {
                    model: t
                }));
                return this.items.push(i),
                this.addListItemListeners(i),
                i.render(),
                this.onViewItemAdded({
                    list: this.items,
                    viewItem: i
                }),
                (this.listEl || this.$el).append(i.el),
                i
            }
        },
        onItemRemove: function(t) {
            this.removeSingleItem(t),
            this.onAfterListChange()
        },
        removeSingleItem: function(t) {
            var i = this.getViewByModel(t);
            i && this.removeSingleView(i)
        },
        removeSingleView: function(t) {
            var i;
            this.stopListening(t),
            t && (this.stopListening(t.model), t.remove(), i = this.items.indexOf(t), this.items.splice(i, 1)),
            0 === this.collection.length && this.onEmptyList()
        },
        addListItemListeners: function(t) {
            var i = this;
            this.listenTo(t, "all",
            function() {
                var i = "item:" + arguments[0],
                e = _.toArray(arguments);
                e.splice(0, 1),
                e.unshift(i, t),
                this.trigger.apply(this, e),
                "item:click" == i && this.onItemClick()
            }),
            this.listenTo(t.model, "change",
            function() {
                i.onAfterListChange({
                    list: this.collection
                })
            })
        },
        getViewByModel: function(t) {
            return _.find(this.items,
            function(i) {
                return i.model === t
            })
        },
        getViewList: function() {
            return this.items
        },
        dispatchEventToAllView: function(t, i) {
            _.each(this.items,
            function(e) {
                e.trigger(t, i)
            })
        },
        removeAllItems: function() {
            this.collection.each(function(t) {
                this.removeSingleItem(t)
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
    return i
}),
define("text!wap/showcase/sku/templates/skuList.html", [],
function() {
    return '<dt class="model-title sku-sel-title">\n    <label><%= skuCollection.k %>：</label>\n</dt>\n<dd>\n    <ul class="model-list sku-sel-list"></ul>\n</dd>'
}),
define("wap/showcase/sku/views/sku_list", ["wap/components/list", "text!wap/showcase/sku/templates/skuList.html"],
function(t, i) {
    var e = Backbone.View.extend({
        tagName: "li",
        className: "btn sku-btn",
        template: _.template("<%= data.name %>"),
        initialize: function(t) {
            this.onItemClick = t.onItemClick ||
            function() {},
            this.listenTo(this, "active", this.onActive),
            this.listenTo(this, "enable", this.enableView),
            this.listenTo(this, "disable", this.disableView)
        },
        events: {
            click: "onClick"
        },
        onClick: function() {
            return this.$el.hasClass("unavailable") ? !1 : (this.toggle(), void this.onItemClick({
                v_id: this.model.id
            }))
        },
        onActive: function(t) {
            t.v_id !== this.model.id && this.deActive()
        },
        toggle: function() {
            this.$el.toggleClass("active"),
            this.isActived = !this.isActived
        },
        active: function() {
            this.$el.addClass("active"),
            this.isActived = !0
        },
        deActive: function() {
            this.$el.removeClass("active"),
            this.isActived = !1
        },
        disableView: function(t) {
            parseInt(this.model.get("id")) == parseInt(t.value) && (this.$el.addClass("unavailable"), this.enabled = !1)
        },
        enableView: function(t) { ! t || "all" !== t.value && parseInt(t.value) !== parseInt(this.model.get("id")) || (this.$el.removeClass("unavailable"), this.enabled = !0)
        },
        isEnabled: function() {
            return this.enabled
        },
        render: function() {
            return this.$el.html(this.template(_.extend({
                data: this.model.toJSON()
            },
            {}))),
            this.enabled = !0,
            this
        }
    }),
    n = Backbone.View.extend({
        tagName: "dl",
        template: _.template(i),
        initialize: function(t) {
            this.skuCollection = t.skuCollection,
            this.onSkuActived = t.onSkuActived ||
            function() {}
        },
        events: {},
        onItemClick: function(t) {
            this.skuListView.dispatchEventToAllView("active", t),
            this.onSkuActived(this.getActivedSku())
        },
        getActivedSku: function() {
            var t = this.skuListView.getViewList(),
            i = _.find(t, _(function(t) {
                return t.isActived
            }).bind(this));
            return i ? {
                k_id: this.skuCollection.k_id,
                k_s: this.skuCollection.k_s,
                v_id: i.model.id
            }: null
        },
        activeFirstSku: function() {
            if (this.skuListView.items) for (var t in this.skuListView.items) {
                var i = this.skuListView.items[t];
                if (i.isEnabled()) return void i.onClick()
            }
        },
        disableSkuItem: function(t) {
            this.skuListView.dispatchEventToAllView("disable", {
                value: t
            })
        },
        enableSkuItem: function(t) {
            this.skuListView.dispatchEventToAllView("enable", {
                value: t
            })
        },
        enalbeAllSkuItem: function() {
            this.skuListView.dispatchEventToAllView("enable", {
                value: "all"
            })
        },
        render: function() {
            return this.$el.html(this.template({
                skuCollection: this.skuCollection
            })),
            this.skuListView = new t({
                collection: this.skuCollection,
                el: this.$(".model-list"),
                itemView: e,
                itemOptions: {
                    onItemClick: _(this.onItemClick).bind(this)
                }
            }).render(),
            this
        }
    });
    return n
}),
define("wap/showcase/sku/views/sku_brain", [],
function() {
    var t = Backbone.View.extend({
        initialize: function(t) {
            this.collection = t.collection,
            Backbone.EventCenter.on("active", _(this.onSkuActived).bind(this))
        },
        onSkuActived: function(t) {
            var i = t.activedSkus,
            e = (t.clickedSku, ["s1", "s2", "s3"]);
            _.each(e, _(function(t) {
                var n = _.filter(i,
                function(i) {
                    return i.k_s !== t
                }),
                s = this.collection.filter(function(t) {
                    for (var i in n) {
                        var e = n[i];
                        if (t.get(e.k_s) !== e.v_id) return ! 1
                    }
                    return ! 0
                }),
                o = {};
                _.each(s,
                function(t) {
                    for (var i in e) {
                        var n = e[i],
                        s = t.get(n);
                        if (0 === parseInt(s)) return;
                        o[s] ? o[s].totalStock += parseInt(t.get("stock_num")) : o[s] = {
                            totalStock: parseInt(t.get("stock_num")),
                            k_s: n
                        }
                    }
                });
                for (var a in o) o[a].totalStock ? this.trigger("sku-comb:hasstock", {
                    v_id: a,
                    k_s: o[a].k_s
                }) : this.trigger("sku-comb:nostock", {
                    v_id: a,
                    k_s: o[a].k_s
                })
            }).bind(this))
        }
    });
    return t
}),
define("wap/showcase/sku/model", [],
function(t, i) {
    var e;
    return i = {},
    i.SkuModel = e = Backbone.Model.extend({}),
    i.SkuCollection = Backbone.Collection.extend({
        model: e
    }),
    i.SkuStockModel = Backbone.Model.extend({}),
    i.SkuStockCollection = Backbone.Collection.extend({}),
    i
}),
define("wap/showcase/sku/views/sku_selector", ["./sku_list", "./sku_brain", "../model"],
function(t, i, e) {
    var n = e.SkuStockCollection,
    s = e.SkuCollection,
    o = Backbone.View.extend({
        initialize: function(t) {
            var i = this;
            this.skuCollectionArray = [],
            this.sku = (t || {}).sku,
            i.sku.none_sku ? this.selectedSkuComb = {
                id: i.sku.collection_id,
                get: function(t) {
                    return "price" === t ? i.sku.collection_price || "": void 0
                }
            }: _.each(this.sku.tree, _(function(t) {
                var i = new s(t.v);
                i.count = t.count,
                i.k = t.k,
                i.k_id = t.k_id,
                i.k_s = t.k_s,
                this.skuCollectionArray.push(i)
            }).bind(this)),
            this.skuStockCollection = new n(this.sku.list),
            Backbone.EventCenter.on("enable", _(this.enalbeAllSkuItem).bind(this))
        },
        events: {},
        onSkuActived: function(t) {
            var i = [];
            _.each(this.skuListViews,
            function(t) {
                var e = t.getActivedSku();
                e && i.push(e)
            }),
            this.selectedSkuComb = this.getSelectedSkuComb(i),
            Backbone.EventCenter.trigger("sku:selected", {
                skuComb: this.selectedSkuComb
            }),
            Backbone.EventCenter.trigger("active", {
                activedSkus: i,
                clickedSku: t
            })
        },
        getSelectedSkuComb: function(t) {
            this.activedSkus = t;
            var i = {},
            e = null;
            return _.each(t,
            function(t) {
                i[t.k_s] = t.v_id
            }),
            t.length === _.size(this.skuListViews) && (e = this.skuStockCollection.find(function(t) {
                for (var e in i) if (t.get(e) !== i[e]) return ! 1;
                return ! 0
            })),
            e
        },
        diableSkuItem: function(t) {
            this.skuListViews[t.k_s].disableSkuItem(t.v_id)
        },
        enableSkuItem: function(t) {
            this.skuListViews[t.k_s].enableSkuItem(t.v_id)
        },
        enalbeAllSkuItem: function(t) { !! this.skuListViews[t.k_s] && this.skuListViews[t.k_s].enalbeAllSkuItem()
        },
        autoSelect: function() {
            this.skuListViews && _.each(this.skuListViews,
            function(t) {
                1 === t.skuCollection.length && t.activeFirstSku()
            })
        },
        getSelectedSku: function() {
            var t = _.pluck(this.sku.tree, "k_id"),
            i = this.sku,
            e = [];
            return this.selectedSkuComb ? {
                status: !0,
                sku: this.selectedSkuComb
            }: (_.each(this.activedSkus,
            function(i) {
                t = _.without(t, "" + i.k_id)
            }), _.each(t,
            function(t) {
                e.push(_.find(i.tree,
                function(i) {
                    return i.k_id === t
                }).k)
            }), {
                status: !1,
                errMsg: e.join(" 和 ")
            })
        },
        render: function() {
            return this.skuListViews = {},
            _.each(this.skuCollectionArray, _(function(i) {
                var e = new t({
                    skuCollection: i,
                    onSkuActived: _(this.onSkuActived).bind(this)
                });
                this.$el.append(e.render().el),
                this.skuListViews[i.k_s] = e
            }).bind(this)),
            this.skuBrain = new i({
                collection: this.skuStockCollection
            }),
            this.listenTo(this.skuBrain, "sku-comb:nostock", _(this.diableSkuItem).bind(this)),
            this.listenTo(this.skuBrain, "sku-comb:hasstock", _(this.enableSkuItem).bind(this)),
            this
        },
        clear: function() {
            return this.stopListening(),
            Backbone.EventCenter.off("enable"),
            this.remove(),
            null
        }
    });
    return o
}),
define("text!wap/showcase/sku/templates/stock.html", [],
function() {
    return '<dt class="model-title stock-label">\n    <label>剩余:</label>\n</dt>\n<dd class="stock-num">\n    <%= data.stock %>\n</dd>'
}),
define("wap/showcase/sku/views/sku_stock", ["text!wap/showcase/sku/templates/stock.html"],
function(t) {
    var i = Backbone.View.extend({
        template: _.template(t),
        initialize: function(t) {
            this.hide_stock = t.hide_stock
        },
        events: {},
        onClick: function() {},
        render: function(t) {
            return this.stock = this.stock || t.stock,
            !this.hide_stock && this.stock && this.$el.html(this.template({
                data: {
                    stock: this.stock
                }
            })),
            this
        },
        setNum: function(t) {
            this.stock = t,
            this.render({})
        }
    });
    return i
}),
define("text!wap/showcase/sku/templates/title.html", [],
function() {
    return "<div class=\"js-cancel\">\n    <div class=\"cancel-img\"></div>\n</div>\n<% if (config['picture'].length > 0){ %>\n    <div class=\"thumb\"><img src=\"<%=config['picture'][0] %>\" alt=\"\"></div>\n<% } %>\n<div class=\"goods-base-info clearfix\">\n    <h2 class=\"title\"><%=config['title'] %></h2>\n    <div class=\"goods-price clearfix\">\n    <% if(activity){ %>\n        <div class=\"current-price activity-price\">\n            <span class='price-name font-size-18'>￥</span><i class=\"js-goods-price price vertical-middle\"><%=activity['price'] %></i>\n            <span class=\"price-tag vertical-middle font-size-12\"><%=activity['price_title'] %></span>\n        </div>\n        <em class=\"old-price vertical-middle font-size-14 line-through\">价格：<%=sku['old_price'] && sku['old_price'] != '0.00' && sku['old_price'] != 0.00 ? sku['old_price'] : config['price'] %></em>\n    <% }else{ %>\n        <div class=\"current-price\">\n            <span class='price-name font-size-18'>￥</span><i class=\"js-goods-price price vertical-middle\"><%=sku['price'] && sku['price'] != '0.00' && sku['price'] != 0.00 ? sku['price'] : config['price'] %></i>\n        </div>\n    <% } %>\n    <% if(config['origin'] && config['origin'] !== '淘价：'){ %>\n        <div class=\"original-price vertical-middle font-size-14 line-through\">\n            <%=config['origin'] %>\n        </div>\n    <% } %>\n    </div>\n</div>"
}),
define("wap/showcase/sku/views/sku_title", ["text!wap/showcase/sku/templates/title.html"],
function(t) {
    var i = Backbone.View.extend({
        initialize: function(t) {
            this.difTitle = (t || {}).difTitle
        },
        template: _.template(t),
        changePrice: function(t) {
            this.price = t,
            this.$(".js-goods-price").html((t / 100).toFixed(2))
        },
        getPrice: function() {
            return this.price
        },
        render: function(t) {
            return this.difTitle && this.$el.html(this.template(t)),
            this
        }
    });
    return i
}),
window.Utils = window.Utils || {},
$.extend(window.Utils, {
    needConfirm: function(t, i, e) {
        var n = window.confirm(t);
        n ? i && "function" == typeof i && i.apply() : e && "function" == typeof e && e.apply()
    }
}),
define("wap/components/util/confirm",
function() {}),
window.Utils = window.Utils || {},
$.extend(window.Utils, {
    getParameterByName: function(t) {
        t = t.replace(/[[]/, "\\[").replace(/[]]/, "\\]");
        var i = "[\\?&]" + t + "=([^&#]*)",
        e = new RegExp(i),
        n = e.exec(window.location.search);
        return null === n ? "": decodeURIComponent(n[1].replace(/\+/g, " "))
    },
    removeParameter: function(t, i) {
        var e = t.split("?");
        if (e.length >= 2) {
            for (var n = encodeURIComponent(i) + "=", s = e[1].split(/[&;]/g), o = s.length; o-->0;) - 1 !== s[o].lastIndexOf(n, 0) && s.splice(o, 1);
            return t = e[0] + "?" + s.join("&")
        }
        return t
    },
    addParameter: function(t, i) {
        if (!t || 0 === t.length || 0 === $.trim(t).indexOf("javascript")) return "";
        var e = t.split("#");
        t = e[0];
        for (var n in i) if (i.hasOwnProperty(n)) {
            if ("" === $.trim(i[n])) continue;
            if (t.indexOf("?") < 0) t += "?" + $.trim(n) + "=" + $.trim(i[n]);
            else {
                var s = {},
                o = t.split("?");
                $.each(o[1].split("&"),
                function(t, i) {
                    var e, t, n;
                    t = i.indexOf("="),
                    e = i.slice(0, t),
                    n = i.slice(t + 1),
                    "" !== $.trim(n) && (s[e] = n)
                }),
                $.each(i,
                function(t, i) {
                    "" !== $.trim(i) && (s[t] = i)
                });
                var a = [];
                $.each(s,
                function(t, i) {
                    a.push(t + "=" + i)
                }),
                t = o[0] + "?" + a.join("&")
            }
        }
        return 2 === e.length && (t += "#" + e[1]),
        t
    }
}),
define("wap/components/util/args",
function() {}),
require(["wap/components/util/args", "wap/components/util/valid"],
function() {
    function t(i, e, n) {
        $(i).text("等待 " + e + "秒"),
        --e >= 0 ? u = setTimeout(function() {
            t(i, e, n)
        },
        1e3) : n()
    }
    function i() {
        clearTimeout(u),
        v.removeAttr("disabled"),
        v.text("获取验证码")
    }
    function e(i, e) {
        v.prop("disabled", !0),
        i = i || 0,
        e = e ||
        function() {},
        t(v, i, e)
    }
    function n() {
        var t = $.trim(w.val()),
        i = $.trim(g.val()),
        e = $.trim(b.val()),
        n = $(".js-login-mode").data("login-mode");
        "signup" === n && i && t ? p.removeAttr("disabled") : "signin" === n && i && t && e ? p.removeAttr("disabled") : p.attr("disabled", "disabled")
    }
    function s() {
        clearInterval(l)
    }
    function o(t) {
        return "" === t.phone ? (r.find('input[name="phone"]').focus(), motify.log("请填写您的手机号码"), !1) : window.Utils.validMobile(t.phone) ? "" === t.password ? (r.find('input[name="password"]').focus(), motify.log("麻烦输入一下您的密码吧"), !1) : t.password.length > 16 ? (r.find('input[name="password"]').focus(), motify.log("亲，密码太长了，怕您记不住，还是短一点啦"), !1) : "signin" !== t.mode || window.Utils.validPostalCode(t.code) ? !0 : (r.find('input[name="code"]').focus(), motify.log("请填写6位短信验证码"), !1) : (r.find('input[name="phone"]').focus(), motify.log("请填写11位手机号码"), !1)
    }
    var a = $(".js-modal-login");
    $.extend($.kdt, {
        login: function(t) {
            a.length <= 0 || (a.addClass("active"), $.isFunction(t) && ($.kdt._loginCB = t))
        }
    });
    var l, u, r = $(".js-login-form"),
    c = r.attr("action"),
    d = r.find(".js-login-pwd"),
    h = r.find(".js-login-title span"),
    m = r.find(".js-forget-password"),
    f = r.find(".js-login-mode"),
    p = r.find(".js-submit"),
    k = r.find(".js-auth-code-li"),
    v = r.find(".js-auth-code-btn"),
    g = r.find(".js-login-phone"),
    w = r.find(".js-login-pwd"),
    b = r.find(".js-auth-code");
    l = setInterval(function() {
        n()
    },
    1e3),
    r.on("keyup", ".js-login-phone, .js-login-pwd, .js-auth-code", n),
    r.on("blur", ".js-login-phone",
    function() {
        var t, i = $(this),
        e = $.trim(i.val()),
        n = m.attr("href");
        e = /^1\d{10}$/.test(e) ? e: "",
        t = /^1\d{10}$/.test(e) ? Utils.addParameter(n, {
            phone: e
        }) : Utils.removeParameter(n, "phone"),
        m.attr("href", t)
    }),
    r.on("click", ".js-login-cancel",
    function() {
        window.history.back()
    }),
    r.on("click", ".js-login-mode",
    function() {
        var t = $(this),
        i = t.data("login-mode");
        "signin" === i ? (h.html("请先登录账号"), d.attr("placeholder", "请填写您的密码"), k.addClass("auth-hide"), p.html("登录"), t.html("注册账号"), t.data("login-mode", "signup")) : (h.html("请先注册账号"), d.attr("placeholder", "请设置至少6位的密码"), k.removeClass("auth-hide"), p.html("立即注册"), t.html("已有账号"), t.data("login-mode", "signin")),
        n()
    }),
    v.click(function() {
        var t = $.trim(g.val());
        return t && window.Utils.validMobile(t) ? void $.ajax({
            url: window._global.url.wap + "/buyer/auth/verifycode.json",
            type: "GET",
            dataType: "json",
            data: {
                phone: t
            },
            beforeSend: function() {
                e(60,
                function() {
                    v.text("再次获取"),
                    v.prop("disabled", !1),
                    b.attr("placeholder", "没有收到验证码？")
                })
            },
            success: function(t) {
                0 === t.code || (i(), motify.log(t.msg))
            },
            error: function() {
                i(),
                motify.log("获取验证码失败，请稍后再试")
            },
            complete: function() {}
        }) : (motify.log("请填写正确手机号码"), void g.focus())
    }),
    r.on("submit",
    function(t) {
        t.preventDefault();
        var i = $.kdt.getFormData(r);
        return i.mode = f.data("login-mode"),
        o(i) ? (s(), void $.ajax({
            url: c,
            type: "GET",
            dataType: "jsonp",
            timeout: 15e3,
            data: i,
            beforeSend: function() {
                p.html("提交中..."),
                p.prop("disabled", !0)
            },
            success: function(t) {
                var i = t.code;
                if (0 === i) {
                    if ($.kdt._loginCB && $.kdt._loginCB(), a.removeClass("active"), window._global && window._global.redirect_url) {
                        var e = Utils.addParameter(window._global.redirect_url, {
                            kdt_sso_token: t.data.kdt_sso_token
                        });
                        window.location.assign(e)
                    } else window._global.kdt_sso_token = t.data.kdt_sso_token;
                    motify.clear()
                } else motify.log(t.msg)
            },
            error: function() {
                motify.log("出错啦，请重试")
            }
        }).always(function() {
            p.html("signin" === i.mode ? "立即注册": "登录"),
            p.prop("disabled", !1)
        })) : !1
    })
}),
define("wap/components/util/login",
function() {}),
define("text!wap/showcase/sku/templates/buyBtn.html", [],
function() {
    return '<% if(!isMultiBtn) {%>\n    <a href="javascript:;" class="js-confirm-it btn btn-block btn-orange-dark butn-buy">下一步</a>\n<% } else { \n    if(!isCartBtnHide) {%>\n        <a href="javascript:;" class="js-mutiBtn-confirm confirm btn btn-block btn-orange-dark butn-buy half-button">立即购买</a>\n        <a href="javascript:;" class="js-mutiBtn-confirm cart btn btn-block btn-orange-dark butn-buy half-button">加入购物车</a>\n    <% } else {%>\n        <a href="javascript:;" class="js-mutiBtn-confirm confirm btn btn-block btn-orange-dark butn-buy">下一步</a> \n    <%}\n}%>'
}),
require(["wap/components/quantity", "wap/showcase/sku/views/message", "wap/showcase/sku/views/sku_selector", "wap/showcase/sku/views/sku_stock", "wap/showcase/sku/views/sku_title", "wap/components/util/confirm", "wap/components/util/args", "wap/components/util/login", "text!wap/showcase/sku/templates/buyBtn.html"],
function(t, i, e, n, s, o, a, l, u) {
    Backbone.EventCenter = _.extend({},
    Backbone.Events);
    var r = Backbone.View.extend({
        initialize: function(t) {
            t = t || {},
            this.skuViewConfig = t.skuViewConfig || {},
            this.isMultiBtn = t.isMultiBtn,
            this.logURL = t.logURL,
            this.baseUrl = t.baseUrl,
            this.need_ajax_login = t.need_ajax_login,
            this.wxpay_env = t.wxpay_env,
            this.isCartBtnHide = t.isCartBtnHide,
            this.quantityReadOnly = t.quantityReadOnly,
            this.isPriceCanChanged = !0,
            this.onHide = t.onHide ||
            function() {},
            this.viewTop = this.skuViewConfig.top,
            delete this.skuViewConfig.top,
            this.deviceView = {
                width: $(document).width(),
                height: $(document).height()
            },
            this.bodyPos = $("html").css("position"),
            this.nActionBtnTemplate = _.template(u),
            this.nPrice = this.$(".js-goods-price"),
            Backbone.EventCenter = _.extend({},
            Backbone.Events),
            Backbone.EventCenter.on("sku:selected", _(this.onSelectChange).bind(this))
        },
        events: {
            "click .js-confirm-it": "doConfirmClicked",
            "click .js-cancel": "onCancelClicked",
            "click .js-mutiBtn-confirm": "onMultiBtnClick"
        },
        onSelectChange: function(t) {
            var i = t.skuComb;
            if (i) {
                var e = parseInt(i.get("stock_num"));
                this.stockView && this.stockView.setNum(e),
                this.quantityView && (this.quantityLimit = this.getLimitNum(this.quota, this.quota_used, e), this.quantityView.setLimitNum(this.quantityLimit.limitNum)),
                this.setPrice(i.get("price")),
                this.height(),
                this.$el.height(this.skuViewHeight)
            } else;
        },
        setPrice: function(t) {
            this.isPriceCanChanged && this.skuTitleView.changePrice(t)
        },
        disablePriceChange: function() {
            this.isPriceCanChanged = !1
        },
        onMultiBtnClick: function(t) {
            t = t || window.event;
            var i = t.target || t.srcElement;
            this.isAddCart = $(i).hasClass("cart"),
            this.doConfirmClicked(t)
        },
        doConfirmClicked: function(t) {
            var i = $(t.target);
            if (!i.attr("disabled")) {
                var e = this,
                n = this.isAddCart ? "add-cart": "buy",
                s = $.Deferred().done(function() {
                    e.doSubmit({
                        buyType: n,
                        needLogin: "buy" === n
                    })
                });
                return s && "function" == typeof s.isResolved && !s.isResolved() ? (motify.log("亲，请稍等。"), !1) : void(t ? $.kdt.log(Utils.addParameter(this.logURL, {
                    spm: $.kdt.spm(),
                    fm: "click",
                    referer_url: encodeURIComponent(document.referrer),
                    title: this.isAddCart ? "加入购物车": "立即购买"
                }), s) : s.resolve())
            }
        },
        doSubmit: function() {
            var t, i = function(t) {
                var i = this,
                e = this.baseUrl;
                $.ajax({
                    url: e + "/trade/order/book.jsonp",
                    type: "GET",
                    dataType: "jsonp",
                    cache: !1,
                    timeout: 15e3,
                    data: t,
                    beforeSend: function() {
                        i.ajaxing = !0,
                        i.nConfirmBtn.html("提交中..."),
                        i.doDisabled(i.nConfirmBtn, !0)
                    },
                    success: function(e) {
                        i.ajaxing = !1,
                        i.submitSuccess(e, t)
                    },
                    error: function(t, e, n) {
                        i.ajaxing = !1,
                        i.submitError(t, e, n)
                    }
                })
            },
            e = function(t) {
                var i = this,
                e = this.baseUrl;
                motify.clear(),
                $.ajax({
                    url: e + "/trade/cart/goods.jsonp",
                    type: "GET",
                    dataType: "jsonp",
                    cache: !1,
                    timeout: 15e3,
                    data: t,
                    beforeSend: function() {
                        i.ajaxing = !0
                    },
                    success: function(t) {
                        0 === +t.code ? (motify.log("已成功添加到购物车"), i.isMultiBtn ? i.onHide() : app.trigger("goods")) : motify.log(t.msg)
                    },
                    error: function() {
                        motify.log("添加到购物车失败", 0)
                    },
                    complete: function() {
                        i.ajaxing = !1,
                        window.refreshCartIcon && window.refreshCartIcon()
                    }
                })
            },
            n = {
                "add-cart": e,
                buy: i
            },
            s = function(i, e) {
                if (e.buyType || "function" == typeof t) {
                    var n = this;
                    e.needLogin && this.need_ajax_login ? $.kdt.login(function() {
                        n.need_ajax_login = !1,
                        t.call(n, i)
                    }) : t.call(n, i)
                }
            },
            o = function() {
                var t, i, e = this.skuSelectorView.getSelectedSku(),
                n = this.messageView.getData();
                if (!e.status) return motify.log("请选择 " + e.errMsg),
                !1;
                if (t = e.sku, !n) return ! 1;
                if (i = this.quantityView.getNum(), !i) return ! 1;
                var s = {
                    goods_id: this.goods_id,
                    postage: this.postage || 0,
                    num: i,
                    activity_alias: this.activity_alias,
                    activity_id: this.activity_id,
                    activity_type: this.activity_type,
                    sku_id: t.id,
                    price: parseInt(this.skuTitleView.getPrice()) || t.get("price")
                },
                o = (window.Utils.getParameterByName ||
                function() {})("from");
                return o && o.length > 0 && (s.from = o),
                s.use_wxpay = this.wxpay_env ? 1 : 0,
                _(s).extend(n),
                s
            };
            return function(i) {
                var e = this;
                if (t = n[i.buyType], !i.notCheckBtnDisabled && this.isDisabled(this.nConfirmBtn)) return ! 1;
                if (e.ajaxing) motify.log("提交订单中，请勿重复提交。");
                else {
                    var a = o.call(e);
                    if (!a) return ! 1;
                    if (a.num < 100 && "2083866" === e.goods_id) return motify.log("定制版100盒起售"),
                    e.quantityView.updateNum(100),
                    !1;
                    e.isGift && (a.order_type = 1),
                    i.accept_price && (a.accept_price = i.accept_price),
                    s.call(e, a, i)
                }
            }
        } (),
        doWait: function(t) {
            t > 0 ? (this.nConfirmBtn.attr("disabled", !0), this.nConfirmBtn.text(this.isMultiBtn ? this.nConfirmBtn.data("text") + "(" + t + ")": "正在排队购买，请等待 " + t + " 秒后再提交"), this.waitId = setTimeout(_(this.doWait).bind(this, t - 1), 1e3)) : (this.nConfirmBtn.removeAttr("disabled"), this.nConfirmBtn.text(this.nConfirmBtn.data("text")), this.waitId = !1)
        },
        submitSuccess: function() {
            var t = {
                11011 : !0,
                11014 : !0,
                11012 : !0,
                11013 : !0
            },
            i = function(t) {
                motify.log(t),
                this.nConfirmBtn.html(t),
                this.doDisabled(view.nConfirmBtn, !0)
            };
            return function(e) {
                var n = this,
                s = e.code;
                if (0 === s) {
                    var o = e.data.order_id;
                    if (isNaN(o) || !e.data.trade_confirm_url) return motify.log("订单生成失败，请联系管理员。"),
                    n.nConfirmBtn.html("确认提交"),
                    n.nConfirmBtn.removeAttr("disabled"),
                    !1;
                    window.location.href = e.data.trade_confirm_url
                } else 12500 === s ? (this.isMultiBtn && motify.log("网络繁忙，请稍后再试～"), n.doWait( + e.data.wait)) : 11010 === s ? Utils.needConfirm(e.msg + "（新价格：" + (parseInt(e.data.cur_price) / 100).toFixed(2) + "元）",
                function() {
                    var t = n.isAddCart ? "add-cart": "buy";
                    n.doSubmit({
                        buyType: t,
                        needLogin: "buy" === t,
                        accept_price: 1,
                        notCheckBtnDisabled: !0
                    })
                },
                function() {
                    window.location.reload()
                }) : 13021 === s ? window.location.href = e.data.redirectUrl: t[s + ""] ? i.call(n, e.msg) : 11020 === s ? (motify.log(e.msg + "正在刷新页面..."), window.location.reload()) : 11021 === s ? (motify.log(e.msg), n.nConfirmBtn.html("确认提交"), n.nConfirmBtn.removeAttr("disabled"), n.quantityView.updateNum(1)) : (motify.log(e.msg), n.nConfirmBtn.html("确认提交"), n.nConfirmBtn.removeAttr("disabled"))
            }
        } (),
        submitError: function() {
            motify.log("购买失败，请重试。"),
            this.doDisabled(this.nConfirmBtn, !1),
            this.nConfirmBtn.html("提交订单")
        },
        onCancelClicked: function() {
            this.onHide()
        },
        hide: function() {
            this.waitId && (clearTimeout(this.waitId), this.waitId = !1, this.nConfirmBtn.removeAttr("disabled")),
            this.isMultiBtn && (this.$(".js-sku-views").empty(), this.$(".layout-content").height("auto"), this.skuSelectorView = this.skuSelectorView.clear())
        },
        height: function() {
            this.$(".layout-content").height("auto");
            var t = $(window).height() - this.viewTop,
            i = this.$(".layout-title").outerHeight(),
            e = t - i,
            n = this.$(".layout-content").height();
            return this.skuConH = n,
            this.skuConWinH = e,
            this.skuConHeight = e > n ? n: e,
            this.skuViewHeight = this.skuConHeight + i,
            this.$(".layout-content").height(this.skuConHeight),
            this.skuViewHeight
        },
        onAfterPopupShow: function() {
            this.quantityView.validateNum()
        },
        displaySku: function(t) {
            return this.render(t || {})
        },
        getLimitNum: function(t, i, e) {
            var n, s, o = t - i;
            return e > o && 0 !== t ? (n = +o, s = "quota") : (n = +e, s = "stock"),
            {
                limitNum: n,
                limitType: s
            }
        },
        doDisabled: function(t, i) {
            i ? this.nConfirmBtn.attr("disabled", !0) : this.nConfirmBtn.removeAttr("disabled")
        },
        isDisabled: function(t) {
            return t.attr("disabled")
        },
        render: function(o) {
            o = o || {},
            this.sku = o.sku || {},
            this.goods_id = o.goods_id,
            this.postage = o.postage,
            this.activity_alias = o.activity_alias || "",
            this.activity_id = o.activity_id || 0,
            this.activity_type = o.activity_type || 0,
            this.quota = o.quota,
            this.quota_used = o.quota_used,
            this.stockNum = this.sku.stock_num,
            this.difTitle = o.difTitle,
            this.isGift = o.isGift,
            this.isAddCart = o.isAddCart,
            this.onAfterHideFunc = o.onAfterHideFunc ||
            function() {},
            this.$el.append($("#tmpl-sku").html());
            var a = this.$(".layout-title");
            this.skuTitleView = new s({
                difTitle: this.difTitle,
                el: a
            }).render({
                config: {
                    title: o.title,
                    picture: o.picture || [],
                    price: o.price,
                    origin: o.origin
                },
                auction: o.auction,
                activity: o.activity,
                sku: o.sku
            }),
            this.$(".js-sku-views").empty();
            var l = this.$(".js-sku-views");
            this.skuSelectorView = new e({
                sku: this.sku
            }).render(),
            l.append(this.skuSelectorView.$el.children());
            var u = this.quantityLimit = this.getLimitNum(this.quota, this.quota_used, this.stockNum),
            r = $('<dl><dt class="model-title sku-num">                <label>数量</label></dt><dd></dd></dl>');
            this.quantityView = new t({
                readonly: this.quantityReadOnly,
                num: 1,
                tagName: "dl",
                className: "quantity-view",
                limitNum: +u.limitNum,
                leastNum: "2083866" === this.goods_id ? 100 : 1,
                onBelowLeast: _(function() {
                    "2083866" === this.goods_id && motify.log("定制版100盒起售")
                }).bind(this),
                onOverLimit: _(function() {
                    var t = this.quota_used > 0 ? "<br>您之前已经购买过" + this.quota_used + "件": "";
                    return "quota" === u.limitType ? void motify.log("该商品每人限购" + this.quota + "件" + t) : "stock" === u.limitType ? void motify.log("就这么几件啦～") : void 0
                }).bind(this)
            }).render(),
            l.append(r),
            this.quantityView.$el.appendTo(r.find("dd"));
            this.quota > 0 ? "（限购&nbsp;" + this.quota + "&nbsp;件）": "";
            if (this.stockView = new n({
                el: $('<div class="stock"></div>'),
                hide_stock: this.sku.hide_stock
            }).render({
                stock: this.stockNum
            }), this.quantityView.$el.append(this.stockView.$el), this.messageView = new i({
                messages: this.sku.messages
            }), l.append(this.messageView.render().el), this.$(".confirm-action").html(this.nActionBtnTemplate({
                isMultiBtn: this.isMultiBtn,
                isCartBtnHide: this.isCartBtnHide
            })), this.nConfirmBtn = this.$(0 === this.$(".js-confirm-it").length ? ".js-mutiBtn-confirm.confirm": ".js-confirm-it"), 0 === u.limitNum && this.nConfirmBtn.attr("disabled", !0), this.isMultiBtn) this.nConfirmBtn.data("text", "立即购买");
            else {
                var c = this.isAddCart ? "加入购物车": "下一步";
                this.nConfirmBtn.text(c),
                this.nConfirmBtn.data("text", c)
            }
            return this.$el.css(this.skuViewConfig),
            this.skuSelectorView.autoSelect(),
            this
        }
    });
    return window.BuyView = r
}),
define("main",
function() {});