<?php

namespace Tools;

class MyRedis{
    //集群标志
    private $_isJiqun = false; //默认不是集群

    //主、从redis对象
    private $_objHandler = array(
        'master' => null,
        'slave' => null
    );

    //构造方法
    //@params $flag   false:非集群设计   true:集群设计
    function __construct($flag=false){
        $this -> _isJiqun = $flag;
    }

    //连接redis
    //@param $isMaster:   false->从服务器  true->主服务器
    function connect($arr = array('host'=>'127.0.0.1','port'=>'6379','auth'=>''),$isMaster=false){
        if($isMaster===true){
            $this->_objHandler['master'] = new \Redis();
            $ret = $this->_objHandler['master']->connect($arr['host'],$arr['port']);
            if(!empty($arr['auth'])){
                $this->_objHandler['master']->auth($arr['auth']);
            }
            return $ret;
        }
        $this->_objHandler['slave'] = new \Redis();
        $ret = $this->_objHandler['slave']->connect($arr['host'],$arr['port']);
        if(!empty($arr['auth'])){
            $this->_objHandler['slave']->auth($arr['auth']);
        }
        return $ret;
    }

    //具体redis操作
    //获得主服务器对象
    private function _getMredis(){
        return $this->_objHandler['master'];
    }
    //获得从服务器对象
    private function _getSredis(){
        return $this->_objHandler['slave'];
    }

    function set($key,$val){
        //直接主服务器“写入(添加、修改、删除)”数据
        $this -> _getMredis()->set($key,$val);
    }
    function get($key){
        //如果非集群，就去主服务器获得数据
        if($this -> _isJiqun === false){
            return $this -> _getMredis()->get($key);
        }
        //否则去 从服务器获得数据
        return $this -> _getSredis()->get($key);
    } 

    function lrem($key,$val,$count){
        $this -> _getMredis()->lrem($key,$val,$count);
    }
    function lpush($key,$val){
        $this -> _getMredis()->lpush($key,$val);
    }    
    function ltrim($key,$start,$end){
        $this -> _getMredis()->ltrim($key,$start,$end);
    }

    function lrange($key,$start,$end){
        //如果非集群，就去主服务器获得数据
        if($this -> _isJiqun === false){
            return $this -> _getMredis()->lrange($key,$start,$end);
        }
        //否则去 从服务器获得数据
        return $this -> _getSredis()->lrange($key,$start,$end);
    }
}
