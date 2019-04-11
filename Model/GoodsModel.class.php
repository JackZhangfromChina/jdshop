<?php
namespace Model;
use Think\Model;

class GoodsModel extends Model {
    //给一些字段设置自动完成(这些字段不在form表单体现)
    // 自动完成定义
    protected $_auto            =   array(
        //array(完成字段1,完成规则,[完成条件,附加规则]),
        array('add_time','time',1,'function'),  //添加数据填充
        array('upd_time','time',3,'function'),  //添加、修改都填充
    );

    //字段映射
    //protected $_map             =   array();  // 字段映射定义
    protected $_map   = array(
        'qiang'=>'goods_is_qiang',
        'hot'=>'goods_is_hot',
        'rec'=>'goods_is_rec',
        'new'=>'goods_is_new',
    );

    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {
        /*
        dump($data);
        array(5) {["goods_id"] => string(3) "113"}
        */
        //添加商品，实现对"属性"信息的维护入库操作
        //数据表：sp_goods_attr填充记录
        if(!empty($_POST['attr_id'])){
            foreach($_POST['attr_id'] as $k => $v ){
                foreach($v as $kk => $vv){
                    $arr['goods_id'] = $data['goods_id']; 
                    $arr['attr_id'] = $k; 
                    $arr['attr_val'] = $vv; 
                    D('GoodsAttr')->add($arr);
                }
            }
        }
        //处理扩展分类，把扩展分类信息存储到sp_goods_cat表中
        if(!empty($_POST['cat_ext_id'])){
            foreach($_POST['cat_ext_id'] as $kk => $vv){
                if($vv>0){
                    $arr2['goods_id'] = $data['goods_id'];
                    $arr2['cat_id'] = $vv;
                    D('GoodsCat')->add($arr2);
                }
            }
        }
    }

    // 修改前的回调方法
    protected function _before_update($data,$options) {
        /*
        dump($options);
        array(3) {
          ["table"] => string(8) "sp_goods"
          ["model"] => string(5) "Goods"
          ["where"] => array(1) {
            ["goods_id"] => int(24)
          }
        }*/
        //修改商品，实现对"属性"信息的维护入库操作
        //战略：删除旧的、添加新的
        //数据表：sp_goods_attr填充记录
        if(!empty($_POST['attr_id'])){
            //删除旧的
            D('GoodsAttr')->where(array('goods_id'=>$options['where']['goods_id']))->delete();
            foreach($_POST['attr_id'] as $k => $v ){
                foreach($v as $kk => $vv){
                    $arr['goods_id'] = $options['where']['goods_id']; 
                    $arr['attr_id'] = $k; 
                    $arr['attr_val'] = $vv; 
                    D('GoodsAttr')->add($arr);
                }
            }
        }
        //维护商品的扩展分类信息
        //战略：删除旧的、添加新的
        //数据表：sp_goods_cat填充记录
        if(!empty($_POST['cat_ext_id'])){
            D('GoodsCat')->where(array('goods_id'=>$options['where']['goods_id']))->delete();
            foreach($_POST['cat_ext_id'] as $kk => $vv){
                if($vv>0){
                    $arr2['goods_id'] = $options['where']['goods_id'];
                    $arr2['cat_id'] = $vv;
                    D('GoodsCat')->add($arr2);
                }
            }
        }
    }
}
