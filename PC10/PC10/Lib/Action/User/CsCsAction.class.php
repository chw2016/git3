﻿<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class CsCsAction extends TableAction {
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
        $this->pz	   = D('common_cs');
        
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
                'name' => '标题',
                'url'  => U('CsCs/index', array('token' => $this->_sToken))
            ),

        );
    }
    //显示
    public function index(){
    	        //$this->assign('qx','审核');//全选  方法名qx
        //$_SESSION['token']=$_SESSION['token'];
		    	unset($_GET['__hash__']);
		       //排序
		    	if($_GET['sortInt']==1){
		    		$_GET['sortInt']=0;
		    		if($_GET['sort']) $sort=$_GET['sort']." ".desc;
		    	}elseif($_GET['sortInt']==0){
		    		$_GET['sortInt']=1;$sort=$_GET['sort'];
		    	}
		    	if(!$_GET['sort']) unset($_SESSION['aWhere']);
		    	
		    	//排序
             	//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$_SESSION['token'];
                    //$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
                    $_SESSION['aWhere']=$aWhere;//排序
                }//结束 
		    	if($_SESSION['aWhere'])$aWhere=$_SESSION['aWhere'];//排序
		    	//P($aWhere);
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('CsCs/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加',//2级
                        'url'    => U('CsCs/add_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                'tips' => array(//3级
                    '<marquee behavior="alternate" onmouseover=this.stop()  onmouseout=this.start() id="mar">你可以在这里管理信息</marquee>'
                ),
                'Table_Header' => array(//4级
                    'ID|id', '名称|name', '性别|sex', '添加时间|add_time', '排序|sort','是否显示|is_show','操作'
                ),
                'List_Opt' => array(

                    /*               			array(
                                                    'name' => '其它信息',
                                                      'url'  => U('CsCs/xx',array('token'=>$_SESSION['token']))
                                            ), */

                    array(
                        'name' => '编辑',
                        'url'  => U('CsCs/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('CsCs/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
                'search'=>array(
                                              //  array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                               // array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                //array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
                                                array('title'=>'分类','name'=>'eq_sex','type'=>'select','many'=>array(
							                        array('value'=>'1','name'=>'男'),
							                        array('value'=>'2','name'=>'女'),
							                        //array('value'=>'3','name'=>'旅游1'),
							                    ))
                                )
            ),
            M('common_cs')->where($aWhere)->count(),
            M('common_cs')->field('id,name,sex,add_time,sort,is_show')->order($sort)->where($aWhere)
        // array($this,'abc')
        );
    }
    public function abc($data){
        foreach($data as $k=>$v){
           $data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            $data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
            $data[$k]['name']=str_substr($v['name'],10,'...');
            $data[$k]['state']="{$v['state']}"?'活动已开启':'活动关闭';
            switch ($v['sex']){
                case 1:$data[$k]['sex']='男';break;
                case 2:$data[$k]['sex']='女';break;
                case 3:$data[$k]['sex']='不限';break;
            }
            //sql语句
            $data[$k]['name']=M('mru_dianphd')->where(array('id'=>$v['name']))->getField('dname');
            //外加子段
            $data[$k]['title4']='不存的title';//title是加的子段，表里没有的子段，
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
    public function add_save($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('CsCs2')->select();//被添加内容的表
                foreach ($list as $k=>$v){
                    $list[$k]['content']=$v['name'];//把内容子段改成content
                    $list[$k]['value']=$v['id'];//把id子段改成value
                    unset($list[$k]['name']);//删除原来的内容子段
                } */
    	//链接$url=C('site_url')."index.php?g=User&m=Zp5&a=a&id=".$_GET['id'];
        $this->$aaa('common_cs',array(
            /*如果有需求要在input框中提示信息,则加入一个'placinfo'的键值，表示input 里面的placeholder属性*/
            //array('title'=>"较长input框",'type'=>"longinput",'name'=>"cname",'value'=>'cname','msg'=>'请填写较长input框'),
            array('title'=>"姓名",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写input框'),
        	array('type'=>'radio','title'=>"点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写点选框','many'=>array(
        				array('value'=>'1','content'=>'男'),
        				array('value'=>'2','content'=>'女'),
        	)),
        	//array('title'=>"链接",'type'=>"input",'name'=>"url",'msg'=>'请填写标题咯','value'=>'url','tishi'=>'<a style="color:red;" >如果镇了链接,前台跳的是链接地址：注意:前面加http://</a>'),
            //array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd'),
            //	array('title'=>"隐藏",'type'=>"hidden",'name'=>"id",'msg'=>'请填写标题咯','value'=>'id'),
            //array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写开始时间','value'=>'k_time'),
            //	array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写结束时间','value'=>'j_time'),
            //	array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num','msg'=>'请填写数量',),
        	//array('title'=>"链接",'type'=>"a",'url'=>$url,'name'=>"链接" ,'target'=>'1'),
            //			array('type'=>'radio','title'=>"特殊点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
            /* 		array('type'=>'select','title'=>"下拉列表",'name'=>"sex2",'value'=>'sex2','msg'=>'请选择下拉列表','many'=>array(
                            array('content'=>'选择'),
                            array('value'=>'1', 'content'=>'一年以上'),
                            array('value'=>'2','content'=>'二年以上'),
                            array('value'=>'3','content'=>'三年以上'),
                            array('value'=>'4','content'=>'不限'),
                    )), */
            //	array('type'=>'select','title'=>"特殊下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
/*             array('type'=>'checkbox','title'=>'复选框', '全选'=>1, 'name'=>"sex3",'value'=>'sex3','msg'=>'请选择复选框','many'=>array(
                array('value'=>'1','content'=>'蓝','checked'=>1),
                array('value'=>'2','content'=>'红','checked'=>1),
            )),


             //	array('type'=>'checkbox','title'=>'特殊复选框','全选'=>1, 'name'=>'jy', 'value'=>'jy','msg'=>'请填写标题咯','many'=>$list),
            array('type'=>'img','many'=>array(
                array('title'=>"图片1",'type'=>"img",'name'=>"pic",'value'=>'pic','width'=>50,'height'=>50),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic2",'value'=>'pic2','width'=>50,'height'=>50),
                array('title'=>"图片2",'type'=>"img",'name'=>"pic3",'value'=>'pic3','width'=>50,'height'=>50)
            )),
            array('title'=>"小的复文本框",'type'=>"textarea2",'name'=>"content2",'value'=>'content2'),
            array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
            array('title'=>"经纬度",'type'=>"map",'name'=>'name2', 'lng'=>"position_x",'lat'=>'position_y'), */
        ),U('CsCs/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));

    }
    public function bbc($data){
        
        $data['add_time']=time();
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
        //M('common_cs')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('common_cs');
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable('common_cs');
    }
    //是否显示  只 能主健是id, 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable('jd_user',$this->is_show);
    }
    
    //是否审核  只 能主健是id, 才能是否审核   有意见自己去开发！
    public function stateAjax(){
    	$this->stateAjaxTable('jd_user',$this->state);
    }
    //全选操作
    public function qx(){
    	$id=implode(',', $_REQUEST['list']);
    	if(!$_REQUEST['list']) script("全选后此操作才有效");
    	$list=M('mru_fuwu')->where(array('id'=>array('in',$id)))->delete();//改这
    	if($list) {
    		$this->success2('操作成功');
    	}else{
    		$this->error2('操作失败');
    	}
    }



//其它信息开头
    public function xx(){
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('CsCs/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('CsCs/index',array('uid'=>$_GET['id']))
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
                        'url'  => U('CsCs/save_xx',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('CsCs/delete_xx',array('token'=>$_SESSION['token']))
                    ),
                ),

            ),
            M('common_cs')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id']))->count(),
            M('common_cs')->field('id')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id'])),
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
        /* 		$list=M('CsCs2')->select();//被添加内容的表
            foreach ($list as $k=>$v){
        $list[$k]['content']=$v['name'];//把内容子段改成content
        $list[$k]['value']=$v['id'];//把id子段改成value
        unset($list[$k]['name']);//删除原来的内容子段
        } */
        $this->$aaa('common_cs',array(
            /* 	array('title'=>"较长input框",'type'=>"longinput",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'), */
            array('title'=>"input框",'type'=>"input",'name'=>"pinzhong",'value'=>'pinzhong','tishi'=>'链接:http://v.wapwei.co'),
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
        ),U('CsCs/xx',array('token'=>$_SESSION['token'],'id'=>$_GET['uid'])),array($this,'bbc'),1);

    }
    public function save_xx(){
        $this->add_save_xx(Edit);
    }
    public function delete_xx(){
        $this->del('common_cs');
    }
//其它信息结束



}

