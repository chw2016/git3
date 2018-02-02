<?php
/*
 * Created by 李铭   2015/4/7
 *
 * 品牌管理员登陆入品
 */
class BrandAction extends BaseAction{
    /*
     * Home page
     *
     */
   public function _initialize(){
       parent::_initialize();
        if(!session('id')){
            $this->error2("页面不存在", U('Brand_login/index',array('token'=>$_GET['token'])));
            exit();
        }else{

        }
    }
    /**
     * 品牌管理首页
     */
    public function first(){
        $model = M('Product_abrand');
        $where['bid'] = session('id');
        $count = $model->where($where)->count();
        $Page = new Page($count, 20);
        $show = $Page->show();
        $list = $model->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->order('sort desc,addtime desc')->select();


        $this->assign('page', $show);
        $this->assign('list', $list);

         $this->display();
    }

    /**
     * 添加品牌文章
     */
    public function add_article(){
        if(IS_POST){
            $_POST['addtime']=time();
            $_POST['bid']=session('id');
            $model=M('Product_abrand');
            $model->create();
            if($model->add()){
                $this->success2("添加成功", U('first'));
            }else{
                $this->error2("添加失败", U('first'));
            }
        }else{
            $this->display();
        }

    }

    /**
     * 修改文章
     */
    public function edit_article(){
        $model=M('Product_abrand');
        if(IS_POST){
            $model->create();
            if($model->save()){
                $this->success2("修改成功", U('first'));
            }else{
                $this->error2("修改失败", U('first'));
            }
        }else{

            $id=$this->_get('id');
            $article=$model->find($id);

            $this->assign('article',$article);
            $this->display();
        }

    }
    /**
     *  删除文章
     */
    public function del_article(){
        $model=M('Product_abrand');
        $id=$this->_get('id');
        if($model->delete($id)){
            $this->success2("删除成功", U('first'));
        }else{
            $this->error2("删除失败", U('first'));
        }
    }
    /**
     * 品牌设置
     */
    public function set(){
        if (IS_POST) {
            $_POST['token'] = session('token');
            $opwd=M('Product_brand')->where(array('id'=>$_POST['id']))->getField('pwd');
            if($_POST['pwd']!=$opwd){
                $_POST['pwd']=MD5($_POST['pwd']);
            }
            if (M('Product_brand')->save($_POST)) {
                $this->success2("修改成功", U('first'));

            } else {
                $this->error2("修改成功", U('first'));
            }
        }else {
            $catList=M('Product_cat_new')->where(array('token'=>session('token'),'parentid'=>0))->select();
            $this->assign('catList', $catList);
            $tg_shop = M('Product_brand')->find(session('id'));
            $this->assign('tg', $tg_shop);
            $this->display();
        }
    }

}