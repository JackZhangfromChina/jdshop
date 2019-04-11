<?php
header("content-type:text/html;charset=utf-8");
//定义常量，方便静态资源的引入
define('SITE_URL','http://jdshop.test/');
define("CSS_URL",SITE_URL."Public/Home/style/");
define('JS_URL',SITE_URL."Public/Home/js/");
define('IMG_URL',SITE_URL."Public/Home/images/");
//后台
define('AD_CSS_URL',SITE_URL."Public/Admin/css/");
define('AD_JS_URL',SITE_URL."Public/Admin/js/");
define('AD_IMG_URL',SITE_URL."Public/Admin/images/");

//方便插件文件引入
define('PLUGIN_URL',SITE_URL."Common/Plugin/");

define('APP_DEBUG',true); //开启调试模式


//引入tp框架的接口文件
include("ThinkPHP/ThinkPHP.php");