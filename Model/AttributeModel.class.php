<?php
namespace Model;
use Think\Model;

class AttributeModel extends Model {
    // 是否批处理验证
    protected $patchValidate    =   true;
    //表单验证
    // 自动验证定义
    protected $_validate        =   array(
        //array(字段，规则，提示信息[，条件，附加规则，时间]),
        array('attr_name','require','属性名称必填'),
        array('type_id','0','类型必须选择一个',0,'notequal'),
    );  
}
