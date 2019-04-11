<?php // content="text/plain; charset=utf-8"
//线形图
require_once ('../jpgraph.php');
require_once ('../jpgraph_line.php');
// Some (random) data
$ydata = array(11,3,8,12,5,1,9,13,5,7,14,18);
// Size of the overall graph
$width=350;
$height=250;
// Create the graph and set a scale.
// These two calls are always required
$graph = new Graph($width,$height);
// 坐标轴刻度样式
$graph->SetScale('textlin');
// 设置图片“阴影”
$graph->SetShadow();
// Setup margin and titles
$graph->SetMargin(40,20,20,40);
$graph->title->Set('2015年各部分效益情况');
$graph->title->setFont(FF_SIMSUN,FS_BOLD,14);
$graph->subtitle->Set('(销售部门)');
$graph->subtitle->setFont(FF_SIMSUN);
$graph->subsubtitle->Set('5月份');
$graph->subsubtitle->setFont(FF_SIMSUN);
$graph->xaxis->title->Set('月份');
$graph->xaxis->title->setFont(FF_SIMSUN,FS_BOLD,10);
$graph->yaxis->title->Set('单位万元');
$graph->yaxis->title->setFont(FF_SIMSUN,FS_BOLD,10);
//$graph->yaxis->title->SetFont( FF_FONT1 , FS_BOLD );
//$graph->xaxis->title->SetFont( FF_FONT1 , FS_BOLD );
// Create the linear plot
$lineplot=new LinePlot($ydata);
$lineplot->SetColor( 'blue' );
$lineplot->SetWeight( 5 );   // Two pixel wide
// Add the plot to the graph
$graph->Add($lineplot);
// Display the graph
$graph->Stroke();
?>