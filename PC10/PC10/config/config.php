<?php
/**
 *项目公共配置
 *@package WeiKuCMS
 *@author WeiKuCMS
 **/
return array(
    //'TMPL_EXCEPTION_FILE'	=> './tpl/exception.tpl',
    	'DEFUALT_AJAX_RETURN'	=> 'JSON',
	'LOAD_EXT_CONFIG' 		=> 'db,info,email,safe,upfile,cache,route,app,alipay,funconfig',
	'APP_AUTOLOAD_PATH'     =>'@.ORG',
	'OUTPUT_ENCODE'         =>  false, 			//页面压缩输出
	'PAGE_NUM'				=> 10,
	/*Cookie配置*/
	'COOKIE_PATH'           => '/',     		// Cookie路径
    'COOKIE_PREFIX'         => '',      		// Cookie前缀 避免冲突
	/*定义模版标签*/
	'TMPL_L_DELIM'   		=>'{weikucms:',			//模板引擎普通标签开始标记
	'TMPL_R_DELIM'			=>'}',				//模板引擎普通标签结束标记

    'TMPL_EXCEPTION_FILE'	=> './tpl/exception.tpl',//错误定义出来


);
?>
