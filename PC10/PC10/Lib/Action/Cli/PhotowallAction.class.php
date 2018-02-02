<?php
class PhotowallAction extends CliAction {

    public function index()
    {
            foreach(
                M('Wx_photo')
                    ->field('id, token, openid, media_id, local_pic')
                    ->where(array('token' => '0c9b61e73a09acf11cdc4b0d8e4255f5'))
                    ->select()
                as $Row
            ){
                if (!$Row['media_id'] || $Row['local_pic']) {
                    continue;
                }
                $Download = new DownloadFileFromServer(
                    $Row['token'],
                    $Row['openid'],
                    $Row['media_id']
                );
                try{
                    $File = $Download->getFile();
                    if (is_array($File)) {
                        if(M('Wx_photo')
                            ->where(array('id' => $Row['id']))
                            ->data(array('local_pic' => $File['real_path']))
                            ->save()){
                            echo "下载成功";
                        }else{
                            echo "下载失败";
                        };
                    }
                }catch(Exception $E){
                    echo $E->getMessage();
                }
        }
    }
}
