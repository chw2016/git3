<?php
/*
 * created by zichao in 2014-09-01
 * Notice:If you want to modify this code,please do note.
 */
class KindergartenAction extends BaseAction {
    /*
     * The function of index will show kindergarten page
     */
    public function index(){
        $m = M('Kindergarten')->where(array('token'=>$this->token,'id'=>$_GET['kgid']))->find();
        $this->assign('m',$m);
        $data = M('Kg_notice')->order('id desc')->where(array('token'=>$this->token,'kgid'=>$_GET['kgid']))->find();
        $d['notice'] = strip_tags(htmlspecialchars_decode($data['notice'],ENT_QUOTES));
        $this->assign('d',$d);
        $this->assign('data',$data);
        $this->display();
    }
    /*
     * Notice
     */
    public function notice(){
        $w['token'] = $_GET['token'];
        $w['kgid'] = $_GET['kgid'];
        $w['id'] = $_GET['notice_id'];
        $data = M('Kg_notice')->order('id desc')->where($w)->find();
        $data['notice'] = htmlspecialchars_decode($data['notice'],ENT_QUOTES);
        $this->assign('data',$data);
        $this->display();
    }
    public function noticelist(){
        $info= M('Kg_notice')->order('id desc')->where(array('token'=>$_GET[token],'kgid'=>$_GET['kgid']))->select();
        foreach($info as $k => $v){
            $info[$k]['notice'] = msubstr(htmlspecialchars_decode($v['notice'],ENT_QUOTES),0,50);
        }
        $this->assign('info',$info);
        $this->display();
    }
    /*
     * This function display some information of kindergarten.
     */
    public function introgarden(){
        $kindergarten = M('Kindergarten');
        $w['id'] = $_GET['kgid'];    
        $w['token'] = $this->token;
        $data = $kindergarten->where(array('id'=>$w['id']))->find();
        $w['userid'] = $data['userid'];
        $data = $kindergarten->where($w)->find();
        $data['kgintro'] = htmlspecialchars_decode($data['kgintro'],ENT_QUOTES);
        $data['kglastedittime'] = date('Y-m-d',$data['kglastedittime']);
        $this->assign('data',$data);
        $this->display();
    }
    
    /*
     * Weekly recipes.
     */
    public function weeklyrecipes(){
        $w['token'] = $this->token;
        $w['kgid'] = $_GET['kgid'];
        $data = M('Kg_recipes')->order('id desc')->where($w)->select();
        $this->assign('data',$data);
        $this->display();
    }
    public function recipes() {
        $w ['kgid'] = $_GET ['kgid'];
        $w ['token'] = $_GET ['token'];
        $w ['id'] = $_GET ['recipes_id'];
        $data = M ( 'Kg_recipes' )->where ( $w )->find ();
        $data['recipes_addtime'] = date('Y-m-d H:i:s',$data['recipes_addtime']);
        $data ['recipes_intro'] = htmlspecialchars_decode ( $data ['recipes_intro'], ENT_QUOTES );
        $this->assign ( 'data', $data );
        $this->display ();
        
    }
    
    /*
     * The team of teachers
     */
    public function ttofteachers(){
        $w['kgid'] = $_GET['kgid'];
        $w['token'] = $this->token;
        $kindergarten = M('Kindergarten');
        $data = $kindergarten->where(array('id'=>$w['kgid']))->find();        
        $w['userid'] = $data['userid'];
        $kgteacher = M('Kgteacher');
        $data = $kgteacher->where($w)->select();
        $a = count($data);
        for($i=0;$i<$a;$i++){
            $data[$i]['declaration'] = msubstr(strip_tags(htmlspecialchars_decode($data[$i]['declaration'],ENT_QUOTES)),0,30,'UTF-8');
        }
        $this->assign('data',$data); 
        $this->display();
    }
    /*
     * Teacher introduction
     */
    public function teacherdetail(){
        $w['uid'] = $this->tpl['uid'];
        $w['kgid'] = $_GET['kgid'];
        $w['id'] = $_GET['teacher_id'];
        $data = M('Kgteacher')->where($w)->find();
        $data['declaration'] = htmlspecialchars_decode($data['declaration'],ENT_QUOTES);
        $data['last_edit_time'] = date('Y-m-d',$data['last_edit_time']);
        $a = M('Kindergarten')->where(array('id'=>$w['kgid']))->find();
        $data['kgname'] = htmlspecialchars_decode($a['kgname'],ENT_QUOTES);
        $this->assign('data',$data);
        $this->display();
    }
    /*
     * Displsy specialcourse
     */
    public function specialcourse(){
        $w['kgid'] = $_GET['kgid'];
        $a = $w['token'] = $this->token;
        $kindergarten = M('Kindergarten');
        $data = $kindergarten->where(array('id'=>$w['kgid']))->find();
        $w['userid'] = $data['userid'];
        $kgspecialcourse = M('Kgspecialcourse');
        $data = $kgspecialcourse->where($w)->select();
        $a = count($data);
        for($i=0;$i<$a;$i++){
            $data[$i]['intro'] = msubstr(strip_tags(htmlspecialchars_decode($data[$i]['intro'],ENT_QUOTES)),0,30,'UTF-8');
        }
        $this->assign('data',$data);
        $this->display();
    }
    /*
     * Display specialcourse detail
     */
    public function specialcoursedetail(){
        $w['uid'] = $this->tpl['uid'];
        $w['kgid'] = $_GET['kgid'];
        $w['id'] = $_GET['course_id'];
        $a['openid'] = $_GET['openid'];
        $this->assign('a',$a);
        $this->assign('w',$w);
        $data = M('Kgspecialcourse')->where($w)->find();
        $data['last_edit_time'] = date('Y-m-d',$data['last_edit_time']);
        $data['intro'] = htmlspecialchars_decode($data['intro'],ENT_QUOTES);
        $this->assign('data',$data);
        $arr = M('Kgmy')->where(array('kgid'=>$w['kgid'],'openid'=>$_GET['openid']))->find();
        if($arr){
            /*
             * If you had add my information,this ciuration just update a predetermined course information.
            */
            $this->assign('arr',$arr);
            if(IS_POST){
                $e['course_id'] = $_POST['course_id'];
                $b = M('Kgspecialcourse')->where(array('id'=>$e['course_id']))->find();
                $e['course_address'] = $a['address'];
                $e['course_time'] = $a['time'];
                $e['course_name'] = $a['name'];
                $e['course_pic'] = $a['pic'];
                $e['addclasstime'] = strtotime(date('Y-m-d H:i:s'));
                $e['remark'] = $_POST['remark'];
                if(M('Kgmy')->where(array('openid'=>$_POST['openid'],'kgid'=>$_POST['kgid']))->save($e)){
                    $this->success('提交成功');
                }else{
                    $this->error('提交失败');
                }
            }
        } else {
            /*
             * If you had not add my infortaion and want to add a predetermined course, there are some field like this:token;uid;userid;kgname...and so on will be filled. Above all automatic filling field are relevant to you predetermined course.
             */
            if (IS_POST) {
                $d ['kgid'] = $_POST ['kgid'];
                $d ['openid'] = $_POST ['openid'];
                $d ['course_id'] = $_POST ['course_id'];
                $d ['name'] = $_POST ['name'];
                $d ['tel'] = $_POST ['tel'];
                $d ['remark'] = $_POST ['remark'];
                $c = M ( 'Kgspecialcourse' )->where ( array ('id' => $d ['course_id'] ) )->find ();
                $d ['course_address'] = $c ['address'];
                $d ['course_time'] = $c ['time'];
                $d ['course_name'] = $c ['name'];
                $d ['course_pic'] = $c ['pic'];
                $d ['addclasstime'] = strtotime ( date ( 'Y-m-d H:i:s' ) );
                $d ['userid'] = $c ['userid'];
                $d ['kgname'] = $c ['kgname'];
                $d ['token'] = $this->token;
                $d ['uid'] = $this->tpl ['uid'];
                // print_r($d);exit();
                if (M ( 'Kgmy' )->where ( array ('openid' => $_POST ['openid'], 'kgid' => $_POST ['kgid'] ) )->add ( $d )) {
                    $this->success ( '提交成功' );
                } else {
                    $this->error ( '提交失败' );
                }
            }
        }
        
        $this->display();
    }
    /*
     * Some book and my information
     */
    public function my(){
   
        $w['kgid'] = $_GET['kgid'];
        $w['openid'] = $_GET['openid'];
        $w['token'] = $this->token;
        //print_r($w);
        $data = M('Kgmy')->where($w)->find();
	
        $this->assign('data',$data);
        //print_r($data);
        $this->display();
    }
    /*
     * My information.
     */
    public function addmy(){
        $w['openid'] = $_GET['openid'];
        $w['kgid'] = $_GET['kgid'];
        //print_r($w);
        $this->assign('w',$w);
        $arr = M('Kgmy')->where($w)->find();
        if(!empty($arr)){
            /*
             * If this kindergarten has been in my information.
             */
            $this->assign('arr',$arr);
            /*
             * Modify my information.
             */
            if(IS_POST){
                $w['openid'] = $_POST['openid'];
                $w['kgid'] = $_POST['kgid'];
                $data['name'] = $_POST['name'];
                $data['tel'] = $_POST['tel'];
                $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                if(M('Kgmy')->where($w)->save($data)) {
                    $this->sucess('修改成功');
                }else{
                    $this->error('修改失败');
                }
            }
        }else{
            /*
             * Add my information.
             */
            if(IS_POST){
                $data['name'] = $_POST['name'];
                $data['tel'] = $_POST['tel'];
                $data['kgid'] = $_POST['kgid'];
                $data['openid'] = $_POST['openid'];
                $data['token'] = $this->token;
                $data['uid'] = $this->tpl['uid'];
                $a = M('Kindergarten')->where(array('id'=>$data['kgid']))->find();
                $data['userid'] = $a['userid'];
                $data['kgname'] = $a['kgname'];
                $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
                //print_r($data);exit();
                if(M('Kgmy')->add($data)){
                    $this->success('提交成功','./index.php?g=Wap&m=Kindergarten&a=my&token='.$this->token.'&openid='.$_GET['openid'].'&kgid='.$data['kgid']);
                }else{
                    $this->error('提交失败','./index.php?g=Wap&m=Kindergarten&a=addmy&token='.$this->token.'&openid='.$_GET['openid'].'&kgid='.$data['kgid']);
                }
            }
        } 
        
        $this->display();
    }
    /*
     * Show my booked course
     */
    public function mycourse(){
        $w['kgid'] = $_GET['kgid'];
        $w['openid'] = $_GET['openid'];
        $w['token'] = $this->token;
        $data = M('Kgmy')->where($w)->select();
        $this->assign('data',$data);
        $this->display();
    }
    /*
     * List the exciting activite
     */
    public function excitingact(){
        $w['kgid'] = $_GET['kgid'];
        $w['token'] = $this->token;
        $kindergarten = M('Kindergarten');
        $data = $kindergarten->where(array('id'=>$w['kgid']))->find();        
        $w['userid'] = $data['userid'];
        $kgexcitingact = M('Kgexcitingact');
        $data = $kgexcitingact->where($w)->select();
        $a = count($data);
        for($i = 0 ; $i < $a ; $i++){
            $data[$i]['intro'] = msubstr(strip_tags(htmlspecialchars_decode($data[$i]['intro'],ENT_QUOTES)),0,30,'UTF-8');
        }
        $this->assign('data',$data);
        $this->display();
    }
    /*
     * Show exciting activite detail
     */
    public function excitingactdetail(){
        $w['uid'] = $this->tpl['uid'];
        $w['kgid'] = $_GET['kgid'];
        $w['id'] = $_GET['act_id'];
        $data = M('Kgexcitingact')->where($w)->find();
        $data['last_edit_time'] = date('Y-m-d',$data['last_edit_time']);
        $data['intro'] = htmlspecialchars_decode($data['intro'],ENT_QUOTES);
        $this->assign('data',$data);
        $this->display();
    }
    /*
     * Show some message what customes can contact us
     */
    public function contactus(){
        $kindergarten = M('Kindergarten');
        $w['id'] = $_GET['kgid'];
        $w['token'] = $this->token;
        $data = $kindergarten->where(array('id'=>$w['id']))->find();
        $w['userid'] = $data['userid'];
        $data = $kindergarten->where($w)->find();
        $data['kglastedittime'] = date('Y-m-d',$data['kglastedittime']);
        $this->assign('data',$data);
        //print_r($data);
        $this->display();
    }
    /*
     * Receive the messahe of custroms feedback
     */
    public function feedback(){
        $kindergarten = M('Kindergarten');
        $a['openid'] = $_GET['openid'];
        $a['kgid'] = $_GET['kgid'];
        $b = $kindergarten->where(array('id'=>$a['kgid']))->find();
        $a['userid'] = $b['userid'];
        $a['kgname'] = $b['kgname'];
        $this->assign('a',$a);
        $arr = M('Kgmy')->where(array('kgid'=>$a['kgid'],'openid'=>$a['openid']))->find();
        if($arr){
            $this->assign('arr',$arr);
        }
        if(IS_POST){
            $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
            $data['name'] = $_POST['name'];
            $data['tel'] = $_POST['tel'];
            $data['content'] = $_POST['content'];
            $data['uid'] = $this->tpl['uid'];
            $data['token'] = $this->token;
            $data['userid'] = $_POST['userid'];
            $data['kgid'] = $_POST['kgid'];
            $data['openid'] = $_POST['openid'];
            $data['kgname'] = $_POST['kgname'];
            if(M('Kgfeedback')->add($data)){
                $this->success();
            }else{
                $this->error();
            }
            $this->assign('data',$data);
        }
        $this->display();
    }
    /*
     * The campus information
     */
    public function campus(){
        $w['id'] = $_GET['kgid'];
        $w['token'] = $this->token;
        $data = M('Kindergarten')->where($w)->find();
        $this->assign('data',$data);
        $this->display();
    }
    
    
    
}