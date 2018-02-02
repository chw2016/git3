<?php
/**
 *  普通服务预约
 *  @For 万科
 **/
class YuyueMarketAction extends Table1Action {

    public $_sTplBaseDir = 'User/default/miye';

    public function _initialize()
    {
    	parent::_initialize();
        $this->YuyueGoods     = D('Yuyue_goods');
        $this->YuyueGoodsInfo = D('Yuyue_datenum');
        $this->cattype        = D('Yuyue_cattype');
        $this->tpl            = 'tpl/User/default/helper/';
        $this->cattyid        = session('cattyid');
    }

    protected function setHeader(){
    	return array(
            array(
                  'name' => '分类管理',
                  'url'  => U('YuyueMarket/index', array('token' => $this->token))
            ),
            array(
                  'name' => '商品管理',
                  'url'  => U('YuyueMarket/YuyueGoods', array('token' => $this->token))
            ),
            array(
                  'name' => '订单管理',
                  'url'  => U('YuyueMarket/order', array('token' => $this->token))
            ),
            array(
                  'name' => '社区管理',
                  'url'  => U('YuyueMarket/Shequ', array('token' => $this->token))
            ),
            array(
                  'name' => '配置',
                  'url'  => U('YuyueMarket/setting', array('token' => $this->token))
            ),
            array(
                'name' => '添加管理员',
                'url'  => U('YuyueMarket/guanliyuan', array('token' => $this->token))
            ),
        );
    }

    public function guanliyuan(){
        $this->Edit('YuyueGoods',array(
            array('title'=>'管理员','type'=>'input','name'=>'username','msg'=>'请输入管理员'),
            array('title'=>'管理员密码','type'=>'password','name'=>'password','msg'=>'请输入管理元密码'),
        ),U('YuyueGoods',array('token'=>$this->token)),array($this,'guanliyuaninfo'));

    }
    function guanliyuaninfo($data){
        $data['password'] = md5($data['password']);
        return $data;
    }
    public function setting()
    {
        $this->Edit('Yuyue_setting',array(
            array('title'=>'分类icon','type'=>'img','many'=>array(
                array('title'=>"轮播图1",'name'=>"imgs_1",'msg'=>''),
                array('title'=>"轮播图2",'name'=>"imgs_2",'msg'=>''),
                array('title'=>"轮播图3",'name'=>"imgs_3",'msg'=>''),
                array('title'=>"轮播图4",'name'=>"imgs_4",'msg'=>''),
            ))
        ),
        U('YuyueMarket/index', array('token'=>$this->token)),
        array($this, 'setting_POST'),
        array($this, 'bf_setting')
        );
    }

    public function bf_setting($aData)
    {
        $msg = explode(',', $aData['imgs']);
        $aData['imgs_1'] = $msg[0];
        $aData['imgs_2'] = $msg[1];
        $aData['imgs_3'] = $msg[2];
        $aData['imgs_4'] = $msg[3];
        return $aData;
    }

    public function setting_POST($data)
    {
        $data['token']  = $this->token;
        $data['imgs']   = implode(',', array(
            $_REQUEST['imgs_1'],
            $_REQUEST['imgs_2'],
            $_REQUEST['imgs_3'],
            $_REQUEST['imgs_4']
        ));
        return $data;
    }

    /**
     *  分类
     **/
	public function index(){
        $aWhere['token']    = $this->_sToken;
        $this->table(
            array(
                //'abc' => 123,
                //'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('YuyueMarket/index', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加分类',
                        'url' => U('YuyueMarket/addCattype',array('token'=>$this->token))
                    ),
                ),
                'aListImg' => array(
                    'container' => array('pic'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'tips' => array(
                    '旅游产品管理'
                ),
                'Table_Header' => array(
                    'ID', '分类名', '分类图', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('YuyueMarket/EditCattype')
                    ),
                    array(
                        'name' => '商品管理',
                        'url' => U('YuyueMarket/YuyueGoods',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('YuyueMarket/DelYuyueMarket')
                    ),
                ),
            ),
            $this->cattype->where($aWhere)->count(),
            $this->cattype->field('id,name, pic')->where($aWhere)
        );
        $this->UDisplay('show1');
	}

    /**
     *  订单
     **/
	public function order(){
        $aWhere['token']    = $this->_sToken;
        $aWhere['source']   = 1;
        $this->table        = M('Yuyue_order');
        $this->table(
            array(
                //'abc' => 123,
                //'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('YuyueMarket/order', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加分类',
                        'url' => U('YuyueMarket/addCattype',array('token'=>$this->token))
                    ),
                ),
                'aListImg' => array(
                    'container' => array('pic'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'tips' => array(
                    '旅游产品管理'
                ),
                'Table_Header' => array(
                    'ID', '订单ID', '产品名','总价格', '日期', '购买数量', '状态', '下单时间', '付款时间', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '查看',
                        'url' => U('orderDetail')
                    ),
                ),
            ),
            $this->table->where($aWhere)->count(),
            $this->table->field('id,orderid,source, product_id, money,month, dates,num,type,add_time,pay_time')->where($aWhere),
            array($this, 'showOrder')
        );
        $this->UDisplay('show1');
	}

    public function orderDetail()
    {
        $v = M('Yuyue_order')->where(array(
            'id'    => $_REQUEST['id']
        ))->find();
        Vendor('Group.Order');
        $Order = new Order($this->token, $this->openid);

        $aProductInfo = $Order->getProductInfo($v['source'], $v['product_id']);
        $info['商品名称']  = Arr::get($aProductInfo, 'name');
        $info['日期'] = $v['month'] . '-' . $v['dates'];
        $info['状态'] = $Order->getTypeName($v['type']);
        $info['下单时间']  = date('Y-m-d H:i:s', $v['add_time']);
        if($v['pay_time']){
            $info['付款时间']  = date('Y-m-d H:i:s', $v['pay_time']);
        }
        //sn
        $aSN = explode(',', $v['sn']);
        $aSNTime = explode(',', $v['sn_used_time']);
        foreach ($aSN as $k => $value) {
            if ($value) {
                $info['sn码:'.$value] = $aSNTime[$k] ? $aSNTime[$k] : '未使用';
            }
        }

        //数据
        $this->assign('info', $info);
        $this->UDisplay('yuyue_order');
    }

    public function showOrder($aData)
    {
        Vendor('Group.Order');
        $Order = new Order($this->token, $this->openid);
        foreach ($aData as $k => $v) {
            $aData[$k]['month'] = $v['month'] . '-' . $v['dates'];
            $aData[$k]['type']      = $Order->getTypeName($v['type']);
            $aData[$k]['add_time']  = date('Y-m-d H:i:s', $v['add_time']);
            if($v['pay_time']){
                $aData[$k]['pay_time']  = date('Y-m-d H:i:s', $v['pay_time']);
            }
            $aProductInfo = $Order->getProductInfo($v['source'], $v['product_id']);
            $aData[$k]['product_id']  = Arr::get($aProductInfo, 'name');
            unset($aData[$k]['dates']);
            unset($aData[$k]['source']);
        }
        return $aData;
    }

    public function addCattype()
    {
        $this->add('Yuyue_cattype',array(
            array('title'=>'分类名','type'=>'input','name'=>'name','msg'=>'请输入分类名'),
            array('title'=>'分类icon','type'=>'img','many'=>array(
                array('title'=>"分类icon",'name'=>'pic','msg'=>'请上传分类的icon')
            ))
        ),U('YuyueMarket/index',array('token'=>$this->token)));
    }

    public function EditCattype()
    {
        $this->Edit('Yuyue_cattype',array(
            array('title'=>'分类名','type'=>'input','name'=>'name','msg'=>'请输入分类名'),
            array('title'=>'分类icon','type'=>'img','many'=>array(
                array('title'=>"分类icon",'name'=>'pic','msg'=>'请上传分类的icon')
            ))
        ),U('YuyueMarket/index',array('token'=>$this->token)));
    }

    public function EditYuyueMarket()
    {
        $this->Edit('Yuyue_goods',array(
            array('title'=>'名称','type'=>'input','name'=>'name','msg'=>'请输入商品名称'),
            array('title'=>'价格','type'=>'input','name'=>'price','msg'=>'请输入价格'),
            array('title'=>'商品图片','type'=>'img','many'=>array(
                array('title'=>"商品图片",'name'=>'url','msg'=>'请上传商品的图片'),
                array('title'=>"轮播图1",'name'=>"imgs_1",'msg'=>''),
                array('title'=>"轮播图2",'name'=>"imgs_2",'msg'=>''),
                array('title'=>"轮播图3",'name'=>"imgs_3",'msg'=>''),
            )),
            array('title'=>'图文介绍','type'=>'textarea','name'=>'short_info','msg'=>'请输入产品 的介绍'),
            array('type'=>"hidden_true",'name'=>"cid", 'value' => $this->cattyid)
        ),
        U('YuyueMarket/index',array('token'=>$this->token)),
        array($this,'addYuyueGoods_POST'),
        array($this,'bf_Edit')
        );
    }

    public function bf_Edit($aData)
    {
        $msg = explode(',', $aData['imgs']);
        $aData['imgs_1'] = $msg[0];
        $aData['imgs_2'] = $msg[1];
        $aData['imgs_3'] = $msg[2];
        return $aData;
    }

    /*
     *  添加预约商品
     */
    public function Shequ()
    {
        $aWhere['token']    = $this->_sToken;
        $this->Shequ = M('Yuyue_shequ');
        $this->table(
            array(
                'HeadHover' => U('YuyueMarket/Shequ', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加社区',
                        'url' => U('YuyueMarket/addShequ',array('token'=>$this->token))
                    ),
                ),
                'tips' => array(
                    '社区管理'
                ),
                'Table_Header' => array(
                    'ID', '社区名','操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('YuyueMarket/EditShequ')
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('YuyueMarket/DelShequ')
                    ),
                ),
            ),
            $this->Shequ->where($aWhere)->count(),
            $this->Shequ->field('id,name')->where($aWhere)
        );
        $this->UDisplay('show1');

    }

    public function DelYuyueMarket()
    {
        //把对应的商品也删掉
        $this->YuyueGoods->where(array(
            'cid' => $_GET['id']
        ))->delete();
        $this->del('Yuyue_cattype');
    }

    public function DelShequ(){
        $this->del('Yuyue_shequ');
    }

    public function EditShequ(){
        $this->Edit('Yuyue_shequ',array(
            array('title'=>'名称','type'=>'input','name'=>'name','msg'=>'请输入商品名称'),
            array('title'=>'开始时间','type'=>'time','name'=>'start_time','msg'=>'请输入开始时间'),
            array('title'=>'结束时间','type'=>'time','name'=>'end_time','msg'=>'请输入结束时间'),
        ),U('Shequ',array('token'=>$this->token)));
    }


    public function addShequ()
    {
        $this->add('Yuyue_shequ',array(
            array('title'=>'名称','type'=>'input','name'=>'name','msg'=>'请输入商品名称'),
            array('title'=>'开始时间','type'=>'time','name'=>'start_time','msg'=>'请输入开始时间'),
            array('title'=>'结束时间','type'=>'time','name'=>'end_time','msg'=>'请输入结束时间'),
        ),U('Shequ',array('token'=>$this->token)));
    }




    /*
     *  添加预约商品
     */
    public function YuyueGoods()
    {
        $aWhere['token']    = $this->_sToken;
        $aWhere['cid']   = $cid = $this->_get('id') ?  $this->_get('id') : $this->cattyid;
        if (empty($cid)) {
            return $this->error2('请从分类进入商品管理',U('YuyueMarket/index'));
        }
        session('cattyid', $cid);
        $this->table(
            array(
                //'abc' => 123,
                //'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('YuyueMarket/YuyueGoods', array('token' => $this->_sToken)),
                'Head_Opt' => array(
                    array(
                        'name' => '添加商品',
                        'url' => U('YuyueMarket/addYuyueGoods',array('token'=>$this->token))
                    ),
                ),
                'aListImg' => array(
                    'container' => array('url'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'tips' => array(
                    '旅游产品管理',
                    '进入区域的入口：'.C('site_url').'index.php?g=User&m=Branch&a=index&token='.$this->_sToken.'&modulename=Yuyue_goods'

                ),
                'Table_Header' => array(
                    'ID', '商品名', '价格', '图片', '操作'
                ),
                'List_Opt' => array(
                    array(
                        'name' => '编辑',
                        'url' => U('YuyueMarket/EditYuyueMarket')
                    ),
                    array(
                        'name' => '设置',
                        'url' => U('YuyueMarket/set_time',array('token'=>$this->token))
                    ),
                    array(
                        'name' => '删除',
                        'url' => U('YuyueMarket/DelYuyueGoods')
                    ),
                ),
            ),
            $this->YuyueGoods->where($aWhere)->count(),
            $this->YuyueGoods->field('id,name,price,url')->where($aWhere)
        );
        $this->UDisplay('show1');

    }

    /*
     *
     */
    public function DelYuyueGoods()
    {
        $this->del('Yuyue_goods');
    }


    /**
     *  设置时间
     */
    public function set_time(){
        $id    = $this->_get('id');
        $year  = $this->_get('y');
        $m     = $this->_get('m1');
        $year  = $year  ? $year : date('Y');
        $m     = $m ? $m : date('m');
        $shequ = $_REQUEST['shequ'];

        $month = $year . '-' . $m;

        $list=$this->YuyueGoodsInfo->where(array(
            'token'         => $this->token,
            'product_id'    => $id,
            'shequ'         => $shequ,
            'month'         => $month
        ))->select();
        $calc=new Calendar();
        $time_info=$calc->showCalendar($list,$id,$this->token);
        $this->assign('shequ',M('Yuyue_shequ')->where(array(
            'token' => $this->token
        ))->select());
        $this->assign('time_info',$time_info);
        $this->display();
    }

    public function addYuyueGoods()
    {
        $list = M('Yuyue_goods')->field('user,pwd')->where(array(
            'token'=>$this->token,
            'user'=>array('neq','')
        ))->group('user')->select();
        foreach($list as $k=>$v){
           /* $pws[$k] = $list[$k]['pwd'];*/
            $jie[$k] = array('value'=>$list[$k] ['username'], 'content'=>$list[$k] ['username']);
        }
        $this->add('YuyueGoods',array(
            array('title'=>'名称','type'=>'input','name'=>'name','msg'=>'请输入商品名称'),
            array('title'=>'价格','type'=>'input','name'=>'price','msg'=>'请输入价格'),
            array('title'=>'商品图片','type'=>'img','many'=>array(
                array('title'=>"商品图片",'name'=>'url','msg'=>'请上传商品的图片'),
                array('title'=>"轮播图1",'name'=>"imgs_1",'msg'=>''),
                array('title'=>"轮播图2",'name'=>"imgs_2",'msg'=>''),
                array('title'=>"轮播图3",'name'=>"imgs_3",'msg'=>''),
            )),
            array('title'=>'图文介绍','type'=>'textarea','name'=>'short_info','msg'=>'请输入产品 的介绍'),
            array('type'=>"hidden_true",'name'=>"cid", 'value' => $this->cattyid),
            /*array('type'=>'select','title'=>"选择管理员",'name'=>"fid")*/
            array('type'=>'select','title'=>"管理员",'name'=>"user",'value'=>'sex2','msg'=>'请选择管理员','many'=>$jie)
         ),U('YuyueGoods',array('token'=>$this->token)),array($this,'addYuyueGoods_POST'));
    }

    public function addYuyueGoods_POST($data)
    {
        $data['token']  = $this->token;
        $data['imgs']   = implode(',', array(
            $_REQUEST['imgs_1'],
            $_REQUEST['imgs_2'],
            $_REQUEST['imgs_3']
        ));
        return $data;
    }

    /**
     * 旅游产品
     */
    public function edit_time(){
        $id     = $_REQUEST['cid'];
        $month  = $_REQUEST['y_m'];
        $dates  = $_REQUEST['d'];
        $num    = $_REQUEST['num'];
        $shequ  = $_REQUEST['shequ'];

        $aExMonth = explode('-', $month);
        $y        = $aExMonth[0];
        $m1       = $aExMonth[1];
       /* echo $_GET['aid'];exit;*/
        $Tour    = $this->YuyueGoods->where(array('id' => $id))->find();
        $backUrl = U('YuyueMarket/set_time',array(
                    'token'=>$this->token,
                    'id'=>$id,
                    'y' => $y,
                    'm1'=> $m1,
                    'shequ' => $_GET['shequ'],
                    'aid'=>$_GET['aid']
                ));
        if (!$Tour) {
            return $this->error2('设置失败正在跳转',$backUrl);
        }

        if(isset($_GET['del'])){//删除
            //日期
            if($this->YuyueGoods->where(array(
                'token' => $this->token,
                'product_id'=> $id,
                'month' => $month,
                'dates' => $dates
            ))->delete()){
                $this->success2('设置成功正在跳转',$backUrl);
            }else{
                $this->error2('设置失败正在跳转',$backUrl);
            }
        }else{
            if (!$num) {
                return $this->error2('设置格式错误,格式为:  数量,如:3',$backUrl);
            }
            //增加或者更新
            if ($this->YuyueGoodsInfo->where(array(
                'token' => $this->token,
                'product_id'=> $id,
                'shequ' => $shequ,
                'month' => $month,
                'dates' => $dates
            ))->count() > 0) {
                if($this->YuyueGoodsInfo->where(array(
                    'token' => $this->token,
                    'product_id'=> $id,
                    'month' => $month,
                    'shequ' => $shequ,
                    'dates' => $dates
                ))->data(array(
                    'totalnum'  => $num
                ))->save()){
                    $this->success2('设置成功正在跳转',$backUrl);
                }else{
                    $this->error2('设置失败正在跳转',$backUrl);
                }
            }else{
                if($this->YuyueGoodsInfo->data(array(
                    'token'    => $this->token,
                    'product_id'  => $id,
                    'month'    => $month,
                    'dates'    => $dates,
                    'shequ' => $shequ,
                    'totalnum' => $num,
                    'money'    => $iMoney
                ))->add()){
                    $this->success2('设置成功正在跳转',$backUrl);
                }else{
                    $this->error2('设置失败正在跳转',$backUrl);
                }
            }
        }
    }



}
/**
 * Class 这个类是做设置旅游时间有关的表
 */
class Calendar{
    protected $_table;//table表格
    protected $_currentDate;//当前日期
    protected $_year; //年
    protected $_month; //月
    protected $_days; //给定的月份应有的天数
    protected $_dayofweek;//给定月份的 1号 是星期几
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->_table="";
        $this->_year = isset($_GET["y"])?$_GET["y"]:date("Y");
        $this->_month = isset($_GET["m1"])?$_GET["m1"]:date("m");
        if ($this->_month>12){//处理出现月份大于12的情况
            $this->_month=1;
            $this->_year++;
        }
        if ($this->_month<1){//处理出现月份小于1的情况
            $this->_month=12;
            $this->_year--;
        }
        $yue=$this->_month;//把月分比如05变成5
        $this->_currentDate = '<span class="y">'.$this->_year.'</span>年<span class="m">'.$yue.'</span>月份';//当前得到的日期信息
        $this->_days = date("t",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份应有的天数
        $this->_dayofweek = date("w",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份的 1号 是星期几
    }
    /**
     * 输出标题和表头信息
     */
    protected function _showTitle()
    {
        $this->_table="<table style='width: 1000px;'><thead><tr align='center' ><th colspan='7' class='tou'>".$this->_currentDate."</th></tr ></thead>";
        $this->_table.="<tbody><tr >";
        $this->_table .="<td style='color:red'>星期日</td>";
        $this->_table .="<td>星期一</td>";
        $this->_table .="<td>星期二</td>";
        $this->_table .="<td>星期三</td>";
        $this->_table .="<td>星期四</td>";
        $this->_table .="<td>星期五</td>";
        $this->_table .="<td style='color:red'>星期六</td>";
        $this->_table.="</tr>";
    }

    /**
     * 输出日期信息
     * 根据当前日期输出日期信息
     */
    protected function _showDate($c='',$id='', $token)
    {
        $aTmp = array();
        foreach ($c as $k => $v) {
            $sTmpKey = '_' . $v['dates'];
            $aTmp[$sTmpKey] = $v;
        }

        $y   = $_REQUEST['y'] && $_REQUEST['y'] != '-' ? $_REQUEST['y'] : date('Y');
        $m1  = $_REQUEST['m1'] && $_REQUEST['m1'] != '-' ? $_REQUEST['m1'] : date('m');
        $y_m = $y . '-' . $m1;

        //计算出已经卖出的数量
        Vendor('Group.Order');
        $aSoldInfo = Arr::changeIndexToKVMap(M('Yuyue_order')->where(array(
            'token'         => $token,
            'source'        => Order::ORDER_SOURCE_YUYUE,
            'product_id'    => $id,
            'shequ'         => $_GET['shequ'],
            'month'         => $y_m,
            'type'          => array('egt', 0)
        ))
        ->field('dates, sum(num) as num')
        ->group('dates')
        ->select(), 'dates', 'num');

        $c = $aTmp;
        $nums = $this->_dayofweek+1;
        for ($i=1;$i<=$this->_dayofweek;$i++){//输出1号之前的空白日期
            $this->_table .= '<td></td>';
        }
        //foreach($c as $v) {
            for ($i = 1; $i <= $this->_days; $i++) {//输出天数信息
                if ($nums % 7 == 0) {//换行处理：7个一行

                    $this->_table .= "<td style='height:91px;' ";
                    if(array_key_exists("_".$i,$c)){
                        $this->_table .=" class='red' ";
                    }

                    $this->_table .="><p class='d'>$i</p>";
                    if(array_key_exists("_".$i,$c)){
                        $num = $c['_' . $i]['totalnum'] - Arr::get($aSoldInfo, $i, 0);
                       $this->_table .="<p>共{$c['_'.$i]['totalnum']}位,余{$num}位</p>";
                    }
                    $this->_table .="<input style='display: none; height: 80px;width:90px;' type='text' name='num' class='num' placeholder='格式:数量'>
                                <input type='button' class='set' value='设置'><input type='button' class='del' value='删除'></td></tr><tr >";

                } else {
                        $this->_table .= "<td style='height:91px;' ";
                    if(array_key_exists("_".$i,$c)){
                        $this->_table .=" class='red' ";
                    }

                         $this->_table .="><p class='d'>$i</p>";

                    if(array_key_exists("_".$i,$c)){
                        $num = $c['_' . $i]['totalnum'] - Arr::get($aSoldInfo, $i, 0);
                       $this->_table .="<p>共{$c['_'.$i]['totalnum']}位,余{$num}位</p>";
                     }


                    $this->_table .="<input style='display: none;height: 80px;width:90px;' type='text' name='num' class='num' placeholder='格式:数量'>
                                        <input type='button' class='set' value='设置'><input type='button' class='del' value='删除'></td>";


                }
                $nums++;
            }

        $this->_table.="</tbody></table>";
        //获取当前id
        $id=$_GET['id'];
        //这里拼接自己的url地址
        $this->_table.= sprintf(
            "<h3><a href='?g=User&m=YuyueMarket&a=set_time&id=%s&y=%s&m1=%s&shequ=%s'>上一月</a>   ", $id, $this->_year, $this->_month-1, $_GET['shequ']);
        $this->_table .= sprintf(
            "<a href='?g=User&m=YuyueMarket&a=set_time&id=%s&y=%s&m1=%s&shequ=%s'>下一月</a></h3>",
            $id,
            $this->_year,
            $this->_month + 1,
            $_GET['shequ']
        );
    }
    /**
     * 输出日历
     */
    public function showCalendar($b='',$id='', $token)
    {
        $this->_showTitle();
        $this->_showDate($b,$id, $token);
        return $this->_table;
    }
}
