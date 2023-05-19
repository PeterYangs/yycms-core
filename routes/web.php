<?php


//后台图片访问
use Ycore\Http\Middleware\home\StaticRender;

Route::get('/backend/{path}.{ex}', function ($path, $ex) {


    switch (env('UPLOAD_TYPE')) {


        case "ali_oss":


            return response()->redirectGuest(env('IMAGE_DOMAIN') . "/uploads/" . $path . "." . $ex);


        default:

            if (Storage::disk('upload')->exists($path . "." . $ex)) {

                return response()->file(Storage::disk('upload')->path($path . "." . $ex));
            }

            abort(404);

    }


})->where(['path' => "[/A-Za-z0-9._]+", 'ex' => "(jpg|jpeg|png|gif|webp){1}"]);


//编辑器图片访问
Route::get("/api/uploads/{path}.{ex}", function ($path, $ex) {



    switch (env('UPLOAD_TYPE')) {


        case "ali_oss":


            return response()->redirectGuest(env('IMAGE_DOMAIN') . "/uploads/" . $path . "." . $ex);


        default:

            if (Storage::disk('upload')->exists($path . "." . $ex)) {

                return response()->file(Storage::disk('upload')->path($path . "." . $ex));
            }

            abort(404);

    }


})->where(['path' => "[/A-Za-z0-9._]+", 'ex' => "(jpg|jpeg|png|gif|webp){1}"]);


Route::get("/", make(\Ycore\Http\Controllers\Pc\Index::class, 'index'))->middleware(StaticRender::class)->name('pc.index');


//Route::get("/game",make(\Ycore\Http\Controllers\Pc\Channel::class,'channel'));


Route::get('/game', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 1,'is_auto'=>1]))->name('pc.game');

Route::get('/game/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 1,'is_auto'=>1]));

Route::get('/jsby', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 2,'is_auto'=>1]));

Route::get('/jsby/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 2,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/jsby/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 2,'is_auto'=>1]));

Route::get('/app', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 3,'is_auto'=>1]))->name('pc.app');

Route::get('/app/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 3,'is_auto'=>1]));

Route::get('/lycx', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 4,'is_auto'=>1]));

Route::get('/lycx/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 4,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/lycx/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 4,'is_auto'=>1]));

Route::get('/dzgt', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 6,'is_auto'=>1]));

Route::get('/dzgt/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 6,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/dzgt/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 6,'is_auto'=>1]));

Route::get('/yyjz', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 7,'is_auto'=>1]));

Route::get('/yyjz/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 7,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/yyjz/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 7,'is_auto'=>1]));

Route::get('/scjs', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 8,'is_auto'=>1]));

Route::get('/scjs/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 8,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/scjs/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 8,'is_auto'=>1]));

Route::get('/xxyl', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 9,'is_auto'=>1]));

Route::get('/xxyl/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 9,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/xxyl/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 9,'is_auto'=>1]));

Route::get('/mnjy', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 10,'is_auto'=>1]));

Route::get('/mnjy/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 10,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/mnjy/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 10,'is_auto'=>1]));

Route::get('/tyjj', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 11,'is_auto'=>1]));

Route::get('/tyjj/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 11,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/tyjj/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 11,'is_auto'=>1]));

Route::get('/kpyx', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 12,'is_auto'=>1]));

Route::get('/kpyx/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 12,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/kpyx/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 12,'is_auto'=>1]));

Route::get('/fxsj', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 13,'is_auto'=>1]));

Route::get('/fxsj/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 13,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/fxsj/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 13,'is_auto'=>1]));

Route::get('/jrlc', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 14,'is_auto'=>1]));

Route::get('/jrlc/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 14,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/jrlc/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 14,'is_auto'=>1]));

Route::get('/sjlt', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 15,'is_auto'=>1]));

Route::get('/sjlt/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 15,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/sjlt/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 15,'is_auto'=>1]));

Route::get('/gzxx', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 16,'is_auto'=>1]));

Route::get('/gzxx/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 16,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/gzxx/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 16,'is_auto'=>1]));

Route::get('/xwyd', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 17,'is_auto'=>1]));

Route::get('/xwyd/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 17,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/xwyd/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 17,'is_auto'=>1]));

Route::get('/sytx', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 18,'is_auto'=>1]));

Route::get('/sytx/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 18,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/sytx/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 18,'is_auto'=>1]));

Route::get('/xtgj', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 19,'is_auto'=>1]));

Route::get('/xtgj/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 19,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/xtgj/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 19,'is_auto'=>1]));

Route::get('/zixun', make(\App\Http\Controllers\Pc\News::class, 'newsList', ['cid' => 20,'is_auto'=>1]))->name('pc.news');

Route::get('/zixun/list-{page}.html', make(\App\Http\Controllers\Pc\News::class, 'newsList', ['cid' => 20,'is_auto'=>1]));

Route::get('/sygl', make(\App\Http\Controllers\Pc\News::class, 'newsList', ['cid' => 22,'is_auto'=>1]))->name('pc.raiders');

Route::get('/sygl/{id}.html', make(\App\Http\Controllers\Pc\News::class, 'newsDetail', ['cid' => 22,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/sygl/list-{page}.html', make(\App\Http\Controllers\Pc\News::class, 'newsList', ['cid' => 22,'is_auto'=>1]));

Route::get('/yxzx', make(\App\Http\Controllers\Pc\News::class, 'newsList', ['cid' => 23,'is_auto'=>1]))->name('pc.latest');

Route::get('/yxzx/{id}.html', make(\App\Http\Controllers\Pc\News::class, 'newsDetail', ['cid' => 23,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/yxzx/list-{page}.html', make(\App\Http\Controllers\Pc\News::class, 'newsList', ['cid' => 23,'is_auto'=>1]));

Route::get('/phb', make(\App\Http\Controllers\Pc\Rank::class, 'rank', ['cid' => 25,'is_auto'=>1]))->name('pc.rank');

Route::get('/phb/{id}.html', make(\App\Http\Controllers\Pc\Rank::class, 'rank_detail', ['cid' => 25,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/hj', make(\App\Http\Controllers\Pc\Collect::class, 'list', ['cid' => 26,'is_auto'=>1]))->name('pc.collect');

Route::get('/hj/{id}.html', make(\App\Http\Controllers\Pc\Collect::class, 'detail', ['cid' => 26,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/hj/list-{page}.html', make(\App\Http\Controllers\Pc\Collect::class, 'list', ['cid' => 26,'is_auto'=>1]));

Route::get('/hj/{id}-{page}.html', make(\App\Http\Controllers\Pc\Collect::class, 'detail', ['cid' => 26,'is_auto'=>1]));

Route::get('/mxjm', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 28,'is_auto'=>1]));

Route::get('/mxjm/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 28,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/mxjm/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 28,'is_auto'=>1]));

Route::get('/yybf', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 29,'is_auto'=>1]));

Route::get('/yybf/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 29,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/yybf/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 29,'is_auto'=>1]));

Route::get('/gwzf', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 31,'is_auto'=>1]));

Route::get('/gwzf/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 31,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/gwzf/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 31,'is_auto'=>1]));

Route::get('/layc', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 33,'is_auto'=>1]));

Route::get('/layc/{id}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameDetail', ['cid' => 33,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/layc/list-{page}.html', make(\App\Http\Controllers\Pc\Game::class, 'gameList', ['cid' => 33,'is_auto'=>1]));

Route::get('/shfw', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 34,'is_auto'=>1]));

Route::get('/shfw/{id}.html', make(\App\Http\Controllers\Pc\App::class, 'appDetail', ['cid' => 34,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/shfw/list-{page}.html', make(\App\Http\Controllers\Pc\App::class, 'appList', ['cid' => 34,'is_auto'=>1]));

Route::get('/yyhj', make(\App\Http\Controllers\Pc\Collect::class, 'list', ['cid' => 35,'is_auto'=>1]))->name('pc.app_collect');

Route::get('/yyhj/{id}.html', make(\App\Http\Controllers\Pc\Collect::class, 'detail', ['cid' => 35,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/yyhj/list-{page}.html', make(\App\Http\Controllers\Pc\Collect::class, 'list', ['cid' => 35,'is_auto'=>1]));

Route::get('/yyhj/{id}-{page}.html', make(\App\Http\Controllers\Pc\Collect::class, 'detail', ['cid' => 35,'is_auto'=>1]));

Route::get('/rjjc', make(\App\Http\Controllers\Pc\News::class, 'newsList', ['cid' => 36,'is_auto'=>1]))->name('pc.app_news');

Route::get('/rjjc/{id}.html', make(\App\Http\Controllers\Pc\News::class, 'newsDetail', ['cid' => 36,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('/rjjc/list-{page}.html', make(\App\Http\Controllers\Pc\News::class, 'newsList', ['cid' => 36,'is_auto'=>1]));

Route::get('/yyph', make(\App\Http\Controllers\Pc\Rank::class, 'rank', ['cid' => 37,'is_auto'=>1]))->name('pc.app_rank');

Route::get('/yyph/{id}.html', make(\App\Http\Controllers\Pc\Rank::class, 'rank_detail', ['cid' => 37,'is_auto'=>1]))->middleware(\Ycore\Http\Middleware\home\StaticRender::class);

Route::get('search', [\App\Http\Controllers\Pc\Search::class, 'search'])->name('pc.search');

Route::get('/page/sitemap.html', [\App\Http\Controllers\Pc\Page::class, 'sitemap'])->name('pc.page_sitemap');
