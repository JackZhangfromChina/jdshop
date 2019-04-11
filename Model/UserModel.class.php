<?php
namespace Model;
use Think\Model;

class UserModel extends Model {

    //自动完成
    // 自动完成定义
    protected $_auto            =   array(
        //array(完成字段1,完成规则,[完成条件,附加规则]),
        array('add_time','time',1,'function'),  //添加数据填充
    );

    // 插入成功后的回调方法
    protected function _after_insert($data,$options) {
        //知道给谁发送邮件
        /*dump($data);
        array(5) {
          ["user_name"] => string(4) "mary"
          ["user_pwd"] => string(3) "123"
          ["user_email"] => string(12) "mary@163.com"
          ["add_time"] => int(1460000995)
          ["user_id"] => string(1) "8"
        }*/
        //邮箱激活账号(激活谁、激活码[确保准确激活账号、有校验对比效果])
        //http://web.shop43.com/Home/User/active/user_id/102/checkcode/skdsld293293sldk
        //制作一个激活码
        $checkcode = md5($data['user_name'].time().$data['user_email']);
        $arr = array(
            'user_id' => $data['user_id'],
            'user_check_code' => $checkcode,
        );
        $this -> save($arr);//把激活码更新到当前会员记录字段边

        $linkurl = "http://web.shop43.com/Home/User/activeS/user_id/".$data['user_id']."/checkcode/".$checkcode;
        $msg = "请点击以下超链接，激活账号<br /><p><a href='".$linkurl."'>".$linkurl."</a></p>";
        \sendMail($data['user_email'],'激活账号',$msg);
    }
}

