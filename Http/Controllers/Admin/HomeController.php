<?php

namespace Ycore\Http\Controllers\Admin;


use Ycore\Core\Core;
use Ycore\Tool\Json;

class HomeController extends AuthCheckController
{

    function spiderTable()
    {


        return Json::code(1, 'success', \Cache::get('spider_table', []));
    }

    /**
     * 访问量
     */
    function access()
    {


        return Json::code(1, 'success', \Cache::get('access', []));
    }


    function search()
    {


        return Json::code(1, 'success', \Cache::get('search_access', []));

    }


    /**
     * 检查更新
     * @return string
     */
    function CheckUpdate()
    {


        if (session()->has('_ignore_update')) {


            return Json::code(1, 'success', false);
        }

        $adminVersion = "";

        try {

            $adminVersion = str_replace("\n", "", \File::get(public_path('yycms/static/version')));

        } catch (\Exception $exception) {


            if (app()->runningInConsole()) {

                $this->error($exception->getMessage());

            }


        }

        if ($adminVersion != Core::ADMIN_VERSION) {


            return Json::code(1, 'success', true);
        }


        return Json::code(1, 'success', false);

    }


    /**
     * 更新
     * @return string
     */
    function update()
    {

        \Artisan::call("GetAdminStatic");

        return Json::code(1, 'success');
    }


    /**
     * 忽略更新
     * @return string
     */
    function ignoreUpdate()
    {

        session()->put("_ignore_update", true);


        return Json::code(1, 'success');

    }

}
