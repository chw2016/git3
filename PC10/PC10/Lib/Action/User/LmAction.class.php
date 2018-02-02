<?php
/**
 *  海南生活定制
 **/
class LmAction extends Table1Action {

    public $_sTplBaseDir = 'User/default/miye';

    public function _initialize()
    {
    	parent::_initialize();
        $this->pz	   = D('No_credit');
        $this->pz1   = D('Credit');
        $this->order  =M('No_credit_order');//订单表
        $this->tpl="tpl/User/default/helper/";
        $this->token=$this->_sToken;
    }
    
    protected function setHeader(){
    	return array(
            array(
                    'name' => '经纪人',
                    'url'  => U('index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '设置',
                'url'  => U('set', array('token' => $this->_sToken))
            ),


            );
    }


    /**
     *
     **/
	public function index(){
        $aWhere='';
        $order='';
        $aWhere['token'] =$this->_sToken;
        session('where_p',$aWhere);
        if(IS_POST){
            $order=$_POST['paixu_form'];
            foreach($_REQUEST as $k=>$v){//$_POST拿不到数组
                if(substr($k,0,2)=='be'&&$v){
                    $_POST[$k]=$v;
                }
            }
            $aWhere = $this->search($_POST);
            foreach($aWhere as $k=>$v){
                    if(strstr($k,'|')){
                        $b=str_replace('|','.',$k);
                        $aWhere[$b]=$v;
                        unset($aWhere[$k]);
                    }
            }
            $aWhere['token'] =$this->_sToken;
            session('where_p',null);
            session('where_p',$aWhere);
        }else{
            //get 过来P分页时，带上条件查询数据
            if(isset($_GET['p'])&&session('?where_p')){
                $aWhere=session('where_p');
            }
        }

            $this->table(
                array(
                  //  'id' => 'name',//如果主键不是id，则需要设置
                    'HeadHover' => U('index', array('token' => $this->_sToken)),
                    'Head_Opt' => array(
                        array(
                            'name' => '添加',
                            'url' => U('add_houses',array('token'=>$this->token))
                        ),
                        array(
                            'name' => '导出',
                            'type'=>'daochu',//type=daochu时，可以带上搜索条件
                            'url' => U('excel',array('token'=>$this->token))
                        ),
                         array(
                        'name' => '一键',
                        'type'=>'yijin',//开启多选用这个type=yijin
                        'url' => U('yijin')
                          ),



                    ),
                    'tips' => array(
                        '经纪人列表'
                    ),
                    'Table_Header' => array(
                        'ID', '标题','SELECT值','积分|num desc','添加时间','操作'
                    ),
                    'List_Opt' => array(
                        array(
                            'name' => '修改',
                            'url' => U('edit_houses')
                        ),
                        array(
                            'name' => '修改',
                            'url' => U('del_houses')
                        ),


                    ),
                  'search'=>array(
                          array('title'=>'标题','name'=>'li_title'),
                          array('title'=>'SELECT框','type'=>'select','name'=>'eq_select','many'=>array(
                              array('name'=>'是','value'=>'1'),
                              array('name'=>'否','value'=>'0'),
                              array('name'=>'其他','value'=>'3'),
                          )),
                             array('type'=>'br'),
                             array('title'=>'添加时间','name'=>'be_add_time','type'=>'between')
                    )
                ),
                M('lm')->where($aWhere)->order($order)->count(),
                M('lm')->field('id,title,select,num,add_time')->order($order)->where($aWhere),
				array($this,'abc')
            );
       $this->assign('duoxuan',1);
        $this->UDisplay('show1');
	}

	public function abc($data){
        foreach($data as $k=>$v){
            if($v['select']==0){
                $data[$k]['select']='否';
            }

            if($v['select']==1){
                $data[$k]['select']='是';
            }

            if($v['select']==3){
                $data[$k]['select']='其他';
            }

			$data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
		}
        return $data;
    }
	





    /**
     * 添加楼盘
     */
    public function add_houses(){
        $this->assign('tishi','这里自定义提示信息');
        $this->add('lm',array(
            array('title'=>"输入框",'type'=>"input",'name'=>"title",'msg'=>'楼盘名称不能为空'),
            array('title'=>"积分",'type'=>"input",'name'=>"num"),
            array('title'=>'单选项','type'=>'radio','name'=>'radio','many'=>array(
                array('content'=>'是','value'=>'1'),
                array('content'=>'否','value'=>'0')

            )),
            array('title'=>'多选项','type'=>'checkbox','name'=>'checkbox','many'=>array(
                array('content'=>'是','value'=>'1'),
                array('content'=>'否','value'=>'0'),
                array('content'=>'其他','value'=>'3'),

            )),
            array('type'=>'img','many'=>array(
                array('title'=>"图片1",'name'=>"pic1"),
                array('title'=>"图片2",'name'=>"pic2")
            )),
            array('title'=>"textarea文本输入框",'type'=>"textarea1",'name'=>"textarea"),
            array('title'=>"图文详情框1",'type'=>"textarea_1",'name'=>"info1"),
            array('title'=>"图文详情框2",'type'=>"textarea_2",'name'=>"info2"),//依此去推，html页面对应添加
            array('title'=>"经纬度",'type'=>"map1",'lng'=>"j",'lat'=>'w'),//经纬度
            array('title'=>"时间",'type'=>"time",'name'=>"time"),
           array('title'=>"地址",'type'=>"address"),
            array('title'=>"扩展",'type'=>"kuozhang"),




        ),U('index',array('token'=>$this->_sTtoken)),array($this,'b'));

    }
    public function b($data){
        return $data;
    }

    public function edit_houses(){
        $this->Edit('lm',array(
            array('title'=>"输入框",'type'=>"input",'name'=>"title",'msg'=>'楼盘名称不能为空'),
            array('title'=>"积分",'type'=>"input",'name'=>"num"),
            array('title'=>'单选项','type'=>'radio','name'=>'radio','many'=>array(
                array('content'=>'是','value'=>'1'),
                array('content'=>'否','value'=>'0')

            )),
            array('title'=>'多选项','type'=>'checkbox','name'=>'checkbox','many'=>array(
                array('content'=>'是','value'=>'1'),
                array('content'=>'否','value'=>'0'),
                array('content'=>'其他','value'=>'3'),

            )),
            array('title'=>'select框','type'=>'select','name'=>'select','many'=>array(
                array('content'=>'是','value'=>'1'),
                array('content'=>'否','value'=>'0'),
                array('content'=>'其他','value'=>'3'),

            )),
            array('type'=>'img','many'=>array(
                array('title'=>"图片1",'name'=>"pic1",'tips'=>'建议图片大于100*500'),
                array('title'=>"图片2",'name'=>"pic2")
            )),
            array('title'=>"textarea文本输入框",'type'=>"textarea1",'name'=>"textarea"),
            array('title'=>"图文详情框1",'type'=>"textarea_1",'name'=>"info1"),
            array('title'=>"图文详情框2",'type'=>"textarea_2",'name'=>"info2"),//依此去推，html页面对应添加
            array('title'=>"经纬度",'type'=>"map1",'lng'=>"j",'lat'=>'w'),//经纬度
            array('title'=>"时间",'type'=>"time",'name'=>"time"),
            array('title'=>"地址",'type'=>"address"),
            array('title'=>"地址",'type'=>"kuozhang"),




        ),U('index',array('token'=>$this->_sTtoken)),array($this,'k'));
    }
    public function del_houses(){
        $this->del('lm');
    }
    public function k($data){
      //  p($data);die;
        return $data;
    }
    //佣金记录


    //设置
    public function set(){
        $this->Edit('hn_set',array(
            array('title'=>"推荐经纪人所得佣金",'type'=>"input",'name'=>"yonjing1"),


        ),U('index',array('token'=>$this->_sTtoken)));
    }

    //导出
    public function excel(){
        p($_POST);
    }
    //一键功能
    public function yijin(){
        p($_POST);
    }

}
?>
