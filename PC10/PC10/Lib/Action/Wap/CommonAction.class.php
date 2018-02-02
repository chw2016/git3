<?php

class CommonAction extends  Action{
	
	
    Public function verify(){
        import('ORG.Util.Image');
        Image::buildImageVerify(4,1,'png',70,30);
    }
}

?>