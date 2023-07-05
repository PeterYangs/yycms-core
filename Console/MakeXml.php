<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Ycore\Tool\Cmd;

class MakeXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MakeXml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成网站地图';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $out = Cmd::commandline(Cmd::getCommandlineByName('goScript') . " makeXml", 90);


        if (app()->runningInConsole()) {


            $this->info($out);
        }


        return 0;
    }
}
