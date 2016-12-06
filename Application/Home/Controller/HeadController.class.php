<?php
namespace Home\Controller;

class HeadController extends BaseController
{
    public function index($s = 0)
    {
        if($s == 1){
            $this->assign('success',1);
        }
        //个人的基本信息
        $personal_info = M('user')->where(array('id'=>session('id')))->select();
        //用户注册天数 intval((当前时间-注册时间)/一天的秒数) 一天86400秒
        $date = intval((time()-$personal_info[0]['create_time'])/86400);
        //消息提醒
        $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
        $this->assign('remind',$remind);
        $link = M('flink')->where(array('show'=>1))->select();
        $this->assign('link',$link);
        $this->assign('date',$date);
        $this->assign('info',$personal_info[0]);
        $this->display('Head/index');
    }

    //上传头像
    public function file_head_image()
    {
//        echo "<pre>";
//        var_dump($_POST);die;
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/upload/'; // 设置附件上传根目录
        $upload->saveName = array('uniqid','');//图片名称,采用uniqid函数生成一个唯一的字符串序列
        $upload->autoSub  = false;//自动使用子目录保存上传文件 默认为true

        // 上传文件
        $info = $upload->upload();
        // 上传错误提示错误信息
        if(!$info) {
            $this->error($upload->getError());
            die;
        }
        M('user')->head_image = $info['head_image']['savename'];
        M('user')->where(array('id'=>session('id')))->save();
        $this->redirect('Head/index?s=1');
    }
}