<?php

use Ycore\Http\Middleware\home\ArticleSpecial;
use Ycore\Http\Middleware\home\HomeTag;
use Ycore\Http\Middleware\home\UserAccess;

Route::middleware([HomeTag::class, UserAccess::class, ArticleSpecial::class])->group(function () {

    //自定义路由


});
