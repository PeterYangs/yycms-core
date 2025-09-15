<?php

namespace Ycore\Http\Middleware\home;

use Ycore\Jobs\UserAccessJob;
use Closure;
use Illuminate\Http\Request;

class UserAccess
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

        if (config('yycms.disable_access_log') === true) {
            return $next($request);
        }

        if ($request->query('admin_key') === env('ADMIN_KEY')) {


            return $next($request);
        }


        $ip = $request->ip();

        $url = $request->fullUrl();

        $referer = $request->header('referer');

        $query = $request->getQueryString();

        $agent = $request->header('user-agent', "");

        try {

            dispatch(new UserAccessJob($ip ?: "", $url ?: "", $referer ?: "", $query ?: "", $agent ?: ""));

        } catch (\Exception $exception) {

        }


        return $next($request);
    }
}
