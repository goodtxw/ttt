<?php
    namespace Admin\Controller;

    use Admin\Common\MyPage;
    use Admin\Model\CommentModel;
    use Think\Page;

    class CommentController extends CommonController
    {
        public function index()
        {
            $comment = new CommentModel();
            // 总页数
            $count = $comment->count();
            // 设置分页
            $page = new Page($count, 5);
            // 设置上一页下一页
            $page->setConfig('prev', '上一页');
            $page->setConfig('next', '下一页');
            // 显示分页
            $show = $page->show();
            // 按照分页查询数据
            $list = $comment->relation(true)->where()->limit($page->firstRow.','.$page->listRows)->select();
            $this->assign('list', $list);
            $this->assign('page', $show);
            $this->display('Comment/index');
        }


        // 删除评论
        public function delete($id)
        {
            $comment = M('comment');
            // 删除成功
            if ($comment->where(['id' => ['eq', $id]])->delete()) {
                echo 0;
            }else {
                echo 1;
            }
        }

        // 搜索
        public function commentSearch($user_id='', $article_id='')
        {
            // 拼接搜索条件   设置分页条件
            $where = [];
            if (trim($article_id) == '') {
                $where['u_id'] = ['eq', $user_id];
            }elseif (trim($user_id) == '') {
                $where['article_id'] = ['eq', $article_id];
            }else {
                $where['u_id'] = ['eq', $user_id];
                $where['article_id'] = ['eq', $article_id];
            }

            $comment = new CommentModel();
            // 总页数
            $count = $comment->where($where)->count();
            // 设置分页
            $page = new MyPage($count, 3);
            // 设置上一页下一页
            $page->setConfig('prev', '上一页');
            $page->setConfig('next', '下一页');


            //设置分页条件
            $page->parameter['user_id'] = urlencode($user_id);
            $page->parameter['article_id'] = urlencode($article_id);

            // 显示分页
            $show = $page->show();

            // 按照分页查询数据
            $list = $comment->relation(true)->where($where)->limit($page->firstRow.','.$page->listRows)->select();

            $this->assign('page', $show);
            $this->assign('list', $list);
            $this->display('Comment/commentSearch');
        }

    }