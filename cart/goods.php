<?php
    require_once('./cart.php');

    $a = $_GET['a'];

    $cart = new Cart();
    //添加商品
    if($a == 'add'){
        $product_info = array('product_id'=>$_POST['product_id'],'product_name'=>$_POST['product_name'],'product_price'=>$_POST['product_price'],'product_buy_number'=>1,'product_total_price'=>$_POST['product_price']);

        $cart -> add($product_info);
        $number_price = $cart->getNumberPrice();

        echo json_encode($number_price);
    
    //商品数量修改
    } else if ($a == 'changeNumber'){
        
        $product_id         = $_POST['product_id'];//被修改商品id
        $product_buy_number = $_POST['product_buy_number'];//修改后的数量

        $product_total_price = $cart -> changeNumber($product_buy_number,$product_id);//商品小计价格
        $cart_info = $cart -> getCartInfo();//获得购物车商品信息
        
        //计算商品总价钱
        if($cart_info) {
            foreach ($cart_info as $_k => $_v) {
                $total += $_v['product_total_price']; //购物车商品总价格
            }
        }

        $arr['product_total_price'] = $product_total_price;
        $arr['total'] = $total;
        echo json_encode($arr);
    //清空商品
    } else if ($a == "delall"){
        $cart -> delall();
    
    //删除商品
    } else if ($a == "del"){
        $cart -> del($_POST['product_id']);
        
        //重新获得购物车里边商品总价格
        $cart_info = $cart->getCartInfo();//获得购物车商品的信息
        
        //根据商品信息获得商品的总价格
        //计算商品总价钱
        foreach($cart_info as $_k => $_v){
            $total += $_v['product_total_price'];//购物车商品一共的价钱
        }
        echo $total;
    }

?>