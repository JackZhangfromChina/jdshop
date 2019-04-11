<?php

//php操作mongo

//① 实例化MongoClient对象
$m = new MongoClient();

//② 选取操作的数据库use shop43
$s = $m -> shop43;

//【修改mongo的数据】
//$s -> user -> update({条件}，{'$set',修改后的字段内容})
$z = $s -> user -> update(array('name'=>'jim'),array('$set'=>array('height'=>165,'address'=>'上海')));
var_dump($z);//array(5) { ["updatedExisting"]=> bool(true) ["n"]=> int(1) ["connectionId"]=> int(2) ["err"]=> NULL ["ok"]=> float(1) } 

//获得数据
echo "<hr />";
var_dump($s -> user->findOne());
