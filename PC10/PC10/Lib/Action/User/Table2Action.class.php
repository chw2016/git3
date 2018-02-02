<?php
/**
 *  基本表单样式
 **/
abstract class Table2Action extends UserAction{
    /**
     *  定义项目模板BaseDir
     **/
    //protected $_sTplBaseDir = 'User/default/jishi';

    /**
     *  Token
     **/
    protected $_sToken = null;

    /**
     *  UID
     **/
    protected $_iUID = null;
    /*
     * pageNumber
     */
    protected $iPageNumer = 25;
    /**
     *  顶部
     **/
    public function _initialize()
    {
    	parent::_initialize();
        $this->assign('set_page',session('?set_page')?session('set_page'):$this->iPageNumer);
        $this->_sToken = session('token');
        $this->_iUID   = session('uid');
        $this->assign(array(
            'Header' => $this->setHeader()
        ));

    }
    /*
     * array(
          array(
             'name' => '理财产品',
             'url'  => U('Jishi/index')
          ),
          array(
              'name' => '产品分类',
              'url'  => U('Jishi/type')
          )
        )
     *
     */
    abstract protected function setHeader();
    /*
     * 	生成地址并带上Token
     *
     */
    protected function UU($sModel, $aParam = array(), $bAutoToken = true ){
    	$aToken = $bAutoToken ? array('token' => $this->_sToken) : array();
    	return U($sModel, array_merge($aParam, $aToken));
    }
    /**
     *  展示table
     *  @param $aHeader header头部设置
     *  @param $iCount  数据总数
     *  @param $Model	带查询条件的Model
     *  $param $aHandleData array 处理数据的接口
     *  @param $aExtra  额外分配数据
     **/
	public function table(
		$aHeader= array(),
		$iCount = 0,
		$Model	= null,
		$aHandleData = array(),
		$aExtra	= array()
	){
        #页面表基本配置
        $this->assign($aHeader);
        /*array(
            'id' => 'id',//如果主键不是id，则需要设置
            'HeadHover' => U('Jishi/index'),//默认选择标题栏的url地址，即setHeader里的导航栏
            'Head_Opt' => array(//导航栏下面的按钮部分，需要加的按钮
                array(
                    'name'   => '添加产品',
                    'url'    => U('Jishi/ProductAdd')
                )
            ),
            'tips' => array(
                'xxxx',
                'ww',
            ),
            //表头信息
            'Table_Header' => array(
                'ID', '名称', '图片', '地区', '理财师', '类型', '实体价', '价格', '操作'
            ),
            //显示表头里是图片的字段，并且设置图片大小
            'aListImg' => array(
                'container' => array('image'),
                'width'     => 70,
                'height'    => 70
            ),
            //操作按钮，字段
            'List_Opt' => array(
                array(
                    'name' => '编辑',
                    'url'  => U('Jishi/ProductEdit')
                ),
                array(
                    'name' => '删除',
                    'url'  => U('Jishi/ProductDel')
                ),
            )
        )*/
        $aHandleData = empty($aHandleData) ? array($this, '_handleData') : $aHandleData;
        #实际数据
		$Page   = new Page($iCount, session('?set_page')?session('set_page'):$this->iPageNumer);
		if(!empty($Model)){
			$aData = call_user_func_array($aHandleData, array(
				$Model->limit($Page->firstRow.','.$Page->listRows)->select()
			));
		}
       // p($aData);die;
		$this->assign(array_merge(array(
            'page'     => $Page->show(),
            'aList'    => $aData
		), $aExtra)
		);

    //      $this->UDisplay('show1');

	}
	public static function _handleData($aData){
		return $aData;
	}
    /**
     * 添加所有
     * $model代表、表名，
     */
    public function add($model,$input,$url,$fun='',$html=''){
        if(IS_POST){
          //  p($_REQUEST);die;
            $_POST=$_REQUEST;
            foreach($_POST as $k=>$v){
               if(is_array($v)){
                   $_POST[$k]= implode(',',$v);
               }
            }
            //扩展
            if(isset($_POST['kuozhang_k'])){
                $_POST['kuozhang']=array();
                $cc1=explode(',',$_POST['kuozhang_k']);
                $cc2=explode(',',$_POST['kuozhang_v']);
                for($i=0;$i<count($cc1);$i++){
                    if($cc1[$i]){
                        $_POST['kuozhang'][$cc1[$i]]=empty($cc2[$i])?"":$cc2[$i];
                    }

                }
               // p($_POST['kuozhang']);
                $_POST['kuozhang'] = addslashes(json_encode($_POST['kuozhang']));
            }
          //  echo $_POST['kuozhang'];
           // p(json_decode('{"\u8096\u6709":"32\u4e00","\u8138\u4e00":"1"}',true));

          //  die;
            $_POST['add_time']=time();
            $_POST=array_filter($_POST);

            $fun = empty($fun) ? array($this, '_handleData') : $fun;
            $_POST = call_user_func_array($fun, array(
                $_POST
            ));
            $Model       = D($model);
            $_POST['token']= $this->_sToken;

            if(($aData = $Model->create()) != false){
                if($Model->add()){
                    $this->success2('产品添加成功',$url);
                }else{
                    $this->error2('服务器繁忙,请稍候再试');
                }
            }else{
                $this->error2($Model->getError());

            }
        }else{
            $this->assign(array(
                'ExtraBtn' => array(
                    array(
                      //  'url'  => U('Cs/index', array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
                'FormUrl'  => U('Miye/add',array('token'=>$_SESSION['token'])),
                'input'=>$input
            ));

            if($html){
                $this->display($this->tpl.$html);
            }else{
                $this->display('tpl/User/default/helper/add1.html');
            }

        }
    }
    /**
     * 所有修改
     */
    public function Edit($model1,$input,$url,$fun='', $beforeFun=null){
        if(IS_POST){

            if($_POST['id']>0){
                $_POST=$_REQUEST;
                $_POST['hobbies']=isset($_POST['hobbies'])?$_POST['hobbies']:'';
                $_POST['is_show']=isset($_POST['is_show'])?$_POST['is_show']:'';
                foreach($_POST as $k=>$v){
                    if(is_array($v)){
                        $_POST[$k]= implode(',',$v);
                    }
                }
                //扩展
                //扩展
                if(isset($_POST['kuozhang_k'])){
                    $_POST['kuozhang']=array();
                    $cc1=explode(',',$_POST['kuozhang_k']);
                    $cc2=explode(',',$_POST['kuozhang_v']);
                    for($i=0;$i<count($cc1);$i++){
                        if($cc1[$i]){
                            $_POST['kuozhang'][$cc1[$i]]=empty($cc2[$i])?"":$cc2[$i];
                        }
                    }
                    // p($_POST['kuozhang']);
                    $_POST['kuozhang'] = json_encode($_POST['kuozhang']);
                }
                $_POST['id']    = FC::P('id');
                $_POST['token'] = $this->_sToken;
                $fun = empty($fun) ? array($this, '_handleData') : $fun;
                $_POST = call_user_func_array($fun, array(
                    $_POST
                ));

                $aWhere         = array(
                    'id'    => FC::P('id'),
                    'token' => FC::P('token')
                );
                 
                $Model = D($model1);
                $Item    = $Model->where($aWhere)->find();
				
                if($Item == false) $this->error2('非法操作');
                if($Model->create()){
                    if($iID = $Model->where($aWhere)->save($_POST)){
                        $this->success2('修改成功',$url);
                    }else{
                        $this->error2('操作失败');
                    }
                }else{
                    $this->error2($Model->getError());
                }
            }else{
                //没有id的时候
                $_POST=$_REQUEST;
                $_POST['hobbies']=isset($_POST['hobbies'])?$_POST['hobbies']:'';
                $_POST['is_show']=isset($_POST['is_show'])?$_POST['is_show']:'';
                foreach($_POST as $k=>$v){
                    if(is_array($v)){
                        $_POST[$k]= implode(',',$v);
                    }
                    if($v==''){
                        unset($_POST[$k]);
                    }
                }

             //   $_POST['id']    = FC::P('id');
                $_POST['token'] = $this->_sToken;
                $fun = empty($fun) ? array($this, '_handleData') : $fun;
                $_POST = call_user_func_array($fun, array(
                    $_POST
                ));

                $aWhere         = array(
                  //  'id'    => FC::P('id'),
                    'token' => $_POST['token']
                );
                // p($_POST);die;
                $Model = D($model1);
                $Item    = $Model->where($aWhere)->find();
                if($Item){
                    if($Model->create()){
                        if($iID = $Model->where($aWhere)->save($_POST)){
                            $this->success2('修改成功',$url);
                        }else{
                            $this->error2('操作失败');
                        }
                    }else{
                        $this->error2($Model->getError());
                    }
                }else{//没有就新增
                    if($Model->create()){
                        if($iID = $Model->add($_POST)){
                            $this->success2('修改成功',$url);
                        }else{
                            $this->error2('操作失败');
                        }
                    }else{
                        $this->error2($Model->getError());
                    }
                }

              //  if($Item == false) $this->error2('非法操作');

            }

        }else{
           // p($_GET);die;
            if(isset($_GET['id'])){
                $iID     = $this->_get('id');
                $PZ		 = D($model1)->where(array(
                    'id' => $iID,
                    'token' => $this->_sToken
                ))->find();
                if($PZ == false) $this->error2('非法操作');
                //扩展新增
               // p($PZ);
                if(!empty($PZ['kuozhang'])){
                    $extend=json_decode($PZ['kuozhang'],true);
                    $this->assign('extend',$extend);
                }else{
                    $this->assign('extend',0);
                }
                $beforeFun = empty($beforeFun) ? array($this, '_handleData') : $beforeFun;
                $PZ = call_user_func_array($beforeFun, array(
                    $PZ
                ));

               // p($extend);die;
                $this->assign(array(
                    'list' => $PZ,
                    'id' => $iID,
                    'FormUrl' => U('Miye/PinzhongEdit',array('token'=>$_SESSION['token'])),
                    'ExtraBtn' => array(
                        array(
                            'url'  => U('Cs/index', array('token' => $this->_sToken)),
                            'name' => '返回'
                        )
                    ),
                    'input'=>$input
                ));
                //加扩展
             //   p($PZ);die;

            }else{
				
              //  $iID     = $this->_get('id');
                $PZ		 = D($model1)->where(array(
                  //  'id' => $iID,
                    'token' => $this->_sToken
                ))->find();
             //   if($PZ == false) $this->error2('非法操作');
                $this->assign(array(
                    'list' => $PZ,
                  //  'id' => $iID,
                    'FormUrl' => U('Miye/PinzhongEdit',array('token'=>$_SESSION['token'])),
                    'ExtraBtn' => array(
                        array(
                            'url'  => U('Cs/index', array('token' => $this->_sToken)),
                            'name' => '返回'
                        )
                    ),
                    'input'=>$input
                ));
            }
            $this->display('tpl/User/default/helper/add1.html');
        }
    }

    /**
     * 所有删除
     */
    public function del($model1){
        $iID     = $this->_get('id');
        $aWhere  = array('id'=>$iID,'token' => $this->_sToken);
        $Product = D($model1);
        $bCheck  = $Product->where($aWhere)->find();
        if($bCheck == false) $this->error2('非法操作');
        if($Product->where($aWhere)->delete()) {
            $this->success2('删除成功');
        }else{
            $this->error2('操作失败');
        }
    }


    //生成查询条件
    public function search($arr) {

       // $arr=array_filter($arr);
        foreach($arr as $k=>$v){
            if($v==''){
                unset($arr[$k]);
            }
        }

        foreach($arr as $k=>$v){
            if($k!='__hash__'){
                $field = substr($k, 3);
                $prefix = substr($k,0,2);
                if($prefix=='li'){
                    $aWhere[$field]=array('like','%'.$v.'%');
                }
                if($prefix=='eq'){
                    $aWhere[$field]=array('eq',$v);
                }
                if($prefix=='be'&&$v[0]&&$v[1]){
                    //  $aWhere['$field']
                    $aWhere[$field]=array('between',array(strtotime($v[0]),strtotime($v[1])));
                }
            }

        }

        return $aWhere;
    }
    //设置每页显示多少条
    public function set_page(){
        if(IS_POST){
            $this->iPageNumer=$_POST['num'];
            session('set_page',$_POST['num']);
            echo json_encode(array('status'=>1));
            //echo $this->iPageNumer;
           // die;

        }
    }

}
?>
