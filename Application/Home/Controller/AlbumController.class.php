<?php
namespace Home\Controller;
use Home\Model\AlbumModel;

class AlbumController extends BaseController
{
    public function index()
    {
        //个人的基本信息
        $personal_info = M('user')->where(array('id'=>session('id')))->select();
        //用户注册天数 intval((当前时间-注册时间)/一天的秒数) 一天86400秒
        $date = intval((time()-$personal_info[0]['create_time'])/86400);
        //用户的相册信息
        $album = new AlbumModel();
        $image =$album->relation(true)->where(array('u_id'=>session('id')))->select();
//        echo "<pre>";
//        var_dump($image);die;
        //消息提醒
        $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
        $this->assign('remind',$remind);
        $link = M('flink')->where(array('show'=>1))->select();
        $this->assign('link',$link);
        $this->assign('date',$date);
        $this->assign('info',$personal_info[0]);
        $this->assign('image',$image);
        $this->display('Album/index');
    }

    //相册详情
    public function detail()
    {
        //个人的基本信息
        $personal_info = M('user')->where(array('id'=>session('id')))->select();
        //用户注册天数 intval((当前时间-注册时间)/一天的秒数) 一天86400秒
        $date = intval((time()-$personal_info[0]['create_time'])/86400);
        //照片信息
        $image = M('image')->where(array('album_id'=>$_GET['id']))->select();
        $album = M('album')->where(array('id'=>$_GET['id']))->select();
        //消息提醒
        $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
        $this->assign('remind',$remind);
        $link = M('flink')->where(array('show'=>1))->select();
        $this->assign('link',$link);
        $this->assign('date',$date);
        $this->assign('image',$image);
        $this->assign('album',$album[0]);
        $this->assign('info',$personal_info[0]);
        $this->display('Album/detail');
    }

    //修改相册名
    public function edit()
    {
        //查重，相册名唯一
        $album = M('album')->where(array('u_id'=>session('id'),'name'=>$_POST['name']))->select();
        if(empty($album)){
            M('album')->name = $_POST['name'];
            M('album')->where(array('id'=>$_POST['id']))->save();
            $this->redirect('Album/detail');
        }else{
            echo 1;
        }
    }

    //添加相册
    public function update()
    {
        //查重，相册名唯一
        $album = M('album')->where(array('u_id'=>session('id'),'name'=>$_POST['name']))->select();
        if(empty($album)){
            $map = [];
            $map['name'] = $_POST['name'];
            $map['time'] = time();
            $map['u_id'] = session('id');
            if (M('album')->add($map) > 0) {
                $this->redirect('Album/index');
            } else {
                $this->error('添加失败....');
            }
        }else{
            echo 1;
        }
    }

    //删除图片
    public function del_image()
    {
        if(M('image')->where(array('id'=>$_POST['id']))->delete()>0){
            $this->redirect('Album/detail');
        }else{
            $this->error('删除失败....');
        }
    }

    //删除相册
    public function del_album()
    {
        M('image')->where(array('album_id'=>$_POST['id']))->delete();
        M('album')->where(array('id'=>$_POST['id']))->delete();
        $this->redirect('Album/index');
    }
}