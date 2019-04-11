<?php 
require_once ('../jpgraph.php');
require_once ('../jpgraph_bar.php');
// 定义数据
$databary=array(12,7,16,6,7,14,9,3,10);
$months=$gDateLocale->GetShortMonth();
// 创建对象
$graph = new Graph(350,250);
// 设置图片“阴影”
$graph->SetShadow();
// 坐标轴刻度样式
$graph->SetScale("textlin");
unset($months);
$months[] = "1月";
$months[] = "2月";
$months[] = "3月";
$months[] = "4月";
$months[] = "5月";
$months[] = "6月";
$months[] = "7月";
$months[] = "8月";
$months[] = "9月";
// 设置x轴坐标内容
$graph->xaxis->SetTickLabels($months);
$graph->xaxis->setFont(FF_SIMSUN);
// 设置x轴说明内容
$graph->xaxis->title->set('月份');
$graph->xaxis->title->setFont(FF_SIMSUN);
// 设置图片标题
$graph->title->Set("2015年各部门效益统计");
// 使用宋体字体  加粗属性
$graph->title->SetFont(FF_SIMSUN,FS_BOLD);
// 创建柱形图对象
$b1 = new BarPlot($databary);
// 设置浮动说明
$b1->SetLegend("单位(万)");
$graph->legend->Pos(0.1,0.1);//设置标题位置
//设置柱条宽度
$b1->SetAbsWidth(10);
$b1->SetShadow(); //设置阴影
// 柱形图片添加到确定的图片上
$graph->Add($b1);
// 最终输出图片
$graph->Stroke();
?>
