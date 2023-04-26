<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
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
