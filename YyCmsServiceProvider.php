<?php

namespace Ycore;

use Ycore\Console\AutoAssociationObject;
use Ycore\Models\Options;
use Ycore\Service\Search\Search;
use Ycore\Service\Search\SearchInterface;
use Ycore\Service\Upload\AliUpload;
use Ycore\Service\Upload\LocalUpload;
use Ycore\Service\Upload\Upload;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Ycore\Console\AutoPush;
use Ycore\Console\BatchPush;
use Ycore\Console\ChangePushTime;
use Ycore\Console\CleanCache;
use Ycore\Console\CleanUserAccess;
use Ycore\Console\CreateAdmin;
use Ycore\Console\CreateArticle;
use Ycore\Console\CreateChannelRoute;
use Ycore\Console\CreateGitHookScript;
use Ycore\Console\FindTag;
use Ycore\Console\GetAccess;
use Ycore\Console\GitCheckoutHook;
use Ycore\Console\HomeStatic;
use Ycore\Console\Init;
use Ycore\Console\MakeAllLink;
use Ycore\Console\MakeXml;
use Ycore\Console\MatchApk;
use Ycore\Console\MysqlBackup;
use Ycore\Console\PushCustom;
use Ycore\Console\PushRandomStoreToArticle;
use Ycore\Console\SearchAccess;
use Ycore\Console\SetSeoTitle;
use Ycore\Console\Spider;
use Ycore\Console\SpiderTable;
use Ycore\Console\StaticTool;
use Ycore\Console\SyncExpand;
use Ycore\Console\Test;
use Ycore\Console\Test2;
use Closure;
use Ycore\Console\TimingArticlePush;
use Ycore\Tool\AcademyPaginator;

class YyCmsServiceProvider extends ServiceProvider
{


    public function boot()
    {


        $this->publishes([
            __DIR__ . '/config/yycms.php' => config_path('yycms.php'),
            __DIR__ . "/database/schema/mysql-schema.dump" => database_path('schema/mysql-schema.dump')
        ]);


        $this->loadMigrationsFrom(__DIR__ . "/database/migrations");


        $this->bootCommands();


        AcademyPaginator::injectIntoBuilder();


        try {

            $items = Options::where('autoload', 1)->get();


            //自动加载的配置写入全局变量
            foreach ($items as $item) {

                $type = $item->type;

                $value = $item->value;

                if ($type === "array") {

                    $value = json_decode($item->value, true, 512, JSON_THROW_ON_ERROR);
                }


                app()->instance("option_" . $item->key, $value);

            }

            \View::share('seoTitle', getOption('seo_title'));
            \View::share('seoKeyword', getOption('seo_keyword'));
            \View::share('seoDesc', getOption('seo_desc'));
            \View::share('siteName', getOption('site_name'));
            \View::share('icp', getOption('icp'));
            \View::share('domain', getOption('domain'));
            \View::share('mDomain', getOption('m_domain'));
            \View::share('isBeian', getOption('is_beian'));


        } catch (\Exception $exception) {

        }


        //邮件发送流量流量限制，1分钟只能发5个
        RateLimiter::for('email', function ($job) {


            return Limit::perMinute(5);

        });


    }


    public function register()
    {


//        $this->mergeConfigFrom(__DIR__."/config/yycms.php",'yycms');


//        $this->loadMigrationsFrom(__DIR__."/database/schema");

        $this->app->bind(SearchInterface::class, function ($app) {


            return new Search(config('search.allowModelList'));

        });


        $this->app->bind(Upload::class, function ($app) {


            switch (env('UPLOAD_TYPE', "local")) {

                case "ali_oss":

                    return new AliUpload();

                default:

                    return new LocalUpload();

            }

        });


    }


    private function bootCommands()
    {


        if ($this->app->runningInConsole()) {

            $this->commands([
                Test2::class,
                AutoPush::class,
                BatchPush::class,
                ChangePushTime::class,
                CleanCache::class,
                CleanUserAccess::class,
                CreateAdmin::class,
                CreateArticle::class,
                CreateChannelRoute::class,
                CreateGitHookScript::class,
                FindTag::class,
                GetAccess::class,
                GitCheckoutHook::class,
                HomeStatic::class,
                MakeAllLink::class,
                MakeXml::class,
                MatchApk::class,
                MysqlBackup::class,
                PushCustom::class,
                PushRandomStoreToArticle::class,
                SearchAccess::class,
                SetSeoTitle::class,
                Spider::class,
                SpiderTable::class,
                StaticTool::class,
                SyncExpand::class,
                Test::class,
                TimingArticlePush::class,
                Init::class,
                AutoAssociationObject::class

            ]);

        }

    }


}
