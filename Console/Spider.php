<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Tool\Cmd;

class Spider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Spider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬取文章到采集表';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $out = Cmd::commandline(Cmd::getCommandlineByName('goScript') . " spider", 60 * 5);


        if (app()->runningInConsole()) {

            $this->info($out);
        }

        return 0;
    }
}
