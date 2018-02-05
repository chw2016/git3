/*百度地图*/
(function(){
    window.BMap_loadScriptTime = (new Date).getTime();
    document.write('<script type="text/javascript" src="http://api.map.baidu.com/getscript?v=2.0&ak=2WQAlmlNeRT29pY8vTqCN7kO&services=&t=20150605180935"></script>');
})();

function getCity(callback){
    var geolocation = new BMap.Geolocation(),
        geoc = new BMap.Geocoder();
    geolocation.getCurrentPosition(function(r){
       if(this.getStatus() == BMAP_STATUS_SUCCESS){
           var pt = r.point;
           geoc.getLocation(pt, function(rs){
               var addComp = rs.addressComponents;
               if(typeof callback === "function"){
                   callback(addComp,pt);
               }
           /*alert(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);*/
           });

       }
       else {
           alert('failed'+this.getStatus());
       }
    },{enableHighAccuracy: true});
}

