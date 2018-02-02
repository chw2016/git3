<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-8-28
 * Time: 下午5:55
 * To change this template use File | Settings | File Templates.
 */
class UsercenterAction extends UserAction{


    public function _initialize() {
        parent::_initialize();

        $this->token=session('token');
        $this->assign('token',$this->token);
        //权限
        if ($this->token!=$_REQUEST['token']){
            //exit();
        }else{
            $usercenter_model = M('Usercenter_set');
            $usercenterdata = $usercenter_model->where(array('token'=>$this->token))->find();
            $this->assign('usercenterdata',$usercenterdata);
        }
        
        

    }


    public function index(){
        $this->assign('hoverhover',1);
        $this->display();
    }

    public function userlevel(){
        $userScoremodel = M('Usercenter_level');
        $data = $userScoremodel->where(array('token'=>$this->token))->select();
        $this->assign('hover',6);
        $this->assign('data',$data);
        $this->display();
    }

    public function adduserlevel(){
        $this->assign('hover',6);
        $this->display();
    }

    public function edituserlevel(){
        if(IS_POST){
            $data = array();
            $data['name'] = $_POST['name'];
            $data['level_type'] = $_POST['level_type'];
            $data['score'] = $_POST['score'];
            $where['id'] = $_POST['id'];
            $where['token'] = $this->token;
            if(M('Usercenter_level')->where($where)->save($data)){
                $this->success('操作成功', U(MODULE_NAME . '/userlevel'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/adduserlevel'),true);
            }
        }else{
            $where['id']=$this->_get('id','intval');
            $where['token']=$this->token;
            $data = D('Usercenter_level')->where($where)->find();
            $this->assign('hover',6);
            $this->assign('data',$data);
            $this->display();
        }
    }

    public function insertuserlevel(){
        if(IS_POST){
            $data = array();
            $data['name'] = $_POST['name'];
            $data['level_type'] = $_POST['level_type'];
            $data['score'] = $_POST['score'];
            $data['token'] = $this->token;
            $userScoremodel = M('Usercenter_level');
            if($userScoremodel->add($data)){
                $this->success('操作成功', U(MODULE_NAME . '/userlevel'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/adduserlevel'),true);
            }
        }
    }

    public function deluserlevel(){
        $where['id']=$this->_get('id','intval');
        $where['token']=$this->token;
        if(D('Usercenter_level')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/userlevel'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/userlevel'));
        }
    }

    public function uprefixset(){
        if(IS_POST){
            $usercenter_model = M('Usercenter_set');
            $data = array();
            $id = $_POST['id'];
            if(!$_POST['is_openphone']){
                $data['is_openphone'] = 0;
            }else{
                $sms_config_model=M('config_sms');
                $where=array('token'=>session('token'));
                $check=$sms_config_model->where($where)->find();
                if(!$check){
                    $this->error('您还没配置万普手机短信验证码系统,请先配置', U(MODULE_NAME . '/memberman'),true);
                }else{
                    if($check['status'] == 0){
                        $this->error('您的手机短信验证码系统还未开启', U(MODULE_NAME . '/memberman'),true);
                    }
                }
                $data['is_openphone'] = 1;
            }
            $data['u_prefix'] = $_POST['u_prefix'];
            if($id){
                if($usercenter_model->where(array('token'=>$this->token,'id'=>$id))->save($data)){
                    $this->success('操作成功', U(MODULE_NAME . '/memberman'),true);
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/memberman'),true);
                }
            }
        }
    }

    public function infoset(){
        if(IS_POST){
            $data = array();
            $data['id'] = $_POST['id'];
            $data['title'] = $_POST['title'];
            $data['keywords'] = $_POST['keywords'];
            $data['picurl'] = $_POST['picurl'];
            $data['address'] = $_POST['address'];
            $data['tel'] = $_POST['tel'];
            $data['position_x'] = $_POST['position_x'];
            $data['position_y'] = $_POST['position_y'];
            $data['token'] = $this->token;
            $usercenter_model = M('Usercenter_set');
            if($data['id']){
                $usercenter_model->data(array('token'=>$this->token))->save($data);
                $wdata['pid']    = $data['id'];
                $wdata['module'] = 'Usercenter';
                $wdata['token']  = $this->token;
                $kdata['keyword']  = $data['keywords'] ;
                if( M('Keyword')->where($wdata)->save($kdata)){
                    $this->success('操作成功', U(MODULE_NAME . '/index'),true);
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/index'),true);
                }
            }else{
                $data['add_time'] = time();
                $lastid = $usercenter_model->data($data)->add();
                $kdata['pid']     = $lastid;
                $kdata['module']  = 'Usercenter';
                $kdata['token']   = $this->token;
                $kdata['keyword'] = $data['keywords'];
                if(M('Keyword')->add($kdata)){
                    $this->success('操作成功', U(MODULE_NAME . '/index'));
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/index'));
                }
            }
        }
    }

    /*
     * 会员积分使用规则
     */
    public function update_member_score_guize(){
        if(IS_POST){
            $data = array();
            $data['member_score_guize'] = $_POST['member_score_guize'];
            $usercenter_model = M('Usercenter_set');
            if( $usercenter_model->where(array('token'=>$this->token))->save($data)){
                $this->success('操作成功', U(MODULE_NAME . '/index'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/index'),true);
            }
        }
    }


    public function memberman(){
        $where=array(); $sWhere = '';
        $sHaving = '';
        if($_POST['score']){
            /*
            $iscore = array('gt',$_POST['score']);
            $alevel= M("Usercenter_level")->where(array('token'=>$this->token,'score'=>$iscore))->order('score asc')->limit(1)->select();
            if($alevel){
                //$where['tp_usercenter_memberlist.score'] = array(array('EGT',$_POST['score']),array('ELT',$alevel[0]['score']));
                $sHaving = "sum(tp_usercenter_score_record.score) >=".$_POST['score']." AND sum(tp_usercenter_score_record.score) <=".$alevel[0]['score'];
            }else{
                //$where['tp_usercenter_memberlist.score'] = array('EGT',$_POST['score']);
                $sHaving = "sum(tp_usercenter_score_record.score) >=".$_POST['score'];
            }
            $this->assign('score',$_POST['score']);
            */
        }

        if($_POST['member_sn']){
            $where['tp_usercenter_memberlist.member_sn'] = array('like','%'.$_POST['member_sn'].'%');
            $sWhere .= " AND tp_usercenter_memberlist.member_sn like '%".$_POST['member_sn']."%'";
            $this->assign('member_sn',$_POST['member_sn']);
        }

		if($_POST['phone']){
            $where['tp_usercenter_memberlist.phone'] = $_POST['phone'];
            $sWhere .= ' AND tp_usercenter_memberlist.phone = '.$_POST['phone'];
            $this->assign('phone',$_POST['phone']);
        }

        if($_POST['name']){
            $where['tp_usercenter_memberlist.name'] = array('like','%'.$_POST['name'].'%');
            $sWhere .= " AND tp_usercenter_memberlist.name like '%".$_POST['name']."%'";
            $this->assign('name',$_POST['name']);
        }

        //return;


        $usersModel = M('Usercenter_memberlist');
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();

        $where['tp_usercenter_memberlist.uid'] = $wxuser['id'];
        $sWhere .= ' AND tp_usercenter_memberlist.uid = '.$wxuser['id'];

        $userlevel = M("Usercenter_level")->where(array('token'=>$this->token))->order('score desc')->select();
        //$count      = $usersModel->where(array('uid'=>$wxuser['id'],'token'=>$this->token))->count();// 查询满足要求的总记录数
        $sWhere = trim($sWhere, ' AND');
        $sWhereData = $sWhere;
        /*
        $aWhereData['tp_usercenter_score_record.type'] = array('not in', '99');
        $aWhereData['tp_usercenter_score_record.token']   = $wxuser['token'];
        $aWhereData['tp_usercenter_score_record.score']   = array('gt', 0);
        */


        //$aWhereData['tp_usercenter_score_record.type'] = array('not in', '99');
        //$sWhereData .= ' AND (tp_usercenter_score_record.type not in (99) or tp_usercenter_score_record.type is null)';
        //$aWhereData['tp_usercenter_score_record.token']   = $wxuser['token'];
        //$sWhereData .= " AND (tp_usercenter_score_record.token='".$wxuser['token']."' or tp_usercenter_score_record.token is null)";
        //$aWhereData['tp_usercenter_score_record.score']   = array('gt', 0);
        //$aWhereData['(tp_usercenter_score_record.score > 0 or tp_usercenter_score_record.score is null) and 1']   = 1;

        /*
        $count =  count($usersModel->field('sum(tp_usercenter_score_record.score) as sscore, tp_wxusers.headimgurl,tp_usercenter_memberlist.*')
            ->join('left join tp_wxusers on tp_wxusers.openid = tp_usercenter_memberlist.openid')
            ->order('tp_usercenter_memberlist.update_time desc')
            ->join('tp_usercenter_score_record on tp_usercenter_memberlist.openid = tp_usercenter_score_record.openid')
            ->group('tp_usercenter_memberlist.openid')
            ->having($sHaving)
            ->where($sWhereData)
            ->select());

        */
        /*
        $count =  $usersModel
            ->field('sum(tp_usercenter_score_record.score) as sscore, tp_wxusers.headimgurl,tp_usercenter_memberlist.*')
            ->join('left join tp_wxusers on tp_wxusers.openid = tp_usercenter_memberlist.openid')
            ->order('tp_usercenter_memberlist.update_time desc')
            ->join('tp_usercenter_score_record on tp_usercenter_memberlist.openid = tp_usercenter_score_record.openid')
            ->group('tp_usercenter_memberlist.openid')
            ->having($sHaving)
            ->where($where)
            ->count();
        */
        $count =  $usersModel
            ->field('tp_wxusers.headimgurl,tp_usercenter_memberlist.*')
            ->join('left join tp_wxusers on tp_wxusers.openid = tp_usercenter_memberlist.openid')
            ->order('tp_usercenter_memberlist.update_time desc')
            //->group('tp_usercenter_memberlist.openid')
            ->where($where)
            ->count();
        //echo $userModel->getLastSql();

        $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        /*
        $list =  $usersModel->field('sum(tp_usercenter_score_record.score) as sscore, tp_wxusers.headimgurl,tp_usercenter_memberlist.*')
            ->join('left join tp_wxusers on tp_wxusers.openid = tp_usercenter_memberlist.openid')
            ->order('tp_usercenter_memberlist.update_time desc')
            ->join('tp_usercenter_score_record on tp_usercenter_memberlist.openid = tp_usercenter_score_record.openid')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->group('tp_usercenter_memberlist.openid')
            ->having($sHaving)
            ->where($where)
            ->select();
        */
        $list =  $usersModel->field('tp_wxusers.headimgurl,tp_usercenter_memberlist.*')
            ->join('left join tp_wxusers on tp_wxusers.openid = tp_usercenter_memberlist.openid')
            ->order('tp_usercenter_memberlist.update_time desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->group('tp_usercenter_memberlist.openid')
            ->where($where)
            ->select();

        $list2 =  Arr::changeIndexToKVMap($usersModel->field('DISTINCT(tp_wxusers.fakeid) as fakeid, tp_wxusers.openid')
            ->join('left join tp_wxusers on tp_wxusers.openid = tp_usercenter_memberlist.openid')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->where($where)
            ->select(), 'openid', 'fakeid');

        foreach($list as $k=>$v){
            $list[$k]['fakeid'] = Arr::get($list2, $v['openid']);
            $swhere['token']=$this->token;

            $zuserscore = M('Usercenter_score_record')
                ->field('sum(score) as allscore')
                ->where(array(
                    'token'=>$this->token,
                    'openid'=>$v['openid'],
                    'score'=>array('gt',0),
                    'type'=>array('not in','99')
                ))->select();
            $fscore = intval(M('Usercenter_score_record')
                ->where(array(
                    'token'=>$this->token,
                    'openid'=>$v['openid'],
                    'type'=>99
                ))->sum('score'));
            $userscore = $zuserscore[0]['allscore'] + $fscore;

            $swhere['score']=array('elt',$userscore);
            $userlevel_data = M("Usercenter_level")->where($swhere)->order('score desc')->limit(1)->find();
            if($userlevel_data){
                $list[$k]['level_name'] =  $userlevel_data['name'];
            }else{
                $list[$k]['level_name'] =  '普通会员';
            }
            //这里把德亿宝社会积分加进来
            $list[$k]['score']=$v['score']+M('shop_users')->where(array('token'=>$this->token,'openid'=>$v['openid']))->getField('score');
        }
      //  p($list);
        //总会员
        $date = date("Y-m-d",time());
        $start_date = strtotime($date." 00:00:00");
        $end_date = strtotime($date." 23:59:59");
        $membercounts = $usersModel->where(array('uid'=>$this->tpl['id']))->count();
        $daymembercounts = $usersModel->where(array('uid'=>$this->tpl['id'],'update_time'=>array('between',array($start_date,$end_date))))->count();

        $scoreModel = M('Usercenter_score_record');
        $addscore = $scoreModel->field('sum(score) as addscore')->where(array('token'=>$this->token,'score'=>array('gt',0),'add_time'=>array('between',array($start_date,$end_date))))->select();
        $subscore = $scoreModel->field('sum(score) as subscore')->where(array('token'=>$this->token,'score'=>array('lt',0),'add_time'=>array('between',array($start_date,$end_date))))->select();

        $moneyModel = M('Usercenter_money_record');
        $addmoney = $moneyModel->field('sum(money) as addmoney')->where(array('token'=>$this->token,'money'=>array('gt',0),'add_time'=>array('between',array($start_date,$end_date)),'status'=>1))->select();
        $submoney = $moneyModel->field('sum(money) as submoney')->where(array('token'=>$this->token,'money'=>array('lt',0),'add_time'=>array('between',array($start_date,$end_date)),'status'=>1))->select();
        //p($list);
        $this->assign('membercounts',$membercounts);// 赋值数据集
        $this->assign('daymembercounts',$daymembercounts);// 赋值数据集
        $this->assign('addscore',$addscore);// 赋值数据集
        $this->assign('subscore',$subscore);// 赋值数据集
        $this->assign('addmoney',$addmoney);// 赋值数据集
        $this->assign('submoney',$submoney);// 赋值数据集
        $this->assign('list',$list);// 赋值数据集
        $this->assign('userlevel',$userlevel);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('hover',2);
        $this->display();
    }


    public function salecard(){
        $data = M('Usercenter_salecard')->field('tp_usercenter_level.name as qname,tp_usercenter_salecard.sale_money,tp_usercenter_salecard.id,tp_usercenter_salecard.start_date,tp_usercenter_salecard.end_date,tp_usercenter_salecard.name,tp_usercenter_salecard.numbers')->join("left join tp_usercenter_level on tp_usercenter_level.id=tp_usercenter_salecard.user_qun_id")
            ->where(array('tp_usercenter_salecard.token'=>$this->token))->select();

        foreach($data as $key=>$val){
            $temp1 = 0;
            $temp2 = 0;
            $temp1 = M('Usercenter_user_salecard')->where(array('sale_id'=>$val['id'],'status'=>0))->count();
            $temp2 = M('Usercenter_user_salecard')->where(array('sale_id'=>$val['id'],'status'=>1))->count();
            $data[$key]['gets'] = $temp1;
            $data[$key]['useds'] = $temp2;
        }

        $this->assign('hover',3);
        $this->assign('data',$data);
        $this->display();
    }


    /*
     * 添加优惠券
     */
    public function addsalecard(){
        $userScoremodel = M('Usercenter_level');
        $data = $userScoremodel->where(array('token'=>$this->token))->select();
        $this->assign('hover',3);
        $this->assign('level_data',$data);
        $this->display();
    }

    /*
     * 删除优惠券
     */
    public function delsalecard(){
        $where['id']=$this->_get('id','intval');
        $where['token']=$this->token;
        if(D('Usercenter_salecard')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/salecard'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/salecard'));
        }
    }


    /*
     * 优惠券用户
     */
    public function salecarduser(){
        $id = $_GET['id'];

        $where['tp_usercenter_user_salecard.sale_id'] = $id;
        if($_POST['sale_sn']){
            $where['tp_usercenter_user_salecard.sale_sn'] = array('like','%'.$_POST['sale_sn'].'%');
            $this->assign('sale_sn',$_POST['sale_sn']);
        }

        if($_POST['status'] == ''){
            $where['status'] = 0;
            $this->assign('status',0);
        }
        if($_POST['status'] == 1){
            $where['status'] = 1;
            $this->assign('status',1);
        }

        if($_POST['status'] == 2){
            $this->assign('status',2);
        }


        $usersaledata = M('Usercenter_user_salecard')->field('tp_usercenter_user_salecard.add_time,tp_usercenter_user_salecard.sale_sn,tp_usercenter_user_salecard.status,tp_usercenter_user_salecard.id,tp_usercenter_user_salecard.openid,tp_usercenter_user_salecard.status,tp_usercenter_memberlist.name,tp_usercenter_memberlist.member_sn')->join('left join tp_usercenter_memberlist on tp_usercenter_memberlist.openid=tp_usercenter_user_salecard.openid')->where($where)->select();
        $this->assign('usersaledata',$usersaledata);
        $this->assign('hover',3);
        $this->display();
    }


    public function editsalecard(){
        if(IS_POST){
            $data = array();
            $data['name'] = $_POST['name'];
            $where['id'] = $_POST['id'];
            $where['token'] = $this->token;
            $data['start_date'] = $_POST['start_date'];
            $data['end_date'] = $_POST['end_date'];
            $data['sale_money'] = $_POST['sale_money'];
            $data['type'] = $_POST['type'];
            $data['numbers'] = $_POST['numbers'];
            $data['desc'] = $_POST['desc'];
            $data['user_qun_id'] = $_POST['user_qun_id'];
            if(M('Usercenter_salecard')->where($where)->save($data)){
                $this->success('操作成功', U(MODULE_NAME . '/salecard'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/editsalecard'),true);
            }
        }else{
            $where['id']=$this->_get('id','intval');
            $where['token']=$this->token;
            $data = D('Usercenter_salecard')->where($where)->find();
            $userScoremodel = M('Usercenter_level');
            $level_data = $userScoremodel->where(array('token'=>$this->token))->select();
            $this->assign('hover',6);
            $this->assign('data',$data);
            $this->assign('level_data',$level_data);
            $this->display();
        }
    }


    /*
     * 添加优惠券
     */
    public function insertsalecard(){
        if(IS_POST){
            $data = array();
            $data['name'] = $_POST['name'];
            $data['start_date'] = $_POST['start_date'];
            $data['end_date'] = $_POST['end_date'];
            $data['sale_money'] = $_POST['sale_money'];
            $data['type'] = $_POST['type'];
            $data['numbers'] = $_POST['numbers'];
            $data['desc'] = $_POST['desc'];
            $data['token'] = $this->token;
            $data['sale_sn'] = 'WP'.time();
            $data['user_qun_id'] = $_POST['user_qun_id'];
            $data['add_time'] = time();
            if(M('Usercenter_salecard')->add($data)){
                $this->success('操作成功', U(MODULE_NAME . '/salecard'));
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/addsalecard'));
            }
        }
    }


    /*
     * 积分
     */
    public function scoreman(){
        $where = array('tp_user_app_list.status'=>1,'tp_user_app_list.uid'=>session('uid'),'tp_user_app_list.token'=>session('token'),'tp_app_list.cate_id'=>3);
        $applist = M('User_app_list')->field('tp_app_list.id,tp_app_list.app_name,tp_user_app_list.uid')
            ->join("left join tp_app_list on tp_app_list.id=tp_user_app_list.app_id")->where($where)->select();
        $scoreGuizeModel = M('Usercenter_score_guize');
        foreach($applist as $k=>$v){
            $tempdata = array();
            $tempdata = $scoreGuizeModel->where(array('token'=>$this->token,'app_id'=>$v['id']))->find();
            if($tempdata){
                $applist[$k]['is_member']  = $tempdata['is_member'];
                $applist[$k]['is_score']  = $tempdata['is_score'];
            }else{
                $applist[$k]['is_member']  = 3;
                $applist[$k]['is_score']  = 3;
            }
        }
        $usercenter_signmodel = M('Usercenter_sign_set');
        $signdata = $usercenter_signmodel->where(array('token'=>$this->token))->find();
        $usercenter_set = M('Usercenter_set')->where(array('token'=>$this->token))->find();
        $this->assign('hover',4);
        $this->assign('applist',$applist);
        $this->assign('signdata',$signdata);
        $this->assign('usercenter_set',$usercenter_set);
        $this->display();
    }

    /*
     * 积分设定
     */
    public function setscore(){
        $scoreGuizeModel = M('Usercenter_score_guize');
        if(IS_POST){
            $data = array();
            $data['is_member'] = $_POST['is_member'];
            $data['is_score'] = $_POST['is_score'];
            $data['app_id'] = $_POST['app_id'];
            $data['need_score'] = $_POST['need_score'];

            if($scoreGuizeModel->where(array('token'=>$this->token,'app_id'=>$data['app_id']))->find()){
                if($scoreGuizeModel->where(array('token'=>$this->token,'app_id'=>$data['app_id']))->save($data)){
                    $this->success('操作成功', U(MODULE_NAME . '/scoreman'));
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/scoreman'));
                }
            }else{
                $data['token'] = $this->token;
                if($scoreGuizeModel->add($data)){
                    $this->success('操作成功', U(MODULE_NAME . '/scoreman'));
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/scoreman'));
                }
            }

        }else{
            $data = $scoreGuizeModel->where(array('token'=>$this->token,'app_id'=>$_GET['app_id']))->find();
            $this->assign('app_id',$_GET['app_id']);
            $this->assign('data',$data);
            $this->assign('hover',4);
            $this->display();
        }
    }

    /*
     *签到积分设置
     */
    public function signset(){
        if(IS_POST){
            $data = array();
            $data['id'] = $_POST['id'];
            $data['day_score'] = $_POST['day_score'];
            $data['days'] = $_POST['days'];
            $data['scores'] = $_POST['scores'];
            $data['status'] = $_POST['status'];
            $data['update_time'] = time();
            $data['token'] = $this->token;
            $usercenter_signmodel = M('Usercenter_sign_set');
            if($_POST['id']){
                if($usercenter_signmodel->data(array('token'=>$this->token,'id'=>$data['id']))->save($data)){
                    $this->success('操作成功', U(MODULE_NAME . '/scoreman'),true);
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/scoreman'),true);
                }
            }else{
                if($usercenter_signmodel->data($data)->add()){
                    $this->success('操作成功', U(MODULE_NAME . '/scoreman'));
                }else{
                    $this->error('操作失败', U(MODULE_NAME . '/scoreman'));
                }
            }
        }
    }

    /*
     * 查看会员
     */
    public function getmember(){
        $id = $_GET['id'];
        $usersModel = M('Usercenter_memberlist');
        $wxuser = M('Wxuser')->where(array('token'=>$this->token))->find();


        $where['tp_usercenter_memberlist.uid'] = $wxuser['id'];
        $where['tp_usercenter_memberlist.id'] = $id;

        $list =  $usersModel->field('tp_wxusers.fakeid,tp_wxusers.headimgurl,tp_wxusers.nickname,tp_wxusers.sex,tp_usercenter_memberlist.*')->join('left join tp_wxusers on tp_wxusers.openid = tp_usercenter_memberlist.openid')->order('tp_usercenter_memberlist.update_time desc')->where($where)->find();

        $swhere['token']=$this->token;
        $swhere['score']=array('elt',$list['score']);
        $userlevel_data = M("Usercenter_level")->where($swhere)->limit(1)->find();
        if($userlevel_data){
            $list['level_name'] =  $userlevel_data['name'];
        }else{
            $list['level_name'] =  '普通会员';
        }

          //  p(explode('|',$list['address']));die;
        $this->assign('userinfo',$list);
        $this->assign('hover',4);
        $this->assign('address',explode('|',$list['address']));
        $this->display();
    }

    /*
     *  手动修改积分
     */
    public function updatescoreandmoney(){
        if(IS_POST){
            $usercenter_scored_recordModel = M('Usercenter_score_record');
            $memberlistModel = M("Usercenter_memberlist");
            $data = array();
            if(!empty($_POST['update_score'])){

                $data['token'] = $this->token;
                $data['openid'] = $_POST['openid'];
                $data['type'] = 3;
                if($_POST['do_update_score'] == 1){
                    $data['score'] = $_POST['update_score'];
                }else if($_POST['do_update_score'] == 2){
                    $data['score'] = -$_POST['update_score'];
                }

                $memdata = $memberlistModel->where(array('token'=>$this->token,'openid'=>$_POST['openid'],'id'=>$_POST['id']))->find();
                $memscore = $memdata['score']+$data['score'];
				if($memscore >= 0){
				    $data['add_time'] = time();
                    $usercenter_scored_recordModel->add($data);
					if($memberlistModel->where(array('token'=>$this->token,'openid'=>$_POST['openid'],'id'=>$_POST['id']))->save(array('score'=>$memscore))){
						$this->success('操作成功', U(MODULE_NAME . '/memberman'),true);
					}else{
						$this->error('操作失败', U(MODULE_NAME . '/memberman'),true);
					}
				}else{
					$this->error('积分不够操作失败', U(MODULE_NAME . '/memberman'),true);
				}
            }

        }
    }

    /*
     *  错误更正积分
     */
    public function eupdatescoreandmoney(){
        if(IS_POST){
            $usercenter_scored_recordModel = M('Usercenter_score_record');
            $memberlistModel = M("Usercenter_memberlist");
            $data = array();
            if(!empty($_POST['update_score'])){

                $data['token'] = $this->token;
                $data['openid'] = $_POST['openid'];
                $data['type'] = 99;
                if($_POST['do_update_score'] == 1){
                    $data['score'] = $_POST['update_score'];
                }else if($_POST['do_update_score'] == 2){
                    $data['score'] = -$_POST['update_score'];
                }

                $memdata = $memberlistModel->where(array('token'=>$this->token,'openid'=>$_POST['openid'],'id'=>$_POST['id']))->find();
                $memscore = $memdata['score']+$data['score'];
                if($memscore >= 0){
                    $data['add_time'] = time();
                    $usercenter_scored_recordModel->add($data);
                    if($memberlistModel->where(array('token'=>$this->token,'openid'=>$_POST['openid'],'id'=>$_POST['id']))->save(array('score'=>$memscore))){
                        $this->success('操作成功', U(MODULE_NAME . '/memberman'),true);
                    }else{
                        $this->error('操作失败', U(MODULE_NAME . '/memberman'),true);
                    }
                }else{
                    $this->error('积分不够操作失败', U(MODULE_NAME . '/memberman'),true);
                }
            }

        }
    }


    /*
     *  手动修改金额
     */
    public function updatemoney(){
        if(IS_POST){
            $usercenter_scored_recordModel = M('Usercenter_money_record');
            $memberlistModel = M("Usercenter_memberlist");
            $data = array();
            if(!empty($_POST['update_score'])){

                $data['token'] = $this->token;
                $data['openid'] = $_POST['openid'];
                $data['pay_type'] = 2;
                $data['status'] = 1;
                if($_POST['do_update_score'] == 1){
                    $data['money'] = $_POST['update_score'];
                }else if($_POST['do_update_score'] == 2){
                    $data['money'] = -$_POST['update_score'];
                }

                $memdata = $memberlistModel->where(array('token'=>$this->token,'openid'=>$_POST['openid'],'id'=>$_POST['id']))->find();
                $memscore = $memdata['money']+$data['money'];
                if($memscore >= 0){
                    $data['add_time'] = time();
                    $usercenter_scored_recordModel->add($data);
                    if($memberlistModel->where(array('token'=>$this->token,'openid'=>$_POST['openid'],'id'=>$_POST['id']))->save(array('money'=>$memscore))){
                        $this->success('操作成功', U(MODULE_NAME . '/memberman'),true);
                    }else{
                        $this->error('操作失败', U(MODULE_NAME . '/memberman'),true);
                    }
                }else{
                    $this->error('金额不够操作失败', U(MODULE_NAME . '/memberman'),true);
                }
            }

        }
    }

    /*
     *积分记录
     */
    public function scorerecord(){
        $usercenter_scoreModel = M('Usercenter_score_record');
	$count=$usercenter_scoreModel->where(array('token'=>$this->token,'openid'=>$this->openid))->count();
        $page=new Page($count,15);
	$scorerecordlist = $usercenter_scoreModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('scorerecordlist',$scorerecordlist);
        $this->assign('page',$page->show());
	$this->assign('hover',4);
        $this->display();
    }

    /*
     *充值记录
     */
    public function moneyrecord(){
        $usercenter_moneyModel = M('Usercenter_money_record');
	$count=$usercenter_moneyModel->where(array('token'=>$this->token,'openid'=>$this->openid))->count();
	$page=new Page($count,15);
        $moneyrecordlist = $usercenter_moneyModel->where(array('token'=>$this->token,'openid'=>$this->openid))->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('moneyrecordlist',$moneyrecordlist);
        $this->assign('page',$page->show());
	$this->assign('hover',4);
        $this->display();
    }



    /*
     * 实体会员
     */
    public function shitimember(){

        $flagModel=M('Usercenter_shitimember');
        $where['token']=session('token');
        if($_POST['member_sn']){
            $where['member_sn'] = array('like','%'.$_POST['member_sn'].'%');
            $this->assign('member_sn',$_POST['member_sn']);
        }

        if($_POST['member_phone']){
            $where['member_phone'] = $_POST['member_phone'];
            $this->assign('member_phone',$_POST['member_phone']);
        }

        if($_POST['member_name']){
            $where['member_name'] = array('like','%'.$_POST['member_name'].'%');
            $this->assign('member_name',$_POST['member_name']);
        }
        $count=$flagModel->where($where)->count();
        $page=new Page($count,15);
        $info=$flagModel->where($where)->limit($page->firstRow.','.$page->listRows)->order('bind_time desc')->select();
        foreach($info as $key=>$val){
           $temp = array();
           $temp =  M('Usercenter_memberlist')->where(array('openid'=>$val['openid'],'token'=>$this->token))->find();
           if($temp){
              $info[$key]['mid'] = $temp['id'];
           }
            //拿会员微信图像
            $uid=M('Wxuser')->where(array('token'=>$this->token))->getField('id');
            $info[$key]['headimgurl']=M('Wxusers')->where(array('openid'=>$val['openid'],'uid'=>$uid))->getField('headimgurl');
        }
        $this->assign('page',$page->show());
        $this->assign('list',$info);
      // p($info);die;
        $this->assign('hover',5);
        $this->display();
    }

    /*
     * 实体会员编辑
     */
    public function editshitimember(){
        if(IS_POST){
            $data = array();
            $data['member_sn'] = $_POST['member_sn'];
            $data['member_name'] = $_POST['member_name'];
            $data['member_phone'] = $_POST['member_phone'];
            $where['id'] = $_POST['id'];
            $where['token'] = $this->token;
            if(M('Usercenter_shitimember')->where($where)->save($data)){
                $this->success('操作成功', U(MODULE_NAME . '/shitimember'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/shitimember'),true);
            }
        }else{
            $where['id'] = $this->_get('id', 'intval');
            $where['token'] = $this->token;
            $data = D('Usercenter_shitimember')->where($where)->find();
            $this->assign('data', $data);
            $this->assign('hover',5);
            $this->display();
        }
    }

    /*
     * 实体删除
     */
    public function shitidel(){
        $where['id']=$this->_get('id','intval');
        $where['token']=session('token');
        if(D('Usercenter_shitimember')->where($where)->delete()){
            $this->success('操作成功',U('Usercenter/shitimember',array('id'=>$where['id'],'token'=>$where['token'],'p'=>$_GET['p'])));
        }else{
            $this->error('操作失败',U('Usercenter/shitimember',array('id'=>$where['id'],'token'=>$where['token'],'p'=>$_GET['p'])));
        }
    }




    /*
     * 使用优惠券
     */
    public function usesalecard(){
        $id = $_GET['id'];
        $openid = $_GET['openid'];
        if($id && $openid){
            $usersalecardModel = M('Usercenter_user_salecard');
            $where = array();
            $where['id'] = $id;
            $where['openid'] = $openid;
            $where['token'] = $this->token;
            if($usersalecardModel->where($where)->find()){
                if($usersalecardModel->where($where)->save(array('status'=>1))){
                    $this->success2('成功使用');
                }else{
                    $this->error2('操作失败');
                }
            }else{
                $this->error2('非法操作');
            }
        }
    }

    public function notice(){
        $db=D('Usercenter_notice');
        $where['token']=session('token');
        $count=$db->where($where)->count();
        $page=new Page($count,15);
        $info=$db->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->assign('hover',7);
        $this->display();
    }

    public function addnotice(){
        if(IS_POST){
            $data = array();
            $data['title'] = $_POST['title'];
            $data['content'] = $_POST['content'];
            $data['token'] = session('token');
            $data['add_time'] = time();
            if(M('Usercenter_notice')->add($data)){
                $this->success('操作成功', U(MODULE_NAME . '/notice'));
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/notice'));
            }

        }else {
            $this->assign('hover', 7);
            $this->display();
        }
    }

    public function editnotice(){
        if(IS_POST){
            $data = array();
            $data['name'] = $_POST['name'];
            $data['content'] = $_POST['content'];
            $where['id'] = $_POST['id'];
            $where['token'] = $this->token;
            if(M('Usercenter_notice')->where($where)->save($data)){
                $this->success('操作成功', U(MODULE_NAME . '/notice'),true);
            }else{
                $this->error('操作失败', U(MODULE_NAME . '/notice'),true);
            }
        }else {
            $where['id'] = $this->_get('id', 'intval');
            $where['token'] = $this->token;
            $data = D('Usercenter_notice')->where($where)->find();
            $this->assign('data', $data);
            $this->assign('hover', 7);
            $this->display();
        }
    }

    public function delnotice(){
        $where['id']=$this->_get('id','intval');
        $where['token']=$this->token;
        if(D('Usercenter_notice')->where($where)->delete()){
            $this->success('操作成功',U(MODULE_NAME.'/notice'));
        }else{
            $this->error('操作失败',U(MODULE_NAME.'/notice'));
        }
    }


    public function importcode(){
    	import('ORG.Net.UploadFile');
    	$upload = new UploadFile();// 实例化上传类
    	$upload->maxSize  = 8145728 ;// 设置附件上传大小
    	$upload->allowExts  = array('xlsx','xls');// 设置附件上传类型
    	$upload->savePath =  './upload/';// 设置附件上传目录
    	if(!$upload->upload()) {// 上传错误提示错误信息
    		$err = $upload->getErrorMsg();
    		$this->ajaxReturn(array('code'=>-1,'msg'=>$err));
    	}else{// 上传成功
    		$data = $upload->getUploadFileInfo();
    		$filename = $data[0]['savepath'].$data[0]['savename'];
    		$exceldata = $this->read($filename);
    		$flagModel = M('Usercenter_shitimember');
    		$c = 0;
    		$d = 0;
    		$e = 0;
    		if($exceldata){
    			for($i=2;$i<=count($exceldata);$i++){
    				$where = array();
    				$where['token'] = session('token');
    				$where['member_sn'] = $exceldata[$i][2];
    				$where['member_phone'] = $exceldata[$i][1];
    				$temres = $flagModel->field('id')->where($where)->find();
    				if($temres == null){
    					$flagdata = array();
    					$flagdata['member_name'] =$exceldata[$i][0];
    					$flagdata['member_phone'] =$exceldata[$i][1];
    					$flagdata['member_sn'] = $exceldata[$i][2];
    					$flagdata['head_img'] = $exceldata[$i][3];
    					$flagdata['member_level'] = $exceldata[$i][4];
    					$flagdata['member_score'] = $exceldata[$i][5];
    					$flagdata['member_money'] = $exceldata[$i][6];
    					$flagdata['door_name'] = $exceldata[$i][7];
    					$flagdata['token'] = session('token');
    					$flagdata['status'] = 0;
    					if($flagModel->add($flagdata)){
    						$c=$c+1;
    					}else{
    						$d=$d+1;
    					}
    					continue;
    				}else{
    					$e=$e+1;
    					continue;
    				}
    			}
    		}else{
    			$this->ajaxReturn(array('code'=>0,'msg'=>'系统错误'));
    		}
    		// Create new PHPExcel object
    
    
    		$this->ajaxReturn(array('code'=>0,'msg'=>'处理成功(成功插入'.$c.'条,失败'.$d.'条,有'.$e.'条重复)'));
    	}
    }
    
    public function read($filename,$encode='utf-8'){
        vendor("PHPExcel.PHPExcel");
        $objReader = PHPExcel_IOFactory::createReader(Excel5);
        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
       
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;

    }

    public function exportexcel(){

        vendor("PHPExcel.PHPExcel");
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set properties
        $objPHPExcel->getProperties()->setCreator("ctos")
            ->setLastModifiedBy("ctos")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);

        //设置行高度
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(22);

        $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

        //set font size bold
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
        //$objPHPExcel->getActiveSheet()->getStyle('A1:I2')->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //设置水平居中
        //$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //
        //$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '会员编号')
            ->setCellValue('B1', '会员名称')
            //->setCellValue('C1', '会员等级')
            ->setCellValue('C1', '手机号')
            ->setCellValue('D1', '积分')
            ->setCellValue('E1', '金额')
            ->setCellValue('F1', '生日');

        $where=array();
        $wxuser = M('Wxuser')->where(array('token'=>session('token')))->find();
        $scodeModel=M('Usercenter_memberlist');
        $info=$scodeModel->where(array('uid'=>$wxuser['id']))->select();

	/*
        foreach($info as $k=>$v){
            $swhere['token']=$this->token;
            $zuserscore = M('Usercenter_score_record')
                ->field('sum(score) as allscore')
                ->where(array(
                    'token'=>$this->token,
                    'openid'=>$v['openid'],
                    'score'=>array('gt',0),
                    'type'=>array('not in','99')
                ))->select();
            $fscore = intval(M('Usercenter_score_record')
                ->where(array(
                    'token'=>$this->token,
                    'openid'=>$v['openid'],
                    'type'=>99
                ))->sum('score'));
            $userscore = $zuserscore[0]['allscore'] + $fscore;

            $swhere['score']=array('elt',$userscore);
            $userlevel_data = M("Usercenter_level")->where($swhere)->order('score desc')->limit(1)->find();
            if($userlevel_data){
                $info[$k]['level_name'] =  $userlevel_data['name'];
            }else{
                $info[$k]['level_name'] =  '普通会员';
            }
        }
	*/


        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<count($info);$i++){
            $objPHPExcel->getActiveSheet(0)->setCellValue('A'.($i+2), $info[$i]['member_sn']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B'.($i+2), $info[$i]['name']);
            //$objPHPExcel->getActiveSheet(0)->setCellValue('C'.($i+2), $info[$i]['level_name']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('C'.($i+2), $info[$i]['phone']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('D'.($i+2), $info[$i]['score']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('E'.($i+2), $info[$i]['money']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('F'.($i+2), $info[$i]['birth_day']);
            $objPHPExcel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(15);
        }
        // Rename sheet
        //$objPHPExcel->getActiveSheet()->setTitle('订单汇总表');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        //print_r($objPHPExcel);exit;
        //ob_end_clean();
        // Redirect output to a client’s web browser (Excel5)
        header("Pragma: public");

        header("Expires: 0");

        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");

        header("Content-Type:application/force-download");

        header("Content-Type:application/vnd.ms-execl");

        header("Content-Type:application/octet-stream");

        header("Content-Type:application/download");

        header('Content-Disposition:attachment;filename="Member-('.date('Ymd-His').').xls"');

        header("Content-Transfer-Encoding:binary");


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
        exit;
    }








    
    
    /*
     * 实体会员
    */
    public function dl(){
        $this->assign('jd',$_GET['type']);
    	
		$this->display();
    }



    public function dl2(){
    	import('ORG.Net.UploadFile');
    	$upload = new UploadFile();// 实例化上传类
    	$upload->maxSize  = 8145728 ;// 设置附件上传大小
    	$upload->allowExts  = array('xlsx','xls');// 设置附件上传类型
    	$upload->savePath =  './upload/';// 设置附件上传目录
    	if(!$upload->upload()) {// 上传错误提示错误信息
    		$err = $upload->getErrorMsg();
    		$this->ajaxReturn(array('code'=>-1,'msg'=>$err));
    	}else{// 上传成功
    		$data = $upload->getUploadFileInfo();
    		$filename = $data[0]['savepath'].$data[0]['savename'];
    		$exceldata = $this->read($filename);

            array_shift($exceldata);
    		foreach ($exceldata as $ke=>$v){
                $dataes['name'] = $v['0'];
                $dataes['phone'] = $v['1'];
                $dataes['email'] = $v['2'];
                $dataes['qq'] = $v['3'];
                $dataes['type'] = $v['4'];
                $dataes['grade']= $v['5'];
                $dataes[ 'is_show'] =  1;
                $dataes[ 'state'] = 1;
                $dataes['token'] = $_SESSION['token'];
                $dataes[ 'add_time']= time();

                if($v['6']){
                    $dataes['password']= md5($v['6']);
                }else{
                    $dataes['password']=md5('888888');
                }
                if($v['5'] ==1){
                    $dataes['vip_time'] = time();
                    $dataes['sp_time'] ='';
                }else{
                    $dataes['vip_time'] = '';
                    $dataes['sp_time'] =time();
                }
    			M('jd_user')->add($dataes);
    		};
    		$this->ajaxReturn(array('code'=>0,'msg'=>'处理成功~~别再次请求'));
    	}
    }
    
    
    public function dl3(){
    	import('ORG.Net.UploadFile');
    	$upload = new UploadFile();// 实例化上传类
    	$upload->maxSize  = 8145728 ;// 设置附件上传大小
    	$upload->allowExts  = array('xlsx','xls');// 设置附件上传类型
    	$upload->savePath =  './upload/';// 设置附件上传目录
    	if(!$upload->upload()) {// 上传错误提示错误信息
    		$err = $upload->getErrorMsg();
    		$this->ajaxReturn(array('code'=>-1,'msg'=>$err));
    	}else{// 上传成功
    		$data = $upload->getUploadFileInfo();
    		$filename = $data[0]['savepath'].$data[0]['savename'];
    		$exceldata = $this->read($filename);

            array_shift($exceldata);
    		foreach ($exceldata as $ke=>$v){
    			$v['8']=explode(',',$v['8']);
    			$v['8']=M('jd_tag')->where(array('name'=>array('in',$v['8'])))->getField('id',true);
    			$v['8']=implode(',', $v['8']);
    			M('jd_wz')->add(array(
    					'title' => $v['0'],
    					'user_id'=>$v['1'],
    					'tel' => $v['2'],
    					'name' => $v['3'],
    					'hy' => $v['4'],
    					'type' => $v['5'],
    					'gjz' => $v['6'],
    					'url' => $v['7'],
    					'tags'=>$v['8'],
    					'hz'=>$v['9'],
    					'ld'=>$v['10'],
    					'content'=>$v['11'],
    					'status' => 1,
    					'token' => $_SESSION['token'],
    					'add_time' => time()
    			));
    		};
    		$this->ajaxReturn(array('code'=>0,'msg'=>'处理成功~~别再次请求'));
    	}
    }
    public function dl4(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        //p($upload);
        $upload->maxSize  = 8145728 ;// 设置附件上传大小
        $upload->allowExts  = array('xlsx','xls');// 设置附件上传类型
        $upload->savePath =  './upload/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $err = $upload->getErrorMsg();
            $this->ajaxReturn(array('code'=>-1,'msg'=>$err));
        }else{// 上传成功
            $data = $upload->getUploadFileInfo();

            $filename = $data[0]['savepath'].$data[0]['savename'];

            
            $exceldata = Excel::excel2Arr($filename);
            
            //$exceldata = $this->read($filename);
            array_shift($exceldata);
            //p($exceldata);die;
            foreach ($exceldata as $ke=>$v){
                if($v['2'] == '')
				{
					$v['2'] = 0;
				}
				if($v['0'] == '')
				{
					$v['0'] = 0;
				}
				if($v['1'] == '')
				{
					$v['1'] = 0;
				}
				if($v['3'] == '')
				{
					$v['3'] = 0;
				}
				if($v['4'] == '')
				{
					$v['4'] = 0;
				}
				if($v['5'] == '')
				{
					$v['5'] = 0;
				}
                $dataes['card'] = $v['0'];
                $dataes['token'] = $_SESSION['token'];
                $dataes['secret'] = $v['1'];
                $dataes['sn'] = $v['2'];
                $dataes['is_yj'] = $v['3'];
                $dataes['is_dj'] = $v['4'];
                $dataes['is_sn'] = $v['5'];
                $dataes['f_time']= $v['7'];
                $dataes[ 'customer_code'] =  $v['6'];
                //$dataes[ 'state'] = 1;
                //$dataes['token'] = $_SESSION['token'];
                //$dataes[ 'add_time']= time();

               /* if($v['6']){
                    $dataes['password']= md5($v['6']);
                }else{
                    $dataes['password']=md5('888888');
                }
                if($v['5'] ==1){
                    $dataes['vip_time'] = time();
                    $dataes['sp_time'] ='';
                }else{
                    $dataes['vip_time'] = '';
                    $dataes['sp_time'] =time();
                }*/
                 M('js_sn')->add($dataes);
            };
            $this->ajaxReturn(array('code'=>0,'msg'=>'处理成功~~别再次请求'));
        }
    }
    


}
