<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南,危昌祥(由你定后台)
 **/
class GjlogAction extends BaseAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/gjlog';
    public function _initialize()
    {
        $this->autoShare = true;       /*分享限制*/
        parent::_initialize();
    }

    public function strqie($k,$data){     /*祭仪*/
        $info = explode($k,$data);
        array_pop($info);
        return $info;
    }

                /*日历页*/
    public function index(){
           /* $tip_id = $_GET['id'];*/

            $blorrt = array_keys(Arr::changeIndex(M('Gjlog_info')
                ->where(array(
                    'token'=>"$this->token",
                    'openid'=>"$this->openid"
                ))
                ->field('data_tima')
                ->select(), 'data_tima'));
             $token = $_GET['token'];

             $time= $yj->yj_addtime=date('Y-n-d');
             $blot = M('Gjlog_tip')->where(array('token'=>"$this->token",'openid'=>"$this->openid"))->select();
             $jieq = M('Jq_info')->where(array('date'=>array('egt',$time)))->find();     /*节气*/
             $img = M('Imag')->where(array('app'=>'Gjlog'))->select();    /*轮播图*/
                $asdsa = strtotime($jieq['date']);
                $times = strtotime($time);
                $khpot = $asdsa - $times;
                $days = $khpot/86400;           /*算出时间*/
                $ieu = getDataInfo($time);
                 $ji = $ieu['suit'];         /*祭仪*/
                $jii = $this->strqie('.',$ji);
                $j = $output = array_slice($jii,0,3);   /*限制数组显示个数*/
                 $yi = $ieu['avoid'];

                 $yii = $this->strqie('.',$yi);
                  $output = array_slice($yii,0,3);   /*限制数组显示个数*/
                 $this->assign('yi',$j);
                  $this->assign('ji',$output);
                  $this->assign('pdl',$blorrt);
                  $this->assign('im',$img);
                  $this->assign('jt',$days);
                  $this->assign('jq',$jieq);
                  $this->assign('user', $blot);
		  $this->autoShare = true;
                  $this->UDisplay('index');
    }

    /*填写日志页*/
    public function loges(){
                    /*这是跳到填写页*/
        if (empty($_GET['id'])) {
                    $token = $this->token;
                    $openid = $this->openid;
                 $time = $yj->yj_addtime = date('Y-m-d');
                $jieq = M('Jq_info')->where(array('date' => array('egt', $time)))->find();     /*节气*/
                $maleigebaz = M('Gjlog_info')->where(array('data_tima' =>array('eq',$time),'token'=>$token,'openid'=>$openid))->order('id desc')->find();
             $this->assign('mabi',$maleigebaz);
             $this->assign('jq', $jieq);

        }else {
                    /*这是跳到修改页*/
            $list = M('Gjlog_info')
                ->where(array(
                    'id'=>$_GET['id']
                ))->find();
            $token = $_GET['token'];
            $time= $yj->yj_addtime=date('Y-m-d');
            $blot = M('Gjlog_tip')->where(array('token'=>"$token",'openid'=>"$this->openid",'id'=>$_GET['id']))->find();
            $jieq = M('Jq_info')->where(array('date'=>array('egt',$time)))->find();     /*节气*/
            $asdsa = strtotime($jieq['date']);
            $times = strtotime($time);
            $khpot = $asdsa - $times;
            $days = $khpot/86400;           /*算出时间*/
            $ieu = getDataInfo('2015-1-10');
            $ji = $ieu['suit'];         /*祭仪*/
            $jii = $this->strqie('.',$ji);
            $yi = $ieu['avoid'];
            $yii = $this->strqie('.',$yi);
            $this->assign('yi',$yii);
            $this->assign('ji',$jii);
            $this->assign('jt',$days);
            $this->assign('jq',$jieq);
            $this->assign('user', $blot);
            $this->assign('list',$list);
         }
                      /*给出一个时间*/
                if($_GET['datetime']){
                    $this->assign('datetime',$_GET['datetime']);
                }
        $this->UDisplay('loges');
    }
                /*填写日志接收方法*/
    public function ajaxlogs(){
                /*日志详情页*/
        if(IS_AJAX){
                // P($_POST);exit;
            $info_id = $_GET['id'];
            if(!$info_id) {
                $_POST['add_time'] = time();
                $_POST['weekday'] = date('w', $_POST['add_time']);
                $_POST['token'] = $this->token;
                $_POST['openid'] = $this->openid;

                if (empty($_GET['datetime'])) {
                   $_POST['data_tima'] = date('Y-m-d');
                }else{
                   $_POST['data_tima']=$_GET['datetime'];
                 }
                //$_POST['data_tima'] = $_GET['datetime'];
                if( M('Gjlog_info')->add($_POST)){
                    $this->success('保存成功！',U('Gjlog/loglist',array('token'=>$this->token,'openid'=>$this->openid,'datetime'=>$_POST['data_tima'])));
                }else{
                    $this->error('保存失败！');

                }
            }else{
                if(M('Gjlog_info')->where(array('id'=>$info_id))->find()){
                    if(M('Gjlog_info')->where(array('id'=>$info_id))->save($_POST)){
                        $this->success('修改成功！',U('Gjlog/loginfo',array('token'=>$this->token,'openid'=>$this->openid,'data_time'=>$_POST['data_tima'],'id'=>$info_id)));
                    }else{
                        $this->error('修改失败！');
                    }
                }else{
                    $this->error('非法操作！');
                }
            }
       }
    }
     /*日志记录列表页*/
    public function loglist(){
                  /*跳到可修改*/
     $list = M('Gjlog_info')
        ->where(array(
            'token' => $this->token,
            'openid' => $this->openid,
            'data_tima' => $_GET['datetime']
        ))->order('id desc')->select();
         /* $ui=strtotime($yj->yj_addtime=date('Y-m-d'));*/
             $ui =strtotime($_GET['dang']);
             $ge =strtotime($_GET['datetime']);
         if($ui == $ge){
            $url = C('site_url') . 'index.php?g=Wap&m=Gjlog&a=loges&token=' . $this->token . '&openid=' . $this->openid . '&datetime=' . $_GET['datetime'] . '&dianji='.$ge;
            $this->redirect($url);
        }else{
             if (empty($list)) {
                 $url = C('site_url') . 'index.php?g=Wap&m=Gjlog&a=loges&token=' . $this->token . '&openid=' . $this->openid . '&datetime=' . $_GET['datetime'] . '&dianji='.$ge;
                 $dji = $_GET['datetime'];
                 $this->assign('datetime',$dji);
                 $this->redirect($url);
             }else{
                 $this->assign(array(
                     'list' => $list
                 ));
             }
         }
	    $this->autoShare = true;
        $this->UDisplay('loglist');
    }


         /*日志详情页*/
    public function loginfo(){
        $list = M('Gjlog_info')
            ->where(array(
             'id'=>$_GET['id']
            ))->find();
             $token = $list['token'];
        if($_GET['share']==1){
            $jiet = C('site_url');
            $this->assign('CC',$jiet);
             $this->assign('open',1);
        }
             $this->assign('list',$list);
	         $this->autoShare = false;
             $this->UDisplay('loginfo');
    }


      public function loglistf(){
          if (IS_AJAX) {
              $ui = $_POST['datetime'];
              $lit = M('Gjlog_info')
                  ->where(array(
                     'data_tima' => $ui))->order('id')->select();
              $this->success('正在查询' . $ui . '的记录', U('Gjlog/loglistf', array('token' => $this->token, 'openid' => $this->openid, 'datetime' => $ui)));
          }
             $list = M('Gjlog_info')
              ->where(array(
                  'token' => $this->token,
                  'openid' => $this->openid,
                  'data_tima' => $_GET['datetime']
              ))->select();
          if (!empty($list)) {
              $this->assign('list', $list);
          } else {
              $this->assign('meiy', '因为你的偷懒了，所以没有这天的记录哟！');
          }

          $this->UDisplay('loglist');
      }

                /*这是天气的*/
       public function tianqi(){

           $this->UDisplay('tianqi');
       }
            /*这是节气的*/
        public function jieqi(){
            $te= $yj->yj_addtime=date('Y');
            $jieud = M('Jq_info')->where(array('date'=>array('like',"$te%")))->select();    /* 全部节气*/
            $this->assign('quanbujq',$jieud);
            $this->UDisplay('jieqi');
        }


    /*图片导出页连接*/

    /*图片导出页*/
    public function daochu(){
        $id=$_GET['id'];
        $li = M('Gjlog_info')->where(array('id'=>$id))->find();     /*这是查他的openid*/
        $openid = $li['openid'];
        $user = M('Gjlog_info')->where(array('openid'=>$openid))->order('data_tima')->limit(40)->select();
        $this->assign('openid',$openid);
        $this->assign('list',$user);
        $this->UDisplay('daochu');
    }
    /*日志导出方法*/
    public function daodddchu(){
        if ($_GET['id']) {
            $id = $_GET['id'];
            $usser = M('Gjlog_info')->field('id,temperature,weather,content,address,name,data_tima')
                ->where(array('id' => $id))->select();
            $jiet = C('site_url');
            foreach ($usser as $k => $v) {
                $usser[$k]['url'] = "$jiet/index.php?g=Wap&m=Gjlog&a=daochu&&id=" . $v['id'];
            }

            Excel::arr2ExcelDownload($usser,
                array('ID', '气温', '天气','内容' ,'地址', '用户名', '添加时间',
                    '连接地址'), '田园导出图');
        }
        if($_GET['openid']) {
            $user = M('Gjlog_info')->field('id,openid,temperature,weather,content,address,name,data_tima')
                ->where(array('openid' =>$_GET['openid']))->select();
            $jiet = C('site_url');
            $openid = $this->openid;
            foreach ($user as $k => $v) {
                $user[$k]['url'] = "$jiet/index.php?g=Wap&m=Gjlog&a=daochu&&id=" . $v['id'];
            }
             Excel::arr2ExcelDownload($user,
                array('ID', 'openid', '气温', '天气','内容', '地址', '用户名', '添加时间',
                    '连接地址'), '田园导出图');
        }
         $this->UDisplay('daochu');
     }

        public function qubuyongh(){
             $oeuoe =  M('Gjlog_info')->field('id,openid,temperature,weather,content,address,name,data_tima')->select();
            $jiet = C('site_url');
            foreach($oeuoe as $k => $v){
                $oeuoe[$k]['url'] = "$jiet/index.php?g=Wap&m=Gjlog&a=daochu&&id=".$v['id'];
            }
            Excel::arr2ExcelDownload($oeuoe,
                array('ID','openid', '气温', '天气','内容', '地址', '用户名', '添加时间',
                    '连接地址'), '田园导出图');
          }

}
?>
