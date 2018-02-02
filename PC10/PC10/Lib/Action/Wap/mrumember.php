<?php 
//判断是否会员 include"./Lib/Action/Wap/mrumember.php";
$uid=M('wxuser')->where(array('token'=>$_GET['token']))->getField('id');
$member=M('Usercenter_memberlist')->where(array('openid'=>$_GET['openid'],'uid'=>$uid))->find();
$_SESSION['url']=$_SERVER['REQUEST_URI'];//存地址
//echo $_SERVER['REQUEST_URI'];
if(!$member){
	echo '<script>alert("您还不是会员,请注册会员");location.href="'.U('MruMhyzx/zc',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';die;
}

//会员注册成功处的跳转
/* if($_SESSION['url']){
	echo '<script>alert("注册成功");location.href="'.C('site_url').$_SESSION['url'].'"</script>';die;
}else{
	echo '<script>alert("注册成功");location.href="'.U('Mhyzxsy/index',array('token'=>$_GET['token'],'openid'=>$_GET['openid'])).'"</script>';die;
} */

?>