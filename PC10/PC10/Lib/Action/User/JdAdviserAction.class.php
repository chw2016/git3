<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南
 **/
class JdAdviserAction extends TableAction {
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/together';

    public function _initialize()
    {
        parent::_initialize();
        $this->pz	   = D('jd_adviser');
        $this->token=session('token');
        
        
        //是否审核
        $this->state='status';//改这
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
            /* array(
                 'name'=>'交标管理',
                 'url'  => U('JdUser/scale', array('token' => $this->_sToken))
             ),*/

        
        );
    }
    //显示
    public function index(){
    
    	$aWhere['token']=$_SESSION['token'];
    	$aWhere['status']=1;
    	
    	//搜索
    	if(IS_POST){
    		$_POST=$_REQUEST;
    		$aWhere=$this->search($_POST);
	    	$aWhere['token']=$_SESSION['token'];
	    	$aWhere['status']=1;
    		$_SESSION['aWhere']=$aWhere;//排序
    	}//结束
    	
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdAdviser/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '添加顾问',//2级
                        'url'    => U('JdAdviser/add_content',array('token'=>$_SESSION['token']))
                    ),
                		array(
                				'name'   => '顾问类型管理',//2级
                				'url'    => U('JdAdvCate/index',array('token'=>$_SESSION['token']))
                		),
                    array(
                        'name'=>'顾问审核',
                        'url'  => U('JdAdviser/checkAdviser', array('token' => $this->_sToken))
                    ),
                		
                		array(
                				'name'=>'顾问预约管理',
                				'url'  => U('JdAdviser/ck', array('token' => $this->_sToken))
                		),
                		
                    array(
                        'name'=>'查看评价',
                        'url'  => U('JdAdviser/talk', array('token' => $this->_sToken))
                    ),

                    array(
                            'name'=>'邮箱与尾部信息管理中心',
                            'url'  => U('JdXx/index', array('token' => $this->_sToken))
                    ),

                ),
                'tips' => array(//3级
                    '因为已审核的顾问不能删除,如果你要删除顾问,请进入"顾问审核"处把审核状态改成X才能删除'
                ),
                'Table_Header' => array(//4级
                    'ID',  '称谓', '顾问类型','联系电话','顾问头像','点赞数','星级','排序','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name'=>'设置预约',
                        'url'=>U('JdAdviser/bespoke',array('token'=>$_SESSION['token'])),
                    ),
                    array(
                        'name'=>'查看预约',
                        'url'=>U('JdAdviser/ck',array('token'=>$_SESSION['token'])),
                    ),
                    array(
                        'name' => '编辑',
                        'url'  => U('JdAdviser/save_content',array('token'=>$_SESSION['token']))
                    ),
             /*        array(
                        'name' => '删除',
                        'url'  => U('JdAdviser/delete_content',array('token'=>$_SESSION['token']))
                    ), */
                ),
            		
            		
            		'search'=>array(
            				array('title'=>'称谓查询','name'=>'li_name','placeholder'=>'请输入称谓模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
            				// array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
            				//array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
            				array('title'=>'顾问分类查询','name'=>'eq_type','type'=>'select','many'=>array(
            						array('value'=>'总部顾问','name'=>'总部顾问'),
            						array('value'=>'机构顾问','name'=>'机构顾问'),
            						array('value'=>'伙伴顾问','name'=>'伙伴顾问'),
            						//array('value'=>'3','name'=>'旅游1'),
            				))
            		)
            		
            ),
            M('jd_adviser')->where($aWhere)->count(),
            M('jd_adviser')->field('id,name,cate,phone,head,praise,xin,sort')->where($aWhere)->order("add_time desc"),
            array($this,'abc')
        );
    }
    
    //排序  只 能主健是id,子段是sort,才能排序 .有意见开发！
    public function sortajax(){
    	$this->sortajaxTable('jd_adviser');
    }
    public function abc($data){
        foreach($data as $k=>$v){
            $data[$k]['head']="<img style='width:50px' height:50px; src='{$v['head']}' />";
            //$data[$k]['name']=mb_substr($v['name'],0,10,'utf-8').'...';
           // $data[$k]['trade']=mb_substr($v['trade'],0,10,'utf-8').'...';
            
            if(strlen($v['remark'])>=30){
            $data[$k]['remark']=mb_substr($v['remark'],0,10,'utf-8').'...';
            }
            $data[$k]['xin']=$v['xin']."星";
        }
        return $data;
    }
    //显示顾问推荐审核
    public function checkAdviser(){
    	$aWhere['token']=$_SESSION['token'];
/*         if($_GET['action']=='check'){
            $data=array('id'=>$_GET['id'],'status'=>1);
            if(M('jd_adviser')->save($data)){
                $this->success2('操作成功！');
            }else{
                $this->error2('操作失败，数据服务器繁忙...');
            }
        } */
        
  
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
        	$sort="add_time desc";
        }//排序
        
        //搜索
        if(IS_POST){
        	$_POST=$_REQUEST;
        	$aWhere=$this->search($_POST);
        	$aWhere['token'] =$_SESSION['token'];
        	//$aWhere['uid'] =$_GET['id'];//其它信息中，需要用到的父级id
        	$_SESSION['aWhere']=$aWhere;//排序
        }//结束
        
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdAdviser/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                		array(
                				'name' => '返回',
                				'url'  => U('JdAdviser/index', array('token' => $this->_sToken))
                		),
                ),
                'tips' => array(//3级
                    '<b style="font-size:14px;" >特别注意:点V图标可取消审核，点X图际可通过审核</b>'
                ),
                'Table_Header' => array(//4级
                    'ID|id',  '称谓|name', '顾问分类|type','顾问类型|cate','联系电话|phone','推荐人|referee','推荐人电话|ref_phone','审核状态|status','推荐时间|add_time','操作'
                ),
                'List_Opt' => array(
      /*               array(
                        'name'=>'审核',
                        'url'=>U('JdAdviser/sh',array('token'=>$_SESSION['token'],'action'=>'check')),
                    ),
                		array(
                				'name'=>'取消审核',
                				'url'=>U('JdAdviser/no_sh',array('token'=>$_SESSION['token'],'action'=>'check')),
                		), */
                    array(
                        'name' => '编辑',
                        'url'  => U('JdAdviser/save_content',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdAdviser/delete_content',array('token'=>$_SESSION['token']))
                    ),
                ),
            		'search'=>array(
            				 array('title'=>'顾问','name'=>'li_name','placeholder'=>'请输入顾问模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
            				// array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
            				//array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
            			/* 	array('title'=>'分类','name'=>'eq_sex','type'=>'select','many'=>array(
            						array('value'=>'1','name'=>'男'),
            						array('value'=>'2','name'=>'女'),
            						//array('value'=>'3','name'=>'旅游1'),
            				)) */
            		)
            		
            ),
            M('jd_adviser')->where($aWhere)->count(),
            M('jd_adviser')->field('id,name,type,cate,phone,referee,ref_phone,status,add_time')->where($aWhere)->order($sort),
            array($this,'xxxabc')
        );
    }
    
    public function xxxabc($data){
    	foreach($data as $k=>$v){
    		$data[$k]['add_time']=date('Y-m-d H:i',$v['add_time']);
    		//$data[$k]['status']="{$v['status']}"?'已审核':'未审核';
    	}
    	return $data;
    }
    
    
    //查看评价
    public function talk(){
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdAdviser/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    // array(
                        // 'name'   => '添加',//2级
                        // 'url'    => U('JdAdviser/add_content',array('token'=>$_SESSION['token']))
                    // )
                		array(
                				'name'   => '返回',//2级
                				'url'    => U('JdAdviser/index',array('token'=>$_SESSION['token']))
                		)
                ),
                'tips' => array(//3级
                    '您可以在这里查看和管理用户对顾问的评价'
                ),
                'Table_Header' => array(//4级
                    'ID',  '顾问','评价人','评价时间','评价内容','预约时间','项目名称','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '删除',
                        'url'  => U('JdAdviser/delete_talk',array('token'=>$_SESSION['token']))
                    ),
                ),
            ),
            M('jd_talk')->where(array('token'=>$_SESSION['token']))->count(),
            M('jd_talk')->field('id,oid,aid,add_time,content')->where(array('token'=>$_SESSION['token']))->order("add_time desc"),
            array($this,'talkAbc')
        );
    }
    public function talkAbc($data){
        foreach($data as $k=>$v){
            $apply=M('jd_apply')->where(array('id'=>$v['aid']))->find();
            $user=M('jd_user')->where(array('id'=>$apply['user_id']))->find();
            $data[$k]['oid']=M('jd_adviser')->where(array('id'=>$v['oid']))->getField('name');
            $data[$k]['aid']=$user['name'];
            $data[$k]['add_time']=date('Y-m-d h:i:s',$v['add_time']);
            $data[$k]['content']=$v['content'];
            $data[$k]['apply_time']=date('y-m-d H:i:s',$apply['add_time']);
            $data[$k]['oid21']=M('jd_apply')->where(array('id'=>$v['aid']))->getField('xm');
            // $data[$k]['email']=$user['email'];
        }
        return $data;
    }
    //顾问预约管理
    public function bespoke(){
        $id=$_GET['id'];
        if(!empty($id)){
            $id=intval($id);
        }else{
            script('没有指设置定对象！');
        }
        //查找配置记录
        //生成设置列表
        for($i=0;$i<=14;$i++){
            $s=$this->allDayToStr($i);//获得明天的起始时间和结束时间时间戳
            $arr[$i]['date']=date('Y-m-d',$s['start']);
            $arr[$i]['week']=$this->getWeek(date('w',$s['start']));
            $arr[$i]['apply']=M('jd_apply')->where(array('token'=>session('token'),'adviser_id'=>$id,'time'=>$s['start']))->count('id');
            $arr[$i]['value']=$this->getConfig($id,$s['start']);//设置的预约次数
            $arr[$i]['time']=$s['start'];
            $arr[$i]['adv_id']=$id;
        }

        $this->assign('token',$this->token);
        $this->assign('da',$arr);
        $this->display();
    }
    //获得顾问申请配置
    protected function getConfig($id,$time){
        $data=M('jd_config')->field('values')->where(array('token'=>$this->token,'adv_id'=>$id,'time'=>$time))->find();
        if($data<1){
            return 0;
        }else{
            return $data['values'];
        } 
    }
    //设置预约限定次数

    public function setConfig(){
        if(IS_AJAX){
            //获得必须的数据
            $data=array(
                'adv_id'=>$_POST['adv_id'],
                'values'=>$_POST['values'],
                'time'=>$_POST['time'],
                'token'=>$this->token,
                'update_time'=>time(),
            );
            //检查数据库是否存在记录 
            $id=M('jd_config')->field('id')->where(array('token'=>$this->token,'adv_id'=>$data['adv_id'],'time'=>$data[time]))->find();
            if(!empty($id)){
                $data['id']=$id['id'];
                $status=M('jd_config')->save($data);//更新
            }else{
                $status=M('jd_config')->add($data);//新增
            }
            $this->ajaxReturn(array('status'=>$status));//返回操作状态
            p($_POST);
        } 
    }
    //根据给定时间戳返回星期
    protected function getWeek($s){
        $week=array('星期天','星期一','星期二','星期三','星期四','星期五','星期六'); 
        return $week[$s];
    }

    //获得当以后第N天的起始时间戳和结束时间戳
    protected function allDayToStr($day) {
        $start=strtotime(date('Y-m-d',strtotime('+0 day')));//获得今天零点时候的时间戳
        $end=strtotime(date('Y-m-d',strtotime('+1 day')))-1;//获得今天的结束时间戳
        //生成时间差
        if(is_numeric($day)){
            $dayMis=3600*24*$day;
            $arr=array(
                'start'=>$start+$dayMis,
                'end'=>$end+$dayMis
            );
            return $arr;
        }else{
            return false;
        }
    }

    //定义增改函数
    public function add_save($aaa){
        $advCate=M('jd_adv_cate')->where(array('token'=>session('token')))->select();//读取顾问类型
            $arr['content']='选择顾问类型';
            $arr2[0]=$arr;
        foreach($advCate as $k=>$v){
            $arr['value']=$v['name'];
            $arr['content']=$v['name'];
            $arr2[$k+1]=$arr;
        }
        $this->$aaa('jd_adviser',array(
            array('title'=>"顾问名称",'type'=>"input",'name'=>"name",'value'=>'name','msg'=>'请填写顾问名称','tishi'=>'填写顾问名称'),

        		array('type'=>'img','many'=>array(
        				array('title'=>"顾问头像",'type'=>"img",'name'=>"head",'value'=>'head','width'=>60,'height'=>60),
        		)),
        	
        		array('type'=>'select','title'=>"顾问分类",'name'=>"type",'value'=>'type','msg'=>'请选择顾问分类','many'=>array(
        				array('content'=>'请选择顾问分类'),
        				array('value'=>'总部顾问', 'content'=>'总部顾问'),
        				array('value'=>'机构顾问','content'=>'机构顾问'),
        				array('value'=>'伙伴顾问','content'=>'伙伴顾问'),
        		)),
        		
        		
            array('type'=>'select','title'=>"顾问类型",'name'=>"cate",'value'=>'cate','msg'=>'请选择顾问类型','many'=>$arr2), 
            array('title'=>"擅长领域",'type'=>"input",'name'=>"trade",'value'=>'trade','msg'=>'请填写顾问擅长领域','tishi'=>'请填写顾问擅长领域'),
            array('title'=>"顾问电话",'type'=>"input",'name'=>"phone",'value'=>'phone','msg'=>'请填写顾问电话' ,'tishi'=>'请填写顾问电话'),
        	array('title'=>"QQ",'type'=>"input",'name'=>"qq",'value'=>'qq' ,'tishi'=>'请填写qq'),
        	array('title'=>"邮箱",'type'=>"input",'name'=>"email",'value'=>'email' ,'tishi'=>'请填写邮箱'),
        		
        		array('title'=>"地区",'type'=>"input",'name'=>"address",'value'=>'address' ,'tishi'=>'请填写地区'),
        		array('title'=>"所属公司",'type'=>"input",'name'=>"gs",'value'=>'gs' ,'tishi'=>'请填写公司'),
        	/* 	array('title'=>"推荐人",'type'=>"input",'name'=>"name2",'value'=>'name2' ,'tishi'=>'请填写推荐人'), */
        		
        		array('type'=>'radio','title'=>"星级",'name'=>"xin",'value'=>'xin','many'=>array(
        				array('value'=>'1','content'=>'一星'),
        				array('value'=>'2','content'=>'二星'),
        				array('value'=>'3','content'=>'三星'),
        				array('value'=>'4','content'=>'四星'),
        				array('value'=>'5','content'=>'五星'),
        		)),
        		
            array('title'=>"顾问履历",'type'=>"textarea2",'name'=>"remark",'value'=>'remark'),
            array('title'=>"项目经验",'type'=>"textarea2",'name'=>"exper",'value'=>'exper'),


        ),U('JdAdviser/index',array('token'=>$_SESSION['token'])),
        array($this,'bbc')
    );

    }
    public function bbc($data){
        $data['add_time']=time();
        $data['status']=1;
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
    	$status = M('jd_adviser')->where(get(id))->getField('status');
    	if($status){
    		script("已审核的顾问不能删除");
    	}else{
    		$this->del('jd_adviser');
    	}
    }
    //删除
    public function delete_talk(){
        $this->del('jd_talk');
    }
    
    //删除
    public function delete_content2(){
    	
    	$status = M('jd_apply')->where(get(id))->getField('status');
    	if($status){
    		script("已审核的顾问不能删除");
    	}else{
    		$this->del('jd_apply');
    	}
    	
    }

    //其它信息开头
    public function xx(){
        $this->table(
            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdAdviser/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
                    array(
                        'name'   => '返回',//2级
                        'url'    => U('JdAdviser/index',array('uid'=>$_GET['id']))
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
                        'url'  => U('JdAdviser/save_xx',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('JdAdviser/delete_xx',array('token'=>$_SESSION['token']))
                    ),
                    array(
                        'name'=>'设置预约',
                        'url'=>U(),
                    ),
                ),

            ),
            M('jd_adviser')->where(array('token'=>$_SESSION['token'],'uid'=>$_GET['id'],'status'=>1))->count(),
            M('jd_adviser')->field('id,name,color')->where(array('token'=>$_SESSION['token'],'status'=>1,'uid'=>$_GET['id'])),
            array($this,'xxabc')
        );
    }

    //定义增改函数
    public function add_save_xx($aaa){
        //特殊点选框,复选框,下拉列表
        /* 		$list=M('JdAdviser2')->select();//被添加内容的表
            foreach ($list as $k=>$v){
        $list[$k]['content']=$v['name'];//把内容子段改成content
        $list[$k]['value']=$v['id'];//把id子段改成value
        unset($list[$k]['name']);//删除原来的内容子段
        } */
        $this->$aaa('jd_apply',array(
        		
        		array('title'=>"申请人",'type'=>"input",'name'=>"name",'value'=>'name'),
        		array('title'=>"申请人手机",'type'=>"input",'name'=>"tel",'value'=>'tel'),
        		array('title'=>"所属行业",'type'=>"input",'name'=>"hy",'value'=>'hy'),
        		array('title'=>"申请机构",'type'=>"input",'name'=>"jg",'value'=>'jg'),
        		array('title'=>"兑争对手",'type'=>"input",'name'=>"ds",'value'=>'ds'),
        		
            array('title'=>"申请内容",'type'=>"textarea2",'name'=>"content",'value'=>'content'),
        ),$_SESSION['urlurl'],array($this,'bbc'),1);

    }
    public function save_xx(){
        $this->add_save_xx(Edit);
    }
    public function delete_xx(){
        $this->del('jd_adviser');
    }
    //其它信息结束

    public function ck(){
   
    	
        $_SESSION['yyid']=$_GET['id'];
        $aWhere['token']=$_SESSION['token'];
        if($_SESSION['yyid']){
            $aWhere['adviser_id']=$_SESSION['yyid'];
        }

        $datas=M('jd_apply')->field('id,user_id,adviser_id,time')->where($aWhere)->select();
        foreach ($datas as $ke=>$v){
        	$datas[$v['user_id']]['user_id']=$v['user_id'];
        	unset($datas[$ke]);
        }
        foreach ($datas as $ke=>$v){
        	$datas[$ke]['name']=M('jd_user')->where(array('id'=>$v['user_id']))->getField('name');
        	$datas[$ke]['value']=$v['user_id'];
        }
       
        //组合时间
        $a = -86400;//因为for有个$a+=86400; 为了取当前选定时间-86400
        for ($i=0; $i<15; $i++){
        	$a+=86400;
        	$aDate[] = date('Y-m-d',time()+$a);
        } //取得当前选定时间，当前选定后一天时间
        foreach ($aDate as $ke=>$v){
        	$list[$ke]['value']=strtotime($v);
        	$list[$ke]['name']=$v;
        }
        //
        
        //排序
        if($_GET['sortInt']==1){
        	$_GET['sortInt']=0;
        	if($_GET['sort']) $sort=$_GET['sort']." ".desc;
        }elseif($_GET['sortInt']==0){
        	$_GET['sortInt']=1;$sort=$_GET['sort'];
        }
        if(!$_GET['sort']) unset($_SESSION['aWhere']);
        
        //搜索
        if(IS_POST){
        	$_POST=$_REQUEST;
        	$aWhere=$this->search($_POST);
	        $aWhere['token']=$_SESSION['token'];
	        $aWhere['adviser_id']=$_SESSION['yyid'];
        }//结束
        
        
        
        $this->table(

            array(
                'abc'=>123,
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('JdAdviser/index', array('token' => $this->_sToken)),//栏目样式
                'Head_Opt' => array(
       /*              array(
                        'name'   => '未完成预约',//2级
                        'url'    => U('JdAdviser/ck',array('status'=>0,'token'=>$_SESSION['token']))
                    ),
                    array(
                        'name'   => '已完成预约',//2级
                        'url'    => U('JdAdviser/ck',array('status'=>1,'token'=>$_SESSION['token']))
                    )  */
                		
                		array(
                				'name'   => '返回',//2级
                				'url'    => U('JdAdviser/index',array('status'=>1,'token'=>$_SESSION['token']))
                		)
                ),
                'tips' => array(//3级
                    '您可以在这里管理顾问信息以及顾问预约'
                ),
                'Table_Header' => array(//4级
                    'ID|id','顾问名称',  '申请人', '申请人手机','所属行业','申请机构','兑争对手','申请时间|add_time','审核状态|status','操作'
                ),
                'List_Opt' => array(
            /*         array(
                        'name' => '通过审核',
                        'url'  => U('JdAdviser/doAdv',array('token'=>$_SESSION['token']))
                    ), */
                		
                		array(
                				'name' => '查看内容',
                				'url'  => U('JdAdviser/save_xx',array('token'=>$_SESSION['token']))
                		),
                		
                    array(
                        'name' => '删除',
                        'url'  => U('JdAdviser/delete_content2',array('token'=>$_SESSION['token']))
                    ),
                ),
                            'search'=>array(
                            		           // array('title'=>'按会员名称查询','name'=>'eq_user_id','type'=>'select','many'=>$datas),

                            		
                                               array('title'=>'申请人','name'=>'li_name','placeholder'=>'请输入申请人模糊查询','search'=>'查询'),//li是Table里判断条件 name是子段
                                               // array('title'=>'名称','name'=>'eq_name','placeholder'=>'请输入名称精确查询','search'=>'查询'),//eq是Table里判断条件 name是子段
                                                //array('title'=>'添加时间','name'=>'be_add_time','type'=>'between','placeholder'=>'请点击输入时间查询','search'=>'查询'),//be是Table里判断条件 add_time是子段
                                                array('title'=>'按日期查询','name'=>'eq_time','type'=>'select','many'=>$list),
			                            	/* 	array('title'=>'完成状态','name'=>'eq_status','type'=>'select','many'=>array(
			                            				array('value'=>'0','name'=>'未完成'),
			                            				array('value'=>'1','name'=>'已完成'),
			                            		)) */

                            		)
            ),
            M('jd_apply')->where($aWhere)->count(),
            M('jd_apply')->field('id,adviser_id,name,tel,hy,jg,ds,time,status')->where($aWhere)->order($sort),
            array($this,'xxabc')
        );
    }


    public function xxabc($data){
        foreach($data as $k=>$v){

           
            
            $data[$k]['time']=date('Y-m-d',$v['time']);
            $data[$k]['adviser_id']=M('jd_adviser')->where(array('id'=>$v['adviser_id']))->getField('name');
           // $data[$k]['status']=$v['status']?'已完成':'未完成';

            
        }
        return $data;
    }
    //完成预约流程
    public function doAdv(){
        $data=array('id'=>$_GET['id'],'status'=>1);
        if(M('jd_apply')->save($data)){
            $this->success2('操作成功！');
        }else{
        	script("");
        }  
    }
    
    
    public function sh(){
    	M('jd_adviser')->where(get(id))->save(array('status'=>1));
    	script("审核已经通过了","checkAdviser",get(token));
    }
    
    public function no_sh(){
    	M('jd_adviser')->where(get(id))->save(array('status'=>0));
    	script("审核已经取消了","checkAdviser",get(token));
    }

    //是否审核  只 能主健是id, 才能是否审核   有意见自己去开发！
    public function stateAjax(){
    	if($_POST['ACTION_NAME']=='ck'){
    	
    		$this->stateAjaxTable('jd_apply',$this->state);
    		
    	}else{
    		$this->stateAjaxTable('jd_adviser',$this->state);
    	}
    	
    }
  


    /*顾问预约情况管理*/
    
    
    

    
}

