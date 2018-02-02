<?php
//Created by my on 2014/12/5.
//User:訾超
//                   _ooOoo_
//                  o8888888o
//                  88" . "88
//                  (| -_- |)
//                  O\  =  /O
//               ____/`---'\____
//             .'  \\|     |//  `.
//            /  \\|||  :  |||//  \
//           /  _||||| -:- |||||-  \
//           |   | \\\  -  /// |   |
//           | \_|  ''\---/''  |   |
//           \  .-\__  `-`  ___/-. /
//         ___`. .'  /--.--\  `. . __
//      ."" '<  `.___\_<|>_/___.'  >'"".
//     | | :  `- \`.;`\ _ /`;.`/ - ` : | |
//     \  \ `-.   \_ __\ /__ _/   .-` /  /
//======`-.____`-.___\_____/___.-`____.-'======
//                   `=---='
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//         佛祖保佑       永无BUG
//
class ForumAction extends BaseAction{
	public function _initialize(){
		parent::_initialize();
		$token = $this->_request('token');
        //bgurl是Forum_star.html页面的背景图片，可以再后台进行设置
		$isopen = M('Forum_config')->field('isopen,bgurl')->where("token = '$token'")->find();
	        if($isopen['isopen'] == 0){

	            $this->error('请联系官方开启讨论社区');
	        }

		$wxusers = $this->wxusers;
        //如果没有头像，则使用默认头像
		if($wxusers['headimgurl'] == ''){
                  $wxusers['headimgurl'] = './tpl/static/star/img/face.png';
		}
		$where['token']=$this->token;
		$this->assign('wxusers',$wxusers);

		$this->assign('bgurl',$isopen['bgurl']);
		
	}

    public function star(){
        $token = $this->_get('token');
        $openid = $this->_get('openid');
        switch ($this->token) {
            case '8d6c5f62afee2198ce8e95e518282478':
                $sStore = U('Wap/Store_shop/cats', array('token' => $this->token, 'openid' => $this->openid));
                break;
            default:
                $sStore = U('Wap/Store_new/cats', array('token' => $this->token, 'openid' => $this->openid));
                break;
        }
        $this->assign('store', $sStore);
        $messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$openid' AND status = 1 AND isread = 1")->count();
        $this->assign('messageNum',$messageNum);
        $this->display();
    }

	//论坛首页
	public function index(){
		$forum = M('Forum_topics');
		$token = $this->_get('token');
        $wx = M('Wxuser')->where(array('token'=>$this->token))->field('id')->find();
        $u = $wx['id'];
		$where = array('status'=>'1','token'=>$token);
		$count      = $forum->where( $where )->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();
        $openid = $this->_get('openid');
		$list = $forum->where( $where )->order('createtime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$openid' AND status = 1 AND isread = 1")->count();

		$wxname = M('Wxuser')->where("token = '$token'")->field('wxname')->find();
		foreach($list as $k=>$v){
			$list["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$id = $v['id'];
			$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1")->select();

			$list["$k"]["cnum"] = count($comment);
			if($v['photos'] != ''){
				$list["$k"]["photoArr"] = explode('|',$v['photos']);
			}
			$list["$k"]["uinfo"] = $this->uinfo($v['uid'],$u);
		}
        //增加浏览量
		if(!cookie('pv')){
			M('Forum_config')->where("token = '$token'")->setInc('pv');
			cookie('pv','1',24*60*60);
		}
		$config = M('Forum_config')->where("token = '$token'")->find();
		$this->assign('messageNum',$messageNum);
		$this->assign('config',$config);
		$this->assign('wxname',$wxname['wxname']);
		$this->assign('show',$show);
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->assign('openid',$openid);
		$this->display();

	
	}
	
	//首页ajax加载
	public function moreList(){
		$token = $this->_get('token');
        $wxuser = M('Wxuser')->where(array('token'=>$token))->field('id')->find();
        $uid = $wxuser['id'];
		$forum = M('Forum_topics');
		$where = array('status'=>'1','token'=>$token);
		$count = $forum->where( $where )->count();
        $Page = new Page($count,10);
		$openid = $this->_get('openid');
		$pageNum = $this->_post('pageNum','intval');
		$pagetotal = ceil($count/10);
		if($pageNum > $pagetotal){
			exit;
		}
		$list2 = $forum->where( $where )->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		if(!empty($list2)){
			foreach($list2 as $k=>$v){
				$list2["$k"]["content"] = htmlspecialchars_decode($v['content'],ENT_QUOTES);
				$id = $v['id'];
				$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1 AND token = '$token'")->select();
				$v["cnum"] = count($comment);
				if($v['photos'] != ''){
					$v["photoArr"] = explode(',',$v['photos']);
				}
				$uinfo = $this->uinfo($v['uid'],$uid);
				$tpl .= '<div class="forumlist"><div class="forumtop"></div><div class="forumuser forum-ul"><img class="forumuserlogo " src="'.$uinfo['headimgurl'].'" onerror="'.$uinfo['hearimgurl'].'" onClick="window.location.href='."'".U('Forum/myMessage',array('token'=>$token,'openid'=>$openid))."'".'"><div class="forumusername " onClick="window.location.href='.U('Forum/myMessage',array('token'=>$token,'openid'=>$openid)).'">'.$uinfo['nickname'].'</div></div>';
		
				if($v['photoArr'] != NULL){
                    $photonum = count($v['photoArr']);
                    $tpl .='<figure data-count="'.$photonum.'张图片"><div>';

                    for($i=0;$i<$photonum;$i++){
                        $tpl .='<img src="/uploads/forum/'.$v['photoArr'][$i].'" data-src="/uploads/forum/'.$v['photoArr'][$i].'" data-gid="g7" onload="preViewImg(this, event);"/>';
                    }
                    $tpl .= '</div></figure>';
                }


				$tpl .= '<div onClick="window.location.href='."'".U('Forum/comment',array('token'=>$token,'openid'=>$openid,'tid'=>$id))."'".'"><div class="forumtitle">'.$v['title'].'</div><div class="forumtime">'.date('Y-m-d H:i:s',$v['createtime']).'</div><div class="forumcontent">'.$v['content'].'</div></div><div class="forumlistfoot forum-ul"><div class="forumhotbox myli myul " ><div class="forumhot  forumico"><div class="hothit" style="height: 3px; "><div id="redpoint"></div></div></div><div id="datacount">12°</div></div>';
											

				if(in_array($openid,explode(',',$v['likeid']))){
					$tpl .= '<div class="forumtagbox myli myul" onClick="collectTrends('.$id.",'".$openid."','".$token."'".')"><div class="forumtag"><span class="icon-heart" style="color:#d42020"></span></div>';
                }else{
					$tpl .= '<div class="forumtagbox myli myul" onClick="collectTrends('.$id.",'".$openid."','".$token."'".')"><div class="forumtag"><span class="icon-heart" style="color:#666"></span></div>';
                }

                if($v['likeid'] == NULL){
                    $tpl .= '<div id="datacount">0</div></div>';
                }else{
                    $tpl .= '<div id="datacount">'.count(explode(',',$list['likeid'])).'</div></div>';
                }

										
				$tpl .= '<div class="forumreplybox myli myul" ><div class="forumreply"><span class="icon-chat"></span></div><div id="datacount">'.$v['cnum'].'</div></div>';
										
                if(in_array($openid,explode(',',$v['favourid']))){
                    $tpl .= '<div class="forumlikebox myli myul" onClick="praiseTrends('.$id.",'".$openid."','".$token."'".')"><div class="forumlike"><span class="icon-thumbs-up"  style="color:#d42020"></span></div>';
                }else{
                    $tpl .= '<div class="forumlikebox myli myul" onClick="praiseTrends('.$id.",'".$openid."','".$token."'".')"><div class="forumlike"><span class="icon-thumbs-up"  style="color:#666"></span></div>';
                }

                if($v['favourid'] == NULL){

                    $tpl .= '<div id="datacount">0</label></div></div></div></div>';

                }else{
                    $tpl .= '<div id="datacount">'.count(explode(',',$list['favourid'])).'</label></div></div></div></div>';
                }
			}
				echo $tpl;
			}else{
				echo 0;
			}
	}
	
	//发表帖子页面
	public function add(){
		$token = $this->_get('token');
                $wxusers = M('Wxusers')->where(array('uid'=>$this->tpl['id'],'openid'=>$this->openid))->field('openid')->find();
		if(!$wxusers['openid']){
		   $this->error('您需要关注官方公众号才能进入');
		}

                $messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1 AND isread = 1")->count();
                $this->assign('messageNum',$messageNum);
		$this->display();
	
	}
	
	//发布新帖子
	public function checkAdd(){
		$data = array();
        $data['uid'] = $this->_post('openid');
		if($data['uid'] == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$data['title'] = $this->_post('title');
		$data['content'] = $this->_post('content');

		$openid = $data['uid'];
		$userinfo = M('Wxusers')->field('nickname')->where("openid = '$openid'")->find();
		$data['uname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		$data['token'] = $this->_post('token');
		$data['createtime'] = time();
		
		$token = $data['token'];
		
		$conf = M('Forum_config')->field('ischeck')->where("token = '$token'")->find();

		if($conf['ischeck'] == 1){
			$data['status'] = -1;
		}else{
			$data['status'] = 1;
		}
		
		$photos[] = $_POST['pic0'];
		$photos[] = $_POST['pic1'];
		$photos[] = $_POST['pic2'];
		$photos[] = $_POST['pic3'];
		$photos[] = $_POST['pic4'];
		$photos[] = $_POST['pic5'];
		$photos[] = $_POST['pic6'];
		$photos[] = $_POST['pic7'];
		foreach($photos as $k=>$v){
			if($v == ''){
				unset($photos[$k]);
			}
		}
		
		$data['photos'] = implode('|',$photos);

		//添加记录
		$forum = M('Forum_topics');
        if($forum->add($data)){
            if($conf['ischeck'] == 1){
                exit(json_encode(array('info'=>'等待管理员审核后您的帖子才可以显示','status'=>1,'url'=>U('Forum/myContent',array('openid'=>$data['uid'],'token'=>$data['token'])))));
            }else{
                exit(json_encode(array('info'=>'发帖成功','status'=>1,'url'=>U('Forum/index',array('openid'=>$data['uid'],'token'=>$data['token'])))));
            }
        }else{
            exit(json_encode(array('info'=>'发帖失败','status'=>0)));
        }
	}
	
	//喜欢
	public function likeAjax(){
		$uid = $this->_post('uid');
		
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$id = $this->_post('tid','intval');
		
		$info = M('Forum_topics')->field('likeid')->where("id = $id AND status = 1")->find();

		if($info['likeid'] == ''){
		    //如果还没有人点击喜欢
			$data['likeid'] = $this->_post('uid');
			$boo = M('Forum_topics')->where("id = $id")->setField($data);
			
		}else{
		    //取消喜欢
			$likeid = explode(',',$info['likeid']);
			if(in_array($this->_post('uid'),$likeid)){
			
				unset($likeid[array_search($this->_post('uid'),$likeid)]);
				$data['likeid'] = implode(',',$likeid);
				$boo = M('Forum_topics')->where("id = $id")->setField($data);
				
			}else{
		        //如果已经有人点击了喜欢，现在又有人点击喜欢
				$data['likeid'] = $info['likeid'].','.$this->_post('uid');
				$boo = M('Forum_topics')->where("id = $id")->setField($data);
			}
		}
		if($boo){
			exit(json_encode(array('info'=>'OK','status'=>1)));
		}else{
            exit(json_encode(array('info'=>'FAIL','status'=>0)));
		}

	}
	
	
	//赞
	public function favourAjax(){
		$uid = $this->_post('uid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$id = $this->_post('tid','intval');
		
		$info = M('Forum_topics')->field('favourid')->where("id = $id")->find();
		if($info['favourid'] == ''){
		    //如果没有人点过赞
			$data['favourid'] = $this->_post('uid');
			$boo = M('Forum_topics')->where("id = $id")->setField($data);
		}else{
		    //如果已经有人点过赞，现在取消点赞
			$favourid = explode(',',$info['favourid']);
			if(in_array($this->_post('uid'),$favourid)){
				unset($favourid[array_search($this->_post('uid'),$favourid)]);
				$data['favourid'] = implode(',',$favourid);
				$boo = M('Forum_topics')->where("id = $id")->setField($data);

			}else{
			    //点赞
				$data['favourid'] = $info['favourid'].','.$this->_post('uid');
				$boo = M('Forum_topics')->where("id = $id")->setField($data);
			}
		}

		if($boo){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	//帖子和评论详情页面
	public function comment(){

		$id = $this->_get('tid','intval');
		$token = $this->_get('token');
        $wx = M('Wxuser')->where(array('token'=>$this->token))->field('id')->find();
        $u = $wx['id'];
		$topics = M('Forum_topics')->where("id = $id AND status = 1 AND token = '$token'")->find();
		$topics['content'] = htmlspecialchars_decode($topics['content']);
		$openid = $this->_get('openid');

		//load comment

		$comment = M('Forum_comment')->order('createtime ASC')->where("tid = $id AND status = 1 AND replyid=''")->select();
        $sql = "select c.*,m.cid,m.touname from tp_forum_comment as c RIGHT JOIN tp_forum_message AS m ON c.id=m.id WHERE c.replyid!='' AND c.status=1";
        $a = M('Forum_comment')->query($sql);
		$cnum = M('Forum_comment')->order('createtime ASC')->where("tid = $id AND status = 1")->count();
		foreach($comment as $k=>$v){
			$comment["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$comment["$k"]["uinfo"] = $this->uinfo($v['uid'],$u);
			if($v['replyid'] != NULL){
				$reuid = $v['replyid'];
				$userinfo = M('Wxusers')->field('nickname')->where("openid = '$reuid'")->find();
				$comment[$k]['reuname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
			}
		}


        $messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$openid' AND status = 1 AND isread = 1")->count();
        $this->assign('messageNum',$messageNum);

		$this->assign('openid',$openid);
		$this->assign('cnum',$cnum);
		$this->assign('comment',$comment);
        $this->assign('a',$a);
		$this->assign('topics',$topics);
		$this->display();
	
	}

	//评论提交处理
	public function checkCommentAdd(){
		$data['uid'] = $this->_get('openid');
		if($data['uid'] == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$data['tid'] = $this->_post('tid','intval');
		$openid = $data['uid'];
		$userinfo = M('Wxusers')->field('nickname')->where("openid = '$openid'")->find();
		$data['uname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		$data['content'] = $this->_post('content');
		$data['token'] = $this->_post('token');
		$token = $this->_post('token');
		$data['createtime'] = time();
		$conf = M('Forum_config')->field('comcheck')->where("token = '$token'")->find();
		if($conf['comcheck'] == 1){
			$data['status'] = -1;
		}else{
			$data['status'] = 1;
		}
		$comment = M('Forum_comment');
        if($comment->add($data)){
            $tid = $data['tid'];
            $token = $data['token'];
            $uid = M('Forum_topics')->where("token = '$token' AND id = $tid AND status = 1")->field('uid')->find();
            if($conf['comcheck'] == 1){
                $message['content'] = '<a href="/index.php?g=Wap&m=Forum&a=comment&tid='.$data['tid'].'&openid='.$uid['uid'].'&token='.$data['token'].'">'.$data['uname'].'评论了您的帖子,该评论需要等待管理员审核后才能显示</a>';
            }else{
                $message['content'] = '<a href="/index.php?g=Wap&m=Forum&a=comment&tid='.$data['tid'].'&openid='.$uid['uid'].'&token='.$data['token'].'">'.$data['uname'].'评论了您的帖子</a>';
            }
            $message['createtime'] = time();
            $message['fromuid'] = $data['uid'];
            $message['token'] = $data['token'];
            $message['touid'] = $uid['uid'];
            $message['tid'] = $data['tid'];
            $message['cid'] = NULL;
            $message['fromuname'] = $data['uname'];
            $touid = $uid['uid'];
            $userinfo = M('Wxusers')->field('nickname')->where("openid = '$touid'")->find();
            $message['touname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
            M('Forum_message')->add($message);

            if($conf['comcheck'] == 1){
                exit(json_encode(array('info'=>'评论成功,等待被审核通过后才可以显示','status'=>1,'url'=>U("Wap/Forum/comment",array('tid'=>$data['tid'],'openid'=>$data['uid'],'token'=>$data['token'])))));
            }else{
                exit(json_encode(array('info'=>'评论成功','status'=>1,'url'=>U("Wap/Forum/comment",array('tid'=>$data['tid'],'openid'=>$data['uid'],'token'=>$data['token'])))));
            }
        }else{
            exit(json_encode(array('info'=>'评论失败','status'=>0)));
        }


	
	}
	
	//赞评论
	public function commentFavourAjax(){
		$uid = $this->_post('uid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$id = $this->_post('id','intval');
		$comment = M('Forum_comment');
		$fav = $comment->field('favourid')->where("id = $id")->find();
		if($fav['favourid'] == NULL){
            //如果没有人赞过。。。
			$boo = $comment->where("id = $id")->setField(array('favourid'=>$uid));
		}else{
            //取消赞
			$favArray = explode(',',$fav['favourid']);
			if(in_array($uid,$favArray)){
				unset($favArray[array_search($uid,$favArray)]);
				$res['favourid'] = implode(',',$favArray);
				$boo = $comment->where("id = $id")->setField($res);
			}else{
                //增加一个赞
				$boo = $comment->where("id = $id")->setField(array('favourid'=>$fav['favourid'].','.$uid));
			}
		}
		if($boo){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	
	//回复评论页面
	/*public function recomment(){
		$uid = $this->_get('openid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$uid = $this->_get('reid');
		
		$data = M('Forum_comment')->where("uid = '$uid'")->field('uname')->find();
		$uname = $data['uname'];
		$this->assign('uname',$uname);
		$this->display();
	}*/
	

	//回复评论提交处理
	public function checkRecomment(){

		$data['uid'] = $this->_post('openid');//用户openid
		if($data['uid'] == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$data['tid'] = $this->_post('tid','intval');//帖子id
		$data['replyid'] = $this->_post('reid');    //被回复者的openid
		$data['token'] = $this->_post('token');
		$token = $data['token'];
		$openid = $data['uid'];
		$userinfo = M('Wxusers')->field('nickname')->where("openid = '$openid'")->find();
		$data['uname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		$data['content'] = $this->_post('content');
		$data['createtime'] = time();
		$conf = M('Forum_config')->field('comcheck')->where("token = '$token'")->find();
		if($conf['comcheck'] == 1){
			$data['status'] = -1;
		}else{
			$data['status'] = 1;
		}
		$comment = M('Forum_comment');
        if($comment->add($data)){
            if($conf['comcheck'] == 1){
                $message['content'] = '<a href="/index.php?g=Wap&m=Forum&a=comment&tid='.$data['tid'].'&openid='.$data['replyid'].'&token='.$data['token'].'">'.$data['uname'].'回复了您的评论，该评论在管理员审核后才能显示</a>';
            }else{
                $message['content'] = '<a href="/index.php?g=Wap&m=Forum&a=comment&tid='.$data['tid'].'&openid='.$data['replyid'].'&token='.$data['token'].'">'.$data['uname'].'回复了您的评论。</a>';
            }
            $message['createtime'] = time();
            $message['fromuid'] = $data['uid'];
            $message['token'] = $data['token'];
            $message['touid'] = $data['replyid'];
            $message['tid'] = $data['tid'];
            $message['cid'] = $this->_post('cid','intval');
            $message['fromuname'] = $data['uname'];
            $touid = $message['touid'];
            $userinfo = M('Wxusers')->field('nickname')->where("openid = '$touid'")->find();
            $message['touname'] = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
            M('Forum_message')->add($message);
            if($conf['comcheck'] == 1){
                exit(json_encode(array('info'=>'等待管理员审核后您的评论才可以显示','status'=>1,'url'=>U('Forum/comment',array('openid'=>$data['uid'],'tid'=>$data['tid'],'token'=>$data['token'])))));
            }else{
                $this->ajaxReturn(array('info'=>'评论成功','status'=>1,'url'=>U('Forum/comment',array('openid'=>$data['uid'],'tid'=>$data['tid'],'token'=>$data['token']))));
            }
        }
	}
 
	//我发表的帖子页面
	public function myContent(){
        $uid = $this->_get('openid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$token = $this->_get('token');
		$userinfo = M('Wxusers')->field('nickname')->where("openid = '$uid'")->find();
		$uname = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		$list = M('Forum_topics')->order('createtime DESC')->where("uid = '$uid' AND status != 0 AND token = '$token'")->select();
		$mylikenum = M('Forum_topics')->field('id')->order('createtime DESC')->where("status = 1 AND token = '$token' AND likeid like '%$uid%'")->count();
		$mymessagenum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1")->count();
		$messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1 AND isread = 1")->count();
        foreach($list as $k=>$v){
			$list["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$id = $v['id'];
			$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1 AND token = '$token'")->select();
			$list["$k"]["cnum"] = count($comment);
		}
		$this->assign('mymessagenum',$mymessagenum);
		$this->assign('messageNum',$messageNum); 
		$this->assign('mylikenum',$mylikenum); 
		$this->assign('uname',$uname); 
		$this->assign('list',$list); 
		$this->display();
	}
	//其他用户页面
	public function otherUser(){
		$openid = $this->_get('openid');
		
		if($openid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$uid = $this->_get('uid');
		$token = $this->_get('token');
		$userinfo = M('Wxusers')->field('nickname,headimgurl')->where("openid = '$uid'")->find();
		$uname = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		if($userinfo['headimgurl'] == ''){
			$portrait = './tpl/static/star/img/face.png';
		}else{
			$portrait = $userinfo['headimgurl'];
		}
		$list = M('Forum_topics')->order('createtime DESC')->where("uid = '$uid' AND status = 1 AND token = '$token'")->select();
		$messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$openid' AND status = 1 AND isread = 1")->count();
		foreach($list as $k=>$v){
			$list["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$id = $v['id'];
			$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1 AND token = '$token'")->select();
			$list["$k"]["cnum"] = count($comment);
		}
		$this->assign('messageNum',$messageNum);
		$this->assign('openid',$openid);
		$this->assign('uname',$uname); 
		$this->assign('portrait',$portrait); 
		$this->assign('list',$list); 
		$this->display();
	}
	
	//我喜欢过的帖子页面
	public function myLike(){

		$uid = $this->_get('openid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$token = $this->_get('token');
		$list = M('Forum_topics')->order('createtime DESC')->where("status = 1 AND token = '$token' AND likeid like '%$uid%'")->select();
		$userinfo = M('Wxusers')->field('nickname')->where("openid = '$uid'")->find();
		$uname = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';
		$u = M('Wxuser')->field('id')->where(array('token'=>$token))->find();

		$mytopicsnum = M('Forum_topics')->field('id')->order('createtime DESC')->where("uid = '$uid' AND status = 1 AND token = '$token'")->count();
		$mymessagenum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1")->count();
		$messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$uid' AND status = 1 AND isread = 1")->count();
        $mylikenum = M('Forum_topics')->field('id')->order('createtime DESC')->where("status = 1 AND token = '$token' AND likeid like '%$uid%'")->count();
        foreach($list as $k=>$v){
			$list["$k"]["content"] = htmlspecialchars_decode($v['content']);
			$id = $v['id'];
			$comment = M('Forum_comment')->field('id')->where("tid = $id AND status = 1")->select();
			$list["$k"]["cnum"] = count($comment);

			$list["$k"]["uinfo"] = $this->uinfo($v['uid'],$u['id']);
		}
		$openid = $this->_get('openid');
		$this->assign('mytopicsnum',$mytopicsnum);
		$this->assign('mymessagenum',$mymessagenum);
		$this->assign('messageNum',$messageNum);
        $this->assign('mylikenum',$mylikenum);
		$this->assign('openid',$openid);
		$this->assign('list',$list); 
		$this->assign('uname',$uname); 
		$this->display();	
		
	}
	
	//我的消息页面
	public function myMessage(){
        $uid = $this->_get('openid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$token = $this->_get('token');
		$userinfo = M('Wxusers')->field('nickname')->where("openid = '$uid'")->find();
        $u = M('Wxuser')->field('id')->where(array('token'=>$token))->find();
		$uname = $userinfo['nickname'] ? $userinfo['nickname'] : '游客';

		$list = M('Forum_message')->order('createtime DESC')->where("touid = '$uid' AND token = '$token' AND status = 1")->select();

		foreach($list as $k=>$v){
			$list["$k"]['uinfo'] = $this->uinfo($v['fromuid'],$u['id']);
		}
		$mylikenum = M('Forum_topics')->field('id')->order('createtime DESC')->where("status = 1 AND token = '$token' AND likeid like '%$uid%'")->count();
		$mytopicsnum = M('Forum_topics')->field('id')->order('createtime DESC')->where("uid = '$uid' AND status = 1 AND token = '$token'")->count();

		M('Forum_message')->where("token = '$token' AND touid = '$uid' AND status = 1 AND isread = 1")->setField('isread',0);

        $messageNum = M('Forum_message')->field('id')->where("token = '$token' AND touid = '$openid' AND status = 1 AND isread = 1")->count();
        $this->assign('messageNum',$messageNum);

		$this->assign('list',$list);
		$this->assign('mylikenum',$mylikenum);
		$this->assign('uname',$uname);
		$this->assign('mytopicsnum',$mytopicsnum);
		$this->display();
	}

	//编辑我的帖子页面
	/*public function myContentEdit(){
		$wecha_id = $this->_get('wecha_id');	
		if($wecha_id == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$tid = $this->_get('tid','intval');
		$wecha_id = $this->_get('wecha_id');
		$token = $this->_get('token');
		$data = M('Forum_topics')->where("id = $tid AND token = '$token' AND uid = '$wecha_id'")->find();
		$data['photoArr'] = explode(',',$data['photos']);
		$data['content'] = htmlspecialchars_decode($data['content']);

		$this->assign('data',$data);
		$this->display();
	}*/
	
	
	//更新我的帖子提交处理
	/*public function myContentUpdate(){

		$data = array();
		$data['uid'] = $this->_post('wecha_id');
		if($data['uid'] == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$topics = M('Forum_topics');
		$data['title'] = $this->_post('title');
		$data['content'] = $this->_post('form_article');
		
		
		$wecha_id = $data['uid'];
		$userinfo = M('Userinfo')->field('wechaname')->where("wecha_id = '$wecha_id'")->find();
		$data['uname'] = $userinfo['wechaname'] ? $userinfo['wechaname'] : '游客';
		

		$data['token'] = $this->_post('token');
		$data['updatetime'] = time();
		
		$token = $data['token'];

		$tid = $this->_post('tid','intval');
		
		$tinfo = $topics->field('photos')->where("token = '$token' AND uid = '$wecha_id' AND status = 1 AND id = $tid")->find();

		$photos[] = $_POST['pics1'];
		$photos[] = $_POST['pics2'];
		$photos[] = $_POST['pics3'];
		$photos[] = $_POST['pics4'];
		$photos[] = $_POST['pics5'];
		$photos[] = $_POST['pics6'];
		$photos[] = $_POST['pics7'];
		$photos[] = $_POST['pics8'];
		

		foreach($photos as $k=>$v){
		
			if($v == ''){
				unset($photos[$k]);
			}
		}
		

		$data['photos'] = implode(',',$photos);

		if($topics->create()){
		
			if($topics->where("id = $tid AND token = '$token' AND uid = '$wecha_id' AND status = 1")->setField($data)){
				$this->redirect(U("Forum/myContent",array('wecha_id'=>$data['uid'],'token'=>$data['token'])));
			}
		}else{
			$this->error('系统错误');
		}
		
	}*/
	
	/*//删除帖子
	public function delTopics(){
		$uid = $this->_post('wecha_id');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		
		$id = $this->_post('tid','intval');
		
		$token = $this->_post('token');
		
		if(M('Forum_topics')->where("id = $id AND token = '$token' AND uid = '$uid' AND status = 1")->setField('status',0)){
				echo 1;
		}else{	
				echo 0;
		}
	}*/
	
	//删除评论
	public function delComment(){
		$uid = $this->_get('openid');
		if($uid == ''){
			$this->error('您需要关注官方公众号才能进入');
		}
		$cid = $this->_post('cid','intval');
		$token = $this->_get('token');
		if(M('Forum_comment')->where("id = $cid AND token = '$token' AND uid = '$uid' AND status = 1")->setField('status',0)){
			exit(json_encode(array('info'=>'删除成功','status'=>1)));
		}else{
            exit(json_encode(array('info'=>'删除失败','status'=>0)));
		}
	}
	
	//获取头像
	
	protected function uinfo($wid='',$to=''){
		$uinfo = M('Wxusers')->field('headimgurl,nickname')->where("openid = '$wid' AND uid = $to")->find();
		if($uinfo['hearimgurl'] == ''){
			$uinfo['hearimgurl'] = './tpl/static/star/img/face.png';
		}
		return $uinfo;
	}


    //资讯
    public function newsList(){
        $token = $this->_get('token');
        $uid = $this->_get('openid');
        if($uid == ''){
            $this->error('您需要关注官方公众号才能进入');
        }
        $configInfo = M('Forum_config')->where(array('token'=>$token,'is_open'=>1))->field('token,openid')->find();
        if($configInfo['token'] == $token){
            $count = M('Forum_topics')->order('createtime DESC')->where(array('token'=>$token,'uid'=>$configInfo['openid'],'status'=>1))->count();
            if($count){
                $Page = new Page($count,10);
                $show = $Page->show();
                $topics = M('Forum_topics')->order('createtime DESC')->where(array('token'=>$token,'uid'=>$configInfo['openid'],'status'=>1))->limit($Page->firstRow.','.$Page->listRows)->select();
                foreach($topics as $k => $v){
                    $topics[$k]['cnum'] = M('Forum_comment')->order('createtime ASC')->where(array('tid'=>$v['id'],'status'=>1))->count();
                }
                $this->assign('topics',$topics);
                $this->assign('page',$show);
            }
        }else{
            exit("非法操作");
        }
        $this->display();
    }

    public function a(){
        $this->display();
    }



	
	

}