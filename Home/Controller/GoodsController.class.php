<?php
namespace Home\Controller;
use Tools\HomeController;

class GoodsController extends HomeController {
    public function showlist(){
        //判断是否有模糊查询
        $goods_name_s = I('get.goods_name_s');
        $index_name = "goods";
        if(!empty($goods_name_s)){
            //使用sphinx,进行内容模糊查询
            $cl = new \Tools\SphinxClient();
            $cl->SetServer ( '127.0.0.1', 9312);
            $cl->SetArrayResult ( true );
            $cl->SetMatchMode ( SPH_MATCH_ANY);//给匹配"任何分词"对应内容

            $res = $cl->Query ( $goods_name_s, $index_name );
            //dump($res['matches']);
            if(!empty($res['matches'])){
                $ids = arraytoString($res['matches'],'id');
                //dump($ids);//string(8) "17,26,28"
                $goods_cdt['goods_id'] = array('in',"$ids");
            }else{
                $ids = "";
                $goods_cdt['goods_id'] = 0;
            }
        }

        $cat_id = I('get.cat_id');
        /****获取显示对应的分类信息****/
        $category = D('Category');
        $now_catinfo = $category->find($cat_id);

        //点击第3个级别分类，做具体分类展开操作
        if($now_catinfo['cat_level']=='2'){
            $this -> assign('open_cat',$now_catinfo['cat_pid']);
        }else{
            $this -> assign('open_cat',0);
        }

        $now_catpath = $now_catinfo['cat_path'];
        $path_part = explode('-',$now_catpath);
        $one_path = $path_part[0];//获得全路径的第一个分解内容

        //给全路径第一个分解内容做模糊查询
        $second_catinfo = $category->where(array('cat_path'=>array('like',$one_path.'-%')))->select();
        $first_catinfo = $category->find($one_path);
        //$ji_catinfo = array_merge($first_catinfo,$second_catinfo);
        $catinfo_A = $first_catinfo;//第1级别分类
        $catinfo_B = array();//第2级别分类
        $catinfo_C = array();//第3级别分类
        foreach($second_catinfo as $k => $v){
            if($v['cat_level']=='1'){
                $catinfo_B[] = $v;
            }else if($v['cat_level']=='2'){
                $catinfo_C[] = $v;
            }
        }
        $this -> assign('catinfo_A',$catinfo_A);
        $this -> assign('catinfo_B',$catinfo_B);
        $this -> assign('catinfo_C',$catinfo_C);
        /****获取显示对应的分类信息****/


        /****根据价格区间筛选商品****/
        $mark_condition = array();  //各种筛选条件的变量
        //读取get参数price_qujian，如果不存在就设置为''空字符串
        $price_qujian = I('get.price_qujian','');
        //3600-4799   或 8400以上
        if(!empty($price_qujian)){
            if(strpos($price_qujian,'-')!==false){//3600-4799情况
                $qujian_arr = explode('-',$price_qujian);
                $goods_cdt['goods_shop_price'] = array('between',array($qujian_arr[0],$qujian_arr[1]));
                //WHERE `goods_shop_price` BETWEEN '3600' AND '4799'
            }else{//"8400以上"情况
                $p = intval($price_qujian);
                $goods_cdt['goods_shop_price'] = array('egt',$p);
                //WHERE `goods_shop_price` >= 8400
            }
            //制作价格标签
            $mark_condition[] = "<li style='border:2px solid gray;width:110px; height:19px;padding:3px 5px;float:left;margin-right:3px;'>价格:".$price_qujian."&nbsp;&nbsp;<a style='font-size:20px;color:red;' href='".unsetUrlParam('price_qujian')."'>X</a></li>";//价格标签
                //unsetUrlParam(参数); 会返回去除了get"参数"的当前请求地址
            
//index.php/Home/Goods/showlist/cat_id/1/price_qujian/7200-8399[当前地址]
//index.php/Home/Goods/showlist/cat_id/1[unsetUrlParam函数处理后的地址]
        }
        /****根据价格区间筛选商品****/



        //ORDER BY add_time desc
        /****根据点击的分类，获得对应的商品列表信息****/
        //① 到sp_goods_cat表获得商品id
        //   判断是点击第2/3级别分类
        $goods = D('Goods');
        $goods_cdt['is_del'] = '正常';
        if($now_catinfo['cat_level']>0){
            $goods_cdt['cat_id'] = $cat_id;
            $goodsid = D('GoodsCat')
                    ->where($goods_cdt)
                    ->field('group_concat(goods_id) gids')
                    ->find();
        }else{
            //② 到sp_goods表获得商品id
            //   点击的是第1个级别分类
            $goods_cdt['cat_id'] = $cat_id;
            $goodsid = $goods 
                    ->where($goods_cdt)
                    ->field('group_concat(goods_id) gids')
                    ->find();
            //SELECT group_concat(goods_id) gids FROM `sp_goods` WHERE `cat_id` = 1 LIMIT 1
            //dump($goodsid);//array(["gids"] => string(14) "4,7,8,11,14,27")
        }
        //此时的$goods_ids是"分类"和 "价格"" 合并求得的商品id信息
        $goods_ids = $goodsid['gids'];
        //当前选取属性对应的商品id 获得出来 并与$goods_ids求交集



        /****根据商品id信息，获得对应的"多选"属性信息****/
        //数据表：sp_goods_attr   sp_attribute
        $attr_d_info = D('GoodsAttr')
                -> alias('ga')
                -> join('__ATTRIBUTE__ a on ga.attr_id=a.attr_id')
                -> where(array('ga.goods_id'=>array('in',$goods_ids),'a.attr_sel'=>'1'))
                -> field('a.attr_name,ga.attr_id,group_concat(distinct ga.attr_val) attrval')
                -> group('ga.attr_id')
                -> select();
        //dump($attr_d_info);
        //把多选属性值由String字符串 变为 Array数组
        foreach($attr_d_info as $k => $v){
            $attr_d_info[$k]['attr_val'] = explode(',',$v['attrval']);
        }
        //dump($attr_d_info);
        $this -> assign('attr_d_info',$attr_d_info);
        /****根据商品id信息，获得对应的多选属性信息****/

        //通过$attr_d_info把 attr_id和attr_name给对应联系起来
        //方便获得属性的“名称”
        $attr_id_name = array();
        foreach($attr_d_info as $k => $v){
            $attr_id_name[$v['attr_id']] = $v['attr_name'];
        }
        //dump($attr_id_name); //array(attr_id=>attr_name)

        /****给其他属性设置显示"标签"****/
        //获得请求地址的类似"attr_id_x"的参数信息
        $args = I('get.');
        $dian_attrids = array();
        $dian_attr_id_val = array();//收集已经选中的属性的"id-value"
        foreach($args as $k => $v){
            if(strpos($k,'attr_id_')!==false){
                //处理属性get参数信息
                $dian_attrids[] = $m_attr_ids = substr($k,8);//获得属性id信息
                //dump($m_attr_ids);//7 、9
                $dian_attr_id_val[$m_attr_ids] = $v;
                $mark_condition[] = "<li style='border:2px solid gray;width:110px; height:19px;padding:3px 5px;float:left;margin-right:3px;'>".$attr_id_name[$m_attr_ids].":".$v."&nbsp;&nbsp;<a style='font-size:20px;color:red;' href='".unsetUrlParam('attr_id_'.$m_attr_ids)."'>X</a></li>";//价格标签
            }
        }
        //被点击属性的attr_id组成的数组传递给模板
        $this -> assign('dian_attrids',$dian_attrids);
        /****给其他属性设置显示"标签"****/

        //根据选取属性的id 查询一共匹配的商品id信息
        //已经获得$goods_id 分别与每个属性对应的商品id【递归】求交集
        $jiaoji_goods_ids = explode(',',$goods_ids);//已经获得的商品id信息
        //dump($dian_attr_id_val);
        /*array(2) {
          [5] => string(8) "cdma2000"
          [7] => string(6) "折叠"
        }*/
        foreach($dian_attr_id_val as $k => $v){
            $dian_attr_goodsids = D('GoodsAttr')
                -> where(array('attr_id'=>$k,'attr_val'=>$v))
                -> field('group_concat(distinct goods_id) as goodsid')
                -> find();
            $attr_goods_arr = explode(',',$dian_attr_goodsids['goodsid']);
            //属性id、属性值 匹配到的goods_id和已经查询出来的goods_id做交集
            $jiaoji_goods_ids = array_intersect($jiaoji_goods_ids, $attr_goods_arr);
        }
        $jiaoji_goods_ids = implode(',',$jiaoji_goods_ids);//string处理

        /****设置排序条件****/
        $pai = I('get.pai','xl');
        $xu = I('get.xu','desc');

        $pai = $pai=='sj'?'add_time':$pai;
        $pai = $pai=='jg'?'goods_shop_price':$pai;
        /****设置排序条件****/

        if(!empty($jiaoji_goods_ids)){
            $tmp = array();
            $goodsinfo = $goods 
                    -> alias('g')
                    -> order("$pai $xu")
                    -> field('g.goods_id,g.goods_name,g.goods_shop_price,g.goods_small_logo,(select sum(goods_number) from sp_order_goods og where g.goods_id=og.goods_id) xl,(select count(*) from sp_comment c where c.goods_id=g.goods_id) pl ')
                    -> select($jiaoji_goods_ids);

            //给$goodsinfo的关键字内容设置语法"高亮""
            //同时判断是模糊查询
            if(!empty($goods_name_s)){
                foreach($goodsinfo as $k => $v){
                    $row = $cl->buildExcerpts($v,$index_name,$goods_name_s,
                        array('before_match'=>"<span style='color:red;'>",'after_match'=>"</span>"));
                    $tmp[$k]['goods_id'] = $row[0];
                    $tmp[$k]['goods_name'] = $row[1];
                    $tmp[$k]['goods_shop_price'] = $row[2];
                    $tmp[$k]['goods_small_logo'] = $row[3];
                }
            }else{
                $tmp = $goodsinfo;
            }
            /****获得价格相关信息****/
            $price_str = arraytostring($tmp,'goods_shop_price');
            $price_arr = explode(',',$price_str);
            $price_min = min($price_arr);
            $price_max = max($price_arr);

            $per = 7; //要显示7个价格区间
            //计算区间间隔“段”的价格
            $price_duan = floor(($price_max-$price_min)/$per);

            $t0 = 0; //声明临时变量
            $start =0; //临时变量
            //计算区间价格
            for($i=0; $i<$per; $i++){
                //把价格段设计为"99"结尾
                $t0 = $start + floor($price_duan/100)*100+99;  
                $qujian[] = $start."-".$t0;  //0-1199  1200-2399
                $start = $t0+1;
            }
            $qujian[] = $start."以上";
            $this -> assign('qujian',$qujian);
            /*array(8) {
              [0] => string(6) "0-1199"
              [1] => string(9) "1200-2399"
              [2] => string(9) "2400-3599"
              [3] => string(9) "3600-4799"
              [4] => string(9) "4800-5999"
              [5] => string(9) "6000-7199"
              [6] => string(9) "7200-8399"
              [7] => string(10) "8400以上"
            }*/

            /****获得价格相关信息****/
        }else{
            $tmp = array();
        }
        $this -> assign('mark_condition',$mark_condition);
        $this -> assign('goodsinfo',$tmp);
        /****根据点击的分类，获得对应的商品列表信息****/

        $this -> display();
    }

    //商品详情
    public function detail(){
        $goods_id = I('get.goods_id');

        /****获得商品评论信息****/
        $comment = new CommentController();
        $commentinfo = $comment -> showlist($goods_id);
        //获得评论的总条数
        $comment_total = count($commentinfo);
        $this -> assign('comment_total',$comment_total);

        $this -> assign('commentinfo',$commentinfo);
        /****获得商品评论信息****/


        //定义回跳地址
        //用户登录系统后还可以跳转回来
        $user_name = session('user_name');
        if(!session('?user_name')){
            session(C('BACK_URL'),'Home/Goods/detail/goods_id/'.$goods_id);
        }

        /****商品基本信息****/
        $goods = D('Goods');
        $info = $goods -> find($goods_id);
        $this -> assign('info',$info);
        /****商品基本信息****/

        /****商品多选属性信息****/
        //sp_goods_attr  和  sp_attribute两个表做查询
        $attrInfoA = D('GoodsAttr')
                ->alias('ga')
                ->join('__ATTRIBUTE__ a on ga.attr_id=a.attr_id')
                ->where(array('ga.goods_id'=>$goods_id,'a.attr_sel'=>'1'))
                ->field('ga.attr_id,group_concat(ga.attr_val) attrval,a.attr_name')
                ->group('a.attr_id')
                ->select();
        //把数据库获得的‘连接’状态的属性信息 变为 单个的数组元素信息
        foreach($attrInfoA as $k => $v){
            $attrInfoA[$k]['attr_val'] = explode(',',$v['attrval']);
        }
        $this -> assign('attrInfoA',$attrInfoA);
        /****商品多选属性信息****/

        /****商品单选属性信息****/
        //sp_goods_attr  和  sp_attribute两个表做查询
        $attrInfoB = D('GoodsAttr')
                ->alias('ga')
                ->join('__ATTRIBUTE__ a on ga.attr_id=a.attr_id')
                ->where(array('ga.goods_id'=>$goods_id,'a.attr_sel'=>'0'))
                ->field('ga.attr_id,ga.attr_val,a.attr_name')
                ->group('a.attr_id')
                ->select();
        $this -> assign('attrInfoB',$attrInfoB);
        /****商品单选属性信息****/

        /****商品相册图片(大、中、小)信息****/
        $goodspics = D('GoodsPic')
                ->where(array('goods_id'=>$goods_id))
                ->select();
        $this -> assign('goodspics',$goodspics);
        /****商品相册图片(大、中、小)信息****/

        /****面包屑制作****/
        //① 根据$goods_id获得第1/2/3级别分类信息
        //数据表sp_goods(1级)   sp_goods_cat(2/3级)
        $cat_id1 = $info['cat_id']; //1
        $cat_id23 = D('GoodsCat')
                ->where(array('goods_id'=>$goods_id))
                ->field('group_concat(cat_id) catid')
                ->find();
        //dump($cat_id23);//array(1) {["catid"] => string(4) "5,23"}
        //获得1/2/3级分类信息
        $cat_id123 = $cat_id1.','.$cat_id23['catid'];
        $cat_info123 = D('Category')
                ->order('cat_id')
                ->select($cat_id123);
        //dump($cat_info123);exit;
        $this -> assign('cat_info123',$cat_info123);
        /****面包屑制作****/

        /****存储当前的商品id到cookie中****/
        $nm = "recentlylook";
        //不断给cookie存储商品id信息，最多保留5个
        //先读取cookie并判断是否已经有数据
        $lookinfo = unserialize($_COOKIE[$nm]);
        if(empty($lookinfo)){
            $five[] = $goods_id;
            setcookie($nm,serialize($five),time()+3600*2,'/');
        }else{
            //给cookie头部追加goods_id
                    //如果内部已经存在
            //该goods_id,就删除，再重新添加
            if(in_array($goods_id,$lookinfo)){
                $f_lookinfo = array_flip($lookinfo);
                unset($f_lookinfo[$goods_id]);
                $lookinfo = array_flip($f_lookinfo);
            }
            //只保留5个
            array_unshift($lookinfo,intval($goods_id));
            $five = array_slice($lookinfo,0,5);
            setcookie($nm,serialize($five),time()+3600*2,'/');
        }
        // setcookie(name,value,time,/斜杠)
        // /斜杠：cookie设置好后，在当前域名主机下的任何目录都可以访问
        // 通过$five获得商品信息并展示
        $s_five = implode(',',$five);
        $recentlyinfo = D('Goods')
                ->where(array('goods_id'=>array('in',$five)))
                ->field('goods_id,goods_name,goods_small_logo')
                ->order("find_in_set(goods_id,'$s_five') ")
                ->select();
        $this -> assign('recentlyinfo',$recentlyinfo);
        /****存储当前的商品id到cookie中****/

        $this -> display();
    }

    //清空最近浏览的商品
    function cleanLook(){
        $goods_id = I('get.goods_id');
        cookie('recentlylook',null);
        $this -> redirect('detail',array('goods_id'=>$goods_id));
    }
}
