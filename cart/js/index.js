
/**
 * 为商品添加评论
 */
function product_comment(){
    var cmt_product_id = $('[name=cmt_product_id]').val();

    //获得评论等级
    var cmt_rank       = "";
    $('[name=cmt_rank]').each(function(){
        if($(this).attr('checked') == true){
            cmt_rank = $(this).val();
        }
    });

    var cmt_content    = $('[name=cmt_content]').val();
    var params = "cmt_product_id="+cmt_product_id+"&cmt_rank="+cmt_rank+"&cmt_content="+cmt_content;
    
    $.ajax({
        url:'./index.php?c=product&a=comment',
        type:'post',
        dataType:'json',
        data:params,
        success:function(json_info){
            //如果用户没有登录系统则跳转到登录页面
            if(json_info.mark == 1){
                alert(json_info.msg);
                window.location.href='./index.php?c=user&a=login';
            } else {
                alert(json_info.msg);
            }
        }
    });
}

function cart_add(product_id,product_name,product_price){
    //寻找弹出框的相对坐标
    //$('#img'+product_id)
    var pos = getElementPos('markimg'+product_id);//获得指定元素的坐标

    $.ajax({
        url:'./goods.php?a=add',
        data:'product_id='+product_id+"&product_name="+product_name+"&product_price="+product_price,
        type:'post',
        dataType:'json',
        success:function(json_info){
            $('#goods_number').html(json_info.number);
            $('#goods_totalprice').html(json_info.price);
            //指定弹出框的位置
            $('#cartBox').css('top',pos.y+50);
            $('#cartBox').css('left',pos.x-100);
            $('#cartBox').show();
            
        }
    });
}

/*
 * 根据元素的id获得其坐标(x轴和y轴)
 */
function getElementPos(elementId) {
    var ua = navigator.userAgent.toLowerCase();
    var isOpera = (ua.indexOf('opera') != -1);
    var isIE = (ua.indexOf('msie') != -1 && !isOpera); // not opera spoof
    var el = document.getElementById(elementId);
    if(el.parentNode === null || el.style.display == 'none') {
        return false;
    }
    var parent = null;
    var pos = [];
    var box;
    if(el.getBoundingClientRect) {   //IE
        box = el.getBoundingClientRect();
        var scrollTop = Math.max(document.documentElement.scrollTop, document.body.scrollTop);
        var scrollLeft = Math.max(document.documentElement.scrollLeft, document.body.scrollLeft);
        return {
            x:box.left + scrollLeft, 
            y:box.top + scrollTop
        };
    }else if(document.getBoxObjectFor) {   // gecko
        box = document.getBoxObjectFor(el);
        var borderLeft = (el.style.borderLeftWidth)?parseInt(el.style.borderLeftWidth):0;
        var borderTop = (el.style.borderTopWidth)?parseInt(el.style.borderTopWidth):0;
        pos = [box.x - borderLeft, box.y - borderTop];
    }else {   // safari & opera
        pos = [el.offsetLeft, el.offsetTop];
        parent = el.offsetParent;
        if (parent != el) {
            while (parent) {
                pos[0] += parent.offsetLeft;
                pos[1] += parent.offsetTop;
                parent = parent.offsetParent;
            }
        }
        if (ua.indexOf('opera') != -1 || ( ua.indexOf('safari') != -1 && el.style.position == 'absolute' )) {
            pos[0] -= document.body.offsetLeft;
            pos[1] -= document.body.offsetTop;
        }
    }
    if (el.parentNode) {
        parent = el.parentNode;
    } else {
        parent = null;
    }
    while (parent && parent.tagName != 'BODY' && parent.tagName != 'HTML') { // account for any scrolled ancestors
        pos[0] -= parent.scrollLeft;
        pos[1] -= parent.scrollTop;
        if (parent.parentNode) {
            parent = parent.parentNode;
        } else {
            parent = null;
        }
    }
    return {
        x:pos[0], 
        y:pos[1]
    };
}

/**
 * 关闭页面的元素标签
 * @param elm 被关闭标签的id
 */
function hideElement(elm){
    $('#'+elm).hide();
}

/**
 * 删除购物车得相应商品
 * @param product_id 被删除商品的id
 */
function cart_del(product_id){
    $.ajax({
        url:'./goods.php?a=del',
        data:'product_id='+product_id,
        type:'post',
        dataType:'html',
        success:function(html_info){
            $('#total_price').html(html_info);
            $('#product'+product_id).remove();
        }
    });
}

/**
 * 清空购物车
 */
function cart_delall(){
    if(confirm('确定要清空购物车么？')){
        //做ajax请求
        $.ajax({
            url:'./goods.php?a=delall',
            dataType:'html',
            type:'post',
            success:function(html_info){

                //将商品的tr共同创建到一个对象里边
                $('[id^=product]').remove();

                //将购物车总价格改为0
                $('#total_price').html('0');
            }
        });
    }
}
/**
 * 更改购物车数量
 * @param product_id 被修改商品的id
 */
function change_number(product_id){
    //当前被修改商品的id利用该函数的参数传递
    //获得修改后的数量
    var product_buy_number = $('#product_buy_number'+product_id).val();

	//校验数量的正确性
	if(!number_check(product_buy_number,product_id)){
		return false;
	}

    //被修改的商品id 和 修改后的数量

    change_number_product(product_buy_number,product_id);//商品数量更改公用函数
}

/**
 * 商品数量递加
 */
function change_add(product_id){
    //获得被修改商品的原数量
    var product_buy_number = $('#product_buy_number'+product_id).val();
    product_buy_number++;//商品修改后的数量

	//校验数量的正确性
	if(!number_check(product_buy_number,product_id)){
		return false;
	}

    //刷新input框数量的值
    $('#product_buy_number'+product_id).val(product_buy_number);

    //之后的操作可以参考change_number
    change_number_product(product_buy_number,product_id);//商品数量更改公用函数
}

/**
 * 商品数量递加
 */
function change_reduce(product_id){
    //获得被修改商品的原数量
    var product_buy_number = $('#product_buy_number'+product_id).val();
    product_buy_number--;//商品修改后的数量

	//校验数量的正确性
	if(!number_check(product_buy_number,product_id)){
		return false;
	}

    //刷新input框数量的值
    $('#product_buy_number'+product_id).val(product_buy_number);

    //之后的操作可以参考change_number
    change_number_product(product_buy_number,product_id);//商品数量更改公用函数
}

function number_check(product_buy_number,product_id){
	var pattern = /^[1-9]$/;
	if(!product_buy_number.toString().match(pattern)){
		alert('商品数量必须为1-9之间的数字');
		$('#product_buy_number'+product_id).val(1);
		change_number(product_id);
		return false;
	} else {
		return true;
	}
}

/**
 * 购物车数量递加、递减、数量变化公用函数
 * @param product_buy_number 商品修改后的数量
 * @param product_id 被修改数量的商品
 */
function change_number_product(product_buy_number, product_id){
    $.ajax({
        url:'./goods.php?a=changeNumber',
        data:'product_buy_number='+product_buy_number+'&product_id='+product_id,
        type:'post',
        dataType:'json',
        success:function(json_info){
            //将小计价格刷新到页面
            $('#product_total_price'+product_id).html(json_info.product_total_price);
            
            //将总价格刷新到页面
            $('#total_price').html(json_info.total);
        }
    });
}

/**
 * 省市区三级联动---省份改变触发的事件
 */
function change_province(){
    //获得被选取的省份的id
    var province_id = $('#province').val();
    
    $.ajax({
        url:'./index.php?c=personal&a=city',
        data:'province_id='+province_id,
        dataType:'html',
        type:'post',
        success:function(html_info){
            //将请求回来的城市信息赋予城市select框
            $('#city').html(html_info);
            change_city();
        }
    });

}
/**
 * 省市区三级联动---城市改变触发的事件
 */
function change_city(){
    //获得被选取的省份的id
    var city_id = $('#city').val();
    
    $.ajax({
        url:'./index.php?c=personal&a=district',
        data:'city_id='+city_id,
        dataType:'html',
        type:'post',
        success:function(html_info){
            //将请求回来的城市信息赋予城市select框
            $('#district').html(html_info);
        }
    });
}

/**
 * 核对订单任意选取收货人信息
 */
function change_consignee(consignee_id){
    $('[id^=consignee_show]').hide();//将收货人信息隐藏
    $('#consignee_show'+consignee_id).fadeIn('slow');//显示指定的收货人信息
//    $('#consignee_check'+consignee_id).attr('checked',true);//将对应的收货人单选按钮置为选择
}
