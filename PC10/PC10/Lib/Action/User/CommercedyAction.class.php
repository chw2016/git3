<?php
/**
 * @Author: zhang
 * @Date:   2015-03-23 18:00:22
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-04-24 18:40:00
 */
class CommercedyAction extends UserAction{
	public $token;
	public $openid;
	public $wxuser;
	public $wxusers;

	public function _initialize(){
		parent::_initialize();
		session('token',$_GET['token']);
		$this->token = session('token');
		$this->wxuser = M('wxuser');
		$this->wxusers = M('wxusers');
		$this->assign('token',$this->token);
        header("Content-type:text/html;charset=utf-8;");
	}

	// 展示帖子的排行
	public function postShow(){
		$commentModel = M('Dy_community');
		$where['token'] = $this->token;
        $where['type']=0;//贴子为0
		$data = $this->Pages($commentModel,$where,12,'add_time');
		$this->assign('info',$data['info']);
		$this->assign('page',$data['show']);
		$this->display();
	}

	//分类功能
    protected function Pages($table,$condition = '',$page = 10,$field = 'time'){
        // condition 例如：$condition = array('token'=>$token,'pid'=>1);
        $count = $table->where($condition)->count();
        $Page = new Page($count,$page);
        $lists = $table->order($field.' desc')->where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();
        $data = array(
                'info' => $lists,
                'show' => $Page->show()
            );
        return $data;
     }
     
     //查阅界面
     public function lookup(){
     	$id = $this->_get('id','intval');
     	$info = M('Dy_community')->where(array('id'=>$id))->find();
     	$this->assign('info',$info);
     	$this->display();
     }

     // 删除帖子
     public function dels(){
     	$where['id'] = $this->_get('id','intvel');
    	$where['token'] = $this->token;
    	$condition['uid'] = $this->_get('id','intvel');
    	$condition['token'] = $this->token;
     	if(M('Dy_community')->where($where)->delete() && M('Dy_users')->where($condition)->delete()){
     		$this->success('删除成功！',U('Commercedy/postShow',array('token'=>$this->token)));
		}else{
			$this->error('删除失败！');
		}
     }

     // 人气排行
     public function popular(){
     	$popularModel = M('Dy_popluar');
     	$where['token'] = $this->token;
     	$data = $this->Pages($popularModel,$where,10,'total');
     	$this->assign('info',$data['info']);
     	$this->assign('page',$data['show']);
     	$this->display();
     }

     // 星级排行
     public function stars(){
     	$shopUserModel = M('Shop_users');
     	$popularModel = M('Dy_popluar');
     	$where['token'] = $this->token;
     	$data = $this->Pages($popularModel,$where,12,'total');
     	// $Popular = $popularModel->where(array('token'=>$this->token))->select();
     	$Popular = $data['info'];
     	foreach ($Popular as $key => $value) {
     		if ($shopUserModel->where(array('openid'=>$value['openid'],'token'=>$this->token))->find()) {
     			$score = $shopUserModel->where(array('openid'=>$value['openid'],'token'=>$this->token))->getField('score');
     			$Popular[$key]['score'] = $score;
     		}else{
     			$Popular[$key]['score'] = 0;
     		}
     	}
     	$Popular = $this->multi_array_sort($Popular,'score',SORT_DESC);
     	$this->assign('info',$Popular);
     	$this->assign('page',$data['show']);
     	$this->display();
     }

     // 二维数组排序
     protected function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
        if(is_array($multi_array)){ 
            foreach ($multi_array as $row_array){ 
                if(is_array($row_array)){ 
                    $key_array[] = $row_array[$sort_key]; 
                }else{ 
                    return false; 
                } 
            } 
        }else{ 
            return false; 
        } 
        array_multisort($key_array,$sort,$multi_array); 
        return $multi_array; 
    }

    // 公告显示界面
    public function notice(){
    	if($_GET['type']){
            $id = $this->_get('id','intval');
            $info = M('Dy_notice')->where(array('token'=>$this->token,'id'=>$id))->find();
            $info['content'] = htmlspecialchars_decode($info['content']);
            $this->assign('info',$info);
        }
		$this->display();
    }
    // 公告列表页面显示
    public function noticelist(){
        $where['token'] = $this->token;
        $data = $this->Pages(M('Dy_notice'),$where,15);
        foreach ($data['info'] as $key => $value) {
            $data['info'][$key]['content'] = htmlspecialchars_decode($value['content']);
        }
        $this->assign('info',$data['info']);
        $this->assign('page',$data['show']);
        $this->display();
    }
    // 公告修改
    public function noticeFix(){
    	$noticeModel = M('Dy_notice');
    	if (IS_AJAX) {
            if(IS_POST){
                $data['title'] = $_POST['notice'];
                $data['content'] = $_POST['content'];
                if($_POST['judge'] == 'add'){
                    $data['time'] = time();
                    $data['token'] = $this->token;
                    if($noticeModel->add($data)){
                    $this->ajaxReturn(array('info'=>'添加成功','url'=>U('Commercedy/noticelist',array('token'=>$this->token)),'status'=>0),'JSON');
                    }else{
                        $this->ajaxReturn(array('info'=>'添加失败','status'=>1),'JSON');
                    }
                }else{
                    if($noticeModel->where(array('id'=>$_POST['judge']))->save($data)){
                    $this->ajaxReturn(array('info'=>'修改成功','url'=>U('Commercedy/noticelist',array('token'=>$this->token)),'status'=>0),'JSON');
                    }else{
                        $this->ajaxReturn(array('info'=>'修改失败','status'=>1),'JSON');
                    }
                }
                
                
            }
           
    	}
    }
    // 删除公告
    public function delNotice(){
        $id = $this->_get('id','intval');
        if(M('Dy_notice')->where(array('token'=>$this->token,'id'=>$id))->delete()){
            $this->ajaxReturn(array('info'=>'删除成功','url'=>U('Commercedy/noticelist',array('token'=>$this->token)),'status'=>1),'JSON');
        }else{
            $this->ajaxReturn(array('info'=>'删除失败','status'=>0),'JSON');
        }
    }
    // 查看公告
    public function fixed(){
        $id = $this->_get('id');
        $find  = M('dy_notice')->where(array('token'=>$this->token,'id'=>$id))->find();
        $find['content'] = htmlspecialchars_decode($find['content']);
        $this->assign('info',$find);
        $this->display();
    }

    // 积分设置
    public function setScore(){
        $find = M('Dy_setscore')->where(array('token'=>$this->token))->find();
        $this->assign('info',$find);
        $this->display();
    }

    // 积分设置数据接收
    public function jifen(){
        if(IS_AJAX){
            if(IS_POST){
                $data['pinglun'] = $_POST['pinglun'];
                $data['huifu'] = $_POST['huifu'];
                $data['zhuanfa'] = $_POST['zhuanfa'];
                $data['yindao'] = $_POST['yindao'];
                $data['shouci'] = $_POST['shouci'];
                $data['fabiao'] = $_POST['fabiao'];
                $data['limits'] = $_POST['limits'];
                $data['token'] = $this->token;
                if(M('Dy_setscore')->where(array('token'=>$this->token))->find()){
                    if(M('Dy_setscore')->where(array('token'=>$this->token))->save($data)){
                        $this->ajaxReturn(array('status'=>1,'url'=>U('Commercedy/setScore',array('token'=>$this->token)),'info'=>'修改成功'));
                    }else{
                        $this->ajaxReturn(array('status'=>0,'info'=>'修改失败'));
                    }
                }else{
                    if(M('Dy_setscore')->add($data)){
                        $this->ajaxReturn(array('status'=>1,'url'=>U('Commercedy/setScore',array('token'=>$this->token)),'info'=>'添加成功'));
                    }else{
                        $this->ajaxReturn(array('status'=>0,'info'=>'添加失败'));
                    }
                }
            }
        }
    }

    // 查看回复
    public function lookReply(){
        $dyUsersModel = M('Dy_users');
        $id = $this->_get('id','intval');
        $findComment = $dyUsersModel->where(array(
                'token' => $this->token,
                'uid' => $id,
                'comment' => array(
                        'NEQ',''
                    )
            ))->select();
        
        foreach ($findComment as $k => $v) {
                if($v['replay_id'] != 0){
                    $findNickName = $dyUsersModel->where(array('id'=>$v['replay_id'],'token'=>$this->token))->find();
                    $findComment[$k]['nickname'] = $v['nickname']."  回复  ".$findNickName['nickname'];
                }
            }
        // print_r($findComment);exit;
        $this->assign('info',$findComment);
        $this->display();
    }
    /**
     * 话题榜列表
     */
    public function huati_index(){
        $commentModel = M('Dy_community');
        $where['token'] = $this->token;
        $where['type']=1;
        $data = $this->Pages($commentModel,$where,12,'add_time');
        $this->assign('info',$data['info']);
        $this->assign('page',$data['show']);
        $this->display();
    }

    /**
     * 新增话题
     */
    public function add_huati(){
        if(isset($_GET['id'])){
            $info=M('Dy_community')->where(array('id'=>$_GET['id']))->find();
            $this->assign('info',$info);
        }
        $this->display();
    }
    /**
     * 编辑话题
     */
    public function edit_huati(){
        //p($_POST);die;
        $noticeModel = M('Dy_community');
     //   p($_POST);die;
        if (IS_AJAX) {

             if(IS_POST){

                 //    $_POST=array_filter($_POST);

                if($_POST['judge'] == 'add'){//新增
                    $_POST['add_time'] = date('Y-m-d h:i:s',time());
                    $_POST['token'] = $this->token;
                    $_POST['nickname']='管理员';
                    $_POST['openid']=$this->token;//管理员发的话题的话openid=token;
                    $_POST['type']=1;
                    if($noticeModel->add($_POST)){
                        $this->ajaxReturn(array('info'=>'添加成功','url'=>U('Commercedy/huati_index',array('token'=>$this->token)),'status'=>0),'JSON');
                    }else{
                        $this->ajaxReturn(array('info'=>'添加失败','status'=>1),'JSON');
                    }
                }else{//修改
                    //echo 8;die;
                //    p($_POST);

                    if($noticeModel->where(array('id'=>$_POST['judge']))->save($_POST)){
                        $this->ajaxReturn(array('info'=>'修改成功','url'=>U('Commercedy/huati_index',array('token'=>$this->token)),'status'=>0),'JSON');
                    }else{
                        $this->ajaxReturn(array('info'=>'修改失败','status'=>1),'JSON');
                    }
                }


            }

        }
    }

    /**
     * 查看话题
     */
    public function see_huati(){
        $where['uid']=$this->_get('id');
        $where['token']=$this->token;
        $where['comment']=array('NEQ','');

        $dyUserModel=M('Dy_users');

        $count = $dyUserModel->where($where)->count();
        $page = new Page($count,45);
        $list = $dyUserModel->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        foreach($list as $k=>$v){
            if($v['replay_id'] != 0) {
                $findNickName = $dyUserModel->where(array('id' => $v['replay_id'], 'token' => $this->token))->find();
                $list[$k]['nickname'] = $v['nickname'] . "  回复  " . $findNickName['nickname'];
                $list[$k]['dianzan']=$dyUserModel->where(array('replay_id'=>$v['id'],'ifzan'=>1))->count();
                $list[$k]['pinglun']=$dyUserModel->where(array('replay_id'=>$v['id'],'comment'=>array('NEQ','')))->count();
            }else{
                $list[$k]['nickname'] = $v['nickname'] . "  回复  " . "话题";
                $list[$k]['dianzan']=$dyUserModel->where(array('replay_id'=>$v['id'],'ifzan'=>1))->count();
                $list[$k]['pinglun']=$dyUserModel->where(array('replay_id'=>$v['id'],'comment'=>array('NEQ','')))->count();
            }
        }
        if(isset($_GET['type']) && $_GET['type']==1){
            //点赞排序
           foreach($list as $k=>$v){
               $a=$v['dianzan'].'_'.$v['id'];
               $list[$a]=$v;
               unset($list[$k]);
           }
           krsort($list);
       }
        if(isset($_GET['type']) && $_GET['type']==2){
            //评论排序
            foreach($list as $k=>$v){
                $b=$v['pinglun'].'_'.$v['id'];
                $list[$b]=$v;
                unset($list[$k]);
            }
            krsort($list);
        }
        $this->assign('page', $page->show());
        $this->assign('info',$list);
        $this->assign('id',$_GET['id']);
        $this->display();
    }
} 
?>