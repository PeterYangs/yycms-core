<?php

namespace Ycore\Http\Controllers\Admin;


use Ycore\Models\SeoTitleChange;
use Ycore\Tool\Json;
use Illuminate\Support\Facades\Schema;

class SeoTitleChangeController extends AuthCheckController
{

    function getArticleFields()
    {

        $list = Schema::getColumnListing('article');


        $list = array_filter($list, static function ($value) {


            if (in_array($value, [
                'id',
                'created_at',
                'updated_at',
                'deleted_at',
                'seo_title',
                'admin_id_create',
                'admin_id_update',
                'select_order'
            ])) {


                return false;
            }

            return true;
        });


        return Json::code(1, 'success', $list);
    }


    function getCategoryItemFields()
    {

        $cid = request()->input('cid');

        $tableName = CategoryController::getExpandTableName($cid);

        $list = Schema::getColumnListing($tableName);


        $list = array_filter($list, static function ($value) {


            if (in_array($value, [
                'id',
                'created_at',
                'updated_at',
                'article_id'

            ])) {


                return false;
            }

            return true;
        });


        return Json::code(1, 'success', array_values($list));


    }


    function update()
    {


        $post = request()->input();

        $id = $post['id'] ?? null;

        SeoTitleChange::updateOrCreate(['id' => $id], $post);


        return Json::code(1, 'success');

    }


    function detail()
    {

        $item = SeoTitleChange::first();


        return Json::code(1, 'success', $item);
    }


}
