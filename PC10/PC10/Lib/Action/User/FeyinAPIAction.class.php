<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class FeyinAPIAction extends TableAction {
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
        $this->pz	   = D('feyinapi');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    //一级
    protected function setHeader(){
    	return array(
                array(
                    'name' => '打印',
                    'url'  => U('FeyinAPI/index', array('token' => $this->_sToken))
                ),

            );
    }
    //显示
 	public function index(){
 	
 		$token=$_SESSION['token'];
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
            	'HeadHover' => U('FeyinAPI/index', array('token' => $this->_sToken)),//栏目样式 
            	'Head_Opt' => array(
                	array(
                    	'name'   => '添加参数',//2级
                    	'url'    => U('FeyinAPI/add_content',array('token'=>$token))
                	)
            	),
                'tips' => array(//3级
                    '你可以在这里管理打印参数信息'
                ),
            	'Table_Header' => array(//4级
                	'ID', '商户代码', '密钥', '打印机设备编码','操作'
            	),
            	'List_Opt' => array(

/*              			array(
            					'name' => '其它信息',
              					'url'  => U('FeyinAPI/xx',array('token'=>$_SESSION['token']))
            			), */
            			
                	array(
                    	'name' => '编辑',
                    	'url'  => U('FeyinAPI/ContentEdit',array('token'=>$token))
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('FeyinAPI/ContentDel',array('token'=>$token))
	                ),
            	),
/*         		//搜索
	        	'search'=>array(
	        					array('title'=>'名称','name'=>'li_name','placeholder'=>'请输入名称模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
	        					array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
	        					array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询')//be是Table里判断条件 add_time是子段
	        	)//结束 */
	        ), 
        	M('feyinapi')->where(array('token'=>$token))->count(),
        	M('feyinapi')->field('id,name1,name2,name3')->where(array('token'=>$token))->order("id desc")
           // array($this,'abc')
         );
	}
 	public function abc($data){
		foreach($data as $k=>$v){
			$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
			$data[$k]['content']=htmlspecialchars_decode(mb_substr($v['content'],0,99,'utf-8'));
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
	public function addEdit($aaa){
		//特殊点选框,复选框,下拉列表
/* 		$list=M('FeyinAPI2')->select();//被添加内容的表
		foreach ($list as $k=>$v){
			$list[$k]['content']=$v['name'];//把内容子段改成content
			$list[$k]['value']=$v['id'];//把id子段改成value
			unset($list[$k]['name']);//删除原来的内容子段
    	} */
		$this->$aaa('feyinapi',array(
			
				array('title'=>"商户代码:",'type'=>"input",'name'=>"name1",'value'=>'name1'),
				array('title'=>"密钥:",'type'=>"input",'name'=>"name2",'value'=>'name2'),
				array('title'=>"打印机设备编码:",'type'=>"input",'name'=>"name3",'value'=>'name3'),

		),U('FeyinAPI/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));
		
	}
	public function bbc($data){
		$data['password']=MD5($data['password']);
		$data['sex']=1;
		return $data;
	}
   //添加
    public function add_content(){
    	$feyinapi=M('feyinapi')->where(array('token'=>$_SESSION['token']))->find();
    	if($feyinapi){
    		echo "<script>alert('你已经添加过了');history.back();</script>";die;
    	}
	    $this->addEdit(add);
    }
    //编辑
    public function ContentEdit(){
    	$this->addEdit(Edit);
    }
   //删除
    public function ContentDel(){
    	//M('feyinapi')->where(array('uid'=>$_GET['id']))->delete();//删除其它信息
        $this->del('feyinapi');
    }
    
    
    public function xx(){

    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('FeyinAPI/index', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '返回',//2级
    									'url'    => U('FeyinAPI/index',array('uid'=>$_GET['id']))
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
    									'url'  => U('FeyinAPI/xxEdit',array('token'=>$token))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('FeyinAPI/xxDel',array('token'=>$token))
    							),
    					),

    			),
    			M('feyinapi')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id']))->count(),
    			M('feyinapi')->field('id')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id'])),
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
    public function xxEdit2($aaa){
    	//特殊点选框,复选框,下拉列表
    	/* 		$list=M('FeyinAPI2')->select();//被添加内容的表
    		foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('feyinapi',array(
    			array('title'=>"较长input框",'type'=>"longinput",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'),
    			array('title'=>"input框",'type'=>"input",'name'=>"name",'value'=>'name'),
    			array('title'=>"密码",'type'=>"password",'name'=>"pwd",'value'=>'pwd'),
    			array('title'=>"隐藏",'type'=>"hidden",'name'=>"id",'msg'=>'请填写标题咯','value'=>'id'),
    			array('title'=>"开始时间",'type'=>"time",'name'=>"k_time",'msg'=>'请填写标题咯','value'=>'k_time'),
    			array('title'=>"结束时间",'type'=>"time",'name'=>"j_time",'msg'=>'请填写标题咯','value'=>'j_time'),
    			array('title'=>"数量",'type'=>"number",'name'=>"num",'msg'=>'','value'=>'num'),
    			array('type'=>'radio','title'=>"点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
    					array('value'=>'1','content'=>'男'),
    					array('value'=>'2','content'=>'女'),
    			)),
    			array('type'=>'radio','title'=>"特殊点选框",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
    			array('type'=>'select','title'=>"下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
    					array('value'=>'1', 'content'=>'一年以上'),
    					array('value'=>'2','content'=>'二年以上'),
    					array('value'=>'3','content'=>'三年以上'),
    					array('value'=>'4','content'=>'不限'),
    			)),
    			array('type'=>'select','title'=>"特殊下拉列表",'name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>$list),
    			array('type'=>'checkbox','title'=>'复选框','name'=>"sex",'value'=>'sex','msg'=>'请填写标题咯','many'=>array(
    					array('value'=>'1','content'=>'蓝'),
    					array('value'=>'2','content'=>'红'),
    			)),
    			array('type'=>'checkbox','title'=>'特殊复选框', 'name'=>'jy', 'value'=>'jy','msg'=>'请填写标题咯','many'=>$list),
    			array('type'=>'img','many'=>array(
    					array('title'=>"图片1",'type'=>"img",'name'=>"pic",'value'=>'pic','whidth'=>50,'height'=>50),
    					array('title'=>"图片2",'type'=>"img",'name'=>"pic2",'value'=>'pic','whidth'=>50,'height'=>50),
    					array('title'=>"图片2",'type'=>"img",'name'=>"pic3",'value'=>'pic','whidth'=>50,'height'=>50)
    			)),
    			array('title'=>"小的复文本框",'type'=>"textarea2",'name'=>"hd_time",'value'=>'hd_time'),
    			array('title'=>"图文详细",'type'=>"textarea",'name'=>"content",'value'=>'content'),
    			array('title'=>"经纬度",'type'=>"map",'name'=>'name', 'lng'=>"position_x",'lat'=>'position_y'),
    	),U('FeyinAPI/xx',array('token'=>$_SESSION['token'],'id'=>$_GET['uid'])),array($this,'bbc'));
    
    }
    
    public function xxEdit(){
    	$this->xxEdit2(Edit);
    }
    
    
    public function xxDel(){
    	$this->del('feyinapi');
    }
    
    
    
}
?>
