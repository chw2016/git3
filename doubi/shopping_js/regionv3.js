/* 
author: Rex
create: 2015-12-25 
description: 省市区四级联动，精确到街道
*/
$(function() {
    var citySelector = {
        provinceData:null,
        cityData:null,
        districtData:null,
        streetData: null,
        province:null,
        city:null,
        district:null,
        preProvince:null,
        preCity:null,
        preDistrict:null,
        preStreet:null,
        provinceStr:null,
        initProvince:"<option value='0'>省份</option>",
        initCity:"<option value='0'>城市</option>",
        initDistrict:"<option value='0'>区县</option>",
        initStreet:"<option value='0'>街道</option>",

        init:function(){
            var self = this;
            var arr = [];
            self.province = $("#province");
            self.city = $("#city");
            self.district = $("#district");
            self.street = $("#street");
            self.preProvince = $("#preProvince");
            self.preCity = $("#preCity");
            self.preDistrict = $("#preDistrict");
            self.preStreet = $("#preStreet");
            self.provinceStr = $("#province_str");
            //获取需要获取的数据文件
            if(self.province.length > 0){
                arr.push({
                    'name':'provinceData',
                    'url':'http://corev2.influx.io/resource/regiondatav3/province/data.js'
                });
            }
            if(self.city.length > 0){
                arr.push({
                    'name':'cityData',
                    'url':'http://corev2.influx.io/resource/regiondatav3/city/data.js'
                });
            }
            if(self.district.length > 0){
                arr.push({
                    'name':'districtData',
                    'url':'http://corev2.influx.io/resource/regiondatav3/district/data.js'
                });
            }
            if(self.street.length > 0 || 1){//没有区的数据用街道的替代
                arr.push({
                    'name':'streetData',
                    'url':'http://corev2.influx.io/resource/regiondatav3/street/data.js'
                });
            }
            self.getData(arr, function(){
                self.addListener();
            });
        },

        getData:function(arr, callback){
            var self = this;
            var count = 0;
            for(var key in arr){
                (function(obj){
                    $.get(obj.url, function(data){
                        self[obj.name] = data;
                        count++;
                        //全部加载完成进行事件绑定
                        if(count >= arr.length){
                            callback();
                        }
                    }, 'json');
                })(arr[key])
            }
        },

        addListener:function(){
            var self = this;
            self.province.on("change",function(){
                self.loadOption(self.cityData,self.preCity,self.city,self.province,2,self.initCity);
            });
            self.city.on("change",function(){
                self.loadOption(self.districtData,self.preDistrict,self.district,self.city,4,self.initDistrict);
            });
            self.district.on("change",function(){
                self.loadOption(self.streetData,self.preStreet,self.street,self.district, 6, self.initStreet);
            });
            
            self.loadOption(self.provinceData,self.preProvince,self.province,null,0,self.initProvince);
        },

        loadOption:function(data, preobj, targetobj, parentobj, comparelen, initoption){
            var self = this;
            var r = data;
            var t = ''; // t: html容器 
            var s; // s: 选中标识 
            var pre; // pre: 初始值 
            if (preobj === undefined||preobj.val()===undefined) {
                pre = 0;
            } else {
                pre = preobj.val();
            }
            
            if(r != null){
                for (var i = 0; i < r.length; i++) {
                    s = '';
                    if (comparelen === 0) {
                        if (pre !== "" && pre !== 0 && (r[i].name.substring(0, pre.length) === pre || r[i].name == pre.substring(0, r[i].name.length))) { 
                            s = ' selected=\"selected\" ';
                            pre = '';
                        }
                        t += '<option value=' + r[i].code + s + '>' + r[i].name + '</option>';
                    } else {
                        var p = parentobj.val();
                        
                        if (p !== "" && p.substring(0, comparelen) === r[i].code.substring(0, comparelen)) {
                            if (pre !== "" && pre !== 0 && (r[i].name.substring(0, pre.length) === pre || r[i].name == pre.substring(0, r[i].name.length))){
                                s = ' selected=\"selected\" ';
                                pre = '';
                            }
                            t += '<option value=' + r[i].code + s + '>' + r[i].name + '</option>';
                        }
                    }
                }
                if(t == '' && targetobj.attr('id') == 'district'){
                    r = self.streetData;
                    for (var i = 0; i < r.length; i++) {
                        s = '';
                        var p = parentobj.val();
                        if (p !== "" && p.substring(0, 4) === r[i].code.substring(0, 4)) {
                            if (pre !== "" && pre !== 0 && (r[i].name.substring(0, pre.length) === pre || r[i].name == pre.substring(0, r[i].name.length))){
                                s = ' selected=\"selected\" ';
                                pre = '';
                            }
                            t += '<option value=' + r[i].code + s + '>' + r[i].name + '</option>';
                        }
                    }
                }
                if (initoption !== '') {
                    targetobj.html(initoption + t);
                } else {
                    targetobj.html(t);
                }
                
                targetobj.change();
            }
        }
    }
    citySelector.init();
});