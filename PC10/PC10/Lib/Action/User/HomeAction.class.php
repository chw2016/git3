<?php
class HomeAction extends UserAction{

    const HOME_BASE = 1;
    /*
    public function _initialize() {
		parent::_initialize();
		$token_open=M('token_open')->field('queryname')->where(array('token'=>session('token')))->find();
		if(!strpos($token_open['queryname'],'shouye')){
            	$this->error('您未开启该模块的使用权,请到功能模块中添加',U('Function/index',array('token'=>session('token'),'id'=>session('wxid'))));
		}

	}
    */
    public function index(){
      //  p(send_email('您好', '您 好', '317030876@qq.com'));
        $token = $this->_get('token');
        if(isset($token)){
            $res = M('Wxuser')->where(array('token'=>$token))->find();
            if($res){
                $this->assign('user',$res);
            }
        }else{
            $res = M('Wxuser')->where(array('token'=>session('token')))->find();
            if($res){
                $this->assign('user',$res);
            }
        }


        $fans = M('Wxusers')->where(array('uid'=>$res['id'],'status'=>1))->count();
        $where['uid']=$res['id'];
        $where['status']=1;
        $time = strtotime(date("Y-m-d")." 00:00:00");
        $where['add_time']=array('between',array($time,$time+3600*24));
        $todayfans = M('Wxusers')->where($where)->count();
        $msgwhere['add_time'] = array('between',array($time,$time+3600*24));
        $msgwhere['uid']=$res['id'];
        $msgs = M('Msg_list')->where($msgwhere)->count();
        $fans = $res['wxfans']+$fans;

        $adminuser = M('users')->field('viptime,is_dz,id')->where(array('id'=>$res['uid']))->find();


        $year=date("Y",time());
        $data = date("d",time());
        if(empty($month)){
            $month=date("m",time());
        }
        $db=D('Requestdata');
        $vwhere['token']=session('token');
        $vwhere['month']=$month;
        $vwhere['year']=$year;
        $list=$db->where($vwhere)->find();
        $viewcounts = $list['3g']+$list['textnum']+$list['imgnum']+$list['videonum']+$list['videonum'];
        $this->assign('viewcounts',$viewcounts);

        // $ArchivesModel = M('Archives','dede_','mysql://root:wapwei!@#$%09876@localhost/wapweiindex');
        // $articledata = $ArchivesModel->field('id,title,pubdate')->where(array('typeid'=>10))->order('pubdate desc')->limit(10)->select();

	$this->assign('fans',$fans);
        $this->assign('articledata',$articledata);
        $this->assign('adminuser',$adminuser);
        $this->assign('todayfans',$todayfans);
        $this->assign('msgs',$msgs);
        $this->assign('hover1',1);
        $this->display();
    }


	public function set(){
		$home=M('Home')->where(array('token'=>session('token')))->find();
		if(IS_POST){
			if($home==false){
				$this->all_insert('Home','/set');
			}else{
				$_POST['id']=$home['id'];
				$this->all_save('Home','/set');
			}
		}else{
			$this->assign('home',$home);
            $this->assign('hover1',1);
			$this->display();
		}
	}

    public function base_set()
    {
        if (IS_POST) {
            $check = $this->_post('ifBG');
            $check = !empty($check) ? $check : "0";
            $bottom_txt = $this->_post('bottom_txt');
            $aData = array(
                'bg'         => $this->_post('bg'),
                'bottom_txt' => $bottom_txt,
                'ifbg' => $check
            );
            if(M('Guangwang_base_set')->where(array(
                'token' => $this->_post('token'),
                'type'  => self::HOME_BASE,
            ))->find()){
                if(M('Guangwang_base_set')->where(array(
                    'token' => $this->_post('token'),
                    'type'  => self::HOME_BASE,
                ))->data(array('value' => json_encode($aData)))->save()){
                    $this->success2('恭喜设置成功', U('Home/base_set', array('token' => $this->_post('token'))));
                    exit;
                }else{
                    $this->error2('设置失败');
                    exit;
                };
            }else{
                if(M('Guangwang_base_set')->data(array(
                    'token' => $this->_post('token'),
                    'type'  => self::HOME_BASE,
                    'value' => json_encode($aData)
                ))->add()){
                    $this->success2('恭喜设置成功', U('Home/base_set', array('token' => $this->_post('token'))));
                    exit;
                }else{
                    $this->error('设置失败');
                    exit;
                };
            };
        }

        $aValue = M('Guangwang_base_set')->where(array(
            'token' => $this->token,
            'type'  => self::HOME_BASE,
        ))->find();
        $aValue = $aValue ? json_decode($aValue['value'],true) : array();
        $this->assign('value', $aValue);
        $this->display();
    }
}
?>
