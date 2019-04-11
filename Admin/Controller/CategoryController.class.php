<?php
namespace Admin\Controller;
use Tools\AdminController;

class CategoryController extends AdminController {
    //列表展示
    function showlist(){
        //获取全部的分类列表信息
        $info = D('Category')->order('cat_path')->select();
        $this -> assign('info',$info);

        //传递差异导航内容
        $daohang = array(
            'first' => '分类管理',
            'second' => '分类列表',
            'btn' => '添加',
            'btn_link' => U('tianjia'),
        );
        $this -> assign('daohang',$daohang);

        $this -> display();
    }

    //添加分类
    function tianjia(){
        $Category = new \Model\CategoryModel();
        if(IS_POST){
            //Category_path 和 Category_level还不具备
            //在“瞻前顾后”机制的里边实现两个字段维护
            //添加数据完毕后维护两个字段
            //根据已有数据先生成一个记录信息
            $shuju = $Category -> create();
            if($Category -> add($shuju)){
                $this -> success('添加分类成功',U('showlist'),2);
            }else{
                $this -> error('添加分类失败',U('tianjia'),2);
            } 
        }else{
            //传递差异导航内容
            $daohang = array(
                'first' => '分类管理',
                'second' => '添加分类',
                'btn' => '返回',
                'btn_link' => U('showlist'),
            );
            $this -> assign('daohang',$daohang);

            //获得可供选取的上级(第1/2级别分类)
            $catinfo = $Category->
                where(array('cat_level'=>array('in','0,1')))->
                order('cat_path')->
                select();
            $this -> assign('catinfo',$catinfo);
            //获得可供选取的上级(第1/2级别分类)

            $this -> display();
        }
    }

    //获取次级分类信息
    function getCatInfoB(){
        $cat_ida = I('get.cat_ida');

        //通过"cat_pid"就可以找到当前分类下的次级分类信息
        $catinfo = D('Category')
            ->where(array('cat_pid'=>$cat_ida))
            ->field('cat_id,cat_name')
            ->select();
        echo json_encode($catinfo); //[{},{},{}...]
        //[Object { cat_id="5", cat_name="电子书"}, Object { cat_id="6", cat_name="数字音乐"}, Object { cat_id="7", cat_name="音像"}]
    }
}

