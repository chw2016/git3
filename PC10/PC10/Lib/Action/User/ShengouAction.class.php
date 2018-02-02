<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南(anyi路况在线)
 **/
class ShengouAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';

    /**
     *  Token
     **/
    //private $_sToken = null;

    public function _initialize()
    {
        //$this->_sToken = $_SESSION['token'];
        parent::_initialize();

    }

    protected function setHeader(){
        return array(
            array(
                'name' => '加盟管理',
                'url'  => U('Shengou/index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '验证码管理',
                'url'  => U('Shengou/yanzheng', array('token' => $this->_sToken))
            ),
            array(
                'name' => '图片管理',
                'url'  => U('Shengou/sophone', array('token' => $this->_sToken))
            ),
        );
    }


    /*
     * 加盟管理
     * */
    public function index(){
        $aWhere = array('token' =>$_SESSION['token']);

      /*  //搜索
        if(IS_POST){
            $_POST=$_REQUEST;
            $aWhere=$this->search($_POST);
            $aWhere['token'] =$_SESSION['token'];
            //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
        }//结束*/


        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Shengou/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(


                    array(
                        'name'   => '加盟广告信息',
                        'url'    => U('Shengou/ucontent')
                    ),


                   /* array(
                        'name'   => '导出数据',
                        'url'    => U('ExportExcel/Zhaopin')
                    ),*/

                ),
                'tips' => array(
                    '你可以在这里管理加盟信息'
                ),
                'Table_Header' => array(
                    'ID', '申请人','学历', '电话','从事职业','性别','年龄', '店面团队人数','现有投资规模','拟投资规模','申请时间','状态','加盟时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查阅',
                        'url'  => U('Shengou/seeuse')
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Shengou/del_use')
                    ),
                ),
                //搜索
//                'search'=>array(
//                    //array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
//                    //array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
//                    array('title'=>'加盟时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
//                )//结束

            ),
            M('mru_wyjm')->where($aWhere)->count(),
            M('mru_wyjm')->field('id,name,xl,dh,zy,sex,age,num,xtz,ytz,add_time,type,read_time')->order("add_time desc")->where($aWhere),
            array($this,'use_index')
        );
    }

    public function use_index($data){
        //P($data);exit;
        foreach($data as $k=>$val){
            switch($val['xl']){
                case 0:$data[$k]['xl'] = '小学';break;
                case 1:$data[$k]['xl'] = '初中';break;
                case 2:$data[$k]['xl'] = '高中';break;
                case 3:$data[$k]['xl'] = '大专';break;
                case 4:$data[$k]['xl'] = '本科';break;
                case 5:$data[$k]['xl'] = '本科以上';break;
                default:$data[$k]['xl'] = '其他';break;
            };
            switch($val['sex']){
                case 0:$data[$k]['sex'] = '男';break;
                case 1:$data[$k]['sex'] = '女';break;
                default:$data[$k]['sex'] = '其他';break;
            };
            switch($val['type']){
                case 0:$data[$k]['type'] = '申请中';break;
                case 1:$data[$k]['type'] = '加盟通过';break;
                case 2:$data[$k]['type'] = '加盟未通过';break;
                default:$data[$k]['type'] = '其他';break;
            };
            $data[$k]['add_time']=date('Y-m-d H:i',$val['add_time']);
            if($val['type'] == 1){
                $data[$k]['read_time']=date('Y-m-d H:i',$val['read_time']);
            }else{
                $data[$k]['read_time']= '';
            }

        }
        return $data;
    }

    /*查阅*/
    public function set_content($aaa){

            $this->$aaa('mru_wyjm',array(

                array('title'=>"申请人",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写申请人','bast'=>'','readonly'=>1),
                array('title'=>"学历",'type'=>"input",'name'=>"xl",'value'=>'xl','msg'=>'请填写学历','bast'=>'','readonly'=>1),
                array('type'=>'select','title'=>"学历",'name'=>"xl",'value'=>'xl','msg'=>'请选择学历','readonly'=>1,'many'=>array(
                    array('content'=>'请选择学历'),
                    array('value'=>'0', 'content'=>'小学'),
                    array('value'=>'1','content'=>'初中'),
                    array('value'=>'2','content'=>'高中'),
                    array('value'=>'3','content'=>'大专'),
                    array('value'=>'4','content'=>'本科'),
                    array('value'=>'5','content'=>'本科以上'),
                )),
                array('title'=>"电话",'type'=>"input",'name'=>"dh",'value'=>'dh','msg'=>'请填写电话','bast'=>'','readonly'=>1),
                array('title'=>"从事职业",'type'=>"input",'name'=>"zy",'value'=>'zy','msg'=>'请填写从事职业','bast'=>'','readonly'=>1),

                array('type'=>'select','title'=>"性别",'name'=>"sex",'value'=>'sex','msg'=>'请选择性别','readonly'=>1,'many'=>array(
                    array('content'=>'请选择加盟是否通过'),
                    array('value'=>'0', 'content'=>'男'),
                    array('value'=>'1','content'=>'女'),
                )),
                array('title'=>"年龄",'type'=>"input",'name'=>"sex",'value'=>'sex','msg'=>'请填写年龄','bast'=>'','readonly'=>1),
                array('title'=>"店面团队人数",'type'=>"input",'name'=>"num",'value'=>'num','msg'=>'请填写店面团队人数','bast'=>'','readonly'=>1),
                array('title'=>"现有投资规模",'type'=>"input",'name'=>"xtz",'value'=>'xtz','msg'=>'请填写现有投资规模','bast'=>'','readonly'=>1),
                array('title'=>"拟投资规模",'type'=>"input",'name'=>"ytz",'value'=>'ytz','msg'=>'请填写拟投资规模','bast'=>'','readonly'=>1),
                array('title'=>"邮箱",'type'=>"input",'name'=>"yx",'value'=>'yx','msg'=>'请填写邮箱','bast'=>'','readonly'=>1),
                array('title'=>"加盟合作区域",'type'=>"input",'name'=>"qy",'value'=>'qy','msg'=>'请填写加盟合作区域','bast'=>'','readonly'=>1),
                array('title'=>"现经营项目",'type'=>"input",'name'=>"xm",'value'=>'xm','msg'=>'请填写现经营项目','bast'=>'','readonly'=>1),
                array('title'=>"店面面积",'type'=>"input",'name'=>"mj",'value'=>'mj','msg'=>'请填写店面面积','bast'=>'','readonly'=>1),
                array('type'=>'select','title'=>"是否自有资金",'name'=>"jfzj",'value'=>'jfzj','msg'=>'请选择是否自有资金','readonly'=>1,'many'=>array(
                    array('content'=>'请选择是否自有资金'),
                    array('value'=>'0', 'content'=>'是'),
                    array('value'=>'1','content'=>'否'),
                )),
                array('type'=>'select','title'=>"是否整体合作项目",'name'=>"hz",'value'=>'hz','msg'=>'请选择是否整体合作项目','readonly'=>1,'many'=>array(
                    array('content'=>'请选择是否整体合作项目'),
                    array('value'=>'0', 'content'=>'是'),
                    array('value'=>'1','content'=>'否'),
                )),
                array('title'=>"联系地址",'type'=>"input",'name'=>"dz",'value'=>'dz','msg'=>'请填写拟投资规模','bast'=>'','readonly'=>1),
                array('title'=>"加盟需求说明",'type'=>"longinput",'name'=>"content",'value'=>'content','msg'=>'请填写加盟需求说明','readonly'=>1),
                array('type'=>'select','title'=>"加盟是否通过",'name'=>"type",'value'=>'type','msg'=>'请选择加盟是否通过','many'=>array(
                        array('content'=>'请选择加盟是否通过'),
                        array('value'=>'0', 'content'=>'申请中'),
                        array('value'=>'1','content'=>'加盟通过'),
                        array('value'=>'2','content'=>'加盟未通过'),
                )),

            ),U('Shengou/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

        }
        public function bbc($data){
            $data['read_time'] = time();
            return $data;
        }

        //编辑
        public function seeuse(){
            $this->set_content(Edit);
        }
        //删除
        public function delete_content(){
            //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
            $this->del('mru_wyjm');
        }

/*加盟广告*/
    public function ucontent(){
        $aWhere = array('token' =>$_SESSION['token']);
        $this->table(
            array(
                'HeadHover' => U('Shengou/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name'   => '添加加盟广告信息',
                        'url'    => U('Shengou/add_ucontent')
                    ),
                     array(
                        'name'   => '返回',
                        'url'    => U('Shengou/index')
                    ),
                ),
                'tips' => array(
                    '你可以在这里管理加盟信息'
                ),
                'Table_Header' => array(
                    'ID', '主题','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Shengou/save_conment')
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Shengou/del_conment')
                    ),
                ),
            ),
            M('Mru_jm')->where($aWhere)->count(),
            M('Mru_jm')->field('id,title')->where($aWhere)
           // array($this,'content_index')
        );
    }


    public function set_contents($aaa){

        $this->$aaa('Mru_jm',array(

            array('title'=>"主题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填主题','bast'=>''),
            array('title'=>"内容",'type'=>"textarea",'name'=>"content",'value'=>'content'),

        ),U('Shengou/ucontent',array('token'=>$_SESSION['token'])),array($this,'bbcinfo'));

    }
    public function bbcinfo($data){
        $data['add_time'] = time();
        return $data;
    }
    public function add_ucontent(){
        $this->set_contents(add);
    }
    //编辑
    public function save_conment(){
        $this->set_contents(Edit);
    }
    //删除
    public function del_conment(){
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Mru_jm');
    }



    public function sophone(){
     
		$oImgModel = M('Imag');
        $this->assign(array(
            'phone'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Shengou','type'=>'phones'))->find(),
            'phone2'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Shengou','type'=>'phones2'))->find(),
            'phone3'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Shengou','type'=>'phones3'))->find(),
            'phone4'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Shengou','type'=>'phones4'))->find(),
        ));
        $this->UDisplay('sophone');
    }

    public function yanzheng(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                'HeadHover' => U('Shengou/yanzheng', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name' => '导入信息',//2级
                        'url' => U('Shengou/add_yanzheng', array('token' => $_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                   '您可以在这里管理数据'
                ),
                'Table_Header' => array(//4级
                    'ID','验证号','状态', '操作'
                ),
                'List_Opt' => array(
                   /* array(
                        'name' => '编辑',
                        'url' => U('Tailg/save_content', array('token' => $_SESSION['token'],'aid'=>$this->_iAid))
                    ),*/

                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url' => U('Shengou/delete_yanzheng', array('token' => $_SESSION['token']))
                    ),
                ),

            ),
            M('Shenou_code')->where($aWhere)->count(),
            M('Shenou_code')->field('id,code,type')->where($aWhere)->order('type ,add_time desc'),
         array($this,'yanzhengindex')
        );
    }

    public function yanzhengindex($data){
        foreach($data as $key=>$val){
            switch($val['type']){
                case 0: $data[$key]['type'] = "未使用";break;
                case 1: $data[$key]['type'] = "已使用";break;
                default:$data[$key]['type'] = "其他";break;
            }
        }
        return $data;
    }


    public function add_yanzheng(){

       /* $a1 = array('abc');
        $a2 = array('def');
       $a3 = array_combine($a1,$a2);
        P($a3);exit;*/
        $oTailgModel = M('Shenou_code');
        if(IS_AJAX){
            $ainfons =explode(',',trim($_POST['info']));
            array_pop($ainfons);
           // $name = array('code');
            foreach($ainfons as $val){

                $ainfots['code'] = $val;

               // $ainfots = array_combine($name,$val);

                $ainfots['token'] = $this->_sToken;
                $ainfots['add_time'] = date('Y-m-d H:i:s');
                $aFind = $oTailgModel->where(array('code'=>$ainfots['code'],'token'=>$this->_sToken))->find();
                if($aFind){
                    $this->error($ainfots['code'].'添加失败');
                }
                $istrue =  $oTailgModel->add($ainfots);
            }
            if($istrue){
                $this->success('添加成功',U('Shengou/yanzheng',array('token'=>$this->_sToken)));
            }else{
                $this->error('添加失败');
            }
        }
        $this->assign(array(
            'ExtraBtn' => array(
                array(
                    'url'  => U('Shengou/yanzheng',array('token' => $this->_sToken)),
                    'name' => '返回'
                )
            ),
        ));

        $this->UDisplay('insettwo');
    }


    //删除
    public function delete_yanzheng(){
        $this->del('Shenou_code');
    }




}