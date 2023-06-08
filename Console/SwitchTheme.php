<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SwitchTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SwitchTheme {theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '切换主题';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $theme = $this->argument('theme');

        $dirs = themeList();

        if (!in_array($theme, $dirs)) {

            if (app()->runningInConsole()) {

                $this->error("该主题不存在");
            }

            return 0;
        }

        setOption("theme", $theme, true);


        Artisan::call("PushAsset " . $theme);


        return 0;
    }
}
