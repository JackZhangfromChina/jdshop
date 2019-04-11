<?php
namespace Admin\Controller;
use Tools\AdminController;

class ManagerController extends AdminController {
    public function login(){
        layout(false);
        if(IS_POST){
            //① 校验验证码
            $very = new \Think\Verify();
            if($very -> check($_POST['chknumber'])){
                //② 用户名、密码校验
                $name = $_POST['user'];
                $pwd = $_POST['pwd'];
                $info = D('Manager')->where(array('mg_name'=>$name,'mg_pwd'=>$pwd))->find();
                if($info !== null){
                    //③ session持久化用户信息
                    session('admin_id',$info['mg_id']);
                    session('admin_name',$info['mg_name']);
                    //④ 页面跳转到后台
                    $this -> redirect('Index/index');
                }else
                echo "用户名或密码错误";
            }else
            echo "验证码不正确";
        }
        $this -> display();
    }   

    //退出系统
    function logout(){
        //清空session
        session(null);
        //跳转到登录页
        $this -> redirect('Manager/login');
    }

    //显示验证码
    function verifyImg(){
        $cfg = array(
            'imageH'    =>  30,               // 验证码图片高度
            'imageW'    =>  100,               // 验证码图片宽度
            'length'    =>  4,               // 验证码位数
            'fontttf'   =>  '4.ttf',              // 验证码字体，不设置随机获取
            'fontSize'  =>  12,              // 验证码字体大小(px)
            'useNoise'  =>  false,            // 是否添加杂点
        );
        $very = new \Think\Verify($cfg);
        $very -> entry();
    } 

    //客户端通过ajax，实现校验验证码
    function checkVerify(){
        $code = I('get.code');

        $very = new \Think\Verify();

        //Verify::check()方法对正确的验证码只允许验证一次
        //(验证成功的校验码就立即被删除了)
        //所以如下就是对check方法的具体实现
        $key = $this->auth_my_code($very,$very->seKey);
        // 验证码不能为空
        $secode = session($key);

        //对$code进行加密，在比较校验
        if($this->auth_my_code($very,strtoupper($code)) == $secode['verify_code']) {
            echo json_encode(array('flag'=>1,'cont'=>'验证码正确'));
        }else{
            echo json_encode(array('flag'=>2,'cont'=>'验证码错误'));
        }
    }

    private function auth_my_code($vry,$str){
        $key = substr(md5($vry->seKey), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }
}

