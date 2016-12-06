<?php

namespace Admin\Controller;
use Admin\Common\MyPage;
use Think\Controller;
use Think\Page;

class LinkController extends CommonController
{
    //链接列表
    public function index($e = 0)
    {
        if($e == 1){
            $this->assign('error',1);
        }
        // 分页
        $count = M('flink')->count();
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,4);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        // 显示分页
        $show = $page->show('Admin/Link/linkSelect');
        // 按照分页查询数据
        $list = M('flink')->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('title','友情链接列表');
        $this->display('Link/index');
    }

    //显示、隐藏
    public function ban($id)
    {
        //接收ID
        $id = I('get.id/d');
        $data = M('flink')->where(array('id'=>$id))->select();
        $count = M('flink')->where(array('show'=>1))->count();
        //隐藏状态
        if($data[0]['show'] == 0){
            //控制前台链接显示不超过四个
            if($count<4){
                $data[0]['show'] = 1;
                M('flink')->where(array('id'=>$id))->save($data[0]);
                $this->redirect('Link/index');
                die;
            }else{
                $this->redirect('Link/index?e=1');
                die;
            }
        }
        //显示状态
        if($data[0]['show'] == 1){
            $data[0]['show'] = 0;
            M('flink')->where(array('id'=>$id))->save($data[0]);
            $this->redirect('Link/index');
            die;
        }
    }

    //执行添加
    public function doadd()
    {
        $data = [];
        $data['webname'] = $_POST['webname'];
        $data['url'] = $_POST['url'];

        //实例化对象
        $admin = M('flink');
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
        }
        $this->display('Link/add');
    }

    // 执行删除
    public function delete($id)
    {
        $comment = M('flink');
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
        $this->display('Link/index');
    }

    //链接搜索
    public function linkSelect($show = '',$name = '')
    {
        // 拼接搜索条件   设置分页条件
        $where = [];
        if(trim($show) == ''){
            $where['webname'] = ['like',"%$name%"];
        }elseif(trim($name) == ''){
            $where['show'] = ['eq',$show];
        }else{
            $where['webname'] = ['like',"%$name%"];
            $where['show'] = ['eq',$show];
        }

        // 分页
        $count = M('flink')->where($where)->count();
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,4);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');

        //设置分页条件
        $page->parameter['show'] = urlencode($show);
        // 显示分页
        $show = $page->show();
        // 按照分页查询数据
        $list = M('flink')->where($where)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display('Link/linkSelect');
    }
}