<?php
namespace Admin\Controller;
use Tools\AdminController;

class SaleController extends AdminController {
    //绘制销售图片[验证码Manager/verifyImg]
    function imgshow(){
        require_once ('./Common/Plugin/jpgraph/jpgraph.php');
        require_once ('./Common/Plugin/jpgraph/jpgraph_bar.php');
        //获得数据库里边真实的销售收入数据
        //select month(order_time) as mth,sum(order_price) tot from sp_order group by mth

        //要根据“年份”获得销售统计情况
        $myyear = I('get.myyear');

        $sql = "select year(order_time) nian,month(order_time) as mth,sum(order_price) tot from sp_order group by mth having nian='$myyear'";
        $monthmoney = D()->query($sql);
        $months = \arraytoString($monthmoney,'mth');
        $moneys = \arraytoString($monthmoney,'tot');
        //dump($months);//string(15) "1,2,3,4,5,7,8,9"
        //dump($moneys);//string(55) "905.00,4400.00,182.00,126.00,465.00,2.00,134.00,9167.00"
        //exit;

        // 定义数据
        //$databary=array(12,7,16,6,7,14,9,3,10);
        $databary=explode(',',$moneys);
        // 创建对象
        $graph = new \Graph(350,250);
        // 设置图片“阴影”
        $graph->SetShadow();
        // 坐标轴刻度样式
        $graph->SetScale("textlin");
 
        $tmp_month = explode(',',$months);
        $months = array();
        foreach($tmp_month as $k => $v){
            $months[] = $v."月";
        }
        

        // 设置x轴坐标内容
        $graph->xaxis->SetTickLabels($months);
        $graph->xaxis->setFont(FF_SIMSUN);
        // 设置x轴说明内容
        $graph->xaxis->title->set('月份');
        $graph->xaxis->title->setFont(FF_SIMSUN);
        // 设置图片标题
        $graph->title->Set($myyear."年各月份销售统计");
        // 使用宋体字体  加粗属性
        $graph->title->SetFont(FF_SIMSUN,FS_BOLD);
        // 创建柱形图对象
        $b1 = new \BarPlot($databary);
        // 设置浮动说明
        $b1->SetLegend("单位(万)");
        ////设置标题位置（右上角为0,0坐标）
        //$graph->legend->Pos(x轴0-1,y轴0-1);
        $graph->legend->Pos(0.5,0.3);//设置标题位置
        //设置柱条宽度
        $b1->SetAbsWidth(20);
        $b1->SetShadow(); //设置阴影
        // 柱形图片添加到确定的图片上
        $graph->Add($b1);
        // 最终输出图片
        $graph->Stroke();
    }

    //展示销售统计情况[登录Manager/login]
    function tongji(){
        //传递差异导航内容
        $daohang = array(
            'first' => '统计管理',
            'second' => '每个月销售情况',
            'btn' => '销售统计',
            'btn_link' => U('tongji'),
        );

        $year = date('Y'); //目前年份
        $year = I('get.nowyear',$year);//如果不传递get参数nowyear，
                //就使用当前年份
        $this -> assign('nowyear',$year);

        $this -> assign('daohang',$daohang);
        $this -> display();
    }
}

