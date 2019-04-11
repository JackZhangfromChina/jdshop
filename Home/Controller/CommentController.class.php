<?php
namespace Home\Controller;
use Tools\HomeController;

class CommentController extends HomeController {
    //添加评论
    function tianjia(){
        $comment = D('Comment');
        //接收post过来的信息
        $star = I('post.star');
        $star = "$star";
        $msg = \fanXSS($_POST['msg']);
        $goods_id = I('post.goods_id');

        $arr = array(
            'cmt_star'=>$star,
            'cmt_msg'=>$msg,
            'add_time'=>time(),
            'user_id'=>session('user_id'),
            'goods_id'=>$goods_id,
        );
        if($z = $comment -> add($arr)){
            $arr['cmt_id'] = $z;
            $arr['add_time'] = date('Y-m-d H:i:s',$arr['add_time']);
            $arr['user_name'] = session('user_name');
            echo json_encode($arr);
        }
    }

    //获取"评论"、"回复"列表信息
    function showlist($goods_id){
        $comment = D('Comment');

        $commentinfo = $comment 
                -> alias('c')
                -> join('left join __BACK__ b on c.cmt_id=b.cmt_id')
                -> join('left join __USER__ u on b.user_id=u.user_id')
                -> join('__USER__ uu on c.user_id=uu.user_id')
                -> field('c.cmt_id,c.cmt_msg,from_unixtime(c.add_time) as cmt_add_time,c.cmt_star,uu.user_name as cmt_user_name,b.back_id,b.back_msg,from_unixtime(b.add_time) as back_add_time,u.user_name as back_user_name')
                -> order('c.cmt_id desc')
                -> where(array('c.goods_id'=>$goods_id))
                -> select();
        //SELECT c.cmt_id,c.cmt_msg,from_unixtime(c.add_time) as cmt_add_time,uu.user_name as cmt_user_name,b.back_id,b.back_msg,from_unixtime(b.add_time) as back_add_time,u.user_name as back_user_name FROM sp_comment c left join sp_back b on c.cmt_id=b.cmt_id left join sp_user u on b.user_id=u.user_id INNER JOIN sp_user uu on c.user_id=uu.user_id WHERE c.goods_id = '18' ORDER BY c.cmt_id desc
        $tmp = array();
        $tmp1 = array();
        foreach($commentinfo as $k => $v){
            $tmp[$v['cmt_id']]['cmt_id'] = $v['cmt_id'];
            $tmp[$v['cmt_id']]['cmt_star'] = $v['cmt_star'];
            $tmp[$v['cmt_id']]['cmt_msg'] = $v['cmt_msg'];
            $tmp[$v['cmt_id']]['cmt_user_name'] = $v['cmt_user_name'];
            $tmp[$v['cmt_id']]['cmt_add_time'] = $v['cmt_add_time'];
            if(!empty($v['back_id'])){
                $tmp1['back_id'] = $v['back_id'];
                $tmp1['back_msg'] = $v['back_msg'];
                $tmp1['back_add_time'] = $v['back_add_time'];
                $tmp1['back_user_name'] = $v['back_user_name'];
                $tmp[$v['cmt_id']]['cmt_back'][] = $tmp1;
            }
        }
        return $tmp;
        //return $commentinfo;
    }

    //根据goods_id、page_index 获取每页的评论信息
    function getPageInfo(){
        $goods_id = I('get.goods_id');
        $page_index = I('get.page_index');
        $per = 3;
        $comment = D('Comment');
        //获取评论
        $commentinfo = $comment
                -> alias('c')
                -> join('__USER__ uu on c.user_id=uu.user_id')
                -> field('c.cmt_id,c.cmt_msg,from_unixtime(c.add_time) as cmt_add_time,c.cmt_star,uu.user_name as cmt_user_name')
                -> order('c.cmt_id desc')
                -> limit($page_index*$per,$per)
                -> where(array('c.goods_id'=>$goods_id))
                -> select();
        //dump($commentinfo);
        $cmt_ids = arraytostring($commentinfo,'cmt_id');
        //dump($cmt_ids);
        //exit;
        //获取回复
        $backinfo = D('Back')
                -> alias('b')
                -> join('left join __USER__ u on b.user_id=u.user_id')
                -> field('b.back_id,b.back_msg,from_unixtime(b.add_time) as back_add_time,u.user_name as back_user_name,b.cmt_id')
                -> where(array('cmt_id'=>array('in',"$cmt_ids")))
                -> select();
        //dump($backinfo);
        //exit;

        //合并回复和评论，变为4维数组
        $tmp = array();
        $tmp1 = array();
        foreach($commentinfo as $k => $v){
            $tmp[$v['cmt_id']]['cmt_id'] = $v['cmt_id'];
            $tmp[$v['cmt_id']]['cmt_star'] = $v['cmt_star'];
            $tmp[$v['cmt_id']]['cmt_msg'] = $v['cmt_msg'];
            $tmp[$v['cmt_id']]['cmt_user_name'] = $v['cmt_user_name'];
            $tmp[$v['cmt_id']]['cmt_add_time'] = $v['cmt_add_time'];
            foreach($backinfo as $kk => $vv){
                if($v['cmt_id'] === $vv['cmt_id']){
                    $tmp1['back_id'] = $vv['back_id'];
                    $tmp1['back_msg'] = $vv['back_msg'];
                    $tmp1['back_add_time'] = $vv['back_add_time'];
                    $tmp1['back_user_name'] = $vv['back_user_name'];
                    $tmp[$v['cmt_id']]['cmt_back'][] = $tmp1;
                }
            }
        }
        //dump($tmp);
        echo json_encode($tmp);
    }

    //给评论添加回复消息入库
    function sendback(){
        $back = D('Back');
        $arr = array(
            'cmt_id'    => I('post.cmt_id'),
            'back_msg'  => I('post.backmsg'),
            'user_id'   => session('user_id'),
            'add_time'  => time(),
        );
        if($back->add($arr)){
            $arr['add_time'] = date('Y-m-d H:i',$arr['add_time']);
            $arr['user_name'] = session('user_name');

            echo json_encode($arr);
        }
    }
}
