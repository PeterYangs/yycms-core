<?php

namespace Ycore\Console;

use Illuminate\Console\Command;

use Ycore\Models\Article;
use Ycore\Models\ArticleAssociationObject;
use Ycore\Models\ArticleDownload;
use Ycore\Models\ArticleTag;
use Ycore\Models\ExpandData;
use Ycore\Models\UserAccess;
use Ycore\Models\WebsitePush;

class ResetDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ResetDatabase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重置数据库';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $this->error("危险操作！请确认三次！！！");

        for ($i = 0; $i < 3; $i++) {

            $ask = $this->ask('确定清理所有数据吗(' . (3 - $i) . ')？(yes/no)', 'no');

            if (strtolower($ask) !== 'yes') {

                return 0;
            }

        }


        Article::truncate();

        ArticleAssociationObject::truncate();

        ArticleTag::truncate();

        ExpandData::truncate();

        UserAccess::truncate();

        WebsitePush::truncate();

        ArticleDownload::truncate();


        $this->info("清理完毕");

        return 0;
    }
}
