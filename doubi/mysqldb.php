<?php
$gj_url=dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);
$APP_ID = "wx623fd03bd2bfe874";
$APP_SECRET="5dafa6f8e070e68be3b85a6fe89476c2";
$WNO="gh_02a8fc124767";
function object_array($array){
    if(is_object($array)){
        $array = (array)$array;
    }
    if(is_array($array)){
        foreach($array as $key=>$value){
            $array[$key] = object_array($value);
        }
    }
    return $array;
}
function guid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = //chr(123) "{"
            substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }
}
	/**********************************************************************
	*  mysql 数据库初始化
	*/

    define('GUANGJIA_WXPAY_PATH',   dirname(__FILE__) . DIRECTORY_SEPARATOR);
    define('IN_GUANGJIA_WXPAY', true);

	// Include ezSQL core
	include_once "mysql/ez_sql_core.php";

	// Include ezSQL database specific component
	include_once "mysql/ez_sql_mysql.php";

    //加载配置文件
    function load_config($file) {
        static $configs = array();
        $path = GUANGJIA_WXPAY_PATH.$file.".php";
        if (is_file($path)) {
            $configs[$file] = include $path;
            return $configs[$file];
        }
    }
    //数据库配置
    $config  = load_config('config');


	// Initialise database object and establish a connection
	// at the same time - db_user / db_password / db_name / db_host
	$db = new ezSQL_mysql($config['username'],$config['password'],
        $config['database_name'],$config['host']);

// where组装
function get_where($parms)
{
    $sql = '';
    foreach ( $parms as $field => $val )
    {
        if ( $val === 'true' ) $val = 1;
        if ( $val === 'false' ) $val = 0;

        if ( $val == 'NOW()' )
        {
            $sql .= "$field = ".$val." and ";
        }
        else
        {
            $sql .= "$field = '".$val."' and ";
        }
    }

    return substr($sql,0,-4);
}


?>