<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class JdUserAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/together';

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
        $this->pz	   = D('jd_user');
        $this->token=session('token');
        
        //是否显示
        $this->is_show='is_show';//改这
        $this->assign('is_show',$this->is_show);
        //是否审核
        $this->state='state';//改这
        $this->assign('state',$this->state);
       
        
    }
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '顾问管理',
                'url'  => U('JdAdviser/index', array('token' => $this->_sToken))
            ),
        
            array(
                'name'=>'方案管理',
                'url'  => U('JdWz/index', array('token' => $this->_sToken))
            ),
            array(
                'name'=>'用户管理',
                'url'  => U('JdUser/index', array('token' => $this->_sToken))
            ),
            array(
                'name'=>'招标方案与活动管理',
                'url'  => U('JdUser/tender', array('token' => $this->_sToken))
            ),
            array(
                'name'=>'标书管理',
                'url'  => U('JdUser/scale', array('token' => $this->_sToken))
            ),
            array(
                'name'=>'前端会员入会须知编辑管理',
                'url'  => U('JdUser/text', array('token' => $this->_sToken))
            ),
            array(
                'name'=>'评价管理',
                'url'  => U('JdUser/evalute', array('token' => $this->_sToken))
            ),

                   
        );
    }
    //显示
    public function index(){
    	
    	
    	//array('token'=>$_SESSION['token'])
    	$aWhere['token']=$_SESSION['token'];
        //$_SESSION['token']=$_SESSION['token'];
             	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$_SESSION['token'];
                    session('where_p',null);
                    session('where_p',$aWhere);
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
                }else{
                    if(session('?where_p')){
                        $aWhere=session('where_p');
                    }
                }
                
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdUser/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加',//2级
                        'url'    => U('JdUser/add_content',array('token'=>$_SESSION['token']))
                    ),
                		array(
                				'name'   => 'EXCEL导入',//2级
                				'url'    => U('Usercenter/dl',array('token'=>$_SESSION['token'],'id'=>3730))
                		)
                ),
                'tips' => array(//3级
                    '<b style="font-size:14px;" >特别注意:点V图标可取消审核，点X图际可通过审核</b>'
                ),
                /*'Table_Header' => array(//4级
                    'ID', '用户名','实名', '邮箱', '手机号码', 'QQ', '头像','组织类别','会员级别', '是否启用','审核状态','操作'
                ),*/
                'Table_Header' => array(//4级
                    'ID', '用户名','实名','手机号码','组织类别','组织全称','会员级别','审核时间','vip启用时间','是否启用','审核状态','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('JdUser/xx',array('token'=>$_SESSION['token']))
                                            ), */

                  /*  array(
                        'name' => '审核时间',
                        'url'  => U('JdUser/time1',array('token'=>$_SESSION['token']))
                    ),*/
                    array(
                        'name' => '编辑',
                        'url'  => U('JdUser/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdUser/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                       		//搜索
                                'search'=>array(
                                                array('title'=>'姓名','name'=>'li_name','placeholder'=>'请输入姓名模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'手机','name'=>'li_phone','placeholder'=>'请输入手机模糊查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                               // array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                                )//结束
            ),
            M('jd_user')->where($aWhere)->count(),
            M('jd_user')->field('id,name,true_name,phone,type,hb,grade,sp_time,vip_time,is_show,state')->where($aWhere)->order("id desc"),
        array($this,'abc')
        );
    }
    //审核时间
    public function time1(){
        $info=M('jd_user')->field('id,vip_time,sp_time')->find($_GET['id']);
       // p($info);
        $this->assign('info',$info);
        $this->display();
    }
    public function abc($data){
        foreach($data as $k=>$v){
           $data[$k]['vip_time']= $v['vip_time']?date('Y-m-d H:i',$v['vip_time']):'';
            $data[$k]['sp_time']= $v['sp_time']?date('Y-m-d H:i',$v['sp_time']):'';
           // $data[$k]['head']="<img style='width:50px' height:50px; src='{$v['head']}' />";
           /* $data[$k]['name']=str_substr($v['name'],10,'...');
            $data[$k]['email']=str_substr($v['email'],15,'...');*/
            switch ($v['type']){
                case 0:$data[$k]['type']='总部';break;
                case 1:$data[$k]['type']='机构';break;
                case 2:$data[$k]['type']='伙伴';break;
            }
            switch ($v['grade']){
                case 0:$data[$k]['grade']='普通';break;
                case 1:$data[$k]['grade']='VIP';break;
               default:$data[$k]['grade']='其他';break;
            }
          /*  $data[$k]['jg']=$v['jg']?$v['jg']:'是伙伴';
            $data[$k]['hb']=$v['hb']?$v['hb']:'是机构';*/
        }
        return $data;
    }
    //定义增改函数
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('JdUser2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
                } */
        $this->$aaa('jd_user',array(
            array('title'=>"用户实名",'type'=>"input",'name'=>"true_name",'value'=>'true_name','msg'=>'请填写用户实名'),
            array('title'=>"用户帐号",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写用户帐号'),

            array('title'=>"密码",'type'=>"password",'name'=>"password",'value'=>'password','msg'=>'请填写密码'),
            array('title'=>"邮箱",'type'=>"input",'name'=>"email",'value'=>'email','msg'=>'请填写邮箱'),
            array('title'=>"手机号码",'type'=>"input",'name'=>"phone",'value'=>'phone','msg'=>'请填写手机号码'),
            array('title'=>"QQ",'type'=>"input",'name'=>"qq",'value'=>'qq','msg'=>'请填写QQ'),
            array('type'=>'img','many'=>array(
                array('title'=>"头像",'type'=>"img",'name'=>"head",'value'=>'head','whidth'=>50,'height'=>50),
            )),
            array('type'=>'radio','title'=>"组织类别",'name'=>"type",'value'=>'type'/*,'msg'=>'请选择组织类别'*/,'many'=>array(
                array('value'=>'0','content'=>'总部'),
                array('value'=>'1','content'=>'机构'),
                array('value'=>'2','content'=>'伙伴'),
            )),
            array('type'=>'radio','title'=>"会员级别",'name'=>"grade",'value'=>'grade'/*,'msg'=>'请选择组织类别'*/,'many'=>array(
                array('value'=>'0','content'=>'普通会员'),
                array('value'=>'1','content'=>'VIP'),
            )),
        		//array('title'=>"机构",'type'=>"input",'name'=>"jg",'value'=>'jg','tishi'=>'机构与个人二选一'),
        		array('title'=>"组织名称",'type'=>"input",'name'=>"hb",'value'=>'hb','tishi'=>'填写完整的组织的名称'),

        ),U('JdUser/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){

        $data['password']=MD5($data['password']);

        //$data['type']=1;
      /*  $data['is_show'] = 1;
        $data['state'] = 1;*/
        if((!isset($data['vip_time']))&&($data['grade']==1) &&($data['state'] = 1)){//VIP
            $data['vip_time']=time();
        }
        if($data['grade']==0){
            $data['vip_time']='';
        }
        return $data;
    }
    //添加
    public function add_content(){
        $this->add_save(add);
    }
    //编辑
    public function save_content(){
     //   $this->add_save(Edit);
        $this->Edit('jd_user',array(
            array('title'=>"用户实名",'type'=>"input",'name'=>"true_name",'value'=>'true_name'),
            array('title'=>"用户帐号",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写用户名'),
            array('title'=>"密码",'type'=>"password",'name'=>"password",'value'=>'password','msg'=>'请填写密码'),
            array('title'=>"邮箱",'type'=>"input",'name'=>"email",'value'=>'email','msg'=>'请填写邮箱'),
            array('title'=>"手机号码",'type'=>"input",'name'=>"phone",'value'=>'phone','msg'=>'请填写手机号码'),
            array('title'=>"QQ",'type'=>"input",'name'=>"qq",'value'=>'qq','msg'=>'请填写QQ'),
            array('type'=>'img','many'=>array(
                array('title'=>"头像",'type'=>"img",'name'=>"head",'value'=>'head','whidth'=>50,'height'=>50),
            )),
            array('type'=>'radio','title'=>"组织类别",'name'=>"type",'value'=>'type'/*,'msg'=>'请选择组织类别'*/,'many'=>array(
                array('value'=>'0','content'=>'总部'),
                array('value'=>'1','content'=>'机构'),
                array('value'=>'2','content'=>'伙伴'),
            )),
            array('type'=>'radio','title'=>"会员级别",'name'=>"grade",'value'=>'grade'/*,'msg'=>'请选择组织类别'*/,'many'=>array(
                array('value'=>'0','content'=>'普通会员'),
                array('value'=>'1','content'=>'VIP'),
            )),
            //array('title'=>"机构",'type'=>"input",'name'=>"jg",'value'=>'jg','tishi'=>'机构与个人二选一'),
            array('title'=>"组织名称",'type'=>"input",'name'=>"hb",'value'=>'hb','tishi'=>'填写完整的组织的名称'),

        ),U('JdUser/index',array('token'=>$_SESSION['token'])),array($this,'saveinfo'));
    }
    public function saveinfo($data){
        if(strlen($data['password'])>= 32){
            $data['password'] = $data['password'];
        }else{
            $data['password'] =md5( $data['password']);
        }
        $user = M('Jd_user')->where(array('name'=>$data['name']))->find();
            if((!isset($data['vip_time']))&&($data['grade']==1)){//VIPda
                if($user['state'] ==1){
                    $data['vip_time']=time();
                }else{
                    $this->error2('您现在还未被审核，不能开通VIP会员');
                }

            }


        if($data['grade']==0){
            $data['vip_time']='';
        }
        return $data;
    }
    //删除
    public function delete_content(){
        $info = M('jd_user')->where(array('id'=>$_GET['id']))->find();//删除其它信息
        if($info['state'] ==1){
            $this->error2('该用户为已审核的用户,不能删除');
        }else{
            $this->del('jd_user');
        }

    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        if($_POST['ACTION_NAME'] == 'index'){
            $this->sortajaxTable('jd_user');
        }elseif($_POST['ACTION_NAME'] == 'tender'){
            $this->sortajaxTable('Jd_tender');
        }elseif($_POST['ACTION_NAME'] == 'scale'){
            $this->sortajaxTable('Jd_scale');
        }
    }

    //是否显示  只 能主健是id, 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
        $info=M('Jd_user')->field('name,email')->where(array('id'=>$_POST['id']))->find();
        if($_POST['is_show']==1) {//不启用
            send_email('请注意查收','尊敬的'.$info['name'].'用户，很抱歉的通知，您的会籍资格被暂时禁用，如有疑问，请联系管理员！',$info['email']);
        }
        if($_POST['is_show']==0) {//不启用
            M('Jd_user')->where(array('id'=>$_POST['id']))->save(array('sp_time'=>time()));
            send_email('请注意查收','邮件内容为，尊敬的'.$info['name'].'用户，很高兴的通知，您的会籍资格已通过！',$info['email']);
        }
        $this->is_showAjaxTable('jd_user',$this->is_show);
    }
//seeuser
    //是否审核  只 能主健是id, 才能是否审核   有意见自己去开发！
    public function stateAjax(){
       // echo 88;die;
        if($_POST['ACTION_NAME'] == 'index'){
            $info=M('Jd_user')->field('name,email')->where(array('id'=>$_POST['id']))->find();
            if($_POST['state']==1){//取消审核
                send_email('请注意查收','尊敬的'.$info['name'].'用户，很抱歉的通知，您的会籍资格被暂时禁用，如有疑问，请联系管理员！',$info['email']);
            }
            if($_POST['state']==0){//通过审核
                send_email('请注意查收','邮件内容为，尊敬的'.$info['name'].'用户，很高兴的通知，您的会籍资格已通过！',$info['email']);
            }
            $this->stateAjaxTable('jd_user',$this->state);
        }elseif($_POST['ACTION_NAME'] == 'tender'){
            $this->stateAjaxTable('Jd_tender',$this->state);
        }elseif($_POST['ACTION_NAME'] == 'scale'){
            $this->stateAjaxTable('Jd_scale',$this->state);
        }elseif($_POST['ACTION_NAME'] == 'seeuser'){
            $this->stateAjaxTable('Jd_registration',$this->state);
        }
    }

    /*招标方案与活动列表*/
    public function tender(){
        $aWhere['token']=$_SESSION['token'];
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdUser/tender', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加',//2级
                        'url'    => U('JdUser/add_tender',array('token'=>$this->_sToken))
                    ),
                ),
                'tips' => array(//3级
                    '<b style="font-size:14px;" >特别注意:点V图标可取消审核，点X图际可通过审核</b>'
                ),
                'Table_Header' => array(//4级
                    'ID', '编码', '主题', '类型', '状态', '开始日期','截至日期','目标奖金', '点赞数','分亨次数','排序号','审核状态','操作'
                ),
                'List_Opt' => array(


                    array(
                        'name' => '编辑',
                        'url'  => U('JdUser/save_tender',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '查看报名',
                        'url'  => U('JdUser/seeuser',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '标书管理',
                        'url'  => U('JdUser/scale',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '查看评论',
                        'url'  => U('JdUser/evalute',array('token'=>$_SESSION['token'],'type'=>'active'))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdUser/delete_tender',array('token'=>$_SESSION['token']))
                    ),
                ),
                /*//搜索
                'search'=>array(
                    array('title'=>'姓名','name'=>'li_name','placeholder'=>'请输入姓名模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                    array('title'=>'手机','name'=>'li_phone','placeholder'=>'请输入手机模糊查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                    // array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束*/
            ),
            M('Jd_tender')->where($aWhere)->count(),
            M('Jd_tender')->field('id,code,title,type,status,startday,endday,money,number,fx_num,sort,state')->where($aWhere)->order("sort desc, id desc"),
            array($this,'tenderindex')
        );
    }
    public function tenderindex($data){
        foreach($data as $k=>$v){

            switch ($v['status']){
                case 0:$data[$k]['status']='发布中';break;
                case 1:$data[$k]['status']='招标中';break;
                case 2:$data[$k]['status']='评审中';break;
                case 3:$data[$k]['status']='结束';break;
                default:$data[$k]['status']='其他';break;
            }
            switch ($v['type']){
                case 0:$data[$k]['type']='方案招标';break;
                case 1:$data[$k]['type']='活动报名';break;
                default:$data[$k]['type']='其他';break;
            }
            /*  $data[$k]['jg']=$v['jg']?$v['jg']:'是伙伴';
              $data[$k]['hb']=$v['hb']?$v['hb']:'是机构';*/
        }
        return $data;
    }

    public function set_tender($aaa){
        $this->$aaa('Jd_tender',array(
            array('title'=>"编码",'type'=>"input",'name'=>"code",'value'=>'code','msg'=>'请填写编码'),
            array('title'=>"主题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写主题'),
            array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic','width'=>50,'height'=>50),
            )),
            array('title'=>"描述",'type'=>"textarea2",'name'=>"abstract",'value'=>'abstract'),
            array('type'=>'select','title'=>"状态",'name'=>"status",'value'=>'status','msg'=>'请选择状态','many'=>array(
                array('value'=>'0', 'content'=>'发布中'),
                array('value'=>'1','content'=>'招标中'),
                array('value'=>'2','content'=>'评审中'),
                array('value'=>'3','content'=>'结束'),
            )),
            array('type'=>'radio','title'=>"类型",'name'=>"type",'value'=>'type'/*,'msg'=>'请选择组织类别'*/,'many'=>array(
                array('value'=>'0','content'=>'方案招标'),
                array('value'=>'1','content'=>'活动报名'),
            )),
            array('title'=>"开始时间",'type'=>"time",'name'=>"startday",'msg'=>'请填开始时间','value'=>'startday'),
            array('title'=>"结束时间",'type'=>"time",'name'=>"endday",'msg'=>'请填结束时间','value'=>'endday'),
            array('title'=>"编目标奖金",'type'=>"input",'name'=>"money",'value'=>'money','msg'=>'请填写编目标奖金'),
            array('title'=>"附件链接地址",'type'=>"input",'name'=>"activeurl",'value'=>'activeurl','msg'=>'请填写附件链接地址'),

        ),U('JdUser/tender',array('token'=>$_SESSION['token'])),array($this,'tenderbbc'));

    }
    public function tenderbbc($data){
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['token'] = $this->_sToken;
        //$data['type']=1;

        return $data;
    }
    //添加
    public function add_tender(){
        $this->set_tender(add);
    }
    //编辑
    public function save_tender(){
        $this->set_tender(Edit);
    }
    //删除
    public function delete_tender(){
        if(M('Jd_tender')->where(array('id'=>$_GET['id']))->getField('state') ==1){
            $this->error2('方案已审核，不能删除');
        }else{
            $this->del('Jd_tender');
        }

    }

    /*交标列表*/
    public function scale(){
        $aWhere['token']=$_SESSION['token'];
        if($_GET['id']){
            $aWhere['tid'] = $_GET['id'];
        }
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdUser/scale', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name' => '新增',
                        'url'  => U('JdUser/add_scale',array('token'=>$_SESSION['token'],'tid'=>$_GET['id']))
                    ),
                ),
                'tips' => array(//3级
                    '<b style="font-size:14px;" >特别注意:点V图标可取消审核，点X图际可通过审核</b>'
                ),
                'Table_Header' => array(//4级
                    'ID','招标主题', '编码', '标书名称', '作者', '交标日期','组织名称','排序号','审核状态','操作'
                ),
                'List_Opt' => array(


                    array(
                        'name' => '编辑',
                        'url'  => U('JdUser/save_scale',array('token'=>$_SESSION['token']))
                    ),

                    array(
                        'name' => '删除',
                        'url'  => U('JdUser/delete_scale',array('token'=>$_SESSION['token']))
                    ),
                ),
                /*//搜索
                'search'=>array(
                    array('title'=>'姓名','name'=>'li_name','placeholder'=>'请输入姓名模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                    array('title'=>'手机','name'=>'li_phone','placeholder'=>'请输入手机模糊查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                    // array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束*/
            ),
            M('Jd_scale')->where($aWhere)->count(),
            M('Jd_scale')->field('id,tid,code,tender,author,time,name,sort,state')->where($aWhere)->order("id desc"),
            array($this,'scaleindex')
        );
    }
    public function scaleindex($data){
        foreach($data as $key=>$val){
            $info = M('Jd_tender')->where(array('id'=>$val['tid']))->find();
            $data[$key]['tid'] = $info['title'];
        }
        return $data;
    }

    public function set_scale($aaa){
        //特殊点选框,复选框,下拉列表

        $aClassify = M('Jd_tender')->field('id as value,title as content')->where(array('token'=>$_SESSION['token']))->select();
        $aClassify = array_merge(array(array('value'=>0,'content'=>'选择分类')),$aClassify);

        $this->$aaa('Jd_scale',array(
            array('type'=>'select','title'=>"招标主题",'name'=>"tid",'value'=>'tid','msg'=>'请选择招标主题','many'=>$aClassify),
            array('title'=>"编码",'type'=>"input",'name'=>"code",'value'=>'code','msg'=>'请填写编码'),
           // array('title'=>"主题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写主题'),
            array('title'=>"标书名称",'type'=>"input",'name'=>"tender",'value'=>'tender','msg'=>'请填写标书名称'),
            array('title'=>"作者",'type'=>"input",'name'=>"author",'value'=>'author','msg'=>'请填写作者'),
            array('title'=>"联系手机",'type'=>"input",'name'=>"phone",'value'=>'phone','msg'=>'请填写联系手机'),
            array('title'=>"投标日期",'type'=>"time",'name'=>"time",'msg'=>'请填招标日期','value'=>'time'),
            array('title'=>"组织名称",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写组织名称'),

            array('type'=>'select','title'=>"组织类别",'name'=>"classify",'value'=>'classify','msg'=>'请选择组织类别','many'=>array(
                array('value'=>'', 'content'=>'请选择组织类别'),
                array('value'=>'0', 'content'=>'总部'),
                array('value'=>'1','content'=>'机构'),
                array('value'=>'2','content'=>'伙伴'),
            )),
            array('title'=>"标书链接地址",'type'=>"input",'name'=>"activeurl",'value'=>'activeurl','msg'=>'请填写附件链接地址'),
        ),U('JdUser/scale',array('token'=>$_SESSION['token'])),array($this,'scalebbc'));

    }
    public function scalebbc($data){
        if($_GET['tid']){
            $data['tid'] =$_GET['id'];
        }
        $data['state'] = 1;
        $data['title'] = M('Jd_tender')->where(array('id'=>$data['tid']))->getField('title');
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['token'] = $this->_sToken;
        //$data['type']=1;
        return $data;
    }
    public function add_scale(){
        $this->set_scale(add);
    }
    //编辑
    public function save_scale(){
        $this->set_scale(Edit);
    }
    //删除
    public function delete_scale(){
        if(M('Jd_scale')->where(array('id'=>$_GET['id']))->getField('state') ==1){
            $this->error2('标书已审核，不能删除');
        }else{
            $this->del('Jd_scale');
        }
        //M('jd_user')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息

    }

    /*查看报名情况*/
    public function seeuser(){
        $aWhere['token']=$_SESSION['token'];
        if($_GET['id']){
            $aWhere['tid'] = $_GET['id'];
        }
        $info = M('Jd_tender')->where(array('id'=>$_GET['id']))->find();
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdUser/tender', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('JdUser/tender',array('token'=>$this->_sToken))
                    ),
                ),
                'tips' => array(//3级
                    '在此处查看“<span style="font-size: 16px;font-weight: 600;">'.$info['title'].'</span>”详情',
                    '<b style="font-size:14px;" >特别注意:点V图标可取消审核，点X图际可通过审核</b>'
                ),
                'Table_Header' => array(//4级
                    'ID', '编码', '主题', '报名类别', '参与人', '联系方式','邮箱', '组织名称','是否投标','审核状态','操作'
                ),
                'List_Opt' => array(


                    array(
                        'name' => '编辑',
                        'url'  => U('JdUser/save_registration',array('token'=>$_SESSION['token']))
                    ),

                    array(
                        'name' => '删除',
                        'url'  => U('JdUser/delete_registration',array('token'=>$_SESSION['token']))
                    ),
                ),
                /*//搜索
                'search'=>array(
                    array('title'=>'姓名','name'=>'li_name','placeholder'=>'请输入姓名模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                    array('title'=>'手机','name'=>'li_phone','placeholder'=>'请输入手机模糊查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                    // array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束*/
            ),
            M('Jd_registration')->where($aWhere)->count(),
            M('Jd_registration')->field('id,code,title,type,name,phone,email,organization,status,state')->where($aWhere)->order("id desc"),
            array($this,'registrationindex')
        );
    }
    public function registrationindex($data){
        foreach($data as $k=>$v){
            switch ($v['type']){
                case 0:$data[$k]['type']='个人';break;
                case 1:$data[$k]['type']='团体';break;
                default:$data[$k]['type']='其他';break;
            }
            switch ($v['status']){
                case 1:$data[$k]['status']='是';break;
                case 2:$data[$k]['status']='否';break;
                default:$data[$k]['status']='其他';break;
            }
            /*  $data[$k]['jg']=$v['jg']?$v['jg']:'是伙伴';
              $data[$k]['hb']=$v['hb']?$v['hb']:'是机构';*/
        }
        return $data;
    }

    public function set_registration($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('JdUser2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
                } */
        $this->$aaa('Jd_registration',array(
            array('title'=>"编码",'type'=>"input",'name'=>"code",'value'=>'code','msg'=>'请填写编码'),
            array('title'=>"主题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写主题'),
            array('type'=>'radio','title'=>"报名类别",'name'=>"type",'value'=>'type'/*,'msg'=>'请选择组织类别'*/,'many'=>array(
                array('value'=>'0','content'=>'个人'),
                array('value'=>'1','content'=>'团队'),
            )),

            array('title'=>"参与人",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写主题'),
            array('title'=>"邮箱",'type'=>"input",'name'=>"email",'value'=>'email','msg'=>'请填写主题'),
            array('title'=>"联系手机",'type'=>"input",'name'=>"phone",'value'=>'phone','msg'=>'请填写主题'),
            array('type'=>'select','title'=>"组织类别",'name'=>"otype",'value'=>'otype','msg'=>'请选择状态','many'=>array(
                array('value'=>'', 'content'=>'请选择组织类别'),
                array('value'=>'0', 'content'=>'总部'),
                array('value'=>'1','content'=>'机构'),
                array('value'=>'2','content'=>'伙伴'),
            )),
            array('type'=>'select','title'=>"是否投标",'name'=>"status",'value'=>'status','msg'=>'请选择状态','many'=>array(
                array('value'=>'', 'content'=>'请选择是否投标'),
                array('value'=>'1', 'content'=>'是'),
                array('value'=>'2','content'=>'否'),

            )),
            array('title'=>"组织名称",'type'=>"input",'name'=>"organization",'value'=>'organization','msg'=>'请填写主题'),

           // array('title'=>"标书链接地址",'type'=>"input",'name'=>"activeurl",'value'=>'activeurl','msg'=>'请填写附件链接地址'),
        ),U('JdUser/seeuser',array('token'=>$_SESSION['token'],'id'=>$_GET['id'])),array($this,'registrationbbc'));

    }
    public function registrationbbc($data){
        $data['token'] = $this->_sToken;
        //$data['type']=1;
        return $data;
    }

    //编辑
    public function save_registration(){
        $this->set_registration(Edit);
    }
    //删除
    public function delete_registration(){
        //M('jd_user')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Jd_registration');
    }


    public function text(){
        $aWhere = array('token'=>$_SESSION['token'],'type'=>0);
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdUser/text', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加',//2级
                        'url'    => U('JdUser/add_text',array('token'=>$this->_sToken))
                    ),
                ),
                'tips' => array(//3级
                   '您可以在管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID',  '主题', '添加时间','操作'
                ),
                'List_Opt' => array(


                    array(
                        'name' => '编辑',
                        'url'  => U('JdUser/save_text',array('token'=>$_SESSION['token']))
                    ),

                    array(
                        'name' => '删除',
                        'url'  => U('JdUser/delete_text',array('token'=>$_SESSION['token']))
                    ),
                ),
                /*//搜索
                'search'=>array(
                    array('title'=>'姓名','name'=>'li_name','placeholder'=>'请输入姓名模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                    array('title'=>'手机','name'=>'li_phone','placeholder'=>'请输入手机模糊查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                    // array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束*/
            ),
            M('Jd_text')->where($aWhere)->count(),
            M('Jd_text')->field('id,title,add_time')->where($aWhere)->order("id desc"),
            array($this,'textindex')
        );
    }
    public function textindex($data){

        return $data;
    }


public function set_text($aaa){
    $this->$aaa('Jd_text',array(
        array('title'=>"主题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写主题'),
        array('title'=>"内容",'type'=>"textarea",'name'=>"content",'value'=>'content'),
    ),U('JdUser/text',array('token'=>$_SESSION['token'],'id'=>$_GET['id'])),array($this,'textinfo'));

}
public function textinfo($data){
    $data['token'] = $this->_sToken;
    $data['add_time']=date('Y-m-d H:i:s');
    //$data['type']=1;
    return $data;
}

    public function add_text(){
        $this->set_text(add);
    }

    //编辑
    public function save_text(){
        $this->set_text(Edit);
    }
//删除
    public function delete_text(){
    //M('jd_user')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Jd_text');
    }

/*评价详情*///JdUser_evalute
    public function evalute(){
        $style = $_GET['type'];
        $tid = $_GET['id'];
        $this->evalutelist($style,$tid);
        $this->display('evalute');
    }


    public function evalutelist($style,$tid){
        $oModel = M('Jd_evaluation');
        if($style !=''){
            $count = $oModel
                ->where(array('style'=>$style,'tid'=>$tid,'token'=>$_SESSION['token']))
                ->count();
            $page = new Page($count,25);
            $list = $oModel
                ->where(array('style'=>$style,'tid'=>$tid,'token'=>$_SESSION['token']))
                ->limit($page->firstRow.','.$page->listRows)
                ->order('add_time desc,path desc')
                ->select();
        }else{
            $count = $oModel
                ->where(array('token'=>$_SESSION['token']))
                ->count();
            $page = new Page($count,25);
            $list = $oModel
                ->where(array('token'=>$_SESSION['token']))
                ->limit($page->firstRow.','.$page->listRows)
                ->order('add_time desc,path desc')
                ->select();
        }

        foreach($list as $key=>$val){
            if($val['style'] == 'plan'){
                $info = M('Jd_wz') ->where(array('id'=>$val['tid']))->find();
                $list[$key]['code'] = "";
                $list[$key]['title'] = $info['title'];
                $list[$key]['type'] = $info['type'];
            }elseif($val['style'] == 'active'){
                $info = M('Jd_tender') ->where(array('id'=>$val['tid']))->find();
                $list[$key]['code'] =$info['code'];
                $list[$key]['title'] = $info['title'];
                $list[$key]['type'] = $info['type'];
            }
            if($val['pid'] == 0){
                $list[$key]['upuname'] =$val['uname'];
                $list[$key]['upcontent'] = $val['content'];
                $list[$key]['uname'] = '';
                $list[$key]['content'] ='';
            }else{
                $contents = $oModel->where(array('id'=>$val['pid']))->find();
                $list[$key]['upuname'] = $contents['uname'];
                $list[$key]['upcontent'] = $contents['content'];
            }
        }
        $this->assign(array(
            'list' => $list,
            'page' => $page->show()
        ));
    }

    /*删除*/
    public function delevalute(){
        $this->del('Jd_evaluation');
    }

    public function ajaxe(){
        $omodel = M('Jd_evaluation');
        if(IS_AJAX){
            if($omodel->where(array('id'=>$_POST['id']))->find()){
                $data['state'] = $_POST['type'];
                if($omodel->where(array('id'=>$_POST['id']))->save($data)){
                    $this->success('操作成功！');
                }else{
                    $this->error('操作失败');
                }
            }else{
                $this->error('非法操作！');
            }
        }
        //print_r($_POST);
    }


}

