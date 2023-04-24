<?php

namespace Ycore;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Traits\ForwardsCalls;
use Ycore\Console\Test2;
use Closure;

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
                Test2::class
            ]);

        }

    }


    private function bootRoutes()
    {


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
