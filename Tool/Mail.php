<?php

namespace Ycore\Tool;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{


    public static function send(array $emails, string $title, string $content, string $attachData = '', string $attachFilename = '', string $attachFilePath = '')
    {


        if (!(getOption("mail_host") && getOption('mail_username') && getOption('mail_password') && getOption('mail_port'))) {


            throw new \Exception("邮箱配置缺失!");

        }


        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = getOption("mail_host");
        $mail->SMTPAuth = true;
        $mail->Username = getOption('mail_username');
        $mail->Password = getOption('mail_password');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = getOption('mail_port');

        //Recipients
        $mail->setFrom(getOption('mail_username'), getOption('mail_username'));

        foreach ($emails as $email) {

            $mail->addAddress($email, $email);     //Add a recipient
        }

        $mail->isHTML();

        $mail->Subject = $title;

        $mail->Body = $content;


        if ($attachData && $attachFilename) {


            $mail->addStringAttachment($attachData, $attachFilename);

        } else {


            if ($attachFilePath) {


                $mail->addAttachment($attachFilePath);
            }

        }

        $mail->send();


    }


}
