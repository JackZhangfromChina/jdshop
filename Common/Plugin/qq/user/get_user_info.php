<?php

require_once("../comm/config.php");

$_SESSION['access_token'] = $_GET['access_token'];
$_SESSION['appid'] = $_GET['appid'];
$_SESSION['openid'] = $_GET['openid'];

function get_user_info()
{
    $get_user_info = "https://graph.qq.com/user/get_user_info?"
        . "access_token=" . $_SESSION['access_token']
        . "&oauth_consumer_key=" . $_SESSION["appid"]
        . "&openid=" . $_SESSION["openid"]
        . "&format=json";

    $info = file_get_contents($get_user_info);
    $arr = json_decode($info, true);

    return $arr;
}

//获取用户基本资料
$arr = get_user_info();
echo json_encode($arr);

