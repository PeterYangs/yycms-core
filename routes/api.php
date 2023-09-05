<?php


use Ycore\Http\Controllers\Admin\AdminController;
use Ycore\Http\Controllers\Admin\ArticleController;
use Ycore\Http\Controllers\Admin\ArticleExpandController;
use Ycore\Http\Controllers\Admin\AutoPushController;
use Ycore\Http\Controllers\Admin\CaptchaController;
use Ycore\Http\Controllers\Admin\CategoryController;
use Ycore\Http\Controllers\Admin\CategoryMapController;
use Ycore\Http\Controllers\Admin\CategoryRouteController;
use Ycore\Http\Controllers\Admin\CollectController;
use Ycore\Http\Controllers\Admin\CommonController;
use Ycore\Http\Controllers\Admin\ExpandChangeController;
use Ycore\Http\Controllers\Admin\HomeController;
use Ycore\Http\Controllers\Admin\InstallController;
use Ycore\Http\Controllers\Admin\LoginController;
use Ycore\Http\Controllers\Admin\MenuController;
use Ycore\Http\Controllers\Admin\ModeController;
use Ycore\Http\Controllers\Admin\PageController;
use Ycore\Http\Controllers\Admin\RoleController;
use Ycore\Http\Controllers\Admin\RouteController;
use Ycore\Http\Controllers\Admin\RulesController;
use Ycore\Http\Controllers\Admin\SearchArticleController;
use Ycore\Http\Controllers\Admin\SearchController;
use Ycore\Http\Controllers\Admin\SeoTitleChangeController;
use Ycore\Http\Controllers\Admin\SitemapController;
use Ycore\Http\Controllers\Admin\SiteSettingController;
use Ycore\Http\Controllers\Admin\SpecialController;
use Ycore\Http\Controllers\Admin\SpiderController;
use Ycore\Http\Controllers\Admin\StaticController;
use Ycore\Http\Controllers\Admin\StoreArticleController;
use Ycore\Http\Controllers\Admin\TagController;
use Ycore\Http\Controllers\Admin\UploadController;
use Ycore\Http\Controllers\Admin\WebsitePushController;

Route::middleware([])->group(function () {


    Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {


        Route::group(['prefix' => "captcha"], function () {


            Route::get('getCaptcha', [CaptchaController::class, 'getCaptcha']);

        });
//
        Route::group(['prefix' => "common"], function () {


            Route::get('forCategory', [CommonController::class, 'forCategory']);

        });


        Route::group(['prefix' => "admin"], function () {


            Route::post('update', [AdminController::class, 'update']);
            Route::post('list', [AdminController::class, 'list']);
            Route::post('detail', [AdminController::class, 'detail']);
            Route::post('groupList', [AdminController::class, 'groupList']);
            Route::post('roleList', [AdminController::class, 'roleList']);
            Route::get('info', [AdminController::class, 'info']);
            Route::post('getAdminList', [AdminController::class, 'getAdminList']);

        });


        Route::group(['prefix' => "rules"], function () {


            Route::post('update', [RulesController::class, 'update']);
            Route::post('list', [RulesController::class, 'list']);
            Route::post('detail', [RulesController::class, 'detail']);
            Route::post('destroy', [RulesController::class, 'destroy']);

        });


        Route::group(['prefix' => "route"], function () {


            Route::post('getRouteTip', [RouteController::class, 'getRouteTip']);

        });


        Route::group(['prefix' => "role"], function () {


            Route::post('allRules', [RoleController::class, 'allRules']);
            Route::post('update', [RoleController::class, 'update']);
            Route::post('list', [RoleController::class, 'list']);
            Route::post('detail', [RoleController::class, 'detail']);


        });


        Route::group(['prefix' => "menu"], function () {


            Route::post('getMenu', [MenuController::class, 'getMenu']);

        });

        //分类管理
        Route::group(['prefix' => 'category'], function () {
            Route::post('categoryUpdate', [CategoryController::class, 'categoryUpdate']);
            Route::post('categoryList', [CategoryController::class, 'categoryList']);
            Route::post('categoryReleaseStatus', [CategoryController::class, 'categoryReleaseStatus']);
            Route::post('getCategoryNameById', [CategoryController::class, 'getCategoryNameById']);
        });


        Route::group(['prefix' => "login"], function () {


            Route::middleware(['throttle:login'])->any('login', [LoginController::class, 'login']);

        });


        Route::group(['prefix' => "article_expand"], function () {


            Route::post('update', [ArticleExpandController::class, 'update']);
            Route::post('detail', [ArticleExpandController::class, 'detail']);
            Route::post('list', [ArticleExpandController::class, 'list']);
            Route::post('deleteField', [ArticleExpandController::class, 'deleteField']);
            Route::any('getExpandByCategoryId', [ArticleExpandController::class, 'getExpandByCategoryId']);
            Route::any('getExpandFields', [ArticleExpandController::class, 'getExpandFields']);

        });


        Route::group(['prefix' => "expand_change"], function () {


            Route::post('update', [ExpandChangeController::class, 'update']);
            Route::post('list', [ExpandChangeController::class, 'list']);
            Route::post('detail', [ExpandChangeController::class, 'detail']);


        });


        Route::group(['prefix' => "category_route"], function () {


            Route::post('getControllerList', [CategoryRouteController::class, 'getControllerList']);
            Route::post('getActionList', [CategoryRouteController::class, 'getActionList']);
            Route::post('CreateChannelRoute', [CategoryRouteController::class, 'CreateChannelRoute']);
            Route::post('destroy', [CategoryRouteController::class, 'destroy']);


        });


        Route::group(['prefix' => "search"], function () {


            Route::any('search', [SearchController::class, 'search']);

        });


        Route::group(['prefix' => "upload"], function () {


            Route::any('uploadNormal', [UploadController::class, 'uploadNormal']);
            Route::any('ueditor', [UploadController::class, 'ueditor']);
            Route::any('uploadFile', [UploadController::class, 'uploadFile']);

        });


        Route::group(['prefix' => "article"], function () {


            Route::any('update', [ArticleController::class, 'update']);
            Route::any('detail', [ArticleController::class, 'detail']);
            Route::any('list', [ArticleController::class, 'list']);
            Route::any('listForAlert', [ArticleController::class, 'listForAlert']);
            Route::any('specialList', [ArticleController::class, 'specialList']);
            Route::any('findGame', [ArticleController::class, 'findGame']);
            Route::any('matchGame', [ArticleController::class, 'matchGame']);
            Route::any('setNewsMatchGame', [ArticleController::class, 'setNewsMatchGame']);
            Route::any('delayOrder', [ArticleController::class, 'delayOrder']);
            Route::any('delete', [ArticleController::class, 'delete']);
            Route::any('article_recover', [ArticleController::class, 'article_recover']);
            Route::any('recover', [ArticleController::class, 'recover']);
            Route::any('homeStatic', [ArticleController::class, 'homeStatic']);
            Route::any('removeArticleAssociationObject', [ArticleController::class, 'removeArticleAssociationObject']);
            Route::any('down', [ArticleController::class, 'down']);
            Route::any('up', [ArticleController::class, 'up']);
            Route::post('CleanStaticPage', [ArticleController::class, 'CleanStaticPage']);
            Route::post('batchImportByTxt', [ArticleController::class, 'batchImportByTxt']);
        });


        Route::group(['prefix' => "mode"], function () {


            Route::any('update', [ModeController::class, 'update']);
            Route::any('list', [ModeController::class, 'list']);
            Route::any('detail', [ModeController::class, 'detail']);


        });


        Route::group(['prefix' => "page"], function () {


            Route::any('update', [PageController::class, 'update']);
            Route::any('list', [PageController::class, 'list']);
            Route::any('detail', [PageController::class, 'detail']);
            Route::any('delete', [PageController::class, 'delete']);


        });


        Route::group(['prefix' => "special"], function () {


            Route::any('update', [SpecialController::class, 'update']);
            Route::any('list', [SpecialController::class, 'list']);


        });


        //网站设置
        Route::group(['prefix' => 'setting'], function () {
            Route::post('settingUpdate', [SiteSettingController::class, 'settingUpdate']);
            Route::any('getSetting', [SiteSettingController::class, 'getSetting']);
            Route::post('setBeian', [SiteSettingController::class, 'setBeian']);
            Route::post('themeList', [SiteSettingController::class, 'themeList']);
            Route::post('switchTheme', [SiteSettingController::class, 'switchTheme']);
            Route::post('theme', [SiteSettingController::class, 'theme']);
            Route::post('PushAsset', [SiteSettingController::class, 'pushAsset']);
            Route::post('sendTestMail', [SiteSettingController::class, 'sendTestMail']);
        });


        Route::group(['prefix' => 'sitemap'], function () {
            Route::post('create', [SitemapController::class, 'create']);
            Route::post('list', [SitemapController::class, 'list']);
        });


        //主页数据
        Route::group(['prefix' => 'home'], function () {
            Route::any('spiderTable', [HomeController::class, 'spiderTable']);
            Route::any('access', [HomeController::class, 'access']);
            Route::any('search', [HomeController::class, 'search']);
            Route::any('CheckUpdate', [HomeController::class, 'CheckUpdate']);
            Route::any('update', [HomeController::class, 'update']);
            Route::any('ignoreUpdate', [HomeController::class, 'ignoreUpdate']);
        });



        Route::group(['prefix' => 'store_article'], function () {
            Route::any('list', [StoreArticleController::class, 'list']);
            Route::any('detail', [StoreArticleController::class, 'detail']);
            Route::any('update', [StoreArticleController::class, 'update']);
            Route::any('destroy', [StoreArticleController::class, 'destroy']);
            Route::any('push', [StoreArticleController::class, 'push']);
            Route::any('removeDebugArticle', [StoreArticleController::class, 'removeDebugArticle']);

        });

        Route::group(['prefix' => 'static'], function () {

            Route::any('run', [StaticController::class, 'run']);
            Route::any('process', [StaticController::class, 'process']);
            Route::any('stop', [StaticController::class, 'stop']);

        });


        Route::group(['prefix' => 'tag'], function () {

            Route::any('list', [TagController::class, 'list']);
            Route::any('update', [TagController::class, 'update']);


        });


        Route::group(['prefix' => 'spider'], function () {

//            Route::any('list', [TagController::class, 'list']);
            Route::any('update', [SpiderController::class, 'update']);
            Route::any('list', [SpiderController::class, 'list']);
            Route::any('detail', [SpiderController::class, 'detail']);
            Route::any('status', [SpiderController::class, 'status']);
            Route::any('runAll', [SpiderController::class, 'runAll']);
            Route::any('debug', [SpiderController::class, 'debug']);
            Route::any('listCheck', [SpiderController::class, 'listCheck']);
            Route::any('hrefCheck', [SpiderController::class, 'hrefCheck']);
            Route::any('titleCheck', [SpiderController::class, 'titleCheck']);
            Route::any('spiderItemDelete', [SpiderController::class, 'spiderItemDelete']);
            Route::any('itemCheck', [SpiderController::class, 'itemCheck']);


        });


        Route::group(['prefix' => 'category_map'], function () {


            Route::any('update', [CategoryMapController::class, 'update']);
            Route::any('list', [CategoryMapController::class, 'list']);
            Route::any('detail', [CategoryMapController::class, 'detail']);
            Route::any('selectList', [CategoryMapController::class, 'selectList']);


        });


        Route::group(['prefix' => 'seo_title_change'], function () {


            Route::any('getArticleFields', [SeoTitleChangeController::class, 'getArticleFields']);
            Route::any('getCategoryItemFields', [SeoTitleChangeController::class, 'getCategoryItemFields']);
            Route::any('update', [SeoTitleChangeController::class, 'update']);
            Route::any('detail', [SeoTitleChangeController::class, 'detail']);


        });


        Route::group(['prefix' => 'collect'], function () {


            Route::any('add', [CollectController::class, 'add']);
            Route::any('remove', [CollectController::class, 'remove']);
            Route::any('search', [CollectController::class, 'search']);
            Route::any('add_association_object', [CollectController::class, 'add_association_object']);
            Route::any('detail', [CollectController::class, 'detail']);
            Route::any('removeObj', [CollectController::class, 'removeObj']);


        });


        Route::group(['prefix' => 'search_article'], function () {


            Route::any('list', [SearchArticleController::class, 'list']);
            Route::any('run', [SearchArticleController::class, 'run']);
            Route::any('detail', [SearchArticleController::class, 'detail']);
            Route::any('update', [SearchArticleController::class, 'update']);
            Route::any('updateAndPush', [SearchArticleController::class, 'updateAndPush']);
            Route::any('destroy', [SearchArticleController::class, 'destroy']);


        });

        Route::group(['prefix' => 'auto_push'], function () {


            Route::post('update', [AutoPushController::class, 'update']);
            Route::post('list', [AutoPushController::class, 'list']);
            Route::post('disable', [AutoPushController::class, 'disable']);
            Route::post('open', [AutoPushController::class, 'open']);
            Route::post('destroy', [AutoPushController::class, 'destroy']);
            Route::post('detail', [AutoPushController::class, 'detail']);


        });


        Route::group(['prefix' => 'website_push'], function () {


            Route::post('list', [WebsitePushController::class, 'list']);


        });


    });

    Route::get('icp', function () {


        return \Ycore\Tool\Json::code(1, "success", getOption('icp'));

    });


    Route::post('configSave',[InstallController::class,'configSave']);

});
