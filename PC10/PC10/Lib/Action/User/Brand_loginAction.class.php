<?php
/*
 * Created by 李铭   2015/4/7
 *
 * 品牌管理员登陆入品
 */
class Brand_loginAction extends BaseAction{
    /*
     * Home page
     *
     */


    public function index(){
        if(IS_POST){
            $w['account'] = $_POST['user_name'];
            $w['pwd'] = md5($_POST['password']);
            $w['token']=$_POST['token'];

            //echo $token;exit();
            $data = M('Product_brand')->where($w)->find();
            if($data){
                //$a = './index.php?g=User&m='.$module.'&a='.$action.'&token='.$token.'&'.$branchid.'='.$data['id'];
                //echo $a;
                session('id',$data['id']);//品牌id
                session('token',$data['token']);
                $this->success('登陆成功','./index.php?g=User&m=Brand&a=first&token'.$_GET['token']);
            }else{
                $this->error('账号或密码错误','./index.php?g=User&m=Brand&a=index&token'.$_GET['token']);
            }    
        }else{
            $this->display();
        }
    }


}