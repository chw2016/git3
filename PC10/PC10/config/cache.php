﻿<?php
/**
 *��վ��������
 *@package WeiKuCMS
 *@author WeiKuCMS
 **/
return array(
	/*���涨��*/
	'DATA_CACHE_TYPE'       => 'File',  		//��������
	'DATA_CACHE_COMPRESS'   => true,   		// ���ݻ����Ƿ�ѹ������
	'DATA_CACHE_SUBDIR'     => true,    	// ʹ����Ŀ¼���� (�Զ����ݻ����ʶ�Ĺ�ϣ������Ŀ¼)
    'DATA_PATH_LEVEL'       => 2,        		// ��Ŀ¼���漶��
	'HTML_CACHE_ON'			=> false,			//�Ƿ�����̬����
	//'HTML_CACHE_RULES'   	=>array('*'=>array('{$_SERVER.REQUEST_URI|md5}')),			//��̬�������
	'HTML_CACHE_TIME'		=>   60,			//��̬������Ч�ڣ��룩
	'HTML_FILE_SUFFIX' 		=>'.html',			//��̬�����׺ 
);