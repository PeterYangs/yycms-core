<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Tool\Cmd;

class StaticTool extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StaticTool';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '网站静态化';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $out = Cmd::commandline(Cmd::getCommandlineByName('goScript') . " staticTool" , 10,true);


        if (app()->runningInConsole()) {


            $this->info($out);
        }


        return 0;
    }
}
