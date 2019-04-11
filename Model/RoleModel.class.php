<?php
namespace Model;
use Think\Model;

class RoleModel extends Model {
    //修改数据的“瞻前顾后”机制丰富(本质：重写父类对应方法)
    // 更新数据前的回调方法
    //参数： &$data 是引用传递，内部对参数的修改，外部也可以访问
    protected function _before_update(&$data,$options) {
        //在真正执行update语句之前把相关的数据都准备好
        //根据role_auth_ids制作role_auth_ac
        $authinfo = D('Auth')->select($data['role_auth_ids']);
        $s = array();
        foreach($authinfo as $k => $v){
            if(!empty($v['auth_c']) && !empty($v['auth_a']))
                $s[] = $v['auth_c'].'-'.$v['auth_a'];
        }
        $s = implode(',',$s);
        //dump($s);string(55) "Goods-showlist,Goods-tianjia,Order-showlist,Order-dayin"
        $data['role_auth_ac'] = $s;
    }
    // 更新成功后的回调方法
    protected function _after_update($data,$options) {}
}
