<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\DownloadSite;
use Ycore\Models\Role;
use Ycore\Tool\Json;

class DownloadSiteController extends AuthCheckController
{

    /**
     * Create by Peter Yang
     * 2022-06-20 14:40:55
     * @return string
     */
    function update()
    {


        $post = request()->post();

        $id = $post['id'] ?? null;

        DownloadSite::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');

    }


    function list()
    {


        $list = DownloadSite::orderBy('id', 'desc');


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function detail()
    {

        $id = request()->input('id');

        $item = DownloadSite::where('id', $id)->firstOrFail();


        return Json::code(1, 'success', $item);

    }

}
