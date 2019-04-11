<?php

//mail(接收者，邮件标题，邮件主体内容)函数发送邮件

//中转邮件发送
ini_set('SMTP','localhost');
ini_set('smtp_port',25);
ini_set('sendmail_from','wolf@192.168.18.33');

//现在可以随意地给许多邮箱发送邮件了
var_dump(mail('phpseven@163.com','how about weather','what the it is fine'));
var_dump(mail('2226230644@qq.com','how about the','what weather,it is fine'));