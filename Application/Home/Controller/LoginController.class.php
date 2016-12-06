<?php

    namespace Home\Controller;

    class LoginController extends BaseController
    {
        public function login($e = 0)
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
            }
            $this->assign('title','人人网-登录');
            $this->display('Login/login');
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

        //执行登录
        public function dologin()
        {
            //登录信息不能为空
            if($_POST['email'] == '' || $_POST['password'] == '' || $_POST['code'] ==''){
                $this->redirect('login\e\1');
                die;
            }

            //判断验证码
            $verify = new \Think\Verify();
            if(!$verify->check($_POST['code'])){
                $this->redirect('login\e\5');
                exit;
            }

            //检查用户
            $user = M('user');
            $data = $user->where(array('email'=>"{$_POST['email']}"))->select();
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
                        session('id',$data[0]['id']);
                        //检测好友分组，如果为空自动添加默认分组
                        $group = M('friend_group')->where(array('u_id'=>session('id')))->select();
                        if($group[0]['name'] == ''){
                            $map = [];
                            $map['name'] = '未分组好友';
                            $map['u_id'] = session('id');
                            M('friend_group')->add($map);
                        }
                        // 增加积分
                        $u = M('user')->where(['email'=>['eq',$_POST['email']]])->find();
                        $data = [];
                        $data['u_id']=$u['id'];
                        $data['integral']=5;
                        $data['way']=0;
                        $data['article_id']=0;
                        $data['com_id']=0;
                        $data['time']=time();
                        M('integral')->add($data);
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
            session('id',null); // 清空所有的session
            session('date_yzm',null);
            $this->redirect('Login/login');
        }

        public function forgetPass($e = 0)
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
            }
            $this->display('Login/forgetPass');
        }

        public function sendMail()
        {
            //登录信息不能为空
            if($_POST['email'] == '' ){
                $this->redirect('forgetPass\e\1');
                die;
            }
            //判断验证码
            $verify = new \Think\Verify();
            if(!$verify->check($_POST['code'])){
                $this->redirect('forgetPass\e\5');
                exit;
            }
            if (!(M('user')->where(['email'=>['eq',$_POST['email']]])->find())){
                $this->redirect('forgetPass\e\4');
            }
            $data = [];
            $data['email'] = $_POST['email'];
            $data['start_time'] = time();
            $data['use'] = 0;
            $data['close_time'] = time() + 7200;
            if(M('forget_pass')->where(['email'=>['eq',$_POST['email']],'use'=>['eq',0]])->find()){
                if(M('forget_pass')->where(['email'=>['eq',$_POST['email']],'use'=>['eq',0]])->save($data)){
                    $a=$this->encrypt($_POST['email'],'E','ttt');
                    if(send_mail($_POST['email'],'点击完成认证', '点击<a href="w192.168.20.144/php/TP_Social/Home/Login/resetPass/email/'.$a.'">链接</a>跳转或则直接访问192.168.20.144/php/TP_Social/Home/Login/resetPass/email/'.$a.'<br>链接两小时内有效')){
                        $this->redirect('forgetPass\e\2');
                    }else {
                        $this->redirect('forgetPass\e\3');
                    }
                }
            }else {
                if($lastId = M('forget_pass')->add($data)){
                    $a=$this->encrypt($_POST['email'],'E','ttt');
                    if(send_mail($_POST['email'],'点击完成认证', '点击<a href="192.168.20.144/php/TP_Social/Home/Login/resetPass/email/'.$a.'">链接</a>跳转或则直接访问192.168.20.144/php/TP_Social/Home/Login/resetPass/email/'.$a.'<br>链接两小时内有效')){
                        $this->redirect('forgetPass\e\2');
                    }else {
                        $this->redirect('forgetPass\e\3');
                    }
                }
            }
        }

        public function resetPass($email)
        {
            $e=$this->encrypt($email,'D','ttt');
            $fp = M('forget_pass')->where(['email'=>['eq',$e],'use'=>['eq',0]])->find();
            if ($fp) {
                if ($fp['close_time'] < time() || $fp['use'] == 1){
                    $this->error('链接无效');
                }else {
                    $this->display();
                }
            }else {
                $this->error('链接无效');
            }
        }

        public function doReset(){
            $data = [];
            $data['pwd'] = $_POST['p1'];
            $e = $this->encrypt($_POST['email'],'D','ttt');
            if(M('user')->where(['email'=>['eq',$e]])->save($data)){
                $data = [];
                $data['use'] = 1;
                M('forget_pass')->where(['email'=>['eq',$e]])->save($data);
                echo 'suc';
            }else {
                echo 'err';
            }
        }

        /*********************************************************************
        函数名称:encrypt
        函数作用:加密解密字符串
        使用方法:
        加密 :encrypt('str','E','qingdou');
        解密 :encrypt('被加密过的字符串','D','qingdou');
        参数说明:
        $string :需要加密解密的字符串
        $operation:判断是加密还是解密:E:加密 D:解密
        $key :加密的钥匙(密匙);
         *********************************************************************/
        function encrypt($string,$operation,$key='')
        {
            $src = array("/","+","=");
            $dist = array("_a","_b","_c");
            if($operation=='D'){$string = str_replace($dist,$src,$string);}
            $key=md5($key);
            $key_length=strlen($key);
            $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
            $string_length=strlen($string);
            $rndkey=$box=array();
            $result='';
            for($i=0;$i<=255;$i++)
            {
                $rndkey[$i]=ord($key[$i%$key_length]);
                $box[$i]=$i;
            }
            for($j=$i=0;$i<256;$i++)
            {
                $j=($j+$box[$i]+$rndkey[$i])%256;
                $tmp=$box[$i];
                $box[$i]=$box[$j];
                $box[$j]=$tmp;
            }
            for($a=$j=$i=0;$i<$string_length;$i++)
            {
                $a=($a+1)%256;
                $j=($j+$box[$a])%256;
                $tmp=$box[$a];
                $box[$a]=$box[$j];
                $box[$j]=$tmp;
                $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
            }
            if($operation=='D')
            {
                if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
                {
                    return substr($result,8);
                }
                else
                {
                    return'';
                }
            }
            else
            {
                $rdate = str_replace('=','',base64_encode($result));
                $rdate = str_replace($src,$dist,$rdate);
                return $rdate;
            }
        }
    }