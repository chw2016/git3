<?php
/**
 *  普通服务预约
 *  @For 万科
 **/
class YuyueDengluAction extends TableAction
{

    public $_sTplBaseDir = 'User/default/togethernext';

    public function _initialize()
    {
        parent::_initialize();
        $this->YuyueGoods = D('Yuyue_goods');
        $this->YuyueGoodsInfo = D('Yuyue_datenum');
        $this->cattype = D('Yuyue_cattype');
        $this->tpl = 'tpl/User/default/helper/';
        $this->cattyid = session('cattyid');
    }

    protected function setHeader()
    {
        return array(
          array(
                  'name' => '分类管理',
                  'url'  => U('YuyueDenglu/index', array('token' => $this->token,'aid' => $_GET['aid']))
            ),
            array(
                  'name' => '订单管理',
                  'url'  => U('YuyueDenglu/order', array('token' => $this->token,'aid' => $_GET['aid']))
            ),

        );
    }

    public function index(){
        $aWhere['token']    = $this->_sToken;
        $aWhere['cid']   = $cid = $this->_get('id') ?  $this->_get('id') : $this->cattyid;
        $aWhere['username']  = session(username); 
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
                   /* array(
                        'name' => '添加商品',
                        'url' => U('YuyueMarket/addYuyueGoods',array('token'=>$this->token))
                    ),*/
                ),
                'aListImg' => array(
                    'container' => array('url'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'tips' => array(
                    '旅游产品管理',
                ),
                'Table_Header' => array(
                    'ID', '商品名', '价格', '图片', '操作'
                ),
                'List_Opt' => array(
                   /* array(
                        'name' => '编辑',
                        'url' => U('YuyueMarket/EditYuyueMarket')
                    ),*/
                    array(
                        'name' => '设置',
                        'url' => U('YuyueDenglu/set_time',array('token'=>$this->token,'aid'=>$_GET['aid']))
                    ),
                    /*array(
                        'name' => '删除',
                        'url' => U('YuyueMarket/DelYuyueGoods')
                    ),*/
                ),
            ),
            $this->YuyueGoods->where($aWhere)->count(),
            $this->YuyueGoods->field('id,name,price,url')->where($aWhere)
        );
       // $this->UDisplay('show1');

    }

    /*
     *
     */
    public function DelYuyueGoods()
    {
        $this->del('Yuyue_goods');
    }

/**
     *  订单
     **/
    public function order(){
        $aWhere['token']    = $this->_sToken;
        $aWhere['source']   = 1;
        //$aWhere['username']  = session(username); 
        $this->table        = M('Yuyue_order');
        $this->table(
            array(
                //'abc' => 123,
                //'id' => 'name',//如果主键不是id，则需要设置
                'HeadHover' => U('Yuyuedengluorder', array('token' => $this->_sToken, 'aid' => $_GET['aid'])),

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
        $this->display('./tpl/User/default/YuyueMarket_set_time.html');
    }
}


    /**
     * Class 这个类是做设置旅游时间有关的表
     */
class Calendar
{
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
        $this->_table = "";
        $this->_year = isset($_GET["y"]) ? $_GET["y"] : date("Y");
        $this->_month = isset($_GET["m1"]) ? $_GET["m1"] : date("m");
        if ($this->_month > 12) {//处理出现月份大于12的情况
            $this->_month = 1;
            $this->_year++;
        }
        if ($this->_month < 1) {//处理出现月份小于1的情况
            $this->_month = 12;
            $this->_year--;
        }
        $yue = $this->_month;//把月分比如05变成5
        $this->_currentDate = '<span class="y">' . $this->_year . '</span>年<span class="m">' . $yue . '</span>月份';//当前得到的日期信息
        $this->_days = date("t", mktime(0, 0, 0, $this->_month, 1, $this->_year));//得到给定的月份应有的天数
        $this->_dayofweek = date("w", mktime(0, 0, 0, $this->_month, 1, $this->_year));//得到给定的月份的 1号 是星期几
    }

    /**
     * 输出标题和表头信息
     */
    protected function _showTitle()
    {
        $this->_table = "<table style='width: 1000px;'><thead><tr align='center' ><th colspan='7' class='tou'>" . $this->_currentDate . "</th></tr ></thead>";
        $this->_table .= "<tbody><tr >";
        $this->_table .= "<td style='color:red'>星期日</td>";
        $this->_table .= "<td>星期一</td>";
        $this->_table .= "<td>星期二</td>";
        $this->_table .= "<td>星期三</td>";
        $this->_table .= "<td>星期四</td>";
        $this->_table .= "<td>星期五</td>";
        $this->_table .= "<td style='color:red'>星期六</td>";
        $this->_table .= "</tr>";
    }

    /**
     * 输出日期信息
     * 根据当前日期输出日期信息
     */
    protected function _showDate($c = '', $id = '', $token)
    {
        $aTmp = array();
        foreach ($c as $k => $v) {
            $sTmpKey = '_' . $v['dates'];
            $aTmp[$sTmpKey] = $v;
        }

        $y = $_REQUEST['y'] && $_REQUEST['y'] != '-' ? $_REQUEST['y'] : date('Y');
        $m1 = $_REQUEST['m1'] && $_REQUEST['m1'] != '-' ? $_REQUEST['m1'] : date('m');
        $y_m = $y . '-' . $m1;

        //计算出已经卖出的数量
        Vendor('Group.Order');
        $aSoldInfo = Arr::changeIndexToKVMap(M('Yuyue_order')->where(array(
            'token' => $token,
            'source' => Order::ORDER_SOURCE_YUYUE,
            'product_id' => $id,
            'shequ' => $_GET['shequ'],
            'month' => $y_m,
            'type' => array('egt', 0)
        ))
            ->field('dates, sum(num) as num')
            ->group('dates')
            ->select(), 'dates', 'num');

        $c = $aTmp;
        $nums = $this->_dayofweek + 1;
        for ($i = 1; $i <= $this->_dayofweek; $i++) {//输出1号之前的空白日期
            $this->_table .= '<td></td>';
        }
        //foreach($c as $v) {
        for ($i = 1; $i <= $this->_days; $i++) {//输出天数信息
            if ($nums % 7 == 0) {//换行处理：7个一行

                $this->_table .= "<td style='height:91px;' ";
                if (array_key_exists("_" . $i, $c)) {
                    $this->_table .= " class='red' ";
                }

                $this->_table .= "><p class='d'>$i</p>";
                if (array_key_exists("_" . $i, $c)) {
                    $num = $c['_' . $i]['totalnum'] - Arr::get($aSoldInfo, $i, 0);
                    $this->_table .= "<p>共{$c['_' . $i]['totalnum']}位,余{$num}位</p>";
                }
                $this->_table .= "<input style='display: none; height: 80px;width:90px;' type='text' name='num' class='num' placeholder='格式:数量'>
                                <input type='button' class='set' value='设置'><input type='button' class='del' value='删除'></td></tr><tr >";

            } else {
                $this->_table .= "<td style='height:91px;' ";
                if (array_key_exists("_" . $i, $c)) {
                    $this->_table .= " class='red' ";
                }

                $this->_table .= "><p class='d'>$i</p>";

                if (array_key_exists("_" . $i, $c)) {
                    $num = $c['_' . $i]['totalnum'] - Arr::get($aSoldInfo, $i, 0);
                    $this->_table .= "<p>共{$c['_' . $i]['totalnum']}位,余{$num}位</p>";
                }


                $this->_table .= "<input style='display: none;height: 80px;width:90px;' type='text' name='num' class='num' placeholder='格式:数量'>
                                        <input type='button' class='set' value='设置'><input type='button' class='del' value='删除'></td>";


            }
            $nums++;
        }

        $this->_table .= "</tbody></table>";
        //获取当前id
        $id = $_GET['id'];
        $aid =$_GET['aid'];
        //这里拼接自己的url地址
        $this->_table .= sprintf(
            "<h3><a href='?g=User&m=YuyueDenglu&a=set_time&id=%s&y=%s&m1=%s&shequ=%s&aid=$aid'>上一月</a>   ", $id, $this->_year, $this->_month - 1, $_GET['shequ']);
        $this->_table .= sprintf(
            "<a href='?g=User&m=YuyueDenglu&a=set_time&id=%s&y=%s&m1=%s&shequ=%s&aid=$aid'>下一月</a></h3>",
            $id,
            $this->_year,
            $this->_month + 1,
            $_GET['shequ']
        );
    }

    /**
     * 输出日历
     */
    public function showCalendar($b = '', $id = '', $token)
    {
        $this->_showTitle();
        $this->_showDate($b, $id, $token);
        return $this->_table;
    }
}



