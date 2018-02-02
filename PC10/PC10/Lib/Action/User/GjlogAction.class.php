<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/9/15
 * Time: 8:52
 */
class GjlogAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

    /**
     *  Token
     **/
    //private $_sToken = null;

    /**
     *  UID
     **/
    //private $_iUID = null;//
    public function _initialize()
    {
        parent::_initialize();
       // $this->pz	   = D('common_cs');
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '日历提示管理',
                'url'  => U('Gjlog/tip', array('token' => $this->_sToken))
            ),
            array(
                'name' => '用户日志管理',
                'url'  => U('Gjlog/rzg', array('token' => $this->_sToken))
            ),
            array(
                'name' => '首页图片管理',
                'url'  => U('Gjlog/img', array('token' => $this->_sToken))
            ),
            array(
                'name' => '导出全部用户',
                'url'  => U('Wap/Gjlog/qubuyongh', array('token' => $this->_sToken))
            ),

        );
    }


    public function tip(){

        $this->table(
            array(
                'HeadHover' => U('Gjlog/tip', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加提示',//2级
                        'url'    => U('Gjlog/add_tip',array('token'=>$_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '提示语', '时间','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '编辑',
                        'url'  => U('Gjlog/save_tip',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Gjlog/delete_tip',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('Gjlog_tip')->where(array('token'=>$_SESSION['token']))->count(),
            M('Gjlog_tip')->field('id,tip_info,add_time')->where(array('token'=>$_SESSION['token']))->order("add_time desc")
        // array($this,'abc')
        );
    }




    public function set_tip($aaa){
        $this->$aaa('Gjlog_tip',array(
            array('title'=>"提示语",'type'=>"longinput",'name'=>"tip_info",'value'=>'tip_info','msg'=>'请填提示语'),
        ),U('Gjlog/tip',array('token'=>$_SESSION['token'])),array($this,'tipinfo'));
    }

    public function tipinfo($data){
        $data['token'] = $this->_sToken;
        if(!$_GET['id']){
            $data['add_time'] = date('Y-m-d H:i:s');
        }
        return $data;
    }
    public function add_tip(){
        $this->set_tip(add);
    }

    public function save_tip(){
        $this->set_tip(edit);
    }

    public function delete_tip(){
        $this->del('Gjlog_tip');
    }


    /*这里是用户管理*/

    public function rzg(){

        $this->table(
            array(
                'HeadHover' => U('Gjlog/rzg', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(
                   /* array(
                        'name'   => '添加提示',//2级
                        'url'    => U('Gjlog/add_rzg',array('token'=>$_SESSION['token']))
                    )*/
                ),

                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','姓名','电话','记录时间','天气','气温','操作'
                ),
                'List_Opt' => array(
                    array(

                        'name' => '分级查看',
                        'tkey' =>'openid',
                        'tval'=>'openid',
                        'url'  => U('Gjlog/xiangqing')
                    ),
					array(
                        'name' => '全部图片',
                        'url'  => U('Wap/Gjlog/daochu')
                    ),
                 ),
            ),
            M('Gjlog_info')->where(array('token'=>$_SESSION['token']))->count(),
            $lie =  M('Gjlog_info')->field('id,openid,name,phone,add_time,weather,temperature')->
						where(array('token'=>$_SESSION['token']))->order("add_time desc")->group('openid'),
         array($this,'indexinfo')
        );

    }



			/*这是二级查询*/

	 public function xiangqing(){
         $id=$_GET['id'];
         $li = M('Gjlog_info')->where(array('id'=>$id))->find();     /*这是查他的openid*/
         $openid = $li['openid'];
        $this->table(
            array(
                'HeadHover' => U('Gjlog/rzg', array('token' => $this->_sToken)), //栏目样式
                'Head_Opt' => array(
                   /* array(
                        'name'   => '添加提示',//2级
                        'url'    => U('Gjlog/add_rzg',array('token'=>$_SESSION['token']))
                    )*/
                ),

                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称','姓名','电话','记录时间','天气','气温','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '详情查看',
                        'url'  => U('Gjlog/xiang_cha',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Gjlog/delete_gnfo',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('Gjlog_info')->where(array('token'=>$_SESSION['token']))->count(),

						

            M('Gjlog_info')->field('id,openid,name,phone,add_time,weather,temperature')->
						where(array('token'=>$_SESSION['token'],'openid'=>$openid))->order("add_time desc"),
         array($this,'indexinfo')
        );
    }

    /*获取微信相关信息*/
    function wxinfo($openid){
        $wxuser = M('Wxuser')
            ->where(array('token'=>$this->_sToken))
            ->find();
        $wxusers = M('Wxusers')
            ->where(array(
                'uid'=>$wxuser['id'],
                'openid'=>$openid
            ))->find();
        return $wxusers;
    }
    function indexinfo($data){
        foreach($data as $key=>$val){
            $wxinfo = $this->wxinfo($val['openid']);
            $data[$key]['openid'] = $wxinfo['nickname'];
            $data[$key]['add_time'] = date('Y-m-d H;i',$val['add_time']);
        }
        return $data;
    }




    /*日志详情*/
    public function xiang_cha(){
        $user_id = $_GET['id'];
      /*  $user = M('Gjlog_info')->where(array('id'=>"$user_id"))->select();*/
        $user = M('Gjlog_info')->where(array('id'=>"$user_id"))->order('content')->limit(40)->select();
        $this->assign('gjl',$user);
        $this->UDisplay('xiang_cha');
    }

    public function delete_gnfo(){
            $this->del('Gjlog_info');
    }

      /*首页图片管理*/

    public function img(){
        $oImgModel = M('Imag');
         $this->assign(array(
            'phone'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Gjlog','type'=>'phones'))->find(),
            'phone1'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Gjlog','type'=>'phones1'))->find(),
            'phone2'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Gjlog','type'=>'phones2'))->find(),
        ));
        $this->UDisplay('img');
      }

				
		
		
	  
}