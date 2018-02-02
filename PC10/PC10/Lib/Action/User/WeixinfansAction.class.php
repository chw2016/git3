<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-4-26
 * Time: 下午4:26
 * To change this template use File | Settings | File Templates.
 */
class WeixinfansAction extends UserAction{

    public function index(){
        $usersModel = M('Wxusers');
        $iType = $_REQUEST['type'];
        if($iType){
            $wxuser = M('Wxuser')->where(array('token' => session('token')))->find();
            $count = $usersModel->where(array('uid' => $wxuser['id'], 'status' => 1,'type'=>$iType))->count();// 查询满足要求的总记录数
            $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $usersModel->order('add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->where(array('uid' => $wxuser['id'], 'status' => 1,'type'=>$iType))->select();
            $classifyname = M('Wxuser_classify')->where(array('id'=>$iType))->find();
        }else {
            $wxuser = M('Wxuser')->where(array('token' => session('token')))->find();
            $count = $usersModel->where(array('uid' => $wxuser['id'], 'status' => 1))->count();// 查询满足要求的总记录数
            $Page = new Page($count, 20);// 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $usersModel->order('add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->where(array('uid' => $wxuser['id'], 'status' => 1))->select();
        }
        $classlist =  M('Wxuser_classify')->where(array('token'=>session('token')))->order('id')->select();
        $this->assign('classlist',$classlist);
        $this->assign('classifyname',$classifyname);
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    public function userview(){
        $id = $_GET['id'];
        $usersModel = M('Wxusers');
        $userinfo = $usersModel->where(array('id'=>$id))->find();

        $wxuser = M('Wxuser')->where(array('token'=>session('token')))->find();

        $where =  "uid=".$wxuser['id']." And from_openid = '".$userinfo['openid']."' or to_openid='".$userinfo['openid']."'";
        import('ORG.Util.Page');// 导入分页类
        $count      = M('Msg_list')->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $msgList = M('Msg_list')->order('add_time','desc')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $show       = $Page->show();// 分页显示输出

        $this->assign('userinfo',$userinfo);
        $this->assign('wxuser',$wxuser);
        $this->assign('page',$show);
        $this->assign('msglist',$msgList);
        $this->assign('fakeid',$wxuser['fakeid']);
        $this->display();
    }
    public function sendmsg(){
        if(IS_AJAX){
            $content = $_REQUEST['content'];
            $openid = $_REQUEST['openid'];
            $wxuser = M('Wxuser')->where(array('token'=>session('token')))->find();

            $params['token'] = $this->token;
            $params['openid'] = $this->openid;
            $params['content'] = $content;
            $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
            $data = $this->api_notice_increment($url,http_build_query($params));
            if(!$data) {
                $this->api_notice_increment($url, http_build_query($params));
            }else{
                $msglistModel = M('Msg_list');
                $mdata = array();
                $mdata['uid'] = $wxuser['id'];
                $mdata['to_openid'] = $openid;
                $mdata['from_openid'] = $wxuser['wx_openid'];
                $mdata['content'] =$content;
                $mdata['type'] = 'text';
                $mdata['add_time'] = time();
                $msglistModel->data($mdata)->add();
                echo $this->encode(array('base_resp'=>0));exit;
            }

        }
    }


   /* public function sendmsg(){
        $content = $_REQUEST['content'];
        $openid = $_REQUEST['openid'];
        $fakeid = $_REQUEST['fakeid'];
        if(!empty($fakeid)){
            $wxuser = M('Wxuser')->where(array('token'=>session('token')))->find();
            if(!empty($wxuser['wx_a']) &&  !empty($wxuser['wx_p'])){
                Vendor('weixin.WX_Remote_Opera');
                $ro = new WX_Remote_Opera();
                $token=$ro->test_login($wxuser['wx_a'],$wxuser['wx_p']);
                if($token){
                    $ro->init($wxuser['wx_a'],$wxuser['wx_p']);
                    //Array ( [wx_account] => diaobaojiecao [fakeid] => 3083415613 [nickname] => 屌爆段子 [ghid] => gh_8da4455c132d )
                    $ro->sendmsg($content,$fakeid,$token);

                    $msglistModel = M('Msg_list');
                    $mdata = array();
                    $mdata['uid'] = $wxuser['id'];
                    $mdata['to_openid'] = $openid;
                    $mdata['from_openid'] = $wxuser['wx_openid'];
                    $mdata['content'] =$content;
                    $mdata['type'] = 'text';
                    $mdata['add_time'] = time();

                    $params['token'] = $this->token;
                    $params['openid'] = $this->openid;
                    $params['content'] = $content;
                    $url =C('site_url')."/index.php?g=Home&m=Auth&a=sendTextMsg";
                    $data = $this->api_notice_increment($url,http_build_query($params));
                    if(!$data){
                        $this->api_notice_increment($url,http_build_query($params));
                    }else{
                         $msglistModel->data($mdata)->add();

                    }

                }
            }
        }
    }*/

    public function msglist(){
        $wxuser = M('Wxuser')->where(array('token'=>session('token')))->find();
        import('ORG.Util.Page');// 导入分页类
        $count      = M('Msg_list')->where(array('uid'=>$wxuser['id'],'to_openid'=>$wxuser['wx_openid']))->count();// 查询满足要求的总记录数
        $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $msgList = M('Msg_list')->order('add_time desc')->where(array('uid'=>$wxuser['id'],'to_openid'=>$wxuser['wx_openid']))->limit($Page->firstRow.','.$Page->listRows)->select();
        $wxusers = M('Wxusers');
        foreach($msgList as $k=>$v){
            $temp= array();
            $temp = $wxusers->field('id,fakeid,headimgurl,nickname')->where(array('openid'=>$v['from_openid'],'uid'=>$wxuser['id']))->find();
            $msgList[$k]['fakeid'] = $temp['fakeid'];
            $msgList[$k]['uid'] = $temp['id'];
            $msgList[$k]['headimgurl'] = $temp['headimgurl'];
	    $msgList[$k]['nickname'] = $temp['nickname'];
        }


        $this->assign('page',$show);
        $this->assign('msglist',$msgList);
        $this->display();
    }

    /*添加粉丝分类名称*/
    public function fansiclassify(){
        $oWxuserModel = M('Wxusers');
        if(IS_AJAX){
            $temp = $oWxuserModel->where(array('id'=>$_POST['id']))->find();
            if($temp){
                $data['remarks'] = $_POST['remarks'];
                $iMove = $oWxuserModel->where(array('id'=>$_POST['id']))->save($data);
                if($iMove){
                    $this->success('备注成功',U('Weixinfans/index',array('token'=>$this->token,'type'=>$_GET['type'])));
                }else{
                    $this->error('系统繁忙,备注失败！',U('Weixinfans/index',array('token'=>$this->token,'type'=>$_GET['type'])));
                }
            }else{
                $this->error('非法操作！',U('Weixinfans/index',array('token'=>$this->token,'type'=>$_GET['type'])));
            }
        }
    }

    /*需改粉丝分组*/
    public function moveclass(){
        $oWxuserModel = M('Wxusers');
        //$oWxclsaaifyModel = M('Wxuser_classify');
        if(IS_AJAX){
            $temp = $oWxuserModel->where(array('id'=>$_POST['id']))->find();
            if($temp){
                $data['type'] = $_POST['classifyid'];
                $iMove = $oWxuserModel->where(array('id'=>$_POST['id']))->save($data);
                if($iMove){
                    $this->success('移组成功',U('Weixinfans/index',array('token'=>$this->token)));
                }else{
                    $this->error('系统繁忙,移组失败！',U('Weixinfans/index',array('token'=>$this->token,'type'=>$_GET['type'])));
                }
            }else{
                $this->error('非法操作！',U('Weixinfans/index',array('token'=>$this->token,'type'=>$_GET['type'])));
            }
        }
    }


    /*分组名删除*/
    public function delclassify(){
        $oWxuserModel = M('Wxuser_classify');
        $oWxusersModel = M('Wxusers');
        if(IS_AJAX){
            $data = $_POST['id'];
            $temp = $oWxuserModel->where(array('id'=>$data))->find();
            if($temp){
                $iMove = $oWxuserModel->where(array('id'=>$data))->delete();
                if($iMove){
                    $aUseres = $oWxusersModel->where(array('uid'=>$temp['uid'],'type'=>$data))->select();
                    foreach($aUseres as $k=>$val){
                        $oWxusersModel->where(array('id'=>$val['id']))->save(array('type'=>0));
                    }
                    $this->success('删组成功',U('Weixinfans/index',array('token'=>$this->token,'type'=>$_GET['type'])));
                }else{
                    $this->error('系统繁忙,删组失败！',U('Weixinfans/index',array('token'=>$this->token,'type'=>$_GET['type'])));
                }
            }else{
                $this->error('非法操作！',U('Weixinfans/index',array('token'=>$this->token,'type'=>$_GET['type'])));
            }
        }
    }
    
    
    public function fanssync(){
    	$access_token = $this->getAccessTokenByTwoType();	
	$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token;
	$content = file_get_contents($url);
	$fans = json_decode($content,true);
	$addcount = 0;
	$updatecount = 0;
	$f = true;
	while($fans['next_openid'] != null){
	     if(isset($fans['total'])){
		    if(count($fans['data']['openid']) > 0){
                foreach($fans['data']['openid'] as $val){
                    $ret = $this->getOpenidUser($access_token,$val);
                    if($ret == 1){
                       $updatecount = $updatecount+1;
                    }else if($ret == 3){
                       $addcount = $addcount+1;
                    }
                }
		     }
             $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token."&next_openid=".$fans['next_openid'];
                 $content = file_get_contents($url);
                 $fans = json_decode($content,true);
         }else{
            $f = false;
            break;
         }
	}
	
	if($f == false){
	    echo $this->encode(array('code'=>-1,'msg'=>'同步失败,可能你的账号类型没有此权限'));
	}else{
	    echo $this->encode(array('code'=>0,'msg'=>'同步成功,新增'.$addcount.'个粉丝,更新'.$updatecount.'个粉丝'));
	}


    	
    }
    
    public function getOpenidUser($access_token,$openid){
    	$getuserinfourl = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid;
	$userinfocontent = $this->api_notice_increment($getuserinfourl);
	$userinfodata = json_decode($userinfocontent);
	if ($userinfodata) {
		$wxuserModel = M('Wxuser');
		$wxusersModel = M('Wxusers');
		$wxuserdata = $wxuserModel->field('id')->where(array('token' => $this->token))->find();
		$updatedata = array();
		$updatedata['nickname'] = $userinfodata->nickname;
		$updatedata['sex'] = $userinfodata->sex;
		$updatedata['language'] = $userinfodata->language;
		$updatedata['city'] = $userinfodata->city;
		$updatedata['province'] = $userinfodata->province;
		$updatedata['country'] = $userinfodata->country;
		$updatedata['headimgurl'] = $userinfodata->headimgurl;
		if ($res = $wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->find()) {
		     if($wxusersModel->where(array('uid' => $wxuserdata['id'], 'openid' => $openid))->data($updatedata)->save()){
		     	return 1;
		     }else{
		     	return 2;
		     }
		}else{	
		    $adddata = $updatedata;
		    $adddata['status'] = 1;
	            $adddata['uid'] = $wxuserdata['id'];
	            $adddata['add_time'] = time();
	            $adddata['update_time'] = time();
	            $adddata['openid'] = $userinfodata->openid;
	            $adddata['subscribe_time'] = $userinfodata->subscribe_time;
		    $adddata['remarks'] = $userinfodata->remarks;
		    //print_r($adddata);die;
		    if($wxusersModel->data($adddata)->add()){
		    	return 3;
		    }else{
		    	return 4;
		    }
		}
	}
    
    }



}
