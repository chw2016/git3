<?php
/**
 * Created by PhpStorm.
 * User: SuperMan
 * Date: 2014/11/13
 * Time: 10:37
 */
class CategoryModel extends Model{
    protected $table="tp_ieat_category";
    protected $token;
    public function __construct(){
       parent::__construct('ieat_category','tp_','');
        $this->token = $_GET['token'];
    }
    /*
     * 查询表中所有数据
     */
    public function selectAll(){
        $sql = "select * from ".$this->table." where token='".$this->token."'";
        return $this->query($sql);
    }
    /*
     * parm:int $id
     *
     */
    public function findOne($id){
        $sql = "select * from ".$this->table." where cat_id=".$id." and token='".$this->token."'";
        return $this->query($sql);
    }
    /*
     * parm:int $id
     */
    public function findAll($id){
        $sql = "select cat_id,cat_name,parent_id from tp_ieat_category where token='".$this->token. "' and parent_id=" . $id;
        return $this->query($sql);
    }

    /*
    * getCatTree
    * pram：int $id
    * 获得$id栏目下的子孙树
    */
    public function getCatTree($arr,$id=0,$lev=0){
        $tree = array();
        foreach($arr as $v){
            if($v['parent_id'] == $id){
                $v['lev'] = $lev;
                $tree[] = $v;
                $tree = array_merge($tree,$this->getCatTree($arr,$v['cat_id'],$lev+1));
            }
        }
        return $tree;
    }
    /*
        parm: int $id
        return array $id栏目的家谱树
    */
    public function getTree($id=0) {
        $tree = array();
        $sql = "select * from ".$this->table."where token='".$this->token."'";
        $cats = $this->query($sql);
        //$cats = M('Ieat_category')->where(array('token'=>$this->token))->select();
        while($id>0) {
            foreach($cats as $v) {
                if($v['cat_id'] == $id) {
                    $tree[] = $v;
                    $id = $v['parent_id'];
                    break;
                }
            }
        }
        return array_reverse($tree);
    }
    /*
        parm: int $id
        return array $id栏目的子栏目
    */
    public function getSon($id){
        $sql = "select cat_id,cat_name,parent_id from ".$this->table." where parent_id=".$id." AND token='" . $this->token ."'";
        //echo $sql;exit;
        return $this->query($sql);
    }
}