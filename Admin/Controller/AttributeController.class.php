<?php
namespace Admin\Controller;
use Tools\AdminController;

class AttributeController extends AdminController {
    //列表展示
    function showlist(){
        $type_id = I('get.type_id');
        //传递差异导航内容
        $daohang = array(
            'first' => '属性管理',
            'second' => '属性列表',
            'btn' => '添加',
            'btn_link' => U('tianjia'),
        );
        $this -> assign('daohang',$daohang);

        /****获得可选择的“类型”信息****/
        $attrinfo = D('Type')->select();
        $this -> assign('attrinfo',$attrinfo);
        /****获得可选择的“类型”信息****/

        //获得全部的属性信息
        $info = D('Attribute')
            ->alias('a')
            ->join('__TYPE__ t on a.type_id=t.type_id')
            ->field('a.*,t.type_name')
            ->select();
        $this -> assign('info',$info);
        $this -> display();
    }
    //添加
    function tianjia(){
        $Attribute = new \Model\AttributeModel();
        if(IS_POST){
            $shuju = $Attribute -> create();
            if($shuju === false){
                //获得验证的错误信息，并分配给模板展示
                $errorinfo = $Attribute->getError();
                $this -> assign('errorinfo',$errorinfo);
            }else{
                if($Attribute->add($shuju)){
                    $this -> success('添加属性成功',U('showlist'),1);
                }else{
                    $this -> error('添加属性失败',U('tianjia'),1);
                } 
                exit;
            }
        }
        //传递差异导航内容
        $daohang = array(
            'first' => '属性管理',
            'second' => '属性列表',
            'btn' => '返回',
            'btn_link' => U('showlist'),
        );
        $this -> assign('daohang',$daohang);
        /****获得可选择的“类型”信息****/
        $attrinfo = D('Type')->select();
        $this -> assign('attrinfo',$attrinfo);
        /****获得可选择的“类型”信息****/
        $this -> display();
    }

    //根据类型信息获得对应的属性信息(添加商品)
    function getAttributeByType(){
        $type_id = I('get.type_id');
        $attrinfo = D('Attribute')
            ->alias('a')
            ->join('__TYPE__ t on a.type_id=t.type_id')
            ->field('a.*,t.type_name')
            ->where(array('a.type_id'=>$type_id))
            ->select();
        echo json_encode($attrinfo); //[{},{},{},{}....]
        //[Object { attr_id="1", attr_name="出版社", type_id="1", 更多...}, Object { attr_id="2", attr_name="作者", type_id="1", 更多...}, Object { attr_id="3", attr_name="出版日期", type_id="1", 更多...}, Object { attr_id="4", attr_name="字数", type_id="1", 更多...}]
    }

    //根据类型信息获得对应的属性信息(修改商品)
    function getAttributeByTypeUpd(){
        $type_id = I('get.type_id');
        $goods_id = I('get.goods_id');
        //判断，获得“原始”或“实体”属性
        $goodsinfo = D('Goods')
            ->where(array('goods_id'=>$goods_id,'type_id'=>$type_id))
            ->find();

        if($goodsinfo === null){
            //获得原始属性sp_attribute
            $attrinfo = D('Attribute')
                ->alias('a')
                ->join('__TYPE__ t on a.type_id=t.type_id')
                ->field('a.*,t.type_name')
                ->where(array('a.type_id'=>$type_id))
                ->select();
            $attrinfo['flag'] =0;
            echo json_encode($attrinfo); //[{},{},{},{}....]
        }else{
            //获得实体属性 sp_goods_attr和sp_attribute
            //① 查询商品对应的全部属性信息
            $goods_attrinfo = D('GoodsAttr')
                ->alias('ga')
                ->join('__ATTRIBUTE__ a on ga.attr_id=a.attr_id')
                ->field('ga.goods_id,ga.attr_id,ga.attr_val,a.attr_name,a.attr_sel,a.attr_opt_vals')
                ->where(array('ga.goods_id'=>$goods_id))
                ->select();
            //整合$goods_attrinfo数据，把属性一致的信息整合到一起
            $z_attrinfo = array();
            foreach($goods_attrinfo as $k => $v){
                //$v['attr_id'] 下标，就把相同的属性都分配设置到一起
                $z_attrinfo[$v['attr_id']]['goods_id'] = $v['goods_id'];
                $z_attrinfo[$v['attr_id']]['attr_id'] = $v['attr_id'];
                $z_attrinfo[$v['attr_id']]['attr_val'][] = $v['attr_val'];
                $z_attrinfo[$v['attr_id']]['attr_name'] = $v['attr_name'];
                $z_attrinfo[$v['attr_id']]['attr_sel'] = $v['attr_sel'];
                $z_attrinfo[$v['attr_id']]['attr_opt_vals'] = $v['attr_opt_vals'];
            }
            $z_attrinfo['flag'] =1;
            echo json_encode($z_attrinfo);
        }
    }
}

