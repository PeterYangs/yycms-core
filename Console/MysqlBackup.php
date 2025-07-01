<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use OSS\OssClient;
use Symfony\Component\Process\Process;

class MysqlBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MysqlBackup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '数据库备份';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $path = storage_path('backup') . "/";

        $filename = env('APP_NAME') . "_" . date('Y-m-d-H-i-s') . ".sql";


        $allPath = $path . $filename;


        $process = Process::fromShellCommandline('mysqldump -u' . env('DB_USERNAME') . ' -p' . env('DB_PASSWORD') . ' ' . env('DB_DATABASE') . ' > ' . $allPath);


        $process->run(function ($type, $buffer) {


            echo $buffer;

        });


        if (env('UPLOAD_TYPE') === "ali_oss") {

            $options = array(
                OssClient::OSS_CHECK_MD5 => true,
                OssClient::OSS_PART_SIZE => 1024 * 1024,
            );

            $accessKeyId = env('ALI_KEY', "");
            $accessKeySecret = env("ALI_SECRET", "");
            $endpoint = env("ALI_ENDPOINT", "");


            // true为开启CNAME。CNAME是指将自定义域名绑定到存储空间上。
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, true);


            $ossClient->multiuploadFile(env("ALI_BUCKET_NAME", ""), "backup/" . $filename,
                $allPath, $options);


        }


        $list = glob(storage_path('backup/*.sql'));

        if (count($list) <= 3) {


            echo "文件数小于3";

            return 0;
        }


        $index = 0;

        foreach ($list as $item) {


            if ($index >= (count($list) - 3)) {


                break;
            }

            echo $item . PHP_EOL;

            unlink($item);


            $index++;
        }


        return 0;
    }
}
