<?php
namespace Home\Controller;
use Tools\HomeController;

class ShopController extends HomeController {
    //给购物车添加商品
    function addCart(){
        $goods_id = I('get.goods_id');
        $goods = D('Goods');
        //购物车信息是一个二维数组:
        //array(
        //    [goods_id]=>array(id,name,price,number,totle),
        //    [goods_id]=>array(id,name,price,number,totle)
        //)
        $goodsinfo = $goods 
        ->field('goods_id,goods_name,goods_shop_price goods_price,goods_shop_price goods_total_price')
        ->find($goods_id);
        $goodsinfo['goods_buy_number'] = 1;
        //实例化购物车工具类对象
        $cart = new \Tools\Cart();
        $cart -> add($goodsinfo);//给购物车添加一个商品

        //返回 购物车商品数量 和 总价格
        $number_price = $cart -> getNumberPrice();
        echo json_encode($number_price);
    }

    //查看购物车
    function flow1(){
        //去购物车(cookie)查看商品列表数据
        $cart = new \Tools\Cart();
        $cartinfo = $cart -> getCartInfo();
        $this -> assign('cartinfo',$cartinfo);

        //商品的总数量和总价格
        $number_price = $cart -> getNumberPrice();
        $this -> assign('number_price',$number_price);

        $this -> display();
    }

    //要生成订单
    function flow2(){
        $order = new \Model\OrderModel();
        if(IS_POST){
            /*dump($_POST);
            array(6) {
              ["cgn_id"] => string(3) "132"
              ["shipping"] => string(1) "3"
              ["pay_method"] => string(1) "2"
              ["fapiao_title"] => string(1) "1"
              ["fapiao_title_msg"] => string(12) "传智公司"
              ["fapiao_content"] => string(1) "3"
            }*/

            /*
            两个数据表需要维护数据：sp_order 和 sp_order_goods
            ① 上边输出的信息直接维护到sp_order表中
            ② 购物车的数据填充到sp_order_goods表中
            两个数据表维护有先后关系，先维护order表，后维护order_goods表
            order_goods表具体在OrderModel::_after_insert()里边维护
            */
            $shuju = $order->create();
            //itcast_php43_20160411144023_1436
            $shuju['order_number'] = "itcast_php43_".date('YmdHis')."_".mt_rand(1000,9999); 
            $cart = new \Tools\Cart();
            $number_price = $cart -> getNumberPrice();
            $shuju['order_price'] = $number_price['price'];

            if($order -> add($shuju)){
                //发起支付
                //通过 "post" 方式发起对alipayapi.php的请求
                $zhifu_url = SITE_URL."Common/Plugin/zhifu/alipayapi.php";
                //file_get_contents($zhifu_url);
                //js代码：form表单  submit提交
                //curl技术：可以模拟get、post方式发起其他地址请求
                $post_data = array (
                    "WIDout_trade_no" => $shuju['order_number'],
                    "WIDsubject" => "京西网站卖东东",
                    "WIDtotal_fee" => $number_price['price'],
                    "WIDbody" => 'sdsdsd',
                    "WIDshow_url" => 'slkdlksd',
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $zhifu_url); //设置请求地址
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回请求的信息
                // 我们在POST数据哦！
                curl_setopt($ch, CURLOPT_POST, 1);
                // 把post的变量加上
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                $output = curl_exec($ch);
                curl_close($ch);
                echo $output;
            }
        }


        //判断用户是否登录系统
        //$user_name = session('user_name');
        if(session('?user_name')===false){

            //跳转到登录页面
            //登录完毕再跳转回当前页面
            session('back_url','Shop/flow2');
            $this -> redirect('User/login');
        }

        //获得购物车商品信息
        $cart = new \Tools\Cart();
        $cartinfo = $cart -> getCartInfo();
        $this -> assign('cartinfo',$cartinfo);
        $number_price = $cart -> getNumberPrice();
        $this -> assign('number_price',$number_price);

        $this -> display();
    }

    //给购物车商品修改数量(增加、减少、输入)
    function changeNumber(){
        $goods_id = I('get.goods_id');
        $number = I('get.number');

        $cart = new \Tools\Cart();
        $xiaojiprice = $cart -> changeNumber($number,$goods_id);

        $arr['xiaoji'] = $xiaojiprice;
        $number_price = $cart -> getNumberPrice();//总数量、总价格
        echo json_encode(array_merge($arr,$number_price));
    }

    //给购物车删除指定的商品
    //通过参数接收get参数信息
    //http://网址/Home/Shop/delGoods/goods_id/120/goods_name/htc
    //function delGoods($goods_id,$goods_name='nokia'){
    //通过形参接收get参数，(形参没有默认值情况)每次请求"必须"传递
    function delGoods($goods_id){
        //去购物车删除$goods_id的商品
        $cart = new \Tools\Cart();
        $cart -> del($goods_id);

        //返回购物车商品总数量、总价格
        $number_price = $cart -> getNumberPrice();
        echo json_encode($number_price);
    }
}

        
