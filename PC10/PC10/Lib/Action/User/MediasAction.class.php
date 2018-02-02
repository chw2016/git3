<?php
/**
 *  、易优惠（企业会员后台）
 **/
class MediasAction extends TableAction
{
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

    /**
     *  Token
     **/
    //private $_sToken = null;
    private $_iaid = null;
    public function _initialize()
    {
        //$this->_sToken = $_SESSION['token'];
        $this->_iaid = !empty($_GET['branch_id'])? $_GET['branch_id']:$_GET['aid'];
        parent::_initialize();


    }

    //一级
    protected function setHeader()
    {
        return array(
            array(
                'name' => '任务管理管理',
                'url' => U('Medias/index', array('token' => $this->_sToken,'aid'=>$this->_iaid))
            ),
            array(
                'name' => '积分流水记录',
                'url' => U('Medias/scoreinfo', array('token' => $this->_sToken,'aid'=>$this->_iaid))
            ),
            /*array(
                'name' => '高速快览管理',
                'url' => U('Medias/quickfacts', array('token' => $this->_sToken))
            ),*/

        );
    }

    //显示
    public function index()
    {
        $this->table(
            array(
                // 'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Medias/index', array('token' => $this->_sToken,'aid'=>$this->_iaid)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name' => '添加任务',//2级
                        'url' => U('Medias/set_task', array('token' => $_SESSION['token'],'aid'=>$this->_iaid))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '任务名称', '所属任务大类','是否开启', '操作'
                ),
                'List_Opt' => array(

                   /* array(
                        'name' => '详情',
                        'url' => U('Medias/seeuser', array('token' => $_SESSION['token'],'aid'=>$this->_iaid))
                    ),*/

                    array(
                        'name' => '编辑',
                        'url' => U('Medias/set_task', array('token' => $_SESSION['token'],'aid'=>$this->_iaid))
                    ),
                    array(
                        'type' => 1,
                        'name' => '删除',
                        'url' => U('Medias/task_delete', array('token' => $_SESSION['token'],'aid'=>$this->_iaid))
                    ),
                ),
            ),
            M('Media_task')->where(array('token' => $_SESSION['token'],'qid'=>$this->_iaid))->count(),
            M('Media_task')->field('id,title,cid,status')->where(array('token' => $_SESSION['token'],'qid'=>$this->_iaid))->order("id"),
         array($this,'indexinfo')
        );
    }
    public function indexinfo($data){
        foreach($data as $k=>$val){
            if($val['status'] ==1){
                $data[$k]['status'] = '开启';
            }else{
                $data[$k]['status'] = '关闭';
            }
            $iTem = M('Media_classification')->where(array('id'=>$val['cid']))->find();
            $data[$k]['cid'] = $iTem['cname'];
        }
        return $data;
    }

public function set_task(){
    $taskmodel = M('Media_task');
    $labelmodel = M('Media_label');
    $classificationmodel = M('Media_classification');
    if(IS_AJAX){
        $variable['id'] = $_POST['id']?$_POST['id']:'';
        $_POST['token'] = $this->_sToken;

        if($variable['id']){
            $savetasks = $taskmodel->where(array('token'=>$this->token,'id'=>$variable['id']))->save($_POST);
            if($savetasks){
                $this->success('编辑成功！',U(MODULE_NAME.'/index',array('token'=>session('token'),'aid'=>$this->_iaid)));
            }else{
                $this->error('编辑失败！',U(MODULE_NAME.'/set_task',array('token'=>session('token'),'id'=>$variable['id'],'aid'=>$this->_iaid)));
            }
        }else{
            $_POST['addtime'] = time();
            $_POST['token'] = $this->_sToken;
            $_POST['date'] = date('Y-m-d');
            $addtasks = $taskmodel->data($_POST)->add();
            if($addtasks){
                $this->success('添加成功！',U(MODULE_NAME.'/index',array('token'=>session('token'),'aid'=>$this->_iaid)));
            }else{
                $this->error('添加失败！',U(MODULE_NAME.'/set_task',array('token'=>session('token'),'aid'=>$this->_iaid)));
            }

        }
    }else{
        $tid = $_GET['id']?$_GET['id']:'';
        $productmodel = M('Product_new');
        if($tid){
            $key = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $this->assign('data',$key);
            $task = $taskmodel->where(array('token'=>$this->token,'id'=>$tid))->find();
            $classification = $classificationmodel->where(array('token'=>$this->token))->select();
            $label = $labelmodel->where(array('token'=>$this->token))->select();
            $product = $productmodel->where(array('token'=>$this->token))->order('time desc')->select();
            $aTask = $taskmodel->field('id,title')->where(array('token'=>$this->token,'pid'=>array('neq','')))->order('addtime desc')->select();
            //print_r($aTask);exit;
            $this->assign(array(
                'product'=>$product,
                'task'=>$task,
                'label'=>$label,
                'tid'=>$tid,
                'aTask'=>$aTask,
                'classification'=>$classification
            ));
            $this->UDisplay('set_task');
        }else{
            $key = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
            $this->assign('data',$key);
            $classification = $classificationmodel->where(array('token'=>$this->token))->select();
            $label = $labelmodel->where(array('token'=>$this->token))->select();
            $aTask = $taskmodel->field('id,title')->where(array('token'=>$this->token,'pid'=>array('NEQ','')))->order('addtime desc')->select();
            $product = $productmodel->where(array('token'=>$this->token))->order('time desc')->select();
            $this->assign('label',$label);
            $this->assign('aTask',$aTask);
            $this->assign('product',$product);
            $this->assign('tid',$tid);
            $this->assign('classification',$classification);
            $this->UDisplay('set_task');
        }
    }
}
    public function task_delete(){
        $this->del('Media_task');
    }


    //显示
    public function scoreinfo()
    {
        $aScore = M('Media_enterprise')->where(array('id'=>$this->_iaid))->find();
        $this->table(
            array(
                // 'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Medias/scoreinfo', array('token' => $this->_sToken,'aid'=>$this->_iaid)),//栏目样式
               /* 'Head_Opt' => array(
                    array(
                        'name' => '添加任务',//2级
                        'url' => U('Medias/set_task', array('token' => $_SESSION['token'],'aid'=>$this->_iaid))
                    )
                ),*/
                'tips' => array(//3级
                    '您现在的积分总余额为：'.$aScore['score'].'分'
                ),
                'Table_Header' => array(//4级
                    'ID', '记录产生方', '积分','类型', '产生时间'
                ),

            ),
            M('Media_enterprise_score')->where(array('token' => $_SESSION['token'],'qid'=>$this->_iaid))->count(),
            M('Media_enterprise_score')->field('id,openid,score,type,add_time')->where(array('token' => $_SESSION['token'],'qid'=>$this->_iaid))->order("id"),
            array($this,'scoreinfoinfo')
        );
    }

    public function scoreinfoinfo($data){
      //  P($data);
        foreach($data as $k=>$val){
            switch($val['type']){
                case 0: $data[$k]['type'] = '减少'; break;
                case 1: $data[$k]['type'] = '增加'; break;
                default: $data[$k]['type'] = '其它';
            }
            if($val['openid']){
                $user = M('Wxuser')->where(array('token'=>$this->_sToken))->find();
                $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$val['openid']))->find();
                $data[$k]['openid'] = $users['nickname'];
            }else{
                $data[$k]['openid'] = '总部';
            }
        }
        return $data;
    }


}