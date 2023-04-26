<?php

namespace Ycore\Http\Middleware\home;

use Closure;
use Illuminate\Http\Request;

class SetCategoryId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {


//        dd($request->route());


        $id=$request->input('id');

//        $uri=



        return $next($request);
    }
}
