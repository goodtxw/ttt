<?php

    namespace Home\Controller;

    use Home\Model\ArticleModel;
    use Home\Model\ChatnowModel;
    use Home\Model\FriendGroupModel;
    use Home\Model\SchoolModel;

    class IndexController extends BaseController
    {
        public function index($t = '')
        {
            if (!session('id')){
                $this->redirect('/Home/Login/login');
                exit;
            }
            // 当前登录的用户id
            $u_id = session('id');
            // 查找当前用户的信息
            $user = M('user')->where(['id' => ['eq', $u_id]])->find();
            // 好友id字符串
            $friend = '';
            // 所有好友id 聊天用
            $friend_id = '';
            // 查询好友id
            $friendGroup = new FriendGroupModel();
            $fg = $friendGroup->relation(true)->where(['u_id' => ['eq', $u_id]])->select();
            if (count($fg)>0) {
                // 遍历
                foreach ($fg as $key => $value){
                    foreach ($value['Friend'] as $k => $v) {
                        $friend .= $v['friend_id'] . ',';
                    }
                }

                // 查出所有好友id
                $arr = explode(',',$friend);
                array_pop($arr);
                $friend_id = implode(',',$arr);

                // 将自己加入
                if ($friend != '') {
                    $friend .= $u_id;
                } else {
                    $friend = $u_id;
                }
            }else {
                $friend = $u_id;
            }
            // 查询所有好友的信息
            $friendAll = M('user')->where(['id'=>['in',$friend_id]])->field(['id','name','head_image'])->select();
            if ($friendAll){
                // 查询正在会话的好友信息
                $chatNow = (new ChatnowModel())->relation(true)->order("id desc")->where(['u_id'=>['eq',session('id')]])->select();
            }
            // 根据用户的id查询文章 已时间倒叙排序
            $article = new ArticleModel();
            if ($t == 1){
                $ar = $article->relation(true)->where("(u_id in (".$friend.") and privacy<>2 and pid=0) or (u_id=".session('id')." and privacy=2 and pid=0)")->order('time desc')->select();
            }elseif($t == 2) {
                $ar = $article->relation(true)->where("(u_id in (".$friend.") and privacy<>2 and pid<>0) or (u_id=".session('id')." and privacy=2 and pid<>0)")->order('time desc')->select();
            }else{
                $ar = $article->relation(true)->where("(u_id in (".$friend.") and privacy<>2) or (u_id=".session('id')." and privacy=2)")->order('time desc')->select();
            }
            if (count($ar)>0){
                // 查找评论用户的name与image
                $comment_user = [];
                $reply_user = [];
                foreach ($ar as $k=>$v){
                    // 判断文章是否时转载
                    if ($v['pid'] > 0){
                        $ar[$k]['Image'] = M('image')->where(['article_id'=>['eq',array_pop(explode(',',$v['pid']))]])->select();
                        $ar[$k]['zhuan'] = M('article')->where(['id'=>['eq',array_pop(explode(',',$v['pid']))]])->find();
                        $ar[$k]['zuser'] = M('user')->where(['id'=>['eq',$ar[$k]['zhuan']['u_id']]])->field('name')->find();
                    }
                    foreach ($v['Comment'] as $key=>$value){
                        $comment_user[$v['id']][$value['id']] = M('user')->where(['id'=>['eq',$value['u_id']]])->field(['id','name','head_image'])->select();
                        if ($value['pid'] != 0){
                            $reply_comment = M('comment')->where(['id'=>['eq',$value['pid']]])->find();
                            $reply_user[$v['id']][$value['id']] = M('user')->where(['id'=>['eq',$reply_comment['u_id']]])->select();
                        }
                    }
                }
            }
            //个人的基本信息
            $personal_info = M('user')->where(array('id'=>session('id')))->select();
            //用户注册天数 intval((当前时间-注册时间)/一天的秒数) 一天86400秒
            $date = intval((time()-$personal_info[0]['create_time'])/86400);
            $link = M('flink')->where(array('show'=>1))->select();

            // 推荐好友
            $user_school = M('school')->where(['u_id'=>['eq',session('id')]])->find();
            if($user_school){
                $us = (new SchoolModel())->relation(true)->where("u_id<>{$user_school['u_id']} and (college='{$user_school['college']}' or middle_school='{$user_school['middle_school']}' or high_school='{$user_school['high_school']}')")->select();
                $uf = [];
                if (($cou = count($us)) > 6) {
                    $i = 0;
                    while($i < 6){
                        $num = mt_rand(0, $cou-1);
                        $uf[$num] = $us[$num];
                        $i++;
                    }
                    $this->assign('us',$uf);
                }else {
                    $this->assign('us',$us);
                }
            }
            $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
            $this->assign('remind',$remind);
            $this->assign('link',$link);
            $this->assign('date',$date);
            $this->assign('article', $ar);
            $this->assign('cuser', $comment_user);
            $this->assign('ruser', $reply_user);
            $this->assign('user', $user);
            $this->assign('friend', $friendAll);
            $this->assign('chatnow', $chatNow);
            $this->display();
        }

        public function subArt()
        {
            $data = Array();
            // 用户id
            $data['u_id'] = session('id');
            // 内容
            $data['content'] = $_POST['content'];
            // 是否公开
            $data['privacy'] = $_POST['privacy'];
            $data['time'] = time();
            $art = M('article');
            if ($insertId = $art->add($data)) {
                // 增加积分
                $u = M('user')->where(['id'=>['eq',session('id')]])->find();
                $data = [];
                $data['u_id']=$u['id'];
                $data['integral']=5;
                $data['way']=1;
                $data['article_id']=$insertId;
                $data['com_id']=0;
                $data['time']=time();
                M('integral')->add($data);
                // 图片名
                $img = explode(',', $_POST['img']);
                array_pop($img);
                if (count($img) > 0) {
                    $album = M('album');
                    // 查找新上传图片相册的id 不存在则创建
                    if (!($album_id = $album->where(['name' => ['eq', '新上传图片']])->getField('id'))) {
                        $data = Array();
                        $data['u_id'] = session('id');
                        $data['name'] = '新上传图片';
                        $data['time'] = time();
                        // 创建相册
                        if ($album_id = $album->add($data)) {
                            $image = M('image');
                            $data = Array();
                            foreach ($img as $k=>$v) {
                                $data[$k]['name'] = $v;
                                // 相册id
                                $data[$k]['album_id'] = $album_id;
                                // 文章id
                                $data[$k]['article_id'] = $insertId;
                                if ($k == 0) {
                                    $data[$k]['cover'] = 1;
                                }else {
                                    $data[$k]['cover'] = 0;
                                }

                                $data[$k]['time'] = time();
                                // 用户id
                                $data[$k]['u_id'] = session('id');
                            }
                            if ($image->addAll($data)){
                                echo 'suc';
                            }else {
                                echo 'err';
                            }
                        }
                    } else {
                        $image = M('image');
                        $data = Array();
                        foreach ($img as $k=>$v) {
                            // 文章id
                            $data[$k]['article_id'] = $insertId;
                            // 相册id
                            $data[$k]['album_id'] = $album_id;
                            // 用户id
                            $data[$k]['u_id'] = session('id');
                            $data[$k]['name'] = $v;
                            $data[$k]['time'] = time();
                        }
                        if ($image->addAll($data)){
                            echo 'suc';
                        }else {
                            echo 'err3';
                        }
                    }
                }else {
                    echo 'err2';
                }
            }else {
                echo 'err1';
            }
        }

        // 提交评论
        public function subComment($article_id, $content, $comment_id='')
        {
            $comment = M('comment');
            $data = [];
            if ($comment_id){
                $data['pid'] = $comment_id;
                $arr = explode(':',$content);
                array_shift($arr);
                $data['content'] = trim(implode(':',$arr));
            }else{
                $data['content'] = $content;
                $data['pid'] = 0;
            }
            $data['article_id'] = $article_id;
            $data['u_id'] = session('id');
            $data['time'] = time();
            if($lastInsertId = $comment->add($data)){
                // 增加积分
                $u = M('user')->where(['id'=>['eq',session('id')]])->find();
                $data = [];
                $data['u_id']=$u['id'];
                $data['integral']=5;
                $data['way']=2;
                $data['article_id']=0;
                $data['com_id']=$article_id;
                $data['time']=time();
                M('integral')->add($data);

                $commentInfo = $comment->where(['id'=>['eq',$lastInsertId]])->find();
                $user = M('user');
                $userInfo = $user->where(['id'=>['eq',$commentInfo['u_id']]])->find();
                if ($commentInfo['pid'] != 0){
                    $replyComment = $comment->where(['id'=>['eq',$commentInfo['pid']]])->find();
                    $repluUser = $user->where(['id'=>['eq',$replyComment['u_id']]])->find();
                }
                echo '<li><div class="feed-reply-container" style="margin-bottom: -10px">
                        <div class="feed-replies">
                            <div class="a-reply clearfix">
                                <a href="" class="avatar" target="_blank">
                                    <img src="/php/TP_Social/Public/upload/' . $userInfo['head_image'] . '" class="user-avatar">
                            </a>
                            <div class="reply-content">
                                <p class="reply-info clearfix">
                                    <a href="" class="user-name ttt-pinglun-name'.$commentInfo['id'].'" target="_blank" >
                                        '.$userInfo['name'].'
                                    </a>
                                    <span class="reply-time">
                                        '.date( 'Y-m-d H:i',$commentInfo['time']).'
                                    </span>
                                    <span class="reply-tool clearfix">
                                        <a href="javascript:void(0)" onclick="replayTopeople('.$commentInfo['id'].','.$commentInfo['article_id'].')" class="to-reply">
                                            回复
                                        </a>
                                    </span>
                                </p>
                                <span class="text">
                                    '.(($commentInfo['pid']==0)?$commentInfo['content']:('回复'.$repluUser['name'].':  '.$commentInfo['content'])).'
                                </span>
                            </div>
                        </div>
                    </div>
                </div></li>';
            }else {
                echo 'err';
            }
        }

        // 点赞
        public function dianZan($article_id, $user_id)
        {
            if ($article_id != '' && $user_id != '') {
                $data = [];
                $data['article_id'] = $article_id;
                $data['u_id'] = $user_id;
                $zan = M('zan');
                // 查看是否点过赞
                if ($zan->where(['u_id' => ['eq', $user_id], 'article_id' => ['eq', $article_id]])->find()) {
                    // 删除数据
                    if ($zan->where(['u_id' => ['eq', $user_id], 'article_id' => ['eq', $article_id]])->delete()) {
                        echo 'del';
                    } else {
                        echo 'delerr';
                    }
                } elseif ($zan->add($data)) {
                    echo 'add';
                } else {
                    echo 'adderr';
                }
            }
        }

        public function zhuanZai($article_id, $user_id)
        {
            if ($article_id != '' && $user_id != ''){
                $artInfo = M('article')->where(['id'=>['eq',$article_id]])->find();
                $userInfo = M('user')->where(['id'=>['eq',$artInfo['u_id']]])->field(['id','name'])->find();
                $imageInfo = M('image')->where(['article_id'=>['eq',$artInfo['id']]])->select();
                $artInfo['user'] = $userInfo;
                $artInfo['image'] = $imageInfo;
                echo json_encode($artInfo);
            }
        }

        public function subZz($article_id, $user_id, $content)
        {
            if ($article_id != '' && $user_id != ''){
                $zart = M('article')->where(['id'=>['eq',$article_id]])->find();
                if ($zart){
                    $data = [];
                    $data['u_id'] = $user_id;
                    $data['content'] = $content;
                    $data['privacy'] = 0;
                    $data['time'] = time();
                    if($zart['pid'] > 0){
                        $data['pid'] = $article_id . ',' . array_pop(explode(',',$zart['pid']));
                    }else {
                        $data['pid'] = $article_id . ',' . $article_id;
                    }
                    if (M('article')->add($data)){
                        echo 'suc';
                    }else {
                        echo 'err';
                    }
                }
            }
        }

        public function search()
        {
            $user2 = M('user')->where(['name'=>['like','%'.$_POST['name'].'%']])->select();
            echo json_encode($user2);
        }
    }