<?php
import('@.ORG.WXAction.WXAction_Base');
class WXAction_Image extends WXAction_Base
{
    public function __construct($aData = array(), $token='')
    {
	parent::__construct($aData, $token);
	$this->ImageModel = M('Wx_photo');
}
    public function handle()
    {
        try{
            //先下载文件
            $Download = new DownloadFileFromServer(
                $this->token,
                $this->aData['FromUserName'],
                $this->get('MediaId', '')
            );
            $Res      = $Download->getFile();
            $local_pic= $Res['real_path'];
            //保存
            if(!$this->ImageModel->data($a=array(
                //'token'		=> $this->aData['ToUserName'],
                'token'		=> $this->token,
                'openid'	=> $this->aData['FromUserName'],
                'create_time'   => $this->aData['CreateTime'],
                'media_id'	=> $this->get('MediaId', ''),
                'msg_id'	=> $this->aData['MsgId'],
                'pic'		=> $this->aData['PicUrl'],
                'local_pic'	=> $local_pic,
                'add_time'	=> date('Y-m-d H:i:s')
            ))->add()){
                return;
            }
            //根据不同的项目进行业务逻辑处理
                    $this->hengbo();
            switch($this->aData['ToUserName']){
                //恒博
                case 'gh_3980c0482a72':
                    $this->hengbo();
                break;
            }
        }catch(Exception $E){
            WL('error:' . $E->getMessage());
        }
    }

/*
	恒博
 */
    public function hengbo(){
	//发个图文消息给用户，提示让给图片命名
	//TODO
/*
	httpMethod('http://v.wapwei.com/index.php?g=Home&m=Auth&a=sendTextMsg', array(
		'token'=> '0c9b61e73a09acf11cdc4b0d8e4255f5',
		'openid'=> $this->get('FromUserName'),
		'content'=> '请输入照片的标题'
	));
*/
    }
}
?>
