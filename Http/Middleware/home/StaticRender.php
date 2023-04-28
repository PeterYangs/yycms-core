<?php

namespace Ycore\Http\Middleware\home;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * 静态化判断中间件
 */
class StaticRender
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (!config('static.open', false)) {


            return $next($request);
        }


        if ($request->query('admin_key') === env('ADMIN_KEY')) {


            return $next($request);
        }


        //主页判断
        if ($request->path() === "/") {


            if ((parse_url(getOption("m_domain"))['host'] ?? "") === $request->host()) {


                if (!Storage::disk('static')->fileExists('mobile/index.html')) {


                    return $next($request);
                }

                return response()->file(\Storage::disk('static')->path('mobile/index.html'), ['is_static' => 1, 'static_time' => date("Y-m-d H:i:s", \Storage::disk('static')->lastModified("mobile/index.html"))]);

            }


            if (!Storage::disk('static')->fileExists('pc/index.html')) {


                return $next($request);
            }


            return response()->file(\Storage::disk('static')->path('pc/index.html'), ['is_static' => 1, 'static_time' => date("Y-m-d H:i:s", \Storage::disk('static')->lastModified("pc/index.html"))]);

        }


        //详情页判断
        if ((parse_url(getOption("m_domain"))['host'] ?? "") === $request->host()) {

            if (!Storage::disk('static')->fileExists('mobile/' . $request->path())) {


                return $next($request);
            }


            return response()->file(\Storage::disk('static')->path('mobile/' . $request->path()), ['is_static' => 1, 'static_time' => date("Y-m-d H:i:s", \Storage::disk('static')->lastModified('mobile/' . $request->path()))]);

        }


        if (!Storage::disk('static')->fileExists('pc/' . $request->path())) {


            return $next($request);
        }


        return response()->file(\Storage::disk('static')->path('pc/' . $request->path()), ['is_static' => 1, 'static_time' => date("Y-m-d H:i:s", \Storage::disk('static')->lastModified('pc/' . $request->path()))]);


    }
}
