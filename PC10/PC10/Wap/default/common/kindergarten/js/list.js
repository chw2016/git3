/**
 * Created by Sean on 14-2-27.
 */
var List = function () {
    var _orgId = 0;
    var _schoolId = 0;
    var _moduleId = 0;
    var _module = '';
    var _page = 1;
    var _callback = null;

    var handleData = function(obj){
        var url = '';
        if(_module.indexOf('/') >=0){
            url = _module;
        }
        else{
            url = '/'+_module+'/data';
        }
        jq.ajax({
            type: "GET",
            dataType: "text",
            url: url,
            data: {oid:_orgId,sid:_schoolId,mid:_moduleId,page:_page},
            success: function (result){
                if(_page == 1){
                    jq('#list').html(result);
                }
                else{
                    jq('#list').append('<hr>'+result);
                }
                if(_callback){
                    _callback();
                }
                var more = jq('#more').val();
                if(more == true){
                    _page++;
                    handleRefresh();
                    if(obj != null){
                        obj.afterDataLoading();
                    }
                }
                else{
                    if(obj != null){
                        obj.disable();
                    }
                    else{
                        jq('.ui-refresh-down').hide();
                    }
                }

                jq('#more').remove();

            }
        });
    }

    var handleRefresh = function(){
        $('.ui-refresh').refresh({
            load: function (dir, type) {
                handleData(this);
            }
        });
    }

    return {
        init: function (orgId,schoolId,moduleId,module,callback) {
            _orgId = parseInt(orgId);
            _schoolId = parseInt(schoolId);
            _moduleId = parseInt(moduleId);
            _module = module;
            _callback = callback;
            handleData(null);
        }
    };
}();