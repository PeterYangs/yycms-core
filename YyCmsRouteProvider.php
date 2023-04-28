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


}
