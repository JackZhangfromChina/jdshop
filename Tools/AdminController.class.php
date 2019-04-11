<?php

namespace Tools;
use Think\Controller; //空间类元素引入

class AdminController extends Controller{
    //构造方法，判断是否越权访问
    function __construct(){
        parent::__construct(); //先实现父类的构造方法
        $admin_id   = session('admin_id');
        $admin_name = session('admin_name');
        //正访问的权限
        $nowac = CONTROLLER_NAME."-".ACTION_NAME;
        if(!empty($admin_name)){
            //当前访问的权限 与 角色对应的权限做"对比"

            //当前用户角色对应的权限信息
            $role_auth_ac = D('Manager')->
                alias('m')->
                join('left join __ROLE__ r on m.role_id=r.role_id')->
                where(array('m.mg_id'=>$admin_id))->
                getField('r.role_auth_ac');

            //不需要控制，任何用户都可以访问的权限
            $allowac = "Manager-logout,Manager-login,Manager-verifyImg,Manager-checkVerify,Index-index,Index-left,Index-right,Index-center,Index-top,Index-down";

            //判断$role_auth_ac是否包含$nowac
            //strpos(s1,s2),在s1里边从左边开始判断s2第一次出现位置
            //              如果没有出现返回false，否则返回0/1/2/3...
            //具体控制：
            //① 权限是角色的一个
            //② 权限是默认允许的
            //③ 是admin超级管理员
            //以上3个条件都不成立，则“没有权限访问”
            if(strpos($role_auth_ac, $nowac)===false && strpos($allowac,$nowac)===false && $admin_name!=='admin'){
                exit('没有权限访问！');
            }

        }else{
            //有几个页面，无论是否是登录状态，都让访问
            $allowac = "Manager-login,Manager-verifyImg,Manager-checkVerify";
            if(strpos($allowac,$nowac)===false){
                //跳转到登录页面
                //$this -> redirect('Manager/login');
                //各个frame框架页都跳转(不是单独右侧跳转)
                //关键通过 window.top跳转
                $js = <<<eof
                    <script type="text/javascript">
                    window.top.location.href="/Admin/Manager/login";
                    </script>
eof;
                echo $js;
            }
        }
    }
}
