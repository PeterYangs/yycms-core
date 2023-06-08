<?php

namespace Ycore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class NewTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'NewTheme {theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建新的主题';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $theme = $this->argument('theme');

        $dirs = themeList();

        if (in_array($theme, $dirs)) {

            $this->error("该主题已存在");

            return 0;
        }


        File::copyDirectory(dirname(__DIR__) . "/template/theme", base_path("theme/" . $theme));


        return 0;
    }
}
