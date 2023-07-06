<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Tool\Cmd;

class BatchPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BatchPush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量推送链接';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        Cmd::commandline(Cmd::getCommandlineByName("goScript") . " baiduPush --path " . storage_path('link'), 60 * 60 * 24, true);


        return 0;
    }
}
