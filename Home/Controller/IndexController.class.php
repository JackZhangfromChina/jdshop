<?php
namespace Home\Controller;
use Tools\HomeController;

class IndexController extends HomeController {
    public function index(){
        //通过redis获得最新登录系统的用户信息
        $logininfo = $this->redis43->lrange('loginname',0,100);
        // dump($logininfo);
        $this -> assign('logininfo',$logininfo);

        //获得抢购商品信息
        $cdt['is_del'] = '正常';

        $field = "goods_id,goods_name,goods_shop_price,goods_small_logo";
        //① 抢购商品 goods_is_qiang='抢' is_del='正常' 5个  商品id倒序排序
        $goods = D('Goods');
        $qiang_cdt = array_merge($cdt,array('goods_is_qiang'=>'抢'));
        $qiangInfo = $goods 
            ->where($qiang_cdt)
            ->order('goods_id desc')
            ->limit(5)
            ->field($field)
            ->select();

        //② 热卖商品，排除"抢购"商品，其他特性同上
        //把已经获得出来的“抢购商品”的id值拼装为字符串
        //遍历二维数组，把一个指定字段的值拼装为字符串信息
        $goodsid_exists = \arrayToString($qiangInfo,'goods_id');
        //dump($goodsid_exists);string(14) "28,27,26,25,24"
        $hot_cdt = array_merge($cdt,
            array('goods_is_hot'=>'热'),
            array('goods_id'=>array('not in',$goodsid_exists)));
        $hotInfo = $goods 
            ->where($hot_cdt)
            ->order('goods_id desc')
            ->limit(5)
            ->field($field)
            ->select();

        //③ 推荐商品，排除"抢购/热卖"商品，其他特性同上
        $goodsid_exists = $goodsid_exists.','.\arrayToString($hotInfo,'goods_id');
        $rec_cdt = array_merge($cdt,
            array('goods_is_rec'=>'推荐'),
            array('goods_id'=>array('not in',$goodsid_exists)));
        $recInfo = $goods 
            ->where($rec_cdt)
            ->order('goods_id desc')
            ->limit(5)
            ->field($field)
            ->select();

        //④ 新品商品，排除"抢购/热卖"商品，其他特性同上
        $goodsid_exists = $goodsid_exists.','.\arrayToString($recInfo,'goods_id');
        $new_cdt = array_merge($cdt,
            array('goods_is_new'=>'新品'),
            array('goods_id'=>array('not in',$goodsid_exists)));
        $newInfo = $goods 
            ->where($new_cdt)
            ->order('goods_id desc')
            ->limit(5)
            ->field($field)
            ->select();

        $this -> assign('qiangInfo',$qiangInfo);
        $this -> assign('hotInfo',$hotInfo);
        $this -> assign('recInfo',$recInfo);
        $this -> assign('newInfo',$newInfo);

        $this -> display();
    }

    function inputdata(){
        $this -> redis43->set('citycity','beijingbeijing');
        echo "okokok";
    }   
    function outputdata(){
        dump($this -> redis43->get('citycity'));
        echo "读取ok";
    }
}
