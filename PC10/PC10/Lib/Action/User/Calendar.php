<?php 
/**
 * Class 这个类是做设置旅游时间有关的表
 */
class Calendar{
	protected $_table;//table表格
	protected $_currentDate;//当前日期
	protected $_year; //年
	protected $_month; //月
	protected $_days; //给定的月份应有的天数
	protected $_dayofweek;//给定月份的 1号 是星期几
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->_table="";
		$this->_year = isset($_GET["y"])?$_GET["y"]:date("Y");
		$this->_month = isset($_GET["m1"])?$_GET["m1"]:date("m");
		if ($this->_month>12){//处理出现月份大于12的情况
			$this->_month=1;
			$this->_year++;
		}
		if ($this->_month<1){//处理出现月份小于1的情况
			$this->_month=12;
			$this->_year--;
		}
		$yue=intval($this->_month);//把月分比如05变成5
		$this->_currentDate = '<span class="y">'.$this->_year.'</span>年<span class="m">'.$yue.'</span>月份';//当前得到的日期信息
		$this->_days = date("t",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份应有的天数
		$this->_dayofweek = date("w",mktime(0,0,0,$this->_month,1,$this->_year));//得到给定的月份的 1号 是星期几
	}
	/**
	 * 输出标题和表头信息
	 */
	protected function _showTitle()
	{
		$this->_table="<table style='width: 700px;'><thead><tr align='center' ><th colspan='7' class='tou'>".$this->_currentDate."</th></tr ></thead>";
		$this->_table.="<tbody><tr >";
		$this->_table .="<td style='color:red'>星期日</td>";
		$this->_table .="<td>星期一</td>";
		$this->_table .="<td>星期二</td>";
		$this->_table .="<td>星期三</td>";
		$this->_table .="<td>星期四</td>";
		$this->_table .="<td>星期五</td>";
		$this->_table .="<td style='color:red'>星期六</td>";
		$this->_table.="</tr>";
	}
	/**
	 * 输出日期信息
	 * 根据当前日期输出日期信息
	 */
	protected function _showDate($c='',$id='')
	{
		// p($c);

		$shoufu=M('No_credit')->where(array('id'=>$id))->getField('shoufu');//得着付钱

		$yue=intval($this->_month);//把月分比如05变成5

		$y_m=$this->_year.'-'.$yue;
		/**
		 * 重组数组，key=d,且不是这个月的去掉
		 */
		foreach($c as $k=>$v){
			if($v['y_m']==$y_m){
				$c[$v[d]]=$v;
				unset($c[$k]);
			}else{
				unset($c[$k]);
			}
		}

		$nums=$this->_dayofweek+1;
		for ($i=1;$i<=$this->_dayofweek;$i++){//输出1号之前的空白日期
			$this->_table.="<td> </td>";
		}
		//foreach($c as $v) {
		for ($i = 1; $i <= $this->_days; $i++) {//输出天数信息
			if ($nums % 7 == 0) {//换行处理：7个一行

				$this->_table .= "<td style='height:91px;' ";
				if(array_key_exists($i,$c)){
					$this->_table .=" class='red' ";
				}

				$this->_table .="><p class='d'>$i</p>";

				if(array_key_exists($i,$c)){
					$this->_table .="<p>￥$shoufu</p><p>2</p>";
				}
				$this->_table .="<input style='display: none; height: 80px;width:90px;' type='text' name='num' class='num'> </td></tr><tr >";

			} else {

				$this->_table .= "<td style='height:91px;' ";
				if(array_key_exists($i,$c)){
					$this->_table .=" class='red' ";
				}

				$this->_table .="><p class='d'>$i</p>";

				if(array_key_exists($i,$c)){
					$this->_table .="<p>￥$shoufu</p><p>2</p>";
				}


				$this->_table .="<input style='display: none;height: 80px;width:90px;' type='text' name='num' class='num'></td>";


			}
			$nums++;
		}

		$this->_table.="</tbody></table>";
		//获取当前id
		$id=$_GET['id'];
		//这里拼接自己的url地址
		$this->_table.="<h3><a href='?g=User&m=Loan&a=set_time&id=".$id."&y=".($this->_year)."&m1=".($this->_month-1)."'>上一月</a>   ";
		$this->_table.="<a href='?g=User&m=Loan&a=set_time&id=".$id."&y=".($this->_year)."&m1=".($this->_month+1)."'>下一月</a></h3>";
	}
	/**
	 * 输出日历
	 */
	public function showCalendar($b='',$id='')
	{
		$this->_showTitle();
		$this->_showDate($b,$id);
		return $this->_table;
	}
}


?>
