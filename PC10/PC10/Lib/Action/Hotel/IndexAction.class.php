<?php
class IndexAction extends UserAction{
    public $token;
    public $hotel_roomlist;
    public $hotel_orderinfo;
    public $uid;
    public $hotel_hotellist;
    //构造函数
    public function _initialize() {
        parent::_initialize();
        $this->hotel_roomlist = M("Hotel_roomlist");  
        $this->hotel_orderinfo = M("Hotel_orderinfo");				 
        $this->hotel_hotellist = M("Hotel_hotellist");
        $this->token = session('token');			  
        $this->uid = session('uid');
    }
    public function index(){
        if($this->token == $_GET['token']){
            $w['token'] = $this->token;
            //房间总数
            $data = $this->hotel_roomlist->where($w)->select();
            $a = count($data);
            for($i=0 ; $i<$a ; $i++){
                $room_count[$i] = $data[$i]['room_count'];
            }
            $index['arc'] = array_sum($room_count);
            //今日订单
            $start_time = strtotime(date('Y-m-d'));
            $end_time = $start_time + 3600*24;
            $w['order_sub_time'] = array(array(egt,$start_time),array(elt,$end_time));
            $index['tao'] = count($this->hotel_orderinfo->where($w)->select());
            //今日收益
            $message = $this->hotel_orderinfo->where($w)->select();
            $b = count($message);
            for($j=0 ; $j<$b ; $j++){
                $c[$j] = $message[$j]['order_price'];
            }
            $index['tap'] = array_sum($c);
            //今日在住间数
            $where['token'] = $this->token;
            $where['check_out_time'] = array(array(elt,$end_time));
            $index['tal'] = count($this->hotel_orderinfo->where($where)->select());
            $this->assign('index',$index);
            //收益图
            $year = date('Y') % 4;
            $month = date('m');
            if($year == 0){
                $date =array('01'=>'31','02'=>'29','03'=>'31','04'=>'30','05'=>'31','06'=>'30','07'=>'31','08'=>'31','09'=>'30','10'=>'31','11'=>'30','12'=>'31');
                $days = $date[$month];
            }else{
                $date =array('01'=>'31','02'=>'28','03'=>'31','04'=>'30','05'=>'31','06'=>'30','07'=>'31','08'=>'31','09'=>'30','10'=>'31','11'=>'30','12'=>'31');
                $days = $date[$month]; 
            }
            $start_month = strtotime(date('Y-m'));
            $end_month = strtotime(date('Y-m'))+$days * 24 * 3600;
            $f['token'] = $this->token;
            $f['check_out_time'] = array(array(egt,$start_month),array(elt,$end_month));
            $g = $this->hotel_orderinfo->where($f)->select();
            $l = count($g);
            for($x=0 ; $x<$l ; $x++){
                $z[$x] = $g[$x]['order_price'];
            }
            $pro[$month] = array_sum($z) / 1000;
            $this->assign('pro',$pro);
        }
        $this->display();
    }
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
?>