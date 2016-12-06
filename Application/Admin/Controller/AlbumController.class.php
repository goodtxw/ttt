<?php

    namespace Admin\Controller;

    use Admin\Common\MyPage;
    use Admin\Model\AlbumModel;
    use Admin\Model\ImageModel;
    use Think\Page;

    class AlbumController extends CommonController
    {
        // 相册列表
        public function index()
        {
            $album = new AlbumModel();
            // 分页
            $count = $album->count();
            // 设置分页  总页数/每页数量
            $page = new MyPage($count,5);
            // 设置上一页下一页
            $page->setConfig('prev', '上一页');
            $page->setConfig('next', '下一页');
            // 显示分页
            $show = $page->show('/Admin/Album/albumSearch');
            // 按照分页查询数据
            $list = $album->relation(true)->limit($page->firstRow.','.$page->listRows)->select();

            $this->assign('list', $list);
            $this->assign('page', $show);
            $this->assign('title', '相册列表');
            $this->display('Album/index');
        }

        // 查看相册中的图片
        public function albumDetial($id)
        {
            $image = new ImageModel();
            // 分页
            $count = $image->where(['album_id' => ['eq', $id]])->count();
            // 设置分页  总页数/每页数量
            $page = new Page($count,8);
            // 设置上一页下一页
            $page->setConfig('prev', '上一页');
            $page->setConfig('next', '下一页');
            // 设置分页查询条件
            $page->parameter['id'] = urlencode($id);
            // 显示分页
            $show = $page->show();
//            var_dump($count);exit;
            // 按照分页查询数据
            $list = $image->relation(true)->where(['album_id' => ['eq', $id]])->limit($page->firstRow.','.$page->listRows)->select();
//            echo '<pre>';var_dump($list);exit;
            $this->assign('list', $list);
            $this->assign('page', $show);
            $this->display('Album/albumDetial');
        }

        // 违规图片的封禁
        public function ban($imageId)
        {
            // 根据图片的id取得图片的名称
            $image = M('image');
            $date = $image->find($imageId);
            // 拼接图片的完整路径
            $filename = C('LOCAL_PATH').'/upload/' . $date['name'];

            // 删除文件
            if (unlink($filename)) {
                // 替换图片
                $img = [];
                $img['name'] = 'weigui.jpg';
                $img['ban'] = 1;
                $image->where(['id'=>['eq', $imageId]])->save($img);

                $re = [];
                $re[] = 0;
                // 返回替换后图片的路径
//                $re[] = C('Public').'/upload/' . $img['name'];
                $re[] = '/php/TP_Social/Public/upload/' . $img['name'];
            }else {
                $re = [];
                $re[] = 1;
                $re[] = '封禁失败';
            }

            echo json_encode($re);
        }

        public function albumSearch($user_id='')
        {
            $where = [];
            if ($user_id != '') {
                $where['u_id'] = ['eq', $user_id];
            }
            $album = new AlbumModel();
            // 分页
            $count = $album->where($where)->count();
            // 设置分页  总页数/每页数量
            $page = new MyPage($count,5);
            // 设置上一页下一页
            $page->setConfig('prev', '上一页');
            $page->setConfig('next', '下一页');

            // 设置查询条件
            if ($user_id != '') {
                $page->parameter['user_id'] = urlencode($user_id);
            }

            // 显示分页
            $show = $page->show();
            // 按照分页查询数据
            $list = $album->relation(true)->where($where)->limit($page->firstRow.','.$page->listRows)->select();
            $this->assign('list', $list);
            $this->assign('page', $show);
            $this->display('Album/albumSearch');
        }
    }