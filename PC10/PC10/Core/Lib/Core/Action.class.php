<?php

abstract class Action {

    protected $view     =  null;

    private   $name     =  '';

    protected $tVar     =   array();


    protected $config   =   array();


    public function __construct() {
        tag('action_begin',$this->config);
        if(method_exists($this,'_initialize'))
            $this->_initialize();
    }

    protected function getActionName() {
        if(empty($this->name)) {
            $this->name     =   substr(get_class($this),0,-6);
        }
        return $this->name;
    }


    protected function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
            if('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }
        if(!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')]))
            return true;
        return false;
    }


    protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
        $this->initView();
        $this->view->display($templateFile,$charset,$contentType,$content,$prefix);
    }


    protected function show($content,$charset='',$contentType='') {
        $this->initView();
        $this->view->display('',$charset,$contentType,$content);
    }

    protected function fetch($templateFile='') {
        $this->initView();
        return $this->view->fetch($templateFile);
    }

    private function initView(){
        if(!$this->view)    $this->view     = Think::instance('View');
        if($this->tVar)     $this->view->assign($this->tVar);
    }


    protected function buildHtml($htmlfile='',$htmlpath='',$templateFile='') {
        $content = $this->fetch($templateFile);
        $htmlpath   = !empty($htmlpath)?$htmlpath:HTML_PATH;
        $htmlfile =  $htmlpath.$htmlfile.C('HTML_FILE_SUFFIX');
        if(!is_dir(dirname($htmlfile)))
            mkdir(dirname($htmlfile),0755,true);
        if(false === file_put_contents($htmlfile,$content))
            throw_exception(L('_CACHE_WRITE_ERROR_').':'.$htmlfile);
        return $content;
    }


    protected function assign($name,$value='') {
        if(is_array($name)) {
            $this->tVar   =  array_merge($this->tVar,$name);
        }else {
            $this->tVar[$name] = $value;
        }
    }

    public function __set($name,$value) {
        $this->assign($name,$value);
    }


    public function get($name='') {
        if('' === $name) {
            return $this->tVar;
        }
        return isset($this->tVar[$name])?$this->tVar[$name]:false;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __call($method,$args) {
        if( 0 === strcasecmp($method,ACTION_NAME.C('ACTION_SUFFIX'))) {
            if(method_exists($this,'_empty')) {

                $this->_empty($method,$args);
            }elseif(file_exists_case(C('TEMPLATE_NAME'))){

                $this->display();
            }elseif(function_exists('__hack_action')) {
                __hack_action();
            }else{
                _404(L('_ERROR_ACTION_').':'.ACTION_NAME);
            }
        }else{
            switch(strtolower($method)) {

                case 'ispost'   :
                case 'isget'    :
                case 'ishead'   :
                case 'isdelete' :
                case 'isput'    :
                    return strtolower($_SERVER['REQUEST_METHOD']) == strtolower(substr($method,2));
                case '_get'     :   $input =& $_GET;break;
                case '_post'    :   $input =& $_POST;break;
                case '_put'     :   parse_str(file_get_contents('php://input'), $input);break;
                case '_param'   :
                    switch($_SERVER['REQUEST_METHOD']) {
                        case 'POST':
                            $input  =  $_POST;
                            break;
                        case 'PUT':
                            parse_str(file_get_contents('php://input'), $input);
                            break;
                        default:
                            $input  =  $_GET;
                    }
                    if(C('VAR_URL_PARAMS')){
                        $params = $_GET[C('VAR_URL_PARAMS')];
                        $input  =   array_merge($input,$params);
                    }
                    break;
                case '_request' :   $input =& $_REQUEST;   break;
                case '_session' :   $input =& $_SESSION;   break;
                case '_cookie'  :   $input =& $_COOKIE;    break;
                case '_server'  :   $input =& $_SERVER;    break;
                case '_globals' :   $input =& $GLOBALS;    break;
                default:
                    throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            }
            if(!isset($args[0])) {
                $data       =   $input;
            }elseif(isset($input[$args[0]])) {
                $data       =	$input[$args[0]];
                $filters    =   isset($args[1])?$args[1]:C('DEFAULT_FILTER');
                if($filters) {
                    $filters    =   explode(',',$filters);
                    foreach($filters as $filter){
                        if(function_exists($filter)) {
                            $data   =   is_array($data)?array_map($filter,$data):$filter($data); //
                        }
                    }
                }
            }else{
                $data       =	 isset($args[2])?$args[2]:NULL;
            }
            return $data;
        }
    }


    protected function error($message,$jumpUrl='',$ajax=true) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }


    protected function success($message,$jumpUrl='',$ajax=true) {
        $this->dispatchJump($message,1,$jumpUrl,$ajax);
    }

    protected function error2($message,$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }


    protected function success2($message,$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,1,$jumpUrl,$ajax);
    }


    protected function ajaxReturn($data,$type='') {
        if(func_num_args()>2) {
            $args           =   func_get_args();
            array_shift($args);
            $info           =   array();
            $info['data']   =   $data;
            $info['info']   =   array_shift($args);
            $info['status'] =   array_shift($args);
            $data           =   $info;
            $type           =   $args?array_shift($args):'';
        }
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        if(strtoupper($type)=='JSON') {

            header('Content-Type:text/html; charset=utf-8');
            exit(json_encode($data));
        }elseif(strtoupper($type)=='XML'){
            header('Content-Type:text/xml; charset=utf-8');
            exit(xml_encode($data));
        }elseif(strtoupper($type)=='EVAL'){
            header('Content-Type:text/html; charset=utf-8');
            exit($data);
        }else{
        }
    }
	 function getmi(){
		$host=$_SERVER['HTTP_HOST'];
		$host=strtolower($host);
		if(strpos($host,"\/")!==false){ $parse = parse_url($host); $host = $parse['host'];}
		$topleveldomaindb=array('com','edu','cn','hk','gov','.so','co','int','tk','mil','net','org','biz','info','pro','name','museum','coop','aero','xxx','idv','mobi','cc','me'); $str='';
		foreach($topleveldomaindb as $v){
			$str.=($str ? '|' : '').$v;
		}
		$matchstr="[^\.]+\.(?:(".$str.")|\w{2}|((".$str.")\.\w{2}))$";
		if(preg_match("/".$matchstr."/ies",$host,$matchs)){
			$do=$matchs['0'];
		}
		else{
			$do=$host;
		}
		return $do;
    }

    protected function redirect($url,$params=array(),$delay=0,$msg='') {
        $url    =   U($url,$params);
        redirect($url,$delay,$msg);
    }


    private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false) {
        if($ajax || $this->isAjax()) {
            $data           =   is_array($ajax)?$ajax:array();
            $data['info']   =   $message;
            $data['status'] =   $status;
            $data['url']    =   $jumpUrl;
            $this->ajaxReturn($data);
        }
        if(!empty($jumpUrl)) $this->assign('jumpUrl',$jumpUrl);

        $this->assign('msgTitle',$status? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));

        if($this->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',$status);

        C('HTML_CACHE_ON',false);
        if($status) {
            $this->assign('message',$message);

            if(!$this->get('waitSecond'))    $this->assign('waitSecond','1');

            if(!$this->get('jumpUrl')) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display(C('TMPL_ACTION_SUCCESS'));
        }else{
            $this->assign('error',$message);

            if(!$this->get('waitSecond'))    $this->assign('waitSecond','3');

            if(!$this->get('jumpUrl')) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(C('TMPL_ACTION_ERROR'));

            exit ;
        }
    }

    public function __destruct() {
        if(C('LOG_RECORD')) Log::save();
        tag('action_end');
    }
}
