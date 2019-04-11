<?php
namespace Model;
use Think\Model;

class OrderModel extends Model {
    // 自动完成定义
    protected $_auto            =   array(
        //array(完成字段1,完成规则,[完成条件,附加规则]),
        array('add_time','time',1,'function'),  //添加数据填充
        array('upd_time','time',3,'function'),  //添加、修改都填充
    );

    //数据添加的“瞻前顾后”
    // 插入数据前的回调方法
    protected function _before_insert(&$data,$options) {}

    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {
        //$data['order_id'] = xxx;
        //维护sp_order_goods表(订单对应的商品信息)
        //获取购物车信息，遍历购物车商品，同时维护sp_order_goods记录信息
        $cart = new \Tools\Cart();
        $cartinfo = $cart -> getCartInfo();
        $cart -> delAll();//清空购物车信息
        foreach($cartinfo as $k => $v){
            $arr = array(
                'order_id' => $data['order_id'],
                'goods_id' => $v['goods_id'],
                'goods_number' => $v['goods_buy_number'],
                'goods_price' => $v['goods_price'],
            );
            D('OrderGoods')->add($arr);
        }
    }
}
