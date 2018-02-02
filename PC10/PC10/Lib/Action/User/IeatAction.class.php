<?php

/**
 * Created by PhpStorm.
 * User: SuperMan
 * Date: 2014/11/10
 * Time: 8:50
 */
class IeatAction extends UserAction {
    //public $Category;
    public function _initialize() {
        parent::_initialize();
        //$this->Category = new CategoryModel();
    }

    //栏目展示首页
    public function category() {
        $Category = new CategoryModel();
        $cats = $Category->selectAll();

        $cats = $Category->getCatTree($cats);
        //P($cats);exit;
        $this->assign('cats', $cats);
        foreach ($cats as $k => $v) {
            if ($v['type'] > 0) {
                $arr[$k] = $v['type'];
            }
        }
        $arr = array_unique($arr);
        $this->assign('arr', $arr);

        $this->display();
    }

    //添加栏目
    public function addcat() {

        $Category = new CategoryModel();

        $op = $_GET['op'] ? $_GET['op'] : 0;
        $this->assign('op', $op);
        if ($op == 1) {
            $cat = $Category->findOne($_GET['cat_id']);

            $this->assign('cat', $cat);
        }
        $cats = $Category->selectAll();

        $cats = $Category->getCatTree($cats);

        $this->assign('cats', $cats);

        if (IS_POST) {
            
            $op = $_POST['op'] ? $_POST['op'] : 0;
            if ($op == 0) {
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                $_POST['token'] = $this->token;
                if (M('Ieat_category')->add($_POST)) {
                    $this->ajaxReturn(array('info' => '保存成功', 'status' => 1, 'url' => 'index.php?g=User&m=Ieat&a=category&token=' . $this->token));
                } else {
                    $this->ajaxReturn(array('info' => '保存失败', 'status' => 0));
                }
            } elseif ($op == 1) {

                // $trees = $Category->getTree($_POST['parent_id']);
                // // 判断自身是否在新父栏目的家谱树里面
                // $flag = true;
                // foreach ($trees as $v) {
                //     if ($v['cat_id'] == $_POST['cat_id']) {
                //         $flag = false;
                //         $this->ajaxReturn(array('info' => '编辑失败!不合理的操作！', 'status' => 2));
                //     }
                // }
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');


                if (M('Ieat_category')->where(array('token' => $this->token, 'cat_id' => $_POST['cat_id']))->save($_POST)) {
                    $this->ajaxReturn(array('info' => '编辑成功', 'status' => 1, 'url' => 'index.php?g=User&m=Ieat&a=category&token=' . $this->token));
                } else {
                    $this->ajaxReturn(array('info' => '编辑失败', 'status' => 0));
                }
            }
        }
        $this->display();
    }

    //删除栏目
    public function del_cat() {
        $Category = new CategoryModel();
        if (IS_POST) {
            $sons = $Category->findAll($_GET['cat_id']);
            if (!empty($sons)) {
                $this->ajaxReturn(array('info' => '删除失败！该栏目下面有子栏目,不能删除', 'status' => 0));
            } else {
                if (M('Ieat_category')->where(array('token' => $this->token, 'cat_id' => $_GET['cat_id']))->delete()) {
                    $this->ajaxReturn(array('info' => '删除成功', 'status' => 1, 'url' => 'index.php?g=User&m=Ieat&a=category&token=' . $this->token));
                } else {
                    $this->ajaxReturn(array('info' => '删除失败！该栏目下面有子栏目,不能删除', 'status' => 0));
                }
            }
        }
    }
    //查看申请
    public function apply(){
        $list = M('Ieat_apply')->where(array('token'=>$this->token))->order('id DESC')->select();
        $this->assign('list',$list);
        $this->display();
    }

    //分配控制器
    public function divide() {
        if ($_GET['type'] == 1) {
            //专题文章
            $this->redirect('User/Ieat/subjectarticle', array('token' => $this->token));
        } elseif ($_GET['type'] == 2) {
            //店铺内容
            $this->redirect('User/Ieat/mall', array('token' => $this->token));
        }
    }

    //专题文章
    public function subjectarticle() {
        $Category = new CategoryModel();
        $subject = $Category->selectAll();
        foreach ($subject as $k => $v) {
            if ($v['type'] == 1) {
                $arr[$k] = $v['cat_id'];
            }
        }
        //$a = $this->getCatTree($subject,9);
        //print_r($arr);
        foreach ($arr as $v) {
            $a[] = $Category->getCatTree($subject, $v);

        }
        foreach ($a as $v) {
            foreach ($v as $value) {
                $array[] = $value;
            }
        }
        $this->assign('array', $array);
        $this->display();
    }

    //编辑专题文章
    public function articlelist() {
        $this->assign('get', $_GET);
        $articleList = M('Ieat_subject_article')->where(array(
            'token' => $this->token,
            'cat_id' => $_GET['cat_id']
        ))->select();
        $this->assign('articleList', $articleList);
        $this->display();
    }

    //添加文章
    public function editarticle() {
        $op = $_GET['op'] ? $_GET['op'] : 0;
        $this->assign('op', $op);
        $this->assign('get', $_GET);
        if ($op == 1) {
            $article = M('Ieat_subject_article')->where(array('token' => $this->token, 'article_id' => $_GET['article_id']))->find();
        }
        $this->assign('article', $article);
        if (IS_POST) {
            $op = $_POST['op'] ? $_POST['op'] : 0;
            $_POST['token'] = $this->token;
            $_POST['last_edit_time'] = date('Y-m-d H:i:s');
            if ($op == 1) {
                //print_r($_POST);exit;
                if (M('Ieat_subject_article')->where(array('token' => $this->token, 'article_id' => $_POST['article_id']))->save($_POST)) {
                    $this->ajaxReturn(array('info' => '编辑成功', 'status' => 1, 'url' => 'index.php?&g=User&m=Ieat&a=articlelist&token=' . $this->token . '&cat_id=' . $_POST['cat_id']));
                } else {
                    $this->ajaxReturn(array('info' => '编辑失败', 'status' => 0));
                }
            } elseif ($op == 0) {
                $_POST['add_time'] = date('Y-m-d H:i:s');
                if (M('Ieat_subject_article')->add($_POST)) {
                    $this->ajaxReturn(array('info' => '添加成功', 'status' => 1, 'url' => 'index.php?&g=User&m=Ieat&a=articlelist&token=' . $this->token . '&cat_id=' . $_POST['cat_id']));
                } else {
                    $this->ajaxReturn(array('info' => '添加失败', 'status' => 0));
                }
            }
        }
        $this->display();
    }

    public function del_article() {
        if (IS_POST) {
            if (M('Ieat_subject_article')->where(array('token' => $this->token, 'article_id' => $_GET['article_id']))->find()) {
                if (M('Ieat_subject_article')->where(array('token' => $this->token, 'article_id' => $_GET['article_id']))->delete()) {;
                    $this->ajaxReturn(array('info' => '删除成功', 'status' => 1, 'url' => 'index.php?g=User&m=Ieat&a=articlelist&token=' . $this->token . '&cat_id=' . $_GET['cat_id']));
                } else {
                    $this->ajaxReturn(array(
                        'info' => '删除失败',
                        'status' => 0
                    ));
                }
            } else {
                $this->ajaxReturn(array(
                    'info' => '非法操作',
                    'status' => 0
                ));
            }
        }
    }

    //店铺内容
    public function mall() {
        $Category = new CategoryModel();
        $catList = $Category->select();
        foreach ($catList as $k => $v) {
            if ($v['type'] == 2) {
                $mallList[$k] = $v['cat_id'];
            }
        }
        //print_r($mallList);
        foreach ($mallList as $v) {
            $a[] = $Category->getSon($v);
        }
        //print_r($a);
        foreach ($a as $v) {
            foreach ($v as $value) {
                $arr[] = $value;
            }
        }
        foreach ($arr as $k => $v) {
            $arr[$k]['mall'] = $Category->getSon($v['cat_id']);
        }
        $this->assign('arr', $arr);
        $this->display();
    }

    /*
     * 商家内容展示
     */
    public function mallcontent() {
        $this->assign('get', $_GET);
        $this->display();
    }

    public function brand_story() {
        $this->assign('get', $_GET);
        if (M('Ieat_mall_brand_story')->where(array('token' => $this->token, 'cat_id' => $_GET['cat_id']))->find()) {
            $story = M('Ieat_mall_brand_story')->where(array('token' => $this->token, 'cat_id' => $_GET['cat_id']))->find();
        }
        $this->assign('story', $story);
        if (IS_POST) {
            $_POST['token'] = $this->token;
            $_POST['add_time'] = date('Y-m-d H:i:s');
            if (M('Ieat_mall_brand_story')->where(array('token' => $this->token, 'cat_id' => $_POST['cat_id']))->find()) {
                if (M('Ieat_mall_brand_story')->where(array('token' => $this->token, 'cat_id' => $_POST['cat_id']))->save($_POST)) {
                    $this->ajaxReturn(array('info' => '编辑成功', 'status' => 1, 'url' => 'index.php?g=User&m=Ieat&a=mallcontent&token=' . $this->token . '&cat_id=' . $_POST['cat_id']));
                } else {
                    $this->ajaxReturn(array('info' => '编辑失败', 'status' => 0));
                }
            } else {
                if (M('Ieat_mall_brand_story')->add($_POST)) {
                    $this->ajaxReturn(array('info' => '保存成功', 'status' => 1, 'url' => 'index.php?g=User&m=Ieat&a=mallcontent&token=' . $this->token . '&cat_id=' . $_POST['cat_id']
                    ));
                } else {
                    $this->ajaxReturn(array('info' => '保存失败', 'status' => 0));
                }
            }
        }
        $this->display();
    }

    /*
     * 商家地址
     */
    public function mall_address() {
        $this->assign('get', $_GET);
        if (M('Ieat_mall_address')->where(array('token' => $this->token, 'cat_id' => $_GET['cat_id']))->find()) {
            $mall = M('Ieat_mall_address')->where(array('token' => $this->token, 'cat_id' => $_GET['cat_id']))->find();
        }

        $this->assign('mall', $mall);
        if (IS_POST) {

            $_POST['token'] = $this->token;
            if (M('Ieat_mall_address')->where(array('token' => $this->token, 'cat_id' => $_POST['cat_id']))->find()) {
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if (M('Ieat_mall_address')->where(array('token' => $this->token, 'cat_id' => $_POST['cat_id']))->save($_POST)) {
                    $this->ajaxReturn(array('info' => '编辑成功', 'status' => 1, 'url' => 'index.php?&g=User&m=Ieat&a=mallcontent&token=' . $this->token . '&cat_id=' . $_POST['cat_id']));
                } else {
                    $this->ajaxReturn(array('info' => '编辑失败', 'status' => 0));
                }
            } else {
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if (M('Ieat_mall_address')->add($_POST)) {
                    $this->ajaxReturn(array('info' => '保存成功', 'status' => 1, 'url' => 'index.php?&g=User&m=Ieat&a=mallcontent&token=' . $this->token . '&cat_id=' . $_POST['cat_id']));
                } else {
                    $this->ajaxReturn(array('info' => '保存失败', 'status' => 0));
                }
            }
        }
        //地区
        unset($map);
        $map['type'] = 3;
        $areaArr = M('Ieat_category')->field('cat_id,cat_name')->where($map)->select();
        $data['areaArr'] = $areaArr;
        $this->assign($data);

        $this->display();
    }

    /*
     * 商家实景图
     */
    public function virtual(){
        $this->assign('get', $_GET);
        $virtual = M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->find();
        if($virtual){
            $virtual['virtual_pic'] = json_decode($virtual['virtual_pic'],true);
            $this->assign('virtual',$virtual['virtual_pic']);
        }
        $this->display();
    }
    public function mall_virtual() {
        $this->assign('get', $_GET);
        if (IS_POST) {  //ajax传过来
            if (!empty($_FILES)) {
                import('ORG.Net.UploadFile');
                $upload = new UploadFile();// 实例化上传类
                $upload->maxSize = 512000;// 设置附件上传大小
                $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');//设置上传文件类型
                $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = __ROOT__ . 'upload/' . $this->token . '/image/' . date('Ymd') . '/';// 设置附件上传目录
                if (!$upload->upload()) {    // 上传错误提示错误信息
                    $this->error($upload->getErrorMsg());
                } else {                      // 上传成功 获取上传文件信息
                    $info = $upload->getUploadFileInfo();
                }
                $virtual = M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->find();
                if($virtual){
                    $virtual['virtual_pic'] = json_decode($virtual['virtual_pic'],true);
                    array_push($virtual['virtual_pic'],$info[0]['savepath'].$info[0]['savename']);
                    $virtual['virtual_pic'] = json_encode($virtual['virtual_pic']);
                    $virtual['last_edit_time'] = date('Y-m-d H:i:s');
                    if(M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'virtual_id'=>$virtual['virtual_id']))->save($virtual)){
                        echo $info[0]['savepath'].$info[0]['savename'];exit;
                    }
                }else{
                    $arr = array();
                    $arr[] = $info[0]['savepath'].$info[0]['savename'];
                    $arr = json_encode($arr);
                    $virtual['virtual_pic'] = $arr;
                    $virtual['cat_id'] = $_GET['cat_id'];
                    $virtual['token'] = $this->token;
                    $virtual['add_time'] = date('Y-m-d H:i:s');
                    $virtual['last_edit_time'] = date('Y-m-d H:i:s');
                    if(M('Ieat_mall_virtual')->add($virtual)){
                        echo $info[0]['savepath'].$info[0]['savename'];exit;
                    }
                }
            }
        } else {
            $this->display();
        }
    }
    public function del_virtual(){
        if(IS_POST){
            if(M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$_POST['cat_id']))->find()){
                $virtual = M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$_POST['cat_id']))->find();
                $virtual['virtual_pic'] = array_diff(json_decode($virtual['virtual_pic'],true),$_POST);
                $virtual['virtual_pic'] = json_encode($virtual['virtual_pic']);
                if(M('Ieat_mall_virtual')->where(array('token'=>$this->token,'cat_id'=>$_POST['cat_id']))->save($virtual)){
                    $this->ajaxReturn(array('info'=>'删除成功！','status'=>1));
                }else{
                    $this->ajaxReturn(array('info'=>'删除失败！','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'非法操作！','status'=>-1));
            }
        }
    }

    /*
     * 招牌菜
     */
    public function dish() {
        $this->assign('get', $_GET);
        $allDish = M('Ieat_mall_dish')->where(array(
                'token' => $this->token,
                'cat_id' => $_GET['cat_id']
            ))->select();
        foreach ($allDish as $v) {
            //echo $v['dish_brief'];exit;
            $v['dish_brief'] = strip_tags($v['dish_brief']);
        }
        $this->assign('allDish', $allDish);
        $a = count($allDish);
        $this->assign('a',$a);
        $this->display();
    }

    public function dishedit() {
        $this->assign('get', $_GET);
        $op = $_GET['op'] ? $_GET['op'] : 0;
        $this->assign('op', $op);
        if ($op == 1) {
            $dish = M('Ieat_mall_dish')->where(array(
                    'token' => $this->token,
                    'cat_id' => $_GET['cat_id'],
                    'dish_id' => $_GET['dish_id']
                ))->find();
        }
        $this->assign('dish', $dish);
        if (IS_POST) {
            $_POST['token'] = $this->token;
            if(!is_numeric($_POST['dish_price'])){
                $this->ajaxReturn(array('info'=>'价格不合法！','status'=>-1));
            }
            $op = $_POST['op'] ? $_POST['op'] : 0;
            if ($op == 1) {
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if (M('Ieat_mall_dish')->where(array(
                        'token' => $this->token,
                        'cat_id' => $_POST['cat_id'],
                        'dish_id' => $_POST['dish_id']
                    ))->save($_POST)
                ) {
                    $this->ajaxReturn(array(
                            'info' => '编辑成功',
                            'status' => 1,
                            'url' => 'index.php?&g=User&m=Ieat&a=dish&token=' . $this->token . '&cat_id=' . $_POST['cat_id']
                        ));
                } else {
                    $this->ajaxReturn(array(
                            'info' => '编辑失败',
                            'status' => 0
                        ));
                }
            } elseif ($op == 0) {
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if (M('Ieat_mall_dish')->add($_POST)) {
                    $this->ajaxReturn(array(
                            'info' => '保存成功',
                            'status' => 1,
                            'url' => 'index.php?&g=User&m=Ieat&a=dish&token=' . $this->token . '&cat_id=' . $_POST['cat_id']
                        ));
                } else {
                    $this->ajaxReturn(array(
                            'info' => '保存失败',
                            'status' => 0
                        ));
                }
            }
        }
        $this->display();
    }
    public function  del_dish(){
        if(IS_POST){
            if(M('Ieat_mall_dish')->where(array('token'=>$this->token,'dish_id'=>$_GET['dish_id']))->find()){
                if(M('Ieat_mall_dish')->where(array('token'=>$this->token,'dish_id'=>$_GET['dish_id']))->delete()){
                    $this->ajaxReturn(array('info'=>'删除成功','status'=>1,'url'=> 'index.php?&g=User&m=Ieat&a=dish&token=' . $this->token . '&cat_id=' . $_GET['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'删除失败','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'非法操作','status'=>-1));
            }
        }
    }
    /*
     * 消费者留言墙
     */
    public function levmsg(){
        $this->assign('get', $_GET);
        $allMsg = M('Ieat_mall_levmsg')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->select();
       
        $this->assign('allMsg',$allMsg);
        $this->display();
    }
    public function del_msg(){
        if(IS_POST){
            if(M('Ieat_mall_levmsg')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'id'=>$_GET['msg_id']))->find()){
                if(M('Ieat_mall_levmsg')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'id'=>$_GET['msg_id']))->delete()){
                    $this->ajaxReturn(array('info'=>'删除成功！','status'=>1,'url'=>'index.php?&g=User&m=Ieat&a=levmsg&token='.$this->token.'&cat_id='.$_GET['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'删除失败！','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'非法操作！','status'=>-1));
            }
        }
    }
    /*
     * 最新优惠及活动
     */
    //活动
    public function act(){
        $this->assign('get', $_GET);
        $this->display();
    }
    //活动文本介绍
    public function actintro(){
        $this->assign('get', $_GET);
        if (M('Ieat_mall_act')->where(array('token' => $this->token, 'cat_id' => $_GET['cat_id']))->find()) {
            $act = M('Ieat_mall_act')->where(array('token' => $this->token, 'cat_id' => $_GET['cat_id']))->find();
        }
        $this->assign('act', $act);
        if (IS_POST) {
            $_POST['token'] = $this->token;
            if (M('Ieat_mall_act')->where(array('token' => $this->token, 'cat_id' => $_POST['cat_id']))->find()) {
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if (M('Ieat_mall_act')->where(array('token' => $this->token, 'cat_id' => $_POST['cat_id']))->save($_POST)) {
                    $this->ajaxReturn(array('info' => '编辑成功', 'status' => 1, 'url' => 'index.php?g=User&m=Ieat&a=act&token=' . $this->token . '&cat_id=' . $_POST['cat_id']));
                } else {
                    $this->ajaxReturn(array('info' => '编辑失败', 'status' => 0));
                }
            } else {
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if (M('Ieat_mall_act')->add($_POST)) {
                    $this->ajaxReturn(array('info' => '保存成功', 'status' => 1, 'url' => 'index.php?g=User&m=Ieat&a=act&token=' . $this->token . '&cat_id=' . $_POST['cat_id']));
                } else {
                    $this->ajaxReturn(array('info' => '保存失败', 'status' => 0));
                }
            }
        }
        $this->display();
    }
    //团购券展示页面
    public function ticket(){
        $this->assign('get',$_GET);
        $tickets = M('Ieat_mall_ticket')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->select();
        $this->assign('tickets',$tickets);
        $this->display();
    }
    //编辑团购券
    public function editticket(){
        $this->assign('get',$_GET);
        $op = $_GET['op']?$_GET['op']:0;
        if($op == 1){
            $ticket = M('Ieat_mall_ticket')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'ticket_id'=>$_GET['ticket_id']))->find();
            $ticket['other_set'] = json_decode($ticket['other_set'],true);
        }
        $this->assign('ticket',$ticket);
        $this->assign('op',$op);
        if(IS_POST){
            $_POST['other_set'] = json_encode(array('sstk'=>$_POST['sstk'],'gqtk'=>$_POST['gqtk']));
            $op = $_POST['op']?$_POST['op']:0;
            $_POST['token'] = $this->token;
            if($op == 1){
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                //print_r($_POST);exit;
                if(M('Ieat_mall_ticket')->where(array('token'=>$this->token,'cat_id'=>$_POST['cat_id'],'ticket_id'=>$_POST['ticket_id']))->save($_POST)){
                    $this->ajaxReturn(array('info'=>'编辑成功','status'=>1,'url'=>'index.php?&g=User&m=Ieat&a=ticket&token='.$this->token.'&cat_id='.$_POST['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'编辑失败','status'=>0));
                }
            }
            elseif($op == 0){
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if(M('Ieat_mall_ticket')->add($_POST)){
                    $this->ajaxReturn(array('info'=>'保存成功','status'=>1,'url'=>'index.php?&g=User&m=Ieat&a=ticket&token='.$this->token.'&cat_id='.$_POST['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'保存失败','status'=>0));
                }
            }
        }
        $this->display();
    }
    //删除团购券
    public function del_ticket(){
        if(IS_POST){
            if(M('Ieat_mall_ticket')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'ticket_id'=>$_GET['ticket_id']))->find()){
                if(M('Ieat_mall_ticket')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'ticket_id'=>$_GET['ticket_id']))->delete()){
                    $this->ajaxReturn(array('info'=>'删除成功','status'=>1,'url'=>'index.php?g=User&m=Ieat&a=ticket&token='.$this->token.'&cat_id='.$_GET['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'删除失败','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'非法操作','status'=>-1));
            }
        }
    }
    /*
     * 特色食物限购
     */
    public function food(){
        $this->assign('get',$_GET);
        $aWhere = array();
        if($_GET['cat_id']){
            $sBackUrl = U('Ieat/mallcontent',array('token'=>$this->token, 'cat_id' => $_GET['cat_id']));
            $aWhere = array('cat_id'=>$_GET['cat_id']);
        }else{
            $sBackUrl = U('Ieat/category',array('token'=>$this->token));
        }
        $this->assign('backUrl', $sBackUrl);
        $iCount = M('Ieat_mall_food')
            ->where(array_merge(array('token'=>$this->token),$aWhere))
            ->count();
        $Page   = new Page($iCount,2);
        $foods = M('Ieat_mall_food')
            ->where(array_merge(array('token'=>$this->token),$aWhere))
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        if($foods){
            $this->assign('foods',$foods);
        }
        $this->assign('page',$Page->show());
        $this->display();
    }
    public function editfood(){
        $this->assign('get',$_GET);
        $op = $_GET['op']?$_GET['op']:0;
        if($op == 1){
            $food = M('Ieat_mall_food')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'food_id'=>$_GET['food_id']))->find();
            $food['other_set'] = json_decode($food['other_set'],true);
        }
        $this->assign('food',$food);
        $this->assign('op',$op);
        if(IS_POST){
            $_POST['other_set'] = json_encode(array('sstk'=>$_POST['sstk'],'gqtk'=>$_POST['gqtk']));
            $op = $_POST['op']?$_POST['op']:0;
            $_POST['token'] = $this->token;
            if($op == 1){
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                //print_r($_POST);exit;
                if(M('Ieat_mall_food')->where(array('token'=>$this->token,'cat_id'=>$_POST['cat_id'],'food_id'=>$_POST['food_id']))->save($_POST)){
                    $this->ajaxReturn(array('info'=>'编辑成功','status'=>1,'url'=>'index.php?&g=User&m=Ieat&a=food&token='.$this->token.'&cat_id='.$_POST['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'编辑失败','status'=>0));
                }
            }
            elseif($op == 0){
                $_POST['add_time'] = date('Y-m-d H:i:s');
                $_POST['last_edit_time'] = date('Y-m-d H:i:s');
                if(M('Ieat_mall_food')->add($_POST)){
                    $this->ajaxReturn(array('info'=>'保存成功','status'=>1,'url'=>'index.php?&g=User&m=Ieat&a=food&token='.$this->token.'&cat_id='.$_POST['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'保存失败','status'=>0));
                }
            }
        }
        $this->display();
    }
    public function del_food(){
        $uriArr = $this->_request();
      
        if(IS_POST){
            if(M('ieat_mall_food')->where(array('token'=>$this->token,'cat_id'=>$uriArr['cat_id'],'food_id'=>$uriArr['food_id']))->find()){
                if(M('ieat_mall_food')->where(array('token'=>$this->token,'cat_id'=>$uriArr['cat_id'],'food_id'=>$uriArr['food_id']))->delete()){
                    $this->ajaxReturn(array('info'=>'删除成功！','status'=>1,'url'=>'index.php?&g=User&m=Ieat&a=food&token='.$this->token.'&cat_id='.$uriArr['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'删除失败！','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'非法操作！','status'=>-1));
            }
        }
    }
    /*
     * 评分
     */
    public function score(){
        $this->assign('get',$_GET);
        $scores = M('Ieat_mall_score')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id']))->select();
        if($scores){
            $sql = "select AVG(enviroment),AVG(service),AVG(taste),AVG(ieat_index) from tp_ieat_mall_score WHERE token='".$this->token."' and cat_id=".$_GET['cat_id'];
            $avgscore = M('Ieat_mall_score')->query($sql);
            $this->assign('avgscore',$avgscore);
            $this->assign('scores',$scores);
        }
        $this->display();
    }
    public function del_score(){
        if(IS_POST){
            if(M('Ieat_mall_score')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'score_id'=>$_GET['score_id']))->find()){
                if(M('Ieat_mall_score')->where(array('token'=>$this->token,'cat_id'=>$_GET['cat_id'],'score_id'=>$_GET['score_id']))->delete()){
                    $this->ajaxReturn(array('info'=>'删除成功！','status'=>1,'url'=>'index.php?&g=User&m=Ieat&a=score&token='.$this->token.'&cat_id='.$_GET['cat_id']));
                }else{
                    $this->ajaxReturn(array('info'=>'删除失败！','status'=>0));
                }
            }else{
                $this->ajaxReturn(array('info'=>'非法操作！','status'=>-1));
            }
        }
    }
    //出售记录
    public function recorder(){
        $this->assign('get',$_GET);
        $list = M('Ieat_mall_food_order')->where(array(
            'token'=>$this->token,
            'cat_id'=>$_GET['cat_id']
        ))->select();
        $this->assign('list',$list);
        $this->display();
    }
    //处理
    public function deal(){
        $res = M('Ieat_mall_food_order')->where(array('token'=>$this->token,'food_id'=>$_GET['food_id']))->setField(array('is_used'=>1,'use_time'=>date('Y-m-d H:i:s')));
        if($res){
            $this->redirect('Ieat/recorder',array('token'=>$this->token));
        }
    }









    #问卷卷调查
    public function investigation(){
        $postArr = $this->_request();
       
        if(IS_POST){
           // dump($postArr);exit;
            //Ieat信息发掘
            //新增
            $inNameArr = $postArr['in_name'][-1];
            $inSelect = $postArr['in_select'][-1];
            $inValue = $postArr['in_value'][-1];
          
            if(!empty($inNameArr)){
                $SQL = array();
                foreach ($inNameArr as $key => $value) {
                   
                    if($value){
                        $SQL[] = array(
                            "name"=>$value,
                            'select'=>$inSelect[$key],
                            'token'=>trim($postArr['token']),
                            'cat_id'=>intval($postArr['cat_id']),
                            'value'=>trim($inValue[$key])

                        );
                    }
                }unset($key);unset($value);
              
                if(!empty($inNameArr)){
                    M('ieat_mall_investigation')->addAll($SQL);
                }
            }

            //更新
            $_inNameArr = $postArr['in_name'];
            $_inSelectArr = $postArr['in_select'];
            $_inValue = $postArr['in_value'];
            if(!empty($_inNameArr)){
                foreach ($_inNameArr as $key => $value) {

                if($key!=-1){
                        unset($map);
                        unset($data);
                        $map['id'] =  $key;
                        $data['name'] = $value;
                        $data['select'] = $_inSelectArr[$key];
                        $data['value'] = $_inValue[$key];
                        
                        M('ieat_mall_investigation')->where($map)->save($data);
                    }
                }unset($key);unset($value);
            }
            
            //十项评分问卷
            //新增
            $tenNameArr = $postArr['ten_name'][-1];
            $tenSelectArr = $postArr['ten_select'][-1];
            $tenDescArr = $postArr['ten_des'][-1];
            $tenPicArr = $postArr['ten_pic'][-1];
            $tenValueArr = $postArr['ten_value'][-1];

            if(!empty($tenNameArr)){
                $SQL = array();
                foreach ($tenNameArr as $key => $value) {
                    if($value){
                        $SQL[] = array(
                            'token'=>trim($postArr['token']),
                            'cat_id'=>trim($postArr['cat_id']),
                            'name'=>trim($value),
                            'select'=>trim($tenSelectArr[$key]),
                            'pic'=>trim($tenPicArr[$key]),
                            'des'=>trim($tenDescArr[$key]),
                            'value'=>trim($tenValueArr[$key])

                        );
                    }
                }unset($key);unset($value);
                M('ieat_mall_ten')->addAll($SQL);
            }

            //更新

            $_tenNameArr = $postArr['ten_name'];
            $_tenSelectArr = $postArr['ten_select'];
            $_tenDesArr = $postArr['ten_des'];
            $_tenTenPicArr = $postArr['ten_pic'];
            $_tenValueArr = $postArr['ten_value'];
          
            if(!empty($_tenNameArr)){
                foreach ($_tenNameArr as $key => $value) {
                    if($key!=-1){
                        unset($map);
                        unset($data);
                        $map['id'] =  $key;
                        $data['name'] = $value;
                        $data['select'] = $_tenSelectArr[$key];
                        $data['pic'] = $_tenTenPicArr[$key];
                        $data['des'] = $_tenDesArr[$key];
                        $data['value'] = $_tenValueArr[$key];
                       
                        M('ieat_mall_ten')->where($map)->save($data);
                    }
                }unset($key);unset($value);
            }

            //主管横向评分问卷
            $subNameArr = $postArr['sub_name'][-1];
            $subSelectArr = $postArr['sub_select'][-1];

            if(!empty($subNameArr)){
                $SQL = array();
                foreach ($subNameArr as $key => $value) {
                    $SQL[] = array(
                        'token'=>$postArr['token'],
                        'cat_id'=>$postArr['cat_id'],
                        'name'=>$value,
                        'select'=>$subSelectArr[$key]
                    );
                }unset($key);unset($value);
                if(!empty($SQL)){
                    M("ieat_mall_subjective")->addAll($SQL);
                }
            }

            //更新
            $_subNameArr = $postArr['sub_name'];
            $_subSelectArr = $postArr['sub_select'];
 
            if(!empty($_subNameArr)){
                foreach ($_subNameArr as $key => $value) {

                if($key!=-1){
                        unset($map);
                        unset($data);
                        $map['id'] =  $key;
                        $data['name'] = $value;
                        $data['select'] = $_subSelectArr[$key];
                    
                        M('ieat_mall_subjective')->where($map)->save($data);
                    }
                }unset($key);unset($value);

                //问卷标题
                $questName = $postArr['sub_value'];
                
                if(!empty($questName)){
                    unset($map);
                
                    $map['token'] = $postArr['token'];
                    $map['cat_id'] = $postArr['cat_id'];
                    $map['name'] = -1;
                    $map['select'] = -1;

                    $info = M('Ieat_mall_subjective')->where($map)->find();

                    if(!empty($info)){
                        $map['token'] = $postArr['token'];
                        $map['cat_id'] = $postArr['cat_id'];
                        $map['name'] = -1;
                        $map['select'] = -1;
                        $data['value'] = $questName;
                        M('Ieat_mall_subjective')->where($map)->save($data);

                       
                    }else{
                        unset($map);
                        unset($data);
                        $data['value'] = $questName;
                        $data['token'] = $postArr['token'];
                        $data['cat_id'] = $postArr['cat_id'];
                        $data['name'] = -1;
                        $data['select'] = -1;

                        M('Ieat_mall_subjective')->add($data);
                    }

                    
                }

            }



            $out = array('status'=>1,'info'=>"操作成功");
            $this->ajaxReturn($out);
        }

        //Ieat信息发掘
        unset($map);
        $map['token'] = trim($postArr['token']);
        $map['cat_id'] = trim($postArr['cat_id']);
        $investigation = M('ieat_mall_investigation')->field('id,token,cat_id,name,select,value')->where($map)->select();
        $data['investigation'] = $investigation;
        //十项评分问卷
        $ten = M('ieat_mall_ten')->field('id,token,cat_id,name,select,pic,des,value')->where($map)->select();
        $data['ten'] = $ten;
        // //主管横向评分问卷
        $map['name']  = array('neq',-1);
        $map['select']  = array('neq',-1);

        $subjective = M("ieat_mall_subjective")->field('id,token,cat_id,name,select')->where($map)->select();
        $data['subjective'] = $subjective;
        //问卷名
        unset($map);
        $map['token'] = trim($postArr['token']);
        $map['cat_id'] = trim($postArr['cat_id']);
        $map['name'] = -1;
        $map['select'] = -1;
        $info = M("ieat_mall_subjective")->field('value')->where($map)->find();
        $data['info'] = $info;

        $this->assign($data);
        $this->display();
    }

    #问卷调查 删除
    public function deltrue(){
        $uriArr = $this->_request();
        $id = $uriArr['id'];
        $key = $uriArr['key'];

        if(empty($id) OR empty($key)){
            $out = array('status'=>-1,'info'=>"页面错误");
        }

        switch ($key) {
            case 'msg':
                $map['id'] = $id;
                M('ieat_mall_investigation')->where($map)->delete();
                $out = array('status'=>1,'info'=>'删除成功');
                break;
            case 'ten':
                $map['id'] = $id;
                M('ieat_mall_ten')->where($map)->delete();
                $out = array('status'=>1,'info'=>'删除成功');
                break;
            case 'x':
                $map['id'] = $id;
                M('ieat_mall_subjective')->where($map)->delete();
                $out = array('status'=>1,'info'=>'删除成功');
                break;
            default:
                $out = array('status'=>-1,'info'=>"页面错误");
                break;
        }

       
        $this->ajaxReturn($out);

    }

    public function uploadIcon(){
        //如果是POST上传图片
        $Model = M('Ieat_icon');
        if(IS_POST){
            $aRet  = array();
            if($_POST['op'] == 1) {//update
                $bUp = $Model->data(array('icon' => $_POST['icon']))->where(array(
                    'token' => $_POST['token'],
                    'type'  => 1
                ))->save();
            }else{
                $bUp = $Model->add(array(
                    'token' => $_POST['token'],
                    'type'  => 1,
                    'icon'  => $_POST['icon']
                ));
            }
            if($bUp){
                $aRet = array(
                    'status' => 0,
                    'info' =>'上传成功',
                    'url' => U('Ieat/mallcontent',
                    array('token' => $_POST['token']))
                );
            }else{
                $aRet = array('status' => -1, 'info' =>'系统繁忙，请稍后再试');
            };
            $this->ajaxReturn($aRet);
        }else{
            $aIcon = $Model->where(array('token' => $this->token, 'type' => 1))->select();
            if(isset($aIcon[0])) { $aIcon = $aIcon[0]; }
            $this->assign('icon', $aIcon);
            $this->assign('token', $this->token);
            $this->display();
        }
    }


    public function iconDelete(){
        $aRet  = array();
        $Model = M('Ieat_icon');
        if(
            $Model->delete(array(
                'token' => $_POST['token'],
                'type'  => 1,
                'icon'  => $_POST['icon']
            ))
        ){
            $aRet = array(
                'status' => 0,
                'info' =>'删除成功',
                'url' => U('Ieat/mallcontent',
                array('token' => $_POST['token']))
            );
        }else{
            $aRet = array('status' => -1, 'info' =>'系统繁忙，请稍后再试');
        };
        $this->ajaxReturn($aRet);
    }


}
