<?php
namespace Model;
use Think\Model;

class AuthModel extends Model {
    //数据添加的“瞻前顾后”
    // 插入数据前的回调方法
    protected function _before_insert(&$data,$options) {}

    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {
        //数据添加完毕要维护auth_path和auth_level
        /*
        dump($data);
        array(5) {
          ["auth_name"] => string(12) "商品品牌"
          ["auth_pid"] => int(101)
          ["auth_c"] => string(5) "Brand"
          ["auth_a"] => string(8) "showlist"
          ["auth_id"] => string(3) "113"
        }
        */
        //1) auth_path维护
        //① 顶级权限：全路径=本身主键id值
        if($data['auth_pid']==0){
            $path = $data['auth_id'];
        }else{
        //② 非顶级权限：全路径=父级全路径-本身主键id值
            $ppath = $this->where(array('auth_id'=>$data['auth_pid']))->getField('auth_path');
            $path = $ppath ."-". $data['auth_id'];
        }
        //2) auth_level维护：全路径中横岗"-"杠的个数
        $level = substr_count($path,'-');
        
        $arr = array(
            'auth_id'=>$data['auth_id'],
            'auth_path'=>$path,
            'auth_level'=>$level,
        );
        $this -> save($arr);
    }
}
