<?php
/**
 *��Ŀ��������
 *@package WeiKuCMS
 *@author WeiKuCMS
 **/
return array(
    //'TMPL_EXCEPTION_FILE'	=> './tpl/exception.tpl',
    	'DEFUALT_AJAX_RETURN'	=> 'JSON',
	'LOAD_EXT_CONFIG' 		=> 'db,info,email,safe,upfile,cache,route,app,alipay,funconfig',
	'APP_AUTOLOAD_PATH'     =>'@.ORG',
	'OUTPUT_ENCODE'         =>  false, 			//ҳ��ѹ�����
	'PAGE_NUM'				=> 10,
	/*Cookie����*/
	'COOKIE_PATH'           => '/',     		// Cookie·��
    'COOKIE_PREFIX'         => '',      		// Cookieǰ׺ �����ͻ
	/*����ģ���ǩ*/
	'TMPL_L_DELIM'   		=>'{weikucms:',			//ģ��������ͨ��ǩ��ʼ���
	'TMPL_R_DELIM'			=>'}',				//ģ��������ͨ��ǩ�������

    'TMPL_EXCEPTION_FILE'	=> './tpl/exception.tpl',//���������


);
?>
