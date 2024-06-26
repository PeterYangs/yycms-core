<?php

namespace Ycore;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class YyCmsRouteProvider extends ServiceProvider
{


    public function boot()
    {
        //下载访问限流
        RateLimiter::for('download', function (Request $request) {
            return Limit::perMinute(4)->by($request->ip())->response(function () {
                return response('访问次数过多', 429);
            });
        });

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

            Route::middleware('web')
                ->prefix('api')
                ->group(__DIR__ . "/routes/api.php");


            Route::middleware('web')->group(__DIR__ . "/routes/web.php");

        });

        Route::pattern('id', '[0-9]+');
        Route::pattern('page', '[0-9]+');
        Route::pattern('order', '[0-9]+');


    }


}
