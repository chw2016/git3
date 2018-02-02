<?php
/**
 * Created by PhpStorm.
 * User: anyi
 * Date: 2015/7/14
 * Time: 10:06
 */

class LaborAction extends TableAction {
    public $_sTplBaseDir = 'User/default/togethernext';
    public function _initialize()
    {
        parent::_initialize();
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '信息公开',
                'url'  => U('Labor/services', array('token' => $this->_sToken))
            ),
            array(
                'name' => '职企服务',
                'url'  => U('Labor/enterprise', array('token' => $this->_sToken))
            ),
            array(
                'name' => '就业服务 ',
                'url'  => U('Labor/text5', array('token' => $this->_sToken,'type'=>5))
            ),

        );
    }

   /* 劳务公开 */
    public function services(){
        //$this->_sToken=$this->_sToken;
        /*     	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$this->_sToken;
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
                }//结束 */
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/services', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加政策法规',//2级
                        'url'    => U('Labor/add_services',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name'   => '仲裁公告',//2级
                        'url'    => U('Labor/text1',array('token'=>$this->_sToken,'type'=>1))
                    ),
                    array(
                        'name'   => '通告批评',//2级
                        'url'    => U('Labor/text2',array('token'=>$this->_sToken,'type'=>2))
                    ),
                    array(
                        'name'   => '申诉热线',//2级
                        'url'    => U('Img/index',array('token'=>$this->_sToken))
                    ),
                    //
                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“政策法规”</span>的相关内容。'
                ),
                'Table_Header' => array(//4级
                    'ID', '名称', '简介', '政策发布时间','平台发布时间','操作'
                ),
                'List_Opt' => array(

                    /*连接上可能会带其他的参数，则参考如下*/
                    /*array(
                        'name' => '用户管理',
                        'tkey' =>'aid', //参数名称；
                        'tval'=>'id',  //参数值
                        'url' => U('Tailg/index', array('token' => $this->_sToken))
                    ),*/

                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_services',array('token'=>$this->_sToken))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_services',array('token'=>$this->_sToken))
                    ),
                ),
            /*
            'search'=>array(
                            array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                            array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                            array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
            )//结束 */
            ),
            M('Labor_services')->where(array('token'=>$this->_sToken))->count(),
            M('Labor_services')->field('id,title,abstract,ftime,add_time')->where(array('token'=>$this->_sToken))->order("add_time"),
         array($this,'servicesinfo')
        );
    }

    public function servicesinfo($data){
        foreach($data as $key=>$value){
            $data[$key]['abstract'] = mb_substr($value['abstract'],0,20,'utf-8').'...';
        }
        return $data;
    }



    /*政策法规添加编辑*/
    public function set_services($aaa){
        $this->$aaa('Labor_services',array(
            /*
             * 如果有需求要在input框中提示信息,则加入一个'msg'的键值，表示input 里面的placeholder属性；
             * 如果在input框后面有需求加备注，则加一个'bast'的键值，作用：input后面显示的一个内容，作为一种提醒信息；
             * 如果要求input框是只读的，则在后面添加一个键值为‘readonly’,表示input为只读框。
             * */
            array('title'=>"政策法规标题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写政策法规标题','bast'=>''),
            array('title'=>"政策法规发布时间",'type'=>"time",'name'=>"ftime",'msg'=>'请填写政策法规发布时间','value'=>'ftime'),
            array('title'=>"政策法规简介",'type'=>"textarea2",'name'=>"abstract",'value'=>'abstract'),
            array('title'=>"政策法规具体内容",'type'=>"textarea",'name'=>"content",'value'=>'content'),
        ),U('Labor/services',array('token'=>$this->_sToken)),array($this,'sercivesset'));

    }
    public function sercivesset($data){
        $data['token'] = $this->_sToken;
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }
    //添加
    public function add_services(){
        $this->set_services(add);
    }
    //编辑
    public function save_services(){
        $this->set_services(Edit);
    }
    //删除
    public function delete_services(){
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Labor_services');
    }
    /*相关图文展示页面的*/
    public function text1(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'type'=>$_GET['type']
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/services', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '仲裁公告添加',//2级
                        'url'    => U('Labor/add_text',array('token'=>$this->_sToken,'type'=>1))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/services',array('token'=>$this->_sToken))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“仲裁公告”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '类型', '内容','平台发布时间','操作'
                ),
                'List_Opt' => array(

                    /*连接上可能会带其他的参数，则参考如下*/
                    /*array(
                        'name' => '用户管理',
                        'tkey' =>'aid', //参数名称；
                        'tval'=>'id',  //参数值
                        'url' => U('Tailg/index', array('token' => $this->_sToken))
                    ),*/

                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_text',array('token'=>$this->_sToken,'type'=>1))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_text',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_text')->where($aWhere)->count(),
            M('Labor_text')->field('id,type,content,add_time')->where($aWhere)->order("add_time desc"),
            array($this,'textinfo')
        );
    }
    public function text2(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'type'=>$_GET['type']
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/services', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '通告批评添加',//2级
                        'url'    => U('Labor/add_text',array('token'=>$this->_sToken,'type'=>2))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/services',array('token'=>$this->_sToken))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“通告批评”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '类型', '内容','平台发布时间','操作'
                ),
                'List_Opt' => array(

                    /*连接上可能会带其他的参数，则参考如下*/
                    /*array(
                        'name' => '用户管理',
                        'tkey' =>'aid', //参数名称；
                        'tval'=>'id',  //参数值
                        'url' => U('Tailg/index', array('token' => $this->_sToken))
                    ),*/

                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_text',array('token'=>$this->_sToken,'type'=>2))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_text',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_text')->where($aWhere)->count(),
            M('Labor_text')->field('id,type,content,add_time')->where($aWhere)->order("add_time"),
            array($this,'textinfo')
        );
    }
    public function text3(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'type'=>$_GET['type']
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/enterprise', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '处理公告添加',//2级
                        'url'    => U('Labor/add_text',array('token'=>$this->_sToken,'type'=>3))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/enterprise',array('token'=>$this->_sToken))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“处理公告”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '类型', '内容','平台发布时间','操作'
                ),
                'List_Opt' => array(

                    /*连接上可能会带其他的参数，则参考如下*/
                    /*array(
                        'name' => '用户管理',
                        'tkey' =>'aid', //参数名称；
                        'tval'=>'id',  //参数值
                        'url' => U('Tailg/index', array('token' => $this->_sToken))
                    ),*/

                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_text',array('token'=>$this->_sToken,'type'=>3))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_text',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_text')->where($aWhere)->count(),
            M('Labor_text')->field('id,type,content,add_time')->where($aWhere)->order("add_time"),
            array($this,'textinfo')
        );
    }
    public function text4(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'type'=>$_GET['type']
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/enterprise', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '申诉热线添加',//2级
                        'url'    => U('Labor/add_text',array('token'=>$this->_sToken,'type'=>4))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/enterprise',array('token'=>$this->_sToken))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“申诉热线”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '类型', '内容','平台发布时间','操作'
                ),
                'List_Opt' => array(

                    /*连接上可能会带其他的参数，则参考如下*/
                    /*array(
                        'name' => '用户管理',
                        'tkey' =>'aid', //参数名称；
                        'tval'=>'id',  //参数值
                        'url' => U('Tailg/index', array('token' => $this->_sToken))
                    ),*/

                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_text',array('token'=>$this->_sToken,'type'=>4))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_text',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_text')->where($aWhere)->count(),
            M('Labor_text')->field('id,type,content,add_time')->where($aWhere)->order("add_time"),
            array($this,'textinfo')
        );
    }

//    /*聊天室列表页*/
//    public function mediation(){
//        //$this->_sToken=$this->_sToken;
//        /*     	//搜索
//                if(IS_POST){
//                    $_POST=$_REQUEST;
//                    $aWhere=$this->search($_POST);
//                    $aWhere['token'] =$this->_sToken;
//                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
//                }//结束 */
//        $this->table(
//            array(
//                //'id' => 'id',//如果主键不是id，则需要设置
//                'HeadHover' => U('Labor/services', array('token' => $this->_sToken)),//栏目样式
//                'Head_Opt' => array(
//                    array(
//                        'name'   => '创建聊天室',//2级
//                        'url'    => U('Labor/setmediation',array('token'=>$this->_sToken))
//                    ),
//                    array(
//                        'name'   => '返回',//2级
//                        'url'    => U('Labor/services',array('token'=>$this->_sToken))
//                    ),
//
//                ),
//                'tips' => array(//3级
//                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“在线调解”</span>的相关内容。'
//                ),
//                'Table_Header' => array(//4级
//                    'ID', '主题', '简介', '聊天室验证码','创建时间','操作'
//                ),
//                'List_Opt' => array(
//
//                    /*连接上可能会带其他的参数，则参考如下*/
//                    /*array(
//                        'name' => '用户管理',
//                        'tkey' =>'aid', //参数名称；
//                        'tval'=>'id',  //参数值
//                        'url' => U('Tailg/index', array('token' => $this->_sToken))
//                    ),*/
//
//                    array(
//                        'name' => '编辑',
//                        'url'  => U('Labor/setmediation',array('token'=>$this->_sToken))
//                    ),
//                    array(
//                        'type'=>1,
//                        'name' => '删除',
//                        'url'  => U('Labor/delete_mediation',array('token'=>$this->_sToken))
//                    ),
//                ),
//                /*
//                'search'=>array(
//                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
//                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
//                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
//                )//结束 */
//            ),
//            M('Labor_code')->where(array('token'=>$this->_sToken))->count(),
//            M('Labor_code')->field('id,title,bask,code,add_time')->where(array('token'=>$this->_sToken))->order("add_time"),
//            array($this,'mediationinfo')
//        );
//    }
//    public function mediationinfo($data){
//        return $data;
//    }
//
//    /*创建聊天室*/
//    public function setmediation(){
//        $oModel = M('Labor_code');
//        if(IS_POST){
//            $id = $_POST['id'];
//            $isFind = $oModel->where(array('id'=>$id))->find();
//            if($isFind){
//                if($oModel->where(array('id'=>$id))->save($_POST)){
//                    $this->success('创建成功！');
//                }else{
//                    $this->error('创建失败!');
//                }
//            }else{
//                $_POST['add_time'] = date('Y-m-d H:i:s');
//                $_POST['token'] = $this->token;
//                $code = $this->randStr();
//                $isFinds = $oModel->where(array('code'=>$code,'token'=>$this->token))->find();
//                if($isFinds){
//                    $this->error('自动生成随机码有重复，创建失败!');
//                }else{
//                    $_POST['code'] = $code;
//                    if($oModel->add($_POST)){
//                        $this->success('创建成功！');
//                    }else{
//                        $this->error('创建失败!');
//                    }
//                }
//            }
//        }else{
//            $lid = $_GET['id'];
//            $info = $oModel->where(array('id'=>$lid))->find();
//            $this->assign('info',$info);
//        }
//        $this->assign(array(
//            'ExtraBtn' => array(
//                array(
//                    'url'  => U('Labor/mediation', array('token' => $this->_sToken)),
//                    'name' => '返回'
//                )
//            ),
//        ));
//        $this->UDisplay('setmediation');
//    }
//
//
//    public function randStr($len=6) {
//        $chars='abdefghijkmnpqrstvwxy23456789'; // characters to build the password from
//        mt_srand((double)microtime()*1000000*getmypid()); // seed the random number generater (must be done)
//        $code='';
//        while(strlen($code)<$len)
//            $code.=substr($chars,(mt_rand()%strlen($chars)),1);
//        return $code;
//    }
//
//
//    //删除
//    public function delete_mediation(){
//        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
//        $this->del('Labor_code');
//    }

    /*
     * 就业服务
     * */
    public function text5(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'type'=>$_GET['type']
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/text5', array('token' => $this->_sToken,'type'=>5)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '市培训补贴信息管理',//2级
                        'url'    => U('Img/index',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name'   => '省培训补贴信息管理',//2级
                        'url'    => U('Img/index',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name'   => '创业扶持政策管理',//2级
                        'url'    => U('Labor/text7',array('token'=>$this->_sToken,'type'=>7))
                    ),
                    array(
                        'name'   => '岗位招聘管理',//2级
                        'url'    => U('Labor/recruitment',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name'   => '最新动态管理',//2级
                        'url'    => U('Img/index',array('token'=>$this->_sToken))
                    ),


                ),
               /* 'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“市培训补贴信息”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '类型', '内容','平台发布时间','操作'
                ),*/
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_text',array('token'=>$this->_sToken,'type'=>5))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_text',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_text')->where($aWhere)->count(),
            M('Labor_text')->field('id,type,content,add_time')->where($aWhere)->order("add_time"),
            array($this,'textinfo')
        );
    }
    public function text6(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'type'=>$_GET['type']
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/text5', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '技能晋升培训补贴信息添加',//2级
                        'url'    => U('Labor/add_text',array('token'=>$this->_sToken,'type'=>6))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/text5',array('token'=>$this->_sToken,'type'=>5))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“技能晋升培训补贴信息”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '类型', '内容','平台发布时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_text',array('token'=>$this->_sToken,'type'=>6))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_text',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_text')->where($aWhere)->count(),
            M('Labor_text')->field('id,type,content,add_time')->where($aWhere)->order("add_time"),
            array($this,'textinfo')
        );
    }
    public function text7(){
        $aWhere = array(
            'token'=>$this->_sToken,
            'type'=>$_GET['type']
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/text5', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '创业扶持政策添加',//2级
                        'url'    => U('Labor/add_text',array('token'=>$this->_sToken,'type'=>7))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/text5',array('token'=>$this->_sToken,'type'=>5))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“创业扶持政策”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '类型', '内容','平台发布时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_text',array('token'=>$this->_sToken,'type'=>7))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_text',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_text')->where($aWhere)->count(),
            M('Labor_text')->field('id,type,content,add_time')->where($aWhere)->order("add_time"),
            array($this,'textinfo')
        );
    }
    public function textinfo($data){
        foreach($data as $key=>$value){
            if(mb_strlen($value['content'],'utf8') < 40){
                $data[$key]['content'] = $value['content'];
            }else{
                $data[$key]['content'] = mb_substr($value['content'],0,40,'utf-8').'...';
            }
            switch($value['type']){
                case 1:$data[$key]['type'] = '榜上有名';break;
                case 2:$data[$key]['type'] = '通知批评';break;
                case 3:$data[$key]['type'] = '处理公告';break;
                case 4:$data[$key]['type'] = '申诉热线';break;
                case 5:$data[$key]['type'] = '市培训补贴';break;
                case 6:$data[$key]['type'] = '技能晋升培训补贴';break;
                case 7:$data[$key]['type'] = '就业补贴申请流程';break;
                default:$data[$key]['type'] = '其他';
            }
        }
        return $data;
    }
    /*政策法规添加编辑*/
    public function set_text($aaa){
        $this->$aaa('Labor_text',
            array(
                /*
                 * 如果有需求要在input框中提示信息,则加入一个'msg'的键值，表示input 里面的placeholder属性；
                 * 如果在input框后面有需求加备注，则加一个'bast'的键值，作用：input后面显示的一个内容，作为一种提醒信息；
                 * 如果要求input框是只读的，则在后面添加一个键值为‘readonly’,表示input为只读框。
                 * */
                array('title'=>"标题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写相关的标题','bast'=>''),
                array('title'=>"内容",'type'=>"textarea",'name'=>"content",'value'=>'content'),
            ),
            U('Labor/text'.$_GET['type'],array('token'=>$this->_sToken,'type'=>$_GET['type'])),
            array($this,'textset'));

    }
    public function textset($data){
        $data['tid'] = $this->_iTid;
        $data['token'] = $this->_sToken;
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['type'] = $_GET['type'];
        return $data;
    }
    //添加
    public function add_text(){
        $this->set_text(add);
    }
    //编辑
    public function save_text(){
        $this->set_text(Edit);
    }
    //删除
    public function delete_text(){
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Labor_text');
    }


    /*
   员工企业服务
    */
    //企业信息
    public function enterprise(){
        $this->assign('qx',"删除");
        //$this->_sToken=$this->_sToken;
        /*     	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$this->_sToken;
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
                }//结束 */
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/enterprise', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '企业信息导入',//2级
                        'url'    => U('Labor/insetdb',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name'   => '员工申诉管理',//2级
                        'url'    => U('Labor/appeal',array('token'=>$this->_sToken))
                    ),
                  /*  array(
                        'name'   => '处理公告信息',//2级
                        'url'    => U('Labor/text3',array('token'=>$this->_sToken,'type'=>3))
                    ),
                    array(
                        'name'   => '申诉热线信息',//2级
                        'url'    => U('Labor/text4',array('token'=>$this->_sToken,'type'=>4))
                    ),*/
                    array(
                        'name'   => '在线调解',//2级
                        'url'    => U('Room/index',array('token'=>$this->_sToken))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“企业信息”</span>的相关内容。'
                ),
                'Table_Header' => array(//4级
                    'ID', '单位名称','组织机构代码','行业类型','单位类型','法定代表人','法定代表人电话','单位联系人','单位联系方式','员工人数','密码','审核状态','更新状态','操作'
                ),
                'List_Opt' => array(

                    /*连接上可能会带其他的参数，则参考如下*/
                    /*array(
                        'name' => '用户管理',
                        'tkey' =>'aid', //参数名称；
                        'tval'=>'id',  //参数值
                        'url' => U('Tailg/index', array('token' => $this->_sToken))
                    ),*/

                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/setenterprise',array('token'=>$this->_sToken))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_enterprise',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_enterprise')->where(array('token'=>$this->_sToken))->count(),
            M('Labor_enterprise')->field('id,company,code,industry,unit_type,legal_person,legal_phone,company_person,compant_phone,number,password,status,type')->where(array('token'=>$this->_sToken))->order("add_time"),
            array($this,'enterpriseinfo')
        );
    }

    public function enterpriseinfo($data){
        foreach($data as $key=>$value){
            switch($value['type']){
                case 0: $data[$key]['type'] = '未更新';break;
                case 1: $data[$key]['type'] = '有更新';break;
                case 2: $data[$key]['type'] = '未更新';break;
                case 3: $data[$key]['type'] = '未更新';break;
                default:$data[$key]['type'] = '其他';
            }
            switch($value['status']){
                case 0: $data[$key]['status'] = '未审核';break;
                case 1: $data[$key]['status'] = '审核通过';break;
                case 2: $data[$key]['status'] = '更新审核未通过';break;
                case 3: $data[$key]['status'] = '更新等待审核';break;
                default:$data[$key]['type'] = '其他';
            }
        }
        return $data;
    }

    public function qx(){
        if(!$_REQUEST['list']){
            $this->success2('全选后此操作才有效');
        }
        foreach ($_REQUEST['list'] as $ke => $v){
            M('Labor_enterprise')->where(array('id'=>$v))->delete();
        }
        $this->success2('删除成功');

    }
    /*导入数据页面*/
    public function insetdb(){
        $this->UDisplay('insetdb');
    }
    /*导入数据库*/
    public function read($filename,$encode='utf-8'){
        vendor("PHPExcel.PHPExcel");
        $objReader = PHPExcel_IOFactory::createReader(Excel5);
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;

    }

    public function insetcompany(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 8145728 ;// 设置附件上传大小
        $upload->allowExts  = array('xlsx','xls');// 设置附件上传类型
        $upload->savePath =  './upload/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $err = $upload->getErrorMsg();
            $this->ajaxReturn(array('code'=>-1,'msg'=>$err));
        }else{// 上传成功
            $data = $upload->getUploadFileInfo();
            $filename = $data[0]['savepath'].$data[0]['savename'];
            $exceldata = $this->read($filename); //此方法为引入
            array_shift($exceldata);
            foreach ($exceldata as $ke=>$v){
                M('Labor_enterprise')->add(array(
                    'company' => $v['0'],
                    'code' => $v['1'],
                    'address'=> $v['2'] ,
                    'capital' => $v['3'],
                    'industry' => $v['4'],
                    'unit_type'=>$v['5'],
                    'legal_person'=>$v['6'],
                    'legal_phone'=>$v['7'],
                    'identity_id'=>$v['8'],
                    'company_person'=>$v['9'],
                    'compant_phone'=>$v['10'],
                    'number'=>$v['11'],
                    'add_time'=>date('Y-m-d H:i:s'),
                    'password'=>'888888',
                    'token' => $this->_sToken,
                    'type'=>0,
                    'status'=>1

                ));
            };
            $this->ajaxReturn(array('code'=>0,'msg'=>'处理成功~~别再次请求'));
        }
    }

    /*更新数据,审核数据*/
    public function setenterprise(){
        $oModel = M('Labor_enterprise');
        $info = $oModel->where(array('id'=>$_GET['id']))->find();
        $infos = json_decode($info['bask'],'true');

        if(IS_POST){
            $status = $_POST['status'];
            $iTem = $oModel->where(array('id'=>$_POST['id']))->find();
            if($iTem['type'] == 1){
                if($status ==1){
                    $_POST['type'] = 2;
                    if($oModel->where(array('id'=>$_POST['id']))->save($_POST)){
                        $this->success('操作成功');
                    }else{
                        $this->error('操作失败');
                    }
                }else{
                    $data['type'] = 3;
                    $data['status'] = $_POST['status'];
                    if($oModel->where(array('id'=>$_POST['id']))->save($data)){
                        $this->success('操作成功');
                    }else{
                        $this->error('操作失败');
                    }
                }
            }else{
                P($_POST);
                $_POST['type'] = 2;
                if($oModel->where(array('id'=>$_POST['id']))->save($_POST)){
                    $this->success('操作成功');
                }else{
                    $this->error('操作失败');
                }
            }


        }else{
            $this->assign(array(
                'info'=>$info,
                'infos'=>$infos
            ));
        }
        $this->UDisplay('setenterprise');
    }

    /*删除数据*/
    public function delete_enterprise(){
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Labor_enterprise');
    }

    /*申诉管理*/
    public function appeal(){
        $aWhere = array(
            'token'=>$this->_sToken,
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/enterprise', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/enterprise',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name'   => '导出数据',//2级
                        'url'    => U('Labor/excelappeal',array('token'=>$this->_sToken))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理有关<span style="color: #ff0315;font-size: 16px;">“员工申诉信息”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '姓名','性别','单位','籍贯','联系方式','申诉状态','申诉时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看详情',
                        'url'  => U('Labor/save_appeal',array('token'=>$this->_sToken))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_appeal',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_appeal')->where($aWhere)->count(),
            M('Labor_appeal')->field('id,name,sex,company,origin,phone,type,add_time')->where($aWhere)->order("add_time"),
            array($this,'appealinfo')
        );
    }
    public function appealinfo($data){
        return $data;
    }

    /*导出数据*/
    public function excelappeal(){
        $list = M('Labor_appeal')->
            field('id,name,sex,')->
            field('id,name,sex,education,origin,address,identity_id,phone,company,return,info,type')->
            where(array('token'=>$this->token))->
            order("add_time")->select();
        foreach($list as $key=>$value){
            switch($value['type']){
                case 0;$list[$key]['type'] = '等待中';break;
                case 1;$list[$key]['type'] = '申诉成功';break;
            }
        }
        Excel::arr2ExcelDownload($list,
            array(
                'ID',
                '姓名',
                '性别',
                '学历',
                '籍贯',
                '居住地址',
                '身份证',
                '联系方式',
                '所在单位',
                '单位地址',
                '诉求',
                '申诉状态'),'员工申诉信息表');
    }
    /*编辑*/
    public function set_appeal($aaa){
        $this->$aaa('Labor_appeal',
            array(
                array('title'=>"姓名",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写姓名','bast'=>'','readonly'=>1),
                array('title'=>"性别",'type'=>"input",'name'=>"sex",'value'=>'sex','msg'=>'请填写性别','bast'=>'','readonly'=>1),
                array('title'=>"学历",'type'=>"input",'name'=>"education",'value'=>'education','msg'=>'请填写学历','bast'=>'','readonly'=>1),
                array('title'=>"籍贯",'type'=>"input",'name'=>"origin",'value'=>'origin','msg'=>'请填写籍贯','bast'=>'','readonly'=>1),
                array('title'=>"居住地址",'type'=>"input",'name'=>"address",'value'=>'address','msg'=>'请填写居住地址','bast'=>'','readonly'=>1),
                array('title'=>"身份证",'type'=>"input",'name'=>"identity_id",'value'=>'identity_id','msg'=>'请填写单位类型身份证','bast'=>'','readonly'=>1),
                array('title'=>"联系方式",'type'=>"input",'name'=>"phone",'value'=>'phone','msg'=>'请填写联系方式','bast'=>'','readonly'=>1),
                array('title'=>"所在单位",'type'=>"input",'name'=>"company",'value'=>'company','msg'=>'请填写所在单位','bast'=>'','readonly'=>1),
                array('title'=>"单位地址",'type'=>"input",'name'=>"return",'value'=>'return','msg'=>'请填写单位地址','bast'=>'','readonly'=>1),
                array('title'=>"诉求",'type'=>"textarea2",'name'=>"info",'value'=>'info','readonly'=>1),
                array('type'=>'select','title'=>"申诉状态",'name'=>"type",'value'=>'type','msg'=>'请选择审核状态','many'=>array(
                    array('value'=>'0', 'content'=>'等待中'),
                    array('value'=>'1','content'=>'申诉成功'),
                )),
                array('title'=>"回复",'type'=>"textarea2",'name'=>"reply",'value'=>'reply'),
            ),
            U('Labor/appeal',array('token'=>$this->_sToken)),
            array($this,'appealset'));

    }
    public function appealset($data){
        $data['token'] = $this->_sToken;
        $data['read_time'] = date('Y-m-d H:i:s');
        return $data;
    }
    /*审核回复*/
    public function save_appeal(){
        $this->set_appeal(Edit);
    }
    /*删除数据*/
    public function delete_appeal(){
        $this->del('Labor_appeal');
    }


    /*公司的招聘（公司）*/
    public function recruitment(){
        $aWhere = array(
            'token'=>$this->_sToken,
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/text5', array('token' => $this->_sToken,'type'=>5)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加公司',//2级
                        'url'    => U('Labor/add_recruitment', array('token' => $this->_sToken,'type'=>5)),
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/text5', array('token' => $this->_sToken,'type'=>5)),
                    ),



                ),
                'tips' => array(//3级
                    '你可以在这里管理平台内所有公司的<span style="color: #ff0315;font-size: 16px;">“招聘信息”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '公司名称','联系人','联系电话','公司地址','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看职位详情',
                        'url'  => U('Labor/message',array('token'=>$this->_sToken))
                    ),
                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_recruitment',array('token'=>$this->_sToken))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_recruitment',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_recruitment')->where($aWhere)->count(),
            M('Labor_recruitment')->field('id,gname,person,phone,address')->where($aWhere)->order("id desc"),
            array($this,'recruitmentinfo')
        );
    }
    public function recruitmentinfo($data){
        return $data;
    }
    public function set_recruitment($aaa){
        $this->$aaa('Labor_recruitment',
            array(
                array('title'=>"公司名称",'type'=>"input",'name'=>"gname",'value'=>'gname','msg'=>'请填写公司名称','bast'=>''),
                array('title'=>"公司地址",'type'=>"input",'name'=>"address",'value'=>'address','msg'=>'请填写公司地址','bast'=>''),
                array('title'=>"联系电话",'type'=>"input",'name'=>"phone",'value'=>'phone','msg'=>'请填写联系电话','bast'=>''),
                array('title'=>"联系人",'type'=>"input",'name'=>"person",'value'=>'person','msg'=>'请填写联系人','bast'=>''),
                array('title'=>"邮箱",'type'=>"input",'name'=>"email",'value'=>'email','msg'=>'请填写招聘邮箱','bast'=>''),
                array('title'=>"公司简介",'type'=>"textarea",'name'=>"abstract",'value'=>'abstract'),

            ),
            U('Labor/recruitment',array('token'=>$this->_sToken)),
            array($this,'recruitmentset'));

    }
    public function recruitmentset($data){
        $data['tid'] = $this->_iTid;
        $data['token'] = $this->_sToken;
        return $data;
    }
    public function add_recruitment(){
        $this->set_recruitment(add);
    }

    public function save_recruitment(){
        $this->set_recruitment(Edit);
    }
    /*删除数据*/
    public function delete_recruitment(){
        $this->del('Labor_recruitment');
    }







    /*公司的招聘（职位）*/
    public function message(){
        $aWhere = array(
            'token'=>$this->_sToken,
        );
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Labor/text5', array('token' => $this->_sToken,'type'=>5)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加职位',//2级
                        'url'    => U('Labor/add_message', array('token' => $this->_sToken,'rid'=>$_GET['id'])),
                    ),

                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Labor/recruitment', array('token' => $this->_sToken)),
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理平台内所有公司的<span style="color: #ff0315;font-size: 16px;">“招聘信息”</span>的相关内容。',
                ),
                'Table_Header' => array(//4级
                    'ID', '公司名称','职位名称','招聘人数','工作地址','开始时间','结束时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Labor/save_message',array('token'=>$this->_sToken,'rid'=>$_GET['id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Labor/delete_message',array('token'=>$this->_sToken))
                    ),
                ),
                /*
                'search'=>array(
                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('Labor_message')->where($aWhere)->count(),
            M('Labor_message')->field('id,rid,office,number,address,starttime,endtime')->where($aWhere)->order("id desc"),
            array($this,'messageinfo')
        );
    }
    public function messageinfo($data){
        return $data;
    }
    public function set_message($aaa){
        $this->$aaa('Labor_message',
            array(
                array('title'=>"职位名称",'type'=>"input",'name'=>"office",'value'=>'office','msg'=>'请填写职位名称','bast'=>''),
                array('title'=>'职位描述','type'=>"textarea",'name'=>"description",'value'=>'description'),
                array('title'=>"职位要求",'type'=>"textarea_1",'name'=>"requirement",'value'=>'requirement'),
                array('title'=>"工作地址",'type'=>"input",'name'=>"address",'value'=>'address','msg'=>'请填写工作地址','bast'=>''),
                array('title'=>"招聘人数",'type'=>"number",'name'=>"number",'value'=>'number','msg'=>'请填写要招聘的人数','bast'=>''),
                array('title'=>"招聘开始时间",'type'=>"time",'name'=>"starttime",'msg'=>'请填写招聘开始时间','value'=>'starttime'),
               	array('title'=>"招聘结束时间",'type'=>"time",'name'=>"endtime",'msg'=>'请填写招聘结束时间','value'=>'endtime'),
            ),
            U('Labor/message',array('token'=>$this->_sToken,'id'=>$_GET['rid'])),
            array($this,'messageset'));

    }
    public function messageset($data){
        $data['rid'] = $_GET['rid'];
        $data['tid'] = $this->_iTid;
        $data['token'] = $this->_sToken;
        return $data;
    }
    public function add_message(){
        $this->set_message(add);
    }

    public function save_message(){
        $this->set_message(Edit);
    }
    /*删除数据*/
    public function delete_message(){
        $this->del('Labor_message');
    }
}