<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class JdTagAction extends TableAction {
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
        $this->pz	   = D('jd_tag');
        $this->token=session('token');
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
            /* array(
                 'name'=>'交标管理',
                 'url'  => U('JdUser/scale', array('token' => $this->_sToken))
             ),*/


        );
    }
    //显示
    public function index(){

        //$_SESSION['token']=$_SESSION['token'];
        /*     	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$_SESSION['token'];
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
        }//结束 */
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdWz/index',array('token'=>$_SESSION['token'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加标签',//2级
                        'url'    => U('JdTag/add_content',array('token'=>$_SESSION['token']))
                    ),
                		array(
                				'name'   => '返回',//2级
                				'url'    => U('JdWz/index',array('token'=>$_SESSION['token']))
                		),
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID',  '标题', '颜色','操作'
                ),
                'List_Opt' => array(

                    /*          			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('JdTag/xx',array('token'=>$_SESSION['token']))
                    ), */

                    array(
                        'name' => '编辑',
                        'url'  => U('JdTag/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdTag/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                /*         		//搜索
                                'search'=>array(
                                                array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                                array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
                )//结束 */
            ),
            M('jd_tag')->where(array('token'=>$_SESSION['token']))->count(),
            M('jd_tag')->field('id,name,color')->where(array('token'=>$_SESSION['token']))->order("sort"),
            array($this,'abc')
        );
    }
    public function abc($data){
        foreach($data as $k=>$v){
            $color=array(
            		'',
                '<span style="color:#f5772d;">橘黄色</span>',
                '<span style="color:#52bd36;">清新绿</span>',
                '<span style="color:#22cdcb;">天蓝色</span>',
                '<span style="color:#9969d4;">紫罗兰</span>'
            );
            $data[$k]['color']=$color[$v['color']];
        }
        return $data;
    }
    //定义增改函数
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('JdTag2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
        } */
        $this->$aaa('jd_tag',array(
            /*如果有需求要在input框中提示信息,则加入一个'placinfo'的键值，表示input 里面的placeholder属性*/
            //array('title'=>"较长input框",'type'=>"longinput",'name'=>"cname",'value'=>'cname','msg'=>'请填写较长input框'),
            //array('title'=>"input框",'type'=>"input",'name'=>"name",'value'=>'pinzhong','msg'=>'请填写input框'),
            //array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd'),
            //	array('title'=>"隐藏",'type'=>"hidden",'name'=>"id",'msg'=>'请填写标题咯','value'=>'id'),
            //array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写开始时间','value'=>'k_time'),
            //	array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写结束时间','value'=>'j_time'),
            //	array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num','msg'=>'请填写数量',),

            //			array('type'=>'radio','title'=>"特殊点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
            /* 		array('type'=>'select','title'=>"下拉列表",'name'=>"sex2",'value'=>'sex2','msg'=>'请选择下拉列表','many'=>array(
                            array('content'=>'选择'),
                            array('value'=>'1', 'content'=>'一年以上'),
                            array('value'=>'2','content'=>'二年以上'),
                            array('value'=>'3','content'=>'三年以上'),
                            array('value'=>'4','content'=>'不限'),
            )), */
            //	array('type'=>'select','title'=>"特殊下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
            array('title'=>"标签标题",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写标签名称'),
            array('type'=>'select','title'=>"标签颜色",'name'=>"color",'value'=>'color','msg'=>'请选择标签颜色','many'=>array(
                array('content'=>'选择'),
                array('value'=>'1', 'content'=>'橘黄色'),
                array('value'=>'2','content'=>'清新绿'),
                array('value'=>'3','content'=>'天蓝色'),
                array('value'=>'4','content'=>'紫罗兰'),
            )), 

        ),U('JdTag/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
        $data['password']=MD5($data['password']);
        $data['sex']=1;
        return $data;
    }
    //添加
    public function add_content(){
        $this->add_save(add);
    }
    //编辑
    public function save_content(){
        $this->add_save(Edit);
    }
    //删除
    public function delete_content(){
        //M('jd_tag')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('jd_tag');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaTable('jd_tag');
    }




    //其它信息开头
    public function xx(){
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdTag/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('JdTag/index',array('uid'=>$_GET['id']))
                    )
                ),
                'tips' => array(//3级
                    '你可以在这里管理信息'
                ),
                'Table_Header' => array(//4级
                    'ID', '标题', '标题', '标题', '标题', '标题', '标题', '标题', '标题', '标题', '标题', '标题','操作'
                ),
                'List_Opt' => array(



                    array(
                        'name' => '查看',
                        'url'  => U('JdTag/save_xx',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdTag/delete_xx',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('jd_tag')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id']))->count(),
            M('jd_tag')->field('id,name,color')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id'])),
            array($this,'xxabc')
        );
    }
    public function xxabc($data){
        foreach($data as $k=>$v){
            $data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            $data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
            $data[$k]['content']=substr(htmlspecialchars_decode($v['content']),0,78);
            $data[$k]['state']="{$v['state']}"?'活动已开启':'活动关闭';
            switch ($v['sex']){
            case 1:$data[$k]['sex']='男';break;
            case 2:$data[$k]['sex']='女';break;
            case 3:$data[$k]['sex']='不限';break;
            }
            //sql语句
            $data[$k]['uid']=M('mru_jf')->where(array('id'=>$v['uid']))->getField('name');
            //特殊复选框显示，先把保存在$v['mdiao']里的id偏历出来，然后用表的id=$v['mdiao']保存的id 把名字查询出来值给临时变量
            foreach (explode(',',$v['mdiao']) as $ke=>$s){//字符转数组遍历
                $str=M('mru_mdian')->where(array('id'=>$s))->field('name')->find();
                $str=implode(',', $str);//$str是个一维数组，转成字符串
                $data[$k]['aaa'].=$str.',';//用,拼结起来，值给临时变量
            }
            $data[$k]['mdiao']=$data[$k]['aaa'];//临时变量的值给正常子段赋值，改变正常子段
            $data[$k]['mdiao']=rtrim($data[$k]['mdiao'],',');//去右边,
            unset($data[$k]['aaa']);//删除临时变量   结束处!

        }
        return $data;
    }
    //定义增改函数
    public function add_save_xx($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('JdTag2')->select();//被添加内容的表
            foreach ($list as $k=>$v){
        $list[$k]['content']=$v['name'];//把内容子段改成content
        $list[$k]['value']=$v['id'];//把id子段改成value
        unset($list[$k]['name']);//删除原来的内容子段
        } */
        $this->$aaa('jd_tag',array(
            /* 	array('title'=>"较长input框",'type'=>"longinput",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'), */
            array('title'=>"input框",'type'=>"input",'name'=>"pinzhong",'value'=>'pinzhong'),
            array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd'),
            array('title'=>"隐藏",'type'=>"hidden",'name'=>"id",'msg'=>'请填写标题咯','value'=>'id'),
            array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
            array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
            array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
            array('type'=>'radio','title'=>"点选框1",'name'=>"sex",'value'=>'sex','msg'=>'请选择点选框','many'=>array(
                array('value'=>'1','content'=>'男'),
                array('value'=>'2','content'=>'女'),
            )),
            //	array('type'=>'radio','title'=>"特殊点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
            array('type'=>'select','title'=>"下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
                array('value'=>'1', 'content'=>'一年以上'),
                array('value'=>'2','content'=>'二年以上'),
                array('value'=>'3','content'=>'三年以上'),
                array('value'=>'4','content'=>'不限'),
            )),
            //		array('type'=>'select','title'=>"特殊下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
            array('type'=>'checkbox','title'=>'复选框','name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
                array('value'=>'1','content'=>'蓝'),
                array('value'=>'2','content'=>'红'),
            )),
            //		array('type'=>'checkbox','title'=>'特殊复选框', 'name'=>'jy', 'value'=>'jy','msg'=>'请填写标题咯','many'=>$list),
            array('type'=>'img','many'=>array(
                array('title'=>"图片1",'type'=>"img",'name'=>"pic",'value'=>'pic','whidth'=>50,'height'=>50),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic2",'value'=>'pic','whidth'=>50,'height'=>50),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic3",'value'=>'pic','whidth'=>50,'height'=>50)
            )),
            array('title'=>"小的复文本框",'type'=>"textarea2",'name'=>"hd_time",'value'=>'hd_time'),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
            array('title'=>"经纬度",'type'=>"map",'name'=>'name', 'lng'=>"position_x",'lat'=>'position_y'),
        ),U('JdTag/xx',array('token'=>$_SESSION['token'],'id'=>$_GET['uid'])),array($this,'bbc'),1);

    }
    public function save_xx(){
        $this->add_save_xx(Edit);
    }
    public function delete_xx(){
        $this->del('jd_tag');
    }
    //其它信息结束

}

