
<?php
/**
 * @Author: zhang
 * @Date:   2014-12-26 18:15:31
 * @Last Modified by:   zhang
 * @Last Modified time: 2014-12-26 22:31:23
 */
class VotterAction extends BaseAction{
	public $db;
	public $token;

	// 初始化的过程
	public function _initialize(){
		parent::_initialize();
		if ($_REQUEST['token']) {
			session('token',$_REQUEST['token']);
		}
		$this->token = session('token');
		$this->db = M('Wechatlecturer');
		$this->assign('token',$this->token);
	}
	// 开始显示页面
	public function index(){
		$this->display();
	}
	// 提交数据请求的页面
	public function accept(){
		// $name = "金海燕";
		$result1 = $this->db->where(array('name'=>'金晓燕'))->find();
		$result2 = $this->db->where(array('name'=>'雷舰'))->find();
		$result3 = $this->db->where(array('name'=>'李宏伟'))->find();
		$result4 = $this->db->where(array('name'=>'李佐'))->find();
		$result5 = $this->db->where(array('name'=>'梁怀超'))->find();
		$result6 = $this->db->where(array('name'=>'林辉'))->find();
		$result7 = $this->db->where(array('name'=>'牛涛'))->find();
		$result8 = $this->db->where(array('name'=>'屠莉佳'))->find();
		$result9 = $this->db->where(array('name'=>'谢朝德'))->find();
		$result10 = $this->db->where(array('name'=>'张海燕'))->find();
		$result11 = $this->db->where(array('name'=>'张慧欣'))->find();
		
		$array = array(
				'1' => $result1['num'].",".$result1['snum'],
				'2' => $result2['num'].",".$result2['snum'],
				'3' => $result3['num'].",".$result3['snum'],
				'4' => $result4['num'].",".$result4['snum'],
				'5' => $result5['num'].",".$result5['snum'],
				'6' => $result6['num'].",".$result6['snum'],
				'7' => $result7['num'].",".$result7['snum'],
				'8' => $result8['num'].",".$result8['snum'],
				'9' => $result9['num'].",".$result9['snum'],
				'10' => $result10['num'].",".$result10['snum'],
				'11' => $result11['num'].",".$result11['snum']
			);
		
		$str = json_encode($array);
		echo $str;
		
	}
	// 显示第二个页面
	public function result(){
		$result1 = $this->db->where(array('name'=>'金晓燕'))->find();
		$result2 = $this->db->where(array('name'=>'雷舰'))->find();
		$result3 = $this->db->where(array('name'=>'李宏伟'))->find();
		$result4 = $this->db->where(array('name'=>'李佐'))->find();
		$result5 = $this->db->where(array('name'=>'梁怀超'))->find();
		$result6 = $this->db->where(array('name'=>'林辉'))->find();
		$result7 = $this->db->where(array('name'=>'牛涛'))->find();
		$result8 = $this->db->where(array('name'=>'屠莉佳'))->find();
		$result9 = $this->db->where(array('name'=>'谢朝德'))->find();
		$result10 = $this->db->where(array('name'=>'张海燕'))->find();
		$result11 = $this->db->where(array('name'=>'张慧欣'))->find();

		$results[$result1['id']] = $result1['num'] + $result1['snum'];
		$results[$result2['id']] = $result2['num'] + $result2['snum'];
		$results[$result3['id']] = $result3['num'] + $result3['snum'];
		$results[$result4['id']] = $result4['num'] + $result4['snum'];
		$results[$result5['id']] = $result5['num'] + $result5['snum'];
		$results[$result6['id']] = $result6['num'] + $result6['snum'];
		$results[$result7['id']] = $result7['num'] + $result7['snum'];
		$results[$result8['id']] = $result8['num'] + $result8['snum'];
		$results[$result9['id']] = $result9['num'] + $result9['snum'];
		$results[$result10['id']] = $result10['num'] + $result10['snum'];
		$results[$result11['id']] = $result11['num'] + $result11['snum'];

		asort($results);
	
		foreach ($results as $key => $value) {
			if ($results[$key] == end($results)) {
				$first = $key;
				break;
			}
		}
		$firstname = $this->db->where(array('id'=>$first))->find();
		// 第一个
		$this->assign('first',$firstname['name']);

		array_pop($results);
		
		asort($results);
		foreach ($results as $key => $value) {
			if ($results[$key] == end($results)) {
				$second = $key;
				break;
			}
		}
		$secondname = $this->db->where(array('id'=>$second))->find();
		// 第二个
		$this->assign('second',$secondname['name']);
		array_pop($results);

		asort($results);
		foreach ($results as $key => $value) {
			if ($results[$key] == end($results)) {
				$third = $key;
				break;
			}
		}
		$thirdname = $this->db->where(array('id'=>$third))->find();
		// 第三个
		$this->assign('third',$thirdname['name']);
		array_pop($results);
		// 第四个
		asort($results);
		foreach ($results as $key => $value) {
			if ($results[$key] == end($results)) {
				$four = $key;
				break;
			}
		}
		$fourname = $this->db->where(array('id'=>$four))->find();
		// 第四个
		$this->assign('four',$fourname['name']);
		array_pop($results);
		asort($results);

		foreach ($results as $key => $value) {
			if ($results[$key] == end($results)) {
				$five = $key;
				break;
			}
		}
		$fivename = $this->db->where(array('id'=>$five))->find();
		// 第五个
		$this->assign('five',$fivename['name']);
		array_pop($results);

		asort($results);

		foreach ($results as $key => $value) {
			if ($results[$key] == end($results)) {
				$six = $key;
				break;
			}
		}
		$sixname = $this->db->where(array('id'=>$six))->find();
		// 第六个
		$this->assign('six',$sixname['name']);
		array_pop($results);
		$this->display();
	}
}
?>