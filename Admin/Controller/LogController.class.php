<?php
namespace Admin\Controller;
use Tools\AdminController;

class LogController extends AdminController {
    //操作日志展示
    function showlist(){
        //传递差异导航内容
        $daohang = array(
            'first' => '日志管理',
            'second' => '日志列表',
            'btn' => '日志列表',
            'btn_link' => U('showlist'),
        );
        $this -> assign('daohang',$daohang);

        //在mongo中把操作日志给获取出来
        //完全限定名称 方式实例化mongoclient对象
        $m = new \MongoClient("mongodb://linken2:1234@localhost:27017/shop43");
        $s = $m -> shop43;
        //时间time 降序 获得数据信息
        $info = $s -> log -> find()->sort(array('time'=>-1));

        //把$info对象 变为 $tmp的数组
        $tmp = array();
        $i = 1;
        foreach($info as $k => $v){
            $tmp[$i]['ip']          = $v['ip'];
            $tmp[$i]['operator']    = $v['operator'];
            $tmp[$i]['time']        = $v['time'];
            $tmp[$i]['content']     = $v['content'];
            $i++;
        }

        $this -> assign('info',$tmp);
        $this -> display();
    }
}
