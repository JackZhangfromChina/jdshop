<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>登录商城</title>
    <link rel="stylesheet" href="<?php echo (CSS_URL); ?>base.css" type="text/css">
    <link rel="stylesheet" href="<?php echo (CSS_URL); ?>global.css" type="text/css">
    <link rel="stylesheet" href="<?php echo (CSS_URL); ?>header.css" type="text/css">
    <link rel="stylesheet" href="<?php echo (CSS_URL); ?>login.css" type="text/css">
    <link rel="stylesheet" href="<?php echo (CSS_URL); ?>footer.css" type="text/css">
    <script type="text/javascript" src="<?php echo (JS_URL); ?>jquery-1.8.3.min.js"></script>
</head>
<body>
    <!-- 顶部导航 start -->
    <div class="topnav">
        <div class="topnav_bd w990 bc">
            <div class="topnav_left">
                
            </div>
            <div class="topnav_right fr">
                <ul>
                    <?php if(empty($_SESSION['user_name'])): ?><li>您好，欢迎来到京西！[<a href="<?php echo U('User/login');?>">登录</a>] [<a href="<?php echo U('User/register');?>">免费注册</a>] </li>
                    <?php else: ?>
                    <li>您好，<span style='color:blue;font-size:25px'>【<?php echo (session('user_name')); ?>】</span>欢迎来到京西网站！[<a  href="<?php echo U('User/logout');?>">退出</a>]系统 </li><?php endif; ?>

                    
                    <li class="line">|</li>
                    <li>我的订单</li>
                    <li class="line">|</li>
                    <li>商家服务</li>

                </ul>
            </div>
        </div>
    </div>
    <!-- 顶部导航 end -->
    
    <div style="clear:both;"></div>

    <!-- 页面头部 start -->
    <div class="header w990 bc mt15">
        <div class="logo w990">
            <h2 class="fl"><a href="index.html"><img src="<?php echo (IMG_URL); ?>logo.png" alt="京西商城"></a></h2>

            <?php if((CONTROLLER_NAME) == "Shop"): ?><div class="flow fr <?php echo (ACTION_NAME); ?>">
<ul>
    <li <?php if((ACTION_NAME) == "flow1"): ?>class="cur"<?php endif; ?>>1.我的购物车</li>
    <li <?php if((ACTION_NAME) == "flow2"): ?>class="cur"<?php endif; ?> >2.填写核对订单信息</li>
    <li <?php if((ACTION_NAME) == "flow3"): ?>class="cur"<?php endif; ?>>3.成功提交订单</li>
</ul>
            </div><?php endif; ?>

        </div>
    </div>
    <!-- 页面头部 end -->



<link rel="stylesheet" href="<?php echo (CSS_URL); ?>cart.css" type="text/css">

<div style="clear:both;"></div>
<!-- 主体部分 start -->
<div class="mycart w990 mt10 bc">
	<h2><span>我的购物车</span></h2>
	<table>
		<thead>
			<tr>
				<th class="col1">商品名称</th>
				<th class="col3">单价</th>
				<th class="col4">数量</th>	
				<th class="col5">小计</th>
				<th class="col6">操作</th>
			</tr>
		</thead>
		<tbody>
<script type="text/javascript">
//参数：
//flag 动作标志：add/red/mod
function change_number(flag,goods_id){
	if(flag === 'add'){
		//获得商品原先数量，并累加1操作
		var goods_number = $('#goods_'+goods_id).val();
		goods_number = parseInt(goods_number)+1;
	}else if(flag === 'red'){
		//商品数量减少
		var goods_number = $('#goods_'+goods_id).val();
		goods_number = parseInt(goods_number)-1;
		if(goods_number <1){
			alert('数量不能小于1');
			return false;
		}
	}else if(flag === 'mod'){
		//商品数量手工输入
		var goods_number = $('#goods_'+goods_id).val();
		goods_number = goods_number;
		//goods_number要求是一个"有意义"的数字信息
		var reg = /^[1-9]\d*$/;
		if(goods_number.match(reg) == null){
			alert('输入的数量不合法');
			return false;
		}
	}else{
		alert('参数有问题');
		return false;
	}
	//调用ajax，请求服务器端，使得购物车cookie对应商品数量发生变化
	$.ajax({
		url:"/index.php/Home/Shop/changeNumber",
		data:{'goods_id':goods_id,'number':goods_number},
		dataType:'json',
		type:'get',
		success:function(msg){
			//console.log(msg);{"xiaoji":36000,"number":430,"price":1272000}
			$('#goods_'+goods_id).val(goods_number);//数量变化
			$('#xiaoji_'+goods_id).html(msg.xiaoji);//单个商品小计价格变化
			$('#total').html(msg.price)//更新总价格
		}
	});
}

//删除购物车指定的商品
function del_goods(goods_id,obj){
	//ajax请求服务器端，删除购物车商品
	$.ajax({
		url:"/index.php/Home/Shop/delGoods",
		data:{'goods_id':goods_id},
		dataType:'json',
		type:'get',
		success:function(msg){
			//console.log(msg);//Object { number=3, price=5340}
			//dom删除商品
			$(obj).parent().parent().remove();
			//把删除商品后的购物车总价格刷新到页面上
			$('#total').html(msg.price);
		}
	});
}
</script>
<?php if(is_array($cartinfo)): foreach($cartinfo as $key=>$v): ?><tr>
	<td class="col1"><a href=""><img src="images/cart_goods1.jpg" alt="" /></a>  <strong><a href=""><?php echo ($v["goods_name"]); ?></a></strong></td>
	<td class="col3">￥<span><?php echo ($v["goods_price"]); ?></span></td>
	<td class="col4"> 
		<a href="javascript:;" onclick="change_number('red',<?php echo ($v["goods_id"]); ?>)" class="reduce_num"></a>
		<input type="text" name="amount" value="<?php echo ($v["goods_buy_number"]); ?>" class="amount" id="goods_<?php echo ($v["goods_id"]); ?>"
		onchange="change_number('mod',<?php echo ($v["goods_id"]); ?>)"
		/>
		<a href="javascript:change_number('add',<?php echo ($v["goods_id"]); ?>)" class="add_num"></a>
	</td>
	<td class="col5">￥<span id="xiaoji_<?php echo ($v["goods_id"]); ?>"><?php echo ($v["goods_total_price"]); ?></span></td>
	<td class="col6"><a href="javascript:;" onclick="if(confirm('确认要删除该商品么？')){del_goods(<?php echo ($v["goods_id"]); ?>,this)}">删除</a></td>
</tr><?php endforeach; endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">购物金额总计： <strong>￥ <span id="total"><?php echo ($number_price["price"]); ?></span></strong></td>
			</tr>
		</tfoot>
	</table>
	<div class="cart_btn w990 bc mt10">
		<a href="" class="continue">继续购物</a>
		<a href="<?php echo U('flow2');?>" class="checkout" target="_blank">结 算</a>
	</div>
</div>
<!-- 主体部分 end -->

<div style="clear:both;"></div>
	


    <!-- 底部版权 start -->
    <div class="footer w1210 bc mt15">
        <p class="links">
            <a href="">关于我们</a> |
            <a href="">联系我们</a> |
            <a href="">人才招聘</a> |
            <a href="">商家入驻</a> |
            <a href="">千寻网</a> |
            <a href="">奢侈品网</a> |
            <a href="">广告服务</a> |
            <a href="">移动终端</a> |
            <a href="">友情链接</a> |
            <a href="">销售联盟</a> |
            <a href="">京西论坛</a>
        </p>
        <p class="copyright">
             © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号 
        </p>
        <p class="auth">
            <a href=""><img src="<?php echo (IMG_URL); ?>xin.png" alt="" /></a>
            <a href=""><img src="<?php echo (IMG_URL); ?>kexin.jpg" alt="" /></a>
            <a href=""><img src="<?php echo (IMG_URL); ?>police.jpg" alt="" /></a>
            <a href=""><img src="<?php echo (IMG_URL); ?>beian.gif" alt="" /></a>
        </p>
    </div>
    <!-- 底部版权 end -->

</body>
</html>