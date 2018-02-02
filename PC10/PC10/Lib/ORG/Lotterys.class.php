<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * 抽奖类型汇总
 * Date: 2014/12/19
 * Time: 14:09
 */
class Lotterys{
    //判断活动有无过期或者是否开始,判断活动是否存在,对用户访问进行一系列判断
    public function check($lid,$openid,$token){
        $agent = $_SERVER['HTTP_USER_AGENT'];
//		if(!strpos($agent,"MicroMessenger")) {
//            return false;
//		}
        $linfo=$linfo=M("lottery")->field("status,statdate,enddate,info,txt,sttxt,starpicurl")->where(array("id"=>$lid))->find();
        if(!M("wxuser")->where(array("token"=>$token))->find() || !M("wxusers")->where(array('openid'=>$openid))->find() || !M("lottery")->where(array('id'=>$lid))->find()){
            return false;
        }
        if($linfo['status']==0 || $linfo['status']==2 || time()<=$linfo['statdate'] || time()>=$linfo['enddate']){
            return false;
        }
        return true;
    }

    //随机抽奖，数据为二维数组
    public function raffles($gift = array()) {
        foreach ($gift as $k => $v) {
            $data[$v['id']] = $v['prob'];
        }
        $rid = $this->get_rand($data);
        foreach ($gift as $k => $v) {
            if ($rid == $v['id']) {
                $res['gift'] = $v['gname'];
            } else {
                $res['no'][] = $v['gname'];
            }
        }
        return $res;
    }

    //获取中奖概率
    public function get_rand($data = array()) {
        $result = "";
        $sum = array_sum($data); //获取概率总和
        foreach ($data as $k => $v) {
            $num = rand(1, $sum);
            if ($num <= $v) {
                $result = $k;
                break;
            } else {
                $sum-=$v;
            }
        }
        unset($data);
        return $result;
    }
}