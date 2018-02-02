<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南(anyi路况在线)
 **/
class RoadAction extends TableAction {
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
    //一级
    protected function setHeader(){
        return array(
            array(
                'name' => '交通资讯管理',
                'url'  => U('Road/traffic', array('token' => $this->_sToken))
            ),
            array(
                'name' => '高速快览管理',
                'url'  => U('Road/quickfacts', array('token' => $this->_sToken))
            ),

        );
    }
    //显示
    public function traffic(){
        $this->table(
            array(
               // 'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Road/traffic', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加分类',//2级
                        'url'    => U('Road/traffic_add',array('token'=>$_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '分类名', '添加时间','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '咨询详情',
                        'url'  => U('Road/information',array('token'=>$_SESSION['token']))
                    ),

                    array(
                        'name' => '编辑',
                        'url'  => U('Road/traffic_save',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/traffic_delete',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('Road_traffic_classify')->where(array('token'=>$_SESSION['token']))->count(),
            M('Road_traffic_classify')->field('id,cname,add_time')->where(array('token'=>$_SESSION['token']))->order("id")
        // array($this,'abc')
        );
    }

    //定义增改函数
    public function add_save($aaa){
        $this->$aaa('Road_traffic_classify',array(
            array('title'=>"分类名称",'type'=>"input",'name'=>"cname",'value'=>'cname','msg'=>'请填写分类名称','bast'=>'建议名称不超过四个字'),
        ),U('Road/traffic',array('token'=>$_SESSION['token'])),array($this,'defind'));

    }
    public function defind($data){
        $data['token'] = $_SESSION['token'];
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    //添加
    public function traffic_add(){
        $this->add_save(add);
    }
    //编辑
    public function traffic_save(){
        $this->add_save(Edit);
    }
    //删除
    public function traffic_delete(){
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Road_traffic_classify');
    }


/*资讯详情*/
    public function information(){
        $this->table(
            array(
                'HeadHover' => U('Road/information', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加资讯',//2级
                        'url'    => U('Road/information_add',array('token'=>$_SESSION['token'],'cid'=>$_GET['id']))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Road/traffic',array('token'=>$_SESSION['token'],'cid'=>$_GET['id']))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '分类名称', '资讯标题','发布人','是否发布', '添加时间','操作'
                ),
                'List_Opt' => array(



                    array(
                        'name' => '查看',
                        'url'  => U('Road/save_information',array('token'=>$_SESSION['token'],'cid'=>$_GET['id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/delete_information',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('Road_traffic_information')->where(array('token'=>$_SESSION['token'],'cid'=>$_GET['id']))->count(),
            M('Road_traffic_information')->field('id,cid,title,openid,is_release,add_time')->where(array('token'=>$_SESSION['token'],'cid'=>$_GET['id'])),
            array($this,'xxabc')
        );
    }
    public function xxabc($data){
        foreach($data as $k=>$val){
            $aClassify = M('Road_traffic_classify')->where(array('id'=>$val['cid']))->find();
            $data[$k]['cid'] = $aClassify['cname'];
            if($val['openid'] == ''){
                $data[$k]['openid'] = '本部';
            }else{
                $user = M('Wxuser')->where(array('token'=>$_SESSION['token']))->find();
                $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$val['openid']))->find();
                $data[$k]['openid'] = $users['nickname'];
            }
            if($val['is_release'] == 1 && $val['openid'] == ''){
                $data[$k]['is_release'] = '发布';
            }elseif($val['is_release'] == 1 && $val['openid'] != ''){
                $data[$k]['is_release'] = '审核通过，已发布';
            }else{
                $data[$k]['is_release'] = '未审核';
            }
        }
        return $data;
    }
    //定义增改函数
    public function set_information($aaa){
        $aClassify = M('Road_traffic_classify')->field('id as value,cname as content')->where(array('token'=>$_SESSION['token']))->select();
        $aClassify = array_merge(array(array('value'=>0,'content'=>'选择分类')),$aClassify);

        $this->$aaa('Road_traffic_information',array(

            array('type'=>'select','title'=>"资讯分类",'name'=>"cid",'value'=>'cid','msg'=>'选择分类','many'=>$aClassify),
            array('title'=>"资讯标题",'type'=>"input",'name'=>"title",'value'=>'title','msg'=>'请填写资讯标题','bast'=>''),
            array('type'=>'img','many'=>array(
                array('title'=>"图片",'type'=>"img",'name'=>"pic",'value'=>'pic','width'=>50,'height'=>50)

            )),
            array('title'=>"资讯内容",'type'=>"textarea",'name'=>"content",'value'=>'content'),
            array('title'=>"发布人",'type'=>"input",'name'=>"nickname",'value'=>'nickname','msg'=>'本部发布','bast'=>'如果后台添加不须添加','readonly'=>1),
            array('type'=>'radio','title'=>"是否发布",'name'=>"is_release",'value'=>'is_release','msg'=>'请选择是否发布','many'=>array(
                array('value'=>'1','content'=>'发布'),
                array('value'=>'2','content'=>'不发布'),)),
        ),U('Road/information',array('token'=>$_SESSION['token'],'id'=>$_GET['cid'])),array($this,'bbc'),'','',array($this,'otherinfo'));

    }
    public function bbc($data){
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }
    public function otherinfo($aData){
        if($aData['openid'] ==""){
            $aData['nickname'] = "本部发送";
        }else{
            $user = M('Wxuser')->where(array('token'=>$_SESSION['token']))->find();
            $users = M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$aData['openid']))->find();
            $aData['nickname'] = $users['nickname'];
        }
        return $aData;
    }
    public function information_add(){
        $this->set_information(add);
    }
    public function save_information(){
        $this->set_information(Edit);
    }
    public function delete_information(){
        $this->del('Road_traffic_information');
    }

    /*
     * 高速快览
     * */
    public function quickfacts(){
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('Road/quickfacts', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加区域',//2级
                        'url'    => U('Road/add_area',array('token'=>$_SESSION['token']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '区域名称', '添加时间','操作'
                ),
                'List_Opt' => array(

                    array(
                        'name' => '路线管理',
                        'url'  => U('Road/line',array('token'=>$_SESSION['token']))
                    ),

                    array(
                        'name' => '编辑',
                        'url'  => U('Road/save_area',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/delete_area',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('Road_quickfacts_area')->where(array('token'=>$_SESSION['token']))->count(),
            M('Road_quickfacts_area')->field('id,aname,add_time')->where(array('token'=>$_SESSION['token']))->order("id")
        // array($this,'abc')
        );
    }

    /*
     * 添加区域*/
    public function set_area($aaa){
        $this->$aaa('Road_quickfacts_area',array(
            array('title'=>"区域名称",'type'=>"input",'name'=>"aname",'value'=>'aname','msg'=>'请填写区域名称'),
        ),U('Road/quickfacts',array('token'=>$_SESSION['token'])),array($this,'area_defind'));

    }
    public function area_defind($data){
        $data['token'] = $_SESSION['token'];
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    //添加
    public function add_area(){
        $this->set_area(add);
    }
    //编辑
    public function save_area(){
        $this->set_area(Edit);
    }
    //删除
    public function delete_area(){
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('Road_quickfacts_area');
    }

    public function line(){
        $this->table(
            array(
                'HeadHover' => U('Road/line', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加路段',//2级
                        'url'    => U('Road/line_add',array('token'=>$_SESSION['token'],'cid'=>$_GET['id']))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Road/quickfacts',array('token'=>$_SESSION['token'],'id'=>$_GET['id']))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID','路段名称', '所在公路网', '高速编号','全线高速名', '添加时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Road/save_line',array('token'=>$_SESSION['token'],'cid'=>$_GET['id']))
                    ),
                    array(
                        'name' => '高速事件',
                        'url'  => U('Road/info',array('token'=>$_SESSION['token'],'cid'=>$_GET['id'],'type'=>1))
                    ),
                    array(
                        'name' => '施工信息',
                        'url'  => U('Road/info',array('token'=>$_SESSION['token'],'cid'=>$_GET['id'],'type'=>2))
                    ),
                    array(
                        'name' => '高速预览管理',
                        'url'  => U('Road/platfrom',array('token'=>$_SESSION['token'],'cid'=>$_GET['id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/delete_line',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('Road_quickfacts_line')->where(array('token'=>$_SESSION['token'],'aid'=>$_GET['id']))->count(),
            M('Road_quickfacts_line')->field('id,lname,jname,bname,qname,add_time')->where(array('token'=>$_SESSION['token'],'aid'=>$_GET['id'])),
            array($this,'xxabcs')
        );
    }
    public function xxabcs($data){

        return $data;
    }
    //定义增改函数
    public function set_line($aaa){
        $aClassify = M('Road_quickfacts_area')->field('id as value,aname as content')->where(array('token'=>$_SESSION['token']))->select();
        $aClassify = array_merge(array(array('value'=>0,'content'=>'选择区域')),$aClassify);

        $this->$aaa('Road_quickfacts_line',array(

            array('type'=>'select','title'=>"所在区域",'name'=>"aid",'value'=>'aid','msg'=>'所在区域','many'=>$aClassify),
            array('title'=>"所在高速公路网",'type'=>"input",'name'=>"jname",'value'=>'jname','msg'=>'请填写如：国家高速','bast'=>''),
            array('title'=>"公路编号",'type'=>"input",'name'=>"bname",'value'=>'bname','msg'=>'请填写如：G4','bast'=>''),
            array('title'=>"全线高速名",'type'=>"input",'name'=>"qname",'value'=>'qname','msg'=>'请填写如：京港澳高速','bast'=>''),
            array('title'=>"路段名称",'type'=>"input",'name'=>"lname",'value'=>'lname','msg'=>'请填写如：广深高速','bast'=>''),
            array('title'=>"路段起始位置",'type'=>"input",'name'=>"position",'value'=>'position','msg'=>'请填写如：广氮--皇岗','bast'=>''),
        ),U('Road/line',array('token'=>$_SESSION['token'],'id'=>$_GET['cid'])),array($this,'bbcs'),'','',array($this,'otherinfo'));

    }
    public function bbcs($data){
        $data['token'] = $_SESSION['token'];
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }
    public function otherinfos($aData){

        return $aData;
    }
    public function line_add(){
        $this->set_line(add);
    }
    public function save_line(){
        $this->set_line(Edit);
    }
    public function delete_line(){
        $this->del('Road_quickfacts_line');
    }

    public function info(){
        $this->table(
            array(
                'HeadHover' => U('Road/info', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                   /* array(
                        'name'   => '添加实时内容',//2级
                        'url'    => U('Road/line_info_add',array('token'=>$_SESSION['token'],'cid'=>$_GET['id'],'lid'=>$_GET['id'],'type'=>$_GET['type']))
                    ),*/
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Road/line',array('token'=>$_SESSION['token'],'id'=>$_GET['cid']))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID','路段名称', '内容类型', '内容', '添加时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Road/save_line_info',array('token'=>$_SESSION['token'],'cid'=>$_GET['id'],'lid'=>$_GET['id'],'type'=>$_GET['type']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/delete_line_info',array('token'=>$_SESSION['token'],'cid'=>$_GET['id'],'lid'=>$_GET['id'],'type'=>$_GET['type']))
                    ),
                ),

            ),
            M('Road_quickfacts_maintain')->where(array('token'=>$_SESSION['token'],'lid'=>$_GET['id'],'type'=>$_GET['type']))->count(),
            M('Road_quickfacts_maintain')->field('id,lid,ctype,content,add_time')->where(array('token'=>$_SESSION['token'],'lid'=>$_GET['id'],'type'=>$_GET['type'])),
            array($this,'supplement')
        );
    }
    public function supplement($data){
        foreach($data as $k=>$val){
            $aLine = M('Road_quickfacts_line')->where(array('id'=>$val['lid']))->find();
            $data[$k]['lid'] = $aLine['lname'];
            if($val['ctype'] == 2){
                $data[$k]['ctype'] = '高速事件';
            }elseif($val['ctype'] == 1){
                $data[$k]['ctype'] = '施工信息';
            }
        }
        return $data;
    }
   /* public function set_line_info($aaa){
        $this->$aaa('Road_quickfacts_info',array(
            array('title'=>"实时内容",'type'=>"textarea",'name'=>"content",'value'=>'content'),
           ),U('Road/info',array('token'=>$_SESSION['token'],'type'=>$_REQUEST['type'],'cid'=>$_REQUEST['cid'],'id'=>$_GET['lid'])),array($this,'line_info'),'','',array($this,'otherinfoes'));

    }

    public function line_info($data){
        $data['aid'] = $_REQUEST['cid'];
        $data['lid'] = $_REQUEST['lid'];
        $data['type'] = $_REQUEST['type'];
        $data['token'] = $_SESSION['token'];
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }
    public function otherinfoes($aData){

        return $aData;
    }
    public function line_info_add(){
        $this->set_line_info(add);
    }
    public function save_line_info(){
        $this->set_line_info(Edit);
    }*/
    public function delete_line_info(){
        $this->del('Road_quickfacts_maintain');
    }

    public function platfrom(){
        $this->table(
            array(
                'HeadHover' => U('Road/platfrom', array('token' => $this->_sToken,'cid'=>$_GET['cid'],'id'=>$_GET['id'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加站点',//2级
                        'url'    => U('Road/platfrom_add',array('token'=>$_SESSION['token'],'cid'=>$_GET['cid'],'lid'=>$_GET['id']))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Road/line',array('token'=>$_SESSION['token'],'id'=>$_GET['cid']))
                    ),

                ),
                'tips' => array(//3级
                    '注意：在每条线的最后一站下面是没有任何东西添加的，添加也是没有任何用的'
                ),
                'Table_Header' => array(//4级
                    'ID','站点名', '所在路线','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Road/platfrom_save',array('token'=>$_SESSION['token'],'cid'=>$_GET['cid'],'lid'=>$_GET['id']))
                    ),
                    array(
                        'name' => '服务区管理',
                        'url'  => U('Road/service',array('token'=>$_SESSION['token'],'cid'=>$_GET['cid'],'lid'=>$_GET['id']))
                    ),
                    array(
                        'name' => 'VMS管理',
                        'url'  => U('Road/vms',array('token'=>$_SESSION['token'],'cid'=>$_GET['cid'],'lid'=>$_GET['id']))
                    ),
                    array(
                        'name' => '事件管理',
                        'url'  => U('Road/maintain',array('token'=>$_SESSION['token'],'cid'=>$_GET['cid'],'lid'=>$_GET['id']))
                    ),
                    /*array(
                        'name' => '摄像照片管理',
                        'url'  => U('Road/platfrom_save',array('token'=>$_SESSION['token'],'cid'=>$_GET['id']))
                    ),*/
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/delete_platfrom',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('Road_quickfacts_platform')->where(array('token'=>$_SESSION['token'],'lid'=>$_GET['id']))->count(),
            M('Road_quickfacts_platform')->field('id,pname,lname')->where(array('token'=>$_SESSION['token'],'lid'=>$_GET['id'])),
            array($this,'xxplatfrom')
        );
    }
    public function xxplatfrom($data){
        return $data;
    }

    public function set_platfrom($aaa){
        $this->$aaa('Road_quickfacts_platform',array(
            array('title'=>"站点名称",'type'=>"input",'name'=>"pname",'value'=>'pname','msg'=>'请填写站点的名称'),
            array('title'=>"地址",'type'=>"longinput",'name'=>"address",'value'=>'address','msg'=>'请填写站点所在的具体地址'),
            array('title'=>"桩号",'type'=>"input",'name'=>"key",'value'=>'key','msg'=>'请填写站点的桩号'),
            array('title'=>"出口车道数",'type'=>"number",'name'=>"cnum",'value'=>'cnum','msg'=>'请填写出口车道数'),
            array('title'=>"入口车道数",'type'=>"number",'name'=>"rcum",'value'=>'rcum','msg'=>'请填写入口车道数'),
            array('title'=>"出口ETC车道数",'type'=>"number",'name'=>"cetcnum",'value'=>'cetcnum','msg'=>'请填写出口ETC车道数'),
            array('title'=>"入口ETC车道数",'type'=>"number",'name'=>"retcnum",'value'=>'retcnum','msg'=>'请填写入口ETC车道数'),
            array('title'=>"出口通达地址",'type'=>"longinput",'name'=>"caddress",'value'=>'caddress','msg'=>'请填写站点的出口通达地址'),
            array('title'=>"入口通达地址",'type'=>"longinput",'name'=>"raddress",'value'=>'raddress','msg'=>'请填写站点的入口通达地址'),
            array('title'=>"连接的外部通道",'type'=>"input",'name'=>"wline",'value'=>'wline','msg'=>'请填写连接的外部通道'),
        ),U('Road/platfrom',array('token'=>$_SESSION['token'],'cid'=>$_REQUEST['cid'],'id'=>$_GET['lid'])),array($this,'platfrom_info')/*,'','',array($this,'otherplatfrom')*/);
    }
    public function platfrom_info($data){
        $data['token'] = $_SESSION['token'];
        $data['lid'] = $_GET['lid'];
        $aLine = M('Road_quickfacts_line')->where(array('id'=>$_GET['lid']))->find();
        $data['lname'] = $aLine['lname'];
        return $data;
    }

    public function platfrom_add(){
        $this->set_platfrom(add);
    }
    public function platfrom_save(){
        $this->set_platfrom(Edit);
    }
    public function delete_platfrom(){
        $this->del('Road_quickfacts_platform');
    }

    /*服务区管理*/
    public function service(){
        $this->table(
            array(
                'HeadHover' => U('Road/service', array('token' => $this->_sToken,'lid'=>$_GET['lid'],'id'=>$_GET['id'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加服务区',//2级
                        'url'    => U('Road/service_add',array('token'=>$_SESSION['token'],'lid'=>$_GET['lid'],'pid'=>$_GET['id']))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Road/platfrom',array('token'=>$_SESSION['token'],'cid'=>$_GET['cid'],'id'=>$_GET['id']))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID','服务区名称', '所在路线','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Road/service_save',array('token'=>$_SESSION['token'],'lid'=>$_GET['lid'],'pid'=>$_GET['id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/delete_service',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('Road_quickfacts_service')->where(array('token'=>$_SESSION['token'],'lid'=>$_GET['lid'],'pid'=>$_GET['id']))->count(),
            M('Road_quickfacts_service')->field('id,sname,lname')->where(array('token'=>$_SESSION['token'],'lid'=>$_GET['lid'],'pid'=>$_GET['id'])),
            array($this,'xxservice')
        );
    }
    public function xxservice($data){
        return $data;
    }

    public function set_service($aaa){
        $this->$aaa('Road_quickfacts_service',array(
            array('title'=>"服务区名称",'type'=>"input",'name'=>"sname",'value'=>'sname','msg'=>'请填写服务区名称'),
            array('title'=>"地址",'type'=>"longinput",'name'=>"address",'value'=>'address','msg'=>'请填写服务区所在的具体地址'),
            array('title'=>"桩号",'type'=>"input",'name'=>"key",'value'=>'key','msg'=>'请填写服务区的桩号'),
            array('title'=>"停车号",'type'=>"input",'name'=>"skey",'value'=>'skey','msg'=>'请填写服务区的停车号'),
           array('title'=>"服务内容（一）加油站",'type'=>"longinput",'name'=>"serviceo",'value'=>'serviceo','msg'=>'请填写服务区加油站所提供的服务','bast'=>'如果没有提供服务，请输入“暂无”'),
            array('title'=>"服务内容（二）餐饮",'type'=>"longinput",'name'=>"servicetw",'value'=>'servicetw','msg'=>'请填写服务区餐饮所提供的服务','bast'=>'如果没有提供服务，请输入“暂无”'),
            array('title'=>"服务内容（三）便利店",'type'=>"longinput",'name'=>"servicet",'value'=>'servicet','msg'=>'请填写服务区便利店所提供的服务','bast'=>'如果没有提供服务，请输入“暂无”'),
            array('title'=>"服务内容（四）维修厂",'type'=>"longinput",'name'=>"servicef",'value'=>'servicef','msg'=>'请填写服务区维修厂所提供的服务','bast'=>'如果没有提供服务，请输入“暂无”'),
        ),U('Road/service',array('token'=>$_SESSION['token'],'lid'=>$_REQUEST['lid'],'id'=>$_GET['pid'])),array($this,'service_info')/*,'','',array($this,'otherplatfrom')*/);
    }
    public function service_info($data){
        $data['token'] = $_SESSION['token'];
        $data['pid'] = $_GET['pid'];
        $data['lid'] = $_GET['lid'];
        $aLine = M('Road_quickfacts_line')->where(array('id'=>$_GET['lid']))->find();
        $data['lname'] = $aLine['lname'];
        return $data;
    }

    public function service_add(){
        $this->set_service(add);
    }
    public function service_save(){
        $this->set_service(Edit);
    }
    public function delete_service(){
        $this->del('Road_quickfacts_service');
    }

    /*vms管理*/
    public function vms(){
        $this->table(
            array(
                'HeadHover' => U('Road/vms', array('token' => $this->_sToken,'lid'=>$_GET['lid'],'id'=>$_GET['id'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加vms',//2级
                        'url'    => U('Road/vms_add',array('token'=>$_SESSION['token'],'lid'=>$_GET['lid'],'pid'=>$_GET['id']))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Road/platfrom',array('token'=>$_SESSION['token'],'cid'=>$_GET['cid'],'id'=>$_GET['id']))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID','所属站点', '所在路线','添加时间','道路左右','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Road/vms_save',array('token'=>$_SESSION['token'],'lid'=>$_GET['lid'],'pid'=>$_GET['id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/delete_vms',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('Road_quickfacts_vms')->where(array('token'=>$_SESSION['token'],'lid'=>$_GET['id']))->count(),
            M('Road_quickfacts_vms')->field('id,pid,lid,add_time,type')->where(array('token'=>$_SESSION['token'],'lid'=>$_GET['id'])),
            array($this,'xxvms')
        );
    }
    public function xxvms($data){
        foreach($data as $k=>$val){
            $aLine = M('Road_quickfacts_platform')->where(array('id'=>$val['pid']))->find();
            $data[$k]['pid'] = $aLine['pname'];
            $aLine = M('Road_quickfacts_line')->where(array('id'=>$val['lid']))->find();
            $data[$k]['lid'] = $aLine['lname'];
            if($val['type'] == 1){
                $data[$k]['type'] = '左侧';
            }elseif($val['type'] == 2){
                $data[$k]['type'] = '右侧';
            }

        }
        return $data;
    }

    public function set_vms($aaa){
        $this->$aaa('Road_quickfacts_vms',array(
            array('title'=>"桩号",'type'=>"input",'name'=>"key",'value'=>'key','msg'=>'请填写服务区的桩号'),
            array('type'=>'select','title'=>"消息所在道路左右",'name'=>"type",'value'=>'type','msg'=>'','many'=>array(
                array('value'=>'0', 'content'=>'请选择左右'),
                array('value'=>'1', 'content'=>'左'),
                array('value'=>'2','content'=>'右'),
            )),
            array('title'=>"vms内容",'type'=>"textarea",'name'=>"content",'value'=>'content'),
         ),U('Road/vms',array('token'=>$_SESSION['token'],'cid'=>$_REQUEST['cid'],'id'=>$_GET['lid'])),array($this,'vms_info')/*,'','',array($this,'otherplatfrom')*/);
    }
    public function vms_info($data){
        $data['token'] = $_SESSION['token'];
        $data['pid'] = $_GET['pid'];
        $data['lid'] = $_GET['lid'];
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function vms_add(){
        $this->set_vms(add);
    }
    public function vms_save(){
        $this->set_vms(Edit);
    }
    public function delete_vms(){
        $this->del('Road_quickfacts_vms');
    }

    /*维修事件管理*/
    public function maintain(){
        $this->table(
            array(
                'HeadHover' => U('Road/maintain', array('token' => $this->_sToken,'lid'=>$_GET['lid'],'id'=>$_GET['id'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加事件',//2级
                        'url'    => U('Road/maintain_add',array('token'=>$_SESSION['token'],'lid'=>$_GET['lid'],'pid'=>$_GET['id']))
                    ),
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('Road/platfrom',array('token'=>$_SESSION['token'],'cid'=>$_GET['cid'],'id'=>$_GET['id']))
                    ),

                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID','服务区名称', '所在路线','事件类型','发生点方位','发布时间','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url'  => U('Road/maintain_save',array('token'=>$_SESSION['token'],'lid'=>$_GET['lid'],'pid'=>$_GET['id']))
                    ),
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url'  => U('Road/delete_maintain',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('Road_quickfacts_maintain')->where(array('token'=>$_SESSION['token'],'aid'=>$_GET['id']))->count(),
            M('Road_quickfacts_maintain')->field('id,pid,lid,ctype,type,add_time')->where(array('token'=>$_SESSION['token'],'aid'=>$_GET['id'])),
            array($this,'xxmaintain')
        );
    }
    public function xxmaintain($data){
        foreach($data as $k=>$val){
            $aLine = M('Road_quickfacts_platform')->where(array('id'=>$val['pid']))->find();
            $data[$k]['pid'] = $aLine['pname'];
            $aLine = M('Road_quickfacts_line')->where(array('id'=>$val['lid']))->find();
            $data[$k]['lid'] = $aLine['lname'];
            if($val['type'] == 1){
                $data[$k]['type'] = '左侧';
            }elseif($val['type'] == 2){
                $data[$k]['type'] = '右侧';
            }
            if($val['ctype'] == 1){
                $data[$k]['ctype'] = '施工信息';
            }elseif($val['ctype'] == 2){
                $data[$k]['ctype'] = '高速事件';
            }

        }
        return $data;
    }

    public function set_maintain($aaa){
        $this->$aaa('Road_quickfacts_maintain',array(
            array('title'=>"桩号区间",'type'=>"longinput",'name'=>"ksection",'value'=>'ksection','msg'=>'请填写事件发生地点所在的桩号区间'),
            array('title'=>"站点区间",'type'=>"longinput",'name'=>"psection",'value'=>'psection','msg'=>'请填写事件发生地点所在的站点区间'),
            array('type'=>'select','title'=>"事件类型",'name'=>"ctype",'value'=>'ctype','msg'=>'','many'=>array(
                array('value'=>'0', 'content'=>'请选择事件类型'),
                array('value'=>'1', 'content'=>'维修事件'),
                array('value'=>'2','content'=>'交通事故'),
            )),
            array('type'=>'select','title'=>"消息所在道路左右",'name'=>"type",'value'=>'type','msg'=>'','many'=>array(
                array('value'=>'0', 'content'=>'请选择左右'),
                array('value'=>'1', 'content'=>'左'),
                array('value'=>'2','content'=>'右'),
            )),
            array('title'=>"事件内容",'type'=>"textarea",'name'=>"content",'value'=>'content'),
        ),U('Road/maintain',array('token'=>$_SESSION['token'],'cid'=>$_REQUEST['cid'],'id'=>$_GET['lid'])),array($this,'maintain_info')/*,'','',array($this,'otherplatfrom')*/);
    }
    public function maintain_info($data){
        $data['token'] = $_SESSION['token'];
        $data['pid'] = $_GET['pid'];
        $data['lid'] = $_GET['lid'];
        $aLine = M('Road_quickfacts_line')->where(array('id'=>$_GET['lid']))->find();
        $data['lname'] = $aLine['lname'];
        $data['add_time'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function maintain_add(){
        $this->set_maintain(add);
    }
    public function maintain_save(){
        $this->set_maintain(Edit);
    }
    public function delete_maintain(){
        $this->del('Road_quickfacts_maintain');
    }
}

