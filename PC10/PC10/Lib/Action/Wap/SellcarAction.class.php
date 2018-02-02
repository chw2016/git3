<?php 
class SellcarAction extends BaseAction{
	public function index(){
        if(isset($_GET['imgpath'])){
           $path=$this->_get('imgpath');
           $this->assign('path',$path); 
        }else{
            $this->assign('path','');
        }
		$this->display();
	}
	//保存数据
	public function saveCar(){
		$db=M('sellcar');
		$data['species']=$_POST['species'];
		$data['brand']=$_POST['brand'];
		$data['presell']=$_POST['presell'];
		$data['first']=$_POST['first'];
		$data['mileage']=$_POST['mileage'];
		$data['sptime']=$_POST['sptime'];
		$data['carColor']=$_POST['carColor'];
		$data['caruser']=$_POST['caruser'];
		$data['contact']=$_POST['contact'];
		$data['speedM']=$_POST['speedM'];
		$data['gasType']=$_POST['gasType'];
		$data['emission']=$_POST['emission'];
		$data['transfer']=$_POST['transfer'];
		$data['mortgage']=$_POST['mortgage'];
		$data['carcondition']=$_POST['carcondition'];
		$data['token']=$this->_get('token');
        $data['imgurl']=$_POST['imgurl'];
		$result=$db->data($data)->add();
		if($result){
			$this->success('保存成功！',U(MODULE_NAME.'/index',array('token'=>$data['token'])));
		}else{
			$this->error('保存失败！',U(MODULE_NAME.'/index',array('token'=>$data['token'])));
		}
	}

	//上传图片
	public function importImg(){
				$max_file_size=2000000;
				$destination_folder="/upload/sellcar/img"; //上传文件路径  
				$imgpreview=1;      //是否生成预览图(1为生成,其他为不生成);  
	 			$imgpreviewsize=1/2;    //缩略图比例 

	 			$uptypes=array(  
    				'image/jpg',  
    				'image/jpeg',  
    				'image/png',  
    				'image/pjpeg',  
    				'image/gif',  
    				'image/bmp',  
    				'image/x-png'  
				); 
    			if (!is_uploaded_file($_FILES["upfile"]['tmp_name']))  
    			//是否存在文件  
    			{  
         			echo "图片不存在!";  
         			exit;  
    			}  
  
    			$file = $_FILES["upfile"]; 

    			

    			if($max_file_size < $file["size"])  
    			//检查文件大小  
    			{  
        			echo "文件太大!";  
        			exit;  
    			}  
  
    			if(!in_array($file["type"], $uptypes))  
    			//检查文件类型  
    			{  
        			echo "文件类型不符!".$file["type"];  
        			exit;  
    			}  
  				//如果目录不存在则创建
    			if(!file_exists($destination_folder))  
    			{  
        			mkdir($destination_folder);  
    			}  
    			$filename=$file["tmp_name"];  
    			$image_size = getimagesize($filename);  
    			$pinfo=pathinfo($file["name"]);  
    			$ftype=$pinfo['extension'];  
    			$destination = $destination_folder.time().".".$ftype;
    			  
    			if (file_exists($destination))  
    			{  
        			echo "同名文件已经存在了";  
        			exit;  
    			}  
    			echo $filename."<br/>";
  				echo $destination;
    			if(!move_uploaded_file($file["name"], $destination_folder))  
    			{  
        			echo "移动文件出错";
        			exit;  
    			}else{
    				echo "文件上传成功";
    			}  
  
    			$pinfo=pathinfo($destination);  
    			$fname=$pinfo[basename];  
    			//上传图片成功
    			/*echo " <font color=red>已经成功上传</font><br>文件名:  <font color=blue>".$destination_folder.$fname."</font><br>";  
    			echo " 宽度:".$image_size[0];  
    			echo " 长度:".$image_size[1];  
    			echo "<br> 大小:".$file["size"]." bytes";  */ 
		 
	}
    //上传图片uploads类
    public function uploadsT(){
        import('ORG.Net.UploadFile');//导入上传类
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('jpg' ,'png' ,'gif');// 设置附件上传类型
        $upload->savePath =  './upload/wapimg/';// 设置附件上传目录
        if(!file_exists($upload->savePath)){
            mkdir($upload->savePath);
        }
        if($upload->upload()){
            // echo "<script language='JavaScrip'>alert('上传成功！);</script>";
            $info =  $upload->getUploadFileInfo();
            $imgpath=$info[0]['savepath'].$info[0]['savename'];
            $arr = array(
                    'name'=>$info[0]['savename'],
                    'pic'=>$imgpath,
                    'size'=>$size
            );
            echo json_encode($arr);
        }else{
            $error = $this->error($upload->getErrorMsg());
            // echo "<script>alert('上传失败！');window.location.href=".U('Sellcar/index')."</script>";
            // $this->error('保存失败！',U(MODULE_NAME.'/index',array('token'=>$_GET['token'])));
        }
    }

    //显示二手车信息
    public function info(){
        $token=$this->token;
        $db=M('sellcar');
        $result=$db->where(array('token'=>$token))->select();
        $this->assign('res',$result);
        $this->display();
    }
    //显示内容页
    public function content(){
        $id = $this->_get('id','intval');
        $token = $this->_get('token');
        $db = M('sellcar');
        $result = $db->where(array('token'=>$token,'id'=>$id))->find();
        $this->res = $result;
        $this->display();
    }
}

?>