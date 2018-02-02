<?php
class PaipaiAction extends CliAction {

    public $sToken = '7a22d71ec7b216144ac970cfaaef0084';

    /*
     * 删除10天以前的数据
     */
    public function deleteBeforeTen()
    {
    	return;
        $sDate = date('Y-m-d',strtotime('-10 days'));
        set_time_limit(0);
        while ($iRow=M('Intel_geo')->where(array(
            'date'  => array('lt', $sDate)
            //'token' => $this->sToken
        ))->limit(1000)->delete() > 0) {
            echo date('Y-m-d H:i:s') . "成功:" . $iRow . "\n";
        }
    }

    public function loadCar()
    {
        $File = fopen('car.txt', 'r');
        $pid  = 0;
        set_time_limit(0);
        while(!feof($File)){
            $Line = trim(fgets($File));
            if (strlen($Line) == 0) {
                continue;
            }
            if (strpos($Line, 'damon') !== false) {
                $Line = str_replace('damon', '', $Line);
                $pid = M('Intel_cartype')->data(array(
                    'token'  => '7a22d71ec7b216144ac970cfaaef0084',
                    'pid'   => 0,
                    'name'  => $Line
                ))->add();
                echo "添加$Line成功";
                continue;
            }
            M('Intel_cartype')->data(array(
                'token'  => '7a22d71ec7b216144ac970cfaaef0084',
                'pid'   => $pid,
                'name'  => $Line
            ))->add();
            echo "添加$Line成功";
        }
    }

    /*
     *  找出离线的用户
     */
    public function index()
    {
        $oGeo = M('Intel_geo');
        $oUser = M('Intelligent_devices_users');
        $dNow = date('Y-m-d H:i:s');
        $dOne = date('Y-m-d H:i:s',time() - 60);//1分钟前
        $dTow = date('Y-m-d H:i:s',time() - 120);//2分钟前
        $isOhas = $oGeo->field('imei')
            ->where(array(
                'add_time',array('between',array($dOne, $dNow)),
                'token' => $this->sToken
            ))
            ->distinct(true)
            ->select();
        $isOhas = array_keys(Arr::changeIndex(array_values($isOhas), 'imei'));
        $isThas = $oGeo->field('imei')
            ->where(array(
                'add_time',array('between',array($dTow, $dNow)),
                'token' => $this->sToken
            ))
            ->distinct(true)
            ->select();
        $isThas = array_keys(Arr::changeIndex(array_values($isThas), 'imei'));
        $result = array_diff($isThas,$isOhas);
        //p($isThas);
        //die;
        foreach($result as $val){
            $user = $oUser->where(array('imei'=>$val))->find();
            msg($this->sToken,$user['openid'],$val.'已经离线');
            echo '发送离线消息:'.$user['openid'].$val.'已经离线';
        }
    }
}
