<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/9
 * Time: 15:39
 */
class GroupBuyAction extends Table1Action
{
    /**
     *  定义项目模板BaseDir
     **/
    public $_sTplBaseDir = 'User/default/miye';
    public $tpl_dir = './tpl/User/default/groupbuy/';


    public function _initialize()
    {
        $this->bShowDefault = true;
        if ($_GET['catid']) {
            $_SESSION['catid'] = $_GET['catid'];
        }
        $this->catid = $_SESSION['catid'];
        parent::_initialize();
    }

    //一级
    protected function setHeader()
    {
        return array(
            array(
                'name' => '团购分类管理',
                'url' => U('Store_new/index')
            ),
            array(
                'name' => '团购商品管理',
                'url' => U('index')
            ),
            array(
                'name' => '开团',
                'url' => U('group')
            ),
            array(
                'name' => '订单管理',
                'url' => U('order')
            ),
            array(
                'name' => '添加电影院',
                'url' => U('movie')
            ),
            array(
                'name' => '系统配置',
                'url' => U('play')
            ),
        );
    }

    public function movie()
    {
        $this->table(
            array(
                'HeadHover' => U('movie'),
                'Head_Opt' => array(
                    array(
                        'name' => '添加影院',//2级
                        'url' => U('addMovie')
                    )
                ),
                'tips' => array(//3级
                    '电影院管理'
                ),
                'Table_Header' => array(//4级
                    'ID', '影院名称', '地址', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('editMovie')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('deleteMovie')
                    ),
                )
            ),
            M('group_movie')->count(),
            M('group_movie')->field('id,name,address')
        );
        $this->UDisplay('show1');
    }

    public function deleteMovie()
    {
        $this->del('group_movie');
    }

    public function editMovie()
    {

        $this->Edit('group_movie',array(
            array('title'=>'名称','type'=>'input','name'=>'name','msg'=>'请填写名称'),
            array('title'=>'地址','type'=>'input','name'=>'address','msg'=>'请填写地址'),
            array('title'=>'管理用户','type'=>'text','name'=>'movie_admin'),
            array('title'=>'管理密码','type'=>'password','name'=>'movie_password','msg'=>'请填写管理密码'),
            array('title'=>'经纬度','type'=>'map1','lat'=>'latitude', 'lng' => 'longtitude','msg'=>'请选择影院地址'),
        ),U('movie'),array($this,'editMovie_POST_DATA'));
/*=======
        $this->Edit('group_movie', array(
            array('title' => '名称', 'type' => 'input', 'name' => 'name', 'msg' => '请填写名称'),
            array('title' => '地址', 'type' => 'input', 'name' => 'address', 'msg' => '请填写地址'),
            array('title' => '经纬度', 'type' => 'map1', 'lat' => 'latitude', 'lng' => 'longtitude', 'msg' => '请选择影院地址'),
        ), U('movie'), array($this, 'addMovie_POST_DATA'));
>>>>>>> .r7123*/
    }

    public function addMovie()
    {

        $this->add('group_movie',array(
            array('title'=>'名称','type'=>'input','name'=>'name','msg'=>'请填写名称'),
            array('title'=>'地址','type'=>'input','name'=>'address','msg'=>'请填写地址'),
            array('title'=> '管理用户','type'=>'input','name'=>'movie_admin','msg'=>'请填写管理员用户名' ),
            array('title'=>'管理密码','type'=>'password','name'=>'movie_password','msg'=>'请填写管理密码'),
            array('title'=>'经纬度','type'=>'map1','lat'=>'latitude', 'lng' => 'longtitude','msg'=>'请选择影院地址'),
        ),U('movie'),array($this,'addMovie_POST_DATA'));
/*=======
        $this->add('group_movie', array(
            array('title' => '名称', 'type' => 'input', 'name' => 'name', 'msg' => '请填写名称'),
            array('title' => '地址', 'type' => 'input', 'name' => 'address', 'msg' => '请填写地址'),
            array('title' => '经纬度', 'type' => 'map1', 'lat' => 'latitude', 'lng' => 'longtitude', 'msg' => '请选择影院地址'),
        ), U('movie'), array($this, 'addMovie_POST_DATA'));
>>>>>>> .r7123*/
    }

    public function addMovie_POST_DATA($data)
    {
        $group_movie = M('group_movie');
        $serch['movie_admin'] = $data['movie_admin'];

        if($group_movie->where($serch)->find())
            $this->error2('管理用户已存在');

        if(strlen($data['movie_password'])>20)
            $this->error2('密码不能超过20位');

        $data['movie_password'] = md5($data['movie_password']);

        return $data + array(
            'token' => $this->token,
        );
    }

    public function editMovie_POST_DATA($data)
    {
        $group_movie = M('group_movie');
        $serch['movie_admin'] = $data['movie_admin'];

        if(!$group_movie->where($serch)->find())
            $this->error2('不可修改管理用户');

        if(strlen($data['movie_password'])==32)
             $data['movie_password'] = $data['movie_password'];


        $data['movie_password'] = md5($data['movie_password']);

        return $data + array(
            'token'     => $this->token,
        );
    }


    public function play()
    {
        $this->Edit('group_setting', array(
            array('title' => "拼团玩法", 'type' => 'textarea', 'name' => 'play'),
            array('title' => "优惠券使用方法", 'type' => 'textarea_1', 'name' => 'yhj'),
        ), U('index'), array($this, 'savePlay_Data'));

    }

    public function savePlay_Data($data)
    {
        if ($aData = M('group_setting')->where(array(
            'token' => $this->token
        ))->find()
        ) {
            unset($aData['id']);
            $data = array_merge($aData, $data);
        }
        return $data + array(
            'token' => $this->token
        );
    }

    /*
     *  开团
     */
    public function group()
    {
        $this->table(
            array(
                'HeadHover' => U('group'),
                'Head_Opt' => array(
                    array(
                        'name' => '下载',//2级
                        'url' => U('kaitxiaz')
                    )
                ),
                'tips' => array(//3级
                    '开团管理'
                ),
                'Table_Header' => array(//4级
                    'ID', '开团人', '团购商品', '状态', '开团时间', '团购人数', '已售人数', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看',
                        'url' => U('grouuiep')
                    ),
                )
            ),
            M('group_info')->count(),
            M('group_info')->field('id,openid,product_id,status,add_time, tnumber'),
            array($this, 'groupInfo')
        );
        $this->UDisplay('show1');
    }

    /*这是开团查看*/
    public function grouuiep()
    {
        $id = $_GET['id'];
        $this->table(
            array(
                'HeadHover' => U('group'),
                'Head_Opt' => array(),
                'tips' => array(//3级
                    '开团管理'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称', '团购商品', '购买数量', '使用优惠券id', '是否使用余额付款', '应付金额', '剩余支付金额', '订单状态', 'sn码'
                ),
                'List_Opt' => array(/* array(
                        'name' => '删除',
                        'url' => U('')
                    ),*/
                )
            ),
            M('group_order')->count(),
            M('group_order')->where(array('gid' => $_GET['id']))
                ->field('id,openid,product_id,number,yhjid,use_yu_er,total,need_total,use_yu_er,status,sn'),
            array($this, 'orderInfock')

        );
        $this->UDisplay('show1');
    }

    /*订单管理*/
    public function order()
    {
        $this->table(
            array(
                'HeadHover' => U('order'),
                'Head_Opt' => array(
                    array(
                        'name' => '下载',//2级
                        'url' => U('dingdanxz')
                    )
                ),
                'tips' => array(//3级
                    '订单管理'
                ),
                'Table_Header' => array(//4级
                    'ID', '商品', '开团数量', '购买数量', '是否使用余额', '是否用优惠券', '总金额', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看',
                        'url' => U('ddchak')
                    ),
                )
            ),
            M('group_order')->count(),
            M('group_order')->field('id, product_id, tnumber, number, use_yu_er, yhjid, total'),
            array($this, 'orderInfo')
        );
        $this->UDisplay('show1');
    }

    /*订单管理查看*/
    public function ddchak()
    {

        $this->table(
            array(
                'HeadHover' => U('order'),
                'Head_Opt' => array(),
                'tips' => array(//3级
                    '订单管理'
                ),
                'Table_Header' => array(//4级
                    'ID', '微信昵称', '团购商品', '购买数量', '使用优惠券id', '是否使用余额付款', '应付金额', '剩余支付金额', '订单状态', 'sn码'
                ),
                'List_Opt' => array(/* array(
                        'name' => '查看',
                        'url' => U('')
                    ),*/
                )
            ),
            M('group_order')->count(),
            M('group_order')->where(array('id' => $_GET['id']))
                ->field('id,openid,product_id,number,yhjid,use_yu_er,total,need_total,use_yu_er,status,sn'),
            array($this, 'orderInfock')
        );
        $this->UDisplay('show1');
    }

    /*开团查看*/
    public function orderInfock($aData)
    {
        if ($aPro = array_filter(array_keys(Arr::changeIndex($aData, 'product_id')))) {
            $aProInfo = Arr::changeIndexToKVMap(M('groupbuy_product')->where(array(
                'id' => array('in', $aPro)
            ))->field('id, name')->select(), 'id', 'name');
        };
        foreach ($aData as $k => $v) {
            if ($v['status'] == 0) {
                $aData[$k]['status'] = '未付款';
            } elseif ($v['status'] == 1) {
                $aData[$k]['status'] = '已付款';
            }
            $aData[$k]['yhjid'] = $v['yhjid'] ? '是' : '否';
            $aData[$k]['use_yu_er'] = $v['use_yu_er'] ? '是' : '否';
            $aData[$k]['openid'] = Arr::get($aUserInfo, $v['openid']);
            $aData[$k]['product_id'] = Arr::get($aProInfo, $v['product_id'], '');
        }

        return $aData;
    }

    public function orderInfo($aData)
    {
        if ($aPro = array_filter(array_keys(Arr::changeIndex($aData, 'product_id')))) {
            $aProInfo = Arr::changeIndexToKVMap(M('groupbuy_product')->where(array(
                'id' => array('in', $aPro)
            ))->field('id, name')->select(), 'id', 'name');
        };
        foreach ($aData as $k => $v) {
            $aData[$k]['yhjid'] = $v['yhjid'] ? '是' : '否';
            $aData[$k]['use_yu_er'] = $v['use_yu_er'] ? '是' : '否';
            $aData[$k]['product_id'] = Arr::get($aProInfo, $v['product_id'], '');
        }
        return $aData;
    }


    public function groupInfo($aData)
    {
        if ($aOpenid = array_filter(array_keys(Arr::changeIndex($aData, 'opendid')))) {
            $aUserInfo = Arr::changeIndexToKVMap(M('Wxusers')->where(array(
                'openid' => array('in', $aOpenid)
            ))->field('openid, name')->select(), 'openid', 'name');
        };
        if ($aProId = array_filter(array_keys(Arr::changeIndex($aData, 'product_id')))) {
            $aProInfo = Arr::changeIndexToKVMap(M('groupbuy_product')->where(array(
                'id' => array('in', $aProId)
            ))->field('id, name')->select(), 'id', 'name');
        };
        //已售人数
        if ($aId = array_filter(array_keys(Arr::changeIndex($aData, 'product_id')))) {
            $OrderInfo = Arr::changeIndexToKVMap(M('Group_order')
                ->where(array(
                    'product_id' => array('in', $aId),
                    'token' => $this->token,
                    'status' => 1
                ))
                ->group('product_id')
                ->field('product_id, sum(number) as num')
                ->select(), 'product_id', 'num');
        };
        foreach ($aData as $k => $v) {
            if ($v['status'] == 0) {
                $aData[$k]['status'] = '进行中';
            } elseif ($v['status'] == 1) {
                $aData[$k]['status'] = '结束';
            } elseif ($v['status'] == 2) {
                $aData[$k]['status'] = '开团失败';
            }
            $aData[$k]['openid'] = Arr::get($aUserInfo, $v['openid']);
            $aData[$k]['product_id'] = Arr::get($aProInfo, $v['product_id']);
            $aData[$k]['num'] = Arr::get($OrderInfo, $v['product_id'], 0);
        }
        return $aData;
    }


    public function index()
    {
        $this->table(
            array(
                'HeadHover' => U('index'),
                'Head_Opt' => array(
                    array(
                        'name' => '添加团购产品',//2级
                        'url' => U('addGoods')
                    )
                ),
                'tips' => array(//3级
                    '团购商品管理'
                ),
                'Table_Header' => array(//4级
                    'ID', '商品名', '团购商品数量', '团购人数', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('editGoods')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('deleteGoods')
                    ),
                )
            ),
            M('groupbuy_product')->count(),
            M('groupbuy_product')->field('id,name,groupnum'),
            array($this, 'callback')
        );
        $this->UDisplay('show1');
    }

    public function addGoods()
    {
        $this->add('groupbuy_product', array(
            array('title' => '类型', 'type' => 'select', 'name' => 'type', 'many' => array(
                array('name' => '阶梯式', 'value' => '1'),
                array('name' => '单团模式', 'value' => '2'),
                array('name' => '单团均分模式', 'value' => '3'),
            )),
            array('title' => '名称', 'type' => 'input', 'name' => 'name', 'msg' => '请填写商品名称'),
            array('title' => '数量', 'type' => 'input', 'name' => 'groupnum', 'msg' => '请填写团购商品数量'),
            array('title' => '市场价', 'type' => 'input', 'name' => 'market_price', 'msg' => '请填写市场价'),
            array('title' => '开始时间', 'type' => 'time', 'name' => 'start_time', 'msg' => '请选择开始时间'),
            array('title' => '结束时间', 'type' => 'time', 'name' => 'end_time', 'msg' => '请选择结束时间'),
            array('title' => '下单送券时间', 'type' => 'input', 'name' => 'card_time', 'placeholder' => '多个时间段设置格式是: 09:00,12:00', 'msg' => '请选择下单赠送优惠券的时间'),
            array('title' => '优惠券金额', 'type' => 'input', 'name' => 'card_num', 'msg' => '请输入赠送优惠券金额'),
            array('type' => 'img', 'many' => array(
                array('title' => '封面图', 'name' => 'headpic', 'tips' => '建议尺寸'),
                array('title' => '轮播图', 'name' => 'company_logo1', 'tips' => '建议尺寸'),
                array('title' => '轮播图', 'name' => 'company_logo2', 'tips' => '建议尺寸'),
                array('title' => '轮播图', 'name' => 'company_logo3', 'tips' => '建议尺寸'),
            )),
            array('title' => '单独购买价格', 'type' => 'input', 'name' => 'single_price', 'msg' => '请输入单独购买价格'),
            array('type' => 'include', 'cnt' => $this->fetch('./tpl/User/default/groupbuy/add_product.html')),
            array('title' => '商品简介', 'type' => 'textarea', 'name' => 'content'),
            array('title' => '商品详情', 'type' => 'textarea_1', 'name' => 'detail'),
        ), U('index'), array($this, 'addGoodsPost_Data'));
    }

    public function addGoodsPost_Data($data)
    {
        $groupmsg = json_encode(array_combine($_REQUEST['number'], $_REQUEST['money']));
        return $data + array(
            'token' => $this->token,
            'typeid' => $_SESSION['catid'],
            'groupmsg' => $groupmsg,
            'add_time' => date('Y-m-d H:i:s'),
            'pic' => implode(',', array(
                $_REQUEST['company_logo1'],
                $_REQUEST['company_logo2'],
                $_REQUEST['company_logo3']
            ))
        );
    }

    //删除主险
    public function deleteGoods()
    {
        $this->del('groupbuy_product');
    }

    public function editGoods()
    {
        $this->assign('row', $this->expData(M('groupbuy_product')->where(array('id' => $_GET['id']))->find()));
        $this->Edit('groupbuy_product', array(
            array('title' => '类型', 'type' => 'select', 'name' => 'type', 'many' => array(
                array('content' => '阶梯式', 'value' => '1'),
                array('content' => '单团模式', 'value' => '2'),
                array('content' => '单团均分模式', 'value' => '3'),
            )),
            array('title' => '名称', 'type' => 'input', 'name' => 'name', 'msg' => '请填写商品名称'),
            array('title' => '数量', 'type' => 'input', 'name' => 'groupnum', 'msg' => '请填写团购商品数量'),
            array('title' => '市场价', 'type' => 'input', 'name' => 'market_price', 'msg' => '请填写市场价'),
            array('title' => '开始时间', 'type' => 'time', 'name' => 'start_time', 'msg' => '请选择开始时间'),
            array('title' => '结束时间', 'type' => 'time', 'name' => 'end_time', 'msg' => '请选择结束时间'),
            array('title' => '下单送券时间', 'type' => 'input', 'name' => 'card_time', 'msg' => '请选择下单赠送优惠券的时间'),
            array('title' => '优惠券金额', 'type' => 'input', 'name' => 'card_num', 'msg' => '请输入赠送优惠券金额'),
            array('type' => 'img', 'many' => array(
                array('title' => '封面图', 'name' => 'headpic', 'tips' => '建议尺寸'),
                array('title' => '轮播图', 'name' => 'company_logo1', 'tips' => '建议尺寸'),
                array('title' => '轮播图', 'name' => 'company_logo2', 'tips' => '建议尺寸'),
                array('title' => '轮播图', 'name' => 'company_logo3', 'tips' => '建议尺寸'),
            )),
            array('title' => '单独购买价格', 'type' => 'input', 'name' => 'single_price', 'msg' => '请输入单独购买价格'),
            array('type' => 'include', 'cnt' => $this->fetch('./tpl/User/default/groupbuy/add_product.html', array('list' => M('groupbuy_product')->where(array('id' => $_GET['id']))->find()))),
            array('title' => '商品简介', 'type' => 'textarea', 'name' => 'content'),
            array('title' => '商品详情', 'type' => 'textarea_1', 'name' => 'detail'),
        ), U('index'), array($this, 'addGoodsPost_Data'), array($this, 'expData'));
    }

    public function expData($aData)
    {
        $msg = explode(',', $aData['pic']);
        $aData['company_logo1'] = $msg[0];
        $aData['company_logo2'] = $msg[1];
        $aData['company_logo3'] = $msg[2];
        $aData['groupmsg'] = json_decode($aData['groupmsg'], true);
        return $aData;
    }

    public function callback($aData)
    {
        $OrderInfo = array();
        if ($aId = array_filter(array_keys(Arr::changeIndex($aData, 'id')))) {
            $OrderInfo = Arr::changeIndexToKVMap(M('Group_order')
                ->where(array(
                    'product_id' => array('in', $aId),
                    'token' => $this->token,
                    'status' => 1
                ))
                ->group('product_id')
                ->field('product_id, sum(number) as num')
                ->select(), 'product_id', 'num');
        };
        if ($aData) foreach ($aData as $k => $v) {
            $aData[$k]['num'] = Arr::get($OrderInfo, $v['id'], 0);
        }
        return $aData;
    }

    /*开团下载*/
    public function kaitxiaz()
    {
        $aData = M('group_info')->field('id,openid,product_id,status,add_time, tnumber')->select();
        if ($aOpenid = array_filter(array_keys(Arr::changeIndex($aData, 'opendid')))) {
            $aUserInfo = Arr::changeIndexToKVMap(M('Wxusers')->where(array(
                'openid' => array('in', $aOpenid)
            ))->field('openid, name')->select(), 'openid', 'name');
        };
        if ($aProId = array_filter(array_keys(Arr::changeIndex($aData, 'product_id')))) {
            $aProInfo = Arr::changeIndexToKVMap(M('groupbuy_product')->where(array(
                'id' => array('in', $aProId)
            ))->field('id, name')->select(), 'id', 'name');
        };
        //已售人数
        if ($aId = array_filter(array_keys(Arr::changeIndex($aData, 'product_id')))) {
            $OrderInfo = Arr::changeIndexToKVMap(M('Group_order')
                ->where(array(
                    'product_id' => array('in', $aId),
                    'token' => $this->token,
                    'status' => 1
                ))
                ->group('product_id')
                ->field('product_id, sum(number) as num')
                ->select(), 'product_id', 'num');
        };
        foreach ($aData as $k => $v) {
            if ($v['status'] == 0) {
                $aData[$k]['status'] = '进行中';
            } elseif ($v['status'] == 1) {
                $aData[$k]['status'] = '结束';
            } elseif ($v['status'] == 2) {
                $aData[$k]['status'] = '开团失败';
            }
            $aData[$k]['openid'] = Arr::get($aUserInfo, $v['openid']);
            $aData[$k]['product_id'] = Arr::get($aProInfo, $v['product_id']);
            $aData[$k]['num'] = Arr::get($OrderInfo, $v['product_id'], 0);
        }
        Excel::arr2ExcelDownload($aData,
            array('ID', '开团人', '团购商品', '状态', '开团时间', '团购人数', '已售人数'
            ), '开团查看');

    }
    public function dingdanxz(){
        $aData =  M('group_order')->field('id, product_id, tnumber, number, use_yu_er, yhjid, total')->select();
        if ($aPro = array_filter(array_keys(Arr::changeIndex($aData, 'product_id')))) {
            $aProInfo = Arr::changeIndexToKVMap(M('groupbuy_product')->where(array(
                'id' => array('in', $aPro)
            ))->field('id, name')->select(), 'id', 'name');
        };
        foreach ($aData as $k => $v) {
            $aData[$k]['yhjid'] = $v['yhjid'] ? '是' : '否';
            $aData[$k]['use_yu_er'] = $v['use_yu_er'] ? '是' : '否';
            $aData[$k]['product_id'] = Arr::get($aProInfo, $v['product_id'], '');
        }
        Excel::arr2ExcelDownload($aData,
            array(  'ID', '商品', '开团数量', '购买数量', '是否使用余额', '是否用优惠券', '总金额'
            ), '订单查看');
    }
    public function koeji($djie){

    }
}
