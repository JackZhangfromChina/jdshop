<?php

//php操作mongo

//① 实例化MongoClient对象
$m = new MongoClient();

//② 选取操作的数据库use shop43
$s = $m -> shop43;

//【删除mongo的记录、字段】
//删除"name=iphone3"的记录信息
$s -> goods -> remove(array('name'=>'iphone3'));

//删除"name=iphone1"的“price”字段
$s -> goods -> update(array('name'=>'iphone1'),array('$unset'=>array('price'=>1)));



