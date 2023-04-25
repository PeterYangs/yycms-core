<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CleanUserAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CleanUserAccess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除4天前日志';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $count = \DB::table('user_access')->whereDate("created_at", "<", now()->subDays(3))->delete();


        echo "已删除:" . $count . PHP_EOL;

        return 0;
    }
}
