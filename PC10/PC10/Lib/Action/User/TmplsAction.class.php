<?php
class TmplsAction extends UserAction{
	public function index(){
		$db=D('Wxuser');
		$where['token']=session('token');
		$where['uid']=session('uid');
		$info=$db->where($where)->find();
		$this->assign('info',$info);
        $this->assign('hover3',1);
		$this->display();
	}
	public function add(){
		$gets=$this->_get('style');
		$db=M('Wxuser');
		switch($gets){
			case 1:
				$data['tpltypeid']=1;
				$data['tpltypename']='ty_index2';
				break;
			case 2:
				$data['tpltypeid']=2;
				$data['tpltypename']='mr_index';
				break;
			case 3:
				$data['tpltypeid']=3;
				$data['tpltypename']='mr_index';
				break;
			case 4:
				$data['tpltypeid']=4;
				$data['tpltypename']='ty_index';
				break;
			case 5:
				$data['tpltypeid']=5;
				$data['tpltypename']='flash_index';
				break;
           case 6:
				$data['tpltypeid']=6;
				$data['tpltypename']='lx_index';
				break;
          case 7:
				$data['tpltypeid']=7;
				$data['tpltypename']='qw_index';
				break;
                      
          case 8:
				$data['tpltypeid']=8;
				$data['tpltypename']='im_index';
				break;
           case 9:
				$data['tpltypeid']=9;
				$data['tpltypename']='hx_index';
				break;
          case 10:
				$data['tpltypeid']=10;
				$data['tpltypename']='yk_index';
				break;
          case 11:
				$data['tpltypeid']=11;
				$data['tpltypename']='wh_index';
				break;
          case 12:
				$data['tpltypeid']=12;
				$data['tpltypename']='jz_index';
				break;
          case 13:
				$data['tpltypeid']=13;
				$data['tpltypename']='hm_index';
				break;
          case 14:
				$data['tpltypeid']=14;
				$data['tpltypename']='abc_index';
				break;
          case 15:
				$data['tpltypeid']=15;
				$data['tpltypename']='jqw_index';
				break;
          case 16:
				$data['tpltypeid']=16;
				$data['tpltypename']='wk_index';
				break;
          case 17:
				$data['tpltypeid']=17;
				$data['tpltypename']='guqi_index';
				break;
          case 18:
				$data['tpltypeid']=18;
				$data['tpltypename']='mlktv_index';
				break;
          case 19:
				$data['tpltypeid']=19;
				$data['tpltypename']='jinlong_index';
				break;
	  	  case 20:
				$data['tpltypeid']=20;
				$data['tpltypename']='xiyuanyaji_index';
				break;
		  case 21:
				$data['tpltypeid']=21;
				$data['tpltypename']='tpl_110_index';
				break;
		  case 22:
				$data['tpltypeid']=22;
				$data['tpltypename']='tpl_107_index';
				break;				
		  case 23:
				$data['tpltypeid']=23;
				$data['tpltypename']='tpl_109_index';
				break;
          case 24:
				$data['tpltypeid']=24;
				$data['tpltypename']='bhgj_index';
				break;
          case 25:
				$data['tpltypeid']=25;
				$data['tpltypename']='tpl_129_index';
				break;
          case 26:
				$data['tpltypeid']=26;
				$data['tpltypename']='tpl_123_index';
				break;
          case 27:
				$data['tpltypeid']=27;
				$data['tpltypename']='tpl_124_index';
				break;
          case 28:
				$data['tpltypeid']=28;
				$data['tpltypename']='tpl_125_index';
				break;
          case 29:
				$data['tpltypeid']=29;
				$data['tpltypename']='tpl_132_index';
				break;
          case 30:
				$data['tpltypeid']=30;
				$data['tpltypename']='tpl_130_index';
				break;
          case 31:
				$data['tpltypeid']=31;
				$data['tpltypename']='tpl_131_index';
				break;
          case 32:
				$data['tpltypeid']=32;
				$data['tpltypename']='zhongshan_index';
				break;	
          case 33:
				$data['tpltypeid']=33;
				$data['tpltypename']='taige_index';
				break;	
          case 34:
				$data['tpltypeid']=34;
				$data['tpltypename']='tangguo_index';
				break;
           case 35:
                $data['tpltypeid']=35;
                $data['tpltypename']='tpl_135_index';
                break;
            case 36:
                $data['tpltypeid']=36;
                $data['tpltypename']='tpl_136_index';
                break;
            case 37:
                $data['tpltypeid']=37;
                $data['tpltypename']='tpl_137_index';
                break;
            case 38:
                $data['tpltypeid']=38;
                $data['tpltypename']='tpl_138_index';
                break;
            case 39:
                $data['tpltypeid']=39;
                $data['tpltypename']='tpl_139_index';
                break;
            case 40:
                $data['tpltypeid']=40;
                $data['tpltypename']='tpl_140_index';
                break;
            case 41:
                $data['tpltypeid']=41;
                $data['tpltypename']='tpl_141_index';
                break;
            case 42:
                $data['tpltypeid']=42;
                $data['tpltypename']='tpl_142_index';
                break;
            case 43:
                $data['tpltypeid']=43;
                $data['tpltypename']='tpl_143_index_new';
                break;
            case 44:
                $data['tpltypeid']=44;
                $data['tpltypename']='tpl_144_index_new';
                break;
            case 45:
                $data['tpltypeid']=45;
                $data['tpltypename']='tpl_145_index_new';
                break;
            case 46:
                $data['tpltypeid']=46;
                $data['tpltypename']='tpl_146_index_new';
                break;
            case 47:
                $data['tpltypeid']=47;
                $data['tpltypename']='tpl_147_index_new';
                break;
            case 48:
                $data['tpltypeid']=48;
                $data['tpltypename']='tpl_148_index_new';
                break;
	        case 49:
                $data['tpltypeid']=49;
                $data['tpltypename']='tpl_149_index_new';
                break;
            case 50:
                $data['tpltypeid']=50;
                $data['tpltypename']='tpl_150_index_new';
                break;
            case 51:
                $data['tpltypeid']=51;
                $data['tpltypename']='tpl_151_index_new';
                break;
            case 52:
            	$data['tpltypeid']=52;
            	$data['tpltypename']='tpl_152_index_new';
            	break;
            case 53:
            	$data['tpltypeid']=53;
            	$data['tpltypename']='tpl_153_index_new';
            	break;
            	//新增加的十套模板，从54开始
           case 54:
            	$data['tpltypeid']=54;
            	$data['tpltypename']='tpl_154_index_new';
            	break;
           case 55:
            	$data['tpltypeid']=55;
            	$data['tpltypename']='tpl_155_index_new';
            	break;
           case 56:
            	$data['tpltypeid']=56;
            	$data['tpltypename']='tpl_156_index_new';
            	break;
           case 57:
            	$data['tpltypeid']=57;
            	$data['tpltypename']='tpl_157_index_new';
            	break;
           case 58:
            	$data['tpltypeid']=58;
            	$data['tpltypename']='tpl_158_index_new';
            	break;
           case 59:
            	$data['tpltypeid']=59;
            	$data['tpltypename']='tpl_159_index_new';
            	break;
           case 60:
            	$data['tpltypeid']=60;
            	$data['tpltypename']='tpl_160_index_new';
            	break;
           case 61:
            	$data['tpltypeid']=61;
            	$data['tpltypename']='tpl_161_index_new';
            	break;
           case 62:
            	$data['tpltypeid']=62;
            	$data['tpltypename']='tpl_162_index_new';
            	break;
           case 63:
            	$data['tpltypeid']=63;
            	$data['tpltypename']='tpl_163_index_new';
            	break;
            	// 国税局主页模板
            case 70:
                $data['tpltypeid']=70;
                $data['tpltypename']='tpl_170_index_new';
                break;
            //东莞控股
            case 71:
                $data['tpltypeid']=71;
                $data['tpltypename']='tpl_171_index_new';
                break;
            case 72:
                $data['tpltypeid']=72;
                $data['tpltypename']='tpl_172_index_new';
                break;
            case 73:
                $data['tpltypeid']=73;
                $data['tpltypename']='tpl_173_index_new';
                break;
            case 74:
                $data['tpltypeid']=74;
                $data['tpltypename']='tpl_174_index_new';
                break;

        }
        $where ['token'] = session ( 'token' );
        $db->where ( $where )->save ( $data );
    }
    public function lists() {
        $gets = $this->_get ( 'style' );
        $db = M ( 'Wxuser' );
        switch ($gets) {
            case 4 :
                $data ['tpllistid'] = 4;
                $data ['tpllistname'] = 'ktv_list';
                break;
            case 1 :
                $data ['tpllistid'] = 1;
                $data ['tpllistname'] = 'yl_list';
                break;
            case 2 :
                $data ['tpllistid'] = 2;
                $data ['tpllistname'] = 'new2_list';
                break;
            case 5:
                $data['tpllistid']=5;
                $data['tpllistname']='new3_list';
                break;  
            case 6:
                $data['tpllistid']=6;
                $data['tpllistname']='new4_list';
                break; 
            case 7:
                $data['tpllistid']=7;
                $data['tpllistname']='new5_list';
                break; 
            case 8:
                $data['tpllistid']=8;
                $data['tpllistname']='new6_list';
                break; 
            case 9:
                $data['tpllistid']=9;
                $data['tpllistname']='new7_list';
                break; 
            case 10:// 国税局列表模板
                $data['tpllistid']=10;
                $data['tpllistname']='new10_list';
                break;
            case 11:
                $data['tpllistid']=11;
                $data['tpllistname']='tpl_173_index_new';
                break;
		}
		$where['token']=session('token');
		$db->where($where)->save($data);
	}
	public function content(){
		$gets=$this->_get('style');
		$db=M('Wxuser');
		switch($gets){
			     case 1:
				        $data['tplcontentid']=1;
				        $data['tplcontentname']='yl_content';
				        break;
			     case 3:
				        $data['tplcontentid']=3;
				        $data['tplcontentname']='ktv_content';
				        break;
				 case 4:
						$data['tplcontentid']=4;
						$data['tplcontentname']='new4_content';
						break;
				 case 5:
						$data['tplcontentid']=5;
						$data['tplcontentname']='new5_content';
						break;
				 case 6:
						$data['tplcontentid']=6;
						$data['tplcontentname']='new6_content';
						break;
		}
		$where['token']=session('token');
		$db->where($where)->save($data);

	}
	public function channel(){
		$gets=$this->_get('style');
		$db=M('Wxuser');
		switch($gets){
			case 1:
				$data['tplchannelid']=1;
				$data['tplchannelname']='new1_channel';
				break;
			case 2:
				$data['tplchannelid']=2;
				$data['tplchannelname']='new2_channel';
				break;
            case 3:
                $data['tplchannelid']=3;
                $data['tplchannelname']='new3_channel';
                break;
            case 4:
                $data['tplchannelid']=4;
                $data['tplchannelname']='new4_channel';
                break;
            case 5:
                $data['tplchannelid']=5;
                $data['tplchannelname']='new5_channel';
                break;
			case 6:
                $data['tplchannelid']=6;
                $data['tplchannelname']='new6_channel';
                break;
            case 7:    // 国税局频道模板
                $data['tplchannelid']=7;
                $data['tplchannelname']='new7_channel';
                break;

            case 8:    // 国税局频道模板
                $data['tplchannelid']=8;
                $data['tplchannelname']='new8_channel';
                break;
		}
		$where['token']=session('token');
		$db->where($where)->save($data);
	}
	public function insert(){
	
	}
	public function upsave(){
	
	}
}
?>