<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<script type="text/javascript" src="<?php echo (JS_URL); ?>jquery-1.8.3.min.js"></script>
<style type="text/css">
<!--
body { 
    margin-left: 3px;
    margin-top: 0px;
    margin-right: 3px;
    margin-bottom: 0px;
}
.STYLE1 {
    color: #e1e2e3;
    font-size: 12px;
}
.STYLE6 {color: #000000; font-size: 12; }
.STYLE10 {color: #000000; font-size: 12px; }
.STYLE19 {
    color: #344b50;
    font-size: 12px;
}
.STYLE21 {
    font-size: 12px;
    color: #3b6375;
}
a{
  font-size: 12px;
  color: #3b6375;
   text-decoration:none;
}
.STYLE22 {
    font-size: 12px;
    color: #295568;
}
/*
a:link{
    color:#e1e2e3; text-decoration:none;
}
a:visited{
    color:#e1e2e3; text-decoration:none;
}*/
-->
</style>
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="30"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="24" bgcolor="#353c44"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="6%" height="19" valign="bottom"><div align="center"><img src="<?php echo (AD_IMG_URL); ?>tb.gif" width="14" height="14" /></div></td>
                <td width="94%" valign="bottom"><span class="STYLE1"> <?php echo ($daohang["first"]); ?> -> <?php echo ($daohang["second"]); ?></span></td>
              </tr>
            </table></td>
            <td><div align="right"><span class="STYLE1">
              <a href="<?php echo ($daohang["btn_link"]); ?>"><img src="<?php echo (AD_IMG_URL); ?>add.gif" width="10" height="10" /> <?php echo ($daohang["btn"]); ?></a>   &nbsp; 
              </span>
              <span class="STYLE1"> &nbsp;</span></div></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>

  <script type="text/javascript">
  function make_excel(){
    //获得选取的商品id信息
    var goods_ids = '';
    $('input[name^=goods_box]:checked').each(function(){
      goods_ids += parseInt($(this).attr('id'))+',';
    });
    alert(goods_ids);//31,29,27,

    //通过ajax触发服务器端获得商品信息，生成excel文件
    $.ajax({
      url:"/index.php/Admin/Goods/makeExcel",
      data:{'goods_ids':goods_ids},
      dataType:'html',
      type:'get',
      success:function(msg){
        alert(msg);
      }
    });
  }
  
  function make_excel2(){
    //获得选取的商品id信息
    var goods_ids = '';
    $('input[name^=goods_box]:checked').each(function(){
      goods_ids += parseInt($(this).attr('id'))+',';
    });
    //使得整个页面提交给"Goods/makeExcel2",同时把
    //goods_ids的get参数 传递过去
    var sonfm = window.open('/index.php/Admin/Goods/makeExcel2/goods_ids/'+goods_ids,'_blank');
  }
  </script>
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#a8c7ce">
      <tr>
        <td colspan='100' height='40'  bgcolor="#FFFFFF">
          <span><a href="/index.php/Admin/Goods/excel_out_page/page/<?php echo ($_GET['page']); ?>" target='_blank'>Excel导出当前页商品信息</a></span>
          
          <span><input type='button' value='生成指定信息的excel文件' onclick='make_excel2()' /></span>

          <span style='float:right'><a href="<?php echo U('updExcel');?>" target='_self'>批量导入数据</a></span>
        </td>
      </tr>
      <tr>
        <td height="20" bgcolor="d3eaef" class="STYLE10"><div align="center">
          <input type="checkbox" name="goods_box" id="checkbox" />
        </div></td>
        <td height="20" width='7%' bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">商品名称</span></div></td>
        <td height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">价格</span></div></td>
        <td height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">数量</span></div></td>
        <td height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">重量</span></div></td>

        <td height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">小图logo</span></div></td>

        <td width="4%" height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">抢购</span></div></td>
        <td width="4%" height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">热卖</span></div></td>
        <td width="4%" height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">推荐</span></div></td>
        <td width="4%" height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">新品</span></div></td>
        <td height="20" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">基本操作</span></div></td>
      </tr>
<script type='text/javascript'>
  /**
  flag:抢购指标 抢购/热卖/推荐/新品
  */
  function change_qiang(goods_id,flag,obj){
    //走ajax去服务器端做状态值修改
    $.ajax({
      url:"/index.php/Admin/Goods/changeQiang",
      data:{'goods_id':goods_id,'flag':flag},
      dataType:'json',
      type:'get',
      success:function(msg){
        if(msg.f==0){
          var src = $(obj).find('img').attr('src');
          src = src.replace('yes','no');
          $(obj).find('img').attr('src',src);
        }else{
          var src = $(obj).find('img').attr('src');
          src = src.replace('no','yes');
          $(obj).find('img').attr('src',src);
        }
      }
    });
  }
</script>
      <?php if(is_array($info)): foreach($info as $key=>$v): ?><tr>
        <td bgcolor="#FFFFFF"><div align="center">
          <input type="checkbox" id="<?php echo ($v["goods_id"]); ?>_goods_box" name='goods_box[]'/>
          <?php echo ($v["goods_id"]); ?>
        </div></td>
        <td bgcolor="#FFFFFF" class="STYLE6"><div align="center"><span class="STYLE19"><?php echo ($v["goods_name"]); ?></span></div></td>
        <td bgcolor="#FFFFFF" class="STYLE19"><div align="center"><?php echo ($v["goods_shop_price"]); ?></div></td>
        <td bgcolor="#FFFFFF" class="STYLE19"><div align="center"><?php echo ($v["goods_number"]); ?></div></td>
        <td bgcolor="#FFFFFF" class="STYLE19"><div align="center"><?php echo ($v["goods_weight"]); ?></div></td>

        <td bgcolor="#FFFFFF" class="STYLE19"><div align="center"><img src='<?php echo (SITE_URL); echo (substr($v["goods_small_logo"],2)); ?>' alt='no_logo' width='50'/></div></td>

        <td bgcolor="#FFFFFF" class="STYLE19"><div align="center" onclick="change_qiang(<?php echo ($v["goods_id"]); ?>,'qiang',this)"><img src="<?php echo (AD_IMG_URL); echo ($v['goods_is_qiang']=='抢'?'yes':'no'); ?>.gif" alt='' /></div></td>

        <td bgcolor="#FFFFFF" class="STYLE19"><div align="center" onclick="change_qiang(<?php echo ($v["goods_id"]); ?>,'hot',this)"><img src="<?php echo (AD_IMG_URL); echo ($v['goods_is_hot']=='热'?'yes':'no'); ?>.gif" alt='' /></div></td>

        <td bgcolor="#FFFFFF" class="STYLE19"><div align="center" onclick="change_qiang(<?php echo ($v["goods_id"]); ?>,'rec',this)"><img src="<?php echo (AD_IMG_URL); echo ($v['goods_is_rec']=='推荐'?'yes':'no'); ?>.gif" alt='' /></div></td>

        <td bgcolor="#FFFFFF" class="STYLE19"><div align="center" onclick="change_qiang(<?php echo ($v["goods_id"]); ?>,'new',this)"><img src="<?php echo (AD_IMG_URL); echo ($v['goods_is_new']=='新品'?'yes':'no'); ?>.gif" alt='' /></div></td>

        <td bgcolor="#FFFFFF"><div align="center" class="STYLE21">
        <img src="<?php echo (AD_IMG_URL); ?>del.gif" width="10" height="10" /> 删除 | 查看 | 
        <a href="<?php echo U('upd',array('goods_id'=>$v['goods_id']));?>"><img src="<?php echo (AD_IMG_URL); ?>edit.gif" width="10" /> 编辑</a></div></td>
      </tr><?php endforeach; endif; ?>
    </table></td>
  </tr>
  <tr>
    <td height="30"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="33%">
        <div align="left"><span class="STYLE22">
        <?php echo ($pagelist); ?>
        </span></div>
        </td>
      </tr>
    </table></td>
  </tr>



</table>
</body>
</html>