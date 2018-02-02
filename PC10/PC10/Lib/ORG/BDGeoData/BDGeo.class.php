<?php
/*
 * 百度热点地图
   http://developer.baidu.com/map/index.php?title=lbscloud/api/geodata
 */
class BDGeo
{
    public function __construct($token, $sName)
    {
        $this->token = $token;
        $this->name  = $sName;
        $AK = M('Geo')->where(array('token' => $token))->find();
        if (!$AK) {
            return false;
        }
        $this->ak    = $AK['ak'];
    }

    //创建表（create geotable）接口
    public function getGEOTableID(array $aParam = array())
    {
        $GeoName = M('Geo_table')->where(array(
            'token'    => $this->token,
            'geo_name' => $this->name
        ))->find();
        if ($GeoName) {
            $this->geotable_id = $GeoName['geotable_id'];
            return true;
        }
        $sGeoTableUrl = 'http://api.map.baidu.com/geodata/v3/geotable/create';
        $aRet = json_decode(httpMethod($sGeoTableUrl, array_merge(array(
            'name'          => $this->name,
            'geotype'       => 1,
            'is_published'  => 1,
            'timestamp'     => time(),
            'ak'            => $this->ak,
            'sn'            => ''
        )), $aParam), true);
        if ($aRet['status'] == 0) {
            $this->geotable_id = $aRet['id'];
            M('Geo_table')->data(array(
                'token' => $this->token,
                'geo_name' => $this->name,
                'geotable_id'   => $this->geotable_id,
                'message'  => $aRet['message'],
                'add_time' => date('Y-m-d H:i:s')
            ))->add();
            return true;
        }else{
            return false;
        }
    }

    //新建点1
    public function addPOI($sTitle, $lat, $lng, array $aParam = array())
    {
        $this->getGEOTableID();
        $sGeoPOIUrl = 'http://api.map.baidu.com/geodata/v3/poi/create';
        $aRet = json_decode(httpMethod($sGeoPOIUrl, $aData=array_merge(array(
            'title'         => $sTitle,
            'address'       => '',
            'tags'          => '',
            'latitude'      => $lat,
            'longitude'     => $lng,
            'coord_type'    => 3,
            'geotable_id'   => $this->geotable_id,
            'ak'            => $this->ak,
            'sn'            => '',
            '{column key}'  => ''
        ), $aParam)), true);
		$aData['poi_id'] = $aRet['id'];
        if ($aRet['status'] == 0) {
            M('Geo_poi')->data($aData)->add();
        }
        if ($aRet['status'] == 0) {
            return $aRet['id'];
        }
        return false;
    }

    public function updatePOI($iPoiID, $sTitle, $sAddress, $lat, $lng, $aParam = array())
    {
        $this->getGEOTableID();
        $sGeoPOIUrl = 'http://api.map.baidu.com/geodata/v3/poi/update';
        $aRet = json_decode(httpMethod($sGeoPOIUrl, $aData=array_merge(array(
            'id'            => $iPoiID,
            'title'         => $sTitle,
            'address'       => $sAddress,
            'latitude'      => $lat,
            'longitude'     => $lng,
            'coord_type'    => 3,
            'geotable_id'   => $this->geotable_id,
            'ak'            => $this->ak,
            'sn'            => '',
            '{column key}'  => ''
        ), $aParam)), true);
        unset($aData['poi_id']); unset($aData['geotable_id']);
        if ($aRet['status'] == 0) {
            M('Geo_poi')->where(array(
                'poi_id'      => $iPoiID,
                'geotable_id' => $this->geotable_id,
            ))->data($aData)->save();
        }
        return !$aRet['status'];
    }

    public function deletePOI($iPoiID)
    {
        $this->getGEOTableID();
        $sGeoPOIUrl = 'http://api.map.baidu.com/geodata/v3/poi/delete';
        $aRet = json_decode(httpMethod($sGeoPOIUrl, array(
            'id'            => $iPoiID,
            'geotable_id'   => $this->geotable_id,
            'ak'            => $this->ak,
            'sn'            => '',
            '{column key}'  => ''
        )), true);
        if ($aRet['status'] == 0) {
            M('Geo_poi')->where(array(
                'poi_id'      => $iPoiID,
                'geotable_id' => $this->geotable_id,
            ))->delete();
        }
        return !$aRet['status'];
    }

}
