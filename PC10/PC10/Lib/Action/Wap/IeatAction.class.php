<?php
/**
 * Created by PhpStorm.
 * User: ����
 * Date: 2014/11/26
 * Time: 16:35
 */
class IeatAction extends BaseAction{
    public $categoryModel;
    public function _initialize() {
        parent::_initialize();
        import('@.Model.User.CategoryModel');
        $this->categoryModel = new CategoryModel();

       /* if(!$_REQUEST['openid']){
            exit("��û�й�ע�ٷ����ں�");
        }
        if(empty($this->wxusers)){
            exit("��û�й�ע�ٷ����ں�");
        }*/
        $this->assign('hover',ACTION_NAME);
    }
    //��ҳ
    public function index(){
        $this->display();
    }
    //��ʳר��
    public function subject_article(){
//        echo $_GET['article_id'];exit;
        if($_GET['article_id']){
           $article = M('Ieat_subject_article')->where(array('article_id'=>$_GET['article_id']))->find();
            $this->assign('article',$article);
            $this->display();
        }else{
            exit('������');
        }
    }
    public function subject(){
        $bIsAjax = isset($_GET['is_ajax']) && $_GET['is_ajax'];
        if(isset($_GET['type'])){
            $cat = $this->categoryModel->selectAll();
            foreach($cat as $k => $v){
                if($v['type'] == $_GET['type']){
                    $arr[$k] = $v['cat_id'];
                }
            }
            foreach($arr as $v){
                $List = $this->categoryModel->getSon($v);
            }
            $iPageNum = 20;
            //�ս���չʾƪ����
            if(isset($_GET['t'])){
                $this->assign('get',$_GET);
                $count = M('Ieat_subject_article')->where(array(
                    'token'=>$this->token,
                    //'cat_id'=>$List[$_GET['t']-1]['cat_id']
                    'cat_id'=>136
                ))->count();
                $Page       = new Page($count,$iPageNum);
                $show       = $Page->show();
                $articleList = M('Ieat_subject_article')
                    ->field('article_id,add_time,token,article_title,article_pic,cat_id,title_bg')
                    ->order('add_time DESC')
                    ->where(array(
                        'token'=>$this->token,
                        'cat_id'=>136
                        //'cat_id'=>$List[$_GET['t']-1]['cat_id']
                    ))->limit($Page->firstRow.','.$Page->listRows)
                    ->select();

                $this->assign('cat_id',$List[$_GET['t']-1]['cat_id']);
            }
            //ajax���ظ������
            if($bIsAjax){
                $count = M('Ieat_subject_article')->where(array(
                    'token'     => $this->token,
                    'cat_id'    => $_GET['cat_id']
                ))->count();
                $pageNum = (int)($_GET['pageNum']);
                $pagetotal = ceil($count/$iPageNum);
                $a = $pageNum > $pagetotal?true:false;
                if($a){
                    exit;
                }else{
                    $moreArtile = M('Ieat_subject_article')
                        ->field('article_id,add_time,token,article_title,article_pic,cat_id')
                        ->order('add_time DESC')
                        ->where(array(
                            'token'=>$this->token,
                            'cat_id'=>$_GET['cat_id']
                            //'cat_id'=>$List[$_GET['t']-1]['cat_id']
                        ))->limit($Page->firstRow.','.$Page->listRows)
                        ->select();
                    if(!empty($moreArtile)){
                        foreach($moreArtile as $k => $v){
                            $tpl .= '<div class="listbox" >';
                            $tpl .= '<div onclick="location.href='."'".U('Ieat/subject_article',array('token'=>$this->token,'openid'=>$_GET['openid'],'type'=>1,'t'=>$_GET['t'],'article_id'=>$v['article_id']))."'".'" class="listpic" style=" background-image:url('.$v['article_pic'].');">';
                            $tpl .= '</div>';
                            $tpl .= '<div class="listtitlebox myul">';
                            $tpl .= '<div class="date">';
                            $c = $this->turnDate($v['add_time']);
                            $tpl .= '<div class="dd">'.$c[2].'</div>';
                            $tpl .= '<div class="mmyy"><span class="mm">'.$c[1].'</span><span class="yy">'.$c[0].'</span></div>';
                            $tpl .= '</div><div class="titlebox" ><div class="title ieat"><span class="titletext">'.$v['article_title'].'</span></div></div></div></div>';
                        }
                        exit($tpl);
                    }else{
                        exit(0);
                    }
                }
            }
            $this->assign('show',$show);
            $this->assign('articleList',$articleList);
            $this->assign('List',$List);
            $this->assign('list',json_encode($List));
        }else{
            exit('�Ƿ����ʡ�');
        }
        $this->display();
    }
    //���·�ת����Ӣ�ĵ���д
    public function turnDate($add_time){
        $b = explode(' ',$add_time);
        $c = explode('-',$b[0]);
        if($c[1] == 1){
            $c[1] = 'JAN.';
        }elseif($c[1] == 2){
            $c[1] = 'Feb.';
        }elseif($c[1] == 3){
            $c[1] = 'Mar.';
        }elseif($c[1] == 4){
            $c[1] = 'Apr.';
        }elseif($c[1] == 5){
            $c[1] = 'May.';
        }elseif($c[1] == 6){
            $c[1] = 'Jun.';
        }elseif($c[1] == 7){
            $c[1] = 'Jul.';
        }elseif($c[1] == 8){
            $c[1] = 'Aug.';
        }elseif($c[1] == 9){
            $c[1] = 'Sept.';
        }elseif($c[1] == 10){
            $c[1] = 'Oct.';
        }elseif($c[1] == 11){
            $c[1] = 'Nov.';
        }elseif($c[1] == 12){
            $c[1] = 'Dec.';
        }
        return $c;
    }
    //shoppingmall
    public function shoppingMall(){
        if(isset($_GET['type'])){
            $cat = $this->categoryModel->selectAll();
            foreach($cat as $k => $v){
                if($v['type'] == $_GET['type']){
                    $arr[$k] = $v['cat_id'];
                }
            }
            foreach($arr as $v){
                $List = $this->categoryModel->getSon($v);
            }
            if(isset($_GET['t'])){
                $this->assign('get',$_GET);
                $count = M('Ieat_category')->where(array('token'=>$this->token,'parent_id'=>$List[$_GET['t']-1]['cat_id']))->count();
                $Page = new Page($count,5);
                $show       = $Page->show();
                $mallList = M('Ieat_category')->field('cat_id,parent_id,cat_name')->where(array('token'=>$this->token,'parent_id'=>$List[$_GET['t']-1]['cat_id']))->limit($Page->firstRow.','.$Page->listRows)->select();
                foreach($mallList as $k => $v){
                    $pic = M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$v['cat_id']))->field('virtual_pic,cat_id')->find();
                    $pic['virtual_pic'] = json_decode($pic['virtual_pic'],true);
                    $key = array_keys($pic['virtual_pic']);
                    $mallList[$k]['pic'] = $pic['virtual_pic'][$key[0]];
                }
                $this->assign('cat_id',$List[$_GET['t']-1]['cat_id']);
            }
            if(IS_POST){
                $count = M('Ieat_category')->where(array('token'=>$this->token,'parent_id'=>$_GET['cat_id']))->count();
                $pageNum = $this->_post('pageNum','intval');
                $pagetotal = ceil($count/10);
                $a = $pageNum > $pagetotal?true:false;
                if($a){
                    exit;
                }else{
                    $moreMall = M('Ieat_category')->where(array('token'=>$this->token,'parent_id'=>$_GET['cat_id']))->field('cat_id,parent_id,cat_name')->select();
                    foreach($moreMall as $k => $v){
                        $pic = M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$v['cat_id']))->field('virtual_pic,cat_id')->find();
                        $pic['virtual_pic'] = json_decode($pic['virtual_pic'],true);
                        $key = array_keys($pic['virtual_pic']);
                        $moreMall[$k]['pic'] = $pic['virtual_pic'][$key[0]];
                    }
                    if(!empty($moreMall)){
                        foreach($moreMall as $k => $v){
                            $tpl .= '<div class="shop listbox">';
                            $tpl .= '<div class="shop listpic" style="background-image:url('.$v['pic'].')"></div>';
                            $tpl .= '<div class="shop listtitlebox myul">';
                            $tpl .= '<div class="shop titlebox" >';
                            $tpl .= '<div class="title shop"><span class="titletext">'.$v['cat_name'].'</span></div>';
                            $tpl .= '</div></div></div>';
                        }
                        exit($tpl);
                    }else{
                        exit(0);
                    }
                }
            }
            $this->assign('show',$show);
            $this->assign('mallList',$mallList);
            $this->assign('List',$List);
        }else{
            exit('�Ƿ����ʡ�');
        }
        $this->display();
    }

    //�̼ҽ���
    public function mallIntro(){
        $this->assign('get',$_GET);
        // if(IS_GET){
        //     if($_GET['from'] == "story"){
        //         $sql = "select c.cat_id,c.cat_name,s.story_content from tp_ieat_category as c RIGHT JOIN tp_ieat_mall_brand_story as s ON c.cat_id=s.cat_id WHERE c.token=s.token AND c.token='".$this->token."' and c.cat_id=".$_GET['cat_id'];
        //         $story = M('Ieat_category')->query($sql);
        //         $story = $story[0];
        //         $story['story_content'] = htmlspecialchars_decode($story['story_content']);
        //         exit(json_encode($story));
        //     }elseif($_GET['from'] == "address"){
        //         $address = M('Ieat_mall_address')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->field('longitude,latitude,zone,address')->find();
        //         exit(json_encode($address));
        //     }elseif($_GET['from'] == "virtual"){
        //         $virtual = M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->field('virtual_pic')->find();
        //         $pic = json_decode($virtual['virtual_pic'],true);
        //         foreach($pic as $k => $v){
        //             $picture[] = $v;
        //         }
        //         exit(json_encode($picture));
        //     }elseif($_GET['from'] == "dish"){
        //         $sql = "select c.cat_id,c.cat_name,d.dish_name,d.dish_price,d.dish_pic from tp_ieat_category as c RIGHT JOIN tp_ieat_mall_dish as d ON c.cat_id=d.cat_id WHERE c.token=d.token AND c.token='".$this->token."' and c.cat_id=".$_GET['cat_id'];
        //         $dish = M('Ieat_mall_dish')->query($sql);
        //         exit(json_encode($dish));
        //     }
        // }
        $sql = "select c.cat_id,c.cat_name,s.story_content from tp_ieat_category as c RIGHT JOIN tp_ieat_mall_brand_story as s ON c.cat_id=s.cat_id WHERE c.token=s.token AND c.token='".$this->token."' and c.cat_id=".$_GET['cat_id'];
        $story = M('Ieat_category')->query($sql);
        $story = $story[0];
        $story['cat_name']   = htmlspecialchars_decode($story['cat_name']);
        $story['story_content'] = htmlspecialchars_decode($story['story_content']);
       
        $this->assign($story);
        $this->display();
    }
    //����ǽ
    public function msgWall(){
        $this->assign('get',$_GET);
        $iPage    = (int)$_GET['page'];
        $iPage    = $iPage <= 1 ? 1 : $iPage;
        $iPageNum = 10;

        $msgList = M('Ieat_mall_levmsg')
            ->order('id DESC')
            ->where(array('token'=>$this->token,'cat_id'=>intval($_REQUEST['cat_id'])))
            ->limit(($iPage-1) * $iPageNum, $iPageNum)
            ->select();

        $Icon = M('Ieat_icon')->where(array('token' => $this->token, 'type' => 1))->find();
        if(isset($Icon[0])) { $Icon = $Icon[0]; }
        $this->assign(array(
            'page'  => $iPage,
            'icon'  => $Icon,
            'cat_id' => $_REQUEST['cat_id']
        ));

        $this->assign('msgList',$msgList);
        if($_GET['ajax']) {
            echo $this->fetch('tpl/Wap/default/Ieat_msgWall_Page.html');
        }else{
            $this->display();
        }
    }
    //��������
    public function commont(){
        $this->assign('get',$_GET);
        $this->assign('info',$this->wxusers);
        $this->display();
    }
    public function ajaxcon(){
        if(IS_POST){
            $data['token'] = $this->token;
            $data['cat_id'] = $_GET['cat_id'];
            $data['customer_openid'] = $this->openid;
            $data['customer_name'] = $this->wxusers['nickname'];
            $data['customer_pic'] = $this->wxusers['headimgurl'];
            $data['customer_msg'] = base64_encode($_POST['customer_msg']);
            $data['customer_upload_pic'] = $_POST['url'];
            $data['add_time'] = date('Y-m-d H:i:s');
            if(M('Ieat_mall_levmsg')->add($data)){
                $this->success('����ɹ���',U('Ieat/msgWall',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error('����ʧ�ܣ�');
            }
        }
    }
    public function uploadPic(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// ʵ���ϴ���
        $upload->maxSize = 512000;// ���ø����ϴ���С
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');//�����ϴ��ļ�����
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');// ���ø����ϴ�����
        $upload->saveRule = "image_".time();
        $upload->savePath = __ROOT__ . 'upload/' . $this->token . '/image/' . date('Ymd') . '/';// ���ø����ϴ�Ŀ¼
        if (!$upload->upload()) {    // �ϴ�������ʾ������Ϣ
            $this->error($upload->getErrorMsg());
        } else {                      // �ϴ��ɹ� ��ȡ�ϴ��ļ���Ϣ
            $info = $upload->getUploadFileInfo();
            exit(json_encode($info));
        }
    }
    //�ʾ����
    public function investigation(){
        $this->assign('get',$_GET);

        $invesgigation = M('ieat_mall_investigation')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->select();
        
        $ten = M('ieat_mall_ten')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->select();
        
        $map['token'] = trim($_REQUEST['token']);
        $map['cat_id'] = trim($_REQUEST['cat_id']);
        $map['name']  = array('neq',-1);
        $map['select']  = array('neq',-1);

        $subjective = M('ieat_mall_subjective')->where($map)->select();

        $score = M('ieat_mall_score')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->select();
        
        if($invesgigation){
            $this->assign('ing',$invesgigation);
        }

        if($ten){
            $this->assign('ten',$ten);
        }
       
        if($subjective){
            foreach($subjective as $k => $v){
                $subjective[$k]['select'] = explode('|',$subjective[$k]['select']);
                $a = 1;
                foreach($score as $key => $value){
                    $score[$key]['new'] = json_decode($score[$key]['subjective'],true);
          
                    if($score[$key]['new'][$subjective[$k]['id']] == $subjective[$k]['select'][0]){
                        
                        
                        $subjective[$k]['select']['better'] += $a;
                        $subjective[$k]['select']['bad'] = count($score) - $a;
                    }else{
                        
                        $subjective[$k]['select']['bad'] += $a;
                        $subjective[$k]['select']['better'] = count($score) - $a;
                    }
                }
            }
          
            //print_r($score);
            //print_r($subjective);
            $Title = M('ieat_mall_subjective')->where(array(
                'token'     => trim($_REQUEST['token']),
                'cat_id'    => trim($_REQUEST['cat_id']),
                'name'      => -1,
                'select'    => -1
            ))->select();
            $sTitle = '';
            if(isset($Title[0])){
                $sTitle = $Title[0]['value'];
            }
            $this->assign('subTitle',$sTitle);
            $this->assign('sub',$subjective);
            $this->assign('score',$score);
        }
        if(IS_POST){
            if(M('ieat_mall_score')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'openid'=>$_REQUEST['openid']))->find()){
                exit(json_encode(array('info'=>'���Ѿ����֣������ظ����','status'=>0)));
            }
            $Str = json_decode(htmlspecialchars_decode($_POST['Str'],ENT_QUOTES),true);
            $data['token'] = $this->token;
            $data['cat_id'] = $_REQUEST['cat_id'];
            $data['openid'] = $_REQUEST['openid'];
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['ten'] = json_encode($Str[0]);
            $data['subjective'] = json_encode($Str[1]);
            if(M('Ieat_mall_score')->add($data)){
                exit(json_encode(array('info'=>'�ύ�ɹ�','status'=>1)));
            }else{
                exit(json_encode(array('info'=>'�ύʧ�ܣ����Ժ�����','status'=>0)));
            }
        }
        $this->display();
    }

    /*��������ķ���*/
    public function classfiy(){
        $model = M('Ieat_category');
        $pid = 151;
        $list = $model->where(array(
            'token'=>$this->token,
            'parent_id' =>$pid
        ))->select();
        $this->assign(array(
            'list'=>$list
        ));

        $this->display();
    }


    //���ز�������
    public function restaurantUnio(){
        $model = M('Ieat_category');
        $pid = $_GET['zone'];
        $list = $model->where(array(
            'token'=>$this->token,
            'parent_id' =>$pid
        ))->select();

       // P($model->where(array('cat_id'=>$pid))->getField('cat_name'));exit;
        $this->assign(array(
            'list'=>$list,
            'titles'=>$model->where(array('id'=>$pid))->getField('cat_name')
        ));





        $request = $_REQUEST;
        $zone_id = intval($request['zone']);
       
        $this->assign('get',$_GET);

        //���е���
        // $_sql = "select b.cat_id,b.cat_name
        //         from tp_ieat_mall_address as a left join tp_ieat_category as b on a.zone=b.cat_id
        //         where a.token='".$this->token."' group by a.zone
        // ";
        $mallAddress = M('Ieat_category')->field("cat_id,cat_name")->where('type=3')->select();
        
        $this->assign('mallAddress',$mallAddress);

        $sTitle = '';
        if(empty($zone_id)){
            $inZone = "";
            foreach ($mallAddress as $key => $value) {
                 $inZone .= $value['cat_id'].',';
            }unset($key);unset($value);
            $inZone = substr($inZone,0,strlen($inZone)-1);
        }else{
            $inZone = $zone_id;
            $mallAddress = Arr::changeIndex($mallAddress, 'cat_id');
            $sTitle      = $mallAddress[$zone_id]['cat_name'];
        }
        $this->assign('title', $sTitle);
        

        //��ǰ������̵�
        $sql1 = "select cat_id from tp_ieat_mall_address WHERE token='".$this->token."' and zone in($inZone)";
  
        $sql2 = "select c.cat_id,c.cat_name,v.virtual_pic,c.style from tp_ieat_category as c RIGHT JOIN tp_ieat_mall_virtual as v ON v.cat_id=c.cat_id WHERE c.cat_id in (".$sql1.") and c.token=v.token and c.token='".$this->token."'";
        $cat_idInfo = M('Ieat_mall_address')->query($sql1);


        
        $count = count($cat_idInfo);
        $pageSize = 2;
        $Page = new Page($count,$pageSize);
        $show = $Page->show();

        $page = intval($request['p']);

        
        $start = 0;
        if($page>1){
            $start = ($page*$pageSize)-1;
        }

        $sql3 = $sql2." limit ".$start.",".$pageSize;
      

        $mallInfo = M('Ieat_mall_address')->query($sql3);

        foreach($mallInfo as $k => $v){
            $mallInfo[$k]['virtual_pic'] = json_decode($mallInfo[$k]['virtual_pic'],true);
            $keys = array_keys($mallInfo[$k]['virtual_pic']);
            $mallInfo[$k]['virtual_pic'] = $mallInfo[$k]['virtual_pic'][$keys[0]];
        }
        
        if(IS_POST){
            if(empty($mallInfo)){
                exit();
            }else{
                foreach($mallInfo as $k => $v){
                    $tpl .= '<div class="shop listbox" style="text-align: center;" onClick="location.href='."'".U('Ieat/mallIntro',array('token'=>$this->token,'openid'=>$_GET['openid'],'cat_id'=>$v['cat_id']))."'".'">'.
                    '<img class="shop listpic"  style="" src="'.$v['virtual_pic'].'" />'.
                    '<div class="shop listtitlebox myul">'.
                    '<div class="shop titlebox" >'.
                    '<div class="title shop" style="background-color:'.$v['style'].' "><span class="titletext">'.$v['cat_name'].'</span></div>'.
                    '</div></div></div>';
                }
                exit($tpl);
            }
           
        }

        $this->assign('count',$count);
        $this->assign('show',$show);
        $this->assign('mallInfo',$mallInfo);


        $this->display();
    }

    //�̼һ
    public function act(){
        $this->assign('get',$_GET);
        $actInfo = M('Ieat_mall_act')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->field(array('act_intro,act_pic'))->find();
        $this->assign('actInfo',$actInfo);
        $this->display();
    }
    //�Ź�ȯ
    public function ticketANDfood(){
         if(isset($_GET['t'])){
            $this->assign('get',$_GET);
            if($_GET['t'] == 1){
                $model = $this->categoryModel;
                $sql = "select count(*) as count from (tp_ieat_category as c RIGHT JOIN tp_ieat_mall_ticket as t ON c.cat_id=t.cat_id) INNER JOIN tp_ieat_mall_virtual as v ON c.cat_id=v.cat_id WHERE c.token=t.token AND c.token='".$this->token."' and t.is_buy=0 and date(now())>=t.start_date and date(now())<=t.end_date";
                $sql1 = "select t.ticket_id,t.ticket_name,t.ticket_price,t.start_date,t.end_date,c.cat_name,t.cat_id,v.virtual_pic from (tp_ieat_category as c RIGHT JOIN tp_ieat_mall_ticket as t ON c.cat_id=t.cat_id) INNER JOIN tp_ieat_mall_virtual as v ON c.cat_id=v.cat_id WHERE c.token=t.token AND c.token='".$this->token."' and t.is_buy=0 and date(now())>=t.start_date and date(now())<=t.end_date";
                $count = $model->query($sql);
               
                $count = $count[0]['count'];
                $Page = new Page($count,10);
                $show = $Page->show();
                $sql1 = $sql1." limit ".$Page->firstRow.",".$Page->listRows;
                $ticketList = $model->query($sql1);
                foreach($ticketList as $k => $v){
                    $ticketList[$k]['virtual_pic'] = json_decode($ticketList[$k]['virtual_pic'],true);
                    $keys = array_keys($ticketList[$k]['virtual_pic']);
                    $ticketList[$k]['virtual_pic'] = $ticketList[$k]['virtual_pic'][$keys[0]];
                }
                $this->assign('show',$show);
                $this->assign('count',$count);
                $this->assign('ticketList',$ticketList);
                if(IS_POST){
                    $count = $_GET['count'];
                    $pageNum = $this->_post('pageNum','intval');
                    $pagetotal = ceil($count/10);
                    $a = $pageNum > $pagetotal?true:false;
                    if($a){
                        exit;
                    }else{
                        $sql1 = "select t.ticket_id,t.ticket_name,t.ticket_price,t.start_date,t.end_date,c.cat_name,t.cat_id,v.virtual_pic from (tp_ieat_category as c RIGHT JOIN tp_ieat_mall_ticket as t ON c.cat_id=t.cat_id) INNER JOIN tp_ieat_mall_virtual as v ON c.cat_id=v.cat_id WHERE c.token=t.token AND c.token='".$this->token."' and t.is_buy=0 and date(now())>=t.start_date and date(now())<=t.end_date";
                        $moreTickets = $model->query($sql1);
                        foreach($moreTickets as $k => $v){
                            $moreTickets[$k]['virtual_pic'] = json_decode($moreTickets[$k]['virtual_pic'],true);
                            $keys = array_keys($moreTickets[$k]['virtual_pic']);
                            $moreTickets[$k]['virtual_pic'] = $moreTickets[$k]['virtual_pic'][$keys[0]];
                        }
                        if(!empty($moreTickets)){
                            foreach($moreTickets as $k => $v){
                                $tpl .= '<div class="listbox">';
                                $tpl .= '<div class="listpic" style=" background-image:url('.$v['virtual_pic'].');">';
                                $tpl .= '</div>';
                                $tpl .= '<div class="listtitlebox myul" style="margin-left:20%">';
                                $tpl .= '<div class="titlebox" >';
                                $tpl .= '<div class="title  ieatXianGou"><span class="titletext">��'.$v['cat_name'].'��'.$v['ticket_name'].'�Ź��ۣ���'.$v['ticket_price'].'Ԫ</span></div>';
                                $tpl .= '</div></div></div>';
                            }
                            exit($tpl);
                        }else{
                            exit(0);
                        }
                    }
                }
            }elseif($_GET['t'] == 2){
                $sql = "select c.cat_id,c.cat_name, c.add_time,f.food_name,f.food_pic1,f.food_price,f.start_date,f.end_date,f.food_id from tp_ieat_category as c RIGHT JOIN tp_ieat_mall_food as f ON c.cat_id=f.cat_id WHERE c.token=f.token AND c.token='".$this->token."' and date(now())>=f.start_date and date(now())<=f.end_date and f.food_num>0";
                $sql1 = "select count(*) as count from tp_ieat_category as c RIGHT JOIN tp_ieat_mall_food as f ON c.cat_id=f.cat_id WHERE c.token=f.token AND c.token='".$this->token."' and date(now())>=f.start_date and date(now())<=f.end_date and f.food_num>0";
                $model = $this->categoryModel;
                $count = $model->query($sql1);
                $count = $count[0]['count'];
                $Page = new Page($count,10);
                $show = $Page->show();
                $sql .= " limit ".$Page->firstRow.",".$Page->listRows;
                $foodList = $model->query($sql);
                $this->assign('show',$show);
                $this->assign('count',$count);
                $this->assign('foodList',$foodList);
                if(IS_POST){
                    $count = $_GET['count'];
                    $pageNum = $this->_post('pageNum','intval');
                    $pagetotal = ceil($count/10);
                    $a = $pageNum > $pagetotal?true:false;
                    if($a){
                        exit;
                    }else{
                        $sql = "select c.cat_id,c.cat_name,f.food_name,f.food_pic1,f.food_price,f.start_date,f.end_date,f.food_id from tp_ieat_category as c RIGHT JOIN tp_ieat_mall_food as f ON c.cat_id=f.cat_id WHERE c.token=f.token AND c.token='".$this->token."' and date(now())>=f.start_date and date(now())<=f.end_date and f.food_num>0";
                        $moreFood = $model->query($sql);
                        if(!empty($moreFood)){
                            foreach($moreFood as $k => $v){
                                $tpl .= '<div class="listbox">';
                                $tpl .= '<div class="listpic" style=" background-image:url('.$v['food_pic1'].');"></div>';
                                $tpl .= '<div class="listtitlebox myul" style="margin-left:20%">';
                                $tpl .= '<div class="titlebox" >';
                                $tpl .= '<div class="title  ieatXianGou"><span class="titletext">��'.$v['cat_name'].'��'.$v['food_name'].'�Ź��ۣ���'.$v['food_price'].'Ԫ</span></div>';
                                $tpl .= '</div></div></div>';
                            }
                        }
                    }
                }
            }
        }
        $this->display();
    }
    //����
    public function detail(){
        if(isset($_GET['cat_id'])){
            $model = $this->categoryModel;
            if(isset($_GET['ticket_id'])){
                //�Ź�����
                $sql = "select v.virtual_pic,t.ticket_name,t.ticket_des,t.taocan,t.ticket_intro,t.ticket_price,t.start_date,t.end_date,t.other_set,t.ticket_id from tp_ieat_mall_ticket as t RIGHT JOIN tp_ieat_mall_virtual as v ON v.cat_id=t.cat_id WHERE v.token=t.token and t.token='".$this->token."' and t.cat_id=".$_GET['cat_id']." and t.ticket_id=".$_GET['ticket_id'];
                $detailInfo = $model->query($sql);
                if(!empty($detailInfo)){
                    $detailInfo[0]['virtual_pic'] = json_decode($detailInfo[0]['virtual_pic'],true);
                    $detailInfo[0]['other_set'] = json_decode($detailInfo[0]['other_set'],true);
                    $buyNum = M('Ieat_mall_ticket')->where(array('token'=>$this->token,'is_buy'=>1))->count();
                    $sql1 = "select cat_id from tp_ieat_mall_ticket WHERE ticket_name='".$detailInfo[0]['ticket_name']."' and token='".$this->token."'";
                    $sql2 = "select ca.cat_name,a.longitude,a.latitude,a.address,a.tel from tp_ieat_category as ca RIGHT JOIN tp_ieat_mall_address as a ON ca.cat_id=a.cat_id WHERE ca.cat_id in(".$sql1.") and ca.token='".$this->token."'";
                    $mallList = $model->query($sql2);
                    $this->assign('mallList',$mallList);
                    $this->assign('buyNum',$buyNum);
                    $this->assign('pic',$detailInfo[0]['virtual_pic']);
                    $this->assign('detailInfo',$detailInfo);
                }
            }elseif(isset($_GET['food_id'])){
                //��ɫʳ���޹�
                $sql = "select food_id,food_name,food_pic1,food_pic2,food_pic3,food_brief,food_price, food_yunfei,food_des,other_set,taocan,end_date from tp_ieat_mall_food WHERE token='".$this->token."' and food_id=".$_GET['food_id'];
                $foodInfo = $model->query($sql);
                if(!empty($foodInfo)){
                    $foodInfo[0]['other_set'] = json_decode($foodInfo[0]['other_set'],true);
                    $sql1 = "select cat_id from tp_ieat_mall_food WHERE token='".$this->token."' and food_name='".$foodInfo[0]['food_name']."'";
                    $sql2 = "select ca.cat_id,ca.cat_name,a.address,a.tel,a.longitude,a.latitude from tp_ieat_category as ca RIGHT JOIN tp_ieat_mall_address as a ON a.cat_id=ca.cat_id WHERE a.cat_id IN (".$sql1.") and a.token=ca.token and ca.token='".$this->token."'";
                    $mallList = $model->query($sql2);
                    $buyNum = M('Ieat_mall_food_order');
                    $this->assign('buyNum',$buyNum);
                    $this->assign('foodInfo',$foodInfo);
                    $this->assign('mallList',$mallList);
                }
            }
        }
        $this->display();
    }
    //���˳�Ա
    public function unioMember(){
        $sql = "select cat_id from tp_ieat_category WHERE token='".$this->token."' and type=2";
        $sql1 = "select cat_id from tp_ieat_category WHERE token='".$this->token."' and parent_id in (".$sql.")";
        $sql2 = "select cat_id,cat_name,token from tp_ieat_category WHERE token='".$this->token."' and parent_id in (".$sql1.")";
        $sql3 = "select v.virtual_pic,mall.cat_id,mall.cat_name from (".$sql2.") as mall right join tp_ieat_mall_virtual as v on mall.cat_id=v.cat_id where mall.token=v.token and v.token='".$this->token."'";
        $res = $this->categoryModel->query($sql3);
        foreach($res as $k => $v){
            $pic = json_decode($res[$k]['virtual_pic'],true);
            $keys = array_keys($pic);
            $res[$k]['virtual_pic'] = $pic[$keys[0]];
        }
        $this->assign('res',$res);
        $this->display();
    }
    //�������
    public function apply(){
        if(IS_POST){
            if(M('Ieat_apply')->add($_REQUEST)){
                exit(json_encode(array('info'=>'����ɹ�����ȴ���ϵ��','status'=>1)));
            }else{
                exit(json_encode(array('info'=>'����ʧ�ܣ����Ժ�����','status'=>0)));
            }
        }
        $this->display();
    }
    //ȷ��֧��
    public function receiving(){
        $this->display();
    }

    //ȷ��֧��
    public function order_confirm(){
        $this->display();
    }
}
