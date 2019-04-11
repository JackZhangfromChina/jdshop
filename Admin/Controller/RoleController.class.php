<?php
namespace Admin\Controller;
use Tools\AdminController;

class RoleController extends AdminController {
    //角色列表展示
    function showlist(){
        //传递差异导航内容
        $daohang = array(
            'first' => '角色管理',
            'second' => '角色列表',
            'btn' => '添加',
            'btn_link' => U('tianjia'),
        );
        $this -> assign('daohang',$daohang);

        //获得全部的角色信息
        $info = D('Role')->select();
        $this -> assign('info',$info);
        $this -> display();
    }

    //给角色分配权限
    function distribute(){
        //传递差异导航内容
        $daohang = array(
            'first' => '角色管理',
            'second' => '分配权限',
            'btn' => '返回',
            'btn_link' => U('showlist'),
        );
        $this -> assign('daohang',$daohang);

        //分配权限的角色id
        $role_id = I('get.role_id');
        $role = new \Model\RoleModel();

        if(IS_POST){
            //sp_role表中的role_auth_ids和role_auth_ac需要
            //二期制作才可以存储到数据库
            $_POST['role_auth_ids'] = implode(',',$_POST['authid']);
            $shuju = $role -> create();
            if($role -> save($shuju)){
                $this -> success('分配成功',U('showlist'),2);
            }else{
                $this -> error('分配失败',U('distribute',array('role_id'=>$role_id)),2);
            } 
        }else{
            $role_info = $role->find($role_id);
            $this -> assign('role_info',$role_info);

            //获得顶、次级权限
            $auth_infoA = D('Auth')->
                where(array('auth_level'=>0))->
                field('auth_id,auth_name,auth_pid,auth_c,auth_a')->
                select();//顶级
            $auth_infoB = D('Auth')->
                where(array('auth_level'=>1))->
                field('auth_id,auth_name,auth_pid,auth_c,auth_a')->
                select();//次顶级
            $this -> assign('auth_infoA',$auth_infoA);
            $this -> assign('auth_infoB',$auth_infoB);

            $this -> display();
        }
    }
}

