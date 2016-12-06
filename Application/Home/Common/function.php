<?php
    function send_mail($to, $title, $content)
    {
        vendor('PHPMailer.class#phpmailer');
        vendor('PHPMailer.class#smtp');

        $mail = new \PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;

        $mail->Username = C('EMAIL');
        $mail->Password = C('EMAIL_PASS');
        $mail->SMTPSecure = 'tls';
        $mail->Host = C('EMAIL_HOST');
        $mail->Port = C('EMAIL_HOST_PORT');

        $mail->setFrom(C('EMAIL'), C('EMAIL_NAME'));
        $mail->addAddress($to);

        $mail->isHTML(C('IS_HTML'));

        $mail->Subject = $title;
        $mail->Body    = $content;

        if(!$mail->send()) {
            return 0;
        } else {
            return 1;
        }
    }