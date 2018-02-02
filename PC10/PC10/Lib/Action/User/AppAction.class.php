<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-7-7
 * Time: 下午2:13
 * To change this template use File | Settings | File Templates.
 */
class AppAction extends UserAction{

    public function index(){
        $cate_id = $this->_get('cate_id');
        $where = array();
        if($cate_id){
            $where['cate_id'] = $cate_id;
        }

        $where['is_open'] = 1;
        $applist = M('App_list')->where($where)->select();
        $user_app_list_model = M('User_app_list');
        foreach($applist as $k=>$v){
            $myapp = array();
            $myapp = $user_app_list_model->where(array('uid'=>session('uid'),'token'=>session('token'),'app_id'=>$v['id']))->find();
            if($myapp){
                $applist[$k]['status'] = $myapp['status'];
                if($myapp['is_pay'] == 1){
                    $applist[$k]['is_pay']  = 1;
                    $applist[$k]['over']  = 0;
                    if($myapp['try_type'] != 0){
                        if($myapp['end_date'] > date("Y-m-d H:i:s",time())){
                            $applist[$k]['over']  = 0;
                        }else{
                            $applist[$k]['over']  = 3;
                        }
                    }else{
                        $applist[$k]['over']  = 0;
                    }
                }else if($myapp['is_pay'] == 0){
                    if($myapp['try_type'] == 0){
                        $applist[$k]['is_pay']  = 1;
                        $applist[$k]['over']  = 0;
                    }else{
                        $applist[$k]['is_pay']  = 2;
                        if($myapp['end_date'] > date("Y-m-d H:i:s",time())){
                            $applist[$k]['over']  = 0;
                        }else{
                            $applist[$k]['over']  = 2;
                        }
                    }
                }
            }else{
                $applist[$k]['is_pay']  = 0;
            }
        }
        $this->assign('cate_id',$cate_id);
        $this->assign('applist',$applist);
        $this->display();
    }

    public function addapp(){
        $uid = $this->_post('uid');
        $app_id = $this->_post('app_id');
        if($uid && $app_id){
            $appdata = M('App_list')->where(array('id'=>$app_id,'is_open'=>1))->find();
            if($appdata['is_free'] == 1){
                if($appdata){
                    $user_app_model =  M('User_app_list');
                    $myapp =$user_app_model->where(array('uid'=>$uid,'app_id'=>$app_id,'token'=>session('token'),'try_type'=>0))->find();//add status=1
               
                    if($myapp){
                            $data=array();
                            $data['status']= 1;
                            $data['add_time']= time();
                            if($user_app_model->where(array('uid'=>$uid,'app_id'=>$app_id,'token'=>session('token'),'try_type'=>0))->save($data)){
                                $this->ajaxReturn(array('code'=>0,'msg'=>'开通成功,进入我的应用即可使用'));
                            }else{
                                $this->ajaxReturn(array('code'=>-3,'msg'=>'开通失败请重试'));
                        }

                    }else{
                        $data=array();
                        $data['uid']= $uid;
                        $data['token']= session('token');
                        $data['app_id']= $app_id;
                        $data['try_type']= $appdata['try_type'];
                        $data['money']= 0;
                        $data['is_pay']= 1;
                        $data['status']= 1;
                        $data['add_time']= time();
                        if($user_app_model->add($data)){
                            $this->ajaxReturn(array('code'=>0,'msg'=>'开通成功,进入我的应用即可使用'));
                        }else{
                            $this->ajaxReturn(array('code'=>-3,'msg'=>'开通失败请重试'));
                        }
                    }
                }else{
                    $this->ajaxReturn(array('code'=>-2,'msg'=>'非法请求'));
                }
            }else{
                $this->ajaxReturn(array('code'=>-4,'msg'=>'功能为收费功能'));
            }
        }else{
            $this->ajaxReturn(array('code'=>-2,'msg'=>'非法请求'));
        }
    }

    public function tryapp(){
        $uid = $this->_post('uid');
        $app_id = $this->_post('app_id');
        $try_type = $this->_post('try_type');
        if($try_type != 0){
            if($uid && $app_id){
                $appdata = M('App_list')->where(array('id'=>$app_id,'is_open'=>1))->find();
                if($appdata['is_free'] != 1){
                    if($appdata){
                        $user_app_model =  M('User_app_list');
                        $myapp =$user_app_model->where(array('uid'=>$uid,'app_id'=>$app_id,'token'=>session('token'),'try_type'=>$try_type))->find();
                        if($myapp){
                            if($myapp['end_date'] > date("Y-m-d H:i:s",time())){
                                if($myapp['is_pay'] == 1){
                                    $this->ajaxReturn(array('code'=>0,'msg'=>'您已开通过此功能,在我的应用中可以正常使用'));
                                }else{
                                    $this->ajaxReturn(array('code'=>-3,'msg'=>'此此项功能的试用期已到,请联系客服购买哦'));
                                }
                            }else{
                                $this->ajaxReturn(array('code'=>-4,'msg'=>'此此项功能的试用期已到,请联系客服购买哦'));
                            }

                        }else{
                            $data=array();
                            $data['uid']= $uid;
                            $data['token']= session('token');
                            $data['app_id']= $app_id;
                            $data['try_type']= $appdata['try_type'];
                            $data['money']= $appdata['price'];
                            $data['is_pay']= 0;
                            $data['status']= 1;
                            $data['add_time']= time();
                            $data['end_date']= date("Y-m-d H:i:s",time()+$try_type*24*3600);
                            if($user_app_model->add($data)){
                                $this->ajaxReturn(array('code'=>0,'msg'=>'开通成功,进入我的应用即可使用'));
                            }else{
                                $this->ajaxReturn(array('code'=>-3,'msg'=>'开通失败请重试'));
                            }
                        }
                    }else{
                        $this->ajaxReturn(array('code'=>-2,'msg'=>'非法请求'));
                    }
                }else{
                    $this->ajaxReturn(array('code'=>-4,'msg'=>'功能为免费功能无需试用'));
                }
            }else{
                $this->ajaxReturn(array('code'=>-2,'msg'=>'非法请求'));
            }
        }else{
            $this->ajaxReturn(array('code'=>-2,'msg'=>'功能为免费功能无需试用'));
        }
    }

    public function dapp(){
        $id = $_REQUEST['id'];
        $app_id = $_REQUEST['app_id'];
        $token = $_REQUEST['token'];
        if($id && $app_id && $token){
           $res =  M('User_app_list')->where(array('id'=>$id,'app_id'=>$app_id,'token'=>$token,'status'=>1))->save(array('status'=>0));
           if($res){
               $this->success('操作成功',U('Home/index'));
           }else{
               $this->error('操作失败',U('Home/index'));
           }
        }else{
            $this->error('操作失败',U('Home/index'));
        }
    }



}