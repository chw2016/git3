<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2015/5/7
 * Time: 10:09
 */
class IntelAction extends TableAction{
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/togethernext';
    public function _initialize()
    {
        parent::_initialize();
        $this->imei = M('Intel_imei');
        $this->cartype = M('Intel_cartype');
    }
    protected function setHeader(){
        return array(
            array(
                'name' => 'IMEI查看',
                'url'  => U('Intel/index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '汽车型号管理',
                'url'  => U('Intel/cartype', array('token' => $this->_sToken))
            ),
            array(
                'name' => '用户中心',
                'url'  => U('Intel/user', array('token' => $this->_sToken))
            ),
            array(
                'name' => '导入IMEI',
                'url'  => U('Intel/imei', array('token' => $this->_sToken))
            ),
        );
    }

    public function index(){
        $aWhere = array('token'=>$this->_sToken);
        $this->bShowDefault = true;
        $this->table(
            array(
               // 'kid' => 'aid',//如果主键不是id，则需要设置
                'HeadHover' => U('Intel/index', array('token' => $this->_sToken)),
                //显示表头里是图片的字段，并且设置图片大小
                'aListImg' => array(
                    'container' => array('qrurl'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'tips' => array(
                    '这里显示的是imei注册的信息',
                ),
                'Table_Header' => array(
                    'ID', 'imei', '二维码图片'
                ),
            ),
            $this->imei->where($aWhere)->count(),
            $this->imei->field('id,imei,qrurl')->where($aWhere)
        );
    }

    public function cartype()
    {
        $aWhere = array('token'=>$this->_sToken, 'pid' => 0);
        $this->bShowDefault = true;
        $this->table(
            array(
               // 'kid' => 'aid',//如果主键不是id，则需要设置
                'HeadHover' => U('Intel/cartype', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加汽车品牌',
                        'url' => U('Intel/addCartype')
                    )
                ),
                'tips' => array(
                    '这里是汽车品牌',
                ),
                'Table_Header' => array(
                    'ID', '品牌','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '品牌管理',
                        'url' => U('Intel/showXH')
                    ),
                    array(
                        'name' => '编辑',
                        'url' => U('Intel/edit_cartype')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('Intel/del_cartype')
                    ),
                ),
            ),
            $this->cartype->where($aWhere)->count(),
            $this->cartype->field('id,name')->where($aWhere)
        );
    }

    public function showXH()
    {
        $aWhere = array('token'=>$this->_sToken, 'pid' => $_REQUEST['id']);
        $this->bShowDefault = true;
        $this->table(
            array(
               // 'kid' => 'aid',//如果主键不是id，则需要设置
                'HeadHover' => U('Intel/cartype', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加汽车型号',
                        'url' => U('Intel/addXH', array('id' => $_REQUEST['id']))
                    )
                ),
                'tips' => array(
                    '这里是汽车型号',
                ),
                'Table_Header' => array(
                    'ID', '型号','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('Intel/editXH', array('pid' => $_REQUEST['id']))
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('Intel/del_cartype')
                    ),
                ),
            ),
            $this->cartype->where($aWhere)->count(),
            $this->cartype->field('id,name')->where($aWhere)
        );
    }
    /*imei*/
    public function imei(){
        $aWhere = array('token'=>$this->token);
        if(IS_POST){
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                'HeadHover' => U('Intel/imei', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name' => '导入EXCEL',
                        'url' => U('add_excel',array('token'=>$this->token))
                    ),
                ),
                'List_Opt' => array(
                    array(
                    'name' => '删除',
                    'url' => U('delete_imei')
                    ),
                ),
                'tips' => array(//3级
                   '导入imei'
                ),
                'Table_Header' => array(//4级
                    'ID','imei', '添加时间','操作'
                ),
                //搜索
                'search'=>array(
                    array('title'=>'IMEI','name'=>'li_imei','placeholder'=>'请输入您要查询的IMEI','search'=>'查询'),//li是Table里判断条件 name是子段
                )//结束
            ),
            M('intel_bind_imei')->where($aWhere)->count(),
            M('intel_bind_imei')->field('id,imei,add_time')->where($aWhere)
        );
    }

    public function delete_imei(){
        $this->del('intel_bind_imei');

    }


    public function add_excel(){
        //$this->assign('jd',$_GET['type']);

        $this->display('add_excel');
    }

    public function exportExcel(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        //p($upload);
        $upload->maxSize  = 8145728 ;// 设置附件上传大小
        $upload->allowExts  = array('xlsx','xls');// 设置附件上传类型
        $upload->savePath =  './upload/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $err = $upload->getErrorMsg();
            $this->ajaxReturn(array('code'=>-1,'msg'=>$err));
        }else{// 上传成功
            $data = $upload->getUploadFileInfo();

            $filename = $data[0]['savepath'].$data[0]['savename'];


            $exceldata = Excel::excel2Arr($filename);
            $aData = array();
            //先找出已经有的
            $aHas = array_keys(Arr::changeIndex(M('Intel_bind_imei')->field('imei')->where(array(
                'token' => $this->_sToken
            ))->select(), 'imei'));
            foreach ($exceldata as $k => $data) {
                if (in_array($data[0], $aHas)) {
                    continue;
                }
                $aData[] = array(
                    'token' => $this->_sToken,
                    'imei'  => $data[0],
                    'add_time'  => date('Y-m-d H:i:s')
                );
            }
            M('Intel_bind_imei')->addAll($aData);

            $this->ajaxReturn(array('code'=>0,'msg'=>'处理成功~~别再次请求'));
        }
    }


    /*用户管理*/
    public function user(){
        $aWhere = array('token'=>$this->token);
        if(IS_POST){
            $_POST = $_REQUEST;
            $aWhere = $this->search($_POST);
            $aWhere['token'] = $this->_sToken;
        }
        $this->table(
            array(
                'HeadHover' => U('Intel/user', array('token' => $this->_sToken)),//栏目样式
                'tips' => array(//3级
                   '这里是用户中心'
                ),
                'Table_Header' => array(//4级
                    'ID','微信昵称', '手机号', 'IMEI','绑定状态','绑定时间', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看轨迹',
                        'tkey' =>'imei',
                        'tval'=>'imei',
                        'url' => U('Wap/Intel/map', array('token' => $_SESSION['token']))
                    ),

                ),
                //搜索
                'search'=>array(
                    array('title'=>'IMEI','name'=>'li_imei','placeholder'=>'请输入您要查询的IMEI','search'=>'查询'),//li是Table里判断条件 name是子段
                    array('title'=>'联系方式','name'=>'li_phone','placeholder'=>'请输入您要查询的联系方式','search'=>'查询'),//eq是Table里判断条件 name是子段

                )//结束
            ),
            M('Intelligent_devices_users')->where($aWhere)->count(),
            M('Intelligent_devices_users')->field('id,openid,phone,imei,is_on,bind_time')->where($aWhere),
         array($this,'userinfo')
        );
    }
    public function userinfo($data){
        foreach($data as $key => $value){
            switch($value['is_on']){
                case 0: $data[$key]['is_on'] = '解除绑定';break;
                case 1: $data[$key]['is_on'] = '绑定';break;
                default:$data[$key]['is_on'] = '其他';
            }
            $user = M('Wxuser')->where(array('token'=>$this->token))->find();
            $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$value['openid']))->find();
            $data[$key]['openid'] = $users['nickname'];
        }
        return $data;
    }



    public function addXH()
    {
        if (isset($_REQUEST['id'])) {
            $_REQUEST['pid'] = $_REQUEST['id'];
            unset($_REQUEST['id']);
        }
        $this->add('Intel_cartype',array(
            array('title'=>"型号",'type'=>"input",'name'=>"name",'msg'=>'请输入型号'),
        ),U('Intel/showXH',array('token' => $this->_sToken, 'id' => $_REQUEST['pid'])));
    }

    public function editXH()
    {
        $this->Edit('Intel_cartype',array(
            array('title'=>"品牌",'type'=>"input", 'value' => 'name','name'=>"name",'msg'=>'请输入品牌'),
        ),U('Intel/showXH',array('token' => $this->_sToken, 'id' => $_REQUEST['pid'])));
    }

    public function addCartype()
    {
        $this->add('Intel_cartype',array(
            array('title'=>"汽车品牌",'type'=>"input",'name'=>"name",'msg'=>'请添加品牌'),
        ),U('Intel/cartype',array('token' => $this->_sToken)));
    }

    public function del_cartype(){
        $this->del('Intel_cartype');
    }

    public function edit_cartype(){
        $this->Edit('Intel_cartype',array(
            array('title'=>"品牌",'type'=>"input", 'value' => 'name','name'=>"name",'msg'=>'请输入品牌'),
        ),U('Intel/cartype',array('token' => $this->_sToken)));
    }
}
