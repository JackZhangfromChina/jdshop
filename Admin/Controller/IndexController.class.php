<?php
namespace Admin\Controller;
use Tools\AdminController;
use Think\Controller;

class IndexController extends AdminController {
    //最大集成页
    public function index(){ 
        layout(false);
        C('SHOW_PAGE_TRACE',false);
        $this -> display(); }
    //头部
    public function top(){ 
        layout(false);
        C('SHOW_PAGE_TRACE',false);
        $this -> display(); }
    //脚部
    public function down(){ 
        layout(false);
        C('SHOW_PAGE_TRACE',false);
        $this -> display(); }
    //center部(左右集成)
    public function center(){ 
        layout(false);
         C('SHOW_PAGE_TRACE',false);
        $this -> display();}
    //左部
    public function left(){ 
        layout(false);
         C('SHOW_PAGE_TRACE',false);
        //获得用户对应角色的权限信息并展示
        //用户的id信息(session)--->用户id--->角色id--->权限信息
        $admin_id   = session('admin_id');
        $admin_name = session('admin_name');
        if($admin_name === 'admin'){
            //超级管理员admin获得并显示全部的权限
            $auth_infoA = D('Auth')->
                where(array('auth_level'=>0))->
                field('auth_id,auth_name,auth_pid,auth_c,auth_a')->
                select();//顶级
            $auth_infoB = D('Auth')->
                where(array('auth_level'=>1))->
                field('auth_id,auth_name,auth_pid,auth_c,auth_a')->
                select();//次顶级
        }else{
            //连表查询 sp_manager/sp_role
            //1) 获得角色对应的权限auth_ids信息
            $authids = D('Manager')->
                alias('m')->
                join('__ROLE__ r on m.role_id=r.role_id')->
                where(array('m.mg_id'=>$admin_id))->
                getField('r.role_auth_ids');
            //dump($authids);//string(11) "102,107,108"
            //2) 根据auth_ids获得具体权限信息
            $auth_infoA = D('Auth')->
                where(array('auth_id'=>array('in',$authids),'auth_level'=>0))->
                field('auth_id,auth_name,auth_pid,auth_c,auth_a')->
                select();//顶级
            //SELECT * FROM `sp_auth` WHERE `auth_id` IN ('102','107','108') AND `auth_level` = 0
            $auth_infoB = D('Auth')->
                where(array('auth_id'=>array('in',$authids),'auth_level'=>1))->
                field('auth_id,auth_name,auth_pid,auth_c,auth_a')->
                select();//次顶级
            //SELECT * FROM `sp_auth` WHERE `auth_id` IN ('102','107','108') AND `auth_level` = 1
        }

        $this -> assign('auth_infoA',$auth_infoA);
        $this -> assign('auth_infoB',$auth_infoB);


        $this -> display();
    }
    //右部
    public function right(){
        layout(false);
        $this -> display(); }
}
