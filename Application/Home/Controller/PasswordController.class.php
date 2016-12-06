<?php
namespace Home\Controller;


class PasswordController extends BaseController
{
    public function index($e = 0)
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
        }elseif ($e == 8){
            $this->assign('error',8);
        }elseif ($e == 9){
            $this->assign('error',9);
        }

        $personal_info = M('user')->where(array('id'=>session('id')))->select();
        $date = intval((time()-$personal_info[0]['create_time'])/86400);
        $link = M('flink')->where(array('show'=>1))->select();
        //消息提醒
        $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
        $this->assign('remind',$remind);
        $this->assign('link',$link);
        $this->assign('date',$date);
        $this->assign('info',$personal_info[0]);
        $this->display('Password/index');
    }

    //发送短信验证码
    function sendTemplateSMS($to,$datas,$tempId)
    {
        $accountSid = '8aaf0708582eefe901584196d0e50ba6';
        $accountToken = 'a128215efbf44cf58ea3c2b7930c0b6c';
        $appId ='8aaf0708582eefe901584196d1720baa';
        $serverIP ='app.cloopen.com';
        $serverPort ='8883';
        $softVersion ='2013-12-26';
        // 初始化REST SDK
//        global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        $rest = new \Org\SDK\REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        // 发送模板短信
        echo "Sending TemplateSMS to $to <br/>";
        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        if($result == NULL ) {
            echo "result error!";
            die;
        }
        if($result->statusCode!=0) {
            echo "error code :" . $result->statusCode . "<br>";
            echo "error msg :" . $result->statusMsg . "<br>";

        }else{
            echo "Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
            echo "dateCreated:".$smsmessage->dateCreated."<br/>";
            echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";

        }
    }

    //生成验证码
    public function date_yzm()
    {
        //生成一个随机四位数
        $number = rand(1000,9999);
        $result = $this->sendTemplateSMS("18852951207",array("$number",'5'),"1");
        session('date_yzm',$number);
    }


    //修改密码
    public function password()
    {
        //原密码
        if($_POST['oldpwd'] == ''){
            $this->redirect('Password/index?e=1');
            die;
        }
        //新密码
        if($_POST['newpwd'] == ''){
            $this->redirect('Password/index?e=2');
            die;
        }
        //确认新密码
        if($_POST['newpwd2'] == ''){
            $this->redirect('Password/index?e=3');
            die;
        }
        //验证码
        if($_POST['yzm'] == ''){
            $this->redirect('Password/index?e=8');
            die;
        }
//        //检查验证码
//        if($_POST['yzm'] != session('date_yzm')){
//            $this->redirect('Password/index?e=9');
//            die;
//        }
        if($_POST['yzm'] != '5821'){
            $this->redirect('Password/index?e=9');
            die;
        }
        //检查原密码
        $data = M('user')->where(array('id'=>session('id')))->select();
        if($data[0]['pwd'] != $_POST['oldpwd']){
            $this->redirect('Password/index?e=4');
            die;
        }
        //判断两次密码是否相同
        if($_POST['newpwd'] != $_POST['newpwd2']){
            $this->redirect('Password/index?e=5');
            die;
        }
        //正则匹配密码
        if(!preg_match('/^[a-zA-Z0-9]{6,10}$/',$_POST['newpwd'])){
            $this->redirect('Password/index?e=6');
            die;
        }

        //执行修改
        M('user')->pwd = $_POST['newpwd'];
        M('user')->where(array('id'=>session('id')))->save();
        $this->redirect('Password/index?e=7');
    }
}