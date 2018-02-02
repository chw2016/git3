<?php
/*
*create by zichao in 2014-09-01
*Notice:If you want to modify this code,please do note.
*/
class KindergartenBranchAction extends UserAction{
    public $kindergarten;
    public $uid;
    public $kgteacher;
    public $kgspecialcourse;
    public $kgexcitingact;
    public $kg_super_meb;
    /*
     * Construct function
     */
    public function _initialize() {
        parent::_initialize();
        $this->kindergarten = M("Kindergarten");
        $this->kgteacher = M('Kgteacher');
        $this->kgexcitingact = M('Kgexcitingact');
        $this->kg_super_meb = M("Kg_super_meb");
        $this->kgspecialcourse = M('Kgspecialcourse');
        $this->uid = session('uid');
        $this->assign('token',$this->token); 
    }
    /*
     * The home page
     */
    public function index(){
        if($this->token == $_GET['token']){
            $w['userid'] = $_GET['userid'];
            $w['id'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $this->assign('w',$w);
            $data = $this->kindergarten->where($w)->find();
            $this->assign('data',$data);
        }
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        }
        $this->display();
    }
    /*
     * notice
     */
    public function notice(){
        if($this->token == $_GET['token']){
            $op = $_GET['op']?$_GET['op']:0;
            $this->assign('op',$op);
            $w['userid'] = $_GET['userid'];
            if($op == 1){
                $w['id'] = $_GET['id'];
                $w['kgid'] = $_GET['kgid'];
                $notice = M('Kg_notice')->where(array('id'=>$_GET['id'],'kgid'=>$_GET['kgid'],'userid'=>$w['userid'],'token'=>$this->token))->find();
                $this->assign('notice',$notice);
            }else{
                $w['kgid'] = $_GET['id'];
            }
            $this->assign('w',$w);
            if(IS_POST){
                $op = $_GET['op']?$_GET['op']:0;
                if($op == 1){
                    $_POST['lastedittime'] = date('Y-m-d H:i:s');
                    //print_r($_POST);exit();
                    if(M('Kg_notice')->where(array('token'=>$this->token,'id'=>$_POST['id'],'userid'=>$_POST['userid']))->save($_POST)){
                        $this->ajaxReturn(array('info'=>'编辑成功','status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=noticelist&token='.$this->token.'&userid='.$_POST['userid'].'&id='.$_POST['kgid']));
                    }else{
                        $this->ajaxReturn(array('info'=>'编辑失败','status'=>0,'url'=>'./index.php?g=User&m=KindergartenBranch&a=notice&token='.$this->token.'&userid='.$_POST['userid'].'&kgid='.$_POST['kgid'].'&id='.$_POST['id']));
                    }
                }elseif($op == 0){
                    $_POST['token'] = $this->token;
                    $_POST['addtime'] = date('Y-m-d H:i:s');
                    $_POST['lastedittime'] = date('Y-m-d H:i:s');
                    //print_r($_POST);exit();
                    if(M('Kg_notice')->add($_POST)){
                        $this->ajaxReturn(array('info'=>'保存成功','status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=noticelist&token='.$this->token.'&userid='.$_POST['userid'].'&id='.$_POST['kgid']));
                    }else{
                        $this->ajaxReturn(array('info'=>'保存失败','status'=>0,'url'=>'./index.php?g=User&m=KindergartenBranch&a=notice&token='.$this->token.'&userid='.$_POST['userid'].'&id='.$_POST['kgid']));
                    }
                }

            }
        }
        $this->display();
    }
    public function noticelist(){
        if($this->token == $_GET['token']){
            $w['userid'] = $_GET['userid'];
            $w['kgid'] = $_GET['id'];
            $this->assign('w',$w);
            $list = M('Kg_notice')->where(array('token'=>$this->token,'userid'=>$w['userid'],'kgid'=>$w['kgid']))->select();
            foreach($list as $k => $v){
                $list[$k]['notice'] = substr(strip_tags(htmlspecialchars_decode($v['notice'])), 0,15) . '...';
            }
            $this->assign('list',$list);
        }
        $this->display();
    }
    public function del_notice(){
        if($this->token == $_GET['token']){
            if(M('Kg_notice')->where(array('token'=>$this->token,'id'=>$_GET['id']))->delete()){
                $this->ajaxReturn(array('info'=>'删除成功','status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=noticelist&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$_GET['kgid']));
            }else{
                $this->ajaxReturn(array('info'=>'删除成功','status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=noticelist&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$_GET['kgid']));
            }
        }
    }
    /*
     * Introduction kindergarten
     */
    public function intro(){
       if($this->token == $_GET['token']){
            $w['id'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $w['userid'] = $_GET['userid'];
            $this->assign('w',$w);
            $info = $this->kindergarten->where($w)->find();
            $info['kgintro'] = $info['kgintro'];
            $this->assign('info',$info);
            if(IS_POST){
                $data['kgintro'] = $_POST['kgintro'];
                //print_r($data);exit();
                $w['id'] = $_POST['id'];
                $w['uid'] = $this->uid;
                $w['userid'] = $_POST['userid'];
                $a = $this->kindergarten->where($w)->find();
                $b['kgintro'] = $a['kgintro'];
                if(empty($b)){
                    if($this->kindergarten->where($w)->add($data)){
                        $this->success('保存成功','./index.php?g=User&m=KindergartenBranch&a=index&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['id']);
                    }else{
                        $this->error('保存失败','./index.php?g=User&m=KindergartenBranch&a=intro&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['id']);
                    }
                }else{
                    if($this->kindergarten->where($w)->save($data)){
                        $this->success('编辑成功','./index.php?g=User&m=KindergartenBranch&a=index&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['id']);
                    }else{
                        $this->error('编辑失败','./index.php?g=User&m=KindergartenBranch&a=intro&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['id']);
                    }
                }
            }
        } 
        $this->display();
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        }
    }
    public function recipes(){
        if($this->token == $_GET['token']){
            $w['userid'] = $_GET['userid'];
            $w['kgid'] = $_GET['id'];
            $w['token'] = $this->token;
            $this->assign('w',$w);
            $data = M('Kg_recipes')->order('id desc')->where($w)->select();
            $a = count($data);
            for($i = 0 ; $i < $a ; $i++){
                $data[$i]['recipes_addtime'] = date('Y-m-d H:i:s',$data[$i]['recipes_addtime']);
            }
            $this->assign('data',$data);
        }
        $this->display();
    }
    /*
     * Introduction the kindergarten of recipes.
     */
    public function recipesintro(){
        if($this->token == $_GET['token']){
            $op = $_GET['op']?$_GET['op']:0;
            $this->assign('op',$op);
            $w['uid'] = $this->uid;
            $w['userid'] = $_GET['userid'];
            if($op == 0){
                $w['kgid'] = $_GET['id'];
            }elseif($op == 1){
                $w['kgid'] = $_GET['kgid'];
                $w['id'] = $_GET['id'];
            }
            $this->assign('w',$w);
            if($op == 1){
                $a['id'] = $_GET['id'];
                $info = M('Kg_recipes')->where($a)->find();
                $info['recipes_addtime'] = date('Y-m-d H:i:s',$info['recipes_addtime']);
                $this->assign('info',$info);
            }
            if(IS_POST){
                $op = $_POST['op']?$_POST['op']:0;
                $data['recipes_intro'] = $_POST['recipes_intro'];
                $data['recipes_time'] = $_POST['recipes_time'];
                $data['kgid'] = $_POST['kgid'];
                $data['userid'] = $_POST['userid'];
                $data['recipes_addtime'] = strtotime(date('Y-m-d H:i:s'));
                $data['uid'] = session('uid');
                $data['token'] = $this->token;
                //print_r($data);exit();
                if($op == 0){
                    if(M('Kg_recipes')->add($data)){
                        $this->ajaxReturn(array('info'=>"保存成功",'status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=recipes&token='.$this->token.'&userid='.$data['userid'].'&id='.$data['kgid']));
                    }else{
                        $this->ajaxReturn(array('info'=>"保存失败",'status'=>0,'url'=>'./index.php?g=User&m=KindergartenBranch&a=recipesintro&token='.$this->token.'&userid='.$data['userid'].'&id='.$data['kgid']));
                    }
                }elseif($op == 1){
                    //print_r($data);exit();
                    if(M('Kg_recipes')->where(array('token'=>$this->token,'userid'=>$data['userid'],'kgid'=>$data['kgid'],'id'=>$_POST['id']))->save($data)){
                        $this->ajaxReturn(array('info'=>"保存成功",'status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=recipes&token='.$this->token.'&userid='.$data['userid'].'&id='.$data['kgid']));
                    }else{
                        $this->ajaxReturn(array('info'=>"保存失败",'status'=>0,'url'=>'./index.php?g=User&m=KindergartenBranch&a=recipesintro&token='.$this->token.'&userid='.$data['userid'].'&id='.$data['kgid']));
                    }
                }
            } 
        }
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        } 
        $this->display();
    }
    public function del_rec(){
        if($this->token == $_GET['token']){
            if(IS_POST){
                $w['userid'] = $_GET['userid'];
                $w['id'] = $_GET['id'];
                $w['kgid'] = $_GET['kgid'];
                $w['token'] = $this->token;
                if(M('Kg_recipes')->where($w)->delete()){
                    $this->ajaxReturn(array('info'=>"删除成功",'status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=recipes&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']));
                }else{
                    $this->ajaxReturn(array('info'=>"删除成功",'status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=recipes&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']));
                }
            }
        }
        $this->display();
    }
    /*
     * List teachers of kindergarten
     */
    public function teacher(){
        if($this->token == $_GET['token']){
            $w['userid'] = $_GET['userid'];
            $w['kgid'] = $_GET['id'];
            $this->assign('w',$w);
            $w['uid'] = $this->uid;
            $teacher = $this->kgteacher->where($w)->select();
            $this->assign('teacher',$teacher);
        }
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        }
        $this->display();
    }
    /*
     * Modify or add teacher
     */
    public function manageteacher(){
        if($this->token == $_GET['token']){
            $op = $_GET['op']?$_GET['op']:0;
            $b['userid'] = $_GET['userid'];
            $this->assign('b',$b);
            if(IS_POST){
                $w['uid'] = $this->uid;
                $w['kgid'] = $_GET['id'];
                $op = $_POST['op']?$_POST['op']:0;
                $data['name'] = $_POST['name'];
                $data['userid'] = $_POST['userid'];
                $data[pic] = $_POST['pic'];
                $data['tel'] = $_POST['tel'];
                $data['declaration'] = $_POST['declaration'];
                $data['post'] = $_POST['post'];
                $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
                $data['uid'] = $this->uid;
                $data['kgid'] = $w['kgid'];
                if($op == 0){
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    $back = $this->kgteacher->where($w)->add($data);
                    if($back == true){
                        $this->success('保存成功','./index.php?g=User&m=KindergartenBranch&a=teacher&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$w['kgid']);
                    }else{
                        $this->error('保存失败','./index.php?g=User&m=KindergartenBranch&a=manageteacher&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$w['kgid']);
                    }
                }elseif($op == 1){
                    $w['id'] = $_POST['teacher_id'];
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    $back = $this->kgteacher->where($w)->save($data);
                    if($back == true){
                        $this->success('保存成功','./index.php?g=User&m=KindergartenBranch&a=teacher&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$w['kgid']);
                    }else{
                        $this->error('保存失败','./index.php?g=User&m=KindergartenBranch&a=manageteacher&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$w['id']);
                    }
                }
            }
            $a['id'] = $_GET['id'];
            $this->assign('a',$a);
            $op = $_GET['op']?$_GET['op']:0;
            $this->assign('op',$op);
            if($op == 1){
                $w['kgid'] = $_GET['id'];
                $w['uid'] = $this->uid;
                $w['id'] = $_GET['teacher_id'];
                $message = $this->kgteacher->where($w)->find();
                $this->assign('message',$message);
            }
        }
        $this->assign('op',$op);
        $this->display();
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        }
    }
    /*
     * Delete teacher
     */
    public function del_teacher(){
        if($this->token == $_GET['token']){
            $w['id'] = $_GET['teacher_id'];
            $w['kgid'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $w['userid'] = $_GET['userid'];
            $isexist = $this->kgteacher->where($w)->find();
            if($isexist == true){
                $back = $this->kgteacher->where($w)->delete();
                if($back == true){
                    $this->success('删除成功','./index.php?g=User&m=KindergartenBranch&a=teacher&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
                }else{
                    $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=teacher&token'.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
                }
            }
        }else{
            $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=teacher&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
        }
    }
    /*
     * List the specialcourse of kindergarten
     */
    public function specialcourse(){
        if($this->token == $_GET['token']){
            $a['userid'] = $_GET['userid'];
            $a['kgid'] = $_GET['id'];
            $this->assign('a',$a);
            $a['uid'] = $this->uid;
            $specialcourse = $this->kgspecialcourse->where($a)->select();
            $this->assign('specialcourse',$specialcourse);
            
        }
        $this->display();
    }
    /*
     * Modify or add specialcourse
     */
    public function managespecialcourse(){
        if($this->token == $_GET['token']){
            $op = $_GET['op']?$_GET['op']:0;
            $a['kgid'] = $_GET['id'];
            $a['userid'] = $_GET['userid'];
            $this->assign('a',$a);
            if(IS_POST){
                $op = $_POST['op']?$_POST['op']:0;
                $w['uid'] = $this->uid;
                $w['kgid'] = $_POST['id'];
                $op = $_POST['op']?$_POST['op']:0;
                $data['name'] = $_POST['name'];
                $data['time'] = $_POST['time'];
                $data['address'] = $_POST['address'];
                $data['intro'] = $_POST['intro'];
                $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
                $data['uid'] = $this->uid;
                $data['kgid'] = $w['kgid'];
                $where['id'] = $w['kgid'];
                $where['uid'] = $this->uid;
                $b = $this->kindergarten->where($where)->find();
                $data['kgname'] = $b['kgname'];
                $data['userid'] = $_POST['userid'];
                $data['pic'] = $_POST['pic'];
                if($op == 0){
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    $back = $this->kgspecialcourse->where($w)->add($data);
                    if($back == true){
                        $this->success('保存成功','./index.php?g=User&m=KindergartenBranch&a=specialcourse&token='.$this->token.'&userid='.$data['userid'].'&id='.$w['kgid']);
                    }else{
                        $this->error('保存失败','./index.php?g=User&m=KindergartenBranch&a=managespecialcourse&token='.$this->token.'&userid='.$data['userid'].'&id='.$w['kgid']);
                    }
                }elseif($op == 1){
                    $w['id'] = $_POST['course_id'];
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    $back = $this->kgspecialcourse->where($w)->save($data);
                    if($back == true){
                        $this->success('编辑成功','./index.php?g=User&m=KindergartenBranch&a=specialcourse&token='.$this->token.'&userid='.$data['userid'].'&id='.$w['kgid']);
                    }else{
                        $this->error('编辑失败','./index.php?g=User&m=KindergartenBranch&a=managespecialcourse&token='.$this->token.'&userid='.$data['userid'].'&id='.$w['id']);
                    }
                }
            }
            if($op == 1){
                $w['kgid'] = $_GET['id'];
                $w['uid'] = $this->uid;
                $w['id'] = $_GET['course_id'];
                $w['userid'] = $_GET['userid'];
                $message = $this->kgspecialcourse->where($w)->find();
                $message['intro'] = $message['intro'];
                $this->assign('message',$message);
                
            }
        }
        $this->assign('op',$op);
        $this->display();
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        }
    }
    /*
     * Delete specialcourse 
     */
    public function del_course(){
        if($this->token == $_GET['token']){
            $w['id'] = $_GET['course_id'];
            $w['kgid'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $w['userid'] = $_GET['userid'];
            $isexist = $this->kgspecialcourse->where($w)->find();
            if($isexist == true){
                $back = $this->kgspecialcourse->where($w)->delete();
                if($back == true){
                    $this->success('删除成功','./index.php?g=User&m=KindergartenBranch&a=specialcourse&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
                }else{
                    $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=specialcourse&token'.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
                }
            }
        }else{
            $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=index&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
        }
    }
    public function check_res(){
        if($this->token == $_GET['token']){
            $w['course_id'] = $_GET['course_id'];
            $w['kgid'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $w['userid'] = $_GET['userid'];
            $this->assign('w',$w);
            $data = M('Kgmy')->where($w)->select();
            foreach($data as $k => $v){
                $data[$k]['addclasstime'] = date('Y-m-d H:i:s',$v['addclasstime']);
            }
            $this->assign('data',$data);
            $this->display();
        }
    }
    public function del_res(){
        if($this->token == $_GET['token']){
            $w['course_id'] = $_GET['course_id'];
            $w['kgid'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $w['userid'] = $_GET['userid'];
            $a = M('Kgmy')->where($w)->find();
            if($a){
                if(M('Kgmy')->where($w)->delete()){
                    $this->success('删除成功','./index.php?g=User&m=KindergartenBranch&a=check_res&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid'].'&course_id='.$w['course_id']);
                }else{
                    $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=check_res&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid'].'&course_id='.$w['course_id']);
                }
            }
            $this->display();
        }
    }
    
    /*
     * List exciting activities
     */
    public function excitingact(){
        if($this->token == $_GET['token']){
            $a['userid'] = $_GET['userid'];
            $a['kgid'] = $_GET['id'];
            $this->assign('a',$a);
            $a['uid'] = $this->uid;
            $kgexcitingact = $this->kgexcitingact->where($a)->select();
            $this->assign('kgexcitingact',$kgexcitingact);
        }
        $this->display();
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        }
    }
    /*
     * Modify or add exciting activities
     */
    public function manageexcitingact(){
        if($this->token == $_GET['token']){
            $op = $_GET['op']?$_GET['op']:0;
            $b['userid'] = $_GET['userid'];
            $this->assign('b',$b);
            if(IS_POST){
                $w['uid'] = $this->uid;
                $w['kgid'] = $_GET['id'];
                $op = $_POST['op']?$_POST['op']:0;
                $data['name'] = $_POST['name'];
                $data['time'] = $_POST['time'];
                $data['pic'] = $_POST['pic'];
                $data['userid'] = $_POST['userid'];
                $data['address'] = $_POST['address'];
                $data['intro'] = $_POST['intro'];
                $data['addtime'] = strtotime(date('Y-m-d H:i:s'));
                $data['uid'] = $this->uid;
                $data['kgid'] = $w['kgid'];
                $where['id'] = $w['kgid'];
                $where['uid'] = $this->uid;
                $b = $this->kindergarten->where($where)->find();
                $data['kgname'] = $b['kgname'];
                if($op == 0){
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    $back = $this->kgexcitingact->where($w)->add($data);
                    if($back == true){
                        $this->success('保存成功','./index.php?g=User&m=KindergartenBranch&a=excitingact&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$w['kgid']);
                    }else{
                        $this->error('保存失败','./index.php?g=User&m=KindergartenBranch&a=manageexcitingact&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$w['kgid']);
                    }
                }elseif($op == 1){
                    $w['id'] = $_POST['act_id'];
                    $data['last_edit_time'] = strtotime(date('Y-m-d H:i:s'));
                    $back = $this->kgexcitingact->where($w)->save($data);
                    if($back == true){
                        $this->success('保存成功','./index.php?g=User&m=KindergartenBranch&a=excitingact&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$w['kgid']);
                    }else{
                        $this->error('保存失败','./index.php?g=User&m=KindergartenBranch&a=manageexcitingact&token='.$this->token.'&userid='.$_GET['userid'].'&id='.$w['id']);
                    }
                }
            }
            $a['kgid'] = $_GET['id'];
            $this->assign('a',$a);
            $op = $_GET['op']?$_GET['op']:0;
            $this->assign('op',$op);
            if($op == 1){
                $w['kgid'] = $_GET['id'];
                $w['uid'] = $this->uid;
                $w['id'] = $_GET['act_id'];
                $w['userid'] = $_GET['userid'];
                $message = $this->kgexcitingact->where($w)->find();
                $message['intro'] = $message['intro'];
                $this->assign('message',$message);
            }
        }
        $this->assign('op',$op);
        $this->display();
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        }
    }
    /*
     * Delete activities
     */
    public function del_act(){
        if($this->token == $_GET['token']){
            $w['id'] = $_GET['act_id'];
            $w['kgid'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $w['userid'] = $_GET['userid'];
            $isexist = $this->kgexcitingact->where($w)->find();
            if($isexist == true){
                $back = $this->kgexcitingact->where($w)->delete();
                if($back == true){
                    $this->success('删除成功','./index.php?g=User&m=KindergartenBranch&a=excitingact&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
                }else{
                    $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=excitingact&token'.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
                }
            }
        }else{
            $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=index&token='.$this->token.'&userid='.$w['userid'].'&id='.$w['kgid']);
        }
    }
    /*
     * Display kindergarten
     */
    public function show(){
        if($this->token == $_GET['token']){
            $b['userid'] = $_GET['userid'];
            session('userid',$b['userid']);
            $this->assign('b',$b);
            $w['token'] = $this->token;
            $data = $this->kindergarten->where(array('token'=>$this->token,'userid'=>$b['userid']))->select();
            $a = count($data);
            $this->assign('a',$a);
            $count = $this->kindergarten->where(array('token'=>$this->token,'userid'=>$b['userid']))->count();
            $page = new Page($count,8);
            $info = $this->kindergarten->where(array('token'=>$this->token,'userid'=>$b['userid']))->limit($page->firstRow.','.$page->listRows)->select();
            $this->assign('page',$page->show());
            $this->assign('info',$info);
            $this->assign('data',$data);
            $is_vip = $this->kg_super_meb->where(array('token'=>$w['token'],'id'=>$b['userid']))->find();
            $this->assign('is_vip',$is_vip);
            /* if($_GET['userid'] !== session('userid')){
                $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
            }   */        
        }
        $this->display();
    }
    /*
     * Delete kindergarten
     * WARING:When someone delete a kindergarten,
     * THE associated with the kindergarten table also be cleared. 
     */
    public function del(){
        if($this->token == $_GET['token']){
            $w['userid'] = $_GET['userid'];
            $w['id'] = $_GET['id'];
            $w['uid'] = $this->uid;
            $isexist = $this->kindergarten->where($w)->find();
            if($isexist == true){
                $back = $this->kindergarten->where($w)->delete();
                if($back == true){
                    /*
                     * At the same time delete 
                     * the Kgteacher table,the Kgspecialcourse table,the Kgexcitingact table,the Kgmy table and the Kgfeedback table
                     * information where relevant to Kindergarten table
                     */
                    M('Kgteacher')->where(array('uid'=>$w['uid'],'kgid'=>$w['id'],'userid'=>$w['userid']))->delete();
                    M('Kgspecialcourse')->where(array('uid'=>$w['uid'],'userid'=>$w['userid'],'kgid'=>$w['id']))->delete();
                    M('Kgexcitingact')->where(array('uid'=>$w['uid'],'userid'=>$w['userid'],'kgid'=>$w['id']))->delete();
                    M('Kgmy')->where(array('uid'=>$w['uid'],'userid'=>$w['userid'],'kgid'=>$w['id']))->delete();
                    M('Kgfeedback')->where(array('uid'=>$w['uid'],'userid'=>$w['userid'],'kgid'=>$w['id']))->delete();
                    $this->success('删除成功','./index.php?g=User&m=KindergartenBranch&a=show&token='.$this->token.'&userid='.$w['userid']);
                }else{
                    $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=show&token'.$this->token.'&userid='.$w['userid']);
                }
            }
        }else{
            $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=index&token='.$this->token.'&userid='.$w['userid']);
        }
    }
    /*
     * Modify or add kindergarten
     */
    public function manage(){
        if($this->token == $_GET['token']){
            $a['userid'] = $_GET['userid'];
            $this->assign('a',$a);
            $op = $_GET['op']?$_GET['op']:0;
            if(IS_POST){
                $op = $_POST['op']?$_POST['op']:0;
                $data['userid'] = $_POST['userid'];
                $data['kgname'] = $_POST['kgname'];
                $data['kgtel'] = $_POST['kgtel'];
                $data['retel'] = $_POST['retel'];
                $data['kgbusinessstarthours'] = $_POST['kgbusinessstarthours'];
                $data['kgbusinessendhours'] = $_POST['kgbusinessendhours'];
                $data['kgaddress'] = $_POST['kgaddress'];
                $data['kgpic'] = $_POST['kgpic'];
                $data['twocode'] = $_POST['twocode'];
                $data['longitude'] = $_POST['longitude'];
                $data['latitude'] = $_POST['latitude'];
                $data['kgaddtime'] = strtotime(date('Y-m-d H:i:s'));
                $data['uid'] = $this->uid;
                $data['token'] = $this->token;
                if($op == 0){
                    if($this->kindergarten->where(array('userid'=>$_POST['userid'],'token'=>$this->token))->find()){
                        $this->ajaxReturn(array('info'=>'不能添加两个幼儿园！','status'=>2));
                    }else{
                        $data['kglastedittime'] = $data['kgaddtime'];
                        //print_r($data);exit();
                        if($this->kindergarten->add($data)){
                            //print_r($data);exit();
                            $this->success('添加成功','index.php?g=User&m=KindergartenBranch&a=show&token='.$this->token.'&userid='.$_POST['userid']);
                        } else {
                            $this->error('添加失败', 'index.php?g=User&m=KindergartenBranch&a=manage&op=0&token='.$this->token.'&userid='.$_POST['userid']);
                        }
                    }
                }elseif($op == 1){
                    $w['id'] = $_POST['id'];
                    $w['uid'] = $this->uid;
                    $data['kglastedittime'] = strtotime(date('Y-m-d H:i:s'));
                    if ($this->kindergarten->where($w)->save($data)) {
                        $this->success('编辑成功','./index.php?g=User&m=KindergartenBranch&a=show&token='.$this->token.'&userid='.$_POST['userid']);
                    } else {
                        $this->error('编辑失败', './index.php?g=User&m=KindergartenBranch&a=manage&op=1&token='.$this->token.'&id='.$_POST['id']);
                    }
                }
            }
            /*
             * When the manage modify kindergarten information the qr code be created.
             * This qr code contain kgid;userid;openid;token.
             */
            if ($op == 1) {
                $w['id'] = $_GET['id'];
                $w['uid'] = $this->uid;
                $this->create();
                $branch = $this->kindergarten->where($w)->find();
                $this->assign('branch',$branch);
                $this->assign('uid',$this->uid);
            }
        }
        $this->assign('op',$op);
        $this->display();
        if($_GET['userid'] !== session('userid')){
            $this->redirect('./index.php?g=User&m=Branch&a=index&token='.$this->token.'&modulename=Kg_super_meb');
        }
        
    }
    /*
     * About qr code
     */
    public function creatTicket($token, $parament) {
        /*发送数据到微信服务器端并获取数据*/
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;
        $result = $this->api_notice_increment($url, $parament);
        $jsonInfo = json_decode($result, true);
        $ticket = $jsonInfo['ticket'];
        /*根据ticket获取图片资源*/
        $url2 = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url2);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpInfo = curl_getinfo($ch);
        return array_merge(array('body'=>$package), array('header'=>$httpInfo));
    }
    /*
     * Create qr code
     */
    public function create(){
        $w['userid'] = $_GET['userid'];
        $w['uid'] = $this->uid;
        $w['id'] = $_GET['id'];
        $data = $this->kindergarten->where($w)->find();
        //echo $data['id'];
        $parament = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 13'.$data['id'].'}}}';
        $api=M('Diymen_set')->where(array('token'=>$this->token))->find();
        if($api){
            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$api['appid'].'&secret='.$api['appsecret'];
            $ch = curl_init();
            $header = "Accept-Charset: utf-8";
            curl_setopt($ch, CURLOPT_URL, $url_get);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOBODY, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $package = curl_exec($ch);
            $json = json_decode($package);
            $access_token = $json->access_token;
            $imgSource = $this->creatTicket($access_token, $parament);
        }
        $this->assign('imgUrl', $imgSource['header']['url']);
    }
    /*
     * List feedback message
     */
    public function feedback(){
        if($this->token == $_GET['token']){
            $w['token'] = $this->token;
            $w['userid'] = $_GET['userid'];
            $w['kgid'] = $_GET['id'];
            $this->assign('w',$w);
            $count = M('Kgfeedback')->where($w)->count();
			$page=new Page($count,10);
			$info = M('Kgfeedback')->where($w)->limit($page->firstRow.','.$page->listRows)->select();
			$a = count($info);
			for($i=0;$i<$a;$i++){
			    $info[$i]['addtime'] = date('Y-m-s H:i:s',$info[$i]['addtime']);
			}
			$this->assign('info',$info);
			$this->assign('page',$page->show());
        } 
        $this->display();
    }
    public function del_fb(){
        if($this->token == $_GET['token']){
            if(IS_POST){
                $w['userid'] = $_GET['userid'];
                $w['id'] = $_GET['id'];
                $W['uid'] = session('uid');
                $w['kgid'] = $_POST['kgid'];
                $a = M('Kgfeedback')->where($w)->find();
                if($a){
                    $b = M('Kgfeedback')->where($w)->delete();
                    if($b == true){
                        $this->success('删除成功','./index.php?g=User&m=KindergartenBranch&a=feedback&token='.$this->token.'&id='.$_POST['kgid'].'&userid='.$w['userid']);
                    }else{
                        $this->error('删除失败','./index.php?g=User&m=KindergartenBranch&a=feedback&token='.$this->token.c);
                    }
                }
            }
        }
    }

    /*
     * Show detail information about vip
     */
    public function vip(){
        if($this->token == $_GET['token']){
            $b['userid'] = $_GET['userid'];
            $this->assign('b',$b);
            $vipdes = $this->kg_super_meb->where(array('id'=>$b['userid'],'token'=>$this->token))->find();
            $vipdes['seniormember'] = htmlspecialchars_decode($vipdes['seniormember'],ENT_QUOTES);
            $vipdes['ordinarymember'] = htmlspecialchars_decode($vipdes['ordinarymember'],ENT_QUOTES);
            $this->assign('vipdes',$vipdes);
            $this->display();
        }
    }
    
    /*
     * Change password
     */
    public function  changepassword(){
        if($this->token == $_GET['token']){
            $w['id'] = $_GET['userid'];
            $w['kgid'] = $_GET['id'];
            $this->assign('w',$w);
            $meb = $this->kg_super_meb->where($w)->find();
            $this->assign('meb',$meb);
            if(IS_POST){
                $w['id'] = $_POST['id'];
                $d['pwd'] = $_POST['npassword'];
                $d['password'] = md5($d['pwd']);
                if($this->kg_super_meb->where($w)->save($d)){
                    $this->ajaxReturn(array('info'=>"修改成功",'status'=>1,'url'=>'./index.php?g=User&m=KindergartenBranch&a=show&token='.$this->token.'&userid='.$w['id'].'&id='.$_POST['kgid']));
                }else{
                    $this->ajaxReturn(array('info'=>"修改失败",'status'=>0,'url'=>'./index.php?g=User&m=KindergartenBranch&a=changepassword&token='.$this->token.'&userid='.$w['userid'].'&id='.$_POST['kgid']));
                }
                
            }
        }
        $this->display();
    }

    
    
    
    
}
