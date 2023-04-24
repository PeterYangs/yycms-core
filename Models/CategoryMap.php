<?php

namespace Ycore\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\CategoryMap
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title 描述
 * @property string $list 替换列表
 * @property int $default_category_id 默认分类
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap query()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap whereDefaultCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap whereList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CategoryMap whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryMap extends Base
{

    protected $table = 'category_map';

    protected $fillable = ['title', 'list', 'default_category_id'];



    function list(): Attribute
    {

        return new Attribute(

            get: function ($value) {


                if (!$value) {

                    return [];
                }


                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            },
            set: function ($value) {

                if (!$value) {


                    return "";
                }


                return json_encode($value, JSON_THROW_ON_ERROR);

            }


        );

    }


}
