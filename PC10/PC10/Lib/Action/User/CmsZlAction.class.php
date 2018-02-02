<?php
/**
 *  技术支持：张湘南
 **/
class CmsZlAction extends CmsAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/cms';

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
        if($_GET['lmid']) $_SESSION['lmid']=$_GET['lmid'];//存lmid
        $b=M('cms_lm')->where(array('id'=>$_SESSION['lmid']))->getField('b');
        $this->b	   = $b;
     }
    //一级
    protected function setHeader(){
       return array(
      			array(
        				'name' => '返回',
        				'url'  => $_SESSION['lmurl'],
        		),
        );
    }
    //显示
    public function index(){
    	$_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
    	$_SESSION['cmszlxxurl']=$_SERVER['REQUEST_URI'];//存地址
    	unset($_GET['__hash__']);
    	if($_GET['lmid']) $_SESSION['lmid']=$_GET['lmid'];//存lmid
    	$aWhere['token']=$_SESSION['token'];
    	$aWhere['uid']=$_SESSION['lmid'];
		//排序
    	if($_GET['sortInt']==1){
    		$_GET['sortInt']=0;
    		if($_GET['sort']) $sort=$_GET['sort']." ".desc;
    	}elseif($_GET['sortInt']==0){
    		$_GET['sortInt']=1; $sort=$_GET['sort'];
    	}
    	if(!$_GET['sort']) unset($_SESSION['aWhere']);
    	if(!$_GET['sort']) $sort="add_time desc";
    	//排序
    	//搜索开始
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
    		$aWhere['token'] =$_SESSION['token'];
    		$aWhere['uid']=$_SESSION['lmid'];
    		//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
    		$_SESSION['aWhere']=$aWhere;//排序
    	}
 	 	//搜索结束
    	if($_SESSION['aWhere'])$aWhere=$_SESSION['aWhere'];//排序
    	//选择栏目功能   $lmgl 栏目功能
    	$data=M('cms_lm')->where(array('token'=>$_SESSION['token'],'id'=>$_SESSION['lmid']))->find();
    	foreach ($data as $k=>$v){//转化成数组
    		$data[$k]=explode('|', $v);
    	}
		$lmgl=$data['lmgl'][0];
    	$lmgl=explode(',',$lmgl);
    	$field='id,';
    	if(in_array('name',$lmgl))$field.=''.$data['name'][1].',';
    	if(in_array('ts1',$lmgl))$field.=''.$data['ts1'][1].',';
    	if(in_array('ts2',$lmgl))$field.=''.$data['ts2'][1].',';
    	if(in_array('ts3',$lmgl))$field.=''.$data['ts3'][1].',';
    	if(in_array('ts4',$lmgl))$field.=''.$data['ts4'][1].',';
    	if(in_array('namepic',$lmgl))$field.=''.$data['namepic'][1].',';
    	if(in_array('cpic',$lmgl))$field.=''.$data['cpic'][1].',';
    	if(in_array('pic3',$lmgl))$field.=''.$data['pic3'][1].',';
    	if(in_array('pic4',$lmgl))$field.=''.$data['pic4'][1].',';
    	if(in_array('pic5',$lmgl))$field.=''.$data['pic5'][1].',';
    	if(in_array('k_time',$lmgl))$field.=''.$data['k_time'][1].',';
    	if(in_array('j_time',$lmgl))$field.=''.$data['j_time'][1].','; 
    	if(in_array('map',$lmgl))$field.=''.$data['map'][1].','.$data['map'][2].',';//在这下面外加
    	
    	//if(in_array('ms',$lmgl))$field.='ms,';//在这下面外加
    	if(in_array('type',$lmgl))$field.=''.$data['type'][1].',';//在这下面外加
    	if(in_array('xl',$lmgl))$field.=''.$data['xl'][1].',';//在这下面外加
    	if(in_array('fx',$lmgl))$field.=''.$data['fx'][1].',';//在这下面外加
        if(in_array('add_time',$lmgl))$field.='add_time,';//在这下面外加
    	if(in_array('is_show',$lmgl))$field.='is_show,';//在这下面外加
    	if(in_array('state',$lmgl))$field.='state,';//在这下面外加
    	if(in_array('sort',$lmgl))$field.='sort,';//在这下面外加
    	$field=rtrim($field,',');
    	$namea=array('ID|id', ''.$data['name'][0].'|'.$data['name'][1].'');
    	if(in_array('ts1',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['ts1'][0].'|'.$data['ts1'][1].''));
    	if(in_array('ts2',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['ts2'][0].'|'.$data['ts2'][1].''));
    	if(in_array('ts3',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['ts3'][0].'|'.$data['ts3'][1].''));
    	if(in_array('ts4',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['ts4'][0].'|'.$data['ts4'][1].''));
    	if(in_array('namepic',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['namepic'][0].'|'.$data['namepic'][1].''));
    	if(in_array('cpic',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['cpic'][0].'|'.$data['cpic'][1].''));
    	if(in_array('pic3',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['pic3'][0].'|'.$data['pic3'][1].''));
    	if(in_array('pic4',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['pic4'][0].'|'.$data['pic4'][1].''));
    	if(in_array('pic5',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['pic5'][0].'|'.$data['pic5'][1].''));
    	if(in_array('k_time',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['k_time'][0].'|'.$data['k_time'][1].''));
    	if(in_array('j_time',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['j_time'][0].'|'.$data['j_time'][1].''));
    	if(in_array('map',$lmgl)) $namea=array_merge($namea,array('0'=>'经度|'.$data['map'][1].'','1'=>'纬度|'.$data['map'][2].''));//在这下面外加
    	
    	//if(in_array('ms',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['ms'].'|ms'));//在这下面外加
    	if(in_array('type',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['type'][0].'|'.$data['type'][1].''));//在这下面外加
    	if(in_array('xl',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['xl'][0].'|'.$data['xl'][1].''));//在这下面外加
    	if(in_array('fx',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['fx'][0].'|'.$data['fx'][1].''));//在这下面外加
    	if(in_array('add_time',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['add_time'][0].'|add_time'));//在这下面外加
    	if(in_array('is_show',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['is_show'][0].'|is_show'));//在这下面外加
    	if(in_array('state',$lmgl)) $namea=array_merge($namea,array('0'=>''.$data['state'][0].'|state'));//在这下面外加
    	if(in_array('sort',$lmgl)) $namea=array_merge($namea,array('0'=>'排序|sort'));//在这下面外加
    	$nameb=array('操作');
    	$namea=array_merge($namea,$nameb);
    	//名称模糊查询
        if(in_array('name',$lmgl)) $name=array('title'=>'按'.$data['name'][0].'查询','name'=>'li_'.$data['name'][1].'','placeholder'=>'请输入'.$data['name'][0].'模糊查询');
        if(in_array('ts1',$lmgl)) $ts1=array('title'=>'按'.$data['ts1'][0].'查询','name'=>'li_'.$data['ts1'][1].'','placeholder'=>'请输入'.$data['ts1'][0].'模糊查询');
        //类型分类查询
        if(in_array('type',$lmgl)) $list=M('cms_type')->where(array('token'=>$_SESSION['token'],'uid'=>$_SESSION['lmid']))->select();
    	foreach ($list as $k=>$v){
    		$list[$k]['name']=$v['name'];//把内容子段改成content
    		$list[$k]['value']=$v['name'];//把id子段改成value
    	}
    	if(in_array('type',$lmgl)) $type=array('title'=>'按'.$data['type'][0].'查询','name'=>'eq_type','type'=>'select','many'=>$list);
    	//下拉列表分类查询
    	if(in_array('xl',$lmgl)) $xls=M('cms_xl')->where(array('token'=>$_SESSION['token'],'uid'=>$_SESSION['lmid']))->select();
    	foreach ($xls as $k=>$v){
    		$xls[$k]['name']=$v['name'];//把内容子段改成content
    		$xls[$k]['value']=$v['name'];//把id子段改成value
    	}
    	if(in_array('xl',$lmgl)) $xl=array('title'=>'按'.$data['xl'][0].'查询','name'=>'eq_xl','type'=>'select','many'=>$xls);
    	//类型显示
    	$typess=array(
    			'name'   => ''.$data['type'][0].'管理',//2级
    			'url'    => U('CmsType/index',array('lmid'=>$_SESSION['lmid']))
    	);
		if(!in_array('xl',$lmgl)) $typess='';
		//下拉列表显示
		$xlss=array(
				'name'   => ''.$data['xl'][0].'管理',//2级
				'url'    => U('CmsXl/index',array('lmid'=>$_SESSION['lmid']))
		);
		if(!in_array('type',$lmgl)) $xlss='';
	    //复选框显示
		$fxss=array(
				'name'   => ''.$data['fx'][0].'管理',//2级
				'url'    => U('CmsFx/index',array('lmid'=>$_SESSION['lmid']))
		);
		if(!in_array('fx',$lmgl)) $fxss='';
    	//列表页轮播图
    	$listlb=array(
                	'name'   => '列表页轮播图',//2级
                	'url'    => U('CmsLb/index',array('lmid'=>$_SESSION['lmid']))
               );
    	if(!in_array('listlb',$lmgl)) $listlb='';
    	//内容页轮播图
    	$showlb=array(
                	'name'   => '内容页轮播图',//2级
                	'url'    => U('CmsLbshow/index',array('lmid'=>$_SESSION['lmid']))
                );
    	if(!in_array('showlb',$lmgl)) $showlb='';
    	//子类信息
    	$zl=array(
    			'name' => ''.$data['zl'][0].'',
    			'url'  => U('CmsZlxx/index',array('lmid' =>$data['zl'][1]))
    	) ;
    	if(!in_array('zl',$lmgl)) $zl='';
        //内容页轮播图(每页不同)
    	$array=array(
    			'name' => '内容页轮播图',
    			'url'  => U('CmsLbshow2/index',array('lmid' =>$_SESSION['lmid']))
    	) ;
        if(!in_array('contentlb',$lmgl)) $array='';
        //查看详情
        $ck=array(
        		'name' => '查看详情',
        		'url'  => U('CmsZl/save_content',array('lmid' =>$_SESSION['lmid'],'ck'=>1))
        ) ;
        if(!in_array('ck',$lmgl)) $ck='';
        //编辑
        $save=array(
        		'name' => '编辑',
        		'url'  => U('CmsZl/save_content',array('lmid' =>$_SESSION['lmid']))
        ) ;
        if(!in_array('save',$lmgl)) $save='';
        //删除
        $delete=array(
        		'name' => '删除',
        		'url'  => U('CmsZl/delete_content',array('lmid' =>$_SESSION['lmid']))
        ) ;
        if(!in_array('delete',$lmgl)) $delete='';
        //全选
        if(in_array('qx',$lmgl)){
        	$this->assign('qx',$data['qx'][0]);
        	$this->assign('dz',$data['qx'][1]);
        }
  /*   	*/
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('CmsZl/index', array('lmid' =>$_SESSION['lmid'])),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加',//2级
                        'url'    => U('CmsZl/add_content',array('lmid'=>$_SESSION['lmid']))
                    ),
                		$typess,
                		$xlss,
                		$fxss,
                		$listlb,
                		$showlb,
                		array(
                				'name' => '返回',
                				'url'  => $_SESSION['lmurl'],
                		),

                ),
                'tips' => array(//3级
                    ''.$data['xx'][0].''
                ),
                'Table_Header' => $namea,
                'List_Opt' => array(
                	$zl,
                    $array,
                	$ck,
                	$save,
                	$delete,
			    
                  
                ),
                'search'=>array($name,$ts1,$type,$xl)
            ),
            M($this->b)->where($aWhere)->count(),
            M($this->b)->field($field)->order($sort)->where($aWhere),
            array($this,'abc')
        );
    }
    public function abc($data){
    	$list=M('cms_lm')->where(array('token'=>$_SESSION['token'],'id'=>$_SESSION['lmid']))->find();
    	foreach ($list as $k=>$v){//转化成数组
    		$list[$k]=explode('|', $v);
    	}
		foreach($data as $k=>$v){
        	
            if($v[$list['add_time'][1]]) $data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
            //if($v['ms'])$data[$k]['ms']=mb_substr($v['ms'],0,10,'utf-8');
            if($v[$list['name'][1]]) $data[$k][$list['name'][1]]=mb_substr($v[$list['name'][1]],0,10,'utf-8');
            if($v[$list['ts1'][1]]) $data[$k][$list['ts1'][1]]=mb_substr($v[$list['ts1'][1]],0,10,'utf-8');
            if($v[$list['ts2'][1]]) $data[$k][$list['ts2'][1]]=mb_substr($v[$list['ts2'][1]],0,10,'utf-8');
            if($v[$list['ts3'][1]]) $data[$k][$list['ts3'][1]]=mb_substr($v[$list['ts3'][1]],0,10,'utf-8');
            if($v[$list['ts4'][1]]) $data[$k][$list['ts4'][1]]=mb_substr($v[$list['ts4'][1]],0,10,'utf-8');
            
            if($v['fx'])$data[$k]['fx']=mb_substr($v['fx'],0,10,'utf-8');
            if($v[$list['namepic'][1]])$data[$k][$list['namepic'][1]]="<img style='width:50px' height:50px; src='{$v[$list['namepic'][1]]}' />";
            if($v[$list['cpic'][1]])$data[$k][$list['cpic'][1]]="<img style='width:50px' height:50px; src='{$v[$list['cpic'][1]]}' />";
            if($v[$list['pic3'][1]])$data[$k][$list['pic3'][1]]="<img style='width:50px' height:50px; src='{$v[$list['pic3'][1]]}' />";
            if($v[$list['pic4'][1]])$data[$k][$list['pic4'][1]]="<img style='width:50px' height:50px; src='{$v[$list['pic4'][1]]}' />";
            if($v[$list['pic5'][1]])$data[$k][$list['pic5'][1]]="<img style='width:50px' height:50px; src='{$v[$list['pic5'][1]]}' />";
          
        }
        return $data;
    }
    //定义增改函数
    public function add_save($aaa,$ck){
    	//查询类型
    	$list=M('cms_type')->where(array('token'=>$_SESSION['token'],'uid'=>$_SESSION['lmid']))->select();
    	foreach ($list as $k=>$v){
    		$list[$k]['content']=$v['name'];//把内容子段改成content
    		$list[$k]['value']=$v['name'];//把id子段改成value
    	}
    	//查询下拉列表
    	$xls=M('cms_xl')->where(array('token'=>$_SESSION['token'],'uid'=>$_SESSION['lmid']))->select();
    	foreach ($xls as $k=>$v){
    		$xls[$k]['content']=$v['name'];//把内容子段改成content
    		$xls[$k]['value']=$v['name'];//把id子段改成value
    	}
    	//查询复选框
    	$fxs=M('cms_fx')->where(array('token'=>$_SESSION['token'],'uid'=>$_SESSION['lmid']))->select();
    	foreach ($fxs as $k=>$v){
    		$fxs[$k]['content']=$v['name'];//把内容子段改成content
    		$fxs[$k]['value']=$v['name'];//把id子段改成value
    	}
    	//先择栏目功能
    	$data=M('cms_lm')->where(array('token'=>$_SESSION['token'],'id'=>$_SESSION['lmid']))->find();
    	foreach ($data as $k=>$v){//转化成数组
    		$data[$k]=explode('|', $v);
    	}
    	$lmgl=$data['lmgl'][0];
    	if(!$lmgl) $lmgl=1;
        $this->$aaa($this->b,array(
                array('name2'=>'name','title'=>"".$data['name'][0]."",'type'=>"input",'name'=>"".$data['name'][1]."",'value'=>"".$data['name'][1]."",'msg'=>'请填写'.$data['name'][0].'','tishi'=>"".$data['name'][2].""),//标题
        		array('name2'=>'url','title'=>"".$data['url'][0]."",'type'=>"input",'name'=>"".$data['url'][1]."",'value'=>"".$data['url'][1]."",'tishi'=>"".$data['url'][2].""),//链接
        		
        		array('name2'=>'namepic','type'=>'img','many'=>array(
        				array('title'=>"".$data['namepic'][0]."",'name2'=>'namepic' ,'type'=>"img",'name'=>"".$data['namepic'][1]."",'value'=>"".$data['namepic'][1]."",'tishi'=>"".$data['namepic'][2].""),//列表内容图片
        				array('title'=>"".$data['cpic'][0]."", 'name2'=>'cpic', 'type'=>"img",'name'=>"".$data['cpic'][1]."",'value'=>"".$data['cpic'][1]."",'tishi'=>"".$data['cpic'][2].""),//内容页图片
        				array('title'=>"".$data['pic3'][0]."", 'name2'=>'pic3', 'type'=>"img",'name'=>"".$data['pic3'][1]."",'value'=>"".$data['pic3'][1]."",'tishi'=>"".$data['pic3'][2].""),//内容页图片
        				array('title'=>"".$data['pic4'][0]."", 'name2'=>'pic4', 'type'=>"img",'name'=>"".$data['pic4'][1]."",'value'=>"".$data['pic4'][1]."",'tishi'=>"".$data['pic4'][2].""),//内容页图片
        				array('title'=>"".$data['pic5'][0]."", 'name2'=>'pic5', 'type'=>"img",'name'=>"".$data['pic5'][1]."",'value'=>"".$data['pic5'][1]."",'tishi'=>"".$data['pic5'][2].""),//内容页图片
        		)),
        		
        		array('name2'=>'type','type'=>'radio','title'=>"".$data['type'][0]."",'name'=>"".$data['type'][1]."",'value'=>"".$data['type'][1]."" ,'msg'=>'请选择'.$data['type'][0].'','tishi'=>"".$data['type'][2]."" , 'many'=>$list),//类型
        		array('name2'=>'xl','type'=>'select','title'=>"".$data['xl'][0]."",'name'=>"".$data['xl'][1]."",'value'=>"".$data['xl'][1]."",'msg'=>'请选择'.$data['xl'][0].'' ,'tishi'=>"".$data['xl'][2]."" ,'many'=>$xls),
        		array('name2'=>'fx','type'=>'checkbox','title'=>"".$data['fx'][0]."", 'name'=>"".$data['fx'][1]."", 'value'=>"".$data['fx'][1]."",'msg'=>'请选择标题咯','tishi'=>"".$data['fx'][2]."",'many'=>$fxs),
        		array('name2'=>'cname','title'=>"".$data['cname'][0]."",'type'=>"cname",'name'=>"".$data['cname'][1]."",'value'=>"".$data['cname'][1]."",'msg'=>'请填写'.$data['cname'][0].'' ,'tishi'=>"".$data['cname'][2].""),//内容标题
        		array('name2'=>'tel','title'=>"".$data['tel'][0]."",'type'=>"input",'name'=>"".$data['tel'][1]."",'value'=>"".$data['tel'][1]."",'msg'=>'请填写'.$data['tel'][0].'','tishi'=>"".$data['tel'][2].""),//手机
        		array('name2'=>'address','title'=>"".$data['address'][0]."",'type'=>"input",'name'=>"".$data['address'][1]."",'value'=>"".$data['address'][1]."",'msg'=>'请填写'.$data['address'][0].'','tishi'=>"".$data['address'][2].""),//地址
        		array('name2'=>'ts1','title'=>"".$data['ts1'][0]."",'type'=>"input",'name'=>"".$data['ts1'][1]."",'value'=>"".$data['ts1'][1]."" ,'msg'=>'请填写'.$data['ts1'][0].'','tishi'=>"".$data['ts1'][2].""),
        		array('name2'=>'ts2','title'=>"".$data['ts2'][0]."",'type'=>"input",'name'=>"".$data['ts2'][1]."",'value'=>"".$data['ts2'][1]."" ,'msg'=>'请填写'.$data['ts2'][0].'','tishi'=>"".$data['ts2'][2].""),
        		array('name2'=>'ts3','title'=>"".$data['ts3'][0]."",'type'=>"input",'name'=>"".$data['ts3'][1]."",'value'=>"".$data['ts3'][1]."" ,'msg'=>'请填写'.$data['ts3'][0].'','tishi'=>"".$data['ts3'][2].""),
        		array('name2'=>'ts4','title'=>"".$data['ts4'][0]."",'type'=>"input",'name'=>"".$data['ts4'][1]."",'value'=>"".$data['ts4'][1]."" ,'msg'=>'请填写'.$data['ts4'][0].'','tishi'=>"".$data['ts4'][2].""),
        		
        		array('name2'=>'k_time','title'=>"".$data['k_time'][0]."",'type'=>"time",'name'=>"".$data['k_time'][1]."",'value'=>"".$data['k_time'][1]."",'msg'=>"".$data['k_time'][0].""),
        		array('name2'=>'j_time','title'=>"".$data['j_time'][0]."",'type'=>"time",'name'=>"".$data['j_time'][1]."",'value'=>"".$data['j_time'][1]."",'msg'=>"".$data['j_time'][0].""),
        		array('name2'=>'map','title'=>"".$data['map'][0]."",'type'=>"map", 'lng'=>"".$data['map'][1]."",'lat'=>"".$data['map'][2].""),
        		
        		array('name2'=>'ts5','title'=>"".$data['ts5'][0]."",'type'=>"textarea2",'name'=>"".$data['ts5'][1]."",'value'=>"".$data['ts5'][1]."",'tishi'=>"".$data['ts5'][2].""),
        		array('name2'=>'ms','title'=>"".$data['ms'][0]."",'type'=>"textarea2",'name'=>"".$data['ms'][1]."",'value'=>"".$data['ms'][1]."",'msg'=>'请填写input框','msg'=>'请填写'.$data['ms'].'','tishi'=>"".$data['ms'][2].""),//栏目描述
        		
        		
        		array('name2'=>'content','title'=>"".$data['content'][0]."",'type'=>"textarea",'name'=>"".$data['content'][1]."",'value'=>"".$data['content'][1].""),//内容
        		array('name2'=>'ts6','type'=>"input",'title'=>"".$data['ts6'][0]."",'type'=>"textarea_1",'name'=>"".$data['ts6'][1]."",'value'=>"".$data['ts6'][1].""),
        		
        		
        ),U('CmsZl/index',array('lmid' =>$_SESSION['lmid'])),array($this,'bbc'),$ck,'','','',$lmgl);

    }
    public function bbc($data){
        $data['uid']=$_SESSION['lmid'];
        $data['add_time']=time();
        return $data;
    }
    //添加
    public function add_content(){
        $this->add_save(add);
    }
    //编辑
    public function save_content(){
    	if($_GET['ck']) $ck=$_GET['ck'];
        $this->add_save(Edit,$ck);
    }
    //删除
    public function delete_content(){
        M('Cms_lbshow2')->where(array('uid'=>$_GET['lmid'],'uuid'=>$_GET['id'],'token'=>$_SESSION['token']))->delete();//删除其它信息
        $this->del($this->b);
    }
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
        $this->sortajaxTable($this->b);
    }



    //是否显示  只 能主健是id,子段是is_show 才能是否显示  有意见自己去开发！
    public function is_showAjax(){
    	$this->is_showAjaxTable($this->b);
    }
    
    
    
    //是否审核  只 能主健是id,子段是state 才能是否审核   有意见自己去开发！
    public function stateAjax(){
    	$this->stateAjaxTable($this->b);
    }
    



}

