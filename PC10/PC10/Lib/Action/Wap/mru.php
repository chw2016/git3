<?php 
//分享给自己+积分
/* if($_GET['dopenid']){
	$url=M('mru_url')->where(array('openid'=>$_GET['openid'],'dopenid'=>$_GET['dopenid'],'url'=>$_SERVER['REQUEST_URI']))->find();
	if (!$url){
		$url=M('mru_url')->add(array('openid'=>$_GET['openid'],'dopenid'=>$_GET['dopenid'],'url'=>$_SERVER['REQUEST_URI']));
		//积分+10
		if(!M('mru_jfb')->where(array('openid'=>$_GET['dopenid'],'token'=>$_GET['token']))->setInc('num',10)){
			$jfb=M('mru_jfb')->add(array('openid'=>$_GET['dopenid'],'token'=>$_GET['token'],'num'=>10));
		}
	}
} */
//好友给他+积分
//include"./Lib/Action/Wap/mru.php";
if($_GET['dopenid']&&$_GET['hy']){
	$b=M('mru_hy')->where(array('token'=>$_GET['token'],'dopenid'=>$_GET['dopenid']))->find();
	if(!$b){
		M('mru_hy')->add(array('token'=>$_GET['token'],'dopenid'=>$_GET['dopenid'],'openid'=>$_GET['openid']));
		//积分+10
		if(!M('mru_jfb')->where(array('openid'=>$_GET['dopenid'],'token'=>$_GET['token']))->setInc('num',10)){
			$jfb=M('mru_jfb')->add(array('openid'=>$_GET['dopenid'],'token'=>$_GET['token'],'num'=>10));
		}
	}
}

if(IS_AJAX){
	$urlfx=M('mru_url')->where(array('openid'=>$_GET['openid'],'token'=>$_GET['token'],'url'=>$_SERVER['REQUEST_URI']))->find();

	if (!$urlfx){
		//积分+10
        //这里判断大于100积分就不加了
        $tj['token']=$_GET['token'];
        $tj['openid']=$_GET['openid'];
        $tj['add_time']=array('between',array(
            strtotime(date('Y-m-d',time())),
            strtotime(date("Y-m-d",strtotime("+1 day")))
        ));
        $count=M('mru_xf')->where($tj)->count();
        if($count>=10){
            $res['str']="每天最高得100积分";
        }else {
            $ooo = M('mru_jfb')->where(array('openid' => $_GET['openid'], 'token' => $_GET['token']))->setInc('num', 10);
            if ($ooo) {
                $urlfx2 = M('mru_url')->add(array('openid' => $_GET['openid'], 'token' => $_GET['token'], 'url' => $_SERVER['REQUEST_URI']));
                //这里判断大于100积分就不加了
                /*$tj['token']=$_GET['token'];
                $tj['openid']=$_GET['openid'];
                $tj['add_time']=array('between',array(
                    strtotime(date('Y-m-d',time())),
                    strtotime(date("Y-m-d",strtotime("+1 day")))
                ));
                $count=M('mru_xf')->where($tj)->count();
                if($count>=10){
                    $res['str']="每天最高得100积分";
                }else{*/
                $res['str'] = "分享朋友圈积分+10";
                //获取积分红包记录
                M('mru_xf')->add(array(
                    'token' => $_GET['token'],
                    'openid' => $_GET['openid'],
                    'num' => 10,
                    'fs' => '首次分享朋友圈',
                    'add_time' => time(),
                ));
                //    }


            } else {
                $res['str'] = "需要先注册会员才能分享朋友圈+积分";
            }
        }
	}
	$this->ajaxReturn($res);
}

?>