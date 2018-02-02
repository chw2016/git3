<?php
/**
 *  基本表单样式 1
 **/
abstract class TableAction extends UserAction{
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
     * 公众号ID 设置为tid
     * */

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
        $this->_sToken = session('token');
        $this->_iUID = session('uid');
        $wxuser = M('Wxuser')->where(array('token'=>session('token')))->find();
        $this->uid   = $wxuser['id'];

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
    protected $bShowDefault = false;
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
		$Page   = new Page($iCount,$this->iPageNumer);
		if(!empty($Model)){

			$aData = call_user_func_array($aHandleData, array(
				$Model->limit($Page->firstRow.','.$Page->listRows)->select()
			));
		}
        //P($aData);die;
		$this->assign(array_merge(array(
            'page'     => $Page->show(),
            'aList'    => $aData
		), $aExtra)
		);
		$_SESSION['urlurl']=$_SERVER['REQUEST_URI'];//存地址
        if ($this->bShowDefault) {
            $this->display('tpl/User/default/helper/show.html');
             //$this->UDisplay('show');
        }else{
            $this->UDisplay('show');
        }
	}

	public static function _handleData($aData){
		return $aData;
	}


    /**
     * 添加所有
     * $model代表、表名，
     */
    public function add($model,$input,$url,$fun='',$baoc='',$add_time){
        if(IS_POST){
            $_POST=$_REQUEST;//复选框取值
            if($add_time) $_POST[$add_time]=date("Y-m-d",time());
            foreach ($_POST as $key=>$v){
                if(is_array($v)){//如果是个数组转化成字符串，键值分开
            		$v=implode($v, ',');
            		$_POST[$key]=$v;
            		$value=$_POST[$key];
            		$ke=$key;
            	}
            }

            $_POST[$ke]=$value;//$ke是键 $value是值

            $fun = empty($fun) ? array($this, '_handleData') : $fun;
            $_POST = call_user_func_array($fun, array(
            		$_POST
            ));

            $Model       = D($model);
            $_POST['token']= $_SESSION['token'];


            if(($aData = $Model->create()) != false){
            	$id=$Model->add();
                if($id){
                	$url=$url."&addid=".$id;
                    $this->success2('添加成功 ',$url);
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
                        'url'  => U($url, array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
                'FormUrl'  => U('Miye/add',array('token'=>$_SESSION['token'])),
                'input'=>$input
            ));
            //$this->display('tpl/User/default/helper/add.html');
           $this->UDisplay('add');
        }
    }
    /**
     * 所有修改
     * $model1 表名
     * $input input值
     * $url 返回地址
     * $fun 回调加post值
     * $baoc 查看
     * $conditions 自定义条件
     * $aheaddata 回调方法
     */
    public function Edit($model1,$input,$url,$fun='',$baoc='',$conditions='',$aheaddata=array()){

        if(IS_POST){
	       	$_POST=$_REQUEST;//复选框取值
        	foreach ($_POST as $key=>$v){
        		if(is_array($v)){//如果是个数组转化成字符串，键值分开
        			$v=implode($v, ',');
        			$_POST[$key]=$v;
        			$value=$_POST[$key];
        			$ke=$key;
        		}
        	}
        	$_POST[$ke]=$value;//$ke是键 $value是值

        	$_POST['id']    = FC::P('id');
            $_POST['token'] = $_SESSION['token'];
            //自定义条件
            if(!$conditions){
            	$aWhere         = array(
            			'id'    => $_GET['id'],
             			'token' => $_SESSION['token']
            	);
            }else{
            	$aWhere=$conditions;
            }

            //删除token条件
            if($_GET['deleteToken']){unset($aWhere['token']);}
            //第4参数
            $fun = empty($fun) ? array($this, '_handleData') : $fun;
            $_POST = call_user_func_array($fun, array(
            		$_POST
            ));
            $Model = D($model1);
            $Item    = $Model->where($aWhere)->find();
            if($Item == false) $this->error2('非法操作');
            if($Model->create()){
                //P($_POST);exit;
            	if($iID = $Model->where($aWhere)->save($_POST)){
                	$url=$url."&editid=".FC::P('id');
                    $this->success2('修改成功',$url);
                }else{
                    $this->error2('操作失败');
                }
            }else{
                $this->error2($Model->getError());
            }
        }else{

            $iID     = $this->_get('id');
            //自定义条件
            if(!$conditions){
            	$aWhere         = array(
            			'id'    => $_GET['id'],
             			'token' => $_SESSION['token']
            	);
            }else{
            	$aWhere=$conditions;
            }

            //删除token条件
            if($_GET['deleteToken']){ unset($aWhere['token']);}

            $aheaddata = empty($aheaddata) ? array($this, '_headdata') : $aheaddata;


            $PZ		 = call_user_func_array($aheaddata,array(D($model1)->where($aWhere)->find()));
            if($PZ == false) $this->error2('非法操作');
            $this->assign(array(
                'pinzhong' => $PZ,
                'id' => $iID,
                'FormUrl' => U('Miye/PinzhongEdit',array('token'=>$_SESSION['token'])),
                'ExtraBtn' => array(
                    array(
                        'url'  => U($url, array('token' => $this->_sToken)),
                        'name' => '返回'
                    )
                ),
                'input'=>$input
            ));
			
            $this->assign('PZ',$PZ);
            //查看
            if($baoc){
              $this->assign('baoc',$baoc);
            }
            //$this->display('tpl/User/default/helper/add.html');
            $this->UDisplay('add');
        }
    }

    public static function _headdata($aData){
        return $aData;
    }

    /**
     * 所有删除
     */
    public function del($model1){
        $iID     = $this->_get('id');
        $aWhere  = array('id'=>$iID,'token' => $this->_sToken);
        //删除token条件
        if($_GET['deleteToken']){unset($aWhere['token']);}
        $Product = D($model1);
        $bCheck  = $Product->where($aWhere)->find();
        if($bCheck == false) $this->error2('非法操作');
        if($Product->where($aWhere)->delete()) {
            $this->success2('删除成功');
        }else{
            $this->error2('操作失败');
        }
    }

    //搜索
    public function search($arr) {

        $arr=array_filter($arr);//删除空数组
    	foreach($arr as $k=>$v){
  			if($k!='__hash__'){
    			$field = substr($k, 3);//找出子段，比如:子段li_name，找出其中name
    			$prefix = substr($k,0,2);//截取子段前两位 比如:子段li_name，找出其中li
    			if($prefix=='li'){
    				$aWhere[$field]=array('like','%'.$v.'%');//模糊查询
    			}
    			if($prefix=='eq'){
    				$aWhere[$field]=array('eq',$v);//等于查询
    			}
    			if($prefix=='be'&&$v[0]&&$v[1]){
    				if(strtotime($v[1])>strtotime($v[0])){
    					$aWhere[$field]=array('between',array(strtotime($v[0]),strtotime($v[1])));//时间查询
    				}else{
    					echo "<script>alert('开始时间不能小于结束时间');</script>";
    				}
    			}
    		}

    	}
    	return $aWhere;
    }
    //排序  只 能主健是id,子段是sort 才能排序  有意见自己去开发！
    public function sortajaxTable($Model	= null){
    		$sort=$_POST['sort'];
    		$sort=intval($sort);
    		if($sort==0){
    			$res['str']='请输入正确数字';
    			$this->ajaxReturn($res);
    			die;
    		}
    		$b=M($Model)->where(array('id'=>$_POST['id']))->save(array('sort'=>$sort));
    		$this->ajaxReturn($res);
    }
    //是否显示 只 能主健是id,才能是否显示  有意见自己去开发！
    public function is_showAjaxTable($Model	= null,$is_show){
          //  echo $Model;die;
           // echo $_POST['is_show'];die;

    		if($_POST['is_show']==1){//不启用
    			M($Model)->where(array('id'=>$_POST['id']))->save(array($is_show=>0));
    		}else{//启用

    			M($Model)->where(array('id'=>$_POST['id']))->save(array($is_show=>1));
    		}
    		$this->ajaxReturn();

    }

    //是否审核  只 能主健是id 才能是否审核   有意见自己去开发！
    public function stateAjaxTable($Model	= null,$state){

    	if($_POST['state']==1){
            if($Model=='jd_user'){
                M($Model)->where(array('id'=>$_POST['id']))->save(array($state=>0,'sp_time'=>'','vip_time'=>'','grade'=>0));

            }else{
                M($Model)->where(array('id'=>$_POST['id']))->save(array($state=>0));
            }

    	}else{
            if($Model=='jd_user'){
                M($Model)->where(array('id'=>$_POST['id']))->save(array($state=>1,'sp_time'=>time()));

            }else{
                M($Model)->where(array('id'=>$_POST['id']))->save(array($state=>1));
            }

    	}

    	$this->ajaxReturn();

    }

    /*获取用户的昵称*/
    public function getUserInfo($aData){
        $aData  = Arr::changeIndex($aData, 'opendid');
        if(!$aData){return $aData;}
        $uid = M('Wxuser')->where(array('token'=>session('token')))->getField('id');
        $aUser =  Arr::changeIndex(
            M('wxusers')->where(array('uid'=>$uid, 'openid' => array('in', array_keys($aData))))->select(),
            'openid'
        );
        foreach($aData as $k => $v) {
            $aData[$k]['name'] = isset($aUser[$v['openid']]) ? $aUser[$v['openid']]['nickname']:'';
        }
        return $aData;
    }
}
