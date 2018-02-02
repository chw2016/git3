<?php
//include"./Lib/Action/User/Mru_top.php";
return array(
    array(
        'name' => '查看预约信息',
        'url'  => U('Dianphd/ck',array('token' => $this->_sToken,'aid'=>$_GET['aid']))
    ),


/*     				array(
    						'name' => '限制抢购',
    						'url'  => U('Qianggoua/index', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
    				), */
    				
    				array(
    						'name' => '最新活动',
    						'url'  => U('Huodonga/index', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
    				),
    			
    			array(
    					
    					'name' => '项目推荐',
    					'url'  => U('MruXm/index', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
    			),


    array(
        'name' => '预约管理',
        'url'  => U('Dianphd/index', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
    ),
		
		array(
					
				'name' => '验证抢购券',
				'url'  => U('MruQgj/xx', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
		),

		array(
					
				'name' => '验证优惠券',
				'url'  => U('Wdyhj2/index', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
		),
		
		
		array(
					
				'name' => '验证红包',
				'url'  => U('MruHb/xx', array('token' => $this->_sToken,'aid'=>$_GET['aid']))
		),
		
		
    			 
);
?>