<?php

namespace Ycore\Console;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;
use Ycore\Models\Category;
use Illuminate\Console\Command;


class CreateDeathLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateDeathLink';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成死链链接';


    /**
     * @var int
     */
    protected int $index = 0;

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {

        @unlink(storage_path('app/public/www-death.txt.temp'));
        @unlink(storage_path('app/public/m-death.txt.temp'));

        $wwwFile = fopen(storage_path('app/public/www-death.txt.temp'), 'wb');
        $mFile = fopen(storage_path('app/public/m-death.txt.temp'), 'wb');

        if (!$wwwFile || !$mFile) {

            Log::error('死链文件打开失败');

            return 0;
        }


        Article::withoutGlobalScopes()->with('category')->whereNotNull('deleted_at')->chunkById(100, function ($items) use ($wwwFile, $mFile) {


            foreach ($items as $item) {

                $pcUrl = getDetailUrlForCli($item);

                fwrite($wwwFile, $pcUrl . "\n");


                $mobileUrl = getDetailUrlForCli($item, "mobile");

                fwrite($mFile, $mobileUrl . "\n");


            }

        });

        fclose($wwwFile);
        fclose($mFile);

        rename(storage_path('app/public/www-death.txt.temp'),storage_path('app/public/www-death.txt'));
        rename(storage_path('app/public/m-death.txt.temp'),storage_path('app/public/m-death.txt'));


        return 0;
    }


}
