<?php

namespace Ycore\Http\Middleware\home;


use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class Pc2Mobile
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

//        return $next($request);

        $a = new Agent();

        $url = $request->fullUrl();


        if ($a->isMobile()) {


            return response()->redirectTo(str_replace("www.", "m.", $url));
        }


        return $next($request);
    }
}
