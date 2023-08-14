<?php

namespace Ycore;

use Ycore\Events\ArticleDestroy;
use Ycore\Events\ArticleUpdate;
use Ycore\Events\WebsitePush;
use Ycore\Listeners\ArticleStatic;
use Ycore\Listeners\BaiduPush;
use Ycore\Listeners\BingPush;
use Ycore\Listeners\DeleteStaticPage;
use Ycore\Listeners\ResetTagList;
use Ycore\Listeners\SelectArticleTag;
use Ycore\Observers\ArticleObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Ycore\Models\Article;

class YyCmsEventProvider extends ServiceProvider
{

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        //站长推送
        WebsitePush::class => [
            BaiduPush::class,
            BingPush::class

        ],
        //文章修改事件
        ArticleUpdate::class => [


            //查找文章标签
            SelectArticleTag::class,

            //文件静态
            ArticleStatic::class,

            //清理标签缓存
            ResetTagList::class,


        ],
        //文章删除事件
        ArticleDestroy::class => [

            //删除静态文件
            DeleteStaticPage::class,

        ]

    ];


    protected $observers = [

        Article::class => [ArticleObserver::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }


}
