<?php

class RequeryAction extends UserAction{
	public function index(){
		$month=$this->_get('month');
		$year=date("Y",time());
		if(empty($month)){
        	$month=date("m",time());
         }
		$db=D('Requestdata');
		$where['token']=session('token');
		$where['month']=$month;
		$where['year']=$year;
		$list=$db->where($where)->select();
		$this->assign('list',$list);
		$this->assign('month',$month);
		$this->display();
	}
	public function msline(){
		header('Content-type: text/xml; charset=gb2312');
		$month=$this->_get('month');
		$year=date("Y",time());
		if(empty($month)){
        	$month=date("m",time());
         }
		$title=$year."-".$month."月数据统计曲线";
		$getdate = strtotime($year."-".$month);
		$t = date("t",$getdate);
		$db=D('Requestdata');
		$where['token']=session('token');
		$where['month']=$month;
		$where['year']=$year;
		$list=$db->where($where)->limit(0,$t)->select();
				
        $data_xml .= "<chart caption='".$title."' >\r\n";
		$data_xml .= "<categories>\r\n";
		for ($i = 1; $i <=$t; $i++)
    	{
			$data_xml .= "<category label='".$i."日' />\r\n";	
		}
		$data_xml .= "</categories>\r\n";
		$data_xml .= "<dataset  seriesName='3G网站浏览量'  >\r\n";
		for ($i = 1; $i <=$t; $i++)
    	{
		    $gcount=M('Requestdata')->field('3g')->where('token="'.session('token').'" and month='.$month.' and year='.$year.' and day='.$i)->find();
                    $gc=isset($gcount['3g'])?$gcount['3g']:0;
			$data_xml .= "<set value='".$gc."' />\r\n";
		}
		$data_xml .= "</dataset>\r\n";
		$data_xml .= "<dataset  seriesName='文本请求数'  >\r\n";
		for ($i = 1; $i <=$t; $i++)
    	{
		    $tcount=M('Requestdata')->field('textnum')->where('token="'.session('token').'" and month='.$month.' and year='.$year.' and day='.$i)->find();
			$tc=isset($tcount['textnum'])?$tcount['textnum']:0;
			$data_xml .= "<set value='".$tc."' />\r\n";
		}
		$data_xml .= "</dataset>\r\n";
		$data_xml .= "<dataset  seriesName='图文请求数'  >\r\n";
		for ($i = 1; $i <=$t; $i++)
    	{
		    $icount=M('Requestdata')->field('imgnum')->where('token="'.session('token').'" and month='.$month.' and year='.$year.' and day='.$i)->find();
			$ic=isset($icount['imgnum'])?$icount['imgnum']:0;
			$data_xml .= "<set value='".$ic."' />\r\n";
		}
		$data_xml .= "</dataset>\r\n";
		$data_xml .= "<dataset  seriesName='语音请求数'  >\r\n";
		for ($i = 1; $i <=$t; $i++)
    	{
		    $vcount=M('Requestdata')->field('videonum')->where('token="'.session('token').'" and month='.$month.' and year='.$year.' and day='.$i)->find();
			$vc=isset($vcount['videonum'])?$vcount['videonum']:0;
			$data_xml .= "<set value='".$vc."' />\r\n";
		}
		$data_xml .= "</dataset>\r\n";
		$data_xml .= "<dataset  seriesName='营销电商请求'  >\r\n";
		for ($i = 1; $i <=$t; $i++)
    	{
		    $ocount=M('Requestdata')->field('other')->where('token="'.session('token').'" and month='.$month.' and year='.$year.' and day='.$i)->find();
			$oc=isset($ocount['other'])?$ocount['other']:0;
			$data_xml .= "<set value='".$oc."' />\r\n";
		}
		$data_xml .= "</dataset>\r\n";
		$data_xml .= "<dataset  seriesName='关注数'  >\r\n";
		for ($i = 1; $i <=$t; $i++)
    	{
		    $fcount=M('Requestdata')->field('follownum')->where('token="'.session('token').'" and month='.$month.' and year='.$year.' and day='.$i)->find();
			$fc=isset($fcount['follownum'])?$fcount['follownum']:0;
			$data_xml .= "<set value='".$fc."' />\r\n";
		}
		$data_xml .= "</dataset>\r\n";
		$data_xml .= "<dataset  seriesName='取消关注数'  >\r\n";
		for ($i = 1; $i <=$t; $i++)
    	{
		    $ucount=M('Requestdata')->field('unfollownum')->where('token="'.session('token').'" and month='.$month.' and year='.$year.' and day='.$i)->find();
			$uc=isset($ucount['unfollownum'])?$ucount['unfollownum']:0;
			$data_xml .= "<set value='".$uc."' />\r\n";
		}	
		$data_xml .= "</dataset>\r\n";	
		$data_xml .= "</chart>\r\n";	
		echo $data_xml;
	}
}
?>