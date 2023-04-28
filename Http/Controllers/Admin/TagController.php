<?php

namespace Ycore\Http\Controllers\Admin;


use Ycore\Models\Tag;
use Ycore\Tool\Json;
use Ycore\Tool\Search;

class TagController extends AuthCheckController
{

    function list()
    {


        $list = Tag::withCount('article_tags')->orderBy('id', 'asc');

        Search::searchList($list, request()->input('search', '[]'));

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));
    }


    function update()
    {


        $post = request()->input();


        try {

            Tag::create($post);

        } catch (\Exception $exception) {


            return Json::code(2, $exception->getMessage());
        }


        return Json::code(1, 'success');

    }


}
