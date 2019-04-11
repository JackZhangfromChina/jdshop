<?php
//使用htmlpurifier实现防止xss攻击
function fanXSS($string){
    require_once './Common/Plugin/htmlpurifier/HTMLPurifier.auto.php';
    // 生成配置对象
    $cfg = HTMLPurifier_Config::createDefault();
    // 以下就是配置：
    $cfg->set('Core.Encoding', 'UTF-8');
    // 设置允许使用的HTML标签
    $cfg->set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,br,span[style],img[width|height|alt|src]');
    // 设置允许出现的CSS样式属性
    $cfg->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
    // 设置a标签上是否允许使用target="_blank"
    $cfg->set('HTML.TargetBlank', TRUE);
    // 使用配置生成过滤用的对象
    $obj = new HTMLPurifier($cfg);
    // 过滤字符串
    return $obj->purify($string);
}

//发送邮件功能
function sendMail($to, $title, $content){
    require_once('./Common/Plugin/phpmailer/class.phpmailer.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();// 设置为要发邮件
    $mail->IsHTML(TRUE);// 是否允许发送HTML代码做为邮件的内容
    $mail->CharSet='UTF-8';
    $mail->SMTPAuth=TRUE;// 是否需要身份验证
    /*  邮件服务器上的账号是什么 -> 到163注册一个账号即可 */
    $mail->From="phpseven@163.com";
    $mail->FromName="phpseven";
    $mail->Host="smtp.163.com";  //发送邮件的服务协议地址
    $mail->Username="phpseven";
    $mail->Password="phpseven777";
    $mail->Port = 25;// 发邮件端口号默认25
    $mail->AddAddress($to);// 收件人
    $mail->Subject=$title;// 邮件标题
    $mail->Body=$content;// 邮件内容

    return($mail->Send());
}

//遍历二维数组$arr，把一个指定字段$field的值拼装为字符串信息
function arrayToString($arr,$field){
    $s = array();
    foreach($arr as $k => $v){
        $s[] = $v["$field"];
    }
    $sids = implode(',',$s);
    return $sids;
}

//把当前请求地址路径的$arg的get参数给去除
//$arg有可能是",逗号分隔"的字符串，要求去除多个get参数信息
//$arg='pai,xu'  array('pai','xu')
function unsetUrlParam($arg){
    $url = $_SERVER['REQUEST_URI'];

    $arg_arr = explode(',',$arg);
    //要把$url里边的 "/$arg/7200-8399"去除
    //  /attr_id_7/gsm/attr_id_9/折叠
    //  正则表达式： [^\d]  ^托字符表示取非/取反
    foreach($arg_arr as $k => $v){
        $reg = "#/".$v."/[^/]+#";
        $url = preg_replace($reg,'',$url);
    }
    return $url;
}


//给Admin后台系统制作一个收集操作日志功能的函数
//具体通过Mongo实现
function inputLog($msg){
    //操作者、时间、ip地址、动作内容
    $m = new \MongoClient("mongodb://linken2:1234@localhost:27017/shop43");
    $s = $m -> shop43;

    $data = array(
        'operator' => session('admin_name'),
        'time'  => time(),
        'ip'    => $_SERVER['REMOTE_ADDR'],
        'content' => $msg,
    );
    $s -> log -> insert($data);
}

