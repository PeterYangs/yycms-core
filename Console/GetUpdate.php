<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class GetUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GetUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取更新';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $this->call('GetLibrary');

        $this->call('GetAdminStatic');


        return 0;
    }
}
