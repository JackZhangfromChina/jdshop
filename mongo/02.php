<?php

//php操作mongo

//① 实例化MongoClient对象
//$m = new MongoClient("mongodb://[username:password@]host1[:port1][,host2[:port2:],...]/db");
//$m = new MongoClient("mongodb://localhost:27017/shop43"); //【没有】用户名和密码
$m = new MongoClient("mongodb://linken2:1234@localhost:27017/shop43"); //【有】用户名和密码
//var_dump($m);//object(MongoClient)#1 (4) { ["connected"]=> bool(true) ["status"]=> NULL ["server":protected]=> NULL ["persistent":protected]=> NULL } 


//② 选取操作的数据库use shop43
$s = $m -> shop43;

//③ 获取数据 db.goods.find();
$info = $s -> goods -> find();
//var_dump($info);//object(MongoCursor)#4 (0) { } 

//④ 遍历$info,输出各个"字段"信息
foreach($info as $k => $v){
    echo "名字:".$v['name']."---价格:".$v['price']."---颜色:".$v['color']."<br />";
}

