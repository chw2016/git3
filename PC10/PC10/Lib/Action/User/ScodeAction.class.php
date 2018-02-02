<?php
/**
 * Created by IntelliJ IDEA.
 * User: Topher
 * Date: 14-6-12
 * Time: 下午2:45
 * To change this template use File | Settings | File Templates.
 */
class ScodeAction extends UserAction{

    public function index(){
        $scodeModel=M('Scode');
        $where['uid']=session('uid');
        $count=$scodeModel->where($where)->count();
        $page=new Page($count,15);
        $info=$scodeModel->where($where)->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('info',$info);
        $this->display();
    }

    public function importcode(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = 3145728 ;// 设置附件上传大小
        $upload->allowExts  = array('txt');// 设置附件上传类型
        $upload->savePath =  './upload/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $err = $upload->getErrorMsg();
            $this->ajaxReturn(array('code'=>-1,'msg'=>$err));
        }else{// 上传成功
            $data = $upload->getUploadFileInfo();
            $filename = $data[0]['savepath'].$data[0]['savename'];
            $handle  = fopen ($filename, "r");
            $scodeModel = M('Scode');
            $where = array();
            $i = 0;
            $c = 0;
            $d = 0;
            while (!feof ($handle)){
                $temparr = array();
                $temres = null;
                $buffer  = fgets($handle, 4096);
                $username = trim($buffer);
                $temparr = explode('|',$username);
                $where['m_code'] = $temparr[0];
                $where['a_code'] = $temparr[1];
                $temres = $scodeModel->field('id')->where($where)->find();
                if($temres == null){
                    $codedata = array();
                    $codedata['m_code'] =$temparr[0];
                    $codedata['a_code'] =$temparr[1];
                    $codedata['add_time'] = time();
                    $codedata['status'] = 0;
                    $codedata['uid'] = session('uid');
                    if($scodeModel->add($codedata)){
                        $i=$i+1;
                    }else{
                        $c=$c+1;
                    }
                    continue;
                }else{
                    $d=$d+1;
                    continue;
                }
            }
            fclose ($handle);
            $this->ajaxReturn(array('code'=>0,'msg'=>'处理成功(成功插入'.$i.'条,失败'.$c.'条,有'.$d.'条重复)'));
        }
    }

}