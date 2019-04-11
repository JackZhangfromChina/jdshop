<?php

//php操作mongo

//① 实例化MongoClient对象
$m = new MongoClient();

//② 选取操作的数据库use shop43
$s = $m -> shop43;

//【给mongo写入数据】
$data = array('name'=>'jim','height'=>175,'address'=>'北京');
$z = $s -> user -> insert($data);
var_dump($z);//array(4) { ["n"]=> int(0) ["connectionId"]=> int(2) ["err"]=> NULL ["ok"]=> float(1) } 
