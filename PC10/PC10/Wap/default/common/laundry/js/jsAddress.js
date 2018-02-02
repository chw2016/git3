
var initArr = ['--城市--','--区域--'];
var provinceArr = ['北京', '上海（试运营，多建议）', '深圳'];
var cityArr = [];
cityArr[0] = ['朝阳','海淀','东城','西城','丰台','石景山','回龙观','天通苑','通州','天竺','亦庄'];
cityArr[1] = ['内环'];
cityArr[2] = ['华侨城'];
function selectinit(p,c,province,city){
    $('#' + province).append('<option value="' + initArr[0] + '">' + initArr[0] + '</option>');
    $('#' + city).append('<option value="' + initArr[1] + '">' + initArr[1] + '</option>');
    $('#' + province).change(function(){selectcity(province,city);});
    var k = '';
    $.each(provinceArr,function(n,value){
         if(p==value){
            $('#' + province).append('<option value="' + value + '" selected="true" >' + value + '</option>');
            k = n;
         }else{
            $('#' + province).append('<option value="' + value + '">' + value + '</option>');
         }
    });
    if(c!=''){
        $('#' + city).empty();
        $.each(cityArr[k],function(n,value){
            if(value == c){
                $('#' + city).append('<option value="' + value + '"  selected="true" >' + value + '</option>');
            }else{
                $('#' + city).append('<option value="' + value + '">' + value + '</option>');
            }
        });
    }
}
function selectcity(province,city){
    $('#' + city).empty();
    if($('#' + province).get(0).selectedIndex-1==-1)$('#' + city).append('<option value="' + initArr[1] + '">' + initArr[1] + '</option>');
    $.each(cityArr[$('#' + province).get(0).selectedIndex-1],function(n,value){
        $('#' + city).append('<option value="' + value + '">' + value + '</option>');
    });
}