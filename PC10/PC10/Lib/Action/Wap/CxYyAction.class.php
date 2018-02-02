<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 前端
 */
class CxYyAction extends CxToAction{

	public $_sTplBaseDir = 'Wap/default/cx';
	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();



	}

	// 首页显示，店铺发送
	public function index(){
		//if(!$_SESSION['sbm']) script("请登陆!","long",get(openid));
		$time = strtotime($_GET['time']);
		$time = $time ? $time : time();//$time当前选定时间
		$a = -86400;//86400是一天的时间秒
		$aDate[0]=date('Y-m-d',$time-86400);//当前选定时间的前一天
        $aWeek[] = $this->getWeek($aDate[0]);
		for ($i=0; $i<30; $i++){
			$a+=86400;
			$aDate[] = $sDate=date('Y-m-d',$time+$a);
            $aWeek[] = $this->getWeek($sDate);
		} //取得2天的时间
		$this->assign('aDate',$aDate);
        $this->assign('aWeek',$aWeek);
	    //查出1楼会议室
 		$lc1=M('cx_lc')->where(array('lc'=>1,'token'=>$_SESSION['wtoken']))->select();
 		if($_GET['type']){
	 	    foreach ($lc1 as $keo=>$vo){
	 	    	$name=$vo['name'];
	 	    	//查出空闲会议室
	 	    	$time=strtotime($_GET['time']);
	 	    	$time=date("Y-m-d",$time);
	 	    	$w=strtotime($_GET['time']);
	 	    	$w=date("w",$w);
	 	    	$yd2=(array)M('cx_yd2')->where(array('token'=>TO,'lc'=>1,'name'=>$name,'time'=>array('like',$time.'%')))->field('time')->select();
	 	    	$yd=(array)M('cx_yd')->where(array('token'=>TO,'lc'=>1,'name'=>$name,'time'=>array('like',$w.','.'%')))->field('time')->select();
	 	    	foreach ($yd2 as $ke=>$v){
	 	    		$yd2[$ke]['time1']=substr($v['time'],strpos($v['time'],',')+1);
	 	    		unset($yd2[$ke]['time']);
	 	    	}
	 	    	foreach ($yd as $ke=>$v){
	 	    		$yd[$ke]['time1']=substr($v['time'],strpos($v['time'],',')+1);
	 	    		unset($yd[$ke]['time']);
	 	    	}
	 	    	$yds=array_merge($yd2,$yd);
	 	    	if(count($yds)>=25){
	 	    		unset($lc1[$keo]);
	 	    	}
	 	    }
 		}
 		//P($lc1);
	    $this->assign('lc1',$lc1);
	    //查出2楼会议室
	    $lc2=M('cx_lc')->where(array('lc'=>2,'token'=>$_SESSION['wtoken']))->select();


	    if($_GET['type']){
	    	foreach ($lc2 as $keo=>$vo){
	    		$name=$vo['name'];
	    		//查出空闲会议室
	    		$time=strtotime($_GET['time']);
	    		$time=date("Y-m-d",$time);
	    		$w=strtotime($_GET['time']);
	    		$w=date("w",$w);
	    		$yd2=(array)M('cx_yd2')->where(array('token'=>TO,'lc'=>2,'name'=>$name,'time'=>array('like',$time.'%')))->field('time')->select();
	    		$yd=(array)M('cx_yd')->where(array('token'=>TO,'lc'=>2,'name'=>$name,'time'=>array('like',$w.','.'%')))->field('time')->select();
	    		foreach ($yd2 as $ke=>$v){
	    			$yd2[$ke]['time1']=substr($v['time'],strpos($v['time'],',')+1);
	    			unset($yd2[$ke]['time']);
	    		}
	    		foreach ($yd as $ke=>$v){
	    			$yd[$ke]['time1']=substr($v['time'],strpos($v['time'],',')+1);
	    			unset($yd[$ke]['time']);
	    		}
	    		$yds=array_merge($yd2,$yd);
	    		if(count($yds)>=25){
	    			unset($lc2[$keo]);
	    		}
	    	}
	    }


	    $this->assign('lc2',$lc2);
	    //查出3楼会议室
	    $lc3=M('cx_lc')->where(array('lc'=>3,'token'=>$_SESSION['wtoken']))->select();


	    if($_GET['type']){
	    	foreach ($lc3 as $keo=>$vo){
	    		$name=$vo['name'];
	    		//查出空闲会议室
	    		$time=strtotime($_GET['time']);
	    		$time=date("Y-m-d",$time);
	    		$w=strtotime($_GET['time']);
	    		$w=date("w",$w);
	    		$yd2=(array)M('cx_yd2')->where(array('token'=>TO,'lc'=>3,'name'=>$name,'time'=>array('like',$time.'%')))->field('time')->select();
	    		$yd=(array)M('cx_yd')->where(array('token'=>TO,'lc'=>3,'name'=>$name,'time'=>array('like',$w.','.'%')))->field('time')->select();
	    		foreach ($yd2 as $ke=>$v){
	    			$yd2[$ke]['time1']=substr($v['time'],strpos($v['time'],',')+1);
	    			unset($yd2[$ke]['time']);
	    		}
	    		foreach ($yd as $ke=>$v){
	    			$yd[$ke]['time1']=substr($v['time'],strpos($v['time'],',')+1);
	    			unset($yd[$ke]['time']);
	    		}
	    		$yds=array_merge($yd2,$yd);
	    		if(count($yds)>=25){
	    			unset($lc3[$keo]);
	    		}
	    	}
	    }


	    $this->assign('lc3',$lc3);
        //P($lc1);





	   $this->UDisplay();

	}


	public function show(){
		if(!$_SESSION['sbm']) script("请登陆!","long",get(openid));
		$time = strtotime($_GET['time']);
		$time = $time ? $time : time();//$time当前选定时间
		$a = -86400;//86400是一天的时间秒
		$aDate[0]=date('Y-m-d',$time-86400);//当前选定时间的前一天
		for ($i=0; $i<2; $i++){
			$a+=86400;
			$aDate[] = date('Y-m-d',$time+$a);
		} //取得2天的时间
		$this->assign('aDate',$aDate);
		//查出会议室
        $hys=M('cx_lc')->select();
        foreach ($hys as $ke=>$v){
            $hys[$v['name']]=$v;//把名称当健，如果有重复的名称自动会替换
        	unset($hys[$ke]);//删除原来的
        }
        $this->assign('hys',$hys);
        //查出预定表时间段的预定成员
        $time = $_GET['time'];
        $lc = $_GET['lc'];
        $name = $_GET['name'];
        $token=$_SESSION['wtoken'];
        $list=M('cx_yd2')->query("select * from tp_cx_yd2 where time like '%$time%' AND lc=$lc AND name='$name' AND token='$token'");
        //重组 将2维数组转为1维数组, 2维数组里的time做为1维数组里的键, 2维数组里的zhi做为1维数组里的值,

        $list=Arr::changeIndexToKVMap($list, 'time', 'zhi');
        $this->assign('list',$list);
        //P($list);
        //查出会员id
        $zhi=M('cx_member')->where(array('sbm'=>'CX6111433835470'))->getField('id');
        $this->assign('zhi',$zhi);
		$this->UDisplay();
	}
	public function yd(){

		//查出会员id
		$zhi=M('cx_member')->where(array('sbm'=>$_SESSION['sbm']))->getField('id');
		$time=$_GET['time'];
		//查出pid
		$_GET['pid']=M('Cx_lc')->where(get1(token,TO,lc,name))->getField('id');
		$_GET['x_time']=strtotime($_GET['time']);
		$_GET['j_time']=$_GET['i'];
		$_GET['p_time']=strtotime($_GET['time']);
		if(!$_GET['pid']) script("楼层或会议室不存在");

          $id=explode(',',$_GET['i']);
     //  p($id);
       // p($time);die;

        //echo $time;die;
        foreach($id as $val){
            M('cx_yd2')->add(array(
                'zhi'=> $zhi,
                'time'=>$time.','.$val,
                'pid'=>$_GET['pid'],
                'token'=> TO,
                'add_time'=>time(),
                'lc'=>$_GET['lc'],
                'name'=>$_GET['name'],
                'x_time'=>$_GET['x_time'],
                'j_time'=>$val,
                'p_time'=>$_GET['p_time'],
                'type'=>$_GET['type'],
            ));
        }

		//if(M('cx_yd2')->add(get4(zhi,$zhi,time,$time,token,TO,add_time,time(),lc,name,pid,x_time,j_time,p_time,type))){
			script("","CxYy/show2",get(time,lc,name));
		/*}else{
			script("预定失败");
		}*/

	}

	public function show2(){

        if (IS_AJAX) {
            //p($_POST);

            if(M('Cx_yd2')->where(array(
                'time'  => $_POST['time'],
                'name'  => $_POST['name'],
                'token' => TO
            ))->delete()){
                exit(json_encode(array('status' =>0)));
            }else{
               // echo 4;die;
               exit(json_encode(array('status' =>1)));
            }
        }
        
		if(!$_SESSION['sbm']) script("请登陆!","long",get(openid));
		$time = strtotime($_GET['time']);
		$time = $time ? $time : time();//$time当前选定时间
		$a = -86400;//86400是一天的时间秒
		$aDate[0]=date('Y-m-d',$time-86400);//当前选定时间的前一天
        $aWeek[] = $this->getWeek($aDate[0]);
		for ($i=0; $i<30; $i++){
            $a+=86400;
            $aDate[] = $sDate = date('Y-m-d',$time+$a);
            $aWeek[] = $this->getWeek($sDate);
        } //取得2天的时间
		$this->assign('aDate',$aDate);
        $this->assign('aWeek',$aWeek);
		//查出会议室
		$hys=M('cx_lc')->select();
		foreach ($hys as $ke=>$v){
			$hys[$v['name']]=$v;//把名称当健，如果有重复的名称自动会替换
			unset($hys[$ke]);//删除原来的
		}
		$this->assign('hys',$hys);
		//查出预定表时间段的预定成员
		$time = $_GET['time'];
		$lc = $_GET['lc'];
		$name = $_GET['name'];
		$token=$_SESSION['wtoken'];
		$list=M('cx_yd2')->query("select * from tp_cx_yd2 where time like '%$time%' AND lc=$lc AND name='$name' AND token='$token'");
		//重组 将2维数组转为1维数组, 2维数组里的time做为1维数组里的键, 2维数组里的zhi做为1维数组里的值,
		$list=Arr::changeIndexToKVMap($list, 'time', 'zhi');
		$this->assign('list',$list);
		//P($list);
		//查出会员id
		$zhi=M('cx_member')->where(array('sbm'=>$_SESSION['sbm']))->getField('id');
		$this->assign('zhi',$zhi);

		//查出固定预约
		$gd=M('cx_yd')->where(get1(token,TO,lc,name))->select();
		$gd=Arr::changeIndexToKVMap($gd, 'time', 'zhi');

		$this->assign('gd',$gd);
		$this->UDisplay();
	}

    function getWeek($sDate){
        $iweek = date('N', strtotime($sDate)-86400);
        switch($iweek){
            case 7:
                return '周日';
                break;
            case 1:
                return '周一';
                 break;
            case 2:
                return '周二';
                break;
            case 3:
                return '周三';
                break;
            case 4:
                return '周四';
                break;
            case 5:
                return '周五';
                break;
            case 6:
                return '周六';
                break;
        }
    }

	public function aaa(){

        //p($_GET);die;

		//echo '<script>if(confirm("你确定要预定吗")){location.href="'.U('yd',get(i,time,name,lc)).'"}else{history.back();};</script>';

		//die;
		$this->UDisplay();
	}


	public function long(){
		if(IS_POST){
			if(!$_POST['sbm']) script("识别码输入为空");
			$list=M('cx_sbm')->where(array('token'=>TO,'sbm'=>$_POST['sbm']))->find();
			if(!$list) script("识别码错误");
			$member=M('cx_member')->where(array('openid'=>OP))->find();
			if(!$member) script("你还没有注册",'zc');
			$_SESSION['sbm']=$member['sbm'];
			$_GET['time']=date("Y-m-d",time());
			script("登陆成功","index",get(openid,time));

		}else{
			$_GET['time']=date("Y-m-d",time());
 			if($_SESSION['sbm']) script("",'index',get(openid,time));
 			
 			$list=M('cx_member')->where(array('openid'=>OP))->find();
 			$this->assign('list',$list);
			$this->UDisplay();
		}

	}

	public function ajax(){
		if(IS_AJAX){
			$time=$_POST['time'];
			$w=date("w",strtotime($_POST['time']));

			$yd2=(array)M('cx_yd2')->where(array('token'=>TO,'lc'=>$_POST['lc'],'name'=>$_POST['name'],'time'=>array('like',$time.'%')))->select();
	 	    $yd=(array)M('cx_yd')->where(array('token'=>TO,'lc'=>$_POST['lc'],'name'=>$_POST['name'],'time'=>array('like',$w.','.'%')))->select();
	 	    $yds=array_merge($yd2,$yd);

	 	    $str='';
	 	    foreach ($yds as $v){
	 	    	$add_time=date("Y年m月d日h时i分",$v['add_time']);
	 	    	$list=M('cx_member')->where(array('id'=>$v['zhi']))->find();
	 	    	$list=$list?$list['bm'].'-'.$list['name']:$v['zhi'];
$str.=<<<str
           <div>{$list}预定了{$_POST['lc']}楼{$_POST['name']}</div>
str;
	 	    }
	 	    $data['str']=$str;
	 	    $this->ajaxReturn($data);
		}
	}

	public function zc(){
		if(IS_POST){
			$list=M('cx_member')->where(array('openid'=>OP))->find();
			if($list){
				M('cx_member')->where(array('openid'=>OP))->save($_POST);
				script("资料修改成功","long");
			}else{
			   $sbm="CX".mt_rand(1,1000).time();
			   M('cx_member')->add(array(
						'token'=>TO,
						'openid'=>OP,
						'name'=>$_POST['name'],
						'sex'=>$_POST['sex'],
						'bm'=>$_POST['bm'],
						'add_time'=>time(),
			   		    'sbm'=>$sbm
				));
				script("注册成功","long");
			}

		}else{


			$list=M('cx_member')->where(array('openid'=>OP))->find();
			$this->assign('list',$list);
			$this->UDisplay();
		}

	}

	public function ajax2(){
		if(IS_AJAX){
			$type=M('cx_yd2')->where(array('token'=>TO,'time'=>$_POST['time']))->getField('type');
			$res['type']=$type;
			$this->ajaxReturn($res);
		}
	}

}
?>
