<?php
class DiymenAction extends UserAction{
	//会员卡配置
	public function index(){
		$data=M('Diymen_set')->where(array('token'=>$_SESSION['token']))->find();
        $wxuserdata = M('Wxuser')->where(array('token'=>session('token')))->find();
		if(IS_POST){
			$_POST['token']=$_SESSION['token'];			
			if($data==false){				
				$this->all_insert('Diymen_set');
			}else{
				$_POST['id']=$data['id'];
				$this->all_save('Diymen_set');
			}
		}else{
			$this->assign('diymen',$data);
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();//dump($class);
			foreach($class as $key=>$vo){
				$c=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id']))->order('sort desc')->select();
				$class[$key]['class']=$c;
			}
		//dump($class);
            $this->assign('wxuserdata',$wxuserdata);
			$this->assign('class',$class);
			$this->display();
		}
	}
	
	
	public function  class_add(){
		if(IS_POST){
			$this->all_insert('Diymen_class','/class_add');
		}else{
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();
            $this->assign('class',$class);
			$this->display();
		}
	}
	public function  class_del(){		
		$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$this->_get('id')))->order('sort desc')->find();
		//echo M('Diymen_class')->getLastSql();exit;
		if($class==false){
			$back=M('Diymen_class')->where(array('token'=>session('token'),'id'=>$this->_get('id')))->delete();
			if($back==true){
				$this->success('删除成功','../index.php?g=User&m=Diymen&a=index');
			}else{
				$this->error('删除失败','../index.php?g=User&m=Diymen&a=index');
			}
		}else{
				$this->error('请删除该分类下的子分类');
		}
		
		
	}
	public function  class_edit(){
        $wxuserdata = M('Wxuser')->where(array('token'=>session('token')))->find();
		if(IS_POST){
			$_POST['id']=$_REQUEST['id'];
			$this->all_save('Diymen_class','/classs_edit'.array('id='.$_REQUEST['id']));
		}else{
			$data=M('Diymen_class')->where(array('token'=>session('token'),'id'=>$_REQUEST['id']))->find();
			if($data==false){
				$this->error('您所操作的数据对象不存在！');
			}else{
				$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0))->order('sort desc')->select();//dump($class);
				$this->assign('class',$class);
				$this->assign('show',$data);
			}
			$arr =M('Diymen_set')->where(array('token'=>session('token')))->find();

            $this->assign('wxuserdata',$wxuserdata);
            $this->assign('arr',$arr);
			$this->display();
		}

	}


    public function getAccesstoken(){
        $url = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
        $data = array();
        $data['component_appid'] = 'wxe7be6810523b9ea2';
        $data['component_appsecret'] = '0c79e1fa963cd80cc0be99b20a18faeb';
        $res = M('Open_ticket_set')->where(array('AppId'=>'wxe7be6810523b9ea2'))->find();
        if($res['ComponentVerifyTicket'] != null){
            $data['component_verify_ticket'] = $res['ComponentVerifyTicket'];
        }
        $data = $this->encode($data);
        $returndata = $this->api_notice_increment($url,$data);
        $returndata = json_decode($returndata,true);
        return $returndata;
    }

	public function  class_send(){
		if(IS_POST){
            $wxuserdata = M('Wxuser')->where(array('token'=>session('token')))->find();
            if($wxuserdata['is_auth']){
                if(true){
                    $componseaccess = $this->getAccesstoken();
                    if($componseaccess['component_access_token']){
                        $accessurl = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=".$componseaccess['component_access_token'];
                        $repostdata = array();
                        $repostdata['component_appid'] = 'wxe7be6810523b9ea2';
                        $repostdata['authorizer_appid'] = $wxuserdata['authorizer_appid'];
                        $repostdata['authorizer_refresh_token'] = $wxuserdata['authorizer_refresh_token'];
                        $repostdata = $this->encode($repostdata);
                        $json = $this->api_notice_increment($accessurl,$repostdata);
                        $json = json_decode($json);
                        $json->access_token = $json->authorizer_access_token;
                    }else{
                        $this->error('获取失败组件access_token');exit;
                    }
                }else{
                    $this->error('您的帐号类型还没有创建自定义菜单的权限哦');exit;
                }

            }else {
                $api = M('Diymen_set')->where(array('token' => session('token')))->find();
                //dump($api);
                $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $api['appid'] . '&secret=' . $api['appsecret'];
                //echo $url_get;exit;
                $json = json_decode(file_get_contents($url_get));
                if($api['appid']==false||$api['appsecret']==false){$this->error('必须先填写【AppId】【 AppSecret】');exit;}
            }
			if(!isset($json->access_token)){
				$this->error('获取access_token失败,请重试','index.php?g=User&m=Diymen&a=index');
			}

			$data = '{"button":[';
			
			$class=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>0,'is_show'=>1))->limit(3)->order('sort desc')->select();//dump($class);
			$i=1;$k=1;
                       $countz=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>'0','is_show'=>1))->limit(3)->order('sort desc')->count();
			foreach($class as $key=>$vo){
				//主菜单

				$data.='{"name":"'.$vo['title'].'",';
				$c=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort desc')->select();
				$count=M('Diymen_class')->where(array('token'=>session('token'),'pid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort desc')->count();
				//子菜单
				
				
				if($c!=false){
					$data.='"sub_button":[';
				}else{
                    if ($vo['url']==""){
					    $data.='"type":"click","key":"'.$vo['keyword'].'"';
                    }else{
                        if($vo['auth_url']==null){
                            $data.='"type":"view","url":"'.htmlspecialchars_decode($vo['url']).'"';
                        }else{
                            $data.='"type":"view","url":"'.htmlspecialchars_decode($vo['auth_url']).'"';
                        }
                    }
				}
				$i=1;
				foreach($c as $voo){					
					if($i==$count){
						if ($voo['url']==""){
						    $data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"}';
                        }else{
                            if($voo['auth_url']==null){
                                $data.='{"type":"view","name":"'.$voo['title'].'","url":"'.htmlspecialchars_decode($voo['url']) .'"}';
                            }else{
						        $data.='{"type":"view","name":"'.$voo['title'].'","url":"'.htmlspecialchars_decode($voo['auth_url']) .'"}';
                            }
                        }
					}else{
						if ($voo['url']==""){
						    $data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"},';
                        }else{
                            if($voo['auth_url']==null){
						        $data.='{"type":"view","name":"'.$voo['title'].'","url":"'.htmlspecialchars_decode($voo['url']) .'"},';
                            }else{
                                $data.='{"type":"view","name":"'.$voo['title'].'","url":"'.htmlspecialchars_decode($voo['auth_url']) .'"},';
                            }
                        }
					}
					$i++;
				}
				if($c!=false){
					$data.=']';
				}
				
				if($k==$countz){
					$data.='}';
				}else{
					$data.='},';
				}
				$k++;
			}	
	$data.=']}';

			file_get_contents('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$json->access_token);

			$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$json->access_token;
			$returndata = $this->api_notice_increment($url,$data);
			$returnjson = json_decode($returndata);
			if($returnjson->errcode != 0){
				$this->error('操作失败'.$returnjson->errmsg,'index.php?g=User&m=Diymen&a=index');
			}else{
				$this->success('操作成功','index.php?g=User&m=Diymen&a=index');
			}
			exit;
		}else{
			$this->error('非法操作');
		}
	}

    public function delete_menu(){
        if(IS_POST){
            $wxuserdata = M('Wxuser')->where(array('token'=>session('token')))->find();
            if($wxuserdata['is_auth']){
                if($wxuserdata['service_type_info'] == 2 || ($wxuserdata['verify_type_info'] >=0 && ($wxuserdata['service_type_info'] == 0 || $wxuserdata['service_type_info'] == 1))){
                    $componseaccess = $this->getAccesstoken();
                    if($componseaccess['component_access_token']){
                        $accessurl = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=".$componseaccess['component_access_token'];
                        $repostdata = array();
                        $repostdata['component_appid'] = 'wxe7be6810523b9ea2';
                        $repostdata['authorizer_appid'] = $wxuserdata['authorizer_appid'];
                        $repostdata['authorizer_refresh_token'] = $wxuserdata['authorizer_refresh_token'];
                        $repostdata = $this->encode($repostdata);
                        $json = $this->api_notice_increment($accessurl,$repostdata);
                        $json = json_decode($json);
                        $json->access_token = $json->authorizer_access_token;
                    }else{
                        $this->error('获取失败组件access_token');exit;
                    }
                }else{
                    $this->error('您的帐号类型还没有创建自定义菜单的权限哦');exit;
                }

            }else {
                $api = M('Diymen_set')->where(array('token' => session('token')))->find();
                //dump($api);
                $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $api['appid'] . '&secret=' . $api['appsecret'];
                if ($api['appid'] == false || $api['appsecret'] == false) {
                    $this->error('必须先填写【AppId】【 AppSecret】');
                    exit;
                }
                $json = json_decode(file_get_contents($url_get));
            }
            if (!isset($json->access_token)) {
                $this->error('获取access_token失败,请重试', 'index.php?g=User&m=Diymen&a=index');
            }
            $data = array();
            $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $json->access_token;
            $returndata = $this->api_notice_increment($url, $data);
            $returnjson = json_decode($returndata);
            if ($returnjson->errcode != 0) {
                $this->error('操作失败' . $returnjson->errmsg, 'index.php?g=User&m=Diymen&a=index');
            } else {
                $this->success('操作成功', 'index.php?g=User&m=Diymen&a=index');
            }
            exit;


        }else{
            $this->error('非法操作');
        }
    }

	function api_notice_increment($url, $data){
		 $ch = curl_init(); 
		  $header = "Accept-Charset: utf-8"; 
		 curl_setopt($ch, CURLOPT_URL, $url); 
		 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		 curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		 curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		 $tmpInfo = curl_exec($ch);

		 if (curl_errno($ch)) {  
			return false;
		 }else{
			return $tmpInfo; 
		 }
	}

}
	?>