<?php

namespace Admin\Controller;
use Admin\Common\MyPage;
use Think\Controller;
use Think\Page;

class AdminController extends CommonController
{
    //管理员列表
    public function index()
    {
        //判断用户权限
        $admin = M('admin')->where(array('id'=>session('admin_id')))->select();
        if($admin[0]['level'] == 0){//没有权限
            $this->error('没有权限','/Admin/User/index');
        }
        // 分页
        $count = M('admin')->count();
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,4);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        // 显示分页
        $show = $page->show('Admin/Admin/adminSelect');
        // 按照分页查询数据
        $list = M('admin')->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('title','管理员列表');
        $this->display('Admin/index');
    }

    //封禁、解封
    public function ban($id)
    {
        //接收ID
        $id = I('get.id/d');
        $data = M('admin')->where(array('id'=>$id))->select();
        //启用状态
        if($data[0]['status'] == 0){
            $data[0]['status'] = 1;
            M('admin')->where(array('id'=>$id))->save($data[0]);
            $this->redirect('Admin/index');
            die;
        }
        //禁用状态
        if($data[0]['status'] == 1){
            $data[0]['status'] = 0;
            M('admin')->where(array('id'=>$id))->save($data[0]);
            $this->redirect('Admin/index');
            die;
        }
    }

    //执行添加
    public function doadd()
    {
        //用户名唯一
        $name = M('admin')->where(array('name'=>$_POST['name']))->select();
        if($name){
            $this->redirect('add\s\3');
            die;
        }
        $data = [];
        $data['name'] = $_POST['name'];
        $data['pwd'] = $_POST['password'];
        $data['status'] = 0;//状态(默认启用)
        $data['level'] = 0;//0为普通管理员

        //实例化对象
        $admin = M('admin');
        //过滤数据,数据验证
        if (!$admin->create($data)) {
            //如果创建数据失败,表示验证没有通过
            //输出错误信息 并且跳转
            $this->error($admin->getError());
        } else {
            //验证通过 执行添加操作
            //执行添加
            if ($admin->add($data) > 0) {
                $this->redirect('add\s\1');
            } else {
                $this->redirect('add\s\2');
            }
        }
    }

    //添加页面
    public function add($s = 0)
    {
        if($s == 1){
            $this->assign('success',1);
        }elseif ($s == 2){
            $this->assign('success',2);
        }elseif ($s == 3){
            $this->assign('success',3);
        }
        $this->display('Admin/add');
    }

    // 执行删除
    public function delete($id)
    {
        $admin = M('admin')->where(array('id'=>$id))->select();
        if($admin[0]['level'] == 1){
            $this->error('超级管理员无法删除');
        }
        $comment = M('admin');
        // 删除成功
        if ($comment->where(['id' => ['eq', $id]])->delete()) {
            $this->redirect('del\s\1');
        }else {
            $this->redirect('del\s\2');;
        }
    }

    //删除页面
    public function del($s = 0)
    {
        if($s == 1){
            $this->assign('success',1);
        }elseif ($s == 2){
            $this->assign('success',2);
        }
        $this->display('Admin/index');
    }

    //管理员搜索
    public function adminSelect($status = '',$name = '')
    {
        // 拼接搜索条件   设置分页条件
        $where = [];
        if(trim($status) == ''){
            $where['name'] = ['like',"%$name%"];
        }elseif(trim($name) == ''){
            $where['status'] = ['eq',$status];
        }else{
            $where['name'] = ['like',"%$name%"];
            $where['status'] = ['eq',$status];
        }

        // 分页
        $count = M('admin')->where($where)->count();
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,4);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');

        //设置分页条件
        $page->parameter['status'] = urlencode($status);
        // 显示分页
        $show = $page->show();
        // 按照分页查询数据
        $list = M('admin')->where($where)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display('Admin/adminSelect');
    }
}