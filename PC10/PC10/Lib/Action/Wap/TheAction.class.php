<?php
/**
 *  技术支持：张银飞 ,李铭,张湘南(由你定后台)
 **/
class TheAction extends BaseAction
{
    /**
     *  定义项目模板BaseDir
     **/
    protected $_sTplBaseDir = 'Wap/default/the';

    public function _initialize()
    {
        $this->autoShare = true;
        parent::_initialize();
    }
            /*屋顶分布页*/
     public function theroof(){

         $this->UDisplay('theroof');
     }

            /*地面集中页*/
    public function thefloor(){
        $this->UDisplay('thefloor');
    }

            /*加盟合作页*/
    public function thejoin(){
        $this->UDisplay('thejoin');
    }

            /*项目案例页*/
    public function thecase(){
        $this->UDisplay('thecase');
    }

            /*联系我们页*/
    public function thewe(){
        $this->UDisplay('thewe');
    }





}