<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Tool\Json;

class DeathController extends AuthCheckController
{


    function getLink()
    {
        $list = [];
        if (file_exists(storage_path('app/public/www-death.txt'))) {
            $list[] = rtrim(getOption('domain'), '/') . "/_www_death.txt";
        }

        if (file_exists(storage_path('app/public/m-death.txt'))) {
            $list[] = rtrim(getOption('m_domain'), '/') . "/_m_death.txt";
        }

        return Json::code(1, 'success', $list);
    }


    function createLink()
    {
        \Artisan::call('CreateDeathLink');
        return Json::code(1, 'success');
    }

}
