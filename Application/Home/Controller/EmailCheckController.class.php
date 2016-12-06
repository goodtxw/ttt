<?php
    namespace Home\Controller;

    class EmailCheckController extends BaseController
    {
        public function config()
        {
            $email_yzm = rand(1000,9999);
            if (send_mail('1411064812@qq.com', '点此激活链接', "$email_yzm")) {
                session('email_yzm',$email_yzm);
            }else {
                echo '发送失败';
            }
        }
    }