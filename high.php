<?php
header("content-type:text/html;charset=utf-8");
//大家抢购id=5的商品信息
$link = mysql_connect('localhost','root','123456');
mysql_select_db('shop43',$link);
mysql_query('set name utf8');

//设置锁
//Myisam的表锁定(类型：写锁)
mysql_query('lock tables goods1 write');

$sql = "select number from goods1 where id=5";
$qry = mysql_query($sql);
$rst = mysql_fetch_assoc($qry);
$num = $rst['number'];  //该商品目前剩余数量
if($num>0){
    //每个用户限购一个商品
    $num--;
    $sql2 = "update goods1 set number='$num' where id=5";
    $qry2 = mysql_query($sql2);
}else{
    echo "库存不足!";
}
//释放锁
mysql_query('unlock tables');
