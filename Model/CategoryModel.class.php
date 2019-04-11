<?php
namespace Model;
use Think\Model;

class CategoryModel extends Model {


    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {
        //数据添加完毕要维护cat_path和cat_level
        /*
        dump($data);
        array(5) {
          ["cat_name"] => string(12) "商品品牌"
          ["cat_pid"] => int(101)
          ["cat_id"] => string(3) "113"
        }
        */
        //1) cat_path维护
        //① 顶级权限：全路径=本身主键id值
        if($data['cat_pid']==0){
            $path = $data['cat_id'];
        }else{
        //② 非顶级权限：全路径=父级全路径-本身主键id值
            $ppath = $this->where(array('cat_id'=>$data['cat_pid']))->getField('cat_path');
            $path = $ppath ."-". $data['cat_id'];
        }
        //2) cat_level维护：全路径中横岗"-"杠的个数
        $level = substr_count($path,'-');
        
        $arr = array(
            'cat_id'=>$data['cat_id'],
            'cat_path'=>$path,
            'cat_level'=>$level,
        );
        $this -> save($arr);
    }
}
