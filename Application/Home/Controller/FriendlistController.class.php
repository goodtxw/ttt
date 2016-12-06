<?php
namespace Home\Controller;

use Home\Model\FriendGroupModel;
use Home\Model\FriendModel;

class FriendlistController extends BaseController
{
    public function index()
    {
        $personal_info = M('user')->where(array('id'=>session('id')))->select();//个人头部信息
        $date = intval((time()-$personal_info[0]['create_time'])/86400);//注册天数

        //遍历所有好友
        $friend_group = new FriendGroupModel();
        $fg_list = $friend_group->relation(true)->where(['u_id'=>['eq',session('id')]])->select();
        if(count($fg_list)>0){
            foreach ($fg_list as $key=>$value){
                foreach ($value['Friend'] as $k => $v){
                    $fg_list[$key]['FriendInfo'][$k] = M('user')->where(['id'=>['eq',$v['friend_id']]])->find();
                }
            }
        }
        //统计好友数量
        $fg = M('friend_group')->where(array('u_id'=>session('id')))->select();
        foreach ($fg as $k => $v){
            $count[$k] = M('friend')->where(array('fg_id'=>$v['id']))->count();
        }
            $count2 = array_sum($count);
//            var_dump($count2);die;

//        echo "<pre>";
//        var_dump($fg_list);die;
        //消息提醒
        $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
        $this->assign('remind',$remind);
        $link = M('flink')->where(array('show'=>1))->select();
        $this->assign('link',$link);
        $this->assign('count',$count2);
        $this->assign('list',$fg_list);
        $this->assign('date',$date);
        $this->assign('info',$personal_info[0]);
        $this->display('Friendlist/index');
    }

    //访客列表
    public function visitor()
    {
        $link = M('flink')->where(array('show'=>1))->select();
        $this->assign('link',$link);
        $this->display('Friendlist/visitor');
    }

    //搜索
    public function friendSelect($fg_id = '')
    {
        $where = [];
        if(trim($fg_id) != ''){
            $where['fg_id'] = ['eq',$fg_id];
            $friend = new FriendModel();
            $list = $friend->relation('User')->where($where)->select();
            $this->assign('list',$list);
            $this->display();
        }else{
            $friend_group = new FriendGroupModel();
            $list = $friend_group->relation(true)->where(['u_id'=>['eq',session('id')]])->select();
            if(count($list)>0){
                foreach ($list as $key=>$value){
                    foreach ($value['Friend'] as $k => $v){
                        $list[$key]['FriendInfo'][$k] = M('user')->where(['id'=>['eq',$v['friend_id']]])->find();
                    }
                }
            }
            $this->assign('list',$list);
            $this->display('Friendlist/friendSelect2');
        }


//        echo "<pre>";
//        var_dump($list);die;

    }

    //修改用户组名
    public function edit()
    {
        //查重，用户组名唯一
        $group = M('friend_group')->where(array('u_id'=>session('id'),'name'=>$_POST['name']))->select();
        if(empty($group)){
            M('friend_group')->name = $_POST['name'];
            M('friend_group')->where(array('id'=>$_POST['id']))->save();
            $this->redirect('Friendlist/index');
            die;
        }else{
            echo 1;
        }
    }

    //添加用户组
    public function update()
    {
        //查重，用户组名唯一
        $group = M('friend_group')->where(array('u_id'=>session('id'),'name'=>$_POST['name']))->select();
        if(empty($group)){
            $map = [];
            $map['name'] = $_POST['name'];
            $map['u_id'] = session('id');
            if (M('friend_group')->add($map) > 0) {
                $this->redirect('Friendlist/index');
                die;
            } else {
                $this->error('添加失败....');
            }
        }else{
            echo 1;
        }
    }

    //删除分组
    public function del_group()
    {
        $data = M('friend')->where(array('fg_id'=>$_POST['id']))->select();
        if(empty($data)){
            M('friend_group')->where(array('id'=>$_POST['id']))->delete();
            echo 1;
        }else{
            echo 2;
        }
    }

    //删除好友
    public function del_friend()
    {
        $data = M('friend_group')->where(array('u_id'=>session('id')))->select();
        foreach ($data as $k => $v){
            M('friend')->where(array('fg_id'=>$v['id'],'friend_id'=>$_POST['id']))->delete();
            }
        echo 1;
    }

    //移动分组
    public function change_group()
    {
        //所在之前组的好友信息
        $friend = M('friend')->field('f.*')->table('rr_friend f,rr_friend_group g')->where('f.fg_id=g.id')->where(array('g.u_id'=>session('id'),'f.friend_id'=>$_POST['id']))->select();

        $group = M('friend_group')->where(array('u_id'=>session('id'),'name'=>$_POST['group']))->select();
        //现在的分组id
        $new_group_id = $group[0]['id'];
        M('friend')->fg_id = $new_group_id;
        M('friend')->where(array('id'=>$friend[0]['id']))->save();
        echo 1;
//        echo "<pre>";
//        echo M('friend_group')->getLastSql().'<br>';
//        var_dump($new_group_id);
    }
}
