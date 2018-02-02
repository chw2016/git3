<?php

class LawyerAction extends Table2Action {

    public $_sTplBaseDir = 'User/default/miye';

    public function _initialize()
    {
    	parent::_initialize();
        $this->pz	   = D('No_credit');
        $this->pz1   = D('Credit');
        $this->order  =M('No_credit_order');
        $this->tpl="tpl/User/default/helper/";
        $this->token=$this->_sToken;
		
    }
    
    protected function setHeader(){
    	return array(
            array(
                    'name' => '客户列表',
                    'url'  => U('index', array('token' => $this->token))
            ),
            array(
                'name' => '律师列表',
                'url'  => U('lawyer', array('token' => $this->token))
            ),
            array(
                'name' => '案件管理',
                'url'  => U('CaseManagement', array('token' => $this->token))
            ),
            array(
                'name' => '评价管理',
                'url'  => U('commentall', array('token' => $this->token))
            ),
            array(
                'name' => '站内信管理',
                'url'  => U('messageall', array('token' => $this->token))
            ),
            array(
                'name' => '意见反馈管理',
                'url'  => U('ideall', array('token' => $this->token))
            ),
            array(
                'name' => '实用工具',
                'url'  => U('tool', array('token' => $this->token))
            ),
            array(
                'name' => '留言管理',
                'url'  => U('gbook', array('token' => $this->token))
            ),
            array(
                'name' => '关于我们',
                'url'  => U('contact', array('token' => $this->token))
            ),
            array(
                'name' => '法律产品',
                'url'  => U('product', array('token' => $this->token))
            ),
            array(
                'name' => '友情链接',
                'url'  => U('links', array('token' => $this->token))
            ),
            array(
                'name' => '材料管理',
                'url'  => U('material', array('token' => $this->token))
            ),

            );
    }


    /**
     *  客户列表
     **/
	public function index(){
        $where['token'] =$this->_sToken;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $where = $this->search($po);
            foreach($where as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $where[$b]=$v;
                        unset($where[$k]);
                    }
            }
                $where['token'] = $this->_sToken;
                //$where['tp_hn_users.renwei'] =1;

                session('where_p',null);
                session('where_p',$where);
            $this->table(
                array(

                    'HeadHover' => U('index', array('token' => $this->token)),
                    'Head_Opt' => array(
                     array(
                          'name' => '导出Excel表',
                          'url' => U('excel',array('token'=>$this->token))
                      ),


					),
                    'tips' => array(
                        '客户列表'
                    ),
                    'Table_Header' => array(
                        'ID', '姓名','手机号码','Email','所在地','性别','职业','时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '我的案件',
                            'url' => U('lawyer_case')
                        ),
                        array(
                            'name' => '我的留言',
                            'url' => U('leave')
                        ),
                        array(
                            'name' => '对我的评价',
                            'url' => U('comment')
                        ),
						array(
                            'name' => '站内信',
                            'url' => U('message')
                        ),
						array(
                            'name' => '意见反馈',
                            'url' => U('idea')
                        ),
                        

                    ),
                  'search'=>array(
							 array('title'=>'姓名','name'=>'li_name'),
							 array('title'=>'手机号码','name'=>'li_phone'),
                    )
                ),
                M('Lawyer_client')
                ->where($where)
                ->order('add_time desc')
                ->count(),
                M('Lawyer_client')
                ->field('id,name,phone,email,location,sex,job,add_time')
                ->order('add_time desc')
                ->where($where),
				array($this,'abc')
            );
        $this->UDisplay('show1');
	}
    public function abc($data){
        foreach($data as $k=>$v){
            if($v['sex']==1){
                $data[$k]['sex'] = '未知';
            }if($v['sex']==2)
            {
                $data[$k]['sex'] = '男';
            }if($v['sex']==3)
            {
                $data[$k]['sex']=='女';
            }
        }
        return $data;
    }
    //导出客户表
    public function excel()
    {
        $data =  M('Lawyer_client')
            ->field('id,name,phone,email,location,sex,job,add_time')
            ->where(array('token'=>$this->token))->select();
        foreach($data as $k=>$v){
           if($v['sex']==1){
                $data[$k]['sex'] = '未知';
            }if($v['sex']==2)
            {
                $data[$k]['sex'] = '男';
            }if($v['sex']==3)
            {
                $data[$k]['sex']=='女';
            }
        }
        Excel::arr2ExcelDownload($data,array('编号', '姓名','手机号码','Email','所在地','性别','职业','时间'),'客户信息');
    }
    //导出律师表
    public function excels()
    {
        $data =  M('Lawyer_lawyer')
            ->field('id,name,phone,email,location,sex,job,education,suffer,realm,licesno,loca,year,add_time')
            ->where(array('token'=>$this->token))->select();
        foreach($data as $k=>$v){
           if($v['sex']==1){
                $data[$k]['sex'] = '未知';
            }if($v['sex']==2)
            {
                $data[$k]['sex'] = '男';
            }if($v['sex']==3)
            {
                $data[$k]['sex']=='女';
            }
        }
        Excel::arr2ExcelDownload($data,array('编号', '姓名','手机号码','Email','所在地','性别','职业','学历','办理经验','领域','证号','所在地','年限','时间'),'客户信息');
    }
	//我的案件
	public function lawyer_case()
	{       $id = $_GET['id'];
		    $name = M('Lawyer_client')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['uid'] =$id;
		    $po = array_merge($_GET,$_POST);
            //p($po);die;
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['uid'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('lawyer_case', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的案件列表'
                ),
                'Table_Header' => array(
                    '编号','案件类型', '起诉地点','律师地点', '纠纷类型','是否通过','委托状态','时间','操作'
                ),
                'List_Opt' => array(
                    
                        array(
                            'name' => '案件详情',
                            'url' => U('case_detail')
                        ),
                ),
              'search'=>array(
                    array('title'=>'案件类型','name'=>'eq_status','type'=>'select','many'=>array(
                        array('value'=>'1','name'=>'民事纠纷'),
                        array('value'=>'2','name'=>'刑事案件'),
                        array('value'=>'3','name'=>'行政纠纷'),
                    )),
              ),
            ),

            M('Lawyer_case')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_case')
                ->field('id,status,start_address,end_address,class,pass,trust,add_time')
                ->order('add_time desc')
                ->where($aWhere),
            array($this,'shop_status')

        );

        $this->UDisplay('show1');
	}
    //对我的留言
    public function leave()
    {       $id = $_GET['id'];
            $name = M('Lawyer_client')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['uid'] =$id;
            $po = array_merge($_GET,$_POST);
            //p($po);die;
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['uid'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('comment', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的留言列表'
                ),
                'Table_Header' => array(
                    '编号','内容','时间',''
                ),
             
              ),


            M('Lawyer_leave')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_leave')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)
            //array($this,'shop_status')

        );

        $this->UDisplay('show1');
    }
    //对我的评价
    public function comment()
    {       $id = $_GET['id'];
            $name = M('Lawyer_client')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['uid'] =$id;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['uid'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('comment', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的评价列表'
                ),
                'Table_Header' => array(
                    '编号','评价内容','时间',''
                ),
             
              ),


            M('Lawyer_eval')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_eval')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)
            //array($this,'shop_status')

        );

        $this->UDisplay('show1');
    }
    //我的站内信
    public function message()
    {       $id = $_GET['id'];
            $name = M('Lawyer_client')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['uid'] =$id;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['uid'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('comment', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的站内信列表'
                ),
                'Table_Header' => array(
                    '编号','标题','内容','状态','时间',''
                ),
              ),


            M('Lawyer_message')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_message')
                ->field('id,title,content,status,add_time')
                ->order('add_time desc')
                ->where($aWhere),
            array($this,'Message_send')

        );

        $this->UDisplay('show1');
    }
    public function Message_send($data){
        foreach($data as $k=>$v){
            if($v['status']==1)
            {
                $data[$k]['status'] = '已读';
            }else{
                $data[$k]['status'] = '未读';
            }
        }
        return $data;
    }

    //客户反馈列表
    public function idea()
    {       $id = $_GET['id'];
            $name = M('Lawyer_client')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['uid'] =$id;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['uid'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('idea', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的反馈列表'
                ),
                'Table_Header' => array(
                    '编号','标题','内容','时间',''
                ),
              ),


            M('Lawyer_idea')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_idea')
                ->field('id,title,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)

        );

        $this->UDisplay('show1');
    }
    public function shop_status($data)
    {
        foreach($data as $key=>$v)
        {
            if($v['status']==1)
            {
                $data[$key]['status']='民事纠纷';
            }
            else if($v['status']==2)
            {
                $data[$key]['status']='刑事案件';
            }
            else if($v['status']==3)
            {
                $data[$key]['status'] = '行政纠纷';
            }
            if($v['pass']==1)
            {
                 $data[$key]['pass']='通过';
            }
            if($v['pass']==2)
            {
                 $data[$key]['pass']='不通过';
            }
            if($v['pass']==3)
            {
                 $data[$key]['pass']='审核中';
            }
            if($v['trust']==1)
            {
                 $data[$key]['trust']='待办理';
            }
            if($v['trust']==2)
            {
                 $data[$key]['trust']='正在办理';
            }
            if($v['trust']==3)
            {
                 $data[$key]['trust']='已结案';
            }
            
        }
        return $data;
    }
    //案件详情
    public function case_detail(){
        $id = $_GET['id'];
        $status = M('Lawyer_case')->where('id='.$id)->getField('trust');
        $uid = M('Lawyer_case')->where('id='.$id)->getField('uid');
        $info = M('Lawyer_case')->where('id='.$id)->find();
        $name = M('Lawyer_client')->where('id='.$uid)->getField('name');
        if($status==3)
        {
            $this->error2('该案件已经结案了');
        }
        if($status==1)//待办理
        {
            $this->assign('name',$name);
            $this->assign('info',$info);
            $this->display('./tpl/User/default/lawyer/details.html');
        }if($status==2)//正在办理
        {
            $this->display('client.html');
        }
    }
    //律师案件详情
    public function case_details(){
        $id = $_GET['id'];
        $status = M('Lawyer_case')->where('id='.$id)->getField('trust');
        $uid = M('Lawyer_bid')->where('lawyer_id='.$id)->getField('lawyer_id');
        $case_id = M('Lawyer_bid')->where('lawyer_id='.$id)->getField('case_id');
        $info = M('Lawyer_case')->where('id='.$case_id)->find();
        $name = M('Lawyer_lawyer')->where('id='.$uid)->getField('name');
        if($status==3)
        {
            $this->error2('该案件已经结案了');
        }
        if($status==1)//待办理
        {
            $this->assign('name',$name);
            $this->assign('info',$info);
            $this->display('./tpl/User/default/lawyer/law_details.html');
        }if($status==2)//正在办理
        {
            $this->display('lawyer.html');
        }
    }
    //// 案件管理
    public function CaseManagement(){
            $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('CaseManagement', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '案件管理列表，当审核通过时，律师才能进行投标'
                ),
                'Table_Header' => array(
                    '编号','案件类型','起诉地点','律师地点','纠纷类型','状态','委托','时间','操作'
                ),
                'List_Opt' => array(
                        array(
                            'name' => '审核',
                            'url' => U('check')
                        ),
                        array(
                            'name' => '进行投标',
                            'url' => U('lawyer_biding')
                        ),
                        array(
                            'name' => '结案',
                            'url' => U('lawsuit')
                        ),
                    ),
                  'search'=>array(
                        array('title'=>'案件类型','name'=>'eq_status','type'=>'select','many'=>array(
                        array('value'=>'1','name'=>'民事纠纷'),
                        array('value'=>'2','name'=>'刑事案件'),
                        array('value'=>'3','name'=>'行政纠纷'),
                    )), 
                       // array('type'=>'br'),
                        array('title'=>'案件类型','name'=>'eq_pass','type'=>'select','many'=>array(
                        array('value'=>'1','name'=>'通过'),
                        array('value'=>'2','name'=>'不通过'),
                        array('value'=>'3','name'=>'审核中'),
                    )),
                    )
              ),
            M('Lawyer_case')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_case')
                ->field('id,status,start_address,end_address,class,pass,trust,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'pass')

        );
        $this->UDisplay('show1');
    }
    public function pass($data)
    {
        foreach($data as $key=>$v)
        {
            if($v['status']==1)
            {
                $data[$key]['status']='民事纠纷';
            }
            else if($v['status']==2)
            {
                $data[$key]['status']='刑事案件';
            }
            else if($v['status']==3)
            {
                $data[$key]['status'] = '行政纠纷';
            }
            if($v['pass']==1)
            {
                 $data[$key]['pass']='通过';
            }
            if($v['pass']==2)
            {
                 $data[$key]['pass']='不通过';
            }
            if($v['pass']==3)
            {
                 $data[$key]['pass']='审核中';
            }
            if($v['trust']==1)
            {
                 $data[$key]['trust']='待办理';
            }
            if($v['trust']==2)
            {
                 $data[$key]['trust']='正在办理';
            }
            if($v['trust']==3)
            {
                 $data[$key]['trust']='已结案';
            }
            
        }
        return $data;
    }
    public function lawsuit(){
        $id = $_GET['id'];
        $cid = M('Lawyer_case')->where('id='.$id)->find();
        if($cid['pass']==2||$cid['pass']==3)
        {
            $this->error2('该案件审核还没有通过');
        }
        if($cid['trust']==1||$cid['trust']==3)
        {
            $this->error2('该案件正在等待办理或者已结案');
        }
        $this->Edit('Lawyer_case',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'trust','many'=>array(
                    array('content'=>"结案",'value'=>"3")
                )),
        ),U('CaseManagement',array('token'=>$this->token)));

    }
    public function check(){

        $info=M('Lawyer_case')->field('pass')->find($_GET['id']);
        if($info['pass']==1||$info['pass']==2)
        {
             $this->error2('已审核过了');
        }
        if($info['pass']==3){
            $this->assign('tishi','审核案件是否通过,当审核通过时，律师才能进行投标');
            $this->Edit('Lawyer_case',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'pass','many'=>array(
                    array('content'=>"通过",'value'=>"1"),
                    array('content'=>"不通过",'value'=>"2")
                )),
            ),U('CaseManagement',array('token'=>$this->token)));
        }
    }
    //律师列表
    public function lawyer(){
            $where['token'] =$this->_sToken;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $where = $this->search($po);
            foreach($where as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $where[$b]=$v;
                        unset($where[$k]);
                    }
            }
                $where['token'] = $this->_sToken;
                //$where['tp_hn_users.renwei'] =1;

                session('where_p',null);
                session('where_p',$where);
            $this->table(
                array(

                    'HeadHover' => U('lawyer', array('token' => $this->token)),
                    'Head_Opt' => array(
                     array(
                          'name' => '导出Excel表',
                          'url' => U('excels',array('token'=>$this->token))
                      ),
                    ),
                    'tips' => array(
                        '律师列表'
                    ),
                    'Table_Header' => array(
                        'ID', '姓名','手机号码','Email','所在地','性别','职业','学历','经验','时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '我的案件',
                            'url' => U('lawyer_cases')
                        ),
                        array(
                            'name' => '我的留言',
                            'url' => U('leaves')
                        ),
                        array(
                            'name' => '对我的评价',
                            'url' => U('comments')
                        ),
                        array(
                            'name' => '站内信',
                            'url' => U('messages')
                        ),
                        array(
                            'name' => '意见反馈',
                            'url' => U('ideas')
                        ),
                    ),
                  'search'=>array(
                             array('title'=>'姓名','name'=>'li_name'),
                             array('title'=>'手机号码','name'=>'li_phone'),
                    )
                ),
                M('Lawyer_lawyer')
                    ->where($where)
                    ->order('add_time desc')
                    ->count(),
                M('Lawyer_lawyer')
                    ->field('id,name,phone,email,location,sex,job,education,suffer,add_time')
                    ->order('add_time desc')
                    ->where($where),
                    array($this,'abc')
            );
        $this->UDisplay('show1');
    }

    //律师留言
    public function leaves()
    {       $id = $_GET['id'];
            $name = M('Lawyer_lawyer')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['lawyer_id'] =$id;
            $po = array_merge($_GET,$_POST);
            //p($po);die;
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['lawyer_id'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('comment', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的留言列表'
                ),
                'Table_Header' => array(
                    '编号','内容','时间',''
                ),
             
              ),


            M('Lawyer_leave')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_leave')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)
            //array($this,'shop_status')

        );

        $this->UDisplay('show1');
    }

    //律师的案件
    public function lawyer_cases()
    {       $id = $_GET['id'];
            $name = M('Lawyer_lawyer')->where(array('id'=>$id))->getField('name');
            $case_id = M('Lawyer_bid')->where(array('lawyer_id'=>$id))->getField('case_id');
            $aWhere['token'] =$this->token;
            $aWhere['id'] =$case_id;
            $po = array_merge($_GET,$_POST);
            //p($po);die;
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['id'] =$case_id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('lawyer_case', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的案件列表'
                ),
                'Table_Header' => array(
                    '编号','案件类型', '起诉地点','律师地点', '纠纷类型','是否通过','委托状态','时间','操作'
                ),
                'List_Opt' => array(
                    
                        array(
                            'name' => '案件详情',
                            'url' => U('case_details')
                        ),
                ),
              'search'=>array(
                    array('title'=>'案件类型','name'=>'eq_status','type'=>'select','many'=>array(
                        array('value'=>'1','name'=>'民事纠纷'),
                        array('value'=>'2','name'=>'刑事案件'),
                        array('value'=>'3','name'=>'行政纠纷'),
                    )),                
              ),
            ),

            M('Lawyer_case')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_case')
                ->field('id,status,start_address,end_address,class,pass,trust,add_time')
                ->order('add_time desc')
                ->where($aWhere),
            array($this,'pass')

        );
        $this->UDisplay('show1');
    }

     //对我的评价
    public function comments()
    {       $id = $_GET['id'];
            $name = M('Lawyer_lawyer')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['send_id'] =$id;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['send_id'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('comments', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的评价列表'
                ),
                'Table_Header' => array(
                    '编号','评价内容','时间',''
                ),
             
              ),


            M('Lawyer_eval')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_eval')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)
            //array($this,'shop_status')

        );

        $this->UDisplay('show1');
    }
    //我的站内信
    public function messages()
    {       $id = $_GET['id'];
            $name = M('Lawyer_lawyer')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['lawyer_id'] =$id;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['lawyer_id'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('comment', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的站内信列表'
                ),
                'Table_Header' => array(
                    '编号','标题','内容','状态','时间',''
                ),
              ),


            M('Lawyer_message')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_message')
                ->field('id,title,content,status,add_time')
                ->order('add_time desc')
                ->where($aWhere),
            array($this,'Message_send')

        );

        $this->UDisplay('show1');
    }

    //客户反馈列表
    public function ideas()
    {       $id = $_GET['id'];
            $name = M('Lawyer_lawyer')->where(array('id'=>$id))->getField('name');
            $aWhere['token'] =$this->token;
            $aWhere['lawyer_id'] =$id;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['lawyer_id'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('idea', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    $name.'的反馈列表'
                ),
                'Table_Header' => array(
                    '编号','标题','内容','时间',''
                ),
              ),


            M('Lawyer_idea')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_idea')
                ->field('id,title,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)

        );

        $this->UDisplay('show1');
    }



    //律师投标
    public function lawyer_biding(){
            $id = $_GET['id'];
            $case = M('Lawyer_case')->where('id='.$id)->getField('pass');
            if($case==2||$case==3)
            {
                $this->error2('该案件审核不通过或者还没有审核');
            }
            $aWhere['tp_lawyer_bid.token'] =$this->token;
            $aWhere['tp_lawyer_bid.case_id'] =$id;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['tp_lawyer_bid.token'] = $this->token;
            $aWhere['tp_lawyer_bid.case_id'] =$id;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('lawyer_biding', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '律师投标',
                    '注意：案件通过时才能进行投标'
                ),
                'Table_Header' => array(
                    '编号','律师姓名','案件类型','纠纷类型','是否通过','状态','操作'
                ),
                'List_Opt' => array(
                        array(
                            'name' => '投标',
                            'url' => U('bidding')
                        ),
                    ),
                  'search'=>array(
                             array('title'=>'姓名','name'=>'li_name'),
                             array('title'=>'手机号码','name'=>'li_phone'),
                    )
              ),
            M('Lawyer_case')
                ->join('join tp_lawyer_bid on tp_lawyer_case.id=tp_lawyer_bid.case_id')
                ->join('join tp_lawyer_lawyer on tp_lawyer_bid.lawyer_id=tp_lawyer_lawyer.id')
                ->where($aWhere)
                ->order('tp_lawyer_bid.add_time desc')
                ->count(),
            M('Lawyer_case')
                ->join('join tp_lawyer_bid on tp_lawyer_case.id=tp_lawyer_bid.case_id')
                ->join('join tp_lawyer_lawyer on tp_lawyer_bid.lawyer_id=tp_lawyer_lawyer.id')
                ->field('tp_lawyer_bid.id,tp_lawyer_lawyer.name,tp_lawyer_case.status,tp_lawyer_case.class,tp_lawyer_case.pass,tp_lawyer_bid.status as c')
                ->order('tp_lawyer_bid.add_time desc')
                ->where($aWhere),
                array($this,'bid')
        );
        $this->UDisplay('show1');
    }
    public function bid($data){
        foreach($data as $key=>$v)
        {
            if($v['status']==1)
            {
                $data[$key]['status']='民事纠纷';
            }
            else if($v['status']==2)
            {
                $data[$key]['status']='刑事案件';
            }
            else if($v['status']==3)
            {
                $data[$key]['status'] = '行政纠纷';
            }
            if($v['pass']==1)
            {
                 $data[$key]['pass']='通过';
            }
            if($v['pass']==2)
            {
                 $data[$key]['pass']='不通过';
            }
            if($v['pass']==3)
            {
                 $data[$key]['pass']='审核中';
            }
            if($v['c']==1)
            {
                $data[$key]['c']='正在办理';
            }
            if($v['c']==2)
            {
                $data[$key]['c']='已完结';
            }
            if($v['c']==3)
            {
                $data[$key]['c']='提交中';
            }
            if($v['c']==4)
            {
                $data[$key]['c']='中标';
            }
            if($v['c']==5)
            {
                $data[$key]['c']='不中标';
            }
        }
        return $data;
    }

    //投标
    public function bidding(){

        $info=M('Lawyer_bid')->field('status,case_id')->find($_GET['id']);
        $case = M('Lawyer_case')->where(array('id'=>$info['case_id']))->field('pass,id,trust')->find();
        if($case['trust']==2||$case['trust']==3)
        {
            $this->error2('该案件已经结案了或者正在办理');
        }
        if($case['pass']==2)
        {
            $this->error2('该案情审核不通过');
        }
        if($info['status']==4)
        {
             $this->error2('该案情已经中标了');
        }
        if($info['status']==5)
        {
             $this->error2('该案情已经不中标了');
        }
        if($info['status']==3){
            $this->assign('tishi','进行投标');
            $this->Edit('Lawyer_bid',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"中标",'value'=>"4"),
                    array('content'=>"不中标",'value'=>"5")
                )),
            ),U('CaseManagement',array('token'=>$this->token)),array($this,'case_trust'));
        }
    }
    public function case_trust($data){
        if($data['status']==4){
            $id = M('Lawyer_bid')->where('id='.$data['id'])->find();
            $p['trust'] = 2;//委托中
            $trust = M('Lawyer_case')->where('id='.$id['case_id'])->save($p);
        }
        return $data;
    }

    //总的评价
    public function commentall()
    {
            $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('commentall', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '总评价列表'
                ),
                'Table_Header' => array(
                    '编号','评价内容','时间',''
                ),
                'List_Opt' => array(
                    array(
                            'name' => '删除',
                            'url' => U('deletecomment')
                    ),
                ),
              ),


            M('Lawyer_eval')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_eval')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)
        );

        $this->UDisplay('show1');
    }
    public function deletecomment(){
        $this->del('Lawyer_eval');
    }
    //所有站内信管理
     public function messageall()
    {
            $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('messageall', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '站内信列表'
                ),
                'Table_Header' => array(
                    '编号','标题','内容','状态','时间',''
                ),
                'List_Opt' => array(
                    array(
                            'name' => '删除',
                            'url' => U('deletemessage')
                    ),
                ),
              ),


            M('Lawyer_message')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_message')
                ->field('id,title,content,status,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'Message_send')

        );

        $this->UDisplay('show1');
    }
    public function deletemessage(){
        $this->del('Lawyer_message');
    }
    //意见反馈管理
    public function ideall(){

        $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('ideall', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '内容列表'
                ),
                'Table_Header' => array(
                    '编号','内容','时间',''
                ),
              ),


            M('Lawyer_idea')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_idea')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)
                //array($this,'Message_send')

        );

        $this->UDisplay('show1');
    }
    //实用工具
    public function tool(){
        $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);
            $this->table(
            array(
                'HeadHover' => U('tool', array('token' => $this->token)),
                'Head_Opt' => array(
                   array(
                          'name' => '添加工具',
                          'url' => U('tool_add',array('token'=>$this->token))
                      ),
                ),
                'tips' => array(
                    '工具列表'
                ),
                'Table_Header' => array(
                    '编号','标题','时间',''
                ),
                'List_Opt' => array(
                    array(
                            'name' => '添加下级工具',
                            'url' => U('lower_tool')
                    ),
                    array(
                            'name' => '删除',
                            'url' => U('del_tool')
                    ),
                    array(
                            'name' => '修改',
                            'url' => U('edit_tool')
                    ),
                ),
              ),


            M('Lawyer_money')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_money')
                ->field('id,title,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'tool_time')
        );

        $this->UDisplay('show1');
    }
    public function tool_time($data){
        foreach($data as $k =>$v )
        {
            $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    public function del_tool(){
        $this->del('Lawyer_money');
    }
    //添加工具
    public function tool_add(){
        $this->add('Lawyer_money',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'标题不能为空'),
            // array('title'=>"链接地址",'type'=>"input",'name'=>"url",'msg'=>'链接地址不能为空','placeholder'=>'请加上http://'),
            // array('type'=>'img','many'=>array(
            //     array('title'=>"项目主图",'name'=>"logopic"),
            //     array('title'=>"详情页背景",'name'=>"back_pic")

            // )),
            //array('type'=>"hidden",'name'=>"add_time"),
        ),U('tool',array('token'=>$this->token)));
    }
    public function edit_tool(){
        $this->Edit('Lawyer_money',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'标题不能为空'),
        ),U('tool',array('token'=>$this->token)));
    }
    public function lower_tool(){
        $toolid = $_GET['id'];
        $title = M('Lawyer_money')->where('id='.$toolid)->getField('title');
        $aWhere['token'] =$this->token;
        $aWhere['uid'] =$toolid;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            $aWhere['uid'] =$toolid;
            session('where_p',null);
            session('where_p',$aWhere);
            $this->table(
            array(
                'HeadHover' => U('lower_tool', array('token' => $this->token)),
                'Head_Opt' => array(
                    array(
                            'name'=>'添加下级工具',
                            'url'=>U('lower_add',array('token'=>$this->token,'toolid'=>$toolid))
                        ),
                    array(
                            'name'=>'返回',
                            'url'=>U('tool',array('token'=>$this->token))
                        ),
                ),
                'tips' => array(
                    $title.' 的下级工具列表'
                ),
                'Table_Header' => array(
                    '编号','标题','时间',''
                ),
                'List_Opt' => array(
                    array(
                            'name' => '修改下级工具',
                            'url' => U('lower_tool_edit')
                    ),
                    array(
                            'name' => '删除下级工具',
                            'url' => U('lower_tool_del')
                    ),
                ),
              ),
            M('Lawyer_query')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_query')
                ->field('id,title,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'tool_time')
        );
        $this->UDisplay('show1');
    }
    //添加下级工具
    public function lower_add(){
        $toolid = $_GET['toolid'];
        $this->add('Lawyer_query',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'标题不能为空'),
            array('title'=>"链接地址",'type'=>"input",'name'=>"url",'msg'=>'链接地址不能为空','placeholder'=>'请加上http://'),
            array('title'=>'','type'=>"hidden_true",'name'=>"uid",'value'=>$toolid),
        ),U('tool',array('token'=>$this->token)));
    }
    //修改下级工具
    public function lower_tool_edit(){
        $this->Edit('Lawyer_query',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'标题不能为空'),
            array('title'=>"链接地址",'type'=>"input",'name'=>"url",'msg'=>'链接地址不能为空','placeholder'=>'请加上http://'),
        ),U('tool',array('token'=>$this->token)));
    }
    //删除下级工具
    public function lower_tool_del(){
        $this->del('Lawyer_query');
    }
    //总留言
    public function gbook()
    {      
            $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            //p($po);die;
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('gbook', array('token' => $this->token)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '留言列表'
                ),
                'Table_Header' => array(
                    '编号','内容','时间',''
                ),
                'List_Opt'=>array(
                        array(
                            'name'=>'删除',
                            'url'=>U('del_gbook',array('token'=>$this->token))
                            )
                    )
              ),
            M('Lawyer_leave')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_leave')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere)
        );
        $this->UDisplay('show1');
    }
    public function del_gbook(){
        $this->del('Lawyer_leave');
    }
    //关于我们
    public function contact()
    {
        $this->assign('tishi','关于我们');
            $this->Edit('Lawyer_about',array(
                array('title'=>"公司简介",'type'=>"textarea",'name'=>"desc"),
                array('title'=>'公司动态','type'=>'input','name'=>'state'),
                array('title'=>'联系我们','type'=>'textarea_2','name'=>'contact')
        ),U('CaseManagement',array('token'=>$this->token)));

    }
    //产品
    public function product()
    {      
            $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            //p($po);die;
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('product', array('token' => $this->token)),
                'Head_Opt' => array(
                    array(
                        'name'=>'添加产品',
                        'url' =>U('add_product',array('token'=>$this->token))
                    )
                ),
                'tips' => array(
                    '产品列表'
                ),
                'Table_Header' => array(
                    '编号','标题','产品图片','时间',''
                ),
                'List_Opt'=>array(
                        array(
                            'name'=>'修改',
                            'url'=>U('edit_product',array('token'=>$this->token))
                        ),
                        array(
                            'name'=>'删除',
                            'url'=>U('del_product',array('token'=>$this->token))
                        )
                    )
              ),
            M('Lawyer_product')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_product')
                ->field('id,title,img,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'img1')
        );
        $this->UDisplay('show1');
    }
    public function img1($data){
        foreach($data as $k=>$v)
        {
            $data[$k]['img'] = '<img src='.$v['img'].' width="70px">';
            $data[$k]['add_time'] = date('Y-m-d H:i:s',time());
        }
        return $data;
    }
    //添加产品
    public function add_product(){
        $this->add('Lawyer_product',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'标题不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图",'name'=>"img")
            )),
            array('title'=>"内容",'type'=>"textarea",'name'=>"content")
        ),U('product',array('token'=>$this->token)));
    }
    //修改产品
    public function edit_product(){
        $this->Edit('Lawyer_product',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'标题不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图",'name'=>"img")
            )),
            array('title'=>"内容",'type'=>"textarea",'name'=>"content"),
        ),U('product',array('token'=>$this->token)));
    }
    public function del_product(){
        $this->del('Lawyer_product');
    }
    //友情链接
    public function links(){
        $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('links', array('token' => $this->token)),
                'Head_Opt' => array(
                    array(
                        'name'=>'添加友情链接',
                        'url' =>U('add_links',array('token'=>$this->token))
                    )
                ),
                'tips' => array(
                    '友情链接列表'
                ),
                'Table_Header' => array(
                    '编号','标题','url','时间',''
                ),
                'List_Opt'=>array(
                        array(
                            'name'=>'修改',
                            'url'=>U('edit_links',array('token'=>$this->token))
                        ),
                        array(
                            'name'=>'删除',
                            'url'=>U('del_links',array('token'=>$this->token))
                        )
                    )
              ),
            M('Lawyer_links')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_links')
                ->field('id,title,url,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'tool_time')
        );
        $this->UDisplay('show1');
    }
    //添加产品
    public function add_links(){
        $this->add('Lawyer_links',array(
            array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'标题不能为空'),
            array('title'=>"跳转地址",'type'=>"text",'name'=>"url",'placeholder'=>'请输入http://')
        ),U('links',array('token'=>$this->token)));
    }
    //修改产品
    public function edit_links(){
        $this->Edit('Lawyer_links',array(
           array('title'=>"标题",'type'=>"input",'name'=>"title",'msg'=>'标题不能为空'),
            array('title'=>"跳转地址",'type'=>"text",'name'=>"url",'placeholder'=>'请输入http://')
        ),U('links',array('token'=>$this->token)));
    }
    public function del_links(){
        $this->del('Lawyer_links');
    }
    //材料管理
    public function material(){
        $aWhere['token'] =$this->token;
            $po = array_merge($_GET,$_POST);
            foreach($po as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $po[$k]=$v;
                }
            }
            $aWhere = $this->search($po);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] = $this->token;
            session('where_p',null);
            session('where_p',$aWhere);

        $this->table(
            array(
                'HeadHover' => U('material', array('token' => $this->token)),
                'Head_Opt' => array(
                    // array(
                    //     'name'=>'添加友情链接',
                    //     'url' =>U('add_links',array('token'=>$this->token))
                    // )
                ),
                'tips' => array(
                    '材料管理'
                ),
                'Table_Header' => array(
                    '编号','代理词','质证意见','时间',''
                ),
                'List_Opt'=>array(
                        // array(
                        //     'name'=>'修改',
                        //     'url'=>U('edit_links',array('token'=>$this->token))
                        // ),
                        // array(
                        //     'name'=>'删除',
                        //     'url'=>U('del_links',array('token'=>$this->token))
                        // )
                    )
              ),
            M('Lawyer_img')->where($aWhere)->order('add_time desc')->count(),
            M('Lawyer_img')
                ->field('id,term,review,add_time')
                ->order('add_time desc')
                ->where($aWhere),
                array($this,'tool_time')
        );
        $this->UDisplay('show1');
    }
    

























	//会员信息详情
	public function details(){
        $info=M('Hn_users')
            ->join("join tp_wxusers on tp_wxusers.id=tp_hn_users.uid")
            ->field('tp_hn_users.from_phone,tp_hn_users.id,tp_hn_users.name,tp_hn_users.phone,tp_hn_users.yonjing,tp_hn_users.fid1,tp_hn_users.fid2,tp_wxusers.nickname,tp_hn_users.brand,tp_hn_users.alipay,tp_hn_users.bank,tp_hn_users.status,tp_hn_users.add_time,tp_hn_users.img,tp_hn_users.img1,tp_hn_users.img2')
            ->where(array('token'=>$this->_sToken,'tp_hn_users.id'=>$_GET['id']))->find();
		$id = M('hn_users')->field('id')->where(array('token'=>$this->_sToken,'id'=>$_GET['id']))->getField('id');
		$xiao = M('hn_xiaofeijl')->field('sum(yonjing) as c ')->where(array('token'=>$this->_sToken,'uid'=>$id))->find();
		$zong = M('hn_users')->where(array('token'=>$this->_sToken,'id'=>$_GET['id']))->getField('yonjing');
        $this->assign('zong',$zong);
        $this->assign('info',$info);
        $this->assign('xiao',$xiao);
        $this->display('./tpl/User/default/hn/details.html');
    }
	
	
    public function abc1($data){
        foreach($data as $k=>$v){
			
           if($v['status']==0){
               $data[$k]['status']='新申请';
           }
            if($v['status']==1){
                $data[$k]['status']='审核通过';
            }
            if($v['status']==-1){
                $data[$k]['status']='审核未通过';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    //新的申请
	/*
    public function zhuce(){
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('zhuce', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    /*  array(
                          'name' => '添加非信贷产品',
                          'url' => U('Loan/add_pinzhong',array('token'=>$this->token))
                      ),
                    *//*

                ),
                'tips' => array(
                    '经纪人列表'
                ),
                'Table_Header' => array(
                    'ID', '姓名','手机号码', '身份','推荐人号码','状态','申请时间','操作'
                ),
                'List_Opt' => array(
                     array(
                         'name' => '审核',
                         'url' => U('hs')
                     ),

                ),
                'search'=>array(
                    array('title'=>'姓名','name'=>'li_name'),
                    array('title'=>'手机号码','name'=>'li_phone'),
                    array('title'=>'订单状态','name'=>'eq_status','type'=>'select','many'=>array(
                        array('value'=>'0','name'=>'新申请'),
                        array('value'=>'1','name'=>'通过'),
                        array('value'=>'-1','name'=>'未通过'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                )
            ),

            M('Hn_users')->where($aWhere)->count(),
            M('Hn_users')->field('id,name,phone,fid,from_phone,status,add_time')->where($aWhere),
            array($this,'abc1')

        );
        $this->UDisplay('show1');
    }*/
    //核审经纪人
    public function hs(){
        $info=M('hn_users')->field('status')->find($_GET['id']);
        if($info['status']!=0){
            $this->error2('已审核过了');
        }
        $this->Edit('hn_users',array(
            array('title'=>'审核','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"通过",'value'=>"1"),
                array('content'=>"不通过",'value'=>"-1")
            )),
            array('type'=>'hidden','name'=>'from_phone')
        ),U('zhuce',array('token'=>$this->token)),array($this,'hs1'));
    }
    public function hs1($data){
       // p($data);die;

        if(($data['status']==1)AND($data['from_phone']!='')){//通过关佣金

           if($id= M('Hn_users')->where(array('token'=>$this->_sToken,'phone'=>$data['from_phone'],'status'=>1))->getField('id')){//假

               $a=M('Hn_set')->where(array('token'=>$this->_sToken))->getField('yonjing1');
               if(M('Hn_users')->where(array('id'=>$id))->setInc('yonjing',$a)){
                   $info['token']=$this->_sToken;
                   $info['uid']=$id;
                   $info['add_time']=time();
                   $info['type_id']=$data['id'];
                   $info['content']='推荐'.$data['from_phone'].'成为经纪人所得';
                   $info['yonjing']=$a;
                   M('hn_yonjing_jl')->add($info);
               }
           }


        }
        return $data;
    }
    /**
     *  推荐客户列表
     **/
    public function tuijian(){
        $aWhere['tp_hn_tuijian.token'] =$this->_sToken;
        if(IS_POST){
          //  p($_POST);
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                $b=str_replace('|','.',$k);
                $aWhere[$b]=$v;
                unset($aWhere[$k]);
            }
            $aWhere['tp_hn_tuijian.token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('tuijian', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    /*  array(
                          'name' => '添加非信贷产品',
                          'url' => U('Loan/add_pinzhong',array('token'=>$this->token))
                      ),
                    */

                ),
                'tips' => array(
                    '推荐客户列表'
                ),
                'Table_Header' => array(
                    'ID','经纪人号码','客户姓名','客户号码','状态','推荐时间','备注','推荐楼盘','操作'
                ),
                'List_Opt' => array(
                    /*array(
                        'name' => '备注',
                        'url' => U('other')
                    ),*/
                     array(
                         'name' => '处理',
                         'url' => U('chuli')
                     ),
                     array(
                         'name' => '新增备注',
                         'url' => U('bei')
                     ),

                ),
                'search'=>array(
                    array('title'=>'客户姓名','name'=>'li_tp_hn_tuijian|name'),
                    array('title'=>'客户号码','name'=>'li_tp_hn_tuijian|phone'),
                    array('title'=>'订单状态','name'=>'eq_tp_hn_tuijian|status','type'=>'select','many'=>array(
                        array('value'=>'0','name'=>'新推荐'),
                        array('value'=>'1','name'=>'已处理'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_tp_hn_tuijian|add_time','type'=>'between')
                )
            ),

            M('hn_tuijian')->where($aWhere)->order('tp_hn_tuijian.add_time desc')->count(),
            M('hn_tuijian')
                   ->join(array('tp_hn_users as a on tp_hn_tuijian.uid=a.id','tp_hn_houses as b on tp_hn_tuijian.hid=b.id'))
                ->field('tp_hn_tuijian.id,a.phone as k,tp_hn_tuijian.name,tp_hn_tuijian.phone,tp_hn_tuijian.status,tp_hn_tuijian.add_time,tp_hn_tuijian.other,tp_hn_tuijian.hid')
                ->order('tp_hn_tuijian.add_time desc')->where($aWhere),
            array($this,'tuijian1')

        );
        $this->UDisplay('show1');
    }

    public function tuijian1($data){
       
        foreach($data as $k=>$v){
             $hid = explode(',',$v['hid']);
             $data[$k]['hid']='';
             for ($i=0; $i <count($hid) ; $i++) { 
                 $data[$k]['hid'].=M('hn_houses')->where(array('id' =>$hid[$i]))->getField('title').' '
                 ;

             }
             
            
            if($v['status']==0){
                $data[$k]['status']="新推荐";
            }
            if($v['status']==1){
                $data[$k]['status']="待到访";
            }
            if($v['status']==2){
                $data[$k]['status']="已到访";
            }
            if($v['status']==3){
                $data[$k]['status']="待签约";
            }
			if($v['status']==4){
                $data[$k]['status']="已签约";
            }
			if($v['status']==5){
                $data[$k]['status']="已结佣";
            }
			if($v['status']==6){
                $data[$k]['status']="无意向";
            }
            if($v['status']==-1){
                $data[$k]['status']="客户未到访";
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            
        }
        
        return $data;
    }
    public function bei()
    {
        $info=M('hn_tuijian')->field('status')->find($_GET['id']);
        $this->assign('tishi','新增备注');
            $this->Edit('hn_tuijian',array(
                array('title'=>'新增备注','type'=>'input','name'=>'bei'),

            ),U('tuijian',array('token'=>$this->_sTtoken)));
    }
    //处理推荐客户状态
    public function chuli(){
        $info=M('hn_tuijian')->field('status')->find($_GET['id']);
        if($info['status']==0){
            $this->assign('tishi','这里管理员根据线下客户具体到访签约情况来改变此订单状态');
            $this->Edit('hn_tuijian',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"待到访",'value'=>"1"),
                    //array('content'=>"未到访",'value'=>"-1"),
                    //array('content'=>"已过期",'value'=>"5"),
                    array('content'=>"无意向",'value'=>"6")
                )),

            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
        }
		if($info['status']==1){
            $this->assign('tishi','这里管理员根据线下客户具体到访签约情况来改变此订单状态');
            $this->Edit('hn_tuijian',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"已到访",'value'=>"2"),
                    //array('content'=>"未到访",'value'=>"-1"),
                    //array('content'=>"已过期",'value'=>"5"),
                    array('content'=>"无意向",'value'=>"6")
                )),

            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
        }
		if($info['status']==2){
            $this->assign('tishi','这里管理员根据线下客户具体到访签约情况来改变此订单状态');
            $this->Edit('hn_tuijian',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"待签约",'value'=>"3"),
                    //array('content'=>"未到访",'value'=>"-1"),
                    //array('content'=>"已过期",'value'=>"5"),
                    array('content'=>"无意向",'value'=>"6")
                )),

            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
        }
        if($info['status']==3){
            $this->assign('tishi','这里管理员根据线下客户具体到访签约情况来改变此订单状态');
            $this->Edit('hn_tuijian',array(
                array('title'=>'改变状态','type'=>'radio','name'=>'status','many'=>array(
                    array('content'=>"已签约",'value'=>"4"),
                    array('content'=>"无意向",'value'=>"6")
                )),

            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
        }

        if($info['status']==-1||$info['status']==5||$info['status']==6){
            $this->error2('此订单已处理完成');
        }
		if($info['status']==4){
            $this->assign('tishi','经纪人所得佣金');
            $this->Edit('hn_tuijian',array(
                array('title'=>'确认签约佣金','type'=>'input','name'=>'money'),
				array('title'=>'确认签约佣金','type'=>'hidden_true','name'=>'status','value'=>5),
            ),U('tuijian',array('token'=>$this->_sTtoken)),array($this,'chuli1'));
		}
    }
   //客户到访送佣金
    public function chuli1($data){
		if($data['money']){
			$info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',$data['money']);
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>$data['money'],
                'add_time'=>time(),
                'content'=>'已结佣',
                'type'=>2,
                'name'=>$info['name'],
                'type_id'=>$info['id']
            ));
			//$data['status']=4;
		}
        if($data['status']==2){
            $info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
            //$yonjing=M('hn_houses')->where(array('id'=>$info['hid']))->getField('yonjing1');
			//p($yonjing);die;
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',100);
			
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>100,
                'add_time'=>time(),
                'content'=>'客户到访',
                'type'=>1,
                'name'=>$info['name'],
                'type_id'=>$info['id'],
                'update_time'=>time()
            ));
        }/*
		if($data['status']==1){
            $info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
            //$yonjing=M('hn_houses')->where(array('id'=>$info['hid']))->getField('yonjing1');
			//p($yonjing);die;
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',$yonjing);
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>$yonjing,
                'add_time'=>time(),
                'content'=>'客户到访',
                'type'=>1,
                'name'=>$info['name'],
                'type_id'=>$info['id'],
                'update_time'=>time()
            ));
        }
		if($data['status']==3){
            $info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
            $yonjing=M('hn_houses')->where(array('id'=>$info['hid']))->getField('yonjing2');
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',$yonjing);
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>$yonjing,
                'add_time'=>time(),
                'content'=>'签约成功',
                'type'=>2,
                'name'=>$info['name'],
                'type_id'=>$info['id']
            ));
        }*/
        return $data;
    }
	//签约佣金
	/*public function yon(){
            $info=M('hn_tuijian')->field('id,name,hid,uid')->where(array('id'=>$_GET['id']))->find();
			$yon = $this->_post('yon');
            $yonjing=M('hn_houses')->where(array('id'=>$info['hid']))->getField('yonjing2');
            M('hn_users')->where(array('id'=>$info['uid']))->setInc('yonjing',$yonjing*$yon);
            M('hn_yonjing_jl')->add(array(
                'token'=>$this->token,
                'uid'=>$info['uid'],
                'status'=>1,
                'yonjing'=>$yonjing*$yon,
                'add_time'=>time(),
                'content'=>'签约成功',
                'type'=>2,
                'name'=>$info['name'],
                'type_id'=>$info['id']
            ));
		
	}*/
	
    //备注
    public function other(){
        $info=M('hn_tuijian')->field('other,money')->find($_GET['id']);
        $this->assign('info',$info);
        $this->display('./tpl/User/default/hn/other.html');
    }



    /**
     *  楼盘列表
     **/
    public function houses(){
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('houses', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                          'name' => '添加楼盘',
                          'url' => U('add_houses',array('token'=>$this->_sTtoken))
                      ),


                ),
                'tips' => array(
                    '楼盘列表'
                ),
                'Table_Header' => array(
                    'ID', '楼盘名称','楼盘图片','看楼佣金', '签约佣金','排序','操作'
                ),
                'List_Opt' => array(
                     array(
                         'name' => '修改',
                         'url' => U('edit_houses')
                     ),
                    array(
                        'name' => '删除',
                        'url' => U('del_houses')
                    ),

                ),
                'search'=>array(
                    array('title'=>'楼盘名称','name'=>'li_title'),
                )
            ),

            M('hn_houses')->where($aWhere)->order('sort desc')->count(),
            M('hn_houses')->field('id,title,logopic,yonjing1,yonjing2,sort')->order('sort desc')->where($aWhere),
            array($this,'houses1')

        );
        $this->UDisplay('show1');
    }
    public function houses1($data){
        foreach($data as $k=>$v){
           $data[$k]['logopic']="<img src='".$v['logopic']."' width=70px />";
        }
        return $data;
    }
    /**
     * 添加楼盘
     */
    public function add_houses(){
        
        $this->add('hn_houses',array(
            array('title'=>"楼盘名称",'type'=>"input",'name'=>"title",'msg'=>'楼盘名称不能为空'),
            array('title'=>"一句话点评",'type'=>"input",'name'=>"jianjie",'msg'=>'简介不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"项目主图",'name'=>"logopic"),
                array('title'=>"详情页背景",'name'=>"back_pic")

            )),
            array('title'=>"简介",'type'=>"textarea_1",'name'=>"content"),
            array('title'=>"区位",'type'=>"textarea",'name'=>"info"),
            array('title'=>"相册",'type'=>"textarea_2",'name'=>"info2"),
			array('title'=>"微楼书",'type'=>"input",'name'=>"info3",'placeholder'=>'请加上http://'),
            array('title'=>"经纬度",'type'=>"map1",'name'=>"lng"),
			array('title'=>"备注",'type'=>"input",'name'=>"other"),
            array('title'=>"楼盘看房佣金(元)",'type'=>"input",'name'=>"yonjing1","placeholder"=>"100"),
            array('title'=>"楼盘签约佣金(元)",'type'=>"input",'name'=>"yonjing2",'width'=>'500px','placeholder'=>'比如输入0.01，经纪人所得佣金就是，签约金*0.01'),
            array('title'=>"排序",'type'=>"input",'name'=>"sort",'placeholder'=>'数值越大排在越前'),

        ),U('houses',array('token'=>$this->_sTtoken)));
    }

    public function edit_houses(){
        $this->Edit('hn_houses',array(
            array('title'=>"楼盘名称",'type'=>"input",'name'=>"title",'msg'=>'楼盘名称不能为空'),
			array('title'=>"一句话点评",'type'=>"input",'name'=>"jianjie",'msg'=>'简介不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"项目主图",'name'=>"logopic"),
                array('title'=>"详情页背景",'name'=>"back_pic")
            )),
            array('title'=>"简介",'type'=>"textarea_1",'name'=>"content"),
            array('title'=>"区位",'type'=>"textarea",'name'=>"info"),
            array('title'=>"相册",'type'=>"textarea_2",'name'=>"info2"),
            array('title'=>"微楼书",'type'=>"input",'name'=>"info3",'placeholder'=>'请加上http://'),
            array('title'=>"经纬度",'type'=>"map1",'lng'=>"lng",'lat'=>'lat'),
			array('title'=>"备注",'type'=>"input",'name'=>"other"),
            array('title'=>"楼盘看房佣金(元)",'type'=>"input",'name'=>"yonjing1",'placeholder'=>'100'),
            array('title'=>"楼盘签约佣金(元)",'type'=>"input",'name'=>"yonjing2",'width'=>'500px','placeholder'=>'比如输入0.01，经纪人所得佣金就是，签约金*0.01'),
            array('title'=>"排序",'type'=>"input",'name'=>"sort",'placeholder'=>'数值越大排在越前'),

        ),U('houses',array('token'=>$this->_sTtoken)));
    }
    public function del_houses(){
        $this->del('hn_houses');
    }
	//联系我们
	// public function contact(){
 //        $this->Edit('hn_set',array(
 //            array('title'=>"联系我们",'type'=>"textarea",'name'=>"content"),
 //            array('title'=>"注册协议",'type'=>"textarea_1",'name'=>"xie"),
 //        ),U('index',array('token'=>$this->_sTtoken)));
 //    }
    //佣金记录
    public function yonjing(){
        $aWhere['token'] =$this->_sToken;
        $aWhere['uid']=$_GET['id'];
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            $aWhere['uid']=$_GET['id'];
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                      array(
                          'name' => '返回经纪人列表',
                          'url' => U('index',array('token'=>$this->_sTtoken))
                      ),
					  array(
                          'name' => '导出佣金统计表',
                          'url' => U('excelyon',array('token'=>$this->_sToken))
                      ),
					  
                ),
                'tips' => array(
                    '【'.M('hn_users')->where(array('id'=>$_GET['id']))->getField('name').'】佣金记录列表',
					
					
                ),
                'Table_Header' => array(
                    'ID','佣金','来源','名字', '时间',''
                ),
				'List_Opt' => array(
                    /* array(
                         'name' => '审核',
                         'url' => U('yon_shen')
                     ),*/

                ),
                'search'=>array(

                    array('title'=>'客户姓名','name'=>'li_name'),

                  //  array('type'=>'br'),
                    array('title'=>'时间','name'=>'be_add_time','type'=>'between')
                )
            ),

            M('hn_yonjing_jl')->where($aWhere)->order('add_time desc')->count(),
            M('hn_yonjing_jl')->field('id,yonjing,content,name,add_time,uid')->order('add_time desc')->where($aWhere),
            array($this,'yonjing3')

        );
        $this->UDisplay('show1');
    }
    public function yonjing3($data){
		
        foreach($data as $k=>$v){

            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
		}
		return $data;
    }
	//佣金统计
	public function excelyon()
	{
		$data = M('Hn_users')->join("join tp_hn_yonjing_jl on tp_hn_yonjing_jl.uid=tp_hn_users.id")->field('tp_hn_yonjing_jl.id,tp_hn_yonjing_jl.yonjing,tp_hn_yonjing_jl.content,tp_hn_yonjing_jl.name,tp_hn_users.phone,tp_hn_yonjing_jl.add_time')->where(array('tp_hn_yonjing_jl.token'=>$this->_sToken))->select();
	    foreach($data as $k=>$v){
			/*$aKufu = M('Hn_tuijian')->where(array('token'=>$this->_sToken,'uid'=>$v['id']))->select();
			$iKufu = count($aKufu);
			$data[$k]['kcount'] = $iKufu;
			$c_phone = M('hn_users')->where(array('token'=>$this->_sToken,'from_phone'=>$v['phone']))->select();
			$c_phone = count($c_phone);
			$data[$k]['c_phone'] = $c_phone;*/
			$data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
		}
		Excel::arr2ExcelDownload($data,array('编号','佣金','来源','姓名','手机号码','时间'),'佣金统计信息');
	}
	
    public function yon_shen(){
        $info=M('hn_yonjing_jl')->field('status')->find($_GET['id']);
        if($info['status']!=0){
            $this->error2('已审核过了');
        }
        $this->Edit('hn_yonjing_jl',array(
            array('title'=>'审核','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"结算",'value'=>"1"),
                array('content'=>"不结算",'value'=>"0")
            )),
        ),U('index',array('token'=>$this->_sTtoken)));
    }
    public function yonjing1($data){
        foreach($data as $k=>$v){
            if($v['status']==1){
                 $data[$k]['status']='新申请';
            }
            if($v['status']==2){
                $data[$k]['status']='审核通过';
            }
			if($v['status']==3)
			{
				$data[$k]['status']='审核未通过';
			}
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            $data[$k]['update_time']=date('Y-m-d H:i:s',$v['update_time']);
        }
        return $data;
    }

	public function yonjing2($data){
        foreach($data as $k=>$v){
            if($v['status']==0){
                 $data[$k]['status']='未结算';
            }
            if($v['status']==1){
                $data[$k]['status']='已结算';
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
    //设置
    public function set(){
        $this->Edit('hn_set',array(
            array('title'=>"推荐经纪人所得佣金",'type'=>"input",'name'=>"yonjing1"),
            array('title'=>"排行榜标题语",'type'=>"textarea1",'name'=>"content1"),
            array('type'=>'img','many'=>array(
                array('title'=>"主页生活家主图",'name'=>"info4"),
				array('title'=>"看房生活家主图",'name'=>"lookinfo")
            )),
            array('title'=>"经纪人生活家详情",'type'=>"textarea_2",'name'=>"info5"),
            array('title'=>"客户生活家详情",'type'=>"textarea_3",'name'=>"jin"),
            array('title'=>"活动规则",'type'=>"textarea",'name'=>"info1"),
            array('title'=>"分享经纪人标题",'type'=>"input",'name'=>"title"),
            array('title'=>"分享经纪人内容",'type'=>"input",'name'=>"share"),
            array('title'=>"照片墙详情",'type'=>"textarea_3",'name'=>"photo"),
        ),U('houses',array('token'=>$this->_sToken)));
    }
    //我的经纪人记录
    public function jjr(){
        $aWhere['token'] =$this->_sToken;
		$aWhere['from_phone']=M('hn_users')->where(array('id'=>$_GET['id']))->getField('phone');

        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            //$aWhere['uid']=$_GET['id'];
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '返回经纪人列表',
                        'url' => U('index',array('token'=>$this->_sTtoken))
                    ),


                ),
                'tips' => array(
                    '【'.M('hn_users')->where(array('id'=>$_GET['id']))->getField('name').'】推荐经纪人记录列表'
                ),
                'Table_Header' => array(
                    'ID','姓名','电话号码','状态', '时间',''
                ),
                'List_Opt' => array(

                ),
                'search'=>array(
                          
                          array('title'=>'手机号码','name'=>'li_phone'),
                          
                    )
            ),

            M('hn_users')->where($aWhere)->count(),
			
            M('hn_users')->field('id,name,phone,status,add_time')->where($aWhere),
            //array($this,'yonjing2'),
            array($this,'abc1')

        );
        $this->UDisplay('show1');
    }
	//推荐客户记录
    public function kefu(){
        $aWhere['tp_hn_tuijian.token'] =$this->_sToken;
		$aWhere['tp_hn_tuijian.uid']=$_GET['id'];
        if(IS_POST){
          //  p($_POST);
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                $b=str_replace('|','.',$k);
                $aWhere[$b]=$v;
                unset($aWhere[$k]);
            }
            $aWhere['tp_hn_tuijian.token'] = $this->_sToken;
			$aWhere['tp_hn_tuijian.uid']=$_GET['id'];
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'Head_Opt' => array(
                     array(
                          'name' => '返回经纪人列表',
                          'url' => U('index',array('token'=>$this->_sTtoken))
                      ),
                ),
                'tips' => array(
                    '【'.M('hn_users')->where(array('id'=>$_GET['id']))->getField('name').'】客户记录列表'
                ),
                'Table_Header' => array(
                    'ID','客户姓名','客户号码','状态','推荐时间','更新时间','推荐楼盘',''
                ),
                
                'search'=>array(
                    array('title'=>'客户姓名','name'=>'li_tp_hn_tuijian|name'),
                    array('title'=>'客户号码','name'=>'li_tp_hn_tuijian|phone'),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_tp_hn_tuijian|add_time','type'=>'between')
                )
            ),

            M('hn_tuijian')->where($aWhere)->order('tp_hn_tuijian.add_time desc')->count(),
            M('hn_tuijian')
                   ->join(array('tp_hn_users as a on tp_hn_tuijian.uid=a.id','tp_hn_houses as b on tp_hn_tuijian.hid=b.id'))
                ->field('tp_hn_tuijian.id,tp_hn_tuijian.name,tp_hn_tuijian.phone,tp_hn_tuijian.status,tp_hn_tuijian.add_time,tp_hn_tuijian.update_time,tp_hn_tuijian.hid')
                ->order('tp_hn_tuijian.add_time desc')->where($aWhere),
            array($this,'tuijian2')

        );
        $this->UDisplay('show1');
    }
	    public function tuijian2($data){

        foreach($data as $k=>$v){
            $hid = explode(',',$v['hid']);
             $data[$k]['hid']='';
             for ($i=0; $i <count($hid) ; $i++) { 
                 $data[$k]['hid'].=M('hn_houses')->where(array('id' =>$hid[$i]))->getField('title').' '
                 ;

             }
            if($v['status']==0){
                $data[$k]['status']="新推荐";
            }
            if($v['status']==1){
                $data[$k]['status']="客户到访";
            }
            if($v['status']==2){
                $data[$k]['status']="客户未签约";
            }
            if($v['status']==3){
                $data[$k]['status']="客户成功签约";
            }
			if($v['status']==4){
                $data[$k]['status']="已结佣";
            }
			if($v['status']==5){
                $data[$k]['status']="已过期";
            }
			if($v['status']==6){
                $data[$k]['status']="无意向";
            }
            if($v['status']==-1){
                $data[$k]['status']="客户未到访";
            }
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
			if($v['status']!=0){
				$data[$k]['update_time']=date('Y-m-d H:i:s',$v['update_time']);
			}
			}
        return $data;
    }
	//反馈信息
	public function feedback(){
		$aWhere['tp_hn_feedback.token'] = $this->_sToken;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['tp_hn_feedback.token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('feedback', array('token' => $this->_sToken)),

                'tips' => array(
                    '反馈信息列表'
                ),
                'Table_Header' => array(
                    'ID', '用户名','电话号码','反馈信息', '时间',''
                ),
				

            ),

            M('hn_feedback')->where($aWhere)->order('tp_hn_feedback.add_time desc')->count(),
            M('hn_feedback')
                ->join(array('tp_hn_users as a on tp_hn_feedback.uid=a.id'))
                ->field('tp_hn_feedback.id,a.name as j,a.phone as k,tp_hn_feedback.content,tp_hn_feedback.add_time')
                ->order('tp_hn_feedback.add_time desc')->where($aWhere),
			array($this,'feed_time')
        );
        $this->UDisplay('show1');
	}
	public function feed_time($data){
        foreach($data as $k=>$v){
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
	//提现管理
	public function balance()
	{
		$aWhere['tp_hn_bank.token'] = $this->_sToken;

        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['tp_hn_bank.token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                //'abc' => 123,
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('balance', array('token' => $this->_sToken)),

                'tips' => array(
                    '提现信息列表'
                ),
                'Table_Header' => array(
                    '序号','姓名','电话号码','提现金额','银行卡号','状态', '申请时间','审核时间','操作'
                ),
				'List_Opt' => array(
                        array(
                            'name' => '审核',
                            'url' => U('audio')
                        ),
                ),

            ),

            M('hn_bank')->where($aWhere)->order('tp_hn_bank.addtime desc')->count(),
            M('hn_bank')
                ->join(array('tp_hn_users as a on tp_hn_bank.uid=a.id'))
                ->field('tp_hn_bank.id,a.name as j,a.phone as k,tp_hn_bank.money,tp_hn_bank.brand,tp_hn_bank.status,tp_hn_bank.addtime,tp_hn_bank.updatetime')
                ->order('tp_hn_bank.addtime desc')->where($aWhere),
			array($this,'mon')
        );
        $this->UDisplay('show1');
	}
	public function mon($data)
	{
		foreach($data as $k=>$v)
		{
			if($v['status']==0){
                $data[$k]['status']="新申请";
            }
            if($v['status']==1){
                $data[$k]['status']="打款成功";
            }
            if($v['status']==-1){
                $data[$k]['status']="不通过";
            }
            $data[$k]['addtime']=date('Y-m-d H:i:s',$v['addtime']);
			if($v['status']==1 || $v['status'] == -1){
				$data[$k]['updatetime']=date('Y-m-d H:i:s',$v['updatetime']);
			}
		}
		return $data;
	}
	//提现审核
	public function audio()
	{
        $info=M('hn_bank')->field('status,money,uid')->find($_GET['id']);
        if($info['status']!=0){
           $this->error2('已审核过了');
        }
		$v = M('hn_users')->field('yonjing')->where(array('token'=>$this->_sToken))->find();
        $this->Edit('hn_bank',array(
            array('title'=>'审核','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"打款成功",'value'=>"1"),
                array('content'=>"不通过",'value'=>"-1")
            )),
			array('title'=>'确认签约佣金','type'=>'hidden_true','name'=>'updatetime','value'=>time()),
        ),
		U('balance',array('token'=>$this->_sTtoken)),array($this,'audio1'));
    }
	public function audio1($data){
		$info=M('hn_bank')->field('status,money,uid')->find($_GET['id']);
		if($data['status']==1){
			M('Hn_users')->where(array('id'=>$info['uid']))->setDec('yonjing',$info['money']);
		}
		return $data;
	}
    //排行榜操作
    public function renwei(){
        $aWhere='';
        $order='yonjing desc';
        $aWhere['token'] =$this->_sToken;
        $aWhere['renwei'] =2;
        if(IS_POST){
            $order=$_POST['paixu_form'];
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                if(strstr($k,'|')){
                    $b=str_replace('|','.',$k);
                    $aWhere[$b]=$v;
                    unset($aWhere[$k]);
                }
            }
            $aWhere['token'] =$this->_sToken;
            $aWhere['renwei'] =2;

            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

        $this->table(
            array(
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('renwei', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                   array(
                        'name' => '添加',
                        'url' => U('add_renwei',array('token'=>$this->token))
                    ),
                    /*
               array(
                   'name' => '导出',
                   'type'=>'daochu',//type=daochu时，可以带上搜索条件
                   'url' => U('excel',array('token'=>$this->token))
               ),
               array(
                   'name' => '一键',
                   'type'=>'yijin',//开启多选用这个type=yijin
                   'url' => U('yijin')
               ),*/



                ),
                'tips' => array(
                    '排行榜前10名操作'
                ),
                'Table_Header' => array(
                    'ID', '姓名','佣金','排名','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '修改',
                        'url' => U('edit_renwei')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_renwei')
                    ),


                ),
               /* 'search'=>array(
                    array('title'=>'标题','name'=>'li_title'),
                    array('title'=>'SELECT框','type'=>'select','name'=>'eq_select','many'=>array(
                        array('name'=>'是','value'=>'1'),
                        array('name'=>'否','value'=>'0'),
                        array('name'=>'其他','value'=>'3'),
                    )),
                    array('type'=>'br'),
                    array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                )*/
            ),
            M('hn_users')->where($aWhere)->order($order)->count(),
            M('hn_users')->field('id,name,yonjing,add_time')->order($order)->where($aWhere),
            array($this,'renwei1')

        );
        $this->UDisplay('show1');
    }
    public function renwei1($data){
        foreach($data as $k=>$v){
            $data[$k]['add_time']=$k+1;
        }
        return $data;
    }

    //添加人为数据
    public function add_renwei(){
        $this->add('hn_users',array(
            array('title'=>'姓名','type'=>'input','name'=>'name'),
            array('title'=>'佣金','type'=>'input','name'=>'yonjing'),
            array('title'=>'','type'=>'hidden_true','name'=>'renwei','value'=>2),

        ),U('renwei',array('token'=>$this->_sTtoken)));

    }
    public function edit_renwei(){
        $this->Edit('hn_users',array(
            array('title'=>'姓名','type'=>'input','name'=>'name'),
            array('title'=>'佣金','type'=>'input','name'=>'yonjing'),
            array('title'=>'','type'=>'hidden_true','name'=>'renwei','value'=>2),

        ),U('renwei',array('token'=>$this->_sTtoken)));

    }
    public function del_renwei(){
        $this->del('hn_users');
    }
    //佣金商城
    public function shop(){
        $aWhere='';
        $order='sort desc';
        $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            $order=$_POST['paixu_form'];
            foreach($_REQUEST as $k=>$v){
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                if(strstr($k,'|')){
                    $b=str_replace('|','.',$k);
                    $aWhere[$b]=$v;
                    unset($aWhere[$k]);
                }
            }
            $aWhere['token'] =$this->_sToken;

            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

        $this->table(
            array(
                //  'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('shop', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加',
                        'url' => U('add_shop',array('token'=>$this->token))
                    ),
                    /*
               array(
                   'name' => '导出',
                   'type'=>'daochu',//type=daochu时，可以带上搜索条件
                   'url' => U('excel',array('token'=>$this->token))
               ),
               array(
                   'name' => '一键',
                   'type'=>'yijin',//开启多选用这个type=yijin
                   'url' => U('yijin')
               ),*/



                ),
                'tips' => array(
                    '商城'
                ),
                'Table_Header' => array(
                    'ID', '名称','是否显示','佣金','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '修改',
                        'url' => U('edit_shopping')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('del_shopping')
                    ),


                ),
                /* 'search'=>array(
                     array('title'=>'标题','name'=>'li_title'),
                     array('title'=>'SELECT框','type'=>'select','name'=>'eq_select','many'=>array(
                         array('name'=>'是','value'=>'1'),
                         array('name'=>'否','value'=>'0'),
                         array('name'=>'其他','value'=>'3'),
                     )),
                     array('type'=>'br'),
                     array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                 )*/
            ),
            M('hn_shop')->where($aWhere)->order($order)->count(),
            M('hn_shop')->field('id,name,status,price')->order($order)->where($aWhere),
            array($this,'shop1')

        );
        $this->UDisplay('show1');
    }
    public function shop1($data){
		foreach($data as $k=>$v)
		{
			if($v['status']==1)
			{
				$data[$k]['status']='不显示';
			}
			else{
				$data[$k]['status']='显示';
			}
		}
        return $data;
    }
    public function add_shop(){
        $this->add('hn_shop',array(
            array('title'=>'商品名称','type'=>'input','name'=>'name'),
            array('title'=>'佣金','type'=>'img','name'=>'pic','many'=>array(
                array('title'=>'商品图片','name'=>'pic')
            )),
            array('title'=>'佣金','type'=>'input','name'=>'price'),
            array('title'=>'排序','name'=>'sort','type'=>'input','placeholder'=>'数值越大排在越前'),
            array('title'=>'链接','name'=>'link','type'=>'input','placeholder'=>'商品跳转地址，必须加上http://'),
            array('title'=>'一句话点评','name'=>'sai','type'=>'input'),
			array('title'=>'是否显示','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"显示",'value'=>"0"),
                array('content'=>"不显示",'value'=>"1")
            )),

        ),U('shop',array('token'=>$this->_sTtoken)));

    }
	public function edit_shopping(){
        $this->Edit('hn_shop',array(
           array('title'=>'商品名称','type'=>'input','name'=>'name'),
            array('title'=>'佣金','type'=>'img','name'=>'pic','many'=>array(
                array('title'=>'商品图片','name'=>'pic')
            )),
            array('title'=>'佣金','type'=>'input','name'=>'price'),
            array('title'=>'排序','name'=>'sort','type'=>'input'),
			array('title'=>'链接','name'=>'link','type'=>'input','placeholder'=>'商品跳转地址，必须加上http://'),
            array('title'=>'一句话点评','name'=>'sai','type'=>'input'),
			array('title'=>'是否显示','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"显示",'value'=>"0"),
                array('content'=>"不显示",'value'=>"1")
            )),
        ),U('shop',array('token'=>$this->_sTtoken)));

    }
	public function del_shopping(){
        $this->del('hn_shop');
    }
	//消费管理
	public function consume()
	{
		 $aWhere['tp_hn_xiaofeijl.token'] =$this->_sToken;
		 session('where_p',$aWhere);
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['tp_hn_xiaofeijl.token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                'HeadHover' => U('consume', array('token' => $this->_sToken)),
                'Head_Opt' => array(

                ),
                'tips' => array(
                    '消费记录列表'
                ),
                'Table_Header' => array(
                    '编号','商品名称', '购买人姓名','购买人手机号码','收货地址', '消费金额','备注','时间','状态','操作'
                ),
                'List_Opt' => array(
                     array(
                         'name' => '处理订单',
                         'url' => U('shop_status_update')
                     ),
                    array(
                        'name' => '发货地址',
                        'url' => U('address')
                    ),

                ),
              /*  'search'=>array(
                    array('title'=>'手机号码','name'=>'li_phone'),
                )*/
            ),

            M('hn_xiaofeijl')->join("join tp_hn_users on tp_hn_xiaofeijl.uid=tp_hn_users.id")->where($aWhere)->order('tp_hn_xiaofeijl.addtime desc')->count(),
            M('hn_xiaofeijl')
                ->join("join tp_hn_users on tp_hn_xiaofeijl.uid=tp_hn_users.id")
                ->field('tp_hn_xiaofeijl.id,tp_hn_xiaofeijl.s_name,tp_hn_users.name,
                tp_hn_users.phone,tp_hn_users.address,tp_hn_xiaofeijl.yonjing,tp_hn_xiaofeijl.other,tp_hn_xiaofeijl.addtime,tp_hn_xiaofeijl.status')
                ->order('tp_hn_xiaofeijl.addtime desc')
                ->where($aWhere),
            array($this,'shop_status')

        );

        $this->UDisplay('show1');
	}
	
	public function shop_status_update(){
        $info=M('hn_xiaofeijl')->field('status')->find($_GET['id']);
        if($info['status']==2){
            $this->error2('已经处理了');
        }
        $this->Edit('hn_xiaofeijl',array(
            array('title'=>'处理','type'=>'radio','name'=>'status','many'=>array(
                array('content'=>"发货",'value'=>"1"),
                array('content'=>"已收货",'value'=>"2")
            )),
        ),U('consume',array('token'=>$this->token)));
    }
    //地址
    public function address(){
        $info=M('hn_xiaofeijl')->field('shop_name,phone,address')->find($_GET['id']);
        $this->assign('info',$info);
        $this->display('./tpl/User/default/hn/address.html');
    }
	public function charts()
	{
		 $aWhere['token'] =$this->_sToken;
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                'HeadHover' => U('charts', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                     array(
                          'name' => '导出Excel表',
                          'url' => U('exceldc',array('token'=>$this->_sToken))
                      ),


					),
                'tips' => array(
                    '佣金排行榜记录列表'
                ),
                'Table_Header' => array(
                    '编号','姓名','佣金','级别','名次',''
                ),
            ),

            M('hn_users')->field('id,name,yonjing')->where($aWhere)->order('yonjing desc')->count(),
            M('hn_users')
                ->field('id,name,yonjing')
                ->order('yonjing desc')
                ->where($aWhere),
				array($this,'listc')
           
        );

        $this->UDisplay('show1');
	}
	public function listc($data)
	{  
		foreach($data as $k=>$v)
		{	
			if(0<=$v['yonjing']&&$v['yonjing']<200)
				{
					 $data[$k]['grade']='菜鸟顾问';
				}
				else if(200<=$v['yonjing']&&$v['yonjing']<400)
				{
					$data[$k]['grade']='高级顾问';
				}
				else if(400<=$v['yonjing']&&$v['yonjing']<800)
				{
					$data[$k]['grade']='销售经理';
				}
				else if(800<=$v['yonjing']&&$v['yonjing']<1600)
				{
					$data[$k]['grade']='销售总监';
				}
				else if(1600<=$v['yonjing']&&$v['yonjing']<3200)
				{
					$data[$k]['grade']='区域总监';
				}
				else if(3200<=$v['yonjing']&&$v['yonjing']<6400)
				{
					$data[$k]['grade']='区域副总';
				}
				else if(6400<=$v['yonjing']&&$v['yonjing']<12800)
				{
					 $data[$k]['grade']='区域老总';
				}
				else if(12800<=$v['yonjing']&&$v['yonjing']<25600)
				{
					$data[$k]['grade']='大区副总';
				}
				else if(25600<=$v['yonjing']&&$v['yonjing']<51200)
				{
					$data[$k]['grade']='大区老总';
				}
				else if(51200<=$v['yonjing']&&$v['yonjing']<102400)
				{
					$data[$k]['grade']='集团副总';
				}
				else if(102400<=$v['yonjing']&&$v['yonjing']<204800)
				{
					 $data[$k]['grade']='集团总裁';
				}
				else if(204800<=$v['yonjing']&&$v['yonjing']<409600)
				{
					 $data[$k]['grade']='执行董事';
				}
				else if(409600<=$v['yonjing']&&$v['yonjing']<819200)
				{
					 $data[$k]['grade']='董事长';
				}
				else if(819200<=$v['yonjing']&&$v['yonjing']<1638400)
				{
					$data[$k]['grade']='马上离职';
				}
				else if($v['yonjing']>=1638400)
				{
					$data[$k]['grade']='加入我们';
				}
				$data[$k]['more'] = $k+1;
		}
		return $data;
	}
	public function exceldc()
	{
		$data = M('Hn_users')->field('id,name,yonjing')->where(array('token'=>$this->_sToken))->order('yonjing desc')->select();
		foreach($data as $k=>$v)
		{
			if(0<=$v['yonjing']&&$v['yonjing']<200)
                {
                     $data[$k]['grade']='菜鸟顾问';
                }
                else if(200<=$v['yonjing']&&$v['yonjing']<400)
                {
                    $data[$k]['grade']='高级顾问';
                }
                else if(400<=$v['yonjing']&&$v['yonjing']<800)
                {
                    $data[$k]['grade']='销售经理';
                }
                else if(800<=$v['yonjing']&&$v['yonjing']<1600)
                {
                    $data[$k]['grade']='销售总监';
                }
                else if(1600<=$v['yonjing']&&$v['yonjing']<3200)
                {
                    $data[$k]['grade']='区域总监';
                }
                else if(3200<=$v['yonjing']&&$v['yonjing']<6400)
                {
                    $data[$k]['grade']='区域副总';
                }
                else if(6400<=$v['yonjing']&&$v['yonjing']<12800)
                {
                     $data[$k]['grade']='区域老总';
                }
                else if(12800<=$v['yonjing']&&$v['yonjing']<25600)
                {
                    $data[$k]['grade']='大区副总';
                }
                else if(25600<=$v['yonjing']&&$v['yonjing']<51200)
                {
                    $data[$k]['grade']='大区老总';
                }
                else if(51200<=$v['yonjing']&&$v['yonjing']<102400)
                {
                    $data[$k]['grade']='集团副总';
                }
                else if(102400<=$v['yonjing']&&$v['yonjing']<204800)
                {
                     $data[$k]['grade']='集团总裁';
                }
                else if(204800<=$v['yonjing']&&$v['yonjing']<409600)
                {
                     $data[$k]['grade']='执行董事';
                }
                else if(409600<=$v['yonjing']&&$v['yonjing']<819200)
                {
                     $data[$k]['grade']='董事长';
                }
                else if(819200<=$v['yonjing']&&$v['yonjing']<1638400)
                {
                    $data[$k]['grade']='马上离职';
                }
                else if($v['yonjing']>=1638400)
                {
                    $data[$k]['grade']='加入我们';
                }
				$data[$k]['more'] = $k+1;
		}
        Excel::arr2ExcelDownload($data,array('编号', '姓名','佣金','级别','名次'),'排行榜信息');
	}
	public function photo()
	{
		 $aWhere['token'] =$this->_sToken;
         
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                'HeadHover' => U('photo', array('token' => $this->_sToken)),
                'tips' => array(
                    '照片墙'
                ),
                'Table_Header' => array(
                    '编号','评论内容','评论时间', ''
                ),
				'List_Opt' => array(
                        array(
                            'name' => '删除',
                            'url' => U('del_photo')
                        ),
                ),
            ),

            M('wx_photo_comment')->field('id,content,add_time')->where($aWhere)->order('add_time desc')->count(),
            M('wx_photo_comment')
                ->field('id,content,add_time')
                ->order('add_time desc')
                ->where($aWhere),
           array($this,'tim')
        );

        $this->UDisplay('show1');
	}
    public function tim($data)
    {
        foreach($data as $k=>$v)
        {
            $data[$k]['add_time']=date('Y-m-d H:i:s',$v['add_time']);
        }
        return $data;
    }
	public function del_photo()
	{
        $this->del('wx_photo_comment');
	}
	public function c_photo()
	{

		 $aWhere['token'] =$this->_sToken;
         session('where_p',$aWhere);
        if(IS_POST){
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);

            $aWhere['token'] = $this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }
        $this->table(
            array(
                'HeadHover' => U('c_photo', array('token' => $this->_sToken)),
                'tips' => array(
                    '照片'
                ),
                'Table_Header' => array(
                    '编号','图片','添加时间', ''
                ),
				'List_Opt' => array(
                        array(
                            'name' => '删除',
                            'url' => U('delc_photo')
                        ),
                ),
            ),

            M('wx_photo')->field('id,local_pic,add_time')->where($aWhere)->order('add_time desc')->count(),
            M('wx_photo')
                ->field('id,local_pic,add_time')
                ->order('add_time desc')
                ->where($aWhere),
				array($this,'img')
           
        );

        $this->UDisplay('show1');
	}
	 public function img($data){
        foreach($data as $k=>$v){
           $data[$k]['local_pic']="<img src='".$v['local_pic']."' width=70px />";
        }
        return $data;
    }
	public function delc_photo()
	{
        $this->del('wx_photo');
	}
	
}
?>
