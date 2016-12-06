<?php

namespace Home\Controller;

use Home\Model\ArticleModel;

class HomepageController extends BaseController
{
    public function index($u_id)
    {
        $user = M('user');
        //个人的基本信息
        $personal_info = $user->where(array('id'=>session('id')))->select();
        //用户注册天数 intval((当前时间-注册时间)/一天的秒数) 一天86400秒
        $date = intval((time()-$personal_info[0]['create_time'])/86400);

        //他人信息
        $other_info = $user->where(array('id'=>$u_id))->find();
        $isFriend = false;
        if ($other_info) {
            $school = M('school')->where(['u_id'=>['eq',$other_info['id']]])->find();
            if ($other_info['status'] == 1) {//用户被禁用
                $this->error('该用户已被封禁');
            }
            //判断是否为本人，本人直接遍历所有信息，否则要判断权限(privacy 0所有人可见 1好友可见 2仅自己可见)
            if ($u_id == session('id')) {
                $art = (new ArticleModel())->relation('Image')->where(['u_id' => ['eq', session('id')]])->select();
            } else {
                // 查询登录用户的好友分组id
                $fg = M('friend_group')->where(['u_id' => ['eq', session('id')]])->field('id')->select();
                $fg_id = [];
                foreach ($fg as $k => $v) {
                    $fg_id[] = $v['id'];
                }
                // 判断该好友所在的分组id是否在数组中
                $u_fg_id = M('friend')->where(['friend_id' => ['eq', $u_id]])->select();

                foreach ($u_fg_id as $k => $v) {
                    if (in_array($v['fg_id'], $fg_id)) {
                        $isFriend = true;
                        break;
                    }
                }
                // 是否是好友
                if ($isFriend) {
                    if ($other_info['privacy'] != 2) {
                        $art = (new ArticleModel())->relation('Image')->where(['u_id' => ['eq', $u_id], 'privacy' => ['neq', 2]])->select();
                    } elseif ($other_info['privacy'] == 2) {
                        $this->error('该用户已设置权限仅自己可见');
                    }
                } else {
                    if ($other_info['privacy'] == 0) {
                        $art = (new ArticleModel())->relation('Image')->where(['u_id' => ['eq', $u_id], 'privacy' => ['eq', 0]])->select();
                    } else {
                        $this->error('该用户已设置权限仅自己和好友可见');
                    }
                }
            }

            foreach ($art as $k=>$v){
                if ($v['pid'] != 0){
                    $art[$k][za] = (new ArticleModel())->relation('Image')->where(['id'=>['eq',array_pop(explode(',',$v['pid']))]])->select();
                }
            }
//            echo '<pre>';
//            var_dump($art);
//            exit;
            //消息提醒
            $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
            $this->assign('remind',$remind);
            $link = M('flink')->where(array('show' => 1))->select();
            $this->assign('link', $link);
            $this->assign('isFriend', $isFriend);
            $this->assign('school', $school);
            $this->assign('art', $art);
            $this->assign('date', $date);
            $this->assign('uinfo', $personal_info[0]);
            $this->assign('info', $other_info);
            $this->assign('title', '个人主页');
            $this->display();
        }else {
            $this->error('用户不存在');
        }
    }

    public function add(){
        if ($_POST['uid'] != ''){
            $data = [];
            $data['u_id'] = session('id');
            $data['r_id'] = $_POST['uid'];
            $data['text'] = $_POST['text'];
            $data['time'] = time();
            $data['agree'] = 0;
            if(M('friend_request')->where(['u_id'=>['eq',session('id')],'r_id'=>['eq',$_POST['uid']]])->select()){
                if(M('friend_request')->where(['u_id'=>['eq',session('id')],'r_id'=>['eq',$_POST['uid']]])->save($data)){
                    echo 'suc';
                }
            }else{
                if(M('friend_request')->add($data)){
                    echo 'suc';
                }
            }
        }
    }
}