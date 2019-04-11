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

<script type='text/javascript'>
  //更换年份，显示对应年份的销售统计情况
  function change_year(){
    var year = $('[name=year]').val();
    window.location.href="/index.php/Admin/Sale/tongji/nowyear/"+year;
  }
</script>
<tr>
  <td><table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#a8c7ce">
    <tr>
      <td height="20" bgcolor="d3eaef" class="STYLE10"><div align="center">
       <h2>2016年度各个月份销售统计示意图</h2>
       <div>
       <select name='year' onchange="change_year()">
        <option value='2016' 
        <?php if(($nowyear) == "2016"): ?>selected='selected'<?php endif; ?> 
        >2016</option>

        <option value='2015'
        <?php if(($nowyear) == "2015"): ?>selected='selected'<?php endif; ?>
        >2015</option>

        <option value='2014'
        <?php if(($nowyear) == "2014"): ?>selected='selected'<?php endif; ?>
        >2014</option>
       </select></div>
      </div>
      </td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF" height='300'><div align="center">
        <img src="/index.php/Admin/Sale/imgshow/myyear/<?php echo ($nowyear); ?>" alt='' />  
      </td>
    </tr>
  </table></td>
</tr>


</table>
</body>
</html>