<?php
class WeipayappAction extends TableAction
{
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/weipayapp';

    public function _initialize()
    {
       // P($_SESSION);exit;

        $this->_sToken = $_REQUEST['token'];
        /*$Code = new Code($this->_sToken, 201001);
        echo "<img src='".$Code->getLSCode()."' />";*/
        parent::_initialize();
    }

    //一级
    protected function setHeader()
    {
        return array(
            array(
                'name' => '二维码管理',
                'url' => U('Weipayapp/index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '图片管理',
                'url' => U('Weipayapp/phone', array('token' => $this->_sToken))
            ),
            array(
                'name' => '支付流水',
                'url' => U('Weipay/weipayorderlist', array('token' => $this->_sToken))
            ),

        );
    }


    //显示
    public function index()
    {
        $info = M('Weipayapp')
            ->where(array(
                'token'=>$this->_sToken))
            ->find();
        if(!$info){
            $oUsercode = new Code($this->_sToken,201001);
            $img = $oUsercode->getYJCode();
            M('Weipayapp')->add(array(
                'token'=>$this->_sToken,
                'name'=>$_SESSION['name'].'收款二维码',
                'img'=>$img,
                'add_time'=>date('Y-m-d H:i:s')));
        }

        $this->bShowDefault  = true;
        $this->table(
            array(
                'HeadHover' => U('Tailg/index', array('token' => $this->_sToken,'aid'=>$this->_iAid)),//栏目样式
                'Head_Opt' => array(//导航栏下面的按钮部分，需要加的按钮
                    /*array(
                        'name'   => '添加收款二维码',
                        'url'    => U('add')
                    )*/
                ),
                'tips' => array(//3级
                    '您可以在此生成收款二维码'
                ),
                'Table_Header' => array(//4级
                    'ID', '二维码图片', '时间', '操作'
                ),
                'aListImg' => array(
                    'container' => array('img'),
                    'width'     => 100,
                    'height'    => 100
                ),
                'List_Opt' => array(
                  /*  array(
                        'name' => '编辑',
                        'url' => U('Tailg/save_content', array('token' => $_SESSION['token'],'aid'=>$this->_iAid))
                    ),*/
                    array(
                        'type'=>1,
                        'name' => '删除',
                        'url' => U('Weipayapp/delete_app', array('token' => $_SESSION['token'],'aid'=>$this->_iAid))
                    ),
                )
            ),
            M('Weipayapp')->where(array('token' => $this->_sToken))->count(),
            M('Weipayapp')->field('id,img,add_time')->where(array('token' => $this->_sToken))
            // array($this,'abc')
        );
    }

    public function delete_app(){
        $this->del('Weipayapp');
    }

    /**
     * 门店添加
     */
    public function add(){
        $this->add('Rh_store',array(
            array('title'=>"楼盘名称",'type'=>"input",'name'=>"name",'msg'=>'楼盘名称不能为空'),
            array('type'=>'img','many'=>array(
                array('title'=>"产品图片",'name'=>"logo")
            )),
            array('title'=>"楼盘电话号码",'type'=>"input",'name'=>"tel",'msg'=>'楼盘电话号码必填咯！'),
            array('title'=>"楼盘地址选择",'type'=>"address"),
            array('title'=>"楼盘详细地址",'type'=>"input",'name'=>"address",'msg'=>'楼盘详细地址必须填写'),
            array('title'=>"楼盘地图位置",'type'=>"map1",'lng'=>"lng",'lat'=>'lat'),
            array('title'=>"产品清单",'type'=>"textarea",'name'=>'info'),
            array('type'=>"hidden_true",'name'=>"type",'value'=>'2'),
        ),U('houses',array('token'=>$this->token)));
    }

    /*生成前端UI图片*/
    public function phone(){
        $oImgModel = M('Imag');
        $this->assign(array(
            'phone'=>$oImgModel->where(array('token'=>$this->token,'app'=>'Weipayapp','type'=>'phones'))->find(),

        ));
        $this->UDisplay('phone');
    }







}
