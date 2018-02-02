<?php
class MyewmAction extends UserAction{
	public function index(){
$id=$this->_get('hid','intval');
$token=$this->_get('token');
$info=$this->generateQRfromGoogle('http://'.$_SERVER['HTTP_HOST'].'/index.php?g=Wap%26m=Discount%26a=index%26token='.$token.'%26hid='.$id);

	
	$this->assign('info',$info);
		$this->display();
	}
	
public	function generateQRfromGoogle($chl)
{

return '<img src="http://qr.liantu.com/api.php?bg=ffffff&fg=000000&gc=000000&el=l&w=400&m=10&text='.$chl.'"/>';
}
	
	
    }
	
?>