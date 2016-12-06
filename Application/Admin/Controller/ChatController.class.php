<?php

namespace Admin\Controller;
use Admin\Common\MyPage;
use Home\Model\ChatModel;
use Think\Controller;
use Think\Model;
use Think\Page;

class ChatController extends CommonController
{
    public function index()
    {
        // 分页
//        $count = count(M('chat')->group('u_from_id')->select());
        $count = M('chat')->count('distinct(u_from_id)');
//                echo "<pre>";
//        var_dump($count);die;
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,3);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        // 显示分页
        $show = $page->show('Admin/Chat/friendSearch');
        // 按照分页查询数据
//        $list = M('chat')->table('rr_chat c,rr_user u')->where('c.u_from_id=u.id')->group('u_from_id')->order('id asc')->limit($page->firstRow.','.$page->listRows)->field('c.*,u.name')->select();
//        $list = M('chat')->table('rr_chat c,rr_user u')->where('c.u_from_id=u.id')->order('id asc')->field('c.*,u.name')->limit($page->firstRow.','.$page->listRows)->select('distinct(u_from_id)');
        $list = (new ChatModel())->relation(true)->order('id asc')->limit($page->firstRow.','.$page->listRows)->select();
//        echo "<pre>";
//        var_dump($list);die;
        $this->assign('page', $show);
        $this->assign('list',$list);
        $this->display('Chat/index');
    }

    //聊天用户搜索
    public function friendSearch()
    {
        $where = [];
        if($_POST['u_from_id'] != ''){
            $where['u_from_id'] = $_POST['u_from_id'];
        }
        // 分页
//        $count = count(M('chat')->group('u_from_id')->where($where)->select());
        $count = M('chat')->count('distinct(u_from_id)');
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,3);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        // 显示分页
        $show = $page->show();
        // 按照分页查询数据
//        $list = M('chat')->field('c.*,u.name')->table('rr_chat c,rr_user u')->where('c.u_from_id=u.id')->group('u_from_id')->where($where)->order('id asc')->limit($page->firstRow.','.$page->listRows)->select();
        $list = (new ChatModel())->relation(true)->limit($page->firstRow.','.$page->listRows)->order('id asc')->select();
//        echo "<pre>";
//        var_dump($list);die;
        $this->assign('page', $show);
        $this->assign('list',$list);
        $this->display('Chat/friendSearch');
    }

    //聊天对象
    public function friend()
    {
        // 分页
//        $count = count(M('chat')->group('u_to_id')->where(array('u_from_id'=>$_GET['id']))->select());
        $count = M('chat')->where(array('u_from_id'=>$_GET['id']))->count('distinct(u_to_id)');
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,3);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        // 显示分页
        $show = $page->show('Admin/Chat/chatSearch');
        // 按照分页查询数据
//        $list = M('chat')->field('c.*,u.name')->table('rr_chat c,rr_user u')->where('c.u_to_id=u.id')->group('u_to_id')->where(array('u_from_id'=>$_GET['id']))->order('id asc')->limit($page->firstRow.','.$page->listRows)->select();
        $list = (new ChatModel())->relation(true)->where(array('u_to_id'=>$_GET['id']))->order('id asc')->limit($page->firstRow.','.$page->listRows)->select();
//        echo "<pre>";
//        var_dump($list);die;
        $user = M('user')->where(array('id'=>$_GET['id']))->select();
        $this->assign('page', $show);
        $this->assign('list',$list);
        $this->assign('user',$user[0]);
        $this->display('Chat/friend');
    }

    //聊天对象搜索
    public function chatSearch()
    {
        $where = [];
        if($_POST['u_to_id'] != ''){
            $where['u_to_id'] = $_POST['u_to_id'];
        }
        // 分页
//        $count = count(M('chat')->group('u_to_id')->where($where)->where(array('u_from_id'=>$_POST['id']))->select());
        $count = M('chat')>where($where)->where(array('u_from_id'=>$_POST['id']))->count('distinct(u_to_id)');
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,3);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        // 显示分页
        $show = $page->show();
        // 按照分页查询数据
//        $list = M('chat')->field('c.*,u.name')->table('rr_chat c,rr_user u')->where('c.u_to_id=u.id')->group('u_to_id')->where($where)->where(array('u_from_id'=>$_POST['id']))->limit($page->firstRow.','.$page->listRows)->select();
        $list = (new ChatModel())->relation(true)->where($where)->where(array('u_from_id'=>$_POST['id']))->limit($page->firstRow.','.$page->listRows)->select();
//        echo "<pre>";
//        var_dump($list);die;
        $user = M('user')->where(array('id'=>$_POST['id']))->select();
        $this->assign('page', $show);
        $this->assign('list',$list);
        $this->assign('user',$user[0]);
        $this->display('Chat/chatSearch');
    }

    //聊天记录详情
    public function detail()
    {
        $data = new Model();
//        $list = $data->query("select * from rr_chat where (u_from_id={$_GET['u_from_id']} and u_to_id={$_GET['u_to_id']}) or (u_from_id={$_GET['u_to_id']} and u_to_id={$_GET['u_from_id']}) order by time desc");
        $list = $data->query("select c.*,u.name from rr_chat as c left join rr_user as u on c.u_from_id=u.id where (u_from_id={$_GET['u_from_id']} and u_to_id={$_GET['u_to_id']}) or (u_from_id={$_GET['u_to_id']} and u_to_id={$_GET['u_from_id']}) order by time desc");
//        echo "<pre>";
//        echo M('chat')->getLastSql().'<br>';
//        var_dump($list);die;
        $this->assign('fname',$_GET['u_from_name']);
        $this->assign('tname',$_GET['u_to_name']);
        $this->assign('list',$list);
        $this->display('Chat/detail');
    }
}