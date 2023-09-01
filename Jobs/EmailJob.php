<?php

namespace Ycore\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected array $emails;

    protected string $title;


    protected string $content;

    //附件名
    protected string $attachFilename;

    //附件（数据）
    protected string $attachData;

    /**
     * @param array $emails
     * @param string $title
     * @param string $content
     * @param string $attachFilename
     * @param string $attachData
     */
    public function __construct(
        array  $emails,
        string $title,
        string $content,
        string $attachFilename = '',
        string $attachData = ''
    )
    {
        $this->emails = $emails;
        $this->title = $title;
        $this->content = $content;
        $this->attachFilename = $attachFilename;
        $this->attachData = $attachData;
    }


    /**
     * 消息队列中间件
     * Create by Peter Yang
     * 2022-08-22 20:26:51
     * @return array
     */
    public function middleware()
    {


        return [(new RateLimited('email'))->dontRelease()];
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

//
//        \Mail::html($this->content, function (\Illuminate\Mail\Message $message) {
//
//
//            foreach ($this->emails as $v) {
//
//                $message->to($v);
//
//            }
//
//            $message->subject($this->title);
//
//            //发送附件内容
//            if ($this->attachFilename) {
//
//                $message->attachData($this->attachData, $this->attachFilename);
//            }
//
//        });


        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = "smtp.qq.com";
        $mail->SMTPAuth = true;
        $mail->Username = "904801074@qq.com";
        $mail->Password = 'fangdlbaosembfif';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = '465';

        //Recipients
        $mail->setFrom('904801074@qq.com', '发送者');
        $mail->addAddress('1259343832@qq.com', '接受者');     //Add a recipient


        $mail->addStringAttachment(file_get_contents(base_path('123.xml')), "123.xml");

        $mail->isHTML(true);


        $mail->Subject = 'title';

        $mail->Body = '<h1>content</h1>';

        $mail->send();


    }
}
