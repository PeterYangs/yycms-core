<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Process\Process;

class Init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '网站搭建初始化脚本';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        try {

            DB::select('show tables');


        } catch (\Exception $exception) {


            $this->error("检测到数据库连接出错，请检查数据库配置" . PHP_EOL . "error:" . $exception->getMessage());

            return 0;
        }


        if (env('CACHE_DRIVER') !== "redis") {


            $this->error("请将缓存驱动改为redis(项目目录下的.env文件中，改CACHE_DRIVER=redis)");

            return 0;
        }


        try {

            Redis::command('ping');

        } catch (\Exception $exception) {


            $this->error("检测到redis连接出错，请检查redis配置" . PHP_EOL . "error:" . $exception->getMessage());


            return 0;

        }

        //发布资源
        $this->call("vendor:publish", ['--provider' => "Ycore\YyCmsServiceProvider"]);


        if (!\Schema::hasTable('migrations')) {


            $this->info("检测到数据库为空，正在进行数据库初始化。。。");

            $this->call("migrate");

            $this->info("数据库初始化成功！");

            //生成拓展表
            $this->call("CreateExpandTable");

            $this->info("生成拓展表成功！");


            $is_test = $this->ask("需要生成测试数据吗？(y/n)", "y");

            if (strtolower($is_test) === "y") {


                $this->call("create:article", ['num' => 30]);

            }

        }


        while (true) {


            $domain = $this->ask("请输入电脑端域名(请带上网络协议)");


            $pc_parse = parse_url($domain);

            if (!isset($pc_parse['scheme'])) {


                $this->error("请带上网络协议！");

                continue;
            }

            setOption("domain", $domain, true);

            break;
        }


        while (true) {


            $m_domain = $this->ask("请输入移动端域名(请带上网络协议)");


            $m_parse = parse_url($m_domain);

            if (!isset($m_parse['scheme'])) {


                $this->error("请带上网络协议！");

                continue;
            }

            setOption("m_domain", $m_domain, true);

            break;
        }


        $this->info("正在生成路由文件。。。");

        $this->call("CreateRoute");

        $this->info("路由文件生成成功！");


        $bar = $this->output->createProgressBar(5);

        $bar->start();

        for ($i = 0; $i < 5; $i++) {

            $bar->advance();

            sleep(1);

        }

        $bar->finish();

        echo PHP_EOL . PHP_EOL;

        $this->info("设置成功！");


        return 0;
    }
}
