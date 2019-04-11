<?php
namespace Admin\Controller;
use Tools\AdminController;

class AuthController extends AdminController {
    //列表展示
    function showlist(){
        //获取全部的权限列表信息
        $info = D('Auth')->order('auth_path')->select();
        $this -> assign('info',$info);

        //传递差异导航内容
        $daohang = array(
            'first' => '权限管理',
            'second' => '权限列表',
            'btn' => '添加',
            'btn_link' => U('tianjia'),
        );
        $this -> assign('daohang',$daohang);

        $this -> display();
    }

    //添加权限
    function tianjia(){
        $auth = new \Model\AuthModel();
        if(IS_POST){
            //auth_path 和 auth_level还不具备
            //在“瞻前顾后”机制的里边实现两个字段维护
            //添加数据完毕后维护两个字段
            //根据已有数据先生成一个记录信息
            $shuju = $auth -> create();
            if($auth -> add($shuju)){
                $this -> success('添加权限成功',U('showlist'),2);
            }else{
                $this -> error('添加权限失败',U('tianjia'),2);
            } 
        }else{
            //传递差异导航内容
            $daohang = array(
                'first' => '权限管理',
                'second' => '添加权限',
                'btn' => '返回',
                'btn_link' => U('showlist'),
            );
            $this -> assign('daohang',$daohang);

            //获得可供选取的上级(第1/2级别权限)
            $authinfo = $auth->
                where(array('auth_level'=>array('in','0,1')))->
                order('auth_path')->
                select();
            //SELECT * FROM `sp_auth` WHERE `auth_level` IN ('0','1') ORDER BY auth_path
            $this -> assign('authinfo',$authinfo);

            $this -> display();
        }
    }
}

