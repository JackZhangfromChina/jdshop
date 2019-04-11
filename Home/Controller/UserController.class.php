<?php
namespace Home\Controller;
use Tools\HomeController;

class UserController extends HomeController {
    //@params $ajax:1非ajax请求    2ajax请求
    public function login($ajax = 1){
        $user = D('User');
        if(IS_POST){
            //判断用户名、密码是否有效
            $name = $_POST['user_name'];
            $pwd = $_POST['user_pwd'];
            $userinfo = $user -> where(array('user_name'=>$name,'user_pwd'=>$pwd))->find();//null或array()
            if($userinfo!== null){
                if($userinfo['user_check']==1){
                    //session持久化用户信息
                    session('user_id',$userinfo['user_id']);
                    session('user_name',$userinfo['user_name']);

                    //把当前登录的用户信息存储给redis

                    //相同用户重复登录系统，删除旧的
                    $this->redis43 -> lrem('loginname',$userinfo['user_name'],0);
                    //在list链表中只存储一个名字即可
                    $this->redis43 -> lpush('loginname',$userinfo['user_name']);
                    //链表中只保留最新的5个数据
                    $this->redis43 -> ltrim('loginname',0,4);

                    //判断是否有定义回跳地址
                    if(session('?back_url') && $ajax==1){
                        $back_url = session(C('BACK_URL'));
                        session(C('BACK_URL'),null);//清除回跳地址
                        $this -> redirect($back_url);
                    }

                    if($ajax==1){
                        //跳转到首页
                        $this -> redirect('Index/index');
                    }else{
                        echo json_encode(array('flag'=>'登录成功'));
                        exit;
                    }
                }else{
                    $this -> error('账号未邮件激活',U('Index/index'));
                }
            }else
                echo "用户名或密码错误";
        }
        $this -> display();
    }  

    //判断用户是否登录系统
    function isLogin(){
        $user_name = session('user_name');
        if($user_name){
            echo json_encode(array('flag'=>1)); //有登录系统
        }else{
            echo json_encode(array('flag'=>2)); //未登录系统
        }
    }

    //qq登录系统
    function qq_login(){

        $access_token = $_SESSION['access_token'];
        $appid = $_SESSION['appid'];
        $openid = $_SESSION['openid'];
        //① 通过相关session信息(appid/appkey/openid)就可以获得qq账号的信息了
        //调用qq/user/get_user_info.php接口
        $url = "http://www.51lfgl.cn/Common/Plugin/qq/user/get_user_info.php?access_token=$access_token&appid=$appid&openid=$openid";
        //对$url进行跨域请求
        //file_get_contents()对其他地址进行请求的时候，“不能传递session信息”
        //需要通过get方式设置
        $info = file_get_contents($url);
        
        //获得昵称信息
        $arr_info = json_decode($info,true);
 
        //②把获得的qq账号信息存储给数据库
        //  存储：nickname  gender  openid
        $exists = D('User')->where(array('openid'=>$openid))->find();
        if($exists === null){
            $arr = array(
                'user_name' => $arr_info['nickname'],
                'user_sex' => $arr_info['gender'],
                'openid' => $openid,
                'add_time'=>time(),
            );
            $newid = D('User')->add($arr);
            session('user_id',$newid);
            session('user_name',$arr_info['nickname']);
        }else{
            $arr = array(
                'user_id' => $exists['user_id'],
                'user_name' => $arr_info['nickname'],
                'user_sex' => $arr_info['gender'],
            );
            D('User')->save($arr);
            //③ 把qq账号给session持久化操作
            session('user_id',$exists['user_id']);
            session('user_name',$arr_info['nickname']);
        }
        //③qq登录成功 
        $this -> redirect('Index/index');
    }  

    function logout(){
        session(null);
        $this -> redirect('Index/index');
    }

    //会员注册
    public function register(){
        $user = new \Model\UserModel();
        if(IS_POST){
            $shuju = $user -> create();
            if($user -> add($shuju)){
                $this -> redirect('Index/index');
            }else{
                $this -> redirect('register');
            }
        }else{
            $this -> display();
        }
    }

    //邮箱激活账号
    function activeS(){
        $user_id = I('get.user_id');
        $checkcode = I('get.checkcode');
        //①判断传递的checkcode  与 $user_id拥有的一致
        //②账号处于未激活状态
        $userinfo = D('User')->find($user_id);
        if($userinfo && $userinfo['user_check_code']===$checkcode && $userinfo['user_check']==0){
            //③规定用户在两天时间内过来激活账号
            if(time()-$userinfo['add_time']<3600*24*2){
                //激活账号：user_check=1  user_check_code=''
                $arr = array(
                    'user_id' => $user_id,
                    'user_check' => 1,
                    'user_check_code' => '',
                );
                if(D('User')->save($arr)){
                    $this -> success('恭喜您已经激活账号',U('Index/index'),1);
                }
            }else
                exit('账号已经过期');
            
        }else{
            exit('非法账号激活');
        }

    }
}
