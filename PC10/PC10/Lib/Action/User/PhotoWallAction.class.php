<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/1
 * Time: 10:50
 * Title:照片墙
 */
class PhotoWallAction extends TableAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'User/default/photowall';
    /**
     *  Token
     **/
    public function _initialize()
    {/*$oWallModel = M('Photo_wall');
        $oPhotoModel = M('Wx_photo');*/
        $this->wall = D('Photo_wall');
        $this->wxph = D('Wx_photo');
        parent::_initialize();
    }

    protected function setHeader()
    {
        return array(
            array(
                'name' => '照片管理',
                'url' => U('PhotoWall/index', array('token' => $this->_sToken))
            ),
            array(
                'name' => '照片墙前端图片',
                'url' => U('PhotoWall/imgCreate', array('token' => $this->_sToken))
            )
        );
    }
    /*照片墙*/
    public function index(){
        $aWhere = array('token'=>$this->_sToken);
        $this->table(
            array(
                //'id' => 'id',//如果主键不是id，则需要设置
                'HeadHover' => U('PhotoWall/index', array('token' => $this->_sToken)),

                'tips' => array(
                    '你可以在这里管理照片信息'
                ),
                'Table_Header' => array(
                    'ID','微信昵称','图片', '主题','审核状态','上传时间','操作'
                ),
                'aListImg' => array(
                    'container' => array('pid'),
                    'width'     => 70,
                    'height'    => 70
                ),
                'List_Opt' => array(
                    array(
                        'name' => '审核',
                        'url'  => U('PhotoWall/check')
                    ),
                    array(
                        'name' => '删除',
                        'url'  => U('PhotoWall/picDel')
                    )
                )
            ),
            $this->wall->where($aWhere)->count(),
            $this->wall->field('id,openid,pid,title,status')->where($aWhere)->order('id desc'),
            array($this, '_handle')
        );
    }
    public function _handle($aData){
        foreach ($aData as $iKey=>$aValue) {
            $aPic =$this->wxph->where(array('id'=>$aValue['pid']))->find();
            //$aData[$iKey]['pid'] = $aPic['pic'];
            $aData[$iKey]['pid'] = $aPic['local_pic'];
            $aData[$iKey]['add_time'] = $aPic['add_time'];
            if($aValue['status'] == 0){
                $aData[$iKey]['status'] = '未审核';
            }elseif($aValue['status'] == 1){
                $aData[$iKey]['status'] = '审核通过';
            }elseif($aValue['status'] == 2){
                $aData[$iKey]['status'] = '审核未通过';
            }
            $aUser = M('Wxuser')->where(array('token'=>$this->_sToken))->find();
            $aUsers = M('Wxusers')->where(array('uid'=>$aUser['id'],'openid'=>$aValue['openid']))->find();
            $aData[$iKey]['openid'] = $aUsers['nickname'];
        }
        return $aData;
    }
    #图片审核
    public function check(){
        $owallModel = $this->wall;
        $oWxModel = $this->wxph;
        $iTem = $owallModel->where(array('id'=>FC::G('id')))->find();
        $aImga = $oWxModel->where(array('id'=>$iTem['pid']))->find();
        if(IS_AJAX){
            $iTems = $owallModel->where(array('id'=>FC::P('id')))->find();
            if(!$iTems) $this->error2('非法操作');
            if($owallModel->where(array('id'=>FC::P('id')))->save($_POST)){
                $this->success2('操作成功', U('PhotoWall/index', array('token' => $this->token)));
            }else{
                $this->error2('操作失败');
            }
        }else{
            $this->assign(array(
                'aInfo'=>$iTem,
                'aImga'=>$aImga,
                'ExtraBtn' => array(
                array(
                    'url'  => U('PhotoWall/index', array('token' => $this->_sToken)),
                    'name' => '返回'
                ))
            ));
            $this->UDisplay('check');
        }
    }
    public function picDel(){
        $owallMOdel = $this->wall;
        $iTem = $owallMOdel->where(array('id'=>FC::G('id')))->find();
        if (!$iTem) $this->error2('非法操作！');
        if ($owallMOdel->where(array('id' => FC::G('id')))->delete()) {
            $this->success2('删除成功', U('PhotoWall/index', array('token' => $this->token)));
        } else {
            $this->error2('删除失败');
        }
    }
    #照片墙前端图片
    public function imgCreate(){
        $oImgaModel = M('Imag');
        $aWhere =array(
            'token'=>$this->_sToken,
            'app'=>'PhotoWall',
            'type'=>'img1'
        );
        $this->assign(array(
            'aImga'=>$oImgaModel->where($aWhere)->find()
        ));
        $this->UDisplay('imgCreate');
    }



}
