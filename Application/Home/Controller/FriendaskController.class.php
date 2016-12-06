<?php
namespace Home\Controller;

class FriendaskController extends BaseController
{
    public function index()
    {
        $personal_info = M('user')->where(array('id'=>session('id')))->select();
        //用户注册天数 intval((当前时间-注册时间)/一天的秒数) 一天86400秒
        $date = intval((time()-$personal_info[0]['create_time'])/86400);
        $link = M('flink')->where(array('show'=>1))->select();

        $request = M('friend_request')->field('r.*,u.*')->table('rr_friend_request r,rr_user u')->where('r.u_id=u.id')->where(array('r_id'=>session('id'),'agree'=>0))->select();

//        echo "<pre>";
//        var_dump($request);die;
        //消息提醒
        $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
        $this->assign('remind',$remind);
        $this->assign('request',$request);
        $this->assign('link',$link);
        $this->assign('date',$date);
        $this->assign('info',$personal_info[0]);
        $this->display('Friendask/index');
    }

    //同意好友申请
    public function agree()
    {
        //修改好友请求表，agree=>1(同意) 2(拒绝)
        M('friend_request')->agree = 1;
        M('friend_request')->where(array('r_id'=>session('id'),'u_id'=>$_POST['id']))->save();
        //检测登录用户是否有未分组好友分组，有将好友放在该组，没有创建‘未分组好友’
        $group = M('friend_group')->where(array('u_id'=>session('id'),'name'=>'未分组好友'))->select();
        if(empty($group)){
            //创建分组
            $map = [];
            $map['name'] = '未分组好友';
            $map['u_id'] = session('id');
            $new_id = M('friend_group')->add($map);
//            $new_group = M('friend_group')->where(array('u_id'=>session('id'),'name'=>'未分组好友'))->select();
//            $new_id = $new_group[0]['id'];
            //往分组里添加好友
            $hehe = [];
            $hehe['fg_id'] = $new_id;
            $hehe['friend_id'] = $_POST['id'];
            M('friend')->add($hehe);
            //往chat表插数据(2条)一人一条
            $chat1 = [];
            $chat1['u_from_id'] = session('id');
            $chat1['u_to_id'] = $_POST['id'];
            $chat1['content'] = '你们已经是好友了，开始聊天吧';
            $chat1['read'] = 1;
            $chat1['time'] = time();
            M('chat')->add($chat1);

            $chat2 = [];
            $chat2['u_from_id'] = $_POST['id'];
            $chat2['u_to_id'] = session('id');
            $chat2['content'] = '你们已经是好友了，开始聊天吧';
            $chat2['read'] = 1;
            $chat2['time'] = time();
            M('chat')->add($chat2);
            echo 1;
        }else{
            //往分组里添加好友
            $hehe = [];
            $hehe['fg_id'] = $group[0]['id'];
            $hehe['friend_id'] = $_POST['id'];
            M('friend')->add($hehe);
            //往chat表插数据(2条)一人一条
            $chat1 = [];
            $chat1['u_from_id'] = session('id');
            $chat1['u_to_id'] = $_POST['id'];
            $chat1['content'] = '你们已经是好友了，开始聊天吧';
            $chat1['read'] = 1;
            $chat1['time'] = time();
            M('chat')->add($chat1);

            $chat2 = [];
            $chat2['u_from_id'] = $_POST['id'];
            $chat2['u_to_id'] = session('id');
            $chat2['content'] = '你们已经是好友了，开始聊天吧';
            $chat2['read'] = 1;
            $chat2['time'] = time();
            M('chat')->add($chat2);
            echo 1;
        }
        //检测请求用户是否有未分组好友分组，有将好友放在该组，没有创建‘未分组好友’
        $group2 = M('friend_group')->where(array('u_id'=>$_POST['id'],'name'=>'未分组好友'))->select();
        if(empty($group2)){
            //创建分组
            $map = [];
            $map['name'] = '未分组好友';
            $map['u_id'] = $_POST['id'];
            $new_id = M('friend_group')->add($map);
//            $new_group = M('friend_group')->where(array('u_id'=>$_POST['id'],'name'=>'未分组好友'))->select();
//            $new_id = $new_group[0]['id'];
            //往分组里添加好友
            $hehe = [];
            $hehe['fg_id'] = $new_id;
            $hehe['friend_id'] = session('id');
            M('friend')->add($hehe);
        }else{
            //往分组里添加好友
            $hehe = [];
            $hehe['fg_id'] = $group2[0]['id'];
            $hehe['friend_id'] = session('id');
            M('friend')->add($hehe);
        }
    }

    //拒绝好友申请
    public function refuse()
    {
        //修改好友请求表，agree=>1(同意) 2(拒绝)
        M('friend_request')->agree = 2;
        M('friend_request')->where(array('r_id'=>session('id'),'u_id'=>$_POST['id']))->save();
        echo 1;
    }

}