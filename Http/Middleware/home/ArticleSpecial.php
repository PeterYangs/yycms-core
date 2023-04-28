<?php

namespace Ycore\Http\Middleware\home;


use Ycore\Models\Article;
use Ycore\Scope\ArticleSpecialScope;
use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class ArticleSpecial
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


        Article::addGlobalScope(new ArticleSpecialScope());


        return $next($request);
    }
}
