<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use RuntimeException;

class SetAppEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetAppEnv {env}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '设置APP_ENV';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $env = (string)$this->argument('env');
        $envFile = base_path('.env');

        try {
            $this->updateEnvFile($envFile, $env);
        } catch (InvalidArgumentException|RuntimeException $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $this->call('config:clear');
        $this->info('APP_ENV=' . $env);

        return 0;
    }

    public function updateEnvFile(string $envFile, string $env): void
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $env)) {
            throw new InvalidArgumentException('APP_ENV只允许字母、数字、下划线和中划线');
        }

        if (!file_exists($envFile)) {
            throw new RuntimeException('.env文件不存在');
        }

        if (!is_readable($envFile)) {
            throw new RuntimeException('.env文件不可读');
        }

        if (!is_writable($envFile)) {
            throw new RuntimeException('.env文件无写入权限');
        }

        $content = file_get_contents($envFile);

        if ($content === false) {
            throw new RuntimeException('.env文件读取失败');
        }

        if (preg_match('/^APP_ENV=.*$/m', $content)) {
            $content = preg_replace('/^APP_ENV=.*$/m', 'APP_ENV=' . $env, $content, 1);
        } else {
            $content = rtrim($content, "\r\n") . PHP_EOL . 'APP_ENV=' . $env . PHP_EOL;
        }

        if (file_put_contents($envFile, $content) === false) {
            throw new RuntimeException('.env文件写入失败');
        }
    }
}
