<?php

namespace Ycore\Tool;

use App\Http\Controllers\Admin\CategoryController;
use Ycore\Models\Article;

class Expand
{


    /**
     * 同步拓展表数据
     * @param Article $value
     * @throws \JsonException
     */
    static function SyncExpand(Article $value)
    {

        $expand = getExpandByCategoryId($value->category_id);


        $table = CategoryController::getExpandTableName($value->category_id);

        $expand_data = \DB::table($table)->where('article_id', $value->id)->first();


        if (!$expand_data) {

            //写入默认数据到拓展表
            \DB::table($table)->insert(['article_id' => $value->id]);

            $expand_data = \DB::table($table)->where('article_id', $value->id)->first();


        }


        foreach ($expand as $k => $v) {


            $field = $v->name;


            $fv = $expand_data->$field;


            if (is_array($v->value)) {

                if ($fv) {

                    $expand[$k]->value = json_decode($fv, true, 512, JSON_THROW_ON_ERROR);

                } else {

                    $expand[$k]->value = [];
                }


            } else {

                if ($fv) {

                    $expand[$k]->value = $fv;

                } else {

                    $expand[$k]->value = "";
                }


            }


        }


        \DB::table('article')->where('id', $value->id)->update([
            'expand' => json_encode($expand,
                JSON_THROW_ON_ERROR)
        ]);


    }


}
