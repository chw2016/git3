<?php
/**
 * Created by PhpStorm.   深鸽模具（我要加盟）
 * User: zhang
 * Date: 2015/7/21
 * Time: 8:48
 */
class ShengouAction extends BaseAction{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/shengou';

    public function _initialize() {

        parent::_initialize();
    }

    public function join(){
        $list=M('Mru_jm')->where(array('token'=>$this->token))->find();
        $this->assign('list',$list);
        $this->UDisplay('join');
    }

    public function index(){
        $this->UDisplay('index');
    }

    public function datainset(){
        if(IS_POST){
            $_POST['add_time'] = time();
            if(M('Mru_wyjm')->add($_POST)){
                $this->success2('加盟申请成功！',U('Shengou/join',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->erro2('系统繁忙，请稍后...');
            };
        }
    }


    public function yanzheng(){
        if(IS_AJAX){
            $code = $_POST['code'];
            if(M('Shenou_code')->where(array('token'=>$this->token,'code'=>$code))->find()){
                if(M('Shenou_code')->where(array('token'=>$this->token,'code'=>$code,'type'=>0))->find()){
                    if(M('Shenou_code')->where(array('token'=>$this->token,'code'=>$code))->save(array('type'=>1,'openid'=>$this->openid,'use_time'=>('Y-m_d H:i:s')))){
                        $cash = new cash($this->token, 20, $this->openid);
                        $cash->cash_info();
                        $this->success('验证成功,并获取红包一个！');
                    }else{
                        $this->error('验证失败！');
                    }
                }else{
                    $this->error('此验证码已使用过了！');
                }
            }else{
                $this->error('此验证码不存在！');
            }
        }
        $oImgModel = M('Imag');
        $this->assign(array(
            'phone4'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Shengou','type'=>'phones4'))->find(),
        ));
        $redata	  = M('Lottery');
        $info = $redata->where(array('token'=>$this->token,'type'=>2))->order('id desc')->select();
        $infos = $info[0];

        $this->assign('info',$infos);
        $this->UDisplay('yanzheng');
    }






}