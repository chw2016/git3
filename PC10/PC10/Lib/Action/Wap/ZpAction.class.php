<?php
/**
 * @Author: zhang
 * @Date:   2015-03-27 16:39:17
 * @Last Modified by:   zhang
 * @Last Modified time: 2015-03-31 17:30:51
 * 米业前端
 */
class ZpAction extends BaseAction{


	// 初始化_initialize
	public function _initialize(){
		parent::_initialize();
		if($token = $_REQUEST['token']){
			$this->token = $token;
		}


	}

	public function index2(){
	    if(IS_AJAX){
	    	$data['name']=$this->_post('name');
	    	if(!$data['name']){
	    		$res['str']="姓名必须镇写";
	    		$this->ajaxReturn($res);
	    		die;
	    	}
	    	$data['sj']=$this->_post('sj');
	    	if(!$data['sj']){
	    		$res['str']="手机必须镇写";
	    		$this->ajaxReturn($res);
	    		die;
	    	}
	    	$data['gw']=$this->_post('gw');
	    	if(!$data['gw']){
	    		$res['str']="岗位必须镇写";
	    		$this->ajaxReturn($res);
	    		die;
	    	}

	    	$data['openid']=$this->_post('openid');
/* 	    	if(!$data['openid']){
	    		$res['str']="对不起！请注册会员";
	    		$this->ajaxReturn($res);
	    		die;
	    	} */
	    	$data['token']=$this->_post('token');
	    	$b=M('zpname')->add($data);
	    	if($b){
	    		$res['str']="应聘信息发布成功";
	    	}

    		$res['page']=22;
    		$this->ajaxReturn($res);
        }
	}

	// 首页显示，店铺发送
	public function index(){
		$token=$this->token;
		//print_r($token);die;
		$zp1=M('zp1')->where(array('token'=>$token))->find();
		//print_r($zp1);die;
		$zp2=M('zp2')->where(array('token'=>$token))->find();
		$zp3=M('zp3')->where(array('token'=>$token))->find();
		$zp4=M('zp4')->where(array('token'=>$token))->find();

		//$zp6=M('zp6')->where(array('token'=>$token))->find();
		$zp7=M('zp7')->where(array('token'=>$token))->find();
		$zp8=M('zp8')->where(array('token'=>$token))->find();
		$zp9=M('zp9')->where(array('token'=>$token))->find();

		$zp55=M('zp5')->where(array('token'=>$token))->order('sort')->select();
		$zp55=array_chunk($zp55,4);
	/* 	foreach ($zp55 as $v){
			P($v);
		}
		die;  */

		$zp5=M('zp5')->where(array('token'=>$token))->order('sort')->limit(0,1)->select();
		//p($zp5['0']['']);die;
		$this->assign('zp1',$zp1);
		$this->assign('zp2',$zp2);
		$this->assign('zp3',$zp3);
		$this->assign('zp4',$zp4);
		$this->assign('zp5',$zp5);
		//$this->assign('zp6',$zp6);
		$this->assign('zp7',$zp7);
		$this->assign('zp8',$zp8);
		$this->assign('zp9',$zp9);
		$this->assign('zp55',$zp55);

/* // 		$token=$this->token;
// 	    $list=M('mru_Zp')->where(array('token'=>$token))->field('content',true)->select();

// 	    $count      = M('mru_Zp')->where(array('token'=>$token))->count();
// 	    $Page       = new Page($count,10);
// 	    $show       = $Page->show();
// 	    $list = M('mru_Zp')->where(array('token'=>$token))->field('content',true)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
// 	    $this->assign('page',$show);
// 	    $this->assign('list',$list); */

	  //  p($list);

		$this->display();
	}

	public function ajax(){
		if(IS_AJAX){
			$id=$this->_post('id');
			$list=M('zp5')->where(array('id'=>$id))->find();
            //$list['add_time'] = date('Y-m-d',$list['add_time']);
			$list['content2']=str_replace("\n","<br />",$list['content2']);

			$str='';
$str.=<<<str

           <div class="officeinfo" style=" background-color:rgb(255,255,255);  font-size: 12px;">
		<div style="  background-color: #EEE;width: 100%;height: 185px;">
			<div class="fanhui"><img src="/tpl/static/wapweiui/Zp/image/fanhui.png" style="  padding: 10px;width: 11%;" ></div>
			<div style="text-align: center;"><img src="/tpl/static/wapweiui/Zp/image/zhiwei.png" style="width:30%;margin-top:5px"></div>
			<div>
				<div style="text-align: center;font-size: 16px;">{$list['name']}</div>
				<div style="text-align: center;font-size: 9px;padding-top: 5px; color:#B8B8B8;">你就是我们要找的人</div>
			</div>

		</div>
		<div style="margin:0 20px;">
		    <div class="officeinfos" style="margin:5px; font-size: 15px;margin-top:15px;">
               <div>发布时间：<span style="color:#B8B8B8;font-size: 14px;">{$list['add_time']}</span></div>
           </div>
           <div class="officeinfos" style="margin:5px; margin-bottom: -15px;font-size: 15px;">
               <div style="margin-bottom: 10px;">招聘人数：<span style="color:#B8B8B8;font-size: 14px;">{$list['number']}人</span></div>
           </div>
			<div class="officeinfos" style="margin:5px;">
				<h3 class="officelogo"  style="position: relative;bottom: -10px;font-size: 15px;">
					任职资格：
				</h3>
				 
				<div class="officecenter" style="color:#B8B8B8;margin-top:15px;font-size: 14px;text-indent: 2em;  " >
                    {$list['content2']}
              </div>
			</div>
			<div class="officeinfos" style="margin:5px;">
				<h3 class="officelogo"  style="position: relative;bottom: -10px;font-size: 15px; ">岗位描述：</h3>
				<div class="officecenter" style="color:#B8B8B8;margin-top:15px;font-size: 14px; ">{$list['content']}</div>
			</div>
		</div>
	</div>
<script>
     $(document).on('touchstart', '.fanhui',function(){
         $('#shadow').hide();
         $('#wrapper').animate({left:'100%'},'slow', function(){
         });
     })
</script>
str;
			$res['str']=$str;
			$res['page']=22;
			$this->ajaxReturn($res);
		}

	}

	public function show(){
/*         $list=M('mru_Zp')->where(array('id'=>$_GET['id']))->find();
        $this->assign('list',$list); */
		echo  $this->json();
	}






	function json(){
	  $token=$this->token;
	  $zp1=M('zp1')->where(array('token'=>$token))->find();
	  $zp2=M('zp2')->where(array('token'=>$token))->find();
	  $zp3=M('zp3')->where(array('token'=>$token))->find();
	  $zp4=M('zp4')->where(array('token'=>$token))->find();
	 // $zp5=M('zp5')->where(array('token'=>$token))->find();
	 // $zp6=M('zp6')->where(array('token'=>$token))->find();
	  $zp7=M('zp7')->where(array('token'=>$token))->find();
	  $zp8=M('zp8')->where(array('token'=>$token))->find();
	  $zp9=M('zp9')->where(array('token'=>$token))->find();
	  $zp5=M('zp5')->where(array('token'=>$token))->order('sort')->limit(0,1)->select();

		return '{
			"success": true,
			"code": 200,
			"msg": "操作成功",
			"obj": {
			"id": 3079297,
			"name": "'.$zp9['name'].'",
			"createUser": "4a2d8aae4bc9cf5b014bdec22c3a575c",
			"createTime": 1427427634000,
			"type": 103,
			"pageMode": 0,
			"image": {
			"bgAudio": {
			"url": "/tpl/static/wapweiui/hxfz/zp.mp3",
			"type": "2"
			},
			"imgSrc": "'.$zp9['pic2'].'",
			"isAdvancedUser": false
		},
		"isTpl": 0,
		"isPromotion": 0,
		"status": 1,
		"openLimit": 0,
		"startDate": null,
		"endDate": null,
		"updateTime": 1428482361000,
		"publishTime": 1428482364000,
		"applyTemplate": 0,
		"applyPromotion": 0,
		"sourceId": null,
		"code": "jG6vqeDx",
		"description": "'.$zp8['name1'].'",
		"sort": 0,
		"bgAudio": null,
		"cover": null,
		"property": "{}",
		"bizType": 0,
		"pageCount": 0,
		"dataCount": 0,
		"showCount": 0,
		"userLoginName": null,
		"userName": null
		},
		"map": null,
		"list": [
		{
			"id": 45566317,
			"sceneId": 3079297,
			"num": 1,
			"name": null,
			"properties": null,
			"elements": [
			{
				"content": "",
				"css": {
				"top": "91px",
				"left": "112px",
				"zIndex": "1",
				"width": 80,
				"height": 97,
				"backgroundColor": "",
				"opacity": 1,
				"color": "#676767",
				"borderWidth": 0,
				"borderStyle": "solid",
				"borderColor": "rgba(0,0,0,1)",
				"paddingBottom": 0,
				"paddingTop": 0,
				"lineHeight": 1,
				"borderRadius": "0px",
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 0,
				"boxShadow": "0px 0px 0px rgba(0,0,0,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0,
				"borderBottomRightRadius": "0px",
				"borderBottomLeftRadius": "0px",
				"borderTopRightRadius": "0px",
				"borderTopLeftRadius": "0px"
				},
				"id": 587,
				"num": 1,
				"pageId": 45566317,
				"properties": {
				"width": "100px",
				"height": "100px",
				"src": "'.$zp1['pic2'].'",
				"imgStyle": {
				"width": 80,
				"height": 97,
				"marginTop": "0px",
				"marginLeft": "0px"
				},
				"anim": {
				"type": 8,
				"direction": 0,
				"duration": 1.5,
				"delay": 2,
				"countNum": 1,
				"count": false
				}
				},
				"sceneId": 3079297,
				"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "277px",
				"left": "60px",
				"zIndex": "2",
				"width": 198,
				"height": 80
			},
			"id": 276,
			"num": 1,
			"pageId": 45566317,
			"properties": {
			"width": 198,
			"height": 80,
			"src": "'.$zp1['pic3'].'",
			"imgStyle": {
			"width": 198,
			"height": 100,
			"marginTop": "-10px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 2,
			"direction": 1,
			"duration": 2,
			"delay": 0,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"css": {
				"zIndex": "3"
				},
				"id": 997,
				"num": 0,
				"pageId": 45566317,
				"properties": {
				"imgSrc": ""
				},
				"sceneId": 3079297,
				"type": 3
			}
			],
			"scene": null
		},
		{
			"id": 45671128,
			"sceneId": 3079297,
			"num": 2,
			"name": null,
			"properties": null,
			"elements": [
			{
				"content": "",
				"css": {
				"top": "82px",
				"left": "73.86339569091797px",
				"zIndex": "1",
				"width": 240,
				"height": 97,
				"transform": "rotateZ(0.09722786162603825deg)"
				},
				"id": 87,
				"num": 1,
				"pageId": 45671128,
				"properties": {
				"width": 240,
				"height": 97,
				"src": "image/yq0KA1UU8CWAeAOaAAALSHtr_Xc944.png",
				"imgStyle": {
				"width": 240,
				"height": 97,
				"marginTop": "0px",
				"marginLeft": "0px"
				},
				"anim": {
				"type": 4,
				"direction": 0,
				"duration": 1,
				"delay": 1,
				"countNum": 1
				}
				},
				"sceneId": 3079297,
				"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "128px",
				"left": "170.07725524902344px",
				"zIndex": "2",
				"width": 126,
				"height": 61
			},
			"id": 435,
			"num": 1,
			"pageId": 45671128,
			"properties": {
			"width": 126,
			"height": 61,
			"src": "image/yq0KA1UU74eAHdv1AAALSHtr_Xc904.png",
			"imgStyle": {
			"width": 150,
			"height": 61,
			"marginTop": "0px",
			"marginLeft": "-12px"
			},
			"anim": {
			"type": 4,
			"direction": 0,
			"duration": 1,
			"delay": 1.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": 73,
				"left": 254.140625,
				"zIndex": "3",
				"width": 57,
				"height": 106
			},
			"id": 189,
			"num": 1,
			"pageId": 45671128,
			"properties": {
			"width": 57,
			"height": 106,
			"src": "image/yq0KA1UU8AGAHXb1AAALVwUgXOg666.png",
			"imgStyle": {
			"width": 77,
			"height": 106,
			"marginTop": "0px",
			"marginLeft": "-10.5px"
			},
			"anim": {
			"type": 4,
			"direction": 2,
			"duration": 1,
			"delay": 0.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "176px",
				"left": "154.5072784423828px",
				"zIndex": "4",
				"width": 146,
				"height": 44,
				"transform": "rotateZ(346.1899006798239deg)"
				},
				"id": 622,
				"num": 1,
				"pageId": 45671128,
				"properties": {
				"width": 146,
				"height": 44,
				"src": "",
				"imgStyle": {
				"width": 146,
				"height": 44,
				"marginTop": "0px",
				"marginLeft": "0px"
				},
				"anim": {
				"type": 4,
				"direction": 0,
				"duration": 1,
				"delay": 2.5,
				"countNum": 1
				}
				},
				"sceneId": 3079297,
				"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "148.60324096679688px",
				"left": "130.98904418945313px",
				"zIndex": "5",
				"width": 167,
				"height": 62,
				"transform": "rotateZ(12.830689178360615deg)"
				},
				"id": 353,
				"num": 1,
				"pageId": 45671128,
				"properties": {
				"width": 167,
				"height": 62,
				"src": "image/yq0KA1UU8L6AR5hsAAALKBy4Elw324.png",
				"imgStyle": {
				"width": 167,
				"height": 62,
				"marginTop": "0px",
				"marginLeft": "0px"
				},
				"anim": {
				"type": 4,
				"direction": 0,
				"duration": 1,
				"delay": 2,
				"countNum": 1
				}
				},
				"sceneId": 3079297,
				"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "345.82489013671875px",
				"left": "7.18511962890625px",
				"zIndex": "6",
				"width": 307,
				"height": 118
			},
			"id": 223,
			"num": 1,
			"pageId": 45671128,
			"properties": {
			"width": 307,
			"height": 118,
			"src": "'.$zp2['pic'].'",
			"imgStyle": {
			"width": 310,
			"height": 118,
			"marginTop": "0px",
			"marginLeft": "-1.5px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 3.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "483.8125px",
				"left": "7.171875px",
				"zIndex": "7",
				"width": 80,
				"height": 40
			},
			"id": 501,
			"num": 1,
			"pageId": 45671128,
			"properties": {
			"width": "100px",
			"height": "100px",
			"src": "image/yq0KA1UU8hSAfbBcAAA1ugbz3vI612.png",
			"imgStyle": {
			"width": 80,
			"height": 40,
			"marginTop": "0",
			"marginLeft": "0"
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": 483.8125,
				"left": "17.171875px",
				"zIndex": "8",
				"width": 80,
				"height": 113
			},
			"id": 376,
			"num": 1,
			"pageId": 45671128,
			"properties": {
			"width": "100px",
			"height": "100px",
			"src": "image/yq0KA1UU8hSAfbBcAAA1ugbz3vI612.png",
			"imgStyle": {
			"width": 80,
			"height": 113,
			"marginTop": "0",
			"marginLeft": "0"
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"css": {
				"zIndex": "9"
				},
				"id": 572,
				"num": 0,
				"pageId": 45671128,
				"properties": {
				"imgSrc": "'.$zp1['pic4'].'"
				},
				"sceneId": 3079297,
				"type": 3
			}
			],
			"scene": null
		},
		{
			"id": 48747497,
			"sceneId": 3079297,
			"num": 3,
			"name": null,
			"properties": null,
			"elements": [
			{
				"css": {
				"zIndex": "1"
				},
				"id": 15,
				"num": 0,
				"pageId": 48747497,
				"properties": {
				"imgSrc": "'.$zp3['pic'].'"
				},
				"sceneId": 3079297,
				"type": 3
			},
			{
				"content": "",
				"css": {
				"top": 0,
				"left": 0,
				"zIndex": "2",
				"width": 317,
				"height": 209
			},
			"id": 547,
			"num": 1,
			"pageId": 48747497,
			"properties": {
			"width": 317,
			"height": 209,
			"src": "'.$zp3['pic2'].'",
			"imgStyle": {
			"width": 327,
			"height": 209,
			"marginTop": "0px",
			"marginLeft": "-5px"
			},
			"anim": {
			"type": 4,
			"direction": 0,
			"duration": 2,
			"delay": 0,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "30px",
				"left": "77px",
				"zIndex": "3",
				"width": 50,
				"height": 134
			},
			"id": 24,
			"num": 1,
			"pageId": 48747497,
			"properties": {
			"width": 50,
			"height": 134,
			"src": "'.$zp3['pic3'].'",
			"imgStyle": {
			"width": 50,
			"height": 134,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 2,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "27px",
				"left": "117px",
				"zIndex": "4",
				"width": 52,
				"height": 138
			},
			"id": 273,
			"num": 1,
			"pageId": 48747497,
			"properties": {
			"width": 52,
			"height": 138,
			"src": "'.$zp3['pic4'].'",
			"imgStyle": {
			"width": 52,
			"height": 140,
			"marginTop": "-1px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 2,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "28px",
				"left": "153px",
				"zIndex": "5",
				"width": 51,
				"height": 134
			},
			"id": 197,
			"num": 1,
			"pageId": 48747497,
			"properties": {
			"width": 51,
			"height": 134,
			"src": "'.$zp3['pic5'].'",
			"imgStyle": {
			"width": 51,
			"height": 137,
			"marginTop": "-1px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 2,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "28px",
				"left": "194px",
				"zIndex": "6",
				"width": 51,
				"height": 135
			},
			"id": 465,
			"num": 1,
			"pageId": 48747497,
			"properties": {
			"width": 51,
			"height": 135,
			"src": "'.$zp3['pic6'].'",
			"imgStyle": {
			"width": 51,
			"height": 137,
			"marginTop": "-1px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 2,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			}
			],
			"scene": null
		},
		{
			"id": 48747501,
			"sceneId": 3079297,
			"num": 4,
			"name": null,
			"properties": null,
			"elements": [
			{
				"css": {
				"zIndex": "1"
				},
				"id": 66,
				"num": 0,
				"pageId": 48747501,
				"properties": {
				"imgSrc": "'.$zp4['pic'].'"
				},
				"sceneId": 3079297,
				"type": 3
			},
			{
				"content": "",
				"css": {
				"top": "1px",
				"left": "0px",
				"zIndex": "2",
				"width": 317,
				"height": 215
			},
			"id": 861,
			"num": 1,
			"pageId": 48747501,
			"properties": {
			"width": 317,
			"height": 215,
			"src": "'.$zp4['pic2'].'",
			"imgStyle": {
			"width": 341,
			"height": 215,
			"marginTop": "0px",
			"marginLeft": "-12px"
			},
			"anim": {
			"type": 4,
			"direction": 0,
			"duration": 2,
			"delay": 0,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "27px",
				"left": "78.3377456665039px",
				"zIndex": "3",
				"width": 51,
				"height": 153
			},
			"id": 772,
			"num": 1,
			"pageId": 48747501,
			"properties": {
			"width": 51,
			"height": 153,
			"src": "'.$zp4['pic3'].'",
			"imgStyle": {
			"width": 51,
			"height": 155,
			"marginTop": "-1px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 2,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "26px",
				"left": "117px",
				"zIndex": "4",
				"width": 52,
				"height": 156
			},
			"id": 704,
			"num": 1,
			"pageId": 48747501,
			"properties": {
			"width": 52,
			"height": 156,
			"src": "'.$zp4['pic4'].'",
			"imgStyle": {
			"width": 52,
			"height": 157,
			"marginTop": "-0.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 2,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "27px",
				"left": "154.67105102539063px",
				"zIndex": "5",
				"width": 50,
				"height": 153
			},
			"id": 101,
			"num": 1,
			"pageId": 48747501,
			"properties": {
			"width": 50,
			"height": 153,
			"src": "'.$zp4['pic5'].'",
			"imgStyle": {
			"width": 50,
			"height": 153,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 2,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "28px",
				"left": "197px",
				"zIndex": "6",
				"width": 50,
				"height": 152
			},
			"id": 413,
			"num": 1,
			"pageId": 48747501,
			"properties": {
			"width": 50,
			"height": 152,
			"src": "'.$zp4['pic6'].'",
			"imgStyle": {
			"width": 50,
			"height": 152,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 2,
			"delay": 2,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			}
			],
			"scene": null
		},
		{
			"id": 45706531,
			"sceneId": 3079297,
			"num": 5,
			"name": null,
			"properties": null,
			"elements": [
			{
				"css": {
				"zIndex": "1"
				},
				"id": 701,
				"num": 0,
				"pageId": 45706531,
				"properties": {
				"imgSrc": "'.$zp5['0']['pic'].'"
				},
				"sceneId": 3079297,
				"type": 3
			},
			{
				"content": "",
				"css": {
				"top": "219px",
				"left": "41px",
				"zIndex": "2",
				"width": 132,
				"height": 201
			},
			"id": 484,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 132,
			"height": 201,
			"src": "'.$zp5['pic4'].'",
			"imgStyle": {
			"width": 132,
			"height": 201,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 12,
			"direction": 0,
			"duration": 1,
			"delay": 1,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "18px",
				"left": "27px",
				"zIndex": "3",
				"width": 127,
				"height": 201
			},
			"id": 656,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 127,
			"height": 201,
			"src": "'.$zp5['pic2'].'",
			"imgStyle": {
			"width": 127,
			"height": 201,
			"marginTop": "-0.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 12,
			"direction": 0,
			"duration": 1,
			"delay": 0,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "68px",
				"left": "153px",
				"zIndex": "4",
				"width": 125,
				"height": 201
			},
			"id": 794,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 125,
			"height": 201,
			"src": "'.$zp5['pic3'].'",
			"imgStyle": {
			"width": 125,
			"height": 201,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 12,
			"direction": 0,
			"duration": 1,
			"delay": 0.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "155px",
				"left": "99px",
				"zIndex": "5",
				"width": 50,
				"height": 29
			},
			"id": 114,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 50,
			"height": 29,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 50,
			"height": 37,
			"marginTop": "-4px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 2.7,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "198px",
				"left": "236px",
				"zIndex": "6",
				"width": 50,
				"height": 25
			},
			"id": 289,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 50,
			"height": 25,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 50,
			"height": 35,
			"marginTop": "-5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 2.9,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "209px",
				"left": "217px",
				"zIndex": "7",
				"width": 50,
				"height": 24
			},
			"id": 709,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 50,
			"height": 24,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 50,
			"height": 35,
			"marginTop": "-5.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 3.1,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "271px",
				"left": "173.84375px",
				"zIndex": "8",
				"width": 133,
				"height": 205,
				"backgroundColor": "",
				"opacity": 1,
				"color": "#676767",
				"borderWidth": 0,
				"borderStyle": "solid",
				"borderColor": "rgba(0,0,0,1)",
				"paddingBottom": 0,
				"paddingTop": 0,
				"lineHeight": 1,
				"borderRadius": "0px",
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 0,
				"borderBottomRightRadius": "0px",
				"borderBottomLeftRadius": "0px",
				"borderTopRightRadius": "0px",
				"borderTopLeftRadius": "0px",
				"boxShadow": "0px 0px 0px rgba(0,0,0,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0
			},
			"id": 758,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 133,
			"height": 205,
			"src": "'.$zp5['pic5'].'",
			"imgStyle": {
			"width": 135,
			"height": 205,
			"marginTop": "0px",
			"marginLeft": "-1px"
			},
			"anim": {
			"type": 12,
			"direction": 0,
			"duration": 1,
			"delay": 1.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "410px",
				"left": "229.6666717529297px",
				"zIndex": "9",
				"width": 58,
				"height": 28
			},
			"id": 741,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 58,
			"height": 28,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 58,
			"height": 39,
			"marginTop": "-5.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 4,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "411px",
				"left": "195.75px",
				"zIndex": "10",
				"width": 56,
				"height": 27
			},
			"id": 362,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 56,
			"height": 27,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 56,
			"height": 38,
			"marginTop": "-5.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 3.3,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "399px",
				"left": "222px",
				"zIndex": "11",
				"width": 50,
				"height": 24
			},
			"id": 777,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 50,
			"height": 24,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 50,
			"height": 35,
			"marginTop": "-5.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.3,
			"delay": 3.7,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "344px",
				"left": "117px",
				"zIndex": "12",
				"width": 50,
				"height": 24
			},
			"id": 520,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 50,
			"height": 24,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 50,
			"height": 35,
			"marginTop": "-5.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 3.3,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "361px",
				"left": "100px",
				"zIndex": "13",
				"width": 50,
				"height": 24
			},
			"id": 607,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 50,
			"height": 24,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 50,
			"height": 35,
			"marginTop": "-5.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.3,
			"delay": 3.7,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": 401,
				"left": 244,
				"zIndex": "14",
				"width": 50,
				"height": 30,
				"backgroundColor": "",
				"opacity": 1,
				"color": "#676767",
				"borderWidth": 0,
				"borderStyle": "solid",
				"borderColor": "rgba(0,0,0,1)",
				"paddingBottom": 0,
				"paddingTop": 0,
				"lineHeight": 1,
				"borderRadius": "0px",
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 0,
				"boxShadow": "0px 0px 0px rgba(0,0,0,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0,
				"borderBottomRightRadius": "0px",
				"borderBottomLeftRadius": "0px",
				"borderTopRightRadius": "0px",
				"borderTopLeftRadius": "0px"
				},
				"id": 207,
				"num": 1,
				"pageId": 45706531,
				"properties": {
				"width": 50,
				"height": 30,
				"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
				"imgStyle": {
				"width": 50,
				"height": 35,
				"marginTop": "-2.5px",
				"marginLeft": "0px"
				},
				"anim": {
				"type": 3,
				"direction": 0,
				"duration": 0.1,
				"delay": 3.9,
				"countNum": 1
				}
				},
				"sceneId": 3079297,
				"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "364px",
				"left": "122px",
				"zIndex": "15",
				"width": 50,
				"height": 24
			},
			"id": 566,
			"num": 1,
			"pageId": 45706531,
			"properties": {
			"width": 50,
			"height": 24,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 50,
			"height": 35,
			"marginTop": "-5.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.1,
			"delay": 3.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			}
			],
			"scene": null
		},
		{
			"id": 51153340,
			"sceneId": 3079297,
			"num": 6,
			"name": null,
			"properties": null,
			"elements": [
			{
				"css": {
				"zIndex": "1"
				},
				"id": 847,
				"num": 0,
				"pageId": 51153340,
				"properties": {
				"imgSrc": "'.$zp5['0']['pic'].'"
				},
				"sceneId": 3079297,
				"type": 3
			},
			{
				"content": "",
				"css": {
				"top": "10px",
				"left": "137px",
				"zIndex": "2",
				"width": 140,
				"height": 197
			},
			"id": 452,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 140,
			"height": 197,
			"src": "'.$zp5['0']['pic'].'",
			"imgStyle": {
			"width": 140,
			"height": 197,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 12,
			"direction": 0,
			"duration": 1,
			"delay": 0.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "272px",
				"left": "48px",
				"zIndex": "3",
				"width": 141,
				"height": 198
			},
			"id": 31,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 141,
			"height": 198,
			"src": "'.$zp5['0']['pic'].'",
			"imgStyle": {
			"width": 141,
			"height": 198,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 12,
			"direction": 0,
			"duration": 1,
			"delay": 1,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "62px",
				"left": "20px",
				"zIndex": "4",
				"width": 130,
				"height": 195
			},
			"id": 202,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 130,
			"height": 195,
			"src": "'.$zp5['0']['pic'].'",
			"imgStyle": {
			"width": 130,
			"height": 196,
			"marginTop": "-0.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 12,
			"direction": 0,
			"duration": 1,
			"delay": 0,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "200px",
				"left": "172px",
				"zIndex": "5",
				"width": 141,
				"height": 199
			},
			"id": 445,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 141,
			"height": 199,
			"src": "'.$zp5['0']['pic'].'",
			"imgStyle": {
			"width": 141,
			"height": 199,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 12,
			"direction": 0,
			"duration": 1,
			"delay": 1.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "189px",
				"left": "12px",
				"zIndex": "6",
				"width": 52,
				"height": 37
			},
			"id": 375,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 2.7,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "125px",
				"left": "146px",
				"zIndex": "7",
				"width": 52,
				"height": 37
			},
			"id": 60,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.1,
			"delay": 3.1,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "141px",
				"left": "159px",
				"zIndex": "8",
				"width": 52,
				"height": 37
			},
			"id": 987,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.3,
			"delay": 2.9,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "385px",
				"left": "62px",
				"zIndex": "9",
				"width": 52,
				"height": 37
			},
			"id": 950,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 3.7,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "400px",
				"left": "48px",
				"zIndex": "10",
				"width": 52,
				"height": 37
			},
			"id": 359,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 3.3,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "402px",
				"left": "75px",
				"zIndex": "11",
				"width": 52,
				"height": 37
			},
			"id": 384,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.3,
			"delay": 3.5,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "317px",
				"left": "183px",
				"zIndex": "12",
				"width": 52,
				"height": 37
			},
			"id": 487,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 4.3,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "331px",
				"left": "197px",
				"zIndex": "13",
				"width": 52,
				"height": 37
			},
			"id": 826,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.3,
			"delay": 4.1,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": "320px",
				"left": "216px",
				"zIndex": "14",
				"width": 54,
				"height": 37
			},
			"id": 381,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 54,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 54,
			"height": 38,
			"marginTop": "-0.5px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.3,
			"delay": 4.4,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "",
				"css": {
				"top": 330,
				"left": 210,
				"zIndex": "15",
				"width": 52,
				"height": 37
			},
			"id": 215,
			"num": 1,
			"pageId": 51153340,
			"properties": {
			"width": 52,
			"height": 37,
			"src": "image/yq0KA1UY2YGAaDD6AABgMhb6olo844.png",
			"imgStyle": {
			"width": 52,
			"height": 37,
			"marginTop": "0px",
			"marginLeft": "0px"
			},
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 0.2,
			"delay": 3.9,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 4
			}
			],
			"scene": null
		},
		{
			"id": 45739903,
			"sceneId": 3079297,
			"num": 7,
			"name": "第7页",
			"properties": null,
			"elements": [
			{
				"content": "",
				"css": {
				"top": "384px",
				"left": "26px",
				"zIndex": "1",
				"color": "rgba(0,0,0,1)",
				"borderWidth": 0,
				"borderStyle": "solid",
				"borderColor": "rgba(29,120,204,1)",
				"borderRadius": "5px",
				"backgroundColor": "rgba(30,139,201,1)",
				"opacity": 0.95,
				"paddingBottom": 0,
				"paddingTop": 0,
				"lineHeight": 1,
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 29,
				"boxShadow": "0px 0px 10px rgba(27,84,204,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0,
				"width": 275,
				"height": 37,
				"borderBottomRightRadius": "5px",
				"borderBottomLeftRadius": "5px",
				"borderTopRightRadius": "5px",
				"borderTopLeftRadius": "5px"
				},
				"id": 747,
				"num": 1,
				"pageId": 45739903,
				"properties": {
				"title": "提交11",
				"width": 275,
				"height": 37,
				"anim": {
				"type": 0,
				"direction": 0,
				"duration": 1,
				"delay": 0.5,
				"countNum": 1
				}
				},
				"sceneId": 3079297,
				"type": 6
			},
			{
				"content": "",
				"css": {
				"top": "217px",
				"left": "25px",
				"zIndex": "2",
				"color": "#676767",
				"borderWidth": 0,
				"borderStyle": "solid",
				"borderColor": "#ccc",
				"borderRadius": "5px",
				"backgroundColor": "#f9f9f9",
				"opacity": 1,
				"paddingBottom": 0,
				"paddingTop": 5,
				"lineHeight": 1,
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 26,
				"boxShadow": "0px 0px 10px rgba(0,0,0,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0,
				"width": 277,
				"height": 41,
				"borderBottomRightRadius": "5px",
				"borderBottomLeftRadius": "5px",
				"borderTopRightRadius": "5px",
				"borderTopLeftRadius": "5px"
				},
				"id": 31,
				"num": 1,
				"pageId": 45739903,
				"properties": {
				"placeholder": "姓名",
				"width": 277,
				"height": 41,
				"anim": {
				"type": 0,
				"direction": 0,
				"duration": 1,
				"delay": 0.5,
				"countNum": 1
				}
				},
				"isInput": 1,
				"sceneId": 3079297,
				"title": "姓名",
				"type": "501"
			},
			{
				"content": "",
				"css": {
				"top": "270px",
				"left": "25px",
				"zIndex": "3",
				"color": "#676767",
				"borderWidth": 0,
				"borderStyle": "solid",
				"borderColor": "#ccc",
				"borderRadius": "5px",
				"backgroundColor": "#f9f9f9",
				"opacity": 1,
				"paddingBottom": 0,
				"paddingTop": 5,
				"lineHeight": 1,
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 26,
				"boxShadow": "0px 0px 10px rgba(0,0,0,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0,
				"borderBottomRightRadius": "5px",
				"borderBottomLeftRadius": "5px",
				"borderTopRightRadius": "5px",
				"borderTopLeftRadius": "5px",
				"width": 276,
				"height": 41
			},
			"id": 98,
			"num": 1,
			"pageId": 45739903,
			"properties": {
			"placeholder": "手机",
			"width": 276,
			"height": 41,
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 1,
			"delay": 0.5,
			"countNum": 1
			}
			},
			"isInput": 1,
			"sceneId": 3079297,
			"title": "手机",
			"type": "502"
			},
			{
				"content": "",
				"css": {
				"top": "324px",
				"left": "26px",
				"zIndex": "4",
				"color": "#676767",
				"borderWidth": 1,
				"borderStyle": "solid",
				"borderColor": "#ccc",
				"borderRadius": "5px",
				"backgroundColor": "#f9f9f9",
				"opacity": 1,
				"paddingBottom": 0,
				"paddingTop": 5,
				"lineHeight": 1,
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 26,
				"boxShadow": "0px 0px 10px rgba(0,0,0,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0,
				"borderBottomRightRadius": "5px",
				"borderBottomLeftRadius": "5px",
				"borderTopRightRadius": "5px",
				"borderTopLeftRadius": "5px",
				"width": 274,
				"height": 43
			},
			"id": 304,
			"num": 1,
			"pageId": 45739903,
			"properties": {
			"placeholder": "岗位",
			"required": "required",
			"width": 274,
			"height": 43,
			"anim": {
			"type": 0,
			"direction": 0,
			"duration": 1,
			"delay": 0.5,
			"countNum": 1
			}
			},
			"isInput": 1,
			"sceneId": 3079297,
			"title": "岗位",
			"type": 5
			},
			{
				"css": {
				"zIndex": "5"
				},
				"id": 297,
				"num": 0,
				"pageId": 45739903,
				"properties": {
				"imgSrc": "'.$zp7['pic'].'"
				},
				"sceneId": 3079297,
				"type": 3
			}
			],
			"scene": null
		},
		{
			"id": 45726833,
			"sceneId": 3079297,
			"num": 8,
			"name": "第8页",
			"properties": null,
			"elements": [
			{
				"content": "",
				"css": {
				"top": "109px",
				"left": "88px",
				"zIndex": "1",
				"width": 139,
				"height": 139
			},
			"id": 73,
			"num": 1,
			"pageId": 45726833,
			"properties": {
			"width": 139,
			"height": 139,
			"src": "'.$zp8['pic2'].'",
			"imgStyle": {
			"width": 139,
			"height": 139,
			"marginTop": "0px",
			"marginLeft": "0px"
			}
			},
			"sceneId": 3079297,
			"type": 4
			},
			{
				"content": "<div style=\"text-align: left;\"><span style=\"line-height: inherit; background-color: rgba(151, 218, 243, 0.298039);\"><font color=\"#000000\" size=\"3\"><b></b></font></span></div>",
				"css": {
				"top": "245px",
				"left": "49px",
				"zIndex": "2",
				"width": 231,
				"height": 41,
				"backgroundColor": "",
				"opacity": 1,
				"color": "#676767",
				"borderWidth": 0,
				"borderStyle": "solid",
				"borderColor": "rgba(0,0,0,1)",
				"paddingBottom": 0,
				"paddingTop": 0,
				"lineHeight": 1,
				"borderRadius": "0px",
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 0,
				"boxShadow": "0px 0px 0px rgba(0,0,0,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0,
				"borderBottomRightRadius": "0px",
				"borderBottomLeftRadius": "0px",
				"borderTopRightRadius": "0px",
				"borderTopLeftRadius": "0px"
				},
				"id": 900,
				"num": 1,
				"pageId": 45726833,
				"properties": {
				"width": 231,
				"height": 41,
				"anim": {
				"type": 13,
				"direction": 0,
				"duration": 1.5,
				"delay": 0.5,
				"countNum": 1
				}
				},
				"sceneId": 3079297,
				"type": 2
			},
			{
				"content": "<font color=\"#000000\" size=\"3\"><b style=\"background-color: rgba(151, 218, 243, 0.298039);\"></b></font>",
				"css": {
				"top": "269px",
				"left": "58px",
				"zIndex": "3",
				"backgroundColor": "",
				"opacity": 1,
				"color": "#676767",
				"borderWidth": 0,
				"borderStyle": "solid",
				"borderColor": "rgba(0,0,0,1)",
				"paddingBottom": 0,
				"paddingTop": 0,
				"lineHeight": 1,
				"borderRadius": "0px",
				"transform": "rotateZ(0deg)",
				"borderRadiusPerc": 0,
				"boxShadow": "0px 0px 0px rgba(0,0,0,0.5)",
				"boxShadowDirection": 0,
				"boxShadowSize": 0,
				"width": 209,
				"height": 36,
				"borderBottomRightRadius": "0px",
				"borderBottomLeftRadius": "0px",
				"borderTopRightRadius": "0px",
				"borderTopLeftRadius": "0px"
				},
				"id": 446,
				"num": 1,
				"pageId": 45726833,
				"properties": {
				"width": 209,
				"height": 36,
				"anim": {
				"type": 13,
				"direction": 0,
				"duration": 1.5,
				"delay": 2,
				"countNum": 1
				}
				},
				"sceneId": 3079297,
				"type": 2
			},
			{
				"content": "<div style=\"text-align: center;\"><font color=\"#000000\" size=\"4\"><b style=\"background-color: rgba(151, 218, 243, 0.298039);\"></b></font></div><div style=\"text-align: center;\"><font color=\"#000000\" size=\"4\"><b style=\"background-color: rgba(151, 218, 243, 0.298039);\"></b></font><br></div>",
				"css": {
				"top": "49px",
				"left": "9px",
				"zIndex": "4",
				"width": 301,
				"height": 59
			},
			"id": 809,
			"num": 1,
			"pageId": 45726833,
			"properties": {
			"width": 301,
			"height": 59,
			"anim": {
			"type": 3,
			"direction": 0,
			"duration": 1.5,
			"delay": 0,
			"countNum": 1
			}
			},
			"sceneId": 3079297,
			"type": 2
			},
			{
				"css": {
				"zIndex": "5"
				},
				"id": 104,
				"num": 0,
				"pageId": 45726833,
				"properties": {
				"imgSrc": "'.$zp8['pic'].'"
				},
				"sceneId": 3079297,
				"type": 3
			}
			],
			"scene": null
		}
		]
		}';
	}






















}
?>
