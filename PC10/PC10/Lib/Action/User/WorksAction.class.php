<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/21
 * Time: 17:23
 */
class WorksAction extends UserAction
{

    public function index()
    {
        $works = M('Match');
        $where = array('token' => session('token'));
        $count = $works->where($where)->count();
        $page = new Page($count, 10);
        $show = $page->show();
        $list = $works->where($where)->order('starttime desc')->limit($page->firstRow.','.$page->listRows)->select();
        // print_r($list);exit;$this->assign('page',$page->show());

        $this->assign('works', $list);
        $this->assign('page', $show);

        $this->display();
    }


    public function addset()
    {
        $snname = M('Snname');
        $list = $snname->where(array('token'=>session('token')))->select();
      //  print_r($list);exit;
        $this->assign('list',$list);

        if ($_GET['id']) {
            $works = M('Match');
            // {name:name,title:title,extent:extent,pic:pic,starttime:starttime,endtime:endtime,explain:explain,token:'{weikucms:$token}',id:id}
            $arr = $works->where(array('token' => $_REQUEST['token'], 'id' => $_GET['id']))->find();
            $this->assign('set', $arr);
        }
        $this->display();
    }

    public function append()
    {
        $_POST['token'] = session('token');
        if (IS_POST) {
            $works = D('Match');
            $id = intval($_POST['id']);
            if ($id) {
                $result = $works->where(array('token' => session('token'), 'id' => $id))->save($_POST);
                if ($result) {
                    $this->success('修改成功！', U(MODULE_NAME . '/index', array('token' => session('token'))));
                } else {
                    $this->error('修改失败！', U(MODULE_NAME . '/index', array('token' => session('token'))));
                }
            } else {
                $appent = $works->data($_POST)->add();
                if ($appent) {
                    $this->success('添加成功！', U(MODULE_NAME . '/index', array('token' => session('token'))));
                } else {
                    $this->error('添加失败！', U(MODULE_NAME . '/index', array('token' =>session('token'))));
                }
            }
        }

    }

    public function del()
    {
        $data = M('Match');
        $token = session('token');
        $id = $_GET['id'];
        $where = array('id' => $id, 'token' => session('token'));

        $back = $data->where($where)->delete();
        if ($back == true) {
            $this->success('操作成功', U('Works/index', array('token' => session('token'), 'id' => $id)));
        } else {
            $this->error('服务器繁忙,请稍后再试', U('Works/index', array('token' => session('token'))));
        }

    }

    public function match()
    {
        $match = M('Works');
        $token = $_REQUEST['token'];
        $mid = $_GET['mid'];
        $count = $match->where(array('token' => $token, 'mid' => $mid))->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $works = $match->where(array('token' => $token, 'mid' => $mid))->order()->select();
        //print_r($works);exit;
        $list = M('Match')->where(array('id' => $mid, 'token' => $token))->find();
        $this->assign('works', $works);
        $this->assign('list', $list);
        $this->assign('show', $show);


        $this->display();
    }

    public function madel()
    {
        // $token = session('token');
        $mid = $_GET['mid'];
        $id = $_GET['id'];
        $where = array('id' => $id, 'token' => session('token'), 'mid' => $mid);
        $data = M('Works');
        $back = $data->where($where)->delete();
        if ($back == true) {
            $this->success('操作成功', U('Works/match', array('token' => session('token'), 'mid' => $mid, 'id' => $id)));
        } else {
            $this->error('服务器繁忙,请稍后再试', U('Works/match', array('token' => session('token'), 'mid' => $mid)));
        }
    }

    public function poll()
    {
        $poll = M('Pollz');
        $token = session('token');
        $mid = $_REQUEST['mid'];
        $wid = $_REQUEST['wid'];
        $pollz = $poll->where(array('token'=>$token,'mid'=>$mid,'wid'=>$wid))->order()->select();

	$fuid = M('Wxuser')->where(array('token' => $token))->find();
        $uid = $fuid['id'];
        foreach($pollz as $key => $value){
             $openid = $value['openid'];
             $userstemp = M('Wxusers')->where(array('uid' =>$uid, 'openid' => $openid))->find();
             $pollz[$key]['nickname'] = $userstemp['nickname'];

        }
        $set = M('Works')->where(array('token'=>$token,'id'=>$wid,'mid'=>$mid))->find();
        $this->assign('pollz',$pollz);

        $this->assign('set',$set);
        $this->display();
    }

    public function explain()
    {
        $match = M('Match');
        $works = M('Works');
        $id = $_REQUEST['id'];
        $token = session('token');
        $mid = $_REQUEST['mid'];
        //$openid = $_REQUEST['openid'];
        if (IS_POST) {
          // print_r($_POST);exit;
          // $a=M('Works')->where(array('id' => $id,'state'=>0))->find();
           //print_r($a);exit;
            if (M('Works')->where(array('id' => $id))->getField('state')!= null) {

                $state = $works->where(array('id' => $id))->save($_POST);

                if ($state) {
                    $this->success('修改成功！', U(MODULE_NAME . '/explain', array('token' => session('token'),'id'=>$id,'mid'=>$mid,'openid'=>$openid)));
                } else {
                    $this->error('修改失败！', U(MODULE_NAME . '/explain', array('token' => session('token'),'id'=>$id,'mid'=>$mid,'openid'=>$openid)));
                }
            } else {
                $appent = $works->data($_POST)->add();
                if ($appent) {
                    $this->success('添加成功！', U(MODULE_NAME . '/explain', array('token' => session('token'),'id'=>$id,'mid'=>$mid,'openid'=>$openid)));
                } else {
                    $this->error('添加失败！', U(MODULE_NAME . '/explain', array('token' => session('token'),'id'=>$id,'mid'=>$mid,'openid'=>$openid)));
                }
            }
        }
            $list = $works->where(array('token' => $token, 'mid' => $mid, 'id' => $id))->find();

            $arr = $match->where(array('id' => $mid, 'token' => $token))->find();

            $openid = $list['openid'];
        //print_r($openid);
            $fuid = M('Wxuser')->where(array('token' => $token))->find();
            $uid = $fuid['id'];
            $wx = M('Wxusers')->where(array('uid' =>$uid, 'openid' => $openid))->find();
            $this->assign('works', $list);
            $this->assign('match', $arr);
            $this->assign('wx', $wx);
            $this->display();
    }

    /*页面顶部图片管理*/
   /* public function pic(){
        $token = $this->token;
        $count = M('Works_pic')->where(array('token'=>$token))->count();
        $page = new Page($count,10);
        $list = M('Works_pic')->where(array('token'=>$token))->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('list', $list);
        $this->display();
    }*/

    public function picset(){
        $token = $this->token;
        $id = $_GET['id'];
        if($id){
            $info = M('Works_pic')->where(array('id'=>$id,'token'=>$token))->find();
            $this->assign('set',$info);
            $this->display();
        }else{
            $this->display();
        }
    }
    public function picedit(){
        if(IS_POST){

            $token = $this->token;
            //print_r($token);exit;
            $id = $_POST['id'];
            //$data = M('Works_pic')->where(array('id'=>$id,'token'=>$token))->find();
            //print_r($data);exit;
           // print_r($id);exit;
            if($id){
                $info = M('Works_pic')->where(array('id'=>$id,'token'=>$token))->save($_POST);
                if($info){
                    $this->success('修改成功！', U(MODULE_NAME . '/picset', array('token' => $this->token,'id'=>$id)));
                } else {
                    $this->error('修改失败！', U(MODULE_NAME . '/picset', array('token' => $this->token,'id'=>$id)));
                }
            }else{
                $_POST['token'] = $token;
                $ginfo = M('Works_pic')->data($_POST)->add();
                if($ginfo){
                    $this->success('添加成功！', U(MODULE_NAME . '/picset', array('token' => $this->token)));
                } else {
                    $this->error('添加失败！', U(MODULE_NAME . '/picset', array('token' => $this->token)));
                }
            }
        }
    }

  /*  public function picdel(){
        $token = $this->token;
        $id = $_GET['id'];
        $data =  M('Works_pic')->where(array('id'=>$id,'token'=>$token))->delete();
        if($data == true){
            $this->success('操作成功', U('Works/pic', array('token' => $this->token, 'id' => $id)));
        } else {
            $this->error('服务器繁忙,请稍后再试', U('Works/pic', array('token' => $this->token)));
        }
    }*/
}