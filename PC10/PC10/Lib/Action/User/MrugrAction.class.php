<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class MrugrAction extends TableAction {
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
        $this->pz	   = D('mru_jfb');
        $this->dp	   = D('Miye_dianpu');
        $this->order   = D('Miye_order');
      
    }
    //一级
    protected function setHeader(){
    		return array(
                array(
                    'name' => '积分兑换',
                    'url'  => U('Hyzx/jf', array('token' => $this->_sToken))
                ),
    			
    			array(
    					'name' => '积分广告位',
    					'url'  => U('Mruggw/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '个人资料',
    					'url'  => U('Mrugr/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '我的优惠卷',
    					'url'  => U('Wdyhj/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '评论管理',
    					'url'  => U('Mrupl/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '我要积分',
    					'url'  => U('MruJf/index', array('token' => $this->_sToken))
    			),
    			
    			array(
    					'name' => '订单管理',
    					'url'  => U('Store_new/orders', array('token' => $this->_sToken,'mru'=>1))
    			),

    			
    			
    			
    			

            );
    }
    //显示
 	public function index(){
        $this->iPageNumer =200;
		$token=$_SESSION['token'];
		$aWhere['token']=$_SESSION['token'];
		//排序
		if($_GET['sortInt']==1){
			$_GET['sortInt']=0;
			$_GET['sort']=$_GET['sort']." ".desc;
		}elseif($_GET['sortInt']==0){
			$_GET['sortInt']=1;
		}
		if($_GET['sort']){
			$sort=$_GET['sort'];
		}else{
			unset($_SESSION['aWhere']);
			$sort="sort";
		}//排序
		
		
//搜索
                if(IS_POST){
                    $_POST=$_REQUEST;
                    $aWhere=$this->search($_POST);
                    $aWhere['token'] =$_SESSION['token'];
                 }//结束 
		$this->table(
        	array(
                'abc'=>123,
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('Mrugr/index', array('token' => $this->_sToken)),//栏目样式 
            	'Head_Opt' => array(
             	array(
                    	'name'   => '导出数据',//2级
                    	'url'    => U('ExportExcel/Mrugr',array('token'=>$token))
                	) 
            	),
                'tips' => array(//3级
                    '你可以在这里管理个人资料信息'
                ),
            	'Table_Header' => array(//4级
                	'ID|id', '昵称|name', '性别|sex', '手机|tel'/*, '会员id|openid'*/,'地址|address','积分|num','会员卡|idcard','优惠券|openid','抢购卷|openid','红包|hongbao','操作'
            	),
            	'List_Opt' => array(

         /*   			array(
            					'name' => '其它信息',
             					'url'  => U('Mrugr/xx',array('token'=>$_SESSION['token']))
            			), */
            			
//             			array(
//             					'name' => '编辑积分红包',
//             					'url'  => U('Mrugr/jfb',array('token'=>$token))
//             			),
            			
                	array(
                    	'name' => '编辑',
                    	'url'  => U('Mrugr/ContentEdit',array('token'=>$token))
                	), 
	                array(
	                    'name' => '删除',
	                    'url'  => U('Mrugr/ContentDel',array('token'=>$token))
	                ),
            	),
         		//搜索
	        	'search'=>array(
 	        					array('title'=>'按手机查询', 'placeholder'=>'请输入手机模糊查询', 'name'=>'li_tel'),//li是Table里判断条件 name是子段
        				/* 	array('title'=>'名称','name'=>'eq_name'),//eq是Table里判断条件 name是子段
        					array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')//be是Tab */
 	        	)//结束
	        ), 
        	$this->pz->where($aWhere)->count(),
        	$this->pz->field('id,name,sex,tel/*,openid*/,address')->order('id')->where($aWhere),
				array($this,'abc')
            
         );
	}
 	public function abc($data){
		foreach($data as $k=>$v){
            $info = M('mru_jfb')->where(array('id'=>$v['id']))->find();
			//sql语句
			$data[$k]['jf']=$info['num'];
		
			$data[$k]['hy']=$info['idcard'];
			$data[$k]['hy']=$data[$k]['hy']?'已绑定':'未绑定';
			
			$uid=M('mru_yhj2')->where(array('openid'=>$info['openid'],'type'=>0,'token'=>$_SESSION['token']))->select();
			$str='';
			foreach ($uid as $ke=>$v2){
				$str.=M('mru_wdyhj')->where(array('id'=>$v2['uid']))->getField('price')."元,";
			}
			
			
			$data[$k]['yhj']=$str;
			if($data[$k]['yhj']=='') $data[$k]['yhj']='无优惠券';
	
			
			$qgj=M('mru_qgj')->where(array('openid'=>$info['openid'],'state'=>0))->getField('price',true);
			$data[$k]['qgj']=implode('元,', $qgj)."元";
			if($data[$k]['qgj']=='元') $data[$k]['qgj']='无抢购卷';
			
			$qgj=M('mru_hb')->where(array('openid'=>$info['openid'],'state'=>0))->getField('price',true);
			$data[$k]['hb']=implode('元,', $qgj)."元";
			if($data[$k]['hb']=='元') $data[$k]['hb']='无红包';

            $user = M("Wxuser")->where(array('token'=>$_SESSION['token']))->find();
            $data[$k]['name'] =$v['name'] ? $v['name']: M('Wxusers')->where(array('uid'=>$user['id'],'openid'=>$info['openid']))->getField('nickname');
			
		}
		return $data;
	}
	//定义增改函数
	public function addEdit($aaa){
		//特殊点选框,复选框,下拉列表
/* 		$list=M('Mrugr2')->select();//被添加内容的表
		foreach ($list as $k=>$v){
			$list[$k]['content']=$v['name'];//把内容子段改成content
			$list[$k]['value']=$v['id'];//把id子段改成value
			unset($list[$k]['name']);//删除原来的内容子段
    	} */
		$this->$aaa('mru_jfb',array(
				array('title'=>"昵称",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写品牌咯'),
				array('type'=>'select','title'=>"性别",'name'=>"sex",'value'=>'sex','msg'=>'请选择下拉列表','many'=>array(
                            array('value'=>'男', 'content'=>'男'),
                            array('value'=>'女','content'=>'女'),
                 )), 
				array('title'=>"手机",'type'=>"input",'name'=>"tel",'value'=>'tel'),
				array('title'=>"地址",'type'=>"input",'name'=>"address",'value'=>'address'),
				//array('title'=>"红包",'type'=>"input",'name'=>"hongbao",'value'=>'hongbao'),
				array('title'=>"积分",'type'=>"input",'name'=>"num",'value'=>'num'),
				array('title'=>"会员卡",'type'=>"input",'name'=>"idcard",'value'=>'idcard','tishi'=>'提示:取消会员卡直接清除既可'),
				//array('title'=>"地址",'type'=>"input",'name'=>"address",'msg'=>'请填写标题咯','value'=>'address'),
				
		),U('Mrugr/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));
		
	}
	public function bbc($data){
        $Member = M('Member_idcard');
        $Jfb    = M('mru_jfb')->where(array('id' => $data['id']))->find();
        if($idcard=$data['idcard']){
            $Member->where(array('idcard' => $idcard))
                ->data(array(
                    'openid'    => $Jfb['openid'],
                    'is_used'   => 1,
                    'add_time'  => date('Y-m-d H:i:s'),
                    'phone'     => $data['tel']
                ))
                ->save();
        }else{
            $Member->where(array('idcard' => $Jfb['idcard']))
                ->data(array(
                    'openid'    => '',
                    'is_used'   => 0,
                    'add_time'  => date('Y-m-d H:i:s'),
                    'phone'     => ''
                ))
                ->save();
        }
		$data['password']=MD5($data['password']);
		//$data['sex']=1;
		return $data;
	}
   //添加
    public function add_content(){
    	//echo 1;die;
        $data=M('Credit_users')->field("id")->select();
      	p($data);die;
        exportExcel($data,array('微信昵称'),'会员资料库');
        die;
    	
    	//$data=M('mru_jfb')->field("id,name,sex,tel,address,num,hongbao,idcard")->select();
    	//P($data);
    	//exportExcel($data,array('编号','昵称','性别','手机','地址','积分','红包','会员卡'),'会员资料库');
    	//P($data);
    	
	   // $this->addEdit(add);
    }
    //编辑
    public function ContentEdit(){
    	$this->addEdit(Edit);
    }
   //删除
    public function ContentDel(){
    	//M('mru_jfb')->where(array('uid'=>$_GET['id']))->delete();
        $this->del('mru_jfb');
    }
    
    
    public function xx(){

    	$this->table(
    			array(
    					'abc'=>123,
    					//'id' => 'id',//如果主键不是id，则需要设置
    					'HeadHover' => U('Mrugr/index', array('token' => $this->_sToken)),//栏目样式
    					'Head_Opt' => array(
    							array(
    									'name'   => '返回',//2级
    									'url'    => U('Mrugr/index',array('uid'=>$_GET['id']))
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
    									'url'  => U('Mrugr/xxEdit',array('token'=>$token))
    							),
    							array(
    									'name' => '删除',
    									'url'  => U('Mrugr/xxDel',array('token'=>$token))
    							),
    					),

    			),
    			M('mru_jf')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id']))->count(),
    			M('mru_jf')->field('id')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id'])),
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
    	/* 		$list=M('Mrugr2')->select();//被添加内容的表
    		foreach ($list as $k=>$v){
    	$list[$k]['content']=$v['name'];//把内容子段改成content
    	$list[$k]['value']=$v['id'];//把id子段改成value
    	unset($list[$k]['name']);//删除原来的内容子段
    	} */
    	$this->$aaa('mru_jfb',array(
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
    	),U('Mrugr/xx',array('token'=>$_SESSION['token'],'id'=>$_GET['uid'])),array($this,'bbc'));
    
    }
    
    public function xxEdit(){
    	$this->xxEdit2(Edit);
    }
    
    
    public function xxDel(){
    	$this->del('mru_jfb');
    }
    
//修改积分红包
   public function jfb(){
   	
   	
   
   	$this->Edit('mru_jfb',array(
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
   	),U('Mrugr/xx',array('token'=>$_SESSION['token'],'id'=>$_GET['uid'])),array($this,'bbc'));
   	
   	
   }
    
   
   //定义增改函数
   public function xxEdit_jfb($aaa){
   	//特殊点选框,复选框,下拉列表
   	/* 		$list=M('Mrugr2')->select();//被添加内容的表
   	 foreach ($list as $k=>$v){
   	$list[$k]['content']=$v['name'];//把内容子段改成content
   	$list[$k]['value']=$v['id'];//把id子段改成value
   	unset($list[$k]['name']);//删除原来的内容子段
   	} */
   
   
   }
}
?>
