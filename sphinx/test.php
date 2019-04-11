<?php
// --------------------------------------------------------------------------
// File name   : test_coreseek.php
// Description : coreseek中文全文检索系统测试程序
// Requirement : PHP5 (http://www.php.net)
//
// Copyright(C), HonestQiao, 2011, All Rights Reserved.
//
// Author: HonestQiao (honestqiao@gmail.com)
//
// 最新使用文档，请查看：http://www.coreseek.cn/products/products-install/
//
// --------------------------------------------------------------------------
header("content-type:text/html;charset=utf-8");
require ( "sphinxapi.php" );

$cl = new SphinxClient ();
$cl->SetServer ( '127.0.0.1', 9312);
$cl->SetArrayResult ( true );
$cl->SetMatchMode ( SPH_MATCH_ANY);//给匹配"任何分词"对应内容
//Query(被查询关键字，索引名称)
//在dizhi索引里边对两个字段进行查询：comname/comaddress
$key = '太平洋服饰';
$index_name = 'dizhi';

//给做分页设置
$cl -> setLimits(0,10);

$res = $cl->Query ( $key, $index_name );
//print_r($cl);//输处sphinx对象，如果提示“由于目标计算机积极拒绝，无法连接”，就需要开启searched服务

if(!empty($res['matches'])){
    printf("<h3>当前查找的关键字是<span style='color:blue;'>%s</span>,搜寻的时间是<span style='color:red;'>【%s】</span>,索引的名称为<span style='color:green;'>%s</span>,有返回<span style='color:red;'>%d</span>条信息</h3>",$key,$res['time'],$index_name,$res['total']);

    //把关键字对应的记录结果给体现出来
    //获得关键字对应的"主键id"值，通过主键id值去mysql获得记录信息
    //遍历$res['matches'] 从中获得id信息
    $ids = array();
    foreach($res['matches'] as $k => $v){
        $ids[] = $v['id'];
    }
    $ids = implode(',',$ids);//array-->string
    //echo $ids;//40181,651,1459,8886,17876,19428,24901,47372,49718,985,1209
    //根据$ids获得数据库的相关记录信息
    $link = mysql_connect('localhost','root','123456');
    mysql_select_db('shop43',$link);
    mysql_query('set names utf8');
    $sql = "select id,comname,comaddress from address where id in ($ids) order by find_in_set(id,'$ids')";
    $qry = mysql_query($sql);
    
    //把结果设置到table表格里边
    echo <<<eof
        <style type="text/css">
        table{width:700px; margin:auto; border:1px solid black; border-collapse:collapse;}
        td {border:1px solid black;}
        </style>
        <table>
            <tr style='font-weight:bold;'><td>id</td><td>公司名称</td><td>公司地址</td></tr>
eof;

    while($rst = mysql_fetch_assoc($qry)){
        //给被查找的关键字设置语法高亮
        //SphinxClient::buildExcerpts ( array $docs被处理信息 , string $index索引名称 , string $words关键字 [, array $opts语法高亮的样式标签设置 ] )
        $row = $cl->buildExcerpts($rst,$index_name,$key,
                array('before_match'=>"<span style='color:blue;'>",'after_match'=>"</span>"));
        //此时显示的信息就通过$row体现，其是一个"索引"数组

        printf("<tr>");
        printf("<td>%d</td>",$row[0]);
        printf("<td>%s</td>",$row[1]);
        printf("<td>%s</td>",$row[2]);
        printf("</tr>");
    }
    echo "</table>";
    
}else{
    echo "没有找到对应内容";
}