<?php

namespace Home\Controller;

class RegisterController extends BaseController
{
    public function register($e = 0)
    {
        if($e == 1){
            $this->assign('error',1);
        }elseif ($e == 2){
            $this->assign('error',2);
        }elseif ($e == 3){
            $this->assign('error',3);
        }elseif ($e == 4){
            $this->assign('error',4);
        }elseif ($e == 5){
            $this->assign('error',5);
        }elseif ($e == 6){
            $this->assign('error',6);
        }elseif ($e == 7){
            $this->assign('error',7);
        }
        $this->assign('title','人人网-注册');
        $this->display('Register/register');
    }

    //生成验证码
    public function yzm()
    {
        $Verify = new \Think\Verify();
        $Verify->fontSize = 40;
        $Verify->length = 4;
        $Verify->codeSet = '0123456789';
        $Verify->entry();
    }

    //执行注册
    public function doregister()
    {
        //注册信息不能为空
        if($_POST['email'] == '' || $_POST['password'] == '' || $_POST['code'] ==''){
            $this->redirect('register\e\1');
            die;
        }

        //判断验证码
        if($_POST['code'] != session('email_yzm')){
            $this->redirect('register\e\5');
            die;
        }

        //验证邮箱格式
        if(!preg_match('"^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\.\\w+([-.]\\w+)*$"',$_POST['email'])){
            $this->redirect('register\e\4');
            die;
        }

        //检查邮箱是否已经存在
        $user = M('user');
        $data = $user->where(array('email'=>$_POST['email']))->select();
        if(!empty($data)){
            $this->redirect('register\e\2');
            die;
        }

        //正则匹配密码
        if(!preg_match('/^[a-zA-Z0-9]{6,10}$/',$_POST['password'])){
            $this->redirect('register\e\3');
            die;
        }

        //写入数据库
        $map = [];
        $map['pwd'] = $_POST['password'];
        $map['name'] = date('YmdHis',time());//用户名不能为空(由日期自动生成)
        $map['email'] = $_POST['email'];
        $map['status'] = 0;//状态(默认启用)
        $map['create_time'] = time();
        $map['privacy'] = 1;//用户前台信息状态(默认陌生人不可见)
        $map['head_image'] = 'moren.jpg';
        //过滤数据,数据验证
        if (!$user->create($map)) {
            //如果创建数据失败,表示验证没有通过
            //输出错误信息 并且跳转
            $this->error($user->getError());
        } else {
            //验证通过 执行添加操作
            //执行添加
            if ($user->add($map) > 0 ) {
                $this->redirect('register\e\7');
            } else {
                $this->redirect('register\e\6');
            }
        }
    }
}