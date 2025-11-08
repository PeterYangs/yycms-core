<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use OSS\Core\OssException;
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

        \DB::statement("TRUNCATE TABLE failed_jobs");

        $backupDir = storage_path("app/public");

        $filename = date("Y_m_d_H_i_s");
        $sqlFile = $backupDir . "/{$filename}.sql";
        $envFile = base_path('.env'); // 添加 .env
        $tarFile = $backupDir . "/{$filename}.tar.gz";

        // Step 1：导出 SQL
        $dumpCmd = sprintf(
            'mysqldump -u%s -p%s --host=%s --port=%d --default-character-set=utf8mb4 --single-transaction --quick --routines --events --triggers %s > %s',
            env("DB_USERNAME"),
            env("DB_PASSWORD"),
            env("DB_HOST", '127.0.0.1'),
            env("DB_PORT", 3306),
            env("DB_DATABASE"),
            $sqlFile
        );

        // Step 2：打包 SQL 和 .env（无需复制 env，直接打包源路径）
        $tarCmd = sprintf(
            'tar -czf %s -C %s %s -C %s %s',
            $tarFile,
            $backupDir, basename($sqlFile),
            base_path(), '.env'
        );


        // Step 3：组合命令并执行
        $fullCmd = $dumpCmd . ' && ' . $tarCmd;

        $process = Process::fromShellCommandline($fullCmd);
        $process->setTimeout(60 * 15);
        $process->setWorkingDirectory(base_path());

        $this->info("开始执行备份...");
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        // 上传 OSS
        $accessKeyId = env('ALI_KEY');
        $accessKeySecret = env("ALI_SECRET");
        $endpoint = env("ALI_ENDPOINT");
        $bucket = env("ALI_BUCKET_NAME");
        $object = 'backup/' . str_replace("-", "_", env("APP_NAME")) . "/" . basename($tarFile);

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, true);
            $uploadId = $ossClient->initiateMultipartUpload($bucket, $object);

            $partSize = 5 * 1024 * 1024;
            $fileSize = filesize($tarFile);
            $pieces = $ossClient->generateMultiuploadParts($fileSize, $partSize);
            $uploadParts = [];

            $uploadFile = fopen($tarFile, 'rb');
            foreach ($pieces as $i => $piece) {
                $fromPos = $piece[$ossClient::OSS_SEEK_TO];
                $toPos = $piece[$ossClient::OSS_LENGTH];
                fseek($uploadFile, $fromPos);
                $upOptions = [
                    OssClient::OSS_FILE_UPLOAD => $tarFile,
                    OssClient::OSS_PART_NUM => ($i + 1),
                    OssClient::OSS_SEEK_TO => $fromPos,
                    OssClient::OSS_LENGTH => $toPos,
                ];
                $eTag = $ossClient->uploadPart($bucket, $object, $uploadId, $upOptions);
                $uploadParts[] = [
                    'PartNumber' => ($i + 1),
                    'ETag' => $eTag,
                ];
            }
            fclose($uploadFile);

            $ossClient->completeMultipartUpload($bucket, $object, $uploadId, $uploadParts);
            $this->info("✅ 上传成功：" . $object);
        } catch (OssException $e) {
            $this->error("❌ 上传失败：" . $e->getMessage());
        }

        // 无论成功或失败都删除本地 .sql
        if (file_exists($sqlFile)) {
            unlink($sqlFile);
            unlink($tarFile);
        }


        return 0;
    }
}
