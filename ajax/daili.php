<?php

header("content-type:text/html;charset=utf-8");

//我是代理，去替ajax完成"跨域"请求
$url = "http://web.0911.com/ajax/01.php";

//两种方式curl 和 file_get_contents()
$cont = file_get_contents($url);

echo $cont;