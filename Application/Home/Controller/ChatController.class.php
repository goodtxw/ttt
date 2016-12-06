<?php

    namespace Home\Controller;

    use Home\Model\ChatModel;

    class ChatController extends BaseController
    {
        public function chatNow($user_id='') {
            if ($user_id != '') {
                $data = [];
                $data['u_id'] = session('id');
                $content1 = M('chat')->where(['u_from_id'=>['eq',session('id')], 'u_to_id'=>['eq',$user_id]])->order('time desc')->find();
                $content2 = M('chat')->where(['u_from_id'=>['eq',$user_id], 'u_to_id'=>['eq',session('id')]])->order('time desc')->find();
                if ($content1['time'] > $content2['time']){
                    $data['content'] = $content1['content'];
                    $data['time'] = $content1['time'];
                    $data['u_from_id'] = $content1['u_from_id'];
                    $data['chat_id'] = $user_id;
                }else {
                    $data['content'] = $content2['content'];
                    $data['time'] = $content2['time'];
                    $data['u_from_id'] = $content2['u_from_id'];
                    $data['chat_id'] = $user_id;
                }
                 // 查看数据库中是否存在u_id chat_id
                if ($c = M('chatnow')->where(['u_id'=>['eq',$data['u_id']], 'chat_id'=>['eq',$data['chat_id']]])->find()){
                    // 判断时间   那个是最新消息
                    if ($data['time'] > $c['time']){
                        if(M('chatnow')->save($data)){
                            echo json_encode($data);
                        }
                    }else {
                        echo json_encode($c);
                    }
                }else {
                    if(M('chatnow')->add($data)){
                        echo json_encode($data);
                    }
                }
            }
        }

        // 搜索chatnow中的某条数据
        public function search($user_id)
        {
            if ($user_id != ''){
                $list = M('chatnow')->where(['u_id'=>['eq',session('id')],'chat_id'=>['eq',$user_id]])->find();
                // 判断是否有数据
                if ($list){
                    echo json_encode($list);
                }
            }
        }

        // 忘chatnow中插入数据
        public function changeMsgStatu($from_id)
        {
            if ($from_id != ''){
                $where = [];
                $where['u_to_id'] = session('id');
                $where['u_from_id'] = $from_id;
                $where['read'] = 0;
                $data = [];
                $data['read'] = 1;
                M('chat')->where($where)->save($data);
            }
        }

        // 查询数据库唯独信息
        public function messageNotRead()
        {
            $chat = new ChatModel();
            $list = $chat->relation(true)->where(['u_to_id'=>['eq',session('id')], 'read'=>['eq',0]])->order('time asc')->select();
            foreach ($list as $key=>$v) {
                $data = [];
                $data['u_id'] = $v['u_to_id'];
                $data['content'] = $v['content'];
                $data['u_from_id'] = $v['u_from_id'];
                $data['chat_id'] = $v['u_from_id'];
                $data['time'] = $v['time'];
                if ($cn = M('chatnow')->where(['u_id'=>['eq',session('id')],'chat_id'=>['eq',$v['u_from_id']]])->find()){
                    if ($cn['time'] < $v['time']){
                        M('chatnow')->save($data);
                    }
                }else {
                    M('chatnow')->add($data);
                }
            }
            echo json_encode($list);
        }
    }