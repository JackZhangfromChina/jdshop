<?php

namespace Tools;
use Think\Controller; //空间类元素引入

class HomeController extends Controller{
    protected $redis43 = null; //声明一个redis操作对象

    //构造方法，判断是否越权访问
    function __construct(){
        //连接redis，并创建主、从对象
        $this -> redis43 = new \Tools\MyRedis(true);
        // $this -> redis43 -> connect(array('host'=>'127.0.0.1','port'=>6379,'auth'=>'php432016'),true);
        $this -> redis43 -> connect(array('host'=>'127.0.0.1','port'=>6379,'auth'=>''),false);



        parent::__construct(); //先实现父类的构造方法
        // //① 实例化memcache对象
        // $m = new \Memcache();

        // //② 设置集群memcache
        // $m -> addServer('192.168.18.89',11211);
        // $m -> addServer('192.168.18.90',11211);

        // $nameA = "categoryA";
        // $nameB = "categoryB";
        // $nameC = "categoryC";

        // $catInfoA = $m -> get($nameA); //获得memcache数据
        // if(empty($catInfoA)){
        //     //memcache没有数据就走数据库
        //     echo "数据库A";
        //     $catInfoA = D('Category')->where(array('cat_level'=>'0'))->select();
        //     //把获得的数据填充给memcache
        //     $m -> set($nameA,$catInfoA,0,0);
        // }        
        // $catInfoB = $m -> get($nameB);
        // if(empty($catInfoB)){
        //     $catInfoB = D('Category')->where(array('cat_level'=>'1'))->select();
        //     //把获得的数据填充给memcache
        //     $m -> set($nameB,$catInfoB,0,0);
        // }        
        // $catInfoC = $m -> get($nameC);
        // if(empty($catInfoC)){
        //     $catInfoC = D('Category')->where(array('cat_level'=>'2'))->select();
        //     //把获得的数据填充给memcache
        //     $m -> set($nameC,$catInfoC,0,0);
        // }
$catInfoA = D('Category')->where(array('cat_level'=>'0'))->select();
$catInfoB = D('Category')->where(array('cat_level'=>'1'))->select();
$catInfoC = D('Category')->where(array('cat_level'=>'2'))->select();
        // //实现所有控制器都需要的信息(商品分类信息)
        // //获得3个级别的分类信息
        $this -> assign('catInfoA',$catInfoA);
        $this -> assign('catInfoB',$catInfoB);
        $this -> assign('catInfoC',$catInfoC);
    }
}
