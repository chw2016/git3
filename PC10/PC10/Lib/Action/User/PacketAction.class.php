<?php
class PacketAction extends UserAction{
	public function index(){
		$token=$this->token;
		$count = M("lottery")->where(array("token" => $token, "type" => 10))->count();
		$page = new Page($count, 20);
		$data = M("lottery")->field('id,click,title,keyword,statdate,enddate,status')->where(array("token" => $token, "type" => 10))->limit($page->firstRow . ',' . $page->listRows)->select();
		foreach($data as $k=>$v){
			$id=$v['id'];
			$count=M("wxlottery")->where(array("lid"=>$id))->count();
			$data[$k]['join']=$count;
		}
		// echo 123;
		$this->assign("page", $page->show());
		$this->assign("data", $data);
		$this->display();
	}

	//增加与修改合并
	public function action(){
		$act=$this->_get("act");
		if(IS_POST){
			$data = $_POST;
			$data['statdate'] = strtotime($data['statdate']);
			$data['enddate'] = strtotime($data['enddate']);
			if($act=="add"){
				//添加
				$this->add($data);
			}else{
				//修改时更新数据
				$this->edit($data);;
			}
		}else{
			if($act=="add"){
				//增加
				$this->display("add");
			}else{
				//修改
				$id=$this->_get("id");
				$data = M("lottery")->field('id,title,keyword,statdate,enddate,status,info,txt,sttxt,starpicurl')->where(array("id" => $id))->find();
				$this->assign("data", $data);
				$this->display("edit");
			}
		}
	}

	public function add($data){
		$data['type'] = 10;
		$data['password']=md5($data['password']);
		$data['token'] = $this->token;
		if ($data['statdate'] > $data['enddate']) {
			$this->error2('起始时间不能大于结束时间');
			return false;
		}
		if (M("lottery")->create() != false) {
			if (M("lottery")->add($data)) {
				$this->success2("活动创建成功", U("Packet/index", array("token" => $data['token'])));
			} else {
				$this->error2("创建失败");
			}
		} else {
			$this->error2('服务器繁忙,请稍候再试');
		}
	}

	public function edit($data){
		if ($data['statdate'] > $data['enddate']) {
			$this->error2('起始时间不能大于结束时间');
			return false;
		}
		if (M("lottery")->create() != false) {
			if (M("lottery")->where(array("id" => $data['id']))->save($data)) {
				$this->success2("活动修改成功", U("Packet/index", array("token" =>  $this->token)));
			} else {
				$this->error2('修改失败');
			}
		} else {
			$this->error2('服务器繁忙,请稍候再试');
		}
	}

	public function del(){
		$id = $this->_get("id"); //活动id
		if (M("lottery")->where(array("id" => $id))->delete()) {
			M("wxlottery")->where(array("lid" => $id))->delete();//删除记录用户信息表信息
			M("wxget")->where(array("lid" => $id))->delete();//删除中奖表信息
			M("wxgift")->where(array("id" => $id))->delete();//删除活动奖品表信息
			$this->success2("活动删除成功", U("Packet/index", array("token" => $this->token)));
		} else {
			$this->error2("活动删除失败", U("Packet/index", array("token" => $this->token)));
		}
	}

	//兑换奖品密码重置
	public function reset(){
		$id=$this->_get("id");
		if(IS_POST){
			$info=M("lottery")->field("password")->where(array("id"=>$id))->find();
			if($info['password']!=md5($_POST['old'])){
				$this->error2("原始密码不正确");
				return false;
			}
			$data['password']=md5($_POST['new']);
			if(M("lottery")->where(array("id"=>$id))->save($data)){
				$this->success2("密码修改成功", U("Packet/index", array("token" => $this->token)));
			}else{
				$this->error2("密码修改失败");
			}
		}else{
			$this->display();
		}
	}


	//活动开始修改状态
	public function start() {
		$id = $this->_get("id", "intval");
		if (M("lottery")->where(array("id" => $id))->save(array("status" => 1))) {
			$this->success2("活动开始", U("Packet/index", array("token" => $this->token)));
		}
	}

	//活动结束修改状态
	public function end() {
		$id = $this->_get("id", "intval");
		if (M("lottery")->where(array("id" => $id))->save(array("status" => 2))) {
			$this->success2("活动已经结束",  U("Packet/index", array("token" => $this->token)));
		}
	}

	public function sn(){
		$id=$this->_get("id","intval");//活动id
		$tel=$this->_get("tel");
		$sn=$this->_get("sn");
		if($sn && $tel){
			$sql="select count(g.id) from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$id} and l.lid={$id} and l.tel ={$tel} and g.sn='{$sn}'";
			$count=M("wxget")->query($sql);
			$count=$count[0]['count(g.id)'];//数据总量
			$page=new Page($count,20);
			$dsql="select u.nickname,f.gname,f.level,g.status,l.tel,g.sn,g.id,g.gtime from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$id} and l.lid={$id} and l.tel !={$tel} and g.sn='{$sn}' limit {$page->firstRow},{$page->listRows} ";
			$data=M("wxget")->query($dsql);
		}else if($sn && !$tel){
			$sql="select count(g.id) from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$id} and l.lid={$id} and l.tel !='' and g.sn='{$sn}'";
			$count=M("wxget")->query($sql);
			$count=$count[0]['count(g.id)'];//数据总量
			$page=new Page($count,20);
			$dsql="select u.nickname,f.gname,f.level,g.status,l.tel,g.sn,g.id,g.gtime from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$id} and l.lid={$id} and l.tel !='' and g.sn='{$sn}' limit {$page->firstRow},{$page->listRows} ";
			$data=M("wxget")->query($dsql);
		}else if(!$sn && $tel){
			$sql="select count(g.id) from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$id} and l.lid={$id} and l.tel ={$tel}";
			$count=M("wxget")->query($sql);
			$count=$count[0]['count(g.id)'];//数据总量
			$page=new Page($count,20);
			$dsql="select u.nickname,f.gname,f.level,g.status,l.tel,g.sn,g.id,g.gtime from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$id} and l.lid={$id} and l.tel ={$tel} limit {$page->firstRow},{$page->listRows} ";
			$data=M("wxget")->query($dsql);
		}else{
			$sql="select count(g.id) from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$id} and l.lid={$id} and l.tel !=''";
			$count=M("wxget")->query($sql);
			$count=$count[0]['count(g.id)'];//数据总量
			$page=new Page($count,20);
			$dsql="select u.nickname,f.gname,f.level,g.status,l.tel,g.sn,g.id,g.gtime from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$id} and l.lid={$id} and l.tel !='' limit {$page->firstRow},{$page->listRows} ";
			$data=M("wxget")->query($dsql);
		}
		$this->assign("lid",$id);//活动id
		$this->assign("data",$data);
		$this->assign("count",$count);
		$this->assign("page",$page->show());
		$this->display();
	}

	public function rank(){
		$lid=$this->_get("id");//活动id
		$sql2="select count(*) from tp_wxlottery as w join tp_wxusers as u on w.uid=u.openid where w.lid={$lid} and w.tel !=''";
		$count=M("wxlottery")->query($sql2);
		$count=$count[0]['count(*)'];
		$page=new Page($count,20);
		$sql="select u.nickname,w.id,w.tel,w.integrity from tp_wxlottery as w join tp_wxusers as u on w.uid=u.openid where w.lid={$lid} and w.tel !='' order by w.integrity desc limit {$page->firstRow},{$page->listRows}";
		$userInfo=M("wxlottery")->query($sql);
//		$userInfo=M("wxlottery")->field("tel,integrity,uid")->where(array("lid"=>$lid))->order("integrity desc")->limit($page->firstRow . ',' . $page->listRows)->select();//参与活动用户信息
		$this->assign("lid",$lid);
		$this->assign("count",$count);
		$this->assign("page",$page->show());
		$this->assign("users",$userInfo);
		$this->display();
	}

	//排行榜修改
	public function editRank(){
		if(IS_POST){
			$id=$this->_get("id");//wxlottery表id
			$data['integrity']=$_POST['integrity'];
			if(M("wxlottery")->where(array("id"=>$id))->save($data)){
				$this->success2("修改成功",U("Packet/rank",array("id"=>$_POST['lid'])));
			}
		}else{
			$id=$this->_get("id");
			$lid=$this->_get("lid","intval");
			$sql="select u.nickname,w.id,w.tel,w.integrity from tp_wxlottery as w join tp_wxusers as u on w.uid=u.openid where w.id={$id}";
			$userInfo=M("wxlottery")->query($sql);
			$this->assign("lid",$lid);
			$this->assign("data",$userInfo);
			$this->display();
		}
	}


	public function exchange(){
		if(IS_POST){
			$data=M("lottery")->field("password")->where(array("id"=>$_POST['lid']))->find();
			if(md5($_POST['password'])===$data['password']){
				if(M("wxget")->where(array("id"=>$_POST['gid']))->save(array("status"=>1))){
					$this->success2("兑换成功",U("Packet/sn",array("id"=>$_POST['lid'])));
				}else{
					$this->error2("兑换失败");
				}
			}else{
				$this->error2("密码错误");
			}
		}else{
			$lid=$this->_get("lid","intval");
			$gid=$this->_get("gid","intval");
			$this->assign("lid",$lid);
			$this->assign("gid",$gid);
			$this->display();
		}
	}
	//导出excel表数据
	public function exportSn(){
		vendor("PHPExcel.PHPExcel");
		vendor("PHPExcel.PHPExcel.IOFactory");
		$lid=$this->_get("lid");
		$phpexcel=new PHPExcel();
		$phpexcel->getActiveSheet()->setCellValue('A1', '序号');
		$phpexcel->getActiveSheet()->setCellValue('B1', 'SN码');
		$phpexcel->getActiveSheet()->setCellValue('C1', '奖品名称');
		$phpexcel->getActiveSheet()->setCellValue('D1', '手机号');
		$phpexcel->getActiveSheet()->setCellValue('E1', '微信昵称');
		$phpexcel->getActiveSheet()->setCellValue('F1', '中奖时间');
		$phpexcel->getActiveSheet()->setCellValue('G1', '状态');
		$dsql="select u.nickname,f.gname,f.level,g.status,l.tel,g.sn,g.id,g.gtime from tp_wxget as g join tp_wxgift as f on g.gid=f.id join tp_wxusers as u on g.uid=u.openid join tp_wxlottery as l on g.uid=l.uid where g.lid={$lid} and l.lid={$lid} and l.tel !=''";
		$data=M("wxget")->query($dsql);//excel表数据
		$i=2;
		foreach($data as $v){
			$phpexcel->getActiveSheet()->setCellValue('A'.$i,$i-1);
			$phpexcel->getActiveSheet()->setCellValue('B'.$i, $v['sn']);
			$phpexcel->getActiveSheet()->setCellValue('C'.$i, $v['gname']);
			$phpexcel->getActiveSheet()->setCellValue('D'.$i, $v['tel']);
			$phpexcel->getActiveSheet()->setCellValue('E'.$i, $v['nickname']);
			$phpexcel->getActiveSheet()->setCellValue('F'.$i, $v['gtime']);
			if($v['status']==0){
				$phpexcel->getActiveSheet()->setCellValue('G'.$i,"未领取");
			}else{
				$phpexcel->getActiveSheet()->setCellValue('G'.$i,"已领取");
			}
			$i++;
		  }
		$obj = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
		$filename = 'excel.xls';
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$obj->save('php://output');
	}


	public function exportRank(){
		vendor("PHPExcel.PHPExcel");
		vendor("PHPExcel.PHPExcel.IOFactory");
		$lid=$this->_get("lid");
		$phpexcel=new PHPExcel();
		$phpexcel->getActiveSheet()->setCellValue('A1', '序号');
		$phpexcel->getActiveSheet()->setCellValue('B1', '微信昵称');
		$phpexcel->getActiveSheet()->setCellValue('C1', '手机号');
		$phpexcel->getActiveSheet()->setCellValue('D1', '节操数');
		$sql="select u.nickname,w.id,w.tel,w.integrity from tp_wxlottery as w join tp_wxusers as u on w.uid=u.openid where w.lid={$lid} and w.tel !='' order by w.integrity desc";
		$data=M("Wxlottery")->query($sql);//excel表数据
		$i=2;
		foreach($data as $v){
			$phpexcel->getActiveSheet()->setCellValue('A'.$i,$i-1);
			$phpexcel->getActiveSheet()->setCellValue('B'.$i, $v['nickname']);
			$phpexcel->getActiveSheet()->setCellValue('C'.$i, $v['tel']);
			$phpexcel->getActiveSheet()->setCellValue('D'.$i, $v['integrity']);
			$i++;
		}
		$obj = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
		$filename = 'rank.xls';
		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$obj->save('php://output');
	}

}