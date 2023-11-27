<?php

namespace Ycore\Http\Middleware\home;

use Ycore\Jobs\UserAccessJob;
use Closure;
use Illuminate\Http\Request;

class HomeTag
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        //标记在前台运行
        app()->instance('run_env', 'home');


        return $next($request);
    }
}
