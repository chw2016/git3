<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/24
 * Time: 15:59
 */
class WorksAction extends BaseAction{

    public function _initialize(){
        parent::_initialize();
    }
    //进入页
    public function start(){
        $match = M('match');
        $token = $this->token;
        $host = $match->where(array('token'=>$token))->order('starttime desc')->limit(20)->select();
        $info = M('Works_pic')->where(array('token'=>$token,'id'=>2))->find();
       // print_r($info);exit;
        $this->assign('info',$info);
        $this->assign('set',$host);
        $this->display();

    }

    //投票主页
    public function index(){
        $match = M('Works');
        $token=$this->token;
        $mid=$this->_get('mid','intval');
        //print_r($mid);exit;
        $list = $match->where(array('token'=>$token,'mid'=>$mid,'state'=>1))->order('poll desc')->select();
        $works = M('Match')->where(array('token'=>$token,'id'=>$mid))->find();
        $this->assign('list',$list);
        $this->assign('set',$works);
        $this->assign('selected',1);
        $this->display();
    }

    //参赛填写资料
    public function set(){
        $works = M('works');
        $where['token']=$this->token;
        $where['mid']=$this->_get('mid','intval');
        $wid=$this->_get('wid');
        //$map['id'] = array(array('gt',3),array('lt',10),'or') ;
        $where['openid']=$this->_get('openid');
        //print_r($where['id']);exit;
        if($wid){
            $list = M('Match')->where(array('token'=>$this->token))->select();
            $list1 = M('Works')->where(array('id'=>$wid,'token'=>$where['token']))->find();
            $this->assign('set',$list);
            $this->assign('res',$list1);
            //  $this->assign('selected',4);
            $this->display('tpl/Wap/default/Works_set.html');
        }else{
            $where['state'] = array(array('eq',0),array('eq',1),array('eq',2),'or') ;
            $host = $works->where($where)->find();
           if($host){

               /*
                 * 引入微信js接口
                 */
               Vendor('weixin.jssdk');
               $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();

               $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
               $signPackage = $jssdk->GetSignPackage($this->token);
               $this->assign('signPackage',$signPackage);

               $gfind = $works->where($where)->getField('poll');
               $rus['poll'] = array('egt',$gfind);
               $rus['state'] = 1;
               $rus['token'] = $where['token'];
               $rus['mid'] =$where['mid'];
               $count = $works->where($rus)->count('poll');
               $list = $works->where($where)->find();
               $this->assign('set',$list);
               $match= M('match');
               $res = $match->where(array('token'=>$where['token'],'id'=>$where['mid']))->find();
               $this->assign('ranking',$count);
               $this->assign('res',$res);
              // $this->assign('selected',4);
                $this->display('tpl/Wap/default/Works_poll.html');
            }else{

                $list = M('Match')->where(array('token'=>$this->token))->select();
                $this->assign('set',$list);
             //  $this->assign('selected',4);
                $this->display('tpl/Wap/default/Works_set.html');
          }
        }

    }

    //上传图片uploads类
    public function uploadsT(){
        import('ORG.Net.UploadFile');//导入上传类
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg' ,'png' ,'gif');// 设置附件上传类型
        $upload->savePath =  './upload/wapimg/';// 设置附件上传目录
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }
        if($upload->upload()){
            // echo "<script language='JavaScrip'>alert('上传成功！);</script>";
            $info =  $upload->getUploadFileInfo();
            $imgpath=$info[0]['savepath'].$info[0]['savename'];
            $arr = array(
                'name'=>$info[0]['savename'],
                'pic'=>$imgpath,
                'size'=>$size
            );
            echo json_encode($arr);
        }else{
            $error = $this->error($upload->getErrorMsg());

            // echo "<script>alert('上传失败！');window.location.href=".U('Sellcar/index')."</script>";
            // $this->error('保存失败！',U(MODULE_NAME.'/index',array('token'=>session('token'))));
        }
    }
    public function entry(){
        if(IS_POST){
          //  print_r($_POST);exit;
            $works = M('Works');
            $token = $this->token;
            $openid =  $this->openid;
            $mid = $_POST['mid'];
            $vol = M('Match')->where(array('token'=>$token,'id'=>$mid))->find();
            $_POST['openid']= $openid;
            $data = $works->where(array('openid'=>$openid,'token'=>$token))->find();
            if($data){
                $_POST['state'] = 0;
               $host =  $works->where(array('openid'=>$openid,'token'=>$token))->save($_POST);
                if($host){
                    $this->success('您已成功再次申请了此次比赛，已在审核中，请等待审核结果！',U(MODULE_NAME.'/poll',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'),'wid'=>$data['id'],'sid'=>$vol['sid'],'ishow'=>1)));
                }else{
                    $this->error('您再次申请失败，请重新申请！',U(MODULE_NAME.'/set',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'))));
                }
            }else{

                $res = $works->data($_POST)->add();
                if($res){
                    $rul = $works->where(array('openid'=>$openid,'token'=>$token))->find();
                    $this->success('您已成功申请了此次比赛，已在审核中，请等待审核结果！',U(MODULE_NAME.'/poll',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'),'wid'=>$rul['id'],'sid'=>$vol['sid'],'ishow'=>1)));
                }else{
                    $this->error('您申请失败，请重新申请！',U(MODULE_NAME.'/set',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'))));
                }
            }
            }

        $this->display();
    }

    //投票页面
    public function poll(){
        $works = M('works');
        $token=$this->token;
        $openid =$this->openid;
        $mid=$this->_get('mid','intval');
        $id = $this->_get('wid','intval');
        $poll1 = $works->where(array('token'=>$token,'mid'=>$mid,'id'=>$id))->getField('poll');
        $days = M('Match')->where(array('token'=>$token,'id'=>$mid))->find();
       //print_r(strtotime($days['starttime']));exit;
        $pollz = M('Pollz');

        if(IS_POST){
            $Wxuser=M('Wxuser')->field('id')->where(array('token'=>$this->token))->find();
            if($Wxuser){
                $Wxusers=M('Wxusers')->field('id')->where(array('uid'=>$Wxuser['id'],'openid'=>$openid,'status'=>1))->find();
                if($Wxusers){
                    if(time() < strtotime($days['starttime']) ||time() >= strtotime($days['endtime'])){
                        $this->error('抱歉，现在不在活动期间',U(MODULE_NAME.'/poll',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'),'wid'=>$id)));
                    }else{
                        $where['polltime'] = array(array('gt',strtotime(date("Y-m-d",time())." 00:00:00")),array('lt', strtotime(date
                            ("Y-m-d",time())." 23:59:59")));
                        $where['token']= $token;
                        $where['mid']= $mid;
                        $where['wid']= $id;
                        $where['openid']=$openid;
                        $data = $pollz->where($where)->find();

                if($data == null && $where['openid']!=null){
                    $data1['poll'] = $poll1+1;
                   // print_r($poll);exit;

                    $polls = $works->where(array('token'=>$token,'id'=>$id))->save($data1);
                    if($polls){
                        $polltime = time();
                        $endtime = strtotime("+4 week");
                        $set=$pollz->data(array('openid'=>$this->openid,'mid'=>$mid,'wid'=>$id,'polltime'=>$polltime,'endtime'=>$endtime))->add();
                        $hast = array('openid'=>$this->openid,'get_time'=>time());
                        $where1['token'] = $token;
                        $where1['sid'] = $this->_get('sid');
                        $where1['openid'] = "";
                      //  print_r($where1);exit;
                        //print_r($where1);
                         // $where1['get_time'] = "";
                        //$nickname = $User->where('status=1')->getField('nickname',8);
                        //$nickname = $User->where('status=1')->limit(8)->getField('nickname',true);

                        $lists = M('Sn')->where($where1)->limit(1)->find();
                        $sn = null;
                        if($lists){
                            $sn = M('Sn')->where(array('id'=>$lists['id']))->save($hast);
                        }else{
                            $this->error('已经没有优惠券了哦！',U(MODULE_NAME.'/poll',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'),'wid'=>$id,'sid'=>$days['sid'],'ishow'=>1)));
                        }
                        //print_r($where2);exit();

                        if($set && $sn){
                            $this->success('提交成功，恭喜您获得优惠券一张！',U(MODULE_NAME.'/salecard',array('token'=>$token,'mid'=>$mid,'sid'=>$this->_get('sid'),'scid'=>$lists['id'],'openid'=>$this->_get('openid'),'wid'=>$id)));
                        }else{
                            $this->error('抱歉，投票失败！',U(MODULE_NAME.'/poll',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'),'wid'=>$id,'sid'=>$days['sid'])));
                        }
                    }else{
                        $this->error('抱歉，投票失败！',U(MODULE_NAME.'/poll',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'),'wid'=>$id,'sid'=>$days['sid'])));
                    }

                        }else{
                            $this->error('您今天已经帮您的好友投过票了，请明天再来！',U(MODULE_NAME.'/poll',array('token'=>$token,'mid'=>$mid,'openid'=>$this->_get('openid'),'wid'=>$id,'sid'=>$days['sid'])));
                        }
                    }
                }else{
                    //$this->error('您还没有关注我们哦！ ','http://v.wapwei.com/Home/Nofind/isnotsub/token/'.$this->token);
                    $this->error('要关注公众号以后，再到朋友圈为我投票哦！赢了我请您吃糖！ ','http://mp.weixin.qq.com/s?__biz=MjM5OTY3NjUxOA==&mid=202059882&idx=1&sn=c9c33feea8f12b71de7ccd62797e9b78&scene=4#wechat_redirect');
		    //return 2;
                }
            }else{
                $this->error('要关注公众号以后，再到朋友圈为我投票哦！赢了我请您吃糖！','http://mp.weixin.qq.com/s?__biz=MjM5OTY3NjUxOA==&mid=202059882&idx=1&sn=c9c33feea8f12b71de7ccd62797e9b78&scene=4#wechat_redirect');
                //return 3;
            }
        }else{
            /*
             * 引入微信js接口
             */
            Vendor('weixin.jssdk');
            $appdata = M('Diymen_set')->where(array('token' => $this->token))->find();
            $jssdk = new JSSDK($appdata['appid'], $appdata['appsecret']);
            $signPackage = $jssdk->GetSignPackage($this->token);
            $this->assign('signPackage',$signPackage);

            $gfind = $works->where(array('token'=>$token,'mid'=>$mid,'id'=>$id))->getField('poll');
            $rus['poll'] = array('egt',$gfind);
            $rus['state'] = 1;
            $rus['token'] = $token;
	    //$rus['openid'] = $this->openid;
            $rus['mid'] = $mid;
            $count = $works->where($rus)->count('poll');
            // print_r($count);exit;
            $list = $works->where(array('token'=>$token,'mid'=>$mid,'id'=>$id))->find();
            $match= M('match');
            $res = $match->where(array('token'=>$token,'id'=>$mid))->find();
            $this->assign('ranking',$count);
            $this->assign('res',$res);
            $this->assign('set',$list);
            //print_r($list);exit;

            $this->display();
        }
    }




    //拉票分享页面
    public function share(){
        $works = M('works');
        $token=$this->token;
        $mid=$this->_get('mid','intval');
        $id = $this->get('wid');
        $arr= $works->where(array('token'=>$token,'id'=>$id))->find();
        $this->assign('set',$arr);
        $this->display();
    }

    #参与方法
    public function rules(){
        $this->assign('selected',2);
        $this->display();
    }

    #活动规则
    public function event(){
        $match = M('match');
        $token=$this->token;
        $mid=$this->_get('mid','intval');
        $set = $match->where(array('token'=>$token,'id'=>$mid))->find();
        $this->assign('set',$set);
        $this->assign('selected',3);
        $this->display();
    }

    /*
     * 优惠券页面
     */
    public function salecard(){
        $sn =M('Sn');
        $id = $this->_get('scid');
        $token = $this->token;
        $sid = $this->_get('sid');
        $openid =$this->openid;
        $set = $sn->where(array('id'=>$id,'token'=>$token,'sid'=>$sid,'openid'=>$openid))->find();
        $this->assign('set',$set);
        $this->display();
    }









}