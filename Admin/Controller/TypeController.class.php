<?php
namespace Admin\Controller;
use Tools\AdminController;

class TypeController extends AdminController {
    //列表展示
    function showlist(){
        //传递差异导航内容
        $daohang = array(
            'first' => '类型管理',
            'second' => '类型列表',
            'btn' => '添加',
            'btn_link' => U('tianjia'),
        );
        $this -> assign('daohang',$daohang);

        //获得全部的类型信息
        $info = D('Type')->select();
        $this -> assign('info',$info);
        $this -> display();
    }
    //添加
    function tianjia(){
        $type = D('Type');
        if(IS_POST){
            $shuju = $type -> create();
            if($type->add($shuju)){
                $this -> success('添加类型成功',U('showlist'),1);
            }else{
                $this -> error('添加类型失败',U('tianjia'),1);
            } 
        }else{
            //传递差异导航内容
            $daohang = array(
                'first' => '类型管理',
                'second' => '类型列表',
                'btn' => '返回',
                'btn_link' => U('showlist'),
            );
            $this -> assign('daohang',$daohang);

            $this -> display();
        }
    }
}

