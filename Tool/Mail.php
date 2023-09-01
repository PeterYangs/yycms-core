<?php

namespace Ycore\Tool;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{


    public static function send(array $emails, string $title, string $content, string $attachData = '', string $attachFilePath = '')
    {

//        if ()

        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = getOption("mail_host");
        $mail->SMTPAuth = true;
        $mail->Username = getOption('mail_username');
        $mail->Password = getOption('mail_password');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = getOption('mail_port');

        //Recipients
        $mail->setFrom('904801074@qq.com', '发送者');
        $mail->addAddress('1259343832@qq.com', '接受者');     //Add a recipient


    }

//
//    protected array $emails;
//
//    protected string $title;
//
//    protected string $content;
//
//    //附件（数据）
//    protected string $attachData;
//
//    //附件名
//    protected string $attachFilepath;
//
//
//    protected PHPMailer $mailer;


}
