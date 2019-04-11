<?php
namespace Admin\Controller;
use Tools\AdminController;

class GoodsController extends AdminController {
    function showlist(){
       //传递差异导航内容
        $daohang = array(
            'first' => '商品管理',
            'second' => '商品列表',
            'btn' => '添加',
            'btn_link' => U('tianjia'),
        );
        $this -> assign('daohang',$daohang);

        //获得商品列表信息
        $goods = D('Goods');

        //给商品列表制作分页效果
        //① 获得记录总条数
        //聚合函数：count() sum()  avg() max()  min()
        //SELECT COUNT(*) AS tp_count FROM `sp_goods` LIMIT 1
        $total = $goods->count('goods_id');  //sum() avg() max() min()
        //exit('123');
        $per = 5;

        //② 实例化分页类对象
        $page = new \Tools\Page($total,$per);

        //③ 获取每页记录信息
        //   limit(偏移量，长度)
        $info = D('Goods')
            ->limit($page->offset,$per)
            ->order('goods_id desc')
            ->select();

        //④ 获得页码列表信息
        $pagelist = $page -> fpage();
        $this -> assign('pagelist',$pagelist);

        $this -> assign('info',$info);
        $this -> display();
    }

    //把一页一页的商品信息导出到excel里边
    function excel_out_page(){
        //通过excel导出当前页的商品信息

        //设置当前页码
        $nowpage = !empty($_GET['page'])?$_GET['page']:1;//当前页码
        $per = 5;

        //根据页码获得数据信息出来
        $info = D('Goods')
            ->limit(($nowpage-1)*$per,$per)
            ->order('goods_id desc')
            ->select();

        //设置Excel
        require_once './Common/Plugin/Excel/PHPExcel.php';
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                             ->setLastModifiedBy("Maarten Balliauw")
                             ->setTitle("PHPExcel Test Document")
                             ->setSubject("PHPExcel Test Document")
                             ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                             ->setKeywords("office PHPExcel php")
                             ->setCategory("Test result file");

        //设置标题
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '记录id')
            ->setCellValue('B1', '商品名称')
            ->setCellValue('C1', '商品价格')
            ->setCellValue('D1', '商品数量')
            ->setCellValue('E1', '商品重量');
        //设置具体内容
        foreach($info as $k => $v){
            $i = $k+2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $v['goods_id'])
                ->setCellValue('B'.$i, $v['goods_name'])
                ->setCellValue('C'.$i, $v['goods_shop_price'])
                ->setCellValue('D'.$i, $v['goods_number'])
                ->setCellValue('E'.$i, $v['goods_weight']);
        }

        //$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //$objWriter->save('./Public/Excel/'.$nowpage.'_goods_page'.time().'.xlsx');
        //echo "excel文件创建成功";

$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
header("Pragma: public");
header("Expires: 0");
header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
//设置http协议，下载支持
header("Content-Type:application/force-download");
//设置下载的内容是excel
header("Content-Type:application/vnd.ms-execl");
//把下载的内容设置为8机制流内容
header("Content-Type:application/octet-stream");
//设置http协议，下载支持
header("Content-Type:application/download");
//下载excel名字的定义
header('Content-Disposition:attachment;filename="商品信息.xlsx"');
//内容设置为二进制形式传输
header("Content-Transfer-Encoding:binary");
//把excel文件直接提供为下载形式
$objWriter->save('php://output');
    }

    //给选中的商品信息生成excel文件
    function makeExcel(){
        $goods_ids = I('get.goods_ids');
        $goods_ids = rtrim($goods_ids,',');
        $info = D('Goods')->order('goods_id desc')->select($goods_ids);
        //设置Excel
        require_once './Common/Plugin/Excel/PHPExcel.php';
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                             ->setLastModifiedBy("Maarten Balliauw")
                             ->setTitle("PHPExcel Test Document")
                             ->setSubject("PHPExcel Test Document")
                             ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                             ->setKeywords("office PHPExcel php")
                             ->setCategory("Test result file");

        //设置标题
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '记录id')
            ->setCellValue('B1', '商品名称')
            ->setCellValue('C1', '商品价格')
            ->setCellValue('D1', '商品数量')
            ->setCellValue('E1', '商品重量');
        //设置具体内容
        foreach($info as $k => $v){
            $i = $k+2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $v['goods_id'])
                ->setCellValue('B'.$i, $v['goods_name'])
                ->setCellValue('C'.$i, $v['goods_shop_price'])
                ->setCellValue('D'.$i, $v['goods_number'])
                ->setCellValue('E'.$i, $v['goods_weight']);
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('./Public/Excel/goods_page_range_'.time().'.xlsx');
        echo "给指定的商品信息生成excel文件成功";
    }

    //给选中的商品信息生成excel文件
    function makeExcel2(){
        $goods_ids = I('get.goods_ids');
        $goods_ids = rtrim($goods_ids,',');
        $info = D('Goods')->order('goods_id desc')->select($goods_ids);
        //设置Excel
        require_once './Common/Plugin/Excel/PHPExcel.php';
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                             ->setLastModifiedBy("Maarten Balliauw")
                             ->setTitle("PHPExcel Test Document")
                             ->setSubject("PHPExcel Test Document")
                             ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                             ->setKeywords("office PHPExcel php")
                             ->setCategory("Test result file");

        //设置标题
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '记录id')
            ->setCellValue('B1', '商品名称')
            ->setCellValue('C1', '商品价格')
            ->setCellValue('D1', '商品数量')
            ->setCellValue('E1', '商品重量');
        //设置具体内容
        foreach($info as $k => $v){
            $i = $k+2;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $v['goods_id'])
                ->setCellValue('B'.$i, $v['goods_name'])
                ->setCellValue('C'.$i, $v['goods_shop_price'])
                ->setCellValue('D'.$i, $v['goods_number'])
                ->setCellValue('E'.$i, $v['goods_weight']);
        }

        //$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //$objWriter->save('./Public/Excel/goods_page_range_'.time().'.xlsx');
        //echo "给指定的商品信息生成excel文件成功";
$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
header("Pragma: public");
header("Expires: 0");
header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
//设置http协议，下载支持
header("Content-Type:application/force-download");
//设置下载的内容是excel
header("Content-Type:application/vnd.ms-execl");
//把下载的内容设置为8机制流内容
header("Content-Type:application/octet-stream");
//设置http协议，下载支持
header("Content-Type:application/download");
//下载excel名字的定义
header('Content-Disposition:attachment;filename="商品信息-选中.xlsx"');
//内容设置为二进制形式传输
header("Content-Transfer-Encoding:binary");
//把excel文件直接提供为下载形式
$objWriter->save('php://output');
    }

    public function tianjia(){

        $goods = new \Model\GoodsModel();
        if(IS_POST){
            $shuju = $goods -> create();  //I('post.')
            //还原富文本编辑器内容
            //使用htmlpurifier对编辑器内容进行防止xss攻击过滤
            $shuju['goods_introduce'] = \fanXSS($_POST['goods_introduce']);
            $this -> logo_deal($shuju);  //对logo图片进行上传处理
            if($newid = $goods->add($shuju)){
                $this -> pics_deal($newid);  //对相册图片进行上传处理

                \inputLog('添加商品信息-goods_id:'.$newid);

                $this -> success('添加商品成功',U('showlist'),2);
            }else{
                $this -> error('添加商品失败',U('tianjia'),2);
            } 
        }else{
            //传递差异导航内容
            $daohang = array(
                'first' => '商品管理',
                'second' => '添加商品',
                'btn' => '返回',
                'btn_link' => U('showlist'),
            );
            $this -> assign('daohang',$daohang);

            /****获得可选择的“类型”信息****/
            $attrinfo = D('Type')->select();
            $this -> assign('attrinfo',$attrinfo);
            /****获得可选择的“类型”信息****/

            /****获得可选择的“主分类”信息****/
            $cat_a_info = D('Category')
                    ->where(array('cat_level'=>'0'))
                    ->select();
            $this -> assign('cat_a_info',$cat_a_info);
            /****获得可选择的“主分类”信息****/

            $this -> display();
        }
    }

    //参数：$data是引用传递,在内部对其进行修改，在外边仍然可以访问到
    private function logo_deal(&$data){
        //判断有进行正常的附加上传
        if($_FILES['logo_tu']['error']===0){
            //判断是否是“更新”商品的logo处理
            //并删除旧的logo图片
            if(!empty($data['goods_id'])){
                $logo_info = D('Goods')->
                    field('goods_big_logo,goods_small_logo')->
                    find($data['goods_id']);
                unlink($logo_info['goods_big_logo']);
                unlink($logo_info['goods_small_logo']);
            }

            //A.大图logo处理
            //tp框架现成功能类实现附件上传(Think\Upload.class.php)
            //保存附件图片的根目录
            $cfg = array(
                'rootPath'      =>  './Public/Upload/', //保存根路径
                'exts'          =>  array('jpg','jpeg','png','gif'), //允许上传的文件后缀
            );
            $up = new \Think\Upload($cfg);

            //通过uploadOne的返回值可以获得附件上传到服务器的情况信息
            //例如：存储目录、存储名字等
            $z = $up -> uploadOne($_FILES['logo_tu']);

            //整理附件的路径 和 名字信息，存储到数据库指定字段里边
            $biglogoname = $up->rootPath.$z['savepath'].$z['savename'];
            $data['goods_big_logo'] = $biglogoname;//存储到数据库
            
            //B.缩略图logo处理，具体通过\Think\Image.class.php实现
            $im = new \Think\Image();
            $im -> open($biglogoname); //找到大图
            $im -> thumb(160,160,2);//制作缩略图
            //存储制作好的缩略图(事先声明好缩略图的路径名)
            //./Public/Upload/2016-03-29/56f9e9c66664f.jpg
            //./Public/Upload/2016-03-29/s_56f9e9c66664f.jpg
            $smalllogoname = $up->rootPath.$z['savepath']."s_".$z['savename'];
            $im -> save($smalllogoname);
            $data['goods_small_logo'] = $smalllogoname; //存储到数据库
        }
    }

    private function pics_deal($goods_id){
        //判断是否有上传相册，遍历error的信息即可，只有有一个为"0"
        //就给做上传处理
        $up_pics = false;
        foreach($_FILES['pics_tu']['error'] as $k => $v){
            if($v === 0){
                $up_pics = true;
                break;
            }
        }
        if($up_pics === true){
            $cfg = array(
                'rootPath'      =>  './Public/UploadPics/', //保存根路径
                'exts'          =>  array('jpg','jpeg','png','gif'), //允许上传的文件后缀
            );
            $up = new \Think\Upload($cfg);
            
            //$_FILES: logo_tu和pics_tu两部分附件
            //upload(array('pics_tu'=>array(name=>123,error=>,tmp_name=>,size=>,type=>)))
            $z = $up -> upload(array('pics_tu'=>$_FILES['pics_tu']));
            //遍历$z,并制作缩略图，完成sp_goods_pics数据表的记录填充
            $arr = array();
            foreach($z as $m => $n){
                //小图：50*50  中图:350*350  大图:800*800
                $im = new \Think\Image();
                $yuan_pics = $up->rootPath.$n['savepath'].$n['savename'];
                $im -> open($yuan_pics);
                $im -> thumb(800,800,6);
                $big_pics = $up->rootPath.$n['savepath'].'b_'.$n['savename'];
                $im -> save($big_pics);
                $im -> thumb(350,350,6);
                $mid_pics = $up->rootPath.$n['savepath'].'m_'.$n['savename'];
                $im -> save($mid_pics);
                $im -> thumb(50,50,6);
                $small_pics = $up->rootPath.$n['savepath'].'s_'.$n['savename'];
                $im -> save($small_pics);

                //完成sp_goods_pics数据表的记录填充
                $arr['goods_id'] = $goods_id;
                $arr['goods_pic_sma'] = $small_pics;
                $arr['goods_pic_mid'] = $mid_pics;
                $arr['goods_pic_big'] = $big_pics;
                D('GoodsPic')->add($arr);
            }
        }

    }

    public function upd(){
        $goods_id = I('get.goods_id');
        $goods = new \Model\GoodsModel();
        if(IS_POST){

            $shuju = $goods -> create();

            //制作原始复选框数据，之后与form表单数据做合并
            $yuan_qiang = array(
                'goods_is_qiang' => '不抢',
                'goods_is_hot' => '不热',
                'goods_is_rec' => '不推荐',
                'goods_is_new' => '不新品',
            );
            //下标一致的元素，后者覆盖前者
            $shuju = array_merge($yuan_qiang,$shuju);
            
            $shuju['goods_introduce'] = \fanXSS($_POST['goods_introduce']);
            $this -> logo_deal($shuju); //修改操作，logo处理
            $this -> pics_deal($goods_id);//再次上传相册
            if($goods -> save($shuju)){
            
                \inputLog('修改商品信息-goods_id:'.$shuju['goods_id']);

                $this -> success('修改商品成功',U('showlist'),2);
            }else{
                $this -> error('修改商品失败',U('upd',array('goods_id'=>$goods_id)),2);
            } 
        }else{
            //传递差异导航内容
            $daohang = array(
                'first' => '商品管理',
                'second' => '修改商品',
                'btn' => '返回',
                'btn_link' => U('showlist'),
            );
            $this -> assign('daohang',$daohang);

            /****获得可选择的“类型”信息****/
            $typeinfo = D('Type')->select();
            $this -> assign('typeinfo',$typeinfo);
            /****获得可选择的“类型”信息****/

            //查询被修改的商品信息
            $info = $goods->find($goods_id);
            $this -> assign('info',$info);

            /****获得可选择的“主分类”信息****/
            $cat_a_info = D('Category')
                    ->where(array('cat_level'=>'0'))
                    ->select();
            $this -> assign('cat_a_info',$cat_a_info);
            /****获得可选择的“主分类”信息****/

            /****获得商品已经设置的扩展分类(2级别)信息****/
            $catextinfo = D('GoodsCat')
                ->where(array('goods_id'=>$goods_id))
                ->order('cat_id')
                ->select();
            //遍历$catextinfo,分别获得第2/3级别分类信息
            $cat_ext_info = array();
            $cat_goods_ext = array(); //收集商品选中的扩展分类信息
            foreach($catextinfo as $kkk => $vvv){
                $pid = D('Category')
                    ->where(array('cat_id'=>$vvv['cat_id']))
                    ->getField('cat_pid');
                $cat_goods_ext[] = $vvv['cat_id']; 

                //根据$pid获得平级的分类信息
                $cat_ext_info[] = D('Category')
                    ->where(array('cat_pid'=>$pid))
                    ->select();
            }
            //dump($cat_ext_info);//array([0]=>第2级别分类信息,[1]=>第3级别分类信息)
            $this -> assign('cat_goods_ext',$cat_goods_ext);
            $this -> assign('cat_ext_info',$cat_ext_info);
            /****获得商品已经设置的扩展分类(2级别)信息****/


            /****获得相册图片****/
            $picsinfo = D('GoodsPic')->
                where(array('goods_id'=>$goods_id))->
                select();
            $this -> assign('picsinfo',$picsinfo);
            /****获得相册图片****/

            $this -> display();
        }
    }

    //对单个相册进行删除
    function delPics(){
        $pics_id = I('get.pics_id');
        $picsinfo = D('GoodsPic')->find($pics_id);
        //① 删除物理图片
        unlink($picsinfo['goods_pic_big']);
        unlink($picsinfo['goods_pic_mid']);
        unlink($picsinfo['goods_pic_sma']);
        //② 删除记录信息
        D('GoodsPic')->delete($pics_id);
    }

    //对商品的抢购指标进行状态值切换
    function changeQiang(){
        $goods_id = I('get.goods_id');
        $flag = I('get.flag');//qiang/new/rec/hot
        //获得当前被修改的商品，并判断抢购状态信息值
        $goodsinfo = D('Goods')->find($goods_id);
        $status = $goodsinfo["goods_is_".$flag];

        //$status = "热";
        if(strpos($status,'不')===false){
            $val = '不'.$status;
            $f = 0;
        }else{
            $val = str_replace('不','',$status);
            $f = 1;
        }
        $flag = "goods_is_".$flag;
        $arr = array(
            'goods_id' => $goods_id,
            "$flag"=>$val,
        );
        D('Goods')->save($arr);
        echo json_encode(array('f'=>$f));
    }

//上传Excel文件
function updExcel(){
if(IS_POST){
    if($_POST['act']=='upd'){
        //① 上传商品信息的excel文件
        $cfg = array(
            'rootPath'      =>  './Public/updexcel/', //保存根路径
        );
        $up = new \Think\Upload($cfg);
        $z = $up -> uploadOne($_FILES['goods_excel']);
        //$z会返回附件在服务器上存储的目录和名字信息
        //dump($z);
        //把excel的名字信息给拼出来
        //./Public/updexcel/2016-04-25/571dc5b19382d.xls
        $excel_name = $up->rootPath.$z['savepath'].$z['savename'];

        $this -> assign('excel_name',$excel_name);
    }else if($_POST['act']=='in'){
//② 对上传的excel文件进行数据导入
require_once './Common/Plugin/Excel/PHPExcel/IOFactory.php';
$reader = \PHPExcel_IOFactory::createReader('Excel5'); //Excel5 或 Excel2007
$PHPExcel = $reader->load($_POST['excel_name']); // 载入excel文件
$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
$highestRow = $sheet->getHighestRow(); // 取得总行数
$highestColumm = $sheet->getHighestColumn(); // 取得总列数

//dump($highestRow); //6
//dump($highestColumm);//D
/** 循环读取每个单元格的数据 */
for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始
    ////列数是以A列开始
    for ($column = 'A'; $column <= $highestColumm; $column++) {
        $dataset[$row][] = $sheet->getCell($column.$row)->getValue();
    }
}
//$dataset收集的excel数据是一个二维数组，内部每个元素都代表一条商品信息
$goods = D('Goods');
//把excel的数据处理入库
/*array(5) {
  [2] => array(4) {
    [0] => string(7) "小米1"
    [1] => float(11)
    [2] => float(110)
    [3] => float(2200)
  }
  */
foreach($dataset as $k => $v){
    $arr = array(
        'goods_name'=>$v[0],
        'goods_number'=>$v[1],
        'goods_weight'=>$v[2],
        'goods_shop_price'=>$v[3],
        'add_time'=>time(),
        'upd_time'=>time(),
    );
    $goods -> add($arr);
}
$this -> success('数据导入完毕!',U('showlist'),4);
exit;

        }
    }

    //传递差异导航内容
    $daohang = array(
        'first' => '商品管理',
        'second' => '批量上传商品信息',
        'btn' => '返回',
        'btn_link' => U('showlist'),
    );
    $this -> assign('daohang',$daohang);
    $this -> display();
}
}
