<?php
class VoteAction extends UserAction{

    public function index(){
        //$this->canUseFunction('vote');
        $user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
        $group=M('User_group')->where(array('id'=>$user['gid']))->find();
        $this->assign('group',$group);
        $this->assign('activitynum',$user['activitynum']);

       // $type = isset($this->_get('type')) ? $this->_get('type') : 'text';
        $list=M('Vote')->where(array('token'=>session('token')))->order('id DESC')->select();
        $count = M('Vote')->where(array('token'=>session('token')))->count();
        $this->assign('count',$count);
        //你不可以再创建投票活动了。
        // if($count >= $user['activitynum'])
        //     $this->assign('ok',1);

        $this->assign('list',$list);
        $this->display();
    }

    public function totals(){
        $token      = session('token');
        $id         = $this->_get('id');
        $t_vote     = M('Vote');
        $t_record  = M('Vote_record');
        $where      = array('id'=>$id,'token'=>session('token'));
        $vote   = $t_vote->where($where)->find();
        if(empty($vote)){
            exit('非法操作');
        }
        $vote_item = M('Vote_item')->where('vid='. $vote['id'])->select();
        $vcount = $t_record->where(array('vid'=>$id))->count();
        $this->assign('count',$vcount);
        $item_count = M('Vote_item')->where('vid='.$id)->select();
        foreach ($item_count as $k=>$value) {
            $vote_item[$k]['per']=(number_format(($value['vcount'] / $vcount),2))*100;
            $vote_item[$k]['pro']=$value['vcount'];

        }
        $this->assign('vote_item', $vote_item);
        $this->assign('vote',$vote);
        $this->display();
    }

    public function add(){
     $this->assign('type',$this->_get('type'));

        if(IS_POST){
         //var_dump($_REQUEST);exit;
            $adds = $_REQUEST['add'];
            if(empty($adds) || empty($_REQUEST['add']['item'][0]) && empty($_REQUEST['add']['startpicurl'][0])){
                $this->error2('投票选项你还没有填写');
                exit;
            }
            foreach ($adds as $ke => $value) {
                 foreach ($value as $k => $v) {
                    if($v != "")
                     $item_add[$k][$ke]=$v;
                 }
            }
            $data=D('Vote');
            //$_POST['id']=$this->_get('id');
            $_POST['token']=session('token');
            $_POST['type'] = $this->_get('type');
            $_POST['statdate']=strtotime($this->_post('statdate'));
            $_POST['enddate']=strtotime($this->_post('enddate'));
            $_POST['cknums'] = $this->_post('cknums');
            $_POST['display'] = $this->_post("display");
            $_POST['info'] = strip_tags($this->_post("info"));
            $_POST['picurl'] = $this->_post("picurl");
			 $_POST['sl'] = $this->_post("sl");
            $_POST['title'] = $this->_post("title");
            $_POST['keyword'] = $this->_post('keyword');

            if($_POST['enddate']<$_POST['statdate']){
                $this->error2('结束时间不能小于开始时间!');
                exit;
            }
            print_r($_POST);exit;

            //$isset_keyword = $data->where(array('keyword' => $_POST['keyword'],'token'=>$_POST['token']))->field('keyword')->find();
            //if($isset_keyword != NULL){
             //   $this->error2('关键词已经存在！');
              //  exit;
           // }
            $t_item = M('Vote_item');

            if($data->create()!=false){
                if($id=$data->add()){
                    foreach ($item_add as $k => $v) {
                      if($v['item'] != ''){
                        $data2['vid'] = $id;
                        $data2['item']=$v['item'];
                        $data2['rank']=empty($v['rank']) ? "1" : $v['rank'];
                        $data2['vcount']=empty($v['vcount']) ? "0" : $v['vcount'];
                        if($_POST['type'] == 'img'){
                            $data2['startpicurl']=empty($v['startpicurl']) ? "#" : $v['startpicurl'];
                            $data2['tourl']=empty($v['tourl']) ? "#" : $v['tourl'];
                        }
                        $t_item->add($data2);
                      }

                    }
                    $data1['pid']=$id;
                    $data1['module']='Vote';
                    $data1['token']=session('token');
                    $data1['keyword']=$_POST['keyword'];
                    M('keyword')->add($data1);
                    //$ukeywordser=M('Users')->where(array('id'=>session('uid')))->setInc('activitynum');
                    $this->success2('添加成功',U('Vote/index',array('token'=>session('token'))));
                }else{
                    $this->error2('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error2($data->geterror2());
            }
        }else{
            $this->display();
        }

    }

    public function del(){

        $type = $this->_get('type');
        $id = $this->_get('id');
        $vote = M('Vote');
        $find = array('id'=>$id,'type'=>$type);
        $result = $vote->where($find)->find();
         if($result){
            $vote->where('id='.$result['id'])->delete();
            M('Vote_item')->where('vid='.$result['id'])->delete();
            M('Vote_record')->where('vid='.$result['id'])->delete();
            $where = array('pid'=>$result['id'],'module'=>'Vote','token'=>session('token'));
            M('Keyword')->where($where)->delete();
            $this->success2('删除成功',U('Vote/index',array('token'=>session('token'))));
         }else{
            $this->error2('非法操作！');
         }

    }

    public function setinc(){
        $id=$this->_get('id');
        $where=array('id'=>$id,'token'=>session('token'));
        $check=M('Vote')->where($where)->find();
        if($check==NULL)$this->error2('非法操作');
        $user=M('Users')->field('gid,activitynum')->where(array('id'=>session('uid')))->find();
        $group=M('User_group')->where(array('id'=>$user['gid']))->find();
        if($user['activitynum']>=$group['activitynum']){
            $this->error2('您的免费活动创建数已经全部使用完,请充值后再使用',U('Home/Index/price'));
        }
        if ($check['status']==0){
            $data=M('Vote')->where($where)->save(array('status'=>1));
            $tip='恭喜你,活动已经开始';
        }else {
            $data=M('Vote')->where($where)->save(array('status'=>0));
            $tip='设置成功,活动已经结束';
        }

        if($data!=NULL){
            $this->success2($tip);
        }else{
            $this->error2('设置失败');
        }

    }
    public function setdes(){
        $id=$this->_get('id');
        $where=array('id'=>$id,'token'=>session('token'));
        $check=M('Vote')->where($where)->find();
        if($check==NULL)$this->error2('非法操作');
        $data=M('Vote')->where($where)->setDec('status');
        if($data!=NULL){
            $this->success2('活动已经结束');
        }else{
            $this->error2('服务器繁忙,请稍候再试');
        }

    }

    public function edit(){
        $this->assign('type',$this->_get('type'));
        if(IS_POST){
            $data=D('Vote');
            $_POST['id']= (int)$this->_post('id');
            $_POST['token']=session('token');
            $_POST['type'] = $this->_get('type');
            $_POST['statdate']=strtotime($this->_post('statdate'));
            $_POST['enddate']=strtotime($this->_post('enddate'));
            $_POST['cknums'] = (int)$this->_post('cknums');
            $_POST['display'] = $this->_post("display");
            $_POST['info'] = strip_tags($this->_post("info"));
            $_POST['picurl'] = $this->_post("picurl");
            $_POST['title'] = $this->_post("title");
             if($_POST['enddate']<$_POST['statdate']){
                $this->error2('结束时间不能小于开始时间!');
                exit;
            }
            $where=array('id'=>$_POST['id'],'token'=>session('token'));
            $check=$data->where($where)->find();

            if($check==NULL) exit($this->error2('非法操作'));
            if(empty($_REQUEST['add'])){
                $this->error2('投票选项必须填写');
                exit;
            }

            $t_item = M('Vote_item');
            $datas = $_REQUEST['add'];
            //$datas = array_filter($datas);
             foreach ($datas as $ke => $value) {
                 foreach ($value as $k => $v) {
                    if( $v != ""){
                        $item_add[$k][$ke]=$v;
                    }
                 }
            }

            $isnull =  $t_item->where('vid='.$_POST['id'])->find();

            foreach ($item_add as $k => $v) {
                $a++;
                if($v['item'] !=""){
                    $i_id['id']=$v['id'];
                    if($i_id['id'] != ''){
                        $data2['item']=$v['item'];
                        $data2['rank']=empty($v['rank']) ? "1" : $v['rank'];
                        $data2['vcount']=empty($v['vcount']) ? "0" : $v['vcount'];
                        if($this->_get('type') == 'img'){
                            $data2['startpicurl']=$v['startpicurl'];
                            $data2['tourl']=empty($v['tourl']) ? "#" : $v['tourl'];
                        }
                      $t_item->where(array('id'=>$i_id['id'],'vid'=>$_POST['id']))->save($data2);

                    }else{

                            $data2['vid'] = $_POST['id'];
                            $data2['item']=$v['item'];
                            $data2['rank']=empty($v['rank']) ? "1" : $v['rank'];
                            $data2['vcount']=empty($v['vcount']) ? "0" : $v['vcount'];
                            if($_POST['type'] == 'img'){
                                $data2['startpicurl']=empty($v['startpicurl']) ? "#" : $v['startpicurl'];
                                $data2['tourl']=empty($v['tourl']) ? "#" : $v['tourl'];
                            }
                            $t_item->add($data2);

                    }
                }

            }

            if($data->create()){

                if($data->where($where)->save($_POST)){
                    $data1['pid']=$_POST['id'];
                    $data1['module']='Vote';
                    $data1['token']=session('token');

                    $da['keyword']=trim($_POST['keyword']);
                    $ok = M('keyword')->where($data1)->save($da);
                    $this->success2('修改成功!',U('Vote/index',array('token'=>session('token'))));exit;
                }else{
                    //$this->error2('没有做任何修改！');exit;
                    $this->success2('修改成功',U('Vote/index',array('token'=>session('token'))));exit;
                }
            }else{
                $this->error2($data->geterror2());
            }


        }else{
            $id=(int)$this->_get('id');
            $where=array('id'=>$id,'token'=>session('token'));
            $data=M('Vote');
            $check=$data->where($where)->find();
            if($check==NULL)$this->error2('非法操作');
            $vo=$data->where($where)->find();
            $items = M('Vote_item')->where('vid='.$id)->order('rank DESC')->select();
            $this->assign('items',$items);

            $this->assign('vo',$vo);

            $this->display('add');
        }
    }

    public function del_tab(){
         $da['tid']      = strval($this->_post('id'));
         M('Vote_item')->where(array('id'=>$da['tid']))->delete();
         //$arr=array('errno'=>0,'tid'=>$da['tid']);
         //echo json_encode($arr);
         exit;
    }


}



?>