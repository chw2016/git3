<?php
//include"./Lib/Action/User/Mru_top.php";
return array(
		array(
				'name' => '服务项目',
				'url'  => U('Fuwu/index', array('token' => $this->_sToken))
		),
		array(
				'name' => '门店导航',
				'url'  => U('Mdian/index', array('token' => $this->_sToken))
		),
		array(
				'name' => '免费体验',
				'url'  => U('Tiyan/index', array('token' => $this->_sToken))
		),
		array(
				'name' => '限时抢购',
				'url'  => U('Qianggou/index', array('token' => $this->_sToken))
		),
		array(
				'name' => '加入我们',
				'url'  => U('Zhaopin/index', array('token' => $this->_sToken))
		),
		/*                 array(
		 'name' => '我要加盟',
				'url'  => U('Zhaopin/index2', array('token' => $this->_sToken))
		),  */
		array(
				'name' => '最新活动',
				'url'  => U('Huodong/index', array('token' => $this->_sToken))
		),
		array(
				'name' => '在线检测',
				'url'  => U('Ceshi/index', array('token' => $this->_sToken))
		),
		array(
				'name' => '品牌介绍',
				'url'  => U('Ppjs/index', array('token' => $this->_sToken))
		),
		 
		array(
				'name' => '专家介绍',
				'url'  => U('Zjjs/index', array('token' => $this->_sToken))
		),
		 

		array(
				'name' => '美丽资讯',
				'url'  => U('MruZx/index', array('token' => $this->_sToken))
		),
		
		array(
				'name' => '后台操作日志',
				'url'  => U('MruRz/index', array('token' => $this->_sToken))
		),
		
		array(
				'name' => '用户操作日志',
				'url'  => U('MruRz2/index', array('token' => $this->_sToken))
		),
		
		array(
				'name' => '红包积分用户获取记录',
				'url'  => U('MruXf/index', array('token' => $this->_sToken))
		),
		
		array(
				'name' => '店铺红包使用记录',
				'url'  => U('MruXf/hb', array('token' => $this->_sToken))
		),
		
		array(
				'name' => '店铺抢购券使用记录',
				'url'  => U('MruXf/qgj', array('token' => $this->_sToken))
		),
		
		
		
		array(
				'name' => '店铺内页图片与其它',
				'url'  => U('MruMt/index', array('token' => $this->_sToken))
		),
		 
 		
);
?>