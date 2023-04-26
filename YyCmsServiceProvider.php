<?php

namespace Ycore;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\ForwardsCalls;
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

class YyCmsServiceProvider extends ServiceProvider
{


    use ForwardsCalls;

    /**
     * The controller namespace for the application.
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * The callback that should be used to load the application's routes.
     *
     * @var \Closure|null
     */
    protected $loadRoutesUsing;


    public function boot()
    {

        $this->bootCommands();

        $this->bootRoutes();

    }


    public function register()
    {


        //这一块是抄laravel的RouteServiceProvider，不要问我为什么这样写，因为我也不知道
        $this->booted(function () {
            $this->setRootControllerNamespace();

            if ($this->routesAreCached()) {
                $this->loadCachedRoutes();
            } else {
                $this->loadRoutes();

                $this->app->booted(function () {
                    $this->app['router']->getRoutes()->refreshNameLookups();
                    $this->app['router']->getRoutes()->refreshActionLookups();
                });
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
                Init::class

            ]);

        }

    }


    private function bootRoutes()
    {

        //api访问限流
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(1000)->by($request->user()?->id ?: $request->ip());
        });

        //后台登录访问限流
        RateLimiter::for('login', function (Request $request) {


            return Limit::perMinute(20)->by($request->ip())->response(function () {


                return response('访问次数过多', 429);

            });
        });


        $this->routes(function () {

            Route::middleware('api')
                ->prefix('api')
                ->group(__DIR__ . "/routes/api.php");

        });


    }


    /**
     * Register the callback that will be used to load the application's routes.
     *
     * @param \Closure $routesCallback
     * @return $this
     */
    protected function routes(Closure $routesCallback)
    {
        $this->loadRoutesUsing = $routesCallback;

        return $this;
    }

    /**
     * Set the root controller namespace for the application.
     *
     * @return void
     */
    protected function setRootControllerNamespace()
    {
        if (!is_null($this->namespace)) {
            $this->app[UrlGenerator::class]->setRootControllerNamespace($this->namespace);
        }
    }

    /**
     * Determine if the application routes are cached.
     *
     * @return bool
     */
    protected function routesAreCached()
    {
        return $this->app->routesAreCached();
    }

    /**
     * Load the cached routes for the application.
     *
     * @return void
     */
    protected function loadCachedRoutes()
    {
        $this->app->booted(function () {
            require $this->app->getCachedRoutesPath();
        });
    }

    /**
     * Load the application routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        if (!is_null($this->loadRoutesUsing)) {
            $this->app->call($this->loadRoutesUsing);
        } elseif (method_exists($this, 'map')) {
            $this->app->call([$this, 'map']);
        }
    }

    /**
     * Pass dynamic methods onto the router instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo(
            $this->app->make(Router::class), $method, $parameters
        );
    }

}
