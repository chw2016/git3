<?php
/*
 * 台铃
 */
class TailgAction extends BaseAction{
    /*
     * Tpl Dir
     */
    protected $_sTplBaseDir = 'Wap/default/tailg';

    protected function _initialize(){
        parent::_initialize();
        $this->GEO       = M('Tailg_geo');
        $this->username  = $_REQUEST['username'] ? $_REQUEST['username'] : $_COOKIE['user'];
        $this->UserInfo  = M('Tailg_user')->where(array(
            'username' => $this->username)
        )->find();
        $this->imei      = Arr::get($this->UserInfo, 'IMEI');
        $this->assign('username', $this->username);
    }

    function faster(){
        $type = $_REQUEST['type'];
        $imei = '';
        switch ($type) {
            case 1://一键启动
                $imei = 'A100###';
                break;
            case 2://解锁
                $imei = 'A103###';
                break;
            case 3://落锁
                $imei = 'A104###';
                break;
            case 4://静音设防
                $imei = 'A107###';
                break;
            case 5://打开座桶
                $imei = 'A106###';
                break;
        }
        //找imei
        if (!$this->imei) {//没有登录
            $this->redirect(U('index', array('token' => $_REQUEST['token'])));
        }else{
            $this->autoShare = true;
            $Ret = $this->doApi(sprintf('#wxcode%s#%s', $this->imei , $imei));
            $ret = json_decode($Ret, true);
            $this->assign('ret', (int)!$ret['code']);
            $this->UDisplay('fater');
        }
    }
    /*
        * 接口统一调用方法
        */
    public function beforeApi(){
        Vendor('Socket.socket');
        $this->Socket = new Socket('121.40.73.130', 7000);
        if(!$this->imei) {
            exit($this->encode(array('code'=>-1,'msg'=>'非法操作')));
        }
    }

    /*
     * 调用API操作
     */
    public function doApi($command){
        $this->beforeApi();
        $sReturn = $this->Socket->send($command);
        $aReturn = json_decode($sReturn, true);
        if (strlen($sReturn) == 0 or $aReturn['status'] != 0) {
            $iStatus = isset($aReturn['status']) ? $aReturn['status'] : -1;
            return $this->encode(array('code'=>$iStatus,'msg'=>'操作失败'));
        }else{
            return $this->encode(array('code'=>0,'msg'=>'成功','msg'=>$sReturn));
        }
    }

    /*应用页面*/
    public function index(){
        //$Cash = new cash($this->token, 19, $this->openid);
        //p($Cash->cash_info());

        if (!$this->username) {
            return $this->redirect(U('Tailg/login', array('token' => $this->token, 'openid' => $this->openid)));
        }
        $username = $this->username;
        $oUserModel = M('Tailg_user');
        $aUser = $oUserModel->where(array('username'=>$username,'token'=>$this->token))->find();
        $sPhone = explode(',',$aUser['phone']);
        $iNum = count($sPhone);
        //P($iNum);exit;
        if(IS_AJAX){
            $phone = $_POST['phone'];
            $data['phone'] = $aUser['phone'].','.$phone;
            //P($data);exit;
            $iTem = $oUserModel->where(array('username'=>$username,'token'=>$this->token))->save($data);
            if($iTem){
                echo $this->encode(array('status'=>1,'infos'=>'绑定成功'));exit;
            }else{
                echo $this->encode(array('status'=>2,'infos'=>'绑定失败'));exit;
            }
        }
        $this->assign(array(
           'iNum'=>$iNum,
           'sphone'=>$aUser['phone']
        ));
        $this->UDisplay('index');
    }

    /*接收前端指令*/
    public function ajaximei(){
        if(IS_AJAX){
            if(strpos($this->imei, 'A111') !== false){
                $this->delCache($sIMEI, 'A111');
            }
            exit($this->doApi(sprintf('#wxcode%s%s', $this->imei , $_POST['aimei'])));
        }
        //P($aIMEI);
        return $aIMEI;
    }

    /*接收前端指令*/
    public function info(){
        $aRecord = $this->GEO->where(array(
            'token' => $this->token,
            'imei'  => $this->imei,
        ))->order('create_time desc')->limit(1)->find();
        if ($aRecord) {
            $aRecord['add_time'] = strtotime($aRecord['add_time']);
            exit(json_encode(array(
                'code'      => 0,
                'aData'     => $aRecord,
                'geo'       => (int)$aRecord['longitude'],
                'status'    => self::getStatus($aRecord['status']),
                'is_online' => (int)(time() - $aRecord['create_time'] <= 600)
            )));
        }else{
            exit(json_encode(array('code' => -1, 'aData' => $aRecord)));
        }

    }

    /*
     *  获取设备的状态
     *  @param $status 16进制
            字符对应16进制表示:
             [左四位：
            Bit7：保留
            Bit6：保留
            Bit5：保留
            Bit4：解锁上电 1:ON  0:OFF
            ]
            [右四位:
            Bit3：靠近解锁 1:ON  0:OFF
            Bit2：靠近启动 1:ON  0:OFF
            Bit1-Bit0：00:撤防 01:静音设防 10:非静音设防 11:备用]
            0000 0000
     */
    public static function getStatus($status)
    {
        $m2 = str_pad(base_convert($status, 16, 2), 8, '0', STR_PAD_LEFT);//2进制
        return array(
            'cf'    => substr($m2, 6, 2),
            'qd'    => substr($m2, 5, 1),
            'js'    => substr($m2, 4, 1),
            'jssd'  => substr($m2, 3, 1)
        );
    }

    /*
     * 获取最近一次的地理位置
     */
    public function getGEO()
    {
        if($aGEO = $this->GEO->where(array(
            'imei'      => $this->imei,
            'longitude' => array('neq', '')
        ))->order('id desc')->find()){
            $aRet = transgps($aGEO['latitude'], $aGEO['longitude'], true);
            exit(json_encode(array('code'=> 0, 'aData' => array(
                'longitude' => $aRet[1],
                'latitude' => $aRet[0]
            ))));
        }else{
            exit(json_encode(array('code'=> -1)));
        }
    }

    /*
     *  获取结果
     */
    public function getResult()
    {
        if (IS_AJAX) {
            set_time_limit(0);
            $mCallBack = false;
            $aPOST = explode('#', $_REQUEST['aimei']);
            $sType = $aPOST[1];
            $mCallBack = $this->getCache($this->imei, $sType);
            if ('A111' == $sType && $mCallBack) {
                $ld = explode(',', $mCallBack);
                $ld = transgps($ld[1], $ld[0], true);
                $mCallBack = array(
                    'longitude' => $ld[1],
                    'latitude'  => $ld[0]
                );
            }
            echo ($this->encode(array(
                'code'  => (int)($mCallBack === false),
                'aData' => $mCallBack,
                'msg'   => ''
            )));
            die;
        }
    }

    /*
     *  设置缓存
     */
    public function getCache($sIMEI, $sType)
    {
        $sKey  = md5(sprintf('%s%s', $sIMEI, $sType));
        $mCache = false;
        if($mCache = cache($sKey)){
            cache($sKey, null);
        }
        return $mCache;
    }
    /*
     *  设置缓存
     */
    public function delCache($sIMEI, $sType)
    {
        $sKey  = md5(sprintf('%s%s', $sIMEI, $sType));
        cache($sKey, null);
    }


    /*
     * 修改密码
     */
    public function editpasswd(){
        if (IS_AJAX) {
            $sName = $_POST['userName'];
            $sPass = $_POST['password'];
            $sNewPass = $_POST['newpassword'];
            $sreNewPass = $_POST['renewpassword'];
            $aUser = M('Tailg_user')->where(array('username'=>$sName,'password'=>$sPass))->find();
            if ($aUser) {
                if(M('Tailg_user')->where(array('username'=>$sName,'password'=>$sPass))->save(array('password' => $sNewPass))){
                    $this->success('成功');
                }else{
                    $this->error('用户名或者密码错误');
                };
            }else{
                $this->error('用户名或者密码错误');
            }
        }else{
            $this->UDisplay('editpasswd');
        }
    }





    /*
     * 登陆页
     */
    public function login(){
        if (IS_AJAX) {
            $sName = $_POST['userName'];
            $sPass = $_POST['password'];
            $aUser = M('Tailg_user')->where(array('username'=>$sName,'password'=>$sPass))->find();
            if ($aUser) {
                //session('user',$aUser);
                setcookie("user", $aUser['username'], time()+3600*24*30);
                $this->success('登陆成功',U('Tailg/index',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error('登陆失败');
            }
        }else{
            //session_destroy();
            if ($_GET['username']) {
                $this->assign('username', $_GET['username']);
            }
            if ($_REQUEST['logout'] == 1) {
                setcookie("user","",-1);
                $this->UDisplay('login');
                return;
            }
            if ($this->username) {
                $this->redirect(U('Tailg/index', array('token' => $this->token, 'openid' => $this->openid)));
            }else{
                $this->UDisplay('login');
            }
        }
    }


    /*
     * 地图展示页
     * */
    public function map(){

        if (!$this->username) {
            return $this->redirect(U('Tailg/login', array('token' => $this->token, 'openid' => $this->openid)));
        }

        if (IS_AJAX) {

            $iPage = max(1, $_REQUEST['page']);
            $iNum  = 100;
            $_GET['beginTime'] = empty($_GET['beginTime']) ? date('Y-m-d H:i', time() - 3600) : $_GET['beginTime'];
            $_GET['endTime'] = empty($_GET['endTime']) ? date('Y-m-d H:i', time()) : $_GET['endTime'];
            $aData = M('Tailg_geo')
                ->field('id, longitude, latitude')
                ->where(array(
                    'token'    => $this->token,
                    'imei'     => $this->imei,
                    'longitude'=> array('neq', ''),
                    'latitude' => array('neq', ''),
                    'add_time' => array('between', $_GET['beginTime'].','.$_GET['endTime'])
                ))
                ->limit(($iPage-1) * $iNum.','.$iNum)
                ->select();

            if (count($aData) > 0) {
                $aGEO = self::transferGPS($aData);
                if (count($aGEO) > 0) {
                    exit(json_encode(array('code' => 0, 'data' => $aGEO)));
                }else{
                    exit(json_encode(array('code' => -1)));
                }
            }else{
                exit(json_encode(array('code' => -1)));
            }
        }

        $this->assign('param', $_GET);
        $this->assign('imei', $this->imei);
        $this->UDisplay('map');
    }

    public static function transfer($aData=array(), $Latlng)
    {
        foreach ($aData as $k => $v) {
            //$aRet = transgps($v['latitude'], $v['longitude'], true);
            $aRet = $Latlng->transform2Mars($v['latitude'], $v['longitude']);
            $aData[$k]['latitude']  = $aRet[0];
            $aData[$k]['longitude'] = $aRet[1];
        }
        return $aData;
    }


    public static function transferGPS($aData=array())
    {
        foreach ($aData as $k => $v) {
            $aRet = transgps($v['latitude'], $v['longitude'], true);
            $aData[$k]['latitude']  = $aRet[0];
            $aData[$k]['longitude'] = $aRet[1];
        }
        return $aData;
    }
}
