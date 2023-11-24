<?php

namespace Ycore;


use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\View\Engines\EngineResolver;
use Ycore\Console\AndroidUrlToDownload;
use Ycore\Console\AutoAssociationObject;
use Ycore\Console\BatchImportArticleWithZip;
use Ycore\Console\CleanStaticPage;
use Ycore\Console\CreateExpandTable;
use Ycore\Console\CreateRoute;
use Ycore\Console\GetAdminStatic;
use Ycore\Console\GetGoScript;
use Ycore\Console\GetLibrary;
use Ycore\Console\GetUpdate;
use Ycore\Console\NewTheme;
use Ycore\Console\PushAsset;
use Ycore\Console\ResetDatabase;
use Ycore\Console\SetExpandData;
use Ycore\Console\SetExpandDataBatch;
use Ycore\Console\SwitchTheme;
use Ycore\Models\Options;
use Ycore\Service\Ai\ChatGpt;
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
use Ycore\Service\Ai\Ai;
use Ycore\View\Common;

class YyCmsServiceProvider extends ServiceProvider
{


    public function boot()
    {

        //加载公共模板
        \View::addLocation(__DIR__ . "/Http/View");

        $this->publishes([
            __DIR__ . '/config/menu.php' => config_path('menu.php'),
            __DIR__ . '/config/yycms.php' => config_path('yycms.php'),
            __DIR__ . "/database/schema/mysql-schema.dump" => database_path('schema/mysql-schema.dump')
        ], 'yycms');


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

                if ($item->type === 'int') {

                    $value = (int)$value;
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


            loadTheme();


            if (!app()->runningInConsole()) {

                //移动端
                if (request()->host() === parse_url(getOption('m_domain'))['host']) {

                    //加载移动端视图路径
                    \View::addLocation(base_path('theme/' . getOption('theme', 'demo') . '/mobile/view'));

                } else {

                    \View::addLocation(base_path('theme/' . getOption('theme', 'demo') . '/pc/view'));

                }

                //加载自定义分页样式
                if (\View::exists('paginator')) {

                    Paginator::defaultView('paginator');


                }


            }


        } catch (\Exception $exception) {

        }


        //邮件发送流量流量限制，1分钟只能发5个(消息队列限流)
        RateLimiter::for('email', function ($job) {


            return Limit::perMinute(5);

        });


    }


    public function register()
    {


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


        $this->app->bind(Ai::class, function ($app) {


            switch (env("AI_TYPE", 'chat_gpt')) {


                default:

                    return new ChatGpt();

            }


        });


        $this->app->extend('view.engine.resolver', function (EngineResolver $resolver, Application $application): EngineResolver {

            return new class ($resolver) extends EngineResolver {

                public function __construct(EngineResolver $resolver)
                {
                    foreach ($resolver->resolvers as $engine => $resolver) {
                        $this->register($engine, $resolver);
                    }

                }

                public function register($engine, \Closure $resolver)
                {
                    parent::register($engine, function () use ($resolver) {
                        return new Common($resolver());
                    });
                }

            };

        });

    }


    private function bootCommands()
    {


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
            SearchAccess::class,
            SetSeoTitle::class,
            Spider::class,
            SpiderTable::class,
            StaticTool::class,
            SyncExpand::class,
            Test::class,
            TimingArticlePush::class,
            Init::class,
            AutoAssociationObject::class,
            CreateRoute::class,
            PushAsset::class,
            NewTheme::class,
            SwitchTheme::class,
            GetAdminStatic::class,
            CreateExpandTable::class,
            CleanStaticPage::class,
            GetLibrary::class,
            GetUpdate::class,
            GetGoScript::class,
            SetExpandData::class,
            SetExpandDataBatch::class,
            BatchImportArticleWithZip::class,
            ResetDatabase::class,
            AndroidUrlToDownload::class,

        ]);


    }


}
