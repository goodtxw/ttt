<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends CommonController
{
    public function login($e = 0)
    {
        if ($e == 1){
            $this->assign('error',1);
        }elseif ($e == 2){
            $this->assign('error',2);
        }elseif ($e == 3){
            $this->assign('error',3);
        }elseif ($e == 4){
            $this->assign('error',4);
        }
        $this->display('Login/login');
    }

    //生成验证码
    public function yzm()
    {
        $Verify = new \Think\Verify();
        $Verify->fontSize = 25;
        $Verify->length = 4;
        $Verify->codeSet = '0123456789';
        $Verify->entry();
    }

    //执行登录
    public function dologin()
    {
        //判断验证码
        $verify = new \Think\Verify();
        if(!$verify->check($_POST['code'])){
            $this->redirect('login\e\1');
            exit;
        }
        //检查用户
        $admin = M('admin');
        $data = $admin->where(array('name'=>"{$_POST['name']}"))->select();
        if(empty($data)){
            $this->redirect('login\e\2');
            die;
        }else{
            //判断用户状态(是否被封禁)
            if($data[0]['status'] == 1){
                $this->redirect('login\e\3');
                die;
            }else{
                //检测密码
                if($data[0]['pwd'] == $_POST['password']){
                    session('admin_id',$data[0]['id']);
                    $this->redirect('Index/index');
                    die;
                }else{
                    $this->redirect('login\e\4');
                }
            }
        }

    }

    //退出登录
    public function logout()
    {
        session('admin_id',null); // 清空所有的session
        $this->redirect('Login/login');
    }

}