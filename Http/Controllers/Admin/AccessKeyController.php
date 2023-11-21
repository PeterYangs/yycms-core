<?php

namespace Ycore\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Ycore\Models\AccessKey;
use Ycore\Models\Role;
use Ycore\Tool\Json;

class AccessKeyController extends AuthCheckController
{


    function create()
    {

        AccessKey::create([
            'app_id' => Str::lower(Str::random(16)),
            'app_secret' => Str::lower(Str::random(32)),
            'status' => 1
        ]);

        return Json::code(1, 'success');
    }


    function change()
    {

        $id = request()->input('id');

        $accessKey = AccessKey::findOrFail($id);

        if ($accessKey->status == 1) {

            $accessKey->status = 2;

        } else {

            $accessKey->status = 1;
        }


        $accessKey->save();

        return Json::code(1, 'success');

    }


    function list()
    {


        $list = AccessKey::orderBy('id', 'desc');


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


}
