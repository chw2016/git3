<?php
import('@.ORG.WXAction.WXAction_Base');
class WXAction_Text extends WXAction_Base
{
    public function __construct($aData = array(), $token='')
    {
	$this->token	 = $token;
	$this->aData     = $aData;
	$this->Wxphoto   = M('Wx_photo');
}
    public function handle()
    {
	//根据不同的项目进行业务逻辑处理
	switch($this->token){
		//使用照片墙的应用 
		case '0c9b61e73a09acf11cdc4b0d8e4255f5':
		$this->wall();
		break;
	}
    }

/*
	照片墙
 */
    public function wall(){
	$this->Photowall = M('Photo_wall');
	//收到图片后是照片墙
	$aPhoto = $this->Wxphoto->where(array(
		'token' => $this->token,
		'openid' => $this->aData['FromUserName'],
		'create_time' => array('GT', time() - 30)
	))->order('create_time desc')->limit(1)->find();
	$bSave = false;
	if(count($aPhoto) > 0){
		//修改照片的名称 
		if($this->Photowall->where(array(
			'pid'	=> $aPhoto['id'],
			'token' => $this->token,
			'openid' => $this->aData['FromUserName']
		))->count()){
			$bSave = $this->Photowall->where(array(
				'pid'	=> $aPhoto['id'],
				'token' => $this->token,
				'openid' => $this->aData['FromUserName']
			))->data(array('title' => $this->aData['Content']))->save();
		}else{
			$bSave = $this->Photowall->data(array(
				'pid'	=> $aPhoto['id'],
				'token' => $this->token,
				'openid' => $this->aData['FromUserName'],
				'title' => $this->aData['Content']
			))->add();
		}
	if ($bSave) {
            //发个图文消息给用户，提示让给图片命名
            httpMethod('http://v.wapwei.com/index.php?g=Home&m=Auth&a=sendTextMsg', array(
		'token' => $this->token,
		'openid' => $this->aData['FromUserName'],
                'content'=> '恭喜，标题命名成功'
            ));
        }else{
            //发个图文消息给用户，提示让给图片命名
            httpMethod('http://v.wapwei.com/index.php?g=Home&m=Auth&a=sendTextMsg', array(
		'token' => $this->token,
		'openid' => $this->aData['FromUserName'],
                'content'=> '遗憾，系统繁忙，请重新输入标题'
            ));
        }

	}
    }
}
?>
