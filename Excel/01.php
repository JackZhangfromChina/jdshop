<?php

//生成一个2007版本的Excel文件
//① 实例化一个压缩文档对象
$ex = new ZipArchive();

//② 打开一个excel文件(2007版本)
$ex -> open('./01.xlsx',ZIPARCHIVE::CREATE);

//③ 创建excel文档的各个组成文件(文件夹、xml文件)
$ex -> addFromString('[Content_Types].xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('_rels/.rels',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('docProps/app.xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('docProps/core.xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('docProps/custom.xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('xl/_rels/workbook.xml.rels',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('xl/theme/theme1.xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('xl/worksheets/sheet1.xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('xl/worksheets/sheet2.xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('xl/worksheets/sheet3.xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('xl/styles.xml',"<?xml version='1.0' charset='utf-8' ?>");
$ex -> addFromString('xl/workbook.xml',"<?xml version='1.0' charset='utf-8' ?>");