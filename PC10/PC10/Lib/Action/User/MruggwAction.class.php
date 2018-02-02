<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class MruggwAction extends TableAction {
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
        $this->pz	   = D('mru_ggw');
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
 		
		$token=$_SESSION['token'];
		
		$this->table(
        	array(
                'abc'=>123,
            	//'id' => 'id',//如果主键不是id，则需要设置
            	'HeadHover' => U('Mruggw/index', array('token' => $this->_sToken)),//栏目样式 
            	'Head_Opt' => array(
                	array(
                    	'name'   => '添加积分广告位',//2级
                    	'url'    => U('Mruggw/add_content',array('token'=>$token))
                	)
            	),
                'tips' => array(//3级
                    '你可以在这里管理积分广告位信息'
                ),
            	'Table_Header' => array(//4级
                	'ID', '广告图','添加时间','操作'
            	),
            	'List_Opt' => array(
                	array(
                    	'name' => '编辑',
                    	'url'  => U('Mruggw/ContentEdit',array('token'=>$token))
                	),
	                array(
	                    'name' => '删除',
	                    'url'  => U('Mruggw/ContentDel',array('token'=>$token))
	                ),
            	)
        	
	        ), 
        	$this->pz->where(array('token'=>$token))->count(),
        	$this->pz->field('id,pic,add_time')->where(array('token'=>$token)),
            array($this,'abc')
         );
	}
 	public function abc($data){
		foreach($data as $k=>$v){
			$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
			$data[$k]['pic']="<img style='width:50px' height:50px; src='{$v['pic']}' />";
		
	
			
		}
		return $data;
	}
	//定义增改函数
	public function addEdit($aaa){

		$this->$aaa('mru_ggw',array(
		       array('type'=>'img','many'=>array(
						array('title'=>"广告图",'type'=>"img",'name'=>"pic",'value'=>'pic','whidth'=>50,'height'=>50),
		       		
						
				)),
				
				array('title'=>"链接",'type'=>"input",'name'=>"url",'value'=>'url','tishi'=>'提示:http://开头'),
				
		),U('Mruggw/index',array('token'=>$_SESSION['token'])),array($this,'bbc'));
		
	}
	public function bbc($data){
		$data['add_time']=time();

		return $data;
	}
   //添加
    public function add_content(){
	    $this->addEdit(add);
    }
    //编辑
    public function ContentEdit(){
    	$this->addEdit(Edit);
    }
   //删除
    public function ContentDel(){
        $this->del('mru_ggw');
    }
}
?>
