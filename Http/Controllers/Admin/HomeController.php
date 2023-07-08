<?php

namespace Ycore\Http\Controllers\Admin;


use Illuminate\Support\Facades\Http;
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
     * @throws \Exception
     */
    function CheckUpdate()
    {


        if (session()->has('_ignore_update')) {


            return Json::code(1, 'success', false);
        }


        $rep = Http::withOptions(['verify' => false])->get("http://121.199.20.221:8198/releases");

        if ($rep->status() !== 200) {

            throw new \Exception("获取更新失败(" . $rep->body() . ")");

        }

        $data = json_decode($rep->body(), true);

        $tag = $data['tag_name'];


        if ($tag !== Core::GetVersion()) {


            return Json::code(1, 'success', true);

        }


        $adminVersion = "";

        try {

            $adminVersion = str_replace("\n", "", \File::get(public_path('yycms/static/version')));

        } catch (\Exception $exception) {


            if (app()->runningInConsole()) {

                $this->error($exception->getMessage());

            }


        }

        if ($adminVersion != Core::GetAdminVersion()) {


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

        try {

            \Artisan::call("GetLibrary");

            \Artisan::call("GetAdminStatic");

            \Artisan::call("migrate");


        } catch (\Exception $exception) {


            return Json::code(2, $exception->getMessage());
        }


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
