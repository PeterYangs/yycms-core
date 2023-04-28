<?php

namespace Ycore\Http\Controllers\Admin;

use Ycore\Models\ArticleExpand;
use Ycore\Models\ArticleExpandDetail;
use Ycore\Tool\Json;
use Illuminate\Database\Schema\Blueprint;

class ArticleExpandController extends AuthCheckController
{


    /**
     * Create by Peter Yang
     * 2022-06-22 13:54:37
     * @return string
     * @throws \Throwable
     */
    function update()
    {

        $post = request()->input();

        $category_id = $post['category_id'] ?? 0;


        $table_name = 'expand_table_' . $category_id;


        $checkArr = [];

        foreach ($post['list'] as $kk => $vv) {


            $checkArr[$vv['name']] = "1";

        }

        if (count($checkArr) !== count($post['list'])) {


            return Json::code(2, '字段不能重复！');

        }


        try {

//            \DB::beginTransaction();

            $id = $post['id'] ?? 0;

            //新增
            if (!$id) {


                if (\Schema::hasTable($table_name)) {

                    throw new \RuntimeException($table_name . ':表已存在！');
                }


                \Schema::create($table_name, function (Blueprint $table) use ($post) {

                    $table->bigIncrements('id');
                    $table->timestamps();
                    $table->integer('article_id')->unsigned()->unique()->comment('文章表id');

                    $list = $post['list'];


                    foreach ($list as $value) {


                        $this->setTable($value, $table);


                    }


                });


                $ae = ArticleExpand::create($post);


                foreach ($post['list'] as $v) {


                    $v['article_expand_id'] = $ae->id;


                    ArticleExpandDetail::create($v);

                }


            } else {

                if (!\Schema::hasTable($table_name)) {

                    throw new \RuntimeException($table_name . ':表不存在！');
                }


                \Schema::table($table_name, function (Blueprint $table) use ($post, $table_name) {


                    $list = $post['list'];


                    $disable_name = ['article_id', 'id', 'created_at', 'updated_at'];


                    foreach ($list as $value) {


                        if (in_array($value['name'], $disable_name, true)) {

                            continue;
                        }


                        if (!\Schema::hasColumn($table_name, $value['name'])) {


                            $this->setTable($value, $table);

                        }


                    }


                });


                foreach ($post['list'] as $v) {


                    $v['article_expand_id'] = $id;


                    ArticleExpandDetail::updateOrCreate(['article_expand_id' => $id, 'name' => $v['name']], $v);

                }


            }

//            \DB::commit();

            return Json::code(1, 'success');


        } catch (\Exception $exception) {

//            \DB::rollBack();


            return Json::code(2, $exception->getMessage());

        }


    }

    private function setTable($value, Blueprint $table)
    {


        switch ($value['type']) {

            case 5:

                $table->timestamp($value['name'])->nullable()->comment($value['desc']);

                break;

            case 7:
            case 6:


                $table->text($value['name'])->nullable()->comment($value['desc']);

                break;

            case 8:

                $table->integer($value['name'])->unsigned()->default(0)->nullable()->comment($value['desc']);

                break;

            default:

                $table->string($value['name'], 500)->nullable()->comment($value['desc']);

        }

    }

    function detail()
    {

        $data = ArticleExpand::with('list')->findOrFail(request()->input('id', 0));


        //获取原始值
        foreach ($data['list'] as $key => $value) {


            $data['list'][$key] = $value->getRawOriginal();

        }


        return Json::code(1, 'success', $data);
    }

    function list()
    {


        $list = ArticleExpand::with('category')->has('category')->orderBy('id', 'desc');

        return Json::code(1, 'success', paginate($list, request()->input('p', 1)));


    }


    function deleteField()
    {


        $category_id = request()->input('category_id');

        $table_name = 'expand_table_' . $category_id;

        $field = request()->input('field');

        $article_expand_detail_id = request()->input('id');


        try {

//            \DB::beginTransaction();

            ArticleExpandDetail::where('id', $article_expand_detail_id)->delete();


            \Schema::table($table_name, function (Blueprint $table) use ($field) {


                $table->dropColumn($field);

            });


//            \DB::commit();


            return Json::code(1, 'success');

        } catch (\Exception $exception) {

//            dd($exception->getMessage());

//            \DB::rollBack();


            return Json::code(2, $exception->getMessage());

        }


    }


    function getExpandByCategoryId()
    {

        $category_id = request()->input('id');


        $list = getExpandByCategoryId($category_id);


        return Json::code(1, 'success', $list);


    }


    function getExpandFields()
    {

        $category_id = request()->input('category_id');


        $tableName = CategoryController::getExpandTableName($category_id);


        $list = \Schema::getColumnListing($tableName);


        $array = [];


        foreach ($list as $key => $value) {

            if ($value === "id" || $value === "created_at" || $value === "updated_at" || $value=== "article_id"){

                continue;
            }

            $array[] = ['value' => $value];

        }


        return Json::code(1, 'success', $array);

    }


}
