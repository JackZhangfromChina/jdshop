<?php

//mail(接收者，邮件标题，邮件主体内容)函数发送邮件

//直投邮件
ini_set('SMTP','163mx01.mxmail.netease.com');
ini_set('smtp_port',25);
ini_set('sendmail_from','wolf@192.168.18.33');

var_dump(mail('phpseven@163.com','how about the weather','what the weather,it is fine'));