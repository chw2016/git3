<?php
/*
    WAP端
    减肥达人前台页面
    @author 老张
*/

class ReduceAction extends BaseAction {
    public $openid;
    public $token;
    //显示首页
    protected function _initialize(){
        header("content-Type: text/html; charset=Utf-8");
        parent::_initialize();

        if ((!session('?token')) || (!session('?openid'))) {
            session('token', $_REQUEST['token']);
            session('openid', $_REQUEST['openid']);
        }

        $this->token = $_REQUEST['token'];
        $this->openid = $_REQUEST['openid'];
        //$this->openid = "oqem0ju8x0YBHpDV_Bnvop8lk2is";
        $this->assign('token',$this->token);
        $this->assign('openid',$this->openid);
    }
    //问题收集显示页面
    public function analysis(){
        // 判断用户第一次进入页面，是第一次显示页面，不是第一次显示后面的
        if(M('Reduce_fatanalysis')->where(array('openid'=>$this->openid,'token'=>$this->token))->find()){
            $this->redirect('Reduce/result', array('token' => $this->token,'openid'=>$this->openid));
        }else{
            if (condition) {
                # code...
            }
            $data = array(
                'token' => $this->token,
                'openid' => $this->openid,
                'haslose' => 0,
                'persist' => 0,
                'birth' => ''
            );
            if (M('Reduce_lose')->where(array('token'=>$this->token,'openid'=>$this->openid))->find()) {

            }else{
                M('Reduce_lose')->add($data);
            }


            $this->display();
        }

    }
    // 问题页面接收数据
    public function receiveAna(){
        $question = $this->_post('question');
        if(substr($question, -3, 1) == ","){
            $question = preg_replace("/,}]/","}]",$question);
        };

        $data = array(
            "token" => $this->_get('token'),
            "question" => $question,
            "openid" => $this->openid,
	    "date"=>date("Y-m-d",time())
        );

        if(M('Reduce_fatanalysis')->add($data)){
            $this->success("保存成功",U(MODULE_NAME.'/result',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            $this->error("保存失败",U(MODULE_NAME.'/analysis'));
        }


    }
    // 从后台接受一些数据来分配到前台
    public function result(){
        $db = M('Reduce_fatanalysis');
        $dbs = M('Reduce_result');
        $res = $dbs->select();
        $result = $db->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        $question = str_replace("&amp;quot;", "\"", $result['question']);

        // 解码，二维数组
        // 空数组，用来收集内容

        if(strstr($question, "15") && strstr($question, "20") && strstr($question, "26") && strstr($question, "29")){
            $this->assign('flags',1);
        }else{
            $content = array();
            // 二维数组
            $arrays = array();
            $array = json_decode($question,true);
            foreach ($array[0] as $key => $value) {
                foreach($res as $keys => $val){
                    if(strstr($val['index'], $value) == true){
                        $results = $dbs->where(array('index'=>$val['index']))->select();
                        foreach ($results as $yao => $zhi) {
                            $content['title'] = $zhi['title'];
                            $content['content'] = $zhi['content'];
                            $content['img_path'] = $zhi['img_path'];
                            array_push($arrays, $content);
                        }

                    }
                }
            }

            $i = 0;
            foreach ($arrays as $key => $value) {
                $newValue[$i] = $value['title'];
                $newValue[++$i] = $value['content'];
                $newValue[++$i] = $value['img_path'];
                $i++;
            }
            $newValue = array_unique($newValue);
            $j = 0;
            foreach ($newValue as $key => $value) {

                if ($key % 3 == 0) {
                    $contents[$j]['title'] = $value;
                }elseif ($key % 3 == 2) {
                    $contents[$j]['img_path'] = $value;
                    $j++;
                }else{
                    $contents[$j]['content'] = $value;
                }
            };

            $this->assign('arrays',$contents);
            /* echo "<pre>";
             print_r($newValue);
             print_r($contents);exit();*/
        }
        // exit();
        $this->display();
    }
    // 分胖分析重新测试结果
    public function repeatTest(){
        if (M('Reduce_fatanalysis')->where(array('token'=>$this->token,'openid'=>$this->openid))->delete()) {
            $this->success("保存成功",U(MODULE_NAME.'/analysis',array('token'=>$this->token,'openid'=>$this->openid)));
        }
    }
    // 显示我知道了页面
    public function iknow(){
        $db = M('Reduce_fatanalysis');
        $dbs = M('Reduce_result');
        $res = $dbs->select();
        $result = $db->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        $question = str_replace("&amp;quot;", "\"", $result['question']);

        // 解码，二维数组
        // 空数组，用来收集内容

        if(strstr($question, "15") && strstr($question, "20") && strstr($question, "26") && strstr($question, "29")){
            $this->assign('flags',1);
        }else{
            $content = array();
            // 二维数组
            $arrays = array();
            $array = json_decode($question,true);
            foreach ($array[0] as $key => $value) {
                foreach($res as $keys => $val){
                    if(strstr($val['index'], $value) == true){
                        $results = $dbs->where(array('index'=>$val['index']))->select();
                        foreach ($results as $yao => $zhi) {
                            $content['no_icon'] = $zhi['no_icon'];
                            $content['no_text'] = $zhi['no_text'];

                            array_push($arrays, $content);
                        }

                    }
                }
            }

            $i = 0;
            foreach ($arrays as $key => $value) {
                $newValue[$i] = $value['no_icon'];
                $newValue[++$i] = $value['no_text'];

                $i++;
            }
            // print_r($newValue);
            $newValue = array_unique($newValue);
            $j = 0;
            foreach ($newValue as $key => $value) {

                if ($key % 2 == 0) {
                    $contents[$j]['no_icon'] = $value;
                }else{
                    $contents[$j]['no_text'] = $value;
                    $j++;
                }
            };

            $this->assign('arrays',$contents);
            // print_r($contents);exit();
            // echo "<pre>";

            // print_r($newValue);exit();
        }
        $this->display();
    }
    // 方案定制，显示页面
    public function scheme(){
        if (M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find()) {
            $this->redirect('Reduce/testResult', array('token' => $this->token,'openid'=>$this->openid));
        }else{
            $this->display();
        }

    }
    // 显示页面接收数据
    public function receiveSch(){
        $db = M('Reduce_custom');//新建的表
        // 年龄的判断
        $age = intval(str_replace("周岁", "", $this->_post('age')));
        $weights = (intval(str_replace("cm", "", $this->_post('height')))*intval(str_replace("cm", "", $this->_post('height'))))/10000;
        // echo $weights;exit();
        $BMI = number_format(intval(str_replace("kg", "", $this->_post('weight')))/$weights,1);
        /*if ($this->_post('sex')) {
            $getlow = 10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) + 5*intval(str_replace("周岁", "", $this->_post('age'))) + 5;
        }else{
            $getlow = 10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) + 5*intval(str_replace("周岁", "", $this->_post('age'))) - 161;
        }*/


        if ($age >= 18) {
            if ($this->_post('sex')) {
                $BMR = intval(13.88*intval(str_replace("kg", "", $this->_post('weight')))+4.16*intval(str_replace("cm", "", $this->_post('height')))-3.43*$age-0+54.34);
            }else{
                $BMR = intval(13.88*intval(str_replace("kg", "", $this->_post('weight')))+4.16*intval(str_replace("cm", "", $this->_post('height')))-3.43*$age-112.40+54.34);
            }

        };
        if ($age < 18 && $age >=7) {
            if ($this->_post('sex')) {
                $BMR = intval(intval(50.2*intval(str_replace("kg", "", $this->_post('weight')))+29.6*intval(str_replace("cm", "", $this->_post('height')))-144.5*$age-0+594.3)/4);
            }else{
                $BMR = intval(intval(50.2*intval(str_replace("kg", "", $this->_post('weight')))+29.6*intval(str_replace("cm", "", $this->_post('height')))-144.5*$age-550+594.3)/4);
            }

        }
        if ($BMI >= 29.9) {
            $health = "肥胖";
        }elseif($BMI >= 23.9 && $BMI < 29.9){
            $health = "偏胖";
        }elseif($BMI >= 18.5 && $BMI < 23.9){
            $health = "正常";
        }else{
            $health = "偏瘦";
        };
        if ($this->_post('run') == "无") {
            if ($this->_post('sex') == "1") {
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))+5)*1.200);
            }else{
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))-161)*1.200);
            }
            $move = 0;
        }elseif($this->_post('run') == "轻微"){
            if ($this->_post('sex') == "1") {
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))+5)*1.375);
            }else{
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))-161)*1.375);
            }
            $move = 590;
        }elseif($this->_post('run') == "中度"){
            if ($this->_post('sex') == "1") {
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))+5)*1.550);
            }else{
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))-161)*1.550);
            }
            $move = 870;
        }elseif($this->_post('run') == "较多"){
            if ($this->_post('sex') == "1") {
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))+5)*1.725);
            }else{
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))-161)*1.725);
            }
            $move = 1150;
        }else{
            if ($this->_post('sex') == "1") {
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))+5)*1.900);
            }else{
                $getlow = intval((10*intval(str_replace("kg", "", $this->_post('weight'))) + 6.25*intval(str_replace("cm", "", $this->_post('height'))) - 5*intval(str_replace("周岁", "", $this->_post('age')))-161)*1.900);
            }
            $move = 1580;
        }
        if (IS_POST) {
            $city = "深圳";
            $data = array(
                'age' => $this->_post('age'),
                'height' => $this->_post('height'),
                'weight' => $this->_post('weight'),
                'run' => $this->_post('run'),
                'token' => $this->token,
                'openid' => $this->openid,
                'sex' => $this->_post('sex'),
                'explose' =>'',
                'exptime' =>'',
                'health' => $health,
                'BMI' => $BMI,
                'getlow' => $getlow,
                'BMR' => $BMR,
                'city' => $city,
                'move' => $move,
                'join_date' => date("Y-m-d")
            );
            if ($db->add($data)) {
                $this->success("提交成功",U(MODULE_NAME.'/aim',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("提交失败",U(MODULE_NAME.'/scheme'));
            }
        }
    }
    // 显示减肥目标页面
    public function aim(){
        if (M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find()) {
            $result = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
            if ($result['sex'] == 0) {
                $shengao = "";
                for ($j=0; $j < strlen($result['height']); $j++) {
                    $sg = substr($result['height'], $j ,1);
                    if (is_numeric($sg)) {
                        $shengao .= $sg;
                    }
                };
                // 女性标准体重
                $Standard = number_format(($shengao-70)*0.6,1);
                // 体重范围
                $before = number_format($Standard*0.9,1);
                $after = number_format($Standard*1.1,1);

                $this->assign('before',$before);
                $this->assign('after',$after);
            }else{
                $shengao = "";
                for ($j=0; $j < strlen($result['height']); $j++) {
                    $sg = substr($result['height'], $j ,1);
                    if (is_numeric($sg)) {
                        $shengao .= $sg;
                    }
                };
                // 男性标准体重
                $Standard = number_format(($shengao-80)*0.7,1);
                $before = number_format($Standard*0.9,1);
                $after = number_format($Standard*1.1,1);

                $this->assign('before',$before);
                $this->assign('after',$after);
            }
            $weight = $result['weight'];
            $str = "";
            for ($i=0; $i < strlen($weight); $i++) {
                $sr = substr($weight, $i ,1);
                if (is_numeric($sr)) {
                    $str .= $sr;
                }
            }
            $this->assign('weight',$str);
            $this->display();
        }
        else{

            // 暂缓三秒执行
            sleep(3);
            $this->redirect('Reduce/scheme', array('token' => $this->token,'openid'=>$this->openid));
        }
    }
    // 显示减肥目标页面的提交数据
    public function aimData(){
        if (IS_POST) {
            $move = intval($this->_post('explose') * 7700 / ($this->_post('exptime') * 30));
            $data = array(
                'explose' => $this->_post('explose'),
                'exptime' => $this->_post('exptime'),
                'move' => $move
            );

        }
        // 向Reduce_plan里面插入数据
        if (M('Reduce_plan')->where(array('token'=>$this->token,'openid'=>$this->openid))->find()) {

        }else{

            $custom = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
            // 运动类型
            $runTy = "[{\"1\":\"行走\",\"2\":\"户外自行车\",\"3\":\"跑步\",\"4\":\"游泳\",\"5\":\"健身羽毛球\",\"6\":\"踏步仪\"}]";
            //消耗卡路里
            $weight = str_replace("kg", "", $custom['weight']);
            if ($weight % 10 >= 5 ) {
                $falseWeight = intval($weight / 10) * 10 + 5;
            }else{
                $falseWeight = intval($weight / 10) * 10;
            }
            $falseWeight = intval($weight);
            // 行走
            $Caloriewalk = number_format($falseWeight * 2.1,1);
            // 户外自行车
            $Caloriecircle = number_format($falseWeight * 5.7,1);
            // 跑步
            $Calorierun = number_format($falseWeight * 9.45,1);
            // 游泳
            $Calorieswim = number_format($falseWeight * 7.35,1);
            // 羽毛球
            $Calorieball = number_format($falseWeight * 4.515,1);
            // 踏步一
            $Caloriepacer = number_format($falseWeight * 9.45,1);
            // 消耗卡路里的量
            $calorie = "[{\"1\":\"".$Caloriewalk."\",\"2\":\""."$Caloriecircle.\",\"3\":\"".$Calorierun."\",\"4\":\"".$Calorieswim."\",\"5\":\"".$Calorieball."\",\"6\":\"".$Caloriepacer."\"}]";
            // 所需要的时间
            $needtime = "[{\"1\":\"60\",\"2\":\"60\",\"3\":\"60\",\"4\":\"60\",\"5\":\"60\",\"6\":\"60\"}]";
            // 图片的位置调用
            $imgPath = "[{\"1\":\"/upload/xingzou.jpg\",\"2\":\"/upload/zixingche.jpg\",\"3\":\"/upload/paobu.jpg\",\"4\":\"/upload/youyong.jpg\",\"5\":\"/upload/yumaoqiu.jpg\",\"6\":\"/upload/tabuyi.jpg\"}]";
            // 内容说明
            $content = "[{\"1\":\"平地小于3.2km/h,运动时间至少30min左右\",\"2\":\"16.1-19.2公里每小时/阻力训练,大强度\",\"3\":\"8.4公里每小时,跑步长度在1500左右\",\"4\":\"每分钟50m左右,运动时间控制在30-40分钟左右\",\"5\":\"建议在室内进行活动,活动时间约为30-45分钟\",\"6\":\"室内活动,运动时间控制在60-70分钟\"}]";

            // 用户id
            $uid = $custom['id'];
            $datas = array(
                'run_type' => $runTy,
                'kcal' => $calorie,
                'minute' => $needtime,
                'imgs' => $imgPath,
                'contents' => $content,
                'uid' => $uid,
                'openid' => $this->openid,
                'token' => $this->token
            );
            M('Reduce_plan')->data($datas)->add();
        }

        if (M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->save($data)) {
            $this->success("提交成功",U(MODULE_NAME.'/testResult',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            $this->error("您已提交过了哦",U(MODULE_NAME.'/aim'));
        }
    }
    // 测试结果显示页面
    public function testResult(){
        $result = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        if (!$result['explose'] || !$result['exptime']) {

            // 暂缓三秒执行
            sleep(3);
            $this->redirect('Reduce/aim', array('token' => $this->token,'openid'=>$this->openid));
        }
        else{
            $db = M('Reduce_fatanalysis');
            $dbs = M('Reduce_result');

            $res = $dbs->select();
            $jieguo = $db->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
            $question = str_replace("&amp;quot;", "\"", $jieguo['question']);

            // 解码，二维数组
            // 空数组，用来收集内容

            if(strstr($question, "15") || strstr($question, "20") || strstr($question, "26") || strstr($question, "29")){
                $this->assign('flags',1);
            }else{
                $content = array();
                // 二维数组
                $arrays = array();
                $array = json_decode($question,true);
                foreach ($array[0] as $key => $value) {
                    foreach($res as $keys => $val){
                        if(strstr($val['index'], $value) == true){
                            $results = $dbs->where(array('index'=>$val['index']))->select();
                            foreach ($results as $yao => $zhi) {
                                $content['no_icon'] = $zhi['no_icon'];
                                $content['no_text'] = $zhi['no_text'];

                                array_push($arrays, $content);
                            }

                        }
                    }
                }

                $i = 0;
                foreach ($arrays as $key => $value) {
                    $newValue[$i] = $value['no_icon'];
                    $newValue[++$i] = $value['no_text'];

                    $i++;
                }
                // print_r($newValue);
                $newValue = array_unique($newValue);
                $j = 0;
                foreach ($newValue as $key => $value) {

                    if ($key % 2 == 0) {
                        $contents[$j]['no_icon'] = $value;
                    }else{
                        $contents[$j]['no_text'] = $value;
                        $j++;
                    }
                };

                $this->assign('arrays',$contents);
                // echo "<pre>";

                // print_r($newValue);exit();
            }
            /* if (M('Reduce_plan')->where(array('token'=>$this->token,'openid'=>$this->openid))->find()) {

             }else{
                $custom = M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid)->find();
                $data = array(
                     'uid' => $custom['id']
                 );
                 M('Reduce_plan')->data($data)->add();
             }*/
            $aimlose = intval(str_replace("kg", "", $result['weight']))-intval($result['explose']);
            $data = array(
                'aimweight' => $aimlose
            );
            M('Reduce_lose')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);
            $BMI = number_format($result['BMI']*120/18.5,3);
            $height2 = number_format(str_replace("cm", "", $result['height'])/100,2) * number_format(str_replace("cm", "", $result['height'])/100,2);
            $aims = number_format($aimlose/$height2,1);
            $aimBMI = number_format($aims*120/18.5,3);

            $everyNeed = $result['getlow'] + $result['move'] + $result['BMR'];
            $this->assign('everyNeed',$everyNeed);
            $this->assign('BMI',$BMI);
            $this->assign('aimBMI',$aimBMI);
            $this->assign('result',$result);
            $this->assign('aimlose',$aimlose);
            $this->display();
        }
    }
    // 重新定制
    public function repeatDingZhi(){
        if(M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->delete()){
            M('Reduce_plan')->where(array('openid'=>$this->openid,'token'=>$this->token))->delete();
            $this->success("保存成功",U(MODULE_NAME.'/scheme',array('token'=>$this->token,'openid'=>$this->openid)));
        }
    }
    // 饮食方案
    public function dietplan(){
        $db = M('Reduce_garnish');
        // 1200是统计的数据]
        $total = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
            $getlow = intval($total['getlow'] / 100)*100;
           if (!$total) {
                 $this->assign('Number',1);
            }
	    
            //echo $getlow;exit();
        $result = $db->where(array('total_kcal'=>$getlow))->find();
        // print_r($result);exit();
        $breakfast = str_replace("&amp;quot;", "\"", $result['breakfast']);
        $data = array(
            'garnish_id' => $result['id']
        );
        M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);
        $lunch = str_replace("&amp;quot;", "\"", $result['lunch']);
        $extra = str_replace("&amp;quot;", "\"", $result['extra']);
        $dinner = str_replace("&amp;quot;", "\"", $result['dinner']);
        $breakfast = json_decode($breakfast,true);
        $lunch = json_decode($lunch,true);
        $extra = json_decode($extra,true);
        $dinner = json_decode($dinner,true);
        $i = 0;
        // 早餐
        // 统计克数
        $b = 0;
        foreach ($breakfast[0] as $key => $value) {
            $breaks[$i]['foodname'] = $key;
            $breaks[$i]['weight'] = $value;
            $b += $value;
            $i++;
        }
        // 中餐
        $i = 0;
        // 统计克数
        $l = 0;
        foreach ($lunch[0] as $key => $value) {
            $lunchs[$i]['foodname'] = $key;
            $lunchs[$i]['weight'] = $value;
            $l += $value;
            $i++;
        }
        // 加餐
        $i = 0;
        // 统计克数
        $e = 0;
        foreach ($extra[0] as $key => $value) {
            $extras[$i]['foodname'] = $key;
            $extras[$i]['weight'] = $value;
            $e += $value;
            $i++;
        }
        // 晚餐
        $i = 0;
        // 统计克数
        $d = 0;
        foreach ($dinner[0] as $key => $value) {
            $dinners[$i]['foodname'] = $key;
            $dinners[$i]['weight'] = $value;
            $d += $value;
            $i++;
        }
        $all = $b + $l + $e + $d;
        // 分配早餐
        $this->assign('breaks',$breaks);
        $this->assign('lunchs',$lunchs);
        $this->assign('extras',$extras);
        $this->assign('dinners',$dinners);
        $this->assign('result',$result);

        // 分配克数
        $this->assign('b',$b);
        $this->assign('l',$l);
        $this->assign('e',$e);
        $this->assign('d',$d);
        $this->assign('all',$all);
        $this->display();
    }

    //饮食换一组请求的随机数据
    public function randomDrink(){
        $type = $_POST['type'];
        $db = M('Reduce_garnish');
        $total = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        $getlow = intval($total['getlow'] / 100)*100;
        $count = $db->where(array('total_kcal'=> $getlow))->count();
        $random = $db->where(array('total_kcal'=> $getlow,'num'=>mt_rand(1,$count)))->find();
        if ($type == 1) {
            // 换一组，全部

            // 上述随机数据
            $breakfast = str_replace("&amp;quot;", "\"", $random['breakfast']);
            $lunch = str_replace("&amp;quot;", "\"", $random['lunch']);
            $extra = str_replace("&amp;quot;", "\"", $random['extra']);
            $dinner = str_replace("&amp;quot;", "\"", $random['dinner']);
            $breakfast = json_decode($breakfast,true);
            $lunch = json_decode($lunch,true);
            $extra = json_decode($extra,true);
            $dinner = json_decode($dinner,true);

            $data = array(
                'garnish_id' => $random['id']
            );
            M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data);

            $i = 0;
            // 早餐
            // 统计克数
            $b = 0;
            // 早餐json
            $bstr = "";
            foreach ($breakfast[0] as $key => $value) {
                $bstr = $bstr."\"".$i."\":\"".$key.",".$value.",".$random['total_break']."\",";
                $i++;
            }
            $bstr = "{".$bstr."}";
            $bstr = preg_replace("/,}/", "}", $bstr);
            // echo $bstr;exit();
            // 中餐
            $i = 0;
            // 统计克数
            $l = 0;
            // 午餐json
            $lstr = "";
            foreach ($lunch[0] as $key => $value) {
                $lstr = $lstr."\"".$i."\":\"".$key.",".$value.",".$random['total_lunch']."\",";
                $i++;
            }
            $lstr = "{".$lstr."}";
            $lstr = preg_replace("/,}/", "}", $lstr);
            // 加餐
            $i = 0;
            // 统计克数
            $e = 0;
            // 加餐json
            $estr = "";
            foreach ($extra[0] as $key => $value) {
                $estr = $estr."\"".$i."\":\"".$key.",".$value.",".$random['total_extra']."\",";
                $i++;
            }
            $estr = "{".$estr."}";
            $estr = preg_replace("/,}/", "}", $estr);
            // 晚餐
            $i = 0;
            // 统计克数
            $d = 0;
            // 加餐json
            $dstr = "";
            foreach ($dinner[0] as $key => $value) {
                $dstr = $dstr."\"".$i."\":\"".$key.",".$value.",".$random['total_dinner']."\",";
                $i++;
            }
            $dstr = "{".$dstr."}";
            $dstr = preg_replace("/,}/", "}", $dstr);
            // echo $dstr;exit();
            $total_json = "{\"0\":".$bstr.",\"1\":".$lstr.",\"2\":".$estr.",\"3\":".$dstr."}";
            echo $total_json;
        };
        /*单个*/
        /*单个*/
        /*单个*/
        if ($type == 2) {
            //换一组 单个


            $id = $this->_post('id');
            $lid = $this->_post('lid');
            $eid = $this->_post('eid');
            $did = $this->_post('did');
            // 早餐
            if ($id == "cereal") {
                $breakfast = str_replace("&amp;quot;", "\"", $random['breakfast']);
                $breakfast = json_decode($breakfast,true);
                $i = 0;
                $str = "";
                // $breaks = array();
                foreach ($breakfast[0] as $key => $value) {
                    $str .= "\"".$i."\":\"".$key.",".$value."\",";
                    $i++;
                }
                $str = $str."}";
                $str = preg_replace('/,}/', '}', $str);
                $str = "{".$str;
                // $str .= "}";
                // echo $breakfasts;exit();
                // $str .= "{";
                /* $str =$str."\"first\":\"".$breaks[mt_rand(0,--$i)]."\",";
                 $str =$str."\"second\":\"".$breaks[mt_rand(0,--$i)]."\"";*/

                echo $str;
            }
            // 午餐
            if ($lid == "lunch") {
                $lunch = str_replace("&amp;quot;", "\"", $random['lunch']);
                $lunch = json_decode($lunch,true);
                $i = 0;
                // $lunchs = array();
                $str = "";
                foreach ($lunch[0] as $key => $value) {
                    $str .= "\"".$i."\":\"".$key.",".$value."\",";
                    $i++;
                }
                $str = $str."}";
                $str = preg_replace('/,}/', '}', $str);
                $str = "{".$str;
                // $str .= "}";
                /* $str = "{";
                 $str =$str."\"first\":\"".$lunchs[mt_rand(0,--$i)]."\",";
                 $str =$str."\"second\":\"".$lunchs[mt_rand(0,--$i)]."\"";
                 $str .= "}";*/
                echo $str;
            }
            // 加餐
            if ($eid == "extra") {
                $extra = str_replace("&amp;quot;", "\"", $random['extra']);
                $extra = json_decode($extra,true);
                $i = 0;
                // $extras = array();
                $str = "";
                foreach ($extra[0] as $key => $value) {
                    $str .= "\"".$i."\":\"".$key.",".$value."\",";
                    $i++;
                }
                $str = $str."}";
                $str = preg_replace('/,}/', '}', $str);
                $str = "{".$str;
                // $str .= "}";
                /* $str = "{";
                 $str =$str."\"first\":\"".$extras[mt_rand(0,--$i)]."\",";
                 $str =$str."\"second\":\"".$extras[mt_rand(0,--$i)]."\"";
                 $str .= "}";*/
                echo $str;
            }
            // 晚餐
            if ($did == "dinner") {
                $dinner = str_replace("&amp;quot;", "\"", $random['dinner']);
                $dinner = json_decode($dinner,true);
                $i = 0;
                // $dinners = array();
                $str = "";
                foreach ($dinner[0] as $key => $value) {
                    $str .= "\"".$i."\":\"".$key.",".$value."\",";
                    $i++;
                }
                $str = $str."}";
                $str = preg_replace('/,}/', '}', $str);
                $str = "{".$str;
                // $str .= "}";
                /* $str = "{";
                 $str =$str."\"first\":\"".$dinners[mt_rand(0,--$i)]."\",";
                 $str =$str."\"second\":\"".$dinners[mt_rand(0,--$i)]."\"";
                 $str .= "}";*/
                echo $str;
            }
        }
    }

    // 运动方案
    public function runplan(){
        //从数据库里面取得运动方案的数据
        $result = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        $results = M('Reduce_plan')->where(array('uid'=>$result['id']))->find();
            if (!$result) {
               $this->assign('Number',1);
            }
            //首先显示第一条的数据
        $runType = str_replace("&amp;quot;", "\"", $results['run_type']);
        $km = str_replace("&amp;quot;", "\"", $results['km']);
        $kcal = str_replace("&amp;quot;", "\"", $results['kcal']);
        $minute = str_replace("&amp;quot;", "\"", $results['minute']);
        $grams = str_replace("&amp;quot;", "\"", $results['grams']);
        $imgs = str_replace("&amp;quot;", "\"", $results['imgs']);
        $contents = str_replace("&amp;quot;", "\"", $results['contents']);
        // 解码
        $runType = json_decode($runType,true);
        $km = json_decode($km,true);
        $kcal = json_decode($kcal,true);
        $minute = json_decode($minute,true);
        $grams = json_decode($grams,true);
        $imgs = json_decode($imgs,true);
        $contents = json_decode($contents,true);
        // 各取第一条数据
        $i = 0;
        foreach ($runType[0] as $key => $value) {
            $runTypes[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($km[0] as $key => $value) {
            $kms[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($kcal[0] as $key => $value) {
            $kcals[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($minute[0] as $key => $value) {
            $minutes[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($grams[0] as $key => $value) {
            $gramsd[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($imgs[0] as $key => $value) {
            $imgsd[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($contents[0] as $key => $value) {
            $contentsd[$i] = $value;
            $i++;
        }
        // 切割内容
        $arrContent = explode(",", $contentsd[0]);
        $arrlength = count($arrContent);
        $arrlength += 1;
        // 新建数据接收数据
        $newArray = array(
            'runtype' => $runTypes[0],
            'km' => $kms[0],
            'kcal' => $kcals[0],
            'minute' => $minutes[0],
            'grams' => $gramsd[0],
            'img' => $imgsd[0],
            'content' => $arrContent
        );
        // echo $arrlength;exit();
        $this->assign('datas',$newArray);
        $this->assign('length',$arrlength);
        // print_r($newArray);exit();
        $this->display();
    }
    // 随机切换运动
    public function randomRun(){
        //从数据库里面取得运动方案的数据
        $result = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        $results = M('Reduce_plan')->where(array('uid'=>$result['id']))->find();
        //首先显示第一条的数据
        $runType = str_replace("&amp;quot;", "\"", $results['run_type']);
        $km = str_replace("&amp;quot;", "\"", $results['km']);
        $kcal = str_replace("&amp;quot;", "\"", $results['kcal']);
        $minute = str_replace("&amp;quot;", "\"", $results['minute']);
        $grams = str_replace("&amp;quot;", "\"", $results['grams']);
        $imgs = str_replace("&amp;quot;", "\"", $results['imgs']);
        $contents = str_replace("&amp;quot;", "\"", $results['contents']);
        // 解码
        $runType = json_decode($runType,true);
        $km = json_decode($km,true);
        $kcal = json_decode($kcal,true);
        $minute = json_decode($minute,true);
        $grams = json_decode($grams,true);
        $imgs = json_decode($imgs,true);
        $contents = json_decode($contents,true);
        // 各取第一条数据
        $i = 0;
        foreach ($runType[0] as $key => $value) {
            $runTypes[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($km[0] as $key => $value) {
            $kms[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($kcal[0] as $key => $value) {
            $kcals[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($minute[0] as $key => $value) {
            $minutes[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($grams[0] as $key => $value) {
            $gramsd[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($imgs[0] as $key => $value) {
            $imgsd[$i] = $value;
            $i++;
        }
        $i = 0;
        foreach ($contents[0] as $key => $value) {
            $contentsd[$i] = $value;
            $i++;
        }
        $rand = mt_rand(0,--$i);
        $arrContent = explode(",", $contentsd[$rand]);
        $arrlength = count($arrContent);
        $arrlength += 1;
        // 新建数据接收数据
        $newArray = array(
            'runtype' => $runTypes[$rand],
            'km' => $kms[$rand],
            'kcal' => $kcals[$rand],
            'minute' => $minutes[$rand],
            'grams' => $gramsd[$rand],
            'img' => $imgsd[$rand],
            'content' => $arrContent
        );

        for ($j=0; $j < count($arrContent); $j++) {
            $str .= "\"".$j."\":\"".$arrContent[$j]."\",";
        }
        $str = "{".$str."}";
        $str = preg_replace("/,}/", "}", $str);

        $newArray = "{\"runtype\":\"".$runTypes[$rand]."\",\"km\":\"".$kms[$rand].
            "\",\"kcal\":\"".$kcals[$rand]."\",\"minute\":\"".$minutes[$rand].
            "\",\"grams\":\"".$gramsd[$rand]."\",\"img\":\"".$imgsd[$rand]."\",\"content\":"
            .$str."}";

        echo $newArray;
    }
    // 食物查询一级分类
    public function findfood(){
        if (!isset($_POST['hide']) || empty($_POST['findFood'])) {
            $result = M('Reduce_food_class')->select();
            $this->assign('result',$result);
            // echo "1";
            $this->display();
        }else{
            $findFood = $_POST['findFood'];//查询条件
            $hide = $_POST['hide'];//判断条件
            $foodName['food_name'] = array('like','%'.$findFood.'%');
            $result = M('Reduce_food_component_detail')->where($foodName)->select();


            foreach ($result as $key => $value) {
                if (preg_match("/[(|（].*".$findFood."/",$value['food_name'])) {
                    unset($result[$key]);
                }
            }

            $this->assign('result',$result);

            $this->assign('hides',$hide);
            $this->assign('findfood',$findFood);
            $this->display();
        }

    }
    // 食物查询二级分类
    public function findfood2(){
        $id = $_GET['id'];
        $title = M('Reduce_food_class')->where(array('id'=>$id))->find();
        if (!isset($_POST['hide']) || empty($_POST['findFoods'])) {

            $result = M('Reduce_food_component_detail')->where(array('class_id'=>$id))->select();
            $this->assign('result',$result);
            $this->assign('tid',$title['id']);

        }else{
            $hide = $_POST['hide'];//判断条件
            $findFood = $_POST['findFoods'];//查询条件
            $id = $_GET['id'];
            $name = $_GET['name'];
            $find['food_name'] = array('like','%'.$findFood.'%');
            $result = M('Reduce_food_component_detail')->where($find)->select();
            foreach ($result as $key => $value) {
                if (preg_match("/[(|（].*".$findFood."/",$value['food_name'])) {
                    unset($result[$key]);
                }
            }
            $this->assign('results',$result);
            $this->assign('hides',$hide);
            $this->assign('findFood',$findFood);
            $this->assign('tid',$title['id']);
        }
        // 常见排序
        $common = M('Reduce_food_component_detail')->where(array('class_id'=>$title['id'],'common'=>1))->select();
        $this->assign('Commons',$common);
        // 热量排序
        $hot = M('Reduce_food_component_detail')->where(array('class_id'=>$title['id']))->order('calorie desc,id asc')->select();
        $this->assign('heats',$hot);

        $hotdown = M('Reduce_food_component_detail')->where(array('class_id'=>$title['id']))->order('calorie asc,id asc')->select();
        $this->assign('hotdown',$hotdown);
        // 从高到低
        $this->assign('name',$title['class_name']);
        $this->display();
    }
    // 食物查询三级分类
    public function findfood3(){
        $id = $_GET['id'];
        if (empty($id)) {
            $this->redirect('Reduce/findfood', array('token' => $this->token,'openid'=>$this->openid));
        }else{
            $result = M('Reduce_food_component_detail')->where(array('id'=>$id))->find();
            $this->assign('result',$result);
            $carbohydrate = $result['carbohydrate_heath_ratio'];
            $data = explode("-", $carbohydrate);
            $data = array($data[0].'%',$data[1].'%');
            $data = implode('-', $data);
            $this->assign('carbohydrate_heath_ratio',$data);

            $fat = $result['fat_heath_ratio'];
            $data1 = explode("-", $fat);
            $data1 = array($data1[0].'%',$data1[1].'%');
            $data1 = implode('-', $data1);
            $this->assign('fat_heath_ratio',$data1);

            $protein = $result['protein_heath_ratio'];
            $data2 = explode("-", $protein);
            $data2 = array($data2[0].'%',$data2[1].'%');
            $data2 = implode('-', $data2);
            $this->assign('protein_heath_ratio',$data2);
            $this->display();
        }

    }
    // 运动查询，一级分类
    public function findrun(){
        if (!isset($_POST['hide']) || empty($_POST['findRun'])) {
            $result = M('Reduce_sport_class')->select();
            $this->assign('result',$result);
            $this->display();
        }else{
            $findRun = $_POST['findRun'];//查询条件
            $hide = $_POST['hide'];//判断条件
            $runName['sport_name'] = array('like','%'.$findRun.'%');
            $result = M('Reduce_sport_detail')->where($runName)->select();
            foreach ($result as $key => $value) {
                if (preg_match("/[(|（].*".$runName."/",$value['sport_name'])) {
                    unset($result[$key]);
                }
            }
            $this->assign('result',$result);

            // echo 2;
            $this->assign('hides',$hide);
            $this->assign('findRun',$findRun);
            $this->display();
        }

    }
    // 运动查询 二级分类
    public function findrun1(){
        $id = $_GET['id'];
        $title = M('Reduce_sport_class')->where(array('id'=>$id))->find();
        if (!isset($_POST['hide']) || empty($_POST['runname'])) {

            $result = M('Reduce_sport_detail')->where(array('class_id'=>$id))->select();
            $this->assign('result',$result);
            $this->assign('tid',$title['id']);

        }else{
            $hide = $_POST['hide'];//判断条件
            $runname = $_POST['runname'];//查询条件
            $id = $_GET['id'];
            $name = $_GET['name'];
            $find['sport_name'] = array('like','%'.$runname.'%');
            // $find['class_id'] = $id;
            $result = M('Reduce_sport_detail')->where($find)->select();
            foreach ($result as $key => $value) {
                if (preg_match("/[(|（].*".$runname."/",$value['sport_name'])) {
                    unset($result[$key]);
                }
            }
            $this->assign('results',$result);
            $this->assign('hides',$hide);
            $this->assign('runname',$runname);
            $this->assign('tid',$title['id']);
        }

        // 常见排序
        $common = M('Reduce_sport_detail')->where(array('class_id'=>$title['id'],'common'=>1))->select();
        $this->assign('Commons',$common);
        // 热量排序
        $hot = M('Reduce_sport_detail')->where(array('class_id'=>$title['id']))->order('calorie desc,id asc')->select();
        $this->assign('heats',$hot);


        $hotdown = M('Reduce_sport_detail')->where(array('class_id'=>$title['id']))->order('calorie asc,id asc')->select();
        $this->assign('hotdown',$hotdown);
        // 从高到低
        $this->assign('name',$title['class_name']);
        $this->display();
    }
    // 运动查询 三级分类
    public function findrun2(){
        $id = $_GET['id'];
        $result = M('Reduce_sport_detail')->where(array('id'=>$id))->find();
        // 强度
        $qiang = number_format($result['calorie'] / 60,1);
        $this->assign('qiang',$qiang);

        // 时间
        $time = intval(1800/$result['calorie']);
        $this->assign('times',$time);
        // 米饭
        $rice = number_format($result['calorie']/115,3);
        $rice = $rice * 100;
        $this->assign('rice',$rice);
        // 苹果
        $apple = number_format($result['calorie']/52,3);
        $apple = $apple * 100;
        $this->assign('apple',$apple);

        // 鸡蛋
        $egg = number_format($result['calorie']/143,3);
        $egg = $egg * 100;
        $this->assign('egg',$egg);

        // 豆浆
        $dou = number_format($result['calorie']/16,3);
        $dou = $dou * 100;
        $this->assign('dou',$dou);

        // 牛奶
        $milk = number_format($result['calorie']/54,3);
        $milk = $milk * 100;
        $this->assign('milk',$milk);

        $this->assign('result',$result);
        $this->display();
    }

    // 填写食物方案
    public function foodpackage(){
        // 用户进入首先插入四条数据
        // 早餐
        if (isset($_POST['dates'])) {
            $date = $_POST['dates'];
            // 前一天
            if ($date == date("Y-m-d",strtotime("-1 day"))) {
                 if (M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d",strtotime("-1 day"))))->select()) {
                    // 在有的情况下面进行的操作,里面没有业务逻辑
                    
                }else{
                    $data1 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'zao',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("-1 day"))
                    );
                    M('Reduce_energy')->data($data1)->add();
                    // 中餐
                    $data2 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'zhong',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("-1 day"))
                    );
                    M('Reduce_energy')->data($data2)->add();

                    // 晚餐
                    $data4 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'wan',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("-1 day"))
                    );
                    M('Reduce_energy')->data($data4)->add();
                    // 加餐
                    $data3 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'jia',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("-1 day"))
                    );
                    M('Reduce_energy')->data($data3)->add();
                    // 运动
                    $data5 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'yun',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("-1 day"))
                    );
                    M('Reduce_energy')->data($data5)->add();
                }
                // 进行数据分配来进行显示前一天
                // 进行数据分配来进行显示前一天
                // 进行数据分配来进行显示前一天
                $beforeDay = date("Y-m-d",strtotime("-1 day"));
                $diffentz = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'zao','openid'=>$this->openid))->find();
                $diffentzh = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'zhong','openid'=>$this->openid))->find();
                $diffentw = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'wan','openid'=>$this->openid))->find();
                $diffentj = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'jia','openid'=>$this->openid))->find();
                // 运动
                $diffenty = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'yun','openid'=>$this->openid))->find();
                
                $allcalorie = $diffentz['caluli'] + $diffentzh['caluli'] + $diffentw['caluli'] + $diffentj['caluli'];
                $allke = $diffentz['ke'] + $diffentzh['ke'] + $diffentw['ke'] + $diffentj['ke'];
                // 分配运动的克数以及卡路里
                $allsportke = $diffenty['ke'];
                $allsportcalorie = $diffenty['caluli'];

                $this->assign('allcalorie',$allcalorie);
                $this->assign('allke',$allke);

                $this->assign('allsportke',$allsportke);
                $this->assign('allsportcalorie',$allsportcalorie);
                
                // 运动
                if (!empty($diffenty)) {
                    $diffentys = json_decode($diffenty['foodname'],true);
                    foreach ($diffentys as $key => $value) {
                        $yundong = explode(",", $value);
                        $yunfood[$key] = $yundong[0];
                        $yuncalorie[$key] = $yundong[1];
                        $yunke[$key] = $yundong[2];
                    }
                    $county = count($diffentys);
                    $this->assign('county',$county);
                    $this->assign('yunfood',$yunfood);
                    $this->assign('yuncalorie',$yuncalorie);
                    $this->assign('yunke',$yunke);
                    
                }
                // 早餐
                if (!empty($diffentz)) {
                    $diffentzs = json_decode($diffentz['foodname'],true);
                    foreach ($diffentzs as $key => $value) {
                        $zaocan = explode(",", $value);
                        $zaofood[$key] = $zaocan[0];
                        $zaocalorie[$key] = $zaocan[1];
                        $zaoke[$key] = $zaocan[2];
                    }
                    $countz = count($diffentzs);
                    $this->assign('countz',$countz);
                    $this->assign('zaofood',$zaofood);
                    $this->assign('zaocalorie',$zaocalorie);
                    $this->assign('zaoke',$zaoke);
                }
                // 午餐
                if (!empty($diffentzh)) {
                    $diffentzhs = json_decode($diffentzh['foodname'],true);
                    foreach ($diffentzhs as $key => $value) {
                        $zhongcan = explode(",", $value);
                        $zhongfood[$key] = $zhongcan[0];
                        $zhongcalorie[$key] = $zhongcan[1];
                        $zhongke[$key] = $zhongcan[2];
                    }
                    $countzh = count($diffentzhs);
                    $this->assign('countzh',$countzh);
                    $this->assign('zhongfood',$zhongfood);
                    $this->assign('zhongcalorie',$zhongcalorie);
                    $this->assign('zhongke',$zhongke);
                    // print_r($zhongcalorie);exit();
                }
                // 晚餐
                if (!empty($diffentw)) {
                    $diffentws = json_decode($diffentw['foodname'],true);
                    foreach ($diffentws as $key => $value) {
                        $wancan = explode(",", $value);
                        $wanfood[$key] = $wancan[0];
                        $wancalorie[$key] = $wancan[1];
                        $wanke[$key] = $wancan[2];
                    }
                    $countw = count($diffentws);

                    $this->assign('countw',$countw);
                    $this->assign('wanfood',$wanfood);
                    $this->assign('wancalorie',$wancalorie);
                    $this->assign('wanke',$wanke);
                }
                // 加餐

                if (!empty($diffentj)) {
                    $diffentjs = json_decode($diffentj['foodname'],true);
                    foreach ($diffentjs as $key => $value) {
                        $jiacan = explode(",", $value);
                        $jiafood[$key] = $jiacan[0];
                        $jiacalorie[$key] = $jiacan[1];
                        $jiake[$key] = $jiacan[2];
                    }
                    $countj = count($diffentjs);
                    $this->assign('countj',$countj);
                    $this->assign('jiafood',$jiafood);
                    $this->assign('jiacalorie',$jiacalorie);
                    $this->assign('jiake',$jiake);

                }
                $this->assign('choicedate',100);
                $this->assign('diffentz',$diffentz);
                $this->assign('datess',$date);
                $this->assign('diffentzh',$diffentzh);
                $this->assign('diffentw',$diffentw);
                $this->assign('diffentj',$diffentj);
                // 分配运动模板数据
                $this->assign('diffenty',$diffenty);
                // 把时间分配到前天前台去判断
                $this->assign('currentime',$beforeDay);
                // 运动
                $yunyun = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'openid'=>$this->openid,'token'=>$this->token,'type'=>'yun'))->find();

                if (!empty($yunyun['foodname'])) {
                    $yun = json_decode($yunyun['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($yun as $key => $value) {
                        $explode = explode(",", $value);
                        $eatyun[$i] = $explode[0];
                        $kaluliyun[$i] = $explode[1];
                        $keyun[$i] = $explode[2];
                        $i++;
                    }
                    $countyun = count($yun);
                    // echo $countzao;exit();
                    $this->assign('countyun',$countyun);
                    $this->assign('eatyun',$eatyun);
                    // print_r($eatyun);exit();
                    $this->assign('yid',$yunyun['id']);
                    $this->assign('kaluliyun',$kaluliyun);
                    $this->assign('keyun',$keyun);
                    // print_r(date("Y-m-d"));exit();
                };
                // print_r($yunyun);exit();
                $this->assign('zao',$zaozao['foodname']);
                // 早餐
                $zaozao = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'openid'=>$this->openid,'token'=>$this->token,'type'=>'zao'))->find();

                if (!empty($zaozao['foodname'])) {
                    $zao = json_decode($zaozao['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zao as $key => $value) {
                        $explode = explode(",", $value);
                        $eat[$i] = $explode[0];
                        $kaluli[$i] = $explode[1];
                        $ke[$i] = $explode[2];
                        $i++;
                    }
                    $countzao = count($zao);
                    // echo $countzao;exit();
                    $this->assign('countzao',$countzao);
                    $this->assign('eat',$eat);
                    $this->assign('zid',$zaozao['id']);
                    $this->assign('kaluli',$kaluli);
                    $this->assign('ke',$ke);

                };
                $this->assign('zao',$zaozao['foodname']);
                // 午餐
                $zhongss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$beforeDay,'type'=>'zhong'))->find();
                if (!empty($zhongss['foodname'])) {
                    $zhong = json_decode($zhongss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zhong as $key => $value) {
                        $explode = explode(",", $value);
                        $eats[$i] = $explode[0];
                        $kalulis[$i] = $explode[1];
                        $kes[$i] = $explode[2];
                        $i++;
                    }
                    $countzhong = count($zhong);

                    $this->assign('countzhong',$countzhong);
                    $this->assign('eats',$eats);
                    $this->assign('wid',$zhongss['id']);
                    $this->assign('kalulis',$kalulis);
                    $this->assign('kes',$kes);
                };
                // print_r($zhongss);exit();
                $this->assign('zhong',$zhongss['foodname']);
                // 加餐
                $jiass = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$beforeDay,'type'=>'jia'))->find();
                if (!empty($zhongss['foodname'])) {
                    $jia = json_decode($jiass['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($jia as $key => $value) {
                        $explode = explode(",", $value);
                        $eatss[$i] = $explode[0];
                        $kaluliss[$i] = $explode[1];
                        $kess[$i] = $explode[2];
                        $i++;
                    }
                    $countjia = count($jia);

                    $this->assign('countjia',$countjia);
                    $this->assign('eatss',$eatss);

                    $this->assign('jid',$jiass['id']);

                    $this->assign('kaluliss',$kaluliss);
                    $this->assign('kess',$kess);
                };
                $this->assign('jia',$jiass['foodname']);
                // 晚餐
                $wanss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$beforeDay,'type'=>'wan'))->find();
                if (!empty($wanss['foodname'])) {
                    $wan = json_decode($wanss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($wan as $key => $value) {
                        $explode = explode(",", $value);
                        $eatsss[$i] = $explode[0];
                        $kalulisss[$i] = $explode[1];
                        $kesss[$i] = $explode[2];
                        $i++;
                    }
                    $countwan = count($wan);

                    $this->assign('countwan',$countwan);
                    $this->assign('eatsss',$eatsss);

                    $this->assign('did',$wanss['id']);
                    $this->assign('kalulisss',$kalulisss);
                    $this->assign('kesss',$kesss);
                };
                $this->assign('wan',$wanss['foodname']);
            }elseif ($date == date("Y-m-d",strtotime("+1 day"))){
                if (M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d",strtotime("+1 day"))))->select()) {
                    // 如果后一天的时候有的话不做任何操作
                }else{
                    // 如果后一天没有的话做添加五条数据
                     $data1 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'zao',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("+1 day"))
                    );
                    M('Reduce_energy')->data($data1)->add();
                    // 中餐
                    $data2 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'zhong',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("+1 day"))
                    );
                    M('Reduce_energy')->data($data2)->add();

                    // 晚餐
                    $data4 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'wan',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("+1 day"))
                    );
                    M('Reduce_energy')->data($data4)->add();
                    // 加餐
                    $data3 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'jia',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("+1 day"))
                    );
                    M('Reduce_energy')->data($data3)->add();
                    // 运动
                    $data5 = array(
                        'caluli' => '0',
                        'ke' => '0',
                        'type' => 'yun',
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'addtime' => date("Y-m-d",strtotime("+1 day"))
                    );
                    M('Reduce_energy')->data($data5)->add();
                }
                // 后一天的业务逻辑
                // 后一天的业务逻辑
                // 后一天的业务逻辑
                $afterDay = date("Y-m-d",strtotime("+1 day"));
                $diffentz = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'zao','openid'=>$this->openid))->find();
                $diffentzh = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'zhong','openid'=>$this->openid))->find();
                $diffentw = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'wan','openid'=>$this->openid))->find();
                $diffentj = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'jia','openid'=>$this->openid))->find();
                // 运动
                $diffenty = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'yun','openid'=>$this->openid))->find();
                
                $allcalorie = $diffentz['caluli'] + $diffentzh['caluli'] + $diffentw['caluli'] + $diffentj['caluli'];
                $allke = $diffentz['ke'] + $diffentzh['ke'] + $diffentw['ke'] + $diffentj['ke'];
                // 分配运动的克数以及卡路里
                $allsportke = $diffenty['ke'];
                $allsportcalorie = $diffenty['caluli'];

                $this->assign('allcalorie',$allcalorie);
                $this->assign('allke',$allke);

                $this->assign('allsportke',$allsportke);
                $this->assign('allsportcalorie',$allsportcalorie);
                
                // 运动
                if (!empty($diffenty)) {
                    $diffentys = json_decode($diffenty['foodname'],true);
                    foreach ($diffentys as $key => $value) {
                        $yundong = explode(",", $value);
                        $yunfood[$key] = $yundong[0];
                        $yuncalorie[$key] = $yundong[1];
                        $yunke[$key] = $yundong[2];
                    }
                    $county = count($diffentys);
                    $this->assign('county',$county);
                    $this->assign('yunfood',$yunfood);
                    $this->assign('yuncalorie',$yuncalorie);
                    $this->assign('yunke',$yunke);
                    
                }
                // 早餐
                if (!empty($diffentz)) {
                    $diffentzs = json_decode($diffentz['foodname'],true);
                    foreach ($diffentzs as $key => $value) {
                        $zaocan = explode(",", $value);
                        $zaofood[$key] = $zaocan[0];
                        $zaocalorie[$key] = $zaocan[1];
                        $zaoke[$key] = $zaocan[2];
                    }
                    $countz = count($diffentzs);
                    $this->assign('countz',$countz);
                    $this->assign('zaofood',$zaofood);
                    $this->assign('zaocalorie',$zaocalorie);
                    $this->assign('zaoke',$zaoke);
                }
                // 午餐
                if (!empty($diffentzh)) {
                    $diffentzhs = json_decode($diffentzh['foodname'],true);
                    foreach ($diffentzhs as $key => $value) {
                        $zhongcan = explode(",", $value);
                        $zhongfood[$key] = $zhongcan[0];
                        $zhongcalorie[$key] = $zhongcan[1];
                        $zhongke[$key] = $zhongcan[2];
                    }
                    $countzh = count($diffentzhs);
                    $this->assign('countzh',$countzh);
                    $this->assign('zhongfood',$zhongfood);
                    $this->assign('zhongcalorie',$zhongcalorie);
                    $this->assign('zhongke',$zhongke);
                    // print_r($zhongcalorie);exit();
                }
                // 晚餐
                if (!empty($diffentw)) {
                    $diffentws = json_decode($diffentw['foodname'],true);
                    foreach ($diffentws as $key => $value) {
                        $wancan = explode(",", $value);
                        $wanfood[$key] = $wancan[0];
                        $wancalorie[$key] = $wancan[1];
                        $wanke[$key] = $wancan[2];
                    }
                    $countw = count($diffentws);

                    $this->assign('countw',$countw);
                    $this->assign('wanfood',$wanfood);
                    $this->assign('wancalorie',$wancalorie);
                    $this->assign('wanke',$wanke);
                }
                // 加餐

                if (!empty($diffentj)) {
                    $diffentjs = json_decode($diffentj['foodname'],true);
                    foreach ($diffentjs as $key => $value) {
                        $jiacan = explode(",", $value);
                        $jiafood[$key] = $jiacan[0];
                        $jiacalorie[$key] = $jiacan[1];
                        $jiake[$key] = $jiacan[2];
                    }
                    $countj = count($diffentjs);
                    $this->assign('countj',$countj);
                    $this->assign('jiafood',$jiafood);
                    $this->assign('jiacalorie',$jiacalorie);
                    $this->assign('jiake',$jiake);

                }
                $this->assign('choicedate',100);
                $this->assign('diffentz',$diffentz);
                $this->assign('datess',$date);
                $this->assign('diffentzh',$diffentzh);
                $this->assign('diffentw',$diffentw);
                $this->assign('diffentj',$diffentj);
                // 分配运动模板数据
                $this->assign('diffenty',$diffenty);
                 // 把时间分配到前天前台去判断
                $this->assign('currentime',$afterDay);
                // 运动
                $yunyun = M('Reduce_energy')->where(array('addtime'=>$afterDay,'openid'=>$this->openid,'token'=>$this->token,'type'=>'yun'))->find();

                if (!empty($yunyun['foodname'])) {
                    $yun = json_decode($yunyun['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($yun as $key => $value) {
                        $explode = explode(",", $value);
                        $eatyun[$i] = $explode[0];
                        $kaluliyun[$i] = $explode[1];
                        $keyun[$i] = $explode[2];
                        $i++;
                    }
                    $countyun = count($yun);
                    // echo $countzao;exit();
                    $this->assign('countyun',$countyun);
                    $this->assign('eatyun',$eatyun);
                    // print_r($eatyun);exit();
                    $this->assign('yid',$yunyun['id']);
                    $this->assign('kaluliyun',$kaluliyun);
                    $this->assign('keyun',$keyun);
                    // print_r(date("Y-m-d"));exit();
                };
                // print_r($yunyun);exit();
                $this->assign('zao',$zaozao['foodname']);
                // 早餐
                $zaozao = M('Reduce_energy')->where(array('addtime'=>$afterDay,'openid'=>$this->openid,'token'=>$this->token,'type'=>'zao'))->find();

                if (!empty($zaozao['foodname'])) {
                    $zao = json_decode($zaozao['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zao as $key => $value) {
                        $explode = explode(",", $value);
                        $eat[$i] = $explode[0];
                        $kaluli[$i] = $explode[1];
                        $ke[$i] = $explode[2];
                        $i++;
                    }
                    $countzao = count($zao);
                    // echo $countzao;exit();
                    $this->assign('countzao',$countzao);
                    $this->assign('eat',$eat);
                    $this->assign('zid',$zaozao['id']);
                    $this->assign('kaluli',$kaluli);
                    $this->assign('ke',$ke);

                };
                $this->assign('zao',$zaozao['foodname']);
                // 午餐
                $zhongss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$afterDay,'type'=>'zhong'))->find();
                if (!empty($zhongss['foodname'])) {
                    $zhong = json_decode($zhongss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zhong as $key => $value) {
                        $explode = explode(",", $value);
                        $eats[$i] = $explode[0];
                        $kalulis[$i] = $explode[1];
                        $kes[$i] = $explode[2];
                        $i++;
                    }
                    $countzhong = count($zhong);

                    $this->assign('countzhong',$countzhong);
                    $this->assign('eats',$eats);
                    $this->assign('wid',$zhongss['id']);
                    $this->assign('kalulis',$kalulis);
                    $this->assign('kes',$kes);
                };
                // print_r($zhongss);exit();
                $this->assign('zhong',$zhongss['foodname']);
                // 加餐
                $jiass = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$afterDay,'type'=>'jia'))->find();
                if (!empty($zhongss['foodname'])) {
                    $jia = json_decode($jiass['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($jia as $key => $value) {
                        $explode = explode(",", $value);
                        $eatss[$i] = $explode[0];
                        $kaluliss[$i] = $explode[1];
                        $kess[$i] = $explode[2];
                        $i++;
                    }
                    $countjia = count($jia);

                    $this->assign('countjia',$countjia);
                    $this->assign('eatss',$eatss);

                    $this->assign('jid',$jiass['id']);

                    $this->assign('kaluliss',$kaluliss);
                    $this->assign('kess',$kess);
                };
                $this->assign('jia',$jiass['foodname']);
                // print_r($jiass);exit();
                // 晚餐
                $wanss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$afterDay,'type'=>'wan'))->find();
                if (!empty($wanss['foodname'])) {
                    $wan = json_decode($wanss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($wan as $key => $value) {
                        $explode = explode(",", $value);
                        $eatsss[$i] = $explode[0];
                        $kalulisss[$i] = $explode[1];
                        $kesss[$i] = $explode[2];
                        $i++;
                    }
                    $countwan = count($wan);

                    $this->assign('countwan',$countwan);
                    $this->assign('eatsss',$eatsss);

                    $this->assign('did',$wanss['id']);
                    $this->assign('kalulisss',$kalulisss);
                    $this->assign('kesss',$kesss);
                };
                $this->assign('wan',$wanss['foodname']);
                // 下面是当前天数的
            }else{
                $diffentz = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'zao','openid'=>$this->openid))->find();
                $diffentzh = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'zhong','openid'=>$this->openid))->find();
                $diffentw = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'wan','openid'=>$this->openid))->find();
                $diffentj = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'jia','openid'=>$this->openid))->find();
                // 运动
                $diffenty = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'yun','openid'=>$this->openid))->find();
                // print_r($diffenty);exit();
                $allcalorie = $diffentz['caluli'] + $diffentzh['caluli'] + $diffentw['caluli'] + $diffentj['caluli'];
                $allke = $diffentz['ke'] + $diffentzh['ke'] + $diffentw['ke'] + $diffentj['ke'];
                // 分配运动的克数以及卡路里
                $allsportke = $diffenty['ke'];
                $allsportcalorie = $diffenty['caluli'];

                $this->assign('allcalorie',$allcalorie);
                $this->assign('allke',$allke);

                $this->assign('allsportke',$allsportke);
                $this->assign('allsportcalorie',$allsportcalorie);
                // print_r($allcalorie);exit();
                // 运动
                if (!empty($diffenty)) {
                    $diffentys = json_decode($diffenty['foodname'],true);
                    foreach ($diffentys as $key => $value) {
                        $yundong = explode(",", $value);
                        $yunfood[$key] = $yundong[0];
                        $yuncalorie[$key] = $yundong[1];
                        $yunke[$key] = $yundong[2];
                    }
                    $county = count($diffentys);
                    $this->assign('county',$county);
                    $this->assign('yunfood',$yunfood);
                    $this->assign('yuncalorie',$yuncalorie);
                    $this->assign('yunke',$yunke);
                    // print_r($diffentys);exit();
                }
                // 早餐
                if (!empty($diffentz)) {
                    $diffentzs = json_decode($diffentz['foodname'],true);
                    foreach ($diffentzs as $key => $value) {
                        $zaocan = explode(",", $value);
                        $zaofood[$key] = $zaocan[0];
                        $zaocalorie[$key] = $zaocan[1];
                        $zaoke[$key] = $zaocan[2];
                    }
                    $countz = count($diffentzs);
                    $this->assign('countz',$countz);
                    $this->assign('zaofood',$zaofood);
                    $this->assign('zaocalorie',$zaocalorie);
                    $this->assign('zaoke',$zaoke);
                }
                // 午餐
                if (!empty($diffentzh)) {
                    $diffentzhs = json_decode($diffentzh['foodname'],true);
                    foreach ($diffentzhs as $key => $value) {
                        $zhongcan = explode(",", $value);
                        $zhongfood[$key] = $zhongcan[0];
                        $zhongcalorie[$key] = $zhongcan[1];
                        $zhongke[$key] = $zhongcan[2];
                    }
                    $countzh = count($diffentzhs);
                    $this->assign('countzh',$countzh);
                    $this->assign('zhongfood',$zhongfood);
                    $this->assign('zhongcalorie',$zhongcalorie);
                    $this->assign('zhongke',$zhongke);
                    // print_r($zhongcalorie);exit();
                }
                // 晚餐
                if (!empty($diffentw)) {
                    $diffentws = json_decode($diffentw['foodname'],true);
                    foreach ($diffentws as $key => $value) {
                        $wancan = explode(",", $value);
                        $wanfood[$key] = $wancan[0];
                        $wancalorie[$key] = $wancan[1];
                        $wanke[$key] = $wancan[2];
                    }
                    $countw = count($diffentws);

                    $this->assign('countw',$countw);
                    $this->assign('wanfood',$wanfood);
                    $this->assign('wancalorie',$wancalorie);
                    $this->assign('wanke',$wanke);
                }
                // 加餐

                if (!empty($diffentj)) {
                    $diffentjs = json_decode($diffentj['foodname'],true);
                    foreach ($diffentjs as $key => $value) {
                        $jiacan = explode(",", $value);
                        $jiafood[$key] = $jiacan[0];
                        $jiacalorie[$key] = $jiacan[1];
                        $jiake[$key] = $jiacan[2];
                    }
                    $countj = count($diffentjs);
                    $this->assign('countj',$countj);
                    $this->assign('jiafood',$jiafood);
                    $this->assign('jiacalorie',$jiacalorie);
                    $this->assign('jiake',$jiake);

                }
                $this->assign('choicedate',100);
                $this->assign('diffentz',$diffentz);
                $this->assign('datess',$date);
                $this->assign('diffentzh',$diffentzh);
                $this->assign('diffentw',$diffentw);
                $this->assign('diffentj',$diffentj);
                // 分配运动模板数据
                $this->assign('diffenty',$diffenty);
                // 把时间分配到前天前台去判断
                $this->assign('currentime',$date);
                // 运动
                $yunyun = M('Reduce_energy')->where(array('addtime'=>date("Y-m-d"),'openid'=>$this->openid,'token'=>$this->token,'type'=>'yun'))->find();

                if (!empty($yunyun['foodname'])) {
                    $yun = json_decode($yunyun['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($yun as $key => $value) {
                        $explode = explode(",", $value);
                        $eatyun[$i] = $explode[0];
                        $kaluliyun[$i] = $explode[1];
                        $keyun[$i] = $explode[2];
                        $i++;
                    }
                    $countyun = count($yun);
                    // echo $countzao;exit();
                    $this->assign('countyun',$countyun);
                    $this->assign('eatyun',$eatyun);
                    // print_r($eatyun);exit();
                    $this->assign('yid',$yunyun['id']);
                    $this->assign('kaluliyun',$kaluliyun);
                    $this->assign('keyun',$keyun);
                    // print_r(date("Y-m-d"));exit();
                };
                // print_r($yunyun);exit();
                $this->assign('zao',$zaozao['foodname']);
                // 早餐
                $zaozao = M('Reduce_energy')->where(array('addtime'=>date("Y-m-d"),'openid'=>$this->openid,'token'=>$this->token,'type'=>'zao'))->find();

                if (!empty($zaozao['foodname'])) {
                    $zao = json_decode($zaozao['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zao as $key => $value) {
                        $explode = explode(",", $value);
                        $eat[$i] = $explode[0];
                        $kaluli[$i] = $explode[1];
                        $ke[$i] = $explode[2];
                        $i++;
                    }
                    $countzao = count($zao);
                    // echo $countzao;exit();
                    $this->assign('countzao',$countzao);
                    $this->assign('eat',$eat);
                    $this->assign('zid',$zaozao['id']);
                    $this->assign('kaluli',$kaluli);
                    $this->assign('ke',$ke);

                };
                $this->assign('zao',$zaozao['foodname']);
                // 午餐
                $zhongss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'zhong'))->find();
                if (!empty($zhongss['foodname'])) {
                    $zhong = json_decode($zhongss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zhong as $key => $value) {
                        $explode = explode(",", $value);
                        $eats[$i] = $explode[0];
                        $kalulis[$i] = $explode[1];
                        $kes[$i] = $explode[2];
                        $i++;
                    }
                    $countzhong = count($zhong);

                    $this->assign('countzhong',$countzhong);
                    $this->assign('eats',$eats);
                    $this->assign('wid',$zhongss['id']);
                    $this->assign('kalulis',$kalulis);
                    $this->assign('kes',$kes);
                };
                // print_r($zhongss);exit();
                $this->assign('zhong',$zhongss['foodname']);
                // 加餐
                $jiass = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'jia'))->find();
                if (!empty($zhongss['foodname'])) {
                    $jia = json_decode($jiass['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($jia as $key => $value) {
                        $explode = explode(",", $value);
                        $eatss[$i] = $explode[0];
                        $kaluliss[$i] = $explode[1];
                        $kess[$i] = $explode[2];
                        $i++;
                    }
                    $countjia = count($jia);

                    $this->assign('countjia',$countjia);
                    $this->assign('eatss',$eatss);

                    $this->assign('jid',$jiass['id']);

                    $this->assign('kaluliss',$kaluliss);
                    $this->assign('kess',$kess);
                };
                $this->assign('jia',$jiass['foodname']);
                // print_r($jiass);exit();
                // 晚餐
                $wanss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'wan'))->find();
                if (!empty($wanss['foodname'])) {
                    $wan = json_decode($wanss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($wan as $key => $value) {
                        $explode = explode(",", $value);
                        $eatsss[$i] = $explode[0];
                        $kalulisss[$i] = $explode[1];
                        $kesss[$i] = $explode[2];
                        $i++;
                    }
                    $countwan = count($wan);

                    $this->assign('countwan',$countwan);
                    $this->assign('eatsss',$eatsss);

                    $this->assign('did',$wanss['id']);
                    $this->assign('kalulisss',$kalulisss);
                    $this->assign('kesss',$kesss);
                };
                $this->assign('wan',$wanss['foodname']);
            }
            $this->display();
        }else{
            // 添加以及修改还有删除数据后所走的路是这里的
            if(M('Reduce_energy')->where(array('token'=>$this->token,'openid'=>$this->openid,'addtime'=>date("Y-m-d")))->select()){
                // 首先判断是否有这天的数据，有的话就不需要新增加数据
            }else{
                $data1 = array(
                    'caluli' => '0',
                    'ke' => '0',
                    'type' => 'zao',
                    'token' => $this->token,
                    'openid' => $this->openid,
                    'addtime' => date("Y-m-d")
                );
                M('Reduce_energy')->data($data1)->add();
                // 中餐
                $data2 = array(
                    'caluli' => '0',
                    'ke' => '0',
                    'type' => 'zhong',
                    'token' => $this->token,
                    'openid' => $this->openid,
                    'addtime' => date("Y-m-d")
                );
                M('Reduce_energy')->data($data2)->add();

                // 晚餐
                $data4 = array(
                    'caluli' => '0',
                    'ke' => '0',
                    'type' => 'wan',
                    'token' => $this->token,
                    'openid' => $this->openid,
                    'addtime' => date("Y-m-d")
                );
                M('Reduce_energy')->data($data4)->add();
                // 加餐
                $data3 = array(
                    'caluli' => '0',
                    'ke' => '0',
                    'type' => 'jia',
                    'token' => $this->token,
                    'openid' => $this->openid,
                    'addtime' => date("Y-m-d")
                );
                M('Reduce_energy')->data($data3)->add();
                // 运动
                $data5 = array(
                    'caluli' => '0',
                    'ke' => '0',
                    'type' => 'yun',
                    'token' => $this->token,
                    'openid' => $this->openid,
                    'addtime' => date("Y-m-d")
                );
                M('Reduce_energy')->data($data5)->add();
            };
            // 首先添加数据
            // 添加修改删除后的数据的改变
            // // 添加修改删除后的数据的改变
            // // 添加修改删除后的数据的改变
            if(isset($_GET['addtime'])){
                // 如果在能够接受到添加时间的时候
                $date = $_GET['addtime'];
            // 前一天
            if ($date == date("Y-m-d",strtotime("-1 day"))) {
                 
                // 进行数据分配来进行显示前一天
                // 进行数据分配来进行显示前一天
                // 进行数据分配来进行显示前一天
                $beforeDay = date("Y-m-d",strtotime("-1 day"));
                $diffentz = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'zao','openid'=>$this->openid))->find();
                $diffentzh = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'zhong','openid'=>$this->openid))->find();
                $diffentw = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'wan','openid'=>$this->openid))->find();
                $diffentj = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'jia','openid'=>$this->openid))->find();
                // 运动
                $diffenty = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'type'=>'yun','openid'=>$this->openid))->find();
                
                $allcalorie = $diffentz['caluli'] + $diffentzh['caluli'] + $diffentw['caluli'] + $diffentj['caluli'];
                $allke = $diffentz['ke'] + $diffentzh['ke'] + $diffentw['ke'] + $diffentj['ke'];
                // 分配运动的克数以及卡路里
                $allsportke = $diffenty['ke'];
                $allsportcalorie = $diffenty['caluli'];

                $this->assign('allcalorie',$allcalorie);
                $this->assign('allke',$allke);

                $this->assign('allsportke',$allsportke);
                $this->assign('allsportcalorie',$allsportcalorie);
                
                // 运动
                if (!empty($diffenty)) {
                    $diffentys = json_decode($diffenty['foodname'],true);
                    foreach ($diffentys as $key => $value) {
                        $yundong = explode(",", $value);
                        $yunfood[$key] = $yundong[0];
                        $yuncalorie[$key] = $yundong[1];
                        $yunke[$key] = $yundong[2];
                    }
                    $county = count($diffentys);
                    $this->assign('county',$county);
                    $this->assign('yunfood',$yunfood);
                    $this->assign('yuncalorie',$yuncalorie);
                    $this->assign('yunke',$yunke);
                    
                }
                // 早餐
                if (!empty($diffentz)) {
                    $diffentzs = json_decode($diffentz['foodname'],true);
                    foreach ($diffentzs as $key => $value) {
                        $zaocan = explode(",", $value);
                        $zaofood[$key] = $zaocan[0];
                        $zaocalorie[$key] = $zaocan[1];
                        $zaoke[$key] = $zaocan[2];
                    }
                    $countz = count($diffentzs);
                    $this->assign('countz',$countz);
                    $this->assign('zaofood',$zaofood);
                    $this->assign('zaocalorie',$zaocalorie);
                    $this->assign('zaoke',$zaoke);
                }
                // 午餐
                if (!empty($diffentzh)) {
                    $diffentzhs = json_decode($diffentzh['foodname'],true);
                    foreach ($diffentzhs as $key => $value) {
                        $zhongcan = explode(",", $value);
                        $zhongfood[$key] = $zhongcan[0];
                        $zhongcalorie[$key] = $zhongcan[1];
                        $zhongke[$key] = $zhongcan[2];
                    }
                    $countzh = count($diffentzhs);
                    $this->assign('countzh',$countzh);
                    $this->assign('zhongfood',$zhongfood);
                    $this->assign('zhongcalorie',$zhongcalorie);
                    $this->assign('zhongke',$zhongke);
                    // print_r($zhongcalorie);exit();
                }
                // 晚餐
                if (!empty($diffentw)) {
                    $diffentws = json_decode($diffentw['foodname'],true);
                    foreach ($diffentws as $key => $value) {
                        $wancan = explode(",", $value);
                        $wanfood[$key] = $wancan[0];
                        $wancalorie[$key] = $wancan[1];
                        $wanke[$key] = $wancan[2];
                    }
                    $countw = count($diffentws);

                    $this->assign('countw',$countw);
                    $this->assign('wanfood',$wanfood);
                    $this->assign('wancalorie',$wancalorie);
                    $this->assign('wanke',$wanke);
                }
                // 加餐

                if (!empty($diffentj)) {
                    $diffentjs = json_decode($diffentj['foodname'],true);
                    foreach ($diffentjs as $key => $value) {
                        $jiacan = explode(",", $value);
                        $jiafood[$key] = $jiacan[0];
                        $jiacalorie[$key] = $jiacan[1];
                        $jiake[$key] = $jiacan[2];
                    }
                    $countj = count($diffentjs);
                    $this->assign('countj',$countj);
                    $this->assign('jiafood',$jiafood);
                    $this->assign('jiacalorie',$jiacalorie);
                    $this->assign('jiake',$jiake);

                }
                $this->assign('choicedate',100);
                $this->assign('diffentz',$diffentz);
                $this->assign('datess',$date);
                $this->assign('diffentzh',$diffentzh);
                $this->assign('diffentw',$diffentw);
                $this->assign('diffentj',$diffentj);
                // 分配运动模板数据
                $this->assign('diffenty',$diffenty);
                // 把时间分配到前天前台去判断
                $this->assign('currentime',$beforeDay);
                // 运动
                $yunyun = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'openid'=>$this->openid,'token'=>$this->token,'type'=>'yun'))->find();

                if (!empty($yunyun['foodname'])) {
                    $yun = json_decode($yunyun['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($yun as $key => $value) {
                        $explode = explode(",", $value);
                        $eatyun[$i] = $explode[0];
                        $kaluliyun[$i] = $explode[1];
                        $keyun[$i] = $explode[2];
                        $i++;
                    }
                    $countyun = count($yun);
                    // echo $countzao;exit();
                    $this->assign('countyun',$countyun);
                    $this->assign('eatyun',$eatyun);
                    // print_r($eatyun);exit();
                    $this->assign('yid',$yunyun['id']);
                    $this->assign('kaluliyun',$kaluliyun);
                    $this->assign('keyun',$keyun);
                    // print_r(date("Y-m-d"));exit();
                };
                // print_r($yunyun);exit();
                $this->assign('zao',$zaozao['foodname']);
                // 早餐
                $zaozao = M('Reduce_energy')->where(array('addtime'=>$beforeDay,'openid'=>$this->openid,'token'=>$this->token,'type'=>'zao'))->find();

                if (!empty($zaozao['foodname'])) {
                    $zao = json_decode($zaozao['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zao as $key => $value) {
                        $explode = explode(",", $value);
                        $eat[$i] = $explode[0];
                        $kaluli[$i] = $explode[1];
                        $ke[$i] = $explode[2];
                        $i++;
                    }
                    $countzao = count($zao);
                    // echo $countzao;exit();
                    $this->assign('countzao',$countzao);
                    $this->assign('eat',$eat);
                    $this->assign('zid',$zaozao['id']);
                    $this->assign('kaluli',$kaluli);
                    $this->assign('ke',$ke);

                };
                $this->assign('zao',$zaozao['foodname']);
                // 午餐
                $zhongss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$beforeDay,'type'=>'zhong'))->find();
                if (!empty($zhongss['foodname'])) {
                    $zhong = json_decode($zhongss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zhong as $key => $value) {
                        $explode = explode(",", $value);
                        $eats[$i] = $explode[0];
                        $kalulis[$i] = $explode[1];
                        $kes[$i] = $explode[2];
                        $i++;
                    }
                    $countzhong = count($zhong);

                    $this->assign('countzhong',$countzhong);
                    $this->assign('eats',$eats);
                    $this->assign('wid',$zhongss['id']);
                    $this->assign('kalulis',$kalulis);
                    $this->assign('kes',$kes);
                };
                // print_r($zhongss);exit();
                $this->assign('zhong',$zhongss['foodname']);
                // 加餐
                $jiass = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$beforeDay,'type'=>'jia'))->find();
                if (!empty($zhongss['foodname'])) {
                    $jia = json_decode($jiass['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($jia as $key => $value) {
                        $explode = explode(",", $value);
                        $eatss[$i] = $explode[0];
                        $kaluliss[$i] = $explode[1];
                        $kess[$i] = $explode[2];
                        $i++;
                    }
                    $countjia = count($jia);

                    $this->assign('countjia',$countjia);
                    $this->assign('eatss',$eatss);

                    $this->assign('jid',$jiass['id']);

                    $this->assign('kaluliss',$kaluliss);
                    $this->assign('kess',$kess);
                };
                $this->assign('jia',$jiass['foodname']);
                // 晚餐
                $wanss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$beforeDay,'type'=>'wan'))->find();
                if (!empty($wanss['foodname'])) {
                    $wan = json_decode($wanss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($wan as $key => $value) {
                        $explode = explode(",", $value);
                        $eatsss[$i] = $explode[0];
                        $kalulisss[$i] = $explode[1];
                        $kesss[$i] = $explode[2];
                        $i++;
                    }
                    $countwan = count($wan);

                    $this->assign('countwan',$countwan);
                    $this->assign('eatsss',$eatsss);

                    $this->assign('did',$wanss['id']);
                    $this->assign('kalulisss',$kalulisss);
                    $this->assign('kesss',$kesss);
                };
                $this->assign('wan',$wanss['foodname']);
            }elseif ($date == date("Y-m-d",strtotime("+1 day"))){
               
                // 后一天的业务逻辑
                // 后一天的业务逻辑
                // 后一天的业务逻辑
                $afterDay = date("Y-m-d",strtotime("+1 day"));
                $diffentz = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'zao','openid'=>$this->openid))->find();
                $diffentzh = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'zhong','openid'=>$this->openid))->find();
                $diffentw = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'wan','openid'=>$this->openid))->find();
                $diffentj = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'jia','openid'=>$this->openid))->find();
                // 运动
                $diffenty = M('Reduce_energy')->where(array('addtime'=>$afterDay,'type'=>'yun','openid'=>$this->openid))->find();
                
                $allcalorie = $diffentz['caluli'] + $diffentzh['caluli'] + $diffentw['caluli'] + $diffentj['caluli'];
                $allke = $diffentz['ke'] + $diffentzh['ke'] + $diffentw['ke'] + $diffentj['ke'];
                // 分配运动的克数以及卡路里
                $allsportke = $diffenty['ke'];
                $allsportcalorie = $diffenty['caluli'];

                $this->assign('allcalorie',$allcalorie);
                $this->assign('allke',$allke);

                $this->assign('allsportke',$allsportke);
                $this->assign('allsportcalorie',$allsportcalorie);
                
                // 运动
                if (!empty($diffenty)) {
                    $diffentys = json_decode($diffenty['foodname'],true);
                    foreach ($diffentys as $key => $value) {
                        $yundong = explode(",", $value);
                        $yunfood[$key] = $yundong[0];
                        $yuncalorie[$key] = $yundong[1];
                        $yunke[$key] = $yundong[2];
                    }
                    $county = count($diffentys);
                    $this->assign('county',$county);
                    $this->assign('yunfood',$yunfood);
                    $this->assign('yuncalorie',$yuncalorie);
                    $this->assign('yunke',$yunke);
                    
                }
                // 早餐
                if (!empty($diffentz)) {
                    $diffentzs = json_decode($diffentz['foodname'],true);
                    foreach ($diffentzs as $key => $value) {
                        $zaocan = explode(",", $value);
                        $zaofood[$key] = $zaocan[0];
                        $zaocalorie[$key] = $zaocan[1];
                        $zaoke[$key] = $zaocan[2];
                    }
                    $countz = count($diffentzs);
                    $this->assign('countz',$countz);
                    $this->assign('zaofood',$zaofood);
                    $this->assign('zaocalorie',$zaocalorie);
                    $this->assign('zaoke',$zaoke);
                }
                // 午餐
                if (!empty($diffentzh)) {
                    $diffentzhs = json_decode($diffentzh['foodname'],true);
                    foreach ($diffentzhs as $key => $value) {
                        $zhongcan = explode(",", $value);
                        $zhongfood[$key] = $zhongcan[0];
                        $zhongcalorie[$key] = $zhongcan[1];
                        $zhongke[$key] = $zhongcan[2];
                    }
                    $countzh = count($diffentzhs);
                    $this->assign('countzh',$countzh);
                    $this->assign('zhongfood',$zhongfood);
                    $this->assign('zhongcalorie',$zhongcalorie);
                    $this->assign('zhongke',$zhongke);
                    // print_r($zhongcalorie);exit();
                }
                // 晚餐
                if (!empty($diffentw)) {
                    $diffentws = json_decode($diffentw['foodname'],true);
                    foreach ($diffentws as $key => $value) {
                        $wancan = explode(",", $value);
                        $wanfood[$key] = $wancan[0];
                        $wancalorie[$key] = $wancan[1];
                        $wanke[$key] = $wancan[2];
                    }
                    $countw = count($diffentws);

                    $this->assign('countw',$countw);
                    $this->assign('wanfood',$wanfood);
                    $this->assign('wancalorie',$wancalorie);
                    $this->assign('wanke',$wanke);
                }
                // 加餐

                if (!empty($diffentj)) {
                    $diffentjs = json_decode($diffentj['foodname'],true);
                    foreach ($diffentjs as $key => $value) {
                        $jiacan = explode(",", $value);
                        $jiafood[$key] = $jiacan[0];
                        $jiacalorie[$key] = $jiacan[1];
                        $jiake[$key] = $jiacan[2];
                    }
                    $countj = count($diffentjs);
                    $this->assign('countj',$countj);
                    $this->assign('jiafood',$jiafood);
                    $this->assign('jiacalorie',$jiacalorie);
                    $this->assign('jiake',$jiake);

                }
                $this->assign('choicedate',100);
                $this->assign('diffentz',$diffentz);
                $this->assign('datess',$date);
                $this->assign('diffentzh',$diffentzh);
                $this->assign('diffentw',$diffentw);
                $this->assign('diffentj',$diffentj);
                // 分配运动模板数据
                $this->assign('diffenty',$diffenty);
                 // 把时间分配到前天前台去判断
                $this->assign('currentime',$afterDay);
                // 运动
                $yunyun = M('Reduce_energy')->where(array('addtime'=>$afterDay,'openid'=>$this->openid,'token'=>$this->token,'type'=>'yun'))->find();

                if (!empty($yunyun['foodname'])) {
                    $yun = json_decode($yunyun['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($yun as $key => $value) {
                        $explode = explode(",", $value);
                        $eatyun[$i] = $explode[0];
                        $kaluliyun[$i] = $explode[1];
                        $keyun[$i] = $explode[2];
                        $i++;
                    }
                    $countyun = count($yun);
                    // echo $countzao;exit();
                    $this->assign('countyun',$countyun);
                    $this->assign('eatyun',$eatyun);
                    // print_r($eatyun);exit();
                    $this->assign('yid',$yunyun['id']);
                    $this->assign('kaluliyun',$kaluliyun);
                    $this->assign('keyun',$keyun);
                    // print_r(date("Y-m-d"));exit();
                };
                // print_r($yunyun);exit();
                $this->assign('zao',$zaozao['foodname']);
                // 早餐
                $zaozao = M('Reduce_energy')->where(array('addtime'=>$afterDay,'openid'=>$this->openid,'token'=>$this->token,'type'=>'zao'))->find();

                if (!empty($zaozao['foodname'])) {
                    $zao = json_decode($zaozao['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zao as $key => $value) {
                        $explode = explode(",", $value);
                        $eat[$i] = $explode[0];
                        $kaluli[$i] = $explode[1];
                        $ke[$i] = $explode[2];
                        $i++;
                    }
                    $countzao = count($zao);
                    // echo $countzao;exit();
                    $this->assign('countzao',$countzao);
                    $this->assign('eat',$eat);
                    $this->assign('zid',$zaozao['id']);
                    $this->assign('kaluli',$kaluli);
                    $this->assign('ke',$ke);

                };
                $this->assign('zao',$zaozao['foodname']);
                // 午餐
                $zhongss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$afterDay,'type'=>'zhong'))->find();
                if (!empty($zhongss['foodname'])) {
                    $zhong = json_decode($zhongss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zhong as $key => $value) {
                        $explode = explode(",", $value);
                        $eats[$i] = $explode[0];
                        $kalulis[$i] = $explode[1];
                        $kes[$i] = $explode[2];
                        $i++;
                    }
                    $countzhong = count($zhong);

                    $this->assign('countzhong',$countzhong);
                    $this->assign('eats',$eats);
                    $this->assign('wid',$zhongss['id']);
                    $this->assign('kalulis',$kalulis);
                    $this->assign('kes',$kes);
                };
                // print_r($zhongss);exit();
                $this->assign('zhong',$zhongss['foodname']);
                // 加餐
                $jiass = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$afterDay,'type'=>'jia'))->find();
                if (!empty($zhongss['foodname'])) {
                    $jia = json_decode($jiass['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($jia as $key => $value) {
                        $explode = explode(",", $value);
                        $eatss[$i] = $explode[0];
                        $kaluliss[$i] = $explode[1];
                        $kess[$i] = $explode[2];
                        $i++;
                    }
                    $countjia = count($jia);

                    $this->assign('countjia',$countjia);
                    $this->assign('eatss',$eatss);

                    $this->assign('jid',$jiass['id']);

                    $this->assign('kaluliss',$kaluliss);
                    $this->assign('kess',$kess);
                };
                $this->assign('jia',$jiass['foodname']);
                // print_r($jiass);exit();
                // 晚餐
                $wanss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>$afterDay,'type'=>'wan'))->find();
                if (!empty($wanss['foodname'])) {
                    $wan = json_decode($wanss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($wan as $key => $value) {
                        $explode = explode(",", $value);
                        $eatsss[$i] = $explode[0];
                        $kalulisss[$i] = $explode[1];
                        $kesss[$i] = $explode[2];
                        $i++;
                    }
                    $countwan = count($wan);

                    $this->assign('countwan',$countwan);
                    $this->assign('eatsss',$eatsss);

                    $this->assign('did',$wanss['id']);
                    $this->assign('kalulisss',$kalulisss);
                    $this->assign('kesss',$kesss);
                };
                $this->assign('wan',$wanss['foodname']);
                // 下面是当前天数的
            }else{
                $diffentz = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'zao','openid'=>$this->openid))->find();
                $diffentzh = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'zhong','openid'=>$this->openid))->find();
                $diffentw = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'wan','openid'=>$this->openid))->find();
                $diffentj = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'jia','openid'=>$this->openid))->find();
                // 运动
                $diffenty = M('Reduce_energy')->where(array('addtime'=>$date,'type'=>'yun','openid'=>$this->openid))->find();
                // print_r($diffenty);exit();
                $allcalorie = $diffentz['caluli'] + $diffentzh['caluli'] + $diffentw['caluli'] + $diffentj['caluli'];
                $allke = $diffentz['ke'] + $diffentzh['ke'] + $diffentw['ke'] + $diffentj['ke'];
                // 分配运动的克数以及卡路里
                $allsportke = $diffenty['ke'];
                $allsportcalorie = $diffenty['caluli'];

                $this->assign('allcalorie',$allcalorie);
                $this->assign('allke',$allke);

                $this->assign('allsportke',$allsportke);
                $this->assign('allsportcalorie',$allsportcalorie);
                // print_r($allcalorie);exit();
                // 运动
                if (!empty($diffenty)) {
                    $diffentys = json_decode($diffenty['foodname'],true);
                    foreach ($diffentys as $key => $value) {
                        $yundong = explode(",", $value);
                        $yunfood[$key] = $yundong[0];
                        $yuncalorie[$key] = $yundong[1];
                        $yunke[$key] = $yundong[2];
                    }
                    $county = count($diffentys);
                    $this->assign('county',$county);
                    $this->assign('yunfood',$yunfood);
                    $this->assign('yuncalorie',$yuncalorie);
                    $this->assign('yunke',$yunke);
                    // print_r($diffentys);exit();
                }
                // 早餐
                if (!empty($diffentz)) {
                    $diffentzs = json_decode($diffentz['foodname'],true);
                    foreach ($diffentzs as $key => $value) {
                        $zaocan = explode(",", $value);
                        $zaofood[$key] = $zaocan[0];
                        $zaocalorie[$key] = $zaocan[1];
                        $zaoke[$key] = $zaocan[2];
                    }
                    $countz = count($diffentzs);
                    $this->assign('countz',$countz);
                    $this->assign('zaofood',$zaofood);
                    $this->assign('zaocalorie',$zaocalorie);
                    $this->assign('zaoke',$zaoke);
                }
                // 午餐
                if (!empty($diffentzh)) {
                    $diffentzhs = json_decode($diffentzh['foodname'],true);
                    foreach ($diffentzhs as $key => $value) {
                        $zhongcan = explode(",", $value);
                        $zhongfood[$key] = $zhongcan[0];
                        $zhongcalorie[$key] = $zhongcan[1];
                        $zhongke[$key] = $zhongcan[2];
                    }
                    $countzh = count($diffentzhs);
                    $this->assign('countzh',$countzh);
                    $this->assign('zhongfood',$zhongfood);
                    $this->assign('zhongcalorie',$zhongcalorie);
                    $this->assign('zhongke',$zhongke);
                    // print_r($zhongcalorie);exit();
                }
                // 晚餐
                if (!empty($diffentw)) {
                    $diffentws = json_decode($diffentw['foodname'],true);
                    foreach ($diffentws as $key => $value) {
                        $wancan = explode(",", $value);
                        $wanfood[$key] = $wancan[0];
                        $wancalorie[$key] = $wancan[1];
                        $wanke[$key] = $wancan[2];
                    }
                    $countw = count($diffentws);

                    $this->assign('countw',$countw);
                    $this->assign('wanfood',$wanfood);
                    $this->assign('wancalorie',$wancalorie);
                    $this->assign('wanke',$wanke);
                }
                // 加餐

                if (!empty($diffentj)) {
                    $diffentjs = json_decode($diffentj['foodname'],true);
                    foreach ($diffentjs as $key => $value) {
                        $jiacan = explode(",", $value);
                        $jiafood[$key] = $jiacan[0];
                        $jiacalorie[$key] = $jiacan[1];
                        $jiake[$key] = $jiacan[2];
                    }
                    $countj = count($diffentjs);
                    $this->assign('countj',$countj);
                    $this->assign('jiafood',$jiafood);
                    $this->assign('jiacalorie',$jiacalorie);
                    $this->assign('jiake',$jiake);

                }
                $this->assign('choicedate',100);
                $this->assign('diffentz',$diffentz);
                $this->assign('datess',$date);
                $this->assign('diffentzh',$diffentzh);
                $this->assign('diffentw',$diffentw);
                $this->assign('diffentj',$diffentj);
                // 分配运动模板数据
                $this->assign('diffenty',$diffenty);
                // 把时间分配到前天前台去判断
                $this->assign('currentime',$date);
                // 运动
                $yunyun = M('Reduce_energy')->where(array('addtime'=>date("Y-m-d"),'openid'=>$this->openid,'token'=>$this->token,'type'=>'yun'))->find();

                if (!empty($yunyun['foodname'])) {
                    $yun = json_decode($yunyun['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($yun as $key => $value) {
                        $explode = explode(",", $value);
                        $eatyun[$i] = $explode[0];
                        $kaluliyun[$i] = $explode[1];
                        $keyun[$i] = $explode[2];
                        $i++;
                    }
                    $countyun = count($yun);
                    // echo $countzao;exit();
                    $this->assign('countyun',$countyun);
                    $this->assign('eatyun',$eatyun);
                    // print_r($eatyun);exit();
                    $this->assign('yid',$yunyun['id']);
                    $this->assign('kaluliyun',$kaluliyun);
                    $this->assign('keyun',$keyun);
                    // print_r(date("Y-m-d"));exit();
                };
                // print_r($yunyun);exit();
                $this->assign('zao',$zaozao['foodname']);
                // 早餐
                $zaozao = M('Reduce_energy')->where(array('addtime'=>date("Y-m-d"),'openid'=>$this->openid,'token'=>$this->token,'type'=>'zao'))->find();

                if (!empty($zaozao['foodname'])) {
                    $zao = json_decode($zaozao['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zao as $key => $value) {
                        $explode = explode(",", $value);
                        $eat[$i] = $explode[0];
                        $kaluli[$i] = $explode[1];
                        $ke[$i] = $explode[2];
                        $i++;
                    }
                    $countzao = count($zao);
                    // echo $countzao;exit();
                    $this->assign('countzao',$countzao);
                    $this->assign('eat',$eat);
                    $this->assign('zid',$zaozao['id']);
                    $this->assign('kaluli',$kaluli);
                    $this->assign('ke',$ke);

                };
                $this->assign('zao',$zaozao['foodname']);
                // 午餐
                $zhongss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'zhong'))->find();
                if (!empty($zhongss['foodname'])) {
                    $zhong = json_decode($zhongss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zhong as $key => $value) {
                        $explode = explode(",", $value);
                        $eats[$i] = $explode[0];
                        $kalulis[$i] = $explode[1];
                        $kes[$i] = $explode[2];
                        $i++;
                    }
                    $countzhong = count($zhong);

                    $this->assign('countzhong',$countzhong);
                    $this->assign('eats',$eats);
                    $this->assign('wid',$zhongss['id']);
                    $this->assign('kalulis',$kalulis);
                    $this->assign('kes',$kes);
                };
                // print_r($zhongss);exit();
                $this->assign('zhong',$zhongss['foodname']);
                // 加餐
                $jiass = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'jia'))->find();
                if (!empty($zhongss['foodname'])) {
                    $jia = json_decode($jiass['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($jia as $key => $value) {
                        $explode = explode(",", $value);
                        $eatss[$i] = $explode[0];
                        $kaluliss[$i] = $explode[1];
                        $kess[$i] = $explode[2];
                        $i++;
                    }
                    $countjia = count($jia);

                    $this->assign('countjia',$countjia);
                    $this->assign('eatss',$eatss);

                    $this->assign('jid',$jiass['id']);

                    $this->assign('kaluliss',$kaluliss);
                    $this->assign('kess',$kess);
                };
                $this->assign('jia',$jiass['foodname']);
                // print_r($jiass);exit();
                // 晚餐
                $wanss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'wan'))->find();
                if (!empty($wanss['foodname'])) {
                    $wan = json_decode($wanss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($wan as $key => $value) {
                        $explode = explode(",", $value);
                        $eatsss[$i] = $explode[0];
                        $kalulisss[$i] = $explode[1];
                        $kesss[$i] = $explode[2];
                        $i++;
                    }
                    $countwan = count($wan);

                    $this->assign('countwan',$countwan);
                    $this->assign('eatsss',$eatsss);

                    $this->assign('did',$wanss['id']);
                    $this->assign('kalulisss',$kalulisss);
                    $this->assign('kesss',$kesss);
                };
                $this->assign('wan',$wanss['foodname']);
            }
                $this->display();
            }else{
                if(M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d")))->select()){
                $result = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d")))->select();
                $count = count($result)-1;
                $num = 0;
                $num1 = 0;
                for ($i=0; $i < $count; $i++) {
                    $num += $result[$i]['ke'];
                    $num1 += $result[$i]['caluli'];
                };
                // 运动时间以及卡路里分配
                $resultsport = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'yun'))->find();
                $this->assign('sportke',$resultsport['ke']);
                $this->assign('sportcaluri',$resultsport['caluli']);

                $this->assign('num',$num);
                $this->assign('num1',$num1);
                $this->assign('result',$result);
                $this->assign('flags',1);
                $this->assign('dates',date("Y-m-d"));

                 // 运动
                $yunyun = M('Reduce_energy')->where(array('addtime'=>date("Y-m-d"),'openid'=>$this->openid,'token'=>$this->token,'type'=>'yun'))->find();

               if (!empty($yunyun['foodname'])) {
                    $diffentys = json_decode($yunyun['foodname'],true);
                    foreach ($diffentys as $key => $value) {
                        $yundong = explode(",", $value);
                        $yunfood[$key] = $yundong[0];
                        $yuncalorie[$key] = $yundong[1];
                        $yunke[$key] = $yundong[2];
                    }
                    $countyun = count($diffentys);

                    // 运动的总的数量
                    $this->assign('countyun',$countyun);
                    $this->assign('yunfood',$yunfood);
                    $this->assign('yuncalorie',$yuncalorie);
                    $this->assign('yunke',$yunke);
                    $this->assign('yid',$yunyun['id']);
                }
               
                // 早餐
                $zaozao = M('Reduce_energy')->where(array('addtime'=>date("Y-m-d"),'openid'=>$this->openid,'token'=>$this->token,'type'=>'zao'))->find();

                if (!empty($zaozao['foodname'])) {
                    $zao = json_decode($zaozao['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zao as $key => $value) {
                        $explode = explode(",", $value);
                        $eat[$i] = $explode[0];
                        $kaluli[$i] = $explode[1];
                        $ke[$i] = $explode[2];
                        $i++;
                    }
                    $countzao = count($zao);
                    // echo $countzao;exit();
                    $this->assign('countzao',$countzao);
                    $this->assign('eat',$eat);
                    $this->assign('zid',$zaozao['id']);
                    $this->assign('kaluli',$kaluli);
                    $this->assign('ke',$ke);
                };
                // print_r($countzao);exit();
                $this->assign('zao',$zaozao['foodname']);
                // 午餐
                $zhongss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'zhong'))->find();
                if (!empty($zhongss['foodname'])) {
                    $zhong = json_decode($zhongss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($zhong as $key => $value) {
                        $explode = explode(",", $value);
                        $eats[$i] = $explode[0];
                        $kalulis[$i] = $explode[1];
                        $kes[$i] = $explode[2];
                        $i++;
                    }
                    $countzhong = count($zhong);

                    $this->assign('countzhong',$countzhong);
                    $this->assign('eats',$eats);
                    $this->assign('wid',$zhongss['id']);
                    $this->assign('kalulis',$kalulis);
                    $this->assign('kes',$kes);
                };
                // print_r($zhongss);exit();
                $this->assign('zhong',$zhongss['foodname']);
                // 加餐
                $jiass = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'jia'))->find();
                if (!empty($zhongss['foodname'])) {
                    $jia = json_decode($jiass['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($jia as $key => $value) {
                        $explode = explode(",", $value);
                        $eatss[$i] = $explode[0];
                        $kaluliss[$i] = $explode[1];
                        $kess[$i] = $explode[2];
                        $i++;
                    }
                    $countjia = count($jia);

                    $this->assign('countjia',$countjia);
                    $this->assign('eatss',$eatss);

                    $this->assign('jid',$jiass['id']);

                    $this->assign('kaluliss',$kaluliss);
                    $this->assign('kess',$kess);
                };
                $this->assign('jia',$jiass['foodname']);
                // print_r($jiass);exit();
                // 晚餐
                $wanss = M('Reduce_energy')->where(array('openid'=>$this->openid,'token'=>$this->token,'addtime'=>date("Y-m-d"),'type'=>'wan'))->find();
                if (!empty($wanss['foodname'])) {
                    $wan = json_decode($wanss['foodname'],true);
                    // 名称
                    $i = 0;
                    foreach ($wan as $key => $value) {
                        $explode = explode(",", $value);
                        $eatsss[$i] = $explode[0];
                        $kalulisss[$i] = $explode[1];
                        $kesss[$i] = $explode[2];
                        $i++;
                    }
                    $countwan = count($wan);

                    $this->assign('countwan',$countwan);
                    $this->assign('eatsss',$eatsss);

                    $this->assign('did',$wanss['id']);
                    $this->assign('kalulisss',$kalulisss);
                    $this->assign('kesss',$kesss);
                };
                $this->assign('wan',$wanss['foodname']);
                // 把时间分配到前天前台去判断
                $this->assign('currentime',date("Y-m-d"));
                $this->display();
            }else{
                 // 把时间分配到前天前台去判断
                $this->assign('currentime',date("Y-m-d"));
                $this->display();
            }
            }
            
        }

    }
    // 从时间的地方取得数据并且返回，数据查询
    public function dataSearch(){
        $time = $_POST['begintime'];
        $alldata = "";
        $result = M('Reduce_energy')->where(array('addtime'=>$time))->select();
        foreach ($result as $key => $value) {
            if ($value['foodname'] == "") {
                $alldata .= "\"".$value['type']."\":{\"caluli\":\"".$value['caluli']."\",\"ke\":\"".$value['ke']."\",\"foodname\":\"\"},";
            }else{
                $alldata .= "\"".$value['type']."\":{\"caluli\":\"".$value['caluli']."\",\"ke\":\"".$value['ke']."\",\"foodname\":".$value['foodname']."},";
            }
        }
        $alldata = "{".$alldata."}";
        $alldata = preg_replace('/,}/', '}', $alldata);
        echo $alldata;
        // print_r($time);
    }
    // 食物方案的删除
    public function fooddel(){
        // 删除早餐
        if (isset($_GET['zid'])) {
            $id = $_GET['zid'];
            // 获取日期
            $date = $_GET['time'];
            
            $results = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($results['foodname'],true);
            
            $i = 0;
            $break = "";
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    echo $results['caluli'];
                    $newcaluli = $results['caluli'] - $values[1];
                    $newke = $results['ke'] - $values[2];
                    continue;
                }
                $break .= "\"".$i."\":\"".$value."\",";
                $i++;
            }

            if (empty($break)) {
                $break = "";
                $data = array(
                    'foodname' => $break,
                    'caluli' => 0,
                    'ke' => 0
                );
            }else{
                $break .= "}";
                $break = preg_replace('/,}/', '}', $break);
                $break = "{".$break;

                $data = array(
                    'foodname' => $break,
                    'caluli' => $newcaluli,
                    'ke' => $newke
                );
                
            }

            M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->save($data);
            $this->redirect('Reduce/foodpackage', array('token' => $this->token,'openid'=>$this->openid,'addtime'=>$date));

        }if (isset($_GET['wid'])) {
            
            $id = $_GET['wid'];
            // 获取日期
            $date = $_GET['time'];
            $results = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($results['foodname'],true);

            $i = 0;
            $break = "";
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $newcaluli = $results['caluli'] - $values[1];
                    $newke = $results['ke'] - $values[2];
                    continue;
                }
                $break .= "\"".$i."\":\"".$value."\",";
                $i++;
            }

            if (empty($break)) {
                $break = "";
                $data = array(
                    'foodname' => $break,
                    'caluli' => 0,
                    'ke' => 0
                );
            }else{
                $break .= "}";
                $break = preg_replace('/,}/', '}', $break);
                $break = "{".$break;

                $data = array(
                    'foodname' => $break,
                    'caluli' => $newcaluli,
                    'ke' => $newke
                );

            }

            M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->save($data);
            $this->redirect('Reduce/foodpackage', array('token' => $this->token,'openid'=>$this->openid,'addtime'=>$date));
        }if (isset($_GET['jid'])) {

            $id = $_GET['jid'];
            // 获取日期
            $date = $_GET['time'];
            $results = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($results['foodname'],true);

            $i = 0;
            $break = "";
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $newcaluli = $results['caluli'] - $values[1];
                    $newke = $results['ke'] - $values[2];
                    continue;
                }
                $break .= "\"".$i."\":\"".$value."\",";
                $i++;
            }

            if (empty($break)) {
                $break = "";
                $data = array(
                    'foodname' => $break,
                    'caluli' => 0,
                    'ke' => 0
                );
            }else{
                $break .= "}";
                $break = preg_replace('/,}/', '}', $break);
                $break = "{".$break;

                $data = array(
                    'foodname' => $break,
                    'caluli' => $newcaluli,
                    'ke' => $newke
                );

            }

            M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->save($data);
            $this->redirect('Reduce/foodpackage', array('token' => $this->token,'openid'=>$this->openid,'addtime'=>$date));
        }if (isset($_GET['did'])){
            $id = $_GET['did'];
             // 获取日期
            $date = $_GET['time'];
            $results = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($results['foodname'],true);

            $i = 0;
            $break = "";
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $newcaluli = $results['caluli'] - $values[1];
                    $newke = $results['ke'] - $values[2];
                    continue;
                }
                $break .= "\"".$i."\":\"".$value."\",";
                $i++;
            }

            if (empty($break)) {
                $break = "";
                $data = array(
                    'foodname' => $break,
                    'caluli' => 0,
                    'ke' => 0
                );
            }else{
                $break .= "}";
                $break = preg_replace('/,}/', '}', $break);
                $break = "{".$break;

                $data = array(
                    'foodname' => $break,
                    'caluli' => $newcaluli,
                    'ke' => $newke
                );

            }

            M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->save($data);
            $this->redirect('Reduce/foodpackage', array('token' => $this->token,'openid'=>$this->openid,'addtime'=>$date));
        }
        // 删除运动
        if (isset($_GET['yid'])){
            $id = $_GET['yid'];
             // 获取日期
            $date = $_GET['time'];
            $results = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($results['foodname'],true);

            $i = 0;
            $break = "";
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $newcaluli = $results['caluli'] - $values[1];
                    $newke = $results['ke'] - $values[2];
                    continue;
                }
                $break .= "\"".$i."\":\"".$value."\",";
                $i++;
            }

            if (empty($break)) {
                $break = "";
                $data = array(
                    'foodname' => $break,
                    'caluli' => 0,
                    'ke' => 0
                );
            }else{
                $break .= "}";
                $break = preg_replace('/,}/', '}', $break);
                $break = "{".$break;

                $data = array(
                    'foodname' => $break,
                    'caluli' => $newcaluli,
                    'ke' => $newke
                );

            }

            M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->save($data);
            $this->redirect('Reduce/foodpackage', array('token' => $this->token,'openid'=>$this->openid,'addtime'=>$date));
        }
    }


    // 搜索框搜索后的结果
    public function foodSearch(){
        // 如果接受的是运动数据的话
        if ($_GET['data'] == "yun") {
            $db = M('Reduce_sport_detail');
            $foodName['sport_name'] = array('like','%'.$_POST['findFood'].'%');
            $result = $db->field('*,LENGTH(sport_name) as sport_length')->where($foodName)->order('sport_length asc')->select();
            $i = 0;
            foreach ($result as $key => $value) {
                if (preg_match("/[(|（].*".$_POST['findFood']."/",$value['sport_name'])) {
                    unset($result[$key]);
                }else{
                    $resultid[$i] = $value['id'];
                    $i++;
                }

            };

            // 运动名称
            $i = 0;
            foreach ($result as $key => $value) {
                if (preg_match("/[(|（].*".$_POST['findFood']."/",$value['sport_name'])) {
                    unset($result[$key]);
                }else{
                    $resultname[$i] = $value['sport_name'];
                    $i++;
                }

            };

            $arrayCombine = array_combine($resultid, $resultname);
            //$string = $this->encode($arrayCombine);
            $string = '';
            foreach ($arrayCombine as $key => $value) {
                $string .= "<a href='/index.php?g=Wap&m=Reduce&a=addfoods&token=".$this->token."&openid=".$this->openid."&id=".$key."&data=".$_GET['data']."&date=".$_GET['date']."' style='color:#666'><li><span>".$value."</span><imgs width='8px' height='15px' src='http://v.wapwei.com/./tpl/static/wapweiui/Reduce/imgs/rightArrow.png'></imgs></li></a>";
            }
            echo $string;

        }else{
            $db = M('Reduce_food_component_detail');
            $foodName['food_name'] = array('like','%'.$_POST['findFood'].'%');
            $result = $db->field('*,LENGTH(food_name) as food_length')->where($foodName)->order('food_length asc')->select();

        // 根据食物的长短来排序
        // echo $db->getLastSql();exit;
        // 食物id

        $i = 0;
        foreach ($result as $key => $value) {
            if (preg_match("/[(|（].*".$_POST['findFood']."/",$value['food_name'])) {
                unset($result[$key]);
            }else{
                $resultid[$i] = $value['id'];
                $i++;
            }

                };

                // 食物名称
                $i = 0;
                foreach ($result as $key => $value) {
                    if (preg_match("/[(|（].*".$_POST['findFood']."/",$value['food_name'])) {
                        unset($result[$key]);
                    }else{
                        $resultname[$i] = $value['food_name'];
                        $i++;
                    }

        };

                $arrayCombine = array_combine($resultid, $resultname);
                //$string = $this->encode($arrayCombine);
                $string = '';
                foreach ($arrayCombine as $key => $value) {
                    $string .= "<a href='/index.php?g=Wap&m=Reduce&a=addfoods&token=".$this->token."&openid=".$this->openid."&id=".$key."&data=".$_GET['data']."&date=".$_GET['date']."' style='color:#666'><li><span>".$value."</span><imgs width='8px' height='15px' src='http://v.wapwei.com/./tpl/static/wapweiui/Reduce/imgs/rightArrow.png'></imgs></li></a>";
                }
                echo $string;
            }
        
    }
    // 添加食物页面以及修改食物的克数
    public function addfoods(){
        if (isset($_GET['zid'])) {
            // 携带不同的参数去判断还是修改
            $id = $_GET['zid'];
            // 获取日期
            $date = $_GET['time'];
            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($result['foodname'],true);


            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $getResult = M('Reduce_food_component_detail')->where(array('food_name'=>$_GET['name']))->find();
                    $this->assign('values',$values[2]);
                    break;
                }
            }
            $this->assign('types',1);
            $this->assign('zid',$_GET['zid']);
            $this->assign('time',$_GET['time']);
            $this->assign('getResult',$getResult);
            $this->display();

        }elseif (isset($_GET['wid'])) {

            // 携带不同的参数去判断还是修改
            $id = $_GET['wid'];
            // 获取日期
            $date = $_GET['time'];
            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($result['foodname'],true);


            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $getResults = M('Reduce_food_component_detail')->where(array('food_name'=>$_GET['name']))->find();
                    $this->assign('valuess',$values[2]);
                    break;
                }
            }
            $this->assign('types',2);
            $this->assign('wid',$_GET['wid']);
            $this->assign('time',$_GET['time']);
            $this->assign('getResults',$getResults);
            $this->display();
        }elseif (isset($_GET['jid'])) {
            // 携带不同的参数去判断还是修改
            $id = $_GET['jid'];
             // 获取日期
            $date = $_GET['time'];
            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($result['foodname'],true);


            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $getResults = M('Reduce_food_component_detail')->where(array('food_name'=>$_GET['name']))->find();
                    $this->assign('valuesss',$values[2]);
                    break;
                }
            }
            $this->assign('types',3);
            $this->assign('getResultss',$getResults);
            $this->assign('time',$_GET['time']);
            $this->assign('jid',$_GET['jid']);
            $this->display();
        }elseif (isset($_GET['did'])) {
            // 携带不同的参数去判断还是修改
            $id = $_GET['did'];
             // 获取日期
            $date = $_GET['time'];
            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($result['foodname'],true);


            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $getResults = M('Reduce_food_component_detail')->where(array('food_name'=>$_GET['name']))->find();
                    $this->assign('valuessss',$values[2]);
                    break;
                }
            }
            $this->assign('types',4);
            $this->assign('getResultsss',$getResults);
            $this->assign('did',$_GET['did']);
            $this->assign('time',$_GET['time']);
            $this->display();
        }
        // 修改运动显示页面
        elseif (isset($_GET['yid'])) {
            $id = $_GET['yid'];
             // 获取日期
            $date = $_GET['time'];
            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date))->find();

            $result = json_decode($result['foodname'],true);


            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_GET['name']) {
                    $getResults = M('Reduce_sport_detail')->where(array('sport_name'=>$_GET['name']))->find();
                    $values[2] = str_replace('g', '', $values[2]);
                    $this->assign('vsport',$values[2]);
                    break;
                }
            }
            
            $this->assign('types',5);
            $this->assign('getResultsss',$getResults);
            $this->assign('time',$_GET['time']);
            $this->assign('yid',$_GET['yid']);
            $this->display();
        }
        // 分配运动数据到模板
        else{
            // 根据不同的去修改
            $id = $_GET['id'];
            // 获取不同的参数
            $know = $_GET['data'];
            // 获取不同的日期
            $date = $_GET['date'];
            if ($know == "yun") {
                $db = M('Reduce_sport_detail');
                $result = $db->where(array('id'=>$id))->find();
                $this->assign('result',$result);
                $this->assign('know',$know); 
                $this->assign('date',$date); 
            }else{
                $db = M('Reduce_food_component_detail');
                $result = $db->where(array('id'=>$id))->find();
                $this->assign('result',$result);
                $this->assign('know',$know);
                $this->assign('date',$date); 
            }
            $this->display();
        }

    }
    // 添加食物接收数据页面
    public function addappect(){
        // 修改
        if ($_POST['type'] == 1) {
            $id = $_POST['zid'];
            // 添加时间
            $date = $_POST['time'];
            $results = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->find();

            $result = json_decode($results['foodname'],true);
            $i = 0;
            $break = "";

            $totalcalorie = 0;

            $totalke = 0;
            $weight = str_replace('g', '', $_POST['weight']);
            foreach ($result as $key => $value) {
                $values = explode(",", $value);

                if ($values[0] == $_POST['name']) {
                    $foodcalorie = M('Reduce_food_component_detail')->where(array('food_name'=>$_POST['name']))->find();
                    $values[1] = number_format($weight * $foodcalorie['calorie']/$foodcalorie['weight'],1);
                    $break .= "\"".$i."\":\"".$values[0].",".$values[1].",".$weight."\",";
                    $totalcalorie += $values[1];
                    $totalke += $weight;
                }else{
                    $break .= "\"".$i."\":\"".$value."\",";
                    $totalcalorie += $values[1];
                    $totalke += $values[2];
                }
                $i++;
            }
            // echo $totalke;exit();


            $break .= "}";
            $break = preg_replace('/,}/', '}', $break);
            $break = "{".$break;

            $data = array(
                'foodname' => $break,
                'caluli' => $totalcalorie,
                'ke' => $totalke
            );

            if(M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->save($data)){
                $this->success("保存成功",U(MODULE_NAME.'/foodpackage',array('token'=>$this->token,'openid'=>$this->openid,'addtime'=>$date)));
            }else{
                $this->error("保存失败",U(MODULE_NAME.'/foodpackage'));
            };

        }elseif ($_POST['type'] == 2) {
            $id = $_POST['wid'];
            // 添加时间
            $date = $_POST['time'];

            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->find();
            $result = json_decode($result['foodname'],true);
            $i = 0;
            $break = "";
            // 统计卡路里
            $totalcalorie = 0;
            // 统计克数
            $totalke = 0;
            $weight = str_replace('g', '', $_POST['weight']);
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_POST['name']) {
                    $foodcalorie = M('Reduce_food_component_detail')->where(array('food_name'=>$_POST['name']))->find();

                    $values[1] = number_format($weight * $foodcalorie['calorie']/$foodcalorie['weight'],1);
                    $break .= "\"".$i."\":\"".$values[0].",".$values[1].",".$weight."\",";
                    $totalcalorie += $values[1];
                    $totalke += $weight;
                }else{
                    $break .= "\"".$i."\":\"".$value."\",";
                    $totalcalorie += $values[1];
                    $totalke += $values[2];
                }
                $i++;
            }


            $break .= "}";
            $break = preg_replace('/,}/', '}', $break);
            $break = "{".$break;

            $data = array(
                'foodname' => $break,
                'caluli' => $totalcalorie,
                'ke' => $totalke
            );

            if(M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->save($data)){
                $this->success("保存成功",U(MODULE_NAME.'/foodpackage',array('token'=>$this->token,'openid'=>$this->openid,'addtime'=>$date)));
            }else{
                $this->error("保存失败",U(MODULE_NAME.'/foodpackage'));
            }
        }elseif ($_POST['type'] == 3) {
            $id = $_POST['jid'];
             // 添加时间
            $date = $_POST['time'];
            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->find();
            $result = json_decode($result['foodname'],true);
            $i = 0;
            $break = "";
            // 统计卡路里
            $totalcalorie = 0;
            // 统计克数
            $totalke = 0;
            $weight = str_replace('g', '', $_POST['weight']);
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_POST['name']) {
                    $foodcalorie = M('Reduce_food_component_detail')->where(array('food_name'=>$_POST['name']))->find();

                    $values[1] = number_format($weight * $foodcalorie['calorie']/$foodcalorie['weight'],1);
                    $break .= "\"".$i."\":\"".$values[0].",".$values[1].",".$weight."\",";
                    $totalcalorie += $values[1];
                    $totalke += $weight;
                }else{
                    $break .= "\"".$i."\":\"".$value."\",";
                    $totalcalorie += $values[1];
                    $totalke += $values[2];
                }
                $i++;
            }


            $break .= "}";
            $break = preg_replace('/,}/', '}', $break);
            $break = "{".$break;

            $data = array(
                'foodname' => $break,
                'caluli' => $totalcalorie,
                'ke' => $totalke
            );

            if(M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->save($data)){
                $this->success("保存成功",U(MODULE_NAME.'/foodpackage',array('token'=>$this->token,'openid'=>$this->openid,'addtime'=>$date)));
            }else{
                $this->error("保存失败",U(MODULE_NAME.'/foodpackage'));
            }
        }elseif ($_POST['type'] == 4) {
            $id = $_POST['did'];
            // 添加时间
            $date = $_POST['time'];
            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->find();
            $result = json_decode($result['foodname'],true);
            $i = 0;
            $break = "";
            // 统计卡路里
            $totalcalorie = 0;
            // 统计克数
            $totalke = 0;
            $weight = str_replace('g', '', $_POST['weight']);
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_POST['name']) {
                    $foodcalorie = M('Reduce_food_component_detail')->where(array('food_name'=>$_POST['name']))->find();

                    $values[1] = number_format($weight * $foodcalorie['calorie']/$foodcalorie['weight'],1);
                    $break .= "\"".$i."\":\"".$values[0].",".$values[1].",".$weight."\",";
                    $totalcalorie += $values[1];
                    $totalke += $weight;
                }else{
                    $break .= "\"".$i."\":\"".$value."\",";
                    $totalcalorie += $values[1];
                    $totalke += $values[2];
                }
                $i++;
            }


            $break .= "}";
            $break = preg_replace('/,}/', '}', $break);
            $break = "{".$break;

            $data = array(
                'foodname' => $break,
                'caluli' => $totalcalorie,
                'ke' => $totalke
            );

            if(M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->save($data)){
                $this->success("保存成功",U(MODULE_NAME.'/foodpackage',array('token'=>$this->token,'openid'=>$this->openid,'addtime'=>$date)));
            }else{
                $this->error("保存失败",U(MODULE_NAME.'/foodpackage'));
            }
        }//修改运动数据
        elseif ($_POST['type'] == 5) {
            // 添加时间
            $date = $_POST['time'];
            // echo $_POST['name'];exit();
            $id = $_POST['yid'];
            // echo $id;exit();
            $result = M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->find();
            $result = json_decode($result['foodname'],true);
            $i = 0;
            $break = "";
            // 统计卡路里
            $totalcalorie = 0;
            // 统计克数
            $totalke = 0;
            // 获取到的时间
            $weight = str_replace('min', '', $_POST['weight']);
            foreach ($result as $key => $value) {
                $values = explode(",", $value);
                if ($values[0] == $_POST['name']) {
                    $foodcalorie = M('Reduce_sport_detail')->where(array('sport_name'=>$_POST['name']))->find();

                    $values[1] = number_format(intval($weight) * $foodcalorie['calorie']/60,1);
                    $break .= "\"".$i."\":\"".$values[0].",".$values[1].",".$weight."\",";
                    $totalcalorie += $values[1];
                    $totalke += $weight;

                }else{
                    $break .= "\"".$i."\":\"".$value."\",";
                    $totalcalorie += $values[1];
                    $totalke += $values[2];
                }
                $i++;
            }

            // print_r($values[0]);exit();
            $break .= "}";
            $break = preg_replace('/,}/', '}', $break);
            $break = "{".$break;

            $data = array(
                'foodname' => $break,
                'caluli' => $totalcalorie,
                'ke' => $totalke
            );
            if(M('Reduce_energy')->where(array('id'=>$id,'addtime'=>$date,'openid'=>$this->openid))->save($data)){
                $this->success("保存成功",U(MODULE_NAME.'/foodpackage',array('token'=>$this->token,'openid'=>$this->openid,'addtime'=>$date)));
            }else{
                $this->error("保存失败",U(MODULE_NAME.'/foodpackage'));
            }
        }//新增
        else{
            //获取的时间或者克数
            $weight = $_POST['weight'];

            // 100g某种事物的卡路里
            $calorie = $_POST['calorie'];
            // 获取不同类型的数据
            $know = $_POST['diff'];
            // 根据不同的日期来添加数据
            $date = $_POST['date'];
            // 获取食物名称或者运动名称
            $foodnames = $_POST['foodnames'];
            $results = M('Reduce_energy')->where(array('type'=>$know,'addtime'=>$date,'openid'=>$this->openid))->find();
            
	       $st = $weight; 
            $calorie = number_format($st/100*$calorie,1);
            $ke = $results['ke'] + $st;
            $kariru = $results['caluli'] + $calorie;

             // 往里面插入一条运动数据
            if ($_POST['diff'] == 'yun') {
                // 分钟数量
                
                // 寻找卡路里
                $findCalorie = M('Reduce_sport_detail')->where(array('sport_name'=>$foodnames))->find();
                $weight = $_POST['weight'];
                $calorie = $_POST['calorie'];
                // 运动时间的获取
                $st = str_replace('min', '', $weight);
                $calorie = number_format($st/60*$findCalorie['calorie'],1);
                // 在之前的时间的基础上添加
                $ke = $results['ke'] + $st;
                // 在之前的卡路里的基础上添加
                $kariru = $results['caluli'] + $calorie;

                 if (M('Reduce_energy')->where(array('type'=>$_POST['diff'],'addtime'=>$date,'openid'=>$this->openid))->find()) {
                    $eneygy = M('Reduce_energy')->where(array('type'=>$_POST['diff'],'addtime'=>$date,'openid'=>$this->openid))->find();
                    if (!empty($eneygy['foodname'])) {
                        // 在存在的情况下去添加数据
                        $eneygy_food = json_decode($eneygy['foodname'],true);
                        $i = 0;
                        foreach ($eneygy_food as $key => $value) {
                            $i++;
                        };
                        $eneygy_food = preg_replace("/}/", ",", $eneygy['foodname']);
                        $eneygy_food = $eneygy_food."\"".$i."\":\"".$foodnames.",".$calorie.",".$st."\"}";
                    }else{
                        $eneygy_food = "{\"0\":\"".$foodnames.",".$calorie.",".$st."\"}";
                    }
                };

                 $data = array(
                    'caluli' => $kariru,
                    'ke' => $ke,
                    // 'openid' => $this->openid,
                    'token' => $this->token,
                    'addtime' => $date,
                    'foodname' => $eneygy_food
                );
                $result = M('Reduce_energy')->where(array('type'=>$_POST['diff'],'addtime'=>$date,'openid'=>$this->openid))->save($data);
                if ($result) {
                    $this->success("提交成功",U(MODULE_NAME.'/foodpackage',array('token'=>$this->token,'openid'=>$this->openid,'addtime'=>$date)));
                }else{
                    $this->error("提交失败",U(MODULE_NAME.'/addappect'));
                }
            }//如果不是运动的情况，添加食物
            else{
                
                 if (M('Reduce_energy')->where(array('type'=>$know,'addtime'=>$date,'openid'=>$this->openid))->find()) {
                    $eneygy = M('Reduce_energy')->where(array('type'=>$know,'addtime'=>$date,'openid'=>$this->openid))->find();
                    if (!empty($eneygy['foodname'])) {
                        // 在存在的情况下去添加数据
                        $eneygy_food = json_decode($eneygy['foodname'],true);
                        $i = 0;
                        foreach ($eneygy_food as $key => $value) {
                            $i++;
                        };
                        $eneygy_food = preg_replace("/}/", ",", $eneygy['foodname']);
                        $eneygy_food = $eneygy_food."\"".$i."\":\"".$foodnames.",".$calorie.",".$st."\"}";
                    }else{
                        $eneygy_food = "{\"0\":\"".$foodnames.",".$calorie.",".$st."\"}";
                    }
                }
                 $data = array(
                    'caluli' => $kariru,
                    'ke' => $ke,
                    // 'openid' => $this->openid,
                    'token' => $this->token,
                    'addtime' => $date,
                    'foodname' => $eneygy_food
                );
                $result = M('Reduce_energy')->where(array('type'=>$know,'addtime'=>$date,'openid'=>$this->openid))->save($data);
                if ($result) {
                    $this->success("提交成功",U(MODULE_NAME.'/foodpackage',array('token'=>$this->token,'openid'=>$this->openid,'addtime'=>$date)));
                }else{
                    $this->error("提交失败",U(MODULE_NAME.'/addappect'));
                }
            }
        }


    }
    // 添加运动方案页面
    public function runpackage(){
        if (M('Reduce_runs')->where(array('openid'=>$this->openid,'token'=>$this->token,'date'=>date("m-d")))->find()) {
            $result = M('Reduce_runs')->where(array('openid'=>$this->openid,'token'=>$this->token,'date'=>date("m-d")))->find();
            $this->assign('result',$result);
            $this->assign('flag',1);
            $this->display();
        }else{
            $this->display();
        }

    }
    // 运动方案页面接收数据
    public function runSearch(){
        $db = M('Reduce_sport_detail');
        $foodrun['sport_name'] = array('like','%'.$_POST['findRun'].'%');
        $result = $db->where($foodrun)->select();
        // 食物id
        $i = 0;
        foreach ($result as $key => $value) {
            $resultid[$i] = $value['id'];
            $i++;
        };

        // 食物名称
        $i = 0;
        foreach ($result as $key => $value) {
            $resultname[$i] = $value['sport_name'];
            $i++;
        };

        $arrayCombine = array_combine($resultid, $resultname);
        $string = json_encode($arrayCombine);
        echo $string;
    }
    // 添加运动
    public function addruns(){
        $id = $_GET['id'];
        // 获取不同的参数
        $know = $_GET['data'];
        // echo $know;exit();
        $db = M('Reduce_sport_detail');
        $result = $db->where(array('id'=>$id))->find();
        $this->assign('result',$result);
        $this->assign('know',$know);
        $this->assign('date',date("Y-m-d"));
        $this->display();
    }
    // 添加运动数据接收
    public function addappectRun(){
        $runtime = $_POST['runtime'];
        $sportname = $_POST['sportname'];
        if (M('Reduce_runs')->where(array('openid'=>$this->openid,'token'=>$this->token,'date'=>date("m-d")))->find()) {
            $data = array(
                'sportsname' => $sportname,
                'sportslong' => $runtime,
            );
            if (M('Reduce_runs')->where(array('date'=>date("m-d")))->save($data)) {
                $this->success("提交成功",U(MODULE_NAME.'/runpackage',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("提交失败",U(MODULE_NAME.'/addruns'));
            }
        }else{
            $data = array(
                'sportsname' => $sportname,
                'sportslong' => $runtime,
                'date' => date("m-d"),
                'token' => $this->token,
                'openid' => $this->openid
            );
            if (M('Reduce_runs')->data($data)->add()) {
                $this->success("提交成功",U(MODULE_NAME.'/runpackage',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("提交失败",U(MODULE_NAME.'/addruns'));
            }
        }
    }
    // 减肥记录，暂时放置
    public function broline(){
        $result = M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
        $total = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        $getlow = intval($total['getlow'] / 100)*100;
        if (!$total) {
            $this->assign('Number',1);
        }
        if (M('Reduce_loserecord')->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>date("Y-m-d")))->find()) {
            $results = M('Reduce_loserecord')->where(array('token'=>$this->token,'openid'=>$this->openid,'date'=>date("Y-m-d")))->find();
        }else{
            $results = M('Reduce_loserecord')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('id desc')->find();
        };

        $st = "";
        for ($j=0; $j < strlen($result['weight']); $j++) {
            $str = substr($result['weight'], $j, 1);
            if (is_numeric($str)) {
                $st .= $str;
            }
        };
        $current['date'] = array('between',date("Y-m-d",strtotime("-1 week")).",".date("Y-m-d"));
        $current['openid'] = $this->openid;
        $current['token'] = $this->token;

        $getRes = M('Reduce_loserecord')->where($current)->select();
        $times = "";
        foreach ($getRes as $keyd => $valued) {
            # code...
            if($keyd == count($getRes)-1){
                $times .= "'".ltrim(str_replace('-','.',substr($valued['date'],5)),'0')."'";
            }else{
                $times .= "'".ltrim(str_replace('-','.',substr($valued['date'],5)),'0')."',";
            }

        }

        if ($getRes == false) {
            $this->assign('flages',1);
        }
        $bmi = "";
        $tizhong = "";
        foreach ($getRes as $key => $value) {
            $tizhong .= $value['weight'].",";
            $bmi .= $value['BMI'].",";
        }
        $bmi = $bmi."}";
        $tizhong = $tizhong."}";
        $bmi = preg_replace('/,}/', '', $bmi);
        $tizhong = preg_replace('/,}/', '', $tizhong);

        // echo $bmi;exit();
        $this->assign('tizhong',$tizhong);
        $this->assign('bmi',$bmi);
        $this->assign('times',$times);
        $diff = $st - $results['weight'];
        if ($diff < 0) {
            $get = 0 - $diff;
            $this->assign('know',1);
            $this->assign('get',$get);
        }
        $this->assign('bweight',$st);
        $this->assign('nweight',$results['weight']);
        $this->assign('diff',$diff);
        $this->display();
    }
    // 减肥记录页面通过ajax传过来的数据，折线图的日期查询
    public function loseSearch(){
        $token = $_GET['token'];
        $openid = $_GET['openid'];
        $start = $_POST['begintime'];
        $end = $_POST['endTime'];
        $map['date'] = array('between',array($start,$end));
        $map['token'] = $token;
        $map['openid'] = $openid;
        $results = M('Reduce_loserecord')->where($map)->select();
        if ($results == false) {
            echo "{\"1\":\"wrong\"}";exit();
        }
        // print_r($results);
        // 获取日期
        $dates = "";
        $j = 1;
        foreach ($results as $key => $value) {
            // = $value['date']
            $dates .= "\"date".$j."\":\"".$value['date']."\",";
            $j++;
        }
        $dates = "{".$dates."}";
        $dates = preg_replace("/,}/", "}", $dates);
        // echo $dates;
        // 获取体重
        $weight = "";
        $j = 1;
        foreach ($results as $key => $value) {
            // = $value['date']
            $weight .= "\"weight".$j."\":\"".$value['weight']."\",";
            $j++;
        }
        $weight = "{".$weight."}";
        $weight = preg_replace("/,}/", "}", $weight);
        // echo $weight;
        // 获取BMI
        $BMI = "";
        $j = 1;
        foreach ($results as $key => $value) {
            // = $value['date']
            $BMI .= "\"BMI".$j."\":\"".$value['BMI']."\",";
            $j++;
        }
        $BMI = "{".$BMI."}";
        $BMI = preg_replace("/,}/", "}", $BMI);
        // 组合三者之值
        $getResult = "{\"0\":".$dates.",\"1\":".$weight.",\"2\":".$BMI."}";
        echo $getResult;
    }
    // 添加减肥记录
    public function record(){
        $date = date("Y-m-d");
        $this->assign('date',$date);
        $this->display();
    }
    // 添加减肥记录数据接收
    public function recordappect(){
        $weight = $this->_post('weight');
        if (M('Reduce_loserecord')->where(array('date'=>date("Y-m-d"),'openid'=>$this->openid,'weight'=>$weight))->find()) {
            $this->error("你已添加当天记录！",U(MODULE_NAME.'/broline',array('token'=>$this->token,'openid'=>$this->openid)));
        }else{
            if (IS_POST) {

                if (M('Reduce_loserecord')->where(array('openid'=>$this->openid,'token'=>$this->token))->select() == false) {
                    // echo M('Reduce_loserecord')->select();exit();
                    $weightss = M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
                    $BMI = number_format(intval($weightss['weight']/1.65/1.65),1);
                    $st = "";
                    for ($j=0; $j < strlen($weightss['weight']); $j++) {

                        $str = substr($weightss['weight'], $j, 1);
                        if (is_numeric($str)) {
                            $st .= $str;
                        }
                    };
                    $data = array(
                        'weight' => $weight,
                        'date' => date("Y-m-d"),
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'BMI' => $BMI,
                        'original' => $st
                    );


                    if (M('Reduce_loserecord')->add($data)) {
                        $this->success("提交成功",U(MODULE_NAME.'/broline',array('token'=>$this->token,'openid'=>$this->openid)));
                    }else{
                        $this->error("提交失败",U(MODULE_NAME.'/record'));
                    }
                }else{
                    // 获取上一次的一条数据，由于id递增的缘故，所以取得最近的一条数据
                    $loserecord = M('Reduce_loserecord')->where(array('token'=>$this->token,'openid'=>$this->openid))->order('id desc')->find();
                    $custom = M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->find();
                    // 体重的获取
                    $avoirdupois = "";
                    for ($j=0; $j < strlen($custom['weight']); $j++) {
                        $str = substr($custom['weight'], $j, 1);
                        if (is_numeric($str)) {
                            $avoirdupois .= $str;
                        }
                    };
                    // 身高的获取
                    $height = "";
                    for ($j=0; $j < strlen($custom['height']); $j++) {
                        $str = substr($custom['height'], $j, 1);
                        if (is_numeric($str)) {
                            $height .= $str;
                        }
                    };
                    // BMI的值
                    $weights = (intval(str_replace("cm", "", $custom['height']))*intval(str_replace("cm", "", $custom['height'])))/10000;
                    $BMI = number_format(intval(str_replace("kg", "", $loserecord['weight']))/$weights,1);
                    // 添加数据到loseRecord表里面，东西是新的
                    $datass = array(
                        'BMI' => $BMI,
                        'token' => $this->token,
                        'openid' => $this->openid,
                        'date' => date("Y-m-d"),
                        'original' => $loserecord['weight'],
                        'weight' => $weight
                    );
                    if(M('Reduce_loserecord')->where(array('date'=>date("Y-m-d"),'openid'=>$this->openid,'weight'=>array('neq',$weight)))->find()){
                        if(M('Reduce_loserecord')->where(array('date'=>date("Y-m-d"),'openid'=>$this->openid))->save(array('weight'=>$weight))){
                            $this->success("修改成功", U(MODULE_NAME . '/broline', array('token' => $this->token, 'openid' => $this->openid)));
                        }else{
                            $this->error("修改失败", U(MODULE_NAME . '/record'));
                        }
                    }else {
                        if (M('Reduce_loserecord')->add($datass)) {
                            $this->success("提交成功", U(MODULE_NAME . '/broline', array('token' => $this->token, 'openid' => $this->openid)));
                        } else {
                            $this->error("提交失败", U(MODULE_NAME . '/record'));
                        }
                    }
                }

            }
        }

    }
    // 个人中心
    public function center(){
        $result = M('Reduce_custom')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        $lose = M('Reduce_lose')->where(array('openid'=>$this->openid,'token'=>$this->token))->find();
        if ($lose['aimweight'] == '') {
            $aimlose = intval(str_replace("kg", "", $result['weight']))-intval($result['explose']);
            $data['aimweight'] = $aimlose."kg";
            M('Reduce_lose')->where(array('openid'=>$this->openid,'token'=>$this->token))->save($data);
        }
        // 取得昵称
        $nikename = M('wxuser')->where(array('token'=>$this->token))->find();
        $nikes = M('wxusers')->where(array('uid'=>$nikename['id'],'openid'=>$this->openid))->find();
        $this->assign('nikename',$nikes['nikename']);
        $this->assign('imgs',$nikes['headimgurl']);
        // custom表
        $this->assign('result',$result);
        // lose表
        $this->assign('lose',$lose);
        $this->display();
    }
    // 个人中心ajax提交界面，上部分
    public function aimsdata(){
        $type = $_POST['type'];
        // 已经减重
        if ($type == 1) {
            $lose = $_POST['lose'];
            $data = array(
                'haslose' => $lose
            );
            if (M('Reduce_lose')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }elseif ($type == 2){
            $ins = $_POST['ins'];
            $data = array(
                'persist' => $ins
            );
            if (M('Reduce_lose')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }else{
            $weight = $_POST['weight'];
            $data = array(
                'aimweight' => $weight
            );
            if (M('Reduce_lose')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }
    }

    // 个人中心ajax提交数据,下半部
    public function humandata(){
        $type = $_POST['type'];
        if ($type == 1) {
            $sex = $_POST['sex'];
            if ($sex = "男") {
                $sex = 1;
            }else{
                $sex = 0;
            };
            $data = array(
                'sex' => $sex
            );
            if (M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }elseif ($type == 2) {
            $birthday = $_POST['birthday'];
            $data = array(
                'age' => $birthday
            );
            if (M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }elseif ($type == 3) {
            $lengths = $_POST['lengths'];
            $data = array(
                'height' => $lengths
            );
            if (M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }elseif ($type == 4) {
            $avoirds = $_POST['avoirds'];
            $data = array(
                'weight' => $avoirds
            );
            if (M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }elseif ($type == 5) {
            $actived = $_POST['actived'];
            $data = array(
                'run' => $actived
            );
            if (M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }else{
            $incitys = $_POST['incitys'];
            $data = array(
                'city' => $incitys
            );
            if (M('Reduce_custom')->where(array('token'=>$this->token,'openid'=>$this->openid))->save($data)) {
                $this->success("修改成功",U(MODULE_NAME.'/center',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("修改失败",U(MODULE_NAME.'/center'));
            }
        }
    }
    // 减肥资讯列表页
    public function info(){
        $result = M('Reduce_losenews')->select();
        $this->assign('results',$result);
        $this->display();
    }
    // 减肥资讯列表内容
    public function infoContent(){
        $id = $_GET['id'];
        $results = M('Reduce_zan')->where(array('uid'=>$id))->select();
        $counts = count($results);

        $result = M('Reduce_losenews')->where(array('id'=>$id,'token'=>$this->token,'openid'=>$this->openid))->find();
        $this->assign('result',$result);
        $j = 0;
        for ($i = 0;$i < $counts;$i++) {
            if (!empty($results[$i]['comment'])) {
                $j++;
            }
        };
        $k = 0;
        for ($i = 0;$i < $counts;$i++) {
            if (!empty($results[$i]['zan'])) {
                $k++;
            }
        };
        $this->assign('zan',$k);
        $this->assign('zans',$results);
        $this->assign('comit',$j);
        $this->display();
    }
    // 赞接收数据
    public function zan(){
        $id = $_GET['id'];
        $result = M('Reduce_zan')->where(array('uid'=>$id,'token'=>$this->token,'openid'=>$this->openid))->find();
        if($result){
            if (empty($result['zan'])) {
                $data = array(
                    'zan' => 1
                );
                if (M('Reduce_zan')->where(array('uid'=>$id))->save($data)) {
                    $this->success("点赞成功！",U(MODULE_NAME.'/infoContent',array('token'=>$this->token,'openid'=>$this->openid,'id'=>$id)));
                }else{
                    $this->error("点赞失败！",U(MODULE_NAME.'/infoContent'));
                }
            }else{
                $this->error("你已经点赞过了哦！",U(MODULE_NAME.'/infoContent'));
            }
        }else{
            // 如果没有数据
            $nikename = M('wxuser')->where(array('token'=>$this->token))->find();
            $nikes = M('wxusers')->where(array('uid'=>$nikename['id'],'openid'=>$this->openid))->find();
            $data = array(
                'nikename' => $nikes['nikename'],
                'zan' => 1,
                'token' => $this->token,
                'openid' => $this->openid,
                'uid' => $id
            );
            if (M('Reduce_zan')->data($data)->add()) {
                $results = M('Reduce_zan')->where(array('uid'=>$id))->select();
                $this->success("点赞成功！-".count($results),U(MODULE_NAME.'/infoContent',array('token'=>$this->token,'openid'=>$this->openid)));
            }else{
                $this->error("点赞失败！",U(MODULE_NAME.'/infoContent'));
            }
        }
    }
    // 评论接收数据
    public function comment(){
        $comment = $_POST['comment'];
        $comment = strip_tags($comment);
        $id = $_GET['id'];
        $result = M('Reduce_zan')->where(array('uid'=>$id,'token'=>$this->token,'openid'=>$this->openid))->find();
        if ($result) {
            if (empty($result['comment'])) {
                $data = array(
                    'comment' => $comment
                );
                if (M('Reduce_zan')->where(array('uid'=>$id))->save($data)) {
                    $this->success("评论成功！",U(MODULE_NAME.'/infoContent',array('token'=>$this->token,'openid'=>$this->openid,'id'=>$id)));
                }else{
                    $this->error("评论失败！",U(MODULE_NAME.'/infoContent'));
                }
            }else{
                $this->error("你已经评论过了哦！",U(MODULE_NAME.'/infoContent'));
            }

        }else{
            $nikename = M('wxuser')->where(array('token'=>$this->token))->find();
            $nikes = M('wxusers')->where(array('uid'=>$nikename['id'],'openid'=>$this->openid))->find();
            $data = array(
                'uid' => $id,
                'comment' => $comment,
                'token' => $this->token,
                'openid' => $this->openid,
                'nikename' => $nikes['nikename']
            );
            if (M('Reduce_zan')->add($data)) {
                $this->success("评论成功！",U(MODULE_NAME.'/infoContent',array('token'=>$this->token,'openid'=>$this->openid,'id'=>$id)));
            }else{
                $this->error("评论失败！",U(MODULE_NAME.'/infoContent'));
            }
        }
    }
}
?>