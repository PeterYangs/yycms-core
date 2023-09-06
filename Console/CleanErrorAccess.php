<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CleanErrorAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CleanErrorAccess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除15天前404日志';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $count = \DB::table('error_access')->whereDate("created_at", "<", now()->subDays(15))->delete();


        echo "已删除:" . $count . PHP_EOL;

        return 0;
    }
}
