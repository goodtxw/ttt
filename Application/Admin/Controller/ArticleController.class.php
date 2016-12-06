<?php

namespace Admin\Controller;
use Admin\Common\MyPage;
use Admin\Model\ArticleModel;
use Think\Controller;
use Think\Page;

class ArticleController extends CommonController
{
    public function index($s = 0)
    {
        if($s == 1){
            $this->assign('success',1);
        }elseif ($s == 2){
            $this->assign('success',2);
        }

        $art = new ArticleModel();
        // 分页
        $count = $art->count();
        // 设置分页  总页数/每页数量
        $page = new MyPage($count,4);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');
        // 显示分页
        $show = $page->show('Admin/Article/articleSearch');
        // 按照分页查询数据
        $list = $art->relation(true)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('title','文章列表');
        $this->display('Article/index');
    }

    //详情
    public function info()
    {
        //接收ID
        $id = I('get.id/d');
        //实例化对象
        $art = new ArticleModel();
        $data = $art->relation(true)->where(array('id'=>$id))->select();
        $image = M('image')->where(array('article_id'=>$id))->select();

        if($data[0]['username2'] == ''){
            $this->assign('data',$data[0]);
            $this->assign('data1',$data[0]['username1']);
        }else{
            $this->assign('data',$data[0]);
            $this->assign('data1',$data[0]['username2']);
        }
        $this->assign('image',$image);
        $this->display('Article/info');
    }

    //删除
    public function del()
    {
        //接收ID
        $id = I('get.id/d');
        M('image')->article_id = 0;
        M('image')->where(array('article_id'=>$id))->save();
        if(M('article')->delete($id)>0){
            $this->redirect('index\s\1');
        }else{
            $this->redirect('index\s\2');
        }
    }

    //搜索
    public function articleSearch($id = '',$u_id = '')
    {
        // 拼接搜索条件   设置分页条件
        $where = [];
        if (trim($id) != '') {
            $where['id'] = ['eq', $id];
        }
        if (trim($u_id) != '') {
            $where['u_id'] = ['eq', $u_id];
        }

        $art = new ArticleModel();
        // 总页数
        $count = $art->where($where)->count();
        // 设置分页
        $page = new MyPage($count,4);
        // 设置上一页下一页
        $page->setConfig('prev', '上一页');
        $page->setConfig('next', '下一页');


        //设置分页条件
        if(trim($u_id) != ''){
            $page->parameter['u_id'] = urlencode($u_id);
        }
        if(trim($id) != ''){
            $page->parameter['id'] = urlencode($id);
        }


        // 显示分页
        $show = $page->show();

        // 按照分页查询数据
        $list = $art->relation(true)->where($where)->limit($page->firstRow.','.$page->listRows)->order('id desc')->select();

        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display('Article/articleSearch');
    }
}