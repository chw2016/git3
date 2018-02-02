<?php
/**
 * Created by PhpStorm.
 * User: 肖国平
 * tel:15889394741
 * Date: 2014/11/18
 * Time: 16:14
 */
class HealthMemberAction extends BaseAction{
	public $_openid;
	public $token;
	public function _initialize() {
		parent::_initialize();
		$this->_openid=$this->_get("openid");
		$this->token=$this->_get("token");
//        if(!M("Wxusers")->where(array("openid"=>$this->_openid,"uid"=>$this->tpl['id']))->find()){
//            header("Location:".C("site_url").'Home/Nofind/isnotsub/token/'.$this->token);
//        }
//        if(!M(Wxusers)->where(array("openid"=>$this->_openid))->find() || !M("Wxuser")->where(array("token"=>$this->token))->find()){
//            header("Location:".C("site_url").'Home/Nofind/isnotsub/token/'.$this->token);
//        }

        //判断用户是否绑定,没有绑定，登陆绑定页面
//        if(!M("sj_khb",NULL,"mysql://weixintjxx:weixintjxx@120.197.114.138:3306/db_yuepuwangcom")->where(array("khid"=>12222222))->find()){
//            $this->display("bind");exit;
//        }
        $this->assign("tpl",$this->tpl);
		$this->assign("openid",$this->_openid);
		$this->assign("token",$this->token);
	}

    //会员首页
    //mysql://root@localhost:3306/health
    public function index(){
        if(!M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array('openid'=>$this->_openid))->find()){
            $this->display("bind");
            exit;
        }
        $this->display();
    }

    //通过vip卡登陆查询
    public function VipLogin(){
        $this->display();
    }
    //vip卡查询信息
    public function Vip(){
        header("content-type:text/html;charset=utf-8");
        $url="http://report.dyjk.cn:8080/wsdl/IVIPService";
//        import("ORG.SoapClient");
        $client = new SoapClient($url);
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $result = $client->getVIPInfo($this->_get("vip"),$this->_get("uname"),$this->_get("tel"));//获取
	$xml = (array)simplexml_load_string($result);
        $json  = json_encode($xml);
        $data=json_decode($json,true);
        $wxuser=M("Wxusers")->field("headimgurl")->where(array("openid"=>$this->openid))->find();
        if(isset($data['vipinfo'])){
            $vipinfo=$data['vipinfo']['@attributes'];
            $this->assign("vipinfo",$vipinfo);
        }

        if(isset($data['costList']['item'])){
            $costlist=$data['costList']['item'];
            $this->assign("costlist",$costlist);
        }

        if(isset($data['rightList']['rightItem'])){
            $rightlist=$data['rightList']['rightItem'];
            $this->assign("rightlist",$rightlist);
        }
        $this->assign("wxuser",$wxuser);
        $this->display();
    }

    //微信账号与第一健康账号绑定
    //mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health
    public function bind(){
        $loginid=intval($_POST['loginid']);
        $passwd=strtoupper(md5($_POST['passwd']));
        if(M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("loginid"=>$loginid,"passwd"=>$passwd))->find()){
            if(M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("loginid"=>$loginid,"passwd"=>$passwd))->save(array("openid"=>$this->_openid))){
                $this->ajaxReturn(array("status"=>1,"info"=>"登陆成功"));
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>"登陆失败"));
            }
        }else{
            $this->ajaxReturn(array("status"=>0,"info"=>"账号或者密码错误"));
        }
    }
    
    public function unbind(){
        if(M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("openid"=>$this->_openid))->find()){
            if(M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("openid"=>$this->_openid))->save(array("openid"=>''))){
                $this->redirect(array("Wap/HealthMember/index",array('token'=>$this->token,'openid'=>$this->openid)));$this->ajaxReturn(array("status"=>1,"info"=>"解除成功"));
            }else{
                $this->redirect(U("Wap/HealthMember/userInfo",array('token'=>$this->token,'openid'=>$this->openid)));
            }
        }else{
            $this->redirect(U("Wap/HealthMember/userInfo",array('token'=>$this->token,'openid'=>$this->openid)));
        }
    }

    //对比第一健康数据，获取相应的用户信息,消费记录等信息,排序方式为desc
    public function userInfo(){
        if(!M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array('openid'=>$this->_openid))->find()){
            $this->display("bind");
            exit;
        }
        $img=M("Wxusers")->field("headimgurl")->where(array("openid"=>$this->_openid))->find();
        $userInfo=M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->field("khid,xm,je")->where(array("openid"=>$this->_openid))->find();
        $ulist=M("sj_xfjl",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("khid"=>$userInfo['khid']))->order("xfsj desc")->select();
        $this->assign("userInfo",$userInfo);
        $this->assign("ulist",$ulist);
        $this->assign("img",$img);
	$this->assign("limit_money",$ulist[count($ulist)-1]);
        $this->display();
    }



    //根据用户的体检id 获取对应的各项指标列表记录,按照时间排序，显示前五条记录
    public function quota(){
        if(!M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array('openid'=>$this->_openid))->find()){
            $this->display("bind");
            exit;
        }
        $sql="select tjid from sj_tjdj where khid=(select khid from sj_khb where openid='{$this->_openid}') order by tjsj DESC limit 5";
        $data=M("sj_tjdj",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->query($sql);
        $userInfo=M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->field("xm,xb,age,jiguan")->where(array("openid"=>$this->_openid))->find();
        $quota=array();
        foreach($data as $k=>$v){
            $q=M("sj_zhibiaos",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->field("mc,ckfw,v,v1,r")->where(array("tjid"=>$v['id']))->select();
            $quota[]=$q;
        }
        $fq=array();
        foreach($quota as $k=>$v){
            foreach($v as $key=>$val){
                $fq[$key]['mc'] = $quota[$k][$key]['mc'];
                $fq[$key]['ckfw'] = $quota[0][$key]['ckfw'];
                $fq[$key]['v'.$k] = $quota[$k][$key]['v'];
                $fq[$key]['r'.$k] = $quota[$k][$key]['r'];
                $fq[$key]['b'.$k] = $quota[$k][$key]['v1'];
            }
        }
        foreach($fq as $k=>$v){
            if($v['r0']=="N"){
                if(intval($v['v0'])>=intval($v['b0'])){
                    $fq[$k]['type0']="U";
                }else{
                    $fq[$k]['type0']="D";
                }
            }else{
                $fq[$k]['type0']=$v['r0'];
            }

            if($v['r1']=="N"){
                if(intval($v['v1'])>=intval($v['b1'])){
                    $fq[$k]['type1']="U";
                }else{
                    $fq[$k]['type1']="D";
                }
            }else{
                $fq[$k]['type1']=$v['r1'];
            }

            if($v['r2']=="N"){
                if(intval($v['v2'])>=intval($v['b2'])){
                    $fq[$k]['type']="U";
                }else{
                    $fq[$k]['type']="D";
                }
            }else{
                $fq[$k]['type2']=$v['r2'];
            }

            if($v['r3']=="N"){
                if(intval($v['v3'])>=intval($v['b3'])){
                    $fq[$k]['type3']="U";
                }else{
                    $fq[$k]['type3']="D";
                }
            }else{
                $fq[$k]['type3']=$v['r3'];
            }

            if($v['r4']=="N"){
                if(intval($v['v4'])>=intval($v['b4'])){
                    $fq[$k]['type4']="U";
                }else{
                    $fq[$k]['type4']="D";
                }
            }else{
                $fq[$k]['type4']=$v['r4'];
            }
        }

//        echo "<pre>";
//        print_r($fq);exit;
        $this->assign("user",$userInfo);
        $this->assign("fq",$fq);
        $this->display();
    }

    //根据会员id查询会员体检报告,显示体检汇总列表
    public function reports(){
        if(!M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array('openid'=>$this->_openid))->find()){
            $this->display("bind");
            exit;
        }
        $userInfo=M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->field("khid,xm,xb,age,jiguan")->where(array("openid"=>$this->_openid))->find();

	$reports=M("sj_tjdj",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("khid"=>$userInfo['khid']))->select();
	$this->assign("user",$userInfo);
        $this->assign("reports",$reports);
        $this->display();
    }

    //根据体检汇总id，查看体检详情信息
    public function report(){
        if(!M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array('openid'=>$this->_openid))->find()){
            $this->display("bind");
            exit;
        }
        $tjid = $this->_get("id","intval");
        $tjid = 126852;
        $userInfo=M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->field("khid,xm,xb,age,jiguan")->where(array("openid"=>$this->_openid))->find();
        $report=M("sj_tjjg",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("tjid"=>$tjid))->select();
        $sj_tjjgmodel = M("sj_tjjg",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health");
        $reportgroup=$sj_tjjgmodel->where(array("tjid"=>$tjid))->group('wpmc')->select();
        foreach($reportgroup as $keys=>$vals){
            $temp = null;
            $temp = $sj_tjjgmodel->where(array('tjid'=>$tjid,'enwpmc'=>$vals['enwpmc']))->select();
            $reportgroup[$keys]['detailgroup'] = $temp;
        }
        $zongjianreport=M("sj_tjhz",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array("tjid"=>$tjid))->order('sort asc')->select();
        $sj_tjjg1model = M("sj_tjjg1",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health");
        foreach($report as $key=>$val){
            $temp = null;
            $temp = $sj_tjjg1model->where(array('tjid'=>$tjid,'jgid'=>$val['jgid']))->select();
            $report[$key]['detaildata'] = $temp;
        }
        $this->assign("user",$userInfo);
        $this->assign("report",$report);
        $this->assign("reportgroup",$reportgroup);
        $this->assign("zongjianreport",$zongjianreport);
        $this->display();
    }

    //会员活动列表
    public function active(){
        $list= M("Img")->field("id,text,pic,title,createtime")->where(array("classid"=>2493))->select();
        if(!M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array('openid'=>$this->_openid))->find()){
            $this->display("bind");
            exit;
        }
        $this->assign("list",$list);
        $this->display();
    }

    //会员活动详情
    public function adetail(){
        $data=M("Img")->field("title,pic,text,info,createtime")->where(array("id"=>$this->_get("id")))->find();
        $data['info']=htmlspecialchars_decode($data['info']);
        if(!M("sj_khb",NULL,"mysql://root:d5HKkJ1238GrDjw599@112.124.62.6:3306/health")->where(array('openid'=>$this->_openid))->find()){
            $this->display("bind");
            exit;
        }
        $this->assign("data",$data);
        $this->display();
    }

}
