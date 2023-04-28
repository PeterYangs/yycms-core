<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\Mode;
use Ycore\Tool\Json;
use Ycore\Tool\Search;

class ModeController extends AuthCheckController
{


    function update()
    {


        try {

            $post = request()->input();

            $id = request()->input('id', 0);


            if ($post['expired_time'] === '') {

                $post['expired_time'] = null;
            }

            Mode::updateOrCreate(['id' => $id], $post);


            return Json::code(1, 'success');

        } catch (\Exception $exception) {


            return Json::code(2, $exception->getMessage());

        }


    }


    function list()
    {


        $list = Mode::where('hide', 1)->orderBy('id', 'desc');


        Search::searchList($list, request()->input('search', '[]'));


        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));

    }


    function detail()
    {

        $id = request()->input('id');


        $data = Mode::find($id);

        return Json::code(1, 'success', $data);
    }


}
